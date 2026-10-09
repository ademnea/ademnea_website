# Dashboard integration

For whoever works on the Laravel dashboard. Everything below is about reading
`bee_counts` — the detection pipeline is already running and writing to it.

---

## 1. The short version

**If the dashboard already reads `bee_counts.bee_count`, it works right now with
no changes.** The pipeline sets `bee_count = max_count` on every video it
processes, precisely so the existing display keeps working.

Everything else in this document is about showing *more* than that one number.

---

## 2. What's in the table now

Nine columns were added on 2026-09-12. The pre-existing ones are untouched.

| Column | Type | Null? | Meaning |
|---|---|---|---|
| `bee_count` | `int` | no | **pre-existing.** Now mirrors `max_count`. |
| `mean_count` | `float` | **yes** | average *simultaneous* bees across sampled frames |
| `max_count` | `int` | **yes** | busiest single frame |
| `activity_fraction` | `float` | **yes** | fraction of sampled frames with ≥1 bee, 0.0–1.0 |
| `frames_analyzed` | `int` | **yes** | how many frames were sampled (typically 49) |
| `model_version` | `varchar(64)` | **yes** | `yolo26x-poc` |
| `processing_status` | `enum` | no | `pending` / `processing` / `done` / `failed` |
| `error_message` | `text` | **yes** | why it failed |
| `attempts` | `tinyint` | no | retry counter |
| `processed_at` | `timestamp` | **yes** | when it finished |

Two indexes exist for you: `idx_status` on `processing_status`, and
`idx_hive_video_status` on `(hive_video_id, processing_status)`.

---

## 3. What the numbers actually mean

Easy to misread, and a wrong label on a dashboard is worse than no label.

**`mean_count` is average bees visible *at the same time*.** It is **not** bees
per hour, and not a total. A clip with `mean_count = 3.3` had, on average, 3.3
bees in frame at any given moment.

**`max_count` is the single busiest frame.** A peak, not a total.

**`activity_fraction` is the proportion of frames containing at least one bee.**
`1.0` means the hive entrance was never empty; `0.1` means bees appeared in a
tenth of the sampled moments. It's a decent "was anything happening" signal,
independent of how many bees.

**Nothing here counts individual bees.** There is no tracking, so the same bee
in twenty frames is twenty detections. And there is no directional counting — no
in/out, no net traffic. If someone asks for "bees per hour" or "foraging rate",
the honest answer is that this PoC does not produce it.

Real examples from the table:

| `mean_count` | `max_count` | `activity_fraction` | reading |
|---|---|---|---|
| 3.3061 | 6 | 1.00 | busy — always occupied, peaked at 6 |
| 1.0204 | 2 | 1.00 | steady light traffic |
| 0.1020 | 1 | 0.10 | nearly idle — one bee, one moment in ten |

---

## 4. Three things that will bite you

### 4.1 Only trust rows where `processing_status = 'done'`

Every metric column is nullable and **is** null until a video is processed.
Always filter:

```sql
SELECT ... FROM bee_counts WHERE processing_status = 'done'
```

A chart that averages `mean_count` across all rows silently includes nulls.

### 4.2 There are 185 rows that will never be processed

They predate the pipeline (July–September 2025), sit at
`processing_status = 'pending'` with NULL metrics, and are invisible to the
dispatcher by design. They are **not** a stuck queue and not a bug.

They also have no useful data — 181 of the 185 have `bee_count = 0`.

**Exclude them.** Filtering on `processing_status = 'done'` does it
automatically.

### 4.3 Most videos have no `bee_counts` row at all

About 15,000 clips predate the cutover and are deliberately skipped. Only videos
uploaded after 2026-09-12 12:40 get processed.

So **`LEFT JOIN`, never `INNER JOIN`**, when going from `hive_videos` to
`bee_counts` — an inner join makes almost every historical video vanish from the
dashboard.

```sql
SELECT hv.id, hv.path, hv.created_at,
       bc.bee_count, bc.mean_count, bc.max_count,
       bc.activity_fraction, bc.processing_status
FROM hive_videos hv
LEFT JOIN bee_counts bc
       ON bc.hive_video_id = hv.id
      AND bc.processing_status = 'done'
ORDER BY hv.created_at DESC;
```

Note the status condition is in the `ON` clause, not `WHERE` — in `WHERE` it
would quietly turn the left join back into an inner one.

---

## 5. Laravel specifics

### 5.1 The schema changed outside Laravel's migration system

The columns were added with a raw SQL script, not `php artisan migrate`. So
Laravel's `migrations` table doesn't know about them, and a fresh
`migrate:fresh` on another environment **will not** produce this schema.

Worth reconciling: add a Laravel migration matching
`ademnea/db_migration.sql`, and mark it as already run on production
(insert the row into `migrations` manually) so it applies on other environments
without trying to re-add existing columns.

### 5.2 Model

New columns need adding to the model's `$fillable` (only if the dashboard ever
writes them — it shouldn't) and to `$casts`:

```php
protected $casts = [
    'mean_count'        => 'float',
    'max_count'         => 'integer',
    'activity_fraction' => 'float',
    'frames_analyzed'   => 'integer',
    'attempts'          => 'integer',
    'processed_at'      => 'datetime',
];
```

Without the casts, `mean_count` arrives as a string and comparisons behave
oddly.

A scope keeps the filter honest everywhere:

```php
public function scopeAnalysed($query)
{
    return $query->where('processing_status', 'done');
}
```

### 5.3 Do not write to these columns

The dispatcher owns `processing_status`, `attempts`, `error_message` and every
metric column, and it uses them for claiming work. A dashboard write can cause a
video to be processed twice or skipped. **Read-only.**

If the dashboard needs a "reprocess this video" button, it should not flip
`processing_status` itself — it should trigger
`python ademnea/dispatcher.py --video-id <id>`.

---

## 6. Timing expectations

- Cron runs **every 30 minutes**, so a clip is counted within roughly half an
  hour of upload.
- A run can take up to ~7 minutes when the GPU worker is cold. A row sitting in
  `'processing'` for a few minutes is normal.
- A row stuck in `'processing'` for hours means a run was killed mid-flight.
  That's an ops problem, not a dashboard one — see README troubleshooting.

## 7. Useful queries

**Health:**

```sql
SELECT processing_status, COUNT(*) FROM bee_counts GROUP BY processing_status;
```

Expect a permanent `185 pending`. That's the legacy rows.

**Recent failures:**

```sql
SELECT hive_video_id, attempts, LEFT(error_message, 120) AS err
FROM bee_counts
WHERE processing_status = 'failed'
ORDER BY id DESC LIMIT 20;
```

**Daily activity per hive:**

```sql
SELECT hv.hive_id,
       DATE(hv.created_at)        AS day,
       COUNT(*)                   AS clips,
       ROUND(AVG(bc.mean_count),2) AS avg_simultaneous_bees,
       MAX(bc.max_count)          AS busiest_frame,
       ROUND(AVG(bc.activity_fraction),2) AS avg_activity
FROM bee_counts bc
JOIN hive_videos hv ON hv.id = bc.hive_video_id
WHERE bc.processing_status = 'done'
GROUP BY hv.hive_id, DATE(hv.created_at)
ORDER BY day DESC, hv.hive_id;
```

---

## 8. If the dashboard looks empty

Most likely, in order:

1. **Not enough data yet.** Only clips uploaded after 2026-09-12 12:40 are
   processed, at ~1.9/hour. A day or two of accumulation makes charts meaningful.
2. **Inner join instead of left join** — see §4.3.
3. **Not filtering `processing_status = 'done'`**, so nulls are being averaged.
4. **The pipeline has stopped.** Check
   `/opt/bee-detection-poc/ademnea/logs/cron.log` on the VPS, or run the health
   query above and see whether anything has reached `done` recently.

If you want the ~15,000 historical clips processed so there's real history to
display, that's a decision for the pipeline owner, not a dashboard change — see
PROGRESS.md.

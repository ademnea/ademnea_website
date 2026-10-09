# Architecture

How the bee-detection PoC is put together, and why. Read this before changing
anything; [COMPUTE-MIGRATION.md](COMPUTE-MIGRATION.md) covers replacing RunPod
with your own hardware.

---

## 1. The problem, and the constraint that shapes everything

Raspberry Pi monitors record short clips of hive entrances and upload them to
the ademnea VPS. Every upload already gets a row in `hive_videos`. Nobody counts
the bees in them.

Fifteen monitors were provisioned; five were reporting as of 2026-09-12, at
~1.9 clips/hour between them. The gap matters — several sizing decisions in this
document are made against the measured rate.

The job is to count bees per clip and write the numbers into `bee_counts`, which
the existing Laravel dashboard already reads.

**The constraint: the VPS has ~88 MB of free memory.** It cannot load PyTorch,
let alone run a YOLO model. Everything about this design follows from that one
fact. Inference happens elsewhere, and the VPS's only job is bookkeeping — find
work, ask someone else to do it, write down the answer.

A second constraint follows from the first: whatever does the inference must not
be paid for while idle, because the hives produce roughly **two clips an hour**
and a permanently-rented GPU would cost more than the project.

---

## 2. Data flow

```
        ┌──────────────┐
        │ hive_videos  │  15,203 rows. Written by the existing upload
        │   (MySQL)    │  pipeline. We only ever SELECT from it.
        └──────┬───────┘
               │  1. "which videos have no counts yet?"
               ▼
        ┌──────────────┐
        │ dispatcher   │  cron, every 30 min, on the VPS.
        │    .py       │  ~40 MB RSS. No ML libraries.
        └──────┬───────┘
               │  2. claim the row  ──────────────┐
               │     (INSERT 'processing')        │  committed BEFORE
               │                                  │  any network call
               │  3. POST {"input":{"video_url"}} ▼
               ▼                            ┌──────────────┐
        ┌──────────────┐                    │  bee_counts  │
        │ RunPod GPU   │                    │   (MySQL)    │
        │  worker      │                    └──────▲───────┘
        │ handler.py   │                           │
        └──────┬───────┘                           │
               │  4. GET the clip                  │
               ▼                                   │
        ┌──────────────┐                           │
        │ Apache on    │                           │
        │ the VPS      │  5. metrics ──────────────┘
        └──────────────┘
```

**The clip never passes through the dispatcher.** The dispatcher sends a URL;
the worker fetches the video itself, directly from Apache, at datacentre
bandwidth. This is why the dispatcher's memory stays flat regardless of clip
size, and why there is no payload ceiling.

---

## 3. The three components

### `ademnea/dispatcher.py` — the only thing that runs on the VPS

A single-shot script. It claims one batch, processes it, exits. It is **not a
daemon** — cron re-invokes it rather than restarting it. Roughly 600 lines,
three dependencies (`mysql-connector-python`, `requests`, `python-dotenv`), no
ML.

Its structure, in the order the code runs:

| Section | What it does |
|---|---|
| `Config` / `load_config()` | Every setting comes from `.env`. `_require()` fails loudly on a missing or placeholder value rather than defaulting to something wrong. |
| `WORK_QUERY` / `find_work()` | Finds videos with no `bee_counts` row, or a `failed` one under the attempt limit, above the watermark. Newest first. |
| `claim()` | **The idempotency boundary.** See §4. |
| `run_inference()` | Submit-then-poll against RunPod, inside one wall-clock budget. |
| `_extract_output()` | Validates the worker's reply before it is trusted. |
| `record_success()` / `record_failure()` | Terminal state. |

### `runpod/handler.py` — the GPU worker

Runs inside the Docker image, which has the model weights baked in. Its whole
job is: fetch clip → sample frames → run YOLO → aggregate → return numbers.

Two design points worth knowing:

- **The model is loaded at import, not per request.** RunPod keeps a worker warm
  between invocations, so only the cold start pays the ~0.2 s weight load.
  Loading inside `handler()` would re-pay it on every video.
- **The handler never raises.** A crashed worker gives the dispatcher no
  diagnosis; an `{"error": "..."}` payload lands in `bee_counts.error_message`
  where you can read it. Every exception path is caught and converted.

### `ademnea/db_migration.sql` — one-time schema change

Adds nine columns and two indexes to `bee_counts`. Idempotent: every statement
is guarded by an `INFORMATION_SCHEMA` check and prints `ADDED` or `SKIPPED`. It
never drops or alters existing data, and `bee_count` is left untouched.

It needs `CREATE ROUTINE` because it builds two temporary helper procedures and
drops them again at the end — so it must be run as an admin, not as
`bee_dispatcher`.

---

## 4. The invariant that matters most: claiming

Two dispatcher runs can overlap — a slow run plus a cron tick, or a manual run
plus cron. Without care, both would process the same video and write two rows.

`claim()` prevents that:

```
SELECT ... FROM bee_counts WHERE hive_video_id = ? FOR UPDATE
  → no row?      INSERT with processing_status='processing'
  → 'failed'?    UPDATE to 'processing', attempts += 1
  → 'processing'? return None  (someone else owns it — skip)
  → 'done'?      return None   (unless --video-id forces it)
COMMIT                                    ← before any network call
```

The commit happens **before** the RunPod request. That ordering is the whole
point: the row is visibly owned for the entire duration of a call that may take
seven minutes. Two racing dispatchers serialise on the `FOR UPDATE`, and the
loser sees `'processing'` and moves on.

**The cost of this design:** a run killed mid-flight leaves a row stranded in
`'processing'` forever, because `WORK_QUERY` never re-selects that state.
Recover with `--video-id <id>`, or by resetting the row. This is a deliberate
trade — a stale-claim timeout would mean a row could be claimed twice, which is
worse than a row needing manual attention.

There is **no `UNIQUE` key on `hive_video_id`**. Adding a constraint to a live
table can fail on pre-existing duplicates. The claiming logic is correct with or
without it; the migration reports whether duplicates exist (currently `0`), and
the `ALTER` sits commented out if you ever want it.

---

## 5. The watermark

`DISPATCHER_MIN_HIVE_VIDEO_ID` — only `hive_videos.id` **greater than** this is
eligible for the scheduled batch.

It exists because ~15,000 clips predate the PoC. Without it, the first cron tick
would begin a ten-day, twelve-GPU-hour march through the archive.

Set to `820272`, the maximum id at cutover (2026-09-12 12:40).

- **An id, not a date**, because ids are auto-increment: "arrived after cutover"
  is exactly "id greater than N", with no dependence on Pi clocks. A date
  watermark would permanently skip clips from any monitor whose clock is wrong.
- **Nothing is written to the database** to express the skip, so it is fully
  reversible — set it to `0` to release the backlog.
- **`--video-id` ignores it entirely**, so any historical clip can still be
  processed on demand.

One subtlety in the SQL: the original `WHERE` was `bc.id IS NULL OR (...failed...)`.
Adding `AND hv.id > %s` without parenthesising that `OR` would have let failed
retries bypass the watermark. The parentheses are load-bearing.

---

## 6. Interfaces

Three contracts. Changing either side of any of them breaks the pipeline.

### 6.1 MySQL

**Read** `hive_videos`: `id`, `path`, `hive_id`, `created_at`. Never written.

**Write** `bee_counts`. Pre-existing columns `id`, `timestamp`, `hive_video_id`,
`video_filename`, `bee_count`, `created_at`, `updated_at`; the migration adds:

| Column | Type | Meaning |
|---|---|---|
| `mean_count` | float | average *simultaneous* bees across sampled frames |
| `max_count` | int | busiest single frame |
| `activity_fraction` | float | fraction of frames with ≥1 detection |
| `frames_analyzed` | int | how many frames were sampled |
| `model_version` | varchar(64) | `yolo26x-poc` |
| `processing_status` | enum | `pending` / `processing` / `done` / `failed` |
| `error_message` | text | why it failed |
| `attempts` | tinyint | retry counter, capped by `DISPATCHER_MAX_ATTEMPTS` |
| `processed_at` | timestamp | when it reached `done` |

`bee_count` is set to `max_count` on success — that column is what the existing
dashboard reads, so it must stay populated.

### 6.2 The worker HTTP contract

This is the part that is RunPod-shaped, and the part you replace when you move
to your own compute.

**Submit** — `POST {RUNPOD_ENDPOINT_URL}` with `Authorization: Bearer <key>`:

```json
{"input": {"video_url": "http://196.43.168.57/hivevideo/5_2026-09-12_142230.mp4"}}
```

**Response**, either terminal immediately or a job to poll:

```json
{"id": "...", "status": "IN_QUEUE"}
{"id": "...", "status": "COMPLETED", "output": { ...metrics... }}
```

**Poll** — `GET {RUNPOD_ENDPOINT_URL minus /runsync}/status/{job_id}` until
`status` is terminal.

- `COMPLETED` → `output` is extracted and validated
- `FAILED`, `CANCELLED`, `TIMED_OUT` → recorded as a failure
- anything else → keep polling until the budget runs out

**The `output` object must contain** `mean_count`, `max_count`,
`activity_fraction`, `frames_analyzed`. `_extract_output()` rejects the reply if
any are missing, and treats an `error` key as a failure. `model_version` and
`processing_seconds` are also returned and used.

### 6.3 Video over HTTP

The worker fetches `{HIVEVIDEO_BASE_URL}/{hive_videos.path}` — plain,
unauthenticated GET. The clips are world-readable at that URL. That is
pre-existing, and it is what lets a stateless GPU worker fetch them. If it ever
becomes unacceptable, the fix is signed expiring URLs, which means moving the
clips to object storage.

`video_b64` is supported as a fallback and is what `test_local.py` uses for
files on local disk.

---

## 7. Configuration reference

All from `.env`, which is gitignored and **differs between the laptop and the
VPS**. `.env.example` documents every key.

| Variable | VPS value | Notes |
|---|---|---|
| `MYSQL_HOST` | `127.0.0.1` | |
| `MYSQL_PORT` | **`3306`** | `3307` on the laptop — that's the SSH tunnel's local port and exists nowhere else |
| `MYSQL_USER` | `bee_dispatcher` | scoped: `SELECT, INSERT, UPDATE, ALTER` on `bee_counts`, `SELECT` on `hive_videos`. **No `DELETE`, no `LOCK TABLES`** |
| `RUNPOD_ENDPOINT_URL` | `https://api.runpod.ai/v2/<id>/runsync` | the `/status` URL is derived from it, so only one is configured |
| `HIVEVIDEO_BASE_URL` | `http://196.43.168.57/hivevideo` | what the worker fetches from |
| `HIVEVIDEO_DIR` | `/var/www/html/ademnea_website/public/hivevideo` | local existence check only |
| `DISPATCHER_BATCH_SIZE` | `5` | videos per run |
| `DISPATCHER_MAX_ATTEMPTS` | `3` | then abandoned as `failed` |
| `DISPATCHER_REQUEST_TIMEOUT_SECONDS` | **`600`** | must cover a worst-case cold start, not just inference — see §8 |
| `DISPATCHER_MIN_HIVE_VIDEO_ID` | `820272` | the watermark |

Worker-side settings (`MODEL_PATH`, `SAMPLE_FPS=5`, `IMG_SIZE=640`,
`CONF_THRESHOLD=0.4`, `BATCH_SIZE=8`) are baked into the image and overridable
via endpoint env vars without a rebuild.

---

## 8. Timing, and the thing that will confuse you

Measured on real hive clips:

| Situation | Total |
|---|---|
| Warm worker | **~3 s** |
| Worker recently released, image still cached | ~16–75 s |
| Genuinely cold — new worker must pull the 7.4 GB image | **~400 s** |
| Inference alone, GPU | 2.5 s (~0.05 s/frame) |
| Inference alone, CPU | 21 s (~0.44 s/frame) |

**Cold start dominates completely.** Inference is 2.5 seconds; getting a machine
ready to do it can take nearly seven minutes.

This produces a failure that looks like a bug and is not:

```
FAILED: InferenceError: timed out after 180s (job sync-... still IN_QUEUE)
```

`IN_QUEUE` means RunPod never assigned a worker — not that inference was slow.
It is almost always a cold endpoint pulling the image. `DISPATCHER_REQUEST_TIMEOUT_SECONDS`
is `600` rather than `180` for exactly this reason.

The arithmetic behind the 30-minute cron: clips arrive about **1.9/hour**, and
the endpoint's 30 s idle timeout means the worker is always dead by the time the
next one shows up. Running every 5 minutes would fire 288 times a day to handle
~45 clips, pay a cold start on nearly every one, and overlap itself. Every 30
minutes with `flock -n` gives ample headroom and 48 cold starts instead of 288.

Keeping workers warm instead would mean paying for an idle A4000 continuously —
which defeats the reason serverless was chosen.

---

## 9. What this PoC deliberately is not

- **No tracking.** Counts are per-frame detections, not identified individuals.
  `mean_count` is average *simultaneous* bees, not bees per hour.
- **No directional counting.** No in/out, no net traffic.
- **No dashboard work.** This stops at MySQL. The columns are populated;
  rendering them is someone else's task.
- **No monitoring or alerting.** Nothing pages when the endpoint dies. Use the
  health-check queries in README §4.
- **No backfill.** 15,018 historical clips are skipped by the watermark, and 185
  legacy `bee_counts` rows are invisible to the dispatcher. Both are reversible
  decisions, neither is made.
- **No retry sophistication.** A flat attempt cap, no exponential backoff, no
  dead-letter queue.
- **No cost controls.** One worker, no budget cap.

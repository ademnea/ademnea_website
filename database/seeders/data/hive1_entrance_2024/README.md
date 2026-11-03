# Hive 1 entrance counts, 14 Sep – 18 Dec 2024

Real data. Exported from the live `bee_counts` table on 2 Nov 2026 by
`bee-detection-poc/ademnea/export_eval.py` after the deployed `yolo26x-poc`
model processed every hive 1 clip in the range. Packaged as
`bee-eval-2024-09-to-12-hive1.zip`; the three files here are copied from it
unchanged. See "Bee Count Model Review" (HTML) for the model evaluation.

Loaded by `Database\Seeders\Hive1Entrance2024Seeder` into its own farm
(`Hive 1 entrance evaluation (Sep-Dec 2024)`) with one hive. On this machine
that is farm 11, hive 43.

## How the CSV maps onto the schema

| results.csv column | goes to | note |
|---|---|---|
| hive_video_id | hive_videos.id, bee_counts.hive_video_id | original live-DB id kept, for traceability |
| hive_id | – | the seeder's own hive id is used instead (live hive 1 is not the mock DB's hive 1) |
| created_at | hive_videos.created_at/updated_at, bee_counts.timestamp | exactly as exported |
| path | hive_videos.path, bee_counts.video_filename | bare filename, as the live DB stores it |
| status | bee_counts.processing_status | |
| mean_count, activity_fraction | bee_counts.* | **rounded to 2 dp by the schema** (`double(8,2)`); CSV has 4 dp |
| max_count | bee_counts.max_count and bee_counts.bee_count | dispatcher mirrors max into bee_count; 0 for failed rows |
| frames_analyzed, model_version, processed_at | bee_counts.* | |
| error | bee_counts.error_message | |

## Assumptions (nothing in the export says otherwise)

- Farm location is set to Makerere University, Kampala. Not recorded in the export.
- Hive `entrance_orientation`, `hive_type`, `site_group`, `colony_epoch_started_on` are left null.
- `bee_counts.attempts` = 1 for every row. The export has no attempts column.
- `bee_counts.created_at/updated_at` = `processed_at` for done rows, null for failed rows.
- Filenames keep their live-DB prefix `1_` although the hive gets a new id here.
  The analysis only reads the date-time part of the name.
- Timestamps: before 29 Nov 2024 `created_at` is 3 h behind the time in the
  filename (server stored UTC, filename is local, UTC+3). From 29 Nov on they
  agree. The analysis takes capture time from the filename, so hours of day
  are consistent throughout; `created_at` is only used for range filtering.

## How the analysis was run, and why in two pieces

```
php artisan db:seed --class=Hive1Entrance2024Seeder
php artisan bee:analyse --farm=11 --from=2024-09-14 --to=2024-10-19
php artisan bee:analyse --farm=11 --from=2024-10-23 --to=2024-12-18
```

A single run over the whole range produced **77 days of `insufficient_data`,
no baseline and no classification**. `CountAnalysisService::buildDailyRows`
sets `n_expected` (captures per full day) to the busiest day in the range.
Normal days have 4 scheduled captures (00, 06, 12, 18 local), but on 20–21
Oct someone recorded ad hoc: 65 clips in two days, 20 of them processed on
21 Oct. `n_expected` became 20, a normal day scored 0.20 completeness, under
the 0.30 `min_completeness` floor, and nothing was ever judged.

Running in two ranges keeps 20–21 Oct out of both windows (the service pads
the `created_at` filter by one day each side, hence the gap to 23 Oct).
`n_expected` then resolves to 4 for Sep–Oct and 6 for Oct–Dec. Days 20–22 Oct
have feature rows but no deviation or verdict.

## What the analysis made of it

- 74 days judged, 73 of them conclusive (≥ 0.30 complete), only 13 "complete" (≥ 0.80).
- Initial baseline (14–20 Sep, 7 days): `base_daily` 1.05, `residual_sd` 1.34.
- Rolling baseline (6 Nov – 4 Dec, 14 days): `base_daily` 0.84, `residual_sd` 0.55.
- Verdicts: 39 × `hive_trend_decline` (low confidence, solo mode), 1 × `insufficient_data`.
  No sudden drop, no spike. 17 Dec (z = −6.8) was a one-day zero and correctly not escalated.
- Classification: size `unclassified` (no peers), condition `declining`,
  while `pct_of_baseline` = 1.39.

Hourly profile from the rolling baseline (median bees per frame):
00h 0.00 · 06h 0.00 · 12h 0.57 · 18h 0.31. All of the daily total comes from
the two daytime clips.

## Assumptions in the analysis that this data puts in question

1. **`n_expected` from the busiest day.** One ad hoc capture session
   silenced 77 days. Needs a declared schedule per hive, or a robust
   statistic (median or mode of clips/day), or a cap.
2. **`total` = sum of `mean_count` over the day.** Depends on how many clips
   were captured. 21 Oct sums to 14.2 against a baseline of ~1 purely because
   20 clips were taken. Mean-per-daytime-clip or a schedule-normalised total
   would be comparable across days.
3. **Trend threshold −2 %/day.** Fires on 39 of 74 days. With a daily total
   of about one bee per frame built from two useful clips, day-to-day
   log-ratio noise is large (`residual_sd` 0.55 even in the steady Nov–Dec
   window, 1.34 in the first week). A 10-day OLS slope on that noise crosses
   −2 %/day routinely. Either the threshold needs to scale with
   `residual_sd`, or the slope needs more days, or the alert needs a minimum
   drop in level before it is reported.
4. **Time to profile a hive: 7 days initial, 5 usable minimum.** The first
   week gave `residual_sd` 1.34, so z-based detection was effectively off
   until the rolling baseline existed (which here needed data up to 4 Dec, i.e.
   `rolling_window_days` 28 + `baseline_lag_days` 14 after the first clip).
5. **Rolling window 28 days.** Only 14 usable days landed in it because of a
   two-week outage (14–28 Nov). It worked, but 5 usable days out of 28 would
   also have passed, which is thin for a median.
6. **Average count for a healthy entrance hive.** On this hive and model:
   median 0.57 bees per frame at noon, 0.31 at 18:00, 0 at night; per-clip
   `max_count` median 2, highest 8. Daily `total` (sum of 4 clips) 0.8–1.1.
   These are an order of magnitude below the demo seeder's synthetic levels.
7. **Completeness ≥ 0.80 before escalating.** With 4 scheduled clips and
   `n_expected` 6, no normal day ever reaches it; escalation is unreachable
   on this schedule.
8. **Peers.** Single-hive farm means solo mode throughout: `size_class` stays
   `unclassified` and every alert is low confidence.
9. **Night clips.** 00h and 06h are always zero and count as usable
   captures. They dilute `mean_of_means` and `mean_activity_fraction` but not
   `total`.

# Evaluation summary: 2024-09-14_2024-12-19_hive1

- clips: **406**, done 262, failed 144 (**35.5%** failure rate)
- captured: 2024-09-14 09:00:06 .. 2024-12-18 18:00:05
- model: yolo26x-poc
- zero-count clips: 30%

### Most common errors

- 139 x `InferenceError: RunPod job FAILED: video download failed: 404 Client Error: Not Found for url: http://196.43.168.57/hive`
- 1 x `InferenceError: RunPod job FAILED: downloaded 0 bytes from http://196.43.168.57/hivevideo/1_2024-09-15_000006.mp4`
- 1 x `InferenceError: RunPod job FAILED: downloaded 0 bytes from http://196.43.168.57/hivevideo/1_2024-10-20_195715.mp4`
- 1 x `InferenceError: RunPod job FAILED: downloaded 0 bytes from http://196.43.168.57/hivevideo/1_2024-10-20_215234.mp4`
- 1 x `InferenceError: RunPod job FAILED: downloaded 0 bytes from http://196.43.168.57/hivevideo/1_2024-10-21_085919.mp4`

## Distributions

| group | metric | n | median | mean | p90 | max |
|---|---|---|---|---|---|---|
| all | max_count | 262 | 2.00 | 1.85 | 4.00 | 8.00 |
| all | mean_count | 262 | 0.27 | 0.39 | 1.00 | 2.63 |
| all | activity_fraction | 262 | 0.21 | 0.26 | 0.63 | 0.98 |
| hive 1 | max_count | 262 | 2.00 | 1.85 | 4.00 | 8.00 |
| hive 1 | mean_count | 262 | 0.27 | 0.39 | 1.00 | 2.63 |
| hive 1 | activity_fraction | 262 | 0.21 | 0.26 | 0.63 | 0.98 |

## Hour-of-day curve (main plausibility check)

Expect a daytime hump and near-zero counts at night. A flat curve, or high night counts, points at false positives (shadows, debris, camera noise) or at `created_at` not being local capture time.

| hour | n | median mean_count | zero-count % | |
|---|---|---|---|---|
| 00 | 19 | 0.00 | 100% | `` |
| 03 | 15 | 0.00 | 100% | `` |
| 04 | 2 | 0.15 | 0% | `█████` |
| 05 | 1 | 0.18 | 0% | `██████` |
| 06 | 22 | 0.00 | 86% | `` |
| 07 | 6 | 0.73 | 0% | `████████████████████████` |
| 08 | 6 | 0.62 | 0% | `████████████████████` |
| 09 | 56 | 0.55 | 0% | `██████████████████` |
| 10 | 4 | 0.93 | 0% | `██████████████████████████████` |
| 11 | 3 | 0.10 | 0% | `███` |
| 12 | 23 | 0.18 | 4% | `██████` |
| 13 | 3 | 0.84 | 0% | `███████████████████████████` |
| 14 | 2 | 0.02 | 50% | `█` |
| 15 | 56 | 0.52 | 4% | `█████████████████` |
| 16 | 3 | 0.00 | 67% | `` |
| 17 | 2 | 0.37 | 0% | `████████████` |
| 18 | 22 | 0.32 | 9% | `██████████` |
| 19 | 2 | 0.00 | 100% | `` |
| 21 | 15 | 0.00 | 100% | `` |

Day (09-17h) median mean_count **0.45** (n=150) vs night median **0.00** (n=52).

## Spot check against human counts

No human counts yet. Watch each clip in `spotcheck.csv`, fill in `human_max_count`, and re-run this script.

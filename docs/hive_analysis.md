# Bee Count Analysis

Turns hive-entrance bee counts into things a beekeeper can act on: which hive is
behaving unusually, whether that is the hive's problem or the whole site's, and
how each hive compares to its neighbours.

Counts only. No weather source, no in-hive sensors. §9 covers how sensors slot
in later without reworking anything.

---

## 1. What it does, in one picture

```
bee_counts ──▶ 1 aggregate ──▶ 2 baselines ──▶ 3 compare ──▶ 4 detect ──▶ 5 conclude
+hive_videos        │               │                                          │
                    ▼               ▼                                          ▼
          hive_daily_features  hive_baselines              hive_insights, hive_classifications
                                                                               │
                                                                               ▼
                                                                        /analysis pages
```

Five passes, each depending only on the one before it. **Nothing is computed on a
page load** — `php artisan bee:analyse` writes the tables, the controller reads
them.

---

## 2. Running it

```bash
php artisan bee:analyse                            # every farm, last 56 days
php artisan bee:analyse --farm=1                   # one farm
php artisan bee:analyse --farm=1 --from=2026-08-01 --to=2026-08-31
```

Scheduled nightly at 02:30 in `app/Console/Kernel.php`, after the day's clips
have been through the detector.

### Demo data

The live pipeline has no history yet, so there is a seeder that fabricates four
weeks for one apiary of three hives:

```bash
php artisan db:seed --class=DemoBeeCountSeeder
php artisan bee:analyse --farm=1
```

It is idempotent — it clears its own rows first. What it plants and why is in §8.

### Checking it worked

```bash
php artisan tinker --execute="echo App\Models\HiveDailyFeature::count();"
php artisan tinker --execute="print_r(App\Models\HiveInsight::select('hive_id','verdict')->selectRaw('count(*) n, min(date) first_seen')->groupBy('hive_id','verdict')->orderBy('hive_id')->get()->toArray());"
php artisan tinker --execute="print_r(App\Models\HiveClassification::current()->get(['hive_id','size_class','condition_class','size_ratio'])->toArray());"
```

> On PHP 8.4 against Laravel 8, deprecation warnings drown everything. Use
> `alias art='php -d error_reporting="E_ALL & ~E_DEPRECATED" artisan'`.

---

## 3. Tables

| Table                  | Holds                                                     | Written by              |
| ---------------------- | --------------------------------------------------------- | ----------------------- |
| `hive_daily_features`  | One row per hive per day. The atom everything else reads. | pass 1, enriched by 3–4 |
| `hive_baselines`       | What "normal" is for each hive                            | pass 2                  |
| `hive_insights`        | Farmer-facing verdicts                                    | pass 5                  |
| `hive_classifications` | Size and condition, as history                            | pass 5                  |
| `apiary_weather`       | Empty. Exists so adding weather needs no migration.       | —                       |

Plus columns added to existing tables:

- `hives` — `entrance_orientation`, `hive_type`, `colony_epoch`,
    `colony_epoch_started_on`, `site_group`
- `users` — `role` (the model already listed it in `$fillable`; the column was
    missing, so mass-assignment had been failing silently)

### Three design choices worth knowing

**`hive_daily_features` is source-agnostic.** No column assumes the numbers came
from video. Sensor modalities add columns here and nothing above changes.

**`hive_classifications` is history, not a column on `hives`.** "Was strong in
September, average in October" is itself the signal.

**Class names are `string`, not `enum`.** Thresholds will be retuned and classes
will be added; altering an enum on a populated table is painful.

---

## 4. Pass 1 — aggregate

### bee_counts has no hive_id

It reaches a hive only through `hive_videos`:

```sql
FROM bee_counts bc
JOIN hive_videos hv ON hv.id = bc.hive_video_id
WHERE bc.processing_status = 'done'
```

Always `processing_status = 'done'` — every metric column is NULL until the
dispatcher finishes, and averaging NULLs silently produces nonsense.

### The clip timestamp is in the filename, not created_at

`hive_videos.created_at` is the **insert** time, which lags capture. The capture
time is in the path: `9_2026-05-17_232640.mp4`. The service parses that and falls
back to `created_at` only when the name does not match.

This matters because every hour-of-day feature depends on it. A two-hour upload
lag would shift a hive's entire daily profile.

### 1,062 clips become 83 rows

![aggregation](img/g2_aggregation.png)

That is one row per hive per day — 27 + 28 + 28. Hive 1 has 27 because one day
has no clips at all.

### Validity — four distinct conditions

| Condition                              | Appearance                              | Rule                                                |
| -------------------------------------- | --------------------------------------- | --------------------------------------------------- |
| Row absent                             | No record for that timestamp            | Leave the gap. **Never interpolate.**               |
| `mean_count` NULL, `frames_analyzed` 0 | Clip never uploaded or failed to decode | `NO_OBSERVATION` — distinct from zero               |
| `frames_analyzed` < 10                 | Short or truncated clip                 | `LOW_CONFIDENCE` — shown, excluded from baselines   |
| `mean_count` 0, `frames_analyzed` 50   | A real observation                      | Keep. A valid zero is the strongest signal we have. |

"No bees" and "no video" must never collapse into the same number. An
interpolated gap renders a dead colony as a healthy one, which is the most
dangerous failure this system can have.

> **Precedent.** `HiveDataController::getTemperatureData` already does
> `$honeyTemp == 2 ? null : $honeyTemp` — the sentinel-2 convention from the
> firmware. This layer follows the same idea rather than inventing a new one.

**Forward-fill is prohibited.** It produces a flat series that mimics a colony
which has stopped responding, inverting the meaning of missing data.

### Completeness gate

A hive-day below 30% completeness produces no conclusion. Silence is correct —
an alert built on a quarter of a day is worse than no alert.

---

## 5. Pass 2 — baselines

### Normal is a curve, not a number

Three bees at 06:00 is not three bees at 13:00. A single high/low threshold on
raw counts is wrong by construction, so the baseline is a **diurnal profile**,
learned per hive and stored as JSON:

```json
{"6": {"median": 0.4, "q25": 0.2, "q75": 0.7}, "7": {...}}
```

"High" and "low" are quantiles of that hive's own distribution at that hour. The
same raw number is high for a weak hive and normal for a strong one — which is
the point.

### Two baselines

| Type      | Window                                            | Use                                              |
| --------- | ------------------------------------------------- | ------------------------------------------------ |
| `initial` | First 7 valid days of a colony epoch, then frozen | "How does this hive compare to when it started?" |
| `rolling` | 28 days, **ending 14 days before today**          | What the detectors compare against               |

### The lag is the important part

A baseline computed up to today cannot be a fair reference for today.

![baseline lag](img/g3_baselinelag.png)

This was a real bug, caught in testing. Hive 2 dropped to a third of normal on
day 16. With the window spanning all 28 days, its baseline came out at **16.96**
— the hive was being judged against its own collapsed state, read as `stable`,
and the drop never triggered a shift alert. Its `residual_sd` was 0.58, more than
double the other hives', which is the contamination measured directly.

With a 14-day lag the baseline is **47.19** and the drop stays visible.

`baseline_lag_days` trades sensitivity against stability. Fourteen is
conservative: slower to follow genuine seasonal drift, but a level shift stays
detectable for a fortnight before it begins contaminating the reference.

### Fallback

When there is not enough lagged history — a hive's first weeks, or any hive after
re-colonisation resets its epoch — `rolling` is deleted rather than written from
a contaminated window, and `applyBaseline()` falls back to `initial`, which is
uncontaminated by definition. The row records which was used in
`threshold_version`, e.g. `v1/initial`.

---

## 6. Passes 3–4 — compare and detect

### z — deviation in units of the hive's own noise

```
ratio = total / expected          1.0 means exactly as expected
z     = log(ratio) / residual_sd
```

Logged so halving and doubling are symmetric. Divided by that hive's own
variability so a quiet, regular hive and an erratic one are held to the same
standard of _surprise_ rather than the same number of bees.

`expected = base_daily × conditions_factor`, and `conditions_factor` is **1.0**
throughout — with no weather and no exterior sensor there is nothing to scale by.
When one arrives, only `applyBaseline()` changes.

### Apiary comparison — the only control we have

Hives at one site share weather, forage and season. A hive diverging from its
neighbours is diverging for a hive-specific reason.

Two features, and the rank-based one is safer: bees drift between neighbouring
hives, so a rise at one can be a fall at another without either colony changing.

- `share` — this hive ÷ apiary total that day
- `vs_median` — ratio to the apiary median

### What the slope is fitted to matters more than the threshold

![share vs ratio](img/g4_share.png)

Second real bug from testing. Fitted to each hive's own activity, the site-wide
dip on day 9 and the recovery after it read as a decline on **all three hives** —
three false alarms from one spell of bad weather.

With no weather data there is nothing to divide that out, so the apiary does the
job instead:

| Mode            | Slope fitted to                                            | Why                                                                                  |
| --------------- | ---------------------------------------------------------- | ------------------------------------------------------------------------------------ |
| `peer_compared` | `log(share)`                                               | A site-wide dip moves every hive together, so shares barely move and the dip cancels |
| `solo`          | `log(ratio)`, excluding days already flagged `apiary_wide` | Weaker — a partial dip that doesn't trip the apiary test still leaks through         |

That weakness is the cost of having fewer than three reporting hives, and it is
recorded per row rather than hidden.

### Shift versus drift

Both eventually push `z` below the threshold. They are separated by `dz`, the
day-over-day change: **a shift jumps several units in a day, a drift creeps.**

Without that test a gradual decline gets labelled abrupt, and the farmer is told
the wrong thing about urgency.

A day already flagged site-wide is never treated as a hive-specific shift, however
deep it is.

---

## 7. Pass 5 — conclude

### The reconciliation ladder

![ladder](img/g5_ladder.png)

Evaluated in order, **first match wins**. Order is a design decision, not an
implementation detail — the guards run before anything is blamed on a colony.

Step 3 is absent. That is the single biggest gap in this version, and it is why
`analysis_mode` exists.

### Modes

![modes](img/g8_modes.png)

Confidence on the same verdict differs by mode. In `solo`, a sudden drop is
reported as medium confidence with the sentence _"too few other hives are
reporting here to rule out a site-wide cause."_ In `peer_compared` it is high
confidence and _"the other hives at this site are unaffected."_

### Wording

Counts establish a **state**, never a **cause**. Absconding, theft and colony
death produce an identical signature.

| Never write                   | Write instead                                                |
| ----------------------------- | ------------------------------------------------------------ |
| "Your colony absconded"       | "Hive 4 shows no activity for three days"                    |
| "Your hive is sick"           | "Activity is well below normal and below its neighbours"     |
| "Pesticide exposure detected" | "Activity dropped sharply while other hives were unaffected" |

### Classification — two independent axes

![classes](img/g6_classes.png)

**Size** is a level from the baseline relative to apiary peers. **Condition** is a
trajectory from the trend. Keeping them apart matters: a small hive can be
perfectly healthy and a large one can be failing.

| size_class     | Rule                              |
| -------------- | --------------------------------- |
| `strong`       | ≥ 1.30 × apiary median baseline   |
| `average`      | 0.70 – 1.30 ×                     |
| `weak`         | 0.40 – 0.70 ×                     |
| `very_weak`    | < 0.40 ×                          |
| `unclassified` | fewer than 3 peers with baselines |

| condition_class | Rule                                        |
| --------------- | ------------------------------------------- |
| `establishing`  | ≥ +1.5%/day sustained                       |
| `stable`        | no sustained trend                          |
| `declining`     | ≤ −2%/day for 4+ days                       |
| `critical`      | ≤ 25% of baseline for 3+ days               |
| `silent`        | zero detections for 2+ days with valid data |
| `unknown`       | uncharacterised                             |

Two notes on the thresholds. They are **ratios, not absolute counts** — published
colony-size figures are in bee entries per unit time and do not convert to
bees-visible-per-frame. The 0.40 / 1.30 split approximates the roughly fourfold
span between small and large colonies reported in the literature. And the class
names describe what was measured; none of them asserts biology we cannot observe,
which is why there is no "healthy" or "dying".

---

## 8. Known behaviour and limits

The demo data plants three different kinds of change plus four edge cases. What
the system found:

![what fired](img/g7_fired.png)

| Planted                       | Detected                         | Lag      |
| ----------------------------- | -------------------------------- | -------- |
| Site-wide dip, day 9          | `apiary_wide` on all three hives | same day |
| Hive 2 abrupt drop, day 16    | `hive_anomaly_sudden` day 24     | ~7 days  |
| Hive 3 decline 3%/day, day 12 | `hive_trend_decline` day 21      | ~8 days  |
| Hive 1 no event               | nothing                          | correct  |
| Camera offline, day 13        | no rows, so no conclusion        | —        |
| Upload failure, day 21        | `monitor_offline`                | same day |

Five things worth flagging:

**Detection takes days, by design.** The shift detector requires persistence
before firing. That delay is the price of not alerting on every noisy day.

**A decline can be reported before a shift.** Hive 2's trend detector fired on
day 15, the shift detector on day 24. The ladder checks `hive_anomaly_sudden`
first, so once both fire the farmer sees the right one — but for several days they
are told "declining" when it was actually abrupt.

**Sub-day events are invisible.** An afternoon orientation-flight burst was
planted on hive 1 and never fired as a spike: one busy afternoon diluted inside a
daily total falls below the z threshold. Daily aggregation cannot see events
shorter than a day.

**Counts compress under dense traffic.** Entrance counting degrades with
occlusion, so high counts understate real activity. Any threshold near the
observed maximum measures the detector, not the bees.

**Thresholds are unvalidated.** Everything in `config/beeanalysis.php` was chosen
to be reasonable, not because it was tuned against known outcomes. Every feature
and insight row carries `threshold_version` so a past verdict can be reproduced —
bump it whenever a threshold changes.

---

## 9. Adding sensor data later

![sensor plan](img/g9_sensorplan.png)

Sensors do not simply take weather's slot in the ladder, because most of them are
not independent of the colony.

**Weather is exogenous.** Rain is outside the hive, hits every hive equally, and
cannot be caused by the colony. That is exactly why it can _explain away_ low
activity.

**Interior sensors are endogenous.** In-hive temperature, CO₂ and weight are
partly _produced by_ the colony. If interior temperature is odd and activity is
low, that is not an innocent explanation — it is a second symptom. Using it the
way weather is used would invert the logic and suppress the alerts we want.

So they split in two:

| Signal                                       | Role                      | Where                               |
| -------------------------------------------- | ------------------------- | ----------------------------------- |
| Exterior temp and humidity                   | Exogenous conditions      | Step 3 — explain away               |
| Apiary peers                                 | Shared-conditions control | Step 2 — already built              |
| Interior temp, CO₂, weight, vibration, audio | Endogenous corroboration  | Step 5 — after a flag, never before |

### The exterior field is already being collected

`hive_temperatures.record` is stored as `honey*brood*exterior`. **The exterior
field is ambient temperature measured at the hive** — on-device weather, with no
API and no new hardware, and better than a weather station because it is at the
apiary rather than kilometres away.

It covers the two strongest documented drivers of flight activity. It does not
cover rain, solar or wind — a wet day with mild temperature will still look
normal to a temperature-only model.

### What changes when it arrives

1. `applyBaseline()` sets `conditions_factor` from the exterior reading instead
   of 1.0, and `conditions_source` to `exterior_sensor`
2. `reconcile()` gains step 3
3. A sensor-corroboration check is added at escalation

Nothing else moves. `conditions_factor`, `conditions_source`, `expected_total`
and `apiary_weather` already exist for this.

---

## 10. Files

```
app/Services/Analysis/CountAnalysisService.php     all five passes
app/Console/Commands/AnalyseHiveCounts.php         bee:analyse
app/Http/Controllers/Admin/HiveAnalysisController.php
app/Models/HiveBaseline.php
app/Models/HiveDailyFeature.php
app/Models/HiveInsight.php
app/Models/HiveClassification.php
config/beeanalysis.php                             every threshold
database/seeders/DemoBeeCountSeeder.php
resources/views/admin/hiveanalysis/{index,farm,hive}.blade.php

database/migrations/2026_10_27_1001..1008_*.php    8 migrations
```

### Routes

| Route                      | Returns                                         |
| -------------------------- | ----------------------------------------------- |
| `/analysis`                | Farm list with open-insight counts              |
| `/analysis/farm/{id}`      | Hive cards, two charts, insight table           |
| `/analysis/farm/{id}/data` | JSON: `dates`, `series`, `mode`                 |
| `/analysis/hive/{id}`      | Baseline summary, three charts, insight history |
| `/analysis/hive/{id}/data` | JSON incl. `profile` and `base_daily`           |

Charts are ECharts, matching the existing `admin/hivegraphs/*` pages: the
controller returns JSON, the Blade view builds the option object, and
daterangepicker drives the window. **When debugging a blank chart, open the
`/data` route first** — if the JSON is populated the problem is front-end.

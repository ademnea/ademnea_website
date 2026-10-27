<?php

/**
 * Thresholds for the bee-count analysis.
 *
 * These live here, not in code, because they will be retuned once there is
 * real ground truth to tune against. Every feature and insight row records
 * threshold_version, so a past verdict can always be traced back to the
 * numbers that produced it - bump the version whenever a threshold changes.
 */
return [

    'threshold_version' => 'v1',

    /*
    |--------------------------------------------------------------------------
    | Validity
    |--------------------------------------------------------------------------
    |
    | A count from four frames is not a count from fifty. Thin samples are
    | kept for display but excluded from baselines and detection.
    |
    */
    'min_frames'            => 10,
    'min_completeness'      => 0.30,   // below this, no conclusion for the day
    'complete_day'          => 0.80,   // required before escalating anything

    /*
    |--------------------------------------------------------------------------
    | Characterisation
    |--------------------------------------------------------------------------
    */
    'initial_window_days'   => 7,      // frozen, per colony epoch
    'rolling_window_days'   => 28,     // trailing, recomputed each run
    'min_baseline_days'     => 5,      // fewer than this and the hive stays uncharacterised

    /*
    |--------------------------------------------------------------------------
    | Detection
    |--------------------------------------------------------------------------
    |
    | z is the deviation from expectation in units of that hive's own
    | variability, so a quiet hive and an erratic one are held to the same
    | standard of surprise rather than the same number of bees.
    |
    */
    'z_sudden'              => -2.5,   // a low day
    'z_spike'               => 3.0,    // a high day
    'persist_days'          => 2,      // consecutive low days before a shift is declared
    'step_dz'               => -3.0,   // a level shift jumps; a slow decline creeps
    'trend_pct_per_day'     => -0.020, // -2% per day
    'trend_persist_days'    => 4,
    'slope_window_days'     => 10,

    /*
    |--------------------------------------------------------------------------
    | Apiary comparison
    |--------------------------------------------------------------------------
    |
    | Peers only absorb shared conditions if there are enough of them. Below
    | this count the farm runs in 'solo' mode and site-wide causes cannot be
    | ruled out.
    |
    */
    'min_peers_for_mode_b'  => 3,

    /*
    |--------------------------------------------------------------------------
    | Classification
    |--------------------------------------------------------------------------
    |
    | Size is a level relative to apiary peers; condition is a trajectory.
    | The two are independent: a small hive can be perfectly healthy and a
    | large one can be failing.
    |
    | The size bands approximate the roughly fourfold span between small and
    | large colonies reported in the literature. They are ratios, not absolute
    | counts, because published figures are in bee entries per unit time and
    | are not convertible to bees-visible-per-frame.
    |
    */
    'size_strong'           => 1.30,
    'size_average'          => 0.70,
    'size_weak'             => 0.40,

    'condition_window_days' => 14,
    'critical_pct'          => 0.25,   // <= 25% of baseline, the local active-vs-reduced contrast
    'critical_days'         => 3,
    'silent_days'           => 2,
    'establishing_pct'      => 0.015,  // >= +1.5% per day

];

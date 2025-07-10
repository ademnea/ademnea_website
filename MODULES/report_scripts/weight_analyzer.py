#!/usr/bin/env python3

import pandas as pd
import matplotlib.pyplot as plt
import os

def analyze_weight_for_month_year(df, year, month, output_dir=None):
    df.index = pd.to_datetime(df.index)
    df['year'] = df.index.year
    df['month'] = df.index.month
    df_filtered = df[(df['year'] == year) & (df['month'] == month)]

    if df_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}

    if output_dir is None:
        output_dir = "."

    # Plot monthly trend
    plt.figure(figsize=(12, 6))
    df_filtered['record'].plot(
        title=f"Weight Trend for {year}-{month:02d}",
        xlabel="Date",
        ylabel="Weight (kgs)",
        color='b',
        linestyle='-',
        marker='o',
        markersize=4
    )
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.xticks(rotation=45, ha='right')
    plt.tight_layout()

    plot_path = os.path.join(output_dir, "weight_monthly_trend.png")
    plt.savefig(plot_path)
    print(f"Saved weight plot to: {plot_path}")  # Optional debug
    plt.close()

    # Calculate statistics
    mean_weight = round(df_filtered['record'].mean(), 2)
    max_weight = round(df_filtered['record'].max(), 2)
    min_weight = round(df_filtered['record'].min(), 2)

    # Daily fluctuations
    daily_fluctuations = df_filtered.resample('D')['record'].agg(['min', 'max', 'mean'])
    daily_fluctuations['fluctuation_range'] = daily_fluctuations['max'] - daily_fluctuations['min']
    significant_fluctuations = daily_fluctuations[daily_fluctuations['fluctuation_range'] > 1]

    # Hourly patterns
    hourly_trend = df_filtered.resample('h')['record'].mean()
    daytime_weight = hourly_trend.between_time("06:00", "17:59")
    nighttime_weight = hourly_trend.between_time("18:00", "05:59")
    day_mean_weight = round(daytime_weight.mean(), 2)
    night_mean_weight = round(nighttime_weight.mean(), 2)

    return {
        "Year": year,
        "Month": month,
        "Statistics": {
            "Maximum Weight": max_weight,
            "Minimum Weight": min_weight,
            "Mean Weight": mean_weight
        },
        "Daily Weight Fluctuations": significant_fluctuations.to_dict(orient='records'),
        "Hourly Patterns": {
            "Daytime Mean Weight": day_mean_weight,
            "Nighttime Mean Weight": night_mean_weight
        }
    }

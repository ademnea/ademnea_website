#!/usr/bin/env python3

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import os
from datetime import date

def detect_anomalies(df_filtered):
    """Detect anomalies in weight data using z-score method."""
    if df_filtered.empty:
        return []
    
    # Calculate z-scores for weight data
    mean = df_filtered['record'].mean()
    std = df_filtered['record'].std()
    if std == 0 or np.isnan(std):
        return []
    
    # Identify anomalies where |z-score| > 2
    df_filtered = df_filtered.assign(z_score=(df_filtered['record'] - mean) / std)
    anomalies = df_filtered[pd.notna(df_filtered['z_score']) & (np.abs(df_filtered['z_score']) > 2)]
    
    # Convert to list of dictionaries with relevant columns
    anomaly_list = []
    for idx, row in anomalies.iterrows():
        anomaly_list.append({
            'record': row['record'],
            'timestamp': idx
        })
    
    return anomaly_list

def analyze_weight_for_month_year(df, year, month, report_folder):
    df.index = pd.to_datetime(df.index)
    df['year'] = df.index.year
    df['month'] = df.index.month
    df_filtered = df[(df['year'] == year) & (df['month'] == month)]
    
    if df_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}
    
    month_name = date(year, month, 1).strftime('%B')
    
    # Plot monthly trend
    plt.figure(figsize=(12, 6))
    plt.plot(df_filtered.index, df_filtered['record'], label='Hive Weight', color='green', linestyle='-', marker='o', markersize=4)
    plt.title(f"Weight Trend for {month_name} {year}", fontsize=16, weight='bold')
    plt.xlabel("Date", fontsize=12)
    plt.ylabel("Weight (kg)", fontsize=12)
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.xticks(rotation=45, ha='right', fontsize=10)
    plt.yticks(fontsize=10)
    plt.legend(loc='upper right', fontsize=10)
    plt.tight_layout()
    plt.savefig(os.path.join(report_folder, "weight_monthly_trend.png"), dpi=300)
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
    day_mean_weight = round(daytime_weight.mean(), 2) if not daytime_weight.empty else None
    night_mean_weight = round(nighttime_weight.mean(), 2) if not nighttime_weight.empty else None
    
    # Detect anomalies
    anomalies = detect_anomalies(df_filtered)
    
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
        },
        "Anomalies": anomalies
    }
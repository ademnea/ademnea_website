#!/usr/bin/env python3

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import os
from scipy.stats import pearsonr
from datetime import date

def detect_anomalies(df_filtered):
    """Detect anomalies in humidity data using z-score method."""
    if df_filtered.empty:
        return []
    
    # Initialize anomalies list
    anomalies = []
    
    # Calculate z-scores for both interior and exterior humidity
    result = df_filtered.copy()
    for col in ['Interior (%)', 'Exterior (%)']:
        mean = df_filtered[col].mean()
        std = df_filtered[col].std()
        if std == 0 or np.isnan(std):
            continue
        result[f'z_score_{col}'] = (df_filtered[col] - mean) / std
    
    # Identify rows where either interior or exterior humidity is an anomaly (|z-score| > 2)
    anomaly_rows = result[(result['z_score_Interior (%)'].abs() > 2) | (result['z_score_Exterior (%)'].abs() > 2)]
    
    # Convert to list of dictionaries with relevant columns
    for idx, row in anomaly_rows.iterrows():
        anomalies.append({
            'Interior (%)': row['Interior (%)'],
            'Exterior (%)': row['Exterior (%)'],
            'timestamp': idx
        })
    
    return anomalies

def analyze_humidity_for_month_year(df_humidity, df_weight, df_temperature, year, month, report_folder):
    df_humidity.index = pd.to_datetime(df_humidity.index)
    df_humidity['year'] = df_humidity.index.year
    df_humidity['month'] = df_humidity.index.month
    df_humidity_filtered = df_humidity[(df_humidity['year'] == year) & (df_humidity['month'] == month)]
    
    if df_humidity_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}
    
    month_name = date(year, month, 1).strftime('%B')
    
    # Plot humidity trends
    plt.figure(figsize=(12, 6))
    plt.plot(df_humidity_filtered.index, df_humidity_filtered['Interior (%)'], label='Interior Humidity', color='orange', linestyle='-', marker='o', markersize=4)
    plt.plot(df_humidity_filtered.index, df_humidity_filtered['Exterior (%)'], label='Exterior Humidity', color='blue', linestyle='-', marker='o', markersize=4)
    plt.title(f"Humidity Trend for {month_name} {year}", fontsize=16, weight='bold')
    plt.xlabel("Date", fontsize=12)
    plt.ylabel("Humidity (%)", fontsize=12)
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.xticks(rotation=45, ha='right', fontsize=10)
    plt.yticks(fontsize=10)
    plt.legend(loc='upper right', fontsize=10)
    plt.tight_layout()
    plt.savefig(os.path.join(report_folder, "humidity_trend.png"), dpi=300)
    plt.close()
    
    # Calculate statistics
    interior_avg = round(df_humidity_filtered['Interior (%)'].mean(), 2)
    exterior_avg = round(df_humidity_filtered['Exterior (%)'].mean(), 2)
    interior_min = round(df_humidity_filtered['Interior (%)'].min(), 2)
    exterior_min = round(df_humidity_filtered['Exterior (%)'].min(), 2)
    interior_max = round(df_humidity_filtered['Interior (%)'].max(), 2)
    exterior_max = round(df_humidity_filtered['Exterior (%)'].max(), 2)
    interior_std = round(df_humidity_filtered['Interior (%)'].std(), 2)
    exterior_std = round(df_humidity_filtered['Exterior (%)'].std(), 2)
    interior_range = interior_max - interior_min
    exterior_range = exterior_max - exterior_min
    
    # Detect anomalies
    anomalies = detect_anomalies(df_humidity_filtered)
    
    return {
        "Year": year,
        "Month": month,
        "Humidity Insights": {
            "Interior Humidity (%)": {
                "Average": interior_avg,
                "Min": interior_min,
                "Max": interior_max,
                "Standard Deviation": interior_std,
                "Range": interior_range
            },
            "Exterior Humidity (%)": {
                "Average": exterior_avg,
                "Min": exterior_min,
                "Max": exterior_max,
                "Standard Deviation": exterior_std,
                "Range": exterior_range
            }
        },
        "Anomalies": anomalies
    }
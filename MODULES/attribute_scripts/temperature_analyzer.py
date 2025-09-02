#!/usr/bin/env python3

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import os
from datetime import date

def detect_anomalies(df_filtered):
    """Detect anomalies in temperature data using z-score method."""
    if df_filtered.empty:
        return []
    
    # Initialize anomalies list
    anomalies = []
    
    # Calculate z-scores for both interior and exterior temperature
    result = df_filtered.copy()
    for col in ['Interior (°C)', 'Exterior (°C)']:
        mean = df_filtered[col].mean()
        std = df_filtered[col].std()
        if std == 0 or np.isnan(std):
            continue
        result[f'z_score_{col}'] = (df_filtered[col] - mean) / std
    
    # Identify rows where either interior or exterior temperature is an anomaly (|z-score| > 2)
    anomaly_rows = result[(result['z_score_Interior (°C)'].abs() > 2) | (result['z_score_Exterior (°C)'].abs() > 2)]
    
    # Convert to list of dictionaries with relevant columns
    for idx, row in anomaly_rows.iterrows():
        anomalies.append({
            'Interior (°C)': row['Interior (°C)'],
            'Exterior (°C)': row['Exterior (°C)'],
            'timestamp': idx
        })
    
    return anomalies

def analyze_temperature_for_month_year(df, year, month, report_folder):
    df.index = pd.to_datetime(df.index)
    df['year'] = df.index.year
    df['month'] = df.index.month
    df_filtered = df[(df['year'] == year) & (df['month'] == month)]
    
    if df_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}
    
    month_name = date(year, month, 1).strftime('%B')
    
    # Plot temperature trends
    plt.figure(figsize=(12, 6))
    plt.plot(df_filtered.index, df_filtered['Interior (°C)'], label='Interior Temperature', color='red', linestyle='-', marker='o', markersize=4)
    plt.plot(df_filtered.index, df_filtered['Exterior (°C)'], label='Exterior Temperature', color='blue', linestyle='-', marker='o', markersize=4)
    plt.title(f"Temperature Trend for {month_name} {year}", fontsize=16, weight='bold')
    plt.xlabel("Date", fontsize=12)
    plt.ylabel("Temperature (°C)", fontsize=12)
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.xticks(rotation=45, ha='right', fontsize=10)
    plt.yticks(fontsize=10)
    plt.legend(loc='upper right', fontsize=10)
    plt.tight_layout()
    plt.savefig(os.path.join(report_folder, "temperature_trend.png"), dpi=300)
    plt.close()
    
    # Calculate statistics
    exterior_lowest = round(df_filtered['Exterior (°C)'].min(), 2)
    exterior_highest = round(df_filtered['Exterior (°C)'].max(), 2)
    exterior_avg = round(df_filtered['Exterior (°C)'].mean(), 2)
    interior_lowest = round(df_filtered['Interior (°C)'].min(), 2)
    interior_highest = round(df_filtered['Interior (°C)'].max(), 2)
    interior_avg = round(df_filtered['Interior (°C)'].mean(), 2)
    
    # Calculate standard deviation
    exterior_std = round(df_filtered['Exterior (°C)'].std(), 2)
    interior_std = round(df_filtered['Interior (°C)'].std(), 2)
    
    # Detect anomalies
    anomalies = detect_anomalies(df_filtered)
    
    return {
        "Year": year,
        "Month": month,
        "Temperature Statistics": {
            "Exterior": {
                "Lowest": exterior_lowest,
                "Highest": exterior_highest,
                "Average": exterior_avg
            },
            "Interior": {
                "Lowest": interior_lowest,
                "Highest": interior_highest,
                "Average": interior_avg
            }
        },
        "Standard Deviation": {
            "Exterior (°C)": exterior_std,
            "Interior (°C)": interior_std
        },
        "Anomalies": anomalies
    }
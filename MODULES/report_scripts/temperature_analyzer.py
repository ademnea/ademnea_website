#!/usr/bin/env python3

import pandas as pd
import matplotlib.pyplot as plt
import os
from scipy.stats import pearsonr

def analyze_temperature_for_month_year(df, year, month, report_folder):
    df.index = pd.to_datetime(df.index)
    df['year'] = df.index.year
    df['month'] = df.index.month
    df_filtered = df[(df['year'] == year) & (df['month'] == month)]
    
    if df_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}
    
    # Plot temperature trends and save to report folder
    plt.figure(figsize=(12, 6))
    df_filtered[['Interior (°C)', 'Exterior (°C)']].plot(title=f"Temperature Trend for {year}-{month:02d}", xlabel="Date", ylabel="Temperature (°C)", color=['r', 'b'], linestyle='-', marker='o', markersize=4)
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.xticks(rotation=45, ha='right')
    plt.tight_layout()
    plt.savefig(os.path.join(report_folder, "temperature_trend.png"))
    plt.close()  # Close figure to free memory
    
    # Calculate statistics
    avg_ext_temp = round(df_filtered['Exterior (°C)'].mean(), 1)
    max_ext_temp = round(df_filtered['Exterior (°C)'].max(), 1)
    min_ext_temp = round(df_filtered['Exterior (°C)'].min(), 1)
    avg_int_temp = round(df_filtered['Interior (°C)'].mean(), 1)
    max_int_temp = round(df_filtered['Interior (°C)'].max(), 1)
    min_int_temp = round(df_filtered['Interior (°C)'].min(), 1)
    
    # Standard deviation
    ext_temp_std = round(df_filtered['Exterior (°C)'].std(), 1)
    int_temp_std = round(df_filtered['Interior (°C)'].std(), 1)
    
    # Correlation analysis
    corr, p_value = pearsonr(df_filtered['Interior (°C)'], df_filtered['Exterior (°C)'])
    
    return {
        "Year": year,
        "Month": month,
        "Temperature Statistics": {
            "Exterior": {"Lowest": min_ext_temp, "Highest": max_ext_temp, "Average": avg_ext_temp},
            "Interior": {"Lowest": min_int_temp, "Highest": max_int_temp, "Average": avg_int_temp}
        },
        "Standard Deviation": {
            "Exterior": ext_temp_std,
            "Interior": int_temp_std
        },
        "Correlation": {
            "Coefficient (r)": round(corr, 2),
            "p-value": round(p_value, 3)
        }
    }
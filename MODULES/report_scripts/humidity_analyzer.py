#!/usr/bin/env python3

import pandas as pd
import matplotlib.pyplot as plt
import os
from scipy.stats import pearsonr

def analyze_humidity_for_month_year(df_humidity, df_weight, df_temperature, year, month, report_folder):
    df_humidity.index = pd.to_datetime(df_humidity.index)
    df_humidity['year'] = df_humidity.index.year
    df_humidity['month'] = df_humidity.index.month
    df_humidity_filtered = df_humidity[(df_humidity['year'] == year) & (df_humidity['month'] == month)]
    
    if df_humidity_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}
    
    # Plot humidity trends and save to report folder
    plt.figure(figsize=(10, 6))
    df_humidity_filtered[['Interior (%)', 'Exterior (%)']].plot(title=f"Humidity Trend for {year}-{month:02d}", xlabel="Date", ylabel="Humidity (%)")
    plt.grid(True)
    plt.savefig(os.path.join(report_folder, "humidity_trend.png"))
    plt.close()  # Close figure to free memory
    
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
    
    # Correlation analysis
    df_combined = df_humidity_filtered.join(df_weight['record'], rsuffix='_weight').join(df_temperature[['Interior (°C)', 'Exterior (°C)']])
    temp_corr_int = pearsonr(df_combined['Interior (%)'], df_combined['Interior (°C)'])[0]
    temp_corr_ext = pearsonr(df_combined['Exterior (%)'], df_combined['Exterior (°C)'])[0]
    weight_corr_int = pearsonr(df_combined['Interior (%)'], df_combined['record'])[0]
    weight_corr_ext = pearsonr(df_combined['Exterior (%)'], df_combined['record'])[0]
    
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
        "Correlation Analysis": {
            "Interior Humidity vs Interior Temperature (°C)": temp_corr_int,
            "Exterior Humidity vs Exterior Temperature (°C)": temp_corr_ext,
            "Interior Humidity vs Hive Weight (kg)": weight_corr_int,
            "Exterior Humidity vs Hive Weight (kg)": weight_corr_ext
        }
    }
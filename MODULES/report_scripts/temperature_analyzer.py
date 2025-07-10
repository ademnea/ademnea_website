#!/usr/bin/env python3


import pandas as pd
import matplotlib.pyplot as plt
from scipy.stats import pearsonr

def analyze_temperature_for_month_year(df, year, month, output_dir=None):
    import os
    df.index = pd.to_datetime(df.index)
    df['year'] = df.index.year
    df['month'] = df.index.month
    df_filtered = df[(df['year'] == year) & (df['month'] == month)]
    
    if df_filtered.empty:
        return {
            "Year": year,
            "Month": month,
            "Error": f"No data available for {year}-{month:02d}",
            "has_plot": False
        }

    if output_dir is None:
        output_dir = "."

    # Plot temperature trends
    plt.figure(figsize=(12, 6))
    df_filtered[['Interior (°C)', 'Exterior (°C)']].plot(
        title=f"Temperature Trend for {year}-{month:02d}",
        xlabel="Date",
        ylabel="Temperature (°C)",
        color=['r', 'b'],
        linestyle='-',
        marker='o',
        markersize=4
    )
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.xticks(rotation=45, ha='right')
    plt.tight_layout()
    plot_path = os.path.join(output_dir, "temperature_trend.png")
    plt.savefig(plot_path)
    plt.close()

    return {
        "Year": year,
        "Month": month,
        "Temperature Statistics": {
            "Exterior": {"Lowest": round(df_filtered['Exterior (°C)'].min(), 1), "Highest": round(df_filtered['Exterior (°C)'].max(), 1), "Average": round(df_filtered['Exterior (°C)'].mean(), 1)},
            "Interior": {"Lowest": round(df_filtered['Interior (°C)'].min(), 1), "Highest": round(df_filtered['Interior (°C)'].max(), 1), "Average": round(df_filtered['Interior (°C)'].mean(), 1)},
        },
        "Standard Deviation": {
            "Exterior": round(df_filtered['Exterior (°C)'].std(), 1),
            "Interior": round(df_filtered['Interior (°C)'].std(), 1)
        },
        "Correlation": {
            "Coefficient (r)": round(pearsonr(df_filtered['Interior (°C)'], df_filtered['Exterior (°C)'])[0], 2),
            "p-value": round(pearsonr(df_filtered['Interior (°C)'], df_filtered['Exterior (°C)'])[1], 3)
        },
        "has_plot": True
    }

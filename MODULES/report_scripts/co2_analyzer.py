#!/usr/bin/env python3


import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns

def prepare_data(df):
    df.index = pd.to_datetime(df.index)
    df['year'] = df.index.year
    df['month'] = df.index.month
    return df

def filter_data_by_month_year(df, year, month):
    return df[(df['year'] == year) & (df['month'] == month)]

def calculate_basic_statistics(df_filtered):
    mean_co2 = round(df_filtered['record'].mean(), 2)
    median_co2 = round(df_filtered['record'].median(), 2)
    std_co2 = round(df_filtered['record'].std(), 2)
    min_co2 = round(df_filtered['record'].min(), 2)
    max_co2 = round(df_filtered['record'].max(), 2)
    return {
        "Mean CO2": mean_co2,
        "Median CO2": median_co2,
        "Standard Deviation": std_co2,
        "Min CO2": min_co2,
        "Max CO2": max_co2
    }

def calculate_diurnal_variation(df_filtered):
    day_records = df_filtered.between_time("07:00", "18:59")
    night_records = df_filtered.between_time("19:00", "06:59")
    day_mean = round(day_records['record'].mean(), 2) if not day_records.empty else None
    night_mean = round(night_records['record'].mean(), 2) if not night_records.empty else None
    day_variability = round(day_records['record'].std(), 2) if not day_records.empty else None
    night_variability = round(night_records['record'].std(), 2) if not night_records.empty else None
    return {
        "Daytime Mean CO2": day_mean,
        "Nighttime Mean CO2": night_mean,
        "Daytime Variability": day_variability,
        "Nighttime Variability": night_variability
    }

def calculate_weekly_trend(df_filtered):
    return df_filtered.resample('W')['record'].mean().round(2).to_dict()

def calculate_daily_trend(df_filtered):
    return df_filtered.resample('D')['record'].mean().round(2).to_dict()

def detect_anomalies(df_filtered, mean_co2, std_co2):
    df_filtered = df_filtered.assign(z_score=(df_filtered['record'] - mean_co2) / std_co2)
    anomalies = df_filtered[pd.notna(df_filtered['z_score']) & (np.abs(df_filtered['z_score']) > 2)]
    return anomalies[['record']].to_dict(orient='records')

def format_results(year, month, statistics, diurnal_variation, weekly_trend, daily_trend, anomalies):
    return {
        "Year": year,
        "Month": month,
        "Statistics": statistics,
        "Diurnal Variations": diurnal_variation,
        "Trends": {
            "Weekly Trend": weekly_trend,
            "Daily Trend": daily_trend
        },
        "Anomalies": anomalies
    }

def plot_monthly_trend(df_filtered, filename):
    plt.figure(figsize=(12, 6))
    plt.plot(df_filtered.index, df_filtered['record'], color='b', linestyle='-', marker='o', markersize=4)
    plt.title("CO2 Levels Over the Month", fontsize=16, weight='bold')
    plt.xlabel("Date", fontsize=12)
    plt.ylabel("CO2 (ppm)", fontsize=12)
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.xticks(rotation=45, ha='right')
    plt.tight_layout()
    plt.savefig(filename)  # Save the plot to a file
    plt.show()  # Display the plot in the notebook

def plot_diurnal_variation(df_filtered, filename):
    df_filtered = df_filtered.assign(period=['Day' if 7 <= x.hour < 19 else 'Night' for x in df_filtered.index])
    plt.figure(figsize=(8, 6))
    sns.boxplot(x='period', y='record', data=df_filtered, palette="coolwarm")
    plt.title("Diurnal Variation of CO2 Levels", fontsize=16, weight='bold')
    plt.xlabel("Period", fontsize=12)
    plt.ylabel("CO2 Levels (ppm)", fontsize=12)
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.savefig(filename)  # Save the plot to a file
    plt.show()  # Display the plot in the notebook

def analyze_co2_pipeline(df, year, month):
    df = prepare_data(df)
    df_filtered = filter_data_by_month_year(df, year, month)
    if df_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}
    
    statistics = calculate_basic_statistics(df_filtered)
    diurnal_variation = calculate_diurnal_variation(df_filtered)
    weekly_trend = calculate_weekly_trend(df_filtered)
    daily_trend = calculate_daily_trend(df_filtered)
    anomalies = detect_anomalies(df_filtered, statistics["Mean CO2"], statistics["Standard Deviation"])
    
    # Save and display plots
    plot_monthly_trend(df_filtered, "co2_monthly_trend.png")
    plot_diurnal_variation(df_filtered, "co2_diurnal_variation.png")
    
    return format_results(year, month, statistics, diurnal_variation, weekly_trend, daily_trend, anomalies)
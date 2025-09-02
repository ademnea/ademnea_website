#!/usr/bin/env python3

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import os
from datetime import date

def load_co2_data(file_path):
    """Load CO2 data from CSV file."""
    try:
        df = pd.read_csv(file_path)
        df['timestamp'] = pd.to_datetime(df['timestamp'])
        df.set_index('timestamp', inplace=True)
        return df
    except Exception as e:
        print(f"Error loading CO2 data: {e}")
        return None

def clean_co2_data(df):
    """Clean CO2 data by removing invalid entries and handling missing values."""
    df = df.copy()
    df = df[df['record'] >= 0]
    df = df.dropna()
    return df

def calculate_statistics(df_filtered):
    """Calculate basic statistics for CO2 data."""
    if df_filtered.empty:
        return {
            "Mean CO2": None,
            "Median CO2": None,
            "Standard Deviation": None,
            "Min CO2": None,
            "Max CO2": None
        }
    
    return {
        "Mean CO2": round(df_filtered['record'].mean(), 2),
        "Median CO2": round(df_filtered['record'].median(), 2),
        "Standard Deviation": round(df_filtered['record'].std(), 2),
        "Min CO2": round(df_filtered['record'].min(), 2),
        "Max CO2": round(df_filtered['record'].max(), 2)
    }

def calculate_diurnal_variations(df_filtered):
    """Calculate diurnal variations in CO2 levels."""
    if df_filtered.empty:
        return {
            "Daytime CO2 (6AM-6PM)": None,
            "Nighttime CO2 (6PM-6AM)": None
        }
    
    df_filtered['hour'] = df_filtered.index.hour
    daytime_co2 = df_filtered[df_filtered['hour'].between(6, 17)]['record'].mean()
    nighttime_co2 = df_filtered[~df_filtered['hour'].between(6, 17)]['record'].mean()
    
    return {
        "Daytime CO2 (6AM-6PM)": round(daytime_co2, 2) if not pd.isna(daytime_co2) else None,
        "Nighttime CO2 (6PM-6AM)": round(nighttime_co2, 2) if not pd.isna(nighttime_co2) else None
    }

def detect_anomalies(df_filtered, mean_co2, std_co2):
    """Detect anomalies in CO2 data using z-score method."""
    if df_filtered.empty or std_co2 == 0 or pd.isna(std_co2):
        return []
    
    # Calculate z-scores
    df_filtered = df_filtered.assign(z_score=(df_filtered['record'] - mean_co2) / std_co2)
    
    # Identify anomalies (|z-score| > 2)
    anomalies = df_filtered[pd.notna(df_filtered['z_score']) & (df_filtered['z_score'].abs() > 2)]
    
    # Return anomalies with their timestamps
    return anomalies[['record']].assign(timestamp=anomalies.index).to_dict(orient='records')

def plot_co2_trends(df_filtered, year, month, report_folder):
    """Generate plots for CO2 trends."""
    month_name = date(year, month, 1).strftime('%B')
    
    # Monthly trend plot
    plt.figure(figsize=(12, 6))
    plt.plot(df_filtered.index, df_filtered['record'], label='CO2 Level', color='red', linestyle='-', marker='o', markersize=4)
    plt.title(f"CO2 Trend for {month_name} {year}", fontsize=16, weight='bold')
    plt.xlabel("Date", fontsize=12)
    plt.ylabel("CO2 (ppm)", fontsize=12)
    plt.grid(True, linestyle='--', alpha=0.7)
    plt.xticks(rotation=45, ha='right', fontsize=10)
    plt.yticks(fontsize=10)
    plt.legend(loc='upper right', fontsize=10)
    plt.tight_layout()
    plt.savefig(os.path.join(report_folder, "co2_monthly_trend.png"), dpi=300)
    plt.close()
    
    # Diurnal variation plot
    if not df_filtered.empty:
        df_filtered['hour'] = df_filtered.index.hour
        hourly_avg = df_filtered.groupby('hour')['record'].mean()
        plt.figure(figsize=(12, 6))
        plt.plot(hourly_avg.index, hourly_avg, label='Hourly Average CO2', color='purple', linestyle='-', marker='o', markersize=4)
        plt.title(f"Diurnal CO2 Variation for {month_name} {year}", fontsize=16, weight='bold')
        plt.xlabel("Hour of Day", fontsize=12)
        plt.ylabel("Average CO2 (ppm)", fontsize=12)
        plt.grid(True, linestyle='--', alpha=0.7)
        plt.xticks(range(24), fontsize=10)
        plt.yticks(fontsize=10)
        plt.legend(loc='upper right', fontsize=10)
        plt.tight_layout()
        plt.savefig(os.path.join(report_folder, "co2_diurnal_variation.png"), dpi=300)
        plt.close()

def calculate_trends(df_filtered):
    """Calculate weekly and daily trends for CO2 data."""
    if df_filtered.empty:
        return {"Weekly Trend": {}, "Daily Trend": {}}
    
    # Weekly trend
    weekly_avg = df_filtered.resample('W')['record'].mean().to_dict()
    weekly_trend = {str(k): round(v, 2) for k, v in weekly_avg.items() if not pd.isna(v)}
    
    # Daily trend
    daily_avg = df_filtered.resample('D')['record'].mean().to_dict()
    daily_trend = {str(k): round(v, 2) for k, v in daily_avg.items() if not pd.isna(v)}
    
    return {
        "Weekly Trend": weekly_trend,
        "Daily Trend": daily_trend
    }

def analyze_co2_pipeline(df, year, month, report_folder):
    """Analyze CO2 data for a specific month and year."""
    df.index = pd.to_datetime(df.index)
    df['year'] = df.index.year
    df['month'] = df.index.month
    df_filtered = df[(df['year'] == year) & (df['month'] == month)]
    
    if df_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}
    
    # Calculate statistics
    statistics = calculate_statistics(df_filtered)
    
    # Calculate diurnal variations
    diurnal_variations = calculate_diurnal_variations(df_filtered)
    
    # Detect anomalies
    anomalies = detect_anomalies(df_filtered, statistics.get("Mean CO2", 0), statistics.get("Standard Deviation", 1))
    
    # Calculate trends
    trends = calculate_trends(df_filtered)
    
    # Generate plots
    plot_co2_trends(df_filtered, year, month, report_folder)
    
    return {
        "Year": year,
        "Month": month,
        "Statistics": statistics,
        "Diurnal Variations": diurnal_variations,
        "Anomalies": anomalies,
        "Trends": trends
    }
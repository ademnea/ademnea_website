#!/usr/bin/env python3

import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import os
from scipy.stats import pearsonr, spearmanr
from datetime import date  # Added for month name conversion

def analyze_correlations(df_humidity, df_temperature, df_weight, df_co2, year, month, report_folder):
    month_name = date(year, month, 1).strftime('%B')  # Convert month number to name
    df_humidity_filtered = df_humidity[(df_humidity.index.year == year) & (df_humidity.index.month == month)]
    df_temperature_filtered = df_temperature[(df_temperature.index.year == year) & (df_temperature.index.month == month)]
    df_weight_filtered = df_weight[(df_weight.index.year == year) & (df_weight.index.month == month)]
    df_co2_filtered = df_co2[(df_co2.index.year == year) & (df_co2.index.month == month)]
    
    if df_humidity_filtered.empty or df_temperature_filtered.empty or df_weight_filtered.empty or df_co2_filtered.empty:
        return {"Year": year, "Month": month_name, "Error": f"No data available for {year}-{month_name}"}
    
    # Rename columns
    df_co2_filtered = df_co2_filtered.rename(columns={'record': 'co2_record'})
    df_weight_filtered = df_weight_filtered.rename(columns={'record': 'weight_record'})
    
    # Combine dataframes
    df_combined = pd.concat([
        df_humidity_filtered[['Interior (%)', 'Exterior (%)']],
        df_temperature_filtered[['Interior (°C)', 'Exterior (°C)']],
        df_weight_filtered[['weight_record']],
        df_co2_filtered[['co2_record']]
    ], axis=1)
    
    # Drop rows with NaN values to ensure valid correlations and plots
    df_combined = df_combined.dropna()
    
    if df_combined.empty:
        return {"Year": year, "Month": month_name, "Error": f"No valid data after cleaning for {year}-{month_name}"}
    
    # Correlation analysis
    correlation_results = {}
    correlation_results['CO2 vs Weight'] = perform_correlation(df_combined['co2_record'], df_combined['weight_record'])
    correlation_results['Temperature vs Weight'] = perform_correlation(df_combined['Interior (°C)'], df_combined['weight_record'])
    correlation_results['Humidity vs Weight'] = perform_correlation(df_combined['Interior (%)'], df_combined['weight_record'])
    
    # Plot trends and heatmap, saving to report folder
    plot_trends(df_combined, report_folder, year, month_name)
    plot_correlation_heatmap(df_combined, report_folder, year, month_name)
    
    return format_correlation_results(correlation_results)

def perform_correlation(x, y):
    try:
        pearson_val = pearsonr(x, y)[0]
        spearman_val = spearmanr(x, y)[0]
        return {'Pearson': pearson_val, 'Spearman': spearman_val}
    except Exception as e:
        return {'Error': str(e)}

def plot_trends(df_combined, report_folder, year, month_name):
    """Plot and save CO2/temperature/weight and humidity/weight trends in the report folder."""
    # First plot: CO2, Temperature, and Weight
    fig1, ax1 = plt.subplots(figsize=(10, 7))
    ax1.plot(df_combined.index, df_combined['co2_record'], label='CO2 Record', color='blue')
    ax1.set_xlabel('Date')
    ax1.set_ylabel('CO2 (ppm)', color='blue')
    ax1.tick_params(axis='y', labelcolor='blue')
    
    ax2 = ax1.twinx()
    ax2.plot(df_combined.index, df_combined['Interior (°C)'], label='Interior Temperature (°C)', color='red')
    ax2.set_ylabel('Temperature (°C)', color='red')
    ax2.tick_params(axis='y', labelcolor='red')
    
    ax3 = ax1.twinx()
    ax3.spines['right'].set_position(('outward', 60))  # Offset second y-axis
    ax3.plot(df_combined.index, df_combined['weight_record'], label='Weight Record', color='green')
    ax3.set_ylabel('Weight (kg)', color='green')
    ax3.tick_params(axis='y', labelcolor='green')
    
    ax1.set_title(f'CO2, Temperature, and Weight Trends for {year}-{month_name}')
    ax1.legend(loc='upper left')
    ax2.legend(loc='upper right')
    ax3.legend(loc='lower right')
    fig1.tight_layout()
    fig1.savefig(os.path.join(report_folder, "co2_temp_weight_trends.png"))
    plt.close(fig1)  # Close figure to free memory
    
    # Second plot: Humidity and Weight
    fig2, ax3 = plt.subplots(figsize=(10, 7))
    ax3.plot(df_combined.index, df_combined['Interior (%)'], label='Interior Humidity (%)', color='orange')
    ax3.set_xlabel('Date')
    ax3.set_ylabel('Humidity (%)', color='orange')
    ax3.tick_params(axis='y', labelcolor='orange')
    
    ax4 = ax3.twinx()
    ax4.plot(df_combined.index, df_combined['weight_record'], label='Weight Record', color='green')
    ax4.set_ylabel('Weight (kg)', color='green')
    ax4.tick_params(axis='y', labelcolor='green')
    
    ax3.set_title(f'Humidity and Weight Trends for {year}-{month_name}')
    ax3.legend(loc='upper left')
    ax4.legend(loc='upper right')
    fig2.tight_layout()
    fig2.savefig(os.path.join(report_folder, "humidity_weight_trends.png"))
    plt.close(fig2)  # Close figure to free memory

def plot_correlation_heatmap(df_combined, report_folder, year, month_name):
    """Plot and save the correlation heatmap in the report folder."""
    corr_matrix = df_combined.corr()
    plt.figure(figsize=(10, 8))
    sns.heatmap(corr_matrix, annot=True, fmt='.2f', cmap='coolwarm', vmin=-1, vmax=1, annot_kws={"size": 10})
    plt.title(f'Correlation Heatmap for {year}-{month_name}')
    plt.savefig(os.path.join(report_folder, "correlation_heatmap.png"))
    plt.close()  # Close figure to free memory

def format_correlation_results(correlation_results):
    formatted_results = {}
    for key, value in correlation_results.items():
        if 'Error' in value:
            formatted_results[key] = f"Error: {value['Error']}"
        else:
            formatted_results[key] = f"Pearson: {value['Pearson']:.2f}, Spearman: {value['Spearman']:.2f}"
    return formatted_results
#!/usr/bin/env python3


import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from scipy.stats import pearsonr, spearmanr

def analyze_correlations(df_humidity, df_temperature, df_weight, df_co2, year, month):
    df_humidity_filtered = df_humidity[(df_humidity.index.year == year) & (df_humidity.index.month == month)]
    df_temperature_filtered = df_temperature[(df_temperature.index.year == year) & (df_temperature.index.month == month)]
    df_weight_filtered = df_weight[(df_weight.index.year == year) & (df_weight.index.month == month)]
    df_co2_filtered = df_co2[(df_co2.index.year == year) & (df_co2.index.month == month)]
    
    if df_humidity_filtered.empty or df_temperature_filtered.empty or df_weight_filtered.empty or df_co2_filtered.empty:
        return {"Year": year, "Month": month, "Error": f"No data available for {year}-{month:02d}"}
    
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
    
    # Correlation analysis
    correlation_results = {}
    correlation_results['CO₂ vs Weight'] = perform_correlation(df_combined['co2_record'], df_combined['weight_record'])
    correlation_results['Temperature vs Weight'] = perform_correlation(df_combined['Interior (°C)'], df_combined['weight_record'])
    correlation_results['Humidity vs Weight'] = perform_correlation(df_combined['Interior (%)'], df_combined['weight_record'])
    
    # Plot trends
    plot_trends(df_combined)
    
    # Plot correlation heatmap
    plot_correlation_heatmap(df_combined)
    
    return format_correlation_results(correlation_results)

def perform_correlation(x, y):
    try:
        pearson_val = pearsonr(x, y)[0]
        spearman_val = spearmanr(x, y)[0]
        return {'Pearson': pearson_val, 'Spearman': spearman_val}
    except Exception as e:
        return {'Error': str(e)}

def plot_trends(df_combined):
    fig, ax1 = plt.subplots(figsize=(10, 7))
    ax1.plot(df_combined.index, df_combined['co2_record'], label='CO₂ Record', color='blue')
    ax1.set_xlabel('Date')
    ax1.set_ylabel('CO₂ Record', color='blue')
    ax1.tick_params(axis='y', labelcolor='blue')
    
    ax2 = ax1.twinx()
    ax2.plot(df_combined.index, df_combined['Interior (°C)'], label='Interior Temperature (°C)', color='red')
    ax2.set_ylabel('Temperature (°C)', color='red')
    ax2.tick_params(axis='y', labelcolor='red')
    
    ax1.plot(df_combined.index, df_combined['weight_record'], label='Weight Record', color='green')
    ax1.set_title('CO₂, Temperature and Weight Trends')
    ax1.legend(loc='upper left')
    ax2.legend(loc='upper right')
    
    fig2, ax3 = plt.subplots(figsize=(10, 7))
    ax3.plot(df_combined.index, df_combined['Interior (%)'], label='Interior Humidity (%)', color='orange')
    ax3.set_xlabel('Date')
    ax3.set_ylabel('Humidity (%)', color='orange')
    ax3.tick_params(axis='y', labelcolor='orange')
    ax3.plot(df_combined.index, df_combined['weight_record'], label='Weight Record', color='green')
    ax3.set_title('Humidity and Weight Trends')
    ax3.legend()
    
    plt.tight_layout()
    plt.savefig("combined_trends.png")  # Save the plot to a file
    plt.show()  # Display the plot in the notebook

def plot_correlation_heatmap(df_combined):
    corr_matrix = df_combined.corr()
    plt.figure(figsize=(10, 8))
    sns.heatmap(corr_matrix, annot=True, cmap='coolwarm', vmin=-1, vmax=1)
    plt.title('Correlation Heatmap')
    plt.savefig("correlation_heatmap.png")  # Save the plot to a file
    plt.show()  # Display the plot in the notebook

def format_correlation_results(correlation_results):
    formatted_results = {}
    for key, value in correlation_results.items():
        if 'Error' in value:
            formatted_results[key] = f"Error: {value['Error']}"
        else:
            formatted_results[key] = f"Pearson: {value['Pearson']:.2f}, Spearman: {value['Spearman']:.2f}"
    return formatted_results
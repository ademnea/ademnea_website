
#!/usr/bin/env python3

import sys
import pandas as pd
import numpy as np
from data_loader import load_data
from data_cleaner import clean_humidity_data, clean_temperature_data, clean_numeric_column, remove_nan_values
from co2_analyzer import analyze_co2_pipeline
from weight_analyzer import analyze_weight_for_month_year
from temperature_analyzer import analyze_temperature_for_month_year
from humidity_analyzer import analyze_humidity_for_month_year
from correlation_analyzer import analyze_correlations
from pdf_generator import generate_pdf

def main(co2_file, weight_file, temp_file, humidity_file, year, month, hive_id, output_dir):
    # Load data
    carbondioxide = load_data(co2_file)
    humidity = load_data(humidity_file)
    temperatures = load_data(temp_file)
    weights = load_data(weight_file)

    # Clean data
    humidity = clean_humidity_data(humidity)
    temperatures = clean_temperature_data(temperatures)
    carbondioxide = clean_numeric_column(carbondioxide)
    weights = clean_numeric_column(weights)

    # Remove NaN values
    carbondioxide = remove_nan_values(carbondioxide)
    humidity = remove_nan_values(humidity)
    temperatures = remove_nan_values(temperatures)
    weights = remove_nan_values(weights)

    # Execute pipelines
    co2_results = analyze_co2_pipeline(carbondioxide, year, month)
    weight_results = analyze_weight_for_month_year(weights, year, month, output_dir)
    temperature_results = analyze_temperature_for_month_year(temperatures, year, month, output_dir)
    humidity_results = analyze_humidity_for_month_year(humidity, weights, temperatures, year, month, output_dir)
    correlation_results = analyze_correlations(humidity, temperatures, weights, carbondioxide, year, month)

    # Collect results
    results = {
        "CO2 Analysis": co2_results,
        "Weight Analysis": weight_results,
        "Temperature Analysis": temperature_results,
        "Humidity Analysis": humidity_results,
        "Correlation Analysis": correlation_results
    }

    # Collect plots only if the section result has valid data
    plots = {}

    if not co2_results.get("Error"):
        plots["CO₂ Analysis"] = ["co2_monthly_trend.png", "co2_diurnal_variation.png"]
    if not weight_results.get("Error"):
        plots["Weight Analysis"] = ["weight_monthly_trend.png"]
    if not temperature_results.get("Error"):
        plots["Temperature Analysis"] = ["temperature_trend.png"]
    if not humidity_results.get("Error"):
        plots["Humidity Analysis"] = ["humidity_trend.png"]
    if not correlation_results.get("Error"):
        plots["Correlation Analysis"] = ["combined_trends.png", "correlation_heatmap.png"]


    # Generate PDF with output directory
    pdf_filename = f"{output_dir}/Monthly_Report_{hive_id}_{year}_{month:02d}.pdf"
    generate_pdf(results, plots, year, month, pdf_filename)

    return pdf_filename

if __name__ == "__main__":
    if len(sys.argv) != 9:
        print("Usage: main.py <co2_file> <weight_file> <temp_file> <humidity_file> <year> <month> <hive_id> <output_dir>")
        sys.exit(1)

    co2_file = sys.argv[1]
    weight_file = sys.argv[2]
    temp_file = sys.argv[3]
    humidity_file = sys.argv[4]
    year = int(sys.argv[5])
    month = int(sys.argv[6])
    hive_id = sys.argv[7]
    output_dir = sys.argv[8]

    pdf_filename = main(co2_file, weight_file, temp_file, humidity_file, year, month, hive_id, output_dir)
    print(f"PDF generated: {pdf_filename}")


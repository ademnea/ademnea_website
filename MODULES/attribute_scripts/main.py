#!/usr/bin/env python3

import pandas as pd #type:ignore
import numpy as np
import os
import argparse
import sys
import logging
import shutil
from datetime import datetime, date
from dateutil.relativedelta import relativedelta
from data_loader import load_data
from data_cleaner import clean_humidity_data, clean_temperature_data, clean_numeric_column, remove_nan_values
from co2_analyzer import analyze_co2_pipeline
from temperature_analyzer import analyze_temperature_for_month_year
from humidity_analyzer import analyze_humidity_for_month_year
from weight_analyzer import analyze_weight_for_month_year
from pdf_generator import generate_attribute_specific_pdf
try:
    from IPython.display import FileLink, display #type:ignore
    JUPYTER_AVAILABLE = True
except ImportError:
    JUPYTER_AVAILABLE = False

# Set up logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Configurable base directory for reports
BASE_REPORTS_DIR = os.getenv('BEEHIVE_REPORT_DIR', '/home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/monthly_reports')

def get_report_folder(year, month, output_dir, attribute):
    """Generate a unique folder path for the report based on year, month, and attribute."""
    month_name = date(year, month, 1).strftime('%B')
    folder_name = f"{year}_{month_name}_{attribute}"
    report_folder = os.path.join(output_dir, folder_name)
    os.makedirs(report_folder, exist_ok=True)
    return report_folder

def create_download_link(filename, download_dir=None):
    """Create a downloadable link for the PDF file or copy to download directory."""
    if JUPYTER_AVAILABLE:
        try:
            display(FileLink(filename))
        except Exception as e:
            logging.warning(f"Failed to create Jupyter FileLink: {str(e)}")
            print(f"PDF available at: {filename}")
    else:
        print(f"PDF available at: {filename}")

    # Copy to download_dir if specified
    if download_dir:
        os.makedirs(download_dir, exist_ok=True)
        dest_path = os.path.join(download_dir, os.path.basename(filename))
        try:
            shutil.copy(filename, dest_path)
            print(f"PDF copied to: {dest_path}")
            logging.info(f"PDF copied to: {dest_path}")
        except Exception as e:
            logging.error(f"Failed to copy PDF to {dest_path}: {str(e)}")

def parse_date_arg(date_str):
    """Parse MM/YYYY format into (month, year) integers."""
    try:
        month_str, year_str = date_str.split('/')
        month = int(month_str)
        year = int(year_str)
        if not (1 <= month <= 12):
            raise ValueError(f"Month must be between 1 and 12, got {month}")
        current_year = datetime.now().year
        if not (2000 <= year <= current_year + 1):
            raise ValueError(f"Year must be between 2000 and {current_year + 1}, got {year}")
        return month, year
    except ValueError as e:
        if '/' not in date_str:
            raise ValueError(f"Invalid format: {date_str}. Use MM/YYYY (e.g., 03/2025)")
        raise ValueError(str(e))

def parse_month(month_input):
    """Convert month input (number or name) to an integer (1-12)."""
    try:
        month = int(month_input)
        if 1 <= month <= 12:
            return month
        raise ValueError("Month must be between 1 and 12")
    except ValueError:
        try:
            month_num = datetime.strptime(month_input.lower(), '%B').month
            return month_num
        except ValueError:
            raise ValueError(f"Invalid month: {month_input}. Use 1-12 or full month name (e.g., March)")

def validate_year(year_input):
    """Validate year input."""
    try:
        year = int(year_input)
        current_year = datetime.now().year
        if 2000 <= year <= current_year + 1:
            return year
        raise ValueError(f"Year must be between 2000 and {current_year + 1}")
    except ValueError:
        raise ValueError(f"Invalid year: {year_input}. Use a number (e.g., 2025)")

def validate_attribute(attribute_input):
    """Validate attribute input with support for Laravel frontend names."""
    # Map Laravel frontend names to Python names
    attribute_map = {
        'co2': 'carbondioxide',
        'temperature': 'temperature',
        'humidity': 'humidity',
        'weight': 'weight'
    }
    
    # Convert input to lowercase and check if it needs mapping
    attr_lower = attribute_input.lower()
    if attr_lower in attribute_map:
        return attribute_map[attr_lower]
    
    # If not a frontend name, check if it's already a valid backend name
    valid_attributes = ['carbondioxide', 'humidity', 'temperature', 'weight']
    if attr_lower in valid_attributes:
        return attr_lower
    
    raise ValueError(f"Invalid attribute: {attribute_input}. Choose from {valid_attributes}")

def get_date_range_and_attribute(args):
    """Get start and end year/month, file paths, hive_id, output_dir, and attribute from args or prompts."""
    parser = argparse.ArgumentParser(description='Beehive Attribute-Specific Reporting Script')
    parser.add_argument('--start_date', type=str, help='Start month and year in MM/YYYY format (e.g., 03/2025)')
    parser.add_argument('--end_date', type=str, help='End month and year in MM/YYYY format (e.g., 06/2025)')
    parser.add_argument('--year', type=str, help='Year (e.g., 2025) for single month analysis')
    parser.add_argument('--month', type=str, help='Month (e.g., 3 or March) for single month analysis')
    parser.add_argument('--attributes', nargs='+', help='List of attributes to analyze (e.g., co2 temperature humidity weight)')
    parser.add_argument('--co2_file', type=str, help='Path to CO2 CSV file')
    parser.add_argument('--weight_file', type=str, help='Path to weight CSV file')
    parser.add_argument('--temp_file', type=str, help='Path to temperature CSV file')
    parser.add_argument('--humidity_file', type=str, help='Path to humidity CSV file')
    parser.add_argument('--hive_id', type=str, default='hive1', help='Hive identifier (default: hive1)')
    parser.add_argument('--output_dir', type=str, default=BASE_REPORTS_DIR, help='Output directory for reports')
    parser.add_argument('--download_dir', type=str, help='Directory to copy the PDF for download')
    args = parser.parse_args()

    # File paths
    csv_data_dir = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'csv_data')
    file_paths = {
        'carbondioxide': args.co2_file or os.path.join(csv_data_dir, f'hive_carbondioxide_{args.hive_id}.csv'),
        'humidity': args.humidity_file or os.path.join(csv_data_dir, f'hive_humidity_{args.hive_id}.csv'),
        'temperature': args.temp_file or os.path.join(csv_data_dir, f'hive_temperatures_{args.hive_id}.csv'),
        'weight': args.weight_file or os.path.join(csv_data_dir, f'hive_weights_{args.hive_id}.csv')
    }
    
    hive_id = args.hive_id
    output_dir = args.output_dir
    download_dir = args.download_dir

    # Determine attributes to process
    attributes_to_process = []
    if args.attributes:
        attributes_to_process = [validate_attribute(attr) for attr in args.attributes]
    elif os.getenv('BEEHIVE_ATTRIBUTE'):
        attributes_to_process = [validate_attribute(os.getenv('BEEHIVE_ATTRIBUTE'))]
    else:
        attributes_to_process = ['temperature']  # Default attribute

    # Range: start_date and end_date
    if args.start_date and args.end_date:
        logging.info(f"Using date range: start_date={args.start_date}, end_date={args.end_date}")
        start_month, start_year = parse_date_arg(args.start_date)
        end_month, end_year = parse_date_arg(args.end_date)
        if args.year or args.month:
            logging.warning("Ignoring --year and --month arguments as date range was provided")
        start_dt = datetime(start_year, start_month, 1)
        end_dt = datetime(end_year, end_month, 1)
        if start_dt > end_dt:
            raise ValueError("Start date must be before or equal to end date")
        return generate_month_range(start_year, start_month, end_year, end_month), file_paths, hive_id, output_dir, download_dir, attributes_to_process

    # Single month: --year and --month
    if args.year and args.month:
        logging.info(f"Using command-line arguments: year={args.year}, month={args.month}")
        year = validate_year(args.year)
        month = parse_month(args.month)
        return [(year, month)], file_paths, hive_id, output_dir, download_dir, attributes_to_process

    # Environment variables for range
    env_start_date = os.getenv('BEEHIVE_START_DATE')
    env_end_date = os.getenv('BEEHIVE_END_DATE')
    if env_start_date and env_end_date:
        logging.info(f"Using environment variables: start_date={env_start_date}, end_date={env_end_date}")
        start_month, start_year = parse_date_arg(env_start_date)
        end_month, end_year = parse_date_arg(env_end_date)
        start_dt = datetime(start_year, start_month, 1)
        end_dt = datetime(end_year, end_month, 1)
        if start_dt > end_dt:
            raise ValueError("Start date must be before or equal to end date")
        return generate_month_range(start_year, start_month, end_year, end_month), file_paths, hive_id, output_dir, download_dir, attributes_to_process

    # Environment variables for single month
    env_year = os.getenv('BEEHIVE_YEAR')
    env_month = os.getenv('BEEHIVE_MONTH')
    if env_year and env_month:
        logging.info(f"Using environment variables: year={env_year}, month={env_month}")
        year = validate_year(env_year)
        month = parse_month(env_month)
        return [(year, month)], file_paths, hive_id, output_dir, download_dir, attributes_to_process

    # Interactive prompts or defaults
    current_year = datetime.now().year
    current_month = datetime.now().month
    default_attribute = 'temperature'  # Default attribute for non-interactive mode

    if sys.stdin.isatty():  # Interactive environment
        try:
            # Prompt for attributes if not provided
            if not attributes_to_process:
                valid_attributes = ['carbondioxide', 'humidity', 'temperature', 'weight']
                attribute_input = input(f"Enter attributes to analyze (comma separated) ({', '.join(valid_attributes)}) [default: {default_attribute}]: ").strip() or default_attribute
                attributes_to_process = [validate_attribute(attr.strip()) for attr in attribute_input.split(',')]

            # Prompt for date range or single month
            range_choice = input("Analyze a single month or a range of months? (single/range) [default: single]: ").strip().lower() or 'single'
            if range_choice == 'range':
                start_date = input("Enter start date (MM/YYYY, e.g., 03/2025): ").strip()
                end_date = input("Enter end date (MM/YYYY, e.g., 06/2025): ").strip()
                start_month, start_year = parse_date_arg(start_date)
                end_month, end_year = parse_date_arg(end_date)
                start_dt = datetime(start_year, start_month, 1)
                end_dt = datetime(end_year, end_month, 1)
                if start_dt > end_dt:
                    raise ValueError("Start date must be before or equal to end date")
                return generate_month_range(start_year, start_month, end_year, end_month), file_paths, hive_id, output_dir, download_dir, attributes_to_process
            else:
                year_input = input(f"Enter year (e.g., {current_year}) [default: {current_year}]: ").strip() or str(current_year)
                year = validate_year(year_input)
                month_input = input(f"Enter month (e.g., 3 or March) [default: {current_month}]: ").strip() or str(current_month)
                month = parse_month(month_input)
                return [(year, month)], file_paths, hive_id, output_dir, download_dir, attributes_to_process
        except ValueError as e:
            logging.error(str(e))
            sys.exit(1)
    else:  # Non-interactive, use defaults
        logging.info(f"Non-interactive mode, using defaults: year={current_year}, month={current_month}, attribute={default_attribute}")
        if not attributes_to_process:
            attributes_to_process = [default_attribute]
        return [(current_year, current_month)], file_paths, hive_id, output_dir, download_dir, attributes_to_process

def generate_month_range(start_year, start_month, end_year, end_month):
    """Generate a list of (year, month) tuples from start to end date."""
    start_date = datetime(start_year, start_month, 1)
    end_date = datetime(end_year, end_month, 1)
    month_list = []
    current_date = start_date
    while current_date <= end_date:
        month_list.append((current_date.year, current_date.month))
        current_date += relativedelta(months=1)
    return month_list

def main(df_dict, year_month_list, hive_id, output_dir, download_dir, attribute):
    logging.info(f"Starting {attribute} analysis for {len(year_month_list)} month(s)")
    
    # Initialize collections for results and plots
    all_results = {}
    all_plots = {}
    start_year, start_month = year_month_list[0]
    end_year, end_month = year_month_list[-1]
    start_month_name = date(start_year, start_month, 1).strftime('%B')
    end_month_name = date(end_year, end_month, 1).strftime('%B')
    
    # Process each month sequentially
    for year, month in year_month_list:
        logging.info(f"Analyzing {attribute} for year={year}, month={month}")
        report_folder = get_report_folder(year, month, output_dir, attribute)
        month_name = date(year, month, 1).strftime('%B')
        month_key = f"{year}_{month_name}"

        # Execute the appropriate pipeline based on attribute
        if attribute == 'carbondioxide':
            results = analyze_co2_pipeline(df_dict['carbondioxide'], year, month, report_folder)
            if not results.get("Error"):
                all_plots[month_key] = {
                    attribute.capitalize() + " Analysis": [
                        os.path.join(report_folder, "co2_monthly_trend.png"),
                        os.path.join(report_folder, "co2_diurnal_variation.png")
                    ]
                }
        elif attribute == 'humidity':
            results = analyze_humidity_for_month_year(df_dict['humidity'], None, df_dict['temperature'], year, month, report_folder)
            if not results.get("Error"):
                all_plots[month_key] = {
                    attribute.capitalize() + " Analysis": [
                        os.path.join(report_folder, "humidity_trend.png")
                    ]
                }
        elif attribute == 'temperature':
            results = analyze_temperature_for_month_year(df_dict['temperature'], year, month, report_folder)
            if not results.get("Error"):
                all_plots[month_key] = {
                    attribute.capitalize() + " Analysis": [
                        os.path.join(report_folder, "temperature_trend.png")
                    ]
                }
        elif attribute == 'weight':
            results = analyze_weight_for_month_year(df_dict['weight'], year, month, report_folder)
            if not results.get("Error"):
                all_plots[month_key] = {
                    attribute.capitalize() + " Analysis": [
                        os.path.join(report_folder, "weight_monthly_trend.png")
                    ]
                }
        else:
            raise ValueError(f"Unsupported attribute: {attribute}")

        all_results[month_key] = {attribute.capitalize() + " Analysis": results}

    # Generate attribute-specific PDF
    report_folder = os.path.join(output_dir, f"Range_{attribute}_{start_year}_{start_month_name}_to_{end_year}_{end_month_name}")
    os.makedirs(report_folder, exist_ok=True)
    pdf_filename = generate_attribute_specific_pdf(all_results, all_plots, start_year, start_month, end_year, end_month, hive_id, attribute, report_folder)
    
    # Output path and provide download link
    print(f"PDF generated successfully at: {pdf_filename}")
    logging.info(f"PDF generated at: {pdf_filename}")
    create_download_link(pdf_filename, download_dir)
    
    return pdf_filename

if __name__ == "__main__":
    # Parse command line arguments
    parser = argparse.ArgumentParser(description='Beehive Attribute-Specific Reporting Script')
    parser.add_argument('--start_date', type=str, help='Start month and year in MM/YYYY format (e.g., 03/2025)')
    parser.add_argument('--end_date', type=str, help='End month and year in MM/YYYY format (e.g., 06/2025)')
    parser.add_argument('--year', type=str, help='Year (e.g., 2025) for single month analysis')
    parser.add_argument('--month', type=str, help='Month (e.g., 3 or March) for single month analysis')
    parser.add_argument('--attributes', nargs='+', help='List of attributes to analyze (e.g., co2 temperature humidity weight)')
    parser.add_argument('--co2_file', type=str, help='Path to CO2 CSV file')
    parser.add_argument('--weight_file', type=str, help='Path to weight CSV file')
    parser.add_argument('--temp_file', type=str, help='Path to temperature CSV file')
    parser.add_argument('--humidity_file', type=str, help='Path to humidity CSV file')
    parser.add_argument('--hive_id', type=str, default='hive1', help='Hive identifier (default: hive1)')
    parser.add_argument('--output_dir', type=str, default=BASE_REPORTS_DIR, help='Output directory for reports')
    parser.add_argument('--download_dir', type=str, help='Directory to copy the PDF for download')
    
    args = parser.parse_args()

    # Get year/month range
    try:
        year_month_list, file_paths, hive_id, output_dir, download_dir, attributes_to_process = get_date_range_and_attribute(args)
    except ValueError as e:
        logging.error(str(e))
        sys.exit(1)

    # Process each attribute sequentially
    for attribute in attributes_to_process:
        df_dict = {}
        try:
            if attribute == 'carbondioxide':
                df_dict['carbondioxide'] = load_data(file_paths['carbondioxide'])
                df_dict['carbondioxide'] = clean_numeric_column(df_dict['carbondioxide'])
                df_dict['carbondioxide'] = remove_nan_values(df_dict['carbondioxide'])
            elif attribute == 'humidity':
                df_dict['humidity'] = load_data(file_paths['humidity'])
                df_dict['temperature'] = load_data(file_paths['temperature'])
                df_dict['humidity'] = clean_humidity_data(df_dict['humidity'])
                df_dict['temperature'] = clean_temperature_data(df_dict['temperature'])
                df_dict['humidity'] = remove_nan_values(df_dict['humidity'])
                df_dict['temperature'] = remove_nan_values(df_dict['temperature'])
            elif attribute == 'temperature':
                df_dict['temperature'] = load_data(file_paths['temperature'])
                df_dict['temperature'] = clean_temperature_data(df_dict['temperature'])
                df_dict['temperature'] = remove_nan_values(df_dict['temperature'])
            elif attribute == 'weight':
                df_dict['weight'] = load_data(file_paths['weight'])
                df_dict['weight'] = clean_numeric_column(df_dict['weight'])
                df_dict['weight'] = remove_nan_values(df_dict['weight'])
        except Exception as e:
            logging.error(f"Failed to load or clean data for {attribute}: {str(e)}")
            continue  # Skip this attribute if there was an error

        # Run main analysis for this attribute
        main(df_dict, year_month_list, hive_id, output_dir, download_dir, attribute)
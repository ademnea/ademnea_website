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
from weight_analyzer import analyze_weight_for_month_year
from temperature_analyzer import analyze_temperature_for_month_year
from humidity_analyzer import analyze_humidity_for_month_year
from correlation_analyzer import analyze_correlations
from pdf_generator import generate_pdf
try:
    from IPython.display import FileLink, display #type:ignore
    JUPYTER_AVAILABLE = True
except ImportError:
    JUPYTER_AVAILABLE = False

# Set up logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

# Configurable base directory for monthly reports
BASE_REPORTS_DIR = os.getenv('BEEHIVE_REPORT_DIR', '/home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/monthly_reports')

def get_report_folder(year, month, output_dir):
    """Generate a unique folder path for the report based on year and month name."""
    month_name = date(year, month, 1).strftime('%B')
    folder_name = f"{year}_{month_name}"
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

def get_date_range(args):
    """Get start and end year/month, file paths, hive_id, and output_dir from command-line args, environment variables, or prompts."""
    # 1. Check for single-month command-line arguments (like the original script)
    if len(sys.argv) == 9:
        try:
            co2_file = sys.argv[1]
            weight_file = sys.argv[2]
            temp_file = sys.argv[3]
            humidity_file = sys.argv[4]
            year = validate_year(sys.argv[5])
            month = parse_month(sys.argv[6])
            hive_id = sys.argv[7]
            output_dir = sys.argv[8]
            logging.info(f"Using single-month command-line arguments: year={year}, month={month}, hive_id={hive_id}")
            return [(year, month)], co2_file, weight_file, temp_file, humidity_file, hive_id, output_dir, None
        except ValueError as e:
            logging.error(str(e))
            sys.exit(1)

    # 2. Check for range-based command-line arguments
    parser = argparse.ArgumentParser(description='Beehive Reporting Script')
    parser.add_argument('--start_date', type=str, help='Start month and year in MM/YYYY format (e.g., 03/2025)')
    parser.add_argument('--end_date', type=str, help='End month and year in MM/YYYY format (e.g., 06/2025)')
    parser.add_argument('--year', type=str, help='Year (e.g., 2025) for single month analysis')
    parser.add_argument('--month', type=str, help='Month (e.g., 3 or March) for single month analysis')
    parser.add_argument('--co2_file', type=str, help='Path to CO2 CSV file')
    parser.add_argument('--weight_file', type=str, help='Path to weight CSV file')
    parser.add_argument('--temp_file', type=str, help='Path to temperature CSV file')
    parser.add_argument('--humidity_file', type=str, help='Path to humidity CSV file')
    parser.add_argument('--hive_id', type=str, default='hive1', help='Hive identifier (default: hive1)')
    parser.add_argument('--output_dir', type=str, default=BASE_REPORTS_DIR, help='Output directory for reports')
    parser.add_argument('--download_dir', type=str, help='Directory to copy the PDF for download')
    args = parser.parse_args()
    
    
    parser.add_argument('--attributes', type=str, nargs='+',
                    default=['co2', 'weight', 'temperature', 'humidity', 'correlation'],
                    help='List of attributes to analyze. Options: co2, weight, temperature, humidity, correlation')


    # File paths: use provided arguments or environment variable defaults
    file_paths = {
        'carbondioxide': args.co2_file or os.getenv('BEEHIVE_CO2_CSV', '/home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_carbondioxide_hive1.csv'),
        'humidity': args.humidity_file or os.getenv('BEEHIVE_HUMIDITY_CSV', '/home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_humidity_hive1.csv'),
        'temperatures': args.temp_file or os.getenv('BEEHIVE_TEMPERATURE_CSV', '/home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_temperatures_hive1.csv'),
        'weights': args.weight_file or os.getenv('BEEHIVE_WEIGHT_CSV', '/home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_weights_hive1.csv')
    }
    hive_id = args.hive_id
    output_dir = args.output_dir
    download_dir = args.download_dir

    # 3. Range: start_date and end_date
    if args.start_date and args.end_date:
        logging.info(f"Using date range: start_date={args.start_date}, end_date={args.end_date}")
        start_month, start_year = parse_date_arg(args.start_date)
        end_month, end_year = parse_date_arg(args.end_date)
        if args.year or args.month:
            logging.warning("Ignoring --year and --month arguments as date range was provided")
        # Validate date range
        start_dt = datetime(start_year, start_month, 1)
        end_dt = datetime(end_year, end_month, 1)
        if start_dt > end_dt:
            raise ValueError("Start date must be before or equal to end date")
        return generate_month_range(start_year, start_month, end_year, end_month), file_paths['carbondioxide'], file_paths['weights'], file_paths['temperatures'], file_paths['humidity'], hive_id, output_dir, download_dir

    # 4. Single month: --year and --month
    if args.year and args.month:
        logging.info(f"Using command-line arguments: year={args.year}, month={args.month}")
        year = validate_year(args.year)
        month = parse_month(args.month)
        return [(year, month)], file_paths['carbondioxide'], file_paths['weights'], file_paths['temperatures'], file_paths['humidity'], hive_id, output_dir, download_dir

    # 5. Environment variables for range
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
        return generate_month_range(start_year, start_month, end_year, end_month), file_paths['carbondioxide'], file_paths['weights'], file_paths['temperatures'], file_paths['humidity'], hive_id, output_dir, download_dir

    # 6. Environment variables for single month
    env_year = os.getenv('BEEHIVE_YEAR')
    env_month = os.getenv('BEEHIVE_MONTH')
    if env_year and env_month:
        logging.info(f"Using environment variables: year={env_year}, month={env_month}")
        year = validate_year(env_year)
        month = parse_month(env_month)
        return [(year, month)], file_paths['carbondioxide'], file_paths['weights'], file_paths['temperatures'], file_paths['humidity'], hive_id, output_dir, download_dir

    # 7. Interactive prompts or defaults
    current_year = datetime.now().year
    current_month = datetime.now().month

    if sys.stdin.isatty():  # Interactive environment
        try:
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
                return generate_month_range(start_year, start_month, end_year, end_month), file_paths['carbondioxide'], file_paths['weights'], file_paths['temperatures'], file_paths['humidity'], hive_id, output_dir, download_dir
            else:
                year_input = input(f"Enter year (e.g., {current_year}) [default: {current_year}]: ").strip() or str(current_year)
                year = validate_year(year_input)
                month_input = input(f"Enter month (e.g., 3 or March) [default: {current_month}]: ").strip() or str(current_month)
                month = parse_month(month_input)
                return [(year, month)], file_paths['carbondioxide'], file_paths['weights'], file_paths['temperatures'], file_paths['humidity'], hive_id, output_dir, download_dir
        except ValueError as e:
            logging.error(str(e))
            sys.exit(1)
    else:  # Non-interactive, use defaults
        logging.info(f"Non-interactive mode, using defaults: year={current_year}, month={current_month}")
        return [(current_year, current_month)], file_paths['carbondioxide'], file_paths['weights'], file_paths['temperatures'], file_paths['humidity'], hive_id, output_dir, download_dir

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

def main(df_co2, df_weight, df_temperature, df_humidity, year_month_list, hive_id, output_dir, download_dir=None, attributes=None):
    if attributes is None:
        attributes = ['co2', 'weight', 'temperature', 'humidity', 'correlation']

    logging.info(f"Starting analysis for {len(year_month_list)} month(s), attributes={attributes}")

    all_results = {}
    all_plots = {}

    for year, month in year_month_list:
        report_folder = get_report_folder(year, month, output_dir)
        month_key = f"{year}_{date(year, month, 1).strftime('%B')}"

        results = {}
        plots = {}

        if 'co2' in attributes:
            co2_results = analyze_co2_pipeline(df_co2, year, month, report_folder)
            results['CO2 Analysis'] = co2_results
            if not co2_results.get("Error"):
                plots['CO2 Analysis'] = [
                    os.path.join(report_folder, "co2_monthly_trend.png"),
                    os.path.join(report_folder, "co2_diurnal_variation.png")
                ]

        if 'weight' in attributes:
            weight_results = analyze_weight_for_month_year(df_weight, year, month, report_folder)
            results['Weight Analysis'] = weight_results
            if not weight_results.get("Error"):
                plots['Weight Analysis'] = [os.path.join(report_folder, "weight_monthly_trend.png")]

        if 'temperature' in attributes:
            temperature_results = analyze_temperature_for_month_year(df_temperature, year, month, report_folder)
            results['Temperature Analysis'] = temperature_results
            if not temperature_results.get("Error"):
                plots['Temperature Analysis'] = [os.path.join(report_folder, "temperature_trend.png")]

        if 'humidity' in attributes:
            humidity_results = analyze_humidity_for_month_year(df_humidity, df_weight, df_temperature, year, month, report_folder)
            results['Humidity Analysis'] = humidity_results
            if not humidity_results.get("Error"):
                plots['Humidity Analysis'] = [os.path.join(report_folder, "humidity_trend.png")]

        if 'correlation' in attributes:
            correlation_results = analyze_correlations(df_humidity, df_temperature, df_weight, df_co2, year, month, report_folder)
            results['Correlation Analysis'] = correlation_results
            if not correlation_results.get("Error"):
                plots['Correlation Analysis'] = [
                    os.path.join(report_folder, "co2_temp_weight_trends.png"),
                    os.path.join(report_folder, "humidity_weight_trends.png"),
                    os.path.join(report_folder, "correlation_heatmap.png")
                ]

        all_results[month_key] = results
        all_plots[month_key] = plots

    # Generate PDF for all months
    report_folder = os.path.join(output_dir, f"Range_{year_month_list[0][0]}_{date(year_month_list[0][0], year_month_list[0][1], 1).strftime('%B')}_to_{year_month_list[-1][0]}_{date(year_month_list[-1][0], year_month_list[-1][1], 1).strftime('%B')}")
    os.makedirs(report_folder, exist_ok=True)
    pdf_filename = generate_pdf(all_results, all_plots, year_month_list[0][0], year_month_list[0][1], year_month_list[-1][0], year_month_list[-1][1], hive_id, report_folder)

    logging.info(f"PDF generated at: {pdf_filename}")
    create_download_link(pdf_filename, download_dir)
    return pdf_filename


if __name__ == "__main__":
    # Get date range and file paths
    try:
        year_month_list, co2_file, weight_file, temp_file, humidity_file, hive_id, output_dir, download_dir = get_date_range(sys.argv)
    except ValueError as e:
        logging.error(str(e))
        sys.exit(1)

    # Load data
    try:
        carbondioxide = load_data(co2_file)
        humidity = load_data(humidity_file)
        temperatures = load_data(temp_file)
        weights = load_data(weight_file)
    except Exception as e:
        logging.error(f"Failed to load data: {str(e)}")
        sys.exit(1)

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

    # Run main analysis
    main(carbondioxide, weights, temperatures, humidity, year_month_list, hive_id, output_dir, download_dir)
    
    #Sample command to run the script:
    #Range of months:
#     python main.py \
#   --start_date 03/2025 \
#   --end_date 06/2025 \
#   --co2_file /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_carbondioxide_hive1.csv \
#   --weight_file /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_weights_hive1.csv \
#   --temp_file /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_temperatures_hive1.csv \
#   --humidity_file /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_humidity_hive1.csv \
#   --hive_id hive1 \
#   --output_dir /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/monthly_reports \
#   --download_dir /home/ltgwgeorge/Desktop/downloads
    # Single month(defau):
#     python main.py \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_carbondioxide_hive1.csv \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_weights_hive1.csv \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_temperatures_hive1.csv \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_humidity_hive1.csv \
#   2025 \
#   3 \
#   hive1 \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/monthly_reports
    # Single month with environment variables:
#     python main.py \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_carbondioxide_hive1.csv \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_weights_hive1.csv \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_temperatures_hive1.csv \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/csv_files/hive_humidity_hive1.csv \
#   2025 \
#   3 \
#   hive1 \
#   /home/ltgwgeorge/Desktop/IoT-RA/forAnalytics/beehive_reporting/monthly_reports \
#   --download_dir /home/ltgwgeorge/Desktop/downloads
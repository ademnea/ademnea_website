#!/usr/bin/env python3


import pandas as pd
import numpy as np

def clean_humidity_data(df):
    """
    Clean the humidity data:
    - Replace '*2*' with a comma in the 'record' column.
    - Split the 'record' column into 'Interior (%)' and 'Exterior (%)'.
    - Convert new columns to numeric types.
    - Replace values of 2 with NaN.
    """
    df['record'] = df['record'].str.replace('*2*', ',', regex=False)
    df[['Interior (%)', 'Exterior (%)']] = df['record'].str.split(',', expand=True)
    df = df.drop(columns=['record'])
    df['Interior (%)'] = pd.to_numeric(df['Interior (%)'], errors='coerce')
    df['Exterior (%)'] = pd.to_numeric(df['Exterior (%)'], errors='coerce')
    df['Interior (%)'] = df['Interior (%)'].replace(2, np.nan)
    df['Exterior (%)'] = df['Exterior (%)'].replace(2, np.nan)
    return df

def clean_temperature_data(df):
    """
    Clean the temperature data:
    - Replace '*2*' with a comma in the 'record' column.
    - Split the 'record' column into 'Interior (°C)' and 'Exterior (°C)'.
    - Convert new columns to numeric types.
    - Replace values of 2 with NaN.
    """
    df['record'] = df['record'].str.replace('*2*', ',', regex=False)
    df[['Interior (°C)', 'Exterior (°C)']] = df['record'].str.split(',', expand=True)
    df = df.drop(columns=['record'])
    df['Interior (°C)'] = pd.to_numeric(df['Interior (°C)'], errors='coerce')
    df['Exterior (°C)'] = pd.to_numeric(df['Exterior (°C)'], errors='coerce')
    df['Interior (°C)'] = df['Interior (°C)'].replace(2, np.nan)
    df['Exterior (°C)'] = df['Exterior (°C)'].replace(2, np.nan)
    return df

def clean_numeric_column(df, column='record'):
    """
    Clean a numeric column by replacing specific values (e.g., 2) with NaN.
    """
    df[column] = df[column].replace(2, np.nan)
    return df

def remove_nan_values(df):
    """
    Remove rows with NaN values from the DataFrame.
    """
    return df.dropna()
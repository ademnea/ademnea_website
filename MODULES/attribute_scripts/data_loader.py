#!/usr/bin/env python3


import pandas as pd

def load_data(file_path, index_col='created_at'):
    """
    Load data from a CSV file and set the specified column as the index.
    Convert the index to datetime and remove duplicate indices.
    """
    df = pd.read_csv(file_path, index_col=index_col)
    df.index = pd.to_datetime(df.index)
    df = df[~df.index.duplicated(keep='first')]  # Remove duplicate indices
    return df
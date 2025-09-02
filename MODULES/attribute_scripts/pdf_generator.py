#!/usr/bin/env python3

import os
from datetime import date
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Image, Table, TableStyle, PageBreak

def generate_attribute_specific_pdf(results, plots, start_year, start_month, end_year, end_month, hive_id, attribute, report_folder):
    """Generate a single PDF report for a specific attribute with structured tables and plots."""
    start_month_name = date(start_year, start_month, 1).strftime('%B')
    end_month_name = date(end_year, end_month, 1).strftime('%B')
    filename = os.path.join(report_folder, f"{attribute.capitalize()}_Report_{hive_id}_{start_year}_{start_month_name}_to_{end_year}_{end_month_name}.pdf")
    doc = SimpleDocTemplate(filename, pagesize=letter)
    styles = getSampleStyleSheet()
    story = []

    # Add title
    title = f"{attribute.capitalize()} Analysis Report for {hive_id}: {start_month_name} {start_year} to {end_month_name} {end_year}"
    story.append(Paragraph(title, styles['Title']))
    story.append(Spacer(1, 24))

    def format_value(value):
        """Format values for display, handling np.float64 and nan."""
        if isinstance(value, float) and value != value:  # Check for nan
            return "N/A"
        return f"{value:.2f}" if isinstance(value, (int, float)) else str(value)

    def create_table(data, headers=None, col_widths=None):
        """Create a ReportLab table with consistent styling."""
        if headers:
            table_data = [headers] + data
        else:
            table_data = data
        table = Table(table_data, colWidths=col_widths)
        table.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
            ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
            ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
            ('FONTNAME', (0, 0), (-1, 0), 'Helvetica-Bold'),
            ('FONTSIZE', (0, 0), (-1, 0), 12),
            ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
            ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
            ('GRID', (0, 0), (-1, -1), 1, colors.black),
        ]))
        return table

    # Process each month sequentially
    for month_key, month_results in results.items():
        year, month_name = month_key.split('_')
        year = int(year)
        story.append(Paragraph(f"{attribute.capitalize()} Analysis for {month_name} {year}", styles['Heading1']))
        story.append(Spacer(1, 24))

        section = f"{attribute.capitalize()} Analysis"
        content = month_results.get(section, {})

        if isinstance(content, dict):
            if 'Error' in content:
                story.append(Paragraph(f"Error: {content['Error']}", styles['BodyText']))
                story.append(Spacer(1, 12))
                continue

            if attribute == 'carbondioxide':
                # Statistics Table
                stats = content.get('Statistics', {})
                stats_data = [[key.replace('CO2', 'CO2 (ppm)'), format_value(value)] for key, value in stats.items()]
                stats_table = create_table(stats_data, headers=["Statistic", "Value"])
                story.append(Paragraph("CO2 Statistics", styles['Heading3']))
                story.append(stats_table)
                story.append(Spacer(1, 12))

                # Diurnal Variations Table
                diurnal = content.get('Diurnal Variations', {})
                diurnal_data = [[key.replace('CO2', 'CO2 (ppm)'), format_value(value)] for key, value in diurnal.items()]
                diurnal_table = create_table(diurnal_data, headers=["Metric", "Value"])
                story.append(Paragraph("Diurnal Variations", styles['Heading3']))
                story.append(diurnal_table)
                story.append(Spacer(1, 12))

                # Weekly Trend Table
                trends = content.get('Trends', {})
                weekly_trend = trends.get('Weekly Trend', {})
                weekly_data = [[str(timestamp), format_value(value)] for timestamp, value in weekly_trend.items()]
                if weekly_data:
                    weekly_table = create_table(weekly_data, headers=["Week", "Average CO2 (ppm)"])
                    story.append(Paragraph("Weekly Trend", styles['Heading3']))
                    story.append(weekly_table)
                    story.append(Spacer(1, 12))

                # Daily Trend Table
                daily_trend = trends.get('Daily Trend', {})
                daily_data = [[str(timestamp), format_value(value)] for timestamp, value in daily_trend.items()]
                if daily_data:
                    daily_table = create_table(daily_data, headers=["Date", "Average CO2 (ppm)"])
                    story.append(Paragraph("Daily Trend", styles['Heading3']))
                    story.append(daily_table)
                    story.append(Spacer(1, 12))

                # Anomalies Table
                anomalies = content.get('Anomalies', [])
                anomalies_data = [[format_value(item['record']), item.get('timestamp', 'N/A')] for item in anomalies]
                if anomalies_data:
                    anomalies_table = create_table(anomalies_data, headers=["Anomalous CO2 (ppm)", "Timestamp"])
                    story.append(Paragraph("Anomalies", styles['Heading3']))
                    story.append(anomalies_table)
                    story.append(Spacer(1, 12))

            elif attribute == 'humidity':
                # Humidity Insights Table
                humidity_insights = content.get('Humidity Insights', {})
                interior = humidity_insights.get('Interior Humidity (%)', {})
                exterior = humidity_insights.get('Exterior Humidity (%)', {})
                humidity_data = [
                    ["Metric", "Interior (%)", "Exterior (%)"],
                    ["Average", format_value(interior.get('Average', 'N/A')), format_value(exterior.get('Average', 'N/A'))],
                    ["Min", format_value(interior.get('Min', 'N/A')), format_value(exterior.get('Min', 'N/A'))],
                    ["Max", format_value(interior.get('Max', 'N/A')), format_value(exterior.get('Max', 'N/A'))],
                    ["Standard Deviation", format_value(interior.get('Standard Deviation', 'N/A')), format_value(exterior.get('Standard Deviation', 'N/A'))],
                    ["Range", format_value(interior.get('Range', 'N/A')), format_value(exterior.get('Range', 'N/A'))]
                ]
                humidity_table = create_table(humidity_data, headers=None)
                story.append(Paragraph("Humidity Statistics", styles['Heading3']))
                story.append(humidity_table)
                story.append(Spacer(1, 12))

                # Anomalies Table
                anomalies = content.get('Anomalies', [])
                anomalies_data = [
                    [format_value(item['Interior (%)']), format_value(item['Exterior (%)']), item.get('timestamp', 'N/A')]
                    for item in anomalies
                ]
                if anomalies_data:
                    anomalies_table = create_table(anomalies_data, headers=["Interior (%)", "Exterior (%)", "Timestamp"])
                    story.append(Paragraph("Anomalies", styles['Heading3']))
                    story.append(anomalies_table)
                    story.append(Spacer(1, 12))

            elif attribute == 'temperature':
                # Temperature Statistics Table
                temp_stats = content.get('Temperature Statistics', {})
                exterior = temp_stats.get('Exterior', {})
                interior = temp_stats.get('Interior', {})
                temp_data = [
                    ["Metric", "Exterior (°C)", "Interior (°C)"],
                    ["Lowest", format_value(exterior.get('Lowest', 'N/A')), format_value(interior.get('Lowest', 'N/A'))],
                    ["Highest", format_value(exterior.get('Highest', 'N/A')), format_value(interior.get('Highest', 'N/A'))],
                    ["Average", format_value(exterior.get('Average', 'N/A')), format_value(interior.get('Average', 'N/A'))]
                ]
                temp_table = create_table(temp_data, headers=None)
                story.append(Paragraph("Temperature Statistics", styles['Heading3']))
                story.append(temp_table)
                story.append(Spacer(1, 12))

                # Standard Deviation Table
                std_dev = content.get('Standard Deviation', {})
                std_data = [[key, format_value(value)] for key, value in std_dev.items()]
                std_table = create_table(std_data, headers=["Metric", "Value"])
                story.append(Paragraph("Standard Deviation", styles['Heading3']))
                story.append(std_table)
                story.append(Spacer(1, 12))

                # Anomalies Table
                anomalies = content.get('Anomalies', [])
                anomalies_data = [
                    [format_value(item['Interior (°C)']), format_value(item['Exterior (°C)']), item.get('timestamp', 'N/A')]
                    for item in anomalies
                ]
                if anomalies_data:
                    anomalies_table = create_table(anomalies_data, headers=["Interior (°C)", "Exterior (°C)", "Timestamp"])
                    story.append(Paragraph("Anomalies", styles['Heading3']))
                    story.append(anomalies_table)
                    story.append(Spacer(1, 12))

            elif attribute == 'weight':
                # Statistics Table
                stats = content.get('Statistics', {})
                stats_data = [[key, format_value(value)] for key, value in stats.items()]
                stats_table = create_table(stats_data, headers=["Statistic", "Value"])
                story.append(Paragraph("Weight Statistics", styles['Heading3']))
                story.append(stats_table)
                story.append(Spacer(1, 12))

                # Daily Fluctuations Table
                fluctuations = content.get('Daily Weight Fluctuations', [])
                if fluctuations:
                    headers = ["Min (kg)", "Max (kg)", "Mean (kg)", "Fluctuation Range (kg)"]
                    fluctuations_data = [[format_value(item.get(key, 'N/A')) for key in ['min', 'max', 'mean', 'fluctuation_range']] for item in fluctuations]
                    fluctuations_table = create_table(fluctuations_data, headers=headers)
                    story.append(Paragraph("Daily Weight Fluctuations", styles['Heading3']))
                    story.append(fluctuations_table)
                    story.append(Spacer(1, 12))

                # Hourly Patterns Table
                hourly = content.get('Hourly Patterns', {})
                hourly_data = [[key, format_value(value)] for key, value in hourly.items()]
                hourly_table = create_table(hourly_data, headers=["Metric", "Value"])
                story.append(Paragraph("Hourly Patterns", styles['Heading3']))
                story.append(hourly_table)
                story.append(Spacer(1, 12))

                # Anomalies Table
                anomalies = content.get('Anomalies', [])
                anomalies_data = [[format_value(item['record']), item.get('timestamp', 'N/A')] for item in anomalies]
                if anomalies_data:
                    anomalies_table = create_table(anomalies_data, headers=["Anomalous Weight (kg)", "Timestamp"])
                    story.append(Paragraph("Anomalies", styles['Heading3']))
                    story.append(anomalies_table)
                    story.append(Spacer(1, 12))

        # Add plots for the section
        if month_key in plots and section in plots[month_key]:
            for plot_path in plots[month_key][section]:
                if os.path.exists(plot_path):
                    img = Image(plot_path, width=400, height=200)
                    story.append(img)
                    story.append(Spacer(1, 12))
                else:
                    story.append(Paragraph(f"Plot not found: {os.path.basename(plot_path)}", styles['BodyText']))
                    story.append(Spacer(1, 12))

        # Add page break after each month (except the last)
        if month_key != list(results.keys())[-1]:
            story.append(PageBreak())

    # Build the PDF
    doc.build(story)
    return filename
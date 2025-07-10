
#!/usr/bin/env python3

import os
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.platypus import Paragraph, SimpleDocTemplate, Spacer, Image

def generate_pdf(results, plots, year, month, filename):
    doc = SimpleDocTemplate(filename, pagesize=letter)
    styles = getSampleStyleSheet()
    story = []

    # Add title
    title = f"Monthly Analysis Report for {year}-{month:02d}"
    story.append(Paragraph(title, styles['Title']))
    story.append(Spacer(1, 12))

    # Add sections
    for section, content in results.items():
        # Section title
        story.append(Paragraph(section, styles['Heading2']))
        story.append(Spacer(1, 12))

        # Section content
        if isinstance(content, dict):
            for key, value in content.items():
                story.append(Paragraph(f"<b>{key}:</b> {value}", styles['BodyText']))
                story.append(Spacer(1, 6))
        else:
            story.append(Paragraph(str(content), styles['BodyText']))
            story.append(Spacer(1, 6))

        # Add plots
        if section in plots:
            for plot in plots[section]:
                plot_path = os.path.join(os.path.dirname(filename), plot)
                if os.path.exists(plot_path):
                    img = Image(plot_path, width=400, height=200)
                    story.append(img)
                    story.append(Spacer(1, 12))
                else:
                    story.append(Paragraph(f"Plot not found: {plot}", styles['BodyText']))
                    story.append(Spacer(1, 12))

    # Build the PDF
    doc.build(story)
    return filename


#!/usr/bin/env python3
"""
Example usage of the Bowling Image to CSV Extractor

This script demonstrates how to use the extractor with sample data
or with an actual image file.
"""

import sys
import os
from image_to_csv_extractor import BowlingImageExtractor



def process_sample_data():
    """Process sample data (no image required)."""
    print("=== Processing Sample Bowling Data ===\n")
    
    # Create extractor instance
    extractor = BowlingImageExtractor()
    
    # Get sample data (simulating Textract output)
    #sample_text_lines = create_sample_data()
    
    print("Processing sample bowling data...")
    
    # Parse bowler data
    bowlers = extractor.parse_bowler_data(sample_text_lines)
    print(f"Found {len(bowlers)} bowlers")
    
    # Extract match information
    match_info = extractor.extract_match_info(sample_text_lines)
    
    # Create CSV files
    extractor.create_bowlers_csv(bowlers, "sample_bowlers.csv")
    extractor.create_database_import_csv(bowlers, match_info, "sample_database_import.csv")
    extractor.create_detailed_csv(bowlers, match_info, "sample_detailed_scores.csv")
    
    # Print summary
    print(f"\n=== MATCH SUMMARY ===")
    print(f"Match: {match_info['team1_name']} vs {match_info['team2_name']}")
    print(f"Date: {match_info['date']}")
    print(f"Type: {match_info['match_type']}")
    print(f"Total Bowlers: {len(bowlers)}")
    
    # Show Anthony Escalona's data
    anthony = next((b for b in bowlers if 'ANTHONY ESCALONA' in b['name']), None)
    if anthony:
        print(f"\n=== ANTHONY ESCALONA DETAILS ===")
        print(f"Lane: {anthony['lane_number']}")
        print(f"Average: {anthony['average']}")
        print(f"Handicap: {anthony['handicap']}")
        print(f"Game 1: {anthony['game1_score']}")
        print(f"Game 2: {anthony['game2_score']}")
        print(f"Game 3: {anthony['game3_score']}")
        print(f"Total: {anthony['total_score']}")
    
    # Show first few bowlers
    print(f"\n=== FIRST 5 BOWLERS ===")
    for i, bowler in enumerate(bowlers[:5]):
        print(f"{i+1}. {bowler['name']} (Lane {bowler['lane_number']})")
        print(f"   Games: {bowler['game1_score']}, {bowler['game2_score']}, {bowler['game3_score']}")
        print(f"   Total: {bowler['total_score']}")
        print()
    
    print("=== CSV FILES CREATED ===")
    print("1. sample_bowlers.csv - Basic bowler information")
    print("2. sample_database_import.csv - Formatted for database import")
    print("3. sample_detailed_scores.csv - Complete match details")

def process_image_file(image_path):
    """Process an actual image file."""
    print(f"=== Processing Image File: {image_path} ===\n")
    
    # Check if file exists
    if not os.path.exists(image_path):
        print(f"Error: Image file '{image_path}' not found.")
        return
    
    # Create extractor instance
    extractor = BowlingImageExtractor()
    
    # Process the image
    extractor.process_image(image_path, "image_output")

def main():
    """Main function to demonstrate the extractor."""
    print("=== Bowling Image to CSV Extractor Example ===\n")
    
   
    image_path = sys.argv[1]
    process_image_file(image_path)
   
    print("\n=== USAGE INSTRUCTIONS ===")
    print("To use with sample data (no image required):")
    print("  python example_usage.py")
    print()
    print("To use with a real image file:")
    print("  python example_usage.py your_bowling_image.jpg")
    print()
    print("To use the main extractor with custom output prefix:")
    print("  python image_to_csv_extractor.py your_bowling_image.jpg my_match")

if __name__ == "__main__":
    main()

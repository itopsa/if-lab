#!/usr/bin/env python3
"""
Bowling Image to CSV Extractor

This script extracts bowler information and scores from bowling sheet images
using Amazon Textract and converts the data to CSV format.
"""

import boto3
import csv
import re
import json
import sys
from typing import List, Dict, Optional
from datetime import datetime

class BowlingImageExtractor:
    def __init__(self, aws_region='us-east-1'):
        """Initialize the Textract client."""
        self.textract_client = boto3.client('textract', region_name=aws_region)
        
    def extract_text_from_image(self, image_path: str) -> List[str]:
        """
        Extract text from an image using Amazon Textract.
        
        Args:
            image_path: Path to the image file
            
        Returns:
            List of text lines extracted from the image
        """
        try:
            # Read the image file
            with open(image_path, 'rb') as document:
                image_bytes = bytearray(document.read())
            
            # Call Textract to detect document text
            response = self.textract_client.detect_document_text(
                Document={'Bytes': image_bytes}
            )
            
            # Extract text lines
            text_lines = []
            for block in response['Blocks']:
                if block['BlockType'] == 'LINE':
                    text_lines.append(block['Text'])
            
            return text_lines
            
        except FileNotFoundError:
            print(f"Error: Image file '{image_path}' not found.")
            return []
        except Exception as e:
            print(f"Error extracting text from image: {e}")
            return []
    
    def parse_bowler_data(self, text_lines: List[str]) -> List[Dict]:
        """
        Parse bowler information from text lines.
        
        Args:
            text_lines: List of text lines from Textract
            
        Returns:
            List of dictionaries containing bowler information
        """
        bowlers = []
        
        # Textract gives us each field on a separate line, so we need to group them
        i = 0
        while i < len(text_lines):
            line = text_lines[i].strip()
            
            # Skip empty lines and headers
            if not line or line in ['Lane #', 'Bowler Name', 'Avg', 'HDCP', 'Game 1', 'Game 2', 'Game 3', 'Total']:
                i += 1
                continue
            
            # Look for a lane number (single digit or double digit)
            if re.match(r'^\d+$', line) and i + 7 < len(text_lines):
                try:
                    lane_num = int(line)
                    bowler_name = text_lines[i + 1].strip()
                    avg = int(text_lines[i + 2].strip())
                    hdcp = int(text_lines[i + 3].strip())
                    game1 = int(text_lines[i + 4].strip())
                    game2 = int(text_lines[i + 5].strip())
                    game3 = int(text_lines[i + 6].strip())
                    total = int(text_lines[i + 7].strip())
                    
                    # Verify this looks like bowler data (name should contain letters)
                    if re.match(r'^[A-Z\s]+$', bowler_name):
                        bowler_info = {
                            'lane_number': lane_num,
                            'name': bowler_name.strip(),
                            'average': avg,
                            'handicap': hdcp,
                            'game1_score': game1,
                            'game2_score': game2,
                            'game3_score': game3,
                            'total_score': total
                        }
                        bowlers.append(bowler_info)
                        i += 8  # Skip to next potential bowler
                    else:
                        i += 1
                except (ValueError, IndexError):
                    i += 1
            else:
                i += 1
        
        return bowlers
    
    def extract_match_info(self, text_lines: List[str]) -> Dict:
        """
        Extract match information from text lines.
        
        Args:
            text_lines: List of text lines from Textract
            
        Returns:
            Dictionary containing match information
        """
        match_info = {
            'team1_name': '',
            'team2_name': '',
            'date': '',
            'match_type': ''
        }
        
        for i, line in enumerate(text_lines):
            line = line.strip()
            
            # Extract team names
            if line == 'Team 1':
                if i + 1 < len(text_lines):
                    match_info['team1_name'] = text_lines[i + 1].strip()
            elif line == 'Team 2':
                if i + 1 < len(text_lines):
                    match_info['team2_name'] = text_lines[i + 1].strip()
            elif line == 'Date':
                if i + 1 < len(text_lines):
                    match_info['date'] = text_lines[i + 1].strip()
            elif 'Scratch Pair' in line:
                match_info['match_type'] = 'Scratch Pair'
            elif 'Handicap' in line:
                match_info['match_type'] = 'Handicap'
        
        return match_info
    
    def create_bowlers_csv(self, bowlers: List[Dict], output_file: str = 'bowlers.csv'):
        """
        Create CSV file with bowler information.
        
        Args:
            bowlers: List of bowler dictionaries
            output_file: Output CSV filename
        """
        with open(output_file, 'w', newline='', encoding='utf-8') as csvfile:
            fieldnames = ['lane_number', 'name', 'average', 'handicap', 'game1_score', 'game2_score', 'game3_score', 'total_score']
            writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
            
            writer.writeheader()
            for bowler in bowlers:
                writer.writerow(bowler)
        
        print(f"Bowlers CSV created: {output_file}")
    
    def create_database_import_csv(self, bowlers: List[Dict], match_info: Dict, output_file: str = 'database_import.csv'):
        """
        Create CSV file formatted for database import.
        
        Args:
            bowlers: List of bowler dictionaries
            match_info: Match information dictionary
            output_file: Output CSV filename
        """
        with open(output_file, 'w', newline='', encoding='utf-8') as csvfile:
            fieldnames = ['bowler_nickname', 'location_name', 'event_date', 'game1_score', 'game2_score', 'game3_score', 'series_type']
            writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
            
            writer.writeheader()
            for bowler in bowlers:
                writer.writerow({
                    'bowler_nickname': bowler['name'],
                    'location_name': 'Unknown Location',
                    'event_date': match_info['date'],
                    'game1_score': bowler['game1_score'],
                    'game2_score': bowler['game2_score'],
                    'game3_score': bowler['game3_score'],
                    'series_type': 'Tour Stop'
                })
        
        print(f"Database import CSV created: {output_file}")
    
    def create_detailed_csv(self, bowlers: List[Dict], match_info: Dict, output_file: str = 'detailed_scores.csv'):
        """
        Create detailed CSV with all information.
        
        Args:
            bowlers: List of bowler dictionaries
            match_info: Match information dictionary
            output_file: Output CSV filename
        """
        with open(output_file, 'w', newline='', encoding='utf-8') as csvfile:
            fieldnames = [
                'match_date', 'team1', 'team2', 'match_type',
                'lane_number', 'bowler_name', 'average', 'handicap',
                'game1_score', 'game2_score', 'game3_score', 'total_score'
            ]
            writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
            
            writer.writeheader()
            for bowler in bowlers:
                writer.writerow({
                    'match_date': match_info['date'],
                    'team1': match_info['team1_name'],
                    'team2': match_info['team2_name'],
                    'match_type': match_info['match_type'],
                    'lane_number': bowler['lane_number'],
                    'bowler_name': bowler['name'],
                    'average': bowler['average'],
                    'handicap': bowler['handicap'],
                    'game1_score': bowler['game1_score'],
                    'game2_score': bowler['game2_score'],
                    'game3_score': bowler['game3_score'],
                    'total_score': bowler['total_score']
                })
        
        print(f"Detailed scores CSV created: {output_file}")
    
    def process_image(self, image_path: str, output_prefix: str = 'bowling'):
        """
        Process an image and create CSV files.
        
        Args:
            image_path: Path to the image file
            output_prefix: Prefix for output CSV files
        """
        print(f"Processing image: {image_path}")
        
        # Extract text from image
        text_lines = self.extract_text_from_image(image_path)
        if not text_lines:
            print("No text extracted from image.")
            return
        
        print(f"Extracted {len(text_lines)} text lines")
        
        # Parse bowler data
        bowlers = self.parse_bowler_data(text_lines)
        if not bowlers:
            print("No bowler data found in image.")
            return
        
        print(f"Found {len(bowlers)} bowlers")
        
        # Extract match information
        match_info = self.extract_match_info(text_lines)
        
        # Create CSV files
        self.create_bowlers_csv(bowlers, f"{output_prefix}_bowlers.csv")
        self.create_database_import_csv(bowlers, match_info, f"{output_prefix}_database_import.csv")
        self.create_detailed_csv(bowlers, match_info, f"{output_prefix}_detailed_scores.csv")
        
        # Print summary
        print(f"\n=== MATCH SUMMARY ===")
        print(f"Match: {match_info['team1_name']} vs {match_info['team2_name']}")
        print(f"Date: {match_info['date']}")
        print(f"Type: {match_info['match_type']}")
        print(f"Total Bowlers: {len(bowlers)}")
        
        # Find specific bowler if requested
        anthony = next((b for b in bowlers if 'ANTHONY ESCALONA' in b['name']), None)
        if anthony:
            print(f"\n=== ANTHONY ESCALONA ===")
            print(f"Lane: {anthony['lane_number']}")
            print(f"Average: {anthony['average']}")
            print(f"Handicap: {anthony['handicap']}")
            print(f"Game 1: {anthony['game1_score']}")
            print(f"Game 2: {anthony['game2_score']}")
            print(f"Game 3: {anthony['game3_score']}")
            print(f"Total: {anthony['total_score']}")
        
        # Save JSON for reference
        result = {
            'match_info': match_info,
            'bowlers': bowlers
        }
        
        with open(f"{output_prefix}_data.json", 'w') as f:
            json.dump(result, f, indent=2)
        
        print(f"\nJSON data saved: {output_prefix}_data.json")

def main():
    """Main function to run the extractor."""
    if len(sys.argv) < 2:
        print("Usage: python image_to_csv_extractor.py <image_path> [output_prefix]")
        print("Example: python image_to_csv_extractor.py bowling_sheet.jpg my_match")
        sys.exit(1)
    
    image_path = sys.argv[1]
    output_prefix = sys.argv[2] if len(sys.argv) > 2 else 'bowling'
    
    # Create extractor and process image
    extractor = BowlingImageExtractor()
    extractor.process_image(image_path, output_prefix)

if __name__ == "__main__":
    main()

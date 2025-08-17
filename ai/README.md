# Bowling Image to CSV Extractor

This tool extracts bowler information and scores from bowling sheet images using Amazon Textract and converts the data to CSV format.

## Features

- ✅ **Image Processing**: Uses Amazon Textract to extract text from bowling sheet images
- ✅ **Bowler Data Extraction**: Extracts names, averages, handicaps, and game scores
- ✅ **Multiple CSV Formats**: Creates different CSV files for different use cases
- ✅ **Match Information**: Extracts team names, dates, and match types
- ✅ **Database Ready**: Formats data for easy database import

## Files

- `image_to_csv_extractor.py` - Main extractor script
- `example_usage.py` - Example usage with sample data
- `bowler_extractor.py` - Core extraction logic
- `bowling_data_extractor.ipynb` - Jupyter notebook version

## Installation

1. Install required packages:
```bash
pip install boto3
```

2. Configure AWS credentials:
```bash
aws configure
```

## Usage

### With Real Image File

```bash
python image_to_csv_extractor.py your_bowling_image.jpg my_match
```

This will create:
- `my_match_bowlers.csv` - Basic bowler information
- `my_match_database_import.csv` - Formatted for database import
- `my_match_detailed_scores.csv` - Complete match details
- `my_match_data.json` - JSON data for reference

### With Sample Data (No Image Required)

```bash
python example_usage.py
```

This demonstrates the extractor using sample bowling data.

## CSV Output Formats

### 1. Bowlers CSV (`*_bowlers.csv`)
```
lane_number,name,average,handicap,game1_score,game2_score,game3_score,total_score
18,ANTHONY ESCALONA,220,8,185,180,223,588
```

### 2. Database Import CSV (`*_database_import.csv`)
```
bowler_nickname,location_name,event_date,game1_score,game2_score,game3_score,series_type
ANTHONY ESCALONA,Unknown Location,8/15/2025,185,180,223,Tour Stop
```

### 3. Detailed Scores CSV (`*_detailed_scores.csv`)
```
match_date,team1,team2,match_type,lane_number,bowler_name,average,handicap,game1_score,game2_score,game3_score,total_score
8/15/2025,THUNDERSTUCK,IF UNLIMITED,Scratch Pair,18,ANTHONY ESCALONA,220,8,185,180,223,588
```

## Example Output

### Anthony Escalona's Data:
- **Lane:** 18
- **Average:** 220
- **Handicap:** 8
- **Game 1:** 185
- **Game 2:** 180
- **Game 3:** 223
- **Total:** 588

### Match Summary:
- **Teams:** THUNDERSTUCK vs IF UNLIMITED
- **Date:** 8/15/2025
- **Type:** Scratch Pair
- **Total Bowlers:** 24

## Integration with Bowling Database

The extracted CSV files can be imported into your bowling database:

1. **Import bowlers** using `*_database_import.csv`
2. **Import game series** using the same file
3. **Update locations** as needed

## Requirements

- Python 3.7+
- boto3 (AWS SDK)
- AWS credentials configured
- Amazon Textract access

## Error Handling

The script includes error handling for:
- Missing image files
- Textract API errors
- Invalid data formats
- File permission issues

## Customization

You can modify the regex patterns in `parse_bowler_data()` to match different bowling sheet formats.

## Support

For issues or questions, check the error messages and ensure:
1. AWS credentials are properly configured
2. Image file exists and is readable
3. Image contains clear, readable bowling data

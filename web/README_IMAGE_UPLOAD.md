# Image Upload Admin Feature

## Overview

The Image Upload Admin page allows administrators to upload bowling sheet images and automatically extract bowler data using AI-powered OCR (Optical Character Recognition).

## Features

### ✅ **Image Upload**
- Supports JPEG, PNG, and GIF formats
- Automatic file validation and security checks
- Unique filename generation to prevent conflicts

### ✅ **AI Data Extraction**
- Uses Amazon Textract for OCR processing
- Extracts bowler names, scores, and match information
- Processes data using the Python image-to-CSV extractor

### ✅ **Data Display**
- Shows extracted data in a formatted table
- Displays statistics (total bowlers, total pins, average score, high series)
- Uses the same header format as `image_output_database_import.csv`

### ✅ **Database Integration**
- One-click import to bowling database
- Automatic bowler and location creation if they don't exist
- Transaction-based import with error handling

## Usage

### 1. **Access the Page**
Navigate to: `?page=image_upload_admin`

### 2. **Upload Image**
- Click "Choose File" and select a bowling sheet image
- Click "Upload & Extract Data"
- Wait for processing to complete

### 3. **Review Results**
- View extracted bowler data in the table
- Check statistics cards for match overview
- Verify data accuracy before importing

### 4. **Import to Database**
- Click "Import to Database" button
- System will automatically:
  - Create new bowlers if they don't exist
  - Create new locations if they don't exist
  - Import all game series data

## Data Format

The extracted data follows the same format as the database import CSV:

```csv
bowler_nickname,location_name,event_date,game1_score,game2_score,game3_score,series_type
ANTHONY ESCALONA,Unknown Location,8/15/2025,185,180,223,Tour Stop
```

## Technical Details

### **File Structure**
```
web/
├── pages/
│   └── image_upload_admin.php    # Main admin page
├── uploads/                      # Uploaded images directory
└── index.php                     # Updated with new navigation
```

### **Dependencies**
- Python script: `ai/image_to_csv_extractor.py`
- AWS Textract access (configured in Python script)
- PHP file upload capabilities
- Database connection (via `config.php`)

### **Security Features**
- File type validation
- File size limits
- Secure file naming
- SQL injection prevention
- XSS protection

## Error Handling

The system handles various error scenarios:
- Invalid file types
- Upload failures
- OCR processing errors
- Database import errors
- Missing dependencies

## Example Workflow

1. **Upload Image**: Admin uploads `bowling_sheet.jpg`
2. **Process**: Python script extracts data using Textract
3. **Display**: Results shown in web interface
4. **Import**: Data imported to database with automatic bowler/location creation
5. **Verify**: Data appears in main bowling application

## Integration

This feature integrates seamlessly with the existing bowling application:
- Uses same database schema
- Follows existing UI patterns
- Compatible with all existing views and reports
- Data immediately available in dashboard and other pages

## Troubleshooting

### **Common Issues**

1. **Upload Fails**
   - Check file type (JPEG, PNG, GIF only)
   - Verify file size (max 10MB)
   - Ensure uploads directory is writable

2. **OCR Processing Fails**
   - Verify AWS credentials are configured
   - Check image quality (clear, readable text)
   - Ensure Python script is accessible

3. **Database Import Fails**
   - Check database connection
   - Verify table permissions
   - Review error messages for specific issues

### **Debug Information**
The system provides detailed error messages and processing output to help troubleshoot issues.

## Future Enhancements

Potential improvements:
- Batch image processing
- Image quality validation
- OCR confidence scoring
- Manual data correction interface
- Export to additional formats

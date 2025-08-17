# Image Upload Admin Feature

## Overview
The Image Upload Admin feature allows administrators to upload bowling score sheet images and automatically extract bowler data using AI-powered OCR (Optical Character Recognition).

## Features
- **Image Upload**: Support for JPEG, PNG, and GIF formats up to 10MB
- **AI Data Extraction**: Uses Amazon Textract via Python script to extract bowler information
- **Data Display**: Shows extracted data in a formatted table with statistics
- **Database Import**: One-click import of extracted data to the bowling database
- **Auto Creation**: Automatically creates new bowlers and locations as needed

## File Structure
```
web/
├── pages/
│   └── image_upload_admin.php    # Main upload interface
├── uploads/                      # Upload directory (requires permissions)
└── README_IMAGE_UPLOAD.md        # This file

ai/
├── image_to_csv_extractor.py     # Python OCR script
└── example_usage.py              # Usage examples
```

## Setup Instructions

### 1. Upload Directory Permissions
**IMPORTANT**: The web server needs write permissions to the uploads directory.

On your Ubuntu server, run these commands:
```bash
# SSH to your server
ssh root@your-server-ip

# Navigate to project directory
cd /var/www/html/if-lab

# Fix ownership (web server user)
sudo chown -R www-data:www-data web/uploads/

# Fix permissions (readable/writable by web server)
sudo chmod -R 755 web/uploads/

# Verify the changes
ls -la web/uploads/
```

### 2. Python Dependencies
Ensure the Python script dependencies are installed:
```bash
# Install boto3 for AWS Textract
pip install boto3

# Or if using pip3
pip3 install boto3
```

### 3. AWS Configuration
The Python script requires AWS credentials for Textract:
```bash
# Configure AWS credentials
aws configure

# Or set environment variables
export AWS_ACCESS_KEY_ID=your_access_key
export AWS_SECRET_ACCESS_KEY=your_secret_key
export AWS_DEFAULT_REGION=us-east-1
```

## Usage

### 1. Access the Upload Page
Navigate to: `http://your-domain/bowling-db/?page=image_upload_admin`

### 2. Upload Image
1. Click "Choose File" and select a bowling score sheet image
2. Click "Upload & Extract Data"
3. Wait for processing (button will show "Processing...")

### 3. Review Data
- View extracted bowler information in the table
- Check statistics (total bowlers, pins, averages, high series)
- Verify data accuracy before importing

### 4. Import to Database
1. Click "Import to Database" button
2. System will automatically:
   - Create new bowlers if they don't exist
   - Create new locations if they don't exist
   - Insert game series data
   - Show import results

## Troubleshooting

### Upload Directory Issues
If you get "Failed to move uploaded file" error:

**Temporary Fix**: The code currently uses `/tmp/bowling_uploads/` as a workaround.

**Permanent Fix**: Run the permission commands above on your Ubuntu server.

### Python Script Issues
If the Python script fails:
1. Check if `boto3` is installed: `pip list | grep boto3`
2. Verify AWS credentials are configured
3. Check the command output in the debug comments

### Database Import Issues
If import fails:
1. Check database connection in `config.php`
2. Verify table structure matches expected format
3. Check for duplicate entries or constraint violations

## File Formats

### Supported Image Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)

### Generated CSV Format
The Python script generates a CSV with these columns:
- `bowler_nickname`: Bowler's name
- `location_name`: Bowling alley name
- `event_date`: Date of the event
- `game1_score`: Score for game 1
- `game2_score`: Score for game 2
- `game3_score`: Score for game 3
- `series_type`: Type of series (League, Tournament, etc.)

## Security Notes
- File uploads are validated for type and size
- Uploaded files are moved to a secure directory
- Database operations use prepared statements
- Error messages don't expose sensitive information

## Performance Notes
- Large images may take longer to process
- Python script execution time depends on image complexity
- Database import uses transactions for data integrity

## Future Enhancements
- Batch upload support
- Image preprocessing for better OCR accuracy
- Export functionality for processed data
- Integration with other bowling management systems

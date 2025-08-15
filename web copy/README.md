# Bowling Database Web Interface

A modern, responsive web interface for viewing and analyzing bowling database data using PHP and MySQL.

## Features

- 📊 **Dashboard** - Overview statistics and recent activity
- 👥 **Bowlers** - Complete bowler profiles with performance metrics
- 📋 **Series Details** - Detailed series information with filtering
- 🏢 **Locations** - Bowling alley statistics and performance
- ⏰ **Recent Performance** - Latest series and trends
- 🎳 **Game Details** - Individual game scores with ratings
- 🏆 **Tournaments** - Tournament-specific statistics

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Bowling database created and populated

## Installation

### 1. Database Setup

First, ensure your bowling database is created and populated:

```bash
# Create the database and tables
mysql -u root -p < sql/bowling_mysql.sql

# Create the views
mysql -u root -p < sql/views/create_all_views.sql
```

### 2. Web Interface Setup

1. **Copy files to web server:**
   ```bash
   # Copy the web directory to your web server
   cp -r web/ /var/www/html/bowling-db/
   # or
   cp -r web/ /opt/lampp/htdocs/bowling-db/
   ```

2. **Configure database connection:**
   ```bash
   # Edit the configuration file
   nano web/config.php
   ```
   
   Update these values:
   ```php
   $config = [
       'host' => 'localhost',
       'dbname' => 'bowling_db',
       'username' => 'your_mysql_username',
       'password' => 'your_mysql_password',
       'charset' => 'utf8mb4'
   ];
   ```

3. **Set permissions:**
   ```bash
   chmod 755 web/
   chmod 644 web/*.php
   chmod 644 web/pages/*.php
   ```

### 3. Access the Interface

Open your web browser and navigate to:
```
http://localhost/bowling-db/
```

## File Structure

```
web/
├── index.php              # Main application entry point
├── config.php             # Database configuration
├── README.md              # This file
└── pages/
    ├── dashboard.php      # Dashboard overview
    ├── bowlers.php        # Bowler management
    ├── series.php         # Series details
    ├── locations.php      # Location statistics
    ├── recent.php         # Recent performance
    ├── games.php          # Game details
    └── tournaments.php    # Tournament statistics
```

## Configuration Options

### Database Connection

Edit `config.php` to match your MySQL setup:

```php
$config = [
    'host' => 'localhost',        // MySQL host
    'dbname' => 'bowling_db',     // Database name
    'username' => 'root',         // MySQL username
    'password' => '',             // MySQL password
    'charset' => 'utf8mb4'        // Character encoding
];
```

### Security Considerations

1. **Use dedicated database user:**
   ```sql
   CREATE USER 'bowling_web'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT SELECT ON bowling_db.* TO 'bowling_web'@'localhost';
   ```

2. **Restrict file access:**
   ```bash
   chmod 600 web/config.php
   ```

3. **Use HTTPS in production**

## Usage

### Dashboard
- Overview statistics
- Recent series activity
- Top performers
- Quick navigation

### Bowlers
- Search and filter bowlers
- View performance metrics
- Sort by various criteria
- Export data (future feature)

### Series Details
- Complete series information
- Filter by date, location, type
- Game-by-game breakdown
- Performance trends

### Locations
- Bowling alley statistics
- Performance comparisons
- Bowler activity by location

### Recent Performance
- Last 10 series per bowler
- Performance trends
- Recent milestones

### Game Details
- Individual game scores
- Automatic game ratings
- Series context
- Performance analysis

### Tournaments
- Tournament-specific stats
- Performance comparisons
- Milestone tracking

## Customization

### Adding New Pages

1. Create a new PHP file in `pages/`
2. Add navigation link in `index.php`
3. Update the switch statement in `index.php`

### Styling

The interface uses Bootstrap 5 and custom CSS. To customize:

1. Edit the `<style>` section in `index.php`
2. Add custom CSS classes
3. Modify Bootstrap classes as needed

### Database Views

The interface uses the SQL views created in `sql/views/`. To add new views:

1. Create the view SQL file
2. Add it to `create_all_views.sql`
3. Create a corresponding web page

## Troubleshooting

### Common Issues

1. **Database Connection Error:**
   - Check database credentials in `config.php`
   - Ensure MySQL is running
   - Verify database exists

2. **Page Not Found:**
   - Check web server configuration
   - Verify file permissions
   - Ensure PHP is enabled

3. **Views Not Working:**
   - Run the view creation scripts
   - Check MySQL user permissions
   - Verify view syntax

### Debug Mode

To enable debug mode, add this to `config.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Performance Optimization

1. **Database Indexing:**
   ```sql
   CREATE INDEX idx_bowler_date ON game_series(bowler_id, event_date);
   CREATE INDEX idx_location_date ON game_series(location_id, event_date);
   ```

2. **Caching:**
   - Consider implementing Redis/Memcached
   - Cache frequently accessed data
   - Use browser caching for static assets

3. **Pagination:**
   - Implement pagination for large datasets
   - Use LIMIT and OFFSET in queries

## Future Enhancements

- [ ] Data export functionality
- [ ] Advanced filtering options
- [ ] Charts and graphs
- [ ] User authentication
- [ ] Data entry forms
- [ ] API endpoints
- [ ] Mobile app integration

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review MySQL error logs
3. Verify database connectivity
4. Test with sample data

## License

This project is open source and available under the MIT License.

# Bowling Database Web Application

A modern, responsive web application for viewing and analyzing bowling database statistics using existing SQL views.

## 🎯 Features

- **Modern UI**: Glassmorphism design with Bootstrap 5.3
- **Responsive**: Works on desktop, tablet, and mobile devices
- **Interactive Tables**: DataTables integration with sorting, searching, and pagination
- **Real-time Filtering**: Advanced filtering options on all pages
- **Data Visualization**: Charts and statistics cards
- **Color-coded Performance**: Visual indicators for scores and performance levels

## 📊 Pages & Views Used

### 1. Dashboard (`dashboard.php`)
- **Overview Statistics**: Total bowlers, locations, series, averages
- **Performance Highlights**: Highest series, 800+, 700+ series counts
- **Top Performers**: Uses `bowler_performance_summary` view
- **Series Distribution Chart**: Interactive doughnut chart
- **Recent Activity**: Latest series with filtering

### 2. Bowlers (`bowlers.php`)
- **Uses View**: `bowler_performance_summary`
- **Features**: 
  - Comprehensive bowler statistics
  - Advanced filtering (dexterity, style, average range)
  - Ranked performance display
  - Color-coded performance indicators

### 3. Series Details (`series.php`)
- **Uses View**: `series_details`
- **Features**:
  - Complete series information
  - Individual game scores (Game 1, 2, 3)
  - Advanced filtering (bowler, location, type, date, score range)
  - Series statistics and milestones

### 4. Locations (`locations.php`)
- **Uses View**: `location_statistics`
- **Features**:
  - Location performance rankings
  - Statistics per location
  - Unique bowlers and series counts
  - Performance metrics by venue

### 5. Recent Performance (`recent.php`)
- **Uses View**: `recent_performance`
- **Features**:
  - Last 10 series for each bowler
  - Recent trends and performance
  - Series ranking (1-10)
  - Filtering by bowler, location, type

### 6. Game Details (`games.php`)
- **Uses View**: `game_details`
- **Features**:
  - Individual game scores with context
  - Automatic game ratings (Perfect, Excellent, Good, etc.)
  - Series context and statistics
  - Advanced filtering options

### 7. Tournaments (`tournaments.php`)
- **Uses View**: `tournament_performance`
- **Features**:
  - Tournament-specific statistics
  - Performance by series type (Tour Stop, Playoffs)
  - Best/worst tournament series
  - Milestone tracking

## 🛠️ Technical Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL with PDO
- **Frontend**: Bootstrap 5.3, jQuery, DataTables, Chart.js
- **Design**: Glassmorphism with CSS3 gradients and backdrop filters
- **Icons**: Font Awesome 6.4

## 📁 File Structure

```
web/
├── index.php              # Main application entry point
├── config.php             # Database configuration and helper functions
├── README.md              # This documentation
└── pages/
    ├── dashboard.php      # Dashboard overview
    ├── bowlers.php        # Bowler statistics
    ├── series.php         # Series details
    ├── locations.php      # Location statistics
    ├── recent.php         # Recent performance
    ├── games.php          # Game details
    └── tournaments.php    # Tournament statistics
```

## 🔧 Configuration

### Database Setup
1. Update `config.php` with your database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'bowling_db';
   $username = 'your_username';
   $password = 'your_password';
   ```

2. Ensure all SQL views are created:
   ```bash
   mysql -u root -p < sql/views/create_all_views.sql
   ```

### Web Server Setup
1. Copy the `web/` directory to your web server:
   ```bash
   cp -r web/ /var/www/html/bowling-db/
   ```

2. Set proper permissions:
   ```bash
   chmod -R 755 /var/www/html/bowling-db/
   ```

3. Access the application at: `http://your-server/bowling-db/`

## 🎨 Design Features

### Color Coding
- **Perfect Games (300)**: Yellow/Warning
- **Excellent (250-299)**: Red/Danger
- **Good (200-249)**: Green/Success
- **Average (150-199)**: Blue/Info
- **Below Average (<150)**: Gray/Muted

### Performance Indicators
- **800+ Series**: Red badges
- **700+ Series**: Yellow badges
- **High Averages (250+)**: Red text
- **Good Averages (220-249)**: Yellow text
- **Average (200-219)**: Green text

### Interactive Elements
- **Hover Effects**: Cards and buttons with smooth transitions
- **Responsive Tables**: Sortable, searchable, paginated
- **Filter Forms**: Real-time filtering with clear options
- **Statistics Cards**: Visual representation of key metrics

## 🔍 Filtering Capabilities

Each page includes comprehensive filtering options:

- **Bowler**: Search by name, UBA ID, USBC ID
- **Location**: Filter by bowling center
- **Series Type**: Tour Stop, Playoffs, House History
- **Date Range**: From/To date filters
- **Score Ranges**: Min/Max score filters
- **Performance Metrics**: Average, total score ranges

## 📈 Data Visualization

- **Charts**: Interactive Chart.js visualizations
- **Statistics Cards**: Key metrics at a glance
- **Progress Indicators**: Visual performance indicators
- **Rankings**: Color-coded ranking badges

## 🚀 Performance Features

- **Optimized Queries**: Uses existing SQL views for efficient data retrieval
- **Lazy Loading**: DataTables with pagination
- **Responsive Design**: Mobile-first approach
- **Caching**: Browser-side caching for static assets

## 🔒 Security Features

- **SQL Injection Protection**: Prepared statements with PDO
- **XSS Prevention**: HTML escaping for all user input
- **Input Validation**: Server-side validation of all parameters
- **Error Handling**: Graceful error handling and user feedback

## 📱 Mobile Responsiveness

The application is fully responsive and works on:
- **Desktop**: Full feature set with side-by-side layout
- **Tablet**: Optimized layout with stacked elements
- **Mobile**: Single-column layout with touch-friendly controls

## 🎯 Usage Examples

### Viewing Top Performers
1. Navigate to "Bowlers" page
2. View automatically ranked bowlers by average
3. Use filters to narrow down by dexterity, style, or average range

### Analyzing Recent Performance
1. Go to "Recent Performance" page
2. Filter by specific bowler to see their last 10 series
3. View trends and performance patterns

### Tournament Analysis
1. Visit "Tournaments" page
2. Filter by series type (Tour Stop vs Playoffs)
3. Compare performance across different tournament types

### Location Performance
1. Check "Locations" page
2. See which venues produce the best scores
3. Analyze bowler distribution across locations

## 🔧 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials in `config.php`
   - Ensure MySQL service is running
   - Check database permissions

2. **Views Not Found**
   - Run `sql/views/create_all_views.sql`
   - Verify view names match exactly

3. **No Data Displayed**
   - Check if database has data
   - Verify view queries return results
   - Check browser console for JavaScript errors

4. **Styling Issues**
   - Ensure all CSS/JS files are loading
   - Check for CDN connectivity issues
   - Verify Bootstrap and Font Awesome are accessible

## 📞 Support

For issues or questions:
1. Check the troubleshooting section above
2. Verify database connectivity and view existence
3. Review browser console for JavaScript errors
4. Ensure all required files are present and accessible

---

**Built with ❤️ for bowling enthusiasts and data analysts**

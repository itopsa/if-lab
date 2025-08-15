# Bowling Database Views

This directory contains SQL views for the bowling database that provide easy access to common queries and reports.

## Files

### Individual View Files
- `bowler_performance_summary.sql` - Overall statistics for each bowler
- `series_details.sql` - Complete series information with bowler and location details
- `location_statistics.sql` - Performance statistics for each bowling location
- `recent_performance.sql` - Last 10 series for each bowler
- `game_details.sql` - Individual game scores with context and ratings
- `tournament_performance.sql` - Tournament-specific statistics

### Master Files
- `create_all_views.sql` - Creates all views at once

## How to Use

### Option 1: Create All Views at Once
```bash
mysql -u root -p < sql/views/create_all_views.sql
```

### Option 2: Create Individual Views
```bash
mysql -u root -p < sql/views/bowler_performance_summary.sql
mysql -u root -p < sql/views/series_details.sql
# ... etc
```

### Option 3: Via phpMyAdmin
1. Open phpMyAdmin
2. Select the `bowling_db` database
3. Go to SQL tab
4. Copy and paste the content of any view file
5. Click "Go" to execute

## View Descriptions

### 1. bowler_performance_summary
**Purpose:** Overall statistics for each bowler
**Key Columns:**
- `total_series` - Number of series bowled
- `overall_average` - Average score across all series
- `highest_series` / `lowest_series` - Best and worst series scores
- `series_700_plus` / `series_800_plus` - Count of milestone series

**Example Query:**
```sql
SELECT * FROM bowler_performance_summary WHERE overall_average > 200;
```

### 2. series_details
**Purpose:** Complete series information with context
**Key Columns:**
- All bowler information (name, UBA ID, dexterity, style)
- All location information (name, city, state)
- All series information (scores, totals, averages)
- Sorted by most recent first

**Example Query:**
```sql
SELECT * FROM series_details WHERE series_type = 'Tour Stop';
```

### 3. location_statistics
**Purpose:** Performance statistics for each bowling location
**Key Columns:**
- `unique_bowlers` - Number of different bowlers at this location
- `total_series` - Total series bowled at this location
- `avg_series_score` - Average series score at this location
- Milestone counts (700+, 800+ series)

**Example Query:**
```sql
SELECT * FROM location_statistics ORDER BY avg_series_score DESC;
```

### 4. recent_performance
**Purpose:** Last 10 series for each bowler
**Key Columns:**
- Recent series data with ranking
- Shows trends and recent performance
- `series_rank` - Position (1-10) with 1 being most recent

**Example Query:**
```sql
SELECT * FROM recent_performance WHERE nickname = 'ZHANDOW, BALL REP';
```

### 5. game_details
**Purpose:** Individual game scores with context and ratings
**Key Columns:**
- Individual game scores with series context
- `game_rating` - Automatic rating (Perfect, Excellent, Good, Average, Below Average)
- Series totals and averages included

**Example Query:**
```sql
SELECT * FROM game_details WHERE game_rating = 'Perfect Game';
```

### 6. tournament_performance
**Purpose:** Tournament-specific statistics
**Key Columns:**
- Separates Tour Stop and Playoff performance
- Tournament-specific averages and milestones
- Best and worst tournament series

**Example Query:**
```sql
SELECT * FROM tournament_performance WHERE series_type = 'Tour Stop';
```

## Common Use Cases

### Performance Analysis
```sql
-- Get top performers
SELECT * FROM bowler_performance_summary 
ORDER BY overall_average DESC 
LIMIT 10;
```

### Location Comparison
```sql
-- Compare locations by average scores
SELECT * FROM location_statistics 
ORDER BY avg_series_score DESC;
```

### Recent Trends
```sql
-- Get recent performance for a specific bowler
SELECT * FROM recent_performance 
WHERE nickname = 'ZHANDOW, BALL REP' 
AND series_rank <= 5;
```

### Tournament Analysis
```sql
-- Compare tournament vs regular performance
SELECT * FROM tournament_performance 
WHERE tournaments_played > 5;
```

## Notes

- All views use `CREATE OR REPLACE VIEW` so they can be safely re-run
- Views automatically include `USE bowling_db;` statement
- Views are optimized for common query patterns
- Use these views as building blocks for more complex reports

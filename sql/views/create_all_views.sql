-- Master script to create all bowling database views
-- Run this after the main database schema is created

USE bowling_db;

-- Create all views
SOURCE sql/views/bowler_performance_summary.sql;
SOURCE sql/views/series_details.sql;
SOURCE sql/views/location_statistics.sql;
SOURCE sql/views/recent_performance.sql;
SOURCE sql/views/game_details.sql;
SOURCE sql/views/tournament_performance.sql;

-- Verify all views were created
SHOW TABLES LIKE '%view%';

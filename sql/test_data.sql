-- Test script to verify data and views
USE bowling_db;

-- Check basic counts
SELECT '=== BASIC COUNTS ===' as info;
SELECT COUNT(*) as total_bowlers FROM bowlers;
SELECT COUNT(*) as total_locations FROM locations;
SELECT COUNT(*) as total_series FROM game_series;

-- Check series types
SELECT '=== SERIES TYPES ===' as info;
SELECT series_type, COUNT(*) as count 
FROM game_series 
GROUP BY series_type 
ORDER BY count DESC;

-- Check recent data
SELECT '=== RECENT DATA ===' as info;
SELECT 
    b.nickname,
    l.name as location,
    gs.series_type,
    gs.event_date,
    gs.total_score,
    gs.average_score
FROM game_series gs
JOIN bowlers b ON gs.bowler_id = b.bowler_id
LEFT JOIN locations l ON gs.location_id = l.location_id
ORDER BY gs.event_date DESC
LIMIT 10;

-- Test recent_performance view
SELECT '=== RECENT PERFORMANCE VIEW ===' as info;
SELECT COUNT(*) as total_records FROM recent_performance;

-- Show sample from recent_performance
SELECT 
    nickname,
    location,
    series_type,
    event_date,
    total_score,
    series_rank
FROM recent_performance
ORDER BY event_date DESC, series_rank ASC
LIMIT 10;

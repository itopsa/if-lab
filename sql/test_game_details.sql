-- Test script to check game_details view and diagnose filtering issues
USE bowling_db;

-- Check if games table has data
SELECT '=== GAMES TABLE CHECK ===' as info;
SELECT COUNT(*) as total_games FROM games;
SELECT COUNT(*) as total_series FROM game_series;

-- Check if game_details view has data
SELECT '=== GAME_DETAILS VIEW CHECK ===' as info;
SELECT COUNT(*) as total_records FROM game_details;

-- Check sample data from game_details
SELECT '=== SAMPLE GAME_DETAILS DATA ===' as info;
SELECT 
    bowler_name,
    location_name,
    series_type,
    game_number,
    score,
    game_rating
FROM game_details 
LIMIT 10;

-- Check if we need to populate games table
SELECT '=== CHECKING IF GAMES TABLE NEEDS POPULATION ===' as info;
SELECT 
    COUNT(*) as total_series_with_games,
    SUM(CASE WHEN game1_score > 0 THEN 1 ELSE 0 END) as game1_count,
    SUM(CASE WHEN game2_score > 0 THEN 1 ELSE 0 END) as game2_count,
    SUM(CASE WHEN game3_score > 0 THEN 1 ELSE 0 END) as game3_count
FROM game_series;

-- Test filtering on game_details view
SELECT '=== TESTING FILTERS ON GAME_DETAILS ===' as info;
SELECT COUNT(*) as filtered_count 
FROM game_details 
WHERE bowler_name LIKE '%AJ CHAPMAN%';

-- Populate the games table from game_series data
-- This script extracts individual game scores from the game_series table
-- and inserts them into the games table for detailed analysis

USE bowling_db;

-- Clear existing games data first
DELETE FROM games;
INSERT INTO games (series_id, game_number, score)
SELECT 
    series_id,
    1 as game_number,
    game1_score as score
FROM game_series 
WHERE game1_score > 0

UNION ALL

SELECT 
    series_id,
    2 as game_number,
    game2_score as score
FROM game_series 
WHERE game2_score > 0

UNION ALL

SELECT 
    series_id,
    3 as game_number,
    game3_score as score
FROM game_series 
WHERE game3_score > 0

ORDER BY series_id, game_number;

-- Display summary
SELECT 'Games table populated successfully!' as status;
SELECT COUNT(*) as total_games FROM games;
SELECT 
    COUNT(*) as total_series,
    SUM(CASE WHEN game1_score > 0 THEN 1 ELSE 0 END) as game1_count,
    SUM(CASE WHEN game2_score > 0 THEN 1 ELSE 0 END) as game2_count,
    SUM(CASE WHEN game3_score > 0 THEN 1 ELSE 0 END) as game3_count
FROM game_series;

-- View: Location Statistics
-- Description: Performance statistics for each bowling location

USE bowling_db;

CREATE OR REPLACE VIEW location_statistics AS
SELECT 
    l.location_id,
    l.name AS location_name,
    l.city,
    l.state,
    COUNT(DISTINCT gs.bowler_id) AS unique_bowlers,
    COUNT(gs.series_id) AS total_series,
    AVG(gs.average_score) AS avg_series_score,
    MAX(gs.total_score) AS highest_series,
    MIN(gs.total_score) AS lowest_series,
    COUNT(CASE WHEN gs.total_score >= 700 THEN 1 END) AS series_700_plus,
    COUNT(CASE WHEN gs.total_score >= 800 THEN 1 END) AS series_800_plus
FROM locations l
LEFT JOIN game_series gs ON l.location_id = gs.location_id
GROUP BY l.location_id, l.name, l.city, l.state;

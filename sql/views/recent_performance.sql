-- View: Recent Performance
-- Description: Last 10 series for each bowler showing recent trends

USE bowling_db;

CREATE OR REPLACE VIEW recent_performance AS
SELECT 
    b.nickname,
    l.name AS location,
    gs.series_type,
    gs.event_date,
    gs.total_score,
    gs.average_score,
    gs.game1_score,
    gs.game2_score,
    gs.game3_score,
    ROW_NUMBER() OVER (PARTITION BY b.bowler_id ORDER BY gs.event_date DESC) AS series_rank
FROM game_series gs
JOIN bowlers b ON gs.bowler_id = b.bowler_id
LEFT JOIN locations l ON gs.location_id = l.location_id
HAVING series_rank <= 10
ORDER BY b.nickname, gs.event_date DESC;

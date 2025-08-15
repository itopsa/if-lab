-- View: Game Details
-- Description: Individual game scores with context and ratings

USE bowling_db;

CREATE OR REPLACE VIEW game_details AS
SELECT 
    g.game_id,
    g.series_id,
    b.nickname AS bowler_name,
    l.name AS location_name,
    gs.series_type,
    gs.event_date,
    g.game_number,
    g.score,
    gs.total_score AS series_total,
    gs.average_score AS series_average,
    CASE 
        WHEN g.score >= 300 THEN 'Perfect Game'
        WHEN g.score >= 250 THEN 'Excellent'
        WHEN g.score >= 200 THEN 'Good'
        WHEN g.score >= 150 THEN 'Average'
        ELSE 'Below Average'
    END AS game_rating
FROM games g
JOIN game_series gs ON g.series_id = gs.series_id
JOIN bowlers b ON gs.bowler_id = b.bowler_id
LEFT JOIN locations l ON gs.location_id = l.location_id
ORDER BY gs.event_date DESC, g.series_id, g.game_number;

-- View: Series Details
-- Description: Complete series information with bowler and location details

USE bowling_db;

CREATE OR REPLACE VIEW series_details AS
SELECT 
    gs.series_id,
    b.nickname AS bowler_name,
    b.uba_id,
    b.dexterity,
    b.style,
    l.name AS location_name,
    l.city,
    l.state,
    gs.series_type,
    gs.event_date,
    gs.round_name,
    gs.game1_score,
    gs.game2_score,
    gs.game3_score,
    gs.total_score,
    gs.average_score,
    gs.created_at
FROM game_series gs
JOIN bowlers b ON gs.bowler_id = b.bowler_id
LEFT JOIN locations l ON gs.location_id = l.location_id
ORDER BY gs.event_date DESC, gs.series_id DESC;

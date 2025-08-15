-- View: Bowler Performance Summary
-- Description: Overall statistics for each bowler including totals, averages, and milestone counts

USE bowling_db;

CREATE OR REPLACE VIEW bowler_performance_summary AS
SELECT 
    b.bowler_id,
    b.nickname,
    b.uba_id,
    b.usbc_id,
    b.dexterity,
    b.style,
    l.name AS home_house,
    COUNT(gs.series_id) AS total_series,
    AVG(gs.average_score) AS overall_average,
    MAX(gs.total_score) AS highest_series,
    MIN(gs.total_score) AS lowest_series,
    COUNT(CASE WHEN gs.total_score >= 700 THEN 1 END) AS series_700_plus,
    COUNT(CASE WHEN gs.total_score >= 800 THEN 1 END) AS series_800_plus
FROM bowlers b
LEFT JOIN game_series gs ON b.bowler_id = gs.bowler_id
LEFT JOIN locations l ON b.home_house_id = l.location_id
GROUP BY b.bowler_id, b.nickname, b.uba_id, b.usbc_id, b.dexterity, b.style, l.name;

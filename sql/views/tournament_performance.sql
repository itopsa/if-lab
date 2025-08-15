-- View: Tournament Performance
-- Description: Tournament-specific statistics for Tour Stop and Playoff events

USE bowling_db;

CREATE OR REPLACE VIEW tournament_performance AS
SELECT 
    b.nickname,
    gs.series_type,
    COUNT(gs.series_id) AS tournaments_played,
    AVG(gs.average_score) AS avg_tournament_score,
    MAX(gs.total_score) AS best_tournament_series,
    MIN(gs.total_score) AS worst_tournament_series,
    SUM(CASE WHEN gs.total_score >= 700 THEN 1 ELSE 0 END) AS series_700_plus,
    SUM(CASE WHEN gs.total_score >= 800 THEN 1 ELSE 0 END) AS series_800_plus
FROM game_series gs
JOIN bowlers b ON gs.bowler_id = b.bowler_id
WHERE gs.series_type IN ('Tour Stop', 'Playoffs')
GROUP BY b.bowler_id, b.nickname, gs.series_type
ORDER BY b.nickname, gs.series_type;

-- Extended Seed Data for Bowling Database
-- Additional tournaments, series, and diverse data

USE bowling_db;

-- Insert additional locations
INSERT IGNORE INTO locations (name, city, state) VALUES
('Strike Force Lanes', 'Phoenix', 'AZ'),
('Pin Palace Pro', 'Denver', 'CO'),
('Bowl America', 'Baltimore', 'MD'),
('AMF University Lanes', 'Austin', 'TX'),
('Brunswick Zone', 'Seattle', 'WA'),
('Lucky Strike Social', 'San Diego', 'CA'),
('Pinstack Entertainment', 'Dallas', 'TX'),
('Kings Bowl Entertainment', 'Atlanta', 'GA'),
('Splitsville Luxury Lanes', 'Orlando', 'FL'),
('Round1 Bowling & Amusement', 'Chicago', 'IL');

-- Insert additional tournament series for more variety
INSERT INTO game_series (bowler_id, location_id, series_type, event_date, round_name, game1_score, game2_score, game3_score) VALUES
-- Additional Jason Belmonte Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE'), (SELECT location_id FROM locations WHERE name = 'Strike Force Lanes'), 'Tour Stop', '2024-01-25', 'Qualifying', 289, 245, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE'), (SELECT location_id FROM locations WHERE name = 'Strike Force Lanes'), 'Tour Stop', '2024-01-25', 'Match Play', 245, 278, 256),
((SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE'), (SELECT location_id FROM locations WHERE name = 'Strike Force Lanes'), 'Playoffs', '2024-01-26', 'Round 1', 267, 289, 245),

-- Additional EJ Tackett Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT'), (SELECT location_id FROM locations WHERE name = 'Pin Palace Pro'), 'Tour Stop', '2024-02-01', 'Qualifying', 245, 289, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT'), (SELECT location_id FROM locations WHERE name = 'Pin Palace Pro'), 'Tour Stop', '2024-02-01', 'Match Play', 278, 245, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT'), (SELECT location_id FROM locations WHERE name = 'Pin Palace Pro'), 'Playoffs', '2024-02-02', 'Round 1', 256, 267, 289),

-- Additional Anthony Simonsen Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN'), (SELECT location_id FROM locations WHERE name = 'Bowl America'), 'Tour Stop', '2024-02-08', 'Qualifying', 267, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN'), (SELECT location_id FROM locations WHERE name = 'Bowl America'), 'Tour Stop', '2024-02-08', 'Match Play', 245, 278, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN'), (SELECT location_id FROM locations WHERE name = 'Bowl America'), 'Playoffs', '2024-02-09', 'Round 1', 289, 245, 278),

-- Additional Chris Via Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA'), (SELECT location_id FROM locations WHERE name = 'AMF University Lanes'), 'Tour Stop', '2024-02-15', 'Qualifying', 278, 245, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA'), (SELECT location_id FROM locations WHERE name = 'AMF University Lanes'), 'Tour Stop', '2024-02-15', 'Match Play', 267, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA'), (SELECT location_id FROM locations WHERE name = 'AMF University Lanes'), 'Playoffs', '2024-02-16', 'Round 1', 245, 278, 267),

-- Additional Kyle Troup Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP'), (SELECT location_id FROM locations WHERE name = 'Brunswick Zone'), 'Tour Stop', '2024-02-22', 'Qualifying', 289, 245, 278),
((SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP'), (SELECT location_id FROM locations WHERE name = 'Brunswick Zone'), 'Tour Stop', '2024-02-22', 'Match Play', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP'), (SELECT location_id FROM locations WHERE name = 'Brunswick Zone'), 'Playoffs', '2024-02-23', 'Round 1', 278, 245, 267),

-- Additional Jakob Butturff Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'JAKOB BUTTURFF'), (SELECT location_id FROM locations WHERE name = 'Lucky Strike Social'), 'Tour Stop', '2024-03-01', 'Qualifying', 267, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'JAKOB BUTTURFF'), (SELECT location_id FROM locations WHERE name = 'Lucky Strike Social'), 'Tour Stop', '2024-03-01', 'Match Play', 245, 278, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'JAKOB BUTTURFF'), (SELECT location_id FROM locations WHERE name = 'Lucky Strike Social'), 'Playoffs', '2024-03-02', 'Round 1', 289, 245, 278),

-- Additional Dom Barrett Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'DOM BARRETT'), (SELECT location_id FROM locations WHERE name = 'Pinstack Entertainment'), 'Tour Stop', '2024-03-08', 'Qualifying', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'DOM BARRETT'), (SELECT location_id FROM locations WHERE name = 'Pinstack Entertainment'), 'Tour Stop', '2024-03-08', 'Match Play', 278, 245, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'DOM BARRETT'), (SELECT location_id FROM locations WHERE name = 'Pinstack Entertainment'), 'Playoffs', '2024-03-09', 'Round 1', 267, 289, 245),

-- Additional Francois Lavoie Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'FRANCOIS LAVOIE'), (SELECT location_id FROM locations WHERE name = 'Kings Bowl Entertainment'), 'Tour Stop', '2024-03-15', 'Qualifying', 289, 245, 278),
((SELECT bowler_id FROM bowlers WHERE nickname = 'FRANCOIS LAVOIE'), (SELECT location_id FROM locations WHERE name = 'Kings Bowl Entertainment'), 'Tour Stop', '2024-03-15', 'Match Play', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'FRANCOIS LAVOIE'), (SELECT location_id FROM locations WHERE name = 'Kings Bowl Entertainment'), 'Playoffs', '2024-03-16', 'Round 1', 278, 245, 267),

-- Additional Tommy Jones Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'TOMMY JONES'), (SELECT location_id FROM locations WHERE name = 'Splitsville Luxury Lanes'), 'Tour Stop', '2024-03-22', 'Qualifying', 267, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'TOMMY JONES'), (SELECT location_id FROM locations WHERE name = 'Splitsville Luxury Lanes'), 'Tour Stop', '2024-03-22', 'Match Play', 245, 278, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'TOMMY JONES'), (SELECT location_id FROM locations WHERE name = 'Splitsville Luxury Lanes'), 'Playoffs', '2024-03-23', 'Round 1', 289, 245, 278),

-- Additional Wes Malott Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'WES MALOTT'), (SELECT location_id FROM locations WHERE name = 'Round1 Bowling & Amusement'), 'Tour Stop', '2024-03-29', 'Qualifying', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'WES MALOTT'), (SELECT location_id FROM locations WHERE name = 'Round1 Bowling & Amusement'), 'Tour Stop', '2024-03-29', 'Match Play', 278, 245, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'WES MALOTT'), (SELECT location_id FROM locations WHERE name = 'Round1 Bowling & Amusement'), 'Playoffs', '2024-03-30', 'Round 1', 267, 289, 245);

-- Insert additional House History Series (Regular League Play)
INSERT INTO game_series (bowler_id, location_id, series_type, event_date, round_name, game1_score, game2_score, game3_score) VALUES
-- Additional league play for various bowlers
((SELECT bowler_id FROM bowlers WHERE nickname = 'SEAN RASH'), (SELECT location_id FROM locations WHERE name = 'Strike Force Lanes'), 'House History', '2024-01-29', 'League Night', 256, 234, 278),
((SELECT bowler_id FROM bowlers WHERE nickname = 'MARSHALL KENT'), (SELECT location_id FROM locations WHERE name = 'Pin Palace Pro'), 'House History', '2024-01-30', 'League Night', 245, 289, 234),
((SELECT bowler_id FROM bowlers WHERE nickname = 'AJ CHAPMAN'), (SELECT location_id FROM locations WHERE name = 'Bowl America'), 'House History', '2024-01-31', 'League Night', 267, 245, 256),
((SELECT bowler_id FROM bowlers WHERE nickname = 'BILL O\'NEILL'), (SELECT location_id FROM locations WHERE name = 'AMF University Lanes'), 'House History', '2024-02-01', 'League Night', 234, 278, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS BARNES'), (SELECT location_id FROM locations WHERE name = 'Brunswick Zone'), 'House History', '2024-02-02', 'League Night', 256, 234, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'RYAN SHAFER'), (SELECT location_id FROM locations WHERE name = 'Lucky Strike Social'), 'House History', '2024-02-05', 'League Night', 245, 289, 234),
((SELECT bowler_id FROM bowlers WHERE nickname = 'PETE WEBER'), (SELECT location_id FROM locations WHERE name = 'Pinstack Entertainment'), 'House History', '2024-02-06', 'League Night', 267, 245, 256),
((SELECT bowler_id FROM bowlers WHERE nickname = 'WALTER RAY WILLIAMS JR'), (SELECT location_id FROM locations WHERE name = 'Kings Bowl Entertainment'), 'House History', '2024-02-07', 'League Night', 234, 278, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'NORM DUKE'), (SELECT location_id FROM locations WHERE name = 'Splitsville Luxury Lanes'), 'House History', '2024-02-08', 'League Night', 256, 234, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'PARKER BOHN III'), (SELECT location_id FROM locations WHERE name = 'Round1 Bowling & Amusement'), 'House History', '2024-02-09', 'League Night', 245, 289, 234),
((SELECT bowler_id FROM bowlers WHERE nickname = 'DANNY WISEMAN'), (SELECT location_id FROM locations WHERE name = 'Strike Force Lanes'), 'House History', '2024-02-12', 'League Night', 267, 245, 256),
((SELECT bowler_id FROM bowlers WHERE nickname = 'AMLEETO MONACELLI'), (SELECT location_id FROM locations WHERE name = 'Pin Palace Pro'), 'House History', '2024-02-13', 'League Night', 234, 278, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'MIKE FAGAN'), (SELECT location_id FROM locations WHERE name = 'Bowl America'), 'House History', '2024-02-14', 'League Night', 256, 234, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'OSCAR RODRIGUEZ'), (SELECT location_id FROM locations WHERE name = 'AMF University Lanes'), 'House History', '2024-02-15', 'League Night', 245, 289, 234),
((SELECT bowler_id FROM bowlers WHERE nickname = 'GREG YOUNG'), (SELECT location_id FROM locations WHERE name = 'Brunswick Zone'), 'House History', '2024-02-16', 'League Night', 267, 245, 256);

-- Insert some perfect games and high scores for variety
INSERT INTO game_series (bowler_id, location_id, series_type, event_date, round_name, game1_score, game2_score, game3_score) VALUES
-- Perfect game series
((SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE'), (SELECT location_id FROM locations WHERE name = 'Strike Force Lanes'), 'Tour Stop', '2024-04-01', 'Championship', 300, 245, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT'), (SELECT location_id FROM locations WHERE name = 'Pin Palace Pro'), 'Tour Stop', '2024-04-05', 'Championship', 245, 300, 278),
((SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN'), (SELECT location_id FROM locations WHERE name = 'Bowl America'), 'Tour Stop', '2024-04-10', 'Championship', 267, 289, 300),
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA'), (SELECT location_id FROM locations WHERE name = 'AMF University Lanes'), 'Tour Stop', '2024-04-15', 'Championship', 300, 245, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP'), (SELECT location_id FROM locations WHERE name = 'Brunswick Zone'), 'Tour Stop', '2024-04-20', 'Championship', 245, 300, 267);

-- Insert some very high series for excitement
INSERT INTO game_series (bowler_id, location_id, series_type, event_date, round_name, game1_score, game2_score, game3_score) VALUES
-- High series
((SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE'), (SELECT location_id FROM locations WHERE name = 'Strike Force Lanes'), 'Tour Stop', '2024-05-01', 'Finals', 289, 298, 295),
((SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT'), (SELECT location_id FROM locations WHERE name = 'Pin Palace Pro'), 'Tour Stop', '2024-05-05', 'Finals', 296, 287, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN'), (SELECT location_id FROM locations WHERE name = 'Bowl America'), 'Tour Stop', '2024-05-10', 'Finals', 285, 299, 288),
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA'), (SELECT location_id FROM locations WHERE name = 'AMF University Lanes'), 'Tour Stop', '2024-05-15', 'Finals', 292, 284, 297),
((SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP'), (SELECT location_id FROM locations WHERE name = 'Brunswick Zone'), 'Tour Stop', '2024-05-20', 'Finals', 288, 295, 291);

-- Display summary
SELECT 'Extended Seed Data Inserted Successfully!' as status;
SELECT COUNT(*) as total_bowlers FROM bowlers;
SELECT COUNT(*) as total_locations FROM locations;
SELECT COUNT(*) as total_series FROM game_series;
SELECT 
    series_type,
    COUNT(*) as count
FROM game_series 
GROUP BY series_type;

-- Seed Data for Bowling Database
-- 30 Professional Bowlers with Tournament Data

USE bowling_db;

-- Clear existing data (optional)
-- DELETE FROM games;
-- DELETE FROM game_series;
-- DELETE FROM bowlers;
-- DELETE FROM locations;

-- Insert Professional Bowling Locations
INSERT INTO locations (name, city, state) VALUES
('Lodi Lanes', 'Lodi', 'NJ'),
('Parkway Lanes', 'Elmwood Park', 'NJ'),
('Battle Bowl', 'Wayne', 'NJ'),
('Bowlmor Lanes', 'New York', 'NY'),
('AMF Bowling Center', 'Brooklyn', 'NY'),
('Strike Zone', 'Philadelphia', 'PA'),
('Pin Palace', 'Boston', 'MA'),
('Lucky Strike', 'Chicago', 'IL'),
('Bowlero', 'Los Angeles', 'CA'),
('Main Event', 'Dallas', 'TX'),
('Round1', 'Las Vegas', 'NV'),
('Dave & Busters', 'Miami', 'FL'),
('Pinstack', 'Houston', 'TX'),
('Kings Bowl', 'San Francisco', 'CA'),
('Splitsville', 'Nashville', 'TN');

-- Insert 30 Professional Bowlers
INSERT INTO bowlers (nickname, uba_id, usbc_id, dexterity, style, home_house_id) VALUES
('JASON BELMONTE', 'AUS001', 'AUS-001', 'Right', '2 Handed', (SELECT location_id FROM locations WHERE name = 'Bowlero')),
('EJ TACKETT', 'USA001', 'USA-001', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'AMF Bowling Center')),
('ANTHONY SIMONSEN', 'USA002', 'USA-002', 'Right', '2 Handed', (SELECT location_id FROM locations WHERE name = 'Strike Zone')),
('CHRIS VIA', 'USA003', 'USA-003', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Pin Palace')),
('KYLE TROUP', 'USA004', 'USA-004', 'Right', '2 Handed', (SELECT location_id FROM locations WHERE name = 'Lucky Strike')),
('JAKOB BUTTURFF', 'USA005', 'USA-005', 'Left', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Main Event')),
('DOM BARRETT', 'ENG001', 'ENG-001', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Round1')),
('FRANCOIS LAVOIE', 'CAN001', 'CAN-001', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Dave & Busters')),
('TOMMY JONES', 'USA006', 'USA-006', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Pinstack')),
('WES MALOTT', 'USA007', 'USA-007', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Kings Bowl')),
('SEAN RASH', 'USA008', 'USA-008', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Splitsville')),
('MARSHALL KENT', 'USA009', 'USA-009', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Lodi Lanes')),
('AJ CHAPMAN', 'USA010', 'USA-010', 'Right', '2 Handed', (SELECT location_id FROM locations WHERE name = 'Parkway Lanes')),
('BILL O\'NEILL', 'USA011', 'USA-011', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Battle Bowl')),
('CHRIS BARNES', 'USA012', 'USA-012', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Bowlmor Lanes')),
('RYAN SHAFER', 'USA013', 'USA-013', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'AMF Bowling Center')),
('PETE WEBER', 'USA014', 'USA-014', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Strike Zone')),
('WALTER RAY WILLIAMS JR', 'USA015', 'USA-015', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Pin Palace')),
('NORM DUKE', 'USA016', 'USA-016', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Lucky Strike')),
('PARKER BOHN III', 'USA017', 'USA-017', 'Left', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Main Event')),
('DANNY WISEMAN', 'USA018', 'USA-018', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Round1')),
('AMLEETO MONACELLI', 'VEN001', 'VEN-001', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Dave & Busters')),
('MIKE FAGAN', 'USA019', 'USA-019', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Pinstack')),
('OSCAR RODRIGUEZ', 'USA020', 'USA-020', 'Right', '2 Handed', (SELECT location_id FROM locations WHERE name = 'Kings Bowl')),
('GREG YOUNG', 'USA021', 'USA-021', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Splitsville')),
('RYAN CIMMINO', 'USA022', 'USA-022', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Lodi Lanes')),
('DARREN TANG', 'USA023', 'USA-023', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Parkway Lanes')),
('ANDREW ANDERSON', 'USA024', 'USA-024', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Battle Bowl')),
('KRISTIJAN PRIVADELJ', 'SLO001', 'SLO-001', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Bowlmor Lanes')),
('THOMAS LARSEN', 'DEN001', 'DEN-001', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'AMF Bowling Center')),
('MARTIN LARSEN', 'SWE001', 'SWE-001', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Strike Zone')),
('JESPER SVENSSON', 'SWE002', 'SWE-002', 'Right', '2 Handed', (SELECT location_id FROM locations WHERE name = 'Pin Palace')),
('JASON STERNER', 'USA025', 'USA-025', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Lucky Strike')),
('SHAWN MALDONADO', 'USA026', 'USA-026', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Main Event')),
('GREG OSTRANDER', 'USA027', 'USA-027', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Round1')),
('JOHN JANAWICZ', 'USA028', 'USA-028', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Dave & Busters')),
('MATT SOUZA', 'USA029', 'USA-029', 'Right', '1 Handed', (SELECT location_id FROM locations WHERE name = 'Pinstack')),
('TYLER JENSEN', 'USA030', 'USA-030', 'Right', '2 Handed', (SELECT location_id FROM locations WHERE name = 'Kings Bowl'));

-- Insert Tournament Series (Tour Stops and Playoffs)
INSERT INTO game_series (bowler_id, location_id, series_type, event_date, round_name, game1_score, game2_score, game3_score) VALUES
-- Jason Belmonte Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE'), (SELECT location_id FROM locations WHERE name = 'Bowlero'), 'Tour Stop', '2024-01-15', 'Qualifying', 279, 268, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE'), (SELECT location_id FROM locations WHERE name = 'Bowlero'), 'Tour Stop', '2024-01-15', 'Match Play', 256, 289, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE'), (SELECT location_id FROM locations WHERE name = 'Bowlero'), 'Playoffs', '2024-01-16', 'Round 1', 278, 245, 289),

-- EJ Tackett Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT'), (SELECT location_id FROM locations WHERE name = 'AMF Bowling Center'), 'Tour Stop', '2024-01-20', 'Qualifying', 267, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT'), (SELECT location_id FROM locations WHERE name = 'AMF Bowling Center'), 'Tour Stop', '2024-01-20', 'Match Play', 245, 278, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT'), (SELECT location_id FROM locations WHERE name = 'AMF Bowling Center'), 'Playoffs', '2024-01-21', 'Round 1', 289, 256, 245),

-- Anthony Simonsen Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN'), (SELECT location_id FROM locations WHERE name = 'Strike Zone'), 'Tour Stop', '2024-02-05', 'Qualifying', 245, 289, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN'), (SELECT location_id FROM locations WHERE name = 'Strike Zone'), 'Tour Stop', '2024-02-05', 'Match Play', 278, 245, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN'), (SELECT location_id FROM locations WHERE name = 'Strike Zone'), 'Playoffs', '2024-02-06', 'Round 1', 267, 289, 245),

-- Chris Via Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA'), (SELECT location_id FROM locations WHERE name = 'Pin Palace'), 'Tour Stop', '2024-02-10', 'Qualifying', 289, 245, 278),
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA'), (SELECT location_id FROM locations WHERE name = 'Pin Palace'), 'Tour Stop', '2024-02-10', 'Match Play', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA'), (SELECT location_id FROM locations WHERE name = 'Pin Palace'), 'Playoffs', '2024-02-11', 'Round 1', 278, 245, 267),

-- Kyle Troup Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP'), (SELECT location_id FROM locations WHERE name = 'Lucky Strike'), 'Tour Stop', '2024-02-15', 'Qualifying', 267, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP'), (SELECT location_id FROM locations WHERE name = 'Lucky Strike'), 'Tour Stop', '2024-02-15', 'Match Play', 245, 278, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP'), (SELECT location_id FROM locations WHERE name = 'Lucky Strike'), 'Playoffs', '2024-02-16', 'Round 1', 289, 245, 278),

-- Jakob Butturff Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'JAKOB BUTTURFF'), (SELECT location_id FROM locations WHERE name = 'Main Event'), 'Tour Stop', '2024-02-20', 'Qualifying', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'JAKOB BUTTURFF'), (SELECT location_id FROM locations WHERE name = 'Main Event'), 'Tour Stop', '2024-02-20', 'Match Play', 278, 245, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'JAKOB BUTTURFF'), (SELECT location_id FROM locations WHERE name = 'Main Event'), 'Playoffs', '2024-02-21', 'Round 1', 267, 289, 245),

-- Dom Barrett Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'DOM BARRETT'), (SELECT location_id FROM locations WHERE name = 'Round1'), 'Tour Stop', '2024-03-01', 'Qualifying', 289, 245, 278),
((SELECT bowler_id FROM bowlers WHERE nickname = 'DOM BARRETT'), (SELECT location_id FROM locations WHERE name = 'Round1'), 'Tour Stop', '2024-03-01', 'Match Play', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'DOM BARRETT'), (SELECT location_id FROM locations WHERE name = 'Round1'), 'Playoffs', '2024-03-02', 'Round 1', 278, 245, 267),

-- Francois Lavoie Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'FRANCOIS LAVOIE'), (SELECT location_id FROM locations WHERE name = 'Dave & Busters'), 'Tour Stop', '2024-03-05', 'Qualifying', 267, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'FRANCOIS LAVOIE'), (SELECT location_id FROM locations WHERE name = 'Dave & Busters'), 'Tour Stop', '2024-03-05', 'Match Play', 245, 278, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'FRANCOIS LAVOIE'), (SELECT location_id FROM locations WHERE name = 'Dave & Busters'), 'Playoffs', '2024-03-06', 'Round 1', 289, 245, 278),

-- Tommy Jones Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'TOMMY JONES'), (SELECT location_id FROM locations WHERE name = 'Pinstack'), 'Tour Stop', '2024-03-10', 'Qualifying', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'TOMMY JONES'), (SELECT location_id FROM locations WHERE name = 'Pinstack'), 'Tour Stop', '2024-03-10', 'Match Play', 278, 245, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'TOMMY JONES'), (SELECT location_id FROM locations WHERE name = 'Pinstack'), 'Playoffs', '2024-03-11', 'Round 1', 267, 289, 245),

-- Wes Malott Tournament Series
((SELECT bowler_id FROM bowlers WHERE nickname = 'WES MALOTT'), (SELECT location_id FROM locations WHERE name = 'Kings Bowl'), 'Tour Stop', '2024-03-15', 'Qualifying', 289, 245, 278),
((SELECT bowler_id FROM bowlers WHERE nickname = 'WES MALOTT'), (SELECT location_id FROM locations WHERE name = 'Kings Bowl'), 'Tour Stop', '2024-03-15', 'Match Play', 245, 267, 289),
((SELECT bowler_id FROM bowlers WHERE nickname = 'WES MALOTT'), (SELECT location_id FROM locations WHERE name = 'Kings Bowl'), 'Playoffs', '2024-03-16', 'Round 1', 278, 245, 267);

-- Insert House History Series (Regular League Play)
INSERT INTO game_series (bowler_id, location_id, series_type, event_date, round_name, game1_score, game2_score, game3_score) VALUES
-- Regular league play for various bowlers
((SELECT bowler_id FROM bowlers WHERE nickname = 'SEAN RASH'), (SELECT location_id FROM locations WHERE name = 'Splitsville'), 'House History', '2024-01-08', 'League Night', 245, 267, 234),
((SELECT bowler_id FROM bowlers WHERE nickname = 'MARSHALL KENT'), (SELECT location_id FROM locations WHERE name = 'Lodi Lanes'), 'House History', '2024-01-09', 'League Night', 256, 245, 278),
((SELECT bowler_id FROM bowlers WHERE nickname = 'AJ CHAPMAN'), (SELECT location_id FROM locations WHERE name = 'Parkway Lanes'), 'House History', '2024-01-10', 'League Night', 234, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'BILL O\'NEILL'), (SELECT location_id FROM locations WHERE name = 'Battle Bowl'), 'House History', '2024-01-11', 'League Night', 267, 245, 256),
((SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS BARNES'), (SELECT location_id FROM locations WHERE name = 'Bowlmor Lanes'), 'House History', '2024-01-12', 'League Night', 245, 278, 234),
((SELECT bowler_id FROM bowlers WHERE nickname = 'RYAN SHAFER'), (SELECT location_id FROM locations WHERE name = 'AMF Bowling Center'), 'House History', '2024-01-15', 'League Night', 256, 234, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'PETE WEBER'), (SELECT location_id FROM locations WHERE name = 'Strike Zone'), 'House History', '2024-01-16', 'League Night', 234, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'WALTER RAY WILLIAMS JR'), (SELECT location_id FROM locations WHERE name = 'Pin Palace'), 'House History', '2024-01-17', 'League Night', 267, 245, 256),
((SELECT bowler_id FROM bowlers WHERE nickname = 'NORM DUKE'), (SELECT location_id FROM locations WHERE name = 'Lucky Strike'), 'House History', '2024-01-18', 'League Night', 245, 278, 234),
((SELECT bowler_id FROM bowlers WHERE nickname = 'PARKER BOHN III'), (SELECT location_id FROM locations WHERE name = 'Main Event'), 'House History', '2024-01-19', 'League Night', 256, 234, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'DANNY WISEMAN'), (SELECT location_id FROM locations WHERE name = 'Round1'), 'House History', '2024-01-22', 'League Night', 234, 289, 245),
((SELECT bowler_id FROM bowlers WHERE nickname = 'AMLEETO MONACELLI'), (SELECT location_id FROM locations WHERE name = 'Dave & Busters'), 'House History', '2024-01-23', 'League Night', 267, 245, 256),
((SELECT bowler_id FROM bowlers WHERE nickname = 'MIKE FAGAN'), (SELECT location_id FROM locations WHERE name = 'Pinstack'), 'House History', '2024-01-24', 'League Night', 245, 278, 234),
((SELECT bowler_id FROM bowlers WHERE nickname = 'OSCAR RODRIGUEZ'), (SELECT location_id FROM locations WHERE name = 'Kings Bowl'), 'House History', '2024-01-25', 'League Night', 256, 234, 267),
((SELECT bowler_id FROM bowlers WHERE nickname = 'GREG YOUNG'), (SELECT location_id FROM locations WHERE name = 'Splitsville'), 'House History', '2024-01-26', 'League Night', 234, 289, 245);

-- Insert Individual Game Details (for the games table)
INSERT INTO games (series_id, game_number, score) VALUES
-- Jason Belmonte Games
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE') AND round_name = 'Qualifying' LIMIT 1), 1, 279),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE') AND round_name = 'Qualifying' LIMIT 1), 2, 268),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'JASON BELMONTE') AND round_name = 'Qualifying' LIMIT 1), 3, 245),

-- EJ Tackett Games
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT') AND round_name = 'Qualifying' LIMIT 1), 1, 267),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT') AND round_name = 'Qualifying' LIMIT 1), 2, 289),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'EJ TACKETT') AND round_name = 'Qualifying' LIMIT 1), 3, 245),

-- Anthony Simonsen Games
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN') AND round_name = 'Qualifying' LIMIT 1), 1, 245),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN') AND round_name = 'Qualifying' LIMIT 1), 2, 289),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'ANTHONY SIMONSEN') AND round_name = 'Qualifying' LIMIT 1), 3, 267),

-- Chris Via Games
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA') AND round_name = 'Qualifying' LIMIT 1), 1, 289),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA') AND round_name = 'Qualifying' LIMIT 1), 2, 245),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'CHRIS VIA') AND round_name = 'Qualifying' LIMIT 1), 3, 278),

-- Kyle Troup Games
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP') AND round_name = 'Qualifying' LIMIT 1), 1, 267),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP') AND round_name = 'Qualifying' LIMIT 1), 2, 289),
((SELECT series_id FROM game_series WHERE bowler_id = (SELECT bowler_id FROM bowlers WHERE nickname = 'KYLE TROUP') AND round_name = 'Qualifying' LIMIT 1), 3, 245);

-- Display summary
SELECT 'Seed Data Inserted Successfully!' as status;
SELECT COUNT(*) as total_bowlers FROM bowlers;
SELECT COUNT(*) as total_locations FROM locations;
SELECT COUNT(*) as total_series FROM game_series;
SELECT COUNT(*) as total_games FROM games;

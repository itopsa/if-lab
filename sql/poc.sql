-- Create a custom type for bowler's dexterity
CREATE TYPE dexterity AS ENUM ('Right', 'Left', 'Ambidextrous');

-- Create a custom type for the bowling style
CREATE TYPE bowling_style AS ENUM ('1 Handed', '2 Handed');

-- Create a custom type for the series type (Tour, Playoffs, etc.)
CREATE TYPE series_type AS ENUM ('Tour Stop', 'Playoffs', 'House History');


-- Table to store information about each bowler
CREATE TABLE bowlers (
    bowler_id SERIAL PRIMARY KEY,
    nickname VARCHAR(100) NOT NULL,
    uba_id VARCHAR(20) UNIQUE,
    usbc_id VARCHAR(20) UNIQUE,
    dexterity dexterity,
    style bowling_style,
    home_house_id INT, -- This will be a foreign key to the locations table
    created_at TIMESTAMPTZ DEFAULT NOW()
);


-- Table to store information about each bowling location/house
CREATE TABLE locations (
    location_id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    city VARCHAR(100),
    state VARCHAR(100),
    created_at TIMESTAMPTZ DEFAULT NOW()
);


-- Add the foreign key constraint to the bowlers table after locations is created
ALTER TABLE bowlers
ADD CONSTRAINT fk_home_house
FOREIGN KEY (home_house_id)
REFERENCES locations(location_id);


-- Table to store a series of games played by a bowler at a location
CREATE TABLE game_series (
    series_id SERIAL PRIMARY KEY,
    bowler_id INT NOT NULL,
    location_id INT NOT NULL,
    series_type series_type NOT NULL,
    event_date DATE NOT NULL,
    round_name VARCHAR(50), -- e.g., 'Round 1', 'Sweet 16' for playoffs
    total_score INT GENERATED ALWAYS AS (game1_score + game2_score + game3_score) STORED,
    average_score NUMERIC(5, 2) GENERATED ALWAYS AS ((game1_score + game2_score + game3_score) / 3.0) STORED,
    created_at TIMESTAMPTZ DEFAULT NOW(),

    CONSTRAINT fk_bowler
        FOREIGN KEY(bowler_id)
        REFERENCES bowlers(bowler_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_location
        FOREIGN KEY(location_id)
        REFERENCES locations(location_id)
        ON DELETE SET NULL
);


-- Table to store individual game scores within a series
-- This provides more detail and is more flexible than storing games as columns in the series table.
CREATE TABLE games (
    game_id SERIAL PRIMARY KEY,
    series_id INT NOT NULL,
    game_number INT NOT NULL,
    score INT NOT NULL,

    CONSTRAINT fk_series
        FOREIGN KEY(series_id)
        REFERENCES game_series(series_id)
        ON DELETE CASCADE,

    -- Ensure a game score is within a valid range
    CONSTRAINT chk_score CHECK (score >= 0 AND score <= 300),
    -- Ensure game number is logical (e.g., 1, 2, 3)
    CONSTRAINT chk_game_number CHECK (game_number > 0),
    -- Each game in a series must be unique
    UNIQUE(series_id, game_number)
);

-- Example of how you might insert data:

-- 1. Insert Locations
INSERT INTO locations (name) VALUES ('Lodi Lanes'), ('Parkway Lanes'), ('Battle Bowl');

-- 2. Insert a Bowler
INSERT INTO bowlers (nickname, uba_id, usbc_id, dexterity, style, home_house_id)
VALUES ('ZHANDOW, BALL REP', '60 919726', '9880-30613', 'Right', '2 Handed', (SELECT location_id FROM locations WHERE name = 'Lodi Lanes'));

-- 3. Insert a Tour Stop Series
INSERT INTO game_series (bowler_id, location_id, series_type, event_date, round_name)
VALUES
    ((SELECT bowler_id FROM bowlers WHERE nickname = 'ZHANDOW, BALL REP'), (SELECT location_id FROM locations WHERE name = 'Lodi Lanes'), 'Tour Stop', '2024-01-10', '1'),
    ((SELECT bowler_id FROM bowlers WHERE nickname = 'ZHANDOW, BALL REP'), (SELECT location_id FROM locations WHERE name = 'Parkway Lanes'), 'Tour Stop', '2024-01-17', '2');


-- 4. Insert the games for that series
-- Games for Lodi Lanes
INSERT INTO games (series_id, game_number, score) VALUES
    ((SELECT series_id FROM game_series WHERE location_id = (SELECT location_id FROM locations WHERE name = 'Lodi Lanes') AND round_name = '1'), 1, 230),
    ((SELECT series_id FROM game_series WHERE location_id = (SELECT location_id FROM locations WHERE name = 'Lodi Lanes') AND round_name = '1'), 2, 224),
    ((SELECT series_id FROM game_series WHERE location_id = (SELECT location_id FROM locations WHERE name = 'Lodi Lanes') AND round_name = '1'), 3, 216);

-- Games for Parkway Lanes
INSERT INTO games (series_id, game_number, score) VALUES
    ((SELECT series_id FROM game_series WHERE location_id = (SELECT location_id FROM locations WHERE name = 'Parkway Lanes') AND round_name = '2'), 1, 224),
    ((SELECT series_id FROM game_series WHERE location_id = (SELECT location_id FROM locations WHERE name = 'Parkway Lanes') AND round_name = '2'), 2, 220),
    ((SELECT series_id FROM game_series WHERE location_id = (SELECT location_id FROM locations WHERE name = 'Parkway Lanes') AND round_name = '2'), 3, 200);


-- IBTrACS Database Schema
-- ========================
-- International Best Track Archive for Climate Stewardship
-- Tropical Cyclone Database Structure

-- Create database
CREATE DATABASE IF NOT EXISTS ibtracs_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ibtracs_db;

-- Main storms table
-- Contains the primary storm data from IBTrACS
CREATE TABLE IF NOT EXISTS storms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sid VARCHAR(50) NOT NULL COMMENT 'Unique storm identifier',
    name VARCHAR(100) COMMENT 'Storm name',
    season INT COMMENT 'Year/season of the storm',
    basin VARCHAR(10) COMMENT 'Ocean basin (NA, EP, WP, etc.)',
    latitude FLOAT COMMENT 'Latitude in decimal degrees',
    longitude FLOAT COMMENT 'Longitude in decimal degrees',
    wind INT COMMENT 'Wind speed in knots',
    pressure INT COMMENT 'Atmospheric pressure in hPa',
    time DATETIME COMMENT 'Observation time',
    nature VARCHAR(50) COMMENT 'Storm nature/type',
    track_type VARCHAR(20) COMMENT 'Track type classification',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    UNIQUE KEY unique_sid (sid),
    INDEX idx_season (season),
    INDEX idx_basin (basin),
    INDEX idx_name (name),
    INDEX idx_time (time),
    INDEX idx_lat_lon (latitude, longitude),
    INDEX idx_wind (wind),
    INDEX idx_pressure (pressure)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Primary storm data from IBTrACS';

-- Storm tracks table
-- Contains detailed track information for each storm
CREATE TABLE IF NOT EXISTS storm_tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sid VARCHAR(50) NOT NULL COMMENT 'Storm identifier (foreign key)',
    time DATETIME COMMENT 'Track point time',
    latitude FLOAT COMMENT 'Latitude in decimal degrees',
    longitude FLOAT COMMENT 'Longitude in decimal degrees',
    wind INT COMMENT 'Wind speed in knots',
    pressure INT COMMENT 'Atmospheric pressure in hPa',
    nature VARCHAR(50) COMMENT 'Storm nature at this point',
    track_type VARCHAR(20) COMMENT 'Track type at this point',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (sid) REFERENCES storms(sid) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_sid_time (sid, time),
    INDEX idx_lat_lon (latitude, longitude),
    INDEX idx_wind (wind),
    INDEX idx_pressure (pressure)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Detailed storm track information';

-- Storm statistics table
-- Contains calculated statistics for each storm
CREATE TABLE IF NOT EXISTS storm_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sid VARCHAR(50) NOT NULL COMMENT 'Storm identifier (foreign key)',
    max_wind INT COMMENT 'Maximum wind speed in knots',
    min_pressure INT COMMENT 'Minimum pressure in hPa',
    duration_hours INT COMMENT 'Storm duration in hours',
    distance_km FLOAT COMMENT 'Total distance traveled in km',
    category VARCHAR(10) COMMENT 'Storm category (TS, C1, C2, etc.)',
    landfall_count INT DEFAULT 0 COMMENT 'Number of landfalls',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (sid) REFERENCES storms(sid) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_category (category),
    INDEX idx_max_wind (max_wind),
    INDEX idx_min_pressure (min_pressure)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Calculated storm statistics';

-- Basin statistics table
-- Contains aggregated statistics by basin and season
CREATE TABLE IF NOT EXISTS basin_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    basin VARCHAR(10) NOT NULL COMMENT 'Ocean basin',
    season INT NOT NULL COMMENT 'Year/season',
    storm_count INT DEFAULT 0 COMMENT 'Number of storms in basin/season',
    avg_wind FLOAT COMMENT 'Average wind speed',
    max_wind INT COMMENT 'Maximum wind speed in basin/season',
    min_pressure INT COMMENT 'Minimum pressure in basin/season',
    category_breakdown JSON COMMENT 'Breakdown by storm categories',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Unique constraint
    UNIQUE KEY unique_basin_season (basin, season),
    
    -- Indexes for performance
    INDEX idx_basin (basin),
    INDEX idx_season (season),
    INDEX idx_storm_count (storm_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Aggregated basin and season statistics';

-- Global statistics table
-- Contains overall database statistics
CREATE TABLE IF NOT EXISTS global_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_name VARCHAR(50) NOT NULL COMMENT 'Statistic name',
    stat_value VARCHAR(255) COMMENT 'Statistic value',
    stat_type ENUM('integer', 'float', 'string', 'json') DEFAULT 'string',
    description TEXT COMMENT 'Description of the statistic',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Unique constraint
    UNIQUE KEY unique_stat_name (stat_name),
    
    -- Indexes
    INDEX idx_stat_type (stat_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Global database statistics';

-- Views for common queries
-- ========================

-- View: Recent storms (last 10 years)
CREATE OR REPLACE VIEW recent_storms AS
SELECT 
    s.sid,
    s.name,
    s.season,
    s.basin,
    s.latitude,
    s.longitude,
    s.wind,
    s.pressure,
    ss.category,
    ss.max_wind,
    ss.min_pressure
FROM storms s
LEFT JOIN storm_stats ss ON s.sid = ss.sid
WHERE s.season >= YEAR(CURDATE()) - 10
ORDER BY s.season DESC, s.name;

-- View: Category 5 storms
CREATE OR REPLACE VIEW category_5_storms AS
SELECT 
    s.sid,
    s.name,
    s.season,
    s.basin,
    s.latitude,
    s.longitude,
    s.wind,
    s.pressure,
    ss.max_wind,
    ss.min_pressure
FROM storms s
LEFT JOIN storm_stats ss ON s.sid = ss.sid
WHERE ss.category = 'Category 5' OR s.wind >= 135
ORDER BY s.season DESC, s.name;

-- View: Basin summary
CREATE OR REPLACE VIEW basin_summary AS
SELECT 
    basin,
    COUNT(DISTINCT sid) as total_storms,
    COUNT(DISTINCT season) as total_seasons,
    MIN(season) as first_season,
    MAX(season) as last_season,
    AVG(wind) as avg_wind,
    MAX(wind) as max_wind,
    MIN(pressure) as min_pressure
FROM storms
WHERE basin IS NOT NULL
GROUP BY basin
ORDER BY total_storms DESC;

-- Stored Procedures
-- =================

-- Procedure: Update storm statistics
DELIMITER //
CREATE PROCEDURE UpdateStormStats()
BEGIN
    -- Update storm statistics table
    INSERT INTO storm_stats (sid, max_wind, min_pressure, category)
    SELECT 
        s.sid,
        MAX(s.wind) as max_wind,
        MIN(s.pressure) as min_pressure,
        CASE 
            WHEN MAX(s.wind) >= 135 THEN 'Category 5'
            WHEN MAX(s.wind) >= 113 THEN 'Category 4'
            WHEN MAX(s.wind) >= 96 THEN 'Category 3'
            WHEN MAX(s.wind) >= 83 THEN 'Category 2'
            WHEN MAX(s.wind) >= 64 THEN 'Category 1'
            WHEN MAX(s.wind) >= 34 THEN 'Tropical Storm'
            ELSE 'Tropical Depression'
        END as category
    FROM storms s
    WHERE s.wind IS NOT NULL
    GROUP BY s.sid
    ON DUPLICATE KEY UPDATE
        max_wind = VALUES(max_wind),
        min_pressure = VALUES(min_pressure),
        category = VALUES(category);
    
    -- Update basin statistics
    INSERT INTO basin_stats (basin, season, storm_count, avg_wind, max_wind, min_pressure)
    SELECT 
        basin,
        season,
        COUNT(DISTINCT sid) as storm_count,
        AVG(wind) as avg_wind,
        MAX(wind) as max_wind,
        MIN(pressure) as min_pressure
    FROM storms
    WHERE basin IS NOT NULL AND season IS NOT NULL
    GROUP BY basin, season
    ON DUPLICATE KEY UPDATE
        storm_count = VALUES(storm_count),
        avg_wind = VALUES(avg_wind),
        max_wind = VALUES(max_wind),
        min_pressure = VALUES(min_pressure);
END //
DELIMITER ;

-- Procedure: Get storm by name
DELIMITER //
CREATE PROCEDURE GetStormByName(IN storm_name VARCHAR(100))
BEGIN
    SELECT 
        s.*,
        ss.category,
        ss.max_wind,
        ss.min_pressure
    FROM storms s
    LEFT JOIN storm_stats ss ON s.sid = ss.sid
    WHERE s.name LIKE CONCAT('%', storm_name, '%')
    ORDER BY s.season DESC, s.name;
END //
DELIMITER ;

-- Procedure: Get storms by basin and year range
DELIMITER //
CREATE PROCEDURE GetStormsByBasinAndYear(
    IN basin_name VARCHAR(10),
    IN start_year INT,
    IN end_year INT
)
BEGIN
    SELECT 
        s.*,
        ss.category,
        ss.max_wind,
        ss.min_pressure
    FROM storms s
    LEFT JOIN storm_stats ss ON s.sid = ss.sid
    WHERE s.basin = basin_name 
    AND s.season BETWEEN start_year AND end_year
    ORDER BY s.season DESC, s.name;
END //
DELIMITER ;

-- Triggers
-- ========

-- Trigger: Update global stats when storms are inserted
DELIMITER //
CREATE TRIGGER after_storm_insert
AFTER INSERT ON storms
FOR EACH ROW
BEGIN
    -- Update total storms count
    INSERT INTO global_stats (stat_name, stat_value, stat_type, description)
    VALUES ('total_storms', (SELECT COUNT(*) FROM storms), 'integer', 'Total number of storms')
    ON DUPLICATE KEY UPDATE 
        stat_value = VALUES(stat_value),
        last_updated = CURRENT_TIMESTAMP;
    
    -- Update year range
    INSERT INTO global_stats (stat_name, stat_value, stat_type, description)
    VALUES ('year_range', 
            CONCAT((SELECT MIN(season) FROM storms), '-', (SELECT MAX(season) FROM storms)),
            'string', 'Year range of data')
    ON DUPLICATE KEY UPDATE 
        stat_value = VALUES(stat_value),
        last_updated = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- Initial data
-- ============

-- Insert some initial global statistics
INSERT INTO global_stats (stat_name, stat_value, stat_type, description) VALUES
('database_version', '1.0', 'string', 'Database schema version'),
('data_source', 'IBTrACS v04r00', 'string', 'Source of tropical cyclone data'),
('last_import', NOW(), 'string', 'Last data import timestamp'),
('total_storms', '0', 'integer', 'Total number of storms'),
('year_range', 'N/A', 'string', 'Year range of data')
ON DUPLICATE KEY UPDATE stat_value = VALUES(stat_value);

-- Comments and documentation
-- =========================

-- Add comments to tables
ALTER TABLE storms COMMENT = 'Primary storm data from IBTrACS - International Best Track Archive for Climate Stewardship';
ALTER TABLE storm_tracks COMMENT = 'Detailed track information for each storm';
ALTER TABLE storm_stats COMMENT = 'Calculated statistics for each storm';
ALTER TABLE basin_stats COMMENT = 'Aggregated statistics by ocean basin and season';
ALTER TABLE global_stats COMMENT = 'Global database statistics and metadata';

-- Create indexes for better performance
CREATE INDEX idx_storms_composite ON storms(season, basin, wind);
CREATE INDEX idx_tracks_composite ON storm_tracks(sid, time, latitude, longitude);
CREATE INDEX idx_stats_composite ON storm_stats(category, max_wind);

-- Grant permissions (adjust as needed)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON ibtracs_db.* TO 'ibtracs_user'@'localhost';
-- GRANT EXECUTE ON PROCEDURE ibtracs_db.* TO 'ibtracs_user'@'localhost';

-- Show completion message
SELECT 'IBTrACS Database Schema Created Successfully!' as message;

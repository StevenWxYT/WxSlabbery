-- 🌀 IBTrACS Cyclone Data
CREATE TABLE IF NOT EXISTS IBTrACS_Storms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sid VARCHAR(100),
    name VARCHAR(100),
    basin VARCHAR(50),
    agency VARCHAR(50),
    lat FLOAT,
    lon FLOAT,
    wind_kts INT,
    pressure_mb INT,
    timestamp DATETIME,
    storm_type VARCHAR(50),
    nature VARCHAR(50),
    track_type VARCHAR(50),
    track_points INT,
    start_date DATETIME,
    end_date DATETIME,
    landfall_count INT,
    max_wind_kts INT,
    min_pressure_mb INT,
    comments TEXT,
    storm_num INT,
    season_year INT,
    storm_classification VARCHAR(50),
    source_notes TEXT
);

-- 🔁 Multi-Agency Wind/Pressure Comparison Table
CREATE TABLE IF NOT EXISTS Agency_Storm_Intensity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ibtracs_id VARCHAR(100),  -- Links to SID in IBTrACS_Storms
    agency VARCHAR(50),
    timestamp DATETIME,
    wind_kts INT,
    pressure_mb INT,
    classification VARCHAR(50),
    source_file VARCHAR(255)
);

-- 🌪️ Synthetic Storm Simulations
CREATE TABLE IF NOT EXISTS Synthetic_Storms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sim_id VARCHAR(100),
    model VARCHAR(100),
    scenario VARCHAR(100),
    lat FLOAT,
    lon FLOAT,
    wind_kts INT,
    pressure_mb INT,
    timestamp DATETIME,
    synthetic_source TEXT
);

-- 🗺️ HURDAT Historical Storms (e.g., Atlantic, East Pacific)
CREATE TABLE IF NOT EXISTS HURDAT_Storms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    storm_name VARCHAR(100),
    year INT,
    basin VARCHAR(50),
    lat FLOAT,
    lon FLOAT,
    wind_kts INT,
    pressure_mb INT,
    timestamp DATETIME,
    status VARCHAR(50)
);

-- 📝 Documentary Storm Records (Historical texts)
CREATE TABLE IF NOT EXISTS Documentary_Records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255),
    country VARCHAR(100),
    year INT,
    month INT,
    day INT,
    description TEXT,
    severity VARCHAR(50),
    source TEXT
);

-- 🧪 Proxy Storm Records (Geological evidence)
CREATE TABLE IF NOT EXISTS Proxy_Storms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    site VARCHAR(255),
    proxy_type VARCHAR(100),
    start_year INT,
    end_year INT,
    frequency_estimate FLOAT,
    uncertainty_years INT,
    source TEXT,
    lat FLOAT,
    lon FLOAT
);

-- 📍 Proxy Site Metadata
CREATE TABLE IF NOT EXISTS Proxy_Sites (
    site_id INT AUTO_INCREMENT PRIMARY KEY,
    site_name VARCHAR(255),
    lat FLOAT,
    lon FLOAT,
    proxy_type VARCHAR(100),
    record_start_year INT,
    record_end_year INT,
    resolution VARCHAR(50),
    source TEXT
);

-- 🌐 Basin Metadata (WMO basins, agencies, regions)
CREATE TABLE IF NOT EXISTS Basin_Metadata (
    basin_code VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100),
    agency VARCHAR(100),
    region VARCHAR(100),
    hemisphere VARCHAR(50)
);

-- 🌎 General TC Database for PHP + CLI Uploads
CREATE TABLE IF NOT EXISTS TCDatabase (
    id INT AUTO_INCREMENT PRIMARY KEY,
    storm_id VARCHAR(100) NOT NULL,
    name VARCHAR(255) NOT NULL,
    basin VARCHAR(100),
    wind_speed INT,
    pressure INT,
    start_date DATETIME,
    end_date DATETIME,
    fatalities INT,
    damages FLOAT,
    ace FLOAT,
    image TEXT
);


CREATE INDEX idx_sid ON IBTrACS_Storms(sid);
CREATE INDEX idx_ibtracs_timestamp ON IBTrACS_Storms(timestamp);
CREATE INDEX idx_agency_sid ON Agency_Storm_Intensity(ibtracs_id);
CREATE INDEX idx_agency_timestamp ON Agency_Storm_Intensity(timestamp);
CREATE INDEX idx_sim_id ON Synthetic_Storms(sim_id);
CREATE INDEX idx_hurdat_timestamp ON HURDAT_Storms(timestamp);
CREATE INDEX idx_documentary_timestamp ON Documentary_Records(timestamp);
CREATE INDEX idx_proxy_site ON Proxy_Storms(site);
CREATE INDEX idx_proxy_site_timestamp ON Proxy_Storms(timestamp);
CREATE INDEX idx_basin_code ON Basin_Metadata(basin_code);
CREATE INDEX idx_tc_storm_id ON TCDatabase(storm_id);
CREATE INDEX idx_tc_timestamp ON TCDatabase(start_date);
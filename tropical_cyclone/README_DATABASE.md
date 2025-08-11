# IBTrACS Database Setup Guide

This guide explains how to set up and use the IBTrACS (International Best Track Archive for Climate Stewardship) tropical cyclone database.

## Overview

The IBTrACS database contains comprehensive tropical cyclone data including:
- Storm tracks and positions
- Wind speeds and atmospheric pressure
- Storm categories and classifications
- Basin and seasonal statistics
- Historical data from multiple sources

## Prerequisites

- **MySQL/MariaDB Server** (5.7 or higher)
- **Python 3.8+** with required packages
- **IBTrACS CSV Data File** (download from NOAA)

## Installation

### 1. Install Python Dependencies

```bash
pip install mysql-connector-python pandas numpy
```

### 2. Download IBTrACS Data

1. Visit [NOAA's IBTrACS website](https://www.ncei.noaa.gov/products/international-best-track-archive)
2. Download the latest IBTrACS data (usually `ibtracs.ALL.list.v04r00.csv`)
3. Place the file in `ibtracs_master/data/` directory

### 3. Database Setup

#### Option A: Using the Python Script (Recommended)

```bash
# Basic setup with default settings
python ibtracs_db.py

# Custom database settings
python ibtracs_db.py --host localhost --user root --password your_password --database ibtracs_db

# Skip data import (create tables only)
python ibtracs_db.py --skip-import

# Show database info only
python ibtracs_db.py --info-only
```

#### Option B: Using SQL Schema

```bash
# Import the schema
mysql -u root -p < ibtracs_schema.sql
```

## Database Structure

### Main Tables

#### `storms`
Primary storm data table containing:
- `sid`: Unique storm identifier
- `name`: Storm name
- `season`: Year/season
- `basin`: Ocean basin (NA, EP, WP, etc.)
- `latitude/longitude`: Position coordinates
- `wind`: Wind speed in knots
- `pressure`: Atmospheric pressure in hPa
- `nature`: Storm nature/type
- `track_type`: Track classification

#### `storm_tracks`
Detailed track information for each storm:
- `sid`: Storm identifier (foreign key)
- `time`: Track point timestamp
- `latitude/longitude`: Position at this time
- `wind/pressure`: Intensity at this time
- `nature/track_type`: Storm characteristics

#### `storm_stats`
Calculated statistics for each storm:
- `max_wind`: Maximum wind speed
- `min_pressure`: Minimum pressure
- `category`: Storm category (TS, C1, C2, etc.)
- `duration_hours`: Storm duration
- `distance_km`: Total distance traveled

#### `basin_stats`
Aggregated statistics by basin and season:
- `storm_count`: Number of storms
- `avg_wind`: Average wind speed
- `max_wind`: Maximum wind speed
- `min_pressure`: Minimum pressure

#### `global_stats`
Overall database statistics and metadata.

### Views

- `recent_storms`: Storms from the last 10 years
- `category_5_storms`: All Category 5 storms
- `basin_summary`: Summary by ocean basin

### Stored Procedures

- `UpdateStormStats()`: Recalculate storm statistics
- `GetStormByName(name)`: Search storms by name
- `GetStormsByBasinAndYear(basin, start_year, end_year)`: Filter by basin and year range

## Usage Examples

### Using the Query Utility

```bash
# Get total storm count
python query_db.py count

# Search storms by name
python query_db.py name "Katrina"

# Get storms by basin
python query_db.py basin NA --limit 20

# Get storms by year
python query_db.py year 2020

# Get Category 5 storms
python query_db.py cat5 --limit 15

# Get basin summary
python query_db.py basin-summary

# Get year summary
python query_db.py year-summary

# Execute custom query
python query_db.py query "SELECT * FROM storms WHERE wind > 100 LIMIT 10"
```

### Direct SQL Queries

```sql
-- Get all Category 5 storms
SELECT s.name, s.season, s.basin, ss.max_wind, ss.min_pressure
FROM storms s
JOIN storm_stats ss ON s.sid = ss.sid
WHERE ss.category = 'Category 5'
ORDER BY s.season DESC;

-- Get storm count by basin
SELECT basin, COUNT(DISTINCT sid) as storm_count
FROM storms
GROUP BY basin
ORDER BY storm_count DESC;

-- Get storms in specific year range
SELECT name, season, basin, wind, pressure
FROM storms
WHERE season BETWEEN 2000 AND 2020
ORDER BY season DESC, name;

-- Get average wind speed by decade
SELECT 
    FLOOR(season/10)*10 as decade,
    AVG(wind) as avg_wind,
    COUNT(DISTINCT sid) as storm_count
FROM storms
WHERE wind IS NOT NULL
GROUP BY FLOOR(season/10)
ORDER BY decade;
```

## Data Sources

The database is populated from the official IBTrACS dataset which includes:
- **NOAA/NHC**: North Atlantic and Eastern Pacific
- **JTWC**: Western Pacific and Indian Ocean
- **BOM**: Australian region
- **Other regional centers**: Global coverage

## Performance Optimization

### Indexes
The database includes optimized indexes for:
- Storm searches by name, basin, year
- Geographic queries (latitude/longitude)
- Intensity-based filtering (wind/pressure)
- Statistical aggregations

### Partitioning
For large datasets, consider partitioning by year:
```sql
ALTER TABLE storms PARTITION BY RANGE (season) (
    PARTITION p1850 VALUES LESS THAN (1860),
    PARTITION p1860 VALUES LESS THAN (1870),
    -- ... continue for each decade
    PARTITION p2020 VALUES LESS THAN (2030)
);
```

## Maintenance

### Regular Updates
```bash
# Update statistics after new data import
mysql -u root -p ibtracs_db -e "CALL UpdateStormStats();"

# Check database size
mysql -u root -p ibtracs_db -e "SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)' FROM information_schema.tables WHERE table_schema = 'ibtracs_db';"
```

### Backup
```bash
# Create backup
mysqldump -u root -p ibtracs_db > ibtracs_backup_$(date +%Y%m%d).sql

# Restore backup
mysql -u root -p ibtracs_db < ibtracs_backup_20231201.sql
```

## Troubleshooting

### Common Issues

1. **Connection Error**
   ```bash
   # Check MySQL service
   sudo systemctl status mysql
   
   # Test connection
   mysql -u root -p -e "SELECT 1;"
   ```

2. **Import Errors**
   ```bash
   # Check CSV file format
   head -5 ibtracs_master/data/ibtracs.ALL.list.v04r00.csv
   
   # Verify file encoding
   file ibtracs_master/data/ibtracs.ALL.list.v04r00.csv
   ```

3. **Performance Issues**
   ```sql
   -- Check slow queries
   SHOW PROCESSLIST;
   
   -- Analyze table performance
   ANALYZE TABLE storms;
   ```

### Data Validation

```sql
-- Check for data quality issues
SELECT 
    COUNT(*) as total_storms,
    COUNT(DISTINCT basin) as basins,
    MIN(season) as earliest_year,
    MAX(season) as latest_year,
    AVG(wind) as avg_wind_speed
FROM storms;
```

## API Integration

The database can be accessed from applications using:
- **Python**: `mysql-connector-python`
- **PHP**: `mysqli` or `PDO`
- **Node.js**: `mysql2`
- **Java**: `mysql-connector-java`

Example Python connection:
```python
import mysql.connector

conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='your_password',
    database='ibtracs_db'
)

cursor = conn.cursor(dictionary=True)
cursor.execute("SELECT * FROM storms WHERE season = 2020")
storms = cursor.fetchall()
```

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review MySQL error logs
3. Verify data file format and encoding
4. Test with smaller data subsets first

## License

This database setup is provided under the MIT License. The IBTrACS data is provided by NOAA and is subject to their terms of use.

---

**IBTrACS Database** - Making tropical cyclone data accessible for research and analysis.

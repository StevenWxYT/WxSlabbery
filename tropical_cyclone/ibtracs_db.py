#!/usr/bin/env python3
"""
IBTrACS Database Setup Script
=============================

This script creates and populates the IBTrACS tropical cyclone database
from the official IBTrACS CSV data file.

Requirements:
- MySQL/MariaDB server
- IBTrACS CSV data file
- Python 3.8+
"""

import mysql.connector
import pandas as pd
import os
import sys
from datetime import datetime
import argparse

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'ibtracs_db'
}

# File paths
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
CSV_PATH = os.path.join(SCRIPT_DIR, 'ibtracs_master', 'data', 'ibtracs.ALL.list.v04r00.csv')

def create_database():
    """Create the IBTrACS database if it doesn't exist."""
    try:
        # Connect without specifying database
        conn = mysql.connector.connect(
            host=DB_CONFIG['host'],
            user=DB_CONFIG['user'],
            password=DB_CONFIG['password']
        )
        cursor = conn.cursor()
        
        # Create database
        cursor.execute(f"CREATE DATABASE IF NOT EXISTS {DB_CONFIG['database']}")
        print(f"✓ Database '{DB_CONFIG['database']}' created/verified")
        
        conn.close()
        return True
        
    except Exception as e:
        print(f"✗ Error creating database: {e}")
        return False

def create_tables():
    """Create the IBTrACS tables."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        
        # Main storms table
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS storms (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sid VARCHAR(50) NOT NULL,
                name VARCHAR(100),
                season INT,
                basin VARCHAR(10),
                latitude FLOAT,
                longitude FLOAT,
                wind INT,
                pressure INT,
                time DATETIME,
                nature VARCHAR(50),
                track_type VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_sid (sid),
                INDEX idx_season (season),
                INDEX idx_basin (basin),
                INDEX idx_name (name),
                INDEX idx_time (time)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        """)
        
        # Storm tracks table for detailed path data
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS storm_tracks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sid VARCHAR(50) NOT NULL,
                time DATETIME,
                latitude FLOAT,
                longitude FLOAT,
                wind INT,
                pressure INT,
                nature VARCHAR(50),
                track_type VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (sid) REFERENCES storms(sid) ON DELETE CASCADE,
                INDEX idx_sid_time (sid, time),
                INDEX idx_lat_lon (latitude, longitude)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        """)
        
        # Storm statistics table
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS storm_stats (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sid VARCHAR(50) NOT NULL,
                max_wind INT,
                min_pressure INT,
                duration_hours INT,
                distance_km FLOAT,
                category VARCHAR(10),
                landfall_count INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (sid) REFERENCES storms(sid) ON DELETE CASCADE,
                INDEX idx_category (category),
                INDEX idx_max_wind (max_wind)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        """)
        
        # Basin statistics table
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS basin_stats (
                id INT AUTO_INCREMENT PRIMARY KEY,
                basin VARCHAR(10) NOT NULL,
                season INT NOT NULL,
                storm_count INT DEFAULT 0,
                avg_wind FLOAT,
                max_wind INT,
                min_pressure INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_basin_season (basin, season),
                INDEX idx_basin (basin),
                INDEX idx_season (season)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        """)
        
        conn.commit()
        conn.close()
        print("✓ Database tables created successfully")
        return True
        
    except Exception as e:
        print(f"✗ Error creating tables: {e}")
        return False

def get_storm_category(wind_speed):
    """Determine storm category based on wind speed."""
    if not wind_speed or pd.isna(wind_speed):
        return 'Unknown'
    
    wind = float(wind_speed)
    if wind >= 135:
        return 'Category 5'
    elif wind >= 113:
        return 'Category 4'
    elif wind >= 96:
        return 'Category 3'
    elif wind >= 83:
        return 'Category 2'
    elif wind >= 64:
        return 'Category 1'
    elif wind >= 34:
        return 'Tropical Storm'
    else:
        return 'Tropical Depression'

def import_csv_data():
    """Import IBTrACS CSV data into the database."""
    if not os.path.exists(CSV_PATH):
        print(f"✗ CSV file not found at: {CSV_PATH}")
        print("Please download IBTrACS data and place it in the specified location.")
        return False
    
    try:
        print(f"📁 Loading CSV data from: {CSV_PATH}")
        
        # Read CSV in chunks to handle large files
        chunk_size = 10000
        total_rows = 0
        imported_storms = 0
        imported_tracks = 0
        
        # Get total rows for progress tracking
        df_total = pd.read_csv(CSV_PATH, low_memory=False)
        total_rows = len(df_total)
        print(f"📊 Total rows to process: {total_rows:,}")
        
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        
        # Process CSV in chunks
        for chunk_num, chunk in enumerate(pd.read_csv(CSV_PATH, low_memory=False, chunksize=chunk_size)):
            print(f"🔄 Processing chunk {chunk_num + 1} ({len(chunk):,} rows)...")
            
            # Clean and prepare data
            chunk = chunk.copy()
            
            # Handle missing values
            chunk = chunk.fillna({
                'SID': '',
                'NAME': '',
                'SEASON': 0,
                'BASIN': '',
                'LAT': 0,
                'LON': 0,
                'WIND': 0,
                'PRESSURE': 0,
                'NATURE': '',
                'TRACK_TYPE': ''
            })
            
            # Convert data types
            chunk['SEASON'] = pd.to_numeric(chunk['SEASON'], errors='coerce').fillna(0).astype(int)
            chunk['LAT'] = pd.to_numeric(chunk['LAT'], errors='coerce').fillna(0)
            chunk['LON'] = pd.to_numeric(chunk['LON'], errors='coerce').fillna(0)
            chunk['WIND'] = pd.to_numeric(chunk['WIND'], errors='coerce').fillna(0).astype(int)
            chunk['PRESSURE'] = pd.to_numeric(chunk['PRESSURE'], errors='coerce').fillna(0).astype(int)
            
            # Insert storm data
            storm_data = []
            track_data = []
            
            for _, row in chunk.iterrows():
                sid = str(row['SID']).strip()
                if not sid:
                    continue
                
                # Storm data
                storm_data.append((
                    sid,
                    str(row['NAME']).strip(),
                    int(row['SEASON']) if row['SEASON'] > 0 else None,
                    str(row['BASIN']).strip(),
                    float(row['LAT']) if row['LAT'] != 0 else None,
                    float(row['LON']) if row['LON'] != 0 else None,
                    int(row['WIND']) if row['WIND'] > 0 else None,
                    int(row['PRESSURE']) if row['PRESSURE'] > 0 else None,
                    str(row['NATURE']).strip(),
                    str(row['TRACK_TYPE']).strip()
                ))
                
                # Track data (same as storm data for now, but could be expanded)
                track_data.append((
                    sid,
                    float(row['LAT']) if row['LAT'] != 0 else None,
                    float(row['LON']) if row['LON'] != 0 else None,
                    int(row['WIND']) if row['WIND'] > 0 else None,
                    int(row['PRESSURE']) if row['PRESSURE'] > 0 else None,
                    str(row['NATURE']).strip(),
                    str(row['TRACK_TYPE']).strip()
                ))
            
            # Insert storms
            if storm_data:
                cursor.executemany("""
                    INSERT IGNORE INTO storms 
                    (sid, name, season, basin, latitude, longitude, wind, pressure, nature, track_type)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                """, storm_data)
                imported_storms += len(storm_data)
            
            # Insert tracks
            if track_data:
                cursor.executemany("""
                    INSERT IGNORE INTO storm_tracks 
                    (sid, latitude, longitude, wind, pressure, nature, track_type)
                    VALUES (%s, %s, %s, %s, %s, %s, %s)
                """, track_data)
                imported_tracks += len(track_data)
            
            conn.commit()
            
            # Progress update
            processed = (chunk_num + 1) * chunk_size
            progress = min(100, (processed / total_rows) * 100)
            print(f"📈 Progress: {progress:.1f}% ({processed:,}/{total_rows:,} rows)")
        
        conn.close()
        
        print(f"✓ Data import completed!")
        print(f"   - Storms imported: {imported_storms:,}")
        print(f"   - Track points imported: {imported_tracks:,}")
        
        return True
        
    except Exception as e:
        print(f"✗ Error importing data: {e}")
        return False

def calculate_statistics():
    """Calculate and store storm statistics."""
    try:
        print("📊 Calculating storm statistics...")
        
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        
        # Calculate storm statistics
        cursor.execute("""
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
                category = VALUES(category)
        """)
        
        # Calculate basin statistics
        cursor.execute("""
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
                min_pressure = VALUES(min_pressure)
        """)
        
        conn.commit()
        conn.close()
        
        print("✓ Statistics calculated and stored")
        return True
        
    except Exception as e:
        print(f"✗ Error calculating statistics: {e}")
        return False

def show_database_info():
    """Display database information."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        
        # Get table counts
        cursor.execute("SELECT COUNT(*) FROM storms")
        storm_count = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM storm_tracks")
        track_count = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM storm_stats")
        stats_count = cursor.fetchone()[0]
        
        cursor.execute("SELECT COUNT(*) FROM basin_stats")
        basin_stats_count = cursor.fetchone()[0]
        
        # Get year range
        cursor.execute("SELECT MIN(season), MAX(season) FROM storms WHERE season IS NOT NULL")
        year_range = cursor.fetchone()
        
        # Get basin count
        cursor.execute("SELECT COUNT(DISTINCT basin) FROM storms WHERE basin IS NOT NULL")
        basin_count = cursor.fetchone()[0]
        
        conn.close()
        
        print("\n📋 Database Information:")
        print(f"   - Total storms: {storm_count:,}")
        print(f"   - Total track points: {track_count:,}")
        print(f"   - Storm statistics: {stats_count:,}")
        print(f"   - Basin statistics: {basin_stats_count:,}")
        print(f"   - Year range: {year_range[0]} - {year_range[1]}")
        print(f"   - Number of basins: {basin_count}")
        
    except Exception as e:
        print(f"✗ Error getting database info: {e}")

def main():
    """Main function to set up the IBTrACS database."""
    parser = argparse.ArgumentParser(description='IBTrACS Database Setup')
    parser.add_argument('--host', default='localhost', help='Database host')
    parser.add_argument('--user', default='root', help='Database user')
    parser.add_argument('--password', default='', help='Database password')
    parser.add_argument('--database', default='ibtracs_db', help='Database name')
    parser.add_argument('--csv-path', help='Path to IBTrACS CSV file')
    parser.add_argument('--skip-import', action='store_true', help='Skip data import')
    parser.add_argument('--info-only', action='store_true', help='Show database info only')
    
    args = parser.parse_args()
    
    # Update configuration
    global DB_CONFIG, CSV_PATH
    DB_CONFIG.update({
        'host': args.host,
        'user': args.user,
        'password': args.password,
        'database': args.database
    })
    
    if args.csv_path:
        CSV_PATH = args.csv_path
    
    print("🌪️  IBTrACS Database Setup")
    print("=" * 50)
    print(f"Database: {DB_CONFIG['database']}")
    print(f"Host: {DB_CONFIG['host']}")
    print(f"User: {DB_CONFIG['user']}")
    print(f"CSV Path: {CSV_PATH}")
    print()
    
    if args.info_only:
        show_database_info()
        return
    
    # Create database
    if not create_database():
        sys.exit(1)
    
    # Create tables
    if not create_tables():
        sys.exit(1)
    
    # Import data (unless skipped)
    if not args.skip_import:
        if not import_csv_data():
            sys.exit(1)
        
        # Calculate statistics
        if not calculate_statistics():
            sys.exit(1)
    
    # Show final information
    show_database_info()
    
    print("\n✅ IBTrACS database setup completed successfully!")
    print("\nYou can now use the database with your applications.")

if __name__ == "__main__":
    main()

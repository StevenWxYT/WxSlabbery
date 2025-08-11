from flask import Flask, render_template, request, jsonify, send_file
import pandas as pd
import os
import threading
import mysql.connector
import json
from datetime import datetime
import numpy as np
from io import BytesIO
import matplotlib.pyplot as plt
import matplotlib
matplotlib.use('Agg')

app = Flask(__name__, template_folder='templates', static_folder='static')

# === Configuration ===
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
CSV_PATH = os.path.join(BASE_DIR, 'ibtracs_master', 'data', 'ibtracs.ALL.list.v04r00.csv')

db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'cyclone_db'
}

TABLE_NAME = "IBTrACS_Storms"
already_imported = False
progress = {"percent": 0}

# === Enhanced Database Schema ===
def create_enhanced_schema():
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        # Main storms table
        cursor.execute(f"""
            CREATE TABLE IF NOT EXISTS {TABLE_NAME} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sid VARCHAR(50) UNIQUE,
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
                INDEX idx_sid (sid),
                INDEX idx_season (season),
                INDEX idx_basin (basin),
                INDEX idx_name (name)
            );
        """)
        
        # Storm tracks table for detailed path data
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS storm_tracks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sid VARCHAR(50),
                time DATETIME,
                latitude FLOAT,
                longitude FLOAT,
                wind INT,
                pressure INT,
                nature VARCHAR(50),
                FOREIGN KEY (sid) REFERENCES IBTrACS_Storms(sid) ON DELETE CASCADE,
                INDEX idx_sid_time (sid, time)
            );
        """)
        
        # Storm statistics table
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS storm_stats (
                id INT AUTO_INCREMENT PRIMARY KEY,
                sid VARCHAR(50),
                max_wind INT,
                min_pressure INT,
                duration_hours INT,
                distance_km FLOAT,
                category VARCHAR(10),
                landfall_count INT,
                FOREIGN KEY (sid) REFERENCES IBTrACS_Storms(sid) ON DELETE CASCADE
            );
        """)
        
        conn.commit()
        conn.close()
        print("Enhanced database schema created successfully")
        
    except Exception as e:
        print(f"Error creating schema: {e}")

# === Import CSV to MySQL (Enhanced) ===
def import_csv_to_db():
    global already_imported, progress
    if already_imported:
        return

    try:
        create_enhanced_schema()
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        # Check if CSV exists
        if not os.path.exists(CSV_PATH):
            print(f"CSV file not found at {CSV_PATH}")
            progress["percent"] = -1
            return

        # Load CSV with more comprehensive columns
        df = pd.read_csv(CSV_PATH, low_memory=False)
        
        # Select relevant columns
        columns_mapping = {
            'SID': 'sid',
            'NAME': 'name', 
            'SEASON': 'season',
            'BASIN': 'basin',
            'LAT': 'latitude',
            'LON': 'longitude',
            'WIND': 'wind',
            'PRESSURE': 'pressure',
            'NATURE': 'nature',
            'TRACK_TYPE': 'track_type'
        }
        
        # Filter columns that exist in the CSV
        available_columns = {k: v for k, v in columns_mapping.items() if k in df.columns}
        df = df[list(available_columns.keys())]
        df.columns = list(available_columns.values())
        
        # Clean data
        df.dropna(subset=["sid"], inplace=True)
        df['wind'] = pd.to_numeric(df['wind'], errors='coerce')
        df['pressure'] = pd.to_numeric(df['pressure'], errors='coerce')
        df['latitude'] = pd.to_numeric(df['latitude'], errors='coerce')
        df['longitude'] = pd.to_numeric(df['longitude'], errors='coerce')
        
        # Insert in batches
        total = len(df)
        batch_size = 1000

        for i in range(0, total, batch_size):
            batch = df.iloc[i:i + batch_size]
            
            # Prepare data for insertion
            insert_data = []
            for _, row in batch.iterrows():
                data = []
                for col in available_columns.values():
                    if col in ['wind', 'pressure', 'latitude', 'longitude']:
                        data.append(row[col] if pd.notna(row[col]) else None)
                    else:
                        data.append(str(row[col]) if pd.notna(row[col]) else None)
                insert_data.append(tuple(data))
            
            # Insert with IGNORE to avoid duplicates
            placeholders = ', '.join(['%s'] * len(available_columns.values()))
            columns = ', '.join(available_columns.values())
            
            cursor.executemany(f"""
                INSERT IGNORE INTO {TABLE_NAME}
                ({columns})
                VALUES ({placeholders})
            """, insert_data)

            conn.commit()
            progress["percent"] = min(100, int((i + batch_size) / total * 100))

        conn.close()
        already_imported = True
        progress["percent"] = 100
        print("Data import completed successfully")

    except Exception as e:
        print(f"[ERROR] Failed to import CSV: {e}")
        progress["percent"] = -1

# === Routes ===
@app.route("/")
def index():
    if not already_imported:
        threading.Thread(target=import_csv_to_db).start()
    return render_template("ibtracs.html")

@app.route("/dashboard")
def dashboard():
    return render_template("dashboard.html")

@app.route("/storm-tracker")
def storm_tracker():
    return render_template("storm_tracker.html")

@app.route("/analytics")
def analytics():
    return render_template("analytics.html")

@app.route("/progress")
def get_progress():
    return jsonify(progress)

@app.route("/api/ibtracs")
def get_ibtracs_data():
    name = request.args.get("name", "")
    basin = request.args.get("basin", "")
    year = request.args.get("season", "")
    sid = request.args.get("sid", "")
    limit = int(request.args.get("limit", 1000))

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        query = f"SELECT * FROM {TABLE_NAME} WHERE 1=1"
        params = []

        if name:
            query += " AND name LIKE %s"
            params.append(f"%{name}%")
        if basin:
            query += " AND basin = %s"
            params.append(basin)
        if year:
            query += " AND season = %s"
            params.append(year)
        if sid:
            query += " AND sid LIKE %s"
            params.append(f"%{sid}%")

        query += f" ORDER BY season DESC, name LIMIT {limit}"

        cursor.execute(query, tuple(params))
        data = cursor.fetchall()
        conn.close()

        return jsonify(data)
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route("/api/storm/<sid>")
def get_storm_details(sid):
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        # Get basic storm info
        cursor.execute(f"SELECT * FROM {TABLE_NAME} WHERE sid = %s", (sid,))
        storm = cursor.fetchone()

        if not storm:
            return jsonify({"error": "Storm not found"}), 404

        # Get track data
        cursor.execute("""
            SELECT * FROM storm_tracks 
            WHERE sid = %s 
            ORDER BY time
        """, (sid,))
        tracks = cursor.fetchall()

        conn.close()

        return jsonify({
            "storm": storm,
            "tracks": tracks
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route("/api/statistics")
def get_statistics():
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        # Basic statistics
        cursor.execute(f"SELECT COUNT(*) as total_storms FROM {TABLE_NAME}")
        total_storms = cursor.fetchone()['total_storms']

        cursor.execute(f"SELECT COUNT(DISTINCT season) as total_seasons FROM {TABLE_NAME}")
        total_seasons = cursor.fetchone()['total_seasons']

        cursor.execute(f"SELECT COUNT(DISTINCT basin) as total_basins FROM {TABLE_NAME}")
        total_basins = cursor.fetchone()['total_basins']

        # Basin distribution
        cursor.execute(f"""
            SELECT basin, COUNT(*) as count 
            FROM {TABLE_NAME} 
            GROUP BY basin 
            ORDER BY count DESC
        """)
        basin_distribution = cursor.fetchall()

        # Season distribution
        cursor.execute(f"""
            SELECT season, COUNT(*) as count 
            FROM {TABLE_NAME} 
            GROUP BY season 
            ORDER BY season DESC 
            LIMIT 20
        """)
        season_distribution = cursor.fetchall()

        # Intensity statistics
        cursor.execute(f"""
            SELECT 
                MAX(wind) as max_wind,
                MIN(pressure) as min_pressure,
                AVG(wind) as avg_wind,
                AVG(pressure) as avg_pressure
            FROM {TABLE_NAME} 
            WHERE wind IS NOT NULL AND pressure IS NOT NULL
        """)
        intensity_stats = cursor.fetchone()

        conn.close()

        return jsonify({
            "total_storms": total_storms,
            "total_seasons": total_seasons,
            "total_basins": total_basins,
            "basin_distribution": basin_distribution,
            "season_distribution": season_distribution,
            "intensity_stats": intensity_stats
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route("/api/basins")
def get_basins():
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        cursor.execute(f"SELECT DISTINCT basin FROM {TABLE_NAME} WHERE basin IS NOT NULL ORDER BY basin")
        basins = cursor.fetchall()
        conn.close()

        return jsonify([basin['basin'] for basin in basins])
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route("/api/seasons")
def get_seasons():
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)

        cursor.execute(f"SELECT DISTINCT season FROM {TABLE_NAME} WHERE season IS NOT NULL ORDER BY season DESC")
        seasons = cursor.fetchall()
        conn.close()

        return jsonify([season['season'] for season in seasons])
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == "__main__":
    app.run(debug=True, host='0.0.0.0', port=5000)

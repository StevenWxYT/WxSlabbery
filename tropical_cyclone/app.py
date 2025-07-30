from flask import Flask, render_template, request, jsonify
import pandas as pd
import os
import threading
import mysql.connector

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


# === Import CSV to MySQL (run once) ===
def import_csv_to_db():
    global already_imported, progress
    if already_imported:
        return

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        # Create table if not exists
        cursor.execute(f"""
            CREATE TABLE IF NOT EXISTS {TABLE_NAME} (
                sid VARCHAR(50),
                name VARCHAR(50),
                season INT,
                basin VARCHAR(10),
                latitude FLOAT,
                longitude FLOAT,
                wind INT,
                pressure INT
            );
        """)

        # Load CSV
        df = pd.read_csv(CSV_PATH, low_memory=False)
        df = df[["SID", "NAME", "SEASON", "BASIN", "LAT", "LON", "WIND", "PRESSURE"]]
        df.columns = ["sid", "name", "season", "basin", "latitude", "longitude", "wind", "pressure"]
        df.dropna(subset=["sid"], inplace=True)

        # Insert in batches
        total = len(df)
        batch_size = 500

        for i in range(0, total, batch_size):
            batch = df.iloc[i:i + batch_size]
            cursor.executemany(f"""
                INSERT IGNORE INTO {TABLE_NAME}
                (sid, name, season, basin, latitude, longitude, wind, pressure)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """, batch.values.tolist())

            conn.commit()
            progress["percent"] = min(100, int((i + batch_size) / total * 100))

        conn.close()
        already_imported = True
        progress["percent"] = 100

    except Exception as e:
        print(f"[ERROR] Failed to import CSV: {e}")
        progress["percent"] = -1


# === Routes ===
@app.route("/")
def index():
    if not already_imported:
        threading.Thread(target=import_csv_to_db).start()
    return render_template("index.html")


@app.route("/progress")
def get_progress():
    return jsonify(progress)


@app.route("/data")
def get_data():
    name = request.args.get("name", "")
    basin = request.args.get("basin", "")
    year = request.args.get("season", "")

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

    cursor.execute(query, tuple(params))
    data = cursor.fetchall()
    conn.close()

    return jsonify(data)


if __name__ == "__main__":
    app.run(debug=True)

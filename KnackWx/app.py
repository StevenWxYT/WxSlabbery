from flask import Flask, jsonify, send_from_directory
import requests

app = Flask(__name__)

# Cyclone API endpoint
API_URL = "https://api.knackwx.com/atcf/v2"

# Serve index.html directly from root directory
@app.route('/')
def home():
    return send_from_directory('.', 'index.html')

# Backend proxy to fetch live cyclone data
@app.route('/api/cyclones')
def api_cyclones():
    try:
        response = requests.get(API_URL)
        response.raise_for_status()
        return jsonify(response.json())
    except requests.exceptions.RequestException as e:
        return jsonify({"error": "Failed to fetch data", "details": str(e)}), 500

# Serve other static files (e.g., JS, CSS)
@app.route('/<path:filename>')
def static_files(filename):
    return send_from_directory('.', filename)

if __name__ == '__main__':
    app.run(debug=True)

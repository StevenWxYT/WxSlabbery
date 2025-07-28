from flask import Flask, render_template, jsonify
import requests

app = Flask(__name__)

# API URL
API_URL = "https://api.knackwx.com/atcf/v2"

@app.route('/')
def home():
    # Fetch data from the cyclone API
    try:
        response = requests.get(API_URL)
        response.raise_for_status()  # Raise an exception for HTTP errors
        cyclones = response.json()  # Parse JSON response
    except requests.exceptions.RequestException as e:
        print(f"Error fetching data: {e}")
        cyclones = None

    return render_template('index.html', cyclones=cyclones)

@app.route('/api/cyclones')
def api_cyclones():
    # Fetch data from the cyclone API for API route
    try:
        response = requests.get(API_URL)
        response.raise_for_status()
        return jsonify(response.json())
    except requests.exceptions.RequestException:
        return jsonify({"error": "Failed to fetch data"}), 500

if __name__ == '__main__':
    app.run(debug=True)

<?php
require_once 'db.php';
require_once 'function.php';

// NOAA IBTrACS Global CSV URL
$csvUrl = "https://www.ncei.noaa.gov/data/international-best-track-archive-for-climate-stewardship-ibtracs/v04r00/access/csv/ibtracs.ALL.list.v04r00.csv";

// Download CSV via cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $csvUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$csvData = curl_exec($ch);
curl_close($ch);

// Handle fetch error
if (!$csvData) {
    die("Error: Failed to download IBTrACS data.");
}

// Parse the CSV
$lines = explode("\n", $csvData);
$headers = str_getcsv(array_shift($lines)); // Remove and parse header

// Setup DB
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

$inserted = 0;
$skipped = 0;

// Loop through each storm entry
foreach ($lines as $line) {
    $row = str_getcsv($line);
    if (count($row) < 20 || empty($row[0])) continue;

    $storm = array_combine($headers, $row);
    if (!$storm) continue;

    // Extract data
    $sid         = $conn->real_escape_string($storm['SID'] ?? '');
    $name        = $conn->real_escape_string($storm['NAME'] ?? '');
    $basin       = $conn->real_escape_string($storm['BASIN'] ?? '');
    $agency      = $conn->real_escape_string($storm['TRACK_TYPE'] ?? '');
    $lat         = floatval($storm['LAT'] ?? 0);
    $lon         = floatval($storm['LON'] ?? 0);
    $wind_kts    = intval($storm['USA_WIND'] ?? 0);
    $pressure_mb = intval($storm['USA_PRES'] ?? 0);
    $timestamp   = $conn->real_escape_string($storm['ISO_TIME'] ?? '');
    $storm_type  = $conn->real_escape_string($storm['STORM_TYPE'] ?? '');
    $nature      = $conn->real_escape_string($storm['NATURE'] ?? '');
    $track_type  = $conn->real_escape_string($storm['TRACK_TYPE'] ?? '');
    $track_points = intval($storm['NUM'] ?? 0);
    $season       = intval(substr($timestamp, 0, 4));
    $start_date   = $timestamp;
    $end_date     = $timestamp;

    // Validate required fields
    if (empty($sid) || empty($timestamp)) {
        $skipped++;
        continue;
    }

    // Check for duplicates
    $check = $conn->query("SELECT COUNT(*) FROM IBTrACS_Storms WHERE sid='$sid' AND timestamp='$timestamp'");
    $exists = $check ? $check->fetch_row()[0] : 0;

    if ($exists == 0) {
        $data = [
            'sid' => $sid,
            'name' => $name,
            'basin' => $basin,
            'agency' => $agency,
            'lat' => $lat,
            'lon' => $lon,
            'wind_kts' => $wind_kts,
            'pressure_mb' => $pressure_mb,
            'timestamp' => $timestamp,
            'storm_type' => $storm_type,
            'nature' => $nature,
            'track_type' => $track_type,
            'track_points' => $track_points,
            'season' => $season,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        $db->insertIBTracsStorm($data);
        $inserted++;
    } else {
        $skipped++;
    }
}

// HTML Output
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IBTrACS Import Results</title>
    <link rel="stylesheet" href="master.css">
</head>
<body>
    <div class="container">
        <h2>✅ IBTrACS Live Import Completed</h2>
        <p><strong>Inserted Records:</strong> $inserted</p>
        <p><strong>Skipped (Duplicates):</strong> $skipped</p>
        <a href="ibtracs_admin.php" class="btn">← Back to IBTrACS Admin</a>
    </div>
</body>
</html>
HTML;
?>

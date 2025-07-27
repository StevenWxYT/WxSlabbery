<?php
require_once '../php/function.php';

$db = new DBFunc($conn);

if (!isset($_GET['sid'])) {
    echo "Missing SID"; exit();
}

$sid = $conn->real_escape_string($_GET['sid']);
$data = $db->showIBTracsStorm($sid);

if (!$data) {
    echo "Storm not found."; exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View IBTrACS Storm</title>
</head>
<body>
    <h1><?= htmlspecialchars($data['name']) ?> (<?= htmlspecialchars($data['sid']) ?>)</h1>

    <p><strong>Basin:</strong> <?= htmlspecialchars($data['basin']) ?></p>
    <p><strong>Agency:</strong> <?= htmlspecialchars($data['agency']) ?></p>
    <p><strong>Latitude:</strong> <?= htmlspecialchars($data['lat']) ?></p>
    <p><strong>Longitude:</strong> <?= htmlspecialchars($data['lon']) ?></p>
    <p><strong>Wind:</strong> <?= htmlspecialchars($data['wind_kts']) ?> kts</p>
    <p><strong>Pressure:</strong> <?= htmlspecialchars($data['pressure_mb']) ?> mb</p>
    <p><strong>Timestamp:</strong> <?= htmlspecialchars($data['timestamp']) ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($data['storm_type']) ?> (<?= htmlspecialchars($data['nature']) ?>)</p>
    <p><strong>Track Type:</strong> <?= htmlspecialchars($data['track_type']) ?></p>
    <p><strong>Track Points:</strong> <?= htmlspecialchars($data['track_points']) ?></p>
    <p><strong>Start Date:</strong> <?= htmlspecialchars($data['start_date']) ?></p>
    <p><strong>End Date:</strong> <?= htmlspecialchars($data['end_date']) ?></p>
    <p><strong>Landfall Count:</strong> <?= htmlspecialchars($data['landfall_count']) ?></p>
    <p><strong>Max Wind:</strong> <?= htmlspecialchars($data['max_wind_kts']) ?> kts</p>
    <p><strong>Min Pressure:</strong> <?= htmlspecialchars($data['min_pressure_mb']) ?> mb</p>
    <p><strong>Comments:</strong> <?= nl2br(htmlspecialchars($data['comments'])) ?></p>
    <p><strong>Storm Number:</strong> <?= htmlspecialchars($data['storm_num']) ?></p>
    <p><strong>Season Year:</strong> <?= htmlspecialchars($data['season_year']) ?></p>
    <p><strong>Classification:</strong> <?= htmlspecialchars($data['storm_classification']) ?></p>
    <p><strong>Source Notes:</strong> <?= nl2br(htmlspecialchars($data['source_notes'])) ?></p>

    <a href="ibtracs_admin.php">Back</a>
</body>
</html>

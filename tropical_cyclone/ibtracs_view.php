<?php
require_once '../php/db.php';
require_once '../php/function.php';

$db = new DBFunc((new DBConn())->getConnection());

if (!isset($_GET['sid'])) {
    echo "❌ Missing SID";
    exit();
}

$sid = htmlspecialchars($_GET['sid']);
$data = $db->showIBTracsStorm($sid);

if (!$data) {
    echo "❌ Storm not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IBTrACS Storm: <?= htmlspecialchars($data['name']) ?></title>
    <link rel="stylesheet" href="../css/master.css">
</head>
<body>
<div class="container">
    <h1 class="title">🌀 <?= htmlspecialchars($data['name']) ?> (<?= htmlspecialchars($data['sid']) ?>)</h1>

    <div class="info-box">
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
        <p><strong>Storm Number:</strong> <?= htmlspecialchars($data['storm_num']) ?></p>
        <p><strong>Season Year:</strong> <?= htmlspecialchars($data['season_year']) ?></p>
        <p><strong>Classification:</strong> <?= htmlspecialchars($data['storm_classification']) ?></p>
        <p><strong>Comments:</strong><br> <?= nl2br(htmlspecialchars($data['comments'])) ?></p>
        <p><strong>Source Notes:</strong><br> <?= nl2br(htmlspecialchars($data['source_notes'])) ?></p>
    </div>

    <div class="button-group">
        <a class="btn btn-secondary" href="ibtracs_admin.php">⬅ Back</a>
    </div>
</div>
</body>
</html>

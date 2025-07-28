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
    <title>IBTrACS Storm: <?= htmlspecialchars($data['name']) ?> (<?= $sid ?>)</title>
    <link rel="stylesheet" href="../css/master.css">
</head>
<body>
<div class="container">
    <h1 class="title">🌀 <?= htmlspecialchars($data['name']) ?> <small>(<?= htmlspecialchars($data['sid']) ?>)</small></h1>

    <div class="info-grid">
        <div><strong>Basin:</strong> <?= htmlspecialchars($data['basin']) ?></div>
        <div><strong>Agency:</strong> <?= htmlspecialchars($data['agency']) ?></div>

        <div><strong>Latitude:</strong> <?= htmlspecialchars($data['lat']) ?></div>
        <div><strong>Longitude:</strong> <?= htmlspecialchars($data['lon']) ?></div>

        <div><strong>Wind:</strong> <?= htmlspecialchars($data['wind_kts']) ?> kts</div>
        <div><strong>Pressure:</strong> <?= htmlspecialchars($data['pressure_mb']) ?> mb</div>

        <div><strong>Timestamp:</strong> <?= htmlspecialchars($data['timestamp']) ?></div>
        <div><strong>Type:</strong> <?= htmlspecialchars($data['storm_type']) ?> (<?= htmlspecialchars($data['nature']) ?>)</div>

        <div><strong>Track Type:</strong> <?= htmlspecialchars($data['track_type']) ?></div>
        <div><strong>Track Points:</strong> <?= htmlspecialchars($data['track_points']) ?></div>

        <div><strong>Start Date:</strong> <?= htmlspecialchars($data['start_date']) ?></div>
        <div><strong>End Date:</strong> <?= htmlspecialchars($data['end_date']) ?></div>

        <div><strong>Landfall Count:</strong> <?= htmlspecialchars($data['landfall_count']) ?></div>
        <div><strong>Max Wind:</strong> <?= htmlspecialchars($data['max_wind_kts']) ?> kts</div>

        <div><strong>Min Pressure:</strong> <?= htmlspecialchars($data['min_pressure_mb']) ?> mb</div>
        <div><strong>Storm Number:</strong> <?= htmlspecialchars($data['storm_num']) ?></div>

        <div><strong>Season Year:</strong> <?= htmlspecialchars($data['season_year']) ?></div>
        <div><strong>Classification:</strong> <?= htmlspecialchars($data['storm_classification']) ?></div>
    </div>

    <div class="info-longtext">
        <h3>📝 Comments</h3>
        <p><?= nl2br(htmlspecialchars($data['comments'])) ?: '—' ?></p>

        <h3>📄 Source Notes</h3>
        <p><?= nl2br(htmlspecialchars($data['source_notes'])) ?: '—' ?></p>
    </div>

    <div class="button-group">
        <a class="btn btn-secondary" href="ibtracs_admin.php">⬅ Back to Admin</a>
        <a class="btn btn-primary" href="ibtracs_edit.php?sid=<?= urlencode($sid) ?>">✏️ Edit</a>
        <a class="btn btn-outline" href="ibtracs_pdf.php?sid=<?= urlencode($sid) ?>" target="_blank">📄 Download PDF</a>
    </div>
</div>
</body>
</html>

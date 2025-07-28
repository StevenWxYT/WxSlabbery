<?php
require_once '../php/db.php';
require_once '../php/function.php';

$db = new DBFunc((new DBConn())->getConnection());

if (!isset($_GET['id'])) {
    echo "❌ Missing ID";
    exit();
}

$id = $_GET['id'];
$data = $db->showIBTracsStorm($id);

if (!$data) {
    echo "❌ Storm not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->updateIBTracsStorm($id, $_POST);
    header("Location: ibtracs_admin.php?updated=1");
    exit();
}

// Format timestamp for datetime-local input
function formatForDatetimeLocal($datetime) {
    return $datetime ? date('Y-m-d\TH:i', strtotime($datetime)) : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit IBTrACS Storm</title>
    <link rel="stylesheet" href="../css/master.css">
</head>
<body>
<div class="container">
    <h1 class="title">✏️ Edit IBTrACS Storm</h1>

    <form method="POST" class="form-grid">

        <label class="form-label">SID:
            <input type="text" name="sid" class="form-control" value="<?= htmlspecialchars($data['sid']) ?>" required>
        </label>

        <label class="form-label">Name:
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>">
        </label>

        <label class="form-label">Basin:
            <input type="text" name="basin" class="form-control" value="<?= htmlspecialchars($data['basin']) ?>">
        </label>

        <label class="form-label">Agency:
            <input type="text" name="agency" class="form-control" value="<?= htmlspecialchars($data['agency']) ?>">
        </label>

        <label class="form-label">Latitude:
            <input type="number" name="lat" step="0.01" class="form-control" value="<?= htmlspecialchars($data['lat']) ?>">
        </label>

        <label class="form-label">Longitude:
            <input type="number" name="lon" step="0.01" class="form-control" value="<?= htmlspecialchars($data['lon']) ?>">
        </label>

        <label class="form-label">Wind (kts):
            <input type="number" name="wind_kts" class="form-control" value="<?= htmlspecialchars($data['wind_kts']) ?>">
        </label>

        <label class="form-label">Pressure (mb):
            <input type="number" name="pressure_mb" class="form-control" value="<?= htmlspecialchars($data['pressure_mb']) ?>">
        </label>

        <label class="form-label">Timestamp:
            <input type="datetime-local" name="timestamp" class="form-control" value="<?= formatForDatetimeLocal($data['timestamp']) ?>">
        </label>

        <label class="form-label">Storm Type:
            <input type="text" name="storm_type" class="form-control" value="<?= htmlspecialchars($data['storm_type']) ?>">
        </label>

        <label class="form-label">Nature:
            <input type="text" name="nature" class="form-control" value="<?= htmlspecialchars($data['nature']) ?>">
        </label>

        <label class="form-label">Track Type:
            <input type="text" name="track_type" class="form-control" value="<?= htmlspecialchars($data['track_type']) ?>">
        </label>

        <label class="form-label">Track Points:
            <input type="number" name="track_points" class="form-control" value="<?= htmlspecialchars($data['track_points']) ?>">
        </label>

        <label class="form-label">Start Date:
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($data['start_date']) ?>">
        </label>

        <label class="form-label">End Date:
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($data['end_date']) ?>">
        </label>

        <div class="button-group">
            <button type="submit" class="btn">💾 Update Storm</button>
            <a href="ibtracs_admin.php" class="btn btn-secondary">⬅ Cancel</a>
        </div>
    </form>
</div>
</body>
</html>

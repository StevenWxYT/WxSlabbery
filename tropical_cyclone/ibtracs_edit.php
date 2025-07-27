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
        <input name="sid" value="<?= htmlspecialchars($data['sid']) ?>" placeholder="SID" required>
        <input name="name" value="<?= htmlspecialchars($data['name']) ?>" placeholder="Name">
        <input name="basin" value="<?= htmlspecialchars($data['basin']) ?>" placeholder="Basin">
        <input name="agency" value="<?= htmlspecialchars($data['agency']) ?>" placeholder="Agency">
        <input name="lat" value="<?= htmlspecialchars($data['lat']) ?>" placeholder="Latitude">
        <input name="lon" value="<?= htmlspecialchars($data['lon']) ?>" placeholder="Longitude">
        <input name="wind_kts" type="number" value="<?= htmlspecialchars($data['wind_kts']) ?>" placeholder="Wind (kts)">
        <input name="pressure_mb" type="number" value="<?= htmlspecialchars($data['pressure_mb']) ?>" placeholder="Pressure (mb)">
        <input name="timestamp" value="<?= htmlspecialchars($data['timestamp']) ?>" placeholder="Timestamp (YYYY-MM-DD HH:MM:SS)">
        <input name="storm_type" value="<?= htmlspecialchars($data['storm_type']) ?>" placeholder="Storm Type">
        <input name="nature" value="<?= htmlspecialchars($data['nature']) ?>" placeholder="Nature">
        <input name="track_type" value="<?= htmlspecialchars($data['track_type']) ?>" placeholder="Track Type">
        <input name="track_points" type="number" value="<?= htmlspecialchars($data['track_points']) ?>" placeholder="Track Points">
        <label>Start Date: <input name="start_date" type="date" value="<?= htmlspecialchars($data['start_date']) ?>"></label>
        <label>End Date: <input name="end_date" type="date" value="<?= htmlspecialchars($data['end_date']) ?>"></label>

        <div class="button-group">
            <button type="submit" class="btn">💾 Update Storm</button>
            <a href="ibtracs_admin.php" class="btn btn-secondary">⬅ Cancel</a>
        </div>
    </form>
</div>
</body>
</html>

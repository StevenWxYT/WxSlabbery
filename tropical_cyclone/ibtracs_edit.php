<?php
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->conn;
$db = new DBFunc($conn);

if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }

$id = $_GET['id'];
$data = $db->showIBTracsStorm($id);
if (!$data) { echo "Storm not found."; exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->updateIBTracsStorm($id, $_POST);
    header("Location: ibtracs_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit IBTrACS Storm</title></head>
<body>
  <h1>Edit IBTrACS Storm</h1>
  <form method="POST">
    <input name="sid" value="<?= htmlspecialchars($data['sid']) ?>" placeholder="SID" required><br>
    <input name="name" value="<?= htmlspecialchars($data['name']) ?>" placeholder="Name"><br>
    <input name="basin" value="<?= htmlspecialchars($data['basin']) ?>" placeholder="Basin"><br>
    <input name="agency" value="<?= htmlspecialchars($data['agency']) ?>" placeholder="Agency"><br>
    <input name="lat" value="<?= htmlspecialchars($data['lat']) ?>" placeholder="Latitude"><br>
    <input name="lon" value="<?= htmlspecialchars($data['lon']) ?>" placeholder="Longitude"><br>
    <input name="wind_kts" type="number" value="<?= htmlspecialchars($data['wind_kts']) ?>" placeholder="Wind (kts)"><br>
    <input name="pressure_mb" type="number" value="<?= htmlspecialchars($data['pressure_mb']) ?>" placeholder="Pressure (mb)"><br>
    <input name="timestamp" value="<?= htmlspecialchars($data['timestamp']) ?>" placeholder="Timestamp"><br>
    <input name="storm_type" value="<?= htmlspecialchars($data['storm_type']) ?>" placeholder="Storm Type"><br>
    <input name="nature" value="<?= htmlspecialchars($data['nature']) ?>" placeholder="Nature"><br>
    <input name="track_type" value="<?= htmlspecialchars($data['track_type']) ?>" placeholder="Track Type"><br>
    <input name="track_points" type="number" value="<?= htmlspecialchars($data['track_points']) ?>" placeholder="Track Points"><br>
    <input name="start_date" type="date" value="<?= htmlspecialchars($data['start_date']) ?>"><br>
    <input name="end_date" type="date" value="<?= htmlspecialchars($data['end_date']) ?>"><br>
    <button type="submit">Update Storm</button>
  </form>
  <a href="ibtracs_admin.php">Back</a>
</body>
</html>

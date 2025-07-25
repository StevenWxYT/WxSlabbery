<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }

$data = $db->showIBTracsStorm($_GET['id']);
if (!$data) { echo "Storm not found."; exit(); }
?>

<!DOCTYPE html>
<html>
<head><title>View IBTrACS Storm</title></head>
<body>
  <h1><?= htmlspecialchars($data['name']) ?> (<?= $data['sid'] ?>)</h1>
  <p><strong>Basin:</strong> <?= $data['basin'] ?></p>
  <p><strong>Agency:</strong> <?= $data['agency'] ?></p>
  <p><strong>Latitude:</strong> <?= $data['lat'] ?></p>
  <p><strong>Longitude:</strong> <?= $data['lon'] ?></p>
  <p><strong>Wind:</strong> <?= $data['wind_kts'] ?> kts</p>
  <p><strong>Pressure:</strong> <?= $data['pressure_mb'] ?> mb</p>
  <p><strong>Timestamp:</strong> <?= $data['timestamp'] ?></p>
  <p><strong>Type:</strong> <?= $data['storm_type'] ?> (<?= $data['nature'] ?>)</p>
  <p><strong>Track Type:</strong> <?= $data['track_type'] ?> | <strong>Points:</strong> <?= $data['track_points'] ?></p>
  <p><strong>Start-End:</strong> <?= $data['start_date'] ?> to <?= $data['end_date'] ?></p>
  <a href="ibtracs_admin.php">Back</a>
</body>
</html>

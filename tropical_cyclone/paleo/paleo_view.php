<?php
require_once '../../php/function.php';

$db = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }

$data = $db->showPaleoStorm($_GET['id']);
if (!$data) { echo "Record not found."; exit(); }
?>

<!DOCTYPE html>
<html>
<head><title>View Paleo Storm</title></head>
<body>
  <h1><?= htmlspecialchars($data['name']) ?> (<?= $data['year'] ?>)</h1>
  <p><strong>Region:</strong> <?= $data['region'] ?></p>
  <p><strong>Source Type:</strong> <?= $data['source_type'] ?></p>
  <p><strong>Estimated Intensity:</strong> <?= $data['intensity_estimate'] ?></p>
  <p><strong>Description:</strong><br><?= nl2br($data['description']) ?></p>
  <p><strong>Location Evidence:</strong> <?= $data['location_found'] ?></p>
  <a href="paleo_admin.php">Back</a>
</body>
</html>

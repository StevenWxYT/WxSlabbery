<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }

$data = $db->showTornado($_GET['id']);
if (!$data) { echo "Tornado not found."; exit(); }
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Tornado</title>
  <link rel="stylesheet" href="../master.css">
</head>
<body>
  <h1>🌪️ Tornado Details</h1>
  <p><strong>Date:</strong> <?= $data['date'] ?></p>
  <p><strong>Location:</strong> <?= $data['location'] ?></p>
  <p><strong>F-Scale:</strong> <?= $data['f_scale'] ?></p>
  <p><strong>Fatalities:</strong> <?= $data['fatalities'] ?></p>
  <p><strong>Description:</strong><br><?= nl2br($data['description']) ?></p>

  <a class="btn" href="tornado_admin.php">⬅ Back</a>
</body>
</html>

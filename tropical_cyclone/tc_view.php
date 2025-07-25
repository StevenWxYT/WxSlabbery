<?php
require_once '../php/function.php';

$functions = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }
$data = $functions->showDatabase($_GET['id']);
if (!$data) { echo "Cyclone not found."; exit(); }
?>

<!DOCTYPE html>
<html>
<head><title>View Cyclone</title></head>
<body>
  <h1><?= htmlspecialchars($data['name']) ?></h1>
  <p><strong>Basin:</strong> <?= $data['basin'] ?></p>
  <p><strong>Wind:</strong> <?= $data['wind_speed'] ?> kts</p>
  <p><strong>Pressure:</strong> <?= $data['pressure'] ?> mb</p>
  <p><strong>Date:</strong> <?= $data['start_date'] ?> to <?= $data['end_date'] ?></p>
  <p><strong>Fatalities:</strong> <?= $data['fatalities'] ?></p>
  <p><strong>Damages:</strong> <?= $data['damages'] ?></p>
  <p><strong>ACE:</strong> <?= $data['ace'] ?></p>
  <?php if (!empty($data['image'])): ?>
    <p><img src="<?= $data['image'] ?>" width="300"></p>
  <?php endif; ?>
  <a href="tc_admin.php">Back</a>
</body>
</html>

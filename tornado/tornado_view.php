<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);
if (!isset($_GET['id'])) {
    echo "Missing ID";
    exit();
}

$data = $db->getTornadoById($_GET['id']);
if (!$data) {
    echo "Tornado not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Tornado</title>
  <link rel="stylesheet" href="../master.css">
</head>
<body>
  <h1>🌪️ Tornado Details</h1>
  <div class="tornado-details">
    <p><strong>Date:</strong> <?= htmlspecialchars($data['date']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($data['tor_location']) ?></p>
    <p><strong>Fujita Rank:</strong> <?= htmlspecialchars($data['fujita_rank']) ?></p>
    <p><strong>Wind Speed:</strong> <?= htmlspecialchars($data['wind_speed']) ?> mph</p>
    <p><strong>Max Width:</strong> <?= htmlspecialchars($data['max_width']) ?> m</p>
    <p><strong>Distance Traveled:</strong> <?= htmlspecialchars($data['distance']) ?> km</p>
    <p><strong>Duration:</strong> <?= htmlspecialchars($data['duration']) ?> minutes</p>

    <?php if (!empty($data['image'])): ?>
      <p><strong>Image:</strong><br>
        <img src="<?= htmlspecialchars($data['image']) ?>" alt="Tornado Image" style="max-width:400px; height:auto;">
      </p>
    <?php endif; ?>
  </div>
  
  <a class="btn" href="tornado_admin.php">⬅ Back</a>
</body>
</html>

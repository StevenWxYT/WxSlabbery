<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Missing or invalid ID";
    exit();
}

$data = $db->getTornadoById($id);
if (!$data) {
    echo "Tornado not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>🌪️ Tornado Details</title>
  <link rel="stylesheet" href="../master.css">
</head>
<body>

  <h1>🌪️ Tornado Details</h1>

  <div class="admin-buttons">
    <a class="btn" href="tornado_admin.php">📄 All Tornadoes</a>
    <a class="btn" href="tornado_create.php">➕ Add New</a>
    <a class="btn" href="tornado_edit.php?id=<?= $id ?>">✏️ Edit This</a>
    <a class="btn" href="../index.php">⬅ Back to Dashboard</a>
  </div>

  <div class="tornado-details form-box">
    <p><strong>📅 Date:</strong> <?= htmlspecialchars($data['date']) ?></p>
    <p><strong>📍 Location:</strong> <?= htmlspecialchars($data['tor_location']) ?></p>
    <p><strong>💨 Fujita Rank:</strong> <?= htmlspecialchars($data['fujita_rank']) ?></p>
    <p><strong>🌬 Wind Speed:</strong> <?= htmlspecialchars($data['wind_speed']) ?> mph</p>
    <p><strong>📏 Max Width:</strong> <?= htmlspecialchars($data['max_width']) ?> m</p>
    <p><strong>📍 Distance Traveled:</strong> <?= htmlspecialchars($data['distance']) ?> km</p>
    <p><strong>⏱ Duration:</strong> <?= htmlspecialchars($data['duration']) ?> minutes</p>

    <?php if (!empty($data['image'])): ?>
      <p><strong>🖼 Image:</strong><br>
        <img src="<?= htmlspecialchars($data['image']) ?>" alt="Tornado Image" style="max-width: 400px; height: auto; border: 1px solid #ccc; border-radius: 5px;">
      </p>
    <?php endif; ?>
  </div>

</body>
</html>

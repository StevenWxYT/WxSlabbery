<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Initialize DB connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

// Fetch tornado records
$tornadoes = $db->getAllTornadoes();
?>

<!DOCTYPE html>
<html>
<head>
  <title>🌪 Tornado Records</title>
  <link rel="stylesheet" href="../master.css">
</head>
<body>
  <h1>🌪 Tornado Records</h1>

  <!-- ✅ CRUD Buttons -->
  <div style="margin-bottom: 15px;">
    <a class="btn" href="tornado_add.php">➕ Add Tornado</a>
    <a class="btn btn-alt" href="tornado_index.php">🏠 Back to Index</a>
  </div>

  <table class="table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Location</th>
        <th>Fujita Scale</th>
        <th>Wind Speed (kts)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $tornadoes->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['date']) ?></td>
          <td><?= htmlspecialchars($row['tor_location']) ?></td>
          <td><?= htmlspecialchars($row['fujita_rank']) ?></td>
          <td><?= htmlspecialchars($row['wind_speed']) ?></td>
          <td>
            <a class="btn btn-sm" href="tornado_view.php?id=<?= $row['id'] ?>">View</a>
            <a class="btn btn-sm" href="tornado_edit.php?id=<?= $row['id'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger" href="tornado_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this tornado record?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>

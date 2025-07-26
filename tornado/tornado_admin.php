<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
$tornadoes = $conn->query("SELECT * FROM tornado_db ORDER BY date DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>All Tornado Records</title>
  <link rel="stylesheet" href="../master.css">
</head>
<body>
  <h1>📄 Tornado Records</h1>
  <table class="table">
    <thead>
      <tr>
        <th>Date</th><th>Location</th><th>F-Scale</th><th>Deaths</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $tornadoes->fetch_assoc()): ?>
      <tr>
        <td><?= $row['date'] ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= $row['f_scale'] ?></td>
        <td><?= $row['fatalities'] ?></td>
        <td>
          <a class="btn btn-sm" href="tornado_view.php?id=<?= $row['id'] ?>">View</a>
          <a class="btn btn-sm" href="tornado_edit.php?id=<?= $row['id'] ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="tornado_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  <a class="btn" href="tornado_index.php">⬅ Back</a>
</body>
</html>

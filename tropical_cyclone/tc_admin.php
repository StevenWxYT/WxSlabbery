<?php
require_once '../php/function.php';

$functions = new DBFunc($conn);
$cyclones = $conn->query("SELECT * FROM tcdatabase ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cyclone Admin</title>
</head>
<body>
  <h1>Tropical Cyclone Admin</h1>
  <a href="tc_add.php">Add New Cyclone</a>
  <table border="1">
    <tr>
      <th>ID</th><th>Name</th><th>Basin</th><th>Start</th><th>End</th><th>Actions</th>
    </tr>
    <?php while ($row = $cyclones->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= $row['basin'] ?></td>
      <td><?= $row['start_date'] ?></td>
      <td><?= $row['end_date'] ?></td>
      <td>
        <a href="tc_view.php?id=<?= $row['id'] ?>">View</a> |
        <a href="tc_edit.php?id=<?= $row['id'] ?>">Edit</a> |
        <a href="tc_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>

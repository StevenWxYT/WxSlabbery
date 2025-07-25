<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
$storms = $conn->query("SELECT * FROM IBTrACS_Storms ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html>
<head><title>IBTrACS Admin</title></head>
<body>
  <h1>IBTrACS Storm Records</h1>
  <table border="1">
    <tr>
      <th>SID</th><th>Name</th><th>Basin</th><th>Agency</th><th>Start</th><th>Type</th><th>Action</th>
    </tr>
    <?php while ($row = $storms->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['sid']) ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= $row['basin'] ?></td>
      <td><?= $row['agency'] ?></td>
      <td><?= $row['start_date'] ?></td>
      <td><?= $row['storm_type'] ?></td>
      <td>
        <a href="ibtracs_view.php?id=<?= $row['id'] ?>">View</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <a href="../index.php">Back to Home</a>
</body>
</html>

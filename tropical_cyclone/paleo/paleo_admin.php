<?php
require_once '../../php/function.php';

$db = new DBFunc($conn);
$storms = $conn->query("SELECT * FROM paleo_storms ORDER BY year DESC");
?>

<!DOCTYPE html>
<html>
<head><title>Paleotempestology Records</title></head>
<body>
  <h1>Paleotempestology Storm Records</h1>
  <table border="1">
    <tr>
      <th>Name</th><th>Year</th><th>Region</th><th>Source</th><th>Category</th><th>Action</th>
    </tr>
    <?php while ($row = $storms->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= $row['year'] ?></td>
      <td><?= $row['region'] ?></td>
      <td><?= $row['source_type'] ?></td>
      <td><?= $row['intensity_estimate'] ?></td>
      <td><a href="paleo_view.php?id=<?= $row['id'] ?>">View</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
  <a href="../tc_admin.php">Back</a>
</body>
</html>

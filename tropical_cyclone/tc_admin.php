<?php
require_once '../php/db.php';         // Creates $conn
require_once '../php/function.php';  // Loads DBFunc class

// Create the DBConn and extract the connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();

// Now pass it to your DBFunc class
$functions = new DBFunc($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tropical Cyclone Admin</title>
  <link rel="stylesheet" href="../master.css">
</head>
<body>

  <h1>🌪️ Tropical Cyclone Admin</h1>

  <a class="button" href="tc_add.php">➕ Add Cyclone</a>
  <a class="button" href="tc_index.php">⬅ Back to TC Index</a>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Storm ID</th>
        <th>Name</th>
        <th>Basin</th>
        <th>Wind (kt)</th>
        <th>Pressure (mb)</th>
        <th>Start</th>
        <th>End</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $result = $conn->query("SELECT * FROM tcdatabase ORDER BY start_date DESC");
        if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
      ?>
        <tr>
          <td><?= htmlspecialchars($row['id']) ?></td>
          <td><?= htmlspecialchars($row['storm_id']) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['basin']) ?></td>
          <td><?= htmlspecialchars($row['wind_speed']) ?></td>
          <td><?= htmlspecialchars($row['pressure']) ?></td>
          <td><?= htmlspecialchars($row['start_date']) ?></td>
          <td><?= htmlspecialchars($row['end_date']) ?></td>
          <td>
            <a href="tc_view.php?id=<?= $row['id'] ?>">🔍 View</a> |
            <a href="tc_edit.php?id=<?= $row['id'] ?>">✏️ Edit</a> |
            <a href="tc_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">🗑️ Delete</a>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="9">No cyclone records found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>

<?php
require_once '../php/db.php';
require_once '../php/function.php';

// DB initialization
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
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

  <?php if (isset($_GET['status'])): ?>
    <div class="alert <?= $_GET['status'] === 'deleted' ? 'success' : 'error' ?>">
      <?php
        switch ($_GET['status']) {
          case 'deleted':
            echo "✅ Cyclone deleted successfully.";
            break;
          case 'error':
            echo "❌ Something went wrong.";
            break;
          // Future cases for ?status=added, updated etc.
        }
      ?>
    </div>
  <?php endif; ?>

  <div class="admin-buttons">
    <a class="btn" href="tc_add.php">➕ Add Cyclone</a>
    <a class="btn" href="tc_index.php">⬅ Back to TC Index</a>
    <a class="btn" href="tc_export.php?format=csv" target="_blank">📁 Export CSV</a>
    <a class="btn" href="tc_export.php?format=pdf" target="_blank">📄 Export PDF</a>
  </div>

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
            <a class="view-btn" href="tc_view.php?id=<?= $row['id'] ?>">🔍 View</a>
            <a class="edit-btn" href="tc_edit.php?id=<?= $row['id'] ?>">✏️ Edit</a>
            <a class="delete-btn" href="tc_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">🗑️ Delete</a>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr>
          <td colspan="9">No cyclone records found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>

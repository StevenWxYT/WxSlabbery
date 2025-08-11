<?php
require_once '../php/db.php';
require_once '../php/function.php';

// DB initialization
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$functions = new DBFunc($conn);

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filter parameters
$nameFilter = $_GET['name'] ?? '';
$basinFilter = $_GET['basin'] ?? '';
$yearFilter = $_GET['year'] ?? '';

// Basin definitions
$basins = [
  'NATL'  => 'North Atlantic',
  'EPAC'  => 'East Pacific',
  'CPAC'  => 'Central Pacific',
  'WPAC'  => 'West Pacific',
  'NIO'   => 'North Indian Ocean',
  'SIO'   => 'South Indian Ocean',
  'AU'    => 'Australian Region',
  'SPAC'  => 'South Pacific',
  'SEPAC' => 'Southeast Pacific',
  'MED'   => 'Mediterranean/Black Sea'
];

// WHERE clause builder
$where = [];
if (!empty($nameFilter)) {
  $where[] = "name LIKE '%" . $conn->real_escape_string($nameFilter) . "%'";
}
if (!empty($basinFilter)) {
  $where[] = "basin = '" . $conn->real_escape_string($basinFilter) . "'";
}
if (!empty($yearFilter)) {
  $yearEscaped = $conn->real_escape_string($yearFilter);
  $where[] = "YEAR(start_date) = '$yearEscaped'";
}
$whereClause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total for pagination
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM tcdatabase $whereClause");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $perPage);

// Final query
$result = $conn->query("SELECT * FROM tcdatabase $whereClause ORDER BY start_date DESC LIMIT $offset, $perPage");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Tropical Cyclone Admin</title>
  <link rel="icon" type="image/png" href="./img/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="./img/favicon.svg" />
  <link rel="shortcut icon" href="./img/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="./img/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="StevenWx" />
  <link rel="manifest" href="./img/site.webmanifest" />
  <link rel="stylesheet" href="../master.css">
  <style>
    img.thumb {
      height: 50px;
      border-radius: 4px;
    }

    .thumb-na {
      color: gray;
      font-size: 0.9em;
    }
  </style>
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
      }
      ?>
    </div>
  <?php endif; ?>

  <div class="admin-buttons">
    <a class="btn" href="tc_index.php">⬅ Back to TC Index</a>
    <a class="btn" href="tc_add.php">➕ Add Cyclone</a>
    <a class="btn" href="tc_export.php?format=csv" target="_blank">📁 Export CSV</a>
    <a class="btn" href="tc_export.php?format=pdf" target="_blank">📄 Export PDF</a>
  </div>

  <form method="GET" class="filter-form">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($nameFilter) ?>">

    <label for="basin">Basin:</label>
    <select id="basin" name="basin">
      <option value="">-- Select Basin --</option>
      <?php foreach ($basins as $code => $label): ?>
        <option value="<?= htmlspecialchars($code) ?>" <?= $basinFilter === $code ? 'selected' : '' ?>>
          <?= htmlspecialchars($label) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label for="year">Year:</label>
    <input type="number" id="year" name="year" value="<?= htmlspecialchars($yearFilter) ?>">

    <button type="submit" class="btn">🔎 Filter</button>
    <a href="tc_admin.php" class="btn">🔁 Reset</a>
  </form>

  <table>
    <thead>
      <tr>
        <th>Best Track</th>
        <th>Satellite</th>
        <th>Storm ID</th>
        <th>Name</th>
        <th>Basin</th>
        <th>Wind (kt)</th>
        <th>Pressure (mb)</th>
        <th>Start</th>
        <th>End</th>
        <th>Fatalities</th>
        <th>Damages</th>
        <th>ACE</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <!-- Best Track -->
            <td>
              <?php if (!empty($row['image_best_track'])): ?>
                <a href="<?= htmlspecialchars($row['image_best_track']) ?>" target="_blank">
                  <img src="<?= htmlspecialchars($row['image_best_track']) ?>" alt="Best Track" class="thumb">
                </a>
              <?php else: ?>
                <span class="thumb-na">N/A</span>
              <?php endif; ?>
            </td>

            <!-- Satellite -->
            <td>
              <?php if (!empty($row['image_satellite'])): ?>
                <a href="<?= htmlspecialchars($row['image_satellite']) ?>" target="_blank">
                  <img src="<?= htmlspecialchars($row['image_satellite']) ?>" alt="Satellite" class="thumb">
                </a>
              <?php else: ?>
                <span class="thumb-na">N/A</span>
              <?php endif; ?>
            </td>

            <!-- Cyclone data -->
            <td><?= htmlspecialchars($row['storm_id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['basin']) ?></td>
            <td><?= htmlspecialchars($row['wind_speed']) ?>mph</td>
            <td><?= htmlspecialchars($row['pressure']) ?>mbar</td>
            <td><?= htmlspecialchars($row['start_date']) ?></td>
            <td><?= htmlspecialchars($row['end_date']) ?></td>
            <td><?= htmlspecialchars($row['fatalities']) ?></td>
            <td>USD $<?= htmlspecialchars($row['damages']) ?></td>
            <td><?= htmlspecialchars($row['ace']) ?></td>

            <!-- Actions -->
            <td>
              <a class="view-btn" href="tc_view.php?id=<?= $row['id'] ?>">🔍 View</a>
              <a class="edit-btn" href="tc_edit.php?id=<?= $row['id'] ?>">✏️ Edit</a>
              <a class="delete-btn" href="tc_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this record?');">🗑️ Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="13">No cyclone records found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php if ($totalPages > 1): ?>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="page-link <?= $i === $page ? 'active' : '' ?>" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    <?php endif; ?>
  </div>

</body>

</html>
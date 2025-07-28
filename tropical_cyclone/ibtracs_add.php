<?php
require_once '../php/db.php';
require_once '../php/function.php';

$db = new DBFunc((new DBConn())->getConnection());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->insertIBTracsStorm($_POST);
    header("Location: ibtracs_admin.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add IBTrACS Storm</title>
  <link rel="stylesheet" href="../css/master.css">
</head>
<body>
  <div class="container">
    <h1 class="title">➕ Add IBTrACS Storm</h1>

    <form method="POST" class="form-grid">

      <label class="form-label">SID:
        <input type="text" name="sid" class="form-control" required>
      </label>

      <label class="form-label">Name:
        <input type="text" name="name" class="form-control">
      </label>

      <label class="form-label">Basin:
        <input type="text" name="basin" class="form-control">
      </label>

      <label class="form-label">Agency:
        <input type="text" name="agency" class="form-control">
      </label>

      <label class="form-label">Latitude:
        <input type="number" name="lat" step="0.01" class="form-control">
      </label>

      <label class="form-label">Longitude:
        <input type="number" name="lon" step="0.01" class="form-control">
      </label>

      <label class="form-label">Wind Speed (kts):
        <input type="number" name="wind_kts" class="form-control">
      </label>

      <label class="form-label">Pressure (mb):
        <input type="number" name="pressure_mb" class="form-control">
      </label>

      <label class="form-label">Timestamp:
        <input type="datetime-local" name="timestamp" class="form-control">
      </label>

      <label class="form-label">Storm Type:
        <input type="text" name="storm_type" class="form-control">
      </label>

      <label class="form-label">Nature:
        <input type="text" name="nature" class="form-control">
      </label>

      <label class="form-label">Track Type:
        <input type="text" name="track_type" class="form-control">
      </label>

      <label class="form-label">Track Points:
        <input type="number" name="track_points" class="form-control">
      </label>

      <label class="form-label">Start Date:
        <input type="date" name="start_date" class="form-control">
      </label>

      <label class="form-label">End Date:
        <input type="date" name="end_date" class="form-control">
      </label>

      <div class="button-group">
        <button type="submit" class="btn">✅ Add Storm</button>
        <a href="ibtracs_admin.php" class="btn btn-secondary">⬅ Back to Admin</a>
      </div>

    </form>
  </div>
</body>
</html>

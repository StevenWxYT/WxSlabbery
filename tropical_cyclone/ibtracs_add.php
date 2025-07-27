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
      <input name="sid" placeholder="SID" required>
      <input name="name" placeholder="Name">
      <input name="basin" placeholder="Basin">
      <input name="agency" placeholder="Agency">
      <input name="lat" placeholder="Latitude">
      <input name="lon" placeholder="Longitude">
      <input name="wind_kts" type="number" placeholder="Wind (kts)">
      <input name="pressure_mb" type="number" placeholder="Pressure (mb)">
      <input name="timestamp" placeholder="Timestamp (YYYY-MM-DD HH:MM:SS)">
      <input name="storm_type" placeholder="Storm Type">
      <input name="nature" placeholder="Nature">
      <input name="track_type" placeholder="Track Type">
      <input name="track_points" type="number" placeholder="Track Points">
      <label>Start Date: <input name="start_date" type="date"></label>
      <label>End Date: <input name="end_date" type="date"></label>

      <div class="button-group">
        <button type="submit" class="btn">✅ Add Storm</button>
        <a href="ibtracs_admin.php" class="btn btn-secondary">⬅ Back to Admin</a>
      </div>
    </form>
  </div>
</body>
</html>

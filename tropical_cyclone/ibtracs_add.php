<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $db->insertIBTracsStorm($_POST);
}
?>

<!DOCTYPE html>
<html>
<head><title>Add IBTrACS Storm</title></head>
<body>
  <h1>Add IBTrACS Storm</h1>
  <form method="POST">
    <input name="sid" placeholder="SID" required><br>
    <input name="name" placeholder="Name"><br>
    <input name="basin" placeholder="Basin"><br>
    <input name="agency" placeholder="Agency"><br>
    <input name="lat" placeholder="Latitude"><br>
    <input name="lon" placeholder="Longitude"><br>
    <input name="wind_kts" type="number" placeholder="Wind (kts)"><br>
    <input name="pressure_mb" type="number" placeholder="Pressure (mb)"><br>
    <input name="timestamp" placeholder="Timestamp"><br>
    <input name="storm_type" placeholder="Storm Type"><br>
    <input name="nature" placeholder="Nature"><br>
    <input name="track_type" placeholder="Track Type"><br>
    <input name="track_points" type="number" placeholder="Track Points"><br>
    <input name="start_date" type="date"><br>
    <input name="end_date" type="date"><br>
    <button type="submit">Add Storm</button>
  </form>
  <a href="ibtracs_admin.php">Back</a>
</body>
</html>

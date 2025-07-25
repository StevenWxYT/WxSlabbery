<?php
require_once '../php/function.php';

$functions = new DbFunc($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $functions->insertDatabase(
    $_POST['storm_id'], $_POST['name'], $_POST['basin'],
    $_POST['wind_speed'], $_POST['pressure'],
    $_POST['start_date'], $_POST['end_date'],
    $_POST['fatalities'], $_POST['damages'], $_POST['ace'],
    $_FILES['image']
  );
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Cyclone</title></head>
<body>
  <h1>Add Tropical Cyclone</h1>
  <form method="POST" enctype="multipart/form-data">
    <input name="storm_id" placeholder="Storm ID" required><br>
    <input name="name" placeholder="Name" required><br>
    <input name="basin" placeholder="Basin" required><br>
    <input name="wind_speed" type="number" placeholder="Wind Speed (kt)" required><br>
    <input name="pressure" type="number" placeholder="Pressure (mb)" required><br>
    <input name="start_date" type="date" required><br>
    <input name="end_date" type="date" required><br>
    <input name="fatalities" type="number" placeholder="Fatalities" required><br>
    <input name="damages" placeholder="Damages" required><br>
    <input name="ace" type="number" step="0.01" placeholder="ACE" required><br>
    <input type="file" name="image"><br><br>
    <button type="submit">Add Cyclone</button>
  </form>
  <a href="tc_admin.php">Back</a>
</body>
</html>

<?php
require_once '../../php/function.php';

$db = new DBFunc($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $db->insertPaleoStorm($_POST);
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Paleo Storm</title></head>
<body>
  <h1>Add Paleotempest Storm</h1>
  <form method="POST">
    <input name="name" placeholder="Storm Name"><br>
    <input name="year" type="number" placeholder="Approx. Year" required><br>
    <input name="region" placeholder="Region"><br>
    <input name="source_type" placeholder="Source Type (e.g. Sediment, Tree Rings)"><br>
    <input name="intensity_estimate" placeholder="Estimated Intensity (e.g. Cat 4)"><br>
    <input name="location_found" placeholder="Location Found"><br>
    <textarea name="description" placeholder="Details, evidence, source..."></textarea><br>
    <button type="submit">Add Record</button>
  </form>
  <a href="paleo_admin.php">Back</a>
</body>
</html>

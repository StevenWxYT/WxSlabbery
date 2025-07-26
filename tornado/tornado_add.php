<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $db->insertTornado($_POST);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Tornado</title>
  <link rel="stylesheet" href="../master.css">
</head>
<body>
  <h1>➕ Add Tornado</h1>
  <form method="POST">
    <input type="date" name="date" required><br>
    <input type="text" name="location" placeholder="Location" required><br>
    <input type="text" name="f_scale" placeholder="F-Scale (e.g. F3)" required><br>
    <input type="number" name="fatalities" placeholder="Fatalities"><br>
    <textarea name="description" placeholder="Details..."></textarea><br>
    <button class="btn" type="submit">Add Tornado</button>
  </form>
  <a class="btn" href="tornado_index.php">⬅ Back</a>
</body>
</html>

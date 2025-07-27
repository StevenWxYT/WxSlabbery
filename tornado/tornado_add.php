<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tor_location = $_POST['tor_location'] ?? '';
  $date = $_POST['date'] ?? '';
  $fujita_rank = $_POST['fujita_rank'] ?? '';
  $wind_speed = $_POST['wind_speed'] ?? 0;
  $max_width = $_POST['max_width'] ?? 0;
  $distance = $_POST['distance'] ?? 0;
  $duration = $_POST['duration'] ?? '';
  $file = $_FILES['image'] ?? null;

  $success = $db->insertTornado($tor_location, $date, $fujita_rank, $wind_speed, $max_width, $distance, $duration, $file);

  if ($success) {
    header("Location: tornado_admin.php");
    exit();
  }
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
  <form method="POST" enctype="multipart/form-data">
    <label>Date:</label><br>
    <input type="date" name="date" required><br>

    <label>Location:</label><br>
    <input type="text" name="tor_location" placeholder="e.g. Moore, OK" required><br>

    <label>Fujita Rank:</label><br>
    <input type="text" name="fujita_rank" placeholder="e.g. EF3" required><br>

    <label>Wind Speed (knots):</label><br>
    <input type="number" name="wind_speed" step="0.1"><br>

    <label>Max Width (meters):</label><br>
    <input type="number" name="max_width" step="0.1"><br>

    <label>Distance Traveled (km):</label><br>
    <input type="number" name="distance" step="0.1"><br>

    <label>Duration (e.g. 1h 15m):</label><br>
    <input type="text" name="duration"><br>

    <label>Optional Image:</label><br>
    <input type="file" name="image" accept="image/*"><br><br>

    <button class="btn" type="submit">✅ Add Tornado</button>
  </form>

  <a class="btn" href="tornado_index.php">⬅ Back</a>
</body>
</html>

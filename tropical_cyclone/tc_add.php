<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Connect to DB
$dbConn = new DBConn();
$conn = $dbConn->getConnection();

$functions = new DbFunc($conn);

// Handle form submission
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Tropical Cyclone</title>
    <link rel="stylesheet" href="../master.css">
</head>
<body>

<div class="form-container">
    <h1>🌪️ Add Tropical Cyclone</h1>

    <form method="POST" enctype="multipart/form-data">
        <input name="storm_id" placeholder="Storm ID" required>
        <input name="name" placeholder="Name" required>
        <input name="basin" placeholder="Basin" required>
        <input name="wind_speed" type="number" placeholder="Wind Speed (kt)" required>
        <input name="pressure" type="number" placeholder="Pressure (mb)" required>
        <input name="start_date" type="date" required>
        <input name="end_date" type="date" required>
        <input name="fatalities" type="number" placeholder="Fatalities" required>
        <input name="damages" placeholder="Damages (USD or qualitative)" required>
        <input name="ace" type="number" step="0.01" placeholder="ACE (Accumulated Cyclone Energy)" required>
        <label>Storm Image (optional):</label>
        <input type="file" name="image">
        <button type="submit">➕ Add Cyclone</button>
    </form>

    <a class="back-link" href="tc_admin.php">⬅ Back to Cyclone Admin</a>
</div>

</body>
</html>

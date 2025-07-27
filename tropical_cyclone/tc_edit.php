<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Initialize DB connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$functions = new DbFunc($conn);

// Check if ID is present
if (!isset($_GET['id'])) {
    echo "Missing ID.";
    exit();
}

// Get data
$data = $functions->showDatabase($_GET['id']);
if (!$data) {
    echo "Cyclone not found.";
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $functions->updateDatabase(
        $_POST['storm_id'], $_POST['name'], $_POST['basin'],
        $_POST['wind_speed'], $_POST['pressure'],
        $_POST['start_date'], $_POST['end_date'],
        $_POST['fatalities'], $_POST['damages'], $_POST['ace'],
        $_GET['id'], $_FILES['image']
    );

    // Optional: reload updated data
    $data = $functions->showDatabase($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Cyclone - <?= htmlspecialchars($data['name']) ?></title>
    <link rel="stylesheet" href="../master.css">
</head>
<body>

<div class="form-container">
    <h1>🛠️ Edit Cyclone: <?= htmlspecialchars($data['name']) ?></h1>

    <form method="POST" enctype="multipart/form-data">
        <input name="storm_id" value="<?= htmlspecialchars($data['storm_id']) ?>" required>
        <input name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
        <input name="basin" value="<?= htmlspecialchars($data['basin']) ?>" required>
        <input name="wind_speed" type="number" value="<?= htmlspecialchars($data['wind_speed']) ?>" required>
        <input name="pressure" type="number" value="<?= htmlspecialchars($data['pressure']) ?>" required>
        <input name="start_date" type="date" value="<?= htmlspecialchars($data['start_date']) ?>" required>
        <input name="end_date" type="date" value="<?= htmlspecialchars($data['end_date']) ?>" required>
        <input name="fatalities" type="number" value="<?= htmlspecialchars($data['fatalities']) ?>" required>
        <input name="damages" value="<?= htmlspecialchars($data['damages']) ?>" required>
        <input name="ace" type="number" step="0.01" value="<?= htmlspecialchars($data['ace']) ?>" required>
        <label>Replace Image (optional):</label>
        <input type="file" name="image">
        <button type="submit">✅ Update Cyclone</button>
    </form>

    <a class="back-link" href="tc_admin.php">⬅ Back to Cyclone Admin</a>
</div>

</body>
</html>

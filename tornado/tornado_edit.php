<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

if (!isset($_GET['id'])) {
    echo "Missing ID";
    exit();
}

$data = $db->getTornadoById($_GET['id']);
if (!$data) {
    echo "Record not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->updateTornado(
        $_GET['id'],
        $_POST['tor_location'],
        $_POST['date'],
        $_POST['fujita_rank'],
        $_POST['wind_speed'],
        $_POST['max_width'],
        $_POST['distance'],
        $_POST['duration'],
        $_FILES['image'] ?? null
    );
    header("Location: tornado_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tornado</title>
    <link rel="stylesheet" href="../master.css">
</head>
<body>
    <h1>📝 Edit Tornado</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="tor_location" value="<?= htmlspecialchars($data['tor_location']) ?>" placeholder="Location" required><br>
        <input type="date" name="date" value="<?= $data['date'] ?>" required><br>
        <input type="text" name="fujita_rank" value="<?= $data['fujita_rank'] ?>" placeholder="Fujita Rank (e.g. F3)" required><br>
        <input type="number" step="0.1" name="wind_speed" value="<?= $data['wind_speed'] ?>" placeholder="Wind Speed (mph)"><br>
        <input type="number" step="0.1" name="max_width" value="<?= $data['max_width'] ?>" placeholder="Max Width (m)"><br>
        <input type="number" step="0.1" name="distance" value="<?= $data['distance'] ?>" placeholder="Distance (km)"><br>
        <input type="number" step="0.1" name="duration" value="<?= $data['duration'] ?>" placeholder="Duration (mins)"><br>
        
        <?php if (!empty($data['image'])): ?>
            <p>Current Image: <a href="<?= htmlspecialchars($data['image']) ?>" target="_blank">View</a></p>
        <?php endif; ?>
        
        <input type="file" name="image"><br>
        <button class="btn" type="submit">Update</button>
    </form>
    <a class="btn" href="tornado_admin.php">⬅ Back</a>
</body>
</html>

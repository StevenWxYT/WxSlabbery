<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

// Validate ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Missing or invalid ID.";
    exit();
}

$data = $db->getTornadoById($id);
if (!$data) {
    echo "Record not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->updateTornado(
        $id,
        $_POST['tor_location'],
        $_POST['date'],
        $_POST['fujita_rank'],
        $_POST['wind_speed'],
        $_POST['max_width'],
        $_POST['distance'],
        $_POST['duration'],
        $_FILES['image'] ?? null
    );
    header("Location: tornado_admin.php?msg=updated");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📝 Edit Tornado</title>
    <link rel="stylesheet" href="../master.css">
</head>
<body>

    <h1>📝 Edit Tornado Record</h1>

    <div class="admin-buttons">
        <a class="btn" href="tornado_admin.php">📄 All Records</a>
        <a class="btn" href="tornado_create.php">➕ Add New</a>
        <a class="btn" href="../index.php">⬅ Back to Dashboard</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="form-box">
        <label>📍 Location:</label>
        <input type="text" name="tor_location" value="<?= htmlspecialchars($data['tor_location']) ?>" required>

        <label>📅 Date:</label>
        <input type="date" name="date" value="<?= $data['date'] ?>" required>

        <label>💨 Fujita Rank:</label>
        <input type="text" name="fujita_rank" value="<?= $data['fujita_rank'] ?>" required>

        <label>🌬 Wind Speed (mph):</label>
        <input type="number" step="0.1" name="wind_speed" value="<?= $data['wind_speed'] ?>">

        <label>📏 Max Width (m):</label>
        <input type="number" step="0.1" name="max_width" value="<?= $data['max_width'] ?>">

        <label>📍 Distance Traveled (km):</label>
        <input type="number" step="0.1" name="distance" value="<?= $data['distance'] ?>">

        <label>⏱ Duration (minutes):</label>
        <input type="number" step="0.1" name="duration" value="<?= $data['duration'] ?>">

        <?php if (!empty($data['image'])): ?>
            <p>🖼 Current Image: <a href="<?= htmlspecialchars($data['image']) ?>" target="_blank">View Image</a></p>
        <?php endif; ?>

        <label>🖼 Upload New Image (optional):</label>
        <input type="file" name="image">

        <button class="btn" type="submit">💾 Update Tornado</button>
    </form>

</body>
</html>

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

$updateSuccess = null;

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateSuccess = $functions->updateDatabase(
        $_POST['storm_id'],
        $_POST['name'],
        $_POST['basin'],
        $_POST['wind_speed'],
        $_POST['pressure'],
        $_POST['start_date'],
        $_POST['end_date'],
        $_POST['fatalities'],
        $_POST['damages'],
        $_POST['ace'],
        $_GET['id'],
        $_FILES['image']
    );

    // Refresh form with updated data
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

        <?php if ($updateSuccess !== null): ?>
            <div class="status-message <?= $updateSuccess ? 'success' : 'error' ?>">
                <?= $updateSuccess ? '✅ Cyclone updated successfully!' : '❌ Update failed. Please try again.' ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input name="storm_id" type="text" value="<?= htmlspecialchars($data['storm_id']) ?>" placeholder="Storm ID" required>
            <input name="name" type="text" value="<?= htmlspecialchars($data['name']) ?>" placeholder="Name" required>
            <select id="basin" name="basin" required>
                <option value="">-- Select Basin --</option>
                <option value="NATL" <?= $data['basin'] == 'NATL' ? 'selected' : '' ?>>North Atlantic</option>
                <option value="EPAC" <?= $data['basin'] == 'EPAC' ? 'selected' : '' ?>>East Pacific</option>
                <option value="CPAC" <?= $data['basin'] == 'CPAC' ? 'selected' : '' ?>>Central Pacific</option>
                <option value="WPAC" <?= $data['basin'] == 'WPAC' ? 'selected' : '' ?>>West Pacific</option>
                <option value="SEPC" <?= $data['basin'] == 'SEPC' ? 'selected' : '' ?>>Southeast Pacific</option>
                <option value="NIO" <?= $data['basin'] == 'NIO'  ? 'selected' : '' ?>>North Indian Ocean</option>
                <option value="SIO" <?= $data['basin'] == 'SIO'  ? 'selected' : '' ?>>South Indian Ocean</option>
                <option value="SPAC" <?= $data['basin'] == 'SPAC' ? 'selected' : '' ?>>South Pacific</option>
                <option value="SATL" <?= $data['basin'] == 'SATL' ? 'selected' : '' ?>>South Atlantic</option>
                <option value="MEDI" <?= $data['basin'] == 'MEDI' ? 'selected' : '' ?>>Mediterranean/Black Sea</option>
            </select>
            <input name="wind_speed" type="number" value="<?= htmlspecialchars($data['wind_speed']) ?>" placeholder="Wind Speed (kt)" required>
            <input name="pressure" type="number" value="<?= htmlspecialchars($data['pressure']) ?>" placeholder="Pressure (mb)" required>
            <input name="start_date" type="date" value="<?= htmlspecialchars($data['start_date']) ?>" required>
            <input name="end_date" type="date" value="<?= htmlspecialchars($data['end_date']) ?>" required>
            <input name="fatalities" type="number" value="<?= htmlspecialchars($data['fatalities']) ?>" placeholder="Fatalities" required>
            <input name="damages" type="text" value="<?= htmlspecialchars($data['damages']) ?>" placeholder="Damages (USD or qualitative)" required>
            <input name="ace" type="number" step="0.01" value="<?= htmlspecialchars($data['ace']) ?>" placeholder="ACE" required>

            <?php if (!empty($data['image_path'])): ?>
                <div class="current-image">
                    <strong>Current Image:</strong><br>
                    <img src="<?= htmlspecialchars($data['image_path']) ?>" alt="Cyclone Image" style="max-width:100%; max-height:200px; margin:10px 0;">
                </div>
            <?php endif; ?>

            <label>Replace Image (optional):</label>
            <input type="file" name="image">

            <button type="submit" class="primary-btn">✅ Update Cyclone</button>
            <a class="secondary-btn" href="tc_admin.php">⬅ Back to Cyclone Admin</a>
        </form>
    </div>

</body>

</html>
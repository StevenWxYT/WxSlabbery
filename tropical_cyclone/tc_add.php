<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$functions = new DbFunc($conn);

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $functions->insertDatabase(
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
            $_FILES['best_track_image'],
            $_FILES['satellite_image']
        );
        $message = "✅ Cyclone added successfully.";
        $success = true;
    } catch (Exception $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
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

        <?php if (!empty($message)): ?>
            <div class="<?= $success ? 'success-message' : 'error-message' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input name="storm_id" placeholder="Storm ID" value="<?= $_POST['storm_id'] ?? '' ?>" required>
            <input name="name" placeholder="Name" value="<?= $_POST['name'] ?? '' ?>" required>

            <label for="basin">Basin:</label>
            <select id="basin" name="basin" required>
                <option value="">-- Select Basin --</option>
                <option value="NATL" <?= ($_POST['basin'] ?? '') == 'NATL' ? 'selected' : '' ?>>North Atlantic</option>
                <option value="EPAC" <?= ($_POST['basin'] ?? '') == 'EPAC' ? 'selected' : '' ?>>East Pacific</option>
                <option value="CPAC" <?= ($_POST['basin'] ?? '') == 'CPAC' ? 'selected' : '' ?>>Central Pacific</option>
                <option value="WPAC" <?= ($_POST['basin'] ?? '') == 'WPAC' ? 'selected' : '' ?>>West Pacific</option>
                <option value="SEPC" <?= ($_POST['basin'] ?? '') == 'SEPC' ? 'selected' : '' ?>>Southeast Pacific</option>
                <option value="NIO"  <?= ($_POST['basin'] ?? '') == 'NIO'  ? 'selected' : '' ?>>North Indian Ocean</option>
                <option value="SIO"  <?= ($_POST['basin'] ?? '') == 'SIO'  ? 'selected' : '' ?>>South Indian Ocean</option>
                <option value="SPAC" <?= ($_POST['basin'] ?? '') == 'SPAC' ? 'selected' : '' ?>>South Pacific</option>
                <option value="SATL" <?= ($_POST['basin'] ?? '') == 'SATL' ? 'selected' : '' ?>>South Atlantic</option>
                <option value="MEDI" <?= ($_POST['basin'] ?? '') == 'MEDI' ? 'selected' : '' ?>>Mediterranean/Black Sea</option>
            </select>

            <input name="wind_speed" type="number" placeholder="Wind Speed (kt)" value="<?= $_POST['wind_speed'] ?? '' ?>" required>
            <input name="pressure" type="number" placeholder="Pressure (mb)" value="<?= $_POST['pressure'] ?? '' ?>" required>
            <input name="start_date" type="date" value="<?= $_POST['start_date'] ?? '' ?>" required>
            <input name="end_date" type="date" value="<?= $_POST['end_date'] ?? '' ?>" required>
            <input name="fatalities" type="number" placeholder="Fatalities" value="<?= $_POST['fatalities'] ?? '' ?>" required>
            <input name="damages" placeholder="Damages (USD or qualitative)" value="<?= $_POST['damages'] ?? '' ?>" required>
            <input name="ace" type="number" step="0.01" placeholder="ACE (Accumulated Cyclone Energy)" value="<?= $_POST['ace'] ?? '' ?>" required>

            <label for="best_track_image">🖼️ Best Track Image (optional):</label>
            <input type="file" name="best_track_image" id="best_track_image">

            <label for="satellite_image">🛰️ Satellite Imagery (optional):</label>
            <input type="file" name="satellite_image" id="satellite_image">

            <button type="submit" class="primary-btn">➕ Add Cyclone</button>
        </form>

        <div class="button-group">
            <a href="tc_admin.php" class="secondary-btn">⬅ Back to Cyclone Admin</a>
        </div>
    </div>

</body>
</html>

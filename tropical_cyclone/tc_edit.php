<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Initialize DB connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$functions = new DBFunc($conn);

// Check if ID is present
if (!isset($_GET['id'])) {
    echo "Missing ID.";
    exit();
}

// Get cyclone data
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
        $_POST['history'],
        $_GET['id'],
        $_FILES['image'],
        $_FILES['satellite_image']
    );

    // Refresh data after update
    $data = $functions->showDatabase($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Cyclone - <?= htmlspecialchars($data['name']) ?></title>
    <link rel="icon" type="image/png" href="./img/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="./img/favicon.svg" />
    <link rel="shortcut icon" href="./img/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="./img/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="StevenWx" />
    <link rel="manifest" href="./img/site.webmanifest" />
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

            <!-- Image fields -->
            <?php if (!empty($data['image'])): ?>
                <div class="current-image">
                    <strong>📊 Current Best Track Image:</strong><br>
                    <img src="<?= htmlspecialchars($data['image']) ?>" alt="Best Track Image" style="max-width: 100%; max-height: 240px;">
                </div>
            <?php endif; ?>
            <label for="image">🖼️ Replace Best Track Image (optional):</label>
            <input type="file" name="image" id="image">

            <?php if (!empty($data['satellite_image'])): ?>
                <div class="current-image">
                    <strong>🛰️ Current Satellite Image:</strong><br>
                    <img src="<?= htmlspecialchars($data['satellite_image']) ?>" alt="Satellite Image" style="max-width: 100%; max-height: 240px;">
                </div>
            <?php endif; ?>
            <label for="satellite_image">📷 Replace Satellite Image (optional):</label>
            <input type="file" name="satellite_image" id="satellite_image">

            <!-- Form fields -->
            <input name="storm_id" type="text" value="<?= htmlspecialchars($data['storm_id']) ?>" placeholder="Storm ID">
            <input name="name" type="text" value="<?= htmlspecialchars($data['name']) ?>" placeholder="Name">

            <select id="basin" name="basin">
                <option value="">-- Select Basin --</option>
                <?php
                $basins = [
                    'NATL' => 'North Atlantic',
                    'EPAC' => 'East Pacific',
                    'CPAC' => 'Central Pacific',
                    'WPAC' => 'West Pacific',
                    'NIO'  => 'North Indian Ocean',
                    'SIO'  => 'South Indian Ocean',
                    'AU'   => 'Australian Region',
                    'SPAC' => 'South Pacific',
                    'SEPAC' => 'Southeast Pacific',
                    'MED'  => 'Mediterranean/Black Sea'
                ];
                foreach ($basins as $code => $label):
                ?>
                    <option value="<?= $code ?>" <?= $data['basin'] === $code ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>

            <input name="wind_speed" type="number" value="<?= htmlspecialchars($data['wind_speed']) ?>" placeholder="Wind Speed (mph)">
            <input name="pressure" type="number" value="<?= htmlspecialchars($data['pressure']) ?>" placeholder="Pressure (mbar)">
            <input name="start_date" type="date" value="<?= htmlspecialchars($data['start_date']) ?>">
            <input name="end_date" type="date" value="<?= htmlspecialchars($data['end_date']) ?>">

            <!-- ✅ Updated fields -->
            <input name="fatalities" type="text" value="<?= htmlspecialchars($data['fatalities']) ?>" placeholder="Fatalities (e.g. 3(2))">
            <input name="damages" type="text" value="<?= htmlspecialchars($data['damages']) ?>" placeholder="Damages (USD or qualitative)">
            <input name="ace" type="text" pattern="^\d+(\.\d{1,4})?$" value="<?= htmlspecialchars($data['ace']) ?>" placeholder="ACE (e.g. 42.1234)">

            <!-- Cyclone History -->
            <label for="history">📝 Cyclone History:</label>
            <textarea name="history" id="history" rows="6" placeholder="Cyclone history..."><?= htmlspecialchars($data['history']) ?></textarea>

            <button type="submit" class="primary-btn">✅ Update Cyclone</button>
            <a class="secondary-btn" href="tc_admin.php">⬅ Back to Cyclone Admin</a>
        </form>
    </div>

</body>

</html>
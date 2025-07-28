<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Initialize database connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$functions = new DBFunc($conn);

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo "Missing ID.";
    exit();
}

// Fetch cyclone data
$data = $functions->showDatabase($_GET['id']);
if (!$data) {
    echo "Cyclone not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Cyclone - <?= htmlspecialchars($data['name']) ?></title>
    <link rel="stylesheet" href="../master.css">
</head>
<body>

<div class="view-container">
    <h1>🌪️ Cyclone: <?= htmlspecialchars($data['name']) ?></h1>

    <div class="info-block">
        <p><strong>🆔 Storm ID:</strong> <?= htmlspecialchars($data['storm_id']) ?></p>
        <p><strong>🗺️ Basin:</strong> <?= htmlspecialchars($data['basin']) ?></p>
        <p><strong>💨 Wind Speed:</strong> <?= htmlspecialchars($data['wind_speed']) ?> knots</p>
        <p><strong>🌡️ Pressure:</strong> <?= htmlspecialchars($data['pressure']) ?> mb</p>
        <p><strong>📅 Duration:</strong> <?= htmlspecialchars($data['start_date']) ?> to <?= htmlspecialchars($data['end_date']) ?></p>
        <p><strong>⚰️ Fatalities:</strong> <?= htmlspecialchars($data['fatalities']) ?></p>
        <p><strong>💰 Damages:</strong> <?= htmlspecialchars($data['damages']) ?></p>
        <p><strong>📈 ACE:</strong> <?= htmlspecialchars($data['ace']) ?></p>
    </div>

    <?php if (!empty($data['image_path'])): ?>
        <div class="image-block">
            <img src="<?= htmlspecialchars($data['image_path']) ?>" alt="Cyclone Image" class="cyclone-image">
        </div>
    <?php endif; ?>

    <div class="button-group">
        <a class="secondary-btn" href="tc_admin.php">⬅ Back to Cyclone List</a>
        <a class="primary-btn" href="tc_edit.php?id=<?= urlencode($_GET['id']) ?>">✏️ Edit Cyclone</a>
    </div>
</div>

</body>
</html>

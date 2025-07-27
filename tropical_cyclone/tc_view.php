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
    <h1>🌪️ <?= htmlspecialchars($data['name']) ?></h1>
    <p><strong>Basin:</strong> <?= htmlspecialchars($data['basin']) ?></p>
    <p><strong>Wind Speed:</strong> <?= htmlspecialchars($data['wind_speed']) ?> kts</p>
    <p><strong>Pressure:</strong> <?= htmlspecialchars($data['pressure']) ?> mb</p>
    <p><strong>Duration:</strong> <?= htmlspecialchars($data['start_date']) ?> to <?= htmlspecialchars($data['end_date']) ?></p>
    <p><strong>Fatalities:</strong> <?= htmlspecialchars($data['fatalities']) ?></p>
    <p><strong>Damages:</strong> <?= htmlspecialchars($data['damages']) ?></p>
    <p><strong>ACE:</strong> <?= htmlspecialchars($data['ace']) ?></p>

    <?php if (!empty($data['image'])): ?>
        <img src="<?= htmlspecialchars($data['image']) ?>" alt="Cyclone Image">
    <?php endif; ?>

    <a class="back-link" href="tc_admin.php">⬅ Back to Cyclone List</a>
</div>

</body>
</html>

<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$functions = new DBFunc($conn);

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$confirm = $_GET['confirm'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Cyclone</title>
    <link rel="stylesheet" href="../master.css">
</head>
<body>
<div class="container">
<?php
if (!$id) {
    echo "<div class='error-box'><h2>Error: Cyclone ID not provided.</h2></div>";
    echo "<a href='tc_admin.php' class='btn'>⬅ Back</a>";
    exit();
}

if ($confirm !== 'yes') {
    echo "<div class='warning-box'>";
    echo "<h2>Are you sure you want to delete this cyclone?</h2>";
    echo "<p>This action is <strong>irreversible</strong> and will permanently delete the record and any uploaded images.</p>";
    echo "<a class='btn danger' href='tc_delete.php?id=" . urlencode($id) . "&confirm=yes'>✅ Yes, delete it</a> ";
    echo "<a class='btn' href='tc_admin.php'>❌ Cancel</a>";
    echo "</div>";
    exit();
}

// Get image paths from database
$imagePaths = $functions->getImagePathsById($id);

// Delete the database record
$deleted = $functions->deleteDatabase($id);

if ($deleted) {
    // Delete associated image files if present (paths stored are relative like 'uploads/...')
    if (!empty($imagePaths['image'])) {
        $img = $imagePaths['image'];
        if (file_exists($img)) unlink($img);
    }

    if (!empty($imagePaths['satellite_image'])) {
        $sat = $imagePaths['satellite_image'];
        if (file_exists($sat)) unlink($sat);
    }

    header("Location: tc_admin.php?status=deleted");
    exit();
} else {
    echo "<div class='error-box'><h2>Error: Failed to delete cyclone record.</h2></div>";
    echo "<a class='btn' href='tc_admin.php'>⬅ Back</a>";
    exit();
}
?>
</div>
</body>
</html>

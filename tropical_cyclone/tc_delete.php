<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$functions = new DBFunc($conn);

// Get ID and confirm flag
$id = $_GET['id'] ?? null;
$confirm = $_GET['confirm'] ?? null;

// Basic validation
if (!$id) {
    echo "<h2>Error: Cyclone ID not provided.</h2>";
    exit();
}

// Optional: confirm before deletion
if ($confirm !== 'yes') {
    echo "<h2>Are you sure you want to delete this cyclone?</h2>";
    echo "<a href=\"tc_delete.php?id=" . urlencode($id) . "&confirm=yes\" style='color:red; font-weight:bold;'>Yes, delete it</a> | ";
    echo "<a href=\"tc_admin.php\">Cancel</a>";
    exit();
}

// Proceed with deletion
$deleted = $functions->deleteDatabase($id);

if ($deleted) {
    header("Location: tc_admin.php?status=deleted");
    exit();
} else {
    echo "<h2>Error: Failed to delete cyclone.</h2>";
    echo "<a href='tc_admin.php'>⬅ Back</a>";
    exit();
}
?>

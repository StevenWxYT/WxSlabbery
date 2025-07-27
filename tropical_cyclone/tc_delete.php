<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Initialize database connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$functions = new DBFunc($conn);

// Check for valid cyclone ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: Cyclone ID not provided.";
    exit();
}

// Delete cyclone by ID
$deleted = $functions->deleteDatabase($_GET['id']);

// Redirect or show error if deletion fails
if ($deleted) {
    header("Location: tc_admin.php?status=deleted");
    exit();
} else {
    echo "Error: Failed to delete cyclone.";
    exit();
}
?>
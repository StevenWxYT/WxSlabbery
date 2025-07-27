<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Initialize database connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

// Validate the ID parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: Storm ID not provided.";
    exit();
}

// Perform the delete operation
$deleted = $db->deleteIBTracsStorm($_GET['id']);

// Redirect or show error if deletion fails
if ($deleted) {
    header("Location: ibtracs_admin.php?status=deleted");
    exit();
} else {
    echo "Error: Failed to delete IBTrACS storm.";
    exit();
}
?>
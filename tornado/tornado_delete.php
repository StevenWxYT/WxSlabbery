<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Initialize DB connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

// Get and validate tornado ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Invalid or missing ID.";
    exit();
}

// Perform delete operation
$deleted = $db->deleteTornado($id);

if ($deleted) {
    header("Location: tornado_admin.php?msg=deleted");
    exit();
} else {
    echo "❌ Failed to delete tornado record.";
}
?>

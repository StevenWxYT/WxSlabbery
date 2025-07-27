<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

// Validate ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "Invalid or missing ID.";
    exit();
}

// Attempt deletion
if ($db->deleteTornado($id)) {
    header("Location: tornado_admin.php?msg=deleted");
    exit();
} else {
    echo "Failed to delete tornado record.";
}
?>

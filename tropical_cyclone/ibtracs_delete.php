<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Initialize DB connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

// Validate storm ID
if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    header("Location: ibtracs_admin.php?error=missing_id");
    exit();
}

$id = htmlspecialchars(trim($_GET['id']));

// Perform deletion
$deleted = $db->deleteIBTracsStorm($id);

// Redirect on result
if ($deleted) {
    header("Location: ibtracs_admin.php?status=deleted&sid=" . urlencode($id));
    exit();
} else {
    header("Location: ibtracs_admin.php?error=delete_failed&sid=" . urlencode($id));
    exit();
}
?>

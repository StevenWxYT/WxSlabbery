<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }

$db->deleteIBTracsStorm($_GET['id']);
header("Location: ibtracs_admin.php");
exit();

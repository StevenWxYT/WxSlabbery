<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }

$db->deleteTornado($_GET['id']);
header("Location: tornado_admin.php");
exit();

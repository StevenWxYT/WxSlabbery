<?php
require_once '../../php/function.php';

$db = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }

$db->deletePaleoStorm($_GET['id']);
header("Location: paleo_admin.php");
exit();

<?php
require_once 'php/function.php'; // Adjust path as needed

$db = new DBConn();
$user = new DBFunc($db->conn);

$user->logoutUser();

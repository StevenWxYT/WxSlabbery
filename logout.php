<?php
require_once 'php/db.php';         // Make sure db.php is included first
require_once 'php/function.php';   // Then include the functions

$db = new DBConn();
$conn = $db->getConnection();      // Use getConnection() to access the private $conn
$user = new DBFunc($conn);         // Pass it to DBFunc constructor

$user->logoutUser();               // Perform logout

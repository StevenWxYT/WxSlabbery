<?php
require_once '../php/function.php';

$functions = new DBFunc($conn);
if (!isset($_GET['id'])) {
  echo "ID not provided.";
  exit();
}
$functions->deleteDatabase($_GET['id']);
header("Location: tc_admin.php");
exit();

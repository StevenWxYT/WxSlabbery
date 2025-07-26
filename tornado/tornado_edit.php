<?php
require_once '../php/function.php';

$db = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }

$data = $db->showTornado($_GET['id']);
if (!$data) { echo "Record not found."; exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $db->updateTornado($_GET['id'], $_POST);
  header("Location: tornado_admin.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Tornado</title>
  <link rel="stylesheet" href="../master.css">
</head>
<body>
  <h1>Edit Tornado</h1>
  <form method="POST">
    <input type="date" name="date" value="<?= $data['date'] ?>"><br>
    <input type="text" name="location" value="<?= htmlspecialchars($data['location']) ?>"><br>
    <input type="text" name="f_scale" value="<?= $data['f_scale'] ?>"><br>
    <input type="number" name="fatalities" value="<?= $data['fatalities'] ?>"><br>
    <textarea name="description"><?= $data['description'] ?></textarea><br>
    <button class="btn" type="submit">Update</button>
  </form>
  <a class="btn" href="tornado_admin.php">⬅ Back</a>
</body>
</html>

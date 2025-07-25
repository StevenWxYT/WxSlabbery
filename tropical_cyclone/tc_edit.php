<?php
require_once '../php/function.php';

$functions = new DBFunc($conn);
if (!isset($_GET['id'])) { echo "Missing ID"; exit(); }
$data = $functions->showDatabase($_GET['id']);
if (!$data) { echo "Not found"; exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $functions->updateDatabase(
    $_POST['storm_id'], $_POST['name'], $_POST['basin'],
    $_POST['wind_speed'], $_POST['pressure'],
    $_POST['start_date'], $_POST['end_date'],
    $_POST['fatalities'], $_POST['damages'], $_POST['ace'],
    $_GET['id'], $_FILES['image']
  );
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Cyclone</title></head>
<body>
  <h1>Edit Cyclone: <?= htmlspecialchars($data['name']) ?></h1>
  <form method="POST" enctype="multipart/form-data">
    <input name="storm_id" value="<?= $data['storm_id'] ?>" required><br>
    <input name="name" value="<?= $data['name'] ?>" required><br>
    <input name="basin" value="<?= $data['basin'] ?>" required><br>
    <input name="wind_speed" type="number" value="<?= $data['wind_speed'] ?>" required><br>
    <input name="pressure" type="number" value="<?= $data['pressure'] ?>" required><br>
    <input name="start_date" type="date" value="<?= $data['start_date'] ?>" required><br>
    <input name="end_date" type="date" value="<?= $data['end_date'] ?>" required><br>
    <input name="fatalities" type="number" value="<?= $data['fatalities'] ?>" required><br>
    <input name="damages" value="<?= $data['damages'] ?>" required><br>
    <input name="ace" type="number" step="0.01" value="<?= $data['ace'] ?>" required><br>
    <input type="file" name="image"><br><br>
    <button type="submit">Update Cyclone</button>
  </form>
  <a href="tc_admin.php">Back</a>
</body>
</html>

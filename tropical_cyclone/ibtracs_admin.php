<?php
require_once '../php/db.php';
require_once '../php/function.php';

define('FPDF_FONTPATH', __DIR__ . '/../php/font/');
require_once '../php/fpdf.php';

if (!isset($conn)) {
    $dbConn = new DBConn();
    $conn = $dbConn->getConnection();
}

$db = new DBFunc($conn);

// PDF Export
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    $result = $conn->query("SELECT * FROM IBTrACS_Storms ORDER BY season DESC LIMIT 100");

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,10,'IBTrACS Storms Export',0,1,'C');
    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(230,230,230);
    $pdf->Cell(30,8,"SID",1,0,'C',true);
    $pdf->Cell(30,8,"Name",1,0,'C',true);
    $pdf->Cell(20,8,"Basin",1,0,'C',true);
    $pdf->Cell(20,8,"Lat",1,0,'C',true);
    $pdf->Cell(20,8,"Lon",1,0,'C',true);
    $pdf->Cell(25,8,"Wind",1,0,'C',true);
    $pdf->Cell(25,8,"Pressure",1,0,'C',true);
    $pdf->Cell(40,8,"Timestamp",1,1,'C',true);

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30,8,$row['sid'],1);
        $pdf->Cell(30,8,$row['name'],1);
        $pdf->Cell(20,8,$row['basin'],1);
        $pdf->Cell(20,8,$row['lat'],1);
        $pdf->Cell(20,8,$row['lon'],1);
        $pdf->Cell(25,8,$row['wind_kts'],1);
        $pdf->Cell(25,8,$row['pressure_mb'],1);
        $pdf->Cell(40,8,$row['timestamp'],1,1);
    }

    $pdf->Output("D", "ibtracs_export.pdf");
    exit;
}

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $result = $conn->query("SELECT * FROM IBTrACS_Storms ORDER BY season DESC LIMIT 100");

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=ibtracs_export.csv");
    $out = fopen("php://output", "w");
    fputcsv($out, ["SID", "Name", "Basin", "Lat", "Lon", "Wind (kts)", "Pressure (mb)", "Timestamp"]);

    while ($row = $result->fetch_assoc()) {
        fputcsv($out, [$row['sid'], $row['name'], $row['basin'], $row['lat'], $row['lon'], $row['wind_kts'], $row['pressure_mb'], $row['timestamp']]);
    }

    fclose($out);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>IBTrACS Admin</title>
  <link rel="stylesheet" href="../master.css">
  <script src="../js/ibtracs.js" defer></script>
</head>
<body>
  <div class="container">
    <h1 class="page-title">🌪️ IBTrACS Storm Admin</h1>

    <form id="filterForm" class="form-grid">
      <input type="text" name="name" placeholder="Name" class="form-input">
      <input type="text" name="year" placeholder="Year" class="form-input">
      <input type="number" name="wind_min" placeholder="Min Wind" class="form-input">
      <input type="number" name="wind_max" placeholder="Max Wind" class="form-input">
      <input type="number" name="pressure_max" placeholder="Max Pressure" class="form-input">
      <input type="text" name="sid" placeholder="SID" class="form-input">
      <input type="text" name="basin" placeholder="Basin" class="form-input">
      <button type="submit" class="btn btn-primary">🔍 Search</button>
      <a href="?export=csv" class="btn btn-secondary">⬇ Export CSV</a>
      <a href="?export=pdf" class="btn btn-secondary">⬇ Export PDF</a>
    </form>

    <div id="stormTableContainer">
      <table id="stormTable" class="table">
        <thead>
          <tr>
            <th>Name</th>
            <th>SID</th>
            <th>Basin</th>
            <th>Lat</th>
            <th>Lon</th>
            <th>Wind (kts)</th>
            <th>Pressure</th>
            <th>Timestamp</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <div id="pagination" class="pagination"></div>
    </div>

    <br>
    <a class="btn btn-secondary" href="tc_index.php">⬅ Back to TC Index</a>
  </div>
</body>
</html>

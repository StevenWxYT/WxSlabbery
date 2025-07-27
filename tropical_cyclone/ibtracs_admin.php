<?php
require_once '../php/db.php'; // <-- Include this BEFORE using $conn
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
<html>
<head>
  <title>IBTrACS Admin</title>
  <link rel="stylesheet" href="../css/master.css">
  <script src="../js/ibtracs.js" defer></script>
</head>
<body>
  <h1>IBTrACS Storm Admin</h1>

  <form id="filterForm">
    <input type="text" name="name" placeholder="Name">
    <input type="text" name="year" placeholder="Year">
    <input type="number" name="wind_min" placeholder="Min Wind">
    <input type="number" name="wind_max" placeholder="Max Wind">
    <input type="number" name="pressure_max" placeholder="Max Pressure">
    <input type="text" name="sid" placeholder="SID">
    <input type="text" name="basin" placeholder="Basin">
    <button type="submit">Search</button>
    <a href="?export=csv">Export CSV</a> |
    <a href="?export=pdf">Export PDF</a>
  </form>

  <div id="stormTableContainer">
    <table id="stormTable">
      <thead>
        <tr>
          <th>Name</th><th>SID</th><th>Basin</th>
          <th>Lat</th><th>Lon</th><th>Wind (kts)</th>
          <th>Pressure</th><th>Timestamp</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <div id="pagination"></div>
  </div>
</body>
</html>

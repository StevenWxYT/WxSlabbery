<?php
require_once '../php/function.php';
require_once '../php/fpdf.php'; // Ensure this is the correct path

$db = new DBFunc($conn);

if (isset($_GET['export'])) {
    $where = [];
    if (!empty($_GET['name'])) $where[] = "name LIKE '%" . $conn->real_escape_string($_GET['name']) . "%'";
    if (!empty($_GET['year'])) $where[] = "season = " . intval($_GET['year']);
    if (!empty($_GET['wind_min'])) $where[] = "wind_kts >= " . intval($_GET['wind_min']);
    if (!empty($_GET['wind_max'])) $where[] = "wind_kts <= " . intval($_GET['wind_max']);
    if (!empty($_GET['pressure_max'])) $where[] = "pressure_mb <= " . intval($_GET['pressure_max']);
    if (!empty($_GET['sid'])) $where[] = "sid = '" . $conn->real_escape_string($_GET['sid']) . "'";
    if (!empty($_GET['basin'])) $where[] = "basin LIKE '%" . $conn->real_escape_string($_GET['basin']) . "%'";

    $sql = "SELECT * FROM IBTrACS_Storms";
    if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
    $result = $conn->query($sql);

    if ($_GET['export'] === 'csv') {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=ibtracs_export.csv");

        $out = fopen("php://output", "w");
        fputcsv($out, ["SID", "Name", "Basin", "Lat", "Lon", "Wind (kts)", "Pressure (mb)", "Timestamp"]);

        while ($row = $result->fetch_assoc()) {
            fputcsv($out, [$row['sid'], $row['name'], $row['basin'], $row['lat'], $row['lon'], $row['wind_kts'], $row['pressure_mb'], $row['timestamp']]);
        }
        fclose($out);
        exit();
    }

    if ($_GET['export'] === 'pdf') {
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
        $pdf->Cell(25,8,"Wind (kts)",1,0,'C',true);
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
        exit();
    }
}

// Fetch from cURL once on load (optional)
$url = "https://www.ncei.noaa.gov/data/international-best-track-archive-for-climate-stewardship-ibtracs/v04r00/access/csv/ibtracs.ALL.list.v04r00.csv";
$tmpFile = tempnam(sys_get_temp_dir(), 'ibtracs');

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);
curl_close($ch);
file_put_contents($tmpFile, $data);

// Search and filter logic
$where = [];
if (!empty($_GET['name'])) $where[] = "name LIKE '%" . $conn->real_escape_string($_GET['name']) . "%'";
if (!empty($_GET['year'])) $where[] = "season = " . intval($_GET['year']);
if (!empty($_GET['wind_min'])) $where[] = "wind_kts >= " . intval($_GET['wind_min']);
if (!empty($_GET['wind_max'])) $where[] = "wind_kts <= " . intval($_GET['wind_max']);
if (!empty($_GET['pressure_max'])) $where[] = "pressure_mb <= " . intval($_GET['pressure_max']);
if (!empty($_GET['sid'])) $where[] = "sid = '" . $conn->real_escape_string($_GET['sid']) . "'";
if (!empty($_GET['basin'])) $where[] = "basin LIKE '%" . $conn->real_escape_string($_GET['basin']) . "%'";

$sql = "SELECT * FROM IBTrACS_Storms";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY season DESC LIMIT 100";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>IBTrACS Admin</title>
  <link rel="stylesheet" href="../css/master.css">
</head>
<body>
  <h1>IBTrACS Storm Admin</h1>
  <form method="GET">
    <input type="text" name="name" placeholder="Name">
    <input type="text" name="year" placeholder="Year">
    <input type="number" name="wind_min" placeholder="Min Wind">
    <input type="number" name="wind_max" placeholder="Max Wind">
    <input type="number" name="pressure_max" placeholder="Max Pressure">
    <input type="text" name="sid" placeholder="SID">
    <input type="text" name="basin" placeholder="Basin">
    <button type="submit">Search</button>
    <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>">Export CSV</a> |
    <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>">Export PDF</a>
  </form>

  <table>
    <thead>
      <tr>
        <th>Name</th><th>SID</th><th>Basin</th>
        <th>Latitude</th><th>Longitude</th>
        <th>Wind (kts)</th><th>Pressure (mb)</th>
        <th>Timestamp</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= $row['sid'] ?></td>
        <td><?= $row['basin'] ?></td>
        <td><?= $row['lat'] ?></td>
        <td><?= $row['lon'] ?></td>
        <td><?= $row['wind_kts'] ?></td>
        <td><?= $row['pressure_mb'] ?></td>
        <td><?= $row['timestamp'] ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
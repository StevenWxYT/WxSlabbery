<?php
require_once '../php/db.php';
require_once '../php/function.php';

// Detect export format (csv or pdf)
$format = isset($_GET['format']) ? $_GET['format'] : 'csv';

// Only load TCPDF if PDF is requested
if ($format === 'pdf') {
    require_once '../php/tcpdf/tcpdf.php'; // Make sure this path is correct
}

// Create DB connection
$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

$filename = "cyclone_data_" . date("Y-m-d");

// Fetch cyclone data
$query = "SELECT * FROM tcdatabase ORDER BY start_date DESC";
$result = $conn->query($query);

// Handle empty result
if (!$result || $result->num_rows === 0) {
    die("No data to export.");
}

if ($format === 'csv') {
    // ================= CSV Export =================
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=$filename.csv");
    header("Pragma: public");
    header("Expires: 0");

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Storm ID', 'Name', 'Basin', 'Wind Speed (kt)', 'Pressure (mb)', 'Start Date', 'End Date']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['storm_id'],
            $row['name'],
            $row['basin'],
            $row['wind_speed'],
            $row['pressure'],
            $row['start_date'],
            $row['end_date']
        ]);
    }

    fclose($output);
    exit();

} elseif ($format === 'pdf') {
    // ================= PDF Export using TCPDF =================
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('WxSlabbery');
    $pdf->SetAuthor('WxSlabbery');
    $pdf->SetTitle('Tropical Cyclone Report');
    $pdf->SetHeaderData('', 0, '🌪️ Tropical Cyclone Report', 'Generated on ' . date('Y-m-d'));

    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 8]);
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(10, 25, 10);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->AddPage();

    // Build HTML table
    $html = '<h2>Tropical Cyclone Records</h2>';
    $html .= '<table border="1" cellpadding="4">
                <thead>
                  <tr style="font-weight: bold; background-color: #f0f0f0;">
                    <th>ID</th><th>Storm ID</th><th>Name</th><th>Basin</th>
                    <th>Wind (kt)</th><th>Pressure (mb)</th><th>Start Date</th><th>End Date</th>
                  </tr>
                </thead><tbody>';

    // Append table rows
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['storm_id'] . '</td>
                    <td>' . htmlspecialchars($row['name']) . '</td>
                    <td>' . $row['basin'] . '</td>
                    <td>' . $row['wind_speed'] . '</td>
                    <td>' . $row['pressure'] . '</td>
                    <td>' . $row['start_date'] . '</td>
                    <td>' . $row['end_date'] . '</td>
                  </tr>';
    }

    $html .= '</tbody></table>';
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("$filename.pdf", 'I');
    exit();

} else {
    echo "❌ Invalid export format.";
}

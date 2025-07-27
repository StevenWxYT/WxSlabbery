<?php
require_once '../php/db.php';
require_once '../php/function.php';

$dbConn = new DBConn();
$conn = $dbConn->getConnection();
$db = new DBFunc($conn);

$format = $_GET['format'] ?? 'csv';

// Get tornado records from the database
$tornadoes = $db->getAllTornadoes(); // Assumes this returns a mysqli_result

if (!$tornadoes || $tornadoes->num_rows === 0) {
    die("No tornado records found.");
}

if ($format === 'csv') {
    // CSV export
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="tornado_records.csv"');
    header("Pragma: public");
    header("Expires: 0");

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date', 'Location', 'Fujita Scale', 'Wind Speed (kts)']);

    while ($row = $tornadoes->fetch_assoc()) {
        fputcsv($output, [
            $row['date'],
            $row['tor_location'],
            $row['fujita_rank'],
            $row['wind_speed']
        ]);
    }

    fclose($output);
    exit;
}

if ($format === 'pdf') {
    // PDF export
    require_once '../php/fpdf/fpdf.php'; // Ensure fpdf.php is inside php/fpdf/ directory

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, '🌪️ Tornado Records', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 10, 'Date', 1);
    $pdf->Cell(60, 10, 'Location', 1);
    $pdf->Cell(30, 10, 'Fujita Scale', 1);
    $pdf->Cell(50, 10, 'Wind Speed (kts)', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $tornadoes->data_seek(0); // Reset pointer since it was used in CSV above
    while ($row = $tornadoes->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['date'], 1);
        $pdf->Cell(60, 10, $row['tor_location'], 1);
        $pdf->Cell(30, 10, $row['fujita_rank'], 1);
        $pdf->Cell(50, 10, $row['wind_speed'], 1);
        $pdf->Ln();
    }

    $pdf->Output('I', 'tornado_records.pdf');
    exit;
}

echo "Invalid export format.";

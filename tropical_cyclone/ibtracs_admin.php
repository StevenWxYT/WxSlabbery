<?php
// ibtracs_admin.php (Live View Only)
$csvUrl = "https://www.ncei.noaa.gov/data/international-best-track-archive-for-climate-stewardship-ibtracs/v04r00/access/csv/ibtracs.ALL.list.v04r00.csv";
$filters = [
    'name' => $_GET['name'] ?? '',
    'year' => $_GET['year'] ?? '',
    'sid'  => $_GET['sid'] ?? '',
    'wind_min' => $_GET['wind_min'] ?? '',
    'wind_max' => $_GET['wind_max'] ?? '',
    'pressure_max' => $_GET['pressure_max'] ?? '',
    'basin' => $_GET['basin'] ?? '',
];

// Helper function to filter
function passesFilter($row, $filters) {
    return
        (empty($filters['name']) || stripos($row['name'], $filters['name']) !== false) &&
        (empty($filters['year']) || strpos($row['season'], $filters['year']) === 0) &&
        (empty($filters['sid']) || $row['sid'] === $filters['sid']) &&
        (empty($filters['wind_min']) || floatval($row['wind_wmo']) >= floatval($filters['wind_min'])) &&
        (empty($filters['wind_max']) || floatval($row['wind_wmo']) <= floatval($filters['wind_max'])) &&
        (empty($filters['pressure_max']) || floatval($row['pres_wmo']) <= floatval($filters['pressure_max'])) &&
        (empty($filters['basin']) || stripos($row['basin'], $filters['basin']) !== false);
}

$data = [];
$error = false;

if (($handle = @fopen($csvUrl, "r")) !== false) {
    $headers = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $assoc = array_combine($headers, $row);
        if (!$assoc || !isset($assoc['sid']) || !isset($assoc['name'])) continue;
        if (passesFilter($assoc, $filters)) $data[] = $assoc;
    }
    fclose($handle);
} else {
    $error = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>IBTrACS Admin (Live)</title>
    <link rel="stylesheet" href="css/master.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            padding: 6px 8px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #2c3e50;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .filter-box {
            padding: 10px;
            background: #ecf0f1;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .filter-box input {
            padding: 5px;
            margin: 4px;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #27ae60;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 12px;
        }
        .btn:hover {
            background-color: #219150;
        }
    </style>
</head>
<body>

<h2>🌐 Live IBTrACS Cyclone Dataset (Direct from NOAA)</h2>

<a href="ibtracs_import.php" class="btn">🔄 Import Latest IBTrACS Live Data</a>

<div class="filter-box">
    <form method="GET">
        <input type="text" name="name" placeholder="Storm Name" value="<?= htmlspecialchars($filters['name']) ?>">
        <input type="text" name="year" placeholder="Year" value="<?= htmlspecialchars($filters['year']) ?>">
        <input type="text" name="sid" placeholder="SID" value="<?= htmlspecialchars($filters['sid']) ?>">
        <input type="text" name="wind_min" placeholder="Min Wind (kt)" value="<?= htmlspecialchars($filters['wind_min']) ?>">
        <input type="text" name="wind_max" placeholder="Max Wind (kt)" value="<?= htmlspecialchars($filters['wind_max']) ?>">
        <input type="text" name="pressure_max" placeholder="Max Pressure (mb)" value="<?= htmlspecialchars($filters['pressure_max']) ?>">
        <input type="text" name="basin" placeholder="Basin" value="<?= htmlspecialchars($filters['basin']) ?>">
        <button type="submit" class="btn">Search</button>
        <a href="ibtracs_admin.php" class="btn" style="background-color:#c0392b;">Reset</a>
    </form>
</div>

<?php if ($error): ?>
    <div style="color: red; background: #ffe6e6; padding: 10px; border: 1px solid #ff0000;">
        ⚠️ Unable to fetch live data. NOAA server may be unavailable (503). Please try again later or <a href="ibtracs_import.php">import locally</a>.
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>SID</th>
            <th>Name</th>
            <th>Season</th>
            <th>Basin</th>
            <th>Wind (kt)</th>
            <th>Pressure (mb)</th>
            <th>Type</th>
            <th>Link</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$error && count($data) > 0): ?>
            <?php foreach ($data as $storm): ?>
                <tr>
                    <td><?= htmlspecialchars($storm['sid']) ?></td>
                    <td><?= htmlspecialchars($storm['name']) ?></td>
                    <td><?= htmlspecialchars($storm['season']) ?></td>
                    <td><?= htmlspecialchars($storm['basin']) ?></td>
                    <td><?= htmlspecialchars($storm['wind_wmo']) ?></td>
                    <td><?= htmlspecialchars($storm['pres_wmo']) ?></td>
                    <td><?= htmlspecialchars($storm['nature']) ?></td>
                    <td><a href="ibtracs_view.php?sid=<?= urlencode($storm['sid']) ?>">View</a></td>
                </tr>
            <?php endforeach; ?>
        <?php elseif (!$error): ?>
            <tr><td colspan="8">No storms match your search criteria.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>

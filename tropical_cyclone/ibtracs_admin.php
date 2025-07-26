<?php
$url = "https://www.ncei.noaa.gov/data/international-best-track-archive-for-climate-stewardship-ibtracs/v04r00/access/csv/ibtracs.last3years.list.v04r00.csv";

// Open the remote CSV file
if (($handle = fopen($url, "r")) !== FALSE) {
    $header = fgetcsv($handle); // Get header row
    $columns = array_flip($header); // Make column index map for quick access

    echo "<h1>Real-Time IBTrACS (Last 3 Years)</h1>";
    echo "<table border='1' cellspacing='0' cellpadding='4'>";
    echo "<tr>
            <th>Name</th><th>SID</th><th>Basin</th>
            <th>Latitude</th><th>Longitude</th>
            <th>Wind (kts)</th><th>Pressure (mb)</th>
            <th>Date/Time</th><th>Agency</th>
          </tr>";

    $shown = [];

    while (($row = fgetcsv($handle)) !== FALSE) {
        $sid = $row[$columns['SID']];
        $name = $row[$columns['NAME']];
        $basin = $row[$columns['BASIN']];
        $lat = $row[$columns['LAT']];
        $lon = $row[$columns['LON']];
        $wind = $row[$columns['USA_WIND']];
        $pres = $row[$columns['USA_PRES']];
        $datetime = $row[$columns['ISO_TIME']];
        $agency = $row[$columns['TRACK_TYPE']];

        // Only show latest record per SID
        if (!isset($shown[$sid])) {
            echo "<tr>
                    <td>" . htmlspecialchars($name) . "</td>
                    <td>$sid</td>
                    <td>$basin</td>
                    <td>$lat</td>
                    <td>$lon</td>
                    <td>$wind</td>
                    <td>$pres</td>
                    <td>$datetime</td>
                    <td>$agency</td>
                  </tr>";
            $shown[$sid] = true;
        }

        if (count($shown) >= 50) break; // Limit to 50 storms max
    }

    echo "</table>";
    fclose($handle);
} else {
    echo "<p style='color:red;'>Failed to load IBTrACS data from NOAA.</p>";
}
?>

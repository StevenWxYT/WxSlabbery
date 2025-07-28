<?php
function getCategory($wind) {
    if ($wind >= 137) return "Category 5";
    if ($wind >= 113) return "Category 4";
    if ($wind >= 96)  return "Category 3";
    if ($wind >= 83)  return "Category 2";
    if ($wind >= 64)  return "Category 1";
    if ($wind >= 34)  return "Tropical Storm";
    return "Tropical Depression";
}

function hollandPressure($wind) {
    return round(1010 - (0.885 * $wind), 2);
}

function ckzPressure($wind, $lat) {
    $a = 8.5;
    $b = 0.4 + (0.01 * abs($lat));
    return round(1013.25 - $a * pow($wind, $b), 2);
}

function rmwEstimate($wind, $lat) {
    return round(466 * exp(-0.015 * $wind) * pow(cos(deg2rad($lat)), -1), 2); // km
}

function stormSurge($wind, $pressure) {
    return round(0.01 * $wind + (1010 - $pressure) * 0.05, 2);
}

function pressureDeficit($pressure) {
    return round(1013.25 - $pressure, 2);
}

function cycloneHeatPotential($sst) {
    return $sst >= 26 ? round(($sst - 26) * 10, 2) : 0;
}

function ace($wind, $hours = 6) {
    $points = floor($hours / 6);
    $sum = 0;
    for ($i = 0; $i < $points; $i++) {
        $sum += pow($wind, 2);
    }
    return round($sum * 1e-4, 2);
}

$wind = isset($_POST['wind']) ? floatval($_POST['wind']) : 0;
$pressure = isset($_POST['pressure']) ? floatval($_POST['pressure']) : 0;
$sst = isset($_POST['sst']) ? floatval($_POST['sst']) : 28;
$lat = isset($_POST['lat']) ? floatval($_POST['lat']) : 15;
$duration = isset($_POST['duration']) ? floatval($_POST['duration']) : 24;

$category = getCategory($wind);
$p_holland = hollandPressure($wind);
$p_ckz = ckzPressure($wind, $lat);
$rmw = rmwEstimate($wind, $lat);
$surge = stormSurge($wind, $pressure);
$deficit = pressureDeficit($pressure);
$chp = cycloneHeatPotential($sst);
$aceValue = ace($wind, $duration);

echo "
<h3>Results:</h3>
<ul>
  <li><strong>Saffir–Simpson Category:</strong> $category</li>
  <li><strong>Holland Pressure Estimate:</strong> $p_holland mb</li>
  <li><strong>CKZ Pressure Estimate:</strong> $p_ckz mb</li>
  <li><strong>Radius of Maximum Wind (RMW):</strong> $rmw km</li>
  <li><strong>Estimated Storm Surge:</strong> $surge meters</li>
  <li><strong>Pressure Deficit:</strong> $deficit mb</li>
  <li><strong>Cyclone Heat Potential:</strong> $chp units</li>
  <li><strong>Accumulated Cyclone Energy (ACE):</strong> $aceValue</li>
</ul>
";

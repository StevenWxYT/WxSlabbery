<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $calcType = $_POST['calcType'];
    $inputs = $_POST; // contains all dynamic inputs
    $result = '';
    $formula = '';
    $history = [];

    switch ($calcType) {
        case 'sshs':
            // Input: wind speed in knots
            $wind = floatval($inputs['wind_kts']);
            if ($wind >= 137) {
                $category = 5;
            } elseif ($wind >= 113) {
                $category = 4;
            } elseif ($wind >= 96) {
                $category = 3;
            } elseif ($wind >= 83) {
                $category = 2;
            } elseif ($wind >= 64) {
                $category = 1;
            } else {
                $category = 0; // Tropical Storm / Depression
            }
            $result = "Category $category Hurricane";
            $formula = "SSHS Category = based on wind speed ≥ thresholds";
            break;

        case 'holland':
            $pn = floatval($inputs['pn']);
            $pc = floatval($inputs['pc']);
            $r = floatval($inputs['r']);
            $rm = floatval($inputs['rm']);

            // Prevent division by zero or invalid log input
            if ($r <= 0 || $rm <= 0 || $pn <= $pc) {
                $result = "Invalid input values for Holland model.";
                $formula = "Ensure Pn > Pc and r, Rm > 0.";
            } else {
                $b = log(($pn - $pc) / ($pn - $pc * exp(-1))) / log($r / $rm);
                $result = "Holland B parameter ≈ " . round($b, 3);
                $formula = "B = log((Pn − Pc) / ((Pn − Pc) × e⁻¹)) / log(r / Rm)";
            }
            break;

        case 'ckz':
            $pressure = floatval($inputs['pressure_mb']);
            $a = 6.7;
            $b_val = 1010;
            $c = 0.644; // coefficients
            $wind = $a * pow(($b_val - $pressure), $c);
            $result = "Estimated wind ≈ " . round($wind, 2) . " knots";
            $formula = "V = A × (B − P)^C";
            break;

        case 'rmw':
            $latitude = floatval($inputs['latitude']);
            $rmw = 46.6 * exp(-0.015 * $latitude);
            $result = "Radius of Maximum Wind ≈ " . round($rmw, 2) . " km";
            $formula = "RMW = 46.6 × e^(−0.015 × latitude)";
            break;

        case 'surge':
            $wind = floatval($inputs['wind_kts']);
            $slope = floatval($inputs['slope']);
            $surge = 0.28 * $wind - 1.9 * $slope + 0.5;
            $result = "Estimated Storm Surge ≈ " . round($surge, 2) . " m";
            $formula = "Surge = 0.28 × V − 1.9 × S + 0.5";
            break;

        case 'pdeficit':
            $pn = floatval($inputs['pn']);
            $pc = floatval($inputs['pc']);
            $dp = $pn - $pc;
            $result = "Pressure Deficit ≈ " . round($dp, 2) . " mb";
            $formula = "ΔP = Pn − Pc";
            break;

        case 'chp':
            $depth = floatval($inputs['depth']);
            $sst = floatval($inputs['sst']);
            $chp = $depth * ($sst - 26);
            $result = "Cyclone Heat Potential ≈ " . round($chp, 2) . " kJ/cm²";
            $formula = "CHP = Depth × (SST − 26°C)";
            break;

        case 'ace':
            $wind = floatval($inputs['wind_kts']);
            $duration = floatval($inputs['duration_hrs']);
            $ace = pow($wind, 2) * $duration;
            $result = "Accumulated Cyclone Energy ≈ " . round($ace, 2) . " (knots²·hrs)";
            $formula = "ACE = V² × duration";
            break;

        default:
            $result = "No valid calculation type selected.";
            $formula = "";
            break;
    }

    echo "<h2>Result</h2><p>$result</p>";
    echo "<h4>Formula</h4><p>$formula</p>";
    echo "<h4>Inputs Used</h4><ul>";
    foreach ($inputs as $key => $value) {
        if ($key != "calcType") {
            echo "<li><strong>$key</strong>: $value</li>";
        }
    }
    echo "</ul>";

    echo "<h4>Memory Log</h4><p>Store result using M+ or M- for history reference.</p>";
} else {
    echo "Invalid access method.";
}

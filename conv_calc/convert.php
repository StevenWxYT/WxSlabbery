<?php
function convert($category, $from, $to, $value) {
    $units = [
        'length' => [
            'm' => 1,
            'km' => 1000,
            'cm' => 0.01,
            'mm' => 0.001,
            'mi' => 1609.34,
            'yd' => 0.9144,
            'ft' => 0.3048,
            'in' => 0.0254,
        ],
        'area' => [
            'sqm' => 1,
            'sqkm' => 1e6,
            'sqmi' => 2.59e6,
            'sqyd' => 0.836127,
            'sqft' => 0.092903,
            'sqin' => 0.00064516,
            'acre' => 4046.86,
            'hectare' => 10000,
        ],
        'volume' => [
            'm3' => 1,
            'liter' => 0.001,
            'ml' => 0.000001,
            'gallon' => 0.00378541,
            'quart' => 0.000946353,
            'pint' => 0.000473176,
            'cup' => 0.00024,
            'oz' => 2.957e-5,
        ],
        'mass' => [
            'kg' => 1,
            'g' => 0.001,
            'mg' => 0.000001,
            'lb' => 0.453592,
            'oz' => 0.0283495,
            'tonne' => 1000,
        ],
        'speed' => [
            'mps' => 1,
            'kmh' => 0.277778,
            'mph' => 0.44704,
            'knot' => 0.514444,
        ],
        'temperature' => [
            // handled separately
        ],
        'time' => [
            'sec' => 1,
            'min' => 60,
            'hr' => 3600,
            'day' => 86400,
            'week' => 604800,
        ],
        'pressure' => [
            'pa' => 1,
            'kpa' => 1000,
            'bar' => 100000,
            'psi' => 6894.76,
            'atm' => 101325,
        ],
        'angle' => [
            'deg' => 1,
            'rad' => 57.2958,
            'grad' => 0.9,
        ],
        'power' => [
            'watt' => 1,
            'kw' => 1000,
            'hp' => 745.7,
        ],
        'data' => [
            'bit' => 1,
            'byte' => 8,
            'kb' => 8000,
            'mb' => 8e6,
            'gb' => 8e9,
        ],
        'energy' => [
            'j' => 1,
            'kj' => 1000,
            'cal' => 4.184,
            'kcal' => 4184,
            'wh' => 3600,
            'kwh' => 3.6e6,
        ]
    ];

    if ($category === "temperature") {
        return convertTemperature($from, $to, $value);
    }

    if (!isset($units[$category][$from]) || !isset($units[$category][$to])) {
        return "Invalid unit.";
    }

    $base = $value * $units[$category][$from];
    $converted = $base / $units[$category][$to];
    return "$value $from = $converted $to";
}

function convertTemperature($from, $to, $value) {
    if ($from === $to) return "$value $from = $value $to";

    $celsius = match ($from) {
        'c' => $value,
        'f' => ($value - 32) * 5 / 9,
        'k' => $value - 273.15,
        default => null
    };

    $result = match ($to) {
        'c' => $celsius,
        'f' => $celsius * 9 / 5 + 32,
        'k' => $celsius + 273.15,
        default => null
    };

    return "$value $from = $result $to";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = $_POST['category'] ?? '';
    $from = $_POST['from_unit'] ?? '';
    $to = $_POST['to_unit'] ?? '';
    $value = floatval($_POST['value'] ?? 0);
    $result = convert($category, $from, $to, $value);
}
?>

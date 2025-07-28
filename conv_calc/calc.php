<?php include "convert.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unit Converter</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="converter-container">
        <h1>Universal Unit Converter</h1>
        <form id="converterForm" method="POST">
            <label for="category">Category:</label>
            <select name="category" id="category" onchange="updateUnits()">
                <option value="length">Length</option>
                <option value="area">Area</option>
                <option value="volume">Volume</option>
                <option value="mass">Mass</option>
                <option value="speed">Speed</option>
                <option value="temperature">Temperature</option>
                <option value="time">Time</option>
                <option value="pressure">Pressure</option>
                <option value="angle">Angle</option>
                <option value="power">Power</option>
                <option value="data">Data</option>
                <option value="energy">Energy</option>
            </select>

            <label for="from_unit">From:</label>
            <select name="from_unit" id="from_unit"></select>

            <label for="to_unit">To:</label>
            <select name="to_unit" id="to_unit"></select>

            <label for="value">Value:</label>
            <input type="number" step="any" name="value" id="value" required>

            <button type="submit">Convert</button>
        </form>

        <?php if (isset($result)) : ?>
            <div class="result">
                <strong>Result:</strong> <?= htmlspecialchars($result) ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>
</html>

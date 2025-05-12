<?php
// Load JSON file safely
$file = 'data.json';
$data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Get filter inputs from URL
$typeFilter = $_GET['type'] ?? '';
$limit = $_GET['limit'] ?? '10';

// Apply type filter
$filtered = array_filter($data, function($row) use ($typeFilter) {
    return !$typeFilter || $row['type'] === $typeFilter;
});

// Apply result limit
if ($limit !== 'all') {
    $filtered = array_slice($filtered, 0, (int)$limit);
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>CTI Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1 >Cyber Threat Intelligence Dashboard</h1>
    <div class="block">
    

    <form method="GET">
    <label for="type" style="font-weight: 10px;">Filter by Type:</label>
    <select name="type" id="type">
        <option value="">All</option>
        <option value="IP" <?= $typeFilter == 'IP' ? 'selected' : '' ?>>IP</option>
    </select>

    <label for="limit">Show:</label>
    <select name="limit" id="limit">
        <option value="10" <?= ($_GET['limit'] ?? '') == '100' ? 'selected' : '' ?>>10</option>
        <option value="100" <?= ($_GET['limit'] ?? '') == '100' ? 'selected' : '' ?>>100</option>
        <option value="500" <?= ($_GET['limit'] ?? '') == '500' ? 'selected' : '' ?>>500</option>
        <option value="1000" <?= ($_GET['limit'] ?? '') == '1000' ? 'selected' : '' ?>>1000</option>
        <option value="all" <?= ($_GET['limit'] ?? '') == 'all' ? 'selected' : '' ?>>All</option>
    </select>

    <input type="submit" value="Apply">
    </form>

    </div>
    <table>
        <tr><th>Indicator</th><th>Type</th><th>Source</th><th>Severity</th><th>Country</th><th>Timestamp</th></tr>
        <?php foreach ($filtered as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['indicator']) ?></td>
        <td><?= $row['type'] ?></td>
        <td><?= $row['source'] ?></td>
        <td><?= $row['severity'] ?></td>
        <td><?= $row['country'] ?></td>
        <td><?= $row['timestamp'] ?></td>
    </tr>
        <?php endforeach; ?>

    </table>
    <div class="block">
    <h2>Severity by Country</h2>
    <canvas id="chart" width="400" height="200"></canvas>
    </div>

    <script>
    const data = <?php echo json_encode($filtered); ?>;
    const countryMap = {};
    data.forEach(row => {
        if (!countryMap[row.country]) countryMap[row.country] = 0;
        countryMap[row.country] += parseInt(row.severity);
    });

    const labels = Object.keys(countryMap);
    const severity = Object.values(countryMap);

    new Chart(document.getElementById('chart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Severity',
                data: severity,
                backgroundColor: 'rgba(255, 99, 132, 0.6)'
            }]
        }
    });
    </script>
</body>
</html>

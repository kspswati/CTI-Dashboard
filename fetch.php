<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Your AbuseIPDB API Key
$apiKey = 'e4ce9e6dd01ddd58ef453632f65fff660a2b1c31e55f196a79d1f7e290a908b064587d8ef5d4a263';

// Fetch data from AbuseIPDB
$url = 'https://api.abuseipdb.com/api/v2/blacklist?confidenceMinimum=90';

$headers = [
    "Key: $apiKey",
    "Accept: application/json"
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Normalize: extract IP, abuseConfidenceScore, and country
$normalized = [];
if (isset($data['data'])) {
    foreach ($data['data'] as $item) {
        $normalized[] = [
            'indicator' => $item['ipAddress'],
            'type' => 'IP',
            'source' => 'AbuseIPDB',
            'severity' => $item['abuseConfidenceScore'],
            'country' => $item['countryCode'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    file_put_contents('data.json', json_encode($normalized));
    echo "Data fetched and saved successfully!";
} else {
    echo "Failed to fetch or parse data.";
}
?>

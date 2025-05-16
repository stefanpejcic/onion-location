<?php

$domain = $_GET['domain'] ?? '';

if (!preg_match('/^[a-zA-Z0-9.-]+$/', $domain)) {
    echo json_encode(['success' => false, 'message' => 'Invalid domain format.']);
    exit;
}

$url = "https://" . $domain;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Error fetching the URL: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);

if (stripos($header, 'onion-location: ') !== false) {
    preg_match('/onion-location: (http[s]?:\/\/[^\r\n]+)/i', $header, $matches);
    if (isset($matches[1])) {
        echo json_encode(['success' => true, 'found' => true, 'onion' => $matches[1]]);
    } else {
        echo json_encode(['success' => true, 'found' => false]);
    }
} else {
    echo json_encode(['success' => true, 'found' => false]);
}

curl_close($ch);

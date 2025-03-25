#!/usr/bin/env php
<?php
date_default_timezone_set('Asia/Taipei');

$basePath = dirname(__DIR__);
if (!file_exists($basePath . '/raw')) {
    mkdir($basePath . '/raw', 0777, true);
}

$baseUrl = 'https://web.water.gov.tw/wateroffmap/supply';
$apiEndpoints = [
    'getWaterOffCases' => 'https://web.water.gov.tw/wateroffapi/f/case/summary',
    'getWaterOffSupply' => 'https://web.water.gov.tw/wateroffapi/data/searchSupply/',
];

function fetchData($url, $postData = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    if ($postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
    }
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'data' => $result
    ];
}

// Fetch main list
$result = fetchData($apiEndpoints['getWaterOffCases']);
if ($result['code'] === 200) {
    $data = json_decode($result['data'], true);
    // sort by no
    usort($data, function($a, $b) {
        return strtotime($a['no']) - strtotime($b['no']);
    });
    file_put_contents($basePath . '/raw/getWaterOffCases.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Fetch supply data
$result = fetchData($apiEndpoints['getWaterOffSupply']);
if ($result['code'] === 200) {
    $data = json_decode($result['data'], true);
    // sort by id
    usort($data['supply'], function($a, $b) {
        return strtotime($a['id']) - strtotime($b['id']);
    });
    file_put_contents($basePath . '/raw/getWaterOffSupply.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

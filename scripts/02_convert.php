#!/usr/bin/env php
<?php
date_default_timezone_set('Asia/Taipei');

$basePath = dirname(__DIR__);

$docsPath = $basePath . '/docs';
if(!file_exists($docsPath)) {
    mkdir($docsPath, 0777, true);
}

class CoordinateConverter {
    const RADIUS = 6378137.0; // Earth's radius in meters

    public function WebMercatorToWGS84($x, $y) {
        $lng = ($x / self::RADIUS) * (180 / M_PI);
        $lat = (M_PI / 2 - 2 * atan(exp(-$y / self::RADIUS))) * (180 / M_PI);
        return [$lng, $lat];
    }
}

$converter = new CoordinateConverter();

// Process getWaterOffCases.json
$casesFile = $basePath . '/raw/getWaterOffCases.json';
if (file_exists($casesFile)) {
    $data = json_decode(file_get_contents($casesFile), true);
    if ($data) {
        foreach($data AS $k => $case) {
            if(isset($case['waterOffArea'])) {
                switch($case['waterOffArea']['type']) {
                    case 'MultiPolygon':
                        foreach($case['waterOffArea']['coordinates'] AS $k2 => $item) {
                            foreach($item AS $k3 => $item2) {
                                foreach($item2 AS $k4 => $item3) {
                                    $data[$k]['waterOffArea']['coordinates'][$k2][$k3][$k4] = $converter->WebMercatorToWGS84($item3[0], $item3[1]);
                                }
                            }
                        }
                        break;
                    case 'Polygon':
                        foreach($case['waterOffArea']['coordinates'] AS $k2 => $item) {
                            foreach($item AS $k3 => $item2) {
                                $data[$k]['waterOffArea']['coordinates'][$k2][$k3] = $converter->WebMercatorToWGS84($item2[0], $item2[1]);
                            }
                        }
                        break;
                }
            }
            if(isset($case['pressureDownArea'])) {
                switch($case['pressureDownArea']['type']) {
                    case 'MultiPolygon':
                        foreach($case['pressureDownArea']['coordinates'] AS $k2 => $item) {
                            foreach($item AS $k3 => $item2) {
                                foreach($item2 AS $k4 => $item3) {
                                    $data[$k]['pressureDownArea']['coordinates'][$k2][$k3][$k4] = $converter->WebMercatorToWGS84($item3[0], $item3[1]);
                                }
                            }
                        }
                        break;
                    case 'Polygon':
                        foreach($case['pressureDownArea']['coordinates'] AS $k2 => $item) {
                            foreach($item AS $k3 => $item2) {
                                $data[$k]['pressureDownArea']['coordinates'][$k2][$k3] = $converter->WebMercatorToWGS84($item2[0], $item2[1]);
                            }
                        }
                        break;
                }
            }
            $y = substr($data[$k]['no'], 0, 4);
            $yPath = $basePath . '/raw/cases/' . $y;
            if(!file_exists($yPath)) {
                mkdir($yPath, 0777, true);
            }
            file_put_contents($yPath . '/' . $data[$k]['no'] . '.json', 
                json_encode($data[$k], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        file_put_contents($docsPath . '/getWaterOffCases.json', 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

// Process getWaterOffSupply.json
$supplyFile = $basePath . '/raw/getWaterOffSupply.json';
if (file_exists($supplyFile)) {
    $data = json_decode(file_get_contents($supplyFile), true);
    if ($data) {
        // Process supply points
        if (isset($data['supply'])) {
            foreach ($data['supply'] as $k => $item) {
                if (isset($item['location']['coordinates'])) {
                    $coords = $converter->WebMercatorToWGS84(
                        $item['location']['coordinates'][0],
                        $item['location']['coordinates'][1]
                    );
                    $data['supply'][$k]['location']['coordinates'] = $coords;
                }
            }
        }
        
        // Process notsupply points
        if (isset($data['notsupply'])) {
            foreach ($data['notsupply'] as $k => $item) {
                if (isset($item['location']['coordinates'])) {
                    $coords = $converter->WebMercatorToWGS84(
                        $item['location']['coordinates'][0],
                        $item['location']['coordinates'][1]
                    );
                    $data['notsupply'][$k]['location']['coordinates'] = $coords;
                }
            }
        }
        
        file_put_contents($docsPath . '/getWaterOffSupply.json', 
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
} 
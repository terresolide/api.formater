<?php
$file = __DIR__."/data/geojson_isgi_auroral.json";
$content = file_get_contents( $file);
$auroral = json_decode( $content);

$file = __DIR__."/data/geojson_isgi_equatorial.json";
$content = file_get_contents( $file);
$equatorial = json_decode( $content);

$file = __DIR__."/data/geojson_isgi_polaire.json";
$content = file_get_contents( $file);
$polaire = json_decode( $content);

$file = __DIR__."/data/geojson_isgi_subauroral.json";
$content = file_get_contents( $file);
$subauroral = json_decode( $content);

// $file = __DIR__."/data/geojson_isgi_global.json";
// $content = file_get_contents( $file);
// $global = json_decode( $content);

$result = array(
        "type" => "FeatureCollection",
        "features" => array( $auroral, $equatorial, $polaire, $subauroral)
);

header("Content-Type: application/json");
echo json_encode($result, JSON_NUMERIC_CHECK);
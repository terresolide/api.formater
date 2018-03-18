<?php
$content = file_get_contents( "data/geojson_observatories.json");
$result = json_decode( $content);
header("Content-Type: application/json");
echo json_encode( $result,  JSON_NUMERIC_CHECK);
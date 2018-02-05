<?php
$content = file_get_contents( "data/geojson_catalog2.json");
if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
}
header("Content-Type: application/json");
echo $content;
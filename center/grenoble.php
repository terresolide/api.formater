<?php

include_once '../class/Geotiff.php';
$geotiff = new \geotiff\Request( $_SERVER['REQUEST_URI']);
$geotiff->execute($_GET);

if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
}
header("Content-Type: application/json");

echo $geotiff->to_json();

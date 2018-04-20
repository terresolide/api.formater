<?php

include_once '../class/Bcmt.php';
$bcmt = new \bcmt\Request( $_SERVER['REQUEST_URI']);
$bcmt->execute($_GET);

if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
}
header("Content-Type: application/json");

echo $bcmt->to_json();

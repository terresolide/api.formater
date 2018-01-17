<?php

include_once '../class/Isgi.php';
$isgi = new \isgi\Request( $_SERVER['REQUEST_URI']);
$isgi->execute($_GET);

if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
}
//header("Content-Type: application/json");

echo $isgi->to_json();
<?php

include_once "config.php";
include_once "functions.php";
include_once 'class/Feature.php';
$features = new FeatureSearcher( );
$features->execute($_GET);

if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
}
header("Content-Type: application/json");

echo $features->to_json();

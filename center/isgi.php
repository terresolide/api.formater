<?php
session_start();
if(isset($_SESSION["token"])){
	$token = $_SESSION["token"];
}else{
	$token = uniqid();
	$_SESSION["token"] = $token;
}
include_once '../class/Isgi.php';


$isgi = new \isgi\Request( $_SERVER['REQUEST_URI']);

$isgi->execute($_GET);

// if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
//     header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
//     header('Access-Control-Allow-Credentials: true');
//     header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//    // header('Access-Control-Max-Age: 86400'); 
    
// }
$isgi->output();

<?php

include_once '../class/Isgi.php';


$isgi = new \isgi\Request( $_SERVER['REQUEST_URI']);
$isgi->execute($_GET);

if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
}

if( $isgi->is_archive()){
	$archive = $isgi->get_result();

	header('Content-Type: application/octet-stream');
	header("Content-Transfer-Encoding: Binary");
	header('Content-disposition: attachment; filename="'.$archive["name"].'"');
	echo readfile($archive["url"]);
}else{
   header("Content-Type: application/json");

   echo $isgi->to_json();
}

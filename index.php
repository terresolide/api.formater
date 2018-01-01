<?php
if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
    
}
var_dump( $_GET);
//if(isset($_GET["cds"]) && file_exists(__DIR__."/cds/".$_GET ["cds"].".php")){
    
    //include_once __DIR__."/cds/".$_GET ["cds"].".php";
//}






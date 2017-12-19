<?php
var_dump($_GET);
var_dump(__DIR__."/cds/".$_GET ["cds"].".php");
if(file_exists(__DIR__."/cds/".$_GET ["cds"].".php")){
    echo "exists";
    include_once "cds/".$_GET ["cds"].".php";
}
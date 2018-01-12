<?php
var_dump("bcmt-obs");
var_dump($_SERVER['REQUEST_URI']);
var_dump($_GET);
include_once '../class/Bcmt.php';
$bcmt = new BcmtResearch( $_SERVER['REQUEST_URI'], $_GET);
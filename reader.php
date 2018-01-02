<?php
include_once 'class/Iaga.php';

//$content = fopen( "data/box201602qhor.hor", "rb");

$reader = new Iaga( array("data/box201602qhor.hor"), "2016-02-02","2016-02-03");
//var_dump($reader->meta)var_dump($reader->data);


/*$conn_id = ftp_connect("ftp.bcmt.fr") or die('{ "error": "NO_FTP_CONNEXION"}');




if( ! @ftp_login( $conn_id,"bcmt_public", "bcmt" ) ){
    die( '{ "error": "BAD_LOGIN_PWD"}');
    
}*/

$reader2 = new Iaga(array("/QUASI_DEFINITIVE/box/hor/box201602qhor.hor", "/QUASI_DEFINITIVE/box/hor/box201603qhor.hor"),"2016-02-29","2016-03-02", "ftp://bcmt_public:bcmt@ftp.bcmt.fr/");
var_dump( $reader2->data);
var_dump( $reader2->meta);
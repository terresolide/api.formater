<?php
/** configuration **/

$ftp_server = "ftp.bcmt.fr";
$ftp_user = "bcmt_public";
$ftp_pwd = "bcmt";


//ini_set("display_errors", 0); 

$observatories = array( "aae", "ams", "bng", "box", "clf", "czt", "dlt", "dmc", "drv", "ipm", "kou", "lzh", "mbo", "paf", "phu");

if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
}
header("Content-Type: application/json"); 

/** observatory **/
if( !isset( $_GET["ob"]) ){
    echo '{"error": "NO_OBSERVATORY"}';
    exit;
}

$observatory = $_GET["ob"];
if( ! in_array( $observatory, $observatories)){
    echo '{"error": "OBSERVATORY_DO_NOT_EXIST"}';
    exit;
}

/** start and end date **/
if( !isset( $_GET["start"]) && !isset($_GET["end"])){
    //last 10 days
    $start = new DateTime(" -10 day");
    $end = new DateTime();
    
    

}else if( isset($_GET["start"])){
    $start = new DateTime( $_GET["start"]);
    if(isset( $_GET["end"])){
        $end = new DateTime( $_GET["end"]);
    }else{
        $end = new DateTime();
    }
}

// diff dates
$diff = $start->diff( $end);

//var_dump($diff);
if( $diff->invert === 1){
    echo '{"error": "INCONSISTENT_DATE"}';
    exit;
}
if($diff->y > 25){
    $type = "yea";
}else if( $diff->y >= 1){
    //search month data
    $type ="mon";
}else if( $diff->days > 20 ){
    //search days data
    $type = "day";
}else{
    //search hor data
    $type = "hor";
}

//search first in DEFINITIVE data

$directory0 = "/DEFINITIVE/".$observatory."/".$type;
$directory1 = "/QUASI_DEFINITIVE".$observatory."/".$type;

$start_year = intVal($start->format("Y"));
$end_year = intVal( $end->format("Y"));


// Mise en place d'une connexion
$conn_id = ftp_connect($ftp_server) or die('{ "error": "NO_FTP_CONNEXION"}'); 




if( ! @ftp_login( $conn_id, $ftp_user, $ftp_pwd ) ){
    die( '{ "error": "BAD_LOGIN_PWD"}');
}


// search in file DEFINITIVE or QUASI-DEFINITIVE
$files = ftp_nlist ( $conn_id , $directory0);

//var_dump( $files);
//$ret = ftp_nb_fget($conn_id, $fp, $file, FTP_BINARY);
/*ob_start();
$result = ftp_get($conn_id, "php://output", $file, FTP_ASCII);
$data = ob_get_contents();
ob_end_clean();
echo $data;*/

//$handle = file_get_contents("ftp://ftp.bcmt.fr/DEFINITIVE/ams/yea/ams.year");


//$handle = ftp_get_contents("ftp://ftp.bcmt.fr/DEFINITIVE/ams/yea/ams.year", "php://output");

//echo $handle;

include_once '../class/Iaga.php';


$iaga = new Iaga( array("../data/box201602qhor.hor"), "2016-02-02","2016-02-03");

echo $iaga->json();

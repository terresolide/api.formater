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
//header("Content-Type: application/json"); 

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
var_dump($diff);
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

var_dump( $files);
//$ret = ftp_nb_fget($conn_id, $fp, $file, FTP_BINARY);
ob_start();
$result = ftp_get($conn_id, "php://output", $file, FTP_ASCII);
$data = ob_get_contents();
ob_end_clean();
echo $data;

//$handle = file_get_contents("ftp://ftp.bcmt.fr/DEFINITIVE/ams/yea/ams.year");

function ftp_get_contents($ftp_stream, $remote_file, $mode=FTP_ASCII, $resume_pos=null){
    $pipes=stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
    if($pipes===false) return false;
    if(!stream_set_blocking($pipes[1], 0)){
        fclose($pipes[0]); fclose($pipes[1]);
        return false;
    }
    $fail=false;
    $data='';
    if(is_null($resume_pos)){
        $ret=ftp_nb_fget($ftp_stream, $pipes[0], $remote_file, $mode);
    } else {
        $ret=ftp_nb_fget($ftp_stream, $pipes[0], $remote_file, $mode, $resume_pos);
    }
    while($ret==FTP_MOREDATA){
        while(!$fail && !feof($pipes[1])){
            $r=fread($pipes[1], 8192);
            if($r==='') break;
            if($r===false){ $fail=true; break; }
            $data.=$r;
        }
        $ret=ftp_nb_continue($ftp_stream);
    }
    while(!$fail && !feof($pipes[1])){
        $r=fread($pipes[1], 8192);
        if($r==='') break;
        if($r===false){ $fail=true; break; }
        $data.=$r;
    }
    fclose($pipes[0]); fclose($pipes[1]);
    if($fail || $ret!=FTP_FINISHED) return false;
    return $data;
}
//$handle = ftp_get_contents("ftp://ftp.bcmt.fr/DEFINITIVE/ams/yea/ams.year", "php://output");

//echo $handle;
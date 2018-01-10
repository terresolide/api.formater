<?php
/** configuration **/

$ftp_server = "ftp.bcmt.fr";
$ftp_user = "bcmt_public";
$ftp_pwd = "bcmt";

$observatories = array( "aae", "ams", "bng", "box", "clf", "czt", "dlt", "dmc", "drv", "ipm", "kou", "lzh", "mbo", "paf", "phu");

/**
 * headers for response
 */
/**
 * Response
 */
if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
}
//header("Content-Type: application/json");

/**
 * CONTROLE PARAMETERS
 */

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


/**
 * SEARCH FILES ON FTP SERVER
 */
function search_files( $directory, $prefix, $observatory, $type, $start, $end){
    global $conn_id;
    $files = ftp_nlist ( $conn_id , $directory);
    $results = array();

    switch( $type){
        case "yea":
        case "mon":
        case "day":
            $start_year = intVal($start->format("Y"));
            $end_year = intVal( $end->format("Y"));
            for($i= $start_year; $i<= $end_year; $i++){
                $file = $directory."/".$observatory . $i. $prefix . $type.".".$type;
 
                if(in_array( $file, $files)){
                    array_push( $results, $file);
                }
            }
            break;
        case "hor":
            $current = new DateTime( $start->format("Y-m-d"));
            
            while( $current<= $end){
                
                $file = $directory."/".$observatory . $current->format("Ym"). $prefix . $type.".".$type;

                if(in_array( $file, $files)){
                    array_push( $results, $file);
                   // array_push( $done, $current->format("Ym"));
                }
                $current->modify( 'first day of next month' );
                
            }
            break;
    }
    
    return $results;
}
if($diff->y > 25){
    $type = "yea";
 
}else if( $diff->y >= 1){
    //search month data
    $type ="mon";
}else if( $diff->days > 15 ){
    //search days data
    $type = "day";
}else{
    //search hor data
    $type = "hor";
}

$conn_id = ftp_connect($ftp_server) or die('{ "error": "NO_FTP_CONNEXION"}'); 

if( ! @ftp_login( $conn_id, $ftp_user, $ftp_pwd ) ){
    die( '{ "error": "BAD_LOGIN_PWD"}');
}

//search first in DEFINITIVE data
// @todo choose quasi_definitive data if no  definitive

$directory0 = "/DEFINITIVE/".$observatory."/".$type;
$directory1 = "/QUASI_DEFINITIVE".$observatory."/".$type;
$result0 = search_files( $directory0,"d", $observatory, $type, $start, $end);




/**
 * extract data from files
 */
include_once '../class/Iaga.php';



$iaga = new Iaga( $result0, $observatory, $start->format("Y-m-d"),$end->format("Y-m-d"), "ftp://bcmt_public:bcmt@ftp.bcmt.fr/");

if(empty($iaga->meta)){
	$result1= search_files($directory1, "q", $observatory, $type, $start,$end);
   $iaga = new Iaga( $result1, $observatory, $start->format("Y-m-d"),$end->format("Y-m-d"), "ftp://bcmt_public:bcmt@ftp.bcmt.fr/");
}
ftp_close($conn_id);
/**
 * response
 */

echo $iaga->json();

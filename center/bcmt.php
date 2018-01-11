<?php
/** configuration **/

$ftp_server = "ftp.bcmt.fr";
$ftp_user = "bcmt_public";
$ftp_pwd = "bcmt";

$observatories = array( "aae", "ams", "bng", "box", "clf", "czt", "dlt", "dmc", "drv", "ipm", "kou", "lzh", "mbo", "paf", "phu", "ppt", "tan", "tam");

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
header("Content-Type: application/json");

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
    $start = new DateTime(" -3 day");
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

function steppify($delta) {
    /**  */
    if($delta<=1){
        return 1;
    }
    $precision = round( log10( $delta ));
   
    $p = pow(10, $precision);
 
    $max = ceil($delta / $p) * $p;
    return $max / 2 > $delta ? $max / 2 : $max;
};
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
/** 
 * search by min in variation
 */

function search_files_by_min( $obs,  $start, $end){
    global $conn_id;
 
    $directory0 = "";
    $results = array();
    $diff = $start->diff( $end );
    $days = $diff->days;
    $current = new DateTime( $start->format("Y-m-d"));
    $files = array();
    $step = steppify( $days/15);
    $cumul = 1;
    $last = false;
    while( $current<= $end && !$last){
        $directory = "/VARIATION/".$obs."/min/".$current->format("Y");
        //read directory if not done
        if( $directory != $directory0){
            $directory0 =  $directory;
            $files = ftp_nlist ( $conn_id , $directory0);
      
        }
        $file = $directory."/".$obs . $current->format("Ymd"). "vmin.min";
        
        if(in_array( $file, $files)){
            array_push( $results, $file);
            // array_push( $done, $current->format("Ym"));
        }
        
        $current->modify( '+'.$step.' days' );
        
    }
    //last day
    $directory = "/VARIATION/".$obs."/min/".$end->format("Y");
    if( $directory != $directory0){
        $directory0 =  $directory;
        $files = ftp_nlist ( $conn_id , $directory0);
        
    }
    $file = $directory."/".$obs . $end->format("Ymd"). "vmin.min";
    
    if(in_array( $file, $files)){
        array_push( $results, $file);
        // array_push( $done, $current->format("Ym"));
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
//$test = search_files_by_min($observatory, $start, $end);
//var_dump($test);
//exit;
//search first in DEFINITIVE data
$ismin = false;
$directory0 = "/DEFINITIVE/".$observatory."/".$type;
$directory1 = "/QUASI_DEFINITIVE/".$observatory."/".$type;
$result0 = search_files( $directory0,"d", $observatory, $type, $start, $end);

if(empty( $result0)){
    $result0 = search_files( $directory1,"q", $observatory, $type, $start, $end);
}
if( empty( $result0)){
    $result0 = search_files_by_min($observatory, $start, $end);
    $ismin = $diff->days + 1;
}

ftp_close($conn_id);
//exit;
/**
 * extract data from files
 */
include_once '../class/Iaga.php';



$iaga = new Iaga( $result0, $observatory, $start->format("Y-m-d"),$end->format("Y-m-d"), "ftp://bcmt_public:bcmt@ftp.bcmt.fr/", $ismin);



/**
 * response
 */

echo $iaga->json();

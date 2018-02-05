<?php

// ftp connexion
const FTP_SERVER = "ftp.bcmt.fr";
const FTP_USER = "bcmt_public";
const FTP_PWD = "bcmt";
$conn_id = ftp_connect(FTP_SERVER) or die('{ "error": "NO_FTP_CONNEXION"}');
if(  ! @ftp_login( $conn_id, FTP_USER, FTP_PWD) ){
    echo "no connexion";
    exit;
}
ftp_pasv($conn_id, true);

function extract_resolution( $url){
   $pattern = "([^/]*$)";
   preg_match( $pattern, $url, $matches);
   switch( $matches[0]){
       case "min":
       case "sec":
       case "day":
           return $matches[0];
           break;
       case "hor":
           return "hour";
           break;
       case "yea":
           return "year";
           break;
       case "mon":
           return "month";
       
   }
}
function search_infos(&$obs, $code, $dataType){
    global $conn_id;
    //var_dump($code);
    $directory = "/".$dataType."/".strtolower( $code);
    $resolutions = ftp_nlist( $conn_id, $directory);
    if( $resolutions === false){
        return false;
    }
    $obs->observedProperty->timeResolution = [];
    foreach( $resolutions as $resolution){
        array_push( $obs->observedProperty->timeResolution, extract_resolution( $resolution));
    }
    $directory ="/".$dataType."/".strtolower( $code)."/min/";
    
    $list1 = ftp_nlist ( $conn_id , $directory);
    // var_dump($list1);
    if($list1 != false){
        //  var_dump("ici");
        $startDirectory = $list1[0];
        $listStart= ftp_nlist( $conn_id, $startDirectory);
        if( count( $listStart) == 0){
            $startDirectory = $list1[1];
            $listStart = ftp_nlist( $conn_id, $startDirectory);
        }
        //parcours du premier dossier
       
        //extract date
        $truc = str_replace("/", "\/", $startDirectory);
        //var_dump($truc);
        //var_dump( $list1[0]);
      
        $pattern = "/^".$truc."\/[a-z]{3}([0-9]{4})([0-9]{2})([0-9]{2}).*$/";
        // var_dump($pattern);
        $ok = preg_match($pattern,$listStart[0], $matches);
        // var_dump($matches);
        $startDate = $matches[1]."-".$matches[2]."-".$matches[3];
        // var_dump( $startDate);
        $obs->temporalExtents->start = $startDate;

        $endDirectory = $list1[ count($list1)-1];
        $listEnd = ftp_nlist( $conn_id, $endDirectory);
        if( count( $listEnd) == 0){
            $endDirectory = $list1[ count($list1)-2];
            $listEnd = ftp_nlist( $conn_id, $endDirectory);
        }
        $truc = str_replace("/", "\/", $endDirectory);
        $pattern = "/^".$truc."\/[a-z]{3}([0-9]{4})([0-9]{2})([0-9]{2}).*$/";
        
        preg_match($pattern, $listEnd[ count($listEnd)-1], $matches);
        $endDate = $matches[1]."-".$matches[2]."-".$matches[3];
        
        if( $dataType == "VARIATION"){
            $now = new DateTime();
            $end = new DateTime( $endDate);
            
            $diff = $end->diff( $now);
            if( $diff->days<4 ){
                $endDate ="now";
            }
            unset($obs->procedure->method);
        }
       
        
        $obs->temporalExtents->end = $endDate;
        return true;
    }else{
        return false;
    }
}
$content = file_get_contents( "data/geojson_catalog.json");
$result = json_decode( $content);

$lieux = $result->features;
foreach($lieux as $key => $lieu ){
   // var_dump( $lieu->properties->code);
    $obs2 = [];
    foreach( $lieu->properties->observations as $obs){
       
        if(strpos( $obs->customId, "VARIATION")>0){
            //search min file
            if(search_infos( $obs, $lieu->properties->code, "VARIATION")){
                array_push($obs2, $obs);
            }

        }
        if( strpos( $obs->customId, "-DEFINITIVE")){
            if(search_infos( $obs, $lieu->properties->code, "DEFINITIVE")){
                array_push($obs2, $obs);
            }
        }
        if( strpos( $obs->customId, "QUASI_DEFINITIVE")){
            if(search_infos( $obs, $lieu->properties->code, "QUASI_DEFINITIVE")){
                array_push($obs2, $obs);
            }
        }
        
    }
    $lieu->properties->observations = $obs2;
}
ftp_close($conn_id);
    file_put_contents("data/geojson_catalog2.json",json_encode( $result, JSON_NUMERIC_CHECK) );
    header("Content-Type: application/json");
    echo json_encode( $result, JSON_NUMERIC_CHECK);
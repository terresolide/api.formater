<?php
/**
 * MISE A JOUR BROUILLON DES DATES DES DONNEES
 * MET LE RESULTAT DANS LE FICHIER geojson_bcmt_test2.json
 */

include "../config.php";
// ftp connexion

$infile =  DATA_FILE_BCMT;
$outputfile = DATA_DIR."/geojson_bcmt_ft_test2.json";
$conn_id = ftp_connect(BCMT_FTP_SERVER) or die('{ "error": "NO_FTP_CONNEXION"}');
if(  ! @ftp_login( $conn_id, BCMT_FTP_USER, BCMT_FTP_PWD) ){
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

    $directory = "/".$dataType."/".strtolower( $code);
    $resolutions = ftp_nlist( $conn_id, $directory);
 
    if( $resolutions === false){
        return false;
    }
    $obs->observedProperty->timeResolution = [];
    foreach( $resolutions as $resolution){
        
        array_push( $obs->observedProperty->timeResolution, extract_resolution( $resolution));
    }
    $base = "/".$dataType."/".strtolower( $code);
    if(in_array(  $base."/min", $resolutions)){
        $res = "min";
    }else{
        $res = "hor";
    }

    $directory = $base."/".$res."/";
    
    $list1 = ftp_nlist ( $conn_id , $directory);
    if($list1 != false){
        if($res == "hor"){
           //premier fichier
            $truc = str_replace("/", "\/", $base."/".$res);
            $pattern = "/^".$truc."\/[a-z]{3}([0-9]{4})([0-9]{2}).*$/";
            
            $ok = preg_match($pattern,$list1[0], $matches);
            $startDate = $matches[1]."-".$matches[2]."-01";
            $ok = preg_match($pattern,$list1[ count($list1)-1], $matches);
            $endDate = $matches[1]."-".$matches[2]."-01";
            
        }else{

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
          
            $pattern = "/^".$truc."\/[a-z]{3}([0-9]{4})([0-9]{2})([0-9]{2})?.*$/";
            // var_dump($pattern);
            
            $ok = preg_match($pattern,$listStart[0], $matches);

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
           
        }
        $obs->dataLastUpdate = $endDate;
        $now = new DateTime();
        if( $dataType == "VARIATION"){
            
            $end = new DateTime( $endDate);
            
            $diff = $end->diff( $now);
            if( $diff->days<4 ){
                $endDate ="now";
                $obs->dataLastUpdate = "now";
            }
            unset($obs->procedure->method);
        }
        $obs->metadataLastUpdate = $now->format("Y-m-d");
       
        $obs->api->parameters = array( "type" => $dataType);
        $obs->temporalExtents->end = $endDate;
        
        return true;
        
    }else{
        return false;
    }
}
$content = file_get_contents($infile);
$result = json_decode( $content);

function computeTemporal( $obs, $temporal){
	if($temporal->start > $obs->temporalExtents->start ){
		$temporal->start = $obs->temporalExtents->start;
	}
	if($temporal->end < $obs->temporalExtents->end || $obs->temporalExtents->end == "now"){
		$temporal->end = $obs->temporalExtents->end ;
	}
	return $temporal;
}
$lieux = $result->features;
foreach($lieux as $key => $lieu ){
   // var_dump( $lieu->properties->code);
    $obs2 = [];
   $temporal = new StdClass();
   $temporal->start = "2018-03-06";
   $temporal->end = "1700-01-01";
   // $lieu->identifiers = array( "customId" => $lieu->code);
   // unset( $lieu->identifier);
$code = $lieu->properties->identifiers->customId;
    foreach( $lieu->properties->observations as $obs){
    	
        if(strpos( $obs->identifiers->customId, "VARIATION")>0){
            //search min file
            if(search_infos( $obs, $code, "VARIATION")){
                array_push($obs2, $obs);
                $temporal = computeTemporal( $obs, $temporal);
         
            }

        }
        if( strpos(  $obs->identifiers->customId, "-DEFINITIVE")){
            if(search_infos( $obs, $code, "DEFINITIVE")){
                array_push($obs2, $obs);
                $temporal = computeTemporal( $obs, $temporal);
            }
           
        }
        if( strpos(  $obs->identifiers->customId, "QUASI_DEFINITIVE")){
            if(search_infos( $obs, $code, "QUASI_DEFINITIVE")){
                array_push($obs2, $obs);
                $temporal = computeTemporal( $obs, $temporal);
            }
           
        }
     
        
    }
    $lieu->properties->observations = $obs2;
    $lieu->properties->temporalExtents = $temporal;
    // si la date de fin du lieu est now, on met tous les observations avec now en date end
    if( $lieu->properties->temporalExtents->end == "now"){
        foreach($lieu->properties->observations as $obs){
            $obs->temporalExtents->end = "now";
        }
    }
}


ftp_close($conn_id);
    file_put_contents($outputfile,json_encode( $result, JSON_NUMERIC_CHECK) );
   header("Content-Type: application/json");
   echo json_encode( $result, JSON_NUMERIC_CHECK);
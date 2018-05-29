<?php
/** Search last date of modification files*/
/** change source file if needed*/
$infile =  "../data/geojson_isgi_ft.json";
$outputfile = "../data/geojson_isgi_local.json";

$content = file_get_contents($infile);
$result = json_decode( $content);


$lieux = $result->features;
// $withdoi = array("aae", "ams", "bng",  "clf",  "czt",  "dlt",  "dmc",  "drv",  "ipm",  "kou",  "mbo",  "paf", "phu", "ppt",  "psm",  "tan",  "vlj");

//Modification des titres de toutes les observations du BCMT pour supprimer les codes


foreach($lieux as $key => $lieu ){
   // var_dump( $lieu->properties->code);
   // $obs2 = [];
    //$lieu->properties->identifiers = array( "customId" => $lieu->properties->code);
   // unset( $lieu->properties->identifier);
   // unset( $lieu->properties->code);
   
    foreach( $lieu->properties->observations as $obs){
    	if(isset( $obs->api)){
	    	$url = str_replace( "api.poleterresolide.fr", "api.formater",$obs->api->url);
	    	$url = str_replace( "https", "http", $url);
	    	$obs->api->url = $url;
    	}
    	
    }
   // $lieu->properties->observations = $obs2;
}

    file_put_contents($outputfile,json_encode( $result, JSON_NUMERIC_CHECK) );
   header("Content-Type: application/json");
   echo json_encode( $result, JSON_NUMERIC_CHECK);
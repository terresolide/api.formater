<?php
include "config_ft.php";
define('OUTPUT_FILE', APP_DIR.'/data/geojson_bcmt_ft.json');

$content = file_get_contents( DATA_FILE);
$result = json_decode( $content);

$lieux = $result->features;
foreach($lieux as $key => $lieu ){
   // var_dump( $lieu->properties->code);
   $code = $lieu->properties->code;
    $obs2 = [];
    foreach( $lieu->properties->observations as $obs){
    	$obs->api->url = APP_URL."/cds/bcmt/data/".strtolower($code);
       
    }
}

    file_put_contents(OUTPUT_FILE,json_encode( $result, JSON_NUMERIC_CHECK) );
    header("Content-Type: application/json");
    echo json_encode( $result, JSON_NUMERIC_CHECK);
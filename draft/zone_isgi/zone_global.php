<?php
include_once "../../config.php";


$now = new DateTime();
$le10 = $now->format("Y-m")."-10";

if( $le10 > $now->format("Y-m-d")){
	$aux = clone $now;
	$aux->sub( new DateInterval('P1M'));
	$le10 = $aux->format("Y-m")."-10";
}
$content = file_get_contents(DIR_ISGI_INDICES."/indice_sc.json");
$indice_sc = json_decode( $content);
$indice_sc->dataLastUpdate = "2017-08-30";
$indice_sc->metadataLastUpdate = $now->format("Y-m-d");
$content = file_get_contents(DIR_ISGI_INDICES."/indice_sfe.json");
$indice_sfe = json_decode( $content);
$indice_sfe->dataLastUpdate = "2017-08-30";
$indice_sfe->metadataLastUpdate = $now->format("Y-m-d");
$content = file_get_contents(DIR_ISGI_INDICES."/indice_ckdays.json");
$indice_ckdays = json_decode( $content);

$content = file_get_contents(DIR_ISGI_INDICES."/indice_qdays.json");
$indice_qdays = json_decode( $content);
$indice_qdays->dataLastUpdate = $le10;
$indice_qdays->metadataLastUpdate = $now->format("Y-m-d");

$polygon = array( array( -180, -90), array( 180, -90), array( 180, 90), array( -180, 90), array( -180, -90));
$geojson = array(
		"type" => "Feature",
		"geometry" => array(
				"type" => "Polygon",
				"coordinates" =>  array($polygon)
		),
		"properties"=> array(
				"style"=> array(
						"fill" => "#98d7ff",
						"stroke" => "#98d7ff",
						"strokeWidth"=> "0",
						"fillOpacity" => "0"
				),
				"name" => array( 
						"fr" => "Global",
						"en" => "Global"
				),
			    "bbox" => array( 
			    		array( "south" => -90, "north" => 90, "east" => 180, "west"=>-180),
			    ),
				"description" => array(
						"fr" => "Terre entière",
						"en" => "Earth"
				),
				"identifiers" => array(),
				"observations" => array(
						//aa
						$indice_sc,
				        $indice_sfe,
				        $indice_ckdays,
				        $indice_qdays

				)
		)
);

header("Content-Type: application/json");
echo json_encode($geojson, JSON_NUMERIC_CHECK);



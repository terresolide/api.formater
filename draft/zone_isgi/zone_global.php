<?php
include_once "../../config.php";


$now = new DateTime();
$content = file_get_contents(DIR_ISGI_INDICES."/indice_sc.json");
$indice_sc = json_decode( $content);
$content = file_get_contents(DIR_ISGI_INDICES."/indice_sfe.json");
$indice_sfe = json_decode( $content);
$content = file_get_contents(DIR_ISGI_INDICES."/indice_ckdays.json");
$indice_ckdays = json_decode( $content);
$content = file_get_contents(DIR_ISGI_INDICES."/indice_qdays.json");
$indice_qdays = json_decode( $content);
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
						"stroke-width"=> "0",
						"fill-opacity" => "0"
				),
				"name" => array( 
						"fr" => "Global",
						"en" => "Global"
				),
			    "bbox" => array( 
			    		array( "south" => -90, "north" => 90, "east" => -180, "west"=>180),
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



<?php

// ZONE POLAIRE NORD
include "../../config.php";
//$file1 = __DIR__."/data/latitude-geomagnetique_58N.json";
$file2 = DIR_LATITUDES."/latitude-geomagnetique_74N.json";
// $content = file_get_contents( $file1);
// $result1 = json_decode( $content);

$content = file_get_contents( $file2);
$result2 = json_decode( $content);

$coordinates1 = array( array( -180,89), array(0,89), array( 180,89));
//array_push($coordinates1, [ $coordinates1[0][0]+360, $coordinates1[0][1]]);
$coordinates1 = array_reverse( $coordinates1);
$coordinates2 = $result2->geometry->coordinates;
array_push($coordinates2, [ $coordinates2[0][0]+360, $coordinates2[0][1]]);


$coordinates = array_merge($coordinates2, $coordinates1);
array_push( $coordinates, $coordinates2[0]);
$polygon1 = $coordinates;
//var_dump( $coordinates);

// ZONE AURORALE SUD
//$file1 = __DIR__."/data/latitude-geomagnetique_58S.json";
$file2 = DIR_LATITUDES."/latitude-geomagnetique_74S.json";
// $content = file_get_contents( $file1);
// $result1 = json_decode( $content);

$content = file_get_contents( $file2);
$result2 = json_decode( $content);

$coordinates1 = array( array(-180, -89), array(0, -89), array( 180, -89));
//array_push($coordinates1, [ $coordinates1[0][0]+360, $coordinates1[0][1]]);
$coordinates1 = array_reverse( $coordinates1);
$coordinates2 = $result2->geometry->coordinates;
array_push($coordinates2, [ $coordinates2[0][0]+360, $coordinates2[0][1]]);
foreach( $coordinates2 as $coord){
	if($coord[1] < -89){
		$coord[1] = -89;
	}
}

$coordinates = array_merge($coordinates2, $coordinates1);
array_push( $coordinates, $coordinates2[0]);

$polygon2 = $coordinates;
$now = new DateTime();
$geojson = array(
		"type" => "Feature",
		"geometry" => array(
				"type" => "MultiPolygon",
				"coordinates" => array( array($polygon1), array($polygon2))
				),
		"properties"=> array(
				"style"=> array(
						"fill" => "#eeffff",
						"stroke" => "#eeffff",
						"stroke-width"=> "1",
						"fill-opacity" => "0.3"
				),
				"name" => array( 
						"fr" => "Zone géomagnétique polaire",
						"en" => "Equatorial geomagnetic zone"
				),
			    "bbox" => array( 
			    		array( "south" => 64.67, "north" => 90, "east" => -180, "west"=>180),
			    		array( "south" => -90, "north" => -59.08, "east" => -180, "west"=>180)
			    ),
				"description" => array(
						"fr" => "Zones Nord et Sud de latitudes magnétiques supérieures à 74°. Avec les données de l'Année 2010",
						"en" => "geomagnetic latitude superior to 74° - Data from year 2010"
				),
				"identifiers" => array(),
				"observations" => array(
						//PC
						array(
								"id" => null,
								"metadataLastUpdate" => $now->format("Y-m-d"),
								"dataLastUpdate" => "",
								"title" => array( 
										"fr" => "Indice PC de l'activité magnétique polaire",
										"en" => "Polar Cap (PC) magnetic activity indices"
								),
								"abstract"=> array(
										"fr" => "L'indice PC surveille l'activité geomagnétique au-dessus des calottes polaires",
										"en" => "PC indices Polar Cap index horizontal component disturbances"
								),
								"description" => array(
										"fr" => "L'indice PC permet de surveiller l'activité géomagnétique au-dessus des calottes polaires causée par les changements dans le champ magnétique interplanétaire (FMI) et le vent solaire, entraînée par le champ électrique interplanétaire géo-efficace, sans tenir compte du temps, de la saison et du cycle solaire.",
										"en" => "PC indices o monitor the geomagnetic activity over the polar caps caused by changes in the interplanetary magnetic field (IMF) and solar wind, driven by the geoeffective interplanetary electric field irrespective of time, season and solar cycle."
								),
								"observedProperty" => array(
										"name" => "PC indices",
										"shortName" => "PCS /PCN",
										"type" => "time series",
										"timeResolution" => array( "min"),
										"unit" => "mV/m"
								),
								"domainOfInterest" => array( "GEOMAGNETISM"),
								"keywords" => array(array(
										"codeSpace" => "GMD",
										"code" => "ae35f430-6534-49de-8b4c-edfc1e98870a")),
								"pole"  => "formater",
								"status"=> "public",
								"formaterDataCenter" => array(
										"code" => "TS-ANO-4/SNO2",
										"name" => "ISGI"
								),
								"formats"=> array(  array("name" => "IAGA2002")),
								"processingLevel" => "L4",
								"license"=> array(
										"code" =>	"CC BY-NC 4.0",
										"url" =>	"https://creativecommons.org/licenses/by-nc/4.0/"
								),
								"temporalExtents" => array(
										"start" => "1975-01-01",
										"end"   => "now"
								),
								"quicklook" => array( 
										array( "url" => "https://api.poleterresolide.fr/images/isgi/reseau_PC.jpg")
								),
								"links" => array(
										array(
												"type" => "INFORMATION_LINK",
												"url"  => "http://isgi.unistra.fr/indices_pc.php",
												"description" => array(
														"fr" => "page de l'indice",
														"en" => "Index page"
												)
										),
										array( 
												"type" => "HTTP_DOWNLOAD_LINK",
												"url" => "http://isgi.unistra.fr/data_download.php"
										)
									
								),
							    "identifiers" => array(
							    		"customId" => "PC"
							    ),
								"distribution" => array(),
							    "contacts" =>array(
							    		array(
							    		"name" =>	"Aude Chambodut",
							    		"email"=>	"aude.chambodut@unistra.fr",
							    		"organisation"=>	"Ecole et Observatoire de la Terre",
							    		"address" =>	array(
							    				"streetAddress"=>	array(
							    						"Service des observatoires magnétiques",
							    						"5, rue René Descartes"
							    				),
							    				"postalCode" =>	67000,
							    				"addressLocality" =>	"Strasbourg",
							    				"addressCountry"=>	"FRANCE"
							    		),
							    		"telephone" =>	"33 (0)3 68 85 01 25",
							    		"roles" =>	array( "pointOfContact"),
							    		"orcId" =>	"0000-0001-8793-1315"
							    		)
							    ),
								"api" => array(
										"url" => "https://api.poleterresolide.fr/cds/isgi/data/PC",
										"name"=> "ISGI"
								)

						)
				)
		)
);

header("Content-Type: application/json");
echo json_encode($geojson, JSON_NUMERIC_CHECK);



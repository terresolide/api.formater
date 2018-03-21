<?php

// ZONE AURORALE NORD
include "../../config.php";

$file1 = DIR_LATITUDES."/latitude-geomagnetique_58N.json";
$file2 = DIR_LATITUDES."/latitude-geomagnetique_74N.json";
$content = file_get_contents( $file1);
$result1 = json_decode( $content);

$content = file_get_contents( $file2);
$result2 = json_decode( $content);

$coordinates1 = $result1->geometry->coordinates;
array_push($coordinates1, [ $coordinates1[0][0]+360, $coordinates1[0][1]]);
$coordinates1 = array_reverse( $coordinates1);
$coordinates2 = $result2->geometry->coordinates;
array_push($coordinates2, [ $coordinates2[0][0]+360, $coordinates2[0][1]]);


$coordinates = array_merge($coordinates2, $coordinates1);
array_push( $coordinates, $coordinates2[0]);
$polygon1 = $coordinates;
//var_dump( $coordinates);

// ZONE AURORALE SUD
$file1 = DIR_LATITUDES."/latitude-geomagnetique_58S.json";
$file2 = DIR_LATITUDES."/latitude-geomagnetique_74S.json";
$content = file_get_contents( $file1);
$result1 = json_decode( $content);

$content = file_get_contents( $file2);
$result2 = json_decode( $content);

$coordinates1 = $result1->geometry->coordinates;
array_push($coordinates1, [ $coordinates1[0][0]+360, $coordinates1[0][1]]);
$coordinates1 = array_reverse( $coordinates1);
$coordinates2 = $result2->geometry->coordinates;
array_push($coordinates2, [ $coordinates2[0][0]+360, $coordinates2[0][1]]);

foreach( $coordinates2 as $coord){
	if($coord[1] < -90){
		$coord[1] = -90;
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
						"fill" => "#c0e6ff",
						"stroke" => "#c0e6ff",
						"stroke-width"=> "1",
						"fill-opacity" => "0.3"
				),
				"name" => array( 
						"fr" => "Zone géomagnétique aurorale",
						"en" => "Auroral geomagnetic zone"
				),
			    "bbox" => array( 
			    		array( "south" => 47.83, "north" => 79.41, "east" => -180, "west"=>180),
			    		array( "south" => -90, "north" => -44.45, "east" => -180, "west"=>180),
			    ),
				"description" => array(
						"fr" => "Il s'agit de la zone de latitudes magnétiques autour de 69°. Année 2010",
						"en" => "Geomagnetic latitude around 69° ( Year 2010)"
				),
				"identifiers" => array(),
				"observations" => array(
						//AE
						array(
								"id" => null,
								"metadataLastUpdate" => $now->format("Y-m-d"),
								"dataLastUpdate" => "",
								"title" => array( 
										"fr" => "Indice géomagnétique AE",
										"en" => "AE geomagnetic indice"
								),
								"abstract"=> array(
										"fr" => "L'indice AE mesure les perturbations géomagnétiques horizontales dans la zone auroral",
										"en" => "Auroral index horizontal component disturbances"
								),
								"description" => array(
										"fr" => "L'indice AE permet de surveiller la signature magnétique des électrojets auroraux orientés vers l'est et l'ouest dans l'hémisphère Nord",
										"en" => "AE indice helps to monitor the magnetic signature of the eastward and westward auroral electrojets in the Northern hemisphere."
								),
								"observedProperty" => array(
										"name" => "Disturbance storm-time",
										"shortName" => "AE INDEX",
										"type" => "time series",
										"timeResolution" => array( "min"),
										"unit" => "nT"
								),
								"domainOfInterest" => array( "GEOMAGNETISM"),
								"keywords" => array(array(
										"codeSpace" => "GMD",
										"code" => "31f77d6b-72f7-45e6-93be-8ac5fd5dc373")),
								"pole"  => "formater",
								"status"=> "public",
								"formaterDataCenter" => array(
										"code" => "TS-ANO-4/SNO2",
										"name" => "ISGI"
								),
								"formats"=> array(  array("name" => "IAGA2002")),
								"processingLevel" => "L4",
								"temporalExtents" => array(
										"start" => "1957-01-01",
										"end"   => "now"
								),
								"quicklook" => array( 
										array( "url" => "https://api.poleterresolide.fr/images/isgi/reseau_AE.jpg")
								),
								"links" => array(
										array(
												"type" => "INFORMATION_LINK",
												"url"  => "http://isgi.unistra.fr/indices_ae.php",
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
							    		"customId" => "AE",
							    		"DOI" => "10.17593/15031-54800"
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
										"url" => "https://api.poleterresolide.fr/cds/isgi/data/AE",
										"name"=> "ISGI"
								)
										
							

								
						),
						//asigma
// 						array(
// 								"id" => null,
// 								"metadataLastUpdate" => $now->format("YYYY-MM-DD"),
// 								"dataLastUpdate" => "",
// 								"title" => array(
// 										"fr" => "Indices Asigma",
// 										"en" => "Asigma index"
// 								),
// 								"abstract"=> array(
// 										"fr" => "Fournir une caractérisation de l'activité géomagnétique locale dans 4 secteurs de longitudes et d'éventuelles divergences hémisphériques",
// 										"en" => "To provide a characterization of local geomagnetic activity in 4 longitudes sectors and possible hemispheric discrepancies "
// 								),
// 								"description" => array(
// 										"fr" => "Fournir une caractérisation de l'activité géomagnétique locale dans 4 secteurs de longitudes et d'éventuelles divergences hémisphériques",
// 										"en" => "To provide a characterization of local geomagnetic activity in 4 longitudes sectors and possible hemispheric discrepancies "
// 								),
// 								"observedProperty" => array(
// 										"name" => "asigma",
// 										"shortName" => "asigma",
// 										"type" => "time series",
// 										"timeResolution" => array( "hour"),
// 										"unit" => "nT"
// 								),
// 								"domainOfInterest" => array( "GEOMAGNETISM"),
// 								"keywords" => array(array(
// 										"codeSpace" => "GMD",
// 										"code" => "ae35f430-6534-49de-8b4c-edfc1e98870a")),
// 								"pole"  => "formater",
// 								"status"=> "public",
// 								"formaterDataCenter" => array(
// 										"code" => "TS-ANO-4/SNO2",
// 										"name" => "ISGI"
// 								),
// 								"formats"=> array(  array("name" => "IAGA2002")),
// 								"processingLevel" => "L4",
// 								"licence"=> array(
// 										"code" =>	"CC BY-NC 4.0",
// 										"url" =>	"https://creativecommons.org/licenses/by-nc/4.0/"
// 								),
// 								"temporalExtents" => array(
// 										"start" => "1959-01-01",
// 										"end"   => "now"
// 								),
// 								"quicklook" => array(
// 										array( "url" => "https://api.poleterresolide.fr/images/isgi/reseau_am.jpg")
// 								),
// 								"links" => array(
// 										array(
// 												"type" => "INFORMATION_LINK",
// 												"url"  => "http://isgi.unistra.fr/indices_asigma.php",
// 												"description" => array(
// 														"fr" => "page de l'indice",
// 														"en" => "Index page"
// 												)),
// 										array(
// 												"type" => "HTTP_DOWNLOAD_LINK",
// 												"url" => "http://isgi.unistra.fr/oi_data_download.php"
// 										)
												
// 								),
// 								"identifiers" => array(
// 										"customId" => "asigma"
// 										//"DOI" => "10.17593/14515-74000"
// 								),
// 								"distribution" => array(),
// 								"contacts" =>array(
// 										array(
// 											"name" =>	"Aude Chambodut",
// 											"email"=>	"aude.chambodut@unistra.fr",
// 											"organisation"=>	"Ecole et Observatoire de la Terre",
// 											"address" =>	array(
// 													"streetAddress"=>	array(
// 															"Service des observatoires magnétiques",
// 															"5, rue René Descartes"
// 													),
// 													"postalCode" =>	67000,
// 													"addressLocality" =>	"Strasbourg",
// 													"addressCountry"=>	"FRANCE"
// 											),
// 											"telephone" =>	"33 (0)3 68 85 01 25",
// 											"roles" =>	array( "pointOfContact"),
// 											"orcId" =>	"0000-0001-8793-1315"
// 										)
// 								),
// 								"api" => array(
// 										"url" => "https://api.poleterresolide.fr/cds/isgi/data/asigma",
// 										"name"=> "ISGI"
// 								)
// 						)
				)
		)
);

header("Content-Type: application/json");
echo json_encode($geojson, JSON_NUMERIC_CHECK);



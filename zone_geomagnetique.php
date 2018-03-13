<?php

// ZONE EQUATORIALE

$file1 = __DIR__."/data/latitude-geomagnetique_30N.json";
$file2 = __DIR__."/data/latitude-geomagnetique_30S.json";

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
$now = new DateTime();
$geojson = array(
		"type" => "Feature",
		"geometry" => array(
				"type" => "Polygon",
				"coordinates" => array($coordinates)
				),
		"properties"=> array(
				"name" => array( 
						"fr" => "Zone géomagnétique équatoriale",
						"en" => "Equatorial geomagnetic zone"
				),
			    "bbox" => array( 
			    		array( "south" => -43.55, "north" => 38.3, "east" => -180, "west"=>180),
			    ),
				"description" => array(
						"fr" => "Il s'agit de la zone comprise entre les latitudes magnétiques -30° et 30°. Année 2010",
						"en" => "geomagnetic latitude between -30° et 30°"
				),
				"identifiers" => array(),
				"observations" => array(
						//DST
						array(
								"id" => null,
								"metadataLastUpdate" => $now->format("YYYY-MM-DD"),
								"dataLastUpdate" => "",
								"title" => array( 
										"fr" => "Indice géomagnétique Dst",
										"en" => "Dst geomagnetic index"
								),
								"abstract"=> array(
										"fr" => "L'indice Dst mesure les perturbations géomagnétiques horizontales dans la zone équatoriale",
										"en" => "Equatorial index horizontal component disturbances"
								),
								"description" => array(
										"fr" => "L'indice Dst permet de surveiller la signature magnétique axisymétrique des courants de la magnétosphère, y compris principalement le courant de l'anneau, les courants de queue et aussi le courant de la magnétopause Chapman-Ferraro",
										"en" => "To monitor the axis-symmetric magnetic signature of magnetosphere currents, including mainly the ring current, the tail currents and also the magnetopause Chapman-Ferraro current."
								),
								"observedProperty" => array(
										"name" => "Disturbance storm-time",
										"shortName" => "DST INDEX",
										"type" => "time series",
										"timeResolution" => array( "hour"),
										"unit" => "nT"
								),
								"domainOfInterest" => array( "GEOMAGNETISM"),
								"keywords" => array(array(
										"codeSpace" => "GMD",
										"code" => "cdb4b514-75c4-4a1f-a4ad-1855fbd396ab")),
								"pole"  => "formater",
								"status"=> "public",
								"formaterDataCenter" => array(
										"code" => "TS-ANO-4/SNO2",
										"name" => "ISGI"
								),
								"formats"=> array( "IAGA2002"),
								"processingLevel" => "L4",
								"licence"=> array(
										"code" =>"NO"
								),
								"temporalExtents" => array(
										"start" => "1957-01-01",
										"end"   => "now"
								),
								"quicklooks" => array( 
										array( "url" => "https://api.poleterresolide.fr/images/isgi/reseau_Dst.jpg")
								),
								"links" => array(
										array(
												"type" => "INFORMATION_LINK",
												"url"  => "http://isgi.unistra.fr/indices_dst.php",
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
							    		"DOI" => "10.17593/14515-74000"
							    ),
								"distribution" => array(),
							    "contacts" =>array(
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
						//asigma
						array(
								"id" => null,
								"metadataLastUpdate" => $now->format("YYYY-MM-DD"),
								"dataLastUpdate" => "",
								"title" => array(
										"fr" => "Indices Asigma",
										"en" => "Asigma index"
								),
								"abstract"=> array(
										"fr" => "Fournir une caractérisation de l'activité géomagnétique locale dans 4 secteurs de longitudes et d'éventuelles divergences hémisphériques",
										"en" => "To provide a characterization of local geomagnetic activity in 4 longitudes sectors and possible hemispheric discrepancies "
								),
								"description" => array(
										"fr" => "Fournir une caractérisation de l'activité géomagnétique locale dans 4 secteurs de longitudes et d'éventuelles divergences hémisphériques",
										"en" => "To provide a characterization of local geomagnetic activity in 4 longitudes sectors and possible hemispheric discrepancies "
								),
								"observedProperty" => array(
										"name" => "asigma",
										"shortName" => "asigma",
										"type" => "time series",
										"timeResolution" => array( "hour"),
										"unit" => "nT"
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
								"formats"=> array( "IAGA2002"),
								"processingLevel" => "L4",
								"licence"=> array(
										"code" =>	"CC BY-NC 4.0",
										"url" =>	"https://creativecommons.org/licenses/by-nc/4.0/"
								),
								"temporalExtents" => array(
										"start" => "1959-01-01",
										"end"   => "now"
								),
								"quicklooks" => array(
										array( "url" => "https://api.poleterresolide.fr/images/isgi/reseau_am.jpg")
								),
								"links" => array(
										array(
												"type" => "INFORMATION_LINK",
												"url"  => "http://isgi.unistra.fr/indices_asigma.php",
												"description" => array(
														"fr" => "page de l'indice",
														"en" => "Index page"
												)),
										array(
												"type" => "HTTP_DOWNLOAD_LINK",
												"url" => "http://isgi.unistra.fr/oi_data_download.php"
										)
												
								),
								"identifiers" => array(
										//"DOI" => "10.17593/14515-74000"
								),
								"distribution" => array(),
								"contacts" =>array(
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
						)
				)
		)
);

header("Content-Type: application/json");
echo json_encode($geojson, JSON_NUMERIC_CHECK);



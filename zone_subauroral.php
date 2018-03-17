<?php

// ZONE SUBAURORALE NORD

$file1 = __DIR__."/data/latitude-geomagnetique_29N.json";
$file2 = __DIR__."/data/latitude-geomagnetique_58N.json";
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

// ZONE SUBAURORALE SUD
$file1 = __DIR__."/data/latitude-geomagnetique_29S.json";
$file2 = __DIR__."/data/latitude-geomagnetique_58S.json";
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

$polygon2 = $coordinates;
$now = new DateTime();
$content = file_get_contents(__DIR__."/data/indice_aa.json");
$indice_aa = json_decode( $content);
$content = file_get_contents(__DIR__."/data/indice_am.json");
$indice_am = json_decode( $content);
$content = file_get_contents(__DIR__."/data/indice_kp.json");
$indice_kp = json_decode( $content);
$geojson = array(
		"type" => "Feature",
		"geometry" => array(
				"type" => "MultiPolygon",
				"coordinates" => array( array($polygon1), array($polygon2))
				),
		"properties"=> array(
				"style"=> array(
						"fill" => "#98d7ff",
						"stroke" => "#1ab2ff",
						"stroke-width"=> "1",
						"fill-opacity" => "0.3"
				),
				"name" => array( 
						"fr" => "Zone géomagnétique subaurorale",
						"en" => "Subauroral geomagnetic zone"
				),
			    "bbox" => array( 
			    		array( "south" => 18.21, "north" => 63.58, "east" => -180, "west"=>180),
			    		array( "south" => -72.9, "north" => -18.13, "east" => -180, "west"=>180),
			    ),
				"description" => array(
						"fr" => "Il s'agit de la zone comprise entre les latitudes magnétiques -30° et 30°. Année 2010",
						"en" => "geomagnetic latitude between -30° et 30°"
				),
				"identifiers" => array(),
				"observations" => array(
						//aa
						$indice_aa,
				        $indice_am,
				        $indice_kp,
// 						array(
// 								"id" => null,
// 								"metadataLastUpdate" => $now->format("Y-m-d"),
// 								"dataLastUpdate" => "",
// 								"title" => array( 
// 										"fr" => "Indice géomagnétique aa",
// 										"en" => "aa geomagnetic index"
// 								),
// 								"abstract"=> array(
// 										"fr" => "L'indice aa mesure les perturbations géomagnétiques horizontales dans la zone équatoriale",
// 										"en" => "Aa index measure the equatorial index horizontal component disturbances"
// 								),
// 								"description" => array(
// 										"fr" => "L'indice aa permet de mesurer l'amplitude de l'activité géomagnétique globale pendant des intervalles de trois heures normalisés à une latitude géomagnétique de ± 50 °. aa a été introduit pour surveiller l'activité géomagnétique sur la période de temps la plus longue possible.",
// 										"en" => "The aa purpose is to measure the amplitude of global geomagnetic activity during 3-hour intervals normalized to geomagnetic latitude ±50°. aa was introduced to monitor geomagnetic activity over the longest possible time period."
// 								),
// 								"observedProperty" => array(
// 										"name" => "aa INDEX",
// 										"shortName" => "aa",
// 										"type" => "time series",
// 										"timeResolution" => array( "hour", "day"),
// 										"unit" => "nT"
// 								),
// 								"domainOfInterest" => array( "GEOMAGNETISM"),
// 								"keywords" => array(array(
// 										"codeSpace" => "GMD",
// 										"code" => "5fd5ccc2-5edb-4823-940d-03a290a5c5fc")),
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
// 										"start" => "1868-01-01",
// 										"end"   => "now"
// 								),
// 						        "procedure"=> array(
// 						                "method"=> array(
// 						                        "fr" => "aa est dérivé des indices K mesurés sur 2 observatoires antipodaux. Les indices K sont converties en amplitudes en utilisant des amplitudes de classe moyenne (K2aK) puis moyennées avec des facteurs de pondération qui tiennent compte de légères variations d'intensité des perturbations géomagnétiques entre aa-observatoires du Nord et du Sud successifs.",
// 						                        "en" => "aa is derived from the K indices measured at two antipodal observatories. The K indices are converted into amplitudes using mid-class amplitudes (K2aK) then averaged with weighting factors that account for slight changes in geomagnetic disturbance intensities between successive Northern and Southern aa-observatories."
// 						                )
// 						        ),
// 								"quicklook" => array( 
// 										array( "url" => "https://api.poleterresolide.fr/images/isgi/reseau_aa.jpg")
// 								),
// 								"links" => array(
// 										array(
// 												"type" => "INFORMATION_LINK",
// 												"url"  => "http://isgi.unistra.fr/indices_aa.php",
// 												"description" => array(
// 														"fr" => "page de l'indice",
// 														"en" => "Index page"
// 												)
// 										),
// 										array( 
// 												"type" => "HTTP_DOWNLOAD_LINK",
// 												"url" => "http://isgi.unistra.fr/data_download.php"
// 										)
									
// 								),
							   
// 								"distribution" => array(),
// 							    "contacts" =>array(
// 							    		array(
// 							    		"name" =>	"Aude Chambodut",
// 							    		"email"=>	"aude.chambodut@unistra.fr",
// 							    		"organisation"=>	"Ecole et Observatoire de la Terre",
// 							    		"address" =>	array(
// 							    				"streetAddress"=>	array(
// 							    						"Service des observatoires magnétiques",
// 							    						"5, rue René Descartes"
// 							    				),
// 							    				"postalCode" =>	67000,
// 							    				"addressLocality" =>	"Strasbourg",
// 							    				"addressCountry"=>	"FRANCE"
// 							    		),
// 							    		"telephone" =>	"33 (0)3 68 85 01 25",
// 							    		"roles" =>	array( "pointOfContact"),
// 							    		"orcId" =>	"0000-0001-8793-1315"
// 							    		)
// 							    ),
// 								"api" => array(
// 										"url" => "https://api.poleterresolide.fr/cds/isgi/data/aa",
// 										"name"=> "ISGI"
// 								)
										
							

								
// 						),
						//asigma
						array(
								"id" => null,
								"metadataLastUpdate" => $now->format("Y-m-d"),
								"dataLastUpdate" => "",
								"title" => array(
										"fr" => "Indices Asigma",
										"en" => "Asigma index"
								),
								"abstract"=> array(
										"fr" => "L'indice asigma fournit une caractérisation de l'activité géomagnétique locale dans 4 secteurs de longitudes et d'éventuelles divergences hémisphériques",
										"en" => "Asigma index provides a characterization of local geomagnetic activity in 4 longitudes sectors and possible hemispheric discrepancies "
								),
								"description" => array(
										"fr" => "L'indice asigma fournit une caractérisation de l'activité géomagnétique locale dans 4 secteurs de longitudes et d'éventuelles divergences hémisphériques",
										"en" => "Asigma index provides a characterization of local geomagnetic activity in 4 longitudes sectors and possible hemispheric discrepancies "
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
								"formats"=> array(  array("name" => "IAGA2002")),
								"processingLevel" => "L4",
								"licence"=> array(
										"code" =>	"CC BY-NC 4.0",
										"url" =>	"https://creativecommons.org/licenses/by-nc/4.0/"
								),
								"temporalExtents" => array(
										"start" => "1959-01-01",
										"end"   => "now"
								),
								"quicklook" => array(
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
										"customId" => "asigma"
										//"DOI" => "10.17593/14515-74000"
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
										"url" => "https://api.poleterresolide.fr/cds/isgi/data/asigma",
										"name"=> "ISGI"
								)
						)
				)
		)
);

header("Content-Type: application/json");
echo json_encode($geojson, JSON_NUMERIC_CHECK);



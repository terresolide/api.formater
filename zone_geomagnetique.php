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

$geojson = array(
		"type" => "Feature",
		"geometry" => array(
				"type" => "Polygon",
				"coordinates" => $coordinates
				),
		"properties"=> array(
				"name" => array( 
						"fr" => "Zone géomagnétique équatoriale",
						"en" => "Equatorial geomagnetic zone"
				),
				"description" => array(
						"fr" => "Il s'agit de la zone comprise entre les latitudes magnétiques -30° et 30°. Année 2010",
						"en" => "geomagnetic latitude between -30° et 30°"
				)
		)
);

header("Content-Type: application/json");
echo json_encode($geojson, JSON_NUMERIC_CHECK);



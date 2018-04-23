<?php
// $bbox = _northEast: Object { lat: 19.963193897, lng: -98.467355268 }
// ​
// _southWest: Object { lat: 18.568748337, lng: -99.529022784 }
 $now = new \DateTime();

$feature = array(
		"type" => "Feature",
		"geometry" => array(
				"type" => "Polygon",
				"coordinates" => [[[-99.529022784,18.568748337], [-99.529022784, 19.963193897], [-98.467355268 , 19.963193897], [-98.467355268 , 18.568748337], [-99.529022784,18.568748337]]]
		),
		"properties" => array(
			"style" => array(
				"fill" =>	"#e7bc53",
				"stroke"	=>"#e7bc53",
				"strokeWidth"	=>	1,
				"fillOpacity"	=>	0.4,
				"border"	=> "triangle"
			),
			"name" => array( 
					"fr" => "burst truc fauche x",
					"en" => "burst truc fauche x"
			),
			"description" => array(
					"fr" => "PEPS CNES machin chose",
					"en" => "machin chose"
			),
			"organisation" => array("CNES"),
			"bbox" => array(
					array( "south" => 18.568748337, "north" =>19.963193897, "east" =>-98.467355268, "west"=>-99.529022784 ),

			),
		    "temporalExtents" => array(
		    		"start" => "2015-07-18",
		    		"end"	=> "2017-08-30"
		    ),
			"observations" => array(
					array(
						"id" => null,
						"metadataLastUpdate" => $now->format("Y-m-d"),
						"dataLastUpdate" => "2018-03-20",
						"title" => array(
								"fr" => "Interfero Mexico NSBASS",
								"en" => "Interfero Mexico NSBASS"
						),
						"abstract" => array(
								"fr" => "resume",
								"en" => "resume"
						),
						"description" => array(
								"fr" => "kè ke sé",
								"en" => "kè ke sé"
						),
						"domainOfInterest" => array("geodesy"),
						"quicklook" => array(
								array( 
										"url" => "https://api.poleterresolide.fr/geotiff/geo_TOT_20160513.unw.png",
										"description" => ""
								)
						),
						"temporalExtents" => array(
								"start" => "2015-07-18",
								"end"	=> "2017-08-30"
						),
						"status" => "public",
						"pole" => "formater",
						"formats" => array( array( "name" => "geotiff")),
						"api" => array(
								"url" => "https://api.poleterresolide.fr/cds/grenoble/data",
								"name"=> "GRENOBLE"
						),
						"processingLevel" => "L4",
						"identifiers" => array(
								"customId" => "grenoble"
						)
					)
			)
		)
		
);

header("Content-Type: application/json");
echo json_encode( $feature);

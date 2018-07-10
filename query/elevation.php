<?php
/**
 * example request
 * http://api.formater/service/elevation?name=geotiff&lat=19.39268618643092&lng=-99.07470703125001&start=2015-07-18&end=2018-06-07
 */
session_start();
if(isset($_SESSION["token"])){
	$token = $_SESSION["token"];
}else{
	$token = uniqid();
	$_SESSION["token"] = $token;
}
include_once '../class/Elevation.php';

$profile = new \elevation\Request( $_SERVER['REQUEST_URI']);
$profile->execute($_GET);

if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
	header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	header('Access-Control-Allow-Credentials: true');
	
}
// header("Content-Type: application/json");

echo $profile->to_json();
/**
*  A partir d'une requete va chercher la valeur en un point du geotiff
*  $_GET['start'] date de début
*  $_GET['end'] date de fin
*  $_GET['directory'] nom du répertoire dans lequel se trouve les fichiers geotiff d'une serie
*   j'ai créé un json des fichiers concernées d'une serie avec leur bbox?
*   ici les noms des geotiffs du répertoire ont tous le même schéma geo_TOT_<date>.unw.tiff
*  $_GET['lat'] la latitude
*  $_GET['lng'] la longitude
*  On fera la même, en javascript pour comparer  
*  
*  */
//
// require_once '../api.formater/lib/elevations/SRTMGeoTIFFReader.php';

// $content = file_get_contents( "../geotiff/info.json");
// $result = json_decode( $content);
// // var_dump($result);

// /* test 
// * filename: geo_TOT_20170818.unw.tiff
// * position{
// *  "lat": 19.39268618643092,
// *  "lng": -99.07470703125001
// * }
// * avec javascript value = 30.672475814819336
// */

// $latlng = array(
// 		"lat" => 19.39268618643092,
// 	    "lng" => -99.07470703125001
// );

// // $reader = new SRTMGeoTIFFReader(__DIR__. "/geo_TOT_20170818.unw.tiff");
// echo "js value = 30.672475814819336<br />\r\n";
// // echo "php value = ". $reader->getElevation( $latlng["lat"], $latlng["lng"], false);
// $answer = array();
// foreach($result->result as $file){
// 	$filename = $file->tiffname;
// 	var_dump($filename);
// 	$cmd = 'gdallocationinfo -geoloc -valonly -b 1 '. '/var/www/api.formater/geotiff/'.$filename. ' '.$latlng['lng']. ' '.$latlng['lat'];
// // var_dump($cmd);
// 	$output = array();
// 	$return_var = null;

// // $cmd = escapeshellcmd($cmd);
// 	var_dump($cmd);
// 	exec($cmd, $output, $return_var);
// 	array_push($answer, array('date' => $file->date, 'value' => $output[0]));
// }
// var_dump($answer);

/** tests gdal
 * ------------
 * commande gdalinfo
 * commande gdallocationinfo (http://www.gdal.org/gdallocationinfo.html)
 * 
 * 
 * epointal@PCFORMATER:/var/www/geotiff.test$ gdallocationinfo geo_TOT_20170818.unw.tiff 900 1900
Report:
  Location: (900P,1900L)
  Band 1:
    Value: 5.6932544708252
  Band 2:
    Value: 0.71247136592865
epointal@PCFORMATER:/var/www/geotiff.test$ gdallocationinfo geo_TOT_20170818.unw.tiff 1200 500
Report:
  Location: (1200P,500L)
  Band 1:
    Value: -8.5189151763916
  Band 2:
    Value: 0.725419163703918

Avec geolocalisation
----------------------------------------------
epointal@PCFORMATER:/var/www/geotiff.test$ gdallocationinfo -wgs84 geo_TOT_20170818.unw.tiff -99.07470703125001 19.39268618643092
ERROR 6: No translation for an empty SRS to PROJ.4 format is known.

Mais avec -geoloc impeccable, même valeur qu'avec javascript
epointal@PCFORMATER:/var/www/geotiff.test$ gdallocationinfo -geoloc -valonly geo_TOT_20170818.unw.tiff -99.07470703125001 19.39268618643092 | tee result.txt
Report:
  Location: (817P,1026L)
  Band 1:
    Value: 30.6724758148193
  Band 2:
    Value: 0.753684520721436

*/

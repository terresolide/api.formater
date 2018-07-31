<?php
session_start();
/**
 * example request
 * http://api.formater/service/elevation?name=mexico&lat=19.39268618643092&lng=-99.07470703125001&start=2015-07-18&end=2018-06-07
 */

if(isset($_SESSION["token"])){
	$token = $_SESSION["token"];
}else{
	$token = uniqid();
	$_SESSION["token"] = $token;
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	header('Access-Control-Allow-Origin : '.$_SERVER['HTTP_ORIGIN']);
	header('Access-Control-Allow-Methods : GET, POST, OPTIONS');
	header('Access-Control-Allow-Credentials: true');
	// header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding");
	exit;
}
include_once '../class/Elevation.php';

$profile = new \elevation\Request( $_SERVER['REQUEST_URI']);
$profile->execute($_GET);

// if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
// 	header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
// 	header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// 	header('Access-Control-Allow-Credentials: true');
	
// }
// header("Content-Type: application/json");

echo $profile->output();


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

<?php
/**
 * REMPLACE L'INDEX DU TABLEAU DE FICHIERS PAR LE NOMBRE DE JOURS DEPUIS LA PREMIERE DATE
 */


$content = file_get_contents('geotiff/mexico/info.json');
$info = json_decode($content);
$result = array();
$first = current((Array)$info->result)->date;
$firstDate = new DateTime($first);
foreach($info->result as $key => $file){
	$date = new DateTime($file->date);
	$diff = $date->diff($firstDate);
	$result[$diff->days] = $file;
}
$info->result = $result;
header("Content-Type: application/json");
echo json_encode($info);
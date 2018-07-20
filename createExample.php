<?php 
/**
 * lit le répertoire example, prend tous les fichiers json et en fait un seul geojson
 */
$features = array(
		"type" => "FeatureCollection",
		"features" => array()
);
if ($handle = opendir('exemples')) {
    while (false !== ($file= readdir($handle))) {
        if ($file != "." && $file != ".." ) {
            //extrait le contenu du fichier
            $content = file_get_contents('exemples/' . $file);
            $feature = json_decode($content);
            array_push($features["features"], $feature);
        }
    }
    closedir($handle);
    header("Content-Type: application/json");
    echo json_encode($features);
}

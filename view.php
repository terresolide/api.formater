<?php
$content = file_get_contents( "data/geojson_catalog2.json");
header("Content-Type: application/json");
echo $content;
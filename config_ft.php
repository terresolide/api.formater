<?php
define('APP_URL', 'https://api.poleterresolide.fr');
define( 'APP_DIR', __DIR__);
define( 'TEST_DIR', realpath( APP_DIR.'/tests'));
define('TEMPLATE_DIR', realpath( APP_DIR.'/templates'));
define('LIB_DIR',  APP_DIR.'/lib');

define('DATA_FILE', APP_DIR.'/data/geojson_bcmt_ft_test.json');
define('DATA_FILE_BCMT', APP_DIR.'/data/geojson_bcmt_ft_test.json');
define('DATA_FILE_ISGI', APP_DIR.'/data/geojson_isgi_ft.json');

// test pour geotiff grenoble
define('DATA_FILE_GRENOBLE', APP_DIR.'/data/geojson_etalab_ft.json');
define('GEOTIFF_DIR', APP_DIR.'/geotiff');
define('GEOTIFF_URL', APP_URL.'/geotiff/');

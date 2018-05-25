<?php
define('APP_URL', 'http://api.formater');
define( 'APP_DIR', __DIR__);
define( 'TEST_DIR', realpath( APP_DIR.'/tests'));
define('TEMPLATE_DIR', realpath( APP_DIR.'/templates'));
define('DATA_DIR', APP_DIR. '/data');
define('LIB_DIR',  APP_DIR.'/lib');

define('DATA_FILE_BCMT', APP_DIR.'/data/geojson_bcmt_ft_test.json');
define('DATA_FILE_ISGI', APP_DIR.'/data/geojson_isgi_local.json');
define('DATA_FILE_GRENOBLE', APP_DIR.'/data/geojson_etalab_ft.json');
define('GEOTIFF_DIR', APP_DIR.'/geotiff');
define('GEOTIFF_URL', APP_URL.'/geotiff/');

/** SERVEUR BCMT**/
define('BCMT_FTP_SERVER' ,"ftp.bcmt.fr");
define('BCMT_FTP_USER', "bcmt_public");
define('BCMT_FTP_PWD', "bcmt");


/**Répertoire des fichiers des latitudes XX_CGM**/
define('DIR_LATITUDES_XX_CGM',  APP_DIR.'/latitudesGeomagnetiques');
/**Répertoire des  fichiers des latitudes geojson **/
define('DIR_LATITUDES', APP_DIR. '/data/latitudes');

/** adresse des fichiers de dev **/
define('DIR_ISGI_INDICES', APP_DIR.'/data/indices');
define('DIR_ISGI_ZONES', APP_DIR.'/data/isgi_zones');
define('DIR_ISGI_LATITUDES', APP_DIR.'/data/latitudes');

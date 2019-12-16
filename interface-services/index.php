<?php
/**
 * request to exterior services
 */
/**
 * filter $_GET */
// $_GET['url'] = 'https://services.data.shom.fr/INSPIRE/wms/r?service=WMS&request=GetCapabilities&version=1.3.0';

if (isset($_GET['url'])) {
	$url = urldecode($_GET['url']);
	$infos = pathinfo($url);
	$requests = [];
	preg_match('/request=([a-zA-Z]+)/', $infos['filename'], $requests);
	$request = '';
	if (!empty($requests)) {
		$request = '_' . $requests[1];
	}
	$filepath = __DIR__ .'/upload/'.preg_replace(['/https?\:\/\//', '/\//'], ['', '_'],$infos['dirname']). $request. '.xml';
}

header('Access-Control-Allow-Origin: *'); 
// search local file
if (file_exists($filepath) && filemtime($filepath) + 7200 > time() ) {
	$response_xml_data = file_get_contents($filepath);
} else if (($response_xml_data = file_get_contents($url)) === false){
	header("HTTP/1.0 404 Not Found");
	exit();
} else {
	file_put_contents($filepath, $response_xml_data);
} 
header('Content-Type: application/xml');
echo $response_xml_data;

// 	header('Content-Description: File Transfer');
// 	header('Content-Type: application/octet-stream');
// 	header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
// 	header('Access-Control-Expose-Headers: Content-Disposition');
// 	header('Expires: 0');
// 	header('Cache-Control: must-revalidate');
// 	header('Pragma: public');
// 	header('Content-Length: ' . filesize($filepath));
// 	ob_clean();
// 	flush();
// 	echo $response_xml_data;
	exit;


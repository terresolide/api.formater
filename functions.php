<?php

function valid_date( $str_date ){
    return preg_match("/^[0-9]{4}-(?:0[0-9]|1[0-2])-(?:0[0-9]|[1-2][0-9]|3[01])$/", $str_date);
}
function steppify($delta) {
    /**  */
    if($delta<=1){
        return 1;
    }
    $precision = round( log10( $delta ));
    
    $p = pow(10, $precision);
    
    $max = ceil($delta / $p) * $p;
    return $max / 2 > $delta ? $max / 2 : $max;
}
function get_response_header($header, $response_headers) {
    foreach ($response_headers as $key => $r) {
        // Match the header name up to ':', compare lower case
        if (stripos($r, $header . ':') === 0) {
            list($headername, $headervalue) = explode(":", $r, 2);
            return trim($headervalue);
        }
    }
    return false;
}

function is_authorized_server_origin(){
	global $_SERVER;
	global $authorized_servers;
	if( isset($_SERVER["HTTP_ORIGIN"] ) && in_array($_SERVER["HTTP_ORIGIN"], $authorized_servers)){
		return true;
	}else{
		return false;
	}
}

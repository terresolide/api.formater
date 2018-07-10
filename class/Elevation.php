<?php
/**
 * Treatment request for variation elevation in geotiff
 * @namespace elevation
 * @author epointal
 *
 */

namespace elevation;


include_once "../config.php";
include_once "../functions.php";




Class Request{
    private $request = null;
    private $searcher = null;
    private $response = array();
    
    public function __construct( $request){
        $this->request = $request;
        $this->parseRequest();
        $this->searcher = new ElevationSearcher( );
            
    }
    public function execute( $get){
        $this->response = $this->searcher->execute( $get );
        return $this->response;
    }
    public function get_response(){
        return $this->response;
    }
    public function to_json(){
        return $this->searcher->to_json();
    }
    public function output(){
    	return $this->searcher->output();
    }
    private function parseRequest(){
       $this->type = "elevation";
        
    }
}

/**
 * LA CLASSE SEARCHER A METTRE AILLEURS
 * @author epointal
 *
 */
Class  Searcher{

    public $start = null;
    public $end = null;
    public $error = null;
    public $result = array();
    protected $forbidden = false;
    protected $is_ajax = true;
    public function __construct( ){
        
    }
    public function execute( $get ){
        $this->extract_params( $get );
        if( !is_null($this->error )){
            $this->result =  array( "error" => $this->error );
            return array( "error" => $this->error );
        }
        $this->treatment();
    }
    
    protected function extract_params( $get = array() ){
        return $get;
    }
    protected function treatment(){
        $this->result = array("error" => "NOT_FOUND");
    }
    public function output(){
    	
    	$this->set_headers();
    	echo $this->to_json();
    }
    public function to_json(){
    	json_encode( $this->result, JSON_NUMERIC_CHECK);
    }
    protected function set_headers(){
    	global $_SERVER;
    	if( $this->forbidden ){
    		header('HTTP/1.0 403 Forbidden');
    		
    	}else{
    		if( $this->is_ajax ){
    			if( isset( $_SERVER['HTTP_ORIGIN']))
    				header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    				header('Access-Control-Allow-Credentials: true');
    				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    				Header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding");
    		}
    		
    	}
    	header("Content-Type: application/json");
    }
    protected function check_request_property( $get){
    	global $token;
    	global $_SERVER;
    	if( isset( $_SERVER['HTTP_ORIGIN'] )
    			|| ( isset( $_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH']== "XMLHttpRequest")){
    				$this->is_ajax = true;
    	}else{
    		$this->is_ajax = false;
    	}
    	
    	if( isset( $get["token"]) && ($token == $get["token"] || DEFAULT_TEST_TOKEN == $get["token"])){
    		$this->forbidden = false;
    	}else if( $this->is_ajax && ( is_authorized_server_origin() || !isset( $_SERVER['HTTP_ORIGIN']))){
    		$this->forbidden = false;
    	}else{
    		$this->forbidden = true;
    		$this->error = array("403" => "FORBIDDEN");
    	}
    	
    	
    }
}

Class  ElevationSearcher extends Searcher{

    public $start = null;
    public $end = null;
    public $error = null;
    public $result = array();
    private $name = null;
    private $files = array();
    private $bbox = null;
    private $lat = null;
    private $lng = null;

    
    public function __construct( $indice = null ){

    }
    public function execute( $get ){
        $this->extract_params( $get );
        if( $this->error ){
            $this->result =  array( "error" => $this->error );
            return array( "error" => $this->error );
        }
        $this->treatment();
    }
    
    public function to_json(){
        if( ! is_null( $this->error) ){
            return json_encode( array("error" => $this->error));
        }else{
            return json_encode( $this->result);
        }
    }
    /**
     *  $_GET['start'] date de début
	 *  $_GET['end'] date de fin
	 *  $_GET['name'] nom du répertoire dans lequel se trouve les fichiers geotiff d'une serie
	 *   j'ai créé un json appelé info.json qui contient les noms des fichiers concernées d'une serie et la bbox?
	 *   ici les noms des geotiffs du répertoire ont tous le même schéma geo_TOT_<date>.unw.tiff
	 *  $_GET['lat'] la latitude
	 *  $_GET['lng'] la longitude
     * @param array $get
     */
    protected function extract_params( $get = array() ){
        if(isset( $get["start"])){
            if( valid_date( $get["start"])){
                $this->start = $get["start"];
            }else{
                $this->error = "INVALID_DATE";
            }
        }
        if(isset( $get["end"])){
            if( valid_date( $get["end"])){
                $this->end = $get["end"];
                if( !is_null( $this->start) && $this->start > $this->end){
                    $this->error = "INCONSITENT_DATE";
                }
            }else{
                $this->error = "INVALID_DATE";
            }
        }
        if(!isset($get['lat']) || !isset($get['lng'])){
        	$this->error = 'INCOMPLETE_LOCATION';
        }else{
        	$this->lat = floatval($get['lat']); // (floatval($get['lat']) - 90) % 180 + 90;
        	$this->lng = floatval($get['lng']); // (floatval($get['lng']) - 180) %360 + 180;
        }
        if(!isset($get['name'])){
        	$this->error = 'MISSING_PROJECT';
        }else{
        	$this->name = filter_var($get['name'], FILTER_SANITIZE_STRING);
        }
    }
    protected function treatment(){
    	if ($this->load_info()) {
    		if ($this->is_inbbox()) {
    			$this->extract_elevation();
    		}else{
    			$this->error = 'OUTOF_BBOX';
    		}
    	}
    }
    private function is_inbbox () {
    	if(!is_null($this->bbox) || is_null($this->lat) || is_null($this->lng)){
    		if ($this->lat < $this->bbox->south || $this->lat > $this->bbox->north 
    			|| $this->lng < $this->bbox->west || $this->lng > $this->bbox->east) {
    			return false;		
    		}else{
    			return true;	
    		}
    	}else{
    		return false;
    	}
    }
    private function load_info(){
    	$infoname = GEOTIFF_DIR.'/'.$this->name.'/info.json';
    	if(file_exists($infoname)){
    		$content = file_get_contents($infoname);
    		$info = json_decode($content);
    		$this->bbox = $info->bbox;
    		$this->files = $info->result;
    		return true;
    	}else{
    		$this->error = 'UNKNOWN_PROJECT';
    		return false;
    	}
    }
    private function is_valid_date( $date){
    	
    	if( is_null( $this->start) || is_null( $this->end)){
    		$this->error = "NO_DATE";
    		return false;
    	}
    	if( $date <= $this->end && $date >= $this->start){
    		return true;
    	}else{
    		//$this->error = "NO_DATA_FOR_THIS_DATE";
    		return false;
    	}
    }
    private function extract_elevation(){
    	$answer = array();
    	foreach($this->files as $file){
    		$filename = $file->tiffname;
    		// check if the date is ok
    		if ($file->date <= $this->end && $file->date >= $this->start) {
	    		// use gdal
	    		// only request for raster 1 (-b 1) where is the information for mexico geotiff
	    		// it can be different for others series
	    		$cmd = 'gdallocationinfo -geoloc -valonly -b 1 '. GEOTIFF_DIR. '/'.$this->name.'/'.$filename. ' '.$this->lng. ' '.$this->lat;
	    		$output = array();
	    		$return_var = null;
	    		exec($cmd, $output, $return_var);
	    		
	    		// stop foreach if shell failed
	    		$failed = ($return_var > 0);
	    		if ($failed) {
	    			$this->error = 'SHELL_ERROR';
	    			break;
	    		}
	    		array_push($answer, array('date' => $file->date, 'value' => floatval($output[0])));
    		}
    	}
       $this->result = $answer;
    }
}


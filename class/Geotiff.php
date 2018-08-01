<?php
/**
 * Treatment request for Grenoble data center
 * @namespace geotiff
 * @author epointal
 *
 */

namespace geotiff;

/** use treatment file IAGA*/


include_once "../config.php";
include_once "../config_unistra.php";
include_once "../functions.php";




Class Request{
    private $request = null;
    private $searcher = null;
    private $response = array();
    private $type = null;
    
    public function __construct( $request){
        $this->request = $request;
        $this->parseRequest();

        switch( $this->type){
          
            case "data":
                $this->searcher = new DataSearcher(  );
                break;
            default:
                $this->searcher = new Searcher();
        }
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
    	$this->type = "data";
    	
    }
}

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
		json_encode( $this->result);
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
					// Header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding");
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

Class  DataSearcher extends Searcher{

    public $start = null;
    public $end = null;
    public $error = null;
    public $result = array();
    private $files = array();
    private $pattern = "/^geo_TOT_([0-9]{4})([0-9]{2})([0-9]{2}).unw.(png|tiff)$/";
    private $bbox = array( "south" => 18.568748337, "north" =>19.963193897, "east" =>-98.467355268, "west"=>-99.529022784 );

    
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
    }
    protected function treatment(){
        $this->search_files();
        
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
    private function search_files(){
        //search files in directory
        $end = false;
        $result = array();
    	if ( $handle = opendir(MEXICO_DIR) ) {
    		$matches = array();
    		/* Ceci est la façon correcte de traverser un dossier. */
    		// start date 
    	    $start = new \DateTime($this->start);
    		while (false !== ($entry = readdir($handle)) ) {
    			if( preg_match( $this->pattern, $entry, $matches)){
    				//var_dump($matches);
    			
    				$date = $matches[1]."-".$matches[2]."-".$matches[3];
    				
    				if( $this->is_valid_date( $date)){
	    				$key = str_replace( "-", "", $date);
	    				// number of days since start day
	    				$current = new \DateTime($date);
	    				$diff = $start->diff($current);
	    				$key = $diff->days;
	    				$extension = $matches[4];
	    				
	    				if( !isset( $result[$key])){
	    					$result[$key] = array(
	    							"date" => $date,
	    					);
	    				}
	    				$result[$key][ $extension] = MEXICO_URL.$entry;
    		  		}
    			}
    		}
    		
    		
    		closedir($handle);
    	}
    	ksort( $result);
    	// decale tous les indexes de celui du début
    	reset($result);
    	$first_key = key($result);
    	$keys = array_keys($result);
    	$return_result = array();
    	foreach( $keys as $value){
    		$return_result[$value - $first_key] = $result[$value];
    	}
    
    	
    	if( count( $result)>0)
    	$this->result = array( 
    			"bbox" => $this->bbox,
    			"result" => $return_result
    	);
       
    }
}


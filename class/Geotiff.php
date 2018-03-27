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
    private function parseRequest(){
       $this->type = "data";
        
    }
}

Class  Searcher{

    public $start = null;
    public $end = null;
    public $error = null;
    public $result = array();
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
    
    public function to_json(){
        return json_encode( $this->result );
    }
    protected function extract_params( $get = array() ){
        return $get;
    }
    protected function treatment(){
        $this->result = array("error" => "NOT_FOUND");
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
    	if ( $handle = opendir(GEOTIFF_DIR) ) {
    		$matches = array();
    		/* Ceci est la façon correcte de traverser un dossier. */
    		while (false !== ($entry = readdir($handle)) ) {
    			if( preg_match( $this->pattern, $entry, $matches)){
    				//var_dump($matches);
    			
    				$date = $matches[1]."-".$matches[2]."-".$matches[3];
    				
    				if( $this->is_valid_date( $date)){
	    				$key = str_replace( "-", "", $date);
	    				$extension = $matches[4];
	    				
	    				if( !isset( $result[$key])){
	    					$result[$key] = array(
	    							"date" => $date,
	    					);
	    				}
	    				$result[ $key][ $extension] = GEOTIFF_URL.$entry;
    		  		}
    			}
    		}
    		
    		
    		closedir($handle);
    	}
    	ksort( $result);
    	if( count( $result)>0)
    	$this->result = array( 
    			"bbox" => $this->bbox,
    			"result" => $result
    	);
       
    }
}


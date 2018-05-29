<?php
/**
 * Treatment request for ISGI data center
 * @namespace isgi
 * @author epointal
 *
 */

namespace isgi;

/** use treatment file IAGA*/
include_once "../config.php";
include_once "../config_unistra.php";
include_once "Iaga.php";

include_once "../functions.php";

function get_http_content_type( $response_headers){
    $type = get_response_header( "Content-Type", $response_headers);

    if( preg_match("/^.*text\/plain.*$/", $type)){
        return "text";
    }
    if( preg_match("/^.*application\/octet-stream.*$/", $type) ){
        return "zip";
    }
}

function get_zip_filename( $response_headers){
    $disposition = get_response_header("Content-Disposition", $response_headers);
    if( preg_match("/^.*filename=(\w+).zip$/", $disposition, $matches)){
        return $matches[1];
    }else{
        return "wsigi_data_" . time();
    }
}
Class Config{
    
    
    public static $upload_dir = null;
    public static $pattern_indices = "/^\/cds\/isgi\/indices\/?(.*)$/";
    public static $pattern_indices_indice = "/^([a-zA-Z]{2,6})\/?(.?)$/";
    public static $pattern_data = "/^\/cds\/isgi\/(data|archive)\/([a-zA-Z]{2,6})\/?(.*)$/";
    public static $indices = array( "aa", "am", "Kp", "Dst", "PC", "AE", "SC", "SFE", "Qdays", "CKdays", "asigma");
    public static function is_code_indice( $indice ){
        if( in_array( $indice, self::$indices)){
            return true;
        }else{
            return false;
        }
    }
    public static function initialize(){
        self::$upload_dir = realpath( "../temp");
    }
}

Config::initialize();

Class Request{
    private $request = null;
    private $searcher = null;
    private $response = array();
    private $indice = null;
    private $type = null;
    
    public function __construct( $request){
        $this->request = $request;
        $this->parse_request( );
  
        switch( $this->type){
            case "indices":
                $this->searcher = new IndicesSearcher( $this->indice);
                break;
            case "data":
                $this->searcher = new DataSearcher( $this->indice );
                break;
            case "archive":
            	$this->searcher = new ArchiveSearcher( $this->indice);
            	break;
            default:
                $this->searcher = new Searcher();
        }
    }
    public function execute( $get){
        $this->response = $this->searcher->execute( $get );
        return $this->response;
    }
    public function set_headers(){
    	if( !is_null( $this->searcher)){
    		$this->searcher->set_headers();
    	}
    }
    public function get_response(){
        return $this->response;
    }
    public function is_archive(){
    	if( $this->type == "archive"){
    		return true;
    	}else{
    		return false;
    	}
    }
    public function get_result(){
    	return $this->searcher->result;
    
    }
    public function to_json(){
        return $this->searcher->to_json();
    }
    public function output(){
    	return $this->searcher->output();
    }
    private function parse_request(){
        //suppr cds/bcmt
        if( preg_match( Config::$pattern_indices, $this->request, $matches)){
            //search observatories
            $this->type = "indices";
            $this->request = $matches[1];
            if( preg_match( Config::$pattern_indices_indice, $this->request, $matches)){
                if( Config::is_code_indice($matches[1])){       
                    $this->indice =  $matches[1];
                }else{
                    $this->type = "404";
                }
            }
        }else if( preg_match( Config::$pattern_data , $this->request, $matches)){
            //search geomagnetic data for one observatory
            if( Config::is_code_indice( $matches[2])){
                $this->indice =  $matches[2];
                $this->request = $matches[3];
                $this->type = $matches[1];
            }else{
                $this->type ="404";
           
            }
        }
    }
    
}

Class  Searcher{
    public $indice = null;
    public $start = null;
    public $end = null;
    public $error = null;
    public $result = array();
    protected $forbidden = false;
    protected $is_ajax = true;
    
    public function __construct( $indice = null ){
        $this->indice = $indice;
    }
    public function execute( $get=array()){
        $this->extract_params( $get );
        $this->check_request_property( $get);
        if( !is_null($this->error )){
            $this->result =  array( "error" => $this->error );
            return array( "error" => $this->error );
        }
        $this->treatment();
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
   
    protected function extract_params( $get = array() ){
        return $get;
    }
    protected function treatment(){
        $this->result = array("error" => "NOT_FOUND");
    }
}

Class ArchiveSearcher extends Searcher{
	public function execute( $get=array()){
		$this->extract_params( $get);
		$this->result = array(
				"archive" => true,
				"url" => $this->build_query()
				
		);
		
	}
	public function output(){
		global $token;
		$this->set_headers();
		if( isset( $this->error)){
			echo $this->error. " token = " . $token;
		}else{
			echo readfile($this->result["url"]);
		}
	}

	protected function set_headers(){

		if( ! isset( $this->error)){
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary");
			header('Content-disposition: attachment; filename="'.$this->build_name().'"');
		}else{
			header('Content-Type: text/plain');
			header('Content-disposition: attachment; filename="'.$this->error.'.txt"');
		}
		
	}
	
	protected function extract_params( $get = array() ){
		global $token;
		
		if( ! isset( $get["token"]) ){
			$this->error = "MISSING_TOKEN";
			$this->forbidden = true;
		}else{
			if( $get["token"] != $token && $get["token"] != DEFAULT_TEST_TOKEN  ){
				$this->forbidden = true;
				$this->error = "INVALID_TOKEN";
			}
		}
		if(isset( $get["StartTime"])){
			if( valid_date( $get["StartTime"])){
				$this->start = $get["StartTime"];
			}else{
				$this->error = "INVALID_DATE";
			}
		}
		if(isset( $get["EndTime"])){
			if( valid_date( $get["EndTime"])){
				$this->end = $get["EndTime"];
				if( !is_null( $this->start) && $this->start > $this->end){
					$this->error = "INCONSITENT_DATE";
				}
			}else{
				$this->error = "INVALID_DATE";
			}
		}
		
	}
	protected function build_query(){
		
		$data = array(
				"user" => ISGI_USER,
				"index"=> $this->indice,
				"StartTime" => $this->start,
				"EndTime" 	=> $this->end
		);
		$query = ISGI_API_URL. "?" . http_build_query($data);
		return $query;
	}
	protected function build_name(){
		$name = "wisgi_". $this->indice."_";
		$name .= str_replace( "-", "", $this->start)."_".str_replace("-", "", $this->end);
		$name .= ".zip";
		return $name;
	}
	
}
Class IndicesSearcher extends Searcher{
	/** @todo passer au parent **/
	private $request_origin = null;
	public function set_request_origin( $server){
		$this->request_origin = $server;
	}
	protected function check_request_property($get){
		
		if( $this->request_origin == "local"){
			// it's not really request, come from another request archive or data
			$this->forbidden = false;
		}else{
			parent::check_request_property($get);
		}
	}
	/** end passer au parent **/
	protected function load_features(){
		$content = file_get_contents( DATA_FILE_ISGI);
		
		return json_decode($content);
	}

    protected function treatment(){
    	
    	$obj = $this->load_features();
       
    	$find = false;
    	$observation = null;
    	$i =0;
    	while( !$find && $i< count($obj->features)){
    		
    		$observations = $obj->features[$i]->properties->observations;
    		$j=0;
    		while( !$find && $j < count( $observations)){
    			if( $this->indice == $observations[ $j]->identifiers->customId){
    				$find = true;
    				$observation = $observations[$j];
    			}
    			$j++;
    		}
    		$i++;
    	}
    	
        $this->result = $observation;
       
        return $observation;
    }
}
Class  DataSearcher extends Searcher{
    public $indice = null;
    public $start = null;
    public $end = null;
    public $error = null;
    public $result = array();
    private $files = array();
    private $isgi_url = null;
    private $root = null;
    private $iaga = null;
    private $indiceSearcher = null;
    private $metadata = null;
    
    public function __construct( $indice = null ){
        $this->indice = $indice;
    }
//     public function execute( $get=array() ){
//         $this->extract_params( $get );
//         $this->check_request_property( $get);
//         if( $this->error ){
//             $this->result =  array( "error" => $this->error );
//             return array( "error" => $this->error );
//         }
//         $this->treatment();
//     }
    
    public function to_json(){
        if( ! is_null( $this->error) ){
            return json_encode( array("error" => $this->error));
        }else{
            return $this->iaga->json();
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
  
    protected function dates_filter(){
    	
    	//Filter dates start and end, with temporalExtents and dataLastUpdate
    	//+Constraint Isgi: only one year data
    	$temporal = $this->get_temporal();
    	if( strtolower($temporal->end) == "now"){
    		$now = new \DateTime();
    		$temporal->end = $now->format("Y-m-d");
    	}
    	
    	
    	//change start and end
    	if( $this->start < $temporal->start){
    		$this->start = $temporal->start;
    	}
    	if( $this->end > $temporal->end){
    		$this->end = $temporal->end;
    	}
    	$update = $this->get_update();
    	if( !empty( $update) && $update < $this->end){
    		$this->end = $update;
    	}
    	// diff between start and end
    	$start = new \DateTime( $this->start);
    	$end = new \DateTime( $this->end);
    	$interval = $start->diff( $end);
    	if( $interval->invert){
    		//end < start
    		$this->error = "NO_DATA";
    	}else{
	    	if( $interval->days > 365){
	    		$start = clone $end;
	    		$start->sub( new \DateInterval("P364D"));
	    		$this->start = $start->format("Y-m-d");
	    	}
    	}
    }
    protected function treatment(){
    	if( $this->forbidden){
    		$this->error = array("403" => "FORBIDDEN");
    		return;
    	}
    	$this->dates_filter();
    	
    	if( $this->error){
    		$this->result = array("error" => $this->error);
    		return;
    	}
        $this->search_files();
       
        if( is_null( $this->error)){
            	
            $this->iaga = new \Iaga( $this->files, "", $this->start ,$this->end, $this->indice);
            $this->iaga->add_meta("isgi_url", $this->false_isgi_url);
            $this->iaga->add_meta("filesize", $this->filesize);
            if( $this->indice == "Qdays" || $this->indice == "CKdays"){
                // look if have the lastest month values
                $code = substr( $this->end, 0, 7);
                if( preg_match('/^.*'.$this->indice.'_([0-9\-]{7})?_?([0-9\-]{7})_D.dat$/', $this->files[0], $matches)){
                    if( $matches[2] != $code){
                        $date = explode("-", $matches[2]);
                        $year = intVal($date[0]);
                        $month = intVal($date[1]);
                        if( $month == 12){
                            $year++;
                            $month = 1;
                        }else{
                            $month++;
                        }
                        //if have not the last month value, add the first day of this month in meta no_data
                        $this->iaga->add_meta("no_data", $year."-".str_pad($month, 2, '0', STR_PAD_LEFT)."-01");
                    }
                }    
            }
            if( $this->indice == "SC" || $this->indice == "SFE"){
            	// look if have the lastest year values
            	$year = substr( $this->end, 0, 4);
            	
            	if( is_null($this->iaga->get_meta("no_data")) && preg_match('/^.*'.$this->indice.'_([0-9]{4})_(?:D|P).dat$/', $this->files[ count($this->files)-1], $matches)){
            		if( $matches[1] != $year){
            			
            			$this->iaga->add_meta("no_data", $year."-01-01");
            		}
            	}    
            }
            $this->result = $this->iaga->to_array();

            $this->clean_temporary_file();
        }else{
            $this->result = array("error" => $this->error);
        }
        
    }
    public function get_metadata(){
    	if( is_null( $this->indiceSearcher) && is_null( $this->metadata)){
    		$this->indiceSearcher = new IndicesSearcher($this->indice);
    		// the indiceSearcher is called from another searcher (here DataSearcher)
    		// and not from the original request
    		$this->indiceSearcher->set_request_origin( "local");
    		$this->indiceSearcher->execute();
    		$this->metadata = $this->indiceSearcher->result;
   	    }
   	    return $this->metadata;
    }
    private function get_temporal(){
    	$metadata = $this->get_metadata();
    	return $metadata->temporalExtents;
    
    }
    private function get_update(){
    	$metadata = $this->get_metadata();
    	return $metadata->dataLastUpdate;
    }
    private function extract_error( $content){
        
        $lines = preg_split("(\r\n|\n|\r)", $content);

        if(isset( $lines[2]) && !empty( $lines[2])){
            $this->error = $lines[2];
        }else{
            $this->error = $lines[0];
        }
        return $this->error;
    }
    private function clean_temporary_file(){
        if(count($this->files) > 0 ){
            foreach( $this->files as $file){
                unlink( $file);
            }
        }
        $this->files = array();
        //remove directory
        rmdir( Config::$upload_dir ."/" .$this->root );
        //remove zip file
        unlink(Config::$upload_dir ."/" .$this->root .".zip");
        $this->root = null;
    }
    private function prepare_get( $direct= true){
    	global $token;
    	
    	if( $direct){
	        $data = array(
	            "user" => ISGI_USER,
	            "index" => $this->indice
	        );
	    }
        if(!is_null( $this->start)){
            $data["StartTime"] = $this->start;
        }
        if(!is_null( $this->end)){
            $data["EndTime"] = $this->end;
        }
        if(! $direct){
        	$data["token"]= $token;
        }
        return http_build_query($data);
    }
    private function no_data(){
        
    }
    private function extract_files(){
        //extract file
        if( !is_null($this->root.".zip")){
            $zip = new \ZipArchive();
            $root = ( Config::$upload_dir ."/" .$this->root );
            if ($zip->open( $root.".zip") === TRUE) {
                  $zip->extractTo( $root );
                  for($i = 0; $i < $zip->numFiles; $i++) {
                      $filename = $zip->getNameIndex($i);
                      array_push( $this->files, $root. "/" . $filename);
                     
                     // $fileinfo = pathinfo($filename);
                     // copy("zip://".$path."#".$filename, "/your/new/destination/".$fileinfo['basename']);
                  } 
                
                  $zip->close();
                  return true;
            } else {
                $this->error = "FAILED UNZIP";
                return false;
            }
        }
        $this->error = "FAILED UPLOAD DATA";
        return false;
    }

    private function search_files(){
        $this->isgi_url = ISGI_API_URL."?" . $this->prepare_get( true);
        $this->false_isgi_url = APP_URL."/cds/isgi/archive/".$this->indice."?". $this->prepare_get( false);
        $ctx = stream_context_create(array('http'=>
        		array(
        				'timeout' => 30,  //1/2 minute
        		)
        ));
       
        	
        $content = @file_get_contents($this->isgi_url, false, $ctx);
        if( $content === false){
        	$this->error ="SERVER_ISGI_HS";
        	return;
        }
        
        // the decoded content of your zip file

       // $content_type = get_response_header("Content-type", $http_response_header);
        //var_dump($content_type);
        switch( get_http_content_type( $http_response_header)){
        	
            case "text":
                $this->error = $this->extract_error($content);
                break;
            case "zip":
            	
            	
                $filename = get_zip_filename( $http_response_header);
                if(!file_exists( Config::$upload_dir ."/" . $filename . ".zip")){
                    $this->root = $filename;
                }else{
                    $this->root = "isgi_".microtime();
                }
                $this->root = $filename;
                file_put_contents( Config::$upload_dir ."/" . $this->root . ".zip" , $content);
                $this->filesize = filesize(  Config::$upload_dir ."/" . $this->root . ".zip" );
                
                $this->extract_files();
                break;
            default:
                $this->error = "FAILED";
        }
       
    }
}


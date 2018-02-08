<?php
/**
 * Treatment request for BCMT data center
 * @namespace bcmt
 * @author epointal
 *
 */

namespace bcmt;

/** use treatment file IAGA*/
include_once "../functions.php";
include_once "Iaga.php";

Class Config{
  const FTP_SERVER = "ftp.bcmt.fr";
  const FTP_USER = "bcmt_public";
  const FTP_PWD = "bcmt";
  public static $conn_id = false;
  public static $default_days = 3; // last 3 days
  public static $pattern_root = "/^\/cds\/bcmt\/?$/"; // for only uri /cds/bcmt
  // examples /cds/bcmt/obs/aae or cds/bcmt/obs?start=2011-01-01
  public static $pattern_obs = "/^\/cds\/bcmt\/obs\/?(.*)$/";
  // examples aae with capture of code observatory
  public static $pattern_obs_ob = "/^([a-zA-Z]{3})\/?(.*)$/"; 
  //examples /cds/bcmt/data/aae or /cds/bcmt/data/aae?start=2011-01-01&end=2011-01-10
  // the observatory code is captured
  public static $pattern_data = "/^\/cds\/bcmt\/data\/([a-zA-Z]{3})\/?(.*)$/"; 
  public static $code_obs = array( "aae", "ams", "bng", "box", "clf", "czt", "dlt", "dmc", "drv", "ipm", "kou", "lzh", "mbo", "paf", "phu", "ppt", "psm", "tan", "tam", "vlj");
  
  public static function get_connexion(){
      if(! self::$conn_id ){
          self::$conn_id = ftp_connect(self::FTP_SERVER);
          // or die('{ "error": "NO_FTP_CONNEXION"}');
          if( ! self::$conn_id || ! @ftp_login( self::$conn_id, self::FTP_USER, self::FTP_PWD) ){
              
              return false;
          }
          ftp_pasv(self::$conn_id, true);
      }
      return self::$conn_id;
  }
  public static function close_connexion(){
      if(self::$conn_id){
          ftp_close( self::$conn_id);
      }
  }
   public static function is_code_obs( $code ){
      if( in_array( strtolower( $code ), self::$code_obs)){
          return true;
      }else{
          return false;
      }
  }
}


  /**
   * Treatment Request URI to BCMT
   *  cds/bcmt/obs ...  search observatories
   *  cds/bcmt/data/[observatory] search data from one observatory
   *  @property string $request the request uri
   *  @property [Searcher] $searcher instance which treate request parameters ($_GET) and find result
   *  @property array $response 
   *  @property string $ob code of observatory extract from URI
   *
   */
    Class Request{
        private $request = null;
        private $searcher = null;
        private $response = array();
        private $ob = null;
        private $type = null;
        
        public function __construct( $request){
            $this->request = $request;
            $this->parseRequest();
            switch( $this->type){
                case "observatories":
                    $this->searcher = new ObservatoriesSearcher( $this->ob);
                    break;
                case "data":
                    $this->searcher = new DataSearcher( $this->ob );
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
            //suppr cds/bcmt
            if( preg_match( Config::$pattern_root, $this->request)){
                //root
                $this->type = "root";
                $this->request ="";
            }else if( preg_match( Config::$pattern_obs, $this->request, $matches)){
                //search observatories
                $this->type = "observatories";
                $this->request = $matches[1];
                if( preg_match( Config::$pattern_obs_ob, $this->request, $matches)){
                    if(Config::is_code_obs($matches[1])){
                        $this->ob = strtolower( $matches[1]);
                    }else{
                        $this->type = "404";
                    }
                }
            }else if( preg_match( Config::$pattern_data , $this->request, $matches)){
                //search geomagnetic data for one observatory
                if( Config::is_code_obs( $matches[1])){
                    $this->ob = strtolower( $matches[1]);
                    $this->request = $matches[2];
                    $this->type = "data";
                }else{
                    $this->type ="404";
                    return false;
                }
                
            }else{
                // not found
                $this->type = "404";
                return false;
            }
            //extract param from request
            //$this->extractParam();
        }
    }
Class  Searcher{
    public $observatory = null;
    public $start = null;
    public $end = null;
    public $error = null;
    public $result = array();
    public function __construct( $obs = null ){
        $this->observatory = $obs;
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
Class ObservatoriesSearcher extends Searcher{
    public $observatory = null;
    public $start = null;
    public $end = null;
    public $north = null;//lat max
    public $south = null;//lat min
    public $east = null; //lng max
    public $west = null; //lng min
    //correstion for bbox
    private  $add = 360;
  
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
        }else{
            $end = new \DateTime();
            $this->end =$end->format("Y-m-d");
        }
        if( isset($get["bbox"])){
            $this->parse_bbox( $get["bbox"]);
        }
    }
    protected function treatment(){
        $observatories = $this->load_file();
       $result = array();
        foreach( $observatories as $obs){
            $obs = $this->is_selected($obs);
            if( $obs){
                array_push($result, $obs);
            }
        }
        if( count($result)>1){
           
            $this->result = array("type" => "FeatureCollection", "features" => $result);
        }else if( count( $result) == 1){
            $this->result = $result[0];
        }else{
            $this->error = "NO_OBSERVATORY";
        }
        
        return $this->result;
    }
    private  function is_selected( $obs){
        if( !is_null( $this->observatory) ){
            if($this->observatory == strtolower( $obs->properties->code)){
                return $obs;
            }else{
                return false;
            }
        }else{
            $startTime = $obs->properties->temporalExtents->start;
            if( $obs->properties->temporalExtents->end == "now"){
                $end = new \DateTime();
                $endTime = $end->format("Y-m-d");
            }else{
                $endTime = $obs->properties->temporalExtents->end;
            }
            if( (is_null( $this->start) || $this->start <= $endTime) 
                && (is_null( $this->end ) || $this->end >= $startTime)){
                    $obs = $this->in_bbox( $obs);
                    return $obs;
            }else{
                return false;
            }
           
            
        }
        
      
    }
    /**
     * Check if $obs is in the bounds bbox
     * correct values lng if necesserary (modulo 360)
     * @param GeojsonFeature $obs
     * @return GeojsonFeature|boolean
     */
    private function in_bbox( $obs){
        if( is_null( $this->south)){
            return $obs;
        }
        $lat = $obs->geometry->coordinates[1];
        $lng = $obs->geometry->coordinates[0];
        if( $lat >= $this->south && $lat <= $this->north ){
            if( $lng >= $this->west && $lng <= $this->east){
                
                return $obs;
                
            }else if( $this->add>0 && $lng + $this->add >= $this->west && $lng + $this->add <= $this->east){
                $obs->geometry->coordinates[0] = $lng + $this->add;
                return $obs;
            }
        }else{
            return false;
        }
    }
    private function parse_bbox( $str_bbox ){
        $values = explode(",", $str_bbox);
        
        if(count($values) == 4){
            $values = array_map("floatVal", $values);
            $keys = array("west", "south", "east", "north");

            $result = array_combine( $keys, $values);
  
            if($result["north"] >90 || $result["south"] < -90 || $result["north"] < $result["south"]){
                $this->error = "INVALID_BBOX";
                return;
            }
            foreach ( $result as $key => $value){
                $this->{ $key } = $value;
            }
            if( $this->west > $this->east){
                $this->east = $this->east + 360;
                $this->add = 360;
                
            }
        }else{
            $this->error = "INVALID_BBOX";
        }
        
    }
    private function load_file(){
        $content = file_get_contents( "../data/geojson_observatories.json");
        $result = json_decode( $content);
        return $result->features;
    }
}

Class DataSearcher extends Searcher{
    
  
    public $observatory = null;
    public $start;
    public $end;
    public $dataType = null;
    public $files = array();
    private $diff = null;
    private $iaga = array();
    
 
    public function __destruct(){
        //close connexion if exists
        Config::close_connexion();
    }
    public function execute( $get ){
        $this->extract_param($get);
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
           return $this->iaga->json();
       }
   }
   /**
    * Extract , filter, $start and $end properties and compute linked property type
    * @param array $get from $_GET
    * @return boolean
    */
   protected function extract_param( $get ){
   
       if( is_null($get) || (!isset( $get["start"]) && !isset($get["end"]))){
           $this->start = new \DateTime(" -". Config::$default_days . " day");
           $this->end = new \DateTime();
           
       }else if( isset($get["start"])){
           if( valid_date( $get["start"])){
               $this->start = new \DateTime( $get["start"]);
           }else{
               $this->error = "INVALID_DATE_START";
               return false;
           }
           
           if(isset( $get["end"])){
               if(valid_date( $get["end"])){
                   $this->end = new \DateTime( $get["end"]);
               }else{
                   $this->error = "INVALID_DATE_END";
                   return false;
               }
           }else{
               $this->end = new \DateTime();
           }
       }
       $this->diff = $this->start->diff( $this->end);
       if( $this->diff->invert === 1){
           $this->error = "INCONSISTENT_DATE";
           return false;
       }
       if( isset( $get["type"]) && in_array( $get["type"], ["VARIATION", "DEFINITIVE", "QUASI_DEFINITIVE"])){
           $this->dataType = $get["type"];
           
       }
       $this->compute_type();
       return true;
       
   }

   protected function treatment(){
       if(  is_null( $this->dataType ) ){
           $ismin = false;
           $directory0 = "/DEFINITIVE/".$this->observatory."/".$this->type;
           $directory1 = "/QUASI_DEFINITIVE/".$this->observatory."/".$this->type;
           $this->files = $this->search_files( $directory0,"d");
           
           if(empty( $this->files)){
               $this->search_files( $directory1,"q");
           }
           if( empty( $this->files)){
               $this->search_files_variation();
               $ismin = $this->diff->days + 1;
           }
      
       }else{
           switch( $this->dataType){
               case "DEFINITIVE":
               case "QUASI_DEFINITIVE":
                   $ismin = false;
                   $directory0 = "/".$this->dataType."/".$this->observatory."/".$this->type;
                   $this->search_files( $directory0, $this->shortname());
                   break;
               case "VARIATION":
                   $this->search_files_variation();
                   $ismin = $this->diff->days + 1;
                   break;
           }
       }
       $ftp = "ftp://" . Config::FTP_USER .":" . Config::FTP_PWD . "@" . Config::FTP_SERVER;
       $this->iaga = new \Iaga( $this->files, $this->observatory, $this->start->format("Y-m-d"),$this->end->format("Y-m-d"), null, $ftp, $ismin);
       
       $this->result = $this->iaga->to_array();
   }
   private function shortname(){
       switch( $this->dataType){
           case "DEFINITIVE":
               return "d";
               break;
           case "QUASI_DEFINITIVE":
               return "q";
               break;
           default:
               return "v";
               break;
       }
   }
    private function compute_type(){
        if( $this->dataType == "VARIATION"){
            $this->type = "min";
        }else{
            if($this->diff->y > 15){
                $this->type = "yea";
                
            }else if( $this->diff->y >= 1){
                //search month data
                $this->type ="mon";
            }else if( $this->diff->days > 15 ){
                //search days data
                $this->type = "day";
            }else{
                //search hor data
                $this->type = "hor";
            }
        }
        
    }
 
  
    
    /**
     * Search concerned files for this request
     * @param string $directory the directory where searching file
     * @param string $prefix    the prefix in filename "d" for definitive, "q" for definitive
     * @param string $type      "yea", "mon", "day" or "hour"
     * @return array
     */
    private function search_files( $directory, $prefix ){
        $conn_id = Config::get_connexion();
        if(!$conn_id){
            $this->error = "FTP_FAILED";
            return;
        }

        $files = ftp_nlist ( $conn_id , $directory);
        $results = array();
        
        switch( $this->type){
            case "yea":
            case "mon":
            case "day":
                $start_year = intVal($this->start->format("Y"));
                $end_year = intVal( $this->end->format("Y"));
                for($i= $start_year; $i<= $end_year; $i++){
                    $file = $directory."/".$this->observatory . $i. $prefix . $this->type.".".$this->type;
                    
                    if(in_array( $file, $files)){
                        array_push( $results, $file);
                    }
                }
                break;
            case "hor":
                $current = new \DateTime( $this->start->format("Y-m-d"));
                
                while( $current<= $this->end){
                    
                    $file = $directory."/".$this->observatory . $current->format("Ym"). $prefix . $this->type.".".$this->type;
                    
                    if(in_array( $file, $files)){
                        array_push( $results, $file);
                        // array_push( $done, $current->format("Ym"));
                    }
                    $current->modify( 'first day of next month' );
                    
                }
                break;
        }
        $this->files = $results;
        return $results;
    }
    private function search_files_variation( ){
        $conn_id = Config::get_connexion();
        if(!$conn_id){
            $this->error = "FTP_FAILED";
            return;
        }
        $directory0 = "";
        $results = array();
        $days = $this->diff->days;
        $current = new \DateTime( $this->start->format("Y-m-d"));
        $files = array();
        $step = steppify( $days/15);
        $cumul = 1;
        $last = false;
        while( $current< $this->end && !$last){
            $directory = "/VARIATION/" . $this->observatory . "/min/".$current->format("Y");
            //read directory if not done
            if( $directory != $directory0){
                $directory0 =  $directory;
                $files = ftp_nlist ( $conn_id , $directory0);
                
            }
            $file = $directory."/".$this->observatory . $current->format("Ymd"). "vmin.min";
            
            if(in_array( $file, $files)){
                array_push( $results, $file);
                // array_push( $done, $current->format("Ym"));
            }
            
            $current->modify( '+'.$step.' days' );
            
        }
        //last day
        $directory = "/VARIATION/". $this->observatory ."/min/".$this->end->format("Y");
        if( $directory != $directory0){
            $directory0 =  $directory;
            $files = ftp_nlist ( $conn_id , $directory0);
            
        }
        $file = $directory."/". $this->observatory . $this->end->format("Ymd"). "vmin.min";
        
        if(!in_array($file,$results) && in_array( $file, $files)){
            array_push( $results, $file);
            // array_push( $done, $current->format("Ym"));
        }
        $this->files = $results;
        return $results;
    }
}

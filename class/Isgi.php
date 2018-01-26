<?php
/**
 * Treatment request for ISGI data center
 * @namespace isgi
 * @author epointal
 *
 */

namespace isgi;

/** use treatment file IAGA*/

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
    const API_URL = "http://isgi.unistra.fr/ws";
    public static $upload_dir = null;
    public static $pattern_indices = "/^\/cds\/isgi\/indices\/?(.*)$/";
    public static $pattern_indices_indice = "/^([a-zA-Z]{2,6})\/?(.?)$/";
    public static $pattern_data = "/^\/cds\/isgi\/data\/([a-zA-Z]{2,6})\/?(.*)$/";
    public static $indices = array( "aa", "am", "Kp", "Dst", "PC", "AE", "SC", "SFE", "Qdays", "CKdays");
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
        $this->parseRequest();

        switch( $this->type){
            case "indices":
                $this->searcher = new IndicesSearcher( $this->indice);
                break;
            case "data":
                $this->searcher = new DataSearcher( $this->indice );
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
            if( Config::is_code_indice( $matches[1])){
                $this->indice =  $matches[1];
                $this->request = $matches[2];
                $this->type = "data";
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
    public function __construct( $indice = null ){
        $this->indice = $indice;
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
Class IndicesSearcher extends Searcher{
    protected function treatment(){
        $this->result = array("error" => "NOT_IMPLEMENTED");
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
    
    public function __construct( $indice = null ){
        $this->indice = $indice;
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
    protected function treatment(){
        $this->search_files();
       
        if( is_null( $this->error)){
            
            $this->iaga = new \Iaga( $this->files, "", $this->start ,$this->end);
            $this->iaga->add_meta("isgi_url", $this->isgi_url);
            if( $this->indice == "Qdays"){
                // look if have the lastest month values
                $code = substr( $this->end, 0, 7);
                if( !preg_match('/^Qdays_([0-9\-]{7}_)?'.$code.'_D.dat$/', $this->files[0])){
                    $this->iaga->add_meta("no_data", $code."-01");
                }
                    
            }
            $this->result = $this->iaga->to_array();

            $this->clean_temporary_file();
        }else{
            $this->result = array("error" => $this->error);
        }
        
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
    private function prepare_get(){
        $data = array(
            "user" => "cnrs-formater610",
            "index" => $this->indice,
        );
        if(!is_null( $this->start)){
            $data["StartTime"] = $this->start;
        }
        if(!is_null( $this->end)){
            $data["EndTime"] = $this->end;
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
        $this->isgi_url = Config::API_URL."?" . $this->prepare_get();
        $content = file_get_contents($this->isgi_url );
        // the decoded content of your zip file

       // $content_type = get_response_header("Content-type", $http_response_header);
        //var_dump($content_type);
        switch( get_http_content_type( $http_response_header)){
            case "text":
                $this->error = $this->extract_error($content);
                break;
            case "zip":
                $filename = get_zip_filename( $http_response_header);
                $this->root = $filename;
                file_put_contents( Config::$upload_dir ."/" . $filename . ".zip" , $content);
                $this->extract_files();
                break;
            default:
                $this->error = "UNKNOWN_ERROR";
        }
       
    }
}


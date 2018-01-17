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
    if( strpos("text/plain", $type) !== false ){
        return "text";
    }
    if(  strpos("application/octet-stream", $type) !== false ){
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
    public static $upload_dir = "../content";
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
}



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
                $this->searcher = new Searcher();
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
        return json_encode( $this->result );
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
        $this->call_service();
        if( is_null( $this->error)){
            //$this->extract_files();
            var_dump( $this->files);
            $this->iaga = new \Iaga( $this->files, "", $this->start ,$this->end);
            $this->result = $this->iaga->to_array();
        }else{
            $this->result = array("error" => "NOT_FOUND");
        }
        
    }
    private function clean_temporary_file(){
        
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
    private function extract_files(){
        //extract file
        if( !is_null($this->root.".zip")){
            $zip = new \ZipArchive();
            $root = ( Config::$upload_dir ."/" .$this->root );
            var_dump( "root = ". $root);
            if ($zip->open( $root.".zip") === TRUE) {
                  $zip->extractTo( $root );
                  for($i = 0; $i < $zip->numFiles; $i++) {
                      $filename = $zip->getNameIndex($i);
                      var_dump(" i = " . $i);
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
    private function read_files(){
       
    }
    private function call_service(){
        $this->isgi_url = Config::API_URL."?" . $this->prepare_get();
        $content = file_get_contents($this->isgi_url );
        // the decoded content of your zip file
        $content_type = get_response_header("Content-type", $http_response_header);
        switch( get_http_content_type( $http_response_header)){
            case "text":
                $this->error = $content;
                break;
            case "zip":
                $filename = get_zip_filename( $http_response_header);
                $this->root = $filename;
                file_put_contents( Config::$upload_dir ."/" . $filename . ".zip" , $content);
                if( $this->extract_files()){
                   var_dump( "succes");
                }
                //$this->treatment_zip();
                break;
            default:
                $this->error = "UNKNOWN_RESPONSE";
        }
        var_dump($content_type);
        // this will empty the memory and appen your zip content
      //  $written = file_put_contents('php://memory', $content);
        $written = file_put_contents("../content/treus.zip", $content);
        // bytes written to memory
        var_dump($written);
        
        // new instance of the ZipArchive
        $zip = new \ZipArchive;
        
        // success of the archive reading
       // var_dump(true === $zip->open('php://memory'));
        var_dump($http_response_header);
       
  
      //  $temp = tmpfile();
       // if (!copy(Config::API_URL."?user=cnrs-formater610&index=aa", "../content/truc.zip")) {
           // echo "failed to copy $file...\n";
       // }
        $zip = new \ZipArchive;
        if ($zip->open("../content/treus.zip") === TRUE) {
           // $zip->extractTo('/mon/dossier/destination/');
           // $zip->close();
            echo 'ok';
            var_dump( "ok");
        } else {
            var_dump( "faoij");
            echo 'échec';
        }
    }
}

Class DataSearcher extends Searcher{
    
    private $diff = null;
    private $iaga = array();
}
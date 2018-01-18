<?php
function replace_code( &$item, $key,$obs){
    
    $item = str_replace( $obs, "", $item);
    
}
Class Iaga
{
    private $code;
    private $start = null;
    private $end = null;
    private $ftp = null;
    private $isgi = false;
    private $pattern = "/^[0-9\-]{10}\s[0-9]{2}:00:00/";
    private $ismin = false; // search in file minutes
    public $data = array();
    public $meta = array();
    
    public function __construct( $files, $code, $start=null, $end=null,$ftp= null, $ismin=false){
        $this->code = strtoupper($code);
        if( empty( $this->code)){
            $this->isgi = true;
        }
        $this->start = $start;
        $this->end = $end;
        $this->ftp = $ftp;
        $this->ismin = $ismin;
       // $this->ismin = true;
        $this->pattern();
        if(!$ftp ){
            foreach($files as $file){
                $flx = fopen( $file, "r+b");
                if( !$flx===false){
                    $this->read( $flx);
                    fclose( $flx);
                }
            }
            
        }else{
            foreach( $files as $file){
                
                $url = $this->ftp.$file;
                $ctx = stream_context_create(array('ftp' => array('resume_pos' => 0)));
                $flx = fopen($url, 'r', false, $ctx);
               // $flx =  ftp_get($connexion, "php://output", $file, FTP_BINARY);
               // $content = ob_get_contents();
              //  ob_end_clean();
               // var_dump( $content);
                //$this->read( $flx);
                if( !$flx===false){
                    $this->read( $flx);
                    fclose( $flx);
                }
            }
        }
    }
    
    public function isRequired( $date ){
        if( $this->start && $date < $this->start ){
            return false;
        }else if( $this->end && $this->end < $date){
            return false;
        }else{
            return true;
        }
    }
    public function read( $resource){
        @ob_end_clean(); 
        $i=0;
        // search line that do not start with a space
        $pattern = "/^(?![ D]{1})/";
        $answer = array();
        $matches = array();
        $first = empty( $this->meta);
       
        while (!feof($resource)) {
            //line start by D
            $line = fgets($resource);
           
            if( preg_match(  "/^D/", $line) ){ 
                $fields = preg_split('/\s+/', $line);
                if($this->isgi){
                    $length = 4;
                    if( substr($fields[4], 0,2) == "Kp"){
                        $length = 5;
                    }
                    $fields = array_splice( $fields, 0, $length);
                }else{
                    array_pop( $fields);
                    array_pop( $fields);
                    array_walk( $fields, "replace_code", $this->code);
                }
            }else if (preg_match($this->pattern, $line, $matches)){
                // data lines
                $data = preg_split('/\s+/',$line); 
                if( $this->isgi){
                    //keep only the index value
                    $data = array_splice( $data, 0, $length );
                    if(!empty( $data[3]) && $data[3]!= "9999"){
                        
                        $this->data[ ] = array_combine( $fields, $data);
                    }
                 }else{
                    array_pop($data);
                    if( !empty($data) && $this->isRequired( $data[0]) && $data[3]<"99999" && $data[4]<"99999"
                        && (!isset($data[5]) || $data[5]<"99999") && (!isset($data[6]) || $data[6]<"99999"))
                    $this->data[ ] = array_combine( $fields, $data);
                }
            }else if( $first && preg_match( "/^(?![1-9]{1}|\s#)/", $line)){

                $find = false;
                $name = trim( substr($line, 1,22));
                for($i=0; $i< count($this->meta) && $find===false; $i++){
                    if(isset( $this->meta[$i][$name])){
                        $find = $i;
                    }
                }
                if($find === false){
                    $this->meta[] = array(
                        "name" => $name, 
                        "content"=> trim( substr( $line, 23,45))
                    );
                    if("name" === "IAGA Code"){
                        $code = trim( substr( $line, 23,45));
                    }
                }else{
                    //case already define, but other value
                    //for example Data Type  quasi-definitive and Data Type definitive
                }
                
            }
            $i++;
        }
        //echo "nombre de lignes ". $i;
      
        //return array
    }
    public function add_meta( $name, $content){
        $this->meta[] = array( "name" => $name, "content" => $content);
    }
    public function to_array(){
        if(!empty( $this->meta)){
            return  array( "meta"=> $this->meta, "collection"=> $this->data);
        }else{
            return array( "error" => "NO_DATA");
        }
    }
     public function json(){
         if( !empty( $this->meta)){
            return json_encode( array( "meta"=> $this->meta, "collection"=> $this->data), JSON_NUMERIC_CHECK);
         }else{
            return '{ "error": "NO_DATA"}';
         }
     }
     private function pattern(){
         if( $this->ismin){
            
             if($this->ismin < 5){
                 $this->pattern = "/^[0-9\-]{10}\s[0-9]{2}:00:00/";
             }else{
                 $this->pattern = "/^[0-9\-]{10}\s12:00:00/";
             }
            // $this->pattern = "/^[0-9\-]{10}\s([0-9]{2}):00:00/";
         }else{
             $this->pattern =  "/^[0-9\-]{10}\s[0-9]{2}:00:00/";
         }
     }
}

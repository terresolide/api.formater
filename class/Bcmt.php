<?php
function steppify($delta) {
    /**  */
    if($delta<=1){
        return 1;
    }
    $precision = round( log10( $delta ));
    
    $p = pow(10, $precision);
    
    $max = ceil($delta / $p) * $p;
    return $max / 2 > $delta ? $max / 2 : $max;
};

Class BcmtSearcherData{
    
    const FTP_SERVER = "ftp.bcmt.fr";
    const FTP_USER = "bcmt_public";
    const FTP_PWD = "bcmt";
    public $observatory = null;
    public $start;
    public $end;
    public $conn_id;
    public $error = null;
    public $files = array();
    private $diff = null;
    
    
    public function __construct( $observatory, $get = array()){
        $this->observatory = $observatory;
        $this->extractParam( $get );
    }
    public function __destruct(){
        //close connexion si existe
    }
    private function compute_type(){
        if($this->diff->y > 25){
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
    private function connect(){
        $this->conn_id = ftp_connect(self::FTP_SERVER);// or die('{ "error": "NO_FTP_CONNEXION"}');
        if( ! @ftp_login( $this->conn_id, self::FTP_USER, self::FTP_PWD) ){
            $this->error = "LOGIN_FAILED";
        }
    }
    /**
     * Extract , filter, $start and $end properties and compute linked property type
     * @param array $get from $_GET
     * @return boolean
     */
    private function extract_param( $get ){
        if( is_null($get) || (!isset( $get["start"]) && !isset($get["end"]))){
            //last 10 days
            $this->start = new DateTime(" -3 day");
            $this->end = new DateTime();
            
        }else if( isset($get["start"])){
            if(preg_match("/^[0-9]{4}-(?:0[0-9]|1[0-2])-(?:0[0-9]|[1-2][0-9]|3[01])$/", $get["start"])){
                $this->start = new DateTime( $get["start"]);
            }else{
                $this->error = "INVALID_DATE_START";
                return false;
            }
           
            if(isset( $get["end"])){
                if(preg_match("/^[0-9]{4}-(?:0[0-9]|1[0-2])-(?:0[0-9]|[1-2][0-9]|3[01])$/", $get["end"])){
                    $this->end = new DateTime( $get["end"]);
                }else{
                    $this->error = "INVALID_DATE_END";
                    return false;
                }
            }else{
                $this->end = new DateTime();
            }
        }
        $this->diff = $this->start->diff( $this->end);
        if( $this->diff->invert === 1){
            $this->error = "INCONSISTENT_DATE";
            return false;
        }
            
        $this->compute_type();
        return true;
        
    }
    
    /**
     * 
     * @param string $directory the directory where searching file
     * @param string $prefix    the prefix in filename "d" for definitive, "q" for definitive
     * @param string $type      "yea", "mon", "day" or "hour"
     * @return array
     */
    private function search_files( $directory, $prefix,  $type){
        
        $files = ftp_nlist ( $this->conn_id , $directory);
        $results = array();
        
        switch( $type){
            case "yea":
            case "mon":
            case "day":
                $start_year = intVal($this->start->format("Y"));
                $end_year = intVal( $this->end->format("Y"));
                for($i= $start_year; $i<= $end_year; $i++){
                    $file = $directory."/".$this->observatory . $i. $prefix . $type.".".$type;
                    
                    if(in_array( $file, $files)){
                        array_push( $results, $file);
                    }
                }
                break;
            case "hor":
                $current = new DateTime( $this->start->format("Y-m-d"));
                
                while( $current<= $this->end){
                    
                    $file = $directory."/".$this->observatory . $current->format("Ym"). $prefix . $type.".".$type;
                    
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
}
Class BcmtResearch{
    private $request = null;
    public $type = "observatories";

   
    public $list = array( "aae", "ams", "bng", "box", "clf", "czt", "dlt", "dmc", "drv", "ipm", "kou", "lzh", "mbo", "paf", "phu", "ppt", "tan", "tam");
    
    public function __construct( $request, $param=null){
        $this->request = $request;
        
        $this->extractParam( $param );
    }
    private function extractParam( $param){
        switch( $this->type){
            
        }
        var_dump( $param );
    }
    private function parseRequest(){
        //suppr cds/bcmt
        if( preg_match( "/^\/cds\/bcmt\/?$/", $this->request)){
            //root           
            $this->type = "root";
            $this->request ="";
        }else if( preg_match("/^\/cds\/bcmt\/obs\/?(.*)$/", $this->request, $matches)){
          //search observatories
            $this->type = "observatories";
            $this->request = $matches[1];
        }else if( preg_match("/^\/cds\/bcmt\/ob\/([a-z]{3})\/?(.*)$/", $this->request, $matches)){
           //search geomagnetic data for one observatory
           if( in_array( strtolower( $matches[1]), $this->list)){
               $this->ob = $matches[1];
               $this->request = $matches[2];
               $this->type = "data";
           }else{
               $this->type ="404";
           }
           
           
            var_dump( $matches); 
        }else{
            // not found
            $this->type = "404";
            return false;
        }
        //extract param from request
       //$this->extractParam();
    }
}
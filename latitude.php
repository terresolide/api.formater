<?php
var_dump("machin");
class XX_CGM{
    public $dir = null;
    public $file = null;
    public $data = array();
    public function __construct( $file){
       echo $file;
       $this->dir = __DIR__ ."/latitudesGeomagnetiques/";
       $this->file = $this->dir.$file; 
      var_dump("construit");
       $this->read( );
    }
    
    public function read( ){
        $flx = fopen( $this->file, "r+b");
        while (!feof($flx)) {
            //line start by D
            $line = fgets($flx);
            $this->extract( $line );
        }
    }
    public function to_json(){
        
    }
    private function extract( $line){
        $result = preg_split( "/\s+/", $line);
        if( count($result)>4){
            $result = array_slice( $result,2,4);
            
            $this->data[] = array_combine(["lat", "lng", "latg", "lngg"], $result);
        }
    }
    
}

$truc = new XX_CGM( "2010_29CGM_N.dat");
var_dump( $truc->data);
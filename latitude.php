<?php

class XX_CGM{
    public $dir = null;
    public $file = null;
    public $data = array();
    public $options = array();
    public $coordinates = array();
    public function __construct( $file, $options=array()){
    	$this->options["name"]= $file;
    	$this->options["filename"]= $file;
    	$this->options = array_merge( $this->options, $options);

       $this->dir = __DIR__ ."/latitudesGeomagnetiques/";
       $this->file = $this->dir.$file; 
  
       $this->read( );
    }
    
    public function read( ){
        $flx = fopen( $this->file, "r+b");
        while (!feof($flx)) {
            //line start by D
            $line = fgets($flx);
            $this->extract( $line );
        }
        $this->normalize();
    }
    
    public function to_geojson(){
    	$result = array(
    			"type" => "Feature",
    			 "geometry" => array(
    			 		"type" => "LineString",
    			        "coordinates"=> $this->coordinates
    			 ),
    			"properties"=> $this->options
    		
    	);
    	return json_encode($result);
    }
    
    private function normalize(){
    	foreach( $this->data as $key=>$values){
    		if( $values["lat"]>90){
    			$this->data[$key]["lat"] = $values["lat"] - 180;
    		}
    		if( $values["lng"]>180){
    			$this->data[$key]["lng"] =  $values["lng"] - 360;
    			
    		}
    		
    	}
    	//tri par longitude croissante
    	foreach ($this->data as $key => $row) {
    		$lng[$key]  = $row['lng'];
    		$lat[$key] = $row['lat'];
    	//	$lngg[$key] = $row["lngg"];
    	//	$latg[$key] = $row["latg"];
    	}
    	array_multisort( $lng, SORT_ASC, $lat, SORT_ASC, $this->data);
    	foreach( $this->data as $value){
    		$this->coordinates[] = array(  $value["lng"], $value["lat"]);
    	}
    }
    private function extract( $line){
        $result = preg_split( "/\s+/", $line);
        if( count($result)>4){
            $result = array_slice( $result,2,4);
            $result = array_map( "floatVal", $result);
            $this->data[] = array_combine(["lat", "lng", "latg", "lngg"], $result);
        }
    }
    
}

$truc = new XX_CGM( "2010_29CGM_N.dat", array("name" => "latitude 29 N"));
//var_dump( $truc->data);
header("Content-Type: application/json");
echo $truc->to_geojson();
<?php

class XX_CGM{
    public $dir = null;
    public $file = null;
    public $data = array();
    public $options = array();
    public $coordinates = array();
    public function __construct( $data, $options=array()){
    	
    
   
    	switch( gettype($data)){
    		case "string":
    			$this->build_from_file( $data, $options);
    			break;
    		case "array":
				$this->build_from_data( $data, $options);
				break;
    	}

    
    }
    
    public function read( ){
        $flx = fopen( $this->file, "r+b");
        if(!$flx){
        	return;
        }
        while (!feof($flx)) {
            //line start by D
            $line = fgets($flx);
            $this->extract( $line );
        }
        
    }
    
    public function to_geojson(){
    	$this->normalize();
    	$result = array(
    			"type" => "Feature",
    			 "geometry" => array(
    			 		"type" => "LineString",
    			        "coordinates"=> $this->coordinates
    			 ),
    			"properties"=> $this->options
    		
    	);
    	return json_encode($result, JSON_NUMERIC_CHECK);
    }
    private function add_point0(){
    	if( count($this->coordinates)<3){
    		return;
    	}
    	$pointi = $this->coordinates[0];
    	$pointf = end($this->coordinates);
    	$point0_lat = (-180-$pointi[0])*($pointf[1] - $pointi[1])/($pointf[0] -360 - $pointi[0]) + $pointi[1];
    	array_unshift( $this->coordinates, array( -180, $point0_lat));

    }
    private function build_from_file( $file, $options=array()){
    	$this->options["name"]= $file;
    	$this->options["filename"]= $file;
    	$this->options = array_merge( $this->options, $options);
    	$this->dir = __DIR__ ."/latitudesGeomagnetiques/";
    	$this->file = $this->dir.$file;
    	
    	$this->read( );
    }
    
    private function build_from_data( $data, $options=array()){

    	$this->options =  $options;
        $this->data = $data;
    	
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
    	$this->add_point0();
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

$truc74 = new XX_CGM( "2010_74CGM_S.dat", array("name" => "latitude 74S"));
//var_dump( $truc->data);
$truc64 = new XX_CGM( "2010_64CGM_N.dat", array("name"=> "latitude 64N"));
$data58 = array();
foreach($truc74->data as $i => $value55){
	$line = array();
	foreach( $value55 as $key=> $value){
		$line[$key] = $value + ($truc64->data[$i][$key] - $value)/3;
	}
	array_push( $data58, $line);
}
//var_dump($data30);
$truc58 = new XX_CGM( $data58, array("name" => "latitude 58N"));
//var_dump( $truc30->data);
//

header("Content-Type: application/json");
echo $truc74->to_geojson();
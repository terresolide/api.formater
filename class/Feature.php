<?php
$list_cds = array( "bcmt", "isgi", "grenoble");

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

// renvoie toutes les observations dans la bbox, quelque soit l'extension temporelle
// compte pour chaque layer, le nombre d'obervations qui sont dans l'intervalle de temps
// et ajoute une propriété au observation ( pour dire que dans l'intervalle ou pas)
Class FeatureSearcher extends Searcher{
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
        global $list_cds;
        
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
        if( isset( $get["cds"]) && in_array( $get["cds"], $list_cds)){
        	$this->cds = $get["cds"];
        }else{
        	$this->cds = "bcmt";
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
            if( ! $this->in_bbox( $obs)){
                return false;
            }
            $return = [];
            $obsInTemporal =0;
            foreach( $obs->properties->observations as $observation){
                $startTime = $observation->temporalExtents->start;
                if( $observation->temporalExtents->end == "now"){
                    $end = new \DateTime();
                    $endTime = $end->format("Y-m-d");
                }else{
                    $endTime = $observation->temporalExtents->end;
                    
                }
                if( !empty( $observation->dataLastUpdate) && $observation->dataLastUpdate < $endTime){
                	$endTime = $observation->dataLastUpdate;
                }
                if( (is_null( $this->start) || $this->start <= $endTime)
                        && (is_null( $this->end ) || $this->end >= $startTime)){
                			$observation->inTemporal = 1;
                            //$return[] = $observation;
                            $obsInTemporal ++;
                            
                }
                $return[] = $observation;
            }
          
            if( count($return)>0){
            	$obs->properties->inTemporal = $obsInTemporal;
                $obs->properties->observations =  $return;
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
        switch( $obs->geometry->type){
        	case "Point":
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
		        break;
        	case "Polygon":
        	case "MultiPolygon":
        		
        		$bbox = $obs->properties->bbox;
        		
        		$i = 0;
        		$find = false;
        		while( !$find && $i < count($bbox)){
        		   $find = $this->cut_bbox( $bbox[$i]);
	        		$i++;
	        		
        		}
        		return $find;
        		break;
        }
    }
    private function cut_bbox( $bbox){
    	
        if( $bbox->south > $this->north || $bbox->north < $this->south){
           return false;
        }
        if( $bbox->east < $this->west || $bbox->west > $this->east){
            return false;
        }
        return true;
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
            
        }else{
            $this->error = "INVALID_BBOX";
        }
        
    }
    private function load_file(){
    	switch( $this->cds){
    		case "isgi":
    			$content = file_get_contents( DATA_FILE_ISGI);
    			break;
    		case "bcmt":
    			$content = file_get_contents( DATA_FILE_BCMT);
    			break;
    		case "grenoble":
    			$content = file_get_contents( DATA_FILE_GRENOBLE);
    			break;
    	}
       
        $result = json_decode( $content);

        if( $result->type == "FeatureCollection"){
        	return $result->features;
        }else{
        	return array( $result);
        }
    }
}

<?php
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
    	global $_SERVER;
    	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    		header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    		header('Access-Control-Allow-Credentials: true');
    		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    		header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding");
    		
    		$this->error = 'PRE_REQUEST';
    	}
        
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
            if( ! $this->in_bbox( $obs)){
                return false;
            }
            $return = [];
            foreach( $obs->properties->observations as $observation){
                $startTime = $observation->temporalExtents->start;
                if( $observation->temporalExtents->end == "now"){
                    $end = new \DateTime();
                    $endTime = $end->format("Y-m-d");
                }else{
                    $endTime = $observation->temporalExtents->end;
                }
                if( (is_null( $this->start) || $this->start <= $endTime)
                        && (is_null( $this->end ) || $this->end >= $startTime)){
                            $return[] = $observation;
                }
            }
           
            if( count($return)>0){
                $obs->properties->observations = $return;
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
        $content = file_get_contents( "../data/geojson_catalog2.json");
        $result = json_decode( $content);
        return $result->features;
    }
}

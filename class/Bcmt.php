<?php
Class Bcmt{
    public $obs = array();
    public $ob = null;
    public $research = null;
    public $start = null;
    public $end =null;
    public $bbox = array();
    public $request = null;
    
    public function __construct( $request){
        $this->request = $request;
        $this->parseRequest();
    }
    private function parseRequest(){
        //suppr cds/bcmt
        if( preg_match("/^\/cds\/bcmt\/obs\/?(.*)$/", $this->request, $matches)){
          var_dump($matches);
            $this->research = "observatories";
            $this->request = $matches[1];
        }else if( preg_match("/^\/cds\/bcmt\/ob\/([a-z]{3})/?(.*)$/"), $this->request, $matches){
            
        }
        //extract param from request
        $result = explode("/", $this->request);
    }
}
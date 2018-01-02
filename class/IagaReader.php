<?php
Class IagaReader
{
    
    public function __construct(){
        
    }
  
    public function read( $resource, $start = null, $end=null){
        $i=0;
        // search line that do not start with a space
        $pattern = "/^(?! )/";
        $answer = array();
        while (!feof($resource)) {
            $line = fgets($resource);
            if (preg_match($pattern, $line)) { $answer[ ] = $line; }
            $i++;
        }
        echo "nombre de lignes ". $i;
        var_dump($answer);
        //return array
    }
}
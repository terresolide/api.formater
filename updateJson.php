<?php
/** Search last date of modification files*/
/** change source file if needed*/
$infile =  "data/geojson_bcmt_ft_test2.json";
$outputfile = "data/geojson_bcmt_ft_test3.json";

$content = file_get_contents($infile);
$result = json_decode( $content);


$lieux = $result->features;
$withdoi = array("aae", "ams", "bng",  "clf",  "czt",  "dlt",  "dmc",  "drv",  "ipm",  "kou",  "mbo",  "paf", "phu", "ppt",  "psm",  "tan",  "vlj");
foreach($lieux as $key => $lieu ){
   // var_dump( $lieu->properties->code);
    $obs2 = [];
    //$lieu->properties->identifiers = array( "customId" => $lieu->properties->code);
   // unset( $lieu->properties->identifier);
   // unset( $lieu->properties->code);
    foreach( $lieu->properties->observations as $obs){
    	if( strpos(  $obs->identifiers->customId, "QUASI_DEFINITIVE")){
    		$type = "DEFINITIVE";
    	}elseif( strpos(  $obs->identifiers->customId, "-DEFINITIVE")){
    	    $type = "QUASI_DEFINITIVE";
    	}else{
    		$type = "VARIATION";
    	}
    	$obs->links[] = array( 
    			"type" => "FTP_DOWNLOAD_LINK",
    			"url"  => "ftp://bcmt_public:bcmt@ftp.bcmt.fr/".$type."/".strtolower($lieu->properties->identifiers->customId),
    			"name" => "FTP DIRECTORY"	);
//     	$obs->identifiers = array();
//     	$obs->identifiers["customId"] = $obs->customId;
//     	unset( $obs->customId);
//         $obs->observedProperty->shortName = "HDZF / XYZF";
//     	$obs->procedure->algorithms = array("Formula for computing non-reported elements:<br />
// X=H*cos(D), Y=H*sin(D), tan(I)=Z/H <br/>
// D is expressed in minutes of arc. <br /> 
// H=SQRT(X*X+Y*Y), tan(D)=Y/X, tan(I)=Z/SQRT(X*X+Y*Y) ");
//     	$code = $lieu->properties->identifiers["customId"];
//         if( strpos(  $obs->identifiers["customId"], "-DEFINITIVE")>0){
            
//             if( in_array( strtolower($code), $withdoi)){
//             	$obs->identifiers["DOI"] = "10.18715/BCMT.MAG.DEF";
//             }
//         }
        
    }
   // $lieu->properties->observations = $obs2;
}

    file_put_contents($outputfile,json_encode( $result, JSON_NUMERIC_CHECK) );
   header("Content-Type: application/json");
   echo json_encode( $result, JSON_NUMERIC_CHECK);
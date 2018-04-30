<?php
/** Search last date of modification files*/
/** change source file if needed*/
$infile =  "../data/geojson_bcmt_ft_test.json";
$outputfile = "../data/geojson_bcmt_ft_test2.json";

$content = file_get_contents($infile);
$result = json_decode( $content);


$lieux = $result->features;
// $withdoi = array("aae", "ams", "bng",  "clf",  "czt",  "dlt",  "dmc",  "drv",  "ipm",  "kou",  "mbo",  "paf", "phu", "ppt",  "psm",  "tan",  "vlj");

//Modification des titres de toutes les observations du BCMT pour supprimer les codes

$translate = array(
		"aae" => array( "fr" => "Addis Abeba", "en" => "Addis Ababa"),
		"ams" => array( "fr" => "Île Amsterdam", "en" => "Amsterdam Island"),
		"bng" => array( "fr" => "Bangui", "en" => "Bangui"),
		"box" => array( "fr" => "Borok, Yaroslavl", "en" => "Borok, Yaroslavl"),
		"clf" => array( "fr" => "Chambon-la-Forêt", "en" => "Chambon-la-Foret"),
		"ipm" => array( "fr" => "Île de Pâques", "en" => "Isla de Pascua"),
		"kou" => array( "fr" => "Kourou, Guyane Française", "en" => "Kourou, French Guiana"),
		"lzh" => array( "fr" => "Lanzhou", "en" => "Lanzhou" ),
		"mbo" => array( "fr" => "Mbour", "en" => "Mbour"),
		"phu" => array( "fr" => "Phu Thuy", "en" => "Phu Thuy"),
		"dlt" => array( "fr" => "Dalat", "en" => "Dalat"),
		"ppt" => array( "fr" => "Pamataï, Tahiti, Polynésie Française", "en" => "Pamatai, Tahiti, French Polynesia "),
		"tam" => array( "fr" => "Tamanrasset", "en" => "Tamanrasset"),
		"czt" => array( "fr" => "Archipel de Crozet, Île de la Possession", "en" => "Crozet Archipelago, Possession Island"),
		"dmc" => array( "fr" => "Base de Concordia, Dôme C, Haut Plateau Antarctique", "en" => "Base of Concordia, Dome C, High Antarctic Plateau"),
		"drv" => array( "fr" => "Dumont d'Urville, Terres Adélie", "en" => "Dumont d'Urville, Adelie Land"),
		"paf" => array( "fr" => "Port aux Français, Îles Kerguelen", "en" => "Port aux Français, Kerguelen Islands"),
		"psm" => array( "fr" => "Parc Saint Maur", "en" => "Parc Saint Maur"),
		"qsb" => array( "fr" => "Qsaybeh, Lebanon", "en" => "Qsaybeh, Lebanon"),
		"tan" => array( "fr" => "Antananarivo", "en" => "Antananarivo"),
		"vlj" => array( "fr" => "Val Joyeux", "en" => "Val Joyeux")		
); 
foreach($lieux as $key => $lieu ){
   // var_dump( $lieu->properties->code);
   // $obs2 = [];
    //$lieu->properties->identifiers = array( "customId" => $lieu->properties->code);
   // unset( $lieu->properties->identifier);
   // unset( $lieu->properties->code);
    $code = strtolower( $lieu->properties->identifiers->customId );
    foreach( $lieu->properties->observations as $obs){
    	
    	//$obs->observedProperty->name = array( "en" => "Geomagnetic Field", "fr" => "Champ magnétique terrestre");
    	if( strpos(  $obs->identifiers->customId, "QUASI_DEFINITIVE")){
    		$type = "QUASI_DEFINITIVE";
    		$obs->title = array( 
    				"fr" => "Données géomagnetiques quasi-définitives - ". $translate[$code]["fr"],
    				"en" => "Quasi-definitive geomagnetic data - ". $translate[$code]["en"]
    		);
    	}elseif( strpos(  $obs->identifiers->customId, "-DEFINITIVE")){
    	    $type = "DEFINITIVE";
    	    $obs->title = array(
    	    		"fr" => "Données géomagnetiques définitives - ". $translate[$code]["fr"],
    	    		"en" => "Definitive geomagnetic data - ". $translate[$code]["en"]
    	    );
    	}else{
    		$type = "VARIATION";
    		$obs->title = array(
    				"fr" => "Variations géomagnetiques - ". $translate[$code]["fr"],
    				"en" => "Geomagnetic variations - ". $translate[$code]["en"]
    		);
    	}
//     	for($i=0; $i<count($obs->links) ; $i++){
//     		if( isset( $obs->links[$i]->name)){
    		
//     			if( $obs->links[$i]->type === "FTP_DOWNLOAD_LINK"){
// 			    	$obs->links[$i] = array( 
// 			    			"type" => "FTP_DOWNLOAD_LINK",
// 			    			"url"  => "ftp://bcmt_public:bcmt@ftp.bcmt.fr/".$type."/".strtolower($lieu->properties->identifiers->customId),
// 			    			"description" => array("fr" => "Répertoire FTP"	, "en" => "FTP Directory")
// 			    			);
//     			}else{
// 			    	$obs->links[$i]->description = array( "en" => "BCMT download page", "fr" => "Page de téléchargement du BCMT");
// 			    	unset( $obs->links[$i]->name);
//     			}
//     		}
//     	}
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
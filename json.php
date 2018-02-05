<?php
$content = file_get_contents( "data/geojson_observatories.json");
$result = json_decode( $content);
$result2 = new stdClass();
$result2->type = "FeatureCollection";
$result2->features = [];
//connexion to bcmt 
foreach( $result->features as $obs){
    $feature = new StdClass();
    $feature->type = "Feature";
    $feature->geometry = $obs->geometry;
    $feature->properties = new StdClass();
    $feature->properties->name = new StdClass();
    $feature->properties->name->fr = $obs->properties->title->fr;
    $feature->properties->name->en = $obs->properties->title->en;
    $feature->properties->description = new stdClass();
    $feature->properties->description = $obs->properties->description;
    //replace description
    $pos = strpos( $feature->properties->description->fr, "<br /><strong>Geomagnetic data : </strong>");
    $feature->properties->description->fr = substr( $feature->properties->description->fr, 0, $pos);
    $pos = strpos( $feature->properties->description->en, "<br");
    $feature->properties->description->en = substr( $feature->properties->description->en, 0, $pos);
    $feature->properties->identifier ="";
    $feature->properties->organisation = [];

    $feature->properties->code = $obs->properties->code;
    $feature->properties->observations = [];
    $instruments = [];
    $abstract = new StdClass();
    switch( $feature->properties->code){
        case "AAE":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Institute of Geophysics, Space Science and Astronomy (Ethiopia)");
            array_push($instruments, "DI-flux Theodolite Zeiss 010B Theodolite & Bartington mag01H");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            array_push($instruments, "Proton scalar magnetometer (GEOMETRICS G856)");
            
            //case variation
            $orientation = "HDZF";
            
            break;
        case "AMS":
            array_push( $feature->properties->organisation, "Ecole et Observatoire des Sciences de la Terre (France)");
            array_push( $feature->properties->organisation, "Institut Polaire Francais (France)");
            array_push($instruments, "DI-flux Theodolite Zeiss 010A Theodolite & Bartington mag01H");
            array_push($instruments, "Overhauser proton magnetometer (SM90R)");
            array_push($instruments, "Triaxial fluxgate variometer (VFO 31)");
            $orientation = "HDZF";
            break;
        case "BOX":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Russian Academy of Sciences (Russia)");
            array_push($instruments, "DI-flux 3T2KP LEMI 203");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            $orientation = "HDZF";
            break;
        case "CLF":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push($instruments, "DI-flux Theodolite Zeiss 010A Theodolite & Bartington mag01H");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Nuclear Magnetic Resonance scalar magnetometer (IXSEA SM100)");
            array_push($instruments, "Three-component fluxgate vector magnetometer (Thomson Sintra ASM)");
            $orientation = "HDZF";
            break;
        case "CZT":
            array_push( $feature->properties->organisation, "Ecole et Observatoire des Sciences de la Terre (France)");
            array_push( $feature->properties->organisation, "Institut Polaire Francais (France)");
            array_push($instruments, "DI-flux Theodolite Zeiss 010A Theodolite & Bartington mag01H");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            array_push($instruments, "3 component fluxgate (VFO 31)");
            $orientation = "HDZF";
            break;
        case "DLT":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Institute of Geophysics of the Vietnamese Academy of Science and Technology (Vietnam)");
            array_push($instruments, "DI-flux Theodolite Zeiss 010B Theodolite & Bartington mag01H");
            array_push($instruments, "Overhauser effect proton scalar magnetometer GEM System (GSM19)");
            array_push($instruments, "Proton Scalar Magnetometer (GEOMETRICS G856)");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            $orientation = "HDZF";
            break;
        case "DMC":
            array_push( $feature->properties->organisation, "Ecole et Observatoire des Sciences de la Terre (France)");
            array_push( $feature->properties->organisation, "Istituto Nazionale di Geofisica e Vulcanologia (Italy)");
            array_push( $feature->properties->organisation, "Institut Polaire Francais (France)");
            
            array_push($instruments, "DI-flux Theodolite Zeiss 010A Theodolite & Bartington mag01H");
            array_push($instruments, "Proton Overhauser magnetometer(Geomag SM90R)");
            array_push($instruments, "DMI Fluxgate Magnetometer, Model FGE version G - special Arctic model for low temperatures");
            array_push($instruments, "Overhauser - Geomag SM90 F");
            $orientation = "XYZF";
            break;
        case "DRV":
            array_push( $feature->properties->organisation, "Ecole et Observatoire des Sciences de la Terre (France)");
            array_push( $feature->properties->organisation, "Institut Polaire Francais (France)");
            
            array_push($instruments, "DI-flux Theodolite Zeiss 010A Theodolite & Bartington mag01H");
            array_push($instruments, "Proton Overhauser magnetometer(Geomag SM90R)");
            array_push($instruments, "DMI Fluxgate Magnetometer, Model FGE version G - special Arctic model for low temperatures");
            array_push($instruments, "Overhauser - Geomag SM90 F");
            $orientation = "XYZF";
            break;
        case "IPM":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Dirección Meteorológica de Chile (Chile) ");
            
            array_push($instruments, "DI-flux Theodolite Zeiss 010B Theodolite & Bartington mag01H");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            array_push($instruments, "Proton Scalar Magnetometer (GEOMETRICS G856)");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Overhauser effect proton magnetometer (Geomag SM90R)");
           
            $orientation = "HDZF";
            break;
        case "KOU":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
         
            
            array_push($instruments, "DI-flux Theodolite Zeiss 010A Theodolite & Bartington mag01H");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
           
            array_push($instruments, "Proton Scalar Magnetometer (GEOMETRICS G856)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");

            $orientation = "HDZF";
            break;
        case "LZH":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "China Earthquake Administration (China) ");
            array_push($instruments, "VM391 IPGP-AD24CUB 24 bits");
            array_push($instruments, "DI-flux MG2KP EOST 93");
            array_push($instruments, "Proton Scalar Magnetometer (GEOMETRICS G856)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            $orientation = "HDZF";
            break;
        case "MBO":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Institut de Recherche pour le Développement (France)");
            
            array_push($instruments, "DI-flux Theodolite Zeiss 010B Theodolite & Bartington mag01H");
            array_push($instruments, "Overhauser effect proton scalar magnetometer GEM System (GSM19)");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Nuclear Magnetic Resonance scalar magnetometer (IXSEA SM100)");
            $orientation = "HDZF";
            break;
        case "PAF":
            array_push( $feature->properties->organisation, "Ecole et Observatoire des Sciences de la Terre (France)");
            array_push( $feature->properties->organisation, "Institut Polaire Francais (France)");
            
            array_push($instruments, "DI-flux Theodolite Zeiss 010A Theodolite & Bartington mag01H");
            array_push($instruments, "Proton Overhauser magnetometer(Geomag SM90R)");
            array_push($instruments, "Three-component fluxgate variometer (VFO 31)");

            $orientation = "HDZF";
            break;
        case "PHU":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Institute of Geophysics of the Vietnamese Academy of Science and Technology (Vietnam)");
            
            array_push($instruments, "DI-flux Theodolite Zeiss 010B Theodolite & Bartington mag01H");
            array_push($instruments, "Overhauser effect proton scalar magnetometer GEM System (GSM19)");
            array_push($instruments, "Proton Scalar Magnetometer (GEOMETRICS G856)");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            $orientation = "HDZF";
            break;
        case "PPT":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Commissariat à l’énergie atomique (CEA)");
            
            array_push($instruments, "DI-flux Theodolite Zeiss 010B Theodolite & Bartington mag01H");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEM System GSM19)");
            array_push($instruments, "Proton Scalar Magnetometer (GEOMETRICS G856)");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Nuclear Magnetic Resonance scalar magnetometer (IXSEA SM100)");
            
            $orientation = "HDZF";
            break;
        case "TAM":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Centre de recherche en astronomie, astrophysique et géophysique (Algeria)");
            
            array_push($instruments, "DI-flux Theodolite Zeiss 020B Theodolite & DMI Model G");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEM System GSM19)");
            array_push($instruments, "Proton Scalar Magnetometer (GEOMETRICS G856)");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R) ");
            
            $orientation = "HDZF";
            break;
        case "BNG":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "Institut de Recherche et de Developpement (France)");
            
            array_push($instruments, "DI-flux Zeiss 010B Bartington 01H");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEM System GSM19)");
            array_push($instruments, "Homocentric fluxgate vector magnetometer (GEOMAG M391)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            
            $orientation = "HDZF";
            break;
        case "PSM":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            
            $orientation = "HDZ";
            break;
        case "QSB":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            array_push( $feature->properties->organisation, "National Council for Scientific Research (Lebanon)");
            
            array_push($instruments, "Homocentric fluxgate vector magnetometer (IPGP VM391)");
            array_push($instruments, "Overhauser effect proton scalar magnetometer (GEOMAG SM90R)");
            array_push($instruments, "DI-flux MG2KP Degre LEMI 203-02");
            array_push($instruments, "Overhauser effect proton scalar magnetometer GEOMETRICS G856");
            
            $orientation = "HDZF";
            break;
        case "TAN":
            array_push( $feature->properties->organisation, "Institut et Observatoire Géophysique d'Antananarivo (Madagascar)");
            array_push( $feature->properties->organisation, "Ecole et Observatoire des Sciences de la Terre (France)");
 
            array_push($instruments, "Portable Declinometer-Inclinometer");
            array_push($instruments, "D-I Flux, theodolite Zeiss 010 B ");
            array_push($instruments, "Three-component fluxgate variometer (VFO 31)");
            array_push($instruments, "proton Overhauser magnetometer(Geomag SM90R)");
            
            $orientation = "HDZF";
            break;
        case "VLJ":
            array_push( $feature->properties->organisation, "Institut de Physique du Globe de Paris (France)");
            
            $orientation = "HDZ";
            break;
    }
   // $content = str_replace('<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">', "", $content);
//$content = str_replace('<link rel="icon" href="/favicon.ico" type="image/x-icon">', "", $content);
  //  $doc->loadHTML($content);
  $titles= array(
          "fr" => array(
                  "VARIATION" => "Variations géomagnétiques",
                  "DEFINITIVE"=> "Données géomagnétiques définitives",
                  "QUASI_DEFINITIVE" => "Données géomagnétiques quasi-définitives"),
          "en" => array(
                  "VARIATION" => "Geomagnetic variations",
                  "DEFINITIVE"=> "Definitive geomagnetic data",
                  "QUASI_DEFINITIVE" => "Quasi-definitive geomagnetic data")
  );
//  if( $feature->properties->code != "PSM" && $feature->properties->code!="VLJ"){
    foreach( ["VARIATION", "DEFINITIVE", "QUASI_DEFINITIVE"] as $dataType){
        $abstract = new StdClass();
        switch( $dataType){
            case "VARIATION":
                $abstract->fr = "Série temporelle en temps réel des variations géomagnétiques, orientation ".$orientation;
                $abstract->en = "Real Time Temporal Serie of geomagnetic variations, orientation ".$orientation;
                break;
            case "DEFINITIVE":
                $abstract->fr = "Série temporelle définitive des données géomagnétiques (latence 1an), orientation ".$orientation;
                $abstract->en = "Definitive Temporal Serie of geomagnetic data (latency 1year), orientation ".$orientation;
                break;
            case "QUASI-DEFINITIVE":
                $abstract->fr = "Série temporelle quasi-définitive des données géomagnétiques (latence 1 mois), orientation ".$orientation;
                $abstract->en = "Quasi-definitive Temporal Serie of geomagnetic data (latency 1 month), orientation ".$orientation;
                
                break;
        }
        // definitive
        $observation = new StdClass();
       // $observation->identifiers = new StdClass();
        $observation->id = null;
        $observation->customId = $feature->properties->code."-".$dataType;
        $observation->metadataLastUpdate = "2018-02-05";
        $observation->dataLastUpdate ="";
         $observation->observedProperty = new StdClass();
         $observation->observedProperty->type = "GeomagneticData";
         $observation->observedProperty->shortName = $orientation;
//         $observation->observedProperty->uom ="";
//         $observation->observedProperty->timeResolution =  array("min");
       
        $observation->title = new StdClass();
        $observation->title->fr = $feature->properties->code." - ".$titles["fr"][$dataType];
        $observation->title->en = $feature->properties->code." - ".$titles["en"][$dataType];
        $observation->abstract = $abstract;
        $observation->description = $abstract;
        $observation->domainOfInterest = "earth";
        $observation->links = $obs->properties->links;
        $observation->quicklook = $obs->properties->quicklook;
        $observation->status = "public";
        $observation->pole = "formater";
        $observation->procedure = new StdClass();

        $observation->procedure->instruments = $instruments;
        
        //pour definitive et quasidefinitive 
        $observation->procedure->method = new StdClass();
        $observation->procedure->method->fr ="";
        $observation->procedure->method->en = "";
        $observation->keywords = [];
        $observation->formats = [];
        $format = new StdClass();
        $format->name = "IAGA2002";
        array_push( $observation->formats, $format);
        $observation->formaterDataCenter = array( "code" => "TS-ANO-4/SNO1", "name" => "BCMT");
        $observation->type = "time series";
        $observation->license = new StdClass();
        $observation->license->code = "CC BY-NC 4.0";
        $observation->license->url = "https://creativecommons.org/licenses/by-nc/4.0/";
        $observation->contacts = $obs->properties->contacts;
        
        foreach($observation->contacts as $contact){
            $contact->roles = ["pointOfContact"];
            if($contact->name == "Aude Chambodut"){
                $contact->orcId = "0000-0001-8793-1315";
            }
            if( $contact->name == "Sergey ANISIMOV"){
                $contact->orcId = "0000-0001-6684-6365";
            }
        }
        
        //to read in BCMT
        //variation //definitive quasi-definitive L1
        $observation->processingLevel = "L1";
        if( $dataType == "VARIATION"){
            $observation->processingLevel = "L0";
        }
        $observation->publication = [];
        $observation->temporalExtents = $obs->properties->temporalExtents;
       
        array_push( $feature->properties->observations, $observation);
    }
 /* }else{
      $observation = new StdClass();
      $abstract->fr = "Série temporelle des variations géomagnétiques, orientation ".$orientation;
      $abstract->en = "Temporal Serie of geomagnetic variations, orientation ".$orientation;
      
      // $observation->identifiers = new StdClass();
      $observation->id = null;
      $observation->customId = $feature->properties->code."-ARCHIVES";
      $observation->metadataLastUpdate = "2018-02-05";
      $observation->dataLastUpdate ="";
      $observation->observedProperty = new StdClass();
      //$observation->observedProperty = array( "type" => "GeomagneticData", "shortName"=>$observation,  "uom" => "", "timeResolution" => array("min"));
      
      $observation->title = new StdClass();
      $observation->title->fr = $feature->properties->code." - archives des données géomagnétiques";
      $observation->title->en = $feature->properties->code." - archives geomagnetic data";
      $observation->abstract = $abstract;
      $observation->description = $abstract;
      $observation->domainOfInterest = "earth";
      $observation->links = $obs->properties->links;
      $observation->quicklook = $obs->properties->quicklook;
      $observation->status = "public";
      $observation->pole = "formater";
      $observation->procedure = new StdClass();
      
     // $observation->procedure->instruments = $instruments;
      
      //pour definitive et quasidefinitive
      $observation->procedure->method = new StdClass();
      $observation->procedure->method->fr ="";
      $observation->procedure->method->en = "";
      $observation->keywords = [];
      $observation->formats = [];
      $format = new StdClass();
      $format->name = "pdf";
      array_push( $observation->formats, $format);
      $observation->formaterDataCenter = array( "code" => "TS-ANO-4/SNO1", "name" => "BCMT");
      $observation->type = "time series";
      $observation->license = new StdClass();
      $observation->license->code = "CC BY-NC 4.0";
      $observation->license->url = "https://creativecommons.org/licenses/by-nc/4.0/";
      $observation->contacts = $obs->properties->contacts;
      
      foreach($observation->contacts as $contact){
          $contact->roles = ["pointOfContact"];
          if($contact->name == "Aude Chambodut"){
              $contact->orcId = "0000-0001-8793-1315";
          }
      }
      
      //to read in BCMT
      //variation //definitive quasi-definitive L1
    
      $observation->processingLevel = "L0";
   
      $observation->publication = [];
      $observation->temporalExtents = $obs->properties->temporalExtents;
      
      array_push( $feature->properties->observations, $observation);
  }*/
    array_push( $result2->features, $feature);
    
}

file_put_contents("data/geojson_catalog.json",json_encode( $result2, JSON_NUMERIC_CHECK) );
header("Content-Type: application/json");
echo json_encode( $result2, JSON_NUMERIC_CHECK);
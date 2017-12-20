<?php
if(file_exists(__DIR__."/center/".$_GET ["cds"].".php")){
    
    include_once __DIR__."/center/".$_GET ["cds"].".php";
}

$response = array( "machin" => "truc");

if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Content-Type: application/json");
    
    echo json_encode( $response );
}else{
    /**
     * case http request
     */
    header("Content-Type: text/html"); 
?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
    <title>reponse</title>
    </head>
    <body>
    <h1>REPONSE A AFFICHER</h1>
    </body>
    </html>
<?php }?>



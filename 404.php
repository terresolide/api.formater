<?php
/**
 * case ajax request
 */
if( isset( $_SERVER['HTTP_ORIGIN'] ) ){
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Content-Type: application/json"); 
   
    echo json_encode(array( "error" => "NOT FOUND"));
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
<title>PAGE NON TROUVÉE</title>
</head>
<body>
<h1>PAGE NON TROUVÉE</h1>
</body>
</html>
<?php }?>
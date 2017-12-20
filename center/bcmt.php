<?php
$ftp_server = "ftp.bcmt.fr";
$file = "ftp://ftp.bcmt.fr/DEFINITIVE/ams/yea/ams.year";
// Mise en place d'une connexion
$conn_id = ftp_connect($ftp_server) or die("Impossible de se connecter au serveur $ftp_server"); 
//$r = ftp_pasv($ftp, true);
//ftp_login($conn_id, 'anonymous', '');

//$ret = ftp_nb_fget($conn_id, $fp, $file, FTP_BINARY);
//ob_start();
//$result = ftp_get($conn_id, "php://output", $file, FTP_BINARY);
//$data = ob_get_contents();
//ob_end_clean();
//echo $data;

$handle = file_get_contents("ftp://ftp.bcmt.fr/DEFINITIVE/ams/yea/ams.year");
echo $handle;
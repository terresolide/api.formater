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

//$handle = file_get_contents("ftp://ftp.bcmt.fr/DEFINITIVE/ams/yea/ams.year");

function ftp_get_contents($ftp_stream, $remote_file, $mode=FTP_ASCII, $resume_pos=null){
    $pipes=stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
    if($pipes===false) return false;
    if(!stream_set_blocking($pipes[1], 0)){
        fclose($pipes[0]); fclose($pipes[1]);
        return false;
    }
    $fail=false;
    $data='';
    if(is_null($resume_pos)){
        $ret=ftp_nb_fget($ftp_stream, $pipes[0], $remote_file, $mode);
    } else {
        $ret=ftp_nb_fget($ftp_stream, $pipes[0], $remote_file, $mode, $resume_pos);
    }
    while($ret==FTP_MOREDATA){
        while(!$fail && !feof($pipes[1])){
            $r=fread($pipes[1], 8192);
            if($r==='') break;
            if($r===false){ $fail=true; break; }
            $data.=$r;
        }
        $ret=ftp_nb_continue($ftp_stream);
    }
    while(!$fail && !feof($pipes[1])){
        $r=fread($pipes[1], 8192);
        if($r==='') break;
        if($r===false){ $fail=true; break; }
        $data.=$r;
    }
    fclose($pipes[0]); fclose($pipes[1]);
    if($fail || $ret!=FTP_FINISHED) return false;
    return $data;
}
$handle = ftp_get_contents("ftp://ftp.bcmt.fr/DEFINITIVE/ams/yea/ams.year", "php://output");

echo $handle;
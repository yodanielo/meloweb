<?php
error_reporting(E_ALL);
ini_set('session.use_trans_sid', 0);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.gc_maxlifetime', 600000);
session_cache_limiter('private,must-revalidate');
session_start();
header("Cache-control: private");

//llamo a la funcion pertinente
include 'includes/config.php';
//include 'core/database.php';
include 'core/helpers.php';
include 'includes/contenido.php';

$paths=  getParams();
if(is_callable("".$paths[1])){
    call_user_func("".$paths[1],  array_slice($paths, 1));
}
else{
    //boto 404
    header("HTTP/1.0 404 Not Found");
    if($config["error"]["error_404"]=="" || $config["error"]["error_404"]==null){
        echo "Error 404: Web not found...";
    }
}
exit;
?>

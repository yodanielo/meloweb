<?php
/**
 * Solo usalo en los valores que sacas de la BD en caso de que en IE se muestren
 * mal los caracetres especiales pero en Firefox, Chrome y safari se muestren
 * bien
 * @param <type> $str
 */
function cie($str) {
    if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
        return utf8_decode($str);
    }else {
        return $str;
    }
}
/**
 * Envia un email con el formato de EdMultimedia
 * @param string $de el correo que envia
 * @param string $para el correo que recibe
 * @param string $asunto el asunto del mensaje
 * @param string $mensaje el cuerpo del mensaje
 * @return int retorna 1 si el envio fue exitoso, y 0 si se produco un error
 */
function enviarmail($de,$para,$asunto,$mensaje) {
    $eol="\r\n";
    $now = mktime().".".md5(rand(1000,9999));
    $headers = "From:".$de.$eol."To:".$para.$eol;
    $headers .= 'Return-Path: '.$de.'<'.$de.'>'.$eol;
    $headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
    $headers .= "X-Mailer: PHP v".phpversion().$eol;
    $headers .= "Content-Type: text/html; charset=iso-8859-1".$eol;
    $resultado=mail($para, $asunto, $mensaje, $headers);
    return $resultado;
}
/**
 * Corta una cadena y la separa por saltos de linea cada cierto numero de 
 * espacios
 * @param String $str cadena de texto
 * @param integer $espacios cada cuántos espacios se va dividir la cadena
 * @return string devuelve una cadena separada por etiquetas <br/>
 */
function separar_nombres($str,$espacios) {
    $min=explode(" ",$str);
    if(count($min)>1){
        $cad='';
        foreach($min as $m) {
            if(strlen($m)>$espacios)
                $cad.=' '.separar_nombres($m, $espacios);
            else
                $cad.=' '.$m;
        }
        return $cad;
    }else{
        if(count($min)==1){
            $min=str_split($str, $espacios);
            $str=implode($min, "-<br/>");
            return $str;
        }
        else{
            return "";
        }
    }
//    if(strlen($str)>0){
//        $r=explode(" ",$str);
//        return $r[0]." ".$r[1];
//    }else
//        return "";
}
/**
 * Sustituye caracteres especiales por sus equivalentes en comandos de escape
 * @param <type> $cadena 
 */
function esp_char($cadena) {
    $traducciones=array(
            "Á"=>"Aacute;",
            "É"=>"Eacute;",
            "Í"=>"Iacute;",
            "Ó"=>"Oacute;",
            "Ú"=>"Uacute;",
            "á"=>"aacute;",
            "é"=>"eacute;",
            "í"=>"iacute;",
            "ó"=>"oacute;",
            "ú"=>"uacute;",
            "Ñ"=>"&Ntilde;",
            "ñ"=>"&ntilde;",
            "¡"=>"&iexcl;",
            "¿"=>"&iquest;",
    );
    strtr($cadena,$traducciones);
}
/**
 * devuelve el número de palabras en un texto
 * @param String $str cadena de texto
 * @param Integer $conetiquetas determina si se consideran las etiquetas HTML
 * @return Integer
 */
function contar_palabras($str,$conetiquetas=0) {
    if(conetiquetas==1)
        $str=strip_tags($str);
    $reemplazar=array(",",".","-","+","(",")","{","}","_",";",":","  ");
    foreach ($reemplazar as $rr) {
        $str=str_replace($rr,"",$str);
    }
    return sizeof(explode(" ", $str));
}
/**
 * Limita el numero de palabras en un texto
 * @param string $end_char el caracter de escape para finalizar la cadena
 * cortada
 * @return String
 */
function limitar_palabras($str, $limit = 100, $end_char = '&#8230;') {

    if (trim($str) == '') {
        return $str;
    }

    preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

    if (strlen($str) == strlen($matches[0])) {
        $end_char = '';
    }

    return rtrim($matches[0]).$end_char;
}
/**
 * Lo mismo solo que limita en base a alas letras (siempre es usado)
 * @param <type> $str
 * @param <type> $n
 * @param <type> $end_char
 * @return <type>
 */
function limitar_letras($str, $n = 500, $end_char = '&#8230;') {
    if (strlen($str) < $n) {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

    if (strlen($str) <= $n) {
        return $str;
    }

    $out = "";
    foreach (explode(' ', trim($str)) as $val) {
        $out .= $val.' ';

        if (strlen($out) >= $n) {
            $out = trim($out);
            return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
        }
    }
}
/**
 * hace la paginacion de una consulta
 * @param Database $db la conexion a ala base de datos
 * @param <type> $sql la sentenia sql a la cual paginar
 * @param <type> $numresults numero de resultados por pagina
 * @param <type> $pag_atual ingresa y devuelve la pagina de la cual retornar los
 * resultados
 * @param <type> $numpags devuelve el numero de paginas en la paginacion
 * @return <type> retorna la paginacion
 */
function sacar_paginacion(Database $db,$sql,$numresults,&$pag_actual,&$numpags) {
    //saco el numero de resultados total
    $db->setQuery($sql);
    $numpags=ceil(count($db->loadObjectList())/$numresults);
    if($pag_actual>=$numpags)
        $pag_actual=$numpags;
    $lmin=($pag_actual-1)*$numresults;
    $lmax=$numresults;
    //saco los resultados de la paginacion
    $db->setQuery($sql." limit ".$lmin.",".$lmax);
    return $db->loadObjectList();
}
/**
 * Devuelve la fecha en formato texto
 */
function fechatexto($fecha,$delimiter="-") {
    $meses=array(
            "",
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Setiembre",
            "Octubre",
            "Noviembre",
            "Diciembre",
    );
    $ff=explode($delimiter, $fecha);
    $dia=$ff[2];
    $mes=$ff[1];
    $anio=$ff[0];
    $cad=str_pad($dia, 2, STR_PAD_LEFT)." de ".$meses." de ".$anio;
    return $cad;
}
/**
 * Convierte una cadena de texto a formato de url amigable
 */
function aurl($l) {
    return strtolower(str_replace(array("/"," ","--","á","é","í","ó","ú"),array("","-","-","a","e","i","o","u"),$l));
}
/**
 * devuelve el código HTML para poner algun recurso multimedia
 */
function get_media($f, $w=370, $h=450) {
    global $config_live_site;
    if ($f != "") {
        $ext = substr($f, strlen($f) - 3);
        switch ($ext) {
            case "mp3":
                $cad.='<script type="text/javascript">';
                $cad.='    runSWF2("' . $config_live_site . '/swf/dewplayer-mini.swf?mp3=' . $config_live_site . '/images/recursos/' . $f . '",160, 20, "9.0.0", "transparent")';
                $cad.='</script>';
                break;
            case "flv":
                $cad.='<script type="text/javascript">';
                $cad.='    runSWF2("' . $config_live_site . '/swf/player_adjunto.swf?file=' . $config_live_site . '/images/recursos/' . $f . '",' . $w . ', ' . $h . ', "9.0.0", "transparent")';
                $cad.='</script>';
                break;
            case "swf":
                $cad.='<script type="text/javascript">';
                $cad.='    runSWF2("' . $config_live_site . '/images/recursos/' . $f . '",' . $w . ', ' . $h . ', "9.0.0", "transparent")';
                $cad.='</script>';
                break;
            case "jpg":
            case "png":
            case "gif":
                $cad.='<img src="tumber.php?w=' . $w . '&h=' . $h . '&src=images/recursos/' . $f . '" />';
                break;
            default:
                $cad.='<a target="_blank" href="' . $config_live_site . '/images/recursos/' . $f . '">Click para descargar el archvo: ' . $f . '</a>';
                break;
        }
        echo '<p>' . $cad . '</p>';
    }
}
/**
 * divide la cadena en parte digeribles por el sistema
 */
function getParams(){
    $cad=array();
    if(isset($_SERVER["PATH_INFO"]))
        $cad=explode("/",$_SERVER["PATH_INFO"]);
    else{
        if(isset($_SERVER['QUERY_STRING'])!="")
            $cad=explode("/",$_SERVER["QUERY_STRING"]);
        else{
            if(isset($_SERVER['SCRIPT_NAME'])!="")
                $cad=explode("/",$_SERVER["SCRIPT_NAME"]);
        }
    }
    //eliminar blancos al inicio
    if(count($cad)>0){
        $i=0;
        while($i<count($cad) && $cad[$i]==""){
            unset($cad[0]);
            $i++;
        }
        $i=count($cad);
        while($i>=0 && $cad[$i]==""){
            unset($cad[$i]);
            $i--;
        }
    }
    if(count($cad)==0)
        $cad[]="index";
    return $cad;
}
/**
 *retorna la url completa 
 * @param string $relPath la ruta relativa (opcional)
 * @return string
 */
function appPath($relPath){
    global $config;
    return $config["site"]["path"].$relPath;
}
?>
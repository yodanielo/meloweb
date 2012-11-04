<?php
function index(){
    global $config;
    include "paginas/home.php";
}
function productos(){
    global $config;
    include "paginas/productos.php";
}
function servicios(){
    global $config;
    include "paginas/servicios.php";
}
function contacto(){
    global $config;
    include "paginas/contacto.php";
}
function contacto_ajax(){
    global $config;
    require("class.phpmailer.php");

    $mail = new PHPMailer(true);
    try{
        $body = file_get_contents(dirname(dirname(__FILE__))."/paginas/email.php");
        $body=  str_replace("{nombre}", htmlspecialchars($_POST["txt1"]), $body);
        $body=  str_replace("{telefono}", htmlspecialchars($_POST["txt2"]), $body);
        $body=  str_replace("{correo}", htmlspecialchars($_POST["txt3"]), $body);
        $body=  str_replace("{mensaje}", htmlspecialchars($_POST["txt4"]), $body);
        
        //$mail->AddReplyTo('name@yourdomain.com', 'First Last');
        $mail->AddAddress($config["mail"]["to"]);
        $mail->SetFrom($config["mail"]["from"], "Optica Impact Vision");
        //$mail->AddReplyTo('name@yourdomain.com', 'First Last');
        $mail->Subject = "Formulario de Contacto";
        $mail->AltBody = 'Para ver éste mensaje, por favor use un cliente de correo compatible con HTML';
        $mail->MsgHTML(utf8_decode($body));
        $mail->Send();
        echo "Mensaje enviado";
    }catch(phpmailerException $e){
        echo $e->errorMessage();
    }catch (Exception $e){
        echo $e->getMessage();
    }
}
function fbhome(){
    global $config;
    include "paginas/fbhome.php";
}
?>
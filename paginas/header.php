<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?=$config["site"]["name"]?></title>
        <link rel="stylesheet" type="text/css" href="<?=appPath("")?>/css/nav.css" media="screen"/>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    </head>
    <body>
        <header>
            <div class="contentcuerpo">
                <ul id="barrablanca">
                    <li class="menu-separador"></li>
                    <li class="menu-link"><a href="<?=appPath("")?>">INICIO</a></li>
                    <li class="menu-separador"></li>
                    <li class="menu-link"><a href="<?=appPath("productos")?>">PRODUCTOS</a></li>
                    <li class="menu-separador"></li>
                    <li class="menu-link"><a href="<?=appPath("servicios")?>">SERVICIOS</a></li>
                    <li class="menu-separador"></li>
                    <li class="menu-link"><a href="<?=appPath("contacto")?>">CONTACTO</a></li>
                    <li class="menu-separador"></li>
                    <!--<li class="menu-redes"><a href="#" target="_blank" class="link-facebook"></a><a href="<?=appPath("")?>/contacto" class="link-telefono"></a></li>-->
                </ul>
                <a href="<?=appPath("")?>"><img id="logo" alt="Impacto Visión" src="<?=appPath("")?>/images/logotipo-optica-impacto-vision.png"/></a>
            </div>
        </header>
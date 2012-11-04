<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?=$config["site"]["name"]?></title>
        <link rel="stylesheet" type="text/css" href="<?=$config["site"]["path"]?>/css/nav.css" media="screen"/>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript" src="<?=  appPath("js/jquery.cycle.all.js")?>"></script>
    </head>
    <body>
        <div id="bodyfb">
            <header>
                <div class="contentcuerpo">
                    <ul id="barrablanca">
                        <li class="menu-link"><a target="_blank" href="<?=$config["site"]["path"]?>/">Visita Nuestra Página Web</a></li>
                    </ul>
                    <a href="<?=$config["site"]["path"]?>"><img id="logo" alt="Impacto Visión" src="<?=$config["site"]["path"]?>/images/logotipo-optica-impacto-vision.png"/></a>
                </div>
            </header>
            <article>
                <div class="lancol1">
                    <div class="lanimg"><img src="<?=  appPath("images/fbimagen1-optica-impacto-vision.jpg")?>" alt=""/></div>
                    <div class="lanimg"><img src="<?=  appPath("images/fbimagen2-optica-impacto-vision.jpg")?>" alt=""/></div>
                    <div class="lanimg"><img src="<?=  appPath("images/fbimagen3-optica-impacto-vision.jpg")?>" alt=""/></div>
                </div>
                <div class="lancol2">
                    <div class="lancol2_content">
                        <p>Somos una empresa dedicada al cuidado y educación de la salud visual en las empresas e Instituciones Educativas, realizando exámenes visuales completos. Se incluyen pruebas de agudeza visual, sensibilidad al contraste, evaluación de los movimientos oculomotores durante la lectura, examen de la visión binocular, refracción y entrenamiento visual.</p>
                        <p>
                            <a class="icolan" href="#"><img src="<?=  appPath("images/fono-optica-impacto-vision.png")?>" alt=""/><?=$config["site"]["telefono"]?></a>
                            <a class="icolan" target="_blank" href="<?=  appPath("contacto")?>"><img src="<?=  appPath("images/correo-optica-impacto-vision.png")?>" alt=""/>Escríbenos</a>
                        </p>
                    </div>
                </div>
            </article>
        </div>
        <script type="text/javascript">
            $(function(){
                $(".lancol1").cycle({
                    speed:    2000, 
                    timeout:  5000 
                });
            });
        </script>
    </body>
</html>
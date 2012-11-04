        <footer>
            <div id="footriangulo">
                <img id="footertriangulonegro" src="<?=appPath("images/triangulo-negro-optica-impacto-vision.png")?>" alt=""/>
            </div>
            <div id="slogan">AL CUIDADO DE TU SALUD VISUAL</div>
            <address>Av. los constructores Nº 2021 - Miraflores - Lima - Peru<br/>
                Telf: <?=$config["site"]["telefono"]?>
            </address>
        </footer>
<script type="text/javascript">
    $(function(){
        resizeWindow=function(){
            $("footer").css("margin-top",$("footer #footriangulo img").height()*-1-10);
        }
        $(window).resize(resizeWindow);
        $("#footriangulo img").load(resizeWindow);
    })
</script>
    </body>
</html>
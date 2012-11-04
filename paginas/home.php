<?php
include 'header.php';
?>
<article class="contentcuerpo">
    <div id="visor">
        <img class="homefondoplomo" alt="" src="<?=appPath("images/fondo-plomo-optica-impacto-vision.png")?>"/>
        <div class="imgscroll">
            <img id="imgscroll1" alt="" src="<?=appPath("images/portada-fondo1-optica-impacto-vision.png")?>" />
            <div class="homebarranaranja">AL CUIDADO DE TU SALUD VISUAL</div>
        </div>
        <div class="imgscroll">
            <img id="imgscroll2" alt="" src="<?=appPath("images/portada-fondo2-optica-impacto-vision.png")?>" />
            <div class="homebarranaranja">EXPERIENCIA Y PROFESIONALISMO</div>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){
            sliders=$(".imgscroll");
            i=0;
            velo=1500;
            //creando bolas
            cad='';
            cad+='<div id="bolascont"><div id="bolasvisor">';
            $(sliders).each(function(i,obj){
                cad+='<div class="bola" id="bola'+i+'"></div>'
            })
            cad+='</div></div>'
            $("#footriangulo").append(cad);
            pasar=function(){
                $(".bola").removeClass("active");
                $(".bola:eq("+i+")").addClass("active");
                sliders.stop().fadeOut(velo,function(){})
                $(sliders[i]).fadeIn(velo,function(){})
                i++;
                if(i==sliders.length)
                    i=0
            }
            tt=setInterval(pasar,1000*7);
            pasar()
            $(".bola").click(function(){
                window.clearInterval(tt);
                i=$(this).attr("id").split("bola").join("");
                pasar()
                tt=setInterval(pasar,1000*7);
            })
        })
    </script>
</article>
<?php
include 'footer.php';
?>

<?php
include 'header.php';
?>
<article class="contentcuerpo contentcontacto">
    <h1>CONTACTO</h1>
    <div id="col1contacto">
        <div><img src="<?=  appPath("images/fono-optica-impacto-vision.png")?>" alt=""/><span><?=$config["site"]["telefono"]?></span></div>
        <div><img src="<?=  appPath("images/facebook-optica-impacto-vision.png")?>" alt=""/><span>Siguenos</span></div>
    </div>
    <div id="col2contacto">
        <div id="innercol2">
        <form id="frmalgo" method="post" action="#">
            <div class="filastrap">
                <label for="">Nombres</label>
                <div><input type="text" id="txt1" /></div>
            </div>
            <div class="filastrap">
                <label for="">Telefono</label>
                <div><input type="text" id="txt2" /></div>
            </div>
            <div class="filastrap">
                <label for="">Correo electrónico</label>
                <div><input type="text" id="txt3" /></div>
            </div>
            <div class="filastrap">
                <label for="">Comentarios</label>
                <div><textarea id="txt4"></textarea></div>
            </div>
            <div class="filastrap">
                <input type="submit" id="btnsubmit" value="Enviar"/>
                <input type="button" id="btncancelar" value="Cancelar"/>
            </div>
        </form>
        </div>
    </div>
</article>
<script type="text/javascript">
    $(function(){
        $("#frmalgo").submit(function(){
            if($("#txt1").val()!="" && ($("#txt2").val()!="" || $("#txt3").val()!="") && $("#txt4").val()!=""){
                $.ajax({
                    url:"<?=  appPath("contacto_ajax")?>",
                    data:"txt1="+encodeURIComponent($("#txt1").val())+"&txt2="+encodeURIComponent($("#txt2").val())+"&txt3="+encodeURIComponent($("#txt3").val())+"&txt4="+encodeURIComponent($("#txt4").val()),
                    type:"post",
                    success:function(data){
                        alert(data);
                        $("#txt1,#txt2,#txt3,#txt4").val("");
                    }
                });
            }
            else{
                if($("#txt1").val()==""){
                    alert("Debe ingresar su nombre");
                }
                else{
                    if($("#txt2").val()=="" || $("#txt3").val()==""){
                        alert("Debe ingresar al menos un telefono o correo electrónico");
                    }
                    else{
                        alert("Debe ingresar un mensaje")
                    }
                }
            }
            return false;
        });
    });
</script>
<?php
include 'footer.php';
?>

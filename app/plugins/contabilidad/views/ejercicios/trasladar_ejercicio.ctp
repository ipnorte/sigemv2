<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'TRASLADAR EJERCICIO'));
// debug($ejercicio);       
// debug($aPlanCtaVig);
//        debug($ejercicio);
//        debug($ejerPos);
?>
<script type="text/javascript">
Event.observe(window, 'load', function() {
            $('procesando').hide();
});

function validateForm(){
    $('procesando').show();
    $('formulario').hide();
}


</script>
<?php
if($cancela == 1){?>
    <h1>
        <br>
        <br>
        <FONT SIZE="+2">
            <marquee behavior="alternate">
                EL PROCESO FUE CANCELADO.
            </marquee>
        </FONT>
    </h1>
<?php
}else{
    if($proceso === 1){
        if($exito === 0){?>
            <h1>
                <br>
                <br>
                <FONT SIZE="+2">
                    <marquee behavior="alternate">
                        EL PROCESO NO TERMINO CORRECTAMETE.
                    </marquee>
                </FONT>
            </h1>
        <?php
        }else{?>
            <h1>
                <br>
                <br>
                <FONT SIZE="+2">
                    <marquee behavior="alternate">
                        EL PROCESO TERMINO CON EXITO.
                    </marquee>
                </FONT>
            </h1>
    <?php
    }}else{
        if(empty($existeFinal)){
            ?>
            <h1>
                <br>
                <br>
                NO SE PUEDE REALIZAR EL TRASLADO DE EJERCICIO.
                <br>
                <br>
                    FALTA EL ASIENTO FINAL DE CIERRE.
            </h1>
        <?php }else{
            if(empty($ejerPos)){
            ?>
                <h1>
                    <br>
                    <br>
                    NO SE PUEDE REALIZAR EL TRASLADO DE EJERCICIO.
                    <br>
                    <br>
                    NO EXISTE EL NUEVO EJERCICIO.
                </h1>

            <?php }else{
                        if(empty($aPlanCtaPos)){
                        ?>
                            <h1>
                                <br>
                                <br>
                                NO SE PUEDE REALIZAR EL TRASLADO DE EJERCICIO.
                                <br>
                                <br>
                                FALTA EL PLAN DE CUENTA DEL NUEVO EJERCICIO.
                            </h1>

                        <?php }else{?>
                        <div class="areaDatoForm">
                            <table>
                                <tr>
                                    <td><FONT SIZE="+2">SE TRASLADARA</FONT></td>
                                </tr>
                                <tr>
                                    <td><FONT SIZE="+2"><?php echo $ejercicio['descripcion']?></FONT></td>
                                </tr>
                                <tr>
                                    <td><FONT SIZE="+2">HACIA</FONT></td>
                                </tr>
                                <tr>
                                    <td><FONT SIZE="+2"><?php echo $ejerPos['descripcion']?></FONT></td>
                                </tr>
                            </table>
                        </div>
                        <div id="procesando">
                            <h1>
                                <br>
                                <br>
                                <FONT SIZE="+2"><marquee>ESPERE A QUE EL PROCESO TERMINE.</marquee></FONT>
                            </h1>
                        </div>    


                        <div id="formulario">
                            <?php echo $form->create(null,array('name'=>'trasEjercicio','id'=>'trasEjercicio','onsubmit' => "return validateForm();",'action' => 'trasladar_ejercicio' ));?>
                            <?php echo $frm->hidden('Ejercicio.co_ejercicio_id', array('value' => $ejercicio['id'])) ?>
                            <?php echo $frm->hidden('Ejercicio.pos_ejercicio_id', array('value' => $ejerPos['id'])) ?>
                            <?php echo $frm->hidden('Ejercicio.fecha_desde', array('value' => $ejerPos['fecha_desde'])) ?>
                            <?php // echo $frm->hidden("Asiento.uuid", array('value' => $uuid)) ?>
                            <?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'TRASLADAR EJERCICIO','URL' => '/contabilidad/ejercicios/trasladar_ejercicio/1'))?>                    
                        </div>
                        <?php }
            }
    // $aPlanCtaPos


        }
    }
}?>

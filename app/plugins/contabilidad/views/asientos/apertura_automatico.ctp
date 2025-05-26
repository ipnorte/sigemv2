<?php 

?>
<style type="text/css">

	input:focus{
	   background-color: #8AAEC6;
	}

</style>

<script language="Javascript" type="text/javascript">

    Event.observe(window, 'load', function() {

	$('AsientoReferencia').select();
	$('AsientoReferencia').focus();
    });
</script>

<h2>ASIENTO DE APERTURA</h2>
<hr>
<?php if(empty($asientoApertura)){?>
    <h3>PARA GENERAR EL ASIENTO DE APERTURA</h3>
    <h3>DEBE EXISTIR EL ASIENTO CIERRE O FINAL DEL EJERCICIO ANTERIOR</h3>
    <?php echo $controles->btnRew('Regresar','/contabilidad/asientos/asiento_apertura/'.$ejercicio['id']);
}else {
    
    echo $form->create(null,array('name'=>'formAsiento','id'=>'formAsiento', 'action' => 'apertura_automatico/' . $ejercicio['id'] ));
    ?>

    <div class="areaDatoForm">

            <table align="center" width="100%">

                    <col width="100" />
                    <col width="50" />
                    <col width="450" />
                    <col width="100" />
                    <col width="100" />
                    <col width="100" />

                    <tr>
                            <th style="font-size: small;">FECHA</th>
                            <th colspan="2" style="font-size: small;">DESCRIPCION</th>
                            <th style="font-size: small;">REFERENCIA</th>
                            <th style="font-size: small;">DEBE</th>
                            <th style="font-size: small;">HABER</th>
                    </tr>

                    <?php
                            $fechaPrimera = true; 
                            $nDebe = 0;
                            $nHaber = 0;

                            foreach($asientoApertura['renglones'] as $renglon){
                                if($renglon['AsientoRenglon']['haber'] > 0){
                                    $nDebe += $renglon['AsientoRenglon']['haber'];
                                    ?>
                                    <tr>
                                        <?php if($fechaPrimera):
                                                $fechaPrimera = false;?>

                                                <td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($ejercicio['fecha_desde']))?></td>
                                        <?php else:?>
                                                <td style="border-left: 1px solid black;font-size: small;"></td>
                                        <?php endif;?>

                                        <td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['descripcion_cuenta']?></td>
                                        <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['codigo_cuenta']?></td>
                                        <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['haber'],2)?></td>
                                        <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                    </tr>
                    <?php
                            }}?>

                    <?php
                            foreach($asientoApertura['renglones'] as $renglon){
                                if($renglon['AsientoRenglon']['debe'] > 0){
                                    $nHaber += $renglon['AsientoRenglon']['debe'];
                                    ?>
                                    <tr>
                                        <td style="border-left: 1px solid black;font-size: small;"></td>
                                        <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                        <td style="font-size: small;"><?php echo $renglon['AsientoRenglon']['descripcion_cuenta']?></td>
                                        <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['codigo_cuenta']?></td>
                                        <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                        <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['debe'],2)?></td>
                                    </tr>
                    <?php
                                }}?>

                    <tr>
                            <td style="border-left: 1px solid black;font-size: small;">REFERENCIA:</td>
                            <td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $frm->input('Asiento.referencia',array('label'=>'', 'value'=>'APERTURA DE EJERCICIO','size'=>60, 'maxlenght'=>100)) ?></td>
                            <td style="border-left: 1px solid black;font-size: small;"></td>
                            <td style="border-left: 1px solid black;font-size: small;"></td>
                            <td style="border-left: 1px solid black;font-size: small;"></td>
                    </tr>	
                    <tr>
                            <td style="border-left: 1px solid black;font-size: small;"></td>
                            <td colspan="2" style="border-left: 1px solid black;font-size: small;"></td>
                            <td style="border-left: 1px solid black;font-size: small;"></td>
                            <td style="border-left: 1px solid black;font-size: small;"></td>
                            <td style="border-left: 1px solid black;font-size: small;"></td>
                    </tr>	
                    <tr>
                            <td style="border-left: 1px solid black;font-size: small;"></td>
                            <td colspan="2" style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
                            <td style="border-left: 1px solid black;border-bottom: 1px solid black;font-size: small;"></td>
                            <td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($nDebe,2)?></td>
                            <td align="right" style="border: 1px solid black;font-size: small;"><?php echo number_format($nHaber,2)?></td>
                    </tr>	
            </table>


    </div>
    <?php
        echo $frm->hidden('Asiento.co_ejercicio_id', array('value' => $ejercicio['id']));
        echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR ASIENTO APERTURA','URL' => '/contabilidad/asientos/asiento_apertura/'.$ejercicio['id']));
}?>

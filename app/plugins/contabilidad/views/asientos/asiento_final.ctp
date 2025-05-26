<?php 
// App::import('Model', 'contabilidad.AsientoRenglon');
// App::import('Model', 'contabilidad.PlanCuenta');

// $oAsientoRenglon = new AsientoRenglon();
// $oPlanCuenta = new PlanCuenta();

// debug($aDebe);
// debug($aResultado);
// debug($aHaber);


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

<h2>ASIENTO FINAL</h2>
<hr>
<?php if($nAsientoResultado === 0){?>
    <h3>PARA GENERAR EL ASIENTO DE CIERRE O FINAL</h3>
    <h3>DEBE EXISTIR EL ASIENTO DE RESULTADO</h3>
    <?php echo $controles->btnRew('Regresar','/contabilidad/asientos/cierre_ejercicio/'.$ejercicio['id']);
}else {?>

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

                            foreach($aDebe as $renglon):
                                $nDebe += $renglon['PlanCuenta']['importe'];
                                ?>
                                <tr>
                                    <?php if($fechaPrimera):
                                            $fechaPrimera = false;?>

                                            <td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($ejercicio['fecha_hasta']))?></td>
                                    <?php else:?>
                                            <td style="border-left: 1px solid black;font-size: small;"></td>
                                    <?php endif;?>

                                    <td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['PlanCuenta']['descripcion']?></td>
                                    <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['PlanCuenta']['codigo_cuenta']?></td>
                                    <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['PlanCuenta']['importe'],2)?></td>
                                    <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                </tr>
                    <?php
                            endforeach;?>

                    <?php
                            foreach($aHaber as $renglon):
                                $nHaber += $renglon['PlanCuenta']['importe'];
                                ?>
                                <tr>
                                    <td style="border-left: 1px solid black;font-size: small;"></td>
                                    <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                    <td style="font-size: small;"><?php echo $renglon['PlanCuenta']['descripcion']?></td>
                                    <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['PlanCuenta']['codigo_cuenta']?></td>
                                    <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                    <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['PlanCuenta']['importe'],2)?></td>
                                </tr>
                    <?php
                            endforeach;?>

                    <tr>
                            <td style="border-left: 1px solid black;font-size: small;">REFERENCIA:</td>
                            <td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $frm->input('Asiento.referencia',array('label'=>'', 'value'=>'RECLASIFICACION FINAL','size'=>60, 'maxlenght'=>100)) ?></td>
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
    <?php if($ejercicio['activo'] == 1){
        echo $form->create(null,array('name'=>'formAsiento','id'=>'formAsiento', 'action' => 'asiento_final/' . $ejercicio['id'] ));
        echo $frm->hidden('Asiento.co_ejercicio_id', array('value' => $ejercicio['id']));
        echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR ASIENTO FINAL','URL' => '/contabilidad/asientos/cierre_ejercicio/'.$ejercicio['id']));
    }else{ ?>
        <h3>EL EJERCICO ESTA CERRADO</h3>
        <?php echo $controles->btnRew('Regresar','/contabilidad/asientos/cierre_ejercicio/'.$ejercicio['id']);

    }
}?>

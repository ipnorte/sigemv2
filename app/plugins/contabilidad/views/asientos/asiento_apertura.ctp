<?php
$fecha = (!empty($fecha_hasta) ? $fecha_hasta : $fecha_desde);
 
?>

<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'ASIENTOS'))?>
<div id="FormSearch">
<?php echo $form->create(null,array('name'=>'frmAsientos','id'=>'frmAsientos', 'action' => 'index'));?>
    <table>
        <tr>
            <td><?php echo $this->renderElement('combo_ejercicio',array(
                                                'plugin'=>'contabilidad',
                                                'label' => " ",
                                                'model' => 'ejercicio.id',
                                                'disabled' => false,
                                                'empty' => false,
                                                'selected' => $ejercicio['id']))?>
            </td>			
            <td><?php echo $frm->submit('SELECCIONAR',array('class' => 'btn_consultar'));?></td>
        </tr>
    </table>
<?php echo $frm->end();?> 
</div>
<?php if(!empty($ejercicio)){
    echo $this->renderElement('opciones_asiento',array(
                                    'plugin'=>'contabilidad',
                                    'label' => " ",
                                    'model' => 'asiento.id',
                                    'disabled' => false,
                                    'empty' => false
    ));

    echo $this->renderElement('head',array('plugin' => 'config','title' => 'ASIENTO DE APERTURA'));

    if($asientoApertura){ ?>
        <h3>EXISTE EL ASIENTO DE APERTURA.</h3>
        

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

                                foreach($asientoApertura['renglones'] as $renglon):
                                    $nDebe += $renglon['AsientoRenglon']['debe'];
                                    $nHaber += $renglon['AsientoRenglon']['haber'];
                                    ?>
                                    <tr>
                                        <?php if($fechaPrimera):
                                                $fechaPrimera = false;?>

                                                <td align="center" style="border-left: 1px solid black;font-size: small;"><?php echo date('d/m/Y',strtotime($asientoApertura['Asiento']['fecha']))?></td>
                                        <?php else:?>
                                                <td style="border-left: 1px solid black;font-size: small;"></td>
                                        <?php endif;

                                        if($renglon['AsientoRenglon']['debe'] > 0){?>

                                            <td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['descripcion_cuenta']?></td>
                                            <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['codigo_cuenta']?></td>
                                            <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['debe'],2)?></td>
                                            <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                        <?php }else{?>
        <!--                                    <td style="border-left: 1px solid black;font-size: small;"></td> -->
                                            <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                            <td style="font-size: small;"><?php echo $renglon['AsientoRenglon']['descripcion_cuenta']?></td>
                                            <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo $renglon['AsientoRenglon']['codigo_cuenta']?></td>
                                            <td align="right" style="border-left: 1px solid black;font-size: small;"></td>
                                            <td align="right" style="border-left: 1px solid black;font-size: small;"><?php echo number_format($renglon['AsientoRenglon']['haber'],2)?></td>
                                        <?php }?>
                                    </tr>
                        <?php
                                endforeach;?>

                        <tr>
                                <td style="border-left: 1px solid black;font-size: small;">REFERENCIA:</td>
                                <td colspan="2" style="border-left: 1px solid black;font-size: small;"><?php echo $asientoApertura['Asiento']['referencia'] ?></td>
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
        <?php /* if($ejercicio['activo'] == 1){
            echo $form->create(null,array('name'=>'formAsiento','id'=>'formAsiento', 'action' => 'eliminar_apertura/' . $ejercicio['id'] ));
            echo $frm->hidden('Asiento.co_ejercicio_id', array('value' => $ejercicio['id']));
            echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ELIMINAR ASIENTO APERTURA','URL' => '/contabilidad/asientos/index/'.$ejercicio['id']));
        
        }else{ */?>
            <!-- h3>EL EJERCICO ESTA CERRADO</h3 -->
            
        <?php //}
    }else{
        ?>
            <script language="Javascript" type="text/javascript">

                Event.observe(window, 'load', function(){

                });

                function CtrlFecha(){
                    var fecha_cierre  = $('AsientoCierreFechaHastaYear').getValue() + '-' + $('AsientoCierreFechaHastaMonth').getValue() + '-' + $('AsientoCierreFechaHastaDay').getValue();
                    var fecha_desde = '<?php echo date('d/m/Y',strtotime($fecha_desde))?>';

                    if(fecha_cierre < '<?php echo $fecha_desde?>')
                    {
                        alert('LA FECHA DEBE SER MAYOR AL ULTIMO CIERRE: ' + fecha_desde);
                        $('AsientoCierreFechaHastaDay').focus();
                        return false;
                    }

                    return true;
                }



            </script>

            <?php echo $this->renderElement('opciones_apertura',array(
                                            'plugin'=>'contabilidad',
                                            'label' => " ",
                                            'model' => 'asiento.id',
                                            'disabled' => false,
                                            'empty' => false
            ));
    }

}?>
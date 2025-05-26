<?php echo $this->renderElement('head',array('title' => 'CUOTAS :: CAMBIO DE SITUACION','plugin' => 'config'))?>
<?php echo $this->renderElement('orden_descuento/form_search_by_numero',array('accion' => 'cambiar_situacion','plugin' => 'mutual'))?>

<?php if(!empty($orden_descuento_id)):?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => true,'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('orden_descuento/resumen_by_id',array('id' => $orden_descuento_id,'detallaCuotas' => false,'plugin' => 'mutual'))?>


<?php if(!empty($cuotas)):?>
<script type="text/javascript">
var rows = <?php echo count($cuotas)?>;
function selUnSelAll(status){
    var cbox;
    for (var i = 0; i < rows; i++){
        cbox = document.getElementById('CHK_' + i);
        if(cbox !== null){
            cbox.checked = status;
            toggleCell('LTR_' + i, cbox);
        }
    }
}
</script>
    <h3 style="margin:2px;">CUOTAS ADEUDADAS</h3>
    <?php echo $frm->create(null,array('action' => 'cambiar_situacion','onsubmit' => "return confirm('Cambiar situación de las cuotas seleccionadas?')"))?>

        <table>
            <tr>
                <th>TIPO / NUMERO</th>
                <th>PERIODO</th>
                <th>PROVEEDOR - PRODUCTO</th>
                <th>CUOTA</th>
                <th>CONCEPTO</th>
                <th>ESTADO</th>
                <th>SITUACION</th>
                <th>VENCIMIENTO</th>
                <th>IMPORTE</th>
                <th>SALDO</th>
                <th></th>
            </tr>
            <?php $i = 0; ?>
            <?php foreach($cuotas as $cuota):?>
                <?php 
                $bloqueo = array();
                if(!empty($cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'])) $bloqueo = $cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'];
                ?>
                <tr id="LTR_<?php echo $i?>" class="<?php echo $cuota['OrdenDescuentoCuota']['estado']?>">
                    <td align="center"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
                    <td><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></td>
                    <td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
                    <td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
                    <td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
                    <td align="center"><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></td>
                    <td align="center"><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></td>
                    <td align="center"><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></td>
                    <td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['importe'])?></td>
                    <td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['saldo_cuota'])?></td>
                    <td>
                        <?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>
                            <span style="color: red;"><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></span>
                        <?php elseif($cuota['OrdenDescuentoCuota']['saldo_cuota'] > 0 || $cuota['OrdenDescuentoCuota']['estado'] == 'B'):?>							
                            <input type="checkbox" id="CHK_<?php echo $i?>" name="data[OrdenDescuentoCuota][check_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="1" onclick="toggleCell('LTR_<?php echo $cuota['OrdenDescuentoCuota']['id']?>', this)"/>
                        <?php endif;?>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php endforeach;?>
                <tr>
                    <th style="background-color: gray;"><input type="button" value="Marcar Todo" onclick="selUnSelAll(true)"></th>
                    <th style="background-color: gray;"><input type="button" value="Limpiar" onclick="selUnSelAll(false)"></th>
                    <th colspan="9" style="background-color: gray;"></th>
                </tr>                  
        </table>    
        <div class="areaDatoForm2">
				
            <table class="tbl_form">
                <tr>
                    <td>NUEVA SITUACION</td>
                    <td>
                        <?php echo $this->renderElement('global_datos/combo',array(
                                                                                            'plugin'=>'config',
                                                                                            'label' => '.',
                                                                                            'model' => 'OrdenDescuentoCuota.situacion',
                                                                                            'prefijo' => 'MUTUSICU',
                                                                                            'disable' => false,
                                                                                            'empty' => false,
                                                                                            'selected' => '0',
                                                                                            'logico' => true,
                        ))?>				
                    </td>
                </tr>
                <tr>
                    <td valign="top">OBSERVACIONES</td>
                    <td><?php echo $frm->textarea('observaciones',array('cols' => 60, 'rows' => 10))?></td>
                </tr>
                <tr>
                    <td colspan="2"><?php echo $frm->submit('CAMBIAR SITUACION DE CUOTAS ORDEN #' . $orden_descuento_id)?></td>
                </tr>
            </table>
            <?php echo $frm->hidden('OrdenDescuento.aprox_id',array('value' => $orden_descuento_id))?>
            <?php echo $frm->end();?>
				
		</div>
    


<?php else:?>
<div class="notices">La Orden de Descuento #<?php echo $orden_descuento_id?> <strong>NO TIENE CUOTAS ADEUDADAS</strong></div>
<?php endif; ?>

<?php endif; ?>
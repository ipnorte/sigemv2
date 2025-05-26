<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona));?>
<?php echo $this->requestAction('/pfyj/socios/view/'.$socio['Socio']['id'])?>

<?php echo $form->create(null,array('name'=>'formGeneraCuotaSocial','id'=>'formGeneraCuotaSocial','onsubmit' => "return confirm('Generar Cuotas y Orden de Cobro por Caja?')",'action' => 'cobro_cuota_adelantada/'. $socio['Socio']['id']));?>
    <?php echo $this->renderElement('orden_descuento/resumen_by_id',array('id'=>$ordenDto['OrdenDescuento']['id'],'detallaCuotas'=>false,'plugin' => 'mutual')) ?>
   <h3>GENERAR COBRO DE CUOTAS SOCIALES POR ADELANTADO</h3>
   
   <script language="Javascript" type="text/javascript">
   
	var rows = <?php echo count($cuotas)?>;
	
	Event.observe(window, 'load', function() {
            $('btn_genera_orden').disable();
            checkOnClick();
	});
        
        function checkOnClick(){
            var totalSeleccionado = 0;
            var ret = true;
            for (i=0;i<rows;i++) {

                var oChkCheck = document.getElementById('OrdenDescuentoCuotaPeriodo_' + i);
                var otxtImpoPago = document.getElementById('OrdenDescuentoCuotaPeriodoImporte_' + i);
                toggleCell('TRL_' + i, oChkCheck);
                valueCheck = parseInt(oChkCheck.value);
                impoPago = new Number(otxtImpoPago.value);
                impoPago = impoPago.toFixed(2);
                impoPago100 = impoPago * 100;
                impoPago100 = impoPago100.toFixed(0);
                
                if(parseInt(impoPago100) === 0 && oChkCheck.checked){
                    alert("EL IMPORTE DEL PAGO " + otxtImpoPago.value + " NO PUEDE SER CERO!");
                    oChkCheck.checked = false;
                    toggleCell('TRL_' + i,oChkCheck);
                    otxtImpoPago.focus();
                    ret = false;	 		
                }
                if (oChkCheck.checked){
                    impoPago100 = parseInt(impoPago100);
                    totalSeleccionado = totalSeleccionado + impoPago100;
                }                
            }
            if(totalSeleccionado >= 0)$('btn_genera_orden').enable();
            else $('btn_genera_orden').disable();
            totalSeleccionado = totalSeleccionado/100;
            totalSeleccionado = FormatCurrency(totalSeleccionado);
            $('total_seleccionado').update(totalSeleccionado);
            return ret;            
        }
   
   </script>
   
    <table>
        <tr>
            <th>Periodo</th>
            <th>TipoOrden</th>
            <th>Producto / Concepto</th>
            <th>Cuota</th>
            <th>Importe</th>
            <th></th>
        </tr>
    <?php 
        if(!empty($cuotas)):
            $i = 0;
    ?>
        <?php foreach($cuotas as $cuota):?>
            <tr id="TRL_<?php echo $i?>">
                <td><strong><?php echo $util->periodo($cuota['periodo'])?></strong></td>
                <td><?php echo $cuota['tipo_numero']?></td>
                <td><?php echo $util->globalDato($cuota['tipo_producto'])?> / <?php echo $util->globalDato($cuota['tipo_cuota'])?></td>
                <td style="text-align: center;"><?php echo $cuota['nro_cuota']?></td>
                <td><input type="text" name="data[OrdenDescuentoCuota][periodo_importe][<?php echo $cuota['periodo']?>]" id="OrdenDescuentoCuotaPeriodoImporte_<?php echo $i?>" value="<?php echo number_format($cuota['importe'],2,".","")?>" class="input_number" onkeypress = "return soloNumeros(event,true)" size="12" maxlength="12"/></td>
                <td><input type="checkbox" name="data[OrdenDescuentoCuota][periodo][<?php echo $cuota['periodo']?>]" id="OrdenDescuentoCuotaPeriodo_<?php echo $i?>" value="<?php echo number_format(round($cuota['importe'],2) * 100,0,".","")?>" onclick="checkOnClick()"/></td>
            </tr>
            <?php $i++;?>
        <?php endforeach;?>
            <tr class="totales">
                <th colspan="4">TOTAL</th>
                <th><span id="total_seleccionado">0.00</span></th>
                <th></th>
            </tr>
    <?php endif;?>
    </table>
    <?php // debug($cuotas)?>
<?php echo $frm->hidden('OrdenDescuentoCuota.socio_id',array('value' => $socio['Socio']['id'])); ?>
<?php echo $frm->hidden('OrdenDescuentoCuota.importe',array('value' => $importeCuotaSocial)); ?>
<?php echo $frm->hidden('OrdenDescuentoCuota.tipo_orden_dto',array('value' => $ordenDto['OrdenDescuento']['tipo_orden_dto'])); ?>
<?php echo $frm->hidden('OrdenDescuentoCuota.orden_descuento_id',array('value' => $ordenDto['OrdenDescuento']['id'])); ?>
<?php echo $frm->hidden('OrdenDescuentoCuota.proveedor_id',array('value' => $ordenDto['OrdenDescuento']['proveedor_id'])); ?>
<?php echo $frm->hidden('OrdenDescuentoCuota.nro_cuota',array('value' => 0)); ?>
<?php echo $frm->hidden('OrdenDescuentoCuota.persona_beneficio_id',array('value' => $ordenDto['OrdenDescuento']['persona_beneficio_id'])); ?>
<?php echo $frm->hidden('OrdenDescuentoCuota.tipo_producto',array('value' => $ordenDto['OrdenDescuento']['tipo_producto'])); ?>
<?php echo $frm->hidden('OrdenDescuentoCuota.tipo_cuota',array('value' => $util->globalDato($ordenDto['OrdenDescuento']['tipo_producto'], 'concepto_2'))); ?>

<?php if(!empty($cuotas)) echo $frm->btnGuardarCancelar(array('ID_GUARDAR' => 'btn_genera_orden','TXT_GUARDAR' => 'GENERAR ORDEN DE COBRO','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$socio['Persona']['id'] : $fwrd) ))?>


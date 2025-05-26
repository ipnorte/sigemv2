<h3>DETALLE DE CUOTAS</h3>

<script language="Javascript" type="text/javascript">
var rows = <?php echo count($cuotas)?>;

Event.observe(window, 'load', function() {
		$('btn_genera_orden').disable();
		selUnSelAll(false,false);
		$('orden_cobro_caja_generada').hide();
});

function selUnSelAll(status,soloVencida){
	for (i=1;i<=rows;i++){
		oChkCheck = document.getElementById('OrdenCajaCobroCuotaOrdenDescuentoCuotaId_' + i);
		oChkCheck.checked = false;
		if(soloVencida){
			vencida = document.getElementById('OrdenCajaCobroCuotaOrdenDescuentoCuotaVencida_' + i).value;
			if(vencida === 1)oChkCheck.checked = status;
		}else{
			oChkCheck.checked = status;
		}	
	}
	SelSum();
}

function chkOnclick(){
	  SelSum();
}

function SelSum(){
	
	var totalSeleccionado = 0;
	
	for (i=1;i<=rows;i++){
		var celdas = $('TRL_' + i).immediateDescendants();
		oChkCheck = document.getElementById('OrdenCajaCobroCuotaOrdenDescuentoCuotaId_' + i);
		if (oChkCheck.checked){
			totalSeleccionado = totalSeleccionado + parseInt(oChkCheck.value);
			celdas.each(function(td){td.addClassName("selected");});
		}else{
			celdas.each(function(td){td.removeClassName("selected");});
		}	
	}
	totalSeleccionado = FormatCurrency(totalSeleccionado/100);
	document.getElementById('OrdenCajaCobroImporte').value = totalSeleccionado;
	if(totalSeleccionado > 0)$('btn_genera_orden').enable();
	else $('btn_genera_orden').disable();
	$('total_seleccionado').update(totalSeleccionado);
}

</script>

<?php 

echo $ajax->form(array('type' => 'post',
    'options' => array(
        'model'=>'OrdenCajaCobro',
        'update'=>'orden_cobro_caja_generada',
        'url' => array('plugin' => 'mutual','controller' => 'orden_caja_cobros', 'action' => 'add'),
		'loading' => "$('spinner').show();$('orden_cobro_caja_generada').hide();",
		'complete' => "$('orden_cobro_caja_generada').show();$('spinner').hide();"
    )
));
 


?>

<div style="width:750px; ;height: 350px; overflow: scroll;">
	<table>
	  <tr>
		<th>PERIODO</th>
		<th>ORDEN</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR / PRODUCTO</th>
		<th>CUOTA</th>
		<th>CONCEPTO</th>
		<th>VTO</th>
		<th colspan="2">SALDO</th>
	
	    <th></th>
	  </tr>
	  <?php
	  	$i=0;
	  	$ACU_VENCIDO = 0;
	  	$ACU_AVENCER = 0;
	  	$ACU_TOTAL = 0;
	  	foreach($cuotas as $cuota):
	  		$i++;
	  		$ACU_TOTAL += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  		if($cuota['OrdenDescuentoCuota']['vencida']==1)$ACU_VENCIDO += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  		else $ACU_AVENCER += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  ?>
		  <tr id="TRL_<?php echo $i?>">
			<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
			<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuento']['id'],'h' => 450, 'w' => 700))?></td>
			<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_orden_dto']?> #<?php echo $cuota['OrdenDescuento']['numero']?></td>
			<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
			<td align="center"><strong><?php echo $cuota['OrdenDescuentoCuota']['nro_cuota'].'/'. $cuota['OrdenDescuento']['cuotas']?></strong></td>
			<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
			<td align="center"><strong><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></strong></td>
			<td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)?></td>
			<td align="center"><?php echo $controles->vencida($cuota['OrdenDescuentoCuota']['vencida'])?></td>
			<td>
				<input type="checkbox" name="data[OrdenCajaCobroCuota][orden_descuento_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['saldo_cuota'] * 100?>" id="OrdenCajaCobroCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
				<input type="hidden" name="data[OrdenCajaCobroCuota][orden_descuento_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="OrdenCajaCobroCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
			</td>
		  </tr>
	  <?php endforeach;?>
	</table>
</div>
<table>
  <tr>
  	<th></th>
  	<th>TOTAL</th>
  	<th>VENCIDO</th>
  	<th>A VENCER</th>
    <th>SELECCIONADO</th>
    <th></th>
  </tr>
  <tr>
  	<td><input type="button" value="Marcar Todo" onclick="selUnSelAll(true,false)"> | <input type="button" value="Solo lo Vencido" onclick="selUnSelAll(true,true)"> | <input type="button" value="Limpiar" onclick="selUnSelAll(false,false)"></td>
  	 <td align="right"><?php echo number_format($ACU_TOTAL,2)?></td>	
  	 <td align="right"><?php echo number_format($ACU_VENCIDO,2)?></td>
  	  <td align="right"><?php echo number_format($ACU_AVENCER,2)?></td>
    <td align="right" class="selected" ><h1><div id="total_seleccionado">0.00</div></h1></td>
    <td>
    	<?php echo $frm->submit("GENERAR ORDEN",array('id' => 'btn_genera_orden'))?>
    </td>
  </tr>
</table>
<?php echo $frm->hidden('OrdenCajaCobro.socio_id',array('value' => $socio_id))?>
<?php echo $frm->hidden('OrdenCajaCobro.importe')?>
</form>
<?php
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>SUSPENDER CONSUMO PERMANENTE</h3>

<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('persona_id' => $socio['Socio']['persona_id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<?php echo $this->renderElement('orden_descuento/resumen_by_id',array('id' => $id,'detallaCuotas'=>false,'plugin' => 'mutual'))?>
<script type="text/javascript">

var rows = <?php echo count($cuotas_adeudadas)?>;

Event.observe(window, 'load', function() {
		selUnSelAll(false,false);
});

function selUnSelAll(status,soloVencida){
	for (i=1;i<=rows;i++){
		oChkCheck = document.getElementById('OrdenDescuentoCuotaOrdenDescuentoCuotaId_' + i);
		oChkCheck.checked = false;
		if(soloVencida){
			vencida = document.getElementById('OrdenDescuentoCuotaOrdenDescuentoCuotaVencida_' + i).value;
			if(vencida == 1)oChkCheck.checked = status;
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
		oChkCheck = document.getElementById('OrdenDescuentoCuotaOrdenDescuentoCuotaId_' + i);
		if (oChkCheck.checked){
			totalSeleccionado = totalSeleccionado + parseInt(oChkCheck.value);
			celdas.each(function(td){td.addClassName("selected");});
		}else{
			celdas.each(function(td){td.removeClassName("selected");});
		}	
	}
	totalSeleccionado = FormatCurrency(totalSeleccionado/100);
	$('total_seleccionado').update(totalSeleccionado);
}


	function validateForm(){
		return confirm("DAR DE BAJA LA ORDEN #<?php echo $id?>?");
	}
</script>
<?php echo $frm->create(null,array('action' => 'suspender_permanente/'.$id.'/'.$socio['Socio']['id'],'onsubmit' => "return validateForm()"))?>
<div class="areaDatoForm">

	<?php //   print_r($cuotas_adeudadas)?>
	
	<h4>SUSPENSION DEL CONSUMO PERMANENTE</h4>
	<br/>
	
	<table class="tbl_form">
		<tr>
			<td>A PARTIR DE</td>
			<td><?php echo $frm->periodo('OrdenDescuento.periodo_hasta',null,date('Ym'),date('Y'),date('Y')+1)?></td>
		</tr>
	</table>	
	
	<table>
		<tr>
			<th colspan="11" style="text-align: left;">DETALLE DE CUOTAS ADEUDADAS</th>
		</tr>
	  <tr>
	  	<th>ORGANISMO</th>
		<th>PERIODO</th>
		<th>ORDEN</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR / PRODUCTO</th>
		<th>CUOTA</th>
		<th>CONCEPTO</th>
		<th>VTO</th>
		<th colspan="2">SALDO</th>
	
	    <th>BAJA</th>
	  </tr>
	  <?php
	  	$i=0;
	  	$ACU_VENCIDO = 0;
	  	$ACU_AVENCER = 0;
	  	$ACU_TOTAL = 0;
	  	foreach($cuotas_adeudadas as $cuota):
	  		$i++;
	  		$ACU_TOTAL += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  		if($cuota['OrdenDescuentoCuota']['vencida']==1)$ACU_VENCIDO += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  		else $ACU_AVENCER += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  ?>
		  <tr id="TRL_<?php echo $i?>">
		  	<td><?php echo $cuota['OrdenDescuentoCuota']['organismo']?></td>
			<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
			<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuento']['id'],'h' => 450, 'w' => 700))?></td>
			<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_orden_dto']?> #<?php echo $cuota['OrdenDescuento']['numero']?></td>
			<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
			<td align="center"><strong><?php echo $cuota['OrdenDescuentoCuota']['nro_cuota'].'/'. $cuota['OrdenDescuento']['cuotas']?></strong></td>
			<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
			<td align="center"><strong><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></strong></td>
			<td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)?></td>
			<td align="center"><?php echo $controles->vencida($cuota['OrdenDescuentoCuota']['vencida'])?></td>
			<td align="center">
				<input type="checkbox" name="data[OrdenDescuentoCuota][orden_descuento_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['saldo_cuota'] * 100?>" id="OrdenDescuentoCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
				<input type="hidden" name="data[OrdenDescuentoCuota][orden_descuento_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="OrdenDescuentoCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
			</td>
		  </tr>
	  <?php endforeach;?>
	</table>
	<table>
	  <tr>
	  	<th></th>
	  	<th>TOTAL</th>
	  	<th>VENCIDO</th>
	  	<th>A VENCER</th>
	    <th>SELECCIONADO</th>
	  </tr>
	  <tr>
	  	<td><input type="button" value="Marcar Todo" onclick="selUnSelAll(true,false)"> | <input type="button" value="Solo lo Vencido" onclick="selUnSelAll(true,true)"> | <input type="button" value="Limpiar" onclick="selUnSelAll(false,false)"></td>
	  	 <td align="right"><?php echo number_format($ACU_TOTAL,2)?></td>	
	  	 <td align="right"><?php echo number_format($ACU_VENCIDO,2)?></td>
	  	  <td align="right"><?php echo number_format($ACU_AVENCER,2)?></td>
	    <td align="right" class="selected" ><h1><div id="total_seleccionado">0.00</div></h1></td>
	  </tr>
	</table>		
	

</div>
<?php echo $frm->hidden("OrdenDescuento.id",array('value' => $id))?>
<?php echo $frm->submit('DAR DE BAJA LA ORDEN #' . $id)?>
<?php echo $frm->end();?>
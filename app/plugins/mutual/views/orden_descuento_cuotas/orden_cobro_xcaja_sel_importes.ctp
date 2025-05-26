<h1>ORDEN DE COBRO POR CAJA</h1>
<hr>
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
				if(vencida === 1 && !oChkCheck.disabled)oChkCheck.checked = status;
			}else{
				if(!oChkCheck.disabled)oChkCheck.checked = status;
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
			var ret = true;
			var celdas = $('TRL_' + i).immediateDescendants();

			oChkCheck = document.getElementById('OrdenCajaCobroCuotaOrdenDescuentoCuotaId_' + i);
			
			otxtImpoPago = document.getElementById('OrdenCajaCobroCuotaOrdenDescuentoCuotaId1_' + i);
	
			toggleCell('TRL_' + i,oChkCheck);

			valueCheck = parseInt(oChkCheck.value);
			impoPago = new Number(otxtImpoPago.value);
			impoPago = impoPago.toFixed(2);
			impoPago100 = impoPago * 100;
			impoPago100 = impoPago100.toFixed(0);

			if(impoPago100 > valueCheck && oChkCheck.checked){
				alert("EL IMPORTE DEL PAGO " + otxtImpoPago.value + " NO PUEDE SER SUPERIOR AL SALDO ACTUAL DE LA CUOTA!");
				oChkCheck.checked = false;
				toggleCell('TRL_' + i,oChkCheck);
				otxtImpoPago.focus();
				ret = false;
			}

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
		
	
//		alert(totalSeleccionado);
		
		if(totalSeleccionado >= 0)$('btn_genera_orden').enable();
		else $('btn_genera_orden').disable();
	
		totalSeleccionado = totalSeleccionado/100;
		
		document.getElementById('OrdenCajaCobroImporte').value = totalSeleccionado;
		document.getElementById('OrdenCajaCobroImporteCobrado').value = totalSeleccionado;
	
	//	alert(totalSeleccionado);
		totalSeleccionado = FormatCurrency(totalSeleccionado);
		
	
		$('total_seleccionado').update(totalSeleccionado);

		return ret;
}

</script>
<?php echo $this->renderElement('socios/apenom',array('socio_id' => $socio_id,'plugin'=>'pfyj'))?>

<?php 

//echo $ajax->form(array('type' => 'post',
//    'options' => array(
//        'model'=>'OrdenCajaCobro',
//        'update'=>'orden_cobro_caja_generada',
//        'url' => array('plugin' => 'mutual','controller' => 'orden_caja_cobros', 'action' => 'add'),
//		'loading' => "$('spinner').show();$('orden_cobro_caja_generada').hide();",
//		'complete' => "$('orden_cobro_caja_generada').show();$('spinner').hide();"
//    )
//));
 


?>

<form id="formGenerarOrdenCobroByCaja" onsubmit="event.returnValue = false; return false;" method="post" action="<?php echo $this->base?>/mutual/orden_caja_cobros/add">
<fieldset style="display:none;"><input type="hidden" name="_method" value="POST" /></fieldset>
<script type="text/javascript">

	Event.observe('formGenerarOrdenCobroByCaja', 'submit', function(event) {
		
		var importe_cobrado = $('OrdenCajaCobroImporteCobrado').getValue();
		var importe_seleccionado = $('OrdenCajaCobroImporte').getValue();
		importe_cobrado = parseFloat(importe_cobrado);
		importe_seleccionado = parseFloat(importe_seleccionado);

		//validar el formulario
		if(!SelSum()){
			$('orden_cobro_caja_generada').hide();
			return false;
		}
		
		
		if(importe_cobrado < importe_seleccionado){
			//paga menos de lo seleccionado---> aplicar criterio de imputacion
			if(confirm('El importe abonado es MENOR que el importe SELECCIONADO!!\nLA IMPUTACION SE EFECTUA DE LA DEUDA MAS VIEJA A LA MAS NUEVA.')){
				document.getElementById('OrdenCajaCobroTipoImputacion').value = 1;
				procesaByAjax();
			}	
		}else if(importe_cobrado > importe_seleccionado){
			alert('EL IMPORTE ABONADO NO PUEDE SER MAYOR AL SELECCIONADO');	
		}else{
			//paga lo seleccionado---> NO aplicar criterio de imputacion
			document.getElementById('OrdenCajaCobroTipoImputacion').value = 0;
			procesaByAjax();
		}	
		
	},false);
	
	function procesaByAjax(){
		new Ajax.Updater('orden_cobro_caja_generada',
				'<?php echo $this->base?>/mutual/orden_caja_cobros/add', 
				{
					asynchronous:true, 
					evalScripts:true, 
					onComplete:function(request, json) {
						$('orden_cobro_caja_generada').show();
						$('spinner').hide();
					}, 
					onLoading:function(request) {
						$('spinner').show();
						$('orden_cobro_caja_generada').hide();
					}, 
					parameters:Form.serialize('formGenerarOrdenCobroByCaja'), 
					requestHeaders:['X-Update', 'orden_cobro_caja_generada']
				});	
	}
</script>



<div style="width:90%; ;height: 300px; overflow: scroll;">
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
		<th>PAGO</th>
	    <th></th>
	  </tr>
	  <?php
	  	$i=0;
	  	$ACU_VENCIDO = 0;
	  	$ACU_AVENCER = 0;
	  	$ACU_TOTAL = 0;
	  	foreach($cuotas as $cuota):
	  	
	  		$bloqueo = array();
	  		if(!empty($cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'])) $bloqueo = $cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'];
	  		$i++;
	  		$ACU_TOTAL += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  		if($cuota['OrdenDescuentoCuota']['vencida']==1)$ACU_VENCIDO += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  		else $ACU_AVENCER += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  ?>
		  <tr id="TRL_<?php echo $i?>">
			<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
			<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuento']['id'].'/'.$cuota['OrdenDescuentoCuota']['socio_id'],'h' => 450, 'w' => 700))?></td>
			<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_orden_dto']?> #<?php echo $cuota['OrdenDescuento']['numero']?></td>
			<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
			<td align="center"><strong><?php echo $cuota['OrdenDescuentoCuota']['nro_cuota'].'/'. $cuota['OrdenDescuento']['cuotas']?></strong></td>
			<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
			<td align="center"><strong><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></strong></td>
			<td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['saldo_cuota'])?></td>
			<td align="center"><?php echo $controles->vencida($cuota['OrdenDescuentoCuota']['vencida'])?></td>
			<td>
				<input type="text" <?php echo (!empty($bloqueo) && $bloqueo['id'] != 0 ? "disabled='disabled'" : "") ?> name="data[OrdenCajaCobroCuota][orden_descuento_cuota_id1][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2,".","")?>" id="OrdenCajaCobroCuotaOrdenDescuentoCuotaId1_<?php echo $i?>" class="input_number" onkeypress = "return soloNumeros(event,true)" size="12" maxlength="12" onblur="SelSum()"/>
			</td>
			<td>
				<?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>
					<input type="checkbox" disabled="disabled" name="data[OrdenCajaCobroCuota][orden_descuento_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo number_format(round($cuota['OrdenDescuentoCuota']['saldo_cuota'],2) * 100,0,".","")?>" id="OrdenCajaCobroCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
					<input type="hidden" disabled="disabled" name="data[OrdenCajaCobroCuota][orden_descuento_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="OrdenCajaCobroCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
					<span style="color: red;"><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></span>
				<?php else:?>
					<input type="checkbox" name="data[OrdenCajaCobroCuota][orden_descuento_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo number_format(round($cuota['OrdenDescuentoCuota']['saldo_cuota'],2) * 100,0,".","")?>" id="OrdenCajaCobroCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
					<input type="hidden" name="data[OrdenCajaCobroCuota][orden_descuento_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="OrdenCajaCobroCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
				<?php endif;?>
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
    <th>IMPORTE ABONADO</th>
    <th></th>
  </tr>
  <tr>
  	<td><input type="button" value="Marcar Todo" onclick="selUnSelAll(true,false)"> | <input type="button" value="Solo lo Vencido" onclick="selUnSelAll(true,true)"> | <input type="button" value="Limpiar" onclick="selUnSelAll(false,false)"></td>
  	 <td align="right"><?php echo number_format($ACU_TOTAL,2)?></td>	
  	 <td align="right"><?php echo number_format($ACU_VENCIDO,2)?></td>
  	  <td align="right"><?php echo number_format($ACU_AVENCER,2)?></td>
    <td align="right" class="selected" ><h1><div id="total_seleccionado">0.00</div></h1></td>
    <!-- <td align="right"><?php //   echo $frm->money('OrdenCajaCobro.importe_cobrado','','0.00')?></td> -->
    <td>
    	<?php echo $frm->submit("GENERAR ORDEN",array('id' => 'btn_genera_orden'))?>
    </td>
  </tr>
</table>
<?php echo $frm->hidden('OrdenCajaCobro.socio_id',array('value' => $socio_id))?>
<?php echo $frm->hidden('OrdenCajaCobro.importe')?>
<?php echo $frm->hidden('OrdenCajaCobro.tipo_imputacion')?>
<?php echo $frm->hidden('OrdenCajaCobro.importe_cobrado')?>
</form>
<div id="spinner" style="display: none; float: left;color:red;"><?php echo $html->image('controles/ajax-loader.gif'); ?><strong>GENERANDO ORDEN...</strong></div>
<div class="areaDatoForm2" id="orden_cobro_caja_generada"></div>
<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>REASIGNACION PUNTUAL DE BENEFICIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<div class="notices"><strong>ATENCION!: </strong> Este proceso afecta &uacute;nicamente a las Cuotas seleccionadas. Para reasignar el beneficio en forma PERMANENTE debe usar la opci&oacute; <strong>R.Benef.Orden</strong>(Reasingar Beneficio Orden de Dto) </div>
<div class="areaDatoForm3">
		<?php echo $form->create(null,array('action'=> 'reasignar_beneficio_puntual/'.$socio['Socio']['id']));?>	
		<table class="tbl_form">
			<tr>
				<td><?php echo $this->requestAction('/pfyj/persona_beneficios/combo/PersonaBeneficio/'.$socio['Persona']['id'])?></td>
				<td><?php echo $frm->submit('CONSULTAR',array('class' => 'btn_consultar'));?></td>
			</tr>
		</table>
		<?php echo $frm->hidden('PersonaBeneficio.reasignar',array('value' => 0))?>
		<?php echo $frm->end();?> 
</div>
<?php if(!empty($cuotas)):?>

<?php echo $form->create(null,array('action'=> 'reasignar_beneficio_puntual/'.$socio['Socio']['id']));?>
<script language="Javascript" type="text/javascript">
var rows = <?php echo count($cuotas)?>;

Event.observe(window, 'load', function() {
		$('btn_reasigna_beneficio').disable();
		selUnSelAll(false,false);
});

function selUnSelAll(status,soloVencida){
	for (i=1;i<=rows;i++){
		oChkCheck = document.getElementById('OrdenDescuentoCuotaOrdenDescuentoCuotaId_' + i);
		oChkCheck.checked = false;
		if(soloVencida){
			vencida = document.getElementById('OrdenDescuentoCuotaOrdenDescuentoCuotaVencida_' + i).value;
			if(vencida == 1 && !oChkCheck.disabled)oChkCheck.checked = status;
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
		var celdas = $('TRL_' + i).immediateDescendants();
		oChkCheck = document.getElementById('OrdenDescuentoCuotaOrdenDescuentoCuotaId_' + i);
		if (oChkCheck.checked){
			totalSeleccionado = totalSeleccionado + parseInt(oChkCheck.value);
			celdas.each(function(td){td.addClassName("selected");});
		}else{
			celdas.each(function(td){td.removeClassName("selected");});
		}	
	}
//	document.getElementById('OrdenCajaCobroImporte').value = totalSeleccionado;
	if(totalSeleccionado > 0)$('btn_reasigna_beneficio').enable();
	else $('btn_reasigna_beneficio').disable();
	totalSeleccionado = FormatCurrency(totalSeleccionado/100);
	$('total_seleccionado').update(totalSeleccionado);
}

</script>

	<table>
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
	  		$bloqueo = array();
	  		if(!empty($cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'])) $bloqueo = $cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'];
	  		
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
			<td>
				<?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>
					<input type="checkbox" disabled="disabled" name="data[OrdenDescuentoCuota][orden_descuento_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['saldo_cuota'] * 100?>" id="OrdenDescuentoCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
					<input type="hidden" disabled="disabled" name="data[OrdenDescuentoCuota][orden_descuento_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="OrdenDescuentoCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
					<span style="color: red;"><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></span>
				<?php else:?>
					<input type="checkbox" name="data[OrdenDescuentoCuota][orden_descuento_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['saldo_cuota'] * 100?>" id="OrdenDescuentoCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
					<input type="hidden" name="data[OrdenDescuentoCuota][orden_descuento_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="OrdenDescuentoCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
				<?php endif;?>			
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
	<div class="notices"><strong>ATENCION!: </strong> Este proceso afecta &uacute;nicamente a las Cuotas seleccionadas. Para reasignar el beneficio en forma PERMANENTE debe usar la opci&oacute; <strong>R.Benef.Orden</strong>(Reasingar Beneficio Orden de Dto) </div>
		
  	<table class="tbl_form">
  		<tr>
  			<td><?php echo $this->requestAction('/pfyj/persona_beneficios/combo/OrdenDescuentoCuota/'.$socio['Persona']['id'].'/0/0/'.$this->data['PersonaBeneficio']['persona_beneficio_id'])?></td>
  		</tr>
  		<tr><td><?php echo $frm->submit("REASIGNAR BENEFICIO",array('id' => 'btn_reasigna_beneficio'))?></td></tr>
  	</table>
  	<?php echo $frm->hidden('PersonaBeneficio.persona_beneficio_id',array('value' => $this->data['PersonaBeneficio']['persona_beneficio_id']))?>
  	<?php echo $frm->hidden('PersonaBeneficio.reasignar',array('value' => 1))?>
  	<?php echo $frm->end();?>	
<?php endif;?>
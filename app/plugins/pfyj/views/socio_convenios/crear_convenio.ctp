<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>CONVENIOS DE PAGO DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<h4>NUEVO CONVENIO DE PAGO</h4>
<hr/>
<h5>DETALLE DE CUOTAS ADEUDADAS (no incluye cuotas de Convenios anteriores)</h5>
<script language="Javascript" type="text/javascript">
var rows = <?php echo count($cuotas)?>;

Event.observe(window, 'load', function() {
		$('btn_genera_convenio').disable();
		$('SocioConvenioCuotas').disable();
		selUnSelAll(false,false);
});

function selUnSelAll(status,soloVencida){
	for (i=1;i<=rows;i++){
		oChkCheck = document.getElementById('SocioConvenioCuotaOrdenDescuentoCuotaId_' + i);
		oChkCheck.checked = false;
		if(soloVencida){
			vencida = document.getElementById('SocioConvenioCuotaOrdenDescuentoCuotaVencida_' + i).value;
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
		oChkCheck = document.getElementById('SocioConvenioCuotaOrdenDescuentoCuotaId_' + i);

		toggleCell('TRL_' + i,oChkCheck);
		
		if (oChkCheck.checked){
			totalSeleccionado = totalSeleccionado + parseInt(oChkCheck.value);
		}	
	}
	totalSeleccionado = FormatCurrency(totalSeleccionado/100);
	document.getElementById('SocioConvenioImporte').value = totalSeleccionado;
	if(totalSeleccionado > 0){
		$('btn_genera_convenio').enable();
		$('SocioConvenioCuotas').enable();
	}else{
		$('btn_genera_convenio').disable();
		$('SocioConvenioCuotas').disable();
	}	
	$('total_seleccionado').update(totalSeleccionado);
}

</script>
<?php 
echo $ajax->form(array('type' => 'post',
    'options' => array(
        'model'=>'SocioConvenio',
        'update'=>'convenio_vista_previa',
        'url' => array('plugin' => 'pfyj','controller' => 'socio_convenios', 'action' => 'crear_convenio/'.$socio['Socio']['id']),
		'loading' => "$('spinner').show();$('convenio_vista_previa').hide();",
		'complete' => "$('convenio_vista_previa').show();$('spinner').hide();"
    )
));
?>
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
				<input type="checkbox" name="data[SocioConvenioCuota][orden_descuento_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['saldo_cuota'] * 100?>" id="SocioConvenioCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
				<input type="hidden" name="data[SocioConvenioCuota][orden_descuento_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="SocioConvenioCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
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
	</tr>
	<tr>
		<td>
			<input type="button" value="Marcar Todo" onclick="selUnSelAll(true,false)">
			&nbsp;|&nbsp;
			<input type="button" value="Solo lo Vencido" onclick="selUnSelAll(true,true)">
			&nbsp;|&nbsp;
			<input type="button" value="Limpiar" onclick="selUnSelAll(false,false)">
		</td>
		<td align="right"><?php echo number_format($ACU_TOTAL,2)?></td>	
		<td align="right"><?php echo number_format($ACU_VENCIDO,2)?></td>
		<td align="right"><?php echo number_format($ACU_AVENCER,2)?></td>
		<td align="right" class="selected" ><h1><div id="total_seleccionado">0.00</div></h1></td>
	</tr>
</table>
<div class="areaDatoForm">
<table class="tbl_form">
	<tr>
		<td>BENEFICIO</td>
		<td>
		
		<?php 
		$comboBeneficios = array();
		$beneficios = $this->requestAction('/pfyj/persona_beneficios/beneficios_by_persona/'.$socio['Persona']['id'].'/1');
		foreach($beneficios as $beneficio){
			$comboBeneficios[$beneficio['PersonaBeneficio']['id']] = $beneficio['PersonaBeneficio']['string'];
		}
		echo $frm->input('SocioConvenio.persona_beneficio_id',array('type'=>'select','options'=>$comboBeneficios,'empty'=>false,'label'=>''));
		?>	
		
		</td>
	</tr>
	<tr>
		<td>FECHA</td>
  		<td><?php echo $frm->input('SocioConvenio.fecha',array('dateFormat' => 'DMY','label'=>'','minYear'=>'1980', 'maxYear' => date("Y") + 1))?></td>
	</tr>	
	<tr>
		<td>CANTIDAD DE CUOTAS</td>
  		<td>
  			<input name="data[SocioConvenio][cuotas]" type="text" size="5" maxlenght="5" class="input_number" onkeypress="return soloNumeros(event)" maxlength="11" value="0" id="SocioConvenioCuotas" />
  		</td>
	</tr>
	<tr>
		<td colspan="2"><?php echo $frm->submit("VISTA PREVIA CONVENIO DE PAGO",array('id' => 'btn_genera_convenio'))?></td>
	</tr>
</table>
</div>
<?php echo $frm->hidden('SocioConvenio.socio_id',array('value' => $socio['Socio']['id']))?>
<?php echo $frm->hidden('SocioConvenio.importe')?>
<?php echo $frm->hidden('SocioConvenio.generar',array('value' => 0))?>
<?php //   echo $frm->hidden('SocioConvenio.tipo_convenio')?>
</form>
<div id="spinner" style="display: none; float: left;color:red;"><?php echo $html->image('controles/ajax-loader.gif'); ?><strong>GENERANDO VISTA PREVIA CONVENIO...</strong></div>
<div id="convenio_vista_previa"></div>
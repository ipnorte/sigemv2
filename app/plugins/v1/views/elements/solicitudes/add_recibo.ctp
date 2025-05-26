<h1>SOLICITUD DE CREDITO Nro. <?php echo $nro_solicitud?> :: (<?php echo $solicitud['Solicitud']['estado_descripcion']?>)</h1>
<hr>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => false,'plugin' => 'pfyj'))?>

<h3 style="border-bottom: 1px solid;">BENEFICIO POR EL CUAL SE DESCUENTA</h3>
<?php echo $solicitud['Beneficio']['string']?>

<h3 style="border-bottom: 1px solid;">LIQUIDACION Y PAGO DEL PRESTAMO</h3>
<?php echo $this->renderElement('solicitudes/grilla_liquidacion', array('solicitud' => $solicitud, 'plugin' => 'v1'))?>

<?php if(count($solicitud['SolicitudCancelacionOrden'])!=0):?>

	<h3 style="border-bottom: 1px solid;">ORDENES DE CANCELACION</h3>
		<?php echo $this->renderElement('solicitud_cancelaciones/ordenes_cancelacion_info_pago', array('cancelaciones' => $solicitud['SolicitudCancelacionOrden'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>
	
<?php elseif(count($solicitud['Cancelaciones'])!=0):?>
	<h3 style="border-bottom: 1px solid;">CANCELACIONES</h3>
		<?php echo $this->renderElement('solicitud_cancelaciones/cancelaciones_info_pago', array('cancelaciones' => $solicitud['Cancelaciones'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>

<?php endif;?>

<?php $parametros = $solicitud['Solicitud']['nro_solicitud'];
	if(isset($persona_id)) $parametros .= '/' . $persona_id;
	?>

<script language="Javascript" type="text/javascript">
	var importe_solicitud = <?php echo round($solicitud['Solicitud']['en_mano'],2) ?>;
	var rows = <?php echo $rows?>;

	Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
		document.getElementById("ReciboImporte").value = importe_solicitud;
		ocultarOptionFCobro();
		totalRecibo();
	
	});


	function totalRecibo(){
		var impoRecibo = parseFloat($('ReciboAporteSocio').getValue());
		var impoCancela = parseFloat($('ReciboImporteCobro').getValue());
		var valor = importe_solicitud + impoRecibo + impoCancela;
		var importe = new Number(valor);

		document.getElementById('ReciboImporteCobro').value = importe;
		document.getElementById('ReciboImporte').value =  importe;
		
		actualizaImporte(0);
	}		

	function chkOnclick(){
		selSum();

		totalRecibo();

		acumulado = parseFloat($('acumulado').getValue());
		cobro = document.getElementById("ReciboImporteCobro").value;
		cobro = parseFloat(cobro);
		resto = cobro - acumulado;
		if(resto == 0) $('btn_submit').enable();
		else $('btn_submit').disable();
		document.getElementById("ReciboImporte").value = resto;
		document.getElementById("ReciboTipoCobro").value = "";
		ocultarOptionFCobro();
		
	}	

	function selSum(){	
		var totalSeleccionado = 0;
		var error = "";	
		var chequeado = 0;

		ocultarOptionFCobro();
		
		for (i=1;i<=rows;i++){
			var celdas = $('TRL_' + i).immediateDescendants();
			oChkCheck = document.getElementById('CancelacionOrdenSaldo_' + i);
			if (oChkCheck.checked){
				chequeado = 1;
				
				strChkTxt = oChkCheck.value;
				numChkTxt = new Number(strChkTxt);
				numChkTxt = numChkTxt / 100;
				numChkTxt = numChkTxt.toFixed(2);

				chkValue = parseInt(oChkCheck.value);
				totalSeleccionado = totalSeleccionado + chkValue;
	
				celdas.each(function(td){td.addClassName("selected");});
			}else{
				celdas.each(function(td){td.removeClassName("selected");});
			}	
		}
		
		totalSeleccionado = totalSeleccionado/100;
		
		document.getElementById('ReciboImporteCobro').value = totalSeleccionado;
		document.getElementById('ReciboImporte').value = totalSeleccionado;
		
		importeCobro = $('ReciboImporteCobro').getValue();
		totalSeleccionado = FormatCurrency(totalSeleccionado);
	}

</script>


<h3 style="border-bottom: 1px solid;">RECIBO DE INGRESO</h3>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro', 'action' => "addRecibo/" . $parametros ));?>
	<?php if(count($cancelaciones) > 0): ?>
		<h4>DETALLE DE ORDENES DE CANCELACION EMITIDAS</h4>
		<table>
			<tr>
				<th>#</th>
				<th>ESTADO</th>
				<th>TIPO</th>
				<th>ORDEN</th>
				<th>TIPO / NUMERO</th>
				<th>PROVEEDOR / PRODUCTO</th>
				<th>A LA ORDEN DE</th>
				<th>DEUDA PROVEEDOR</th>
				<th>SALDO ORDEN DTO</th>
				<th>IMPORTE SELECCIONADO</th>
				<th>DEBITO/CREDITO</th>
				<th>VENCIMIENTO</th>
				<th></th>
				
			</tr>
			<?php $i=0; // $nRespon = $cancelaciones[0]['CancelacionOrden']['origen_proveedor_id'];?>
			<?php foreach($cancelaciones as $cancelacion):
				if($cancelacion['CancelacionOrden']['orden_proveedor_id'] == $solicitud['Producto']['Proveedor']['idr']):
				$class = null;
				if($cancelacion['CancelacionOrden']['estado'] == 'P') $class = ' class="altrow"';
				$i++;
			?>
				<tr id="TRL_<?php echo $i?>" <?php echo $class?>>
					<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
					<td><strong><?php echo ($cancelacion['CancelacionOrden']['estado'] == 'P' ? 'COBRADO' : 'EMITIDO')?></strong>
					<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
					<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_descuento_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['tipo_nro_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
					<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
					<td align="right">
						<?php
							if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
								echo $this->requestAction('/config/global_datos/valor/' . $cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
								echo "&nbsp;= \$";
								echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
							}
						?>
					</td>
					<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
					<td><input type="checkbox" name="data[CancelacionOrden][id_check][<?php echo $cancelacion['CancelacionOrden']['id'] ?>]" value="<?php echo number_format(round($cancelacion['CancelacionOrden']['importe_proveedor'],2) * -100,0,".","")?>" id="CancelacionOrdenSaldo_<?php echo $i ?>" onclick="toggleCell('TRL_<?php echo $i?>',this); chkOnclick()"/></td>
				</tr>
				<?php endif;?>
			<?php endforeach;?>	
		</table>
	<?php endif; ?>

	<table class="tbl_form">
		<tr>
			<td>FECHA RECIBO:</td>
			<td><?php echo $frm->calendar('Recibo.fecha_comprobante',null,null,null,date('Y')+1)?></td>
		</tr>
		<?php if($aporte_socio > 0): ?>
		<tr>
			<td>APORTE SOCIO:</td>
			<td><div class="input text"><label for="ReciboAporteSocio"></label><input name="data[Recibo][aporte_socio]" type="text" value=<?php echo $aporte_socio ?> size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,false)" onblur="totalRecibo()" id="ReciboAporteSocio" /></div></td>
		</tr>
		<?php endif; ?>
		
		<tr>
			<td>CONCEPTO:</td>
			<td><?php echo $frm->input('Recibo.observacion', array('value' => utf8_encode($solicitud['Solicitud']['solicitante'] . ' ** Solic. ' . $solicitud['Solicitud']['nro_solicitud']),'size'=>50,'maxlength'=>200)) ?></td>
		</tr>
		
		<?php echo $this->renderElement('recibos/forma_cobro',array(
										'plugin'=>'clientes'))?>
	</table>
	<?php echo $frm->hidden('Recibo.tipo_documento', array('value' => 'REC')); ?>
	<?php echo $frm->hidden('Recibo.cabecera_nro_solicitud', array('value' => $solicitud['Solicitud']['nro_solicitud'])); ?>
	<?php echo $frm->hidden('Recibo.destinatario', array('value' => '')); ?>
	<?php echo $frm->hidden('Recibo.forma_cobro_desc') ?>
	<?php echo $frm->hidden('Recibo.importe_cobro', array('value' => 0)) ?>
	<?php if($aporte_socio == 0): ?>
		<?php echo $frm->hidden('Recibo.aporte_socio', array('value' => 0)) ?>
	<?php endif; ?>
	<?php echo $frm->hidden('Solicitud.id',array('value' => $solicitud['Solicitud']['nro_solicitud']))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR E IMPRIMIR','URL' => $regresar))?>

</div>
<div style="clear: both;"></div>

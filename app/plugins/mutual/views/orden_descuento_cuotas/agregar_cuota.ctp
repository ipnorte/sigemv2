<?php echo $this->renderElement('head',array('title' => 'AGREGAR CUOTA A LA ORDEN DE DESCUENTO #'.$orden_descuento_id,'plugin' => 'config'))?>
<?php echo $this->renderElement('socios/apenom',array('socio_id' => $orden['OrdenDescuento']['socio_id'], 'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('orden_descuento/resumen_by_id',array('plugin' => 'mutual','id' => $orden_descuento_id,'detallaCuotas' => false))?>

<script language="Javascript" type="text/javascript">
	function validate_form() {
	

		var nroCuota = parseInt($('OrdenDescuentoCuotaNroCuota').getValue());
		var importe = parseFloat($('OrdenDescuentoCuotaImporte').getValue());
		if(isNaN(nroCuota)) {
			alert("Indicar el número de cuota!")
			$('OrdenDescuentoCuotaNroCuota').focus();
			return false;
		}
		if(isNaN(importe)) {
			alert("Indicar el importe de cuota!")
			$('OrdenDescuentoCuotaImporte').focus();
			return false;
		}
		
		var msg = "*** ATENCION! ***\n\n";
    	msg = msg + "AGREGAR CUOTA ***\n";
    	msg = msg + "PROVEEDOR: " + getTextoSelect('OrdenDescuentoCuotaProveedorId') + "\n";
    	
    	msg = msg + "CONCEPTO: " + getTextoSelect('OrdenDescuentoCuotaTipoCuota') + "\n";
		msg = msg + "CUOTA: " + nroCuota + "\n";
		msg = msg + "IMPORTE: " + importe + "\n\n";
	
	
		msg = msg + "PERIODO: " + getTextoSelect('LiquidacionPeriodo') + "\n\n";
		msg = msg + "Cargar la cuota?";	
				
		return confirm(msg);
	}
</script>

<?php echo $form->create(null,array('action' => 'agregar_cuota/'.$orden_descuento_id, 'onsubmit' => "return validate_form()"));?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td>ASIGNAR AL PROVEEDOR</td><td><?php echo $this->renderElement('/proveedor/combo_proveedores_cancelacion',array('orden_descuento_id'=>$orden['OrdenDescuento']['id'].'/0','label' => '','plugin' => 'proveedores'))?></td>
		</tr>	
		<tr>
			<td>PERIODO</td>
			<td>
				<?php // echo $frm->periodo('OrdenDescuentoCuota.periodo','',null,$anio_ini,date('Y') + 2)?>
				<?php echo $this->renderElement('liquidacion/combo_periodos_habilitados',array('plugin' => 'mutual','organismo' => $orden['OrdenDescuento']['codigo_organismo'],'cantidad_periodos' => 18))?>
			</td>
		</tr>
		<tr>
			<td>VENCIMIENTO</td><td><?php echo $frm->input('OrdenDescuentoCuota.vencimiento',array('dateFormat' => 'DMY','label'=>'','minYear'=>$anio_ini, 'maxYear' => date("Y") + 2))?></td>
		</tr>		
		<tr>
			<td>CONCEPTO</td>
			
			<td>
			<?php echo $this->renderElement('global_datos/combo_cuotas_puntuales',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'OrdenDescuentoCuota.tipo_cuota',
																			'disabled' => false,
																			'empty' => false,
																			'selected' => $this->data['OrdenDescuentoCuota']['tipo_cuota'],
			))?>			
			</td>
		</tr>
		<tr>
			<td>NRO CUOTA</td><td><?php echo $frm->number('OrdenDescuentoCuota.nro_cuota',array('size' => 3,'maxlength' => 3))?></td>
		</tr>
		<tr>
			<td>IMPORTE</td><td><?php echo $frm->money('OrdenDescuentoCuota.importe',null,$this->data['OrdenDescuentoCuota']['importe'])?></td>
		</tr>
		<tr>
			<td>OBSERVACIONES</td><td><?php echo $frm->textarea('OrdenDescuentoCuota.observaciones',array('cols' => 60, 'rows' => 10))?></td>
		</tr>
	
		
								
	</table>
</div>
<?php echo $frm->hidden('OrdenDescuentoCuota.id',array('value' => 0))?>
<?php echo $frm->hidden('OrdenDescuentoCuota.orden_descuento_id',array('value' => $orden['OrdenDescuento']['id']))?>
<?php echo $frm->hidden('OrdenDescuentoCuota.socio_id',array('value' => $orden['OrdenDescuento']['socio_id']))?>
<?php echo $frm->hidden('OrdenDescuentoCuota.persona_beneficio_id',array('value' => $orden['OrdenDescuento']['persona_beneficio_id']))?>
<?php echo $frm->hidden('OrdenDescuentoCuota.tipo_orden_dto',array('value' => $orden['OrdenDescuento']['tipo_orden_dto']))?>
<?php echo $frm->hidden('OrdenDescuentoCuota.tipo_producto',array('value' => $orden['OrdenDescuento']['tipo_producto']))?>
<?php echo $frm->hidden('OrdenDescuentoCuota.nro_referencia_proveedor',array('value' => $orden['OrdenDescuento']['nro_referencia_proveedor']))?>
<?php echo $frm->hidden('OrdenDescuentoCuota.nro_orden_referencia',array('value' => $orden['OrdenDescuento']['nro_orden_referencia']))?>
<?php echo $frm->hidden('OrdenDescuentoCuota.codigo_comercio_referencia',array('value' => $orden['OrdenDescuento']['codigo_comercio_referencia']))?>

<?php if($orden['OrdenDescuento']['activo'] === '1') echo $frm->btnGuardarCancelar(array('URL' => '/mutual/orden_descuentos/carga_deuda/'.$orden_descuento_id))?>

<?php //   debug($orden)?>

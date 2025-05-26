<?php echo $this->renderElement('head',array('title' => 'MODIFICAR CUOTA DE LA ORDEN DE DESCUENTO #'.$orden_descuento_id,'plugin' => 'config'))?>
<?php echo $this->renderElement('socios/apenom',array('socio_id' => $orden['OrdenDescuento']['socio_id'], 'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('orden_descuento/resumen_by_id',array('plugin' => 'mutual','id' => $orden_descuento_id,'detallaCuotas' => false))?>
<?php //   echo $this->requestAction('/mutual/orden_descuento_cuotas/view/'.$orden_descuento_cuota_id)?>

<?php 
$bloqueo = array();
if(!empty($cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'])) $bloqueo = $cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'];
?>
<script language="Javascript" type="text/javascript">

function validate_form(){
    if($('OrdenDescuentoCuotaAplicarTodas').getValue()){
        var msg = "ATENCIÓN:\n";
        msg = msg + "Actualizar el valor a TODAS LAS CUOTAS DE LA ORDEN?";
        return confirm(msg);
    }
    return true;
}

</script>
<?php echo $form->create(null,array('action' => 'modificar_cuota/'.$cuota['OrdenDescuentoCuota']['id'],'onsubmit' => 'return validate_form()'));?>
<div class="areaDatoForm3">
	<h4>DATOS DE LA CUOTA</h4>
	<table class="tbl_form">
		<tr>
			<td>PROVEEDOR - PRODUCTO</td><td><strong><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></strong></td>
		</tr>	
		<tr>
			<td>BENEFICIO</td><td><strong><?php echo $cuota['OrdenDescuentoCuota']['beneficio']?></strong></td>
		</tr>		
		<tr>
			<td>CUOTA</td><td><strong><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></strong> - <strong><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></strong></td>
		</tr>
		<tr>
			<td>PERIODO</td><td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'],true)?></strong></td>
		</tr>		
	</table>
</div>
<?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>

	<div class="notices_error">LA CUOTA NO PUEDE SER MODIFICADA PORQUE SE ENCUENTRA EN LA LIQUIDACION <strong><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></strong> - CERRADA (NO IMPUTADA)</div>
	<div style="clear: both; width: 100%"></div>
	<?php echo $frm->btnForm(array('URL' => '/mutual/orden_descuentos/carga_deuda/'.$orden_descuento_id,'LABEL' => 'CANCELAR'))?>
<?php else:?>
	<div class="areaDatoForm">
		<h4>MODIFICAR CUOTA</h4>
		<table class="tbl_form">
			<tr>
                            <td>BENEFICIO</td><td colspan="3"><?php echo $this->requestAction('/pfyj/persona_beneficios/combo/OrdenDescuentoCuota/'.$cuota['Socio']['persona_id'].'/0/0/0/./'.$cuota['OrdenDescuentoCuota']['persona_beneficio_id'])?></td>
			</tr>	
			<tr>
				<td>IMPORTE</td>
                                <td><?php echo $frm->money('OrdenDescuentoCuota.importe',"",$cuota['OrdenDescuentoCuota']['importe'])?></td>
                                <td><input type="checkbox" name="data[OrdenDescuentoCuota][aplicar_todas]" value="1" id="OrdenDescuentoCuotaAplicarTodas"/></td>
                                <td>Aplicar este valor a TODAS las cuotas de la Orden</td>
			</tr>
			<tr>
				<td>OBSERVACIONES</td><td  colspan="3"><?php echo $frm->textarea('OrdenDescuentoCuota.observaciones',array('cols' => 60, 'rows' => 10))?></td>
			</tr>
		</table>
	</div>
	<?php echo $frm->hidden('OrdenDescuentoCuota.id',array('value' => $cuota['OrdenDescuentoCuota']['id']))?>
	<?php echo $frm->hidden('OrdenDescuentoCuota.orden_descuento_id',array('value' => $cuota['OrdenDescuentoCuota']['orden_descuento_id']))?>
	<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/orden_descuentos/carga_deuda/'.$orden_descuento_id))?>
<?php endif;?>

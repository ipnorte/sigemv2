<?php
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>
<h3>ORDENES DE CANCELACION</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<h3>ANULAR ORDEN #<?php echo $cancelacion['CancelacionOrden']['id']?></h3>
<?php echo $this->renderElement('cancelacion_orden/resumen',array('orden' => $cancelacion,'detalle_cuotas'=>false, 'detalle_forma_pago' => true, 'detalle_nueva_orden' => false, 'plugin' => 'mutual'));?>
<?php echo $this->renderElement('orden_descuento_cobro/resumen',array('cobro' => $cobro,'plugin' => 'mutual'));?>
<?php echo $frm->create(null,array('action' => 'anular/'.$cancelacion['CancelacionOrden']['id']))?>

<?php echo $frm->hidden('CancelacionOrden.id',array('value' => $cancelacion['CancelacionOrden']['id']))?>
<?php echo $frm->hidden('CancelacionOrden.socio_id',array('value' => $cancelacion['CancelacionOrden']['socio_id']))?>
<div class="notices_error" style="width:100%;">
	<strong>ATENCION!</strong>
	<br/><br/>
	La anulaci&oacute;n de una ORDEN DE CANCELACION implica:
	<br/>
	<ul>
		<li style='margin:5px 0px 5px 25px;list-style-type:square;padding:1px;text-indent:0px;'>Eliminar la Orden de Cancelaci&oacute;n.</li>
		<li style='margin:5px 0px 5px 25px;list-style-type:square;padding:1px;text-indent:0px;'>Eliminar el Cobro emitido.</li>
		<li style='margin:5px 0px 5px 25px;list-style-type:square;padding:1px;text-indent:0px;'>Eliminar la NC/ND emitida (si corresponde).</li>
		<li style='margin:5px 0px 5px 25px;list-style-type:square;padding:1px;text-indent:0px;'>Actualizar el per&iacute;odo de la cuota al per&iacute;odo original.</li>
		<li style='margin:5px 0px 5px 25px;list-style-type:square;padding:1px;text-indent:0px;'>Marcar las cuotas afectadas como Adeudadas.</li>
	</ul>
</div>
<div style="clear:both;"></div>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ANULAR LA ORDEN DE CANCELACION #' . $cancelacion['CancelacionOrden']['id'],'URL' => '/mutual/cancelacion_ordenes/by_socio/'.$cancelacion['CancelacionOrden']['socio_id']))?>
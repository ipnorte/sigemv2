<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin'=>'pfyj'))?>
<?php 
	if(MODULO_V1) echo "<h3>ANULAR ORDEN DE COMPRA #".$solicitud['MutualProductoSolicitud']['id']."</h3>";
	else echo "<h3>ANULAR SOLICITUD DE CONSUMO  #".$solicitud['MutualProductoSolicitud']['id']."</h3>";
?>
<?php echo $this->renderElement('mutual_producto_solicitudes/menu',array('persona' => $persona,'plugin'=>'mutual'))?>

<?php 

	if($solicitud['MutualProductoSolicitud']['tipo_orden_dto'] != Configure::read('APLICACION.tipo_orden_dto_credito')) echo $this->renderElement('mutual_producto_solicitudes/ficha',array('solicitud'=>$solicitud,'fPago'=>true,'plugin' => 'mutual'));
	else echo $this->renderElement('mutual_producto_solicitudes/ficha_solicitud_credito',array('solicitud'=>$solicitud,'fPago'=>true,'plugin' => 'mutual'));

?>
<hr/>
<div class="areaDatoForm">
	<div class='notices_error' style="width: 98%">
	<strong>ATENCION!</strong><br/>
	Anular la Solicitud significa DAR DE BAJA TODAS LAS CUOTAS ADEUDADAS (vencidas y a vencer) de las ORDENES DE DESCUENTO vinculadas a la presente.
	</div>
	<?php echo $form->create(null,array('action' => 'anular/'.$solicitud['MutualProductoSolicitud']['id'], 'id' => 'anular_orden', 'onsubmit' => "confirm('ANULAR LA SOLICITUD # ".$solicitud['MutualProductoSolicitud']['id']."?')"));?>
	<table class="tbl_form">
		<tr>
			<td><?php echo $frm->submit("ANULAR LA SOLICITUD #" . $solicitud['MutualProductoSolicitud']['id'])?></td>
		</tr>
	</table>
	<?php echo $frm->hidden('MutualProductoSolicitud.id',array('value' => $solicitud['MutualProductoSolicitud']['id']))?>
	<?php echo $frm->end()?>
	
</div>
<?php //   debug($solicitud)?>
<?php // debug($solicitud)?>
<?php if($solicitud['MutualProductoSolicitud']['socio_id'] != 0):?>
	<?php echo $this->renderElement('socios/apenom',array('socio_id' => $solicitud['MutualProductoSolicitud']['socio_id'], 'plugin' => 'pfyj'))?>
<?php else:?>
	<?php echo $this->renderElement('personas/datos_personales_info_header',array('persona_id'=>$solicitud['MutualProductoSolicitud']['persona_id'],'plugin' => 'pfyj'))?>
<?php endif;?>

<?php 

if($solicitud['MutualProductoSolicitud']['tipo_orden_dto'] != Configure::read('APLICACION.tipo_orden_dto_credito')){ echo $this->renderElement('mutual_producto_solicitudes/ficha',array('solicitud'=>$solicitud,'fPago'=>true,'plugin' => 'mutual'));}
else { echo $this->renderElement('mutual_producto_solicitudes/ficha_solicitud_credito',array('solicitud'=>$solicitud,'fPago'=>true,'plugin' => 'mutual'));}

?>

<?php //   echo $this->renderElement('mutual_producto_solicitudes/ficha_solicitud_credito',array('solicitud'=>$solicitud,'fPago'=>true,'plugin' => 'mutual'))?>
<?php //   echo $this->renderElement('mutual_producto_solicitudes/ficha',array('solicitud'=>$solicitud,'fPago'=>true,'plugin' => 'mutual'))?>

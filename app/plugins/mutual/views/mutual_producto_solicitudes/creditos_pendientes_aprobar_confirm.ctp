<?php echo $this->renderElement('head',array('title' => 'SOLICITUD DE CREDITO #'.$solicitud['MutualProductoSolicitud']['id'],'plugin' => 'config'))?>
<?php echo $this->requestAction('/mutual/mutual_producto_solicitudes/view/'.$solicitud['MutualProductoSolicitud']['id']."/0")?>
<h3>ORDEN DE DESCUENTO EMITIDA</h3>
<?php echo $this->requestAction('/mutual/orden_descuentos/view/'.$solicitud['MutualProductoSolicitud']['orden_descuento_id'])?>
<div class="areaDatoForm"><strong>
<?php echo $controles->btnImprimir('IMPRIMIR TALON DE CONTROL','/mutual/mutual_producto_solicitudes/imprimir_credito_pdf/'.$solicitud['MutualProductoSolicitud']['id']."/1",'blank');?>
</strong>
</div>
<h1>RESUMEN EXPEDIENTE Nro. <?php echo $nro_solicitud?></h1>
<hr>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link' => true,'plugin' => 'pfyj'))?>

<h3 style="border-bottom: 1px solid;">BENEFICIO POR EL CUAL SE DESCUENTA</h3>
<?php echo $this->renderElement('persona_beneficios/beneficio_by_ordendto', array('orden_dto_id' => $solicitud['Solicitud']['orden_descuento_id'], 'plugin' => 'pfyj'))?>
<?php //   echo $this->renderElement('persona_beneficios/beneficio_by_idr', array('idr' => $solicitud['Solicitud']['id_beneficio'], 'plugin' => 'pfyj'))?>

<h3 style="border-bottom: 1px solid;">LIQUIDACION Y PAGO DEL PRESTAMO</h3>
<?php echo $this->renderElement('solicitudes/grilla_liquidacion', array('solicitud' => $solicitud, 'plugin' => 'v1'))?>

<?php if(count($solicitud['SolicitudCancelacionOrden'])!=0):?>

	<h3 style="border-bottom: 1px solid;">ORDENES DE CANCELACION</h3>
		<?php echo $this->renderElement('solicitud_cancelaciones/ordenes_cancelacion_info_pago', array('cancelaciones' => $solicitud['SolicitudCancelacionOrden'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>
	
<?php elseif(count($solicitud['Cancelaciones'])!=0):?>
	<h3 style="border-bottom: 1px solid;">CANCELACIONES</h3>
		<?php echo $this->renderElement('solicitud_cancelaciones/cancelaciones_info_pago', array('cancelaciones' => $solicitud['Cancelaciones'],'persona_id' => $persona['Persona']['id'], 'plugin' => 'v1'))?>

<?php endif;?>

<?php if($solicitud['Solicitud']['reasignar_proveedor_id'] != 0):?>
	
	<div class='notices_error' style="width: 100%">
		<strong>ATENCION!</strong><br/>
		La presente solicitud fu&eacute; marcada para ser reasignada el <?php echo date("d-m-Y", strtotime($solicitud['Solicitud']['reasigna_proveedor_fecha']))?> por
		el usuario <strong><?php echo $solicitud['Solicitud']['reasigna_proveedor_user']?></strong>.
		La Orden de Descuento que se emitir&aacute; ser&aacute; asignada a <strong><?php echo $solicitud['Solicitud']['reasignar_proveedor_razon_social']?></strong>
	</div>
	<div style="clear: both;"></div>

<?php endif;?>


<h3 style="border-bottom: 1px solid;">ORDENES DE DESCUENTO EMITIDAS</h3>
<?php echo $this->renderElement('orden_descuento/by_numero', array('tipo' => 'EXPTE','numero'=> $nro_solicitud, 'plugin' => 'mutual'))?>

<!--<h3 style="border-bottom: 1px solid;">OBSERVACIONES</h3>-->



<p>&nbsp;</p>
<?php echo $controles->botonGenerico('/v1/solicitudes/caratula_expediente_pdf/'.$nro_solicitud,'controles/pdf.png','IMPRIMIR CARATULA EXPEDIENTE',array('target' => 'blank'))?>
<?php //   echo $frm->btnForm(array('LABEL' => 'IMPRIMIR CARATULA EXPEDIENTE','URL' => '/v1/solicitudes/caratula_expediente_pdf/'.$nro_solicitud))?>
<?php //   debug($solicitud)?>
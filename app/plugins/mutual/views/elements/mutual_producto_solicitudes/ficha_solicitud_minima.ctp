<div class="areaDatoForm3">
	<h4>ORDEN DE CONSUMO / SERVICIO :: 
	<?php echo $solicitud['MutualProductoSolicitud']['tipo_orden_dto']?> #<?php echo $solicitud['MutualProductoSolicitud']['id']?>
	<?php if($solicitud['MutualProductoSolicitud']['sin_cargo'] == 1) echo " *** SIN CARGO ***"?>
	</h4>
	<div classs="row">
		FECHA CARGA: <?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?>
		&nbsp;|&nbsp;FECHA PAGO: <?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha_pago'])?>
		&nbsp;|&nbsp;ESTADO: <strong><?php echo $util->globalDato($solicitud['MutualProductoSolicitud']['estado'])?><?php // echo ($solicitud['MutualProductoSolicitud']['aprobada'] == 1 ? 'APROBADA' : 'EMITIDA')?></strong>
	</div>
	<div classs="row">
		INICIA EN: <strong><?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?></strong>
		&nbsp;|&nbsp;1er. VTO SOCIO: <strong><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['primer_vto_socio'])?></strong>
	</div>
	<div classs="row">
		PROVEEDOR - PRODUCTO: <strong><?php echo $solicitud['MutualProductoSolicitud']['proveedor_producto']?></strong>
	</div>
	<div classs="row">
		IMPORTE CUOTA: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_cuota'],2);?></strong>
		<?php echo ($solicitud['MutualProductoSolicitud']['permanente'] == 0 ? ' ('.$solicitud['MutualProductoSolicitud']['cuotas']. ' CUOTAS) ' : ' (PERMANENTE)')?>
		<?php if($solicitud['MutualProductoSolicitud']['permanente'] == 0):?>
		&nbsp;|&nbsp;TOTAL: <strong><?php echo number_format($solicitud['MutualProductoSolicitud']['importe_total'],2);?></strong>
		<?php endif;?>
	</div>
	<div classs="row">
		BENEFICIO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_str']?></strong>
	</div>
	<?php if(!empty($solicitud['MutualProductoSolicitud']['observaciones'])):?>
		<div class="areaDatoForm2" style="font-size: x-small;">
		<?php echo $solicitud['MutualProductoSolicitud']['observaciones']?>
		</div>
	<?php endif;?>

</div>
<div style="clear: both;"></div>
<?php //   debug($solicitud)?>
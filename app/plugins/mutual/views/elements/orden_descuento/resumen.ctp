<div class="areaDatoForm3">
	<?php // debug($orden)?>
	<h4>ORDEN DE DESCUENTO <?php echo '#'.$orden['OrdenDescuento']['id']?></h4>	
	<div classs="row">
		REF: <strong><?php echo $orden['OrdenDescuento']['tipo_orden_dto']?>
		#<?php echo $orden['OrdenDescuento']['numero']?> </strong>
	</div>
	<div classs="row">
		PRODUCTO: <?php echo $orden['OrdenDescuento']['proveedor_resumido']?> - 
		<strong><?php echo $this->requestAction('/config/global_datos/valor/'.$orden['OrdenDescuento']['tipo_producto'])?></strong>
	</div>
	<div classs="row">
		BENEFICIO: <strong><?php echo $this->requestAction('/pfyj/persona_beneficios/view/'.$orden['OrdenDescuento']['persona_beneficio_id'])?></strong>
	</div>
	<div classs="row">
		INICIA: <strong><?php echo $util->periodo($orden['OrdenDescuento']['periodo_ini'])?></strong>
		<?php //   echo ($orden['OrdenDescuento']['permanente'] == 1 ? ' (PERMANENTE)' : '')?>
		&nbsp;	&nbsp;
		VTO 1er CUOTA: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_socio'])?></strong>
		&nbsp;	&nbsp;
		VTO PROVEEDOR: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_proveedor'])?></strong>
	</div>
	<div classs="row">
		IMPORTE CUOTA: <strong><?php echo number_format($orden['OrdenDescuento']['importe_cuota'],2)?></strong>
		<?php echo ($orden['OrdenDescuento']['permanente'] == 0 ? ' ('.$orden['OrdenDescuento']['cuotas']. ' CUOTAS) ' : ' (PERMANENTE)')?>
		<?php if($orden['OrdenDescuento']['permanente'] == 0):?>
			&nbsp;	&nbsp;	
			IMPORTE TOTAL: <strong><?php echo number_format($orden['OrdenDescuento']['importe_total'],2)?></strong>
		<?php endif;?>
	</div>
</div>
<?php if(isset($detallaCuotas) && $detallaCuotas):?>
		<div class="areaDatoForm3">
			<h4>DETALLE DE CUOTAS</h4>
			<p>&nbsp;</p>
			<?php echo $this->renderElement('orden_descuento_cuotas/grilla_cuotas',array('plugin'=>'mutual','orden_descuento_id' => $orden['OrdenDescuento']['id']))?>
		</div>
<?php endif;?>
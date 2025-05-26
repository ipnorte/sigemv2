<h3>VISTA PREVIA DE LA REPROGRAMACION</h3>
<!--<div class="notices"><strong>ATENCION!: </strong> Verifique que las fechas sean las correctas antes de confirmar la reprogramaci&oacute;n.</div>-->
<div class="areaDatoForm3">
	<?php //   debug($orden)?>
	<?php //   debug($ultimoPeriodoCerrado)?>
	<h4>ORDEN DE DESCUENTO <?php echo '#'.$orden['OrdenDescuento']['id']?></h4>	
	<div classs="row">
		REF: <strong><?php echo $orden['OrdenDescuento']['tipo_orden_dto']?>
		#<?php echo $orden['OrdenDescuento']['numero']?> </strong>
		&nbsp;|&nbsp;<span style="color:red;padding: 0px;">FECHA ORDEN DTO.: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['fecha'])?></strong></span>
	</div>
	<div classs="row">
		PRODUCTO: <?php echo $orden['Proveedor']['razon_social_resumida']?> - 
		<strong><?php echo $this->requestAction('/config/global_datos/valor/'.$orden['OrdenDescuento']['tipo_producto'])?></strong>
	</div>
	<div classs="row">
		BENEFICIO: <strong><?php echo $this->requestAction('/pfyj/persona_beneficios/view/'.$orden['OrdenDescuento']['persona_beneficio_id'])?></strong>
	</div>
	<div classs="row">
		<span style="color:red;padding: 0px;">INICIA: <strong><?php echo $util->periodo($orden['OrdenDescuento']['periodo_ini'])?></strong></span>
		<?php //   echo ($orden['OrdenDescuento']['permanente'] == 1 ? ' (PERMANENTE)' : '')?>
		&nbsp;	&nbsp;
		<span style="color:red;padding: 0px;">VTO 1er CUOTA: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_socio'])?></strong></span>
		&nbsp;	&nbsp;
		<span style="color:red;padding: 0px;">VTO PROVEEDOR: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_proveedor'])?></strong></span>
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
<?php if(count($orden['OrdenDescuentoCuota']) != 0):?>

	<?php if($ultimoPeriodoCerrado >= $orden['OrdenDescuentoCuota'][0]['periodo']):?>

	<div class='notices_error' style="width: 100%">
		<strong>ATENCION!!!</strong><br/>
		La cuota <strong><?php echo $orden['OrdenDescuentoCuota'][0]['nro_cuota']?></strong> se reprograma para el 
		per&iacute;odo <strong><?php echo $util->periodo($orden['OrdenDescuentoCuota'][0]['periodo'],true)?></strong>
		y el <u>&uacute;ltimo per&iacute;odo cerrado</u> para <strong><?php echo $orden['OrdenDescuento']['organismo']?></strong> 
		es <strong><?php echo $util->periodo($ultimoPeriodoCerrado,true)?></strong>.<br/>
		Las cuotas ser&aacute;n tratadas como <strong>DEUDA VENCIDA</strong>.
	</div>	
	
	<?php endif;?>

	<table>
		
		<tr>
			<th colspan="9">DETALLE DE CUOTAS ADEUDADAS A REPROGRAMAR</th>
		</tr>
		
		<tr>
			<th>CUOTA</th>
			<th>NUEVO PERIODO</th>
			<th>NUEVO VTO</th>
			<th>CONCEPTO</th>
			<th>ESTADO</th>
			<th>SIT</th>
			<th>IMPORTE</th>
			<th></th>
			<th></th>
		</tr>	
		
		<?php
			$ACUM = 0; 
			foreach($orden['OrdenDescuentoCuota'] as $cuota):
	  		$bloqueo = array();
	  		if(!empty($cuota['bloqueo_liquidacion'])) $bloqueo = $cuota['bloqueo_liquidacion'];
			
		?>
			<tr class="<?php echo $cuota['estado']?>">
				<td align="center"><strong><?php echo $cuota['nro_cuota'].'/'. $orden['OrdenDescuento']['cuotas']?></strong></td>
				<td align="center"><strong><?php echo $util->periodo($cuota['periodo'])?></strong></td>
				<td align="center"><strong><?php echo $util->armaFecha($cuota['vencimiento'])?></strong></td>
				<td><?php echo $cuota['tipo_cuota_desc']?></td>
				<td><?php echo $cuota['estado_desc']?></td>
				<td><?php echo $cuota['situacion_desc']?></td>
				<td align="right"><strong><?php echo ($cuota['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['importe'],2).'</span>' : number_format($cuota['importe'],2)) ?></strong></td>
				<td align="center"><?php echo $controles->btnModalBox(array('title' => 'DETALLE CUOTA','url' => '/mutual/orden_descuento_cuotas/view/'.$cuota['id'],'h' => 450, 'w' => 750))?></td>
				<td>
					<?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>
					<span style="color: red;"><strong>ERROR! </strong><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></span>
					<?php endif;?>
				</td>
			</tr>
			
		<?php endforeach;?>

	</table>
	
<?php endif;?>
<div class="notices"><strong>ATENCION!: </strong> Verifique que las fechas sean las correctas antes de confirmar la reprogramaci&oacute;n.</div>
	<?php if($ultimoPeriodoCerrado >= $orden['OrdenDescuentoCuota'][0]['periodo']):?>

	<div class='notices_error' style="width: 100%">
		<strong>ATENCION!!!</strong><br/>
		La cuota <strong><?php echo $orden['OrdenDescuentoCuota'][0]['nro_cuota']?></strong> se reprograma para el 
		per&iacute;odo <strong><?php echo $util->periodo($orden['OrdenDescuentoCuota'][0]['periodo'],true)?></strong>
		y el <u>&uacute;ltimo per&iacute;odo cerrado</u> para <strong><?php echo $orden['OrdenDescuento']['organismo']?></strong> 
		es <strong><?php echo $util->periodo($ultimoPeriodoCerrado,true)?></strong>.<br/>
		Las cuotas ser&aacute;n tratadas como <strong>DEUDA VENCIDA</strong>.
	</div>	
	
	<?php endif;?>	

<div class="row">
	<br/>
	<?php echo $frm->btnForm(array('LABEL' => 'CONFIRMAR LA REPROGRAMACION','URL' => '/mutual/orden_descuentos/reprogramar/?do=REPRO&token_id='.$TOKEN))?>
</div>
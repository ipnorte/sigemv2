<?php echo $this->renderElement('socios/apenom',array('socio_id' => $pago['SocioReintegroPago']['socio_id'], 'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('socio_reintegros/ficha',array('reintegro' =>$reintegro, 'plugin' => 'pfyj'))?>
<div class="areaDatoForm3">
	<div classs="row">
		FORMA DE PAGO: <strong><?php echo $pago['SocioReintegroPago']['forma_pago_desc']?></strong>
	</div>
	<?php if(!empty($pago['SocioReintegroPago']['banco_id'])):?>
	<div classs="row">
		BANCO: <strong><?php echo $util->banco($pago['SocioReintegroPago']['banco_id'])?></strong>
	</div>	
	<?php endif;?>	
	<?php if(!empty($pago['SocioReintegroPago']['nro_operacion'])):?>
	<div classs="row">
		NRO. OPERACION: <strong><?php echo $pago['SocioReintegroPago']['nro_operacion']?></strong>
	</div>	
	<?php endif;?>
	<div classs="row">
		FECHA: <strong><?php echo $util->armaFecha($pago['SocioReintegroPago']['fecha_operacion'])?></strong>
	</div>
	<div classs="row">
		IMPORTE TOTAL: <strong><?php echo $util->nf($pago['SocioReintegroPago']['importe'])?></strong>
	</div>				
</div>
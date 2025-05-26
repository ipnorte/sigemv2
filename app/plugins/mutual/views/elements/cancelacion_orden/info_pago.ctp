<div class="areaDatoForm" style="font-size:11px;">
<h4>DATOS DEL PAGO</h4>
<div class="row">
	<strong><?php echo $this->requestAction('/config/global_datos/valor/'.$cancelacion['CancelacionOrden']['forma_cancelacion'])?></strong>
	&nbsp;|&nbsp; FECHA IMPUTACION: <?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_imputacion'])?>
	<?php echo ($cancelacion['CancelacionOrden']['pendiente_rendicion_proveedor'] == 1 ? '&nbsp;<span style="color:red;">(PENDIENTE)</span>' : '')?>
</div>
	<?php if($cancelacion['CancelacionOrden']['forma_cancelacion'] == 'MUTUTICA0002'):?>
	<div class="row">
		FORMA DE PAGO:&nbsp;<strong><?php echo $this->requestAction('/config/global_datos/valor/'.$cancelacion['CancelacionOrden']['forma_pago'])?></strong>
	</div>
	<?php if($cancelacion['CancelacionOrden']['forma_pago'] != "MUTUFPAG0001"):?>
		<div class="row">
			<strong><?php if(isset($cancelacion['CancelacionOrden']['banco_id']) && $cancelacion['CancelacionOrden']['banco_id'] != 0) echo $this->requestAction('/config/bancos/nombre/'.$cancelacion['CancelacionOrden']['banco_id'])?></strong>
		</div>
		<div class="row">
			CUENTA: <?php echo $cancelacion['CancelacionOrden']['nro_cta_bco']?>
		</div>
		<div class="row">
			NRO.OPERACION: <?php echo $cancelacion['CancelacionOrden']['nro_operacion']?>
		</div>
	<?php endif;?>
<?php endif;?>
</div>
<?php //   debug($cancelacion)?>
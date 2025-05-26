<div class="areaDatoForm" style="font-size:11px;">
	<h3>INFORMACION DE LA ORDEN DE CANCELACION #<?php echo $orden['CancelacionOrden']['id']?></h3>
	<div class="row">
	TIPO: <strong><?php echo $orden['CancelacionOrden']['tipo_cancelacion_desc']?></strong>
	|
	ORDEN DTO: <?php echo $orden['CancelacionOrden']['orden_descuento_id']?> - <strong><?php echo $orden['CancelacionOrden']['tipo_nro_odto']?></strong>
	</div>
	<div class="row">
		PROVEEDOR/PRODUCTO: <strong><?php echo $orden['CancelacionOrden']['proveedor_producto_odto']?></strong>
	</div>
	<div class="row">
		A LA ORDEN DE: <strong><?php echo $orden['CancelacionOrden']['a_la_orden_de']?></strong>
	</div>	
	<div class="row" style="margin-top:3px;margin-bottom: 3px;">
		<span style="background-color:green;color:white;padding:2px;">DEUDA PROVEEDOR: <strong style="font-size:13px;"><?php echo $util->nf($orden['CancelacionOrden']['importe_proveedor'])?></strong>
		|
		VENCIMIENTO: <strong style="font-size:13px;"><?php echo $util->armaFecha($orden['CancelacionOrden']['fecha_vto'])?></strong></span>
	</div>
	<div class="row">
		SALDO ORDEN DTO: <?php echo $util->nf($orden['CancelacionOrden']['saldo_orden_dto'])?>
		|
		TOTAL ORDEN DE CANCELACION: <strong><?php echo $util->nf($orden['CancelacionOrden']['importe_seleccionado'])?></strong>
	</div>
	<?php //   if($orden['CancelacionOrden']['importe_proveedor'] < $orden['CancelacionOrden']['importe_seleccionado']):?>
	<?php if($orden['CancelacionOrden']['importe_diferencia'] < 0):?>
	<div class="row" style="color:#B30000;background-color:#FFBBBB;border: 1px solid #B30000;padding: 2px; margin: 2px;">
		Concepto Generado en Cuenta Corriente:<br/>
		<strong><?php echo $util->globalDato($orden['CancelacionOrden']['tipo_cuota_diferencia'])?>: <?php echo $util->nf($orden['CancelacionOrden']['importe_diferencia'])?></strong>
		&nbsp;(DEUDA PROVEEDOR - TOTAL ORDEN DE CANCELACION)
	</div>
	<?php endif;?>
	<?php if($orden['CancelacionOrden']['importe_diferencia'] > 0):?>
	<div class="row" style="color:#008000;background-color:#D1EABF;border: 1px solid #008000;padding: 2px; margin: 2px;">
		Concepto Generado en Cuenta Corriente:<br/>
		<strong><?php echo $util->globalDato($orden['CancelacionOrden']['tipo_cuota_diferencia'])?>: <?php echo $util->nf($orden['CancelacionOrden']['importe_diferencia'])?></strong>
		&nbsp;(DEUDA PROVEEDOR - TOTAL ORDEN DE CANCELACION)
	</div>
	<?php endif;?>	
	<?php if(!empty($orden['CancelacionOrden']['observaciones'])):?>
		<div class="areaDatoForm3" style="width: 98%;">
		<?php echo $orden['CancelacionOrden']['observaciones']?>
		</div>
	<?php endif;?>
</div>
<?php if((isset($detalle_forma_pago) ? $detalle_forma_pago : true)) echo (isset($orden['CancelacionOrden']['forma_cancelacion']) ? $this->renderElement('cancelacion_orden/info_pago',array('cancelacion' => $orden,'plugin' => 'mutual')) : '')?>
<?php if((isset($detalle_nueva_orden) ? $detalle_nueva_orden : true)):?>
<div style="font-size:11px;">
	<?php if($orden['CancelacionOrden']['nueva_orden_dto_id'] != 0):?>
		<h4>NUEVA ORDEN DE DESCUENTO</h4>
		<?php echo $this->renderElement('orden_descuento/resumen_by_id',array('id' => $orden['CancelacionOrden']['nueva_orden_dto_id'],'plugin' => 'mutual'))?>
	<?php endif;?>	
</div>
<?php endif;?>
<?php if((isset($detalle_cuotas) ? $detalle_cuotas : false)):?>
<div id="detalle_cuotas">
	<?php echo $this->renderElement('cancelacion_orden_cuotas/grilla_cuotas',array('orden_cancelacion_id' => $orden['CancelacionOrden']['id'], 'plugin' => 'mutual'));?>
</div>
<?php endif;?>
<?php echo $this->renderElement('head',array('title' => 'NOVAR ORDEN DE DESCUENTO','plugin' => 'config'))?>
<?php echo $this->renderElement('orden_descuento/form_search_by_numero',array('accion' => 'novar','plugin' => 'mutual','orden_descuento_id' => $ordenId))?>
<?php if($ordenId != 0):?>
	
	<h3>CONSULTA DE ORDEN DE DESCUENTO</h3>

	<?php echo $this->requestAction("/mutual/orden_descuentos/view/$ordenId/$socioId/1/0/1")?>
	<form id="OrdenDescuentoNovarFormProcess" method="post" action="<?php echo $this->base?>/mutual/orden_descuentos/novar" onsubmit="return confirm('***ATENCION! ***\nNovar la Orden de Descuento #<?php echo $ordenId?>\nSe Anular esta Orden y se emitir bajo un numero nuevo')">
	<div class="areaDatoForm">
		<h3>NOVAR ORDEN</h3>
		<table class="tbl_form">
			<tr>
				<td>MOTIVO</td><td><?php echo $frm->textarea('OrdenDescuento.motivo_novacion',array('cols' => 60, 'rows' => 10, 'value' => ""))?></td>
			</tr>
			<tr>
				<td></td><td><input type="submit" value="NOVAR ORDEN"/></td>
			</tr>
		</table>
		
	</div>
	<?php echo $frm->hidden('OrdenDescuento.anterior_orden_descuento_id',array('value' => $ordenId))?>
	</form>
<?php endif;?>
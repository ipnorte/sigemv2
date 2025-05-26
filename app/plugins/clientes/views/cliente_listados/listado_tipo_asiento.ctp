<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('cliente_listados/menu_listado',array('plugin' => 'clientes'))?>
<h1>LISTADO POR TIPO DE ASIENTO</h1>
<hr>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_tipo_asiento').disable();
	<?php endif;?>

});
</script>
<?php
?>
<?php echo $frm->create(null,array('action' => 'listado_tipo_asiento','id' => 'form_tipo_asiento'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>FECHA DESDE:</td>
			<td><?php echo $frm->calendar('ListadoTipoAsiento.fecha_desde', null, $fecha_desde, '2000',date("Y") + 1)?></td>
		</tr>
		
		<tr>
			<td>FECHA HASTA:</td>
			<td><?php echo $frm->calendar('ListadoTipoAsiento.fecha_hasta', null, $fecha_hasta, '2000',date("Y") + 1)?></td>
		</tr>
		
		<tr>
			<td><?php echo $frm->submit("ACEPTAR")?></td>
		</tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

 	
<?php if(isset($showTabla) && $showTabla == 1):

?>

<div class="areaDatoForm">

	<?php 
		echo $controles->botonGenerico('/clientes/cliente_listados/listado_tipo_asiento_xls/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/clientes/cliente_listados/listado_tipo_asiento_pdf/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>
	
<table align="center">

		
	<tr border="0">
		<th style="font-size: small;">TIPO ASIENTO</th>
		<th style="font-size: small;">FACTURAS</th>
		<th style="font-size: small;">N.CREDITOS</th>
		<th style="font-size: small;">INF.DETALLADO</th>
	</tr>

	<?php
	foreach ($aTipoAsiento as $TipoAsiento):
	?>
		<tr>
			<td style="font-size: x-small;"><?php echo $TipoAsiento['ClienteTipoAsiento']['concepto']?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($TipoAsiento['0']['facturado'],2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($TipoAsiento['0']['credito'],2)?></td>
			<td align="center" style="font-size: small;">
				<?php 
				echo $controles->botonGenerico('/clientes/cliente_listados/listado_tipo_asiento_detalle/' . $TipoAsiento['ClienteTipoAsiento']['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/HTML-globe.png', null, array('id' => 'html'));
				echo $controles->botonGenerico('/clientes/cliente_listados/listado_tipo_asiento_detalle_xls/' . $TipoAsiento['ClienteTipoAsiento']['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
				echo $controles->botonGenerico('/clientes/cliente_listados/listado_tipo_asiento_detalle_pdf/' . $TipoAsiento['ClienteTipoAsiento']['id'] . '/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
	
	<?php 
		echo $controles->botonGenerico('/clientes/cliente_listados/listado_tipo_asiento_xls/' . $fecha_desde . '/' . $fecha_hasta,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/clientes/cliente_listados/listado_tipo_asiento_pdf/' . $fecha_desde . '/' . $fecha_hasta,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>	
	
</div>
<?php endif;?>
 	
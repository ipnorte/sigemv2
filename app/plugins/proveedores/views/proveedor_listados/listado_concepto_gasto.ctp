<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('proveedor_listados/menu_listado',array('plugin' => 'proveedores'))?>
<h1>LISTADO POR CONCEPTO DEL GASTO</h1>
<hr>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_concepto_gasto').disable();
	<?php endif;?>

});
</script>
<?php
?>
<?php echo $frm->create(null,array('action' => 'listado_concepto_gasto','id' => 'form_concepto_gasto'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>FECHA DESDE:</td>
			<td><?php echo $frm->calendar('ListadoConceptoGasto.fecha_desde', null, $fecha_desde, '2000',date("Y") + 1)?></td>
			<td>HASTA:</td>
			<td><?php echo $frm->calendar('ListadoConceptoGasto.fecha_hasta', null, $fecha_hasta, '2000',date("Y") + 1)?></td>
		</tr>
		
		<tr>
			<td>TIPO PROVEEDOR:</td>
			<td><?php echo $frm->input('ListadoConceptoGasto.tipo',array('type'=>'select','options'=>array(3 => 'TODOS', 0 => 'PROVEEDOR', 1 => 'COMERCIO'), 'selected' => $tipo));?></td>
			<td><?php echo $frm->submit("ACEPTAR")?></td>
		</tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

 	
<?php if(isset($showTabla) && $showTabla == 1):

?>

<div class="areaDatoForm">

	<?php 
		echo $controles->botonGenerico('/proveedores/proveedor_listados/listado_concepto_gasto_xls/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/proveedores/proveedor_listados/listado_concepto_gasto_pdf/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>
	
<table align="center">

		
	<tr border="0">
		<th style="font-size: small;">CONCEPTO DEL GASTO</th>
		<th style="font-size: small;">FACTURAS</th>
		<th style="font-size: small;">N.CREDITOS</th>
		<th style="font-size: small;">INF.DETALLADO</th>
	</tr>

	<?php
	foreach ($aConceptoGasto as $ConceptoGasto):
		if($ConceptoGasto[0]['facturado'] > 0 || $ConceptoGasto[0]['credito'] > 0):
	?>
		<tr>
			<td style="font-size: x-small;"><?php echo $ConceptoGasto[0]['concepto_1']?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($ConceptoGasto['0']['facturado'],2)?></td>
			<td align="right" style="font-size: small;"><?php echo number_format($ConceptoGasto['0']['credito'],2)?></td>
			<td align="center" style="font-size: small;">
				<?php 
				echo $controles->botonGenerico('/proveedores/proveedor_listados/listado_concepto_gasto_detalle/' . $ConceptoGasto[0]['id'] . '/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/HTML-globe.png', null, array('id' => 'html'));
				echo $controles->botonGenerico('/proveedores/proveedor_listados/listado_concepto_gasto_detalle_xls/' . $ConceptoGasto[0]['id'] . '/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/ms_excel.png', null, array('id' => 'xls'));
				echo $controles->botonGenerico('/proveedores/proveedor_listados/listado_concepto_gasto_detalle_pdf/' . $ConceptoGasto[0]['id'] . '/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
				?>
			</td>
		</tr>
	<?php
		endif; 
	endforeach; ?>
</table>
	
	<?php 
		echo $controles->botonGenerico('/proveedores/proveedor_listados/listado_concepto_gasto_xls/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/ms_excel.png', null, array('id' => 'xls'));
		echo $controles->botonGenerico('/proveedores/proveedor_listados/listado_concepto_gasto_pdf/' . $fecha_desde . '/' . $fecha_hasta . '/' . $tipo,'controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));
	?>	
	
</div>
<?php endif;?>
 	
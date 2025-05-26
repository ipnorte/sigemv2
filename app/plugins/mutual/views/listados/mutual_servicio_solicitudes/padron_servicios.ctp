<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('listados/menu_listados',array('plugin' => 'mutual'))?>
<h3>PADRON DE ORDENES DE SERVICIOS</h3>

<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_servicio_padron').disable();
	<?php endif;?>

	document.getElementById("ListadoServiceFechaCoberturaDesdeDay").value = "1";
	$('ListadoServiceFechaCoberturaDesdeDay').disable();
	
});
</script>
<?php echo $frm->create(null,array('action' => 'padron_servicios','id' => 'form_servicio_padron'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>SERVICIO</td><td><?php echo $this->renderElement('mutual_servicios/combo_servicios',array('plugin' => 'mutual','model' => 'ListadoService','solo_activos' => 0,'selected' => $this->data['ListadoService']['tipo_servicio_mutual_producto_id']))?></td>
		</tr>
		<tr>
			<td>COBERTURA DESDE</td><td><?php echo $frm->calendar('ListadoService.fecha_cobertura_desde','',$fecha_cobertura_desde,date("Y") - 3,date("Y") + 1)?></td>
		</tr>
		
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

<?php if($show_asincrono == 1):?>

	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'listado_padron_servicio_sp',
											'accion' => '.mutual.listados.padron_servicios.XLS',
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "PADRON DE ASOCIADOS AL SERVICIO",
											'subtitulo' => $servicio_desc . ' - Cob.desde '.$util->armaFecha($fecha_cobertura_desde),
											'p1' => $servicio_id,
											'p2' => $fecha_cobertura_desde
	));
	
	?>


<?php endif;?>
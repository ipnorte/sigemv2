<?php echo $this->renderElement('head',array('title' => 'LISTADOS','plugin' => 'config'))?>
<?php echo $this->renderElement('listados/menu_listados',array('plugin' => 'mutual'))?>

<h2>REPORTES INAES</h2>
<?php 
$tabs = array(
				0 => array('url' => '/mutual/listados/reporte_inaes','label' => 'LISTADO CONSUMOS', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/mutual/listados/reporte_inaesA9/','label' => 'ARTICULO 9', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/mutual/listados/reporte_inaesbe','label' => 'BALANCE ELECTRONICO', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs,false);			
?>

<h3>LISTADO CONSUMOS</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('frm_listado_inaes').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'reporte_inaes','id' => 'frm_listado_inaes'))?>
	<table class="tbl_form">
		<tr>
			<td>FECHA CORTE</td><td><?php echo $frm->calendar('ListadoService.fecha_corte','',$fecha_corte,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>FORMATO</td><td><?php echo $frm->input('ListadoService.tipo_salida',array('type' => 'select', 'options' => array('XLS' => 'XLS'), 'label' => null,'empty' => false, 'selected' => 'PDF'))?></td>
		</tr>		
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if($show_asincrono == 1):?>
	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'listado_inaes',
											'accion' => '.mutual.listados.reporte_inaes.'.$tipo_salida,
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "LISTADO INAES - FORMATO $tipo_salida",
											'subtitulo' => 'Fecha de Corte '.$util->armaFecha($fecha_corte),
											'p1' => $fecha_corte,
	));
	
	?>
<?php endif?>
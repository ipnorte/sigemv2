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

<h3>BALANCE ELECTRONICO</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('frm_listado_inaesbe').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'reporte_inaesbe','id' => 'frm_listado_inaesbe'))?>
	<table class="tbl_form">
		<tr>
			<td>FECHA DESDE</td>
			<td><?php echo $frm->calendar('ListadoService.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
		</tr>
		<tr>	
			<td>FECHA HASTA</td>
			<td><?php echo $frm->calendar('ListadoService.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td><?php echo $frm->submit("CONSULTAR")?></td>
		</tr>
	</table>
	<?php echo $frm->end()?>
</div>
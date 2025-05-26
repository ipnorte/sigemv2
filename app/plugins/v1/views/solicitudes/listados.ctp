<?php echo $this->renderElement('solicitudes/menu_listados',array('plugin' => 'v1'))?>

<h3>CONTROL VENTAS PRODUCTORES</h3>

<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($show_asincrono == 1):?>
		$('form_productores_vtas').disable();
	<?php endif;?>
});
</script>

<?php echo $frm->create(null,array('action' => 'listados','id' => 'form_productores_vtas'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>PERIODO LIQUIDADO (COMISIONES)</td><td><?php echo $frm->input("Productor.periodo_liquidado",array('type' => 'select','options' => $periodos))?></td>
		</tr>
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

<?php if($show_asincrono == 1):?>

	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'solicitudes_listado_ventas_productores',
											'accion' => '.v1.solicitudes.listados.XLS',
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "CONTROL VENTAS PRODUCTORES",
											'subtitulo' => 'PERIODO '.$util->periodo($this->data['Productor']['periodo_liquidado'],true,'-'),
											'p1' => $this->data['Productor']['periodo_liquidado'],
	));
	
	?>


<?php endif;?>

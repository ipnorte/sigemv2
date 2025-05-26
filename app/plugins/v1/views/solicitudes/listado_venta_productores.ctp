<?php echo $this->renderElement('solicitudes/menu_listados',array('plugin' => 'v1'))?>


<h3>CONTROL VENTAS PRODUCTORES</h3>

<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($show_asincrono == 1):?>
		$('form_productores_vtas').disable();
	<?php endif;?>
});
</script>

<?php echo $frm->create(null,array('action' => 'listados/1','id' => 'form_productores_vtas'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>PERIODO LIQUIDADO (COMISIONES DESDE)</td>
			<td><?php echo $frm->input("Productor.periodo_liquidado_desde",array('type' => 'select','options' => $periodos))?></td>
		</tr>
		<tr>	
			<td>PERIODO LIQUIDADO (COMISIONES HASTA)</td>
			<td><?php echo $frm->input("Productor.periodo_liquidado_hasta",array('type' => 'select','options' => $periodos))?></td>
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
											'accion' => '.v1.solicitudes.listados.1.XLS',
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "CONTROL VENTAS PRODUCTORES",
											'subtitulo' => 'PERIODO DESDE '.$util->periodo($this->data['Productor']['periodo_liquidado_desde'],true,'-')." HASTA ".$util->periodo($this->data['Productor']['periodo_liquidado_hasta'],true,'-'),
											'p1' => $this->data['Productor']['periodo_liquidado_desde'],
											'p2' => $this->data['Productor']['periodo_liquidado_hasta'],
	));
	
	?>


<?php endif;?>

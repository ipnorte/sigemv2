<?php echo $this->renderElement('solicitudes/menu_listados',array('plugin' => 'v1'))?>

<h3>CONTROL VENTAS PROVEEDORES</h3>

<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($show_asincrono == 1):?>
		$('form_proveedores_vtas').disable();
	<?php endif;?>
});
</script>

<?php echo $frm->create(null,array('action' => 'listados/2','id' => 'form_proveedores_vtas'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td>PROVEEDOR</td>
			<td><?php echo $frm->input("Proveedor.proveedor",array('type' => 'select','options' => $proveedores, 'empty' => true))?></td>
		</tr>
		<tr>
			<td>DESDE</td>
			<td><?php echo $frm->input("Proveedor.periodo_liquidado_desde",array('type' => 'select','options' => $periodos))?></td>
		</tr>
		<tr>	
			<td>HASTA</td>
			<td><?php echo $frm->input("Proveedor.periodo_liquidado_hasta",array('type' => 'select','options' => $periodos))?></td>
		</tr>		
		
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR LISTADO")?></td></tr>
	
	</table>
	
</div>
<?php echo $frm->end()?>

<?php if($show_asincrono == 1):?>

	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'solicitudes_listado_ventas_proveedores',
											'accion' => '.v1.solicitudes.listados.2.XLS',
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "CONTROL VENTAS PROVEEDORES",
											'subtitulo' => 'PERIODO DESDE '.$util->periodo($this->data['Proveedor']['periodo_liquidado_desde'],true,'-').' HASTA '.$util->periodo($this->data['Proveedor']['periodo_liquidado_hasta'],true,'-'),
											'p1' => $this->data['Proveedor']['periodo_liquidado_desde'],
											'p2' => $this->data['Proveedor']['periodo_liquidado_hasta'],
											'p3' => $this->data['Proveedor']['proveedor'],
	));
	
	?>


<?php endif;?>


<?php 
// debug($proveedores)
?>
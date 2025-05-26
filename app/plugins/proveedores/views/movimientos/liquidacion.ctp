<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedor, 'plugin' => 'proveedores'))?>
<h3>RESUMEN DE LIQUIDACIONES</h3>

<?php debug($liquidaciones)?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
});
</script>
<!--  
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'frmLiquidacion','id' => 'frmLiquidacion'))?>
	<table class="tbl_form">
		<tr>
		
			<td>TIPO COBRO</td>
			<td>
				<?php echo $frm->input('ProveedorLiquidacion.tipo_deposito',array('type'=>'select','options'=>array('' => '', 'LI' => 'RECIBO SUELDO', 'CA' => 'CANCELACION', 'CC' => 'COBRO CAJA')));?>
			</td>
		</tr>

		<tr>
			<td>DESDE FECHA</td><td><?php echo $frm->calendar('ProveedorLiquidacion.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>HASTA FECHA</td><td><?php echo $frm->calendar('ProveedorLiquidacion.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
		</tr>
		<tr><td colspan="2"><?php echo $frm->submit("GENERAR")?></td></tr>
		
	</table>
	<?php echo $frm->end()?>
</div>
-->


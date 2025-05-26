<?php echo $this->renderElement('head',array('title' => 'CREDITOS :: ANALISIS DE CONCILIACION CON PROVEEDOR'))?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('conciliar_proveedor').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">

	<?php echo $frm->create(null,array('action' => 'conciliar_proveedor','id' => 'conciliar_proveedor','type' => 'file'))?>
	
	<table class="tbl_form">
		<tr>
			<td>PROVEEDOR</td>
			<td>
			<?php echo $this->renderElement('proveedor/combo_general',array(
																			'plugin'=>'proveedores',
																			'metodo' => "proveedores_list",
																			'model' => 'OrdenDescuento.proveedor_id',
																			'empty' => false,
																			'selected' => (isset($this->data['OrdenDescuento']['proveedor_id']) ? $this->data['OrdenDescuento']['proveedor_id'] : "")
			))?>			
			</td>				
		</tr>		
		<tr>
			<td>PERIODO CONTROL CJP</td><td><?php echo $frm->periodo('OrdenDescuento.periodo_control_cjp','',$periodo_cjp1,date('Y') - 10,date('Y') + 1)?></td>
		</tr>
		<tr>
			<td>PERIODO CONTROL ANSES</td><td><?php echo $frm->periodo('OrdenDescuento.periodo_control_anses','',$periodo_anses1,date('Y') - 10,date('Y') + 1)?></td>
		</tr>
		<tr>
			<td>PERIODO CONTROL CBU</td><td><?php echo $frm->periodo('OrdenDescuento.periodo_control_cbu','',$periodo_cbu1,date('Y') - 10,date('Y') + 1)?></td>
		</tr>		
        <tr>
            <td>ARCHIVO DE DATOS (EXCEL)</td><td><input type="file" name="data[OrdenDescuento][archivo_datos]" id="OrdenDescuentoArchivoDatos"/></td>
        </tr>
        <tr><td colspan="2"><?php echo $frm->submit("GENERAR PROCESO DE CONCILIACION")?></td></tr>	
	
	</table>
	
	
	<?php echo $frm->end()?>

</div>

<?php if($show_asincrono == 1):?>

	<?php
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'conciliar_proveedor',
											'accion' => '.mutual.orden_descuentos.conciliar_proveedor',
											'target' => '_blank',
											'btn_label' => 'Ver Listado',
											'titulo' => "CREDITOS :: ANALISIS DE CONCILIACION CON PROVEEDOR",
											'subtitulo' => "",
											'p1' => $proveedor_id,
											'p2' => $archivo_excel,
											'p3' => $periodo_cjp,
											'p4' => $periodo_anses,
											'p5' => $periodo_cbu,
	));
	
	?>
	
	

<?php endif;?>
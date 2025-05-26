<h1>APROBAR ASIENTOS AUTOMATICOS</h1>
<h1>FECHA ULTIMO PROCESO :: <?php echo $util->armaFecha($aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_desde'])?></h1>
<hr>
<div class="row">
	<?php echo $controles->btnRew('Regresar','/contabilidad/mutual_proceso_asientos/index')?>
</div>

<script type="text/javascript">
Event.observe(window, 'load', function(){
	$('form_aprobar_asientos').disable();

});
</script>
<?php
?>
<?php echo $frm->create(null,array('action' => 'aprobar_asientos','id' => 'form_aprobar_asientos'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>PROCESAR HASTA:</td>
			<td colspan="2"><?php echo $frm->calendar('AprobarAsiento.fecha_proceso','',$aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_hasta'])?></td>
		</tr>
		
	
	</table>
	
</div>

<?php echo $frm->end()?>

	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'aprobar_asientos',
											'accion' => '.contabilidad.mutual_proceso_asientos',
											'target' => '',
											'btn_label' => 'Ver Asientos Generados',
											'titulo' => "APROBAR ASIENTOS",
											'subtitulo' => 'FECHA DE PROCESO: '.$util->armaFecha($aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_hasta']),
											'p1' => $procesoId
	));
	
	?>





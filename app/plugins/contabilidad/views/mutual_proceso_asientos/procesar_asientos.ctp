<h1>PROCESO AUTOMATICO DE ASIENTOS</h1>
<h1>FECHA ULTIMO PROCESO :: <?php echo $util->armaFecha($aEjercicio['Ejercicio']['fecha_proceso'])?></h1>
<hr>
<div class="row">
	<?php echo $controles->btnRew('Regresar','/contabilidad/mutual_proceso_asientos/index')?>
</div>

<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_procesar_asientos').disable();
	<?php endif;?>

});
</script>
<?php
?>
<?php echo $frm->create(null,array('action' => 'procesar_asientos','id' => 'form_procesar_asientos'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>PROCESAR HASTA:</td>
			<td colspan="2"><?php echo $frm->calendar('ProcesarAsiento.fecha_proceso','',$fecha_nuevo_proceso,date("Y", strtotime($aEjercicio['Ejercicio']['fecha_hasta'])),date("Y", strtotime($aEjercicio['Ejercicio']['fecha_hasta'])))?></td>
		</tr>
		
		<tr>
			<td>AGRUPAR ASIENTO:</td>
			<td><?php echo $frm->input('ProcesarAsiento.agrupar',array('type'=>'select','options'=>array(0 => 'SIN AGRUPAR', 1 => 'POR FECHA', 2 => 'FECHA Y MODULO', 3 => 'FECHA, MODULO Y TIPO ASIENTO')));?></td>
			<td><?php echo $frm->submit("ACEPTAR")?></td>
		</tr>
	
	</table>
	
</div>
<?php echo $frm->hidden('ProcesarAsiento.id',array('value' => $procesoId));?>
<?php echo $frm->end()?>

<?php if($show_asincrono == 1):?>

	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'procesar_asientos',
											'accion' => '.contabilidad.mutual_proceso_asientos',
											'target' => '',
											'btn_label' => 'Ver Asientos Generados',
											'titulo' => "PROCESO AUTOMATICO DE ASIENTOS",
											'subtitulo' => 'FECHA DE PROCESO: '.$util->armaFecha($fecha_nuevo_proceso),
											'p1' => $procesoId
	));
	
	?>


<?php endif;?>



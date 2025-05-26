<?php 
App::import('Model', 'contabilidad.AsientoRenglon');
App::import('Model', 'contabilidad.PlanCuenta');

$oAsientoRenglon = new AsientoRenglon();
$oPlanCuenta = new PlanCuenta();


			
?>

<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disableForm === 1):?>
		$('form_agrupar_asientos').disable();
	<?php endif;?>

});
</script>

<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'REPORTES'))?>
<div id="FormSearch">
<?php echo $form->create(null,array('name'=>'frmReportes','id'=>'frmReportes', 'action' => 'index'));?>
	<table>
		<tr>
			<td><?php echo $this->renderElement('combo_ejercicio',array(
												'plugin'=>'contabilidad',
												'label' => " ",
												'model' => 'ejercicio.id',
												'disabled' => false,
												'empty' => false,
												'selected' => $ejercicio['id']))?>
			</td>			
			<td><?php echo $frm->submit('SELECCIONAR',array('class' => 'btn_consultar'));?></td>
		</tr>
	</table>
<?php echo $frm->end();?> 
</div>



<?php if(!empty($ejercicio)): ?>
	<?php echo $this->renderElement('opciones_reportes',array(
													'plugin'=>'contabilidad',
													'label' => " ",
													'model' => 'reporte.id',
													'disabled' => false,
													'empty' => false
	));?>
	
	<h3> AGRUPAR ASIENTOS (<?php echo $ejercicio['descripcion']?>)</h3>

	<?php echo $frm->create(null,array('action' => 'agrupar_asientos/' . $ejercicio['id'], 'id' => 'form_agrupar_asientos'))?>
	<div class="areaDatoForm">
	
		<table class="tbl_form">
		
			<tr>
				<td>
                                    <p>AGRUPA LOS ASIENTO POR DIAS</p>
                                    <p>EXCEPTO EL DE APERTURA.</p> 
                                    <p>LOS NUMERO DE ASIENTOS NO SON LOS ORIGINALES.</p> 

                                </td>
			</tr>
			
			<tr>
				<td><?php echo $frm->submit("ACEPTAR")?></td>
			</tr>
		
		</table>
		
	</div>
        <?php echo $frm->hidden('Reporte.Asincrono', array('value' => 1)); ?>
        
	<?php echo $frm->end()?>
	
	
	<?php if($show_asincrono === 1):
                    echo $this->renderElement('show',array(
							'plugin' => 'shells',
							'process' => 'agrupar_asientos',
							'accion' => '.contabilidad.reportes.agrupar_asientos',
							'target' => '',
							'btn_label' => 'Libro Diario',
							'titulo' => "PROCESO AGRUPAR ASIENTOS DIARIOS",
							'subtitulo' => '',
							'p1' => $ejercicio['id']
						));
						
			
	endif;?>


        <?php if(isset($showTabla) && $showTabla === 1):?>


			<div class="areaDatoForm">
				<?php 
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_agrupado_xls/' . $ejercicio['id'] . '/' . $asincrono_id,'controles/ms_excel.png', 'Formato Excel', array('target' => '_blank'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_agrupado_pdf/' . $ejercicio['id'] . '/' . $asincrono_id . '/1','controles/pdf.png', 'Con Encabezado', array('target' => '_blank', 'id' => 'pdf'));
				echo $controles->botonGenerico('/contabilidad/reportes/libro_diario_agrupado_pdf/' . $ejercicio['id'] . '/' . $asincrono_id . '/0','controles/pdf.png', 'Sin Encabezado', array('target' => '_blank', 'id' => 'pdf'));
				?>
			</div>

	<?php endif;?>
<?php endif;?>

	
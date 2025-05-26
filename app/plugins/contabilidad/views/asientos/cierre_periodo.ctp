<?php

$fecha = (!empty($fecha_hasta) ? $fecha_hasta : $fecha_desde);
 
?>

<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'ASIENTOS'))?>
<div id="FormSearch">
<?php echo $form->create(null,array('name'=>'frmAsientos','id'=>'frmAsientos', 'action' => 'index'));?>
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
	<?php echo $this->renderElement('opciones_asiento',array(
													'plugin'=>'contabilidad',
													'label' => " ",
													'model' => 'asiento.id',
													'disabled' => false,
													'empty' => false
	))?>
	
	<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CIERRE PERIODO'))?>
	
	<?php if(!$asientoApertura): ?>
		<h3>NO EXISTE EL ASIENTO DE APERTURA.</h3>
		<p>Para poder realizar el proceso de cierre de periodo debe existir el asiento inicial.</p>
	<?php else: ?>
		<?php if($lHabilitado): ?>
			<script language="Javascript" type="text/javascript">
				
				Event.observe(window, 'load', function(){
				
				});
			
				function CtrlFecha(){
					var fecha_cierre  = $('AsientoCierreFechaHastaYear').getValue() + '-' + $('AsientoCierreFechaHastaMonth').getValue() + '-' + $('AsientoCierreFechaHastaDay').getValue();
					var fecha_desde = '<?php echo date('d/m/Y',strtotime($fecha_desde))?>';
					
					if(fecha_cierre < '<?php echo $fecha_desde?>')
					{
						alert('LA FECHA DEBE SER MAYOR AL ULTIMO CIERRE: ' + fecha_desde);
						$('AsientoCierreFechaHastaDay').focus();
						return false;
					}
			
					return true;
				}
				
			
				
			</script>

	
			<?php echo $frm->create(null,array('action' => 'cierre_periodo/' . $ejercicio['id'],'id' => 'form_cierre_periodo', 'onsubmit' => "return CtrlFecha()"))?>
			<div>
			<h4>Se producira el cierre del periodo a partir de la Fecha: <?php echo date('d/m/Y',strtotime($fecha_desde)) ?>.</h4>
			</div>
				<table class="tbl_form">
				
					<tr>
						<td>FECHA DE CIERRE:</td>
						<td><?php echo $frm->calendar('AsientoCierre.fecha_hasta', null, $fecha, date("Y",strtotime($fecha)),date("Y",strtotime($fecha)))?></td>
						<td><?php echo $frm->submit("ACEPTAR")?></td>
					</tr>
				
				</table>
				
			<?php echo $frm->end()?>
			
			<?php if($show_asincrono == 1):

					if(!$okProceso):?>
						<p>EL CIERRE DE PERIODO NO SE PUEDE REALIZAR POR QUE EXISTEN</p>
						<p>PROCESOS DE ASIENTOS QUE FALTAN DE APROBAR.</p> 
						<p>POR FAVOR APRUEBE LOS PROCESOS QUE FALTAN PARA PODER REALIZAR EL CIERRE DE PERIODO.</p> 
					<?php else:?>
			
						<p>El cierre implica la renumeracion y eliminacion de aquellos asientos borrados que figuran abajo.</p>
						<p>Es preciso que controle los asientos antes de realizar el cierre.</p>
						<p>Despues del cierre no se podran modificar, ni borrar asientos. Solo se prodran anular asientos de periodos cerrados.</p>
						<p>ANULAR ASIENTO es realizar el contra-asiento correspondiente.</p>
						<h3>Asientos que seran eliminados</h3>
					
						<?php if(empty($asientoAnulados)):?>
								<p>NO EXISTEN ASIENTOS A SER ELIMINADOS.</p>
						<?php else: ?>
								  	<div>
								  		<table>
											<tr>
												<th>NRO. ASIENTO</th>
												<th>FECHA</th>
												<th>REFERENCIA</th>
												<th>DEBE</th>
												<th>HABER</th>
											</tr>
											<?php
											$i = 0;
											foreach ($asientoAnulados as $asiento):
												$class = null;
												if ($i++ % 2 == 0) {
													$class = ' class="altrow"';
												}
											?>
												<tr<?php echo $class;?> >
													<td align="right"><?php echo $asiento['Asiento']['nro_asiento']?></td>
													<td><?php echo date('d/m/Y',strtotime($asiento['Asiento']['fecha']))?></td>
													<td><strong><?php echo $asiento['Asiento']['referencia']?></strong></td>
													<td align="right"><?php echo $asiento['Asiento']['debe']?></td>
													<td align="right"><?php echo $asiento['Asiento']['haber']?></td>
												</tr>
											<?php endforeach; ?>	
								  		</table>
								  	</div>
						
						<?php endif; ?>
	
						<?php 
						echo $this->renderElement('show',array(
																'plugin' => 'shells',
																'process' => 'cierre_periodo',
																'accion' => '.contabilidad.asientos',
																'target' => '',
																'btn_label' => 'Ver Asientos',
																'titulo' => "PROCESO CIERRE PERIODO DE ASIENTOS",
																'subtitulo' => 'FECHA DE CIERRE: '.$util->armaFecha($fecha_hasta),
																'p1' => $fecha_hasta,
																'p2' => $ejercicio['id']
						));
						
						?>
			
				<?php endif;?>
			
			<?php endif;?>

		
		<?php else: ?>
			<p>El ejercicio no esta habilitado para el cierre de periodos.</p>
		<?php endif;?>
	<?php endif;?>

<?php endif?>
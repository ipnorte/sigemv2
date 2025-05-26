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
	
	<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CIERRE EJERCICIO'))?>
	
	<?php if(!$asientoApertura): ?>
		<h3>NO EXISTE EL ASIENTO DE APERTURA.</h3>
		<p>Para poder realizar el proceso de cierre de ejercicio debe existir el asiento inicial.</p>
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

                        <?php echo $this->renderElement('opciones_cierre',array(
                                                                                 'plugin'=>'contabilidad',
                                                                                 'label' => " ",
                                                                                 'model' => 'asiento.id',
                                                                                 'disabled' => false,
                                                                                 'empty' => false
                        ))?>
		<?php else: ?>
			<p>El ejercicio no esta habilitado para el cierre de periodos.</p>
		<?php endif;?>
	<?php endif;?>

<?php endif?>
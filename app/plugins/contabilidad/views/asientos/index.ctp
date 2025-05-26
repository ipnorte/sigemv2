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
<?php if(!empty($asientos)): ?>
<div class='row'>
	<?php echo $form->create(null,array('action'=> 'index'));?>
	<table>
		<tr>
			<td colspan="7"><strong>BUSCAR ASIENTOS</strong></td>
		</tr>
		<tr>
			<td>
				<?php echo $frm->input('Asiento.fecha',array('label' => 'Fecha Asiento:', 'dateFormat' => 'DMY','minYear'=>date("Y", strtotime($ejercicio['fecha_desde'])), 'maxYear' => date("Y", strtotime($ejercicio['fecha_hasta']))))?>
			</td>
			<td>
				<?php echo $frm->input('Asiento.nro_asiento',array('label' => 'Nro.Asiento:'))?>
			</td>
			<td>Asiento desbalanceados</td>
			<td><input type="checkbox" name="data[Asiento][desbalanceado]" value="" id="AsientoDesbalanceado" onclick="chkOnclick()"/>			
			<td><input type="submit" class="btn_consultar" value="APROXIMAR" /></td>
		</tr>
	</table>
	<?php echo $frm->hidden('Asiento.co_ejercicio_id',array('value' => $ejercicio['id'])); ?>
	<?php echo $form->end();?> 
</div>
<?php echo $this->renderElement('paginado')?>

<table cellpadding="0" cellspacing="0">

	<tr>
		<th></th>
		<th><?php echo $paginator->sort('NRO. ASIENTO','nro_asiento');?></th>
		<th><?php echo $paginator->sort('FECHA','fecha');?></th>
		<th>REFERENCIA</th>
		<th>DEBE</th>
		<th>HABER</th>
		<th>BAL.</th>
		<th>BOR/REC</th>
		<th>ANULAR</th>
	</tr>
	<?php
	$i = 0;
	foreach ($asientos as $asiento):
		if($asiento['Asiento']['borrado'] == 0):
			$accion = 'delete/';
			$imgAccion = 'controles/editdelete.png';
		else:
			$accion = 'recuperar/';
			$imgAccion = 'controles/arrow_redo.png';
		endif;

		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?> >
			<td align="center"><?php echo $controles->botonGenerico('edit/'.$asiento['Asiento']['id'],'controles/folder_user.png')?></td>
			<td align="right"><?php echo $controles->linkModalBox($asiento['Asiento']['nro_asiento'],array('title' => 'ASIENTO NRO.:' . $asiento['Asiento']['nro_asiento'],'url' => '/contabilidad/asientos/view/'.$asiento['Asiento']['id'],'h' => 450, 'w' => 750))?></td>
			<td><?php echo date('d/m/Y',strtotime($asiento['Asiento']['fecha']))?></td>
			<td><strong><?php echo $asiento['Asiento']['referencia']?></strong></td>
			<td align="right"><?php echo $asiento['Asiento']['debe']?></td>
			<td align="right"><?php echo $asiento['Asiento']['haber']?></td>
			<td><?php echo ($asiento['Asiento']['debe'] != $asiento['Asiento']['haber'] ? $html->image('controles/alert.png', array('alt' => 'NO BALANCEADO')) : $html->image('controles/activo.gif', array('alt' => 'BALANCEADO')));?></td>
			<td><?php echo ($asiento['Asiento']['fecha'] > $ejercicio['fecha_cierre'] && ($asiento['Asiento']['borrado'] == 0 || $asiento['Asiento']['borrado'] == 1) ? $controles->botonGenerico($accion . $asiento['Asiento']['id'],$imgAccion) : '');?></td>	
			<td><?php echo ($asiento['Asiento']['borrado'] == 0 ? $controles->botonGenerico('anular/'. $asiento['Asiento']['id'],'controles/arrow_undo.png') : 'BORRADO');?></td>	
		</tr>
	<?php endforeach; ?>	
</table>
<?php echo $this->renderElement('paginado')?>

<?php else:?>
<h4>NO EXISTEN ASIENTOS PARA <?php echo $ejercicio['descripcion'] ?></h4>
<?php endif?>

<?php endif?>
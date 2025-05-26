<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PLAN DE CUENTAS'))?>
<div id="FormSearch">

<?php echo $form->create(null,array('name'=>'frmPlanCuenta','id'=>'frmPlanCuenta','onsubmit' => "", 'action' => 'index'));?>
	<table>
		<tr>
			<td><?php echo $this->renderElement('combo_ejercicio',array(
												'plugin'=>'contabilidad',
												'label' => " ",
												'model' => 'Ejercicio.id',
												'disabled' => false,
												'empty' => false,
												'selected' => $ejercicio['id']))?>
			</td>			
			<td><?php echo $frm->submit('SELECCIONAR',array('class' => 'btn_consultar'));?></td>
		</tr>
	</table>
<?php echo $frm->end();?> 
</div>
<?php if(!empty($ejercicio)){ ?>
	<h3><?php echo $ejercicio['descripcion']?></h3>
		<?php 
		echo $controles->botonGenerico('/contabilidad/plan_cuentas/plan_cuenta_xls/' . $ejercicio['id'],'controles/ms_excel.png', null, array('target' => 'blank'));
		echo $controles->botonGenerico('/contabilidad/plan_cuentas/plan_cuenta_pdf/' . $ejercicio['id'],'controles/pdf.png', null, array('target' => 'blank', 'id' => 'pdf'));
?>


<table>

	<tr>
	
		<th>CODIGO</th>
		<th>CONCEPTO</th>
		<th>RECIBE ASIENTO</th>
		<th>AJUSTABLE</th>
		<th>RUBRO</th>
		<th>NIVEL</th>
		<th>SUMARIZA</th>
		<th></th>
		<th><?php echo $controles->botonGenerico('add'.'/1/0/' . $ejercicio['id'],'controles/add.png')?></th>
		<th>BORRAR</th>	
	</tr>

	<?php foreach($PlanCuentas as $dato){
		$class = '';
		if($dato['PlanCuenta']['imputable'] == 0) $class = ' class="altrow"';
		
		$nivel = $dato['PlanCuenta']['nivel'] + 1;
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $dato['PlanCuenta']['cuenta']?></td>
			<td><?php echo $dato['PlanCuenta']['descripcion']?></td>
			<td><?php echo $controles->OnOff($dato['PlanCuenta']['imputable'])?></td>
			<td><?php echo $controles->OnOff($dato['PlanCuenta']['actualiza'])?></td>
			<td><?php echo $dato['PlanCuenta']['tipo_cuenta']?></td>
			<td><?php echo $dato['PlanCuenta']['nivel']?></td>
			<td><?php echo $dato['PlanCuenta']['co_plan_cuenta_id']?></td>
			<td><?php echo $controles->botonGenerico('edit/'. $ejercicio['id'] . '/' . str_replace('.', '', $dato['PlanCuenta']['cuenta']) . '/' . $dato['PlanCuenta']['nivel'] . '/' . str_replace('.', '', $dato['PlanCuenta']['co_plan_cuenta_id']),'controles/edit.png')?></td>	
			<?php if($nivel <= $ejercicio['nivel'] && $dato['PlanCuenta']['imputable'] == 0){ ?>
			<td><?php echo $controles->botonGenerico('add/'. $nivel . '/' . str_replace('.', '', $dato['PlanCuenta']['cuenta']) . '/' . $ejercicio['id'],'controles/add.png')?></td>	
                        <?php }else{ ?>
			<td></td>
                        <?php }; ?>
			<?php if($dato['PlanCuenta']['borrar'] == 0){ ?>
			<td align="center"><?php echo $controles->botonGenerico('del/'. $dato['PlanCuenta']['id'],'controles/desactivo.gif', '', null, 'ESTA SEGURO DE BORRAR LA CUENTA:\n ' . $dato['PlanCuenta']['cuenta'] . '-' . $dato['PlanCuenta']['descripcion']);?></td>
                        <?php }else{ ?>
			<td></td>
                        <?php }; ?>
		</tr>
	
	<?php };?>
</table>
<?php }; ?>

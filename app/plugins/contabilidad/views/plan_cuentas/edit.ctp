<?php if($this->data['PlanCuenta']['existe'] == 0): ?> 
<h3>NUEVA CUENTA</h3>
<?php else: ?>
<h3>MODIFICAR</h3>
<?php endif; ?>
<?php echo $frm->create(null,array('name'=>'formEditPlanCuenta','id'=>'formEditPlanCuenta','action' => 'edit/'.$this->data['PlanCuenta']['co_ejercicio_id'].'/'.$this->data['PlanCuenta']['codigo'].'/'.$nivel_add.'/'.$sumariza));?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td>C&oacute;digo de Cuenta</td>
			<td><?php echo $frm->input('PlanCuenta.codigo', array('value' => $this->data['PlanCuenta']['codigo'], 'disabled' => 'disabled')); ?></td>
		</tr>
		<tr>
			<td>Descripci&oacute;n</td>	
			<td><?php echo $frm->input('PlanCuenta.descripcion',array('label'=>'','size'=>60,'maxlength'=>50)); ?></td>
		</tr>
		<tr>
			<td>Recibe Asiento</td><td><?php echo $frm->input('PlanCuenta.imputable',array('label'=>'','checked'=>'')) ?></td>
		</tr>
		<tr>
			<td>Ajuste por Inflaci&oacute;n</td><td><?php echo $frm->input('PlanCuenta.actualiza',array('label'=>'','checked'=>'')) ?></td>
		</tr>
		<tr>
			<td>Sumariza en:</td><td><?php echo $frm->input('PlanCuenta.sumariza',array('label'=>'','size'=>30, 'maxlength' => 30, 'value' => $sumariza)) ?></td>
		</tr>
		<tr>
			<td>Tipo de Cuenta</td>	
			<td><?php echo $frm->tipoCuenta($this->data['PlanCuenta']['tipo_cuenta']); ?></td>
		</tr>
	
	</table>
</div>
<?php if($this->data['PlanCuenta']['existe'] == 0): ?> 
<?php echo $frm->hidden('PlanCuenta.id'); ?>
<?php else: ?>
<?php echo $frm->hidden('PlanCuenta.id',array('value' => $this->data['PlanCuenta']['id'])); ?>
<?php endif; ?>
<?php echo $frm->hidden('PlanCuenta.cuenta',array('value' => $this->data['PlanCuenta']['cuenta'])); ?>
<?php echo $frm->hidden('PlanCuenta.nivel',array('value' => $nivel_add)); ?>
<?php // echo $frm->hidden('PlanCuenta.co_plan_cuenta_id',array('value' => $sumariza)); ?>
<?php echo $frm->hidden('PlanCuenta.co_ejercicio_id',array('value' => $ejercicio_id)); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => "/contabilidad/plan_cuentas/index/" . $this->data['PlanCuenta']['co_ejercicio_id']))?>
<?php $form->end() ?>
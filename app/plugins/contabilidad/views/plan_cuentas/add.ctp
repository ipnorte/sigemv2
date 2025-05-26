<h3>NUEVA CUENTA</h3>
<?php echo $form->create(null,array('name'=>'formEditPlanCuenta','id'=>'formEditPlanCuenta', 'action' => "add/$nivel_add/$sumariza/$ejercicio_id" ));?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td>C&oacute;digo de Cuenta</td>
			<td>
			<?php 
			echo $frm->hidden('codigo_a',array('value' => $codigo_a));
			if($nivel_add != 1):
				echo $frm->input('codigo_aa',array('label'=>'','disabled'=>'disabled','value' => $codigo_a, 'size'=>strlen($codigo_a), 'maxlength'=>strlen($codigo_a)));
			endif;
			if($nivel_add == 5 && $nivel > 5):
				echo $frm->input('codigo_5',array('label'=>'','size'=>$nivel_len5,'maxlength'=>$nivel_len5));
				echo $frm->input('codigo_6',array('label'=>'','size'=>$nivel_len6,'maxlength'=>$nivel_len6));
			else:
				echo $frm->input('codigo',array('label'=>'','size'=>$nivel_len,'maxlength'=>$nivel_len));
				echo $frm->hidden('codigo_p',array('value' => $codigo_p));
				if($nivel_add != $nivel):
					echo $frm->input('codigo_pp',array('label'=>'','disabled'=>'disabled','value' => $codigo_p, 'size'=>strlen($codigo_p), 'maxlength'=>strlen($codigo_p)));
				endif;
			endif;
			?></td>
			<td><?php echo $frm->submit('VERIFICAR')?></td>
			<td><?php echo $controles->botonGenerico('/contabilidad/plan_cuentas/index/' . $ejercicio_id,'controles/arrow_undo.png')?></td>
		</tr>
	</table>
</div>
<?php echo $frm->hidden('PlanCuenta.nivel',array('value' => $nivel_add)); ?>
<?php echo $frm->hidden('PlanCuenta.sumariza',array('value' => $sumariza)); ?>
<?php echo $frm->hidden('PlanCuenta.ejercicio_id',array('value' => $ejercicio_id)); ?>
<?php echo $frm->end(); ?>

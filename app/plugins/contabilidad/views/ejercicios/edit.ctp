<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'EJERCICIOS CONTABLE :: MODIFICAR'))?>

<?php echo $frm->create('Ejercicio',array('action' => 'edit/'));?>

<div class="areaDatoForm">
	 		<div class='row'>
				<?php echo $frm->input('codigo',array('label'=>'Ejercicio Nro.:','size'=>2,'maxlength'=>1,'disabled' => 'disabled', 'value' => $this->data['Ejercicio']['id'])); ?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.descripcion',array('label'=>'Descripci&oacute;n','size'=>60,'maxlength'=>50)); ?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.fecha_desde',array('label' => 'Fecha Incio Ej.:', 'dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1))?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.fecha_hasta',array('label' => 'Fecha Final Ej.:', 'dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1))?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.nivel',array('label'=>'Nivel de Cuenta:','size'=>2,'maxlength'=>1)); ?>
	 		</div>	 		
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.nivel_1',array('label'=>'Cant.Digito Nivel 1:','size'=>2,'maxlength'=>1)); ?>
	 		</div> 	
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.nivel_2',array('label'=>'Cant.Digito Nivel 2:','size'=>2,'maxlength'=>1)); ?>
	 		</div>	 		 		 	 		 
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.nivel_3',array('label'=>'Cant.Digito Nivel 3:','size'=>2,'maxlength'=>1)); ?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.nivel_4',array('label'=>'Cant.Digito Nivel 4:','size'=>2,'maxlength'=>1)); ?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.nivel_5',array('label'=>'Cant.Digito Nivel 5:','size'=>2,'maxlength'=>1)); ?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Ejercicio.nivel_6',array('label'=>'Cant.Digito Nivel 6:','size'=>2,'maxlength'=>1)); ?>
	 		</div>
	 		<?php if($this->data['Ejercicio']['existe'] == 0):?>
		 		<div>
		 			<?php echo $frm->input("Ejercicio.copiar",array('type' => 'checkbox', 'label' => 'Copiar Plan de Cuenta Ejercicio Vigente'))?>
		 		</div></br>
	 		<?php endif;?>
	 		<div style="clear: both;"></div>	 		 		 	 		 
</div>

<?php echo $frm->hidden('Ejercicio.id') ?>
<?php if($this->data['Ejercicio']['existe'] == 1):
	echo $frm->hidden('Ejercicio.copiar', array('value' => 0));	
endif;?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/contabilidad/ejercicios'))?>
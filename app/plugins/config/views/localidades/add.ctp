<?php echo $this->renderElement('head',array('title' => 'LOCALIDADES :: AGREGAR LOCALIDAD'))?>
<?php echo $frm->create('Localidad');?>
<div class="areaDatoForm">
 	<div class='row'>
 		<?php echo $frm->input('cp',array('label'=>'CP','size'=>4,'maxlength'=>4)) ?>
 		<?php echo $frm->input('nombre',array('label'=>'NOMBRE','size'=>50,'maxlength'=>50)) ?>
 	</div>
 	<div class='row'>
		<?php echo $frm->input('provincia_id',$provincias,array('label'=>'PROVINCIA'));?>
	</div>
	<div style="clear: both;"></div>	
<?php //   debug($provincias)?>
	<?php echo $frm->hidden('idr',array('value' => 0))?>
</div>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/localidades'))?>
<?php echo $this->renderElement('head',array('title' => 'CONFIGURACION DE PLANTILLAS :: NUEVO'))?>
<?php echo $frm->create(null,array('action'=>'add'));?>
<fieldset>
	<legend>Nueva Plantilla</legend>
  		<div class='row'>
  			<?php echo $frm->input('tipo',array('type'=>'select','options'=> array('M' => 'MUTUO','P' => 'PAGARE','L'=>'LIQUIDACION'),'label'=>'TIPO PLANTILLA'));?>
 			<?php echo $frm->input('descripcion',array('label'=>'DESCRIPCION','size' => 50, 'maxlenght' => 100)); ?>
 			<?php echo $frm->input('activo',array('label'=>'ACTIVO','checked' => 'checked')); ?>
 		</div>
 		<div class='row'>  	
			<?php echo $fck->fckeditor(array('Template', 'html'), $html->base, ""); ?>
		</div>	
</fieldset>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/templates'))?>
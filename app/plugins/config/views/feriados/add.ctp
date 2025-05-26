<?php echo $this->renderElement('head',array('title' => 'FERIADOS :: NUEVO FERIADO'))?>

<?php echo $frm->create('Feriado');?>
<div class="areaDatoForm">
 	<div class='row'>
 		<?php echo $frm->calendar('Feriado.fecha','FERIADO') ?>
 	</div>

	<div style="clear: both;"></div>	
<?php //   debug($provincias)?>
</div>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/feriados'))?>
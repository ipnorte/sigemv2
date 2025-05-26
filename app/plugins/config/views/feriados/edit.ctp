<?php echo $this->renderElement('head',array('title' => 'FERIADOS :: MODIFICAR FERIADO'))?>

<?php echo $frm->create('Feriado');?>
<div class="areaDatoForm">
 	<div class='row'>
 		<?php echo $frm->calendar('Feriado.fecha','FERIADO',$this->data['Feriado']['fecha']) ?>
 	</div>

	<div style="clear: both;"></div>	
<?php //   debug($provincias)?>
</div>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/feriados'))?>
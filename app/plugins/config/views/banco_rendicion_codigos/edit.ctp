<?php echo $this->renderElement('head',array('title' => 'BANCO :: CODIGOS DE RENDICION :: MODIFICAR CODIGO'))?>

<div class="areaDatoForm">
	BANCO: <strong><?php echo $banco['Banco']['id']?></strong>
	&nbsp;-&nbsp;
	<strong><?php echo $banco['Banco']['nombre']?></strong>
</div>

<?php echo $frm->create('BancoRendicionCodigo',array('action' => 'add/'.$banco['Banco']['id']));?>
<div class="areaDatoForm">
 	<div class='row'>
 		<?php echo $frm->input('codigo',array('label'=>'CODIGO','size'=>3,'maxlength'=>3,'disabled' => 'disabled')) ?>
 		<?php echo $frm->input('descripcion',array('label'=>'DESCRIPCION','size'=>60,'maxlenght'=>100)) ?>
 	</div> 		
	<div class="row">
		<?php echo $frm->input('indica_pago',array('label' => 'ESTE CODIGO INDICA EL PAGO'))?>
	</div> 
	<div class="row">
		<?php echo $this->renderElement('global_datos/combo',array(
																			'plugin'=>'config',
																			'label' => 'CALIFICACION DEL SOCIO PARA ESTE CODIGO',
																			'model' => 'BancoRendicionCodigo.calificacion_socio',
																			'prefijo' => 'MUTUCALI',
																			'disable' => false,
																			'empty' => false,
																			'selected' => $this->data['BancoRendicionCodigo']['calificacion_socio'],
																			'logico' => true,
		))?>	
	</div>	
	<div style="clear: both;"></div>	
<?php //   debug($provincias)?>
</div>
<?php echo $frm->hidden('id') ?>
<?php echo $frm->hidden('banco_id',array('value' => $banco['Banco']['id'])) ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/banco_rendicion_codigos/index/'.$banco['Banco']['id']))?>
<?php echo $this->renderElement('head',array('title' => 'SUCURSALES BANCARIAS :: MODIFICAR SUCURSAL'))?>

<div class="areaDatoForm">
	BANCO: <strong><?php echo $banco['Banco']['id']?></strong>
	&nbsp;-&nbsp;
	<strong><?php echo $banco['Banco']['nombre']?></strong>
</div>

<?php echo $frm->create('BancoSucursal');?>
<div class="areaDatoForm">
 	<div class='row'>
 		<?php echo $frm->input('nro_sucursal',array('label'=>'NRO','size'=>10,'maxlength'=>10)) ?>
                <?php echo $frm->input('codigo_bcra',array('label'=>'BCRA','size'=>10,'maxlength'=>10)) ?>
 		<?php echo $frm->input('nombre',array('label'=>'NOMBRE','size'=>60,'maxlenght'=>100,'value' => utf8_decode($this->data['BancoSucursal']['nombre']))) ?>
 	</div> 		
 	<div class='row'>
 		<?php echo $frm->input('direccion',array('label'=>'DIRECCION','size'=>60,'maxlenght'=>100)) ?>
 	</div> 
	<div style="clear: both;"></div>	
<?php //   debug($provincias)?>
</div>
<?php echo $frm->hidden('id')?>
<?php echo $frm->hidden('banco_id',array('value' => $banco['Banco']['id'])) ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/config/banco_sucursales/index/'.$banco['Banco']['id']))?>
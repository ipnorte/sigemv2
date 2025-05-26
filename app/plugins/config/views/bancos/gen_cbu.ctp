<?php echo $this->renderElement('head',array('title' => 'BANCOS :: GENERAR NUMERO DE CBU'))?>
<?php echo $frm->create(null,array('action' => 'gen_cbu'));?>
<div class="areaDatoForm">
	<table class="tbl_form">
	
		<tr>
			<td>BANCO</td>
			<td><?php echo $this->requestAction('/config/bancos/combo/Banco.banco_id/0/0/1')?></td>
		</tr>
		<tr>
			<td>SUCURSAL</td>
			<td><?php echo $frm->number('Banco.nro_sucursal',array('label'=>'','size'=>5,'maxlength'=>4)); ?></td>
		</tr>
		<tr>
			<td>CUENTA</td>
			<td><?php echo $frm->number('Banco.nro_cta_bco',array('label'=>'','size'=>13,'maxlength'=>11)); ?></td>
		</tr>
	
	</table>

</div>

<?php echo $frm->end("GENERAR")?>

<?php if(!empty($cbu)):?>
	
	<div class="areaDatoForm2">
		<h3>GENERACION DE CBU</h3>
		<table class="tbl_form">
			<tr>
				<td>STATUS</td>
				<td><strong><?php echo ($cbu['error'] == 0 ? "<span style='color:green;'>OK</span>":"<span style='color:red;'>ERROR: ".$cbu['mensaje']."</span>")?></strong></td>
			</tr>
			<tr>
				<td>CBU</td>
				<td><h1><?php echo $cbu['cbu']?></h1></td>
			</tr>
		</table>
	
	<?php //   debug($cbu)?>
	</div>
<?php endif;?>
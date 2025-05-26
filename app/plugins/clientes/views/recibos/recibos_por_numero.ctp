<?php echo $this->renderElement('head',array('title' => 'INFORMES DE RECIBOS','plugin' => 'config'))?>
<?php echo $this->renderElement('recibos/menu_recibos',array('plugin' => 'clientes'))?>
<h3>INFORME DE RECIBOS POR NUMERO</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_recibos_por_numero').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'recibos_por_numero','id' => 'form_recibos_por_numero'))?>
	<table class="tbl_form">
		<tr>
			<td>LETRA</td>
			<td>
				<?php echo $frm->input('Recibo.letra',array('size' => 1, 'maxlength' => 1, 'value' => $letra));?>
			</td>
		</tr>
		<tr>
			<td>SUCURSAL</td>
			<td>
				<?php echo $frm->number('Recibo.sucursal',array('size'=>4,'maxlength'=>4, 'value' => $sucursal));?>
			</td>
		</tr>
		<tr>
			<td>DESDE NUMERO</td>
			<td>
				<?php echo $frm->number('Recibo.numero_desde',array('size'=>8,'maxlength'=>8, 'value' => $numero_desde));?>
			</td>
		</tr>
		<tr>
			<td>HASTA NUMERO</td>
			<td>
				<?php echo $frm->number('Recibo.numero_hasta',array('size'=>8,'maxlength'=>8, 'value' => $numero_hasta));?>
			</td>
		</tr>
		<tr><td colspan="2"><?php echo $frm->submit("ACEPTAR")?></td></tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if(isset($aReciboNumero)): ?>
	<table class="tbl_form">
  		<tr>
			<td colspan="2" align="right">IMPRIMIR ESTA PLANILLA</td>
			<td><?php echo $controles->botonGenerico('/clientes/recibos/informe_recibo_numero/'.$letra.'/'.$sucursal.'/'.$numero_desde.'/'.$numero_hasta.'/PDF','controles/printer.png','',array('target' => 'blank'))?></td>
			<td><?php echo $controles->botonGenerico('/clientes/recibos/informe_recibo_numero/'.$letra.'/'.$sucursal.'/'.$numero_desde.'/'.$numero_hasta.'/XLS','controles/ms_excel.png','',array('target' => 'blank'))?></td>
	  	</tr>
	</table>


	<?php echo $this->renderElement('recibos/informe_recibo',array('aReciboInforme'=>$aReciboNumero,'plugin' => 'clientes'))?>

<?php endif; ?>

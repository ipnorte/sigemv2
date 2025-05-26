<?php echo $this->renderElement('head',array('title' => 'INFORMES DE RECIBOS','plugin' => 'config'))?>
<?php echo $this->renderElement('recibos/menu_recibos',array('plugin' => 'clientes'))?>
<h3>INFORME DE RECIBOS ENTRE FECHAS</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_recibos_entre_fecha').disable();
	<?php endif;?>
});
</script>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'recibos_entre_fecha','id' => 'form_recibos_entre_fecha'))?>
	<table class="tbl_form">
		<tr>
			<td>DESDE FECHA</td><td><?php echo $frm->calendar('Recibo.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
		</tr>
		<tr>
			<td>HASTA FECHA</td><td><?php echo $frm->calendar('Recibo.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
		</tr>
		<tr><td colspan="2"><?php echo $frm->submit("ACEPTAR")?></td></tr>
	</table>
	<?php echo $frm->end()?>
</div>
<?php if(isset($aReciboFechas)): ?>

	<table class="tbl_form">
  		<tr>
			<td colspan="2" align="right">IMPRIMIR ESTA PLANILLA</td>
			<td><?php echo $controles->botonGenerico('/clientes/recibos/informe_recibo_fecha/'.$fecha_desde.'/'.$fecha_hasta.'/PDF','controles/printer.png','',array('target' => 'blank'))?></td>
			<td><?php echo $controles->botonGenerico('/clientes/recibos/informe_recibo_fecha/'.$fecha_desde.'/'.$fecha_hasta.'/XLS','controles/ms_excel.png','',array('target' => 'blank'))?></td>
	  	</tr>
	</table>

<?php echo $this->renderElement('recibos/informe_recibo',array('aReciboInforme'=>$aReciboFechas,'plugin' => 'clientes'))?>

<?php endif; ?>

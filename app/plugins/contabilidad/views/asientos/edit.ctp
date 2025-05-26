<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'ASIENTO :: MODIFICAR'))?>
<h3><?php echo $ejercicio['descripcion'] ?></h3>
<?php echo $form->create(null,array('name'=>'formAsiento','id'=>'formAsiento','onsubmit' => "",'action' => 'edit/' . $asiento['Asiento']['id'] ));?>

<div class="areaDatoForm">
	 		<div class='row'>
				<?php echo $frm->input('Asiento.fecha',array('label' => 'Fecha Asiento:', 'dateFormat' => 'DMY','minYear'=>date("Y", strtotime($asiento['Asiento']['fecha'])), 'disable' => 'disabled'))?>
	 		</div>
	 		<div class='row'>
				<?php echo $frm->input('Asiento.referencia',array('label'=>'REFERENCIA:','size'=>60, 'maxlenght'=>100)); ?>
	 		</div>	 		
</div>
<h3>Renglones del Asiento</h3>

<div class="areaDatoForm">
<script type="text/javascript">
Event.observe(window, 'load', function() {
		$('btn_submit').disable();
});
</script>
<table class="tbl_form">

	<tr>
		<td>APROXIMAR CUENTA</td>
		<td><?php echo $frm->input('Asiento.descripcionAproxima',array('label'=>'','size'=>50,'maxlenght'=>100, 'value' => $this->data['Asiento']['descripcionAproxima'])); ?>
		<div id="Cuenta_autoComplete" class="auto_complete"></div>
		<?php echo $frm->hidden('Asiento.co_plan_cuenta_id'); ?>
		<?php echo $frm->hidden('Asiento.cuenta_seleccionada'); ?>
		<span id="ajax_loader1" style="display: none;font-size: 11px;font-style:italic;color:red;">
		Procesando...<?php echo $html->image('controles/red_animated.gif') ?>
		</span>			

		<script type="text/javascript">
			document.getElementById("AsientoDescripcionAproxima").value = "<?php echo $this->data['Asiento']['descripcionAproxima']?>";
			document.getElementById("AsientoCoPlanCuentaId").value = "<?php echo $this->data['Asiento']['co_plan_cuenta_id']?>";				
		
			new Ajax.Autocompleter('AsientoDescripcionAproxima', 'Cuenta_autoComplete', '<?php echo $this->base?>/contabilidad/plan_cuentas/autocompleteDescripcion/<?php echo $ejercicio['id']?>/1', {minChars:3, afterUpdateElement:getSelectionId2, indicator:'ajax_loader1'});
			function getSelectionId2(text, li) {
				var id = li.id;
				var values = id.split("|");
				document.getElementById("AsientoDescripcionAproxima").value = values[2];
				document.getElementById("AsientoCoPlanCuentaId").value = values[0];
				document.getElementById("descripcionCuenta").value = values[1] + " - " + values[2];
				document.getElementById("AsientoCuentaSeleccionada").value = values[1] + " - " + values[2];
			} 
		</script>
		</td>		
	</tr>
	<tr>
		<td>CUENTA</td>
		<td>
			<div class="input text"><label for="descripcionCuenta"></label><input type="text" id="descripcionCuenta" disabled="disabled" size="50"/></div>
		</td>
	</tr>
	<tr>
		<td>REFERENCIA</td>
		<td><?php echo $frm->input('Asiento.referencia_renglon',array('label'=>'','size'=>60, 'maxlenght'=>100)) ?></td>
	</tr>
	<tr>
		<td>DEBE - HABER</td>
		<td><?php echo $frm->comboDebeHaber() ?></td>
	</tr>
	<tr>
		<td>IMPORTE</td>
	 	<td>
	 		<?php echo $frm->money('Asiento.importe') ?>
	 		<?php //echo $controles->btnAjax('controles/add.png','/contabilidad/asientos/cargar_renglones','grilla_renglones','formAsiento')?>
	 		<a href="<?php echo $this->base?>/contabilidad/asientos/cargar_renglones" id="link1568620940" onclick=" event.returnValue = false; return false;">
	 		<img src="<?php echo $this->base?>/img/controles/add.png" border="0" alt="" />
	 		</a>
	 		<script type="text/javascript">
	 			Event.observe('link1568620940', 'click', function(event) { 
					$('ajax_loader_2124618328').show();
					new Ajax.Updater(
						'grilla_renglones',
						'<?php echo $this->base?>/contabilidad/asientos/cargar_renglones', 
						{
							asynchronous:true, 
							evalScripts:true, 
							onComplete:function(request, json) {
									$('ajax_loader_2124618328').hide();
									total_debe = parseFloat($('total_debe').getValue());
									total_haber = parseFloat($('total_haber').getValue());
									if(total_debe == total_haber) $('btn_submit').enable();
									else $('btn_submit').disable();
									document.getElementById("AsientoDescripcionAproxima").value = "";
									document.getElementById("AsientoCoPlanCuentaId").value = "";	
									document.getElementById("descripcionCuenta").value = "";			
							}, 
							parameters:$('formAsiento').serialize(), 
							requestHeaders:['X-Update', 'grilla_renglones']
						}
					)
				}, 
				false);
	 		
	 		</script>
	 		<span id="ajax_loader_2124618328" style="display: none;font-size: 11px;font-style:italic;color:red;margin-left:10px;"><img src="<?php echo $this->base?>/img/controles/ajax-loader.gif" border="0" alt="" /></span>
	 	</td>
	</tr>
	<tr>
		<td colspan="2" id="grilla_renglones">
		
		</td>
	</tr>
</table>
</div>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/contabilidad/asientos/index/'.$ejercicio['id']))?>
<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CONFIGURACION DE CUENTAS BANCARIAS :: MODIFICAR CUENTA'))?>
<?php echo $frm->create('BancoCuenta');?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td>BANCO</td><td><?php echo $this->renderElement('banco/combo_global',array('plugin' => 'config','tipo' => 4, 'disabled' => true ,'model' => 'BancoCuenta.banco_id', 'selected' => (isset($this->data['BancoCuenta']['banco_id']) ? $this->data['BancoCuenta']['banco_id'] : "")))?></td>
		</tr>
		<tr>
			<td>DENOMINACION DE LA CUENTA</td><td><?php echo $frm->input('denominacion',array('size' => 60, 'maxlength' => 100))?></td>
		</tr>
		<tr>
			<td>NRO. DE CUENTA</td><td><?php echo $frm->input('numero',array('size' => 20, 'maxlength' => 20, 'disabled' => 'disabled'))?></td>
		</tr>
		<tr>
			<td>FECHA APERTURA</td><td><?php echo $frm->input('fecha_apertura',array('dateFormat' => 'DMY'))?></td>
		</tr>
		<tr>
			<td>APROXIMAR CUENTA</td>
			<td><?php echo $frm->input('Asiento.descripcionAproxima',array('label'=>'','size'=>50,'maxlenght'=>100, 'value' => $this->data['BancoCuenta']['descripcionAproxima'])); ?>
			<div id="Cuenta_autoComplete" class="auto_complete"></div>
			<?php echo $frm->hidden('BancoCuenta.co_plan_cuenta_id'); ?>
			<?php echo $frm->hidden('BancoCuenta.cuenta_seleccionada'); ?>
			<span id="ajax_loader1" style="display: none;font-size: 11px;font-style:italic;color:red;">
			Procesando...<?php echo $html->image('controles/red_animated.gif') ?>
			</span>			
	
			<script type="text/javascript">
				document.getElementById("AsientoDescripcionAproxima").value = "<?php echo $this->data['Asiento']['descripcionAproxima']?>";
				document.getElementById("BancoCuentaCoPlanCuentaId").value = "<?php echo $this->data['BancoCuenta']['co_plan_cuenta_id']?>";				
			
				new Ajax.Autocompleter('AsientoDescripcionAproxima', 'Cuenta_autoComplete', '<?php echo $this->base?>/contabilidad/plan_cuentas/autocompleteDescripcion/<?php echo $util->globalDato("CONTEVIG","entero_1")?>/1', {minChars:3, afterUpdateElement:getSelectionId2, indicator:'ajax_loader1'});
				function getSelectionId2(text, li) {
					var id = li.id;
					var values = id.split("|");
					document.getElementById("AsientoDescripcionAproxima").value = values[2];
					document.getElementById("BancoCuentaCoPlanCuentaId").value = values[0];
					document.getElementById("descripcionCuenta").value = values[1] + " - " + values[2];
					document.getElementById("BancoCuentaCuentaSeleccionada").value = values[1] + " - " + values[2];
				} 
			</script>
			</td>		
		</tr>		
		<tr>
			<td>CUENTA CONTABLE</td>
			<td>
				<input type="text" id="descripcionCuenta" disabled="disabled" size="50" value="<?php echo $this->data['BancoCuenta']['cuenta_contable']?>"/>
			</td>
		</tr>				
		<tr>
			<td>MANEJA CHEQUERA</td><td><?php echo $frm->input('chequeras')?></td>
		</tr>
		<tr>
			<td>VIGENTE</td><td><?php echo $frm->input('activo')?></td>
		</tr>		
	</table>
</div>
<?php echo $frm->hidden('id')?>
<?php echo $frm->hidden('banco_id')?>
<?php echo $frm->hidden('numero')?>
<?php echo $frm->hidden('co_plan_cuenta_id_actual',array('value' => $this->data['BancoCuenta']['co_plan_cuenta_id']))?>
<?php echo $frm->hidden('banco_saldo_id', array('value' => $this->data['BancoCuenta']['banco_saldo_id']))?>
<?php echo $frm->hidden('banco_saldo_alta_id', array('value' => $this->data['BancoCuenta']['banco_saldo_alta_id']))?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/cajabanco/banco_cuentas'))?>
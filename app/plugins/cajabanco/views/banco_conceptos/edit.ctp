<?php
if($this->data['BancoConcepto']['tipo'] == 7):
	$tipo = 7;
	$txtHeader = 'CONFIGURACION CONCEPTOS DE CAJA :: MODIFICAR CONCEPTO';
else:
	$tipo = 6;
	$disabled = '';
	if($this->data['BancoConcepto']['tipo'] != 6) $disabled = 'disabled';
	$txtHeader = 'CONFIGURACION CONCEPTOS DE BANCO :: MODIFICAR CONCEPTO';
endif;
?>
<?php echo $this->renderElement('head',array('plugin' => 'config','title' => $txtHeader))?>
<?php
$checkedA = '';
$checkedB = '';
if($this->data['BancoConcepto']['debe_haber']==0) $checkedA = 'checked';
else $checkedB = 'checked';

?>
<?php echo $frm->create('BancoConcepto');?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td>CONCEPTO</td><td><?php echo $frm->input('concepto',array('size' => 60, 'maxlength' => 100, 'value' => $this->data['BancoConcepto']['concepto'], 'disabled' => 'disabled'))?></td>
		</tr>
<!--	
		<tr>
			<td>APROXIMAR CUENTA</td>
			<td><?php echo $frm->input('Asiento.descripcionAproxima',array('label'=>'','size'=>50,'maxlenght'=>100, 'value' => 'Seleccione Cuenta Contable. . .')); ?>
			<div id="Cuenta_autoComplete" class="auto_complete"></div>
			<?php echo $frm->hidden('BancoConcepto.co_plan_cuenta_id'); ?>
			<?php echo $frm->hidden('BancoConcepto.cuenta_seleccionada'); ?>
			<span id="ajax_loader1" style="display: none;font-size: 11px;font-style:italic;color:red;">
			Procesando...<?php echo $html->image('controles/red_animated.gif') ?>
			</span>			
	
			<script type="text/javascript">
//				document.getElementById("AsientoDescripcionAproxima").value = "<?php //echo $this->data['Asiento']['descripcionAproxima']?>";
				document.getElementById("AsientoDescripcionAproxima").value = "Seleccione Cuenta Contable. . .";
				document.getElementById("BancoConceptoCoPlanCuentaId").value = "<?php //echo $this->data['BancoConcepto']['co_plan_cuenta_id']?>";
				$('AsientoDescripcionAproxima').select();
				$('AsientoDescripcionAproxima').focus();
			
				new Ajax.Autocompleter('AsientoDescripcionAproxima', 'Cuenta_autoComplete', '<?php //echo $this->base?>/contabilidad/plan_cuentas/autocompleteDescripcion/<?php echo $util->globalDato("CONTEVIG","entero_1")?>/1', {minChars:3, afterUpdateElement:getSelectionId2, indicator:'ajax_loader1'});
				function getSelectionId2(text, li) {
					var id = li.id;
					var values = id.split("|");
					document.getElementById("AsientoDescripcionAproxima").value = values[2];
					document.getElementById("BancoConceptoCoPlanCuentaId").value = values[0];
					document.getElementById("descripcionCuenta").value = values[1] + " - " + values[2];
					document.getElementById("BancoConceptoCuentaSeleccionada").value = values[1] + " - " + values[2];
				} 
			</script>
			</td>		
		</tr>
		<tr>
			<td>CUENTA CONTABLE</td>
			<td>
				<input type="text" id="descripcionCuenta" disabled="disabled" size="50" value="<?php echo $this->data['BancoConcepto']['cuenta_contable']?>"/>
			</td>
		</tr>				
-->
		<tr>
			<td>TIPO DE MOVIMIENTO</td>
			<td><input type="radio" name="data[BancoConcepto][debe_haber]" id="ConceptoAccion_a" value="0" <?php echo $checkedA?> <?php echo $disabled?>/>INGRESO</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="radio" name="data[BancoConcepto][debe_haber]" id="ConceptoAccion_b" value="1" <?php echo $checkedB?> <?php echo $disabled?>/>EGRESO</td>
		</tr>
	</table>
</div>
<?php echo $frm->hidden('BancoConcepto.tipo', array('value' => $tipo)); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'MODIFICAR','URL' => '/cajabanco/banco_conceptos/index/' . $tipo))?>
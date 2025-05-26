<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<script type="text/javascript">

	var impoAcuerdoFix = new Number(<?php echo $beneficio['PersonaBeneficio']['acuerdo_debito']?>);
	impoAcuerdoFix = impoAcuerdoFix.toFixed(2);

	var impoMaxRegCbuFix = new Number(<?php echo $beneficio['PersonaBeneficio']['importe_max_registro_cbu']?>);
	impoMaxRegCbuFix = impoMaxRegCbuFix.toFixed(2);
	
	function validateFormAcuerdo(){
		var impoAcuerdo = new Number($("PersonaBeneficioAcuerdoDebito").getValue());
		impoAcuerdo = impoAcuerdo.toFixed(2);
		if(impoAcuerdo != 0){
			var msg = "FIJAR UN ACUERDO DE DEBITO DE " + impoAcuerdo;
			msg = msg + "\n";
			msg = msg + "PARA EL BENEFICIO: <?php echo $beneficio['PersonaBeneficio']['string']?>";
			return confirm(msg);
		}else if(impoAcuerdoFix != 0){
			var msg = "QUITAR EL ACUERDO DE DEBITO DE " + impoAcuerdoFix;
			return confirm(msg);
		}		
		return false;
	}

	function validateFormImpoMaxRegCBU(){
		var impoMaxRegCbu = new Number($("PersonaBeneficioImporteMaxRegistroCbu").getValue());
		impoMaxRegCbu = impoMaxRegCbu.toFixed(2);
		if(impoMaxRegCbu != 0){
			var msg = "FRACCIONAR EL TOTAL A DEBITAR EN REGISTROS NO MAYORES A " + impoMaxRegCbu;
			msg = msg + "\n";
			msg = msg + "PARA EL BENEFICIO: <?php echo $beneficio['PersonaBeneficio']['string']?>";
			return confirm(msg);
		}else if(impoMaxRegCbuFix != 0){
			var msg = "QUITAR EL FRACCIONAMIENTO DEL TOTAL A DEBITAR EN REGISTROS NO MAYORES A " + impoMaxRegCbuFix;
			return confirm(msg);
		}		
		return false;
	}	

</script>

<div class="areaDatoForm">
	<h3>ACUERDO DEBITO</h3>
	<?php echo $form->create(null,array('action' => 'acuerdo_debito/'.$beneficio['PersonaBeneficio']['id'],'onsubmit' => "return validateFormAcuerdo()"))?>
	<div class="areaDatoForm2">
		<h4>BENEFICIO</h4>
		ORGANISMO: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_beneficio_desc']?></strong>
		<br/>
		BENEFICIO: <strong><?php echo $beneficio['PersonaBeneficio']['string']?></strong>
	</div>
	<table class="tbl_form">
		<tr>
			<td>IMPORTE MAXIMO A DEBITAR (0 = DEBITA TODO)</td>
			<td><input name="data[PersonaBeneficio][acuerdo_debito]" type="text" value="<?php echo $beneficio['PersonaBeneficio']['acuerdo_debito']?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" id="PersonaBeneficioAcuerdoDebito" /></td>
		</tr>
	</table>
	<div class="notices" style="clear: both;width: 99%;"><strong>ATENCION!:</strong> Este importe afecta la liquidaci&oacute;n de deuda e importe a enviar a descuento del Socio. </div>
	<?php echo $frm->hidden('PersonaBeneficio.id',array('value' => $beneficio['PersonaBeneficio']['id'])); ?>
	<?php echo $frm->hidden('PersonaBeneficio.persona_id',array('value' => $beneficio['PersonaBeneficio']['persona_id'])); ?>
	<?php echo $frm->hidden('PersonaBeneficio.action',array('value' => 'ACUERDO_DEBITO')); ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/persona_beneficios/index/".$persona['Persona']['id'] : $fwrd) ))?>

</div>





<div class="areaDatoForm">
	<?php echo $form->create(null,array('action' => 'acuerdo_debito/'.$beneficio['PersonaBeneficio']['id'],'onsubmit' => "return validateFormImpoMaxRegCBU()"))?>
	<h3>LIMITE ORDEN DE DEBITO - FRACCIONAR IMPORTES</h3>
	<div class="areaDatoForm2">
		<h4>BENEFICIO</h4>
		ORGANISMO: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_beneficio_desc']?></strong>
		<br/>
		BENEFICIO: <strong><?php echo $beneficio['PersonaBeneficio']['string']?></strong>
	</div>
	<table class="tbl_form">
		<tr>
			<td>IMPORTE MAXIMO POR REGISTRO A DEBITAR (0 = SIN FRACCIONAR)</td>
			<td><input name="data[PersonaBeneficio][importe_max_registro_cbu]" type="text" value="<?php echo $beneficio['PersonaBeneficio']['importe_max_registro_cbu']?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" id="PersonaBeneficioImporteMaxRegistroCbu" /></td>
		</tr>
	</table>
	<?php echo $frm->hidden('PersonaBeneficio.id',array('value' => $beneficio['PersonaBeneficio']['id'])); ?>
	<?php echo $frm->hidden('PersonaBeneficio.persona_id',array('value' => $beneficio['PersonaBeneficio']['persona_id'])); ?>
	<?php echo $frm->hidden('PersonaBeneficio.action',array('value' => 'MAXIMO_REG_CBU')); ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/persona_beneficios/index/".$persona['Persona']['id'] : $fwrd) ))?>
</div>



<?php 
if(isset($persona['Socio']['id']) && $persona['Socio']['id'] != 0) echo $this->renderElement('orden_descuento/grilla_ordenes_by_beneficio',array('plugin' => 'mutual','socio_id' => $persona['Socio']['id'], 'persona_beneficio_id' => $beneficio['PersonaBeneficio']['id']));
?>
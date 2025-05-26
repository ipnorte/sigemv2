<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<h3>AGREGAR UN NUEVO BENEFICIO COMPARTIDO</h3>

<div class="areaDatoForm">
	<h4>BENEFICIO PRINCIPAL AL CUAL SE ASOCIAN LOS BENEFICIOS COMPARTIDOS</h4>
	ORGANISMO: <strong><?php echo $beneficio['PersonaBeneficio']['codigo_beneficio_desc']?></strong>
	<br/>
	BENEFICIO: <strong><?php echo $beneficio['PersonaBeneficio']['string']?></strong>
	<br/>
	PORCENTAJE: <strong><?php echo $util->nf($beneficio['PersonaBeneficio']['porcentaje'])?> %</strong>	
</div>
<?php echo $form->create(null,array('name'=>'formAddBeneficioCompartido','id'=>'formAddBeneficioCompartido','onsubmit' => "",'action' => 'agregar_beneficio_compartido/'. $beneficio['PersonaBeneficio']['id']));?>
<div class="areaDatoForm">
	<?php if(substr($beneficio['PersonaBeneficio']['codigo_beneficio'],8,2) == '22'):?>
	<table class="tbl_form">
		<tr>
			<td>EMPRESA</td>
			<td><?php echo $frm->input('PersonaBeneficioCompartido.codigo_empresa',array('size'=>60,'maxlength'=>100,'value' => $beneficio['PersonaBeneficio']['codigo_empresa_desc'], 'disabled' => 'disabled')); ?></td>
		</tr>
		<tr>
			<td>COD.REPARTICION</td>
			<td>
				<?php echo $frm->input('PersonaBeneficioCompartido.codigo_reparticion',array('size'=>11,'maxlength'=>11)); ?>
				NRO.OP <input name="data[PersonaBeneficioCompartido][turno_pago]" type="text" size="7" maxlength="6" value="" id="PersonaBeneficioCompartidoTurnoPago" />
				PORCENTAJE <input name="data[PersonaBeneficioCompartido][porcentaje]" type="text" value="" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" id="PersonaBeneficioCompartidoPorcentaje" />
			</td>
		</tr>
	</table>
	<?php endif;?>

	<?php //   debug($beneficio)?>

</div>

<?php echo $frm->hidden('PersonaBeneficioCompartido.persona_id',array('value' => $beneficio['PersonaBeneficio']['persona_id'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.persona_beneficio_id',array('value' => $beneficio['PersonaBeneficio']['id'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.documento',array('value' => $persona['Persona']['documento'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.beneficiario',array('value' => $persona['Persona']['apellido']." ".$persona['Persona']['nombre'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.codigo_beneficio',array('value' => $beneficio['PersonaBeneficio']['codigo_beneficio'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.cbu',array('value' => $beneficio['PersonaBeneficio']['cbu'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.banco_id',array('value' => $beneficio['PersonaBeneficio']['banco_id'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.nro_sucursal',array('value' => $beneficio['PersonaBeneficio']['nro_sucursal'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.tipo_cta_bco',array('value' => $beneficio['PersonaBeneficio']['tipo_cta_bco'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.nro_cta_bco',array('value' => $beneficio['PersonaBeneficio']['nro_cta_bco'])); ?>
<?php echo $frm->hidden('PersonaBeneficioCompartido.codigo_empresa',array('value' => $beneficio['PersonaBeneficio']['codigo_empresa'])); ?>



<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/persona_beneficios/index/".$persona['Persona']['id'] : $fwrd) ))?>
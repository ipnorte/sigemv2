<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>
<script type="text/javascript">


Event.observe(window, 'load', function() {
	<?php if($persona['Persona']['fallecida'] == 1):?>
		$('formBajaBeneficio').disable();
		return;
	<?php endif;?>

	var beneficio = $('PersonaBeneficioPersonaBeneficioId').getValue();
	if(beneficio === null)$('reasignacion_beneficio').hide();

		
});

function validateForm(){
	var reasignar = document.getElementById('PersonaBeneficioAccion_r').checked;
	if(reasignar){
		var beneficio = $('PersonaBeneficioPersonaBeneficioId').getValue();
		if(beneficio === null){
			alert('Para reasignar un Beneficio debe poseer otro Beneficio ACTIVO');
			return false;
		}
	}

	var msg = "ATENCION! *** ";
	msg = msg  + "BAJA DE BENEFICIO ***\n";
	msg = msg  + "CAUSA: " + getTextoSelect('PersonaBeneficioCodigoBaja') + "\n";
	msg = msg  + "FECHA: " + getStrFecha('PersonaBeneficioFechaBaja') + "\n";
	msg = msg  + "\n";
        msg = msg  + "\n";
        msg = msg  + "A PARTIR DE: " + getTextoSelect('PersonaBeneficioPeriodoDesdeMonth') + "/" + getTextoSelect('PersonaBeneficioPeriodoDesdeYear');
        msg = msg  + "\n";
        msg = msg  + "\n";
	msg = msg  + "*** ACCION ***\n\n";
	if(!reasignar){
		msg = msg  + "DAR DE BAJA CUOTAS\n";
	}else{
		msg = msg  + "REASIGNAR ORDENES DE DESCUENTO Y CUOTAS ADEUDADAS AL BENEFICIO: \n\n";
		msg = msg  + getTextoSelect('PersonaBeneficioPersonaBeneficioId') + "\n";
	}
	
	return confirm(msg);
}


</script>


<?php echo $form->create(null,array('name'=>'formBajaBeneficio','id'=>'formBajaBeneficio','onsubmit' => "return validateForm()",'action' => 'baja/'. $beneficio['PersonaBeneficio']['id']));?>
<h3>BAJA BENEFICIO</h3>
<div class="areaDatoForm2">
<?php echo $this->requestAction('/pfyj/persona_beneficios/view/'.$beneficio['PersonaBeneficio']['id'])?>
</div>
<div class="areaDatoForm">
	<table class="tbl_form">
	
			<tr>
				<td>CAUSA DE LA BAJA</td>
				<td>
					<?php echo $this->renderElement('global_datos/combo',array(
																						'plugin'=>'config',
																						'label' => '.',
																						'model' => 'PersonaBeneficio.codigo_baja',
																						'prefijo' => 'MUTUBABE',
																						'disable' => false,
																						'empty' => false,
																						'selected' => '0',
																						'logico' => true,
					))?>				
				</td>
			</tr>
			<tr>
				<td>FECHA</td>
				<td><?php echo $frm->input('PersonaBeneficio.fecha_baja',array('dateFormat' => 'DMY'))?></td>
			</tr>
			<tr>
				<td style="color: red;font-weight: bold;">A PARTIR DE</td>
				<td>
                                    <?php // echo $frm->periodo('PersonaBeneficio.periodo_desde','',(isset($periodo_corte) ? $periodo_corte :  null),date('Y')-1,date('Y')+1,false)?>
                                    <?php // echo $this->renderElement("liquidacion/periodos_liquidados",array('plugin' => 'mutual','order' => 'ASC','model' => 'PersonaBeneficio.periodo_desde', 'facturados' => false, 'organismo' => $beneficio['PersonaBeneficio']['codigo_beneficio']))?>
									<?php echo $frm->input('PersonaBeneficio.periodo_desde',array('type' => 'select', 'options' => $periodos))?>
									</td>
			</tr>                        
                        
			<tr>
				<td valign="top">OBSERVACIONES</td>
				<td><?php echo $frm->textarea('observaciones',array('cols' => 60, 'rows' => 10))?></td>
			</tr>
			<tr id="reasignacion_beneficio">
                <td style="color: green;font-weight: bold;">REASIGNAR BENEFICIO</td>
				<td>
					<input type="radio" name="data[PersonaBeneficio][accion]" id="PersonaBeneficioAccion_r" checked="checked" value="R" onclick="$('PersonaBeneficioPersonaBeneficioId').enable();"/>
					<?php echo $this->requestAction('/pfyj/persona_beneficios/combo/PersonaBeneficio/'.$beneficio['PersonaBeneficio']['persona_id'].'/0/0/'.$beneficio['PersonaBeneficio']['id'])?>
				</td>
			</tr>
			<tr>
                <td style="color: red;font-weight: bold;">DAR DE BAJA CUOTAS</td>
				<td>
					<input type="radio" name="data[PersonaBeneficio][accion]" id="PersonaBeneficioAccion_b" value="B" onclick="$('PersonaBeneficioPersonaBeneficioId').disable();"/>
				</td>
			</tr>
			
	</table>

	
<div style="clear: both;"></div>

</div>
<?php echo $frm->hidden('PersonaBeneficio.activo',array('value' => 0)); ?>
<?php echo $frm->hidden('PersonaBeneficio.persona_id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.idr_persona',array('value' => $persona['Persona']['idr'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.id',array('value' => $beneficio['PersonaBeneficio']['id'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.idr',array('value' => $beneficio['PersonaBeneficio']['idr'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'DAR DE BAJA EL BENEFICIO','URL' => ( empty($fwrd) ? "/pfyj/persona_beneficios/index/".$persona['Persona']['id'] : $fwrd) ))?>

<?php 
if(isset($persona['Socio']['id']) && $persona['Socio']['id'] != 0) echo $this->renderElement('orden_descuento/grilla_ordenes_by_beneficio',array('plugin' => 'mutual','socio_id' => $persona['Socio']['id'], 'persona_beneficio_id' => $beneficio['PersonaBeneficio']['id'],'solo_adeudadas' => 1));
?>
<?php 
$busquedaAvanzada = (isset($busquedaAvanzada) && $busquedaAvanzada ? true : false);
$showOnLoad = (isset($showOnLoad) && $showOnLoad ? true : false);
$porSocio = (isset($nro_socio) && $nro_socio ? true : false);
$porSocio = (isset($nro_socio) && $nro_socio ? true : false);
$tipo_busqueda_avanzada = (isset($tipo_busqueda_avanzada) ? $tipo_busqueda_avanzada : "");

?>
<div id="FormSearch">
	<script type="text/javascript">
	function fillDocumento(){
		if($('PersonaDocumento').getValue()!== '')document.getElementById('PersonaDocumento').value = rellenar($('PersonaDocumento').getValue(),'0',8,'L');
		return true;
	}
	function cleanForm(){
		document.getElementById('PersonaDocumento').value = "";
		document.getElementById('PersonaApellido').value = "";
		document.getElementById('PersonaNombre').value = "";
	}

	<?php if($busquedaAvanzada):?>
	Event.observe(window, 'load', function(){
		$('PersonaDocumento').focus();
		<?php if(!$showOnLoad):?>
		$('busquedaAvanzada').hide();
		document.getElementById("PersonaBusquedaAvanzada").value = 0;
		<?php else:?>
		document.getElementById("PersonaBusquedaAvanzada").value = 1;
		<?php endif;?>
		var organismo = $('PersonaBeneficioCodigoBeneficio').getValue();
		disableElementosForm(organismo);

		$('PersonaBeneficioCodigoBeneficio').observe('change',function(){
			organismo = $('PersonaBeneficioCodigoBeneficio').getValue();
			disableElementosForm(organismo);		
		});

		$('PersonaDocumento').focus();
		
	});

	function disableElementosForm(organismo){
		org = organismo.substr(8,2);
		if(org === '22'){
			$('PersonaBeneficioCbu').enable();	
			$('PersonaBeneficioCbu').focus();
			$('PersonaBeneficioTipo').disable();
			$('PersonaBeneficioNroLey').disable();
			$('PersonaBeneficioNroBeneficio').disable();
			$('PersonaBeneficioSubBeneficio').disable();
			document.getElementById('PersonaBeneficioTipo').value = "";
			document.getElementById('PersonaBeneficioNroLey').value = "";
			document.getElementById('PersonaBeneficioNroBeneficio').value = "";
			document.getElementById('PersonaBeneficioSubBeneficio').value = "";		
		}
		if(org === '77'){
			$('PersonaBeneficioTipo').enable();
			$('PersonaBeneficioNroLey').enable();
			$('PersonaBeneficioNroBeneficio').enable();
			$('PersonaBeneficioSubBeneficio').enable();		
			$('PersonaBeneficioNroLey').focus();
			$('PersonaBeneficioCbu').disable();
			document.getElementById('PersonaBeneficioCbu').value = "";
		}
		if(org === '66'){
			$('PersonaBeneficioNroBeneficio').enable();
			$('PersonaBeneficioNroBeneficio').focus();
			$('PersonaBeneficioTipo').enable();
			document.getElementById('PersonaBeneficioTipo').value = 1;
			$('PersonaBeneficioNroLey').disable();
			$('PersonaBeneficioSubBeneficio').disable();
			$('PersonaBeneficioCbu').disable();
			document.getElementById('PersonaBeneficioNroLey').value = "";
			document.getElementById('PersonaBeneficioSubBeneficio').value = "";
			document.getElementById('PersonaBeneficioCbu').value = "";
		}		
	}


	function BusquedaAvanzada(){
		$('busquedaAvanzada').toggle();
		document.getElementById("PersonaBusquedaAvanzada").value = 1;
		if(!$('busquedaAvanzada').visible()){
			document.getElementById('PersonaBeneficioCbu').value = "";
			document.getElementById('PersonaBeneficioTipo').value = "";
			document.getElementById('PersonaBeneficioNroLey').value = "";
			document.getElementById('PersonaBeneficioNroBeneficio').value = "";
			document.getElementById('PersonaBeneficioSubBeneficio').value = "";	
			document.getElementById("PersonaBusquedaAvanzada").value = 0;	
		}
	}
	<?php else:?>

	Event.observe(window, 'load', function(){
		$('PersonaDocumento').focus();
	});
		
	<?php endif;?>	
	</script>
	<?php echo $form->create(null,array('id' => 'searchPersonaForm','action'=> (isset($accion) ? $accion : 'index'),'onsubmit' => "return fillDocumento()"));?>
	<table>
		<tr>
			<td colspan="8"><strong>BUSCAR PERSONA</strong></td>
		</tr>
		<tr>
			<td>
				<?php //   echo $this->requestAction('/config/global_datos/combo/TIPO/Persona.tipo_documento/PERSTPDC');?>
				<?php echo $this->renderElement('global_datos/combo_global',array('plugin' => 'config','model' => 'Persona.tipo_documento' ,'metodo' => 'get_tipos_documento','empty' => 1,'selected' => ""))?>
			</td>
			<td><?php echo $frm->number('Persona.documento',array('label'=>'DOCUMENTO','size'=>12,'maxlength'=>11,'value' => $this->data['Persona']['documento'])); ?></td>
			<td><?php echo $frm->input('Persona.apellido',array('label'=>'APELLIDO','size'=>20,'maxlength'=>100,'value' => $this->data['Persona']['apellido'])); ?></td>
			<td><?php echo $frm->input('Persona.nombre',array('label'=>'NOMBRE','size'=>20,'maxlength'=>100,'value' => $this->data['Persona']['nombre'])); ?></td>
			<td><?php if($porSocio) echo $frm->number('Persona.nro_socio',array('label'=>'NRO SOCIO','size'=>10,'maxlength'=>10,'value' => $this->data['Persona']['nro_socio'])); ?></td>
			<td><input type="submit" class="btn_consultar" value="APROXIMAR" /></td>
			<td><?php echo $frm->reset('searchPersonaForm')?></td>
			<?php if($busquedaAvanzada):?>
			<td><div style="cursor:pointer;padding:3px;font-size:11px;text-decoration:underline;" onclick="BusquedaAvanzada();">Busqueda Avanzada</div></td>
			<?php endif;?>
		</tr>
		<?php if($busquedaAvanzada):?>
		<tr id="busquedaAvanzada">
			<td colspan="8">
					<table class="tbl_form">
						<tr>
							<td>
                            <?php echo $this->renderElement('global_datos/combo_global',array(
                                                                                            'plugin'=>'config',
                                                                                            'label' => " ",
                                                                                            'model' => 'PersonaBeneficio.codigo_beneficio',
                                                                                            'prefijo' => 'MUTUCORG',
                                                                                            'disabled' => false,
                                                                                            'empty' => true,
                                                                                            'metodo' => "get_organismos",
                                                                                            'selected' => (isset($this->data['PersonaBeneficio']['codigo_beneficio']) ? $this->data['PersonaBeneficio']['codigo_beneficio'] : ""),    
                            ))?>	                            
                            </td>
							<td><?php echo $frm->input('PersonaBeneficio.tipo',array('type' => 'select','options' => array('' => '',1 => 'JUBILADO', 0 => 'PENSIONADO')))?></td>
							<td><?php echo $frm->number('PersonaBeneficio.nro_ley',array('label'=>'LEY','size'=>2,'maxlength'=>2)); ?></td>
							<td><?php echo $frm->number('PersonaBeneficio.nro_beneficio',array('label'=>'BENEFICIO','size'=>20,'maxlength'=>50)); ?></td>
							<td><?php echo $frm->number('PersonaBeneficio.sub_beneficio',array('label'=>'SUB-BENEFICIO','size'=>2,'maxlength'=>2)); ?></td>
							<td><?php echo $frm->number('PersonaBeneficio.cbu',array('label'=>'CBU','size'=>24,'maxlength'=>22)); ?></td>
						</tr>
						
					</table>
					<?php echo $frm->hidden("Persona.busquedaAvanzada",array('value' => 0))?>
			</td>
		</tr>
		<?php endif;?>
	</table>
	<?php echo $frm->hidden("Persona.tipo_busqueda_avanzada",array('value' => $tipo_busqueda_avanzada))?>
	<?php echo $form->end();?> 
</div>
<div style="clear: both;"></div>
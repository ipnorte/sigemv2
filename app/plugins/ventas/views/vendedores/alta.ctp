<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PADRON DE VENDEDORES :: ALTA '))?>
<script type="text/javascript">
	function validateForm(){
		
		if(!validRequired('PersonaDocumento','')) return false;
		if(!validRequired('PersonaApellido','')) return false;
		if(!validRequired('PersonaNombre','')) return false;
		if(!controlCUIT($('PersonaCuitCuil').getValue(),'PersonaCuitCuil','')) return false;
		document.getElementById('PersonaDocumento').value = rellenar($('PersonaDocumento').getValue(),'0',8,'L');
		if(!cuit_ndoc('PersonaDocumento','PersonaCuitCuil','')) return false;
		if(!validRequired('PersonaTelefonoFijo','')) return false;
		if(!validRequired('PersonaCalle','')) return false;
		if(!validRequired('PersonaNumeroCalle','')) return false;
		if(!validRequired('PersonaLocalidadAproxima','')) return false;
		if(!validRequired('PersonaCodigoPostal','')) return false;
		return true;
	}
</script>

<?php echo $form->create(null,array('name'=>'formAddPersona','id'=>'formAddPersona','onsubmit' => "return validateForm()",'action' => 'alta' ));?>

			<div class="areaDatoForm">
		
			<h3>DATOS PERSONALES</h3>
		
				<table class="tbl_form">
					<tr>
						<td>TIPO DOCUMENTO</td>
						<td><?php echo $this->requestAction('/config/global_datos/combo/./Persona.tipo_documento/PERSTPDC/0/0/'.$this->data['Persona']['tipo_documento']);?></td>
					</tr>
					<tr>
						<td>NRO DOCUMENTO</td>
						<td><?php echo $frm->input('Persona.documento',array('label'=>'','size'=>'15','maxlength'=>'11'));?></td>
					</tr>
					<tr>
						<td>APELLIDO</td>
						<td>
							<div class="input text">
							<label for="PersonaApellido"></label>	
							<input name="data[Persona][apellido]" type="text" size="40"  value="<?php echo $this->data['Persona']['apellido']?>" maxlength="100" id="PersonaApellido" />
							</div>
						</td>
					</tr>					
					<tr>
						<td>NOMBRES</td>	
						<td><?php echo $frm->input('Persona.nombre',array('label'=>'','size'=>40,'maxlength'=>100)); ?></td>
					</tr>
					<tr>
						<td>SEXO</td>	
						<td><?php echo $form->input('Persona.sexo',array('type'=>'select','options'=>array('M' =>'MASCULINO', 'F'=>'FEMENINO'),'empty'=>false,'label'=>''));?></td>
					</tr>					
				
					<tr>
						<td>CUIT CUIL</td>	
						<td><?php echo $frm->number('Persona.cuit_cuil',array('label'=>'','size'=>12,'maxlength'=>11)); ?></td>
					</tr>				
				
					<tr>
						<td>FECHA NACIMIENTO</td>	
						<td><?php echo $frm->input('Persona.fecha_nacimiento',array('dateFormat' => 'DMY','label'=>'','minYear'=>'1900', 'maxYear' => date("Y") - 18))?></td>
					</tr>
					<tr>
						<td>ESTADO CIVIL</td>	
						<td><?php echo $this->requestAction('/config/global_datos/combo/./Persona.estado_civil/PERSXXEC/0/0/'.$this->data['Persona']['estado_civil']);?></td>
					</tr>														
					<tr>
						<td>NOMBRE CONYUGE</td>	
						<td><?php echo $frm->input('Persona.nombre_conyuge',array('label'=>'','size'=>100,'maxlength'=>100)); ?></td>
					</tr>
					<tr>
						<td>TELEFONO FIJO</td>	
						<td><?php echo $frm->input('Persona.telefono_fijo',array('label'=>'','size'=>50,'maxlength'=>50)); ?></td>
					</tr>
					<tr>
						<td>TELEFONO MOVIL</td>	
						<td><?php echo $frm->input('Persona.telefono_movil',array('label'=>'','size'=>20,'maxlength'=>20)); ?></td>
					</tr>
					<tr>
						<td>EMAIL</td>	
						<td><?php echo $frm->input('Persona.e_mail',array('label'=>'','size'=>50,'maxlength'=>50)); ?></td>
					</tr>
					<tr>
						<td>TELEFONO REFERENCIA</td>	
						<td><?php echo $frm->input('Persona.telefono_referencia',array('label'=>'','size'=>30,'maxlength'=>27)); ?></td>
					</tr>
					<tr>
						<td>PERSONA REFERENCIA</td>	
						<td><?php echo $frm->input('Persona.persona_referencia',array('label'=>'','size'=>40,'maxlength'=>40)); ?></td>
					</tr>																														
				</table>
            
				<div style="clear: both;"></div>
	
			</div>
			
			<div class="areaDatoForm">
				
				<h3>DOMICILIO</h3>

				<table class="tbl_form">
					<tr>
						<td>CALLE</td>	
						<td><?php echo $frm->input('Persona.calle',array('label'=>'','size'=>40,'maxlength'=>31)); ?></td>
					</tr>
					<tr>
						<td>NUMERO</td>	
						<td><?php echo $frm->number('Persona.numero_calle',array('label'=>'')); ?></td>
					</tr>
					<tr>
						<td>PISO</td>	
						<td><?php echo $frm->input('Persona.piso',array('label'=>'','size'=>3,'maxlength'=>3)); ?></td>
					</tr>
					<tr>
						<td>DPTO</td>	
						<td><?php echo $frm->input('Persona.dpto',array('label'=>'','size'=>3,'maxlength'=>3)); ?></td>
					</tr>
					<tr>
						<td>BARRIO</td>	
						<td><?php echo $frm->input('Persona.barrio',array('label'=>'','size'=>30,'maxlength'=>20)); ?></td>
					</tr>																									
				</table>
				
				<div class='row'>
					<?php echo $this->requestAction('/config/localidades/form/Persona/'.$this->data['Persona']['localidad_id'].'/'.$this->data['Persona']['localidad'].'/'.$this->data['Persona']['codigo_postal'].'/'.$this->data['Persona']['provincia_id']); ?>
				</div>				
				
				<div style="clear: both;"></div>
								
			
			</div>
			<?php echo $frm->hidden('Persona.idr_persona',array('value' => 0)); ?>
			<?php echo $frm->hidden('Persona.fallecida',array('value' => 0)); ?>
			<?//=$frm->hidden('Persona.tipo_documento'); ?>
			<?//=$frm->hidden('Persona.id'); ?>	
    
<?php echo $frm->btnGuardarCancelar(array('URL' => '/ventas/vendedores'))?>
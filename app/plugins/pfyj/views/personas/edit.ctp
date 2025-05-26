<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<script type="text/javascript">
	function validateForm(){
		
//		if(!validRequired('PersonaApellido','')) return false;
//		if(!validRequired('PersonaNombre','')) return false;
//		if(!controlCUIT($('PersonaCuitCuil').getValue(),'PersonaCuitCuil','')) return false;
//		if(!cuit_ndoc('PersonaDocumento','PersonaCuitCuil','')) return false;
		if(!validRequired('PersonaTelefonoFijo','')) return false;
		if(!validRequired('PersonaCalle','')) return false;
		if(!validRequired('PersonaNumeroCalle','')) return false;
		if(!validRequired('PersonaLocalidadAproxima','')) return false;
		if(!validRequired('PersonaCodigoPostal','')) return false;
		return true;
	}
</script>


<h3>DATOS PERSONALES</h3>
<?php echo $form->create(null,array('name'=>'formAddPersona','id'=>'formAddPersona','onsubmit' => "return validateForm()" ));?>
			<div class="areaDatoForm">
				<h3>DATOS PRINCIPALES</h3>
				<table class="tbl_form">
					<tr>
						<td>TIPO Y NRO. DE DOCUMENTO:</td>
						<td><strong><?php echo $util->globalDato($this->data['Persona']['tipo_documento'])?></strong> - <strong><?php echo $this->data['Persona']['documento']?></strong></td>
					</tr>
					<tr>
						<td>APELLIDO Y NOMBRE:</td>
						<td><strong><?php echo $this->data['Persona']['apellido'].", ".$this->data['Persona']['nombre']?></strong></td>
					</tr>
					<tr>
						<td>CUIT-CUIL</td>
						<td><strong><?php echo $this->data['Persona']['cuit_cuil']?></strong></td>
					</tr>
					<tr>
						<td>MODIFICAR ESTA INFORMACION</td>
						<td><?php echo $controles->botonGenerico("/pfyj/personas/modificar_apenom/".$this->data['Persona']['id'],"controles/edit.png")?></td>
					</tr>
				</table>
				
				<div id="edita_datos_personales" style="display: none;">
				
					#form_editar_datos_personales#
				
				</div>
				 
			</div>
			<div class="areaDatoForm">
				<h3>DATOS GENERALES</h3>
				<table class="tbl_form">
					<!-- 
					<tr>
						<td>TIPO DOCUMENTO</td>
						<td><?php echo $this->requestAction('/config/global_datos/combo/./Persona.tipo_documento/PERSTPDC/1/0/'.$this->data['Persona']['tipo_documento']);?></td>
					</tr>
					<tr>
						<td>NRO DOCUMENTO</td>
						<td><?php echo $frm->input('Persona.documento',array('label'=>'','size'=>'15','maxlength'=>'11','disabled'=>'disabled'));?></td>
					</tr>
					<tr>
						<td>APELLIDO</td>
						<td><?php echo $frm->input('Persona.apellido',array('label'=>'','size'=>40,'maxlength'=>100,'disabled'=>'disabled')); ?></td>
					</tr>					
					<tr>
						<td>NOMBRES</td>	
						<td><?php echo $frm->input('Persona.nombre',array('label'=>'','size'=>40,'maxlength'=>100,'disabled'=>'disabled')); ?></td>
					</tr>
					<tr>
						<td>CUIT CUIL</td>	
						<td><?php echo $frm->number('Persona.cuit_cuil',array('label'=>'','size'=>12,'maxlength'=>11, 'disabled'=>'disabled')); ?></td>
					</tr>					
					 -->
					<tr>
						<td>SEXO</td>	
						<td><?php echo $form->input('Persona.sexo',array('type'=>'select','options'=>array('M' =>'MASCULINO', 'F'=>'FEMENINO'),'empty'=>false,'label'=>''));?></td>
					</tr>					
				
				
				
					<tr>
						<td>FECHA NACIMIENTO</td>	
						<td><?php echo $frm->input('Persona.fecha_nacimiento',array('dateFormat' => 'DMY','label'=>'','minYear'=>'1900', 'maxYear' => date("Y") - 18))?></td>
					</tr>
					<tr>
							
						<td><?php echo $frm->input('Persona.fallecida',array('label'=>'FALLECIDA','onclick' => "\$('cargarFechaFallecida').toggle();")); ?></td>
						<td><span id="cargarFechaFallecida" <?php echo ($this->data['Persona']['fallecida'] == 1 ? "" : "style=\"display: none;\"")?> ><?php echo $frm->input('Persona.fecha_fallecimiento',array('dateFormat' => 'DMY','label'=>'','minYear'=>'1900', 'maxYear' => date("Y")))?></span></td>
					</tr>
					<tr>
						<td>ESTADO CIVIL</td>	
						<td><?php echo $this->requestAction('/config/global_datos/combo/./Persona.estado_civil/PERSXXEC/0/0/'.$this->data['Persona']['estado_civil']);?></td>
					</tr>														
					<tr>
						<td>NOMBRE CONYUGE</td>	
						<td><?php echo $frm->input('Persona.nombre_conyuge',array('label'=>'','size'=>40,'maxlength'=>100)); ?></td>
					</tr>
					<tr>
						<td>TELEFONO FIJO</td>	
						<td><?php echo $frm->input('Persona.telefono_fijo',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
					</tr>
					<tr>
						<td>TELEFONO MOVIL</td>	
						<td><?php echo $frm->input('Persona.telefono_movil',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
					</tr>
					<tr>
						<td>EMAIL</td>	
						<td><?php echo $frm->input('Persona.e_mail',array('label'=>'','size'=>30,'maxlength'=>50)); ?></td>
					</tr>
					<tr>
						<td>TELEFONO REFERENCIA</td>	
						<td><?php echo $frm->input('Persona.telefono_referencia',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
					</tr>
					<tr>
						<td>PERSONA REFERENCIA</td>	
						<td><?php echo $frm->input('Persona.persona_referencia',array('label'=>'','size'=>40,'maxlength'=>100)); ?></td>
					</tr>																														
				</table>
			
			<div style="clear: both;"></div>
	
			</div>
			
			<div class="areaDatoForm">
				
				<h3>DOMICILIO</h3>
				
				<table class="tbl_form">
					<tr>
						<td>CALLE</td>	
						<td><?php echo $frm->input('Persona.calle',array('label'=>'','size'=>40,'maxlength'=>100)); ?></td>
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
						<td><?php echo $frm->input('Persona.barrio',array('label'=>'','size'=>30,'maxlength'=>100)); ?></td>
					</tr>
				<tr>
						<td>ENTRE CALLES</td>	
						<td>
                                                    <table class="tbl_form">
                                                        <tr>
                                                            <td><?php echo $frm->input('Persona.entre_calle_1',array('label'=>'','size'=>40,'maxlength'=>40)); ?></td>
                                                            <td>Y</td>
                                                            <td><?php echo $frm->input('Persona.entre_calle_2',array('label'=>'','size'=>40,'maxlength'=>40)); ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
					</tr>                                        
				</table>
				
				<div class='row'>
					<?php echo $this->requestAction('/config/localidades/form/Persona/'.$this->data['Persona']['localidad_id'].'/'.$this->data['Persona']['localidad'].'/'.$this->data['Persona']['codigo_postal'].'/'.$this->data['Persona']['provincia_id']); ?>
				</div>				
				
				<div style="clear: both;"></div>
                                <?php echo $this->renderElement('personas/forms/geolocalizacion_latlong_form',array('persona_id' => $this->data['Persona']['id'],'maps_latitud' => $this->data['Persona']['maps_latitud'],'maps_longitud' => $this->data['Persona']['maps_longitud'],'plugin' => 'pfyj'))?>

                             
			
			
			</div>
			
			<input type="hidden" name="data[Persona][nombre]" value="<?php echo $this->data['Persona']['nombre']?>" id="PersonaNombre" />
			<input type="hidden" name="data[Persona][apellido]" value="<?php echo $this->data['Persona']['apellido']?>" id="PersonaApellido" />
			
			<?php echo $frm->hidden('Persona.documento',array('value' => $this->data['Persona']['documento'])); ?>
			<?php echo $frm->hidden('Persona.tipo_documento',array('value' => $this->data['Persona']['tipo_documento'])); ?>
			<?php echo $frm->hidden('Persona.cuit_cuil',array('value' => $this->data['Persona']['cuit_cuil'])); ?>
			<?php echo $frm->hidden('Persona.id'); ?>
			<?php echo $frm->hidden('Persona.idr_persona',array('value' => $this->data['Persona']['idr'])); ?>			
    
		<?php echo $frm->submit('GUARDAR')?>
		
<?php echo $form->end();?> 
                        
<?php // debug($persona)?>
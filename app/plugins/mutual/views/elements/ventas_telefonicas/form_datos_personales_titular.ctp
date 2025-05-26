<h3>ACTUALIZAR DATOS DEL TITULAR</h3>
<div class="areaDatoForm">

	<h4>DATOS GENERALES</h4>
	<hr/>
	<table class="tbl_form">
		<tr>
			<td>SEXO</td>	
			<td><?php echo $form->input('Persona.sexo',array('type'=>'select','options'=>array('M' =>'MASCULINO', 'F'=>'FEMENINO'),'empty'=>false,'label'=>'', 'selected' => $persona['Persona']['sexo']));?></td>
		</tr>					
		<tr>
			<td>FECHA NACIMIENTO</td>	
			<td><?php echo $frm->calendar('Persona.fecha_nacimiento','',$persona['Persona']['fecha_nacimiento'],'1900',date('Y') - 18)?></td>
		</tr>
		<tr>
			<td>ESTADO CIVIL</td>	
			<td><?php echo $this->requestAction('/config/global_datos/combo/./Persona.estado_civil/PERSXXEC/0/0/'.$persona['Persona']['estado_civil']);?></td>
		</tr>														
		<tr>
			<td>NOMBRE CONYUGE</td>	
			<td><?php echo $frm->input('Persona.nombre_conyuge',array('label'=>'','size'=>40,'maxlength'=>100,'value' => $persona['Persona']['nombre_conyuge'])); ?></td>
		</tr>
		<tr>
			<td>TELEFONO FIJO</td>	
			<td><?php echo $frm->input('Persona.telefono_fijo',array('label'=>'','size'=>20,'maxlength'=>50,'value' => $persona['Persona']['telefono_fijo'])); ?></td>
		</tr>
		<tr>
			<td>TELEFONO MOVIL</td>	
			<td><?php echo $frm->input('Persona.telefono_movil',array('label'=>'','size'=>20,'maxlength'=>50,'value' => $persona['Persona']['telefono_movil'])); ?></td>
		</tr>
		<tr>
			<td>EMAIL</td>	
			<td><?php echo $frm->input('Persona.e_mail',array('label'=>'','size'=>30,'maxlength'=>50,'value' => $persona['Persona']['e_mail'])); ?></td>
		</tr>
		<tr>
			<td>TELEFONO REFERENCIA</td>	
			<td><?php echo $frm->input('Persona.telefono_referencia',array('label'=>'','size'=>20,'maxlength'=>50,'value' => $persona['Persona']['telefono_referencia'])); ?></td>
		</tr>
		<tr>
			<td>PERSONA REFERENCIA</td>	
			<td><?php echo $frm->input('Persona.persona_referencia',array('label'=>'','size'=>40,'maxlength'=>100,'value' => $persona['Persona']['persona_referencia'])); ?></td>
		</tr>																														
	</table>
</div>
<div class="areaDatoForm">	
	<h4>DOMICILIO ACTUAL</h4>
	<hr/>
	<table class="tbl_form">
		<tr>
			<td>CALLE</td>	
			<td><?php echo $frm->input('Persona.calle',array('label'=>'','size'=>40,'maxlength'=>100,'value' => $persona['Persona']['calle'])); ?></td>
		</tr>
		<tr>
			<td>NUMERO</td>	
			<td><?php echo $frm->number('Persona.numero_calle',array('label'=>'','value' => $persona['Persona']['numero_calle'])); ?></td>
		</tr>
		<tr>
			<td>PISO</td>	
			<td><?php echo $frm->input('Persona.piso',array('label'=>'','size'=>3,'maxlength'=>3,'value' => $persona['Persona']['piso'])); ?></td>
		</tr>
		<tr>
			<td>DPTO</td>	
			<td><?php echo $frm->input('Persona.dpto',array('label'=>'','size'=>3,'maxlength'=>3,'value' => $persona['Persona']['dpto'])); ?></td>
		</tr>
		<tr>
			<td>BARRIO</td>	
			<td><?php echo $frm->input('Persona.barrio',array('label'=>'','size'=>30,'maxlength'=>100,'value' => $persona['Persona']['barrio'])); ?></td>
		</tr>																									
	</table>
	
	<div class='row'>
		<?php echo $this->requestAction('/config/localidades/form/Persona/'.$persona['Persona']['localidad_id'].'/'.$persona['Persona']['localidad'].'/'.$persona['Persona']['codigo_postal'].'/'.$this->data['Persona']['provincia_id']); ?>
	</div>	
</div>
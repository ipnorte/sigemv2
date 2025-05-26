<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<h3>NUEVO ADICIONAL</h3>

<script type="text/javascript">
	function validateForm(){
		
		if(!validRequired('SocioAdicionalDocumento','')) return false;
		if(!validRequired('SocioAdicionalApellido','')) return false;
		if(!validRequired('SocioAdicionalNombre','')) return false;
		document.getElementById('SocioAdicionalDocumento').value = rellenar($('SocioAdicionalDocumento').getValue(),'0',8,'L');
		if(!validRequired('SocioAdicionalCalle','')) return false;
		if(!validRequired('SocioAdicionalNumeroCalle','')) return false;
		if(!validRequired('SocioAdicionalLocalidadAproxima','')) return false;
		if(!validRequired('SocioAdicionalCodigoPostal','')) return false;
		return true;
	}
</script>

<?php echo $form->create(null,array('action' => "add/".$persona['Persona']['id'],'name'=>'formAddAdicional','id'=>'formAddAdicional','onsubmit' => "return validateForm()" ));?>
<div class="areaDatoForm">
	<h3>DATOS PERSONALES DEL ADICIONAL</h3>
	<table class="tbl_form">
		<tr>
			<td>TIPO DOCUMENTO</td>
			<td><?php echo $this->requestAction('/config/global_datos/combo/./SocioAdicional.tipo_documento/PERSTPDC/0/0/');?></td>
		</tr>
		<tr>
			<td>NRO DOCUMENTO</td>
			<td><?php echo $frm->number('SocioAdicional.documento',array('label'=>'','size'=>'15','maxlength'=>'11'));?></td>
		</tr>
		<tr>
			<td>APELLIDO</td>
			<td>
				<div class="input text">
				<label for="SocioAdicionalApellido"></label>	
				<input name="data[SocioAdicional][apellido]" type="text" size="40" maxlength="100" value="<?php echo $this->data['SocioAdicional']['apellido']?>" maxlength="100" id="SocioAdicionalApellido" />
				</div>
			</td>
		</tr>					
		<tr>
			<td>NOMBRES</td>	
			<td><?php echo $frm->input('SocioAdicional.nombre',array('label'=>'','size'=>40,'maxlength'=>100)); ?></td>
		</tr>
		<tr>
			<td>SEXO</td>	
			<td><?php echo $form->input('SocioAdicional.sexo',array('type'=>'select','options'=>array('M' =>'MASCULINO', 'F'=>'FEMENINO'),'empty'=>false,'label'=>''));?></td>
		</tr>
		<tr>
			<td>FECHA NACIMIENTO</td>	
			<td><?php echo $frm->input('SocioAdicional.fecha_nacimiento',array('dateFormat' => 'DMY','label'=>'','minYear'=>'1900', 'maxYear' => date("Y")))?></td>
		</tr>
		<tr>
			<td>VINCULO</td>	
			<td><?php echo $this->requestAction('/config/global_datos/combo/./SocioAdicional.vinculo/PERSVINC/0/0/');?></td>
		</tr>
	</table>
</div>
<div class="areaDatoForm">
	<h3>DOMICILIO</h3>
	<table class="tbl_form">	
		<tr>
			<td>CALLE</td>	
			<td><?php echo $frm->input('SocioAdicional.calle',array('label'=>'','size'=>40,'maxlength'=>100,'value' => $calle)); ?></td>
		</tr>
		<tr>
			<td>NUMERO</td>	
			<td><?php echo $frm->number('SocioAdicional.numero_calle',array('label'=>'','value' => $numero_calle)); ?></td>
		</tr>
		<tr>
			<td>PISO</td>	
			<td><?php echo $frm->input('SocioAdicional.piso',array('label'=>'','size'=>3,'maxlength'=>3,'value' => $piso)); ?></td>
		</tr>
		<tr>
			<td>DPTO</td>	
			<td><?php echo $frm->input('SocioAdicional.dpto',array('label'=>'','size'=>3,'maxlength'=>3,'value' => $dpto)); ?></td>
		</tr>
		<tr>
			<td>BARRIO</td>	
			<td><?php echo $frm->input('SocioAdicional.barrio',array('label'=>'','size'=>30,'maxlength'=>100,'value' => $barrio)); ?></td>
		</tr>																									
	</table>	
	<div class='row'>
	<?php echo $this->requestAction('/config/localidades/form/SocioAdicional/'.$localidad_id.'/'.$localidad.'/'.$codigo_postal.'/'.$provincia_id); ?>
	</div>
</div>


<?php echo $frm->hidden('SocioAdicional.id',array('value' => 0)); ?>
<?php echo $frm->hidden('SocioAdicional.persona_id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->hidden('SocioAdicional.socio_id',array('value' => (!empty($persona['Socio']) ? $persona['Socio']['id'] : 0))); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/pfyj/socio_adicionales/index/'.$persona['Persona']['id']))?>
<?php //   debug($persona)?>
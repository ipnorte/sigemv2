<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>

<h3>MODIFICAR DATOS DEL ADICIONAL</h3>

<script type="text/javascript">
	function validateForm(){
		
		if(!validRequired('SocioAdicionalApellido','')) return false;
		if(!validRequired('SocioAdicionalNombre','')) return false;
		if(!validRequired('SocioAdicionalCalle','')) return false;
		if(!validRequired('SocioAdicionalNumeroCalle','')) return false;
		if(!validRequired('SocioAdicionalLocalidadAproxima','')) return false;
		if(!validRequired('SocioAdicionalCodigoPostal','')) return false;
		return true;
	}
</script>

<?php echo $form->create(null,array('action' => "edit/".$this->data['SocioAdicional']['id'],'name'=>'formEditAdicional','id'=>'formEditAdicional','onsubmit' => "return validateForm()" ));?>
<div class="areaDatoForm">
	<h3>DATOS PERSONALES DEL ADICIONAL</h3>
	<table class="tbl_form">
		<tr>
			<td>DOCUMENTO:</td>
			<td><?php echo $frm->input("SocioAdicional.tdoc_ndoc",array('value' => $this->data['SocioAdicional']['tdoc_ndoc'], 'disabled' => true))?></td>
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
			<td><?php echo $this->requestAction('/config/global_datos/combo/./SocioAdicional.vinculo/PERSVINC/0/0/' . $this->data['SocioAdicional']['vinculo']);?></td>
		</tr>
	</table>
</div>
<div class="areaDatoForm">
	<h3>DOMICILIO</h3>
	<table class="tbl_form">	
		<tr>
			<td>CALLE</td>	
			<td><?php echo $frm->input('SocioAdicional.calle',array('label'=>'','size'=>40,'maxlength'=>100,'value' => $this->data['SocioAdicional']['calle'])); ?></td>
		</tr>
		<tr>
			<td>NUMERO</td>	
			<td><?php echo $frm->number('SocioAdicional.numero_calle',array('label'=>'','value' => $this->data['SocioAdicional']['numero_calle'])); ?></td>
		</tr>
		<tr>
			<td>PISO</td>	
			<td><?php echo $frm->input('SocioAdicional.piso',array('label'=>'','size'=>3,'maxlength'=>3,'value' => $this->data['SocioAdicional']['piso'])); ?></td>
		</tr>
		<tr>
			<td>DPTO</td>	
			<td><?php echo $frm->input('SocioAdicional.dpto',array('label'=>'','size'=>3,'maxlength'=>3,'value' => $this->data['SocioAdicional']['dpto'])); ?></td>
		</tr>
		<tr>
			<td>BARRIO</td>	
			<td><?php echo $frm->input('SocioAdicional.barrio',array('label'=>'','size'=>30,'maxlength'=>100,'value' => $this->data['SocioAdicional']['barrio'])); ?></td>
		</tr>																									
	</table>	
	<div class='row'>
	<?php echo $this->requestAction('/config/localidades/form/SocioAdicional/'.$localidad_id.'/'.$localidad.'/'.$codigo_postal.'/'.$provincia_id); ?>
	</div>
</div>



<?php echo $frm->hidden('SocioAdicional.id'); ?>
<?php echo $frm->hidden('SocioAdicional.persona_id',array('value' => $this->data['SocioAdicional']['persona_id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/pfyj/socio_adicionales/index/'.$this->data['SocioAdicional']['persona_id']))?>
<?php //   debug($persona)?>
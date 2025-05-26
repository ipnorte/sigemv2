<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<?php echo $this->requestAction('/pfyj/socios/view/'.$socio['Socio']['id'])?>
<h3>MODIFICAR LA CATEGORIA DEL SOCIO</h3>
<?php echo $form->create(null,array('name'=>'formModiCatSocio','id'=>'formModiCatSocio','action' => 'modificar_categoria/'. $socio['Socio']['id']));?>
<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td>CATEGORIA</td>
			<td>
					<?php echo $this->renderElement('global_datos/combo',array(
																						'plugin'=>'config',
																						'label' => '.',
																						'model' => 'Socio.categoria',
																						'prefijo' => 'MUTUCASO',
																						'disable' => false,
																						'empty' => false,
																						'selected' => $socio['Socio']['categoria'],
																						'logico' => true,
					))?>			
			</td>
		</tr>		 
	
	</table>

</div>
<?php echo $frm->hidden('Socio.id',array('value' => $socio['Socio']['id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/socios/index/".$socio['Socio']['persona_id'] : $fwrd) ))?>

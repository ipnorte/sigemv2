<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'TIPO ASIENTO :: MODIFICACION'))?>

<script language="Javascript" type="text/javascript">

	function ctrlForm(){
		var descri = $('TipoAsientoDescripcion').getValue();
		
		if(descri == ''){
			alert('INDICAR UNA DESCRIPCION DEL TIPO DE ASIENTO');
			$('TipoAsientoDescripcion').focus();
			return false;
		}
		
		return true;
	
	}
	
	
</script>

<?php echo $form->create(null,array('name'=>'formAddTipoAsiento','id'=>'formAddTipoAsiento','onsubmit' => "return ctrlForm()" ));?>

<!-- 	<div class="areaDatoForm"> -->
		<?php // echo $frm->input('TipoAsiento.tipoasiento',array('type' => 'select','options' => array('' => 'Seleccionar Tipo Asiento...', 'GR' => 'GENERAL','PR' => 'PROVEEDORES','CG' => 'CONCEPTO DEL GASTO'), 'disabled' => 'disabled', 'selected' => $this->data['TipoAsiento']['tipo_asiento']));?>
<!-- 	</div> -->

	<div id="tipo_asiento" class="areaDatoForm">	
		
		<table class="tbl_form">
			<tr id="descripcion">
				<td>Descripci&oacute;n</td>
				<td colspan="2"><?php echo $frm->input('TipoAsiento.descripcion', array('label'=>'', 'value' => $this->data['TipoAsiento']['descripcion'],'size'=>60,'maxlength'=>50));?></td>
			</tr>
			<tr>
				<table>
					<tr id="t_asiento">
						<th>VARIABLES</th>
						<th>CUENTA CONTABLE</th>
						<th>DEBE - HABER</th>
					</tr>
							
					<?php if($this->data['TipoAsiento']['tipo_asiento'] == 'GR'): ?>
						<tr id="total_comp">
							<td>TOTAL DEL COMPROBANTE</td>
							<td><?php echo $this->renderElement('combo_plan_cuenta',array(
													'plugin'=>'contabilidad',
													'label' => "",
													'model' => 'TipoAsiento.totalcuenta',
													'disabled' => false,
													'empty' => true,
													'selected' => $this->data['TipoAsiento']['totalcuenta']))?>
							</td>			
							<td><?php echo $frm->input('TipoAsiento.totaltipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['totaltipo']));?></td>
						</tr>
					<?php endif; ?>
		
					<tr id="grava_comp">
						<td>NETO GRAVADO</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.gravacuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['TipoAsiento']['gravacuenta']))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.gravatipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['gravatipo']));?></td>
					</tr>
					<tr id="iva_comp">
						<td>I.V.A.</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.ivacuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['TipoAsiento']['ivacuenta']))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.ivatipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['ivatipo']));?></td>
					</tr>
					<tr id="ngrav_comp">
						<td>NETO NO GRAVADO</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.ngravcuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['TipoAsiento']['ngravcuenta']))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.ngravtipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['ngravtipo']));?></td>
					</tr>
					<tr id="perce_comp">
						<td>PERCEPCIONES</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.percecuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['TipoAsiento']['percecuenta']))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.percetipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['percetipo']));?></td>
					</tr>
					<tr id="reten_comp">
						<td>RETENCIONES</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.retencuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['TipoAsiento']['retencuenta']))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.retentipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['retentipo']));?></td>
					</tr>
					<tr id="imint_comp">
						<td>IMP.INTERNOS</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.imintcuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['TipoAsiento']['imintcuenta']))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.iminttipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['iminttipo']));?></td>
					</tr>
					<tr id="inbru_comp">
						<td>ING.BRUTOS</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.inbrucuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['TipoAsiento']['inbrucuenta']))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.inbrutipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['inbrutipo']));?></td>
					</tr>
					<tr id="otros_comp">
						<td>OTROS IMPUESTOS</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.otroscuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['TipoAsiento']['otroscuenta']))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.otrostipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['TipoAsiento']['otrostipo']));?></td>
					</tr>
				</table>
			</tr>
		</table>
	</div>
		
	<div style="clear: both;"></div>
<?php echo $frm->hidden('TipoAsiento.id', array('value' => $this->data['TipoAsiento']['id'])) ?>
<?php echo $frm->hidden('TipoAsiento.totalid', array('value' => $this->data['TipoAsiento']['totalid'])) ?>
<?php echo $frm->hidden('TipoAsiento.gravaid', array('value' => $this->data['TipoAsiento']['gravaid'])) ?>
<?php echo $frm->hidden('TipoAsiento.ivaid', array('value' => $this->data['TipoAsiento']['ivaid'])) ?>
<?php echo $frm->hidden('TipoAsiento.ngravid', array('value' => $this->data['TipoAsiento']['ngravid'])) ?>
<?php echo $frm->hidden('TipoAsiento.perceid', array('value' => $this->data['TipoAsiento']['perceid'])) ?>
<?php echo $frm->hidden('TipoAsiento.retenid', array('value' => $this->data['TipoAsiento']['retenid'])) ?>
<?php echo $frm->hidden('TipoAsiento.imintid', array('value' => $this->data['TipoAsiento']['imintid'])) ?>
<?php echo $frm->hidden('TipoAsiento.inbruid', array('value' => $this->data['TipoAsiento']['inbruid'])) ?>
<?php echo $frm->hidden('TipoAsiento.otrosid', array('value' => $this->data['TipoAsiento']['otrosid'])) ?>
<?php echo $frm->hidden('TipoAsiento.tipo_asiento', array('value' => $this->data['TipoAsiento']['tipo_asiento'])) ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'MODIFICAR', 'URL' => '/Proveedores/tipo_asientos/index'))?>

<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'TIPO ASIENTO :: MODIFICACION'))?>

<script language="Javascript" type="text/javascript">

	function ctrlForm(){
		var descri = $('ClienteTipoAsientoDescripcion').getValue();
		
		if(descri == ''){
			alert('INDICAR UNA DESCRIPCION DEL TIPO DE ASIENTO');
			$('ClienteTipoAsientoDescripcion').focus();
			return false;
		}
		
		return true;
	
	}
	
	
</script>

<?php echo $form->create(null,array('name'=>'formAddClienteTipoAsiento','id'=>'formAddClienteTipoAsiento','onsubmit' => "return ctrlForm()" ));?>

	<div id="tipo_asiento" class="areaDatoForm">	
		
		<table class="tbl_form">
			<tr id="descripcion">
				<td>Descripci&oacute;n</td>
				<td colspan="2"><?php echo $frm->input('ClienteTipoAsiento.descripcion', array('label'=>'', 'value' => $this->data['ClienteTipoAsiento']['descripcion'],'size'=>60,'maxlength'=>50));?></td>
			</tr>
			<tr>
				<table>
					<tr id="t_asiento">
						<th>VARIABLES</th>
						<th>CUENTA CONTABLE</th>
						<th>DEBE - HABER</th>
					</tr>
		
					<tr id="ngrav_comp">
						<td>TOTAL SERVICIO</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'ClienteTipoAsiento.ngravcuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => $this->data['ClienteTipoAsiento']['ngravcuenta']))?>
						</td>			
						<td><?php echo $frm->input('ClienteTipoAsiento.ngravtipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['ClienteTipoAsiento']['ngravtipo']));?></td>
					</tr>
							
					<?php if($this->data['ClienteTipoAsiento']['tipo_asiento'] == 'GR'): ?>
						<tr id="total_comp">
							<td>TOTAL DEL COMPROBANTE</td>
							<td><?php echo $this->renderElement('combo_plan_cuenta',array(
													'plugin'=>'contabilidad',
													'label' => "",
													'model' => 'ClienteTipoAsiento.totalcuenta',
													'disabled' => false,
													'empty' => true,
													'selected' => $this->data['ClienteTipoAsiento']['totalcuenta']))?>
							</td>			
							<td><?php echo $frm->input('ClienteTipoAsiento.totaltipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'', 'selected' => $this->data['ClienteTipoAsiento']['totaltipo']));?></td>
						</tr>
					<?php endif; ?>
				</table>
			</tr>
		</table>
	</div>
		
	<div style="clear: both;"></div>
<?php echo $frm->hidden('ClienteTipoAsiento.id', array('value' => $this->data['TipoAsiento']['id'])) ?>
<?php echo $frm->hidden('ClienteTipoAsiento.totalid', array('value' => $this->data['TipoAsiento']['totalid'])) ?>
<?php echo $frm->hidden('ClienteTipoAsiento.ngravid', array('value' => $this->data['TipoAsiento']['ngravid'])) ?>
<?php echo $frm->hidden('ClienteTipoAsiento.tipo_asiento', array('value' => 'GR')) ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'MODIFICAR', 'URL' => '/Clientes/cliente_tipo_asientos/index'))?>

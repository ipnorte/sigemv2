<?php echo $this->renderElement('mutual_tipo_asientos/menu')?>


<h3>TIPOS DE ASIENTOS :: MODIFICAR TIPO</h3>
<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {
		
	});

	function ctrlForm(){
		var descri = $('MutualTipoAsientoConcepto').getValue();
		if(descri == ''){
			alert('INDICAR UNA DESCRIPCION DEL TIPO DE ASIENTO');
			$('MutualTipoAsientoConcepto').focus();
			return false;
		}
		var v1 = $("MutualTipoAsientoNgravcuenta").getValue();
		var v2 = $("MutualTipoAsientoTotalcuenta").getValue();

		if(v1 == 0){
			alert('INDICAR LA CUENTA PARA TOTAL FACTURADO');
			$('MutualTipoAsientoNgravcuenta').focus();
			return false;			
		}	
		if(v2 == 0){
			alert('INDICAR LA CUENTA PARA EL PRODUCTO');
			$('MutualTipoAsientoTotalcuenta').focus();
			return false;			
		}

		var msg = "";

		v1 = getTextoSelect("MutualTipoAsientoNgravcuenta");
		v2 = getTextoSelect("MutualTipoAsientoTotalcuenta");
		v3 = getTextoSelect("MutualTipoAsientoNgravtipo");
		v4 = getTextoSelect("MutualTipoAsientoTotaltipo");

		msg = "** MODIFICAR TIPO ASIENTO **\n";
		msg = msg + "TOTAL FACTURADO ---> " + v1 + " [" + v3 + "]\n";
		msg = msg + "PRODUCTO ---> " + v2 + " [" + v4 + "]\n";
					
		return confirm(msg);
	
	}
	
	

	
</script>

<?php echo $form->create(null,array('name'=>'formAddMutualTipoAsiento','id'=>'formAddMutualTipoAsiento','onsubmit' => "return ctrlForm()", 'action' => 'edit/' .$tipo['MutualTipoAsiento']['id'] ));?>

	<div id="tipo_asiento" class="areaDatoForm">	
		
		<table class="tbl_form">
			<tr id="descripcion">
				<td>Descripci&oacute;n</td>
				<td colspan="2"><?php echo $frm->input('MutualTipoAsiento.concepto', array('label'=>'','size'=>60,'maxlength'=>50, 'value' => $tipo['MutualTipoAsiento']['concepto']));?></td>
			</tr>
			<tr>
				<table>
					<tr id="t_asiento">
						<th>VARIABLES</th>
						<th>CUENTA CONTABLE</th>
						<th>DEBE - HABER</th>
					</tr>
							
					<tr id="ngrav_comp">
						<td>TOTAL FACTURADO</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'MutualTipoAsiento.ngravcuenta',
												'disabled' => false,
												'empty' => false,
												'selected' => $tipo['MutualTipoAsiento']['renglones'][0]['co_plan_cuenta_id']))?>
						</td>			
						<td><?php echo $frm->input('MutualTipoAsiento.ngravtipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'','selected' => $tipo['MutualTipoAsiento']['renglones'][0]['debe_haber']));?></td>
					</tr>

					<tr id="total_comp">
						<td>PRODUCTO</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'MutualTipoAsiento.totalcuenta',
												'disabled' => false,
												'empty' => false,
												'selected' => $tipo['MutualTipoAsiento']['renglones'][1]['co_plan_cuenta_id']))?>
						</td>			
						<td><?php echo $frm->input('MutualTipoAsiento.totaltipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>'','selected' => $tipo['MutualTipoAsiento']['renglones'][1]['debe_haber']));?></td>
					</tr>
		
				</table>
			</tr>
		</table>
	</div>
		
	<div style="clear: both;"></div>
	<?php echo $frm->hidden('MutualTipoAsiento.id',array('value' => $tipo['MutualTipoAsiento']['id']));?>
	<?php echo $frm->hidden('MutualTipoAsiento.tipo_asiento',array('value' => $tipo['MutualTipoAsiento']['tipo_asiento']));?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_tipo_asientos/'))?>
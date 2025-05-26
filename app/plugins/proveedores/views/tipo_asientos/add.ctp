<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'TIPO ASIENTO :: NUEVO'))?>
<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {
		
//		ocultarTipoAsiento();
	
	});

	function ctrlForm(){
		var descri = $('TipoAsientoDescripcion').getValue();
		
		if(descri == ''){
			alert('INDICAR UNA DESCRIPCION DEL TIPO DE ASIENTO');
			$('TipoAsientoDescripcion').focus();
			return false;
		}
		
		return true;
	
	}
	
	
	function ocultarTipoAsiento(){

		$("t_asiento").hide();
		$("tipo_asiento").hide();
		$("descripcion").hide();
		$("total_comp").hide();
		$("grava_comp").hide();
		$("iva_comp").hide();
		$("ngrav_comp").hide();
		$("perce_comp").hide();
		$("reten_comp").hide();
		$("imint_comp").hide();
		$("inbru_comp").hide();
		$("otros_comp").hide();
		
		
	}	

	
</script>

<script language="Javascript" type="text/javascript">

	function seleccionTipoAsiento(){
//		var seleccion = $('TipoAsientoTipoAsiento').getValue();

		ocultarTipoAsiento();

		if(seleccion != ''){
			$("t_asiento").show();
			$("tipo_asiento").show();
			$("descripcion").show();
//			if(seleccion == 'GR'){
				$("total_comp").show();
//			}
			$("grava_comp").show();
			$("iva_comp").show();
			$("ngrav_comp").show();
			$("perce_comp").show();
			$("reten_comp").show();
			$("imint_comp").show();
			$("inbru_comp").show();
			$("otros_comp").show();
		}
		
	}
</script>

<?php echo $form->create(null,array('name'=>'formAddTipoAsiento','id'=>'formAddTipoAsiento','onsubmit' => "return ctrlForm()" ));?>

<!-- 	<div class="areaDatoForm"> -->
		<?php // echo $frm->input('TipoAsiento.tipo_asiento',array('type' => 'select','options' => array('' => 'Seleccionar Tipo Asiento...', 'GR' => 'GENERAL','PR' => 'PROVEEDORES','CG' => 'CONCEPTO DEL GASTO'), 'onchange' => 'seleccionTipoAsiento()', 'selected' => ''));?>
<!-- 	</div> -->

	<div id="tipo_asiento" class="areaDatoForm">	
		
		<table class="tbl_form">
			<tr id="descripcion">
				<td>Descripci&oacute;n</td>
				<td colspan="2"><?php echo $frm->input('TipoAsiento.descripcion', array('label'=>'','size'=>60,'maxlength'=>50));?></td>
			</tr>
			<tr>
				<table>
					<tr id="t_asiento">
						<th>VARIABLES</th>
						<th>CUENTA CONTABLE</th>
						<th>DEBE - HABER</th>
					</tr>
							
					<tr id="total_comp">
						<td>TOTAL DEL COMPROBANTE</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.totalcuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.totaltipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
		
					<tr id="grava_comp">
						<td>NETO GRAVADO</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.gravacuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.gravatipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
					<tr id="iva_comp">
						<td>I.V.A.</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.ivacuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.ivatipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
					<tr id="ngrav_comp">
						<td>NETO NO GRAVADO</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.ngravcuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.ngravtipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
					<tr id="perce_comp">
						<td>PERCEPCIONES</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.percecuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.percetipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
					<tr id="reten_comp">
						<td>RETENCIONES</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.retencuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.retentipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
					<tr id="imint_comp">
						<td>IMP.INTERNOS</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.imintcuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.iminttipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
					<tr id="inbru_comp">
						<td>ING.BRUTOS</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.inbrucuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.inbrutipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
					<tr id="otros_comp">
						<td>OTROS IMPUESTOS</td>
						<td><?php echo $this->renderElement('combo_plan_cuenta',array(
												'plugin'=>'contabilidad',
												'label' => "",
												'model' => 'TipoAsiento.otroscuenta',
												'disabled' => false,
												'empty' => true,
												'selected' => 0))?>
						</td>			
						<td><?php echo $frm->input('TipoAsiento.otrostipo',array('type' => 'select','options' => array('D' => 'DEBE','H' => 'HABER'),'label'=>''));?></td>
					</tr>
				</table>
			</tr>
		</table>
	</div>
		
	<div style="clear: both;"></div>
	<?php echo $frm->hidden('TipoAsiento.tipo_asiento',array('value' => 'GR'));?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/proveedores/tipo_asientos/'))?>
		
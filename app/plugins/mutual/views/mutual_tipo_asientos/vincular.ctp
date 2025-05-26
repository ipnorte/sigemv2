<?php echo $this->renderElement('mutual_tipo_asientos/menu')?>
<h3>VINCULACION DE TIPOS DE ASIENTOS</h3>
<?php if($showFormEdit == 0):?>
<?php if(!empty($vinculos)):?>
<table>
	<?php foreach($vinculos as $key => $vinculo):?>
		<tr>
			<th colspan="9" style="text-align: left;"><h3 style="color: #FFFFFF;"><?php echo $key?></h3></th>
		</tr>
		<tr>
			<th></th>
			<th>PRODUCTO</th>
			<th>CONCEPTO CUOTA</th>
			<th>CUENTA CONTABLE</th>
			<th>TIPO ASIENTO</th>
			<th>INSTANCIA</th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
			<?php foreach($vinculo as $values):?>
			<?php $prod = null;?>
			<?php foreach($values['values'] as $value):?>
				<tr>
					<?php if($prod != $value['concepto_producto']):?>
						<?php $prod = $value['concepto_producto'];?>
						<td style="border-top: 1px solid gray;"></td>
						<td style="border-top: 1px solid gray;"><strong><?php echo $value['concepto_producto']?></strong></td>
						<td style="border-top: 1px solid gray;"><?php echo $value['concepto_cuota']?></td>
						<td style="border-top: 1px solid gray;color:red;font-weight: bold;"><?php echo $value['cuenta']?></td>
						<td style="border-top: 1px solid gray;color: green;font-weight: bold;"><?php echo $value['tipo_asiento']?></td>
						<td style="border-top: 1px solid gray;text-align: center;"><?php echo $value['instancia']?></td>
						<td style="border-top: 1px solid gray;"><?php echo ($value['co_plan_cuenta_id'] == 0 && $value['mutual_tipo_asiento_id'] == 0 ? $html->image("controles/alert_red.gif") : "")?></td>
						<td style="border-top: 1px solid gray;"><?php echo $controles->botonGenerico('/mutual/mutual_tipo_asientos/vincular/edit/'.$value['id'],'controles/folder.png')?></td>
						<td style="border-top: 1px solid gray;"><?php echo $controles->botonGenerico('/mutual/mutual_tipo_asientos/vincular/drop/'.$value['id'],'controles/user-trash.png',null,null,"BORRAR VINCULACION?")?></td>
					<?php else:?>
						<td></td>
						<td></td>
						<td><?php echo $value['concepto_cuota']?></td>
						<td style="color:red;font-weight: bold;"><?php echo $value['cuenta']?></td>
						<td style="color:green;font-weight: bold;"><?php echo $value['tipo_asiento']?></td>
						<td style="text-align: center;"><?php echo $value['instancia']?></td>
						<td><?php echo ($value['co_plan_cuenta_id'] == 0 && $value['mutual_tipo_asiento_id'] == 0 ? $html->image("controles/alert_red.gif") : "")?></td>
						<td><?php echo $controles->botonGenerico('/mutual/mutual_tipo_asientos/vincular/edit/'.$value['id'],'controles/folder.png')?></td>
						<td><?php echo $controles->botonGenerico('/mutual/mutual_tipo_asientos/vincular/drop/'.$value['id'],'controles/user-trash.png',null,null,"BORRAR VINCULACION?")?></td>
					<?php endif;?>
				</tr>
			<?php endforeach;?>		
		<?php endforeach;?>
	<?php endforeach;?>
</table>
<?php endif;?>
<?php else:?>
	<script language="Javascript" type="text/javascript">
	function ctrlForm(){
		
		var tipoOrden = "<?php echo $vinculo['MutualCuentaAsiento']['tipo_orden_dto']?>";
		var tipoProdu = "<?php echo (!empty($vinculo['MutualCuentaAsiento']['concepto_producto']) ? $vinculo['MutualCuentaAsiento']['concepto_producto'] : $vinculo['MutualCuentaAsiento']['tipo_producto'])?>";
		var tipoCuota = "<?php echo (!empty($vinculo['MutualCuentaAsiento']['concepto_cuota']) ? $vinculo['MutualCuentaAsiento']['concepto_cuota'] : $vinculo['MutualCuentaAsiento']['tipo_cuota'])?>";
		var ctaId = $('MutualCuentaAsientoCoPlanCuentaId').getValue();
		var tipoAsid = $('MutualCuentaAsientoMutualTipoAsientoId').getValue();
		var instancia = $('MutualCuentaAsientoInstancia').getValue();
		
		var ctaStr = getTextoSelect('MutualCuentaAsientoCoPlanCuentaId');
		var tasiStr = getTextoSelect('MutualCuentaAsientoMutualTipoAsientoId');	
		var instStr = getTextoSelect('MutualCuentaAsientoInstancia');

		
		if(ctaId == 0 && tipoAsid == ""){
			alert('DEBE SELECCIONAR LA CUENTA CONTABLE O EL TIPO DE ASIENTO!');
			return false;
		}
		if(ctaId != 0 && tipoAsid != ""){
			alert('DEBE SELECCIONAR O UNA CUENTA CONTABLE O EL TIPO DE ASIENTO, PERO NO AMBOS!');
			return false;
		}			

		var msg = "*** NUEVA VINCULACION ***\n";
		msg = msg + "TIPO: " + tipoOrden + "\n";
		if(tipoProdu != "") msg = msg + "PRODUCTO: " + tipoProdu + "\n";
		if(tipoCuota != "") msg = msg + "CONCEPTO: " + tipoCuota + "\n";

		if(ctaId != 0){
			msg = msg + "CUENTA CONTABLE: " + ctaStr + "\n";
			document.getElementById('MutualCuentaAsientoMutualTipoAsientoId').value = 0;
			document.getElementById('MutualCuentaAsientoInstancia').value = "COBRO";
			instStr = getTextoSelect('MutualCuentaAsientoInstancia');
			msg = msg + "INSTANCIA: " + instStr + "\n";
		}
		if(tipoAsid != ""){
			msg = msg + "TIPO ASIENTO: " + tasiStr + "\n";
			document.getElementById('MutualCuentaAsientoCoPlanCuentaId').value = 0;
			if(instancia == 'COBRO'){
				alert("SI ELIGE UN TIPO DE ASIENTO LA INSTANCIA DEBE SER DISTINTA A LA DE COBRO!");
				return false;
			}
			msg = msg + "INSTANCIA: " + instStr + "\n";
		}		
		return confirm(msg);
	}	
	</script>
	<?php echo $form->create(null,array('name'=>'formAddMutualTipoAsientoVinc','id'=>'formAddMutualTipoAsientoVinc','onsubmit' => "return ctrlForm()", 'action' => 'vincular/edit/' . $vinculo['MutualCuentaAsiento']['id'] ));?>
	<div class="areaDatoForm">
		<table class="tbl_form">
			<tr>
				<td>TIPO ORDEN</td><td><strong><?php echo $vinculo['MutualCuentaAsiento']['tipo_orden_dto']?></strong></td>
			</tr>
			<tr>
				<td>PRODUCTO</td><td><strong><?php echo (!empty($vinculo['MutualCuentaAsiento']['concepto_producto']) ? $vinculo['MutualCuentaAsiento']['concepto_producto'] : $vinculo['MutualCuentaAsiento']['tipo_producto'])?></strong></td>
			</tr>
			<tr>
				<td>CONCEPTO CUOTA</td><td><strong><?php echo (!empty($vinculo['MutualCuentaAsiento']['concepto_cuota']) ? $vinculo['MutualCuentaAsiento']['concepto_cuota'] : $vinculo['MutualCuentaAsiento']['tipo_cuota'])?></strong></td>
			</tr>
			<tr>
				<td>CUENTA CONTABLE</td>
				<td><?php echo $this->renderElement('combo_plan_cuenta',array(
										'plugin'=>'contabilidad',
										'label' => "",
										'model' => 'MutualCuentaAsiento.co_plan_cuenta_id',
										'disabled' => false,
										'empty' => true,
										'selected' => $vinculo['MutualCuentaAsiento']['co_plan_cuenta_id'],
										'seleccionar_todas' => false,
				))
				?>
				</td>
			</tr>
			<tr>
				<td>TIPO ASIENTO</td>
				<td><?php echo $frm->input('MutualCuentaAsiento.mutual_tipo_asiento_id',array('type' => 'select', 'options' => $tiposAsientos, 'empty' => true, 'selected' => $vinculo['MutualCuentaAsiento']['mutual_tipo_asiento_id']))?></td>
			</tr>										
			<tr>
				<td>INSTANCIA</td>
				<td><?php echo $frm->input('MutualCuentaAsiento.instancia',array('type' => 'select', 'options' => array('APROB' => 'APROBACION', 'LIQUI' => 'LIQUIDACION', 'COBRO' => 'COBRO'), 'empty' => false, 'selected' => $vinculo['MutualCuentaAsiento']['instancia']))?></td>
			</tr>
		</table>
	
	</div>
	<?php echo $frm->hidden('MutualCuentaAsiento.id',array('value' => $vinculo['MutualCuentaAsiento']['id']));?>
	<?php echo $frm->btnGuardarCancelar(array('URL' => '/mutual/mutual_tipo_asientos/vincular'))?>	
	<?php //   debug($vinculo)?>
<?php endif;?>

<?php //   debug($vinculos)?>

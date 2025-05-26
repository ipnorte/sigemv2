<?php
// debug($this->data)
?>

<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'CLIENTE'))?>
<?php
$checkedA = '';
$checkedB = '';
if($this->data['Cliente']['tipo_saldo']==0) $checkedA = 'checked';
else $checkedB = 'checked';

$disabledSaldo = '';
if($this->data['Cliente']['estado']!='A') $disabledSaldo = 'disabled';

?>
<h3>DATOS DEL CLIENTE</h3>
<?php echo $form->create(null,array('name'=>'formEditCliente','id'=>'formEditCliente','onsubmit' => ""));?>
<div class="areaDatoForm">
	<table class="tbl_form">
		<tr>
			<td>CUIT</td>
			<td><?php echo $frm->input('Cliente.cuit',array('label'=>'','size'=>'15','maxlength'=>'11','disabled'=>'disabled'));?></td>
		</tr>
		<tr>
			<td>RAZON SOCIAL</td>	
			<td><?php echo $frm->input('Cliente.razon_social',array('label'=>'','size'=>60,'maxlength'=>100)); ?></td>
		</tr>
		<tr>
			<td>RAZON SOCIAL RESUMIDA</td>	
			<td><?php echo $frm->input('Cliente.razon_social_resumida',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
		</tr>
		<tr>
			<td>CALLE</td>	
			<td><?php echo $frm->input('Cliente.calle',array('label'=>'','size'=>40,'maxlength'=>100)); ?></td>
			<td>NUMERO</td>	
			<td><?php echo $frm->number('Cliente.numero_calle',array('label'=>'')); ?></td>
			<td>PISO</td>	
			<td><?php echo $frm->input('Cliente.piso',array('label'=>'','size'=>3,'maxlength'=>3)); ?></td>
			<td>DPTO</td>	
			<td><?php echo $frm->input('Cliente.dpto',array('label'=>'','size'=>3,'maxlength'=>3)); ?></td>
		</tr>
		<tr>
			<td>BARRIO</td>	
			<td><?php echo $frm->input('Cliente.barrio',array('label'=>'','size'=>50,'maxlength'=>100)); ?></td>
			<td>LOCALIDAD</td>	
			<td><?php echo $frm->input('Cliente.localidad',array('label'=>'','size'=>50,'maxlength'=>100)); ?></td>
			<td>C.P.</td>	
			<td><?php echo $frm->input('Cliente.codigo_postal',array('label'=>'','size'=>11,'maxlength'=>100)); ?></td>
		</tr>
		<tr>
			<td>TELEFONO FIJO</td>	
			<td><?php echo $frm->input('Cliente.telefono_fijo',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
			<td>TELEFONO MOVIL</td>	
			<td><?php echo $frm->input('Cliente.telefono_movil',array('label'=>'','size'=>20,'maxlength'=>50)); ?></td>
			<td>FAX</td>
			<td><?php echo $frm->input('Cliente.fax', array('label' => '', 'size' => 20, 'maxlength' => 50)); ?></td>
		</tr>
		<tr>
			<td>EMAIL</td>	
			<td><?php echo $frm->input('Cliente.email',array('label'=>'','size'=>30,'maxlength'=>50)); ?></td>
		</tr>
		<tr>
			<td>CONDICION ANTE I.V.A.</td>	
			<td>
			<?php echo $this->renderElement('global_datos/combo',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'Cliente.condicion_iva',
																			'prefijo' => 'PERSXXTI',
																			'disable' => false,
																			'empty' => false,
																			'selected' => (!empty($this->data['Cliente']['condicion_iva']) ? $this->data['Cliente']['condicion_iva'] : '0'),
																			'logico' => true,
			))?>			
			</td>
		</tr>
		<tr>
			<td>VINCULO COMERCIO</td>
			<td>
			<?php echo $this->renderElement('proveedor/combo_general',array(
																			'plugin'=>'proveedores',
																			'metodo' => "proveedores_list",
																			'model' => 'Cliente.proveedor_id',
																			'empty' => true,
																			'selected' => $this->data['Cliente']['proveedor_id']))?>
			</td>
			<!-- <td>TIPO ASIENTO</td>
			<td><?php echo $this->renderElement('tipo_asiento/combo_tipo_asiento',array(
									'plugin'=>'clientes',
									'label' => "",
									'model' => 'Cliente.cliente_tipo_asiento_id',
									'disabled' => false,
									'empty' => false,
									'selected' => $this->data['Proveedor']['proveedor_tipo_asiento_id']))?>
			</td> -->			
		</tr>
		<tr>
			<td>IMP.CTA.CONTABLE</td>
			<td><?php echo $this->renderElement('combo_plan_cuenta',array(
									'plugin'=>'contabilidad',
									'label' => "",
									'model' => 'Cliente.co_plan_cuenta_id',
									'disabled' => false,
									'empty' => false,
									'selected' => $this->data['Cliente']['co_plan_cuenta_id']))?>
			</td>			
		</tr>													
		<tr>
			<td>RESPONSABLE</td>	
			<td><?php echo $frm->input('Cliente.responsable',array('label'=>'','size'=>60,'maxlength'=>100)); ?></td>
		</tr>
		<tr>
			<td>CONTACTO</td>	
			<td><?php echo $frm->input('Cliente.contacto',array('label'=>'','size'=>60,'maxlength'=>100)); ?></td>
		</tr>
		<tr>
			<td>SALDO INICIALES AL:</td>
			<td><?php echo $frm->input('Cliente.fecha_saldo',array('label' => '', 'dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1, 'disabled' => $disabledSaldo))?></td>
		</tr>
		<tr>
			<td>IMPORTE:</td>
			<td><div class="input text"><label for="ClienteImporteSaldo"></label><input name="data[Cliente][importe_saldo]" type="text" value="<?php echo $this->data['Cliente']['importe_saldo'] ?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" id="ClienteImporteSaldo" <?php echo $disabledSaldo?>/></div></td>
			<!-- <td><?php echo $frm->money('Cliente.importe_saldo','',$this->data['Cliente']['importe_saldo']) ?></td> -->
		</tr>
		<tr>
			<td>SALDO:</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="radio" name="data[Cliente][tipo_saldo]" id="ClienteAccion_a" value="0" <?php echo $checkedA?> <?php echo $disabledSaldo?>/>DEUDOR</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="radio" name="data[Cliente][tipo_saldo]" id="ClienteAccion_b" value="1" <?php echo $checkedB?> <?php echo $disabledSaldo?>/>ACREEDOR</td>
		</tr>
		<tr>
			<td>ACTIVO</td>	
			<td><?php echo $frm->input('Cliente.activo',array('label'=>'')) ?></td>
		</tr>															
	
	</table>
</div>
<?php echo $frm->hidden('Cliente.cliente_factura_id', array('value' => $this->data['Cliente']['cliente_factura_id'])); ?>
<?php echo $frm->hidden('Cliente.estado', array('value' => $this->data['Cliente']['estado'])); ?>
<?php echo $frm->hidden('Cliente.id'); ?>
<?php echo $frm->btnGuardarCancelar(array('URL' => '/clientes/clientes/'))?> 
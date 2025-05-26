<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>COMISIONES POR COBRANZA :: NUEVO VALOR</h3>

<div class="areaDatoForm">
	<script type="text/javascript">

		function validate(){
			var porc = $('ProveedorComisionComision').getValue();
			if(!isNaN(parseFloat(porc)) && isFinite(porc)){

				var msg = "*** ALTA NUEVA COMISION ***\n";
				msg = msg + "ORGANISMO: " + getTextoSelect("ProveedorComisionCodigoOrganismo") + "\n";
				msg = msg + "COMISION: " + porc + "%";
				 return confirm(msg);
			}else{
				alert("DEBE INDICAR EL PORCENTAJE!");
				$('ProveedorComisionComision').focus();
				return false;
			}	
		}

	</script>
	<?php echo $form->create(null,array('action' => 'comision_cobranza/' . $proveedor['Proveedor']['id'].'/ADD','name'=>'formEditProveedor','id'=>'formEditProveedor','onsubmit' => "return validate()"));?>
	
	<table class="tbl_form">
	
		<tr>
			<td>ORGANISMO</td>
			<td colspan="3">
			<?php //echo $this->renderElement('global_datos/combo_global',array(
// 																			'plugin'=>'config',
// 																			'metodo' => "get_organismos",
// 																			'model' => 'ProveedorComision.codigo_organismo',
// 																			'empty' => false,
// 																			'selected' => ""	
			//))?>
			
				<?php echo $this->renderElement('global_datos/grilla_checks',array(
																				'plugin'=>'config',
																				'label' => " ",
																				'model' => 'ProveedorComision.codigo_organismo',
																				'prefijo' => 'MUTUCORG',
																				'disabled' => false,
																				'header' => true,
																				'metodo' => "get_organismos",
																				'selected' => (isset($this->data['Liquidacion']['codigo_organismo']) ? $this->data['Liquidacion']['codigo_organismo'] : array())	
				))?>									
			</td>
		</tr>	
		<tr>
			<td>PORCENTAJE</td><td><?php echo $frm->money("ProveedorComision.comision")?></td>
		</tr>		
	</table>
	<?php echo $frm->hidden('ProveedorComision.id',array('value' => 0)); ?>
	<?php echo $frm->hidden('ProveedorComision.tipo',array('value' => 'COB')); ?>
	<?php echo $frm->hidden('ProveedorComision.proveedor_id',array('value' => $proveedor['Proveedor']['id'])); ?>
	<?php echo $frm->btnGuardarCancelar(array('URL' => '/proveedores/proveedores/comision_cobranza/'.$proveedor['Proveedor']['id']))?>

</div>
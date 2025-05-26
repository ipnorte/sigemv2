<?php echo $this->renderElement('head',array('title' => 'GENERAR ORDEN DE CANCELACION DE TERCEROS','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link'=>true,'plugin' => 'pfyj'))?>
<div class="areaDatoForm">
<script language="Javascript" type="text/javascript">
function validateForm(){

	//controlo el total
	var totalProveedor = parseFloat(document.getElementById('CancelacionOrdenImporteProveedor').value);
	if(totalProveedor == 0){
		alert("El importe de DEUDA QUE CANCELA SEGUN PROVEEDOR no puede ser cero (0).");
		$('CancelacionOrdenImporteProveedor').focus();
		return false;
	}
	if($('CancelacionOrdenConcepto').getValue() == ''){
		alert("Debe indicar el concepto.");
		$('CancelacionOrdenConcepto').focus();
		return false;
	}		
	return true;
}
</script>
<?php echo $frm->create(null,array('action' => 'terceros_generar/'.$persona['Persona']['id'],'onsubmit' => "return validateForm();"))?>
<table class="tbl_form">

	<tr>
  		<td align="right">A LA ORDEN DE</td>
  		<td>
			<?php echo $this->renderElement('proveedor/combo_general',array(
																			'plugin'=>'proveedores',
																			'metodo' => "proveedores_list/1",
																			'model' => 'CancelacionOrden.proveedor_id',
																			'empty' => false,
																			'selected' => (isset($this->data['CancelacionOrden']['proveedor_id']) ? $this->data['CancelacionOrden']['proveedor_id'] : "")
			))?>  		
  		
  		</td>
	</tr>

	<tr>	
  		<td align="right">DEUDA QUE CANCELA SEGUN PROVEEDOR</td>
  		<td><?php echo $frm->money('CancelacionOrden.importe_proveedor','','0.00')?></td>
  	</tr>
  	
  	
  	<tr>
  		<td align="right">FECHA DE VENCIMIENTO</td><td><?php echo $frm->calendar('CancelacionOrden.fecha_vto','',date('Y-m-d'),date('Y') - 1,date('Y') + 1)?></td>
	</tr>
	<tr>	
  		<td align="right">EN CONCEPTO DE</td>
  		<td><?php echo $frm->input('CancelacionOrden.concepto',array('type' => 'text','size' => 70, 'maxlength' => 70))?></td>
  	</tr>
	
	
  	<tr>
  		<td align="right">OBSERVACIONES</td><td><?php echo $frm->textarea('CancelacionOrden.observaciones',array('cols' => 60, 'rows' => 10))?></td>
	</tr>


</table>


<?php echo $frm->hidden('CancelacionOrden.hoy',array('value' => $hoy))?>
<?php echo $frm->hidden('CancelacionOrden.socio_id',array('value' => $persona['Socio']['id']))?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR ORDEN DE CANCELACION','URL' => ( empty($fwrd) ? "/mutual/cancelacion_ordenes/by_socio/" . $persona['Socio']['id'] : $fwrd) ))?>

</div>

<?php //   debug($persona)?>
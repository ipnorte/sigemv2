
<?php echo $this->renderElement('head',array('title' => 'GENERAR ORDEN DE CANCELACION :: SELECCIONAR CUOTAS','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link'=>true,'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('orden_descuento/resumen',array('orden'=>$orden,'plugin' => 'mutual'))?>




<h3>DETALLE DE CUOTAS DE LA ORDEN DE DESCUENTO (No incluidas en otra Orden de Cancelación EMITIDA) </h3>

<script language="Javascript" type="text/javascript">
 

//

var rows = <?php echo count($cuotas)?>;

Event.observe(window, 'load', function() {
	
		$('btn_submit').disable();
		selUnSelAll(false,false);
		$('imputacionDiferencia').hide();
                
		$('CancelacionOrdenImporteProveedor').observe('blur',function(){
			totalProveedor = parseFloat(document.getElementById('CancelacionOrdenImporteProveedor').value) * 100;
			nota = totalProveedor - parseFloat(document.getElementById('CancelacionOrdenImporte').value) * 100;
			if(nota < 0){
				$('imputacionDiferencia').show();
				armaCmbTipoCuota('RESTA');				
			}else if(nota > 0){
				$('imputacionDiferencia').show();
				armaCmbTipoCuota('SUMA');				
			}else{
				$('imputacionDiferencia').hide();
			}
			nota = FormatCurrency(nota / 100);
			$('importeDiferencia').update(nota);
					
		});

                
		
		
});

function selUnSelAll(status,soloVencida){
	for (i=1;i<=rows;i++){
		oChkCheck = document.getElementById('CancelacionOrdenCuotaOrdenDescuentoCuotaId_' + i);
		oChkCheck.checked = false;
		if(soloVencida){
			vencida = document.getElementById('CancelacionOrdenCuotaOrdenDescuentoCuotaVencida_' + i).value;
			if(vencida == 1 && !oChkCheck.disabled)oChkCheck.checked = status;
		}else{
			if(!oChkCheck.disabled) oChkCheck.checked = status;
		}	
	}
	SelSum();
}

function chkOnclick(){
    SelSum();
}

function SelSum(){
	
	var totalSeleccionado = 0;
	var error = "";	
	for (i=1;i<=rows;i++){
		var celdas = $('TRL_' + i).immediateDescendants();
		oChkCheck = document.getElementById('CancelacionOrdenCuotaOrdenDescuentoCuotaId_' + i);
		oTxtImpoCancela = document.getElementById('CancelacionOrdenCuotaOrdenDescuentoCuotaId1_' + i);
		if (oChkCheck.checked){
//			totalSeleccionado = totalSeleccionado + parseInt(oChkCheck.value);
			strValTxt = oTxtImpoCancela.value;
			numValTxt = new Number(strValTxt);
			numValTxt = numValTxt.toFixed(2);
//			impoTxt = parseInt(numValTxt * 100);
			impoTxt = numValTxt * 100;
			impoTxt = impoTxt.toFixed(0);
			chkValue = parseInt(oChkCheck.value);
			if(impoTxt > chkValue){
				alert("EL IMPORTE INDICADO " + strValTxt + " NO PUEDE SER SUPERIOR AL SALDO ACTUAL DE LA CUOTA!");
				oChkCheck.checked = false;
				break;
			}
			impoTxt = parseInt(impoTxt);
			totalSeleccionado = totalSeleccionado + impoTxt;

			celdas.each(function(td){td.addClassName("selected");});
		}else{
			celdas.each(function(td){td.removeClassName("selected");});
		}	
	}

	if(totalSeleccionado > 0)$('btn_submit').enable();
	else $('btn_submit').disable();
	
	totalSeleccionado = totalSeleccionado/100;
	document.getElementById('CancelacionOrdenImporte').value = totalSeleccionado;
	document.getElementById('CancelacionOrdenImporteProveedor').value = totalSeleccionado;

	totalSeleccionado = FormatCurrency(totalSeleccionado);

	$('total_seleccionado').update(totalSeleccionado);
}


function validateForm(){

	$('mensaje_error_js').removeClassName('form-error');
	$('mensaje_error_js').update('');
	$('mensaje_error_js').hide();

	var diaSel = $('CancelacionOrdenFechaVtoDay').getValue();
	var mesSel = $('CancelacionOrdenFechaVtoMonth').getValue();
	var anioSel = $('CancelacionOrdenFechaVtoYear').getValue();
	
	var fechaSel = diaSel + '/' + mesSel + '/' + anioSel;
        
	var hoy = $('CancelacionOrdenHoy').getValue();

	if(!validDiaPosterior(fechaSel,hoy,'')){
		return false;
	}

	//controlo el total
	var totalProveedor = parseFloat(document.getElementById('CancelacionOrdenImporteProveedor').value);

	totalProveedor = totalProveedor * 100;

	//avisar que genera una NOTA DE CREDITO PARA EL SOCIO
//	if(totalProveedor == 0){

		totalProveedor = FormatCurrency(totalProveedor/100);
		nota = totalProveedor - document.getElementById('CancelacionOrdenImporte').value;
		nota = FormatCurrency(nota);

		if(nota < 0){
			return confirm("GENERAR NOTA DE CREDITO AL SOCIO POR $" + nota + " AL MOMENTO DE PROCESAR ESTA ORDEN?");			
		}else if(nota > 0){
			$('imputacionDiferencia').show();
//			armaCmbTipoCuota('SUMA');
			return confirm("GENERAR NOTA DE DEBITO AL SOCIO POR $" + nota + " AL MOMENTO DE PROCESAR ESTA ORDEN?");
		}	
		
//		$('mensaje_error_js').addClassName('form-error');
//		$('mensaje_error_js').update('Indicar el importe de la Deuda segun el Proveedor la cual va a ser cancelada');
//		$('mensaje_error_js').show();
//
//		return false;
		
//	}	
		
	return true;
}


function armaCmbTipoCuota(signo){
	url = '<?php echo $this->base?>/config/global_datos/cmb_tipoCuota/0/CancelacionOrden.tipo_cuota_diferencia/' + signo;
	new Ajax.Updater('cmbTipoCuota',url, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner_tipo_cuota').hide();$('FormGenOrdCanc').enable();},onLoading:function(request) {Element.show('spinner_tipo_cuota');$('FormGenOrdCanc').disable();}, requestHeaders:['X-Update', 'cmbTipoCuota']});
}

</script>

<?php echo $frm->create(null,array('id'=>'FormGenOrdCanc','onsubmit' => "return validateForm();",'action' => 'sel_cuota/'.$persona['Persona']['id'].'/'.$orden['OrdenDescuento']['id']))?>

<div style="width:850px; ;height: 350px; overflow: scroll;">
	<table>
	  <tr>
		<th>PERIODO</th>
		<th>ORDEN</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR / PRODUCTO</th>
		<th>CUOTA</th>
		<th>CONCEPTO</th>
		<th>VTO</th>
		<th colspan="2">SALDO</th>
		<th>CANCELA</th>
	    <th></th>
	  </tr>
	  <?php
	  	$i=0;
	  	$ACU_VENCIDO = 0;
	  	$ACU_AVENCER = 0;
	  	$ACU_TOTAL = 0;
	  	
	  	foreach($cuotas as $cuota):
	  		$bloqueo = array();
	  		if(!empty($cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'])) $bloqueo = $cuota['OrdenDescuentoCuota']['bloqueo_liquidacion'];
	  	
                        if(isset($cuota['OrdenDescuentoCuota']['pre_imputado']) && !empty($cuota['OrdenDescuentoCuota']['pre_imputado'])){
                            $cuota['OrdenDescuentoCuota']['saldo_cuota'] -= $cuota['OrdenDescuentoCuota']['pre_imputado'];
                        }                        
                        
//	  		echo number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2,".","") . " *** " . number_format(round($cuota['OrdenDescuentoCuota']['saldo_cuota'],2) * 100,0,".","") . "<br/>";
	  	
	  		$i++;
	  		$ACU_TOTAL += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  		if($cuota['OrdenDescuentoCuota']['vencida']==1)$ACU_VENCIDO += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  		else $ACU_AVENCER += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
	  ?>
		  <tr id="TRL_<?php echo $i?>">
			<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
			<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuentoCuota']['orden_descuento_id'],'h' => 450, 'w' => 700))?></td>
			<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
			<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
			<td align="center"><strong><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></strong></td>
			<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
			<td align="center"><strong><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></strong></td>
			<td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)?></td>
			<td align="center"><?php echo $controles->vencida($cuota['OrdenDescuentoCuota']['vencida'])?></td>
			<td>
				<input type="text" <?php echo (!empty($bloqueo) && $bloqueo['id'] != 0 ? "disabled='disabled'" : "") ?> name="data[CancelacionOrdenCuota][cancelacion_orden_cuota_id1][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2,".","")?>" id="CancelacionOrdenCuotaOrdenDescuentoCuotaId1_<?php echo $i?>" class="input_number" onkeypress = "return soloNumeros(event,true)" size="12" maxlength="12"/>
			</td>
			<td>
				<?php if(!empty($bloqueo) && $bloqueo['id'] != 0):?>
					<input type="checkbox" disabled="disabled"  name="data[CancelacionOrdenCuota][cancelacion_orden_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo number_format(round($cuota['OrdenDescuentoCuota']['saldo_cuota'],2) * 100,0,".","")?>" id="CancelacionOrdenCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
					<input type="hidden" disabled="disabled" name="data[CancelacionOrdenCuota][cancelacion_orden_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="CancelacionOrdenCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
					<span style="color: red;"><?php echo "LIQ #".$bloqueo['id'] . " " . $bloqueo['liquidacion']?></span>
				<?php else:?>			
				<input type="checkbox" name="data[CancelacionOrdenCuota][cancelacion_orden_cuota_id][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo number_format(round($cuota['OrdenDescuentoCuota']['saldo_cuota'],2) * 100,0,".","")?>" id="CancelacionOrdenCuotaOrdenDescuentoCuotaId_<?php echo $i?>" onclick="chkOnclick()"/>
				<input type="hidden" name="data[CancelacionOrdenCuota][cancelacion_orden_cuota_vencida][<?php echo $cuota['OrdenDescuentoCuota']['id']?>]" value="<?php echo $cuota['OrdenDescuentoCuota']['vencida']?>" id="CancelacionOrdenCuotaOrdenDescuentoCuotaVencida_<?php echo $i?>">
				<?php endif;?>
			</td>
		  </tr>
	  <?php endforeach;?>
	</table>
</div>
<table>
  <tr>
  	<th></th>
  	<th>TOTAL</th>
  	<th>VENCIDO</th>
  	<th>A VENCER</th>
    <th>TOTAL DEUDA A CANCELAR</th>
  </tr>
  <tr>
  	<td><input type="button" value="Marcar Todo" onclick="selUnSelAll(true,false)"> | <input type="button" value="Solo lo Vencido" onclick="selUnSelAll(true,true)"> | <input type="button" value="Limpiar" onclick="selUnSelAll(false,false)"></td>
  	 <td align="right"><?php echo number_format($ACU_TOTAL,2)?></td>	
  	 <td align="right"><?php echo number_format($ACU_VENCIDO,2)?></td>
  	  <td align="right"><?php echo number_format($ACU_AVENCER,2)?></td>
    <td align="right" class="selected" ><h1><div id="total_seleccionado">0.00</div></h1></td>
  </tr>
</table>

<table>
	<tr><th colspan="2">DATOS DE LA ORDEN DE CANCELACION</th></tr>
	<tr>
		<td align="right">CANCELACION TOTAL</td><td><?php echo $frm->input("CancelacionOrden.tipo_cancelacion",array('type' => 'checkbox', 'value' => 'T'))?></td>
	</tr>	
	<tr>
  		<td align="right">A LA ORDEN DE</td><td><?php echo $this->renderElement('/proveedor/combo_proveedores_cancelacion',array('orden_descuento_id'=>$orden['OrdenDescuento']['id'],'label' => '','plugin' => 'proveedores'))?></td>
	</tr>

	<tr>	
  		<td align="right">DEUDA QUE CANCELA SEGUN PROVEEDOR</td>
  		<td>
  			<?php echo $frm->money('importe_proveedor','','0.00')?>
  			<div id="spinner_tipo_cuota" style="display: none; float: left;"><?php echo $html->image('controles/ajax-loader.gif'); ?></div>
  		</td>
  	</tr>
  	
	<tr id="imputacionDiferencia">	
  		<td align="right">IMPUTAR DIFERENCIA (<span id="importeDiferencia" style="font-weight:bold;"></span>) COMO: </td>
  		<td>
  			<div id="cmbTipoCuota"><?php echo $this->requestAction('/config/global_datos/cmb_tipoCuota/./CancelacionOrden.tipo_cuota')?></div>
  			
  		</td>
  	</tr>  	
  	
  	<tr>
  		<td align="right">FECHA DE VENCIMIENTO</td><td><?php echo $frm->calendar('fecha_vto','',date('Y-m-d'),date('Y') - 1,date('Y') + 1)?></td>
	</tr>
  	<tr>
  		<td align="right">OBSERVACIONES</td><td><?php echo $frm->textarea('observaciones',array('cols' => 60, 'rows' => 10))?></td>
	</tr>
 		
</table>

<?php echo $frm->hidden('CancelacionOrden.socio_id',array('value' => $persona['Socio']['id']))?>
<?php echo $frm->hidden('CancelacionOrden.importe')?>
<?php echo $frm->hidden('CancelacionOrden.hoy',array('value' => $hoy))?>
<?php echo $frm->hidden('CancelacionOrden.orden_descuento_id',array('value' => $orden['OrdenDescuento']['id']))?>
<?php echo $frm->hidden('CancelacionOrden.persona_idr',array('value' => $persona['Persona']['idr']))?>
<?php echo $frm->hidden('CancelacionOrden.importe_cuota',array('value' => ($orden['OrdenDescuento']['cuotas'] != 0 ? $orden['OrdenDescuento']['importe_total'] / $orden['OrdenDescuento']['cuotas'] : 0)))?>
<?php echo $frm->hidden('CancelacionOrden.saldo_orden_dto',array('value' => $orden['OrdenDescuento']['saldo']))?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR ORDEN','URL' => ( empty($fwrd) ? "/mutual/cancelacion_ordenes/generar/".$persona['Persona']['id'].'/'.$orden['OrdenDescuento']['id'] : $fwrd) ))?>
</form>
<?php // debug($cuotas)?>
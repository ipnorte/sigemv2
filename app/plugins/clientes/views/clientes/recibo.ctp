<?php 
$fechaCobro = date('Ymd');
$importeCobro = 0.00;
$cntRenglones = count($facturaPendiente);

$rows = 0;
if($bloquear==1):
	$fechaCobro = $this->data['Recibo']['fecha_cobro']['year'] . '-' . $this->data['Recibo']['fecha_cobro']['month'] . '-' . $this->data['Recibo']['fecha_cobro']['day'];
	$importeCobro = $this->data['Recibo']['importe_cabecera'];
endif;
?>
<?php echo $this->renderElement('clientes/cliente_header',array('cliente' => $clientes))?>
<h3>RECIBO DE INGRESO</h3>

<script language="Javascript" type="text/javascript">
	var rows = <?php echo count($facturaPendiente)?>;
	var items = 0;
	var fechaCobro = <?php echo $fechaCobro?>;
	var saldo_clie = <?php echo $saldo ?>;

	Event.observe(window, 'load', function() {
		
		var cabeceraCobro = $('formCabeceraCobro');
		
		<?php if($bloquear==1):?> 
			cabeceraCobro.disabled = true;
			$('formCabeceraCobro').disable();
			$('btn_submit').disable();
			selSum();
			ocultarOptionFCobro();
		<?php endif; ?>
	
	});


</script>
	
	
<script language="Javascript" type="text/javascript">
	
	function ctrlCobro(){
		var cbcImporte = $('ReciboImporteCabecera').getValue();
		var nmbImporte = new Number(cbcImporte);
		
//		if(rows == 0 && nmbImporte == 0){
		if(saldo_clie <= 0 && nmbImporte == 0){
			alert('DEBE INDICAR UN IMPORTE EN EL RECIBO');
			$('ReciboImporteCabecera').focus()
			$('ReciboImporteCabecera').select()
			return false;
		}

//		if(rows == 0 && nmbImporte > 0){
		if(saldo_clie <= 0 && nmbImporte > 0){
			if(!confirm("SE REALIZARA UN COBRO A CUENTA. \n \n ESTA SEGURO DE REALIZAR ESTA OPERACION")){return false;}
			else{
                            $('btn_submit').disable();
                            return true;
                        }
		}
                $('btn_submit').disable();
		return true;
	}
	
	function ctrlDetalle(){
		var cbcImporte = $('ReciboImporteCabecera').getValue();
		var nmbImporte = new Number(cbcImporte);
		nmbImporte = nmbImporte.toFixed(2);
		
		var dtlImporte = $('ReciboImporteDetalle').getValue();
		var nmbImpDetalle = new Number(dtlImporte);
		nmbImpDetalle = nmbImpDetalle.toFixed(2);
		
		var nmbAnticipo = nmbImporte - nmbImpDetalle;
		
//		if((nmbAnticipo > 0 && nmbImpDetalle > 0) || (nmbAnticipo > 0 && nmbImpDetalle == 0 && rows > 0)){
		if((nmbAnticipo > 0 && nmbImpDetalle > 0) || (nmbAnticipo > 0 && nmbImpDetalle == 0 && saldo_clie > 0)){
			if(!confirm('SE GENERARA UN COBRO A CUENTA DE $ ' + nmbAnticipo + '\n \n ESTA SEGURO DE REALIZAR ESTA OPERACION')){return false;}
			else{return true;}
		}
                $('btn_submit').disable();
		return true;
	}
	
	function chkOnclick(){
		selSum();

		acumulado = parseFloat($('ReciboAcumula').getValue());
		
		cobro = document.getElementById("ReciboImporteCobro").value;
		cobro = parseFloat(cobro);
		resto = cobro - acumulado;

//		if(resto == 0) $('btn_submit').enable();
//		else $('btn_submit').disable();
		if(resto == 0 && items == 1) $('btn_submit').enable();
		else $('btn_submit').disable();
		document.getElementById("ReciboImporte").value = resto;
		document.getElementById("ReciboFormaCobro").value = "";
		ocultarOptionFCobro();
		
	}	

	function selSum(){	
		var totalSeleccionado = 0;
		var importeCabecera = $('ReciboImporteCabecera').getValue();
		var importeCobro = $('ReciboImporteCobro').getValue();
		var error = "";	

		items = 0;
		
		importeCabecera = new Number(importeCabecera);
		importeCabecera = importeCabecera.toFixed(2);
		
		for (i=1;i<=rows;i++){
			var celdas = $('TRL_' + i).immediateDescendants();
			oChkCheck = document.getElementById('ReciboSaldo_' + i);
			oTxtImpoCobrar = document.getElementById('ReciboImporteACobrar_' + i);
			if (oChkCheck.checked){
				items = 1;
				strValTxt = oTxtImpoCobrar.value;
				numValTxt = new Number(strValTxt);
				numValTxt = numValTxt.toFixed(2);
				impoTxt = numValTxt * 100;
				impoTxt = impoTxt.toFixed(0);

				strChkTxt = oChkCheck.value;
				numChkTxt = new Number(strChkTxt);
				numChkTxt = numChkTxt / 100;
				numChkTxt = numChkTxt.toFixed(2);
	
				if (impoTxt == 0){
					alert("EL IMPORTE NO PUEDE SER 0 (CERO)");
					oChkCheck.checked = false;
					document.getElementById('ReciboImporteACobrar_' + i).value = numChkTxt
					break;
				}
	
				chkValue = parseInt(oChkCheck.value);
	
				if(chkValue < 0){
	
					if(impoTxt > 0){
						alert("EL IMPORTE NO PUEDE SER MAYOR A 0 (CERO)");
						oChkCheck.checked = false;
						document.getElementById('ReciboImporteACobrar_' + i).value = numChkTxt
						break;
					}
					if(impoTxt < chkValue){
						alert("EL IMPORTE INDICADO " + strValTxt + " NO PUEDE SER MENOR AL SALDO");
						oChkCheck.checked = false;
						document.getElementById('ReciboImporteACobrar_' + i).value = numChkTxt
						break;
					}
				}else{
				
					if(impoTxt > chkValue){
						alert("EL IMPORTE INDICADO " + strValTxt + " NO PUEDE SER SUPERIOR AL SALDO");
						oChkCheck.checked = false;
						document.getElementById('ReciboImporteACobrar_' + i).value = numChkTxt
						break;
					}
				}
				impoTxt = parseInt(impoTxt);
				totalSeleccionado = totalSeleccionado + impoTxt;
	
				celdas.each(function(td){td.addClassName("selected");});
			}else{
				celdas.each(function(td){td.removeClassName("selected");});
			}	
		}
	
//		document.getElementById('ReciboImporteCobro').value = 0;

		totalSeleccionado = totalSeleccionado/100;

		if(totalSeleccionado >= importeCabecera){
			importeCobro = totalSeleccionado;
			document.getElementById('ReciboImporteCobro').value = totalSeleccionado;
		}
//		alert(totalSeleccionado);
//		alert(importeCabecera);
//		alert(importeCobro);
		
		document.getElementById('ReciboImporte').value = importeCobro;
		
		document.getElementById("ReciboImporteDetalle").value = totalSeleccionado;
		totalSeleccionado = FormatCurrency(totalSeleccionado);
		document.getElementById("ReciboImporteDetalleMostrar").value = totalSeleccionado;
		document.getElementById("ReciboImporteDetalleMostrar").disabled = true;
	}

</script>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formCabeceraCobro','id'=>'formCabeceraCobro','onsubmit' => "return ctrlCobro()", 'action' => "recibo/" . $clientes['Cliente']['id'] ));?>

		<table class="tbl_form">
			<tr>
				<td>Fecha del Cobro:</td>
				<td><?php echo $frm->calendar('Recibo.fecha_cobro',null,$fechaCobro,date('Y')-1,date('Y')+1)?></td>
				<td>Importe del Cobro:</td>
				<td><?php echo $frm->money('Recibo.importe_cabecera','',$importeCobro) ?></td>
			</tr>
			<tr>
				<td>Observaci&oacute;n</td>
				<td><?php echo $frm->input('Recibo.observacion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
				<td><?php echo $frm->submit("SIGUIENTE")?></td>
				<td></td>
			</tr>
		</table>
	    <?php echo $frm->hidden("Recibo.Cabecera", array('value' => 1)) ?>
<?php echo $frm->end(); ?>
</div>
<div style="clear: both;"></div>

<?php if($bloquear == 1): ?>
	
	<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro','onsubmit' => "return ctrlDetalle()", 'action' => "recibo/" . $clientes['Cliente']['id'] ));
	if($cntRenglones > 0):?>
		<table class="areaDatoForm">
			<caption>COMPROBANTES PENDIENTES DE COBRO</caption>
			<tr>
				<th>Comprobante</th>
				<th>Fecha Comp.</th>
				<th>Imp.Comp.</th>
				<th>Cuota</th>
				<th>Vencimiento</th>
				<th>Imp.Venc.</th>
				<th>Cobrado</th>
				<th>Saldo</th>
				<th>Importe a Cobrar</th>
				<th></th>
			</tr>
			<?php
			  	foreach($facturaPendiente as $fPendiente):
			  		$i++;
			?>
		  		<tr id="TRL_<?php echo $i?>">
					<td><?php echo $fPendiente['tipo_comprobante_desc'] . ' (' . $fPendiente['comentario'] . ')' ?></td>
					<td><?php echo $fPendiente['fecha_comprobante'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente['fecha_comprobante'])?></td>
					<td align="right"><?php echo number_format($fPendiente['total_comprobante'],2) ?></td>
					<td><?php echo $fPendiente['cuota'] ?></td>
					<td><?php echo $fPendiente['vencimiento'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente["vencimiento"])?></td>
					<td align="right"><?php echo number_format($fPendiente["importe"],2) ?></td>
					<td align="right"><?php echo number_format($fPendiente["cobro"],2) ?></td>
					<td align="right"><?php echo number_format($fPendiente["saldo"],2) ?></td>
					<?php
						if($fPendiente['signo'] == '-'):
					?>
						<td align="right"><input name="data[Recibo][detalle][importe_a_cobrar][<?php echo $i ?>]" type="text" value="<?php echo number_format($fPendiente["saldo"],2,".","") ?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,true)" onblur="selSum()" id="ReciboImporteACobrar_<?php echo $i ?>" /></td>
					<?php 
						else:
					?>
						<td align="right"><input name="data[Recibo][detalle][importe_a_cobrar][<?php echo $i ?>]" type="text" value="<?php echo number_format($fPendiente["saldo"],2,".","") ?>" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true)" onblur="selSum()" id="ReciboImporteACobrar_<?php echo $i ?>" /></td>
					<?php
						endif;
					?>
					<td>
						<input type="checkbox" name="data[Recibo][detalle][check][<?php echo $i ?>]" value="<?php echo number_format(round($fPendiente["saldo"],2) * 100,0,".","")?>" id="ReciboSaldo_<?php echo $i?>" onclick="chkOnclick()"/>
						<?php echo $frm->hidden("Recibo.detalle.tipo.$i", array('value' => $fPendiente['tipo'])) ?>
					    <?php echo $frm->hidden("Recibo.detalle.id.$i", array('value' => $fPendiente['id'])) ?>
					</td>
		  		</tr>
	  		<?php endforeach;?>
		
			<tr class='totales'>
				<td colspan="5"></td>
				<td colspan="3" align='right'>TOTAL DEL COBRO</td>
				<td align="right"><?php echo $frm->number('Recibo.importe_detalle_mostrar',array('size'=>12,'maxlength'=>12, 'disabled' => 'disabled'));?></td>
				<td></td>
			</tr>
		</table>
	<? else: ?>
		<?php echo $frm->hidden("Recibo.importe_detalle_mostrar", array('value' => 0.00)); ?>
	<?php endif; ?>	
	<?php echo $frm->hidden("Recibo.importe_detalle", array('value' => 0.00)); ?>
		<div  class="areaDatoForm">	
		
			<table class="tbl_form">
			<?php echo $this->renderElement('recibos/forma_cobro',array(
										'plugin'=>'clientes'))?>
			</table>
		</div>
		
		<div style="clear: both;"></div>
			<?php echo $frm->hidden('Recibo.tipo_documento', array('value' => 'REC')); ?>
			<?php echo $frm->hidden("Recibo.cabecera_cliente_id", array('value' => $clientes['Cliente']['id'])) ?>
			<?php echo $frm->hidden("Recibo.destinatario", array('value' => $clientes['Cliente']['razon_social_resumida'])) ?>
		    <?php echo $frm->hidden("Recibo.detalle_cobro", array('value' => 1)) ?>
			<?php echo $frm->hidden('Recibo.fecha_comprobante', array('value' => $fechaCobro)) ?>
			<?php echo $frm->hidden('Recibo.forma_cobro_desc') ?>
			<?php echo $frm->hidden('Recibo.importe_cabecera', array('value' => $importeCobro)) ?>
			<?php echo $frm->hidden('Recibo.importe_cobro', array('value' => $importeCobro)) ?>
			<?php echo $frm->hidden('Recibo.acumula', array('value' => 0)) ?>
			<?php echo $frm->hidden('Recibo.observacion', array('value' => $this->data['Recibo']['observacion'])) ?>
			<?php echo $frm->hidden("Recibo.formadetalle", array('value' => 1)) ?>
			<?php echo $frm->btnGuardarCancelar(array('URL' => '/clientes/clientes/cta_cte/' . $clientes['Cliente']['id']))?>
		<div style="clear: both;"></div>
<?php endif; ?>	
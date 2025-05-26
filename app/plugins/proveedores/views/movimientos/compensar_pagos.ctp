<?php 
$fechaPago = date('Ymd');
$importePago = 0.00;
$cntRenglones = 0;

if(isset($facturaPendiente)) $cntRenglones = count($facturaPendiente);

?>

<script language="Javascript" type="text/javascript">
	
	var varFac = 0;
	var varAnt = 0;
	var rFac = <?php echo $rFac?>;
	var rAnt = <?php echo $rAnt?>;

	Event.observe(window, 'load', function() {
		
		var cabeceraPago = $('formCabeceraPago');
		
		$('btn_submit').disable();
	
	});


	function ctrlCompensar(){

	}
	
	function chkOnclickFac(){
		selSumFac();


		if(varFac == 1 && varAnt == 1) $('btn_submit').enable();
		else $('btn_submit').disable();
		
	}	

	function selSumFac(){	
		var totalSeleccionado = 0;
		var error = "";	

		items = 0;
		varFac = 0;

		for (i=1;i<=rFac;i++){
			var celdas = $('FAC_' + i).immediateDescendants();
			oChkCheck = document.getElementById('MovimientoSaldoFac_' + i);
			oTxtImpoPagar = document.getElementById('MovimientoImporteFac_' + i);
			if (oChkCheck.checked){
				items = 1;
				strValTxt = oTxtImpoPagar.value;
				numValTxt = new Number(strValTxt);
				numValTxt = numValTxt.toFixed(2);
				impoTxt = numValTxt * 100;
				impoTxt = impoTxt.toFixed(0);
	
				strChkTxt = oChkCheck.value;
				numChkTxt = new Number(strChkTxt);
				numChkTxt = numChkTxt / 100;
				numChkTxt = numChkTxt.toFixed(2);
	
				impoTxt = parseInt(impoTxt);
				totalSeleccionado = totalSeleccionado + impoTxt;
				varFac = 1;
	
				celdas.each(function(td){td.addClassName("selected");});
			}else{
				celdas.each(function(td){td.removeClassName("selected");});
			}	
		}
	
//		document.getElementById('MovimientoImportePago').value = 0;

		totalSeleccionado = totalSeleccionado/100;
		
		totalSeleccionado = FormatCurrency(totalSeleccionado);
		document.getElementById("ClienteImporteFacturaMostrar").value = totalSeleccionado;
		document.getElementById("ClienteImporteFacturaMostrar").disabled = true;
	}


	function chkOnclickAnt(){
		selSumAnt();

		if(varFac == 1 && varAnt == 1) $('btn_submit').enable();
		else $('btn_submit').disable();
		
	}	

	function selSumAnt(){	
		var totalSeleccionado = 0;
		var error = "";	

		items = 0;
		varAnt = 0;
		
		for (i=1;i<=rAnt;i++){
			var celdas = $('ANT_' + i).immediateDescendants();
			oChkCheck = document.getElementById('MovimientoSaldoAnt_' + i);
			oTxtImpoPagar = document.getElementById('MovimientoImporteAnt_' + i);
			if (oChkCheck.checked){
				items = 1;
				strValTxt = oTxtImpoPagar.value;
				numValTxt = new Number(strValTxt);
				numValTxt = numValTxt.toFixed(2);
				impoTxt = numValTxt * 100;
				impoTxt = impoTxt.toFixed(0);
	
				strChkTxt = oChkCheck.value;
				numChkTxt = new Number(strChkTxt);
				numChkTxt = numChkTxt / 100;
				numChkTxt = numChkTxt.toFixed(2);
	
				impoTxt = parseInt(impoTxt);
				totalSeleccionado = totalSeleccionado + impoTxt;
				varAnt = 1;
	
				celdas.each(function(td){td.addClassName("selected");});
			}else{
				celdas.each(function(td){td.removeClassName("selected");});
			}	
		}
	
//		document.getElementById('MovimientoImportePago').value = 0;

		totalSeleccionado = totalSeleccionado/100;
		
		totalSeleccionado = FormatCurrency(totalSeleccionado);
		document.getElementById("ClienteImporteAplicoMostrar").value = totalSeleccionado;
		document.getElementById("ClienteImporteAplicoMostrar").disabled = true;
	}

</script>

<?php echo $this->renderElement('proveedor/proveedor_header',array('proveedor' => $proveedores, 'plugin' => 'proveedores'))?>
<h3>COMPENSAR PAGOS</h3>
	<?php echo $frm->create(null,array('name'=>'formDetallePago','id'=>'formDetallePago','onsubmit' => "return ctrlCompensar()", 'action' => "compensar_pagos/" . $proveedores['Proveedor']['id'] ));
 		if($cntRenglones > 0):?>
			<table class="areaDatoForm">
				<caption>FACTURAS PENDIENTES DE COMPENSAR</caption>
				<tr>
					<th>Comprobante</th>
					<th align="center">Fecha Comp.</th>
					<th align="right">Imp.Comp.</th>
					<th>Cuota</th>
					<th>Vencimiento</th>
					<th align="right">Imp.Venc.</th>
					<th align="right">Pagado</th>
					<th align="right">Saldo a compensar</th>
					<th></th>
				</tr>
				<?php
					$i = 0;
				  	foreach($facturaPendiente as $fPendiente):
				  		if($fPendiente['tipo_pago'] == 'FA'):
					  		$i++;
							?>
			  		<tr id="FAC_<?php echo $i?>">
						<td><?php echo $fPendiente['tipo_comprobante_desc'] . ' (' . $fPendiente['comentario'] . ')' ?></td>
						<td align="center"><?php echo $fPendiente['fecha_comprobante'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente['fecha_comprobante'])?></td>
						<td align="right"><?php echo number_format($fPendiente['total_comprobante'],2) ?></td>
						<td align="center"><?php echo $fPendiente['cuota'] ?></td>
						<td align="center"><?php echo $fPendiente['vencimiento'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente["vencimiento"])?></td>
						<td align="right"><?php echo number_format($fPendiente["importe"],2) ?></td>
						<td align="right"><?php echo number_format($fPendiente["pago"],2) ?></td>
						<td align="right"><?php echo number_format($fPendiente["saldo"],2) ?></td>
						
						<td>
							<input type="checkbox" name="data[Movimiento][fac][check][<?php echo $i ?>]" value="<?php echo number_format(round($fPendiente["saldo"],2) * 100,0,".","")?>" id="MovimientoSaldoFac_<?php echo $i?>" onclick="chkOnclickFac()"/>
							<?php // echo $frm->hidden("Movimiento.fac.tipo.$i", array('value' => $fPendiente['tipo'])) ?>
						    <?php echo $frm->hidden("Movimiento.fac.id.$i", array('value' => $fPendiente['id'])) ?>
						</td>
						<td align="right"><input type="hidden" name="data[Movimiento][fac][importe_a_pagar][<?php echo $i ?>]" type="text" value="<?php echo number_format($fPendiente["saldo"],2,".","") ?>" id="MovimientoImporteFac_<?php echo $i ?>" /></td>
			  		</tr>
			  		<?php endif;?>
		  		<?php endforeach;?>
		  		<tr>
					<td colspan="5" style="border-top: 1px solid black;border-left: 1px solid black;border-bottom: 1px solid black;"></td>
					<td colspan="2" style="border-top: 1px solid black;border-bottom: 1px solid black;" align='right'>TOTAL FACTURA</td>
					<td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;"><?php echo $frm->number('Cliente.importe_factura_mostrar',array('size'=>12,'maxlength'=>12, 'disabled' => 'disabled'));?></td>
					<td colspan="2" style="border-top: 1px solid black;border-right: 1px solid black;border-bottom: 1px solid black;"></td>
		  		</tr>
		</table>

			<table class="areaDatoForm">
				<caption>PAGO A CUENTAS, NOTAS DE DEBITOS Y CREDITOS</caption>
				<tr>
					<th>Comprobante</th>
					<th align="center">Fecha Comp.</th>
					<th align="right">Imp.Comp.</th>
					<th>Cuota</th>
					<th>Vencimiento</th>
					<th align="right">Imp.Venc.</th>
					<th align="right">Pagado</th>
					<th align="right">Saldo a compensar</th>
					<th></th>
				</tr>
				<?php
					$i = 0;
				  	foreach($facturaPendiente as $fPendiente):
				  		if($fPendiente['tipo_pago'] != 'FA'):
					  		$i++;
							?>
			  		<tr id="ANT_<?php echo $i?>">
						<td><?php echo $fPendiente['tipo_comprobante_desc'] . ' (' . $fPendiente['comentario'] . ')' ?></td>
						<td align="center"><?php echo $fPendiente['fecha_comprobante'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente['fecha_comprobante'])?></td>
						<td align="right"><?php echo number_format($fPendiente['total_comprobante'],2) ?></td>
						<td align="center"><?php echo $fPendiente['cuota'] ?></td>
						<td align="center"><?php echo $fPendiente['vencimiento'] == '  /  /  ' ? '' : $util->armaFecha($fPendiente["vencimiento"])?></td>
						<td align="right"><?php echo number_format($fPendiente["importe"],2) ?></td>
						<td align="right"><?php echo number_format($fPendiente["pago"],2) ?></td>
						<td align="right"><?php echo number_format($fPendiente["saldo"],2) ?></td>
						
						<td>
							<input type="checkbox" name="data[Movimiento][ant][check][<?php echo $i ?>]" value="<?php echo number_format(round($fPendiente["saldo"],2) * 100,0,".","")?>" id="MovimientoSaldoAnt_<?php echo $i?>" onclick="chkOnclickAnt()"/>
							<?php echo $frm->hidden("Movimiento.ant.tipo.$i", array('value' => $fPendiente['tipo'])) ?>
						    <?php echo $frm->hidden("Movimiento.ant.id.$i", array('value' => $fPendiente['id'])) ?>
						</td>
						<td align="right"><input type="hidden" name="data[Movimiento][ant][importe_a_pagar][<?php echo $i ?>]" type="text" value="<?php echo number_format($fPendiente["saldo"],2,".","") ?>" id="MovimientoImporteAnt_<?php echo $i ?>" /></td>
			  		</tr>
			  		<?php endif;?>
		  		<?php endforeach;?>
		  		<tr>
					<td colspan="5" style="border-top: 1px solid black;border-left: 1px solid black;border-bottom: 1px solid black;"></td>
					<td colspan="2" style="border-top: 1px solid black;border-bottom: 1px solid black;" align='right'>TOTAL APLICAR</td>
					<td align="right" style="border-top: 1px solid black;border-bottom: 1px solid black;" align="right"><?php echo $frm->number('Cliente.importe_aplico_mostrar',array('size'=>12,'maxlength'=>12, 'disabled' => 'disabled'));?></td>
					<td colspan="2" style="border-top: 1px solid black;border-right: 1px solid black;border-bottom: 1px solid black;"></td>
		  		</tr>
		</table>

	<?php endif; ?>	

		<div  class="areaDatoForm">	

			<table class="tbl_form">
				<tr id="fPago">
					<td>FECHA A COMPENSAR:</td>
					<td><?php echo $frm->calendar('Movimiento.fpago',null,$fechaPago,date('Y')-3,date('Y')+1)?></td>
				</tr>
			</table>
		</div>
		
		<div style="clear: both;"></div>
		<?php echo $frm->hidden("Movimiento.proveedor_id", array('value' => $proveedores['Proveedor']['id'])) ?>
		<?php echo $frm->btnGuardarCancelar(array('URL' => '/Proveedores/movimientos/pendientes/' . $proveedores['Proveedor']['id']))?>

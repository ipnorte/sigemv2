
<?php echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'))?>

<h3>HISTORIAL DE PAGOS DEL SOCIO :: REVERSAR PAGO</h3>

<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => 1,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>


<?php if(!empty($cobro['OrdenDescuentoCobroCuota'])):?>

	<script language="Javascript" type="text/javascript">

	var rows = <?php echo count($cobro['OrdenDescuentoCobroCuota'])?>;
	var rowsR = <?php echo count($reintegros)?>;

	Event.observe(window, 'load', function(){
		$('btn_submit').disable();	
		document.getElementById('OrdenDescuentoCobroCuotaSumaReverso').value = 0;
		document.getElementById('SocioReintegroSumaReverso').value = 0;
		enableCuentaBanco();
	});

	function validateForm(){
		if(!SelSum()) return false;

                var periodoInforme = $('LiquidacionPeriodo').getValue();
                if(periodoInforme === null){
                    alert('NO EXISTEN PERIODOS CORRESPONDIENTES A LIQUIDACIONES ABIERTAS');
                    return false;
                }
        
        

		var totalPago = new Number(document.getElementById('OrdenDescuentoCobroCuotaSumaTotal').value);
		var totalReverso = new Number(document.getElementById('OrdenDescuentoCobroCuotaSumaReverso').value);

		var nroOperacion = document.getElementById('OrdenDescuentoCobroCuotaNumeroOperacion').value;
		var seleccion = $('OrdenDescuentoCobroCuotaBancoCuentaId').getValue();
		var tipo = seleccion.substr(0,1);

		if(nroOperacion === '' && tipo !== 'C')
		{ 
			alert('INGRESE EL NUMERO DE OPERACION');
			document.getElementById('OrdenDescuentoCobroCuotaNumeroOperacion').focus();
			return false;
		}
		
		totalPago = totalPago.toFixed(2);
		totalReverso = totalReverso.toFixed(2);

		var msgConfirm = "";

		if(totalReverso !== '0.00'){
			msgConfirm = "ATENCION!\n\n*** REVERSAR PAGOS ****\n\nIMPORTE TOTAL DEL PAGO: "+totalPago+"\nIMPORTE REVERSADO: "+totalReverso+"\n\nINFORMAR AL PROVEEDOR CON LA LIQUIDACION DE\n\n" + getStrPeriodo("OrdenDescuentoCobroCuotaPeriodoProveedorReverso");
			msgConfirm = msgConfirm + "\n\n";
		}
		
		var totalPagoReint = new Number(document.getElementById('SocioReintegroSumaTotal').value);
		var totalReversoReint = new Number(document.getElementById('SocioReintegroSumaReverso').value);
		totalPagoReint = totalPagoReint.toFixed(2);
		totalReversoReint = totalReversoReint.toFixed(2);

		if(totalReversoReint !== '0.00'){
			 msgConfirm = msgConfirm + "*** REVERSAR REINTEGROS ***\n\n";
			 msgConfirm = msgConfirm + "TOTAL EMITIDO: " + totalPagoReint + "\nIMPORTE REVERSADO: " + totalReversoReint + "\n\n";
		}
		msgConfirm = msgConfirm + "DESEA CONTINUAR?";
		
		return confirm(msgConfirm);
	}
		

	function chkOnclick(){
		  SelSum();
	}

	function SelSum(){
		
		var totalSeleccionado = 0;
		var error = "";	
		for (i = 1; i <= rows; i++){
			
			var celdas = $('LTR_' + i).immediateDescendants();
			oChkCheck = document.getElementById('OrdenDescuentoCobroCuotaCheckId_' + i);
			oTxtImpoReversa = document.getElementById('OrdenDescuentoCobroCuotaId1_' + i);
			
			if (oChkCheck.checked){
				strValTxt = oTxtImpoReversa.value;
				numValTxt = new Number(strValTxt);
				numValTxt = numValTxt.toFixed(2);
				impoTxt = numValTxt * 100;
				impoTxt = impoTxt.toFixed(0);
				if(impoTxt == 0){
					alert("EL IMPORTE INDICADO PARA REVERSAR NO PUEDE SER CERO!");
					oChkCheck.checked = false;
					celdas.each(function(td){td.removeClassName("selected");});
					return false;
				}
				chkValue = parseInt(oChkCheck.value);
				if(impoTxt > chkValue){
					alert("EL IMPORTE INDICADO PARA REVERSAR (" + strValTxt + ") NO PUEDE SER SUPERIOR AL IMPORTE PAGADO DE LA CUOTA!");
					oChkCheck.checked = false;
					celdas.each(function(td){td.removeClassName("selected");});
					return false;
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
		document.getElementById('OrdenDescuentoCobroCuotaSumaReverso').value = totalSeleccionado;

		totalSeleccionado = FormatCurrency(totalSeleccionado);

		$('total_seleccionado').update(totalSeleccionado);
		return true;
	}		


	function chkOnclickReintegro(){
		  SelSumReintegro();
	}

	function SelSumReintegro(){
		
		var totalSeleccionado = 0;
		var error = "";	

		for (i = 1; i <= rowsR; i++){
			
			var celdas = $('LTRR_' + i).immediateDescendants();
			oChkCheck = document.getElementById('SocioReintegroCheckId_' + i);
			oTxtImpoReversa = document.getElementById('SocioReintegroId1_' + i);
			
			if (oChkCheck.checked){
				strValTxt = oTxtImpoReversa.value;
				numValTxt = new Number(strValTxt);
				numValTxt = numValTxt.toFixed(2);
				impoTxt = numValTxt * 100;
				impoTxt = impoTxt.toFixed(0);
				if(impoTxt === 0){
					alert("EL IMPORTE INDICADO PARA REVERSAR NO PUEDE SER CERO!");
					oChkCheck.checked = false;
					celdas.each(function(td){td.removeClassName("selected");});
					return false;
				}
				chkValue = parseInt(oChkCheck.value);
				if(impoTxt > chkValue){
					alert("EL IMPORTE INDICADO PARA REVERSAR (" + strValTxt + ") NO PUEDE SER SUPERIOR AL SALDO DEL REINTEGRO!");
					oChkCheck.checked = false;
					celdas.each(function(td){td.removeClassName("selected");});
					return false;
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
		document.getElementById('SocioReintegroSumaReverso').value = totalSeleccionado;

		totalSeleccionado = FormatCurrency(totalSeleccionado);

		$('total_seleccionado_reintegro').update(totalSeleccionado);
		return true;
	}	
	


	function enableCuentaBanco(){
		var seleccion = $('OrdenDescuentoCobroCuotaBancoCuentaId').getValue();
		var tipo = seleccion.substr(0,1);
		
		disableCuentaBanco();

		$('fecha').show();

		if(tipo !== 'C')
		{
			$('nro_opera').show();
		}
	}


	function disableCuentaBanco(){
		$('fecha').hide();
		$('nro_opera').hide();
	}
	
	
	</script>


	<h4>DETALLE DE COBROS POR RECIBO DE SUELDO - ORDEN DE COBRO #<?php echo $cobro['OrdenDescuentoCobro']['id']?></h4>
	
	<?php echo $frm->create(null,array('action' => 'reversar/'.$id,'id' => 'FormReversos', 'onsubmit' => "return validateForm();"))?>
	<table>
		<tr>
			<th>ORGANISMO</th>
			<th>ORDEN</th>
			<th>TIPO - NUMERO</th>
			<th>PROVEEDOR - PRODUCTO</th>
			<th>CONCEPTO</th>
			<th>CUOTA</th>
			<th>PERIODO</th>
			<th>IMPORTE COBRADO</th>
			<th>REVERSADO</th>
			<th></th>
			<th>INFORMADO</th>
			<th></th>
			
		</tr>
		<?php $periodoActual = null;?>
		<?php $primero = true;?>
		<?php $ACUM = 0;?>
		<?php $i = 0;?>
		<?php foreach($cobro['OrdenDescuentoCobroCuota'] as $cobro):?>
		
			<?php //   debug($cobro)?>
		
			<?php 
				if($periodoActual != $cobro['periodo_cobro']):
				
					$periodoActual = $cobro['periodo_cobro'];
					
					if($primero):
					
						$primero = false;
						
					else:
				?>
				
						<tr class="totales">
							<th colspan="7">TOTAL PERIODO</th>
							<th><?php echo $util->nf($ACUM)?></th>
							<th></th>
						</tr>
				
				<?php 		
					
						$ACUM = 0;
					
					endif;
				
				endif;
				
				$ACUM += $cobro['importe'];
			
				$i++;
			?>
		
			<tr id="LTR_<?php echo $i?>" class="<?php echo ($cobro['reversado'] == 1 ? "activo_0" : "")?>">
				<td><?php echo $cobro['OrdenDescuentoCuota']['organismo']?></td>
				<td><?php echo $cobro['OrdenDescuentoCuota']['orden_descuento_id']?></td>
				<td><?php echo $cobro['OrdenDescuentoCuota']['tipo_nro']?></td>
				<td><?php echo $cobro['OrdenDescuentoCuota']['proveedor_producto']?></td>
				<td><?php echo $cobro['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
				<td align="center"><?php echo $cobro['OrdenDescuentoCuota']['cuota']?></td>
				<td align="center"><?php echo $util->periodo($cobro['OrdenDescuentoCuota']['periodo'])?></td>
				<td align="right"><?php echo $util->nf($cobro['importe'])?></td>
				<?php if($cobro['reversado'] == 0):?>
					<td><input type="text" name="data[OrdenDescuentoCobroCuota][id1][<?php echo $cobro['id']?>]" value="<?php echo number_format($cobro['importe'],2,".","")?>" id="OrdenDescuentoCobroCuotaId1_<?php echo $i?>" class="input_number" onkeypress = "return soloNumeros(event,true)" size="12" maxlength="12"/></td>
					<td align="center"><input type="checkbox" name="data[OrdenDescuentoCobroCuota][check_id][<?php echo $cobro['id']?>]" id="OrdenDescuentoCobroCuotaCheckId_<?php echo $i?>" value="<?php echo number_format(round($cobro['importe'],2) * 100,0,".","")?>" onclick="chkOnclick()"/></td>
					<td align="center"></td>
					<td align="center"></td>
				<?php else:?>
					<td align="right"><?php echo $util->nf($cobro['importe_reversado'])?></td>
					<td align="center"><input type="checkbox" name="data[OrdenDescuentoCobroCuota][check_id][<?php echo $cobro['id']?>]" id="OrdenDescuentoCobroCuotaCheckId_<?php echo $i?>" value="<?php echo number_format(round($cobro['importe'],2) * 100,0,".","")?>" onclick="chkOnclick()" disabled="disabled"/></td>
					<td align="center"><?php echo $util->periodo($cobro['periodo_proveedor_reverso'])?></td>
					<td><?php echo $controles->btnModalBox(array('title' => 'DETALLE DE LA NOTA DE DEBITO REVERSO','url' => '/mutual/orden_descuento_cuotas/view/'.$cobro['debito_reverso_id'],'h' => 450, 'w' => 750))?></td>
				<?php endif;?>
			</tr>
		
		<?php endforeach;?>
			<tr class="totales">
				<th colspan="7">TOTAL COBRO #<?php echo $cobro['id']?></th>
				<th><?php echo $util->nf($ACUM)?></th>
				<th id="totalReversado"><div style="color: green; font-weight: bold;" id="total_seleccionado"></div></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
			<?php 
            $ACUM_REINTEGRO = 0;
            if(!empty($reintegros)):
            ?>
				
				<td colspan="12">
					<h3>REINTEGROS EMITIDOS PARA EL PERIODO (pendientes)</h3>
					<table>
						<tr>
							<th>#</th>
							<th>TIPO</th>
							<th>PERIODO</th>
							<th>FECHA</th>
							<th>LIQUIDACION</th>
							<th>REINTEGRO</th>
							<th>APLICADO</th>
							<th>REVERSADO</th>
							<th>SALDO</th>
							<th>A REVERSAR</th>
							<th></th>
						</tr>
						<?php $ACUM_REINTEGRO = $i = 0;?>
						<?php foreach($reintegros as $reintegro):?>
							<?php $i++;?>
							<?php $ACUM_REINTEGRO += $reintegro['SocioReintegro']['saldo']?>
							<tr id="LTRR_<?php echo $i?>">
								<td><?php echo $reintegro['SocioReintegro']['id']?></td>
								<td><strong><?php echo $reintegro['SocioReintegro']['tipo']?></strong></td>
								<td><?php echo $util->periodo($reintegro['SocioReintegro']['periodo'],true)?></td>
								<td><?php echo $util->armaFecha($reintegro['SocioReintegro']['created'])?></td>
								<td><?php echo $reintegro['SocioReintegro']['liquidacion_str']?></td>
								<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_reintegro'])?></td>
								<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_aplicado'])?></td>	
								<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_reversado'])?></td>	
								<td align="right"><?php echo ($reintegro['SocioReintegro']['saldo'] < 0 ? "<span style='color:red;'>".$util->nf($reintegro['SocioReintegro']['saldo'])."</span>" : $util->nf($reintegro['SocioReintegro']['saldo']))?></td>
								<td><input type="text" <?php echo ($reintegro['SocioReintegro']['saldo'] > 0 ? "" : "disabled='disabled'")?>  name="data[SocioReintegro][id1][<?php echo $reintegro['SocioReintegro']['id']?>]" value="<?php echo number_format($reintegro['SocioReintegro']['saldo'],2,".","")?>" id="SocioReintegroId1_<?php echo $i?>" class="input_number" onkeypress = "return soloNumeros(event,true)" size="12" maxlength="12"/></td>
								<td><input type="checkbox" <?php echo ($reintegro['SocioReintegro']['saldo'] > 0 ? "" : "disabled='disabled'")?> name="data[SocioReintegro][check_id][<?php echo $reintegro['SocioReintegro']['id']?>]" id="SocioReintegroCheckId_<?php echo $i?>" value="<?php echo number_format(round($reintegro['SocioReintegro']['saldo'],2) * 100,0,".","")?>" onclick="chkOnclickReintegro()"/></td>				
							</tr>
						<?php endforeach;?>
						<tr class="totales">
							<th colspan="8">TOTAL REINTEGROS</th>
							<th><?php echo $util->nf($ACUM_REINTEGRO)?></th>
							<th id="totalReversadoReintegro"><div style="color: green; font-weight: bold;" id="total_seleccionado_reintegro"></div></th>
							<th></th>
						</tr>
					</table>
					<hr/>
				</td>				
			<?php endif;?>	
			
			<tr>
			
				<td colspan="12">
				
					<table class="tbl_form">
						<tr>
							<td>FECHA DE NOVEDAD</td><td><strong><?php echo date('d-m-Y')?></strong></td>
						</tr>
						<tr>
							<td>PERIODO A INFORMAR AL PROVEEDOR</td>
							<td>
                                <?php //   echo $frm->periodo('OrdenDescuentoCobroCuota.periodo_proveedor_reverso','',null,date('Y')-1,date('Y'))?>
                                <?php echo $this->renderElement("liquidacion/periodos_liquidados",array('plugin' => 'mutual', 'facturados' => false, 'organismo' => $cobro['OrdenDescuentoCuota']['codigo_organismo']))?>
                            </td>
						</tr>
						
						<tr id="cta_banco">
							<td>CUENTA BANCARIA:</td>
							<td><?php echo $this->renderElement('banco_cuentas/combo_cuentas',array(
													'plugin'=>'cajabanco',
													'label' => "",
													'model' => 'OrdenDescuentoCobroCuota.banco_cuenta_id',
													'disabled' => false,
													'empty' => false,
													'caja' => true,
													'onChange' => 'enableCuentaBanco()',
													'selected' => 0))?>
							</td>			
						</tr>
						
						<tr id="fecha">
							<td>FECHA MOVIMIENTO:</td>
							<td><?php echo $frm->calendar('OrdenDescuentoCobroCuota.fecha_operacion',null,null,date('Y')-1,date('Y')+1)?></td>
						</tr>
						
						<tr id="nro_opera">
							<td>NRO. OPERACION:</td>
							<td><?php echo $frm->input('OrdenDescuentoCobroCuota.numero_operacion', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
						</tr>

						<tr>
							<td colspan="2"><input type="submit" value = "REVERSAR" id = "btn_submit"/></td>
						</tr>
						
						
					</table>
				
				</td>
			
			</tr>
				
	
	</table>
	<?php //   echo $frm->hidden('OrdenDescuentoCobroCuota.periodo_proveedor_reverso',array('value' => $periodo_corte))?>
	<?php echo $frm->hidden('OrdenDescuentoCobroCuota.suma_total',array('value' => $ACUM))?>
	<?php echo $frm->hidden('OrdenDescuentoCobroCuota.suma_reverso')?>
	<?php echo $frm->hidden('SocioReintegro.suma_total',array('value' => $ACUM_REINTEGRO))?>
	<?php echo $frm->hidden('SocioReintegro.suma_reverso')?>	
	<?php echo $frm->end();?>
	<?php echo $controles->btnRew('REGRESAR AL LISTADO PAGOS DEL SOCIO','/mutual/orden_descuento_cobros/by_socio/'.$socio['Socio']['id'])?>
<?php else:?>
	<h4>NO EXISTEN PAGOS POR RECIBO DE SUELDO</h4>
	<?php //   echo $controles->btnRew('REGRESAR','/mutual/orden_descuento_cuotas/reversos/')?>
<?php endif;?>

<?php //   debug($cobros)?>
<?php //   debug($reintegros)?>
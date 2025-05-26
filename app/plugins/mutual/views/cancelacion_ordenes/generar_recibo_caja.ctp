<?php echo $this->renderElement('head',array('title' => 'ORDEN DE CANCELACION :: COBRO POR CAJA','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link'=>true,'plugin' => 'pfyj'))?>
<div class="row">
	<?php echo $controles->btnRew('Regresar','/mutual/cancelacion_ordenes/generar/'.$persona['Persona']['id'])?>
</div>

<script language="Javascript" type="text/javascript">
	var rows = <?php echo count($cancelaciones)?>;
	var reintegrar = 0;
	
	Event.observe(window, 'load', function() {
		
		var cabeceraCobro = $('formCabeceraCobro');
		
		<?php if($bloquear==1):?> 
			cabeceraCobro.disabled = true;
			$('formCabeceraCobro').disable();
			$('btn_submit').disable();
			document.getElementById("CancelacionOrdenImporte").value = 0.00;
			disableCobro();
			ocultarOptionFCobro();
			$('liquida').hide();
			
		<?php else:?>
			seleccionComercio()
		<?php endif; ?>
	
	});

	function disableCobro()
	{
		$("impCobro").hide();
		$("fecha").hide();
		$("observa").hide();
		$("compensa").hide();
		$("forma").hide();
	}
	
	function seleccionComercio()
	{
		var comercio = $('CancelacionOrdenProveedorOrigenFondoId').getValue();

		$('retiene').hide();
		$('siguiente').hide();
		
		if(comercio != 0){
			if(comercio != <?php echo MUTUALPROVEEDORID?>) $('retiene').show();
			$('siguiente').show();
		}
	}
	
	function enableCobro()
	{
		var comercio = $('CancelacionOrdenProveedorOrigenFondoId').getValue();

		disableCobro();

		if(comercio != 0)
		{
			$("impCobro").show();
			$("fecha").show();
			$("observa").show();
			if(comercio != <?php echo MUTUALPROVEEDORID?>)
			{
				$("compensa").show();
			}
			$("forma").show();
		}
	}
		
	function chkOnclick()
	{
		selSum();

//		acumulado = parseFloat($('acumulado').getValue());
		cobro = document.getElementById("CancelacionOrdenImporteCobro").value;
		cobro = parseFloat(cobro);
//		resto = cobro - acumulado;
		resto = cobro;
		if(resto == 0) $('btn_submit').enable();
		else $('btn_submit').disable();
		document.getElementById("CancelacionOrdenImporte").value = resto;
		document.getElementById("CancelacionOrdenFormaCobro").value = "";
		ocultarOptionFCobro();
       //selecciono el texto
       document.getElementById('CancelacionOrdenImporteTotal').select();
       //coloco otra vez el foco
       document.getElementById('CancelacionOrdenImporteTotal').focus();
		
	}	

	function selSum()
	{	
		var totalSeleccionado = 0;
		var importeCobro = $('CancelacionOrdenImporteCobro').getValue();
		var error = "";	
		var chequeado = 0;
		
		$('btn_submit').disable();
		disableCobro();
		ocultarOptionFCobro();
		
		for (i=1;i<=rows;i++)
		{
			var celdas = $('TRL_' + i).immediateDescendants();
			oChkCheck = document.getElementById('CancelacionOrdenSaldo_' + i);
			if (oChkCheck.checked)
			{
				chequeado = 1;
				
				strChkTxt = oChkCheck.value;
				numChkTxt = new Number(strChkTxt);
				numChkTxt = numChkTxt / 100;
				numChkTxt = numChkTxt.toFixed(2);
	
				chkValue = parseInt(oChkCheck.value);
				totalSeleccionado = totalSeleccionado + chkValue;
	
				celdas.each(function(td){td.addClassName("selected");});
			}
			else
			{
				celdas.each(function(td){td.removeClassName("selected");});
			}	
		}
		
		if(chequeado == 1 && totalSeleccionado == 0)
		{
//			seleccionComercio();
			$("fecha").show();
			$("observa").show();
			$('btn_submit').enable();
		}
		
		if(chequeado == 1 && totalSeleccionado != 0)
		{
			enableCobro();
		}
		
		totalSeleccionado = totalSeleccionado/100;
		
		document.getElementById('CancelacionOrdenImporteCobro').value = totalSeleccionado;
		document.getElementById('CancelacionOrdenImporteTotal').value = totalSeleccionado;
		document.getElementById('CancelacionOrdenImporte').value = totalSeleccionado;
		document.getElementById('CancelacionOrdenImporteCancela').value = totalSeleccionado;
		
//		totalSeleccionado = FormatCurrency(totalSeleccionado);
	}
	
	
	function ctrlFondo()
	{
		var origenFondo = $('CancelacionOrdenProveedorOrigenFondoId').getValue();
		$('btn_submit').disable();
		if(origenFondo == 0)
		{
			alert('Debe seleccionar el \nOrigen de los Fondos');
			return false;
		}
                $('btn_submit').enable();
		return true;
	}


	function chkCompensa()
	{
		var cmpPago = $('CancelacionOrdenCompensaPago').getValue();


		ocultarOptionFCobro();
		$('forma').show();
		$('btn_submit').disable();
		if(cmpPago == 'on')
		{
			$('forma').hide();
			$('btn_submit').enable();
		}
		
	}
		

	function validarNumero(valor)
	{
	    //intento convertir a entero.
	    //si era un entero no le afecta, si no lo era lo intenta convertir
	    valor = parseFloat(valor);

	    //Compruebo si es un valor num�rico
	    if (isNaN(valor)) 
		{
	       //entonces (no es numero) devuelvo el valor cadena vacia
	       return "";
	    }
	    else
		{
	       //En caso contrario (Si era un n�mero) devuelvo el valor
	       return valor;
	    }
	}

	function compruebaValidoNumero()
	{
	    var numeroValidado = validarNumero(document.getElementById('CancelacionOrdenImporteTotal').value);
	    if(numeroValidado == "")
		{
	       // si era la cadena vac�a es que no era v�lido. Lo aviso
	       alert ("Debe escribir un valor!");
	       // selecciono el texto
	       document.getElementById('CancelacionOrdenImporteTotal').select();
	       // coloco otra vez el foco
	       document.getElementById('CancelacionOrdenImporteTotal').focus();
	    }
	    else
		{
			document.getElementById('CancelacionOrdenImporteCobro').value = numeroValidado;
			document.getElementById('CancelacionOrdenImporteTotal').value = numeroValidado;
			document.getElementById('CancelacionOrdenImporte').value = numeroValidado;
	    }
	} 


	function ctrlFormulario(){
            var importeCobrado = $('CancelacionOrdenImporteCobro').getValue();
            var importeCancela = $('CancelacionOrdenImporteCancela').getValue();
            var	importeReintegro = new Number(importeCobrado - importeCancela);

            $('btn_submit').disable();
            importeReintegro = importeReintegro.toFixed(2);

            if(importeReintegro > 0.00 && reintegrar == 0){
                if(confirm('ATENCION!!!\n Se generara Reintegro por $ ' + importeReintegro))
                { 
                    reintegrar = 1;
                    $('liquida').show();
                    $('CancelacionOrdenLiquidacionId').focus();
                    return false;
                }
                else return false;	
            }

//            $('btn_submit').enable();
            return true;
		
	}
</script>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formCabeceraCobro','id'=>'formCabeceraCobro','onsubmit' => "return ctrlFondo()", 'action' => "generar_recibo_caja/".$persona['Persona']['id'] ));?>

		<table class="tbl_form">
			<tr id="respo">
				<td>ORIGEN DE LOS FONDOS</td>
				<td><?php echo $frm->input('CancelacionOrden.proveedor_origen_fondo_id',array('type'=>'select','onchange' => 'seleccionComercio()', 'options'=>$cmbProveedores,'empty'=>FALSE, 'selected' => $origenFondo));?></td>
			</tr>
			<tr id="retiene">
				<td>RETIENE COMERCIO:</td>
				<td><input type="checkbox" name="data[CancelacionOrden][retiene_comercio] ?>]" id="CancelacionOrdenRetieneComercio" <?php echo ($retieneComercio == 1 ? 'checked="checked"' : "")?>/></td>
			</tr>
			<tr id="siguiente">
				<td></td>
				<td><?php echo $frm->submit("SIGUIENTE")?></td>
			</tr>
		</table>
	    <?php echo $frm->hidden("CancelacionOrden.Cabecera", array('value' => 1)) ?>
<?php echo $frm->end(); ?>
</div>

<?php if(count($cancelaciones) != 0):
	$socio_id = $cancelaciones[0]['CancelacionOrden']['socio_id'];?>
	<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro', 'onsubmit' => "return ctrlFormulario();", 'action' => 'generar_recibo_caja/'.$persona['Persona']['id'] ));?>
	<h4>DETALLE DE ORDENES DE CANCELACION EMITIDAS</h4>
		<script type="text/javascript">
		</script>
		<table>
			<tr>
				<th>#</th>
				<th>TIPO</th>
				<th>ORDEN</th>
				<th>TIPO / NUMERO</th>
				<th>PROVEEDOR / PRODUCTO</th>
				<th>A LA ORDEN DE</th>
				<th>DEUDA PROVEEDOR</th>
				<th>SALDO ORDEN DTO</th>
				<th>IMPORTE SELECCIONADO</th>
				<th>DEBITO/CREDITO</th>
				<th>VENCIMIENTO</th>
				<th></th>
				
			</tr>
			<?php $i = 0; ?>
			<?php foreach($cancelaciones as $cancelacion):?>
				<?php $i++; ?>
				<?php $importeProveedor =  $cancelacion['CancelacionOrden']['importe_proveedor'];?>			
				<?php //if($origenFondo == $cancelacion['CancelacionOrden']['orden_proveedor_id'] && $origenFondo != MUTUALPROVEEDORID) $importeProveedor = 0; ?>
				<?php if($retieneComercio == 1 || ($origenFondo == $cancelacion['CancelacionOrden']['orden_proveedor_id'] && $origenFondo != MUTUALPROVEEDORID)) $importeProveedor = 0;?>
				<?php // if($origenFondo != MUTUALPROVEEDORID && $cancelacion['CancelacionOrden']['orden_proveedor_id'] != MUTUALPROVEEDORID) $importeProveedor = 0; ?>
				<tr id="TRL_<?php echo $i?>">
					<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
					<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
					<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_descuento_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['tipo_nro_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
					<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
					<td align="right">
						<?php
							if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
								echo $this->requestAction('/config/global_datos/valor/' . $cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
								echo "&nbsp;= \$";
								echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
							}
						?>
					</td>
					<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
					<td><input type="checkbox" name="data[CancelacionOrden][id_check][<?php echo $cancelacion['CancelacionOrden']['id'] ?>]" value="<?php echo number_format(round($importeProveedor,2) * 100,0,".","")?>" id="CancelacionOrdenSaldo_<?php echo $i ?>" onclick="toggleCell('TRL_<?php echo $i?>',this); chkOnclick()"/></td>
				</tr>
			<?php endforeach;?>	
		</table>
		<div class="areaDatoForm2">
			<h4>DETALLE DEL COBRO POR CAJA</h4>
			<table class="tbl_form">
				<tr id="impCobro">
					<td>IMPORTE COBRO:</td>
					<!-- <td><?php echo $frm->money('CancelacionOrden.importe_total',null,0.00)?></td> -->
					<td>
						<div class="input text"><label for="CancelacionOrdenImporteTotal"></label><input name="data[CancelacionOrden][importe_total]" type="text" value="0" size="12" maxlength="12" class="input_number" onkeypress="return soloNumeros(event,true,false)" onblur="return compruebaValidoNumero()" id="CancelacionOrdenImporteTotal" /></div>
					</td>
				</tr>
				<tr id="fecha">
					<td>FECHA COBRO:</td>
					<td><?php echo $frm->calendar('CancelacionOrden.fecha_comprobante',null,(isset($fechaCobro) ? $fechaCobro : date('Y-m-d')),date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr id="observa">
					<td>OBSERVACION:</td>
					<td><?php echo $frm->input('CancelacionOrden.observacion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
				</tr>
				<tr id="compensa">
					<td>COMPENSA PAGO:</td>
					<td><input type="checkbox" name="data[CancelacionOrden][compensa_pago] ?>]" id="CancelacionOrdenCompensaPago" onclick="chkCompensa()" /></td>
				</tr>
					<?php echo $this->renderElement('recibos/forma_cobro',array(
													'model' =>'CancelacionOrden',	
													'plugin'=>'clientes'))?>
													
				<tr id="liquida">
					<td>LIQUIDACION (PENDIENTE DE IMPUTAR)</td>
					<td><?php echo $frm->input('CancelacionOrden.liquidacion_id',array('type' => 'select','options' => $liquidaciones,'label' => null))?></td>
				</tr>
				<tr><td colspan="3"><?php echo $frm->submit("RECAUDAR", array('id' => 'btn_submit'))?></td></tr>
			</table>
	
		</div>
		
		<div style="clear: both;"></div>
			<?php echo $frm->hidden('CancelacionOrden.tipo_documento', array('value' => 'REC')); ?>
			<?php echo $frm->hidden("CancelacionOrden.cabecera_socio_id", array('value' => $socio_id)) ?>
			<?php echo $frm->hidden('CancelacionOrden.forma_cobro_desc') ?>
			<?php echo $frm->hidden('CancelacionOrden.importe_cobro', array('value' => 0.00)) ?>
			<?php echo $frm->hidden('CancelacionOrden.acumula', array('value' => 0.00)) ?>
			<?php echo $frm->hidden('CancelacionOrden.importe_cancela', array('value' => 0.00)) ?>
			<?php echo $frm->hidden('CancelacionOrden.proveedor_origen_id', array('value' => $origenFondo));?>
			<?php echo $frm->hidden('CancelacionOrden.retiene_comercio', array('value' => $retieneComercio));?>
		<div style="clear: both;"></div>
		<?php echo $frm->end();?>
		
		<div style="clear: both;"></div>
<?php endif;?>
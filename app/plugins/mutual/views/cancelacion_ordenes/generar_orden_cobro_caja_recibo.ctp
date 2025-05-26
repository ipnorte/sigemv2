<?php echo $this->renderElement('head',array('title' => 'ORDEN DE CANCELACION :: COBRO POR CAJA','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link'=>true,'plugin' => 'pfyj'))?>
<div class="row">
	<?php echo $controles->btnRew('Regresar','/mutual/cancelacion_ordenes/generar/'.$persona['Persona']['id'])?>
</div>

<script language="Javascript" type="text/javascript">
	var rows = <?php echo count($cancelaciones)?>;
	var reintegrar = 0;
	
	Event.observe(window, 'load', function() {
		
            document.getElementById("CancelacionOrdenImporte").value = 0.00;
            disableCobro();
            ocultarOptionFCobro();
            $('liquida').hide();
            $('btn_submit').disable();
			
	
	});

	function disableCobro()
	{
            $("impCobro").hide();
            $("fecha").hide();
            $("observa").hide();
            $("forma").hide();
	}
	
	function enableCobro()
	{
            $("impCobro").show();
            $("fecha").show();
            $("observa").show();
            $("forma").show();
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
            var importeReintegro = new Number(importeCobrado - importeCancela);

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

            $('btn_submit').disable();
            return true;
		
	}
</script>

<?php if(count($cancelaciones) != 0):

	$socio_id = $cancelaciones[0]['CancelacionOrden']['socio_id'];?>
	<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro', 'onsubmit' => "return ctrlFormulario();", 'action' => 'generar_orden_cobro_caja_recibo/'.$persona['Persona']['id'] ));?>
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
                                <th></th>
				
			</tr>
                        <?php $importe_proveedor_acum = $saldo_orden_dto = $importe_seleccionado = $importe_diferencia = $orden_dto_cancela_total = 0;?>
			<?php $i = 0; ?>
			<?php foreach($cancelaciones as $cancelacion):?>
                    <?php 
                    $importe_proveedor_acum += $cancelacion['CancelacionOrden']['importe_proveedor'];
                    $saldo_orden_dto += $cancelacion['CancelacionOrden']['saldo_orden_dto'];
                    $importe_seleccionado += $cancelacion['CancelacionOrden']['importe_seleccionado'];
                    $importe_diferencia += $cancelacion['CancelacionOrden']['importe_diferencia'];
                    $orden_dto_cancela_total += $cancelacion['CancelacionOrden']['orden_dto_cancela_total'];
                    ?>                          
				<?php $i++; ?>
				<?php $importeProveedor = $cancelacion['CancelacionOrden']['importe_proveedor'];?>			
				<?php //if($origenFondo == $cancelacion['CancelacionOrden']['orden_proveedor_id'] && $origenFondo != MUTUALPROVEEDORID) $importeProveedor = 0; ?>
				<?php if($retieneComercio == 1 || ($origenFondo == $cancelacion['CancelacionOrden']['orden_proveedor_id'])) $importeProveedor = 0;?>
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
//								echo $this->requestAction('/config/global_datos/valor/' . $cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
//								echo "&nbsp;= \$";
								echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
							}
						?>
					</td>
					<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
					<td>
                                            <?php if($cancelacion['CancelacionOrden']['total_cuotas_orden'] > $cancelacion['CancelacionOrden']['saldo_cuotas_orden']):?>
                                            <input type="checkbox" disabled="disabled" name="data[CancelacionOrden][id_check][<?php echo $cancelacion['CancelacionOrden']['id'] ?>]" value="<?php echo number_format(round($cancelacion['CancelacionOrden']['importe_proveedor'],2) * 100,0,".","")?>" id="CancelacionOrdenSaldo_<?php echo $i ?>" onclick="toggleCell('TRL_<?php echo $i?>',this); chkOnclick()"/>
                                            <?php else:?>
                                            <input type="checkbox" name="data[CancelacionOrden][id_check][<?php echo $cancelacion['CancelacionOrden']['id'] ?>]" value="<?php echo number_format(round($cancelacion['CancelacionOrden']['importe_proveedor'],2) * 100,0,".","")?>" id="CancelacionOrdenSaldo_<?php echo $i ?>" onclick="toggleCell('TRL_<?php echo $i?>',this); chkOnclick()"/>
                                            <?php endif;?>
                                        </td>
					<td>
                                            <?php if($cancelacion['CancelacionOrden']['total_cuotas_orden'] > $cancelacion['CancelacionOrden']['saldo_cuotas_orden']):?>
                                            <span style="color: red;">El saldo actual de las cuotas incluidas es MENOR al saldo original seleccionado al momento de generar esta orden.</span>                                           
                                            <?php endif;?>
                                        </td>                                        
				</tr>

			<?php endforeach;?>
                                <tr class="totales">
                                    <th colspan="6">TOTALES</th>
                                    <th><?php echo number_format($importe_proveedor_acum,2)?></th>
                                    <th><?php echo number_format($saldo_orden_dto,2)?></th>
                                    <th><?php echo number_format($importe_seleccionado,2)?></th>
                                    <th><?php echo number_format($importe_diferencia,2)?></th>
                                    <th colspan="3"></th>
                                </tr>
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
                
<?php else:?>
                <div class="notices_error">
                    <p>NO EXISTEN ORDENES DE CANCELACION DISPONIBLES PARA COBRO POR CAJA</p>
                    <p>Verifique que la/s ordenes EMITIDAS (si existe o existieran) <strong>NO</strong> se encuentre/n vinculadas a una solicitud.</p>
                </div>            
<?php endif;?>

                <?php // debug($cancelaciones)?>
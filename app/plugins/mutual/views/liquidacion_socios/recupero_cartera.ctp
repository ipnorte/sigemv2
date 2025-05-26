<?php echo $this->renderElement('head',array('title' => 'RECUPERO DE CARTERA'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php if(empty($resumen)):?>
<div class="areaDatoForm">

	<script type="text/javascript">

	Event.observe(window, 'load', function(){

		<?php if(!$isRecuperable):?>
		$('recupero_cartera').disable();
		<?php endif;?>

	});

	</script>
	
	<?php echo $frm->create(null,array('action' => 'recupero_cartera/' . $liquidacion['Liquidacion']['id'],'id' => 'recupero_cartera'))?>
	
	<table class="tbl_form">
	
			<tr>
				<td>PROVEEDOR</td>
				<td>
				<?php echo $this->renderElement('proveedor/combo_general',array(
																				'plugin'=>'proveedores',
																				'metodo' => "proveedores_liquidados_list/" . $liquidacion['Liquidacion']['id'].'/0',
																				'model' => 'LiquidacionSocios.proveedor_id',
																				'empty' => false,
																				'selected' => (isset($this->data['LiquidacionSocios']['proveedor_id']) ? $this->data['LiquidacionSocios']['proveedor_id'] : "")
				))?>			
				</td>
				<td><?php echo $frm->submit("CONSULTAR")?></td>				
			</tr>	
	
	</table>
	<?php echo $frm->end()?>
	<?php if(!$isRecuperable):?>
	<div class='notices_error' style="width: 100%">
		NO SE PUEDE EFECTUAR EL RECUPERO DE CARTERA PORQUE EXISTEN LIQUIDACIONES POSTERIORES IMPUTADAS
	</div>
	<?php endif;?>

</div>
<?php elseif($isRecuperable):?>

<h3>RESUMEN DE LA LIQUIDACION :: <?php echo $resumen[0]['Proveedor']['razon_social']?> [<?php echo $liquidacion['Liquidacion']['periodo_desc_amp']?> | <?php echo $liquidacion['Liquidacion']['organismo']?>]</h3>

	<table>
		<tr>
			<th>LIQU. PERIODO</th>
			<th>LIQU. ATRASO</th>
			<th>TOTAL LIQU.</th>
			<th>COB. PERIODO</th>
			<th>COB. ATRASO</th>
			<th>TOTAL COB.</th>
			<th>REVERSADO</th>
			<th>COMISION</th>
			<th>NETO PROV.</th>
			<th>SALDO PERIODO</th>
			<th>SALDO ATRASO</th>
			<th>SALDO</th>
		</tr>
		<tr>
			<td align="center" style="background: #FFFFB8;"><?php echo $util->nf($resumen[0]['Proveedor']['total_periodo'])?></td>
			<td align="center" style="background: #FFFFB8;"><?php echo $util->nf($resumen[0]['Proveedor']['total_atraso'])?></td>
			<td align="center" style="background: #FFFFB8;"><strong><?php echo $util->nf($resumen[0]['Proveedor']['total'])?></strong></td>
			<td align="center" style="background: #F2FEE9"><?php echo $util->nf($resumen[0]['Proveedor']['total_periodo_cobrado'])?></td>
			<td align="center" style="background: #F2FEE9"><?php echo $util->nf($resumen[0]['Proveedor']['total_atraso_cobrado'])?></td>
			<td align="center" style="background: #F2FEE9"><strong><?php echo $util->nf($resumen[0]['Proveedor']['total_cobrado'])?></strong></td>
			<td align="center"><?php echo $util->nf($resumen[0]['Proveedor']['total_reversado'])?></td>
			<td align="center"><?php echo $util->nf($resumen[0]['Proveedor']['total_comision'])?></td>
			<td align="center" style="color:green;"><strong><?php echo $util->nf($resumen[0]['Proveedor']['neto_proveedor'])?></strong></td>
			<td align="center" style="background: #FBEAEA;"><?php echo $util->nf($resumen[0]['Proveedor']['adeudado_periodo'])?></td>
			<td align="center" style="background: #FBEAEA;"><?php echo $util->nf($resumen[0]['Proveedor']['adeudado_atraso'])?></td>
			<td align="center" style="background: #FBEAEA;"><?php echo $util->nf($resumen[0]['Proveedor']['adeudado'])?></td>
		</tr>
	</table>
	
	<?php if(!empty($recuperos)):?>
	
		<h3>DETALLE DE ORDENES DE RECUPERO EMITIDAS</h3>
	
		<table>
		
		<tr>
			<th>#</th>
			<th>DOCUMENTO</th>
			<th>APELLIDO Y NOMBRE</th>
			<th>CUOTA COBRADA</th>
			<th>IMPORTE</th>
			<th>COMISION</th>
			<th>ORDEN DE DESCUENTO EMITIDA AL SOCIO</th>
			<th><?php echo $controles->botonGenerico('/mutual/liquidacion_socios/recupero_cartera/'.$liquidacion['Liquidacion']['id'].'/'.$resumen[0]['Proveedor']['id'].'/?ANULAR=TODO','controles/user-trash-full.png',null,null,"ANULAR TODAS LAS ORDENES DE RECUPERO EMITIDAS?")?></th>
			
		</tr>
		<?php foreach($recuperos as $recupero):?>
			<?php 
				$origen = $recupero['LiquidacionCuotaRecupero']['orden_descuento_tipo_nro'] . " | CUOTA: " . $recupero['LiquidacionCuotaRecupero']['cuota'];
//				$origen .= " | IMPORTE: ".$util->nf($recupero['LiquidacionCuotaRecupero']['importe_liquidado'])."";

				$destino = $recupero['LiquidacionCuotaRecupero']['orden_descuento_emitida_tipo_nro'];
//				$destino.= " | " . $recupero['LiquidacionCuotaRecupero']['orden_descuento_emitida_producto'] . " [".$recupero['LiquidacionCuotaRecupero']['orden_descuento_emitida_proveedor']."] ";
				$destino.= " | CUOTAS: " . $recupero['LiquidacionCuotaRecupero']['orden_descuento_emitida_cuotas'];
				$destino.= " | IMPORTE: " . $util->nf($recupero['LiquidacionCuotaRecupero']['orden_descuento_emitida_importe_cuota']);
				$destino.= " | INICIA: " . $recupero['LiquidacionCuotaRecupero']['orden_descuento_emitida_periodo_d'];
				?>
			<tr>
				<td><?php echo $recupero['LiquidacionCuotaRecupero']['id']?></td>
<!--					<td><?php //   echo $html->link($recupero['LiquidacionCuotaRecupero']['socio_id'],'/mutual/orden_descuento_cuotas/estado_cuenta/'.$recupero['LiquidacionCuotaRecupero']['socio_id'],array("target"=>"blank"))?></td>-->
				<td><?php echo $recupero['LiquidacionCuotaRecupero']['persona_tdoc'] . " " . $recupero['LiquidacionCuotaRecupero']['persona_ndoc']?></td>
				<td><?php echo $html->link(utf8_encode($recupero['LiquidacionCuotaRecupero']['persona_apenom']),'/mutual/orden_descuento_cuotas/estado_cuenta/'.$recupero['LiquidacionCuotaRecupero']['socio_id'], array("target"=>"blank"))?></td>
				<td><?php echo $controles->ordenDescuentoPopPup($recupero['LiquidacionCuotaRecupero']['orden_descuento_recupera_id'],$recupero['LiquidacionCuotaRecupero']['socio_id'],$origen)?></td>
				<td align="right"><?php echo $util->nf($recupero['LiquidacionCuotaRecupero']['importe_liquidado'])?></td>
				<td align="right"><?php echo $util->nf($recupero['LiquidacionCuotaRecupero']['comision_cobranza'])?></td>
				<td><?php echo $controles->ordenDescuentoPopPup($recupero['LiquidacionCuotaRecupero']['orden_descuento_id'],$recupero['LiquidacionCuotaRecupero']['socio_id'],$destino)?></td>
				<td><?php echo $controles->botonGenerico('/mutual/liquidacion_socios/recupero_cartera/'.$liquidacion['Liquidacion']['id'].'/'.$resumen[0]['Proveedor']['id'].'/?ANULAR='.$recupero['LiquidacionCuotaRecupero']['id'],'controles/user-trash-full.png',null,null,"ANULAR LA ORDEN DE RECUPERO #" . $recupero['LiquidacionCuotaRecupero']['id'] . "?")?>
				<?php //   echo $frm->btnForm(array('URL'=>'/mutual/liquidacion_socios/recupero_cartera/'.$liquidacion['Liquidacion']['id'].'/'.$resumen[0]['Proveedor']['id'].'/?ANULAR='.$recupero['LiquidacionCuotaRecupero']['id'],'LABEL' => 'ANULAR'))?></td>
			</tr>
		
		<?php endforeach;?>
		
		</table>
	
	
	<?php endif;?>		
	
	
	<?php if(!empty($cuotas)):?>
	

		<h3>CUOTAS ADEUDADAS DEL PERIODO DE SOCIOS QUE NO REGISTRAN MORA DE PERIODOS ANTERIORES</h3>
	
		<script language="Javascript" type="text/javascript">

		var rows = <?php echo count($cuotas)?>;
		var checkAll = false;
		


		Event.observe(window, 'load', function(){

			$("btn_submit").disable();

		});		

		function checkUnCheckAll(rows){
			
			if(checkAll)checkAll = false;
			else checkAll = true;

			for (i = 0; i < rows; i++){
				
				objCHK = document.getElementById('LiquidacionCuota_' + i);
				if(!objCHK.disabled){
					if(checkAll)objCHK.checked = true;
					else objCHK.checked = false;
				}
				
			}
			
			chkOnclick();
			
		}

		
		function chkOnclick(){

			var totalSeleccionado = 0;

			for (i = 0; i < rows; i++){
				
				objCHK = document.getElementById('LiquidacionCuota_' + i);
				if(!objCHK.disabled)toggleCell("tr_LiquidacionCuota_" + i,objCHK);

				if (objCHK.checked && !objCHK.disabled){
					valSel = new Number(objCHK.value);
					valSel = valSel.toFixed(0);
					valSel = parseInt(valSel);
					totalSeleccionado = totalSeleccionado + valSel;					
				}
				
			}

			if(totalSeleccionado > 0) $("btn_submit").enable();
			else $("btn_submit").disable();
			
			totalSeleccionado = totalSeleccionado / 100;

			document.getElementById('LiquidacionCuotaImporteRecupero').value = totalSeleccionado;

			totalSeleccionado = FormatCurrency(totalSeleccionado);

			$('total_seleccionado').update(totalSeleccionado);
			
		}

		function confirmarForm(){
			impoRecu = new Number($("LiquidacionCuotaImporteRecupero").getValue());
			impoRecu = impoRecu.toFixed(2);
			msg = "ATENCION!!!\n\nSE EFECTUARAN LAS SIGUIENTES ACCIONES:\n\n";
			msg = msg + "1) GENERAR UN PAGO EN CONCEPTO DE *** " + getTextoSelect('LiquidacionCuotaRecuperoTipoCobro') + " ***";
			msg = msg + " A <?php echo $resumen[0]['Proveedor']['razon_social']?> POR EL IMPORTE DE $ " + impoRecu;
			msg = msg + "\n\n";
			msg = msg + "2) GENERAR AL SOCIO UNA ORDEN DE DESCUENTO ";
			msg = msg + "POR RECUPERO A PARTIR DE " + getStrPeriodo("LiquidacionCuotaRecuperoPeriodoSocio") + "\n";
			msg = msg + "A DESCONTAR EN " + $("LiquidacionCuotaRecuperoCantidadCuotas").getValue() + " CUOTA/S";
			msg = msg + "\n\n";
			msg = msg + "DESEA CONTINUAR?";
			return confirm(msg);
		}	

		</script>
		
		<?php echo $frm->create(null,array('id'=>'FormGenRecuperoCartera','onsubmit' => "return confirmarForm();",'action' => 'recupero_cartera/'.$liquidacion['Liquidacion']['id'].'/'.$resumen[0]['Proveedor']['id']))?>
		
	
		<table>
			<tr>
				<th>#SOCIO</th>
				<th>DOCUMENTO</th>
				<th>APELLIDO Y NOMBRE</th>
				<th>ORDEN DTO</th>
				<th>TIPO / NRO</th>
				<th>PERIODO</th>
				<th>CUOTA</th>
				<th>CONCEPTO</th>
				<th>IMPORTE</th>
				<th>LIQUIDADO</th>
				<th>SALDO ACTUAL</th>
				<th><?php echo $controles->btnCallJS("checkUnCheckAll(".count($cuotas).")","","controles/12-em-check.png")?></th>
			</tr>
			<?php $TOTAL_IMPORTE = $TOTAL_ACTUAL = $i = 0;?>
			<?php foreach($cuotas as $cuota):?>
				<tr id="tr_LiquidacionCuota_<?php echo $i?>">
					<td><?php echo $html->link($cuota['LiquidacionCuota']['socio_id'],'/mutual/orden_descuento_cuotas/estado_cuenta/'.$cuota['LiquidacionCuota']['socio_id'],array("target"=>"blank"))?></td>
					<td><?php echo $cuota['LiquidacionCuota']['persona_tdoc'] . " " . $cuota['LiquidacionCuota']['persona_ndoc']?></td>
					<td><?php echo $html->link(utf8_encode($cuota['LiquidacionCuota']['persona_apenom']),'/mutual/orden_descuento_cuotas/estado_cuenta/'.$cuota['LiquidacionCuota']['socio_id'], array("target"=>"blank"))?></td>
					<td align="center"><?php echo $controles->ordenDescuentoPopPup($cuota['LiquidacionCuota']['orden_descuento_id'],$cuota['LiquidacionCuota']['socio_id'])?></td>
					<td><?php echo $cuota['LiquidacionCuota']['orden_descuento_tipo_nro']?></td>
					<td><?php echo $cuota['LiquidacionCuota']['periodo_d']?></td>
					<td align="center"><?php echo $controles->ordenDescuentoCuotaPopPup($cuota['LiquidacionCuota']['orden_descuento_cuota_id'],$cuota['LiquidacionCuota']['cuota'])?></td>
					<td><?php echo $cuota['LiquidacionCuota']['producto_concepto']?></td>
					<td align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['importe'])?></td>
					<td align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['saldo_liquidado'])?></td>
					<td align="right"><?php echo ($cuota['LiquidacionCuota']['saldo_actual'] == 0 ? "<span style='color:red;'>".$util->nf($cuota['LiquidacionCuota']['saldo_actual'])."</span>" : $util->nf($cuota['LiquidacionCuota']['saldo_actual']))?></td>
					<td><input type="checkbox" name="data[LiquidacionCuota][id][<?php echo $cuota['LiquidacionCuota']['id']?>]" value="<?php echo number_format(round($cuota['LiquidacionCuota']['saldo_actual'],2) * 100,0,".","")?>" id="LiquidacionCuota_<?php echo $i?>" onclick="chkOnclick()" <?php echo ($cuota['LiquidacionCuota']['saldo_actual'] == 0 ? "disabled='disabled'" : "")?>/></td>
				</tr>
				<?php $TOTAL_IMPORTE += $cuota['LiquidacionCuota']['importe']?>
				<?php $TOTAL_ACTUAL += $cuota['LiquidacionCuota']['saldo_actual']?>
				<?php $i++;?>
			<?php endforeach;?>
			
			<tr class="totales">
				<th colspan="8">TOTAL (<?php echo count($cuotas)?> CUOTAS)</th>
				<th><?php echo $util->nf($TOTAL_IMPORTE)?></th>
				<th><?php echo $util->nf($TOTAL_ACTUAL)?></th>
				<th><?php echo $util->nf($TOTAL_ACTUAL)?></th>
				<th></th>
			</tr>
			<tr class="totales">
				<th colspan="10">SELECCIONADO</th>
				<th id="total_seleccionado">0.00</th>
				<th></th>
			</tr>
			
		
		</table>
		<div class="areaDatoForm">
		<table class="tbl_form">
		
			<tr>
				<td>FECHA COBRO</td><td><?php echo $frm->calendar("LiquidacionCuotaRecupero.fecha_cobro")?></td>
			</tr>
			<tr>
			<td>TIPO COBRO A EMITIR</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'LiquidacionCuotaRecupero.tipo_cobro',
																			'prefijo' => 'MUTUTCOB',
																			'disabled' => false,
																			'empty' => false,
																			'metodo' => "get_tipos_cobro_caja",
																			'selected' => "MUTUTCOBRECU"	
			))?>				
			</td>
			</tr>			
			<tr>
				<td>RECUPERAR A PARTIR DE</td><td><?php echo $frm->periodo("LiquidacionCuotaRecupero.periodo_socio")?></td>
			</tr>
			<tr>
				<td>CANTIDAD DE CUOTAS</td><td><?php echo $frm->number('LiquidacionCuotaRecupero.cantidad_cuotas',array('value' => 1))?></td>
			</tr>
					
		</table>
		<?php echo $frm->hidden('LiquidacionCuota.importe_recupero')?>
		<?php echo $frm->hidden('LiquidacionCuotaRecupero.tipo_producto_recupero',array('value' => "MUTUPROD0020"))?>
		<?php echo $frm->hidden('LiquidacionCuotaRecupero.liquidacion_id',array('value' => $liquidacion['Liquidacion']['id']))?>
		<?php echo $frm->hidden('LiquidacionCuotaRecupero.periodo_proveedor',array('value' => $liquidacion['Liquidacion']['periodo']))?>
		<?php echo $frm->hidden('LiquidacionCuotaRecupero.proveedor_id',array('value' => $resumen[0]['Proveedor']['id']))?>
		<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'PROCESAR','URL' => ( empty($fwrd) ? "/mutual/liquidacion_socios/recupero_cartera/".$liquidacion['Liquidacion']['id'] : $fwrd) ))?>
		</div>
	<?php else:?>
	
		<div class='notices_error' style="width: 100%">
		
			NO EXISTEN CUOTAS ADEUDADAS DEL PERIODO DE SOCIOS QUE NO REGISTRAN MORA DE PERIODOS ANTERIORES
		
		</div>
		<?php echo $frm->btnForm(array('LABEL' => 'CANCELAR', 'URL' => "/mutual/liquidacion_socios/recupero_cartera/".$liquidacion['Liquidacion']['id']))?>
			
	<?php endif;?>
	


<?php endif;?>
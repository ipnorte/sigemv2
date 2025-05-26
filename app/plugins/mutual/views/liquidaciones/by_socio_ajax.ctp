<?php if(count($liquidaciones)!=0):?>

	<?php if($periodo != $periodo_hasta):?>
		<table style="width: 100%;">
			<tr>
				<td align="right" style="background-color:#B7CEE2;font-weight:bold;">
					<?php echo $controles->botonGenerico('/mutual/liquidaciones/by_socio/'.$socio['Socio']['id'].'/0/1/'.$periodo.'/'.$periodo_hasta,'controles/pdf.png','IMPRIMIR TODOS LOS PERIODOS',array('target' => 'blank'))?>
				</td>
			</tr>
		</table>
		
	<?php endif;?>
	
	
	<?php foreach($liquidaciones as $periodo => $detalle):?>
		
		
		<table style="width: 100%;">
	
		<tr>
			<th colspan="14" style="color:#FFFFFF;font-weight: bold; font-size: 14px;text-align: left;">
				LIQUIDACION <?php echo $util->periodo($periodo,true)?>
			</th>
		</tr>
		<?php if(!empty($detalle['status'])):?>
			<tr>
				<td colspan="14" style="color:#FFFFFF;background:red; font-weight: bold; font-size: 12px;text-align: left;"><?php echo $detalle['status']?></td>
			</tr>
		<?php endif;?>
		<?php //   if($mostar_controles):?>
		<tr>
			<td colspan="8">
<!--				ESTADO DE LA LIQUIDACION: <?php //   echo ($detalle['cabecera_liquidacion'][0]['Liquidacion']['cerrada']==1 ? '<span style="color:red;"><strong>CERRADA</strong></span>' : '<span style="color:green;"><strong>ABIERTA</strong></span>')?>-->
<!--				&nbsp;|&nbsp; IMPUTADA : <?php //   echo ($detalle['cabecera_liquidacion'][0]['LiquidacionSocio']['imputada']==1 ? '<strong>SI</strong>' : '<strong>NO</strong>')?>-->
			</td>
			<td colspan="6" align="right">
				<?php if($mostar_controles):?>
					<?php //   if($detalle['cabecera_liquidacion'][0]['Liquidacion']['imputada'] == 0):?>
						<?php echo $controles->botonGenerico('/mutual/liquidacion_socios/reliquidar_by_socio/'.$socio['Socio']['id'].'/'.$periodo,'controles/calculator.png','RELIQUIDAR EL PERIODO',null,'*** RELIQUIDACION DEL PERIODO '.$util->periodo($periodo,true).' ***\n\nATENCION: Este proceso afecta solamente a las Liquidaciones ABIERTAS / NO IMPUTADAS del Socio y genera los siguientes eventos: \n\n1) Se recalcula la deuda y se generan todos los conceptos permanentes no devengados al periodo.  \n2) Se procesan los Archivos de Rendicion existentes vinculados la Liquidacion procesada.\n\nDesea Continuar?')?>
						&nbsp;|&nbsp;
					<?php //   endif;?>
				<?php endif;?>
				<?php echo $controles->botonGenerico('/mutual/liquidaciones/by_socio/'.$socio['Socio']['id'].'/0/1/'.$periodo,'controles/pdf.png','IMPRIMIR EL PERIODO',array('target' => 'blank'))?>
			</td>
		</tr>
		<?php //   endif;?>
		<tr>
			<td colspan="14" style="font-size:10px;font-weight: bold;font-family: verdana;color: #36393D;text-decoration: underline;">DETALLE DE LA LIQUIDACION</td>
		</tr>
		<tr>
			<td colspan="14">

				<table style="width: 100%;">
				
					<tr>
						<td colspan="14" style="font-size:10px;font-weight: bold;font-family: verdana;color: #993;text-decoration: underline;">CUOTAS LIQUIDADAS</td>
					</tr>				
				
					<tr>
						<th class="subtabla"></th>
						<th class="subtabla">ORD.DTO.</th>
						<th class="subtabla">LIQ - ORGANISMO</th>
						<th class="subtabla">TIPO / NUMERO</th>
						<th class="subtabla">PERIODO</th>
						<th class="subtabla">PROVEEDOR / PRODUCTO</th>
						<th class="subtabla">CUOTA</th>
						<th class="subtabla">VTO</th>
						<th class="subtabla" colspan="2">CONCEPTO</th>
						<th class="subtabla">IMPORTE</th>
						<th class="subtabla">LIQUIDADO</th>
						<th class="subtabla">IMPUTADO</th>
						<th class="subtabla"></th>
						<th class="subtabla">SALDO</th>
					</tr>						
					
					<?php 
					$style = "";
					$TOTAL_SALDO = 0;
					$TOTAL_ADEBITAR = 0;
					$TOTAL_DEBITADO = 0;
					$TOTAL_ATRASO_ADEBITAR = 0;
					$TOTAL_ATRASO_DEBITADO = 0;
					$TOTAL_PERIODO_ADEBITAR =0;
					$TOTAL_PERIODO_DEBITADO =0;
					
					$TOTAL_IMPORTE = $TOTAL_MORA_IMPORTE = $TOTAL_PERIODO_IMPORTE = 0;
					
					$TOTAL_SALDO_ATRASO = 0;
					$TOTAL_SALDO_PERIODO = 0;
					
					foreach($detalle['cuotas'] as $cuota):
						
//						debug($periodo ." --" . $cuota['LiquidacionCuota']['periodo_cuota']);
//					
//						debug($cuota);
						
						
						if($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo){
							$TOTAL_ATRASO_ADEBITAR += $cuota['LiquidacionCuota']['saldo_actual'];
							$TOTAL_ATRASO_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];
							$TOTAL_SALDO_ATRASO += $cuota['LiquidacionCuota']['saldo_actual'];
							$style = "background-color: #FBEAEA;";
							
							$TOTAL_MORA_IMPORTE += $cuota['LiquidacionCuota']['importe'];
							
						}else{
							$TOTAL_PERIODO_ADEBITAR += $cuota['LiquidacionCuota']['saldo_actual'];
							$TOTAL_PERIODO_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];
							$TOTAL_SALDO_PERIODO += $cuota['LiquidacionCuota']['saldo_actual'];				
							$style = "background-color: #F2FEE9;";
							
							$TOTAL_PERIODO_IMPORTE += $cuota['LiquidacionCuota']['importe'];
						}
						
						$TOTAL_ADEBITAR += $cuota['LiquidacionCuota']['saldo_actual'];
						$TOTAL_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];	
						$TOTAL_SALDO += $cuota['LiquidacionCuota']['saldo_actual'];
						
						$TOTAL_IMPORTE += $cuota['LiquidacionCuota']['importe'];
						
						$SALDO_CUOTA = $cuota[0]['saldo'];
						
//						debug($cuota['LiquidacionCuota']['importe']);
//						debug($TOTAL_ATRASO_ADEBITAR);
                                                
                                                
                                                
						
					?>
					
						<tr>
							<td style="<?php echo $style?>width:10px;" align="center"><?php echo $controles->btnModalBox(array('title' => 'DETALLE CUOTA','url' => '/mutual/orden_descuento_cuotas/view/'.$cuota['LiquidacionCuota']['orden_descuento_cuota_id'],'h' => 450, 'w' => 750))?></td>
							<td style="<?php echo $style?>" align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuento']['id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuento']['id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuento']['id'].'/'.$cuota['LiquidacionCuota']['socio_id'],'h' => 450, 'w' => 750))?></td>
							<td style="<?php echo $style?>" align="center">#<?php echo $cuota['Liquidacion']['id']?> - <?php echo $cuota['Organismo']['concepto_1']?></td>
							<td style="<?php echo $style?>"><?php echo $cuota[0]['tipo_nro']?></td>
							<td style="<?php echo $style?>" align="center"><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></td>
							<td style="<?php echo $style?>"><?php echo $cuota[0]['proveedor_producto']?></td>
							<td style="<?php echo $style?>" align="center"><?php echo $cuota[0]['cuota']?></td>
							<td style="<?php echo $style?>" align="center"><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></td>
							<td style="<?php echo $style?>" colspan="2"><?php echo $cuota['TipoCuota']['concepto_1']?></td>
							<td style="<?php echo $style?>" align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['importe'])?></td>
							<td style="<?php echo $style?>" align="right"><?php echo $util->nf($cuota['LiquidacionCuota']['saldo_actual'])?></td>
							<td style="<?php echo $style?>" align="right"><?php echo ($cuota['LiquidacionCuota']['importe_debitado']!= 0 ? $util->nf($cuota['LiquidacionCuota']['importe_debitado']) : '')?></td>
							<td style="<?php echo $style?>" align="center"><?php echo $controles->onOff($cuota['LiquidacionCuota']['imputada'],true)?></td>
							<td style="<?php echo $style?>" align="right">
								<?php if($SALDO_CUOTA != 0):?>
									<span style="color: red;"><strong><?php echo $util->nf($SALDO_CUOTA)?></strong></span>
								<?php else:?>
									<span style="color: green;"><strong><?php echo $util->nf($SALDO_CUOTA)?></strong></span>
								<?php endif;?>				
							
							</td>
						</tr>
						
					<?php endforeach;?>
					<?php if($TOTAL_ATRASO_ADEBITAR != 0):?>
						<tr>
							<td align="right" colspan="10"><strong>ATRASO</strong></td>
							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"><strong><?php echo $util->nf($TOTAL_MORA_IMPORTE)?></strong></td>
							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO_ATRASO)?></strong></td>
							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"><strong><?php echo $util->nf($TOTAL_ATRASO_DEBITADO)?></strong></td>
							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"></td>
							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"><strong><?php echo $util->nf($TOTAL_ATRASO_ADEBITAR - $TOTAL_ATRASO_DEBITADO)?></strong></td>
						</tr>
						<tr>
							<td align="right" colspan="10"><strong>PERIODO</strong></td>
							<td style="background-color: #F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_PERIODO_IMPORTE)?></strong></td>
							<td style="background-color: #F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO_PERIODO)?></strong></td>
							<td style="background-color: #F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_PERIODO_DEBITADO)?></strong></td>
							<td style="background-color: #F2FEE9;" align="right"></td>
							<td style="background-color: #F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_PERIODO_ADEBITAR - $TOTAL_PERIODO_DEBITADO)?></strong></td>
						</tr>
					<?php endif;?>
					<tr>
						<td align="right" colspan="10"><strong>SUB-TOTAL CUOTAS</strong></td>
						<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_IMPORTE)?></strong></td>
						<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO_ATRASO + $TOTAL_SALDO_PERIODO)?></strong></td>
						<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_ATRASO_DEBITADO + $TOTAL_PERIODO_DEBITADO)?></strong></td>
						<td style="border-top: 1px solid;" align="right"></td>
						<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO_ATRASO + $TOTAL_SALDO_PERIODO - $TOTAL_ATRASO_DEBITADO - $TOTAL_PERIODO_DEBITADO)?></strong></td>
					</tr>
					<?php $TOTAL_ADICIONAL = 0;?>
					<?php $TOTAL_ADICIONAL_DEBITADO = 0;?>
					<?php $SALDO_ADICIONAL = 0;?>
					<?php if(!empty($detalle['adicionales_pendientes'])):?>					
					
						<tr>
							<td colspan="14" style="font-size:10px;font-weight: bold;font-family: verdana;color: #993;text-decoration: underline;">CUOTAS ADICIONALES</td>
						</tr>
						<tr>
							<th class="subtabla">ORD.DTO.</th>
							<th class="subtabla">ORGANISMO</th>
							<th class="subtabla">TIPO / NUMERO</th>
							<th class="subtabla">PERIODO</th>
							<th class="subtabla">PROVEEDOR / PRODUCTO</th>
							<th class="subtabla">CONCEPTO</th>
							<th class="subtabla" colspan="5">FORMULA</th>
							<th class="subtabla">LIQUIDADO</th>
							<th class="subtabla">A DEBITAR</th>
							<th class="subtabla">IMPUTADO</th>
							<th class="subtabla">SALDO</th>
						</tr>						
						<?php 
						foreach($detalle['adicionales_pendientes'] as $adicional):
			//				debug($adicional);
							$TOTAL_ADICIONAL += $adicional['MutualAdicionalPendiente']['importe'];
							$TOTAL_ADICIONAL_DEBITADO += $adicional['LiquidacionCuota']['importe_debitado'];
							$SALDO_ADICIONAL = $adicional['MutualAdicionalPendiente']['importe'] - $adicional['LiquidacionCuota']['importe_debitado'];
						?>
						
							<tr>
								<td align="center"><?php echo $controles->linkModalBox($adicional['MutualAdicionalPendiente']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $adicional['MutualAdicionalPendiente']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$adicional['MutualAdicionalPendiente']['orden_descuento_id'].'/'.$adicional['MutualAdicionalPendiente']['socio_id'],'h' => 450, 'w' => 750))?></td>
								<td align="center"><?php echo $util->globalDato($adicional['MutualAdicionalPendiente']['codigo_organismo'])?></td>
								<td><?php echo $adicional['OrdenDescuento']['tipo_orden_dto']?> #<?php echo $adicional['OrdenDescuento']['numero']?></td>
								<td align="center"><?php echo $util->periodo($adicional['LiquidacionCuota']['periodo_cuota'])?></td>
								<td><?php echo $adicional['Proveedor']['razon_social_resumida'].' / '.$util->globalDato($adicional['LiquidacionCuota']['tipo_producto'])?></td>
								<td><?php echo $util->globalDato($adicional['MutualAdicionalPendiente']['tipo_cuota'])?></td>
								<td align="center" colspan="5">
								
									<?php if($adicional['MutualAdicionalPendiente']['deuda_calcula'] == 1):?>
										s/DEUDA TOTAL<br/>
									<?php elseif($adicional['MutualAdicionalPendiente']['deuda_calcula'] == 2):?>	
										s/DEUDA VENCIDA<br/>
									<?php elseif($adicional['MutualAdicionalPendiente']['deuda_calcula'] == 3):?>	
										s/DEUDA PERIODO<br/>							
									<?php endif;?>
									<?php if($adicional['MutualAdicionalPendiente']['tipo'] == 'P' && $adicional['MutualAdicionalPendiente']['deuda_calcula'] != 5):?>
										<?php echo '$ '.$adicional['MutualAdicionalPendiente']['total_deuda']?> x <?php echo $adicional['MutualAdicionalPendiente']['valor']?>% = <?php echo $adicional['MutualAdicionalPendiente']['importe']?>
									<?php elseif($adicional['MutualAdicionalPendiente']['deuda_calcula'] == 5):?>
                                                                                PUNITORIO <?php echo $adicional['MutualAdicionalPendiente']['valor']?>% MENSUAL s/DEUDA VENCIDA
									<?php elseif($adicional['MutualAdicionalPendiente']['deuda_calcula'] == 6):?>
										COSTO $<?php echo $adicional['MutualAdicionalPendiente']['valor']?> x <?php echo round($adicional['MutualAdicionalPendiente']['total_deuda'],0)?> REGISTRO/S DE DEBITO
									<?php else:?>        
										Fijo = <?php echo $adicional['MutualAdicionalPendiente']['valor']?>	
									<?php endif;?>
								
								
								</td>
								<td align="right"><?php echo $util->nf($adicional['MutualAdicionalPendiente']['importe'])?></td>
								<td align="right"><?php echo $util->nf($adicional['MutualAdicionalPendiente']['importe'])?></td>
								<td align="right"><?php echo $util->nf($adicional['LiquidacionCuota']['importe_debitado'])?></td>
								<td align="right">
									<?php if($SALDO_ADICIONAL != 0):?>
										<span style="color: red;"><strong><?php echo $util->nf($SALDO_ADICIONAL)?></strong></span>
									<?php else:?>
										<span style="color: green;"><strong><?php echo $util->nf($SALDO_ADICIONAL)?></strong></span>
									<?php endif;?>							
								</td>
							</tr>			
							
						<?php endforeach;?>
						
						<tr>
							<td align="right" colspan="11"><strong>ADICIONALES A DEVENGAR</strong></td>
							<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_ADICIONAL)?></strong></td>
							<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_ADICIONAL)?></strong></td>
							<td style="border-top: 1px solid;" align="right"><?php echo $util->nf($TOTAL_ADICIONAL_DEBITADO)?></td>
							<td style="border-top: 1px solid;" align="right"><?php echo $util->nf($TOTAL_ADICIONAL - $TOTAL_ADICIONAL_DEBITADO)?></td>
						</tr>						
															
					<?php endif;?>
					<?php $colorCeldaTotales = '#FFFF88';?>
					<tr>
						<td align="right" colspan="11" style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;"><strong>TOTAL LIQUIDADO <?php echo $util->periodo($periodo,true)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_ADEBITAR + $TOTAL_ADICIONAL)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO + $TOTAL_ADICIONAL)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_DEBITADO + $TOTAL_ADICIONAL_DEBITADO)?></strong></td>
<!--						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"></td>-->
						<!--<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;"></td>-->
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO + $TOTAL_ADICIONAL - $TOTAL_DEBITADO - $TOTAL_ADICIONAL_DEBITADO)?></strong></td>
					</tr>					
					
				</table>			
			
			</td>
		</tr>
		<tr>
			<td colspan="14">
				<table style="width: 100%;">
					<tr>
						<td colspan="13" style="font-size:10px;font-weight: bold;font-family: verdana;color: #36393D;text-decoration: underline;">
						DETALLE DE INFORMACION A ENVIAR PARA DESCUENTO
						</td>
						<td colspan="2" align="right">
						<?php 
						$codigo = substr($detalle['cabecera_liquidacion'][0]['Liquidacion']['codigo_organismo'],8,2);
						$imputada = $detalle['cabecera_liquidacion'][0]['Liquidacion']['imputada'];
						$archivos = $detalle['cabecera_liquidacion'][0]['Liquidacion']['archivos_procesados'];
						if($codigo == 22 && $imputada == 0 && $archivos == 0):
							echo $controles->botonGenerico('/mutual/liquidaciones/modificar_importes_dto/'.$socio['Socio']['id'].'/'.$detalle['cabecera_liquidacion'][0]['Liquidacion']['id'],'controles/edit.png','MODIFICAR IMPORTES');
						endif;
						?>						
						</td>						
					</tr>
					
					
					
					<tr>
						<th class="subtabla">#</th>
						<th class="subtabla">ORGANISMO</th>
						<th class="subtabla" colspan="9">IDENTIFICACION</th>
						<th class="subtabla">TURNO</th>
						<th class="subtabla">CRITERIO DE CALCULO</th>
						<th class="subtabla">LIQUIDADO</th>
						<th class="subtabla">A DEBITAR</th>
					</tr>		
					
					<?php 
					$TOTAL_LIQUIDACION = 0;
					$TOTAL_LIQUIDACION_ADEBITAR = 0;
					$SALDO_LIQUIDACION = 0;
					$TOTAL_LIQUIDACION_IMPUTADO = 0;
					$TOTAL_REINTEGRO = 0;
					$TOTAL_LIQUIDACION_DEBITADO = 0;
					foreach($detalle['cabecera_liquidacion'] as $liquidacion):

						$TOTAL_LIQUIDACION += $liquidacion['LiquidacionSocio']['importe_dto'];
						$TOTAL_LIQUIDACION_ADEBITAR += $liquidacion['LiquidacionSocio']['importe_adebitar'];
						$TOTAL_LIQUIDACION_IMPUTADO += $liquidacion['LiquidacionSocio']['importe_imputado'];
						$SALDO_LIQUIDACION = $TOTAL_LIQUIDACION - $TOTAL_LIQUIDACION_IMPUTADO;
						$TOTAL_REINTEGRO += $liquidacion['LiquidacionSocio']['importe_reintegro'];
						$TOTAL_LIQUIDACION_DEBITADO += $liquidacion['LiquidacionSocio']['importe_debitado'];
						$color = "";
//						if($liquidacion['LiquidacionSocio']['periodo'] == 0){
//							$color = "background-color: #FBEAEA;";
//						}else{
//							$color = "background-color: #F2FEE9;";
//						}
				
					?>
					<tr>
						<td style="<?php echo $color?>"><?php echo $liquidacion['LiquidacionSocio']['id']?></td>
						<td align="center" style="<?php echo $color?>" nowrap="nowrap"><?php echo $util->globalDato($liquidacion['LiquidacionSocio']['codigo_organismo'])?></td>
						<td colspan="9" style="<?php echo $color?>"><?php echo "#".$liquidacion['LiquidacionSocio']['persona_beneficio_id']." - ".$liquidacion['LiquidacionSocio']['beneficio_str'] . "  (".$liquidacion['LiquidacionSocio']['porcentaje']."%)"?></td>
						<td align="center" class="top" style="<?php echo $color?>"><?php echo $liquidacion['LiquidacionSocio']['turno']?></td>
						<td style="font-size:9px;<?php echo $color?>"><?php echo str_replace("\n","<br/>",$liquidacion['LiquidacionSocio']['formula_criterio_deuda'])?></td>
						<td align="right" style="<?php echo $color?>"><?php echo $util->nf($liquidacion['LiquidacionSocio']['importe_dto'])?></td>
						<td align="right" style="<?php echo $color?>"><?php echo $util->nf($liquidacion['LiquidacionSocio']['importe_adebitar'])?></td>
					</tr>			
					<?php endforeach;?>
					<tr>
						<td align="right" colspan="13" style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;"><strong>TOTAL A ENVIAR A DESCUENTO <?php echo $util->periodo($periodo,true)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_LIQUIDACION)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_LIQUIDACION_ADEBITAR)?></strong></td>
			<!--			<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_LIQUIDACION_DEBITADO)?></strong></td>-->
			<!--			<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_LIQUIDACION_IMPUTADO)?></strong></td>--><!--
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right">
						<strong>
						
								<?php if($TOTAL_REINTEGRO > 0):?>
									<span style="color: red;"><strong><?php echo $util->nf($TOTAL_REINTEGRO)?></strong></span>
								<?php else:?>
									<span style="color: green;"><strong><?php echo $util->nf($TOTAL_REINTEGRO)?></strong></span>
								<?php endif;?>				
						
							
						</strong>
						</td>
						-->
			<!--			<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($SALDO_LIQUIDACION)?></strong></td>-->
					</tr>
				</table>	
		</td>								
	</tr>
    <tr>
        <td colspan="14">
            <?php if (!empty($detalle['envios'])): ?>
    <table style="width: 100%;">
        <tr>
            <td colspan="10" style="font-size:10px;font-weight: bold;font-family: verdana;color: #36393D;text-decoration: underline;">
                DETALLE DE ORDENES DE DEBITO EMITIDAS
            </td>
        </tr>
        <tr>
            <th class="subtabla">ORGANISMO</th>
            <th class="subtabla">LOTE</th>
            <th class="subtabla">FECHA_DEBITO</th>
            <th class="subtabla">ARCHIVO</th>
            <th class="subtabla">USUARIO</th>
            <th class="subtabla">EMITIDO EL</th>
            <th class="subtabla">BANCO</th>
            <th class="subtabla">EXCLUIDO | MOTIVO</th>
            <th class="subtabla">IMPORTE</th>
        </tr>
        <?php 
        $ACUM_ENVIO = 0;           // Acumulador global
        $subtotal  = 0;            // Acumulador por lote
        $currentLote = null;       // Lote actual
        $totalEnvios = count($detalle['envios']);
        $i = 0;
        $fechaDebito = $banco = null;
        foreach ($detalle['envios'] as $envio):
            $i++;
            $loteActual = $envio['LiquidacionSocioEnvio']['id'];

            // Si se cambia de lote (y no es la primera iteración)
            if ($currentLote !== null && $loteActual != $currentLote):
                ?>
                <tr class="subtotal">
                    <td colspan="8" align="right" style="border: 1px solid #666666; background-color: #D8DBD4; font-weight: bold;">
                        LOTE #<?php echo $currentLote; ?> - <?php echo $fechaDebito; ?> - <?php echo $banco; ?>
                    </td>
                    <td align="right" style="border: 1px solid #666666; background-color: #D8DBD4; font-weight: bold;">
                        <?php echo $util->nf($subtotal); ?>
                    </td>
                </tr>
                <?php 
                // Reiniciamos el subtotal para el nuevo lote
                $subtotal = 0;
            endif;

            // Actualizamos el lote actual
            $currentLote = $loteActual;
            $fechaDebito = $util->armaFecha($envio['LiquidacionSocioEnvio']['fecha_debito']);
            $banco = $envio['LiquidacionSocioEnvio']['banco_nombre'];
            ?>
            <tr class="<?php echo ($envio['LiquidacionSocioEnvioRegistro']['excluido'] ? "activo_0" : ""); ?>">
                <td><?php echo $envio['Organismo']['concepto_1']; ?></td>
                <td align="center"><?php echo $loteActual; ?></td>
                <td align="center"><?php echo $util->armaFecha($envio['LiquidacionSocioEnvio']['fecha_debito']); ?></td>
                <td><?php echo $envio['LiquidacionSocioEnvio']['archivo']; ?></td>
                <td><?php echo $envio['LiquidacionSocioEnvio']['user_created']; ?></td>
                <td align="center"><?php echo $envio['LiquidacionSocioEnvio']['created']; ?></td>
                <td><?php echo $envio['LiquidacionSocioEnvio']['banco_nombre']; ?></td>
                <td>
                    <?php if ($envio['LiquidacionSocioEnvioRegistro']['excluido']): ?>
                        <span style="color: red; font-weight: bold;">
                            SI | <?php echo $envio['LiquidacionSocioEnvioRegistro']['motivo']; ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td align="right">
                    <?php echo $util->nf($envio['LiquidacionSocioEnvioRegistro']['importe_adebitar']); ?>
                </td>
            </tr>
            <?php 
            // Acumulamos el importe del lote y el total general
            $subtotal  += $envio['LiquidacionSocioEnvioRegistro']['importe_adebitar'];
            $ACUM_ENVIO += $envio['LiquidacionSocioEnvioRegistro']['importe_adebitar'];

            // Si es la última iteración, se imprime el subtotal final para el último lote
            if ($i == $totalEnvios):
                ?>
                <tr class="subtotal">
                    <td colspan="8" align="right" style="border: 1px solid #666666; background-color: #D8DBD4; font-weight: bold;">
                        LOTE <?php echo $loteActual; ?> - <?php echo $fechaDebito; ?> - <?php echo $banco; ?>
                    </td>
                    <td align="right" style="border: 1px solid #666666; background-color: #D8DBD4; font-weight: bold;">
                        #<?php echo $util->nf($subtotal); ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <tr class="totales">
            <td colspan="8" align="right" style="border: 1px solid #666666; background-color: <?php echo $colorCeldaTotales; ?>;">
                TOTAL ORDEN DEBITO EMITIDAS
            </td>
            <td align="right" style="border: 1px solid #666666; background-color: <?php echo $colorCeldaTotales; ?>;">
                <?php echo $util->nf($ACUM_ENVIO); ?>
            </td>
        </tr>
    </table>
<?php endif; ?>

        </td>
    </tr>
		<?php if(!empty($detalle['rendicion'])):?>
		<tr>
			<td colspan="14">
				<table style="width: 100%;">
					<tr>
						<td colspan="14" style="font-size:10px;font-weight: bold;font-family: verdana;color: #36393D;text-decoration: underline;">
						DETALLE DE LA RENDICION DE DATOS POR EL AGENTE DE RETENCION
						</td>
						
					</tr>
					<tr>
						<th class="subtabla">ORGANISMO</th>
						<th class="subtabla">IDENTIFICACION</th>
						<th class="subtabla">FECHA</th>
                                                <th class="subtabla">LOTE</th>
						<th colspan="6" class="subtabla">BANCO INTERCAMBIO</th>
						<th class="subtabla">CODIGO</th>
						<th class="subtabla">DESCRIPCION</th>
						<th class="subtabla">IMPORTE</th>
						<th class="subtabla">O.COBRO</th>
					
					</tr>
					<?php $TOTAL_COBRADO=0;?>
					<?php $TOTAL_NOCOBRADO=0;?>
					<?php foreach($detalle['rendicion'] as $rendicion):?>
						<?php 
							if($rendicion['LiquidacionSocioRendicion']['indica_pago'] == 1) $TOTAL_COBRADO += $rendicion['LiquidacionSocioRendicion']['importe_debitado'];
							else $TOTAL_NOCOBRADO += $rendicion['LiquidacionSocioRendicion']['importe_debitado'];
						?>
						<tr class="activo_<?php echo  $rendicion['LiquidacionSocioRendicion']['indica_pago']?>">
							<td align="center"><?php echo $rendicion['LiquidacionSocioRendicion']['organismo']?></td>
							<td><?php echo $rendicion['LiquidacionSocioRendicion']['identificacion']?></td>
							<td align="center"><?php echo $util->armaFecha($rendicion['LiquidacionSocioRendicion']['fecha_debito'])?></td>
                                                        <td align="center">#<?php echo $rendicion['LiquidacionSocioRendicion']['liquidacion_intercambio_id']?></td>
							<td colspan="6"><?php echo $rendicion['LiquidacionSocioRendicion']['banco_intercambio_desc']?></td>
							
							<td align="center"><?php echo $rendicion['LiquidacionSocioRendicion']['status']?></td>
							<td align="center"><?php echo $rendicion['LiquidacionSocioRendicion']['status_desc']?></td>
							<td align="right"><?php echo $util->nf($rendicion['LiquidacionSocioRendicion']['importe_debitado'])?></td>
							<td align="center"><?php echo ($rendicion['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] != 0 ? $controles->linkModalBox('#'.$rendicion['LiquidacionSocioRendicion']['orden_descuento_cobro_id'],array('title' => 'ORDEN DE COBRO #' . $liquidacion['LiquidacionSocio']['orden_descuento_cobro_id'],'url' => '/mutual/orden_descuento_cobros/view/'.$rendicion['LiquidacionSocioRendicion']['orden_descuento_cobro_id'],'h' => 450, 'w' => 750)) : '')?></td>
						</tr>
					<?php endforeach;?>	
					<tr class="totales">
						<th colspan="12">TOTAL NO COBRADO</th>
						<th><?php echo $util->nf($TOTAL_NOCOBRADO)?></th>
						<th></td>
					</tr>				
					<tr>
						<td align="right" colspan="12" style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;"><strong>RENDICION <?php echo $util->periodo($periodo,true)?> - TOTAL COBRADO</strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_COBRADO)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"></td>
					</tr>
					<?php if(!empty($detalle['intercambio_recibido'])):?>
					<tr>
						<td colspan="15" style="color:gray;">
						
							<strong>DETALLE DEL REGISTRO RECIBIDO</strong>
							<br/>
							<textarea rows="5" cols="190" readonly="readonly"><?php echo $detalle['intercambio_recibido']?></textarea>
						</td>
					</tr>					
					
					<?php endif;?>					
				</table>
			</td>			
		</tr>
		<?php endif;?>
		<?php if(!empty($detalle['reintegros'])):?>
			
			
		<tr>
			<td colspan="14">

				<table style="width: 100%;">
					
					<tr>
						<td colspan="14" style="font-size:10px;font-weight: bold;font-family: verdana;color: #36393D;text-decoration: underline;">DETALLE DE REINTEGROS EMITIDOS POR ESTA LIQUIDACION</td>
					</tr>
					
					<tr>
						<th>#</th>
						<th colspan="3">LIQUIDACION</th>
						<th colspan="3">TIPO REINTEGRO</th>
						<th>IMPORTE DEBITADO</th>
						<th>IMPORTE REINTEGRO</th>
						<th>IMPORTE APLICADO</th>
						<th>SALDO</th>
						<th colspan="3"></th>
					</tr>
					<?php $ACU_DEBITADO=0;?>
					<?php $ACU_IMPUTADO=0;?>
					<?php $ACU_REINTEGRO=0;?>
					<?php $ACU_SALDO=0;?>
					<?php foreach($detalle['reintegros'] as $reintegro):?>
						<?php //   debug($reintegro)?>
						<?php $ACU_DEBITADO += $reintegro['SocioReintegro']['importe_debitado'];?>
						<?php $ACU_IMPUTADO += $reintegro['SocioReintegro']['importe_reintegro'];?>
						<?php $ACU_REINTEGRO += $reintegro['SocioReintegro']['importe_aplicado'];?>
						<?php $ACU_SALDO += $reintegro['SocioReintegro']['saldo'];?>
					
						<tr class="<?php echo ($reintegro['SocioReintegro']['imputado_deuda'] == 1 ?  "activo_1" : "activo_0")?>">
							<td><?php echo $reintegro['SocioReintegro']['id']?></td>
							<td colspan="3"><?php echo $reintegro['SocioReintegro']['liquidacion_str']?></td>
							<td colspan="3"><strong><?php echo $reintegro['SocioReintegro']['tipo']?></strong></td>
							<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_debitado'])?></td>
							<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_reintegro'])?></td>
							<td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['importe_aplicado'])?></strong></td>
							<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['saldo'])?></td>
							<td align="center" colspan="3">
							
							<?php if($reintegro['SocioReintegro']['imputado_deuda'] == 1):?>
							<strong> 
							<?php echo ($reintegro['SocioReintegro']['orden_descuento_cobro_id'] != 0 ? $controles->linkModalBox('ORDEN COBRO #'.$reintegro['SocioReintegro']['orden_descuento_cobro_id'],array('title' => 'ORDEN DE COBRO #' . $reintegro['SocioReintegro']['orden_descuento_cobro_id'],'url' => '/mutual/orden_descuento_cobros/view/'.$reintegro['SocioReintegro']['orden_descuento_cobro_id'],'h' => 450, 'w' => 750)) : '')?>
							</strong>
							|
							<?php endif;?>
							<?php if($reintegro['SocioReintegro']['reintegrado'] == 1):?>
								<?php if($reintegro['SocioReintegro']['orden_pago_id'] > 0):?>
									<?php echo 'OPA - ' . $reintegro['SocioReintegro']['nro_orden_pago']?>
								<?php elseif(!empty($reintegro['SocioReintegro']['ordenes_pagos'])): ?>	
									 
									<?php foreach($reintegro['SocioReintegro']['ordenes_pagos'] as $ordenPago):?>
										<?php echo $ordenPago?> |
									<?php endforeach;?>
								<?php else: ?>
									<strong> <?php // echo ($reintegro['SocioReintegro']['socio_reintegro_pago_id'] != 0 ? $controles->linkModalBox('PAGO #'.$reintegro['SocioReintegro']['socio_reintegro_pago_id'],array('title' => 'PAGO #' . $reintegro['SocioReintegro']['socio_reintegro_pago_id'],'url' => '/pfyj/socio_reintegros/ver_detalle_pago/'.$reintegro['SocioReintegro']['socio_reintegro_pago_id'].'/'.$reintegro['SocioReintegro']['id'],'h' => 450, 'w' => 750)) : '')?></strong>
								<?php endif;?>
							<?php elseif(!empty($reintegro['SocioReintegro']['ordenes_pagos'])):?>	
								 
								<?php foreach($reintegro['SocioReintegro']['ordenes_pagos'] as $ordenPago):?>
									<?php echo $ordenPago?> |
								<?php endforeach;?>
							<?php endif;?>

							
							</td>
						</tr>					
					<?php endforeach;?>	
					
					<tr>
						<td align="right" colspan="7" style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;"><strong>TOTAL REINTEGROS <?php echo $util->periodo($periodo,true)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($ACU_DEBITADO)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($ACU_IMPUTADO)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($ACU_REINTEGRO)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($ACU_SALDO)?></strong></td>
						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"></td>
					</tr>									
					
				</table>
				
				<?php //   debug($detalle['reintegros'])?>

			</td>
		</tr>		
			
		<?php endif;?>	
		
		
		
		</table>
		
		<br/>
	
	<?php endforeach;?>	
	
	<?php if($periodo != $periodo_hasta):?>
		<table style="width: 100%;">
			<tr>
				<td align="right" style="background-color:#B7CEE2;font-weight:bold;">
					<?php echo $controles->botonGenerico('/mutual/liquidaciones/by_socio/'.$socio['Socio']['id'].'/0/1/'.$periodo.'/'.$periodo_hasta,'controles/pdf.png','IMPRIMIR TODOS LOS PERIODOS',array('target' => 'blank'))?>
				</td>
			</tr>
		</table>
		
	<?php endif;?>	


<?php else:?>

	<h4>NO EXISTEN LIQUIDACIONES GENERADAS</h4>	

<?php endif;?>
<div class="card">
    <div class="card-header"><strong>Liquidaci&oacute;n de Deuda</strong> | <strong><?php echo $persona['Persona']['tdoc_ndoc']?> - <?php echo $persona['Persona']['apenom']?></strong></div>
    <div class="card-body">
		<small>
            <div class="row mb-1 ">
                <div class="col-12">DOMICILIO: <strong><?php echo $persona['Persona']['domicilio']?></strong></div>
            </div>
            <div class="row mb-1 ">
                <div class="col-12">DATOS COMPLEMENTARIOS: <strong><?php echo $persona['Persona']['datos_complementarios']?></strong></div>
            </div>
            <?php if(!empty($persona['Persona']['socio_nro'])):?>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Socio</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Fecha de Alta</th>
                        <th>Ultima Calificación</th>
                        <th>Fecha Calificación</th>
                        <th>Calificaciones Anteriores</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $persona['Persona']['socio_nro']?></td>
                        <td><?php echo $persona['Persona']['socio_categoria']?></td>
                        <td><?php echo $persona['Persona']['socio_status']?></td>
                        <td><?php echo $util->armaFecha($persona['Persona']['socio_fecha_alta'])?></td>
                        <td><?php echo $persona['Persona']['socio_ultima_calificacion']?></td>
                        <td><?php echo $util->armaFecha($persona['Persona']['socio_fecha_ultima_calificacion'])?></td>
                        <td><?php echo $persona['Persona']['socio_resumen_calificacion']?></td>
                    </tr>
                </tbody>
            </table>
            <?php endif;?>
        </small>
		<?php if(!empty($persona['Persona']['id'])):?>
		<div class="row mb-1 ">
			<div class="col-12">
				<a href="<?php echo $this->base . "/ventas/solicitudes/estado_cuenta/" . $persona['Persona']['id']?>" class="btn btn-success">
					<i class="fas fa-address-book"></i>&nbsp;Estado de Deuda
				</a>
			</div>
		</div>
		<?php endif;?>
		
		<?php if(!empty($liquidaciones)):?>
<!-- 					<hr> -->
<!-- 					<h6>ULTIMA LIQUIDACION EMITIDA</h6> -->
                	<?php foreach($liquidaciones as $periodo => $detalle):?>
    				<div class="card mb-1">
                        <div class="card-header bg-secondary">
                        	Ultima Liquidaci&oacute;n emitida (S.E.U.O.):<strong> <?php echo $util->periodo($periodo,true)?> </strong>
                        </div>
                        <div class="card-body">
                        	<small>
                        	<table class="table table-sm" style="font-size: 80%;">
                        		<thead>
                					<tr>
                						<th>ORD.DTO.</th>
                						<th>ORGANISMO</th>
                						<th>NUMERO</th>
                						<th>PERIODO</th>
                						<th>PRODUCTO</th>
                						<th>CUOTA</th>
                						<th>CONCEPTO</th>
                						<th>IMPORTE</th>
                						<th>LIQUIDADO</th>
                						<th>IMPUTADO</th>
                						<th>SALDO</th>
                					</tr>                        		
                        		
                        		</thead>
                        		<tbody>
                        		
                        		<?php 
                        		$style = "";
                        		$TOTAL_SALDO = 0;
                        		$TOTAL_ADEBITAR = 0;
                        		$TOTAL_DEBITADO = 0;
                        		$TOTAL_ATRASO_ADEBITAR = 0;
                        		$TOTAL_ATRASO_DEBITADO = 0;
                        		$TOTAL_PERIODO_ADEBITAR =0;
                        		$TOTAL_PERIODO_DEBITADO =0;
                        		
                        		$TOTAL_SALDO_ATRASO = 0;
                        		$TOTAL_SALDO_PERIODO = 0;
                        		
                        		foreach($detalle['cuotas'] as $cuota):
                        		
                        		if($cuota['LiquidacionCuota']['periodo_cuota'] != $periodo){
                        		    $TOTAL_ATRASO_ADEBITAR += $cuota['LiquidacionCuota']['saldo_actual'];
                        		    $TOTAL_ATRASO_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];
                        		    $TOTAL_SALDO_ATRASO += $cuota['LiquidacionCuota']['saldo_actual'];
                        		    $style = "background-color: #FBEAEA;";
                        		}else{
                        		    $TOTAL_PERIODO_ADEBITAR += $cuota['LiquidacionCuota']['saldo_actual'];
                        		    $TOTAL_PERIODO_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];
                        		    $TOTAL_SALDO_PERIODO += $cuota['LiquidacionCuota']['saldo_actual'];
                        		    $style = "background-color: #F2FEE9;";
                        		}
                        		
                        		$TOTAL_ADEBITAR += $cuota['LiquidacionCuota']['saldo_actual'];
                        		$TOTAL_DEBITADO += $cuota['LiquidacionCuota']['importe_debitado'];
                        		$TOTAL_SALDO += $cuota['LiquidacionCuota']['saldo_actual'];
                        		
                        		$SALDO_CUOTA = $cuota[0]['saldo'];
                        		
                        		
                        		?>
                        		
                        			<tr style="<?php echo $style?>">
                        			
                        				<td><?php echo $cuota['OrdenDescuento']['id']?></td>
                        				<td>#<?php echo $cuota['Liquidacion']['id']?> - <?php echo $cuota['Organismo']['concepto_1']?></td>
                        				<td><?php echo $cuota[0]['tipo_nro']?></td>
                        				<td><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></td>
                        				<td><?php echo $cuota['TipoProducto']['concepto_1']?></td>
                        				<td><?php echo $cuota[0]['cuota']?></td>
                        				<td><?php echo $cuota['TipoCuota']['concepto_1']?></td>
                        				<td align="right"><?php echo number_format($cuota['LiquidacionCuota']['importe'],2)?></td>
                        				<td align="right"><?php echo number_format($cuota['LiquidacionCuota']['saldo_actual'],2)?></td>
                        				<td align="right"><?php echo number_format($cuota['LiquidacionCuota']['importe_debitado'],2)?></td>
                        				<td align="right"><?php echo number_format($cuota[0]['saldo'],2)?></td>
                        			</tr>
                        		
                        		<?php endforeach;?>
                        		<?php if($TOTAL_ATRASO_ADEBITAR != 0):?>
        						<tr>
        							<td align="right" colspan="7"><strong>ATRASO</strong></td>
        							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"><strong><?php echo $util->nf($TOTAL_ATRASO_ADEBITAR)?></strong></td>
        							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO_ATRASO)?></strong></td>
        							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"><strong><?php echo $util->nf($TOTAL_ATRASO_DEBITADO)?></strong></td>
        							<td style="border-top: 1px solid;background-color: #FBEAEA;" align="right"><strong><?php echo $util->nf($TOTAL_ATRASO_ADEBITAR - $TOTAL_ATRASO_DEBITADO)?></strong></td>
        						</tr>
        						<tr>
        							<td align="right" colspan="7"><strong>PERIODO</strong></td>
        							<td style="background-color: #F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_PERIODO_ADEBITAR)?></strong></td>
        							<td style="background-color: #F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO_PERIODO)?></strong></td>
        							<td style="background-color: #F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_PERIODO_DEBITADO)?></strong></td>
        							<td style="background-color: #F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_PERIODO_ADEBITAR - $TOTAL_PERIODO_DEBITADO)?></strong></td>
        						</tr>                        		
                        		<?php endif;?> 
            					<tr>
            						<td align="right" colspan="7"><strong>SUB-TOTAL CUOTAS</strong></td>
            						<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_ATRASO_ADEBITAR + $TOTAL_PERIODO_ADEBITAR)?></strong></td>
            						<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO_ATRASO + $TOTAL_SALDO_PERIODO)?></strong></td>
            						<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_ATRASO_DEBITADO + $TOTAL_PERIODO_DEBITADO)?></strong></td>
            						<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO_ATRASO + $TOTAL_SALDO_PERIODO - $TOTAL_ATRASO_DEBITADO - $TOTAL_PERIODO_DEBITADO)?></strong></td>
            					</tr>
            					<?php $TOTAL_ADICIONAL = 0;?>
            					<?php $TOTAL_ADICIONAL_DEBITADO = 0;?>
            					<?php $SALDO_ADICIONAL = 0;?>
            					<?php if(!empty($detalle['adicionales_pendientes'])):?>	 
            						
            						<tr>
            							<th colspan="11" >CARGOS ADICIONALES</th>
            						</tr>
                					<tr>
                						<th>ORD.DTO.</th>
                						<th>ORGANISMO</th>
                						<th>NUMERO</th>
                						<th>PERIODO</th>
                						<th>PRODUCTO</th>
                						<th>CUOTA</th>
                						<th>CONCEPTO</th>
                						<th>IMPORTE</th>
                						<th>LIQUIDADO</th>
                						<th>IMPUTADO</th>
                						<th>SALDO</th>
                					</tr>
            						<?php 
            						foreach($detalle['adicionales_pendientes'] as $adicional):
            							$TOTAL_ADICIONAL += $adicional['MutualAdicionalPendiente']['importe'];
            							$TOTAL_ADICIONAL_DEBITADO += $adicional['LiquidacionCuota']['importe_debitado'];
            							$SALDO_ADICIONAL = $adicional['MutualAdicionalPendiente']['importe'] - $adicional['LiquidacionCuota']['importe_debitado'];
            						?> 
            						
            						<tr style="font-size: 80%;">
            							<td><?php echo $adicional['MutualAdicionalPendiente']['orden_descuento_id']?></td>
            							<td><?php echo $util->globalDato($adicional['MutualAdicionalPendiente']['codigo_organismo'])?></td>
            							<td><?php echo $adicional['OrdenDescuento']['tipo_orden_dto']?> #<?php echo $adicional['OrdenDescuento']['numero']?></td>
            							<td><?php echo $util->periodo($adicional['LiquidacionCuota']['periodo_cuota'])?></td>
            							<td><?php echo $util->globalDato($adicional['LiquidacionCuota']['tipo_producto'])?></td>
            							<td><?php echo $util->globalDato($adicional['MutualAdicionalPendiente']['tipo_cuota'])?></td>
            							<td>
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
            							<td align="right"><?php echo number_format($adicional['MutualAdicionalPendiente']['importe'],2)?></td>
            							<td align="right"><?php echo number_format($adicional['MutualAdicionalPendiente']['importe'],2)?></td>
            							<td align="right"><?php echo number_format($adicional['LiquidacionCuota']['importe_debitado'],2)?></td>
            							<td align="right"><?php echo number_format($SALDO_ADICIONAL,2)?></td>
            						
            						</tr>
            						
            						               					        						            					
                        			<?php endforeach;?>
            						<tr>
            							<td align="right" colspan="7"><strong>CARGOS ADICIONALES A DEVENGAR</strong></td>
            							<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_ADICIONAL)?></strong></td>
            							<td style="border-top: 1px solid;" align="right"><strong><?php echo $util->nf($TOTAL_ADICIONAL)?></strong></td>
            							<td style="border-top: 1px solid;" align="right"><?php echo $util->nf($TOTAL_ADICIONAL_DEBITADO)?></td>
            							<td style="border-top: 1px solid;" align="right"><?php echo $util->nf($TOTAL_ADICIONAL - $TOTAL_ADICIONAL_DEBITADO)?></td>
            						</tr>                        			
                        					           					
                        		<?php endif;?>			                        		
                        		

            					<?php $colorCeldaTotales = '#FFFF88';?>
            					<tr>
            						<td align="right" colspan="7" style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;"><strong>TOTAL LIQUIDADO <?php echo $util->periodo($periodo,true)?></strong></td>
            						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_ADEBITAR + $TOTAL_ADICIONAL)?></strong></td>
            						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO + $TOTAL_ADICIONAL)?></strong></td>
            						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_DEBITADO + $TOTAL_ADICIONAL_DEBITADO)?></strong></td>
            						<td style="border: 1px solid #666666;background-color: <?php echo $colorCeldaTotales?>;" align="right"><strong><?php echo $util->nf($TOTAL_SALDO + $TOTAL_ADICIONAL - $TOTAL_DEBITADO - $TOTAL_ADICIONAL_DEBITADO)?></strong></td>
            					</tr>
            					<tr>
            					
            						<td colspan="11">
            						
            							 <div class="card mb-1">
                                            <div class="card-header bg-light">
                                            	<small>
                                                <button class="btn btn-light btn-sm" type="button" data-toggle="collapse" data-target="#detalleEnvioDescuento" aria-expanded="true" aria-controls="collapseOne">
                                                  <i class="fas fa-arrow-alt-circle-down"></i>&nbsp;Detalle Informaci&oacute;n a Enviar para Descuento
                                                </button>
                                                </small>                                            	
                                            </div>
                                            <div class="collapse" id="detalleEnvioDescuento">
                                            <div class="card-body">
                                            
											<table style="width: 100%;">
                        					
                        					<tr style="font-size: 80%">
                        						<th class="subtabla">#</th>
                        						<th class="subtabla">ORGANISMO</th>
                        						<th class="subtabla">IDENTIFICACION</th>
                        						<th class="subtabla">TURNO</th>
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
                        					?>
                        					<tr style="font-size: 80%">
                        						<td style="<?php echo $color?>"><?php echo $liquidacion['LiquidacionSocio']['id']?></td>
                        						<td align="center" style="<?php echo $color?>" nowrap="nowrap"><?php echo $util->globalDato($liquidacion['LiquidacionSocio']['codigo_organismo'])?></td>
                        						<td style="<?php echo $color?>"><?php echo "#".$liquidacion['LiquidacionSocio']['persona_beneficio_id']." - ".$liquidacion['LiquidacionSocio']['beneficio_str'] . "  (".$liquidacion['LiquidacionSocio']['porcentaje']."%)"?></td>
                        						<td align="center" class="top" style="<?php echo $color?>"><?php echo $liquidacion['LiquidacionSocio']['turno']?></td>
                        						<td align="right" style="<?php echo $color?>"><?php echo $util->nf($liquidacion['LiquidacionSocio']['importe_dto'])?></td>
                        						<td align="right" style="<?php echo $color?>"><?php echo $util->nf($liquidacion['LiquidacionSocio']['importe_adebitar'])?></td>
                        					</tr>			
                        					<?php endforeach;?>
                        					<tr>
                        						<td align="right" colspan="4" style="border-top: 1px solid #666666;"><strong>TOTAL A ENVIAR A DESCUENTO <?php echo $util->periodo($periodo,true)?></strong></td>
                        						<td style="border-top: 1px solid #666666;" align="right"><strong><?php echo $util->nf($TOTAL_LIQUIDACION)?></strong></td>
                        						<td style="border-top: 1px solid #666666;" align="right"><strong><?php echo $util->nf($TOTAL_LIQUIDACION_ADEBITAR)?></strong></td>
                        					</tr>
                        				</table>                                             
                                            
                                            </div>
                                            </div>
                                         </div>
            						
            						
                        				           						
            						
            						</td>
            					
            					</tr>
            					
            					<?php if(!empty($detalle['rendicion'])):?>
            					
            						<tr>
            						
            							<td colspan="11">
            							
            							 <div class="card mb-1">
                                            <div class="card-header bg-light">
                                            
                                            	<small>
                                                <button class="btn btn-light btn-sm" type="button" data-toggle="collapse" data-target="#detalleRendicionDescuento" aria-expanded="true" aria-controls="collapseOne">
                                                  <i class="fas fa-arrow-alt-circle-down"></i>&nbsp;Detalle Rendici&oacute;n de Datos por el Agente de Retenci&oacute;n
                                                </button>
                                                </small>                                              
                                            
                                            	
                                            </div>
                                            <div class="collapse" id="detalleRendicionDescuento">
                                            <div class="card-body">
                                            
                            				<table style="width: 100%;">
     
                            					<tr style="font-size: 80%">
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
                            						
                            						if($rendicion['LiquidacionSocioRendicion']['indica_pago'] == 1){
                            						    $TOTAL_COBRADO += $rendicion['LiquidacionSocioRendicion']['importe_debitado'];
                            						    $style = "background-color: #F2FEE9;";
                            						}else {
                            						    $TOTAL_NOCOBRADO += $rendicion['LiquidacionSocioRendicion']['importe_debitado'];
                            						    $style = "background-color: #FBEAEA;";
                            						}
                            						?>
                            						<tr style="<?php echo $style?>font-size: 80%">
                            							<td align="center"><?php echo $rendicion['LiquidacionSocioRendicion']['organismo']?></td>
                            							<td><?php echo $rendicion['LiquidacionSocioRendicion']['identificacion']?></td>
                            							<td align="center"><?php echo $util->armaFecha($rendicion['LiquidacionSocioRendicion']['fecha_debito'])?></td>
                                                        <td align="center">#<?php echo $rendicion['LiquidacionSocioRendicion']['liquidacion_intercambio_id']?></td>
                            							<td colspan="6"><?php echo $rendicion['LiquidacionSocioRendicion']['banco_intercambio_desc']?></td>
                            							<td align="center"><?php echo $rendicion['LiquidacionSocioRendicion']['status']?></td>
                            							<td align="center"><?php echo $rendicion['LiquidacionSocioRendicion']['status_desc']?></td>
                            							<td align="right"><?php echo $util->nf($rendicion['LiquidacionSocioRendicion']['importe_debitado'])?></td>
                            							<td align="center"><?php echo ($rendicion['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] != 0 ? $rendicion['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] : '')?></td>
                            						</tr>
                            					<?php endforeach;?>	
                            					<tr style="background-color: #FBEAEA;font-weight: bold;">
                            						<td colspan="12" align="right" style="border-top: 1px solid #666666;">TOTAL NO COBRADO</td>
                            						<td align="right" style="border-top: 1px solid #666666;"><?php echo number_format($TOTAL_NOCOBRADO,2)?></td>
                            						<td style="border-top: 1px solid #666666;"></td>
                            					</tr>				
                            					<tr>
                            						<td align="right" colspan="12" style="border-top: 1px solid #666666;background-color:#F2FEE9;"><strong>RENDICION <?php echo $util->periodo($periodo,true)?> - TOTAL COBRADO</strong></td>
                            						<td style="border-top: 1px solid #666666;background-color:#F2FEE9;" align="right"><strong><?php echo $util->nf($TOTAL_COBRADO)?></strong></td>
                            						<td style="border-top: 1px solid #666666;background-color:#F2FEE9;" align="right"></td>
                            					</tr>
                            				</table>                                             
                                            
                                            </div> 
                                            </div>
										 </div>          							
            							
            							
           							
            							
            							</td>
            						
            						</tr>
            					
            					<?php endif;?> 
            					
            					
            					</tbody>                        		
                        	
                        	</table>
                        	</small>
                        </div>
                	</div>
                	<?php endforeach;?>
		<?php endif;?>                
    </div>
</div>
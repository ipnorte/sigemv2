<?php if(!empty($pago)):?>
	<h1>DETALLE DE LA ORDEN DE COBRO #<?php echo $pago['OrdenDescuentoCobro']['id'] ." ". ($pago['OrdenDescuentoCobro']['anulado']==1 ? " *** ANULADO *** " : "")?></h1>
	<?php if($pago['OrdenDescuentoCobro']['socio_id'] != 0):?>
	<?php echo $this->renderElement('socios/apenom',array('socio_id' => $pago['OrdenDescuentoCobro']['socio_id'], 'plugin' => 'pfyj'))?>
	<?php endif;?>
	
	<div class="areaDatoForm3">
		<?php if($pago['OrdenDescuentoCobro']['anulado']==1):?>
			<span style="color:red;font-weight: bold;"> *** ANULADO ***</span>
		<?php endif;?>
		<table>
			<tr>
				<th></th>
				<th>#</th>
				<th>TIPO COBRO</th>
				<th>FECHA PAGO</th>
				<th>PERIODO PAGO</th>
				<th>RECIBO</th>
				<th>OBSERVACIONES</th>
				<th>TOTAL</th>
				<th>USUARIO</th>
			</tr>
			<tr>
				<td><?php echo $controles->botonGenerico('/mutual/orden_descuento_cobros/view/'.$pago['OrdenDescuentoCobro']['id'].'/1','controles/pdf.png')?></td>
				<td align="center"><?php echo $pago['OrdenDescuentoCobro']['id']?></td>
				<td nowrap="nowrap"><?php echo $this->requestAction('/config/global_datos/valor/'.$pago['OrdenDescuentoCobro']['tipo_cobro'])?></td>
				<td align="center"><?php echo $util->armaFecha($pago['OrdenDescuentoCobro']['fecha'])?></td>
				<td align="center"><?php echo (!empty($pago['OrdenDescuentoCobro']['periodo_cobro']) ? $util->periodo($pago['OrdenDescuentoCobro']['periodo_cobro']) : 'S/D')?></td>
				<td align="center"><?php echo (!empty($pago['OrdenDescuentoCobro']['Recibo']) ? $pago['OrdenDescuentoCobro']['Recibo']['numero_string'] : "")?></td>
				<td><?php echo (!empty($pago['OrdenDescuentoCobro']['Recibo']) ? $pago['OrdenDescuentoCobro']['Recibo']['comentarios'] : "")?></td>
				<td align="right"><strong><?php echo number_format($pago['OrdenDescuentoCobro']['importe'],2)?></strong></td>
				<td align="center"><?php echo $pago['OrdenDescuentoCobro']['user_created']?> - <?php echo $pago['OrdenDescuentoCobro']['created']?></td>
			</tr>
		</table>
                        
                        <div class="areaDatoForm">
                        <?php if(!empty($pago['OrdenDescuentoCobro']['debito_cbu'])):?>
                            <h4>INFORMACION DEL DEBITO BANCARIO ASOCIADO AL COBRO</h4>
                            <table>
                                <tr>
                                    <th>BANCO INTERCAMBIO</th>
                                    <th>SUCURSAL</th>
                                    <th>CUENTA</th>
                                    <th>CBU</th>
                                    <th>FECHA DEBITO</th>
                                    <th>IMPORTE</th>
                                </tr>
                                <?php foreach($pago['OrdenDescuentoCobro']['debito_cbu'] as $debito):?>
                                <tr>
                                    <td><?php echo $debito['banco']?></td>
                                    <td><?php echo $debito['sucursal']?></td>
                                    <td><?php echo $debito['nro_cta_bco']?></td>
                                    <td><?php echo $debito['cbu']?></td>
                                    <td style="text-align: center;"><?php echo $util->armaFecha($debito['fecha_debito'])?></td>
                                    <td style="text-align: right;"><?php echo number_format($debito['importe_debito'],2)?></td>
                                </tr>
                                <?php endforeach;?>
                            </table>
                        <?php endif;?>                            
                        </div>        
                        
                        
		<h3>DETALLE DE CUOTAS COBRADAS</h3>
		<table>
			<tr>
				<th>PERIODO</th>
				<th>ORD.DTO.</th>
				<th>TIPO / NUMERO</th>
				<th>PROVEEDOR / PRODUCTO</th>
				<th>CUOTA</th>
				<th>CONCEPTO</th>
				<th>COBRADO</th>
<!--				<th>SALDO</th>-->
				<th></th>
			</tr>
			<?php $ACU_TOTAL_CUOTA = 0;?>
			<?php foreach($pago['OrdenDescuentoCobroCuota'] as $cuota):?>
				<?php $saldoCuota = $cuota['OrdenDescuentoCuota']['importe'] - $cuota['importe'];?>
				<?php $ACU_TOTAL_CUOTA += $cuota['importe'];?>
				<tr class="<?php echo ($cuota['reversado'] == 1 ? "activo_0" : "")?>">
					<td>
						<?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?>
					</td>
					<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuentoCuota']['orden_descuento_id'].'/'.$cuota['OrdenDescuentoCuota']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
					<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
					<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
					<td align="right"><?php echo $util->nf($cuota['importe'])?></td>
<!--					<td align="right"><?php echo $util->nf($saldoCuota)?></td>-->
					<td align="center"><?php echo ($cuota['reversado'] == 1 ? "<span style='background-color:red;color:white;padding:2px;font-weight:bold;'>R</span>":"")?></td>
				</tr>
			<?php endforeach;?>	
			<tr>
				<th colspan="6" class="totales">TOTAL COBRADO</th>
				<th class="totales"><?php echo $util->nf($ACU_TOTAL_CUOTA)?></th>
<!--				<th class="totales"></th>-->
				<th class="totales"></th>
			</tr>
		</table>
	</div>

        
<?php endif;?>	

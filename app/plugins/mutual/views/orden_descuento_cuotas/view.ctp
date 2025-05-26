<?php echo $this->renderElement('socios/apenom',array('socio_id' => $cuota['OrdenDescuentoCuota']['socio_id'], 'plugin' => 'pfyj'))?>
<h2>DETALLE DE LA CUOTA</h2>
<div class="areaDatoForm3">
	
	<table>
		<tr>
			<td>ORDEN DTO</td><td><strong><?php echo $controles->ordenDescuentoPopPup($cuota['OrdenDescuento']['id'],$cuota['OrdenDescuento']['socio_id'])?></strong></td>
		</tr>
		<tr>
			<td>TIPO NUMERO</td><td><strong><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></strong></td>		
		</tr>
		<tr>
			<td>PRODUCTO</td>
			<td><strong><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></strong></td>
		</tr>
		<tr>
			<td>CUOTA</td>
			<td><strong><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></strong></td>
		</tr>
		<tr>	
			<td>PERIODO</td>
			<td><strong><?php echo $cuota['OrdenDescuentoCuota']['periodo_d']?></strong></td>
		</tr>		
		<tr>	
			<td>CONCEPTO</td>
			<td><strong><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></strong></td>
		</tr>
		<tr>	
			<td>VENCIMIENTO</td>
			<td><strong><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento'])?></strong></td>
		</tr>
		<tr>	
			<td>ESTADO</td>
			<td><strong><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></strong></td>
		</tr>
		<tr>	
			<td>SITUACION</td>
			<td><strong><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></strong></td>
		</tr>
		<tr>	
			<td>IMPORTE</td>
			<td><strong><?php echo number_format($cuota['OrdenDescuentoCuota']['importe'],2)?></strong></td>
		</tr>
		<tr>	
			<td>PAGADO</td>
			<td><strong><?php echo number_format($cuota['OrdenDescuentoCuota']['pagado'],2)?></strong></td>
		</tr>
		<tr>	
			<td>SALDO ACTUAL</td>
			<td>
                            <strong><?php echo number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)?></strong>
                            <?php if($cuota['OrdenDescuentoCuota']['pre_imputado'] != "0"):?>
                            <span style="color: red;">PREIMPUTADO ** <?php echo number_format($cuota['OrdenDescuentoCuota']['pre_imputado'],2)?> ***</span>
                            <?php endif;?>
                            <?php if($cuota['OrdenDescuentoCuota']['importe_en_cancelacion'] != "0"):?>
                            <span style="color: red;">CANCELACION PENDIENTE** <?php echo number_format($cuota['OrdenDescuentoCuota']['importe_en_cancelacion'],2)?> ***</span>
                            <?php endif;?>                             
                        </td>
		</tr>
		<tr>	
			<td>BENEFICIO</td>
			<td>#<?php echo $cuota['OrdenDescuentoCuota']['persona_beneficio_id']?> <strong><?php echo $cuota['OrdenDescuentoCuota']['organismo']?></strong> - <strong><?php echo $cuota['OrdenDescuentoCuota']['beneficio']?></strong></td>
		</tr>
		<?php if(!empty($cuota['OrdenDescuentoCuota']['observaciones'])):?>
		<tr>
			<td>OBSERVACIONES</td>
			<td>
				<div class="areaDatoForm2" style="font-size: 10px;">
					<?php echo $cuota['OrdenDescuentoCuota']['observaciones']?>
				</div>			
			</td>
		</tr>
		<?php endif;?>
		<tr><td>Created:</td><td><?php if(!empty($cuota['OrdenDescuentoCuota']['user_created'])){echo $cuota['OrdenDescuentoCuota']['user_created'] . ' | ' . $cuota['OrdenDescuentoCuota']['created'];}?></td></tr>
		<tr><td>Modified:</td><td><?php if(!empty($cuota['OrdenDescuentoCuota']['user_modified'])){echo $cuota['OrdenDescuentoCuota']['user_modified'] . ' | ' . $cuota['OrdenDescuentoCuota']['modified'];} ?></td></tr>																
	</table>


    
<?php if(!empty($cuota['OrdenDescuentoCuota']['detalle_items'])):?>
    <div class="areaDatoForm2">
        <div class="row">
            CAPITAL: <?php echo $util->nf($cuota['OrdenDescuentoCuota']['detalle_items']['CAPITAL'])?>
            <?php if(floatval($cuota['OrdenDescuentoCuota']['detalle_items']['INTERES'])!= 0):?>
            &nbsp;INTERES:  <?php echo $util->nf($cuota['OrdenDescuentoCuota']['detalle_items']['INTERES'])?>
            <?php endif;?>
            <?php if(floatval($cuota['OrdenDescuentoCuota']['detalle_items']['ADICIONAL'])!= 0):?>
            &nbsp;GASTO ADM.:  <?php echo $util->nf($cuota['OrdenDescuentoCuota']['detalle_items']['ADICIONAL'])?>
            <?php endif;?>
            <?php if(floatval($cuota['OrdenDescuentoCuota']['detalle_items']['SELLADO'])!= 0):?>
            &nbsp;SELLADO:  <?php echo $util->nf($cuota['OrdenDescuentoCuota']['detalle_items']['SELLADO'])?>
            <?php endif;?>
            <?php if(floatval($cuota['OrdenDescuentoCuota']['detalle_items']['IVA'])!= 0):?>
            &nbsp;IVA:  <?php echo $util->nf($cuota['OrdenDescuentoCuota']['detalle_items']['IVA'])?>
            <?php endif;?>
        </div>
        <?php // debug($cuota['OrdenDescuentoCuota']['detalle_items']);?>
    </div>
    
<?php endif;?>    


</div>
<?php if(count($cuota['OrdenDescuentoCobroCuota'])!=0):?>
	<div class="areaDatoForm3">
		<h3>INFORMACION DEL COBRO</h3>
		<table>
			<tr>
				<th>#</th>
				<th>TIPO COBRO</th>
				<th>FECHA PAGO</th>
				<th>PERIODO PAGO</th>
				<th>RECIBO Nro</th>
				<th>TOTAL</th>
				<th>ORD.CANC.</th>
				<th></th>
				<th></th>
			</tr>
			<?php foreach($cuota['OrdenDescuentoCobroCuota'] as $pago):?>
				<?php //   debug($pago)?>
				<tr class="<?php echo ($pago['reversado'] == 1 ? "activo_0" : "")?>">
					<!--<td align="center"><?php echo $pago['OrdenDescuentoCobro']['id']?></td>-->
                                        <td align="center"><?php echo $controles->linkModalBox($pago['OrdenDescuentoCobro']['id'],array('title' => 'ORDEN DE COBRO #' . $pago['OrdenDescuentoCobro']['id'],'url' => '/mutual/orden_descuento_cobros/view/'.$pago['OrdenDescuentoCobro']['id'],'h' => 450, 'w' => 750))?></td>
<!--					<td align="center"><?php //   echo $controles->linkModalBox('#'.$pago['OrdenDescuentoCobro']['id'],array('title' => 'ORDEN DE COBRO #' . $pago['OrdenDescuentoCobro']['id'],'url' => '/mutual/orden_descuento_cobros/view/'.$pago['OrdenDescuentoCobro']['id'],'h' => 450, 'w' => 750))?></td>-->
					<td nowrap="nowrap"><?php echo $this->requestAction('/config/global_datos/valor/'.$pago['OrdenDescuentoCobro']['tipo_cobro'])?></td>
					<td align="center"><?php echo $util->armaFecha($pago['OrdenDescuentoCobro']['fecha'])?></td>
					<td align="center"><?php echo (!empty($pago['OrdenDescuentoCobro']['periodo_cobro']) ? $util->periodo($pago['OrdenDescuentoCobro']['periodo_cobro']) : 'S/D')?></td>
					<td><?php echo $pago['OrdenDescuentoCobro']['nro_recibo']?></td>
					<td align="right"><strong><?php echo number_format($pago['importe'],2)?></strong></td>
					<td align="center"><strong><?php echo ($pago['OrdenDescuentoCobro']['cancelacion_orden_id'] != 0 ? $controles->linkModalBox('#'.$pago['OrdenDescuentoCobro']['cancelacion_orden_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$pago['OrdenDescuentoCobro']['cancelacion_orden_id'],'h' => 450, 'w' => 750)) :  '')?></strong></td>
					<td align="center"><?php echo ($pago['reversado'] == 1 ? "<span style='background-color:red;color:white;padding:2px;font-weight:bold;'>R</span>":"")?></td>
					<td align="center">
						<?php if($pago['OrdenDescuentoCobro']['anulado']==1):?>
							<span style="color:red;font-weight: bold;"> *** ANULADO ***</span>
						<?php endif;?>	
					</td>
				</tr>
	
			<?php endforeach;?>
		</table>
	</div>
<?php endif;?>




<?php if(!empty($cuota['CancelacionOrden'])):?>
<div class="areaDatoForm3">
    <h3>ORDENES DE CANCELACION :: EMITIDAS / PENDIENTES</h3>
    <table>
        <tr>
            <th>#</th>
            <th>TIPO</th>
            <th>ORDEN</th>
            <th>TIPO / NUMERO</th>
            <th>PROVEEDOR / PRODUCTO</th>
            <th>CUOTAS</th>
            <th>A LA ORDEN DE</th>
            <th>CONCEPTO</th>
            <th>IMPORTE SELECCIONADO</th>
            <th>VENCIMIENTO</th>
        </tr>
        <?php foreach($cuota['CancelacionOrden'] as $cancelacion):?>
            <tr>
                <td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
                <td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']." (".$cancelacion['CancelacionOrden']['origen'].")"?></td>
                <td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_dto_cancela_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_dto_cancela_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_dto_cancela_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
                <td><?php echo $cancelacion['CancelacionOrden']['orden_dto_cancela_tipo_nro']?></td>
                <td><?php echo $cancelacion['CancelacionOrden']['orden_dto_cancela_proveedor_producto']?></td>
                <td><?php echo $cancelacion['CancelacionOrden']['cuotas_str']?></td>
                <td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
                <td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
                <td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
                <td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>

            </tr>
        <?php endforeach;?>
    </table>
</div>

<?php // debug($cuota['CancelacionOrden'])?>
<?php endif;?>


<?php if(!empty($cuota['LiquidacionCuota'])):?>

<div class="areaDatoForm3">
	<h3>LIQUIDACION DE DEUDA :: INFORMACION DE DESCUENTOS</h3>
	<table>
		<tr>
			<th>INFORMADA EN</th>
			<th>FECHA LIQUIDACION</th>
			<th>ORGANISMO</th>
			<th>ESTADO LIQUIDACION</th>
			<th>A DEBITAR</th>
<!--			<th>DEBITADO</th>-->
		</tr>
		<?php foreach($cuota['LiquidacionCuota'] as $liquidacion):?>
			<tr>
				<td><?php echo $util->periodo($liquidacion['Liquidacion']['periodo'],true)?></td>
				<td><?php echo $liquidacion['Liquidacion']['created']?></td>
				<td><?php echo $util->globalDato($liquidacion['Liquidacion']['codigo_organismo'])?></td>
				<td align="center"><?php echo ($liquidacion['Liquidacion']['cerrada'] == 0 ? 'ABIERTA' : 'CERRADA')?></td>
				<td align="right"><?php echo $util->nf($liquidacion['saldo_actual'])?></td>
<!--				<td align="right"><?php echo $util->nf($liquidacion['importe_debitado'])?></td>-->
			</tr>
			<?php //   debug($liquidacion)?>
		<?php endforeach;?>
	</table>
</div>
<?php endif;?>



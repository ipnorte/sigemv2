
<?php if(!empty($cuotas)):?>

	<table style="width: 100%;" class="tbl_grilla">
	
		<tr>
			<?php if(!empty($proveedor_razon_social)):?>
				<td>PROVEEDOR:</td>
				<td colspan="4"><strong><?php echo $proveedor_razon_social?></strong></td>
			<?php else:?>
				<td colspan="5"></td>
			<?php endif;?>
			<td colspan="11" align="right"><?php echo $controles->botonGenerico('/mutual/orden_descuento_cuotas/estado_cuenta_pdf2/'.$socio['Socio']['id'].'/'.$periodo_d.'/'.$periodo_h."/".($solo_deuda ? 1 : 0).'/'.$proveedor_id.'/'.$codigo_organismo.'/'.($discrimina_pagos ? 1 : 0).'/'.$tipo_producto,'controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?></td>
		</tr>	
		
		<tr>
			<th>ORD.DTO.</th>
			<th>ORGANISMO</th>
			<th>TIPO / NUMERO</th>
			<th>COD - NRO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>SOLICITADO</th>
                        <th>CUOTA</th>
			<th>CONCEPTO</th>
			<th>VTO / PAGO</th>
			<th></th>
			<th>SIT</th>
			<th>IMPORTE</th>
			<th>PAGADO</th>
            <th>PREIMP.</th>
			<th>SALDO</th>
			<th></th>
			
		</tr>
        
        <?php $periodo = null?>
        <?php $primero = true;?>
        <?php $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = 0?>
        <?php foreach($cuotas as $cuota):?>

        <?php if($cuota['tipo_registro'] == 'SALDO_ANTERIOR'):?>
            <?php $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = $cuota['saldo_conciliado'];?>                                 
        <?php endif;?> 
        
        
        <?php if($periodo != $cuota['periodo']):?>
            <?php $periodo = $cuota['periodo'];?>
            <?php if($primero):?>
                <?php $primero = false;?>
            <?php else:?>
        
                <tr>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="11" align="right"><strong>TOTAL PERIODO</strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="14" align="right"><strong>SALDO ACUMULADO A <?php echo $util->periodo($periodo_actual,true)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                </tr>
                
            <?php endif;?>
        
            <tr>
                <th colspan="16" style="font-size:13px;background-color: #666666;border:0"><h4 style="text-align: left;color:#FFFFFF;"><?php echo $util->periodo($cuota['periodo'],true)?></h4></th>
            </tr> 
			<?php if($ACU_SALDO_CUOTA_ACUM != 0):?>
				<tr>
					<td style="border-bottom: 1px solid #D8DBD4;color:red;" colspan="14" align="right"><strong>SALDO ANTERIOR</strong></td>
					<td style="border-bottom: 1px solid #D8DBD4;color:red;background-color: #FBEAEA;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
					<td style="border-bottom: 1px solid #D8DBD4;color:red;" align="right"><?php echo $controles->btnModalBox(array('title' => 'ATRASO A '.$util->periodo($cuota['periodo'],true),'url' => '/mutual/orden_descuento_cuotas/ver_atraso/'.$socio['Socio']['id'].'/'.$cuota['periodo'].'/'.$proveedor_id.'/'.$codigo_organismo,'h' => 500, 'w' => 900))?></td>
				</tr>				
							
			<?php endif;?>    
                
            <?php 
            $periodo_actual = $cuota['periodo'];
            $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = 0;
            ?>    
        <?php endif;?>
        
                       
                                
         <?php if($cuota['tipo_registro'] == 'CUOTA'):?>       
            <?php $ACU_IMPO_CUOTA += $cuota['importe'];?>
            <?php $ACU_SALDO_CUOTA += $cuota['saldo_conciliado'];?>
            <?php $ACU_SALDO_CUOTA_ACUM += $cuota['saldo_conciliado'];?>                
                
            <tr class="<?php echo $cuota['estado']?>">
                <td align="center"><?php echo $controles->linkModalBox($cuota['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['orden_descuento_id'].'/'.$cuota['socio_id'],'h' => 450, 'w' => 750))?></td>
                <td><?php echo $cuota['organismo']?></td>
                <td nowrap="nowrap">
                <?php
                    switch ($cuota['tipo_orden_dto']){
                        case 'OCOMP':
                            echo $controles->linkModalBox($cuota['tipo_numero'],array('title' => 'ORDEN DE CONSUMO / SERVICIO #' . $cuota['numero'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$cuota['numero'],'h' => 450, 'w' => 850));
                            break;								
                        case 'CONVE':
                            echo $controles->linkModalBox($cuota['tipo_numero'],array('title' => 'CONVENIO DE PAGO #' . $cuota['numero'],'url' => '/pfyj/socio_convenios/view/'.$cuota['numero'],'h' => 450, 'w' => 850));
                            break;
                        case Configure::read('APLICACION.tipo_orden_dto_credito'):
                            echo $controles->linkModalBox($cuota['tipo_numero'],array('title' => 'SOLICITUD DE CREDITO #' . $cuota['numero'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$cuota['numero'],'h' => 450, 'w' => 850));
                            break;									
                        default:
                            echo $cuota['tipo_numero'];
                            break;	
                    }

    //							if($cuota['OrdenDescuentoCuota']['tipo_orden_dto'] != 'CONVE')echo $tipoAndNro;
    //							else echo $controles->linkModalBox($tipoAndNro,array('title' => 'CONVENIO DE PAGO #' . $cuota['OrdenDescuento']['numero'],'url' => '/pfyj/socio_convenios/view/'.$cuota['OrdenDescuento']['numero'],'h' => 450, 'w' => 850))
                ?> 
                </td>
                <td align="center"><?php echo $cuota['cod_nro']?></td>
                <td><?php echo $cuota['proveedor_producto']?></td> 
                <td align="right"><?php echo ($cuota['importe_solicitado']!=0 ? number_format($cuota['importe_solicitado'],2) : "") ?></td>
                <td align="center"><?php echo $cuota['cuota']?></td>
                <td><?php echo $cuota['tipo_cuota']?></td>
                <td align="center"><?php echo $util->armaFecha($cuota['vencimiento'])?></td>
                <td><?php echo $cuota['estado']?></td>
                <td><?php echo $cuota['situacion_cuota']?></td>
                <td align="right"><?php echo ($cuota['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['importe'],2).'</span>' : number_format($cuota['importe'],2)) ?></td>
                <td align="right"><?php echo number_format($cuota['pagado'],2)?></td>
                <td align="right" style="color:green;"><?php if($cuota['pendiente'] != 0) echo number_format($cuota['pendiente'],2)?></td>
                <td align="right"><?php echo ($cuota['saldo_conciliado'] < 0 ? '<span style="color:red;">'.number_format($cuota['saldo_conciliado'],2).'</span>' : number_format($cuota['saldo_conciliado'],2)) ?></td>
                <td align="center"><?php echo $controles->btnModalBox(array('title' => 'DETALLE CUOTA','url' => '/mutual/orden_descuento_cuotas/view/'.$cuota['id'],'h' => 450, 'w' => 750))?></td>
            </tr>
        <?php endif;?>
        <?php if($cuota['tipo_registro'] == 'PAGO'):?>
        <?php $ACU_PAGO_CUOTA += $cuota['pagado'];?>
            
        <tr class="info_pago">
            <td colspan="7"></td>
            <td style="font-size: 75%;font-style: italic;text-align: right;"><?php echo $cuota['tipo_cobro']?></td>
            <td style="font-size: 75%;font-style: italic;"><?php echo $util->armaFecha($cuota['vencimiento'])?></td>
            <td colspan="3" style="font-size: 75%;font-style: italic;"><?php echo $util->periodo($cuota['situacion_cuota'])?></td>
            <td align="right" style="font-size: 75%;font-style: italic;"><?php echo $util->nf($cuota['pagado'])?></td>
            <td colspan="3">
                <?php if($cuota['cancelacion_orden_id'] != 0):?>
                    <?php echo $controles->linkModalBox('ORD.CANC. #'.$cuota['cancelacion_orden_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cuota['cancelacion_orden_id'],'h' => 450, 'w' => 750))?>
                <?php endif;?>
                <?php if($cuota['reversado'] == 1):?>
                    <span style='color:red'>Reversado</span>
                <?php endif;?>                
            </td>
        </tr>
            
        <?php endif;?>    
        <?php // if(!empty($cuota['OrdenDescuentoCuota']['pagos'])):?>
        <?php // foreach($cuota['OrdenDescuentoCuota']['pagos'] as $pago):?>
<!--        <tr class="info_pago">
            <td colspan="6"></td>
            <td style="font-size: 75%;font-style: italic;text-align: right;"><?php // echo $pago['TipoCobro']['concepto_1']?></td>
            <td style="font-size: 75%;font-style: italic;"><?php // echo $util->armaFecha($pago['OrdenDescuentoCobro']['fecha'])?></td>
            <td colspan="3" style="font-size: 75%;font-style: italic;"><?php // echo $util->periodo($pago['OrdenDescuentoCobro']['periodo_cobro'])?></td>
            
            
            <td align="right" style="font-size: 75%;font-style: italic;"><?php // echo $util->nf($pago['OrdenDescuentoCobroCuota']['importe'])?></td>
            <td colspan="2"></td>
        </tr>-->
        <?php // endforeach;?>
        <?php // endif;?>
        
        <?php endforeach;?>
        <tr>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="11" align="right"><strong>TOTAL PERIODO</strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="14" align="right"><strong>SALDO ACUMULADO A <?php echo $util->periodo($cuota['periodo'],true)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
        </tr>           
    </table>    
<?php else:?>
<div class="notices">
<h4>NO EXISTEN CUOTAS PARA LOS PARAMETROS DE BUSQUEDA INDICADOS</h4>	
</div>
<?php endif;?>

<?php
//debug($cuotas);
//$cuotas = null;
?>
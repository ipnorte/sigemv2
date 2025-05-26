<h3>DETALLE DE ATRASO A <?php echo $util->periodo($periodo,true)?></h3>
<?php if(count($cuotas)!=0):?>
	<table style="width: 100%;" class="tbl_grilla">
	
		<tr>
			<th>ORD.DTO.</th>
			<th>ORGANISMO</th>
			<th>TIPO / NUMERO</th>
			<th>COD - NRO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTA</th>
			<th>CONCEPTO</th>
			<th>VTO / PAGO</th>
			<th></th>
			<th>SIT</th>
			<th>IMPORTE</th>
			<th>PAGADO</th>
			<th>SALDO</th>
			<th></th>
			
		</tr>
        
        <?php $periodo = null?>
        <?php $primero = true;?>
        <?php $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = 0?>
        <?php foreach($cuotas as $cuota):?>


        
        
        <?php if($periodo != $cuota['estado_cuenta']['periodo']):?>
            <?php $periodo = $cuota['estado_cuenta']['periodo'];?>
            <?php if($primero):?>
                <?php $primero = false;?>
            <?php else:?>
        
                <tr>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="10" align="right"><strong>TOTAL PERIODO</strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="12" align="right"><strong>SALDO ACUMULADO A <?php echo $util->periodo($periodo_actual,true)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
                    <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                </tr>        
                
            <?php endif;?>
        
            <tr>
                <th colspan="14" style="font-size:13px;background-color: #e2e6ea;border:0"><h4 style="text-align: left;color:#000000;"><?php echo $util->periodo($cuota['estado_cuenta']['periodo'],true)?></h4></th>
            </tr> 
                
            <?php 
            $periodo_actual = $cuota['estado_cuenta']['periodo'];
            $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = 0;
            ?>
            
        <?php endif;?>
        <?php $ACU_IMPO_CUOTA += $cuota['estado_cuenta']['importe'];?>
        <?php $ACU_PAGO_CUOTA += $cuota['estado_cuenta']['pagado'];?>
        <?php $ACU_SALDO_CUOTA += $cuota['estado_cuenta']['saldo_conciliado'];?>
        <?php $ACU_SALDO_CUOTA_ACUM += $cuota['estado_cuenta']['saldo_conciliado'];?>                
        
        <tr class="<?php echo $cuota['estado_cuenta']['estado']?>">
            <td align="center"><?php echo $controles->linkModalBox($cuota['estado_cuenta']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['estado_cuenta']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['estado_cuenta']['orden_descuento_id'].'/'.$cuota['estado_cuenta']['socio_id'],'h' => 450, 'w' => 750))?></td>
            <td><?php echo $cuota['estado_cuenta']['organismo']?></td>
            <td nowrap="nowrap">
            <?php
                switch ($cuota['estado_cuenta']['tipo_orden_dto']){
                    case 'OCOMP':
                        echo $controles->linkModalBox($cuota['estado_cuenta']['tipo_numero'],array('title' => 'ORDEN DE CONSUMO / SERVICIO #' . $cuota['estado_cuenta']['numero'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$cuota['estado_cuenta']['numero'],'h' => 450, 'w' => 850));
                        break;								
                    case 'CONVE':
                        echo $controles->linkModalBox($cuota['estado_cuenta']['tipo_numero'],array('title' => 'CONVENIO DE PAGO #' . $cuota['estado_cuenta']['numero'],'url' => '/pfyj/socio_convenios/view/'.$cuota['estado_cuenta']['numero'],'h' => 450, 'w' => 850));
                        break;
                    case Configure::read('APLICACION.tipo_orden_dto_credito'):
                        echo $controles->linkModalBox($cuota['estado_cuenta']['tipo_numero'],array('title' => 'SOLICITUD DE CREDITO #' . $cuota['estado_cuenta']['numero'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$cuota['estado_cuenta']['numero'],'h' => 450, 'w' => 850));
                        break;									
                    default:
                        echo $cuota['estado_cuenta']['tipo_numero'];
                        break;	
                }

//							if($cuota['OrdenDescuentoCuota']['tipo_orden_dto'] != 'CONVE')echo $tipoAndNro;
//							else echo $controles->linkModalBox($tipoAndNro,array('title' => 'CONVENIO DE PAGO #' . $cuota['OrdenDescuento']['numero'],'url' => '/pfyj/socio_convenios/view/'.$cuota['OrdenDescuento']['numero'],'h' => 450, 'w' => 850))
            ?> 
            </td>
            <td align="center"><?php echo $cuota['estado_cuenta']['cod_nro']?></td>
            <td><?php echo $cuota['estado_cuenta']['proveedor_producto']?></td> 
            <td align="center"><?php echo $cuota['estado_cuenta']['cuota']?></td>
            <td><?php echo $cuota['estado_cuenta']['tipo_cuota']?></td>
            <td align="center"><?php echo $util->armaFecha($cuota['estado_cuenta']['vencimiento'])?></td>
            <td><?php echo $cuota['estado_cuenta']['estado']?></td>
            <td><?php echo $cuota['estado_cuenta']['situacion_cuota']?></td>
            <td align="right"><?php echo ($cuota['estado_cuenta']['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['estado_cuenta']['importe'],2).'</span>' : number_format($cuota['estado_cuenta']['importe'],2)) ?></td>
            <td align="right"><?php echo number_format($cuota['estado_cuenta']['pagado'],2)?></td>
            <td align="right"><?php echo ($cuota['estado_cuenta']['saldo_conciliado'] < 0 ? '<span style="color:red;">'.number_format($cuota['estado_cuenta']['saldo_conciliado'],2).'</span>' : number_format($cuota['estado_cuenta']['saldo_conciliado'],2)) ?></td>
            <td align="center"><?php echo $controles->btnModalBox(array('title' => 'DETALLE CUOTA','url' => '/mutual/orden_descuento_cuotas/view/'.$cuota['estado_cuenta']['id'],'h' => 450, 'w' => 750))?></td>
            
            
        </tr>
        <?php endforeach;?>
        <tr>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="10" align="right"><strong>TOTAL PERIODO</strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="12" align="right"><strong>SALDO ACUMULADO A <?php echo $util->periodo($periodo_actual,true)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
        </tr>           
    </table>    


<?php // debug($cuotas)?>
<?php endif;?>
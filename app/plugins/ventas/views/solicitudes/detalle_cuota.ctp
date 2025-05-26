<div class="card">
    <div class="card-body">
        <small>
            <div class="row mb-1 ">
                <div class="col-12">
                    ORDEN DTO: <strong><?php echo $cuota['OrdenDescuentoCuota']['orden_descuento_id']?></strong> 
                    &nbsp; TIPO/NRO: <strong><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></strong>
                    &nbsp;PRODUCTO:<strong><?php echo $cuota['OrdenDescuentoCuota']['tipo_producto_desc']?></strong>
                    &nbsp;CUOTA: <strong><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></strong>
                    &nbsp;PERIODO: <strong><?php echo $cuota['OrdenDescuentoCuota']['periodo_d']?></strong>
                </div>
            </div>
            <div class="row mb-1 ">
                <div class="col-12">
                    CONCEPTO: <strong><?php echo $cuota['OrdenDescuento']['tipo_cuota_desc']?></strong> 
                    &nbsp;ESTADO: <strong><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></strong>
                    &nbsp;SITUACION:<strong><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></strong>
                </div>
            </div>
            <div class="row mb-1 ">
                <div class="col-12">
                    &nbsp;BENEFICIO: <strong>#<?php echo $cuota['OrdenDescuentoCuota']['persona_beneficio_id']?> <strong><?php echo $cuota['OrdenDescuentoCuota']['organismo']?></strong> - <strong><?php echo $cuota['OrdenDescuentoCuota']['beneficio']?></strong></strong>
                </div>
            </div>            
            <div class="row mb-1 ">
                <div class="col-12">
                    IMPORTE: <strong><?php echo number_format($cuota['OrdenDescuentoCuota']['importe'],2)?></strong>
                    &nbsp; PAGADO: <strong><?php echo number_format($cuota['OrdenDescuentoCuota']['pagado'],2)?></strong>
                    &nbsp;SALDO ACTUAL:<strong><?php echo number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)?></strong>
                    <?php if($cuota['OrdenDescuentoCuota']['pre_imputado'] != "0"):?>
                    <span style="color: red;">&nbsp;PREIMPUTADO ** <?php echo number_format($cuota['OrdenDescuentoCuota']['pre_imputado'],2)?> ***</span>
                    <?php endif;?>
                    <?php if($cuota['OrdenDescuentoCuota']['importe_en_cancelacion'] != "0"):?>
                    <span style="color: red;">&nbsp;CANCELACION PENDIENTE** <?php echo number_format($cuota['OrdenDescuentoCuota']['importe_en_cancelacion'],2)?> ***</span>
                    <?php endif;?>                             
                </div>
            </div>
            <?php if(!empty($cuota['OrdenDescuentoCuota']['observaciones'])):?>
                <div class="row mb-1 ">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" style="font-size: 10px;">
                                <?php echo $cuota['OrdenDescuentoCuota']['observaciones']?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>
        </small>
        <?php if(count($cuota['OrdenDescuentoCobroCuota'])!=0):?>
        
            <div class="row mb-1 mt-3 ">
                <div class="col-12">
                    <h7 class="card-title">INFORMACION DEL COBRO</h7>
                    <table class="table table-sm" style="font-size: 80%;">
                        <thead>
                            <tr>
				<th>#</th>
				<th>TIPO COBRO</th>
				<th>FECHA PAGO</th>
				<th>PERIODO PAGO</th>
				<th>RECIBO Nro</th>
				<th>TOTAL</th>
				<th>ORD.CANC.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cuota['OrdenDescuentoCobroCuota'] as $pago):?>
				<tr class="<?php echo ($pago['reversado'] == 1 ? "activo_0" : "")?>">
					<!--<td align="center"><?php echo $pago['OrdenDescuentoCobro']['id']?></td>-->
                                        <td align="center"><?php echo $pago['OrdenDescuentoCobro']['id']?></td>
<!--					<td align="center"><?php //   echo $controles->linkModalBox('#'.$pago['OrdenDescuentoCobro']['id'],array('title' => 'ORDEN DE COBRO #' . $pago['OrdenDescuentoCobro']['id'],'url' => '/mutual/orden_descuento_cobros/view/'.$pago['OrdenDescuentoCobro']['id'],'h' => 450, 'w' => 750))?></td>-->
					<td nowrap="nowrap"><?php echo $this->requestAction('/config/global_datos/valor/'.$pago['OrdenDescuentoCobro']['tipo_cobro'])?></td>
					<td align="center"><?php echo $util->armaFecha($pago['OrdenDescuentoCobro']['fecha'])?></td>
					<td align="center"><?php echo (!empty($pago['OrdenDescuentoCobro']['periodo_cobro']) ? $util->periodo($pago['OrdenDescuentoCobro']['periodo_cobro']) : 'S/D')?></td>
					<td><?php echo $pago['OrdenDescuentoCobro']['nro_recibo']?></td>
					<td align="right"><strong><?php echo number_format($pago['importe'],2)?></strong></td>
					<td align="center"><strong><?php echo ($pago['OrdenDescuentoCobro']['cancelacion_orden_id'] != 0 ? $pago['OrdenDescuentoCobro']['cancelacion_orden_id'] :  '')?></strong></td>
					<td align="center"><?php echo ($pago['reversado'] == 1 ? "<span style='background-color:red;color:white;padding:2px;font-weight:bold;'>R</span>":"")?></td>
					<td align="center">
						<?php if($pago['OrdenDescuentoCobro']['anulado']==1):?>
							<span style="color:red;font-weight: bold;"> *** ANULADO ***</span>
						<?php endif;?>	
					</td>
				</tr>                            
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>        
        
        <?php endif;?>
        
    </div>
</div>


<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

// debug($cuota);

?>


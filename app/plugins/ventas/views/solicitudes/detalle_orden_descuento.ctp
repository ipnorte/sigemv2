<?php ?>

<style>
.table-success-light {
    background-color: #eaf7ef !important;
}

.table-danger-light {
    background-color: #fbeaea !important;
}
</style>


<div class="card">
        <div class="card-body">
            <small>
                <div class="row mb-1 ">
                    <div class="col-12">
                        ORDEN DE DESCUENTO: <strong># <?php echo $orden['OrdenDescuento']['id']?></strong>
                        &nbsp; <?php echo ($orden['OrdenDescuento']['reprogramada'] == 1 ? '(** Reprogramada **)' : '')?>
                        &nbsp; <?php echo ($orden['OrdenDescuento']['permanente'] == 1 ? '(** Permanente **)' : '')?>
                        &nbsp;&nbsp;FECHA: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['fecha'])?></strong>
                    </div>
                </div>
                <div class="row mb-1 ">
                    <div class="col-12">
                        SOLICITUD: <strong><?php echo $orden['OrdenDescuento']['tipo_nro']?></strong>
                        &nbsp;
                        PRODUCTO: <strong><?php echo $orden['OrdenDescuento']['producto']?></strong>
                        &nbsp; 
                        ORGANISMO: <strong><?php echo $orden['OrdenDescuento']['organismo_desc']?></strong>
                    </div>
                </div>
                <div class="row mb-1 ">
                    <div class="col-12">
                        BENEFICIO: <strong><?php echo $orden['OrdenDescuento']['beneficio_str']?></strong>
                    </div>
                </div>                          
                <div class="row mb-2 ">
                    <div class="col-12">
                        INICIA: <strong><?php echo $util->periodo($orden['OrdenDescuento']['periodo_ini'])?></strong>
                        <?php //   echo ($orden['OrdenDescuento']['permanente'] == 1 ? ' (PERMANENTE)' : '')?>
                        &nbsp;	&nbsp;
                        VTO 1er CUOTA: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_socio'])?></strong>
                        &nbsp;	&nbsp;
                        VTO PROVEEDOR: <strong><?php echo $util->armaFecha($orden['OrdenDescuento']['primer_vto_proveedor'])?></strong>                        

                    </div>
                </div>                          
                        
                        
                <!-- comment             
                <div class="row mb-1">
                    <div class="col-9"></div>
                    <div class="col-3">
                        <?php if ($solicitud['MutualProductoSolicitud']['anulada'] == 0): ?>
                            &nbsp;<a role="button" class="btn btn-info btn-small btn-block" href="<?php echo $this->base . "/mutual/orden_descuentos/impresion/" . $orden['OrdenDescuento']['id'] . '/PDF' ?>" target="_blank"><i class="fas fa-download"></i>&nbsp;Descargar</a>
                        <?php endif; ?>                 
                    </div>
                </div>       
                -->
                        
            </small>
            
            <table class="table table-sm" style="font-size: 100%;">
                <thead>
                    <tr>
                        <th>CUOTA</th>
                        <th>PERIODO</th>
                        <th>CONCEPTO</th>
                        <th>ESTADO</th>
                        <th>SIT</th>
                        <th>IMPORTE</th>
                        <th>PAGADO</th>
                        <th>F.ULT.PAG.</th>
                        <th>SALDO</th>
                        <th>ACUM</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody style="font-size: 80%;">
                
                <?php 
                $ACUM = $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = 0;
                $nCuoPerm = 0;
                foreach($orden['OrdenDescuentoCuota'] as $cuota):
                    $ACUM += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
                    $ACU_IMPO_CUOTA += $cuota['OrdenDescuentoCuota']['importe'];
                    $ACU_PAGO_CUOTA += $cuota['OrdenDescuentoCuota']['pagado'];  
                    $ACU_SALDO_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
                    $nCuoPerm++;
                ?>
                        <?php
                        $claseFila = '';

                        if ($cuota['OrdenDescuentoCuota']['estado'] == 'B') {
                            $claseFila = 'table-danger-light';
                        } elseif ($cuota['OrdenDescuentoCuota']['saldo_cuota'] == 0) {
                            $claseFila = 'table-success-light';
                        }
                        ?>

                        <tr class="<?php echo $claseFila ?>">

                        <td>
                            <?php if($cuota['OrdenDescuentoCuota']['orden_descuento_permanente']):?>
                            <?php echo $nCuoPerm ."/" . count($orden['OrdenDescuentoCuota'])?>    
                            <?php else:?>    
                            <?php echo $cuota['OrdenDescuentoCuota']['cuota']?>
                            <?php endif;?>                               
                        </td>
                        <td><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></td>
                        <td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
                        <td><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></td>
                        <td><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></td>
                        <td align="right"><?php echo ($cuota['OrdenDescuentoCuota']['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['importe'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['importe'],2)) ?></td>
                        <td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['pagado'],2)?></td>
                        <td align="center"><?php echo $util->armaFecha($cuota['OrdenDescuentoCuota']['fecha_ultimo_pago'])?></td>
                        <td align="right" style="font-weight: bold;"><?php echo ($cuota['OrdenDescuentoCuota']['saldo_cuota'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)) ?></td>
                        <td align="right"><strong><?php echo number_format($ACUM,2)?></strong></td>
                        <td>
                            <a href="#" 
                               class="ver-cuota-detalle" 
                               data-toggle="modal" 
                               data-target="#modalCuotaDetalle" 
                               data-url="<?php echo $this->base . '/ventas/solicitudes/detalle_cuota/' . $cuota['OrdenDescuentoCuota']['id']; ?>">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody> 
                <tfoot>
                    <tr>
                        <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="5" align="right"><strong>TOTALES</strong></td>
                        <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
                        <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
                        <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                        <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
                        <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACUM,2)?></strong></td>
                        <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                    </tr>
                </tfoot>
            </table>    

<?php

// debug($orden);

?>             
            
        </div>
</div>

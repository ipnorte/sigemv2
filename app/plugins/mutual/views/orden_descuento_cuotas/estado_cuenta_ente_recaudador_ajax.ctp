<h3><?php echo $datos['ente']?> :: ESTADO DE CUENTA</h3>

<?php if($datos['error'] == 0):?>

<div class="areaDatoForm2">
    CLIENTE:&nbsp;<strong><?php echo $datos['cliente']?></strong>
    <hr/>

    <p></p>
    <table class="tbl_grilla">
        <tr>
            <th colspan="15" style="text-align: left;">
                <?php echo $datos['estado_cuenta']->datos_personales->tdoc . " " . $datos['estado_cuenta']->datos_personales->ndoc?>
                &nbsp;|&nbsp;CUIL:&nbsp;<?php echo $datos['estado_cuenta']->datos_personales->cuil?> 
                &nbsp;|&nbsp;Nombre:&nbsp;<?php echo $datos['estado_cuenta']->datos_personales->apenom?>
            </th>
        </tr>
        <tr>
            <th colspan="11">ESTADO DE CUENTA</th>
            <th colspan="4">MEDIO DE PAGO</th>
        </tr>
        <tr>
            <th>ORD</th>
            <th>REF</th>
            <th>PRODUCTO</th>
            <th>CONCEPTO</th>
            <th>PERIODO</th>
            <th>CUOTA</th>
            <th>IMPORTE</th>
            <th>PAGOS</th>
            <th>S/ACREDITAR</th>
            <th>SALDO</th>
            <th>ACUMULADO</th>
            <th>CBU</th>
            <th>SUC-CTA</th>
            <th>EMPRESA</th>
            <th>ACUERDO DEBITO</th>
        </tr>
        <?php foreach ($datos['estado_cuenta']->estado_cuota as $cuota):?>
        
        <tr class="<?php echo $cuota->tipo?>">
            <td style="font-weight: bold;"><?php echo $controles->linkModalBox($cuota->nro_referencia_proveedor,array('title' => 'ORDEN DE DESCUENTO #' . $cuota->nro_referencia_proveedor,'url' => '/mutual/orden_descuentos/view/'.$cuota->nro_referencia_proveedor.'/'.$socio['Socio']['id'],'h' => 450, 'w' => 750))?>
                <?php //   echo $cuota->nro_referencia_proveedor?></td>
            <td><?php echo $cuota->orden_descuento?></td>
            <td><?php echo $cuota->producto?></td>
            <td><?php echo $cuota->concepto?></td>
            <td><?php echo $cuota->periodo?></td>
            <td><?php echo $cuota->cuota?></td>
            <td style="text-align: right;"><?php echo $util->nf($cuota->importe)?></td>
            <td style="text-align: right;"><?php echo $util->nf($cuota->pagos)?></td>
            <td style="text-align: right;"><?php echo $util->nf($cuota->pendiente_acreditar)?></td>
            <td style="text-align: right;color: green;font-weight: bold;"><?php echo $util->nf($cuota->saldo_aconciliar)?></td>
            <td style="text-align: right;font-weight: bold;"><?php echo $util->nf($cuota->saldo_aconciliar_acumulado)?></td>
            <td style="color: #667;"><?php echo $cuota->cbu?></td>
            <td style="color: #667;"><?php echo $cuota->sucursal ." ". $cuota->nro_cta?></td>
            <td style="color: #667;"><?php echo $cuota->empresa?></td>
            <td style="text-align: right;color: #667;"><?php if($cuota->acuerdo_debito != 0) echo $util->nf($cuota->acuerdo_debito)?></td>
        </tr>
        
        <?php endforeach;?>
    </table>
    
</div>


<?php else:?>

<div class="notices_error"><?php echo $datos['msg']?></div>

<?php endif;?>

<?php //   debug($datos);?>
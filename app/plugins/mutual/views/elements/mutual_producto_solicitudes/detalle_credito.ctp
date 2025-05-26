<?php 
    $objetoCalculado = json_decode($detalle_calculo_plan);
    // debug($objetoCalculado);
?>

<table style="margin-top: 5px;">
    <tr>
        <th>SOLICITADO</th>
        <th>GASTOS</th>
        <th>SELLADOS</th>
        <th>TOTAL GASTOS</th>
        <th>NETO A PERCIBIR</th>
    </tr>
    <tr>
        <td style="text-align: center;font-weight: bold;background-color: #FFFF88;"><?php echo number_format($objetoCalculado->liquidacion->capitalSolicitado,2)?></td>
        <td style="text-align: center;"><?php echo number_format($objetoCalculado->liquidacion->gastoAdminstrativo->importe,2)?></td>
        <td style="text-align: center;"><?php echo number_format($objetoCalculado->liquidacion->sellado->importe,2)?></td>
        <td style="text-align: center;"><?php echo number_format($objetoCalculado->liquidacion->gastoAdminstrativo->importe + $objetoCalculado->liquidacion->sellado->importe,2)?></td>
        <td style="text-align: center;font-weight: bold;background-color: #FFFF88;"><?php echo number_format($objetoCalculado->liquidacion->netoPercibe,2)?></td>
    </tr>
</table>
<table style="margin-top: 5px;">

    <tr>
        <th colspan="6">DETALLE DEL PLAN DE CUOTAS</th>
    </tr>
    <tr>
        <th>CUOTA</th>
        <th>PERIODO</th>        
        <th>CAPITAL</th>
        <th>INTERES</th>
        <th>IMPORTE</th>
        <th>SALDO</th>
    </tr>

    <?php foreach($objetoCalculado->detalleCuotas as $nroCuota => $objetoCuota):?>

        <tr>
            <td style="text-align: center;"><?php echo $objetoCuota->nroCuota?></td>
            <td><?php echo $util->periodo($objetoCuota->periodo)?></td>
            <td style="text-align: right;"><?php echo number_format($objetoCuota->capital,2)?></td>
            <td style="text-align: right;"><?php echo number_format($objetoCuota->interes + $objetoCuota->iva,2)?></td>
            <td style="text-align: right;font-weight: bold;"><?php echo number_format($objetoCuota->importe,2)?></td>
            <td style="text-align: right;"><?php echo number_format($objetoCuota->saldo,2)?></td>
        </tr>

    <?php endforeach;?>
    <tr class="totales">
        <th colspan="2"></th>
        <th><?php echo number_format($objetoCalculado->liquidacion->capitalBaseCalculo,2)?></th>
        <th><?php echo number_format($objetoCalculado->liquidacion->interesesDevengados,2)?></th>
        <th><?php echo number_format($objetoCalculado->liquidacion->totalPrestamo,2)?></th>
        <th></th>
    </tr>

</table>

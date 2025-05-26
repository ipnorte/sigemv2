<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>ADMINISTRACION DE PLANES :: PRUEBA DE GRILLAS</h3>

<?php echo $this->renderElement('proveedor_planes/info_plan',array('plan' => $plan))?>
<?php // debug($plan)?>
<div class="areaDatoForm2">
    <h3>Simular Plan</h3>
    <hr/>
    <?php echo $frm->create(null,array('action' => 'grillas/' . $plan['ProveedorPlan']['id'].'/'.$grilla['ProveedorPlanGrilla']['id']))?>
    <table class="tbl_form">
        <tr>
            <td><?php echo $frm->input('ProveedorPlanGrillaCuota.liquido',array('label' => 'LIQUIDO','type' => 'select', 'options' => $liquido_opts,'style' => 'font-size: 13px;font-weight: bold;'))?></td>
            <td><?php echo $frm->input('ProveedorPlanGrillaCuota.cuotas',array('label' => 'CUOTAS','type' => 'select', 'options' => $cuotas_opts,'style' => 'font-size: 13px;font-weight: bold;'))?></td>
            <td><input type="submit" value="CALCULAR"/></td>
        </tr>
    </table>
    <?php echo $frm->end();?>
    
<?php if(!empty($calculo)):?>
<hr/>

<table>
    <tr>
        <th>METODO</th>
        <th>SOLICITADO</th>
        
        <th><?php echo $liquidacion['tipoCuotaGAdminDesc']?></th>
        <th><?php echo $liquidacion['tipoCuotaSelladoDesc']?></th>
        <th>NETO A PERCIBIR</th>
        <th>T.N.A.</th>
        <th>T.E.A.</th>
        <th>T.E.M.</th>
        <th>C.F.T.</th>
    </tr>
    <tr>
        <td style="text-align: center;"><?php echo $liquidacion['metodo']?></td>
        <td style="text-align: center;font-weight: bold;background-color: #FFFF88;"><?php echo number_format($liquidacion['capitalSolicitado'],2)?></td>
        
        <td style="text-align: center;"><?php echo number_format($liquidacion['gastoAdmin'],2)?></td>
        <td style="text-align: center;"><?php echo number_format($liquidacion['sellados'],2)?></td>

        <td style="text-align: center;font-weight: bold;background-color: #FFFF88;"><?php echo number_format($liquidacion['netoPercibe'],2)?></td>
        <td style="text-align: center;"><?php echo number_format($liquidacion['TNA'],2)?>%</td>
        <td style="text-align: center;"><?php echo number_format($liquidacion['TEA'],2)?>%</td>
        <td style="text-align: center;"><?php echo number_format($liquidacion['TEM'],2)?>%</td>
        <td style="text-align: center;"><?php echo number_format($liquidacion['CFT'],2)?>%</td>
    </tr>
</table>
<h3>Detalle de Cuotas</h3>
<table style="width: 70%">
    <tr>
        <th>CUOTA</th>
        <th>1 - CAPITAL</th>
        <th>2 - INTERES</th>
        <th>3 - IVA</th>
        <th>4 - INTERES + IVA</th>
        <th>CUOTA TOTAL (1 + 4)</th>
        <th>SALDO</th>
    </tr>
<php 

    $CUOTA = "0";
    $CAPITAL = "0";
    $INTERES = "0";
    $IVA = "0";
    $INTERESIVA = "0";
    $CUOTATOTAL = "0";

?>    
<?php 
    $CUOTA = $CAPITAL = $INTERES = $IVA = $INTERESIVA = $CUOTATOTAL = floatval(0);

foreach($calculo as $cuota):
?>

    <?php 
        
        $CAPITAL += $cuota['CAPITAL'];
        $INTERES += $cuota['INTERES'];
        $IVA += $cuota['IVA'];
        $INTERESIVA += $cuota['INTERES'] + $cuota['IVA'];
        $CUOTATOTAL += $cuota['IMPORTE'];
    ?>

    <tr>
        <td style="text-align: center;font-weight: bold;background-color: #FFFF88;"><?php echo $cuota['CUOTA']?></td>
        <td style="text-align: right;"><?php echo number_format($cuota['CAPITAL'],2)?></td>
        <td style="text-align: right;"><?php echo number_format($cuota['INTERES'],2)?></td>
        <!-- <td style="font-size:150%;text-align: right;"><?php // echo number_format($cuota['CAPITAL'] + $cuota['INTERES'],2)?></td> -->
        <td style="text-align: right;"><?php echo number_format($cuota['IVA'],2)?></td>
        <td style="text-align: right;"><?php echo number_format($cuota['INTERES'] + $cuota['IVA'],2)?></td>
        <td style="text-align: right;font-weight: bold;;background-color: #FFFF88;"><?php echo number_format($cuota['IMPORTE'],2)?></td>
        <td style="text-align: right;"><?php echo number_format($cuota['SALDO'],2)?></td>
        <!-- <td style="font-size:150%;text-align: right;background-color: #D3EFA7;"><?php echo number_format($cuota['TNA'],2)?>%</td> -->
        <!-- <td style="font-size:150%;text-align: right;background-color: #D3EFA7;"><?php echo number_format($cuota['TEM'],2)?>%</td> -->
        <!-- <td style="font-size:150%;text-align: center;background-color: #D3EFA7;"><?php echo number_format($cuota['CFT'],2)?>%</td> -->
    </tr>
<?php endforeach;?>
    <tr class="totales">
        <th></th>
        <th><?php echo number_format($CAPITAL,2)?></th>
        <th><?php echo number_format($INTERES,2)?></th>
        <th><?php echo number_format($IVA,2)?></th>
        <th><?php echo number_format($INTERESIVA,2)?></th>
        <th><?php echo number_format($CUOTATOTAL,2)?></th>
        <th></th>
    </tr>
</table>

<?php //debug($liquidacion);?>

<?php endif;?>    
    
</div>

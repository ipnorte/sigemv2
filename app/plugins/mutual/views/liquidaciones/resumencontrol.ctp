<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: LISTADO DE LIQUIDACIONES EMITIDAS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<h3>RESUMEN CONTROL DE LIQUIDACION : Comparativo respecto al per&iacute;odo anterior</h3>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<table>
    <tr>
        <th rowspan="2">PRODUCTO</th>
        <th rowspan="2">CUOTA</th>
        <th colspan="3">LIQUIDACION ACTUAL</th>
        <th colspan="3">LIQUIDACION ANTERIOR</th>
        <th colspan="3">VARIACION</th>
    </tr>
    <tr>
        <th>PERIODO</th>
        <th>MORA</th>
        <th>TOTAL</th>
        <th>PERIODO</th>
        <th>MORA</th>
        <th>TOTAL</th>
        <th>PERIODO</th>
        <th>MORA</th>
        <th>TOTAL</th>
        
    </tr>

    <?php 
    
    $periodo_actual = 0;
    $mora_actual = 0;
    $total_actual = 0;
    $periodo_anterior = 0;
    $mora_anterior = 0;
    $total_anterior = 0;
    $diferencia_periodo = 0;
    $diferencia_mora = 0;
    $diferencia_total = 0;
    
    ?>

    <?php foreach($datos as $dato):?>

        <?php 
    
        $periodo_actual += $dato[0]['periodo_actual'];
        $mora_actual += $dato[0]['mora_actual'];
        $total_actual += $dato[0]['total_actual'];
        $periodo_anterior += $dato[0]['periodo_anterior'];
        $mora_anterior += $dato[0]['mora_anterior'];
        $total_anterior += $dato[0]['total_anterior'];
        $diferencia_periodo += $dato[0]['diferencia_periodo'];
        $diferencia_mora += $dato[0]['diferencia_mora'];
        $diferencia_total += $dato[0]['diferencia_total'];
        
        ?>    
    
    <tr>
        <td><?php echo $dato['tp']['tipo_producto']?></td>
        <td><?php echo $dato['tc']['tipo_cuota']?></td>
        <td style="text-align: right; background: #D3EFA7;"><?php echo number_format($dato[0]['periodo_actual'],2,",",".")?></td>
        <td style="text-align: right; background: #D3EFA7;"><?php echo number_format($dato[0]['mora_actual'],2,",",".")?></td>
        <td style="text-align: right; background: #D3EFA7;font-weight: bold;"><?php echo number_format($dato[0]['total_actual'],2,",",".")?></td>

        <td style="text-align: right; background: #EFE378;"><?php echo number_format($dato[0]['periodo_anterior'],2,",",".")?></td>
        <td style="text-align: right; background: #EFE378;"><?php echo number_format($dato[0]['mora_anterior'],2,",",".")?></td>
        <td style="text-align: right; background: #EFE378;font-weight: bold;"><?php echo number_format($dato[0]['total_anterior'],2,",",".")?></td>
        
        <td style="text-align: right;color: <?php echo ($dato[0]['diferencia_periodo'] < 0 ? "red" : "green")?>"><?php echo number_format($dato[0]['diferencia_periodo'],2,",",".")?></td>
        <td style="text-align: right;color: <?php echo ($dato[0]['diferencia_mora'] < 0 ? "red" : "green")?>"><?php echo number_format($dato[0]['diferencia_mora'],2,",",".")?></td>
        <td style="font-weight: bold; text-align: right;color: <?php echo ($dato[0]['diferencia_total'] < 0 ? "red" : "green")?>"><?php echo number_format($dato[0]['diferencia_total'],2,",",".")?></td>
    </tr>
    
    <?php endforeach;?>
    <tr class="totales">
        <th colspan="2"></th>
        <th><?php echo number_format($periodo_actual,2,",",".")?></th>
        <th><?php echo number_format($mora_actual,2,",",".")?></th>
        <th><?php echo number_format($total_actual,2,",",".")?></th>
        <th><?php echo number_format($periodo_anterior,2,",",".")?></th>
        <th><?php echo number_format($mora_anterior,2,",",".")?></th>
        <th><?php echo number_format($total_anterior,2,",",".")?></th>
        <th><?php echo number_format($diferencia_periodo,2,",",".")?></th>
        <th><?php echo number_format($diferencia_mora,2,",",".")?></th>
        <th><?php echo number_format($diferencia_total,2,",",".")?></th>

    </tr>
</table>

<?php // debug($datos)?>

<?php 
echo $this->renderElement('show',array(
										'plugin' => 'shells',
										'process' => 'reporte_control_liquidacion',
										'accion' => '.mutual.listados.reporte_liquidacion_deuda.'.$liquidacion['Liquidacion']['id'].'.1',
										'btn_label' => 'VER REPORTE',
										'titulo' => 'REPORTE CONTROL DE LIQUIDACION | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) ." - ". $util->periodo($liquidacion['Liquidacion']['periodo']),
										'p1' => $liquidacion['Liquidacion']['id'],
));

?>




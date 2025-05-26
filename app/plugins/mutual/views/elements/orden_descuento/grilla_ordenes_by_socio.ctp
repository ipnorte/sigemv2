<?php 
//$ords = $this->requestAction('/mutual/orden_descuentos/ordenes_by_socio/'.$socio_id.'/'.(isset($estado_actual) ? $estado_actual : 1));
$ordenes = $this->requestAction('/mutual/orden_descuentos/ordenes_by_socio2/'.$socio_id.'/'.(isset($estado_actual) ? $estado_actual : 1));
?>
<?php if(!empty($ordenes)):?>


<table>

	<tr>
		<td colspan="18" style="text-align: left;">
		<h3>DETALLE DE ORDENES DE DESCUENTO VIGENTES</h3>
		</td>
	</tr>

	<tr>
		<th>ORDEN</th>
		<th>INICIA</th>
		<th>1er VTO</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR - PRODUCTO</th>
		<th>TOTAL</th>
                <th>CUOTAS</th>
		<th>IMPORTE</th>
		<th>SALDO</th>
                <th></th>
		<th>PER</th>
		<th>BENEFICIO</th>
	</tr>

<?php
$i = 0;
$TOTAL = 0;
$VENCIDO = 0;
$AVENCER = 0;
$PAGADO = 0;

$SALDO = 0;

foreach ($ordenes as $ord):

	$TOTAL += $ord['OrdenDescuento']['total_cuotas'];
	$VENCIDO += $ord['OrdenDescuento']['saldo'];
//	$AVENCER += $ord['OrdenDescuento']['importe_avencer'];
	$PAGADO += $ord['OrdenDescuento']['total_pagos'];

        $SALDO += $ord['OrdenDescuento']['saldo'];
    
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
//	debug($ord);
?>	
	<tr class="<?php echo $ord['OrdenDescuento']['tipo_orden_dto']?>">
	
		<td align="center"><?php echo $controles->linkModalBox($ord['OrdenDescuento']['id'],array('title' => 'ORDEN DE DESCUENTO #' . $ord['OrdenDescuento']['id'],'url' => '/mutual/orden_descuentos/view/'.$ord['OrdenDescuento']['id'].'/'.$ord['OrdenDescuento']['socio_id'],'h' => 450, 'w' => 750))?></td>
		<td nowrap="nowrap"><?php echo $util->periodo($ord['OrdenDescuento']['periodo_ini'])?></td>
		<td nowrap="nowrap"><?php echo $util->armaFecha($ord['OrdenDescuento']['primer_vto_socio'])?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['tipo_numero']?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['proveedor_producto']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_total'],2)?></td>
                <td nowrap="nowrap" style="text-align: center;"><?php echo $ord['OrdenDescuento']['cuotas']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_cuota'],2)?></td>
		<td align="right" nowrap="nowrap"><?php echo number_format($ord['OrdenDescuento']['saldo'],2)?></td>
		<td align="center"><?php // echo $ord['OrdenDescuento']['cuotas_adeudadas']?></td>
		<td align="center"><?php echo $controles->OnOff($ord['OrdenDescuento']['permanente'],true)?></td>
		<td><?php echo $ord['OrdenDescuento']['beneficio_str']?></td>
	</tr>

<?php endforeach;?>	

	<tr class="totales">
		<th colspan="8">TOTAL ADEUDADO <?php echo $util->periodo(date('Ym'),true)?></th>
		<th><?php echo number_format($SALDO,2)?></th>
                <th colspan="3"></th>
	</tr>




</table>
<input type="hidden" name="ordenDescuentoTotales" id="ordenDescuentoTotales" value="<?php echo $TOTAL?>" />
<input type="hidden" name="ordenDescuentoVencido" id="ordenDescuentoVencido" value="<?php echo $VENCIDO?>" />
<input type="hidden" name="ordenDescuentoAVencer" id="ordenDescuentoAVencer" value="0" />
<input type="hidden" name="ordenDescuentoPagado" id="ordenDescuentoPagado" value="<?php echo $PAGADO?>" />
<input type="hidden" name="ordenDescuentoSaldo" id="ordenDescuentoSaldo" value="<?php echo $SALDO?>" />


<?php endif;?>
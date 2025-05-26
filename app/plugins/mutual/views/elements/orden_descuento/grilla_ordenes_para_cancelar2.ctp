<?php 
$ordenes = $this->requestAction('/mutual/orden_descuentos/ordenes_by_socio2/'.$persona['Socio']['id']."/0");
//debug($ordenes);
//exit;
?>

<?php if(!empty($ordenes)):?>
<table>

	<tr>
		<td colspan="13" align="right">
		<?php echo $controles->botonGenerico('/mutual/orden_descuentos/reporte_by_socio_pdf/'.$persona['Socio']['id'].'/1','controles/printer.png','IMPRIMIR',array('target' => 'blank'))?>
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
		<th></th>
	</tr>

<?php
$i = 0;
$TOTAL = 0;
$VENCIDO = 0;
$AVENCER = 0;
$PAGADO = 0;

$SALDO = 0;

foreach ($ordenes as $ord):

//	$TOTAL += $ord['OrdenDescuento']['importe_devengado'];
//	$VENCIDO += $ord['OrdenDescuento']['importe_vencido'];
//	$AVENCER += $ord['OrdenDescuento']['importe_avencer'];
//	$PAGADO += $ord['OrdenDescuento']['importe_pagado'];

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
		<td><?php echo $controles->botonGenerico('/mutual/cancelacion_ordenes/sel_cuota/'.$persona['Persona']['id'].'/'.$ord['OrdenDescuento']['id'],'controles/16-em-plus.png','')?></td>
	</tr>

<?php endforeach;?>	

	<tr class="totales">
		<th colspan="8">TOTAL ADEUDADO <?php echo $util->periodo(date('Ym'),true)?></th>
		<th><?php echo number_format($SALDO,2)?></th>
                <th colspan="4"></th>
	</tr>

	<tr>
		<td colspan="13" align="right">
		<?php echo $controles->botonGenerico('/mutual/orden_descuentos/reporte_by_socio_pdf/'.$persona['Socio']['id'].'/1','controles/printer.png','IMPRIMIR',array('target' => 'blank'))?>
		</td>
	</tr>


</table>
<?php else:?>
<h4>NO EXISTEN ORDENES DE CONSUMOS</h4>	
<?php endif;?>


<?php
$tipo = (isset($tipo) ? $tipo : "EXPTE"); 
$ordenes = $this->requestAction('/mutual/orden_descuentos/ordenes_by_numero/'.$tipo.'/' . $numero);
//debug($ordenes);
?>
<?php if(!empty($ordenes)):?>
<h3 style="border-bottom: 1px solid;">ORDENES DE DESCUENTO EMITIDAS BAJO EL NUMERO #<?php echo $tipo?>/<?php echo $numero?></h3>
<table>

	<tr>
		<th>ORDEN</th>
		<th>INICIA</th>
		<th>1er VTO</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR - PRODUCTO</th>
		<th>TOTAL</th>
		<th>CUOTAS</th>
		<th>IMPORTE</th>
		<th>PER</th>
		<th>BENEFICIO</th>
	</tr>

<?php
$i = 0;
$TOTAL = 0;
$VENCIDO = 0;
$AVENCER = 0;
$PAGADO = 0;

foreach ($ordenes as $ord):

	$TOTAL += $ord['OrdenDescuento']['importe_total'];
	$VENCIDO += $ord['OrdenDescuento']['importe_vencido'];
	$AVENCER += $ord['OrdenDescuento']['importe_avencer'];
	$PAGADO += $ord['OrdenDescuento']['importe_pagado'];

	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
//	debug($sol);
?>	
	<tr class="<?php echo $ord['OrdenDescuento']['tipo_orden_dto']?>">
	
		<td align="center"><?php echo $controles->linkModalBox($ord['OrdenDescuento']['id'],array('title' => 'ORDEN DE DESCUENTO #' . $ord['OrdenDescuento']['id'],'url' => '/mutual/orden_descuentos/view/'.$ord['OrdenDescuento']['id'].'/'.$ord['OrdenDescuento']['socio_id'],'h' => 450, 'w' => 700))?></td>
		<td nowrap="nowrap"><?php echo $util->periodo($ord['OrdenDescuento']['periodo_ini'])?></td>
		<td nowrap="nowrap"><?php echo $util->armaFecha($ord['OrdenDescuento']['primer_vto_socio'])?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['tipo_orden_dto']?> #<?php echo $ord['OrdenDescuento']['numero']?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['proveedor_producto']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_total'],2)?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['cuotas']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_cuota'],2)?></td>
		<td align="center"><?php echo $controles->OnOff2($ord['OrdenDescuento']['permanente'],true)?></td>
		<td><?php echo ($ord['OrdenDescuento']['persona_beneficio_id'] != 0 ? $ord['OrdenDescuento']['beneficio_str'] : "<span style='color:red;'>ERROR: SIN BENEFICIO!</span>")?></td>
	</tr>

<?php endforeach;?>	

	<tr>
		<th colspan="5" style="text-align: right;">TOTALES</th>
		<th style="text-align: right;"><?php echo number_format($TOTAL,2)?></th>
		<th style="text-align: right;"><?php echo number_format($VENCIDO,2)?></th>
		<th style="text-align: right;"><?php echo number_format($AVENCER,2)?></th>
		<th style="text-align: right;"><?php echo number_format($PAGADO,2)?></th>
		<th style="text-align: right;" colspan="2"></th>
	</tr>


</table>
<?php endif;?>
<?php 
$soloVigentes = (isset($soloVigentes) ? $soloVigentes : 0);
$ordenes = $this->requestAction('/mutual/orden_descuentos/ordenes_by_socio/'.$socio_id."/1");
?>


<?php if(!empty($ordenes)):?>
<table>

	<tr>
		<td colspan="16" align="right">
		<?php echo $controles->botonGenerico('/mutual/orden_descuentos/reporte_by_socio_pdf/'.$socio_id.'/'.$estadoActual,'controles/printer.png','IMPRIMIR',array('target' => 'blank'))?>
		</td>
	</tr>

	<tr>
		<th>ORDEN</th>
		<th>INICIA</th>
		<th>1er VTO</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR - PRODUCTO</th>
		<th colspan="2">TOTAL</th>
		<th>IMPORTE CUOTA</th>
		<th colspan="2">VENCIDO</th>
		<th colspan="2">A VENCER</th>
		<th colspan="2">PAGADO</th>
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

	$TOTAL += $ord['OrdenDescuento']['importe_devengado'];
	$VENCIDO += $ord['OrdenDescuento']['importe_vencido'];
	$AVENCER += $ord['OrdenDescuento']['importe_avencer'];
	$PAGADO += $ord['OrdenDescuento']['importe_pagado'];

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
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['tipo_nro']?></td>
		<td nowrap="nowrap"><?php echo $ord['OrdenDescuento']['proveedor_producto']?></td>
		
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_devengado'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['cuotas']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_cuota'],2)?></td>
		<td align="right" nowrap="nowrap"><?php echo number_format($ord['OrdenDescuento']['importe_vencido'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['vencidas']?></td>
		<td align="right" nowrap="nowrap"><?php echo number_format($ord['OrdenDescuento']['importe_avencer'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['avencer']?></td>	
		<td align="right" nowrap="nowrap"><?php echo number_format($ord['OrdenDescuento']['importe_pagado'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['pagadas']?></td>				
		<td align="center"><?php echo $controles->OnOff($ord['OrdenDescuento']['permanente'],true)?></td>
		<td><?php echo $ord['OrdenDescuento']['beneficio_str']?></td>
	</tr>

<?php endforeach;?>	

	<tr class="totales">
		<th colspan="5">TOTALES</th>
		<th colspan="2"><?php echo number_format($TOTAL,2)?></th>
		<th></th>
		<th colspan="2"><?php echo number_format($VENCIDO,2)?></th>
		<th colspan="2"><?php echo number_format($AVENCER,2)?></th>
		<th colspan="2"><?php echo number_format($PAGADO,2)?></th>
		<th colspan="2"></th>
	</tr>

	<tr>
		<td colspan="16" align="right">
		<?php echo $controles->botonGenerico('/mutual/orden_descuentos/reporte_by_socio_pdf/'.$socio_id.'/'.$estadoActual,'controles/printer.png','IMPRIMIR',array('target' => 'blank'))?>
		</td>
	</tr>


</table>
<?php else:?>
<h4>NO EXISTEN ORDENES DE CONSUMOS</h4>	
<?php endif;?>


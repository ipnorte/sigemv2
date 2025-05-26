<?php if(!empty($ordenes)):?>
<table>

	<tr>
		<td colspan="8"><h3>CONSUMOS DEL SOCIO <?php echo ($estadoActual == 1 ? "VIGENTES (con saldo)" : ($estadoActual == 2 ? "FINALIDADOS (Pagados Totalmente)" : ($estadoActual == 3 ? "DADOS DE BAJA" : "")))?></h3></td>
		<td colspan="11" align="right">
		<?php echo $controles->botonGenerico('/mutual/orden_descuentos/reporte_by_socio_pdf/'.$socio_id.'/'.$estadoActual,'controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?>
		</td>
	</tr>

	<tr>
		<th>ORDEN</th>
		<th>INICIA</th>
		<th>1er VTO</th>
		<th>TIPO / NUMERO</th>
		<th>PROVEEDOR - PRODUCTO</th>
		<th colspan="2">TOTAL</th>
		<th>CUOTA</th>
		<th colspan="2">VENCIDO</th>
		<th colspan="2">A VENCER</th>
		<th colspan="2">PAGADO</th>
		<th>SALDO</th>
		<th>PER</th>
		<th>EST</th>
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

	$TOTAL += $ord['OrdenDescuento']['importe_devengado'];
	$VENCIDO += $ord['OrdenDescuento']['importe_vencido'];
	$AVENCER += $ord['OrdenDescuento']['importe_avencer'];
	$PAGADO += $ord['OrdenDescuento']['importe_pagado'];
	$SALDO += $ord['OrdenDescuento']['saldo'];

	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
//	debug($ord);
?>	
	<tr class="<?php echo $ord['OrdenDescuento']['tipo_orden_dto']?>">
	
		<td align="center"><?php echo $controles->linkModalBox($ord['OrdenDescuento']['id'],array('title' => 'ORDEN DE DESCUENTO #' . $ord['OrdenDescuento']['id'],'url' => '/mutual/orden_descuentos/view/'.$ord['OrdenDescuento']['id'].'/'.$ord['OrdenDescuento']['socio_id'],'h' => 450, 'w' => 750))?></td>
		<td><?php echo $util->periodo($ord['OrdenDescuento']['periodo_ini'])?></td>
		<td><?php echo $util->armaFecha($ord['OrdenDescuento']['primer_vto_socio'])?></td>
		<td><?php echo $ord['OrdenDescuento']['tipo_nro']?></td>
		<td><?php echo $ord['OrdenDescuento']['proveedor_producto']?></td>
		
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_devengado'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['cuotas']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_cuota'],2)?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_vencido'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['vencidas']?></td>
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_avencer'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['avencer']?></td>	
		<td align="right"><?php echo number_format($ord['OrdenDescuento']['importe_pagado'],2)?></td>
		<td align="center"><?php echo $ord['OrdenDescuento']['pagadas']?></td>
		<td align="right"><strong><?php echo number_format($ord['OrdenDescuento']['saldo'],2)?></strong></td>				
		<td align="center"><?php echo $controles->OnOff($ord['OrdenDescuento']['permanente'],true)?></td>
		<td align="center"><?php echo $controles->OnOff($ord['OrdenDescuento']['activo'])?></td>
		<td><?php echo $ord['OrdenDescuento']['beneficio_str']?></td>
		<td align="center">
			<?php if($ord['OrdenDescuento']['activo']==1 && $ord['OrdenDescuento']['permanente']==1 && ($ord['OrdenDescuento']['tipo_orden_dto'] == "OCOMP" || $ord['OrdenDescuento']['tipo_orden_dto'] == "OSERV")):?>
			<?php echo $frm->btnForm(array('URL'=>'/mutual/orden_descuentos/suspender_permanente/'.$ord['OrdenDescuento']['id'].'/'.$ord['OrdenDescuento']['socio_id'],'LABEL' => 'SUSPENDER'))?>
			<?php endif;?>
			<?php if($estadoActual == 3 && $ord['OrdenDescuento']['activo']==0 && $ord['OrdenDescuento']['permanente']==1 && ($ord['OrdenDescuento']['tipo_orden_dto'] == "OCOMP" || $ord['OrdenDescuento']['tipo_orden_dto'] == "OSERV")):?>
				BAJA 
				<?php if(!empty($ord['OrdenDescuento']['periodo_hasta'])):?>
				(A PARTIR DE  <strong><?php echo $util->periodo($ord['OrdenDescuento']['periodo_hasta'],true)?></strong>)
				<?php endif;?>			
			<?php endif;?>

		</td>
	</tr>

<?php endforeach;?>	

	<tr class="totales">
		<th colspan="5">TOTALES</th>
		<th colspan="2"><?php echo number_format($TOTAL,2)?></th>
		<th></th>
		<th colspan="2"><?php echo number_format($VENCIDO,2)?></th>
		<th colspan="2"><?php echo number_format($AVENCER,2)?></th>
		<th colspan="2"><?php echo number_format($PAGADO,2)?></th>
		<th><?php echo number_format($SALDO,2)?></th>
		<th colspan="4"></th>
	</tr>

	<tr>
		<td colspan="19" align="right">
		<?php echo $controles->botonGenerico('/mutual/orden_descuentos/reporte_by_socio_pdf/'.$socio_id.'/'.$estadoActual,'controles/pdf.png','IMPRIMIR',array('target' => 'blank'))?>
		</td>
	</tr>


</table>
<?php else:?>
<h4>NO EXISTEN ORDENES DE CONSUMOS <?php echo ($estadoActual == 1 ? "VIGENTES" : ($estadoActual == 2 ? "FINALIDADOS" : ($estadoActual == 3 ? "DADOS DE BAJA" : "")))?></h4>	
<?php endif;?>


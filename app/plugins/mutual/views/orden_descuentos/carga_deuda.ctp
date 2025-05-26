<?php echo $this->renderElement('head',array('title' => 'CARGA - MODIFICA DEUDA','plugin' => 'config'))?>
<?php if($show_form_searh == 1)echo $this->renderElement('orden_descuento/form_search_by_numero',array('accion' => 'carga_deuda','plugin' => 'mutual'))?>
<?php if(!empty($orden)):?>
<?php echo $this->renderElement('socios/apenom',array('socio_id' => $orden['OrdenDescuento']['socio_id'], 'plugin' => 'pfyj'))?>
<?php echo $this->renderElement('orden_descuento/resumen_by_id',array('plugin' => 'mutual','id' => $orden['OrdenDescuento']['id'],'detallaCuotas' => false))?>

<div class="actions"><?php if($orden['OrdenDescuento']['activo'] === '1') echo $controles->botonGenerico('/mutual/orden_descuento_cuotas/agregar_cuota/'.$orden['OrdenDescuento']['id'],'controles/add.png','Agregar Cuota')?></div>

<?php
$cuotas = $this->requestAction('/mutual/orden_descuento_cuotas/cuotas_by_odescuento/'.$orden['OrdenDescuento']['id']);

$ACU_IMPO_CUOTA = 0;
$ACU_PAGO_CUOTA = 0;
$ACU_SALDO_CUOTA = 0;
?>
	<?php if(!empty($cuotas)):?>
		<h4>DETALLE DE CUOTAS DE LA ORDEN DE DESCUENTO</h4>
		<table>
			
			<tr>
				<th>CUOTA</th>
				<th>BENEFICIO</th>
				<th>PERIODO</th>
				<th>VTO / PAGO</th>
				<th>CONCEPTO</th>
				<th>ESTADO</th>
				<th>SIT</th>
				<th colspan="2">IMPORTE</th>
				<th>PAGADO</th>
				<th>SALDO</th>
				<th></th>
				
			</tr>	
			
			<?php
				$ACUM = 0; 
				foreach($cuotas as $cuota):
					$ACUM += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
					$ACU_IMPO_CUOTA += $cuota['OrdenDescuentoCuota']['importe'];
					$ACU_PAGO_CUOTA += $cuota['OrdenDescuentoCuota']['pagado'];
					$ACU_SALDO_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];	
						?>
							<tr class="<?php echo $cuota['OrdenDescuentoCuota']['estado']?>">
								<td align="center"><strong><?php echo $cuota['OrdenDescuentoCuota']['nro_cuota'].'/'. $cuota['OrdenDescuento']['cuotas']?></strong></td>
								<td><?php echo $cuota['OrdenDescuentoCuota']['beneficio']?></td>
								<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
								<td align="center"><strong><?php echo $util->armaFecha(( $cuota['OrdenDescuentoCuota']['estado'] != 'P' ? $cuota['OrdenDescuentoCuota']['vencimiento'] : $cuota['OrdenDescuentoCuota']['fecha_ultimo_pago']))?></strong></td>
								<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
								<td><?php echo $cuota['OrdenDescuentoCuota']['estado_desc']?></td>
								<td><?php echo $cuota['OrdenDescuentoCuota']['situacion_desc']?></td>
								<td align="right"><strong><?php echo ($cuota['OrdenDescuentoCuota']['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['importe'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['importe'],2)) ?></strong></td>
								<td align="center"><?php echo ( $cuota['OrdenDescuentoCuota']['estado'] == 'A' ? $controles->vencida($cuota['OrdenDescuentoCuota']['vencida']) : '')?></td>
								<td align="right"><?php echo number_format($cuota['OrdenDescuentoCuota']['pagado'],2)?></td>
								
								<td align="right"><?php echo ($cuota['OrdenDescuentoCuota']['saldo_cuota'] < 0 ? '<span style="color:red;">'.number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2).'</span>' : number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2)) ?></td>
								<td align="center"><?php echo ($cuota['OrdenDescuentoCuota']['estado'] == 'A' && $orden['OrdenDescuento']['activo'] == 1 ? $controles->botonGenerico('/mutual/orden_descuento_cuotas/modificar_cuota/'.$cuota['OrdenDescuentoCuota']['id'],'controles/edit.png') : '')?></td>
								
							</tr>
			<?php endforeach;?>
	
			<tr>
				<th colspan="7" style="text-align: right;">TOTALES </th>
				<th colspan="2" style="text-align: right;"><?php echo number_format($ACU_IMPO_CUOTA,2)?></th>
				<th style="text-align: right;"><?php echo number_format($ACU_PAGO_CUOTA,2)?></th>
				<th style="text-align: right;"><?php echo number_format($ACU_SALDO_CUOTA,2)?></th>
				<th></th>
				
			</tr>
		<table>
	<?php endif;?>
<?php endif;?>
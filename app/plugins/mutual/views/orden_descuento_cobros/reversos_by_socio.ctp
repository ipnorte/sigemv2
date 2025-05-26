<?php echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'))?>

<h3>HISTORIAL DE PAGOS DEL SOCIO</h3>

<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => 1,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<h4>DETALLE DE CUOTAS REVERSADAS POR EL SOCIO</h4>

<?php if(!empty($reversos)):?>
	<?php echo $controles->btnRew('REGRESAR AL LISTADO PAGOS DEL SOCIO','/mutual/orden_descuento_cobros/by_socio/'.$socio['Socio']['id'])?>
	<br/><br/>
	<table class="tbl_grilla">

		
		<?php foreach($reversos as $periodo => $cobros):?>
		
			<tr>
				<th colspan="14" style="font-size:13px;background-color: #666666">
				<h4 style="text-align: left;color:#FFFFFF;">INFORME PROVEEDOR: <?php echo $util->periodo($periodo,true)?></h4>
				</th>
			</tr>	
			
			<tr>
				<th>#COBRO</th>
				<th>FECHA</th>
				<th>ORGANISMO</th>
				<th>ORDEN DTO.</th>
				<th>TIPO - NUMERO</th>
				<th>PROVEEDOR - PRODUCTO</th>
				<th>CONCEPTO</th>
				<th>CUOTA</th>
				<th>PERIODO</th>
				<th>COBRADO</th>
				<th>REVERSADO</th>
				<th>PERIODO_REVERSO</th>
				<th>USUARIO_REVERSO</th>
			</tr>				
		
			<?php foreach($cobros['cuotas'] as $cuota):?>
			
				
		
				<tr>
					<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'],array('title' => 'ORDEN DE COBRO #' . $cuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'],'url' => '/mutual/orden_descuento_cobros/view/'.$cuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'],'h' => 450, 'w' => 750))?></td>
					<td align="center"><?php echo $util->armaFecha($cuota['OrdenDescuentoCobroCuota']['fecha_reverso'])?></td>
					<td><?php echo $cuota['OrdenDescuentoCobroCuota']['organismo']?></td>
					<td align="center"><?php echo $controles->linkModalBox($cuota['OrdenDescuentoCobroCuota']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cuota['OrdenDescuentoCobroCuota']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cuota['OrdenDescuentoCobroCuota']['orden_descuento_id'].'/'.$cuota['OrdenDescuentoCobroCuota']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cuota['OrdenDescuentoCobroCuota']['tipo_nro']?></td>
					<td><?php echo $cuota['OrdenDescuentoCobroCuota']['proveedor_producto']?></td>
					<td><?php echo $cuota['OrdenDescuentoCobroCuota']['tipo_cuota_desc']?></td>
					<td align="center"><?php echo $cuota['OrdenDescuentoCobroCuota']['cuota_cuotas']?></td>
					<td><?php echo $util->periodo($cuota['OrdenDescuentoCobroCuota']['periodo'],true)?></td>
					<td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCobroCuota']['importe'])?></td>
					<td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCobroCuota']['importe_reversado'])?></td>
					<td align="center"><?php echo $util->periodo($cuota['OrdenDescuentoCobroCuota']['periodo_proveedor_reverso'],true)?></td>
					<td align="center"><?php echo $cuota['OrdenDescuentoCobroCuota']['usuario_reverso']?></td>

				</tr>
		
			<?php endforeach;?>
			<tr>
			
				<th class="totales" colspan="9" style="border-top: 1px solid;">TOTAL</th>
				<th class="totales" style="border-top: 1px solid;"><?php echo $util->nf($cobros['total_cobrado'])?></th>
				<th class="totales" style="border-top: 1px solid;"><?php echo $util->nf($cobros['total_reversado'])?></th>
				<th class="totales" style="border-top: 1px solid;"></th>
				<th class="totales" style="border-top: 1px solid;"></th>
			
			</tr>
		
		<?php endforeach;?>

		</table>
	<?php //   debug($reversos)?>
<?php else:?>
	<br/>
	<h5>NO EXISTEN PAGOS REVERSADOS</h5>
	<?php echo $controles->btnRew('REGRESAR AL LISTADO PAGOS DEL SOCIO','/mutual/orden_descuento_cobros/by_socio/'.$socio['Socio']['id'])?>
<?php endif;?>
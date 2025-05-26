<?php echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'))?>

<h3>HISTORIAL DE PAGOS DEL SOCIO</h3>

<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => 1,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<h4>DETALLE DE COBROS ANULADOS</h4>
<?php if(!empty($cobros)):?>
		<table class="tbl_grilla">
			<tr>
				<th>#</th>
				<th>TIPO COBRO</th>
				<th>FECHA PAGO</th>
				<th>PERIODO PAGO</th>
				<th>RECIBO</th>
				<th>OBSERVACIONES</th>
				<th>TOTAL</th>
				<th>REVERSADO</th>
				<th>ORD.CANC.</th>
                                <th>CREADO</th>
                                <th>MODIFICADO</th>
                                <th>IP</th>
			</tr>
			<?php foreach($cobros as $pago):?>
				<tr <?php echo ($pago['OrdenDescuentoCobro']['anulado'] == 1 ? "class='activo_0'" : "")?>>
					<td align="center"><?php echo $pago['OrdenDescuentoCobro']['id']?></td>
					<td nowrap="nowrap"><?php echo $pago['OrdenDescuentoCobro']['tipo_cobro_descripcion']?></td>
					<td align="center"><?php echo $util->armaFecha($pago['OrdenDescuentoCobro']['fecha'])?></td>
					<td align="center"><?php echo (!empty($pago['OrdenDescuentoCobro']['periodo_cobro']) ? $util->periodo($pago['OrdenDescuentoCobro']['periodo_cobro']) : 'S/D')?></td>
					<td align="center"><?php echo $pago['OrdenDescuentoCobro']['recibo_numero_string']?></td>
					<td><?php echo (empty($pago['OrdenDescuentoCobro']['observaciones']) ? $pago['OrdenDescuentoCobro']['recibo_comentarios'] : $pago['OrdenDescuentoCobro']['observaciones'])?></td>
					<td align="right"><strong><?php echo number_format($pago['OrdenDescuentoCobro']['importe'],2)?></strong></td>
					<td align="right"><strong><?php if($pago['OrdenDescuentoCobro']['total_reversado'] != 0) echo number_format($pago['OrdenDescuentoCobro']['total_reversado'],2)?></strong></td>
					<td align="center"><strong><?php echo ($pago['OrdenDescuentoCobro']['cancelacion_orden_id'] != 0 ? $pago['OrdenDescuentoCobro']['cancelacion_orden_id'] :  '')?></strong></td>
                                        <td><?php echo $pago['OrdenDescuentoCobro']['user_created'] . " ".$pago['OrdenDescuentoCobro']['created']?></td>
                                        <td><?php echo $pago['OrdenDescuentoCobro']['user_modified'] . " ".$pago['OrdenDescuentoCobro']['modified']?></td>
                                        <td><?php echo $pago['OrdenDescuentoCobro']['last_ip']?></td>
				</tr>

	
			<?php endforeach;?>
		</table>
<?php else:?>
	<br/>
	<h5>NO EXISTEN COBROS ANULADOS</h5>
	<?php echo $controles->btnRew('REGRESAR AL LISTADO PAGOS DEL SOCIO','/mutual/orden_descuento_cobros/by_socio/'.$socio['Socio']['id'])?>
<?php endif;?>

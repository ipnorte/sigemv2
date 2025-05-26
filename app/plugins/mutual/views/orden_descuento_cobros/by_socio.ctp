<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>HISTORIAL DE PAGOS DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<?php if(!empty($cobros)):?>

		<div class="actions">
		<?php echo $controles->botonGenerico('/mutual/orden_descuento_cobros/reversos_by_socio/'.$socio['Socio']['id'],'controles/information.png','Reversados')?>
		&nbsp;|&nbsp;
		<?php echo $controles->botonGenerico('/mutual/orden_descuento_cobros/anulados_by_socio/'.$socio['Socio']['id'],'controles/error.png','Anulados')?>
		</div>
		

		<table class="tbl_grilla">
			<tr>
				<th></th>
				<th>#</th>
				<th>TIPO COBRO</th>
				<th>FECHA PAGO</th>
				<th>PERIODO PAGO</th>
				<th>RECIBO</th>
				<th>OBSERVACIONES</th>
				<th>TOTAL</th>
				<th>REVERSADO</th>
				<th>ORD.CANC.</th>
				<th></th>
				<th></th>
                                <th>CREADO</th>
                                <th>MODIFICADO</th>
                                <th>IP</th>
			</tr>
			<?php foreach($cobros as $pago):?>
				<tr <?php echo ($pago['OrdenDescuentoCobro']['anulado'] == 1 ? "class='activo_0'" : "")?>>
					<td><?php echo $controles->botonGenerico('/mutual/orden_descuento_cobros/view/'.$pago['OrdenDescuentoCobro']['id'].'/1','controles/pdf.png')?></td>
					<td align="center"><?php echo $controles->linkModalBox($pago['OrdenDescuentoCobro']['id'],array('title' => 'ORDEN DE COBRO #' . $pago['OrdenDescuentoCobro']['id'],'url' => '/mutual/orden_descuento_cobros/view/'.$pago['OrdenDescuentoCobro']['id'],'h' => 450, 'w' => 750))?></td>
					<td nowrap="nowrap"><?php echo $pago['OrdenDescuentoCobro']['tipo_cobro_descripcion']?></td>
					<td align="center"><?php echo $util->armaFecha($pago['OrdenDescuentoCobro']['fecha'])?></td>
					<td align="center"><?php echo (!empty($pago['OrdenDescuentoCobro']['periodo_cobro']) ? $util->periodo($pago['OrdenDescuentoCobro']['periodo_cobro']) : 'S/D')?></td>
					<td align="center">
                                            <?php // echo (!empty($pago['OrdenDescuentoCobro']['recibo_numero_string']) ? $pago['OrdenDescuentoCobro']['recibo_numero_string'] : "")?>
                                            <?php if (!empty($pago['OrdenDescuentoCobro']['recibo_numero_string'])) echo $controles->Html->link($pago['OrdenDescuentoCobro']['recibo_numero_string'],'/clientes/recibos/imprimir_recibo_pdf/'. $pago['OrdenDescuentoCobro']['recibo_id'], array('target' => 'blank'));?>
                                        </td>
					<td><?php echo (empty($pago['OrdenDescuentoCobro']['observaciones']) ? $pago['OrdenDescuentoCobro']['recibo_comentarios'] : $pago['OrdenDescuentoCobro']['observaciones'])?></td>
					<td align="right"><strong><?php echo number_format($pago['OrdenDescuentoCobro']['importe'],2)?></strong></td>
					<td align="right"><strong><?php if($pago['OrdenDescuentoCobro']['total_reversado'] != 0) echo number_format($pago['OrdenDescuentoCobro']['total_reversado'],2)?></strong></td>
					<td align="center"><strong><?php echo ($pago['OrdenDescuentoCobro']['cancelacion_orden_id'] != 0 ? $controles->linkModalBox('#'.$pago['OrdenDescuentoCobro']['cancelacion_orden_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$pago['OrdenDescuentoCobro']['cancelacion_orden_id'],'h' => 450, 'w' => 750)) :  '')?></strong></td>
					<td align="center"><?php if($pago['OrdenDescuentoCobro']['tipo_cobro'] != "MUTUTCOBRECS" && $pago['OrdenDescuentoCobro']['tipo_cobro'] != "MUTUTCOBCSAC") echo $controles->botonGenerico('anular/'. $pago['OrdenDescuentoCobro']['id'] . '/' . $socio['Socio']['id'],'controles/12-em-cross.png', '', null, 'ESTA SEGURO DE ANULAR \n EL COBRO. ')?></td>
					<td align="center"><?php if($pago['OrdenDescuentoCobro']['tipo_cobro'] == "MUTUTCOBRECS" || $pago['OrdenDescuentoCobro']['tipo_cobro'] == "MUTUTCOBCSAC") echo $frm->btnForm(array('URL'=>'/mutual/orden_descuento_cobros/reversar/'.$pago['OrdenDescuentoCobro']['id'],'LABEL' => 'REVERSAR PAGOS'))?></td>
                                        <td><?php echo $pago['OrdenDescuentoCobro']['user_created'] . " ".$pago['OrdenDescuentoCobro']['created']?></td>
                                        <td><?php echo $pago['OrdenDescuentoCobro']['user_modified'] . " ".$pago['OrdenDescuentoCobro']['modified']?></td>
                                        <td><?php echo $pago['OrdenDescuentoCobro']['last_ip']?></td>
				</tr>
			<?php endforeach;?>
		</table>
<?php endif;?>
<?php // debug($cobros)?>
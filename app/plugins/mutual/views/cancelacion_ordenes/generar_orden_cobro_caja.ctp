<?php echo $this->renderElement('head',array('title' => 'ORDEN DE CANCELACION :: COBRO POR CAJA','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link'=>true,'plugin' => 'pfyj'))?>
<div class="row">
	<?php echo $controles->btnRew('Regresar','/mutual/cancelacion_ordenes/generar/'.$persona['Persona']['id'])?>
</div>
<?php if(count($cancelaciones) != 0):?>
	<?php echo $frm->create(null,array('action' => 'generar_orden_cobro_caja/'.$persona['Persona']['id']))?>
	<h4>DETALLE DE ORDENES DE CANCELACION EMITIDAS</h4>
		<script type="text/javascript">
		</script>
		<table>
			<tr>
				<th>#</th>
				<th>TIPO</th>
				<th>ORDEN</th>
				<th>TIPO / NUMERO</th>
				<th>PROVEEDOR / PRODUCTO</th>
				<th>A LA ORDEN DE</th>
				<th>DEUDA PROVEEDOR</th>
				<th>SALDO ORDEN DTO</th>
				<th>IMPORTE SELECCIONADO</th>
				<th>DEBITO/CREDITO</th>
				<th>VENCIMIENTO</th>
				<th></th>
				
			</tr>
			<?php $i=0;?>
			<?php foreach($cancelaciones as $cancelacion):?>
				<?php $i++;?>
				<tr id="TRL_<?php echo $i?>">
					<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
					<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
					<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_descuento_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['tipo_nro_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
					<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
					<td align="right">
						<?php
							if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
								echo $this->requestAction('/config/global_datos/valor/' . $cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
								echo "&nbsp;= \$";
								echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
							}
						?>
					</td>
					<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
					<td><input type="checkbox" name="data[CancelacionOrden][id_check][<?php echo $cancelacion['CancelacionOrden']['id']?>]" value="<?php echo $cancelacion['CancelacionOrden']['id']?>" onclick="toggleCell('TRL_<?php echo $i?>',this)"/></td>
			<?php endforeach;?>	
		</table>
		<div class="areaDatoForm2">
			<h4>DETALLE DEL COBRO POR CAJA</h4>
			<table class="tbl_form">
				<tr>
					<td>FORMA DE PAGO</td>
					<td>
					<?php echo $this->renderElement('global_datos/combo',array(
                                                    'plugin'=>'config',
                                                    'label' => " ",
                                                    'model' => 'CancelacionOrden.forma_pago',
                                                    'prefijo' => 'MUTUFPAG',
                                                    'disable' => false,
                                                    'empty' => false,
                                                    'selected' => (!empty($this->data['CancelacionOrden']['forma_pago']) ? $this->data['CancelacionOrden']['forma_pago'] : '0'),
                                                    'logico' => true,
					))?>
					</td>
					<td>
					<?php echo $frm->input('CancelacionOrden.pendiente_rendicion_proveedor',array('label' => 'PENDIENTE RENDICION AL PROVEEDOR'))?>					
					</td>
				</tr>
				<tr id="datoBanco">
					<td>BANCO</td>
					<td colspan="2">
					<?php echo $this->renderElement('banco/combo',array(
                                                    'plugin'=>'config',
                                                    'label' => " ",
                                                    'model' => 'CancelacionOrden.banco_id',
                                                    'disable' => false,
                                                    'empty' => true,
                                                    'tipo' => 4
					))?>	
					</td>
				</tr>
				<tr id="datoBancoNroCuota">
					<td>NRO DE CUENTA</td>
					<td colspan="2"><?php echo $frm->input('CancelacionOrden.nro_cta_bco',array('size'=>50,'maxlenght'=>50)); ?></td>
				</tr>
				<tr id="datoBancoNroOpenBan">
					<td>NRO.OPERACION / NRO.CHEQUE</td>
					<td colspan="2"><?php echo $frm->input('CancelacionOrden.nro_operacion',array('size'=>50,'maxlenght'=>50)); ?></td>
				</tr>
				<tr>
					<td>RECIBO NRO.</td>
					<td colspan="2"><?php echo $frm->input('CancelacionOrden.nro_recibo',array('label' => '', 'size' => 25))?></td>
				</tr>
				<tr>
					<td>FECHA IMPUTACION</td>
					<td colspan="2"><?php echo $frm->input('CancelacionOrden.fecha_imputacion',array('dateFormat' => 'DMY','label'=>'','minYear'=>date("Y"), 'maxYear' => date("Y")+1))?>
					<?php //   echo $jsCalendar->input('CancelacionOrden.fecha_imputacion',array(),array('label'=>'','size'=>10,'maxlenght'=>10))?></td>
				</tr>								
				<tr><td colspan="3"><?php echo $frm->submit("RECAUDAR")?></td></tr>
			</table>
	
		</div>
		
		<?php echo $frm->end();?>
		
		<div style="clear: both;"></div>
<?php endif;?>
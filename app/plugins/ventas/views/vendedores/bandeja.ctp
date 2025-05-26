<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'MESA DE ENTRADA DE SOLICITUDES'))?>
<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' =>'bandeja'))?>
	<table class="tbl_form">
		<tr>
			<td>VENDEDOR</td><td><?php echo $frm->input('VendedorBandeja.vendedor_id',array('type' => 'select', 'options' => $vendedores))?></td>
			<td><input type="submit" name="data[VendedorBandeja][btn_presentar]" value="PRESENTAR SOLICITUDES"/></td>
			<td><input type="submit" name="data[VendedorBandeja][btn_ver_remitos]" value="VER CONSTANCIAS"/></td>
		</tr>
	</table>
	<?php echo $frm->end()?>
</div>

<?php if(!empty($solicitudes)):?>
	<h3>SOLICITUDES :: VENDEDOR #<?php echo $vendedor['Vendedor']['id'] . " - " . $vendedor['Persona']['tdoc_ndoc_apenom']?></h3>

	<?php echo $frm->create(null,array('action' =>'bandeja','onsubmit' => "return confirm('GENERAR CONSTANCIA DE PRESENTACION?');"))?>
	<table>
		<tr>
			<th></th>
			<th>ESTADO</th>
			<th>NUMERO</th>
			<th>DOCUMENTO</th>
			<th>SOLICITANTE</th>
			<th>FECHA CARGA</th>
			<th>PROVEEDOR - PRODUCTO</th>
			<th>CAPITAL</th>
			<th>SOLICITADO</th>
			<th>TOTAL</th>
			<th>CUOTAS</th>
			<th>IMPORTE</th>
			<th>BENEFICIO</th>
			<th></th>
		</tr>
	
	<?php
	$i = 0;
	foreach ($solicitudes as $sol):
	?>	
		<tr id="TRL_<?php echo $i?>">
			<td><input type="checkbox" onclick="toggleCell('TRL_<?php echo $i?>',this)" name="data[VendedorRemito][solicitud_id][<?php echo $sol['MutualProductoSolicitud']['id']?>]" value="<?php echo $sol['MutualProductoSolicitud']['id']?>" id="VendedorRemitoSolicitud_<?php echo $i?>"/></td>
			<td><strong><?php echo $sol['MutualProductoSolicitud']['estado_desc']?></strong></td>
			<td align="center"><?php echo $controles->linkModalBox($sol['MutualProductoSolicitud']['id'],array('title' => 'ORDEN DE CONSUMO / SERVICIO #' . $sol['MutualProductoSolicitud']['id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$sol['MutualProductoSolicitud']['id'],'h' => 450, 'w' => 850))?></td>
			<td><strong><?php echo $sol['MutualProductoSolicitud']['beneficiario_tdocndoc']?></strong></td>
			<td><strong><?php echo $sol['MutualProductoSolicitud']['beneficiario_apenom']?></strong></td>
			<td nowrap="nowrap" align="center"><?php echo $util->armaFecha($sol['MutualProductoSolicitud']['fecha'])?></td>
			<td nowrap="nowrap"><?php echo $sol['MutualProductoSolicitud']['proveedor_producto']?></td>
			<td align="right"><?php echo number_format($sol['MutualProductoSolicitud']['importe_solicitado'],2)?></td>
			<td align="right"><strong><?php echo number_format($sol['MutualProductoSolicitud']['importe_percibido'],2)?></strong></td>
			<td align="right"><?php echo number_format($sol['MutualProductoSolicitud']['importe_total'],2)?></td>
			<td align="center"><strong><?php echo $sol['MutualProductoSolicitud']['cuotas']?></strong></td>
			<td align="right"><strong><?php echo number_format($sol['MutualProductoSolicitud']['importe_cuota'],2);?></strong></td>
			<td><?php echo $sol['MutualProductoSolicitud']['beneficio_str']?></td>
			<td align="center">
				<?php 
					if($sol['MutualProductoSolicitud']['tipo_orden_dto'] == Configure::read('APLICACION.tipo_orden_dto_credito')){
						echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_credito_mutual_pdf/'.$sol['MutualProductoSolicitud']['id'],'blank');
					}else{
						echo $controles->btnImprimir('','/mutual/mutual_producto_solicitudes/imprimir_orden_pdf/'.$sol['MutualProductoSolicitud']['id'].'/'.$sol['MutualProductoSolicitud']['permanente'],'blank');
					}
				?>
			</td>
		</tr>
	
	<?php
		$i++;
	endforeach;
	?>	
		<tr>
			<td colspan="13">
				<div class="areaDatoForm">
				<table class="tbl_form">
                    <tr>
                        <td>ASIGNAR ESTADO</td>
                        <td><?php echo $frm->input('VendedorRemito.estado_solicitud',array('type' => 'select', 'options' => array('MUTUESTA0002' => 'LIQUIDACION','MUTUESTA0000' => 'ANULADA')))?></td>
                    </tr>                                    
					<tr>
						<td>OBSERVACIONES</td>
						<td><?php echo $frm->textarea('VendedorRemito.observaciones',array('cols' => 70, 'rows' => 10))?></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" value="GENERAR CONSTANCIA DE PRESENTACION"/></td>
					</tr>
				</table>
				</div>
			</td>
		</tr>
		
	</table>
	
	<?php echo $frm->hidden('VendedorRemito.vendedor_id',array('value' => $vendedor['Vendedor']['id']))?>
	<?php echo $frm->hidden('VendedorRemito.generar_remito',array('value' => 1))?>

	<?php echo $frm->end()?>
<?php else:?>
	<h4>NO EXISTEN SOLICITUDES EMITIDAS PARA EL VENDEDOR</h4>
<?php endif;?>

<?php //   debug($solicitudes)?>
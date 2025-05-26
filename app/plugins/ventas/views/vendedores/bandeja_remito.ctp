<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'MESA DE ENTRADA DE SOLICITUDES'))?>
<?php if(!empty($remito)):?>
<h3>CONSTANCIA DE PRESENTACION DE SOLICITUDES #<?php echo $remito['VendedorRemito']['id']?></h3>

	<table>
		<tr>
			<th></th>
			<th>#</th>
			<th>EMITO EL</th>
			<th>VENDEDOR</th>
			<th>GENERADO POR</th>
		</tr>
		<tr>
			<td><?php echo $controles->btnImprimir('','/ventas/vendedores/imprimir_remito/'.$remito['VendedorRemito']['id'],'blank');?></td>
			<td><strong><?php echo $remito['VendedorRemito']['id']?></strong></td>
			<td><?php echo $remito['VendedorRemito']['created']?></td>
			<td><strong><?php echo "#".$remito['Vendedor']['Vendedor']['id']." - ".$remito['Vendedor']['Persona']['tdoc_ndoc_apenom']?></strong></td>
			<td><?php echo $remito['VendedorRemito']['user_created']?></td>
		</tr>
	</table>
	<h4>DETALLE DE SOLICITUDES PRESENTADAS</h4>
	<table>
		<tr>
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
			<th>ESTADO</th>
			<th></th>
		</tr>
		<?php foreach ($remito['MutualProductoSolicitud'] as $solicitud):?>
		<?php $sol['MutualProductoSolicitud'] = $solicitud;?>
			<tr>
			<td align="center"><?php echo $controles->linkModalBox($sol['MutualProductoSolicitud']['nro_print'],array('title' => 'ORDEN DE CONSUMO / SERVICIO #' . $sol['MutualProductoSolicitud']['id'],'url' => '/mutual/mutual_producto_solicitudes/view/'.$sol['MutualProductoSolicitud']['id'],'h' => 450, 'w' => 850))?></td>
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
			<td><strong><?php echo $sol['MutualProductoSolicitud']['estado_desc']?></strong></td>
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
		<?php endforeach;?>
	</table>	
<?php //   debug($remito)?>

<?php endif;?>
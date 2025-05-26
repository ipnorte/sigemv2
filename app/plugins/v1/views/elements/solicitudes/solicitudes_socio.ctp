<?php 

$persona = $this->requestAction('/pfyj/personas/get_persona/'.$persona_id);
$solicitudes = null;
if(!empty($persona)){
    $socioId = (isset($persona['Socio']['id']) ? $persona['Socio']['id'] : null);
    $solicitudes = $this->requestAction('/v1/solicitudes/get_solicitudes_by_socio/'.$socioId.'/'.$persona['Persona']['tipo_documento'].'/'.$persona['Persona']['documento']);
}

?>

<?php if(!empty($solicitudes)):?>
	<table>
		<tr>
			<th>NRO</th>
			<th>FECHA</th>
			<th>PRODUCTO</th>
			<th>ESTADO</th>
			<th>SOLICITADO</th>
			<th>CUOTAS</th>
			<th>MONTO CUOTA</th>
			<th>CUOTA SOCIAL</th>
			<th>SEGURO</th>
			<th>BENEFICIO</th>
			<th>LIQUIDACION</th>
			<th>RECIBO</th>
			<th>O.PAGO</th>
	<!--		<th></th>-->
			<th></th>
			<th></th>
		</tr>
	<?php
	$i = 0;
	foreach ($solicitudes as $s):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
	
		<td><strong><?php echo ($s['Solicitud']['estado'] == 14 || $s['Solicitud']['estado'] == 19 ? $controles->linkModalBox($s['Solicitud']['nro_solicitud'],array('title' => 'CARATULA EXPEDIENTE #' . $s['Solicitud']['nro_solicitud'],'url' => '/v1/solicitudes/resumen_expediente/'.$s['Solicitud']['nro_solicitud'],'h' => 450, 'w' => 850)) : $controles->linkModalBox($s['Solicitud']['nro_solicitud'],array('title' => 'CARATULA SOLICITUD #' . $s['Solicitud']['nro_solicitud'],'url' => '/v1/solicitudes/caratula/'.$s['Solicitud']['nro_solicitud'],'h' => 450, 'w' => 850))) ?></strong></td>
		<td align="center"><?php echo $util->armaFecha($s['Solicitud']['fecha_solicitud'])?></td>
		<td><?php echo '#' . $s['Producto']['codigo_producto'] . ' - ' . $s['Producto']['Proveedor']['razon_social'] .' - ' . $s['Producto']['descripcion'] . (!empty($s['Solicitud']['reasignar_proveedor_razon_social']) ? " <span style='color:red;'>[*** ".$s['Solicitud']['reasignar_proveedor_razon_social']." ***] </span>" : "")?></td>
		<td align="center"><?php echo $s['Solicitud']['estado_descripcion']?></td>
		<td align="right"><?php echo $s['Solicitud']['solicitado']?></td>
		<td align="center"><?php echo $s['Solicitud']['cuotas']?></td>
		<td align="right"><?php echo $s['Solicitud']['monto_cuota']?></td>
		<td align="right"><?php echo $s['Solicitud']['monto_cuota_social']?></td>
		<td align="right"><?php echo $s['Solicitud']['monto_seguro']?></td>
		<td>
			<?php echo $s['Beneficio']['beneficio_concepto']?>
			<br>
			<?php if(substr($s['Beneficio']['codigo_beneficio'],0,2) == '22'):?>
				NRO: <?php echo $s['Beneficio']['cbu']?>
				&nbsp;-&nbsp;
				<?php echo $s['Beneficio']['banco']?>
				<br>
				EMPRESA:<?php echo $s['Beneficio']['empresa']?>
			<?php endif;?>
			<?php if(substr($s['Beneficio']['codigo_beneficio'],0,2) == '77'):?>
				NRO: <?php echo $s['Beneficio']['nro_beneficio']?>
				&nbsp;-&nbsp;
				LEY:<?php echo $s['Beneficio']['nro_ley']?>
			<?php endif;?>
			<?php if(substr($s['Beneficio']['codigo_beneficio'],0,2) == '66'):?>
				NRO: <?php echo $s['Beneficio']['nro_beneficio']?>
			<?php endif;?>					
		</td>
		<td>
		
			<strong><?php echo $s['Solicitud']['forma_pago']?></strong>
			<br/>
			<?php if(!empty($s['Solicitud']['banco'])):?>
				<?php echo ($s['Solicitud']['codigo_fpago']=='0003' ? $s['Solicitud']['dato_giro'] : $s['Solicitud']['banco'])?>
			<?php endif;?>
			<br/>
			OP.NRO:<?php echo $s['Solicitud']['nro_operacion_pago']?>
			<BR/>
			FECHA: <?php echo $util->armaFecha($s['Solicitud']['fecha_operacion_pago'])?>
			<br/>
			NRO.CRED.PROV.:<?php echo $s['Solicitud']['nro_credito_proveedor']?>
		</td>
				<?php if($s['Solicitud']['recibo_id'] > 0):?><td><?php echo $html->link($s['Solicitud']['recibo_link'],'/pfyj/personas/editRecibo/'.$s['Solicitud']['recibo_id']. '/'.$s['Solicitud']['nro_solicitud'].'/'.$persona_id)?></td>  
				<?php else:?><td><?php echo $controles->botonGenerico('/pfyj/personas/addRecibo/'.$s['Solicitud']['nro_solicitud'].'/'.$persona_id,'controles/book_open.png','') ?></td>  
				<?php endif; ?>

				<?php if($s['Solicitud']['orden_pago_id'] > 0):?><td><?php echo $html->link($s['Solicitud']['orden_pago_link'],'/pfyj/personas/editOrdenPago/'.$s['Solicitud']['orden_pago_id']. '/'.$s['Solicitud']['nro_solicitud'].'/'.$persona_id)?></td>  
				<?php else:?><td><?php echo $controles->botonGenerico('/pfyj/personas/addOrdenPago/'.$s['Solicitud']['nro_solicitud'].'/'.$persona_id,'controles/zone_money.png','') ?></td>  
				<?php endif; ?>
	<!--	<td><?php echo $controles->btnModalBox(array('title' => 'MODIFICAR LIQUIDACION SOLICITUD Nro.'.$s['Solicitud']['nro_solicitud'],'url' => '/v1/solicitudes/editar_fpago/'.$s['Solicitud']['nro_solicitud'],'h' => 500, 'w' => 750,'img'=>'application_edit.png'))?></td>-->
		<td><?php echo $controles->btnModalBox(array('title' => 'HISTORIAL DE LA SOLICITUD Nro.'.$s['Solicitud']['nro_solicitud'],'url' => '/v1/solicitud_estados/historial/'.$s['Solicitud']['nro_solicitud'],'h' => 500, 'w' => 750,'img'=>'calendar.png'))?></td>
		<td><?php if(substr($s['Beneficio']['codigo_beneficio'],0,2) == '66') echo $controles->btnModalBox(array('title' => 'CUPONES DE ANSES DE LA SOLICITUD Nro.'.$s['Solicitud']['nro_solicitud'],'url' => '/v1/solicitud_cupones_anses/by_solicitud/'.$s['Solicitud']['nro_solicitud'],'h' => 450, 'w' => 550,'img'=>'attach.png'))?></td>
	</tr>
	
	<?php endforeach;?>
	</table>
<?php else:?>

	*** NO EXISTEN SOLICITUDES DE CREDITO ANTERIORES ***

<?php endif;?>	
<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>REINTEGROS EMITIDOS A FAVOR DEL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>

	<div>
		<?php echo $controles->botonGenerico('reintegro_anticipado/'.$socio['Socio']['id'],'controles/add.png','GENERAR ORDEN DE REINTEGRO ANTICIPADO')?>
		<?php //echo $controles->botonGenerico('reintegro_anticipado_opago/'.$socio['Socio']['id'],'controles/add.png','GENERAR ORDEN DE REINTEGRO ANTICIPADO')?>
		<?php if(!empty($reintegros)):?>
			&nbsp;|&nbsp;
			<?php //echo $controles->botonGenerico('generar_opago/'.$socio['Socio']['id'],'controles/zone_money.png','ABONAR AL SOCIO')?>
			<?php echo $controles->botonGenerico('generar_orden_pago/'.$socio['Socio']['id'],'controles/zone_money.png','ABONAR AL SOCIO')?>
			&nbsp;|&nbsp;
			<?php echo $controles->botonGenerico('reporte_socio_reintegros/'.$socio['Socio']['id'],'controles/pdf.png','IMPRIMIR RESUMEN',array('target' => 'blank'))?>
		<?php endif;?>
	</div>
	<br/>
	
<?php if(!empty($reintegros)):?>
	<table>
		<tr>
			<th></th>
			<th>#</th>
			<th>TIPO</th>
			<th>PERIODO</th>
			<th>FECHA</th>
			<th>LIQUIDACION</th>
			<th>REINTEGRO S/LIQUIDACION</th>
			<th>REVERSADO</th>
			<th>ABONADO</th>
			<th>SALDO</th>
			<th></th>
			<th>FORMA DE APLICACION</th>
		</tr>
		<?php foreach($reintegros as $reintegro):?>
                
                    <?php 
                    $ordenesPago = array();
                    if(!empty($reintegro[0]['ordenes_pagos'])) {
                        foreach (explode(',', $reintegro[0]['ordenes_pagos']) as $value) {
                            if(!empty($value)) {
                                $op = explode('|', $value);
                                $ordenesPago[$op[0]] = $op[1];
                            }
                        }
                    }
                    ?>
			<tr class="<?php echo ($reintegro[0]['saldo'] > 0 ?  "" : "activo_0")?>">
				<td align="center"><?php if($reintegro['sr']['procesado'] == 0 && $reintegro['SocioReintegro']['anticipado'] == 1 && $reintegro['sr']['importe_reintegro'] == $reintegro[0]['saldo'] && empty($reintegro[0]['ordenes_pagos']) && empty($reintegro['sr']['orden_descuento_cobro_id'])) echo $controles->botonGenerico('borrar/'.$reintegro['SocioReintegro']['id'],'controles/user-trash.png',null,null,"ELIMINAR REINTEGRO #".$reintegro['sr']['id']."?")?></td>
				<td><?php echo $reintegro['sr']['id']?></td>
				<td><strong><?php echo $reintegro['sr']['tipo']?></strong></td>
				<td><?php echo $util->periodo($reintegro['sr']['periodo'],true)?></td>
				<td><?php echo $util->armaFecha($reintegro['sr']['created'])?></td>
				<td><?php echo $reintegro[0]['liquidacion']?></td>
				<td align="right"><?php echo $util->nf($reintegro['sr']['importe_reintegro'])?></td>
				<td align="right"><?php echo $util->nf($reintegro['sr']['importe_reversado'])?></td>
				<td align="right"><?php echo $util->nf($reintegro[0]['pagado'])?></td>	
				<td align="right"><strong><?php echo ($reintegro[0]['saldo'] < 0 ? "<span style='color:red;'>".$util->nf($reintegro[0]['saldo'])."</span>" : $util->nf($reintegro[0]['saldo']))?></strong></td>				
				<td><?php echo $controles->onOff($reintegro['sr']['procesado'])?></td>
				<td>
					<?php if($reintegro['sr']['imputado_deuda'] == 1):?>
					<strong> 
					<?php echo ($reintegro['sr']['orden_descuento_cobro_id'] != 0 ? $controles->linkModalBox('ORDEN COBRO #'.$reintegro['sr']['orden_descuento_cobro_id'],array('title' => 'ORDEN DE COBRO #' . $reintegro['sr']['orden_descuento_cobro_id'],'url' => '/mutual/orden_descuento_cobros/view/'.$reintegro['sr']['orden_descuento_cobro_id'],'h' => 450, 'w' => 750)) : '')?>
					</strong>
					|
					<?php endif;?>
					<?php if($reintegro['sr']['reintegrado'] == 1):?>
						<?php if($reintegro['sr']['orden_pago_id'] > 0):?>
							<strong><?php echo $html->link($ordenesPago[$reintegro['sr']['orden_pago_id']],'/pfyj/socio_reintegros/editOrdenPago/'.$reintegro['sr']['orden_pago_id'] . '/' . $socio['Socio']['id'])?></strong>
						<?php elseif(!empty($reintegro[0]['ordenes_pagos'])): ?>	
							 
							<?php foreach($ordenesPago as $ordenPago => $nroOpago):?>
								<strong><?php echo $html->link($nroOpago,'/pfyj/socio_reintegros/editOrdenPago/'.$ordenPago . '/' . $socio['Socio']['id'])?></strong> |
							<?php endforeach;?>
						<?php else: ?>
							<strong> <?php // echo ($reintegro['SocioReintegro']['socio_reintegro_pago_id'] != 0 ? $controles->linkModalBox('PAGO #'.$reintegro['SocioReintegro']['socio_reintegro_pago_id'],array('title' => 'PAGO #' . $reintegro['SocioReintegro']['socio_reintegro_pago_id'],'url' => '/pfyj/socio_reintegros/ver_detalle_pago/'.$reintegro['SocioReintegro']['socio_reintegro_pago_id'].'/'.$reintegro['SocioReintegro']['id'],'h' => 450, 'w' => 750)) : '')?></strong>
						<?php endif;?>
					<?php elseif(!empty($reintegro[0]['ordenes_pagos'])):?>	
						 
						<?php foreach($ordenesPago as $ordenPago => $nroOpago):?>
							<strong><?php echo $html->link($nroOpago,'/pfyj/socio_reintegros/editOrdenPago/'.$ordenPago . '/' . $socio['Socio']['id'])?></strong> |
						<?php endforeach;?>
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
<?php else:?>
	<h4>SIN REINTEGROS EMITIDOS</h4>
<?php endif;?>

<?php //debug($reintegros)?>
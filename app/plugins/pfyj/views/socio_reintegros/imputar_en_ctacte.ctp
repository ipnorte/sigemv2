<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>IMPUTAR REINTEGROS EN CUENTA CORRIENTE</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<div class="actions"><?php echo $controles->btnRew('Regresar','by_socio/'.$socio['Socio']['id'])?></div>

<?php echo $this->renderElement('socio_reintegros/ficha',array('reintegro' =>$reintegro, 'plugin' => 'pfyj'))?>

<?php echo $frm->create(null,array('action' => 'imputar_en_ctacte/'.$socio['Socio']['id'].'/'.$reintegro['SocioReintegro']['id'].'/1'))?>
<?php echo $frm->hidden('SocioReintegro.socio_id',array('value' => $socio['Socio']['id']))?>
<?php echo $frm->hidden('SocioReintegro.importe_reintegro',array('value' => $reintegro['SocioReintegro']['importe_reintegro']))?>
<?php echo $frm->hidden('SocioReintegro.saldo',array('value' => $reintegro['SocioReintegro']['saldo']))?>

<?php echo $frm->hidden('SocioReintegro.accion',array('value' => 'PREVIEW'))?>

<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'VISTA PREVIA DE LA IMPUTACION','URL' => ( empty($fwrd) ? '/pfyj/socio_reintegros/by_socio/'.$socio['Socio']['id'] : $fwrd) ))?>

<?php if(!empty($imputacion)):?>
<hr/>
<h3>DETALLE DE CUOTAS A IMPUTAR</h3>
	<table>
		<tr>
			<th>PERIODO</th>
			<th>ORDEN</th>
			<th>TIPO / NUMERO</th>
			<th>PROVEEDOR / PRODUCTO</th>
			<th>CUOTA</th>
			<th>CONCEPTO</th>
			<th>SALDO ACTUAL</th>
			<th>IMPUTA</th>
			<th>SALDO</th>
		</tr>
		<?php 
		$ACU_TOTAL_CUOTA = 0;
		$ACU_TOTAL_IMPUTA = 0;
		$ACU_TOTAL_SALDO = 0;
		?>
		<?php foreach($imputacion['cuotas'] as $cuota):?>
			<?php 
//			debug($cuota['OrdenDescuentoCuota']);
			$ACU_TOTAL_CUOTA += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
			$ACU_TOTAL_IMPUTA += $cuota['OrdenDescuentoCuota']['importe_aimputar'];
			$ACU_TOTAL_SALDO += $cuota['OrdenDescuentoCuota']['n_saldocuota'];
			?>
			<tr>
			
				<td><strong><?php echo $util->periodo($cuota['OrdenDescuentoCuota']['periodo'])?></strong></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['orden_descuento_id']?></td>
				<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['tipo_nro']?></td>
				<td nowrap="nowrap"><?php echo $cuota['OrdenDescuentoCuota']['proveedor_producto']?></td>
				<td align="center"><?php echo $cuota['OrdenDescuentoCuota']['cuota']?></td>
				<td><?php echo $cuota['OrdenDescuentoCuota']['tipo_cuota_desc']?></td>
				<td align="right"><?php echo $util->nf($cuota['OrdenDescuentoCuota']['saldo_cuota'])?></td>
				<td align="right"><strong><?php echo $util->nf($cuota['OrdenDescuentoCuota']['importe_aimputar'])?></strong></td>
				<td align="right">
					<?php if($cuota['OrdenDescuentoCuota']['n_saldocuota'] != 0):?>
						<span style="color: red;"><strong><?php echo $util->nf($cuota['OrdenDescuentoCuota']['n_saldocuota'])?></strong></span>
					<?php else:?>
						<span style="color: green;"><strong><?php echo $util->nf($cuota['OrdenDescuentoCuota']['n_saldocuota'])?></strong></span>
					<?php endif;?>
				</td>			
			
			</tr>
		<?php endforeach;?>
		
		<tr>
			<th colspan="6" style="text-align: right;">TOTALES</th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_CUOTA)?></th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_IMPUTA)?></th>
			<th style="text-align: right;"><?php echo $util->nf($ACU_TOTAL_SALDO)?></th>
		</tr>
		
	</table>
	
	<?php //   debug($reintegro)?>
	<?php //   debug($ACU_TOTAL_CUOTA)?>

	<?php if(round($reintegro['SocioReintegro']['saldo'],2) > round($ACU_TOTAL_CUOTA,2)):?>

		<div class="notices_error">
			<strong>ATENCION!:</strong><br/>
			El total adeudado del socio <strong>$<?php echo $util->nf($ACU_TOTAL_CUOTA)?></strong> (vencido y a vencer) es MENOR que el importe del reintegro 
			<strong>$<?php echo $util->nf($reintegro['SocioReintegro']['saldo'])?></strong>.<br/>
			La Orden de Reintegro <strong>#<?php echo $reintegro['SocioReintegro']['id']?></strong> ser&aacute; modificada al importe de $<?php echo $util->nf($ACU_TOTAL_CUOTA)?> y 
			se genera una <u>NUEVA</u> Orden de Reintegro por la diferencia a favor del socio ($<?php echo $util->nf($reintegro['SocioReintegro']['saldo'] - $ACU_TOTAL_CUOTA)?>).
		</div>
	
	<?php endif;?>

	<?php echo $frm->create(null,array('action' => 'imputar_en_ctacte/'.$socio['Socio']['id'].'/'.$reintegro['SocioReintegro']['id'].'/1'))?>	
	<?php echo $frm->hidden('SocioReintegro.accion',array('value' => 'APROBAR'))?>
	<?php echo $frm->hidden('SocioReintegro.socio_id',array('value' => $socio['Socio']['id']))?>
	<?php echo $frm->hidden('SocioReintegro.id',array('value' => $reintegro['SocioReintegro']['id']))?>
	<?php echo $frm->hidden('SocioReintegro.saldo',array('value' => $reintegro['SocioReintegro']['saldo']))?>
	<?php echo $frm->end("APROBAR")?>
	

<?php elseif(isset($this->data['SocioReintegro']['accion']) && $this->data['SocioReintegro']['accion'] == 'PREVIEW'):?>
<div class="notices_error">NO EXISTEN CUOTAS ADEUDADAS DISPONIBLES PARA APLICAR EL REINTEGRO.</div>	
<?php endif;?>
	


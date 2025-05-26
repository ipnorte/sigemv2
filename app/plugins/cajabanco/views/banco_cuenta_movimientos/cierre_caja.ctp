<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>
<h3>CIERRE PLANILLA DE CAJA</h3>
<script type="text/javascript">
Event.observe(window, 'load', function(){
	<?php if($disable_form == 1):?>
		$('form_planilla_caja').disable();
	<?php endif;?>

});
</script>
<?php echo $frm->create(null,array('action' => 'cierre_caja/' . $cuenta['BancoCuenta']['id'],'id' => 'form_planilla_caja'))?>
<div class="areaDatoForm">

	<table class="tbl_form">
	
		<tr>
			<td>FECHA ULTIMO CIERRE:</td>
			<td><?php echo $frm->calendar('BancoCuenta.fecha_conciliacion','',$cuenta['BancoCuenta']['fecha_conciliacion'],'1990',date("Y"), array('disabled' => 'disabled'))?></td>
		</tr>
		<tr>
			<td>SALDO ULTIMO CIERRE:</td>
			<td align="right"><?php echo $frm->input('BancoCuenta.saldo_conciliacion',array('value'=>number_format($cuenta['BancoCuenta']['importe_conciliacion'],2),'disabled'=> 'disabled')); ?></td>
		</tr>
		<tr>
			<td>FECHA DE CIERRE:</td>
			<td><?php echo $frm->calendar('BancoCuenta.fecha_extracto','',($cuenta['BancoCuenta']['fecha_extracto'] == '0000-00-00' ? null : $cuenta['BancoCuenta']['fecha_extracto']),'1900',date("Y")+1)?></td>
		</tr>
		
		<tr><td colspan="2"><?php echo $frm->submit("ACEPTAR")?></td></tr>
	
	</table>
	
</div>
<?php echo $frm->hidden('BancoCuenta.id',array('value' => $BancoCuentaId));?>
<?php echo $frm->end()?>

<?php if($show_asincrono == 1):?>

	<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'cierre_planilla_caja',
											'accion' => '.cajabanco.banco_cuenta_movimientos.cierre_caja',
											'target' => '',
											'btn_label' => 'Ver Listado',
											'titulo' => "CIERRE PLANILLA DE CAJA",
											'subtitulo' => 'FECHA DE CIERRE: '.$util->armaFecha($fecha_cierre),
											'p1' => $BancoCuentaId,
											'p2' => $fecha_cierre,
											'p3' => -1
	));
	
	?>


<?php endif;?>

<?php if(isset($showTabla) && $showTabla == 1):?>
<div class="areaDatoForm">

	<table align="center" width="100%">
		<?php if($dias > 1):?> 
			<caption style="font-size: x-large;">PLANILLA CAJA DEL DIA <?php echo date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion']))?> AL DIA <?php echo date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_extracto']))?></caption>
		<?php else:?>
			<caption style="font-size: x-large;">PLANILLA CAJA DEL DIA <?php echo date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_extracto']))?></caption>
		<?php endif;?>
		<tr>
			<th style="border: 1px solid white;font-size: large;">SALDO AL INICIO</th>
			<th style="border: 1px solid white;font-size: large;">I N G R E S O</th>
			<th style="border: 1px solid white;font-size: large;">E G R E S O</th>
			<th style="border: 1px solid white;font-size: large;">SALDO AL FINAL</th>
		</tr>
		
		<tr>
			<td align="center" style="border: 1px solid black;font-size: x-large;"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'],2)?></td>
			<td align="center" style="border: 1px solid black;font-size: x-large;"><?php echo number_format($ingreso + $ingresoCheque,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: x-large;"><?php echo number_format($egreso + $ingresoCheque,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: x-large;"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'] + $ingreso - $egreso,2)?></td>
		</tr>
	</table>

<table align="center" width="100%">

	<tr border="0">
		<?php if($dias > 1):?> 
			<th style="font-size: small;">FECHA</th>
		<?php endif;?>
		<th style="font-size: small;">CONCEPTO</th>
		<th style="font-size: small;">INGRESO</th>
		<th style="font-size: small;">EGRESO</th>
		<th style="font-size: small;">TOTAL</th>
		<th style="font-size: small;">COMENTARIO</th>
	</tr>
	<?php $saldo_anterior = 'SALDO AL ' . date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_conciliacion']))?>
	<tr class="altrow">
		
		<?php if($dias > 1):?> 
			<td style="font-size: x-small;"></td>
		<?php endif;?>
		
		<td style="font-size: small;"><?php echo $saldo_anterior?></td>
		<td style="font-size: small;"></td>
		<td style="font-size: small;"></td>
		<td align="right" style="font-size: small;"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'],2) ?></td>
		<td style="font-size: small;"></td>
	</tr>
	<?php
	$saldo = $cuenta['BancoCuenta']['importe_conciliacion'];
	$i = 1;
	$ingreso = 0;
	$egreso = 0;
	$primeraVez = 0;
	foreach ($temporal as $renglon):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		if($renglon['AsincronoTemporal']['entero_2'] == 0):
			$ingreso += $renglon['AsincronoTemporal']['decimal_1'];
		else:?>
			<?php if($primeraVez == 0):
					$primeraVez = 1;?>
				<?php if($ingresoCheque > 0):?>
					<tr>
					<?php if($dias > 1):?> 
						<th style="font-size: x-small;"></th>
					<?php endif;?>
						<th style="font-size: small;">INGRESOS</th>
						<th style="font-size: small;"></th>
						<th style="font-size: small;"></th>
						<th align="right" style="font-size: small;"><?php echo number_format($ingreso,2)?></th>
						<th style="font-size: small;"></th>
					</tr>
					<tr>
					<?php if($dias > 1):?> 
						<th style="font-size: x-small;"></th>
					<?php endif;?>
						<th style="font-size: small;">EFECTIVIZACION CHEQUES</th>
						<th style="font-size: small;"></th>
						<th style="font-size: small;"></th>
						<th align="right" style="font-size: small;"><?php echo number_format($ingresoCheque,2)?></th>
						<th style="font-size: small;"></th>
					</tr>
				<?php endif;?>
				<tr>
				<?php if($dias > 1):?> 
					<th style="font-size: x-small;"></th>
				<?php endif;?>
					<th style="font-size: small;">TOTAL INGRESO</th>
					<th style="font-size: small;"></th>
					<th style="font-size: small;"></th>
					<th align="right" style="font-size: small;"><?php echo number_format($ingreso + $ingresoCheque,2)?></th>
					<th style="font-size: small;"></th>
				</tr>
			<?php endif;
			$egreso += $renglon['AsincronoTemporal']['decimal_1'];
		endif;
		$error = '';
		if($renglon['AsincronoTemporal']['entero_3'] == 1) $error = 'ERROR EN DOCUMENTO';
	?>
		<tr<?php echo $class;?> >
			<?php if($dias > 1):?> 
				<td align="center" style="font-size: x-small;"><?php echo $renglon['AsincronoTemporal']['texto_1']?></td>
			<?php endif;?>
			<td style="font-size: x-small;"><?php echo $renglon['AsincronoTemporal']['texto_3']?></br>
			    <?php echo $renglon['AsincronoTemporal']['texto_4']?>
			    <?php if(!empty($renglon['AsincronoTemporal']['texto_6']) && $renglon['AsincronoTemporal']['texto_6'] != $renglon['AsincronoTemporal']['texto_3']):?>
			    	</br>
			    	<?php echo $renglon['AsincronoTemporal']['texto_6'];
			    endif;?>
			    <?php if($renglon['AsincronoTemporal']['texto_7'] == 'CT' || $renglon['AsincronoTemporal']['texto_7'] == 'DB'):?>
			    	</br><?php
			    	echo $renglon['AsincronoTemporal']['texto_5'] . ' ' . $renglon['AsincronoTemporal']['texto_8'];
			    endif;?>
			</td>
			<td align="right" style="font-size: small;"><?php echo ($renglon['AsincronoTemporal']['entero_2'] == 0 ? number_format($renglon['AsincronoTemporal']['decimal_1'],2) : '')?>
			<td align="right" style="font-size: small;"><?php echo ($renglon['AsincronoTemporal']['entero_2'] == 1 ? number_format($renglon['AsincronoTemporal']['decimal_1'],2) : '')?>
			<td style="font-size: x-small;"></td>
			<td style="font-size: x-small;"><?php echo $error?></td>
		</tr>
	<?php endforeach; ?>	

	<?php if($primeraVez == 0):
			$primeraVez = 1;?>
		<?php if($ingresoCheque > 0):?>
			<tr>
			<?php if($dias > 1):?> 
				<th style="font-size: x-small;"></th>
			<?php endif;?>
				<th style="font-size: small;">INGRESOS</th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th align="right" style="font-size: small;"><?php echo number_format($ingreso,2)?></th>
				<th style="font-size: small;"></th>
			</tr>
			<tr>
			<?php if($dias > 1):?> 
				<th style="font-size: x-small;"></th>
			<?php endif;?>
				<th style="font-size: small;">EFECTIVIZACION CHEQUES</th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th align="right" style="font-size: small;"><?php echo number_format($ingresoCheque,2)?></th>
				<th style="font-size: small;"></th>
			</tr>
		<?php endif;?>
		<tr>
		<?php if($dias > 1):?> 
			<th style="font-size: x-small;"></th>
		<?php endif;?>
			<th style="font-size: small;">TOTAL INGRESO</th>
			<th style="font-size: small;"></th>
			<th style="font-size: small;"></th>
			<th align="right" style="font-size: small;"><?php echo number_format($ingreso + $ingresoCheque,2)?></th>
			<th style="font-size: small;"></th>
		</tr>
	<?php endif;
	
	if($egreso > 0):?>
		<?php if($ingresoCheque > 0):?>
			<tr>
				<?php if($dias > 1):?> 
					<th style="font-size: x-small;"></th>
				<?php endif;?>
				<th style="font-size: small;">EGRESO</th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th align="right" style="font-size: small;"><?php echo number_format($egreso,2)?></th>
				<th style="font-size: small;"></th>
			</tr>
			<tr>
				<?php if($dias > 1):?> 
					<th style="font-size: x-small;"></th>
				<?php endif;?>
				<th style="font-size: small;">EFECTIVIZACION CHEQUES</th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th align="right" style="font-size: small;"><?php echo number_format($ingresoCheque,2)?></th>
				<th style="font-size: small;"></th>
			</tr>
		<?php endif;?>
		<tr>
			<?php if($dias > 1):?> 
				<th style="font-size: x-small;"></th>
			<?php endif;?>
			<th style="font-size: small;">TOTAL EGRESO</th>
			<th style="font-size: small;"></th>
			<th style="font-size: small;"></th>
			<th align="right" style="font-size: small;"><?php echo number_format($egreso + $ingresoCheque,2)?></th>
			<th style="font-size: small;"></th>
		</tr>
	<?php endif;?>


	<tr>
		<?php if($dias > 1):?> 
			<th style="font-size: x-small;"></th>
		<?php endif;?>
		<th style="font-size: small;">SALDO AL <?php echo date('d/m/Y',strtotime($cuenta['BancoCuenta']['fecha_extracto']))?></th>
		<th style="font-size: small;"></th>
		<th style="font-size: small;"></th>
		<th align="right" style="font-size: small;"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'] + $ingreso - $egreso,2)?></th>
		<th style="font-size: small;"></th>
	</tr>
</table>

<table align="center" width="100%">
	<caption style="font-size: large;">LISTADO DE CHEQUES</caption>
	<tr>
		<th>INGRESO</th>
		<th>VENCIMIENTO</th>
		<th>LIBRADOR</th>
		<th>BANCO NRO.CHEQUE</th>
		<th>ENTREGADO</th>
		<th>DESTINATARIO</th>
		<th>INGRESO</th>
		<th>EGRESO</th>
	</tr>
	
	<?php 
		$i = 0;
		$primeraVez = 0;
		$ingreso_cheque = 0;
		$egreso_cheque = 0;
		$caja_cheque = 0;
		$linea_totales = 0;
		foreach($detalle as $cheque):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			if($cheque['AsincronoTemporalDetalle']['entero_2'] == 0):
				$ingreso_cheque += $cheque['AsincronoTemporalDetalle']['decimal_1'];
				$linea_totales = 1;
			else:?>
				<?php if($linea_totales == 1):?>
					<tr>
						<th style="font-size: small;"></th>
						<th style="font-size: small;"></th>
						<th style="font-size: small;"></th>
						<th style="font-size: small;">TOTAL INGRESO CHEQUES</th>
						<th style="font-size: small;"></th>
						<th style="font-size: small;"></th>
						<th align="right" style="font-size: small;"><?php echo number_format($ingreso_cheque,2)?></th>
						<th style="font-size: small;"></th>
					</tr>
				<?php endif;
				$egreso_cheque += $cheque['AsincronoTemporalDetalle']['decimal_1'];
				$linea_totales = 2;
			endif;?>
				
			<tr<?php echo $class;?> >
				<td align="center"><?php echo $cheque['AsincronoTemporalDetalle']['texto_1']?></td>
				<td align="center"><?php echo $cheque['AsincronoTemporalDetalle']['texto_2']?></td>
				<td><?php echo $cheque['AsincronoTemporalDetalle']['texto_3']?></td>
				<td><?php echo $cheque['AsincronoTemporalDetalle']['texto_5'] . 'CH. ' . $cheque['AsincronoTemporalDetalle']['texto_7']?></td>
				<?php 
					if($cheque['AsincronoTemporalDetalle']['entero_2'] == 0):?>
						<td></td>
						<td></td>
						<td align="right" style="font-size: small;"><?php echo number_format($cheque['AsincronoTemporalDetalle']['decimal_1'],2)?></td>
						<td></td>
				<?php 
					else:?>
						<td><?php echo $cheque['AsincronoTemporalDetalle']['texto_8']?></td>
						<td><?php echo $cheque['AsincronoTemporalDetalle']['texto_4']?></td>
						<td></td>
						<td align="right" style="font-size: small;"><?php echo number_format($cheque['AsincronoTemporalDetalle']['decimal_1'],2)?></td>
				<?php 
					endif;
				?>
			</tr>
	<?php		
		endforeach;
		if($linea_totales == 1):?>
			<tr>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;">TOTAL INGRESO CHEQUES</th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th align="right" style="font-size: small;"><?php echo number_format($ingreso_cheque,2)?></th>
				<th style="font-size: small;"></th>
			</tr>
		<?php endif;
		if($linea_totales == 2):?>
			<tr>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;">TOTAL EGRESO CHEQUES</th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th align="right" style="font-size: small;"><?php echo number_format($egreso_cheque,2)?></th>
			</tr><?php		
		endif;
		
		if($linea_totales == 3):?>
			<tr>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;">TOTAL CAJA CHEQUES</th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th style="font-size: small;"></th>
				<th align="right" style="font-size: small;"><?php echo number_format($caja_cheque,2)?></th>
			</tr><?php		
		endif;?>
</table>

	<table align="center" width="100%">
		<caption style="font-size: x-large;">RESUMEN FINAL</caption>
		<tr>
			<th colspan="2" style="border: 1px solid black;font-size: large;">SALDO AL INICIO</th>
			<th colspan="2" style="border: 1px solid black;font-size: large;">I N G R E S O</th>
			<th colspan="2" style="border: 1px solid black;font-size: large;">E G R E S O</th>
			<th colspan="2" style="border: 1px solid black;font-size: large;">SALDO AL FINAL</th>
		</tr>
		
		<tr>
			<td style="border: 1px solid black;font-size: small;">EFECTIVO</td>
			<td style="border: 1px solid black;font-size: small;">CHEQUE</td>
			<td style="border: 1px solid black;font-size: small;">EFECTIVO</td>
			<td style="border: 1px solid black;font-size: small;">CHEQUE</td>
			<td style="border: 1px solid black;font-size: small;">EFECTIVO</td>
			<td style="border: 1px solid black;font-size: small;">CHEQUE</td>
			<td style="border: 1px solid black;font-size: small;">EFECTIVO</td>
			<td style="border: 1px solid black;font-size: small;">CHEQUE</td>
		</tr>
		
		<tr>
			<td align="center" style="border: 1px solid black;font-size: small;"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'] - $saldo_cheque_inicial,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: small;"><?php echo number_format($saldo_cheque_inicial,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: small;"><?php echo number_format($ingreso + $ingresoCheque - $ingreso_cheque,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: small;"><?php echo number_format($ingreso_cheque,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: small;"><?php echo number_format($egreso + $ingresoCheque - $egreso_cheque,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: small;"><?php echo number_format($egreso_cheque,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: small;"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'] - $saldo_cheque_inicial + $ingreso - $ingreso_cheque - $egreso + $egreso_cheque,2)?></td>
			<td align="center" style="border: 1px solid black;font-size: small;"><?php echo number_format($saldo_cheque_inicial + $ingreso_cheque - $egreso_cheque,2)?></td>
		</tr>

		<tr>
			<th colspan="2" align="center" style="border: 1px solid black;font-size: large;"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'],2)?></th>
			<th colspan="2" align="center" style="border: 1px solid black;font-size: large;"><?php echo number_format($ingreso + $ingresoCheque,2)?></th>
			<th colspan="2" align="center" style="border: 1px solid black;font-size: large;"><?php echo number_format($egreso + $ingresoCheque,2)?></th>
			<th colspan="2" align="center" style="border: 1px solid black;font-size: x-large;"><?php echo number_format($cuenta['BancoCuenta']['importe_conciliacion'] + $ingreso - $egreso,2)?></th>
		</tr>
	</table>
	<?php 
	echo $controles->botonGenerico('/cajabanco/banco_listados/planilla_caja_salida/' . $asincrono_id . '/XLS','controles/ms_excel.png', null, array('id' => 'xls'));
	echo $controles->botonGenerico('/cajabanco/banco_listados/planilla_caja_salida/' . $asincrono_id . '/PDF','controles/pdf.png', null, array('target' => '_blank', 'id' => 'pdf'));

	echo $frm->create(null,array('action' => 'cierre_caja/' . $BancoCuentaId,'id' => 'cierre'));
	echo $frm->hidden('BancoCuenta.cerrar',array('value' => 1));
	echo $frm->hidden('BancoCuenta.asincrono_id',array('value' => $asincrono_id));
	echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'CERRAR PLANILLA CAJA','URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $cuenta['BancoCuenta']['id']));
	?>
</div>
<?php endif;?>
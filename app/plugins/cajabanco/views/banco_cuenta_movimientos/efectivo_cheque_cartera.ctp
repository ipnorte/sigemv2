<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>
<?php $mutual = strtoupper(Configure::read('APLICACION.nombre_fantasia')) ?>
<h4>EFECTIVIZAR CHEQUES EN CARTERAS</h4>

<script language="Javascript" type="text/javascript">
	var rows = <?php echo count($chqCarteras)?>;
	Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
	
	});
	
		
	function chkOnclick(){
		
		var totalSeleccionado = 0;
		
		for (i=1;i<=rows;i++){
			var celdas = $('TRL_' + i).immediateDescendants();
			oChkCheck = document.getElementById('BancoCuentaMovimientoSeleccion_' + i);
			if (oChkCheck.checked){
				totalSeleccionado = totalSeleccionado + parseInt(oChkCheck.value);
				celdas.each(function(td){td.addClassName("selected");});
			}else{
				celdas.each(function(td){td.removeClassName("selected");});
			}	
		}
		
		$('btn_submit').disable();
		if(totalSeleccionado > 0) $('btn_submit').enable();
		
		totalSeleccionado = FormatCurrency(totalSeleccionado/100);
		document.getElementById('BancoCuentaMovimientoImporteChequeMostrar').value = totalSeleccionado;
		document.getElementById('BancoCuentaMovimientoImporteCheque').value = totalSeleccionado;
	}

	
</script>


<?php echo $frm->create(null,array('name'=>'formMovimiento','id'=>'formMovimiento', 'action' => "efectivo_cheque_cartera/" . $cuenta['BancoCuenta']['id'] ));?>
	<div class="areaDatoForm">
		<table class="tbl_form">
			<tr id="fOpera">
				<td>FECHA OPERACION:</td>
				<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_operacion',null,null,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr>
				<table id="chq_cartera">
					<tr>
						<th>#</th>
						<th>BANCO</th>
						<th>FECHA</th>
						<th>VENCIMIENTO</th>
						<th>NRO.CHEQUE</th>
						<th>LIBRADOR</th>
						<th>IMPORTE</th>
						<th></th>
						
					</tr>
					<?php foreach($chqCarteras as $chqCartera):?>
						<?php $i++;?>
						<tr id="TRL_<?php echo $i?>">
							<td align="center"><?php echo $chqCartera['BancoChequeTercero']['id']?></td>
							<td><?php echo $chqCartera['BancoChequeTercero']['banco']?></td>
							<td><?php echo $util->armaFecha($chqCartera['BancoChequeTercero']['fecha_ingreso'])?></td>
							<td><?php echo $util->armaFecha($chqCartera['BancoChequeTercero']['fecha_vencimiento'])?></td>
							<td><?php echo $chqCartera['BancoChequeTercero']['numero_cheque'] ?></td>
							<td><?php echo $chqCartera['BancoChequeTercero']['librador'] ?></td>
							<td align="right"><strong><?php echo number_format($chqCartera['BancoChequeTercero']['importe'],2)?></strong></td>
							<td><input type="checkbox" name="data[BancoCuentaMovimiento][id_check][<?php echo $chqCartera['BancoChequeTercero']['id'] ?>]" value="<?php echo number_format(round($chqCartera['BancoChequeTercero']['importe'],2) * 100,0,".","")?>" id="BancoCuentaMovimientoSeleccion_<?php echo $i ?>" onclick="toggleCell('TRL_<?php echo $i?>',this); chkOnclick()"/></td>
						</tr>
					<?php endforeach;?>	
					<tr class='totales'>
						<td colspan="5"></td>
						<td align="right">TOTAL</td>
						<td align="right"><?php echo $frm->number('BancoCuentaMovimiento.importe_cheque_mostrar',array('size'=>12,'maxlength'=>12, 'disabled' => 'disabled'));?></td>
					</tr>
				</table>
			</tr>
		</table>
		
	</div>

	<div>
		<?php echo $frm->hidden("BancoCuentaMovimiento.importe_cheque", array('value' => 0.00)); ?>
		<?php echo $frm->hidden("BancoCuentaMovimiento.banco_cuenta_id", array('value' => $cuenta['BancoCuenta']['id'])); ?>
		<?php echo $frm->btnGuardarCancelar(array('URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $cuenta['BancoCuenta']['id']))?>
	</div>
	
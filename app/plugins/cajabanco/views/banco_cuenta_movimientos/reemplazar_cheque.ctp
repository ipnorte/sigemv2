<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>
<div class="areaDatoForm">
	<h4>DATOS DEL CHEQUE</h4>
	<table class="tbl_form">
		<tr>
			<td>NRO. DE CHEQUE:</td>
			<td><?php echo $movimiento['numero_operacion']?></td>
		</tr>
		<tr>
			<td>BANCO Y CUENTA:</td>
			<td><?php echo $movimiento['banco_cuenta']?></td>
		</tr>
		<tr>
			<td>FECHA EMISION:</td>
			<td><?php echo $movimiento['fecha_operacion']?></td>
		</tr>
		<tr>
			<td>FECHA VENCIMIENTO:</td>
			<td><?php echo $movimiento['fecha_vencimiento']?></td>
		</tr>
		<tr>
			<td>DESTINATARIO:</td>
			<td><?php echo $movimiento['destinatario']?></td>
		</tr>
		<tr>
			<td>DESCRIPCION:</td>
			<td><?php echo $movimiento['descripcion']?></td>
		</tr>
		<tr>
			<td>IMPORTE:</td>
			<td><?php echo $movimiento['importe']?></td>
		</tr>
	</table>
</div>
<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {

		$('BancoCuentaMovimientoImporteCheque').disable();
		seleccionarCuenta();
//		$('btn_submit').disable();
//		ocultarOption();
	
	});


	function seleccionarCuenta(){
		var seleccion = $('BancoCuentaMovimientoRescatarBancoCuentaId').getValue();
		var tipo = seleccion.substr(0,1);
		
		ocultarOpcion();

		if(tipo == 'C')
		{
			$('fCaja').show();
		}
		else
		{
			$('a_orden').show();
			$('nro_cheque').show();
			$('fOpera').show();
			$('fVenc').show();
		}
//		endif;
	}


	function ocultarOpcion()
	{
		$('a_orden').hide();
		$('nro_cheque').hide();
		$('fOpera').hide();
		$('fCaja').hide();
		$('fVenc').hide();
	}
</script>


<?php echo $frm->create(null,array('name'=>'formMovimiento','id'=>'formMovimiento', 'action' => "reemplazar_cheque/" . $movimiento['id'] ));?>
	<div class="areaDatoForm">
		<h4>REEMPLAZAR POR</h4>
		<table class="tbl_form">
			<tr id="cta_banco">
				<td>CUENTA BANCARIA:</td>
				<td><?php echo $this->renderElement('banco_cuentas/combo_cuentas',array(
										'plugin'=>'cajabanco',
										'label' => "",
										'model' => 'BancoCuentaMovimiento.rescatar_banco_cuenta_id',
										'disabled' => false,
										'empty' => false,
										'caja' => true,
										'onChange' => 'seleccionarCuenta()',
										'selected' => 0))?>
				</td>			
			</tr>
			<tr id="descri">
				<td>DESCRIPCION:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.descripcion', array('label'=>'','size'=>60,'maxlength'=>50, 'value' => 'REEMPLAZO CH. ' . $movimiento['numero_operacion'] . ' CTA: ' .$movimiento['cuenta_str'])) ?></td>
			</tr>
			<tr id="a_orden">
				<td>A LA ORDEN DE:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.destinatario', array('label'=>'','size'=>60,'maxlength'=>50, 'value' => $movimiento['destinatario'])) ?></td>
			</tr>
			<tr id="nro_cheque">
				<td>NRO. CHEQUE:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.numero_cheque', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
			</tr>
			<tr id="fOpera">
				<td>FECHA EMISION:</td>
				<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_operacion',null,null,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr id="fVenc">
				<td>FECHA VENCIMIENTO:</td>
				<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_vencimiento',null,null,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr id="fCaja">
				<td>FECHA:</td>
				<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_caja',null,null,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr id="importe">
				<td>IMPORTE:</td>
				<td><?php echo $frm->money('BancoCuentaMovimiento.importe_cheque','', $movimiento['importe']) ?></td>
			</tr>
		</table>
		
	</div>

	<div>
		<?php echo $frm->hidden("BancoCuentaMovimiento.banco_cuenta_id", array('value' => $movimiento['banco_cuenta_id'])); ?>
		<?php echo $frm->hidden("BancoCuentaMovimiento.id", array('value' => $movimiento['id'])); ?>
		<?php echo $frm->hidden("BancoCuentaMovimiento.importe", array('value' => $movimiento['importe'])); ?>
		<?php echo $frm->btnGuardarCancelar(array('URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $cuenta['BancoCuenta']['id']))?>
	</div>
	
<?php
	$nroComprobante = str_pad($movimiento[0]['BancoCuentaMovimiento']['id'], 12, 0, STR_PAD_LEFT);
	$nroComprobante = substr($nroComprobante,0,4) . '-' . substr($nroComprobante,-8);
?>
<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {
		<?php if($movimiento[0]['BancoCuentaMovimiento']['orden_pago_id'] > 0 || $movimiento[0]['BancoCuentaMovimiento']['recibo_id'] > 0 || 
				$movimiento[0]['BancoCuentaMovimiento']['cancelacion_orden_id'] > 0 || $movimiento[0]['BancoCuentaMovimiento']['orden_caja_cobro_id'] > 0):?>
			$('btn_submit').disable();
		<?php endif;?>
	
	});



</script>

<h1>COMPROBANTE NRO.: <?php echo $nroComprobante ?></h1>

<?php if($documento[0]['tipo'] == 1):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/emision_cheque',array('movimiento' => $documento))?>
<?php endif;?>


<?php if($documento[0]['tipo'] == 2):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/deposito_bancario',array('movimiento' => $documento))?>
<?php endif;?>


<?php if($documento[0]['tipo'] == 3):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/extraccion_fondo',array('movimiento' => $documento))?>
<?php endif;?>


<?php if($documento[0]['tipo'] == 4):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/traspaso_fondo',array('movimiento' => $documento))?>
<?php endif;?>


<?php if($documento[0]['tipo'] == 5):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/transferencia_bancaria',array('movimiento' => $documento))?>
<?php endif;?>


<?php if($documento[0]['tipo'] == 6):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/banco',array('movimiento' => $documento))?>
<?php endif;?>


<?php if($documento[0]['tipo'] == 7):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/caja',array('movimiento' => $documento))?>
<?php endif;?>



<div class="areaDatoForm">
	<?php echo $frm->create(null,array('name'=>'formRecibo','id'=>'formRecibo', 'action' => 'edit_fecha/' . $movimiento[0]['BancoCuentaMovimiento']['id']));?>

		<div class="areaDatoForm">
		
			<table class="tbl_form">
			
				<tr>
					<td>FECHA NUEVA:</td>
					<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_operacion','',$movimiento[0]['BancoCuentaMovimiento']['fecha_operacion'],'1900',date("Y")+1)?></td>
				</tr>
				
			</table>
			
		</div>


	<?php 
		echo $frm->hidden('BancoCuentaMovimiento.id', array('value' => $movimiento[0]['BancoCuentaMovimiento']['id']));
		echo $frm->hidden('BancoCuentaMovimiento.banco_cuenta_id', array('value' => $movimiento[0]['BancoCuentaMovimiento']['banco_cuenta_id']));
		echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'CAMBIAR FECHA', 'TXT_CANCELAR' => 'REGRESAR', 'URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $movimiento[0]['BancoCuentaMovimiento']['banco_cuenta_id']))
	?>
</div>



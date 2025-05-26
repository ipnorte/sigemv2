<?php
	$nroComprobante = str_pad($movimiento[0]['id'], 12, 0, STR_PAD_LEFT);
	$nroComprobante = substr($nroComprobante,0,4) . '-' . substr($nroComprobante,-8);
?>
<script language="Javascript" type="text/javascript">
	Event.observe(window, 'load', function() {
		<?php if($movimiento[0]['orden_pago_id'] > 0 || $movimiento[0]['recibo_id'] > 0 || 
				$movimiento[0]['cancelacion_orden_id'] > 0 || $movimiento[0]['orden_caja_cobro_id'] > 0):?>
			$('btn_submit').disable();
		<?php endif;?>
	
	});



</script>

<h1>COMPROBANTE NRO.: <?php echo $nroComprobante ?></h1>

<?php if($movimiento[0]['tipo'] == 1):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/emision_cheque',array('movimiento' => $movimiento))?>
<?php endif;?>


<?php if($movimiento[0]['tipo'] == 2):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/deposito_bancario',array('movimiento' => $movimiento))?>
<?php endif;?>


<?php if($movimiento[0]['tipo'] == 3):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/extraccion_fondo',array('movimiento' => $movimiento))?>
<?php endif;?>


<?php if($movimiento[0]['tipo'] == 4):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/traspaso_fondo',array('movimiento' => $movimiento))?>
<?php endif;?>


<?php if($movimiento[0]['tipo'] == 5):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/transferencia_bancaria',array('movimiento' => $movimiento))?>
<?php endif;?>


<?php if($movimiento[0]['tipo'] == 6):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/banco',array('movimiento' => $movimiento))?>
<?php endif;?>


<?php if($movimiento[0]['tipo'] == 7):?>
	<?php echo $this->renderElement('banco_cuenta_movimientos/caja',array('movimiento' => $movimiento))?>
<?php endif;?>



<?php // $movimiento['BancoCuentaMovimiento']['tipo'] == 1 && $movimiento['BancoCuentaMovimiento']['anulado'] == 0 &&
	if( 
	  !($movimiento[0]['orden_pago_id'] > 0 || $movimiento[0]['recibo_id'] > 0 || 
		$movimiento[0]['cancelacion_orden_id'] > 0 || $movimiento[0]['orden_caja_cobro_id'] > 0)):
		echo $controles->btnImprimirPDF('IMPRIMIR COMPROBANTE','/cajabanco/banco_cuenta_movimientos/imprimir_comprobante_pdf/'.$movimiento[0]['id'],'blank');
	endif;
?>
<?php if(!($movimiento[0]['orden_pago_id'] > 0 || $movimiento[0]['recibo_id'] > 0 || 
		$movimiento[0]['cancelacion_orden_id'] > 0 || $movimiento[0]['orden_caja_cobro_id'] > 0)):?>
	<div class="areaDatoForm">
	<?php echo $frm->create(null,array('name'=>'formRecibo','id'=>'formRecibo', 'action' => 'delete/' . $movimiento[0]['id'] . '/' . $movimiento[0]['banco_cuenta_id']));?>
	<?php 
		echo $frm->hidden('BancoCuentaMovimiento.id', array('value' => $movimiento[0]['id']));
                if(!$movimiento[0]['conciliado']){
                    echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ELIMINAR', 'TXT_CANCELAR' => 'REGRESAR', 'URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $movimiento[0]['banco_cuenta_id']));
                }else{
                    echo $frm->btnForm(array('LABEL' => 'REGRESAR','URL' =>  '/cajabanco/banco_cuenta_movimientos/resumen/' . $movimiento[0]['banco_cuenta_id']));
                }
		
	?>
	</div>
<?php endif;?>


<?php // debug($movimiento)?>
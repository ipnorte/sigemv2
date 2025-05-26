<?php echo $this->renderElement('head',array('title' => 'PROCESO DE FACTURACION A PROVEEDORES'))?>
<?php 
	if($liquidacion['Liquidacion']['facturada'] == 1):
		echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'));
	endif;
?>

<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>

<?php 
	if($liquidacion['Liquidacion']['facturada'] == 0):
		if(isset($anular)):
			if($anular):
				echo $this->renderElement('msg',array('msg' => array('OK' => 'EL PROCESO DE ANULACION TERMINO CON EXITO')));
			else:
				echo $this->renderElement('msg',array('msg' => array('ERROR' => 'ERROR AL ANULAR LA FACTURACION')));
			endif;
		endif;
	endif;
?>
<div class="row">
	<?php echo $controles->btnRew('Regresar','/mutual/liquidaciones/reporte_proveedores/' . $liquidacion['Liquidacion']['id'] . '/?pid=' . $PID)?>
</div>

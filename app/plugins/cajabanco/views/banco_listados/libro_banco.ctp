<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>

<script language="Javascript" type="text/javascript">
	
	Event.observe(window, 'load', function(){
		<?php if($showForm == 1):?>
			$('libro_banco').disable();
		<?php endif;?>
	
	});

	function CtrlFecha(){
		var fecha_desde  = $('ListadoFechaDesdeYear').getValue() + '-' + $('ListadoFechaDesdeMonth').getValue() + '-' + $('ListadoFechaDesdeDay').getValue();

		if(fecha_desde <= '<?php echo $fecha_desde?>')
		{
			alert('LA FECHA DEBE SER MAYOR A LA FECHA DE ALTA DE LA CUENTA');
			$('ListadoFechaDesdeDay').focus();
			return false;
		}

		return true;
	}
	

	
</script>

<h4>LIBRO BANCO</h4>

<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'libro_banco/' . $cuenta['BancoCuenta']['id'],'id' => 'libro_banco', 'onsubmit' => "return CtrlFecha()"))?>
	<table class="tbl_form">
		<tr>
			<td>FECHA DESDE:</td>
			<td><?php echo $frm->calendar('Listado.fecha_desde','',$fecha_desde,'1990',date("Y") + 1)?></td>
			<td>FECHA HASTA:</td>
			<td><?php echo $frm->calendar('Listado.fecha_hasta','',$fecha_hasta,'1990',date("Y") + 1)?></td>
		</tr>
	</table>
	<?php
		echo $frm->hidden('BancoCuenta.id',array('value' => $cuenta['BancoCuenta']['id']));
		echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ACEPTAR','URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $BancoCuentaId));
	?>
</div>

<?php 
	if($showForm == 1):
		echo $controles->botonGenerico('/cajabanco/banco_listados/libro_banco_salida/' . $cuenta['BancoCuenta']['id'] . '/' . $fecha_desde . '/' . $fecha_hasta . '/XLS','controles/ms_excel.png');
		echo $controles->botonGenerico('/cajabanco/banco_listados/libro_banco_salida/' . $cuenta['BancoCuenta']['id'] . '/' . $fecha_desde . '/' . $fecha_hasta . '/PDF','controles/pdf.png', null, array('target' => '_blank'));
		
		echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_banco', array('cuenta' => $cuenta, 'movimientos' => $movimientos, 'conciliacion' => 1));
	endif;
?>

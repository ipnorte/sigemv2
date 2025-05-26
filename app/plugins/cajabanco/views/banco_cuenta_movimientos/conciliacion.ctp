<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>
<?php 
	$saldo_conciliacion = $cuenta['BancoCuenta']['importe_conciliacion'];
	if($cuenta['BancoCuenta']['tipo_conciliacion'] == 1) $saldo_conciliacion *= -1;
?>

<script language="Javascript" type="text/javascript">
	
	Event.observe(window, 'load', function(){
		<?php if($conciliar == 1 && (!isset($cerrar) || $cerrar == 0)):?>
			$('conciliacion').disable();
			$('btn_submit').disable();
			ok_conciliacion();
		<?php endif;?>

		
		<?php if(isset($cerrar) && $cerrar == 1):?>
			$('conciliacion').disable();
			ok_cerrar();
		<?php endif;?>

	
	});

	function CtrlConciliacion(){
		var numero_extracto = $('BancoCuentaNumeroExtracto').getValue();
		var fecha_extracto  = $('BancoCuentaFechaExtractoYear').getValue() + '-' + $('BancoCuentaFechaExtractoMonth').getValue() + '-' + $('BancoCuentaFechaExtractoDay').getValue();
		var saldo_extracto  = $('BancoCuentaSaldoExtracto').getValue();

		if(numero_extracto == '')
		{
			alert('INGRESE EL NUMERO DE EXTRACTO BANCARIO');
			$('BancoCuentaNumeroExtracto').focus();
			return false;
		}

		if(fecha_extracto <= '<?php echo $cuenta['BancoCuenta']['fecha_conciliacion']?>')
		{
			alert('LA FECHA DEBE SER MAYOR AL ULTIMO CONCILIADO');
			$('BancoCuentaFechaExtractoDay').focus();
			return false;
		}

		return true;
	}
	

	function ok_conciliacion()
	{
		var saldo_banco = new Number($('BancoCuentaSaldoConciliado').getValue());
		var saldo_extracto = new Number($('BancoCuentaSaldoExtracto').getValue());
		var okBanco = $('ok_banco');
		var okExtracto = $('ok_extracto');

		
		saldo_banco = saldo_banco.toFixed(2);
		saldo_extracto = saldo_extracto.toFixed(2);
		
		if(saldo_banco == saldo_extracto)
		{
			okBanco.style.display="";
			okExtracto.style.display="";
			alert('ATENCION: Los saldos han sido conciliados.\nPuede cerrar la conciliacion.');
			$('btn_submit').enable();
		}
		else
		{
			okBanco.style.display="none";
			okExtracto.style.display="none";
			$('btn_submit').disable();
		}
		

		return true;
	}

	function ok_cerrar()
	{
		var okBanco = $('ok_banco');
		var okExtracto = $('ok_extracto');

		okBanco.style.display="";
		okExtracto.style.display="";

		return true;
	}
	
</script>

<?php 
	if($conciliar == 1 && (!isset($cerrar) || $cerrar == 0)):
?>
		<h3>GRILLA DE CONCILIACION</h3>
<?php 
		echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_conciliacion', array('cuenta' => $cuenta, 'movimientos' => $movimientos));
	endif;
?>
		
<h4>CONCILIACION</h4>

<div class="areaDatoForm">
	<?php echo $frm->create(null,array('action' => 'conciliacion/' . $BancoCuentaId,'id' => 'conciliacion', 'onsubmit' => "return CtrlConciliacion()"))?>
	<table class="tbl_form">
		<tr>
			<td width="35%">
				<table width="100%">
					<tr>
						<th colspan="2">ULTIMA CONCILIACION</th>
					</tr>
					<tr>
						<td colspan="2"><br/></td>
					</tr>
					<tr>
						<td>FECHA:</td>
						<td><?php echo $frm->calendar('BancoCuenta.fecha_conciliacion','',$cuenta['BancoCuenta']['fecha_conciliacion'],'1990',date("Y"), array('disabled' => 'disabled'))?></td>
					</tr>
					<tr>
						<td>SALDO:</td>
						<td align="right"><?php echo $frm->input('BancoCuenta.saldo_conciliacion',array('value'=>number_format($saldo_conciliacion,2),'disabled'=> 'disabled')); ?></td>
					</tr>
				</table>
			</td>

			<td id="td_conciliacion" width="30%">
				
				<div id="div_conciliacion">
					<table>
						<tr>
							<th colspan="2">CONCILIACION</th>
						</tr>
						<tr>
							<td>SALDO ANTERIOR:</th>
							<td><?php echo $frm->input('BancoCuenta.saldo_anterior',array('value'=>number_format($saldos['saldo_anterior'],2),'disabled'=> 'disabled')); ?></td>
						</tr>
						<tr>
							<td align="right">SALDO LIBRO BANCO:</td>
							<td><?php echo $frm->input('BancoCuenta.saldo_libro',array('value'=>number_format($saldos['saldo_libro'],2),'disabled'=> 'disabled')); ?></td>
						</tr>
						<tr>
							<td align="right">SALDO NO CONCILIADO:</td>
							<td><?php echo $frm->input('BancoCuenta.saldo_no_conciliado',array('value'=>number_format($saldos['saldo_no_conciliado'],2),'disabled'=> 'disabled')); ?></td>
						</tr>
						<tr>
							<td align="right">SALDO BANCO:</td>
							<td><?php echo $frm->input('BancoCuenta.saldo_banco',array('value'=>number_format($saldos['saldo_banco'],2),'disabled'=> 'disabled')); ?>
								<div id="ok_banco" style="display:none"><img src="<?php echo $this->base?>/img/controles/ok.png" border="0" alt="" /></div>
							</td>
							
						</tr>
					</table>
				</div>
			</td>

			<td width="35%">
				<table>
					<tr>
						<th colspan="2">EXTRACTO BANCO</th>
					</tr>
					<tr>
						<td>NUMERO O IDENTIFICACION:</th>
						<td><?php echo $frm->input('BancoCuenta.numero_extracto',array('label'=>'','size'=>32,'maxlength'=>30, 'value' => $cuenta['BancoCuenta']['numero_extracto'])); ?></td>
					</tr>
					<tr>
						<td align="right">FECHA:</td>
						<td><?php echo $frm->calendar('BancoCuenta.fecha_extracto','',$cuenta['BancoCuenta']['fecha_extracto'],'1990',date("Y"))?></td>
					</tr>
					<tr>
						<td align="right">SALDO:</td>
						<td><?php echo $frm->money('BancoCuenta.saldo_extracto','',$cuenta['BancoCuenta']['saldo_extracto'], true) ?>
							<div id="ok_extracto" style="display:none"><img src="<?php echo $this->base?>/img/controles/ok.png" border="0" alt="" /></div>
						</td>
					</tr>
				</table>
			</td>

		</tr>
		
<!--		<tr><td colspan="2"><?php // echo $frm->submit("GENERAR LISTADO")?></td></tr>-->
	</table>
	<?php //echo $frm->end()?>
</div>

<?php 
echo $frm->hidden('BancoCuenta.saldo_conciliado',array('value' => $saldos['saldo_banco']));
if($conciliar == 0):
	echo $frm->hidden('BancoCuenta.id',array('value' => $BancoCuentaId));
	echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'ACEPTAR','URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $BancoCuentaId));
elseif($conciliar == 1 && (!isset($cerrar) || $cerrar == 0)):
	echo $frm->create(null,array('action' => 'conciliacion/' . $BancoCuentaId,'id' => 'conciliado', 'onsubmit' => "return CtrlConciliacion()"));
	echo $frm->hidden('BancoCuenta.cerrar',array('value' => 1));
	echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'CERRAR CONCILIACION','URL' => '/cajabanco/banco_cuenta_movimientos/resumen/' . $BancoCuentaId));
else:
?>
<h3>CERRAR CONCILIACION</h3>
<?php 
	echo $this->renderElement('show',array(
											'plugin' => 'shells',
											'process' => 'cerrar_conciliacion_banco',
											'accion' => '.cajabanco.listados.view_conciliacion',
											'btn_label' => 'Resumen Conciliacion',
											'titulo' => "CIERRE DE CONCILIACION AL ".$util->armaFecha($cuenta['BancoCuenta']['fecha_extracto']),
											'subtitulo' => 'Saldo Extracto Banco $ '.number_format($cuenta['BancoCuenta']['saldo_extracto'],2),
											'p1' => $cuenta['BancoCuenta']['id']
							));
	
endif;
?>

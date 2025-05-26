<?php 
if($menuPersonas == 1) {echo $this->renderElement('personas/padron_header',array('persona' => $socio,'plugin'=>'pfyj'));}
else {echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$socio,'link'=>true,'plugin' => 'pfyj'));}
?>

<h3>ABONAR REINTEGRO AL SOCIO</h3>
<?php echo $this->renderElement('orden_descuento/opciones_vista_estado_cta',array('menuPersonas' => $menuPersonas,'persona_id' => $socio['Persona']['id'],'socio_id' => $socio['Socio']['id'],'plugin' => 'mutual'))?>
<div class="actions"><?php echo $controles->btnRew('Regresar','by_socio/'.$socio['Socio']['id'])?></div>
<?php if(!empty($reintegros)):?>
<script language="Javascript" type="text/javascript">
var rows = <?php echo count($reintegros)?>;

Event.observe(window, 'load', function() {
		$('btn_preview').disable();
		selUnSelAll(false,false);

		$('cmb_banco').hide();
		$('nro_operacion').hide();
		$('SocioReintegroPagoBancoId').disable();
		$('SocioReintegroPagoNroOperacion').disable();

		$('SocioReintegroPagoFormaPago').observe('change',function(){

			var fpago = $('SocioReintegroPagoFormaPago').getValue();
			
			if(fpago == 'MUTUFPAG0001'){
				$('cmb_banco').hide();
				$('nro_operacion').hide();
				$('SocioReintegroPagoBancoId').disable();
				$('SocioReintegroPagoNroOperacion').disable();
			}else{
				$('cmb_banco').show();
				$('nro_operacion').show();
				$('SocioReintegroPagoBancoId').enable();
				$('SocioReintegroPagoNroOperacion').enable();
			}

		});
		
});

function selUnSelAll(status,soloVencida){
	for (i=1;i<=rows;i++){
		oChkCheck = document.getElementById('SocioReintegroId_' + i);
		oChkCheck.checked = false;
		if(soloVencida){
			vencida = document.getElementById('SocioReintegroId_' + i).value;
			if(vencida == 1)oChkCheck.checked = status;
		}else{
			oChkCheck.checked = status;
		}	
	}
	SelSum();
}

function chkOnclick(){
	  SelSum();
}

function SelSum(){
	
	var totalSeleccionado = new Number(0);
	
	for (i=1;i<=rows;i++){
		var celdas = $('TRL_' + i).immediateDescendants();
		oChkCheck = document.getElementById('SocioReintegroId_' + i);

		toggleCell('TRL_' + i,oChkCheck);
		
		if (oChkCheck.checked){
			numValTxt = new Number(oChkCheck.value);
			impoTxt = numValTxt.toFixed(0);
			totalSeleccionado = totalSeleccionado + parseInt(impoTxt);
//			totalSeleccionado = totalSeleccionado + parseInt(oChkCheck.value);
		}	
	}
	
	totSel = FormatCurrency(totalSeleccionado/100);
	document.getElementById('SocioReintegroPagoImporte').value = totSel;
	if(totalSeleccionado > 0)$('btn_preview').enable();
	else $('btn_preview').disable();
	$('total_seleccionado').update(totSel);
}

function validateForm(){
	var ret = true;
	var fpago = $('SocioReintegroPagoFormaPago').getValue();
	if(fpago != 'MUTUFPAG0001') ret = validRequired("SocioReintegroPagoNroOperacion","");
	return ret;
}

</script>
<?php echo $frm->create(null,array('action' => 'generar_opago/'.$socio['Socio']['id'],'onsubmit' => "return validateForm()"))?>

	<table>
		<tr>
			<th>#</th>
			<th>TIPO</th>
			<th>LIQUIDACION</th>
			<th>IMPORTE DEBITADO</th>
			<th>IMPORTE IMPUTADO</th>
			<th>IMPORTE REINTEGRO</th>
			<th></th>
		</tr>
		<?php $i=0;?>
		<?php foreach($reintegros as $reintegro):?>
			<?php $i++;?>
			<tr id="TRL_<?php echo $i?>">
				<td><?php echo $reintegro['SocioReintegro']['id']?></td>
				<td><strong><?php echo $reintegro['SocioReintegro']['tipo']?></strong></td>
				<td><?php echo $reintegro['SocioReintegro']['liquidacion_str']?></td>
<!--				<td><?php //   echo $controles->linkModalBox($reintegro['SocioReintegro']['liquidacion_str'],array('title' => 'LIQUIDACION ' . $reintegro['SocioReintegro']['liquidacion_str'],'url' => '/mutual/liquidaciones/by_socio_periodo/'.$reintegro['SocioReintegro']['socio_id'].'/'.$reintegro['SocioReintegro']['periodo'],'h' => 450, 'w' => 850))?></td>-->
				<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_debitado'])?></td>
				<td align="right"><?php echo $util->nf($reintegro['SocioReintegro']['importe_imputado'])?></td>
				<td align="right"><strong><?php echo $util->nf($reintegro['SocioReintegro']['importe_reintegro'])?></strong></td>
				<td align="center">
				<input type="checkbox" name="data[SocioReintegro][id][<?php echo $reintegro['SocioReintegro']['id']?>]" value="<?php echo number_format($reintegro['SocioReintegro']['importe_reintegro'] * 100,0,".","")?>" id="SocioReintegroId_<?php echo $i?>" onclick="chkOnclick()"/>
				</td>
			</tr>
		<?php endforeach;?>
		<tr class="totales">
			<th colspan="4">TOTAL A ABONAR AL SOCIO</th>
			<th><h1 id="total_seleccionado">0.00</h1></th>
			<th></th>
		</tr>
		<tr>
			<td colspan="7">
				<table class="tbl_form">
					<tr>
						<td>FORMA DE PAGO</td>
						<td>
						<?php echo $this->renderElement('global_datos/combo',array(
																						'plugin'=>'config',
																						'label' => " ",
																						'model' => 'SocioReintegroPago.forma_pago',
																						'prefijo' => 'MUTUFPAG',
																						'disable' => false,
																						'empty' => false,
																						'selected' => 0,
																						'logico' => true,
						))?>						
						</td>
					</tr>
					<tr id="cmb_banco">
						<td>BANCO</td>
						<td>
							<?php echo $this->renderElement('banco/combo',array(
																				'plugin'=>'config',
																				'label' => " ",
																				'model' => 'SocioReintegroPago.banco_id',
																				'disable' => false,
																				'empty' => FALSE,
																				'tipo' => 0
							))?>				
						</td>
					</tr>					
					<tr id="nro_operacion">
						<td>NRO.OPERACION</td>
						<td colspan="2"><?php echo $frm->input('SocioReintegroPago.nro_operacion',array('size'=>20,'maxlenght'=>20)); ?></td>
					</tr>
					<tr>
  						<td>FECHA DE PAGO</td>
  						<td><?php echo $frm->calendar('SocioReintegroPago.fecha_operacion','',date('Y-m-d'),date('Y'),date('Y') + 1)?></td>
					</tr>					
				</table>
			</td>
		</tr>		
		<tr>
			<td colspan="7" align="center"><?php echo $frm->submit("ABONAR EL IMPORTE AL SOCIO",array('id' => 'btn_preview'))?></td>
		</tr>		
	</table>
	<?php echo $frm->hidden('SocioReintegroPago.socio_id',array('value' => $socio['Socio']['id']))?>
	<?php echo $frm->hidden('SocioReintegroPago.importe',array('value' => 0))?>
</form>


<?php else:?>
	<h4>SIN REINTEGROS PENDIENTES DE PROCESAR</h4>
<?php endif;?>
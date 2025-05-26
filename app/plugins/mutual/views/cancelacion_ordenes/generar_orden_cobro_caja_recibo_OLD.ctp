<?php echo $this->renderElement('head',array('title' => 'ORDEN DE CANCELACION :: COBRO POR CAJA','plugin' => 'config'))?>
<?php echo $this->renderElement('personas/tdoc_apenom',array('persona'=>$persona,'link'=>true,'plugin' => 'pfyj'))?>
<div class="row">
	<?php echo $controles->btnRew('Regresar','/mutual/cancelacion_ordenes/generar/'.$persona['Persona']['id'])?>
</div>
<?php // debug($cancelaciones) ?>

<?php if(count($cancelaciones) != 0):
$socio_id = $cancelaciones[0]['CancelacionOrden']['socio_id'];?>
<script language="Javascript" type="text/javascript">
	var rows = <?php echo count($cancelaciones)?>;
        var vproveedorid = <?php echo $MutualProveedorId?>;
        
	Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
		document.getElementById("CancelacionOrdenImporte").value = 0.00;
		$("respo").hide();
		ocultarComercio();
		ocultarOptionFCobro();
	
	});

	function ocultarComercio(){
		$("fecha").hide();
		$("observa").hide();
		$("compensa").hide();
		$("forma").hide();
	}
	
	
	function seleccionComercio(){
		var comercio = $('CancelacionOrdenProveedorOrigenId').getValue();

		ocultarComercio();

		if(comercio != 0){
			$("fecha").show();
			$("observa").show();
//			if(comercio != 18){
			if(comercio != vproveedorid){
				$("compensa").show();
			}
			$("forma").show();
		}
	}
		
	function chkOnclick(){
		selSum();

		acumulado = parseFloat($('acumulado').getValue());
		cobro = document.getElementById("CancelacionOrdenImporteCobro").value;
		cobro = parseFloat(cobro);
		resto = cobro - acumulado;
		if(resto == 0) $('btn_submit').enable();
		else $('btn_submit').disable();
		document.getElementById("CancelacionOrdenImporte").value = resto;
		document.getElementById("CancelacionOrdenTipoCobro").value = "";
		ocultarOptionFCobro();
		
	}	

	function selSum(){	
		var totalSeleccionado = 0;
		var importeCobro = $('CancelacionOrdenImporteCobro').getValue();
		var error = "";	
		var chequeado = 0;
		
		$("respo").hide();
		ocultarComercio();
		ocultarOptionFCobro();
		
		for (i=1;i<=rows;i++){
			var celdas = $('TRL_' + i).immediateDescendants();
			oChkCheck = document.getElementById('CancelacionOrdenSaldo_' + i);
			oTxtImpoCobrar = document.getElementById('ReciboImporteACobrar_' + i);
			if (oChkCheck.checked){
				chequeado = 1;
				
				strChkTxt = oChkCheck.value;
				numChkTxt = new Number(strChkTxt);
				numChkTxt = numChkTxt / 100;
				numChkTxt = numChkTxt.toFixed(2);
	
				chkValue = parseInt(oChkCheck.value);
				totalSeleccionado = totalSeleccionado + chkValue;
	
				celdas.each(function(td){td.addClassName("selected");});
			}else{
				celdas.each(function(td){td.removeClassName("selected");});
			}	
		}
		
		if(chequeado == 1){
			$("respo").show();
			seleccionComercio();
		}
		
		totalSeleccionado = totalSeleccionado/100;
		
		document.getElementById('CancelacionOrdenImporteCobro').value = totalSeleccionado;
		document.getElementById('CancelacionOrdenImporte').value = totalSeleccionado;
		
		totalSeleccionado = FormatCurrency(totalSeleccionado);
	}


	function ctrlCobroCaja(){

		ocultarOptionFCobro();
		$('btn_submit').disable();
		
	}

</script>

	<?php // echo $frm->create(null,array('action' => 'generar_orden_cobro_caja/'.$persona['Persona']['id']))?>
	<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro', 'onsubmit' => "return ctrlCobroCaja()", 'action' => 'generar_orden_cobro_caja_recibo/'.$persona['Persona']['id'] ));?>
	<h4>DETALLE DE ORDENES DE CANCELACION EMITIDAS</h4>
		<script type="text/javascript">
		</script>
		<table>
			<tr>
				<th>#</th>
				<th>TIPO</th>
				<th>ORDEN</th>
				<th>TIPO / NUMERO</th>
				<th>PROVEEDOR / PRODUCTO</th>
				<th>A LA ORDEN DE</th>
				<th>DEUDA PROVEEDOR</th>
				<th>SALDO ORDEN DTO</th>
				<th>IMPORTE SELECCIONADO</th>
				<th>DEBITO/CREDITO</th>
				<th>VENCIMIENTO</th>
				<th></th>
				
			</tr>
			<?php $i=0; $nRespon = $cancelaciones[0]['CancelacionOrden']['origen_proveedor_id'];?>
			<?php foreach($cancelaciones as $cancelacion):?>
				<?php $i++;
					if($nRespon != $cancelacion['CancelacionOrden']['origen_proveedor_id']):
						$nRespon = 0;
					endif
				?>
				<tr id="TRL_<?php echo $i?>">
					<td><strong><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cancelacion['CancelacionOrden']['id'],'h' => 450, 'w' => 750))?></strong></td>
					<td align="center"><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
					<td align="center"><?php echo $controles->linkModalBox($cancelacion['CancelacionOrden']['orden_descuento_id'],array('title' => 'ORDEN DE DESCUENTO #' . $cancelacion['CancelacionOrden']['orden_descuento_id'],'url' => '/mutual/orden_descuentos/view/'.$cancelacion['CancelacionOrden']['orden_descuento_id'].'/'.$cancelacion['CancelacionOrden']['socio_id'],'h' => 450, 'w' => 750))?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['tipo_nro_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
					<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
					<td align="right"><strong><?php echo number_format($cancelacion['CancelacionOrden']['importe_proveedor'],2)?></strong></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['saldo_orden_dto'],2)?></td>
					<td align="right"><?php echo number_format($cancelacion['CancelacionOrden']['importe_seleccionado'],2)?></td>
					<td align="right">
						<?php
							if(!empty($cancelacion['CancelacionOrden']['tipo_cuota_diferencia'])){
								echo $this->requestAction('/config/global_datos/valor/' . $cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
								echo "&nbsp;= \$";
								echo number_format($cancelacion['CancelacionOrden']['importe_diferencia'],2);
							}
						?>
					</td>
					<td align="center"><strong><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></strong></td>
					<td><input type="checkbox" name="data[CancelacionOrden][id_check][<?php echo $cancelacion['CancelacionOrden']['id'] ?>]" value="<?php echo number_format(round($cancelacion['CancelacionOrden']['importe_proveedor'],2) * 100,0,".","")?>" id="CancelacionOrdenSaldo_<?php echo $i ?>" onclick="toggleCell('TRL_<?php echo $i?>',this); chkOnclick()"/></td>
			<?php endforeach;?>	
		</table>
		<div class="areaDatoForm2">
			<h4>DETALLE DEL COBRO POR CAJA</h4>
			<table class="tbl_form">
				<?php 
					$disabled = '';
					if($nRespon != 0):
						$disabled = 'disabled';
					endif; 
				?>
				<tr id="respo">
					<td>RESPONSABLE DE LA CANCELACION</td>
					<td><?php echo $frm->input('CancelacionOrden.proveedor_origen_id',array('type'=>'select','options'=>$cmbProveedores,'empty'=>FALSE, 'onchange' => 'seleccionComercio()', 'selected' => $nRespon, 'disabled' => $disabled));?></td>
				</tr>
				<tr id="fecha">
					<td>FECHA COBRO:</td>
					<td><?php echo $frm->calendar('CancelacionOrden.fecha_comprobante',null,$fechaCobro,date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr id="observa">
					<td>OBSERVACION:</td>
					<td><?php echo $frm->input('CancelacionOrden.observacion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
				</tr>
				<tr id="compensa">
					<td>COMPENSA PAGO:</td>
					<td><input type="checkbox" name="data[CancelacionOrden][compensa_pago] ?>]" id="CancelacionOrdenCompensaPago" /></td>
				</tr>
					<?php echo $this->renderElement('recibos/forma_cobro',array(
													'model' =>'CancelacionOrden',	
													'plugin'=>'clientes'))?>
				<tr><td colspan="3"><?php echo $frm->submit("RECAUDAR", array('id' => 'btn_submit'))?></td></tr>
			</table>
	
		</div>
		
		<div style="clear: both;"></div>
			<?php echo $frm->hidden('CancelacionOrden.tipo_documento', array('value' => 'REC')); ?>
			<?php echo $frm->hidden("CancelacionOrden.cabecera_socio_id", array('value' => $socio_id)) ?>
			<?php echo $frm->hidden('CancelacionOrden.forma_cobro_desc') ?>
			<?php echo $frm->hidden('CancelacionOrden.importe_cobro', array('value' => 0.00)) ?>
			<?php if($nRespon != 0):
					echo $frm->hidden('CancelacionOrden.proveedor_origen_id', array('value' => $nRespon));
				  endif;
			?>
		<div style="clear: both;"></div>
		<?php echo $frm->end();?>
		
		<div style="clear: both;"></div>
<?php endif;?>
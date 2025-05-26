<?php echo $this->renderElement('banco_cuenta_movimientos/banco_cuenta_movimiento_header',array('cuenta' => $cuenta))?>
<?php $mutual = strtoupper(Configure::read('APLICACION.nombre_fantasia')) ?>
<h4>REGISTRACION</h4>
<script language="Javascript" type="text/javascript">
	var rows = <?php echo count($chqCarteras)?>;
	Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
		ocultarOption();
	
	});
	
	function ocultarOption(){
		$('tipo_depo').hide();
		$('tipo_cheque').hide();
		$('descri').hide();
		$('a_orden').hide();
		$('a_cta').hide();
		$('cta_banco').hide();
		$('cta_con').hide();
		$('des_cta').hide();
		$('nro_cheque').hide();
		$('nro_depo').hide();
		$('nro_opera').hide();
		$('fEmi').hide();
		$('fDepo').hide();
		$('fOpera').hide();
		$('fVenc').hide();
		$('importe').hide();
		$('chq_cartera').hide();
	}
	
	
	function mostrarOption(){
		var seleccion = $('BancoCuentaMovimientoBancoConceptoId').getValue();
		
		ocultarOption();
		$('btn_submit').disable();
		
		if(seleccion != ''){

			switch (seleccion) {
    			case '1': // Emision de Cheques
        			$('tipo_cheque').show();
        			mostrarCheque();
       				break
	    		case '2':  // Deposito en Efectivo o Cheques en Cartera
	    			$('tipo_depo').show();
	    			mostrarDeposito();
       				break
    			case '3':  // Extraccion de Fondo
					$('descri').show();
					$('nro_opera').show();
					$('fOpera').show();
					$('importe').show();
			       	break
    			case '4':  // traspaso de fondo ('Movimiento entre Banco')
					$('descri').show();
					$('cta_banco').show();
					$('nro_opera').show();
					$('fOpera').show();
					$('importe').show();
		    	   	break
	    		case '5':  // transferrencia Bancaria
					$('descri').show();
					$('a_cta').show();
					$('cta_con').show();
					$('des_cta').show();
					$('nro_opera').show();
					$('fOpera').show();
					$('importe').show();
			       	break
			    case '6':  // Gastos Bancario o Gastos Varios de Caja
					$('descri').show();
					$('cta_con').show();
					$('des_cta').show();
					$('nro_opera').show();
					$('fOpera').show();
					$('importe').show();
			       	break
    			default:  // Operaciones de caja
					$('descri').show();
					$('cta_con').show();
					$('des_cta').show();
					$('fOpera').show();
					$('importe').show();
			}
			$('btn_submit').enable();
		}

	}


	function mostrarDeposito(){
		var selecDepo = $('BancoCuentaMovimientoTipoDeposito').getValue();
		
		$('importe').hide();
		$('chq_cartera').hide();

		if(selecDepo != ''){

			switch (selecDepo) {
    			case 'EF': // Deposito en Efectivo
					$('descri').show();
					$('nro_opera').show();
					$('fDepo').show();
					$('importe').show();
       				break
	    		case 'CH':  // Deposito con Cheques en Cartera
					$('descri').show();
					$('nro_opera').show();
					$('fDepo').show();
					$('chq_cartera').show();
       				break
       		}
       	}
	}	
	
	
	function mostrarCheque(){
		var selecCheq = $('BancoCuentaMovimientoTipoCheque').getValue();

		if(selecCheq != ''){

			$('descri').show();
			$('a_orden').show();
			$('cta_con').show();
			$('des_cta').show();
			$('nro_cheque').show();
			$('fEmi').show();
			$('fVenc').show();
			$('importe').show();
			
			switch (selecCheq){
				case 'CA': // CHEQUE PARA CAJA
					document.getElementById('BancoCuentaMovimientoDescripcion').value = 'EXTRACCION DE EFECTIVO PARA CAJA';
					document.getElementById('BancoCuentaMovimientoDestinatario').value = '<?php echo $mutual?>';
					$('cta_con').hide();
					$('des_cta').hide();
					break
				case 'AN': // ANULA CHEQUE
					$('cta_con').hide();
					$('des_cta').hide();
//					break
				case 'CO': // EMISION DE CHEQUE COMUN
					document.getElementById('BancoCuentaMovimientoDescripcion').value = '';
					document.getElementById('BancoCuentaMovimientoDestinatario').value = '';
					break
			}
		}
	}

			
	function chkOnclick(){
		SelSum();
	}

	function SelSum(){
		
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
		totalSeleccionado = FormatCurrency(totalSeleccionado/100);
		document.getElementById('BancoCuentaMovimientoImporteChequeMostrar').value = totalSeleccionado;
		document.getElementById('BancoCuentaMovimientoImporteCheque').value = totalSeleccionado;
	}
	
</script>


<?php echo $frm->create(null,array('name'=>'formMovimiento','id'=>'formMovimiento', 'action' => "registracion/" . $cuenta['BancoCuenta']['id'] ));?>
	<div class="areaDatoForm">
		<table class="tbl_form">
			<tr>
				<td>CONCEPTO:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.banco_concepto_id',array('type'=>'select','options'=>$combo, 'onchange' => 'mostrarOption()'));?></td>
			</tr>
			<tr id="tipo_depo">
				<td>TIPO DEPOSITO:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.tipo_deposito',array('type'=>'select','options'=>array('EF' => 'DEPOSITO EFECTIVO', 'CH' => 'DEP.CHEQUE CARTERA'), 'onchange' => 'mostrarDeposito()'));?></td>
			</tr>
			<tr id="tipo_cheque">
				<td>TIPO CHEQUE:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.tipo_cheque',array('type'=>'select','options'=>array('CA' => 'CHEQUE PARA CAJA', 'CO' => 'EMISION CHEQUE', 'AN' => 'ANULAR CHEQUE'), 'onchange' => 'mostrarCheque()'));?></td>
			</tr>
			<tr id="descri">
				<td>DESCRIPCION:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.descripcion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
			</tr>
			<tr id="a_orden">
				<td>A LA ORDEN DE:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.destinatario', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
			</tr>
			<tr id="a_cta">
				<td>NRO.CTA.BANCARIA:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.destino', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
			</tr>
			<tr id="cta_banco">
				<td>HACIA CTA.BANCARIA:</td>
				<td><?php echo $this->renderElement('banco_cuentas/combo_cuentas',array(
										'plugin'=>'cajabanco',
										'label' => "",
										'model' => 'BancoCuentaMovimiento.hacia_banco_cuenta_id',
										'disabled' => false,
										'empty' => false,
										'selected' => 0,
										'ecepto' => $cuenta['BancoCuenta']['id']))?>
				</td>			
			</tr>
			<tr id="cta_con">
				<td>APROXIMAR CUENTA:</td>
				<td><?php echo $frm->input('Asiento.descripcionAproxima',array('label'=>'','size'=>50,'maxlenght'=>100, 'value' => $this->data['BancoCuenta']['descripcionAproxima'])); ?>
				<div id="Cuenta_autoComplete" class="auto_complete"></div>
				<?php echo $frm->hidden('BancoCuentaMovimiento.co_plan_cuenta_id'); ?>
				<?php echo $frm->hidden('BancoCuentaMovimiento.cuenta_seleccionada'); ?>
				<span id="ajax_loader1" style="display: none;font-size: 11px;font-style:italic;color:red;">
				Procesando...<?php echo $html->image('controles/red_animated.gif') ?>
				</span>			
				
				<script type="text/javascript">
					document.getElementById("AsientoDescripcionAproxima").value = "<?php echo $this->data['Asiento']['descripcionAproxima']?>";
					document.getElementById("BancoCuentaMovimientoCoPlanCuentaId").value = "<?php echo $this->data['BancoCuenta']['co_plan_cuenta_id']?>";				
					
					new Ajax.Autocompleter('AsientoDescripcionAproxima', 'Cuenta_autoComplete', '<?php echo $this->base?>/contabilidad/plan_cuentas/autocompleteDescripcion/<?php echo $util->globalDato("CONTEVIG","entero_1")?>/1', {minChars:3, afterUpdateElement:getSelectionId2, indicator:'ajax_loader1'});
					function getSelectionId2(text, li) {
						var id = li.id;
						var values = id.split("|");
						document.getElementById("AsientoDescripcionAproxima").value = values[2];
						document.getElementById("BancoCuentaMovimientoCoPlanCuentaId").value = values[0];
						document.getElementById("descripcionCuenta").value = values[1] + " - " + values[2];
						document.getElementById("BancoCuentaMovimientoCuentaSeleccionada").value = values[1] + " - " + values[2];
					} 
				</script>
				</td>		
			</tr>		
			<tr id="des_cta">
				<td>CUENTA CONTABLE:</td>
				<td>
					<input type="text" id="descripcionCuenta" disabled="disabled" size="50" value="<?php echo $this->data['BancoCuenta']['cuenta_contable']?>"/>
				</td>
			</tr>
			<tr id="nro_cheque">
				<td>NRO. CHEQUE:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.numero_cheque', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
			</tr>
			<tr id="nro_depo">
				<td>NRO. DEPOSITO:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.numero_deposito', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
			</tr>
			<tr id="nro_opera">
				<td>NRO. OPERACION:</td>
				<td><?php echo $frm->input('BancoCuentaMovimiento.numero_operacion', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
			</tr>
			<tr id="fEmi">
				<td>FECHA DE EMISION:</td>
				<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_emision',null,null,date('Y')-2,date('Y')+1)?></td>
			</tr>
			<tr id="fDepo">
				<td>FECHA DEPOSITO:</td>
				<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_deposito',null,null,date('Y')-2,date('Y')+1)?></td>
			</tr>
			<tr id="fOpera">
				<td>FECHA OPERACION:</td>
				<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_operacion',null,null,date('Y')-2,date('Y')+1)?></td>
			</tr>
			<tr id="fVenc">
				<td>FECHA VENCIMIENTO:</td>
				<td><?php echo $frm->calendar('BancoCuentaMovimiento.fecha_vencimiento',null,null,date('Y')-2,date('Y')+1)?></td>
			</tr>
			<tr id="importe">
				<td>IMPORTE:</td>
				<td><?php echo $frm->money('BancoCuentaMovimiento.importe','') ?></td>
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
					<?php $i = 0;?>
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
	
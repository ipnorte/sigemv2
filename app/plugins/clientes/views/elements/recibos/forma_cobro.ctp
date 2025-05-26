<?php $model = (isset($model) ? $model : 'Recibo'); ?>
<?php $fechaCobro = (isset($fechaCobro) ? $fechaCobro : date('Y-m-d'));?>
<script language="Javascript" type="text/javascript">
	function actualizaImporte(valor){
		v1 = valor;
		v2 = document.getElementById("<?php echo $model ?>Importe").value;
		v1 = new Number(v1);
		v2 = new Number(v2);
		document.getElementById("<?php echo $model ?>Importe").value = v1 + v2;
		document.getElementById("<?php echo $model ?>FormaCobro").value = "";
		ocultarOptionFCobro();
	}

	function limpiarForm(impResto){
		document.getElementById("<?php echo $model ?>FormaCobro").value = "";
		document.getElementById("<?php echo $model ?>BancoId").value = "";
		document.getElementById("<?php echo $model ?>Plaza").value = "";	
		document.getElementById("<?php echo $model ?>NumeroDeposito").value = "";	
		document.getElementById("<?php echo $model ?>NumeroCheque").value = "";	
		document.getElementById("<?php echo $model ?>Librador").value = "";
		document.getElementById("<?php echo $model ?>Importe").value = impResto;
	}

	function ocultarOptionFCobro(){
		$("cuenta").hide();
		$("nroOpera").hide();
		$("fOpera").hide();
		$("banco").hide();
		$("plaza").hide();
		$("nroCheque").hide();
		$("fCobro").hide();
		$("fVenc").hide();
		$("libra").hide();
		$("importe").hide();
		
		
	}	
	
	
	function seleccionCobro(){
		var seleccion = $('<?php echo $model ?>FormaCobro').getValue();

		ocultarOptionFCobro();

		if(seleccion == 'EF'){
			document.getElementById("<?php echo $model ?>FormaCobroDesc").value = "EFECTIVO"
			$("cuenta").hide();
			$("nroOpera").hide();
			$("fOpera").hide();
			$("banco").hide();
			$("plaza").hide();
			$("nroCheque").hide();
			$("fCobro").hide();
			$("fVenc").hide();
			$("libra").hide();
			$("importe").show();
		}

		if(seleccion == 'CT'){
			
			document.getElementById("<?php echo $model ?>FormaCobroDesc").value = "CHEQUE"
			$("cuenta").hide();
			$("nroOpera").hide();
			$("fOpera").hide();
			$("banco").show();
			$("plaza").show();
			$("nroCheque").show();
			$("fCobro").show();
			$("fVenc").show();
			$("libra").show();
			$("importe").show();
		}

		if(seleccion == 'DB'){
			document.getElementById("<?php echo $model ?>FormaCobroDesc").value = "DEPOSITO BANCARIO"
			$("cuenta").show();
			$("nroOpera").show();
			$("fOpera").show();
			$("banco").hide();
			$("plaza").hide();
			$("nroCheque").hide();
			$("fCobro").hide();
			$("fVenc").hide();
			$("libra").hide();
			$("importe").show();
		}

	}
</script>

			<tr id="forma">
				<td>FORMA COBRO</td>
				<td><?php echo $frm->input($model . '.forma_cobro',array('type' => 'select','options' => array('' => 'Seleccionar...', 'EF' => 'EFECTIVO', 'CT' => 'CHEQUES TERCERO', 'DB' => 'DEPOSITO BANCARIO'), 'onchange' => 'seleccionCobro()', 'selected' => ''));?></td>
			</tr>
			<tr id="cuenta">
				<td>CUENTA BANCARIA</td>
				<td><?php echo $this->renderElement('banco_cuentas/combo_cuentas',array(
										'plugin'=>'cajabanco',
										'label' => "",
										'model' => $model . '.banco_cuenta_id',
										'disabled' => false,
										'empty' => false,
										'selected' => 0))?>
				</td>			
			</tr>
			<tr id="nroOpera">
				<td>NUMERO OPERACION</td>
				<td><?php echo $frm->input($model . '.numero_deposito', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
			</tr>
			<tr id="fOpera">
				<td>FECHA OPERACION</td>
				<td><?php echo $frm->calendar($model . '.fdeposito',null,$fechaCobro,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr id="banco">
				<td>BANCO</td>
				<td><?php echo $this->renderElement('banco/combo_global',array('plugin' => 'config','tipo' => 4, 'model' => $model . '.banco_id', 'selected' => (isset($this->data[$model ]['banco_id']) ? $this->data[$model ]['banco_id'] : "")))?></td>
			</tr>
			<tr id="plaza">
				<td>PLAZA</td>
				<td><?php echo $frm->input($model . '.plaza', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
			</tr>
			<tr id="nroCheque">
				<td>NUMERO CHEQUE</td>
				<td><?php echo $frm->input($model . '.numero_cheque', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
			</tr>
			<tr id="fCobro">
				<td>FECHA EMISION</td>
				<td><?php echo $frm->calendar($model . '.fcheque',null,$fechaCobro,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr id="fVenc">
				<td>FECHA VENCIMIENTO</td>
				<td><?php echo $frm->calendar($model . '.fvenc',null,$fechaCobro,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr id="libra">
				<td>LIBRADOR</td>
				<td><?php echo $frm->input($model . '.librador', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
			</tr>
			<tr id="importe">
				<td>IMPORTE</td>
				<td><?php echo $frm->money($model . '.importe','') ?>
					<?php echo $frm->hidden('Referencia.modelo', array('value' => $model)); ?>
		 			<?php //echo $controles->btnAjax('controles/add.png','/contabilidad/asientos/cargar_renglones','grilla_renglones','formAsiento')?>
			 		<a href="<?php echo $this->base?>/clientes/recibos/cargar_renglones" id="link1568620940" onclick=" event.returnValue = false; return false;">
			 		<img src="<?php echo $this->base?>/img/controles/add.png" border="0" alt="" />
			 		</a>
					<script type="text/javascript">
						Event.observe('link1568620940', 'click', function(event) 
						{ 
							$('ajax_loader_2124618328').show();
							new Ajax.Updater('grilla_cobros', '<?php echo $this->base?>/clientes/recibos/cargar_renglones', 
							{ 
								asynchronous:true, evalScripts:true, onComplete:function(request, json) 
								{
									$('ajax_loader_2124618328').hide();
						  			acumulado = parseFloat($('acumulado').getValue());
									cobro = document.getElementById("<?php echo $model ?>ImporteCobro").value;
						  			cobro = parseFloat(cobro);
									resto = new Number(cobro - acumulado);
									resto = resto.toFixed(2);
									// resto = cobro - acumulado;
						  			if(resto == 0) $('btn_submit').enable();
					  				else $('btn_submit').disable();
					  				if(document.getElementById("<?php echo $model ?>Acumula") != null){
					  					document.getElementById("<?php echo $model ?>Acumula").value = acumulado;
						  			}
					  				limpiarForm(resto);
						  			ocultarOptionFCobro();
								},
								parameters:$('formReciboCobro').serialize(), 
								requestHeaders:['X-Update', 'grilla_cobros']
							})
						}, false);
					</script>
		 			<span id="ajax_loader_2124618328" style="display: none;font-size: 11px;font-style:italic;color:red;margin-left:10px;"><img src="<?php echo $this->base?>/img/controles/ajax-loader.gif" border="0" alt="" /></span>
				</td>
			</tr>
			<tr>
				<td colspan="2" id="grilla_cobros"></td>
			</tr>

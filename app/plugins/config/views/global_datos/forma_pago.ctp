
<script type="text/javascript">
	//<![CDATA[
	
	Event.observe(window, 'load', function() {

		$('OperacionBancaria').hide();
		$('sucursales').hide();

		$('CreditoFormaPago').observe('change',function(){
			var sel = $('CreditoFormaPago').getValue();
//			alert(sel);

			if(sel == 'FPAG0001')$('OperacionBancaria').hide();
			else $('OperacionBancaria').show();

			if(sel == 'FPAG0003'){
				$('sucursales').show();
				$('CreditoBancoId').observe('change',function(){
					new Ajax.Updater('sucursales',
									'/<?php echo $this->base?>/config/banco_sucursales/combo/' + $('CreditoBancoId').getValue(), 
									{asynchronous:true, evalScripts:true, requestHeaders:['X-Update', 'sucursales']}
									);
				});
			}else{
				$('sucursales').hide();
			}
		});

	});
	
	//]]>	
</script>	
<h3>FORMA DE PAGO</h3>
<div class="areaDatoForm">
	<div class="row">
		<?php echo $frm->input('Credito.forma_pago',array('type'=>'select','options'=>$values,'empty'=>false,'label'=>'MEDIO','disabled' => $disabled));?>
		<div id="OperacionBancaria">
				<?php echo $this->requestAction('/config/bancos/combo')?>
				<div id="sucursales" class="row"></div>
				<div id="datosOperacionBancaria" class="row">
				
				<?php echo $frm->calendar('Credito.fecha_ope_ban','FECHA OPERACION BANCARIA')?>
				<?php echo $frm->input('Credito.nro_operacion',array('label'=>'NRO DE OPERACION (en caso de CHEQUE colocar NRO CHEQUE)','size'=>25,'maxlenght'=>50)); ?>
				
				</div>
				<div style="clear: both;"></div>
		</div>
		<div style="clear: both;"></div>
	</div>
	<div style="clear: both;"></div>
</div>
<div style="clear: both;"></div>


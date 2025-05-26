
<script type="text/javascript">

Event.observe(window, 'load', function() {

	<?php if(!isset($this->data['CancelacionOrden']['forma_cancelacion'])):?>

		$('DetallePagoPorCaja').hide();
		$('datoBanco').hide();
		$('datoBancoNroCuota').hide();
		$('datoBancoNroOpenBan').hide();

	<?php else:?>

		$('DetallePagoPorCaja').show();

		<?php if($this->data['CancelacionOrden']['forma_pago'] == 'MUTUFPAG0001'):?>
			
			$('datoBanco').hide();
			$('datoBancoNroCuota').hide();
			$('datoBancoNroOpenBan').hide();
		
		<?php else:?>
		
			document.getElementById("CancelacionOrdenBancoId").value = "<?php echo $this->data['CancelacionOrden']['banco_id']?>";	
		
		<?php endif;?>
		
	<?php endif;?>
	
	$('CancelacionOrdenFormaCancelacion').observe('change',function(){
		if($('CancelacionOrdenFormaCancelacion').getValue() == "MUTUTICA0002"){
			$('DetallePagoPorCaja').show();
		}else{
			$('DetallePagoPorCaja').hide();
		}	
	});
	
	$('CancelacionOrdenFormaPago').observe('change',function(){
		if($('CancelacionOrdenFormaPago').getValue() != "MUTUFPAG0001"){
			$('datoBanco').show();
			$('datoBancoNroCuota').show();
			$('datoBancoNroOpenBan').show();
		}else{
			$('datoBanco').hide();
			$('datoBancoNroCuota').hide();
			$('datoBancoNroOpenBan').hide();
		}	
	});	
	
});

</script>


<h1>CARGAR DATOS RELACIONADOS AL PAGO DE LA CANCELACION</h1>

<?php echo $this->renderElement('cancelacion_orden/resumen',array('orden' => $this->data,'detalle_cuotas'=>true, 'plugin' => 'mutual'));?>

<?php echo $frm->create(null,array('action' => 'cargar_info_pago/'.$this->data['CancelacionOrden']['id']))?>
<div class="areaDatoForm2">

	<h3 style="border-bottom:1px solid;">DATOS DEL PAGO</h3>

	<table class="tbl_form">

		<tr>
		
			<td>TIPO CANCELACION</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'CancelacionOrden.forma_cancelacion',
																			'prefijo' => 'MUTUTICA',
																			'disable' => false,
																			'empty' => false,
																			'selected' => (!empty($this->data['CancelacionOrden']['forma_cancelacion']) ? $this->data['CancelacionOrden']['forma_cancelacion'] : '0'),
																			'logico' => true,
			))?>		
			</td>
			<td>FECHA IMPUTACION</td><td><?php echo $frm->input('CancelacionOrden.fecha_imputacion',array('dateFormat' => 'DMY','minYear'=>date("Y") - 1, 'maxYear' => date("Y") + 1))?></td>
		</tr>

	</table>
</div>
<div class="areaDatoForm" id="DetallePagoPorCaja">
	<h4>DETALLE DEL PAGO POR CAJA</h4>
	<table class="tbl_form">
		<tr>
			<td>FORMA DE PAGO</td>
			<td>
			<?php echo $this->renderElement('global_datos/combo',array(
																			'plugin'=>'config',
																			'label' => " ",
																			'model' => 'CancelacionOrden.forma_pago',
																			'prefijo' => 'MUTUFPAG',
																			'disable' => false,
																			'empty' => false,
																			'selected' => (!empty($this->data['CancelacionOrden']['forma_pago']) ? $this->data['CancelacionOrden']['forma_pago'] : '0'),
																			'logico' => true,
			))?>
			</td>
			<td>
			<?php echo $frm->input('CancelacionOrden.pendiente_rendicion_proveedor',array('label' => 'PENDIENTE'))?>					
			</td>
		</tr>
		<tr id="datoBanco">
			<td>BANCO</td>
			<td colspan="2">
			<?php echo $this->renderElement('banco/combo',array(
																'plugin'=>'config',
																'label' => " ",
																'model' => 'CancelacionOrden.banco_id',
																'disable' => false,
																'empty' => false,
																'tipo' => 4
			))?>	
			</td>
		</tr>
		<tr id="datoBancoNroCuota">
			<td>NRO DE CUENTA</td>
			<td colspan="2"><?php echo $frm->input('CancelacionOrden.nro_cta_bco',array('size'=>50,'maxlenght'=>50)); ?></td>
		</tr>
		<tr id="datoBancoNroOpenBan">
			<td>NRO.OPERACION / NRO.CHEQUE</td>
			<td colspan="2"><?php echo $frm->input('CancelacionOrden.nro_operacion',array('size'=>50,'maxlenght'=>50)); ?></td>
		</tr>
		
	</table>
</div>
<?php echo $frm->hidden('id')?>

<?php echo $frm->end("GUARDAR")?>
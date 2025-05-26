<h1>RECAUDACION POR CAJA</h1>
<hr>
	<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro', 'onsubmit' => "return ctrlCobroCaja()", 'action' => "add_recibo/$orden_cobro_xcaja_id"))?>
	<?php echo $this->requestAction('/mutual/orden_caja_cobros/view/'.$orden_cobro_xcaja_id)?>
<?php if($procesada==0):?>	
<script language="Javascript" type="text/javascript">
        var vproveedorid = <?php echo $MutualProveedorId?>;

        Event.observe(window, 'load', function() {
		
		$('btn_submit').disable();
		document.getElementById("OrdenDescuentoCobroImporte").value = <?php echo $importe_cobrado?>;
//		ocultarComercio();
		ocultarOptionFCobro();
	
	});
	
	
	function ocultarComercio(){
		$("fecha").hide();
		$("observa").hide();
		$("compensa").hide();
		$("forma").hide();
	}
	
	
	function seleccionComercio(){
		var comercio = $('OrdenDescuentoCobroProveedorOrigenId').getValue();

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


	function ctrlCobroCaja(){

		ocultarOptionFCobro();
		$('btn_submit').disable();
		
	}
</script>

<div class="areaDatoForm">
	<h3>DATOS DEL COBRO</h3>
	<table class="tbl_form">
				<tr>
					<td>RECAUDADO EN:</td>
					<td><?php echo $frm->input('OrdenDescuentoCobro.proveedor_origen_id',array('type'=>'select','options'=>$cmbProveedores,'empty'=>FALSE, 'onchange' => 'seleccionComercio()', 'selected' => 18));?></td>
				</tr>
				<tr id="fecha">
					<td>FECHA COBRO:</td>
					<td><?php echo $frm->calendar('OrdenDescuentoCobro.fecha_comprobante',null,$fechaCobro,date('Y')-1,date('Y')+1)?></td>
				</tr>
				<tr id="observa">
					<td>OBSERVACION:</td>
					<td><?php echo $frm->input('OrdenDescuentoCobro.observacion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
				</tr>
				<tr id="compensa">
					<td>COMPENSA PAGO:</td>
					<td><input type="checkbox" name="data[OrdenDescuentoCobro][compensa_pago] ?>]" id="OrdenDescuentoCobroCompensaPago" /></td>
				</tr>
					<?php echo $this->renderElement('recibos/forma_cobro',array(
													'model' =>'OrdenDescuentoCobro',	
													'plugin'=>'clientes'))?>
	</table>
	<?php echo $frm->hidden('tipo_documento', array('value' => 'REC')); ?>
	<?php echo $frm->hidden('orden_caja_cobro_id',array('value' => $orden_cobro_xcaja_id))?>
	<?php echo $frm->hidden('cabecera_socio_id',array('value' => $ocaja['OrdenCajaCobro']['socio_id']))?>
	<?php echo $frm->hidden('forma_cobro_desc') ?>
	<?php echo $frm->hidden('importe_cobro', array('value' => $importe_cobrado)) ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'COBRAR','URL' => ( empty($fwrd) ? "/mutual/orden_caja_cobros/": $fwrd) ))?>
</div>
<?php else:?>
	<div class="notices_error">
		LA ORDEN DE COBRO POR CAJA #<?php echo $orden_cobro_xcaja_id?> ESTA PROCESADA
	</div>
<?php endif;?>
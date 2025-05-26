<h1>RECAUDACION POR CAJA</h1>
<hr>

<?php //echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro','action' => "add_recibo/$orden_cobro_xcaja_id"))?>
<?php echo $this->requestAction('/mutual/orden_caja_cobros/view/'.$orden_cobro_caja_id)?>
<?php if($procesada==0):?>	
<script language="Javascript" type="text/javascript">
        var vproveedorid = <?php echo $MutualProveedorId?>;

        Event.observe(window, 'load', function() {
		
            <?php // if($importe_cobrado != 0):?>
                $('btn_submit').disable();
//			$('btn_submit').enable();
            <?php // endif; ?>
            document.getElementById("OrdenDescuentoCobroImporte").value = <?php echo $importe_cobrado?>;
            ocultarOptionFCobro();

	
	});
	
	
	function ocultarComercio(){
		$("fecha").hide();
		$("observa").hide();
		$("forma").hide();
	}
	
	
	function seleccionComercio(){
		var comercio = $('OrdenDescuentoCobroProveedorOrigenFondoId').getValue();

		ocultarComercio();

		if(comercio != 0){
			$("fecha").show();
			$("observa").show();
			$("forma").show();
		}
	}


	function ctrlCobroCaja(){

		ocultarOptionFCobro();
		$('btn_submit').disable();
		
	}
		
</script>

<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro', 'onsubmit' => "return ctrlCobroCaja()", 'action' => "orden_cobro_caja_mutual/".$orden_cobro_caja_id ));?>
<div class="areaDatoForm">
	<h3>DATOS DEL COBRO</h3>
	<table class="tbl_form">
		<tr id="fecha">
			<td>FECHA COBRO:</td>
			<td><?php echo $frm->calendar('OrdenDescuentoCobro.fecha_comprobante',null,$fechaCobro,date('Y')-1,date('Y')+1)?></td>
		</tr>
		<tr id="observa">
			<td>OBSERVACION:</td>
			<td><?php echo $frm->input('OrdenDescuentoCobro.observacion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
		</tr>
		<?php if($importe_cobrado != 0):?>
				<?php echo $this->renderElement('recibos/forma_cobro',array(
												'model' =>'OrdenDescuentoCobro',	
												'plugin'=>'clientes'))?>
		<?php endif;?>
	</table>
	<?php echo $frm->hidden('OrdenDescuentoCobro.tipo_documento', array('value' => 'REC')); ?>
	<?php echo $frm->hidden('OrdenDescuentoCobro.orden_caja_cobro_id',array('value' => $orden_cobro_caja_id))?>
	<?php echo $frm->hidden('OrdenDescuentoCobro.cabecera_socio_id',array('value' => $ocaja['OrdenCajaCobro']['socio_id']))?>
	<?php echo $frm->hidden('OrdenDescuentoCobro.proveedor_origen_fondo_id',array('value' => $origenFondo))?>
	<?php echo $frm->hidden('OrdenDescuentoCobro.forma_cobro_desc') ?>
	<?php echo $frm->hidden('OrdenDescuentoCobro.importe_cobro', array('value' => $importe_cobrado)) ?>
	<?php echo $frm->hidden('OrdenDescuentoCobro.importe_total', array('value' => $importe_total)) ?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'COBRAR','URL' => ( empty($fwrd) ? "/mutual/orden_caja_cobros/": $fwrd) ))?>
</div>
<?php else:?>
	<div class="notices_error">
		LA ORDEN DE COBRO POR CAJA #<?php echo $orden_cobro_caja_id?> ESTA PROCESADA
	</div>
<?php endif;?>
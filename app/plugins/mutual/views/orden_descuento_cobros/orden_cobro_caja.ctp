<h1>RECAUDACION POR CAJA</h1>
<hr>

<?php //echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro','action' => "add_recibo/$orden_cobro_xcaja_id"))?>
<?php echo $this->requestAction('/mutual/orden_caja_cobros/view/'.$orden_cobro_caja_id)?>
<?php if($procesada==0):?>	
<script language="Javascript" type="text/javascript">
        var vproveedorid = <?php echo $MutualProveedorId?>;

        Event.observe(window, 'load', function() {
		
		var cabeceraCobro = $('formCabeceraCobro');
		
		<?php if($bloquear==1):?> 
			cabeceraCobro.disabled = true;
			$('formCabeceraCobro').disable();
			<?php if($importe_cobrado != 0):?>
				$('btn_submit').disable();
			<?php endif; ?>
			document.getElementById("OrdenDescuentoCobroImporte").value = <?php echo $importe_cobrado?>;
			ocultarOptionFCobro();
			<?php if($origenFondo == MUTUALPROVEEDORID):?>
				$("compensa").hide();
			<?php endif; ?>
		<?php endif; ?>

	
	});
	
	
	function ocultarComercio(){
		$("fecha").hide();
		$("observa").hide();
		$("compensa").hide();
		$("forma").hide();
	}
	
	
	function seleccionComercio(){
		var comercio = $('OrdenDescuentoCobroProveedorOrigenFondoId').getValue();

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


	function chkCompensa(){
		var cmpPago = $('OrdenDescuentoCobroCompensaPago').getValue();


		ocultarOptionFCobro();
		$('forma').show();
		$('btn_submit').disable();
		if(cmpPago == 'on'){
			$('forma').hide();
			$('btn_submit').enable();
		}

		
	}


	function ctrlCobroCaja(){
		if(confirm("GENERAR COBRO?")) {
    		ocultarOptionFCobro();
    		$('btn_submit').disable();
    		return true;
		}else {
			return false;
		}
	}
		
</script>
<div class="areaDatoForm">
<?php echo $frm->create(null,array('name'=>'formCabeceraCobro', 'id'=>'formCabeceraCobro', 'action' => "orden_cobro_caja/".$orden_cobro_caja_id ));?>
		<table class="tbl_form">
			<tr id="respo">
				<td>ORIGEN DE LOS FONDOS</td>
				<td><?php echo $frm->input('OrdenCobro.proveedor_origen_fondo_id',array('type'=>'select','options'=>$cmbProveedores,'empty'=>FALSE, 'selected' => (isset($this->data['OrdenCobro']['proveedor_origen_fondo_id']) ? $this->data['OrdenCobro']['proveedor_origen_fondo_id'] : MUTUALPROVEEDORID)));?></td>
				<td><?php echo $frm->submit("SIGUIENTE")?></td>
			</tr>
		</table>
	    <?php echo $frm->hidden("OrdenCobro.Cabecera", array('value' => 1)) ?>
<?php echo $frm->end(); ?>
</div>

<?php if($bloquear == 1):?>

<?php echo $frm->create(null,array('name'=>'formReciboCobro','id'=>'formReciboCobro', 'onsubmit' => "return ctrlCobroCaja()", 'action' => "orden_cobro_caja/".$orden_cobro_caja_id ));?>
<div class="areaDatoForm">
	<h3>DATOS DEL COBRO</h3>
	<table class="tbl_form">
		<!-- 				
			<tr>
				<td>RECAUDADO EN:</td>
				<td><?php echo $frm->input('OrdenDescuentoCobro.proveedor_origen_id',array('type'=>'select','options'=>$cmbProveedores,'empty'=>FALSE, 'onchange' => 'seleccionComercio()', 'selected' => 18));?></td>
			</tr>
	 	-->
	 	
	 	<?php 
	 	$file = parse_ini_file(CONFIGS.'mutual.ini', true);
	 	$EDITAR_TIPO_COBRO = (isset($file['general']['editar_tipo_cobro']) && !empty($file['general']['editar_tipo_cobro']) ? TRUE : FALSE);
	 	if($EDITAR_TIPO_COBRO):
	 	?>
    		 <tr>
    		 <td>TIPO COBRO</td>
    		 <td>
    		 <?php echo $this->renderElement('global_datos/combo_global',array(
    																			'plugin'=>'config',
    																			'label' => " ",
    																			'model' => 'OrdenDescuentoCobro.tipo_cobro',
    																			'prefijo' => 'MUTUTCOB',
    																			'disabled' => false,
    																			'empty' => false,
    																			'metodo' => "get_tipos_cobro_caja",
    																			'selected' => (isset($tipo_cobro) ? $tipo_cobro : "MUTUTCOBCAJA")	
    			))?>			 
    		 </td>
    		 </tr>
		 <?php endif;?>
		 	 	
		<tr id="fecha">
			<td>FECHA COBRO:</td>
			<td><?php echo $frm->calendar('OrdenDescuentoCobro.fecha_comprobante',null,$fechaCobro,date('Y')-1,date('Y')+1)?></td>
		</tr>
		<tr id="observa">
			<td>OBSERVACION:</td>
			<td><?php echo $frm->input('OrdenDescuentoCobro.observacion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
		</tr>
		<?php if($importe_cobrado != 0):?>
			<tr id="compensa">
				<td>COMPENSA PAGO:</td>
				<td><input type="checkbox" name="data[OrdenDescuentoCobro][compensa_pago]" id="OrdenDescuentoCobroCompensaPago" onclick="chkCompensa()" /></td>
			</tr>
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
<?php endif;?>
<?php else:?>
	<div class="notices_error">
		LA ORDEN DE COBRO POR CAJA #<?php echo $orden_cobro_caja_id?> ESTA PROCESADA
	</div>
<?php endif;?>
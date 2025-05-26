<?php echo $this->renderElement('clientes/cliente_header',array('cliente' => $cliente));?>

<div class="areaDatoForm">
<h3>FACTURACION DE CLIENTES</h3>
<script language="Javascript" type="text/javascript">

	Event.observe(window, 'load', function() {
		
		var cabeceraFactura = $('formCabeceraFactura');
		
		<?php if($cabecera==0):?> 
			$('formCabeceraFactura').disable();
			$('btn_submit').disable();
		<?php endif; ?>
	
	});


	function disparo_funcion(nTotalFactura)
	{
//		var nTotalFactura = $('ClienteFacturaDetalleTotalFacturado').getValue();
//		alert(nTotalFactura);
		if(nTotalFactura == 0) $('btn_submit').disable();
		else $('btn_submit').enable();
	}
	
	function ctrlDetalle()
	{
            $('btn_submit').disable();
//		alert("Estoy disparando una Funcion");
	}
	
</script>
	
	
<?php echo $frm->create(null,array('name'=>'formCabeceraFactura','id'=>'formCabeceraFactura', 'action' => "clientes/facturas/" . $cliente['Cliente']['id'] ));?>

		<table class="tbl_form">
			<tr>
                            <td>Tipo:</td>
                            <td><?php echo $frm->input('ClienteFactura.tipo',array('type' => 'select','options' => array('FA' => 'FACTURA', 'NC' => 'NOTA CREDITO', 'ND' => 'NOTA DEBITO'),'label'=>''));?></td>
                            <td>Talonario:</td>
                            <td><?php echo $frm->input('ClienteFactura.tipo_talonario',array('type'=>'select','options'=> $aComboTalonario));?></td>
                            <td align="right">Fecha:</td>
                            <td><?php echo $frm->calendar('ClienteFactura.fecha',null,$fecha_factura,date('Y')-1,date('Y')+1)?></td>
			</tr>
			<tr>
				<td>IMP.CTA.CONTABLE</td>
                                <td colspan="3"><?php echo $this->renderElement('combo_plan_cuenta',array(
										'plugin'=>'contabilidad',
										'label' => "",
										'model' => 'ClienteFactura.co_plan_cuenta_id',
										'disabled' => false,
										'empty' => false,
										'selected' => $cliente['Cliente']['co_plan_cuenta_id']))?>
				</td>			
				<td>Descripci&oacute;n</td>
				<td><?php echo $frm->input('ClienteFactura.observacion', array('label'=>'','size'=>60,'maxlength'=>50)) ?></td>
			</tr>
			<tr>
				<td><?php echo $frm->submit("SIGUIENTE")?></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	    <?php echo $frm->hidden("ClienteFactura.cabecera", array('value' => $cabecera)) ?>
<?php echo $frm->end(); ?>
</div>

<?php if($cabecera == 0):?>

	<div  class="areaDatoForm">	
	<?php echo $frm->create(null,array('name'=>'formFacturaDetalle','id'=>'formFacturaDetalle','onsubmit' => "return ctrlDetalle()", 'action' => "facturas/" . $cliente['Cliente']['id'] ));?>
		<table align="center" width="100%">
			<tr>
				<th>PRODUCTO</th>
				<th>CTA.CONTABLE</th>
				<th>CANTIDAD</th>
				<th>P.UNITARIO</th>
				<th></th>
			</tr>
				<td><?php echo $this->renderElement("mutual_productos/combo_productos",array('plugin' => 'mutual','empty' => true,'model' => "ClienteFacturaDetalle", 'proveedor_id' => $mutual_proveedor_id))?></td>			
				<td><?php echo $this->renderElement('combo_plan_cuenta',array(
										'plugin'=>'contabilidad',
										'label' => "",
										'model' => 'ClienteFacturaDetalle.co_plan_cuenta_id',
										'disabled' => false,
										'empty' => false,
										'selected' => ''))?>
				</td>
				<td><?php echo $frm->number('ClienteFacturaDetalle.cantidad',array('value' => 1)); ?></td>
				<td><?php echo $frm->money('ClienteFacturaDetalle.importe_unitario')?></td>
<!--				<td><?php // echo $controles->btnAjax('controles/add.png','/clientes/clientes/cargar_factura_detalle','grilla_factura_detalle','formFacturaDetalle')?></td>-->
				<td>
					<a href="<?php echo $this->base?>/clientes/clientes/cargar_factura_detalle" id="link_factura" onclick=" event.returnValue = false; return false;">
						<img src="<?php echo $this->base?>/img/controles/add.png" border="0" alt="" />
					</a>
					
<script language="Javascript" type="text/javascript">
	Event.observe('link_factura', 'click', function(event) 
	{ 
		$('ajax_loader_factura').show(); 
		new Ajax.Updater
		(
			'grilla_factura_detalle',
			'<?php echo $this->base?>/clientes/clientes/cargar_factura_detalle', 
			{
				asynchronous:true, 
				evalScripts:true, 
				onComplete:function(request, json) 
				{
					$('ajax_loader_factura').hide();
					nTotalFacturado = $('ClienteFacturaTotalFactura').getValue();
					if(nTotalFacturado == 0) $('btn_submit').disable();
					else $('btn_submit').enable();
				}, 
				parameters:$('formFacturaDetalle').serialize(), 
				requestHeaders:['X-Update', 'grilla_factura_detalle']
			}
		);
//		disparo_funcion(nTotalFacturado);
	}, 
	false);
</script>


					<span id="ajax_loader_factura" style="display: none;font-size: 11px;font-style:italic;color:red;margin-left:10px;">
						<img src="<?php echo $this->base?>/img/controles/ajax-loader.gif" border="0" alt="" />
					</span>
				</td>
			</tr>
		</table>


		<table align="center" width="100%">
			<tr>
				<td colspan="5" id="grilla_factura_detalle"></td>
			</tr>
		</table>

		
		<?php echo $frm->hidden("ClienteFactura.cliente_id", array('value' => $cliente['Cliente']['id'])) ?>
		<?php echo $frm->hidden("ClienteFactura.tipo", array('value' => $this->data['ClienteFactura']['tipo'])) ?>
		<?php echo $frm->hidden("ClienteFactura.tipo_talonario", array('value' => $this->data['ClienteFactura']['tipo_talonario'])) ?>
		<?php echo $frm->hidden("ClienteFactura.fecha", array('value' => $fecha_factura )) ?>
		<?php echo $frm->hidden("ClienteFactura.co_plan_cuenta_id", array('value' => $this->data['ClienteFactura']['co_plan_cuenta_id'])) ?>
		<?php echo $frm->hidden("ClienteFactura.observacion", array('value' => $this->data['ClienteFactura']['observacion'])) ?>

		<?php echo $frm->hidden("ClienteFacturaDetalle.uuid", array('value' => $uuid)) ?>
		<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR FACTURA', 'URL' => '/clientes/clientes/cta_cte/' . $cliente['Cliente']['id']))?>
		
	</div>
<?php endif;?>
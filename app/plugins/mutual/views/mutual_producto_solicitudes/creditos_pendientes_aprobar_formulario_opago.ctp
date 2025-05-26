<?php echo $this->renderElement('head',array('title' => 'APROBAR SOLICITUD DE CREDITO #'.$mutual_producto_solicitud_id,'plugin' => 'config'))?>
<?php echo $this->requestAction('/mutual/mutual_producto_solicitudes/view/'.$mutual_producto_solicitud_id)?>


	<?php 
	echo $frm->create(null,array('name'=>'formDetallePago','onsubmit' => "return validateConfirm();",'id'=>'formDetallePago', 'action' => 'creditos_pendientes_aprobar_opago'));
            $importePago = $solicitud['MutualProductoSolicitud']['importe_percibido'];?>

            <script language="Javascript" type="text/javascript">
		var rows = <?php echo ( isset($facturaPendiente) ? count($facturaPendiente) : 0) ?>;
		var fechaPago = "<?php echo date('Y-m-d')?>";
		var importeTotal = <?php echo  $solicitud['MutualProductoSolicitud']['importe_percibido'] ?>;
		Event.observe(window, 'load', function() {
				
                    document.getElementById("MovimientoImporteEfectivo").value = importeTotal;

                    $('btn_submit').enable();
                    <?php if($solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] == 1):?>
                            $('btn_submit').disable();
                    <?php endif;?>
                        
                    <?php if($solicitud['MutualProductoSolicitud']['prestamo'] == 1): ?>
			$('btn_submit').disable();
                    <?php endif; ?>		
		
                    ocultarOptionFPago();

		});
		

                function validateConfirm(){
                    var msg = "APROBAR LA SOLICITUD DE CREDITO # <?php echo $mutual_producto_solicitud_id?>";
                    msg = msg + "\n";
                    msg = msg + "PERIODO INICIO: <?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?>";

                    <?php if($solicitud['MutualProductoSolicitud']['beneficio_acuerdo_debito'] != 0):?>
                    msg = msg + "\n";
                    msg = msg + "\n";
                    msg = msg + "ATENCION!!!";
                    msg = msg + "\n";
                    msg = msg + "El Beneficio posee *** ACUERDO DE PAGO ***";
                    msg = msg + "\n";
                    msg = msg + "\n";
                    msg = msg + "Desea aprobar lo mismo esta solicitud?";
                    <?php endif;?>


//                    orgOrg = $('MutualProductoSolicitudOrganismoReasignado').getValue();
//
//                    if(orgOrg !== '<?php // echo $solicitud['MutualProductoSolicitud']['organismo']?>'){
//                        msg = msg + "\n";
//                        msg = msg + "\n";
//                        msg = msg + "ATENCION!! Se modifica el Beneficio";
//                        msg = msg + "\n";
//                        msg = msg + "Nuevo Organismo: " + getTextoSelect('MutualProductoSolicitudOrganismoReasignado');
//                    }    

                    return confirm(msg);
                }
		
		function ocultarOptionFPago(){
		
                    $("cta_banco").hide();
                    $("nro_opera").hide();
                    $("fPago").hide();
                    $("fVenc").hide();
                    $("importe").hide();
                    $("chq_cartera").hide();
				
				
		}	
		
		function seleccionPago(){
                    var seleccion = $('MovimientoTipoPago').getValue();

                    $("MovimientoFpagoYear").value = $F('MovimientoFechaOperacion').substring(0,4);
                    $("MovimientoFpagoMonth").value = $F('MovimientoFechaOperacion').substring(5,7);
                    $("MovimientoFpagoDay").value = $F('MovimientoFechaOperacion').substring(8);
				
                    $("MovimientoFvencYear").value = $F('MovimientoFechaOperacion').substring(0,4);
                    $("MovimientoFvencMonth").value = $F('MovimientoFechaOperacion').substring(5,7);
                    $("MovimientoFvencDay").value = $F('MovimientoFechaOperacion').substring(8);
		
                    ocultarOptionFPago();
		
                    if(seleccion === 'EF'){
			document.getElementById("MovimientoTipoPagoDesc").value = "EFECTIVO";
			$("cta_banco").hide();
			$("nro_opera").hide();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").show();
                    }
		
                    if(seleccion === 'CH'){
					
                        document.getElementById("MovimientoTipoPagoDesc").value = "CHEQUE PROPIO";
                        $("cta_banco").show();
			$("nro_opera").show();
			$("fPago").show();
			$("fVenc").show();
			$("importe").show();
		
			document.getElementById("MovimientoFpagoDay").disabled = true;
			document.getElementById("MovimientoFpagoMonth").disabled = true;
			document.getElementById("MovimientoFpagoYear").disabled = true;
					
                    }		
				
		
                    if(seleccion === 'TR'){
			document.getElementById("MovimientoTipoPagoDesc").value = "TRANSFERENCIA BANCARIA";
			$("cta_banco").show();
			$("nro_opera").show();
			$("fPago").hide();
			$("fVenc").show();
			$("importe").show();
                    }
		
		
                    if(seleccion === 'DB'){
			document.getElementById("MovimientoTipoPagoDesc").value = "DEBITO BANCARIO";
			$("cta_banco").hide();
			$("nro_opera").show();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").show();
                    }		
		
                    if(seleccion === 'CT'){
			$("MovimientoTipoPagoDesc").value = "CHEQUES EN CARTERA";
			$("cta_banco").hide();
			$("nro_opera").hide();
			$("fPago").hide();
			$("fVenc").hide();
			$("importe").hide();
			$("chq_cartera").show();
                    }		
				
		}
			

		function actualizaImporteAnt(valor){
                    v1 = valor;
                    v2 = document.getElementById("MovimientoImporteEfectivo").value;
                    v1 = new Number(v1);
                    v2 = new Number(v2);
                    document.getElementById("MovimientoImporteEfectivo").value = v1 + v2;
                    document.getElementById("MovimientoTipoPago").value = "";
                    ocultarOptionFPago();
		}


		function actualizaImporte(valor, idCheque){
                    var v1, v2, v3, acumulado;

                    v1 = valor;
                    v2 = $F("MovimientoImporteEfectivo");
                    v1 = new Number(v1);
                    v2 = new Number(v2);
                    v3 = new Number(v1 + v2);

                    acumulado = new Number($('MovimientoAcumula').getValue());
                    acumulado -= v1;
                    $("MovimientoAcumula").value = acumulado.toFixed(2);

                    if(idCheque > 0){
                        var checkbox = $('MovimientoCheck' + idCheque);
                        checkbox.checked = !checkbox.checked;
                        checkbox.enable();
                    }

                    if(v3.toFixed(2) === new Number("0").toFixed(2)){
                        $('btn_submit').enable();                    
                    }else{
                        $('btn_submit').disable();
                    }

                    v3 = v3.toFixed(2);
                    $("MovimientoImporteEfectivo").value = v3;
                    $("MovimientoTipoPago").value = "";
                    ocultarOptionFPago();
                }
	

		function actDatos(){
                    var importe, pago;

                    $('ajax_loader_2124618328').hide();

                    $("MovimientoBancoCuentaId").value = "";
                    $("MovimientoNumeroOperacion").value = "";	
                    $("MovimientoTipoPago").value = "";


                    importe = new Number($F('MovimientoImporteEfectivo'));
                    acumulado = new Number($('MovimientoAcumula').getValue());
                    pago = new Number($('MovimientoImportePago').getValue());

                    acumulado = pago - acumulado;
                    $('MovimientoImporteEfectivo').value = acumulado.toFixed(2);

                    if(acumulado.toFixed(2) >= importe){
                        actualizaImporte(-importe, 0);
                    }else{
                        actualizaImporte(0, 0);		
                    }
		}


		function chqOnclick(chequeId, importe, uuid){
                    var checkbox = $('MovimientoCheck' + chequeId);
                    var checked = 0;

                    if(checkbox.checked === false) return true;
                    if(checkbox.checked === true) checked = 1;

                    cargarRenglones(chequeId, checked, uuid);

                    return true;
		}	
		
		
		function cargarRenglones(chequeId, checked, uuid){
                    var fecha_operacion = $F('MovimientoFechaOperacion');
                    var importe_detalle = 0;
                    var importe_pago = $('MovimientoImportePago').getValue();
                    var clave = 'mutual_producto_solicitud_id';
                    var valor = $('MutualProductoSolicitudId').getValue();

                    new Ajax.Updater(
                        'grilla_pagos',
                        '<?php echo $this->base?>/proveedores/movimientos/cargar_cheques/'+chequeId+'/'+checked+'/'+fecha_operacion+'/'+importe_detalle+'/'+importe_pago+'/'+clave+'/'+valor+'/'+uuid, 
                        {
                            asynchronous:true, 
                            evalScripts:true,
                            onLoading:function(request) 
                            {
                                $('msjAjax_' + chequeId).show();
                            },
                            onComplete:function(request) 
                            {
                                actCheque(chequeId);
                            }, 
                            requestHeaders:['X-Update', 'grilla_pagos']
                        }
                    );

                    return true;
		
		}
		
		
		function actCheque(chequeId){
                    var checkbox = $('MovimientoCheck' + chequeId);
                    var txtImpCheque = checkbox.getValue();
                    var txtImpEfectivo = $("MovimientoImporteEfectivo").getValue();

                    var impEfectivo = new Number(txtImpEfectivo);
                    var impCheque = new Number(txtImpCheque);

                    $('msjAjax_' + chequeId).hide();
                    $('MovimientoTipoPago').value = '';


                    if(impCheque > impEfectivo){
                        checkbox.checked = false;
                    }else{
                        checkbox.disable();
                        impCheque = impCheque * (-1);
                        actualizaImporte(impCheque, 0);
                    }

                    ocultarOptionFPago();
		}

            </script>
	

            <div  class="areaDatoForm">	
		
                <table class="tbl_form">
                    <tr>
			<td>Forma de Pago</td>
			<td><?php echo $frm->input('Movimiento.tipo_pago',array('type' => 'select','options' => array('' => 'Seleccionar...', 'EF' => 'EFECTIVO', 'CT' => 'CHEQUES EN CARTERA', 'CH' => 'CHEQUES PROPIOS', 'TR' => 'TRANSFERENCIA BANCARIA', 'DB' => 'DEPOSITO BANCARIO'), 'onchange' => 'seleccionPago()', 'selected' => ''));?></td>
                    </tr>
                    <tr id="cta_banco">
			<td>Cuenta Bancaria</td>
			<td><?php echo $this->renderElement('banco_cuentas/combo_cuentas',array(
                                                            'plugin'=>'cajabanco',
                                                            'label' => "",
                                                            'model' => 'Movimiento.banco_cuenta_id',
                                                            'disabled' => false,
                                                            'empty' => false,
                                                            'selected' => 0))?>
			</td>			
                    </tr>
                    <tr id="nro_opera">
			<td>Nro.Operaci&oacute;n/Cheque</td>
			<td><?php echo $frm->input('Movimiento.numero_operacion', array('label'=>'','size'=>20,'maxlength'=>15)) ?></td>
                    </tr>
                    <tr id="fPago">
			<td>Fecha Pago</td>
			<td><?php echo $frm->calendar('Movimiento.fpago',null,$solicitud['MutualProductoSolicitud']['fecha_pago'],date('Y')-1,date('Y')+1)?></td>
                    </tr>
                    <tr id="fVenc">
			<td>Fecha Vencimiento</td>
			<td><?php echo $frm->calendar('Movimiento.fvenc',null,$solicitud['MutualProductoSolicitud']['fecha_pago'],date('Y')-1,date('Y')+1)?></td>
                    </tr>
                    <tr id="importe">
			<td>Importe</td>
			<td><?php echo $frm->money('Movimiento.importe_efectivo','') ?>
                            <?php //echo $controles->btnAjax('controles/add.png','/contabilidad/asientos/cargar_renglones','grilla_renglones','formAsiento')?>
                            <a href="<?php echo $this->base?>/proveedores/movimientos/cargar_renglones" id="link1568620940" onclick=" event.returnValue = false; return false;">
                                <img src="<?php echo $this->base?>/img/controles/add.png" border="0" alt="" />
                            </a>
                            <script type="text/javascript">
                                Event.observe('link1568620940', 'click', function(event){ 
                                    $('ajax_loader_2124618328').show();
                                    new Ajax.Updater('grilla_pagos', '<?php echo $this->base?>/proveedores/movimientos/cargar_renglones',{ 
                                                    asynchronous:true, evalScripts:true, onComplete:function(request, json){
                                                        actDatos();
                                                    },
                                                    parameters:$('formDetallePago').serialize(), 
                                                    requestHeaders:['X-Update', 'grilla_pagos']
                                    });
                                }, false);
                            </script>
                            <span id="ajax_loader_2124618328" style="display: none;font-size: 11px;font-style:italic;color:red;margin-left:10px;"><img src="<?php echo $this->base?>/img/controles/ajax-loader.gif" border="0" alt="" /></span>
			</td>
                    </tr>

                    <tr>
			<td colspan="2">
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
                                    <th></th>
				</tr>
				<?php foreach($chqCarteras as $chqCartera):?>
                                    <?php $i = $chqCartera['BancoChequeTercero']['id'];?>
                                    <tr id="CHQ_<?php echo $i?>">
                                        <td align="center"><?php echo $chqCartera['BancoChequeTercero']['id']?></td>
                                        <td><?php echo $chqCartera['BancoChequeTercero']['banco']?></td>
                                        <td><?php echo $util->armaFecha($chqCartera['BancoChequeTercero']['fecha_ingreso'])?></td>
                                        <td><?php echo $util->armaFecha($chqCartera['BancoChequeTercero']['fecha_vencimiento'])?></td>
                                        <td><?php echo $chqCartera['BancoChequeTercero']['numero_cheque'] ?></td>
                                        <td><?php echo $chqCartera['BancoChequeTercero']['librador'] ?></td>
                                        <td align="right"><strong><?php echo number_format($chqCartera['BancoChequeTercero']['importe'],2)?></strong></td>
                                        <td><input type="checkbox" name="data[Movimiento][id_check][<?php echo $chqCartera['BancoChequeTercero']['id'] ?>]"  id="MovimientoCheck<?php echo $i?>" value='<?php echo $chqCartera['BancoChequeTercero']['importe']?>' onclick="chqOnclick('<?php echo $i?>', '<?php echo $chqCartera['BancoChequeTercero']['importe'] * (-1)?>', '<?php echo $uuid?>')"/></td>
                                        <td><?php echo $controles->ajaxLoader('msjAjax_' . $i,'')?></td>
                                    </tr>
				<?php endforeach;?>	
                            </table>
			</td>
                    </tr>

                    <tr>
			<td colspan="2" id="grilla_pagos"></td>
                    </tr>
		</table>
            </div>
	
            <div style="clear: both;"></div>
            <?php echo $frm->hidden("Movimiento.proveedor_id", array('value' => (empty($solicitud['MutualProductoSolicitud']['reasignar_proveedor_id']) ? $solicitud['MutualProductoSolicitud']['proveedor_id'] : $solicitud['MutualProductoSolicitud']['reasignar_proveedor_id']))) ?>
            <?php echo $frm->hidden("Movimiento.destinatario", array('value' => $solicitud['MutualProductoSolicitud']['beneficiario_apenom'])) ?>
            <?php echo $frm->hidden("Movimiento.observacion", array('value' => $solicitud['MutualProductoSolicitud']['beneficiario_apenom'] . ' #'. $solicitud['MutualProductoSolicitud']['id'])) ?>
            <?php echo $frm->hidden('Movimiento.fecha_operacion', array('value' => date('Y-m-d'))) ?>
            <?php echo $frm->hidden('Movimiento.tipo_pago_desc') ?>
            <?php echo $frm->hidden('Movimiento.acumula', array('value' => 0)) ?>
            <?php echo $frm->hidden('Movimiento.importe_pago', array('value' => $importePago)) ?>
            <div style="clear: both;"></div>

	<?php 
	/***************************************************************************************************************************
	 * APRUEBO LA ORDEN
	 ***************************************************************************************************************************/
	?>
    
   
	<div class=notices_error2>
            <strong>ATENCION!</strong>
            <br/>
            INICIA EN: <strong><?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?></strong>
            &nbsp;|&nbsp;1er. VTO CLIENTE: <strong><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['primer_vto_socio'])?></strong>
	</div>
	<?php if($solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] == 1):?>
		<div class=notices_error>
                    <strong>**** NO SE PUEDE APROBAR ESTA SOLICITUD PORQUE EL PERIODO DE INICIO CORRESPONDE A UNA LIQUIDACION CERRADA ( <?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?> )***</strong>
                    <br/><br/>
                    Deber&aacute; abrir la liquidaci&oacute;n de <?php echo $util->periodo($solicitud['MutualProductoSolicitud']['periodo_ini'])?> (siempre y cuando no este imputada)
                    para aprobar la presente solicitud.
                    <br/><br/>
                    <strong>RECUERDE: </strong> Una vez aprobada la presente, deber&aacute; efectuar la reliquidaci&oacute;n del cliente a los efectos de que
                    esta solicitud (#<?php echo $mutual_producto_solicitud_id?>) sea inclu&iacute;da en la liquidaci&oacute;n del periodo abierto.
		</div>
	<?php endif;?>
        <?php if($solicitud['MutualProductoSolicitud']['beneficio_acuerdo_debito'] != 0):?>
            <div class="notices_error2" style="width: 100%;">
                <p><strong>ATENCION!!</strong></p>
                <p>El beneficio de la presente solicitud posee un <strong>ACUERDO DE DEBITO POR *** <?php echo $solicitud['MutualProductoSolicitud']['beneficio_acuerdo_debito']?> ***</strong></p>

            </div>
        <?php endif;?>            

        <table class="tbl_form">
            <tr>
                <td>NRO.REF.PROV.</td>
                <td colspan="2"><?php echo $frm->input('MutualProductoSolicitud.nro_referencia_proveedor',array('size'=>15,'maxlenght'=>15)); ?></td>
            </tr>
        </table>
            
	<?php echo $frm->hidden('MutualProductoSolicitud.id',array('value' => $mutual_producto_solicitud_id))?>
	<?php echo $frm->hidden('MutualProductoSolicitud.aprobar',array('value' => 1))?>
	<?php echo $frm->hidden("Movimiento.uuid", array('value' => $uuid)) ?>
        <?php echo $frm->hidden('MutualProductoSolicitud.organismo',array('value' => $solicitud['MutualProductoSolicitud']['organismo']))?>
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'APROBAR SOLICITUD DE CREDITO #' . $mutual_producto_solicitud_id,'URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/pendientes_aprobar_opago" : $fwrd) ))?>
	
	<?php //debug($solicitud) ?>
<?php

echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>NUEVA SOLICITUD</h3>
<hr/>
<?php echo $this->renderElement('solicitudes/alta_info_datos',array('plugin' => 'ventas','solicitud' => $solicitud))?>
<?php echo $form->create(null,array('action' => 'alta_plan/'.$TOKEN_ID,'name'=>'formAltaPlan','onsubmit' => "return validar()",'id'=>'formAltaPlan','type' => 'file'));?>

<script type="text/javascript">
    Event.observe(window, 'load', function () {

        $('btnProcessformAltaPlan').disable();

    <?php if(!$solicitud['Persona']['fallecida']):?>
        $('ProveedorPlanCuotaGrillaMontoId').enable();
        $('ProveedorPlanGrillaCuotaCuotaId').enable();
        $('MutualProductoSolicitudFormaPago').enable();
        $('MutualProductoSolicitudObservaciones').enable();
        getComboPlanes();
        getCapacidadPago();
    <?php else:?>
        $('MutualProductoSolicitudPersonaBeneficioId').disable();
        $('ProveedorPlanCuotaGrillaMontoId').disable();
        $('ProveedorPlanGrillaCuotaCuotaId').disable();
        $('MutualProductoSolicitudFormaPago').disable();
        $('MutualProductoSolicitudObservaciones').disable();
        $('MutualProductoSolicitudSueldoNeto').disable();
        $('MutualProductoSolicitudDebitosBancarios').disable();
    <?php endif;?>



        $('MutualProductoSolicitudPersonaBeneficioId').observe('change', function () {
            getComboPlanes();
            getCapacidadPago();
        });

        $('ProveedorPlanId').observe('change', function () {
            $('ProveedorPlanCuotaGrillaMontoId').enable();
            $('ProveedorPlanGrillaCuotaCuotaId').enable();
            $('MutualProductoSolicitudFormaPago').enable();
            $('MutualProductoSolicitudObservaciones').enable();
//        $('MutualProductoSolicitudVendedorId').enable();        
            getComboMontos();
            getDocumentosPlan();
        });

        $('ProveedorPlanCuotaGrillaMontoId').observe('change', function () {
            getComboCuotas();
        });
        $('MutualProductoSolicitudSueldoNeto').focus();
        $('MutualProductoSolicitudDebitosBancarios').observe('blur', function (event) {
            var valor = parseFloat($('MutualProductoSolicitudDebitosBancarios').getValue()).toFixed(2);
            if (isNaN(valor)) {
                document.getElementById('MutualProductoSolicitudDebitosBancarios').value = '0.00';
            }
        });

    });

    function validar() {

        var sueldoNeto = parseFloat($('MutualProductoSolicitudSueldoNeto').getValue()).toFixed(2);
        if (isNaN(sueldoNeto) || sueldoNeto <= 0) {
            alert('Debe indicar el Sueldo Neto!');
            $('MutualProductoSolicitudSueldoNeto').focus();
            return false;
        } else {
            return true;
        }

    }

    function getCapacidadPago() {
        beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
        var url = '<?php echo $this->base?>/pfyj/persona_beneficios/get_capacidadad_pago/' + beneficio;
        new Ajax.Request(url, {
            asynchronous: true,
            evalScripts: true,
            onInteractive: function (request) {
                $('spinner_beneficio').show();
            },
            onSuccess: function (response) {
                $('spinner_beneficio').hide();
                var json = response.responseText.evalJSON();
                document.getElementById('MutualProductoSolicitudSueldoNeto').value = parseFloat(json.sueldo_neto).toFixed(2);
                document.getElementById('MutualProductoSolicitudDebitosBancarios').value = parseFloat(json.debitos_bancarios).toFixed(2);
            }
        });
    }


    function getComboPlanes() {
        beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
        new Ajax.Updater('ProveedorPlanId', '<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/' + beneficio + '/P/0/0/0/0', {asynchronous: false, evalScripts: true, onComplete: function (request, json) {
                $('spinner_plan').hide();
                $('btnProcessformAltaPlan').enable();
                getComboMontos();
                getDocumentosPlan();
            }, onLoading: function (request) {
                Element.show('spinner_plan');
                $('btnProcessformAltaPlan').disable();
            }, requestHeaders: ['X-Update', 'ProveedorPlanId']});
    }

    function getComboMontos() {
        planID = $('ProveedorPlanId').getValue();
        if (planID !== null) {
            beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
            new Ajax.Updater('ProveedorPlanCuotaGrillaMontoId', '<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/' + beneficio + '/M/' + planID, {asynchronous: false, evalScripts: true, onComplete: function (request, json) {
                    $('spinner_montos').hide();
                    $('btnProcessformAltaPlan').enable();
                    getComboCuotas();
                }, onLoading: function (request) {
                    Element.show('spinner_montos');
                    $('btnProcessformAltaPlan').disable();
                }, requestHeaders: ['X-Update', 'ProveedorPlanCuotaGrillaMontoId']});
        } else {
            $('btnProcessformAltaPlan').disable();
            alert("**** NO EXISTEN PLANES DISPONIBLES PARA EL ORGANISMO DEL BENEFICIO ****");
        }
    }

    function getComboCuotas() {
        planID = $('ProveedorPlanId').getValue();
        beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
        fecha = '<?php echo date('Y-m-d')?>';
// 	$('spinner_msg').update("CARGANDO CUOTAS");
        cuotaID = $('ProveedorPlanCuotaGrillaMontoId').getValue();
        if (cuotaID !== null) {
            new Ajax.Updater('ProveedorPlanGrillaCuotaCuotaId', '<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/' + beneficio + '/C/' + planID + '/' + cuotaID, {asynchronous: false, evalScripts: true, onComplete: function (request, json) {
                    $('spinner_cuotas').hide();
                    $('btnProcessformAltaPlan').enable();
                }, onLoading: function (request) {
                    Element.show('spinner_cuotas');
                    $('btnProcessformAltaPlan').disable();
                }, requestHeaders: ['X-Update', 'ProveedorPlanGrillaCuotaCuotaId']});
        } else {
            $('ProveedorPlanCuotaGrillaMontoId').disable();
            $('ProveedorPlanGrillaCuotaCuotaId').disable();
            $('ProveedorPlanGrillaCuotaCuotaId').update("");
//        $('MutualProductoSolicitudFormaPago').disable();
//        $('MutualProductoSolicitudObservaciones').disable();
//        $('MutualProductoSolicitudVendedorId').disable();
            $('btnProcessformAltaPlan').disable();
            alert("**** NO EXISTEN MONTOS DISPONIBLES PARA PLAN ****");
        }
    }
    
    function getDocumentosPlan() {
        planID = $('ProveedorPlanId').getValue();
        if (planID !== null) {
            new Ajax.Updater('ProveedorPlanDocumentoId', '<?php echo $this->base?>/proveedores/proveedor_planes/documentos_plan/' + planID, {asynchronous: false, evalScripts: true, onComplete: function (request, json) {
                    $('btnProcessformAltaPlan').enable();
                }, onLoading: function (request) {
                    $('btnProcessformAltaPlan').disable();
                }, requestHeaders: ['X-Update', 'ProveedorPlanDocumentoId']});
        } else {
            $('btnProcessformAltaPlan').disable();
            alert("**** NO EXISTEN PLANES DISPONIBLES PARA EL ORGANISMO DEL BENEFICIO ****");
        }
    }
    

</script>

<div class="areaDatoForm">
    <h3>INFORMACION DE LA SOLICITUD</h3>
    <hr/>

    <div style="border-bottom: 1px solid #BAB8B7;width: 100%;">
        <table class="tbl_form">
            <tr>
                <td><label for="MutualProductoSolicitudPersonaBeneficioId">BENEFICIO</label></td>
                <td>
                            <?php echo $this->renderElement('persona_beneficios/combo_beneficios',array('plugin' => 'pfyj','model'=>'MutualProductoSolicitud','persona_id' => $solicitud['Persona']['id'],'soloActivos' => 1,'style' => 'font-weight: bold;'))?>
                            <?php if(!$solicitud['Persona']['fallecida']) echo $controles->botonGenerico('/ventas/solicitudes/alta_beneficio/'.$TOKEN_ID,'controles/add.png','Alta Nuevo Medio de Pago')?>

                </td>
            </tr>
            <tr>
                <td><label>CAPACIDAD DE PAGO</label></td>
                <td>
                    <strong><?php echo $frm->money('MutualProductoSolicitud.sueldo_neto','SUELDO NETO *',(isset($this->data['MutualProductoSolicitud']['sueldo_neto']) ? $this->data['MutualProductoSolicitud']['sueldo_neto'] : '0.00')); ?></strong>
                        <?php echo $frm->money('MutualProductoSolicitud.debitos_bancarios','DEBITOS BANCARIOS',(isset($this->data['MutualProductoSolicitud']['debitos_bancarios']) ? $this->data['MutualProductoSolicitud']['debitos_bancarios'] : '0.00')); ?>
                    <div id="spinner_beneficio" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div>
                </td>
            </tr>            
        </table>
    </div>

    <div style="border-bottom: 1px solid #BAB8B7;width: 100%;">
        <table class="tbl_form">
            <tr>
                <td><label for="ProveedorPlanId">PLAN</label></td>
                <td>
                    <table class="tbl_form">
                        <tr>
                            <td><select style="font-weight: bold;" name="data[ProveedorPlan][id]" id="ProveedorPlanId"></select></td>
                            <td><div id="spinner_plan" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td><label>MONTOS Y CUOTAS</label></td>
                <td>
                    <table class="tbl_form">
                        <tr>
                            <td><select style="font-size: 15px;font-weight: bold;" name="data[ProveedorPlanGrillaCuota][monto_id]" id="ProveedorPlanCuotaGrillaMontoId"></select></td>
                            <td><div id="spinner_montos" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div></td>
                            <td><select style="font-size: 15px;" name="data[ProveedorPlanGrillaCuota][cuota_id]" id="ProveedorPlanGrillaCuotaCuotaId"></select></td>
                            <td><div id="spinner_cuotas" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>            
        </table>
    </div>    

    <table class="tbl_form">
        <tr>
            <td>FORMA DE PAGO</td>
            <td>
                <?php echo $this->renderElement('global_datos/combo_global',array(
                                                                                    'plugin'=>'config',
                                                                                    'metodo' => "get_fpago_solicitud",
                                                                                    'model' => 'MutualProductoSolicitud.forma_pago',
                                                                                    'empty' => false,
                                                                                    'selected' => $this->data['MutualProductoSolicitud']['forma_pago']
                ))?>				
            </td>
        </tr>
        <tr>
            <td>OBSERVACIONES</td>
            <td><?php echo $frm->textarea('MutualProductoSolicitud.observaciones',array('cols' => 60, 'rows' => 10))?></td>
        </tr>         
        <?php if(!empty($cancelaciones)):?>  
        <tr>
            <td>CANCELACIONES</td>
            <td>
                <script language="Javascript" type="text/javascript">
                    var rows = <?php echo (!empty($cancelaciones) ? count($cancelaciones) : 0)?>;

                    Event.observe(window, 'load', function () {
                        SelSum();
                    });

                    function chkOnclick() {
                        SelSum();
                    }
                    function SelSum() {
                        var totalSeleccionado = 0;
                        for (i = 1; i <= rows; i++) {
                            var ret = true;
                            var celdas = $('TRL_' + i).immediateDescendants();
                            oChkCheck = document.getElementById('CancelacionOrdenId_' + i);
                            toggleCell('TRL_' + i, oChkCheck);
                            impoPago = new Number(parseInt(oChkCheck.value));
                            if (oChkCheck.checked) {
                                totalSeleccionado = totalSeleccionado + impoPago;
                            }
                        }
                        totalSeleccionado = totalSeleccionado / 100;
                        totalSeleccionado = FormatCurrency(totalSeleccionado);
                        $('total_seleccionado').update(totalSeleccionado);
                    }

                </script>

                <div class="areaDatoForm3">
                    <h4>Ordenes de Cancelaci&oacute;n Emitidas</h4>
                    <table>
                        <tr>
                            <th></th>
                            <th>#</th>
                            <th>VENCIMIENTO</th>
                            <th>A LA ORDEN DE</th>
                            <th>CONCEPTO</th>
                            <th>IMPORTE</th>
                        </tr>
    <?php 
        $i=0;
        foreach ($cancelaciones as $cancelacion):
            $i++;
    ?>
                        <tr id="TRL_<?php echo $i?>">
                            <td><input type="checkbox" name="data[MutualProductoSolicitud][CancelacionOrden][<?php echo $cancelacion['CancelacionOrden']['id']?>]" value="<?php echo number_format(round($cancelacion['CancelacionOrden']['importe_proveedor'],2) * 100,0,".","")?>" id="CancelacionOrdenId_<?php echo $i?>" onclick="chkOnclick()"/></td>
                            <td><?php echo $cancelacion['CancelacionOrden']['id']?></td>
                            <td><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></td>
                            <td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
                            <td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
                            <td align="right"><?php echo $util->nf($cancelacion['CancelacionOrden']['importe_proveedor'])?></td>
                        </tr>
    <?php endforeach;?>
                        <tr class="subtotales">
                            <th class="subtotales" colspan="5" style="text-align: right;">TOTAL A CANCELAR</th>
                            <th class="subtotales" style="text-align: right;"><div id="total_seleccionado">0.00</div></th>
                        </tr>
                    </table> 
                </div>
            </td></tr>
        <?php endif;?>
        <tr>
            <td>DOCUMENTACION</td>
            <td>
                <div class="areaDatoForm3">
                    <h4>Adjuntar Documentaci&oacute;n</h4>
                    <table class="tbl_form"> 
                            <tbody name="data[ProveedorPlanDocumento][codigo_documento]" id="ProveedorPlanDocumentoId"></tbody>
                            
                        <!--tr>
                            <td>ARCHIVO 1</td><td><input type="file" name="data[MutualProductoSolicitud][archivo_1]" id="MutualProductoSolicitudArchivo1"/></td>
                        </tr>
                        <tr>
                            <td>ARCHIVO 2</td><td><input type="file" name="data[MutualProductoSolicitud][archivo_2]" id="MutualProductoSolicitudArchivo2"/></td>
                        </tr>
                        <tr>
                            <td>ARCHIVO 3</td><td><input type="file" name="data[MutualProductoSolicitud][archivo_3]" id="MutualProductoSolicitudArchivo3"/></td>
                        </tr> 
                        <tr>
                            <td>ARCHIVO 4</td><td><input type="file" name="data[MutualProductoSolicitud][archivo_4]" id="MutualProductoSolicitudArchivo4"/></td>
                        </tr>
                        <tr>
                            <td>ARCHIVO 5</td><td><input type="file" name="data[MutualProductoSolicitud][archivo_5]" id="MutualProductoSolicitudArchivo5"/></td>
                        </tr--> 

                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>
        <?php echo $this->renderElement('mutual_producto_solicitudes/info_operaciones_pendientes_by_persona', array(
           'plugin' => 'mutual',
            'persona_id' => $solicitud['Persona']['id'],
        ));?>
<hr/>

<?php if(!$solicitud['Persona']['fallecida']):?>
<input type="submit" value="SIGUIENTE" id="btnProcessformAltaPlan"/>
<?php else:?>
<input type="submit" value="SIGUIENTE" id="btnProcessformAltaPlan" disabled="disabled"/>
<?php endif;?>
<?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
<?php echo $form->end();?>


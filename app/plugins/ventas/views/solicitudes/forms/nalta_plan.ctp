<?php
#CONTROL DEL MODULO DE NOSIS VALIDACION DE IDENTIDAD
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_NOSIS_CBU = (isset($INI_FILE['general']['nosis_validar_cbu']) && $INI_FILE['general']['nosis_validar_cbu'] == 1 ? TRUE : FALSE);        
#MODULO DE TARJETAS DE DEBITO
$MOD_TARJETAS = (isset($INI_FILE['general']['tarjetas_de_debito']) && $INI_FILE['general']['tarjetas_de_debito'] == 1 ? TRUE : FALSE);
$MOD_SIISA = (isset($INI_FILE['general']['modulo_siisa']) && $INI_FILE['general']['modulo_siisa'] != 0 ? TRUE : FALSE);
?>

<script type="text/javascript">

$(document).ready(function (){
    <?php if($solicitud['Persona']['fallecida'] == 1):?>
    $("#formAltaPlan *").prop("disabled", true);
    <?php endif;?> 
    getCapacidadPago(); 
    getComboPlanes();

    $('#MutualProductoSolicitudPersonaBeneficioId').change(function () {
        getCapacidadPago();
        getComboPlanes(); 
        <?php if($MOD_SIISA):?>
        consultaSIISA();
        <?php endif;?>        
    }); 
    
    $('#MutualProductoSolicitudSueldoNeto').blur(function () {
        <?php if($MOD_SIISA):?>
        consultaSIISA();
        <?php endif;?>
    });
    
    $('#MutualProductoSolicitudDebitosBancarios').blur(function () {
        <?php if($MOD_SIISA):?>
        consultaSIISA();
        <?php endif;?>
    });
        
    
    $('#ProveedorPlanId').change(function () {getComboMontos();getDocumentosPlan();});
    $('#ProveedorPlanCuotaGrillaMontoId').change(function () {getComboCuotas();});
    
    $('#ProveedorPlanGrillaCuotaCuotaId').change(function () {
    	getCuota();
    });
    
    
    
});

function getCapacidadPago(){
    beneficio = $('#MutualProductoSolicitudPersonaBeneficioId').val();
    var url = '<?php echo $this->base?>/pfyj/persona_beneficios/get_capacidadad_pago/' + beneficio;
    $.ajax({
        url: url,
        data: {},
        type: "GET",
        dataType: "json"
    }).done(function (json) {
        $('#MutualProductoSolicitudSueldoNeto').val(parseFloat(json.sueldo_neto).toFixed(2));
        $('#MutualProductoSolicitudDebitosBancarios').val(parseFloat(json.debitos_bancarios).toFixed(2));
    });    
}

function getComboPlanes(){
	$('#btn_submit').prop('disabled', false);
    beneficio = $('#MutualProductoSolicitudPersonaBeneficioId').val();
    var url = '<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/P/0/0/0/0';
    $.ajax({
        url: url,
        data: {},
        type: "GET",
        dataType: "html"
    }).done(function (html) {
        $('#ProveedorPlanId').html(html);
        getComboMontos();
        getDocumentosPlan();
    });    
}

function getComboMontos(){
	$('#btn_submit').prop('disabled', false);
    var beneficio = $('#MutualProductoSolicitudPersonaBeneficioId').val();
    var planID = $('#ProveedorPlanId').val();
    if(!planID){
        alert("**** NO EXISTEN PLANES DISPONIBLES PARA EL ORGANISMO DEL BENEFICIO ****");
        $('#btn_submit').prop('disabled', true);
    }else{
        var url = '<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/M/' + planID;
        $.ajax({
            url: url,
            data: {},
            type: "GET",
            dataType: "html"
        }).done(function (html) {
            $('#ProveedorPlanCuotaGrillaMontoId').html(html);
            getComboCuotas();
            getDocumentosPlan();
        });    
    }
    
}

function getComboCuotas(){
	$('#btn_submit').prop('disabled', false);
    planID = $('#ProveedorPlanId').val();
    beneficio = $('#MutualProductoSolicitudPersonaBeneficioId').val();
    fecha = '<?php echo date('Y-m-d')?>';
    cuotaID = $('#ProveedorPlanCuotaGrillaMontoId').val();
    if(!cuotaID){
        alert("**** NO EXISTEN MONTOS DISPONIBLES PARA PLAN ****");
        $('#btn_submit').prop('disabled', true);
    }else{
        var url = '<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/C/' + planID + '/' + cuotaID;
        $.ajax({
            url: url,
            data: {},
            type: "GET",
            dataType: "html"
        }).done(function (html) {
            $('#ProveedorPlanGrillaCuotaCuotaId').html(html);
            getCuota();
        });        
    }
}

function getCuota(){
    cuotaID = $('#ProveedorPlanGrillaCuotaCuotaId').val();
    var url = '<?php echo $this->base ?>/proveedores/proveedor_planes/get_cuota_grilla/'+ cuotaID;
    $.ajax({
        url: url,
        data: {},
        type: "GET",
        dataType: "json"
    }).done(function (json) {

        $('#MutualProductoSolicitudCuotaGrillaId').val(json.liquido);
        $('#MutualProductoSolicitudCuotaGrillaImporte').val(json.importe);
        
        <?php if($MOD_SIISA):?>
        consultaSIISA();
        <?php endif;?>            

    });    
}

function getDocumentosPlan() {
    planID = $('#ProveedorPlanId').val();
    if (planID !== null) {
        var url = '<?php echo $this->base?>/proveedores/proveedor_planes/documentos_plan/'+ planID;
        $.ajax({
            url: url,
            data: {},
            type: "GET",
            dataType: "html"
        }).done(function (html) {
            $('#ProveedorPlanDocumentoId').html(html);
        });         
    }
}

function validar(){
	$('#btn_submit').prop('disabled', false);
    var liquido = parseFloat($('#MutualProductoSolicitudCuotaGrillaId').val());
    var totalCancela = parseFloat($('#TotalCancela').val());
    totalCancela = (!isNaN(totalCancela) ? totalCancela : 0);
    if(liquido < totalCancela){
        alert('El monto solicitado [' + liquido + '] NO PUEDE SER MENOR que el TOTAL a Cancelar [' + totalCancela + ']!');
        $('#btn_submit').prop('disabled', true);
        return false;
    }
    return true;
}


</script>

<div class="card mb-1">
    <div class="card-header bg-info text-white"><i class="fas fa-handshake"></i>&nbsp;Información de la Solicitud</div>
    <div class="card-body">
        <?php echo $form->create(null,array('action' => 'alta_plan/'.$TOKEN_ID,'name'=>'formAltaPlan', 'id' => 'formAltaPlan','onsubmit' => "return validar()",'id'=>'formAltaPlan','type' => 'file'));?>
        <div class="form-row">
            <div class="col-12">
                Solicitante: <strong><?php echo $solicitud['Persona']['tdoc_ndoc']?></strong> - <strong><?php echo $solicitud['Persona']['apenom']?></strong>
            </div>
        </div>
        <hr/>
        <div class="form-row">
            <div class="form-group col-md-10">
                <?php $beneficios = $this->requestAction('/pfyj/persona_beneficios/beneficios_by_persona/'.$solicitud['Persona']['id'].'/1');?>                
                <label for="MutualProductoSolicitudPersonaBeneficioId">Beneficio / Medio de Pago</label>
                <select class="form-control" id="MutualProductoSolicitudPersonaBeneficioId" name="data[MutualProductoSolicitud][persona_beneficio_id]">
                    <?php if(!empty($beneficios)):?>
                    <?php foreach($beneficios as $beneficio):?>
                    <option value="<?php echo $beneficio['PersonaBeneficio']['id']?>"><?php echo $beneficio['PersonaBeneficio']['string']?></option>
                    <?php endforeach;?>
                    <?php endif;?>
                </select>              
            </div>
            <div class="form-group col-md-1">
                <label for="btnAddBeneficio"><span style="visibility: hidden;">Nuevo</span></label>                
                <button type="button" data-toggle="modal" data-target="#altaBeneficioModal" class="btn btn-secondary btn-link form-control"><i class="fas fa-plus-square"></i></button>
            </div>
            <?php if($MOD_TARJETAS):?>	
           	<div class="form-group col-md-1">
                <label for="btnModificaTarjetaDebito"><span style="visibility: hidden;">Nuevo</span></label>                
                <button type="button" data-toggle="modal" data-target="#modificaTarjetaModal" class="btn btn-primary btn-link form-control"><i class="far fa-credit-card"></i></button>
            </div>
            <?php endif;?>            
        </div>
        <div class="form-row">
            <div class="form-group col-md-2">
                Capacidad de Pago
            </div>
            <div class="form-group col-md-2">
                <label for="MutualProductoSolicitudSueldoNeto"><strong>Sueldo Neto *</strong></label>
                <input class="form-control" id="MutualProductoSolicitudSueldoNeto" step="0.01" name="data[MutualProductoSolicitud][sueldo_neto]" required=""  value="<?php echo (isset($this->data['MutualProductoSolicitud']['sueldo_neto']) ? $this->data['MutualProductoSolicitud']['sueldo_neto'] : '0.00')?>" type="number" >
            </div>
            <div class="form-group col-md-2">
                <label for="MutualProductoSolicitudDebitosBancarios">Débitos Bancarios</label>
                <input class="form-control" id="MutualProductoSolicitudDebitosBancarios" step="0.01" name="data[MutualProductoSolicitud][debitos_bancarios]" required=""  value="<?php echo (isset($this->data['MutualProductoSolicitud']['debitos_bancarios']) ? $this->data['MutualProductoSolicitud']['debitos_bancarios'] : '0.00')?>" type="number" >
            </div>             
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <div class="card mb-1">
                    <div class="card-header">Datos del Prestamo</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="ProveedorPlanId">Plan</label>
                                <select class="form-control" id="ProveedorPlanId" name="data[ProveedorPlan][id]">
                                </select>                  
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="ProveedorPlanCuotaGrillaMontoId">Monto</label>
                                <select class="form-control" id="ProveedorPlanCuotaGrillaMontoId" name="data[ProveedorPlanGrillaCuota][monto_id]">
                                </select>                  
                            </div>
                            <div class="form-group col-md-4">
                                <label for="ProveedorPlanGrillaCuotaCuotaId">Cuota</label>
                                <select class="form-control" id="ProveedorPlanGrillaCuotaCuotaId" name="data[ProveedorPlanGrillaCuota][cuota_id]">
                                </select>                  
                            </div>
                            <div class="form-group col-md-4">
                                <?php $datos = $this->requestAction('/config/global_datos/get_fpago_solicitud');?>
                                <label for="MutualProductoSolicitudFormaPago">Forma de Liquidaci&oacute;n</label>
                                <select class="form-control" id="MutualProductoSolicitudFormaPago" name="data[MutualProductoSolicitud][forma_pago]">
                                    <?php if(!empty($datos)):?>
                                    <?php foreach($datos as $id => $dato):?>
                                    <option value="<?php echo $id?>"><?php echo $dato?></option>
                                    <?php endforeach;?>
                                    <?php endif;?>
                                </select>                        
                            </div>                            
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="observaciones">Observaciones</label>
                                <textarea style="resize: none;" class="form-control" id="observaciones" name="data[MutualProductoSolicitud][observaciones]" rows="5" ></textarea>
                            </div>                            
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <div class="card mb-1">
                                    <div class="card-header">Adjuntar Documentación</div>
                                    <div class="card-body">
                                        <small>
                                        <table class="tbl_form"> 
                                            <tbody name="data[ProveedorPlanDocumento][codigo_documento]" id="ProveedorPlanDocumentoId"></tbody>
<!--                                            <tr>
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
                                            </tr>                     -->
                                        </table>
                                        </small>    
                                    </div>
                                </div>
                            </div>                            
                            <div class="form-group col-md-7">
                                <div class="card mb-1">
                                    <div class="card-header">Ordenes de Cancelación Emitidas</div>
                                    <div class="card-body">
                                        <small>
                                        <?php if(!empty($cancelaciones)):?>
                                        <script>
                                            var rows = <?php echo (!empty($cancelaciones) ? count($cancelaciones) : 0)?>;
                                            $(document).ready(function (){
                                                SelSum();
                                            });
                                            function chkOnclick(){
                                                SelSum();
                                            }
                                            function SelSum(){
                                                var totalSeleccionado = 0;
                                                for (i=1;i<=rows;i++){
                                                    
                                                    oChkCheck = $('#CancelacionOrdenId_' + i);

                                                    impoPago = new Number(parseInt(oChkCheck.val()));
                                                    if (oChkCheck.prop('checked')){
                                                        totalSeleccionado = totalSeleccionado + impoPago;
                                                    }
                                                    
                                                }
                                                totalSeleccionado = totalSeleccionado/100;
                                                totalSeleccionado = parseFloat(totalSeleccionado).toFixed(2);

                                                $('#total_seleccionado').html(totalSeleccionado); 
                                                $('#TotalCancela').val(totalSeleccionado);
                                                
                                            }
                                        </script>    
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                        <th></th>
                                                        <th>#</th>
                                                        <th>Vencimiento</th>
                                                        <th>A la órden de</th>
                                                        <th>Concepto</th>
                                                        <th>Importe</th>
                                                </tr>                                                
                                            </thead>
                                            <tbody>
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
                                                        <td class="text-right"><?php echo $util->nf($cancelacion['CancelacionOrden']['importe_proveedor'])?></td>
                                                    </tr>                                                
                                                <?php endforeach;?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="5" class="text-right">Total a Cancelar</th>
                                                    <th class="text-right font-weight-bold">
                                                        <div id="total_seleccionado">0.00</div>
                                                        <input type="hidden" id="TotalCancela" value="0">
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <?php endif;?>
                                        </small>    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>


        </div>
        <div class="form-row">
            <div class="col-12">
            <?php $solicitudesPendientes = $this->requestAction('/mutual/mutual_producto_solicitudes/get_operaciones_pendientes_by_persona/'.$solicitud['Persona']['id']);?>                
            <?php if(!empty($solicitudesPendientes)):?>
                <small>
                <table class=" table table-sm">
                    <thead>
                        <tr>
                            <th colspan="7" class="bg-warning text-white"><strong>ATENCION!</strong> El solicitante posee OTRAS OPERACIONES PENDIENTES DE APROBACION.</th>
                        </tr>
                        <tr class="text-center">
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Producto</th>
                            <th>Solicitado</th>
                            <th>Cuotas</th>
                            <th>Importe</th>
                        </tr>                        
                    </thead>
                    <tbody>
                        <?php $TOTAL_MENSUAL = $TOTAL_SOLICITADO = 0;?>
                        <?php foreach($solicitudesPendientes as $solicitudPendiente):?>
                        <tr>
                        <td class="text-center"><?php echo $solicitudPendiente['MutualProductoSolicitud']['id']?></td>
                        <td class="text-center"><?php echo $util->armaFecha($solicitudPendiente['MutualProductoSolicitud']['fecha'])?></td>
                        <td class="text-center"><?php echo $solicitudPendiente['EstadoSolicitud']['concepto_1']?></td>
                        <td class="text-center"><?php echo $solicitudPendiente['TipoProducto']['concepto_1']?></td>
                        <td class="text-right font-weight-bold"><?php echo $util->nf($solicitudPendiente['MutualProductoSolicitud']['importe_percibido'])?></td>
                        <td class="text-center font-weight-bold"><?php echo $solicitudPendiente['MutualProductoSolicitud']['cuotas']?></td>
                        <td class="text-right font-weight-bold"><?php echo $util->nf($solicitudPendiente['MutualProductoSolicitud']['importe_cuota'])?></td>
                        </tr>
                        <?php 
                        $TOTAL_MENSUAL += $solicitudPendiente['MutualProductoSolicitud']['importe_cuota'];
                        $TOTAL_SOLICITADO += $solicitudPendiente['MutualProductoSolicitud']['importe_solicitado'];
                        ?>                        
                        <?php endforeach;?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">TOTAL PENDIENTE DE APROBAR</th>
                            <th class="text-right font-weight-bold"><?php echo $util->nf($TOTAL_SOLICITADO)?></th>
                            <th></th>
                            <th class="text-right font-weight-bold"><?php echo $util->nf($TOTAL_MENSUAL)?></th>
                        </tr>
                    </tfoot>
                </table>
                </small>    
            <?php endif;?>    
              
            </div>
        </div> 
        <?php if($MOD_SIISA):?>  
        
            <script type="text/javascript">
            
            	// MutualProductoSolicitudSiisa
            
            	function consultaSIISA() {
            	
            		var payload = {
            			"data": {
                			"sueldo_neto": parseFloat($('#MutualProductoSolicitudSueldoNeto').val()),
                			"debitos_bancarios": parseFloat($('#MutualProductoSolicitudDebitosBancarios').val()),
                			"cuota_credito": parseFloat($('#MutualProductoSolicitudCuotaGrillaImporte').val())
            			}
            		}
            	
            		var beneficio = $('#MutualProductoSolicitudPersonaBeneficioId').val();
                    var url = '<?php echo $this->base?>/pfyj/persona_beneficios/consulta_siisa_ajax/' + beneficio;
                    $('#siisa_aprueba').hide();
                    $('#siisa_rechaza').hide();
                    $('#spinner_siisa').show();
                    $('#btn_submit').prop('disabled', true);
                    $.ajax({
                        url: url,
                        data: payload,
                        type: "POST",
                        dataType: "json"
                    }).done(function (json) {
                    	$('#spinner_siisa').hide();
                    	
                        if(json.aprueba) {
                        	$('#siisa_aprueba').show();
                        	// $('#siisa_response').addClass('text-success');
                        	$('#MutualProductoSolicitudSiisa').val(json.serialize);
                        	$('#siisa_response').html(json.decisionResult);
                        } else if(json.rechaza) {
                        	$('#siisa_rechaza').show();
                        	// $('#siisa_response').addClass('text-danger');
                        	$('#MutualProductoSolicitudSiisa').val(json.serialize);
                        	$('#siisa_response').html(json.decisionResult);
                        } else if(json.onError){
                        	$('#siisa_response').html("ERROR SERVICIO COD: " + json.oERROR.httpCode + " | MSG SIISA: "  + json.oERROR.message);
                        	$('#card-body-siisa').addClass('bg-warning');
                        }
                        $('#btn_submit').prop('disabled', false);						
						
                    });                          	
            	}
            
            </script>
                       
        <div class="card mb-2">
          <div class="card-header bg-info text-white font-weight-bold" id="siisa_cardHeader"><i class="fas fa-business-time"></i>&nbsp;Motor de Decisi&oacute;n SIISA</div>
          <div class="card-body" id="card-body-siisa" >
          <i class="fas fa-thumbs-up text-success" id="siisa_aprueba"></i><i class="fas fa-thumbs-down text-danger" id="siisa_rechaza"></i>&nbsp;<span id="siisa_response" class="ml-2"></span>
          <div id="spinner_siisa" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div>
          </div>
        </div> 
        <?php echo $frm->hidden('MutualProductoSolicitud.siisa',array('value' => ""))?>                      
        <?php endif;?>
        <div class="form-row">
            <div class="form-group col-md-4"></div>
            <div class="form-group col-md-4"></div>
            <div class="form-group col-md-4">
                <?php if(!$solicitud['Persona']['fallecida']):?>
                <button type="submit" id="btn_submit" name="btn_submit" class="form-control btn btn-primary btn-small"><i class="fas fa-arrow-circle-right"></i>&nbsp;Siguiente</button>
                <?php else:?>
                <button type="submit" id="btn_submit" name="btn_submit" disabled="" class="form-control btn btn-primary btn-disable"><i class="fas fa-arrow-circle-right"></i>&nbsp;Siguiente</button>
                <?php endif;?>        
            </div>
        </div>

        <?php echo $frm->hidden('MutualProductoSolicitud.cuota_grilla_id',array('value' => 0))?>
        <?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
        <?php echo $frm->hidden('MutualProductoSolicitud.cuota_grilla_importe',array('value' => 0))?>
        
        <?php echo $form->end();?>        
    </div>
</div>

<?php echo $this->renderElement('solicitudes/nalta_beneficio_modal',array('plugin' => 'ventas','solicitud' => $solicitud))?>
<?php echo $this->renderElement('solicitudes/modifica_tarjeta_debito_modal',array('plugin' => 'ventas','solicitud' => $solicitud))?>





<!-- Modal -->
<?php
#CONTROL DEL MODULO DE NOSIS VALIDACION DE IDENTIDAD
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_NOSIS_CBU = (isset($INI_FILE['general']['nosis_validar_cbu']) && $INI_FILE['general']['nosis_validar_cbu'] == 1 ? TRUE : FALSE);        
#MODULO DE TARJETAS DE DEBITO
$MOD_TARJETAS = (isset($INI_FILE['general']['tarjetas_de_debito']) && $INI_FILE['general']['tarjetas_de_debito'] == 1 ? TRUE : FALSE);

?>

<?php 
echo $html->css('creditCard');
echo $javascript->link('aplicacion/creditCard');
?>

<script>

    $(document).ready(function () {
        <?php if($solicitud['Persona']['fallecida'] == 1):?>
        //$("#formAltaBeneficio *").prop("disabled", true);
        //$("#btnModalClose").prop("disabled", false);
        //$("#btnCancel").prop("disabled", false);
        <?php endif;?>
        $('#spinnerCBU').hide();
//        var organismo = $('#PersonaBeneficioCodigoBeneficio').val();
        disableElementosForm();

        $('#PersonaBeneficioCodigoBeneficio').change(function () {
            document.getElementById('PersonaBeneficioCodigoReparticion').value = "";
		    document.getElementById('PersonaBeneficioTurnoPago').value = "";             
            disableElementosForm();
            getComboEmpresas();
        });
        $('#PersonaBeneficioCodigoEmpresa').change(function () {
            // checkOPRequired();
            document.getElementById('PersonaBeneficioCodigoReparticion').value = "";
		    document.getElementById('PersonaBeneficioTurnoPago').value = "";            
        });
        $('#PersonaBeneficioCbu').blur(function () {
            
            var cbuValue = $('#PersonaBeneficioCbu').val();
            if(cbuValue === ''){return;} 
            $('#spinnerCBU').show();
            var url = "<?php echo $this->base?>/config/bancos/deco_cbu/" + cbuValue;
            $.ajax({
                url: url,
                data: {},
                type: "GET",
                dataType: "json"
            }).done(function (json) {
                $('#PersonaBeneficioNroSucursal').val('');
                $('#PersonaBeneficioNroCtaBco').val('');
                if(json.validado){
                    $('#PersonaBeneficioCbu').removeClass( "is-invalid" );
                    var sucursal = json.sucursal;
                    var nroCta = json.nro_cta_bco;
                    if (sucursal) {$('#PersonaBeneficioNroSucursal').val(sucursal);}
                    if (nroCta) {$('#PersonaBeneficioNroCtaBco').val(nroCta);}
                }else{
                    $('#PersonaBeneficioCbu').addClass( "is-invalid" );
                    $('#PersonaBeneficioCbu').focus();
                }
                $('#spinnerCBU').hide();
            });
            
            <?php if($MOD_NOSIS_CBU):?>
            var ndoc = '<?php echo $solicitud['Persona']['cuit_cuil']?>';
            var cbu = $('#PersonaBeneficioCbu').val();
            var url = '<?php echo $this->base?>/pfyj/persona_beneficios/validar_cbu_nosis' + '/' + ndoc + '/' + cbu;
            
            $.ajax({
                method: "GET",
                url: url,
                dataType: "json",
                data: {}
                })
                .done(function( json ) {
                    $('#PersonaBeneficioCbu').removeClass( "is-invalid is-valid" );
                    if(json.Resultado === null){
                        error = true;
                        validado = true;
                        msg = 'ATENCION:\nNo se pudo efecutar la validación del CBU';
                        msg = msg + '\n\n' + ' *** ERROR NO ESPECIFICADO EN LA COMUNICACION CON EL SERVICIO ***';
                        msg = msg + '\n\n' + 'Guardar de todos modos?';

                    }else if(json.Resultado.Estado >= '400'){
                        validado = true;
                        error = true;
                        msg = 'ATENCION:\nNo se pudo efecutar la validación del CBU';
                        msg = msg + '\n\nNOSIS: *** ' + json.Resultado.Novedad + ' ***';
                        msg = msg + '\n' + 'Transacción: ' + json.Resultado.Transaccion;
                        msg = msg + '\n' + 'Ref: ' + json.Resultado.Referencia;
                        msg = msg + '\n\n' + 'Guardar de todos modos?';                    
                    }else{
                        validado = json.Datos.Cbu.Validado;
                        msg = 'NOSIS :: Servicio de Validación\n';
                        msg = msg + '*** '  + json.Datos.Cbu.Estado + ' ***'; 
                        msg = msg + '\n'
                        msg = msg + 'CBU: ' + json.Pedido.Cbu;
                        msg = msg + '\n';
                        msg = msg + json.Datos.Persona.Documento + ' ' + json.Datos.Persona.RazonSocial + ' (' + json.Datos.Persona.Sexo + ') ';
                         
                        if(!validado){
                            $('#PersonaBeneficioCbu').addClass( "is-invalid" ); 
                            $('#PersonaBeneficioNroSucursal').val('');
                            $('#PersonaBeneficioNroCtaBco').val('');                            
                        }else{
                            $('#PersonaBeneficioCbu').addClass( "is-valid" );
                        }
                        confirm(msg);
                    }
                    $('#spinnerCBU').hide();
            });            
            
            <?php endif;?>    
            
            
        });
        getComboEmpresas();



    });

    function getComboEmpresas() {
        var url = "<?php echo $this->base?>/config/global_datos/combo_empresas_ajax/" + $('#PersonaBeneficioCodigoBeneficio').val();
        $.ajax({
            url: url,
            data: {},
            type: "GET",
            dataType: "html"
        }).done(function (html) {
            $("#PersonaBeneficioCodigoEmpresa").html(html);
            checkOPRequired();
        });
    }

    function checkOPRequired(){}

function disableElementosForm() {
    var organismo = $('#PersonaBeneficioCodigoBeneficio').val();
    var org = organismo.substr(8, 2);

    // Configuración para org = 22 (CBU y otros campos obligatorios, excepto los especificados)
    if (org === '22') {
        $('#PersonaBeneficioCodigoEmpresa').prop("required", true);
        $('#PersonaBeneficioSueldoNeto').prop("required", true);
        $('#PersonaBeneficioCbu').prop("required", true);
        $('#PersonaBeneficioNroSucursal').prop("required", true);
        $('#PersonaBeneficioNroCtaBco').prop("required", true);

        // Quitar la obligatoriedad de estos campos
        $('#PersonaBeneficioCodigoReparticion').prop("required", false);
        $('#PersonaBeneficioTurnoPago').prop("required", false);
        $('#PersonaBeneficioNroLegajo').prop("required", false);
        $('#PersonaBeneficioFechaIngreso').prop("required", false);
        $('#PersonaBeneficioDebitosBancarios').prop("required", false);
        
        $('#PersonaBeneficioCodigoEmpresa').prop("disabled", false);
        $('#PersonaBeneficioCodigoReparticion').prop("disabled", false);
        $('#PersonaBeneficioCodigoEmpresa').prop("disabled", false);
        $('#PersonaBeneficioCodigoReparticion').prop("disabled", false);
        $('#PersonaBeneficioTurnoPago').prop("disabled", false);
        $('#PersonaBeneficioNroLegajo').prop("disabled", false);
        $('#PersonaBeneficioFechaIngreso').prop("disabled", false);        
    } 
    // Configuración para org = 77 (Beneficio y otros campos obligatorios)
    else if (org === '77') {
        $('#PersonaBeneficioTipo').prop("required", true);
        $('#PersonaBeneficioNroLey').prop("required", true);
        $('#PersonaBeneficioNroBeneficio').prop("required", true);
        $('#PersonaBeneficioSubBeneficio').prop("required", true);
        
        $('#PersonaBeneficioCodigoEmpresa').prop("disabled", true);
        $('#PersonaBeneficioCodigoReparticion').prop("disabled", true);
        $('#PersonaBeneficioCodigoEmpresa').prop("disabled", true);
        $('#PersonaBeneficioCodigoReparticion').prop("disabled", true);
        $('#PersonaBeneficioTurnoPago').prop("disabled", true);
        $('#PersonaBeneficioNroLegajo').prop("disabled", true);
        $('#PersonaBeneficioFechaIngreso').prop("disabled", true);
        
        $('#PersonaBeneficioTipo').prop("disabled", false);
        $('#PersonaBeneficioNroLey').prop("disabled", false);
        $('#PersonaBeneficioNroBeneficio').prop("disabled", false);
        $('#PersonaBeneficioSubBeneficio').prop("disabled", false);          
        
    } 
    // Configuración para org = 66 (Solo Beneficio Nro. obligatorio)
    else if (org === '66') {
        $('#PersonaBeneficioNroBeneficio').prop("required", true);
    } 
    // Configuración para org = 'MU' (Todos los campos opcionales)
    else if (org === 'MU') {
        
        $('#PersonaBeneficioCodigoEmpresa').prop("disabled", true);
        $('#PersonaBeneficioCodigoReparticion').prop("disabled", true);
        $('#PersonaBeneficioCodigoEmpresa').prop("disabled", true);
        $('#PersonaBeneficioCodigoReparticion').prop("disabled", true);
        $('#PersonaBeneficioTurnoPago').prop("disabled", true);
        $('#PersonaBeneficioNroLegajo').prop("disabled", true);
        $('#PersonaBeneficioFechaIngreso').prop("disabled", true);
        $('#PersonaBeneficioTipo').prop("disabled", true);
        $('#PersonaBeneficioNroLey').prop("disabled", true);
        $('#PersonaBeneficioNroBeneficio').prop("disabled", true);
        $('#PersonaBeneficioSubBeneficio').prop("disabled", true);        
        
        
        $('#PersonaBeneficioCodigoEmpresa').prop("required", false);
        $('#PersonaBeneficioCodigoReparticion').prop("required", false);
        $('#PersonaBeneficioTurnoPago').prop("required", false);
        $('#PersonaBeneficioNroLegajo').prop("required", false);
        $('#PersonaBeneficioFechaIngreso').prop("required", false);
        $('#PersonaBeneficioSueldoNeto').prop("required", false);
        $('#PersonaBeneficioDebitosBancarios').prop("required", false);
        $('#PersonaBeneficioCbu').prop("required", false);
        $('#PersonaBeneficioNroSucursal').prop("required", false);
        $('#PersonaBeneficioNroCtaBco').prop("required", false);

        $('#PersonaBeneficioTipo').prop("required", false);
        $('#PersonaBeneficioNroLey').prop("required", false);
        $('#PersonaBeneficioNroBeneficio').prop("required", false);
        $('#PersonaBeneficioSubBeneficio').prop("required", false);
    }
}


    
function validateForm(){

	ret = true;
	<?php if($MOD_TARJETAS):?>		
    var mes = document.getElementById('TarjetaDebitoCardExpirationMonth').value;
    var anio = document.getElementById('TarjetaDebitoCardExpirationYear').value;
    var cardName = document.getElementById('TarjetaDebitoCardHolderName').value;
    var cardCSV = document.getElementById('TarjetaDebitoSecurityCode').value;
    var cardNumber = document.getElementById('TarjetaDebitoCardNumber').value;
    
    if( cardName !== '' ) {
    
        if (!isNaN(cardNumber) && cardNumber !== ''){
        	ret = validateNumberJQUERY(document.getElementById('TarjetaDebitoCardNumber'));
        	if(!ret) { return false; }
            ret = controlVigenciaTarjetaDebito(mes,anio,3);
            if(!ret) { return false; }
            if((isNaN(cardCSV) || cardCSV === '')) {
            	alert('Debe indicar el Codigo');
            	return false;
            }
                
        } else { 
        
        	document.getElementById('TarjetaDebitoCardHolderName').value = '';
        	document.getElementById('TarjetaDebitoSecurityCode').value = '';
        }    
    
    }
    

    <?php endif;?>

    return ret;  
}    
    
</script>


<div class="modal fade" id="altaBeneficioModal" tabindex="-1" role="dialog" aria-labelledby="altaBeneficioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <?php echo $form->create(null,array('action' => 'alta_beneficio/'.$TOKEN_ID,'name'=>'formAltaBeneficio','id'=>'formAltaBeneficio','onsubmit' => "return validateForm()"));?>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="altaBeneficioModalLabel">Nuevo Beneficio / Medio de Pago</h5>
                <button type="button" id="btnModalClose" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-row">
                    <div class="form-group col-md-11">
                        <?php $datos = $this->requestAction('/config/global_datos/get_organismos_activos');?>
                        <label for="PersonaBeneficioCodigoBeneficio">Organismo</label>
                        <select class="form-control" id="PersonaBeneficioCodigoBeneficio" name="data[PersonaBeneficio][codigo_beneficio]">
                            <?php if(!empty($datos)):?>
                            <?php foreach($datos as $id => $dato):?>
                            <option value="<?php echo $id?>"><?php echo $dato?></option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>                        
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="PersonaBeneficioTipo">Tipo</label>
                        <select class="form-control" id="PersonaBeneficioTipo" name="data[PersonaBeneficio][tipo]">
                            <option value="" selected=""></option>
                            <option value="1">JUBILADO</option>
                            <option value="0">PENSIONADO</option>
                        </select>                         
                    </div>
                    <div class="form-group col-md-2">
                        <label for="PersonaBeneficioNroLey">Ley</label>
                        <input type="text" id="PersonaBeneficioNroLey" class=" form-control" name="data[PersonaBeneficio][nro_ley]">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="PersonaBeneficioNroBeneficio">Beneficio Nro.</label>
                        <input type="text" id="PersonaBeneficioNroBeneficio" class=" form-control" name="data[PersonaBeneficio][nro_beneficio]">
                    </div>    
                    <div class="form-group col-md-2">
                        <label for="PersonaBeneficioSubBeneficio">Sub-Beneficio</label>
                        <input type="text" id="PersonaBeneficioSubBeneficio" class=" form-control" name="data[PersonaBeneficio][sub_beneficio]">
                    </div>                    
                </div>    
                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label for="PersonaBeneficioCodigoEmpresa">Empresa</label>
                        <select class="form-control" id="PersonaBeneficioCodigoEmpresa" name="data[PersonaBeneficio][codigo_empresa]">
                        </select>  
                    </div>
                    <div class="form-group col-md-2">
                        <label for="PersonaBeneficioCodigoReparticion">Reparticion</label>
                        <input type="text" id="PersonaBeneficioCodigoReparticion" class=" form-control" name="data[PersonaBeneficio][codigo_reparticion]">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="PersonaBeneficioTurnoPago">Nro.OP</label>
                        <input type="text" id="PersonaBeneficioTurnoPago" class=" form-control" name="data[PersonaBeneficio][turno_pago]">
                    </div> 
                    <div class="form-group col-md-2">
                        <label for="PersonaBeneficioNroLegajo">Legajo</label>
                        <input type="text" id="PersonaBeneficioNroLegajo" class=" form-control" name="data[PersonaBeneficio][nro_legajo]">
                    </div>                    
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">            
                        <label for="PersonaBeneficioFechaIngreso">Fecha Ingreso</label>
                        <div class="input-group date fhasta">
                            <input type="text" id="PersonaBeneficioFechaIngreso" name="data[PersonaBeneficio][fecha_ingreso]" class="form-control" value="" placeholder="DD/MM/AAAA">&nbsp;<span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp;</span>
                        </div>
                        <script type="text/javascript">
                            $('.input-group.date.fhasta').datepicker({
                                clearBtn: true,
                                language: "es",
                                autoclose: true,
                                todayHighlight: true,
                                orientation: "auto bottom",
                                format: "dd/mm/yyyy",
                                assumeNearbyYear: 20
                            });
                        </script>                         
                    </div>
                    <div class="form-group col-md-3">
                        <label for="PersonaBeneficioSueldoNeto"><strong>Sueldo Neto *</strong></label>
                        <input class="form-control" id="PersonaBeneficioSueldoNeto" step="0.01" name="data[PersonaBeneficio][sueldo_neto]" value="" type="number" >
                    </div>
                    <div class="form-group col-md-3">
                        <label for="PersonaBeneficioDebitosBancarios">Débitos Bancarios</label>
                        <input class="form-control" id="PersonaBeneficioDebitosBancarios" step="0.01" name="data[PersonaBeneficio][debitos_bancarios]" value="" type="number" >
                    </div>                     
                </div>                
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="PersonaBeneficioCbu">C.B.U.</label>
                        <input class="form-control" id="PersonaBeneficioCbu" name="data[PersonaBeneficio][cbu]" maxlength="22" minlength="22" value="" type="text" >
                    </div>
                    <div class="form-group col-md-2">
                        <label for="PersonaBeneficioNroSucursal">Sucursal</label>
                        <input class="form-control" id="PersonaBeneficioNroSucursal" maxlength="5" name="data[PersonaBeneficio][nro_sucursal]" value="" type="text" >
                    </div>
                    <div class="form-group col-md-3">
                        <label for="PersonaBeneficioNroCtaBco">Número de Cuenta</label>
                        <input class="form-control" id="PersonaBeneficioNroCtaBco" maxlength="11" name="data[PersonaBeneficio][nro_cta_bco]"  value="" type="text" >
                    </div>
                    <div id="spinnerCBU" class="form-group col-md-1">
                        <label for="spinnerCBU"><span style="visibility: hidden;">Verificar</span></label>
                        <i class="fas fa-sync-alt fa-spin"></i>
                    </div>                    
                </div>
                <?php if($MOD_TARJETAS):?>
                <hr>
                <div class="card mb-1">
                    <div class="card-header bg-info text-white"><i class="fas fa-credit-card"></i>&nbsp;Tarjeta de Débito</div>
                    <div class="card-body">

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="TarjetaDebitoCardHolderName">Titular</label>
                                <input class="form-control" id="TarjetaDebitoCardHolderName" name="data[TarjetaDebito][card_holder_name]" maxlength="30" value="" type="text" >
                            </div>
                            <div class="form-group col-md-4">
                                <label for="TarjetaDebitoCardNumber">Número</label>
                                <input class="form-control" id="TarjetaDebitoCardNumber" minlength="8" type="number" maxlength="19" name="data[TarjetaDebito][card_number]" onkeypress='return event.charCode >= 48 && event.charCode <= 57'   value="" type="text"  onblur="validateNumberJQUERY(this)">
                            </div> 
                            <div class="form-group col-md-1">
                            <label for="card_icon">&nbsp;</label>
                                <div id="card_icon"></div>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="TarjetaDebitoCardExpirationMonth">Mes</label>
                                <select class="form-control" id="TarjetaDebitoCardExpirationMonth" name="data[TarjetaDebito][card_expiration_month]">
                                <?php 
                                    for($i=1;$i<=12;$i++){
                                        $value = str_pad($i,2,'0',STR_PAD_LEFT);
                                        echo "<option value=\"$value\" ".( $value == date('m') ? " selected " : "").">$value</option>";
                                    }                                    
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="TarjetaDebitoCardExpirationYear">Año</label>
                                <select class="form-control" id="TarjetaDebitoCardExpirationYear" name="data[TarjetaDebito][card_expiration_year]">
                                    <?php 
                                    for($i=intval(date('y'));$i<= (intval(date('y')) + 40);$i++){
                                        $value = str_pad($i,2,'0',STR_PAD_LEFT);
                                        echo "<option value=\"$value\" ".( $value == date('y') ? " selected " : "").">$value</option>";
                                    }                                    
                                    ?>
                                </select>
                            </div>                            
                            

                            <div class="form-group col-md-1">
                                <label for="TarjetaDebitoSecurityCode">Cod.Seg.</label>
                                <input class="form-control" id="TarjetaDebitoSecurityCode" maxlength="3" name="data[TarjetaDebito][security_code]" placeholder="###"  value="" type="password" onkeypress='return event.charCode >= 48 && event.charCode <= 57' >
                            </div>                                                          
                        </div>

                    </div>
                </div>
                <?php endif;?>    

            <?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
            <?php echo $frm->hidden('PersonaBeneficio.persona_id',array('value' => $solicitud['Persona']['id'])); ?>
            <?php echo $frm->hidden('PersonaBeneficio.id'); ?>
            <?php echo $frm->hidden('PersonaBeneficio.idr_persona',array('value' => $solicitud['Persona']['idr'])); ?>
            <?php echo $frm->hidden('PersonaBeneficio.idr',array('value' => 0)); ?>
            <?php echo $frm->hidden('PersonaBeneficio.activo',array('value' => 1)); ?>
            <?php echo $frm->hidden('PersonaBeneficio.porcentaje',array('value' => 100)); ?>
            <?php echo $frm->hidden('PersonaBeneficio.cbu_nosis_validado',array('value' => 0)); ?>


            </div>
            <div class="modal-footer">
                <button type="button" id="btnCancel" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
        <?php echo $form->end();?>
    </div>
</div>

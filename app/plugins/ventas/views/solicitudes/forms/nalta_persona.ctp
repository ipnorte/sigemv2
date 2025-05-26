
<script type="text/javascript">
    $( document ).ready(function(){
        $('#PersonaApellido').focus();
    });

</script>
<div class="card mb-1">
    <div class="card-header bg-info text-white"><i class="fas fa-user-check"></i>&nbsp;Información Personal del Solicitante</div>
    <div class="card-body">
        <?php echo $form->create(null,array('action' => 'alta_persona/'.$TOKEN_ID,'name'=>'formAltaPersona','id'=>'formAltaPersona',));?>
        <!--<h5>Datos Personales</h5>-->
        <?php if($solicitud['Persona']['fallecida']):?>
            <div class="row mb-1">
                <div class="col-12">
                    <div class="alert alert-dismissible alert-danger">
                        Persona registrada como FALLECIDA el <strong><?php echo $util->armaFecha($solicitud['Persona']['fecha_fallecimiento'])?></strong>
                    </div>
                </div>
            </div>        
        <?php endif;?>
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="PersonaCuitCuil"><strong>CUIT *</strong></label>
                <input class="form-control solo-numero" id="PersonaCuitCuil" name="data[Persona][cuit_cuil]" value="<?php echo $solicitud['Persona']['cuit_cuil']?>" type="text" maxlength="11" minlength="11" readonly="" <?php // echo (!empty($solicitud['Persona']['id']) ? 'disabled=""' : '')?> >                
            </div>
            <div class="form-group col-md-2">
                <label for="PersonaDocumento"><strong>Documento *</strong></label>
                <input class="form-control solo-numero" id="PersonaDocumento" name="data[Persona][documento]" value="<?php echo $solicitud['Persona']['documento']?>" type="text" maxlength="8" minlength="8" readonly="" >                
            </div>
            <div class="form-group col-md-4">
                <label for="PersonaApellido"><strong>Apellido *</strong></label>
                <input class="form-control" id="PersonaApellido" name="data[Persona][apellido]" value="<?php echo $solicitud['Persona']['apellido']?>" type="text" <?php echo (!empty($solicitud['Persona']['id']) ? 'readonly=""' : '')?> >                
            </div> 
            <div class="form-group col-md-4">
                <label for="PersonaNombre"><strong>Nombre/s *</strong></label>
                <input class="form-control" id="PersonaNombre" name="data[Persona][nombre]" value="<?php echo $solicitud['Persona']['nombre']?>" type="text" <?php echo (!empty($solicitud['Persona']['id']) ? 'readonly=""' : '')?> >                
            </div>            
        </div>
        <div class="form-row">
            <div class="form-group col-md-1">
                <label for="PersonaSexo">Sexo</label>
                <select class="form-control" id="PersonaSexo" name="data[Persona][sexo]">
                    <option value="F" <?php echo ($solicitud['Persona']['sexo'] == 'F' ? "selected":"")?>>F</option>
                    <option value="M" <?php echo ($solicitud['Persona']['sexo'] == 'M' ? "selected":"")?>>M</option>
                </select>              
            </div>
            <div class="form-group col-md-2">            
                <label for="fecha_nacimiento"><strong>Fecha Nacimiento *</strong></label>
                <div class="input-group date fhasta">
                    <input type="text" id="fecha_nacimiento" required="" name="data[Persona][fecha_nacimiento]" class="form-control fhasta" value="<?php  if(!empty($solicitud['Persona']['fecha_nacimiento'])) echo date('d/m/Y', strtotime($solicitud['Persona']['fecha_nacimiento'])) ?>" placeholder="DD/MM/AAAA">&nbsp;<span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp;</span>
                </div>
                <script type="text/javascript">
                    $('.input-group.date.fhasta').datepicker({
                        clearBtn: true,
                        language: "es",
                        autoclose: true,
                        todayHighlight: true,
                        orientation: "auto bottom",
                        format: "dd/mm/yyyy",
                        assumeNearbyYear: 20,
                        startDate: '<?php echo date('d/m/Y',strtotime($limiteMayorEdad));?>',
                        endDate: '<?php echo date('d/m/Y',strtotime($limiteMenorEdad));?>'
                    });
                </script>                         
            </div>
            <div class="form-group col-md-2">
                <label for="PersonaEstadoCivil">Estado Civil</label>
                <?php $estados = $this->requestAction('/config/global_datos/get_estados_civil'); ?>
                <select class="form-control" id="PersonaEstadoCivil" name="data[Persona][estado_civil]">
                    <?php foreach($estados as $id => $descripcion):?>
                    <option value="<?php echo $id?>" <?php echo ($solicitud['Persona']['estado_civil'] == $id ? "selected":"")?>><?php echo $descripcion?></option>
                    <?php endforeach;?>
                </select>              
            </div>
            <div class="form-group col-md-7">
                <label for="nombre_conyuge">Conyuge</label>
                <input class="form-control" id="nombre_conyuge" name="data[Persona][nombre_conyuge]" value="<?php echo $solicitud['Persona']['nombre_conyuge']?>" type="text" >                
            </div>             
        </div>
    </div>
</div>
<div class="card mb-1">
    <div class="card-header bg-info text-white"><i class="fas fa-map-marker-alt"></i>&nbsp;Domicilio</div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="persona_calle"><strong>Calle *</strong></label>
                <input class="form-control" id="persona_calle" name="data[Persona][calle]" maxlength="45" name="calle" value="<?php echo $solicitud['Persona']['calle']?>" type="text" required="">
            </div>
            <div class="form-group col-md-1">
                <label for="numero_calle"><strong>Numero *</strong></label>
                <input class="form-control solo-numero" id="numero_calle" name="data[Persona][numero_calle]" maxlength="5" name="numero" value="<?php echo $solicitud['Persona']['numero_calle']?>" type="text" required="">
            </div>
            <div class="form-group col-md-1">
                <label for="persona_piso">Piso</label>
                <input class="form-control" id="persona_piso" name="piso" name="data[Persona][piso]" maxlength="3" value="<?php echo $solicitud['Persona']['piso']?>" type="text" >
            </div>
            <div class="form-group col-md-1">
                <label for="persona_dpto">Dpto</label>
                <input class="form-control" id="persona_dpto" name="dpto" name="data[Persona][dpto]" maxlength="3" value="<?php echo $solicitud['Persona']['dpto']?>" type="text" >
            </div>            
            <div class="form-group col-md-5">
                <label for="persona_barrio">Barrio</label>
                <input class="form-control" id="persona_barrio" maxlength="45" name="data[Persona][barrio]" name="barrio" value="<?php echo $solicitud['Persona']['barrio']?>" type="text" >
            </div>                    
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="entre_calle_1">Entre Calle</label>
                <input class="form-control" id="entre_calle_1" name="data[Persona][entre_calle_1]" maxlength="45" name="calle" value="<?php echo $solicitud['Persona']['entre_calle_1']?>" type="text">
            </div> 
            <div class="form-group col-md-6">
                <label for="entre_calle_2">y Calle</label>
                <input class="form-control" id="entre_calle_2" name="data[Persona][entre_calle_2]" maxlength="45" name="calle" value="<?php echo $solicitud['Persona']['entre_calle_2']?>" type="text">
            </div>             
        </div>
        <script src="<?php echo $this->base ?>/js/jquery.localidadcomplete.js" type="text/javascript"></script>
        <script type="text/javascript">
        
        $( document ).ready(function(){
            $('#PersonaProvinciaId').change(function(){
                $('#PersonaCodigoPostalAproxima').val("");
                $('#PersonaCodigoPostal').val("");
                $('#PersonaLocalidad').val("");
                $('#PersonaCodigoPostalAproxima').focus();
            });
        });
        $( function(){
            $('#PersonaCodigoPostalAproxima').localidadcomplete({
                url:'<?php echo $this->base?>/config/localidades/autocomplete2/1',
                minLength : 2,
                type: "POST",
                idField: true,
                idFieldName: 'localidades_id',
                fieldCp: $('#PersonaCodigoPostal'),
                fieldLocalidadNombre: $('#PersonaLocalidad'),
                formParams: {
                    'query' : $('#PersonaCodigoPostalAproxima'),
                    'provincia_id' : $('#PersonaProvinciaId')
                }
            });   
        });         
        </script>
        
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="PersonaProvinciaId">Provincia</label>
                <?php $estados = $this->requestAction('/config/localidades/cmb_provincias'); ?>
                <select class="form-control" id="PersonaProvinciaId" name="data[Persona][provincia_id]">
                    <?php foreach($estados as $id => $descripcion):?>
                    <option value="<?php echo $id?>" <?php echo ($solicitud['Persona']['provincia_id'] == $id ? "selected":"")?>><?php echo $descripcion?></option>
                    <?php endforeach;?>
                </select>              
            </div>
            <div class="form-group col-md-5">
                <label for="PersonaCodigoPostalAproxima">Localidad</label>
                <input class="form-control" id="PersonaCodigoPostalAproxima" value="" type="text" placeholder="Aproximar por Nombre o CP de la Localidad">                        
                <input type="hidden" id="PersonaLocalidadId" name="data[Persona][localidad_id]" required=""/>
            </div> 
            <div class="form-group col-md-1">
                <label for="PersonaCodigoPostal"><strong>CP *</strong></label>
                <input class="form-control" id="PersonaCodigoPostal" name="data[Persona][codigo_postal]" required="" maxlength="5" minlength="5" value="<?php echo $solicitud['Persona']['codigo_postal']?>" type="text" >
            </div>
            <div class="form-group col-md-4">
                <label for="PersonaLocalidad"><strong>Localidad *</strong></label>
                <input class="form-control" id="PersonaLocalidad" name="data[Persona][localidad]" required=""  value="<?php echo $solicitud['Persona']['localidad']?>" type="text" >
            </div>            
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="maps_latitud">Latitud</label>
                <input class="form-control" id="maps_latitud" name="data[Persona][maps_latitud]" maxlength="3" value="<?php echo $solicitud['Persona']['maps_latitud']?>" type="text" >
            </div>
            <div class="form-group col-md-3">
                <label for="maps_longitud">Longitud</label>
                <input class="form-control" id="maps_longitud" name="data[Persona][maps_longitud]" maxlength="3" value="<?php echo $solicitud['Persona']['maps_longitud']?>" type="text" >
            </div>
            <?php $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);?>            
            <?php if(isset($INI_FILE['general']['google_api_key']) &&  !empty($INI_FILE['general']['google_api_key']) && !empty($solicitud['Persona']['maps_latitud']) && !empty($solicitud['Persona']['maps_longitud'])):?>
            <div class="form-group col-md-1"><label for="linkToMaps"><span style="visibility: hidden;">Ver Mapa</span></label><a id="linkToMaps" href="<?php echo $this->base .'/pfyj/personas/google_maps/' . $solicitud['Persona']['id'] ?>" target="_blank" class="btn btn-secondary"><i class="fas fa-street-view"></i></a></div>            
            <?php endif;?>
        </div>        
    </div>
</div>
<?php
#CONTROL DEL MODULO DE NOSIS VALIDACION DE IDENTIDAD
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_NOSIS_SMS = (isset($INI_FILE['general']['nosis_validar_sms']) && $INI_FILE['general']['nosis_validar_sms'] == 1 ? TRUE : FALSE);        
?>
<div class="card mb-1">
    <div class="card-header bg-info text-white"><i class="fas fa-phone-square"></i>&nbsp;Información de Contacto</div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="PersonaTelefonoMovilC"><strong>Celular Prefijo *</strong></label>
                <input class="form-control solo-numero" id="PersonaTelefonoMovilC" maxlength="5" name="data[Persona][telefono_movil_c]" name="prefijo" value="<?php echo (isset($solicitud['Persona']['telefono_movil_c']) ? $solicitud['Persona']['telefono_movil_c'] : '')?>" type="text" required="" placeholder="#####">
            </div>
            <div class="form-group col-md-4">
                <label for="PersonaTelefonoMovilN"><strong>Celular Numero *</strong></label>
                <input class="form-control solo-numero" id="PersonaTelefonoMovilN" name="data[Persona][telefono_movil_n]" maxlength="10" name="numero" value="<?php echo (isset($solicitud['Persona']['telefono_movil_n']) ? $solicitud['Persona']['telefono_movil_n'] : '')?>" type="text" required="" placeholder="##########">
            </div> 
            <?php if($MOD_NOSIS_SMS):?>
                <script type="text/javascript">
                $( document ).ready(function(){
                    $('#PersonaCelularNosisPIN').val("");
                    $('#PersonaCelularNosisValidado').val(0);
                });  
                function validarCelularOnClick(){
                    
                    var validado = false;
                    var msg = '';
                    var error = false;
                    var consultaId = 0;                    
                    
                    $('#PersonaCelularNosisPIN').removeClass( "bg-warning text-white is-valid is-invalid" );
                    $('#PersonaTelefonoMovilC').removeClass( "is-valid" );
                    $('#PersonaTelefonoMovilN').removeClass( "is-valid" );                    
                    
                    <?php if($solicitud['Persona']['celular_nosis_validado']):?>
                    $('#celular_nosis_validado').hide();
                    <?php endif;?>                    
                    $('#PersonaCelularNosisValidado').val(0);
                    var celular = $('#PersonaTelefonoMovilC').val().trim() + $('#PersonaTelefonoMovilN').val().trim();
                    var ndoc = $('#PersonaCuitCuil').val();
                    var url = '<?php echo $this->base?>/pfyj/personas/validar_sms_nosis/' + ndoc + '/' + celular;
                    $.ajax({
                      method: "GET",
                      url: url,
                      dataType: "json",
                      data: {}
                    }).done(function( json ) {
                        if(json.Resultado === null){
                            error = true;
                            validado = true;
                            msg = 'ATENCION:\nNo se pudo efecutar la validación del Celular';
                            msg = msg + '\n\n' + ' *** ERROR NO ESPECIFICADO EN LA COMUNICACION CON EL SERVICIO ***';
//                            msg = msg + '\n\n' + 'Guardar de todos modos?';

                        }else if(json.Resultado.Estado >= '400'){
                            validado = true;
                            error = true;
                            msg = 'ATENCION:\nNo se pudo efecutar la validación del Celular';
                            msg = msg + '\n\nNOSIS: *** ' + json.Resultado.Novedad + ' ***';
                            msg = msg + '\n' + 'Transacción: ' + json.Resultado.Transaccion;
                            msg = msg + '\n' + 'Ref: ' + json.Resultado.Referencia;
//                            msg = msg + '\n\n' + 'Guardar de todos modos?';                    
                        }else{
                            validado = json.Datos.Sms.TokenEnviado;
                            consultaId = json.Datos.IdConsulta;
                            $('#PersonaCelularNosisConsultaId').val(consultaId);
                            msg = 'NOSIS :: Servicio de Validación\n\n';
                            msg = msg + json.Datos.Sms.Novedad; 
                            if(validado === 0){
                                consultaId = null;
                                msg = msg + ' | Estado: ' + json.Datos.Sms.Estado;
                            }else{
                                msg = msg + ' | PIN ENVIADO: ' + json.Datos.Sms.Prefijo + "#####";
                                $('#PersonaCelularNosisPIN').addClass( "bg-warning text-white" );
                            }
                            
                            $('#PersonaCelularNosisPIN').focus();
                        }
                        alert(msg);
                    }); 
                }
                function evaluarCelularOnClick(){
                    $('#PersonaCelularNosisPIN').removeClass( "bg-warning text-white is-invalid is-valid" );
                    $('#PersonaTelefonoMovilC').removeClass( "is-valid" );
                    $('#PersonaTelefonoMovilN').removeClass( "is-valid" );  
                    
                    var pin = $('#PersonaCelularNosisPIN').val().trim();
                    if(pin === ''){
                        alert('Debe indicar el PIN recibido');
                        $('PersonaCelularNosisPIN').focus();
                        return;
                    }
                    var consultaId = $('#PersonaCelularNosisConsultaId').val();
                    var url = '<?php echo $this->base?>/pfyj/personas/evaluar_sms_nosis/' + consultaId + '/' + pin;  
                    $.ajax({
                      method: "GET",
                      url: url,
                      dataType: "json",
                      data: {}
                    }).done(function( json ) {
                        if(json.Resultado === null){
                            error = true;
                            validado = true;
                            msg = 'ATENCION:\nNo se pudo efecutar la validación del Celular';
                            msg = msg + '\n\n' + ' *** ERROR NO ESPECIFICADO EN LA COMUNICACION CON EL SERVICIO ***';
                            msg = msg + '\n\n' + 'Guardar de todos modos?';
                            $('#PersonaCelularNosisPIN').addClass( "is-invalid" );
                        }else if(json.Resultado.Estado >= '400'){
                            validado = false;
                            error = true;
                            msg = 'ATENCION:\nNo se pudo efecutar la validación del Celular';
                            msg = msg + '\n\nNOSIS: *** ' + json.Resultado.Novedad + ' ***';
                            msg = msg + '\n' + 'Transacción: ' + json.Resultado.Transaccion;
                            msg = msg + '\n' + 'Ref: ' + json.Resultado.Referencia;
                            msg = msg + '\n\n' + 'Guardar de todos modos?';
                            $('#PersonaCelularNosisPIN').addClass( "is-invalid" );
                        }else{
                            validado = json.Datos.Sms.Validado;
                            msg = 'NOSIS :: Servicio de Validación\n\n';
                            msg = msg + 'El PIN ' + pin + ' fué ' + json.Datos.Sms.Estado;
                            if(!validado){
                                $('#PersonaCelularNosisPIN').addClass( "is-invalid" );
                            }else{
                                $('#PersonaCelularNosisPIN').addClass( "is-valid" );
                                $('#PersonaTelefonoMovilC').addClass( "is-valid" );
                                $('#PersonaTelefonoMovilN').addClass( "is-valid" );
                            }    
                            alert(msg);
                        }                        
                    });                    
                }
                </script>
                <div class="form-group col-md-1">
                    <label for="btnSendSMS_PIN"><span style="visibility: hidden;">Verificar</span></label>
                    <button type="button" id="btnSendSMS_PIN" onclick="validarCelularOnClick()" class="btn btn-success"><i class="fas fa-sms"></i></button>
                </div>
                <div class="form-group col-md-2">
                    <label for="PersonaCelularNosisPIN">PIN Recibido</label>
                    <input class="form-control" id="PersonaCelularNosisPIN" name="data[Persona][celular_nosis_consulta_pin]" maxlength="10" value="" type="text" >
                    <input type="hidden" id="PersonaCelularNosisConsultaId" name="data[Persona][celular_nosis_consulta_id]">
                </div>
                <div class="form-group col-md-1">
                    <label for="btnSendSMS_PIN"><span style="visibility: hidden;">Verificar</span></label>
                    <button type="button" id="btnEvaluar_PIN" onclick="evaluarCelularOnClick()" class="btn btn-success"><i class="fas fa-check"></i></button>
                    <input type="hidden" id="PersonaCelularNosisValidado" name="data[Persona][celular_nosis_validado]" value="<?php echo (isset($persona['celular_nosis_validado']) ? $persona['celular_nosis_validado'] : '')?>">
                </div>
            <?php endif;?>
        </div>
        <?php if($solicitud['Persona']['celular_nosis_validado']):?>
            <div class="form-row">
                <div class="form-group col-md-6"><p class="text-success"><strong>VALIDADO EL <?php echo $solicitud['Persona']['celular_nosis_fecha_validacion']?></strong></p></div>
            </div>
        <?php endif;?>
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="prefijo">Linea Prefijo</label>
                <input class="form-control solo-numero" id="prefijo" maxlength="5" name="data[Persona][telefono_fijo_c]" value="<?php echo (isset($solicitud['Persona']['telefono_fijo_c']) ? $solicitud['Persona']['telefono_fijo_c'] : '')?>" type="text" placeholder="#####">
            </div>
            <div class="form-group col-md-4">
                <label for="numero">Linea Numero</label>
                <input class="form-control solo-numero" id="numero" maxlength="10" name="data[Persona][telefono_fijo_n]" value="<?php echo (isset($solicitud['Persona']['telefono_fijo_n']) ? $solicitud['Persona']['telefono_fijo_n'] : '')?>" type="text" placeholder="##########">
            </div>            
        </div>
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="prefijo">Mensajes Prefijo</label>
                <input class="form-control solo-numero" id="prefijo" maxlength="5" name="data[Persona][telefono_referencia_c]" value="<?php echo (isset($solicitud['Persona']['telefono_referencia_c']) ? $solicitud['Persona']['telefono_referencia_c'] : '')?>" type="text" placeholder="#####">
            </div>
            <div class="form-group col-md-4">
                <label for="numero">Mensajes Numero</label>
                <input class="form-control solo-numero" id="numero" maxlength="10" name="data[Persona][telefono_referencia_n]" value="<?php echo (isset($solicitud['Persona']['telefono_referencia_n']) ? $solicitud['Persona']['telefono_referencia_n'] : '')?>" type="text" placeholder="##########">
            </div>
            <div class="form-group col-md-6">
                <label for="persona_referencia">Persona de Referencia</label>
                <input class="form-control" id="persona_referencia" name="data[Persona][persona_referencia]"  value="<?php echo (isset($solicitud['Persona']['persona_referencia']) ? $solicitud['Persona']['persona_referencia'] : '')?>" type="text" >
            </div>             
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="e_mail">Email</label>
                <input class="form-control" id="e_mail" maxlength="50" name="data[Persona][e_mail]" size="50" value="<?php echo (isset($solicitud['Persona']['e_mail']) ? $solicitud['Persona']['e_mail'] : '')?>" placeholder="Ej: m.adrian.torres@gmail.com">
            </div>
            <div class="form-group col-md-4">
                <label for="facebook_profile">Perfil Facebook</label>
                <input class="form-control" id="facebook_profile" name="data[Persona][facebook_profile]" maxlength="50" size="50" value="<?php echo (isset($solicitud['Persona']['facebook_profile']) ? $solicitud['Persona']['facebook_profile'] : '')?>" type="text" placeholder="">
            </div>
            <div class="form-group col-md-4">
                <label for="twitter_profile">Perfil Twitter</label>
                <input class="form-control" id="twitter_profile" name="data[Persona][twitter_profile]" maxlength="50" size="50" value="<?php echo (isset($solicitud['Persona']['twitter_profile']) ? $solicitud['Persona']['twitter_profile'] : '')?>" type="text" placeholder="">
            </div>             
        </div>        
    </div>
</div>
<?php if(!empty($solicitud['Persona']['socio_nro'])):?> 
<div class="card mb-1">
    <div class="card-header bg-info text-white"><i class="fas fa-user-shield"></i>&nbsp;Persona registrada como Socio</div>
    <div class="card-body">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Socio</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Fecha de Alta</th>
                    <th>Ultima Calificación</th>
                    <th>Fecha Calificación</th>
                    <th>Calificaciones</th>
                    <th>Situaciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $solicitud['Persona']['socio_nro']?></td>
                    <td><?php echo $solicitud['Persona']['socio_categoria']?></td>
                    <td><?php echo $solicitud['Persona']['socio_status']?></td>
                    <td><?php echo $util->armaFecha($solicitud['Persona']['socio_fecha_alta'])?></td>
                    <td><?php echo $solicitud['Persona']['socio_ultima_calificacion']?></td>
                    <td><?php echo $util->armaFecha($solicitud['Persona']['socio_fecha_ultima_calificacion'])?></td>
                    <td><?php echo $solicitud['Persona']['socio_resumen_calificacion']?></td>
                    <td><?php echo $solicitud['Persona']['socio_resumen_situaciones']?></td>
                </tr>
            </tbody>
        </table>        
    </div>
</div>
<?php endif;?>
<?php 
$INI_FILE = $_SESSION['MUTUAL_INI'];
$MOD_BCRA = (isset($INI_FILE['general']['modulo_bcra']) && $INI_FILE['general']['modulo_bcra'] != 0 ? TRUE : FALSE);    
?>
<?php if($MOD_BCRA):?>
<div class="card mb-1">
    <div class="card-header bg-info text-white"><i class="fas fa-university"></i>&nbsp;Informe Banco Central</div>
    <div class="card-body">
        <style>
            h3,h1{
                font-size: 120%;
                font-weight: bold;
            }
            .tbl_frm{
                font-size: 80%;
            }
            .tbl_frm th{
                border-bottom: 1px solid gray;
            }
        </style>
        <?php echo $this->renderElement('personas/consulta_bcra',array('cuit'=> $solicitud['Persona']['cuit_cuil'],'plugin' => 'pfyj'))?>
    </div>
</div>
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

<?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
<?php echo $frm->hidden('Persona.idr',array('value' => (isset($solicitud['Persona']['idr']) ? $solicitud['Persona']['idr'] : ''))); ?>
<?php echo $frm->hidden('Persona.fallecida',array('value' => (isset($solicitud['Persona']['fallecida']) ? $solicitud['Persona']['fallecida'] : 0))); ?>
<?php echo $frm->hidden('Persona.id',array('value' => (isset($solicitud['Persona']['id']) ? $solicitud['Persona']['id'] : 0))); ?> 
<?php echo $frm->hidden('Persona.tipo_documento',array('value' => (isset($solicitud['Persona']['id']) ? $solicitud['Persona']['tipo_documento'] : 'PERSTPDC0001'))); ?> 
<?php echo $form->end();?>



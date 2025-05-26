<h4>Datos Complementarios - Información de Contacto</h4>
<hr/>
<?php
#CONTROL DEL MODULO DE NOSIS VALIDACION DE IDENTIDAD
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_NOSIS_SMS = (isset($INI_FILE['general']['nosis_validar_sms']) && $INI_FILE['general']['nosis_validar_sms'] == 1 ? TRUE : FALSE);        
?>
<?php if($MOD_NOSIS_SMS):?>
<script type="text/javascript">
Event.observe(window, 'load', function(){
    <?php // if(empty($persona['celular_nosis_consulta_pin'])):?>
    $('PersonaCelularNosisPINLabel').hide();
    $('PersonaCelularNosisPIN').hide();
    $('btnValidarId').hide();
    <?php // endif;?>
    
});
function validarCelularOnClick(){
    <?php if($persona['celular_nosis_validado']):?>
    $('celular_nosis_validado').hide();
    <?php endif;?>
    document.getElementById('PersonaCelularNosisValidado').value = 0;
    var urlValidador = '<?php echo $this->base?>/pfyj/personas/validar_sms_nosis';
    var celular = $('PersonaTelefonoMovilC').getValue().trim() + $('PersonaTelefonoMovilN').getValue().trim();
    var ndoc = $('PersonaDocumento').getValue();
    var spinnerId = 'spinnerNosis';
    $(spinnerId).show();
    document.getElementById('PersonaCelularNosisPIN').value = "";
    var consultaId = validateSMSNosis(urlValidador,celular,ndoc,spinnerId);
    $(spinnerId).hide();
    if(consultaId !== null){
        $('PersonaCelularNosisPINLabel').show();
        $('PersonaCelularNosisPIN').show();
        document.getElementById('PersonaCelularNosisConsultaId').value = consultaId;
        $('PersonaCelularNosisPIN').focus();
        $('btnValidarId').show();
    }else{
        $('PersonaTelefonoMovilN').focus();
    }    
        

}

function evaluarCelularOnClick(){
    var pin = $('PersonaCelularNosisPIN').getValue();
    if(pin === ''){
        alert('Debe indicar el PIN recibido');
        $('PersonaCelularNosisPIN').focus();
        return;
    } 
    var consultaId = $('PersonaCelularNosisConsultaId').getValue();
    var urlValidador = '<?php echo $this->base?>/pfyj/personas/evaluar_sms_nosis';
    document.getElementById('PersonaCelularNosisValidado').value = 0;
    var res = evaluateSMSNosis(urlValidador,consultaId,pin);
    $('PersonaTelefonoMovilC').addClassName((res ? 'notices_ok' : 'notices_error'));
    $('PersonaTelefonoMovilN').addClassName((res ? 'notices_ok' : 'notices_error'));
    if(res){
        document.getElementById('PersonaCelularNosisValidado').value = 1;
        $('PersonaCelularNosisPIN').disable();
        $('btnValidarId').hide();
    }else{
        alert('ATENCION!\nNo se pudo validar correctamente el número de celular.\nVerifique que el PIN sea correcto o intente enviar uno nuevo');
    }    

}

</script>
<?php endif;?>

<table class="tbl_form">
    <tr>
        <td style="color: #444;">
            <div class="input text"><label for="2" style="font-weight: bold;">Nro.Teléfono Celular *</label>
                (0<input type="text" id="PersonaTelefonoMovilC" name="data[Persona][telefono_movil_c]" maxlength="5" size="5" class="input_number" onkeypress="return soloNumeros(event,false,false)" value="<?php echo (isset($persona['telefono_movil_c']) ? $persona['telefono_movil_c'] : '')?>"/>)
            -15<input type="text" id="PersonaTelefonoMovilN" name="data[Persona][telefono_movil_n]" maxlength="15" size="15" class="input_number" onkeypress="return soloNumeros(event,false,false)" value="<?php echo (isset($persona['telefono_movil_n']) ? $persona['telefono_movil_n'] : '')?>"/>
            </div>
            </td>
<?php if($MOD_NOSIS_SMS):?>
            <td>
                <div class="input text">
                    <?php echo $controles->btnCallJS('validarCelularOnClick()','Enviar SMS','controles/pin.png')?>
                    <label for="PersonaCelularNosisPIN" id="PersonaCelularNosisPINLabel" style="font-weight: bold;">Código de Verificación *</label>
                    <input type="text" id="PersonaCelularNosisPIN" name="data[Persona][celular_nosis_consulta_pin]"  size="10" maxlength="15" value="<?php // echo (isset($persona['celular_nosis_consulta_pin']) ? $persona['celular_nosis_consulta_pin'] : '')?>">
                    <input type="hidden" id="PersonaCelularNosisConsultaId" name="data[Persona][celular_nosis_consulta_id]" value="<?php // echo (isset($persona['celular_nosis_consulta_id']) ? $persona['celular_nosis_consulta_id'] : '')?>">
                    <?php echo $controles->btnCallJS('evaluarCelularOnClick()','Validar','controles/lock.png','btnValidarId')?>
                </div>
                <div id="spinnerNosis" style="display: none; float: left;color:red;font-size:xx-small;">
                <?php echo $html->image('controles/ajax-loader.gif'); ?>
                </div>                 
                
            </td>
            <td>
                <?php if($persona['celular_nosis_validado']):?>
                <input type="text" id="celular_nosis_validado" readonly="" style="color: green;font-weight: bold;" value="VALIDADO EL <?php echo $persona['celular_nosis_fecha_validacion']?>" size="30"/>
                    <!--<span style="color: green;">VALIDADO EL <strong><?php // echo $persona['celular_nosis_fecha_validacion']?></strong></span>-->
                <?php endif;?>
                <input type="hidden" id="PersonaCelularNosisValidado" name="data[Persona][celular_nosis_validado]" value="<?php echo (isset($persona['celular_nosis_validado']) ? $persona['celular_nosis_validado'] : '')?>">
            </td>
<?php else:?>            
            <td>
            <?php echo $form->input('Persona.telefono_movil_empresa',array('type'=>'select','options'=>array('1' =>'CLARO ARGENTINA', '2'=>'TELECOM PERSONAL','3' => 'MOVISTAR ARGENTINA', '4' => 'NEXTEL ARGENTINA'),'empty'=>false,'label'=>'Empresa'));?>
            </td>

<?php endif;?>
        
    </tr>
</table>    

<table class="tbl_form">    
    <tr>
        <td style="color: #444;">
            <div class="input text"><label for="1">Nro.Teléfono Fijo</label>
                (<input type="text" name="data[Persona][telefono_fijo_c]" maxlength="5" size="5" class="input_number" onkeypress="return soloNumeros(event,false,false)" value="<?php echo (isset($persona['telefono_fijo_c']) ? $persona['telefono_fijo_c'] : '')?>"/>)-
                <input type="text" name="data[Persona][telefono_fijo_n]" maxlength="15" size="15" class="input_number" onkeypress="return soloNumeros(event,false,false)" value="<?php echo (isset($persona['telefono_fijo_n']) ? $persona['telefono_fijo_n'] : '')?>"/>
            </div>
        </td>        
    </tr>
</table>    
<table class="tbl_form">    
    <tr>
        <td style="color: #444;">
            <div class="input text"><label for="1">Teléfono para Mensajes</label>
            (<input type="text" name="data[Persona][telefono_referencia_c]" maxlength="5" size="5" class="input_number" onkeypress="return soloNumeros(event,false,false)" value="<?php echo (isset($persona['telefono_referencia_c']) ? $persona['telefono_referencia_c'] : '')?>"/>)
            -<input type="text" name="data[Persona][telefono_referencia_n]" maxlength="15" size="15" class="input_number" onkeypress="return soloNumeros(event,false,false)" value="<?php echo (isset($persona['telefono_referencia_n']) ? $persona['telefono_referencia_n'] : '')?>"/>
            </div>
        </td>
        <td><?php echo $frm->input('Persona.persona_referencia',array('label'=>'Persona de Referencia','size'=>40,'maxlength'=>100,'value' => (isset($persona['persona_referencia']) ? $persona['persona_referencia'] : ''))); ?></td>
    </tr>
</table>    
<table class="tbl_form">     
    <tr>
        <td><?php echo $frm->input('Persona.e_mail',array('label'=>'E-MAIL','size'=>40,'maxlength'=>100,'value' => (isset($persona['e_mail']) ? $persona['e_mail'] : ''))); ?></td>
        
    </tr>
</table>
<table class="tbl_form">     
    <tr>
        <td><?php echo $frm->input('Persona.facebook_profile',array('label'=>'Perfil Facebook | https://www.facebook.com/','size'=>40,'maxlength'=>100,'value' => (isset($persona['facebook_profile']) ? $persona['facebook_profile'] : ''))); ?></td>
        <td><?php echo $frm->input('Persona.twitter_profile',array('label'=>'Perfil Twitter | https://twitter.com/','size'=>40,'maxlength'=>100,'value' => (isset($persona['twitter_profile']) ? $persona['twitter_profile'] : ''))); ?></td>
    </tr>  
</table>
<?php echo $frm->hidden('Persona.telefono_fijo',array('value' => (isset($persona['telefono_fijo_c']) ? $persona['telefono_fijo_c'] : '').(isset($persona['telefono_fijo_n']) ? "-".$persona['telefono_fijo_n'] : ''))); ?>	
<?php echo $frm->hidden('Persona.telefono_movil',array('value' => (isset($persona['telefono_movil_c']) ? $persona['telefono_movil_c'] : '').(isset($persona['telefono_movil_c']) ? "-".$persona['telefono_movil_c'] : ''))); ?>	
<?php echo $frm->hidden('Persona.telefono_referencia',array('value' => (isset($persona['telefono_referencia_c']) ? $persona['telefono_referencia_c'] : '').(isset($persona['telefono_referencia_c']) ? "-".$persona['telefono_referencia_c'] : ''))); ?>	

<?php
#CONTROL DEL MODULO DE NOSIS VALIDACION DE IDENTIDAD
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_NOSIS_CBU = (isset($INI_FILE['general']['nosis_validar_cbu']) && $INI_FILE['general']['nosis_validar_cbu'] == 1 ? TRUE : FALSE);        
#MODULO DE TARJETAS DE DEBITO
$MOD_TARJETAS = (isset($INI_FILE['general']['tarjetas_de_debito']) && $INI_FILE['general']['tarjetas_de_debito'] == 1 ? TRUE : FALSE);

?>

<?php echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>NUEVA SOLICITUD</h3>
<hr/>
<?php echo $this->renderElement('solicitudes/alta_info_datos',array('plugin' => 'ventas','solicitud' => $solicitud))?>
<h3>ALTA NUEVO MEDIO DE PAGO</h3>
<hr/>
<script type="text/javascript">

Event.observe(window, 'load', function() {
	<?php if($solicitud['Persona']['fallecida'] == 1):?>
		$('formAltaBeneficio').disable();
		return;
	<?php endif;?>

	var organismo = $('PersonaBeneficioCodigoBeneficio').getValue();
	disableElementosForm(organismo);
	

	$('PersonaBeneficioCodigoBeneficio').observe('change',function(){
		organismo = $('PersonaBeneficioCodigoBeneficio').getValue();
		disableElementosForm(organismo);
		getComboEmpresas();		
	});

	var empresa = $('PersonaBeneficioCodigoEmpresa').getValue();

	// if(empresa==='MUTUEMPRP001'){
	// 	$('PersonaBeneficioTurnoPago').enable();
	// 	$('PersonaBeneficioCodigoReparticion').enable();
	// }

	$('PersonaBeneficioCodigoEmpresa').observe('change',function(){

		document.getElementById('PersonaBeneficioCodigoReparticion').value = "";
		document.getElementById('PersonaBeneficioTurnoPago').value = "";
				
		// empresa = $('PersonaBeneficioCodigoEmpresa').getValue();
		// if(empresa==='MUTUEMPRP001'){
		// 	$('PersonaBeneficioTurnoPago').enable();
		// 	$('PersonaBeneficioCodigoReparticion').enable();
		// }else{
		// 	$('PersonaBeneficioTurnoPago').disable();
		// 	$('PersonaBeneficioCodigoReparticion').disable();			
		// }	
	});	
        
        $('PersonaBeneficioCbu').observe('blur', function(){
            $('spinnerNosis').show();
            var url = '<?php echo $this->base?>/config/bancos/deco_cbu/' + $('PersonaBeneficioCbu').getValue();
            new Ajax.Request(url, {
                    asynchronous:false,
                    evalScripts:true,
                    onInteractive: function(request){
                    },  
                    onSuccess: function(response) {
                        var json = response.responseText.evalJSON();
                        var sucursal = json.sucursal;
                        var nroCta = json.nro_cta_bco;
                        if(sucursal){
                            document.getElementById('PersonaBeneficioNroSucursal').value = sucursal;
                        }
                        if(nroCta){
                            document.getElementById('PersonaBeneficioNroCtaBco').value = nroCta;
                        }
                        $('spinnerNosis').hide();
                  }
            });                         
        });        

	getComboEmpresas();		
		
});

function disableElementosForm(organismo){
    
	org = new Number(organismo.substr(8,2)).toFixed(0);
//        alert(org);
        
	if(org === '22'){
		$('PersonaBeneficioTipo').disable();
		$('PersonaBeneficioNroLey').disable();
		$('PersonaBeneficioNroBeneficio').disable();
		$('PersonaBeneficioSubBeneficio').disable();
		
		$('PersonaBeneficioCodigoEmpresa').enable();
		$('PersonaBeneficioCodigoReparticion').enable();
		$('PersonaBeneficioNroLegajo').enable();
		$('PersonaBeneficioFechaIngresoYear').enable();
		$('PersonaBeneficioFechaIngresoMonth').enable();
		$('PersonaBeneficioFechaIngresoDay').enable();
		$('PersonaBeneficioCbu').enable();	

		$('PersonaBeneficioNroSucursal').enable();
		$('PersonaBeneficioNroCtaBco').enable();

		$('PersonaBeneficioTurnoPago').disable();		

		$('PersonaBeneficioCodigoEmpresa').focus();
		
		document.getElementById('PersonaBeneficioTipo').value = "";
		document.getElementById('PersonaBeneficioNroLey').value = "";
		document.getElementById('PersonaBeneficioNroBeneficio').value = "";
		document.getElementById('PersonaBeneficioSubBeneficio').value = "";		
			
	}else if(org === '77'){
		$('PersonaBeneficioTipo').enable();
		$('PersonaBeneficioNroLey').enable();
		$('PersonaBeneficioNroBeneficio').enable();
		$('PersonaBeneficioSubBeneficio').enable();		
		$('PersonaBeneficioNroLey').focus();
		
		$('PersonaBeneficioCodigoEmpresa').disable();
		$('PersonaBeneficioCodigoReparticion').disable();
		$('PersonaBeneficioNroLegajo').disable();
		$('PersonaBeneficioFechaIngresoYear').disable();
		$('PersonaBeneficioFechaIngresoMonth').disable();
		$('PersonaBeneficioFechaIngresoDay').disable();
        
		$('PersonaBeneficioCbu').enable();
		$('PersonaBeneficioNroSucursal').enable();
		$('PersonaBeneficioNroCtaBco').enable();
        
		$('PersonaBeneficioTurnoPago').disable();		

//		document.getElementById('PersonaBeneficioNroBeneficio').value = "";
		
		document.getElementById('PersonaBeneficioCodigoEmpresa').value = "";
		document.getElementById('PersonaBeneficioCodigoReparticion').value = "";
		document.getElementById('PersonaBeneficioCbu').value = "";
		document.getElementById('PersonaBeneficioNroLegajo').value = "";		
		
	}else if(org === '66'){

		$('PersonaBeneficioNroBeneficio').enable();
		$('PersonaBeneficioNroBeneficio').focus();
		
		$('PersonaBeneficioTipo').enable();
		document.getElementById('PersonaBeneficioTipo').value = 1;
		
		$('PersonaBeneficioNroLey').disable();
		$('PersonaBeneficioSubBeneficio').disable();
		$('PersonaBeneficioCodigoEmpresa').disable();
		$('PersonaBeneficioCodigoReparticion').disable();
		$('PersonaBeneficioNroLegajo').disable();
		$('PersonaBeneficioFechaIngresoYear').disable();
		$('PersonaBeneficioFechaIngresoMonth').disable();
		$('PersonaBeneficioFechaIngresoDay').disable();
		$('PersonaBeneficioCbu').disable();
		$('PersonaBeneficioNroSucursal').disable();
		$('PersonaBeneficioNroCtaBco').disable();
		$('PersonaBeneficioTurnoPago').disable();		

		document.getElementById('PersonaBeneficioNroLey').value = "";
//		document.getElementById('PersonaBeneficioNroBeneficio').value = "";
		document.getElementById('PersonaBeneficioSubBeneficio').value = "";
		
		document.getElementById('PersonaBeneficioCodigoEmpresa').value = "";
		document.getElementById('PersonaBeneficioCodigoReparticion').value = "";
		document.getElementById('PersonaBeneficioCbu').value = "";
		document.getElementById('PersonaBeneficioNroLegajo').value = "";
		
	}else{
		$('PersonaBeneficioTipo').disable();
		$('PersonaBeneficioNroLey').disable();
		$('PersonaBeneficioNroBeneficio').disable();
		$('PersonaBeneficioSubBeneficio').disable();
		
		$('PersonaBeneficioCodigoEmpresa').disable();
		$('PersonaBeneficioCodigoReparticion').disable();
		$('PersonaBeneficioNroLegajo').disable();
		$('PersonaBeneficioFechaIngresoYear').disable();
		$('PersonaBeneficioFechaIngresoMonth').disable();
		$('PersonaBeneficioFechaIngresoDay').disable();
		$('PersonaBeneficioCbu').disable();	

		$('PersonaBeneficioNroSucursal').disable();
		$('PersonaBeneficioNroCtaBco').disable();

		$('PersonaBeneficioTurnoPago').disable();		

		document.getElementById('PersonaBeneficioTipo').value = "";
		document.getElementById('PersonaBeneficioNroLey').value = "";
		document.getElementById('PersonaBeneficioNroBeneficio').value = "";
		document.getElementById('PersonaBeneficioSubBeneficio').value = "";		

    }    
}

function getComboEmpresas(){

	organismo = $('PersonaBeneficioCodigoBeneficio').getValue();
	new Ajax.Updater('PersonaBeneficioCodigoEmpresa','<?php echo $this->base?>/config/global_datos/combo_empresas_ajax/'+ organismo, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'PersonaBeneficioCodigoEmpresa']});
	
}


function validateForm(){
	var ret = true;
    
    var organismo = $('PersonaBeneficioCodigoBeneficio').getValue();
    
    var FORCE_CBU = true;
    var organismosCBU = [<?php echo $organismosCBU?>];
    if(organismosCBU.indexOf(organismo) === -1) FORCE_CBU = false;

    
    if(isNaN(organismo.substr(8,2))){
        return ret;
    } 
	var empresa = $('PersonaBeneficioCodigoEmpresa').getValue();
	var cbu = $('PersonaBeneficioCbu').getValue();
    $('PersonaBeneficioCbu').removeClassName('form-error');
    if(cbu.length !== 22 && FORCE_CBU){       
        $('PersonaBeneficioCbu').focus();
        $('PersonaBeneficioCbu').addClassName('form-error');
        alert("El CBU esta incompleto");
        return false;
    }    
	var cbu_bco = cbu.substring(0,3); 
	if(cbu !== '' && empresa !== 'MUTUEMPRE018' && FORCE_CBU){
		var bancos = [<?php echo (isset($bcos_hab) ? $bcos_hab : '')?>];
		var ret = validarCBU('PersonaBeneficioCbu','CBU incorrecto',0,'mensaje_error_js',0);

//		var ctrlBanco = false;
                var ctrlBanco = true;
		for (var index = 0; index < bancos.length; ++index) {
			var item = bancos[index];
			if(item === cbu_bco){
				ctrlBanco = true;
				break;
			}			  
		}
		if(ret && ctrlBanco) ret = true;
		else ret = false;

		if(!ret) alert("El CBU corresponde a un Banco NO HABILITADO para carga de Beneficio!");
        
        $('PersonaBeneficioNroSucursal').removeClassName('form-error');
        if(cbu !== '' && $('PersonaBeneficioNroSucursal').getValue() === ''){
            alert("Debe indicar el Nro de Sucursal");
            $('PersonaBeneficioNroSucursal').focus();
            $('PersonaBeneficioNroSucursal').addClassName('form-error');
            return false;
        } 
        $('PersonaBeneficioNroCtaBco').removeClassName('form-error');
        if(cbu !== '' && $('PersonaBeneficioNroCtaBco').getValue() === ''){
            alert("Debe indicar el Nro de Cuenta");
            $('PersonaBeneficioNroCtaBco').focus();
            $('PersonaBeneficioNroCtaBco').addClassName('form-error');
            return false;
        } 

	}
        <?php if($MOD_NOSIS_CBU):?>
            var ndoc = '<?php echo $solicitud['Persona']['cuit_cuil']?>';
            var urlValidador = '<?php echo $this->base?>/pfyj/persona_beneficios/validar_cbu_nosis';
            var btnId = 'btn_submit';
            var spinnerId = 'spinnerNosis';
            var divId = 'validadorCbuNosis';
            var cbuInputId = 'PersonaBeneficioCbu';
            var hiddenResultadoId = 'PersonaBeneficioCbuNosisValidado';
            $(spinnerId).show();
            ret = validateCbuNosis(urlValidador,cbu,ndoc,btnId,spinnerId,divId,cbuInputId,hiddenResultadoId);
            $(spinnerId).hide();
            $(cbuInputId).addClassName((ret ? 'notices_ok' : 'notices_error'));
        <?php endif;?> 
            
	return ret;	
}


</script>
<?php echo $form->create(null,array('action' => 'alta_beneficio/'.$TOKEN_ID,'name'=>'formAltaBeneficio','id'=>'formAltaBeneficio','onsubmit' => "return validateForm();"));?>
<div class="areaDatoForm">
	<table class="tbl_form">
            <tr>
                <td>
                        <?php echo $this->renderElement('global_datos/combo_global',array(
                            'plugin'=>'config',
                            'metodo' => "get_organismos_activos",
                            'model' => 'PersonaBeneficio.codigo_beneficio',
                            'empty' => false,
                            'label' => 'ORGANISMO',
                            //'selected' => (isset($this->data['PersonaBeneficio']['codigo_beneficio']) ? $this->data['PersonaBeneficio']['codigo_beneficio'] : "")
                        ))?>				

                </td>
                <td>
                        <?php echo $frm->input('PersonaBeneficio.tipo',array('type' => 'select','options' => array(1 => 'JUBILADO', 0 => 'PENSIONADO')))?>
                </td>
                <td><?php echo $frm->input('PersonaBeneficio.nro_ley',array('label'=>'LEY','size'=>2,'maxlength'=>2)); ?></td>
                <td><?php echo $frm->input('PersonaBeneficio.nro_beneficio',array('label'=>'BENEFICIO','size'=>20,'maxlength'=>50)); ?></td>
                <td><?php echo $frm->input('PersonaBeneficio.sub_beneficio',array('label'=>'SUB-BENEFICIO','size'=>2,'maxlength'=>2)); ?></td>
            </tr>
	</table>
	<table class="tbl_form">
            <tr>
                <td style="font-size: 100%;padding-right:5px;width: auto;height: 15px;color:#666666;">EMPRESA</td>
                <td>
                        <select name="data[PersonaBeneficio][codigo_empresa]" id="PersonaBeneficioCodigoEmpresa">
                        </select>
                        <div id="spinner" style="display: none; float: left;color:red;font-size:xx-small;">
                        <?php echo $html->image('controles/ajax-loader.gif'); ?>
                        </div>
                </td>
                <td><?php echo $frm->input('PersonaBeneficio.codigo_reparticion',array('label'=>'COD.REPARTICION','size'=>11,'maxlength'=>11)); ?></td>
                <td><?php echo $frm->input('PersonaBeneficio.turno_pago',array('label'=>'Nro.OP','size'=>7,'maxlength'=>6)); ?></td>
            </tr>
	</table>
	<table class="tbl_form">
		<tr>
			<td><?php echo $frm->input('PersonaBeneficio.nro_legajo',array('label'=>'LEGAJO','size'=>11,'maxlength'=>11)); ?></td>
			<td>
                            <?php // echo $frm->input('PersonaBeneficio.fecha_ingreso',array('dateFormat' => 'DMY','label'=>'FECHA DE INGRESO','minYear'=>'1900', 'maxYear' => date("Y") - 1))?>
                            <?php echo $frm->calendar('PersonaBeneficio.fecha_ingreso','FECHA DE INGRESO',NULL,1900,date("Y") - 1);?>
                        <td><?php echo $frm->money('PersonaBeneficio.sueldo_neto','SUELDO NETO',(isset($this->data['PersonaBeneficio']['sueldo_neto']) ? $this->data['PersonaBeneficio']['sueldo_neto'] : '0.00')); ?></td>
                        <td><?php echo $frm->money('PersonaBeneficio.debitos_bancarios','DEBITOS BANCARIOS',(isset($this->data['PersonaBeneficio']['debitos_bancarios']) ? $this->data['PersonaBeneficio']['debitos_bancarios'] : '0.00')); ?></td>
                            
                        </td>
		</tr>
	</table>
	<table class="tbl_form">
		<tr>
			<td>
                            <?php echo $frm->number('PersonaBeneficio.cbu',array('label'=>'CBU','size'=>24,'maxlength'=>22)); ?>
                            <?php // if($MOD_NOSIS_CBU):?>
                            <div id="spinnerNosis" style="display: none; float: left;color:red;font-size:xx-small;">
                            <?php echo $html->image('controles/ajax-loader.gif'); ?>
                            </div>        
                            <?php // endif;?>                            
                        </td>
			<td><?php echo $frm->number('PersonaBeneficio.nro_sucursal',array('label'=>'SUCURSAL','size'=>10,'maxlength'=>10)); ?></td>
			<td><?php echo $frm->number('PersonaBeneficio.nro_cta_bco',array('label'=>'CUENTA','size'=>20,'maxlength'=>20)); ?></td>
			
		</tr>
	</table>
	<div style="clear: both;"></div>
</div>

<?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
<?php echo $frm->hidden('PersonaBeneficio.persona_id',array('value' => $solicitud['Persona']['id'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.id'); ?>
<?php echo $frm->hidden('PersonaBeneficio.idr_persona',array('value' => $solicitud['Persona']['idr'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.idr',array('value' => 0)); ?>
<?php echo $frm->hidden('PersonaBeneficio.activo',array('value' => 1)); ?>
<?php echo $frm->hidden('PersonaBeneficio.porcentaje',array('value' => 100)); ?>
<?php echo $frm->hidden('PersonaBeneficio.cbu_nosis_validado',array('value' => 0)); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => '/ventas/solicitudes/alta_plan/'.$TOKEN_ID ))?>

    
    <?php echo $form->end();?>

<?php // debug($solicitud['Persona']['documento'])?>
<?php echo $this->renderElement('personas/padron_header',array('persona' => $persona))?>
<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>
        
<?php
#CONTROL DEL MODULO DE NOSIS VALIDACION DE IDENTIDAD
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_NOSIS_CBU = (isset($INI_FILE['general']['nosis_validar_cbu']) && $INI_FILE['general']['nosis_validar_cbu'] == 1 ? TRUE : FALSE);        
#MODULO DE TARJETAS DE DEBITO
$MOD_TARJETAS = (isset($INI_FILE['general']['tarjetas_de_debito']) && $INI_FILE['general']['tarjetas_de_debito'] == 1 ? TRUE : FALSE);

?>        
        
<script type="text/javascript">
Event.observe(window, 'load', function() {
	<?php if($persona['Persona']['fallecida'] == 1):?>
		$('formEditBeneficio').disable();
		return;
	<?php endif;?>	

	organismo = $('PersonaBeneficioCodigoBeneficio').getValue();
	disableElementosForm(organismo);

	// $('PersonaBeneficioTurnoPago').disable();
	// $('PersonaBeneficioCodigoReparticion').disable();

	var empresa = $('PersonaBeneficioCodigoEmpresa').getValue();
	if(empresa === null){empresa = '<?php echo (isset($this->data['PersonaBeneficio']['codigo_empresa']) ? $this->data['PersonaBeneficio']['codigo_empresa'] : "")?>';}
	
	$('PersonaBeneficioCodigoEmpresa').observe('change',function(){
		document.getElementById('PersonaBeneficioCodigoReparticion').value = "";
		document.getElementById('PersonaBeneficioTurnoPago').value = "";
	});

	// if(empresa==='MUTUEMPRP001'){
	// 	$('PersonaBeneficioTurnoPago').enable();
	// 	$('PersonaBeneficioCodigoReparticion').enable();
	// }else{
	// 	document.getElementById('PersonaBeneficioTurnoPago').value = "";
	// }	

	// $('PersonaBeneficioCodigoEmpresa').observe('change',function(){
	// 	empresa = $('PersonaBeneficioCodigoEmpresa').getValue();

	// 	if(empresa==='MUTUEMPRP001'){
	// 		$('PersonaBeneficioTurnoPago').enable();
	// 		$('PersonaBeneficioCodigoReparticion').enable();
	// 	}else{
	// 		$('PersonaBeneficioTurnoPago').disable();
	// 		$('PersonaBeneficioCodigoReparticion').disable();
	// 		document.getElementById('PersonaBeneficioTurnoPago').value = "";			
	// 	}	
	// });
        
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
	org = organismo.substr(8,2);
	
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
		$('PersonaBeneficioTurnoPago').enable();		
		return;			
	}else if(org === '77'){

		$('PersonaBeneficioTipo').enable();
		$('PersonaBeneficioNroLey').enable();
		$('PersonaBeneficioNroBeneficio').enable();
		$('PersonaBeneficioSubBeneficio').enable();		
		
		$('PersonaBeneficioCodigoEmpresa').disable();
		$('PersonaBeneficioCodigoReparticion').disable();
		$('PersonaBeneficioNroLegajo').disable();
		$('PersonaBeneficioFechaIngresoYear').disable();
		$('PersonaBeneficioFechaIngresoMonth').disable();
		$('PersonaBeneficioFechaIngresoDay').disable();
		
//		$('PersonaBeneficioCbu').disable();
//		$('PersonaBeneficioNroSucursal').disable();
//		$('PersonaBeneficioNroCtaBco').disable();

		$('PersonaBeneficioCbu').enable();	
		$('PersonaBeneficioNroSucursal').enable();
		$('PersonaBeneficioNroCtaBco').enable();		
		
		$('PersonaBeneficioTurnoPago').disable();	
		return;
	}else if(org === '66'){

		$('PersonaBeneficioNroBeneficio').enable();

		$('PersonaBeneficioTipo').enable();
//		document.getElementById('PersonaBeneficioTipo').value = 1;		
		
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
		return;
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
	selected = '<?php echo (isset($this->data['PersonaBeneficio']['codigo_empresa']) ? $this->data['PersonaBeneficio']['codigo_empresa'] : "")?>';
	new Ajax.Updater('PersonaBeneficioCodigoEmpresa','<?php echo $this->base?>/config/global_datos/combo_empresas_ajax/'+ organismo + '/' + selected, {asynchronous:true, evalScripts:true, onComplete:function(request, json) {$('spinner').hide();$('btn_submit').enable();},onLoading:function(request) {Element.show('spinner');$('btn_submit').disable();}, requestHeaders:['X-Update', 'PersonaBeneficioCodigoEmpresa']});	
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
		var bancos = [<?php echo $bcos_hab?>];
		var ret = validarCBU('PersonaBeneficioCbu','CBU incorrecto',0,'mensaje_error_js',0);
                if(!ret){
                    alert("El CBU no es correcto!");
                    return false;
                }    
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
        var cbuValidado = $('PersonaBeneficioCbuNosisValidado').getValue();
        if(cbuValidado === '0'){
            var ndoc = '<?php echo $persona['Persona']['cuit_cuil']?>';
            var urlValidador = '<?php echo $this->base?>/pfyj/persona_beneficios/validar_cbu_nosis';
            var btnId = 'btn_submit';
            var spinnerId = 'spinnerNosis';
            var divId = 'validadorCbuNosis';
            var cbuInputId = 'PersonaBeneficioCbu';
            var hiddenResultadoId = 'PersonaBeneficioCbuNosisValidado';
            $(spinnerId).show();
            ret = validateCbuNosis(urlValidador,cbu,ndoc,btnId,spinnerId,divId,cbuInputId,hiddenResultadoId);
            document.getElementById(hiddenResultadoId).value = (ret ? 1 : 0);
            $(spinnerId).hide();
            $(cbuInputId).addClassName((ret ? 'notices_ok' : 'notices_error'));
 
        }
        <?php endif;?>
        
        
//        var cbua = cbu_split(cbu);
//        
//        document.getElementById('PersonaBeneficioNroSucursal').value = (cbua ===! '011' ? cbua[1] : $('PersonaBeneficioNroSucursal').getValue());
//        document.getElementById('PersonaBeneficioNroCtaBco').value = cbua[3];
        
        
	<?php if($MOD_TARJETAS):?>
		
		//ret = validarTarjeta(3);
        
    <?php endif;?>    
        
	return ret;	
}




</script>


<?php echo $form->create(null,array('name'=>'formAddPersona','id'=>'formEditBeneficio','onsubmit' => "return validateForm()",'action' => 'edit/'. $this->data['PersonaBeneficio']['id']));?>
<h3>MODIFICAR DATOS BENEFICIO</h3>
<div class="areaDatoForm">

	<table class="tbl_form">
		<tr>
			<td><h3><?php echo $util->globalDato($this->data['PersonaBeneficio']['codigo_beneficio'])?></h3><?php //   echo $this->requestAction('/config/global_datos/combo/ORGANISMO/PersonaBeneficio.codigo_beneficio/MUTUCORG/0/0/'.$this->data['PersonaBeneficio']['codigo_beneficio'])?></td>
			<td>
				<?php echo $frm->input('PersonaBeneficio.tipo',array('type' => 'select','options' => array(1 => 'JUBILADO', 0 => 'PENSIONADO'),'selected' => $this->data['PersonaBeneficio']['tipo']))?>
				<?php //   echo $frm->input('PersonaBeneficio.tipo',array('label'=>'TIPO','size'=>1,'maxlength'=>1)); ?>
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
			<td><?php echo $frm->input('PersonaBeneficio.turno_pago',array('label'=>'Nro.OP','size'=>7,'maxlength'=>6, 'value' => ($this->data['PersonaBeneficio']['codigo_empresa'] == $this->data['PersonaBeneficio']['turno_pago'] ? '' : $this->data['PersonaBeneficio']['turno_pago']))); ?></td>
		</tr>
	</table>
	<table class="tbl_form">
		<tr>
			<td><?php echo $frm->input('PersonaBeneficio.nro_legajo',array('label'=>'LEGAJO','size'=>11,'maxlength'=>11)); ?></td>
			<td><?php echo $frm->input('PersonaBeneficio.fecha_ingreso',array('dateFormat' => 'DMY','label'=>'FECHA DE INGRESO','minYear'=>'1900', 'maxYear' => date("Y") - 1))?></td>
                        <td><?php echo $frm->money('PersonaBeneficio.sueldo_neto','SUELDO NETO',(isset($this->data['PersonaBeneficio']['sueldo_neto']) ? $this->data['PersonaBeneficio']['sueldo_neto'] : '0.00')); ?></td>
                        <td><?php echo $frm->money('PersonaBeneficio.debitos_bancarios','DEBITOS BANCARIOS',(isset($this->data['PersonaBeneficio']['debitos_bancarios']) ? $this->data['PersonaBeneficio']['debitos_bancarios'] : '0.00')); ?></td>
		</tr>
	</table>
	<table class="tbl_form">
		<tr>
			<td colspan="3"><?php echo $this->renderElement('banco/nombre',array('id' => $this->data['PersonaBeneficio']['banco_id'],'plugin' => 'config'))?></td>
		</tr>
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
	<?php if($MOD_TARJETAS):?>
	<hr>
	<?php //echo $this->renderElement('persona_beneficios/tarjeta_debito_form',array('plugin' => 'pfyj'))?>
	<?php endif;?>	


	<div style="clear: both;"></div>

</div>
<?php echo $frm->hidden('PersonaBeneficio.codigo_beneficio',array('value' => $this->data['PersonaBeneficio']['codigo_beneficio'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.persona_id',array('value' => $persona['Persona']['id'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.idr_persona',array('value' => $persona['Persona']['idr'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.idr',array('value' => $this->data['PersonaBeneficio']['idr'])); ?>
<?php echo $frm->hidden('PersonaBeneficio.cbu_nosis_validado',array('value' => $this->data['PersonaBeneficio']['cbu_nosis_validado'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/pfyj/persona_beneficios/index/".$persona['Persona']['id'] : $fwrd) ))?>

<?php 
if(isset($persona['Socio']['id']) && $persona['Socio']['id'] != 0) echo $this->renderElement('orden_descuento/grilla_ordenes_by_beneficio',array('plugin' => 'mutual','socio_id' => $persona['Socio']['id'], 'persona_beneficio_id' => $this->data['PersonaBeneficio']['id'],'solo_adeudadas' => 1));
?>

<?php echo $this->renderElement('persona_beneficios/' . Configure::read('APLICACION.beneficios_externos_render'),array('documento' => $persona['Persona']['documento']))?>
<?php // debug($this->data)?>
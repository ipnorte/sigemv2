<?php 
$INI_FILE = $_SESSION['MUTUAL_INI'];
$MOD_SIISA = (isset($INI_FILE['general']['modulo_siisa']) && $INI_FILE['general']['modulo_siisa'] != 0 ? TRUE : FALSE);
?>
<?php if($menuPersonas == 1) echo $this->renderElement('personas/padron_header',array('persona' => $persona,'plugin'=>'pfyj'))?>
<?php if($persona['Persona']['fallecida'] == 1):?>
	<div class="notices_error">PERSONA REGISTRADA COMO FALLECIDA EL <?php echo $util->armaFecha($persona['Persona']['fecha_fallecimiento'])?></div>
<?php endif;?>
<h3>ALTA SOLICITUD DE CREDITO</h3>        

    <?php echo $this->renderElement('mutual_producto_solicitudes/menu',array('persona' => $persona,'plugin'=>'mutual'))?>

<?php if(count($persona['PersonaBeneficio']) != 0):?>

<script type="text/javascript">
Event.observe(window, 'load', function(){


	<?php if($persona['Persona']['fallecida'] == 1):?>
		$('NuevaOrdenProductoMutual').disable();
		$('btn_submit').disable();
		return;
	<?php endif;?>
	
   
    $('ProveedorPlanCuotaGrillaMontoId').enable();
     $('ProveedorPlanGrillaCuotaCuotaId').enable();
     $('MutualProductoSolicitudFormaPago').enable();
     $('MutualProductoSolicitudObservaciones').enable();
     $('MutualProductoSolicitudVendedorId').enable();    
	
	var beneficioSel = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
	getComboPlanes();
    getCapacidadPago();

	$('MutualProductoSolicitudPersonaBeneficioId').observe('change',function(){
		getComboPlanes();
        getCapacidadPago();
	});
	

	$('ProveedorPlanId').observe('change',function(){
        $('ProveedorPlanCuotaGrillaMontoId').enable();
        $('ProveedorPlanGrillaCuotaCuotaId').enable();
        $('MutualProductoSolicitudFormaPago').enable();
        $('MutualProductoSolicitudObservaciones').enable();
        $('MutualProductoSolicitudVendedorId').enable();        
		getComboMontos();
	});	
	$('ProveedorPlanCuotaGrillaMontoId').observe('change',function(){
		getComboCuotas();
	});

    $('MutualProductoSolicitudDebitosBancarios').observe('blur', function(event){
        var valor = parseFloat($('MutualProductoSolicitudDebitosBancarios').getValue()).toFixed(2);
        if(isNaN(valor)){
            document.getElementById('MutualProductoSolicitudDebitosBancarios').value = '0.00';
        }
        <?php if($MOD_SIISA):?>
        consultaSIISA();
        <?php endif;?>        
    }); 
    
	<?php if($MOD_SIISA):?>
        $('MutualProductoSolicitudSueldoNeto').observe('blur', function(e){
            consultaSIISA();
        });	
	<?php endif;?>
	
    $('ProveedorPlanGrillaCuotaCuotaId').observe('change', function () {
    	getCuota();
    });	           
        

});

function getCapacidadPago(){
    beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
    var url = '<?php echo $this->base?>/pfyj/persona_beneficios/get_capacidadad_pago/' + beneficio;
    new Ajax.Request(url, {
            asynchronous:true,
            evalScripts:true,
            onInteractive: function(request){
                $('spinner_beneficio').show();
            },  
            onSuccess: function(response) {
                $('spinner_beneficio').hide();
                var json = response.responseText.evalJSON();
                document.getElementById('MutualProductoSolicitudSueldoNeto').value = parseFloat(json.sueldo_neto).toFixed(2);
                document.getElementById('MutualProductoSolicitudDebitosBancarios').value = parseFloat(json.debitos_bancarios).toFixed(2);
          }
    });
}

function getComboPlanes(){
//    $('btn_submit').disable();
	beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
// 	$('spinner_msg').update("CARGANDO PLANES");
	new Ajax.Updater('ProveedorPlanId','<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/P/0/0/0/0' , {asynchronous:false, evalScripts:true, onComplete:function(request, json) {$('spinner_plan').hide();$('btn_submit').enable();getComboMontos();},onLoading:function(request) {Element.show('spinner_plan');$('btn_submit').disable();}, requestHeaders:['X-Update', 'ProveedorPlanId']});
}

function getComboMontos(){
//    $('btn_submit').disable();
	planID = $('ProveedorPlanId').getValue();
	if(planID !== null){
		beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
// 		$('spinner_msg').update("CARGANDO MONTOS");
		new Ajax.Updater('ProveedorPlanCuotaGrillaMontoId','<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/M/' + planID, {asynchronous:false, evalScripts:true, onComplete:function(request, json) {$('spinner_montos').hide();$('btn_submit').enable();getComboCuotas();},onLoading:function(request) {Element.show('spinner_montos');$('btn_submit').disable();}, requestHeaders:['X-Update', 'ProveedorPlanCuotaGrillaMontoId']});
	}else{
		$('btn_submit').disable();
		alert("**** NO EXISTEN PLANES DISPONIBLES PARA EL ORGANISMO DEL BENEFICIO ****");
	}		
}

function getComboCuotas(){

	planID = $('ProveedorPlanId').getValue();
	beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
	fecha = '<?php echo date('Y-m-d')?>';
	cuotaID = $('ProveedorPlanCuotaGrillaMontoId').getValue();

    if(cuotaID !== null){

        new Ajax.Updater(
        	'ProveedorPlanGrillaCuotaCuotaId',
        	'<?php echo $this->base?>/proveedores/proveedor_planes/combo_planes_vigentes/'+ beneficio + '/C/' + planID + '/' + cuotaID, 
        	{
        		asynchronous:true, 
        		evalScripts:true, 
        		onComplete: function(request, json) {
        			$('spinner_cuotas').hide();
        			$('btn_submit').enable();
        			getCuota();
        		},
        		onLoading: function(request) { 
        			Element.show('spinner_cuotas');
        			$('btn_submit').disable();
        		}, 
        		requestHeaders:['X-Update', 'ProveedorPlanGrillaCuotaCuotaId']});      

	}else{
        $('ProveedorPlanCuotaGrillaMontoId').disable();
        $('ProveedorPlanGrillaCuotaCuotaId').disable();
        $('ProveedorPlanGrillaCuotaCuotaId').update("");
        $('MutualProductoSolicitudFormaPago').disable();
        $('MutualProductoSolicitudObservaciones').disable();
        $('MutualProductoSolicitudVendedorId').disable();
		$('btn_submit').disable();
		alert("**** NO EXISTEN MONTOS DISPONIBLES PARA PLAN ****");
    }
}

function confirmar(){

	if($('ProveedorPlanId').getValue() === null || $('ProveedorPlanCuotaGrillaMontoId').getValue() === null || $('ProveedorPlanGrillaCuotaCuotaId').getValue() === null){
		return false;
	}
        
        var sueldoNeto = parseFloat($('MutualProductoSolicitudSueldoNeto').getValue()).toFixed(2);
        if(isNaN(sueldoNeto) || sueldoNeto <= 0){
            alert('Debe indicar el Sueldo Neto!');
            $('MutualProductoSolicitudSueldoNeto').focus();
            return false;
        }  

	
	var msg = "**** NUEVA SOLICITUD DE CREDITO ****\n\n";
	msg = msg + "BENEFICIO: " + getTextoSelect('MutualProductoSolicitudPersonaBeneficioId') + "\n\n";
	msg = msg + "PLAN: " + getTextoSelect('ProveedorPlanId') + "\n";
	msg = msg + "SOLICITADO: " + getTextoSelect('ProveedorPlanCuotaGrillaMontoId') + "\n";
        msg = msg + "CUOTAS: " + getTextoSelect('ProveedorPlanGrillaCuotaCuotaId') + '\n';

	if($('MutualProductoSolicitudVendedorId').getValue() !== ""){
		msg = msg + "\n";
		msg = msg + "VENDEDOR: " + getTextoSelect('MutualProductoSolicitudVendedorId') + "\n\n";
	}
	
	msg = msg + "\n" + "GENERAR SOLICITUD?";
	return confirm(msg);
}



function getCuota(){
    cuotaID = $('ProveedorPlanGrillaCuotaCuotaId').getValue();
    var url = '<?php echo $this->base ?>/proveedores/proveedor_planes/get_cuota_grilla/'+ cuotaID;
    new Ajax.Request(url, {
    		method: 'get',
            asynchronous:true,
            evalScripts:true,
            onInteractive: function(request){
                $('spinner_beneficio').show();
            },  
            onSuccess: function(response) {
                $('spinner_beneficio').hide();
                var json = response.responseText.evalJSON();
                document.getElementById('MutualProductoSolicitudCuotaGrillaId').value = parseFloat(json.liquido).toFixed(2);
                document.getElementById('MutualProductoSolicitudCuotaGrillaImporte').value = parseFloat(json.importe).toFixed(2);
          },
          onComplete: function(response) {
                <?php if($MOD_SIISA):?>
                consultaSIISA();
                <?php endif;?>                     
          }
    });    
    
}



</script>

<div class="areaDatoForm">

	<?php echo $frm->create(null,array('action' => 'nuevo_credito/'.$persona['Persona']['id'],'id' => 'NuevaOrdenProductoMutual','onsubmit' => "return confirmar()"))?>

    <div style="border-bottom: 1px solid #BAB8B7;width: 100%;">
        <table class="tbl_form">
                <tr>
                        <td><label for="MutualProductoSolicitudPersonaBeneficioId">BENEFICIO</label></td>
                        <td><?php echo $this->renderElement('persona_beneficios/combo_beneficios',array('plugin' => 'pfyj','persona_id' => $persona['Persona']['id'],'soloActivos' => 1,'style' => 'font-weight: bold;'))?></td>
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
                            <td><label>FORMA DE PAGO</label></td>
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
			<?php if(!empty($cancelaciones)):?>
			<tr>
				<td colspan="2">
				<div class="areaDatoForm3">
					<h3>ORDENES DE CANCELACION EMITIDAS</h3>
					<table>
						<tr>
							<th></th>
							<th>#</th>
							<th>VENCIMIENTO</th>
							<th>A LA ORDEN DE</th>
							<th>CONCEPTO</th>
							<th>IMPORTE</th>
						</tr>
						<?php foreach ($cancelaciones as $cancelacion):?>
							<tr>
								<td><input type="checkbox" name="data[MutualProductoSolicitud][CancelacionOrden][<?php echo $cancelacion['CancelacionOrden']['id']?>]" value="<?php echo $cancelacion['CancelacionOrden']['id']?>"/></td>
								<td><?php echo $cancelacion['CancelacionOrden']['id']?></td>
								<td><?php echo $util->armaFecha($cancelacion['CancelacionOrden']['fecha_vto'])?></td>
								<td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
								<td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
								<td align="right"><?php echo $util->nf($cancelacion['CancelacionOrden']['importe_proveedor'])?></td>
							</tr>
						<?php endforeach;?>
					</table>
				
				</div>
				</td>
			</tr>
			<?php endif;?>
			<tr>
                            <td><label>OBSERVACIONES</label></td>
				<td><?php echo $frm->textarea('MutualProductoSolicitud.observaciones',array('cols' => 60, 'rows' => 10))?></td>
			</tr>
			<tr>
                            <td><label>VENDEDOR</label></td>
				<td><?php echo $frm->input('MutualProductoSolicitud.vendedor_id',array('type' => 'select','options' => $vendedores, 'empty' => true))?></td>
			</tr>

		</table>
		<div style="clear: both;"></div>
		
        <?php if($MOD_SIISA):?>
            <script type="text/javascript">
            	function consultaSIISA() {
            		beneficio = $('MutualProductoSolicitudPersonaBeneficioId').getValue();
                    var url = '<?php echo $this->base?>/pfyj/persona_beneficios/consulta_siisa_ajax/' + beneficio;
        			var payload = {
            			"data[sueldo_neto]": parseFloat($('MutualProductoSolicitudSueldoNeto').getValue()),
            			"data[debitos_bancarios]": parseFloat($('MutualProductoSolicitudDebitosBancarios').getValue()),
            			"data[cuota_credito]": parseFloat($('MutualProductoSolicitudCuotaGrillaImporte').getValue())
        			}

                    $('spinner_siisa').show();
                    $('#siisa_response').removeClassName('notices_ok');
                    $('#siisa_response').removeClassName('notices_error');
                    $('#siisa_response').removeClassName('notices');
                    document.getElementById('#siisa_response').innerHTML = "";
                    new Ajax.Request(url, {
                    		method: 'post',
							parameters: payload,
                            asynchronous: true,
                            // evalScripts: true,
                            onInteractive: function(request){
                                $('spinner_siisa').show();
                            },  
                            onSuccess: function(response) {
                                $('spinner_siisa').hide();
                                var json = response.responseText.evalJSON();
                                document.getElementById('#siisa_response').innerHTML = json.decisionResult;
                                if(json.aprueba) {
                                	$('#siisa_response').addClassName('notices_ok');
                                } else if(json.rechaza) {
                                	$('#siisa_response').addClassName('notices_error');
                                } else if(json.onError) {
                                	document.getElementById('#siisa_response').innerHTML = "ERROR SERVICIO COD: " + json.oERROR.httpCode + " | MSG SIISA: "  + json.oERROR.message;
                                	$('#siisa_response').addClassName('notices');
                                }
                          }
                    });            	
            	}
            
            </script>
            <table style="width: 90%; margin-bottom: 5px;">
            	<tr>
            		<th >Motor de Decisi&oacute;n SIISA</th>
            		<td style="width: 85%;">
            		<div id="#siisa_response" style="font-weight: bold; width: 98%;"></div>
            		<div id="spinner_siisa" style="display: none; float: left;color:red;font-size:xx-small;"><?php echo $html->image('controles/ajax-loader.gif');?></div>
            		</td>
            	</tr>
            </table>
        	
        <?php endif;?>		
				
		<?php //   echo $controles->ajaxLoader('spinner_msg','PROCESANDO...')?>
        <?php echo $this->renderElement('mutual_producto_solicitudes/info_operaciones_pendientes_by_persona', array(
           'plugin' => 'mutual',
            'persona_id' => $persona['Persona']['id'],
        ));?>

	<?php echo $frm->hidden('persona_id',array('value' => $persona['Persona']['id']))?>
	<?php echo $frm->hidden('socio_id',array('value' => (isset($persona['Socio']['id']) ? $persona['Socio']['id'] : 0)))?>
	<?php echo $frm->hidden('permanente',array('value' => 0))?>
	<?php echo $frm->hidden('sin_cargo',array('value' => 0))?>
	<?php echo $frm->hidden('con_importe_fijo',array('value' => 0))?>
	<?php echo $frm->hidden('prestamo',array('value' => 1))?>
	<?php echo $frm->hidden('MutualProductoSolicitud.cuota_grilla_importe',array('value' => 0))?>
	
	<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GENERAR SOLICITUD','URL' => ( empty($fwrd) ? "/mutual/mutual_producto_solicitudes/by_persona/".$persona['Persona']['id'] : $fwrd) ))?>


</div>
<?php else:?>

		<div class='notices_error'>NO POSEE BENEFICIOS CARGADOS!</div>
		<div class="actions"><?php echo $controles->botonGenerico('/pfyj/persona_beneficios/add/'.$persona['Persona']['id'],'controles/add.png','Cargar Beneficio')?></div>

<?php endif;?>	

<?php //   debug($cancelaciones)?>
<?php echo $this->renderElement('proveedor/padron_header',array('proveedor' => $proveedor))?>
<h3>ADMINISTRACION DE PLANES :: MODIFICAR PLAN</h3>

<script type="text/javascript">

Event.observe(window, 'load', function(){
    <?php echo (empty($plan['ProveedorPlan']['metodo_calculo']) ? " disableParamsCalculo();" : "")?>
    $('ProveedorPlanMetodoCalculo').observe('change',function(){setStatusParamsCalculo()});
});

function setStatusParamsCalculo(){
    var metodoCalculo = $('ProveedorPlanMetodoCalculo').getValue();
    if(metodoCalculo == ""){disableParamsCalculo();}
    else{enableParamsCalculo();}    
}

function enableParamsCalculo(){
    $('ProveedorPlanTna').enable();
    $('ProveedorPlanIva').enable();
    $('ProveedorPlanTipoCuotaGastoAdmin').enable();
    $('ProveedorPlanGastoAdmin').enable();
    $('ProveedorPlanGastoAdminBaseCalculo').enable(); 
    $('ProveedorPlanTipoCuotaSellado').enable();
    $('ProveedorPlanSellado').enable();
    $('ProveedorPlanSelladoBaseCalculo').enable(); 
    
    document.getElementById('ProveedorPlanTna').value = "0.00";
    document.getElementById('ProveedorPlanIva').value = "0.00";
    document.getElementById('ProveedorPlanGastoAdmin').value = "0.00";
    document.getElementById('ProveedorPlanSellado').value = "0.00";
    document.getElementById('ProveedorPlanGastoAdminBaseCalculo').value = "1";
    document.getElementById('ProveedorPlanSelladoBaseCalculo').value = "2";

    document.getElementById('ProveedorPlanTipoCuotaGastoAdmin').value = "MUTUTCUOGOTO";
    document.getElementById('ProveedorPlanTipoCuotaSellado').value = "MUTUTCUOSELL";    

}

function disableParamsCalculo(){
    $('ProveedorPlanTna').disable();
    document.getElementById('ProveedorPlanTna').value = "";
    $('ProveedorPlanIva').disable();
    document.getElementById('ProveedorPlanIva').value = "";  
    $('ProveedorPlanTipoCuotaGastoAdmin').disable();
    $('ProveedorPlanGastoAdmin').disable();
    $('ProveedorPlanGastoAdminBaseCalculo').disable();
    document.getElementById('ProveedorPlanTipoCuotaGastoAdmin').value = ""; 
    document.getElementById('ProveedorPlanGastoAdmin').value = "";
    document.getElementById('ProveedorPlanGastoAdminBaseCalculo').value = "";

    $('ProveedorPlanTipoCuotaSellado').disable();
    $('ProveedorPlanSellado').disable();
    $('ProveedorPlanSelladoBaseCalculo').disable();    
    document.getElementById('ProveedorPlanTipoCuotaSellado').value = "";
    document.getElementById('ProveedorPlanSellado').value = ""; 
    document.getElementById('ProveedorPlanSelladoBaseCalculo').value = "";       
}


//

</script>

<div class="areaDatoForm">
<?php echo $frm->create(null,array('action' => 'editar_plan/' . $plan['ProveedorPlan']['id']))?>
<table class="tbl_form">
	<tr>
		<td>DENOMINACION</td>
        <td><?php echo $frm->input('ProveedorPlan.descripcion',array('value' => $plan['ProveedorPlan']['descripcion'],'size'=>60,'maxlength'=>100)); ?></td>
	</tr>
	<tr>
		<td>TIPO PRODUCTO</td>
		<td>
		<?php echo $this->renderElement('global_datos/combo_global',array(
																		'plugin'=>'config',
																		'label' => " ",
																		'model' => 'ProveedorPlan.tipo_producto',
																		'prefijo' => 'MUTUPROD',
																		'disabled' => true,
																		'empty' => false,
																		'metodo' => "get_tipo_productos",
																		'selected' => $plan['ProveedorPlan']['tipo_producto']	
		))?>				
		</td>
    <tr>
            <td>CONDICIONES DE CALCULO</td>
            <td>
                <div class="areaDatoForm2">
                    <table class="tbl_form">
                        <tr>
                        <td>

                        <?php echo $frm->input('ProveedorPlan.metodo_calculo',array('label' => 'METODO DE CALCULO','type' => 'select','empty' => true,'options' => $metodos, 'value' => $plan['ProveedorPlan']['metodo_calculo']))?></td>
                        <td>
                        <?php echo $frm->money('ProveedorPlan.tna','T.N.A. (TASA NOMINAL ANUAL - %)',$plan['ProveedorPlan']['tna'],true,6,6)?>
                        </td>
                        <td>
                        <?php echo $frm->money('ProveedorPlan.iva','I.V.A. (%)',$plan['ProveedorPlan']['iva'],false,4,4)?>
                        </td>
                        </tr>
                    </table> 
                    <table class="tbl_form"> 
        <tr>
            <td>
            <?php echo $this->renderElement('global_datos/combo_cuotas_puntuales',array(
																					'plugin'=>'config',
																					'label' => "GASTO ADM./OTORGAM.",
																					'model' => 'ProveedorPlan.tipo_cuota_gasto_admin',
																					'disable' => false,
																					'empty' => true,
																					'selected' => $plan['ProveedorPlan']['tipo_cuota_gasto_admin'],
					))?>
            </td>
            <td>
                <?php echo $frm->money('ProveedorPlan.gasto_admin','%',$plan['ProveedorPlan']['gasto_admin'],false,6,6)?>
            </td>
            <td>
            <?php echo $frm->input('ProveedorPlan.gasto_admin_base_calculo',array('label' => 'BASE DE CALCULO','type' => 'select', 'options' => $criterios, 'value' => $plan['ProveedorPlan']['gasto_admin_base_calculo']))?>
            </td>

        </tr>
        <tr>
        <td>
            <?php echo $this->renderElement('global_datos/combo_cuotas_puntuales',array(
																					'plugin'=>'config',
																					'label' => "SELLADO CONTRATO/PAG.",
																					'model' => 'ProveedorPlan.tipo_cuota_sellado',
																					'disable' => false,
																					'empty' => true,
																					'selected' => $plan['ProveedorPlan']['tipo_cuota_sellado'],
					))?>
            </td>        
            <td>
                <?php echo $frm->money('ProveedorPlan.sellado','%',$plan['ProveedorPlan']['sellado'],false,6,6)?>
            </td>
            <td>
            <?php echo $frm->input('ProveedorPlan.sellado_base_calculo',array('label' => 'BASE DE CALCULO','type' => 'select', 'options' => $criterios, 'value' => $plan['ProveedorPlan']['sellado_base_calculo']))?>
            </td>        
        </tr>       
    </table>                     
                </div>
            </td>
    </tr>  
	</tr>
    <tr>
        <td>MORA/CANCELACION</td>
        <td>
            <?php echo $frm->money('ProveedorPlan.interes_moratorio','INTERES MORATORIO %',$plan['ProveedorPlan']['interes_moratorio'],false,6,6)?>
            <?php echo $frm->money('ProveedorPlan.costo_cancelacion_anticipada','COSTO CANCELACION ANTICIPADO %',$plan['ProveedorPlan']['costo_cancelacion_anticipada'],false,6,6)?>
        </td>
    </tr>
        <?php if(isset($_SESSION['MUTUAL_INI']['general']['modulo_ayuda_economica']) && $_SESSION['MUTUAL_INI']['general']['modulo_ayuda_economica'] == 1):?>
        <tr>
            <td>AYUDA ECONOMICA (Res. No1418/03 INAES)</td><td><?php echo $frm->input('ProveedorPlan.ayuda_economica',array('value' => $plan['ProveedorPlan']['ayuda_economica'],'checked' => ($plan['ProveedorPlan']['ayuda_economica'] == 1 ? 'checked' : ''),'disabled' => 'disabled')); ?></td>
        </tr> 
        <?php endif;?>
	<tr>
		<td>ORGANISMO</td>
		<td>
		<?php
		echo $this->renderElement('global_datos/grilla_checks',array(
			'plugin' => 'config',
			'metodo' => 'get_organismos/1',
			'model' => 'ProveedorPlan.organismos',
			'header' => false,
			'selected' =>Set::extract("ProveedorPlanOrganismo/codigo_organismo",$plan['ProveedorPlanOrganismo']),
		));
		?>
		</td>
	</tr>
        <tr>
            <td>PLANTILLA DE IMPRESION</td>
            <td>
		<?php 
		echo $this->renderElement('global_datos/combo_global',array(
			'plugin' => 'config',
			'metodo' => 'get_solicitud_templates/1',
			'model' => 'ProveedorPlan.modelo_solicitud_codigo',
			'empty' => false,
			'selected' => (isset($plan['ProveedorPlan']['modelo_solicitud_codigo']) ? $plan['ProveedorPlan']['modelo_solicitud_codigo'] : NULL),
		));
		?>                 
            </td>
        </tr>
        <tr>
            <td>IMPRIMIR ANEXOS</td>
            <td>
		<?php 
		echo $this->renderElement('global_datos/grilla_checks',array(
			'plugin' => 'config',
			'metodo' => 'get_solicitud_anexos',
			'model' => 'ProveedorPlan.anexos',
			'header' => false,
			'selected' =>Set::extract("ProveedorPlanAnexo/codigo_anexo",$plan['ProveedorPlanAnexo']),
		));
		?>                
            </td>
        </tr>
        <tr>
            <td>DOCUMENTACION</td>
            <td>
            <?php 
            echo $this->renderElement('global_datos/grilla_checks',array(
                    'plugin' => 'config',
                    'metodo' => 'get_solicitud_documentos',
                    'model' => 'ProveedorPlan.documentos',
                    'header' => false,
                    'selected' => Set::extract("ProveedorPlanDocumento/codigo_documento",$plan['ProveedorPlanDocumento']),
            ));
            ?>
            </td>
	</tr>

</table>
<?php echo $frm->hidden('ProveedorPlan.id',array('value' => $plan['ProveedorPlan']['id'])); ?>
<?php echo $frm->hidden('ProveedorPlan.proveedor_id',array('value' => $plan['ProveedorPlan']['proveedor_id'])); ?>
<?php echo $frm->btnGuardarCancelar(array('TXT_GUARDAR' => 'GUARDAR','URL' => ( empty($fwrd) ? "/proveedores/proveedor_planes/index/".$proveedor['Proveedor']['id'] : $fwrd) ))?>

</div>

<?php // debug($plan)?>
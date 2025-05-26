<?php echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>NUEVA SOLICITUD</h3>

<?php echo $form->create(null,array('action' => 'alta_confirm/'.$TOKEN_ID,'name'=>'formAltaConfirm','id'=>'formAltaConfirm','onsubmit'=>"return confirm('GENERAR SOLICITUD?')"));?>
<hr/>
<?php echo $this->renderElement('solicitudes/alta_info_datos',array('plugin' => 'ventas','solicitud' => $solicitud))?>
<style>
    .field{
        width: auto;float: left;margin:5px 10px 5px 0px;
    }
</style>
<div class="areaDatoForm">
    <h3>DATOS DE LA SOLICITUD A EMITIR</h3>
    <div class="areaDatoForm2">
        <h3>PRODUCTO SOLICITADO</h3>
        <hr/>
        <table class="tbl_form">
            <tr>
                <td><strong><?php echo $solicitud['Plan']['ProveedorPlan']['cadena']?></strong></td>
            </tr>
            <tr>
                <td>
                    <div class="field" style="color:green; font-size: larger;">CAPITAL: <strong><?php echo $util->nf($solicitud['Cuota']['ProveedorPlanGrillaCuota']['capital'])?></strong></div>
                    <div class="field" style="color:green; font-size: larger;">LIQUIDO: <strong><?php echo $util->nf($solicitud['Cuota']['ProveedorPlanGrillaCuota']['liquido'])?></strong></div>
                    <div class="field" style="color:green; font-size: larger;">CANTIDAD DE CUOTAS: <strong><?php echo $solicitud['Cuota']['ProveedorPlanGrillaCuota']['cuotas']?></strong></div>
                    <div class="field" style="color:green; font-size: larger;">IMPORTE CUOTA: <strong><?php echo $util->nf($solicitud['Cuota']['ProveedorPlanGrillaCuota']['importe'])?></strong></div>
                </td>
            </tr>
        </table>
    </div>
    <?php if(isset($solicitud['Beneficio'])):?>
        <h3>MEDIO DE PAGO</h3>
        <hr>
        <table class="tbl_form">
            <tr>
                <td>
                    <div class="field">ORGANISMO: <strong><?php echo $solicitud['Beneficio']['codigo_beneficio_desc']?></strong></div>
                    <div class="field">EMPRESA/ENTIDAD: <strong><?php echo $solicitud['Beneficio']['codigo_empresa_desc']?></strong></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="field">BANCO: <strong><?php echo $solicitud['Beneficio']['banco']?></strong></div>
                    <div class="field">SUCURSAL: <strong><?php echo $solicitud['Beneficio']['nro_sucursal']?></strong></div>
                    <div class="field">CUENTA: <strong><?php echo $solicitud['Beneficio']['nro_cta_bco']?></strong></div>
                    <div class="field">CBU: <strong><?php echo $solicitud['Beneficio']['cbu']?></strong></div>
                </td>
            </tr>
            <?php if(isset($solicitud['Beneficio']['sueldo_neto']) && !empty($solicitud['Beneficio']['sueldo_neto'])):?>
            <tr>
                <td>
                    <div class="field">SUELDO NETO: <strong><?php echo $util->nf($solicitud['Beneficio']['sueldo_neto'])?></strong></div>
                    <div class="field">DEBITOS BANCARIOS: <strong><?php echo $util->nf($solicitud['Beneficio']['debitos_bancarios'])?></strong></div>
                </td>
            </tr>
            <?php endif;?>
            <?php if(isset($solicitud['Beneficio']['tarjeta_numero']) && !empty($solicitud['Beneficio']['tarjeta_numero'])):?>
                <tr>
                <td>
                    <div class="field">TITULAR: <strong><?php echo $solicitud['Beneficio']['tarjeta_titular']?></strong></div>
                    <div class="field">NUMERO: <strong><?php echo $solicitud['Beneficio']['tarjeta_numero']?></strong></div>
                    <div class="field">VIGENCIA: <strong><?php echo $solicitud['Beneficio']['tarjeta_vigencia']?></strong></div>
                </td>
            </tr>
            <?php endif;?>    
        </table>
    <?php endif;?>
    <?php if(isset($solicitud['Cancelaciones']) && !empty($solicitud['Cancelaciones'])):?>
    <hr/>
    <h4>Ordenes de Cancelaciones a procesar</h4>
    <table class="tbl_form">
        <tr>
            <th>#</th>
            <th>A LA ORDEN DE</th>
            <th>CONCEPTO</th>
            <th>PRODUCTO</th>
            <th>TIPO</th>
            <th>VTO</th>
            <th>IMPORTE</th>
        </tr>
        <?php $TOTAL=0;?>
        <?php foreach ($solicitud['Cancelaciones'] as $cancelacion):?>
        <tr>
            <td><?php echo $cancelacion['CancelacionOrden']['id']?></td>
            <td><?php echo $cancelacion['CancelacionOrden']['a_la_orden_de']?></td>
            <td><?php echo $cancelacion['CancelacionOrden']['concepto']?></td>
            <td><?php echo $cancelacion['CancelacionOrden']['proveedor_producto_odto']?></td>
            <td><?php echo $cancelacion['CancelacionOrden']['tipo_cancelacion_desc']?></td>
            <td><?php echo $cancelacion['CancelacionOrden']['fecha_vto']?></td>
            <td style="text-align: right;"><?php echo $util->nf($cancelacion['CancelacionOrden']['importe_proveedor'])?></td>
        </tr>
        <?php $TOTAL+=$cancelacion['CancelacionOrden']['importe_proveedor'];?>
        <?php endforeach;?>
        <tr class="totales">
            <th colspan="6"></th>
            <th><?php echo $util->nf($TOTAL)?></th>
        </tr>
            
    </table>
    <?php endif;?>

    <?php if(isset($solicitud['Archivos']) && !empty($solicitud['Archivos'])):?>
    <div style="clear: both;"></div>
    <hr/>
    <h4>Documentos adjuntos</h4>
    <table>
        <?php foreach($solicitud['Archivos'] as $id => $archivo):?>
        
        <tr>
            <td><?php echo $archivo['descripcion']?> - </td>
            <td><?php echo $archivo['file_name']?> - </td>
            <td><?php echo $archivo['file_type']?></td>
        </tr>
        <?php endforeach;?>
        
    </table>
    
    <?php endif;?>
    
    
</div>
        <?php echo $this->renderElement('mutual_producto_solicitudes/info_operaciones_pendientes_by_persona', array(
           'plugin' => 'mutual',
            'persona_id' => $solicitud['Persona']['id'],
        ));?>
<hr/>

<?php if(!$solicitud['Persona']['fallecida']):?>
<input type="submit" value="GENERAR SOLICITUD DE CREDITO" id="btnProcessConfirm" style="background-color: green;color: white;height: 35px;border-color: green;font-size: large;padding: 5px;"/>
<?php else:?>
    <?php if($solicitud['Persona']['fallecida']):?>
    <div style="clear: both;"></div>
    <div class="notices_error2" style="width: 98%;">Persona registrada como FALLECIDA el <strong><?php echo $util->armaFecha($solicitud['Persona']['fecha_fallecimiento'])?></strong></div>
    <div style="clear: both;"></div>
    <?php endif;?> 
	<input type="submit" value="GENERAR SOLICITUD DE CREDITO" id="btnProcessConfirm" style="height: 35px;border-color: gray;font-size: large;padding: 5px;" disabled=""/>
<?php endif;?>
    
    <?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
<?php echo $frm->hidden('MutualProductoSolicitud.vendedor_id',array('value' => (isset($_SESSION['Auth']['Usuario']['vendedor_id']) && !empty($_SESSION['Auth']['Usuario']['vendedor_id']) ? $_SESSION['Auth']['Usuario']['vendedor_id'] : NULL)))?>
<?php echo $form->end();?>

<?php //debug($solicitud) ?>
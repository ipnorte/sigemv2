<?php echo $form->create(null,array('action' => 'alta_confirm/'.$TOKEN_ID,'name'=>'formAltaConfirm','id'=>'formAltaConfirm','onsubmit'=>"return confirm('GENERAR SOLICITUD?')"));?>
<div class="card mb-1">
    <div class="card-header bg-info text-white"><i class="fas fa-handshake"></i>&nbsp;Aprobar Solicitud</div>
    <div class="card-body">
        <div class="card mb-1">
            <div class="card-header"><strong>Datos del Solicitante</strong>
                &nbsp;<a href="<?php echo $this->base . "/ventas/solicitudes/estado_cuenta/" . $solicitud['Persona']['id']?>" target="_blank"><i class="fas fa-calculator"></i></a></div>
            <div class="card-body">
                <div class="row mb-1 ">
                    <div class="col-6">NOMBRE: <strong><?php echo $solicitud['Persona']['apenom']?></strong></div>
                    <div class="col-5">DOCUMENTO: <strong><?php echo $solicitud['Persona']['tdoc_ndoc']?></strong></div>
                </div>
                <div class="row mb-1 ">
                    <div class="col-12"><?php echo $solicitud['Persona']['domicilio']?></div>
                </div>
                <div class="row mb-1 ">
                    <div class="col-12"><?php echo $solicitud['Persona']['datos_complementarios']?></div>
                </div>
            </div>
        </div>
        <div class="card mb-1">
            <div class="card-header"><strong>Datos de la Solicitud</strong></div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto / Plan</th>
                            <th class="text-center">Capital</th>
                            <th class="text-center">Liquido</th>
                            <th class="text-center">Cuotas</th>
                            <th class="text-center">Importe</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $solicitud['Plan']['ProveedorPlan']['cadena']?></td>
                            <td class="font-weight-bold text-center"><?php echo $util->nf($solicitud['Cuota']['ProveedorPlanGrillaCuota']['capital'])?></td>
                            <td class="font-weight-bold text-center"><?php echo $util->nf($solicitud['Cuota']['ProveedorPlanGrillaCuota']['liquido'])?></td>
                            <td class="font-weight-bold text-center"><?php echo $solicitud['Cuota']['ProveedorPlanGrillaCuota']['cuotas']?></td>
                            <td class="font-weight-bold text-center"><?php echo $util->nf($solicitud['Cuota']['ProveedorPlanGrillaCuota']['importe'])?></td>
                            <td class="font-weight-bold text-center"><?php echo $util->nf($solicitud['Cuota']['ProveedorPlanGrillaCuota']['importe'] * $solicitud['Cuota']['ProveedorPlanGrillaCuota']['cuotas'])?></td>
                        </tr>
                    </tbody>
                </table>                
                <div class="card mb-1">
                    <div class="card-header"><strong>Medio de Pago</strong></div>
                    <div class="card-body">
                        <div class="row mb-1 ">
                            <div class="col-4">Organismo: <strong><?php echo $solicitud['Beneficio']['codigo_beneficio_desc']?></strong></div>
                            <div class="col-8">Empresa/Entidad: <strong><?php echo $solicitud['Beneficio']['codigo_empresa_desc']?></strong></div>
                        </div>
                        <div class="row mb-1 ">
                            <div class="col-6">Banco: <strong><?php echo $solicitud['Beneficio']['banco']?></strong></div>
                            <div class="col-3">Sucursal: <strong><?php echo $solicitud['Beneficio']['nro_sucursal']?></strong></div>
                            <div class="col-3">Cuenta: <strong><?php echo $solicitud['Beneficio']['nro_cta_bco']?></strong></div>
                        </div>
                        <div class="row mb-1 ">
                            <div class="col-4">C.B.U.: <strong><?php echo $solicitud['Beneficio']['cbu']?></strong></div>
                            <div class="col-3">Sueldo Neto: <strong><?php echo $util->nf($solicitud['Beneficio']['sueldo_neto'])?></strong></div>
                            <div class="col-3">Débitos Bancarios: <strong><?php echo $util->nf($solicitud['Beneficio']['debitos_bancarios'])?></strong></div>
                        </div>

                        <?php if(isset($solicitud['Beneficio']['tarjeta_numero']) && !empty($solicitud['Beneficio']['tarjeta_numero'])):?>
                            <div class="row mb-1 ">
                                <div class="col-12">
                                    Tarjeta de Débito: &nbsp;
                                    <strong><?php echo $solicitud['Beneficio']['tarjeta_numero']?></strong>
                                    &nbsp;
                                    Titular: <strong><?php echo $solicitud['Beneficio']['tarjeta_titular']?></strong>
                                </div>
                            </div>                            
                        <?php endif;?> 
                    </div>    
                </div>
                <?php if(isset($solicitud['Cancelaciones']) && !empty($solicitud['Cancelaciones'])):?>
                <div class="card mb-1">
                    <div class="card-header"><strong>Cancelaciones</strong></div>
                    <div class="card-body">
                        <small>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>A la Orden de</th>
                                    <th>Concepto</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Vencimiento</th>
                                    <th class="text-right">Importe</th>
                                </tr>                                                
                            </thead>
                            <tbody>
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
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6"></th>
                                    <th class="text-right"><?php echo $util->nf($TOTAL)?></th>
                                </tr>                                
                            </tfoot>
                        </table>
                        </small>    
                    </div>    
                </div>
                <?php endif;?>
                <?php if(isset($solicitud['siisa']) && !empty($solicitud['siisa'])):?>
                <div class="card mb-1">
                <div class="card-header font-weight-bold" id="siisa_cardHeader"><i class="fas fa-business-time"></i>&nbsp;Motor de Decisi&oacute;n SIISA</div>
                <div class="card-body">
                <?php
                App::import('Vendor','SIISAService',array('file' => 'siisa_service.php'));
                $SIISA = unserialize($solicitud['siisa']);
                ?>
                
                <?php if($SIISA->aprueba):?>
					<i class="fas fa-thumbs-up text-success" id="siisa_aprueba"></i>&nbsp;<span id="siisa_response" class="ml-2"><?php echo $SIISA->decisionResult?></span>
                <?php endif;?>

                <?php if($SIISA->rechaza):?>
                	<i class="fas fa-thumbs-down text-danger" id="siisa_rechaza"></i>&nbsp;<span id="siisa_response" class="ml-2 text-danger"><?php echo $SIISA->decisionResult?></span>
                <?php endif;?>

                
                </div>
                </div>
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
            
        </div>
    </div>
    <div class="card-footer">
        <div class="form-row">
            <div class="form-group col-md-4"></div>
            <div class="form-group col-md-4">
                <?php if(!$solicitud['Persona']['fallecida']):?>
                <button type="submit" id="btn_submit" name="btn_submit" class="form-control btn btn-primary btn-small"><i class="fas fa-check"></i>&nbsp;EMITIR LA SOLICITUD</button>
                <?php else:?>
                <button type="submit" id="btn_submit" name="btn_submit" disabled="" class="form-control btn btn-primary btn-disable"><i class="fas fa-check"></i>&nbsp;EMITIR LA SOLICITUD</button>
                <?php endif;?>        
            </div>            
            
            <div class="form-group col-md-4"></div>
        </div>        
    </div>    
</div>
<?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
<?php echo $frm->hidden('MutualProductoSolicitud.vendedor_id',array('value' => (isset($_SESSION['Auth']['Usuario']['vendedor_id']) && !empty($_SESSION['Auth']['Usuario']['vendedor_id']) ? $_SESSION['Auth']['Usuario']['vendedor_id'] : NULL)))?>
<?php echo $form->end();?>

<div class="card <?php echo ($solicitud['MutualProductoSolicitud']['anulada'] == 1 ? "border-danger" : "border-success") ?> ">
    <div class="card-header <?php echo ($solicitud['MutualProductoSolicitud']['anulada'] == 1 ? "bg-danger text-white" : "bg-success text-white") ?> ">
        <h4>Solicitud #<?php echo $solicitud['MutualProductoSolicitud']['id'] ?></h4>
    </div>
    <div class="card-body">
        <small>
            <div class="row mb-1 ">
                <div class="col-2">ESTADO: <strong><?php echo $solicitud['MutualProductoSolicitud']['estado_desc'] ?></strong></div>
                <div class="col-2">FECHA EMISION: <strong><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha']) ?></strong></div>
                <div class="col-2">EMITIDA POR: <strong><?php echo $solicitud['MutualProductoSolicitud']['user_created'] ?></strong></div>       
            </div>
            <?php if (!empty($solicitud['MutualProductoSolicitud']['vendedor_id'])): ?>
                <div class="row mb-1 ">
                    <div class="col-4">VENDEDOR: <strong><?php echo $solicitud['MutualProductoSolicitud']['vendedor_nombre'] ?></strong></div>
                    <?php if (!empty($solicitud['MutualProductoSolicitud']['vendedor_remito_id'])): ?>
                        <div class="col-6">
                            CONSTANCIA DE ENTREGA: <strong><?php echo $solicitud['MutualProductoSolicitud']['vendedor_remito'] ?></strong>
                            &nbsp;<a href="<?php echo $this->base . "/ventas/vendedores/imprimir_remito/" . $solicitud['MutualProductoSolicitud']['vendedor_remito_id'] ?>" target="_blank"><i class="fas fa-download"></i></a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?> 
            <div class="row mb-1">
                <div class="col-10"></div>
                <div class="col-2">
                    <?php if ($solicitud['MutualProductoSolicitud']['anulada'] == 0): ?>
                        &nbsp;<a role="button" class="btn btn-info btn-small btn-block" href="<?php echo $this->base . "/mutual/mutual_producto_solicitudes/imprimir_credito_mutual_pdf/" . $solicitud['MutualProductoSolicitud']['id'] ?>" target="_blank"><i class="fas fa-download"></i>&nbsp;Descargar</a>
                    <?php endif; ?>                 
                </div>
            </div>            
            <div class="row mb-1 ">
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="7" class="text-center">DATOS DEL PRODUCTO SOLICITADO</th>
                        </tr>
                        <tr>
                            <th>Producto</th>
                            <th>Plan</th>
                            <th class="text-center">Capital</th>
                            <th class="text-center">Líquido</th>
                            <th class="text-center">Cuotas</th>
                            <th class="text-center">Importe Cuota</th>
                            <th class="text-center">Total a Reintegrar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $solicitud['MutualProductoSolicitud']['producto'] ?></td>
                            <td><?php echo $solicitud['MutualProductoSolicitud']['proveedor_plan'] ?></td>
                            <td class="text-center"><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_solicitado']) ?></td>
                            <td class="text-center"><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_percibido']) ?></td>
                            <td class="text-center"><strong><?php echo $solicitud['MutualProductoSolicitud']['cuotas'] ?></strong></td>
                            <td class="text-center"><strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_cuota']) ?></strong></td>
                            <td class="text-center"><strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_total']) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--            <div class="row mb-1 bg-secondary">
                            <div class="col-6"><?php // echo $solicitud['MutualProductoSolicitud']['observaciones'] ?></div>
                        </div>-->
            <div class="row mb-1 ">
                <div class="col-6">
                    <div class="card mb-1">
                        <div class="card-header"><strong>Datos del Solicitante</strong>
                            &nbsp;<a href="<?php echo $this->base . "/ventas/solicitudes/estado_cuenta/" . $solicitud['MutualProductoSolicitud']['persona_id'] ?>" target="_blank"><i class="fas fa-calculator"></i></a></div>
                        <div class="card-body">
                            <div class="row mb-1 ">
                                <div class="col-6">NOMBRE: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_apenom'] ?></strong></div>
                                <div class="col-5">DOCUMENTO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc'] ?></strong></div>
                            </div>
                            <div class="row mb-1 ">
                                <div class="col-12"><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_domicilio'] ?></div>
                            </div>
                            <div class="row mb-1 ">
                                <div class="col-12"><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_complementarios'] ?></div>
                            </div>
                            <div class="row mb-1 ">
                                <div class="col-12"><?php echo $solicitud['MutualProductoSolicitud']['beneficiario_medio_contacto'] ?></div>
                            </div>
                        </div>
                    </div>                    
                </div>
                <div class="col-6">
                    <div class="card mb-1">
                        <div class="card-header"><strong>Medio de Pago</strong></div>
                        <div class="card-body">
                            <div class="row mb-1 ">
                                <div class="col-12">ORGANISMO: <strong><?php echo $solicitud['MutualProductoSolicitud']['organismo_desc'] ?></strong></div>
                            </div>
                            <div class="row mb-1 ">
                                <div class="col-12">EMPRESA/ENTIDAD: <strong><?php echo $solicitud['MutualProductoSolicitud']['turno_desc'] ?></strong></div>
                            </div>
                            <div class="row mb-1 ">
                                <div class="col-12">BANCO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_banco'] ?></strong></div>                                
                            </div>
                            <div class="row mb-1 ">
                                <div class="col-5">CBU: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_cbu'] ?></strong></div>
                                <div class="col-7">
                                    SUCURSAL: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_sucursal'] ?></strong>
                                    &nbsp;CUENTA: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_cuenta'] ?></strong>
                                </div>
                            </div>
                            <?php if (isset($solicitud['MutualProductoSolicitud']['sueldo_neto']) && !empty($solicitud['MutualProductoSolicitud']['sueldo_neto'])): ?>
                                <div class="row mb-1 ">
                                    <div class="col-12">
                                        SUELDO NETO: <strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['sueldo_neto']) ?></strong>
                                        &nbsp;DEBITOS BANCARIOS: <strong><?php echo $util->nf($solicitud['MutualProductoSolicitud']['debitos_bancarios']) ?></strong>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($solicitud['MutualProductoSolicitud']['beneficio_tarjeta_numero']) && !empty($solicitud['MutualProductoSolicitud']['beneficio_tarjeta_numero'])):?>
                                <hr>
                                <span><strong>Tarjeta de Débito</strong></span>
                                <br>
                                <div class="row mb-1 ">
                                <div class="col-12">
                                    TITULAR: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_tarjeta_titular'] ?></strong>
                                    &nbsp;NUMERO: <strong><?php echo $solicitud['MutualProductoSolicitud']['beneficio_tarjeta_numero'] ?></strong>
                                </div>
                                </div>
                                

                            <?php endif; ?>    
                        </div>
                    </div>                    
                </div>
            </div>
            <div class="row mb-1 ">
                <div class="col-6">
                    <div class="card mb-1">
                        <div class="card-header"><strong>Instrucción de Pago</strong></div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>A la Orden De</th>
                                        <th>Concepto</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $TOTAL_IPAGO = 0 ?>
                                    <?php foreach ($solicitud['MutualProductoSolicitudInstruccionPago'] as $ipago): ?>
                                        <?php $TOTAL_IPAGO += $ipago['importe'] ?>
                                        <tr>
                                            <td><?php echo $ipago['a_la_orden_de'] ?></td>
                                            <td><?php echo $ipago['concepto'] ?></td>
                                            <td class="text-right"><?php echo $util->nf($ipago['importe']) ?></td>
                                        </tr>                                    
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2"></th>
                                        <th class="text-right"><?php echo $util->nf($TOTAL_IPAGO) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>                    
                </div>
                <div class="col-6">
                    <div class="card mb-1">
                        <div class="card-header"><strong>Historial de Estados</strong></div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th><th>Usuario</th><th>Estado</th><th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($solicitud['MutualProductoSolicitud']['MutualProductoSolicitudEstado'] as $estado): ?>
                                        <tr>
                                            <td><?php echo $estado['created'] ?></td>
                                            <td><?php echo $estado['user_created'] ?></td>
                                            <td><?php echo $estado['estado_desc'] ?></td>
                                            <td><?php echo $estado['observaciones'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>                                    
                                </tbody>
                            </table>
                        </div>
                    </div>                    
                </div>
            </div>


            <div class="card mb-1">
                <div class="card-header">
                    <strong>Documentación Adjunta</strong>

                    <?php if (!empty($solicitud['MutualProductoSolicitudDocumento'])): ?>
                        &nbsp;
                        <span class="badge badge-info badge-pill"><?php echo count($solicitud['MutualProductoSolicitudDocumento']) ?></span>
                        &nbsp;
                        <a href="<?php echo $this->base . "/mutual/mutual_producto_solicitudes/download_attach_zipped/" . $solicitud['MutualProductoSolicitud']['id'] ?>" target="_blank"><i class="fas fa-download"></i></a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row mb-1 ">
                        <div class="col-6">
                            <?php
                                $array_tmp = array();
                                if (!empty($solicitud['MutualProductoSolicitudDocumento'])): ?>
                                <div class="list-group">
                                    <?php foreach ($solicitud['MutualProductoSolicitudDocumento'] as $documento):
                                        ?>
                                        <a href="<?php echo $this->base ?>/mutual/mutual_producto_solicitudes/download_attach/<?php echo $documento['MutualProductoSolicitudDocumento']['id'] ?>" target="_blank" class="list-group-item list-group-item-action">
                                            <i class="fas fa-paperclip"></i>&nbsp;
                                            <?php
                                            array_push($array_tmp, $documento['MutualProductoSolicitudDocumento']['codigo_documento']);
                                            echo $documento['GlobalDato']['concepto_1'] . " (" . $documento['MutualProductoSolicitudDocumento']['file_name'] . ")"
                                            ?>
                                        </a>
                                <?php endforeach; ?>
                                </div>
                        <?php endif; ?>
                        </div>
                            <?php if ($solicitud['MutualProductoSolicitud']['anulada'] == 0): ?>
                            <div class="col-6">
                                <?php echo $form->create(null, array('action' => 'adjuntar_documentacion/' . $solicitud['MutualProductoSolicitud']['id'] . '/1', 'type' => 'file')); ?>
                                    <span>
                                        Adjuntar Documentación - Tiene disponible para adjuntar 
                                        <span class="badge badge-success badge-pill">
                                            <?php
                                            echo (count($datos) - count($solicitud['MutualProductoSolicitudDocumento']))
                                            ?>
                                        </span> documentos.
                                    </span>
                                    <div class="form-group row border">
                                        <!--div class="form-group col-md-8">
                                            <label for="MutualProductoSolicitudArchivo"></label>
                                            <input type="file" class="form-control-file" id="MutualProductoSolicitudArchivo" name="data[MutualProductoSolicitud][archivo]" aria-describedby="fileHelp" required="">
                                        </div-->
                                        <table class="tbl_form"> 
                                            <?php

                                            foreach ($datos as $valor) {
                                                if (!in_array($valor["GlobalDato"]["id"], $array_tmp)) {
                                                    echo '<tr>';
                                                    echo '<td style="vertical-align:middle;">';
                                                    echo $valor["GlobalDato"]["concepto_1"];
                                                    echo '</td>';
                                                    echo '<td><input type="file" name="data[ProveedorPlanDocumento][' . $valor["GlobalDato"]["id"] . '|' . $valor["GlobalDato"]["concepto_1"] . ']" id=ProveedorPlanDocumento' . $valor["GlobalDato"]["concepto_1"] . '"/></td>';
                                                    echo '</tr>';
                                                }
                                            }
                                            ?>
                                        </table>
                                        <?php if( (count($datos) - count($solicitud['MutualProductoSolicitudDocumento'])) > 0):?>
                                        <div class="form-group col-md-5">
                                            <label for="btn_submit">&nbsp;</label>
                                            <button type="submit" id="btn_submit" name="btn_submit" class="form-control btn btn-secondary btn-small"><i class="fas fa-upload"></i>&nbsp;Subir Archivo (2Mb Max)</button>
                                        </div>                                     
                                        <?php endif; ?>
                                    </div>
                                <?php echo $frm->hidden('MutualProductoSolicitud.id', array('value' => $solicitud['MutualProductoSolicitud']['id'])); ?>
                                <?php $form->end() ?>
                            <?php endif; ?>
                        </div>
                    </div>    
                </div>
            </div>             
        </small>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-10"></div>
            <div class="col-2">
                <?php if ($solicitud['MutualProductoSolicitud']['anulada'] == 0): ?>
                    &nbsp;<a role="button" class="btn btn-info btn-small btn-block" href="<?php echo $this->base . "/mutual/mutual_producto_solicitudes/imprimir_credito_mutual_pdf/" . $solicitud['MutualProductoSolicitud']['id'] ?>" target="_blank"><i class="fas fa-download"></i>&nbsp;Descargar</a>
<?php endif; ?>                 
            </div>
        </div>
    </div>
</div>

<?php
// debug($solicitud)?>
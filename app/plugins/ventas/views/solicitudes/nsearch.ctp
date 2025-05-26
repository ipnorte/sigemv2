<div class="card mb-1">
    <div class="card-header"><i class="fas fa-home"></i>&nbsp;Búsqueda y Consulta de Solicitudes</div>
    <div class="card-body">
        <?php echo $frm->create(null,array('action'=>'search','id' => 'formSearchSolicitudes'))?>

            
        
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="fecha_desde">Desde</label>
                <div class="input-group date fdesde">
                    <input type="text" id="fecha_desde" name="data[MutualProductoSolicitud][fecha_desde]" class="form-control fdesde" value="<?php echo $fecha_desde ?>" placeholder="DD/MM/AAAA">&nbsp;<span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp;</span>
                </div>
                <script type="text/javascript">
                    $('.input-group.date.fdesde').datepicker({
                        clearBtn: true,
                        language: "es",
                        autoclose: true,
                        todayHighlight: true,
                        orientation: "auto bottom",
                        format: "dd/mm/yyyy",
                        assumeNearbyYear: 20,
                    });
                </script>
            </div>
            <div class="form-group col-md-2">            
                <label for="fecha_hasta">Hasta</label>
                <div class="input-group date fhasta">
                    <input type="text" id="fecha_hasta" name="data[MutualProductoSolicitud][fecha_hasta]" class="form-control fhasta" value="<?php echo $fecha_hasta ?>" placeholder="DD/MM/AAAA">&nbsp;<span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp;</span>
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
                    });
                </script>                         
            </div>
            <div class="form-group col-md-2">
                <label for="MutualProductoSolicitudEstado">Estado</label>
                <?php $estados = $this->requestAction('/config/global_datos/get_estados_solicitud'); ?>
                <select class="form-control" id="MutualProductoSolicitudEstado" name="data[MutualProductoSolicitud][estado]">
                    <option value=""></option>
                    <?php foreach($estados as $id => $descripcion):?>
                    <option value="<?php echo $id?>" <?php echo ($estado == $id ? "selected":"")?>><?php echo $descripcion?></option>
                    <?php endforeach;?>
                </select>              
            </div>
            <div class="form-group col-md-2">
                <label for="MutualProductoSolicitudDocumento">Documento</label>
                <input class="form-control solo-numero" id="MutualProductoSolicitudDocumento" name="data[MutualProductoSolicitud][documento]" value="" type="text" maxlength="8" minlength="8" autofocus="" placeholder="########" >                
            </div>
            <div class="form-group col-md-2">
                <label for="MutualProductoSolicitudNumero">Nro Solicitud</label>
                <input class="form-control solo-numero" id="MutualProductoSolicitudNumero" name="data[MutualProductoSolicitud][numero]" value="" type="text" autofocus="" placeholder="########" >                
            </div>
            <div class="form-group col-md-2">
                <label for="btn_submit">&nbsp;</label>
                <button type="submit" id="btn_submit" name="btn_submit" class="form-control btn btn-secondary btn-small"><i class="fa fa-search"></i>&nbsp;Buscar</button>
            </div>            
        </div>
        <?php echo $frm->end();?>   

    </div>
</div>
<?php if(!empty($solicitudes)):?>
<div class="card">
    <div class="card-header bg-success text-white">Resultado de la Búsqueda&nbsp;(<small>SE MUESTRAN LAS PRIMERAS 50</small>)</div>
    <div class="card-body">
        
        <table class="table table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <!--<th>Producto</th>-->
                    <th>Documento</th>
                    <th>Solicitante</th>
                    <th>Solicitado</th>
                    <th>Cuotas</th>
                    <th>Importe</th>
                </tr>
                
            </thead>
            <tbody>
            <?php foreach($solicitudes as $solicitud):?>
            <tr class="<?php echo ($solicitud['MutualProductoSolicitud']['anulada'] == 1 ? "text-danger" : "")?>">
                <td>
                    <?php if($solicitud['MutualProductoSolicitud']['anulada'] == 0):?>
                    <a href="<?php echo $this->base . "/mutual/mutual_producto_solicitudes/imprimir_credito_mutual_pdf/" . $solicitud['MutualProductoSolicitud']['id']?>" target="_blank"><i class="fas fa-download"></i></a>
                    <?php endif;?>
                    <a href="<?php echo $this->base . "/ventas/solicitudes/ficha/" . $solicitud['MutualProductoSolicitud']['id']?>"><i class="fas fa-eye"></i></a>            
                <td style="font-weight: bold;"><?php echo $solicitud['MutualProductoSolicitud']['id']?></td>
                <td><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?></td>
                <td style="font-weight: bold;"><?php echo $solicitud['EstadoSolicitud']['concepto_1']?></td>
                <!--<td><?php // echo $solicitud['Proveedor']['razon_social']." - ".$solicitud['TipoProducto']['concepto_1']?></td>-->
                <td><?php echo $solicitud['TipoDocumento']['concepto_1']." - ".$solicitud['Persona']['documento']?></td>
                <td><?php echo $solicitud['Persona']['apellido'].", ".$solicitud['Persona']['nombre']?></td>
                <td style="text-align: right;"><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_solicitado'])?></td>
                <td style="text-align: center;"><?php echo $solicitud['MutualProductoSolicitud']['cuotas']?></td>
                <td style="text-align: right;"><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_cuota'])?></td>
            </tr>
            <?php    endforeach;?> 
            </tbody>
        </table>

    </div>
</div>
<?php elseif(!empty($this->data)):?>
        <div class="alert alert-dismissible alert-secondary">
            NO EXISTEN SOLICITUDES PARA EL CRITERIO DE BUSQUEDA INDICADO...
        </div>
<?php endif; ?>



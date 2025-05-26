<div class="card mb-1">
    <div class="card-header"><i class="fas fa-print"></i>&nbsp;Listado de Operaciones</div>
    <div class="card-body">
        <?php echo $frm->create(null,array('action'=>'listado','id' => 'formSearchSolicitudes'))?>
        <div class="form-row">
        <div class="form-group col-md-2">
                <label for="fecha_desde">Desde</label>
                <div class="input-group date fdesde">
                    <input type="text" id="fecha_desde" name="data[MutualProductoSolicitud][fecha_desde]" class="form-control fdesde" value="<?php echo date('d/m/Y', strtotime($fecha_desde)) ?>" placeholder="DD/MM/AAAA">&nbsp;<span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp;</span>
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
                    <input type="text" id="fecha_hasta" name="data[MutualProductoSolicitud][fecha_hasta]" class="form-control fhasta" value="<?php echo date('d/m/Y', strtotime($fecha_hasta)) ?>" placeholder="DD/MM/AAAA">&nbsp;<span class="input-group-addon"><i class="fa fa-calendar"></i>&nbsp;</span>
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
                <label for="btn_submit">&nbsp;</label>
                <button type="submit" id="btn_submit" name="btn_submit" class="form-control btn btn-primary btn-small"><i class="fas fa-print"></i>&nbsp;Generar Proceso</button>
            </div>             
        </div>
        <?php echo $frm->end();?>
    </div>
</div>
<?php if($show_asincrono == 1):?>
	<?php 
	echo $this->renderElement('solicitudes/proceso_asincrono',array(
            'plugin' => 'ventas',
            'process' => 'ventas_listado_solicitudes',
            'accion' => '.ventas.solicitudes.listado.PDF',
            'target' => '_blank',
            'btn_label' => 'Ver Listado',
            'titulo' => "LISTADO DE SOLICITUDES",
            'subtitulo' => "Emitidas desde " . $util->armaFecha($fecha_desde) . " hasta " . $util->armaFecha($fecha_hasta),
            'p1' => (isset($this->data['MutualProductoSolicitud']['vendedor_id']) ? $this->data['MutualProductoSolicitud']['vendedor_id'] : NULL),
            'p2' => $this->data['MutualProductoSolicitud']['estado'],
            'p3' => $fecha_desde,
            'p4' => $fecha_hasta,
            'p5' => Configure::read('APLICACION.tipo_orden_dto_credito'),
            'p6' => NULL,        
            'p7' => $periodo_corte,
            'p8' => $vendedor_id,
	));
	?>
<?php endif?>

<?php if(empty($persona)):?>
    <div class="card mb-1">
        <div class="card-header"><i class="fas fa-address-book"></i>&nbsp;Estado de Deuda</div>
        <div class="card-body">
            <?php // if(empty($personas)):?>
            <?php echo $frm->create(null,array('action'=>'estado_cuenta','id' => 'formSearchSolicitudes'))?>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <input class="form-control solo-numero" id="MutualProductoSolicitudDocumento" name="data[Persona][documento]" value="<?php echo (isset($this->data['Persona']['documento']) ? $this->data['Persona']['documento'] : "")?>" type="text" maxlength="8" minlength="8" autofocus="" placeholder="Nro Documento" >                
                </div> 
                <div class="form-group col-md-3">
                    <input type="text" class="form-control" name="data[Persona][apellido]" value="<?php echo (isset($this->data['Persona']['apellido']) ? $this->data['Persona']['apellido'] : "")?>" id="Nombre" placeholder="Apellido" maxlength="20" autofocus="">
                </div>
                <div class="form-group col-md-3">
                    <input type="text" class="form-control" name="data[Persona][nombre]" value="<?php echo (isset($this->data['Persona']['nombre']) ? $this->data['Persona']['nombre'] : "")?>" id="Nombre" placeholder="Nombre" maxlength="20" autofocus="">
                </div>
                <div class="form-group col-md-2">
                    <button type="submit" name="btn_submit" class="btn btn-secondary btn-small"><i class="fa fa-search"></i>&nbsp;Buscar</button>
                </div>            
            </div>
            <?php echo $frm->end();?>  
            <?php // else:?> 
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th style="white-space: nowrap;width: 1%;"></th>
                        <th style="white-space: nowrap;width: 1%;">Documento</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($personas) && !empty($personas)):?>
                    <?php foreach ($personas as $persona):?>
                    <tr>
                        <td><a href="<?php echo $this->base . "/ventas/solicitudes/estado_cuenta/" . $persona['Persona']['id']?>"><i class="fas fa-calculator"></i></a></td>
                        <td><?php echo $persona['Persona']['documento']?></td>
                        <td><?php echo $persona['Persona']['apellido']?>,&nbsp;<?php echo $persona['Persona']['nombre']?></td>
                    </tr>
                    <?php endforeach;?>
                    <?php endif;?>
                </tbody>
            </table>            
            
            <?php // endif;?> 
            
        </div>
    </div>

<?php else:?>

<div class="card">
    <div class="card-header"><strong>Estado de Deuda</strong> | <strong><?php echo $persona['Persona']['tdoc_ndoc']?> - <?php echo $persona['Persona']['apenom']?></strong></div>
    <div class="card-body">
        <small>
            <div class="row mb-1 ">
                <div class="col-12">DOMICILIO: <strong><?php echo $persona['Persona']['domicilio']?></strong></div>
            </div>
            <div class="row mb-1 ">
                <div class="col-12">DATOS COMPLEMENTARIOS: <strong><?php echo $persona['Persona']['datos_complementarios']?></strong></div>
            </div>
            <?php if(!empty($persona['Persona']['socio_nro'])):?>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Socio</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Fecha de Alta</th>
                        <th>Ultima Calificación</th>
                        <th>Fecha Calificación</th>
                        <th>Calificaciones Anteriores</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $persona['Persona']['socio_nro']?></td>
                        <td><?php echo $persona['Persona']['socio_categoria']?></td>
                        <td><?php echo $persona['Persona']['socio_status']?></td>
                        <td><?php echo $util->armaFecha($persona['Persona']['socio_fecha_alta'])?></td>
                        <td><?php echo $persona['Persona']['socio_ultima_calificacion']?></td>
                        <td><?php echo $util->armaFecha($persona['Persona']['socio_fecha_ultima_calificacion'])?></td>
                        <td><?php echo $persona['Persona']['socio_resumen_calificacion']?></td>
                    </tr>
                </tbody>
            </table>
            <?php endif;?>
            <?php if(!empty($solicitudes)):?>            
            <div class="card mb-1">
                <div class="card-header bg-warning text-white"><strong>Operaciones Pendientes de Aprobación</strong></div>
                <div class="card-body">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Producto</th>
                                <th>Cuotas</th>
                                <th>Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $TOTAL_MENSUAL = $TOTAL = 0;?>
                            <?php foreach($solicitudes as $solicitud):?>
                            <tr>
                                <td><?php echo $solicitud['MutualProductoSolicitud']['id']?></td>
                                <td><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?></td>
                                <td><?php echo $solicitud['EstadoSolicitud']['concepto_1']?></td>
                                <td><?php echo $solicitud['TipoProducto']['concepto_1']?></td>
                                <td class="text-center" style="font-weight: bold;"><?php echo $solicitud['MutualProductoSolicitud']['cuotas']?></td>
                                <td class="text-right" style="font-weight: bold;"><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_cuota'])?></td>
                            </tr>
                            <?php 
                            $TOTAL_MENSUAL += $solicitud['MutualProductoSolicitud']['importe_cuota'];
                            ?>
                            <?php    endforeach;?>                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">TOTAL PENDIENTE DE APROBAR</th>
                                <th class="text-right"><strong><?php echo $util->nf($TOTAL_MENSUAL)?></strong></th>
                            </tr>                            
                        </tfoot>    
                    </table>
                </div>
            </div>
            <?php endif;?>
			<?php if(!empty($persona['Persona']['id'])):?>
    		<div class="row mb-1 ">
    			<div class="col-12">
    				<a href="<?php echo $this->base . "/ventas/solicitudes/liquidacion/" . $persona['Persona']['id']?>" class="btn btn-success">
    					<i class="far fa-calendar-alt"></i>&nbsp;Liquidaci&oacute;n de Deuda
    				</a>
    			</div>
    		</div>
			<?php endif;?>
            <div class="card mb-1">
                <div class="card-header bg-secondary">
                	<strong>Estado de Deuda a la Fecha (S.E.U.O.)</strong>
                </div>
                <div class="card-body">
                
                	
                	
                    <?php if(!empty($cuotas)):?>
                    <table class="table table-sm" style="font-size: 100%;">
                        <thead>
                            <tr>
                                <th>ORD.DTO.</th>
                                <th>ORGANISMO</th>
                                <th>TIPO / NUMERO</th>
                                <th>COD - NRO</th>
                                <th>PRODUCTO</th>
                                <th>SOLICITADO</th>
                                <th>CUOTA</th>
                                <th>CONCEPTO</th>
                                <th>VTO / PAGO</th>
                                <th></th>
                                <th>SIT</th>
                                <th>IMPORTE</th>
                                <th>PAGADO</th>
                                <th>PREIMP.</th>
                                <th>SALDO</th>
                                <th></th>
                            </tr>                            
                        </thead>
                        <tbody>
                            <?php $periodo = null?>
                            <?php $primero = true;?>
                            <?php $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = $ACUM_PENDIENTE = $ACUM_PENDIENTE_TOTAL = 0?>
                            <?php foreach($cuotas as $cuota):?> 
                                <?php if($cuota['tipo_registro'] == 'SALDO_ANTERIOR'):?>
                                    <?php $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = $cuota['saldo'];?>                                 
                                <?php endif;?> 
                                <?php if($periodo != $cuota['periodo']):?>
                                    <?php $periodo = $cuota['periodo'];?>
                                    <?php if($primero):?>
                                        <?php $primero = false;?>
                                    <?php else:?>

                                        <tr>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="11" align="right"><strong>TOTAL PERIODO</strong></td>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;color: green;" align="right"><strong><?php echo number_format($ACUM_PENDIENTE,2)?></strong></td>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                                        </tr>
                                        <tr>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="14" align="right"><strong>SALDO ACUMULADO A <?php echo $util->periodo($periodo_actual,true)?></strong></td>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
                                            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                                        </tr>

                                    <?php endif;?>

                                    <tr>
                                        <th colspan="16" style="font-size:13px;background-color: #666666;border:0; color: #FFFFFF;"><?php echo $util->periodo($cuota['periodo'],true)?></th>
                                    </tr> 
                                                <?php if($ACU_SALDO_CUOTA_ACUM != 0):?>
                                                        <tr>
                                                                <td style="border-bottom: 1px solid #D8DBD4;color:red;" colspan="14" align="right"><strong>SALDO ANTERIOR</strong></td>
                                                                <td style="border-bottom: 1px solid #D8DBD4;color:red;background-color: #FBEAEA;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
                                                                <td style="border-bottom: 1px solid #D8DBD4;color:red;" align="right"><?php // echo $controles->btnModalBox(array('title' => 'ATRASO A '.$util->periodo($cuota['periodo'],true),'url' => '/mutual/orden_descuento_cuotas/ver_atraso/'.$socio['Socio']['id'].'/'.$cuota['periodo'].'/'.$proveedor_id.'/'.$codigo_organismo,'h' => 500, 'w' => 900))?></td>
                                                        </tr>				

                                                <?php endif;?>    

                                    <?php 
                                    $periodo_actual = $cuota['periodo'];
                                    $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = $ACUM_PENDIENTE = 0;
                                    ?>    
                                <?php endif;?> 
                                <?php if($cuota['tipo_registro'] == 'CUOTA'):?>       
                                   <?php $ACU_IMPO_CUOTA += $cuota['importe'];?>
                                   <?php $ACU_SALDO_CUOTA += $cuota['saldo'];?>
                                   <?php $ACU_SALDO_CUOTA_ACUM += $cuota['saldo'];?>   
                                   <?php $ACUM_PENDIENTE += $cuota['pendiente'];?> 
                                   <?php $ACUM_PENDIENTE_TOTAL += $cuota['pendiente'];?>                   

                                   <tr class="<?php echo $cuota['estado']?>">
                                       <!-- <td align="center"><?php // echo $cuota['orden_descuento_id']?></td> -->
                                       
                                        <td align="center">
                                            <a href="#" 
                                               class="ver-orden-detalle" 
                                               data-toggle="modal" 
                                               data-target="#modalOrdenDetalle" 
                                               data-url="<?php echo $this->base . '/ventas/solicitudes/detalle_orden_descuento/' . $cuota['orden_descuento_id']; ?>">
                                                <?php echo $cuota['orden_descuento_id']?>
                                            </a>
                                        </td>
                                       
                                       
                                       <td><?php echo $cuota['organismo']?></td>
                                       <td nowrap="nowrap"><?php echo $cuota['tipo_numero'];?></td>
                                       <td align="center"><?php echo $cuota['cod_nro']?></td>
                                       <td><?php echo $cuota['producto']?></td> 
                                       <td align="right"><?php echo ($cuota['importe_solicitado']!=0 ? number_format($cuota['importe_solicitado'],2) : "") ?></td>
                                       <td align="center">
                                       

                                            
                                           
                                            <a href="#" 
                                               class="ver-cuota-detalle" 
                                               data-toggle="modal" 
                                               data-target="#modalCuotaDetalle" 
                                               data-url="<?php echo $this->base . '/ventas/solicitudes/detalle_cuota/' . $cuota['id']; ?>">
                                                <?php echo $cuota['cuota']?>
                                            </a>                                           
                                       
                                       
                                       </td>
                                       <td><?php echo $cuota['tipo_cuota']?></td>
                                       <td align="center"><?php echo $util->armaFecha($cuota['vencimiento'])?></td>
                                       <td><?php echo $cuota['estado']?></td>
                                       <td><?php echo $cuota['situacion_cuota']?></td>
                                       <td align="right"><?php echo ($cuota['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['importe'],2).'</span>' : number_format($cuota['importe'],2)) ?></td>
                                       <td align="right"><?php echo number_format($cuota['pagado'],2)?></td>
                                       <td align="right" style="color:green;"><?php if($cuota['pendiente'] != 0) echo number_format($cuota['pendiente'],2)?></td>
                                       <td align="right"><?php echo ($cuota['saldo'] == 0 ? '<span style="color:green;">'.number_format($cuota['saldo'],2).'</span>' : number_format($cuota['saldo'],2)) ?></td>
                                       <td align="center"><?php // echo $controles->btnModalBox(array('title' => 'DETALLE CUOTA','url' => '/mutual/orden_descuento_cuotas/view/'.$cuota['id'],'h' => 450, 'w' => 750))?></td>
                                   </tr>
                               <?php endif;?>
                               <?php if($cuota['tipo_registro'] == 'PAGO'):?>
                               <?php $ACU_PAGO_CUOTA += $cuota['pagado'];?>
                               <?php endif;?>                                                          
                            
                            <?php endforeach;?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="11" align="right"><strong>TOTAL PERIODO</strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;color:green;" align="right"><strong><?php echo number_format($ACUM_PENDIENTE,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                            </tr>
                            <tr>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="14" align="right"><strong>SALDO</strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                            </tr>
                            <?php if(!empty($reintegros)):?> 
                            <tr>
                                <td colspan="16" style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;"><strong>Reintegros Pendientes de Abonar / Compensar (S.E.U.O.)</strong></td>
                            </tr>
                                <?php 
                                $TOTAL_REINTEGRO = $TOTAL_PAGO_REINTEGRO = $TOTAL_SALDO_REINTEGRO = 0;
                                ?>
                                <?php foreach($reintegros as $reintegro):?>
                                <?php 
                                $TOTAL_REINTEGRO += $reintegro['SocioReintegro']['importe_reintegro'];
                                $TOTAL_PAGO_REINTEGRO += $reintegro['SocioReintegro']['pagos'];
                                $TOTAL_SALDO_REINTEGRO -= $reintegro['SocioReintegro']['saldo'];
                                ?>
                               <tr>
                                   <td>#<?php echo $reintegro['SocioReintegro']['id']?></td>
                                   <td><?php echo $util->armaFecha($reintegro['SocioReintegro']['created'])?></td>
                                   <td><?php echo $reintegro['SocioReintegro']['tipo']?></td>
                                   <td colspan="7"><?php echo $reintegro['SocioReintegro']['liquidacion_str']?></td>
                                   
                                   <td></td>
                                   <td align="right"><?php echo number_format($reintegro['SocioReintegro']['importe_reintegro'],2)?></td>
                                   <td align="right"><?php echo number_format($reintegro['SocioReintegro']['pagos'],2)?></td>
                                   <td></td>
                                   <td align="right"><?php echo number_format($reintegro['SocioReintegro']['saldo'] * -1,2)?></td>
                                   <td></td>
                               </tr>
                               <?php endforeach;?>
                               <tr>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="11" align="right"><strong>TOTAL REINTEGROS</strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($TOTAL_REINTEGRO,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($TOTAL_PAGO_REINTEGRO,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;color:green;" align="right"></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($TOTAL_SALDO_REINTEGRO,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                                   
                               </tr>
                            <tr>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="14" align="right"><strong>SALDO A CONCILIAR</strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM + $TOTAL_SALDO_REINTEGRO,2)?></strong></td>
                                <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
                            </tr>                               
                            <?php endif;?>                             
                        </tfoot>
                    </table>
                    <?php else:?>
                    <div class="alert alert-dismissible alert-light">
                        NO EXISTEN REGISTROS DE CUOTAS ADEUDADAS
                    </div>
                    
                    <?php endif;?>                    
                </div>
            </div>

        </small>
    </div>
</div>    


<div class="modal fade" id="modalOrdenDetalle" tabindex="-1" role="dialog" aria-labelledby="modalOrdenDetalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header text-white bg-primary">
        <h5 class="modal-title" id="modalOrdenDetalleLabel">Detalle de Orden de Descuento</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="modal-orden-content">Cargando...</div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = $('#modalOrdenDetalle');
    const content = $('#modal-orden-content');

    $(document).on('click', '.ver-orden-detalle', function (e) {
        e.preventDefault();
        const url = $(this).data('url');
        content.html('Cargando...');
        $.get(url, function (html) {
            content.html(html);
        }).fail(function () {
            content.html('<div class="alert alert-danger">Error cargando detalle.</div>');
        });
    });

    modal.on('hidden.bs.modal', function () {
        content.html(''); // Limpiar contenido al cerrar
    });
});
</script>

<div class="modal fade" id="modalCuotaDetalle" tabindex="-1" role="dialog" aria-labelledby="modalCuotaDetalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white bg-info">
        <h5 class="modal-title" id="modalCuotaDetalleLabel">Detalle Cuota</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="modal-cuota-content">Cargando...</div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = $('#modalCuotaDetalle');
    const content = $('#modal-cuota-content');

    $(document).on('click', '.ver-cuota-detalle', function (e) {
        e.preventDefault();
        const url = $(this).data('url');
        content.html('Cargando...');
        $.get(url, function (html) {
            content.html(html);
        }).fail(function () {
            content.html('<div class="alert alert-danger">Error cargando detalle.</div>');
        });
    });

    modal.on('hidden.bs.modal', function () {
        content.html(''); // Limpiar contenido al cerrar
    });
});
</script>


<?php endif;?> 


<?php // debug($cuotas);?>
 




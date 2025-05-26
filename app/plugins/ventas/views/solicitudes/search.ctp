<?php echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>BUSQUEDA Y CONSULTA DE SOLICITUDES</h3>
<?php echo $frm->create(null,array('action'=>'search','id' => 'formSearchSolicitudes'))?>
<div class="areaDatoForm">
    <table class="tbl_form">
        <tr>
            <td>EMITIDAS ENTRE</td>
            <td><?php echo $frm->calendar('MutualProductoSolicitud.fecha_desde',NULL,$fecha_desde,date("Y")-15,date("Y") + 1)?></td>
            <td>Y</td>
            <td><?php echo $frm->calendar('MutualProductoSolicitud.fecha_hasta',NULL,$fecha_hasta,date("Y")-15,date("Y") + 1)?></td>
        </tr>
        <tr>
            <td>ESTADO</td>
            <td colspan="3">
			<?php echo $this->renderElement('global_datos/combo_global',array(
																			'plugin'=>'config',
																			'metodo' => "get_estados_solicitud",
																			'model' => 'MutualProductoSolicitud.estado',
																			'empty' => true,
																			'selected' => $this->data['MutualProductoSolicitud']['estado']
			))?>	                
            </td>
        </tr>
        <tr>
            <td>DNI SOLICITANTE</td><td colspan="3"><?php echo $frm->number('MutualProductoSolicitud.documento',array('maxlength' => 8,'size' => 8))?></td>
        </tr>
        <tr>
            <td>NUMERO DE SOLICITUD</td><td colspan="3"><?php echo $frm->number('MutualProductoSolicitud.numero',array('maxlength' => 10,'size' => 10))?></td>
            
        </tr>
    </table>
    <?php echo $frm->submit("BUSCAR SOLICITUDES",array('id' => 'btn_searchSolicitudes'))?>
</div>
<?php echo $frm->end();?>

<?php if(!empty($solicitudes)):?>
<?php if(count($solicitudes) == 50):?>
<div class="notices">
    SE MUESTRAN LAS PRIMERAS 50
</div>
<?php endif;?>
<table>
    <tr>
        <th></th>
        <th>NRO</th>
        <th>FECHA</th>
        <th>ESTADO</th>
        <th>PRODUCTO</th>
        <th>DOCUMENTO</th>
        <th>SOLICITANTE</th>
        <th>SOLICITADO</th>
        <th>CUOTAS</th>
        <th>IMPORTE</th>
    </tr>
    <?php foreach($solicitudes as $solicitud):?>
    <tr class="<?php echo ($solicitud['MutualProductoSolicitud']['anulada'] == 1 ? " disable "  : ($solicitud['MutualProductoSolicitud']['estado'] == 'MUTUESTA0001' ? " amarillo " : ($solicitud['MutualProductoSolicitud']['estado'] == 'MUTUESTA0014' ? " verde " : ($solicitud['MutualProductoSolicitud']['estado'] == 'MUTUESTA0002' ? " activo_1 " : ""))))?>">
        <td><?php echo $controles->botonGenerico('/ventas/solicitudes/ficha/'.$solicitud['MutualProductoSolicitud']['id'],'controles/report.png',null,array())?></td>
        <td><?php echo $solicitud['MutualProductoSolicitud']['id']?></td>
        <td><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?></td>
        <td><?php echo $solicitud['EstadoSolicitud']['concepto_1']?></td>
        <td><?php echo $solicitud['Proveedor']['razon_social']." - ".$solicitud['TipoProducto']['concepto_1']?></td>
        <td><?php echo $solicitud['TipoDocumento']['concepto_1']." - ".$solicitud['Persona']['documento']?></td>
        <td><?php echo $solicitud['Persona']['apellido'].", ".$solicitud['Persona']['nombre']?></td>
        <td style="text-align: right;"><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_solicitado'])?></td>
        <td style="text-align: center;"><?php echo $solicitud['MutualProductoSolicitud']['cuotas']?></td>
        <td style="text-align: right;"><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_cuota'])?></td>
    </tr>
    <?php    endforeach;?>
</table>

    <?php // debug($solicitudes)?>
<?php elseif(!empty($this->data)):?>

<div class="notices">
    NO EXISTEN SOLICITUDES PARA EL CRITERIO DE BUSQUEDA INDICADO...
</div>

<?php endif; ?>

<?php
$solicitudesPendientes = $this->requestAction('/mutual/mutual_producto_solicitudes/get_operaciones_pendientes_by_persona/'.$persona_id);
?>

<?php if(!empty($solicitudesPendientes)):?>
<div class="notices_error2" style="width: 100%;">
    <strong>ATENCION!</strong> El solicitante posee OTRAS OPERACIONES PENDIENTES DE APROBACION.
</div>
    <table style="margin-bottom: 5px;">
    <tr>
        <th>NRO</th>
        <th>FECHA</th>
        <th>ESTADO</th>
        <th>PRODUCTO</th>
        <th>CUOTAS</th>
        <th>IMPORTE</th>
    </tr>
    <?php $TOTAL_MENSUAL = $TOTAL = 0;?>
    <?php foreach($solicitudesPendientes as $solicitud):?>
    <tr class="<?php echo ($solicitud['MutualProductoSolicitud']['anulada'] == 1 ? " disable "  : ($solicitud['MutualProductoSolicitud']['estado'] == 'MUTUESTA0001' ? " amarillo " : ($solicitud['MutualProductoSolicitud']['estado'] == 'MUTUESTA0014' ? " verde " : ($solicitud['MutualProductoSolicitud']['estado'] == 'MUTUESTA0002' ? " activo_1 " : ""))))?>">
        <td><?php echo $solicitud['MutualProductoSolicitud']['id']?></td>
        <td><?php echo $util->armaFecha($solicitud['MutualProductoSolicitud']['fecha'])?></td>
        <td><?php echo $solicitud['EstadoSolicitud']['concepto_1']?></td>
        <td><?php echo $solicitud['TipoProducto']['concepto_1']?></td>
        <td style="text-align: center;font-weight: bold;"><?php echo $solicitud['MutualProductoSolicitud']['cuotas']?></td>
        <td style="text-align: right;font-weight: bold;"><?php echo $util->nf($solicitud['MutualProductoSolicitud']['importe_cuota'])?></td>
    </tr>
    <?php 
    $TOTAL_MENSUAL += $solicitud['MutualProductoSolicitud']['importe_cuota'];
    ?>
    <?php    endforeach;?>
    <tr class="totales">
        <th colspan="4">TOTAL PENDIENTE DE APROBAR</th>
        <th></th>
        <th><?php echo $util->nf($TOTAL_MENSUAL)?></th>
    </tr>
</table>
    <p></p>
    <hr/>
<?php endif;?>

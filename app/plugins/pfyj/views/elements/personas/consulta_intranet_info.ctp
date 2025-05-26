<h3>INFORME DE DATOS EN LA INTRANET</h3>

<?php 

$INI_FILE = $_SESSION['MUTUAL_INI'];
$MOD_BCRA = (isset($INI_FILE['general']['modulo_bcra']) && $INI_FILE['general']['modulo_bcra'] != 0 ? TRUE : FALSE);    

?>

<?php if(!empty($cuitCuil) && $MOD_BCRA):?>
<div style="background-color: #666666;color: white;padding: 5px;margin-top: 10px;">
    <h1>CONSULTA BANCO CENTRAL</h1>    
</div>
<?php echo $this->renderElement('personas/consulta_bcra',array('cuit'=> $cuitCuil,'plugin' => 'pfyj'))?>
<?php endif?>


<?php if(!empty($informe)):?>

<?php foreach ($informe as $key => $value):?>
    
<div style="background-color: #666666;color: white;padding: 5px;margin-top: 10px;">
    <h1><?php echo $value['CLIENTE']?></h1>
    
</div>
    <?php if(!empty($value['RESULTADO'])):?>
    <h3>Datos Personales</h3>
    <div class="">
        
        <strong><?php echo $value['RESULTADO']->tdoc_ndoc_apenom?></strong>
        <br/>
        <?php // echo $value['RESULTADO']->domicilio?>
        <!--<br/>-->
        <?php echo $value['RESULTADO']->datos_complementarios?>        
        <br/>
        <!--
        Tel.Fijo: <?php // echo $value['RESULTADO']->telefono_fijo?> | Movil: <?php // echo $value['RESULTADO']->telefono_movil?>
        | E-mail: <?php // echo $value['RESULTADO']->e_mail?>
        <br/>
        Tel.Mensajes: <?php // echo $value['RESULTADO']->telefono_referencia?>
        -->
    </div>
    <h3>Resúmen de Calificaciones</h3>
    <?php if(!empty($value['RESULTADO']->socio_nro)):?>
    
    
    
    <div class="">    
        
        Status Actual: <strong><?php echo $value['RESULTADO']->socio_status?></strong>
        | Ultima: <strong><?php echo $value['RESULTADO']->socio_ultima_calificacion?></strong> (<?php echo $value['RESULTADO']->socio_fecha_ultima_calificacion?>)
        <br/>
        Historial: <?php echo $value['RESULTADO']->socio_resumen_calificacion?>
    </div>
        <?php else:?>
        <div class="notices_ok">SIN INFORMACION</div>    
    <?php endif;?>
    <h3>Antecedentes de STOP DEBIT</h3>
    <div class="">
        
        <?php if(!empty($value['RESULTADO']->socio_historico_stop)):?>
        <!--<div class="notices_error">La persona cuenta con antecedentes de STOP DEBIT</div>-->
        <table>
            <tr>
                <th>Banco</th>
                <th>Fecha</th>
                <th>Importe</th>
            </tr>
            <?php foreach ($value['RESULTADO']->socio_historico_stop as $stop):?>
            <?php // debug($value)?>
            <tr>
                <td><?php echo $stop->banco_nombre?></td>
                <td><?php echo $stop->fecha_debito?></td>
                <td style="text-align: right;"><?php echo $util->nf($stop->importe_debitado)?></td>
            </tr>
            
            <?php endforeach;?>
        </table>    
        <?php else:?>
        <div class="notices_ok">SIN REGISTRO DE STOP DEBIT</div>
        <?php endif;?>
    </div>
    <div>
    <h3>Antecedentes de Reversos</h3>  
    <?php if(!empty($value['RESULTADO']->socio_historico_reversos)):?>
    <table>
        <tr>
            <th>Periodo</th><th>Importe</th>
        </tr>
        <?php foreach ($value['RESULTADO']->socio_historico_reversos as $periodo => $reverso):?>
        <tr>
            <td><?php echo $util->periodo($periodo)?></td>
            <td style="text-align: right;"><?php echo $util->nf($reverso)?></td>
        </tr>
        <?php endforeach;?>
    </table>
    <?php else:?>
    <div class="notices_ok">SIN REGISTRO DE REVERSOS</div>
    <?php endif;?>    
    </div>
    <h3>Operaciones pendientes de APROBACION</h3>
    <div class="">    
        
        <?php if(!empty($value['RESULTADO']->operaciones_pendientes_aprobar)):?>
        <table>
            <tr>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Concepto</th>
                <th>Cuotas</th>
                <th>Importe</th>
            </tr>
            <?php foreach ($value['RESULTADO']->operaciones_pendientes_aprobar as $operacion):?>
            <tr>
                <td><?php echo $operacion->MutualProductoSolicitud->fecha;?></td>
                <td><?php echo $operacion->EstadoSolicitud->concepto_1;?></td>
                <td><?php echo $operacion->TipoProducto->concepto_1;?></td>
                <td style="text-align: center;"><?php echo $operacion->MutualProductoSolicitud->cuotas;?></td>
                <td style="text-align: right;"><?php echo $util->nf($operacion->MutualProductoSolicitud->importe_cuota);?></td>
            </tr>
            <?php endforeach;?>
        </table>
        <?php else:?>
        <div class="notices_ok">SIN OPERACIONES PENDIENTES DE APROBAR</div>
        <?php endif;?> 
        
    </div>
    <h3>Resúmen de Deuda a la fecha (S.E.U.O)</h3>
    <?php if(!empty($value['RESULTADO']->socio_nro)):?>
    <div class="">
        Vencida: <strong><?php echo $util->nf($value['RESULTADO']->socio_deuda_total_vencida);?></strong>
        | A <?php echo $util->periodo(date('Ym'))?> : <strong><?php echo $util->nf($value['RESULTADO']->socio_deuda_total_periodo);?></strong>
        | A Vencer: <strong><?php echo $util->nf($value['RESULTADO']->socio_deuda_total_avencer);?></strong>
    </div>
    
    <?php else:?>
    <div class="notices_ok">SIN INFORMACION</div>
    <?php endif;?>    
    
<?php else:?>
<div class="notices_ok">SIN INFORMACION</div>
<?php endif;?>
<hr/>
    <?php // debug($value)?>
    
<?php endforeach;?>


<?php endif; ?>


<?php // debug($informe)?>


<?php echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>ESTADO DE DEUDA</h3>
<hr/>
<?php if(empty($persona)):?>
<?php echo $this->renderElement('personas/search',array(
    'plugin' => 'pfyj',	
    'accion' => 'estado_cuenta',
    'nro_socio' => true,
    'busquedaAvanzada' => FALSE, 
    'showOnLoad' => (isset($this->data['Persona']['busquedaAvanzada']) && $this->data['Persona']['busquedaAvanzada'] ? true : false),
    'tipo_busqueda_avanzada' => 'by_beneficio',
														
));
echo $this->renderElement(
    'personas/grilla_personas_paginada2',
    array(
    'plugin' => 'pfyj',
    'accion' => 'estado_cuenta/',
        'limit' => 3,
    'personas' => $personas,    
));

?>

<?php else:?>


<style>
    .field{
        width: auto;float: left;margin:5px 10px 5px 0px;
    }
</style>
<div class="areaDatoForm">
    <h3>DATOS PERSONALES</h3>
    <hr>
    <div class="field">TIPO Y NRO DE DOCUMENTO: <strong><?php echo $persona['Persona']['tdoc_ndoc']?></strong></div>
    <div class="field">NOMBRE: <strong><?php echo $persona['Persona']['apenom']?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">DOMICILIO: <strong><?php echo $persona['Persona']['domicilio']?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">DATOS COMPLEMENTARIOS: <strong><?php echo $persona['Persona']['datos_complementarios']?></strong></div>
    <div style="clear: both;"></div>
    <?php if(!empty($persona['Persona']['socio_nro'])):?>
    <h4>Persona registrada como Socio</h4>
    <hr/>
    <div class="areaDatoForm2">
        <table class="tbl_form">
            <tr>
                <td>Socio Nro.</td>
                <td><strong><?php echo $persona['Persona']['socio_nro']?></strong></td>
                <td>Categoría</td>
                <td><strong><?php echo $persona['Persona']['socio_categoria']?></strong></td>
                <td>Estado</td>
                <td><strong><?php echo $persona['Persona']['socio_status']?></strong></td> 
                <td>Alta</td>
                <td><strong><?php echo $util->armaFecha($persona['Persona']['socio_fecha_alta'])?></strong></td>                  
                <td>Ultima Calificación</td>
                <td><strong><?php echo $persona['Persona']['socio_ultima_calificacion']?></strong></td>  
                <td>Fecha Ultima Calificación</td>
                <td><strong><?php echo $util->armaFecha($persona['Persona']['socio_fecha_ultima_calificacion'])?></strong></td>                  
            </tr>
            <tr>
                <td colspan="3">Calificaciones Anteriores</td>
                <td colspan="9"><strong><?php echo $persona['Persona']['socio_resumen_calificacion']?></strong>
                    &nbsp;<?php echo $controles->btnModalBox(array('title' => 'ULTIMO STOCK DE DEUDA','img'=> 'calendar_2.png','texto' => 'Scoring de Deuda','url' => '/mutual/liquidacion_socios/cargar_scoring_by_socio/'.$persona['Persona']['socio_nro'],'h' => 450, 'w' => 750))?></td>
            </tr>
        </table>
    </div>
    <?php endif;?>     
</div>
<?php if(!empty($solicitudes)):?>
<h3>OPERACIONES PENDIENTES DE APROBACION</h3>

<table>
    <tr>
        <th>NRO</th>
        <th>FECHA</th>
        <th>ESTADO</th>
        <th>PRODUCTO</th>
        <th>CUOTAS</th>
        <th>IMPORTE</th>
    </tr>
    <?php $TOTAL_MENSUAL = $TOTAL = 0;?>
    <?php foreach($solicitudes as $solicitud):?>
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

<?php endif;?>

<?php if(!empty($cuotas)):?>
<h3>DETALLE DE DEUDA A LA FECHA</h3>
	<table style="width: 100%;" class="tbl_grilla">
	
	
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
        
        <?php $periodo = null?>
        <?php $primero = true;?>
        <?php $ACU_IMPO_CUOTA = $ACU_PAGO_CUOTA = $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = $ACUM_PENDIENTE = $ACUM_PENDIENTE_TOTAL = 0?>
        <?php foreach($cuotas as $cuota):?>

        <?php if($cuota['tipo_registro'] == 'SALDO_ANTERIOR'):?>
            <?php $ACU_SALDO_CUOTA = $ACU_SALDO_CUOTA_ACUM = $cuota['saldo_conciliado'];?>                                 
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
                <th colspan="16" style="font-size:13px;background-color: #666666;border:0"><h4 style="text-align: left;color:#FFFFFF;"><?php echo $util->periodo($cuota['periodo'],true)?></h4></th>
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
            <?php $ACU_SALDO_CUOTA += $cuota['saldo_conciliado'];?>
            <?php $ACU_SALDO_CUOTA_ACUM += $cuota['saldo_conciliado'];?>   
            <?php $ACUM_PENDIENTE += $cuota['pendiente'];?> 
            <?php $ACUM_PENDIENTE_TOTAL += $cuota['pendiente'];?>                   
                
            <tr class="<?php echo $cuota['estado']?>">
                <td align="center"><?php echo $cuota['orden_descuento_id']?></td>
                <td><?php echo $cuota['organismo']?></td>
                <td nowrap="nowrap"><?php echo $cuota['tipo_numero'];?></td>
                <td align="center"><?php echo $cuota['cod_nro']?></td>
                <td><?php echo $cuota['producto']?></td> 
                <td align="right"><?php echo ($cuota['importe_solicitado']!=0 ? number_format($cuota['importe_solicitado'],2) : "") ?></td>
                <td align="center"><?php echo $cuota['cuota']?></td>
                <td><?php echo $cuota['tipo_cuota']?></td>
                <td align="center"><?php echo $util->armaFecha($cuota['vencimiento'])?></td>
                <td><?php echo $cuota['estado']?></td>
                <td><?php echo $cuota['situacion_cuota']?></td>
                <td align="right"><?php echo ($cuota['importe'] < 0 ? '<span style="color:red;">'.number_format($cuota['importe'],2).'</span>' : number_format($cuota['importe'],2)) ?></td>
                <td align="right"><?php echo number_format($cuota['pagado'],2)?></td>
                <td align="right" style="color:green;"><?php if($cuota['pendiente'] != 0) echo number_format($cuota['pendiente'],2)?></td>
                <td align="right"><?php echo ($cuota['saldo_conciliado'] < 0 ? '<span style="color:red;">'.number_format($cuota['saldo_conciliado'],2).'</span>' : number_format($cuota['saldo_conciliado'],2)) ?></td>
                <td align="center"><?php // echo $controles->btnModalBox(array('title' => 'DETALLE CUOTA','url' => '/mutual/orden_descuento_cuotas/view/'.$cuota['id'],'h' => 450, 'w' => 750))?></td>
            </tr>
        <?php endif;?>
        <?php if($cuota['tipo_registro'] == 'PAGO'):?>
        <?php $ACU_PAGO_CUOTA += $cuota['pagado'];?>
            
        <tr class="info_pago">
            <td colspan="7"></td>
            <td style="font-size: 75%;font-style: italic;text-align: right;"><?php echo $cuota['tipo_cobro']?></td>
            <td style="font-size: 75%;font-style: italic;"><?php echo $util->armaFecha($cuota['vencimiento'])?></td>
            <td colspan="3" style="font-size: 75%;font-style: italic;"><?php echo $util->periodo($cuota['situacion_cuota'])?></td>
            <td align="right" style="font-size: 75%;font-style: italic;"><?php echo $util->nf($cuota['pagado'])?></td>
            <td colspan="3">
                <?php if($cuota['cancelacion_orden_id'] != 0):?>
                    <?php echo $controles->linkModalBox('ORD.CANC. #'.$cuota['cancelacion_orden_id'],array('title' => 'DETALLE ORDEN DE CANCELACION','url' => '/mutual/cancelacion_ordenes/vista_detalle/'.$cuota['cancelacion_orden_id'],'h' => 450, 'w' => 750))?>
                <?php endif;?>
                <?php if($cuota['reversado'] == 1):?>
                    <span style='color:red'>Reversado</span>
                <?php endif;?>                
            </td>
        </tr>
            
        <?php endif;?>    
        <?php // if(!empty($cuota['OrdenDescuentoCuota']['pagos'])):?>
        <?php // foreach($cuota['OrdenDescuentoCuota']['pagos'] as $pago):?>
<!--        <tr class="info_pago">
            <td colspan="6"></td>
            <td style="font-size: 75%;font-style: italic;text-align: right;"><?php // echo $pago['TipoCobro']['concepto_1']?></td>
            <td style="font-size: 75%;font-style: italic;"><?php // echo $util->armaFecha($pago['OrdenDescuentoCobro']['fecha'])?></td>
            <td colspan="3" style="font-size: 75%;font-style: italic;"><?php // echo $util->periodo($pago['OrdenDescuentoCobro']['periodo_cobro'])?></td>
            
            
            <td align="right" style="font-size: 75%;font-style: italic;"><?php // echo $util->nf($pago['OrdenDescuentoCobroCuota']['importe'])?></td>
            <td colspan="2"></td>
        </tr>-->
        <?php // endforeach;?>
        <?php // endif;?>
        
        <?php endforeach;?>
        <tr>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="11" align="right"><strong>TOTAL PERIODO</strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_IMPO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_PAGO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;color:green;" align="right"><strong><?php echo number_format($ACUM_PENDIENTE,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
        </tr>
        <tr>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" colspan="14" align="right"><strong>SALDO ACUMULADO A <?php echo $util->periodo($cuota['periodo'],true)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"><strong><?php echo number_format($ACU_SALDO_CUOTA_ACUM,2)?></strong></td>
            <td style="border-top: 1px solid #D8DBD4; background-color: #F5f7f7;" align="right"></td>
        </tr>           
    </table>    
<?php else:?>
<div class="notices">
<h4>NO EXISTEN CUOTAS PARA LOS PARAMETROS DE BUSQUEDA INDICADOS</h4>	
</div>
<?php endif;?>



<?php // debug($cuotas)?>
<?php endif;?>
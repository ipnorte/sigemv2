<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA :: EXPORTAR DATOS'))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php echo $this->renderElement('liquidacion/info_cabecera_liquidacion',array('liquidacion'=>$liquidacion,'plugin'=>'mutual'))?>



<?php if(!empty($diskette)):?>

<?php echo $controles->btnRew('REGRESAR AL LISTADO DE TURNOS','/mutual/liquidaciones/exportar2/'.$liquidacion['Liquidacion']['id'])?>
<br/>	

<h3>DETALLE DE TURNOS PROCESADOS</h3>

<table>
    <tr>
        <th>EMPRESA - TURNO</th>
        <th>REG TOTAL</th>
        <th>IMP. DISKETTE</th>
    </tr>
    
    <?php $ACU_IMPO = $ACU_REGISTROS = $ACU_OK = $ACU_ERRORES = $ACU_IMPO_ERROR = 0;?>
    
    <?php foreach($diskette['resumen_operativo'] as $turno):?>
    
    
        <?php //if($turno[0]['tipo'] == 'OK'):?>
        
        <?php $ACU_IMPO += $turno[0]['importe_adebitar'];?>
        <?php $ACU_REGISTROS += $turno[0]['liquidados'];?>
        <?php $ACU_OK += $turno[0]['cantidad_ok'];?>
        
        <tr>
            <td><?php echo $turno[0]['turno_pago_desc']?></td>
            <td align="center"><?php echo $turno[0]['liquidados']?></td>
            <td align="right"><?php echo $util->nf($turno[0]['importe_adebitar'])?></td>
        </tr>
        <?php //endif;?>
        
    
    <?php endforeach;?>
    
    
    <tr class="totales">
        <th>TOTAL GENERAL</th>
        <th style="text-align: center;"><?php echo $ACU_REGISTROS?></th>
        <th><?php echo $util->nf($ACU_IMPO)?></th>
        
    </tr>		
    
</table>

<?php echo $this->renderElement("banco/info_diskette",array('plugin' => 'config','toPDF' => TRUE, 'toXLS' => TRUE, 'uuid' => $DISKETTE_UUID, 'listado' => '/mutual/liquidaciones/resumen_control_diskette_sp/' . $liquidacion['Liquidacion']['id']))?>

<?php else:?>

<div class="notices_error">*** SIN DATOS GENERADOS ***</div>
    
<?php endif;?>	

<BR/>
<?php echo $controles->btnRew('REGRESAR AL LISTADO DE TURNOS','/mutual/liquidaciones/exportar2/'.$liquidacion['Liquidacion']['id'])?>	

<?php //debug($diskette['resumen_operativo'])?>
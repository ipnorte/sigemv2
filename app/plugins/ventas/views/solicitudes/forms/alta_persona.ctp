<?php echo $this->renderElement('solicitudes/menu_solicitudes',array('plugin' => 'ventas'))?>
<h3>NUEVA SOLICITUD</h3>
<hr/>
<?php echo $form->create(null,array('action' => 'alta_persona/'.$TOKEN_ID,'name'=>'formAltaPersona','id'=>'formAltaPersona',));?>
<div class="areaDatoForm">    
    <h3>INFORMACION PERSONAL DEL SOLICITANTE</h3>
    <?php if($solicitud['Persona']['fallecida']):?>
    <div class="notices_error2" style="width: 98%;">Persona registrada como FALLECIDA el <strong><?php echo $util->armaFecha($solicitud['Persona']['fecha_fallecimiento'])?></strong></div>
    <?php endif;?>
    <?php 
    if(!empty($solicitud['Persona']['id'])){
        echo $this->renderElement('personas/forms/datos_personales_form',
                array(
                    'persona' => $solicitud['Persona'],
                    'plugin' => 'pfyj',
                    'disabled'=> array(
                        'Persona.cuit_cuil',
                        'Persona.tipo_documento',
                        'Persona.documento',
                        'Persona.apellido',
                        'Persona.nombre',
                    ),
        ));
    }else{
        echo $this->renderElement('personas/forms/datos_personales_form',
                array(
                    'persona' => $solicitud['Persona'],
                    'plugin' => 'pfyj',
        ));        
    }        
    ?>     
        
   
    <?php echo $this->renderElement('personas/forms/domicilio_form',array('persona' => $solicitud['Persona'],'plugin' => 'pfyj'))?>
    <?php echo $this->renderElement('personas/forms/contacto_form',array('persona' => $solicitud['Persona'],'plugin' => 'pfyj'))?>
    
    <?php 
    $INI_FILE = $_SESSION['MUTUAL_INI'];
    $MOD_BCRA = (isset($INI_FILE['general']['modulo_bcra']) && $INI_FILE['general']['modulo_bcra'] != 0 ? TRUE : FALSE);    
    ?>
    <?php if($MOD_BCRA):?>
    <h4>Informe Banco Central</h4>
    <hr/>
    <div>
        <?php echo $this->renderElement('personas/consulta_bcra',array('cuit'=> $solicitud['Persona']['cuit_cuil'],'plugin' => 'pfyj'))?>
    </div>
    <?php endif;?>

    <?php if(!empty($solicitud['Persona']['socio_nro'])):?>
    <h4>Persona registrada como Socio</h4>
    <hr/>
    <div class="areaDatoForm2">
        <table class="tbl_form">
            <tr>
                <td>Socio Nro.</td>
                <td><strong><?php echo $solicitud['Persona']['socio_nro']?></strong></td>
                <td>Categoría</td>
                <td><strong><?php echo $solicitud['Persona']['socio_categoria']?></strong></td>
                <td>Estado</td>
                <td><strong><?php echo $solicitud['Persona']['socio_status']?></strong></td> 
                <td>Alta</td>
                <td><strong><?php echo $util->armaFecha($solicitud['Persona']['socio_fecha_alta'])?></strong></td>                  
                <td>Ultima Calificación</td>
                <td><strong><?php echo $solicitud['Persona']['socio_ultima_calificacion']?></strong></td>  
                <td>Fecha Ultima Calificación</td>
                <td><strong><?php echo $util->armaFecha($solicitud['Persona']['socio_fecha_ultima_calificacion'])?></strong></td>                  
            </tr>
            <tr>
                <td colspan="3">Calificaciones Anteriores</td>
                <td colspan="9"><strong><?php echo $solicitud['Persona']['socio_resumen_calificacion']?></strong>
                    &nbsp;<?php echo $controles->btnModalBox(array('title' => 'ULTIMO STOCK DE DEUDA','img'=> 'calendar_2.png','texto' => 'Scoring de Deuda','url' => '/mutual/liquidacion_socios/cargar_scoring_by_socio/'.$solicitud['Persona']['socio_nro'],'h' => 450, 'w' => 750))?></td>
            </tr>
        </table>
    </div>
    <?php endif;?>
<?php if(isset($solicitud['Persona']['fallecida']) && $solicitud['Persona']['fallecida']):?>
<div class="notices_error2" style="width: 98%;">Persona registrada como FALLECIDA el <strong><?php echo $util->armaFecha($solicitud['Persona']['fecha_fallecimiento'])?></strong></div>
<?php endif;?>    
</div>
<hr/>
<?php if(!$solicitud['Persona']['fallecida']):?>
<input type="submit" value="SIGUIENTE" id="btnProcessCUIT"/>
<?php else:?>
<input type="submit" value="SIGUIENTE" id="btnProcessCUIT" disabled=""/>
<?php endif;?>
<?php echo $frm->hidden('MutualProductoSolicitud.token_id',array('value' => $TOKEN_ID))?>
<?php echo $form->end();?>

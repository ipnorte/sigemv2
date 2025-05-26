<style>
    .field{
        width: auto;float: left;margin:5px 10px 5px 0px;
    }
</style>
<div class="areaDatoForm">
    <?php if(isset($solicitud['Persona'])):?>
    <h3>DATOS PERSONALES</h3>
    <hr>
    <div class="field">TIPO Y NRO DE DOCUMENTO: <strong><?php echo $solicitud['Persona']['tdoc_ndoc']?></strong></div>
    <div class="field">NOMBRE: <strong><?php echo $solicitud['Persona']['apenom']?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">DOMICILIO: <strong><?php echo $solicitud['Persona']['domicilio']?></strong></div>
    <div style="clear: both;"></div>
    <div class="field">DATOS COMPLEMENTARIOS: <strong><?php echo $solicitud['Persona']['datos_complementarios']?></strong></div>
    <?php if($solicitud['Persona']['fallecida']):?>
    <div style="clear: both;"></div>
    <div class="notices_error2" style="width: 98%;">Persona registrada como FALLECIDA el <strong><?php echo $util->armaFecha($solicitud['Persona']['fecha_fallecimiento'])?></strong></div>
    <?php endif;?>      
    
    <?php 
    $INI_FILE = $_SESSION['MUTUAL_INI'];
    $MOD_BCRA = (isset($INI_FILE['general']['modulo_bcra']) && $INI_FILE['general']['modulo_bcra'] != 0 ? TRUE : FALSE);    
    ?>
    <div style="clear: both;width: 100%;"></div>
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
    
    
    <?php endif;?>
</div>

<?php // debug($solicitud)?>
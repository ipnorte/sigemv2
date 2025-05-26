<?php echo $this->renderElement('head',array('title' => 'FACTURACION ELECTRONICA','plugin' => 'config'))?>


<?php
    if($errorConexion == 'OK'){
        echo $this->renderElement('head',array('title' => 'CONEXION ESTABLECIDA CON EL WEB SERVER DE AFIP','plugin' => 'config'));
        if($datos_afip['AfipDato']['modo'] == 0){
            echo $this->renderElement('head',array('title' => 'EL PROCESO ESTA EN MODO HOMOLOGACION (PRUEBA)','plugin' => 'config'));
        }
        else{
            echo $this->renderElement('head',array('title' => 'EL PROCESO ESTA EN MODO PRODUCCION','plugin' => 'config'));
        }
    
?>

        <script type="text/javascript">
        Event.observe(window, 'load', function(){
            <?php if($disable_form == 1):?>
                    $('form_cobros_por_fecha').disable();
            <?php endif;?>
        });
        </script>


        <div class="areaDatoForm">
            <?php echo $frm->create(null,array('action' => 'facturacion_electronica','id' => 'form_cobros_por_fecha'))?>
            <table class="tbl_form">
                    <tr>
                            <td>DESDE FECHA</td><td><?php echo $frm->calendar('ListadoService.fecha_desde','',$fecha_desde,'1990',date("Y"))?></td>
                    </tr>
                    <tr>
                            <td>HASTA FECHA</td><td><?php echo $frm->calendar('ListadoService.fecha_hasta','',$fecha_hasta,'1990',date("Y"))?></td>
                    </tr>
                    <tr>
                            <td>FECHA FACTURACION</td><td><?php echo $frm->calendar('ListadoService.fecha_factura','',$fecha_factura,'1990',date("Y"))?></td>
                    </tr>
                    <tr><td colspan="2"><?php echo $frm->submit("GENERAR FACTURACION")?></td></tr>

            </table>
            <?php echo $frm->end()?>
        </div>


        <?php if($show_asincrono == 1){

            echo $this->renderElement('show',array(
                'plugin' => 'shells',
                'process' => 'factura_electronica',
//                'accion' => '',
                'accion' => '.facturacion.facturaciones.facturacion_electronica',
                'target' => '_blank',
                'btn_label' => 'Ver Listado',
                'titulo' => "FACTURA ELECTRONICA",
                'subtitulo' => 'Facturacion de cobro entre fechas. ' . MUTUALPROVEEDORID,
                'p3' => $fecha_factura,
                'p1' => $fecha_desde,
                'p2' => $fecha_hasta,
                'p4' => MUTUALPROVEEDORID
            ));

        }
    }
    else{
            echo $this->renderElement('head',array('title' => 'ERROR CONEXION CON EL WEB SERVER DE AFIP','plugin' => 'config'));
    }
?>
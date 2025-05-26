<?php echo $this->renderElement('head',array('title' => 'LIQUIDACION DE DEUDA | PROCESO :: '.$util->periodo($periodo,true).' :: ' . $util->globalDato($organismo)))?>
<?php echo $this->renderElement('liquidacion/menu_liquidacion_deuda',array('plugin'=>'mutual'))?>
<?php  if(!empty($periodo)):?>
    <div class="notices"><strong>ATENCION!:</strong> Mientras se encuentra ejecut&aacute;ndose el proceso NO CERRAR ESTA VENTANA!</div>
    <?php if($pre_imputacion == 1):?>
        <div class="notices_error"><p><strong style="font-size: medium;">El proceso de calculo de saldos se efect&uacute;a sobre la PRE-IMPUTACION del per&iacute;odo anterior!.</strong></p></div>
    <?php endif;?>
    <?php if(isset($archivos) && $archivos && $pre_imputacion == 0):?>
    <div class="notices_error"><p><strong style="font-size: medium;">
    ATENCION: LA LIQUIDACION PARA EL PERIODO / ORGANISMO INDICADO YA TIENE ARCHIVOS EMITIDOS.</strong></p>
    <p>Si reliquida el per&iacute;odo, <strong>TODA</strong> la informaci&oacute;n ser&aacute; borrada. </p>
    </div>
    <?php endif;?>
    <?php 
    echo $this->renderElement('show',array(
                                            'plugin' => 'shells',
                                            'process' => 'liquida_deuda_fraccion',
                                            'accion' => '.mutual.liquidaciones.resumencontrol.'.$periodo.'.'.$organismo,
                                            'btn_label' => 'Resumen de Liquidacion',
                                            'target' => '',
                                            'titulo' => 'PROCESO DE LIQUIDACION DE DEUDA',
                                            'subtitulo' => 'Periodo a Liquidar: ' . $util->periodo($periodo) .' - ORGANISMO: ' . $util->globalDato($organismo) . ($pre_imputacion == 1 ? " *** SOBRE PRE-IMPUTACION ***" : ""),
                                            'p1' => $periodo,
                                            'p2' => $organismo,
                                            'p3' => $pre_imputacion,
                                            'p4' => $tipo_deuda_liquida,
    ));

    ?>

    
<?php endif;?>
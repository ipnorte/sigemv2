<?php 
echo $this->renderElement('show',array(
    'plugin' => 'shells',
    'process' => 'tmp_analisis_estadistico',
    'accion' => '.shells.asincronos.estadisticas',
    'target' => '',
    'btn_label' => 'VER REPORTE',
    'titulo' => 'INFORME ORDENES DE DESCUENTOS FINALIZADAS',
    'p1' => $periodo,
    'p2' => $meses,
));

?>

<?php 
$tabs = array(
    0 => array('url' => '/mutual/liquidaciones/proceso_nuevo','label' => 'PROCESO LIQUIDACION DEUDA', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null),
    1 => array('url' => '/mutual/liquidaciones/consulta2/','label' => 'CONSULTAR LIQUIDACIONES', 'icon' => 'controles/cart.png','atributos' => array(), 'confirm' => null),
    2 => array('url' => '/mutual/liquidaciones/reporte_general_imputacion','label' => 'REPORTE GENERAL IMPUTACION', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
    3 => array('url' => '/mutual/liquidaciones/reporte_control_vtos','label' => 'CONTROL VENCIMIENTOS', 'icon' => 'controles/printer.png','atributos' => array(), 'confirm' => null),
);
echo $cssMenu->menuTabs($tabs,false);			
?>

<?php 
$tabs = array(
    0 => array('url' => '/mutual/informe_comerciales/index','label' => 'Consultar Informes', 'icon' => 'controles/folder_user.png','atributos' => array(), 'confirm' => null),
    1 => array('url' => '/mutual/informe_comerciales/generar_informe','label' => 'Generar Nuevo Informe', 'icon' => 'controles/report.png','atributos' => array(), 'confirm' => null),
);
echo $cssMenu->menuTabs($tabs,false);	
?>

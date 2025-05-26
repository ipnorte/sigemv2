<?php
$tabs = array(
    0 => array('url' => '/mutual/liquidaciones/notifica_deuda','label' => 'Notificaciones', 'icon' => 'controles/folder_user.png','atributos' => array(), 'confirm' => null),
    1 => array('url' => '/mutual/liquidaciones/notifica_deuda_envio','label' => 'Enviar Notificaciones', 'icon' => 'controles/email.png','atributos' => array(), 'confirm' => null),
);
echo $cssMenu->menuTabs($tabs,false);	
?>
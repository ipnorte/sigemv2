<?php 
$tabs = array(
				0 => array('url' => '/clientes/recibos/recibos_entre_fecha/','label' => 'Recibos entre Fechas', 'icon' => 'controles/date.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/clientes/recibos/recibos_por_numero/','label' => 'Recibos por Numero', 'icon' => 'controles/report.png','atributos' => array(), 'confirm' => null),
			);
echo $cssMenu->menuTabs($tabs);			
?>
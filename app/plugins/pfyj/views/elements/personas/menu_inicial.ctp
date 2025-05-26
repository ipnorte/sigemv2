<?php echo $this->renderElement('head',array('plugin' => 'config','title' => 'PADRON DE PERSONAS / SOCIOS'))?>
<?php
$tabs = array(
                                0 => array('url' => '/pfyj/personas/index','label' => 'Padrón', 'icon' => 'controles/folder_user.png','atributos' => array(), 'confirm' => null),
				1 => array('url' => '/pfyj/personas/add','label' => 'Nueva Persona', 'icon' => 'controles/add.png','atributos' => array(), 'confirm' => null),
				2 => array('url' => '/pfyj/personas/imprimir_padron','label' => 'Imprimir Padron', 'icon' => 'controles/pdf.png','atributos' => array(), 'confirm' => null),
                                3 => array('url' => '/pfyj/personas/consultar_intranet','label' => 'Consultar Intranet', 'icon' => 'controles/information.png','atributos' => array(), 'confirm' => null),
			);

$INI_FILE = (isset($_SESSION['MUTUAL_INI']) ? $_SESSION['MUTUAL_INI'] : NULL);
$MOD_BCRA = (isset($INI_FILE['general']['modulo_bcra']) && $INI_FILE['general']['modulo_bcra'] != 0 ? TRUE : FALSE);
$MOD_SIISA = (isset($INI_FILE['general']['modulo_siisa']) && $INI_FILE['general']['modulo_siisa'] != 0 ? TRUE : FALSE);

if($MOD_BCRA){
    $tabs[4] = array('url' => '/pfyj/personas/consultaBCRA','label' => 'B.C.R.A.', 'icon' => 'controles/vcard.png','atributos' => array(), 'confirm' => null);
}
if($MOD_SIISA){
    $tabs[5] = array('url' => '/pfyj/personas/consulta_siisa_general','label' => 'SIISA', 'icon' => 'controles/calculator.png','atributos' => array(), 'confirm' => null);
}

echo $cssMenu->menuTabs($tabs);	
?>

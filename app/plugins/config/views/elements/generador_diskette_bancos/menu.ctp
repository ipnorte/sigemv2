<?php


#MODULO DE TARJETAS DE DEBITO
$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$MOD_TARJETAS = (isset($INI_FILE['general']['tarjetas_de_debito']) && $INI_FILE['general']['tarjetas_de_debito'] == 1 ? TRUE : FALSE);
$MOD_FIRSTDATA = (isset($INI_FILE['intercambio']['firsdata_comercio']) ? TRUE : FALSE);
$MOD_COFINCRED = (isset($INI_FILE['intercambio']['cofincred']) ? TRUE : FALSE);


$tabs = array(
        0 => array('url' => '/config/generador_diskette_bancos/exportar','label' => 'GENERAR ARCHIVO PARA DISKETTE', 'icon' => 'controles/disk.png','atributos' => array(), 'confirm' => null),
        1 => array('url' => '/config/generador_diskette_bancos/importar','label' => 'PROCESAR ARCHIVO DE DISKETTE', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        2 => array('url' => '/config/generadorDisketteBancos/encriptar_bcocba','label' => 'ENCRIPTAR ARCHIVO BANCO CORDOBA', 'icon' => 'controles/encrypted.png','atributos' => array(), 'confirm' => null),
        3 => array('url' => '/config/generadorDisketteBancos/excel_municipio','label' => 'PROCESAR ARCHIVO MUNICIPIO', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        4 => array('url' => '/config/generadorDisketteBancos/excel_reintegros','label' => 'PROCESAR ARCHIVO REINTEGROS', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        5 => array('url' => '/config/generadorDisketteBancos/excel_cobrodigital','label' => 'PROCESAR ARCHIVO COBRO DIGITAL', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        6 => array('url' => '/config/generadorDisketteBancos/excel_fenanjor','label' => 'PROCESAR ARCHIVO FENANJOR', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        7 => array('url' => '/config/generadorDisketteBancos/excel_cuenca','label' => 'PROCESAR ARCHIVO CUENCA', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        8 => array('url' => '/config/generadorDisketteBancos/excel_arcofisa','label' => 'PROCESAR ARCHIVO ARCOFISA', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        9 => array('url' => '/config/generadorDisketteBancos/excel_sicon','label' => 'PROCESAR ARCHIVO SICON', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        10 => array('url' => '/config/generadorDisketteBancos/unificar_comafi','label' => 'UNIFICAR BANCO COMAFI', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        // 11 => array('url' => '/config/generadorDisketteBancos/excel_cjpc','label' => 'PROCESAR CJPC', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        12 => array('url' => '/config/generadorDisketteBancos/excel_bcocomer','label' => 'PROCESAR BANCO COMERCIO', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        13 => array('url' => '/config/generadorDisketteBancos/zip_coinag','label' => 'BANCO COINAG', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        14 => array('url' => '/config/generadorDisketteBancos/bna','label' => 'BNA', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        15 => array('url' => '/config/generadorDisketteBancos/unificar_cronocred','label' => 'UNIFICAR BANCO CRONOCRED', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        16 => array('url' => '/config/generadorDisketteBancos/divide_celesol','label' => 'MUTUAL CELESOL * BCO.CORDOBA', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        17 => array('url' => '/config/generadorDisketteBancos/unificar_coinag','label' => 'UNIFICAR BANCO COINAG', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null),
        
);
if($MOD_TARJETAS){
    $tabs[17] = array('url' => '/config/generadorDisketteBancos/excel_zenrise','label' => 'PROCESAR ARCHIVO ZENRISE', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null);
}

if($MOD_FIRSTDATA){
    $tabs[18] = array('url' => '/config/generadorDisketteBancos/excel_firstdata','label' => 'FIRSTDATA', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null);
}

if($MOD_COFINCRED){
    $tabs[19] = array('url' => '/config/generadorDisketteBancos/excel_cofincred','label' => 'COFINCRED', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null);
}

$tabs[20] = array('url' => '/config/generadorDisketteBancos/excel_reversos_santander','label' => 'REVERSOS SANTANDER', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null);
$tabs[21] = array('url' => '/config/generadorDisketteBancos/divide_liquidacion','label' => 'DIVIDE POR LIQUIDACION', 'icon' => 'controles/cog.png','atributos' => array(), 'confirm' => null);
$tabs[21] = array('url' => '/config/generadorDisketteBancos/excel_cjpc_main','label' => 'PROCESAR EXCEL CJPC', 'icon' => 'controles/ms_excel.png','atributos' => array(), 'confirm' => null);


echo $cssMenu->menuTabs($tabs,false);
?>

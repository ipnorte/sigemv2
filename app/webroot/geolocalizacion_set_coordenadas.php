<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$INI_FILE = parse_ini_file(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR.'mutual.ini', true);
if(isset($INI_FILE['general']['domi_fiscal_latitud']) &&  !empty($INI_FILE['general']['domi_fiscal_latitud']) && isset($INI_FILE['general']['domi_fiscal_longitud']) &&  !empty($INI_FILE['general']['domi_fiscal_longitud'])){
    $LATITUD = $INI_FILE['general']['domi_fiscal_latitud'];
    $LONGITUD = $INI_FILE['general']['domi_fiscal_longitud'];
}else{
    echo "ERROR DE CONFIGURACION DEL SERVICIO GOOGLE MAPS";
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "database.php";
function dbLink(){
	$dbCONFIG = new DATABASE_CONFIG();
	$link = mysql_connect($dbCONFIG->default['host'],$dbCONFIG->default['login'],$dbCONFIG->default['password'])
	or die ("No se establecio conexion a la base de datos");
	mysql_select_db($dbCONFIG->default['database'],$link);
	return $link;
}


?>
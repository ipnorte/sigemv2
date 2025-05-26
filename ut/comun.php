<?php

/**
*
* comun.php
* @author adrian [* 17/07/2012]
*/



define("BASE_PATH", dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
define("LOGS",BASE_PATH."app".DIRECTORY_SEPARATOR."tmp".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR);
define('PHPEXCEL_APP', BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'vendors' . DIRECTORY_SEPARATOR . 'PHPExcel' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR);

function dbLink(){
	require_once BASE_PATH .  "app" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "database.php";
	$dbCONFIG = new DATABASE_CONFIG();
	$link = mysql_connect($dbCONFIG->default['host'],$dbCONFIG->default['login'],$dbCONFIG->default['password'])
	or die ("No se establecio conexion a la base de datos");
	mysql_select_db($dbCONFIG->default['database'],$link);	
	return $link;
}


function dbLinkTmp(){
	require_once BASE_PATH .  "app" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "database.php";
	$dbCONFIG = new DATABASE_CONFIG();
	$link = mysql_connect($dbCONFIG->tmp['host'],$dbCONFIG->tmp['login'],$dbCONFIG->tmp['password'])
	or die ("No se establecio conexion a la base de datos");
	mysql_select_db($dbCONFIG->tmp['database'],$link);	
	return $link;
}

function debug($var){
	echo "<pre>".print_r($var)."</pre>";
}

?>
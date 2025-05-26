<?php

/**
* PROCESA UN ARCHIVO DE AUDITORIA Y LO CARGA EN UNA TABLA TEMPORAL DE LA BASE TEMPORAL
* auditoria.php
* @author adrian [* 17/07/2012]
* 
* 
* CREATE TABLE `auditorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `action_type` varchar(4) DEFAULT NULL,
  `action` longtext,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`usuario`),
  KEY `idx_fecha` (`fecha`),
  KEY `idx_file` (`file_name`),
  KEY `idx_act` (`action_type`)
) ENGINE=MyISAM
* 
*/

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "comun.php";

$separador = chr(174).chr(175);
$fileName = "AUDITORIA_20120626.log";
//$fileName = "AUDITORIA_20120324.log";

//echo BASE_PATH;
//echo LOGS;
//exit;


ini_set("memory_limit","1000M");


//$dbLnk = dbLink();
$conn = mysql_connect("127.0.0.1","sigem_sa","sa#1qaz");
mysql_select_db("temporal",$conn);

//$fp = fopen(LOGS.$fileName.".sql", 'w');

$sql = "DELETE FROM `temporal`.`auditorias` WHERE `file_name` = '$fileName';\n";
mysql_query($sql,$conn);
//fwrite($fp, $sql);

$registros = file(LOGS.$fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach($registros as $registro){
	$items = explode($separador, $registro);
	$sql = "INSERT INTO `temporal`.`auditorias` (`file_name`,`fecha`,`usuario`,`ip`,`action_type`,`action`) VALUES ('$fileName','".$items[0]."','".$items[1]."','".$items[2]."','".$items[3]."','".$items[4]."');\n";
	echo $sql;
	mysql_query($sql,$conn);
//	fwrite($fp,$sql);
}
//fclose($fp);

?>
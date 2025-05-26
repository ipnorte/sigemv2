<?php

/**
*
* auditoria_read.php
* @author adrian [* 18/07/2012]
*/

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "comun.php";


$conn = mysql_connect("127.0.0.1","sigem_sa","sa#1qaz");
mysql_select_db("temporal",$conn);

$sql = "SELECT * FROM auditorias WHERE ACTION LIKE '%s:10:\"persona_id\";s:4:\"2409\"%'";
$result = mysql_query($sql,$conn);
while($row = mysql_fetch_assoc($result)):

	$action = unserialize($row['action']);
	$data = unserialize($action['data_model_serialized']);

	debug($data);

endwhile;

?>
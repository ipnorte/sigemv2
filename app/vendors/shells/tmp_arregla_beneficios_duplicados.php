<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 */

include("libreria.php");
$dbLink = dbLink();

##############################################################
#PROCESO CBU
##############################################################
$sql = "select persona_id,codigo_empresa,cbu,count(1) from persona_beneficios where codigo_beneficio = 'MUTUCORG2201'
		group by persona_id,codigo_empresa,cbu having count(1) > 1";

$result = mysql_query($sql,$dbLink);
while($row = mysql_fetch_assoc($result)){
	$persona_id = $row['persona_id'];
	$codigo_empresa = $row['codigo_empresa'];
	$cbu = $row['cbu'];
	
	#saco el beneficio que voy a dejar
	$sql_1 = "select id from persona_beneficios where persona_id = $persona_id and codigo_beneficio = 'MUTUCORG2201' and cbu = '$cbu' and codigo_empresa = '$codigo_empresa' order by id DESC LIMIT 1;";
	$result_1 = mysql_query($sql_1,$dbLink);
	$id_queda = 0;
	while($row1 = mysql_fetch_assoc($result_1)){
		$id_queda = $row1['id'];
	}
	
	#recorro los beneficios restantes analizando las ordenes de compra, ordenes de descuentos, cuotas
	$sql_2 = "select id from persona_beneficios where id <> $id_queda and persona_id = $persona_id and cbu = '$cbu' and codigo_empresa = '$codigo_empresa';";
	$result_2 = mysql_query($sql_2,$dbLink);
	while($row2 = mysql_fetch_assoc($result_2)){
		
		$id_saca = $row2['id'];
		
		$sqlTablas = "select TABLE_NAME from information_schema.COLUMNS where TABLE_SCHEMA = 'aman2_db' and COLUMN_NAME = 'persona_beneficio_id';";
		$result_3 = mysql_query($sqlTablas,$dbLink);
		while($row3 = mysql_fetch_assoc($result_3)){
			$tabla = $row3['TABLE_NAME'];
			
			$sqlUpd = "update aman2_db.$tabla set persona_beneficio_id = $id_queda where persona_beneficio_id = $id_saca";
			mysql_query($sqlUpd,$dbLink);
		}
		
		$sql_marca = "update persona_beneficios set activo = 0, dupli = 1, fecha_baja = '".date('Y-m-d')."' where id = $id_saca";
		echo $sql_marca ."\n";
		mysql_query($sql_marca,$dbLink);
		
	}
	
}



##############################################################
#PROCESO CJP
##############################################################
$sql = "select persona_id,nro_beneficio,nro_ley,count(1) from persona_beneficios where codigo_beneficio = 'MUTUCORG7701'
		group by persona_id,nro_beneficio,nro_ley having count(1) > 1";

$result = mysql_query($sql,$dbLink);
while($row = mysql_fetch_assoc($result)){
	$persona_id = $row['persona_id'];
	$nro_beneficio = $row['nro_beneficio'];
	$nro_ley = $row['nro_ley'];
	
	#saco el beneficio que voy a dejar
	$sql_1 = "select id from persona_beneficios where persona_id = $persona_id and codigo_beneficio = 'MUTUCORG7701' and nro_beneficio = '$nro_beneficio' and nro_ley = '$nro_ley' order by id DESC LIMIT 1;";
	$result_1 = mysql_query($sql_1,$dbLink);
	$id_queda = 0;
	while($row1 = mysql_fetch_assoc($result_1)){
		$id_queda = $row1['id'];
	}
	
	#recorro los beneficios restantes analizando las ordenes de compra, ordenes de descuentos, cuotas
	$sql_2 = "select id from persona_beneficios where id <> $id_queda and persona_id = $persona_id and codigo_beneficio = 'MUTUCORG7701' and nro_beneficio = '$nro_beneficio' and nro_ley = '$nro_ley';";
	$result_2 = mysql_query($sql_2,$dbLink);
	while($row2 = mysql_fetch_assoc($result_2)){
		
		$id_saca = $row2['id'];
		
		$sqlTablas = "select TABLE_NAME from information_schema.COLUMNS where TABLE_SCHEMA = 'aman2_db' and COLUMN_NAME = 'persona_beneficio_id';";
		$result_3 = mysql_query($sqlTablas,$dbLink);
		while($row3 = mysql_fetch_assoc($result_3)){
			$tabla = $row3['TABLE_NAME'];
			
			$sqlUpd = "update aman2_db.$tabla set persona_beneficio_id = $id_queda where persona_beneficio_id = $id_saca";
			mysql_query($sqlUpd,$dbLink);
		}
		
		$sql_marca = "update persona_beneficios set activo = 0, dupli = 1, fecha_baja = '".date('Y-m-d')."' where id = $id_saca";
		echo $sql_marca ."\n";
		mysql_query($sql_marca,$dbLink);
		
	}
	
}



##############################################################
#PROCESO ANSES
##############################################################
$sql = "select persona_id,nro_beneficio,count(1) from persona_beneficios where codigo_beneficio = 'MUTUCORG6601'
		group by persona_id,nro_beneficio having count(1) > 1";

$result = mysql_query($sql,$dbLink);
while($row = mysql_fetch_assoc($result)){
	$persona_id = $row['persona_id'];
	$nro_beneficio = $row['nro_beneficio'];
	
	#saco el beneficio que voy a dejar
	$sql_1 = "select id from persona_beneficios where persona_id = $persona_id and codigo_beneficio = 'MUTUCORG6601' and nro_beneficio = '$nro_beneficio' order by id DESC LIMIT 1;";
	$result_1 = mysql_query($sql_1,$dbLink);
	$id_queda = 0;
	while($row1 = mysql_fetch_assoc($result_1)){
		$id_queda = $row1['id'];
	}
	
	#recorro los beneficios restantes analizando las ordenes de compra, ordenes de descuentos, cuotas
	$sql_2 = "select id from persona_beneficios where id <> $id_queda and persona_id = $persona_id and codigo_beneficio = 'MUTUCORG6601' and nro_beneficio = '$nro_beneficio' ;";
	$result_2 = mysql_query($sql_2,$dbLink);
	while($row2 = mysql_fetch_assoc($result_2)){
		
		$id_saca = $row2['id'];
		
		$sqlTablas = "select TABLE_NAME from information_schema.COLUMNS where TABLE_SCHEMA = 'aman2_db' and COLUMN_NAME = 'persona_beneficio_id';";
		$result_3 = mysql_query($sqlTablas,$dbLink);
		while($row3 = mysql_fetch_assoc($result_3)){
			$tabla = $row3['TABLE_NAME'];
			
			$sqlUpd = "update aman2_db.$tabla set persona_beneficio_id = $id_queda where persona_beneficio_id = $id_saca";
			mysql_query($sqlUpd,$dbLink);
		}
		
		$sql_marca = "update persona_beneficios set activo = 0, dupli = 1, fecha_baja = '".date('Y-m-d')."' where id = $id_saca";
		echo $sql_marca ."\n";
		mysql_query($sql_marca,$dbLink);
		
	}
	
}

?>
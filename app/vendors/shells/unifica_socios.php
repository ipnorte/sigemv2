<?php
/**
 * PROCESO UNIFICACION DE SOCIOS
 * PARAMETROS
 * 1 --> ID_SOCIO QUE QUEDA
 * 2 --> ID_SOCIO A UNIFICAR
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * LANZADOR
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php unifica_socios 3897 18457 -app /home/adrian/dev/www/sigem/app/
 *
 *
 */

class UnificaSociosShell extends Shell{


	var $id_socio_new = 0;
	var $id_socio_old = 0;
	
	var $uses = array('Shells.Asincrono');
	
	
	function main(){

		
		$this->id_socio_new = $this->args[0];
		$this->id_socio_old = $this->args[1];

		$this->out("**** PROCESO UNIFICACION DE SOCIOS ". $this->id_socio_new ." ===> ".$this->id_socio_old." *****");
		
		$db = & ConnectionManager::getDataSource($this->Asincrono->useDbConfig);
		
		$tablas = $db->_sources;
		$conn = $db->connection;
		
		$name_DB = $db->config['database'];
		
//		debug($tablas);
		
		$sql = "SELECT table_name FROM information_schema.columns WHERE table_schema = '$name_DB' AND column_name = 'socio_id'";
		$result = mysql_query($sql,$conn);
		while($row = mysql_fetch_assoc($result)):
			
//			debug($row);
			
			$sql1 = "update $name_DB." . $row['table_name'] ." set socio_id = ".$this->id_socio_new." where socio_id = " . $this->id_socio_old.";";
			$this->out($sql1);
			
		endwhile;
//		$sql = "select * from global_datos";
//		
//		$result = mysql_query($sql,$conn);
//		
//		while($row = mysql_fetch_assoc($result)):
//		
//			debug($row);
//		
//		endwhile;
		
//		debug($db);
		
		

		
	}
	
	
	
	
	
	
}

?>
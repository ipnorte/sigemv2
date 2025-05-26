<?php
/******************************************************************************************************************
 * GENERADOR DE BACKUPS
 * ADRIAN - 23/11/2009
 * /usr/bin/mysqldump --opt --single-transaction --comments --dump-date --no-autocommit --password=mutual+mysql*180407 
 * --user=mutual mutual_db --result-file=/var/backups/mysql/mutual_db.sql
 * 
 * mysql -u mutual -p mutual_dbu < /var/backups/mysql/mutual_db.sql
 * 
 * lanzador
 * 
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php backup 1 -app /var/www/sigem/app/
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php backup 1 -app /home/adrian/dev/www/sigem/app/
 * 
 ******************************************************************************************************************/
//define('USER_DB','mutual');
//define('PASS_DB','mutual+mysql*180407');
//define('DB_NAME','mutual_db');
//define('PATH_BACKUP','/var/backups/mysql/');
//define('EXEC_MYDUMP','/usr/bin/mysqldump');



/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 */

class BackupShell extends Shell{

	var $uses = array('Shells.Asincrono','v1.Solicitud');
	
	function main(){
		
	
		
		//limpio temporales
		$this->Asincrono->limpiarTablas();
		
		//verificar la cantidad de backups (no mas de 7)
		
		$db = & ConnectionManager::getDataSource($this->Asincrono->useDbConfig);
		$db1 = & ConnectionManager::getDataSource($this->Solicitud->useDbConfig);

		$userDB = $db->config['login'];
		$passDB = $db->config['password'];
		$name_DB = $db->config['database'];	
		
		$userDB_v1 = $db1->config['login'];
		$passDB_v1 = $db1->config['password'];
		$name_DB_v1 = $db1->config['database'];			
		
		
		
		$zipCMD 		= "tar -cvzf ";
		$pathFTP_Local 	= "backup/sigem";
		
		$MSQL_DUMP_COMMAND						= DS . "usr". DS ."bin". DS ."mysqldump";
		$basePath_MSQL_DATAFOLDER 				= DS. "datos". DS ."mysql";
		
		
		$basePath_MSQL_Backup 					= DS . "var" . DS . "backups" . DS . "mysql";
		$basePath_LOGS_Backup 					= DS . "var" . DS . "backups" . DS . "logs";
		$basePath_INTERCAMBIOS_Backup 			= DS . "var" . DS . "backups" . DS . "intercambios";
		$basePath_SOCIONOVEDADES_ADJ_Backup 	= DS . "var" . DS . "backups" . DS . "socio_novedades_adj";
		
		
		$fileBackup = $basePath_MSQL_Backup . DS . "diario" . DS . $name_DB ."_".date('Ymd').".sql";
		
		
		if(date('N') == 6){
		
			$fileTARGZ = "backup_".date('Ymd').".tar.gz";
			
			//creo un backup
			$CMD = $zipCMD . $basePath_MSQL_Backup . DS . "acumulado" . DS . "$fileTARGZ $fileBackup";
			exec($CMD);
			
			//borro todos los sql
			if(file_exists($basePath_MSQL_Backup . DS . "acumulado" . DS . $fileTARGZ) && filesize($basePath_MSQL_Backup . DS . "acumulado" . DS . $fileTARGZ) > 1000){
				$CMD = "rm -rf $basePath_MSQL_Backup" . DS . 'diario' . DS . "*.sql";
				exec($CMD);
			}

			//mando al 128 el acumulado
			$conn_id = $this->conectarFTP_LOCAL();
			ftp_put($conn_id,"sigem_db_semanal.tar.gz",$basePath_MSQL_Backup . DS . "acumulado" . DS . $fileTARGZ,FTP_BINARY);
			ftp_close($conn_id);
			
			
		}
		
		
//		$CMD = $MSQL_DUMP_COMMAND ." --opt --single-transaction --comments --dump-date --no-autocommit --password=$passDB --user=$userDB $name_DB --result-file=$fileBackup";
		
		$CMDBCKOPT = " --opt";
		$CMDBCKOPT .= " --single-transaction";
		$CMDBCKOPT .= " --comments";
		$CMDBCKOPT .= " --dump-date";
		$CMDBCKOPT .= " --no-autocommit";
		$CMDBCKOPT .= " --password=" . $passDB;
		$CMDBCKOPT .= " --user=" . $userDB;
		$CMDBCKOPT .= " " . $name_DB;
		$CMDBCKOPT .= " --result-file=" . $fileBackup;
		
		$CMD = $MSQL_DUMP_COMMAND . $CMDBCKOPT;
		exec($CMD);	
		
		//HAGO UN BACK DE LA VERSION 1
		$fileBackup_v1 = $basePath_MSQL_Backup . DS . "diario" . DS . $name_DB_v1 ."_".date('Ymd').".sql";
		
		$CMDBCKOPT = " --opt";
		$CMDBCKOPT .= " --single-transaction";
		$CMDBCKOPT .= " --comments";
		$CMDBCKOPT .= " --dump-date";
		$CMDBCKOPT .= " --no-autocommit";
		$CMDBCKOPT .= " --password=" . $passDB_v1;
		$CMDBCKOPT .= " --user=" . $userDB_v1;
		$CMDBCKOPT .= " " . $name_DB_v1;
		$CMDBCKOPT .= " --result-file=" . $fileBackup_v1;
		
		$CMD = $MSQL_DUMP_COMMAND . $CMDBCKOPT;
		exec($CMD);		
		
		
		//guardo las auditorias (quedan los ultimos 3 meses)
		$CMD = "find * -name \auditoria_*.tar.gz -mtime +90 -exec rm {} \;";
		exec($CMD);
		$fileTARGZ = "auditoria_".date('Ymd').".tar.gz";
		$CMD = $zipCMD . $basePath_LOGS_Backup . DS . "$fileTARGZ " . LOGS . "*.log";
		exec($CMD);
		
		if(file_exists($basePath_LOGS_Backup . DS . $fileTARGZ)):
			$conn_id = $this->conectarFTP_LOCAL();
			ftp_put($conn_id,$pathFTP_Local . DS . "auditoria.tar.gz",$basePath_LOGS_Backup . DS . $fileTARGZ,FTP_BINARY);
			ftp_close($conn_id);			
		endif;
		
		//guardo los intercambios
		$CMD = "find * -name \intercambios_*.tar.gz -mtime +90 -exec rm {} \;";
		exec($CMD);		
		$fileTARGZ = "intercambios_".date('Ymd').".tar.gz";
		$CMD = $zipCMD . $basePath_INTERCAMBIOS_Backup . DS . $fileTARGZ  ." ". WWW_ROOT . "files" . DS . "intercambio";
		exec($CMD);

		if(file_exists($basePath_INTERCAMBIOS_Backup . DS . $fileTARGZ)):
			$conn_id = $this->conectarFTP_LOCAL();
			ftp_put($conn_id,$pathFTP_Local . DS . "intercambios.tar.gz",$basePath_INTERCAMBIOS_Backup . DS . $fileTARGZ,FTP_BINARY);
			ftp_close($conn_id);
		endif;			
		
		//guardo los adjuntos de los socios
		$fileTARGZ = "socio_novedades_".date('Ymd').".tar.gz";
		$CMD = "find * -name \socio_novedades_*.tar.gz -mtime +90 -exec rm {} \;";
		exec($CMD);		
		$CMD = $zipCMD . $basePath_SOCIONOVEDADES_ADJ_Backup . DS . $fileTARGZ ." ". WWW_ROOT . "files" . DS . "socios" . DS . "novedades";
		exec($CMD);				
		
		if(file_exists($basePath_SOCIONOVEDADES_ADJ_Backup . DS . $fileTARGZ)):
			$conn_id = $this->conectarFTP_LOCAL();
			ftp_put($conn_id,$pathFTP_Local . DS . "socio_novedades.tar.gz",$basePath_SOCIONOVEDADES_ADJ_Backup . DS . $fileTARGZ,FTP_BINARY);
			ftp_close($conn_id);
		endif;		
		
		
		//guardo la carpeta DATA de Mysql *** IMPORTANTE ***
		//DETENER EL SERVICIO DE MYSQL
//		$fileTARGZ = "mysqlDATAFolder_".date('D').".tar.gz";
//		$CMD = $zipCMD . $basePath_MSQL_Backup . DS . $fileTARGZ . " " . $basePath_MSQL_DATAFOLDER . DS;
//		exec($CMD);

		$fileTARGZ = $basePath_MSQL_Backup . DS . "mysql_binary_".date('D').".tar.gz";
		$CMD = APP . DS. "vendors" . DS ."backup_binario.sh $basePath_MSQL_DATAFOLDER $fileTARGZ";
		
//		exec($CMD);
//		return;			
		
		//ARRANCAR EL SERVICIO DE MYSQL
		
		
		if(file_exists($basePath_MSQL_Backup . DS . $fileTARGZ)):
			$conn_id = $this->conectarFTP_LOCAL();
			ftp_put($conn_id,$pathFTP_Local . DS . "mysql_binary.tar.gz",$basePath_MSQL_Backup . DS . $fileTARGZ,FTP_BINARY);
			ftp_close($conn_id);
		endif;			
		
		
		//CREO UN BACKUP DE LA TABLA TEMPORAL
		$fileBckTmp = $basePath_MSQL_Backup . DS . "diario" . DS . "temporal_".date('D').".sql";
		$CMDBCKOPT = " --opt";
		$CMDBCKOPT .= " --single-transaction";
		$CMDBCKOPT .= " --comments";
		$CMDBCKOPT .= " --dump-date";
		$CMDBCKOPT .= " --no-autocommit";
		$CMDBCKOPT .= " --password=" . $passDB;
		$CMDBCKOPT .= " --user=" . $userDB;
		$CMDBCKOPT .= " temporal";
		$CMDBCKOPT .= " --result-file=" . $fileBckTmp;
		
		$CMD = $MSQL_DUMP_COMMAND . $CMDBCKOPT;
		exec($CMD);		
		
		if(file_exists($fileBackup)):
			//copio el backup en el server FTP
			$conn_id = $this->conectarFTP_LOCAL();
			
			if(!$conn_id) return;
	
			//creo un tar con el backup
//			if(file_exists($fileBckTmp))$CMD = $zipCMD . $basePath_MSQL_Backup . DS . "backup_diario.tar.gz  $fileBackup $fileBckTmp $fileBackup_v1";
//			else $CMD = $zipCMD . $basePath_MSQL_Backup . DS . "backup_diario.tar.gz  $fileBackup $fileBackup_v1";
//			exec($CMD);		
//			
//			if(!ftp_put($conn_id,$pathFTP_Local . DS . "backup_diario.tar.gz",$basePath_MSQL_Backup . DS . "backup_diario.tar.gz",FTP_BINARY)){
//				trigger_error("SE PRODUJO UN ERROR AL INTENTAR SUBIR EL ARCHIVO AL SERVIDOR LOCAL 192.168.0.128");
//			}
			
			if(file_exists($fileBackup)){
				$CMD = $zipCMD . $basePath_MSQL_Backup . DS . "backup_diario_sigem.tar.gz  $fileBackup";
				exec($CMD);
			}
			if(file_exists($fileBackup_v1)){
				$CMD = $zipCMD . $basePath_MSQL_Backup . DS . "backup_diario_aman.tar.gz  $fileBackup_v1";
				exec($CMD);
			}
			if(file_exists($fileBckTmp)){
				$CMD = $zipCMD . $basePath_MSQL_Backup . DS . "backup_diario_temporal.tar.gz  $fileBckTmp";
				exec($CMD);
			}

			if(file_exists($basePath_MSQL_Backup . DS . "backup_diario_sigem.tar.gz")){
				if(!ftp_put($conn_id,$pathFTP_Local . DS . "backup_diario_sigem.tar.gz",$basePath_MSQL_Backup . DS . "backup_diario_sigem.tar.gz",FTP_BINARY)){
					trigger_error("SE PRODUJO UN ERROR AL INTENTAR SUBIR EL ARCHIVO backup_diario_sigem.tar.gz AL SERVIDOR LOCAL 192.168.0.128");
				}
			}
			if(file_exists($basePath_MSQL_Backup . DS . "backup_diario_aman.tar.gz")){
				if(!ftp_put($conn_id,$pathFTP_Local . DS . "backup_diario_aman.tar.gz",$basePath_MSQL_Backup . DS . "backup_diario_aman.tar.gz",FTP_BINARY)){
					trigger_error("SE PRODUJO UN ERROR AL INTENTAR SUBIR EL ARCHIVO backup_diario_aman.tar.gz AL SERVIDOR LOCAL 192.168.0.128");
				}
			}
			if(file_exists($basePath_MSQL_Backup . DS . "backup_diario_temporal.tar.gz")){
				if(!ftp_put($conn_id,$pathFTP_Local . DS . "backup_diario_temporal.tar.gz",$basePath_MSQL_Backup . DS . "backup_diario_temporal.tar.gz",FTP_BINARY)){
					trigger_error("SE PRODUJO UN ERROR AL INTENTAR SUBIR EL ARCHIVO backup_diario_temporal.tar.gz AL SERVIDOR LOCAL 192.168.0.128");
				}
			}			
			ftp_close($conn_id);
		endif;
		
		//lo mando al mutualaman.com
//		$conn_id = $this->conectarFTP_REMOTO();
//		if(!$conn_id) return;		
//
//		if(!ftp_put($conn_id,"sigem_db_diario.tar.gz","/var/backups/mysql/sigem_db_diario.tar.gz",FTP_BINARY)){
//			trigger_error("SE PRODUJO UN ERROR AL INTENTAR SUBIR EL ARCHIVO AL SERVIDOR REMOTO mutualaman.com");
//		}
//		ftp_quit($conn_id);
		
		//borro el tar
		$CMD = "rm -rf $basePath_MSQL_Backup" . DS . "aman_diario.tar.gz";
		exec($CMD);
		
		
		
		//los sabados creo el back acumulado
		if(date('N') == 6){
			
			$fileTARGZ = "backup_".date('Ymd').".tar.gz";
			
			//creo un backup
			$CMD = $zipCMD . $basePath_MSQL_Backup . DS . "acumulado" . DS . "$fileTARGZ $fileBackup";
			exec($CMD);
			
			//borro todos los sql
			if(file_exists($basePath_MSQL_Backup . DS . "acumulado" . DS . $fileTARGZ) && filesize($basePath_MSQL_Backup . DS . "acumulado" . DS . $fileTARGZ) > 1000){
				$CMD = "rm -rf $basePath_MSQL_Backup" . DS . 'diario' . DS . "*.sql";
				exec($CMD);
			}
			
			//mando al 128 el acumulado
			$conn_id = $this->conectarFTP_LOCAL();
			ftp_put($conn_id,"sigem_db_semanal.tar.gz",$basePath_MSQL_Backup . DS . "acumulado" . DS . $fileTARGZ,FTP_BINARY);
			ftp_close($conn_id);
			
			
		}		
		
		
		//copio todo al var del disco local
//		if(!is_dir(DS . "var" . DS . "backups" . DS . "sigem")) mkdir(DS . "var" . DS . "backups" . DS . "sigem");
//		$CMD = "cp -r $basePath_Backup " . DS . "* " . DS . "var" . DS . "backups" . DS . "sigem";	
//		exec($CMD);
		
		
		//verifico si esta el backup de aman_db (v1)
//		if(file_exists("/home/mutual/aman_db_diario.sql")):
//			//creo un tar
//			$CMD = "tar czfv /home/mutual/aman_db_diario.tar.gz  /home/mutual/aman_db_diario.sql";
//			exec($CMD);
//			if(file_exists("/home/mutual/aman_db_diario.tar.gz")):
//				//lo mando al mutualaman.com
//				$conn_id = $this->conectarFTP_REMOTO();
//				if(!$conn_id) return;		
//				ftp_put($conn_id,"aman_db_diario.tar.gz","/home/mutual/aman_db_diario.tar.gz",FTP_BINARY);
//				ftp_quit($conn_id);
//				$CMD = "rm -rf /home/mutual/aman_db_diario.tar.gz";
//				exec($CMD);	
//			endif;
//		endif;

		return;
		
	}
	
	
	function conectarFTP_LOCAL(){
		$id_ftp = ftp_connect('190.136.179.204',21);
		if(!ftp_login($id_ftp,'ftp_mutual','amanftp_180407')) return null;
		ftp_pasv($id_ftp,true);
		return $id_ftp;
	}
	
	function conectarFTP_REMOTO(){
		$id_ftp = ftp_connect('mutualaman.com',21);
		if(!ftp_login($id_ftp,'backup@mutualaman.com','aman_bck180407')) return null;
		ftp_pasv($id_ftp,true);
		return $id_ftp;
	}	
	
	
}

?>
<?php
/*
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php backup_aman 1 -app /home/adrian/dev/www/sigem/app/
 * ARGREGAR AL CRONTAB
 * editar el archivo crontab -e
 * 
 * 0 0 * * * * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php backup_aman 1 -app /home/mutualam/public_html/sigem/app/
 * 0 0 * * * * /usr/bin/php5 /home/mutual22/public_html/sigem/cake/console/cake.php backup_aman 1 -app /home/mutual22/public_html/sigem/app/
 * 
 * /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php backup_aman 0 -app /home/adrian/Trabajo/www/sigemv2/app/
 */
class BackupAmanShell extends Shell{

	// var $uses = array('v1.Solicitud');
	
	function main(){

		
		//LIMPIAR TABLAS TEMPORALES
		App::import('Model','v1.Solicitud');
		$oSOLICITUD = new Solicitud();		

		
        $sql = "DELETE FROM process_remote_status";
		$oSOLICITUD->query($sql);
        
		$sql = "DELETE FROM process_remote_params";
		$oSOLICITUD->query($sql);
		
		$sql = "DELETE FROM process_remote_status";
		$oSOLICITUD->query($sql);



		$db = & ConnectionManager::getDataSource($oSOLICITUD->useDbConfig);
		
		$userDB = $db->config['login'];
		$passDB = $db->config['password'];
		$name_DB = $db->config['database'];

		
		$BASE_DIR = APP.DS."plugins".DS."seguridad".DS."views".DS."backups".DS."files".DS;
		$FILE_BCK = $BASE_DIR.$name_DB."_".date('Ymd').".sql";
		
		$BASE_AUDITORIA_AMAN = dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR.'aman'.DIRECTORY_SEPARATOR.'root'.DIRECTORY_SEPARATOR.'auditoria'.DIRECTORY_SEPARATOR;
		
		if(date('N') == 6):
			$CMD = "find $BASE_DIR* -name \\$name_DB *.sql.gz -mtime +7 | xargs rm -f";
			exec($CMD);            
			$CMD = "find $BASE_DIR* -name \\$name_DB *.sql.tgz -mtime +7 | xargs rm -f";
			exec($CMD);
			#COMPACTO Y GUARDO LAS AUDITORIAS
			$CMD = "find $BASE_DIR* -name \gestion_auditoria_*.tar.gz -mtime +7 | xargs rm -f;";
			exec($CMD);
			$CMD = "find $BASE_DIR* -name \gestion_auditoria_*.tgz -mtime +7 | xargs rm -f;";
			exec($CMD);            
			// $fileTARGZ = "gestion_auditoria_".date('Ymd').".tgz";
			// $CMD = "tar czvf ".$BASE_DIR.$fileTARGZ. " " . $BASE_AUDITORIA_AMAN . "AUDITORIA*.log";
			$fileTARGZ = "gestion_auditoria_".date('Ymd').".tar.bz2";
			$CMD = "tar -cjvf ".$BASE_DIR.$fileTARGZ. " " . $BASE_AUDITORIA_AMAN . "AUDITORIA*.log";            
			exec($CMD);
			if(file_exists($BASE_DIR.$fileTARGZ)){
				$CMD = "rm -rf ".$BASE_AUDITORIA_AMAN."AUDITORIA*.log";
				exec($CMD);
			}
			#BORRO LOS ERRORES
			$CMD = "find ".$BASE_AUDITORIA_AMAN."* -name \LOG_ERRORES_*.log -mtime +7 | xargs rm -f;";
			exec($CMD);						
		endif;
		
//		$CMD = "/usr/bin/mysqldump --add-drop-table --create-options --disable-keys --no-create-db --extended-insert --single-transaction --comments --dump-date --no-autocommit --password=$passDB --user=$userDB $name_DB | gzip > $FILE_BCK";
//		exec($CMD);

        // $oSOLICITUD->query("SET group_concat_max_len = 10240;");
        // $sql = "SELECT GROUP_CONCAT(table_name separator ' ') AS tables FROM information_schema.tables 
        //         WHERE table_schema = '$name_DB' and table_type = 'BASE TABLE';";
        // $data =  $oSOLICITUD->query($sql);
        // $tables = $data[0][0]['tables'];
        $BASE_DIR = APP."plugins".DS."seguridad".DS."views".DS."backups".DS."files".DS;
        $FILE = $BASE_DIR.$name_DB."_".date('Ymd').".bz2";
        $CMDBACK = "mysqldump --no-create-db --routines --skip-comments --add-drop-table --password=$passDB --user=$userDB  $name_DB | bzip2 > $FILE";
        shell_exec($CMDBACK);
        
        // $CMD = "/usr/bin/mysqldump --opt --single-transaction --comments --dump-date --no-autocommit --password=$passDB --user=$userDB $name_DB > $FILE_BCK";
        
		// exec($CMD);
        // $CMD = "tar czvf ".$FILE_BCK.".tgz $FILE_BCK";
        // exec($CMD);
        // $CMD = "rm $FILE_BCK";
        // exec($CMD);       
		
	}
	
	
	
}
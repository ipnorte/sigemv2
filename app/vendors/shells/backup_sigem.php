<?php
/*
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php backup_sigem 1 -app /home/adrian/trabajo/www/sigem/app/
 *
 * ARGREGAR AL CRONTAB
 * editar el archivo crontab -e
 *
 * 0 0 * * * * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php backup_sigem 1 -app /home/mutualam/public_html/sigem/app/
 * 0 0 * * * * /usr/bin/php5 /home/mutual22/public_html/sigem/cake/console/cake.php backup_sigem 1 -app /home/mutual22/public_html/sigem/app/
 * 0 0 * * * * /usr/bin/php5 /home/cordobas/public_html/solydar/cake/console/cake.php backup_sigem 1 -app /home/cordobas/public_html/solydar/app/
 *
 * /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php backup_sigem 0 -app /home/adrian/Trabajo/www/sigemv2/app/
 */

// BORRAR ARCHIVOS ANTERIORES A 5 DIAS
if(!defined('CMD_BORRADO_POR_FECHA')){define('CMD_BORRADO_POR_FECHA', '-mtime +3 -type f -exec rm -f {} \;');}



class BackupSigemShell extends Shell{

    var $uses = array('Shells.Asincrono');

    function main(){

        //LIMPIAR TABLAS TEMPORALES

        $sql = "DELETE FROM asincrono_errores;";
        $this->Asincrono->query($sql);

        $sql = "DELETE FROM asincrono_temporal_detalles;";
        $this->Asincrono->query($sql);

        $sql = "DELETE FROM asincrono_temporales;";
        $this->Asincrono->query($sql);

        $sql = "DELETE FROM asincronos WHERE DATEDIFF(now(),created) > 30";
        $this->Asincrono->query($sql);

        $db = & ConnectionManager::getDataSource($this->Asincrono->useDbConfig);

        $userDB = $db->config['login'];
        $passDB = $db->config['password'];
        $name_DB = $db->config['database'];

        $BASE_DIR = APP."plugins".DS."seguridad".DS."views".DS."backups".DS."files".DS;

        if(date('N') == 6):

            #LIMPIO ARCHIVOS TEMPORALES
            $FOLDER = WWW_ROOT . "files" . DS . "reportes" . DS;
            $CMD = "find $FOLDER*.xls " . CMD_BORRADO_POR_FECHA;
            shell_exec($CMD);
            $FOLDER = WWW_ROOT . "graphics" . DS;
            $CMD = "find $FOLDER*.png " . CMD_BORRADO_POR_FECHA;
            shell_exec($CMD);


            #BORRO LOS BACKUPS ANTERIORES A UNA SEMANA (7 DIAS)
            $CMD = "find $BASE_DIR* -name \\".$name_DB."_*.bz2 " . CMD_BORRADO_POR_FECHA;
            shell_exec($CMD);

            #COMPACTO Y GUARDO LAS AUDITORIAS
            $CMD = "find $BASE_DIR* -name \sigem_auditoria_*.bz2 " . CMD_BORRADO_POR_FECHA;
            shell_exec($CMD);

            $CMD = "find $BASE_DIR* -name \documentacion_solicitudes_*.tar.bz2 " . CMD_BORRADO_POR_FECHA;
            shell_exec($CMD);

            $fileTARGZ = "sigem_auditoria_".date('Ymd').".tar.bz2";
            $CMD = "tar -cjvf ".$BASE_DIR.$fileTARGZ. " " . LOGS . "*.log";


            shell_exec($CMD);

            if(file_exists($BASE_DIR.$fileTARGZ)){
                $CMD = "find ".LOGS."*.log " . CMD_BORRADO_POR_FECHA;
                shell_exec($CMD);
            }

            #BORRO LOS LOG DE SQL
            $CMD = "find ".LOGS."* -name \SQL_*.log " . CMD_BORRADO_POR_FECHA;
            shell_exec($CMD);

            #BORRO LOS ERRORES
            $CMD = "find ".WWW_ROOT."files/logs/* -name \ERRORES_*.log " . CMD_BORRADO_POR_FECHA;
            shell_exec($CMD);

            #BACKUP DE IMAGENES DE SOLICITUDES
            if(is_dir(WWW_ROOT . "files" . DS . "solicitudes")){
                // $fileTARGZ = "documentacion_solicitudes_".date('Ymd').".tgz";
                // $CMD = "tar czvf ".$BASE_DIR.$fileTARGZ. " " . WWW_ROOT . "files" . DS . "solicitudes";
                $fileTARGZ = "documentacion_solicitudes_".date('Ymd').".tar.bz2";
                $CMD = "tar -cjvf ".$BASE_DIR.$fileTARGZ. " " . WWW_ROOT . "files" . DS . "solicitudes";
                shell_exec($CMD);	
            }                

        endif;

        ###############################################################################################
        # COPIAR EL ARCHIVO mutual.ini en el campo TEXTO_1 de la global datos para el codigo MUTU
        ###############################################################################################
        App::import('model','config.GlobalDato');
        $oGLOBAL = new GlobalDato();
        $oGLOBAL->id = 'MUTU';
        $oGLOBAL->saveField("texto_1", file_get_contents(CONFIGS.'mutual.ini'));

        $BASE_DIR = APP."plugins".DS."seguridad".DS."views".DS."backups".DS."files".DS;
        $FILE = $BASE_DIR.$name_DB."_".date('Ymd').".bz2";
        $CMDBACK = "mysqldump --no-create-db --routines --skip-comments --add-drop-table --single-transaction --quick --password=$passDB --user=$userDB  $name_DB | sed -E 's/DEFINER=`[^`]+`@`[^`]+`/DEFINER=CURRENT_USER/g' | bzip2 > $FILE";
        shell_exec($CMDBACK);

    }

}

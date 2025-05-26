<?php

/* ***********************************************************************************************
 * DESCARGA DE ARCHIVOS REMOTOS DE BACKUP POR LINEA DE COMANDOS
 * ADRIAN - 21/02/2015
 * ***********************************************************************************************
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// SERVER FTP
define("FPT_SERVER", "cordobasoft.com");

// DIRECTORIO LOCAL DONDE SE EJECUTA EL SCRIPT
define("LOCAL_FOLDER", dirname(__FILE__).DIRECTORY_SEPARATOR.FPT_SERVER.DIRECTORY_SEPARATOR);

// ARRAY DE CUENTAS Y ARCHIVOS DE MYSQL A DESCARGAR
$cuentas = array(
    'backupsoluciones@cordobasoft.com' => array(
        'backupsoluciones*180407',array(
            "cordobas_soluciones_".date("Ymd").".sql.tgz"
        )
    ),
    'backup@mutual22deseptiembre.com' => array(
        'backup*180407',array(
            "mutual22_sigemdb_".date("Ymd").".sql.tgz"
        )
    ),    
    'backup@mutualaman.com' => array(
            'backup*180407',array(
                "gestion_db_".date("Ymd").".sql.tgz",
                "sigem_db_".date("Ymd").".sql.tgz"
            )
    ),
    
);

exec("wget http://cordobasoft.com/download/sigem_backup/mutualam_sigemdb.sql.tgz");
exec("wget http://cordobasoft.com/download/sigem_backup/mutual22_sigemdb.sql.tgz");
exec("wget http://cordobasoft.com/download/sigem_backup/cordobas_soluciones.sql.tgz");

//foreach ($cuentas as $userName => $params){
//    foreach($params[1] as $file){
//        FTP_DOWNLOAD_FILE($userName,$params[0],$file);
//    }
//    // SI ES SABADO BAJO LAS AUDITORIAS Y BORRO LOS ARCHIVOS DEJANDO LOS ULTIMOS 7
//    if(date('N') == 6){
//        FTP_DOWNLOAD_FILE($userName,$params[0],"sigem_auditoria_".date("Ymd").".tar.gz");
//        exec("find ".LOCAL_FOLDER.$userName."/*.sql.gz -mtime +7 | xargs rm -f;");
//        exec("find ".LOCAL_FOLDER.$userName."/*.tar.gz -mtime +7 | xargs rm -f;");       
//    }
//}
// BAJO LA AUDITORIA DEL GESTION
//FTP_DOWNLOAD_FILE("backup@mutualaman.com","backup*180407","gestion_auditoria_".date("Ymd").".tar.gz");



/**
 * FTP_DOWNLOAD_FILE
 * @param type $ftp_user_name
 * @param type $ftp_user_pass
 * @param type $file
 * @return type
 */
function FTP_DOWNLOAD_FILE($ftp_user_name, $ftp_user_pass,$file){
    $conn_id = ftp_connect(FPT_SERVER);
    if(!ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)){
        write_error_log ("ERROR DE CONEXION $ftp_user_name");
        return;
    }
    if(!is_dir(LOCAL_FOLDER.$ftp_user_name)){
        mkdir(LOCAL_FOLDER.$ftp_user_name, 0777, true);
    }
    // fijo un network timeout a 2 horas
    ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 72000);
    if(!ftp_get($conn_id,LOCAL_FOLDER.$ftp_user_name.DIRECTORY_SEPARATOR.$file, $file, FTP_BINARY)){
        write_error_log("no se pudo descargar el archivo $file [$ftp_user_name]");
    }
    ftp_close($conn_id);
}

/**
 * write_error_log
 * @param type $mensaje
 */
function write_error_log($mensaje){
    $gestor = fopen(LOCAL_FOLDER."ERROR_".date('Ymd').".log", "a");
    fwrite($gestor, date('Y-m-d H:i:s')."\t".$mensaje."\n\r");
    fclose($gestor);
}

?>
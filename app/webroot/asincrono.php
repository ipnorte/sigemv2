<?php

try {
    if (!defined('CONFIGS')) {
        define('CONFIGS', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR);
    }
    if (!defined('VENDORS')) {
        define('VENDORS',dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "vendors" . DIRECTORY_SEPARATOR);
    }

    if (!defined('CAKE_CONSOLA')) {
        define('CAKE_CONSOLA', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "cake" . DIRECTORY_SEPARATOR . "console" . DIRECTORY_SEPARATOR . "cake.php");
    }

    if (!defined('APP')) {
        define('APP', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "app");
    }
    
    if(!defined('WWW_ROOT')) {
        define('WWW_ROOT', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "webroot" . DIRECTORY_SEPARATOR);
    }

    require_once CONFIGS . "database.php";
    require_once VENDORS . "exec.php";
    
    $PID = filter_input(INPUT_GET, 'PID', FILTER_VALIDATE_INT);
    if (!$PID) {
        throw new Exception("PID inválido.");
    }

    $SHELL = null;
    $ACCION = filter_input(INPUT_GET, 'ACTION', FILTER_SANITIZE_STRING);
    if (in_array($ACCION, ['START', 'STOP', 'STATUS'])) {
        $SHELL = new exec();
    }

    switch($ACCION) {
        case 'STOP':
            $link = dbLink();
            $stmt = mysqli_prepare($link, "UPDATE asincronos SET estado = ?, msg = ? WHERE id = ?");
            $estado = 'S';
            $msg = 'DETENIDO POR EL USUARIO...';
            mysqli_stmt_bind_param($stmt, 'ssi', $estado, $msg, $PID);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al ejecutar la consulta: " . mysqli_error($link));
            }
            mysqli_stmt_close($stmt);
            $datos = __getAsincrono($PID);
            if (!empty($datos['SHELL_PID'])) {
                $SHELL->kill($datos['SHELL_PID']);
            }
            echo 1;
            mysqli_close($link);
            break;

        case 'START':
            $str = explode(" ", php_uname());
            $os = trim($str[0]);
            $php_pharser = $SHELL->get_phpcli();
            $link = dbLink();
            $sql = sprintf("SELECT proceso FROM asincronos WHERE id = %u", $PID);
            $result = mysqli_query($link, $sql);
            if (!$result) {
                throw new Exception("Error al ejecutar la consulta: " . mysqli_error($link));
            }
            $exec = "";
            while($row = $result->fetch_assoc()){
                $exec = $row['proceso'];
            }
            $SHELL_PID = 0;

            if ($os == 'Windows') {
                $WshShell = new COM("WScript.Shell") or die ("Could not initialise Object.");
                $CMD = "\"C:\\wamp64\\bin\\php\\php5.6.40\\php.exe\" \"" . CAKE_CONSOLA . "\" $exec $PID -app \"" . APP . "\"";
                $oExec = $WshShell->Run($CMD, 0, false);
                unset($WshShell);
            } else {
                $CMD = $php_pharser . " -d max_execution_time=6000 " . CAKE_CONSOLA . " " . $exec . " " . $PID . " -app " . APP;
                
                // $add_to_cron = "echo '* * * * * $CMD >> /home/adrian/trabajo/tmp/proceso_$PID.log 2>&1' | crontab -";
                // exec($add_to_cron);
                
                // $cronjob = "* * * * * $CMD >> /home/adrian/trabajo/tmp/proceso_$PID.log 2>&1";
                // file_put_contents('/home/adrian/trabajo/tmp/cronjob.txt', $cronjob . PHP_EOL);
                // exec('crontab /home/adrian/trabajo/tmp/cronjob.txt');
                // Limpiar el archivo temporal
                // unlink('/home/adrian/trabajo/tmp/cronjob.txt');                  
                
                
                $SHELL_PID = $SHELL->background($CMD);
                if (!$SHELL_PID) {
                    throw new Exception("Error al ejecutar el comando en segundo plano: $CMD");
                }
            }

            $sqlUpdate = sprintf("UPDATE asincronos SET shell_pid = $SHELL_PID, estado = 'P', porcentaje=0, msg='COMENZANDO...' WHERE id = %u", $PID);
            if (!mysqli_query($link, $sqlUpdate)) {
                throw new Exception("Error al actualizar asincronos: " . mysqli_error($link));
            }
            mysqli_close($link);
            break;

        case 'STATUS':
            $datos = __getAsincrono($PID);
            echo json_encode($datos);
            break;

        default:
            throw new Exception("Acción no válida.");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    exit("Se produjo un error: " . $e->getMessage());
}

/**
 * Obtiene la conexión a la base de datos.
 *
 * @return mysqli Enlace de conexión a la base de datos.
 * @throws Exception Si no se puede conectar a la base de datos.
 */
function dbLink(){
    $dbCONFIG = new DATABASE_CONFIG();
    $link = mysqli_connect(
        $dbCONFIG->default['host'], 
        $dbCONFIG->default['login'], 
        $dbCONFIG->default['password'], 
        $dbCONFIG->default['database']
    );

    if (!$link) {
        throw new Exception('Error al conectar a la base de datos: ' . mysqli_connect_error());
    }

    return $link;
}

/**
 * Obtiene información sobre el proceso asincrónico
 *
 * @param int $id ID del proceso asincrónico
 * @return array Información del proceso
 */
function __getAsincrono($id){
    $link = dbLink();
    $sql = sprintf("SELECT shell_pid, estado, total, contador, porcentaje, msg FROM asincronos WHERE id = %u", $id);
    $result = mysqli_query($link, $sql);

    if (!$result) {
        throw new Exception("Error al ejecutar la consulta: " . mysqli_error($link));
    }

    $datos = array(
        "SHELL_PID" => 0,
        "ERRORES" => 0,
        "TOTAL" => 0,
        "ACTUAL" => 0,
        "PORCENTAJE" => 0,
        "ESTADO" => null,
        "MENSAJE" => "..."
    );

    while($row = $result->fetch_assoc()){
        $datos["SHELL_PID"] = $row['shell_pid'];
        $datos["TOTAL"] = $row['total'];
        $datos["ACTUAL"] = $row['contador'];
        $datos["PORCENTAJE"] = $row['porcentaje'];
        $datos["ESTADO"] = $row['estado'];
        $datos["MENSAJE"] = (!empty($row['msg']) ? utf8_encode($row['msg']) : "...");
    }
    $result->close();

    $sql2 = sprintf("SELECT id FROM asincrono_errores WHERE asincrono_id = %u", $id);
    $result2 = mysqli_query($link, $sql2);
    $datos["ERRORES"] = $result2->num_rows;
    $result2->close();
    mysqli_close($link);

    return $datos;
}
?>

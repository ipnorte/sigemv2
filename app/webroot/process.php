<?php

try {
    if (!defined('CONFIGS')) {
        define('CONFIGS', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR);
    }
    if (!defined('VENDORS')) {
        define('VENDORS', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . basename(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "vendors" . DIRECTORY_SEPARATOR);
    }
    if (!defined('CAKE_CONSOLA')) {
        define('CAKE_CONSOLA', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "cake" . DIRECTORY_SEPARATOR . "console" . DIRECTORY_SEPARATOR . "cake.php");
    }
    if (!defined('APP')) {
        define('APP', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "app");
    }

    require_once CONFIGS . "database.php";
    require_once VENDORS . "exec.php";

    $PID = filter_input(INPUT_GET, 'PID', FILTER_VALIDATE_INT);
    if (!$PID) {
        throw new Exception("PID inválido.");
    }

    $SHELL = null;
    $ACCION = filter_input(INPUT_GET, 'ACTION', FILTER_SANITIZE_STRING);
    if (in_array($ACCION, ['START', 'STOP'])) {
        $SHELL = new exec();
    }

    switch ($ACCION) {
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
            while ($row = $result->fetch_assoc()) {
                $exec = $row['proceso'];
            }

            // Uso de proc_open en lugar de shell_exec
            $descriptors = array(
                0 => array("pipe", "r"),  // STDIN
                1 => array("pipe", "w"),  // STDOUT
                2 => array("pipe", "w")   // STDERR
            );

            if ($os == 'Windows') {
                $CMD = "\"C:\\wamp64\\bin\\php\\php5.6.40\\php.exe\" \"" . CAKE_CONSOLA . "\" $exec $PID -app \"" . APP . "\"";
            } else {
                $CMD = $php_pharser . " " . CAKE_CONSOLA . " " . $exec . " " . $PID . " -app " . APP;
            }

            // Ejecutar el comando con proc_open
            $process = proc_open($CMD, $descriptors, $pipes);
            if (is_resource($process)) {
                // Obtener el PID del proceso
                $status = proc_get_status($process);
                $processPID = $status['pid'];

                // Guardar el PID en la tabla de procesos (asincronos)
                $sql = sprintf("UPDATE asincronos SET shell_pid = %u WHERE id = %u", $processPID, $PID);
                if (!mysqli_query($link, $sql)) {
                    throw new Exception("Error al guardar el PID en la base de datos: " . mysqli_error($link));
                }

                // Leer la salida del proceso mientras se ejecuta
                while (!feof($pipes[1])) {
                    $output = fgets($pipes[1]);
                    if ($output !== false) {
                        $jsonOutput = json_decode($output, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            echo $output;   
                            if (function_exists('ob_flush') && ob_get_length()) {
                                ob_flush();
                            }                            
                            flush();
                        }
                    }
                }
                fclose($pipes[1]);
                // Almacenar el recurso del proceso en la sesión
                $_SESSION['PROP_OPEN'] = $process;
                proc_close($process);
            }

            break;

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

            // Recuperamos los detalles del proceso desde la base de datos
            $datos = __getAsincrono($PID);

            // Si el proceso tiene un PID guardado, lo matamos
            if (!empty($datos['SHELL_PID'])) {
                $process_pid = $datos['SHELL_PID'];

                // Ejecutar el comando para detener el proceso
                $SHELL->kill($process_pid);

                // Verificar si el recurso de proc_open está en la sesión
                if (isset($_SESSION['PROP_OPEN'])) {
                    $process = $_SESSION['PROP_OPEN'];

                    // Asegurarse de que el proceso sigue existiendo antes de cerrar
                    if (is_resource($process)) {
                        proc_close($process);
                        // Limpiar la variable de sesión para liberar recursos
                        unset($_SESSION['PROP_OPEN']);
                    }
                }
                echo 1; // Respuesta de éxito
            } else {
                throw new Exception("No se encontró el proceso con el PID especificado.");
            }

            mysqli_close($link);            
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
function dbLink() {
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

function __getAsincrono($id) {
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

    while ($row = $result->fetch_assoc()) {
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

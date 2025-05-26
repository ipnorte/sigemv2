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
                while (!feof($pipes[1])) {
                    $output = fgets($pipes[1]);
                    if ($output !== false) {
                        echo $output;
                        ob_flush();
                        flush();  // Fuerza el envío inmediato de la salida
                    }
                }
                fclose($pipes[1]);
                proc_close($process);
            }
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
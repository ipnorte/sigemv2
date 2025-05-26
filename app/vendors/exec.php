<?php

class exec {

    /**
     * Propiedad para almacenar la ruta del ejecutable PHP
     */
    private $phpCli;

    /**
     * Propiedad para almacenar la configuración desde un archivo INI
     */
    private $config;

    /**
     * Constructor que inicializa la ruta al PHP CLI y carga la configuración.
     */
    public function __construct(){
        $this->phpCli = $this->get_phpcli();
        $this->config = $this->loadConfig();
    }

    /**
     * Carga el archivo de configuración mutual.ini
     *
     * @return array|bool Retorna la configuración cargada o false si no existe
     * @throws Exception Si el archivo no existe
     */
    private function loadConfig() {
        $iniFilePath = CONFIGS . 'mutual.ini';
        if (file_exists($iniFilePath)) {
            return parse_ini_file($iniFilePath, true);
        } else {
            throw new Exception("El archivo de configuración no existe: " . $iniFilePath);
        }
    }

    /**
     * Obtiene el cliente PHP
     * Si está configurado en mutual.ini, toma esa configuración
     *
     * @return string Ruta al ejecutable PHP
     * @throws Exception Si el archivo de configuración no se pudo cargar
     */
    public function get_phpcli(){
        $str = explode(" ", php_uname());
        $os = trim($str[0]);
        $php_pharser = PHP_BINDIR . "/" . ($os == 'Windows' ? "php.exe" : "php5");
        // Verifica si hay una configuración específica para el intérprete de PHP
        $phpini = parse_ini_file(CONFIGS . 'mutual.ini', true);
        if (isset($phpini['general']['php_pharser']) && !empty($phpini['general']['php_pharser'])) {
            $php_pharser = $phpini['general']['php_pharser'];
        }
        return $php_pharser;                
    }
    
    /**
     * Ejecuta un comando en segundo plano con una prioridad opcional.
     *
     * @param string $Command El comando a ejecutar
     * @param int $Priority Prioridad del comando (opcional)
     * @return string PID del proceso en segundo plano
     * @throws Exception Si no se pudo ejecutar el comando
     */
    public function background($Command, $Priority = 0){
        $Command = escapeshellcmd($Command);
        $PID = null;
        if ($Priority) {
            $PID = shell_exec("nohup nice -n " . intval($Priority) . " $Command > /dev/null & echo $!");
        } else {
            $PID = shell_exec("$Command > /dev/null 2>&1 & echo $!");
        }

        if (!$PID) {
            throw new Exception("Error al ejecutar el comando en segundo plano: $Command");
        }

        return trim($PID);
    }

    /**
     * Verifica si el proceso con el PID especificado sigue corriendo.
     *
     * @param string $PID PID del proceso
     * @return bool True si el proceso sigue corriendo, False si no
     * @throws Exception Si hay un error al ejecutar el comando ps
     */
        public function is_running($PID){
            $PID = escapeshellarg($PID);  // Escapar el PID para evitar inyecciones
            $output = shell_exec("ps -p $PID -o comm=");
            if ($output === null || empty($output)) {
                // Si no se obtiene ninguna salida de 'ps', el proceso no está en ejecución
                return false;
            }
            return true;
        }


    /**
     * Mata un proceso con el PID especificado.
     *
     * @param string $PID PID del proceso
     * @return bool True si el proceso fue detenido, False si no estaba corriendo
     * @throws Exception Si no se pudo verificar o matar el proceso
     */
    public function kill($PID){
        $PID = escapeshellarg($PID);
        if ($this->is_running($PID)) {
            shell_exec("kill -9 $PID");
            return true;
        } else {
            return false;
        }
    }
}

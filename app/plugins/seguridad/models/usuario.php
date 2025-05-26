<?php

App::import('Vendor','SMTPMailer',array('file' => 'SMTPMailer.php'));

class Usuario extends SeguridadAppModel {

    var $name = 'Usuario';
    var $recursive = 0;
    var $validate = array(
        'usuario' => array(
            VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY, 'message' => 'Debe indicar un nombre de usuario'),
            'alphaNumeric' => array('rule' => array('checkUserName'), 'message' => 'El nombre de usuario ya existe!'),
        ),
        'email' => array(VALID_NOT_EMPTY => array('rule' => VALID_EMAIL, 'message' => 'Debe indicar una direccion de email válida'),),
        'descripcion' => array(VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY, 'message' => 'Debe indicar una descripcion o referencia a quien pertenece el usuario'),),
    );
    //The Associations below have been created with all possible keys, those that are not needed can be removed
    var $belongsTo = array(
        'Grupo' => array('className' => 'Grupo',
            'foreignKey' => 'grupo_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    function save($data = null, $validate = true, $fieldList = array()) {
//            if(isset($_SESSION['MUTUAL_INI']['general']['revalidar_clave_usuario_email']) && $_SESSION['MUTUAL_INI']['general']['revalidar_clave_usuario_email']){
//                $data['Usuario']['reset_password'] = 1;
//                $data['Usuario']['validado'] = 0;
////                $data['Usuario']['pin'] = null;
//            }else{
//                $data['Usuario']['reset_password'] = 0;
//                $data['Usuario']['validado'] = 0;
////                $data['Usuario']['pin'] = null;                
//            }
        return parent::save($data, $validate,$fieldList);
    }

    /**
     * checkUserName
     * verifica que un usuario no exista
     * @param unknown_type $data
     * @return unknown
     */
    function checkUserName($data) {
        if (!$this->isUnique(array('usuario' => $data['usuario'])))
            return FALSE;
        if (!$this->isUnique(array('usuario' => strtoupper($data['usuario']))))
            return FALSE;
        return $this->isUnique(array('usuario' => strtolower($data['usuario'])));
    }

    /**
     * resetPassword
     * Blanquea la clave del usuario asingando el nombre del usuario como clave inicial
     */
    function resetPassword() {
        
        if(empty($this->id)){
            parent::notificar("El identificador del Usuario es requerido");
            return FALSE;            
        }
        
        $this->unbindModel(array('belongsTo' => array('Grupo')));
        $us = $this->read(null, $this->id);
        
        if(empty($us)){
            parent::notificar("No se pudo cargar el usuario");
            return FALSE;
        }
//            $pws = Security::hash($us['Usuario']['usuario'], null, true);
//        $us['Usuario']['ultimo_password'] = $us['Usuario']['password'];
//        $us['Usuario']['password'] = Security::hash($us['Usuario']['usuario'], null, true);
//        $us['Usuario']['reset_password'] = 1;
//        $us['Usuario']['caduca'] = date("Y-m-d H:i:s");
//        $us['Usuario']['pin'] = null;
//        $this->save($us);

        $INI_FILE = parse_ini_file(CONFIGS . 'mutual.ini', true);
        $USERFROM = (isset($INI_FILE['general']['email_blank']) ? $INI_FILE['general']['email_blank'] : $INI_FILE['general']['email']);

        
        
        
        $SMTPMailer = (isset($INI_FILE['general']['php_mailer']) ? TRUE : FALSE);
        
        if($SMTPMailer && !empty($us['Usuario']['email'])){
            $mail = new SMTPMailer(true);
            try {
                
                $mail->sendEmailBlankPassword($us['Usuario']['email'], $us['Usuario']['usuario'], $us['Usuario']['descripcion']);
                
                $us['Usuario']['ultimo_password'] = $us['Usuario']['password'];
                $us['Usuario']['password'] = Security::hash($us['Usuario']['usuario'], null, true);
                $us['Usuario']['reset_password'] = 1;
                $us['Usuario']['caduca'] = date("Y-m-d H:i:s");
                $us['Usuario']['pin'] = null;
                $this->save($us);                
                
                return TRUE;
            } catch (Exception $exc) {
                parent::notificar($mail->ErrorInfo);
                return FALSE;
            }
            
        }else{
            
            #ENVIAR CORREO ELECTRONICO###################################################################
            $titulo = $USERFROM . " - Validacion de Usuario - PIN ";
            $cabeceras = 'From: ' . $USERFROM . "\r\n" .
                    'Reply-To: ' . $USERFROM . "\r\n";
            $mensaje = "------- BLANQUEO DE CLAVE -------\r\n";
            $mensaje .= "\r\n";
            $mensaje .= "EMITIDO POR: " . Configure::read('APLICACION.nombre_fantasia') . "\r\n";
            $mensaje .= "USUARIO: " . trim($us['Usuario']['usuario']) . "\r\n";
            $mensaje .= "DESCRIPCION: " . trim($us['Usuario']['descripcion']) . "\r\n";
            $mensaje .= "\r\n";
            $mensaje .= "El Administrador de Sistema blanqueo su clave. Deberá ingresar al sistema con su usuario y su clave temporal será el mismo nombre de usuario.";
            $mensaje .= "\r\n";
            
            if (!empty($us['Usuario']['email'])) {
                $RET = mail($us['Usuario']['email'], $titulo, $mensaje, $cabeceras, '-f' . $USERFROM);
                if(!$RET){
                    parent::notificar('No se pudo enviar el correo');
                    return FALSE;
                }
                $us['Usuario']['ultimo_password'] = $us['Usuario']['password'];
                $us['Usuario']['password'] = Security::hash($us['Usuario']['usuario'], null, true);
                $us['Usuario']['reset_password'] = 1;
                $us['Usuario']['caduca'] = date("Y-m-d H:i:s");
                $us['Usuario']['pin'] = null;
                $this->save($us);                
                return TRUE;
            }
            #############################################################################################             
            
        }
        
        
                       
//            $this->saveField('password',$pws);
//            $this->saveField('reset_password',1);
//            $this->saveField('caduca',date("Y-m-d H:i:s"));
    }

    function resetAllPasswords() {
        $users = $this->find('all');
        if (!empty($users)) {

            $INI_FILE = parse_ini_file(CONFIGS . 'mutual.ini', true);
            $USERFROM = (isset($INI_FILE['general']['email_blank']) ? $INI_FILE['general']['email_blank'] : $INI_FILE['general']['email']);

            $SMTPMailer = (isset($INI_FILE['general']['php_mailer']) ? TRUE : FALSE);
            

            foreach ($users as $user) {
                
                if($SMTPMailer && !empty($user['Usuario']['email'])){
                    
                    $mail = new SMTPMailer(true);
                    try {
                        
                        $mail->sendEmailBlankPassword($user['Usuario']['email'], $user['Usuario']['usuario'], $user['Usuario']['descripcion']);
                        $user['Usuario']['ultimo_password'] = $user['Usuario']['password'];
                        $user['Usuario']['password'] = Security::hash($user['Usuario']['usuario'], null, true);
                        $user['Usuario']['reset_password'] = 1;
                        $user['Usuario']['caduca'] = date("Y-m-d H:i:s");
                        $user['Usuario']['pin'] = null;
                        return $this->save($user);                         
                        
                    } catch (Exception $exc) {
                        parent::notificar($mail->ErrorInfo);
                        return FALSE;
                    }
                                    
                }else{
                    
                    #ENVIAR CORREO ELECTRONICO###################################################################
                    $titulo = $USERFROM . " - Validacion de Usuario - PIN ";
                    $cabeceras = 'From: ' . $USERFROM . "\r\n" .
                            'Reply-To: ' . $USERFROM . "\r\n";
                    $mensaje = "------- BLANQUEO DE CLAVE -------\r\n";
                    $mensaje .= "\r\n";
                    $mensaje .= "EMITIDO POR: " . Configure::read('APLICACION.nombre_fantasia') . "\r\n";
                    $mensaje .= "USUARIO: " . trim($user['Usuario']['usuario']) . "\r\n";
                    $mensaje .= "DESCRIPCION: " . trim($user['Usuario']['descripcion']) . "\r\n";
                    $mensaje .= "\r\n";
                    $mensaje .= "El Administrador de Sistema blanqueo su clave. Deberá ingresar al sistema con su usuario y su clave temporal será el mismo nombre de usuario.";
                    $mensaje .= "\r\n";
                    if (!empty($user['Usuario']['email'])) {
                        $RET = mail($user['Usuario']['email'], $titulo, $mensaje, $cabeceras, '-f' . $USERFROM);
                        if(!$RET){
                            parent::notificar('No se pudo enviar el correo: ' . $user['Usuario']['email']);
                            return FALSE;
                        }else{
                            $user['Usuario']['ultimo_password'] = $user['Usuario']['password'];
                            $user['Usuario']['password'] = Security::hash($user['Usuario']['usuario'], null, true);
                            $user['Usuario']['reset_password'] = 1;
                            $user['Usuario']['caduca'] = date("Y-m-d H:i:s");
                            $user['Usuario']['pin'] = null;
                            return $this->save($user);                            
                        }
                    }
                    #############################################################################################                      
                    
                }
                
//                $user['Usuario']['ultimo_password'] = $user['Usuario']['password'];
//                $user['Usuario']['password'] = Security::hash($user['Usuario']['usuario'], null, true);
//                $user['Usuario']['reset_password'] = 1;
//                $user['Usuario']['caduca'] = date("Y-m-d H:i:s");
//                $user['Usuario']['pin'] = null;
//                if ($this->save($user)) {
//                    #ENVIAR CORREO ELECTRONICO###################################################################
//                    $titulo = $USERFROM . " - Validacion de Usuario - PIN ";
//                    $cabeceras = 'From: ' . $USERFROM . "\r\n" .
//                            'Reply-To: ' . $USERFROM . "\r\n";
//                    $mensaje = "------- BLANQUEO DE CLAVE -------\r\n";
//                    $mensaje .= "\r\n";
//                    $mensaje .= "EMITIDO POR: " . Configure::read('APLICACION.nombre_fantasia') . "\r\n";
//                    $mensaje .= "USUARIO: " . trim($user['Usuario']['usuario']) . "\r\n";
//                    $mensaje .= "DESCRIPCION: " . trim($user['Usuario']['descripcion']) . "\r\n";
//                    $mensaje .= "\r\n";
//                    $mensaje .= "El Administrador de Sistema blanqueo su clave. Deberá ingresar al sistema con su usuario y su clave temporal será el mismo nombre de usuario.";
//                    $mensaje .= "\r\n";
//                    if (!empty($user['Usuario']['email']))
//                        mail($user['Usuario']['email'], $titulo, $mensaje, $cabeceras, '-f' . $USERFROM);
//                    #############################################################################################                        
//                }
            }
        }
    }

    function getUsersActivos($bindToGrupo = false, $grupo_id = null) {
        if (!$bindToGrupo)
            $this->unbindModel(array('belongsTo' => array('Grupo')));
        $conditions = array();
        $conditions['Usuario.activo'] = 1;
        if (!empty($grupo_id))
            $conditions['Usuario.grupo_id'] = $grupo_id;
        $users = $this->find('all', array('conditions' => $conditions, 'order' => array('Usuario.usuario')));
        return $users;
    }

    function getArchivosAuditorias() {

        $files = array();

        $handle = opendir(LOGS);

        while ($archivo = readdir($handle)) {
            if (filetype(LOGS . $archivo) == 'file') {
                if (substr($archivo, 0, 10) === 'AUDITORIA_')
                    $files[$archivo] = $archivo;
            }
        }
        ksort($files);
        return $files;
    }

    function password_check($data) {
        $return = true;
        $userLogon = $this->read(null, $_SESSION[$this->keyIdUserLogon]);
        $data['Usuario']['password_nuevo'] = trim($data['Usuario']['password_nuevo']);
        $data['Usuario']['password_nuevo_confirm'] = trim($data['Usuario']['password_nuevo_confirm']);



        if (isset($data['Usuario']['password'])) {

            if (empty($data['Usuario']['password'])) {
                parent::notificar("Debe indicar el password actual.");
                return false;
            }
            if ($userLogon['Usuario']['password'] !== Security::hash($data['Usuario']['password'], null, true)) {
                parent::notificar("El password actual NO ES CORRECTO.");
                return false;
            }
        }

        if (strpos(strtoupper(trim($data['Usuario']['password_nuevo'])), strtoupper(trim($userLogon['Usuario']['usuario']))) !== false) {
            parent::notificar("El password no puede contener el nombre de usuario.");
            $return = false;
        }
        if ($userLogon['Usuario']['password'] == Security::hash($data['Usuario']['password_nuevo'], null, true)) {
            parent::notificar("El password NO puede ser igual al actual.");
            $return = false;
        }
        if ($userLogon['Usuario']['ultimo_password'] == Security::hash($data['Usuario']['password_nuevo'], null, true)) {
            parent::notificar("El password NO puede ser igual al ultimo utilizado.");
            $return = false;
        }
        if (Security::hash($data['Usuario']['password_nuevo'], null, true) !== Security::hash($data['Usuario']['password_nuevo_confirm'], null, true)) {
            parent::notificar("El password nuevo y su verificación no coinciden.");
            $return = false;
        }

        if (strlen(trim($data['Usuario']['password_nuevo'])) < 8 || strlen(trim($data['Usuario']['password_nuevo'])) > 20) {
            parent::notificar("El password nuevo debe tener como mínimo de 8 a 20 caracteres.");
            $return = false;
        }

    //    if (!ereg("[a-zA-Z0-9\-\_\*\¿\?\#\¡\!]$", trim($data['Usuario']['password_nuevo']))){
    //        parent::notificar("El password no posee caractéres validos.");
    //        $return = false;            
    //    }

        // if (!preg_match("[a-zA-Z0-9\-\_\*\¿\?\#\¡\!]$", trim($data['Usuario']['password_nuevo']))){
        //     parent::notificar("El password no posee caractéres validos.");
        //     $return = false;            
        // }       

        // if (!preg_match("[a-zA-Z0-9]$", trim($data['Usuario']['password_nuevo']))) {
        //     parent::notificar("El password no posee caractéres validos.");
        //     $return = false;
        // }

        $letras = preg_replace('/[^a-zA-Z]+/', '', trim($data['Usuario']['password_nuevo']));
        if (strlen($letras) < 4) {
            parent::notificar("El password debe contener al menos 4 letras.");
            $return = false;
        }
        $numeros = preg_replace('/[^0-9]+/', '', trim($data['Usuario']['password_nuevo']));
        if (strlen($numeros) < 4) {
            parent::notificar("El password debe contener al menos 4 números.");
            $return = false;
        }





//        if (!ereg("[a-zA-Z]{4}", trim($data['Usuario']['password_nuevo']))){
//            parent::notificar("El password debe contener al menos cuatro 4 letras.");
//            $return = false;            
//        } 
//        if (!ereg("[\-\_\*\¿\?\#\¡\!]", trim($data['Usuario']['password_nuevo']))){
//            parent::notificar("El password debe contener al menos 1 caracter especial -_*¿?#¡!");
//            $return = false;            
//        }        
//        if (!ereg("[0-9]{4}", trim($data['Usuario']['password_nuevo']))){
//            parent::notificar("El password debe contener al menos 4 números.");
//            $return = false;            
//        }
//        debug($data);
//        debug($userLogon);
//        exit;

        if (!$return)
            return false;

        $this->unbindModel(array('belongsTo' => array('Grupo')));
        $this->id = $userLogon['Usuario']['id'];
        if(!empty($this->id)){
            $pws = Security::hash($data['Usuario']['password_nuevo'], null, true);
            $this->saveField('password', $pws);
            $this->saveField('reset_password', 0);
            $this->saveField('pin', null);
            $mkTFC = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $fechaCaduca = $this->addDayToDate($mkTFC, 90);
            $this->saveField('caduca', date("Y-m-d H:i:s", $fechaCaduca));
            $this->saveField('validado', 1);
            $this->saveField('ip_registro', filter_input(INPUT_SERVER,'REMOTE_ADDR'));            
        }


//        $_SESSION['Auth.Usuario'] = $this->read(null,$this->id);
//        debug($_SESSION['Auth.Usuario']);
//        debug($data);
//        exit;
        return true;
    }

    function enviar_email_PIN($email) {

        if (empty($email)) {
            parent::notificar("DEBE INDICAR UN EMAIL DE VALIDACION");
            return false;
        }
//        $userLogon = $this->read(null,$_SESSION[$this->keyIdUserLogon]);
        $userLogon = $this->get_user_logon();
        if (empty($userLogon) || empty($userLogon['Usuario']['id'])) {
            parent::notificar("ERROR AL RECUPERAR LOS DATOS DE LA SESSION");
            return false;
        }
        $this->id = $userLogon['Usuario']['id'];
        $PIN = parent::generarPIN(10);
        if (empty($PIN)) {
            parent::notificar("NO SE PUDO GENERAR UN PIN");
            return false;
        }
        if (!$this->saveField('pin', $PIN)) {
            parent::notificar("NO SE PUDO ASIGNAR UN PIN AL USUARIO");
            return false;
        }

        $IP = filter_input(INPUT_SERVER,'REMOTE_ADDR');
        $dns = gethostbyaddr($IP);
        $this->saveField('host_registro', $dns);
        
        
        $INI_FILE = parse_ini_file(CONFIGS . 'mutual.ini', true);
        $SMTPMailer = (isset($INI_FILE['general']['php_mailer']) ? TRUE : FALSE);
        if($SMTPMailer && !empty($userLogon['Usuario']['email'])){
            $mail = new SMTPMailer(true);
            try {
                $mail->sendEmailPINAcceso(
                        $userLogon['Usuario']['email'], 
                        $userLogon['Usuario']['usuario'], 
                        $PIN, 
                        $IP, 
                        $dns
                );                              
            } catch (Exception $exc) {
                parent::notificar($mail->ErrorInfo);
                return FALSE;
            }
                    
        }else{
            
            #ENVIAR CORREO ELECTRONICO###################################################################
            $titulo = Configure::read('APLICACION.nombre_fantasia') . " - Validacion de Usuario - PIN ";
            $cabeceras = 'From: ' . Configure::read('APLICACION.email') . "\r\n" .
                    'Reply-To: ' . Configure::read('APLICACION.email') . "\r\n";
            $mensaje = "------- REVALIDACION / CAMBIO DE PASSWORD -------\r\n";
            $mensaje .= "\r\n";
            $mensaje .= "EMITIDO POR: " . Configure::read('APLICACION.nombre_fantasia') . "\r\n";
            $mensaje .= "USUARIO: " . trim($userLogon['Usuario']['usuario']) . "\r\n";
            $mensaje .= "DESCRIPCION: " . trim($userLogon['Usuario']['descripcion']) . "\r\n";
            $mensaje .= "\r\n";
            $mensaje .= "PIN: $PIN\r\n";
            $mensaje .= "\r\n";
            $mensaje .= "IP: " . $IP . "\r\n";
            $mensaje .= "HOST: $dns\r\n";
            if (!mail($userLogon['Usuario']['email'], $titulo, $mensaje, $cabeceras, '-f' . Configure::read('APLICACION.email'))) {
                parent::notificar("NO SE PUDO ENVIAR EL EMAIL A " . $userLogon['Usuario']['email']);
                return false;
            }
            #############################################################################################            
            
        }
        
        




        return true;
    }

    function validar_PIN($pin) {
        if (empty($pin)) {
            parent::notificar("DEBE INDICAR EL PIN DE VALIDACION");
            return false;
        }
        $userLogon = $this->read(null, $_SESSION[$this->keyIdUserLogon]);
        if (empty($userLogon)) {
            parent::notificar("ERROR AL RECUPERAR LOS DATOS DE LA SESSION");
            return false;
        }
        if ($userLogon['Usuario']['pin'] === $pin) {
            $this->id = $userLogon['Usuario']['id'];
            $this->saveField('pin', NULL);
            return true;
        }
        parent::notificar("EL PIN PROPORCIONADO " . $pin . " NO PUDO SER VERIFICADO");
        return false;
    }

    function validar_clave($user, $clave, $claveConfirm = null) {

        $return = true;
        $claveConfirm = (empty($claveConfirm) ? $clave : $claveConfirm);

        if (strpos(strtoupper(trim($clave)), strtoupper(trim($user['Usuario']['usuario']))) !== false) {
            parent::notificar("El password no puede contener el nombre de usuario.");
            $return = false;
        }
        if ($user['Usuario']['password'] == Security::hash($clave, null, true)) {
            parent::notificar("El password NO puede ser igual al actual.");
            $return = false;
        }
        if ($user['Usuario']['ultimo_password'] == Security::hash($clave, null, true)) {
            parent::notificar("El password NO puede ser igual al ultimo utilizado.");
            $return = false;
        }
        if (Security::hash($clave, null, true) !== Security::hash($claveConfirm, null, true)) {
            parent::notificar("El password nuevo y su verificación no coinciden.");
            $return = false;
        }
        if (strlen(trim($clave)) < 8 || strlen(trim($clave)) > 20) {
            parent::notificar("El password nuevo debe tener como mínimo de 8 a 20 caracteres.");
            $return = false;
        }
        if (!preg_match("[a-zA-Z0-9]$", trim($clave))) {
            parent::notificar("El password no posee caractéres validos.");
            $return = false;
        }
        if (!preg_match("[a-zA-Z]{4}", trim($clave))) {
            parent::notificar("El password debe contener al menos cuatro 4 letras.");
            $return = false;
        }
        if (!preg_match("[0-9]{4}", trim($clave))) {
            parent::notificar("El password debe contener al menos 4 números.");
            $return = false;
        }

        return $return;
    }

    function check_status_logon($user) {
        if ($user['Usuario']['reset_password'])
            return false;
        $mkTFC_1 = mktime(date('H'), date('m'), date('i'), date('m'), date('d'), date('Y'));
        $mkTFC_2 = mktime(date('H', strtotime($user['Usuario']['caduca'])), date('m', strtotime($user['Usuario']['caduca'])), date('i', strtotime($user['Usuario']['caduca'])), date('m', strtotime($user['Usuario']['caduca'])), date('d', strtotime($user['Usuario']['caduca'])), date('Y', strtotime($user['Usuario']['caduca'])));
//        debug($user['Usuario']['caduca']);
//        debug($mkTFC_2);
//        exit;
        if (($mkTFC_1 > $mkTFC_2))
            return false;
        return true;
    }

    function get_user_logon() {
        $userLogon = $this->read(null, $_SESSION[$this->keyIdUserLogon]);
        return $userLogon;
    }

    function get_vendedorId_logon($cargarAsociados = false) {
        $userLogon = $this->read(null, $_SESSION[$this->keyIdUserLogon]);
        App::import('model', 'seguridad.grupo');
//        $oGRUPO = new Grupo();				
//        $GRUPO_ID = $oGRUPO->getGrupoVendedores();        
//        if($userLogon['Usuario']['grupo_id'] != $GRUPO_ID) return null;
        if (!$cargarAsociados) {
            return $userLogon['Usuario']['vendedor_id'];
        } else {

            /*             * *************************************** */
            //SACAR LINEA = 1;
            /*             * *************************************** */
            //$userLogon['Usuario']['vendedor_id'] = 1;
            /*             * *************************************** */
            if (!is_null($userLogon['Usuario']['vendedor_id'])) {

                $v_id = $userLogon['Usuario']['vendedor_id'];
                $vendedores_array = array();

                $sql = "SELECT
                        u.vendedor_id, v.supervisor_id 
                    FROM 
                        usuarios u
                    LEFT JOIN vendedores v ON u.vendedor_id = v.id
                    WHERE 
                        v.supervisor_id = $v_id;";

                $vendedores_array = $this->query($sql);
                $arreglo = array();

                if (!empty($vendedores_array)) {

                    foreach ($vendedores_array as $valor) {
                        array_push($arreglo, $valor['u']['vendedor_id']);
                    }
                }

                array_push($arreglo, $v_id);
                $vendedoresCadena = implode(',', $arreglo);

                return $vendedoresCadena;
            }else{
                return NULL;
            }
        }
        /*         * ************************************* */
        //return $userLogon['Usuario']['vendedor_id'];
    }

    function validar_user($user) {
        $ERROR = FALSE;
        if (empty($user['Usuario']['usuario'])) {
            parent::notificar("Debe indicar el nombre de usuario para el inicio de sesión.");
            $ERROR = TRUE;
        }
        if (!$this->checkUserName(array('usuario' => $user['Usuario']['usuario']))) {
            parent::notificar("Ya existe el usuario " . $user['Usuario']['usuario']);
            $ERROR = TRUE;
        }
        if (strpos($user['Usuario']['usuario'], " ")) {
            parent::notificar("El usuario NO puede contener espacios vacios.");
            $ERROR = TRUE;
        }
        if (empty($user['Usuario']['descripcion'])) {
            parent::notificar("Debe indicar el nombre completo a quien pertenece el usuario.");
            $ERROR = TRUE;
        }
        if (empty($user['Usuario']['email'])) {
            parent::notificar("Debe indicar una dirección de email valida.");
            $ERROR = TRUE;
        }

        return !$ERROR;
    }
    
    
    function borrar_usuario($id) {
        App::import('model','seguridad.UsuarioAcceso');
        $oUA = new UsuarioAcceso();	
        if(!$oUA->deleteAll("UsuarioAcceso.usuario_id = $id")){
            return false;
        }
        return $this->del($id);
    }
    

}

?>
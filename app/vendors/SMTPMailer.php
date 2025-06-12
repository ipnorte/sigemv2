<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PHPMailer
 *
 * @author adrian
 */

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


class SMTPMailer extends \PHPMailer\PHPMailer\PHPMailer{
    //put your code here
    public $INI_FILE;
    public function __construct() {
        parent::__construct(true);
        $INI_FILE = parse_ini_file(CONFIGS . 'mutual.ini', true);
        $this->INI_FILE = $INI_FILE;
        $from = ($INI_FILE['general']['php_mailer_from'] ? $INI_FILE['general']['php_mailer_from'] : $INI_FILE['general']['php_mailer_user']);
        $this->isSMTP();
        $this->SMTPAuth = true;
        $this->Host = $INI_FILE['general']['php_mailer_pop3_server'];
        $this->Username = $INI_FILE['general']['php_mailer_user'];
        $this->Password = $INI_FILE['general']['php_mailer_pass'];
        $this->Port = $INI_FILE['general']['php_mailer_pop3_port'];
        $this->SMTPSecure = true;
        $FROM = (isset($INI_FILE['general']['php_mailer_from']) ? $INI_FILE['general']['php_mailer_from'] : $INI_FILE['general']['php_mailer_user']);
        $this->setFrom($FROM,$INI_FILE['general']['nombre_fantasia']);
        $this->setLanguage('es', '/PHPMailer/language/');
        $this->SMTPDebug = 1;
    }
    
    public function sendEmailBlankPassword($userEmail,$userNick,$userDescrip){
        
        if(empty($userEmail)){
            throw new Exception("El email es requerido!");
        }
        
        $this->isHTML(TRUE);
        $this->Subject = $this->INI_FILE['general']['nombre_fantasia'] . ' - BLANQUEO DE CLAVE';
        
        $mensaje =  "<h4>BLANQUEO DE CLAVE</h4>
                    <p>El Administrador de Sistema inici&oacute; el proceso de blanqueo de su clave. 
                    <br/>Deber&aacute; ingresar al sistema con su <strong>usuario</strong> y su <strong>clave temporal</strong> 
                    indicada en este correo para iniciar el proceso de cambio de clave de acceso.
                    <br/>Por favor, NO RESPONDA ESTE CORREO.</p>
                    <table style=\"border: 1px solid\">
                        <tr>
                            <td>USUARIO:</td><td><strong>" . trim($userNick) . "</strong></td>                            
                        </tr>
                        <tr>
                            <td>CLAVE TEMPORAL:</td><td><strong>" . trim($userNick) . "</strong></td>
                        </tr>
                    </table>
                    <p>" . $this->INI_FILE['general']['nombre_fantasia'] . " - ".Configure::read('APLICACION.nombre'). " @v" . Configure::read('APLICACION.version')."</p>
                    ";       
        $this->Body = $mensaje;
        $this->addAddress($userEmail);
        $this->send();
    }
    
    public function sendEmailPINAcceso($userEmail,$userNick,$PIN,$remoteIP,$remoteDNS){
        if(empty($userEmail)){
            throw new Exception("El email es requerido!");
        }
        $this->isHTML(TRUE);
        $this->Subject = $this->INI_FILE['general']['nombre_fantasia'] . ' - PIN';
        
        
        $mensaje =  "<h4>BLANQUEO DE CLAVE - PIN</h4>
                    <p>Deber&aacute; copiar y pegar el mismo en donde se lo solicita para completar 
                    el proceso de cambio de clave de acceso.
                    <br/>
                    En caso de inconveniente, o que Ud. pierda este correo, tendr&aacute; que ingresar nuevamente al sistema con
                    su <strong>usuario</strong> y su <strong>clave temporal</strong> que se indican y solicitar 
                    nuevamente el env&iacute;o
                    del PIN.
                    <br/>Por favor, NO RESPONDA ESTE CORREO.</p>
                    <table style=\"border: 1px solid\">
                        <tr>
                            <td>USUARIO:</td><td><strong>" . trim($userNick) . "</strong></td>                            
                        </tr>
                        <tr>
                            <td>CLAVE TEMPORAL:</td><td><strong>" . trim($userNick) . "</strong></td>
                        </tr>
                    </table>
                    <p><strong>Su PIN de verificaci&oacute;n es:</strong></p>
                    <h2 style=\"color:green;\">$PIN</h2>
                    <p></p>        
                    <p>" . $this->INI_FILE['general']['nombre_fantasia'] . " - ".Configure::read('APLICACION.nombre'). " @v" . Configure::read('APLICACION.version')."</p>
                    <p>Registrado desde: $remoteIP ($remoteDNS)</p>
                    ";         
        
//        $mensaje = "<p><strong>BLANQUEO DE CLAVE :: PIN</strong></p>";
//        $mensaje .= "<table><tr><td>USUARIO</td><td><strong>" . trim($userNick) . "</strong></td></tr><tr><td>CLAVE TEMPORAL</td><td><strong>" . trim($userNick) . "</strong></td></tr></table>";
//        $mensaje .= "<p></p>";
//        $mensaje .= "<p>PIN: <h3>$PIN</h3></p>";
//        $mensaje .= "<p></p>";
//        $mensaje .= "<p>Su IP: $remoteIP ($remoteDNS)</p>";
//        $mensaje .= "<p>*** NO RESPONDA ESTE CORREO ***</p>";
        
        $this->Body = $mensaje;
        $this->addAddress($userEmail);
        $this->send();        
    }
    
    
    
    
    
}

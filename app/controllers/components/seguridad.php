<?php

App::import(array('Auth'));
/**
 * 
 * @author ADRIAN TORRES
 * @package general
 * @subpackage components
 */

class SeguridadComponent extends AuthComponent {

   
    function login($data = null){
        
        if(!parent::login($data)){
            return false;
        }else{
            $userLogin = $this->Session->read($this->sessionKey);
            if($userLogin['reset_password'] === 1){
                $this->redirect('/seguridad/usuarios/password_check');
            }else{
                App::import('model','seguridad.UsuarioAcceso');
                $oUA = new UsuarioAcceso();
                $oUA->registrar($userLogin['id']);
                $this->redirect('/home');
                return true;      
            }

        }
    }

    
    
}
?>
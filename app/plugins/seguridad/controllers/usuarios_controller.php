<?php

class UsuariosController extends SeguridadAppController {  
	var $name = 'Usuarios';  
//	var $scaffold; 

	
	function beforeFilter(){ 
		$this->Seguridad->allow('login','verify','auditoria','password_check','accesos','accesos_listar'); 
		parent::beforeFilter();  
 	}	
	
	
	function index(){
		$this->Usuario->recursive = 1;
//		$condiciones = array('conditions'=>array('Usuario.usuario <>' => "ADMIN"),'order' => array('Usuario.usuario'));
		$condiciones = array('Usuario.usuario <>' => "ADMIN");
		$this->paginate = array('limit' => 300,'order' => array('Usuario.usuario' => 'ASC'));
		$this->set('usuarios', $this->paginate(null,$condiciones));		
	}
	
	function login(){
		$this->Auditoria->log();
// 		$this->render(null,'login');
		$this->render('new_login',Configure::read('APLICACION.default_layout_login'));
	}
	
	
// 	function verify(){
// 		parent::unSetUserLogon();
// 		$render = "new_login_ajax";
// 		$ajax = false;
// 		$error = null;
// //		ECHO "PASO";
// //		EXIT;
// 		if(!$this->RequestHandler->isAjax()) $this->redirect('login');
// 		if(!isset($this->params['url']['h'])) $this->redirect('login');
// 		$hashKey = $this->params['url']['h'];
// 		$this->layout = Configure::read('APLICACION.default_layout_login');
// 		if($hashKey != parent::getHashKey()){
// 			$this->set('hashKey',parent::getHashKey());
// 			$error = null;
// 			$render = 'login_ajax_failure';
// 			$ajax = true;
// 		}else if (!empty($this->data) && $this->RequestHandler->isAjax()){
// 			$this->set('hashKey',parent::getHashKey());
// 			$conditions = array();
// 			$conditions['Usuario.usuario'] = $this->data['Usuario']['usuario'];
// 			$conditions['Usuario.password'] = Security::hash($this->data['Usuario']['password'], null, true);
// 			$usuario = $this->Usuario->find('all',array('conditions' => $conditions));
// 			if(!empty($usuario)):
// 				$usuario = $usuario[0];
// 				if(isset($usuario['Usuario']['activo']) && $usuario['Usuario']['activo'] == 0):
// 					$error = "La cuenta del Usuario " . $usuario['Usuario']['nombre'] . " NO ESTA ACTIVA.";
// 					$render = 'new_login_ajax_failure';
// 					$ajax = true;
// 				else:
// 					parent::setUserLogon($usuario);
// 					$render = 'new_login_ajax_sucess';
// 					$ajax = true;
// 				endif;
// 			else:
// 				$error = "Usuario o Contraseña Incorrecto";
// 				$render = 'new_login_ajax_failure';
// 				$ajax = true;
// 			endif;
// 		}
// 		$this->set('error',$error);
// 		$this->render($render,($ajax ? 'ajax' : Configure::read('APLICACION.default_layout_login')));
// 	}	

	
	
	function logout(){
		$this->Auditoria->log();  
		$this->Session->destroy();  
		$this->redirect($this->Seguridad->logout());  
	}

	/**
	 * pws
	 * Cambio de contraseña de acceso del usuario
	 */
	
	function pws(){
            $user = $this->Seguridad->user();
            $this->Usuario->bindModel(array('belongsTo' => array('Grupo')));
            $usuario = $this->Usuario->read(null,$user['Usuario']['id']);
            if(!$this->Usuario->check_status_logon($usuario)) $this->redirect('logout');
            $this->set('user',$usuario);
            if(!empty($this->data)){
                if($this->Usuario->password_check($this->data)){
                    $this->redirect('logout');
                }else{
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES: ",$this->Usuario->notificaciones);
                }                                        
            }
	}
	
	function datos_user(){
		$user = $this->Seguridad->user();
		$this->Usuario->bindModel(array('belongsTo' => array('Grupo')));
		$this->set('user',$this->Usuario->read(null,$user['Usuario']['id']));
		$this->render();		
	}

	function menu(){
		$user = $this->Seguridad->user();
		if(empty($user)) $this->redirect('logout');
		$this->set('grupo',$user['Usuario']['grupo_id']);		
		$this->render();
	}
	

	function userMenu(){
		$user = $this->Seguridad->user();
		if(empty($user)) $this->redirect('logout');
		$this->set('grupo',$user['Usuario']['grupo_id']);		
		$this->render();
	}
	
	
	function quick_menu(){
		$user = $this->Seguridad->user();
		if(empty($user)) $this->redirect('logout');
		$this->set('user',$this->Usuario->read(null,$user['Usuario']['id']));		
		$this->render();
	}
	
	function no_autorizado(){
		$this->Auditoria->log_controller($this);
		Configure::write('debug',0);
	}
	function no_disponible(){
           Configure::write('debug',0);
        }
	
	
	/**
	 * METODOS PARA EL ABM
	 */

	function reset_pws($id=null,$todos=0){
            $this->Auditoria->log();
            if(empty($id) && empty($todos)) $this->redirect(array('action'=>'listar'));
            
            if(!empty($todos)){
                $this->Usuario->resetAllPasswords();
                $this->redirect('index');
            }
            
            $user = $this->Usuario->read(null,$id); 
            $this->set('user',$user);
            
            if(!empty($this->data)){
                if(isset($this->data['Usuario']['email'])){
                    $user['Usuario']['email'] = $this->data['Usuario']['email']; 
                }
                if ($this->Usuario->save($user)){
                    $this->Usuario->id = $id;
//                    $this->Usuario->resetPassword();
                    if($this->Usuario->resetPassword()){
                        $this->Auditoria->log();
                        $this->Mensaje->ok('La clave fue reseteada correctamente!');
                        $this->redirect(array('action'=>'index'));	                        
                    }else{
                        $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES: ",$this->Usuario->notificaciones);
                    }
			
                }else{
                    $this->Mensaje->errorGuardar();
                }                
            }
            
//            if(!empty($user['Usuario']['email'])){
//                $this->Usuario->id = $id;
//                $this->Usuario->resetPassword();
//                $this->Mensaje->ok('La clave fue reseteada correctamente!');
//                $this->redirect(array('action'=>'index'));
//            }else{
//                $this->render('reset_pws');
//            }
             
            $this->render('reset_pws');
            
	}
	
	function edit($id=null){
		if(!$id && empty($this->data)) $this->redirect(array('action'=>'index'));
		if (!empty($this->data)) {
			if ($this->Usuario->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}
		}
                $this->Usuario->recursive = 0;
                $user = $this->Usuario->read(null,$id);
                $this->set('user',$user);
                $this->set('grupos',$this->requestAction('/seguridad/grupos/getList'));
                $this->data = $user;
	}
	
	function add(){
		if (!empty($this->data)){
                    if($this->Usuario->validar_user($this->data)){
			if ($this->Usuario->save($this->data)){
				$this->Auditoria->log();
				$this->Usuario->resetPassword();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index'));				
			}else{
				$this->Mensaje->errorGuardar();
			}                        
                    }else{
                        $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES: ",$this->Usuario->notificaciones);
                    }
                    
//			debug($this->data);
//                        exit;
//                        $this->data['Usuario']['reset_password'] = 1;
//			if ($this->Usuario->save($this->data)){
//				$this->Auditoria->log();
//				$this->Usuario->resetPassword();
//				$this->Mensaje->okGuardar();
//				$this->redirect(array('action'=>'index'));				
//			}else{
//				$this->Mensaje->errorGuardar();
//			}			
		}
		$this->set('grupos',$this->requestAction('/seguridad/grupos/getList'));
	}
	
	function del($id = null) {
		if (!$id) {
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Usuario->borrar_usuario($id)) {
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
			$this->redirect(array('action'=>'index'));
		}else{
			$this->Mensaje->error("El usuario esta asociado a un vendedor. No se puede eliminar.");
                        $this->redirect(array('action'=>'index'));
		}
	}		
	
	
	function auditoria(){
		$process = 0;
		if(!empty($this->data)){
			$process = 1;
		}
		$files = $this->Usuario->getArchivosAuditorias();
		$this->set('files',$files);
		$this->set('process',$process);
		
	}
    
    
    function password_check($paso=1){
        
        $user = $this->Seguridad->user();
        $this->Usuario->bindModel(array('belongsTo' => array('Grupo')));
        $this->set('user',$this->Usuario->read(null,$user['Usuario']['id']));
        
        $mkTFC_1 = mktime(date('H'),date('m'),date('i'),date('m'),date('d'),date('Y'));
        $mkTFC_2 = mktime(date('H',strtotime($user['Usuario']['caduca'])),date('m',strtotime($user['Usuario']['caduca'])),date('i',strtotime($user['Usuario']['caduca'])),date('m',strtotime($user['Usuario']['caduca'])),date('d',strtotime($user['Usuario']['caduca'])),date('Y',strtotime($user['Usuario']['caduca'])));
        
//        debug($user);
//        exit;
        
        if($user['Usuario']['validado'] && $user['Usuario']['reset_password'] == 0 && ($mkTFC_1 < $mkTFC_2)) $this->redirect('/home');
//        if($user['Usuario']['validado'] && $this->Usuario->check_status_logon($user)) $this->redirect('/home');
        
        if($paso == 1){
            $render = "revalidar_usuario_solicita_pin";
            if(!empty($this->data)){
                $enviado = $this->Usuario->enviar_email_PIN($this->data['Usuario']['email']);
                if($enviado){
                    $this->redirect('password_check/2');
                }else{
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES: ",$this->Usuario->notificaciones);
                }
            }
            
        }
        if($paso == 2){
            if(!empty($this->data)){
                if($this->Usuario->validar_PIN($this->data['Usuario']['pin_confirma'])){
                    $this->redirect('password_check/3');
                }else{
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES: ",$this->Usuario->notificaciones);
                }
            }
            $render = "revalidar_usuario_valida_pin";
        }
        if($paso == 3){
            $render = "revalidar_usuario_cambio_clave";
            if(!empty($this->data)){
                if($this->Usuario->password_check($this->data)){
                    $this->redirect('logout');
                }else{
                    $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES: ",$this->Usuario->notificaciones);
                }
            }            
        }
        
//        if(!empty($this->data)){
//            if($this->Usuario->password_check($this->data)){
//                $this->redirect('logout');
//            }else{
//                $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES: ",$this->Usuario->notificaciones);
//            }
//        }
        $this->render($render);
    }
	

	public function accesos($userId){
		App::import('model','seguridad.UsuarioAcceso');
		$oUA = new UsuarioAcceso();	
		$this->set('accesos',$oUA->getByUserId($userId));
		$this->Usuario->bindModel(array('belongsTo' => array('Grupo')));
		$usuario = $this->Usuario->read(null,$userId);
		$this->set('user',$usuario);		
	}


	public function accesos_listar(){
		App::import('model','seguridad.UsuarioAcceso');
		$oUA = new UsuarioAcceso();
		$accesos = NULL;
		if(!empty($this->data)){
			$fDesde = $oUA->armaFecha($this->data['UsuarioAcceso']['fecha_desde']);
			$fHasta = $oUA->armaFecha($this->data['UsuarioAcceso']['fecha_hasta']);
			$accesos = $oUA->listarByFechas($fDesde,$fHasta);
		}	
		$this->set('accesos',$accesos);
	}

}

?>

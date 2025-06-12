<?php

uses('L10n');

//ob_start ('ob_gzhandler');
header('Content-type: text/html; charset: UTF-8');
header('Cache-Control: must-revalidate');
$offset = -1;
$ExpStr = "Expires: " .gmdate('D, d M Y H:i:s',time() + $offset) . ' GMT';
header($ExpStr);

/**
 *
 * @author ADRIAN TORRES
 * @package general
 * @subpackage controller
 */

class AppController extends Controller{

	var $helpers = array('Controles','Util','Frm','Lightbox','CssMenu');
	var $components = array('Seguridad','Auditoria','RequestHandler','Mensaje','Util');

	/**
	 * Indica si se audita una URL
	 * @var unknown_type
	 */
	var $auditable = true;

	function __construct(){
            	
		//cargo en la session el archivo ini
		if(!isset($_SESSION['MUTUAL_INI'])):
			$file = CONFIGS.'mutual.ini';
			$_SESSION['MUTUAL_INI'] = parse_ini_file($file, true);
		endif;
			
		Configure::write('APLICACION',
				array(
				// 								'nombre'=>'SIGEM :: Sistema Integrado de Gesti&oacute;n Mutual',
						'nombre'=> $_SESSION['MUTUAL_INI']['general']['nombre_fantasia'],
						'version'=>'2.6.1',
						'folder_install' => 'sigem',
						'logo_grande' => $_SESSION['MUTUAL_INI']['general']['logo_grande'],
						'logo_chico'=> $_SESSION['MUTUAL_INI']['general']['logo_chico'],
						'logo_pdf' => $_SESSION['MUTUAL_INI']['general']['logo_pdf'],
						'nombre_fantasia' => $_SESSION['MUTUAL_INI']['general']['nombre_fantasia'],
						'domi_fiscal' => $_SESSION['MUTUAL_INI']['general']['domi_fiscal'],
						'matricula_inaes' => $_SESSION['MUTUAL_INI']['general']['matricula_inaes'],
						'cuit_mutual' => $_SESSION['MUTUAL_INI']['general']['cuit_mutual'],
						'telefonos' => $_SESSION['MUTUAL_INI']['general']['telefonos'],
						'email' => $_SESSION['MUTUAL_INI']['general']['email'],
						'beneficios_externos_render' => $_SESSION['MUTUAL_INI']['general']['beneficios_externos_render'],
						'modelo_padron_jubilados' => $_SESSION['MUTUAL_INI']['general']['modelo_padron_jubilados'],
						'modelo_padron_activos' => $_SESSION['MUTUAL_INI']['general']['modelo_padron_activos'],
						'intercambio_bancos' => $_SESSION['MUTUAL_INI']['intercambio'],
						'default_layout' => $_SESSION['MUTUAL_INI']['general']['default_layout'],
						'default_layout_login' => $_SESSION['MUTUAL_INI']['general']['default_layout_login'],
						'default_css' => $_SESSION['MUTUAL_INI']['general']['default_css'],
						'mutual_proveedor_id' => $_SESSION['MUTUAL_INI']['general']['mutual_proveedor_id'],
						'habilitar_modulo_v1' => $_SESSION['MUTUAL_INI']['general']['habilitar_modulo_v1'],
						'tipo_orden_dto_credito' => $_SESSION['MUTUAL_INI']['general']['tipo_orden_dto_credito']
				)
		);
// 		debug($_SESSION['MUTUAL_INI']);
		if(!defined('MUTUALPROVEEDORID')) define('MUTUALPROVEEDORID', Configure::read('APLICACION.mutual_proveedor_id'));
		
		$this->layout = Configure::read('APLICACION.default_layout');
                
		parent::__construct();

			
	}


	/**
	 * beforeFilter
	 *
	 * Application hook which runs prior to each controller action
	 *
	 * @access public
	 */
	function beforeFilter(){

            Configure::write('Config.language', 'spa');

            $this->Seguridad->autoRedirect = true;
            $this->Seguridad->userModel = 'Usuario';
            //		//Override default fields used by Auth component
            $this->Seguridad->fields = array('username'=>'usuario','password'=>'password');
            //Set application wide actions which do not require authentication
            $this->Seguridad->allow('datos_user','quick_menu','logout','no_autorizado','userMenu','opcionesMenu','no_disponible');
            //Set the default redirect for users who logout
            $this->Seguridad->logoutRedirect = '/seguridad/usuarios/login';
            //Set the default redirect for users who login
            $this->Seguridad->loginRedirect = '/home';
            //Extend auth component to include authorisation via isAuthorized action
            $this->Seguridad->authorize = 'controller';
            //Restrict access to only users with an active account
            $this->Seguridad->userScope = array('Usuario.activo = 1');
            $this->Seguridad->loginError = "Datos de ingreso incorrectos";
            $this->Seguridad->authError = " ";
            //Pass auth component data over to view files
            $user = $this->Seguridad->user();
            //		if(empty($user))$this->redirect('/seguridad/usuarios/login');
            $this->Session->write("ID_USER_LOGON_SIGEM",$user['Usuario']['id']);
            $this->Session->write("NAME_USER_LOGON_SIGEM",$user['Usuario']['usuario']);
            $this->set('Seguridad',$user);

            //SI NO EXISTE SESION ACTIVA PARA UN USUARIO REDIRECCIONO SIEMPRE Y CUANDO LA
            //PAGINA ACTUAL NO SEA EL LOGIN (PARA EVITAR UN BUCLE INFINITO)
            $plugin = low($this->params['plugin']);
            $controller = $this->params['controller'];
            $action = $this->params['action'];

            $thisController = low($controller);
            $thisAction = low($action);

            parent::beforeFilter();
            
	}

	/**
	 * despues de filtar el controlador y la accion
	 * genero la auditoria
	 * @return unknown_type
	 */
	function afterFilter(){
		parent::afterFilter();
	}

	/**
	 * beforeRender
	 *
	 * Application hook which runs after each action but, before the view file is
	 * rendered
	 *
	 * @access public
	 */
	function beforeRender(){
//		if($this->auditable) $this->Auditoria->log_URL();
		if($this->auditable) $this->Auditoria->log_controller($this);
// 		$this->Auditoria->log_URL();
		parent::beforeRender();
	}

	/**
	 * isAuthorized
	 *
	 * Called by Auth component for establishing whether the current authenticated
	 * user has authorization to access the current controller:action
	 *
	 * @return true if authorised/false if not authorized
	 * @access public
	 */
	function isAuthorized(){
            
		#######################################################################################################
		$userLogin = null;
		$userLogin = $this->Seguridad->user();
		$userLogin = $userLogin['Usuario'];
		
		// if(!empty($userLogin) && $userLogin['reset_password'] === 1) $this->redirect('/seguridad/usuarios/password_check');
		if(!empty($userLogin) && isset($userLogin['caduca'])){
			$mkTFC_1 = mktime(date('H'),date('m'),date('i'),date('m'),date('d'),date('Y'));
			$mkTFC_2 = mktime(date('H',strtotime($userLogin['caduca'])),date('m',strtotime($userLogin['caduca'])),date('i',strtotime($userLogin['caduca'])),date('m',strtotime($userLogin['caduca'])),date('d',strtotime($userLogin['caduca'])),date('Y',strtotime($userLogin['caduca'])));
			// if($mkTFC_1 > $mkTFC_2) $this->redirect('/seguridad/usuarios/password_check');
		}
		#######################################################################################################	
            
		$vendedor = $this->Seguridad->user('vendedor_id');
		if(!empty($vendedor) && $this->params['plugin'].'/'.$this->params['controller'] !== 'ventas/solicitudes'){
                    $this->redirect('/ventas/solicitudes');
		}	
		
		//		if(!$this->__permitir()) exit;
		if($this->__permitir())return true;
		else $this->redirect('/seguridad/usuarios/no_autorizado');
		//		return true;
	}

	/**
	 * redirige a una pagina especifica
	 */
	function noAutorizado(){
		$this->redirect('/seguridad/usuarios/no_autorizado');
	}

	function noDisponible(){
		$this->redirect('/seguridad/usuarios/no_disponible');
	}

	function __permitir(){

		$plugin = low($this->params['plugin']);
		$controller = $this->params['controller'];
		$action = $this->params['action'];
                
//                debug($plugin."/".$controller."/".$action);

		$thisController = low($controller);
		$thisAction = low($action);

		if($plugin.':'.$thisController.':'.$thisAction == 'seguridad.usuarios:logout')return true;

		$grupoUserId = $this->Seguridad->user('grupo_id');
		###############################################################################
	   	$RESET_PASSWORD = $this->Seguridad->user('reset_password');
	   	if($RESET_PASSWORD == 1) $this->redirect('/seguridad/usuarios/password_check');
		###############################################################################
			 
		if(!$this->Session->check("GRUPO_MENU_$grupoUserId")){
			App::import('Model', 'Seguridad.Grupo');
			$grupo = new Grupo;
			$grupo->id = $grupoUserId;
			$actions = $grupo->actions();
			$menus = $grupo->permisosHabilitados();
			$menus = Set::extract($menus,'{n}.url');
			$this->Session->write("GRUPO_MENU_$grupoUserId",$menus);
			$this->Session->write("GRUPO_ACTIONS_$grupoUserId",$actions);
		}
		 
		$this->allowedActions = $this->Session->read("GRUPO_MENU_$grupoUserId");
		$actions = $this->Session->read("GRUPO_ACTIONS_$grupoUserId");

		 
		$fullPath = (!empty($plugin) ? '/'.$plugin : '') . (!empty($controller) ? '/'.$controller : '') . (!empty($action) ? '/'.$action : '');
		$relativePath = (!empty($plugin) ? '/'.$plugin : '') . (!empty($controller) ? '/'.$controller : '');

//		$fullPath = (!empty($plugin) ? ( $plugin != $controller ? '/'.$plugin : '') : '') . (!empty($controller) ? '/'.$controller : '') . (!empty($action) ? '/'.$action : '');
//		$relativePath = (!empty($plugin) ? ( $plugin != $controller ? '/'.$plugin : '') : '') . (!empty($controller) ? '/'.$controller : '');

//		debug($this->allowedActions);
//		debug($fullPath);
//                debug($actions);
//                debug($relativePath);
//		exit;
//		$this->Auditoria->log_controller($this);
		
		foreach($this->allowedActions as $allowedAction){


			//	    	debug('allowedAction: '.$allowedAction);
			//	    	debug('fullPath: '.$fullPath);
			//	    	debug('relativePath: '.$relativePath);



			if($allowedAction == $fullPath){
				 
				return true;
			}

			if($allowedAction == $relativePath){

				switch ($action) {
					case 'index':
						if($actions[$action] == 1) return true;
						break;
					case 'view':
						if($actions[$action] == 1) return true;
						break;
					case 'edit':
						if($actions[$action] == 1) return true;
						break;
					case 'add':
						if($actions[$action] == 1) return true;
						break;
					case 'del':
						if($actions[$action] == 1) return true;
						break;
				}
			}

			//            if($allowedAction == $relativePath && $actions[$action] == 1){
			//            	return true;
			//            }
		}
		return false;
	}



}



?>

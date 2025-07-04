<?php
/* SVN FILE: $Id: routes.php 7690 2008-10-02 04:56:53Z nate $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 7690 $
 * @modifiedby		$LastChangedBy: nate $
 * @lastmodified	$Date: 2008-10-02 00:56:53 -0400 (Thu, 02 Oct 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */
//	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
	Router::connect('/', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	
	Router::connect('/usuarios/login', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/seguridad', array('controller' => 'home', 'action' => 'index', 'index'));
	Router::connect('/config/usuarios/login', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/pfyj/usuarios/login', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/mutual/usuarios/login', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/v1/usuarios/login', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/proveedores/usuarios/login', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/shells/usuarios/login', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/mutual', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/pfyj', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/proveedores', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/shells', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/config', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
	Router::connect('/v1', array('plugin'=>'seguridad','controller' => 'usuarios', 'action' => 'login', 'login'));
//	
//	
//	Router::connect('/tesoreria/usuarios/login', array('plugin'=>'usuarios','controller' => 'usuario', 'action' => 'index', 'index'));
//	Router::connect('/personas/usuarios/login', array('plugin'=>'usuarios','controller' => 'usuario', 'action' => 'index', 'index'));
//	Router::connect('/usuarios/usuarios/login', array('plugin'=>'usuarios','controller' => 'usuario', 'action' => 'index', 'index'));
//	Router::connect('/config/usuarios/login', array('plugin'=>'usuarios','controller' => 'usuario', 'action' => 'index', 'index'));
//	Router::connect('/tesoreria', array('plugin'=>'tesoreria','controller' => 'config', 'action' => 'index', 'index'));
//	Router::connect('/usuarios', array('controller' => 'home', 'action' => 'index', 'index'));
	
	
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
?>
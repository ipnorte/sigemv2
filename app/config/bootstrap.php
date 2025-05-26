<?php
/* SVN FILE: $Id: bootstrap.php 6311 2008-01-02 06:33:52Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
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
 * @since			CakePHP(tm) v 0.10.8.2117
 * @version			$Revision: 6311 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-02 01:33:52 -0500 (Wed, 02 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */
//EOF


if (!class_exists('File')) {
	require LIBS . 'file.php';
}

/**
 * Manejador de Errores
 * @param unknown_type $errno
 * @param unknown_type $errstr
 * @param unknown_type $errfile
 * @param unknown_type $errline
 * @param unknown_type $context
 */
function productionError($errno, $errstr, $errfile, $errline, $context){
	
	if ($errno === 2048 || error_reporting() === 0) {
		return;
	}
	
	$level = LOG_DEBUG;
	
	switch ($errno){
		case E_PARSE:
		case E_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			$level = LOG_ERROR;
			break;

		case E_WARNING:
		case E_USER_WARNING:
		case E_COMPILE_WARNING:
		case E_RECOVERABLE_ERROR:
			$error = 'Warning';
			$level = LOG_WARNING;
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			$level = LOG_NOTICE;
			break;
		default:
			return false;
//			break;			
								
	}
	
	$trace = Debugger::trace(array('start' => 1, 'depth' => '20'));
	$vars = array();
	foreach ((array)$context as $var => $value) {
		$vars[] = "\${$var}\t=\t" . Debugger::exportVar($value, 1);
	}	
	$codigoFuente = leerCodigoFuentePHP($errfile,$errline,2);
	
	$ERROR =  "#######################################################################################\n";
	$ERROR .= date("Y-m-d H:i:s")."\n";
	$ERROR .=  "#######################################################################################\n";
	$ERROR .= "ERROR: $errstr\nFILE: $errfile\nLINEA: $errline\n";
	if(!empty($trace)):
		$ERROR .= "*** TRACE *********************************************************\n$trace\n";
	endif;
	if(!empty($codigoFuente)):
		$ERROR .= "*** CODE *********************************************************\n";
		foreach($codigoFuente as $n => $code):
			$ERROR .= ($errline - 2 + $n)."\t".$code;
			if(($errline - 2 + $n) == $errline) $ERROR .= "\t --> $errstr";
			$ERROR .= "\n";
		endforeach;
	endif;
//	if(!empty($vars)):
//		$ERROR .= "*** ARGS **********************************************************\n" . implode("\n", $vars) . "\n";
//	endif;

	$fileLogName = WWW_ROOT . DS . "files" . DS . "logs" . DS . "ERRORES_".date("Ymd").".log";
	
	$log = new File($fileLogName, true);
	$log->append($ERROR);

}

set_error_handler('productionError');


/**
 * devuelve el codigo fuente de un archivo PHP
 * @param unknown_type $file
 * @param unknown_type $line
 * @param unknown_type $context
 */
function leerCodigoFuentePHP($file, $line, $context = 2) {
	$data = $lines = array();
	if (!file_exists($file)) {
		return array();
	}
	$data = explode("\n", file_get_contents($file));

	if (empty($data) || !isset($data[$line])) {
		return;
	}
	for ($i = $line - ($context + 1); $i < $line + $context; $i++) {
		if (!isset($data[$i])) {
			continue;
		}
		$string = str_replace(array("\r\n", "\n"), "",$data[$i]);
		$lines[] = $string;
//		$string = str_replace(array("\r\n", "\n"), "", highlight_string($data[$i], true));
//		if ($i == $line) {
//			$lines[] = '<span class="code-highlight">' . $string . '</span>';
//		} else {
//			$lines[] = $string;
//		}
	}
	return $lines;
}


function writeLog($var) {
    $filename = WWW_ROOT . DS . "files" . DS . "logs" . DS . "DEBUG_".date("Ymd").".log";
    $data = print_r($var, true);
    file_put_contents($filename, $data);    
}

?>
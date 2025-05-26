<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package general
 * @subpackage components
 */
class AuditoriaComponent extends Object{
    
	var $controller = true;
    var $modelClass = null;
    var $separator = "|";
    
    
    function __construct(){
		$this->separator = chr(174).chr(175);
		$this->separator = "\t";
    	parent::__construct();
    }
    
	
    function startup(&$controller){
		$this->controller =& $controller;
		$this->modelClass = $this->controller->modelClass;
		
    }


    /**
     * 
     * @deprecated
     * @author adrian [17/03/2012]
     */
	function log_URL() {
//		debug($this->controller->params['url']['url']);
		$message = 'url='. $this->controller->params['url']['url'].$this->separator;	
		if(isset($_SESSION['ID_USER_LOGON_SIGEM']) && isset($_SESSION['NAME_USER_LOGON_SIGEM']) && isset($_SERVER['REMOTE_ADDR'])){
			if(!empty($_SESSION['ID_USER_LOGON_SIGEM']))$user = up($_SESSION['NAME_USER_LOGON_SIGEM']);
			$message = $this->separator.$_SERVER['REMOTE_ADDR'].$this->separator.$user.$this->separator.$message;
			parent::log($message,$user.'_'.date('Ymd'));
		}else{
			$message = $this->separator.'BACKGROUND_PROCESS'.$this->separator.'SERVER'.$this->separator.$message;
			parent::log($message,'SERVER_'.date('Ymd'));
		}	    
	}	

	/**
	 * Genera una auditoria desde el metodo save de la clase AppModel para registar todas las operaciones
	 * de insertar y/o modificar datos.
	 * este metodo tiene que ser llamado desde los callBacks del modelo (beforeSave y beforeDelete)
	 * @param $data
	 * @return unknown_type
	 * @deprecated
	 */
	function log_MODELO(){
		$backTrace = debug_backtrace();
		$message = "";
		foreach($backTrace[1] as $key => $tr){
			if($key == 'function'){
				$message .= $key .':'.$tr . '|';
			}else if($key == 'object'){
				$message .= 'modelo:'.$tr->name.'|dbConfig='.$tr->useDbConfig.',table='.$tr->table.',id='.$tr->id;
				if(!empty($tr->data)){
					$message .= ',data={';
					foreach($tr->data[$tr->name] as $field => $value){
						$message .= $field.':'.$value.',';
					}
					$message .= '}';
				}
				$message .= '|';
			}
		}
		if(isset($_SESSION['ID_USER_LOGON_SIGEM']) && isset($_SESSION['NAME_USER_LOGON_SIGEM']) && isset($_SERVER['REMOTE_ADDR'])){
			if(!empty($_SESSION['ID_USER_LOGON_SIGEM']))$user = up($_SESSION['NAME_USER_LOGON_SIGEM']);
			$message = $this->separator.$_SERVER['REMOTE_ADDR'].$this->separator.$user.$this->separator.$message;
			parent::log($message,$user.'_'.date('Ymd'));
		}else{
			$message = $this->separator.'BACKGROUND_PROCESS'.$this->separator.'SERVER'.$this->separator.$message;
			parent::log($message,'SERVER_'.date('Ymd'));
		}		
		
	}
	
	/**
	 * Nuevo esquema de auditoria en archivo plano 
	 * Se guarda serializado el array que representa el dato
	 * Este metodo se llama desde el app_model (beforeSave)
	 * 
	 * @author adrian [17/03/2012]
	 * @param object $model
	 */
	function log_model_serialize(&$model,$action='SAVE'){
		
		$aLog = array();

		$aLog['model_name'] = $model->name; 
		$aLog['db_config'] = $model->useDbConfig;
		$aLog['table'] = $model->useTable;
		$aLog['primary_key_name'] = $model->primaryKey;
		$aLog['primary_key_value'] = $model->id;
		$aLog['data_model_serialized'] = serialize($model->data);
		
		$logStr = $action.$this->separator.addslashes(serialize($aLog));
		
		if(isset($_SESSION[$model->keyIdUserLogon]) && isset($_SESSION[$model->keyNameUserLogon]) && isset($_SERVER['REMOTE_ADDR'])){
			if(!empty($_SESSION[$model->keyIdUserLogon]))$user = up($_SESSION[$model->keyNameUserLogon]);
			$message = date('Y-m-d H:i:s').$this->separator.$user.$this->separator.$_SERVER['REMOTE_ADDR'].$this->separator.$logStr;
		}else{
			$message = date('Y-m-d H:i:s').$this->separator.'PROCESO'.$this->separator.$_SERVER['SCRIPT_FILENAME'].$this->separator.$logStr;
		}
		$this->__writeLog($message);
		
	}
	
	/**
	 * Nuevo esquema de auditoria para las URL.
	 * Este metodo se llama desde el app_controller en el metodo __permitir()
	 * 
	 * @author adrian [17/03/2012]
	 * @param unknown_type $controller
	 */
	function log_controller(& $controller){
		$logStr = "LINK" . $this->separator . $controller->here;
		$message = "";
		if(isset($_SESSION['ID_USER_LOGON_SIGEM']) && isset($_SESSION['NAME_USER_LOGON_SIGEM']) && isset($_SERVER['REMOTE_ADDR'])){
			if(!empty($_SESSION['ID_USER_LOGON_SIGEM']))$user = up($_SESSION['NAME_USER_LOGON_SIGEM']);
			$message = date('Y-m-d H:i:s').$this->separator.$user.$this->separator.$_SERVER['REMOTE_ADDR'].$this->separator.$logStr;
		// }else{
		// 	$message = date('Y-m-d H:i:s').$this->separator.'PROCESO'.$this->separator.$_SERVER['SCRIPT_FILENAME'].$this->separator.$logStr;
		}
//		$message = utf8_encode($message);
		$this->__writeLog($message);	
		//prepare
	}
	
	/**
	 * metodo privado que escribe un archivo de log
	 * 
	 * @author adrian [17/03/2012]
	 * @param unknown_type $message
	 */
	function __writeLog($message){
		
		//borro los archivos con las de 60 dias de antiguedad
//		$CMD = "find * -name \"" . APP ."AUDITORIA_*.log -mtime +90 -exec rm {} \;";
//		exec($CMD);
		
		$filename = LOGS . 'AUDITORIA_'.date('Ymd') . '.log';
		$log = new File($filename, true);
		$message .= "\r\n"; 
		if ($log->writable()) $log->append($message);
	}
	

    /**
     * no se usa mas
     * (non-PHPdoc)
     * @see cake/libs/Object#log($msg, $type)
     * @deprecated
     */
	function log() {
		$message = 'url='. $this->controller->params['url']['url'].'|';	
		if ($usuario = $this->controller->Seguridad->user()) {
	    	$user = up($usuario['Usuario']['usuario']);
	        $message = ''.$_SERVER['REMOTE_ADDR'].'|'.$usuario['Usuario']['usuario'].'|'.$message;
//			parent::log($message,$user.'_'.date('Ymd'));
	    }
	}	
	
	/**
	 * NO SE USA MAS
	 * @return unknown_type
	 * @deprecated
	 */
	function parseDataParams(){
		$params = "";
		if(!empty($this->controller->params['data'])){
			foreach($this->controller->params['data'] as $key => $value){
				if($key != '_Token'){
					$params .= '['.$key.']=';
					if(is_array($value)){
						$params .= '{';
						foreach($value as $key1 => $value1){
							
							if(is_array($value1)){
								$params .= $key1.':[';	
								foreach($value1 as $key2 => $value2){
									$params .= $key2.'=>'.$value2.'|';
								}
								$params .= '],';
							}else{
								$params .= $key1.':'.$value1.',';								
							}
						}
						$params .= '}';
					}else{
						$params .= $value;
					}
				}
			}
		}
		return 'post?'.$params;
	}
	
	function debugLog($message){
		parent::log($message);
	}
	

    
}
?>
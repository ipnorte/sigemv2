<?php
/**
 *
 * @author ADRIAN TORRES
 * @package general
 * @subpackage model
 */
if(!defined('MUTUALPROVEEDORID')) define('MUTUALPROVEEDORID', Configure::read('APLICACION.mutual_proveedor_id'));

class AppModel extends Model{

	var $impoMinDtoCBU = 5;
	var $impoMaxDtoCBU = 3000;

	var $notificaciones = array();

	var $sqlTrace = FALSE;

	/**
	 * indica si se actualizan los datos en la VERSION 1
	 * @var boolean
	 */
	var $v1 = false;
	/**
	 * indica si un modelo es auditable en la operacion SAVE
	 * @var boolean
	 */
	var $auditable = true;

	var $keyIdUserLogon = "ID_USER_LOGON_SIGEM";
	var $keyNameUserLogon = "NAME_USER_LOGON_SIGEM";
	
	var $CJP_COD_CONS = 207;
	var $CJP_COD_CSOC = 207;
	var $CJP_SCOD_CONS = 1;
	var $CJP_SCOD_CSOC = 0;

	var $ANSES_COD_CONS = 397109;
	var $ANSES_COD_CSOC = 324109;
	var $ANSES_SCOD_CONS = 0;
	var $ANSES_SCOD_CSOC = 0;	
	
	function __construct($id = false, $table = null, $ds = null){
            parent::__construct($id,$table,$ds);
            if(isset($_SESSION['MUTUAL_INI'])){
                
                $this->CJP_COD_CONS = (isset($_SESSION['MUTUAL_INI']['intercambio']['CJP_COD_CONS']) 
                        && !empty($_SESSION['MUTUAL_INI']['intercambio']['CJP_COD_CONS']) 
                        ? $_SESSION['MUTUAL_INI']['intercambio']['CJP_COD_CONS'] : $this->CJP_COD_CONS);
                
                $this->CJP_COD_CSOC = (isset($_SESSION['MUTUAL_INI']['intercambio']['CJP_COD_CSOC']) 
                        && !empty($_SESSION['MUTUAL_INI']['intercambio']['CJP_COD_CSOC']) 
                        ? $_SESSION['MUTUAL_INI']['intercambio']['CJP_COD_CSOC'] : $this->CJP_COD_CSOC);
                
                $this->CJP_SCOD_CONS = (isset($_SESSION['MUTUAL_INI']['intercambio']['CJP_SCOD_CONS']) 
                        && !empty($_SESSION['MUTUAL_INI']['intercambio']['CJP_SCOD_CONS']) 
                        ? $_SESSION['MUTUAL_INI']['intercambio']['CJP_SCOD_CONS'] : $this->CJP_SCOD_CONS);
                
                $this->CJP_SCOD_CSOC = (isset($_SESSION['MUTUAL_INI']['intercambio']['CJP_SCOD_CSOC']) 
                        && !empty($_SESSION['MUTUAL_INI']['intercambio']['CJP_SCOD_CSOC']) 
                        ? $_SESSION['MUTUAL_INI']['intercambio']['CJP_SCOD_CSOC'] : $this->CJP_SCOD_CSOC);                
            }
  
    	}

    function save($data = null, $validate = true, $fieldList = array()){        
        $save = parent::save($data,$validate,$fieldList);
        if(!empty(parent::getDataSource()->error)){
            $this->notificar(parent::getDataSource()->error);
        }
        return $save;
    }
    	
    	
	/**
	 * beforeSave
	 * guardo el dato del usuario que esta creando o modificando un registro
	 * genero la auditoria antes de almacenar el dato en la base
	 * (non-PHPdoc)
	 * @see cake/libs/model/Model#beforeSave($options)
	 */
	function beforeSave($options = array()){

		$this->notificaciones = array();

		$keyIdUserLogon = "ID_USER_LOGON_SIGEM";
		$keyNameUserLogon = "NAME_USER_LOGON_SIGEM";

		$id_user_logon = (isset($_SESSION[$this->keyIdUserLogon]) ? $_SESSION[$this->keyIdUserLogon] : '0');
		$name_user_logon = (isset($_SESSION[$this->keyNameUserLogon]) ? $_SESSION[$this->keyNameUserLogon] : 'APLICACION_SERVER');


		foreach($this->_schema as $field => $schema){
			if($schema['type'] == 'text'){
				App::import('Sanitize');
				if(isset($this->data[$this->name][$field]))$this->data[$this->name][$field] =  Sanitize::html($this->data[$this->name][$field],true);
			}
			if($schema['type'] == 'string'){
				App::import('Sanitize');
				if(isset($this->data[$this->name][$field]))$this->data[$this->name][$field] =  Sanitize::html($this->data[$this->name][$field],true);
//				if(isset($this->data[$this->name][$field]))$this->data[$this->name][$field] = addslashes(utf8_encode($this->data[$this->name][$field]));
				if(isset($this->data[$this->name][$field]))$this->data[$this->name][$field] = addslashes($this->data[$this->name][$field]);
			}

		}

// 		if(array_key_exists('usuario_id',$this->_schema)){
// 			if(!empty($id_user_logon))$this->data[$this->name]['usuario_id'] = $id_user_logon;
// 		}
		if(array_key_exists('user',$this->_schema)){
			if(!empty($id_user_logon))$this->data[$this->name]['user'] = $name_user_logon;
		}
		if(array_key_exists('user_created',$this->_schema) && empty($this->data[$this->name]['id'])){
			if(!empty($id_user_logon))$this->data[$this->name]['user_created'] = $name_user_logon;            
		}
        if(array_key_exists('created',$this->_schema) && empty($this->data[$this->name]['id'])){
            $this->data[$this->name]['created'] = date('Y-m-d H:i:s');
        }
        if(array_key_exists('modified',$this->_schema) && !empty($this->data[$this->name]['id']) ){
            $this->data[$this->name]['modified'] = date('Y-m-d H:i:s');
        }
        if(array_key_exists('last_ip',$this->_schema)){
            $this->data[$this->name]['last_ip'] = $_SERVER['REMOTE_ADDR'];
        }
        if(array_key_exists('user_modified',$this->_schema) && !empty($this->data[$this->name]['id']) ){
                if(!empty($id_user_logon))$this->data[$this->name]['user_modified'] = $name_user_logon;
        }
//        Configure::write('debug', 2);
		return parent::beforeSave();
	}


	function afterSave($created){
            if($this->auditable){
                App::import('Component','Auditoria');
                $oAuditoria = new AuditoriaComponent();
                $oAuditoria->log_model_serialize($this);
            }            
            return parent::afterSave($created);
	}

	/**
	 * SobreCarga metodo nativo saveField para evitar que los campos fecha me los ponga null
	 * @param $field
	 * @param $valor
	 * @param $validate
	 */
	function saveField($field,$valor,$validate = false){
		if(empty($this->id)) return false;
		$data = $this->read(null,$this->id);
                
		$data[$this->name][$field] = $valor;
		if ($validate) {
			$options = array_merge(array('validate' => false, 'fieldList' => array($field)), $validate);
		} else {
			$options = array('validate' => $validate, 'fieldList' => array($field));
		}
		return $this->save($data);
	}

	/**
	 * Metodo envoltorio para llamar al metodo nativo saveField
	 * @param $field
	 * @param $valor
	 * @param $validate
	 */
	function saveFieldCake($field,$valor,$validate = false){
		return parent::saveField($field,$valor,$validate);
	}


	/**
	 * Genera un trace de las consultas que procesa la base de datos
	 */
	function sqlTrace(){
////            $this->sqlTrace = FALSE;
////		if($this->sqlTrace):
////			//guardar los query como un trace de SQL
////			$filename = LOGS . 'SQL_'.date('Ymd') . '.log';
////			$dbo = $this->getDataSource();
//////            debug($dbo->_queriesLog);
////			$querys = $dbo->_queriesLog;
////			if(!empty($querys)):
////				$log = new File($filename, true);
////				$queryStr = null;
////				foreach($querys as $query):
//////					if(strstr($query['query'],"INSERT INTO") !== FALSE){
//////						$queryStr = date('Y-m-d H:i:s')."|".$query['query'].";\n";
//////					}
//////					if(strstr($query['query'],"UPDATE") !== FALSE){
//////						$queryStr = date('Y-m-d H:i:s')."|".$query['query'].";\n";
//////					}	
//////					if(strstr($query['query'],"DELETE") !== FALSE){
//////						$queryStr = date('Y-m-d H:i:s')."|".$query['query'].";\n";
//////					}
//////					if(strstr($query['query'],"START TRANSACTION") !== FALSE){
//////						$queryStr = date('Y-m-d H:i:s')."|".$query['query'].";\n";
//////					}
//////					if(strstr($query['query'],"COMMIT") !== FALSE){
//////						$queryStr = date('Y-m-d H:i:s')."|".$query['query'].";\n";
//////					}
//////					if(strstr($query['query'],"ROLLBACK") !== FALSE){
//////						$queryStr = date('Y-m-d H:i:s')."|".$query['query'].";\n";
//////					}
//////					if(strstr($query['query'],"SELECT LAST_INSERT_ID") !== FALSE){
//////						$queryStr = date('Y-m-d H:i:s')."|".$query['query'].";\n";
//////					}
////					$queryStr = date('Y-m-d H:i:s')."|".$query['query'].";\n";												
////					if ($log->writable()) $log->write($queryStr);				
////				endforeach;
////
////			endif;
////		endif;
	}





	/**
	 * sobrecarga del metodo DELETEALL para que por defecto pase en FALSE el segundo parametro
	 * ya que si viene TRUE intenta borrar todos los datos relacionados al modelo que se esta borrando
	 * @param $conditions
	 * @param $cascade
	 * @param $callbacks
	 * @return boolean
	 */
	function deleteAll($conditions,$cascade=false,$callbacks=false){
//		$this->sqlTrace();
		return parent::deleteAll($conditions,$cascade,$callbacks);
	}

	/**
	 * beforeDelete
	 * Genero la auditoria antes de borrar
	 * (non-PHPdoc)
	 * @see cake/libs/model/Model#beforeDelete($cascade)
	 */
	function beforeDelete($cascade = true){
		return true;
	}

	function afterDelete(){
//		$this->sqlTrace();
		if($this->auditable){
			App::import('Component','Auditoria');
			$oAuditoria = new AuditoriaComponent();
			$oAuditoria->log_model_serialize($this,'DELE');
		}		
		return true;
	}
	

	/**
	 * devuelve un objeto inicializado de dato global
	 * @param $fields
	 * @param $codigo
	 * @return array
	 */
	function getGlobalDato($fields,$codigo){
		App::import('Model', 'Config.GlobalDato');
		$this->GlobalDato = new GlobalDato(null);
		return $this->GlobalDato->read($fields,$codigo);
	}

	/**
	 * devuelve un dato global (valor)
	 * @param $field
	 * @param $codigo
	 * @return string
	 */
	function GlobalDato($field,$codigo){
            App::import('Model', 'Config.GlobalDato');
            $this->GlobalDato = new GlobalDato(null);
            $dato = $this->GlobalDato->read($field,$codigo);
            return $dato['GlobalDato'][$field];
	}

	/**
	 * devuelve una localidad
	 * @param $id
	 * @return unknown_type
	 * @return array
	 */
	function getLocalidad($id){
		App::import('Model', 'Config.Localidad');
		$this->Localidad = new Localidad(null);
		$this->Localidad->bindModel(array('belongsTo' => array('Provincia')));
		$this->Localidad->recursive = 2;
		return $this->Localidad->read(null,$id);
	}

	function getProvinciaByLetra($letra,$field=null){
		App::import('Model', 'Config.Provincia');
		$oPROVINCIA = new Provincia(null);
		$provincia = $oPROVINCIA->find('all',array('conditions' => array('Provincia.letra' => $letra)));
		if(!empty($provincia)) return (empty($field) ? $provincia[0] : $provincia[0]['Provincia'][$field]);
		else return null;
	}

	/**
	 * devuelve un banco
	 * @param $id
	 * @return array
	 */
	function getBanco($id){
		App::import('Model', 'Config.Banco');
		$this->Banco = new Banco(null);
		return $this->Banco->read(null,$id);
	}

	/**
	 *
	 * @param integer $id
	 * @return string
	 */
	function getNombreBanco($id){
		App::import('Model', 'Config.Banco');
		$this->Banco = new Banco(null);
		$banco = $this->Banco->read(null,$id);
		if(empty($banco)) return null;
		return 	$banco['Banco']['nombre'];
	}

	/**
	 *
	 * @param $cbu
	 * @return bollean
	 */
	function validarCBU($cbu){
		if(empty($cbu)) return false;
		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);
		return $oBanco->isCbuValido($cbu);
	}

	/**
	 * sumar meses a una fecha
	 * @param $timeStamp
	 * @param $totalMonths
	 * @return timestamp
	 */
	function addMonthToDate($timeStamp, $totalMonths=1){
		// You can add as many months as you want. mktime will accumulate to the next year.
		$thePHPDate = getdate($timeStamp); // Covert to Array
		 
		$isUltimoDiaMes = false;
		
		if($this->ultimoDiaMes($thePHPDate['mon'],$thePHPDate['year']) == $thePHPDate['mday']) $isUltimoDiaMes = true;
		 
		$timeStamp = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']); // Convert back to timestamp
		$thePHPDate['mon'] = $thePHPDate['mon'] + $totalMonths; // Add to Month
		
		$ultimoDiaNvoMes = $this->ultimoDiaMes($thePHPDate['mon'],$thePHPDate['year']);

		if($isUltimoDiaMes) $thePHPDate['mday'] = $ultimoDiaNvoMes;
		else if($ultimoDiaNvoMes < $thePHPDate['mday']) $thePHPDate['mday'] = $ultimoDiaNvoMes;
		
		$timeStamp = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']); // Convert back to timestamp
		return $timeStamp;
	}
	 
	/**
	 * sumar dias a una fecha
	 * @param $timeStamp
	 * @param $totalDays
	 * @return timestamp
	 */
	function addDayToDate($timeStamp, $totalDays=1){
		// You can add as many days as you want. mktime will accumulate to the next month / year.
		$thePHPDate = getdate($timeStamp);
		$thePHPDate['mday'] = $thePHPDate['mday'] + $totalDays;
		$timeStamp = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']);
		return $timeStamp;
	}

	/**
	 * sumar años a una fecha
	 * @param $timeStamp
	 * @param $totalYears
	 * @return timestamp
	 */
	function addYearToDate($timeStamp, $totalYears=1){
		$thePHPDate = getdate($timeStamp);
		$thePHPDate['year'] = $thePHPDate['year'] + $totalYears;
		$timeStamp = mktime($thePHPDate['hours'], $thePHPDate['minutes'], $thePHPDate['seconds'], $thePHPDate['mon'], $thePHPDate['mday'], $thePHPDate['year']);
		return $timeStamp;
	}


	//    function ultimoDiaMes($mes,$ano){return strftime("%d", mktime(0, 0, 0, $mes + 1, 0, $ano));}


	/**
	 * normaliza un periodo
	 * @param $periodo
	 * @param $ampliado
	 * @return string
	 */
	function periodo($periodo,$ampliado=false,$separador=" / "){
		App::import('Helper', 'Util');
		$oUT = new UtilHelper();
		return $oUT->periodo($periodo,$ampliado,$separador);

	}


        /**
         * 
         * @param type $valor
         * @param type $longitud
         * @param type $relleno
         * @param type $orientacion
         * @return type
         */
	function fill($valor,$longitud,$relleno="0",$orientacion='L'){
		if($orientacion=='L')return str_pad($valor,$longitud,$relleno,STR_PAD_LEFT);
		else if($orientacion=='R')return str_pad($valor,$longitud,$relleno,STR_PAD_RIGHT);
		else return $valor;
	}



	/**
	 * genera el string que representa un numero
	 * @param $Numero
	 * @return string
	 */
	function num2letras($Numero,$decimales=true){
//		$str = "";
//		$tt = $Numero;
//		$tt = $tt+0.009;
//		$Numero = intval($tt);
//		$Decimales = $tt - Intval($tt);
//		$Decimales= $Decimales*100;
//		$Decimales= Intval($Decimales);
//		$str = $this->__NumerosALetras($Numero);
//		if ($Decimales >= 0 && $decimales){
//			//$y=self::NumerosALetras($Decimales);
//			$str .= "con $Decimales/100.-";
//		}
        $str = $this->num2letras2($Numero);
		$str = strtoupper($str);
		return $str;
	}

	/**
	 *
	 * @param $VCentena
	 * @return string
	 */
	function Centenas($VCentena) {
		$Numeros[0] = "cero";
		$Numeros[1] = "uno";
		$Numeros[2] = "dos";
		$Numeros[3] = "tres";
		$Numeros[4] = "cuatro";
		$Numeros[5] = "cinco";
		$Numeros[6] = "seis";
		$Numeros[7] = "siete";
		$Numeros[8] = "ocho";
		$Numeros[9] = "nueve";
		$Numeros[10] = "diez";
		$Numeros[11] = "once";
		$Numeros[12] = "doce";
		$Numeros[13] = "trece";
		$Numeros[14] = "catorce";
		$Numeros[15] = "quince";
		$Numeros[20] = "veinte";
		$Numeros[30] = "treinta";
		$Numeros[40] = "cuarenta";
		$Numeros[50] = "cincuenta";
		$Numeros[60] = "sesenta";
		$Numeros[70] = "setenta";
		$Numeros[80] = "ochenta";
		$Numeros[90] = "noventa";
		$Numeros[100] = "ciento";
		$Numeros[101] = "quinientos";
		$Numeros[102] = "setecientos";
		$Numeros[103] = "novecientos";
		If ($VCentena == 1) {
			return $Numeros[100];
		}
		Else If ($VCentena == 5) {
			return $Numeros[101];
		}
		Else If ($VCentena == 7 ) {
			return ( $Numeros[102]);
		}
		Else If ($VCentena == 9) {
			return ($Numeros[103]);
		}
		Else {return $Numeros[$VCentena];
		}
	}

	/**
	 *
	 * @param $VUnidad
	 * @return string
	 */
	function Unidades($VUnidad) {
		$Numeros[0] = "cero";
		$Numeros[1] = "un";
		$Numeros[2] = "dos";
		$Numeros[3] = "tres";
		$Numeros[4] = "cuatro";
		$Numeros[5] = "cinco";
		$Numeros[6] = "seis";
		$Numeros[7] = "siete";
		$Numeros[8] = "ocho";
		$Numeros[9] = "nueve";
		$Numeros[10] = "diez";
		$Numeros[11] = "once";
		$Numeros[12] = "doce";
		$Numeros[13] = "trece";
		$Numeros[14] = "catorce";
		$Numeros[15] = "quince";
		$Numeros[20] = "veinte";
		$Numeros[30] = "treinta";
		$Numeros[40] = "cuarenta";
		$Numeros[50] = "cincuenta";
		$Numeros[60] = "sesenta";
		$Numeros[70] = "setenta";
		$Numeros[80] = "ochenta";
		$Numeros[90] = "noventa";
		$Numeros[100] = "ciento";
		$Numeros[101] = "quinientos";
		$Numeros[102] = "setecientos";
		$Numeros[103] = "novecientos";

		$tempo=$Numeros[$VUnidad];
		return $tempo;
	}

	/**
	 *
	 * @param $VDecena
	 * @return string
	 */
	function Decenas($VDecena) {
		$Numeros[0] = "cero";
		$Numeros[1] = "uno";
		$Numeros[2] = "dos";
		$Numeros[3] = "tres";
		$Numeros[4] = "cuatro";
		$Numeros[5] = "cinco";
		$Numeros[6] = "seis";
		$Numeros[7] = "siete";
		$Numeros[8] = "ocho";
		$Numeros[9] = "nueve";
		$Numeros[10] = "diez";
		$Numeros[11] = "once";
		$Numeros[12] = "doce";
		$Numeros[13] = "trece";
		$Numeros[14] = "catorce";
		$Numeros[15] = "quince";
		$Numeros[20] = "veinte";
		$Numeros[30] = "treinta";
		$Numeros[40] = "cuarenta";
		$Numeros[50] = "cincuenta";
		$Numeros[60] = "sesenta";
		$Numeros[70] = "setenta";
		$Numeros[80] = "ochenta";
		$Numeros[90] = "noventa";
		$Numeros[100] = "ciento";
		$Numeros[101] = "quinientos";
		$Numeros[102] = "setecientos";
		$Numeros[103] = "novecientos";
		$tempo = ($Numeros[$VDecena]);
		return $tempo;
	}

	/**
	 *
	 * @param $Numero
	 * @return string
	 */
	function __NumerosALetras($Numero){

		$Decimales = 0;
		//$Numero = intval($Numero);
		$letras = "";

		while ($Numero != 0){

			// '*---> Validaci�n si se pasa de 100 millones

			if ($Numero >= 1000000000) {
				$letras = "Error en Conversion a Letras";
				$Numero = 0;
				$Decimales = 0;
			}

			// '*---> Centenas de Mill�n
			if (($Numero < 1000000000) && ($Numero >= 100000000)){
				if ((Intval($Numero / 100000000) == 1) && (($Numero - (Intval($Numero / 100000000) * 100000000)) < 1000000)){
					$letras .= (string) "cien millones ";
				}else {
					$letras = $letras & $this->Centenas(Intval($Numero / 100000000));
					if ((Intval($Numero / 100000000) <> 1) && (Intval($Numero / 100000000) <> 5) And (Intval($Numero / 100000000) <> 7) And (Intval($Numero / 100000000) <> 9)) {
						$letras .= (string) "cientos ";
					}else {
						$letras .= (string) " ";
					}
				}
				$Numero = $Numero - (Intval($Numero / 100000000) * 100000000);
			}

			// '*---> Decenas de Millon
			if (($Numero < 100000000) && ($Numero >= 10000000)) {
				if (Intval($Numero / 1000000) < 16) {
					$tempo = $this->Decenas(Intval($Numero / 1000000));
					$letras .= (string) $tempo;
					$letras .= (string) " millones ";
					$Numero = $Numero - (Intval($Numero / 1000000) * 1000000);
				}else {
					$letras = $letras & $this->Decenas(Intval($Numero / 10000000) * 10);
					$Numero = $Numero - (Intval($Numero / 10000000) * 10000000);
					if ($Numero > 1000000) {
						$letras .= $letras & " y ";
					}
				}
			}

			// '*---> Unidades de Mill�n
			if (($Numero < 10000000) And ($Numero >= 1000000)) {
				$tempo=(Intval($Numero / 1000000));
				if ($tempo == 1) {
					$letras .= (string) " un millon ";
				}else {
					$tempo= $this->Unidades(Intval($Numero / 1000000));
					$letras .= (string) $tempo;
					$letras .= (string) " millones ";
				}
				$Numero = $Numero - (Intval($Numero / 1000000) * 1000000);
			}

			// '*---> Centenas de Millar
			if (($Numero < 1000000) && ($Numero >= 100000)) {
				$tempo=(Intval($Numero / 100000));
				$tempo2=($Numero - ($tempo * 100000));
				if (($tempo == 1) && ($tempo2 < 1000)) {
					$letras .= (string) "cien mil ";
				}else {
					$tempo=$this->Centenas(Intval($Numero / 100000));
					$letras .= (string) $tempo;
					$tempo=(Intval($Numero / 100000));
					if (($tempo <> 1) && ($tempo <> 5) && ($tempo <> 7) && ($tempo <> 9)) {
						$letras .= (string) "cientos ";
					}else {
						$letras .= (string) " ";
					}
				}
				$Numero = $Numero - (Intval($Numero / 100000) * 100000);
			}

			// '*---> Decenas de Millar
			if (($Numero < 100000) && ($Numero >= 10000)) {
				$tempo= (Intval($Numero / 1000));
				if ($tempo < 16) {
					$tempo = $this->Decenas(Intval($Numero / 1000));
					$letras .= (string) $tempo;
					$letras .= (string) " mil ";
					$Numero = $Numero - (Intval($Numero / 1000) * 1000);
				}else {
					$tempo = $this->Decenas(Intval($Numero / 10000) * 10);
					$letras .= (string) $tempo;
					$Numero = $Numero - (Intval(($Numero / 10000)) * 10000);
					if ($Numero > 1000) {
						$letras .= (string) " y ";
					}else {
						$letras .= (string) " mil ";
					}
				}
			}

			// '*---> Unidades de Millar
			if (($Numero < 10000) And ($Numero >= 1000)) {
				$tempo=(Intval($Numero / 1000));
				if ($tempo == 1) {
					$letras .= (string) "un";
				}else {
					$tempo = $this->Unidades(Intval($Numero / 1000));
					$letras .= (string) $tempo;
				}
				$letras .= (string) " mil ";
				$Numero = $Numero - (Intval($Numero / 1000) * 1000);
			}

			// '*---> Centenas
			if (($Numero < 1000) && ($Numero > 99)) {
				if ((Intval($Numero / 100) == 1) && (($Numero - (Intval($Numero / 100) * 100)) < 1)) {
					$letras = $letras & "cien ";
				}else {
					$temp=(Intval($Numero / 100));
					$l2=$this->Centenas($temp);
					$letras .= (string) $l2;
					if ((Intval($Numero / 100) <> 1) && (Intval($Numero / 100) <> 5) && (Intval($Numero / 100) <> 7) && (Intval($Numero / 100) <> 9)) {
						$letras .= "cientos ";
					}else {
						$letras .= (string) " ";
					}
				}
				$Numero = $Numero - (Intval($Numero / 100) * 100);
			}

			// '*---> Decenas
			if (($Numero < 100) And ($Numero > 9) ) {
				if ($Numero < 16 ) {
					$tempo = $this->Decenas(Intval($Numero));
					$letras .= $tempo;
					$Numero = $Numero - Intval($Numero);
				}else {
					$tempo= $this->Decenas(Intval(($Numero / 10)) * 10);
					$letras .= (string) $tempo;
					$Numero = $Numero - (Intval(($Numero / 10)) * 10);
					if ($Numero > 0.99) {
						$letras .=(string) " y ";
					}
				}
			}

			// '*---> Unidades
			if (($Numero < 10) And ($Numero > 0.99)) {
				$tempo=$this->Unidades(Intval($Numero));
				$letras .= (string) $tempo;
				$Numero = $Numero - Intval($Numero);
			}

			// '*---> Decimales
			if ($Decimales > 0) {
				// $letras .=(string) " con ";
				// $Decimales= $Decimales*100;
				// echo ("*");
				// $Decimales = number_format($Decimales, 2);
				// echo ($Decimales);
				// $tempo = Decenas(Intval($Decimales));
				// $letras .= (string) $tempo;
				// $letras .= (string) "centavos";
			}else {
				if (($letras <> "Error en Conversion a Letras") && (strlen(Trim($letras)) > 0)) {
					$letras .= (string) " ";
				}
			}
			return $letras;
		}
	}


	/**
	 * calcula las horas habiles entre fechas, tomando como jornada laboral de lunes
	 * a viernes de 9 a 18hs
	 * @param $fecha_d
	 * @param $fecha_h
	 * @return integer
	 */
	function horasHabiles($fecha_d,$fecha_h){

		$segXdia = 86400;
		$segXhora = 3600;

		if(empty($fecha_d)) $fecha_d = date('Y-m-d H:m:s');
		if(empty($fecha_h)) $fecha_h = date('Y-m-d H:m:s');

		$d_d = date('d',strtotime($fecha_d));
		$m_d = date('m',strtotime($fecha_d));
		$y_d = date('y',strtotime($fecha_d));
		$hor_d = date('H',strtotime($fecha_d));
		$min_d = date('m',strtotime($fecha_d));
		$sec_d = date('s',strtotime($fecha_d));

		$d_h = date('d',strtotime($fecha_h));
		$m_h = date('m',strtotime($fecha_h));
		$y_h = date('y',strtotime($fecha_h));
		$hor_h = date('H',strtotime($fecha_h));
		$min_h = date('m',strtotime($fecha_h));
		$sec_h = date('s',strtotime($fecha_h));


		$mkDesde = mktime($hor_d,$min_d,$sec_d,$m_d,$d_d,$y_d);
		$mkHasta = mktime($hor_h,$min_h,$sec_h,$m_h,$d_h,$y_h);

		$mkDesde_d = mktime(0,0,0,$m_d,$d_d,$y_d);
		$mkHasta_d = mktime(0,0,0,$m_h,$d_h,$y_h);

		$diff_h = (($mkHasta - $mkDesde) / $segXhora);

		$diff_d = $diff_h / 24;


		/**
		 * es el mismo dia, devuelvo la cantidad de horas y no analizo nada mas
		 */
		if($mkDesde_d == $mkHasta_d) return abs($diff_h);

		//		print $fecha_d ." --> " .date('D',$mkDesde) ."<br>";
		//		print $fecha_h ." --> " .date('D',$mkHasta) ."<br>";
		//
		//		print " --> DIAS: $diff_d --> HORAS: $diff_h <br>";

		$dia = $mkDesde_d;
		$first = true;

		$acu = 0;
		$ultimoDiaProcesado = null;

		while($dia <= $mkHasta_d){
				

			switch (date('N',$dia)){

				case 6:
					$first = false;
					break;
						
				case 7:
					$first = false;
					break;
						
				default:
						
					if($first){

					$acu = mktime(18,0,0,$m_d,$d_d,$y_d) - $mkDesde;
					$first = false;

					if($acu < 0) $acu = 0;

				}else{

					$acu += $segXhora * 9;

				}
				break;
					
					
			}

				
				
			//			print "\t\t		dia --> ". date('Y-m-d',$dia) . " --> " . date('D',$dia) ." **** " . $acu / $segXhora ."\n";
				
			$ultimoDiaProcesado = $dia;
			$dia += $segXdia;
				
				
		}


		//		print "mkHasta " . $mkHasta ."\n";
		//		print "HoraSalida " .mktime(18,0,0,$m_h,$d_h,$y_h)."\n";
		//		print "UltimoDiaProcesado " .$ultimoDiaProcesado."\n";


		//		print date('Ymd',$mkHasta)	." ---- " . date('Ymd',$ultimoDiaProcesado)."\n";


		if($mkHasta < mktime(18,0,0,$m_h,$d_h,$y_h)){

			if(date('Ymd',$ultimoDiaProcesado) >= date('Ymd',$mkHasta)) $acu = $acu - ($segXhora * 9);
			$acu2 = ($mkHasta - mktime(9,0,0,$m_h,$d_h,$y_h)) + $acu ;

			//			print $acu."\n";
			//			print ($mkHasta - mktime(9,0,0,$m_h,$d_h,$y_h))."\n";


		}else{

			$acu2 = $acu;

		}


		//		print " \t--> HORAS ===> ". $acu2 / $segXhora." \n";

		//		print "</div>";

		return $acu2 / $segXhora;

			
	}



	function armaFecha($data){
		return $data['year'].'-'.$data['month'] .(isset($data['day']) ? '-'.$data['day'] : '-01');
	}

	/**
	 * Verifica si el string pasado por parametro es una fecha valida
	 * @param $str
	 */
	function is_date($str){
		$stamp = strtotime( $str );
		if (!is_numeric($stamp)) return false;
		$month = date( 'm', $stamp );
		$day   = date( 'd', $stamp );
		$year  = date( 'Y', $stamp );
		if (checkdate($month, $day, $year)) return true;
		else return false;
	}

	/**
	 * Convierte un string en fecha
	 * @param $string
	 */
	function strToDate($string){
		if(!$this->is_date($string)) return null;
		$stamp = strtotime( $string );
		return date('Y-m-d',$stamp);
	}

	/**
	 * START TRANSACTION
	 */
	function begin(){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db->begin($this);
	}

	/**
	 * ROLLBACK TRANSACTION
	 */
	function rollback(){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db->rollback($this);
	}

	/**
	 * COMMIT TRANSACTION
	 */
	function commit(){
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		return $db->commit($this);
	}


	/**
	 * Calcula la diferencia entre fechas y devuelve el valor segun el interval
	 * @param $interval
	 * @param $datefrom
	 * @param $dateto
	 * @param $using_timestamps
	 */
	function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
		/*
		 $interval can be:
		yyyy - Number of full years
		q - Number of full quarters
		m - Number of full months
		y - Difference between day numbers
		(eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
		d - Number of full days
		w - Number of full weekdays
		ww - Number of full weeks
		h - Number of full hours
		n - Number of full minutes
		s - Number of full seconds (default)
		*/
		#
		if (!$using_timestamps) {
		$datefrom = strtotime($datefrom, 0);
		$dateto = strtotime($dateto, 0);
	}
	$difference = $dateto - $datefrom; // Difference in seconds
	$months_difference = 0;
	#
	switch($interval) {
		#
		case 'yyyy': // Number of full years
			
		$years_difference = floor($difference / 31536000);
	if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
		$years_difference--;
	}
	if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
		$years_difference++;
	}
	$datediff = $years_difference;
	$months_difference = 0;
	break;
		
		case "q": // Number of full quarters
				
			$quarters_difference = floor($difference / 8035200);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$quarters_difference--;
			$datediff = $quarters_difference;
			break;
				
		case "m": // Number of full months
				
			$months_difference = floor($difference / 2678400);
			while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			}
			$months_difference--;
			$datediff = $months_difference;
			break;
				
		case 'y': // Difference between day numbers
				
			$datediff = date("z", $dateto) - date("z", $datefrom);
			break;
				
		case "d": // Number of full days
				
			$datediff = floor($difference / 86400);
			break;
				
		case "w": // Number of full weekdays
				
			$days_difference = floor($difference / 86400);
			$weeks_difference = floor($days_difference / 7); // Complete weeks
			$first_day = date("w", $datefrom);
			$days_remainder = floor($days_difference % 7);
			$odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			if ($odd_days > 7) { // Sunday
				$days_remainder--;
			}
			if ($odd_days > 6) { // Saturday
				$days_remainder--;
			}
			$datediff = ($weeks_difference * 5) + $days_remainder;
			break;
				
		case "ww": // Number of full weeks
				
			$datediff = floor($difference / 604800);
			break;
				
		case "h": // Number of full hours
				
			$datediff = floor($difference / 3600);
			break;
				
		case "n": // Number of full minutes
				
			$datediff = floor($difference / 60);
			break;
				
		default: // Number of full seconds (default)
				
			$datediff = $difference;
		break;
	}
		
	return $datediff;

	}



	/**
	 * genera un digito verificador clave 10 con el ponderador 9713 por defecto
	 * @param $numero
	 * @param $ponderador
	 */
	function digitoVerificador($numero,$ponderador="9713"){

		$pond = str_split($ponderador);

		$aBlq = str_split($numero);
		$lnBl = strlen($numero);

		$suma = 0;

		for($i=1;$i <= $lnBl;$i++){

			$v1 = $aBlq[$lnBl - $i];
				
			$resto = ((4 - $i) % 4);
				
			if( $resto < 0 ) $resto += 4;
				
			$v2 = $pond[$resto];
				
			$suma += $v1 * $v2;
		}

		$digito = 10 - ($suma % 10);



		$aDigito = str_split($digito);
		$digito = $aDigito[strlen($digito) - 1];
		return $digito;
	}

	function onError(){
		//		$dbo = $this->getDataSource();
		//		$querys = $dbo->_queriesLog;
		//		if(!empty($querys)):
		//			$error = $querys[1]['query']."\n".$querys[1]['error'];
		//			trigger_error($error);
		//		endif;
	}

	/**
	 * Arma un periodo de corte para un dia pasado por parametro
	 * @param $diaCorte
	 */
	function periodoCorte($diaCorte = 15){
		$periodo = null;
		$diaHoy = intval(date('d'));
		if($diaCorte >= $diaHoy) return date('Ym');
		//es mayor
		$mk = $this->addMonthToDate(mktime(0,0,0,date('m'),date('d'),date('Y')),1);
		return date('Ym',$mk);
	}


	/**
	 * Devuelve el ultimo d�a de un mes
	 * @param $mes
	 * @param $anio
	 */
	function ultimoDiaMes($mes,$anio=null){

		//		return strftime("%d", mktime(0, 0, 0, $mes + 1, 0, $ano));

		if(empty($anio)) $anio = date('Y');

		$ultimoDia = 31;

		if (((fmod($anio,4)==0) and (fmod($anio,100)!=0)) or (fmod($anio,400)==0)) {
			$dias_febrero = 29;
		} else {
			$dias_febrero = 28;
		}

		switch($mes) {
			case '02':
				$ultimoDia = $dias_febrero;
				break;
			case '01':
			case '03':
			case '05':
			case '07':
			case '08':
			case '10':
			case '12':
				$ultimoDia = 31;
				break;
			case '04':
			case '06':
			case '09':
			case '11':
				$ultimoDia = 30;
				break;
		}
		return $ultimoDia;

	}


	/**
	 * Convierte un array de datos en un string donde los elementos del array son concatenados usando el $separador
	 * @param $array
	 * @param $separador
	 * @param $toUpper
	 * @return string codificado con UTF-8
	 */
	function arrayToString($array,$separador="|",$toUpper=true){
		$str = "";
		if(empty($array)) return null;
		$tmp = array();
		foreach($array as $item){
			foreach($item as $key => $value){
				array_push($tmp,$key."=".( $toUpper ? strtoupper($value) : $value));
			}
			$str .= implode($separador,$tmp);
			$str .= "\n\r";
		}
		return utf8_encode($str);
	}

	/**
	 * Genera un XML en base a un array de datos pasados por parametro
	 * @param $datos
	 * @return XML
	 */
	function getXML($datos){
		App::Import('Helper', 'Xml');
		$objXmlHelper = new XmlHelper();
		$objXml = $objXmlHelper->serilize($datos);
		return $objXml;
	}

	/**
	 * Serializa y codifica
	 * @param $datos
	 */
	function serializar($datos){
		return base64_encode(serialize($datos));
	}

	/**
	 * Decodifica y des-serializa un string codificado
	 * @param $encode
	 */
	function unSerializar($encode){
		return unserialize(base64_decode($encode));
	}

	/**
	 * Genera un array en base a un string con valores separados y con final de linea
	 * @param $str
	 * @param $separador
	 * @param $nuevaLinea
	 */
	function stringToArray($str,$separador="|",$nuevaLinea="\n"){
		$datos = array();
		$lineas = explode($nuevaLinea,$str);
		if(empty($lineas)) return $datos;
		foreach($lineas as $linea){
			if(!empty($linea)):
			$tmp = array();
			$tmp = explode($separador,$linea);
			if(!empty($tmp)) array_push($datos,$tmp);
			endif;
		}
		return $datos;
	}


	function notificar($msg){
		array_push($this->notificaciones,$msg);
	}


	function importarModelo($modelo, $plugin=null){
		$class = (!empty($plugin) ? $plugin . '.' . $modelo : $modelo);
		App::import('Model',$class);
		return new $modelo();
		//   		$objeto = &ClassRegistry::init(array('class' => $class,'alias' => $modelo));
		//   		return $objeto;
		//   		debug($objeto);
		//   		exit;
	}


	/**
	 * Sobrecarga del metodo SAVEALL para evitar los commit automaticos
	 * en las transacciones
	 * @param $data
	 * @param $options
	 */
	function saveAll($data = null, $options = array()){
		if(!isset($options['atomic'])) $options['atomic'] = false;
		return parent::saveAll($data,$options);
	}

	/**
	 * Sobrecarga del metodo QUERY para evitar el cache de consultas
	 * 
	 * @author adrian [13/03/2012]
	 * @param string $query
	 * @param boolean $cachequeries
	 */
	function query($query, $cachequeries = false){
		$result = parent::query($query,$cachequeries);
//		$this->sqlTrace();
		return $result;
	}
	
	
	function getDiaHabil($periodo,$opcion='PRIMER',$antPost='POSTERIOR'){
		$secPerDay = 24*60*60;
		 
		$mes = substr($periodo,4,2);
		$anio = substr($periodo,0,4);
		 
		$fechaTime = mktime(0,0,0,$mes,1,$anio);
		 
		$dia = ($opcion == 'PRIMER' ? '01' : $this->ultimoDiaMes($mes,$anio));
		 
		switch (date('N',strtotime("$dia-$mes-$anio"))){
			case 6:
				//SABADO
				$nFechaTime = $fechaTime + ($secPerDay * ($antPost == 'POSTERIOR' ? 2 : -1));
				break;
			case 7:
				//DOMINGO
				$nFechaTime = $fechaTime + ($secPerDay * ($antPost == 'POSTERIOR' ? 1 : -2));
				break;
			default:
				$nFechaTime = $fechaTime;
			break;
		}
		 
		return date('Y-m-d', $nFechaTime);
		 
	}


	function generarPIN($len = 10){
		$str = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$PIN = "";
		for($i=0; $i < $len; $i++){
			$PIN .= $str[mt_rand(0,strlen($str)-1)];
		}
		return $PIN;
	}

	
	/**
	 * Calcula los meses entre periodos
	 * 
	 * @author adrian [01/02/2012]
	 * @param string $periodo1
	 * @param string $periodo2
	 * @param string $resultTo
	 * @return int
	 */
	function periodoDiff($periodo1,$periodo2,$resultTo='mon'){
		$iniDay = 1;
		$mkIni1 = mktime(0,0,0,substr($periodo1,4,2),$iniDay,substr($periodo1,0,4));
		$mkIni2 = mktime(0,0,0,substr($periodo2,4,2),$iniDay,substr($periodo2,0,4));
		$diff = $mkIni2 - $mkIni1;
		$thePHPDate = getdate($diff); 
		return $thePHPDate[$resultTo];
		
	}
    
    
    function num2letras2($number){

        $tt = $number;
        $tt = $tt + 0.009;
        $decimal = $tt - Intval($tt);
        /*
         * esto no va aca por que si decimal es 0 nunca agrega los decimales 0/100.-
         */
        $decimal = intval(($tt - Intval($tt)) * 100);

        $number = intval($number);

        $converted = '';
        $numberStr = (string) $number;
        $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);

        if (intval($millones) > 0) {
          if ($millones == '001') $converted .= 'UN MILLON ';
          else if (intval($millones) > 0) $converted .= sprintf('%sMILLONES ', $this->num2letras2_convertGroup($millones));
        }

        if (intval($miles) > 0) {
          if ($miles == '001') $converted .= 'MIL ';
          else if (intval($miles) > 0)$converted .= sprintf('%sMIL ', $this->num2letras2_convertGroup($miles));
        }

        if (intval($cientos) > 0) {
          if ($cientos == '001')$converted .= 'UN ';
          else if (intval($cientos) > 0)$converted .= sprintf('%s ', $this->num2letras2_convertGroup($cientos));
        }

        if($decimal > 0):
//            $decimal = intval(($tt - Intval($tt)) * 100);
            $converted .= "CON $decimal/100.-";
        endif;

        return $converted;	


    }

    function num2letras2_convertGroup($n){
        $output = '';
        if ($n == '100') {
          $output = "CIEN ";
        } else if ($n[0] !== '0') {
          $output = $this->num2letras2_unidades('C',$n[0] - 1);   
        }
        $k = intval(substr($n,1));
        if ($k <= 20) {
          $output .= $this->num2letras2_unidades('U',$k);
        } else {
          if(($k > 30) && ($n[2] !== '0')) {
             $output .= sprintf('%sY %s', $this->num2letras2_unidades('D',intval($n[1]) - 2), $this->num2letras2_unidades('U',intval($n[2])));
          } else {
             $output .= sprintf('%s%s', $this->num2letras2_unidades('D',intval($n[1]) - 2), $this->num2letras2_unidades('U',intval($n[2])));
          }
        }
        return $output;
    }

    function num2letras2_unidades($type,$index){
        $unidades = array();
        $unidades['U'] = array(
               '',
               'UN ',
               'DOS ',
               'TRES ',
               'CUATRO ',
               'CINCO ',
               'SEIS ',
               'SIETE ',
               'OCHO ',
               'NUEVE ',
               'DIEZ ',
               'ONCE ',
               'DOCE ',
               'TRECE ',
               'CATORCE ',
               'QUINCE ',
               'DIECISEIS ',
               'DIECISIETE ',
               'DIECIOCHO ',
               'DIECINUEVE ',
               'VEINTE '
        );	
        $unidades['D'] = array(
            'VENTI',
            'TREINTA ',
            'CUARENTA ',
            'CINCUENTA ',
            'SESENTA ',
            'SETENTA ',
            'OCHENTA ',
            'NOVENTA ',
            'CIEN '
        );

        $unidades['C'] = array(
            'CIENTO ',
            'DOSCIENTOS ',
            'TRESCIENTOS ',
            'CUATROCIENTOS ',
            'QUINIENTOS ',
            'SEISCIENTOS ',
            'SETECIENTOS ',
            'OCHOCIENTOS ',
            'NOVECIENTOS '
        );
        return $unidades[$type][$index];
    }    
    

    function generarPeriodos($periodoInicial, $cantidad) {
    // Verifica que el periodo inicial sea una cadena de 6 caracteres
    if (strlen($periodoInicial) != 6) {
        throw new Exception("El periodo inicial debe tener 6 caracteres en el formato YYYYMM.");
    }
    
    // Verifica que la cantidad sea un entero mayor que 0
    if (!is_int($cantidad) || $cantidad <= 0) {
        throw new Exception("La cantidad debe ser un entero mayor que 0.");
    }
    
    // Extrae el año y el mes del periodo inicial
    $anio = intval(substr($periodoInicial, 0, 4));
    $mes = intval(substr($periodoInicial, 4, 2));

    // Verifica que el mes sea válido
    if ($mes < 1 || $mes > 12) {
        throw new Exception("El mes en el periodo inicial debe estar entre 01 y 12.");
    }

    $periodos = [];
    for ($i = 0; $i < $cantidad; $i++) {
        // Añade el periodo actual al arreglo
        $periodos[] = sprintf('%04d%02d', $anio, $mes);
        
        // Incrementa el mes
        $mes++;
        if ($mes > 12) {
            $mes = 1;
            $anio++;
        }
    }
    
    return $periodos;
}
    
}
?>
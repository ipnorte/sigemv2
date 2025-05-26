<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-task
 */
class IntercambioTask extends Shell{
	
	var $liquidacionID;
	var $liquidacionIntercambioID;
	var $disenioRegistro;
	

	/**
	 * setea el diseño del registro de intercambio en la variable del objeto
	 * @param $liquidacionIntercambioID
	 */
	function setDisenioRegistro($liquidacionIntercambioID){
		$this->liquidacionIntercambioID = $liquidacionIntercambioID;
		$banco_id = $this->__getCampo('banco_id');
		$codigo_organismo = $this->__getCampo('codigo_organismo');
		App::import('Model','Mutual.LiquidacionDisenioRegistro');
		$oDis = new LiquidacionDisenioRegistro();	
		$registros = $oDis->find('all',array('conditions' => array('LiquidacionDisenioRegistro.codigo_organismo' => $codigo_organismo,'LiquidacionDisenioRegistro.banco_id' => $banco_id,'LiquidacionDisenioRegistro.entrada_salida' => 'E'),'order' => array('LiquidacionDisenioRegistro.columna')));
		$this->disenioRegistro = $registros;	
	}	
	
	/**
	 * fragmenta el string pasado como parametro
	 * @param $registro
	 */
	function fragmentarRegistro($registro){
		$data = array();
		$criterios = $this->disenioRegistro;
		$offSet = 0;
		$data['LiquidacionIntercambioRegistro']['liquidacion_intercambio_id'] = $this->liquidacionIntercambioID;
		$data['LiquidacionIntercambioRegistro']['registro'] = trim($registro);
		$data['LiquidacionIntercambioRegistro']['liquidacion_id'] = $this->__getCampo('liquidacion_id');
		$data['LiquidacionIntercambioRegistro']['banco_intercambio'] = $this->__getCampo('banco_id');

		foreach($criterios as $idx => $criterio){
			$dato = substr($registro,$offSet,$criterio['LiquidacionDisenioRegistro']['longitud']);
			switch ($criterio['LiquidacionDisenioRegistro']['tipo_dato']){
				case 'I':
					//numero
					$dato = intval($dato);
					break;				
				case 'D':
					//numero
					$dato = intval($dato);
					if($criterio['LiquidacionDisenioRegistro']['decimales'] != 0){
						$dato = $dato / (pow(10,$criterio['LiquidacionDisenioRegistro']['decimales']));
					}
					break;
				case 'F':
					//fecha
					$dato = substr($dato,0,4).'-'.substr($dato,4,2).'-'.substr($dato,6,2);
					break;	
				default:
					// ES UN STRING
					break;
			}
			$data['LiquidacionIntercambioRegistro'][$criterio['LiquidacionDisenioRegistro']['columna_destino']] = $dato;
			$offSet += $criterio['LiquidacionDisenioRegistro']['longitud'];
		}
		App::import('Model','Mutual.LiquidacionIntercambioRegistro');
		$oFile = new LiquidacionIntercambioRegistro();	
		$oFile->id = 0;
		return $oFile->save($data);
	}

	
	/**
	 * devuelve para la liquidacion_intercambio el valor de un campo pasado por parametro
	 * @param $field
	 */
	function __getCampo($field){
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();
		$archivo = $oFile->read($field,$this->liquidacionIntercambioID);
		return $archivo['LiquidacionIntercambio'][$field];
	}
	
	/**
	 * devuelve el organismo que se esta liquidando
	 * @return codigo_organismo
	 */
	function getOrganismoLiquidacion($liquidacionID){
		App::import('Model','Mutual.Liquidacion');
		$oLIQ = new Liquidacion();
		$liquidacion = $oLIQ->read('codigo_organismo',$liquidacionID);
		return $liquidacion['Liquidacion']['codigo_organismo'];
	}

	/**
	 * devuelve los archivos para una liquidacion determinada que no estan marcados como
	 * procesados 
	 * @param unknown_type $liquidacionID
	 */
	function getArchivos($liquidacionID){
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();
		$archivos = $oFile->find('all',array('conditions' => array(
													'LiquidacionIntercambio.liquidacion_id' => $liquidacionID,
													'LiquidacionIntercambio.procesado' => 0
											)
		));
		return $archivos;
	}
	
	/**
	 * devuelve el contenido del archivo plano como un array
	 * @return $registros (array)
	 */
	function getContenidoArchivo(){
		$registros = array();
		$path = $this->__getCampo('target_path');
		if(!file_exists($path)) return false;
		$handle = fopen($path, "rb");
		$contents = '';
		while (!feof($handle)) {
		  $contents .= fread($handle, 8192);
		}
		fclose($handle);
		$registros = explode("\n",$contents);
		if(!is_array($registros)) return null;
		#limpio tabla intercambio
		$this->limpiarTablaIntercambio();
		#determinar que tipo de registro es para saber si tengo que eliminar el primero y el ultimo
		App::import('Model','Config.Banco');
		$oBanco = new Banco();		
		$tipo = $oBanco->read('tipo_registro',$this->__getCampo('banco_id'));
		$tipo = $tipo['Banco']['tipo_registro'];
		if($tipo == 3){
			$primero = array_shift($registros);
			$ultimo = array_pop($registros);
			if(empty($ultimo))$ultimo = array_pop($registros);
		}else{
			if(empty($ultimo))$ultimo = array_pop($registros);
		}
		return $registros;		
	}

	/**
	 * RESETEA TABLA LIQUIDACION SOCIOS
	 * @param unknown_type $liquidacionID
	 */
	function resetTablaLiquidacionSocio($liquidacionID){
		//LIMPIO LOS CAMPOS DE LA TABLA LIQUIDACION SOCIOS
		App::import('Model','Mutual.LiquidacionSocio');
		$oLSOC = new LiquidacionSocio();
		$oLSOC->updateAll(
							array(
									'LiquidacionSocio.banco_intercambio' => null,
									'LiquidacionSocio.importe_adebitar' => 0,
									'LiquidacionSocio.importe_debitado' => 0, 
									'LiquidacionSocio.importe_imputado' => 0,
									'LiquidacionSocio.importe_reintegro' => 0,  
									'LiquidacionSocio.status' => null,
									'LiquidacionSocio.fecha_pago' => null,
									'LiquidacionSocio.liquidacion_intercambio_id' => 0
								),
							array(
								'LiquidacionSocio.liquidacion_id' => $liquidacionID
							)
		);
		//LIMPIO LOS CAMPOS DE LA TABLA LIQUIDACION CUOTAS
		App::import('Model','Mutual.LiquidacionCuota');
		$oLCU = new LiquidacionCuota();		
		$oLCU->updateAll(
							array('LiquidacionCuota.importe_debitado' => 0,'LiquidacionCuota.liquidacion_intercambio_id' => 0),
							array(
								'LiquidacionCuota.liquidacion_id' => $liquidacionID
							)
		);		
	}
	
	/**
	 * limpia las tablas de liquidacionSocio, LiquidacionIntercambioRegistro
	 * @param $liquidacionIntercambioID
	 */
	function limpiarTablaIntercambio(){
		App::import('Model','Mutual.LiquidacionIntercambioRegistro');
		$oFile = new LiquidacionIntercambioRegistro();	
		$oFile->deleteAll("LiquidacionIntercambioRegistro.liquidacion_intercambio_id = " . $this->liquidacionIntercambioID);
	}

	/**
	 * marca como fragmentado al archivo
	 */
	function marcarArchivoFragmentado(){
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();
		$oFile->id = $this->liquidacionIntercambioID;
		$oFile->saveField('fragmentado',1);
	}

	/**
	 * marca como procesada la liquidacion intercambio y graba en la cabecera de la liquidacion
	 * la marca indicando que los archivos de esta liquidacion fueron procesados
	 * 
	 * @param unknown_type $liquidacionID
	 */
	function marcarArchivosProcesados($liquidacionID){
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();
		$oFile->updateAll(
							array('LiquidacionIntercambio.procesado' => 1),
							array('LiquidacionIntercambio.liquidacion_id' => $liquidacionID)		
		);
//		App::import('Model','Mutual.Liquidacion');
//		$oLIQ = new Liquidacion();
//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();			
//		
//		$liquidacion = $oLIQ->read(null,$liquidacionID);
//		$liquidacion['Liquidacion']['archivos_procesados'] = 1;	
//		$liquidacion['Liquidacion']['registros_enviados'] = $oLSR->getCantidadRegistrosRecibidos($liquidacionID);
//		$liquidacion['Liquidacion']['importe_recibido'] = $oLSR->getTotalByLiquidacion($liquidacionID);
//		$liquidacion['Liquidacion']['importe_cobrado'] = $oLSR->getTotalByLiquidacion($liquidacionID);
//		$liquidacion['Liquidacion']['importe_no_cobrado'] = $oLSR->getTotalByLiquidacion($liquidacionID,0);
//		$liquidacion['Liquidacion']['importe_imputado'] = $oLC->getTotalImputadoByLiquidacion($liquidacionID);
//		$liquidacion['Liquidacion']['importe_reintegro'] = $liquidacion['Liquidacion']['importe_cobrado'] - $liquidacion['Liquidacion']['importe_imputado'];
//		
//		$oLiq->save($liquidacion);				
	}	
	
	/**
	 * carga los datos de la liquidacionRegistros
	 * resetea la liquidacion de socios
	 * @param $liquidacionID
	 */
	function cargarDatosImportacion($liquidacionID){
		
		$this->resetTablaLiquidacionSocio($liquidacionID);

		$codigo_organismo = $this->getOrganismoLiquidacion($liquidacionID);
//		$columnaStatus = $this->__getCampoStatus($liquidacionID);
		$columnas = $this->__getCamposConsulta($liquidacionID);
		$columnas_agrupa = $this->__getCamposAgrupa($liquidacionID);

		App::import('Model','Mutual.LiquidacionIntercambioRegistro');
		$oRegistros = new LiquidacionIntercambioRegistro();
	
		$registros = $oRegistros->find('all', array(
													'conditions' => array(
																		'LiquidacionIntercambioRegistro.liquidacion_id' => $liquidacionID,
													),
													'fields' => $columnas,
													'group' => $columnas_agrupa	
		));		
		$datos_importacion = array();
		foreach($registros as $idx => $registro){

			if(isset($registro[0]) && !empty($registro[0])){
				$key = key($registro[0]);
				$valor = $registro[0][$key];
				$registro['LiquidacionIntercambioRegistro'][$key] = $valor;
			}
			array_push($datos_importacion,array_slice($registro,0,1));			
			
		}		
		App::import('Model','Mutual.LiquidacionIntercambioRegistroProcesado');
		$oRegistroProcesado = new LiquidacionIntercambioRegistroProcesado();
		$oRegistroProcesado->deleteAll("LiquidacionIntercambioRegistroProcesado.liquidacion_id = $liquidacionID");			
		return $datos_importacion;		
		
	}
	
	
	/**
	 * devuelve el campo que indica el codigo del status
	 * @param $liquidacionID
	 */
	function __getCampoStatus($liquidacionID){
		$codigo_organismo = $this->getOrganismoLiquidacion($liquidacionID);
		App::import('Model','Mutual.LiquidacionDisenioRegistro');
		$oDSGN = new LiquidacionDisenioRegistro();
		return $oDSGN->getCampoCodigoStatus($codigo_organismo);
	}

	/**
	 * devuelve los campos marcados como consulta
	 * @param $liquidacionID
	 */
	function __getCamposConsulta($liquidacionID){
		$codigo_organismo = $this->getOrganismoLiquidacion($liquidacionID);
		App::import('Model','Mutual.LiquidacionDisenioRegistro');
		$oDSGN = new LiquidacionDisenioRegistro();
		return $oDSGN->getCamposConsulta($codigo_organismo);
	}

	/**
	 * devuelve los campos que se agrupan
	 * @param $liquidacionID
	 */
	function __getCamposAgrupa($liquidacionID){
		$codigo_organismo = $this->getOrganismoLiquidacion($liquidacionID);
		App::import('Model','Mutual.LiquidacionDisenioRegistro');
		$oDSGN = new LiquidacionDisenioRegistro();
		return $oDSGN->getCamposAgrupa($codigo_organismo);
	}	
	
	/**
	 * genera el registro de intercambio
	 * @param unknown_type $registro
	 */
	function generarRegistroIntercambio($registro){
		
		$variables = $this->cargarVariables($registro);
		
		if(!isset($variables['status'])) $variables['status'] = 'OK';
		if(!isset($variables['fecha_pago'])) $variables['fecha_pago'] = date('Y-m-d');
		
		$variables['liquidacion_id'] = $registro['LiquidacionIntercambioRegistro']['liquidacion_id'];
		$variables['codigo_organismo'] = $this->getOrganismoLiquidacion($registro['LiquidacionIntercambioRegistro']['liquidacion_id']);
		$variables['banco_intercambio'] = $registro['LiquidacionIntercambioRegistro']['banco_intercambio'];
		
		App::import('Model','Mutual.LiquidacionIntercambioRegistroProcesado');
		$oRegistroProcesado = new LiquidacionIntercambioRegistroProcesado();

		App::import('Model','Config.BancoRendicionCodigo');
		$oBR = new BancoRendicionCodigo();
		$isPago = $oBR->isCodigoPago($registro['LiquidacionIntercambioRegistro']['banco_intercambio'],$variables['status']);
		
		$variables['indica_pago'] = ($isPago ? 1 : 0);
	
		$procesado['LiquidacionIntercambioRegistroProcesado'] = $variables;
		
		$oRegistroProcesado->id = 0;
		return $oRegistroProcesado->save($procesado);
	}

	/**
	 * carga el intercambio para una liquidacion
	 * ordenado por el campo indica_pago para procesar primero los codigos que no son pagos y
	 * por ultimo los pagos
	 * @param $liquidacionID
	 */
	function cargarIntercambio($liquidacionID){
		App::import('Model','Mutual.LiquidacionIntercambioRegistroProcesado');
		$oRegistroProcesado = new LiquidacionIntercambioRegistroProcesado();
		return $oRegistroProcesado->find('all',array('conditions' => array('LiquidacionIntercambioRegistroProcesado.liquidacion_id' => $liquidacionID),'order' => array('LiquidacionIntercambioRegistroProcesado.indica_pago')));		
	}	
	
	
	
	/**
	 * carga variables de acuerdo a la tabla donde se dise�a el registro
	 * @param $liquidacion_intercambio_id
	 * @param $registro
	 */
	function cargarVariables($registro){
		$variables = array();

		$codigo_organismo = $this->getOrganismoLiquidacion($registro['LiquidacionIntercambioRegistro']['liquidacion_id']);
		
		App::import('Model','Mutual.LiquidacionDisenioRegistro');
		$oDSGN = new LiquidacionDisenioRegistro();	

		$camposVariables = $oDSGN->getCamposVariables($codigo_organismo);
		
		$keys = array_keys($camposVariables);
		foreach($keys as $key){
			$model = $camposVariables[$key][0];
			$field = $camposVariables[$key][1];
			$variables[$key] = $registro[$model][$field];
		}
		return $variables;
	}

	
	function cargarCriterioIgualacion($registro){
		$criterio = array();
		App::import('Model','Mutual.LiquidacionDisenioRegistro');
		$oDSGN = new LiquidacionDisenioRegistro();	

		$camposIgualables = $oDSGN->getCamposIgualables($registro['LiquidacionIntercambioRegistroProcesado']['codigo_organismo']);
		foreach($camposIgualables as $campo){
			$criterio['LiquidacionSocio.'.$campo] = $registro['LiquidacionIntercambioRegistroProcesado'][$campo];
		}
		return $criterio;
	}
	
}
?>
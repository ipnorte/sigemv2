<?php

/**
 * ### PROCESA ARCHIVO DE RENCIDION ###
 * EL MANEJADOR DE LA IMPORTACION DE DATOS ESTA EN EL TASK "intercambio.php"
 * PROCESO POR PASOS:
 * PASO 1: 	FRAGMENTO EL ARCHIVO
 * PASO 2:	GENERO LA TABLA INTERMEDIA
 * PASO 3: 	CRUZO LA TABLA INTERMEDIA CON LA LIQUIDACION SOCIOS
 *
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php procesa_archivo 93 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php procesa_archivo 1334 -app /var/www/sigem/app/
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php procesa_archivo 3636 -app /home/adrian/dev/www/sigem/app/
 * 
 * D:\Desarrollo\xampp\php\php.exe D:\Desarrollo\xampp\htdocs\sigem\cake\console\cake.php procesa_archivo 157 -app D:\Desarrollo\xampp\htdocs\sigem\app\
 * 
 *  
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ProcesaArchivoShell extends Shell {
	
	var $disenioRegistro = null;
	var $liquidacionIntercambioID = 0;
	var $liquidacionID = 0;
	var $codigo_organismo = '';
	var $PROCESS_ID = 0;
	
//	var $tasks = array('Intercambio');
	
	var $uses 		= array(
								'Mutual.LiquidacionIntercambioRegistro',
								'Mutual.LiquidacionSocio',
								'Mutual.Liquidacion',
								'Mutual.LiquidacionCuota',
								'Mutual.LiquidacionSocioRendicion',
								'Mutual.LiquidacionIntercambio',
								'Config.Banco'
							);	
	
	function main() {
		
		$regCobrado = 0;
		$totalCobrado = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$this->PROCESS_ID = $pid;

		$this->liquidacionID		= $asinc->getParametro('p1');
		
//		$this->Intercambio->liquidacionID = $this->liquidacionID;

		
		if($this->__getCampo('bloqueada') == 1):
			$asinc->actualizar(5,100,"PROCESO BLOQUEADO POR OTRO USUARIO....");
			return;
		endif;
		
		//verifico que no haya otros procesos bloqueados
		
		
		$this->__setCampo('bloqueada',1);
		$this->__setCampo('asincrono_id',$pid);		

		$asinc->actualizar(5,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		
		
		//CARGAR TODOS LOS REGISTROS DE LA TABLA DE INTERCAMBIO (CABECERA) PARA LA LIQUIDACION
		$asinc->actualizar(6,100,"CARGANDO ARCHIVOS...");
		$archivos = $this->cargarArchivos();

// 		debug(WWW_ROOT);

		$asinc->actualizar(7,100,"COMENZANDO ANALISIS ARCHIVO...");	
		
		if(!empty($archivos)){
			
			App::import('Model','Config.Banco');
			$oBanco = new Banco();			
			
			#####################################################################################################
			# PASO 1: FRAGMENTO ARCHIVO 
			#####################################################################################################
			
			$ERROR_ARCHIVO = NULL;
			
			foreach($archivos as $archivo):

				if($archivo['LiquidacionIntercambio']['fragmentado'] == 0):
				
				
					$bancoIntercambio = $this->getCampoIntercambio($archivo['LiquidacionIntercambio']['id'],'banco_id');
					$this->LiquidacionSocioRendicion->deleteAll("LiquidacionSocioRendicion.liquidacion_id = " . $this->liquidacionID ." and LiquidacionSocioRendicion.liquidacion_intercambio_id = " . $archivo['LiquidacionIntercambio']['id']);
				
					$rows = $this->getContenidoArchivo($archivo['LiquidacionIntercambio']['id']);

					if(!$rows){
						
						$STOP = 1;
						$ERROR_ARCHIVO = "*** ARCHIVO ".$archivo['LiquidacionIntercambio']['archivo_nombre']." NO ENCONTRADO ***";
						$asinc->actualizar(0,100,$ERROR_ARCHIVO);
// 						$this->LiquidacionIntercambio->updateAll(
// 							array('LiquidacionIntercambio.observaciones' => "'".$ERROR_ARCHIVO."'"),
// 							array('LiquidacionIntercambio.id' => $archivo['LiquidacionIntercambio']['id'])
// 						);						
						break;
					}

					$total = count($rows);
					$asinc->setTotal($total);
					$i = 0;
					
					//controlar el tipo de registro si es cabecera, detalle o pie
					$bancoTipoRegistro = $oBanco->read(null,$bancoIntercambio);
					$tipoRegistro = $bancoTipoRegistro['Banco']['tipo_registro'];
					$idRegistroDetalle = trim($bancoTipoRegistro['Banco']['indicador_detalle']);
					$longitud = $bancoTipoRegistro['Banco']['longitud'];
					
//					if($bancoTipoRegistro['Banco']['id'] == 99999) $longitud = $longRegistro;
					
					
					foreach($rows as $idx => $row){
						
						$linea = $idx + 1;
						
// 						$this->out($row);
						
						if(!empty($row)){
							
							$asinc->actualizar($i,$total,"$i / $total - PASO 1/2 :: FRAGMENTANDO ARCHIVO >> ".$archivo['LiquidacionIntercambio']['archivo_nombre']);
							
							//saco el caracter de fin de linea
							$row = eregi_replace("[\n|\r|\n\r]","", $row);
							
							$longRegistro = strlen($row);
							
							if($longRegistro != $longitud){
								$this->out($row);
								$ERROR_ARCHIVO = "**** ERROR DE LONGITUD DEL ARCHIVO ".$archivo['LiquidacionIntercambio']['archivo_nombre']." LINEA[$linea] ***";
								$STOP = 1;
								break;
							}
							
							if($tipoRegistro == 3){
								$primerCaracter = substr(trim($row),0,1);
								if($idRegistroDetalle == $primerCaracter){
									if(!$this->fragmentarArchivo($row,$archivo['LiquidacionIntercambio']['id'])){
										$ERROR_ARCHIVO = "**** ERROR AL FRAGMENTAR ARCHIVO ".$archivo['LiquidacionIntercambio']['archivo_nombre']." ***";
										$STOP = 1;
										break;
									}
								}
							}else if(!$this->fragmentarArchivo($row,$archivo['LiquidacionIntercambio']['id'])){
								$ERROR_ARCHIVO = "**** ERROR AL FRAGMENTAR ARCHIVO ".$archivo['LiquidacionIntercambio']['archivo_nombre']." ***";
								$STOP = 1;
								break;
							}
							
						}
						
						if($asinc->detenido()){
							$STOP = 1;
							break;
						}							
						$i++;
					}//endfor rows
					
					//controlo si viene un error de procesamiento
					if($STOP == 1 && !empty($ERROR_ARCHIVO)){
						$asinc->actualizar(0,100,$ERROR_ARCHIVO);
						$this->__setCampo('bloqueada',0);
						$this->__setCampo('asincrono_id',0);
// 						$this->LiquidacionIntercambio->updateAll(
// 							array('LiquidacionIntercambio.observaciones' => "'".$ERROR_ARCHIVO."'"),
// 							array('LiquidacionIntercambio.id' => $archivo['LiquidacionIntercambio']['id'])
// 						);						
						return;
					}
					
					$totalRegistros = $this->LiquidacionSocioRendicion->getTotalRegistros($archivo['LiquidacionIntercambio']['id']);
					$regCobrado = $this->LiquidacionSocioRendicion->getRegistrosCobrados($archivo['LiquidacionIntercambio']['id']);
					$totalCobrado = $this->LiquidacionSocioRendicion->getImporteCobrado($archivo['LiquidacionIntercambio']['id']);

					$this->LiquidacionIntercambio->updateAll(
										array('LiquidacionIntercambio.fragmentado' => 1,
											  'LiquidacionIntercambio.total_registros' => $totalRegistros,
											  'LiquidacionIntercambio.registros_cobrados' => $regCobrado,
											  'LiquidacionIntercambio.importe_cobrado' => $totalCobrado),
										array('LiquidacionIntercambio.id' => $archivo['LiquidacionIntercambio']['id'])		
					);					
					
// 					$this-->out($ERROR_ARCHIVO);
					
				endif; //END if($archivo['LiquidacionIntercambio']['fragmentado'] == 0)

// 				if($archivo['LiquidacionIntercambio']['procesado'] == 0 && empty($ERROR_ARCHIVO)):

// 					#################################################################################################
// 					# PRE IMPUTAR ARCHIVO
// 					#################################################################################################
				
// 					$asinc->actualizar(1,100,"COMENZANDO PROCESAMIENTO DE COBROS...");

// 					$sql = "SELECT 
// 								LiquidacionSocioRendicion.socio_id 
// 							FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
// 							WHERE 
// 								LiquidacionSocioRendicion.liquidacion_id = ".$this->liquidacionID."
// 								AND LiquidacionSocioRendicion.liquidacion_intercambio_id = ".$archivo['LiquidacionIntercambio']['id']."
// 								AND IFNULL(LiquidacionSocioRendicion.socio_id,0) <> 0
// 								AND LiquidacionSocioRendicion.indica_pago = 1 "
// 								.
// 								(!empty($archivo['LiquidacionIntercambio']['proveedor_id']) ? 
// 								"
// 								AND LiquidacionSocioRendicion.socio_id IN
// 									(
// 										SELECT 
// 											socio_id 
// 										FROM liquidacion_cuotas 
// 										WHERE 
// 											liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
// 											AND proveedor_id = LiquidacionSocioRendicion.proveedor_id
// 									)
// 								"
// 								: "")
// 								.
								
// 								"	
// 							GROUP BY LiquidacionSocioRendicion.socio_id;";
// 					$socios = $this->LiquidacionSocioRendicion->query($sql);
// 					$socios = Set::extract('/LiquidacionSocioRendicion/socio_id',$socios);
					
// 					if(!empty($socios)):
					
// 						$total = count($socios);
// 						$asinc->setTotal($total);
// 						$i = 0;
					
// 						$periodo = $archivo['LiquidacionIntercambio']['periodo'];
// 						$organismo = $archivo['LiquidacionIntercambio']['codigo_organismo'];
// 						foreach($socios as $socio_id):
						
// 							$asinc->actualizar($i,$total,"$i / $total - PASO 2/2 :: PROCESANDO COBROS [".$archivo['LiquidacionIntercambio']['archivo_nombre']."] >> SOCIO #" . $socio_id);
							
// 							//REPROCESAR LA LIQUIDACION CUOTAS
// 							$ret = $this->LiquidacionSocio->liquidar($socio_id,$periodo,$organismo,$this->liquidacionID,false);
// 							if(isset($ret[0]) && $ret[0] == 1):
// 								$asinc->actualizar($i,$total,"$i / $total - PASO 2/2 :: PROCESANDO COBROS >> SOCIO #" . $socio_id ." - ".$ret[1]);
// 								$STOP = 1;
// 								break;
// 							endif;
							
// // 							debug($archivo);
							
// 							if(substr($organismo,8,2) != 77) $cuotas = $this->LiquidacionCuota->armaImputacion($this->liquidacionID,$socio_id,$archivo['LiquidacionIntercambio']['proveedor_id']);
// 							else $cuotas = $this->LiquidacionCuota->armaImputacionCJP($this->liquidacionID,$socio_id);
// 							if(!empty($cuotas)):
// 								if(!$this->LiquidacionCuota->saveAll($cuotas)){
// 									$STOP = 1;
// 									break;
// 								}
// 							endif;
// 							if($asinc->detenido()){
// 								$STOP = 1;
// 								break;
// 							}
// 							$i++;
						
// 						endforeach;
						
						
// 						if($STOP != 1):
						
// 							$this->LiquidacionIntercambio->updateAll(
// 									array('LiquidacionIntercambio.procesado' => 1),
// 									array('LiquidacionIntercambio.id' => $archivo['LiquidacionIntercambio']['id'])
// 							);
						
// 						endif;					
						
					
// 					endif;
				
// 				endif;	
				

			endforeach; //FIN ARCHIVOS foreach($archivos as $archivo):
			
			#####################################################################################################
			# PASO 2: PROCESAR LO COBRADO
			#####################################################################################################
			$asinc->actualizar(1,100,"COMENZANDO PROCESAMIENTO DE COBROS...");
			$socios = $this->cargarSociosCobrados();
			$total = count($socios);
			$asinc->setTotal($total);
			$i = 0;
			
			if(!empty($socios)):
				
				$periodo = $this->__getCampo('periodo');
				$organismo = $this->__getCampo('codigo_organismo');
					
				foreach($socios as $socio_id):
				
					$asinc->actualizar($i,$total,"$i / $total - PASO 2/2 :: PROCESANDO COBROS >> SOCIO #" . $socio_id);
						
					//REPROCESAR LA LIQUIDACION CUOTAS
					$ret = $this->LiquidacionSocio->liquidar($socio_id,$periodo,$organismo,$this->liquidacionID,false);
					if(isset($ret[0]) && $ret[0] == 1):
						$asinc->actualizar($i,$total,"$i / $total - PASO 2/2 :: PROCESANDO COBROS >> SOCIO #" . $socio_id ." - ".$ret[1]);
						$STOP = 1;
						break;
					endif;
						
					$proveedores = $this->LiquidacionSocioRendicion->find("all",array('conditions' => array("LiquidacionSocioRendicion.liquidacion_id" => $this->liquidacionID, "LiquidacionSocioRendicion.socio_id" => $socio_id), "fields" => array("LiquidacionSocioRendicion.proveedor_id"), "group" => array("LiquidacionSocioRendicion.proveedor_id")));
					if(!empty($proveedores)):
						//imputo en base a si un archivo fue vinculado a un proveedor especifico
						foreach($proveedores as $proveedor):
							if(substr($organismo,8,2) != 77) $cuotas = $this->LiquidacionCuota->armaImputacion($this->liquidacionID,$socio_id,$proveedor['LiquidacionSocioRendicion']['proveedor_id']);
							else $cuotas = $this->LiquidacionCuota->armaImputacionCJP($this->liquidacionID,$socio_id);
							if(!empty($cuotas)):
								if(!$this->LiquidacionCuota->saveAll($cuotas)){
									$STOP = 1;
									break;
								}
							endif;
						endforeach;
					else:
						//esquema de imputacion normal
						if(substr($organismo,8,2) != 77) $cuotas = $this->LiquidacionCuota->armaImputacion($this->liquidacionID,$socio_id);
						else $cuotas = $this->LiquidacionCuota->armaImputacionCJP($this->liquidacionID,$socio_id);
						if(!empty($cuotas)):
							if(!$this->LiquidacionCuota->saveAll($cuotas)){
								$STOP = 1;
								break;
							}
						endif;
					endif;					
					
					$i++;
					
					if($asinc->detenido()){
						$STOP = 1;
						break;
					}
					
				endforeach; //END foreach($socios as $socio_id):
				
				if($STOP != 1):
					$this->LiquidacionIntercambio->updateAll(
						array('LiquidacionIntercambio.procesado' => 1),
						array('LiquidacionIntercambio.liquidacion_id' => $this->liquidacionID)
					);
					$this->Liquidacion->updateAll(
						array('Liquidacion.archivos_procesados' => 1),
						array('Liquidacion.id' => $this->liquidacionID)
					);
				
				endif; 
			
			endif;// END if(!empty($socios)):
				
			$this->__setTotales();			
		
				
		}else{ //if(!empty($archivos))
			
			$asinc->actualizar(1,100,"NO EXISTEN ARCHIVOS PENDIENTES DE PROCESAR");
			$asinc->fin("**** NO EXISTEN ARCHIVOS PENDIENTES DE PROCESAR ****");	
			$this->__setCampo('bloqueada',0);
			$this->__setCampo('asincrono_id',0);	
		
		} //endif empty $archivos

		
		if($STOP == 1){
			$this->__setCampo('bloqueada',0);
			$this->__setCampo('asincrono_id',0);			
		}		
		
		if($STOP == 2){
			$asinc->actualizar(1,100,"ERROR AL INTENTAR CARGAR EL ARCHIVO!.");
			$asinc->fin("**** PROCESO FINALIZADO ****");
			$this->__setCampo('bloqueada',0);
			$this->__setCampo('asincrono_id',0);		
		}		
		
		if($STOP == 0){
			$asinc->actualizar(99,100,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
			$this->__setCampo('bloqueada',0);
			$this->__setCampo('asincrono_id',0);		
		}
		
		
		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS PARA EL CRUZAMIENTO DE LA INFORMACION
	####################################################################################################
	
	/**
	 * devuelve un campo especificado de la tabla liquidaciones
	 * @param $field
	 * @return contenido del campo
	 */
	function __getCampo($field){
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$liquidacion = $oLQ->read($field,$this->liquidacionID);
		return $liquidacion['Liquidacion'][$field];
	}
	/**
	 * setea un valor de un campo para la liquidacion
	 * @param $field
	 * @param $value
	 */
	function __setCampo($field,$value){
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$oLQ->id = $this->liquidacionID;
		return $oLQ->saveField($field,$value);
	}	
	
	/**
	 * carga la liquidacion socios
	 */
	function __getLiquidacionSocios(){
		$liquidacion_id = $this->liquidacionID;
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		$socios = $oLS->find('all',array('conditions' => array('LiquidacionSocio.liquidacion_id' => $liquidacion_id), 'order' => array('LiquidacionSocio.apenom')));
		return $socios;		
	}
	
	/**
	 * Procesa el intercambio en forma unificada con el proceso de reliquidacion
	 * @param $liquidacionSocio
	 */
	function __procesarIntercambioLiquidacionSocio($liquidacionSocio){
		$liquidacion_id = $this->liquidacionID;
		$socio_id = $liquidacionSocio['LiquidacionSocio']['socio_id'];
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();		
		
//		if(!$oLS->procesarArchivoIntercambio($socio_id,$liquidacion_id)) return false;
		if(!$oLS->procesarPreImputacion($socio_id,$liquidacion_id)) return false;
		
		//CONTROLAR
//		App::import('Model','Shells.AsincronoError');
//		$oERROR = new AsincronoError();
//		$error = array();
//		
//		$TOTAL_COBRADO_SOCIO = $oLSR->getTotalBySocioByLiquidacion($socio_id,$liquidacion_id,1);
//		$TOTAL_IMPUTADO_SOCIO = $oLC->getTotalImputadoBySocioByLiquidacion($liquidacion_id,$socio_id);
//		
//		App::import('Model','Mutual.LiquidacionIntercambioRegistroProcesado');
//		$oRP = new LiquidacionIntercambioRegistroProcesado();
//		
//		$TOTAL_COBTADO_REG = $oRP->getTotalCobrado($liquidacionSocio);
//		
//		if($TOTAL_COBRADO_SOCIO != $TOTAL_COBTADO_REG){
//			$error['AsincronoError']['asincrono_id'] = $this->PROCESS_ID;
//			$error['AsincronoError']['mensaje_1'] = $liquidacionSocio['LiquidacionSocio']['documento']." - ".$liquidacionSocio['LiquidacionSocio']['apenom'];
//			$error['AsincronoError']['mensaje_2'] = "EL TOTAL COBRADO SEGUN ARCHIVO NO COINCIDE CON EL TOTAL COBRADO DE LA LIQUIDACION DEL SOCIO!";
//			$oERROR->save($error);
//		}
//		
//		App::import('Model','Mutual.LiquidacionCuota');
//		$oLC = new LiquidacionCuota();
//
//		$TOTAL_IMPUTADO_CUOTAS = $oLC->getTotalImputadoBySocioByLiquidacion($liquidacion_id,$socio_id);
//		if($TOTAL_IMPUTADO_SOCIO != $TOTAL_IMPUTADO_CUOTAS){
//			$error['AsincronoError']['asincrono_id'] = $this->PROCESS_ID;
//			$error['AsincronoError']['mensaje_1'] = $liquidacionSocio['LiquidacionSocio']['documento']." - ".$liquidacionSocio['LiquidacionSocio']['apenom'];
//			$error['AsincronoError']['mensaje_2'] = "EL TOTAL IMPUTADO DE LA LIQUIDACION DEL SOCIO NO COINCIDE CON LA IMPUTACION EN LA LIQUIDACION CUOTAS!";
//			$oERROR->save($error);
//		}		
		
		return true;
	}
	
	
	function getCampoIntercambio($id,$field){
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();
		$archivo = $oFile->read($field,$id);
		return $archivo['LiquidacionIntercambio'][$field];
	}	
	
	
	function getContenidoArchivo($intercambio_id){
		$registros = array();
		$path = WWW_ROOT . $this->getCampoIntercambio($intercambio_id,'archivo_file');
		if(!file_exists($path)) return false;
		$registros = array();
		$registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if(!is_array($registros)) return null;
		// limpiar cadenas
		foreach ($registros as $i => $registro) {
// 		    $registros[$i] = preg_replace('/[\x00-\x1F\x7F]/', '', $registro);
// 		    $registros[$i] = preg_replace('/[\x00-\x1F\x7F]/', '',utf8_encode($registro));
		    $registros[$i] = preg_replace("[^A-Za-z0-9]", "",$registro);
		}	
		return $registros;
	}	
	
	function cargarArchivos(){
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();
		$archivos = $oFile->find('all',array('conditions' => array(
													'LiquidacionIntercambio.liquidacion_id' => $this->liquidacionID,
													'LiquidacionIntercambio.procesado' => 0
											)
		));
		return $archivos;
	}	
	
	
	function fragmentarArchivo($registro,$intercambio_id){
		
		$bancoIntercambio = $this->getCampoIntercambio($intercambio_id,'banco_id');
		$organismo = $this->getCampoIntercambio($intercambio_id,'codigo_organismo');
		$liquidacion_id = $this->getCampoIntercambio($intercambio_id,'liquidacion_id');
		
		$imputaProveedorId = $this->getCampoIntercambio($intercambio_id,'proveedor_id');
		
		$rendicionSocio = array();
		$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_id'] = $liquidacion_id;
		$rendicionSocio['LiquidacionSocioRendicion']['codigo_organismo'] = $organismo;
		$rendicionSocio['LiquidacionSocioRendicion']['registro'] = $registro;
		$rendicionSocio['LiquidacionSocioRendicion']['periodo'] = $this->getCampoIntercambio($intercambio_id,'periodo');
		$rendicionSocio['LiquidacionSocioRendicion']['banco_intercambio'] = $bancoIntercambio;
		$rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = 0;
		$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_intercambio_id'] = $intercambio_id;
		
		if(!empty($imputaProveedorId)) $rendicionSocio['LiquidacionSocioRendicion']['proveedor_id'] = $imputaProveedorId;
		
		$organismo = substr($organismo,8,2);
		
		if($organismo == 22):
		
			#############################################################################################
			#BANCO DE CORDOBA
			#############################################################################################
			if($bancoIntercambio == '00020') $decode = $this->Banco->decodeStringDebitoBcoCba($registro);
			
			#############################################################################################
			#STANDAR BANK
			#############################################################################################
			if($bancoIntercambio == '00430') $decode = $this->Banco->decodeStringDebitoStandarBank($registro);

			#############################################################################################
			#BANCO NACION
			#############################################################################################
			if($bancoIntercambio == '00011') $decode = $this->Banco->decodeStringDebitoBcoNacion($registro);
			

			#############################################################################################
			#BANCO CREDICOOP
			#############################################################################################
			if($bancoIntercambio == '00191') $decode = $this->Banco->decodeStringDebitoBancoCredicoop($registro);
			
			
			
			if(empty($decode)) return false;
			
			foreach($decode as $key => $value){
				$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
			}
			
                        $rendicionSocio['LiquidacionSocioRendicion']['liquidacion_id'] = $liquidacion_id;
                        
			//si no detecto el id del socio lo busco por el campo liquidacion_socio_id
			if(empty($rendicionSocio['LiquidacionSocioRendicion']['socio_id']) && isset($rendicionSocio['LiquidacionSocioRendicion']['liquidacion_socio_id'])){
				$socio = $this->LiquidacionSocio->read(null,$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_socio_id']);
				if(!empty($socio)) $rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = $socio['LiquidacionSocio']['socio_id'];
			}
			
//			$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
//						'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
//						'LiquidacionSocio.documento' => $rendicionSocio['LiquidacionSocioRendicion']['documento']
//			)));
//			
//			$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);
//			debug($decode);
//			debug($rendicionSocio);
			
		endif;

		#############################################################################################
		#CAJA DE JUBILACIONES
		#############################################################################################
		
		if($organismo == 77):
		
//			$decode = $this->Banco->decodeStringDebitoCJP($registro);
			$decode = $this->Banco->decodeNuevoStringDebitoCJP($registro);
			
			if(empty($decode)) return false;
			
			foreach($decode as $key => $value){
				$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
			}
			
			$beneficio = intval($rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio']);
			
			$rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio'] = str_pad($beneficio,6,0,STR_PAD_LEFT);
			
			$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
						'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
						'LiquidacionSocio.tipo' => $rendicionSocio['LiquidacionSocioRendicion']['tipo'],
						'LiquidacionSocio.nro_ley' => $rendicionSocio['LiquidacionSocioRendicion']['nro_ley'],
						'LiquidacionSocio.nro_beneficio' => $rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio'],
						'LiquidacionSocio.sub_beneficio' => $rendicionSocio['LiquidacionSocioRendicion']['sub_beneficio'],
						'LiquidacionSocio.codigo_dto' => $rendicionSocio['LiquidacionSocioRendicion']['codigo_dto'],
						'LiquidacionSocio.sub_codigo' => $rendicionSocio['LiquidacionSocioRendicion']['sub_codigo'],
			)));
			
			$socio_id = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);
			
			//lo busco en la liquidacion socios por documento
			if($socio_id==0):
				$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
							'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
							'LiquidacionSocio.documento' => $rendicionSocio['LiquidacionSocioRendicion']['documento']
				)));
				$socio_id = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);			
			endif;
			
			$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = $socio_id;
			
			//controlar la rendicion especial para el caso de las ordenes que sean mayores al 800000
			//para los periodos de 201111, 201112, 201201
			if($rendicionSocio['LiquidacionSocioRendicion']['periodo'] >= '201112' && $rendicionSocio['LiquidacionSocioRendicion']['periodo'] <= '201202'){
				if($rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_id'] >= 800000 ){
                                    $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_id'] = $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_id'] - 800000;
                                }
			}
		
		endif;
		
		#############################################################################################
		#ANSES
		#############################################################################################
		
		if($organismo == 66):

			$decode = $this->Banco->decodeStringDebitoANSES($registro);
			
			foreach($decode as $key => $value){
				$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
			}
			
			$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
						'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
						'LiquidacionSocio.nro_beneficio' => $rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio'],
						'LiquidacionSocio.codigo_dto' => $rendicionSocio['LiquidacionSocioRendicion']['codigo_dto'],
			)));
			
			$socio_id = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);

			//lo busco en la liquidacion socios por documento
			if($socio_id==0):
				$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
							'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
							'LiquidacionSocio.documento' => $rendicionSocio['LiquidacionSocioRendicion']['documento']
				)));
				$socio_id = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);			
			endif;
			
			$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = $socio_id;
		
		
		endif;
		
// 		$this->out($registro);
// 		debug($rendicionSocio);

		$this->LiquidacionSocioRendicion->id = 0;
		return $this->LiquidacionSocioRendicion->save($rendicionSocio);		
		
	}
	
	function cargarSociosCobrados(){
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $this->liquidacionID;
		$conditions['LiquidacionSocioRendicion.indica_pago'] = 1;
		$conditions['LiquidacionSocioRendicion.socio_id <>'] = 0;
//		$conditions['LiquidacionSocioRendicion.socio_id'] = 4466;
		$fields = array('LiquidacionSocioRendicion.socio_id');
		$group = array('LiquidacionSocioRendicion.socio_id');
		$socios = $this->LiquidacionSocioRendicion->find('all',array('conditions' => $conditions,'fields' => $fields, 'group' => $group));
		$socios = Set::extract('/LiquidacionSocioRendicion/socio_id',$socios);
		return $socios;
	}
	
	function __setTotales(){
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$liquidacion = $oLQ->read(null,$this->liquidacionID);
		
	
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();

		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
	
		$liquidacion['Liquidacion']['registros_recibidos'] = $oLSR->getCantidadRegistrosRecibidos($this->liquidacionID);
		$liquidacion['Liquidacion']['importe_cobrado'] = $oLSR->getTotalByLiquidacion($this->liquidacionID,1);
		$liquidacion['Liquidacion']['importe_no_cobrado'] = $oLSR->getTotalByLiquidacion($this->liquidacionID,0);
		$liquidacion['Liquidacion']['importe_imputado'] = $oLC->getTotalImputadoByLiquidacion($this->liquidacionID);
		$liquidacion['Liquidacion']['importe_reintegro'] = $liquidacion['Liquidacion']['importe_cobrado'] - $liquidacion['Liquidacion']['importe_imputado'];
		
//		$liquidacion['Liquidacion']['fecha_imputacion'] = $this->fecha_pago;
//		$liquidacion['Liquidacion']['nro_recibo'] = $this->nro_recibo;
		
		return $oLQ->save($liquidacion);

	}	
	
}
?>
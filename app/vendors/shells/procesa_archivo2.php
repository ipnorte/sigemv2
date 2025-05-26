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
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php procesa_archivo2 93 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /datos/www/sigem/cake/console/cake.php procesa_archivo2 3711 -app /datos/www/sigem/app/
 * /usr/bin/php5 /home/mutual22/public_html/sigem/cake/console/cake.php procesa_archivo2 380 -app /home/mutual22/public_html/sigem/app/
 * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php procesa_archivo2 42917 -app /home/mutualam/public_html/sigem/app/
 *	C:\wamp64\bin\php\php5.6.40\php.exe C:\wamp64\www\sigemv2\cake\console\cake.php procesa_archivo2 40551  -app C:\wamp64\www\sigemv2\app\
 * D:\Desarrollo\xampp\php\php.exe D:\Desarrollo\xampp\htdocs\sigem\cake\console\cake.php procesa_archivo2 157 -app D:\Desarrollo\xampp\htdocs\sigem\app\
 *
 * /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php procesa_archivo2 71748 -app /home/adrian/Trabajo/www/sigemv2/app/
 * /usr/bin/php5 /var/www/solydar/cake/console/cake.php procesa_archivo2 103 -app /var/www/solydar/sigem/app/
 * 
 * * /usr/bin/php5 /home/cordobas/public_html/solydar/cake/console/cake.php procesa_archivo2 16064 -app /home/cordobas/public_html/solydar/app/
 *
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ProcesaArchivo2Shell extends Shell {

	var $disenioRegistro = null;
	var $liquidacionIntercambioID = 0;
	var $liquidacionID = 0;
	var $codigo_organismo = '';
	var $PROCESS_ID = 0;
        var $CJP_SCOD_CSOC = '0';



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

        Configure::write('debug',1);

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

		$this->__setCampo('cerrada',1);
		//$this->__setCampo('bloqueada',1);
		$this->__setCampo('asincrono_id',$pid);

		$asinc->actualizar(5,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;

                $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                $this->CJP_SCOD_CSOC = (isset($INI_FILE['intercambio']['CJP_SCOD_CSOC']) && !empty($INI_FILE['intercambio']['CJP_SCOD_CSOC']) ? $INI_FILE['intercambio']['CJP_SCOD_CSOC'] : $this->CJP_SCOD_CSOC);

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

                                        $idRegistroCabecera = trim($bancoTipoRegistro['Banco']['indicador_cabecera']);
                                        $idRegistroPie = trim($bancoTipoRegistro['Banco']['indicador_pie']);


//					$longitud = $bancoTipoRegistro['Banco']['longitud_salida'];
                                        $longitud = $oBanco->getLongitudRegistro($bancoIntercambio,'OUT');

//					if($bancoTipoRegistro['Banco']['id'] == 99999) $longitud = $longRegistro;

                                        /**************************************************************************
                                         * PARA TIPO 3 QUITAR EL HEADER Y EL TRAILER
                                         **************************************************************************/
                                        if($tipoRegistro == 3){
                                            if(!empty($idRegistroCabecera)){array_shift($rows);}
                                            if(!empty($idRegistroPie)){array_pop($rows);}
                                        }


					foreach($rows as $idx => $row){

						$linea = $idx + 1;

// 						$this->out($row);

						if(!empty($row)){

                                                    $asinc->actualizar($i,$total,"$i / $total - PASO 1/2 :: FRAGMENTANDO ARCHIVO >> ".$archivo['LiquidacionIntercambio']['archivo_nombre']);

                                                    //saco el caracter de fin de linea
                                                    $row = preg_replace("[\n|\r|\n\r]","", $row);

                                                    $longRegistro = strlen($row);
                                                    # AGREGADO CON URGENCIA PARA QUE NO CONTROLE LA LONGUITUD DEL REGISTRO DE ANSES
                                                    if($bancoTipoRegistro['Banco']['id'] != 99999):

                                                            if($tipoRegistro == 3 && $idRegistroDetalle != substr(trim($row),0,strlen(trim($idRegistroDetalle)))){
                                                                    $longRegistro = $longitud;
                                                            }
                                                            if($longRegistro < $longitud){
                                                                    $this->out($row);
                                                                    $ERROR_ARCHIVO = " **** ERROR DE LONGITUD DEL ARCHIVO ".$archivo['LiquidacionIntercambio']['archivo_nombre']." LINEA[$linea] ***";
                                                                    $STOP = 1;
                                                                    break;
                                                            }
                                                    endif;

                                                    if(!$this->fragmentarArchivo($row,$archivo['LiquidacionIntercambio']['id'])){
                                                            $ERROR_ARCHIVO = "**** ERROR AL FRAGMENTAR ARCHIVO ".$archivo['LiquidacionIntercambio']['archivo_nombre']." ***";
                                                            $STOP = 1;
                                                            break;
                                                    }


						}
						$i++;
					}//endfor rows

					//controlo si viene un error de procesamiento
					if($STOP == 1 && !empty($ERROR_ARCHIVO)){
						$asinc->actualizar(0,100,$ERROR_ARCHIVO);
						$this->__setCampo('bloqueada',0);
						$this->__setCampo('asincrono_id',0);
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

// 				$archivo['LiquidacionIntercambio']['procesado'] = 1;

				if($archivo['LiquidacionIntercambio']['procesado'] == 0 && empty($ERROR_ARCHIVO)):

					#################################################################################################
					# PRE IMPUTAR ARCHIVO
					#################################################################################################

					$asinc->actualizar(1,100,"COMENZANDO PROCESAMIENTO DE COBROS...");

					$sql = "SELECT
								LiquidacionSocioRendicion.socio_id
							FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
							WHERE
								LiquidacionSocioRendicion.liquidacion_id = ".$this->liquidacionID."
								AND LiquidacionSocioRendicion.liquidacion_intercambio_id = ".$archivo['LiquidacionIntercambio']['id']."
								AND IFNULL(LiquidacionSocioRendicion.socio_id,0) <> 0
								AND LiquidacionSocioRendicion.indica_pago = 1 "
								.
								(!empty($archivo['LiquidacionIntercambio']['proveedor_id']) ?
								"
								AND LiquidacionSocioRendicion.socio_id IN
									(
										SELECT
											socio_id
										FROM liquidacion_cuotas
										WHERE
											liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
											AND proveedor_id = LiquidacionSocioRendicion.proveedor_id
									)
								"
								: "")
								.

								"
							GROUP BY LiquidacionSocioRendicion.socio_id;";
					$socios = $this->LiquidacionSocioRendicion->query($sql);
					$socios = Set::extract('/LiquidacionSocioRendicion/socio_id',$socios);

					if(!empty($socios)):

						$total = count($socios);
						$asinc->setTotal($total);
						$i = 0;

						$periodo = $archivo['LiquidacionIntercambio']['periodo'];
						$organismo = $archivo['LiquidacionIntercambio']['codigo_organismo'];

						foreach($socios as $socio_id):

							$asinc->actualizar($i,$total,"$i/$total-PASO 2/2::PROCESANDO COBROS [#".$archivo['LiquidacionIntercambio']['archivo_nombre']."] >> SOCIO #" . $socio_id);

							//REPROCESAR LA LIQUIDACION CUOTAS
							//GENERA NUEVAMENTE LA TABLA LIQUIDACION_CUOTAS
//							reliquidar($socio_id,$periodo,$cerrada=1,$imputada=0,$organismo=null,$soloProcesaIntercambio=false,$excludeLiquidacionBloquedas = true, $excludeLiquidacionEnProceso = true)
							$ret = $this->LiquidacionSocio->reliquidar($socio_id,$periodo,true,false,$organismo,false,false,false);
							if(isset($ret[0]) && $ret[0] == 1):
								$asinc->actualizar($i,$total,"$i / $total - PASO 2/2 :: PROCESANDO COBROS >> SOCIO #" . $socio_id ." - ".$ret[1]);
								$STOP = 1;
								break;
							endif;

//							if($asinc->detenido()){
//								$STOP = 1;
//								break;
//							}
							$i++;

						endforeach;


					endif; //END if(!empty($socios)):

					if($STOP != 1):

						$this->LiquidacionIntercambio->updateAll(
						array('LiquidacionIntercambio.procesado' => 1),
						array('LiquidacionIntercambio.id' => $archivo['LiquidacionIntercambio']['id'])
						);

					endif;

				endif;	// END if($archivo['LiquidacionIntercambio']['procesado'] == 0 && empty($ERROR_ARCHIVO)):


			endforeach; //FIN ARCHIVOS foreach($archivos as $archivo):

			#######################################################################################################
			# PROCESO DE CONCILIACION DE ALTAS Y BAJAS INFORMADAS (SOLO PARA JP)
			#######################################################################################################
//			$codigoOrganismo = $this->__getCampo('codigo_organismo');
//			if(substr($codigoOrganismo,8,2) == 77):
//				$asinc->actualizar(0,5,"CARGANDO ORDENES PARA CONCILIAR");
//
//				App::import('Model','Mutual.OrdenDescuento');
//				$oORDEN = new OrdenDescuento();
//
//				$sql = "SELECT OrdenDescuento.id
//						FROM orden_descuentos AS OrdenDescuento
//						INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
//						WHERE PersonaBeneficio.codigo_beneficio = '$codigoOrganismo'
//						AND  OrdenDescuento.alta_informada = 0 AND OrdenDescuento.baja_informada = 0";
//
//				$ordenes = $oORDEN->query($sql);
//
//				if(!empty($ordenes)):
//
//					foreach ($ordenes as $orden):
//
//						debug($orden);
//
//					endforeach;
//
//				endif;
//
//			endif;



			############################################################################################
			#	PROCESO LOS CASOS QUE HAY QUE GENERAR UN REINTEGRO ANTICIPADO PENDIENTE DE PAGO
			############################################################################################


			if($STOP != 1):
				$this->Liquidacion->updateAll(
					array('Liquidacion.archivos_procesados' => 1,'Liquidacion.scoring' => 1),
					array('Liquidacion.id' => $this->liquidacionID)
				);
				$this->__setTotales();
			endif;


		}else{ //if(!empty($archivos))


//			#######################################################################################################
//			# TESTING PROCESO DE CONCILIACION DE ALTAS Y BAJAS INFORMADAS (SOLO PARA JP)
//			#######################################################################################################
//			$codigoOrganismo = $this->__getCampo('codigo_organismo');
//
//			if(substr($codigoOrganismo,8,2) == 77):
//
//				$periodo = $this->__getCampo('periodo');
//
//				$asinc->actualizar(0,5,"CARGANDO ORDENES PARA CONCILIAR");
//
//				$this->out("CARGANDO ORDENES PARA CONCILIAR");
//
//				App::import('Model','Mutual.OrdenDescuento');
//				$oORDEN = new OrdenDescuento();
//
//				App::import('model','Mutual.OrdenDescuentoCuota');
//				$oCUOTA = new OrdenDescuentoCuota();
//
//
//				$sql = "SELECT OrdenDescuento.id
//						FROM orden_descuentos AS OrdenDescuento
//						INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
//						WHERE PersonaBeneficio.codigo_beneficio = '$codigoOrganismo'
//						AND OrdenDescuento.periodo_ini <= '$periodo'
//						AND  OrdenDescuento.alta_informada = 0 AND OrdenDescuento.baja_informada = 0";
//
//				$ordenes = $oORDEN->query($sql);
//
//				if(!empty($ordenes)):
//
//					foreach($ordenes as $orden):
//
//						$orden_id = $orden['OrdenDescuento']['id'];
//
//
//						$saldos = $oCUOTA->getSaldosByOrdenDto($orden_id,$periodo,$codigoOrganismo);
//						$saldo = $saldos['saldo'];
//
//						$cantidad = $this->LiquidacionSocioRendicion->find('count',
//									array('conditions' => array('LiquidacionSocioRendicion.liquidacion_id' => $this->liquidacionID, 'LiquidacionSocioRendicion.orden_descuento_id' => $orden_id))
//									);
//
//						#MARCO LAS BAJAS CONCILIADAS
//						if($saldo == 0 && $cantidad == 0){
//							$accion = "BAJA INF";
//							$oORDEN->id = $orden_id;
//							$oORDEN->saveField('baja_informada', 1);
//							$oORDEN->saveField('baja_informada_periodo', $periodo);
//							$this->out($orden_id."\t".$saldo."\t".$cantidad."\t\t".$accion."\t\t".$periodo);
//						}
//
//						#MARCO LAS ALTAS CONCILIADAS
//						if($saldo != 0 && $cantidad != 0){
//							$accion = "ALTA INF";
//							$oORDEN->id = $orden_id;
//							$oORDEN->saveField('alta_informada', 1);
//							$oORDEN->saveField('alta_informada_periodo', $periodo);
//
//						}
//
//						if($saldo == 0 && $cantidad != 0) $accion = "BAJA PEN";
//						if($saldo != 0 && $cantidad == 0) $accion = "ALTA PEN";
//
//						$this->out($orden_id."\t".$saldo."\t".$cantidad."\t\t".$accion."\t\t".$periodo);
//
//						//verifico si esta la orden en la liquidacion socio rendiciones
//
//
//					endforeach; //endforeach ordenes
//
//				endif; //if empty ordenes
//
//			endif;	//if codigoOrganismo

			$asinc->actualizar(1,100,"NO EXISTEN ARCHIVOS PENDIENTES DE PROCESAR");
			$asinc->fin("**** NO EXISTEN ARCHIVOS PENDIENTES DE PROCESAR ****");
			$this->__setCampo('bloqueada',0);
			$this->__setCampo('asincrono_id',0);

		} //endif empty $archivos


		if($STOP == 1){
//			$this->__setCampo('bloqueada',0);
//			$this->__setCampo('asincrono_id',0);
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
// 		$path = $this->getCampoIntercambio($intercambio_id,'target_path');
		$path = WWW_ROOT . $this->getCampoIntercambio($intercambio_id,'archivo_file');
                $registros = array();
		if(!file_exists($path)){
                    $lote = $this->getCampoIntercambio($intercambio_id,'lote');
                    $registros = array_filter(explode("\r\n", $lote));
//                    debug($registros);
//                    exit;

                }else{
                    $registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                }
		if(!is_array($registros)) return null;
		
		// limpiar cadenas
		foreach ($registros as $i => $registro) {
// 		    $registros[$i] = preg_replace('/[\x00-\x1F\x7F]/', '',utf8_encode($registro));
		    $registros[$i] = preg_replace("[^A-Za-z0-9]", "",$registro);
		}
		
		return $registros;

//		if(!file_exists($path)) return false;
//		$handle = fopen($path, "rb");
//		$contents = '';
//		while (!feof($handle)) {
//		  $contents .= fread($handle, 8192);
//		}
//		fclose($handle);
//		$registros = explode("\n",$contents);
//
//		if(!is_array($registros)) return null;
//		#determinar que tipo de registro es para saber si tengo que eliminar el primero y el ultimo
//		App::import('Model','Config.Banco');
//		$oBanco = new Banco();
//		$tipo = $oBanco->read('tipo_registro',$this->getCampoIntercambio($intercambio_id,'banco_id'));
//		$tipo = $tipo['Banco']['tipo_registro'];
//		if($tipo == 3){
//			$primero = array_shift($registros);
//			$ultimo = array_pop($registros);
//			if(empty($ultimo))$ultimo = array_pop($registros);
//		}
//		return $registros;
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
		$rendicionSocio['LiquidacionSocioRendicion']['id'] = 0;
		$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_id'] = $this->liquidacionID;
		$rendicionSocio['LiquidacionSocioRendicion']['codigo_organismo'] = $organismo;
		$rendicionSocio['LiquidacionSocioRendicion']['registro'] = $registro;
		$rendicionSocio['LiquidacionSocioRendicion']['periodo'] = $this->getCampoIntercambio($intercambio_id,'periodo');
		$rendicionSocio['LiquidacionSocioRendicion']['banco_intercambio'] = $bancoIntercambio;
		$rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = 0;
		$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_intercambio_id'] = $intercambio_id;

		if (!empty($imputaProveedorId)) {
			$rendicionSocio['LiquidacionSocioRendicion']['proveedor_id'] = $imputaProveedorId;
		}

		$organismo = substr($organismo,8,2);

		if($organismo == 22):

			$decode = $this->Banco->getRegistroDecodificado($bancoIntercambio,$registro);
			if (empty($decode)) {
				return false;
			}

			#si esta seteada el id de la liquidacion y viene vacio no guardar
			#fix problema con bbva
			if(isset($decode['liquidacion_id']) && empty($decode['liquidacion_id'])){
				return TRUE;
			}
			if(isset($decode['ref_univ']) && empty($decode['ref_univ'])){
				return TRUE;
			}




			foreach($decode as $key => $value){
				$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
			}

                        $rendicionSocio['LiquidacionSocioRendicion']['liquidacion_id'] = $this->liquidacionID;

			//si no detecto el id del socio lo busco por el campo liquidacion_socio_id
            if(isset($rendicionSocio['LiquidacionSocioRendicion']['liquidacion_socio_id'])){
                if(empty($rendicionSocio['LiquidacionSocioRendicion']['socio_id']) && isset($rendicionSocio['LiquidacionSocioRendicion']['liquidacion_socio_id'])){
                    $socio = $this->LiquidacionSocio->read(null,$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_socio_id']);
                    if(!empty($socio)) $rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = $socio['LiquidacionSocio']['socio_id'];
                }
            }

			//si no viene el id del socio lo busco por el nro de cbu
            if(isset($rendicionSocio['LiquidacionSocioRendicion']['cbu'])){
                if(empty($rendicionSocio['LiquidacionSocioRendicion']['socio_id']) && !empty($rendicionSocio['LiquidacionSocioRendicion']['cbu'])){
                    $socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
                                'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
                                'LiquidacionSocio.cbu' => $rendicionSocio['LiquidacionSocioRendicion']['cbu']
                    )));
                    $rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);
                }
            }

			//lo busco por el documento
            if(!empty($rendicionSocio['LiquidacionSocioRendicion']['documento'])){
                if(empty($rendicionSocio['LiquidacionSocioRendicion']['socio_id'])){

                    $sql = "";

                    $socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
                            'LiquidacionSocio.liquidacion_id' => $liquidacion_id,
                            "LPAD(LiquidacionSocio.documento,8,'0')" => $rendicionSocio['LiquidacionSocioRendicion']['documento']
                    )));
                    $rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);
                }
            }

			//BUSCO EL ID DEL SOCIO BASADO EN EL DOCUMENTO EN PERSONAS
			if(empty($rendicionSocio['LiquidacionSocioRendicion']['socio_id']) && !empty($rendicionSocio['LiquidacionSocioRendicion']['documento'])):
                App::import('Model','pfyj.Socio');
                $oSOCIO = new Socio();
                $socio = $oSOCIO->getSocioByDocumento($rendicionSocio['LiquidacionSocioRendicion']['documento']);
				$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = $socio['Socio']['id'];
			endif;

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

			//BUSCO EL ID DEL SOCIO BASADO EN EL DOCUMENTO EN PERSONAS
			App::import('Model','pfyj.Socio');
			$oSOCIO = new Socio();
			if(empty($socio_id)):
				$socio = $oSOCIO->getSocioByDocumento($rendicionSocio['LiquidacionSocioRendicion']['documento']);
				$socio_id = (!empty($socio) ? $socio['Socio']['id'] : NULL);
			endif;

			//BUSCO EL SOCIO BASADO EN EL BENEFICIO QUE VIENE EN EL ARCHIVO
			if(empty($socio_id)):
				App::import('Model','pfyj.PersonaBeneficio');
				$oBEN = new PersonaBeneficio();
				$beneficio = $oBEN->getBeneficioByNro($rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio'],$rendicionSocio['LiquidacionSocioRendicion']['tipo'],$rendicionSocio['LiquidacionSocioRendicion']['nro_ley'],$rendicionSocio['LiquidacionSocioRendicion']['sub_beneficio']);
				if(!empty($beneficio)){
					$socio = $oSOCIO->getSocioByPersonaId($beneficio['PersonaBeneficio']['persona_id']);
					if(!empty($socio)) $socio_id = $socio['Socio']['id'];
				}
			endif;

			$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = $socio_id;

			//controlar la rendicion especial para el caso de las ordenes que sean mayores al 800000
			//para los periodos de 201111, 201112, 201201
			if($rendicionSocio['LiquidacionSocioRendicion']['periodo'] >= '201112' && $rendicionSocio['LiquidacionSocioRendicion']['periodo'] <= '201202'){
				if($rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_id'] >= 800000 ) $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_id'] = $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_id'] - 800000;
			}

			//DETERMINAR LA ORDEN DE DESCUENTO PARA EL CASO DE LAS CUOTAS SOCIALES PARA
			//ARMAR LA CONCILIACION
			if(empty($rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_id']) && !empty($socio_id)):

				if($rendicionSocio['LiquidacionSocioRendicion']['sub_codigo'] == $this->CJP_SCOD_CSOC){
					$ordenDtoCuotaSocial = $oSOCIO->getOrdenDtoCuotaSocial($socio_id);
					if(!empty($ordenDtoCuotaSocial)) $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_id'] = $ordenDtoCuotaSocial['OrdenDescuento']['id'];
				}
			endif;


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

//		$this->LiquidacionSocioRendicion->id = 0;


                if(!$this->LiquidacionSocioRendicion->save($rendicionSocio)){
                    return FALSE;
                }
                if(!empty($rendicionSocio['LiquidacionSocioRendicion']['socio_id'])){
                    $this->__calificarSocio($rendicionSocio['LiquidacionSocioRendicion']['socio_id']);
                }

                return TRUE;

//		return $this->LiquidacionSocioRendicion->save($rendicionSocio);

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


	/**
	 * genera la calificacion del socio en base a la tabla liquidacion_socios para la liquidacion procesada
	 * @param $socio_id
	 */
	function __calificarSocio($socio_id){

		App::import('Model','Pfyj.SocioCalificacion');
		$oSC = new SocioCalificacion();

		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();

		//saco la calificacion del periodo
		$sql = "	select
                                        Liquidacion.id,
                                        Liquidacion.periodo,
                                        LiquidacionSocioRendicion.socio_id,
                                        LiquidacionSocioRendicion.banco_intercambio,
                                        LiquidacionSocioRendicion.status,
                                        BancoRendicionCodigo.calificacion_socio,
                                        LiquidacionSocio.persona_beneficio_id,
                                        Liquidacion.periodo
                                from liquidacion_socio_rendiciones as LiquidacionSocioRendicion
                                left join banco_rendicion_codigos as BancoRendicionCodigo on (LiquidacionSocioRendicion.banco_intercambio = BancoRendicionCodigo.banco_id and LiquidacionSocioRendicion.status = BancoRendicionCodigo.codigo)
                                left join liquidacion_socios as LiquidacionSocio on (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id)
                                inner join liquidaciones as Liquidacion on (LiquidacionSocioRendicion.liquidacion_id = Liquidacion.id)
                                where
                                        LiquidacionSocioRendicion.socio_id = $socio_id
                                        and LiquidacionSocioRendicion.liquidacion_id = $this->liquidacionID
                                        and IFNULL(LiquidacionSocioRendicion.status,'') <> ''
                                group by LiquidacionSocioRendicion.socio_id,LiquidacionSocio.persona_beneficio_id,LiquidacionSocioRendicion.banco_intercambio,LiquidacionSocioRendicion.status
                                order by LiquidacionSocioRendicion.indica_pago ASC, LiquidacionSocioRendicion.indica_pago LIMIT 1";

                $datos = $oSC->query($sql);


		if(!empty($datos)):
			$periodo = $datos[0]['Liquidacion']['periodo'];
			$persona_beneficio_id = $datos[0]['LiquidacionSocio']['persona_beneficio_id'];
			$calificacion = $datos[0]['BancoRendicionCodigo']['calificacion_socio'];
			$oSC->deleteAll("SocioCalificacion.socio_id = $socio_id and SocioCalificacion.calificacion = '$calificacion' and SocioCalificacion.periodo = '$periodo'");
			$oSC->calificar($socio_id,$calificacion,$persona_beneficio_id,$periodo,date('Y-m-d H:i:s'));
		endif;

	}
}
?>

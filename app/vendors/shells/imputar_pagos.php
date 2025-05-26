<?php

/**
 * PROCESO DE IMPUTACION DE PAGOS DE UNA LIQUIDACION
 * 
 * LANZADORES 
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php imputar_pagos 381476 -app /home/adrian/trabajo/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php imputar_pagos 113 -app /var/www/sigem/app/
 * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php genera_archivo 47710 -app /home/mutualam/public_html/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * 
 */

// 
// 
class ImputarPagosShell extends Shell {
	
	var $liquidacionID = 0;
	var $fecha_pago = null;
	var $nro_recibo = null;
	var $PROCCES_ID = 0;
	
	var $tasks = array('Temporal');
	
	function main() {
		
		$ERROR = NULL;
		
		App::import('Model','Mutual.OrdenDescuentoCobro');
		App::import('Model','Mutual.Liquidacion');
		
		
		$oCOBRO = new OrdenDescuentoCobro();
		$oLIQUI = new Liquidacion();				
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$this->PROCCES_ID = $pid;
		
		$this->Temporal->pid = $pid;
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$this->liquidacionID		= $asinc->getParametro('p1');
		$this->fecha_pago		= $asinc->getParametro('p2');
		$this->nro_recibo		= $asinc->getParametro('p3');
		
		$DESIMPUTAR			= ($asinc->getParametro('p4') == 1 ? TRUE : FALSE);

		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");
		

		
		if($oLIQUI->isBloqueada($this->liquidacionID)):
			$idBloquedo = $oLIQUI->getBloqueoPID($this->liquidacionID);
			$msg = "PROCESO BLOQUEADO POR OTRO USUARIO [PID #$idBloquedo]....";
			$asinc->actualizar(2,100,$msg);
			$msg2 = $asinc->getCadenaInfo($idBloquedo);
			$this->Temporal->setErrorMsg("BOQUEO PID #$idBloquedo",$msg2);
			return;
		endif;
		
		$oLIQUI->cerrar($this->liquidacionID);
		//verifico que no haya otros procesos bloqueados
		$oLIQUI->bloquear($this->liquidacionID,$pid);
		
		//cargar la tabla liquidacion_cuotas para la liquidacion ID
		
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();	

		
		if($DESIMPUTAR):
			$asinc->actualizar(2,100,"ESPERE, CARGANDO SOCIOS CON IMPUTACION PREVIA...");
			$socios = $oLSR->getSociosDebitadosCobrados($this->liquidacionID);
			
			$total = 0;
			$i = 0;		
			
			$total = count($socios);
			$asinc->setTotal($total);	
			
			//DESIMPUTAR COBROS EMITIDOS
			if(!empty($socios)):
				foreach($socios as $socio_id){
					$oCOBRO->desimputarLiquidacion($this->liquidacionID, $socio_id);
					$porc = round($i/$total * 100,0);
					$msg ="$i / $total - DESIMPUTADO >> SOCIO #$socio_id";
					$asinc->actualizar($i,$total,$msg);
					$i++;
				}
			endif;
		
		endif;
		
//		$socios = $this->__getSociosPreimputados();
		$asinc->actualizar(3,100,"ESPERE, CARGANDO SOCIOS PARA IMPUTAR...");
		$socios = $oLSR->getSociosDebitadosNoCobrados($this->liquidacionID);
                
//                
//                debug($socios);
//                exit;
                
                $asinc->actualizar(7,100,"COMENZANDO ...");
                
		$total = 0;
		$i = 1;		
		
		$total = count($socios);
		$asinc->setTotal($total);		
		
		//imputar los pagos
		if(!empty($socios)):
		
			$ERROR = NULL;
			
			foreach($socios as $socio_id):
	
				$msg = "$i / $total - IMPUTANDO PAGOS >> SOCIO #$socio_id";
//				$this->out($socio_id);
				$resultado = $oCOBRO->imputarLiquidacion($this->liquidacionID, $socio_id, $this->fecha_pago, $this->nro_recibo,FALSE,TRUE);
//				debug($resultado);
                                if(empty($resultado)){
                                    $resultado['ERROR'] = 1;
                                    $resultado['MENSAJE'] = "ERROR EN PROCEDIMIENTO imputarLiquidacion *** RESULTADO NULO - SOCIO # $socio_id***";
                                }

                                if($resultado['ERROR'] == 1){
                                        $ERROR = $resultado['MENSAJE'];
                                        $this->Temporal->setErrorMsg($msg,$ERROR);
                                }else{
                                    $msg .= "| COBRO EMITIDO #" . $resultado['COBRO_ID'];
                                    $asinc->actualizar($i,$total,$msg);
                                }
                                
                                if(empty($ERROR)) $this->__calificarSocio($socio_id);
				
                                $i++;
				
			endforeach;

			if(empty($ERROR)){
//                                $this->__calificarSocio($socio_id);
				$oLIQUI->setTotales($this->liquidacionID);			
				$oLIQUI->setImputada($this->liquidacionID,$this->fecha_pago,$this->nro_recibo);
				$oLIQUI->desbloquear($this->liquidacionID);
				$asinc->actualizar(99,100,"FINALIZANDO...");
				$asinc->fin("**** PROCESO FINALIZADO ****");		
			}else{
				$asinc->actualizar(99,100,"ULTIMO ERROR: $ERROR");
			}
			
		else:

//			$asinc->actualizar(10,100,"NO EXISTEN DATOS PARA PROCESAR");
                    
                        $oLIQUI->setTotales($this->liquidacionID);			
                        $oLIQUI->setImputada($this->liquidacionID,$this->fecha_pago,$this->nro_recibo);
                        $oLIQUI->desbloquear($this->liquidacionID);
                        $asinc->actualizar(99,100,"FINALIZANDO...");
                        $asinc->fin("**** PROCESO FINALIZADO ****");                    
                    

		endif;

                ##############################################################################################
                # CALIFICAR TODOS LOS SOCIOS
                # 1) MARCAR COMO NORMAL A LOS QUE VINO UN PAGO Y NO TIENEN REGISTROS ADEUDADOS
                # 2) MARCAR EL RESTO DE LAS CALIFICACIONES QUE NO SON STOP
                # 3) MARCAR LOS STOP
                ##############################################################################################
		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	/**
	 * metodo general de imputacion
	 * 
	 * @param $socio_id
	 */
	function __imputar($socio_id){
		

		$imputacion = true;
		
		
		#######################################################################################
		# CARGO LA REFERENCIA A OBJETOS
		#######################################################################################
		
		App::import('Model','Mutual.OrdenDescuento');
		$oDTO = new OrdenDescuento();		
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
		
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOB = new OrdenDescuentoCobro();	
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCOBROCUOTA = new OrdenDescuentoCobroCuota();		
		

		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();	

		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();		
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();	
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();

		App::import('Model','Pfyj.SocioReintegro');
		$oREINTEGRO = new SocioReintegro();	

		App::import('Model','Proveedores.ProveedorComision');
		$oCOMISION = new ProveedorComision();	

		App::import('Model','pfyj.Socio');
		$oSOCIO = new Socio();		

		#######################################################################################
		# BORRO LA IMPUTACION PREVIA
		#######################################################################################
		$cobroAnteriorID = $oLSR->getOrdenCobroID($socio_id,$this->liquidacionID);
		if($cobroAnteriorID != 0) $oCOB->borrarDetalle($cobroAnteriorID,false);

		#######################################################################################
		# BORRO EL GASTO ADMINISTRATIVO
		#######################################################################################
		App::import('Model','Mutual.MutualAdicionalPendiente');
		$oAP = new MutualAdicionalPendiente();		
		$oAP->borrarCuotasDevengadasBySocioByLiquidacionId($socio_id,$this->liquidacionID);
		
		
		#######################################################################################
		# SETEO EL PERIODO DE COBRO
		#######################################################################################
		$liquidacion = $oLQ->read(null,$this->liquidacionID);
//		$periodoCobro = date('Ym',strtotime($liquidacionSocio['LiquidacionSocio']['fecha_pago']));
		//el periodo de cobro le pongo el periodo de la liquidacion
		$periodoCobro = $liquidacion['Liquidacion']['periodo'];
		
		#######################################################################################
		# ACTUALIZO LA LIQUIDACION CUOTAS
		#######################################################################################		
// 		$oLS->liquidar($socio_id,$periodoCobro,$liquidacion['Liquidacion']['codigo_organismo'],$liquidacion['Liquidacion']['id'],false);
		
		
		#######################################################################################
		# RELIQUIDO AL SOCIO [GENERA LA LIQUIDACION CUOTAS NUEVAMENTE Y PROCESA EL LOS DEBITOS COBRADOS]
		#######################################################################################
		$ret = $oLS->reliquidar($socio_id,$liquidacion['Liquidacion']['periodo'],true,false,$liquidacion['Liquidacion']['codigo_organismo']);
		
		//reprocesar la imputacion
//		$cuotasActualizadas = $oLC->armaImputacion($this->liquidacionID,$socio_id);

		//DE LA LIQUIDACION SOCIO RENDICION SACO LOS DISTINTOS PROVEEDORES POR
		//SI ME CARGARON UN ARCHIVO PARA IMPUTAR A UN PROVEEDOR ESPECIFICO
// 		$proveedores = $oLSR->find("all",array('conditions' => array("LiquidacionSocioRendicion.liquidacion_id" => $liquidacion['Liquidacion']['id'], "LiquidacionSocioRendicion.socio_id" => $socio_id), "fields" => array("LiquidacionSocioRendicion.proveedor_id"), "group" => array("LiquidacionSocioRendicion.proveedor_id")));
// 		if(!empty($proveedores)):
// 			//imputo en base a si un archivo fue vinculado a un proveedor especifico
// 			foreach($proveedores as $proveedor):
// 				if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) != 77) $cuotasActualizadas = $oLC->armaImputacion($liquidacion['Liquidacion']['id'],$socio_id,$proveedor['LiquidacionSocioRendicion']['proveedor_id']);
// 				else $cuotasActualizadas = $oLC->armaImputacionCJP($liquidacion['Liquidacion']['id'],$socio_id);
// 				if(!empty($cuotasActualizadas)) $oLC->saveAll($cuotasActualizadas);
// 			endforeach;
// 		else:
// 			//esquema de imputacion normal
// 			if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) != 77) $cuotasActualizadas = $oLC->armaImputacion($liquidacion['Liquidacion']['id'],$socio_id);
// 			else $cuotasActualizadas = $oLC->armaImputacionCJP($liquidacion['Liquidacion']['id'],$socio_id);
// 			if(!empty($cuotasActualizadas)) $oLC->saveAll($cuotasActualizadas);
// 		endif;

// 		#PROCESA LA PRE-IMPUTACION
// 		$condProv = array();
// 		$condProv['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion['Liquidacion']['id'];
// 		$condProv['LiquidacionSocioRendicion.socio_id'] = $socio_id;
// 		$condProv['LiquidacionSocioRendicion.indica_pago'] = 1;
// 		$fieldsProv = array("LiquidacionSocioRendicion.proveedor_id, sum(LiquidacionSocioRendicion.importe_debitado) as importe_debitado");
// 		$groupProv = array("LiquidacionSocioRendicion.proveedor_id");
// 		$ordProv = array("LiquidacionSocioRendicion.proveedor_id ASC");

// 		$proveedores = $oLSR->find("all",array('conditions' => $condProv, "fields" => $fieldsProv, "group" => $groupProv, "order" => $ordProv));
		
// 		if(!empty($proveedores)):
			
// 			foreach ($proveedores as $proveedor):
			
// 				$importeDebitado = (isset($proveedor[0]['importe_debitado']) ? $proveedor[0]['importe_debitado'] : 0);
// 				$proveedor_id = $proveedor['LiquidacionSocioRendicion']['proveedor_id'];
					
// 				if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) != 77) $cuotasActualizadas = $oLC->armaImputacion($liquidacion['Liquidacion']['id'],$socio_id,$proveedor_id);
// 				else $cuotasActualizadas = $oLC->armaImputacionCJP($liquidacion['Liquidacion']['id'],$socio_id);
// 				if(!empty($cuotasActualizadas)):
// 					if(!$oLC->saveAll($cuotasActualizadas)){
// 						$STOP = 1;
// 						break;
// 					}
// 				endif;
				
// 			endforeach; //END foreach ($proveedores as $proveedor):
			
// 		else:
		
// 			//esquema de imputacion normal
// 			if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) != 77) $cuotasActualizadas = $oLC->armaImputacion($liquidacion['Liquidacion']['id'],$socio_id);
// 			else $cuotasActualizadas = $oLC->armaImputacionCJP($liquidacion['Liquidacion']['id'],$socio_id);
// 			if(!empty($cuotasActualizadas)) $oLC->saveAll($cuotasActualizadas);
			
// 		endif; //END if(!empty($proveedores)):		
		
		
		
		
		#######################################################################################
		# CARGO LAS CUOTAS PAGADAS
		#######################################################################################		
		$cuotas = $this->__getCuotasPagadasBySocio($socio_id);
		
		
		$ACUM_IMPUTADO 		= 0;
		$saldoActual 		= 0;
		$importeDebitado 	= 0;
		
		$cuotaPagada 	= array();
		$cuotasPagadas 	= array();
		################################################
		# SI NO TIENE CUOTAS A IMPUTAR NO SIGO PROCESANDO
		# SI DEBITO UN IMPORTE LE GENERO UN REINTEGRO
		################################################
		if(empty($cuotas)):
			$impoDebitado = $oLSR->getTotalBySocioByLiquidacion($socio_id,$this->liquidacionID,1);
			
			if($impoDebitado != 0):
				
				$oREINTEGRO->deleteAll("SocioReintegro.liquidacion_id = " . $this->liquidacionID ." AND SocioReintegro.socio_id = ".$socio_id . " AND SocioReintegro.anticipado = 0");
			
				$reintegro = array('SocioReintegro' => array(
							'id' => 0,
							'socio_id' => $socio_id,
							'liquidacion_id' => $this->liquidacionID,
							'periodo' => $this->__getCampo('periodo'),
							'importe_dto' => 0,
							'importe_debitado' => $impoDebitado,
							'importe_imputado' => 0,
							'importe_reintegro' => $impoDebitado
				));
				$oREINTEGRO->save($reintegro);	
			endif;		
			return true;
		endif;
		
		#######################################################################################
		# INICIO UNA TRANSACCION
		#######################################################################################		
		$oCOB->begin();
		
		
		#######################################################################################
		# PROCESO LOS ADICIONALES Y ARMO EL DETALLE DEL COBRO
		#######################################################################################	
		foreach($cuotas as $idx => $cuota){
			
			//genero el adicional
			if($cuota['LiquidacionCuota']['mutual_adicional_pendiente_id'] != 0 && $cuota['LiquidacionCuota']['orden_descuento_cuota_id'] == 0){
				$cuota['LiquidacionCuota']['orden_descuento_cuota_id'] = $this->__devengarAdicionalPendiente($cuota,$this->fecha_pago);
			}
			$importeDebitado = $cuota['LiquidacionCuota']['importe_debitado'];
			$saldoActual = $oCUOTA->getSaldo($cuota['LiquidacionCuota']['orden_descuento_cuota_id']);
			
			$cuota['LiquidacionCuota']['saldo_actual'] = $saldoActual;
//			$saldoActual = $cuota['LiquidacionCuota']['saldo_actual'];
			
			if($importeDebitado > $saldoActual) $cuota['LiquidacionCuota']['importe_debitado'] = $saldoActual;
			
			$ACUM_IMPUTADO += $cuota['LiquidacionCuota']['importe_debitado'];
			
			$cuotaPagada['periodo_cobro'] = $periodoCobro;
			$cuotaPagada['orden_descuento_cuota_id'] = $cuota['LiquidacionCuota']['orden_descuento_cuota_id'];
			$cuotaPagada['proveedor_id'] = $cuota['LiquidacionCuota']['proveedor_id'];
			$cuotaPagada['importe'] = $cuota['LiquidacionCuota']['importe_debitado'];
			
			#CALCULO LA COMISION POR LA COBRANZA
			$comision = $oCOBROCUOTA->calcularComisionCobranza($cuotaPagada['orden_descuento_cuota_id'],$cuotaPagada['importe']);
			$cuotaPagada['alicuota_comision_cobranza'] = $comision['alicuota'];
			$cuotaPagada['comision_cobranza'] = $comision['comision'];			
			
			$cuotaPagada['pago_total_cuota'] = ($cuota['LiquidacionCuota']['importe_debitado'] == $saldoActual ? 1 : 0);
			
			//controlo que la cuota ya no este pagada totalmente para no generar un nuevo pago
			if($saldoActual != 0)array_push($cuotasPagadas,$cuotaPagada);
			
			$cuotas[$idx] = $cuota;
			
		}
		#######################################################################################
		#ARMO EL COBRO
		#######################################################################################
		$pago = array('OrdenDescuentoCobro' => array(
			'tipo_cobro' => 'MUTUTCOBRECS',
			'socio_id' => $socio_id,	
			'fecha' => $this->fecha_pago,
			'nro_recibo' => $this->nro_recibo,
			'importe' => $ACUM_IMPUTADO,
			'periodo_cobro' => $periodoCobro
		));

		$pago['OrdenDescuentoCobroCuota'] = $cuotasPagadas;
		
	
		#######################################################################################
		# GUARDO EL COBRO CON LA TRANSACCION DISABLE PARA QUE TOME LA TRANSACCION DE ESTE PROCESO
		#######################################################################################
		if(!$oCOB->saveAll($pago,array('atomic'=>false))) {
			$oCOB->rollback();
			return false;
		}
		
//		debug($pago);
	
		#######################################################################################
		# MARCO LAS CUOTAS QUE ESTAN PAGADAS TOTALMENTE
		#######################################################################################
		$pagadasTotalmente = Set::extract('/OrdenDescuentoCobroCuota[pago_total_cuota=1]',$pago);
		foreach($pagadasTotalmente as $pagoCuota){
			if(!$oCUOTA->setPagoTotal($pagoCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'])){
				$imputacion = false;
				break;
			}
		}

		if(!$imputacion){
			$oCOB->rollback();
			return false;
		}
		$idCobro = $oCOB->getLastInsertID();
		

		#######################################################################################
		# ACTUALIZO LA LIQUIDACION CUOTAS CON EL ID DEL COBRO
		#######################################################################################	
		$codigoOrganismo = $liquidacion['Liquidacion']['codigo_organismo'];
		
		foreach($cuotas as $cuota):
			$cuota['LiquidacionCuota']['orden_descuento_cobro_id'] = $idCobro;
			$cuota['LiquidacionCuota']['imputada'] = 1;
			#####################################################################################
			#CALCULO LA COMISION POR LA COBRANZA
			#####################################################################################
			$comision = $oCOBROCUOTA->calcularComisionCobranza($cuota['LiquidacionCuota']['orden_descuento_cuota_id'],$cuota['LiquidacionCuota']['importe_debitado']);
			$cuota['LiquidacionCuota']['alicuota_comision_cobranza'] = $comision['alicuota'];
			$cuota['LiquidacionCuota']['comision_cobranza'] = $comision['comision'];
//			$proveedor_id = $cuota['LiquidacionCuota']['proveedor_id'];
//			$tipo_producto = $cuota['LiquidacionCuota']['tipo_producto'];
//			$tipo_cuota = $cuota['LiquidacionCuota']['tipo_cuota'];
//			$comision = $oCOMISION->getComision($codigoOrganismo,$proveedor_id,$tipo_producto,$tipo_cuota);
//			$cuota['LiquidacionCuota']['alicuota_comision_cobranza'] = $comision;
//			$cuota['LiquidacionCuota']['comision_cobranza'] = round(round($cuota['LiquidacionCuota']['importe_debitado'],2) * ($comision / 100),2);
			#####################################################################################
			if(!$oLC->save($cuota)){
				$imputacion = false;
				break;
			}
		endforeach;
		
		if(!$imputacion){
			$oCOB->rollback();
			return false;
		}		

		#######################################################################################
		# PROCESO REINTEGROS
		#######################################################################################	

		$oREINTEGRO->deleteAll("SocioReintegro.liquidacion_id = " . $this->liquidacionID ." AND SocioReintegro.socio_id = ".$socio_id . " AND SocioReintegro.anticipado = 0");
		
		$impoImputado = $oLC->getTotalImputadoBySocioByLiquidacion($this->liquidacionID,$socio_id);
		$impoDebitado = $oLSR->getTotalBySocioByLiquidacion($socio_id,$this->liquidacionID,1);
		
		$impoLiquidado = $oLS->getTotalImporteLiquidadoBySocio($this->liquidacionID,$socio_id);
		
		//VERIFICO QUE NO TENGA REINTEGROS ANTICIPADOS
		$anticipado = $oREINTEGRO->getTotalReintegrosAnticipados($socio_id,$this->liquidacionID);
		$impoReintegro = abs($impoDebitado - $impoImputado);
		
		
		$reintegro = array();
		
		if($anticipado != 0):
		
			if($anticipado > $impoReintegro && $oSOCIO->isActivo($socio_id)):
				
				//cargar una ND por la diferencia para recuperar el anticipo
							
				$proveedor_id = $oDTO->GlobalDato('entero_1','MUTUTCUONDRR');
				$orden = $oDTO->getOrdenByNumero($socio_id,"CMUTU","MUTUPROD0003",false,false,false);

				$cuotaRecuReintegro = array('OrdenDescuentoCuota' => array(
											'id' => 0,
											'orden_descuento_id' => $orden['OrdenDescuento']['id'],
											'persona_beneficio_id' => $orden['OrdenDescuento']['persona_beneficio_id'],
											'socio_id' => $socio_id,
											'tipo_orden_dto' => $oDTO->GlobalDato('concepto_3','MUTUPROD0003'),
											'tipo_producto' => 'MUTUPROD0003',
											'periodo' => $periodoCobro,
											'nro_cuota' => 0,
											'tipo_cuota' => 'MUTUTCUONDRR',
											'estado' => 'A',
											'situacion' => 'MUTUSICUMUTU',
											'importe' => $anticipado - $impoReintegro,
											'proveedor_id' => $proveedor_id,
											'vencimiento' => date('Y-m-d'),
											'vencimiento_proveedor' => date('Y-m-d')
							));	
				
				$oCUOTA->save($cuotaRecuReintegro);
				
				$idCuotaRecupero = $oCUOTA->getLastInsertID();
				
				$reintegros = $oREINTEGRO->getReintegrosAnticipados($socio_id,$this->liquidacionID);
				
				foreach($reintegros as $reintegro):
					$reintegro['SocioReintegro']['recupero_cuota_id'] = $idCuotaRecupero;
					$reintegro['SocioReintegro']['importe_debitado'] = $impoDebitado;
					$reintegro['SocioReintegro']['importe_imputado'] = $impoImputado;
					$reintegro['SocioReintegro']['importe_reintegro'] = $impoReintegro;
					$oREINTEGRO->save($reintegro); 	
				endforeach;
			
			elseif($impoReintegro!=0):
			
				//marcar los anticipados
				$reintegros = $oREINTEGRO->getReintegrosAnticipados($socio_id,$this->liquidacionID);
				foreach($reintegros as $reintegro):
//					$reintegro['SocioReintegro']['importe_debitado'] = $reintegro['SocioReintegro']['importe_aplicado'];
//					$reintegro['SocioReintegro']['importe_imputado'] = $reintegro['SocioReintegro']['importe_aplicado'];
//					$reintegro['SocioReintegro']['importe_reintegro'] = $reintegro['SocioReintegro']['importe_aplicado'];
					$reintegro['SocioReintegro']['importe_debitado'] = $reintegro['SocioReintegro']['pagos'];
//					$reintegro['SocioReintegro']['importe_imputado'] = $reintegro['SocioReintegro']['pagos'];
					$reintegro['SocioReintegro']['importe_reintegro'] = $reintegro['SocioReintegro']['pagos'];					
					$oREINTEGRO->save($reintegro); 	
				endforeach;				

				//cargar un reintegro por la diferencia
				$reintegro = array('SocioReintegro' => array(
							'id' => 0,
							'socio_id' => $socio_id,
							'liquidacion_id' => $this->liquidacionID,
							'periodo' => $this->__getCampo('periodo'),
							'importe_dto' => $impoLiquidado,
							'importe_debitado' => $impoDebitado - $anticipado,
							'importe_imputado' => $impoImputado - $anticipado,
							'importe_reintegro' => $impoReintegro - $anticipado
				));	
				$oREINTEGRO->id = 0;
				if(!$oREINTEGRO->save($reintegro)):
					$oCOB->rollback();
					return false;		
				endif;								
				
			endif;
		
		elseif($impoImputado < $impoDebitado):	
		
			$reintegro = array('SocioReintegro' => array(
						'id' => 0,
						'socio_id' => $socio_id,
						'liquidacion_id' => $this->liquidacionID,
						'periodo' => $this->__getCampo('periodo'),
						'importe_dto' => $impoLiquidado,
						'importe_debitado' => $impoDebitado,
						'importe_imputado' => $impoImputado,
						'importe_reintegro' => $impoReintegro
			));
			$oREINTEGRO->id = 0;
			if(!$oREINTEGRO->save($reintegro)):
				$oCOB->rollback();
				return false;		
			endif;			
			
		endif;
		

		
		#######################################################################################
		# GRABAR EN LA RENDICION SOCIOS EL ID DEL COBRO
		#######################################################################################
		
		$imputacion = $oLSR->updateAll(
										array(
												'LiquidacionSocioRendicion.orden_descuento_cobro_id' => $idCobro,
											),
										array(
												'LiquidacionSocioRendicion.liquidacion_id' => $this->liquidacionID, 
												'LiquidacionSocioRendicion.socio_id' => $socio_id,
												'LiquidacionSocioRendicion.indica_pago' => 1
										)
		);	

		
		#CONTROL GENERAL (CRUCE ENTRE IMPUTADO SEGUN LIQUIDACION CUOTAS Y COBROS)
		$TOTAL_IMPUTADO_LC = $oLC->getTotalImputadoBySocioByLiquidacion($this->liquidacionID, $socio_id);
		$TOTAL_COBRO = $oCOBROCUOTA->getMontoPagoByOrdenCobro($idCobro);
		
		if($TOTAL_IMPUTADO_LC != $TOTAL_COBRO){
			$imputacion = false;
			App::import('Model','Shells.AsincronoError');
			$oERROR = new AsincronoError();
			$error = array();
			$error['AsincronoError'] = array();
			$error['AsincronoError']['id'] = 0;
			$error['AsincronoError']['asincrono_id'] = $this->PROCCES_ID;
			$error['AsincronoError']['mensaje_1'] = "SOCIO #$socio_id";
			$error['AsincronoError']['mensaje_2'] = "INCONSISTENCIA ENTRE IMPUTADO SEGUN LIQUIDACION [$TOTAL_IMPUTADO_LC] Y COBRO GENERADO [#$idCobro][TOTAL: $TOTAL_COBRO]";
			$oERROR->save($error);
		}
	
		if(!$imputacion){
			$oCOB->rollback();
			return false;
		}else{
			$oCOB->commit();
			return true;
		}


	}
	
	/**
	 * Devenga el adicional pendiente
	 * @param $cuota
	 * @param $fechaPago
	 */
	function __devengarAdicionalPendiente($cuota,$fechaPago){
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		
		$cuotaAdicional = array('OrdenDescuentoCuota' => array(
						'id' => $cuota['LiquidacionCuota']['orden_descuento_cuota_id'],
						'orden_descuento_id' => $cuota['LiquidacionCuota']['orden_descuento_id'],
						'persona_beneficio_id' => $cuota['LiquidacionCuota']['persona_beneficio_id'],
						'socio_id' => $cuota['LiquidacionCuota']['socio_id'],
						'tipo_orden_dto' => $cuota['LiquidacionCuota']['tipo_orden_dto'],
						'tipo_producto' => $cuota['LiquidacionCuota']['tipo_producto'],
						'periodo' => $cuota['LiquidacionCuota']['periodo_cuota'],
						'nro_cuota' => 0,
						'tipo_cuota' => $cuota['LiquidacionCuota']['tipo_cuota'],
						'estado' => 'P',
						'situacion' => 'MUTUSICUMUTU',
						'importe' => $cuota['LiquidacionCuota']['importe_debitado'],
						'proveedor_id' => $cuota['LiquidacionCuota']['proveedor_id'],
						'vencimiento' => $fechaPago,
						'vencimiento_proveedor' => $fechaPago
					));
					
		App::import('Model', 'Mutual.OrdenDescuentoCuota');
		$oDeuda = new OrdenDescuentoCuota();						
		$oDeuda->save($cuotaAdicional);
		
		$idCuota = $oDeuda->getLastInsertID();
		
		App::import('Model', 'Mutual.MutualAdicionalPendiente');
		$oADIC = new MutualAdicionalPendiente();

		$adicional = $oADIC->read(null,$cuota['LiquidacionCuota']['mutual_adicional_pendiente_id']);
		
		$adicional['MutualAdicionalPendiente']['procesado'] = 1;
		$adicional['MutualAdicionalPendiente']['orden_descuento_id'] = $cuota['LiquidacionCuota']['orden_descuento_id'];
		$adicional['MutualAdicionalPendiente']['orden_descuento_cuota_id'] = $idCuota;
		
		$oADIC->save($adicional);
		
		return $idCuota;
		
	}
	
	/**
	 * 
	 * @param $socio_id
	 */
	function __getCuotasPagadasBySocio($socio_id){
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		$cuotas = $oLC->getCuotasPagadasByLiquidacion($this->liquidacionID,$socio_id);
		return $cuotas;	
	}
	
	

	/**
	 * 
	 * @return unknown_type
	 */
	function __getSociosLiquidados(){
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		$condiciones = array(
								'LiquidacionSocio.liquidacion_id' => $this->liquidacionID,
//								'LiquidacionSocio.socio_id' => 10966
		);
		$socios = $oLS->find('all',array(
									'conditions' => $condiciones,
									'fields' => array('LiquidacionSocio.socio_id'),
									'group' => array('LiquidacionSocio.socio_id'),
		));	
		$socios = Set::extract("/LiquidacionSocio/socio_id",$socios);
		return $socios;			
	}
	
	
	function __getSociosPreimputados(){
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		$condiciones = array(
										'LiquidacionCuota.liquidacion_id' => $this->liquidacionID,
										'LiquidacionCuota.para_imputar' => 1
		);
		$socios = $oLC->find('all',array(
											'conditions' => $condiciones,
											'fields' => array('LiquidacionCuota.socio_id'),
											'group' => array('LiquidacionCuota.socio_id'),
		));
		$socios = Set::extract("/LiquidacionCuota/socio_id",$socios);
		return $socios;
		
	}
	

	/**
	 * genera la calificacion del socio en base a la tabla liquidacion_socios para la liquidacion procesada
	 * @param $socio_id
	 */
	function __calificarSocio($socio_id){
		
		App::import('Model','Pfyj.SocioCalificacion');
		$oSC = new SocioCalificacion();
				
//		$sql = "	select 
//						LiquidacionSocioRendicion.socio_id,
//						LiquidacionSocioRendicion.banco_intercambio,
//						LiquidacionSocioRendicion.status,
//						BancoRendicionCodigo.calificacion_socio,
//						LiquidacionSocio.persona_beneficio_id,
//						Liquidacion.periodo
//					from liquidacion_socio_rendiciones as LiquidacionSocioRendicion
//					left join banco_rendicion_codigos as BancoRendicionCodigo on (LiquidacionSocioRendicion.banco_intercambio = BancoRendicionCodigo.banco_id and LiquidacionSocioRendicion.status = BancoRendicionCodigo.codigo)
//					inner join liquidacion_socios as LiquidacionSocio on (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id and LiquidacionSocio.socio_id and LiquidacionSocioRendicion.socio_id)
//					inner join liquidaciones as Liquidacion on (LiquidacionSocio.liquidacion_id = Liquidacion.id)
//					where 
//						LiquidacionSocioRendicion.liquidacion_id = $this->liquidacionID
//						and LiquidacionSocioRendicion.socio_id = $socio_id
//					group by LiquidacionSocioRendicion.socio_id,LiquidacionSocioRendicion.banco_intercambio,LiquidacionSocioRendicion.status
//					order by LiquidacionSocioRendicion.socio_id, LiquidacionSocioRendicion.indica_pago LIMIT 1";


		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();	

		$liquidacion = $oLQ->read(null,$this->liquidacionID);
		
		//saco la calificacion del periodo
		$sql = "	select 
						Liquidacion.id,
						LiquidacionSocioRendicion.socio_id,
						LiquidacionSocioRendicion.banco_intercambio,
						LiquidacionSocioRendicion.status,
						BancoRendicionCodigo.calificacion_socio,
						LiquidacionSocio.persona_beneficio_id,
						Liquidacion.periodo
					from liquidacion_socio_rendiciones as LiquidacionSocioRendicion
					left join banco_rendicion_codigos as BancoRendicionCodigo on (LiquidacionSocioRendicion.banco_intercambio = BancoRendicionCodigo.banco_id and LiquidacionSocioRendicion.status = BancoRendicionCodigo.codigo)
					LEFT join liquidacion_socios as LiquidacionSocio on (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id)
					inner join liquidaciones as Liquidacion on (LiquidacionSocio.liquidacion_id = Liquidacion.id)
					where 
						LiquidacionSocioRendicion.socio_id = $socio_id
						and Liquidacion.periodo = '".$liquidacion['Liquidacion']['periodo']."'
						and IFNULL(LiquidacionSocioRendicion.status,'') <> ''
					group by Liquidacion.id,LiquidacionSocioRendicion.socio_id,LiquidacionSocio.persona_beneficio_id,LiquidacionSocioRendicion.banco_intercambio,LiquidacionSocioRendicion.status
					order by LiquidacionSocioRendicion.indica_pago ASC, LiquidacionSocioRendicion.indica_pago LIMIT 1";
		$datos = $oSC->query($sql);
		
		if(!empty($datos)):
			$periodo = $datos[0]['Liquidacion']['periodo'];
			$persona_beneficio_id = $datos[0]['LiquidacionSocio']['persona_beneficio_id'];	
			$calificacion = $datos[0]['BancoRendicionCodigo']['calificacion_socio'];	
			$oSC->deleteAll("SocioCalificacion.socio_id = $socio_id and SocioCalificacion.calificacion = '$calificacion' and SocioCalificacion.periodo = '$periodo'");
			$oSC->calificar($socio_id,$calificacion,$persona_beneficio_id,$periodo,$this->fecha_pago);
		
		endif;
		
	}
	
	/**
	 * setea la cabecera de la liquidacion con los datos de los archivos recibidos
	 * importe cobrado y no cobrado
	 */
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
		$liquidacion['Liquidacion']['importe_recibido'] = $oLSR->getTotalByLiquidacion($this->liquidacionID,1,null);
		$liquidacion['Liquidacion']['fecha_imputacion'] = $this->fecha_pago;
		$liquidacion['Liquidacion']['nro_recibo'] = $this->nro_recibo;
		
		return $oLQ->save($liquidacion);

	}
	
	
	/**
	 * devuelve el calculo de la liquidacion del proveedor en base a lo cobrado y marcado como imputado
	 * @return $proveedores
	 */
	function __getLiquidacionProveedores(){
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		$proveedores = $oLC->getCuotasImputadasByLiquidacionByProveedor($this->liquidacionID);
		return $proveedores;		
	}
	
	/**
	 * graba la liquidacion del proveedor
	 * @param $liquidacionProveedor
	 */
	function __generarLiquidacionProveedor($liquidacionProveedor){
		
		App::import('Model','Proveedores.ProveedorLiquidacion');
		$oPL = new ProveedorLiquidacion();		
		$oPL->id = 0;
		
		//borro la que existe
		$oPL->deleteAll("ProveedorLiquidacion.liquidacion_id = ".$this->liquidacionID." and ProveedorLiquidacion.proveedor_id = " . $liquidacionProveedor['LiquidacionCuota']['proveedor_id']);
		
		$liquidacion = array('ProveedorLiquidacion' => array(
			'proveedor_id' => $liquidacionProveedor['LiquidacionCuota']['proveedor_id'],
			'periodo' => $this->__getCampo('periodo'),
			'codigo_organismo' => $this->__getCampo('codigo_organismo'),
			'liquidacion_id' => $this->liquidacionID,
			'tipo_cuota' => $liquidacionProveedor['LiquidacionCuota']['tipo_cuota'],
			'importe_liquidado' => $liquidacionProveedor['LiquidacionCuota']['saldo_actual'],
			'importe_debitado' => $liquidacionProveedor['LiquidacionCuota']['importe_debitado']
		
		));
		
		$oPL->save($liquidacion);
		
		$proveedorLiquidacionID = $oPL->getLastInsertID();
		
		//MARCO LA CUOTA DE LA LIQUIDACION CON EL ID DE LA LIQUIDACION DEL PROVEEDOR
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();

		$oLC->updateAll(
				array('LiquidacionCuota.proveedor_liquidacion_id' => $proveedorLiquidacionID),
				array(
						'LiquidacionCuota.liquidacion_id' => $this->liquidacionID, 
						'LiquidacionCuota.proveedor_id' => $liquidacionProveedor['LiquidacionCuota']['proveedor_id'],
						'LiquidacionCuota.imputada' => 1
				)
		);
		
		//marco la liquidacion del proveedor en el pago de la cuota
		$cuotasLiquidadas = $oLC->find('all',array('conditions' => array(
						'LiquidacionCuota.liquidacion_id' => $this->liquidacionID, 
						'LiquidacionCuota.proveedor_id' => $liquidacionProveedor['LiquidacionCuota']['proveedor_id'],
						'LiquidacionCuota.imputada' => 1,
						'LiquidacionCuota.proveedor_liquidacion_id' => $proveedorLiquidacionID
		)));
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCOBROCUOTA = new OrdenDescuentoCobroCuota();		
		
		foreach ($cuotasLiquidadas as $cuotasLiquidada){
			$oCOBROCUOTA->id = $cuotasLiquidada['LiquidacionCuota']['orden_descuento_cobro_cuota_id'];
			$cobroCuota = $oCOBROCUOTA->read(null,$cuotasLiquidada['LiquidacionCuota']['orden_descuento_cobro_cuota_id']);
			$cobroCuota['proveedor_liquidacion_id'] = $cuotasLiquidada['LiquidacionCuota']['proveedor_liquidacion_id'];
			$oCOBROCUOTA->save($cobroCuota);
//			$oCOBROCUOTA->saveField('proveedor_liquidacion_id',$cuotasLiquidada['LiquidacionCuota']['proveedor_liquidacion_id']);
		}
		
		return true;
	}	
	
	
	####################################################################################################
	# METODOS GENERALES
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
}
?>
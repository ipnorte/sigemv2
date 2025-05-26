<?php

/**
 * PROCESO DE IMPUTACION DE PAGOS DE UNA LIQUIDACION
 * 
 * LANZADORES 
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php imputar_pagos_fraccion 381486 -app /home/adrian/trabajo/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php imputar_pagos_fraccion 113 -app /var/www/sigem/app/
 * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php imputar_pagos_fraccion 47710 -app /home/mutualam/public_html/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * 
 */

// 
// 

App::import('Vendor','exec',array('file' => 'exec.php'));

class ImputarPagosFraccionShell extends Shell {
	
	var $liquidacionID = 0;
	var $fecha_pago = null;
	var $nro_recibo = null;
	var $PROCCES_ID = 0;
	
	var $tasks = array('Temporal','Error');
	
	function main() {
		
            
                Configure::write('debug', 1);

                $STOP = 0;

                if (empty($this->args[0])) {
                    $this->out("ERROR: PID NO ESPECIFICADO");
                    return;
                }

                $pid = $this->args[0];
                $this->pid = $pid;

                $this->Error->pid = $pid;
                $this->Error->limpiarTabla();

        //		$this->__limpiarTemportal($pid);

        //        $oASINC = &ClassRegistry::init(array('class' => 'Shells.Asincrono', 'alias' => 'Asincrono'));
                App::import('Model','Shells.Asincrono');
                $oASINC = new Asincrono(); 
                $oASINC->auditable = TRUE;
                $oASINC->id = $pid;
                

                $this->oASINC = $oASINC;            
            
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
		
//		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
//		$asinc->id = $pid; 

		$this->liquidacionID		= $oASINC->getParametro('p1');
		$this->fecha_pago		= $oASINC->getParametro('p2');
		$this->nro_recibo		= $oASINC->getParametro('p3');
		
		$DESIMPUTAR			= ($oASINC->getParametro('p4') == 1 ? 1 : 0);
                $SHELL_PID = $oASINC->getParametro('shell_pid');

		$oASINC->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");
		
//                debug($SHELL_PID);
		
		if($oLIQUI->isBloqueada($this->liquidacionID)):
			$idBloquedo = $oLIQUI->getBloqueoPID($this->liquidacionID);
			$msg = "PROCESO BLOQUEADO POR OTRO USUARIO [PID #$idBloquedo]....";
			$oASINC->actualizar(2,100,$msg);
			$msg2 = $asinc->getCadenaInfo($idBloquedo);
			$this->Temporal->setErrorMsg("BOQUEO PID #$idBloquedo",$msg2);
			return;
		endif;
		
		$oLIQUI->cerrar($this->liquidacionID);
		//verifico que no haya otros procesos bloqueados
//		$oLIQUI->bloquear($this->liquidacionID,$pid);
		
		//cargar la tabla liquidacion_cuotas para la liquidacion ID
		
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();
                
                
                ############################################################################################
                # SACAR LA CANTIDAD DE SOCIOS A IMPUTAR
                ############################################################################################
//                $socios = $oLSR->getSociosDebitadosNoCobrados($this->liquidacionID);
                
                $SQL = "select count(distinct socio_id) as cantidad from liquidacion_socio_rendiciones
                        where liquidacion_id = ".$this->liquidacionID." and indica_pago = 1
                        and orden_descuento_cobro_id = 0;";
                $registros = $oLSR->query($SQL);                
                
                if(empty($registros)):
                    $oASINC->actualizar(100,100,"FINALIZANDO...");
                    $oASINC->fin("**** PROCESO FINALIZADO ****");	
                    $this->__setCampo('bloqueada',0);
                    return;
                endif;                 
                
//                debug($socios);
                $cantidad = $registros[0][0]['cantidad'];
                
                $oASINC->setTotal($cantidad);
                $SHELL = new exec();
                
                $FRACCIONAR_LOTE = 20;
                $i = 0;
                $limit = intval($cantidad / $FRACCIONAR_LOTE);
                $limit = ($limit > 0 ? $limit : $cantidad);
                
//                debug($FRACCIONAR_LOTE." ** ".$cantidad." ** ".$limit);
                
                #BORRO LOS SUBPROCESOS
                $oASINC->query("DELETE FROM asincronos where trim(p13) = $pid");

                while (true){
                    
                    $SQL = "select socio_id from liquidacion_socio_rendiciones
                            where liquidacion_id = ".$this->liquidacionID." and indica_pago = 1
                            and orden_descuento_cobro_id = 0 
                            group by socio_id order by socio_id LIMIT $i,$limit ";

                    $socios = $oLSR->query($SQL);
                    
                    if (empty($socios)){
                        break;
                    }                    
                    $socio_id = $socios[0]['liquidacion_socio_rendiciones']['socio_id'];

                    //CREO UN REGISTRO ASINCRONO CLONANDO EL PRINCIPAL
                    $asincrono = $oASINC->read(null,$pid);

                    $SUBASINCRONO = new Asincrono();

                    $asincrono['Asincrono']['id'] = 0;
                    $asincrono['Asincrono']['p13'] = $pid; //guardo el id del principal
                    $asincrono['Asincrono']['titulo'] .= " [$i]";
                    $SUBASINCRONO->save($asincrono);
                    $asincrono['Asincrono']['id'] = $SUBASINCRONO->getLastInsertID();
                    
//                    debug($asincrono['Asincrono']['id']);

                    $CORE = CORE_PATH . "cake" . DIRECTORY_SEPARATOR . "console" . DIRECTORY_SEPARATOR . "cake.php";
                    $CMD = $oASINC->get_phpcli() . " $CORE imputar_pagos_fraccion_lote " .  $this->liquidacionID . " " . $DESIMPUTAR . " " . $asincrono['Asincrono']['id'] . " $SHELL_PID $i $limit " . $this->fecha_pago . " " . $this->nro_recibo . " -app ". APP_PATH;

//                    debug($CMD);
                    
                    $asincrono['Asincrono']['txt1'] = $CMD;
                    $asincrono['Asincrono']['shell_pid'] = $SHELL->background($CMD);
                    $SUBASINCRONO->save($asincrono);

                    $oASINC->actualizar(10, $cantidad, "CREANDO SUBPROCESO #" . $asincrono['Asincrono']['shell_pid'] . " [$i/$cantidad]");

                    $i += $limit;

                    if(!$SHELL->is_running($SHELL_PID)){
                        $STOP = TRUE;
                        break;
                    }

                    if($oASINC->detenido()){
                        $STOP = TRUE;
                        $SHELL->kill ($asincrono['Asincrono']['shell_pid']);
                        break;
                    }                    
                    
                    
                }
                
                
                while(true && $SHELL->is_running($SHELL_PID)){

                    $sql = "SELECT sum(total) as total, sum(contador) as contador FROM asincronos as Asincrono WHERE trim(p13) = $pid;";
                    $procesos = $oASINC->query($sql); 

                    if(empty($procesos)) break;

                    if(!isset($procesos[0][0]['total']) || !isset($procesos[0][0]['contador'])) break;

                    $total = $procesos[0][0]['total'];
                    $contador = $procesos[0][0]['contador'];

                    $oASINC->actualizar($contador, $total, "$contador / $total - IMPUTANDO COBROS ...");

                    if($contador >= $total){
                        $oASINC->actualizar($contador, $total, "FINALIZANDO ...");
                        $oASINC->fin("**** PROCESO FINALIZADO ****");
                        break;
                    }

                    if($oASINC->detenido()){
                        $STOP = TRUE;
                        $sql = "SELECT id,shell_pid FROM asincronos as Asincrono WHERE trim(p13) = $pid;";
                        $procesos = $oASINC->query($sql);
                        foreach ($procesos as $proceso){
                            $proceso['Asincrono']['estado'] = 'S';
                            $SHELL->kill ($proceso['Asincrono']['shell_pid']);
                            $oASINC->save($asincrono);

                        }
                    }

                }
                $oASINC->auditable = TRUE;
        //        if(!$STOP):
        //            $oASINC->actualizar(99,100,"ACTUALIZANDO CABECERA DE LIQUIDACION...");
        //            #actualizo la cabecera
        //            $this->__actualizarCabecera($this->liquidacionID,$pid);
        //            $this->__setCampo('bloqueada',0);
        //        endif;

                if(!$STOP):
                    $oASINC->actualizar(100,100,"FINALIZANDO...");
                    $oASINC->fin("**** PROCESO FINALIZADO ****");	
                    $this->__setCampo('bloqueada',0);	
                endif;                
                
		
		
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
					inner join liquidacion_socios as LiquidacionSocio on (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id)
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
			$oSC->deleteAll("SocioCalificacion.socio_id = $socio_id and SocioCalificacion.persona_beneficio_id = $persona_beneficio_id and SocioCalificacion.periodo = '$periodo'");
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
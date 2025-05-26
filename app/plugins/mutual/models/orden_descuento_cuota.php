<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class OrdenDescuentoCuota extends MutualAppModel{
	
	var $name = 'OrdenDescuentoCuota';
	var $habilitarBloqueo = true;
	
	var $belongsTo = array('Socio','Proveedor','OrdenDescuento');
	var $hasMany = array('OrdenDescuentoCobroCuota','LiquidacionCuota');
	
//	function read($fields,$id,$armaDatos=true){
//		$cuota = parent::read($fields,$id);
//		if($armaDatos)return $this->determinaEstado($cuota);
//		else return $cuota;
//	}
	
	function save($data = null, $validate = true, $fieldList = array()){
		$ret = parent::save($data);
		return $ret;
	}
	
	
	function getCuota($id,$determinaEstado=true){
		$tmp = array();
		$cuota = $this->read(null,$id);
		if(empty($cuota)) return null;
		array_push($tmp,$cuota);
		$cuota = $this->armaInfoAdicional($tmp,$determinaEstado);
		return $cuota[0];
	}
	
	function getPeriodo($id){
		$cuota = $this->read(null,$id);
		if(empty($cuota)) return null;
		else return $cuota['OrdenDescuentoCuota']['periodo'];
	}
	
	/**
	 * armaInfoAdicional
	 * @param $resultados
	 * @return unknown_type
	 */
	function armaInfoAdicional($resultados,$determinaEstado=true){
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBEN = new PersonaBeneficio();
		App::import('Model','Proveedores.Proveedor');
		$oProveedor = new Proveedor();
		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();

		
		
		
		foreach($resultados as $clave => $valor){

			if($determinaEstado)$resultados[$clave] = $this->determinaEstado($valor);
			
			//SACO LOS DATOS DESCRIPTIVOS DE LA TABLA GLOBAL
			$glb = $this->getGlobalDato('concepto_1',$valor['OrdenDescuentoCuota']['tipo_producto']);
			$resultados[$clave]['OrdenDescuentoCuota']['tipo_producto_desc'] = $glb['GlobalDato']['concepto_1'];
					
			$glb = $this->getGlobalDato('concepto_1',$valor['OrdenDescuentoCuota']['tipo_cuota']);
			$resultados[$clave]['OrdenDescuentoCuota']['tipo_cuota_desc'] = $glb['GlobalDato']['concepto_1'];
			
		
			$glb = $this->getGlobalDato('concepto_1',$valor['OrdenDescuentoCuota']['situacion']);
			$resultados[$clave]['OrdenDescuentoCuota']['situacion_desc'] = $glb['GlobalDato']['concepto_1'];


			if(!empty($valor['OrdenDescuentoCuota']['periodo_origen'])){
				$resultados[$clave]['OrdenDescuentoCuota']['periodo_d'] = $this->periodo($valor['OrdenDescuentoCuota']['periodo_origen']);
			}else{
				$resultados[$clave]['OrdenDescuentoCuota']['periodo_d'] = $this->periodo($valor['OrdenDescuentoCuota']['periodo']);
			}		

			
			
//			App::import('Model','Proveedores.Proveedor');
//			$oProveedor = new Proveedor();			
			
			$resultados[$clave]['OrdenDescuentoCuota']['proveedor'] = $oProveedor->getRazonSocialResumida($valor['OrdenDescuentoCuota']['proveedor_id']);
			
			$resultados[$clave]['OrdenDescuentoCuota']['proveedor_producto'] = $oProveedor->getRazonSocialResumida($valor['OrdenDescuentoCuota']['proveedor_id']) . ' / ' . $resultados[$clave]['OrdenDescuentoCuota']['tipo_producto_desc'] . ( !empty($valor['OrdenDescuentoCuota']['nro_referencia_proveedor']) ? ' (REF:'.$valor['OrdenDescuentoCuota']['nro_referencia_proveedor'].')' : '');
//			$resultados[$clave]['OrdenDescuentoCuota']['proveedor_producto'] = $valor['Proveedor']['razon_social_resumida'] . ' / ' . $resultados[$clave]['OrdenDescuentoCuota']['tipo_producto_desc'] . ( !empty($valor['OrdenDescuentoCuota']['nro_referencia_proveedor']) ? ' (REF:'.$valor['OrdenDescuentoCuota']['nro_referencia_proveedor'].')' : '');
			$resultados[$clave]['OrdenDescuentoCuota']['producto_cuota_ref'] = $resultados[$clave]['OrdenDescuentoCuota']['tipo_producto_desc'] . ' - ' . $resultados[$clave]['OrdenDescuentoCuota']['tipo_cuota_desc'] . ( !empty($valor['OrdenDescuentoCuota']['nro_referencia_proveedor']) ? ' (REF:'.$valor['OrdenDescuentoCuota']['nro_referencia_proveedor'].')' : '');
			$resultados[$clave]['OrdenDescuentoCuota']['producto_cuota'] = $resultados[$clave]['OrdenDescuentoCuota']['tipo_producto_desc'] . ' - ' . $resultados[$clave]['OrdenDescuentoCuota']['tipo_cuota_desc'];
			
			App::import('Model','Mutual.OrdenDescuento');
			$oOD = new OrdenDescuento();
            
                        $ordenDto = $oOD->getOrden($valor['OrdenDescuentoCuota']['orden_descuento_id']);
//            debug($ordenDto);
			
//			$resultados[$clave]['OrdenDescuentoCuota']['tipo_nro'] = $oOD->getTipoAndNro($valor['OrdenDescuentoCuota']['orden_descuento_id']);
//			$tipAndNro = $oOD->getTipoAndNro($valor['OrdenDescuentoCuota']['orden_descuento_id'],false);
			$resultados[$clave]['OrdenDescuentoCuota']['numero_odto'] = $ordenDto['OrdenDescuento']['numero'];
			$resultados[$clave]['OrdenDescuentoCuota']['tipo_nro'] = $ordenDto['OrdenDescuento']['tipo_nro'];
			
			$resultados[$clave]['OrdenDescuentoCuota']['cuota'] = str_pad($valor['OrdenDescuentoCuota']['nro_cuota'],2,"0",STR_PAD_LEFT).'/'.str_pad($ordenDto['OrdenDescuento']['cuotas'],2,"0",STR_PAD_LEFT);
//			$resultados[$clave]['OrdenDescuentoCuota']['cuota'] = $valor['OrdenDescuentoCuota']['nro_cuota'].'/'.$valor['OrdenDescuento']['cuotas'];
//			debug($valor);
			$resultados[$clave]['OrdenDescuentoCuota']['orden_descuento_periodo_ini'] = $ordenDto['OrdenDescuento']['periodo_ini'];
			$resultados[$clave]['OrdenDescuentoCuota']['orden_descuento_periodo_ini_d'] = $ordenDto['OrdenDescuento']['inicia_en'];
			$resultados[$clave]['OrdenDescuentoCuota']['orden_descuento_total'] = $ordenDto['OrdenDescuento']['importe_total'];
			$resultados[$clave]['OrdenDescuentoCuota']['orden_descuento_cuotas'] = $ordenDto['OrdenDescuento']['cuotas'];
			$resultados[$clave]['OrdenDescuentoCuota']['orden_descuento_impo_cuota'] = $ordenDto['OrdenDescuento']['importe_cuota'];
			$resultados[$clave]['OrdenDescuentoCuota']['orden_descuento_primer_vto_socio'] = $ordenDto['OrdenDescuento']['primer_vto_socio'];
			$resultados[$clave]['OrdenDescuentoCuota']['orden_descuento_primer_vto_proveedor'] = $ordenDto['OrdenDescuento']['primer_vto_proveedor'];
			$resultados[$clave]['OrdenDescuentoCuota']['orden_descuento_fecha'] = $ordenDto['OrdenDescuento']['fecha'];
			
			//cargo el benficio
			$beneficio = $oBEN->getBeneficio($valor['OrdenDescuentoCuota']['persona_beneficio_id'],false);
			$resultados[$clave]['OrdenDescuentoCuota']['organismo'] = $beneficio['PersonaBeneficio']['codigo_beneficio_desc'];
			$resultados[$clave]['OrdenDescuentoCuota']['beneficio'] = $beneficio['PersonaBeneficio']['string'];
			$resultados[$clave]['OrdenDescuentoCuota']['codigo_organismo'] = $beneficio['PersonaBeneficio']['codigo_beneficio'];
			
			//cargo los datos del socio
			$persona = $oSOCIO->getPersonaBySocioID($valor['OrdenDescuentoCuota']['socio_id']);
			
			$resultados[$clave]['OrdenDescuentoCuota']['persona_tipo_documento'] = $this->GlobalDato('concepto_1',$persona['Persona']['tipo_documento']);
			$resultados[$clave]['OrdenDescuentoCuota']['persona_documento'] = $persona['Persona']['documento'];
			$resultados[$clave]['OrdenDescuentoCuota']['persona_tdocndoc'] = $resultados[$clave]['OrdenDescuentoCuota']['persona_tipo_documento']."-".$persona['Persona']['documento'];
			$resultados[$clave]['OrdenDescuentoCuota']['persona_apenom'] = $persona['Persona']['apellido'].", ".$persona['Persona']['nombre'];
			
			$resultados[$clave]['OrdenDescuentoCuota']['bloqueo_liquidacion'] = null;
			if($valor['OrdenDescuentoCuota']['estado'] == 'A' && $this->habilitarBloqueo):
				//VERIFICAR SI LA CUOTA HAY QUE BLOQUEARLA O NO
				App::import('Model','mutual.LiquidacionCuota');
				$oLC = new LiquidacionCuota();
				$resultados[$clave]['OrdenDescuentoCuota']['bloqueo_liquidacion'] = $oLC->isCuotaOriginalBloqueada($valor['OrdenDescuentoCuota']['id']);
			endif;
                        
                        ##################################################################################
                        # CALCULAR METODO
                        ##################################################################################
                        $resultados[$clave]['OrdenDescuentoCuota']['detalle_items'] = null;
                        
                        if(!empty($ordenDto['OrdenDescuento']['metodo_calculo'])){

//                            App::import('model','proveedores.metodo_calculo_cuota');
//                            $oCALC = new MetodoCalculoCuota();                             
//                            
//                            $oCALC->solicitado = $ordenDto['OrdenDescuento']['importe_capital'];
//                            $oCALC->cuotas = $ordenDto['OrdenDescuento']['cuotas'];
//                            $oCALC->porcAdic = $ordenDto['OrdenDescuento']['gasto_admin_porc'];
//                            $oCALC->porcSello = $ordenDto['OrdenDescuento']['sellado_porc'];
//                            $oCALC->porcIVA = $ordenDto['OrdenDescuento']['iva_porc'];
//                            $oCALC->TNA =   $ordenDto['OrdenDescuento']['tna'];
//                            $oCALC->TEM = $ordenDto['OrdenDescuento']['tem'];
//                            $oCALC->TNM = $ordenDto['OrdenDescuento']['tnm'];                
//                            $oCALC->tasa = $oCALC->TNM;
//                            $oCALC->METODO_CALCULO = $ordenDto['OrdenDescuento']['metodo_calculo'];
//                            $oCALC->armar_plan();
//                            $resultados[$clave]['OrdenDescuentoCuota']['detalle_items'] = $oCALC->plan[$valor['OrdenDescuentoCuota']['nro_cuota']];
                        }
                        
                        ######################################################################################
                        # INFO DE CANCELACIONES 
                        ######################################################################################
                        App::import('model','mutual.CancelacionOrden');
						$oCANC = new CancelacionOrden();
                        $cancelaciones  = $oCANC->get_socio_by_estado_2($valor['OrdenDescuentoCuota']['socio_id'],'E',$valor['OrdenDescuentoCuota']['id']);
						$resultados[$clave]['CancelacionOrden'] = $cancelaciones;

                        
		}
        
		return $resultados;		
	}
	
	
	function infoCuota($cuota){

		//SACO LOS DATOS DESCRIPTIVOS DE LA TABLA GLOBAL
		$glb = $this->getGlobalDato('concepto_1',$cuota['tipo_producto']);
		$cuota['tipo_producto_desc'] = $glb['GlobalDato']['concepto_1'];
				
		$glb = $this->getGlobalDato('concepto_1',$cuota['tipo_cuota']);
		$cuota['tipo_cuota_desc'] = $glb['GlobalDato']['concepto_1'];
	
		$glb = $this->getGlobalDato('concepto_1',$cuota['situacion']);
		$cuota['situacion_desc'] = $glb['GlobalDato']['concepto_1'];
		
		$cuota['estado_desc'] = $this->codigos_estado_cuota[$cuota['estado']]['label_vista'];
		
		//armo la descripcion del producto
		App::import('Model','Proveedores.Proveedor');
		$oProveedor = new Proveedor();
		
		$proveedor = $oProveedor->getRazonSocialResumida($cuota['proveedor_id']);
		$cuota['proveedor'] = $proveedor;
		$cuota['proveedor_producto'] = $proveedor . ' / ' . $cuota['tipo_producto_desc'] . ( !empty($cuota['nro_referencia_proveedor']) ? ' (REF:'.$cuota['nro_referencia_proveedor'].')' : '');
		
		
		App::import('Model','Mutual.OrdenDescuento');
		$oOD = new OrdenDescuento();		
		$cuota['tipo_nro'] = $oOD->getTipoAndNro($cuota['orden_descuento_id']);
		$cuota['cuota_de_cuotas'] = str_pad($cuota['nro_cuota'],2,"0",STR_PAD_LEFT).'/'.$oOD->getCuotas($cuota['orden_descuento_id']);
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBEN = new PersonaBeneficio();

		$beneficio = $oBEN->getBeneficio($cuota['persona_beneficio_id'],false);
		$cuota['organismo'] = $beneficio['PersonaBeneficio']['codigo_beneficio_desc'];
		$cuota['beneficio'] = $beneficio['PersonaBeneficio']['string'];
		$cuota['codigo_organismo'] = $beneficio['PersonaBeneficio']['codigo_beneficio'];
		
		$cuota['bloqueo_liquidacion'] = null;
		if($cuota['estado'] == 'A' && $this->habilitarBloqueo):
			//VERIFICAR SI LA CUOTA HAY QUE BLOQUEARLA O NO
			App::import('Model','mutual.LiquidacionCuota');
			$oLC = new LiquidacionCuota();
			$cuota['bloqueo_liquidacion'] = $oLC->isCuotaOriginalBloqueada($cuota['id']);
		endif;		
		
		return $cuota;
	}
	
	/**
	 * determinaEstado
	 * @param $cuotas
	 * @return unknown_type
	 */
	function determinaEstado($cuota){
		$pagado = 0;
		$hoy = date('Y-m-d');
		$periodo = date('Ym');

                
                ############################################################################
                # VERIFICAR PENDIENTE DE ACREDITAR / VERIFICAR PENDIENTE EN CANCELACIONES
                ############################################################################
                $cuota = $this->__calculaSaldo($cuota, $periodo,NULL,TRUE);
                
                
		$cuota['OrdenDescuentoCuota']['vencida'] = $this->isVencida($cuota['OrdenDescuentoCuota']['id']);
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oPago = new OrdenDescuentoCobroCuota();		
		$pagado = $oPago->getMontoPagoByCuota($cuota['OrdenDescuentoCuota']['id']);
		
		$cuota['OrdenDescuentoCuota']['estado_desc'] = $this->codigos_estado_cuota[$cuota['OrdenDescuentoCuota']['estado']]['label_vista'];
//		if($cuota['OrdenDescuentoCuota']['vencimiento'] < $hoy)$cuota['OrdenDescuentoCuota']['vencida'] = 1;
		
// 		debug($pagado);
		
		if($pagado == 0){
			#CUOTA ADEUDADA COMPLETAMENTE
//			$cuota['OrdenDescuentoCuota']['estado'] = $this->codigos_estado_cuota['A']['codigo_db'];
			$cuota['OrdenDescuentoCuota']['estado_desc'] = $this->codigos_estado_cuota[$cuota['OrdenDescuentoCuota']['estado']]['label_vista'];
			
//			if(!empty($cuota['OrdenDescuentoCuota']['vencimiento'])){
//				if($cuota['OrdenDescuentoCuota']['vencimiento'] < $hoy)$cuota['OrdenDescuentoCuota']['vencida'] = 1;
//			}else{
//				if(empty($cuota['OrdenDescuentoCuota']['vencimiento']) && $cuota['OrdenDescuentoCuota']['periodo'] >= $periodo) $cuota['OrdenDescuentoCuota']['vencida'] = 0;
//				else $cuota['OrdenDescuentoCuota']['vencida'] = 1;
//			}
			
			//algunas cuotas estan marcadas como P y no tienen registro de pago
			if($cuota['OrdenDescuentoCuota']['estado'] == $this->codigos_estado_cuota['P']['codigo_db'])$pagado = $cuota['OrdenDescuentoCuota']['importe'];
			
		}else if($pagado >= $cuota['OrdenDescuentoCuota']['importe']){
			#CUOTA PAGADA TOTALMENTE
			$cuota['OrdenDescuentoCuota']['vencida'] = 0;
			$cuota['OrdenDescuentoCuota']['estado'] = $this->codigos_estado_cuota['P']['codigo_db'];
			$cuota['OrdenDescuentoCuota']['estado_desc'] = $this->codigos_estado_cuota['P']['label_vista'];
			
		}
		
		$cuota['OrdenDescuentoCuota']['pagado'] = $pagado;
		
		if($this->isDeudaBySituacion($cuota['OrdenDescuentoCuota']['situacion']) && $cuota['OrdenDescuentoCuota']['estado'] == $this->codigos_estado_cuota['A']['codigo_db']){
			$cuota['OrdenDescuentoCuota']['saldo_cuota'] = $cuota['OrdenDescuentoCuota']['importe'] - abs($pagado);
		}else{
			$cuota['OrdenDescuentoCuota']['saldo_cuota'] = 0;
		}
		
		
		//si la cuota esta PAGADA cargar la fecha del ultimo pago
		if($cuota['OrdenDescuentoCuota']['estado'] == $this->codigos_estado_cuota['P']['codigo_db']){
			App::import('Mutual','Mutual.OrdenDescuentoCobroCuota');
			$oPago = new OrdenDescuentoCobroCuota();
			$cuota['OrdenDescuentoCuota']['fecha_ultimo_pago'] = $oPago->getFechaUltimoPago($cuota['OrdenDescuentoCuota']['id']);
			
		}
                
// 		debug($cuota);
		//provisorio adrian hasta que esten migrados los pagos
//		if($cuota['OrdenDescuentoCuota']['estado'] == $this->codigos_estado_cuota['P']['codigo_db'])$cuota['OrdenDescuentoCuota']['saldo_cuota'] = 0;
		return $cuota;
	}
		
	/**
	 * calculaAtraso
	 * @param $resultados
	 * @return unknown_type
	 */
	function calculaAtraso($resultados,$proveedor_id=0,$codigo_organismo=null){
		
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oPago = new OrdenDescuentoCobroCuota();
		
		$conditions = array();
		
		foreach($resultados as $clave => $valor){
			
			$conditions['OrdenDescuentoCuota.socio_id'] = $valor['OrdenDescuentoCuota']['socio_id'];
			$conditions['OrdenDescuentoCuota.estado'] = 'A';
			$conditions['OrdenDescuentoCuota.situacion'] = 'MUTUSICUMUTU';
			$conditions['OrdenDescuentoCuota.periodo <'] = $valor['OrdenDescuentoCuota']['periodo'];
			if(!empty($proveedor_id) && $proveedor_id != 0) $conditions['OrdenDescuentoCuota.proveedor_id'] = $proveedor_id;
			
			if(!empty($codigo_organismo)):
				$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
				$conditions['PersonaBeneficio.codigo_beneficio'] = $codigo_organismo;
			endif;			
			
			$deuda = $this->find('all',array('conditions' => $conditions,'fields' => array("OrdenDescuentoCuota.id,OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.estado,OrdenDescuentoCuota.importe"),'order' => array('OrdenDescuentoCuota.periodo')));
						
			if(count($deuda)!=0){
				$ATRASO = 0;
				$PAGOS = 0;	
				foreach($deuda as $cuota){
					$ATRASO += $cuota['OrdenDescuentoCuota']['importe'];
					$pagado = $oPago->getMontoPagoByCuota($cuota['OrdenDescuentoCuota']['id']);
					//si esta marcado como P tomar como importe pagado el importe de la cuota
					if($pagado == 0 && $cuota['OrdenDescuentoCuota']['estado'] == $this->codigos_estado_cuota['P']['codigo_db']){
						$pagado = $cuota['OrdenDescuentoCuota']['importe'];
					}
					$PAGOS += $pagado;
				}
				$resultados[$clave]['OrdenDescuentoCuota']['atraso'] = $ATRASO - $PAGOS;
			}else{
				$resultados[$clave]['OrdenDescuentoCuota']['atraso'] = 0;
			}
		}
		return $resultados;
	}
	
	/**
	 * calculaEnvios
	 * @param $resultados
	 * @return unknown_type
	 */
	function calculaEnvios($resultados){
		App::import('model','Mutual.LiquidacionSocio');
		$oLiqSoc = new LiquidacionSocio();
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();		
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();		
		
		foreach($resultados as $clave => $valor){
//			$liquidado = $oLiqSoc->importeLiquidadoBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo']);
			
			//saco lo liquidado
			$liquidado_total = $oLC->getTotalLiquidadoBySocioByPeriodo2($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo'],'T');
			$liquidado_periodo = $oLC->getTotalLiquidadoBySocioByPeriodo2($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo'],'P');
			$liquidado_atraso = $oLC->getTotalLiquidadoBySocioByPeriodo2($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo'],'A');
			
			$debitado = $oLSR->getTotalBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo']);
			$aDebitar = $oLiqSoc->importeADebitarBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo']);
//			$pendienteImputar = $oLC->getTotalPendienteImputarBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo']);

			//saco lo imputado
			$imputado_total = $oLC->getTotalImputadoBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo']);
			$imputado_periodo = $oLC->getTotalImputadoBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo'],'P');
			$imputado_atraso = $oLC->getTotalImputadoBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo'],'A');
			
			//saco lo pendiente de imputar
			$pendienteImputar_total = $oLC->getTotalPendienteImputarBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo']);
			$pendienteImputar_periodo = $oLC->getTotalPendienteImputarBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo'],'P');			
			$pendienteImputar_atraso = $oLC->getTotalPendienteImputarBySocioByPeriodo($valor['OrdenDescuentoCuota']['socio_id'],$valor['OrdenDescuentoCuota']['periodo'],'A');
			
			
			#CALCULO LO ENVIADO A DESCUENTO Y DESCONTADO PARA EL PERIODO
			$resultados[$clave]['OrdenDescuentoCuota']['liquidado'] = $liquidado_total;
			$resultados[$clave]['OrdenDescuentoCuota']['debitado'] = $debitado;
			$resultados[$clave]['OrdenDescuentoCuota']['imputado'] = $imputado_total;
			$resultados[$clave]['OrdenDescuentoCuota']['adebitar'] = $aDebitar;
			$resultados[$clave]['OrdenDescuentoCuota']['pendiente_imputar'] = $pendienteImputar_total;
			
			$resultados[$clave]['OrdenDescuentoCuota']['liquidado_total'] = $liquidado_total;
			$resultados[$clave]['OrdenDescuentoCuota']['liquidado_periodo'] = $liquidado_periodo;
			$resultados[$clave]['OrdenDescuentoCuota']['liquidado_atraso'] = $liquidado_atraso;

			$resultados[$clave]['OrdenDescuentoCuota']['imputado_total'] = $imputado_total;
			$resultados[$clave]['OrdenDescuentoCuota']['imputado_periodo'] = $imputado_periodo;
			$resultados[$clave]['OrdenDescuentoCuota']['imputado_atraso'] = $imputado_atraso;	
			
			$resultados[$clave]['OrdenDescuentoCuota']['pendiente_imputar_total'] = $pendienteImputar_total;
			$resultados[$clave]['OrdenDescuentoCuota']['pendiente_imputar_periodo'] = $pendienteImputar_periodo;
			$resultados[$clave]['OrdenDescuentoCuota']['pendiente_imputar_atraso'] = $pendienteImputar_atraso;
			
		}
		
		return $resultados;
	}
	

	/**
	 * Devuelve las cuotas por socio por periodo. Si $soloDeuda es true no filtra los periodos
	 * METODO USADO PARA ARMAR EL ESTADO DE CUENTA
	 * @param unknown_type $socio_id
	 * @param unknown_type $periodo_desde
	 * @param unknown_type $periodo_hasta
	 * @param unknown_type $soloDeuda
	 * @param boolean $discriminaPagos
	 */
	function cuotasSocioByPeriodo($socio_id,$periodo_desde,$periodo_hasta,$soloDeuda=false,$proveedor_id=0,$codigo_organismo=null,$discriminaPagos=false){
		$ret = array();
		$this->unbindModel(array('belongsTo' => array('Socio'),'hasMany' => array('OrdenDescuentoCobroCuota')));
		
		$conditions_1 = array();
		$conditions_1['OrdenDescuentoCuota.socio_id'] = $socio_id;
		
		if($soloDeuda) $conditions_1['OrdenDescuentoCuota.estado'] = 'A';
		
		if(!$soloDeuda) $conditions_1['OrdenDescuentoCuota.periodo BETWEEN ? AND ?'] = array($periodo_desde,$periodo_hasta);
		else $conditions_1['OrdenDescuentoCuota.periodo <='] = $periodo_hasta;
		
		if(!empty($proveedor_id) && $proveedor_id != 0) $conditions_1['OrdenDescuentoCuota.proveedor_id'] = $proveedor_id;
		if(!empty($codigo_organismo)):
			$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
			$conditions_1['PersonaBeneficio.codigo_beneficio'] = $codigo_organismo;
		endif;
		
		$periodos = $this->find('all',array('conditions' => $conditions_1,'fields' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'group' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'order' => array('OrdenDescuentoCuota.periodo')));
		
//		if(!$soloDeuda) $periodos = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.periodo BETWEEN ? AND ?' => array($periodo_desde,$periodo_hasta)),'fields' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'group' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'order' => array('OrdenDescuentoCuota.periodo')));
//		else $periodos = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.estado' => 'A','OrdenDescuentoCuota.periodo <=' => $periodo_hasta),'fields' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'group' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'order' => array('OrdenDescuentoCuota.periodo')));
//		
		$periodos = $this->calculaAtraso($periodos,$proveedor_id,$codigo_organismo);
		$periodos = $this->calculaEnvios($periodos);
		$this->bindModel(array('hasMany' => array('OrdenDescuentoCobroCuota')));
		$saldoPeriodo = 0;
		
		$conditions_2 = array();
		$conditions_2['OrdenDescuentoCuota.socio_id'] = $socio_id;
		if(!empty($proveedor_id) && $proveedor_id != 0 ) $conditions_2['OrdenDescuentoCuota.proveedor_id'] = $proveedor_id;
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oPago = new OrdenDescuentoCobroCuota();		

		
		foreach($periodos as $idx => $periodo){
//			if(!$soloDeuda)$cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.periodo' => $periodo['OrdenDescuentoCuota']['periodo']),'order' => array('OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.vencimiento')));
//			else $cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.periodo' => $periodo['OrdenDescuentoCuota']['periodo'],'OrdenDescuentoCuota.estado' => 'A'),'order' => array('OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.vencimiento')));

			
			if(!empty($codigo_organismo)):
				$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
				$conditions_2['PersonaBeneficio.codigo_beneficio'] = $codigo_organismo;
			endif;			
			
			$conditions_2['OrdenDescuentoCuota.periodo'] = $periodo['OrdenDescuentoCuota']['periodo'];
			$this->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
//			$cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.periodo' => $periodo['OrdenDescuentoCuota']['periodo']),'order' => array('OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.vencimiento')));
			$cuotas = $this->find('all',array('conditions' => $conditions_2,'order' => array('OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.vencimiento')));
			
			$cuotas = $this->armaInfoAdicional($cuotas);

			if($soloDeuda) $cuotas = Set::extract('/OrdenDescuentoCuota[estado=A]',$cuotas);
			if($discriminaPagos):
				foreach($cuotas as $idc => $cuota):
					$cuota['OrdenDescuentoCuota']['cobros'] = $oPago->getCobrosByCuota($cuota['OrdenDescuentoCuota']['id']);
					$cuotas[$idc] = $cuota;
				endforeach;
			endif;
			if(!empty($cuotas)){
				$ret[$periodo['OrdenDescuentoCuota']['periodo']] = array(
						'detalle_cuotas'=>$cuotas,
						'atraso'=>$periodo['OrdenDescuentoCuota']['atraso'],
						'liquidado' => $periodo['OrdenDescuentoCuota']['liquidado'],
						'debitado' => $periodo['OrdenDescuentoCuota']['debitado'],
						'imputado' => $periodo['OrdenDescuentoCuota']['imputado'],
						'adebitar' => $periodo['OrdenDescuentoCuota']['adebitar'],
						'pendiente_imputar' => $periodo['OrdenDescuentoCuota']['pendiente_imputar'],
						'liquidado_total' => $periodo['OrdenDescuentoCuota']['liquidado_total'],
						'liquidado_periodo' => $periodo['OrdenDescuentoCuota']['liquidado_periodo'],
						'liquidado_atraso' => $periodo['OrdenDescuentoCuota']['liquidado_atraso'],
						'imputado_total' => $periodo['OrdenDescuentoCuota']['imputado_total'],
						'imputado_periodo' => $periodo['OrdenDescuentoCuota']['imputado_periodo'],
						'imputado_atraso' => $periodo['OrdenDescuentoCuota']['imputado_atraso'],
						'pendiente_imputar_total' => $periodo['OrdenDescuentoCuota']['pendiente_imputar_total'],
						'pendiente_imputar_periodo' => $periodo['OrdenDescuentoCuota']['pendiente_imputar_periodo'],
						'pendiente_imputar_atraso' => $periodo['OrdenDescuentoCuota']['pendiente_imputar_atraso'],
				);
			}
		}
// 		debug($ret);
		return $ret;
	}
	
	

	/**
	 * periodosBySocio
	 * @param $socio_id
	 * @param $bindModel
	 * @param $ordenDescuento
	 * @param $soloAdeudados
	 * @param $ordenPeriodo
	 * @param $toArray
	 * @return unknown_type
	 */
	function periodosBySocio($socio_id,$bindModel=false,$ordenDescuento=null,$soloAdeudados=false,$ordenPeriodo="ASC",$toArray=false){
		$ret = array();
		if(!$bindModel)$this->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$conditions = array('OrdenDescuentoCuota.socio_id' => $socio_id);
		if(!empty($ordenDescuento)) $conditions['OrdenDescuentoCuota.orden_descuento_id'] = $ordenDescuento;
		if($soloAdeudados) $conditions['OrdenDescuentoCuota.estado'] = 'A';
		$periodos = $this->find('all',array('conditions' => $conditions,'fields' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'group' => array('OrdenDescuentoCuota.periodo','OrdenDescuentoCuota.socio_id'),'order' => array("OrdenDescuentoCuota.periodo $ordenPeriodo")));
		if($toArray) $periodos = Set::extract("{n}.OrdenDescuentoCuota.periodo",$periodos);
		return $periodos;
	}
		
	/**
	 * atrasoSocioByPeriodo
	 * Atraso del socio agrupado por periodo
	 * @param $socio_id
	 * @return unknown_type
	 */
	function atrasoSocioByPeriodo($socio_id,$periodo,$proveedor_id=0,$codigo_organismo=0){
		$ret = array();
		$atraso = array();
		
		$this->unbindModel(array('belongsTo' => array('Socio')));
//		$periodos = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.estado ' => 'A','OrdenDescuentoCuota.periodo < ' => $periodo),'fields' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'group' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'order' => array('OrdenDescuentoCuota.periodo')));
		
		$conditions_1 = array();
		$conditions_1['OrdenDescuentoCuota.socio_id'] = $socio_id;
		$conditions_1['OrdenDescuentoCuota.estado'] = 'A';
		$conditions_1['OrdenDescuentoCuota.periodo <'] = $periodo;
		if(!empty($proveedor_id) && $proveedor_id != 0) $conditions_1['OrdenDescuentoCuota.proveedor_id'] = $proveedor_id;

		if(!empty($codigo_organismo) && $codigo_organismo != "0" ):
			$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
			$conditions_1['PersonaBeneficio.codigo_beneficio'] = $codigo_organismo;
		endif;		
		
		$periodos = $this->find('all',array('conditions' => $conditions_1,'fields' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'group' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.socio_id'),'order' => array('OrdenDescuentoCuota.periodo')));
		$periodos = $this->calculaAtraso($periodos,$proveedor_id,$codigo_organismo);
		$periodos = $this->calculaEnvios($periodos);
		$saldoPeriodo = 0;
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oPago = new OrdenDescuentoCobroCuota();		
		
		foreach($periodos as $idx => $periodo){
			
			$conditions_2 = array();
			$conditions_2['OrdenDescuentoCuota.socio_id'] = $socio_id;
			$conditions_2['OrdenDescuentoCuota.estado'] = 'A';
			$conditions_2['OrdenDescuentoCuota.periodo'] = $periodo['OrdenDescuentoCuota']['periodo'];
			if(!empty($proveedor_id) && $proveedor_id != 0) $conditions_2['OrdenDescuentoCuota.proveedor_id'] = $proveedor_id;
			if(!empty($codigo_organismo) && $codigo_organismo != "0" ):
				$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
				$conditions_2['PersonaBeneficio.codigo_beneficio'] = $codigo_organismo;
			endif;			

			$cuotas = $this->find('all',array('conditions' => $conditions_2,'order' => array('OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.vencimiento')));
//			$cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.periodo' => $periodo['OrdenDescuentoCuota']['periodo'],'OrdenDescuentoCuota.situacion' => 'MUTUSICUMUTU'),'order' => array('OrdenDescuentoCuota.tipo_orden_dto,OrdenDescuentoCuota.vencimiento')));
			$saldoCuota = 0;
			$atraso = array();
			foreach($cuotas as $idx => $cuota){
				$saldoCuota = 0;
				$cuota['OrdenDescuentoCuota']['pagado'] = $oPago->getMontoPagoByCuota($cuota['OrdenDescuentoCuota']['id']);
				//si esta marcado como P tomar como importe pagado el importe de la cuota
				if($cuota['OrdenDescuentoCuota']['pagado'] == 0 && $cuota['OrdenDescuentoCuota']['estado'] == $this->codigos_estado_cuota['P']['codigo_db']){
					$cuota['OrdenDescuentoCuota']['pagado'] = $cuota['OrdenDescuentoCuota']['importe'];
				}				
				$saldoCuota = $cuota['OrdenDescuentoCuota']['importe'] - $cuota['OrdenDescuentoCuota']['pagado'];
//				$cuota['OrdenDescuentoCuota']['saldo_cuota'] = $saldoCuota;
				if($saldoCuota != 0){
					$cuota['OrdenDescuentoCuota']['saldo_cuota'] = $saldoCuota;
					array_push($atraso,$cuota);
				}
			}
			$cuotas = $this->armaInfoAdicional($atraso);

			if(!empty($cuotas))$ret[$periodo['OrdenDescuentoCuota']['periodo']] = array('detalle_cuotas'=>$cuotas,'atraso'=>$periodo['OrdenDescuentoCuota']['atraso'],'a_debitar' => $periodo['OrdenDescuentoCuota']['adebitar'],'debitado' => $periodo['OrdenDescuentoCuota']['debitado']);
			
//			$cuotas = $this->armaInfoAdicional($cuotas);
//			$ret[$periodo['OrdenDescuentoCuota']['periodo']] = array('detalle_cuotas'=>$cuotas,'atraso'=>$periodo['OrdenDescuentoCuota']['atraso'],'a_debitar' => $periodo['OrdenDescuentoCuota']['a_debitar'],'debitado' => $periodo['OrdenDescuentoCuota']['debitado']);
		}
		return $ret;
	}
			
	/**
	 * cuotasAdeudadasBySocio
	 * 
	 * @param $socio_id
	 * @param $situacion
	 * @return unknown_type
	 */
	function cuotasAdeudadasBySocio($socio_id,$situacion='MUTUSICUMUTU',$filtraConvenios=false){
		$deuda = array();
        
        $conditions = array();
        $conditions['OrdenDescuentoCuota.socio_id'] = $socio_id;
        if($filtraConvenios) $conditions['OrdenDescuentoCuota.tipo_orden_dto <>'] = 'CONVE';
        if(!empty($situacion))$conditions['OrdenDescuentoCuota.situacion'] = $situacion;
        $conditions['OrdenDescuentoCuota.estado'] = 'A';
        $cuotas = $this->find('all',array('conditions' => $conditions,'order' => array('OrdenDescuentoCuota.periodo ASC, OrdenDescuentoCuota.nro_cuota DESC')));
//		if($filtraConvenios) $cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.tipo_orden_dto <>' => 'CONVE','OrdenDescuentoCuota.situacion ' => $situacion,'OrdenDescuentoCuota.estado ' => 'A'),'order' => array('OrdenDescuentoCuota.periodo')));
//		else $cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.situacion ' => $situacion,'OrdenDescuentoCuota.estado ' => 'A'),'order' => array('OrdenDescuentoCuota.periodo ASC, OrdenDescuentoCuota.nro_cuota DESC')));
        
		$cuotas = $this->armaInfoAdicional($cuotas);
		foreach($cuotas as $clave => $cuota){
			if($cuota['OrdenDescuentoCuota']['estado'] != 'P'){
				$deuda[$clave] = $cuota; 
			}

		}
	 	return $deuda;
	}
	
	
	function cuotasAdeudadasBySocioByPeriodoCorte($socio_id,$periodoCorte=null,$proveedor_id=null,$situacion='MUTUSICUMUTU'){
		
		$conditions = array();
		$conditions['OrdenDescuentoCuota.socio_id'] = $socio_id;
		$conditions['OrdenDescuentoCuota.periodo <='] = (empty($periodoCorte) ? date('Ym') : $periodoCorte);
		$conditions['OrdenDescuentoCuota.situacion'] = $situacion;
		$conditions['OrdenDescuentoCuota.estado'] = "A";
		if(!empty($proveedor_id)) $conditions['OrdenDescuentoCuota.proveedor_id'] = $proveedor_id;
		
		$cuotas = $this->find('all',array('conditions' => $conditions,'order' => array('OrdenDescuentoCuota.periodo ASC, OrdenDescuentoCuota.nro_cuota DESC')));
		$cuotas = $this->armaInfoAdicional($cuotas,false);
		$cuotas = Set::extract("/OrdenDescuentoCuota",$cuotas);

		$cuotasAdeudadas = array();		
		
		if(!empty($cuotas)):
		
			App::import('Model','Mutual.OrdenDescuentoCobroCuota');
			$oPago = new OrdenDescuentoCobroCuota();		
			
			foreach($cuotas as $idx => $cuota):
			
				$pagado = $oPago->getMontoPagoByCuota($cuota['OrdenDescuentoCuota']['id']);	
				$cuota['OrdenDescuentoCuota']['pago_acumulado'] = $pagado;
				$cuota['OrdenDescuentoCuota']['saldo_actual'] = $cuota['OrdenDescuentoCuota']['importe'] - $pagado;

				if($cuota['OrdenDescuentoCuota']['saldo_actual'] != 0) array_push($cuotasAdeudadas,$cuota);
		
			endforeach;
			
		endif;
		
		return $cuotasAdeudadas;
		
	}

	
	function cuotasAdeudadasBySocioByBeneficio($socio_id,$persona_beneficio_id,$situacion='MUTUSICUMUTU',$filtraConvenios=false){
		$deuda = array();
		if($filtraConvenios) $cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.persona_beneficio_id' => $persona_beneficio_id,'OrdenDescuentoCuota.tipo_orden_dto <>' => 'CONVE','OrdenDescuentoCuota.situacion ' => $situacion,'OrdenDescuentoCuota.estado ' => 'A'),'order' => array('OrdenDescuentoCuota.periodo')));
		else $cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.persona_beneficio_id' => $persona_beneficio_id,'OrdenDescuentoCuota.situacion ' => $situacion,'OrdenDescuentoCuota.estado ' => 'A'),'order' => array('OrdenDescuentoCuota.periodo')));
		$cuotas = $this->armaInfoAdicional($cuotas);
		foreach($cuotas as $clave => $cuota){
			if($cuota['OrdenDescuentoCuota']['estado'] != 'P'){
				$deuda[$clave] = $cuota; 
			}

		}
	 	return $deuda;
	}	
	
	function cuotasAdeudadasByOrdenDto($orden_descuento_id,$situacion='MUTUSICUMUTU',$filtraConvenios=false,$periodoControl = null,$armaInfoAdicional=TRUE,$noIncEnCancelacionPendiente=false){
// 		$deuda = array();
// 		if($filtraConvenios)$cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.orden_descuento_id' => $orden_descuento_id,'OrdenDescuentoCuota.tipo_orden_dto <>' => 'CONVE','OrdenDescuentoCuota.situacion ' => $situacion,'OrdenDescuentoCuota.estado ' => 'A'),'order' => array('OrdenDescuentoCuota.periodo')));
// 		else $cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.orden_descuento_id' => $orden_descuento_id,'OrdenDescuentoCuota.situacion ' => $situacion,'OrdenDescuentoCuota.estado ' => 'A'),'order' => array('OrdenDescuentoCuota.periodo')));
// 		if(!empty($cuotas)):
// 			$cuotas = $this->armaInfoAdicional($cuotas);
// 			foreach($cuotas as $clave => $cuota){
// 				if($cuota['OrdenDescuentoCuota']['estado'] != 'P'){
// 					$deuda[$clave] = $cuota; 
// 				}
	
// 			}
// 		endif;
		
// 		$periodoControl = (empty($periodoControl) ? date('Ym') : $periodoControl);
		
//		$sql = "SELECT * 
//				FROM orden_descuento_cuotas as OrdenDescuentoCuota
//				WHERE 
//					OrdenDescuentoCuota.orden_descuento_id = $orden_descuento_id
//                                        AND OrdenDescuentoCuota.estado NOT IN ('B','C')     
//					".(!empty($periodoControl) ? " AND OrdenDescuentoCuota.periodo <= '$periodoControl' " : "")."
//                    ".(!empty($situacion) ? " AND OrdenDescuentoCuota.situacion = '$situacion' " : " ")."    
//					".($filtraConvenios ? " AND OrdenDescuentoCuota.tipo_orden_dto <> 'CONVE'" : "")."
//					AND OrdenDescuentoCuota.importe > IFNULL((
//																SELECT SUM(cocu.importe) from
//																orden_descuento_cobro_cuotas cocu
//																INNER JOIN orden_descuento_cobros  co ON (co.id = cocu.orden_descuento_cobro_id) 
//																WHERE 
//																cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//																".(!empty($periodoControl) ? " AND co.periodo_cobro <= '$periodoControl' " : "").")
//																,0)
//					ORDER BY OrdenDescuentoCuota.periodo;";
		$sql = "SELECT * 
				FROM orden_descuento_cuotas as OrdenDescuentoCuota
				WHERE 
					OrdenDescuentoCuota.orden_descuento_id = $orden_descuento_id
					".(!empty($periodoControl) ? " AND OrdenDescuentoCuota.periodo <= '$periodoControl' " : "")."
					".(!empty($situacion) ? " AND OrdenDescuentoCuota.situacion = '$situacion' " : " ")."
					".($filtraConvenios ? " AND OrdenDescuentoCuota.tipo_orden_dto <> 'CONVE'" : "")."
					AND OrdenDescuentoCuota.importe > IFNULL((
                                        SELECT SUM(cocu.importe) from
                                        orden_descuento_cobro_cuotas cocu
                                        INNER JOIN orden_descuento_cobros  co ON (co.id = cocu.orden_descuento_cobro_id) 
                                        WHERE 
                                        cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                                        ".(!empty($periodoControl) ? " AND co.periodo_cobro <= '$periodoControl' " : "").")
                                        ,0) 
					".($noIncEnCancelacionPendiente ? " AND OrdenDescuentoCuota.id NOT IN (
						SELECT orden_descuento_cuota_id 
						FROM cancelacion_orden_cuotas AS coc
						INNER JOIN cancelacion_ordenes c ON (c.id = coc.cancelacion_orden_id)
						WHERE c.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
						AND c.estado = 'E'
						)" : "")."																		
					ORDER BY OrdenDescuentoCuota.periodo;";            
		$cuotas = $this->query($sql);
		if(!empty($cuotas) && $armaInfoAdicional) $cuotas = $this->armaInfoAdicional($cuotas);
	 	return $cuotas;
	}

	
	function cuotasByOrdenDto($orden_descuento_id,$discriminaPagos=false,$soloDeuda=false){
		$cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.orden_descuento_id' => $orden_descuento_id),'order' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.nro_cuota,OrdenDescuentoCuota.vencimiento,OrdenDescuentoCuota.tipo_cuota')));
		$cuotas = $this->armaInfoAdicional($cuotas);
// 		debug($cuotas);
// 		exit;
		if(!empty($cuotas)):
			App::import('Model','Mutual.OrdenDescuentoCobroCuota');
			$oPago = new OrdenDescuentoCobroCuota();		
			if($soloDeuda) $cuotas = Set::extract('/OrdenDescuentoCuota[estado=A]',$cuotas);
			if($discriminaPagos):
//				$cuotas = Set::sort($cuotas, '{n}.OrdenDescuentoCuota.fecha_ultimo_pago', 'asc');
				foreach($cuotas as $idc => $cuota):
					$cuota['OrdenDescuentoCuota']['cobros'] = $oPago->getCobrosByCuota($cuota['OrdenDescuentoCuota']['id']);
					$cuotas[$idc] = $cuota;
				endforeach;
			endif;
		endif;
		return $cuotas;
	}
	

	/**
	 * cuotas que estan adeudadas para un periodo determinado y segun situacion
	 * @param $periodo
	 * @param $situacion
	 * @return unknown_type
	 */
	function cuotasAdeudadasByPeriodo($periodo,$situacion='MUTUSICUMUTU'){
		$this->unbindModel(array('belongsTo' => array('Socio','OrdenDescuento','Proveedor')));
		$cuotas = $this->find('all',array(
								'conditions' => array('OrdenDescuentoCuota.periodo' => $periodo,'OrdenDescuentoCuota.estado' => 'A','OrdenDescuentoCuota.situacion' => $situacion),
								'fields' => array(
										'OrdenDescuentoCuota.id',
										'OrdenDescuentoCuota.orden_descuento_id',
										'OrdenDescuentoCuota.socio_id',
										'OrdenDescuentoCuota.persona_beneficio_id',
										'OrdenDescuentoCuota.nro_cuota',
										'OrdenDescuentoCuota.importe',
										'OrdenDescuentoCuota.situacion',
										'OrdenDescuentoCuota.tipo_orden_dto',
										'OrdenDescuentoCuota.tipo_producto',
										'OrdenDescuentoCuota.tipo_cuota',
										'OrdenDescuentoCuota.periodo',
										'OrdenDescuentoCuota.proveedor_id',
										'OrdenDescuentoCuota.vencimiento',
								),
								'order' => array('OrdenDescuentoCuota.periodo')
		));
	 	return $cuotas;
	}


	/**
	 * cuotas adeudadas de un socio a un periodo determinado
	 * @param $socio_id
	 * @param $periodo
	 * @param $situacion
	 * @return unknown_type
	 */
	function cuotasAdeudadasBySocioAlPeriodo($socio_id,$periodo,$situacion='MUTUSICUMUTU'){
		$this->unbindModel(array('belongsTo' => array('Socio','OrdenDescuento','Proveedor')));
		$cuotas = $this->find('all',array(
								'conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.periodo <=' => $periodo,'OrdenDescuentoCuota.estado' => 'A','OrdenDescuentoCuota.situacion' => $situacion),
								'fields' => array(
										'OrdenDescuentoCuota.id',
										'OrdenDescuentoCuota.orden_descuento_id',
										'OrdenDescuentoCuota.socio_id',
										'OrdenDescuentoCuota.persona_beneficio_id',
										'OrdenDescuentoCuota.nro_cuota',
										'OrdenDescuentoCuota.importe',
										'OrdenDescuentoCuota.situacion',
										'OrdenDescuentoCuota.tipo_orden_dto',
										'OrdenDescuentoCuota.tipo_producto',
										'OrdenDescuentoCuota.tipo_cuota',
										'OrdenDescuentoCuota.periodo',
										'OrdenDescuentoCuota.proveedor_id',
										'OrdenDescuentoCuota.vencimiento',
								),
								'order' => array('OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.orden_descuento_id'),
		));
	 	return $cuotas;
	}	
	
        
        
        
        /**
         * 
         * @param type $socio_id
         * @param type $periodo
         * @param type $organismo
         * @param type $situacion
         * @param type $preImputacion
         * @param type $proveedor_id
         * @param type $tipoFiltro (0 = TODO; 1 = SOLO PERIODO; 2 = SOLO MORA)
         * @return type
         */
	
	function cuotasAdeudadasBySocioAlPeriodoByOrganismo($socio_id,$periodo,$organismo,$situacion='MUTUSICUMUTU',$preImputacion = false,$proveedor_id = null,$tipoFiltro=0,$punitorio=0,$groupByOrden = FALSE){
        
        $preImputacion = TRUE;
        $sql = "SELECT 
                    OrdenDescuentoCuota.id,
                    OrdenDescuentoCuota.orden_descuento_id,
                    OrdenDescuentoCuota.socio_id,
                    OrdenDescuentoCuota.persona_beneficio_id,
                    OrdenDescuentoCuota.nro_cuota,
                    OrdenDescuentoCuota.importe,
                    OrdenDescuentoCuota.situacion,
                    OrdenDescuentoCuota.tipo_orden_dto,
                    OrdenDescuentoCuota.tipo_producto,
                    OrdenDescuentoCuota.tipo_cuota,
                    OrdenDescuentoCuota.periodo,
                    OrdenDescuentoCuota.proveedor_id,
                    OrdenDescuentoCuota.vencimiento,
                    PersonaBeneficio.codigo_beneficio,
                    PersonaBeneficio.acuerdo_debito,
                    OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
                    FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
                    WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                    AND cocu.orden_descuento_cobro_id = co.id
                    AND co.periodo_cobro <= '$periodo'),0)
                    - ifnull((select SUM(coc.importe) from cancelacion_ordenes co
						inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = co.id
						where coc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
						and co.estado = 'E' and co.fecha_vto >= date_format(now(), '%Y-%m-%d')),0)                        
                    ".($preImputacion ? " - ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc inner join liquidaciones l on l.id = lc.liquidacion_id
					WHERE l.periodo <= '$periodo' and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
					AND lc.orden_descuento_cobro_id = 0
					order by lc.liquidacion_id desc limit 1 ),0) " : " ")."
                    as saldo
                    ,PERIOD_DIFF('$periodo',OrdenDescuentoCuota.periodo) as periodos 
					,ifnull(OrdenDescuentoCuota.periodo,'$periodo') as periodo_min
					,ifnull(OrdenDescuentoCuota.periodo,'$periodo') as periodo_max
					,0 as punitorio                        
                FROM orden_descuento_cuotas AS OrdenDescuentoCuota
			    INNER JOIN socios Socio on Socio.id = OrdenDescuentoCuota.socio_id
                INNER JOIN orden_descuentos OrdenDescuento on OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id
                INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                WHERE 
                    OrdenDescuentoCuota.socio_id = $socio_id
                    AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,'$periodo'),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) >= '$periodo'
                    ".(!empty($proveedor_id) ? " AND OrdenDescuentoCuota.proveedor_id =  $proveedor_id " : "")."    
                    AND OrdenDescuentoCuota.estado NOT IN ('B','C') 
                    AND PersonaBeneficio.codigo_beneficio = '$organismo'
                    AND OrdenDescuentoCuota.periodo ".($tipoFiltro == 0 ? " <= " : ($tipoFiltro == 1 ? " = " : ($tipoFiltro == 2 ? " < " : " <= ") ))." '$periodo'
                    AND OrdenDescuentoCuota.situacion = '$situacion'
                    AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
                    FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
                    WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                    AND cocu.orden_descuento_cobro_id = co.id
                    AND co.periodo_cobro <= '$periodo'),0)  + ifnull((select SUM(coc.importe) from cancelacion_ordenes co
						inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = co.id
						where coc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
						and co.estado = 'E'),0) "
                . "" .( $preImputacion ? " + ifnull((SELECT ROUND(ifnull(SUM(lc.importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc inner join liquidaciones l on l.id = lc.liquidacion_id
					WHERE l.periodo <= '$periodo' and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
					AND lc.orden_descuento_cobro_id = 0
					order by lc.liquidacion_id desc limit 1 ),0)" : "");
        
        if($groupByOrden){
            
        $sql = "SELECT 
                    OrdenDescuentoCuota.orden_descuento_id,
                    OrdenDescuentoCuota.persona_beneficio_id,
                    OrdenDescuentoCuota.importe,
                    OrdenDescuentoCuota.tipo_orden_dto,
                    OrdenDescuentoCuota.tipo_producto,
                    OrdenDescuentoCuota.proveedor_id,
                    PersonaBeneficio.acuerdo_debito,
                    sum(OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
                    FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
                    WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                    AND cocu.orden_descuento_cobro_id = co.id
                    AND co.periodo_cobro <= '$periodo'),0)
                    - ifnull((select SUM(coc.importe) from cancelacion_ordenes co
						inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = co.id
						where coc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
						and co.estado = 'E' and co.fecha_vto >= date_format(now(), '%Y-%m-%d')),0)                        
                    ".($preImputacion ? " - ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc inner join liquidaciones l on l.id = lc.liquidacion_id
					WHERE l.periodo <= '$periodo' and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
					AND lc.orden_descuento_cobro_id = 0
					order by lc.liquidacion_id desc limit 1 ),0) " : " ")." )
                    as saldo
                    ,PERIOD_DIFF('$periodo',OrdenDescuentoCuota.periodo) as periodos
                    ,min(OrdenDescuentoCuota.periodo) as periodo_min
                    ,max(OrdenDescuentoCuota.periodo) as periodo_max                        
                    ,sum((OrdenDescuentoCuota.importe - IFNULL((SELECT SUM(cocu.importe)
                    FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
                    WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                    AND cocu.orden_descuento_cobro_id = co.id
                    AND co.periodo_cobro <= '$periodo'),0)
                    - ifnull((select SUM(coc.importe) from cancelacion_ordenes co
						inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = co.id
						where coc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
						and co.estado = 'E'),0)                        
                    ".($preImputacion ? " - ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc inner join liquidaciones l on l.id = lc.liquidacion_id
					WHERE l.periodo <= '$periodo' and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
					AND lc.orden_descuento_cobro_id = 0
					order by lc.liquidacion_id desc limit 1 ),0) " : " ")." ) 
                    * PERIOD_DIFF('$periodo',OrdenDescuentoCuota.periodo) * $punitorio) as punitorio                       
                FROM orden_descuento_cuotas AS OrdenDescuentoCuota
			    INNER JOIN socios Socio on Socio.id = OrdenDescuentoCuota.socio_id
                INNER JOIN orden_descuentos OrdenDescuento on OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id

                INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                WHERE 
                    OrdenDescuentoCuota.socio_id = $socio_id
                    AND IF(Socio.activo = 0,IFNULL(OrdenDescuento.periodo_hasta,'$periodo'),IF(ISNULL(OrdenDescuento.periodo_hasta) AND OrdenDescuento.activo = 1,'999999',OrdenDescuento.periodo_hasta)) >= '$periodo'
                    ".(!empty($proveedor_id) ? " AND OrdenDescuentoCuota.proveedor_id =  $proveedor_id " : "")."    
                    AND OrdenDescuentoCuota.estado NOT IN ('B','C') 
                    AND PersonaBeneficio.codigo_beneficio = '$organismo'
                    AND OrdenDescuentoCuota.periodo ".($tipoFiltro == 0 ? " <= " : ($tipoFiltro == 1 ? " = " : ($tipoFiltro == 2 ? " < " : " <= ") ))." '$periodo'
                    AND OrdenDescuentoCuota.situacion = '$situacion'
                    AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
                    FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
                    WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                    AND cocu.orden_descuento_cobro_id = co.id
                    AND co.periodo_cobro <= '$periodo'),0)  + ifnull((select SUM(coc.importe) from cancelacion_ordenes co
						inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = co.id
						where coc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
						and co.estado = 'E'),0) "
                . "" .( $preImputacion ? " + ifnull((SELECT ROUND(ifnull(SUM(lc.importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc inner join liquidaciones l on l.id = lc.liquidacion_id
					WHERE l.periodo <= '$periodo' and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
					AND lc.orden_descuento_cobro_id = 0
					order by lc.liquidacion_id desc limit 1 ),0)" : "") . " group by  OrdenDescuentoCuota.orden_descuento_id,OrdenDescuentoCuota.periodo,
                    OrdenDescuentoCuota.persona_beneficio_id,
                    OrdenDescuentoCuota.proveedor_id HAVING periodos > 0;";            
//            debug($sql);
            
        }
        
//        exit;
        $cuotas = $this->query($sql);        
        if(!empty($cuotas)){
            foreach($cuotas as $i => $cuota){
                
                $calculaPunitorio = parent::GlobalDato('concepto_4', $cuota['OrdenDescuentoCuota']['tipo_producto']);
                $calculaPunitorio = trim($calculaPunitorio);
//                $cuota[0]['punitorio'] = 0;
                
                $CALC_PUNITORIO = 0;
                
                if($calculaPunitorio === 'CALC_PUNITORIO'){
//                    debug($cuota['OrdenDescuentoCuota']['tipo_producto'] . " *** " . $punitorio . " *** " . $calculaPunitorio);
//                    $cuota[0]['punitorio'] = (!isset($cuota[0]['punitorio']) ? 0 : $cuota[0]['punitorio']);
                    $CALC_PUNITORIO = (!isset($cuota[0]['punitorio']) ? 0 : $cuota[0]['punitorio']);
//                    debug($cuota[0]['punitorio']);
                }
//                $cuota[0]['punitorio'] = (!isset($cuota[0]['punitorio']) ? 0 : $cuota[0]['punitorio']);
                
                $cuotas[$i]['OrdenDescuentoCuota']['saldo_calculado'] = $cuota[0]['saldo'];
                $cuotas[$i]['OrdenDescuentoCuota']['periodos'] = $cuota[0]['periodos'];
                $cuotas[$i]['OrdenDescuentoCuota']['periodo_min'] = $cuota[0]['periodo_min'];
                $cuotas[$i]['OrdenDescuentoCuota']['periodo_max'] = $cuota[0]['periodo_max'];
                
                $cuotas[$i]['OrdenDescuentoCuota']['punitorio'] = $punitorio;
//                $cuotas[$i]['OrdenDescuentoCuota']['punitorios'] = $cuota[0]['punitorio'];
                $cuotas[$i]['OrdenDescuentoCuota']['punitorios'] = $CALC_PUNITORIO;
                
                if($cuota['PersonaBeneficio']['acuerdo_debito'] == 0){
                    $cuotas[$i]['OrdenDescuentoCuota']['punitorios'] = $cuota[0]['punitorio'];
                }else{
                    $cuotas[$i]['OrdenDescuentoCuota']['punitorios'] = 0;
//                    $cuotas[$i]['OrdenDescuentoCuota']['saldo_calculado'] = 0;
                }
                $cuotas[$i]['OrdenDescuentoCuota']['acuerdo_debito'] = $cuota['PersonaBeneficio']['acuerdo_debito'];
                
                
            }
        }
//        debug($cuotas);
//        exit;
        return $cuotas;

//        ##############################################################################
//        #ADRIAN 09/10/2014 - LOS QUE TENGAN BANCO NACION SOLAMENTE MANDO EL PERIODO
//        ##############################################################################
//        $cuotas = array();
//        $file = parse_ini_file(CONFIGS.'mutual.ini', true);
//        if(isset($file['general']['banco_nacion_debito_periodo']) && $file['general']['banco_nacion_debito_periodo'] == 1){
//
//            $sql = "SELECT 
//                        OrdenDescuentoCuota.id,
//                        OrdenDescuentoCuota.orden_descuento_id,
//                        OrdenDescuentoCuota.socio_id,
//                        OrdenDescuentoCuota.persona_beneficio_id,
//                        OrdenDescuentoCuota.nro_cuota,
//                        OrdenDescuentoCuota.importe,
//                        OrdenDescuentoCuota.situacion,
//                        OrdenDescuentoCuota.tipo_orden_dto,
//                        OrdenDescuentoCuota.tipo_producto,
//                        OrdenDescuentoCuota.tipo_cuota,
//                        OrdenDescuentoCuota.periodo,
//                        OrdenDescuentoCuota.proveedor_id,
//                        OrdenDescuentoCuota.vencimiento,
//                        PersonaBeneficio.codigo_beneficio,
//                        0 as es_nacion 
//                    FROM orden_descuento_cuotas AS OrdenDescuentoCuota
//                    INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                    WHERE 
//                        OrdenDescuentoCuota.socio_id = $socio_id
//                        AND OrdenDescuentoCuota.estado <> 'B' 
//                        AND PersonaBeneficio.codigo_beneficio = '$organismo'
//                        AND OrdenDescuentoCuota.situacion = '$situacion'
//                        AND OrdenDescuentoCuota.periodo <= '$periodo'    
//                        AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
//                        FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
//                        WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//                        AND cocu.orden_descuento_cobro_id = co.id
//                        AND co.periodo_cobro <= '$periodo'),0) AND IFNULL(PersonaBeneficio.banco_id,'') <> '00011' 
//                    UNION 
//                    SELECT 
//                        OrdenDescuentoCuota.id,
//                        OrdenDescuentoCuota.orden_descuento_id,
//                        OrdenDescuentoCuota.socio_id,
//                        OrdenDescuentoCuota.persona_beneficio_id,
//                        OrdenDescuentoCuota.nro_cuota,
//                        OrdenDescuentoCuota.importe,
//                        OrdenDescuentoCuota.situacion,
//                        OrdenDescuentoCuota.tipo_orden_dto,
//                        OrdenDescuentoCuota.tipo_producto,
//                        OrdenDescuentoCuota.tipo_cuota,
//                        OrdenDescuentoCuota.periodo,
//                        OrdenDescuentoCuota.proveedor_id,
//                        OrdenDescuentoCuota.vencimiento,
//                        PersonaBeneficio.codigo_beneficio,
//                        1 as es_nacion 
//                    FROM orden_descuento_cuotas AS OrdenDescuentoCuota
//                    INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                    WHERE 
//                        OrdenDescuentoCuota.socio_id = $socio_id
//                        AND OrdenDescuentoCuota.estado <> 'B' 
//                        AND PersonaBeneficio.codigo_beneficio = '$organismo'
//                        AND OrdenDescuentoCuota.situacion = '$situacion'
//                        AND OrdenDescuentoCuota.periodo = '$periodo'    
//                        AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
//                        FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
//                        WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//                        AND cocu.orden_descuento_cobro_id = co.id
//                        AND co.periodo_cobro <= '$periodo'),0) AND IFNULL(PersonaBeneficio.banco_id,'') = '00011'"; 
//            
//            $datos = $this->query($sql);
//            $TOTAL_PERIODO_NACION = 0;
//            if(!empty($datos)){
//                foreach($datos as $ix => $dato){
//                    $cuotas[$ix]['OrdenDescuentoCuota']['id'] = $dato[0]['id'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['orden_descuento_id'] = $dato[0]['orden_descuento_id'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['socio_id'] = $dato[0]['socio_id'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['persona_beneficio_id'] = $dato[0]['persona_beneficio_id'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['nro_cuota'] = $dato[0]['nro_cuota'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['importe'] = $dato[0]['importe'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['situacion'] = $dato[0]['situacion'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['tipo_orden_dto'] = $dato[0]['tipo_orden_dto'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['tipo_producto'] = $dato[0]['tipo_producto'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['tipo_cuota'] = $dato[0]['tipo_cuota'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['periodo'] = $dato[0]['periodo'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['proveedor_id'] = $dato[0]['proveedor_id'];
//                    $cuotas[$ix]['OrdenDescuentoCuota']['vencimiento'] = $dato[0]['vencimiento'];
//                    $cuotas[$ix]['PersonaBeneficio']['codigo_beneficio'] = $dato[0]['codigo_beneficio'];
//                    
//                    if($dato[0]['es_nacion'] == 1 && $dato[0]['tipo_cuota'] != 'MUTUTCUOCSOC'){
//                        $TOTAL_PERIODO_NACION += $dato[0]['importe'];
//                    }
//                }
//            }
////            debug($cuotas);
//            
//            if($TOTAL_PERIODO_NACION == 0){
//                // no tiene nada del periodo, analizo la mora
//                /*****************************************************************/
//                /* ANALIZO POR ORDEN DE DESCUENTO*/
//                /*****************************************************************/
//                $sql = "SELECT 
//                        OrdenDescuentoCuota.orden_descuento_id
//                    FROM orden_descuento_cuotas AS OrdenDescuentoCuota
//                    INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                    WHERE 
//                        OrdenDescuentoCuota.socio_id = $socio_id
//                        AND OrdenDescuentoCuota.estado <> 'B' 
//                        AND PersonaBeneficio.codigo_beneficio = '$organismo'
//                        AND OrdenDescuentoCuota.situacion = '$situacion'
//                        AND OrdenDescuentoCuota.periodo < '$periodo'    
//                        AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
//                        FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
//                        WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//                        AND cocu.orden_descuento_cobro_id = co.id
//                        AND co.periodo_cobro <= '$periodo'),0) AND IFNULL(PersonaBeneficio.banco_id,'') = '00011' 
//                        GROUP BY OrdenDescuentoCuota.orden_descuento_id";
//                
//                $ordenes = $this->query($sql);
//                if(!empty($ordenes)){
//                    
//                    foreach($ordenes as $orden){
//                        
//                        $ordenId = $orden['OrdenDescuentoCuota']['orden_descuento_id'];
////                        debug($ordenId);
//                        
//                        $sql = "SELECT 
//                                    OrdenDescuentoCuota.id,
//                                    OrdenDescuentoCuota.orden_descuento_id,
//                                    OrdenDescuentoCuota.socio_id,
//                                    OrdenDescuentoCuota.persona_beneficio_id,
//                                    OrdenDescuentoCuota.nro_cuota,
//                                    OrdenDescuentoCuota.importe,
//                                    OrdenDescuentoCuota.situacion,
//                                    OrdenDescuentoCuota.tipo_orden_dto,
//                                    OrdenDescuentoCuota.tipo_producto,
//                                    OrdenDescuentoCuota.tipo_cuota,
//                                    OrdenDescuentoCuota.periodo,
//                                    OrdenDescuentoCuota.proveedor_id,
//                                    OrdenDescuentoCuota.vencimiento,
//                                    PersonaBeneficio.codigo_beneficio
//                                FROM orden_descuento_cuotas AS OrdenDescuentoCuota
//                                INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                                WHERE 
//                                    OrdenDescuentoCuota.socio_id = $socio_id
//                                    AND OrdenDescuentoCuota.estado <> 'B' 
//                                    AND PersonaBeneficio.codigo_beneficio = '$organismo'
//                                    AND OrdenDescuentoCuota.situacion = '$situacion'
//                                    AND OrdenDescuentoCuota.periodo < '$periodo'    
//                                    AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
//                                    FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
//                                    WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//                                    AND cocu.orden_descuento_cobro_id = co.id
//                                    AND co.periodo_cobro <= '$periodo'),0) AND IFNULL(PersonaBeneficio.banco_id,'') = '00011' 
//                                    AND OrdenDescuentoCuota.orden_descuento_id = $ordenId LIMIT 1;";
//                        $datos = $this->query($sql);
//                        if(!empty($datos)) array_push ($cuotas, $datos[0]);
//                        
//                    }
//                    
//                }
//                
//            }
////            debug($cuotas);
////            debug($TOTAL_PERIODO_NACION);
//            
//        }else{
//
//            $sql = "SELECT 
//                        OrdenDescuentoCuota.id,
//                        OrdenDescuentoCuota.orden_descuento_id,
//                        OrdenDescuentoCuota.socio_id,
//                        OrdenDescuentoCuota.persona_beneficio_id,
//                        OrdenDescuentoCuota.nro_cuota,
//                        OrdenDescuentoCuota.importe,
//                        OrdenDescuentoCuota.situacion,
//                        OrdenDescuentoCuota.tipo_orden_dto,
//                        OrdenDescuentoCuota.tipo_producto,
//                        OrdenDescuentoCuota.tipo_cuota,
//                        OrdenDescuentoCuota.periodo,
//                        OrdenDescuentoCuota.proveedor_id,
//                        OrdenDescuentoCuota.vencimiento,
//                        PersonaBeneficio.codigo_beneficio
//                    FROM orden_descuento_cuotas AS OrdenDescuentoCuota
//                    INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                    WHERE 
//                        OrdenDescuentoCuota.socio_id = $socio_id
//                        AND OrdenDescuentoCuota.estado <> 'B' 
//                        AND PersonaBeneficio.codigo_beneficio = '$organismo'
//                        AND OrdenDescuentoCuota.periodo <= '$periodo'
//                        AND OrdenDescuentoCuota.situacion = '$situacion'
//                        AND OrdenDescuentoCuota.importe > IFNULL((SELECT SUM(cocu.importe)
//                        FROM orden_descuento_cobro_cuotas cocu, orden_descuento_cobros co
//                        WHERE cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//                        AND cocu.orden_descuento_cobro_id = co.id
//                        AND co.periodo_cobro <= '$periodo'),0)";    
//            $cuotas = $this->query($sql);
//        }
//
//		
//	 	return $cuotas;
	}	
	
	/**
	 * Calcula el saldo de una cuota
	 * 
	 * @author adrian [07/02/2012]
	 * @param unknown_type $cuota
	 * @param unknown_type $periodo
	 * @param unknown_type $codigoOrganismo
	 */
	function __calculaSaldo($cuota,$periodo=null,$codigoOrganismo = null,$pre_imputacion=false){
		$hoy = date('Y-m-d');
		$pagado = 0;
		
		$periodo = (!empty($periodo) ? $periodo : date('Ym'));
		
		
// 		$sql = "SELECT f_calcula_saldo_cuota(".$cuota['OrdenDescuentoCuota']['id'].",'$periodo',$pre_imputacion) AS saldo;";
// 		$saldo = $this->query($sql);
// 		debug($saldo);
		
		App::import('model','Mutual.OrdenDescuentoCobroCuota');
		$oCOBRO = new OrdenDescuentoCobroCuota();
		$pagado = $oCOBRO->getMontoPagoByCuota($cuota['OrdenDescuentoCuota']['id'],$periodo);		
		
		if(abs($cuota['OrdenDescuentoCuota']['importe']) < abs($pagado)) $pagado = $cuota['OrdenDescuentoCuota']['importe'];
		
		$cuota['OrdenDescuentoCuota']['pagado'] = $pagado;
		
		if($this->isDeudaBySituacion($cuota['OrdenDescuentoCuota']['situacion']))$cuota['OrdenDescuentoCuota']['saldo_cuota'] = abs($cuota['OrdenDescuentoCuota']['importe']) - abs($pagado);
		else $cuota['OrdenDescuentoCuota']['saldo_cuota'] = 0;
		
		if($cuota['OrdenDescuentoCuota']['importe'] < 0) $cuota['OrdenDescuentoCuota']['saldo_cuota'] = $cuota['OrdenDescuentoCuota']['saldo_cuota'] * (-1);
		
//		if($cuota['OrdenDescuentoCuota']['vencimiento'] < $hoy && $cuota['OrdenDescuentoCuota']['saldo_cuota'] > 0)$cuota['OrdenDescuentoCuota']['vencida'] = 1;
//		else $cuota['OrdenDescuentoCuota']['vencida'] = 0;

		if($cuota['OrdenDescuentoCuota']['periodo'] < $periodo && $cuota['OrdenDescuentoCuota']['saldo_cuota'] > 0)$cuota['OrdenDescuentoCuota']['vencida'] = 1;
		else $cuota['OrdenDescuentoCuota']['vencida'] = 0;
		
		
		$cuota['OrdenDescuentoCuota']['periodo_desc'] = parent::periodo($cuota['OrdenDescuentoCuota']['periodo']);
		$cuota['OrdenDescuentoCuota']['pre_imputado'] = 0;
                
		if($pre_imputacion){
//			$sql = "SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
//					WHERE orden_descuento_cuota_id = ".$cuota['OrdenDescuentoCuota']['id']."
//					and ifnull(orden_descuento_cobro_id,0) = 0
//					order by liquidacion_id desc limit 1;";
                    $sql = "SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                            inner join liquidaciones l on l.id = lc.liquidacion_id
                            WHERE l.periodo <= '$periodo' and lc.orden_descuento_cuota_id = ".$cuota['OrdenDescuentoCuota']['id']."
                            and ifnull(lc.orden_descuento_cobro_id,0) = 0
                            order by lc.liquidacion_id desc limit 1;";
                    
			$datos = $this->query($sql);
// 			debug($datos[0][0]['importe_debitado']);
// 			$cuota['OrdenDescuentoCuota']['saldo_cuota'] = round($cuota['OrdenDescuentoCuota']['saldo_cuota'],2);
			$cuota['OrdenDescuentoCuota']['pre_imputado'] = (!empty($datos) && isset($datos[0][0]['importe_debitado']) && !empty($datos[0][0]['importe_debitado']) ? $datos[0][0]['importe_debitado'] : 0);
// 			$cuota['OrdenDescuentoCuota']['pre_imputado'] = number_format($cuota['OrdenDescuentoCuota']['pre_imputado'],2);
			$cuota['OrdenDescuentoCuota']['saldo_cuota'] = $cuota['OrdenDescuentoCuota']['saldo_cuota'] - $cuota['OrdenDescuentoCuota']['pre_imputado'];
// 			$cuota['OrdenDescuentoCuota']['saldo_cuota'] -= round($cuota['OrdenDescuentoCuota']['pre_imputado'],2);
// 			$cuota['OrdenDescuentoCuota']['saldo_cuota'] = round($cuota['OrdenDescuentoCuota']['saldo_cuota'],2);
			//echo $cuota['OrdenDescuentoCuota']['id']."\t".$cuota['OrdenDescuentoCuota']['pre_imputado']."\t".$cuota['OrdenDescuentoCuota']['saldo_cuota']."\n";

		}
                
                #######################################################################################
                # EL SALDO DE LA CUOTA SE VE AFECTADO SI ESTA DENTRO DE CANCELACIONES EMITIDAS
                #######################################################################################
                $sql = "select IFNULL(SUM(coc.importe),0) as importe_en_cancelacion from cancelacion_ordenes co
                        inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = co.id
                        where coc.orden_descuento_cuota_id = ".$cuota['OrdenDescuentoCuota']['id']."
                        and co.estado = 'E'";
                $datos = $this->query($sql);
                if(!empty($datos) && isset($datos[0][0]['importe_en_cancelacion'])){
                    $cuota['OrdenDescuentoCuota']['importe_en_cancelacion'] = $datos[0][0]['importe_en_cancelacion'];
                    $cuota['OrdenDescuentoCuota']['saldo_cuota'] = $cuota['OrdenDescuentoCuota']['saldo_cuota'] - $cuota['OrdenDescuentoCuota']['importe_en_cancelacion'];
                }                
                
                
                
// 		debug($cuota['OrdenDescuentoCuota']);
                
                
		return $cuota;
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see cake/libs/model/Model#afterFind($results, $primary)
	 */		
	function afterFind($resultados, $primary = true) {
		return $resultados;
	}
	
	/**
	 * migraGeneraCuotasPermanente
	 * @param $data
	 * @return unknown_type
	 */
	function migraGeneraCuotasPermanente($data){
		
		$inicio = $data['OrdenDescuento']['periodo_ini'];
		
		$mIni = substr($inicio,4,2);
		$yIni = substr($inicio,0,4);
		
		$mkIni = mktime(0,0,0,$mIni,1,$yIni);		
		$mkFin = mktime(0,0,0,date('m'),1,date('Y'));
		
		$pIni = $data['OrdenDescuento']['periodo_ini'];
		$pFin = date('Ym');

		$pAct = $pIni;
		$i = 0;
//		$mkAct = 0;
		
		while($pFin > $pAct){
			
			$ss = 60 * 60 * 24 * 31 * $i;
//			$mkAct = $mkIni + $ss;
			
			$pAct = date('Ym',($mkIni + $ss));
			
		}
		
		
	}	
	
	/**
	 * NO SE USA
	 * GeneraCuotasPermanente
	 * @param $data
	 * @return unknown_type
	 */
	function GeneraCuotasPermanente($data){
		
		$inicio = $data['OrdenDescuento']['periodo_ini'];
		
		$mIni = substr($inicio,4,2);
		$yIni = substr($inicio,0,4);
		
		$mkIni = mktime(0,0,0,$mIni,1,$yIni);		
		$mkFin = mktime(0,0,0,date('m'),1,date('Y'));
		
		$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($data['OrdenDescuento']['primer_vto_socio'])),date('d',strtotime($data['OrdenDescuento']['primer_vto_socio'])),date('Y',strtotime($data['OrdenDescuento']['primer_vto_socio'])));
		$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])),date('d',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])),date('Y',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])));
		
		$pIni = $data['OrdenDescuento']['periodo_ini'];
		$pFin = date('Ym');

		$pAct = $pIni;
		$i = 0;

		
		while($pFin > $pAct){
			
			$ss = 60 * 60 * 24 * 31 * $i;
//			$mkAct = $mkIni + $ss;
			
			
			$periodoActual = date('Ym',$this->addMonthToDate($mkIni,$i));
			$vtoSocio = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$i));
			$vtoProv = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$i));
			
			$pAct = date('Ym',($mkIni + $ss));
			$i++;
			
			$glb = $this->getGlobalDato('concepto_2',$data['OrdenDescuento']['tipo_producto']);
			
			$ret = $this->save(array('OrdenDescuentoCuota' => array(
						'orden_descuento_id' => $data['OrdenDescuento']['id'],
						'persona_beneficio_id' => $data['OrdenDescuento']['persona_beneficio_id'],
						'socio_id' => $data['OrdenDescuento']['socio_id'],
						'tipo_orden_dto' => $data['OrdenDescuento']['tipo_orden_dto'],
						'tipo_producto' => $data['OrdenDescuento']['tipo_producto'],
						'periodo' => $periodoActual,
						'nro_cuota' => $i,
						'tipo_cuota' => $glb['GlobalDato']['concepto_2'],
						'estado' => 'A',
						'situacion' => 'MUTUSICUMUTU',
						'importe' => $data['OrdenDescuento']['importe_cuota'],
						'proveedor_id' => $data['OrdenDescuento']['proveedor_id'],
						'vencimiento' => $vtoSocio,
						'vencimiento_proveedor' => $vtoProv
					)));
			$this->id = 0;			
			
		}
		
		
	}		
	
	/**
	 * generaCuotas
	 * @param $data
	 * @return unknown_type
	 */
	function generaCuotas($data){
		
		if($data['OrdenDescuento']['tipo_orden_dto'] == 'MUTUTPROCFIJ') return $this->GeneraCuotasPermanente($data);
		
		$inicio = $data['OrdenDescuento']['periodo_ini'];
		$segundosPlan = $data['OrdenDescuento']['cuotas'] * 30 * 24 * 60 * 60;
		
		$mIni = substr($inicio,4,2);
		$yIni = substr($inicio,0,4);
		
		$mkIni = mktime(0,0,0,$mIni,1,$yIni);
//		$mkFin = $mkIni  + $segundosPlan;
		
		//SACO EL PROVEEDOR
		
		$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($data['OrdenDescuento']['primer_vto_socio'])),date('d',strtotime($data['OrdenDescuento']['primer_vto_socio'])),date('Y',strtotime($data['OrdenDescuento']['primer_vto_socio'])));
		$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])),date('d',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])),date('Y',strtotime($data['OrdenDescuento']['primer_vto_proveedor'])));
		
		
//		App::import('Model', 'Mutual.MutualProductoSolicitud');
//		$this->MutualProductoSolicitud = new MutualProductoSolicitud(null);
//
//		$producto = $this->MutualProductoSolicitud->read(null,$data['OrdenDescuento']['numero']);
		
		$i = 0;
		$cuota = 1;
		
		$impoCuota = number_format($data['OrdenDescuento']['importe_cuota'],2);
		
		$impoTotal = $impoCuota * $data['OrdenDescuento']['cuotas'];
		$impoOC = $data['OrdenDescuento']['importe_total'];
		
		$diff = $impoOC - $impoTotal;
		
		for($i;$i < $data['OrdenDescuento']['cuotas'];$i++){
			
			$ss = 60 * 60 * 24 * 31 * $i;
			$mkFin = $mkIni  + $ss;
			
			$periodoActual = date('Ym',$this->addMonthToDate($mkIni,$i));
			$vtoSocio = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$i));
			$vtoProv = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$i));
			
			$importeCuota = $data['OrdenDescuento']['importe_cuota'];
			if($cuota == $data['OrdenDescuento']['cuotas']) $importeCuota += $diff;
			
			$glb = $this->getGlobalDato('concepto_2',$data['OrdenDescuento']['tipo_producto']);
			
			// grabo la cuota del consumo
			$ret = $this->save(array('OrdenDescuentoCuota' => array(
						'orden_descuento_id' => $data['OrdenDescuento']['id'],
						'persona_beneficio_id' => $data['OrdenDescuento']['persona_beneficio_id'],
						'socio_id' => $data['OrdenDescuento']['socio_id'],
						'tipo_orden_dto' => $data['OrdenDescuento']['tipo_orden_dto'],
						'tipo_producto' => $data['OrdenDescuento']['tipo_producto'],
						'periodo' => date('Ym',$mkFin),
						'nro_cuota' => $cuota,
						'tipo_cuota' => $glb['GlobalDato']['concepto_2'],
						'estado' => 'A',	
						'situacion' => 'MUTUSICUMUTU',
						'importe' => $importeCuota,
						'proveedor_id' => $data['OrdenDescuento']['proveedor_id'],
						'vencimiento' => $vtoSocio,
						'vencimiento_proveedor' => $vtoProv,
						'nro_referencia_proveedor' => $data['OrdenDescuento']['nro_referencia_proveedor'],			
					)));
			$this->id = 0;
			
			$cuota++;
		}	
		
	}
	
	function marcarPagada($cuota_id){
		$this->id = $cuota_id;
		//calcular saldo
		if($this->getSaldo($cuota_id) <= 0){
			$cuota = $this->read(null,$cuota_id);
			$cuota['OrdenDescuentoCuota']['estado'] = 'P';
			return parent::save($cuota);
		}else{
			return true;
		}
	}
	
	/**
	 * devuelve el saldo de una cuota
	 * toma el importe devengado de la cuota y le resta todos los pagos que tiene al momento.
	 * @param int $cuota_id
	 * @return decimal saldo
	 */
	function getSaldo($cuota_id, $periodoCorte = null){
		$saldo = 0;
		$pagado = 0;
		$this->bindModel(array('hasMany' => array('OrdenDescuentoCobroCuota')));
		$cuota = $this->read(null,$cuota_id);
                if($cuota['OrdenDescuentoCuota']['estado'] == 'B') {
                    return 0;
                }
		$saldo = abs($cuota['OrdenDescuentoCuota']['importe']);
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oPago = new OrdenDescuentoCobroCuota();		
		$pagado = $oPago->getMontoPagoByCuota($cuota['OrdenDescuentoCuota']['id'], $periodoCorte);			
		$saldo -= abs($pagado);
		return $saldo;			
	}
	
	/**
	 * CAMBIO DE ESTADO DE UNA CUOTA
	 * 
	 * Si $cuota_id es un entero modifica el estado de esa cuota en particular.  
	 * Si $cuota_id es un array del tipo array(id1,id2,id3...,idn) modifica el estado de todas esas cuotas
	 * 
	 * @author adrian [28/03/2012]
	 * @param mixed $cuota_id
	 * @param string $estado
	 */
	function cambiarEstado($cuota_id,$estado){
//		$cuota = $this->read(null,$cuota_id);
//		$cuota['OrdenDescuentoCuota']['estado'] = $estado;
//		return $this->save($cuota);
		$this->unbindModel(array('belongsTo' => array('OrdenDescuento','Proveedor','Socio')));
		return $this->updateAll(array("OrdenDescuentoCuota.estado" => "'$estado'"),array('OrdenDescuentoCuota.id' => $cuota_id));		
	}	
	
	function generarNotaCreditoPorCancelacion($orden_descuento_id,$importe,$fecha,$tipoCuota='MUTUTCUONCRC'){
		
		$ordenDto = $this->OrdenDescuento->read(null,$orden_descuento_id);
		
		$tipoCuota = (empty($tipoCuota) ? 'MUTUTCUONCRC' : $tipoCuota);
		
		$nc = array('OrdenDescuentoCuota' => array(
						'orden_descuento_id' => $orden_descuento_id,
						'persona_beneficio_id' => $ordenDto['OrdenDescuento']['persona_beneficio_id'],
						'socio_id' => $ordenDto['OrdenDescuento']['socio_id'],
						'tipo_orden_dto' => $ordenDto['OrdenDescuento']['tipo_orden_dto'],
						'tipo_producto' => $ordenDto['OrdenDescuento']['tipo_producto'],
						'periodo' => date('Ym',strtotime($fecha)),
						'nro_cuota' => 0,
						'tipo_cuota' => $tipoCuota,
						'estado' => 'P',	
						'situacion' => 'MUTUSICUMUTU',
						'importe' => $importe,
						'proveedor_id' => $ordenDto['OrdenDescuento']['proveedor_id'],
						'vencimiento' => $fecha,
						'vencimiento_proveedor' => $fecha,
						'nro_referencia_proveedor' => $ordenDto['OrdenDescuento']['nro_referencia_proveedor'],			
					));
		return parent::save($nc);
	}
	
	function getProveedor($id){
		$cuota = $this->read(null,$id);
		return $cuota['OrdenDescuentoCuota']['proveedor_id'];
	}
	
	
	function cambiarSituacionCuota($id,$situacion,$observaciones,$moverAlPeriodo=null){
		$cuota = $this->read(null,$id);
		if($cuota['OrdenDescuentoCuota']['situacion'] != $situacion) $cuota['OrdenDescuentoCuota']['situacion'] = $situacion;
		if(!empty($observaciones))$cuota['OrdenDescuentoCuota']['observaciones'] .= '<br/>' . $observaciones;
		if($this->isDeudaBySituacion($cuota['OrdenDescuentoCuota']['situacion'])){
			$cuota['OrdenDescuentoCuota']['estado'] = 'A';
			if(!$cuota['OrdenDescuento']['permanente']){
				App::import('Model','Mutual.OrdenDescuento');
				$oORDEN = new OrdenDescuento(); 
				$status = $oORDEN->activarOrden($cuota['OrdenDescuentoCuota']['orden_descuento_id'],array($id));						
			}
		}else {
			$cuota['OrdenDescuentoCuota']['estado'] = 'B';
		}
		if(!empty($moverAlPeriodo))  $cuota['OrdenDescuentoCuota']['periodo'] = $moverAlPeriodo;
		return parent::save($cuota);
	}
	
	
	function cambiarSituacionCuotas($cuotas,$situacion,$observacion,$socio_id=null,$moverAlPeriodo=null){
		$status = true;
		foreach($cuotas as $cuota_id => $sel){
			if(!$this->cambiarSituacionCuota($cuota_id,$situacion,$observacion,$moverAlPeriodo)){
				$status = false;
				break;
			}
		}

		#RECALIFICO AL SOCIO
//		if($status && !empty($socio_id)){
//			$calificacion = parent::getGlobalDato('concepto_2',$situacion);
//			$calificacion = $calificacion['GlobalDato']['concepto_2'];
//			if(!empty($calificacion)){
//				App::import('Model','Pfyj.SocioCalificacion');
//				$obj = new SocioCalificacion();
//				$obj->calificar($socio_id,$calificacion);				
//			}
//		}
		
		return $status;
	}
	
	
	function resumenDeDeuda($socio_id){
		$resumen = array();
		$cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id,'OrdenDescuentoCuota.estado' => 'A')));
		$cuotas = $this->armaInfoAdicional($cuotas);
		
		//saco las distintas situaciones
		App::import('Model','Config.GlobalDato');
		$oGlb = new GlobalDato();
		
		$situaciones = $oGlb->find('list',array('conditions' => array('GlobalDato.id LIKE ' => 'MUTUSICU' . '%', 'GlobalDato.id <> ' => 'MUTUSICU'),'fields' => array('concepto_1'),'order' => array('GlobalDato.id')));
		foreach($situaciones as $situacion => $descripcion){
			
			$cuotasBySituacionVencidas = Set::extract('/OrdenDescuentoCuota[situacion='.$situacion.'][vencida=1]',$cuotas);
			$cuotasBySituacionNoVencida = Set::extract('/OrdenDescuentoCuota[situacion='.$situacion.'][vencida=0]',$cuotas);
			
			$totalVencido = 0;
			if(count($cuotasBySituacionVencidas) != 0){
				foreach($cuotasBySituacionVencidas as $cuota){
					$totalVencido += $cuota['OrdenDescuentoCuota']['importe'] - $cuota['OrdenDescuentoCuota']['pagado'];
				}
			}
			
			$totalAVencer = 0;
			if(count($cuotasBySituacionNoVencida) != 0){
				foreach($cuotasBySituacionNoVencida as $cuota){
					$totalAVencer += $cuota['OrdenDescuentoCuota']['importe'] - $cuota['OrdenDescuentoCuota']['pagado'];
				}			
			}

			$resumen[$situacion] = array(
											'descripcion_situacion' => $descripcion,
											'total_adeudado_vencido' => $totalVencido,
											'total_adeudado_avencer' => $totalAVencer,
										
			);
			
		}
		
		return $resumen;
		
	}
	
	
	function isDeudaBySituacion($situacion){
		$glb = parent::getGlobalDato('logico_2',$situacion);
		if($glb['GlobalDato']['logico_2'] == 1) return true;
		else return false;
	}
	
	
	function grabarNuevaCuota($data){
		$this->id = 0;
		$cuota = array('OrdenDescuentoCuota' => array(
						'orden_descuento_id' => $data['orden_descuento_id'],
						'socio_id' => $data['socio_id'],
						'tipo_orden_dto' => $data['tipo_orden_dto'],
						'tipo_producto' => $data['tipo_producto'],
						'periodo' => $data['periodo'],
						'nro_cuota' =>  $data['nro_cuota'],
						'tipo_cuota' => $data['tipo_cuota'],
						'estado' => 'A',	
						'situacion' => 'MUTUSICUMUTU',
						'importe' => $data['importe'],
						'proveedor_id' => $data['proveedor_id'],
						'vencimiento' => $data['vencimiento'],
						'vencimiento_proveedor' => $data['vencimiento_proveedor'],
						'nro_referencia_proveedor' => $data['nro_referencia_proveedor'],			
					));
		return parent::save($cuota);		
	}
	
	/**
	 * Baja por Socio
	 * Da de baja las cuotas de un socio.
	 * @param integer $socio_id  id del socio
	 * @param string $causaBaja codigo global del motivo de la baja
	 * @param string $periodoBaja periodo a partir del cual se bajan las cuotas
	 * @param boolean $bajarDeudaCompleta
	 */
	function bajaBySocio($socio_id,$causaBaja,$periodoBaja=null,$bajarDeudaCompleta=false){
		$status = true;
		$conditions = array();
		
		$conditions['OrdenDescuentoCuota.socio_id'] = $socio_id;
		$conditions['OrdenDescuentoCuota.estado'] = 'A';
		if(!empty($periodoBaja) && !$bajarDeudaCompleta) $conditions['OrdenDescuentoCuota.periodo >='] = $periodoBaja;
		
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$cuotas = $this->find('all',array('conditions' => $conditions,'fields' => array('OrdenDescuentoCuota.id,OrdenDescuentoCuota.socio_id,OrdenDescuentoCuota.periodo,OrdenDescuentoCuota.observaciones')));
		
		$glb = parent::getGlobalDato('concepto_1,concepto_2',$causaBaja);
		$situacionCuota = $glb['GlobalDato']['concepto_2'];
		$obs = $glb['GlobalDato']['concepto_1'];
		
		foreach($cuotas as $cuota){
			
			$cuota['OrdenDescuentoCuota']['estado'] = 'B';
			$cuota['OrdenDescuentoCuota']['situacion'] = $situacionCuota;
			if(!empty($periodoBaja) && !$bajarDeudaCompleta):
				$cuota['OrdenDescuentoCuota']['periodo_origen'] = $cuota['OrdenDescuentoCuota']['periodo'];
				$cuota['OrdenDescuentoCuota']['periodo'] = $periodoBaja;
			endif;
			
			$cuota['OrdenDescuentoCuota']['observaciones'] = $cuota['OrdenDescuentoCuota']['observaciones']."<br/>".$obs;
			
			if(!$this->save($cuota)){
				$status = false;
				break;
			}
			
//			$this->id = $cuota['OrdenDescuentoCuota']['id'];
//			if(!$this->saveField('estado','B')) $status = false;
//			if(!$this->saveField('situacion',$situacionCuota)) $status = false;;
//			if(!empty($periodoBaja)){
//				if(!$this->saveField('periodo_origen',$cuota['OrdenDescuentoCuota']['perido'])) $status = false;;
//				if(!$this->saveField('perido',$periodoBaja)) $status = false;
//			}
//			if(!$this->saveField('observaciones',$cuota['OrdenDescuentoCuota']['observaciones']."<br/>".$obs)) $status = false;
//			if(!$status) break;
			
		}
		return $status;
	}
	
	function bajaByPersonaBeneficio($persona_beneficio_id,$causaBajaBeneficio){
		
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota')));
		$cuotas = $this->find('all',array('conditions' => array('OrdenDescuentoCuota.persona_beneficio_id' => $persona_beneficio_id,'OrdenDescuentoCuota.estado' => 'A'),'fields' => array('OrdenDescuentoCuota.id,OrdenDescuentoCuota.socio_id')));
		
		$glb = parent::getGlobalDato('concepto_1,concepto_2',$causaBajaBeneficio);
		$situacionCuota = $glb['GlobalDato']['concepto_2'];
		$obs = $glb['GlobalDato']['concepto_1'];
		
		$status = true;
		$socio_id = null;
		
		foreach($cuotas as $cuota){
			$socio_id = $cuota['OrdenDescuentoCuota']['socio_id'];
			if(!$this->cambiarSituacionCuota($cuota['OrdenDescuentoCuota']['id'],$situacionCuota,$obs)){
				$status = false;
				break;
			}			
		}
		
		if($status && !empty($socio_id)){
			$calificacion = parent::getGlobalDato('concepto_2',$situacionCuota);
			$calificacion = $calificacion['GlobalDato']['concepto_2'];
			if(!empty($calificacion)){
				App::import('Model','Pfyj.SocioCalificacion');
				$obj = new SocioCalificacion();
				$obj->calificar($socio_id,$calificacion);				
			}
		}		
	}
	
	
	function getTotalDeudaBySocioByBeneficioAlPeriodo($socio_id,$persona_beneficio_id,$periodo){
		$sql = "	select 
						socio_id,persona_beneficio_id,
						SUM(importe - ifnull((select sum(importe)from orden_descuento_cobro_cuotas
						where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
						as deuda
					from 
						orden_descuento_cuotas
					where 
						socio_id = $socio_id and persona_beneficio_id = $persona_beneficio_id and
						periodo <= '$periodo' and estado = 'A' and situacion = 'MUTUSICUMUTU' 
						group by socio_id,persona_beneficio_id";
		$datos = $this->query($sql);
		if(isset($datos[0][0]['deuda'])) return $datos[0][0]['deuda'];
		else return 0;
	}
	
	function getTotalDeudaBySocioAlPeriodo($socio_id,$periodo){
		$sql = "	select 
						socio_id,
						SUM(importe - ifnull((select sum(importe)from orden_descuento_cobro_cuotas
						where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
						as deuda
					from 
						orden_descuento_cuotas
					where 
						socio_id = $socio_id and periodo <= '$periodo' and estado = 'A' and situacion = 'MUTUSICUMUTU' 
						group by socio_id";
		$datos = $this->query($sql);
		if(isset($datos[0][0]['deuda'])) return $datos[0][0]['deuda'];
		else return 0;
	}

	/**
	 * Devuelve el total de deuda de un socio para un periodo, puede excluir o no la cuota social del total
	 * @param $socio_id
	 * @param $periodoCorte
	 * @param $excluyeCuotaSocial
	 * @return unknown_type
	 */
	function getTotalDeudaBySocio($socio_id,$periodoCorte=null,$excluyeCuotaSocial=false,$proveedorId = false){
		$periodoCorte = (!empty($periodoCorte) ? $periodoCorte : date('Ym'));
//		$sql = "	select 
//						socio_id,
//						SUM(ABS(importe) - ifnull((select sum(ABS(importe))from orden_descuento_cobro_cuotas
//						where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id
//                                                ),0)
//                                                - ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc 
//                                                inner join liquidaciones l on l.id = lc.liquidacion_id
//                                                WHERE l.periodo <= '$periodoCorte' and lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
//                                                AND lc.orden_descuento_cobro_id = 0
//                                                order by lc.liquidacion_id desc limit 1 ),0)
//                                                )
//						as deuda
//					from 
//						orden_descuento_cuotas
//					where 
//						socio_id = $socio_id and estado <> 'B' 
//                                                and importe > (ifnull((select sum(ABS(importe))from orden_descuento_cobro_cuotas
//                                                    where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
//						AND periodo <= '$periodoCorte' ".($excluyeCuotaSocial ? " AND tipo_cuota <> 'MUTUTCUOCSOC'" : "")."
//                        ". ($proveedorId ? " AND proveedor_id IN (SELECT id FROM proveedores WHERE genera_cuota_social = 1 AND id <> 18)" : " ") ."    
//						group by socio_id";
//						

                $sql = "select 
                        socio_id,
                        sum(importe) - ((select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)) as deuda,
                        count(*) as cantidad
                        from 
                            orden_descuento_cuotas
                        where 
                        socio_id = $socio_id and estado NOT IN ('B','D')
                        ". ( $excluyeCuotaSocial ? " and tipo_cuota <> 'MUTUTCUOCSOC' " : " " ) ."  
                        ". ( $proveedorId ? " AND proveedor_id IN (SELECT id FROM proveedores WHERE genera_cuota_social = 1 AND id <> 18)" : " " ) ."    
                        AND periodo <= '$periodoCorte'
                        and importe > (select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)
                        group by socio_id;";                
                
//        debug($sql);
		$datos = $this->query($sql);
		if(isset($datos[0][0]['deuda']) && $datos[0][0]['deuda'] > 0) return $datos[0][0]['deuda'];
		else return 0;
	}
        
	function getTotalDeudaVencidaBySocio($socio_id,$periodoCorte=null,$excluyeCuotaSocial=false,$proveedorId = false){
		$periodoCorte = (!empty($periodoCorte) ? $periodoCorte : date('Ym'));
                $sql = "select 
                        socio_id,
                        sum(importe) - ((select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)) as deuda,
                        count(*) as cantidad
                        from 
                            orden_descuento_cuotas
                        where 
                        socio_id = $socio_id and estado NOT IN ('B','D')
                        ". ( $excluyeCuotaSocial ? " and tipo_cuota <> 'MUTUTCUOCSOC' " : " " ) ."  
                        ". ( $proveedorId ? " AND proveedor_id IN (SELECT id FROM proveedores WHERE genera_cuota_social = 1 AND id <> 18)" : " " ) ."    
                        AND periodo < '$periodoCorte'
                        and importe > (select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)
                        group by socio_id;";                
		$datos = $this->query($sql);
		if(isset($datos[0][0]['deuda']) && $datos[0][0]['deuda'] > 0) return $datos[0][0]['deuda'];
		else return 0;
	} 
        
	function getTotalDeudaNoVencidaBySocio($socio_id,$periodoCorte=null,$excluyeCuotaSocial=false,$proveedorId = false){
		$periodoCorte = (!empty($periodoCorte) ? $periodoCorte : date('Ym'));
                $sql = "select 
                        socio_id,
                        sum(importe) - ((select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)) as deuda,
                        count(*) as cantidad
                        from 
                            orden_descuento_cuotas
                        where 
                        socio_id = $socio_id and estado NOT IN ('B','D')
                        ". ( $excluyeCuotaSocial ? " and tipo_cuota <> 'MUTUTCUOCSOC' " : " " ) ."  
                        ". ( $proveedorId ? " AND proveedor_id IN (SELECT id FROM proveedores WHERE genera_cuota_social = 1 AND id <> 18)" : " " ) ."    
                        AND periodo = '$periodoCorte'
                        and importe > (select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)
                        group by socio_id;";                
		$datos = $this->query($sql);
		if(isset($datos[0][0]['deuda']) && $datos[0][0]['deuda'] > 0) return $datos[0][0]['deuda'];
		else return 0;
	}         
    
	function getTotalDeudaAVencerBySocio($socio_id,$periodoCorte=null,$excluyeCuotaSocial=false,$proveedorId = false){
		$periodoCorte = (!empty($periodoCorte) ? $periodoCorte : date('Ym'));
                $sql = "select 
                        socio_id,
                        sum(importe) - ((select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)) as deuda,
                        count(*) as cantidad
                        from 
                            orden_descuento_cuotas
                        where 
                        socio_id = $socio_id and estado NOT IN ('B','D')
                        ". ( $excluyeCuotaSocial ? " and tipo_cuota <> 'MUTUTCUOCSOC' " : " " ) ."  
                        ". ( $proveedorId ? " AND proveedor_id IN (SELECT id FROM proveedores WHERE genera_cuota_social = 1 AND id <> 18)" : " " ) ."    
                        AND periodo > '$periodoCorte'
                        and importe > (select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)
                        group by socio_id;";                
		$datos = $this->query($sql);
		if(empty($datos)){return 0;}
		if(isset($datos[0][0]['deuda']) && $datos[0][0]['deuda'] > 0) return $datos[0][0]['deuda'];
		else return 0;
	}         

	/**
	 * Devuelve un array con el total y cantidad de cuotas sociales adeudadas de un socio para un periodo
	 * @param unknown_type $socio_id
	 * @param unknown_type $periodoCorte
	 * @return unknown_type
	 */
	function getTotalCuotaSocialAdeudadaBySocio($socio_id,$periodoCorte=null){
		$periodoCorte = (!empty($periodoCorte) ? $periodoCorte : date('Ym'));
//		$sql = "	select 
//						socio_id,
//						SUM(ABS(importe) - ifnull((select sum(ABS(importe))from orden_descuento_cobro_cuotas
//						where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))
//                                                - ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc 
//                                                inner join liquidaciones l on l.id = lc.liquidacion_id
//                                                WHERE l.periodo <= '$periodoCorte' and lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
//                                                AND lc.orden_descuento_cobro_id = 0 AND lc.tipo_cuota = 'MUTUTCUOCSOC'
//                                                order by lc.liquidacion_id desc limit 1 ),0)
//                                                )                                                
//						as deuda,
//						count(*) as cantidad
//					from 
//						orden_descuento_cuotas
//					where 
//						socio_id = $socio_id and estado <> 'B' and tipo_cuota = 'MUTUTCUOCSOC'
//						AND periodo <= '$periodoCorte' 
//                                                and importe > (ifnull((select sum(ABS(importe))from orden_descuento_cobro_cuotas
//                                                    where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id),0))                                                    
//						group by socio_id";
                $sql = "select 
                        socio_id,
                        sum(importe) - ((select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)) as deuda,
                        count(*) as cantidad
                        from 
                                orden_descuento_cuotas
                        where 
                        socio_id = $socio_id and estado NOT IN ('B','D') and tipo_cuota = 'MUTUTCUOCSOC'
                        AND periodo <= '$periodoCorte'
                        and importe > (select ifnull(sum(cocu.importe),0) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where co.periodo_cobro <= '$periodoCorte' and cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id)
                        + (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc 
                        where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id
                        and lc.orden_descuento_cobro_id = 0)
                        group by socio_id;";
		$datos = $this->query($sql);
		$total = array();
		$total[0] = 0;
		$total[1] = 0;
		if(isset($datos[0][0]['deuda']) && $datos[0][0]['deuda'] > 0):
			$total[0] = $datos[0][0]['deuda'];
			$total[1] = $datos[0][0]['cantidad'];
		endif;
		return $total;		
	}

	/**
	 * devueve los proveedores de las cuotas adeudadas de una orden de descuento
	 * @param $orden_descuento_id
	 */
	function getProveedoresCuotasAdeudadasByOrdenDto($orden_descuento_id){
		$this->unbindModel(array('belongsTo' => array('Socio','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$proveedores = $this->find('all',array(
											'conditions' => array('OrdenDescuentoCuota.orden_descuento_id' => $orden_descuento_id,'OrdenDescuentoCuota.estado ' => 'A'),
											'fields' => array('OrdenDescuentoCuota.proveedor_id'),
											'group' => array('OrdenDescuentoCuota.proveedor_id'),
											)
		);
		return $proveedores;
	}

	/**
	 * devuelve los proveedores de las cuotas de una orden de descuento
	 * @param $orden_descuento_id
	 */
	function getProveedoresCuotasByOrdenDto($orden_descuento_id){
		$this->unbindModel(array('belongsTo' => array('Socio','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$proveedores = $this->find('all',array(
											'conditions' => array('OrdenDescuentoCuota.orden_descuento_id' => $orden_descuento_id),
											'fields' => array('OrdenDescuentoCuota.proveedor_id'),
											'group' => array('OrdenDescuentoCuota.proveedor_id'),
											)
		);
		return $proveedores;
	}

	/**
	 * Devuelve los proveedores que tienen vinculacion con un socio mediante el estado de cuenta
	 * @param unknown_type $socio_id
	 * @return unknown_type
	 */
	function getProveedoresBySocio($socio_id){
		$proveedores = array();
		$this->unbindModel(array('belongsTo' => array('Socio','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$this->bindModel(array('belongsTo' => array('Proveedor')));
		$datos = $this->find('all',array(
											'conditions' => array('OrdenDescuentoCuota.socio_id' => $socio_id),
											'fields' => array('Proveedor.id','Proveedor.razon_social'),
											'group' => array('Proveedor.id','Proveedor.razon_social'),
											)
		);
		if(empty($datos)) return null;
		foreach($datos as $proveedor):
			$proveedores[$proveedor['Proveedor']['id']] = $proveedor['Proveedor']['razon_social'];
		endforeach;
		
		return $proveedores;		
	}
	
	/**
	 * arma el array de cuotas con los vencimientos e importes para una orden de descuento dada
	 * @param unknown_type $OrdenDto
	 * @param unknown_type $situacion
	 * @param unknown_type $estado
	 * @return unknown_type
	 */
	function armaCuotas($OrdenDto,$situacion="MUTUSICUMUTU",$estado="A"){
		$nroCuota = 1;
		$cuota = array();
		$cuotas = array();
		$inicio = $OrdenDto['OrdenDescuento']['periodo_ini'];
		$mIni = substr($inicio,4,2);
		$yIni = substr($inicio,0,4);
		$mkIni = mktime(0,0,0,$mIni,1,$yIni);	
		$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($OrdenDto['OrdenDescuento']['primer_vto_socio'])),date('d',strtotime($OrdenDto['OrdenDescuento']['primer_vto_socio'])),date('Y',strtotime($OrdenDto['OrdenDescuento']['primer_vto_socio'])));
		$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($OrdenDto['OrdenDescuento']['primer_vto_proveedor'])),date('d',strtotime($OrdenDto['OrdenDescuento']['primer_vto_proveedor'])),date('Y',strtotime($OrdenDto['OrdenDescuento']['primer_vto_proveedor'])));
		
		$i = 0;
		
		$glb = $this->getGlobalDato('concepto_2',$OrdenDto['OrdenDescuento']['tipo_producto']);
		
		for($nroCuota=1;$nroCuota <= $OrdenDto['OrdenDescuento']['cuotas']; $nroCuota++){
			
				$periodoCuota = date('Ym',$this->addMonthToDate($mkIni,$i));
				$vtoSocio = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$i));
				$vtoProv = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$i));
				
			
				$cuota['persona_beneficio_id'] = $OrdenDto['OrdenDescuento']['persona_beneficio_id'];
				$cuota['socio_id'] = $OrdenDto['OrdenDescuento']['socio_id'];
				$cuota['tipo_orden_dto'] = $OrdenDto['OrdenDescuento']['tipo_orden_dto'];
				$cuota['tipo_producto'] = $OrdenDto['OrdenDescuento']['tipo_producto'];
				$cuota['periodo'] = $periodoCuota;
				$cuota['nro_cuota'] = $nroCuota;
				$cuota['tipo_cuota'] = $glb['GlobalDato']['concepto_2'];
				$cuota['situacion'] = $situacion;
				$cuota['estado'] = $estado;
				$cuota['importe'] = $OrdenDto['OrdenDescuento']['importe_cuota'];
				$cuota['proveedor_id'] = $OrdenDto['OrdenDescuento']['proveedor_id'];
				$cuota['vencimiento'] = $vtoSocio;
				$cuota['vencimiento_proveedor'] = $vtoProv;	
				$cuota['nro_referencia_proveedor'] = (isset($OrdenDto['OrdenDescuento']['nro_referencia_proveedor']) ? $OrdenDto['OrdenDescuento']['nro_referencia_proveedor'] : "");			
				array_push($cuotas,$cuota);
				$i++;
		}
		return $cuotas;
	}
	
	/**
	 * marca una cuota como pagada totalmente
	 * @param unknown_type $id
	 */
	function setPagoTotal($id){
		$cuota = $this->read(null,$id);
		$cuota['OrdenDescuentoCuota']['estado'] = 'P';
		return $this->save($cuota);
	}
	
	
	function modificaBeneficio($id,$persona_beneficio_id){
		$cuota = $this->read(null,$id);
		$cuota['OrdenDescuentoCuota']['persona_beneficio_id'] = $persona_beneficio_id;
		return $this->save($cuota);		
	}
	
	
	function generaCuotaSocial($ordenDescuento,$periodo,$soloSiTieneConsumo = false){
		$cuota_social = 0;
		$socio_id = $ordenDescuento['OrdenDescuento']['socio_id'];
                $proveedor_id = $ordenDescuento['OrdenDescuento']['proveedor_id'];
                $tipo_producto = $ordenDescuento['OrdenDescuento']['tipo_producto'];
		$codigo_organismo = $ordenDescuento['PersonaBeneficio']['codigo_beneficio'];
                $codigo_empresa = $ordenDescuento['PersonaBeneficio']['codigo_empresa'];
		$codigo = 'MUTUCUOS' . substr($codigo_organismo,8,4);
		$glb = parent::getGlobalDato('decimal_1',$codigo);

		// $cuotaSocialGeneral = (!empty($glb) ? $glb['GlobalDato']['decimal_1'] : 0);

		// si es anses traer el importe de la cabecera de la orden de descuento
                // SI TIENE SETEADO LA VARIABLE EN EL INI PARA QUE TOME EL VALOR DE LA CUOTA DESDE SOCIOS APLICAR
                /*$DATOS_GLOBALES = Configure::read('APLICACION.general');
                $cuotaSocialEspecial = (isset($DATOS_GLOBALES['cuota_social_especial']) && $DATOS_GLOBALES['cuota_social_especial'] == '1' ? true : false);
                
		if(substr($codigo_organismo,8,2) == 66 || $cuotaSocialEspecial){
			App::import('Model','Pfyj.Socio');
			$oSocio = new Socio();
			$cuotaSocAnses = $oSocio->getImporteCuotaSocialEspecial($socio_id);
			if($cuotaSocAnses != 0) $cuotaSocialGeneral = $cuotaSocAnses;
		}
                */
                
                // $cuotaSocialEspecial = (!empty($ordenDescuento[0]['importe_cuota_social']) && $ordenDescuento[0]['importe_cuota_social'] != 0 ? $ordenDescuento[0]['importe_cuota_social'] : $cuotaSocialGeneral);
                // $cuota_social = (!empty($ordenDescuento[0]['cuota_social_diferenciada']) && $ordenDescuento[0]['cuota_social_diferenciada'] != 0 ? $ordenDescuento[0]['cuota_social_diferenciada'] : ($cuotaSocialEspecial != 0 ? $cuotaSocialEspecial : $cuotaSocialGeneral));
                
		/*
		//verifico si tiene solamente una orden de descuento de un producto con cuota social diferencial
		App::import('Model','Mutual.OrdenDescuento');
		$oDto = new OrdenDescuento();		
		$cuotaSocialDiferenciada = $oDto->getCuotaSocialDiferenciada($socio_id,$periodo);
		if($cuotaSocialDiferenciada != 0) $cuota_social = $cuotaSocialDiferenciada;
		else{
			//verifico si esta en la tabla de productos el importe
			App::import('Model', 'Mutual.MutualProducto');
			$oMP = new MutualProducto(null);
			$cdif = $oMP->getMayorCuotaSocialDiferenciada($ordenDescuento['OrdenDescuento']['tipo_producto'],$ordenDescuento['OrdenDescuento']['proveedor_id']);				
			if($cdif != 0) $cuota_social = $cdif;
			else $cuota_social = $cuotaSocialGeneral;		
		}
		*/
                
                $soloSiTieneConsumo = (empty($soloSiTieneConsumo) || !$soloSiTieneConsumo ? 0 : 1);
                $SPCALL = "SELECT FX_CALCULA_CUOTA_SOCIAL(".$socio_id.",'".$proveedor_id."','".$tipo_producto."','".$codigo_organismo."','". $periodo ."',".$soloSiTieneConsumo.",'".$codigo_empresa."') as importe;";
                $RET = $this->query($SPCALL);
                $cuota_social = isset($RET[0][0]['importe']) ? (float)$RET[0][0]['importe'] : 0;                
                
		App::import('Model','Proveedores.ProveedorVencimiento');
		$oVto = new ProveedorVencimiento();	
		
		$diaInicio = date('d',strtotime($ordenDescuento['OrdenDescuento']['fecha']));
		$anioPeriodo = substr($periodo,0,4);
		$mesPeriodo = substr($periodo,4,2);

		$fecha = $anioPeriodo .'-'.$mesPeriodo.'-'.$diaInicio;
		
		$vtos = $oVto->calculaVencimientoByPeriodo($ordenDescuento['OrdenDescuento']['proveedor_id'],$codigo_organismo,$periodo,$ordenDescuento['OrdenDescuento']['fecha']);		

		$glb = parent::getGlobalDato('concepto_2',$ordenDescuento['OrdenDescuento']['tipo_producto']);
		$tipo_cuota = $glb['GlobalDato']['concepto_2'];

                // considerar el periodo hasta
		if(!empty($ordenDescuento['OrdenDescuento']['periodo_hasta'])){
			$cuota_social = ($ordenDescuento['OrdenDescuento']['periodo_hasta'] > $periodo ? $cuota_social : 0);
		}

                if($cuota_social > 0) {
                    
                    $proveedorDefault = parent::GlobalDato('entero_1',$tipo_cuota);
                    $proveedorDefault = (empty($proveedorDefault) ? $ordenDescuento['OrdenDescuento']['proveedor_id'] : $proveedorDefault);
		
                    $cuota = array('OrdenDescuentoCuota' => array(
                        'orden_descuento_id' => $ordenDescuento['OrdenDescuento']['id'],
                        'persona_beneficio_id' => $ordenDescuento['OrdenDescuento']['persona_beneficio_id'],
                        'socio_id' => $ordenDescuento['OrdenDescuento']['socio_id'],
                        'tipo_orden_dto' => $ordenDescuento['OrdenDescuento']['tipo_orden_dto'],
                        'tipo_producto' => $ordenDescuento['OrdenDescuento']['tipo_producto'],
                        'periodo' => $periodo,
                        'nro_cuota' =>  0,
                        'tipo_cuota' => $tipo_cuota,
                        'estado' => 'A',	
                        'situacion' => 'MUTUSICUMUTU',
                        'importe' => $cuota_social,
                         'proveedor_id' => $proveedorDefault,
                        'vencimiento' => $vtos['vto_cuota_socio'],
                        'vencimiento_proveedor' => $vtos['vto_cuota_proveedor'],
                        'nro_referencia_proveedor' => $ordenDescuento['OrdenDescuento']['nro_referencia_proveedor'],			
                                            ));			
                    $this->id = 0;
                    if($this->save($cuota)){
                            return $this->getLastInsertID();
                    }else{
                            return 0;
                    }                    
                    
                } else {
                    
                    return 0;
                    
                }
                
                /*
		if($soloSiTieneConsumo){
			$deuda = $this->getTotalDeudaBySocio($socio_id,$periodo,false,false);
			// exit;
			if($deuda <= 0) $cuota_social = 0;
		}

        ###############################################################################
        # GABRIEL / ALEXIS -> REUNION 25/06
        ###############################################################################
        $valorCuotaSocialEmpresa = parent::GlobalDato('decimal_1',$ordenDescuento['PersonaBeneficio']['codigo_empresa']);
        $cuota_social = (empty($valorCuotaSocialEmpresa) || $valorCuotaSocialEmpresa == 0 ? $cuota_social : $valorCuotaSocialEmpresa);
//		debug($valorCuotaSocialEmpresa);
//        debug($cuota_social);
        
		if($cuota_social != 0):
//		if($cuota_social != 0 && $ordenDescuento['OrdenDescuento']['activo'] == 1):

		      // en el tipo de cuota setear el proveedor, si no esta toma el de la orden de descuento
		$proveedorDefault = parent::GlobalDato('entero_1',$tipo_cuota);
		$proveedorDefault = (empty($proveedorDefault) ? $ordenDescuento['OrdenDescuento']['proveedor_id'] : $proveedorDefault);
		
			$cuota = array('OrdenDescuentoCuota' => array(
							'orden_descuento_id' => $ordenDescuento['OrdenDescuento']['id'],
							'persona_beneficio_id' => $ordenDescuento['OrdenDescuento']['persona_beneficio_id'],
							'socio_id' => $ordenDescuento['OrdenDescuento']['socio_id'],
							'tipo_orden_dto' => $ordenDescuento['OrdenDescuento']['tipo_orden_dto'],
							'tipo_producto' => $ordenDescuento['OrdenDescuento']['tipo_producto'],
							'periodo' => $periodo,
							'nro_cuota' =>  0,
							'tipo_cuota' => $tipo_cuota,
							'estado' => 'A',	
							'situacion' => 'MUTUSICUMUTU',
							'importe' => $cuota_social,
			                 'proveedor_id' => $proveedorDefault,
							'vencimiento' => $vtos['vto_cuota_socio'],
							'vencimiento_proveedor' => $vtos['vto_cuota_proveedor'],
							'nro_referencia_proveedor' => $ordenDescuento['OrdenDescuento']['nro_referencia_proveedor'],			
						));			
			$this->id = 0;
			
			if($this->save($cuota)){
				return $this->getLastInsertID();
			}else{
				return 0;
			}
		else:
			return 0;
		endif;
                 *
                 */
		
	}
	
	
	function getCuotaSocialByOrdenIdByPeriodo($orden_descuento_id, $periodo){
		$conditions = array();
		$conditions['OrdenDescuentoCuota.orden_descuento_id'] = $orden_descuento_id;
		$conditions['OrdenDescuentoCuota.periodo'] = $periodo;
		$conditions['OrdenDescuentoCuota.tipo_cuota'] = 'MUTUTCUOCSOC';
		
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$cuotas = $this->find('all',array('conditions' => $conditions));
		if(!empty($cuotas)) return $cuotas[0];
		else return null;
	}
	
	
	/**
	 * genera la cuota permanente para una orden de descuento para un periodo determinado
	 * @param $ordenDescuento
	 * @param $periodo
	 */
	function generaCuotaPermanente($ordenDescuento,$periodo){
		
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio();	
		
		App::import('Model','Mutual.MutualProducto');
		$oMP = new MutualProducto();
		
		
		$producto = $oMP->query("select MutualProducto.importe_fijo from mutual_productos as MutualProducto where MutualProducto.tipo_producto = '".$ordenDescuento['OrdenDescuento']['tipo_producto']."' and MutualProducto.proveedor_id = ". $ordenDescuento['OrdenDescuento']['proveedor_id']);
		$importePermanente = 0;
		if(!empty($producto))$importePermanente = $producto[0]['MutualProducto']['importe_fijo'];
		
		$codigo_organismo_beneficio = $oBen->getCodigoOrganismo($ordenDescuento['OrdenDescuento']['persona_beneficio_id']);
		$valor_liqui = ($ordenDescuento['OrdenDescuento']['importe_cuota'] != 0 ? $ordenDescuento['OrdenDescuento']['importe_cuota'] : $importePermanente);
		
		//ANALIZAR SI ES UN SERVICIO TOMAR EL VALOR DE CALCULO
		if($ordenDescuento['OrdenDescuento']['tipo_orden_dto'] == 'OSERV'){
			App::import('Model','Mutual.MutualServicioValor');
			$oSERV_VALOR = new MutualServicioValor();
			$valor_liqui = $oSERV_VALOR->calcularImporteMensual($ordenDescuento['OrdenDescuento']['numero'],$periodo,$ordenDescuento['PersonaBeneficio']['codigo_beneficio'],true);
		}
		
		
		//armo los vencimientos
		App::import('Model','Proveedores.ProveedorVencimiento');
		$oVto = new ProveedorVencimiento();	
		
		$diaInicio = date('d',strtotime($ordenDescuento['OrdenDescuento']['fecha']));
		$anioPeriodo = substr($periodo,0,4);
		$mesPeriodo = substr($periodo,4,2);

		$fecha = $anioPeriodo .'-'.$mesPeriodo.'-'.$diaInicio;
		
		$vtos = $oVto->calculaVencimientoByPeriodo($ordenDescuento['OrdenDescuento']['proveedor_id'],$codigo_organismo_beneficio,$periodo,$ordenDescuento['OrdenDescuento']['fecha']);		
		$glb = parent::getGlobalDato('concepto_2',$ordenDescuento['OrdenDescuento']['tipo_producto']);
		$tipo_cuota = $glb['GlobalDato']['concepto_2'];
		
		//BORRAR LA CUOTA ADEUDADA DEVENGADA PREVIAMENTE
//		$conditions = array();
//		$conditions['OrdenDescuentoCuota.orden_descuento_id'] = $ordenDescuento['OrdenDescuento']['id'];
//		$conditions['OrdenDescuentoCuota.periodo'] = $periodo;
//		$conditions['OrdenDescuentoCuota.tipo_orden_dto'] = $ordenDescuento['OrdenDescuento']['tipo_orden_dto'];
//		$conditions['OrdenDescuentoCuota.tipo_producto'] = $ordenDescuento['OrdenDescuento']['tipo_producto'];
//		$conditions['OrdenDescuentoCuota.situacion'] = 'MUTUSICUMUTU';
//		$conditions['OrdenDescuentoCuota.tipo_cuota'] = $tipo_cuota;
//		$conditions['OrdenDescuentoCuota.estado'] = 'A';
//
//		$existente = $this->find('all',array('conditions' => $conditions));
//		$existente = $existente[0]['OrdenDescuentoCuota'];
//		
//		//verifico que no tenga pagos
//		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
//		$oPAGOCUOTA = new OrdenDescuentoCobroCuota();
//		$mPagos = $oPAGOCUOTA->getMontoPagoByCuota($existente['id']);
//		
//		$borrada = false;
//		if(!empty($existente) && $mPagos == 0):
//			$this->del($existente['id']);
//			$borrada = true;
//		endif;
		
		// considerar el periodo hasta
		if(!empty($ordenDescuento['OrdenDescuento']['periodo_hasta'])){
			$valor_liqui = ($ordenDescuento['OrdenDescuento']['periodo_hasta'] > $periodo ? $valor_liqui : 0);
		}		
		
		if($valor_liqui != 0):
//		if($valor_liqui != 0 && $ordenDescuento['OrdenDescuento']['activo'] == 1):
		
			$cuota = array('OrdenDescuentoCuota' => array(
							'orden_descuento_id' => $ordenDescuento['OrdenDescuento']['id'],
							'persona_beneficio_id' => $ordenDescuento['OrdenDescuento']['persona_beneficio_id'],
							'socio_id' => $ordenDescuento['OrdenDescuento']['socio_id'],
							'tipo_orden_dto' => $ordenDescuento['OrdenDescuento']['tipo_orden_dto'],
							'tipo_producto' => $ordenDescuento['OrdenDescuento']['tipo_producto'],
							'periodo' => $periodo,
							'nro_cuota' =>  0,
							'tipo_cuota' => $tipo_cuota,
							'estado' => 'A',	
							'situacion' => 'MUTUSICUMUTU',
							'importe' => $valor_liqui,
							'proveedor_id' => $ordenDescuento['OrdenDescuento']['proveedor_id'],
							'vencimiento' => $vtos['vto_cuota_socio'],
							'vencimiento_proveedor' => $vtos['vto_cuota_proveedor'],
							'nro_referencia_proveedor' => $ordenDescuento['OrdenDescuento']['nro_referencia_proveedor'],			
						));			
			$this->id = 0;
			if($this->save($cuota)){
				return $this->getLastInsertID();
			}else{
				return 0;
			}
		else:
			return 0;
		endif;
		
	}
	
	/**
	 * Despago de una cuota. Si el segundo parametro viene el cero hace un despago total y anula la cabecera del pago
	 * @param unknown_type $id
	 * @param unknown_type $cobro_id
	 */
	function despagarCuota($id,$cobro_id=0){
		
		$cuota = $this->read(null,$id);

		//anulo el cobro
		$cobros = array();
		
		if($cobro_id != 0){
			
			$cobroCuotas = Set::extract("/OrdenDescuentoCobroCuota[orden_descuento_cobro_id=$cobro_id]",$cuota);
			if(!empty($cobroCuotas[0]['OrdenDescuentoCobroCuota']))$cobros['OrdenDescuentoCobroCuota'] = $cobroCuotas[0]['OrdenDescuentoCobroCuota'];
			else $cobros = null;
			
		}else{
			
			$cobros['OrdenDescuentoCobroCuota'] = $cuota['OrdenDescuentoCobroCuota'];
			
		}
		if(!empty($cobros)){
			
			App::import('Model','Mutual.OrdenDescuentoCobro');
			$oCOBRO = new OrdenDescuentoCobro();

			App::import('Model','Mutual.OrdenDescuentoCobroCuota');
			$oCCUOTA = new OrdenDescuentoCobroCuota();			
			
			foreach($cobros as $cobro){
				if(!empty($cobro)){
					$oCCUOTA->del($cobro['id']);
					$oCOBRO->recalcularCobro($cobro['orden_descuento_cobro_id']);
				}	
			}
		}
		
		$cuota['OrdenDescuentoCuota']['estado'] = 'A';
		return $this->save($cuota);
		
	}
	
	
	/**
	 * devuelve un array con los saldos actuales de una orden de descuento
	 * vencidas = cantidad de cuotas vencidas
	 * avencer = cantidad de cuotas a vencer
	 * adeudadas = cantidad total de cuotas adeudadas
	 * pagadas = cantidad de cuotas pagadas
	 * importe_vencido = total deuda vencida
	 * importe_avencer = total deuda a vencer
	 * importe_pagado = total pagado
	 * importe_devengado = total devengado
	 * saldo = saldo actual de la orden (si saldo = 0 la orden esta finalizada - totalmente pagada)
	 * @param int $orden_descuento_id
	 * @param string $codigoOrganismo (para calcular el saldo segun el organismo de descuento)
	 * @return unknown_type
	 */
	function getSaldosByOrdenDto($orden_descuento_id,$periodo=null,$codigoOrganismo = null){
		$saldos = array();
		$conditions = array();
		$conditions['OrdenDescuentoCuota.orden_descuento_id'] = $orden_descuento_id;
		$conditions['OrdenDescuentoCuota.estado'] = array('A','P');
		
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		
		if(!empty($codigoOrganismo)){
			$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
			$conditions['PersonaBeneficio.codigo_beneficio'] = $codigoOrganismo;
		}
		$cuotas = $this->find('all',array('conditions' => $conditions));

		$vencidas = 0;
		$avencer = 0;
		$adeudadas = 0;
		$pagadas = 0;
		$importe_vencido = 0;
		$importe_avencer = 0;
		$importe_pagado = 0;
		$importe_devengado = 0;
		$saldo = 0;
		$bloqueo = null;
		$baja_cuotas = 0;
		$baja_importe = 0;
		
		foreach($cuotas as $cuota):
		
			$cuota = $this->__calculaSaldo($cuota,$periodo);
			
			$importe_devengado += $cuota['OrdenDescuentoCuota']['importe'];
			$saldo += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
			
			
			if($cuota['OrdenDescuentoCuota']['vencida'] == 1 && $cuota['OrdenDescuentoCuota']['saldo_cuota'] > 0){
				$vencidas++;
				$importe_vencido += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
			}
			if($cuota['OrdenDescuentoCuota']['vencida'] == 0 && $cuota['OrdenDescuentoCuota']['saldo_cuota'] > 0){
				$avencer++;
				$importe_avencer += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
			}
			if($cuota['OrdenDescuentoCuota']['saldo_cuota'] > 0) $adeudadas++;
			
			if($cuota['OrdenDescuentoCuota']['saldo_cuota'] == 0){
				$importe_pagado += $cuota['OrdenDescuentoCuota']['pagado'];
				$pagadas++;
			}
			
			if($cuota['OrdenDescuentoCuota']['estado'] == 'A' && $this->habilitarBloqueo):
				//VERIFICAR SI LA CUOTA HAY QUE BLOQUEARLA O NO
				App::import('Model','mutual.LiquidacionCuota');
				$oLC = new LiquidacionCuota();
				$bloqueo = $oLC->isCuotaOriginalBloqueada($cuota['OrdenDescuentoCuota']['id']);
			endif;
			
			
		endforeach;
		
		$saldos['vencidas'] = $vencidas;
		$saldos['avencer'] = $avencer;
		$saldos['adeudadas'] = $adeudadas;
		$saldos['pagadas'] = $pagadas;
		$saldos['importe_vencido'] = round($importe_vencido,2);
		$saldos['importe_avencer'] = round($importe_avencer,2);
		$saldos['importe_pagado'] = round($importe_pagado,2);
		$saldos['importe_devengado'] = round($importe_devengado,2);
		$saldos['saldo'] = round($saldo,2);
		$saldos['bloqueo'] = $bloqueo;	
		
		
		//ARMO LOS DATOS DE LA BAJA
		$baja = $this->getCuotasBajasByOrdenDescuento($orden_descuento_id,$periodo);
		$saldos['baja_cuotas'] = $baja['cuotas'];
		$saldos['baja_importe'] = $baja['importe'];
		
		return $saldos;
	}
	
	/**
	 * Verifica que una cuota no exista para un mismo proveedor, periodo, concepto y numero de cuota
	 * @param unknown_type $data
	 * @return boolean
	 */
	function checkExisteCuota($data){
		$conditions = array();
		$conditions['OrdenDescuentoCuota.orden_descuento_id'] = $data['OrdenDescuentoCuota']['orden_descuento_id'];
		$conditions['OrdenDescuentoCuota.proveedor_id'] = $data['OrdenDescuentoCuota']['proveedor_id'];
		$conditions['OrdenDescuentoCuota.periodo'] = $data['OrdenDescuentoCuota']['periodo'];
		$conditions['OrdenDescuentoCuota.tipo_cuota'] = $data['OrdenDescuentoCuota']['tipo_cuota'];
		$conditions['OrdenDescuentoCuota.nro_cuota'] = $data['OrdenDescuentoCuota']['nro_cuota'];
		$this->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$cuotas = $this->find('count',array('conditions' => $conditions));
		return ($cuotas != 0 ? false : true);
	}
	
	
	function borrarCuota($id){
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCobroCuota = new OrdenDescuentoCobroCuota();
        $pagado = $oCobroCuota->getMontoPagoByCuota($id);
		return ($pagado == 0 ? $this->del($id) : false);
	}
	
	
	/**
	 * Borra todos los conceptos permanentes para un socio, periodo y organismo que
	 * esten en estado 'A', situacion NORMAL y que no tengan un pago
	 * @param unknown_type $socio_id
	 * @param unknown_type $periodo
	 * @param unknown_type $organismo
	 */
	function borrarConsumoPermanenteDevengado($socio_id,$periodo,$organismo = null){
		$sql = "SELECT
					PersonaBeneficio.codigo_beneficio, 
					OrdenDescuento.id,
					OrdenDescuento.fecha,
					OrdenDescuento.tipo_orden_dto,
					OrdenDescuento.tipo_producto,
					OrdenDescuento.socio_id,
					OrdenDescuento.persona_beneficio_id,
					OrdenDescuento.proveedor_id,
					OrdenDescuento.nro_referencia_proveedor,
					OrdenDescuento.importe_cuota		 
				FROM 
					orden_descuentos as OrdenDescuento, 
					socios as Socio,
					persona_beneficios as PersonaBeneficio  
				WHERE
					Socio.id = $socio_id  
					AND OrdenDescuento.socio_id = Socio.id 
					AND OrdenDescuento.tipo_orden_dto <> 'CMUTU'
					AND OrdenDescuento.tipo_producto <> 'MUTUPROD0003'
					AND OrdenDescuento.periodo_ini <= '$periodo'
					AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
					".(!empty($organismo) ? "AND PersonaBeneficio.codigo_beneficio = '$organismo'" : "")."
					AND OrdenDescuento.permanente = 1";		
		$ordenes = $this->query($sql);
//        debug($ordenes);
		if(!empty($ordenes)):
			foreach($ordenes as $orden):
				$glb = parent::getGlobalDato('concepto_2',$orden['OrdenDescuento']['tipo_producto']);
				$tipo_cuota = $glb['GlobalDato']['concepto_2'];
				$conditions = array();
				$conditions['OrdenDescuentoCuota.orden_descuento_id'] = $orden['OrdenDescuento']['id'];
				$conditions['OrdenDescuentoCuota.periodo'] = $periodo;
				$conditions['OrdenDescuentoCuota.situacion'] = 'MUTUSICUMUTU';
				$conditions['OrdenDescuentoCuota.tipo_cuota'] = $tipo_cuota;
				$conditions['OrdenDescuentoCuota.estado'] = 'A';
				$existentes = $this->find('all',array('conditions' => $conditions));
//                debug($existentes);
				if(!empty($existentes)):
					App::import('Model','Mutual.OrdenDescuentoCobroCuota');
					$oPAGOCUOTA = new OrdenDescuentoCobroCuota();
					foreach($existentes as $existente):
						$mPagos = $oPAGOCUOTA->getMontoPagoByCuota($existente['OrdenDescuentoCuota']['id']);
						if(!empty($existente['OrdenDescuentoCuota']['id']) && $mPagos == 0){
                            $this->del($existente['OrdenDescuentoCuota']['id']);
                        }
					endforeach;
				endif;
			endforeach;
		endif;
	}
	
	/**
	 * Borra la cuota social devengada para un socio, periodo y organismo que
	 * esten en estado 'A', situacion NORMAL y que no tengan un pago
	 * @param unknown_type $socio_id
	 * @param unknown_type $periodo
	 * @param unknown_type $organismo
	 */
	function borrarCuotaSocialDevengada($socio_id,$periodo,$organismo = null){
		$sql = "SELECT
					PersonaBeneficio.codigo_beneficio, 
					OrdenDescuento.id,
					OrdenDescuento.fecha,
					OrdenDescuento.tipo_orden_dto,
					OrdenDescuento.tipo_producto,
					OrdenDescuento.socio_id,
					OrdenDescuento.persona_beneficio_id,
					OrdenDescuento.proveedor_id,
					OrdenDescuento.nro_referencia_proveedor,
					OrdenDescuento.importe_cuota		 
				FROM 
					orden_descuentos as OrdenDescuento, 
					socios as Socio,
					persona_beneficios as PersonaBeneficio  
				WHERE
					Socio.id = $socio_id  
					AND OrdenDescuento.socio_id = Socio.id 
					AND OrdenDescuento.tipo_orden_dto = 'CMUTU'
					AND OrdenDescuento.tipo_producto = 'MUTUPROD0003'
					AND OrdenDescuento.periodo_ini <= '$periodo'
					AND OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
					".(!empty($organismo) ? "AND PersonaBeneficio.codigo_beneficio = '$organismo'" : "")."
					AND OrdenDescuento.permanente = 1";		
		$ordenes = $this->query($sql);
		if(!empty($ordenes)):
			foreach($ordenes as $orden):
				$glb = parent::getGlobalDato('concepto_2',$orden['OrdenDescuento']['tipo_producto']);
				$tipo_cuota = $glb['GlobalDato']['concepto_2'];
				$conditions = array();
				$conditions['OrdenDescuentoCuota.orden_descuento_id'] = $orden['OrdenDescuento']['id'];
				$conditions['OrdenDescuentoCuota.periodo'] = $periodo;
				$conditions['OrdenDescuentoCuota.situacion'] = 'MUTUSICUMUTU';
				$conditions['OrdenDescuentoCuota.tipo_cuota'] = $tipo_cuota;
				//$conditions['OrdenDescuentoCuota.estado'] = 'A';
				$existentes = $this->find('all',array('conditions' => $conditions));
				if(!empty($existentes)):
					App::import('Model','Mutual.OrdenDescuentoCobroCuota');
					$oPAGOCUOTA = new OrdenDescuentoCobroCuota();
					foreach($existentes as $existente):
						$mPagos = $oPAGOCUOTA->getMontoPagoByCuota($existente['OrdenDescuentoCuota']['id']);
						if(!empty($existente['OrdenDescuentoCuota']['id']) && $mPagos == 0){
                            //BORRO DE LA LIQUIDACION CUOTAS PARA EL PERIODO 
                            $sql = "delete from liquidacion_cuotas where orden_descuento_cuota_id = ".$existente['OrdenDescuentoCuota']['id']." and periodo_cuota = '$periodo'";
                            $this->query($sql);
							$this->del($existente['OrdenDescuentoCuota']['id']);
						}
					endforeach;
				endif;
			endforeach;
		endif;
	}	
	

    function get_mora_by_orden_dto($orden_dto_id,$periodo = null){
        
        if(empty($orden_dto_id)){
            $mora['error'] = '*** SIN NRO DE ORDEN DE DESCUENTO ***';
            return $mora;        
        }        
        
        $periodo = (!empty($periodo) ? $periodo : date('Ym'));
        $mora = array();
		$mora = array(
                    'orden_descuento_id' => $orden_dto_id,
                    'periodo_corte' => $periodo,
                    'importe_total' => 0,
                    'importe_baja' => 0,
                    'pago_acumulado' => 0,
                    'pendiente_acreditar' => 0,
                    'devengado' => 0, 
                    'cobrado' => 0,
                    'cobrado_termino' => 0,
                    'cobrado_ntermino' => 0,
                    'cobrado_cancelacion' => 0,
                    'cobrado_liquidacion' => 0,
                    'cobrado_caja' => 0,
                    'cobrado_optimo' => 0,
                    'cobrado_indice' => 0,
                    'saldo' => 0,
                    'saldo_aconciliar' => 0,
                    'saldo_avencer' => 0,
                    'cuotas_vencidas' => 0, 
                    'cuotas_avencer' => 0,
                    'cuotas_baja' => 0,
                    'pendiente_acreditar' => 0,
                    'saldo_0003' => 0,
                    'saldo_0306' => 0,
                    'saldo_0609' => 0,
                    'saldo_0912' => 0,
                    'saldo_1213' => 0,
                    'saldoav_03' => 0,
                    'saldoav_06' => 0,
                    'saldoav_09' => 0,
                    'saldoav_12' => 0,
                    'saldoav_13' => 0,
                    'importe_percibido' => 0,
                    'importe_solicitado' => 0,
        );        
        
        $sql = "select
                FX_ORDENDTO_CANT_CUOTAS($orden_dto_id,'$periodo','TOT') as cuotas_totales
                ,FX_ORDENDTO_CANT_CUOTAS($orden_dto_id,'$periodo','VEN') as cuotas_vencidas
                ,FX_ORDENDTO_CANT_CUOTAS($orden_dto_id,'$periodo','NVE') as cuotas_nvencidas
                ,FX_ORDENDTO_CANT_CUOTAS($orden_dto_id,'$periodo','ANU') as cuotas_anuladas    
                ,FX_ORDENDTO_DEVENGADO($orden_dto_id,'$periodo','TOT') as devengado_total
                ,FX_ORDENDTO_DEVENGADO($orden_dto_id,'$periodo','PER') as devengado_periodo
                ,FX_ORDENDTO_DEVENGADO($orden_dto_id,'$periodo','ANU') as devengado_anulado    
                ,FX_ORDENDTO_PAGO_ACUMULADO($orden_dto_id,'$periodo','TOT') as pago_acumulado 
                ,FX_ORDENDTO_PAGO_ACUMULADO($orden_dto_id,'$periodo','TER') as pago_atermino
                ,FX_ORDENDTO_PAGO_ACUMULADO($orden_dto_id,'$periodo','VEN') as pago_vencido 
                ,FX_ORDENDTO_PAGO_ACUMULADO($orden_dto_id,'$periodo','CAN') as pago_cancelacion 
                ,FX_ORDENDTO_PAGO_ACUMULADO($orden_dto_id,'$periodo','LIQ') as pago_liquidacion
                ,FX_ORDENDTO_PAGO_ACUMULADO($orden_dto_id,'$periodo','CAJ') as pago_caja    
                ,FX_ORDENDTO_PENDIENTE_ACREDITAR($orden_dto_id,'$periodo') as pendiente_acreditar 
                ,FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO($orden_dto_id,'$periodo',0,999) AS saldo_vencido
                ,FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO($orden_dto_id,'$periodo',0,3) AS saldo_0003
                ,FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO($orden_dto_id,'$periodo',3,6) AS saldo_0306
                ,FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO($orden_dto_id,'$periodo',6,9) AS saldo_0609
                ,FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO($orden_dto_id,'$periodo',9,12) AS saldo_0912
                ,FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO($orden_dto_id,'$periodo',12,99) AS saldo_1213
                ,FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO($orden_dto_id,'$periodo',0,999) AS saldo_avencer
                ,FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO($orden_dto_id,'$periodo',0,3) AS saldo_avencer_3
                ,FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO($orden_dto_id,'$periodo',3,6) AS saldo_avencer_6
                ,FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO($orden_dto_id,'$periodo',6,9) AS saldo_avencer_9
                ,FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO($orden_dto_id,'$periodo',9,12) AS saldo_avencer_12
                ,FX_ORDENDTO_SALDO_A_VENCER_POR_RANGO($orden_dto_id,'$periodo',12,99) AS saldo_avencer_13
                ,FX_ORDENDTO_DEVENGADO($orden_dto_id,'$periodo','IPE') as importe_percibido  
                ,FX_ORDENDTO_DEVENGADO($orden_dto_id,'$periodo','ISO') as importe_solicitado
                ;";
        
        $datos = $this->query($sql);
        
        if(!empty($datos)):
            if (isset($datos[0][0]['devengado_periodo'])) {
                $mora['devengado'] = round($datos[0][0]['devengado_periodo'], 2);
            }
            if (isset($datos[0][0]['devengado_periodo'])) {
                $mora['cobrado_optimo'] = round($datos[0][0]['devengado_periodo'], 2);
            }
            if (isset($datos[0][0]['pago_acumulado'])) {
                $mora['cobrado_termino'] = round($datos[0][0]['pago_atermino'], 2);
            }
            if (isset($datos[0][0]['pago_acumulado'])) {
                $mora['cobrado_ntermino'] = round($datos[0][0]['pago_vencido'], 2);
            }
            if (isset($datos[0][0]['pago_acumulado'])) {
                $mora['cobrado'] = round($datos[0][0]['pago_acumulado'], 2);
            }            
            if (isset($datos[0][0]['saldo_vencido'])) {
                $mora['saldo'] = round($datos[0][0]['saldo_vencido'], 2);
            }
            if (isset($datos[0][0]['cuotas_vencidas'])) {
                $mora['cuotas_vencidas'] = $datos[0][0]['cuotas_vencidas'];
            }
            if (isset($datos[0][0]['cuotas_nvencidas'])) {
                $mora['cuotas_avencer'] = $datos[0][0]['cuotas_nvencidas'];
            }
            if (isset($datos[0][0]['pendiente_acreditar'])) {
                $mora['pendiente_acreditar'] = $datos[0][0]['pendiente_acreditar'];
            }
            if (isset($datos[0][0]['saldo_0003'])) {
                $mora['saldo_0003'] = $datos[0][0]['saldo_0003'];
            }
            if (isset($datos[0][0]['saldo_0306'])) {
                $mora['saldo_0306'] = $datos[0][0]['saldo_0306'];
            }
            if (isset($datos[0][0]['saldo_0609'])) {
                $mora['saldo_0609'] = $datos[0][0]['saldo_0609'];
            }
            if (isset($datos[0][0]['saldo_0912'])) {
                $mora['saldo_0912'] = $datos[0][0]['saldo_0912'];
            }
            if (isset($datos[0][0]['saldo_1213'])) {
                $mora['saldo_1213'] = $datos[0][0]['saldo_1213'];
            }
            if (isset($datos[0][0]['devengado_total'])) {
                $mora['importe_total'] = $datos[0][0]['devengado_total'];
            }
            if (isset($datos[0][0]['pago_acumulado'])) {
                $mora['pago_acumulado'] = $datos[0][0]['pago_acumulado'];
            }
            if (isset($datos[0][0]['pendiente_acreditar'])) {
                $mora['pendiente_acreditar'] = $datos[0][0]['pendiente_acreditar'];
            }

            if (isset($datos[0][0]['saldo_avencer'])) {
                $mora['saldo_avencer'] = $datos[0][0]['saldo_avencer'];
            }
            if (isset($datos[0][0]['saldo_avencer_3'])) {
                $mora['saldoav_03'] = $datos[0][0]['saldo_avencer_3'];
            }
            if (isset($datos[0][0]['saldo_avencer_6'])) {
                $mora['saldoav_06'] = $datos[0][0]['saldo_avencer_6'];
            }
            if (isset($datos[0][0]['saldo_avencer_9'])) {
                $mora['saldoav_09'] = $datos[0][0]['saldo_avencer_9'];
            }            
            if (isset($datos[0][0]['saldo_avencer_12'])) {
                $mora['saldoav_12'] = $datos[0][0]['saldo_avencer_12'];
            }
            if (isset($datos[0][0]['saldo_avencer_13'])) {
                $mora['saldoav_13'] = $datos[0][0]['saldo_avencer_13'];
            }

            if (isset($datos[0][0]['devengado_anulado'])) {
                $mora['importe_baja'] = $datos[0][0]['devengado_anulado'];
            }            
            if (isset($datos[0][0]['cuotas_anuladas'])) {
                $mora['cuotas_baja'] = $datos[0][0]['cuotas_anuladas'];
            }   
            
            if (isset($datos[0][0]['pago_cancelacion'])) {
                $mora['cobrado_cancelacion'] = $datos[0][0]['pago_cancelacion'];
            } 
            if (isset($datos[0][0]['pago_liquidacion'])) {
                $mora['cobrado_liquidacion'] = $datos[0][0]['pago_liquidacion'];
            }            
            if (isset($datos[0][0]['pago_caja'])) {
                $mora['cobrado_caja'] = $datos[0][0]['pago_caja'];
            }

            if (isset($datos[0][0]['importe_percibido'])) {
                $mora['importe_percibido'] = $datos[0][0]['importe_percibido'];
            }
            if (isset($datos[0][0]['importe_solicitado'])) {
                $mora['importe_solicitado'] = $datos[0][0]['importe_solicitado'];
            }

            
            $mora['saldo_aconciliar'] = $mora['saldo'] - $mora['pendiente_acreditar'];
            $mora['cobrado_indice'] = round($mora['cobrado'] / $mora['cobrado_optimo'],2);

        endif;
        return $mora;        
        
//        debug($datos);
//        exit;
    }    
        

    function getMoraByOrdenDtoHastaPeriodo($orden_dto_id,$periodo = null){
        
        $periodo = (!empty($periodo) ? $periodo : date('Ym'));
		$mora = array();
		$mora = array(
                    'orden_descuento_id' => $orden_dto_id,
                    'periodo_corte' => $periodo,
                    'importe_total' => 0,
                    'pago_acumulado' => 0,
                    'pendiente_acreditar' => 0,
                    'devengado' => 0, 
                    'cobrado' => 0, 
                    'cobrado_optimo' => 0,
                    'cobrado_indice' => 0,
                    'saldo' => 0,
                    'saldo_aconciliar' => 0,
                    'saldo_avencer' => 0,
                    'cuotas_vencidas' => 0, 
                    'cuotas_avencer' => 0,
                    'pendiente_acreditar' => 0,
                    'saldo_0003' => 0,
                    'saldo_0306' => 0,
                    'saldo_0609' => 0,
                    'saldo_0912' => 0,
                    'saldo_1213' => 0,
                    'saldoav_03' => 0,
                    'saldoav_06' => 0,
                    'saldoav_12' => 0,
                    'saldoav_13' => 0,
        );
        if(empty($orden_dto_id)){
            $mora['error'] = '*** SIN NRO DE ORDEN DE DESCUENTO ***';
            return $mora;        
        }
//		$sql = "SELECT *,
//			(SELECT SUM(importe) FROM orden_descuento_cuotas WHERE 
//			orden_descuento_id = $orden_dto_id AND periodo < '$periodo' and estado <> 'B') AS impo_devengado,
//			(SELECT SUM(cc.importe) FROM orden_descuento_cobro_cuotas cc, orden_descuento_cuotas c
//			WHERE c.orden_descuento_id = $orden_dto_id AND c.periodo < '$periodo' AND cc.orden_descuento_cuota_id = c.id
//			GROUP BY c.orden_descuento_id) AS impo_cobrado
//			FROM orden_descuentos WHERE id = $orden_dto_id";
        
        $sql = "SELECT *,
                ifnull((SELECT SUM(importe) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id AND periodo <= '$periodo' and estado NOT IN ('B','D')),0)
                + ifnull((SELECT sum(cc.importe) FROM orden_descuento_cuotas c, orden_descuento_cobro_cuotas cc 
                                    WHERE 
                                    c.id = cc.orden_descuento_cuota_id and 
                                    c.orden_descuento_id = $orden_dto_id AND periodo <= '$periodo' and estado IN ('B','D')),0)                    
                AS impo_devengado,
                
                ifnull((SELECT SUM(c.importe) FROM orden_descuento_cuotas c
                WHERE 
                orden_descuento_id = $orden_dto_id AND periodo <= '$periodo' and estado NOT IN ('B','D')),0)
                + ifnull((SELECT sum(cc.importe) FROM orden_descuento_cuotas c, orden_descuento_cobro_cuotas cc 
                                                    WHERE 
                                                    c.id = cc.orden_descuento_cuota_id and 
                                                    c.orden_descuento_id = $orden_dto_id AND periodo <= '$periodo' and estado IN ('B','D')),0)                    
                AS cobrado_optimo, 
                    
                (SELECT count(*) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id AND periodo <= '$periodo' and estado NOT IN ('B','D')
                AND importe > IFNULL((
                                SELECT SUM(cocu.importe) from
                                orden_descuento_cobro_cuotas cocu
                                INNER JOIN orden_descuento_cobros  co ON (co.id = cocu.orden_descuento_cobro_id) 
                                WHERE 
                                cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id
                                AND co.periodo_cobro <= '$periodo'),0)
                ) AS cuotas_vencidas,

                (SELECT count(*) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id AND periodo > '$periodo' and estado NOT IN ('B','D')
                AND importe > IFNULL((
                                SELECT SUM(cocu.importe) from
                                orden_descuento_cobro_cuotas cocu
                                INNER JOIN orden_descuento_cobros  co ON (co.id = cocu.orden_descuento_cobro_id) 
                                WHERE 
                                cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id),0)
                ) AS cuotas_avencer,

                ifnull((SELECT SUM(cc.importe) FROM orden_descuento_cobro_cuotas cc, orden_descuento_cuotas c, orden_descuento_cobros  co
                WHERE co.id = cc.orden_descuento_cobro_id and c.orden_descuento_id = $orden_dto_id 
                AND c.periodo <= '$periodo' and co.periodo_cobro <= '$periodo'  
                    AND cc.orden_descuento_cuota_id = c.id
                GROUP BY c.orden_descuento_id),0) AS impo_cobrado,
                
                ifnull((SELECT SUM(importe) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id AND periodo <= '$periodo' and estado NOT IN ('B','D')),0) -
                ifnull((SELECT SUM(cc.importe) FROM orden_descuento_cobro_cuotas cc, orden_descuento_cuotas c, orden_descuento_cobros  co
                WHERE co.id = cc.orden_descuento_cobro_id and c.orden_descuento_id = $orden_dto_id AND c.periodo <= '$periodo' and co.periodo_cobro <= '$periodo' AND cc.orden_descuento_cuota_id = c.id and c.estado NOT IN ('B','D')
                GROUP BY c.orden_descuento_id),0) AS saldo,
                
                ifnull((
                 select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_id = $orden_dto_id and lc.imputada = 0 and 
                    lc.para_imputar = 1 and lc.periodo_cuota <= '$periodo' 
                    and ifnull(lc.orden_descuento_cobro_id,0) = 0)
                 ,0) as pendiente_acreditar
                 
                ,ifnull((SELECT SUM(importe) - SUM(ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)) - SUM(ifnull((
                 select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id and lc.imputada = 0 and 
                    lc.para_imputar = 1 and lc.periodo_cuota <= '$periodo' 
                    and ifnull(lc.orden_descuento_cobro_id,0) = 0)
                 ,0)) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id 
                AND periodo > date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 3 month),'%Y%m')
                AND periodo <= date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 0 month),'%Y%m')
                and estado NOT IN ('B','D') and importe > ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)),0) 
                AS saldo_0003
                
                ,ifnull((SELECT SUM(importe) - SUM(ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)) - SUM(ifnull((
                 select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id and lc.imputada = 0 and 
                    lc.para_imputar = 1 and lc.periodo_cuota <= '$periodo' 
                    and ifnull(lc.orden_descuento_cobro_id,0) = 0)
                 ,0)) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id 
                AND periodo > date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 6 month),'%Y%m')
                AND periodo <= date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 3 month),'%Y%m')
                and estado NOT IN ('B','D')  and importe > ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)),0) 
                AS saldo_0306
                
                ,ifnull((SELECT SUM(importe) - SUM(ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)) - SUM(ifnull((
                 select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id and lc.imputada = 0 and 
                    lc.para_imputar = 1 and lc.periodo_cuota <= '$periodo' 
                    and ifnull(lc.orden_descuento_cobro_id,0) = 0)
                 ,0)) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id 
                AND periodo > date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 9 month),'%Y%m')
                AND periodo <= date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 6 month),'%Y%m')
                and estado NOT IN ('B','D')  and importe > ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)),0)
                AS saldo_0609 
                
                ,ifnull((SELECT SUM(importe)  - SUM(ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)) - SUM(ifnull((
                 select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id and lc.imputada = 0 and 
                    lc.para_imputar = 1 and lc.periodo_cuota <= '$periodo' 
                    and ifnull(lc.orden_descuento_cobro_id,0) = 0)
                 ,0)) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id 
                AND periodo > date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 12 month),'%Y%m')
                AND periodo <= date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 9 month),'%Y%m')
                and estado NOT IN ('B','D')  and importe > ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)),0)
                AS saldo_0912 
                
                ,ifnull((SELECT SUM(importe) - SUM(ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)) - SUM(ifnull((
                 select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = orden_descuento_cuotas.id and lc.imputada = 0 and 
                    lc.para_imputar = 1 and lc.periodo_cuota <= '$periodo' 
                    and ifnull(lc.orden_descuento_cobro_id,0) = 0)
                 ,0)) FROM orden_descuento_cuotas WHERE 
                orden_descuento_id = $orden_dto_id 
                AND periodo <= date_format(date_sub(date_format(concat('$periodo','01'),'%Y-%m-%d'), interval 12 month),'%Y%m')
                and estado NOT IN ('B','D')  and importe > ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                where orden_descuento_cuotas.id = cc.orden_descuento_cuota_id
                and cc.periodo_cobro <= '$periodo'),0)),0) 
                AS saldo_1213,

                ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                WHERE cu.orden_descuento_id = $orden_dto_id
                    AND cu.periodo > '$periodo' and cu.estado NOT IN ('B','D')
                    AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                    INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                    WHERE 
                    cocu.orden_descuento_cuota_id = cu.id
                    AND co.periodo_cobro <= '$periodo') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                        inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '$periodo'
                    WHERE orden_descuento_cuota_id = cu.id
                    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                    order by liquidacion_id desc limit 1 ),0))
                    GROUP BY cu.orden_descuento_id),0) as saldo_avencer,

                    ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                    WHERE cu.orden_descuento_id = $orden_dto_id
                        AND cu.periodo > '$periodo' 
                    and cu.periodo <= date_format(date_add(STR_TO_DATE('".$periodo."01','%Y%m%d'), interval 3 month),'%Y%m')
                    and cu.estado NOT IN ('B','D','C')
                        AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                        INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                        WHERE 
                        cocu.orden_descuento_cuota_id = cu.id 
                        AND co.periodo_cobro <= '".$periodo."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                            inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$periodo."'
                        WHERE orden_descuento_cuota_id = cu.id
                        AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                        order by liquidacion_id desc limit 1 ),0))
                        GROUP BY cu.orden_descuento_id),0) as saldo_avencer_3,    

                    ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                    WHERE cu.orden_descuento_id = $orden_dto_id
                        AND cu.periodo > date_format(date_add(STR_TO_DATE('".$periodo."01','%Y%m%d'), interval 3 month),'%Y%m') 
                    and cu.periodo <= date_format(date_add(date_add(STR_TO_DATE('".$periodo."01','%Y%m%d'), interval 3 month),interval 6 month),'%Y%m')
                    and cu.estado NOT IN ('B','D','C')
                        AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                        INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                        WHERE 
                        cocu.orden_descuento_cuota_id = cu.id 
                        AND co.periodo_cobro <= '".$periodo."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                            inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$periodo."'
                        WHERE orden_descuento_cuota_id = cu.id
                        AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                        order by liquidacion_id desc limit 1 ),0))
                        GROUP BY cu.orden_descuento_id),0) as saldo_avencer_6, 

                    ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                    WHERE cu.orden_descuento_id = $orden_dto_id
                        AND cu.periodo > date_format(date_add(date_add(STR_TO_DATE('".$periodo."01','%Y%m%d'), interval 3 month),interval 6 month),'%Y%m') 
                    and cu.periodo <= date_format(date_add(date_format(date_add(date_add(STR_TO_DATE('".$periodo."01','%Y%m%d'), interval 3 month),interval 6 month),'%Y%m%d'),interval 6 month),'%Y%m')
                    and cu.estado NOT IN ('B','D','C')
                        AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                        INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                        WHERE 
                        cocu.orden_descuento_cuota_id = cu.id 
                        AND co.periodo_cobro <= '".$periodo."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                            inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$periodo."'
                        WHERE orden_descuento_cuota_id = cu.id
                        AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                        order by liquidacion_id desc limit 1 ),0))
                        GROUP BY cu.orden_descuento_id),0) as saldo_avencer_12,     

                    ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                    WHERE cu.orden_descuento_id = $orden_dto_id
                    and cu.periodo > date_format(date_add(date_format(date_add(date_add(STR_TO_DATE('".$periodo."01','%Y%m%d'), interval 3 month),interval 6 month),'%Y%m%d'),interval 6 month),'%Y%m')
                    and cu.estado NOT IN ('B','D','C')
                        AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                        INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                        WHERE 
                        cocu.orden_descuento_cuota_id = cu.id 
                        AND co.periodo_cobro <= '".$periodo."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                            inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$periodo."'
                        WHERE orden_descuento_cuota_id = cu.id
                        AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                        order by liquidacion_id desc limit 1 ),0))
                        GROUP BY cu.orden_descuento_id),0) as saldo_avencer_13,
                        
                        ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                        WHERE cu.orden_descuento_id = $orden_dto_id
                        and cu.estado NOT IN ('B','D')),0) 
                        + ifnull((SELECT sum(cc.importe) FROM orden_descuento_cuotas c, orden_descuento_cobro_cuotas cc 
                                                            WHERE 
                                                            c.id = cc.orden_descuento_cuota_id and 
                                                            c.orden_descuento_id = $orden_dto_id AND periodo <= '$periodo' and estado IN ('B','D')),0)                        
                        as importe_total,
                        
                        SUM(ifnull((select sum(cc.importe) 
                        from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co,orden_descuento_cuotas cu 
                        where 
                        cu.orden_descuento_id = $orden_dto_id
                        and cc.orden_descuento_cuota_id = cu.id and cc.orden_descuento_cobro_id = co.id and
                        co.periodo_cobro <= '".$periodo."'
                        group by cu.orden_descuento_id
                        ),0)) as pago_acumulado,
                        
                       
                        ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                        inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$periodo."'
                
                        WHERE orden_descuento_id = $orden_dto_id
                        AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                        order by liquidacion_id),0) as pendiente_acreditar                            
                
                FROM orden_descuentos WHERE id = $orden_dto_id;";
//                debug($sql);
                
		$datos = $this->query($sql);
//        debug($datos);
//        exit;                

		if(!empty($datos)):
                    if(isset($datos[0][0]['impo_devengado'])) $mora['devengado'] = round($datos[0][0]['impo_devengado'],2);
                    if(isset($datos[0][0]['cobrado_optimo'])) $mora['cobrado_optimo'] = round($datos[0][0]['cobrado_optimo'],2);
                    if(isset($datos[0][0]['impo_cobrado'])) $mora['cobrado'] = round($datos[0][0]['impo_cobrado'],2);
                    if(isset($datos[0][0]['saldo'])) $mora['saldo'] = round($datos[0][0]['saldo'],2);
                    if(isset($datos[0][0]['cuotas_vencidas'])) $mora['cuotas_vencidas'] = $datos[0][0]['cuotas_vencidas'];
                    if(isset($datos[0][0]['cuotas_avencer'])) $mora['cuotas_avencer'] = $datos[0][0]['cuotas_avencer'];
                    if(isset($datos[0][0]['pendiente_acreditar'])) $mora['pendiente_acreditar'] = $datos[0][0]['pendiente_acreditar'];
                    if(isset($datos[0][0]['saldo_0003'])) $mora['saldo_0003'] = $datos[0][0]['saldo_0003'];
                    if(isset($datos[0][0]['saldo_0306'])) $mora['saldo_0306'] = $datos[0][0]['saldo_0306'];
                    if(isset($datos[0][0]['saldo_0609'])) $mora['saldo_0609'] = $datos[0][0]['saldo_0609'];
                    if(isset($datos[0][0]['saldo_0912'])) $mora['saldo_0912'] = $datos[0][0]['saldo_0912'];
                    if(isset($datos[0][0]['saldo_1213'])) $mora['saldo_1213'] = $datos[0][0]['saldo_1213'];
                    
                    
                    if(isset($datos[0][0]['importe_total'])) $mora['importe_total'] = $datos[0][0]['importe_total'];
                    if(isset($datos[0][0]['pago_acumulado'])) $mora['pago_acumulado'] = $datos[0][0]['pago_acumulado'];
                    if(isset($datos[0][0]['pendiente_acreditar'])) $mora['pendiente_acreditar'] = $datos[0][0]['pendiente_acreditar'];
                    
                    if(isset($datos[0][0]['saldo_avencer'])) $mora['saldo_avencer'] = $datos[0][0]['saldo_avencer'];
                    if(isset($datos[0][0]['saldo_avencer_3'])) $mora['saldoav_03'] = $datos[0][0]['saldo_avencer_3'];
                    if(isset($datos[0][0]['saldo_avencer_6'])) $mora['saldoav_06'] = $datos[0][0]['saldo_avencer_6'];
                    if(isset($datos[0][0]['saldo_avencer_12'])) $mora['saldoav_12'] = $datos[0][0]['saldo_avencer_12'];
                    if(isset($datos[0][0]['saldo_avencer_13'])) $mora['saldoav_13'] = $datos[0][0]['saldo_avencer_13'];
                    
                    $mora['saldo_aconciliar'] = $mora['saldo'] - $mora['pendiente_acreditar'];
                    $mora['cobrado_indice'] = round($mora['cobrado'] / $mora['cobrado_optimo'],2);
                    
		endif;
		return $mora;
    }

	
	function getCuotasBajaByPeriodoOrganismoProveedor($periodo, $organismo, $proveedor_id){
		$cuotas = array();
		$sql = "SELECT 
				OrdenDescuentoCuota.id 
				FROM orden_descuento_cuotas AS OrdenDescuentoCuota
				INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
				WHERE estado = 'B'
				AND periodo = '$periodo'
				AND proveedor_id = $proveedor_id
                                ".(!empty($organismo) ? "AND PersonaBeneficio.codigo_beneficio = '$organismo' " : " ");
		$datos = $this->query($sql);
		if(empty($datos)) return $cuotas;
		foreach($datos as $dato):
			$idCuota = $dato['OrdenDescuentoCuota']['id'];
			$cuota = $this->getCuota($idCuota,false);
			$cuota = $cuota['OrdenDescuentoCuota'];
			array_push($cuotas,$cuota);
		endforeach;
		return $cuotas;
	}
	
	
	function agregarCuota($datos){
		$oODTO = parent::importarModelo('OrdenDescuento','mutual');
		$orden = $oODTO->read(null,$datos['OrdenDescuentoCuota']['orden_descuento_id']);
		if($orden['OrdenDescuento']['activo'] === '1') return parent::save($datos);
        else return false;
		
//		debug($datos);
//		if(!parent::save($datos)) return false;

//		$oODTO = parent::importarModelo('OrdenDescuento','mutual');
//		$orden = $oODTO->read(null,$datos['OrdenDescuentoCuota']['orden_descuento_id']);
//		debug($orden);
//		if($orden['OrdenDescuento']['permanente'] == 0 && $orden['OrdenDescuento']['cuotas'] != count($orden['OrdenDescuentoCuota'])){
//			debug($orden);
//			$orden['OrdenDescuento']['cuotas'] = count($orden['OrdenDescuentoCuota']);
//			debug($orden);
//			if(!$oODTO->save($orden)) return false;
//		}
//		exit;
//		return true;
	}
	
	/**
	 * Determina si una cuota esta vencida o no
	 * Criterio:  Si la cuota esta A y el periodo es <= al ultimo periodo imputado para el organismo esta vencida
	 * 
	 * @author adrian [01/02/2012]
	 * @param int $id  id de la cuota
	 */
	function isVencida($id){
		$this->unbindModel(array('hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
		$cuota = $this->read("persona_beneficio_id,periodo,estado",$id);
		
		if($cuota['OrdenDescuentoCuota']['estado'] != 'A') return false;
		
		#saco el organismo
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBEN = new PersonaBeneficio();		
		$organismo = $oBEN->getCodigoOrganismo($cuota['OrdenDescuentoCuota']['persona_beneficio_id']);
		
		#controlo con la liquidacion
		App::import('Model','Mutual.Liquidacion');
		$oLIQ = new Liquidacion();		
		
		return $oLIQ->isImputada($organismo, $cuota['OrdenDescuentoCuota']['periodo']);
	}

	
	function getCuotasBajasByOrdenDescuento($orden_descuento_id, $periodo = null){
		$bajas = array('cuotas' => 0, 'importe' => 0);
		$sql = "SELECT SUM(importe) AS importe,COUNT(*) AS cuotas FROM 
				orden_descuento_cuotas as OrdenDescuentoCuota WHERE orden_descuento_id = $orden_descuento_id
				AND estado = 'B' ".(!empty($periodo) ? " AND periodo <= '$periodo'" : "")."
				GROUP BY orden_descuento_id";
		$datos = $this->query($sql);
		if(empty($datos)) return $bajas;
		$bajas['cuotas'] = $datos[0][0]['cuotas'];
		$bajas['importe'] = $datos[0][0]['importe'];
		return $bajas;
	}
	
	
	function cuotasAdeudadasTotalmenteByOrdenDto($orden_descuento_id,$situacion='MUTUSICUMUTU',$filtraConvenios=false,$periodoControl = null){
// 		$periodoControl = (empty($periodoControl) ? date('Ym') : $periodoControl);
// 		$sql = "SELECT *
// 				FROM orden_descuento_cuotas as OrdenDescuentoCuota
// 				WHERE
// 				OrdenDescuentoCuota.orden_descuento_id = $orden_descuento_id
// 				".(!empty($periodoControl) ? "AND OrdenDescuentoCuota.periodo <= '$periodoControl'" : "")."
// 				AND OrdenDescuentoCuota.situacion = '$situacion'
// 				".($filtraConvenios ? " AND OrdenDescuentoCuota.tipo_orden_dto <> 'CONVE'" : "")."
// 				AND OrdenDescuentoCuota.id NOT IN (SELECT orden_descuento_cuota_id FROM orden_descuento_cobro_cuotas);";
		
		$sql = "SELECT *
				FROM orden_descuento_cuotas as OrdenDescuentoCuota
				WHERE
				OrdenDescuentoCuota.orden_descuento_id = $orden_descuento_id
				".(!empty($periodoControl) ? "AND OrdenDescuentoCuota.periodo <= '$periodoControl'" : "")."
				".(!empty($situacion) ? "AND OrdenDescuentoCuota.situacion = '$situacion'" : "")."
				".($filtraConvenios ? " AND OrdenDescuentoCuota.tipo_orden_dto <> 'CONVE'" : "")."
				AND OrdenDescuentoCuota.importe > IFNULL((
											SELECT SUM(cocu.importe) from
											orden_descuento_cobro_cuotas cocu
											INNER JOIN orden_descuento_cobros  co ON (co.id = cocu.orden_descuento_cobro_id) 
											WHERE 
											cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
											".(!empty($periodoControl) ? " AND co.periodo_cobro <= '$periodoControl' " : "").")
											,0);";		
		
		$cuotas = $this->query($sql);
		// debug($sql);
		// exit(0);
		if(!empty($cuotas)) $cuotas = $this->armaInfoAdicional($cuotas);
		
		return $cuotas;
		}	
	
    /**
     * 
     * @param type $socio_id
     * @param type $periodo_desde
     * @param type $periodo_hasta
     * @param type $soloDeuda
     * @param type $proveedor_id
     * @param type $codigo_organismo
     * @param type $discriminaPagos
     * @param type $extract
     * @param type $filtrar (CUOTA, PAGO, SALDO_ANTERIOR)
     * @return type
     */
    function procesa_deuda($socio_id,$periodo_desde,$periodo_hasta,$soloDeuda=false,$proveedor_id=0,$codigo_organismo=null,$discriminaPagos=false,$extract = true,$filtrar = null,$tipo_producto = null,$punitorio = 0,$situacion = NULL){
        
        $periodo_desde = (!empty($periodo_desde) ? $periodo_desde : date('Ym'));
        $periodo_hasta = (!empty($periodo_hasta) ? $periodo_hasta : date('Ym'));
        
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $NRO_CUOTA_PERM = (isset($INI_FILE['general']['numera_cuota_permanete']) && $INI_FILE['general']['numera_cuota_permanete'] != 0 ? $INI_FILE['general']['numera_cuota_permanete'] : 0);
        $NRO_CUOTA_PERM = ($NRO_CUOTA_PERM != 0 ? 1 : 0);
        
        $periodoCortePunitorio = ($periodo_hasta > date('Ym') ? date('Ym') : $periodo_hasta);
        
        
        $sql = "select * from ((select 'SALDO_ANTERIOR' as tipo_registro,0 as socio_id,".(!empty($proveedor_id) ? " $proveedor_id " : "0")." as proveedor_id,
                ".(!empty($codigo_organismo) ? " '$codigo_organismo' " : " null ")." as codigo_beneficio, 
                ".(!empty($tipo_producto) ? " '$tipo_producto' " : " null ")." as tipo_producto,    
                0 as id,
                null as periodo,
                0 as orden_descuento_id,
                null as tipo_orden_dto,
                null as numero,
                null as permanente,
                null as organismo,
                null as tipo_numero,
                null as cod_nro,
                null as proveedor_producto,
                null as producto,
                null as cuota,
                'SALDO ANTERIOR' as tipo_cuota,
                '' as tipo_cobro,
                null  as vencimiento,
                null as estado,
                null as situacion_cuota,
                0 as importe,
                0 as pagado,
                0 as pendiente,                
                ifnull(sum(if(OrdenDescuentoCuota.estado NOT IN ('B','D'),OrdenDescuentoCuota.importe - (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0)),0) as saldo_conciliado,
                ifnull(sum(if(OrdenDescuentoCuota.estado NOT IN ('B','D'),OrdenDescuentoCuota.importe - (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id) - ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
                                    WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
                                    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                    order by liquidacion_id),0),0)),0) as saldo,
                0 as reversado,
                0 as cancelacion_orden_id,
                0 as liquidacion_id
                ,0 as importe_solicitado
                ,0 as importe_en_cancelacion
                ,0 as persona_beneficio_id
                ,'' as periodos_adeuda
                ,0 as punitorio
                ,0 as punitorios
                ,NULL as tipo_cuota_punitorio
                from orden_descuento_cuotas OrdenDescuentoCuota
                inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                inner join proveedores Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
                inner join socios Socio on (Socio.id = OrdenDescuentoCuota.socio_id)
                inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                inner join personas Persona on (Persona.id = Socio.persona_id)
                inner join global_datos CodigoBeneficio on (CodigoBeneficio.id = PersonaBeneficio.codigo_beneficio)
                inner join global_datos TipoProducto on (TipoProducto.id = OrdenDescuentoCuota.tipo_producto)
                inner join global_datos TipoCuota on (TipoCuota.id = OrdenDescuentoCuota.tipo_cuota)
                inner join global_datos SituacionCuota on (SituacionCuota.id = OrdenDescuentoCuota.situacion)
                where 
                OrdenDescuentoCuota.socio_id = $socio_id
                ".($soloDeuda ?  " and 1 = 2 " : " and OrdenDescuentoCuota.periodo < '$periodo_desde' ")."    
                ".(!empty($proveedor_id) ? " and OrdenDescuentoCuota.proveedor_id = $proveedor_id " : "")."
                ".(!empty($codigo_organismo) ? " and PersonaBeneficio.codigo_beneficio = '$codigo_organismo' " : "")." 
                ".(!empty($tipo_producto) ? " and OrdenDescuentoCuota.tipo_producto = '$tipo_producto' " : "")."  
                ".(!empty($situacion) ? " and OrdenDescuentoCuota.situacion = '$situacion' " : "")."    
                AND OrdenDescuentoCuota.importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id))
                union 
                (select 'CUOTA' as tipo_registro,OrdenDescuentoCuota.socio_id,OrdenDescuentoCuota.proveedor_id,
                PersonaBeneficio.codigo_beneficio,  
                OrdenDescuentoCuota.tipo_producto as tipo_producto,
                OrdenDescuentoCuota.id as id,
                OrdenDescuentoCuota.periodo,
                OrdenDescuento.id as orden_descuento_id,
                OrdenDescuento.tipo_orden_dto,
                OrdenDescuento.numero,
                OrdenDescuento.permanente,
                CodigoBeneficio.concepto_1 as organismo,
                concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_numero,
                concat(OrdenDescuentoCuota.codigo_comercio_referencia,'-',OrdenDescuentoCuota.nro_orden_referencia) as cod_nro,
                if(ifnull(OrdenDescuentoCuota.nro_referencia_proveedor,'') <> '',concat(ifnull(Proveedor.razon_social_resumida,Proveedor.razon_social),' / ',
                TipoProducto.concepto_1,' (REF: ',
                OrdenDescuentoCuota.nro_referencia_proveedor,')'),concat(ifnull(Proveedor.razon_social_resumida,Proveedor.razon_social),' / ',
                TipoProducto.concepto_1)) as proveedor_producto,   
                TipoProducto.concepto_1 as producto,
                if(OrdenDescuento.permanente = 1 and 1 = $NRO_CUOTA_PERM,concat((select lpad(trim(cast(count(*) as char(5))),2,0) from orden_descuento_cuotas c1 where c1.orden_descuento_id = OrdenDescuento.id and c1.periodo <= OrdenDescuentoCuota.periodo),'/',
                (select lpad(trim(cast(count(*) as char(5))),2,0) from orden_descuento_cuotas c1 where c1.orden_descuento_id = OrdenDescuento.id)),concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',
                lpad(if(OrdenDescuento.permanente = 1,0,OrdenDescuento.cuotas),2,0))) as cuota,                
                TipoCuota.concepto_1 as tipo_cuota,
                '' as tipo_cobro,
                OrdenDescuentoCuota.vencimiento,
                if(OrdenDescuentoCuota.importe - (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id) = 0, 'P', OrdenDescuentoCuota.estado)
                as estado,
                SituacionCuota.concepto_1 as situacion_cuota,
                OrdenDescuentoCuota.importe,
                (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id) as pagado,
                ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
                                    WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
                                    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                    order by liquidacion_id),0) as pendiente,                
                if(OrdenDescuentoCuota.estado NOT IN ('B','D'),OrdenDescuentoCuota.importe - (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) as saldo_conciliado,
                if(OrdenDescuentoCuota.estado NOT IN ('B','D'),OrdenDescuentoCuota.importe - (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id) - ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
                                    WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
                                    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                    order by liquidacion_id),0),0) as saldo,
                0 as reversado,
                0 as cancelacion_orden_id,
                ifnull((SELECT 
				Liquidacion.id
				FROM liquidacion_cuotas AS LiquidacionCuota
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
				WHERE LiquidacionCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                AND Liquidacion.cerrada = 1 and Liquidacion.imputada = 0
				ORDER BY Liquidacion.id DESC
				LIMIT 1),0) as liquidacion_id
                ,ifnull(MutualProductoSolicitud.importe_solicitado,0) as importe_solicitado
                ,(select IFNULL(SUM(coc.importe),0) as importe_en_cancelacion from cancelacion_ordenes co
                inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = co.id
                where coc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                and co.estado = 'E') as importe_en_cancelacion
                ,OrdenDescuentoCuota.persona_beneficio_id as persona_beneficio_id 
                ,PERIOD_DIFF('$periodoCortePunitorio',OrdenDescuentoCuota.periodo) as periodos_adeuda
                ,ifnull(adicional.valor,0) as punitorio
                ,if(OrdenDescuentoCuota.estado NOT IN ('B','D'),OrdenDescuentoCuota.importe - (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id) - ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
                                    WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
                                    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                    order by liquidacion_id),0),0) * PERIOD_DIFF('$periodoCortePunitorio',OrdenDescuentoCuota.periodo) * (ifnull(adicional.valor,0)/100) as punitorios    
                ,adicional.tipo_cuota as tipo_cuota_punitorio
                from orden_descuento_cuotas OrdenDescuentoCuota
                inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                inner join proveedores Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
                inner join socios Socio on (Socio.id = OrdenDescuentoCuota.socio_id)
                inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                inner join personas Persona on (Persona.id = Socio.persona_id)
                inner join global_datos CodigoBeneficio on (CodigoBeneficio.id = PersonaBeneficio.codigo_beneficio)
                inner join global_datos TipoProducto on (TipoProducto.id = OrdenDescuentoCuota.tipo_producto)
                inner join global_datos TipoCuota on (TipoCuota.id = OrdenDescuentoCuota.tipo_cuota)
                inner join global_datos SituacionCuota on (SituacionCuota.id = OrdenDescuentoCuota.situacion)
                left join mutual_producto_solicitudes MutualProductoSolicitud on (MutualProductoSolicitud.orden_descuento_id = OrdenDescuento.id and MutualProductoSolicitud.tipo_producto = OrdenDescuento.tipo_producto and MutualProductoSolicitud.socio_id = OrdenDescuento.socio_id)
                left join mutual_adicionales adicional on (
                    adicional.codigo_organismo = PersonaBeneficio.codigo_beneficio
                    and adicional.activo = 1
                    and adicional.deuda_calcula = 5
                    and adicional.periodo_desde >= OrdenDescuentoCuota.periodo
					and adicional.periodo_hasta >= OrdenDescuentoCuota.periodo
					and adicional.proveedor_id = OrdenDescuentoCuota.proveedor_id
                )                
                where 
                OrdenDescuentoCuota.socio_id = $socio_id
                ".(!empty($proveedor_id) ? " and OrdenDescuentoCuota.proveedor_id = $proveedor_id " : "")."
                ".(!empty($codigo_organismo) ? " and PersonaBeneficio.codigo_beneficio = '$codigo_organismo' " : "")." 
                ".(!empty($tipo_producto) ? " and OrdenDescuentoCuota.tipo_producto = '$tipo_producto' " : "")."
                ".(!empty($situacion) ? " and OrdenDescuentoCuota.situacion = '$situacion' " : " ")."  
                ".(!$soloDeuda ? " " : " and OrdenDescuentoCuota.estado NOT IN ('B','D') ")."  
                    
                ".($soloDeuda ? "and OrdenDescuentoCuota.periodo <= '$periodo_hasta' and ABS(OrdenDescuentoCuota.importe) > (select ifnull(sum(ABS(importe)),0) from orden_descuento_cobro_cuotas ccu
                
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id) " : " and OrdenDescuentoCuota.periodo BETWEEN '$periodo_desde' AND '$periodo_hasta' ").")
                
                union
                (select 'PAGO' as tipo_registro,OrdenDescuentoCuota.socio_id,OrdenDescuentoCuota.proveedor_id,
                PersonaBeneficio.codigo_beneficio,
                OrdenDescuentoCuota.tipo_producto as tipo_producto,
                OrdenDescuentoCuota.id as id,
                OrdenDescuentoCuota.periodo,
                OrdenDescuento.id as orden_descuento_id,
                OrdenDescuento.tipo_orden_dto,
                OrdenDescuento.numero,
                OrdenDescuento.permanente,
                CodigoBeneficio.concepto_1 as organismo, 
                concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_numero,
                concat(OrdenDescuentoCuota.codigo_comercio_referencia,'-',OrdenDescuentoCuota.nro_orden_referencia) as cod_nro,
                if(ifnull(OrdenDescuentoCuota.nro_referencia_proveedor,'') <> '',concat(ifnull(Proveedor.razon_social_resumida,Proveedor.razon_social),' / ',
                TipoProducto.concepto_1,' (REF: ',
                OrdenDescuentoCuota.nro_referencia_proveedor,')'),concat(ifnull(Proveedor.razon_social_resumida,Proveedor.razon_social),' / ',
                TipoProducto.concepto_1)) as proveedor_producto,  
                TipoProducto.concepto_1 as producto,
                if(OrdenDescuento.permanente = 1,concat((select lpad(trim(cast(count(*) as char(5))),2,0) from orden_descuento_cuotas c1 where c1.orden_descuento_id = OrdenDescuento.id and c1.periodo <= OrdenDescuentoCuota.periodo),'/',
                (select lpad(trim(cast(count(*) as char(5))),2,0) from orden_descuento_cuotas c1 where c1.orden_descuento_id = OrdenDescuento.id)),concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',
                lpad(if(OrdenDescuento.permanente = 1,0,OrdenDescuento.cuotas),2,0))) as cuota, 
                TipoCuota.concepto_1 as tipo_cuota,
                ifnull(TipoCobro.concepto_1,'** COBRO **') as tipo_cobro,
                OrdenDescuentoCobro.fecha,
                if(OrdenDescuentoCuota.importe - (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id) = 0, 'P', OrdenDescuentoCuota.estado)
                as estado,
                OrdenDescuentoCobro.periodo_cobro,
                0 as pagado,
                OrdenDescuentoCobroCuota.importe,
                0 as pendiente,
                0 as saldo_conciliado,
                0 as saldo,
                OrdenDescuentoCobroCuota.reversado as reversado,
                OrdenDescuentoCobro.cancelacion_orden_id as cancelacion_orden_id,
                0 as liquidacion_id
                ,0 as importe_solicitado
                ,0 as importe_en_cancelacion
                ,0 as persona_beneficio_id
                ,'' as periodos_adeuda
                ,0 as punitorio
                ,0 as punitorios
                ,NULL as tipo_cuota_punitorio
                from orden_descuento_cuotas OrdenDescuentoCuota
                inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                inner join proveedores Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
                inner join socios Socio on (Socio.id = OrdenDescuentoCuota.socio_id)
                inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                inner join personas Persona on (Persona.id = Socio.persona_id)
                inner join global_datos CodigoBeneficio on (CodigoBeneficio.id = PersonaBeneficio.codigo_beneficio)
                inner join global_datos TipoProducto on (TipoProducto.id = OrdenDescuentoCuota.tipo_producto)
                inner join global_datos TipoCuota on (TipoCuota.id = OrdenDescuentoCuota.tipo_cuota)
                inner join global_datos SituacionCuota on (SituacionCuota.id = OrdenDescuentoCuota.situacion)
                left join orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota on (OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
                inner join orden_descuento_cobros OrdenDescuentoCobro on (OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id)
                left join global_datos TipoCobro on (TipoCobro.id = OrdenDescuentoCobro.tipo_cobro)
                where 
                OrdenDescuentoCuota.socio_id = $socio_id
                ".(!empty($proveedor_id) ? " and OrdenDescuentoCuota.proveedor_id = $proveedor_id " : "")."
                ".(!empty($codigo_organismo) ? " and PersonaBeneficio.codigo_beneficio = '$codigo_organismo' " : "")." 
                ".(!empty($tipo_producto) ? " and OrdenDescuentoCuota.tipo_producto = '$tipo_producto' " : "")."
                ".(!empty($situacion) ? " and OrdenDescuentoCuota.situacion = '$situacion' " : "")."    
                    
                ".($soloDeuda ? "and OrdenDescuentoCuota.periodo <= '$periodo_hasta' and OrdenDescuentoCuota.estado NOT IN ('B','D') 
                and ABS(OrdenDescuentoCuota.importe) > (select ifnull(sum(ABS(importe)),0) from orden_descuento_cobro_cuotas ccu
                where ccu.orden_descuento_cuota_id = OrdenDescuentoCuota.id) " : " and OrdenDescuentoCuota.periodo BETWEEN '$periodo_desde' AND '$periodo_hasta' ").")) 
                    as estado_cuenta
                where 
                    1 = 1 
                ".(!empty($proveedor_id) ? " and proveedor_id = $proveedor_id" : "")."
                ".(!empty($codigo_organismo) ? " and codigo_beneficio = '$codigo_organismo'" : "")." 
                ".(!empty($tipo_producto) ? " and tipo_producto = '$tipo_producto' " : "")."     
                order by periodo,orden_descuento_id,cuota,id,tipo_registro;";
//        debug($sql);
//        exit;
        $datos = $this->query($sql);
        if(!empty($filtrar)) $datos = Set::extract("/estado_cuenta[tipo_registro=$filtrar]",$datos);

        if($extract)$cuotas = Set::extract('{n}.estado_cuenta',$datos);
        else $cuotas = $datos;
//        debug($cuotas);
//        exit;
        return $cuotas;
    }    
        
    
    
    function modificar_cuota($data){
        parent::begin();
        if(!$this->save($data)){
            parent::rollback();
            return FALSE;
        }
        if(isset($data['OrdenDescuentoCuota']['aplicar_todas'])){
            $update = array(
                    'OrdenDescuentoCuota.importe' => $data['OrdenDescuentoCuota']['importe'],
            );
            if(!$this->updateAll($update,array('OrdenDescuentoCuota.orden_descuento_id' => $data['OrdenDescuentoCuota']['orden_descuento_id']))){
                parent::rollback();
                return FALSE;
            }            
        }
        parent::commit();
        return TRUE;

    }
    
    public function getCuotaSocialImportes($socioId,$periodoControl = NULL){
        $periodo = (empty($periodoControl) ? date('Ym') : $periodoControl);
        $importes = NULL;
        $sql = "select cu.importe, ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cobros co on co.id = cc.orden_descuento_cobro_id
                where co.periodo_cobro <= cu.periodo and cc.orden_descuento_cuota_id = cu.id),0) as pagado
                from orden_descuento_cuotas cu
                where cu.socio_id = $socioId and cu.tipo_cuota = 'MUTUTCUOCSOC'
                and cu.periodo = '$periodo';";
        $datos = $this->query($sql);
        if(!empty($datos)){
            $importes = array(
                'importe' => $datos[0]['cu']['importe'],
                'pagado' => $datos[0][0]['pagado'],
                'saldo' => round(($datos[0]['cu']['importe'] - $datos[0][0]['pagado']),2),
            );
        }
        return $importes;
    }
    
    public function getDatosParaCombosEstadoCuenta($socioId,$tipo = 'PRV'){
        $combo = array();
        
        if($tipo === 'PROV'){
            $sql = "select 
                    t1.proveedor_id as id,t2.razon_social as concepto 
                    from orden_descuento_cuotas t1
                    inner join proveedores t2 on t2.id = t1.proveedor_id
                    where socio_id = $socioId
                    group by t1.proveedor_id order by concepto;";
        }
        if($tipo === 'PROD'){
            $sql = "select 
                    t1.tipo_producto as id,t2.concepto_1 as concepto 
                    from orden_descuento_cuotas t1
                    inner join global_datos t2 on t2.id = t1.tipo_producto
                    where socio_id = $socioId
                    group by t1.tipo_producto order by concepto;";
        }
        if($tipo === 'SITU'){
            $sql = "select 
                    t1.situacion as id,t2.concepto_1 as concepto 
                    from orden_descuento_cuotas t1
                    inner join global_datos t2 on t2.id = t1.situacion
                    where socio_id = $socioId
                    group by t1.situacion order by concepto;";
        } 
        if($tipo === 'ORGA'){
            $sql = "select 
                    t1.codigo_beneficio as id,t2.concepto_1 as concepto 
                    from orden_descuento_cuotas t3
                    inner join persona_beneficios t1 on t1.id = t3.persona_beneficio_id
                    inner join global_datos t2 on t2.id = t1.codigo_beneficio
                    where socio_id = $socioId
                    group by t1.codigo_beneficio order by concepto;";
        }        
        $datos = $this->query($sql);
        if(!empty($datos)){
            foreach($datos as $dato){
                $combo[$dato['t1']['id']] = $dato['t2']['concepto'];
            }
        }
        return $combo;
    }    
    
    
    public function getTotalOperacionesPorSituacionDeuda($socioId){
        $sql = "select
                socio_id 
                ,si.concepto_1
                ,count(distinct cu.orden_descuento_id) as operaciones
                from orden_descuento_cuotas cu 
                inner join global_datos si on si.id = cu.situacion
                where socio_id = $socioId
                group by cu.situacion order by concepto_1;";
        $datos = $this->query($sql);
        return $datos;
    }    
    
    
    function getCuotasBy($id) {
        $sql = "";
        
    }
}
?>

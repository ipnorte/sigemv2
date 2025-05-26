<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class OrdenDescuentoCobro extends MutualAppModel{
	
	var $name = 'OrdenDescuentoCobro';
	var $hasMany = array('OrdenDescuentoCobroCuota');
//	var $actsAs   = array('Mutual.transaction');
	
	function getCobro($id, $lDescuentoDetalle=false){
		$this->bindModel(array('hasMany' => array('OrdenDescuentoCobroCuota')));
		$cobro = $this->read(null,$id);
//		if($cobro['OrdenDescuentoCobro']['socio_id'] != 0){
//			App::import('Model','Pfyj.Socio');
//			$oSOCIO = new Socio();	
//			$socio = $oSOCIO->read(null,$cobro['OrdenDescuentoCobro']['socio_id']);
//			$glb = parent::getGlobalDato('concepto_1',$socio['Persona']['tipo_documento']);
//			
//			$d1 = "SOCIO #".$socio['Socio']['id'];
//			$d1 = str_pad($d1,12," ",STR_PAD_RIGHT);
//			
//			$datosSocio =  $d1 .' - ' .$glb['GlobalDato']['concepto_1'] .' '.$socio['Persona']['documento'];
//			$datosSocio .= " :: ".$socio['Persona']['apellido'].', '.$socio['Persona']['nombre'];
//			$cobro['Socio']['str'] = $datosSocio;	
//		}
//		if(!empty($cobro['OrdenDescuentoCobroCuota'])){
//			App::import('Model','Mutual.OrdenDescuentoCuota');
//			$oCUOTA = new OrdenDescuentoCuota();			
//			foreach($cobro['OrdenDescuentoCobroCuota'] as $idx => $cobroCuota){
//				$cuota = $oCUOTA->getCuota($cobroCuota['orden_descuento_cuota_id']);
//				$cobroCuota['OrdenDescuentoCuota'] = $cuota['OrdenDescuentoCuota'];
//				$cobro['OrdenDescuentoCobroCuota'][$idx] = $cobroCuota;
//			}
//			
//		}
		return $this->armaDatos($cobro, $lDescuentoDetalle);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param int $cobro
	 * @param boolean $lDescuentoDetalle
	 * @return Array
	 */
	function armaDatos($cobro, $lDescuentoDetalle=false){
	    if(empty($cobro)){ return null;}

                
        App::import('model','mutual.CancelacionOrden');
        $oCancelacionOrden = new CancelacionOrden();
                
//		$this->CancelacionOrden = $this->importarModelo('CancelacionOrden', 'mutual');
		
		$cobro['OrdenDescuentoCobro']['tipo_cobro_desc'] = parent::GlobalDato('concepto_1',$cobro['OrdenDescuentoCobro']['tipo_cobro']);
		if($cobro['OrdenDescuentoCobro']['socio_id'] != 0){
			App::import('Model','Pfyj.Socio');
			$oSOCIO = new Socio();	
			$socio = $oSOCIO->read(null,$cobro['OrdenDescuentoCobro']['socio_id']);
			$glb = parent::getGlobalDato('concepto_1',$socio['Persona']['tipo_documento']);
			
			$d1 = "SOCIO #".$socio['Socio']['id'];
			$d1 = str_pad($d1,12," ",STR_PAD_RIGHT);
			
			$datosSocio =  $d1 .' - ' .$glb['GlobalDato']['concepto_1'] .' '.$socio['Persona']['documento'];
			$datosSocio .= " :: ".$socio['Persona']['apellido'].', '.$socio['Persona']['nombre'];
			$cobro['Socio']['str'] = $datosSocio;
			$cobro['OrdenDescuentoCobro']['destinatario'] = $datosSocio;
		}
		
		$ACU_REVERSO = 0;
        $reversos = array();
		if(!empty($cobro['OrdenDescuentoCobroCuota'])){
		    
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();
            App::import('model','cajabanco.BancoCuentaMovimiento');
            $oBcoCtaMov = new BancoCuentaMovimiento();
            App::import('model','cajabanco.BancoCuenta');
            $oBcoCta = new BancoCuenta();    
            
            
            App::import('Model','Mutual.OrdenDescuentoCuotaService');
            $oCUOTASERVICE = new OrdenDescuentoCuotaService();
            
			foreach($cobro['OrdenDescuentoCobroCuota'] as $idx => $cobroCuota){
			    
			    $cuota = $oCUOTASERVICE->getCuota($cobroCuota['orden_descuento_cuota_id']);
// 				$cuota = $oCUOTA->getCuota($cobroCuota['orden_descuento_cuota_id']);
				$cobroCuota['OrdenDescuentoCuota'] = $cuota['OrdenDescuentoCuota'];
				$cobro['OrdenDescuentoCobroCuota'][$idx] = $cobroCuota;
                  if($cobroCuota['reversado'] == '1'){
                      $ACU_REVERSO += $cobroCuota['importe_reversado'];
                        $tmp = array();
                        $tmp['reverso_cuota'] = "ORDEN #" . $cobroCuota['OrdenDescuentoCuota']['orden_descuento_id'] . " - " . $cobroCuota['OrdenDescuentoCuota']['tipo_nro']." - CUOTA: ".$cobroCuota['OrdenDescuentoCuota']['cuota'];
                        $tmp['reverso_proveedor_producto'] = $cobroCuota['OrdenDescuentoCuota']['proveedor_producto'];
                        $tmp['reverso_importe'] = $cobroCuota['importe_reversado'];
                        $tmp['reverso_periodo_proveedor'] = $cobroCuota['periodo_proveedor_reverso'];
                        $tmp['reverso_usuario'] = $cobroCuota['usuario_reverso'];
                        $tmp['reverso_fecha'] = $cobroCuota['fecha_reverso'];
                        if(!empty($cobroCuota['debito_reverso_id'])){
                            $NDRev = $oCUOTA->getCuota($cobroCuota['debito_reverso_id']);
                            $NDRev = $NDRev['OrdenDescuentoCuota'];
                            $tmp['reverso_debito'] = "ORDEN #" . $NDRev['orden_descuento_id'] . " - " . $NDRev['tipo_nro']." - CUOTA: ".$NDRev['cuota'] ." - ".$NDRev['tipo_cuota_desc'];
                            //"ORDEN #" . $cobroCuota['OrdenDescuentoCuota']['orden_descuento_id'] . " - " . $cobroCuota['OrdenDescuentoCuota']['tipo_nro']." - CUOTA: ".$cobroCuota['OrdenDescuentoCuota']['cuota'];
                        }
                        if(!empty($cobroCuota['banco_cuenta_movimiento_id'])){
                            $OpeBan = $oBcoCtaMov->getMovimientoId($cobroCuota['banco_cuenta_movimiento_id']);
                            $bcoCta = $oBcoCta->getCuenta($OpeBan[0]['BancoCuentaMovimiento']['banco_cuenta_id']);
                            $bcoCta = $bcoCta['BancoCuenta'];
                            $tmp['reverso_cuenta'] = $bcoCta['banco']." - ".$bcoCta['denominacion']." - ".$bcoCta['numero'];
                        } 
                        array_push($reversos, $tmp);
                  }      
//                if(isset($cobro['OrdenDescuentoCobroCuota']['reversado'])){
//                    if($cobro['OrdenDescuentoCobroCuota']['reversado'] == 1){
//                        $ACU_REVERSO += $cobroCuota['OrdenDescuentoCuota']['importe_reversado'];
//
//                    }
//                }
                
			}
            $cobro['reversos'] = $reversos;
			
		}
		
		$cobro['OrdenDescuentoCobro']['observaciones'] = '';
		if($cobro['OrdenDescuentoCobro']['cancelacion_orden_id'] > 0):
			$aObservaciones = $oCancelacionOrden->read('CancelacionOrden.observaciones', $cobro['OrdenDescuentoCobro']['cancelacion_orden_id']);
//			if(!empty($aObservaciones['CancelacionOrden']['observaciones'])): 
				$cobro['OrdenDescuentoCobro']['observaciones'] = $aObservaciones['CancelacionOrden']['observaciones'];
//			endif;
		endif;
		
		$cobro['OrdenDescuentoCobro']['total_reversado'] = $ACU_REVERSO;
		$cobro['OrdenDescuentoCobro']['Recibo'] = null;
		if(!empty($cobro['OrdenDescuentoCobro']['recibo_id'])){
			App::import('Model','clientes.Recibo');
			$oRECIBO = new Recibo();
			$recibo = $oRECIBO->getRecibo($cobro['OrdenDescuentoCobro']['recibo_id'],false,false);
			$cobro['OrdenDescuentoCobro']['Recibo'] = $recibo['Recibo'];
		}
		
		
		if($lDescuentoDetalle) $cobro['ReciboDetalle'] = $this->ReciboDetalle($cobro);
		
		if($lDescuentoDetalle) $cobro['ProveedorLiquidacion'] = $this->ProveedorLiquidacion($cobro);
		
//        $sql = "select b.nombre,lsr.cbu,lsr.sucursal,lsr.nro_cta_bco, fecha_debito,sum(importe_debitado) as importe_debitado  from liquidacion_socio_rendiciones lsr
//                inner join bancos b on (b.id = lsr.banco_id)
//                where lsr.orden_descuento_cobro_id = ".$cobro['OrdenDescuentoCobro']['id']."
//                group by lsr.cbu,fecha_debito;";  
        
        $sql = "select b.nombre,lsr.cbu,lsr.sucursal,lsr.nro_cta_bco, fecha_debito,sum(importe_debitado) as importe_debitado  
                from liquidacion_socio_rendiciones lsr
                inner join liquidacion_intercambios li on (li.id = lsr.liquidacion_intercambio_id)
                inner join bancos b on (b.id = li.banco_id)
                where lsr.orden_descuento_cobro_id = ".$cobro['OrdenDescuentoCobro']['id']."
                group by lsr.cbu,lsr.sucursal,lsr.nro_cta_bco,fecha_debito;     ";

        
        $debitos = $this->query($sql);
        
        $cobro['OrdenDescuentoCobro']['debito_cbu'] = array();
        if(!empty($debitos)){
            foreach($debitos as $debito){
                $tmp = array(
                    'banco' => $debito['b']['nombre'],
                    'cbu' => $debito['lsr']['cbu'],
                    'sucursal' => $debito['lsr']['sucursal'],
                    'nro_cta_bco' => $debito['lsr']['nro_cta_bco'],
                    'fecha_debito' => $debito['lsr']['fecha_debito'],
                    'importe_debito' => $debito[0]['importe_debitado'],
                    
                );
                array_push($cobro['OrdenDescuentoCobro']['debito_cbu'], $tmp);
            }
        }
//         exit;
////        debug($debitos);
//        debug($cobro);
//        exit;
		return $cobro;
	}
	
        
        function get_by_socio($socio_id, $anulados = 0){
            
            $sql = "select OrdenDescuentoCobro.id
                    ,OrdenDescuentoCobro.socio_id
                    ,if(OrdenDescuentoCobro.anulado = 0,'EMITIDO','ANULADO') as estado
                    ,tipo_cobro.concepto_1
                    ,OrdenDescuentoCobro.fecha
                    ,OrdenDescuentoCobro.periodo_cobro
                    ,OrdenDescuentoCobro.importe
                    ,OrdenDescuentoCobro.cancelacion_orden_id
                    ,OrdenDescuentoCobro.user_created
                    ,OrdenDescuentoCobro.created
                    ,OrdenDescuentoCobro.user_modified
                    ,OrdenDescuentoCobro.modified
                    ,OrdenDescuentoCobro.last_ip
                    ,(select ifnull(sum(importe_reversado),0) from orden_descuento_cobro_cuotas ccu
                    where ccu.orden_descuento_cobro_id = OrdenDescuentoCobro.id) as total_reversado
                    ,r.id
                    ,concat(r.tipo_documento,'-'
                    ,r.sucursal,'-'
                    ,r.nro_recibo) as recibo_nro
                    ,r.comentarios
                    ,OrdenDescuentoCobro.observaciones
                    ,OrdenDescuentoCobro.tipo_cobro
                    ,OrdenDescuentoCobro.anulado
                    from orden_descuento_cobros OrdenDescuentoCobro
                    inner join global_datos tipo_cobro on tipo_cobro.id = OrdenDescuentoCobro.tipo_cobro
                    left join recibos r on r.id = OrdenDescuentoCobro.recibo_id
                    where OrdenDescuentoCobro.socio_id = $socio_id and OrdenDescuentoCobro.anulado = $anulados
                    order by OrdenDescuentoCobro.created DESC;";
            $datos = $this->query($sql);
            $cobros = array();
            if(!empty($datos)){
                
                foreach($datos as $i => $dato){
                    $cobros[$i]['OrdenDescuentoCobro'] = $dato['OrdenDescuentoCobro'];
                    $cobros[$i]['OrdenDescuentoCobro']['estado'] = $dato[0]['estado'];
                    $cobros[$i]['OrdenDescuentoCobro']['tipo_cobro_descripcion'] = $dato['tipo_cobro']['concepto_1'];
                    $cobros[$i]['OrdenDescuentoCobro']['total_reversado'] = $dato[0]['total_reversado'];
                    $cobros[$i]['OrdenDescuentoCobro']['recibo_id'] = $dato['r']['id'];
                    $cobros[$i]['OrdenDescuentoCobro']['recibo_comentarios'] = $dato['r']['comentarios'];
                    $cobros[$i]['OrdenDescuentoCobro']['recibo_numero_string'] = $dato[0]['recibo_nro'];
                    
                }
                
            }
//            debug($datos);
//            debug($cobros);
            return $cobros;
            
        }
        
        
	function getCobrosBySocio($socio_id,$bindModel=true){
		$cobros = array();
		$datos = array();
		if(!$bindModel) $this->unbindModel(array('hasMany' => array('OrdenDescuentoCobroCuota'),'belongsTo' => array('Socio')));
		$cobros = $this->find('all',array('conditions' => array('OrdenDescuentoCobro.socio_id' => $socio_id),'order' => array('OrdenDescuentoCobro.fecha DESC')));
		if(!empty($cobros)):
			foreach($cobros as $i => $cobro):
				$totalReversado = 0;
				$cobro = $this->armaDatos($cobro);
				if(!empty($cobro['OrdenDescuentoCobroCuota'])):
					foreach($cobro['OrdenDescuentoCobroCuota'] as $idx => $cobroCuota):
						if($cobroCuota['reversado'] == 1) $totalReversado += $cobroCuota['importe_reversado'];
					endforeach;
				else:
					App::import('Model','mutual.OrdenDescuentoCobroCuota');
					$oCOBCUO = new OrdenDescuentoCobroCuota();
					$totalReversado = $oCOBCUO->getMontoReversadoByCobro($cobro['OrdenDescuentoCobro']['id']);
				endif;
				$cobro['OrdenDescuentoCobro']['total_reversado'] = $totalReversado;
				$cobros[$i] = $cobro;
				$datos[$i]['OrdenDescuentoCobro'] = $cobro['OrdenDescuentoCobro'];
			endforeach;
		endif;
		return $datos;
	}
	
	
	function getCobroByCancelacion($cancelacion_orden_id,$bindCuotas =true){
		$conditions = array();
		$conditions['OrdenDescuentoCobro.cancelacion_orden_id'] = $cancelacion_orden_id;
		$conditions['OrdenDescuentoCobro.anulado'] = 0;
		if(!$bindCuotas) $this->unbindModel(array('hasMany' => array('OrdenDescuentoCobroCuota')));
		$cobros = $this->find('all',array('conditions' => $conditions,'order' => array('OrdenDescuentoCobro.fecha DESC')));
		if(!empty($cobros)):
			foreach($cobros as $i => $cobro):
				$cobros[$i] = $this->armaDatos($cobro);
			endforeach;
		endif;
		if(isset($cobros[0])) return $cobros[0];
		else return null;
	}
	
	/**
	 * Genera un pago por cancelacion.  
	 * Si el periodo de la cancelacion (sale de la fecha) es menor al periodo de cuota que esta cancelando, poner las cuotas al periodo de cancelacion.
	 * si es menor dejar como esta.
	 * @param unknown_type $ordenCancelacion
	 * @param unknown_type $fecha_cobro
	 */
	function generarPagoByCancelacion($ordenCancelacion,$fecha_cobro){
		$this->begin();
		$flag = true;
		$periodo_cobro = date('Ym',strtotime($fecha_cobro));
		#calculo la nota de credito
		$NC = ($ordenCancelacion['CancelacionOrden']['importe_seleccionado'] - $ordenCancelacion['CancelacionOrden']['importe_proveedor']) * (-1);
		
		$cobro = array('OrdenDescuentoCobro' => array(
				'id' => 0,
				'socio_id' => $ordenCancelacion['CancelacionOrden']['socio_id'],
				'tipo_cobro' => ($ordenCancelacion['CancelacionOrden']['orden_proveedor_id'] != MUTUALPROVEEDORID && $ordenCancelacion['CancelacionOrden']['origen_proveedor_id'] == $ordenCancelacion['CancelacionOrden']['orden_proveedor_id'] ? 'MUTUTCOBCCOM' : 'MUTUTCOBCANC'),
				'fecha' => $fecha_cobro,
				'periodo_cobro' => $periodo_cobro,
				'importe' => $ordenCancelacion['CancelacionOrden']['importe_proveedor'],
				'proveedor_origen_fondo_id' => $ordenCancelacion['CancelacionOrden']['origen_proveedor_id'],
				'cancelacion_orden_id' => $ordenCancelacion['CancelacionOrden']['id']
		));
		
		$calculaComision = false;
		if($ordenCancelacion['CancelacionOrden']['tipo_cancelacion'] == 'P')$calculaComision = true;
		
		# PARA CALCULAR LA COMISION POR CANCELACION SE DEBE CONFIGURAR
		# EN LA GLOBAL DATOS PARA FORZAR EL CALCULO DE LA COMISION PARA FACTURAR EN CLIENTE.
		# EL CODIGO DE CONFIGURACION ES "MUTUMUTUCOMC", EN EL CAMPO LOGICO_1 
		# DEBERA ESTAR TILDADO PARA FORZAR LA FACTURACION DE LA COMISION 
		# POR LA RECAUDACION.
		
		$cancelacionComision = parent::getGlobalDato('logico_1', 'MUTUMUTUCOMC');
		$cancelacionComision = isset($cancelacionComision['GlobalDato']['logico_1']) ? $cancelacionComision['GlobalDato']['logico_1'] : 0;

		if($cancelacionComision == 1) $calculaComision = true;
		
		if(!parent::save($cobro)){
			$this->rollback();
			return false;
		}
		
		$idCobro = $this->getLastInsertID();
		
		#proceso las cuotas
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$this->OrdenDescuentoCobroCuota = new OrdenDescuentoCobroCuota();

		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
                
            

                $cuotasCancela = array();
                
                if(empty($ordenCancelacion['CancelacionOrdenCuota'])){
                    App::import('Model','Mutual.CancelacionOrdenCuota');
                    $oCCAN = new CancelacionOrdenCuota();                    
                    $cuotasCancela = $oCCAN->getByOrden($ordenCancelacion['CancelacionOrden']['id']);
                    $cuotasCancela = Set::extract('{n}.CancelacionOrdenCuota',$cuotasCancela);
                }else{
                    $cuotasCancela = $ordenCancelacion['CancelacionOrdenCuota'];
                }
		
                if(empty($cuotasCancela)){
                    $this->rollback();
                    return false;                    
                }
                
		foreach($cuotasCancela as $cuota){
			
			if(!$this->__setPeriodoCobro($cuota['orden_descuento_cuota_id'],$periodo_cobro)){
				$flag = false;
				break;				
			}
			
			$porc = 0;
			$impCom = 0;
			if($calculaComision){
				$comision = $this->OrdenDescuentoCobroCuota->calcularComisionCobranza($cuota['orden_descuento_cuota_id'],$cuota['importe']);
				$porc = $comision['alicuota'];
				$impCom = $comision['comision'];
			}

			$cobro = array('OrdenDescuentoCobroCuota' => array(
				'periodo_cobro' => $periodo_cobro,
				'orden_descuento_cobro_id' => $idCobro,
				'orden_descuento_cuota_id' => $cuota['orden_descuento_cuota_id'],
				'importe' => $cuota['importe'],
				'proveedor_id' => $cuota['proveedor_id'],
				'alicuota_comision_cobranza' => $porc,
				'comision_cobranza' => $impCom
			));

			if(!$this->OrdenDescuentoCobroCuota->grabarPago($cobro)){
				$flag = false;
				break;
			}
//                        exit;
			
		}
		
		if(!$flag){
			$this->rollback();
			return false;
		}
		#genero la nota de credito
		if(abs($NC) != 0){
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCuota = new OrdenDescuentoCuota();
			$oCuota->generarNotaCreditoPorCancelacion($ordenCancelacion['CancelacionOrden']['orden_descuento_id'],$NC,$fecha_cobro,$ordenCancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
			$idNC = $oCuota->getLastInsertID();
			
			$proveedorNC = $oCuota->getProveedor($idNC);
			
			//grabar en la cabecera de la orden de cancelacion el id de la cuota de la nota de credito
			App::import('Model','Mutual.CancelacionOrden');
			$oCANC = new CancelacionOrden();

			$cancelacion = $oCANC->read(null,$ordenCancelacion['CancelacionOrden']['id']);
			$cancelacion['CancelacionOrden']['cuota_diferencia_id'] = $idNC;
			$oCANC->save($cancelacion);
			
			#meto la nota de credito en el cobro
			$cobro = array('OrdenDescuentoCobroCuota' => array(
				'periodo_cobro' => $periodo_cobro,
				'orden_descuento_cobro_id' => $idCobro,
				'orden_descuento_cuota_id' => $oCuota->getLastInsertID(),
				'importe' => $NC,
				'proveedor_id' => $proveedorNC
			));			
			if(!$this->OrdenDescuentoCobroCuota->grabarPago($cobro)){
				$flag = false;
//				break;
			}
		}
		if(!$flag){
			$this->rollback();
			return false;
		}else{
			$this->commit();
			return $idCobro;
		}

	}
	
	/**
	 * Genera un pago desde una orden de cobro por caja.  Si el periodo de cobro es menor al periodo de la cuota
	 * llevar el periodo de la cuota al periodo de cobro.
	 * @param $data
	 */
	function generarPagoByOrdenCobroByCaja($data){
		$flag = false;
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$this->OrdenDescuentoCuota = new OrdenDescuentoCuota();
		
		App::import('Model','Mutual.OrdenCajaCobroCuota');
		$this->OrdenCajaCobroCuota = new OrdenCajaCobroCuota();	

		App::import('Model','Mutual.OrdenCajaCobro');
		$this->OrdenCajaCobro = new OrdenCajaCobro();	
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$this->OrdenDescuentoCobroCuota = new OrdenDescuentoCobroCuota();	

		App::import('Model','Proveedores.ProveedorComision');
		$oCOMISION = new ProveedorComision();		
		
		########################
		#ABRIR UNA TRANSACCION
		########################
		$this->begin();
		
		#GRABAR LA CABECERA DEL COBRO
		$fechaCobro = parent::armaFecha($data['OrdenDescuentoCobro']['fecha']);
		$data['OrdenDescuentoCobro']['tipo_cobro'] = (!empty($data['OrdenDescuentoCobro']['tipo_cobro']) ? $data['OrdenDescuentoCobro']['tipo_cobro'] : 'MUTUTCOBCAJA');
		$data['OrdenDescuentoCobro']['fecha'] = $fechaCobro;
		$data['OrdenDescuentoCobro']['importe'] = $data['OrdenCajaCobro']['orden_caja_cobro_importe'];
//		$this->rollback();

		//saco las cuotas de la orden
		$cuotas = $this->OrdenCajaCobroCuota->findAllByOrdenCajaCobroId($data['OrdenDescuentoCobro']['orden_caja_cobro_id']);

		$proveedor_anterior_id = 0;
		$lDistintoProveedor = false;
		foreach($cuotas as $cuota){
			
			$proveedor_id = $this->OrdenDescuentoCuota->field('proveedor_id',"OrdenDescuentoCuota.id = ".$cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
			if($proveedor_anterior_id == 0) $proveedor_anterior_id = $proveedor_id;
			if($proveedor_anterior_id != $proveedor_id) $lDistintoProveedor = true;
		}

		if($lDistintoProveedor):
			parent::notificar('LA ORDEN TIENE DISTINTOS PROVEEDORES');
			return false;
		endif;

		$periodo_cobro = date('Ym',strtotime($data['OrdenDescuentoCobro']['fecha']));
		$data['OrdenDescuentoCobro']['periodo_cobro'] = $periodo_cobro;
		$data['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] = $proveedor_id;

		$this->id = 0;
		
		$flag = parent::save($data);
		$id = $this->getLastInsertID();
		
	
		if(!$flag){
			$this->rollback();
			return $flag;
		}
		
		#GRABAR EL DETALLE DEL COBRO
		//saco las cuotas de la orden
		$cuotas = $this->OrdenCajaCobroCuota->findAllByOrdenCajaCobroId($data['OrdenDescuentoCobro']['orden_caja_cobro_id']);

		foreach($cuotas as $cuota){
			
			$proveedor_id = $this->OrdenDescuentoCuota->field('proveedor_id',"OrdenDescuentoCuota.id = ".$cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
			
			#CALCULO LA COMISION POR LA COBRANZA
			$comision = $this->OrdenDescuentoCobroCuota->calcularComisionCobranza($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'],$cuota['OrdenCajaCobroCuota']['importe_abonado']);
				
			#GUARDO EL DETALLE DEL PAGO DE LA CUOTA
			$cobroCuota = array();
			$cobroCuota['OrdenDescuentoCobroCuota'] = array();
			$cobroCuota['OrdenDescuentoCobroCuota']['periodo_cobro'] = $periodo_cobro;
			$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'] = $id;
			$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'] = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
			$cobroCuota['OrdenDescuentoCobroCuota']['importe'] = $cuota['OrdenCajaCobroCuota']['importe_abonado'];
			$cobroCuota['OrdenDescuentoCobroCuota']['proveedor_id'] = $proveedor_id;
			$cobroCuota['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'] = $comision['alicuota'];
			$cobroCuota['OrdenDescuentoCobroCuota']['comision_cobranza'] = $comision['comision'];
			
			
			if($this->OrdenDescuentoCobroCuota->save($cobroCuota)){
				$flag = true;
				#MARCO LA CUOTA COMO PAGADA SI LA PAGA TOTALMENTE
				if($cuota['OrdenCajaCobroCuota']['importe_abonado'] == $cuota['OrdenCajaCobroCuota']['importe']){
					$this->OrdenDescuentoCuota->id = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
					if(!$this->OrdenDescuentoCuota->saveField('estado','P')){
						$flag = false;
						break;
					}
					if(!$this->__setPeriodoCobro($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'],$periodo_cobro)){
						$flag = false;
						break;				
					}					
//					//si el periodo de la cuota es mayor al periodo de cobro llevo el periodo de la cuota al periodo de cobro
//					$periodoCuota = $this->OrdenDescuentoCuota->getPeriodo($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
//					if($periodo_cobro < $periodoCuota && !empty($periodoCuota)){
//						$this->OrdenDescuentoCuota->id = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
//						$periodo_origen = $this->OrdenDescuentoCuota->read('periodo',$cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
//						$this->OrdenDescuentoCuota->saveField('periodo_origen',$periodo_origen);						
//						$flag = $this->OrdenDescuentoCuota->saveField('periodo',$periodo_cobro);
//					}
				}
			}else{
				$flag = false;
				break;				
			}
			$this->OrdenDescuentoCobroCuota->id = 0;
		}

		if(!$flag){
			$this->rollback();
			return $flag;
		}		
		
		#MARCO LA ORDEN DE COBRO POR CAJA COMO PROCESADA
		$flag = $this->OrdenCajaCobro->marca_procesada($data['OrdenDescuentoCobro']['orden_caja_cobro_id']);
		
		# ACTUALIZO LA ORDEN DE COBRO POR CAJA CON EL ID DEL COBRO
		$aOrdenCajaCobro = array('OrdenCajaCobro' => array('id' => $data['OrdenDescuentoCobro']['orden_caja_cobro_id'], 'orden_descuento_cobro_id' => $id));
		$this->OrdenCajaCobro->id = $data['OrdenDescuentoCobro']['orden_caja_cobro_id'];
		if(!$this->OrdenCajaCobro->save($aOrdenCajaCobro)):
			$this->rollback();
			return false;
		endif;
		
		########################
		# Genero la Provedor Liquidaciones con el id de la orden descuento cobro.
		########################
/*
 * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
 * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
 * esta funcion queda obsoleta. 26/06/2017
		$this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
		if(!$this->ProveedorLiquidacion->grabarLiquidacionByCaja($id)):
			$this->rollback();
			return false;
		endif;
 * 
 */
		
		
		//		$flag = false;
		
		########################
		#TODO BIEN --> CERRAR LA TRANSACCION
		########################
		if($flag)$this->commit();
		else $this->rollback();
//		exit;
		return $flag;		
	}
	
	/**
	 * actualiza el periodo de la cuota al periodo del cobro, si el periodo de cobro es menor al de la cuota
	 * mueve los periodos de la cuota al periodo de cobro y guarda el periodo original
	 * @param unknown_type $orden_descuento_cuota_id
	 * @param unknown_type $periodo_cobro
	 */
	function __setPeriodoCobro($orden_descuento_cuota_id,$periodo_cobro){
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
        
        $cuota = $oCUOTA->read(NULL,$orden_descuento_cuota_id);
        $periodoCuota = $cuota['OrdenDescuentoCuota']['periodo'];
        
        if($periodo_cobro < $periodoCuota && !empty($periodoCuota)){
            $cuota['OrdenDescuentoCuota']['periodo'] = $periodo_cobro;
            $cuota['OrdenDescuentoCuota']['periodo_origen'] = $periodoCuota;
        }
        
        return $oCUOTA->save($cuota);

//		$periodoCuota = $oCUOTA->getPeriodo($orden_descuento_cuota_id);
//		if($periodo_cobro < $periodoCuota && !empty($periodoCuota)){
//			$oCUOTA->id = $orden_descuento_cuota_id;
//			$periodo_origen = $oCUOTA->read('periodo',$orden_descuento_cuota_id);
//			if(!$oCUOTA->saveField('periodo_origen',$periodoCuota)) return false;
//			if(!$oCUOTA->saveField('periodo',$periodo_cobro)) return false;
//		}
//		return true;				
	}
	
	
	function recalcularCobro($id){
		$cobro = $this->read(null,$id);
		$suma = 0;
		if(empty($cobro['OrdenDescuentoCobroCuota'])){
			$cobro['OrdenDescuentoCobro']['anulado'] = 1;
			return $this->save($cobro);
		}
		foreach($cobro['OrdenDescuentoCobroCuota'] as $detalle){
			$suma += $detalle['importe'];
		}
		if($suma!=0)$cobro['OrdenDescuentoCobro']['importe'] = $suma;
		return $this->save($cobro);
	}
	
	/**
	 * borra el detalle de una orden de cobro, por defecto marca como anulado la orden
	 * @param unknown_type $id
	 * @param unknown_type $anula
	 */
	function borrarDetalle($id,$anula=true){
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCobroCuota = new OrdenDescuentoCobroCuota();
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();		
		
		$detalle = $oCobroCuota->find('all',array('conditions' => array('OrdenDescuentoCobroCuota.orden_descuento_cobro_id' => $id)));
		$cuotasOriginalesIDs = array();
		if(!empty($detalle)) $cuotasOriginalesIDs = Set::extract("/OrdenDescuentoCobroCuota/orden_descuento_cuota_id",$detalle);
		if(!empty($cuotasOriginalesIDs)) $oCuota->cambiarEstado($cuotasOriginalesIDs,'A');
//			$oCuota->unbindModel(array('belongsTo' => array('OrdenDescuento','Proveedor','Socio')));
//			$oCuota->updateAll(array("OrdenDescuentoCuota.estado" => "'A'"),array('OrdenDescuentoCuota.id' => $cuotasOriginalesIDs));
//		endif;
		if(!$oCobroCuota->deleteAll("OrdenDescuentoCobroCuota.orden_descuento_cobro_id = $id")) return false;
//		if(!empty($detalle)):
//			foreach($detalle as $cuota):
//				$oCuota->cambiarEstado($cuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'],'A');
//			endforeach;
//		endif;
		
		if($anula){
			$cobro = $this->read(null,$id);
			$cobro['OrdenDescuentoCobro']['anulado'] = 1;
			return $this->save($cobro);
		}else{
			return $this->del($id);
		}

	}
	
	/**
	 * Devuelve los cobros para un proveedor / periodo
	 * que no hayan sido generados por recibo de sueldo
	 * @param unknown_type $proveedor_id
	 * @param unknown_type $periodoCobro
	 */
	function getCobroByCajaByProveedorPeriodo($proveedor_id,$periodoCobro,$organismo=null,$tipoProducto=null,$tipoCuota=null){
		$sql = "SELECT 
					OrdenDescuentoCobro.id,
					OrdenDescuentoCobro.fecha,
					OrdenDescuentoCobro.periodo_cobro,
					OrdenDescuentoCobro.socio_id,
					OrdenDescuentoCobro.tipo_cobro,
					OrdenDescuentoCobro.cancelacion_orden_id,
					OrdenDescuentoCobroCuota.orden_descuento_cuota_id,
					OrdenDescuentoCobroCuota.importe AS importe,
					OrdenDescuentoCuota.nro_referencia_proveedor,
                                        Organismo.concepto_1
				FROM orden_descuento_cobros AS OrdenDescuentoCobro  
				INNER JOIN  orden_descuento_cobro_cuotas AS OrdenDescuentoCobroCuota ON (OrdenDescuentoCobroCuota.orden_descuento_cobro_id = OrdenDescuentoCobro.id)
				INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id)
				INNER JOIN persona_beneficios AS PersonaBeneficio ON (OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id ".(!empty($organismo) ? "AND PersonaBeneficio.codigo_beneficio = '$organismo'" : "").")
                                INNER JOIN global_datos Organismo on (Organismo.id = PersonaBeneficio.codigo_beneficio)
				WHERE 
					OrdenDescuentoCobro.periodo_cobro = '$periodoCobro' AND 
					OrdenDescuentoCobro.anulado = 0 AND 
					OrdenDescuentoCobroCuota.proveedor_id = $proveedor_id AND 
					OrdenDescuentoCobro.tipo_cobro <> 'MUTUTCOBRECS' 
					".(!empty($tipoProducto) ? " AND OrdenDescuentoCuota.tipo_producto = '$tipoProducto'" : "")."
					".(!empty($tipoCuota) ? " AND OrdenDescuentoCuota.tipo_cuota = '$tipoCuota'" : "")."
				ORDER BY OrdenDescuentoCobro.fecha DESC, OrdenDescuentoCuota.periodo ASC ";
		$cobros = $this->query($sql);
		return $cobros;
	}	
	

	function getCobrosBySocioByPeriodosByTipoCobro($socioId,$periodoDesde=null,$periodoHasta=null,$tipoCobro=null){
		
		$conditions = array();
		
		$conditions['OrdenDescuentoCobro.socio_id'] = $socioId;
		if(!empty($periodoDesde)) $conditions['OrdenDescuentoCobro.periodo_cobro >='] = $periodoDesde;
		if(!empty($periodoHasta)) $conditions['OrdenDescuentoCobro.periodo_cobro <='] = $periodoHasta;
		if(!empty($tipoCobro)) $conditions['OrdenDescuentoCobro.tipo_cobro <='] = $tipoCobro;
		
		$this->unbindModel(array('hasMany' => array('OrdenDescuentoCobroCuota'),'belongsTo' => array('Socio')));
		$cobros = $this->find('all',array('conditions' => $conditions, 'group' => array('OrdenDescuentoCobro.periodo_cobro','OrdenDescuentoCobro.tipo_cobro') ,'order' => array('OrdenDescuentoCobro.periodo_cobro')));
		
		
	}
	
	
	/**
	 * Genera un COBRO por cancelacion.  Es llamada por la funcion _procesarCancelacion() que esta en el modelo CANCELACION_ORDEN.
	 * Si el periodo de la cancelacion (sale de la fecha) es menor al periodo de cuota que esta cancelando, poner las cuotas al periodo de cancelacion.
	 * si es menor dejar como esta.
	 * @param unknown_type $ordenCancelacion
	 * @param unknown_type $fecha_cobro
	 */
	function generarCobroByCancelacion($ordenCancelacion,$fecha_cobro, $cTipoCobro='MUTUTCOBCCOM', $proveedorOrigenFondoId = NULL){
//		$this->begin();
		$flag = true;
		$periodo_cobro = date('Ym',strtotime($fecha_cobro));
		#calculo la nota de credito
		$NC = ($ordenCancelacion['CancelacionOrden']['importe_seleccionado'] - $ordenCancelacion['CancelacionOrden']['importe_proveedor']) * (-1);
		
		$cobro = array('OrdenDescuentoCobro' => array(
                    'id' => 0,
                    'socio_id' => $ordenCancelacion['CancelacionOrden']['socio_id'],
                    'tipo_cobro' => $cTipoCobro,
                    'fecha' => $fecha_cobro,
                    'periodo_cobro' => $periodo_cobro,
                    'importe' => $ordenCancelacion['CancelacionOrden']['importe_proveedor'],
                    'cancelacion_orden_id' => $ordenCancelacion['CancelacionOrden']['id'],
                    'recibo_id' => (isset($ordenCancelacion['CancelacionOrden']['recibo_id']) ? $ordenCancelacion['CancelacionOrden']['recibo_id'] : 0),
                    'proveedor_origen_fondo_id' => $proveedorOrigenFondoId,
		));
		
		$calculaComision = false;
		if($ordenCancelacion['CancelacionOrden']['tipo_cancelacion'] == 'P')$calculaComision = true;
		
		
		if(!parent::save($cobro)){
//			$this->rollback();
			return false;
		}
		
		$idCobro = $this->getLastInsertID();
		
		#proceso las cuotas
		$this->OrdenDescuentoCobroCuota = $this->importarModelo('OrdenDescuentoCobroCuota', 'mutual');

		$this->OrdenDescuentoCuota = $this->importarModelo('OrdenDescuentoCuota', 'mutual');
		
		foreach($ordenCancelacion['CancelacionOrdenCuota'] as $cuota){
			
			if(!$this->__setPeriodoCobro($cuota['orden_descuento_cuota_id'],$periodo_cobro)){
				$flag = false;
				break;				
			}
			
			$porc = 0;
			$impCom = 0;
			if($calculaComision){
				$comision = $this->OrdenDescuentoCobroCuota->calcularComisionCobranza($cuota['orden_descuento_cuota_id'],$cuota['importe']);
				$porc = $comision['alicuota'];
				$impCom = $comision['comision'];
			}
			
			$cobro = array('OrdenDescuentoCobroCuota' => array(
				'periodo_cobro' => $periodo_cobro,
				'orden_descuento_cobro_id' => $idCobro,
				'orden_descuento_cuota_id' => $cuota['orden_descuento_cuota_id'],
				'importe' => $cuota['importe'],
				'proveedor_id' => $cuota['proveedor_id'],
				'alicuota_comision_cobranza' => $porc,
				'comision_cobranza' => $impCom,
				'recibo_id' => (isset($ordenCancelacion['CancelacionOrden']['recibo_id']) ? $ordenCancelacion['CancelacionOrden']['recibo_id'] : 0)
			));

			if(!$this->OrdenDescuentoCobroCuota->grabarPago($cobro)){
				$flag = false;
				break;
			}			
			
		}
		if(!$flag){
			return false;
		}
		#genero la nota de credito
		if(abs($NC) != 0){
			$this->OrdenDescuentoCuota->generarNotaCreditoPorCancelacion($ordenCancelacion['CancelacionOrden']['orden_descuento_id'],$NC,$fecha_cobro,$ordenCancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
			$idNC = $this->OrdenDescuentoCuota->getLastInsertID();
			
			$proveedorNC = $this->OrdenDescuentoCuota->getProveedor($idNC);
			
			//grabar en la cabecera de la orden de cancelacion el id de la cuota de la nota de credito
			App::import('Model','Mutual.CancelacionOrden');
			$oCANC = new CancelacionOrden();

			$orden = $oCANC->read(null,$ordenCancelacion['CancelacionOrden']['id']);
			$orden['CancelacionOrden']['cuota_diferencia_id'] = $this->OrdenDescuentoCuota->getLastInsertID();
			
			$oCANC->save($orden);
			
			#meto la nota de credito en el cobro
			$cobro = array('OrdenDescuentoCobroCuota' => array(
				'periodo_cobro' => $periodo_cobro,
				'orden_descuento_cobro_id' => $idCobro,
				'orden_descuento_cuota_id' => $this->OrdenDescuentoCuota->getLastInsertID(),
				'importe' => $NC,
				'proveedor_id' => $proveedorNC,
				'recibo_id' => (isset($ordenCancelacion['CancelacionOrden']['recibo_id']) ? $ordenCancelacion['CancelacionOrden']['recibo_id'] : 0)
			));			
			if(!$this->OrdenDescuentoCobroCuota->grabarPago($cobro)){
				$flag = false;
//				break;
			}
		}
		
		if(!$flag){
			return false;
		}else{
			return $idCobro;
		}

	}

	
	function recaudarOrdenCobroByCaja($datos){
		// $oRecibo = $this->importarModelo('recibo', 'clientes');
        App::import('Model','clientes.Recibo');
        $oRecibo = new Recibo();
		$nReciboId = $oRecibo->recaudarOrdenCobroCaja($datos);
		if(!$nReciboId):
			return false;
		endif;
		return $nReciboId;
	}
	
	
	function anularCobro($id=0, $nReciboId=0){
		// TRAIGO LA ORDEN DESCUENTO COBRO, QUE A PARTIR DE ESTA EMPEZAMOS A ANULAR TODOS LOS MOVIMIENTO INVOLUCRADOS.
		// LOS MOVIMIENTO INVOLUCRADOS SON: RECIBO, MOVIMIENTO DE BANCO, FACTURA PROVEEDOR, FACTURA CLIENTES, ORDEN DE PAGO, 
		// DETALLE DE LAS CUOTAS COBRADAS Y SE AGREGO PROVEEDOR LIQUIDACIONES.
		
		$this->Recibo = $this->importarModelo('recibo', 'clientes');
		$this->OrdenDescuentoCuota = $this->importarModelo('OrdenDescuentoCuota', 'mutual');
		$this->OrdenDescuentoCobroCuota = $this->importarModelo('OrdenDescuentoCobroCuota', 'mutual');
		$this->CancelacionOrden = $this->importarModelo('CancelacionOrden', 'mutual');
		$this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
		$this->SocioReintegro = $this->importarModelo('SocioReintegro', 'pfyj');
		
//		if($nReciboId == 0):
//			$aCobros = $this->getCobro($id);
//			$nReciboId = $aCobros['OrdenDescuentoCobro']['recibo_id'];
//		endif;
		
		$lBorrarByRecibo = false;
		if($nReciboId > 0):
			$aCobros = $this->getCobroByReciboId($nReciboId);
			$lBorrarByRecibo = true;
			
		elseif($id > 0):
			$cCobro = $this->getCobro($id);
			if($cCobro['OrdenDescuentoCobro']['recibo_id'] > 0):
				$nReciboId = $cCobro['OrdenDescuentoCobro']['recibo_id'];
				$aCobros = $this->getCobroByReciboId($nReciboId);
				$lBorrarByRecibo = true;
			endif;
		else:
			return false;
		endif;


		$this->begin();
		if($lBorrarByRecibo):

			$aReintegro = $this->SocioReintegro->find('all', array('conditions' => array('SocioReintegro.recibo_id' => $nReciboId)));
			if(!empty($aReintegro)):
				if($aReintegro[0]['SocioReintegro']['orden_pago_id'] > 0): 
					return false;
				endif;
			endif;

			foreach($aCobros as $cCobro):
				if(!$this->borrarCobro($cCobro)):
					$this->rollback();
					return false;
				endif;	

			endforeach;
			
			
			$aRecibo = array('Recibo' => array('id' => $nReciboId));
			$this->Recibo->anularRecibo($aRecibo);
		else:
			if(!$this->borrarCobro($cCobro)):
				$this->rollback();
				return false;
			endif;	
		
		endif;
		$this->commit();		
		
		return true;
		
	}
	
	
	function borrarCobro($cCobro){
		$lEsCancelacion = false;
		$nImporteCredito = 0.00;
		$nCuotaCreditoId = 0;

		// SI ES UNA CANCELACION BUSCO LA ORDEN DE CANCELACION. SINO BUSCO LA ORDEN DE CAJA.
		if($cCobro['OrdenDescuentoCobro']['cancelacion_orden_id'] > 0):
			// TRAIGO LA ORDEN DE CANCELACION PARA BUSCAR LA CUOTA DE CREDITO PARA BORRARLA DE LA ORDEN DESCUENTO CUOTAS.
			if(!$this->anularByCancelacion($cCobro['OrdenDescuentoCobro']['cancelacion_orden_id'])) return false;
			$aCancelacion = $this->CancelacionOrden->get($cCobro['OrdenDescuentoCobro']['cancelacion_orden_id'],true);
			$lEsCancelacion = true;
			$nImporteCredito = $aCancelacion['CancelacionOrden']['importe_diferencia'];
			$nCuotaCreditoId = $aCancelacion['CancelacionOrden']['cuota_diferencia_id'];
			if(!$this->anularByCancelacion($cCobro['OrdenDescuentoCobro']['cancelacion_orden_id'])):
				return false;
			endif;
		else:
			if(!$this->anularByCaja($cCobro['OrdenDescuentoCobro']['id'])) return false;
		
		endif;
			
		// ACTUALIZO LA ORDEN DESCUENTO CUOTAS Y BORRO EL DETALLE DE LA ORDEN DESCUENTO COBRO.
		foreach($cCobro['OrdenDescuentoCobroCuota'] as $cCuota):

			$aOrdenDescuentoCuota = $this->OrdenDescuentoCuota->read(null,$cCuota['orden_descuento_cuota_id']);
			$aOrdenDescuentoCuota['OrdenDescuentoCuota']['estado'] = 'A';
			if(!empty($aOrdenDescuentoCuota['OrdenDescuentoCuota']['periodo_origen'])){
				$aOrdenDescuentoCuota['OrdenDescuentoCuota']['periodo'] = $aOrdenDescuentoCuota['OrdenDescuentoCuota']['periodo_origen'];
				$aOrdenDescuentoCuota['OrdenDescuentoCuota']['periodo_origen'] = NULL;	
			}

			// $aOrdenDescuentoCuota = array('OrdenDescuentoCuota' => array('id' => $cCuota['orden_descuento_cuota_id'], 'estado' => 'A'));
			if(!$this->OrdenDescuentoCuota->save($aOrdenDescuentoCuota)) return false;
				
			if(!$this->OrdenDescuentoCobroCuota->deleteAll('OrdenDescuentoCobroCuota.id = ' . $cCuota['id'])) return false;

			if($lEsCancelacion):
				if($nCuotaCreditoId == $cCuota['orden_descuento_cuota_id'] || $cCuota['importe'] < 0):
					if(!$this->OrdenDescuentoCuota->deleteAll("OrdenDescuentoCuota.id = " . $cCuota['orden_descuento_cuota_id'])) return false;
				endif;
			endif;
		endforeach;
			
		// BORRO LA PROVEEDOR LIQUIDACIONES
		if(!$this->ProveedorLiquidacion->deleteAll("ProveedorLiquidacion.orden_descuento_cobro_id = " . $cCobro['OrdenDescuentoCobro']['id'])) return false;
				
		// PONGO COMO ANULADA LA ORDEN DESCUENTO COBRO
		$this->id = $cCobro['OrdenDescuentoCobro']['id'];
		$cCobro['OrdenDescuentoCobro']['anulado'] = 1;
		$aOrdenDescuentoCobro = array('OrdenDescuentoCobro' => array('id' => $cCobro['OrdenDescuentoCobro']['id'], 'anulado' => 1));
                $this->auditable = TRUE;
                if(!$this->save($aOrdenDescuentoCobro)) {return false;}

		return true;
	}
	
	
	function getCobroByReciboId($nReciboId){
		$aOrdenDescuentoCobro = $this->find('all',array('conditions' => array('OrdenDescuentoCobro.recibo_id' => $nReciboId)));

		return $aOrdenDescuentoCobro;
	}


	function anularByCaja($nOrdenDescuentoCobroId){
		$this->OrdenCajaCobro = $this->importarModelo('OrdenCajaCobro', 'mutual');
		$this->OrdenPago = $this->importarModelo('Movimiento', 'proveedores');
		$this->ProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
		$this->ClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		$this->ClienteFacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
		$this->BancoCuentaMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		
		$aCajas = $this->OrdenCajaCobro->getByOrdenDescuentoCobro($nOrdenDescuentoCobroId);

		foreach($aCajas as $cCaja):
			if(!empty($cCaja['OrdenCajaCobro']['orden_pago_id'])):
				if(!$this->OrdenPago->anular($cCaja['OrdenCajaCobro']['orden_pago_id'])) return false;
			endif;
			if(!empty($cCaja['OrdenCajaCobro']['banco_cuenta_movimiento_id'])):
				if(!$this->BancoCuentaMovimiento->deleteAll("BancoCuentaMovimiento.id = " . $cCaja['OrdenCajaCobro']['banco_cuenta_movimiento_id'])) return false;
			endif;
			if(!$this->ProveedorFactura->deleteAll("ProveedorFactura.orden_caja_cobro_id = " . $cCaja['OrdenCajaCobro']['id'])) return false;
			// ANULO LA FACTURA CLIENTE Y BORRO EL DETALLE DE LA MISMA.
			if(!$this->ClienteFactura->anularByOrdenCaja($cCaja['OrdenCajaCobro']['id'])) return false;
			// ANULO MOVIMIENTO BANCO.
			if(!$this->BancoCuentaMovimiento->deleteAll("BancoCuentaMovimiento.orden_caja_cobro_id = " . $cCaja['OrdenCajaCobro']['id'])) return false;
			// BORRO EL CREDITO GENERADO AL PROVEEDOR
			if(!empty($cCaja['OrdenDescuentoCobro']['proveedor_factura_id'])):
				if(!$this->ProveedorFactura->deleteAll("ProveedorFactura.id = " . $cCaja['OrdenDescuentoCobro']['proveedor_factura_id'])) return false;
			endif;
			// LA ORDEN CAJA COBRO LA PONGO EN EMITIDA.
			$cCaja['OrdenCajaCobro']['estado'] = 'E';
			$cCaja['OrdenCajaCobro']['orden_pago_id'] = 0;
			$cCaja['OrdenCajaCobro']['banco_cuenta_movimiento_id'] = 0;
			$cCaja['OrdenCajaCobro']['orden_descuento_cobro_id'] = 0;
			$cCaja['OrdenCajaCobro']['recibo_id'] = 0;
			$cCaja['OrdenCajaCobro']['importe_contado'] = 0;
			$cCaja['OrdenCajaCobro']['importe_orden_pago'] = 0;
			if(!$this->OrdenCajaCobro->save(($cCaja))) return false;
		endforeach;
		
		return true;
		
	}


	function anularByCancelacion($nCancelacionOrdenId){
		$this->OrdenPago = $this->importarModelo('Movimiento', 'proveedores');
		$this->ProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
		$this->ClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		$this->ClienteFacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
		$this->ProveedorFacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');
		$this->BancoCuentaMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		$this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
		
		$this->CancelacionOrden = $this->importarModelo('CancelacionOrden', 'mutual');
		$aCancelacion = $this->CancelacionOrden->get($nCancelacionOrdenId,true);
		
		if(!empty($aCancelacion['CancelacionOrden']['orden_pago_id'])):
			if(!$this->OrdenPago->anular($aCancelacion['CancelacionOrden']['orden_pago_id'])) return false;
		endif;
		if(!empty($aCancelacion['CancelacionOrden']['proveedor_factura_id'])):
			if(!$this->ProveedorFactura->deleteAll("ProveedorFactura.orden_descuento_cobro_id = " . $aCancelacion['CancelacionOrden']['orden_descuento_cobro_id'])) return false;
		endif;
		if(!empty($aCancelacion['CancelacionOrden']['proveedor_factura_id'])):
			if(!$this->ClienteFacturaDetalle->deleteAll("ClienteFacturaDetalle.cliente_factura_id = " . $aCancelacion['CancelacionOrden']['cliente_factura_id'])) return false;
			if(!$this->ClienteFactura->updateAll(array('ClienteFactura.anulado' => 1), array('ClienteFactura.id' => $aCancelacion['CancelacionOrden']['cliente_factura_id']))) return false;
		endif;
		if(!empty($aCancelacion['CancelacionOrden']['banco_cuenta_movimiento_id'])):
			if(!$this->BancoCuentaMovimiento->deleteAll("BancoCuentaMovimiento.id = " . $aCancelacion['CancelacionOrden']['banco_cuenta_movimiento_id'])) return false;
		endif;
		
		if(!$this->BancoCuentaMovimiento->deleteAll("BancoCuentaMovimiento.cancelacion_orden_id = " . $aCancelacion['CancelacionOrden']['id'])) return false;
		
		if(!$this->ProveedorLiquidacion->deleteAll("ProveedorLiquidacion.cancelacion_orden_id = " . $aCancelacion['CancelacionOrden']['id'])) return false;
		
		
		if(!empty($aCancelacion['CancelacionOrden']['credito_proveedor_factura_id'])):
			if(!$this->ProveedorFactura->deleteAll("ProveedorFactura.id = " . $aCancelacion['CancelacionOrden']['credito_proveedor_factura_id'])) return false;
			if(!$this->ProveedorLiquidacion->deleteAll("ProveedorLiquidacion.proveedor_factura_id = " . $aCancelacion['CancelacionOrden']['credito_proveedor_factura_id'])) return false;
		endif;
		
		// LA ORDEN DE CANCELACION LA PONGO EN EMITIDA.
		$aCancelacion['CancelacionOrden']['estado'] = 'E';
		$aCancelacion['CancelacionOrden']['cuota_diferencia_id'] = 0;
		$aCancelacion['CancelacionOrden']['orden_pago_id'] = 0;
		$aCancelacion['CancelacionOrden']['banco_cuenta_movimiento_id'] = 0;
		$aCancelacion['CancelacionOrden']['proveedor_factura_id'] = 0;
		$aCancelacion['CancelacionOrden']['credito_proveedor_factura_id'] = 0;
		$aCancelacion['CancelacionOrden']['cliente_factura_id'] = 0;
		$aCancelacion['CancelacionOrden']['orden_descuento_cobro_id'] = 0;
		$aCancelacion['CancelacionOrden']['recibo_id'] = 0;
		if(!$this->CancelacionOrden->save($aCancelacion)) return false;
        

        
        
        
        #MARCAR LAS CUOTAS ORIGINALES EL PERIODO ORIGEN

        $sql = "select * from cancelacion_orden_cuotas CancelacionOrdenCuota "
                . " where CancelacionOrdenCuota.cancelacion_orden_id = " . $aCancelacion['CancelacionOrden']['id'];
        
        $cuotas = $this->CancelacionOrden->query($sql);
        if(!empty($cuotas)){
            
            App::import('model','mutual.OrdenDescuentoCuota');
            $oCUOTA = new OrdenDescuentoCuota();
            
            foreach($cuotas as $cuota){
                $sqlu = "UPDATE orden_descuento_cuotas "
                        . " SET periodo = periodo_origen, "
                        . " periodo_origen = NULL "
                        . " WHERE id = " . $cuota['CancelacionOrdenCuota']['orden_descuento_cuota_id'] ." AND IFNULL(periodo_origen,'') <> ''";
                if(!empty($cuota['CancelacionOrdenCuota']['orden_descuento_cuota_id'])){
                    $oCUOTA->query($sqlu);
//                    if(!$this->CancelacionOrden->query($sqlu)) return false;
                }
                
            }
        }        
        
//        $sql = "UPDATE orden_descuento_cuotas OrdenDescuentoCuotas "
//                . "SET OrdenDescuentoCuotas.periodo = OrdenDescuentoCuotas.periodo_origen, OrdenDescuentoCuotas.periodo_origen = NULL "
//                . "WHERE	OrdenDescuentoCuotas.id IN("
//                . "SELECT	CancelacionOrdenCuota.orden_descuento_cuota_id "
//                . "FROM  cancelacion_orden_cuotas CancelacionOrdenCuota, cancelacion_ordenes CancelacionOrden "
//                . "WHERE CancelacionOrden.id = " . $aCancelacion['CancelacionOrden']['id'] . " AND CancelacionOrden.estado = 'E' "
//                . "AND CancelacionOrden.id = CancelacionOrdenCuota.cancelacion_orden_id) AND OrdenDescuentoCuotas.periodo_origen IS NOT NULL";
//
//        $sql = "UPDATE cancelacion_orden_cuotas CancelacionOrdenCuota, orden_descuento_cuotas OrdenDescuentoCuota, cancelacion_ordenes CancelacionOrden "
//                . "SET OrdenDescuentoCuota.periodo = OrdenDescuentoCuota.periodo_origen, OrdenDescuentoCuota.periodo_origen = NULL "
//                . "WHERE CancelacionOrden.id = '".$aCancelacion['CancelacionOrden']['id']."' AND CancelacionOrden.estado = 'E' "
//                . "AND CancelacionOrden.id = CancelacionOrdenCuota.cancelacion_orden_id AND CancelacionOrdenCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id AND cu.periodo_origen IS NOT NULL";

        if(!$this->CancelacionOrden->query($sql)) return false;

        return true;
	}


	function getRecibo($id=null){
		if(empty($id)) return array();
		
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		
		return $oRecibo->getRecibo($id);
	}
	

	function ReciboDetalle($ordenCobro){
		
		if($ordenCobro['OrdenDescuentoCobro']['cancelacion_orden_id'] > 0):
			return $this->ReciboCancelacionDetalle($ordenCobro['OrdenDescuentoCobro']['cancelacion_orden_id']);
		else:
			return $this->ReciboCajaDetalle($ordenCobro['OrdenDescuentoCobro']['id'], $ordenCobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id']);
		endif;
		
	}
	
	
	function ReciboCajaDetalle($id, $proveedor_origen_fondo_id){

		$sql = "select GlobalDato.concepto_1, GlobalDatoConcepto.concepto_1,OrdenDescuentoCuota.tipo_cuota, OrdenDescuento.*, OrdenDescuento.numero, OrdenDescuento.cuotas, 
				sum(OrdenDescuentoCobroCuota.importe) as importe,sum(OrdenDescuentoCobroCuota.comision_cobranza) as comision_cobranza
				from orden_descuentos OrdenDescuento
				inner join orden_descuento_cuotas OrdenDescuentoCuota
				on OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id
				inner join orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
				on OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
				inner join global_datos GlobalDato
				on GlobalDato.id = OrdenDescuento.tipo_producto
				inner join global_datos GlobalDatoConcepto
				on GlobalDatoConcepto.id = OrdenDescuentoCuota.tipo_cuota
				where OrdenDescuentoCobroCuota.orden_descuento_cobro_id = '$id'
				group by OrdenDescuento.id, OrdenDescuentoCuota.tipo_cuota
				order by OrdenDescuento.id";
		
		$aOrdenDescuentos =  $this->query($sql);
		
		$aTmpDetalle = array();
		$aOrdenDescuentoDetalle = array();
		foreach($aOrdenDescuentos as $aOrDescuento):
			$OrdenDescuentoId = $aOrDescuento['OrdenDescuento']['id'];
			$OrdenDescuentoTipoCuota = $aOrDescuento['OrdenDescuentoCuota']['tipo_cuota'];
			$sqlCuotas = "select OrdenDescuentoCuota.nro_cuota as nro_cuota, OrdenDescuentoCuota.periodo
							from orden_descuento_cuotas OrdenDescuentoCuota
							inner join orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
							on OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
							where OrdenDescuentoCuota.orden_descuento_id = $OrdenDescuentoId and OrdenDescuentoCuota.tipo_cuota = '$OrdenDescuentoTipoCuota' and OrdenDescuentoCobroCuota.orden_descuento_cobro_id = '$id'";
			$aOrdenDescuentoCuotas = $this->query($sqlCuotas);


			$cuotas = Set::extract('/OrdenDescuentoCuota/nro_cuota',$aOrdenDescuentoCuotas);
			$strDesc = implode('-', $cuotas) ."/" . $aOrDescuento['OrdenDescuento']['cuotas'];


			$periodo = Set::extract('/OrdenDescuentoCuota/periodo', $aOrdenDescuentoCuotas);
			
			foreach($periodo as $key => $valor):
				$periodo[$key] = parent::periodo($valor);
			endforeach;
			$strPeriodo = implode(',', $periodo);

			$facturar = ($aOrDescuento['OrdenDescuento']['proveedor_id'] == MUTUALPROVEEDORID || $proveedor_origen_fondo_id == $aOrDescuento['OrdenDescuento']['proveedor_id'] ? 0 : 1);
						
			$aTmpDetalle['proveedor_origen_fondo_id'] = $proveedor_origen_fondo_id;
			$aTmpDetalle['proveedor_id'] = $aOrDescuento['OrdenDescuento']['proveedor_id'];
			$aTmpDetalle['cancelacion_orden_id'] = 0;
			$aTmpDetalle['orden_descuento_id'] = $aOrDescuento['OrdenDescuento']['id'];
			$aTmpDetalle['orden_descuento_cobro_id'] = $id;
			$aTmpDetalle['concepto'] = 'EXPTE: ' . $aOrDescuento['OrdenDescuento']['numero'] . ' - ' . $aOrDescuento['GlobalDato']['concepto_1'] . ' - ctas: ' . $strDesc;
			if($aOrDescuento['OrdenDescuento']['permanente'] == 1):
				$aTmpDetalle['concepto'] = $aOrDescuento['GlobalDatoConcepto']['concepto_1'] . ' - PER.: ' . $strPeriodo;
			endif; 
			$aTmpDetalle['importe'] = $aOrDescuento[0]['importe'];
			$aTmpDetalle['comision_cobranza'] = $aOrDescuento[0]['comision_cobranza'];
			$aTmpDetalle['facturar'] = $facturar;
			array_push($aOrdenDescuentoDetalle, $aTmpDetalle);
		endforeach;

		
		return $aOrdenDescuentoDetalle;
		
	}
	
	
	function ReciboCancelacionDetalle($id){

		$this->CancelacionOrden = $this->importarModelo('CancelacionOrden', 'mutual');
		$orden = $this->CancelacionOrden->get($id);

		$aTmpDetalle = array();
		$aOrdenDescuentoDetalle = array();
		
		$prnDetalle = ($orden['CancelacionOrden']['orden_proveedor_id'] != MUTUALPROVEEDORID && $orden['CancelacionOrden']['orden_proveedor_id'] == $orden['CancelacionOrden']['origen_proveedor_id'] ? 0 : 1);
				
		$aTmpDetalle['proveedor_origen_fondo_id'] = $orden['CancelacionOrden']['origen_proveedor_id'];
		$aTmpDetalle['proveedor_id'] = $orden['CancelacionOrden']['orden_proveedor_id'];
		$aTmpDetalle['cancelacion_orden_id'] = $id;
		$aTmpDetalle['orden_descuento_cobro_id'] = $orden['CancelacionOrden']['orden_descuento_cobro_id'];
		$aTmpDetalle['orden_descuento_id'] = $orden['CancelacionOrden']['orden_descuento_id'];
		$aTmpDetalle['concepto'] = $orden['CancelacionOrden']['recibo_detalle'];
		$aTmpDetalle['importe'] = $orden['CancelacionOrden']['total_orden'] + ($orden['CancelacionOrden']['importe_diferencia'] * -1);
		$aTmpDetalle['comision_cobranza'] = 0;
		$aTmpDetalle['imprimir_detalle'] = $prnDetalle;
		array_push($aOrdenDescuentoDetalle, $aTmpDetalle);

		if($orden['CancelacionOrden']['importe_diferencia'] != 0):
			$aTmpDetalle['proveedor_origen_fondo_id'] = $orden['CancelacionOrden']['origen_proveedor_id'];
			$aTmpDetalle['proveedor_id'] = $orden['CancelacionOrden']['orden_proveedor_id'];
			$aTmpDetalle['cancelacion_orden_id'] = $id;
			$aTmpDetalle['orden_descuento_cobro_id'] = $orden['CancelacionOrden']['orden_descuento_cobro_id'];
			$aTmpDetalle['orden_descuento_id'] = $orden['CancelacionOrden']['orden_descuento_id'];
			$aTmpDetalle['orden_descuento_cuota_id'] = $orden['CancelacionOrden']['cuota_diferencia_id'];
			$aTmpDetalle['concepto'] = $orden['CancelacionOrden']['tipo_cuota_diferencia_desc'];
			$aTmpDetalle['importe'] = $orden['CancelacionOrden']['importe_diferencia'];
			$aTmpDetalle['comision_cobranza'] = 0;
			$aTmpDetalle['imprimir_detalle'] = $prnDetalle;
			
			array_push($aOrdenDescuentoDetalle, $aTmpDetalle);
		endif;

		return $aOrdenDescuentoDetalle;
	}


	function ProveedorLiquidacion($ordenCobro){
		
		if($ordenCobro['OrdenDescuentoCobro']['cancelacion_orden_id'] > 0):
			return $this->LiquidacionCancelacionDetalle($ordenCobro['OrdenDescuentoCobro']['cancelacion_orden_id']);
		else:
			return $this->LiquidacionCajaDetalle($ordenCobro['OrdenDescuentoCobro']['id'], $ordenCobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id']);
		endif;
		
	}
	
	
	function LiquidacionCajaDetalle($id, $proveedor_origen_fondo_id){

		$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');
		
		$sql = "select OrdenCajaCobro.id, GlobalDato.concepto_1, GlobalDatoConcepto.concepto_1,OrdenDescuentoCuota.tipo_cuota, OrdenDescuento.*, OrdenDescuento.numero, OrdenDescuento.cuotas, 
				sum(OrdenDescuentoCobroCuota.importe) as importe,sum(OrdenDescuentoCobroCuota.comision_cobranza) as comision_cobranza
				from orden_descuentos OrdenDescuento
				inner join orden_descuento_cuotas OrdenDescuentoCuota
				on OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id
				inner join orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
				on OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
				inner join global_datos GlobalDato
				on GlobalDato.id = OrdenDescuento.tipo_producto
				inner join global_datos GlobalDatoConcepto
				on GlobalDatoConcepto.id = GlobalDato.concepto_2
				inner join orden_caja_cobros OrdenCajaCobro
				on OrdenCajaCobro.orden_descuento_cobro_id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
				where OrdenDescuentoCobroCuota.orden_descuento_cobro_id = '$id'
				group by OrdenDescuento.id
				order by OrdenDescuento.id";
		
		$aOrdenDescuentos =  $this->query($sql);
		
		$aTmpDetalle = array();
		$aOrdenDescuentoDetalle = array();
		foreach($aOrdenDescuentos as $aOrDescuento):
			if($aOrDescuento['OrdenDescuento']['proveedor_id'] != MUTUALPROVEEDORID):
				$OrdenDescuentoId = $aOrDescuento['OrdenDescuento']['id'];
				$sqlCuotas = "select OrdenDescuentoCobroCuota.id, OrdenDescuentoCuota.nro_cuota as nro_cuota, OrdenDescuentoCuota.periodo
								from orden_descuento_cuotas OrdenDescuentoCuota
								inner join orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
								on OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
								where OrdenDescuentoCuota.orden_descuento_id = $OrdenDescuentoId and OrdenDescuentoCobroCuota.orden_descuento_cobro_id = '$id'";
				$aOrdenDescuentoCuotas = $this->query($sqlCuotas);

				$cobro_id = Set::extract('/OrdenDescuentoCobroCuota/id',$aOrdenDescuentoCuotas);	
				
				$cuotas = Set::extract('/OrdenDescuentoCuota/nro_cuota',$aOrdenDescuentoCuotas);
				$strDesc = implode('-', $cuotas) ."/" . $aOrDescuento['OrdenDescuento']['cuotas'];
	
	
				$periodo = Set::extract('/OrdenDescuentoCuota/periodo', $aOrdenDescuentoCuotas);

				
				foreach($periodo as $key => $valor):
					$periodo[$key] = parent::periodo($valor);
				endforeach;
				$strPeriodo = implode(',', $periodo);
	
				$aProveedor = $this->Proveedor->getProveedor($aOrDescuento['OrdenDescuento']['proveedor_id']);
			
				$facturar = ($aOrDescuento['OrdenDescuento']['proveedor_id'] == MUTUALPROVEEDORID || $proveedor_origen_fondo_id == $aOrDescuento['OrdenDescuento']['proveedor_id'] ? 0 : 1);
					
				
				$aTmpDetalle['proveedor_origen_fondo_id'] = $proveedor_origen_fondo_id;
				$aTmpDetalle['proveedor_id'] = $aOrDescuento['OrdenDescuento']['proveedor_id'];
				$aTmpDetalle['cliente_id'] = $aProveedor['Proveedor']['cliente_id'];
				$aTmpDetalle['proveedor_factura_id'] = $this->getProveedorFactura($OrdenDescuentoId, $id, $aOrDescuento['OrdenDescuento']['proveedor_id']);
				$aTmpDetalle['cliente_factura_id'] = $this->getClienteFactura($OrdenDescuentoId, $id, $aOrDescuento['OrdenDescuento']['proveedor_id']);
				$aTmpDetalle['cancelacion_orden_id'] = 0;
				$aTmpDetalle['orden_descuento_id'] = $aOrDescuento['OrdenDescuento']['id'];
				$aTmpDetalle['orden_caja_cobro_id'] = $aOrDescuento['OrdenCajaCobro']['id'];
				$aTmpDetalle['orden_descuento_cobro_id'] = $id;
				$aTmpDetalle['concepto'] = 'EXPTE: ' . $aOrDescuento['OrdenDescuento']['numero'] . ' - ' . $aOrDescuento['GlobalDato']['concepto_1'] . ' - ctas: ' . $strDesc;
				if($aOrDescuento['OrdenDescuento']['permanente'] == 1):
					$aTmpDetalle['concepto'] = $aOrDescuento['GlobalDatoConcepto']['concepto_1'] . ' - PER.: ' . $strPeriodo;
				endif; 
				$aTmpDetalle['importe'] = $aOrDescuento[0]['importe'];
				$aTmpDetalle['comision_cobranza'] = $aOrDescuento[0]['comision_cobranza'];
				$aTmpDetalle['facturar'] = $facturar;
				$aTmpDetalle['cobro_cuota_id'] = $cobro_id;
				array_push($aOrdenDescuentoDetalle, $aTmpDetalle);
			endif;
		endforeach;

		
		return $aOrdenDescuentoDetalle;
		
	}
	
	
	
	function LiquidacionCancelacionDetalle($id){

		$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');
		$this->CancelacionOrden = $this->importarModelo('CancelacionOrden', 'mutual');
		$orden = $this->CancelacionOrden->get($id);

		if($orden['CancelacionOrden']['orden_proveedor_id'] == MUTUALPROVEEDORID) return array();
		
		$aProveedor = $this->Proveedor->getProveedor($orden['CancelacionOrden']['orden_proveedor_id']);
		
		$aTmpDetalle = array();
		$aOrdenDescuentoDetalle = array();
		
		$facturar = ($orden['CancelacionOrden']['orden_proveedor_id'] == MUTUALPROVEEDORID || $orden['CancelacionOrden']['orden_proveedor_id'] == $orden['CancelacionOrden']['origen_proveedor_id'] ? 0 : 1);
				
		$aTmpDetalle['proveedor_origen_fondo_id'] = $orden['CancelacionOrden']['origen_proveedor_id'];
		$aTmpDetalle['proveedor_id'] = $orden['CancelacionOrden']['orden_proveedor_id'];
		$aTmpDetalle['cliente_id'] = $aProveedor['Proveedor']['cliente_id'];
		$aTmpDetalle['proveedor_factura_id'] = $orden['CancelacionOrden']['proveedor_factura_id'];
		$aTmpDetalle['cliente_factura_id'] = $orden['CancelacionOrden']['cliente_factura_id'];
		$aTmpDetalle['cancelacion_orden_id'] = $id;
		$aTmpDetalle['orden_descuento_cobro_id'] = $orden['CancelacionOrden']['orden_descuento_cobro_id'];
		$aTmpDetalle['concepto'] = $orden['CancelacionOrden']['recibo_detalle'];
		$aTmpDetalle['importe'] = $orden['CancelacionOrden']['total_orden'];
		$aTmpDetalle['comision_cobranza'] = $orden['CancelacionOrden']['comision_cobranza'];
		$aTmpDetalle['facturar'] = $facturar;
		array_push($aOrdenDescuentoDetalle, $aTmpDetalle);

		
		return $aOrdenDescuentoDetalle;
	}


	function getProveedorFactura($DescuentoId, $CobroId, $ProveedorId){
		$this->ProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
		
		$facturaId = $this->ProveedorFactura->find('all', array('conditions' => array('ProveedorFactura.orden_descuento_id' => $DescuentoId, 'ProveedorFactura.orden_descuento_cobro_id' => $CobroId, 'ProveedorFactura.proveedor_id' => $ProveedorId)));
		
		if (empty($facturaId)) return 0;
		
		return $facturaId[0]['ProveedorFactura']['id'];
	}



	function getClienteFactura($DescuentoId, $CobroId, $ProveedorId){
		$this->ClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		$this->Proveedor = $this->importarModelo('Proveedor', 'proveedores');
		
		$aProveedor = $this->Proveedor->read(null, $ProveedorId);
		
		$facturaId = $this->ClienteFactura->find('all', array('conditions' => array('ClienteFactura.orden_descuento_id' => $DescuentoId, 'ClienteFactura.orden_descuento_cobro_id' => $CobroId, 'ClienteFactura.cliente_id' => $aProveedor['Proveedor']['cliente_id'])));
		
		if (empty($facturaId)) return 0;
		
		return $facturaId[0]['ClienteFactura']['id'];
	}
	

	function orden_cobro_caja($datos){
            $flag = true;
		
            $renglones = array();
            if(isset($datos['Recibo']['renglonesSerialize'])){
                $renglones = base64_decode($datos['Recibo']['renglonesSerialize']);
                $renglones = unserialize($renglones);
            }


            # TIPO DE DOCUMENTO.
            $this->TipoDocumento = $this->importarModelo('TipoDocumento', 'config');
    	
            #########################
            # BUSCO EL NUMERO DE RECIBO Y BLOQUEO LA TABLA.
            $nNroRecibo = $this->TipoDocumento->getNumero($datos['OrdenDescuentoCobro']['tipo_documento']);
            if($nNroRecibo == 0):
                parent::notificar('LA TABLA DE RECIBO ESTA OCUPADO POR OTRO USUARIO');
                return false;
            endif;

            #########################
            # Busco el Numero de la Factura del cliente
            $nroFactura = $this->TipoDocumento->getNumero('FAC');
            if($nroFactura == 0):
                parent::notificar('LA TABLA DE FACTURA ESTA OCUPADO POR OTRO USUARIO');
                $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                return false;
            endif;
		
//		$nroOrdenPago = $this->TipoDocumento->getNumero('OPA');
//		if($nroOrdenPago == 0):
//			parent::notificar('LA ORDEN DE PAGO ES USADA POR OTRO USUARIO');
//			parent::rollback(); 
//			$this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//			$this->TipoDocumento->unLookRegistro('FAC');
//			$this->TipoDocumento->unLookRegistro('OPA');
//			return false;
//		endif;
		
            ########################
            #ABRIR UNA TRANSACCION
            ########################
            $this->begin();

            $datos['OrdenDescuentoCobro']['socio_id'] = $datos['OrdenDescuentoCobro']['cabecera_socio_id'];

            # GENERO LA ORDEN COBRO CAJA.
            $nOrdenDescuentoCobroId = $this->generarOrdenCobroCaja($datos);
            if(!$nOrdenDescuentoCobroId):
                    parent::rollback();
                    parent::notificar('NO SE GENERO LA ORDEN DE COBRO');
                    $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                    $this->TipoDocumento->unLookRegistro('FAC');
                    return false;
            endif;

            if($datos['OrdenDescuentoCobro']['importe_cobro'] == 0):
			########################
			# Genero la Provedor Liquidaciones con el id de la orden descuento cobro.
			########################
/*
 * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
 * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
 * esta funcion queda obsoleta. 26/06/2017
			$this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
			if(!$this->ProveedorLiquidacion->grabarLiquidacionByCaja($nOrdenDescuentoCobroId)):
				parent::notificar('LA LIQUIDACION A COMERCIO NO PUDO SER ACTUALIZADA');
				parent::rollback(); 
				$this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				$this->TipoDocumento->unLookRegistro('FAC');
				return false;
			endif;
 * 
 */
			
                $this->commit();
                $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                $this->TipoDocumento->unLookRegistro('FAC');
                return 'A';
            endif;
		
            $datos['OrdenDescuentoCobro']['id'] = $nOrdenDescuentoCobroId;
            $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'] = $nOrdenDescuentoCobroId;
		
		
		
            # MODELO PARA GRABAR EL RECIBO DE INGRESO.
            $this->Recibo = $this->importarModelo('Recibo', 'clientes');

            $nReciboId = 'A';
            # SI COMPENSA PAGO NO GENERO EL RECIBO DE INGRESO POR QUE NO HAY ENTRADA DE DINERO.
            if(!isset($datos['OrdenDescuentoCobro']['compensa_pago'])):
                $datos['Recibo']['nro_recibo'] = str_pad($nNroRecibo, 8, 0, STR_PAD_LEFT);
                $aCmpRecibo = $this->TipoDocumento->getComprobante($datos['OrdenDescuentoCobro']['tipo_documento']);
                $datos['Recibo']['letra'] = $aCmpRecibo['letra'];
                $nReciboId = $this->Recibo->grabarReciboCaja($datos);


                if(!$nReciboId):
                    parent::notificar('NO SE GENERO EL RECIBO DE INGRESO');
                    $this->rollback();
                    $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                    $this->TipoDocumento->unLookRegistro('FAC');
                    return false;
                endif;

                $datos['OrdenDescuentoCobro']['recibo_id'] = $nReciboId;
            endif;
		
		
            # MODELOS A UTILIZAR PARA EL COMERCIO (PROVEEDOR Y CLIENTE)
            $oProveedor = $this->importarModelo('Proveedor', 'proveedores');
            
            $this->ProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');

            $this->ClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
		
            // Factura Detalle
            $this->FacturaDetalle = $this->importarModelo('ClienteFacturaDetalle', 'clientes');

            # TRAIGO DE LA ORDEN DESCUENTO COBRO CUOTAS ACUMULADO POR PROVEEDOR LOS MONTOS A FACTURAR CORRESPONDIENTE A CADA UNO INVOLUCRADO EN EL COBRO.
            $aCobroProveedor = $this->__getCobroCuotaByProveedor($nOrdenDescuentoCobroId);

            $nroFactura -= 1;
            $nCantidadFactura = 0;
            $fechaCobro = parent::armaFecha($datos['OrdenDescuentoCobro']['fecha_comprobante']);
            $periodo_cobro = date('Ym',strtotime($fechaCobro));

            foreach($aCobroProveedor as $aCobro):
            
                $aCobro['orden_descuento_cobro_cuotas']['orden_caja_cobro_id'] = $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'];
                $aCobro['orden_descuento_cobro_cuotas']['fecha_comprobante'] = $fechaCobro;
                $aCobro['orden_descuento_cobro_cuotas']['periodo_cobro'] = $periodo_cobro;
                $aCobro['orden_descuentos']['proveedor_id'] = $aCobro['orden_descuento_cobro_cuotas']['proveedor_id'];
                if($aCobro['orden_descuento_cobro_cuotas']['proveedor_id'] != MUTUALPROVEEDORID && $aCobro['orden_descuento_cobro_cuotas']['proveedor_id'] != $datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id']):
                    if($aCobro['orden_descuento_cobro_cuotas']['proveedor_id'] != $datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id']):
                        // GRABO LA FACTURA DEL COMERCIO-PROVEEDOR
                        if($aCobro[0]['importe'] > 0):

                            $aFacturaProveedor = $this->ProveedorFactura->prepararFacturaProveedor($aCobro);
                            if(!$this->ProveedorFactura->save($aFacturaProveedor)):
                                parent::notificar('NO SE GENERO LA FACTURA AL COMERCIO');
                                $flag = false;
                                break;				
                            endif;
                        endif;
                        // GRABO LA FACTURA DEL COMERCIO-CLIENTE POR LA COMISION
                        $aProveedor = $oProveedor->getProveedor($aCobro['orden_descuento_cobro_cuotas']['proveedor_id']);
                        if($aCobro[0]['comision_cobranza'] > 0 && $aProveedor['Proveedor']['cliente_id'] > 0):
                            $nCantidadFactura += 1;
                            $nroFactura += 1;
                            $aFacturaCliente = $this->ClienteFactura->prepararFacturaCliente($aCobro);
                            $aFacturaCliente['numero_comprobante'] = str_pad($nroFactura, 8, 0, STR_PAD_LEFT);
                            if(!$this->ClienteFactura->save($aFacturaCliente)):
                                parent::notificar('NO SE GENERO LA COMISION AL COMERCIO');
                                $flag = false;
                                break;				
                            endif;
                            $aFacturaCliente['factura_detalle']['cliente_factura_id'] = $this->ClienteFactura->getLastInsertID();
                            if(!$this->FacturaDetalle->save($aFacturaCliente['factura_detalle'])):
                                parent::notificar('NO SE GENERO EL DETALLE DE LA COMISION AL COMERCIO');
                                $flag = false;
                                break;				
                            endif;
                            $this->ClienteFactura->id = 0;
                        endif;
                    endif;
                endif;
            endforeach;
		

            if(!$flag):
                parent::rollback(); 
                $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                $this->TipoDocumento->unLookRegistro('FAC');
                return false;
            endif;
		

		# GENERO UNA ORDEN DE PAGO ANTICIPADO SI LA RECAUDACION FUE EN UN COMERCIO Y ESTE NO ENTREGO EL EFECTIVO.
//		$importe_anticipo = 0;
//		$nOrdenPagoId = 0;
//		if($datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] != MUTUALPROVEEDORID && isset(
//		   $datos['OrdenDescuentoCobro']['compensa_pago'])):
//			// Llamo al Modelo Orden de Pago
//			$this->OrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
//			$datos['OrdenDescuentoCobro']['numero_orden_pago'] = $nroOrdenPago;
//			$nOrdenPagoId = $this->OrdenPago->grabarOrdenPagoAnticipoCaja($datos);
//			if(!$nOrdenPagoId):
//				$flag = false;
//				parent::notificar('LA ORDEN DE PAGO ANTICIPADO NO PUDO SER GENERADA');
//				parent::rollback(); 
//				$this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
//				$this->TipoDocumento->unLookRegistro('FAC');
//				$this->TipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;
//			$importe_anticipo = $datos['OrdenDescuentoCobro']['importe_cobrado'];
//		endif;

		
            $this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
		
            # GENERO UNA NOTA DE CREDITO SI LA RECAUDACION FUE EN UN COMERCIO Y ESTE NO ENTREGO EL EFECTIVO.
            $importe_credito = 0;
            $nCreditoId = 0;
            if($datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] != MUTUALPROVEEDORID && isset(
                $datos['OrdenDescuentoCobro']['compensa_pago'])):
                $datos['OrdenDescuentoCobro']['periodo_cobro'] = $periodo_cobro;
                $aFacturaProveedor = $this->ProveedorFactura->prepararCreditoProveedor($datos);
                if(!$this->ProveedorFactura->save($aFacturaProveedor)):
                    $flag = false;
                    parent::notificar('LA NOTA DE CREDITO NO PUDO SER GENERADA');
                    parent::rollback(); 
                    $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                    $this->TipoDocumento->unLookRegistro('FAC');
                    return false;
                endif;
                $datos['OrdenDescuentoCobro']['proveedor_factura_id'] = $this->ProveedorFactura->getLastInsertID();
                $nCreditoId = $this->ProveedorFactura->id;
                $importe_credito = $datos['OrdenDescuentoCobro']['importe_cobrado'];

/*
 * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
 * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
 * esta funcion queda obsoleta. 26/06/2017
			if(!$this->ProveedorLiquidacion->grabarLiquidacionCredito($datos)):
				parent::notificar('LA LIQUIDACION DE CREDITO A COMERCIO NO PUDO SER ACTUALIZADA');
				parent::rollback(); 
				$this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
				$this->TipoDocumento->unLookRegistro('FAC');
				return false;
			endif;
 * 
 */
            endif;

		
            #MARCO LA ORDEN DE COBRO POR CAJA COMO PROCESADA
            if(!$this->OrdenCajaCobro->marca_procesada($datos['OrdenDescuentoCobro']['orden_caja_cobro_id'])):
                parent::notificar('NO SE PROCESO LA ORDEN DE COBRO CAJA');
                parent::rollback(); 
                $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                $this->TipoDocumento->unLookRegistro('FAC');
                return false;
            endif;
            $aOrdenCajaCobro = array(
                'id' => $datos['OrdenDescuentoCobro']['orden_caja_cobro_id'],
                'orden_descuento_cobro_id' =>  $datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'],
                'proveedor_factura_id' => $nCreditoId,
                'banco_cuenta_movimiento_id' => (isset($datos['OrdenDescuentoCobro']['banco_cuenta_movimiento_id']) ? $datos['OrdenDescuentoCobro']['banco_cuenta_movimiento_id'] : null),
                'recibo_id' => $datos['OrdenDescuentoCobro']['recibo_id'],
                'importe_factura' => $importe_credito,
                'observaciones' => $datos['OrdenDescuentoCobro']['observacion'],
            );
            if(!$this->OrdenCajaCobro->save($aOrdenCajaCobro)):
                parent::notificar('NO SE ACTUALIZO LA ORDEN DE COBRO CAJA');
                parent::rollback(); 
                $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                $this->TipoDocumento->unLookRegistro('FAC');
                return false;
            endif;


            ########################
            # Genero la Provedor Liquidaciones con el id de la orden descuento cobro.
            ########################
/*
 * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
 * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
 * esta funcion queda obsoleta. 26/06/2017
		if(!$this->ProveedorLiquidacion->grabarLiquidacionByCaja($nOrdenDescuentoCobroId)):
			parent::notificar('LA LIQUIDACION A COMERCIO NO PUDO SER ACTUALIZADA');
			parent::rollback(); 
			$this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
			$this->TipoDocumento->unLookRegistro('FAC');
			return false;
		endif;
 * 
 */

		
            $this->id = $nOrdenDescuentoCobroId;
            $datos['OrdenDescuentoCobro']['importe'] = $datos['OrdenDescuentoCobro']['importe_total'];
            if(!parent::save($datos)):
                parent::notificar('LA ORDEN DESCUENTO COBRO NO PUDO SER ACTUALIZADA');
                parent::rollback(); 
                $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
                $this->TipoDocumento->unLookRegistro('FAC');
                return false;
            endif;
		
		
            parent::commit();

            # ACUMULO LOS NUMERO DE FACTURAS QUE HE GENERADO
            if(isset($datos['OrdenDescuentoCobro']['compensa_pago'])):
                $this->TipoDocumento->unLookRegistro($datos['OrdenDescuentoCobro']['tipo_documento']);
            else:
                $this->TipoDocumento->putNumero($datos['OrdenDescuentoCobro']['tipo_documento']);
            endif;
            $this->TipoDocumento->putNumero('FAC', $nCantidadFactura);


            return $nReciboId;

	}


	function generarOrdenCobroCaja($data){
		$flag = false;
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$this->OrdenDescuentoCuota = new OrdenDescuentoCuota();
		
		App::import('Model','Mutual.OrdenCajaCobroCuota');
		$this->OrdenCajaCobroCuota = new OrdenCajaCobroCuota();	

		App::import('Model','Mutual.OrdenCajaCobro');
		$this->OrdenCajaCobro = new OrdenCajaCobro();	
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$this->OrdenDescuentoCobroCuota = new OrdenDescuentoCobroCuota();	

		App::import('Model','Proveedores.ProveedorComision');
		$oCOMISION = new ProveedorComision();		
		
                parent::begin();
		
		#GRABAR LA CABECERA DEL COBRO
		
		$fechaCobro = parent::armaFecha($data['OrdenDescuentoCobro']['fecha_comprobante']);
		
		$tipoCobro = (isset($data['OrdenDescuentoCobro']['tipo_cobro']) ? $data['OrdenDescuentoCobro']['tipo_cobro'] : 'MUTUTCOBCAJA');
		$data['OrdenDescuentoCobro']['tipo_cobro'] = ($data['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] != MUTUALPROVEEDORID ? 'MUTUTCOBCACO' : $tipoCobro);
		$data['OrdenDescuentoCobro']['fecha'] = $fechaCobro;
		$data['OrdenDescuentoCobro']['importe'] = $data['OrdenDescuentoCobro']['importe_total'];
		if (isset($data['OrdenDescuentoCobro']['observacion'])) {
                    $data['OrdenDescuentoCobro']['observaciones'] = $data['OrdenDescuentoCobro']['observacion'];
                }

                $periodo_cobro = date('Ym',strtotime($fechaCobro));
		$data['OrdenDescuentoCobro']['periodo_cobro'] = $periodo_cobro;
		$data['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] = $data['OrdenDescuentoCobro']['proveedor_origen_fondo_id'];

		$this->id = 0;
		
		if(!parent::save($data)):
                    parent::rollback();
                    parent::notificar('NO SE GENERO LA ORDEN DESCUENTO COBRO');
                    return false;
		endif;

		$id = $this->getLastInsertID();
		
	
		#GRABAR EL DETALLE DEL COBRO
		//saco las cuotas de la orden
		$cuotas = $this->OrdenCajaCobroCuota->findAllByOrdenCajaCobroId($data['OrdenDescuentoCobro']['orden_caja_cobro_id']);

		foreach($cuotas as $cuota){
			
			$proveedor_id = $this->OrdenDescuentoCuota->field('proveedor_id',"OrdenDescuentoCuota.id = ".$cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
			
			#CALCULO LA COMISION POR LA COBRANZA
			$comision = $this->OrdenDescuentoCobroCuota->calcularComisionCobranza($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'],$cuota['OrdenCajaCobroCuota']['importe_abonado']);
				
			#GUARDO EL DETALLE DEL PAGO DE LA CUOTA
			$cobroCuota = array();
			$cobroCuota['OrdenDescuentoCobroCuota'] = array();
			$cobroCuota['OrdenDescuentoCobroCuota']['periodo_cobro'] = $periodo_cobro;
			$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'] = $id;
			$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'] = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
			$cobroCuota['OrdenDescuentoCobroCuota']['importe'] = $cuota['OrdenCajaCobroCuota']['importe_abonado'];
			$cobroCuota['OrdenDescuentoCobroCuota']['proveedor_id'] = $proveedor_id;
			$cobroCuota['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'] = $comision['alicuota'];
			$cobroCuota['OrdenDescuentoCobroCuota']['comision_cobranza'] = $comision['comision'];
			
			
			if($this->OrdenDescuentoCobroCuota->save($cobroCuota)){
				$flag = true;
				#MARCO LA CUOTA COMO PAGADA SI LA PAGA TOTALMENTE
				if($cuota['OrdenCajaCobroCuota']['importe_abonado'] == $cuota['OrdenCajaCobroCuota']['importe']){
					$this->OrdenDescuentoCuota->id = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
					if(!$this->OrdenDescuentoCuota->saveField('estado','P')){
                                            parent::rollback();
                                            parent::notificar('NO SE ACTUALIZO EL ESTADO DE LA CUOTA');
                                            $flag = false;
                                            break;
					}
					/*if(!$this->__setPeriodoCobro($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'],$periodo_cobro)){
                                            parent::rollback();
                                            parent::notificar('NO SE ACTUALIZO EL PERIODO COBRO DE LA CUOTA');
                                            $flag = false;
                                            break;				
					}*/					
//					//si el periodo de la cuota es mayor al periodo de cobro llevo el periodo de la cuota al periodo de cobro
//					$periodoCuota = $this->OrdenDescuentoCuota->getPeriodo($cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
//					if($periodo_cobro < $periodoCuota && !empty($periodoCuota)){
//						$this->OrdenDescuentoCuota->id = $cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id'];
//						$periodo_origen = $this->OrdenDescuentoCuota->read('periodo',$cuota['OrdenCajaCobroCuota']['orden_descuento_cuota_id']);
//						$this->OrdenDescuentoCuota->saveField('periodo_origen',$periodo_origen);						
//						$flag = $this->OrdenDescuentoCuota->saveField('periodo',$periodo_cobro);
//					}
				}
			}else{
                            parent::rollback();
                            parent::notificar('NO SE GENERO EL COBRO DE LA CUOTA');
                            $flag = false;
                            break;				
			}
			$this->OrdenDescuentoCobroCuota->id = 0;
		}

		if(!$flag){
                    parent::rollback();
                    return $flag;
		}		
		
		#MARCO LA ORDEN DE COBRO POR CAJA COMO PROCESADA
		if(!$this->OrdenCajaCobro->marca_procesada($data['OrdenDescuentoCobro']['orden_caja_cobro_id'])):
                    parent::rollback();
                    parent::notificar('LA ORDEN COBRO CAJA NO FUE PROCESADA');
                    return false;
		endif;
		
		# ACTUALIZO LA ORDEN DE COBRO POR CAJA CON EL ID DEL COBRO
		$aOrdenCajaCobro = array('OrdenCajaCobro' => array('id' => $data['OrdenDescuentoCobro']['orden_caja_cobro_id'], 'orden_descuento_cobro_id' => $id));
		$this->OrdenCajaCobro->id = $data['OrdenDescuentoCobro']['orden_caja_cobro_id'];
		if(!$this->OrdenCajaCobro->save($aOrdenCajaCobro)):
                    parent::rollback();
                    parent::notificar('LA ORDEN COBRO CAJA NO FUE ACTUALIZADA');
                    return false;
		endif;
                parent::commit();
		return $id;
	}


	/**
	 * traer la suma de cada proveedor de las cuotas cobradas para facturarlas y luego pagarlas.
	 * Tambien se genera la comision por cobranza.
	 */
	function __getCobroCuotaByProveedor($OrdenDescuentoCobroId){

		$sql = "SELECT  global_datos.concepto_1,
				globaldatos.concepto_1,
				proveedores.razon_social,
				orden_descuento_cobro_cuotas.proveedor_id,
				orden_descuentos.*,
				orden_descuento_cobro_cuotas.orden_descuento_cobro_id,
				SUM(orden_descuento_cobro_cuotas.importe) AS importe,
				SUM(orden_descuento_cobro_cuotas.comision_cobranza) AS comision_cobranza
				FROM orden_descuento_cobro_cuotas
				inner join proveedores
				on proveedores.id = proveedor_id
				inner join orden_descuento_cuotas
				on orden_descuento_cuotas.id = orden_descuento_cobro_cuotas.orden_descuento_cuota_id
				inner join orden_descuentos
				on orden_descuentos.id = orden_descuento_cuotas.orden_descuento_id
				inner join global_datos
				on global_datos.id = orden_descuentos.tipo_producto
				inner join global_datos as globaldatos
				on globaldatos.id = global_datos.concepto_2
				WHERE orden_descuento_cobro_cuotas.orden_descuento_cobro_id = '$OrdenDescuentoCobroId'
				GROUP BY orden_descuentos.id, orden_descuento_cobro_cuotas.proveedor_id";
				
		$aOrdenProveedor = $this->query($sql);
		
		foreach($aOrdenProveedor as $key => $aOrDescuento):
			$OrdenDescuentoId = $aOrDescuento['orden_descuentos']['id'];
			$sqlCuotas = "select	OrdenDescuentoCuota.nro_cuota as nro_cuota, OrdenDescuentoCuota.periodo
							from	orden_descuento_cuotas OrdenDescuentoCuota
							inner	join orden_caja_cobro_cuotas OrdenCajaCobroCuota
							on	OrdenDescuentoCuota.id = OrdenCajaCobroCuota.orden_descuento_cuota_id
							where	OrdenDescuentoCuota.orden_descuento_id = '$OrdenDescuentoId'";
			$aOrdenDescuentoCuotas = $this->query($sqlCuotas);
			
			$cuotas = Set::extract('/OrdenDescuentoCuota/nro_cuota',$aOrdenDescuentoCuotas);
			$strDesc = implode('-', $cuotas) ."/" . $aOrDescuento['orden_descuentos']['cuotas'];

			$periodo = Set::extract('/OrdenDescuentoCuota/periodo', $aOrdenDescuentoCuotas);
			foreach($periodo as $clave => $valor):
				$periodo[$clave] = substr($valor,0,4) . "/" . substr($valor,-2);
			endforeach;
			$strPeriodo = implode('-', $periodo);

			$aOrdenProveedor[$key]['concepto'] = 'EXPTE: ' . $aOrDescuento['orden_descuentos']['numero'] . ' - ' . $aOrDescuento['global_datos']['concepto_1'] . ' - ctas: ' . $strDesc;
			if($aOrDescuento['orden_descuentos']['tipo_orden_dto'] == 'CMUTU'):
				$aOrdenProveedor[$key]['concepto'] = $aOrDescuento['globaldatos']['concepto_1'] . ' - PER.: ' . $strPeriodo;
			endif; 
		endforeach;
		
		return $aOrdenProveedor;
					
	}
	
	
	/**
	 * IMPUTA LA LIQUIDACION DE UN SOCIO
	 * 
	 * @author adrian [27/03/2012]
	 * @param integer $liquidacion_id
	 * @param integer $socio_id
	 * @param boolean $desimputar
	 * @return array
	 */
	function imputarLiquidacion($liquidacion_id,$socio_id,$fecha_pago,$nro_recibo,$desimputar = false,$reliquidar = true){
		
		$ret = array('ERROR' => 0, 'MENSAJE' => '');
		
		if(empty($liquidacion_id)) return $ret;
		if(empty($socio_id)) return $ret;
		
		#######################################################################################
		# CARGO LA REFERENCIA A OBJETOS
		#######################################################################################
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		App::import('Model','Mutual.MutualAdicionalPendiente');
		App::import('Model','Mutual.Liquidacion');
		App::import('Model','Mutual.LiquidacionSocio');
		App::import('Model','Mutual.LiquidacionCuota');
		App::import('Model','Mutual.OrdenDescuentoCuota');
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');	
		App::import('Model', 'Mutual.MutualAdicionalPendiente');
		App::import('Model','Pfyj.SocioReintegro');	
		App::import('Model','pfyj.Socio');	
		App::import('Model','Mutual.OrdenDescuento');
		App::import('Model','Pfyj.SocioCalificacion');
		
		$oLSR 			= new LiquidacionSocioRendicion();
		$oAP 			= new MutualAdicionalPendiente();	
		$oLQ 			= new Liquidacion();
		$oLS 			= new LiquidacionSocio();
		$oLC 			= new LiquidacionCuota();	
		$oCUOTA 		= new OrdenDescuentoCuota();								
		$oCOBROCUOTA 	= new OrdenDescuentoCobroCuota();	
		$oADIC 			= new MutualAdicionalPendiente();	
		$oREINTEGRO 	= new SocioReintegro();	
		$oSOCIO 		= new Socio();
		$oDTO 			= new OrdenDescuento();	
		$oSC 			= new SocioCalificacion();							


		#######################################################################################
		# SETEO EL PERIODO DE COBRO
		#######################################################################################
		$liquidacion 	= $oLQ->read(null,$liquidacion_id);
		$periodoCobro 	= $liquidacion['Liquidacion']['periodo'];
		$organismo 	= $liquidacion['Liquidacion']['codigo_organismo'];
		
		parent::begin();
		
		#######################################################################################
		# BORRO EL COBRO Y EL GASTO ADMINISTRATIVO GENERADO
		#######################################################################################
		if($desimputar):
			$cobroAnteriorID = $oLSR->getOrdenCobroID($socio_id,$liquidacion_id);
			if($cobroAnteriorID != 0) $this->borrarDetalle($cobroAnteriorID,false);
			$oAP->borrarCuotasDevengadasBySocioByLiquidacionId($socio_id,$liquidacion_id);
		endif;
		
		#######################################################################################
		# RELIQUIDO AL SOCIO [GENERA LA LIQUIDACION CUOTAS NUEVAMENTE Y PROCESA EL LOS DEBITOS COBRADOS]
		#######################################################################################
		if($reliquidar){
                        //$socio_id,$periodo,$cerrada,$imputada,$organismo,$soloProcesaIntercambio,$excludeLiquidacionBloquedas,$excludeLiquidacionEnProceso,$liquidacion_id,$generaLiqSocio
			$ret = $oLS->reliquidar($socio_id,$liquidacion['Liquidacion']['periodo'],true,false,$organismo,false,false,true,$liquidacion_id,FALSE);	

			if($ret[0] == 1):
				parent::rollback();
				$ret = array('ERROR' => 1, 'MENSAJE' => "[1] ".$ret[1]);
				return $ret;
			endif;
		}

		#######################################################################################
		# CARGO LAS CUOTAS PAGADAS
		#######################################################################################
		$cuotas = $oLC->getCuotasPagadasByLiquidacion($liquidacion_id,$socio_id,null,true);
		
		#######################################################################################
		# NO TIENE CUOTAS PARA IMPUTAR --> GENERO UN REINTEGRO SI CORRESPONDE Y SALGO DEL METODO
		#######################################################################################		
		if(empty($cuotas)):
		
			$impoDebitado = $oLSR->getTotalBySocioByLiquidacion($socio_id,$liquidacion_id,1);
			$impoImputado = 0;
			
			if($impoDebitado != 0):
			
				#-- verificar si tiene reintegros no pagados
				$oREINTEGRO->deleteAll("SocioReintegro.liquidacion_id = " . $liquidacion_id ." AND SocioReintegro.socio_id = ".$socio_id . " AND SocioReintegro.anticipado = 0");
			
                                $anticipado = $oREINTEGRO->getTotalReintegrosAnticipados($socio_id,$liquidacion_id);
                                $impoReintegro = abs($impoDebitado - $impoImputado);

                                $reintegroNeto = round($impoReintegro - $anticipado,2);
                
				$reintegro = array('SocioReintegro' => array(
							'id' => 0,
							'socio_id' => $socio_id,
							'liquidacion_id' => $liquidacion_id,
							'periodo' => $periodoCobro,
							'importe_dto' => 0,
							'importe_debitado' => $impoDebitado,
							'importe_imputado' => 0,
							'importe_reintegro' => $reintegroNeto
				));
                                if($reintegroNeto != 0){
                                    if(!$oREINTEGRO->save($reintegro)){
                                            parent::rollback();
                                            $ret = array('ERROR' => 1, 'MENSAJE' => "[1] SOCIO SIN LIQUIDACION - GENERA REINTEGRO");
                                            return $ret;
                                    }
                                }
			
			endif;


			/**
			 * Si tiene configurado el metodo 6 de adicionales (gastos bancarios)
			 * Si no tiene nada imputado devengar la cuota?
			 * ADRIAN: 22/03/2021
			 */

			
			parent::commit();
			return $ret;
		
		endif;
		
		
		#######################################################################################
		# GENERO UN COBRO
		#######################################################################################
		$ACUM_IMPUTADO 		= 0;
		$saldoActual 		= 0;
		$importeDebitado 	= 0;
		
		$cuotaPagada 	= array();
		$cuotasPagadas 	= array();

		$ESTATUS = TRUE;
		
		foreach($cuotas as $idx => $cuota):
		
			#SI TIENE UN ADICIONAL NO DEVENGADO LO PROCESO
			if($cuota['LiquidacionCuota']['mutual_adicional_pendiente_id'] != 0 && $cuota['LiquidacionCuota']['orden_descuento_cuota_id'] == 0):
			
				$cuotaAdicional = array('OrdenDescuentoCuota' => array(
								'id' => 0,
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
								'vencimiento' => $fecha_pago,
								'vencimiento_proveedor' => $fecha_pago,
				                'capital' => $cuota['LiquidacionCuota']['importe_debitado'],
            				    'interes' => 0,
            				    'iva' => 0,
							));			
					$ESTATUS = $oCUOTA->save($cuotaAdicional);

					if(!$ESTATUS) break;
					
					$cuota['LiquidacionCuota']['orden_descuento_cuota_id'] = $oCUOTA->getLastInsertID();
					$adicional = $oADIC->read(null,$cuota['LiquidacionCuota']['mutual_adicional_pendiente_id']);
					
					$adicional['MutualAdicionalPendiente']['procesado'] = 1;
					$adicional['MutualAdicionalPendiente']['orden_descuento_id'] = $cuota['LiquidacionCuota']['orden_descuento_id'];
					$adicional['MutualAdicionalPendiente']['orden_descuento_cuota_id'] = $cuota['LiquidacionCuota']['orden_descuento_cuota_id'];
					
					$ESTATUS = $oADIC->save($adicional);

					if(!$ESTATUS) break;
							
			endif;
			
			$importeDebitado = $cuota['LiquidacionCuota']['importe_debitado'];
			
			$saldoActual = $oCUOTA->getSaldo($cuota['LiquidacionCuota']['orden_descuento_cuota_id']);
			
			$cuota['LiquidacionCuota']['saldo_actual'] = $saldoActual;
			
			if($importeDebitado > $saldoActual) $cuota['LiquidacionCuota']['importe_debitado'] = $saldoActual;

			$ACUM_IMPUTADO += $cuota['LiquidacionCuota']['importe_debitado'];
			
			$cuotaPagada['periodo_cobro'] = $periodoCobro;
			
			$cuotaPagada['orden_descuento_cuota_id'] = $cuota['LiquidacionCuota']['orden_descuento_cuota_id'];
			
			$cuotaPagada['proveedor_id'] = $cuota['LiquidacionCuota']['proveedor_id'];
			
			$cuotaPagada['importe'] = $cuota['LiquidacionCuota']['importe_debitado'];

			$comision = $oCOBROCUOTA->calcularComisionCobranza($cuotaPagada['orden_descuento_cuota_id'],$cuotaPagada['importe']);
			
			$cuotaPagada['alicuota_comision_cobranza'] = $comision['alicuota'];
			
			$cuotaPagada['comision_cobranza'] = $comision['comision'];			
			
			$cuotaPagada['pago_total_cuota'] = ($cuota['LiquidacionCuota']['importe_debitado'] == $saldoActual ? 1 : 0);	

			if($saldoActual != 0)array_push($cuotasPagadas,$cuotaPagada);
			
			$cuota['LiquidacionCuota']['alicuota_comision_cobranza'] = $comision['alicuota'];
			$cuota['LiquidacionCuota']['comision_cobranza'] = $comision['comision'];
			
			$cuotas[$idx] = $cuota;
			
		endforeach;
		
		if(!$ESTATUS):
			$ret = array('ERROR' => 1, 'MENSAJE' => '[2] ERROR AL PROCESAR ADICIONAL');
			parent::rollback();
			return $ret;
		endif;
		
		
		$pago = array('OrdenDescuentoCobro' => array(
			'id' => 0,
			'tipo_cobro' => 'MUTUTCOBRECS',
			'socio_id' => $socio_id,	
			'fecha' => $fecha_pago,
			'nro_recibo' => $nro_recibo,
			'importe' => $ACUM_IMPUTADO,
			'periodo_cobro' => $periodoCobro
		));

		$pago['OrdenDescuentoCobroCuota'] = $cuotasPagadas;		
		
		if(!$this->saveAll($pago,array('atomic'=>false))):
			$ret = array('ERROR' => 1, 'MENSAJE' => '[3] ERROR AL GENERAR EL COBRO');
			parent::rollback();
			return $ret;
		endif;
		
		#######################################################################################
		# ACTUALIZO EL ID DEL COBRO EN LA LIQUIDACION CUOTAS
		#######################################################################################		
		foreach($cuotas as $idx => $cuota):
			$cuota['LiquidacionCuota']['orden_descuento_cobro_id'] = $this->id;
			$cuota['LiquidacionCuota']['imputada'] = 1;
			$ESTATUS = $oLC->save($cuota);
			// if(!$ESTATUS) break;
			// $ESTATUS = $oCUOTA->cambiarEstado($cuota['LiquidacionCuota']['orden_descuento_cuota_id'],'P');
			if(!$ESTATUS) break;
			$cuotas[$idx] = $cuota;		
		endforeach;
		
		if(!$ESTATUS):
			$ret = array('ERROR' => 1, 'MENSAJE' => '[4] ERROR AL ACTUALIZAR EL # COBRO EN LA LIQUIDACION CUOTAS');
			parent::rollback();
			return $ret;
		endif;
		
		#######################################################################################
		# GRABAR EN LA RENDICION SOCIOS EL ID DEL COBRO
		#######################################################################################
		$ESTATUS = $oLSR->updateAll(
						array(
								'LiquidacionSocioRendicion.orden_descuento_cobro_id' => $this->id,
							),
						array(
								'LiquidacionSocioRendicion.liquidacion_id' => $liquidacion_id, 
								'LiquidacionSocioRendicion.socio_id' => $socio_id,
								'LiquidacionSocioRendicion.indica_pago' => 1
						)
		);
		if(!$ESTATUS):
			$ret = array('ERROR' => 1, 'MENSAJE' => '[5] ERROR AL ACTUALIZAR EL # COBRO EN LAS RENDICIONES');
			parent::rollback();
			return $ret;
		endif;				
		
		#######################################################################################
		# PROCESO REINTEGROS
		#######################################################################################			
		$oREINTEGRO->deleteAll("SocioReintegro.liquidacion_id = $liquidacion_id AND SocioReintegro.socio_id = $socio_id AND SocioReintegro.anticipado = 0");
		
		$impoImputado = $oLC->getTotalImputadoBySocioByLiquidacion($liquidacion_id,$socio_id);
		$impoDebitado = $oLSR->getTotalBySocioByLiquidacion($socio_id,$liquidacion_id,1);
		
		$impoLiquidado = $oLS->getTotalImporteLiquidadoBySocio($liquidacion_id,$socio_id);
		
		//VERIFICO QUE NO TENGA REINTEGROS ANTICIPADOS (UNICAMENTE LO EFECTIVAMENTE PAGADO)
		$anticipado = $oREINTEGRO->getTotalReintegrosAnticipados($socio_id,$liquidacion_id);
		$impoReintegro = abs($impoDebitado - $impoImputado);
		
		$reintegroNeto = round($impoReintegro - $anticipado,2);
		
		$reintegro = array();
		
		if($anticipado != 0):
		
			#CARGAR LOS REINTEGROS ANTICIPADOS 
			$reintegros = $oREINTEGRO->getReintegrosAnticipados($socio_id,$liquidacion_id);
			$reintegrosIds = Set::extract("/SocioReintegro/id",$reintegros);
		
			if($anticipado > $impoReintegro && $oSOCIO->isActivo($socio_id)):
			
				//cargar una ND por la diferencia para recuperar el anticipo pagado de mas
				$proveedor_id = parent::GlobalDato('entero_1','MUTUTCUONDRR');
				$orden = $oSOCIO->getOrdenDtoCuotaSocial($socio_id);
				$cuotaRecuReintegro = array('OrdenDescuentoCuota' => array(
											'id' => 0,
											'orden_descuento_id' => $orden['OrdenDescuento']['id'],
											'persona_beneficio_id' => $orden['OrdenDescuento']['persona_beneficio_id'],
											'socio_id' => $socio_id,
											'tipo_orden_dto' => $orden['OrdenDescuento']['tipo_orden_dto'],
											'tipo_producto' => $orden['OrdenDescuento']['tipo_producto'],
											'periodo' => $periodoCobro,
											'nro_cuota' => 0,
											'tipo_cuota' => 'MUTUTCUONDRR',
											'estado' => 'A',
											'situacion' => 'MUTUSICUMUTU',
											'importe' => $anticipado - $impoReintegro,
											'proveedor_id' => $proveedor_id,
											'vencimiento' => $fecha_pago,
											'vencimiento_proveedor' => $fecha_pago
							));	
				
				$ESTATUS = $oCUOTA->save($cuotaRecuReintegro);
				if(!$ESTATUS):
					$ret = array('ERROR' => 1, 'MENSAJE' => '[7] ERROR AL GENERAR LA CUOTA RECUPERO DE REINTEGRO ANTICIPADO');
					parent::rollback();
					return $ret;
				endif;					
				
				$ESTATUS = $oREINTEGRO->updateAll(array(
												'SocioReintegro.recupero_cuota_id' => $oCUOTA->id,
												'SocioReintegro.importe_debitado' => $impoDebitado,
												'SocioReintegro.importe_imputado' => $impoImputado,
												'SocioReintegro.importe_reintegro' => $impoReintegro,
											),
										array('SocioReintegro.id' => $reintegrosIds)
										);
				if(!$ESTATUS):
					$ret = array('ERROR' => 1, 'MENSAJE' => '[8] ERROR AL ACTUALIZAR LOS DATOS DEL REINTEGRO (CUOTA REINTEGRO ANTICIPADO)');
					parent::rollback();
					return $ret;
				endif;								
			
			elseif($impoReintegro!=0):
			
				foreach($reintegros as $reintegro):
					$reintegro['SocioReintegro']['importe_debitado'] = $reintegro['SocioReintegro']['pagos'];
					$reintegro['SocioReintegro']['importe_reintegro'] = $reintegro['SocioReintegro']['pagos'];					
					$ESTATUS = $oREINTEGRO->save($reintegro);
					if(!$ESTATUS) break; 	
				endforeach;
				if(!$ESTATUS):
					$ret = array('ERROR' => 1, 'MENSAJE' => '[9] ERROR AL ACTUALIZAR LOS DATOS DEL REINTEGRO');
					parent::rollback();
					return $ret;
				endif;				
				//cargar un reintegro por la diferencia
				$reintegro = array('SocioReintegro' => array(
							'id' => 0,
							'socio_id' => $socio_id,
							'liquidacion_id' => $liquidacion_id,
							'periodo' => $periodoCobro,
							'importe_dto' => $impoLiquidado,
							'importe_debitado' => $impoDebitado - $anticipado,
							'importe_imputado' => $impoImputado - $anticipado,
							'importe_reintegro' => $reintegroNeto,
				));	
				if(!$oREINTEGRO->save($reintegro)):
					$ret = array('ERROR' => 1, 'MENSAJE' => '[10] ERROR AL GENERAR REINTEGRO POR LA DIFERENCIA');
					parent::rollback();
					return $ret;				
				endif;							
					
			endif;
		
		elseif($impoImputado < $impoDebitado):
		
			$reintegro = array('SocioReintegro' => array(
						'id' => 0,
						'socio_id' => $socio_id,
						'liquidacion_id' => $liquidacion_id,
						'periodo' => $periodoCobro,
						'importe_dto' => $impoLiquidado,
						'importe_debitado' => $impoDebitado,
						'importe_imputado' => $impoImputado,
						'importe_reintegro' => $impoReintegro
			));	
			if(!$oREINTEGRO->save($reintegro)):
				$ret = array('ERROR' => 1, 'MENSAJE' => '[11] ERROR AL GENERAR REINTEGRO');
				parent::rollback();
				return $ret;			
			endif;	
		
		endif;
		
		#######################################################################################
		#CONTROL GENERAL
		#######################################################################################
		#ERROR CONTROL IMPUTADO SEGUN LIQUIDACION_CUOTAS Y ORDEN_DESCUENTO_COBRO_CUOTAS
		$TOTAL_COBRO = $oCOBROCUOTA->getMontoPagoByOrdenCobro($this->id);
		$TOTAL_COBRO = round($TOTAL_COBRO,2);
		$impoImputado = round($impoImputado,2);
		if($impoImputado != $TOTAL_COBRO):
			$ret = array('ERROR' => 1, 'MENSAJE' => "TOTAL IMPUTADO S/LIQUIDACION [$impoImputado] <> TOTAL COBRADO [$TOTAL_COBRO]");
			parent::rollback();			
			return $ret;
		endif;
		
		#ERROR CUOTAS NO IMPUTADAS CUANDO TIENE UN DEBITO COBRADO
		$TOTAL_CONTROL = round($impoImputado + $reintegroNeto + $anticipado,2);
		$impoDebitado = round($impoDebitado,2);
		if($impoDebitado != $TOTAL_CONTROL):
			$ret = array('ERROR' => 1, 'MENSAJE' => "EL IMPORTE DEBITADO [$impoDebitado] <> IMPUTADO + ANTICIPOS + REINTEGRO [$TOTAL_CONTROL]");
			parent::rollback();			
			return $ret;
		endif;
		
		#######################################################################################
		# FIN
		#######################################################################################			
		parent::commit();

		
		#######################################################################################
		# CALIFICO AL SOCIO
		#######################################################################################
		$sql = "	
				select
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
				and Liquidacion.periodo = '$periodoCobro'
				and IFNULL(LiquidacionSocioRendicion.status,'') <> ''
				group by Liquidacion.id,LiquidacionSocioRendicion.socio_id,LiquidacionSocio.persona_beneficio_id,LiquidacionSocioRendicion.banco_intercambio,LiquidacionSocioRendicion.status
				order by LiquidacionSocioRendicion.indica_pago ASC, LiquidacionSocioRendicion.indica_pago LIMIT 1";
		$datos = $oSC->query($sql);
		if(!empty($datos)):
			$persona_beneficio_id = $datos[0]['LiquidacionSocio']['persona_beneficio_id'];
			$calificacion = $datos[0]['BancoRendicionCodigo']['calificacion_socio'];
			$oSC->deleteAll("SocioCalificacion.socio_id = $socio_id and SocioCalificacion.persona_beneficio_id = $persona_beneficio_id and SocioCalificacion.periodo = '$periodoCobro'");
			$oSC->calificar($socio_id,$calificacion,$persona_beneficio_id,$periodoCobro,$fecha_pago);
		endif;
		
		
		$ret = array(
						'ERROR' => 0, 
						'MENSAJE' => "IMPUTADO CORRECTAMENTE",
						'COBRO_ID' => $this->id,
						'DEBITADO' => $impoDebitado,
						'COBRADO' => $TOTAL_COBRO,
						'IMPUTADO' => $impoImputado,
						'REINTEGRO_ANTICIPADO' => $anticipado,
						'REINTEGRO_CALCULADO' => $impoReintegro,
						'REINTEGRO_NETO' => $reintegroNeto,
					);		
		return $ret;
		
	}
	
	
	function desimputarLiquidacion($liquidacion_id,$socio_id){
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();	
		
		$cobroAnteriorID = $oLSR->getOrdenCobroID($socio_id,$liquidacion_id);
		
		#BORRAR LOS COBROS
		if($cobroAnteriorID != 0) $this->borrarDetalle($cobroAnteriorID,false);
		
		#BORRAR LOS ADICIONALES PENDIENTES DEVENGADOS Y COBRADOS
		App::import('Model','Mutual.MutualAdicionalPendiente');
		$oAP = new MutualAdicionalPendiente();		
		$oAP->borrarCuotasDevengadasBySocioByLiquidacionId($socio_id,$liquidacion_id);
		
		#MARCAR COMO NO IMPUTADAS LAS CUOTAS (DEJAR PRE)
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		$oLC->updateAll(array(
								'LiquidacionCuota.imputada' => 0,
								'LiquidacionCuota.orden_descuento_cobro_id' => 0,
								'LiquidacionCuota.alicuota_comision_cobranza' => 0,
								'LiquidacionCuota.comision_cobranza' => 0,
							),
						array('LiquidacionCuota.liquidacion_id' => $liquidacion_id, 'LiquidacionCuota.socio_id' => $socio_id)
		);
		#MARCAR COMO A LAS CUOTAS ORIGINALES
		
		#MARCAR LA SOCIO RENDICION CON COBRO_ID = 0
		$oLSR->updateAll(
						array(
								'LiquidacionSocioRendicion.orden_descuento_cobro_id' => 0,
							),
						array(
								'LiquidacionSocioRendicion.liquidacion_id' => $liquidacion_id, 
								'LiquidacionSocioRendicion.socio_id' => $socio_id,
								'LiquidacionSocioRendicion.indica_pago' => 1
						)
		);		
		
		#BORRAR TODOS LOS REINTEGROS GENERADOS
		App::import('Model','Pfyj.SocioReintegro');
		$oREINTEGRO = new SocioReintegro();			
		$oREINTEGRO->deleteAll("SocioReintegro.liquidacion_id = $liquidacion_id AND SocioReintegro.socio_id = $socio_id AND SocioReintegro.anticipado = 0");
				
	}
	
	
	function cobrarGastoAdminSelladosPrestamo($socio_id, $cuotas, $fecha = NULL, $periodoCobro = NULL, $tipoCobro = 'MUTUTCOBCACO') {
	    
	    if(empty($cuotas)) {return false;}
	    if(empty($fecha)) {$fecha = date('Y-m-d');}
	    if(empty($periodoCobro)) {$periodoCobro = date('Ym', strtotime($fecha));}
	    
	    $ACUM_IMPUTADO = 0;
	    $cuotasPagadas = array();
	    foreach ($cuotas as $cuota) {
	        $ACUM_IMPUTADO += $cuota['importe'];
	        array_push($cuotasPagadas,array(
	            'periodo_cobro' => $periodoCobro,
	            'orden_descuento_cuota_id' => $cuota['id'],
	            'proveedor_id' => $cuota['proveedor_id'],
	            'importe' => $cuota['importe']
	        ));
	    }
    
	    $cobro = array('OrdenDescuentoCobro' => array(
	        'id' => 0,
	        'tipo_cobro' => $tipoCobro,
	        'socio_id' => $socio_id,
	        'fecha' => $fecha,
	        'nro_recibo' => 0,
	        'importe' => $ACUM_IMPUTADO,
	        'periodo_cobro' => $periodoCobro
	    ));
	    
	    $cobro['OrdenDescuentoCobroCuota'] = $cuotasPagadas;    
	    if(!$this->saveAll($cobro,array('atomic'=>false))):
	       return false;
	    endif;
	    return true;
	    
	}
	
}
?>

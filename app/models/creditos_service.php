<?php 
/**
 * WEB SERVICE PARA CONEXION CON FINANCIERAS
 * @author adrian
 *
 */

App::import('Vendor','sigem_service');
App::import('Model', 'V1.Solicitud');
App::import('Model', 'mutual.OrdenDescuento');
App::import('Model', 'mutual.OrdenDescuentoCuota');
App::import('Model', 'mutual.OrdenDescuentoCobro');
App::import('Model', 'mutual.OrdenDescuentoCobroCuota');

class CreditosService extends SIGEMService{
	
	var $name = 'CreditosService';
	var $useTable = false;
	var $codigoRechazoSolicitud = 10;
	var $codigoAprobacion = 11;

	
	/**
	 * Obtiene un token
	 * @param string $PIN
         * @return string
	 */        
        function getToken($PIN) {
            $this->validatePIN($PIN, false);
            return $this->getResponse();            
        }        
        
	/**
	 * Valida la conexion
	 * @param string $PIN
	 * @return string
	 */
	function validate($PIN){
		$this->validatePIN($PIN);
		return $this->getResponse();
	}
	
	/**
	 *  Devuelve las solicitudes pendientes de aprobacion
	 * @param string $PIN
         * @param Integer $offset Description
         * @param Integer $limit Description         * 
	 * @return string
	 */
	function getSolicitudesPendientesAprobar($PIN,$offset,$limit){
		if(!$this->validatePIN($PIN)) return $this->getResponse();
		$oSOLICITUD = new Solicitud();
                $limit = (empty($limit) ? 50 : ($limit > 50 ? 50 : $limit));
                $offset = (empty($offset) ? 0 : ($offset > $limit ? $limit : $offset));                
		$solicitudes = $oSOLICITUD->getSolicitudesByCuitProveedor($this->proveedor,9,$offset,$limit);
		$this->setResponse('find',count($solicitudes));
		$pendientes = array();
		if(!empty($solicitudes)):
			foreach($solicitudes as $solicitud):
				array_push($pendientes,$this->armaDatoSolicitud($solicitud));
			endforeach;
		endif;		
		$this->setResponse('result',$pendientes);
		return $this->getResponse();
	}
	
	/**
	 * Traer una solicitud para un proveedor
	 * @param string $nroSolicitud
	 * @param string $fechaPagoProveedor
	 * @param string $PIN
	 * @return string
	 */	
	function getSolicitud($nroSolicitud,$fechaPagoProveedor,$PIN){
		if(!$this->validatePIN($PIN)) return $this->getResponse();
		$oSOLICITUD = new Solicitud();
		$solicitud = $oSOLICITUD->getSolicitud($nroSolicitud);
		if($solicitud['Solicitud']['cuit_proveedor'] != $this->proveedor['cuit']
		&& $solicitud['Solicitud']['reasignar_proveedor_id'] != $this->proveedor['id']
		){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"EL NUMERO DE SOLICITUD NO PERTENECE AL PIN");
			return $this->getResponse();
		}
		if(empty($solicitud)){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"EL NUMERO DE SOLICITUD NO EXISTE");
			return $this->getResponse();			
		}
		$solicitud = $this->armaDatoSolicitud($solicitud);
		if(!empty($fechaPagoProveedor)){
			App::import('Model', 'Proveedores.ProveedorVencimiento');
			$oVto = new ProveedorVencimiento(null);
			$vtos = $oVto->calculaVencimientoByCodigoOrganismo($this->proveedor['id'],"MUTUCORG".$solicitud['codigo_beneficio'],$fechaPagoProveedor);
			$solicitud['fecha_pago'] = $vtos['fecha_carga'];
			$solicitud['inicia_en'] = $vtos['inicia_en'];
			$solicitud['vto_primer_cuota_socio'] = $vtos['vto_primer_cuota_socio'];
			$solicitud['vto_primer_cuota_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
		}
		$this->setResponse('find',1);
		$this->setResponse('result',$solicitud);
		return $this->getResponse();		
	}
	
	/**
	 * Marca como rechazada una solicitud, genera el evento en el historial y notifica a los usuarios
	 * @param string $nroSolicitud
	 * @param string $motivo
	 * @param string $fechaRechazo
	 * @param string $PIN
	 * @return string
	 */
	function rechazarSolicitud($nroSolicitud,$motivo,$fechaRechazo,$PIN){
		if(!$this->validatePIN($PIN)) return $this->getResponse();
		$oSOLICITUD = new Solicitud();
		$solicitud = $oSOLICITUD->getSolicitud($nroSolicitud);
		if($solicitud['Solicitud']['cuit_proveedor'] != $this->proveedor['cuit']){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"EL NUMERO DE SOLICITUD NO PERTENECE AL PIN");
			return $this->getResponse();
		}
		if(empty($solicitud)){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"EL NUMERO DE SOLICITUD NO EXISTE");
			return $this->getResponse();			
		}
		$solicitud['Solicitud']['estado'] = $this->codigoRechazoSolicitud;
		App::import('Model', 'V1.SolicitudEstadoHist');
		$oEstado = new SolicitudEstadoHist(null);
		if(!$oEstado->grabarHistorial($solicitud['Solicitud']['nro_solicitud'],$this->codigoRechazoSolicitud,$motivo,$this->proveedor['razon_social_resumida'])){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"ERROR: NO SE PUDO GENERAR EL EVENTO EN EL HISTORIAL DE LA SOLICITUD.");
			return $this->getResponse();			
		}		
		if (!$oSOLICITUD->save($solicitud)){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"ERROR: NO SE PUDO ACTUALIZAR EL ESTADO DE LA SOLICITUD.");
			return $this->getResponse();			
		}
		$usuariosTo = $oEstado->getUsuariosNotifica();
		$ERROR_NOTIFICA = FALSE;
		if(!empty($usuariosTo)):
			App::import('Model', 'V1.SolicitudEvento');
			$oEVENTO = new SolicitudEvento(null);
			$evento = array();
			foreach($usuariosTo as $user):
				$evento['SolicitudEvento']['id'] = 0;
				$evento['SolicitudEvento']['fecha'] = date("Y-m-d H:i:s");
				$evento['SolicitudEvento']['usuario_from'] = $this->proveedor['razon_social_resumida'];
				$evento['SolicitudEvento']['usuario_to'] = $user;
				$evento['SolicitudEvento']['estado_solicitud'] = $this->codigoRechazoSolicitud;
				$evento['SolicitudEvento']['nro_solicitud'] = $solicitud['Solicitud']['nro_solicitud'];
				$evento['SolicitudEvento']['comentario'] = $motivo;
				if(!$oEVENTO->save($evento)){
					$ERROR_NOTIFICA = TRUE;
					break;
				}
			endforeach;
			if($ERROR_NOTIFICA){
				$this->setResponse('error',1);
				$this->setResponse('msg_error',"ERROR: NO SE PUDO NOTIFICAR A LOS SUPERVISORES PERO LA SOLICITUD FUE MARCADA COMO RECHAZADA CORRECTAMENTE");
				return $this->getResponse();					
			}
		
		endif;
		$this->setResponse('find',0);
		$this->setResponse('result',"SOLICITUD #$nroSolicitud --> RECHAZADA CORRECTAMENTE");
		return $this->getResponse();		
	}
	
	
	/**
	 * Marca como aprobada por el proveedor una solicitud y carga los datos
	 * de referencia. Notifica a los supervisores.
	 * 	forma_pago (0001 = EFECTIVO | 0002 = CHEQUE | 0003 = GIRO / TRANSFERENCIA | 0004 = DEPOSITO EN CUENTA)
	 *  banco_pago (CODIGO INDICADO POR EL BCRA 5 DIGITOS)
	 *  periodo_ini (AAAAMM)
	 *  vto_primer_cuota_socio (AAAAMMDD)
	 *  vto_primer_cuota_proveedor (AAAAMMDD)
	 * @param string $nroSolicitud
	 * @param string $nroCreditoAsingado
	 * @param string $fechaOperacion
	 * @param string $datosLiquidacionJsonEncode
	 * @param string $PIN
	 * @return string
	 */
	function aprobarSolicitud($nroSolicitud,$nroCreditoAsingado,$fechaOperacion,$datosLiquidacionJsonEncode,$PIN){
		if(!$this->validatePIN($PIN)) return $this->getResponse();
		
		$oSOLICITUD = new Solicitud();
		$solicitud = $oSOLICITUD->getSolicitud($nroSolicitud);
		if($solicitud['Solicitud']['cuit_proveedor'] != $this->proveedor['cuit']){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"EL NUMERO DE SOLICITUD NO PERTENECE AL PIN");
			return $this->getResponse();
		}
		if(empty($solicitud)){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"EL NUMERO DE SOLICITUD NO EXISTE");
			return $this->getResponse();			
		}
		
//		if($solicitud['Solicitud']['estado'] == $this->codigoAprobacion){
//			$this->setResponse('error',1);
//			$this->setResponse('msg_error',"LA SOLICITUD #$nroSolicitud YA SE ENCUENTRA MARCADA COMO APROBADA POR EL PROVEEDOR");
//			return $this->getResponse();			
//		}
		
		$datosLiquidacion = unserialize($datosLiquidacionJsonEncode);
		
		$solicitud['Solicitud']['estado'] = $this->codigoAprobacion;
		$solicitud['Solicitud']['nro_credito_proveedor'] = $datosLiquidacion['nro_credito_proveedor'];
		$solicitud['Solicitud']['fecha_operacion_pago'] = $datosLiquidacion['fecha_operacion_pago'];
		$solicitud['Solicitud']['codigo_fpago'] = $datosLiquidacion['forma_pago'];
		$solicitud['Solicitud']['codigo_banco'] = ($datosLiquidacion['forma_pago'] != '0001' ? $datosLiquidacion['banco_pago'] : null);
		$solicitud['Solicitud']['id_sucursal'] = $datosLiquidacion['sucursal_pago'];
		$solicitud['Solicitud']['nro_operacion_pago'] = $datosLiquidacion['nro_operacion_pago'];
		$solicitud['Solicitud']['periodo_ini'] = $datosLiquidacion['periodo_ini'];
		$solicitud['Solicitud']['vto_primer_cuota_socio'] = $datosLiquidacion['vto_primer_cuota_socio'];
		$solicitud['Solicitud']['vto_primer_cuota_proveedor'] = $datosLiquidacion['vto_primer_cuota_proveedor'];
		
		$solicitud['Solicitud']['total_credito_proveedor'] = $datosLiquidacion['total_credito'];
		$solicitud['Solicitud']['capital_total_proveedor'] = $datosLiquidacion['capital_total'];
		$solicitud['Solicitud']['interes_total_proveedor'] = $datosLiquidacion['interes_total'];
		$solicitud['Solicitud']['redondeo_total_proveedor'] = $datosLiquidacion['redondeo_total'];
		
		
		App::import('Model', 'V1.SolicitudEstadoHist');
		$oEstado = new SolicitudEstadoHist(null);
		
		//armo el mensaje
		$obs = "CREDITO: #" . $solicitud['Solicitud']['nro_credito_proveedor']." \n";
		$obs .= "INICIA: " . parent::periodo($solicitud['Solicitud']['periodo_ini'])." \n";
		$obs .= "1er VTO.: " . date('d-m-Y', strtotime($solicitud['Solicitud']['vto_primer_cuota_socio']))." \n";
//		$obs .= "VTO. PRIMER CUOTA PROVEEDOR: " . date('d-m-Y', strtotime($solicitud['Solicitud']['vto_primer_cuota_proveedor']))." \n";
		
		if(!$oEstado->grabarHistorial($solicitud['Solicitud']['nro_solicitud'],$this->codigoAprobacion,$obs,$this->proveedor['razon_social_resumida'])){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"ERROR: NO SE PUDO GENERAR EL EVENTO EN EL HISTORIAL DE LA SOLICITUD.");
			return $this->getResponse();			
		}
		if (!$oSOLICITUD->save($solicitud)){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"ERROR: NO SE PUDO ACTUALIZAR EL ESTADO DE LA SOLICITUD.");
			return $this->getResponse();			
		}
		$usuariosTo = $oEstado->getUsuariosNotifica();
		$ERROR_NOTIFICA = FALSE;
		if(!empty($usuariosTo)):
			App::import('Model', 'V1.SolicitudEvento');
			$oEVENTO = new SolicitudEvento(null);
			$evento = array();
			foreach($usuariosTo as $user):
				$evento['SolicitudEvento']['id'] = 0;
				$evento['SolicitudEvento']['fecha'] = date("Y-m-d H:i:s");
				$evento['SolicitudEvento']['usuario_from'] = $this->proveedor['razon_social_resumida'];
				$evento['SolicitudEvento']['usuario_to'] = $user;
				$evento['SolicitudEvento']['estado_solicitud'] = $this->codigoAprobacion;
				$evento['SolicitudEvento']['nro_solicitud'] = $solicitud['Solicitud']['nro_solicitud'];
				$evento['SolicitudEvento']['comentario'] = $obs;
				if(!$oEVENTO->save($evento)){
					$ERROR_NOTIFICA = TRUE;
					break;
				}
			endforeach;
			if($ERROR_NOTIFICA){
				$this->setResponse('error',1);
				$this->setResponse('msg_error',"ERROR: NO SE PUDO NOTIFICAR A LOS SUPERVISORES PERO LA SOLICITUD FUE MARCADA COMO APROBADA CORRECTAMENTE");
				return $this->getResponse();					
			}
		
		endif;
		$this->setResponse('find',0);
		$this->setResponse('result',"SOLICITUD #$nroSolicitud --> APROBADA CORRECTAMENTE");
		return $this->getResponse();		
	}
	
	
	/**
	 * Carga las ordenes de un socio
	 * @param string $socio_id
	 * @param string $PIN
	 * @return string
	 */
	private function getOrdenes($socio_id,$PIN){
		$params = func_get_args();
		if(empty($params[0]) || empty($params[1])){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"FALTAN PARAMETROS PARA EL METODO REMOTO");
			return $this->getResponse();
		}
		if(!$this->validatePIN($params[1])){
			$this->setResponse('result',$params[1]);
			return $this->getResponse();
		}
		//CARGO LAS ORDENES DE DESCUENTO
		$oODTO = new OrdenDescuento();
		$ordenes = $oODTO->getOrdenesBySocioByProveedor($params[0],$this->proveedor['id'],true);
		$this->setResponse('result',$ordenes);
		return $this->getResponse();
	}
	
	/**
	 * Devuelve todos los pagos de una cuota
	 * @param string $cuota_id
	 * @param string $PIN
	 * @return string
	 */
	private function getCobrosByIdentificadorCuota($cuota_id,$PIN){
			$params = func_get_args();
		if(empty($params[0]) || empty($params[1])){
			$this->setResponse('error',1);
			$this->setResponse('msg_error',"FALTAN PARAMETROS PARA EL METODO REMOTO");
			return $this->getResponse();
		}
		if(!$this->validatePIN($params[1])){
			$this->setResponse('result',$params[1]);
			return $this->getResponse();
		}
		$oCOBROCUOTA = new OrdenDescuentoCobroCuota();
		$cobros = $oCOBROCUOTA->getCobrosByCuota($cuota_id);
		$this->setResponse('result',$cobros);
		return $this->getResponse();		
	}
	
	/**
	 * Devuelve TODAS las solicitudes para un PIN
	 * @param String $PIN
         * @param Integer $offset Description
         * @param Integer $limit Description
	 * @return string
	 */
	public function getSolicitudes($PIN,$offset,$limit){
		if(!$this->validatePIN($PIN)) return $this->getResponse();
		$oSOLICITUD = new Solicitud();
                $limit = (empty($limit) ? 50 : ($limit > 50 ? 50 : $limit));
                $offset = (empty($offset) ? 0 : ($offset > $limit ? $limit : $offset));
		$solicitudes = $oSOLICITUD->getSolicitudesByCuitProveedor($this->proveedor,null,$offset,$limit);
		$this->setResponse('find',count($solicitudes));
		$pendientes = array();
		if(!empty($solicitudes)):
		foreach($solicitudes as $solicitud):
		array_push($pendientes,$this->armaDatoSolicitud($solicitud));
		endforeach;
		endif;
		$this->setResponse('result',$pendientes);
		return $this->getResponse();
	}
	
	/**
	 * Devuelve solicitudes en base a un codigo de estado
	 * @param string $PIN
	 * @param integer $codEstado
	* @param Integer $offset Description
	* @param Integer $limit Description
	 * @return string
	 */
	public function getSolicitudesByEstado($PIN,$codEstado,$offset,$limit){
		if(!$this->validatePIN($PIN)) return $this->getResponse();
		$oSOLICITUD = new Solicitud();
		$limit = (empty($limit) ? 50 : ($limit > 50 ? 50 : $limit));
		$offset = (empty($offset) ? 0 : ($offset > $limit ? $limit : $offset));                
		$solicitudes = $oSOLICITUD->getSolicitudesByCuitProveedor($this->proveedor,$codEstado,$offset,$limit);
		$this->setResponse('find',count($solicitudes));
		$pendientes = array();
		if(!empty($solicitudes)):
		foreach($solicitudes as $solicitud):
		array_push($pendientes,$this->armaDatoSolicitud($solicitud));
		endforeach;
		endif;
		$this->setResponse('result',$pendientes);
		return $this->getResponse();
	}	
	

    /**
     * Cargar Solicitudes entre fechas
     * @param string $PIN
     * @param string $fechaDesde
     * @param string $fechaHasta
	 * @param Integer $offset Description
	 * @param Integer $limit Description 
     * @return string
     */
    function getSolicitudesEntreFechas($PIN,$fechaDesde,$fechaHasta,$offset,$limit){
        if (!$this->validatePIN($PIN)) {
            return $this->getResponse();
		}
		$limit = (empty($limit) ? 50 : ($limit > 50 ? 50 : $limit));
		$offset = (empty($offset) ? 0 : ($offset > $limit ? $limit : $offset));  
		$oSOLICITUD = new Solicitud();              
		$sols = $oSOLICITUD->getSolicitudesByCuitProveedor($this->proveedor,NULL,$offset,$limit,$fechaDesde,$fechaHasta);
		$this->setResponse('find',count($sols));
		$solicitudes = array();
		if(!empty($sols)):
			foreach($sols as $solicitud):
				array_push($solicitudes,$this->armaDatoSolicitud($solicitud));
			endforeach;
		endif;
		$this->setResponse('result',$solicitudes);		
        return $this->getResponse();
	} 
	

	

	
	/**
	 * Setea los datos para enviar
	 * @param string $solicitud
	 * @return string
	 * 
	 */
	private function armaDatoSolicitud($solicitud){
		$datos = array();
		$datos['nro_solicitud'] = $solicitud['Solicitud']['nro_solicitud'];
		$datos['fecha_solicitud'] = $solicitud['Solicitud']['fecha_solicitud'];
		$datos['estado'] = $solicitud['Solicitud']['estado'];
		$datos['estado_descripcion'] = strtoupper($solicitud['Solicitud']['estado_descripcion']);
		$datos['proveedor_producto'] = $solicitud['Solicitud']['proveedor_producto'];
		$datos['capital'] = $solicitud['Solicitud']['solicitado'];
		$datos['solicitado'] = $solicitud['Solicitud']['en_mano'];
		$datos['cuotas'] = $solicitud['Solicitud']['cuotas'];
		$datos['monto_cuota'] = $solicitud['Solicitud']['monto_cuota'];
		$datos['forma_pago'] = $solicitud['Solicitud']['forma_pago'];
		$datos['codigo_forma_pago'] = $solicitud['Solicitud']['codigo_fpago'];
		$datos['codigo_banco_pago'] = $solicitud['Solicitud']['codigo_banco'];
		$datos['nombre_banco_pago'] = $solicitud['Solicitud']['banco'];
		$datos['dato_giro'] = $solicitud['Solicitud']['dato_giro'];
		$datos['fecha_operacion_pago'] = $solicitud['Solicitud']['fecha_operacion_pago'];
		$datos['fecha_cupon_anses'] = $solicitud['Solicitud']['fecha_cupon_anses'];
		$datos['tipo_documento'] = substr($solicitud['PersonaV1']['tipo_documento'],-4);
		$datos['documento'] = $solicitud['PersonaV1']['documento'];
		$datos['sexo'] = $solicitud['PersonaV1']['sexo'];
		$datos['apellido'] = $solicitud['PersonaV1']['apellido'];
		$datos['nombre'] = $solicitud['PersonaV1']['nombre'];
		$datos['fecha_nacimiento'] = $solicitud['PersonaV1']['fecha_nacimiento'];
		$datos['cuit'] = $solicitud['PersonaV1']['cuit_cuil'];
		$datos['calle'] = $solicitud['PersonaV1']['calle'];
		$datos['nro_calle'] = $solicitud['PersonaV1']['nro_calle'];
		$datos['piso'] = $solicitud['PersonaV1']['piso'];
		$datos['dpto'] = $solicitud['PersonaV1']['dpto'];
		$datos['barrio'] = $solicitud['PersonaV1']['barrio'];
		$datos['codigo_postal'] = $solicitud['PersonaV1']['codigo_postal'];
		$datos['localidad'] = $solicitud['PersonaV1']['localidad'];
		$datos['codigo_provincia'] = $solicitud['PersonaV1']['codigo_provincia'];
		$datos['provincia'] = parent::getProvinciaByLetra($solicitud['PersonaV1']['codigo_provincia'],'nombre');
		$datos['conyugue'] = $solicitud['PersonaV1']['nombre_conyuge'];
		$datos['telefono_fijo'] = $solicitud['PersonaV1']['telefono_fijo'];
		$datos['telefono_movil'] = $solicitud['PersonaV1']['telefono_movil'];
		$datos['telefono_referencia'] = $solicitud['PersonaV1']['telefono_referencia'];
		$datos['persona_referencia'] = $solicitud['PersonaV1']['persona_referencia'];
		$datos['email'] = $solicitud['PersonaV1']['email'];
		$datos['codigo_beneficio'] = substr($solicitud['Beneficio']['codigo_beneficio'],-4);
		$datos['medio_de_pago'] = parent::GlobalDato("concepto_1",$solicitud['Beneficio']['codigo_beneficio']);
		$datos['nro_beneficio'] = $solicitud['Beneficio']['nro_beneficio'];
		$datos['cbu'] = $solicitud['Beneficio']['cbu'];
		$datos['codigo_banco'] = $solicitud['Beneficio']['codigo_banco'];
		$datos['sucursal'] = $solicitud['Beneficio']['sucursal'];
		$datos['nro_cta_bco'] = $solicitud['Beneficio']['nro_cta_bco'];
		$datos['productor_documento'] = $solicitud['Solicitud']['productor_documento'];
		$datos['productor_nombre'] = $solicitud['Solicitud']['productor_nombre'];
		$datos['productor_filial'] = $solicitud['Solicitud']['productor_filial'];
		$datos['productor_tipo'] = $solicitud['Solicitud']['productor_tipo'];
		
		foreach($datos as $key => $value){
			$datos[$key] = utf8_encode($value);
		}
		
		
		return $datos;
	}	
	
	
	/**
	 * Metodo de prueba
	 * @param string $param
	 * @param string $PIN
	 * @return string
	 */
	function testing($param,$PIN){
		$response = $param;
		if(!$this->validatePIN($PIN)) return $this->getResponse();
		//SIGO CON EL METODO
		$resultado = array("$param",2,3,4,5,6 => $param);
		$this->setResponse('find',count($resultado));
		$this->setResponse('result',$resultado);
		return $this->getResponse();
	}
	

	
    /**
     * 
     * @param type $pin
     * @param type $aprobada
     * @return array
     */
    private function cargarOrdenes($pin,$aprobada = 0,$fechaDesde = NULL,$fechaHasta = NULL,$byFechaPago = FALSE){
        $ordenes = array();
        $oSOLICITUD = new Solicitud();
        $query = "  (select solicitudes.id from mutual_producto_solicitudes as solicitudes
                    inner join proveedores on (proveedores.id = solicitudes.proveedor_id)
                    where proveedores.codigo_acceso_ws = '$pin' and ifnull(solicitudes.reasignar_proveedor_id,0) = 0
                    and solicitudes.anulada = 0
                    and solicitudes.aprobada = $aprobada and solicitudes.estado = ".($aprobada == 0 ? "'MUTUESTA0002'" : "'MUTUESTA0014'")."
                    ".(!empty($fechaDesde) ? "and solicitudes.".(!$byFechaPago ? "fecha" : "fecha_pago")." >= '$fechaDesde' " : " " )."
                    ".(!empty($fechaHasta) ? "and solicitudes.".(!$byFechaPago ? "fecha" : "fecha_pago")." <= '$fechaHasta' " : " " )."    
                    )
                    union
                    (select solicitudes.id from mutual_producto_solicitudes as solicitudes
                    inner join proveedores on (proveedores.id = solicitudes.reasignar_proveedor_id)
                    where proveedores.codigo_acceso_ws = '$pin' and ifnull(solicitudes.reasignar_proveedor_id,0) <> 0
                    and solicitudes.anulada = 0 and solicitudes.aprobada = $aprobada 
                    ".(!empty($fechaDesde) ? "and solicitudes.".(!$byFechaPago ? "fecha" : "fecha_pago")." >= '$fechaDesde' " : " " )."    
                    ".(!empty($fechaHasta) ? "and solicitudes.".(!$byFechaPago ? "fecha" : "fecha_pago")." <= '$fechaHasta' " : " " )."    
                    and solicitudes.estado = ".($aprobada == 0 ? "'MUTUESTA0002'" : "'MUTUESTA0014'")." order by solicitudes.fecha);";
        // $datos = $oSOLICITUD->query($query);
        // if(!empty($datos)){
        //     foreach($datos as $row){
        //         $solicitud = $oSOLICITUD->getOrden($row[0]['id']);
        //         $solicitud = $oSOLICITUD->armaDatos($solicitud,false,true);
        //         $solicitud = $this->armaDatoSolicitud($solicitud);
        //         array_push($ordenes, $solicitud);
        //     }
        // }
        // $LOG = date('Y-m-d H:i:s')."|".$pin."|".$query;
        // parent::writeLog($LOG);
        return $ordenes;
    }	

	

	
	
	
}

?>
<?php 

class MutualServicioSolicitud extends MutualAppModel{
	
	var $name = "MutualServicioSolicitud";
	var $hasMany = array('MutualServicioSolicitudAdicional' => array('dependent' => true));
	
	
	
	function borrar($id){
		return $this->del($id,true);
	}
	
	
	/**
	 * Carga una solicitud
	 * @param $id
	 */
	function getSolicitud($id){
		$solicitud = $this->read(null,$id);
		if(empty($solicitud)) return null;
		else return $this->armaDatos($solicitud);
	}
	
	

	/**
	 * Carga solicitudes para un id de persona
	 * 
	 * @author adrian [30/05/2012]
	 * @param unknown_type $persona_id
	 * @param unknown_type $order
	 * @param unknown_type $servicioID
	 */
	function getSolicitudesByPersonaID($persona_id,$order="MutualServicioSolicitud.created DESC",$servicioID = null){
		$conditions = array();
		$conditions['MutualServicioSolicitud.persona_id'] = $persona_id;
		if(!empty($servicioID)) $conditions['MutualServicioSolicitud.mutual_servicio_id'] = $servicioID;
		$solicitudes = $this->find('all',array('conditions' => $conditions,'order' => $order));
		if(empty($solicitudes)) return null;
		foreach($solicitudes as $idx => $solicitud):
			$solicitudes[$idx] = $this->armaDatos($solicitud);
		endforeach;
		return $solicitudes;
	}
	
	/**
	 * Carga solicitudes para un id de socio
	 * 
	 * @author adrian [30/05/2012]
	 * @param unknown_type $socio_id
	 * @param unknown_type $order
	 * @param unknown_type $servicioID
	 */
	function getSolicitudesBySocioID($socio_id,$order="MutualServicioSolicitud.created DESC",$servicioID = null,$listadoPadronXLS = false){
		$conditions = array();
		$conditions['MutualServicioSolicitud.socio_id'] = $socio_id;
		if(!empty($servicioID)) $conditions['MutualServicioSolicitud.mutual_servicio_id'] = $servicioID;
		$solicitudes = $this->find('all',array('conditions' => $conditions,'order' => $order));
		if(empty($solicitudes)) return null;
		foreach($solicitudes as $idx => $solicitud):
			$solicitudes[$idx] = $this->armaDatos($solicitud,true,null,$listadoPadronXLS);
		endforeach;
		return $solicitudes;
	}	
	
	
	function getSolicitudesVigentesByServicioID($servicio_id,$fechaCoberturaDesde = null){
		
		$conditions = array();
		$conditions['MutualServicioSolicitud.mutual_servicio_id'] = $servicio_id;
		
		$fechaCoberturaDesde = (empty($fechaCoberturaDesde) ? date('Y-m-d') : $fechaCoberturaDesde);
		$conditions['MutualServicioSolicitud.fecha_alta_servicio >='] = $fechaCoberturaDesde;
//		$conditions['MutualServicioSolicitud.fecha_baja_servicio <='] = $fechaCoberturaDesde;

		$solicitudes = $this->find('all',array('conditions' => $conditions,'order' => 'MutualServicioSolicitud.created DESC'));
		
		if(empty($solicitudes)) return null;
		
		foreach($solicitudes as $idx => $solicitud):
		
			$solicitudes[$idx] = $this->armaDatos($solicitud);
			
		endforeach;
		
		debug($solicitudes);
		
	}
	
	
	/**
	 * Arma datos de la solicitud
	 * @param $solicitud
	 */
	function armaDatos($solicitud,$valoresVigentes = true,$periodoControl = null,$listadoPadronXLS = false){
		$solicitud['MutualServicioSolicitud']['estado_actual'] = ($solicitud['MutualServicioSolicitud']['aprobada'] == 1 ? 'VIGENTE' : 'EMITIDA');
		if(!empty($solicitud['MutualServicioSolicitud']['periodo_hasta'])) $solicitud['MutualServicioSolicitud']['estado_actual']  = 'BAJA';
		$solicitud['MutualServicioSolicitud']['estado_actual_min'] = ($solicitud['MutualServicioSolicitud']['aprobada'] == 1 ? 'V' : 'E');
		if(!empty($solicitud['MutualServicioSolicitud']['periodo_hasta'])) $solicitud['MutualServicioSolicitud']['estado_actual_min']  = 'B';
		App::import('Model','mutual.MutualServicio');
		$oSERV = new MutualServicio();
		
        if(!$listadoPadronXLS):
        
            $datosAdicionales = $oSERV->getDatosAdicionales($solicitud['MutualServicioSolicitud']['mutual_servicio_id']);

            $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio'] = $datosAdicionales['MutualServicio']['tipo_producto_descripcion_proveedor'];
            $solicitud['MutualServicioSolicitud']['mutual_servicio_proveedor'] = $datosAdicionales['MutualServicio']['proveedor_razon_social'];
            $solicitud['MutualServicioSolicitud']['mutual_servicio_producto'] = $datosAdicionales['MutualServicio']['tipo_producto_descripcion'];
            $solicitud['MutualServicioSolicitud']['mutual_servicio_codigo'] = $datosAdicionales['MutualServicio']['tipo_producto'];
            $solicitud['MutualServicioSolicitud']['proveedor_id'] = $datosAdicionales['MutualServicio']['proveedor_id'];
            $solicitud['MutualServicioSolicitud']['tipo_producto'] = $datosAdicionales['MutualServicio']['tipo_producto'];
            $solicitud['MutualServicioSolicitud']['tipo_orden_dto'] = $datosAdicionales['MutualServicio']['tipo_orden_dto'];
		
        
		
    // 		$solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio'] = $oSERV->getNombreProveedorServicio($solicitud['MutualServicioSolicitud']['mutual_servicio_id']);
    // 		$solicitud['MutualServicioSolicitud']['mutual_servicio_proveedor'] = $oSERV->getNombreProveedor($solicitud['MutualServicioSolicitud']['mutual_servicio_id']);
    // 		$solicitud['MutualServicioSolicitud']['mutual_servicio_producto'] = $oSERV->getNombreServicio($solicitud['MutualServicioSolicitud']['mutual_servicio_id']);
    // 		$solicitud['MutualServicioSolicitud']['mutual_servicio_codigo'] = $oSERV->getCodigoServicio($solicitud['MutualServicioSolicitud']['mutual_servicio_id']);
    // 		$solicitud['MutualServicioSolicitud']['proveedor_id'] = $oSERV->getProveedorID($solicitud['MutualServicioSolicitud']['mutual_servicio_id']);
    // 		$solicitud['MutualServicioSolicitud']['tipo_producto'] = $oSERV->getCodigoServicio($solicitud['MutualServicioSolicitud']['mutual_servicio_id']);
    // 		$solicitud['MutualServicioSolicitud']['tipo_orden_dto'] = $oSERV->getTipoOrdenDtoServicio($solicitud['MutualServicioSolicitud']['mutual_servicio_id']);

            


            $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio_ref'] = $solicitud['MutualServicioSolicitud']['mutual_proveedor_servicio'] . (!empty($solicitud['MutualServicioSolicitud']['nro_referencia_proveedor']) ? " (REF: " . $solicitud['MutualServicioSolicitud']['nro_referencia_proveedor'].")" : "");
		endif;
        
        $solicitud['MutualServicioSolicitud']['tipo_numero'] = $solicitud['MutualServicioSolicitud']['tipo_orden_dto'] . " #".$solicitud['MutualServicioSolicitud']['id'];
        
        if(!$listadoPadronXLS):
            App::import('Model','pfyj.PersonaBeneficio');
            $oBEN = new PersonaBeneficio();

            $beneficio = $oBEN->getBeneficio($solicitud['MutualServicioSolicitud']['persona_beneficio_id']);

    //		$solicitud['MutualServicioSolicitud']['beneficio'] = $oBEN->getStrBeneficio($solicitud['MutualServicioSolicitud']['persona_beneficio_id']);
    //		$solicitud['MutualServicioSolicitud']['beneficio_organismo'] = $oBEN->getCodigoOrganismo($solicitud['MutualServicioSolicitud']['persona_beneficio_id']);
    //		$solicitud['MutualServicioSolicitud']['beneficio_organismo_desc'] = $oBEN->getOrganismo($solicitud['MutualServicioSolicitud']['persona_beneficio_id']);

            $solicitud['MutualServicioSolicitud']['beneficio'] = $oBEN->getStrBeneficio($solicitud['MutualServicioSolicitud']['persona_beneficio_id'],true,$beneficio);
            $solicitud['MutualServicioSolicitud']['beneficio_organismo'] = $beneficio['PersonaBeneficio']['codigo_beneficio'];
            $solicitud['MutualServicioSolicitud']['beneficio_organismo_desc'] = $beneficio['PersonaBeneficio']['codigo_beneficio_desc'];
        
        endif;
        
        App::import('Model','pfyj.Persona');
		$oPER = new Persona();
        
        $persona = $oPER->getPersona($solicitud['MutualServicioSolicitud']['persona_id']);
        
		
//		$tdocNdoc = $oPER->getTdocNdoc($solicitud['MutualServicioSolicitud']['persona_id']," ",true);
		
//		$solicitud['MutualServicioSolicitud']['titular_tdoc'] = $tdocNdoc[0];
//		$solicitud['MutualServicioSolicitud']['titular_ndoc'] = $tdocNdoc[1];
//		$solicitud['MutualServicioSolicitud']['titular_tdocndoc'] = $tdocNdoc[0] . " " . $tdocNdoc[1];
//		$solicitud['MutualServicioSolicitud']['titular_apenom'] = $oPER->getApenom($solicitud['MutualServicioSolicitud']['persona_id'],false);
//		$solicitud['MutualServicioSolicitud']['titular_sexo'] = $oPER->getSexo($solicitud['MutualServicioSolicitud']['persona_id']);
//		$solicitud['MutualServicioSolicitud']['titular_edad'] = $oPER->getEdadByPersonaId($solicitud['MutualServicioSolicitud']['persona_id']);
//		$solicitud['MutualServicioSolicitud']['titular_tdocndoc_apenom'] = $oPER->getApenom($solicitud['MutualServicioSolicitud']['persona_id'],true);
//		$solicitud['MutualServicioSolicitud']['titular_domicilio'] = $oPER->getDomicilioByPersonaId($solicitud['MutualServicioSolicitud']['persona_id']);
//		$solicitud['MutualServicioSolicitud']['titular_fecha_nacimiento'] = $oPER->getFechaNac($solicitud['MutualServicioSolicitud']['persona_id']);
		
        
		$solicitud['MutualServicioSolicitud']['titular_tdoc'] = $persona['Persona']['tipo_documento_desc'];
		$solicitud['MutualServicioSolicitud']['titular_ndoc'] = $persona['Persona']['documento'];
		$solicitud['MutualServicioSolicitud']['titular_tdocndoc'] = $persona['Persona']['tipo_documento_desc'] . " " . $persona['Persona']['documento'];
		$solicitud['MutualServicioSolicitud']['titular_apenom'] = $persona['Persona']['apenom'];
		$solicitud['MutualServicioSolicitud']['titular_sexo'] = $persona['Persona']['sexo'];
		$solicitud['MutualServicioSolicitud']['titular_edad'] = $persona['Persona']['edad'];
		$solicitud['MutualServicioSolicitud']['titular_tdocndoc_apenom'] = $persona['Persona']['tipo_documento_desc']. ' ' . $persona['Persona']['documento'] .' - '.$persona['Persona']['apellido'].', '.$persona['Persona']['nombre'];
		$solicitud['MutualServicioSolicitud']['titular_domicilio'] = $oPER->getDomicilio($persona);
		$solicitud['MutualServicioSolicitud']['titular_fecha_nacimiento'] = $persona['Persona']['fecha_nacimiento'];
        
        
        
//		$domiArray = $oPER->getDomicilioByPersonaId($solicitud['MutualServicioSolicitud']['persona_id'],true);
        $domiArray = $oPER->getDomicilioByPersonaId(null,true,$persona);
		
		$solicitud['MutualServicioSolicitud']['titular_calle'] = $domiArray['calle'];
		$solicitud['MutualServicioSolicitud']['titular_numero_calle'] = $domiArray['numero_calle'];
		$solicitud['MutualServicioSolicitud']['titular_piso'] = $domiArray['piso'];
		$solicitud['MutualServicioSolicitud']['titular_dpto'] = $domiArray['dpto'];
		$solicitud['MutualServicioSolicitud']['titular_barrio'] = $domiArray['barrio'];
		$solicitud['MutualServicioSolicitud']['titular_localidad'] = $domiArray['localidad'];
		$solicitud['MutualServicioSolicitud']['titular_cp'] = $domiArray['cp'];
		$solicitud['MutualServicioSolicitud']['titular_provincia'] = $domiArray['provincia'];
		
		if(!$listadoPadronXLS) $solicitud = $this->setBarcode($solicitud);
		
		
		
		if($valoresVigentes):
			#VALORES DE CALCULO ACTUALES
			App::import('Model','Mutual.MutualServicioValor');
			$oSERV_VALOR = new MutualServicioValor();			
		
			
			$periodoCalculo = (isset($solicitud['MutualServicioSolicitud']['periodo_calculo']) ? $solicitud['MutualServicioSolicitud']['periodo_calculo'] : date("Ym"));
			$servicioID = $solicitud['MutualServicioSolicitud']['mutual_servicio_id'];
			$codOrg = $solicitud['MutualServicioSolicitud']['beneficio_organismo'];
			$valorCuotaServicio = $oSERV_VALOR->getValoresVigentes($servicioID,$periodoCalculo,$codOrg);
			$solicitud['MutualServicioSolicitud']['importe_mensual_actual_titular'] = $valorCuotaServicio['importe_titular'];
			$solicitud['MutualServicioSolicitud']['importe_mensual_actual_adicional'] = $valorCuotaServicio['importe_adicional'];
			$solicitud['MutualServicioSolicitud']['importe_mensual_actual_periodo_vigencia'] = $valorCuotaServicio['periodo_vigencia'];
			
			$solicitud['MutualServicioSolicitud']['importe_mensual_actual_total'] = $oSERV_VALOR->calcularImporteMensual($solicitud['MutualServicioSolicitud']['id'],$periodoCalculo,$codOrg,false);
			
			$solicitud['MutualServicioSolicitud']['costo_mensual_actual_titular'] = $valorCuotaServicio['costo_titular'];
			$solicitud['MutualServicioSolicitud']['costo_mensual_actual_adicional'] = $valorCuotaServicio['costo_adicional'];
		endif;
		
		
		
		if(!empty($solicitud['MutualServicioSolicitudAdicional'])):
		
			App::import('Model','pfyj.SocioAdicional');
			$oADICIONAL = new SocioAdicional();		
		
			foreach($solicitud['MutualServicioSolicitudAdicional'] as $idx => $servAdic):
			
				$adicional = $oADICIONAL->getAdicional($servAdic['socio_adicional_id']);

				$servAdic['adicional_tdoc'] = $adicional['SocioAdicional']['tipo_documento_desc'];
				$servAdic['adicional_ndoc'] = $adicional['SocioAdicional']['documento'];
				
				$servAdic['adicional_tdocndoc'] = $adicional['SocioAdicional']['tdoc_ndoc'];
				$servAdic['adicional_apenom'] = $adicional['SocioAdicional']['apenom'];
				$servAdic['adicional_sexo'] = $adicional['SocioAdicional']['sexo'];
				$servAdic['adicional_tdoc_ndoc_apenom'] = $adicional['SocioAdicional']['tdoc_ndoc_apenom'];
				$servAdic['adicional_vinculo'] = $adicional['SocioAdicional']['vinculo_desc'];
				$servAdic['adicional_domicilio'] = $adicional['SocioAdicional']['domicilio'];
				
				$servAdic['adicional_calle'] = $adicional['SocioAdicional']['calle'];
				$servAdic['adicional_numero_calle'] = $adicional['SocioAdicional']['numero_calle'];
				$servAdic['adicional_piso'] = $adicional['SocioAdicional']['piso'];
				$servAdic['adicional_dpto'] = $adicional['SocioAdicional']['dpto'];
				$servAdic['adicional_barrio'] = $adicional['SocioAdicional']['barrio'];
				$servAdic['adicional_localidad'] = $adicional['SocioAdicional']['localidad'];
				$servAdic['adicional_cp'] = $adicional['SocioAdicional']['codigo_postal'];
				$servAdic['adicional_provincia'] = $adicional['SocioAdicional']['provincia'];
				$servAdic['adicional_edad'] = $adicional['SocioAdicional']['edad'];
				
				$solicitud['MutualServicioSolicitudAdicional'][$idx] = $servAdic;
				
//				debug($servAdic);
//				debug($adicional);
			
			endforeach;
			
			$solicitud['MutualServicioSolicitudAdicional'] = Set::sort($solicitud['MutualServicioSolicitudAdicional'], '{n}.adicional_apenom', 'asc');
		
		endif;
		
		//saco la cantidad de cuotas sociales adeudadas
		App::import('Model','pfyj.Socio');
		$oSOCIO = new Socio();	

		$ordenCuotaSocial = $oSOCIO->getOrdenDtoCuotaSocial($solicitud['MutualServicioSolicitud']['socio_id']);
		$solicitud['MutualServicioSolicitud']['cuotas_sociales_adeudadas'] = 0;
		if(!empty($ordenCuotaSocial)){
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCUOTA = new OrdenDescuentoCuota();	
			$cuotasSociales = $oCUOTA->cuotasAdeudadasTotalmenteByOrdenDto($ordenCuotaSocial['OrdenDescuento']['id']);
			$solicitud['MutualServicioSolicitud']['cuotas_sociales_adeudadas'] = count($cuotasSociales);
		}
		return $solicitud;
		
	}
	
	
	function calculaFechaCobertura($fechaEmision,$isBaja = false){
		$sumaAntesCorte = 0;
		$sumaDespuesCorte = 0;
		$diaCorte = 25;
		$diaInicio = (!$isBaja ? 1 : parent::ultimoDiaMes(date('m',strtotime($fechaEmision)),date('Y',strtotime($fechaEmision))));
		$mkTimeEmite = mktime(0,0,0,date('m',strtotime($fechaEmision)),$diaInicio,date('Y',strtotime($fechaEmision)));
		if(date('d',strtotime($fechaEmision)) <= $diaCorte) $mk = parent::addMonthToDate($mkTimeEmite,$sumaAntesCorte);
		else $mk = parent::addMonthToDate($mkTimeEmite,$sumaDespuesCorte);
		return date('Y-m-d',$mk);
	}
	
	
	/**
	 * Genera una nueva solicitud
	 * @param $datos
	 */
	function generarNuevaSolicitud($datos){
		
//		debug($datos);
//		exit;
		
        if(empty($datos['MutualServicioSolicitud']['mutual_servicio_id'])) return false;
        
//		$datos['MutualServicioSolicitud']['fecha_alta_servicio'] = parent::armaFecha($datos['MutualServicioSolicitud']['fecha_alta_servicio']);
//		$datos['MutualServicioSolicitud']['fecha_emision'] = date('Y-m-d');
		$datos['MutualServicioSolicitud']['periodo_desde'] = date('Ym',  strtotime($datos['MutualServicioSolicitud']['fecha_alta_servicio']));
		
		#CALCULAR LOS VALORES ACTUALES AL PERIODO DESDE
		App::import('Model','Mutual.MutualServicioValor');
		$oSERV_VALOR = new MutualServicioValor();		

		App::import('Model','pfyj.PersonaBeneficio');
		$oBEN = new PersonaBeneficio();
		
		$periodoCalculo = $datos['MutualServicioSolicitud']['periodo_desde'];
		$servicioID = $datos['MutualServicioSolicitud']['mutual_servicio_id'];
		$codOrg = $oBEN->getCodigoOrganismo($datos['MutualServicioSolicitud']['persona_beneficio_id']);
		$valorCuotaServicio = $oSERV_VALOR->getValoresVigentes($servicioID,$periodoCalculo,$codOrg);
		$datos['MutualServicioSolicitud']['mutual_servicio_valor_id'] = $valorCuotaServicio['id'];
		$datos['MutualServicioSolicitud']['importe_mensual'] = $valorCuotaServicio['importe_titular'];
		
		$IMPORTE_MENSUAL_TOTAL = $datos['MutualServicioSolicitud']['importe_mensual'];
		
		if(isset($datos['MutualServicioSolicitud']['socio_adicional_id']) && !empty($datos['MutualServicioSolicitud']['socio_adicional_id'])):
			$datos['MutualServicioSolicitudAdicional'] = array();
			foreach($datos['MutualServicioSolicitud']['socio_adicional_id'] as $adicional_id => $selected):
				$adicional = array();
				$adicional['socio_adicional_id'] = $adicional_id;
				$adicional['fecha_emision_alta'] = $datos['MutualServicioSolicitud']['fecha_emision'];
				$adicional['fecha_alta'] = $datos['MutualServicioSolicitud']['fecha_alta_servicio'];
				$adicional['periodo_desde'] = $datos['MutualServicioSolicitud']['periodo_desde'];
				$adicional['mutual_servicio_valor_id'] = $valorCuotaServicio['id'];
				$adicional['importe_mensual'] = $valorCuotaServicio['importe_adicional'];
				$IMPORTE_MENSUAL_TOTAL += $adicional['importe_mensual'];
				array_push($datos['MutualServicioSolicitudAdicional'],$adicional);
			endforeach;
		endif;
		$datos['MutualServicioSolicitud']['importe_mensual_total'] = $IMPORTE_MENSUAL_TOTAL;
        
//		debug($datos);
//		exit;
		return $this->saveAll($datos);
	}
	
	
	function anexarAdicionales($solicitudID,$periodoDesde,$adicionales,$fechaAlta=null,$personaBeneficioId=null){
		if(empty($adicionales)) return false;
		$fechaAlta = (!empty($fechaAlta) ? $fechaAlta : date('Y-m-d'));
		App::import('Model','mutual.MutualServicioSolicitudAdicional');
		$oSERV_ADIC = new MutualServicioSolicitudAdicional();

		parent::begin();

		$solicitud = $this->getSolicitud($solicitudID);
		
		App::import('Model','Mutual.MutualServicioValor');
		$oSERV_VALOR = new MutualServicioValor();
		
		App::import('Model','Mutual.OrdenDescuento');
		$oODTO = new OrdenDescuento();		
		
		#CONTROLO QUE TENGA UNA ORDEN DE DESCUENTO GENERADA
		if($solicitud['MutualServicioSolicitud']['orden_descuento_id'] == 0){
			
			if(empty($solicitud['MutualServicioSolicitud']['periodo_desde'])) $solicitud['MutualServicioSolicitud']['periodo_desde'] = $periodoDesde;
			if($solicitud['MutualServicioSolicitud']['periodo_desde'] < $periodoDesde) $solicitud['MutualServicioSolicitud']['periodo_desde'] = $periodoDesde;
			
			//LE ASIGNO EL BENEFICIO ID
			if($solicitud['MutualServicioSolicitud']['persona_beneficio_id'] == 0){
				$solicitud['MutualServicioSolicitud']['periodo_desde'] = $periodoDesde;
				$solicitud['MutualServicioSolicitud']['persona_beneficio_id'] = $personaBeneficioId;
				$this->save($solicitud);
				$solicitud = $this->getSolicitud($solicitudID);
			}
			
			$servicio_id = $solicitud['MutualServicioSolicitud']['mutual_servicio_id'];
			$periodo = $solicitud['MutualServicioSolicitud']['periodo_desde'];
			$organismo = $solicitud['MutualServicioSolicitud']['beneficio_organismo'];
			
			$valores = $oSERV_VALOR->getValoresVigentes($servicio_id,$periodo,$organismo);
			
			App::import('Model', 'Proveedores.ProveedorVencimiento');
			$oVTOS = new ProveedorVencimiento(null);
			
			$vtos = $oVTOS->calculaVencimiento($solicitud['MutualServicioSolicitud']['proveedor_id'],$solicitud['MutualServicioSolicitud']['persona_beneficio_id'],$solicitud['MutualServicioSolicitud']['fecha_alta_servicio']);
			
			$importe = $valores['importe_titular'] + ($valores['importe_adicional'] * count($adicionales));
			
			//GENERAR LA ORDEN DE DESCUENTO
			$OrdenDto['OrdenDescuento'] = array(
							'fecha' => $solicitud['MutualServicioSolicitud']['fecha_alta_servicio'],
							'tipo_orden_dto' => $solicitud['MutualServicioSolicitud']['tipo_orden_dto'],
							'numero' => $solicitud['MutualServicioSolicitud']['id'],
							'tipo_producto' => $solicitud['MutualServicioSolicitud']['mutual_servicio_codigo'],
							'socio_id' => $solicitud['MutualServicioSolicitud']['socio_id'],
							'persona_beneficio_id' => $solicitud['MutualServicioSolicitud']['persona_beneficio_id'],
							'proveedor_id' => $solicitud['MutualServicioSolicitud']['proveedor_id'],
							'mutual_producto_id' => 0,
							'periodo_ini' => $solicitud['MutualServicioSolicitud']['periodo_desde'],
							'importe_cuota' => $importe,
							'importe_total' => $importe,
							'primer_vto_socio' => $vtos['vto_primer_cuota_socio'],
							'primer_vto_proveedor' => $vtos['vto_primer_cuota_proveedor'],
							'cuotas' => 1,
							'permanente' => 1,
							'nro_referencia_proveedor' => $solicitud['MutualServicioSolicitud']['nro_referencia_proveedor'],
						);

			$oODTO->save($OrdenDto);
			$solicitud['MutualServicioSolicitud']['importe_mensual'] = $valores['importe_titular'];
			$solicitud['MutualServicioSolicitud']['importe_mensual_total'] = $valores['importe_titular'] + ($valores['importe_adicional'] * count($adicionales));
			$solicitud['MutualServicioSolicitud']['orden_descuento_id'] = $oODTO->getLastInsertID();
			$this->save($solicitud);
			
		}else{
			
			
			$OrdenDto = $oODTO->read(null,$solicitud['MutualServicioSolicitud']['orden_descuento_id']);
			

			if(empty($solicitud['MutualServicioSolicitud']['periodo_desde'])){
				$solicitud['MutualServicioSolicitud']['periodo_desde'] = $periodoDesde;
				$OrdenDto['OrdenDescuento']['periodo_ini'] = $solicitud['MutualServicioSolicitud']['periodo_desde'];
			}
			if($solicitud['MutualServicioSolicitud']['periodo_desde'] < $periodoDesde){
				$solicitud['MutualServicioSolicitud']['periodo_desde'] = $periodoDesde;
				$OrdenDto['OrdenDescuento']['periodo_ini'] = $solicitud['MutualServicioSolicitud']['periodo_desde'];
			}
			
			$servicio_id = $solicitud['MutualServicioSolicitud']['mutual_servicio_id'];
			$periodo = $solicitud['MutualServicioSolicitud']['periodo_desde'];
			$organismo = $solicitud['MutualServicioSolicitud']['beneficio_organismo'];
			
			$valores = $oSERV_VALOR->getValoresVigentes($servicio_id,$periodo,$organismo);
			
			$OrdenDto['OrdenDescuento']['importe_cuota'] = $valores['importe_titular'] + ($valores['importe_adicional'] * count($adicionales));
			$OrdenDto['OrdenDescuento']['importe_total'] = $valores['importe_titular'] + ($valores['importe_adicional'] * count($adicionales));
			
			$oODTO->save($OrdenDto);
			$solicitud['MutualServicioSolicitud']['importe_mensual'] = $valores['importe_titular'];
			$solicitud['MutualServicioSolicitud']['importe_mensual_total'] = $valores['importe_titular'] + ($valores['importe_adicional'] * count($adicionales));
			$this->save($solicitud);
			
		}
		
		
		
		foreach($adicionales as $socio_adicional_id):
			$datos['MutualServicioSolicitudAdicional'] = array();
			$datos['MutualServicioSolicitudAdicional']['id'] = 0;
			$datos['MutualServicioSolicitudAdicional']['mutual_servicio_solicitud_id'] = $solicitudID;
			$datos['MutualServicioSolicitudAdicional']['socio_adicional_id'] = $socio_adicional_id;
			$datos['MutualServicioSolicitudAdicional']['fecha_alta'] = $fechaAlta;
			$datos['MutualServicioSolicitudAdicional']['periodo_desde'] = $periodoDesde;
			$datos['MutualServicioSolicitudAdicional']['fecha_emision_alta'] = date("Y-m-d");
			
			$datos['MutualServicioSolicitudAdicional']['importe_mensual'] = $valores['importe_adicional'];
			$datos['MutualServicioSolicitudAdicional']['mutual_servicio_valor_id'] = $valores['id'];
			
			if(!$oSERV_ADIC->save($datos)){
				return false;
			}
		endforeach;
		
		
		$solicitud = $this->getSolicitud($solicitudID);

		//ACTUALIZO LOS VALORES A VALORES VIGENTES
		$oSERV_VALOR->calcularImporteMensual($solicitud['MutualServicioSolicitud']['id'],$solicitud['MutualServicioSolicitud']['periodo_desde'],$solicitud['MutualServicioSolicitud']['beneficio_organismo']);
		parent::commit();
		return true;
	}
	
	
	function bajaSolicitud($solicitudID,$fechaBajaServicio,$periodoHasta,$observaciones=null){
		$solicitud = $this->read(null,$solicitudID);
		if(empty($solicitud)) return false;	
		$periodoHasta = ($periodoHasta < $solicitud['MutualServicioSolicitud']['periodo_desde'] ? $solicitud['MutualServicioSolicitud']['periodo_desde'] : $periodoHasta);
		
		$solicitud['MutualServicioSolicitud']['fecha_baja_servicio'] = $fechaBajaServicio;
		$solicitud['MutualServicioSolicitud']['periodo_hasta'] = $periodoHasta;
		$solicitud['MutualServicioSolicitud']['observaciones'] = $observaciones;
		$solicitud['MutualServicioSolicitud']['fecha_emision_baja'] = date("Y-m-d");
		if(!empty($solicitud['MutualServicioSolicitudAdicional'])){
			foreach($solicitud['MutualServicioSolicitudAdicional'] as $idx => $adicional){
				if(empty($adicional['periodo_hasta']) || $adicional['periodo_hasta'] >= $periodoHasta){
					$adicional['fecha_baja'] = $fechaBajaServicio;
					$adicional['periodo_hasta'] = $periodoHasta;
					$adicional['fecha_emision_baja'] = date("Y-m-d");
				}
				$solicitud['MutualServicioSolicitudAdicional'][$idx] = $adicional;
			}
			if(!$this->saveAll($solicitud)) return false;
		}else if(!$this->save($solicitud)){
			return false;
		}
				
		
		
		
		if($solicitud['MutualServicioSolicitud']['orden_descuento_id'] != 0):
			App::import('Model','Mutual.OrdenDescuento');
			$oODTO = new OrdenDescuento();
			if(!$oODTO->suspenderOrdenPermanente($solicitud['MutualServicioSolicitud']['orden_descuento_id'],$periodoHasta,null)) return false;
		endif;
		
		return true;
		
	}
	
	
	function getNoAprobadas(){
		$noAprobadas = array();
		$solicitudes = $this->find('all',array('conditions' => array('MutualServicioSolicitud.aprobada' => 0),'order' => 'MutualServicioSolicitud.fecha_emision DESC'));
		if(empty($solicitudes)) return null;
		foreach($solicitudes as $idx => $solicitud):
			if(empty($solicitud['MutualServicioSolicitud']['periodo_hasta'])){
				array_push($noAprobadas,$this->armaDatos($solicitud));
			}
		endforeach;
		return $noAprobadas;
	}
	
	
	function aprobar($id){
		$solicitud = $this->getSolicitud($id);
		if(empty($solicitud)) return false;	

		if($solicitud['MutualServicioSolicitud']['aprobada'] == 0):
		
		
			App::import('Model','Mutual.OrdenDescuento');
			$oODTO = new OrdenDescuento();		

			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCuota = new OrdenDescuentoCuota();			
		
			//VERIFICAR EL IMPORTE DE LA CUOTA SOCIAL
			App::import('Model','pfyj.Socio');
			$oSOCIO = new Socio();			
			$ordenDtoCuotaSocial = $oSOCIO->getOrdenDtoCuotaSocial($solicitud['MutualServicioSolicitud']['socio_id']);
			
			$impoCuotaSocial = $oSOCIO->getImpoCuotaSocial($solicitud['MutualServicioSolicitud']['socio_id']);
			
			if($ordenDtoCuotaSocial['OrdenDescuento']['importe_cuota'] != $impoCuotaSocial){
				$ordenDtoCuotaSocial['OrdenDescuento']['importe_cuota'] = $impoCuotaSocial;
				$ordenDtoCuotaSocial['OrdenDescuento']['importe_total'] = $impoCuotaSocial;
				$oODTO->save($ordenDtoCuotaSocial);
			}

			App::import('Model', 'Proveedores.ProveedorVencimiento');
			$oVTOS = new ProveedorVencimiento(null);
			
			$vtos = $oVTOS->calculaVencimiento($solicitud['MutualServicioSolicitud']['proveedor_id'],$solicitud['MutualServicioSolicitud']['persona_beneficio_id'],$solicitud['MutualServicioSolicitud']['fecha_alta_servicio']);
			
//			debug($vtos);
			
			#SACO EL IMPORTE ACTUAL DEL SERVICIO EN BASE AL PERIODO DE INICIO DE LA PRIMER CUOTA
			App::import('Model','Mutual.MutualServicioValor');
			$oSERV_VALOR = new MutualServicioValor();			
		
			
			$periodoDesde = $solicitud['MutualServicioSolicitud']['periodo_desde'];
			$importeMensualActual = $oSERV_VALOR->calcularImporteMensual($solicitud['MutualServicioSolicitud']['id'],$solicitud['MutualServicioSolicitud']['periodo_desde'],$solicitud['MutualServicioSolicitud']['beneficio_organismo']);
			
			
			if($importeMensualActual == 0 && empty($solicitud['MutualServicioSolicitud']['cuotas'])){
				
				$solicitud['MutualServicioSolicitud']['orden_descuento_id'] = 0;
		    	$solicitud['MutualServicioSolicitud']['aprobada'] = 1;
		    	$solicitud['MutualServicioSolicitud']['fecha_aprobacion'] = date("Y-m-d");
		    	$solicitud['MutualServicioSolicitud']['aprobada_por'] = (isset($_SESSION['NAME_USER_LOGON_SIGEM']) ? $_SESSION['NAME_USER_LOGON_SIGEM'] : 'APLICACION_SERVER');;
		    	
				return parent::save($solicitud);
			}
			
			$OrdenDto['OrdenDescuento'] = array(
							'fecha' => $solicitud['MutualServicioSolicitud']['fecha_alta_servicio'],
							'tipo_orden_dto' => $solicitud['MutualServicioSolicitud']['tipo_orden_dto'],
							'numero' => $solicitud['MutualServicioSolicitud']['id'],
							'tipo_producto' => $solicitud['MutualServicioSolicitud']['mutual_servicio_codigo'],
							'socio_id' => $solicitud['MutualServicioSolicitud']['socio_id'],
							'persona_beneficio_id' => $solicitud['MutualServicioSolicitud']['persona_beneficio_id'],
							'proveedor_id' => $solicitud['MutualServicioSolicitud']['proveedor_id'],
							'mutual_producto_id' => 0,
							'periodo_ini' => $solicitud['MutualServicioSolicitud']['periodo_desde'],
							'importe_cuota' => (!empty($solicitud['MutualServicioSolicitud']['cuotas']) ? $solicitud['MutualServicioSolicitud']['importe_cuota'] : $importeMensualActual),
							'importe_total' => (!empty($solicitud['MutualServicioSolicitud']['cuotas']) ? $solicitud['MutualServicioSolicitud']['importe_mensual_total'] : $importeMensualActual),
							'primer_vto_socio' => $vtos['vto_primer_cuota_socio'],
							'primer_vto_proveedor' => $vtos['vto_primer_cuota_proveedor'],
							'cuotas' => (!empty($solicitud['MutualServicioSolicitud']['cuotas']) ? $solicitud['MutualServicioSolicitud']['cuotas'] : 1),
							'permanente' => (!empty($solicitud['MutualServicioSolicitud']['cuotas']) ? 0 : 1),
							'nro_referencia_proveedor' => $solicitud['MutualServicioSolicitud']['nro_referencia_proveedor'],
						);

			
			$OrdenDto['OrdenDescuentoCuota'] = $oCuota->armaCuotas($OrdenDto);
			
			if(!$oODTO->saveAll($OrdenDto)) return false;
			
			$solicitud['MutualServicioSolicitud']['orden_descuento_id'] = $oODTO->getLastInsertID();
	    	$solicitud['MutualServicioSolicitud']['aprobada'] = 1;
	    	$solicitud['MutualServicioSolicitud']['fecha_aprobacion'] = date("Y-m-d");
	    	$solicitud['MutualServicioSolicitud']['aprobada_por'] = (isset($_SESSION['NAME_USER_LOGON_SIGEM']) ? $_SESSION['NAME_USER_LOGON_SIGEM'] : 'APLICACION_SERVER');;
	    	
			return parent::save($solicitud);

		endif;
		
		return false;
		
	}
	
	
    function setBarcode($solicitud){
    	$barCode = parent::fill($solicitud['MutualServicioSolicitud']['id'],7,'0','L');
    	$barCode .= parent::fill($solicitud['MutualServicioSolicitud']['socio_id'],7,'0','L');
    	$barCode .= substr($solicitud['MutualServicioSolicitud']['tipo_producto'],8,4);
    	$barCode .= parent::fill($solicitud['MutualServicioSolicitud']['nro_referencia_proveedor'],10,'0','L');
    	$barCode .= parent::fill(0,1,'0','L');
    	$barCode .= (!empty($solicitud['MutualServicioSolicitud']['fecha_alta_servicio']) ? date('Ymd',strtotime($solicitud['MutualServicioSolicitud']['fecha_alta_servicio'])) : "00000000");
    	$barCode .= (!empty($solicitud['MutualServicioSolicitud']['fecha_baja_servicio']) ? date('Ymd',strtotime($solicitud['MutualServicioSolicitud']['fecha_baja_servicio'])) : "00000000");
    	$barCode .= parent::digitoVerificador($barCode);
    	$solicitud['MutualServicioSolicitud']['barcode'] = $barCode;
    	return $solicitud;
    }	
	
  
    function ifExists($servicio_id,$persona_id){
    	$conditions = array();
    	$conditions['MutualServicioSolicitud.mutual_servicio_id'] = $servicio_id;
    	$conditions['MutualServicioSolicitud.persona_id'] = $persona_id;
    	$conditions['MutualServicioSolicitud.fecha_baja_servicio'] = null;
    	$datos = $this->find('all',array('conditions' => $conditions));
    	return (count($datos) != 0 ? true : false);
    }
    
}

?>
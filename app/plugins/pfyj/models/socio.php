<?php

class Socio extends PfyjAppModel{

	var $name = 'Socio';

	var $belongsTo = array(
            'Persona' => array(
                'className' => 'Persona',
                'foreignKey' => 'persona_id'
            )
        );

	var $hasMany = 'SocioCalificacion';

//        var $hasMany = array(
//                            'SocioCalificacion' =>
//                            array(
//                            'className' => 'SocioCalificacion',
//                            'foreignKey' => 'socio_id',
//                            'order' => 'SocioCalificacion.periodo DESC',
////                          'limit' => '12',
//                            ),
////                            'SocioInforme' =>
////                            array(
////                            'className' => 'SocioInforme',
////                            'foreignKey' => 'socio_id',
////                            'order' => 'SocioInforme.created DESC',
////                            ),
//        );


	function guardar($data){
		if(!isset($data['Socio']['persona_beneficio_id']) || $data['Socio']['persona_beneficio_id'] == 0) return false;

//    	App::import('Model', 'Pfyj.PersonaBeneficio');
//		$oBENEFICIO = new PersonaBeneficio();
//		$organismo = $oBENEFICIO->getCodigoOrganismo($data['Socio']['persona_beneficio_id']);
//		$data['Socio']['categoria'] = parent::GlobalDato('concepto_3',$organismo);

		$data = $this->categorizar($data);

		$ret = parent::save($data);

		if(isset($data['Socio']['id'])){
			$this->id = $data['Socio']['id'];
			if(isset($data['Socio']['idr']))$this->saveField('idr',$data['Socio']['idr']);
			$this->__historial($data['Socio']['id']);
		}
		return $ret;
	}


	function isActivo($id){
		$data = $this->read('activo',$id);
		if($data['Socio']['activo'] == 1) return true;
		else return false;
	}


	function reactivar($id,$periodoInicio,$primerVencimiento = null){

		$this->id = $id;
		//grabo en el historial
		$this->__historial($id);

		$socio = $this->read(null,$id);

    	App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();
		$organismo = $oBENEFICIO->getCodigoOrganismo($socio['Socio']['persona_beneficio_id']);
//		$categoria = parent::GlobalDato('concepto_3',$organismo);
//		$socio['Socio']['categoria'] = $categoria;

		$socio = $this->categorizar($socio);

		if(empty($primerVencimiento)):
			$proveedor_id = parent::GlobalDato('entero_1',"MUTUPROD0003");
			App::import('Model', 'Proveedores.ProveedorVencimiento');
			$oVTOS = new ProveedorVencimiento(null);
			$vtos = $oVTOS->calculaVencimientoByPeriodo($proveedor_id,$organismo,$periodoInicio,date('Y-m-d'));
			$primerVencimiento = (!empty($vtos) ? $vtos['vto_cuota_socio'] : date('Y-m-d'));
		endif;
		//genero una solicitud de reafiliacion
    	App::import('Model', 'Pfyj.SocioSolicitud');
		$oSolSoc = new SocioSolicitud();

		$afiliacion = array('SocioSolicitud' => array(
			'tipo_solicitud' => 'R',
			'aprobada' => 1,
			'persona_id' => $socio['Socio']['persona_id'],
			'persona_beneficio_id' => $socio['Socio']['persona_beneficio_id'],
			'fecha' => date('Y-m-d'),
			'periodo_ini' => $periodoInicio,
			'primer_vto_socio' => $primerVencimiento,
			'primer_vto_proveedor' => $primerVencimiento,
		));

		if(!$oSolSoc->save($afiliacion)) return false;
		$idSolicitudAfiliacion = $oSolSoc->getLastInsertID();



		//marco como activo
		$socio['Socio']['activo'] = 1;
		$socio['Socio']['fecha_alta'] = date('Y-m-d');
		$socio['Socio']['socio_solicitud_id'] = $idSolicitudAfiliacion;
		$socio['Socio']['codigo_baja'] = null;
		$socio['Socio']['fecha_baja'] = null;
		$socio['Socio']['periodo_hasta'] = null;

		if(!parent::save($socio)) return false;

		//reactivo la orden de descuento de la cuota social
		##################################################################
		#ACTIVAR LA ORDEN DE DESCUENTO DE LA CUOTA SOCIAL
		##################################################################

        $orden = $this->getOrdenDtoCuotaSocial($id);
//        debug($orden);
//        exit;

    	App::import('Model', 'Mutual.OrdenDescuento');
    	$oOrdenDescuento = new OrdenDescuento(null);
//    	$orden = $oOrdenDescuento->read(null,$socio['Socio']['orden_descuento_id']);

        App::import('Model','Mutual.OrdenDescuentoCuota');
        $oCuota = new OrdenDescuentoCuota();


    	if(!empty($orden)):

    		$orden['OrdenDescuento']['activo'] = 1;
    		$orden['OrdenDescuento']['periodo_hasta'] = null;
    		$oOrdenDescuento->save($orden);

                $oOrdenDescuento->bindModel(array('belongsTo' => array('PersonaBeneficio')));
                $orden = $oOrdenDescuento->read(null,$orden['OrdenDescuento']['id']);

                $oCuota->generaCuotaSocial($orden,$periodoInicio,FALSE);

    	else:

            $ordenExistente = $this->getOrdenDtoCuotaSocial($id);

	    	$ordenDto = array();
	    	$ordenDto['OrdenDescuento'] = array(
                    'id' => 0,
	    			'fecha' => $afiliacion['SocioSolicitud']['fecha'],
	    			'tipo_orden_dto' => parent::GlobalDato('concepto_3',"MUTUPROD0003"),
	    			'numero' => $id,
	    			'tipo_producto' => 'MUTUPROD0003',
	    			'socio_id' => $id,
	    			'persona_beneficio_id' => $afiliacion['SocioSolicitud']['persona_beneficio_id'],
	    			'proveedor_id' => parent::GlobalDato('entero_1',"MUTUPROD0003"),
	    			'mutual_producto_id' => 0,
	    			'periodo_ini' => $afiliacion['SocioSolicitud']['periodo_ini'],
                                'periodo_hasta' => NULL,
	    			'primer_vto_socio' => $afiliacion['SocioSolicitud']['primer_vto_socio'],
	    			'primer_vto_proveedor' => $afiliacion['SocioSolicitud']['primer_vto_proveedor'],
	    			'importe_cuota' => $this->getImpoCuotaSocial($id),
	    			'cuotas' => 1,
	    			'permanente' => 1,
	    			'activo' => 1,
                                'periodo_hasta' => null,
	    	);

            if(!empty($ordenExistente)){
                $ordenDto['OrdenDescuento']['id'] = $ordenExistente['OrdenDescuento']['id'];
            }

	    	$oOrdenDescuento->save($ordenDto);
	    	$this->actualizarOrdenDto($idSolicitudAfiliacion,$oOrdenDescuento->getLastInsertID());
	    	$this->actualizarOrdenDto($id,$oOrdenDescuento->getLastInsertID());

                $oOrdenDescuento->bindModel(array('belongsTo' => array('PersonaBeneficio')));

                $orden = $oOrdenDescuento->read(null,$ordenDto['OrdenDescuento']['id']);

                $oCuota->generaCuotaSocial($orden,$afiliacion['SocioSolicitud']['periodo_ini'],FALSE);

	    	App::import('Model', 'Mutual.LiquidacionSocio');
	    	$oLS = new LiquidacionSocio(null);
	    	$oLS->reliquidar($id,$afiliacion['SocioSolicitud']['periodo_ini']);

//                exit;


    	endif;

		// actualizo la cuota social
		if(!$this->actualizarCuotaSocial($id,$socio['Socio']['persona_beneficio_id'])) return false;


//		if(!$this->saveField('activo',1)) return false;
//		if(!$this->saveField('fecha_alta',date('Y-m-d'))) return false;
//		if(!$this->saveField('socio_solicitud_id',$idSolicitudAfiliacion)) return false;

		return true;

	}

    function actualizarOrdenDto($id,$idexp){
    	$data = $this->read(null,$id);
    	$data['Socio']['orden_descuento_id'] = $idexp;
    	return parent::save($data);
    }


    function getImpoCuotaSocial($id){
    	$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
    	$socio = $this->read(null,$id);
    	$codigo = 'MUTUCUOS' . substr($socio['PersonaBeneficio']['codigo_beneficio'],8,4);
    	$glb = $this->getGlobalDato('decimal_1',$codigo);
    	return (empty($glb['GlobalDato']['decimal_1']) ? 0 : $glb['GlobalDato']['decimal_1']);
    }

    function getImporteCuotaSocialEspecial($id){
    	$socio = $this->read(null,$id);
    	return (empty($socio['Socio']['importe_cuota_social']) ? 0 : $socio['Socio']['importe_cuota_social']);
    }


    /**
     * Actualiza el importe de la cuota social en la orden de descuento al valor vigente (en tabla global - MUTUCUOS)
     * se pasa el id del socio, el importe de la cuota social y el beneficio nuevo.  Si el beneficio nuevo es
     * el mismo que asignado a la orden de descuento se actualiza el monto de la cuota social. Para el caso que el
     * parametro $reasignaBeneficio sea true no se comparan los codigos de organismos y directamente se actualiza la cuota
     * social al valor vigente de la tabla global.
     * @param $id
     * @param $nuevo_beneficio_id
     * @param $reasignaBeneficio
     * @return unknown_type
     */
    function actualizarCuotaSocial($id,$nuevo_beneficio_id,$importe=null,$reasignaBeneficio=false){
    	$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
    	$socio = $this->read(null,$id);
    	$codigoOrganismoSocio = substr($socio['PersonaBeneficio']['codigo_beneficio'],8,4);

    	App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBen = new PersonaBeneficio(null);

		App::import('Model', 'Mutual.OrdenDescuento');
		$oOdto = new OrdenDescuento(null);

		$nuevoBeneficio = $oBen->read(null,$nuevo_beneficio_id);
		$codigoNuevo = substr($nuevoBeneficio['PersonaBeneficio']['codigo_beneficio'],8,4);

		#RECATEGORIZO AL SOCIO
//		$categoria = parent::GlobalDato('concepto_3',$nuevoBeneficio['PersonaBeneficio']['codigo_beneficio']);
//		$socio['Socio']['categoria'] = $categoria;

		$socio = $this->categorizar($socio);

		parent::save($socio);


		if($oBen->isMismoOrganismo($nuevo_beneficio_id,$codigoOrganismoSocio)){

			if(empty($importe))$cuotaSocialValorActual = $this->getImpoCuotaSocial($id);
			else $cuotaSocialValorActual = $importe;

			if(MODULO_V1) $this->__actualizarCuotaSocialV1($socio['Socio']['idr'],$cuotaSocialValorActual);


		}else if($reasignaBeneficio){

			$nuevoBeneficio = $oBen->read(null,$nuevo_beneficio_id);
			$codigoNuevo = substr($nuevoBeneficio['PersonaBeneficio']['codigo_beneficio'],8,4);
			if(empty($importe)){
				$glb = $this->getGlobalDato('decimal_1','MUTUCUOS'.$codigoNuevo);
				$cuotaSocialValorActual = $glb['GlobalDato']['decimal_1'];
			}else{
				$cuotaSocialValorActual = $importe;
			}

			if(MODULO_V1) $this->__actualizarCuotaSocialV1($socio['Socio']['idr'],$cuotaSocialValorActual,$nuevoBeneficio['PersonaBeneficio']['idr']);


		}else{

				$glb = $this->getGlobalDato('decimal_1','MUTUCUOS'.$codigoOrganismoSocio);
				$cuotaSocialValorActual = $glb['GlobalDato']['decimal_1'];

		}

		$oOdto->actualizarValor($socio['Socio']['orden_descuento_id'],'importe_total',$cuotaSocialValorActual);
		$oOdto->actualizarValor($socio['Socio']['orden_descuento_id'],'importe_cuota',$cuotaSocialValorActual);

		return true;
    }


    function __actualizarCuotaSocialV1($id,$importe,$nuevoBeneficioId=null){
    	App::import('Model', 'V1.SocioV1');
    	$oSocV1 = new SocioV1();
    	$socioV1 = $oSocV1->read(null,$id);
    	$socioV1['SocioV1']['cuota_social'] = $importe;
    	if(!empty($nuevoBeneficioId)) $socioV1['SocioV1']['id_beneficio'] = $nuevoBeneficioId;
    	return $oSocV1->save($socioV1);
    }


    function __historial($socio_id){
    	App::import('Model','Pfyj.SocioHistorico');
    	$oHist = new SocioHistorico();
    	return $oHist->grabarHistorial($socio_id);
    }

    /**
     * Alta de un socio dado de baja
     * @param $socio_id
     * @param $persona_beneficio_id
     * @param $periodo_ini
     * @param $vto_socio
     * @param $vto_proveedor
     * @return unknown_type
     */
    function alta($socio_id,$persona_beneficio_id,$periodo_ini,$vto_socio,$vto_proveedor=null){

    	$socio = $this->read(null,$socio_id);

    	##################################################################
    	#GENERAR UNA NUEVA SOLICITUD DE AFILIACION
    	##################################################################
    	App::import('Model', 'Pfyj.SocioSolicitud');
		$oSolSoc = new SocioSolicitud();

		$tipoAfiliacion = ($socio['Socio']['activo'] == 0 ? 'R' : 'A');

		$afiliacion = array('SocioSolicitud' => array(
			'tipo_solicitud' => $tipoAfiliacion,
			'aprobada' => 1,
			'persona_id' => $socio['Socio']['persona_id'],
			'persona_beneficio_id' => $persona_beneficio_id,
			'fecha' => date('Y-m-d'),
			'periodo_ini' => $periodo_ini,
			'primer_vto_socio' => $vto_socio,
			'primer_vto_proveedor' => $vto_socio,
		));
		if(!$oSolSoc->save($afiliacion)) return false;
		$id = $oSolSoc->getLastInsertID();

		##################################################################
    	#ACTIVAR EL SOCIO
    	##################################################################
		$this->id = $socio_id;
		$this->__historial($socio_id);
		//marco como activo
		$socio['Socio']['activo'] = 1;
		$socio['Socio']['fecha_alta'] = date('Y-m-d');
		$socio['Socio']['codigo_baja'] = null;
		$socio['Socio']['fecha_baja'] = null;
		$socio['Socio']['periodo_hasta'] = null;

		##################################################################
		#CATEGORIZAR AL SOCIO EN BASE AL BENEFICIO POR EL CUAL SE DESCUENTA LA CUOTA SOCIAL
		##################################################################
//    	App::import('Model', 'Pfyj.PersonaBeneficio');
//		$oBENEFICIO = new PersonaBeneficio();
//		$organismo = $oBENEFICIO->getCodigoOrganismo($persona_beneficio_id);
//		$categoria = parent::GlobalDato('concepto_3',$organismo);
//		$socio['Socio']['categoria'] = $categoria;

		$socio = $this->categorizar($socio);

		if(!parent::save($socio)) return false;



		##################################################################
        #ACTUALIZAR EN LA V1
        ##################################################################
        if(MODULO_V1){
            App::import('Model', 'V1.PersonaV1');
            $oPersonaV1 = new PersonaV1();
            if(!empty($socio['Persona'])):
                $pv1 = $oPersonaV1->getByTdocNdoc($socio['Persona']['tipo_documento'],$socio['Persona']['documento']);
            else:
                $persona = $this->getPersonaBySocioID($socio['Socio']['id']);
                $pv1 = $oPersonaV1->getByTdocNdoc($persona['Persona']['tipo_documento'],$persona['Persona']['documento']);
            endif;

            App::import('Model', 'V1.SocioV1');
            $oSocV1 = new SocioV1();
            $socioV1 = $oSocV1->read(null,$pv1['PersonaV1']['id_persona']);

            if(!empty($socioV1)){
                $socioV1['SocioV1']['activo'] = 1;
                $socioV1['SocioV1']['fecha_alta'] = date('Y-m-d');
                $socioV1['SocioV1']['fecha_baja'] = null;
                $socioV1['SocioV1']['codigo_baja'] = null;
                $oSocV1->save($socioV1);
            }else{
                //no esta generado el registro de socio
                $datos = array();
                $datos['SocioV1']['id_socio'] = $pv1['PersonaV1']['id_persona'];
                $datos['SocioV1']['nro_socio'] = $socio_id;
                $datos['SocioV1']['activo'] = 1;
                $datos['SocioV1']['fecha_alta'] = date('Y-m-d');
            }
        }


    	#INFORMAR LA NOVEDAD DE LA NUEVA ALTA

		##################################################################
		#ACTIVAR LA ORDEN DE DESCUENTO DE LA CUOTA SOCIAL
		##################################################################
    	App::import('Model', 'Mutual.OrdenDescuento');
    	$oOrdenDescuento = new OrdenDescuento(null);
    	$orden = $oOrdenDescuento->read(null,$socio['Socio']['orden_descuento_id']);
    	if(!empty($orden)):
    		$orden['OrdenDescuento']['activo'] = 1;
    		$orden['OrdenDescuento']['periodo_hasta'] = null;
    		$oOrdenDescuento->save($orden);
    	endif;

    }


    function setNoActivo($socio_id,$codigoBaja=''){
    	$socio = $this->read(null,$socio_id);
    	$this->__historial($socio_id);
		$socio['Socio']['activo'] = 0;
		$socio['Socio']['codigo_baja'] = $codigoBaja;
		$socio['Socio']['fecha_baja'] = date('Y-m-d');

        if(MODULO_V1){
	    	App::import('Model', 'V1.SocioV1');
	    	$oSocV1 = new SocioV1();
	    	$oSocV1->id = $socio['Socio']['idr'];
	    	$oSocV1->saveField('activo',0);
	    	$oSocV1->saveField('fecha_baja',parent::armaFecha($socio['Socio']['fecha_baja']));
	    	$oSocV1->saveField('codigo_baja',substr($socio['Socio']['codigo_baja'],8,4));
    	}

    	return parent::save($socio);
    }

    /**
     * Baja
     * Procesa la baja del socio. El parametro que recibe son los datos del post del formulario de baja
     * 1) id del socio
     * 2) codigo de baja
     * 3) fecha de baja
     * 4) observaciones
     * 5) periodo de cuotas
     * @param array $data
     * @return boolean
     */
    function baja($data){

    	#########################################################
    	#GRABAR EN EL HISTORIAL LOS DATOS ACTUALES
    	#########################################################
    	$this->__historial($data['Socio']['id']);

    	$data['Socio']['fecha_baja'] = parent::armaFecha($data['Socio']['fecha_baja']);
        
        $personaFall = null;
        if($data['Socio']['codigo_baja'] === 'MUTUBASOBFAL'){
            $periodoBaja = date('Ym',strtotime($data['Socio']['fecha_baja']));
            $data['Socio']['baja_deuda'] = true;

            App::import('Model', 'pfyj.Persona');
            $oPERSONA = new Persona();
            $personaFall = $oPERSONA->read(null,$data['Socio']['persona_id']);
            if(!empty($personaFall['Persona']['fecha_fallecimiento'])
                && $personaFall['Persona']['fecha_fallecimiento'] != $data['Socio']['fecha_baja']){
                    $data['Socio']['fecha_baja'] = $personaFall['Persona']['fecha_fallecimiento'];
            }
        }else{
            $periodoBaja = $data['Socio']['periodo_hasta_liquida']['year'].$data['Socio']['periodo_hasta_liquida']['month'];
        }



    	#########################################################
    	#MARCAR COMO NO ACTIVO Y CAUSA BAJA EN EL SOCIO
    	#########################################################
    	$socio = $this->read(null,$data['Socio']['id']);
    	$socio['Socio']['activo'] = 0;
    	$socio['Socio']['codigo_baja'] = $data['Socio']['codigo_baja'];
    	$socio['Socio']['fecha_baja'] = $data['Socio']['fecha_baja'];
    	$socio['Socio']['observaciones'] = $data['Socio']['observaciones'];
    	$socio['Socio']['periodo_hasta'] = $periodoBaja;

    	if (!parent::save($socio)) {
            return false;
        }

    	#########################################################
    	#DAR DE BAJA ORDENES DE DESCUENTO Y CUOTAS (ANALIZAR DE ACUERDO AL CODIGO DEL CAMPO 2 DE LA GLOBAL PARA EL CODIGO DE BAJA
    	#########################################################
    	App::import('Model','Mutual.OrdenDescuento');
    	$oDto = new OrdenDescuento();
    	$oDto->desactivarBySocio($data['Socio']['id'],$periodoBaja);

    	#########################################################
    	#DAR DE BAJA LAS CUOTAS A PARTIR DEL PERIODO PASADO POR PARAMETRO
    	#########################################################
    	App::import('Model','Mutual.OrdenDescuentoCuota');
    	$oCuota = new OrdenDescuentoCuota();
    	if (!$oCuota->bajaBySocio($data['Socio']['id'], $data['Socio']['codigo_baja'], $periodoBaja, (isset($data['Socio']['baja_deuda']) ? true : false))) {
            return false;
        }


        #############################################################################################
        # MARCAR COMO FALLECIDA LA PERSONA Y DESACTIVAR EL VENDEDOR
        #############################################################################################
    	if($data['Socio']['codigo_baja'] === 'MUTUBASOBFAL' && !empty($personaFall)){
            $personaFall['Persona']['fallecida'] = true;
            $personaFall['Persona']['fecha_fallecimiento'] = $data['Socio']['fecha_baja'];
            $oPERSONA->save($$personaFall);

            #SI ES UN VENDEDOR DAR DE BAJA EL USUARIO
            App::import('Model', 'ventas.Vendedor');
            $oVENDEDOR = new Vendedor();
            $vendedor = $oVENDEDOR->getByPersonaId($personaFall['Persona']['id']);
            if(!empty($vendedor)){
                if (!$oVENDEDOR->suspender($vendedor[0]['Vendedor']['id'], 0)) {
                    return false;
                }
            }
        }


    	return true;
    }

    /**
     * calcula la deuda de un socio (vencido y a vencer)
     * @param $socio_id
     * @param $periodo_corte
     */
    function deuda($socio_id,$periodo_corte = NULL){
    	App::import('Model','Mutual.OrdenDescuentoCuota');
    	$oCuota = new OrdenDescuentoCuota();
        $deuda = array(
            'total' => 0,
            'fecha_calculo_deuda' => NULL,
            'cuotas' => array(),
            'cuota_id_saldo' => array(),

        );
        $periodo_corte = (empty($periodo_corte) ? date('Ym') : $periodo_corte);
        #proceso la deuda
        $cuotas = $oCuota->procesa_deuda($socio_id, '200001', $periodo_corte);
        if(empty($cuotas)) return $deuda;
//        debug($cuotas);
        $total = 0;

        $fechaMin = $cuotaTimeStamp = NULL;

        foreach($cuotas as $i => $cuota){
            if($cuota['saldo_conciliado'] > 0){

                $total += $cuota['saldo_conciliado'];
//                $deuda['cuota'][$i] = $cuota;
                array_push($deuda['cuotas'], $cuota);
                array_push($deuda['cuota_id_saldo'], array('id' => $cuota['id'],'saldo_conciliado' => $cuota['saldo_conciliado']));

                if(empty($cuota['vencimiento'])){
                    $vto = new DateTime();
                    $vto->setDate(substr($deuda['periodo'],0,4), substr($deuda['periodo'],-2), 1);
                    $cuota['vencimiento'] = $vto->format("Y-m-d");
                }

                if($i == 0){
                    $cuotaTime = new DateTime($cuota['vencimiento']);
                    $fechaMin = $cuotaTimeStamp = $cuotaTime->getTimestamp();
                }else{
                    if($fechaMin < $cuotaTimeStamp){
                        $fechaMin = $cuotaTimeStamp;
//                        debug($cuota['vencimiento']." *** FMin: ".date('Y-m-d',$fechaMin)." ** Cuo:".date('Y-m-d',$cuotaTimeStamp));
    //                    $cuotaTimeStamp = NULL;
                        $cuotaTime = new DateTime($cuota['vencimiento']);
                        $cuotaTimeStamp = $cuotaTime->getTimestamp();

//                    }else{
//                        $cuotaTime = new DateTime($cuota['vencimiento']);
//                        $cuotaTimeStamp = $cuotaTime->getTimestamp();
                    }
                }








            }
        }

//        debug(date('Y-m-d',$fechaMin));

        $deuda['fecha_calculo_deuda'] = date('Y-m-d');
//        $deuda['fecha_calculo_deuda'] = date('Y-m-d',$fechaMin);
        $deuda['total'] = $total;
//        debug($deuda);
//        exit;
        return $deuda;
////        return $total;
//        debug($deuda);
////        exit;
////
////    	return $oCuota->getTotalDeudaBySocio($socio_id);
    }

    /**
     * devuelve el organismo al cual esta vinculada la cuota social
     * @param $socio_id
     */
    function getOrganismoCuotaSocial($socio_id){
    	$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
    	$socio = $this->read(null,$socio_id);
    	return $socio['PersonaBeneficio']['codigo_beneficio'];
    }

    function isCuotaSocialPorCBU($socio_id){
    	$organismo = $this->getOrganismoCuotaSocial($socio_id);
    	$glb = parent::getGlobalDato('concepto_2',$organismo);
    	if(trim($glb['GlobalDato']['concepto_2']) == 'AC') return true;
    	else return false;
    }

    function getApenom($socio_id,$conDocumento=true){
        App::import('model','Pfyj.Persona');
        $oPersona = new Persona();
        $this->unbindModel(array('hasMany' => array('SocioCalificacion')));
        $socio = $this->read('persona_id',$socio_id);
//        debug($socio);
        if($socio['Socio']['persona_id'] != 0)return $oPersona->getApenom($socio['Socio']['persona_id'],$conDocumento);
        else return null;
    }


    function getPersonaBySocioID($socio_id){
		App::import('model','Pfyj.Persona');
		$oPersona = new Persona();
		$socio = $this->read('persona_id',$socio_id);
		return  $oPersona->read(null,$socio['Socio']['persona_id']);
    }

    function getSocioByDocumento($documento,$detachRelations = FALSE){
//        if($detachRelations){
            $sql = "select Socio.*,Persona.* from personas as Persona
                    inner join socios Socio on Socio.persona_id = Persona.id
                    where Persona.documento = '$documento';";
            $socio = $this->query($sql);
//        }else{
//            App::import('model','Pfyj.Persona');
//            $oPersona = new Persona();
//            $persona_id = $oPersona->getIdByDocumento($documento);
//            if(empty($persona_id)) return null;
//            $socio = $this->getSocioByPersonaId($persona_id,$detachRelations);
//        }
            return (!empty($socio) ? $socio[0] : null);
    }

    function getSocioByCuitCuil($cuitCuil,$detachRelations = FALSE){
        App::import('model','Pfyj.Persona');
        $oPersona = new Persona();
    	$persona = $oPersona->getByCUIT($cuitCuil,false);
    	if(empty($persona)) return null;
        $persona = $persona[0];
    	$socio = $this->getSocioByPersonaId($persona['Persona']['id'],$detachRelations);
    	return $socio;
    }


    function getTdocNdoc($socio_id,$sep='-',$toArray=false){
        App::import('model','Pfyj.Persona');
        $oPersona = new Persona();
        $socio = $this->read('persona_id',$socio_id);
        if($socio['Socio']['persona_id'] != 0)return $oPersona->getTdocNdoc($socio['Socio']['persona_id'],$sep,$toArray);
        else return null;
    }


    function getPersona($socio_id,$armaDatos = TRUE){
        App::import('model','Pfyj.Persona');
        $oPersona = new Persona();
        $socio = $this->read('persona_id',$socio_id);
        if($socio['Socio']['persona_id'] != 0)return $oPersona->getPersona($socio['Socio']['persona_id'],$armaDatos);
        else return null;
    }


    /**
     * devuelve el socio_id en base al id de la persona
     * para el caso que tenga mas de un socio (ERROR) toma el primero creado y devuelve ese registro
     * @param int $persona_id
     * @return array $socio;
     */
    function getSocioByPersonaId($persona_id,$detachRelations = FALSE){
        if($detachRelations){
            $this->unbindModel(
                    array(

                        'belongsTo' => array('Persona'),

                    )
            );
        }

        $this->unbindModel(
                array(
                    'hasMany' => array('SocioCalificacion')
                )
        );


    	$socio = $this->find('all',array(
    										'conditions' => array('Socio.persona_id' => $persona_id),
    										'order' => array('Socio.created ASC'),
    										'limit' => 1
    	));
    	if(!empty($socio) && isset($socio[0])) return $socio[0];
    	else return null;
    }


    function getOrdenDtoCuotaSocial($socio_id){
//    	App::import('Model', 'Mutual.OrdenDescuento');
//    	$oOD = new OrdenDescuento(null);
//    	$conditions = array();
//    	$conditions['OrdenDescuento.socio_id'] = $socio_id;
//    	$conditions['OrdenDescuento.tipo_orden_dto'] = 'CMUTU';
//    	$conditions['OrdenDescuento.numero'] = $socio_id;
//    	$conditions['OrdenDescuento.tipo_producto'] = 'MUTUPROD0003';
//    	$orden =  $oOD->find('all',array('conditions' => $conditions));
//    	if(!empty($orden)) return $orden[0];
//    	else return null;

     	App::import('Model', 'Mutual.OrdenDescuento');
    	$oORDEN = new OrdenDescuento();
        $sql = "select OrdenDescuento.* from orden_descuento_cuotas as OrdenDescuentoCuota
                inner join orden_descuentos as OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                where
                OrdenDescuentoCuota.socio_id = $socio_id
                and OrdenDescuento.activo = 1
                and OrdenDescuentoCuota.tipo_cuota = 'MUTUTCUOCSOC' order by id desc limit 1;";
        $orden = $oORDEN->query($sql);
        
        if(!empty($orden)) return $orden[0];
        else{
//            $sql = "select OrdenDescuento.* from orden_descuentos OrdenDescuento where OrdenDescuento.socio_id = $socio_id and tipo_orden_dto = 'CMUTU' and tipo_producto = 'MUTUPROD0003' and activo = 1;    ";
            $sql = "select * from orden_descuentos OrdenDescuento
                    where socio_id = $socio_id and tipo_orden_dto = 'CMUTU' and tipo_producto = 'MUTUPROD0003'
                    and nueva_orden_descuento_id is null;";
            $orden = $oORDEN->query($sql);
            if(!empty($orden)) return $orden[0];
            else{
                return null;
            }
        }
    }

    function getOrdenDtoCuotaSocialByPersonaID($persona_id){
//    	App::import('Model', 'Mutual.OrdenDescuento');
//    	$oOD = new OrdenDescuento(null);
//    	$conditions = array();
//    	$conditions['OrdenDescuento.socio_id'] = $socio_id;
//    	$conditions['OrdenDescuento.tipo_orden_dto'] = 'CMUTU';
//    	$conditions['OrdenDescuento.numero'] = $socio_id;
//    	$conditions['OrdenDescuento.tipo_producto'] = 'MUTUPROD0003';
//    	$orden =  $oOD->find('all',array('conditions' => $conditions));
//    	if(!empty($orden)) return $orden[0];
//    	else return null;

     	App::import('Model', 'Mutual.OrdenDescuento');
    	$oORDEN = new OrdenDescuento();
        $sql = "select OrdenDescuento.* from orden_descuento_cuotas as OrdenDescuentoCuota
                inner join orden_descuentos as OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                inner join socios as Socio on (Socio.id = OrdenDescuentoCuota.socio_id)
                where
                Socio.persona_id = $persona_id
                and OrdenDescuentoCuota.tipo_cuota = 'MUTUTCUOCSOC' order by id desc limit 1;";
        $orden = $oORDEN->query($sql);
        if(!empty($orden)) return $orden[0];
        else return null;
    }


    /**
     * Get Ultima Calificacion
     * @param $socio_id
     * @param $persona_beneficio_id
     * @param $toString
     * @param $incluyeFecha
     * @param $noEnviaDiskette
     * @return unknown_type
     */
    function getUltimaCalificacion($socio_id,$persona_beneficio_id=null,$toString=false,$incluyeFecha=false,$noEnviaDiskette=false,$periodo = NULL){

		App::import('Model', 'Pfyj.SocioCalificacion');
		$oCALIFICACION = new SocioCalificacion(null);
		$calificacion =  $oCALIFICACION->ultimaCalificacion($socio_id,$persona_beneficio_id,$toString,$incluyeFecha,$noEnviaDiskette,$periodo);
		return $calificacion;
    }


    function getResumenCalificaciones($socio_id,$toString=false){
		App::import('Model', 'Pfyj.SocioCalificacion');
		$oCALIFICACION = new SocioCalificacion(null);
		$calificaciones = $oCALIFICACION->getResumenCalificaciones($socio_id);
		$string = "";
		if($toString && !empty($calificaciones)):
			foreach($calificaciones as $calificacion):
				$string .= $calificacion['SocioCalificacion']['calificacion_desc'] ."(".$calificacion['SocioCalificacion']['cantidad'].")|";
			endforeach;
			return $string;
		else:
			return $calificaciones;
		endif;
    }


	function getDetalleDeuda($socio_id,$periodoCorte=null,$proveedor_id=null,$codigoOrganismo=null){

		if(empty($periodoCorte)) $periodoCorte = date('Ym');

		App::import('Model','Mutual.OrdenDescuentoCuota');
    	$oCuota = new OrdenDescuentoCuota();

    	$cuotas = $oCuota->cuotasAdeudadasBySocioByPeriodoCorte($socio_id,$periodoCorte,$proveedor_id);

    	if(!empty($cuotas) && !empty($codigoOrganismo)) $cuotas = Set::extract("/OrdenDescuentoCuota[codigo_organismo=$codigoOrganismo]",$cuotas);

		return $cuotas;

	}


	function isStopDebit($socio_id){
		App::import('Model', 'Pfyj.SocioCalificacion');
		$oCALIFICACION = new SocioCalificacion(null);
		return $oCALIFICACION->isStopDebit($socio_id);
	}



	function altaDirecta($persona_id,$persona_beneficio_id,$periodoIni,$fechaAlta = null){


		if(empty($fechaAlta)) $fechaAlta = date('Y-m-d');

                App::import('Model', 'Pfyj.SocioSolicitud');
		$oSolSoc = new SocioSolicitud();

		App::import('Model','Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();

		$codOrg = $oBENEFICIO->getCodigoOrganismo($persona_beneficio_id);
		$cuotaSocial = $oBENEFICIO->getImporteCuotaSocial($persona_beneficio_id);
		$cuotaSocial = $cuotaSocial['decimal_1'];

		$proveedor_id = parent::GlobalDato('entero_1',"MUTUPROD0003");

		App::import('Model', 'Proveedores.ProveedorVencimiento');
		$oVTOS = new ProveedorVencimiento(null);

		$vtos = $oVTOS->calculaVencimientoByPeriodo($proveedor_id,$codOrg,$periodoIni,date('Y-m-d'));


		#GENERO UNA SOLICITUD DE AFILIACION
		$afiliacion = array('SocioSolicitud' => array(
			'tipo_solicitud' => 'A',
			'aprobada' => 0,
			'persona_id' => $persona_id,
			'persona_beneficio_id' => $persona_beneficio_id,
			'fecha' => $fechaAlta,
			'periodo_ini' => $periodoIni,
			'primer_vto_socio' => $vtos['vto_cuota_socio'],
			'primer_vto_proveedor' => $vtos['vto_cuota_proveedor'],
			'importe_cuota_social' => $cuotaSocial,
		));

		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$periodosImputados = $oLiq->getPeriodosLiquidados(false,true,$codOrg);
		$periodoCorte = array_slice($periodosImputados,count($periodosImputados)-1,1);
		$periodoCorte = $periodoCorte[0];
//		debug($afiliacion);
//        debug($periodoIni . " *** " . $periodoCorte);
//        exit;
		if($periodoIni > $periodoCorte):

			if(!$oSolSoc->save($afiliacion)):

				parent::notificar("SE PRODUJO UN ERROR AL GENERAR LA SOLICITUD DE AFILIACION");
				return null;

			else:

				return $oSolSoc->getLastInsertID();

			endif;

		else:

			parent::notificar("EL PERIODO DE INICIO INDICADO (".parent::periodo($periodoIni).") DEBE SER POSTERIOR AL ULTIMO PERIODO IMPUTADO DEL ORGANISMO (" .parent::periodo($periodoCorte).")");
			return null;

		endif;

	}


	function getUltimoBeneficioActivosByCodOrganismo($socio_id,$codigoOrganismo){
		$this->unbindModel(array('belongsTo' => array('Persona'),'hasMany' => array('SocioCalificacion')));
		$socio = $this->read('persona_id',$socio_id);
		$persona_id = $socio['Socio']['persona_id'];
		App::import('Model', 'pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio(null);
		$beneficios = $oBENEFICIO->beneficiosActivosByPersona($persona_id,false);
		$beneficios = Set::extract("/PersonaBeneficio[codigo_beneficio=$codigoOrganismo]",$beneficios);
		$beneficios = Set::sort($beneficios, '{n}.PersonaBeneficio.id', 'desc');
		$beneficio = array_shift($beneficios);
		return $beneficio;
	}

	/**
	 * Categoriza a un socio en base al beneficio que tiene como principal
	 * donde se descuenta la cuota social.
	 *
	 * En caso de ser CBU verifica con la global el codigo de empresa.  Si en la global en el concepto_3
	 * no esta indicado que es categoria activo se toma como adherente
	 *
	 * @author adrian [11/04/2012]
	 * @param array $socio
	 * @return array
	 */
	function categorizar($socio){

    	App::import('Model', 'Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();
		$beneficio = $oBENEFICIO->read(null,$socio['Socio']['persona_beneficio_id']);

		if(empty($beneficio)) return $socio;

		$organismo = $beneficio['PersonaBeneficio']['codigo_beneficio'];
		$categoria = parent::GlobalDato('concepto_3',$organismo);
		$socio['Socio']['categoria'] = (empty($categoria) ? 'MUTUCASOADHE' : $categoria);

		if(substr($organismo,8,2) == 22):
			$empresa = $beneficio['PersonaBeneficio']['codigo_empresa'];
			$categoria = parent::GlobalDato('concepto_3',$empresa);
			if(!empty($categoria) && $socio['Socio']['categoria'] != $categoria) $socio['Socio']['categoria'] = $categoria;
		endif;

		return $socio;

	}


	function getResumenAltaBajaByCategoriaEntreFechas($fecha_desde,$fecha_hasta){

		$sql = "SELECT categoria FROM socios AS Socio GROUP BY categoria";
		$categorias = $this->query($sql);

		if(empty($categorias)) return null;

		foreach($categorias as $row):

			$categoria = $row['Socio']['categoria'];

//			debug($categoria);

		endforeach;



	}

	function getDatoSocio($socio_id,$toArray=FALSE){
		$datos = null;
		$socio = $this->read(null,$socio_id);
                if($toArray){
                    $datos = array();
                    $datos['id'] = $socio['Socio']['id'];
                    $datos['fecha_alta'] = date('d-m-Y',strtotime($socio['Socio']['fecha_alta']));
                    $datos['categoria'] = parent::GlobalDato("concepto_1", $socio['Socio']['categoria']);
                    $datos['calificacion'] = parent::GlobalDato("concepto_1", $socio['Socio']['calificacion']);
                    $datos['fecha_calificacion'] = date('d-m-Y',strtotime($socio['Socio']['fecha_calificacion']));
                }else{
                    $datos = "#".$socio['Socio']['id'];
                    $datos .= " | ALTA: ".date('d-m-Y',strtotime($socio['Socio']['fecha_alta']));
                    $datos .= " | CATEGORIA: " . parent::GlobalDato("concepto_1", $socio['Socio']['categoria']);
                    $datos .= " | CALIF: " . parent::GlobalDato("concepto_1", $socio['Socio']['calificacion']) . " (".date('d-m-Y',strtotime($socio['Socio']['fecha_calificacion'])).")";
                }
                return $datos;

	}


    function get_estado_cuenta_ente_recaudador($socio,$enteRecaudador){
        $datosEnte = parent::getGlobalDato(null,$enteRecaudador);
        ini_set("soap.wsdl_cache_enabled", 0);
        $sClient = new SoapClient($datosEnte['GlobalDato']['concepto_2'],array('trace' => true));
        $result = $sClient->__soapCall("estadoCuenta", array($socio['Persona']['documento'],trim($datosEnte['GlobalDato']['concepto_3'])));
        $result = json_decode($result);

        $consulta = array(
            'ente' => $datosEnte['GlobalDato']['concepto_1'],
            'cliente' => $result->client,
            'registros' => $result->find,
            'error' => $result->error,
            'msg' => $result->msg_error,
            'estado_cuenta' => $result->result

        );
        return $consulta;
    }


    function get_liquidacion_deuda_ente_recaudador($socio,$enteRecaudador){
        $datosEnte = parent::getGlobalDato(null,$enteRecaudador);
        ini_set("soap.wsdl_cache_enabled", 0);
        $sClient = new SoapClient($datosEnte['GlobalDato']['concepto_2'],array('trace' => true));
        $result = $sClient->__soapCall("liquidacionDeuda", array($socio['Persona']['documento'],trim($datosEnte['GlobalDato']['concepto_3'])));
        $result = json_decode($result);

        $consulta = array(
            'ente' => $datosEnte['GlobalDato']['concepto_1'],
            'cliente' => $result->client,
            'registros' => $result->find,
            'error' => $result->error,
            'msg' => $result->msg_error,
            'estado_cuenta' => $result->result

        );
        return $consulta;

    }

    function get_registros_stop_debit($socio_id){
        App::import('Model', 'mutual.LiquidacionSocioRendicion');
        $oLSR = new LiquidacionSocioRendicion(null);
        return $oLSR->get_historico_stop_debit($socio_id);
    }


    function alta_informe($datos){

//        debug($datos);
//        exit;

        App::import('Model', 'pfyj.SocioInforme');
        $oSI = new SocioInforme();

        $socioInfo = array(
            'id' => 0,
            'socio_id' => $datos['SocioInforme']['socio_id'],
            'empresa' => $datos['SocioInforme']['empresa'],
            'tipo_novedad' => 'A',
            'periodo_hasta' => $datos['SocioInforme']['periodo_corte'],
            'deuda_informada' => $datos['SocioInforme']['saldo_conciliado'],
            'fecha_calculo_deuda' => $datos['SocioInforme']['fecha_calculo_deuda'],
        );

        parent::begin();
        if(!$oSI->save($socioInfo)){
            parent::rollback();
            return false;
        }

        $socioInfoID = $oSI->getLastInsertID();

        App::import('Model', 'pfyj.SocioInformeCuota');
        $oSIC = new SocioInformeCuota();

        $cuotas = unserialize(base64_decode($datos['SocioInforme']['cuotas']));

        if(empty($cuotas)){
            parent::rollback();
            return false;
        }

        foreach($cuotas as $cuota){

            $socioInfoCuota = array(
                'orden_descuento_cuota_id' => $cuota['id'],
                'socio_informe_id' => $socioInfoID,
                'saldo_informado' => $cuota['saldo_conciliado'],
            );

            if(!$oSIC->save($socioInfoCuota)){
                parent::rollback();
                return false;
            }

        }

        return parent::commit();

    }


}
?>

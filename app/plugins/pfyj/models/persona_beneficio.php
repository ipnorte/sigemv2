<?php
class PersonaBeneficio extends PfyjAppModel{

	var $name = 'PersonaBeneficio';
	var $hasMany = array('PersonaBeneficioCompartido');

	var $validate = array(
							'tipo' => array(
													'rule' => 'validate_Tipo',
													'message' => '!'
													),
							'nro_ley' => array(
													'rule' => 'validate_NroLey',
													'message' => '!'
													),
							'nro_beneficio' => array(
													'rule' => 'validate_NroBeneficio',
													'message' => '!'
													),
							'sub_beneficio' => array(
													'rule' => 'validate_SubBeneficio',
													'message' => '!'
													),
							'codigo_empresa' => array(
													'rule' => 'validate_CodigoEmpresa',
													'message' => '!'
													),
							'codigo_reparticion' => array(
													'rule' => 'validate_CodigoReparticion',
													'message' => '!'
													),
							'turno_pago' => array(
													'rule' => 'validate_TurnoPago',
													'message' => '!'
													),
							'cbu' => array(
													'rule' => 'validate_Cbu',
													'message' => '!'
													),

	);


	function save($data = null, $validate = true, $fieldList = array()){
		$data = $this->deco_cbu($data);
		if(!$validate) $this->validate = null;
		return parent::save($data,$validate,$fieldList);
	}

	function getBeneficio($id,$agregarOrganismo=true,$labelyJP = true){
		$beneficio = $this->read(null,$id);
		$beneficio = $this->armaDatos($beneficio,$agregarOrganismo,$labelyJP);
		return $beneficio;
	}

	function beneficiosByPersona($persona_id,$armaDatos=true,$soloActivos = false){
		$this->unbindModel(array('belongsTo' => array('Persona')));
		$conditions = array(
			'PersonaBeneficio.persona_id' => $persona_id,
		);
		if($soloActivos){$conditions['PersonaBeneficio.activo'] = 1;}
		$beneficios = $this->find('all',array('conditions'=>$conditions,'order'=>'PersonaBeneficio.created DESC'));

		if(!empty($beneficios)){
			if(!$armaDatos){return $beneficios;}
			foreach($beneficios as $idx => $beneficio){
					$beneficio = $this->armaDatos($beneficio);
					$beneficios[$idx] = $beneficio;
			}
			return $beneficios;
		}else{
			return null;
		}
	}

        /**
         *
         * @param type $persona_id
         * @param type $armaDatos
         * @return type
         * @deprecated usar metodo beneficiosByPersona($persona_id,$armaDatos=true,$soloActivos = false)
         */
	function beneficiosActivosByPersona($persona_id,$armaDatos=true){
		$this->unbindModel(array('belongsTo' => array('Persona')));
		$beneficios = $this->find('all',array('conditions'=>array('PersonaBeneficio.persona_id' => $persona_id,'PersonaBeneficio.activo' => 1),'order'=>'PersonaBeneficio.created DESC'));
		if(!empty($beneficios)){
			foreach($beneficios as $idx => $beneficio){
				if($armaDatos)$beneficio = $this->armaDatos($beneficio);
				$beneficios[$idx] = $beneficio;
			}
			return $beneficios;
		}else{
			return null;
		}
	}

	function beneficiosActivosSinAcuerdoByPersona($persona_id,$armaDatos=true){
		$this->unbindModel(array('belongsTo' => array('Persona')));
		$beneficios = $this->find('all',array('conditions'=>array('PersonaBeneficio.persona_id' => $persona_id,'PersonaBeneficio.acuerdo_debito' => 0,'PersonaBeneficio.activo' => 1),'order'=>'PersonaBeneficio.created DESC'));
		if(!empty($beneficios)){
			foreach($beneficios as $idx => $beneficio){
				if($armaDatos)$beneficio = $this->armaDatos($beneficio);
				$beneficios[$idx] = $beneficio;
			}
			return $beneficios;
		}else{
			return null;
		}
	}


	function getByIdr($idr){
		$beneficio = $this->findAllByIdr($idr);
		if(count($beneficio)==0) return;
		$beneficio = $this->armaDatos($beneficio[0]);
		return $beneficio;
	}

	function getByOrdenDto($ordenDtoId){

		App::import('Model','mutual.OrdenDescuento');
		$oDTO = new OrdenDescuento();
		$oDTO->unbindModel(array('hasMany' => array('OrdenDescuentoCuota')));
		$orden = $oDTO->read('persona_beneficio_id',$ordenDtoId);
		if(empty($orden)) return null;

		$beneficio = $this->getBeneficio($orden['OrdenDescuento']['persona_beneficio_id']);

		return $beneficio;

	}


	function bancosHabilitadosToCBU(){
		App::import('Model', 'Config.Banco');
		$this->Banco = new Banco(null);
		$bancos = $this->Banco->find('list',array('conditions' => array('Banco.activo' => 1,'Banco.beneficio' => 1),'fields' => 'Banco.nombre', 'order' => 'Banco.nombre'));
		$bancos = array_keys($bancos);
		$tmp = array();
		foreach($bancos as $idx => $banco){
			$bcoCbu = substr($banco,-3,3);
			array_push($tmp,"'".$bcoCbu."'");
		}
		$bancos = implode(',',$tmp);
		return $bancos;
	}



	function armaDatos($beneficio,$agregarOrganismo=true,$labelyJP = true){

		$glb = $this->getGlobalDato('concepto_1,concepto_2',$beneficio['PersonaBeneficio']['codigo_beneficio']);
		$beneficio['PersonaBeneficio']['codigo_beneficio_desc'] = $glb['GlobalDato']['concepto_1'];
		$beneficio['PersonaBeneficio']['tipo_org'] = $glb['GlobalDato']['concepto_2'];

		if(isset($beneficio['PersonaBeneficio']['banco_id'])):
			$bco = $this->getBanco($beneficio['PersonaBeneficio']['banco_id']);
			$beneficio['PersonaBeneficio']['banco'] = $bco['Banco']['nombre'];
		endif;

		if(isset($beneficio['PersonaBeneficio']['codigo_empresa'])):
			$glb = $this->getGlobalDato('concepto_1',$beneficio['PersonaBeneficio']['codigo_empresa']);
			$beneficio['PersonaBeneficio']['codigo_empresa_desc'] = $glb['GlobalDato']['concepto_1'];
		endif;

		//descomponer datos del banco, sucursal, tipo y nro de cuenta
		$beneficio = $this->deco_cbu($beneficio);

		$tipo = substr($beneficio['PersonaBeneficio']['codigo_beneficio'],8,2);
		$beneficio['PersonaBeneficio']['beneficio_cjpc_nro'] = NULL;
		switch ($tipo) {
//			case '22':
//				$str = $beneficio['PersonaBeneficio']['codigo_beneficio_desc'] . ' - EMPRESA: ' . $beneficio['PersonaBeneficio']['codigo_empresa_desc'] . ' - ' . $beneficio['PersonaBeneficio']['banco'] . ' - CBU: '.$beneficio['PersonaBeneficio']['cbu'] ;
//				break;
			case '77':
				$beneficio['PersonaBeneficio']['beneficio_cjpc_nro'] = $beneficio['PersonaBeneficio']['tipo'].$beneficio['PersonaBeneficio']['nro_ley'].$beneficio['PersonaBeneficio']['nro_beneficio'].$beneficio['PersonaBeneficio']['sub_beneficio'] ;
				break;
//			case '66':
//				$str = $beneficio['PersonaBeneficio']['codigo_beneficio_desc'] . ' - NRO.: ' . $beneficio['PersonaBeneficio']['nro_beneficio'];
//				break;
//
		}
		if(isset($beneficio['PersonaBeneficio']['id']))$beneficio['PersonaBeneficio']['string'] = $this->getStrBeneficio($beneficio['PersonaBeneficio']['id'],$agregarOrganismo,$beneficio,$labelyJP);
		return $beneficio;
	}


	function deco_cbu($beneficio){

		if(!isset($beneficio['PersonaBeneficio']['cbu']) || empty($beneficio['PersonaBeneficio']['cbu'])) return $beneficio;
		$cbu = trim($beneficio['PersonaBeneficio']['cbu']);
		if(empty($cbu)) return $beneficio;

		App::import('Model','Config.Banco');
		$oBANCO = new Banco();
		$datos = $oBANCO->deco_cbu($cbu);

		if(empty($datos)) return $beneficio;
		$beneficio['PersonaBeneficio']['banco_id'] = $datos['banco_id'];
                $beneficio['PersonaBeneficio']['tipo_cta_bco'] = $datos['tipo_cta_bco'];

                if($datos['banco_id'] == '00020'){
                    $beneficio['PersonaBeneficio']['nro_cta_bco'] = (
                            intval($beneficio['PersonaBeneficio']['nro_cta_bco']) != intval($datos['nro_cta_bco'])
                            ? intval($datos['nro_cta_bco']) : intval($beneficio['PersonaBeneficio']['nro_cta_bco'])
                    );
                    $beneficio['PersonaBeneficio']['nro_cta_bco'] = str_pad($beneficio['PersonaBeneficio']['nro_cta_bco'],9,0,STR_PAD_LEFT);
                }

//                debug($datos);
//                debug($beneficio);
//                exit;


//		$beneficio['PersonaBeneficio']['banco_id'] = (isset($datos['banco_id']) ? $datos['banco_id'] : (isset($beneficio['PersonaBeneficio']['nro_sucursal']) ? $beneficio['PersonaBeneficio']['nro_sucursal'] : ""));
//		$beneficio['PersonaBeneficio']['nro_sucursal'] = (isset($datos['sucursal']) ? $datos['sucursal'] : (isset($datos['nro_sucursal']) ? $datos['nro_sucursal'] : (isset($beneficio['PersonaBeneficio']['nro_sucursal']) ? $beneficio['PersonaBeneficio']['nro_sucursal'] : "")));
//		$beneficio['PersonaBeneficio']['tipo_cta_bco'] = (isset($datos['tipo_cta_bco']) ? $datos['tipo_cta_bco'] : (isset($beneficio['PersonaBeneficio']['tipo_cta_bco']) ? $beneficio['PersonaBeneficio']['tipo_cta_bco'] : ""));
//		$beneficio['PersonaBeneficio']['nro_cta_bco'] = (isset($datos['nro_cta_bco']) ? $datos['nro_cta_bco'] : $beneficio['PersonaBeneficio']['nro_cta_bco']);

//		$beneficio['PersonaBeneficio']['cbu_codigo_banco'] = str_pad(substr($cbu,0,3),5,'0',STR_PAD_LEFT);
//		$beneficio['PersonaBeneficio']['cbu_sucursal'] = substr($cbu,3,4);
//		$beneficio['PersonaBeneficio']['cbu_tipo_cta_bco'] = substr($cbu,8,2);
//		$beneficio['PersonaBeneficio']['cbu_nro_cta_bco'] = substr($cbu,10,11);
//		debug($beneficio);
		return $beneficio;
	}

	//Actualizo datos tarjeta debito
	function actualizarTarjetaV2($data,$validar){
		return parent::save($data);
	}

	function guardar($data,$validar = true){
		//actualizo en la v1
		$data = $this->deco_cbu($data);

		if(!empty($data['TarjetaDebito']['card_number'])){
			App::import('Vendor','crypt');
			$oCRYPT = new Crypt();

			$oTarjeta = new stdClass();
			$oTarjeta->card_holder_name = $data['TarjetaDebito']['card_holder_name'];
			$oTarjeta->card_number = $data['TarjetaDebito']['card_number'];
			$oTarjeta->card_expiration_month = $data['TarjetaDebito']['card_expiration_month'];
			$oTarjeta->card_expiration_year = $data['TarjetaDebito']['card_expiration_year'];
			
			$oTarjeta->security_code = $data['TarjetaDebito']['security_code'];

			$data['PersonaBeneficio']['tarjeta_titular'] = $oTarjeta->card_holder_name;

			App::import('Helper', 'Util');
			$oUT = new UtilHelper();

			$data['PersonaBeneficio']['tarjeta_numero'] = $oUT->maskLeft($oTarjeta->card_number);
			$data['PersonaBeneficio']['tarjeta_debito'] = $oCRYPT->encrypt(serialize($oTarjeta));
		}

		if(isset($data['PersonaBeneficio']['nro_sucursal'])) if(empty($data['PersonaBeneficio']['nro_sucursal'])) $data['PersonaBeneficio']['nro_sucursal'] = $data['PersonaBeneficio']['nro_sucursal'];
		if(isset($data['PersonaBeneficio']['nro_cta_bco'])) if(empty($data['PersonaBeneficio']['nro_cta_bco'])) $data['PersonaBeneficio']['nro_cta_bco'] = $data['PersonaBeneficio']['nro_cta_bco'];
		
                if(isset($data['PersonaBeneficio']['turno_pago'])) { $data['PersonaBeneficio']['turno_pago'] = strtoupper($data['PersonaBeneficio']['turno_pago']);}
		
		if(!isset($data['PersonaBeneficio']['id']) && $data['PersonaBeneficio']['id'] == 0 && MODULO_V1){
			App::import('Model', 'V1.Beneficio');
			$oBeneficio = new Beneficio(null);
			if($oBeneficio->guardar($data)){
				$idr = $oBeneficio->getLastInsertID();
				if(!empty($idr))$data['PersonaBeneficio']['idr'] = $idr;
			}
		}
		if(!isset($data['PersonaBeneficio']['codigo_reparticion'])) $data['PersonaBeneficio']['codigo_reparticion'] = "";
		if(isset($data['PersonaBeneficio']['codigo_empresa'])){
			if(!isset($data['PersonaBeneficio']['turno_pago']) || empty($data['PersonaBeneficio']['turno_pago'])){
				$data['PersonaBeneficio']['turno_pago'] = $data['PersonaBeneficio']['codigo_empresa'];
			} 
		} 

		// si es jp verifico que tenga cargado el tipo, si no lo tiene se lo genero
		// si es jp y el nro de beneficio < 6 caracteres lo completo con ceros a la izquierda
		if(substr($data['PersonaBeneficio']['codigo_beneficio'],8,2) == 77){
			if(empty($data['PersonaBeneficio']['tipo']) && !empty($data['PersonaBeneficio']['nro_ley'])) $data['PersonaBeneficio']['tipo'] = $this->getTipoJubilacionByNroLey($data['PersonaBeneficio']['nro_ley']);
			if(strlen(trim($data['PersonaBeneficio']['nro_beneficio'])) < 6) $data['PersonaBeneficio']['nro_beneficio'] = parent::fill($data['PersonaBeneficio']['nro_beneficio'],6);
		}
                if($data['PersonaBeneficio']['cbu_nosis_validado']){
                    $data['PersonaBeneficio']['cbu_nosis_fecha_validacion'] = date('Y-m-d H:i:s');
                }
//		debug($data);
//        exit;
		return parent::save($data,$validar);
	}


	function getImporteCuotaSocial($id){
		$beneficio = $this->read(null,$id);
		$glb = parent::getGlobalDato('concepto_1,decimal_1','MUTUCUOS' . substr($beneficio['PersonaBeneficio']['codigo_beneficio'],8,4));
		return $glb['GlobalDato'];
	}

	function getStrBeneficio($id,$agregarOrganismo=true,$beneficio = null,$labelyJP = true){
		$str = "";
		if(empty($beneficio) && !empty($id)) $beneficio = $this->read(null,$id);
		$tipoOrganismo = parent::getGlobalDato('concepto_1,concepto_2',$beneficio['PersonaBeneficio']['codigo_beneficio']);
		$codigo = substr($beneficio['PersonaBeneficio']['codigo_beneficio'],8,2);
		switch($codigo){
			case '22':
				$empresa = '';
				if(!empty($beneficio['PersonaBeneficio']['codigo_empresa']))$empresa = parent::getGlobalDato('concepto_1',$beneficio['PersonaBeneficio']['codigo_empresa']);
				$cbu = $beneficio['PersonaBeneficio']['cbu'];
				$reparticion = '';
				$turno = "";
				if($beneficio['PersonaBeneficio']['codigo_empresa']=="MUTUEMPRP001")$turno = "OP:".$beneficio['PersonaBeneficio']['turno_pago']."|";
				if(!empty($beneficio['PersonaBeneficio']['codigo_reparticion']))$reparticion = $beneficio['PersonaBeneficio']['codigo_reparticion'];
				$str = (isset($empresa['GlobalDato']['concepto_1']) ? $empresa['GlobalDato']['concepto_1'] : '') . '|' . $turno . $reparticion . '|CBU:'.$cbu;
				if($beneficio['PersonaBeneficio']['acuerdo_debito'] != 0){
					$str .= " *** ACUERDO DE DEBITO $ ".number_format($beneficio['PersonaBeneficio']['acuerdo_debito'],2)." ***";
				}
				if(!empty($beneficio['PersonaBeneficio']['tarjeta_numero'])){
					$str .= " | T.DEB. " . $beneficio['PersonaBeneficio']['tarjeta_numero'];
				}

				break;
			case '77':
				$ley = $beneficio['PersonaBeneficio']['nro_ley'];
				$tipo = ($beneficio['PersonaBeneficio']['tipo'] == 1 ? 'J' : 'P');
				$nroBeneficio = $beneficio['PersonaBeneficio']['nro_beneficio'];
				$subBeneficio = $beneficio['PersonaBeneficio']['sub_beneficio'];
                                
                                $str = "BENFICIO: " . $tipo."-".$ley."-".str_pad($nroBeneficio,6,0,STR_PAD_LEFT)."-".$subBeneficio;
                                
                                if(!empty($beneficio['PersonaBeneficio']['cbu'])) {
                                    $str .= " | CBU: " . $beneficio['PersonaBeneficio']['cbu'];
                                }
                                
				/*if($labelyJP) {
				    $str = "LEY:$ley|TIPO:$tipo|BENFICIO:$nroBeneficio|SUB-BENEFICIO:$subBeneficio";
				}else{
				    $str = $tipo."-".$ley."-".str_pad($nroBeneficio,6,0,STR_PAD_LEFT)."-".$subBeneficio;
				}*/
				break;
			case '66':
				$nroBeneficio = $beneficio['PersonaBeneficio']['nro_beneficio'];
				$str = "BENFICIO:$nroBeneficio";
				break;
			default:
				$str = $tipoOrganismo['GlobalDato']['concepto_1'];
				break;
		}
		if($beneficio['PersonaBeneficio']['activo'] == 0) $str .= " (*** NO VIGENTE ***)";
		if($agregarOrganismo)return $tipoOrganismo['GlobalDato']['concepto_1']." - ".$str;
		else return $str;
	}

	function getOrganismo($id){
		$beneficio = $this->read('codigo_beneficio',$id);
		$tipoOrganismo = parent::getGlobalDato('concepto_1',$beneficio['PersonaBeneficio']['codigo_beneficio']);
		return $tipoOrganismo['GlobalDato']['concepto_1'];
	}

	/**
	 * Devuelve el codigo del organismo al cual pertenece un beneficio
	 * @param $id
	 * @return unknown_type
	 */
	function getCodigoOrganismo($id){
		$beneficio = $this->read('codigo_beneficio',$id);
		return $beneficio['PersonaBeneficio']['codigo_beneficio'];
	}

	function isMismoOrganismo($id,$codigo_organismo){
		$beneficio = $this->read(null,$id);
		if(substr($beneficio['PersonaBeneficio']['codigo_beneficio'],8,4) === $codigo_organismo) return true;
		else return false;
	}


	function baja($data){

//            debug($data);
//            exit;

		$ben = $this->read(null,$data['PersonaBeneficio']['id']);

//		$this->id = $data['PersonaBeneficio']['id'];
//		parent::saveField('codigo_baja',$data['PersonaBeneficio']['codigo_baja']);
//		parent::saveField('fecha_baja',$data['PersonaBeneficio']['fecha_baja']);
//		parent::saveField('accion',$data['PersonaBeneficio']['accion']);

                // if(!empty($data['PersonaBeneficio']['periodo_desde'])){
                //     $data['PersonaBeneficio']['periodo_desde'] = $data['PersonaBeneficio']['periodo_desde']['year'] . $data['PersonaBeneficio']['periodo_desde']['month'];
                // }

		if($data['PersonaBeneficio']['accion'] == 'R' && isset($data['PersonaBeneficio']['persona_beneficio_id'])){
			App::import('Model', 'Mutual.OrdenDescuento');
			$oDto = new OrdenDescuento();
			if($oDto->reasignarBeneficioByPersonaBeneficioId($data['PersonaBeneficio']['id'],$data['PersonaBeneficio']['persona_beneficio_id'],$data['PersonaBeneficio']['periodo_desde'])){
//				parent::saveField('reasignado_id',$data['PersonaBeneficio']['persona_beneficio_id']);
                                $ben['PersonaBeneficio']['reasignado_id'] = $data['PersonaBeneficio']['persona_beneficio_id'];
			}else{
				return false;
			}

		}

		$ben['PersonaBeneficio']['codigo_baja'] = $data['PersonaBeneficio']['codigo_baja'];
		$ben['PersonaBeneficio']['fecha_baja'] = $data['PersonaBeneficio']['fecha_baja'];
		$ben['PersonaBeneficio']['accion'] = $data['PersonaBeneficio']['accion'];
		$ben['PersonaBeneficio']['activo'] = 0;
                $ben['PersonaBeneficio']['banco_id'] = (empty($ben['PersonaBeneficio']['banco_id']) || intval($ben['PersonaBeneficio']['banco_id'])  == 0 ? null : $ben['PersonaBeneficio']['banco_id']);



		if(!parent::save($ben,false)){
                    return false;
                }


		if(isset($data['PersonaBeneficio']['idr']) && MODULO_V1){
			App::import('Model', 'V1.Beneficio');
			$oBeneficio = new Beneficio(null);
			$oBeneficio->id = $data['PersonaBeneficio']['idr'];
			$oBeneficio->saveField('activo',0);
		}

		if($data['PersonaBeneficio']['accion'] == 'B'){
			parent::saveField('reasignado_id','');
			#baja cuotas
			App::import('Model', 'Mutual.OrdenDescuentoCuota');
			$oCuota = new OrdenDescuentoCuota();
			$oCuota->bajaByPersonaBeneficio($data['PersonaBeneficio']['id'],$data['PersonaBeneficio']['codigo_baja']);
			#marco como no activa la orden
			App::import('Model', 'Mutual.OrdenDescuento');
			$oDto = new OrdenDescuento();
			$oDto->desactivarByPersonaBeneficioId($data['PersonaBeneficio']['id']);
		}

		return true;

	}

	/**
	 * guarda en el beneficio el mayor importe debitado
	 * @param unknown_type $persona_beneficio_id
	 * @param unknown_type $importe_debitado
	 * @param unknown_type $periodo
	 */
	function actualizarMayorPeriodoDebitado($persona_beneficio_id,$importe_debitado,$periodo){
		$this->id = $persona_beneficio_id;
		$mayor_debito = $this->read('importe_mayor_debito',$persona_beneficio_id);
		$mayor_debito = $mayor_debito['PersonaBeneficio']['importe_mayor_debito'];
		if($importe_debitado > $mayor_debito){
			parent::saveField('importe_mayor_debito',$importe_debitado);
			parent::saveField('periodo_mayor_debito',$periodo);
		}
	}

    function modificarOrganismo($id,$codigoOrganismo){
        $this->id = $id;
        if(!empty($codigoOrganismo)){
            parent::saveField('codigo_beneficio',$codigoOrganismo);
        }
    }


    function clonarBeneficio($id,$codigoOrganismo = null){
        $beneficio = $this->read(null,$id);
        $newId = $beneficio['PersonaBeneficio']['id'];
        if(!empty($codigoOrganismo)){
            $beneficio['PersonaBeneficio']['id'] = 0;
            $beneficio['PersonaBeneficio']['codigo_beneficio'] = $codigoOrganismo;
            if(parent::save($beneficio)) $newId = parent::getLastInsertId();
        }
        return $newId;
    }

	function getTurno($id){
		$beneficio = $this->read(null,$id);
		if(!empty($beneficio['PersonaBeneficio']['turno_pago'])) return $beneficio['PersonaBeneficio']['turno_pago'];
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
		$turnos = $oTURNO->find('all',array('conditions' => array('LiquidacionTurno.codigo_empresa' => $beneficio['PersonaBeneficio']['codigo_empresa'],'LiquidacionTurno.codigo_reparticion' => ( !empty($beneficio['PersonaBeneficio']['codigo_reparticion']) ? substr(trim($beneficio['PersonaBeneficio']['codigo_reparticion']),0,6) : '')),'limit' => 1));
		if(isset($turnos[0]['LiquidacionTurno'])) return $turnos[0]['LiquidacionTurno']['turno'];
		else return "SDATO";
	}


	function getTurnoDescripcion($id){
		$beneficio = $this->read(null,$id);
		if(empty($beneficio['PersonaBeneficio']['turno_pago'])) return null;
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
		$desc = $oTURNO->getDescripcionByTruno($beneficio['PersonaBeneficio']['turno_pago']);
		return $desc;
	}

	function getEmpresaDescripcion($id){
		$beneficio = $this->read('codigo_empresa',$id);
		if(empty($beneficio['PersonaBeneficio']['codigo_empresa']) || $beneficio['PersonaBeneficio']['codigo_empresa'] == 'MUTUEMPR') return null;
		$empresa = parent::getGlobalDato('concepto_1',$beneficio['PersonaBeneficio']['codigo_empresa']);
		return $empresa['GlobalDato']['concepto_1'];
	}

	function getNombreBanco($id){
		$beneficio = $this->read('banco_id',$id);
		if(empty($beneficio['PersonaBeneficio']['banco_id'])) return null;
		$banco = parent::getNombreBanco($beneficio['PersonaBeneficio']['banco_id']);
		return $banco;
	}

	function resetAcuerdoPago($id){
		$beneficio = $this->read(null,$id);
		$beneficio['PersonaBeneficio']['acuerdo_debito'] = 0;
		return parent::save($beneficio);
	}


	/**
	 * Devuelve el importe maximo a divir cada registro de debito para el CBU
	 *
	 * @author adrian [15/02/2012]
	 * @param int $id
	 * @return float
	 */
	function getImpoMaxRegistroCBU($id){
		$beneficio = $this->read('importe_max_registro_cbu',$id);
		return $beneficio['PersonaBeneficio']['importe_max_registro_cbu'];
	}


	function validate_Tipo($value){
		$validado = true;
//		$datos = $this->data['PersonaBeneficio'];
//		if(substr($datos['codigo_beneficio'],8,2) == 77){
//			if(preg_match('|^[0-1]*$|', $datos['tipo'])) $validado = false;
//		}
		return $validado;
	}

	function validate_NroLey($value){
		$validado = true;
		$datos = $this->data['PersonaBeneficio'];
		if(substr($datos['codigo_beneficio'],8,2) == 77){
			if(empty($datos['nro_ley'])) $validado = false;
			//control tipo -> ley
			if(isset($datos['tipo']))$validado = $this->controlTipoLeyCJP($datos['tipo'],$datos['nro_ley']);
			else $validado = false;
		}
		return $validado;
	}
	function validate_NroBeneficio($value){
		$validado = true;
		$datos = $this->data['PersonaBeneficio'];
		if(substr($datos['codigo_beneficio'],8,2) == 77 || substr($datos['codigo_beneficio'],8,2) == 66){
			if(empty($datos['nro_beneficio'])){
				parent::notificar("DEBE INDICAR EL NUMERO DE BENEFICIO.");
				return false;
			}
		}
		if(substr($datos['codigo_beneficio'],8,2) == 77 && strlen(trim($datos['nro_beneficio'])) > 6 ){

			parent::notificar("EL NUMERO DE BENEFICIO TIENE MAS DE 6 CARACTERES");
			return false;

		}else if(substr($datos['codigo_beneficio'],8,2) == 77 && strlen(trim($datos['nro_beneficio'])) == 6){
			$conditions = array();
			$conditions['PersonaBeneficio.codigo_beneficio'] = $this->data['PersonaBeneficio']['codigo_beneficio'];
			$conditions['PersonaBeneficio.nro_ley'] = $this->data['PersonaBeneficio']['nro_ley'];
			$conditions['PersonaBeneficio.tipo'] = $this->data['PersonaBeneficio']['tipo'];
			$conditions['PersonaBeneficio.nro_beneficio'] = $this->data['PersonaBeneficio']['nro_beneficio'];
			$conditions['PersonaBeneficio.sub_beneficio'] = $this->data['PersonaBeneficio']['sub_beneficio'];
			$conditions['PersonaBeneficio.persona_id'] = $this->data['PersonaBeneficio']['persona_id'];
			$conditions['PersonaBeneficio.activo'] = 1;
			$cantidad = $this->find('all',array('conditions' => $conditions,'fields' => array('PersonaBeneficio.persona_id'),'group' => array('PersonaBeneficio.persona_id')));
			if(!empty($cantidad) && count($cantidad) > 1){
				parent::notificar("EL BENEFICIO YA EXISTE PARA ESTA PERSONA.");
				return false;
			}
			$conditions = array();
			$conditions['PersonaBeneficio.codigo_beneficio'] = $this->data['PersonaBeneficio']['codigo_beneficio'];
			$conditions['PersonaBeneficio.nro_ley'] = $this->data['PersonaBeneficio']['nro_ley'];
			$conditions['PersonaBeneficio.tipo'] = $this->data['PersonaBeneficio']['tipo'];
			$conditions['PersonaBeneficio.nro_beneficio'] = $this->data['PersonaBeneficio']['nro_beneficio'];
			$conditions['PersonaBeneficio.sub_beneficio'] = $this->data['PersonaBeneficio']['sub_beneficio'];
			$conditions['PersonaBeneficio.activo'] = 1;
			$conditions['PersonaBeneficio.persona_id <>'] = $this->data['PersonaBeneficio']['persona_id'];
			$cantidad = $this->find('all',array('conditions' => $conditions,'fields' => array('PersonaBeneficio.persona_id'),'group' => array('PersonaBeneficio.persona_id')));
			if(!empty($cantidad)){
				App::import('Model','Pfyj.Persona');
				$oPER = new Persona();
				foreach($cantidad as $cant):
					$persona = $oPER->getDatoPersona($cant['PersonaBeneficio']['persona_id']);
					parent::notificar("EL NRO DE BENEFICIO YA EXISTE PARA OTRA PERSONA | $persona");
				endforeach;
				return false;
			}

//			debug($cantidad);
//			exit;

		}
		return $validado;
	}

	function validate_SubBeneficio($value){
		$validado = true;
		$datos = $this->data['PersonaBeneficio'];
		if(substr($datos['codigo_beneficio'],8,2) == 77){
			if(empty($datos['sub_beneficio'])) $validado = false;
		}
		return $validado;
	}


	function validate_Cbu($value){
		$validado = true;
		$datos = $this->data['PersonaBeneficio'];
		if(substr($datos['codigo_beneficio'],8,2) == 22 || (substr($datos['codigo_beneficio'],8,2) == 77 && !empty($datos['cbu']))){
                        $validar = parent::GlobalDato('concepto_2', $datos['codigo_beneficio']);
			if(empty($datos['cbu']) && $validar == 'CBU'){
				parent::notificar("DEBE INDICAR EL NRO DE CBU!");
				return false;
			}else if(!parent::validarCBU($datos['cbu'])){
                            $validado = false;
                        }
                        if($validar == 'CBU'){
                            $conditions = array();
                            $conditions['PersonaBeneficio.cbu'] = $this->data['PersonaBeneficio']['cbu'];
                            $conditions['PersonaBeneficio.persona_id <>'] = $this->data['PersonaBeneficio']['persona_id'];
                            $conditions['PersonaBeneficio.activo'] = 1;
                            $cantidad = $this->find('all',array('conditions' => $conditions,'fields' => array('PersonaBeneficio.persona_id'),'group' => array('PersonaBeneficio.persona_id')));
                            if(!empty($cantidad)){
                                    $validado = false;
                                    App::import('Model','Pfyj.Persona');
                                    $oPER = new Persona();
                                    foreach($cantidad as $cant):
                                            $persona = $oPER->getDatoPersona($cant['PersonaBeneficio']['persona_id']);
                                            parent::notificar("EL NRO DE CBU INDICADO YA EXISTE PARA OTRA PERSONA | $persona");
                                    endforeach;

                            }
                        }else{
                            $validado = true;
                        }
		}
//                debug($validado);
//                exit;
		return $validado;
	}

	function validate_CodigoEmpresa($value){
		$validado = true;
		$datos = $this->data['PersonaBeneficio'];
		if(substr($datos['codigo_beneficio'],8,2) == 22){
			if(empty($datos['codigo_empresa'])){
				$validado = false;
				parent::notificar("DEBE INDICAR LA EMPRESA!");
			}
			#######################################################################
			#control del DNI ANSES
			#######################################################################
			$ctrlDoc = parent::GlobalDato("concepto_2", $datos['codigo_empresa']);

			$ctrlDoc = parent::getGlobalDato("concepto_1,concepto_2", $datos['codigo_empresa']);

			$empresa = $ctrlDoc['GlobalDato']['concepto_1'];
			$ctrlDoc = $ctrlDoc['GlobalDato']['concepto_2'];

			if(!empty($ctrlDoc) && substr($datos['codigo_empresa'],0,10) == "MUTUEMPRE1"){

				$ctrlDoc = str_replace("[", "", $ctrlDoc);
				$ctrlDoc = str_replace("]", "", $ctrlDoc);
				list($dni1,$dni2) = explode("|", $ctrlDoc);

				App::import('Model','Pfyj.Persona');
				$oPER = new Persona();

				$persona = $oPER->read('documento',$datos['persona_id']);
				$dniPersona = $persona['Persona']['documento'];
				$dniPersona = trim($dniPersona);
				$dniPersona = intval(substr($dniPersona,-1));

				$dni1 = intval($dni1);
				$dni2 = intval($dni2);

				if( ($dni1 != $dni2) && (($dni1 <= $dniPersona) && ($dniPersona >= $dni2)) ){
					parent::notificar("LA TERMINACION DEL NRO DE DOCUMENTO [$dniPersona] NO COINCIDE CON LA EMPRESA $empresa");
					$validado = false;
				}else if($dniPersona != $dni1){
					parent::notificar("LA TERMINACION DEL NRO DE DOCUMENTO [$dniPersona] NO COINCIDE CON LA EMPRESA $empresa");
					$validado = false;
				}

			}

		}
		return $validado;
	}

	function validate_CodigoReparticion($value){
		$validado = true;
		$datos = $this->data['PersonaBeneficio'];
		if(substr($datos['codigo_beneficio'],8,2) == 22 && $datos['codigo_empresa'] == 'MUTUEMPRP001'){
			if(empty($datos['codigo_reparticion'])){
				parent::notificar("DEBE INDICAR EL CODIGO DE LA REPARTICION");
				$validado = false;
			}
		}
		return $validado;
	}

	function validate_TurnoPago($value){
		$validado = true;
		$datos = $this->data['PersonaBeneficio'];
		if(substr($datos['codigo_beneficio'],8,2) == 22 && $datos['codigo_empresa'] == 'MUTUEMPRP001'){
			if(!isset($datos['turno_pago']) || empty($datos['turno_pago'])){
				$validado = false;
				parent::notificar("DEBE INDICAR EL TURNO DE PAGO");
			}
		}
		return $validado;
	}

	/**
	 * Control para la Caja de Jubilaciones
	 * tipo -> 0 1 2 3 10  y tipo impar > 12 ---> PENSION (0) (el resto es JUBILADO (1))
	 * @param unknown_type $tipo
	 * @param unknown_type $ley
	 * @return boolean
	 */
	function controlTipoLeyCJP($tipo,$ley){
//		if($tipo != $this->getTipoJubilacionByNroLey($ley)){
//			parent::notificar("EL NUMERO DE LEY NO SE CORRESPONDE CON EL TIPO");
//			return false;
//		}else{
//			return true;
//		}
            return TRUE;
	}

	/**
	 * Determina el tipo de jubilacion en base al numero de ley
	 * tipo -> 0 1 2 3 10  y tipo impar > 12 ---> PENSION (0) (el resto es JUBILADO (1))
	 * @param $ley
	 */
	function getTipoJubilacionByNroLey($ley){
		$leyes = array(0,1,2,3,10);
		if(in_array($ley,$leyes)) return 0;
		if($ley > 12){
			if(($ley % 2) != 0) return 0;
			else return 1;
		}else{
			return 1;
		}

	}

	/**
	 * Valida si el CBU corresponde a un socio
	 *
	 * @author adrian [23/02/2012]
	 * @param int $socio_id
	 * @param string $cbu
	 * @return bool
	 */
	function isValidateSocioCBU($socio_id,$cbu){
		App::import('Model','pfyj.Socio');
		$oSOCIO = new Socio();
		$persona = $oSOCIO->getPersonaBySocioID($socio_id);
		if(empty($persona)) return false;
		$persona_id = $persona['Persona']['id'];
		$beneficios = $this->find('count',array('conditions' => array('PersonaBeneficio.persona_id' => $persona_id, 'PersonaBeneficio.cbu' => $cbu)));
		return ($beneficios != 0 ? true : false);
	}

	function isValidateSocioCuentaSucursal($socio_id,$sucursal,$cuenta){
		App::import('Model','pfyj.Socio');
		$oSOCIO = new Socio();
		$persona = $oSOCIO->getPersonaBySocioID($socio_id);
		if(empty($persona)) return false;
		$persona_id = $persona['Persona']['id'];
		$sucursal = str_pad($sucursal,10,0,STR_PAD_LEFT);
		$cuenta = str_pad($cuenta,15,0,STR_PAD_LEFT);
		$sql = "SELECT
				COUNT(*) AS cant
				FROM persona_beneficios AS PersonaBeneficio
				WHERE persona_id = $persona_id AND activo = 1
				AND RIGHT(CONCAT('0000000000',PersonaBeneficio.nro_sucursal),10) = '$sucursal'
				AND RIGHT(CONCAT('000000000000000',PersonaBeneficio.nro_cta_bco),15) = '$cuenta';";
		$beneficios = $this->query($sql);
		return (isset($beneficios[0][0]['cant']) && $beneficios[0][0]['cant'] > 0 ? true : false);
	}


	function getBeneficioByNro($nro,$tipo=null,$ley=null,$sub=null){
		$conditions = array();
		$conditions['PersonaBeneficio.nro_beneficio'] = $nro;
		if(!empty($tipo)) $conditions['PersonaBeneficio.tipo'] = $tipo;
		if(!empty($ley)) $conditions['PersonaBeneficio.nro_ley'] = $ley;
		if(!empty($sub)) $conditions['PersonaBeneficio.sub_beneficio'] = $sub;
		$datos = $this->find('all',array('conditions' => $conditions));
		if(!empty($datos)) return $datos[0];
		else return null;
	}


	function getDatosBanco($id,$fechaCalculo = null){
		$datos = array();
		$beneficio = $this->read(null,$id);

		$fechaCalculo = (empty($fechaCalculo) ? date('Y-m-d') : $fechaCalculo);

		if(empty($beneficio['PersonaBeneficio']['banco_id'])) return null;

		$datos['banco_id'] = $beneficio['PersonaBeneficio']['banco_id'];
		$datos['banco'] = parent::getNombreBanco($beneficio['PersonaBeneficio']['banco_id']);
		$datos['sucursal'] = $beneficio['PersonaBeneficio']['nro_sucursal'];
		$datos['nro_cta_bco'] = $beneficio['PersonaBeneficio']['nro_cta_bco'];
		$datos['cbu'] = $beneficio['PersonaBeneficio']['cbu'];

		//PROVISIORIO AGREGO DATOS DE FECHA DE INGRESO, ANTIGUEDAD, BENEFICIO, LEGAJO, LEY
		$datos['fecha_ingreso'] = $beneficio['PersonaBeneficio']['fecha_ingreso'];
		$datos['antiguedad'] = parent::datediff('yyyy', $beneficio['PersonaBeneficio']['fecha_ingreso'], $fechaCalculo);
		$datos['legajo'] = $beneficio['PersonaBeneficio']['nro_legajo'];
		$datos['nro_beneficio'] = $beneficio['PersonaBeneficio']['nro_beneficio'];
		$datos['nro_ley'] = $beneficio['PersonaBeneficio']['nro_ley'];
		$datos['codigo_reparticion'] = $beneficio['PersonaBeneficio']['codigo_reparticion'];
                
                $datos['codigo_cjpc'] = '';
                if($beneficio['PersonaBeneficio']['codigo_beneficio'] == 'MUTUCORG7701') {
                    $codigoCJPC = parent::GlobalDato('entero_1', $beneficio['PersonaBeneficio']['codigo_beneficio']);
                    $codigoCJPC = substr(trim($codigoCJPC),0,3);
                    $datos['codigo_cjpc'] = substr(trim($codigoCJPC),0,3);                
                }
		return $datos;
	}


    function get_by_sucursal_cuenta($nro_cta_banco,$nro_sucursal,$banco_id,$codigo_beneficio = NULL){

        $sql = "select * from persona_beneficios PersonaBeneficio
                inner join personas Persona on Persona.id = PersonaBeneficio.persona_id
                inner join global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                left join socios Socio on Socio.persona_id = Persona.id
                where nro_cta_bco LIKE '%".trim($nro_cta_banco)."%'
                and nro_sucursal LIKE '%".trim($nro_sucursal)."%'
                -- and PersonaBeneficio.activo = 1
                ".(!empty($banco_id) ? " and PersonaBeneficio.banco_id = '$banco_id' " : "")."
                ".(!empty($codigo_beneficio) ? " and PersonaBeneficio.codigo_beneficio = '$codigo_beneficio' " : " ") ."
                order by PersonaBeneficio.id desc limit 1 ";
        $datos = $this->query($sql);
        return (!empty($datos) ? $datos[0] : NULL);

    }

    function get_cbu_by_sucursal_cuenta($persona_id,$nro_cta_banco,$nro_sucursal){
        if(empty($persona_id)) return NULL;
        $sql = "select cbu from persona_beneficios b
                where persona_id = $persona_id
                and nro_sucursal = '$nro_sucursal'
                and nro_cta_bco = '$nro_cta_banco'
                group by cbu limit 1";
        $datos = $this->query($sql);
        if(empty($datos)) return NULL;
        return $datos[0]['b']['cbu'];

    }

    public function setSueldoNetoDebitoBancario($id,$sueldoNeto,$debitoBancario){
        $beneficio = $this->read(null,$id);
        $beneficio['PersonaBeneficio']['sueldo_neto'] = $sueldoNeto;
        $beneficio['PersonaBeneficio']['debitos_bancarios'] = $debitoBancario;
        return parent::save($beneficio);
    }


    public function get_by_tipo_ley_numero_sub($tipo,$ley,$numero,$sub,$ndoc = NULL){
        $ndoc = str_pad($ndoc,8,'0',STR_PAD_LEFT);
        $sql = "select PersonaBeneficio.* from persona_beneficios PersonaBeneficio
                inner join personas Persona on Persona.id = PersonaBeneficio.persona_id
                where
                    PersonaBeneficio.codigo_beneficio = 'MUTUCORG7701'
                    and PersonaBeneficio.tipo = '$tipo'
                    and PersonaBeneficio.nro_ley = '$ley'
                    and PersonaBeneficio.nro_beneficio = '$numero'
                    and PersonaBeneficio.sub_beneficio = '$sub'
                    and Persona.documento = '$ndoc';";
//        debug($sql);
        $datos = $this->query($sql);
        if(empty($datos)) return NULL;
        return $datos;
    }
    
    function actualizarTarjetaDebito($data){
        
        $beneficio = $this->read(null,$data['PersonaBeneficio']['id']);
        
        if(empty($beneficio)){
            parent::notificar("Beneficio No Especificado");
            return false;
        }
        
        if(!empty($data['TarjetaDebito']['card_number'])){
            App::import('Vendor','crypt');
            $oCRYPT = new Crypt();
            
            $oTarjeta = new stdClass();
            $oTarjeta->card_holder_name = $data['TarjetaDebito']['card_holder_name'];
            $oTarjeta->card_number = $data['TarjetaDebito']['card_number'];
            $oTarjeta->card_expiration_month = $data['TarjetaDebito']['card_expiration_month'];
            $oTarjeta->card_expiration_year = $data['TarjetaDebito']['card_expiration_year'];
            
            $oTarjeta->security_code = $data['TarjetaDebito']['security_code'];
            
            $beneficio['PersonaBeneficio']['tarjeta_titular'] = $oTarjeta->card_holder_name;
            
            App::import('Helper', 'Util');
            $oUT = new UtilHelper();
            
            $beneficio['PersonaBeneficio']['tarjeta_numero'] = $oUT->maskLeft($oTarjeta->card_number);
            $beneficio['PersonaBeneficio']['tarjeta_debito'] = $oCRYPT->encrypt(serialize($oTarjeta));
            
            return $this->save($beneficio);
            
        }else{
            parent::notificar("Número de tarjeta no especificado");
            return false;
        }
        
    }
    

}
?>

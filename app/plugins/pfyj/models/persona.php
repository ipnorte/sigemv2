<?php
class Persona extends PfyjAppModel{
	
	var $name = 'Persona';
	var $hasOne = 'Socio';
	var $belongsTo = array('Localidad','Provincia');
        var $invalidFields = array();

	
//	var $belongsTo = array(
//			'Localidad' => array('className' => 'Localidad',
//								'foreignKey' => 'localidad_id',
//								'conditions' => '',
//								'fields' => '',
//								'order' => ''
//			),
//	);
	
    var $validate = array(
						'cuit_cuil' => array( 
    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
    									)
    					);	
	
    
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
    }
                                                    
    function save($data = null, $validate = true, $fieldList = array()){
    	return parent::save($data,$validate,$fieldList);
    }					
	
    
    function alta($data){
    	$persona_id = null;
    	$persona = $this->getByCUIT($data['Persona']['cuit_cuil']);
    	if(empty($persona)){
    		
    		//verificar si no existe el tipo y nro de documento
    		$persona = $this->getByTdocNdoc($data['Persona']['tipo_documento'],$data['Persona']['documento']);
    		if(empty($persona)){
    			if(!$this->save($data)) return $persona_id;
    			$persona_id = $this->getLastInsertID();		 
    		}else {
    			$persona['Persona']['cuit_cuil'] = $data['Persona']['cuit_cuil'];
    			$this->save($persona);
    			$persona_id = $persona['Persona']['id'];
    		}
    	}else{
    		$persona_id = $persona['Persona']['id'];
    	}
    	return $persona_id;
    }    
    
    
	function guardar($data){
                
		if($data['Persona']['localidad_id'] == 0){

			App::import('Model', 'Config.Localidad');
			$oLocalidad = new Localidad(null);
                        
                        $localidad = $oLocalidad->getByProvinciaAndCP($data['Persona']['provincia_id'], $data['Persona']['codigo_postal']);
			if(empty($localidad)){
                            $oLocalidad->save(array('Localidad' => array(
                                                                                                    'nombre' => $data['Persona']['localidad'],
                                                                                                    'cp' => $data['Persona']['codigo_postal'],
                                                                                                    'provincia_id' => (isset($data['Persona']['provincia_id']) ? $data['Persona']['provincia_id'] : 0),
                            )));
                            $data['Persona']['localidad_id'] = $this->Localidad->getLastInsertID();
                        }else{
                            $data['Persona']['localidad_id'] = $localidad['Localidad']['id'];
                        }
		}
//		debug($data);
//                exit;

//                $data['Persona']['localidad'] = $data['Persona']['localidadAproxima'];
		$data['Persona']['documento'] = parent::fill($data['Persona']['documento'],8);
		if(empty($data['Persona']['cuit_cuil'])) $data['Persona']['cuit_cuil'] = $data['Persona']['documento'];
//		if(MODULO_V1){
//			App::import('Model', 'V1.PersonaV1');
//			$this->PersonaV1 = new PersonaV1(null);			
//			if($this->PersonaV1->save($data)){
//				$idr = $this->PersonaV1->getLastInsertID();
//				if(!empty($idr))$data['Persona']['idr'] = $idr;
//			}
//		}

		if($data['Persona']['fallecida'] == 0) $data['Persona']['fecha_fallecimiento'] = null;
		
		if(isset($data['Persona']['apellido']))$data['Persona']['apellido'] = trim($data['Persona']['apellido']);
		if(isset($data['Persona']['nombre']))$data['Persona']['nombre'] = trim($data['Persona']['nombre']);
                

		
		if(!parent::save($data)) return false;
		
        if(MODULO_V1){
            App::import('Model', 'V1.PersonaV1');
            $oPV1 = new PersonaV1(null);
            if(!isset($data['Persona']['id'])) $data['Persona']['id'] = $this->getLastInsertID();
            $data['Persona']['idr'] = $oPV1->actualizarV1($data);
            if(!parent::save($data)) return false;
        }
		//actualizo el IDR
				
		
		//actualizo en la liquidacion socios
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		
		$socioLiquidaciones = $oLS->getLiquidacionesByDocumento($data['Persona']['tipo_documento'],$data['Persona']['documento']);
		if(!empty($socioLiquidaciones)):
			foreach($socioLiquidaciones as $idx => $liquidacion){
				$liquidacion['LiquidacionSocio']['apenom'] = $data['Persona']['apellido'].','.$data['Persona']['nombre'];
				$oLS->save($liquidacion);
			}
		endif;
		return true;
		
	}
	

	function getByIdr($idr){
		$persona = $this->findAllByIdr($idr);
		if(count($persona) == 0) return null;
		else $persona = $this->__armaDatos($persona[0]);
		return $persona;
	}
	
	function getPersona($id,$armaDatos = TRUE,$calculaSaldos = true){
		$persona = $this->read(null,$id);
		if($armaDatos){
			$persona = $this->__armaDatos($persona,$calculaSaldos);
		}		
		return $persona;
	}
	
	function __armaDatos($persona,$calculaSaldos = true){
		if(isset($persona['Persona'])){
			$glb = parent::getGlobalDato('concepto_1',$persona['Persona']['tipo_documento']);
			$persona['Persona']['tipo_documento_desc'] = $glb['GlobalDato']['concepto_1'];
			$persona['Persona']['apenom'] = $persona['Persona']['apellido'].", ".$persona['Persona']['nombre'];
			$persona['Persona']['tdoc_ndoc'] = $persona['Persona']['tipo_documento_desc']." ".$persona['Persona']['documento'];
			$persona['Persona']['estado_civil_desc'] = parent::GlobalDato('concepto_1',$persona['Persona']['estado_civil']);
			$persona['Persona']['tdoc_ndoc_apenom'] = $persona['Persona']['tdoc_ndoc']." - ".$persona['Persona']['apenom'];
			$persona['Persona']['domicilio'] = $this->getDomicilio($persona);
                        $persona['Persona']['domicilio_values'] = $this->getDomicilio($persona,true);
                        $persona['Persona']['datos_complementarios'] = $this->getDatosComplementarios($persona['Persona']['id'],false);
			
			$persona['Persona']['socio_nro'] = "";
			$persona['Persona']['socio'] = "";
			$persona['Persona']['socio_categoria'] = "";
			$persona['Persona']['socio_activo'] = "";
			$persona['Persona']['socio_status'] = "";
			$persona['Persona']['socio_ultima_calificacion'] = "";
			$persona['Persona']['socio_fecha_ultima_calificacion'] = "";
			$persona['Persona']['socio_resumen_calificacion'] = "";
                        $persona['Persona']['socio_resumen_situaciones'] = "";
                        $persona['Persona']['socio_situaciones'] = "";
			$persona['Persona']['socio_calificaciones'] = "";
			$persona['Persona']['socio_codigo_baja'] = "";
			$persona['Persona']['socio_baja'] = "";
			$persona['Persona']['socio_fecha_baja'] = "";
			$persona['Persona']['socio_fecha_alta'] = "";
			$persona['Persona']['socio_periodo_hasta'] = "";
                        $persona['Persona']['socio_historico_stop'] = NULL;
                        $persona['Persona']['socio_deuda_total_periodo'] = "0.00";
			$persona['Persona']['socio_deuda_total_vencida'] = "0.00";
                        $persona['Persona']['socio_deuda_total_avencer'] = "0.00";
                        $persona['Persona']['socio_historico_reversos'] = NULL;
                        
			if(isset($persona['Socio']) && !empty($persona['Socio']['id'])):
				$persona['Socio']['socio'] = "SOCIO #". $persona['Socio']['id'];
				$persona['Socio']['categoria_desc'] = parent::GlobalDato('concepto_1',$persona['Socio']['categoria']);
				$persona['Socio']['status'] = ($persona['Socio']['activo'] == 1 ? "VIGENTE" : "NO VIGENTE");
				$persona['Socio']['baja_desc'] = parent::GlobalDato('concepto_1',$persona['Socio']['codigo_baja']);
				$persona['Socio']['ultima_calificacion'] = parent::GlobalDato('concepto_1',$persona['Socio']['calificacion']);
				$persona['Socio']['fecha_ultima_calificacion'] = $persona['Socio']['fecha_calificacion'];
				App::import('Model', 'Pfyj.Socio');
				$oSOCIO = new Socio(null);
				$calificaciones = $oSOCIO->getResumenCalificaciones($persona['Socio']['id']);
				$persona['Socio']['resumen_calificaciones'] = "";
				if(!empty($calificaciones)):
					foreach($calificaciones as $calificacion):
						$persona['Socio']['resumen_calificaciones'] .= $calificacion['SocioCalificacion']['calificacion_desc']." (".$calificacion['SocioCalificacion']['cantidad'].") |";
					endforeach;
				endif;
				$persona['Socio']['calificaciones'] = $calificaciones;
                                
				
				$persona['Persona']['socio_nro'] = $persona['Socio']['id'];
				$persona['Persona']['socio'] = $persona['Socio']['socio'];
				$persona['Persona']['socio_categoria'] = $persona['Socio']['categoria_desc'];
				$persona['Persona']['socio_activo'] = $persona['Socio']['activo'];
				$persona['Persona']['socio_status'] = $persona['Socio']['status'];

				$persona['Persona']['socio_codigo_baja'] = $persona['Socio']['codigo_baja'];
				$persona['Persona']['socio_baja'] = $persona['Socio']['baja_desc'];
				$persona['Persona']['socio_fecha_baja'] = $persona['Socio']['fecha_baja'];
				$persona['Persona']['socio_fecha_alta'] = $persona['Socio']['fecha_alta'];
				$persona['Persona']['socio_codigo_ultima_calificacion'] = $persona['Socio']['calificacion'];
				$persona['Persona']['socio_ultima_calificacion'] = $persona['Socio']['ultima_calificacion'];
				$persona['Persona']['socio_fecha_ultima_calificacion'] = $persona['Socio']['fecha_ultima_calificacion'];
				$persona['Persona']['socio_resumen_calificacion'] = $persona['Socio']['resumen_calificaciones'];	
				$persona['Persona']['socio_calificaciones'] = $persona['Socio']['calificaciones'];
				$persona['Persona']['socio_periodo_hasta'] = $persona['Socio']['periodo_hasta'];	
                                
                                #SACO EL HISTORIAL DE STOP DEBIT
                                $persona['Persona']['socio_historico_stop'] = $oSOCIO->get_registros_stop_debit($persona['Socio']['id']);
                                // sacar estas lineas adrian 11/11/2020
                                // #SACO EL RESUMEN DE DEUDA
                                App::import('Model','mutual.OrdenDescuentoCuota');
								$oCUOTA = new OrdenDescuentoCuota();
								if($calculaSaldos){
									$persona['Persona']['socio_deuda_total_periodo'] = $oCUOTA->getTotalDeudaNoVencidaBySocio($persona['Socio']['id']);
									$persona['Persona']['socio_deuda_total_vencida'] = $oCUOTA->getTotalDeudaVencidaBySocio($persona['Socio']['id']);
									$persona['Persona']['socio_deuda_total_avencer'] = $oCUOTA->getTotalDeudaAVencerBySocio($persona['Socio']['id']);
									
									#REGISTRO DE REVERSOS
									App::import('Model','mutual.OrdenDescuentoCobroCuota');
									$oCOBRO_CUOTA = new OrdenDescuentoCobroCuota(); 
									$persona['Persona']['socio_historico_reversos'] = $oCOBRO_CUOTA->getTotalReversadoPorSocioPorPeriodo($persona['Socio']['id']);
								}

                                #REGISTRO DE SITUACIONES
                                $situaciones = $oCUOTA->getTotalOperacionesPorSituacionDeuda($persona['Socio']['id']);
				$persona['Persona']['socio_resumen_situaciones'] = "";
				if(!empty($situaciones)):
					foreach($situaciones as $situacion):
						$persona['Persona']['socio_resumen_situaciones'] .= $situacion['si']['concepto_1']." (".$situacion[0]['operaciones'].") |";
					endforeach;
				endif;
				$persona['Persona']['socio_situaciones'] = $situaciones;
                                
                                
                                
			endif;
			$persona['Persona']['edad'] = parent::datediff('yyyy', $persona['Persona']['fecha_nacimiento'], date('Y-m-d'));
			
			//SACAR SI TIENE ADICIONALES
			App::import('Model','pfyj.SocioAdicional');
			$oADICIONAL = new SocioAdicional();
			
			$persona['Persona']['adicionales'] = $oADICIONAL->getAdicionalesByPersonaID($persona['Persona']['id']);
			
                        // nuevos datos de contacto
                        if(empty($persona['Persona']['telefono_fijo_c'])){
                            $persona['Persona']['telefono_fijo_c'] = substr($persona['Persona']['telefono_fijo'],0,5);
                        }
                        if(empty($persona['Persona']['telefono_fijo_n'])){
                            $persona['Persona']['telefono_fijo_n'] = substr($persona['Persona']['telefono_fijo'],5,15);
                        }
                        if(empty($persona['Persona']['telefono_movil_c'])){
                            $persona['Persona']['telefono_movil_c'] = substr($persona['Persona']['telefono_movil'],0,5);
                        }
                        if(empty($persona['Persona']['telefono_movil_n'])){
                            $persona['Persona']['telefono_movil_n'] = substr($persona['Persona']['telefono_movil'],5,15);
                        }                        
                        if(empty($persona['Persona']['telefono_referencia_c'])){
                            $persona['Persona']['telefono_referencia_c'] = substr($persona['Persona']['telefono_referencia'],0,5);
                        }
                        if(empty($persona['Persona']['telefono_referencia_n'])){
                            $persona['Persona']['telefono_referencia_n'] = substr($persona['Persona']['telefono_referencia'],5,15);
                        } 
                        
                        #operaciones pendientes de aprobar
			App::import('Model','mutual.MutualProductoSolicitud');
			$oSOL = new MutualProductoSolicitud();                        
                        $operaciones_pendientes = $oSOL->getSolicitudesByVendedorGrillaModuloVentas(NULL,array(),NULL,NULL,NULL,NULL,$persona['Persona']['id'],FALSE,FALSE,array('MUTUESTA0000','MUTUESTA0014'));
                        $persona['Persona']['operaciones_pendientes_aprobar'] = $operaciones_pendientes;
		}
		return $persona;
	}
	
	function getDatoPersona($persona_id){
		$persona = $this->read(null,$persona_id);
		$persona = $this->__armaDatos($persona);
		$str = $persona['Persona']['apellido'].', '.$persona['Persona']['nombre'];
		$str .= ' ('.$persona['Persona']['tipo_documento_desc'] . ' ' . $persona['Persona']['documento'] .')';
		return $str;
	}
	
	function getSexo($persona_id){
		$persona = $this->read(null,$persona_id);
		return $persona['Persona']['sexo'];
	}
	
	function getFechaNac($persona_id){
		$persona = $this->read(null,$persona_id);
		return $persona['Persona']['fecha_nacimiento'];
	}	
	
	function getApeNomByDocumento($documento){
		$this->unbindModel(array('hasOne' => array('Socio'),'belongsTo' => array('Localidad')));
		$persona = $this->findAllByDocumento($documento);
		if(empty($persona)) return "";
		$persona = $persona[0];
		return $persona['Persona']['apellido'].', '.$persona['Persona']['nombre'];
	}
	
	function getIdByDocumento($documento){
		$this->unbindModel(array('hasOne' => array('Socio'),'belongsTo' => array('Localidad')));
		$persona = $this->findAllByDocumento($documento);
		if(empty($persona)) return 0;
		$persona = $persona[0];
		return $persona['Persona']['id'];
	}	
	

	function getApenom($persona_id,$conDocumento=true){
		$persona = $this->read(null,$persona_id);
		$persona = $this->__armaDatos($persona);
		$str = "";
		if($conDocumento) $str = $persona['Persona']['tipo_documento_desc']. ' ' . $persona['Persona']['documento'] .' - ';
		$str .= $persona['Persona']['apellido'].', '.$persona['Persona']['nombre'];
		return $str;
	}

	function getTdocNdoc($persona_id,$sep='-',$toArray=false){
		$persona = $this->read(null,$persona_id);
		$persona = $this->__armaDatos($persona);
		$str = $persona['Persona']['tipo_documento_desc']. $sep . $persona['Persona']['documento'];
		if($toArray) return array($persona['Persona']['tipo_documento_desc'],$persona['Persona']['documento']);
		return $str;
	}
	function getNdoc($persona_id){
            $persona = $this->read(null,$persona_id);
            if(!empty($persona) && isset($persona['Persona']['documento'])) return $persona['Persona']['documento'];
	}        
	
	function getByCUIT($cuit,$armaDatos=true){
		$persona = $this->find('all',array('conditions' => array('Persona.cuit_cuil' => $cuit),'limit' => 1));
		if(empty($persona)) return null;
		if($armaDatos) $persona = $this->__armaDatos($persona[0]);
		return $persona;
	
	}	
	
	function getDomicilio($persona,$toArray = false){
            
            if(!$toArray){
                
                $domicilio = $persona['Persona']['calle'] . " " .($persona['Persona']['numero_calle'] != 0 ? $persona['Persona']['numero_calle'] : '');
                
            }else{
                $domicilio = array(
                    'domicilio_calle' => $persona['Persona']['calle'],
                    'domicilio_numero' => ($persona['Persona']['numero_calle'] != 0 ? $persona['Persona']['numero_calle'] : ''),
                );
            }
            
        
            if(!empty($persona['Persona']['localidad'])){
                if(!$toArray){
                    $domicilio .= " - ";
                    $domicilio .= $persona['Persona']['localidad'] ." (CP ".$persona['Persona']['codigo_postal'].")";	                    
                }else{
                    $domicilio['domicilio_localidad'] = $persona['Persona']['localidad'];
                    $domicilio['domicilio_cp'] = $persona['Persona']['codigo_postal'];
                }
            } else if(!empty($persona['Localidad']['nombre'])){
                
                if(!$toArray){
                    $domicilio .= " - ";
                    $domicilio .= $persona['Localidad']['nombre'] ." (CP ".$persona['Localidad']['cp'].")";                    
                }else{
                    $domicilio['domicilio_localidad'] = $persona['Localidad']['nombre'];
                    $domicilio['domicilio_cp'] = $persona['Localidad']['cp'];             
                }
            }
        
//		if(!empty($persona['Localidad']['nombre'])):
//			$domicilio .= " - ";
//			$domicilio .= $persona['Localidad']['nombre'] ." (CP ".$persona['Localidad']['cp'].")";
//		else:
//			$domicilio .= " - ";
//			$domicilio .= $persona['Persona']['localidad'] ." (CP ".$persona['Persona']['codigo_postal'].")";	
//		endif;
		if(!empty($persona['Provincia']['nombre'])):
                    if(!$toArray){
                        $domicilio .= " - " . $persona['Provincia']['nombre'];
                    }else{
                        $domicilio['domicilio_provincia'] = $persona['Provincia']['nombre'];
                    }
			
		endif;
                
                
                
                
		return $domicilio;
	}
	
	
	function getDomicilioByPersonaId($persona_id,$toArray=false,$persona = null){
		if(empty($persona) && !empty($persona_id))$persona = $this->read(null,$persona_id);
		if(!$toArray)return $this->getDomicilio($persona);
		else return $this->getDomicilioByPersonaIdToArray($persona);
	}
	
	
	function getEdadByPersonaId($persona_id){
		$persona = $this->read('fecha_nacimiento',$persona_id);
		return parent::datediff('yyyy', $persona['Persona']['fecha_nacimiento'], date('Y-m-d'));
	}	
	
	function getDomicilioByPersonaIdToArray($persona){
		$domi = array();
		$domi['persona_id'] = $persona['Persona']['id'];
		$domi['calle'] = $persona['Persona']['calle'];
		$domi['numero_calle'] = $persona['Persona']['numero_calle'];
		$domi['piso'] = $persona['Persona']['piso'];
		$domi['dpto'] = $persona['Persona']['dpto'];
		$domi['barrio'] = $persona['Persona']['barrio'];
        
        if(!empty($persona['Persona']['localidad'])){
            $domi['localidad'] = $persona['Persona']['localidad'];
            $domi['cp'] = $persona['Persona']['codigo_postal'];
        }else if(!empty($persona['Localidad']['nombre'])){
			$domi['localidad'] = $persona['Localidad']['nombre'];
			$domi['cp'] = $persona['Localidad']['cp'];
        }
		
//		if(!empty($persona['Localidad']['nombre'])):
//		
//			$domi['localidad'] = $persona['Localidad']['nombre'];
//			$domi['cp'] = $persona['Localidad']['cp'];
//
//		else:	
//		
//			$domi['localidad'] = $persona['Persona']['localidad'];
//			$domi['cp'] = $persona['Persona']['codigo_postal'];
//		
//		endif;
		
		if(!empty($persona['Provincia']['nombre'])):
		
			$domi['provincia'] = $persona['Provincia']['nombre'];

		else:

			$domi['provincia'] = "";
		
		endif;
		
		return $domi;
		
	}
	
	
	
	
	function getPersonasByTipoListado($tipoListado=0){

		#SOCIOS
		if($tipoListado == 0){
			$sql = "SELECT	* 
					FROM personas as Persona 
					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)";
		}		
		#SOCIOS ACTIVOS Y ADHERENTES
		if($tipoListado == 1){
			$sql = "SELECT	* 
					FROM personas as Persona 
					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
					WHERE Socio.activo = 1 AND Socio.categoria IN ('MUTUCASOACTI','MUTUCASOADHE')";
		}
		#SOLO SOCIOS ACTIVOS
		if($tipoListado == 2){
			$sql = "SELECT	* 
					FROM personas as Persona 
					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
					WHERE Socio.activo = 1 AND Socio.categoria = 'MUTUCASOACTI'";			
		}
		#SOLO SOCIOS ADHERENTES
		if($tipoListado == 3){
			$sql = "SELECT	* 
					FROM personas as Persona 
					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
					WHERE Socio.activo = 1 AND Socio.categoria = 'MUTUCASOADHE'";			
		}
		#SOCIOS NO VIGENTES
		if($tipoListado == 4){
			$sql = "SELECT	* 
					FROM personas as Persona 
					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
					WHERE Socio.activo = 0";			
		}				
		#PERSONAS NO ASOCIADAS
		if($tipoListado == 5){
			$sql = "SELECT	* 
					FROM personas as Persona 
					WHERE Persona.id NOT IN (SELECT persona_id FROM socios)";			
		}
		$sql .= " ORDER BY Persona.apellido,Persona.nombre";
		$personas = $this->query($sql);
		return $personas;		
		
	}
	
	
	
	function getByTdocNdoc($tDoc,$nDoc){
		$nDoc = parent::fill($nDoc,8);
		$personaByTdocNdoc = $this->find('all',array('conditions' => array('Persona.tipo_documento' => $tDoc, 'Persona.documento' => $nDoc),'limit' => 1));
		if(empty($personaByTdocNdoc)) return null;
		$personaByTdocNdoc = $this->__armaDatos($personaByTdocNdoc[0]);
		return $personaByTdocNdoc;
	}
        
	function getByNdoc($nDoc,$armaDatos=TRUE){
            $nDoc = parent::fill($nDoc,8);
            $personaByTdocNdoc = $this->find('all',array('conditions' => array('Persona.documento' => $nDoc),'limit' => 1));
            if(empty($personaByTdocNdoc)) return null;
            if($armaDatos){
                $personaByTdocNdoc = $this->__armaDatos($personaByTdocNdoc[0]);
            }            
            return $personaByTdocNdoc;
	}        

	
	function getPersonasByBusquedaAvanzada($params,$limit = 50){


		$tipoDocumento = $params['tipo_documento'];
		$documento = $params['nro_documento'];
		$apellido = $params['apellido'];
		$nombre = $params['nombre'];
		$nroSocio = (isset($params['nro_socio']) ? $params['nro_socio'] : null);
		$organismo = $params['busqueda_avanzada_by_beneficio']['organismo'];
		$nroLey = $params['busqueda_avanzada_by_beneficio']['nro_ley'];
		$tipo = $params['busqueda_avanzada_by_beneficio']['tipo'];
		$nroBeneficio = $params['busqueda_avanzada_by_beneficio']['nro_beneficio'];
		$subBeneficio = $params['busqueda_avanzada_by_beneficio']['sub_beneficio'];
		$cbu = $params['busqueda_avanzada_by_beneficio']['cbu'];
		
		$sql = "SELECT	* 
				FROM personas AS Persona 
				INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
				INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.persona_id = Persona.id)
				WHERE
				Persona.tipo_documento LIKE '$tipoDocumento%'
				AND Persona.documento LIKE '$documento%' 
				AND Persona.apellido LIKE '$apellido%' 
				AND Persona.nombre LIKE $nombre'%' 
				AND Socio.id LIKE '$nroSocio%'
				AND PersonaBeneficio.codigo_beneficio LIKE '%$organismo%' 
				AND IFNULL(PersonaBeneficio.nro_ley,'') LIKE '$nroLey%'
				AND IFNULL(PersonaBeneficio.tipo,'') LIKE '$tipo%'
				AND IFNULL(PersonaBeneficio.nro_beneficio,'') LIKE '%$nroBeneficio%'
				AND IFNULL(PersonaBeneficio.sub_beneficio,'') LIKE '$subBeneficio%'
				AND IFNULL(PersonaBeneficio.cbu,'') LIKE '%$cbu%'
				ORDER BY Persona.apellido,Persona.nombre
				LIMIT $limit
		";
		
		
		$registros = $this->query($sql);
		
		if(!empty($registros)):
			App::import('Model', 'pfyj.PersonaBeneficio');
			$oBENEFICIO = new PersonaBeneficio(null);
			foreach($registros as $idx => $registro):
				$registros[$idx] = $oBENEFICIO->armaDatos($registro);
			endforeach;
		endif;
		return $registros;
	}
	
	
	function getMediosContacto($persona_id,$toArray = false){
		$contacto = null;
		$persona = $this->read(null,$persona_id);
		if(!$toArray){
			if(!empty($persona['Persona']['telefono_fijo'])){
				$contacto .= "TEL: " . $persona['Persona']['telefono_fijo'];
			}
			if(!empty($persona['Persona']['telefono_movil'])){
				$contacto .= " | CEL: " . $persona['Persona']['telefono_movil'];
			}
			if(!empty($persona['Persona']['telefono_referencia'])){
				$contacto .= " | OTRO: " . $persona['Persona']['telefono_referencia'];
				if(!empty($persona['Persona']['persona_referencia'])){
					$contacto .= " (Ref: ".$persona['Persona']['persona_referencia'].")";
				}
			}
			if(!empty($persona['Persona']['e_mail'])){
				$contacto .= " | EMAIL: " . $persona['Persona']['e_mail'];
			}
	
		}else{
			$contacto = array();
			$contacto['telefono_fijo'] = $persona['Persona']['telefono_fijo'];
			$contacto['telefono_movil'] = $persona['Persona']['telefono_movil'];
			$contacto['telefono_referencia'] = $persona['Persona']['telefono_referencia'];
			$contacto['persona_referencia'] = $persona['Persona']['persona_referencia'];
			$contacto['e_mail'] = $persona['Persona']['e_mail'];
		}
		return $contacto;
	}
	
	
	function getDatosComplementarios($persona_id,$toArray=false,$fechaCalculoEdad=null){
		$datos = null;
		$fechaCalculoEdad = (empty($fechaCalculoEdad) ? date('Y-m-d') : $fechaCalculoEdad);
		$persona = $this->read(null,$persona_id);
		if(!$toArray){
			$datos = "EST. CIVIL: " . parent::GlobalDato("concepto_1", $persona['Persona']['estado_civil']);
			$datos .= " | SEXO: ".$persona['Persona']['sexo'];
			$datos .= " | NACIMIENTO: " . date('d-m-Y',strtotime($persona['Persona']['fecha_nacimiento']));
			$datos .= " | EDAD: " . parent::datediff('yyyy', $persona['Persona']['fecha_nacimiento'], $fechaCalculoEdad);
			$datos .= " | CUIT/L: ".$persona['Persona']['cuit_cuil'];
		}else{
			$datos = array();
			$datos['estado_civil'] = parent::GlobalDato("concepto_1", $persona['Persona']['estado_civil']);
			$datos['sexo'] = $persona['Persona']['sexo'];
			$datos['fecha_nacimiento'] = date('d-m-Y',strtotime($persona['Persona']['fecha_nacimiento']));
			$datos['edad'] = parent::datediff('yyyy', $persona['Persona']['fecha_nacimiento'], $fechaCalculoEdad);
			$datos['cuit_cuil'] = $persona['Persona']['cuit_cuil'];
		}
		return $datos;
	}	

        
//        function validar_cuit_existente($cuit){
//            if(empty($cuit)){
//                parent::notificar("NO SE INDICA EL CUIT");
//                return false;                
//            }
//            $count = $this->find('count',array('conditions' => array('Persona.cuit_cuil' => $cuit)));
//            if($count > 0){
//                parent::notificar("EL CUIT $cuit YA EXISTE");
//                return false;
//            }
//        }
        
        function validar_datos_personales($persona){
            
//            debug($persona);
//            exit;
            
            
            $this->invalidFields = array();

            $VALIDADO = TRUE;
            if(empty($persona['cuit_cuil']) && (empty($persona['id']) || !isset($persona['id']))){
                parent::notificar("El CUIT/CUIL es requerido");
                array_push($this->invalidFields, 'cuit_cuil');
                $VALIDADO = FALSE; 
                return $VALIDADO;
            }
            $count = $this->find('count',array('conditions' => array('Persona.cuit_cuil' => $persona['cuit_cuil'])));
            if($count > 0 && (empty($persona['id']) || !isset($persona['id']))){
                parent::notificar("EL CUIT ".$persona['cuit_cuil']." YA EXISTE");
                $VALIDADO = FALSE;
            }
            if((empty($persona['id']) || !isset($persona['id']))){
                $count = $this->find('count',array('conditions' => array('Persona.documento' => substr($persona['cuit_cuil'],2,8))));
                if($count > 0){
                    parent::notificar("EL DOCUMENTO ".substr($persona['cuit_cuil'],2,8)." YA EXISTE");
                    $VALIDADO = FALSE;
                }
            }
            
            App::import('Helper', 'Util');
            $oUT = new UtilHelper();

//            if(!$oUT->validar_cuit($persona['cuit_cuil']) && (empty($persona['id']) || !isset($persona['id']))){
//                array_push($this->invalidFields, 'cuit_cuil');
//                parent::notificar("El CUIT/CUIL proporcionado es inválido.");
//                $VALIDADO = FALSE;                 
//            }
            if(!$oUT->validar_cuit($persona['cuit_cuil'])){
                array_push($this->invalidFields, 'cuit_cuil');
                parent::notificar("El CUIT/CUIL proporcionado es inválido.");
                $VALIDADO = FALSE;                 
            }            
            $persona['documento'] = substr($persona['cuit_cuil'],2,8);
            
            
            if(empty($persona['apellido']) && (empty($persona['id']) || !isset($persona['id']))){
                array_push($this->invalidFields, 'apellido');
                parent::notificar("El apellido es requerido");
                $VALIDADO = FALSE;                
            }
            if(empty($persona['nombre']) && (empty($persona['id']) || !isset($persona['id']))){
                array_push($this->invalidFields, 'nombre');
                parent::notificar("El nombre es requerido");
                $VALIDADO = FALSE;                
            }
            
            if(is_array($persona['fecha_nacimiento'])) $persona['fecha_nacimiento'] = parent::armaFecha($persona['fecha_nacimiento']);
            $dif = parent::datediff("yyyy", $persona['fecha_nacimiento'],date('Y-m-d'));
            if($dif < 18){
                array_push($this->invalidFields, 'nombre');
                parent::notificar("La persona no puede ser menor de 18 años.");
                $VALIDADO = FALSE;                 
            }
            if(empty($persona['calle'])){
                array_push($this->invalidFields, 'calle');
                parent::notificar("El nombre de calle es requerido");
                $VALIDADO = FALSE;                
            }
            if($persona['numero_calle'] == ""){
                array_push($this->invalidFields, 'numero_calle');
                parent::notificar("El número de calle es requerido");
                $VALIDADO = FALSE;                
            }
            if(empty($persona['localidad'])){
                array_push($this->invalidFields, 'localidad');
                parent::notificar("La localidad es requerida");
                $VALIDADO = FALSE;                
            }
            
            if($persona['localidad_id'] == 0){
//                array_push($this->invalidFields, 'localidad');
//                parent::notificar("La localidad es requerida");
//                $VALIDADO = FALSE; 
                $persona['localidad_id'] = NULL;
            }
            
            if(empty($persona['codigo_postal'])){
                array_push($this->invalidFields, 'codigo_postal');
                parent::notificar("El codigo postal es requerido");
                $VALIDADO = FALSE;                
            } 
            if(!isset($persona['provincia_id']) || empty($persona['provincia_id'])){
                array_push($this->invalidFields, 'provincia_id');
                parent::notificar("La provincia es requerida");
                $VALIDADO = FALSE;                
            }
            $persona['telefono_fijo_c'] = trim($persona['telefono_fijo_c']);
            $persona['telefono_fijo_n'] = trim($persona['telefono_fijo_n']);
            $persona['telefono_movil_c'] = trim($persona['telefono_movil_c']);
            $persona['telefono_movil_n'] = trim($persona['telefono_movil_n']);
            $persona['telefono_referencia_c'] = trim($persona['telefono_referencia_c']);
            $persona['telefono_referencia_n'] = trim($persona['telefono_referencia_n']);
            
//            if(!preg_match("/^[0-9]+$/",$persona['telefono_fijo_c']) || !preg_match("/^[0-9]+$/",$persona['telefono_fijo_n'])){
//                array_push($this->invalidFields, 'telefono_fijo_c');
//                array_push($this->invalidFields, 'telefono_fijo_n');
//                parent::notificar("El número de teléfono fijo contiene caracteres no validos. Requerida [0123456789].");
//                $VALIDADO = FALSE;                
//            }
            if(!preg_match("/^[0-9]+$/",$persona['telefono_movil_c']) || !preg_match("/^[0-9]+$/",$persona['telefono_movil_n'])){
                array_push($this->invalidFields, 'telefono_movil_c');
                array_push($this->invalidFields, 'telefono_movil_n');
                parent::notificar("El número de teléfono celular contiene caracteres no validos. Requerida [0123456789].");
                $VALIDADO = FALSE;                
            }
            if(!empty($persona['telefono_referencia_c']) || !empty($persona['telefono_referencia_n'])){
                if(!preg_match("/^[0-9]+$/",$persona['telefono_referencia_c']) || !preg_match("/^[0-9]+$/",$persona['telefono_referencia_n'])){
                    array_push($this->invalidFields, 'telefono_referencia_c');
                    array_push($this->invalidFields, 'telefono_referencia_n');
                    parent::notificar("El número de teléfono de referencia contiene caracteres no validos. Requerida [0123456789].");
                    $VALIDADO = FALSE;                
                }                
            }
//            if(empty(intval($persona['telefono_fijo_c'])) || empty(intval($persona['telefono_fijo_n']))){
//                array_push($this->invalidFields, 'telefono_fijo_c');
//                array_push($this->invalidFields, 'telefono_fijo_n');
//                parent::notificar("El número de teléfono fijo es incorrecto.");
//                $VALIDADO = FALSE;                
//            }
//            if(empty(intval($persona['telefono_movil_n']))|| empty(intval($persona['telefono_movil_n']))){
//                array_push($this->invalidFields, 'telefono_movil_c');
//                array_push($this->invalidFields, 'telefono_movil_n');
//                parent::notificar("El número de teléfono celular es incorrecto.");
//                $VALIDADO = FALSE;                
//            }             
            $email = $persona['e_mail'];
            if(!empty($persona['e_mail']) && !filter_var($email,FILTER_VALIDATE_EMAIL)){
                array_push($this->invalidFields, 'e_mail');
                parent::notificar("El formato de email indicado no es válido.");
                $VALIDADO = FALSE;                  
            }
            
            $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
            $MOD_NOSIS_SMS = (isset($INI_FILE['general']['nosis_validar_sms']) && $INI_FILE['general']['nosis_validar_sms'] == 1 ? TRUE : FALSE);        
            
            if($MOD_NOSIS_SMS){
                
                if($persona['celular_nosis_validado']){
                    $persona['celular_nosis_fecha_validacion'] = date('Y-m-d H:i:s');
                }
                
//                App::import('Model', 'Pfyj.Persona');
//                $oPERSONA = new Persona();         
//                $URL = trim($oPERSONA->GlobalDato("concepto_2","PERSNVID"));
//                $USER = trim($oPERSONA->GlobalDato("concepto_3","PERSNVID"));
//                $TOKEN = trim($oPERSONA->GlobalDato("concepto_4","PERSNVID"));
//                $GRUPO = trim($oPERSONA->GlobalDato("entero_1","PERSNVID"));
//                $ACTIVO = trim($oPERSONA->GlobalDato("logico_1","PERSNVID"));
//                if(!empty($URL) && $ACTIVO == "1"){
//                    
//                    if(empty($persona['celular_nosis_consulta_id']) && !$persona['celular_nosis_validado']){
//                        parent::notificar("El número de celular NO FUE VERIFICADO!.");
//                        $VALIDADO = FALSE; 
//                    }else if(!$persona['celular_nosis_validado']){
//                        App::import('Vendor','NosisVidApi',array('file' => 'nosis_vid_api.php'));
//                        $oNOSIS = new NosisVidApi($URL, $USER, $TOKEN, $GRUPO, $persona['documento']);
//                        $respuesta = $oNOSIS->evaluarTokenSMS($persona['celular_nosis_consulta_id'], $persona['celular_nosis_consulta_pin']);
////                        debug($respuesta);
//                        if(!empty($respuesta)){
//                            if($respuesta['Resultado']->Estado >= 400){
//                                parent::notificar("No se pudo verificar. " . $respuesta['Resultado']['Novedad']);
//                            }else{
//                                $TokenSms = $respuesta['Pedido']->TokenSms;
//                                $tokenValidado = $respuesta['Datos']['Sms']['Validado'];
//                                if(!$tokenValidado){
//                                    parent::notificar("Validación del Celular :: El Código $TokenSms fue " . $respuesta['Datos']['Sms']['Estado']);
//                                    $VALIDADO = FALSE;
//                                }else{
//                                    $persona['celular_nosis_validado'] = TRUE;
//                                    $persona['celular_nosis_fecha_validacion'] = date('Y-m-d H:i:s');
//                                }
//                            }
//                        }
//                    }                    
//                }                 
            }

            if($VALIDADO){
                $persona['telefono_fijo'] = $persona['telefono_fijo_c'].$persona['telefono_fijo_n'];
                $persona['telefono_movil'] = $persona['telefono_movil_c'].$persona['telefono_movil_n'];
                $persona['telefono_referencia'] = $persona['telefono_referencia_c'].$persona['telefono_referencia_n'];
            }
            return array('STATUS' => $VALIDADO,'PERSONA' => $persona);
            
        }
        
        
        function get_beneficios($id,$armaDatos = false,$soloActivos = true){
            App::import('Model', 'pfyj.PersonaBeneficio');
            $oBEN = new PersonaBeneficio(); 
            return $oBEN->beneficiosByPersona($id, $armaDatos, $soloActivos);
        }
        
        
}
?>
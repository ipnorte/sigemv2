<?php
class PersonaV1 extends V1AppModel{
	
	var $name = 'PersonaV1';
	var $primaryKey = 'id_persona';
	var $useTable = 'personas';
	
    var $validate = array(
						'cuit_cuil' => array( 
    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
    									)
    					);

    					
    function getPersona($id){
    	$persona = $this->read(null,$id);
    	$persona = $this->setDatosV2($persona);
    	return $persona;
    }					

    
    function setDatosV2($persona){
    	
    	if(empty($persona)) return $persona;
    	
    	$persona['PersonaV1']['id'] = 0;
    	$persona['PersonaV1']['numero_calle'] = $persona['PersonaV1']['nro_calle'];
    	$persona['PersonaV1']['tipo_documento'] = "PERSTPDC" . $persona['PersonaV1']['tipo_documento'];
    	$persona['PersonaV1']['documento'] = parent::fill($persona['PersonaV1']['documento'],8);
    	$persona['PersonaV1']['estado_civil'] = "PERSXXEC" . $persona['PersonaV1']['estado_civil'];
    	$persona['PersonaV1']['tipo_vivienda'] = "PERSTIVI" . $persona['PersonaV1']['tipo_vivienda'];
    	$persona['PersonaV1']['filial'] = "MUTUFILI" . (empty($persona['PersonaV1']['filial']) ? '0000' : $persona['PersonaV1']['filial']);
    	$persona['PersonaV1']['idr_tv2'] = $persona['PersonaV1']['id_persona'];
    	$persona['PersonaV1']['idr_fv2'] = 0;
    	$persona['PersonaV1']['user_created'] = $persona['PersonaV1']['usuario'];
    	$persona['PersonaV1']['created'] = $persona['PersonaV1']['fecha_ac'];
    	
    	$provincia = parent::getProvinciaByLetra($persona['PersonaV1']['codigo_provincia']);
    	if(!empty($provincia))$persona['PersonaV1']['provincia_id'] = $provincia['Provincia']['id'];
    	else $persona['PersonaV1']['provincia_id'] = 0;
    	
    	//verifico que no exista una persona en V2
		App::import('Model', 'Pfyj.Persona');
		$oPERSONAV2 = new Persona(null);
		$oPERSONAV2->bindModel(array('hasOne' => array('Socio')));
		$personaV2 = $oPERSONAV2->findAllByIdr($persona['PersonaV1']['id_persona']);
		
		if(empty($personaV2)){
			
			//buscar por tipo y por numero de documento
			$personaByTdocNdoc = $oPERSONAV2->getByTdocNdoc($persona['PersonaV1']['tipo_documento'],$persona['PersonaV1']['documento']);
			
			if(empty($personaByTdocNdoc)){

				$persona['PersonaV1']['idr'] = $persona['PersonaV1']['id_persona'];
				$personaV2Nueva = Set::extract("PersonaV1",$persona);
				$personaV2Nueva = Set::insert(array(),'Persona',$personaV2Nueva);
				if($oPERSONAV2->save($personaV2Nueva))$persona['PersonaV1']['idr_fv2'] = $oPERSONAV2->getLastInsertID();
				
			}else{
				
				$persona['PersonaV1']['idr_fv2'] = (!empty($personaByTdocNdoc) && isset($personaByTdocNdoc[0]['Persona']['id']) ? $personaByTdocNdoc[0]['Persona']['id'] : 0);
				
			}

		}else{
			$persona['PersonaV1']['idr_fv2'] = $personaV2[0]['Persona']['id'];
		
		}
		
    	return $persona;
    }
    
    function actualizarV1($personaV2){
    	
		$persona = $this->getByTdocNdoc($personaV2['Persona']['tipo_documento'],$personaV2['Persona']['documento']);
		if(!empty($persona) && empty($personaV2['Persona']['idr_persona'])){
			$personaV2['Persona']['idr_persona'] = $persona['PersonaV1']['id_persona'];
		}
		$datos = array('PersonaV1' => array(
			'id_persona' => $personaV2['Persona']['idr_persona'],
			'tipo_documento' => substr($personaV2['Persona']['tipo_documento'],8,4),
			'documento' => $personaV2['Persona']['documento'],
			'apellido' => $personaV2['Persona']['apellido'],
			'nombre' => $personaV2['Persona']['nombre'],
			'calle' => $personaV2['Persona']['calle'],
			'nro_calle' => $personaV2['Persona']['numero_calle'],
			'piso' => $personaV2['Persona']['piso'],
			'dpto' => $personaV2['Persona']['dpto'],
			'barrio' => $personaV2['Persona']['barrio'],
			'codigo_postal' => $personaV2['Persona']['codigo_postal'],
			'localidad' => $personaV2['Persona']['localidad'],
			'telefono_fijo' => $personaV2['Persona']['telefono_fijo'],
			'telefono_movil' => $personaV2['Persona']['telefono_movil'],
			'telefono_referencia' => $personaV2['Persona']['telefono_referencia'],
			'persona_referencia' => $personaV2['Persona']['persona_referencia'],
			'e_mail' => $personaV2['Persona']['e_mail'],
			'cuit_cuil' => $personaV2['Persona']['cuit_cuil'],
			'estado_civil' => substr($personaV2['Persona']['estado_civil'],8,4),
			'nombre_conyuge' => $personaV2['Persona']['nombre_conyuge'],
			'sexo' => $personaV2['Persona']['sexo'],
			'fecha_nacimiento' => $personaV2['Persona']['fecha_nacimiento']['year'].'-'.$personaV2['Persona']['fecha_nacimiento']['month'].'-'.$personaV2['Persona']['fecha_nacimiento']['day'],
			'idr' => $personaV2['Persona']['id'],
		));    	
    	if(!parent::save($datos)) return 0;
		return $this->getLastInsertID();    	
    }
    
    
	function save($data = null, $validate = true, $fieldList = array()){
//		debug($data);
		
		$persona = $this->getByTdocNdoc($data['Persona']['tipo_documento'],$data['Persona']['documento']);
		if(!empty($persona) && empty($persona['Persona']['idr_persona'])){
			$data['Persona']['idr_persona'] = $persona['PersonaV1']['id_persona'];
		}		
		
		$datos = array('PersonaV1' => array(
			'id_persona' => $data['Persona']['idr_persona'],
			'tipo_documento' => substr($data['Persona']['tipo_documento'],8,4),
			'documento' => $data['Persona']['documento'],
			'apellido' => $data['Persona']['apellido'],
			'nombre' => $data['Persona']['nombre'],
			'calle' => $data['Persona']['calle'],
			'nro_calle' => $data['Persona']['numero_calle'],
			'piso' => $data['Persona']['piso'],
			'dpto' => $data['Persona']['dpto'],
			'barrio' => $data['Persona']['barrio'],
			'codigo_postal' => $data['Persona']['codigo_postal'],
			'localidad' => $data['Persona']['localidad'],
			'telefono_fijo' => $data['Persona']['telefono_fijo'],
			'telefono_movil' => $data['Persona']['telefono_movil'],
			'telefono_referencia' => $data['Persona']['telefono_referencia'],
			'persona_referencia' => $data['Persona']['persona_referencia'],
			'e_mail' => $data['Persona']['e_mail'],
			'cuit_cuil' => $data['Persona']['cuit_cuil'],
			'estado_civil' => substr($data['Persona']['estado_civil'],8,4),
			'nombre_conyuge' => $data['Persona']['nombre_conyuge'],
			'sexo' => $data['Persona']['sexo'],
			'fecha_nacimiento' => $data['Persona']['fecha_nacimiento']['year'].'-'.$data['Persona']['fecha_nacimiento']['month'].'-'.$data['Persona']['fecha_nacimiento']['day'],
		));
//		debug($datos);
		return parent::save($datos);
	}
	
	
	
	function getByTdocNdoc($tDoc,$nDoc){
		$tDoc = substr(trim($tDoc),-4); //TOMO LOS ULTIMOS 4 CARACTERES
		$persona = $this->find('all',array('conditions' => array('PersonaV1.tipo_documento' => $tDoc, 'PersonaV1.documento' => $nDoc)));
		return (!empty($persona) ? $persona[0] : null);
	}
	
	
}
?>
<?php 

class SocioAdicional extends PfyjAppModel{
	
	var $name = "SocioAdicional";
	var $belongsTo = array('Localidad','Provincia');
	
	function guardar($datos){
		if($this->isExistsByTdocNdoc($datos['SocioAdicional']['tipo_documento'],$datos['SocioAdicional']['documento'])){
			parent::notificar("YA EXISTE UN ADICIONAL PARA EL TIPO Y NRO DE DOCUMENTO INDICADO");
			return false;
		}
		return parent::save($datos);
	}
	
	
	function isExistsByTdocNdoc($tdoc,$ndoc){
		$conditions = array();
		$conditions['SocioAdicional.tipo_documento'] = $tdoc;
		$conditions['SocioAdicional.documento'] = parent::fill($ndoc,8);
		$count = $this->find('count',array('conditions' => $conditions));
		return ($count != 0 ? true : false);
	}
	
	function getByTdocNdoc($tdoc,$ndoc){
		$conditions = array();
		$conditions['SocioAdicional.tipo_documento'] = $tdoc;
		$conditions['SocioAdicional.documento'] = parent::fill($ndoc,8);
		$datos = $this->find('all',array('conditions' => $conditions));
		return (!empty($datos) ? $datos[0] : null);
	}	
	
	/**
	 * Borra un adicional. Controla que no este
	 * asociado a ningun servicio del socio titular
	 * @param $id
	 */
	function borrar($id){
		
		App::import('Model','mutual.MutualServicioSolicitudAdicional');
		$oSERV_ADIC = new MutualServicioSolicitudAdicional();
		
		if($oSERV_ADIC->checkAdicional($id)) return false;
		else return parent::del($id);
		
//		return parent::del($id);
	}
	
	/**
	 * Carga un adicional con su informacion complementaria
	 * @param $id
	 */
	function getAdicional($id){
		$adicional = $this->read(null,$id);
		if(empty($adicional)) return null;
		else return $this->armaDatos($adicional);
	}
	
	/**
	 * Carga los adicionales asociados a una persona
	 * @param int $persona_id
	 * @return NULL|array
	 */
	function getAdicionalesByPersonaID($persona_id){
		$adicionales = $this->find('all',array('conditions' => array('SocioAdicional.persona_id' => $persona_id),'order' => array('SocioAdicional.apellido,SocioAdicional.nombre')));
		if(empty($adicionales)) return null;
		foreach($adicionales as $idx => $adicional):
			$adicionales[$idx] = $this->armaDatos($adicional);
		endforeach;
		return $adicionales;
	}
	
	/**
	 * Arma los datos complementarios de un adicional
	 * @param $adicional
	 */
	function armaDatos($adicional){
		
		$adicional['SocioAdicional']['tipo_documento_desc'] = parent::GlobalDato('concepto_1',$adicional['SocioAdicional']['tipo_documento']);
		$adicional['SocioAdicional']['apenom'] = $adicional['SocioAdicional']['apellido'].", ".$adicional['SocioAdicional']['nombre'];
		$adicional['SocioAdicional']['tdoc_ndoc'] = $adicional['SocioAdicional']['tipo_documento_desc']." ".$adicional['SocioAdicional']['documento'];
		$adicional['SocioAdicional']['vinculo_desc'] = parent::GlobalDato('concepto_1',$adicional['SocioAdicional']['vinculo']);
		
		$adicional['SocioAdicional']['tdoc_ndoc_apenom'] = $adicional['SocioAdicional']['tdoc_ndoc']." - ".$adicional['SocioAdicional']['apenom'];
		
		$domicilio = $adicional['SocioAdicional']['calle'] . " " .($adicional['SocioAdicional']['numero_calle'] != 0 ? $adicional['SocioAdicional']['numero_calle'] : '');
		$domicilio .= " - " . $adicional['SocioAdicional']['localidad'] ." (CP ".$adicional['SocioAdicional']['codigo_postal'].")";
		$adicional['SocioAdicional']['provincia'] = "";
		if(!empty($adicional['Provincia']['nombre'])){
			$adicional['SocioAdicional']['provincia'] = $adicional['Provincia']['nombre'];
			$domicilio .= " - " .$adicional['Provincia']['nombre'];
		}
		
		$adicional['SocioAdicional']['domicilio'] = $domicilio;
		$adicional['SocioAdicional']['edad'] = parent::datediff('yyyy', $adicional['SocioAdicional']['fecha_nacimiento'], date('Y-m-d'));
		
		return $adicional;
	}
	
	
}

?>
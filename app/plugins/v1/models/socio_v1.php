<?php
class SocioV1 extends V1AppModel{
	
	var $name = 'SocioV1';
	var $primaryKey = 'id_socio';
	var $useTable = 'socios';

	
	function guardar($data){
		
		//busco el IDR de la persona
		App::import('Model', 'Pfyj.Persona');
		$this->Persona = new Persona(null);		
		$idrPersona = $this->Persona->read('idr',$data['Socio']['persona_id']);
		
		$datos = array('SocioV1' => array(
			'id_socio' => $idrPersona['Persona']['idr'],
			'nro_socio' => $data['Socio']['id'],
			'fecha_alta' => $data['Socio']['fecha_alta'],
			'fecha_ac' => date('Y-m-d'),
			'activo' => $data['Socio']['activo'],
			'observaciones' => '*** ACTIVADO POR V2 ***',
			'id_beneficio' => $data['Socio']['persona_beneficio_id'],
			'anio_ini' => substr($data['Socio']['periodo_ini'],0,4),
			'periodo_ini' => substr($data['Socio']['periodo_ini'],4,2),
			'tipo_novedad' => 'A',
			'cuota_social' => $data['Socio']['cuota_social'],
		
		));
		return parent::save($datos);
	}
	
	
	function actualizarValor($id,$campo,$valor){
		
	}
	
}
?>
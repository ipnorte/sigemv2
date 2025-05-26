<?php
class SocioHistorico extends PfyjAppModel{
	var $name = 'SocioHistorico';
	
	function grabarHistorial($socio_id){
		App::import('Model','Pfyj.Socio');
		$oSocio = new Socio();
		$socio = $oSocio->read(null,$socio_id);
		$data = array('SocioHistorico' => array(
			'socio_id' => $socio['Socio']['id'],
			'socio_solicitud_id' => $socio['Socio']['socio_solicitud_id'],
			'persona_beneficio_id' => $socio['Socio']['persona_beneficio_id'],
			'periodo_ini' => $socio['Socio']['periodo_ini'],
			'periodicidad' => $socio['Socio']['periodicidad'],
			'activo' => $socio['Socio']['activo'],
			'fecha_alta' => $socio['Socio']['fecha_alta'],
			'orden_descuento_id' => $socio['Socio']['orden_descuento_id'],
			'calificacion' => $socio['Socio']['calificacion'],
			'fecha_calificacion' => $socio['Socio']['fecha_calificacion'],
			'codigo_baja' => $socio['Socio']['codigo_baja'],
			'fecha_baja' => $socio['Socio']['fecha_baja'],
			'observaciones' => $socio['Socio']['observaciones'],
		));
		return parent::save($data);
	}
	
}
<?php
class SolicitudCancelaciones extends V1AppModel{
	
	var $name = 'SolicitudCancelaciones';
	var $primaryKey = 'id_cancelacion';
	var $useTable = 'solicitud_cancelaciones';
	
	
	function totalCanceladoBySolicitud($nro_solicitud){
		$total = $this->find('all',array('conditions' => array('SolicitudCancelaciones.nro_solicitud' => $nro_solicitud),'fields' => array("SUM(SolicitudCancelaciones.importe_deuda_cancela) as importe_deuda_cancela"),'group' => array('SolicitudCancelaciones.nro_solicitud')));
		return (isset($total[0][0]['importe_deuda_cancela']) ? $total[0][0]['importe_deuda_cancela'] : 0);
	}
	
	function bySolicitud($nro_solicitud){
		$cancelaciones = $this->find('all',array('conditions' => array('SolicitudCancelaciones.nro_solicitud' => $nro_solicitud),'order' => array('SolicitudCancelaciones.codigo_item')));
		return $this->armaDatos($cancelaciones);
	}
	
	function armaDatos($resultados){
		
		App::import('Model', 'V1.OrganismosCodigos');
		$oCodOrg = new OrganismosCodigos(null);	
		
		App::import('Model', 'V1.SolicitudCancelacionDetalle');
		$oCD = new SolicitudCancelacionDetalle(null);		
		
		$deudaTotalCancela = 0;

		foreach($resultados as $clave => $valor){
			
			$deudaTotalCancela += $valor['SolicitudCancelaciones']['importe_deuda_cancela'];
			
			$codigo = $oCodOrg->getCodigo($valor['SolicitudCancelaciones']['codigo_item']);
			$resultados[$clave]['OrganismosCodigos']['descripcion'] = $codigo['OrganismosCodigos']['descripcion'];
			$resultados[$clave]['SolicitudCancelaciones']['total_deuda_cancela'] = $deudaTotalCancela;
			
			//busco el detalle de la cancelacion
			$detalle = $oCD->getDetalleByCancelacion($valor['SolicitudCancelaciones']['id_cancelacion']);
			$resultados[$clave]['SolicitudCancelacionDetalle'] = $detalle;
			

		}
		
		return $resultados;
	}
	
	
}
?>
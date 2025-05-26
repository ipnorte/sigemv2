<?php
class SolicitudCuponesAnses extends V1AppModel{
	
	var $name = 'SolicitudCuponesAnses';
	var $useTable = 'solicitud_cupones_anses';

	
	function cuponesBySolicitud($nro_solicitud){
		$sql = "select * from solicitud_cupones_anses where nro_solicitud = $nro_solicitud";
		$cupones = parent::query($sql);
		return $cupones;
	}
	
}
?>
<?php
class SolicitudCancelacionDetalle extends V1AppModel{
	
	var $name = 'SolicitudCancelacionDetalle';
	var $primaryKey = 'id';
	var $useTable = 'solicitud_cancelacion_detalle';

	function getDetalleByCancelacion($id_cancelacion){
		$detalle = $this->findAllByIdCancelacion($id_cancelacion);
		foreach($detalle as $idx => $cancelacion){
			$detalle[$idx] = $this->__armaDatos($cancelacion);
		}
		return $detalle;
	}
	
	
	function __armaDatos($detalle){
		$glb = parent::getGlobal('concepto','TICA'.$detalle['SolicitudCancelacionDetalle']['tipo_liquidacion']);
		$detalle['SolicitudCancelacionDetalle']['tipo_liquidacion_desc'] = $glb['Tglobal']['concepto'];
		$detalle['SolicitudCancelacionDetalle']['tipo_pago_desc'] = ($detalle['SolicitudCancelacionDetalle']['tipo_pago'] == '0001' ? 'EFECTIVO' : ($detalle['SolicitudCancelacionDetalle']['tipo_pago'] == '0002' ? 'CHEQUE' : ($detalle['SolicitudCancelacionDetalle']['tipo_pago'] == '0003' ? 'DEPOSITO' : '')));;
		
				
		App::import('model','Proveedores.Proveedor');
		$oProV2 = new Proveedor(null);
		
		$proveedor = $oProV2->findAllByCuit($detalle['SolicitudCancelacionDetalle']['codigo_proveedor']);
		$detalle['SolicitudCancelacionDetalle']['proveedor'] = $proveedor[0]['Proveedor']['razon_social'];
		
		$cuotaIni = substr($detalle['SolicitudCancelacionDetalle']['cuota_desde'],0,2);
		$cuotaFin = substr($detalle['SolicitudCancelacionDetalle']['cuota_hasta'],0,2);
		$cuotaTot = substr($detalle['SolicitudCancelacionDetalle']['cuota_desde'],2,2);

		$detalle['SolicitudCancelacionDetalle']['cuotas'] = $cuotaIni." - ".$cuotaFin ."/".$cuotaTot;
		
		$banco = parent::getBanco('banco',$detalle['SolicitudCancelacionDetalle']['codigo_banco']);
		$detalle['SolicitudCancelacionDetalle']['banco'] = $banco['BancoV1']['banco'];
		
		//armo el opeban
		$opeban = $detalle['SolicitudCancelacionDetalle']['tipo_pago_desc'];
		if($detalle['SolicitudCancelacionDetalle']['codigo_banco'] != ''){
			$opeban .= " " . $detalle['SolicitudCancelacionDetalle']['banco'];
			$opeban .= ($detalle['SolicitudCancelacionDetalle']['nro_operacion_pago'] != '' ? "<br>Nro: " . $detalle['SolicitudCancelacionDetalle']['nro_operacion_pago'] : '');
		}
		$detalle['SolicitudCancelacionDetalle']['detalle_ope_ban'] = $opeban;
		return $detalle;
	}
	
}
?>
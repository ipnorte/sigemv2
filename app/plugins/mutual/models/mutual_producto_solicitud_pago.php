<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class MutualProductoSolicitudPago extends MutualAppModel{
	var $name = 'MutualProductoSolicitudPago';
	
	function grabarPago($datos,$mutual_producto_solicitud_id){
		//verifico el total de pagos que sea el total de la orden
		$pagos = $this->getPagosBySolicitud($mutual_producto_solicitud_id);
		$acumPagos = 0;
		if(count($pagos)!=0):
			foreach($pagos as $pago){
				$acumPagos += $pago['MutualProductoSolicitudPago']['importe'];
			}
		endif;
		App::import('Model','Mutual.MutualProductoSolicitud');
		$oMPS = new MutualProductoSolicitud();
		$importeSolicitud = $oMPS->getImporteTotal($mutual_producto_solicitud_id);
		
		if($datos['MutualProductoSolicitudPago']['forma_pago'] != 'MUTUFPAG0002'){
			$datos['MutualProductoSolicitudPago']['banco_id'] = '';
			$datos['MutualProductoSolicitudPago']['nro_comprobante'] = '';
		}
		
		if(!empty($datos['MutualProductoSolicitudPago']['banco_id']))$datos['MutualProductoSolicitudPago']['forma_pago'] = 'MUTUFPAG0002';
		
		if($importeSolicitud > $acumPagos) return parent::save($datos);
		else return false;
	}
	
	function getPagosBySolicitud($mutual_producto_solicitud_id){
		$pagos = $this->find('all',array('conditions' => array('MutualProductoSolicitudPago.mutual_producto_solicitud_id' => $mutual_producto_solicitud_id)));
		if(empty($pagos)) return null;
		foreach($pagos as $idx => $pago){
			$pago = $this->armaDatos($pago);
			$pagos[$idx] = $pago;
		}
		return $pagos;
	}
	
	function armaDatos($pago){
		$glb = parent::getGlobalDato('concepto_1',$pago['MutualProductoSolicitudPago']['forma_pago']);
		$banco = parent::getBanco($pago['MutualProductoSolicitudPago']['banco_id']);
		
		$pago['MutualProductoSolicitudPago']['banco'] = $banco['Banco']['nombre'];
        $pago['MutualProductoSolicitudPago']['forma_pago_desc'] = $glb['GlobalDato']['concepto_1'] . (!empty($pago['MutualProductoSolicitudPago']['banco']) ? " - " . $pago['MutualProductoSolicitudPago']['banco'] : "");
		return $pago;
	}
	
}
?>
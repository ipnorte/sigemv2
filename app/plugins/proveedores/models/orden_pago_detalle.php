<?php
class OrdenPagoDetalle extends ProveedoresAppModel{
	var $name = 'OrdenPagoDetalle';
	
	function getOrdenPagoDetalle($id=null){
            $aOrdenPagoDetalle = array();

            if(empty($id)) return $aOrdenPagoDetalle;

            $aOrdenPagoDetalle = $this->find('all',array('conditions' => array('OrdenPagoDetalle.orden_pago_id' => $id)));

            $aOrdenPagoDetalle = $this->armaDatos($aOrdenPagoDetalle);

            $aOrdenPagoDetalle = Set::extract("{n}.OrdenPagoDetalle",$aOrdenPagoDetalle);
            return $aOrdenPagoDetalle;
	}
	
	
	
	function getPagoDetalleCuenta($proveedor_id){
            $anticipos = $this->find('all',array('conditions' => array('OrdenPagoDetalle.proveedor_id' => $proveedor_id, 'OrdenPagoDetalle.tipo_pago' => 'AN', 'importe >' => 0)));

            if(empty($anticipos)) return array();

            $anticipos = $this->armaDatos($anticipos);

            $anticipos = Set::extract("{n}.OrdenPagoDetalle",$anticipos);
            return $anticipos;
		
	}


	function getOPagoByProductoSolicitud($id=null){
            $aOrdenDePago = array();

            if(empty($id)) return 0;

            $aOPago = $this->find('list', array('conditions' => array('OrdenPagoDetalle.mutual_producto_solicitud_id' => $id), 'fields' => 'OrdenPagoDetalle.orden_pago_id'));

            foreach($aOPago as $oPago):
                return $oPago;
            endforeach;

            return 0;
	}
	
	
	function getOPagoByReintegro($id=null, $todas= false){
		$aOrdenDePago = array();

		if(empty($id)) return 0;
		
		$aOPago = $this->find('list', array('conditions' => array('OrdenPagoDetalle.socio_reintegro_id' => $id), 'fields' => 'OrdenPagoDetalle.orden_pago_id'));

		foreach($aOPago as $oPago):
			if(!$todas)return $oPago;
		endforeach;
		if($todas) return $aOPago;
		else return 0;
	}
	
	
	
	function getOPagoBySolicitud($id=null){
		$aOrdenDePago = array();

		if(empty($id)) return 0;
		
		$aOPago = $this->find('list', array('conditions' => array('OrdenPagoDetalle.nro_solicitud' => $id), 'fields' => 'OrdenPagoDetalle.orden_pago_id'));

		foreach($aOPago as $oPago):
			return $oPago;
		endforeach;
		
		return 0;
	}
	
	
	/**
	 * Total Pagado por Reintegro
	 * 
	 * @author adrian [14/02/2012]
	 * @param float $socio_reintegro_id
	 */
	function getTotalPagoByReintegro($socio_reintegro_id){
		$pago = $this->find('all',array('conditions' => array('OrdenPagoDetalle.socio_reintegro_id' => $socio_reintegro_id), 'fields' => array('sum(importe) as importe')));
		return (isset($pago[0][0]['importe']) && !empty($pago[0][0]['importe']) ? $pago[0][0]['importe'] : 0);
	}
	
	
	function armaDatos($datos){
		
		$this->OrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
		
		foreach($datos as $indice => $dato):
			$comentario = $this->OrdenPago->read(null, $dato['OrdenPagoDetalle']['orden_pago_id']);
		
			$datos[$indice]['OrdenPagoDetalle']['comentario'] = $comentario['OrdenPago']['comentario']; 
			$datos[$indice]['OrdenPagoDetalle']['fecha_comprobante'] = $comentario['OrdenPago']['fecha_pago'];

		endforeach;

		return $datos;
		
	}
	
	
	function getImporte($orden_pago_id){
		$condiciones = array(
                        'conditions' => array(
                                'OrdenPagoDetalle.orden_pago_id' => $orden_pago_id,
                        ),
                        'fields' => array('SUM(OrdenPagoDetalle.importe) as importe'),
		);
		$importe_detalle = $this->find('all',$condiciones);
		return (isset($importe_detalle[0][0]['importe']) ? $importe_detalle[0][0]['importe'] : 0);		
		
	}

}	

?>
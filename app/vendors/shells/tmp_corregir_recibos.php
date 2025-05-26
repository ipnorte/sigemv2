<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_padron_servicio 43 -app /home/adrian/dev/www/sigem/app/
 *  D:\Desarrollo\xampp\php\php.exe D:\Desarrollo\xampp\htdocs\sigem\cake\console\cake.php tmp_corregir_recibos -app D:\Desarrollo\xampp\htdocs\sigem\app\
 * 
 * 
 */

class TmpCorregirRecibosShell extends Shell {

	
	var $uses = array('clientes.Recibo','Proveedores.OrdenPago', 'cajabanco.BancoCuentaMovimiento', 'cajabanco.BancoCuenta',
					  'mutual.OrdenDescuentoCobro', 'Proveedores.Proveedor', 'cajabanco.BancoConcepto', 'cajabanco.BancoCuentaSaldo',
					  'clientes.TmpReciboDetalle', 'mutual.OrdenDescuento', 'clientes.ReciboDetalle', 'mutual.OrdenCajaCobro');
	var $tasks = array('Temporal');
	
	function main() {
		
//		$this->Recibo = $this->BancoCuentaMovimiento->importarModelo('Recibo', 'clientes');
//		$this->OrdenDescuento = $this->BancoCuentaMovimiento->importarModelo('OrdenDescuento', 'mutual');
//		$this->OrdenDescuentoCobro = $this->BancoCuentaMovimiento->importarModelo('OrdenDescuentoCobro', 'mutual');
//		$this->ReciboDetalle = $this->BancoCuentaMovimiento->importarModelo('ReciboDetalle', 'clientes');
		
		$aRecibos = $this->Recibo->getRecibosEntreFecha('2011-10-01', '2012-06-29');
//		$aRecibos = $this->Recibo->getRecibosEntreFecha('2012-02-17', '2012-02-17');
		
		
		foreach($aRecibos as $recibo):
			if($recibo['Recibo']['anulado'] == 0):
				$cobros = $this->OrdenDescuentoCobro->find('all', array('conditions' => array('OrdenDescuentoCobro.recibo_id' => $recibo['Recibo']['id'])));
				foreach($cobros as $cobro):
					$recDetalleCaja = array();
					$recCobro = $this->OrdenDescuentoCobro->getCobro($cobro['OrdenDescuentoCobro']['id'], true);
					if($recCobro['OrdenDescuentoCobro']['cancelacion_orden_id'] > 0):
						$recDetalleCaja = $this->TmpReciboDetalle($recCobro['OrdenDescuentoCobro']['id'], $recCobro['OrdenDescuentoCobro']['recibo_id']);
					else:
						$cajaCobro = $this->OrdenCajaCobro->find('all', array('conditions' => array('OrdenCajaCobro.orden_descuento_cobro_id' => $recCobro['OrdenDescuentoCobro']['id'])));
						$datos = array();
						$datos['OrdenDescuentoCobro']['cabecera_socio_id'] = $recCobro['OrdenDescuentoCobro']['socio_id'];
						$datos['OrdenDescuentoCobro']['orden_caja_cobro_id'] = $cajaCobro[0]['OrdenCajaCobro']['id'];
						$datos['OrdenDescuentoCobro']['proveedor_origen_fondo_id'] = $recCobro['OrdenDescuentoCobro']['proveedor_origen_fondo_id'];
						$datos['OrdenDescuentoCobro']['recibo_id'] = $recCobro['OrdenDescuentoCobro']['recibo_id'];
						$datos['OrdenDescuentoCobro']['orden_descuento_cobro_id'] = $recCobro['OrdenDescuentoCobro']['id'];
						$recDetalleCaja = $this->ReciboDetalle->detalleReciboCaja($datos);
					endif;
	
					debug('RECIBO NRO.: ' . $recCobro['OrdenDescuentoCobro']['recibo_id']);
					

					if(!empty($recDetalleCaja)):
						if(!$this->TmpReciboDetalle->saveAll($recDetalleCaja)):
							return false;
						endif;
					endif;
								
				endforeach;
			endif;
		endforeach;
	}	
//$oReciboDetalle->detalleReciboCaja($datos);		
//['OrdenDescuentoCobro']['cabecera_socio_id'];
//['OrdenDescuentoCobro']['orden_caja_cobro_id'];
//['OrdenDescuentoCobro']['proveedor_origen_fondo_id'];
//['OrdenDescuentoCobro']['recibo_id'];
//['OrdenDescuentoCobro']['orden_descuento_cobro_id'];

//		$aRecibos = $this->Recibo->getRecibosEntreFecha('2011-01-01', '2012-06-30');
//		
//		foreach($aRecibos as $recibo):
//			foreach($recibo['Recibo']['detalle'] as $detalle):
//				
//				$resultado = ereg_replace("[^0-9]", "", $detalle['concepto']);
//
//				$conditions = array();
//				$conditions['OrdenDescuento.socio_id'] = $detalle['socio_id'];
//				if(substr($detalle['concepto'],0,5) == 'EXPTE' ||
//					substr($detalle['concepto'],0,5) == 'OCOMP' ||
//				   	substr($detalle['concepto'],0,5) == 'CMUTU' ||
//				   	substr($detalle['concepto'],0,5) == 'OSERV' ):
//				   
//				   	if(strpos($detalle['concepto'],'CREDITO')):
//				   		$conditions['OrdenDescuento.proveedor_id !='] = '18';
//				   	elseif(strpos($detalle['concepto'],'FONDO')):
//				   		$conditions['OrdenDescuento.proveedor_id'] = '18';
//				   	endif;
//				   	$conditions['OrdenDescuento.tipo_orden_dto'] = substr($detalle['concepto'],0,5);
//					$numero = ereg_replace("[^0-9]", "", substr($detalle['concepto'],7,6));
//					$conditions['OrdenDescuento.numero'] = $numero;
//					$OrdenDescuento = $this->OrdenDescuento->find('all', array('conditions' => $conditions));
//					
//					$detalle['verificar_orden_descuento_id'] = $OrdenDescuento[0]['OrdenDescuento']['id'];
//					$this->ReciboDetalle->save($detalle);
//				elseif(substr($detalle['concepto'],0,12) == 'CUOTA SOCIAL'):
//					$conditions['OrdenDescuento.proveedor_id'] = '18';
//				   	$conditions['OrdenDescuento.tipo_orden_dto'] = 'CMUTU';
//					$conditions['OrdenDescuento.proveedor_id'] = '18';
//					$OrdenDescuento = $this->OrdenDescuento->find('all', array('conditions' => $conditions));
//					
//					$detalle['verificar_orden_descuento_id'] = $OrdenDescuento[0]['OrdenDescuento']['id'];
//					$this->ReciboDetalle->save($detalle);
//				endif;
//			endforeach;
//		endforeach;
	//FIN PROCESO ASINCRONO
	

	function TmpReciboDetalle($cobro_id, $recibo_id){
		$aTmpDetalle = array();
		$aReciboDetalle = array();
		
//		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
		$ordenDescuento = $this->OrdenDescuentoCobro->getCobro($cobro_id, true);
		
		
		// Establezco que el ingreso viene del socio
		$aOrigen = array();
		$aOrigen['persona_id'] = 0;
		$aOrigen['socio_id']   = $ordenDescuento['OrdenDescuentoCobro']['socio_id'];
		$aOrigen['cliente_id'] = 0;
		$aOrigen['banco_id']   = 0;
		$aOrigen['codigo_organismo']  = null;

		foreach ($ordenDescuento['ReciboDetalle'] as $orden) {
			
//			if($orden['imprimir_detalle'] != 0):
				$aTmpDetalle['id'] = 0;
				$aTmpDetalle['persona_id'] = $aOrigen['persona_id'];
				$aTmpDetalle['socio_id'] = $aOrigen['socio_id'];
				$aTmpDetalle['cliente_id'] = $aOrigen['cliente_id'];
				$aTmpDetalle['banco_id'] = $aOrigen['banco_id'];
				$aTmpDetalle['codigo_organismo'] = $aOrigen['codigo_organismo'];
				$aTmpDetalle['recibo_id'] = $recibo_id;
				$aTmpDetalle['tipo_cobro'] = 'FA';
				$aTmpDetalle['orden_descuento_cobro_id'] = $orden['orden_descuento_cobro_id'];
				$aTmpDetalle['orden_descuento_id'] = $orden['orden_descuento_id'];
				$aTmpDetalle['concepto'] = $orden['concepto'];
				$aTmpDetalle['importe'] = $orden['importe'];
				array_push($aReciboDetalle, $aTmpDetalle);
//			endif;
		}

//		if(!empty($aReciboDetalle)):
//			if(!$this->TmpReciboDetalle->saveAll($aReciboDetalle)):		
//				return false;
//			endif;
//		endif;
		
		return $aReciboDetalle;
	}
	
}


?>
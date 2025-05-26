<?php
class OrdenPagosController extends ProveedoresAppController{
	var $name = 'OrdenPago';
	var $uses = array('Proveedores.Proveedor', 'Proveedores.Movimiento', 'Proveedores.OrdenPago');
	
	var $autorizar = array('imprimir_orden_pago_pdf', 'view_orden_pago', 'detalle_pago_facturas');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	

	



	function imprimir_orden_pago_pdf($id){
		// traer Orden de Pago
		$aOrdenDePago = $this->OrdenPago->getOrdenDePago($id);
		
		if(isset($aOrdenDePago['OrdenPago']['proveedor_factura_id'])):

			$oProveedores = $this->OrdenPago->importarModelo('Proveedor', 'proveedores');
			$aProveedor = $oProveedores->getProveedor($aOrdenDePago['OrdenPago']['proveedor_id']);
			$aOrdenDePago['Proveedor'] = $aProveedor['Proveedor']; 

			
		elseif(isset($aOrdenDePago['OrdenPago']['mutual_producto_solicitud_id'])):

			$this->persona = $this->OrdenPago->importarModelo('Persona', 'pfyj');
			$this->MutualProductoSolicitud = $this->OrdenPago->importarModelo('MutualProductoSolicitud', 'mutual');
			$orden = $this->MutualProductoSolicitud->read(null,$aOrdenDePago['OrdenPago']['mutual_producto_solicitud_id']);
			$aPersona = $this->persona->getPersona($orden['MutualProductoSolicitud']['persona_id']); 
			$aOrdenDePago['Proveedor'] = array(
				'id' => $aPersona['Persona']['id'],
				'razon_social' => $aPersona['Persona']['apenom'],
				'domicilio' => $aPersona['Persona']['domicilio'],
				'iva_concepto' => 'CONSUMIDOR FINAL',
				'formato_cuit' => $aPersona['Persona']['documento'],
				'nro_ingresos_brutos' => ''
			);
			
			
		elseif(isset($aOrdenDePago['OrdenPago']['socio_reintegro_id'])):
		
			$this->Socio = $this->OrdenPago->importarModelo('Socio', 'pfyj');
			$this->persona = $this->OrdenPago->importarModelo('Persona', 'pfyj');
	
			$aSocio = $this->Socio->getPersonaBySocioID($aOrdenDePago['OrdenPago']['socio_id']);
	
			$aPersona = $this->persona->getPersona($aSocio['Persona']['id']); 
			$aOrdenDePago['Proveedor'] = array(
				'id' => $aPersona['Persona']['id'],
				'razon_social' => $aPersona['Persona']['apenom'],
				'domicilio' => $aPersona['Persona']['domicilio'],
				'iva_concepto' => 'CONSUMIDOR FINAL',
				'formato_cuit' => $aPersona['Persona']['documento'],
				'nro_ingresos_brutos' => ''
			);
		
		
		elseif(isset($aOrdenDePago['OrdenPago']['nro_solicitud'])):
			$this->Solicitud = $this->OrdenPago->importarModelo('Solicitud', 'v1');
			$solicitud = $this->Solicitud->getSolicitud($aOrdenDePago['OrdenPago']['nro_solicitud']);
		
			$aOrdenDePago['Proveedor'] = array(
				'id' => $solicitud['PersonaV1']['id_persona'],
				'razon_social' => $solicitud['PersonaV1']['apellido'] . ' ' . $solicitud['PersonaV1']['nombre'],
				'domicilio' => $solicitud['PersonaV1']['calle'] . ' Nro.:' . $solicitud['PersonaV1']['nro_calle'],
				'iva_concepto' => 'CONSUMIDOR FINAL',
				'formato_cuit' => $solicitud['PersonaV1']['documento'],
				'nro_ingresos_brutos' => ''
			);
		else:
			if($aOrdenDePago['OrdenPago']['proveedor_id'] > 0):
				$oProveedores = $this->OrdenPago->importarModelo('Proveedor', 'proveedores');
				$aProveedor = $oProveedores->getProveedor($aOrdenDePago['OrdenPago']['proveedor_id']);
				$aOrdenDePago['Proveedor'] = $aProveedor['Proveedor']; 

			
			elseif($aOrdenDePago['OrdenPago']['socio_id'] > 0):
				$this->Socio = $this->OrdenPago->importarModelo('Socio', 'pfyj');
				$this->persona = $this->OrdenPago->importarModelo('Persona', 'pfyj');
		
				$aSocio = $this->Socio->getPersonaBySocioID($aOrdenDePago['OrdenPago']['socio_id']);
		
				$aPersona = $this->persona->getPersona($aSocio['Persona']['id']); 
				$aOrdenDePago['Proveedor'] = array(
					'id' => $aPersona['Persona']['id'],
					'razon_social' => $aPersona['Persona']['apenom'],
					'domicilio' => $aPersona['Persona']['domicilio'],
					'iva_concepto' => 'CONSUMIDOR FINAL',
					'formato_cuit' => $aPersona['Persona']['documento'],
					'nro_ingresos_brutos' => ''
				);
		
			elseif($aOrdenDePago['OrdenPago']['persona_id'] > 0):
				$this->persona = $this->OrdenPago->importarModelo('Persona', 'pfyj');
				$aPersona = $this->persona->getPersona($aOrdenDePago['OrdenPago']['persona_id']); 
				$aOrdenDePago['Proveedor'] = array(
					'id' => $aPersona['Persona']['id'],
					'razon_social' => $aPersona['Persona']['apenom'],
					'domicilio' => $aPersona['Persona']['domicilio'],
					'iva_concepto' => 'CONSUMIDOR FINAL',
					'formato_cuit' => $aPersona['Persona']['documento'],
					'nro_ingresos_brutos' => ''
				);
			
			endif;
		endif;
		
		
		$this->set('aOrdenDePago', $aOrdenDePago);
		$this->render('imprimir_orden_pago_pdf','pdf');	
		
	}
	

	function view_orden_pago($id){
		$aOrdenDePago = $this->OrdenPago->getOrdenPagoAmpliado($id);	
		$this->set('aOrdenPago', $aOrdenDePago);
	}

        function detalle_pago_facturas($proveedor_factura_id){
            $oPrvFactura = $this->OrdenPago->importarModelo('ProveedorFactura', 'proveedores');
            $oOPFactura = $this->OrdenPago->importarModelo('OrdenPagoFactura', 'proveedores');
            
            $aProvFct = $oPrvFactura->getFactura($proveedor_factura_id);
            $aDetalleFactura = $oOPFactura->DetallePagoFacturas($proveedor_factura_id);

            $this->set('aProvFct', $aProvFct);
            $this->set('aDetalleFactura', $aDetalleFactura);

        }
}
?>
<?php
class ProveedorListadosController extends ProveedoresAppController{
	var $name = 'ProveedorListados';
	var $uses = array('Proveedores.ProveedorListado', 'Proveedores.Movimiento');
	
	var $autorizar = array('iva_compra_PDF', 'iva_compra_xls', 'saldo_a_fecha', 'cta_cte_fecha', 'saldo_a_fecha_xls', 'saldo_a_fecha_detalle_xls',
						   'saldo_a_fecha_pdf', 'saldo_a_fecha_detalle_pdf', 'cta_cte_fecha_xls', 'cta_cte_fecha_pdf', 'listado_tipo_asiento',
						   'listado_tipo_asiento_pdf', 'listado_tipo_asiento_xls', 'listado_tipo_asiento_detalle', 'listado_tipo_asiento_detalle_pdf',
			               'listado_tipo_asiento_detalle_xls', 'listado_concepto_gasto', 'listado_concepto_gasto_pdf', 'listado_concepto_gasto_xls', 
						   'listado_concepto_gasto_detalle', 'listado_concepto_gasto_detalle_pdf', 'listado_concepto_gasto_detalle_xls',
						'cta_cte_operativo_pdf', 'cta_cte_operativo_xls');
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}
	
	
	function index(){
		$this->redirect('iva_compra');	
	}
	
	
	function iva_compra(){
		$facturas = array();
		$showTabla = 0;
		$disable_form = 0;
		$fecha_desde = date("Y") . '-01-01';
		$fecha_hasta = date("Ymd");
		$tipo = 0;
		
		if(!empty($this->data)):

			$showTabla = 1;
			$disable_form = 1;
			$fecha_desde = $this->ProveedorListado->armaFecha($this->data['ListadoIvaCompra']['fecha_desde']);
			$fecha_hasta = $this->ProveedorListado->armaFecha($this->data['ListadoIvaCompra']['fecha_hasta']);
			$tipo = $this->data['ListadoIvaCompra']['tipo'];
			
			$facturas = $this->ProveedorListado->facturas_periodo($fecha_desde, $fecha_hasta, $this->data['ListadoIvaCompra']['tipo']);
//			$concepto_gasto = $this->Listado->factura_concepto_gasto($fecha_desde, $fecha_hasta, $this->data['ListadoIvaCompra']['tipo']);
		endif;
		
		$this->set('facturas', $facturas);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('tipo', $tipo);
		$this->set('showTabla', $showTabla);
		$this->set('disable_form', $disable_form);
	}


	function iva_compra_pdf($fecha_desde, $fecha_hasta, $tipo){
		
		$facturas = $this->ProveedorListado->facturas_periodo($fecha_desde, $fecha_hasta, $tipo);
		
		$this->set('facturas', $facturas);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('tipo', $tipo);

		$this->render('iva_compra_pdf', 'pdf');
	}


	function iva_compra_xls($fecha_desde, $fecha_hasta, $tipo){
	
		$facturas = $this->ProveedorListado->facturas_periodo($fecha_desde, $fecha_hasta, $tipo);
	
		$this->set('facturas', $facturas);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('tipo', $tipo);
	
		$this->render('iva_compra_xls', 'blank');
	}
	
	
	function saldo_a_fecha(){
	
		$saldos = array();
		$showTabla = 0;
		$disable_form = 0;
		$fecha_desde = date("Y") . '-01-01';
		$fecha_hasta = date("Ymd");
		$tipo = 0;
		
		if(!empty($this->data)):

			$showTabla = 1;
			$disable_form = 1;
			$fecha_desde = $this->ProveedorListado->armaFecha($this->data['SaldoFecha']['fecha_desde']);
			$fecha_hasta = $this->ProveedorListado->armaFecha($this->data['SaldoFecha']['fecha_hasta']);
			$tipo = $this->data['SaldoFecha']['tipo'];
			
			$saldos = $this->ProveedorListado->saldo_a_fecha($fecha_desde, $fecha_hasta, $this->data['SaldoFecha']['tipo']);
			
		endif;
		
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($fecha_desde), -1));
		
		$this->set('saldos', $saldos);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('tipo', $tipo);
		$this->set('showTabla', $showTabla);
		$this->set('disable_form', $disable_form);
	}
	
	
	function saldo_a_fecha_pdf($desdeFecha, $hastaFecha, $tipo){
	
		$saldos = array();
		
			
		$saldos = $this->ProveedorListado->saldo_a_fecha($desdeFecha, $hastaFecha, $tipo);
			
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($desdeFecha), -1));
		
		$this->set('saldos', $saldos);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);

		$this->render('saldo_a_fecha_pdf', 'pdf');
	}


	function saldo_a_fecha_xls($desdeFecha, $hastaFecha, $tipo){
	
		$saldos = array();
	
			
		$saldos = $this->ProveedorListado->saldo_a_fecha($desdeFecha, $hastaFecha, $tipo);
			
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($desdeFecha), -1));
	
		$this->set('saldos', $saldos);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
	
		$this->render('saldo_a_fecha_xls', 'blank');
		return true;
	}
	
	
	function saldo_a_fecha_detalle_pdf($desdeFecha, $hastaFecha, $tipo){
	
		$saldos = array();
	
			
		$saldos = $this->ProveedorListado->saldo_a_fecha($desdeFecha, $hastaFecha, $tipo);
			
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($desdeFecha), -1));
	
		$this->set('saldos', $saldos);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
	
		$this->render('saldo_a_fecha_detalle_pdf', 'pdf');
		return true;
	}


	function saldo_a_fecha_detalle_xls($desdeFecha, $hastaFecha, $tipo){
	
		$saldos = array();
	
			
		$saldos = $this->ProveedorListado->saldo_a_fecha($desdeFecha, $hastaFecha, $tipo);
			
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($desdeFecha), -1));
	
		$this->set('saldos', $saldos);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
	
		$this->render('saldo_a_fecha_detalle_xls', 'blank');
		return true;
	}
	
	
	function cta_cte_fecha($proveedor_id, $desdeFecha, $hastaFecha, $saldoAnterior = 0){
		$ctaCte = $this->ProveedorListado->ctaCteFecha($proveedor_id, $desdeFecha, $hastaFecha);
		
		$proveedor = $this->Movimiento->traerProveedor($proveedor_id);
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($desdeFecha), -1));

		$this->set('ctaCte', $ctaCte);
		$this->set('proveedor', $proveedor);
		$this->set('saldo_anterior', $saldoAnterior);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);		
		$this->set('fecha_desde', $desdeFecha);
		$this->set('fecha_hasta', $hastaFecha);
		
	}
	
	
	function cta_cte_fecha_pdf($proveedor_id, $desdeFecha, $hastaFecha){
	
		$saldos = array();
	
			
		$saldos = $this->ProveedorListado->saldo_a_fecha($desdeFecha, $hastaFecha, 0, $proveedor_id);
			
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($desdeFecha), -1));
	
		$this->set('saldos', $saldos);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
	
		$this->render('saldo_a_fecha_detalle_pdf', 'pdf');
		return true;
	}


	function cta_cte_fecha_xls($proveedor_id, $desdeFecha, $hastaFecha){
	
		$saldos = array();
	
			
		$saldos = $this->ProveedorListado->saldo_a_fecha($desdeFecha, $hastaFecha, 0, $proveedor_id);
			
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($desdeFecha), -1));
	
		$this->set('saldos', $saldos);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
	
		$this->render('saldo_a_fecha_detalle_xls', 'blank');
		return true;
	}
	
	
	function listado_tipo_asiento(){
	
		$aTipoAsiento = array();
		$showTabla = 0;
		$disable_form = 0;
		$fecha_desde = date("Y") . '-01-01';
		$fecha_hasta = date("Ymd");
		$tipo = 0;
		
		if(!empty($this->data)):

			$showTabla = 1;
			$disable_form = 1;
			$fecha_desde = $this->ProveedorListado->armaFecha($this->data['ListadoTipoAsiento']['fecha_desde']);
			$fecha_hasta = $this->ProveedorListado->armaFecha($this->data['ListadoTipoAsiento']['fecha_hasta']);
			$tipo = $this->data['ListadoTipoAsiento']['tipo'];
			$aTipoAsiento = $this->ProveedorListado->factura_tipo_asiento($fecha_desde, $fecha_hasta, $this->data['ListadoTipoAsiento']['tipo']);
		endif;
		
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($fecha_desde), -1));
		
		$this->set('aTipoAsiento', $aTipoAsiento);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('tipo', $tipo);
		$this->set('showTabla', $showTabla);
		$this->set('disable_form', $disable_form);
	}
	
	
	function listado_tipo_asiento_pdf($desdeFecha, $hastaFecha, $tipo){
		$aTipoAsiento = $this->ProveedorListado->factura_tipo_asiento($desdeFecha, $hastaFecha, $tipo);
		
		$this->set('aTipoAsiento', $aTipoAsiento);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
		
		$this->render('listado_tipo_asiento_pdf', 'pdf');
		return true;
	}
	
	
	function listado_tipo_asiento_xls($desdeFecha, $hastaFecha, $tipo){
		$aTipoAsiento = $this->ProveedorListado->factura_tipo_asiento($desdeFecha, $hastaFecha, $tipo);
		
		$this->set('aTipoAsiento', $aTipoAsiento);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
		
		$this->render('listado_tipo_asiento_xls', 'blank');
		return true;
	}
	
	
	function listado_tipo_asiento_detalle($tipo_asiento, $desdeFecha, $hastaFecha, $tipo=3){
		$aTipoAsiento = $this->ProveedorListado->factura_tipo_asiento($desdeFecha, $hastaFecha, $tipo, $tipo_asiento);
		
		$this->set('aTipoAsiento', $aTipoAsiento);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
		
	}
	
	
	function listado_tipo_asiento_detalle_pdf($tipo_asiento, $desdeFecha, $hastaFecha, $tipo=3){
		$aTipoAsiento = $this->ProveedorListado->factura_tipo_asiento($desdeFecha, $hastaFecha, $tipo, $tipo_asiento);
		
		$this->set('aTipoAsiento', $aTipoAsiento);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
		
		$this->render('listado_tipo_asiento_pdf', 'pdf');
		return true;
		
	}
	
	
	function listado_tipo_asiento_detalle_xls($tipo_asiento, $desdeFecha, $hastaFecha, $tipo=3){
		$aTipoAsiento = $this->ProveedorListado->factura_tipo_asiento($desdeFecha, $hastaFecha, $tipo, $tipo_asiento);
		
		$this->set('aTipoAsiento', $aTipoAsiento);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);

		$this->render('listado_tipo_asiento_xls', 'blank');
		return true;
		
	}


	function listado_concepto_gasto(){
	
		$aConceptoGasto = array();
		$showTabla = 0;
		$disable_form = 0;
		$fecha_desde = date("Y") . '-01-01';
		$fecha_hasta = date("Ymd");
		$tipo = 0;
	
		if(!empty($this->data)):
	
			$showTabla = 1;
			$disable_form = 1;
			$fecha_desde = $this->ProveedorListado->armaFecha($this->data['ListadoConceptoGasto']['fecha_desde']);
			$fecha_hasta = $this->ProveedorListado->armaFecha($this->data['ListadoConceptoGasto']['fecha_hasta']);
			$tipo = $this->data['ListadoConceptoGasto']['tipo'];
			$aConceptoGasto = $this->ProveedorListado->factura_concepto_gastos($fecha_desde, $fecha_hasta, $this->data['ListadoConceptoGasto']['tipo']);
		endif;
	
		$fecha_saldo_anterior = date("Y-m-d", $this->Movimiento->addDayToDate(strtotime($fecha_desde), -1));
	
		$this->set('aConceptoGasto', $aConceptoGasto);
		$this->set('fecha_saldo_anterior', $fecha_saldo_anterior);
		$this->set('fecha_desde', $fecha_desde);
		$this->set('fecha_hasta', $fecha_hasta);
		$this->set('tipo', $tipo);
		$this->set('showTabla', $showTabla);
		$this->set('disable_form', $disable_form);
	}


	function listado_concepto_gasto_pdf($desdeFecha, $hastaFecha, $tipo){
		$aConceptoGasto = $this->ProveedorListado->factura_concepto_gastos($desdeFecha, $hastaFecha, $tipo);
	
		$this->set('aConceptoGasto', $aConceptoGasto);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
	
		$this->render('listado_concepto_gasto_pdf', 'pdf');
		return true;
	}
	
	
	function listado_concepto_gasto_xls($desdeFecha, $hastaFecha, $tipo){
		$aConceptoGasto = $this->ProveedorListado->factura_concepto_gastos($desdeFecha, $hastaFecha, $tipo);
	
		$this->set('aConceptoGasto', $aConceptoGasto);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
	
		$this->render('listado_concepto_gasto_xls', 'blank');
		return true;
	}
	
	
	function listado_concepto_gasto_detalle($concepto_gasto, $desdeFecha, $hastaFecha, $tipo=3){
		$aConceptoGasto = $this->ProveedorListado->factura_concepto_gastos($desdeFecha, $hastaFecha, $tipo, $concepto_gasto);
	
		$this->set('aConceptoGasto', $aConceptoGasto);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
		
	}
	
	
	function listado_concepto_gasto_detalle_pdf($concepto_gasto, $desdeFecha, $hastaFecha, $tipo=3){
		$aConceptoGasto = $this->ProveedorListado->factura_concepto_gastos($desdeFecha, $hastaFecha, $tipo, $concepto_gasto);
	
		$this->set('aConceptoGasto', $aConceptoGasto);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
	
		$this->render('listado_concepto_gasto_pdf', 'pdf');
		return true;
	
	}
	
	
	function listado_concepto_gasto_detalle_xls($concepto_gasto, $desdeFecha, $hastaFecha, $tipo=3){
		$aConceptoGasto = $this->ProveedorListado->factura_concepto_gastos($desdeFecha, $hastaFecha, $tipo, $concepto_gasto);
	
		$this->set('aConceptoGasto', $aConceptoGasto);
		$this->set('desdeFecha', $desdeFecha);
		$this->set('hastaFecha', $hastaFecha);
		$this->set('tipo', $tipo);
	
		$this->render('listado_concepto_gasto_xls', 'blank');
		return true;
	
	}
	
	
	function cta_cte_operativo_pdf($id){

            $proveedor = $this->Movimiento->traerProveedor($id);

            $ctaCte = $this->Movimiento->armaCtaCteOperativo($id);
            
//            debug($proveedor);
//            debug($ctaCte);
//            exit;
            
            $this->set('ctaCte', $ctaCte);
            $this->set('proveedor', $proveedor);			
		
            $this->render('cta_cte_operativo_pdf', 'pdf');
            return true;
		
	}
	
	
	function cta_cte_operativo_xls($id){

            $proveedor = $this->Movimiento->traerProveedor($id);

            $ctaCte = $this->Movimiento->armaCtaCteOperativo($id);
            
//            debug($ctaCte);
//            exit;
            
            $this->set('ctaCte', $ctaCte);
            $this->set('proveedor', $proveedor);			
		
            $this->render('cta_cte_operativo_xls', 'blank');
            return true;
		
	}
		
	
}	
?>
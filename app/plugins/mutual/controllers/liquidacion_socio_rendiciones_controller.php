<?php
/**
* 
* liquidacion_socio_rendiciones_controller.php
* adrian [12/01/2012]
*/

class LiquidacionSocioRendicionesController extends MutualAppController{
	
	var $name = 'LiquidacionSocioRendiciones';
	
	var $autorizar = array('get_resumen_turnos_by_archivo_list','get_resumen_turnos_by_liquidacion_list',
            'imputar_archivo_add_recibo');
	
	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}
	
	function index(){
		parent::noDisponible();
	}

	
	function get_resumen_turnos_by_archivo_list($liquidacionIntercambioID = null, $noCobrados = 1){
		if(empty($liquidacionIntercambioID)) return null;
		$resumenSelect = $this->LiquidacionSocioRendicion->getResumenByEmpresaTurno($liquidacionIntercambioID,($noCobrados == 1 ? true : false),true);
		return $resumenSelect;
	}
    
	function get_resumen_turnos_by_liquidacion_list($liquidacionID = null, $noCobrados = 1){
		if(empty($liquidacionID)) return null;
		$resumenSelect = $this->LiquidacionSocioRendicion->getResumenByEmpresaTurnoByLiquidacionId($liquidacionID,($noCobrados == 1 ? true : false),true);
		return $resumenSelect;
	} 
        
        
        function imputar_archivo($intercambio_id){
            
            App::import('Model','Mutual.LiquidacionIntercambio');
            $oFile = new LiquidacionIntercambio();
            $archivo = $oFile->get($intercambio_id);

            if(empty($archivo)) parent::noDisponible();

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();		

            $liquidacion = $oLiq->cargar($archivo['LiquidacionIntercambio']['liquidacion_id']);

            if(empty($liquidacion)) parent::noDisponible(); 
            
            $this->set('liquidacion', $liquidacion);
            $this->set('intercambioId', $intercambio_id); 
            
            $this->Session->del('grilla_cobros');
            
            if(!empty($this->data)):
                    $ReciboId = $oLiq->guardarRecibo($this->data);
                    if(!$ReciboId):
                            $this->Mensaje->errorGuardar();
                    endif;
                    $this->redirect("imputar_archivo/$intercambio_id");
            endif; 
            
            $oLqdInterCambio = $oLiq->importarModelo('LiquidacionIntercambio', 'mutual');
            $aLqdInterCambio = $oLqdInterCambio->get($intercambio_id);

            $oBancoCuenta = $oLiq->importarModelo('BancoCuenta', 'cajabanco');
            $cmbBancoCuenta = $oBancoCuenta->comboByBanco($aLqdInterCambio['LiquidacionIntercambio']['banco_id']);

            $oTipoDocumento = $oLiq->importarModelo('TipoDocumento', 'config');
            $cmbRecibo = $oTipoDocumento->comboRecibo();

            $this->set('LqdInterCambio', $aLqdInterCambio);
            $this->set('cmbCuenta', $cmbBancoCuenta);
            $this->set('cmbRecibo', $cmbRecibo);            
            
            
        }
        
        
      
        
	
}

?>
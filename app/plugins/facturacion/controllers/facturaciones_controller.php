<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FacturacionesController extends FacturacionAppController{
	
	var $name = 'Facturaciones';

//	var $uses = array('Mutual.OrdenDescuentoCobro', 'Mutual.OrdenDescuentoCobroCuota');
	var $uses =  array('Mutual.ListadoService','Shells.Asincrono', 'Facturacion.Factura');

	var $autorizar = array('ver_error_afip', 'imprimir_factura_afip', 'reporte_xls');

	function __construct(){
		parent::__construct();
	}       
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}
        
        
        function facturacion_electronica($fecha = null){
            $disableForm = 0;
            $showAsincrono = 0;
            $datosAfip = array();
            $cnxAfip = array();
            

//            $this->set('fecha_desde',date('Y-m-d'));
            $this->set('fecha_desde', date("Y-m") . '-01');
            $this->set('fecha_hasta',date('Y-m-d'));		

            if(!empty($this->data)){
                $this->set('codigo_organismo',$this->data['ListadoService']['codigo_organismo']);
                $this->set('tipo_cobro',$this->data['ListadoService']['tipo_cobro']);
                $this->set('fecha_desde',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_desde']));
                $this->set('fecha_hasta',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_hasta']));
                $this->set('fecha_factura',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_factura']));
                $this->set('proveedor_id',$this->data['ListadoService']['proveedor_id']);
                $this->set('tipo_salida','XLS');
                $disableForm = 1;
                $showAsincrono = 1;
            }
            
            if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){
                
                App::import('model','Shells.Asincrono');
                $oASINC = new Asincrono();
                $asinc = $oASINC->read('p3',$this->params['url']['pid']);
                
                $fechaFactura = date('Y-m-d', strtotime($asinc['Asincrono']['p3']));
                
                $this->set('fecha_desde', $fechaFactura);
                $this->set('fecha_hasta', $fechaFactura);
                
                $aFacturaInforme = $this->Factura->getFacturaEntreFecha($fechaFactura,$fechaFactura);
                
                $this->set('fecha_desde',$fechaFactura);
                $this->set('fecha_hasta',$fechaFactura);
                $this->set('aFacturaInforme', $aFacturaInforme);
                $this->set('disable_form',1);
                
                
                $this->render('facturacion_informe');
            }
            
/*            if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){
            else{	
*/
            
            /*
             * Traigo los datos de configuracion del AFIP
             */
            $datosAfip = $this->getDatoAfip();

            /*
             * Conecto con los servidores del Afip
             */
            $cnxAfip = $this->conexionAfip($datosAfip);

            $server_status = $cnxAfip->ElectronicBilling->GetServerStatus();

            $this->set('errorConexion', $server_status->AuthServer);
            $this->set('datos_afip', $datosAfip);
            $this->set('disable_form',$disableForm);
            $this->set('show_asincrono',$showAsincrono);

		
        }
	
        
        function facturacion_informe($fecha = null){
            $disableForm = 0;
		
            $this->set('fecha_desde', date("Y-m") . '-01');
            $this->set('fecha_hasta', date('Y-m-d'));
            
            $fechas = array(
                'fecha_desde' => date("Y-m") . '-01',
                'fecha_hasta' => date('Y-m-d')
            ); 
		
            if(!empty($this->data)):
        		$disableForm = 1;
                $aFacturaInforme = $this->Factura->getFacturaEntreFecha($this->Factura->armaFecha($this->data['Factura']['fecha_desde']), $this->Factura->armaFecha($this->data['Factura']['fecha_hasta']));

                $this->set('fecha_desde',$this->Factura->armaFecha($this->data['Factura']['fecha_desde']));
        		$this->set('fecha_hasta',$this->Factura->armaFecha($this->data['Factura']['fecha_hasta']));
        		$this->set('aFacturaInforme', $aFacturaInforme);
        		
        		$fechas = array(
        		    'fecha_desde' => $this->Factura->armaFecha($this->data['Factura']['fecha_desde']),
        		    'fecha_hasta' => $this->Factura->armaFecha($this->data['Factura']['fecha_hasta'])
        		); 
        		
            endif;
            
            $this->set('fechas',base64_encode(serialize($fechas)));
		
            $this->set('disable_form',$disableForm);
		
        }
	

        function ver_error_afip($factura_id){
            $aFactura = $this->Factura->getFactura($factura_id);
            
            $this->set('aFactura', $aFactura);
            
        }
        
        
        function imprimir_factura_afip($factura_id){
            $aFacturaAfip = $this->Factura->factura_afip($factura_id);

            $this->set('aFactura', $aFacturaAfip);


            $this->render('imprimir_factura_afip','pdf');
            
        }
        
        
        function getDatoAfip(){
            App::import('Model','config.AfipDato');
            $oAfipDato = new AfipDato();
            
            return $oAfipDato->getAfipDato();
            
        }
            

        function conexionAfip($datoAfip){
            $include = ROOT . DS . APP_DIR . DS . 'vendors' . DS . 'afip' . DS . 'src' . DS . 'Afip.php';
            $Afip;

            include $include;
            if($datoAfip['AfipDato']['modo'] == 0){     // HOMOLOGACION
                $Afip = new Afip(array('CUIT' => $datoAfip['AfipDato']['cuit'], 'cert' => $datoAfip['AfipDato']['pem'], 'key' => $datoAfip['AfipDato']['clave']));
            }
            else{     // PRODUCCION
                    $Afip = new Afip(array('CUIT' => $datoAfip['AfipDato']['cuit'], 'production' => TRUE, 'cert' => $datoAfip['AfipDato']['certificado'], 'key' => $datoAfip['AfipDato']['clave']));
            }
            $server_status = $Afip->ElectronicBilling->GetServerStatus();

            return $Afip;
        }
        
        
        function reporte_xls($param) {
            
            $fechas = unserialize(base64_decode($param));
            $aFacturaInforme = $this->Factura->getFacturaEntreFecha($fechas['fecha_desde'], $fechas['fecha_hasta']);
            $this->set('datos', $aFacturaInforme);
            $this->set('fecha_desde',$fechas['fecha_desde']);
            $this->set('fecha_hasta',$fechas['fecha_hasta']);
            $this->render('reporte_xls','blank');
        }
}
?>
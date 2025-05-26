<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage controller
 */

class LiquidacionSociosController extends MutualAppController{
	
	var $name = "LiquidacionSocios";
	
	
	var $autorizar = array('reprocesar_archivo','reprocesar_archivo_general','cargar_scoring_by_socio');	
	
	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}	
	
	function index(){
		parent::noDisponible();
	}
	
	
	function reliquidar_by_socio($socio_id,$periodo){

		$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
		if(isset($INI_FILE['general']['sp_liquida_deuda_cbu']) && $INI_FILE['general']['sp_liquida_deuda_cbu'] == 1){
			// $liquidacion = $this->LiquidacionSocio->reliquidar_sp($socio_id,$periodo);
			$liquidacion = $this->LiquidacionSocio->reliquidar($socio_id,$periodo);
		}else{
			$liquidacion = $this->LiquidacionSocio->reliquidar($socio_id,$periodo);
		}
		
		if(isset($liquidacion[0]) && $liquidacion[0] == 0){
			$this->Mensaje->ok("RELIQUIDACION EFECTUADA CORRECTAMENTE - " . $liquidacion[2]);
		}else if(isset($liquidacion[0]) && $liquidacion[0] == 1){
			$this->Mensaje->error("SE PRODUJO UN ERROR AL RELIQUIDAR >> CAUSA = " . $liquidacion[1]);
		}
		$this->redirect('/mutual/liquidaciones/by_socio/'.$socio_id);
	}
	
	/**
	 * 
	 * @param $socio_id
	 * @return unknown_type
	 */
	function generar_liquidacion($socio_id){
		
            if(!empty($this->data)) {
                $liquidacion = $this->LiquidacionSocio->reliquidar($socio_id,$this->data['Liquidacion']['periodo']);
                if(isset($liquidacion[0]) && $liquidacion[0] != 0){
                    $this->Mensaje->error("SE PRODUJO UN ERROR AL LIQUIDAR >> CAUSA = " . $liquidacion[2]);
                } else {
                    if($error != 1) $this->Mensaje->ok("RELIQUIDACION EFECTUADA CORRECTAMENTE");
                }                
                $this->redirect('/mutual/liquidaciones/by_socio/'.$socio_id);
            }
            $this->set('socio_id',$socio_id);
	}
	
	
	function recupero_cartera($liquidacion_id = null, $proveedor_id = null){
		
		if(empty($liquidacion_id)) parent::noDisponible();
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $oLiq->cargar($liquidacion_id);		
		
		if(empty($liquidacion['Liquidacion']['id'])) parent::noDisponible();

		//CONTROLAR QUE NO TENGA LIQUIDACIONES POSTERIORES IMPUTADAS
		$isRecuperable = $oLiq->isCarteraRecuperable($liquidacion_id);
		
		$resumen = null;
		$cuotas = null;
		$recuperos = null;
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
				
		App::import('Model','Mutual.LiquidacionCuotaRecupero');
		$oLCR = new LiquidacionCuotaRecupero();	

		//veo si viene una anulacion
		if(isset($this->params['url']['ANULAR']) && !empty($this->params['url']['ANULAR'])){
			
			if(is_numeric($this->params['url']['ANULAR']) && $this->params['url']['ANULAR'] != "TODO"){
				$recupero = $oLCR->read(null,$this->params['url']['ANULAR']);
				if(!empty($recupero)){
					
					if($oLCR->anular($recupero['LiquidacionCuotaRecupero']['id'])){
						$this->Mensaje->ok("LAS ORDENE DE RECUPERO #".$recupero['LiquidacionCuotaRecupero']['id']." FUE ANULADA CORRECTAMENTE");
						$this->redirect("recupero_cartera/$liquidacion_id/$proveedor_id");
					}else{	
						debug($oLCR->notificaciones);
					}
					
				}
				
			}else if($this->params['url']['ANULAR'] == "TODO"){
				
				if($oLCR->anularByLiquidacion($liquidacion_id)){
					$this->Mensaje->ok("TODAS LAS ORDENES DE RECUPERO FUERON ANULADAS CORRECTAMENTE");
					$this->redirect("recupero_cartera/$liquidacion_id/$proveedor_id");
				}else{
					debug($oLCR->notificaciones);
				}				
				
			}
			
			
			
		}
		
		
		if(!empty($this->data)){
			
			if(!isset($this->data['LiquidacionCuota']['importe_recupero'])) $this->redirect("recupero_cartera/$liquidacion_id/" . $this->data['LiquidacionSocios']['proveedor_id']);
			
			$recuperoData = array(
				'liquidacion_id' => $this->data['LiquidacionCuotaRecupero']['liquidacion_id'],
				'liquidacion_cuotas' => array_keys($this->data['LiquidacionCuota']['id']),
				'importe_total' => $this->data['LiquidacionCuota']['importe_recupero'],
				'tipo_producto_recupero' => $this->data['LiquidacionCuotaRecupero']['tipo_producto_recupero'],
				'tipo_cobro_recupero' => $this->data['LiquidacionCuotaRecupero']['tipo_cobro'],
				'fecha_cobro' => $oLCR->armaFecha($this->data['LiquidacionCuotaRecupero']['fecha_cobro']),
				'cantidad_cuotas' => (!empty($this->data['LiquidacionCuotaRecupero']['cantidad_cuotas']) ? $this->data['LiquidacionCuotaRecupero']['cantidad_cuotas'] : 1),
				'periodo_proveedor' => $this->data['LiquidacionCuotaRecupero']['periodo_proveedor'],
				'periodo_socio' => $this->data['LiquidacionCuotaRecupero']['periodo_socio']['year'].$this->data['LiquidacionCuotaRecupero']['periodo_socio']['month'],
				'proveedor_id' => $this->data['LiquidacionCuotaRecupero']['proveedor_id'],
			);
			
			if($oLCR->generar($recuperoData)){
				$this->redirect("recupero_cartera/$liquidacion_id/" . $recuperoData['proveedor_id']);
			}else{
				$this->Mensaje->errorGuardar();
			}
			
		}
		
		if(!empty($proveedor_id)){
			
			$resumen = $oLC->getResumenLiquidacionByProveedor($liquidacion_id,$liquidacion['Liquidacion']['periodo'],$proveedor_id,true,true,false);
			$cuotas = $oLC->getCuotasAdeudadasByProveedorByLiquidacion($liquidacion_id,$proveedor_id,true);
			$recuperos = $oLCR->getByLiquidacion($liquidacion_id,$proveedor_id);
			
		}
		
		

		
		$this->set('liquidacion',$liquidacion);	
		$this->set('resumen',$resumen);	
		$this->set('cuotas',$cuotas);
		$this->set('recuperos',$recuperos);
		$this->set('isRecuperable',$isRecuperable);

		
		
	}
	
	
	function reprocesar_archivo($intercambioId = null, $showListadoControl = null, $formato = null){
		
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();
		$archivo = $oFile->get($intercambioId);
		
		if(empty($archivo)) parent::noDisponible();
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();		
		
		$liquidacion = $oLiq->cargar($archivo['LiquidacionIntercambio']['liquidacion_id']);

		if(empty($liquidacion)) parent::noDisponible();
		
		//cargo los datos del diskette
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oSR = new LiquidacionSocioRendicion();		

		App::import('Model','Mutual.LiquidacionSocio');
		$oSOCIO = new LiquidacionSocio();	
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLQ = new LiquidacionCuota();		

		
		if(!empty($showListadoControl)):
		
			$datos = unserialize($this->Session->read("DISKETTE_$showListadoControl"));
//			debug($this->Session->read("DISKETTE_$showListadoControl"));
//            exit;
			$this->set('params',$datos['params']);
			$this->set('sociosOK',Set::extract("/LiquidacionSocio[error=0]",$datos['info_procesada']));
			$this->set('sociosERROR',Set::extract("/LiquidacionSocio[error=1]",$datos['info_procesada']));
			$this->set('diskette',$datos['diskette']);
			
			$this->set('liquidacion', $liquidacion);
			$this->set('archivo', $archivo);
			
			if($formato == 'PDF'){
				$this->render('reportes/reprocesamiento_diskette_cbu_listado_control_pdf','pdf');
			}
			return;
		
		endif;
		
		
// 		$resumenSelect = $oSR->getResumenByEmpresaTurno($intercambioId,true);
		
		$disableForm = 0;
		
		$datos = null;
		$resumenByTurno = false;
		
		if(!empty($this->data)){
			
			$disableForm = 1;
// 			$filtro = explode("|", $this->data['LiquidacionSocio']['filtro_empresa']);
			
			$filtro = (is_array($this->data['LiquidacionSocio']['filtro_empresa']) ? array_keys($this->data['LiquidacionSocio']['filtro_empresa']) : $this->data['LiquidacionSocio']['filtro_empresa']);
			
			$params = array(
				'liquidacion_id' => $this->data['LiquidacionSocio']['liquidacion_id'],
				'filtro' => $filtro,
				'proveedor_id' => $this->data['LiquidacionSocio']['proveedor_id'],			
				'criterio_deuda' =>$this->data['LiquidacionSocio']['criterio_deuda'],
				'banco_intercambio' => $this->data['LiquidacionSocio']['banco_intercambio'],
				'fecha_debito' => $oSOCIO->armaFecha($this->data['LiquidacionSocio']['fecha_debito']),
				'nro_archivo' => $this->data['LiquidacionSocio']['nro_archivo'],
				'tipo_cuota' => $this->data['LiquidacionSocio']['tipo_cuota'],
				'monto_corte' => $this->data['LiquidacionSocio']['monto_corte'],
                'fecha_presentacion' => $oSOCIO->armaFecha($this->data['LiquidacionSocio']['fecha_presentacion']),
			);
			$datos = $oSOCIO->reprocesarDisketteCBU($params,$resumenByTurno);
//			debug($datos);
//			exit;
			$this->Session->write("DISKETTE_" . $datos['diskette']['uuid'] ,serialize($datos));
			$this->set('diskette_uuid',$datos['diskette']['uuid']);
			
			if(!$resumenByTurno){
				$this->set('sociosOK',Set::extract("/LiquidacionSocio[error=0]",$datos['info_procesada']));
				$this->set('sociosERROR',Set::extract("/LiquidacionSocio[error=1]",$datos['info_procesada']));
			}else{
				$datos = $datos['info_procesada_by_turno'];
			}
			
			
		}
		
		$this->set('intercambioId', $intercambioId);
		$this->set('liquidacion', $liquidacion);
		$this->set('archivo', $archivo);
// 		$this->set('resumenSelect', $resumenSelect);
		$this->set('disableForm', $disableForm);
		$this->set('datos',$datos);
		$this->set('resumenByTurno',$resumenByTurno);
		
		
	}	
	
	
//	function consultar_recupero_cartera($liquidacion_id = null, $proveedor_id = null){
//		
//		if(empty($liquidacion_id)) parent::noDisponible();
//		
//		App::import('Model','Mutual.Liquidacion');
//		$oLiq = new Liquidacion();
//		$liquidacion = $oLiq->cargar($liquidacion_id);		
//		
//		if(empty($liquidacion['Liquidacion']['id'])) parent::noDisponible();
//		
//		App::import('Model','Mutual.LiquidacionCuotaRecupero');
//		$oLCR = new LiquidacionCuotaRecupero();		
//		
//		$recuperos = $oLCR->getByLiquidacion($liquidacion_id,$proveedor_id);
//		
//		$this->set('liquidacion',$liquidacion);
//		$this->set('recuperos',$recuperos);
//		
//	}
//	
//	function anular_recupero_cartera($liquidacion_id = null, $proveedor_id = null){
//		
//		if(empty($liquidacion_id)) parent::noDisponible();
//		
//		App::import('Model','Mutual.LiquidacionCuotaRecupero');
//		$oLCR = new LiquidacionCuotaRecupero();				
//		
//		if(!$oLCR->anularByLiquidacion($liquidacion_id)){
//			debug($oLCR->notificaciones);
//		}
//		exit;
//		
//	}
	
	
    
	function reprocesar_archivo_general($liquidacionId = null, $showListadoControl = null, $formato = null){
		
//		App::import('Model','Mutual.LiquidacionIntercambio');
//		$oFile = new LiquidacionIntercambio();
//		$archivo = $oFile->get($intercambioId);
//		
		if(empty($liquidacionId)) parent::noDisponible();
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();		
		
		$liquidacion = $oLiq->cargar($liquidacionId);

		if(empty($liquidacion)) parent::noDisponible();
		
		//cargo los datos del diskette
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oSR = new LiquidacionSocioRendicion();		

		App::import('Model','Mutual.LiquidacionSocio');
		$oSOCIO = new LiquidacionSocio();	
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLQ = new LiquidacionCuota();		

		
		if(!empty($showListadoControl)):
		
			$datos = unserialize(base64_decode($this->Session->read("DISKETTE_$showListadoControl")));
			
			$this->set('params',$datos['params']);
			$this->set('sociosOK',Set::extract("/LiquidacionSocio[error=0]",$datos['info_procesada']));
			$this->set('sociosERROR',Set::extract("/LiquidacionSocio[error=1]",$datos['info_procesada']));
			$this->set('diskette',$datos['diskette']);
			
			$this->set('liquidacion', $liquidacion);
//			$this->set('archivo', $archivo);
			
			if($formato == 'PDF'){
				$this->render('reportes/reprocesamiento_diskette_cbu_listado_control_pdf','pdf');
			}
			return;
		
		endif;
		
		
// 		$resumenSelect = $oSR->getResumenByEmpresaTurno($intercambioId,true);
		
		$disableForm = 0;
		
		$datos = null;
		$resumenByTurno = false;
		
		if(!empty($this->data)){
			
			$disableForm = 1;
// 			$filtro = explode("|", $this->data['LiquidacionSocio']['filtro_empresa']);
			
			$filtro = (is_array($this->data['LiquidacionSocio']['filtro_empresa']) ? array_keys($this->data['LiquidacionSocio']['filtro_empresa']) : $this->data['LiquidacionSocio']['filtro_empresa']);
			
			$params = array(
				'liquidacion_id' => $this->data['LiquidacionSocio']['liquidacion_id'],
				'filtro' => $filtro,
				'proveedor_id' => $this->data['LiquidacionSocio']['proveedor_id'],			
				'criterio_deuda' =>$this->data['LiquidacionSocio']['criterio_deuda'],
				'banco_intercambio' => $this->data['LiquidacionSocio']['banco_intercambio'],
				'fecha_debito' => $oSOCIO->armaFecha($this->data['LiquidacionSocio']['fecha_debito']),
				'nro_archivo' => $this->data['LiquidacionSocio']['nro_archivo'],
				'tipo_cuota' => $this->data['LiquidacionSocio']['tipo_cuota'],
				'monto_corte' => $this->data['LiquidacionSocio']['monto_corte'],
			);
//			$datos = $oSOCIO->reprocesarDisketteCBU($params,$resumenByTurno);
//			debug($datos);
//			exit;
			$this->Session->write("DISKETTE_" . $datos['diskette']['uuid'] ,serialize($datos));
			$this->set('diskette_uuid',$datos['diskette']['uuid']);
			
			if(!$resumenByTurno){
				$this->set('sociosOK',Set::extract("/LiquidacionSocio[error=0]",$datos['info_procesada']));
				$this->set('sociosERROR',Set::extract("/LiquidacionSocio[error=1]",$datos['info_procesada']));
			}else{
				$datos = $datos['info_procesada_by_turno'];
			}
			
			
		}
		
		$this->set('liquidacionId', $liquidacionId);
		$this->set('liquidacion', $liquidacion);
//		$this->set('archivo', $archivo);
// 		$this->set('resumenSelect', $resumenSelect);
		$this->set('disableForm', $disableForm);
		$this->set('datos',$datos);
		$this->set('resumenByTurno',$resumenByTurno);
		
		
	}    
    
	function cargar_scoring_by_socio($socio_id, $limit = 1){
        $scoring = $this->LiquidacionSocio->cargar_scoring_by_socio($socio_id,$limit);
		$scores = $this->LiquidacionSocio->cargar_scoring_by_socio($socio_id,$limit,TRUE);
		$score = $this->LiquidacionSocio->cargar_scoring_by_socio($socio_id,$limit,FALSE,TRUE);
        $this->set('scoring', $scoring[0]);
		$this->set('scores', $scores);
		$this->set('score', $score[0][0]);
    }
}
?>
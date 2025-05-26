<?php
class OrdenDescuentoCuotasController extends MutualAppController{
	
	var $name = 'OrdenDescuentoCuotas';
	
	
	var $autorizar = array(
                        'by_orden_descuento',
                        'view',
                        'cuotas_by_odescuento',
                        'saldo_cuota',
                        'get_resumen_deuda',
                        'reversos',
                        'ente_recaudador',
                        'estado_cuenta_pdf2',
                        'estado_cuenta'
        );
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
		
	
	function view($id){
		$this->OrdenDescuentoCuota->recursive = 3;
		$this->OrdenDescuentoCuota->OrdenDescuentoCobroCuota->bindModel(array('belongsTo' => array('OrdenDescuentoCobro')));
		$this->OrdenDescuentoCuota->LiquidacionCuota->bindModel(array('belongsTo' => array('Liquidacion')));
		$cuota = $this->OrdenDescuentoCuota->getCuota($id);
		if(empty($cuota)){
			parent::noAutorizado();	
		}else{
			$this->set('cuota',$cuota);
			$this->set('saldos',$this->saldo_cuota($cuota['OrdenDescuentoCuota']['id']));
			$this->render();
		}
	}
	

	function estado_cuenta($socio_id,$menuPersonas=1){
            $this->redirect('estado_cuenta2');
		$cuotas = null;
		$this->OrdenDescuentoCuota->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->OrdenDescuentoCuota->Socio->read(null,$socio_id);
		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		$soloDeuda = true;
		
		$periodos = $this->OrdenDescuentoCuota->periodosBySocio($socio_id);
		$periodos = Set::extract("{n}.OrdenDescuentoCuota.periodo",$periodos);
		$periodo_fin = date('Ym');

		if(!empty($periodos)):
			$key = array_search($periodo_fin, $periodos);
			if(!empty($key)):
				$key = $key - count($periodos) - 4;
			else:
				//el ultimo periodo que tiene es menor que YM -> periodo_fin el ultimo periodo
				//y la key para el periodo ini tomo del ultimo que tiene menos 4
				$periodo_fin = array_slice($periodos,count($periodos)-1,1);
				$periodo_fin = $periodo_fin[0];
				$key = (count($periodos) - 4) - count($periodos);
			endif;
			$periodo_ini = array_slice($periodos,$key,1);
			$periodo_ini = $periodo_ini[0];
		else:
			$periodo_ini = null;
			$periodo_fin = null;
		endif;
		
		//CARGAR LOS PROVEEDORES VINCULADOS AL SOCIO
		$proveedores = $this->OrdenDescuentoCuota->getProveedoresBySocio($socio_id);
		$proveedor_id = 0;
		$codigo_organismo = null;
		
		if(!empty($this->data)):
			$this->set('periodo_d',$this->data['OrdenDescuentoCuota']['periodo_ini']);
			$this->set('periodo_h',$this->data['OrdenDescuentoCuota']['periodo_fin']);
			$soloDeuda = (isset($this->data['OrdenDescuentoCuota']['solo_deuda']) ? true : false);
			$discriminaPagos = (isset($this->data['OrdenDescuentoCuota']['discrimina_pagos']) ? true : false);
			$this->set('solo_deuda',$soloDeuda);
			$this->set('discrimina_pagos',$discriminaPagos);
			$this->OrdenDescuentoCuota->unbindModel(array('belongsTo' => array('Socio')));
			$proveedor_id = $this->data['OrdenDescuentoCuota']['proveedor_id'];
			$codigo_organismo = $this->data['OrdenDescuentoCuota']['codigo_organismo'];
            
//            $cuotas = $this->OrdenDescuentoCuota->procesa_deuda($socio_id,$this->data['OrdenDescuentoCuota']['periodo_ini'],$this->data['OrdenDescuentoCuota']['periodo_fin'],$soloDeuda,$proveedor_id,$codigo_organismo,$discriminaPagos);
			$cuotas = $this->OrdenDescuentoCuota->cuotasSocioByPeriodo($socio_id,$this->data['OrdenDescuentoCuota']['periodo_ini'],$this->data['OrdenDescuentoCuota']['periodo_fin'],$soloDeuda,$proveedor_id,$codigo_organismo,$discriminaPagos);
		endif;
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);
		$this->set('cuotas',$cuotas);
		
		$this->set('periodos',$periodos);
		$this->set('periodo_ini',$periodo_ini);
		$this->set('periodo_fin',$periodo_fin);
		$this->set('solo_deuda',$soloDeuda);
		$this->set('proveedores',$proveedores);
		$this->set('proveedor_id',(empty($proveedor_id) ? 0 : $proveedor_id));
		$this->set('codigo_organismo',(empty($codigo_organismo) ? 0 : $codigo_organismo));
		
		if($proveedor_id !=0) $this->set('proveedor_razon_social',$this->requestAction('/proveedores/proveedores/get_razon_social/'.$proveedor_id));
		else $this->set('proveedor_razon_social',null);
		
		if ($this->RequestHandler->isAjax()){
			$this->disableCache();
//			$this->render('estado_cuenta_mutual_ajax2');
            $this->render('estado_cuenta_mutual_ajax');
		}else{
			$this->render('estado_cuenta_mutual');	
		}
		
	}
    
    
	function estado_cuenta2($socio_id,$menuPersonas=1){
		$cuotas = null;
		$this->OrdenDescuentoCuota->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->OrdenDescuentoCuota->Socio->read(null,$socio_id);
		
		if(empty($socio)) $this->redirect('/pfyj/personas');
		
		$soloDeuda = true;
		
//		$periodos = $this->OrdenDescuentoCuota->periodosBySocio($socio_id,false,null,false,"ASC",true);
                $periodos = $this->OrdenDescuentoCuota->periodosBySocio($socio_id);
		$periodos = Set::extract("{n}.OrdenDescuentoCuota.periodo",$periodos);
//                debug($periodos);
		$periodo_fin = date('Ym');

		if(!empty($periodos)):
			$key = array_search($periodo_fin, $periodos);
//                        debug($key);
			if(!empty($key)):
				$key = $key - count($periodos) - 4;
			else:
				//el ultimo periodo que tiene es menor que YM -> periodo_fin el ultimo periodo
				//y la key para el periodo ini tomo del ultimo que tiene menos 4
				$periodo_fin = array_slice($periodos,count($periodos)-1,1);
                                
				$periodo_fin = $periodo_fin[0];
//                                debug($periodo_fin);
				$key = (count($periodos) - 4) - count($periodos);
//                                debug($key);
			endif;
			$periodo_ini = array_slice($periodos,$key,1);
			$periodo_ini = $periodo_ini[0];
		else:
			$periodo_ini = null;
			$periodo_fin = null;
		endif;
		
		//CARGAR LOS PROVEEDORES VINCULADOS AL SOCIO
//		$proveedores = $this->OrdenDescuentoCuota->getProveedoresBySocio($socio_id);
		$proveedor_id = 0;
		$codigo_organismo = null;
                
                $proveedores = $this->OrdenDescuentoCuota->getDatosParaCombosEstadoCuenta($socio_id,'PROV');
                $productos = $this->OrdenDescuentoCuota->getDatosParaCombosEstadoCuenta($socio_id,'PROD');
                $situaciones = $this->OrdenDescuentoCuota->getDatosParaCombosEstadoCuenta($socio_id,'SITU');
                $organismos = $this->OrdenDescuentoCuota->getDatosParaCombosEstadoCuenta($socio_id,'ORGA');
		
		if(!empty($this->data)):
			$this->set('periodo_d',$this->data['OrdenDescuentoCuota']['periodo_ini']);
			$this->set('periodo_h',$this->data['OrdenDescuentoCuota']['periodo_fin']);
			$soloDeuda = (isset($this->data['OrdenDescuentoCuota']['solo_deuda']) ? true : false);
			$discriminaPagos = (isset($this->data['OrdenDescuentoCuota']['discrimina_pagos']) ? true : false);
			$this->set('solo_deuda',$soloDeuda);
			$this->set('discrimina_pagos',$discriminaPagos);
			$this->OrdenDescuentoCuota->unbindModel(array('belongsTo' => array('Socio')));
			$proveedor_id = $this->data['OrdenDescuentoCuota']['proveedor_id'];
			$codigo_organismo = $this->data['OrdenDescuentoCuota']['codigo_organismo'];
                        $tipo_producto = $this->data['OrdenDescuentoCuota']['tipo_producto'];
                        $situacion = $this->data['OrdenDescuentoCuota']['situacion'];
            
            $cuotas = $this->OrdenDescuentoCuota->procesa_deuda($socio_id,$this->data['OrdenDescuentoCuota']['periodo_ini'],$this->data['OrdenDescuentoCuota']['periodo_fin'],$soloDeuda,$proveedor_id,$codigo_organismo,$discriminaPagos,TRUE,NULL,$tipo_producto,0,$situacion);
//			$cuotas = $this->OrdenDescuentoCuota->cuotasSocioByPeriodo($socio_id,$this->data['OrdenDescuentoCuota']['periodo_ini'],$this->data['OrdenDescuentoCuota']['periodo_fin'],$soloDeuda,$proveedor_id,$codigo_organismo,$discriminaPagos);
		endif;
		$this->set('menuPersonas',$menuPersonas);
		$this->set('socio',$socio);
		$this->set('cuotas',$cuotas);
		
		$this->set('periodos',$periodos);
		$this->set('periodo_ini',$periodo_ini);
		$this->set('periodo_fin',$periodo_fin);
		$this->set('solo_deuda',$soloDeuda);
		$this->set('proveedores',$proveedores);
                
                $this->set('productos',$productos);
                $this->set('situaciones',$situaciones);
                $this->set('organismos',$organismos);
                
		$this->set('proveedor_id',(empty($proveedor_id) ? 0 : $proveedor_id));
		$this->set('codigo_organismo',(empty($codigo_organismo) ? 0 : $codigo_organismo));
                $this->set('tipo_producto',(empty($tipo_producto) ? 0 : $tipo_producto));
		$this->set('situacion',(empty($situacion) ? 0 : $situacion));
                
		if($proveedor_id !=0) $this->set('proveedor_razon_social',$this->requestAction('/proveedores/proveedores/get_razon_social/'.$proveedor_id));
		else $this->set('proveedor_razon_social',null);
		
		if ($this->RequestHandler->isAjax()){
			$this->disableCache();
			$this->render('estado_cuenta_mutual_ajax2');
//            $this->render('estado_cuenta_mutual_ajax');
		}else{
			$this->render('estado_cuenta_mutual2');	
		}
		
	}    
	
	function estado_cuenta_pdf($socio_id,$periodo_d,$periodo_h,$soloDeuda,$proveedor_id=0,$codigo_organismo=0,$discriminaPagos=0){
		$this->OrdenDescuentoCuota->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->OrdenDescuentoCuota->Socio->read(null,$socio_id);
		$this->OrdenDescuentoCuota->unbindModel(array('belongsTo' => array('Socio')));
		$cuotas = $this->OrdenDescuentoCuota->cuotasSocioByPeriodo($socio_id,$periodo_d,$periodo_h,$soloDeuda,$proveedor_id,$codigo_organismo,$discriminaPagos);
//        $cuotas = $this->OrdenDescuentoCuota->procesa_deuda($socio_id,$periodo_d,$periodo_h,$soloDeuda,$proveedor_id,$codigo_organismo,$discriminaPagos);
		
        $this->set('solo_deuda',$soloDeuda);
		$this->set('socio',$socio);
		$this->set('cuotas',$cuotas);
		$this->set('periodo_d',$periodo_d);
		$this->set('periodo_h',$periodo_h);
		$this->set('proveedor_id',$proveedor_id);
		$this->set('codigo_organismo',$codigo_organismo);
		$this->set('discrimina_pagos',$discriminaPagos);
		
		if($proveedor_id !=0) $this->set('proveedor_razon_social',$this->requestAction('/proveedores/proveedores/get_razon_social/'.$proveedor_id));
		else $this->set('proveedor_razon_social',null);
		
		
//		$this->set('resumen',$this->get_resumen_deuda($socio_id));
//		$this->render('reportes/estado_cuenta_pdf','pdf');
		$this->render('reportes/estado_cuenta_apaisado_pdf','pdf');
//        $this->render('reportes/estado_cuenta_apaisado_pdf2','pdf');
	}
	
	function estado_cuenta_pdf2($socio_id,$periodo_d,$periodo_h,$soloDeuda,$proveedor_id=0,$codigo_organismo=0,$discriminaPagos=0,$tipo_producto=NULL){
		$this->OrdenDescuentoCuota->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->OrdenDescuentoCuota->Socio->read(null,$socio_id);
		$this->OrdenDescuentoCuota->unbindModel(array('belongsTo' => array('Socio')));
//		$cuotas = $this->OrdenDescuentoCuota->cuotasSocioByPeriodo($socio_id,$periodo_d,$periodo_h,$soloDeuda,$proveedor_id,$codigo_organismo,$discriminaPagos);
        $cuotas = $this->OrdenDescuentoCuota->procesa_deuda($socio_id,$periodo_d,$periodo_h,$soloDeuda,$proveedor_id,$codigo_organismo,$discriminaPagos,TRUE,NULL,$tipo_producto);
		
        $this->set('solo_deuda',$soloDeuda);
		$this->set('socio',$socio);
		$this->set('cuotas',$cuotas);
		$this->set('periodo_d',$periodo_d);
		$this->set('periodo_h',$periodo_h);
		$this->set('proveedor_id',$proveedor_id);
		$this->set('codigo_organismo',$codigo_organismo);
		$this->set('discrimina_pagos',$discriminaPagos);
		
		if($proveedor_id !=0) $this->set('proveedor_razon_social',$this->requestAction('/proveedores/proveedores/get_razon_social/'.$proveedor_id));
		else $this->set('proveedor_razon_social',null);
		
		
//		$this->set('resumen',$this->get_resumen_deuda($socio_id));
//		$this->render('reportes/estado_cuenta_pdf','pdf');
//		$this->render('reportes/estado_cuenta_apaisado_pdf','pdf');
        $this->render('reportes/estado_cuenta_apaisado_pdf2','pdf');
	}	

	
	function saldo_cuota($cuota_id){
		$return = array();
		$this->OrdenDescuentoCuota->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento')));
		$cuota = $this->OrdenDescuentoCuota->read(null,$cuota_id);
		
		$return['monto_pagado'] = 0;
		if(count($cuota['OrdenDescuentoCobroCuota'])!=0):
			foreach($cuota['OrdenDescuentoCobroCuota'] as $pago){
				$return['monto_pagado'] += $pago['importe'];
			}
		endif;
		$return['saldo_cuota'] = $cuota['OrdenDescuentoCuota']['importe'] - $return['monto_pagado'];
		return $return;
	}
	
	
	function cuotas_by_odescuento($orden_descuento_id,$detallaPagos=0,$habilitarBloqueo=0){
//		Configure::write('debug',0);
		$cuotas = null;
// 		App::import('model','mutual.OrdenDescuentoCuota');
// 		$oODC = new OrdenDescuentoCuota();
// //		$cuotas = $oODC->find('all',array('conditions' => array('OrdenDescuentoCuota.orden_descuento_id' => $orden_descuento_id),'order' => array('OrdenDescuentoCuota.periodo')));
// //		$cuotas = $oODC->armaInfoAdicional($cuotas);
// 		if($habilitarBloqueo == 1) $oODC->habilitarBloqueo = true;
// 		$cuotas = $oODC->cuotasByOrdenDto($orden_descuento_id,true);
		
		
		App::import('model','mutual.OrdenDescuentoCuotaService');
		$oODCS = new OrdenDescuentoCuotaService();
		$cuotas = $oODCS->cuotasByOrdenDto($orden_descuento_id,true);
		
		
		return $cuotas;
	}
	
	function orden_cobro_xcaja($socio_id){
//            $this->set('cuotas',$this->OrdenDescuentoCuota->cuotasAdeudadasBySocio($socio_id,null));
            $this->set('cuotas',$this->OrdenDescuentoCuota->procesa_deuda($socio_id,NULL,'299912',TRUE,NULL,NULL,FALSE,TRUE,'CUOTA'));
            $this->set('socio_id',$socio_id);
            $this->render('orden_cobro_xcaja_sel_importes2');
	}
	
	
	function ver_atraso($socio_id,$periodo,$proveedor_id=0,$codigo_organismo=0){
		$this->OrdenDescuentoCuota->Socio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $this->OrdenDescuentoCuota->Socio->read(null,$socio_id);
		$this->OrdenDescuentoCuota->unbindModel(array('belongsTo' => array('Socio')));
        
        $cuotas = $this->OrdenDescuentoCuota->procesa_deuda($socio_id,$periodo,$periodo,TRUE,0,null,true,false); 
        $cuotas = Set::extract('/estado_cuenta[periodo<'.$periodo.']',$cuotas);
//        $cuotas =  Set::extract('{n}.estado_cuenta',$cuotas);
//        debug($cuotas);
//        exit;
        
//		$cuotas = $this->OrdenDescuentoCuota->atrasoSocioByPeriodo($socio_id,$periodo,$proveedor_id,$codigo_organismo);
		$this->set('socio',$socio);
		$this->set('cuotas',$cuotas);		
		$this->set('periodo',$periodo);
		$this->set('proveedor_id',$proveedor_id);
		if($proveedor_id !=0) $this->set('proveedor_razon_social',$this->requestAction('/proveedores/proveedores/get_razon_social/'.$proveedor_id));
		else $this->set('proveedor_razon_social',null);
		$this->set('codigo_organismo',$codigo_organismo);
        $this->render('ver_atraso2');
		
	}

	function cambiar_situacion($persona_id = null){

//		App::import('model','Pfyj.Persona');
//		$this->Persona = new Persona();	
//
//		if(!empty($persona_id)){
//			
//			$persona = $this->Persona->read(null,$persona_id);
//			$this->set('persona',$persona);
//			
//			if(!empty($this->data)){
//				if(isset($this->data['OrdenDescuentoCuota']['check_id'])){
//					if(!$this->OrdenDescuentoCuota->cambiarSituacionCuotas($this->data['OrdenDescuentoCuota']['check_id'],$this->data['OrdenDescuentoCuota']['situacion'],$this->data['OrdenDescuentoCuota']['observaciones'],$persona['Socio']['id'])){
//						$this->Mensaje->error("Se produjo un error al cambiar la situación");
//					}
//					$this->redirect('cambiar_situacion/'.$persona_id);
//				}
//			}
//
//			$ordenes = null;
//			if(isset($persona['Socio']['id']) && !empty($persona['Socio']['id'])){
//				App::import('Model','Mutual.OrdenDescuento');
//				$oOrden = new OrdenDescuento();
//				$ordenes = $oOrden->OrdenesBySocioBySituacion($persona['Socio']['id']);
//			}
//			$this->set('ordenes',$ordenes);			
//			$this->render('cambiar_situacion_form');
//			
//		}else{
//			$this->render('cambiar_situacion');			
//			
//		}
        
        $cuotas = null;
        $orden_descuento_id = null;
        $persona = null;
        
        if(!empty($this->data)){
            
			$orden_descuento_id = $this->data['OrdenDescuento']['aprox_id'];

            App::import('Model','Mutual.OrdenDescuentoCuota');
            $oCUOTA = new OrdenDescuentoCuota();              
            
            if(isset($this->data['OrdenDescuentoCuota']['check_id'])){
                if(!$oCUOTA->cambiarSituacionCuotas($this->data['OrdenDescuentoCuota']['check_id'],$this->data['OrdenDescuentoCuota']['situacion'],$this->data['OrdenDescuentoCuota']['observaciones'])){
                    $this->Mensaje->error("Se produjo un error al cambiar la situación");
                }
            }            
            

            
            $cuotas = $oCUOTA->cuotasAdeudadasByOrdenDto($orden_descuento_id,null);
            
            App::import('Model','Mutual.OrdenDescuento');
            $oOrden = new OrdenDescuento(); 
            $persona = $oOrden->getPersonaByOrdenDescuento($orden_descuento_id);
            

            
            
        }
        $this->set('orden_descuento_id',$orden_descuento_id);
        $this->set('cuotas',$cuotas);
        $this->set('persona',$persona);
        $this->render('cambiar_situacion_orden');
		
	}
	
	function modificar_cuota($orden_descuento_cuota_id = null){
		if(empty($orden_descuento_cuota_id)) parent::noAutorizado();
		
		if(!empty($this->data)){
                    
			if($this->OrdenDescuentoCuota->modificar_cuota($this->data)){
				$this->redirect('/mutual/orden_descuentos/carga_deuda/'.$this->data['OrdenDescuentoCuota']['orden_descuento_id']);
			}else{
				$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GENERAR LA CUOTA!");
			}
		}
		
		$cuota = $this->OrdenDescuentoCuota->getCuota($orden_descuento_cuota_id);
//		debug($cuota);
		$this->set('cuota',$cuota);
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
		$orden = $oORDEN->read(null,$cuota['OrdenDescuentoCuota']['orden_descuento_id']);
		$periodo_ini = $orden['OrdenDescuento']['periodo_ini'];
		$anio_ini = substr($periodo_ini,0,4);
		$this->set('orden_descuento_id',$cuota['OrdenDescuentoCuota']['orden_descuento_id']);
		$this->set('orden_descuento_cuota_id',$orden_descuento_cuota_id);
		$this->set('anio_ini',$anio_ini);
		$this->set('orden',$orden);
	}
	
	
	function agregar_cuota($orden_descuento_id = null){
		
		
		if(empty($orden_descuento_id)) parent::noAutorizado();
		
		if(!empty($this->data)){
			
//			$this->data['OrdenDescuentoCuota']['periodo'] = $this->data['OrdenDescuentoCuota']['periodo']['year'].$this->data['OrdenDescuentoCuota']['periodo']['month'];
			$this->data['OrdenDescuentoCuota']['periodo'] = $this->data['Liquidacion']['periodo'];
			$this->data['OrdenDescuentoCuota']['vencimiento'] = $this->OrdenDescuentoCuota->armaFecha($this->data['OrdenDescuentoCuota']['vencimiento']);
			$mkTvtoSocio = mktime(0,0,0,date('m',strtotime($this->data['OrdenDescuentoCuota']['vencimiento'])),date('d',strtotime($this->data['OrdenDescuentoCuota']['vencimiento'])),date('Y',strtotime($this->data['OrdenDescuentoCuota']['vencimiento'])));
			$this->data['OrdenDescuentoCuota']['vencimiento_proveedor'] = date('Y-m-d',$this->OrdenDescuentoCuota->addDayToDate($mkTvtoSocio,10));
			$this->data['OrdenDescuentoCuota']['estado'] = 'A';
			$this->data['OrdenDescuentoCuota']['situacion'] = 'MUTUSICUMUTU';
			$glb = $this->OrdenDescuentoCuota->getGlobalDato('concepto_2',$this->data['OrdenDescuentoCuota']['tipo_cuota']);
			$signo = $glb['GlobalDato']['concepto_2']; 
			$signo = trim($signo);
			
			if(empty($signo)) $signo = '+';
			
			if($signo == '-') $this->data['OrdenDescuentoCuota']['importe'] = abs($this->data['OrdenDescuentoCuota']['importe']) * (-1);
			
			//chequear que no exista la cuota
			if($this->OrdenDescuentoCuota->checkExisteCuota($this->data)):
			
				if($this->OrdenDescuentoCuota->agregarCuota($this->data)){
					
					$this->redirect('/mutual/orden_descuentos/carga_deuda/'.$orden_descuento_id);
					
				}else{
					
					$this->Mensaje->error("SE PRODUJO UN ERROR AL INTENTAR GENERAR LA CUOTA.");
					
				}
			else:
			
				$this->Mensaje->error("Verifique si la cuota no existe para el Proveedor, Período, Concepto y Número de Cuota");
			
			endif;
		}
		
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
		$orden = $oORDEN->getOrden($orden_descuento_id);
		$periodo_ini = $orden['OrdenDescuento']['periodo_ini'];
		$anio_ini = substr($periodo_ini,0,4);
		$this->set('orden_descuento_id',$orden_descuento_id);
		$this->set('anio_ini',$anio_ini);
		$this->set('orden',$orden);
		
	}
	
	function get_resumen_deuda($socio_id){
		return $this->OrdenDescuentoCuota->resumenDeDeuda($socio_id);
	}
	
    function ente_recaudador($socio_id){
        $this->set('menuPersonas',1);
        App::import('Model','pfyj.Socio');
        $oSOCIO = new Socio();
		$oSOCIO->bindModel(array('belongsTo' => array('Persona')));
		$socio = $oSOCIO->read(null,$socio_id);
		
		if(empty($socio)) $this->redirect('/pfyj/personas');
        $this->set('socio',$socio);
        
        if(!empty($this->data)){
            if($this->data['OrdenDescuentoCuota']['tipo_reporte'] == 1) $datos = $oSOCIO->get_estado_cuenta_ente_recaudador($socio, $this->data['OrdenDescuentoCuota']['ente_recaudador']);
            if($this->data['OrdenDescuentoCuota']['tipo_reporte'] == 2) $datos = $oSOCIO->get_liquidacion_deuda_ente_recaudador($socio, $this->data['OrdenDescuentoCuota']['ente_recaudador']);
            $this->set('datos',$datos);
        }
        
		if ($this->RequestHandler->isAjax()){
			$this->disableCache();
			if($this->data['OrdenDescuentoCuota']['tipo_reporte'] == 1) $this->render('estado_cuenta_ente_recaudador_ajax');
            if($this->data['OrdenDescuentoCuota']['tipo_reporte'] == 2) $this->render('estado_cuenta_ente_recaudador_liquidacion_deuda_ajax');
		}else{
			$this->render('estado_cuenta_ente_recaudador');	
		}        

    }
    
	
//	function reversos($socio_id = null){
//		
//		App::import('Model','Pfyj.Socio');
//		$oSOCIO = new Socio();
//
//		if(empty($socio_id)) parent::noDisponible();
//		
//		$oSOCIO->bindModel(array('belongsTo' => array('Persona')));
//		
//		$socio = $oSOCIO->read(null,$socio_id);
//		
//		if(empty($socio)) parent::noDisponible();
//		
//		$this->set('socio',$socio);
//		
//		App::import('model','Mutual.OrdenDescuentoCobroCuota');
//		$oCCUOTA = new OrdenDescuentoCobroCuota();				
//		
//		if(!empty($this->data)):
//
//			
//			if(!empty($this->data['OrdenDescuentoCobroCuota']['check_id'])):
//				$error = false;
//				$periodo = $this->data['OrdenDescuentoCobroCuota']['periodo_proveedor_reverso']['year'].$this->data['OrdenDescuentoCobroCuota']['periodo_proveedor_reverso']['month'];
//				foreach($this->data['OrdenDescuentoCobroCuota']['check_id'] as $id => $chk):
//					if(!$oCCUOTA->reversarCobro($id,$periodo)):
//						$error = true;
//						break;
//					endif;
//				endforeach;
//				
//				if(!$error):
//					$this->redirect("/mutual/orden_descuento_cuotas/estado_cuenta/".$socio_id."/1");
//				else:
//					$this->Mensaje->error("SE PRODUJO UN ERROR AL PROCESAR LOS REVERSOS!");
//				endif;
//			
//			endif;
//			
//		endif;
//		
//		$cobros = $oCCUOTA->getCuotasBySocioByTipoPago($socio_id,'MUTUTCOBRECS');
//		$this->set('cobros',$cobros);		
//		
//
//	}
	
	

	
	
}
?>
<?php

class VendedoresController extends VentasAppController{

	var $name = 'vendedores';
	
	var $autorizar = array('imprimir_remito','ficha_remito','get_vendedores_list','listado_remitos_vendedor');
	
	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();
	}	
	
	function index(){
		
		$vendedores = $this->Vendedor->getVendedores();
		$this->set('vendedores',$vendedores);
		
		
	}
	
	function padron($vendedor_id = null){
		if(empty($vendedor_id)) $this->redirect('index');
		$vendedor = $this->Vendedor->getVendedor($vendedor_id);
		if(empty($vendedor)) parent::noDisponible();
                
                if(!empty($this->data)){
//                    
//                    $vendedor['Vendedor']['consultar_deuda'] = 0;
//                    $vendedor['Vendedor']['consultar_intranet'] = 0;
//                    
//                    if(isset($this->data['Vendedor']['consultar_deuda'])){
//                        $vendedor['Vendedor']['consultar_deuda'] = 1;
//                    }
//                    if(isset($this->data['Vendedor']['consultar_intranet'])){
//                        $vendedor['Vendedor']['consultar_intranet'] = 1;
//                    }  
                    if(isset($this->data['Vendedor']['supervisor_id'])){
                        $vendedor['Vendedor']['supervisor_id'] = $this->data['Vendedor']['supervisor_id'];
                    }
                    
                    if(isset($this->data['Vendedor']['mail_contacto'])){
                        $vendedor['Vendedor']['mail_contacto'] = $this->data['Vendedor']['mail_contacto'];
                    } 
                    
                    $this->Vendedor->save($vendedor);
                    $this->redirect('padron/' . $vendedor_id);
               }
                
                //debug($vendedor);
		$this->set('vendedor',$vendedor);
	}
	
	
	function alta(){
//		if(!empty($this->data)){
//			$vendedorID = $this->Vendedor->alta($this->data);
//			if(!empty($vendedorID)) $this->redirect('padron/' . $vendedorID);
//		}
            $persona  = null;
            $cuit_cuil = null;
            if(!empty($this->data)){
                if(isset($this->data['Persona']['cuit_cuil']) && isset($this->data['Persona']['process_cuitcuil'])){
                    $cuit_cuil = $this->data['Persona']['cuit_cuil'];
                    $persona = $this->Vendedor->get_persona_by_cuit($cuit_cuil);
                    if(!empty($persona)){
                        $vendedor = $this->Vendedor->getByPersonaId($persona['Persona']['id']);
                        if(!empty($vendedor)) $this->redirect('padron/' . $vendedor[0]['Vendedor']['id']);
                    }    
                }
                if(isset($this->data['Persona']['id']) && !empty($this->data['Persona']['id'])){
                    $vendedorID = $this->Vendedor->alta_nueva($this->data['Persona']['id']);
                    if(!$vendedorID){
                        $this->Mensaje->errores("SE DETECTARON LOS SIGUIENTES ERRORES:",$this->Vendedor->notificaciones);
                    }else{
                         $this->redirect('padron/' . $vendedorID);
                    }
                }
            }
            $this->set('cuit_cuil',$cuit_cuil); 
            $this->set('persona',$persona);            
            $this->render('alta_nueva');    
	}
	
	function planes($vendedor_id = null){
		if(empty($vendedor_id)) $this->redirect('index');
		$vendedor = $this->Vendedor->getVendedor($vendedor_id);
		if(empty($vendedor)) parent::noDisponible();
		$this->set('vendedor',$vendedor);
		$this->set('planes',$this->Vendedor->getPlanes($vendedor_id));
		
	}	
	
	function nuevo_plan($vendedor_id = null){
		if(empty($vendedor_id)) $this->redirect('index');
		$vendedor = $this->Vendedor->getVendedor($vendedor_id);
		if(empty($vendedor)) parent::noDisponible();
		
		if(!empty($this->data)){
			if($this->Vendedor->cargarComision($this->data)) $this->redirect('planes/' . $vendedor_id);
			else $this->Mensaje->error("SE PRODUJO UN ERROR AL CARGAR LA COMISION");
		}
		
		$this->set('vendedor',$vendedor);
// 		$this->set('planes',$this->requestAction('/proveedores/proveedor_planes/get_todos_planes_vigentes/0/1/1'));
		$planes = $this->Vendedor->getPlanesDisponibles($vendedor_id);
		if(empty($planes)){
			$this->Mensaje->error("NO EXISTEN PLANES VIGENTES NO ASIGNADOS AL VENDEDOR");
			$this->redirect('planes/' . $vendedor_id);
		}
		$this->set('planes',$planes);
	}
	
	
	function borrar_plan($plan_id = null){
		if(empty($plan_id)) $this->redirect('index');
		$plan = $this->Vendedor->getComision($plan_id);
		if(empty($plan)) parent::noDisponible();
		if(!$this->Vendedor->borrarComision($plan_id)){
			$this->Mensaje->error("SE PRODUJO UN ERROR AL BORRAR LA COMISION");
		}
		$this->redirect('planes/' . $plan['VendedorProveedorPlan']['vendedor_id']);
	}
	
	function modificar_plan($plan_id = null){
		if(empty($plan_id)) $this->redirect('index');
		$comision = $this->Vendedor->getComision($plan_id);
		if(empty($comision)) parent::noDisponible();
		$vendedor = $this->Vendedor->getVendedor($comision['VendedorProveedorPlan']['vendedor_id']);
		if(empty($vendedor)) parent::noDisponible();	
// 		debug($comision);	
		$this->set('comision',$comision);
		$this->set('planes',$this->requestAction('/proveedores/proveedor_planes/get_todos_planes_vigentes/0/1/1'));
		$this->set('vendedor',$vendedor);
		if(!empty($this->data)){
			debug($this->data);
			if($this->Vendedor->cargarComision($this->data)) $this->redirect('planes/' . $comision['VendedorProveedorPlan']['vendedor_id']);
			else $this->Mensaje->error("SE PRODUJO UN ERROR AL GUARDAR LA COMISION");
		}
	}
	
	function desactivar_plan($campo,$id,$option){
		Configure::write('debug',0);
		$comision = $this->Vendedor->getComision($id);
		if(parent::isAuthorized()){
			$comision['VendedorProveedorPlan']['activo'] = $option;
			$this->Vendedor->cargarComision($comision);
			echo $option;
		}else{
			echo $comision['VendedorProveedorPlan']['activo'];
		}
		exit;
	}	
	
	
	function bandeja(){
		$solicitudes = null;
		$vendedor = null;
		
// 		if(!empty($remito_id)){
// 			App::import('model','ventas.VendedorRemito');
// 			$oVENDEDORREMITO = new VendedorRemito();
// 			$remito = $oVENDEDORREMITO->cargarRemito($remito_id);
// 			$this->set('vendedores',$this->Vendedor->getVendedores(true));
// 			$this->set('remito',$remito);
// 			$this->render('bandeja_remito');
				
// 		}
		
		if(!empty($this->data)){
			
			if(isset($this->data['VendedorRemito']['generar_remito']) && !empty($this->data['VendedorRemito']['generar_remito']) && !empty($this->data['VendedorRemito']['solicitud_id'])){
				App::import('model','ventas.VendedorRemito');
				$oVENDEDORREMITO = new VendedorRemito();
				$remito = $oVENDEDORREMITO->generar($this->data);
				if(!empty($remito)){
					$this->redirect("ficha_remito/" . $remito['VendedorRemito']['id']);
				}else{
					$this->Mensaje->errores("SE PRODUJERON LOS SIGUIENTES ERRORES:",$oVENDEDORREMITO->notificaciones);
				}
			}	
			if(isset($this->data['VendedorBandeja']['btn_presentar'])){
				$solicitudes = $this->Vendedor->getSolicitudes($this->data['VendedorBandeja']['vendedor_id']);
				$vendedor = $this->Vendedor->getVendedor($this->data['VendedorBandeja']['vendedor_id']);

				$this->set('solicitudes',$solicitudes);
				$this->set('vendedor',$vendedor);	
				$this->set('vendedores',$this->Vendedor->getVendedores(true));
				$this->render('bandeja');
			}
			if(isset($this->data['VendedorBandeja']['btn_ver_remitos'])){
				$vendedor = $this->Vendedor->getVendedor($this->data['VendedorBandeja']['vendedor_id']);
				$this->set('vendedor',$vendedor);
				$this->set('vendedores',$this->Vendedor->getVendedores(true));
				
				App::import('model','ventas.VendedorRemito');
				$oVENDEDORREMITO = new VendedorRemito();				
				$this->set('remitos',$oVENDEDORREMITO->getRemitosByVendedor($this->data['VendedorBandeja']['vendedor_id'],false));
				$this->render('bandeja_remitos');

			}
			
			
			
		}
		
// 		$this->set('solicitudes',$solicitudes);
// 		$this->set('vendedor',$vendedor);
		$this->set('vendedores',$this->Vendedor->getVendedores(true));
	}
	
	
	function ficha_remito($remito_id = null){
		if(empty($remito_id)) parent::noDisponible();
		App::import('model','ventas.VendedorRemito');
		$oVENDEDORREMITO = new VendedorRemito();
		$remito = $oVENDEDORREMITO->cargarRemito($remito_id);
		if(empty($remito)) parent::noDisponible();
		$this->set('vendedores',$this->Vendedor->getVendedores(true));
		$this->set('remito',$remito);
		$this->render('bandeja_remito');
	}
	
	
	function imprimir_remito($remito_id = null){
		App::import('model','ventas.VendedorRemito');
		$oVENDEDORREMITO = new VendedorRemito();
		$this->set('remito',$oVENDEDORREMITO->cargarRemito($remito_id));
		
		$this->render('imprimir_remito_pdf','pdf');
	}
	
	
	function listados($tipoReporte=null,$menuVendedores = 0){
		$this->set('menuVendedores',$menuVendedores);
		if($tipoReporte == "XLS"){
                    
                    if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){
                        App::import('model','Shells.Asincrono');
                        $oASINC = new Asincrono();
                        $asinc = $oASINC->read('p1,p2,p3,p4,p6,p9',$this->params['url']['pid']);  
                        $this->redirect('/mutual/listados/download/'.$asinc['Asincrono']['p9']);                
                    }                    
                    
//			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
//			$pid = $this->params['url']['pid'];
//
//			App::import('model','Mutual.ListadoService');
//			$oListado = new ListadoService();
//
//			$columnas = array(
//					'texto_16' => 'USUARIO',
//					'texto_17' => 'FECHA_EMISION',
//					'texto_1' => 'VENDEDOR',
//					'texto_2' => 'PLAN',
//					'texto_3' => 'SOLICITUD',
//					'texto_4' => 'FECHA',
//					'texto_5' => 'ESTADO',
//					'texto_6' => 'REASIGNADA',
//					'texto_7' => 'SOLICITANTE',
//					'decimal_1' => 'CAPITAL',
//					'decimal_2' => 'SOLICITADO',
//					'entero_1' => 'CUOTAS',
//					'decimal_3' => 'IMPORTE_CUOTA',
//					'decimal_4' => 'TOTAL',
//					'texto_8' => 'ORGANISMO',
//					'texto_9' => 'EMPRESA_TURNO',
//					'texto_10' => 'BENEFICIO',
//					'texto_11' => 'ORDEN DTO',
//                                        'entero_4' => 'NRO ORDEN DTO',
//					'texto_12' => 'FECHA_ORDEN',
//					'texto_13' => 'PROVEEDOR_PRODUCTO',
//					'texto_14' => 'INICIA_EN',
//					'texto_15' => 'APROBADA_POR',
//                                        'decimal_9' => 'COBRADO_OPTIMO',        
//                                        'decimal_8' => 'COBRADO_REAL',        
//                                        'decimal_5' => 'SALDO',
//                                        'decimal_6' => 'PENDIENTE_ACREDITAR',
//                                        'decimal_7' => 'SALDO_NETO',
//                                        'decimal_15' => 'SALDO_AVENCER',
//                                        'entero_3' => 'CUOTAS_ADEUDADAS',
//                                        'entero_6' => 'CUOTAS_AVENCER',
//                                        'entero_5' => 'PERIODO_CORTE',
//                                        'decimal_10' => 'VENC_0_3_MESES', 
//                                        'decimal_11' => 'VENC_3_6_MESES', 
//                                        'decimal_12' => 'VENC_6_9_MESES', 
//                                        'decimal_13' => 'VENC_9_12_MESES', 
//                                        'decimal_14' => 'VENC_+12_MESES', 
//                                        'decimal_16' => 'AVENC_3_MESES', 
//                                        'decimal_17' => 'AVENC_6_MESES', 
//                                        'decimal_19' => 'AVENC_12_MESES', 
//                                        'decimal_20' => 'AVENC_+12_MESES',                            
//			);
//				
//			$order = array('AsincronoTemporal.entero_2,AsincronoTemporal.texto_7');
//			$conditions = array('AsincronoTemporal.clave_3' => 'REPORTE_1','AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
//			$datos = $oListado->getDetalleToExcel($conditions,$order,$columnas);
//			$this->set('datos',$datos);
//                        
//                        $columnas2 = array(
//                                        'clave_2' => 'USUARIO',
//					'texto_1' => 'VENDEDOR',
//                                        'texto_3' => 'SOCIO_SOLICITUD',
//					'texto_2' => 'TIPO',                          
//					'texto_4' => 'FECHA',
//					'texto_5' => 'APROBADA',
//					'texto_6' => 'DOCUMENTO',
//					'texto_7' => 'SOLICITANTE',
//			);                        
//                        
//                        $order = array('AsincronoTemporal.entero_2,AsincronoTemporal.texto_7');
//			$conditions = array('AsincronoTemporal.clave_3' => 'REPORTE_2','AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
//			$datosSocios = $oListado->getDetalleToExcel($conditions,$order,$columnas2);
//                        $this->set('datosSocios',$datosSocios);
//                        
//			$this->render('listado_xls','blank');
//			return;			

		}
		
		$show_asincrono = 0;
		$fecha_desde = $fecha_hasta = $fecha_corte = date("Y-m-d");
                $periodo_corte = date('Ym');

		
		if(!empty($this->data)){
			$show_asincrono = 1;
			$fecha_desde = $this->Vendedor->armaFecha($this->data['MutualProductoSolicitud']['fecha_desde']);
			$fecha_hasta = $this->Vendedor->armaFecha($this->data['MutualProductoSolicitud']['fecha_hasta']);
                        $periodo_corte = $this->data['MutualProductoSolicitud']['periodo_corte']['year'].$this->data['MutualProductoSolicitud']['periodo_corte']['month'];
                        $fecha_corte = $this->data['MutualProductoSolicitud']['periodo_corte']['year'].$this->data['MutualProductoSolicitud']['periodo_corte']['month'];
		}
		$userLogin = $this->Seguridad->user();
		$this->set('show_asincrono',$show_asincrono);
		$this->set('fecha_desde',$fecha_desde);
		$this->set('fecha_hasta',$fecha_hasta);
                $this->set('periodo_corte',$periodo_corte);
                $this->set('fecha_corte',$fecha_corte);
                $this->set('vendedor_id',$userLogin['Usuario']['vendedor_id']);
		$this->set('vendedores',$this->Vendedor->getVendedores(true));
	}
	
        
        function get_vendedores_list($soloActivos = 0){
            return $this->Vendedor->getVendedores(true,($soloActivos == '1' ? TRUE : FALSE));
        }
        
        function listado_remitos_vendedor(){
            
            $remitos = NULL;
            
            if(!empty($this->data)){
                
                App::import('model','ventas.SolicitudService');
                $oSSERVICE = new SolicitudService();
                $fecha_desde = $oSSERVICE->armaFecha($this->data['VendedorRemito']['fecha_desde']);
                $fecha_hasta = $oSSERVICE->armaFecha($this->data['VendedorRemito']['fecha_hasta']);
                
                $remitos = $oSSERVICE->get_remitos($fecha_desde,$fecha_hasta);
                
                $this->set('remitos',$remitos);
            }
            
        }
        
        
}
?>
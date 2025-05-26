<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage controller
 */

class ListadosController extends MutualAppController{
	
	var $name = 'Listados';
	var $uses =  array('Mutual.ListadoService','Shells.Asincrono');
	
	var $autorizar = array(
							'ordenes_dto_por_fecha',
							'consumos_por_fecha',
							'cancelaciones_por_fecha',
							'cobros_por_fecha',
							'reporte_proveedores',
							'reporte_liquidacion_deuda',
							'reporte_imputacion_deuda',
							'reporte_inaes',
							'listado_deuda',
							'padron_servicios',
							'reporte_inaesA9',
							'listado_reintegros',
							'reporte_inaesbe',
                            'download',
                            'reporte_liquidacion_deuda3'
	);
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}		
	
	function index(){
		$this->redirect('ordenes_dto_por_fecha');
	}
	
	function ordenes_dto_por_fecha($tipoReporte=null){
		$disableForm = 0;
		$showAsincrono = 0;
		
                $periodo_corte = date('Ym');
                $fecha_corte = date("Y-m-d");
		
		$this->set('fecha_desde',date('Y-m-d'));
		$this->set('fecha_hasta',date('Y-m-d'));
		$this->set('optionList',0);
		$this->set('selectedStr',0);
		$this->set('tipoReporte',"PDF");
                $this->set('periodo_corte',$periodo_corte);
                $this->set('fecha_corte',$fecha_corte);
		
		if(!empty($this->data)){
                    
//                        debug($this->data);
                        
                        $tipoProductoOptions = explode("|", $this->data['ListadoService']['tipo_producto_mutual_producto_id']);
//                        debug($tipoProductoOptions);
                        
			$disableForm = 1;
			$showAsincrono = 1;
                        $this->set('selectedStr',$this->data['ListadoService']['selected_str']);
			$this->set('fecha_desde',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_desde']));
			$this->set('fecha_hasta',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_hasta']));
			$this->set('tipoReporte',$this->data['ListadoService']['tipo_reporte']);
                        $this->set('proveedorId',$this->data['ListadoService']['proveedor_id']);
                        $this->set('codigoOrganismo',$this->data['ListadoService']['codigo_organismo']);    
//                        $this->set('periodo_corte',$this->data['ListadoService']['periodo_corte']['year'].$this->data['ListadoService']['periodo_corte']['month']);
                        $this->set('tipo_producto',$tipoProductoOptions[1]);
                        $this->set('optionList',$this->data['ListadoService']['tipo_producto_mutual_producto_id']);
                        
                        $periodo_corte = $this->data['ListadoService']['periodo_corte']['year'].$this->data['ListadoService']['periodo_corte']['month'];
                        $fecha_corte = $this->data['ListadoService']['periodo_corte']['year'].'-'.$this->data['ListadoService']['periodo_corte']['month'].'-01';

                        $this->set('periodo_corte',$periodo_corte);
                        $this->set('fecha_corte',$fecha_corte);                         
                        
		}
		$this->set('tipos_orden_dto',$this->tiposOrdenDto);	
		$this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);
		
		
		
		if($tipoReporte == "PDF"){
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
			$this->set('datos',$this->ListadoService->getTemporal($pid,false,array('AsincronoTemporal.texto_1,AsincronoTemporal.texto_14')));
			$asinc = $this->Asincrono->read('p1,p2',$pid);
			$this->set('fecha_desde',$asinc['Asincrono']['p1']);
			$this->set('fecha_hasta',$asinc['Asincrono']['p2']);
			$this->render('orden_descuentos/reportes/entre_fechas_pdf','pdf');
			return;
		}
		
		if($tipoReporte == "XLS"){
			
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];

			$columnas = array(
				'texto_1' => 'TIPO',
				'texto_2' => 'ORDEN #',
                                'texto_16' => 'ESTADO',
				'texto_11' => 'FECHA',
                                'texto_17' => 'NRO_SOCIO',
				'texto_3' => 'TIPO_NRO',
				'texto_13' => 'DOCUMENTO',
				'texto_14' => 'BENEFICIARIO',
                                'texto_18' => 'CUIT_CUIL',
				'texto_15' => 'PROVEEDOR',
				'texto_5' => 'PROVEEDOR_PRODUCTO',
				'texto_10' => 'ORGANISMO',
				'texto_6' => 'INICIA_EN',
				'texto_7' => '1ER_VTO',
				'decimal_1' => 'TOTAL',
				'entero_1' => 'CUOTAS',
				'decimal_2' => 'IMPORTE_CUOTA',
				'texto_9' => 'PERM',
                                'decimal_4' => 'DEVENGADO',
                                'decimal_7' => 'PAGADO',
                                'decimal_6' => 'ANULADO',
                                'decimal_5' => 'SALDO',
			
			);
			
			$order = array('AsincronoTemporal.texto_1,AsincronoTemporal.texto_14');
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
			$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
			
			$this->set('datos',$datos);	
			$asinc = $this->Asincrono->read('p1,p2',$this->params['url']['pid']);
			$this->set('fecha_desde',$asinc['Asincrono']['p1']);
			$this->set('fecha_hasta',$asinc['Asincrono']['p2']);
			
			
			$this->render('orden_descuentos/reportes/entre_fechas_xls','blank');
			return;
			
		}
		
		$this->render('orden_descuentos/entre_fechas');
		
	}
	
	
	function consumos_por_fecha($tipoReporte=null){
		$disableForm = 0;
		$showAsincrono = 0;
                
                $periodo_corte = date('Ym');
                $fecha_corte = date("Y-m-d");
		
		$this->set('fecha_desde',date('Y-m-d'));
		$this->set('fecha_hasta',date('Y-m-d'));
		$this->set('optionList',0);
		$this->set('selectedStr',0);
		$this->set('tipoReporte',"PDF");
                $this->set('periodo_corte',$periodo_corte);
                $this->set('fecha_corte',$fecha_corte); 
		
		if(!empty($this->data)){
			$disableForm = 1;
			$showAsincrono = 1;
                        
                        $tipoProductoOptions = explode("|", $this->data['ListadoService']['tipo_producto_mutual_producto_id']);
                        
			$this->set('fecha_desde',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_desde']));
			$this->set('fecha_hasta',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_hasta']));
			$this->set('optionList',$this->data['ListadoService']['tipo_producto_mutual_producto_id']);
			$this->set('selectedStr',$this->data['ListadoService']['selected_str']);
			$this->set('tipoReporte',$this->data['ListadoService']['tipo_reporte']);
                        $this->set('proveedorId',$this->data['ListadoService']['proveedor_id']);
                        $this->set('codigoOrganismo',$this->data['ListadoService']['codigo_organismo']);  
                        $periodo_corte = $this->data['MutualProductoSolicitud']['periodo_corte']['year'].$this->data['MutualProductoSolicitud']['periodo_corte']['month'];
                        $fecha_corte = $this->data['MutualProductoSolicitud']['periodo_corte']['year'].'-'.$this->data['MutualProductoSolicitud']['periodo_corte']['month'].'-01';
            
                        $this->set('periodo_corte',$periodo_corte);
                        $this->set('fecha_corte',$fecha_corte);                        
                        
                        $this->set('tipo_producto',$tipoProductoOptions[1]);
                        
		}
		$this->set('tipos_orden_dto',$this->tiposOrdenDto);	
		$this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);
			
		if($tipoReporte == "PDF"){
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
			$this->set('datos',$this->ListadoService->getTemporal($pid));
			$asinc = $this->Asincrono->read('p1,p2',$pid);
			$this->set('fecha_desde',$asinc['Asincrono']['p1']);
			$this->set('fecha_hasta',$asinc['Asincrono']['p2']);
			$this->render('mutual_producto_solicitudes/reportes/entre_fechas_pdf','pdf');
			return;
		}
		
		if($tipoReporte == "XLS"){
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
			
			$columnas = array(
			
				'texto_7' => 'PRODUCTO',
				'clave_1' => 'SOLICITUD',
				'clave_2' => 'ORDEN_DTO',
				'texto_3' => 'FECHA',
				'texto_4' => 'FECHA_PAGO',
				'texto_8' => 'INICIA',
				'texto_20' => 'NRO_SOCIO',
				'texto_5' => 'BENEFICIARIO',
				'texto_19' => 'CUIT_CUIL',
				'texto_10' => 'ORGANISMO',
				'texto_15' => 'TURNO_PAGO',
				'texto_6' => 'BENEFICIO',
				'decimal_1' => 'IMPORTE_TOTAL',
				'entero_1' => 'CUOTAS',
				'decimal_2' => 'IMPORTE_CUOTA',	
				'texto_16' => 'VENDEDOR_NRO',
				'texto_17' => 'VENDEDOR_CUIT',
				'texto_18' => 'VENDEDOR_APENOM', 
				'decimal_9' => 'COBRADO_OPTIMO',        
				'decimal_8' => 'COBRADO_REAL',        
				'decimal_5' => 'SALDO',
				'decimal_6' => 'PENDIENTE_ACREDITAR',
				'decimal_7' => 'SALDO_NETO',
				'decimal_15' => 'SALDO_AVENCER',
				'entero_3' => 'CUOTAS_ADEUDADAS',
				'entero_6' => 'CUOTAS_AVENCER',
				'entero_5' => 'PERIODO_CORTE',
				'decimal_10' => 'VENC_0_3_MESES', 
				'decimal_11' => 'VENC_3_6_MESES', 
				'decimal_12' => 'VENC_6_9_MESES', 
				'decimal_13' => 'VENC_9_12_MESES', 
				'decimal_14' => 'VENC_+12_MESES', 
				'decimal_16' => 'AVENC_3_MESES', 
				'decimal_17' => 'AVENC_6_MESES', 
				'decimal_19' => 'AVENC_12_MESES', 
				'decimal_20' => 'AVENC_+12_MESES', 
				'texto_1' => 'USUARIO'                            
			);
			
			$order = array('AsincronoTemporal.texto_7,AsincronoTemporal.texto_14');
			
			
			
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
			$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
			$this->set('datos',$datos);				
			
			$this->render('mutual_producto_solicitudes/reportes/entre_fechas_xls','blank');
			return;
			
		}
		

		$this->render('mutual_producto_solicitudes/entre_fechas');
		

	}
	
	
	function padron_servicios($tipo_salida=null){
		
		$disableForm = 0;
		$showAsincrono = 0;
		
		$fechaCoberturaDesde = date('Y-m-1');
		$this->set('fecha_cobertura_desde',$fechaCoberturaDesde);
		
		App::import('Model','mutual.MutualServicio');
		$oSERV = new MutualServicio();
		
		
		if(!empty($this->data)):
		
			$disableForm = 1;
			$showAsincrono = 1;
			$this->set('fecha_cobertura_desde',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_cobertura_desde']));
			
			
			$selected = explode("|",$this->data['ListadoService']['tipo_servicio_mutual_producto_id']);
			
			$servicio_id = $selected[0];
			
			$this->set('servicio_desc',$oSERV->getNombreProveedorServicio($servicio_id));
			
			$this->set('servicio_id',$servicio_id);
		
		
		endif;
		
		if($tipo_salida == 'XLS'):
		
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
            
            
			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();
						
			$asinc = $oASINC->read('p6',$this->params['url']['pid']);
            
            $this->redirect('/mutual/listados/download/'.$asinc['Asincrono']['p6']);            
			
//			$columnas = array(
//								'texto_1' => 'TIPO',
//								'texto_2' => 'DOCUMENTO',
//								'texto_3' => 'APELLIDO_NOMBRE',
//								'texto_4' => 'SEXO',
//								'texto_16' => 'FECHA_NACIMIENTO',
//								'texto_5' => 'CALLE',
//								'texto_6' => 'NRO',
//								'texto_7' => 'PISO',
//								'texto_8' => 'DPTO',
//								'texto_9' => 'BARRIO',
//								'texto_10' => 'LOCALIDAD',
//								'texto_11' => 'CP',
//								'texto_12' => 'PROVINCIA',
//								'texto_13' => 'COBERTURA_DESDE',
//								'texto_14' => 'COBERTURA_HASTA',
//								'texto_15' => 'CONDICION',
//								'decimal_1' => 'IMPORTE',
//								
//			);
//
//			$order = array('AsincronoTemporal.texto_3');
//			
//			
//			
//			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1');
//			$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
//			$this->set('datos',$datos);			
//
//			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_2');
//			$datos2 = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
//			$this->set('datos2',$datos2);		
//
//			$asinc = $this->Asincrono->read('p1,p2',$pid);
//			$this->set('servicio_desc',$oSERV->getNombreProveedorServicio($asinc['Asincrono']['p1']));
//			$this->set('fecha_cobertura_desde',$asinc['Asincrono']['p2']);
//			
//			$this->render('mutual_servicio_solicitudes/reportes/padron_servicio_xls','blank');
			
		
		endif;
		
		
		$this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);
		
		
		$this->render('mutual_servicio_solicitudes/padron_servicios');
		
	}
	
	
	
	function cancelaciones_por_fecha($tipo_salida=null){
		$disableForm = 0;
		$showAsincrono = 0;
		
		$this->set('fecha_desde',date('Y-m-d'));
		$this->set('fecha_hasta',date('Y-m-d'));
		
		$this->set('criterio_fecha','FV');
		$criterios = array('FV' => 'POR FECHA DE VENCIMIENTO','FI' => 'POR FECHA DE IMPUTACION','FA' => 'POR FECHA DE CARGA');
		$this->set('criterios',$criterios);
		if(!empty($this->data)){
			$disableForm = 1;
			$showAsincrono = 1;
			$this->set('fecha_desde',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_desde']));
			$this->set('fecha_hasta',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_hasta']));
			$this->set('criterio_fecha',$this->data['ListadoService']['criterio_fecha']);
			$this->set('criterio_desc',$criterios[$this->data['ListadoService']['criterio_fecha']]);
			$this->set('tipo_reporte',$this->data['ListadoService']['tipo_reporte']);
			$GLB = $this->ListadoService->getGlobalDato('concepto_1',$this->data['ListadoService']['forma_cancelacion']);
			$this->set('forma_cancelacion_desc',$GLB['GlobalDato']['concepto_1']);
			
		}
		$this->set('tipos_orden_dto',$this->tiposOrdenDto);	
		$this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);
		
		
		App::import('Model','Mutual.CancelacionOrden');
		$oCANCELACION = new CancelacionOrden();	

		$this->set('proveedores',$oCANCELACION->getListProveedoresDestino());
//		$this->set('proveedores',null);
		
		if($tipo_salida == 'PDF'){

			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
			
						
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
			$order = array('AsincronoTemporal.texto_6,AsincronoTemporal.texto_3');
			$datos = $this->ListadoService->getTemporalByConditions(true,$conditions,$order);
			
//			debug($datos);
//			exit;
			
			
//			$datos = $this->ListadoService->getTemporal($pid);

			$this->set('datos',$datos);
			$asinc = $this->Asincrono->read('p1,p2,p3,p4',$pid);
			$this->set('fecha_desde',$asinc['Asincrono']['p1']);
			$this->set('fecha_hasta',$asinc['Asincrono']['p2']);
			$this->set('criterio_desc',$criterios[$asinc['Asincrono']['p3']]);
			
			$GLB = $this->ListadoService->getGlobalDato('concepto_1',$asinc['Asincrono']['p4']);
			$this->set('forma_cancelacion_desc',$GLB['GlobalDato']['concepto_1']);
			
			$this->render('cancelacion_ordenes/reportes/entre_fechas_pdf','pdf');

		}else if($tipo_salida == 'XLS'){	
			
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];

			$columnas = array(
								'clave_2' => 'ORDEN_CANCELACION_NRO',
								'texto_2' => 'DOCUMENTO',
								'texto_3' => 'BENEFICIARIO',
								'texto_4' => 'TIPO_NUMERO',
								'texto_5' => 'PROVEEDOR_PRODUCTO',
								'texto_13' => 'CUOTAS',
								'texto_6' => 'A_ORDEN_DE',
								'texto_7' => 'FORMA_CANCELACION',
								'texto_9' => 'ESTADO',
								'texto_11' => 'VENCIMIENTO',
								'texto_12' => 'FECHA_IMPUTACION',
								'texto_14' => 'RECIBO',
								'decimal_1' => 'IMPORTE_TOTAL_SELECCIONADO',
								'decimal_2' => 'IMPORTE_PROVEEDOR',
								'decimal_3' => 'DIFERENCIA_ND_NC',
								'texto_8' => 'CANCELADA_CON_CREDITO',
								'texto_15' => 'FACT_LIQUIDACION_PROVEEDOR',
								'decimal_4' => 'TOTAL_FACTURA',
								'decimal_6' => 'SALDO_FACTURA',			
								'entero_1' => 'PENDIENTE_PROVEEDOR'
								
			);
			
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
			$order = array('AsincronoTemporal.texto_6,AsincronoTemporal.texto_3');
			$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
			$this->set('datos',$datos);
			$this->render('cancelacion_ordenes/reportes/entre_fechas_xls','blank');
			
		}else{
			
			$this->render('cancelacion_ordenes/entre_fechas');
				
		}			
		
	}

	function cobros_por_fecha($tipo_salida='PDF'){
		$disableForm = 0;
		$showAsincrono = 0;
		
		$this->set('fecha_desde',date('Y-m-d'));
		$this->set('fecha_hasta',date('Y-m-d'));		
		
		if(!empty($this->data)){
                        $this->set('codigo_organismo',$this->data['ListadoService']['codigo_organismo']);
			$this->set('tipo_cobro',$this->data['ListadoService']['tipo_cobro']);
			$this->set('fecha_desde',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_desde']));
			$this->set('fecha_hasta',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_hasta']));
			$this->set('proveedor_id',$this->data['ListadoService']['proveedor_id']);
//			$this->set('tipo_salida',$this->data['ListadoService']['tipo_salida']);
			$this->set('tipo_salida','XLS');
			$disableForm = 1;
			$showAsincrono = 1;
		}
		if(isset($this->params['url']['pid'])):
		
			$pid = $this->params['url']['pid'];
			if(empty($pid)) parent::noAutorizado();
			
			$asinc = $this->Asincrono->read('p1,p2,p3,p4',$pid);
			
			$columnas = array(
								'texto_1' => 'TIPO_COBRO',
								'texto_2' => 'FECHA_COBRO',
								'texto_3' => 'PROVEEDOR',
								'texto_4' => 'DOCUMENTO',
								'texto_5' => 'APENOM',
								'texto_11' => 'ORGANISMO',
								'entero_2' => 'ORDEN_DTO',
								'texto_6' => 'TIPO_NUMERO',
								'texto_7' => 'PROVEEDOR_PRODUCTO',
								'texto_13' => 'NRO_REFERENCIA',
								'texto_8' => 'CONCEPTO',
								'texto_9' => 'CUOTA',
								'texto_10' => 'PERIODO',
								'decimal_2' => 'IMPORTE_CUOTA',
								'decimal_1' => 'IMPORTE_PAGO',
								'decimal_5' => 'PORCENTAJE_COMISION',
								'decimal_6' => 'COMISION',
								'decimal_7' => 'NETO_PROVEEDOR',
                                'texto_14' => 'RECIBO',
			);				
			
			$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
			if(isset($INI_FILE['general']['factura_electronica']) && $INI_FILE['general']['factura_electronica'] != 0){
			    $columnas['decimal_10'] = 'CAPITAL';
			    $columnas['decimal_9'] = 'INTERES';
			    $columnas['decimal_8'] = 'IVA';
			} 
			

			
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
			$datos = $this->ListadoService->getDetalleToExcel($conditions,array(),$columnas);
			$this->set('datos',$datos);
			$this->set('asinc',$asinc);	

			if(empty($asinc['Asincrono']['p4'])){
				$this->set('proveedor','*** TODOS ***');
			}else{
				$proveedor = $this->requestAction('/proveedores/proveedores/get_razon_social/'.$asinc['Asincrono']['p4']);
				$this->set('proveedor',$proveedor);
			}

			$this->render('orden_descuento_cobros/reportes/entre_fechas_xls','blank');
			
		else:	
			
			$this->set('disable_form',$disableForm);
			$this->set('show_asincrono',$showAsincrono);
			
			$this->render('orden_descuento_cobros/entre_fechas');
							
		endif;
		
		


//		if($toPDF == 0):
//			$this->render('orden_descuento_cobros/entre_fechas');
//		else:
//			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
//			$pid = $this->params['url']['pid'];
//			$datos = $this->ListadoService->getTemporal($pid);
//			$this->set('datos',$datos);
//			$asinc = $this->Asincrono->read('p1,p2',$pid);
//			$this->set('periodo_cobro',$asinc['Asincrono']['p1']);
//			$this->set('tipo_cobro',$asinc['Asincrono']['p2']);
//			
//			//armo el resumen
//			$resumen = array();
//			foreach($datos as $dato):
//				$resumen[$dato['AsincronoTemporal']['texto_1']] = $dato['AsincronoTemporal']['texto_1'];
//			endforeach;
//			$valores = array();
//			foreach($resumen as $id => $tipoCobro){
//				$valores = Set::extract("/AsincronoTemporal[texto_1=$tipoCobro]",$datos);
//				$acumula = 0;
//				foreach($valores as $valor):
//					$acumula += $valor['AsincronoTemporal']['decimal_1'];
//				endforeach;
//				$resumen[$id] = $acumula;
//			}
//			$this->set('resumen',$resumen);
//	
//			
//			$this->render('orden_descuento_cobros/reportes/entre_fechas_pdf','pdf');
//		
//		endif;
	}

	
	function reporte_proveedores($liquidacion_id,$proveedor_id=0,$tipo_producto=0,$tipo_cuota=0,$tipo_salida='PDF',$procesarSobrePreImputacion=0,$periodo=NULL){
		
		App::import('Model','Proveedores.Proveedor');
		$oPRV = new Proveedor();
		$oPRV->unbindModel(array('hasMany' => array('MutualProducto')));	

		App::import('Model','Mutual.Liquidacion');
                
                if(!empty($liquidacion_id)){
                    $oLiq = new Liquidacion();
                    $liquidacion = $oLiq->cargar($liquidacion_id);
                    $this->set('liquidacion',$liquidacion);	
                }

		$this->set('proveedor',$oPRV->read(null,$proveedor_id));
		
		
		$this->set('tipo_salida',$tipo_salida);	
		$this->set('proveedor_id',$proveedor_id);
		$this->set('tipo_producto',$tipo_producto);
		$this->set('tipo_cuota',$tipo_cuota);
                $this->set('periodo',$periodo);
		
		$this->set('procesarSobrePreImputacion',$procesarSobrePreImputacion);
		
//		$this->render('liquidacion_proveedores/reporte_proveedores');
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
                $oCCUOTA = new OrdenDescuentoCobroCuota();
		//$oCCUOTA = ClassRegistry::init('Mutual.OrdenDescuentoCobroCuota');			
		

		if($tipo_producto == 'REVERSADO' && $tipo_cuota == 'REVERSADO'):
		
			$reversos = $oCCUOTA->reversosByProveedorByLiquidacion($proveedor_id,$liquidacion_id);
			$this->set('reversos',$reversos);
			$this->set('comision_reversada',$oCCUOTA->getTotalComisionAplicadaReverso($proveedor_id,$liquidacion_id));			
			if($tipo_salida == 'PDF'):
				$this->render('liquidacion_proveedores/reportes/reversos_pdf','pdf');
			endif;
			if($tipo_salida == 'XLS'):
				$this->render('liquidacion_proveedores/reportes/reversos_xls','blank');
			endif;	
			
		endif;
		
		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){
			
			$this->set('datos',$this->ListadoService->getTemporal($this->params['url']['pid'],false));
			$this->set('pid',$this->params['url']['pid']);
			
			
			if($tipo_salida == 'PDF'){
				
				$this->set('cabecera',$this->ListadoService->getTemporalByID($this->params['url']['pid']));
				
				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1');
				$cuotas = $this->ListadoService->getTemporalByConditions(false,$conditions,array('AsincronoTemporal.texto_2,AsincronoTemporal.entero_3,AsincronoTemporal.texto_3,AsincronoTemporal.texto_6'));
				
				$cuotas = Set::extract('{n}.AsincronoTemporal',$cuotas);
				$this->set('cuotas',$cuotas);
				
				// COBROS POR CAJA	
				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_3');
				$cobrosByCaja = $this->ListadoService->getTemporalByConditions(false,$conditions,array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_5,AsincronoTemporal.texto_8,AsincronoTemporal.texto_11'));
				
				$cobrosByCaja = Set::extract('{n}.AsincronoTemporal',$cobrosByCaja);
				$this->set('cobrosByCaja',$cobrosByCaja);
				
				//SACO LOS NO COBRADOS SEGUN CODIGO BANCO
				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_2');
				$order = array('AsincronoTemporal.texto_2');
				$group = array('AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_4,AsincronoTemporal.texto_5');
				$fields = array('AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_4,AsincronoTemporal.texto_5');
				$noCobradosBanco = $this->ListadoService->getTemporalByConditions(true,$conditions,$order);
				$noCobradosBanco = Set::extract('{n}.AsincronoTemporal',$noCobradosBanco);
				$this->set('noCobradosBanco',$noCobradosBanco);
				
				//saco el total reversado
				$this->set('total_reverso',$oCCUOTA->getTotalReversoByProveedorByLiquidacion($proveedor_id,$liquidacion_id,$tipo_producto,$tipo_cuota));

				$reversos = $oCCUOTA->reversosByProveedorByLiquidacion($proveedor_id,$liquidacion_id,$tipo_producto,$tipo_cuota);
				$this->set('reversos',$reversos);
				$this->set('comision_reversada',$oCCUOTA->getTotalComisionAplicadaReverso($proveedor_id,$liquidacion_id));
				
				
				// BAJAS	
				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_4');
				$bajas = $this->ListadoService->getTemporalByConditions(false,$conditions,array('AsincronoTemporal.texto_2,AsincronoTemporal.entero_2,AsincronoTemporal.entero_3'));
				
				$bajas = Set::extract('{n}.AsincronoTemporal',$bajas);
				$this->set('bajas',$bajas);
				
				//HISTORICO DE STOP
				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_5');
				$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.clave_2');
				$group = array('AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_4,AsincronoTemporal.texto_5');
				$fields = array('AsincronoTemporal.clave_2,AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_4,AsincronoTemporal.texto_5,AsincronoTemporal.decimal_1');
				$stops = $this->ListadoService->getTemporalByConditions(false,$conditions,$order,$fields,$group);
				$stops = Set::extract('{n}.AsincronoTemporal',$stops);
				$this->set('stops',$stops);
				
				$this->render('liquidacion_proveedores/reportes/liquidacion_detallada_pdf','pdf');
				return;

			}
			
			if($tipo_salida == 'XLS'){

                            App::import('model','Shells.Asincrono');
                            $oASINC = new Asincrono();

                            $asinc = $oASINC->read('p1,p2,p3,p4,p6',$this->params['url']['pid']);  
//                            debug($asinc);
//                            exit;

                            $this->redirect('/mutual/listados/download/'.$asinc['Asincrono']['p6']);
		
			}
			
			if($tipo_salida == 'SMTP'){
			    
			    App::import('model','Shells.Asincrono');
			    $oASINC = new Asincrono();
			    
			    $asinc = $oASINC->read('p1,p2,p3,p4,p6',$this->params['url']['pid']);
			    
			    $this->redirect('/mutual/listados/download/'.$asinc['Asincrono']['p6']);
			    
			}
			
			
		}else{
			
			if($tipo_salida == "TXT") $accion = '.mutual.liquidaciones.intercambio_proveedores.'.$liquidacion['Liquidacion']['id'].'.'.$proveedor_id;
			else $accion = '.mutual.listados.reporte_proveedores.'.$liquidacion['Liquidacion']['id'].'.'.$proveedor_id.'.'.$tipo_producto.'.'.$tipo_cuota.'.'.$tipo_salida.'.'.$procesarSobrePreImputacion;
//			$this->render('liquidacion_proveedores/reporte_proveedores');
		}
                
                $render = "liquidacion_proveedores/reporte_proveedores";
                if(empty($liquidacion_id) && !empty($periodo)){
                    $render = "liquidacion_proveedores/reporte_proveedores_periodo";
                    $accion = '.mutual.listados.reporte_proveedores.0.'.$proveedor_id.'.'.$tipo_producto.'.'.$tipo_cuota.'.'.$tipo_salida.'.'.$procesarSobrePreImputacion.'.'.$periodo;
                }
                $this->set('accion',$accion);
                $this->render($render);                
		
	}
	
	
	function reporte_liquidacion_deuda($liquidacionId,$toPDF=0,$showProcess=0){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();	
		$this->set('liquidacion',$oLiq->cargar($liquidacionId));
		
		if($toPDF == 0){
			$this->render('liquidaciones/reporte_liquidacion_deuda');
		}else if($showProcess == 0){
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_3' => array('REPORTE_1','REPORTE_2'));
			$order = array('AsincronoTemporal.clave_1','AsincronoTemporal.clave_2','AsincronoTemporal.clave_3','AsincronoTemporal.texto_3');
			$datos = $this->ListadoService->getTemporalByConditions(false,$conditions,$order);
// 			$datos = Set::extract("/AsincronoTemporal[decimal_3>0]",$datos);
			$this->set('datos',$datos);
			
			//saco las altas
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_3' => array('REPORTE_3','REPORTE_4'));
			$order = array('AsincronoTemporal.clave_1','AsincronoTemporal.clave_2','AsincronoTemporal.clave_3','AsincronoTemporal.texto_3');
			$altas = $this->ListadoService->getTemporalByConditions(false,$conditions,$order);
			$altas = Set::extract("{n}.AsincronoTemporal",$altas);
			$this->set('altas',$altas);
			
			//saco las ordenes de debito
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_3' => 'REPORTE_5');
			$order = array('AsincronoTemporal.texto_3','AsincronoTemporal.entero_1');
			$debitos = $this->ListadoService->getTemporalByConditions(false,$conditions,$order);
			$debitos = Set::extract("{n}.AsincronoTemporal",$debitos);
			$this->set('debitos',$debitos);			
			
			$this->render('liquidaciones/reportes/reporte_liquidacion_deuda_pdf','pdf');
		}	
		
				
	}
	
        
	function reporte_liquidacion_deuda3($liquidacionId,$toPDF=0,$showProcess=0){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();	
		$this->set('liquidacion',$oLiq->cargar($liquidacionId));
		
		if($toPDF == 0){
			$this->render('liquidaciones/reporte_liquidacion_deuda3');
		}else if($showProcess == 0){
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_3' => array('REPORTE_1','REPORTE_2'));
			$order = array('AsincronoTemporal.clave_1','AsincronoTemporal.clave_2','AsincronoTemporal.clave_3','AsincronoTemporal.texto_3');
			$datos = $this->ListadoService->getTemporalByConditions(false,$conditions,$order);
// 			$datos = Set::extract("/AsincronoTemporal[decimal_3>0]",$datos);
			$this->set('datos',$datos);
			
			//saco las altas
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_3' => array('REPORTE_3','REPORTE_4'));
			$order = array('AsincronoTemporal.clave_1','AsincronoTemporal.clave_2','AsincronoTemporal.clave_3','AsincronoTemporal.texto_3');
			$altas = $this->ListadoService->getTemporalByConditions(false,$conditions,$order);
			$altas = Set::extract("{n}.AsincronoTemporal",$altas);
			$this->set('altas',$altas);
			
			//saco las ordenes de debito
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_3' => 'REPORTE_5');
			$order = array('AsincronoTemporal.texto_3','AsincronoTemporal.entero_1');
			$debitos = $this->ListadoService->getTemporalByConditions(false,$conditions,$order);
			$debitos = Set::extract("{n}.AsincronoTemporal",$debitos);
			$this->set('debitos',$debitos);			
			
			$this->render('liquidaciones/reportes/reporte_liquidacion_deuda_pdf3','pdf');
		}	
		
				
	}
        
	
	function reporte_imputacion_deuda($liquidacionId,$pid,$tipo_salida='PDF',$procesarSobrePreImputacion=0,$periodo = NULL){
		
		$liquidacion = NULL;
		if(!empty($liquidacionId)){
			App::import('Model','Mutual.Liquidacion');
			$oLiq = new Liquidacion();
			$liquidacion = $oLiq->cargar($liquidacionId);                    
		}
		$this->set('liquidacion',$liquidacion);		
		$this->set('procesarSobrePreImputacion',$procesarSobrePreImputacion);

		$this->set('periodo',$periodo);		

        $render_pdf = 'liquidaciones/reportes/reporte_imputacion_deuda_pdf';
        $render_xls = 'liquidaciones/reportes/reporte_imputacion_deuda_xls';
        
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $CALC_IVA = false;
        if(isset($INI_FILE['general']['discrimina_iva']) && $INI_FILE['general']['discrimina_iva'] != 0){
            $render_pdf = 'liquidaciones/reportes/reporte_imputacion_deuda_pdf_IVA';
            $render_xls = 'liquidaciones/reportes/reporte_imputacion_deuda_xls_IVA';
            $CALC_IVA = true;
        }        
		
		if($tipo_salida == 'PDF'):

			$datos =$this->ListadoService->getTemporal($pid,false);
            $datos = Set::extract("/AsincronoTemporal[clave_3=REPORTE_1]",$datos);
			$datos = Set::extract("/AsincronoTemporal[decimal_3>0]",$datos);
			$this->set('datos',$datos);	
            $socios = null;
            if($CALC_IVA){
                $socios = $this->ListadoService->getTemporal($pid,false);
                $socios = Set::extract("/AsincronoTemporal[clave_3=REPORTE_5]",$socios);
                $socios = Set::extract("{n}.AsincronoTemporal",$socios);            
                $this->set('socios',$socios);            
            }
			
			$this->render($render_pdf,'pdf');

		elseif($tipo_salida == 'XLS'):
		
			$columnas = array(
								'texto_1' => 'PROVEEDOR',
								'texto_4' => 'PRODUCTO',
								'texto_5' => 'CONCEPTO',
								'decimal_7' => 'LIQUIDADO_PERIODO',
								'decimal_8' => 'LIQUIDADO_DEUDA',
								'decimal_9' => 'LIQUIDADO_TOTAL',
								'decimal_4' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO"). '_PERIODO',
								'decimal_5' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO").'_DEUDA',
								'decimal_6' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO").'_TOTAL',
                                'entero_3' => 'SOCIOS',
								'decimal_10' => 'ALICUOTA_COMISION',
								'decimal_11' => 'COMISION',	
								'decimal_12' => 'NETO_PROVEEDOR',
								'decimal_13' => 'LIQUI_NO_COB',		
			
			);	
        
            if($CALC_IVA){
                $columnas = array(
                                    'texto_1' => 'PROVEEDOR',
                                    'texto_4' => 'PRODUCTO',
                                    'texto_5' => 'CONCEPTO',
                                    'decimal_7' => 'LIQUIDADO_PERIODO',
                                    'decimal_8' => 'LIQUIDADO_DEUDA',
                                    'decimal_9' => 'LIQUIDADO_TOTAL',
                                    'decimal_4' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO"). '_PERIODO',
                                    'decimal_5' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO").'_DEUDA',
                                    'decimal_6' => ($procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO").'_TOTAL',
                                    'entero_3' => 'SOCIOS',
                                    'decimal_10' => 'ALICUOTA_COMISION',
                                    'decimal_11' => 'COMISION',
                                    'decimal_15' => 'I.V.A.',
                                    'decimal_12' => 'NETO_PROVEEDOR',
                                    'decimal_13' => 'LIQUI_NO_COB',		

                );	                
            }
			
			$conditions = array('AsincronoTemporal.asincrono_id' => $pid,'AsincronoTemporal.clave_3' => 'REPORTE_1');
			$order = array();
			$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
			$this->set('datos',$datos);				
		
		
			$this->render($render_xls,'blank');
			
		else:
			parent::noDisponible();
				
		endif;
		
		
	}
	
	
	function reporte_inaes($tipo_salida=null){
		$showAsincrono = 0;
		$disableForm = 0;
		
		$this->set('fecha_corte',date('Y-m-d'));
		
		if(!empty($tipo_salida)):
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
			$columnas = array(
								'texto_1' => 'TIPO_DOCUMENTO',
								'texto_2' => 'DOCUMENTO',
								'texto_3' => 'APELLIDO_NOMBRE',
								'texto_4' => 'ORDEN_DTO',
								'texto_7' => 'PERIODO_INICIO',
								'texto_8' => 'FECHA',
								'decimal_1' => 'IMPORTE',
								'entero_1' => 'CUOTAS',
								'entero_2' => 'CUOTAS_ABONADAS',
								'decimal_2' => 'VENCIDAS_0_29',
								'decimal_3' => 'VENCIDAS_30_59',
								'decimal_4' => 'VENCIDAS_60_179',
								'decimal_5' => 'VENCIDAS_180_179',
								'decimal_6' => 'VENCIDAS_180_269',	
								'decimal_7' => 'VENCIDAS_270_9999',
								'decimal_8' => 'A_VENCER_0_3',
								'decimal_9' => 'A_VENCER_4_6',
								'decimal_10' => 'A_VENCER_7_9',
								'decimal_11' => 'A_VENCER_10_12',
								'decimal_12' => 'A_VENCER_13_9999',		
			
			);				
			
			$conditions = array('AsincronoTemporal.asincrono_id' => $pid);
			$order = array();
			$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
			
			$this->set('datos',$datos);	

			$this->render('orden_descuento_cuotas/reportes/reporte_inaes_xls','blank');
			
		endif;
		
		if(!empty($this->data)){
			$this->set('fecha_corte',$this->ListadoService->armaFecha($this->data['ListadoService']['fecha_corte']));
			$tipo_salida = $this->data['ListadoService']['tipo_salida'];
			$disableForm = 1;
			$showAsincrono = 1;
		}		
		$this->set('disable_form',$disableForm);
		$this->set('tipo_salida',$tipo_salida);
		$this->set('show_asincrono',$showAsincrono);
		
		$this->render('orden_descuento_cuotas/reporte_inaes');
	}
	
	function reporte_inaesA9($periodoAnalisis=null,$toPDF=0){
		$showAsincrono = 0;
		$disableForm = 0;
		$datos = null;
		
		if(!empty($this->data)){
                    
			$this->set('periodo',$this->data['Liquidacion']['periodo']);
			//CALCULO LOS TOTALES
			App::import('Model','Mutual.OrdenDescuentoCobroCuota');
			$oCOBROCUOTA = new OrdenDescuentoCobroCuota();				

			$datos = $oCOBROCUOTA->cuotasSocialesCobradasByPeriodo($this->data['Liquidacion']['periodo']);

			
		}
		
		if(!empty($periodoAnalisis) && $toPDF == 1):
			App::import('Model','Mutual.OrdenDescuentoCobroCuota');
			$oCOBROCUOTA = new OrdenDescuentoCobroCuota();				

			$datos = $oCOBROCUOTA->cuotasSocialesCobradasByPeriodo($periodoAnalisis);
			$this->set('periodoAnalisis',$periodoAnalisis);
			$this->set('datos',$datos);
			$this->render('orden_descuento_cuotas/reportes/reporte_inaesA9_pdf','pdf');
			
		endif;
		

		$this->set('datos',$datos);
		$this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);
		
		$this->render('orden_descuento_cuotas/reporte_inaesA9');
		
	}
	
	
	
	function listado_deuda($tipo_salida=null,$consolidado=0){
		
		$showAsincrono = 0;
		$disableForm = 0;
		
//		$opcionesListado = array(1 => "1 - CONSOLIDADO POR SOCIO", 2 => '2 - CONSOLIDADO POR ORDEN DE DESCUENTO', 3 => '3 - DETALLE DE CUOTAS ADEUDADAS',4 => '4 - ORDENES DE DESCUENTO POR FINALIZAR');
                $opcionesListado = array(2 => 'DEUDA POR ORDEN DE DESCUENTO', 4 => 'ORDENES DE DESCUENTO POR FINALIZAR');
		
		if(!empty($tipo_salida)):
			
			if(!isset($this->params['url']['pid']) || empty($this->params['url']['pid'])) parent::noAutorizado();
			$pid = $this->params['url']['pid'];
			
			$asinc = $this->Asincrono->read('p1,p2,p3,p4,p8,p6',$pid);

			$codigo_organismo = $asinc['Asincrono']['p1'];
			$periodo_corte = $asinc['Asincrono']['p2'];
			$proveedor = (!empty($asinc['Asincrono']['p3']) ? $this->requestAction('/proveedores/proveedores/get_razon_social/'.$asinc['Asincrono']['p3']) : "");
			$tipo_listado = $asinc['Asincrono']['p4'];
                        $tipo_producto = $asinc['Asincrono']['p8'];
			
			$this->set('codigo_organismo',$codigo_organismo);
			$this->set('periodo_corte',$periodo_corte);
			$this->set('proveedor',$proveedor);
			$this->set('tipo_listado',$tipo_listado);	
			$this->set('tipo_listado_desc',$opcionesListado[$tipo_listado]);
                        $this->set('tipo_producto',$tipo_producto);
                        
                        
                        $this->redirect('download/'.$asinc['Asincrono']['p6']);
                        
                        if($tipo_listado == 4) $tipo_salida = 'XLS';
			
			if($tipo_salida == 'PDF'):
	
			
				$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
				$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_10,AsincronoTemporal.texto_12,AsincronoTemporal.texto_3,AsincronoTemporal.texto_6');
				$datos = $this->ListadoService->getTemporalByConditions(true,$conditions,$order);
				$datos = Set::extract('{n}.AsincronoTemporal',$datos);
				$this->set('datos',$datos);
				
				switch ($tipo_listado):
					case 1:
						//CONSOLIDADO POR SOCIO
						$this->render('orden_descuento_cuotas/reportes/listado_deuda_socio_pdf','pdf');
						break;
					case 2:
						//CONSOLIDADO POR ORDEN DE DESCUENTO
						$this->render('orden_descuento_cuotas/reportes/listado_deuda_ordendto_pdf','pdf');
						break;	
					case 3:
						//DETALLE DE CUOTAS ADEUDADAS
						$this->render('orden_descuento_cuotas/reportes/listado_deuda_detallado_pdf','pdf');
						break;	
				endswitch;
				
				return;
					
//				if($consolidado==0):
//					$this->render('orden_descuento_cuotas/reportes/listado_deuda_detallado_pdf','pdf');
//				else:
//					$this->render('orden_descuento_cuotas/reportes/listado_deuda_socio_pdf','pdf');
//				endif;
	
			elseif($tipo_salida == 'XLS'):
			
			
				switch ($tipo_listado):
					case 1:
						//CONSOLIDADO POR SOCIO
						$columnas = array(
											'entero_1' => 'NRO_SOCIO',
											'texto_1' => 'DOCUMENTO',
											'texto_2' => 'APELLIDO_NOMBRE',
                                            'texto_3' => 'CALLE',
                                            'texto_4' => 'NRO_CALLE',
                                            'texto_5' => 'PISO',
                                            'texto_6' => 'DPTO',
                                            'texto_7' => 'BARRIO',
                                            'texto_8' => 'LOCALIDAD',
                                            'texto_9' => 'CP',
                                            'texto_10' => 'PROVINCIA',
                                            'texto_11' => 'TELEFONO_FIJO',
                                            'texto_12' => 'TELEFONO_MOVIL',
                                            'texto_13' => 'TELEFONO_REF',
                                            'texto_14' => 'EMAIL',
											'decimal_1' => 'LIQUIDADO',
											'decimal_2' => 'COBRADO',
											'decimal_3' => 'SALDO_CONCILIADO',	
                                            'decimal_4' => 'PENDIENTE_ACREDITAR',
                                            'decimal_5' => 'SALDO_A_CONCILIAR',
						);
						$datos = array();
						$this->set('cabecera',$this->ListadoService->getTemporalByID($this->params['url']['pid']));
						
						
						$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
						$order = array('AsincronoTemporal.texto_2');
						$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
						
						$this->set('datos',$datos);					
					
						$this->render('orden_descuento_cuotas/reportes/listado_deuda_socio_xls','blank');
						break;
					case 2:
						//CONSOLIDADO POR ORDEN DE DESCUENTO
						$columnas = array(
											'texto_1' => 'DOCUMENTO',
											'texto_2' => 'APELLIDO_NOMBRE',
											'texto_10' => 'ORGANISMO',
                                                                                        'texto_8' => 'EMPRESA',
                                                                                        'texto_9' => 'TURNO',
											'texto_12' => 'PROVEEDOR',	
                                            'texto_3' => 'TIPO_NUMERO',
											'texto_13' => 'ORDEN_DTO',
                            
											'texto_6' => 'REF_PROVEEDOR',
											'texto_14' => 'PROVEEDOR',
                                                                                        'texto_15' => 'PRODUCTO',
											'texto_7' => 'CUOTAS_DEBE',
											'texto_5' => 'PERIODO_INICIO',
                                                                                        'decimal_1' => 'TOTAL_ORDEN',
                                                                                        'decimal_2' => 'PAGOS',
											'decimal_3' => 'SALDO_CONCILIADO',	
                                                'decimal_4' => 'PENDIENTE_ACREDITAR',
                                                'decimal_5' => 'SALDO_A_CONCILIAR',			
                                                'decimal_6' => 'SALDO_A_VENCER',
                                                'decimal_7' => 'HASTA_3_MESES',    
                                                'decimal_8' => 'HASTA_6_MESES',        
                                                'decimal_9' => 'HASTA_12_MESES',
                                                'decimal_10' => 'MAS_12_MESES',        
                                                 'entero_4' => 'CUOTAS_A_VENCER',
                                                'texto_16'  => 'VENDEDOR_CUIT',
                                                'texto_17'  => 'VENDEDOR_APENOM',
                                                 'texto_18'  => 'SOLICITUD_EMITIDA',
                                                 'texto_19'  => 'SOLICITUD_APROBADA',   
						);
						$datos = array();
						$this->set('cabecera',$this->ListadoService->getTemporalByID($this->params['url']['pid']));
						
						
						$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
						$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_10,AsincronoTemporal.texto_12,AsincronoTemporal.texto_3,AsincronoTemporal.texto_6');
						$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
						
						$this->set('datos',$datos);					
					
						$this->render('orden_descuento_cuotas/reportes/listado_deuda_detallado_xls','blank');
						break;	
					case 3:
						//DETALLE DE CUOTAS ADEUDADAS
						$columnas = array(
											'texto_1' => 'DOCUMENTO',
											'texto_2' => 'APELLIDO_NOMBRE',
											'texto_10' => 'ORGANISMO',
											'texto_12' => 'PROVEEDOR',	
											'texto_3' => 'TIPO_NUMERO',
											'texto_9' => 'REF_PROVEEDOR',
											'texto_4' => 'PROVEEDOR_PRODUCTO',
											'texto_5' => 'CONCEPTO',
											'texto_6' => 'CUOTA',
											'texto_7' => 'PERIODO',
											'decimal_1' => 'LIQUIDADO',
											'decimal_2' => 'COBRADO',
											'decimal_3' => 'SALDO_CONCILIADO',	
                                            'decimal_4' => 'PENDIENTE_ACREDITAR',
                                            'decimal_5' => 'SALDO_A_CONCILIAR',			
						);
						$datos = array();
						$this->set('cabecera',$this->ListadoService->getTemporalByID($this->params['url']['pid']));
						
						
						$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
						$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_10,AsincronoTemporal.texto_12,AsincronoTemporal.texto_3,AsincronoTemporal.texto_6');
						$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
						
						$this->set('datos',$datos);					
					
						$this->render('orden_descuento_cuotas/reportes/listado_deuda_detallado_xls','blank');
						break;	
                                            
                                            
                                        case 4:
						$columnas = array(
                                                    'entero_4' => 'NRO_SOCIO',
                                                    'texto_1' => 'DOCUMENTO',
                                                    'texto_2' => 'APELLIDO_NOMBRE',
                                                    'texto_3' => 'CALLE',
                                                    'texto_4' => 'NRO_CALLE',
                                                    'texto_5' => 'PISO',
                                                    'texto_6' => 'DPTO',
                                                    'texto_7' => 'BARRIO',
                                                    'texto_8' => 'LOCALIDAD',
                                                    'texto_9' => 'CP',
                                                    'texto_10' => 'PROVINCIA',
                                                    'texto_11' => 'TELEFONO_FIJO',
                                                    'texto_12' => 'TELEFONO_MOVIL',
                                                    'texto_13' => 'TELEFONO_REF',
                                                    'texto_14' => 'EMAIL',
                                                    'texto_15' => 'TIPO_NUMERO',
                                                    'entero_3' => 'ORD_DTO',
                                                    'entero_1' => 'CUOTAS',	
                                                    'decimal_1' => 'MORA',
                                                    'texto_16' => 'ORGANISMO',
                                                    'texto_17' => 'BANCO_BENEFICIO',
                                                    'entero_2' => 'SOCIO_ACTIVO',
                                                    'texto_18' => 'VENDEDOR_CUIT',
                                                    'texto_19' => 'VENDEDOR_NOMBRE',
						);  
					$datos = array();
                                        $this->set('cabecera',$this->ListadoService->getTemporalByID($this->params['url']['pid']));


                                        $conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
                                        $order = array('AsincronoTemporal.texto_2');
                                        $datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);

                                        $this->set('datos',$datos);					

                                        $this->render('orden_descuento_cuotas/reportes/listado_deuda_socio_xls','blank');
                                        break;
                                            
				endswitch;
				
//				return;			
			

				if($consolidado == 0){

					$columnas = array(
										'texto_1' => 'DOCUMENTO',
										'texto_2' => 'APELLIDO_NOMBRE',
										'texto_10' => 'ORGANISMO',
										'texto_12' => 'PROVEEDOR',	
										'texto_3' => 'TIPO_NUMERO',
										'texto_9' => 'REF_PROVEEDOR',
										'texto_4' => 'PROVEEDOR_PRODUCTO',
										'texto_5' => 'CONCEPTO',
										'texto_6' => 'CUOTA',
										'texto_7' => 'PERIODO',
										'decimal_1' => 'LIQUIDADO',
										'decimal_2' => 'COBRADO',
										'decimal_3' => 'SALDO',				
					);
					$datos = array();
					$this->set('cabecera',$this->ListadoService->getTemporalByID($this->params['url']['pid']));
					
					
					$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
					$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_10,AsincronoTemporal.texto_12,AsincronoTemporal.texto_3,AsincronoTemporal.texto_6');
					$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
					
					$this->set('datos',$datos);					
				
					$this->render('orden_descuento_cuotas/reportes/listado_deuda_detallado_xls','blank');
                    return;
                }else{
				
					$columnas = array(
										'entero_1' => 'NRO_SOCIO',
										'texto_1' => 'DOCUMENTO',
										'texto_2' => 'APELLIDO_NOMBRE',
										'decimal_1' => 'LIQUIDADO',
										'decimal_2' => 'COBRADO',
										'decimal_3' => 'SALDO',				
					);
					$datos = array();
					$this->set('cabecera',$this->ListadoService->getTemporalByID($this->params['url']['pid']));
					
					
					$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
					$order = array('AsincronoTemporal.texto_2');
					$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
					
					$this->set('datos',$datos);					
				
					$this->render('orden_descuento_cuotas/reportes/listado_deuda_socio_xls','blank');
					return;
					
                }
			else:
				parent::noDisponible();
					
			endif;			
			
		endif;			
		
		$this->set('periodo_corte',date('Ym'));
		
		if(!empty($this->data)){
			
			$tipo_salida = (isset($this->data['ListadoService']['tipo_reporte']) ? $this->data['ListadoService']['tipo_reporte'] : '2');
			
			$this->set('codigo_organismo',$this->data['ListadoService']['codigo_organismo']);
			$this->set('periodo_corte',$this->data['ListadoService']['periodo_corte']['year'].$this->data['ListadoService']['periodo_corte']['month']);
			
			$this->set('fecha_corte',$this->ListadoService->armaFecha($this->data['ListadoService']['periodo_corte']));
			
			
			$codigoEmpresa = null;
			$turnoPago = null;
			if(!empty($this->data['ListadoService']['codigo_empresa'])){
				$empresa = explode("|", $this->data['ListadoService']['codigo_empresa']);
				$codigoEmpresa = $empresa[0];
				$turnoPago = $empresa[1];
			}
			$this->set('codigo_empresa',$codigoEmpresa);
			$this->set('turno_pago',$turnoPago);
                        
                        
                       $this->set('proveedor_id',$this->data['ListadoService']['proveedor_id']);
//			$tipo_listado = $this->data['ListadoService']['tipo_listado'];
//			$this->set('tipo_listado',$tipo_listado);
//			$cantidad_cuotas = $this->data['ListadoService']['cantidad_cuotas'];
//			$this->set('cantidad_cuotas',$cantidad_cuotas);
//                        $this->set('tipo_producto',$this->data['ListadoService']['tipo_producto']);
//                        $this->set('tipo_cuota',$this->data['ListadoService']['tipo_cuota']);
                        
                        // $this->set('proveedor_id',NULL);
			$this->set('tipo_listado',2);
			$this->set('cantidad_cuotas',NULL);
                        $this->set('tipo_producto',NULL);
                        $this->set('tipo_cuota',NULL);                        
                        
                        
			$disableForm = 1;
			$showAsincrono = 1;			
		}
		
		$this->set('disable_form',$disableForm);
		$this->set('tipo_salida',$tipo_salida);
		$this->set('show_asincrono',$showAsincrono);
		

		$this->set('opcionesListado',$opcionesListado);
				
		$this->render('orden_descuento_cuotas/listado_deuda');
		
	
		
	}
	
	
	
	function listado_reintegros($tipo_salida=null){
		
		$showAsincrono = 0;
		
		App::import('Model','Pfyj.SocioReintegro');
		$oSR = new SocioReintegro();				
		
		$periodos = $oSR->getPeriodos(true);
        $this->set('consolidado',0);
		
		if(isset($this->params['url']['pid']) && !empty($this->params['url']['pid'])){
			
//			App::import('model','Mutual.ListadoService');
//			$oListado = new ListadoService();
			
			
			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();

			$asinc = $oASINC->read(null,$this->params['url']['pid']);
			
//			debug($asinc);
			
			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);
			$order = array('AsincronoTemporal.texto_2 ASC,AsincronoTemporal.texto_3 DESC');
			$fields = array(
								'AsincronoTemporal.clave_1,
								AsincronoTemporal.clave_2,
								AsincronoTemporal.clave_3,
								AsincronoTemporal.texto_1,
								AsincronoTemporal.texto_2,
								AsincronoTemporal.texto_3,
								AsincronoTemporal.texto_4,
								AsincronoTemporal.decimal_1,
								AsincronoTemporal.decimal_2,
								AsincronoTemporal.decimal_3,
								AsincronoTemporal.decimal_4,
								AsincronoTemporal.decimal_5,
								AsincronoTemporal.texto_5,
								AsincronoTemporal.texto_6,
								AsincronoTemporal.texto_7,
								AsincronoTemporal.entero_1	
			');
			
			if(intval($asinc['Asincrono']['p5']) == 0):
			
				$columnas = array(
									'clave_2' => 'NRO_SOCIO',
									'texto_1' => 'DOCUMENTO',
									'texto_2' => 'APELLIDO_NOMBRE',
									'texto_3' => 'PERIODO',
									'texto_4' => 'ORGANISMO',
									'decimal_2' => 'DEBITADO',
									'decimal_3' => 'IMPUTADO',
									'decimal_4' => 'REINTEGRO',
									'decimal_5' => 'APLICADO',
									'texto_5' => 'ANTICIPADO',
									'texto_6' => 'COMPENSA',
									'texto_7' => 'PROCESADO',					
				);
			else:
				$columnas = array(
									'clave_2' => 'NRO_SOCIO',
									'texto_1' => 'DOCUMENTO',
									'texto_2' => 'APELLIDO_NOMBRE',
									'decimal_1' => 'A_DEBITAR',
									'decimal_2' => 'DEBITADO',
									'decimal_3' => 'IMPUTADO',
									'decimal_4' => 'REINTEGRO',
									'decimal_5' => 'APLICADO',
									'entero_1' => 'CANTIDAD',
				);
			
			endif;
			
			$datos = $this->ListadoService->getDetalleToExcel($conditions,$order,$columnas);
			$this->set('datos',$datos);
			$this->set('asinc',$asinc);
			$this->render('socio_reintegros/reportes/listado_reintegros_xls','blank');
			return;
		}
		
		
		
		if(!empty($this->data)):
		
			$periodoDesde = $this->data['ListadoService']['periodo_desde'];
			$periodoHasta = $this->data['ListadoService']['periodo_hasta'];
			$codigoOrganismo = $this->data['ListadoService']['codigo_organismo'];
			$codigoEmpresa = (isset($this->data['ListadoService']['codigo_empresa']) ? $this->data['ListadoService']['codigo_empresa'] : null);
			$consolidado = (isset($this->data['ListadoService']['consolidado']) ? $this->data['ListadoService']['consolidado'] : 0);
		
			$tipo_reporte = 'XLS';
			
			$showAsincrono = 1;	
			
			$this->set('periodoDesde',$periodoDesde);
			$this->set('periodoHasta',$periodoHasta);
			$this->set('codigoOrganismo',$codigoOrganismo);
			$this->set('codigoEmpresa',$codigoEmpresa);
			$this->set('consolidado',$consolidado);
			
			$this->set('tipo_reporte',$tipo_reporte);
			
		
		endif;
		
		
		
		$this->set('periodos',$periodos);
		$this->set('showAsincrono',$showAsincrono);
		$this->render('socio_reintegros/listado_reintegros');
	}
	
	
	function reporte_inaesbe(){
		
		$fecha_desde = date('Y-m-d');
		$fecha_hasta = date('Y-m-d');
        $disable_form = 0;
		
		if(!empty($this->data)){
			
			$fecha_desde = $this->ListadoService->armaFecha($this->data['ListadoService']['fecha_desde']);
			$fecha_hasta = $this->ListadoService->armaFecha($this->data['ListadoService']['fecha_hasta']);
			
			App::import('Model','Mutual.OrdenDescuentoCobroCuota');
			$oCOBROCUOTA = new OrdenDescuentoCobroCuota();		

			$cobrado = $oCOBROCUOTA->getTotalCuotasSocialesCobradasEntreFechas($fecha_desde,$fecha_hasta);
			
			App::import('Model','pfyj.Socio');
			$oSOCIO = new Socio();
			
			$categorias = $oSOCIO->getResumenAltaBajaByCategoriaEntreFechas($fecha_desde,$fecha_hasta);
			$disable_form = 1;
			
		}
		
		$this->set('disable_form',$disable_form);
		$this->set('fecha_desde',$fecha_desde);
		$this->set('fecha_hasta',$fecha_hasta);
		$this->render('orden_descuento_cuotas/reporte_inaesbe');
	}
	
    
    function download($file,$type='application/vnd.ms-excel'){
        
            $filePath = WWW_ROOT . "files" . DS . "reportes" . DS . $file;
            
            header('Content-Description: Backup File Transfer');
            header('Content-Type: '.$type);
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: base64');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));

            $gestor = fopen($filePath, "r");
            if ($gestor){
                while (($búfer = fgets($gestor, 4096)) !== false){
                    echo $búfer;
                }
                if (!feof($gestor)){
                    echo "Error: fallo inesperado de fgets()\n";
                }
                fclose($gestor);
            }            
            
            exit;        
        
    }
    
    
	
}
?>
<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * 
 * LANZADOR
 * 	/usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php listado_deuda_xls 2812 -app /home/adrian/Trabajo/www/sigemv2/app/
 * 
 */

class ListadoDeudaXlsShell extends Shell {

	var $codigo_organismo;
	var $periodo_corte;
	var $proveedor_id;
	var $consolidado;
	var $tipo_listado;  //1 => "CONSOLIDADO POR SOCIO", 2 => 'CONSOLIDADO POR ORDEN DE DESCUENTO', 3 => 'DETALLE DE CUOTAS ADEUDADAS'
	var $codigo_empresa;
	var $turno_pago;
        var $cantidad_cuotas;
        var $tipo_producto;
        var $tipo_cuota;
	
	var $uses = array(
						'Mutual.OrdenDescuento',
						'Mutual.OrdenDescuentoCuota',
						'Pfyj.Persona',
	);	
	
	var $tasks = array('Temporal');
	
	function main() {
        
        Configure::write('debug',1);
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$this->codigo_organismo		= $asinc->getParametro('p1');
		$this->periodo_corte		= $asinc->getParametro('p2');
		$this->proveedor_id			= $asinc->getParametro('p3');
		$this->tipo_listado			= $asinc->getParametro('p4');
		$this->codigo_empresa		= $asinc->getParametro('p5');
		$this->turno_pago			= $asinc->getParametro('p6');
                $this->cantidad_cuotas			= $asinc->getParametro('p7');
                $this->tipo_producto			= $asinc->getParametro('p8');
                $this->tipo_cuota			= $asinc->getParametro('p9');
		
		
                $this->periodo_corte = (empty($this->periodo_corte) ? date('Ym') : $this->periodo_corte);
                
		$asinc->actualizar(1,100,"ESPERE, GENERANDO LISTADO DE PERSONAS A PROCESAR...");
		$STOP = 0;
		$total = 0;
		$i = 0;

		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();	
                
		App::import('Model','Pfyj.Persona');
		$oPERSONA = new Persona();                
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
                
                
                $FILE_EXCEL = "LISTADO_DEUDA_A_".$this->periodo_corte."_".date('Ymd-His').".xls";
                $this->Temporal->setXLSObject(6);

                $oXLS = $this->Temporal->getXLSObject(); 
                
                switch ($this->tipo_listado):
                    
                    case 1:
                        
                        $set = array();
                        $set['sheet_title'] = 'LISTADO_DEUDA_SOCIO';
                        $set['labels'] = array(
                            'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR SOCIO'
                        );
                        $set['columns'] = array(
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
                        $this->Temporal->prepareXLSSheet(0,$set);                         
                        
                        break;
                    case 2:
                        
                        $set = array();
                        $set['sheet_title'] = 'ORDENES_DESCUENTO';
                        $set['labels'] = array(
//                            'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR ORDEN DE DESCUENTO'
                        );
                        $set['columns'] = array(
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
                                'decimal_16' => 'COBRADO_OPTIMO',
                                'decimal_2' => 'COBRADO',
                                'decimal_17' => 'INDICE_COBRANZA',
                                'decimal_3' => 'SALDO_CONCILIADO',	
                                'decimal_4' => 'PENDIENTE_ACREDITAR',
                                'decimal_5' => 'SALDO_A_CONCILIAR',
                                'entero_5' => 'CUOTAS_A_VENCIDAS',
                                'decimal_11' => 'MORA_0_3_MESES',    
                                'decimal_12' => 'MORA_3_6_MESES',        
                                'decimal_13' => 'MORA_6_9_MESES',
                                'decimal_14' => 'MORA_9_12_MESES',  
                                'decimal_15' => 'MORA_MAS_12_MESES', 
                                'decimal_6' => 'SALDO_A_VENCER',
                                'entero_4' => 'CUOTAS_A_VENCER',
                                'decimal_7' => 'A_VENCER_3_MESES',    
                                'decimal_8' => 'A_VENCER_6_MESES',        
                                'decimal_9' => 'A_VENCER_12_MESES',
                                'decimal_10' => 'A_VENCER_MAS_12_MESES',        
                                'texto_16'  => 'VENDEDOR_OPERADOR',
                                'texto_17'  => 'VENDEDOR_CUIT',
                                 'texto_18'  => 'SOLICITUD_EMITIDA',
                                 'texto_19'  => 'SOLICITUD_APROBADA', 
                                 'clave_1'  => 'SOCIO', 
                                 'clave_2'  => 'PROVEEDOR', 
                                 'clave_3'  => 'ORGANISMO', 
                                 'clave_4'  => 'EMPRESA', 
                                 'clave_5'  => 'USUARIO', 
                        );		        
                        $this->Temporal->prepareXLSSheet(0,$set);                          
                        
                        break;
                    
                    case 3:
                        
                        $set = array();
                        $set['sheet_title'] = 'LISTADO_DEUDA_SOCIO';
                        $set['labels'] = array(
                            'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR ORDEN DE DESCUENTO DETALLADA'
                        );
                        $set['columns'] = array(
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
                        $this->Temporal->prepareXLSSheet(0,$set);                          
                        break;
                    
                    case 4:
                        
                        $set = array();
                        $set['sheet_title'] = 'LISTADO_DEUDA_SOCIO';
                        $set['labels'] = array(
                            'B1' => 'LISTADO DE OPERACIONES POR FINALIZAR'
                        );
                        $set['columns'] = array(
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
                        $this->Temporal->prepareXLSSheet(0,$set);                          
                        
                        break;
                    
                endswitch;
                
                
		
		$personas = $this->getPersonas();
		
		if(!empty($personas)):
		
			$total = count($personas);
			$asinc->setTotal($total);
			$i = 0;	
	
			$temp = array();		
			
			App::import('Helper','Util');
			$oUT = new UtilHelper();			
		
			foreach($personas as $persona):
				
				$msg = "$i / $total - PROCESANDO >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
			
				$cuotas = null;
//				$this->out($persona['Socio']['id']);
				
//				$cuotas = $oSOCIO->getDetalleDeuda($persona['Socio']['id'],$this->periodo_corte,$this->proveedor_id,$this->codigo_organismo);

				switch ($this->tipo_listado):
					case 1:
						//CONSOLIDADO POR SOCIO
//						$cuotas = $this->getDeudaSocioConsolidada($persona['Socio']['id']);
//						break;
					case 2:
						//CONSOLIDADO POR ORDEN DE DESCUENTO
						$cuotas = $this->getDeudaSocioConsolidadaByOrdenDto($persona['Socio']['id']);
						break;
					case 3:
						//DETALLE DE CUOTAS ADEUDADAS
//						$cuotas = $this->getDeudaSocioDetallada($persona['Socio']['id']);
//						break;
                                        case 4:
                                                $cuotas = $this->operacionesPorTerminar($persona['Socio']['id']);
                                                break;
				endswitch;
				

//				if($this->consolidado == 0)$cuotas = $this->getDeudaSocioDetallada($persona['Socio']['id']);
//				else $cuotas = $this->getDeudaSocioConsolidada($persona['Socio']['id']);
				
			
				if(!empty($cuotas)):
				
					foreach($cuotas as $registro):
					
//						debug($registro);

//						if($registro['OrdenDescuentoCuota']['orden_descuento_id'] == 83709){
//							debug($registro);
//							return;
//						}
				
						$temp = array();
						
						switch ($this->tipo_listado):
						
							case 1:
								//CONSOLIDADO POR SOCIO
								$msg = "$i / $total - PROCESANDO >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
								$asinc->actualizar($i,$total,$msg);
								if($registro[0]['saldo_actual'] != 0):
									$temp['AsincronoTemporal'] = array(
											'asincrono_id' => $asinc->id,
											'texto_1' => $persona['Persona']['documento'],
											'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
                                                                                        'texto_3' => $persona['Persona']['calle'],
                                                                                        'texto_4' => $persona['Persona']['numero_calle'],
                                                                                        'texto_5' => $persona['Persona']['piso'],
                                                                                        'texto_6' => $persona['Persona']['dpto'],
                                                                                        'texto_7' => $persona['Persona']['barrio'],
                                                                                        'texto_8' => $persona['Persona']['localidad'],
                                                                                        'texto_9' => $persona['Persona']['codigo_postal'],
                                                                                        'texto_10' => $persona['Provincia']['nombre'],
                                                                                        'texto_11' => $persona['Persona']['telefono_fijo'],
                                                                                        'texto_12' => $persona['Persona']['telefono_movil'],
                                                                                        'texto_13' => $persona['Persona']['telefono_referencia'],
                                                                                        'texto_14' => $persona['Persona']['e_mail'],
                                                                                        'decimal_1' => round($registro[0]['importe'],2),
                                                                                        'decimal_2' => round($registro[0]['pago_acumulado'],2),
                                                                                        'decimal_3' => round($registro[0]['saldo_actual'],2),
                                                                                        'decimal_4' => round($registro[0]['pendiente_acreditar'],2),
                                                                                        'decimal_5' => round($registro[0]['saldo_actual'] - $registro[0]['pendiente_acreditar'],2),
                                                                                        'entero_1' => $persona['Socio']['id']
									);	
								endif;								
								break;
						
						case 2:
							//CONSOLIDADO POR ORDEN DE DESCUENTO
//							$orden = $this->OrdenDescuento->getOrden($registro['OrdenDescuentoCuota']['orden_descuento_id']);
//							$orden = $orden['OrdenDescuento'];
//							debug($registro);
                                                    
//                                                        $saldos = $oCUOTA->getMoraByOrdenDtoHastaPeriodo($registro['OrdenDescuentoCuota']['orden_descuento_id'],$this->periodo_corte);
                                                        $saldos = $oCUOTA->get_mora_by_orden_dto($registro['OrdenDescuentoCuota']['orden_descuento_id'],$this->periodo_corte);
//                                                        debug($saldos);
                                                        
                                                        $orden = array();
//							$orden['pago_acumulado'] = (isset($registro[0]['pago_acumulado']) ? $registro[0]['pago_acumulado'] : 0);
//							$orden['saldo_actual'] = (isset($registro[0]['saldo_actual']) ? $registro[0]['saldo_actual'] : 0);
//							$orden['cuotas_adeudadas'] = (isset($registro[0]['cuotas_adeudadas']) ? $registro[0]['cuotas_adeudadas'] : 0);
//							$orden['cuotas_adeudadas'] = (!empty($orden['cuotas_adeudadas']) ? $orden['cuotas_adeudadas'] : 0);
                                                        
                                                        $orden['id'] = $registro['OrdenDescuentoCuota']['orden_descuento_id'];
                                                        $orden['tipo_nro'] = $registro[0]['tipo_nro'];
                                                        $orden['proveedor_producto'] = $registro['Proveedor']['razon_social']." - " . $registro['TipoProducto']['concepto_1'];
                                                        $orden['periodo_ini'] = $registro['OrdenDescuento']['periodo_ini'];
                                                        $orden['nro_referencia_proveedor'] = $registro['OrdenDescuentoCuota']['nro_referencia_proveedor'];
                                                        $orden['organismo'] = $registro['Organismo']['concepto_1'];
                                                        $orden['empresa'] = $registro['Empresa']['empresa'];
                                                        $orden['turno'] = $registro['LiquidacionTurno']['turno'];
                                                        $orden['beneficio_str'] = $registro[0]['beneficio_str'];
                                                        $orden['proveedor_resumido'] = $registro['Proveedor']['razon_social_resumida'];
                                                        $orden['proveedor'] = $registro['Proveedor']['razon_social'];
                                                        $orden['producto_descripcion'] = $registro['TipoProducto']['concepto_1'];
                                                        $orden['persona_beneficio_id'] = $registro['OrdenDescuentoCuota']['persona_beneficio_id'];
                                                        
                                                        $orden['proveedor_id'] = $registro['Proveedor']['id'];
                                                        $orden['codigo_beneficio'] = $registro['PersonaBeneficio']['codigo_beneficio'];
                                                        $orden['codigo_empresa'] = $registro['PersonaBeneficio']['codigo_empresa'];
                                                        
                                                        $orden['fecha_orden'] = $registro['OrdenDescuento']['fecha'];
                                                        
                                                        
//                                                        $orden['cuotas_avencer'] = $registro[0]['cuotas_avencer'];
//                                                        $orden['saldo_avencer'] = $registro[0]['saldo_avencer'];
//                                                        $orden['importe_total'] = $registro[0]['importe_total'];
//                                                        
//                                                        $orden['saldo_avencer_3'] = $registro[0]['saldo_avencer_3'];
//                                                        $orden['saldo_avencer_6'] = $registro[0]['saldo_avencer_6'];
//                                                        $orden['saldo_avencer_12'] = $registro[0]['saldo_avencer_12'];
//                                                        $orden['saldo_avencer_13'] = $registro[0]['saldo_avencer_13'];
                                                        
                                                        
                                                        $orden['vendedor_apenom'] = (!empty($registro[0]['vendedor_apenom']) ? $registro[0]['vendedor_apenom'] : $registro['Solicitud']['user_created']);
                                                        $orden['vendedor_cuit'] = $registro['Vendedor']['cuit_cuil'];
                                                        $orden['solicitud_fecha'] = $registro[0]['fecha_solicitud'];
                                                        $orden['solicitud_aprobacion'] = $registro[0]['fecha_aprobacion'];
                                                        
                                                        
                                                        
                                                        $orden['importe_percibido'] = $registro['Solicitud']['importe_percibido'];
                                                        $orden['importe_solicitado'] = $registro['Solicitud']['importe_solicitado'];
                                                        
                                                        $orden['importe_percibido'] = ($orden['importe_percibido'] == 0 ? $saldos['importe_total'] : $orden['importe_percibido']);
                                                        $orden['importe_solicitado'] = ($orden['importe_solicitado'] == 0 ? $saldos['importe_total'] : $orden['importe_solicitado']);
                                                        
                                                        $orden['importe_cuota'] = $registro[0]['solicitud_importe_cuota'];
                                                        
							$msg = "$i / $total - PROCESANDO ORDENES >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
							$msg .= " | " . $orden['tipo_nro'] . " - " . $orden['proveedor_producto'];
							
//                                                        $this->out($msg);
//                                                        if($persona['Socio']['id'] == 2757){
//                                                            debug($registro);
//                                                            debug($orden);
//                                                            exit;
//                                                        }
//							debug($orden);
							
							$asinc->actualizar($i,$total,$msg);
							
							if(($saldos['saldo'] + $saldos['saldo_avencer']) > 0):
							
							
								$temp['AsincronoTemporal'] = array(
										'asincrono_id' => $asinc->id,
                                                                                'clave_1' => $persona['Socio']['id'],
                                                                                'clave_2' => $orden['proveedor_id'],
                                                                                'clave_3' => $orden['codigo_beneficio'],
                                                                                'clave_4' => $orden['codigo_empresa'],
                                                                                'clave_5' => $orden['vendedor_apenom'],
										'texto_1' => $persona['Persona']['documento'],
										'texto_2' => utf8_decode($persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre']),
										'texto_3' => $orden['tipo_nro'],
										'texto_4' => $orden['proveedor_producto'],
										'texto_5' => $this->OrdenDescuento->periodo($orden['periodo_ini']),
										'texto_6' => $orden['nro_referencia_proveedor'],
										'texto_7' => $saldos['cuotas_vencidas'],
										'texto_10' => $orden['organismo'],
                                                                                'texto_8' => $orden['empresa'],
                                                                                'texto_9' => $orden['turno'],
										'texto_11' => $orden['beneficio_str'],
										'texto_12' => $orden['proveedor_resumido'],
                                                                                'texto_13' => $orden['id'],
                                                                                'texto_14' => $orden['proveedor'],
                                                                                'texto_15' => $orden['producto_descripcion'],
                                                                                'texto_16' => $orden['vendedor_apenom'],
                                                                                'texto_17' => $orden['vendedor_cuit'],
                                                                                'texto_18' => $orden['solicitud_fecha'],
                                                                                'texto_19' => $orden['solicitud_aprobacion'],
										'decimal_1' => round($saldos['importe_total'],2),
										'decimal_2' => round($saldos['cobrado'],2),
										'decimal_3' => round($saldos['saldo'],2),
                                                                                'decimal_4' => round($saldos['pendiente_acreditar'],2),
                                                                                'decimal_5' => round($saldos['saldo'] - $saldos['pendiente_acreditar'],2),                                                                                  
                                                                                'decimal_6' => round($saldos['saldo_avencer'],2),
                                                                                'decimal_7' => round($saldos['saldoav_03'],2),
                                                                                'decimal_8' => round($saldos['saldoav_06'],2),
                                                                                'decimal_9' => round($saldos['saldoav_12'],2),
                                                                                'decimal_10' => round($saldos['saldoav_13'],2),
                                                                                'decimal_11' => round($saldos['saldo_0003'],2),
                                                                                'decimal_12' => round($saldos['saldo_0306'],2),
                                                                                'decimal_13' => round($saldos['saldo_0609'],2),
                                                                                'decimal_14' => round($saldos['saldo_0912'],2),
                                                                                'decimal_15' => round($saldos['saldo_1213'],2),
                                                                                'decimal_16' => round($saldos['cobrado_optimo'],2),
                                                                                'decimal_17' => round($saldos['cobrado_indice'],2),
                                                                                'decimal_18' => round($saldos['devengado'],2),
                                                                                'decimal_19' => round($orden['importe_percibido'],2),
                                                                                'decimal_20' => round($orden['importe_cuota'],2),
										'entero_1' => $persona['Socio']['id'],
										'entero_2' => $orden['persona_beneficio_id'],
										'entero_3' => $orden['id'],
                                                                                'entero_4' => $saldos['cuotas_avencer'],
                                                                                'entero_5' => $saldos['cuotas_vencidas'],
                                                                                'entero_6' => $persona[0]['edad'],
										
								);	
//							debug($temp);
							
							endif;
							break;						

						case 3:
							//DETALLE DE CUOTAS ADEUDADAS
//							$cuota = $this->OrdenDescuentoCuota->infoCuota($registro['OrdenDescuentoCuota']);
							
							$cuota = $oCUOTA->getCuota($registro['OrdenDescuentoCuota']['id'],false);
							$cuota = $cuota['OrdenDescuentoCuota'];

							
							$cuota['pago_acumulado'] = (isset($registro[0]['cobrado']) ? $registro[0]['cobrado'] : 0);
							
							$cuota['saldo_actual'] = $cuota['importe'] - $cuota['pago_acumulado'];
							
							$msg = "$i / $total - PROCESANDO >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
							$msg .= " | " . $cuota['tipo_nro'] . " - " . $cuota['cuota'];
							
							$asinc->actualizar($i,$total,$msg);
							
							if($cuota['saldo_actual'] != 0):
							
								$temp['AsincronoTemporal'] = array(
										'asincrono_id' => $asinc->id,
										'texto_1' => $persona['Persona']['documento'],
										'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
										'texto_3' => $cuota['tipo_nro'],
										'texto_4' => $cuota['proveedor_producto'],
										'texto_5' => $cuota['tipo_cuota_desc'],
										'texto_6' => $cuota['cuota'],
										'texto_7' => $cuota['periodo'],
										'texto_8' => $cuota['orden_descuento_id'],
										'texto_9' => $cuota['nro_referencia_proveedor'],
										'texto_10' => $cuota['organismo'],
										'texto_11' => $cuota['beneficio'],
										'texto_12' => $cuota['proveedor'],
										'decimal_1' => round($cuota['importe'],2),
										'decimal_2' => round($cuota['pago_acumulado'],2),
										'decimal_3' => round($cuota['saldo_actual'],2),
                                        'decimal_4' => round($registro[0]['pendiente_acreditar'],2),
                                        'decimal_5' => round($cuota['saldo_actual'] - $registro[0]['pendiente_acreditar'],2),                                                                       
										'entero_1' => $persona['Socio']['id'],
										'entero_2' => $cuota['persona_beneficio_id'],
										
								);	

							endif;
							break;							
						
                                                        case 4:
                                                            
                                                            if(!empty($registro)){
                                                                $msg = "$i / $total - PROCESANDO >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
                                                                $msg .= " | " . $registro['o']['tipo_orden_dto']." #".$registro['o']['numero'] . "[ ".$registro[0]['cuotas']." ]";

                                                                $asinc->actualizar($i,$total,$msg);
                                                                
//                                                                debug($registro);
                                                                $temp['AsincronoTemporal'] = array(
                                                                    'asincrono_id' => $asinc->id,
//                                                                    'texto_1' => $persona['Persona']['documento'],
//                                                                    'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
//                                                                    'texto_3' => $registro['p']['calle'],
//                                                                    'texto_4' => $registro['p']['numero_calle'],
//                                                                    'texto_5' => $registro['p']['barrio'],
//                                                                    'texto_6' => $registro['p']['codigo_postal'],
//                                                                    'texto_7' => $registro['p']['localidad'],
//                                                                    'texto_8' => $registro['p']['telefono_fijo'],
//                                                                    'texto_9' => $registro['p']['telefono_movil'],
                                                                    'texto_1' => $persona['Persona']['documento'],
                                                                    'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
                                                                    'texto_3' => $persona['Persona']['calle'],
                                                                    'texto_4' => $persona['Persona']['numero_calle'],
                                                                    'texto_5' => $persona['Persona']['piso'],
                                                                    'texto_6' => $persona['Persona']['dpto'],
                                                                    'texto_7' => $persona['Persona']['barrio'],
                                                                    'texto_8' => $persona['Persona']['localidad'],
                                                                    'texto_9' => $persona['Persona']['codigo_postal'],
                                                                    'texto_10' => $persona['Provincia']['nombre'],
                                                                    'texto_11' => $persona['Persona']['telefono_fijo'],
                                                                    'texto_12' => $persona['Persona']['telefono_movil'],
                                                                    'texto_13' => $persona['Persona']['telefono_referencia'],
                                                                    'texto_14' => $persona['Persona']['e_mail'],                                                                    
                                                                    'texto_15' => $registro['o']['tipo_orden_dto']." #".$registro['o']['numero'],
                                                                    'entero_1' => $registro[0]['cuotas'],
                                                                    'decimal_1' => $registro[0]['mora'],
                                                                    'texto_16' => $registro['cb']['organismo'],
                                                                    'texto_17' => $registro['ba']['nombre'],
                                                                    'texto_18' => $registro['Vendedor']['cuit_cuil'],
                                                                    'texto_19' => $registro[0]['vendedor_apenom'],
                                                                    'entero_2' => $registro['so']['activo'],
                                                                    'entero_3' => $registro['o']['orden_descuento'],
                                                                    'entero_4' => $persona['Socio']['id']
                                                                );
                                                                
                                                                
                                                            }
                                                            
                                                            
                                                            break;
                                                        
						endswitch;
						
						
//						if($this->consolidado == 0):
//						
//							//ARMO LOS DATOS
//							$cuota = $this->OrdenDescuentoCuota->infoCuota($registro['OrdenDescuentoCuota']);
//							$cuota['pago_acumulado'] = (isset($registro[0]['cobrado']) ? $registro[0]['cobrado'] : 0);
//							
//							$cuota['saldo_actual'] = $cuota['importe'] - $cuota['pago_acumulado'];
//							
//							$msg = "$i / $total - PROCESANDO >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
//							$msg .= " | " . $cuota['tipo_nro'] . " - " . $cuota['cuota_de_cuotas'];
//							
//							$asinc->actualizar($i,$total,$msg);
//							
//							if($cuota['saldo_actual'] != 0):
//							
//								$temp['AsincronoTemporal'] = array(
//										'asincrono_id' => $asinc->id,
//										'texto_1' => $persona['Persona']['documento'],
//										'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
//										'texto_3' => $cuota['tipo_nro'],
//										'texto_4' => $cuota['proveedor_producto'],
//										'texto_5' => $cuota['tipo_cuota_desc'],
//										'texto_6' => $cuota['cuota_de_cuotas'],
//										'texto_7' => $cuota['periodo'],
//										'texto_8' => $cuota['orden_descuento_id'],
//										'texto_9' => $cuota['nro_referencia_proveedor'],
//										'texto_10' => $cuota['organismo'],
//										'texto_11' => $cuota['beneficio'],
//										'texto_12' => $cuota['proveedor'],
//										'decimal_1' => round($cuota['importe'],2),
//										'decimal_2' => round($cuota['pago_acumulado'],2),
//										'decimal_3' => round($cuota['saldo_actual'],2),
//										'entero_1' => $persona['Socio']['id'],
//										'entero_2' => $cuota['persona_beneficio_id'],
//										
//								);	
//								
//							endif;
//								
//						else:
//						
//							$msg = "$i / $total - PROCESANDO >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
//							
//							$asinc->actualizar($i,$total,$msg);
//						
//							if($registro[0]['saldo_actual'] != 0):
//							
//								$temp['AsincronoTemporal'] = array(
//										'asincrono_id' => $asinc->id,
//										'texto_1' => $persona['Persona']['documento'],
//										'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
//										'decimal_1' => round($registro[0]['importe'],2),
//										'decimal_2' => round($registro[0]['pago_acumulado'],2),
//										'decimal_3' => round($registro[0]['saldo_actual'],2),
//										'entero_1' => $persona['Socio']['id']
//										
//								);	
//								
//							endif;						
//						
//						endif;
						
// 						debug($temp);
							
						if($asinc->detenido()){
							$STOP = 1;
							break;
						}				
						
						
						if(!empty($temp)):
						
                                                    $this->Temporal->writeXLSRow(0,$temp['AsincronoTemporal']);
                                                    
							if(!$this->Temporal->grabar($temp)){
								$STOP = 1;
								break;
							}								
						
						endif;	
						
						
				
					endforeach;
					
				endif;
				
			
				$i++;
				
			endforeach;
		
		
		endif;
		
                ##################################################################################
                #TOTALIZAR POR PERSONAS
                ##################################################################################
                $asinc->actualizar(5,100,"ESPERE, GENERANDO RESUMEN POR SOCIO...");
                $set = array();
                $set['sheet_title'] = 'CONSOLIDADO_SOCIOS';
                $set['labels'] = array(
//                    'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR SOCIO'
                );
                $set['columns'] = array(
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
                                    'decimal_1' => 'TOTAL_ORDEN',
                                    'decimal_16' => 'COBRADO_OPTIMO',
                                    'decimal_2' => 'COBRADO',
                                    'decimal_17' => 'INDICE_COBRANZA',
                                    'decimal_3' => 'SALDO_CONCILIADO',	
                                    'decimal_4' => 'PENDIENTE_ACREDITAR',
                                    'decimal_5' => 'SALDO_A_CONCILIAR',
                                    'decimal_11' => 'MORA_0_3_MESES',    
                                    'decimal_12' => 'MORA_3_6_MESES',        
                                    'decimal_13' => 'MORA_6_9_MESES',
                                    'decimal_14' => 'MORA_9_12_MESES',  
                                    'decimal_15' => 'MORA_MAS_12_MESES', 
                                    'decimal_6' => 'SALDO_A_VENCER',
                                    'entero_5' => 'CUOTAS_A_VENCER',
                                    'decimal_7' => 'A_VENCER_3_MESES',    
                                    'decimal_8' => 'A_VENCER_6_MESES',        
                                    'decimal_9' => 'A_VENCER_12_MESES',
                                    'decimal_10' => 'A_VENCER_MAS_12_MESES',        

                );		                        
                $this->Temporal->prepareXLSSheet(1,$set); 
                
                $total = count($personas);
                $asinc->setTotal($total);
                $i = 0;	
                
                
                foreach($personas as $persona){
                    
                    $sql =  "   select 
                                sum(decimal_1) as decimal_1 
                                ,sum(decimal_2) as decimal_2
                                ,sum(decimal_3) as decimal_3
                                ,sum(decimal_4) as decimal_4
                                ,sum(decimal_5) as decimal_5
                                ,sum(decimal_6) as decimal_6
                                ,sum(decimal_7) as decimal_7
                                ,sum(decimal_8) as decimal_8
                                ,sum(decimal_9) as decimal_9
                                ,sum(decimal_10) as decimal_10
                                ,sum(decimal_11) as decimal_11
                                ,sum(decimal_12) as decimal_12
                                ,sum(decimal_13) as decimal_13
                                ,sum(decimal_14) as decimal_14
                                ,sum(decimal_15) as decimal_15
                                ,sum(decimal_16) as decimal_16
                                ,sum(decimal_2) / sum(decimal_16) as decimal_17
                                ,sum(entero_5) as entero_5
                                from asincrono_temporales
                                where asincrono_id = $asinc->id
                                and clave_1 = " . $persona['Socio']['id']." and (decimal_3 + decimal_6) > 0 group by clave_1;";
                    $datos = $oCUOTA->query($sql);
                    
                    $temp = array();
                    
                    if(!empty($datos)){
                        
                        $msg = "$i / $total - TOTALIZANDO SOCIOS >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
                        $asinc->actualizar($i,$total,$msg);                        
                        
                        $i++;
                        
                        $temp['AsincronoTemporal'] = array(
                                        'asincrono_id' => $asinc->id,
                                        'texto_1' => $persona['Persona']['documento'],
                                        'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
                                        'texto_3' => $persona['Persona']['calle'],
                                        'texto_4' => $persona['Persona']['numero_calle'],
                                        'texto_5' => $persona['Persona']['piso'],
                                        'texto_6' => $persona['Persona']['dpto'],
                                        'texto_7' => $persona['Persona']['barrio'],
                                        'texto_8' => $persona['Persona']['localidad'],
                                        'texto_9' => $persona['Persona']['codigo_postal'],
                                        'texto_10' => $persona['Provincia']['nombre'],
                                        'texto_11' => $persona['Persona']['telefono_fijo'],
                                        'texto_12' => $persona['Persona']['telefono_movil'],
                                        'texto_13' => $persona['Persona']['telefono_referencia'],
                                        'texto_14' => $persona['Persona']['e_mail'],
                                        'decimal_1' => round($datos[0][0]['decimal_1'],2),
                                        'decimal_2' => round($datos[0][0]['decimal_2'],2),
                                        'decimal_3' => round($datos[0][0]['decimal_3'],2),
                                        'decimal_4' => round($datos[0][0]['decimal_4'],2),
                                        'decimal_5' => round($datos[0][0]['decimal_5'],2),                            
                                        'decimal_6' => round($datos[0][0]['decimal_6'],2),
                                        'decimal_7' => round($datos[0][0]['decimal_7'],2),
                                        'decimal_8' => round($datos[0][0]['decimal_8'],2),
                                        'decimal_9' => round($datos[0][0]['decimal_9'],2),
                                        'decimal_10' => round($datos[0][0]['decimal_10'],2),
                                        'decimal_11' => round($datos[0][0]['decimal_11'],2),
                                        'decimal_12' => round($datos[0][0]['decimal_12'],2),
                                        'decimal_13' => round($datos[0][0]['decimal_13'],2),
                                        'decimal_14' => round($datos[0][0]['decimal_14'],2),
                                        'decimal_15' => round($datos[0][0]['decimal_15'],2),
                                        'decimal_16' => round($datos[0][0]['decimal_16'],2),
                                        'decimal_17' => round($datos[0][0]['decimal_17'],2),
                                        'entero_1' => $persona['Socio']['id'],
                                        'entero_5' => $datos[0][0]['entero_5'],
                        );                        
                        $this->Temporal->writeXLSRow(1,$temp['AsincronoTemporal']);
                    }
                    
                } // ENDFOREACH PERSONAS
                
                
                ##################################################################################
                #TOTALIZAR POR ORGANISMO EMPRESA
                ##################################################################################
                $asinc->actualizar(5,100,"ESPERE, GENERANDO RESUMEN POR ORGANISMO...");
                $set = array();
                $set['sheet_title'] = 'CONSOLIDADO_ORGANISMO';
                $set['labels'] = array(
//                    'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR SOCIO'
                );
                $set['columns'] = array(
                                    'texto_1' => 'ORGANISMO',
                                    'texto_2' => 'EMPRESA',
                                    'decimal_1' => 'TOTAL_ORDEN',
                                    'decimal_16' => 'COBRADO_OPTIMO',
                                    'decimal_2' => 'COBRADO',
                                    'decimal_17' => 'INDICE_COBRANZA',
                                    'decimal_3' => 'SALDO_CONCILIADO',	
                                    'decimal_4' => 'PENDIENTE_ACREDITAR',
                                    'decimal_5' => 'SALDO_A_CONCILIAR',
                                    'decimal_11' => 'MORA_0_3_MESES',    
                                    'decimal_12' => 'MORA_3_6_MESES',        
                                    'decimal_13' => 'MORA_6_9_MESES',
                                    'decimal_14' => 'MORA_9_12_MESES',  
                                    'decimal_15' => 'MORA_MAS_12_MESES', 
                                    'decimal_6' => 'SALDO_A_VENCER',
                                    'decimal_7' => 'A_VENCER_3_MESES',    
                                    'decimal_8' => 'A_VENCER_6_MESES',        
                                    'decimal_9' => 'A_VENCER_12_MESES',
                                    'decimal_10' => 'A_VENCER_MAS_12_MESES',        

                );		                        
                $this->Temporal->prepareXLSSheet(2,$set); 
                
                $sql = "select
                        texto_10,texto_11,texto_8
                        ,sum(decimal_1) as decimal_1 
                        ,sum(decimal_2) as decimal_2
                        ,sum(decimal_3) as decimal_3
                        ,sum(decimal_4) as decimal_4
                        ,sum(decimal_5) as decimal_5
                        ,sum(decimal_6) as decimal_6
                        ,sum(decimal_7) as decimal_7
                        ,sum(decimal_8) as decimal_8
                        ,sum(decimal_9) as decimal_9
                        ,sum(decimal_10) as decimal_10
                        ,sum(decimal_11) as decimal_11
                        ,sum(decimal_12) as decimal_12
                        ,sum(decimal_13) as decimal_13
                        ,sum(decimal_14) as decimal_14
                        ,sum(decimal_15) as decimal_15
                        ,sum(decimal_16) as decimal_16
                        ,sum(decimal_2) / sum(decimal_16) as decimal_17
                        from asincrono_temporales as AsincronoTemporal
                        where asincrono_id = $asinc->id and (decimal_3 + decimal_6) > 0
                        group by clave_3,clave_4; ";
                $datos = $oCUOTA->query($sql);
                
                $total = count($datos);
                $asinc->setTotal($total);
                $i = 0;	
                
                if(!empty($datos)){
                    foreach ($datos as $dato){
                        
                        $msg = "$i / $total - TOTALIZANDO ORGANISMOS >> " . $dato['AsincronoTemporal']['texto_10'] . " | " . $dato['AsincronoTemporal']['texto_11'];
                        $asinc->actualizar($i,$total,$msg);                        
                        
                        $i++;
                        
                        $temp['AsincronoTemporal'] = array(
                                        'asincrono_id' => $asinc->id,
                                        'texto_1' => $dato['AsincronoTemporal']['texto_10'],
                                        'texto_2' => $dato['AsincronoTemporal']['texto_8'],
                                        'decimal_1' => round($dato[0]['decimal_1'],2),
                                        'decimal_2' => round($dato[0]['decimal_2'],2),
                                        'decimal_3' => round($dato[0]['decimal_3'],2),
                                        'decimal_4' => round($dato[0]['decimal_4'],2),
                                        'decimal_5' => round($dato[0]['decimal_5'],2),                            
                                        'decimal_6' => round($dato[0]['decimal_6'],2),
                                        'decimal_7' => round($dato[0]['decimal_7'],2),
                                        'decimal_8' => round($dato[0]['decimal_8'],2),
                                        'decimal_9' => round($dato[0]['decimal_9'],2),
                                        'decimal_10' => round($dato[0]['decimal_10'],2),
                                        'decimal_11' => round($dato[0]['decimal_11'],2),
                                        'decimal_12' => round($dato[0]['decimal_12'],2),
                                        'decimal_13' => round($dato[0]['decimal_13'],2),
                                        'decimal_14' => round($dato[0]['decimal_14'],2),
                                        'decimal_15' => round($dato[0]['decimal_15'],2),
                                        'decimal_16' => round($dato[0]['decimal_16'],2),
                                        'decimal_17' => round($dato[0]['decimal_17'],2),
                        );                        
                        $this->Temporal->writeXLSRow(2,$temp['AsincronoTemporal']);                        
                        
                    }
                }
                
               
                ##################################################################################
                #TOTALIZAR POR PROVEEDOR
                ##################################################################################
                $asinc->actualizar(5,100,"ESPERE, GENERANDO RESUMEN POR PROVEEDOR...");
                $set = array();
                $set['sheet_title'] = 'CONSOLIDADO_PROVEEDOR';
                $set['labels'] = array(
//                    'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR SOCIO'
                );
                $set['columns'] = array(
                                    'texto_1' => 'PROVEEDOR',
                                    'decimal_1' => 'TOTAL_ORDEN',
                                    'decimal_16' => 'COBRADO_OPTIMO',
                                    'decimal_2' => 'COBRADO',
                                    'decimal_17' => 'INDICE_COBRANZA',
                                    'decimal_3' => 'SALDO_CONCILIADO',	
                                    'decimal_4' => 'PENDIENTE_ACREDITAR',
                                    'decimal_5' => 'SALDO_A_CONCILIAR',
                                    'decimal_11' => 'MORA_0_3_MESES',    
                                    'decimal_12' => 'MORA_3_6_MESES',        
                                    'decimal_13' => 'MORA_6_9_MESES',
                                    'decimal_14' => 'MORA_9_12_MESES',  
                                    'decimal_15' => 'MORA_MAS_12_MESES', 
                                    'decimal_6' => 'SALDO_A_VENCER',
                                    'decimal_7' => 'A_VENCER_3_MESES',    
                                    'decimal_8' => 'A_VENCER_6_MESES',        
                                    'decimal_9' => 'A_VENCER_12_MESES',
                                    'decimal_10' => 'A_VENCER_MAS_12_MESES',        

                );		                        
                $this->Temporal->prepareXLSSheet(3,$set); 
                
                $sql = "select
                        texto_14
                        ,sum(decimal_1) as decimal_1 
                        ,sum(decimal_2) as decimal_2
                        ,sum(decimal_3) as decimal_3
                        ,sum(decimal_4) as decimal_4
                        ,sum(decimal_5) as decimal_5
                        ,sum(decimal_6) as decimal_6
                        ,sum(decimal_7) as decimal_7
                        ,sum(decimal_8) as decimal_8
                        ,sum(decimal_9) as decimal_9
                        ,sum(decimal_10) as decimal_10
                        ,sum(decimal_11) as decimal_11
                        ,sum(decimal_12) as decimal_12
                        ,sum(decimal_13) as decimal_13
                        ,sum(decimal_14) as decimal_14
                        ,sum(decimal_15) as decimal_15
                        ,sum(decimal_16) as decimal_16
                        ,sum(decimal_2) / sum(decimal_16) as decimal_17
                        from asincrono_temporales as AsincronoTemporal
                        where asincrono_id = $asinc->id and (decimal_3 + decimal_6) > 0
                        group by clave_2;";
                $datos = $oCUOTA->query($sql);
                
                $total = count($datos);
                $asinc->setTotal($total);
                $i = 0;	
                
                if(!empty($datos)){
                    foreach ($datos as $dato){
                        
                        $msg = "$i / $total - TOTALIZANDO PROVEEDORES >> " . $dato['AsincronoTemporal']['texto_14'];
                        $asinc->actualizar($i,$total,$msg);                        
                        
                        $i++;
                        
                        $temp['AsincronoTemporal'] = array(
                                        'asincrono_id' => $asinc->id,
                                        'texto_1' => $dato['AsincronoTemporal']['texto_14'],
                                        'decimal_1' => round($dato[0]['decimal_1'],2),
                                        'decimal_2' => round($dato[0]['decimal_2'],2),
                                        'decimal_3' => round($dato[0]['decimal_3'],2),
                                        'decimal_4' => round($dato[0]['decimal_4'],2),
                                        'decimal_5' => round($dato[0]['decimal_5'],2),                            
                                        'decimal_6' => round($dato[0]['decimal_6'],2),
                                        'decimal_7' => round($dato[0]['decimal_7'],2),
                                        'decimal_8' => round($dato[0]['decimal_8'],2),
                                        'decimal_9' => round($dato[0]['decimal_9'],2),
                                        'decimal_10' => round($dato[0]['decimal_10'],2),
                                        'decimal_11' => round($dato[0]['decimal_11'],2),
                                        'decimal_12' => round($dato[0]['decimal_12'],2),
                                        'decimal_13' => round($dato[0]['decimal_13'],2),
                                        'decimal_14' => round($dato[0]['decimal_14'],2),
                                        'decimal_15' => round($dato[0]['decimal_15'],2),
                                        'decimal_16' => round($dato[0]['decimal_16'],2),
                                        'decimal_17' => round($dato[0]['decimal_17'],2),
                        );                        
                        $this->Temporal->writeXLSRow(3,$temp['AsincronoTemporal']);                        
                        
                    }
                }                
                
		
                ##################################################################################
                #TOTALIZAR POR USUARIO
                ##################################################################################
                $asinc->actualizar(5,100,"ESPERE, GENERANDO RESUMEN POR USUARIO...");
                $set = array();
                $set['sheet_title'] = 'CONSOLIDADO_USUARIO';
                $set['labels'] = array(
//                    'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR SOCIO'
                );
                $set['columns'] = array(
                                    'texto_1' => 'USUARIO_VENDEDOR',
                                    'decimal_1' => 'TOTAL_ORDEN',
                                    'decimal_16' => 'COBRADO_OPTIMO',
                                    'decimal_2' => 'COBRADO',
                                    'decimal_17' => 'INDICE_COBRANZA',
                                    'decimal_3' => 'SALDO_CONCILIADO',	
                                    'decimal_4' => 'PENDIENTE_ACREDITAR',
                                    'decimal_5' => 'SALDO_A_CONCILIAR',
                                    'decimal_11' => 'MORA_0_3_MESES',    
                                    'decimal_12' => 'MORA_3_6_MESES',        
                                    'decimal_13' => 'MORA_6_9_MESES',
                                    'decimal_14' => 'MORA_9_12_MESES',  
                                    'decimal_15' => 'MORA_MAS_12_MESES', 
                                    'decimal_6' => 'SALDO_A_VENCER',
                                    'decimal_7' => 'A_VENCER_3_MESES',    
                                    'decimal_8' => 'A_VENCER_6_MESES',        
                                    'decimal_9' => 'A_VENCER_12_MESES',
                                    'decimal_10' => 'A_VENCER_MAS_12_MESES',        

                );		                        
                $this->Temporal->prepareXLSSheet(4,$set); 
                
                $sql = "select
                        texto_16
                        ,sum(decimal_1) as decimal_1 
                        ,sum(decimal_2) as decimal_2
                        ,sum(decimal_3) as decimal_3
                        ,sum(decimal_4) as decimal_4
                        ,sum(decimal_5) as decimal_5
                        ,sum(decimal_6) as decimal_6
                        ,sum(decimal_7) as decimal_7
                        ,sum(decimal_8) as decimal_8
                        ,sum(decimal_9) as decimal_9
                        ,sum(decimal_10) as decimal_10
                        ,sum(decimal_11) as decimal_11
                        ,sum(decimal_12) as decimal_12
                        ,sum(decimal_13) as decimal_13
                        ,sum(decimal_14) as decimal_14
                        ,sum(decimal_15) as decimal_15
                        ,sum(decimal_16) as decimal_16
                        ,sum(decimal_2) / sum(decimal_16) as decimal_17
                        from asincrono_temporales as AsincronoTemporal
                        where asincrono_id = $asinc->id and (decimal_3 + decimal_6) > 0
                        group by clave_5;";
                $datos = $oCUOTA->query($sql);
                
                $total = count($datos);
                $asinc->setTotal($total);
                $i = 0;	
                
                if(!empty($datos)){
                    foreach ($datos as $dato){
                        
                        $msg = "$i / $total - TOTALIZANDO USUARIOS >> " . $dato['AsincronoTemporal']['texto_16'];
                        $asinc->actualizar($i,$total,$msg);                        
                        
                        $i++;
                        
                        $temp['AsincronoTemporal'] = array(
                                        'asincrono_id' => $asinc->id,
                                        'texto_1' => $dato['AsincronoTemporal']['texto_16'],
                                        'decimal_1' => round($dato[0]['decimal_1'],2),
                                        'decimal_2' => round($dato[0]['decimal_2'],2),
                                        'decimal_3' => round($dato[0]['decimal_3'],2),
                                        'decimal_4' => round($dato[0]['decimal_4'],2),
                                        'decimal_5' => round($dato[0]['decimal_5'],2),                            
                                        'decimal_6' => round($dato[0]['decimal_6'],2),
                                        'decimal_7' => round($dato[0]['decimal_7'],2),
                                        'decimal_8' => round($dato[0]['decimal_8'],2),
                                        'decimal_9' => round($dato[0]['decimal_9'],2),
                                        'decimal_10' => round($dato[0]['decimal_10'],2),
                                        'decimal_11' => round($dato[0]['decimal_11'],2),
                                        'decimal_12' => round($dato[0]['decimal_12'],2),
                                        'decimal_13' => round($dato[0]['decimal_13'],2),
                                        'decimal_14' => round($dato[0]['decimal_14'],2),
                                        'decimal_15' => round($dato[0]['decimal_15'],2),
                                        'decimal_16' => round($dato[0]['decimal_16'],2),
                                        'decimal_17' => round($dato[0]['decimal_17'],2),
                        );                        
                        $this->Temporal->writeXLSRow(4,$temp['AsincronoTemporal']);                        
                        
                    }
                }

                ##################################################################################
                #TOTALIZAR POR PERSONAS / PROVEEDOR
                ##################################################################################
                $asinc->actualizar(5,100,"ESPERE, GENERANDO RESUMEN POR SOCIO / PROVEEDOR...");
                $set = array();
                $set['sheet_title'] = 'CONSOLIDADO_SOCIOS_PROV';
                $set['labels'] = array(
//                    'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR SOCIO'
                );
                $set['columns'] = array(
                                    'texto_15' => 'PROVEEDOR',
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
                                    'decimal_1' => 'TOTAL_ORDEN',
                                    'decimal_16' => 'COBRADO_OPTIMO',
                                    'decimal_2' => 'COBRADO',
                                    'decimal_17' => 'INDICE_COBRANZA',
                                    'decimal_3' => 'SALDO_CONCILIADO',	
                                    'decimal_4' => 'PENDIENTE_ACREDITAR',
                                    'decimal_5' => 'SALDO_A_CONCILIAR',
                                    'decimal_11' => 'MORA_0_3_MESES',    
                                    'decimal_12' => 'MORA_3_6_MESES',        
                                    'decimal_13' => 'MORA_6_9_MESES',
                                    'decimal_14' => 'MORA_9_12_MESES',  
                                    'decimal_15' => 'MORA_MAS_12_MESES', 
                                    'decimal_6' => 'SALDO_A_VENCER',
                                    'entero_5' => 'CUOTAS_A_VENCER',
                                    'decimal_7' => 'A_VENCER_3_MESES',    
                                    'decimal_8' => 'A_VENCER_6_MESES',        
                                    'decimal_9' => 'A_VENCER_12_MESES',
                                    'decimal_10' => 'A_VENCER_MAS_12_MESES',        

                );		                        
                $this->Temporal->prepareXLSSheet(5,$set); 
                
                $total = count($personas);
                $asinc->setTotal($total);
                $i = 0;	

                foreach($personas as $persona){
                    
                    $sql =  "   select 
                                sum(decimal_1) as decimal_1 
                                ,sum(decimal_2) as decimal_2
                                ,sum(decimal_3) as decimal_3
                                ,sum(decimal_4) as decimal_4
                                ,sum(decimal_5) as decimal_5
                                ,sum(decimal_6) as decimal_6
                                ,sum(decimal_7) as decimal_7
                                ,sum(decimal_8) as decimal_8
                                ,sum(decimal_9) as decimal_9
                                ,sum(decimal_10) as decimal_10
                                ,sum(decimal_11) as decimal_11
                                ,sum(decimal_12) as decimal_12
                                ,sum(decimal_13) as decimal_13
                                ,sum(decimal_14) as decimal_14
                                ,sum(decimal_15) as decimal_15
                                ,sum(decimal_16) as decimal_16
                                ,sum(decimal_2) / sum(decimal_16) as decimal_17
                                ,sum(entero_5) as entero_5
                                ,ltrim(rtrim(texto_14)) as texto_14
                                from asincrono_temporales
                                where asincrono_id = $asinc->id
                                and clave_1 = " . $persona['Socio']['id']." and (decimal_3 + decimal_6) > 0 group by clave_1,clave_2;";
                    $datos = $oCUOTA->query($sql);
                    
                    $temp = array();
                    
                    if(!empty($datos)){
                        
                        foreach($datos as $dato){
                            
                            $msg = "$i / $total - TOTALIZANDO SOCIOS/PROV >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
                            $asinc->actualizar($i,$total,$msg);                        

                            $i++;  
                            
                            
                            $temp['AsincronoTemporal'] = array(
                                            'asincrono_id' => $asinc->id,
                                            'texto_1' => $persona['Persona']['documento'],
                                            'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
                                            'texto_3' => $persona['Persona']['calle'],
                                            'texto_4' => $persona['Persona']['numero_calle'],
                                            'texto_5' => $persona['Persona']['piso'],
                                            'texto_6' => $persona['Persona']['dpto'],
                                            'texto_7' => $persona['Persona']['barrio'],
                                            'texto_8' => $persona['Persona']['localidad'],
                                            'texto_9' => $persona['Persona']['codigo_postal'],
                                            'texto_10' => $persona['Provincia']['nombre'],
                                            'texto_11' => $persona['Persona']['telefono_fijo'],
                                            'texto_12' => $persona['Persona']['telefono_movil'],
                                            'texto_13' => $persona['Persona']['telefono_referencia'],
                                            'texto_14' => $persona['Persona']['e_mail'],
                                            'texto_15' => $dato[0]['texto_14'],
                                            'decimal_1' => round($dato[0]['decimal_1'],2),
                                            'decimal_2' => round($dato[0]['decimal_2'],2),
                                            'decimal_3' => round($dato[0]['decimal_3'],2),
                                            'decimal_4' => round($dato[0]['decimal_4'],2),
                                            'decimal_5' => round($dato[0]['decimal_5'],2),                            
                                            'decimal_6' => round($dato[0]['decimal_6'],2),
                                            'decimal_7' => round($dato[0]['decimal_7'],2),
                                            'decimal_8' => round($dato[0]['decimal_8'],2),
                                            'decimal_9' => round($dato[0]['decimal_9'],2),
                                            'decimal_10' => round($dato[0]['decimal_10'],2),
                                            'decimal_11' => round($dato[0]['decimal_11'],2),
                                            'decimal_12' => round($dato[0]['decimal_12'],2),
                                            'decimal_13' => round($dato[0]['decimal_13'],2),
                                            'decimal_14' => round($dato[0]['decimal_14'],2),
                                            'decimal_15' => round($dato[0]['decimal_15'],2),
                                            'decimal_16' => round($dato[0]['decimal_16'],2),
                                            'decimal_17' => round($dato[0]['decimal_17'],2),
                                            'entero_1' => $persona['Socio']['id'],
                                            'entero_5' => $datos[0][0]['entero_5'],
                            );                        
                            $this->Temporal->writeXLSRow(5,$temp['AsincronoTemporal']);                            
                            
                        }
                        

                        

                    }
                    
                } // ENDFOREACH PERSONAS
                
                
                ##################################################################################
                #PLANILLA PARA ESTUDIO JURIDICO
                ##################################################################################
                $asinc->actualizar(5,100,"ESPERE, GENERANDO PLANILLA GENERAL...");
                $set = array();
                $set['sheet_title'] = 'PLANILLA';
                $set['labels'] = array(
//                    'B1' => 'LISTADO DE DEUDA CONSOLIDADO POR SOCIO'
                );
                $set['columns'] = array(
                                    'texto_1' => 'PROVEEDOR',
                                    'texto_2' => 'FECHA_SOLICITUD',
                                    'texto_3' => 'FECHA_MORA',
                                    'texto_4' => 'DNI',
                                    'decimal_1' => 'CAPITAL',
                                    'texto_5' => 'SOLICITANTE',
                                    'entero_1' => 'EDAD',
                                    'texto_6' => 'PRESTAMO',
                                    'decimal_2' => 'CAPITAL',
                                    'entero_2' => 'CUOTAS_PAGAS',
                                    'entero_3' => 'CUOTAS',
                                    'decimal_3' => 'IMPORTE_PAGARE',
                                    'decimal_4' => 'COBRADO',
                                    'entero_4' => 'CUOTAS',
                                    'entero_5' => 'CUOTAS_MORA',
                                    'decimal_5' => 'MORA',
                                    'decimal_6' => 'DEUDA',
                                    'decimal_7' => 'IMPORTE_CUOTA',
                                    'texto_7' => 'EMPRESA',
                                    'texto_8' => 'DOMICILIO',
                                    'texto_9' => 'TELEFONO',
                                    'texto_10' => 'CELULAR',
                                    'texto_11' => 'EMAIL',
                );		                        
                $this->Temporal->prepareXLSSheet(6,$set); 
                
                $total = count($personas);
                $asinc->setTotal($total);
                $i = 0;	

                foreach($personas as $persona){
                    
                    $sql =  "select 
                                texto_14 as proveedor
                                ,texto_19 as fecha_solicitud
                                ,(SELECT min(vencimiento) FROM orden_descuento_cuotas WHERE 
                                orden_descuento_id = entero_3 and estado NOT IN ('B','D')
                                AND importe > IFNULL((
                                SELECT SUM(cocu.importe) from
                                orden_descuento_cobro_cuotas cocu
                                INNER JOIN orden_descuento_cobros co 
                                ON (co.id = cocu.orden_descuento_cobro_id) 
                                WHERE 
                                cocu.orden_descuento_cuota_id = orden_descuento_cuotas.id),0)) as fecha_mora
                                ,texto_1 as ndoc
                                ,decimal_19 as capital
                                ,texto_2 as solicitante,entero_6 as edad
                                ,texto_3 as prestamo
                                ,decimal_19 as capital
                                ,FX_ORDENDTO_CANT_CUOTAS(entero_3,'".$this->periodo_corte."','PAG') as qpagas
                                -- ,(FX_ORDENDTO_CANT_CUOTAS(entero_3,'".$this->periodo_corte."','PAG') + entero_4 + entero_5) as cuotas
                                ,FX_ORDENDTO_CANT_CUOTAS(entero_3,'".$this->periodo_corte."','TOT') as cuotas
                                ,decimal_1 as impo_pagare
                                ,decimal_2 as cobrado
                                -- ,(FX_ORDENDTO_CANT_CUOTAS(entero_3,'".$this->periodo_corte."','PAG') + entero_4 + entero_5) as cuotas
                                ,FX_ORDENDTO_CANT_CUOTAS(entero_3,'".$this->periodo_corte."','VEN') as qmora
                                ,decimal_3 as mora
                                ,IFNULL((decimal_1 - decimal_2),0) as deuda
                                ,decimal_20 as impocuota
                                ,concat(ifnull(texto_10,''), ' | ',ifnull(texto_8,'')) lugar_trabajo
                                from asincrono_temporales 
                                where asincrono_id = $asinc->id and clave_1 = " . $persona['Socio']['id']."
                                and decimal_3 > 0;";
                    $datos = $oCUOTA->query($sql);
                    
                    $temp = array();
                    
                    if(!empty($datos)){
                        
                        foreach($datos as $dato){
                            
                            $msg = "$i / $total - GENERANDO PLANILLA RESUMEN >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
                            $asinc->actualizar($i,$total,$msg);                        

//                            debug($persona);  
                            
                            $domicilio = $oPERSONA->getDomicilio($persona);
                            
                            $temp['AsincronoTemporal'] = array(
                                            'asincrono_id' => $asinc->id,
                                            'texto_1' => $dato['asincrono_temporales']['proveedor'],
                                            'texto_2' => $dato['asincrono_temporales']['fecha_solicitud'],
                                            'texto_3' => $dato[0]['fecha_mora'],
                                            'texto_4' => $dato['asincrono_temporales']['ndoc'],
                                            'decimal_1' => round($dato['asincrono_temporales']['capital'],2),
                                            'texto_5' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
                                            'entero_1' => $dato['asincrono_temporales']['edad'],
                                            'texto_6' => $dato['asincrono_temporales']['prestamo'],
                                            'decimal_2' => round($dato['asincrono_temporales']['capital'],2),
                                            'entero_2' => $dato[0]['qpagas'],
                                            'entero_3' => $dato[0]['cuotas'],
                                            'decimal_3' => round($dato['asincrono_temporales']['impo_pagare'],2),
                                            'decimal_4' => round($dato['asincrono_temporales']['cobrado'],2),
                                            'entero_4' => $dato[0]['cuotas'],
                                            'entero_5' => $dato[0]['qmora'],
                                            'decimal_5' => round($dato['asincrono_temporales']['mora'],2),
                                            'decimal_6' => round($dato[0]['deuda'],2),
                                            'decimal_7' => round($dato['asincrono_temporales']['impocuota'],2),
                                            'texto_7' => $dato[0]['lugar_trabajo'],
                                            'texto_8' => $domicilio,
                                            'texto_9' => $persona['Persona']['telefono_fijo'],
                                            'texto_10' => $persona['Persona']['telefono_movil'],
                                            'texto_11' => $persona['Persona']['e_mail'],
                            );                        
                            $this->Temporal->writeXLSRow(6,$temp['AsincronoTemporal']);                            
                            
                        }
                        
                        $i++;
                        

                    }
                    
                } // ENDFOREACH PERSONAS                
                
                

		if($STOP == 0){
                        $asinc->actualizar(98,100,"CREANDO ARCHIVO $FILE_EXCEL ...");
                        $this->Temporal->saveToXLSFile($FILE_EXCEL);
                        $asinc->setValue('p6',$FILE_EXCEL);                    
                    
			$asinc->actualizar($i,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}
		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function getPersonas(){
		$personas = null;
//		$sql = "select 
//					Persona.tipo_documento,
//					Persona.documento,
//					Persona.apellido,
//					Persona.nombre,
//					Socio.id
//				from personas as Persona
//				inner join socios as Socio on (Socio.persona_id = Persona.id)
//				ORDER BY Persona.apellido,Persona.nombre";
		
//		$sql = "SELECT 
//				Persona.id,
//                                Persona.tipo_documento,
//				Persona.documento,
//				Persona.apellido,
//				Persona.nombre,
//				Socio.id,
//                Persona.calle,
//                Persona.numero_calle,
//                Persona.piso,
//                Persona.dpto,
//                Persona.barrio,
//                Persona.codigo_postal,
//                Persona.localidad,
//                Persona.telefono_fijo,
//                Persona.telefono_movil,
//                Persona.telefono_referencia,
//                Persona.telefono_movil,
//                Persona.e_mail,
//                Provincia.nombre
//                FROM personas AS Persona
//                INNER JOIN persona_beneficios AS beneficio ON (beneficio.persona_id = Persona.id)
//                INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
//                LEFT JOIN provincias Provincia on (Provincia.id = Persona.provincia_id)
//                INNER JOIN orden_descuentos as orden on (orden.socio_id = Socio.id) 
//                INNER JOIN orden_descuento_cuotas as cuota on (cuota.socio_id = Socio.id 
//                and cuota.orden_descuento_id = orden.id and cuota.proveedor_id = orden.proveedor_id
//                and cuota.persona_beneficio_id = beneficio.id)  
//                WHERE 1=1
//                ".(!empty($this->proveedor_id) ? " AND cuota.proveedor_id = ".$this->proveedor_id : "")."
//                ".(!empty($this->tipo_producto) ? " AND orden.tipo_producto = '".$this->tipo_producto."' " : "")."     
//                ".(!empty($this->tipo_cuota) ? " AND cuota.tipo_cuota = '".$this->tipo_cuota."' " : "")."         
//                ".(!empty($this->codigo_organismo) ? " AND beneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
//                ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) ? " AND beneficio.codigo_empresa = '".$this->codigo_empresa."'" : "")."	
//                ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) && !empty($this->turno_pago) ? " AND beneficio.turno_pago = '".$this->turno_pago."'" : "")."				
//                GROUP BY Socio.id";	
                
		$sql = "SELECT 
				Persona.id,
                                Persona.tipo_documento,
				Persona.documento,
				Persona.apellido,
				Persona.nombre,
				Socio.id,
                Persona.calle,
                Persona.numero_calle,
                Persona.piso,
                Persona.dpto,
                Persona.barrio,
                Persona.codigo_postal,
                Persona.localidad,
                Persona.telefono_fijo,
                Persona.telefono_movil,
                Persona.telefono_referencia,
                Persona.telefono_movil,
                Persona.e_mail,
                Persona.fecha_nacimiento,
                Provincia.nombre
                ,TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, now()) as edad
                FROM personas AS Persona
                INNER JOIN persona_beneficios AS beneficio ON (beneficio.persona_id = Persona.id)
                INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
                LEFT JOIN provincias Provincia on (Provincia.id = Persona.provincia_id)
                WHERE 1=1
                ".(!empty($this->codigo_organismo) ? " AND beneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
                ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) ? " AND beneficio.codigo_empresa = '".$this->codigo_empresa."'" : "")."	
                ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) && !empty($this->turno_pago) ? " AND beneficio.turno_pago = '".$this->turno_pago."'" : "")."				
                GROUP BY Socio.id ORDER BY Persona.apellido, Persona.nombre;";                

		$personas = $this->Persona->query($sql);
		return $personas;
	}
	
	
	function getDeudaSocioDetallada($socio_id){
//		$cuotas = null;
		$sql = "select 
				OrdenDescuentoCuota.id,
				OrdenDescuentoCuota.tipo_cuota,
				OrdenDescuentoCuota.situacion,
				OrdenDescuentoCuota.estado,
				OrdenDescuentoCuota.proveedor_id,
				OrdenDescuentoCuota.nro_referencia_proveedor,
				OrdenDescuentoCuota.orden_descuento_id,
				OrdenDescuentoCuota.persona_beneficio_id,
				OrdenDescuentoCuota.tipo_producto,
				OrdenDescuentoCuota.nro_cuota,
				OrdenDescuentoCuota.importe,
				OrdenDescuentoCuota.periodo,
				Proveedor.razon_social_resumida,
				ifnull((select sum(cc.importe) 
									from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co 
									where 
									cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and cc.orden_descuento_cobro_id = co.id and
									co.periodo_cobro <= '".$this->periodo_corte."'
									group by cc.orden_descuento_cuota_id
									),0) as cobrado,
                ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas 
                                    WHERE orden_descuento_cuota_id = OrdenDescuentoCuota.id
                                    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                    order by liquidacion_id),0) as pendiente_acreditar                                    
				from orden_descuento_cuotas as OrdenDescuentoCuota
				inner join persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
				inner join proveedores as Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
				where 
					OrdenDescuentoCuota.socio_id = $socio_id
					and OrdenDescuentoCuota.periodo <= '".$this->periodo_corte."'
					and OrdenDescuentoCuota.estado NOT IN ('B','D')
					".(!empty($this->codigo_organismo) ? "and PersonaBeneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
					".(!empty($this->proveedor_id) ? "and OrdenDescuentoCuota.proveedor_id = ".$this->proveedor_id : "")."
                                        ".(!empty($this->tipo_producto) ? "and OrdenDescuentoCuota.tipo_producto = '".$this->tipo_producto . "' " : "")."    
                                        ".(!empty($this->tipo_cuota) ? "and OrdenDescuentoCuota.tipo_cuota = '".$this->tipo_cuota . "' " : "")."        
					".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '".$this->codigo_empresa."'" : "")."	
					".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) && !empty($this->turno_pago) ? " AND PersonaBeneficio.turno_pago = '".$this->turno_pago."'" : "")."				
							
				order by PersonaBeneficio.codigo_beneficio,Proveedor.razon_social";
		$cuotas = $this->OrdenDescuentoCuota->query($sql);
		return $cuotas;

	}
	
	function getDeudaSocioConsolidada($socio_id){
//		$cuotas = null;
		$sql = "select
				ifnull(sum(OrdenDescuentoCuota.importe),0) as importe,
				SUM(ifnull((select sum(cc.importe) 
									from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co 
									where 
									cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and cc.orden_descuento_cobro_id = co.id and
									co.periodo_cobro <= '".$this->periodo_corte."'
									group by cc.orden_descuento_cuota_id
									),0)) as pago_acumulado,
				ifnull(sum(OrdenDescuentoCuota.importe) -
				SUM(ifnull((select sum(cc.importe) 
									from orden_descuento_cobro_cuotas cc, orden_descuento_cobros co 
									where 
									cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and cc.orden_descuento_cobro_id = co.id and
									co.periodo_cobro <= '".$this->periodo_corte."'
									group by cc.orden_descuento_cuota_id
									),0)),0) as saldo_actual,
                ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc 
                                        inner join proveedores as Proveedor on (Proveedor.id = lc.proveedor_id)
                                        inner join persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = lc.persona_beneficio_id)                
					WHERE lc.socio_id = OrdenDescuentoCuota.socio_id
					AND lc.orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                        ".(!empty($this->codigo_organismo) ? " and PersonaBeneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
                                        ".(!empty($this->proveedor_id) ? " and lc.proveedor_id = ".$this->proveedor_id : "")." 
                                        ".(!empty($this->tipo_producto) ? "and lc.tipo_producto = '".$this->tipo_producto . "' " : "")."        
                                        ".(!empty($this->tipo_cuota) ? "and lc.tipo_cuota = '".$this->tipo_cuota . "' " : "")."            
                                        ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '".$this->codigo_empresa."'" : "")."    
                                        ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) && !empty($this->turno_pago) ? " AND PersonaBeneficio.turno_pago = '".$this->turno_pago."'" : "")."
					order by liquidacion_id),0) as pendiente_acreditar                    
				from orden_descuento_cuotas as OrdenDescuentoCuota
				inner join persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
				inner join proveedores as Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
				where 
					OrdenDescuentoCuota.socio_id = $socio_id
					and OrdenDescuentoCuota.periodo <= '".$this->periodo_corte."'
					and OrdenDescuentoCuota.estado <> 'B'
					".(!empty($this->codigo_organismo) ? "and PersonaBeneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
					".(!empty($this->proveedor_id) ? "and OrdenDescuentoCuota.proveedor_id = ".$this->proveedor_id : "")."
                                        ".(!empty($this->tipo_producto) ? "and OrdenDescuentoCuota.tipo_producto = '".$this->tipo_producto . "' " : "")."        
                                        ".(!empty($this->tipo_cuota) ? "and OrdenDescuentoCuota.tipo_cuota = '".$this->tipo_cuota . "' " : "")."    
					".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '".$this->codigo_empresa."'" : "")."			
					".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) && !empty($this->turno_pago) ? " AND PersonaBeneficio.turno_pago = '".$this->turno_pago."'" : "");
				
// 		debug($sql);
		$cuotas = $this->OrdenDescuentoCuota->query($sql);
		return $cuotas;

	}

	
	function getDeudaSocioConsolidadaByOrdenDto($socio_id){
//		$cuotas = null;
		$sql = "select 

                            concat(OrdenDescuentoCuota.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_nro,
                            Proveedor.razon_social,
                            Proveedor.razon_social_resumida,
                            TipoProducto.concepto_1,
                            OrdenDescuentoCuota.nro_referencia_proveedor,
                            Organismo.concepto_1,
                            Empresa.concepto_1 as empresa,
                            LiquidacionTurno.descripcion as turno,
                            concat(Empresa.concepto_1,' | ',ifnull(LiquidacionTurno.descripcion,''),' | ',
                            PersonaBeneficio.cbu) as beneficio_str,
                            OrdenDescuento.fecha,
                            OrdenDescuento.periodo_ini,
                            OrdenDescuentoCuota.persona_beneficio_id,
                            OrdenDescuentoCuota.orden_descuento_id
                            ,OrdenDescuento.importe_cuota
                            ,Vendedor.cuit_cuil
                            ,concat(Vendedor.apellido,', ',Vendedor.nombre) as vendedor_apenom
                            ,ifnull(Solicitud.fecha,OrdenDescuento.fecha) as fecha_solicitud
                            ,ifnull(Solicitud.fecha_pago,OrdenDescuento.fecha) as fecha_aprobacion
                            ,Solicitud.user_created
                            ,Solicitud.importe_total
                            ,Solicitud.importe_solicitado
                            ,Solicitud.importe_percibido
                            ,ifnull(Solicitud.importe_cuota,OrdenDescuento.importe_cuota) as solicitud_importe_cuota
                            ,Proveedor.id
                            ,PersonaBeneficio.codigo_beneficio
                            ,PersonaBeneficio.codigo_empresa
                            from orden_descuento_cuotas as OrdenDescuentoCuota
                            inner join orden_descuentos OrdenDescuento on OrdenDescuento.id =  OrdenDescuentoCuota.orden_descuento_id
                            inner join persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                            inner join proveedores as Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
                            inner join global_datos TipoProducto on TipoProducto.id = OrdenDescuentoCuota.tipo_producto
                            inner join global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                            left join global_datos Empresa on Empresa.id = PersonaBeneficio.codigo_empresa
                            left join liquidacion_turnos LiquidacionTurno on LiquidacionTurno.codigo_empresa = PersonaBeneficio.codigo_empresa
                                        and LiquidacionTurno.turno = PersonaBeneficio.turno_pago
                            left join mutual_producto_solicitudes Solicitud on Solicitud.id = OrdenDescuento.numero
                            and (Solicitud.proveedor_id = OrdenDescuento.proveedor_id or Solicitud.reasignar_proveedor_id = OrdenDescuento.proveedor_id)
                            left join vendedores v on v.id = Solicitud.vendedor_id
                            left join personas Vendedor on Vendedor.id = v.persona_id
                            where 
					OrdenDescuentoCuota.socio_id = $socio_id
					and OrdenDescuentoCuota.periodo <= '".$this->periodo_corte."'
					and OrdenDescuentoCuota.estado NOT IN ('B','D')
					".(!empty($this->codigo_organismo) ? "and PersonaBeneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
					".(!empty($this->proveedor_id) ? "and OrdenDescuentoCuota.proveedor_id = ".$this->proveedor_id : "")."
                                        ".(!empty($this->tipo_producto) ? "and OrdenDescuentoCuota.tipo_producto = '".$this->tipo_producto . "' " : "")."    
                                        ".(!empty($this->tipo_cuota) ? "and OrdenDescuentoCuota.tipo_cuota = '".$this->tipo_cuota . "' " : "")."     
					".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '".$this->codigo_empresa."'" : "")."			
					".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) && !empty($this->turno_pago) ? " AND PersonaBeneficio.turno_pago = '".$this->turno_pago."'" : "")."
				GROUP BY OrdenDescuentoCuota.orden_descuento_id	
				order by PersonaBeneficio.codigo_beneficio,Proveedor.razon_social";
		$cuotas = $this->OrdenDescuentoCuota->query($sql);
//                debug($sql);
//		debug($cuotas);
//		exit;
//		if($socio_id == 8827) debug($sql);
		return $cuotas;

	}	
	
        
        function operacionesPorTerminar($socioId){
            
            $sql = "select 
                    o.tipo_orden_dto,
                    o.numero,
                    o.id as orden_descuento,
                    count(c.id) as cuotas,
                    ifnull(
                    (
                    select sum(importe) - ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                    where cc1.orden_descuento_cuota_id = c1.id  ),0)
                    from orden_descuento_cuotas c1 where c1.orden_descuento_id = o.id
                    and c1.periodo < '".$this->periodo_corte."' and c1.estado <> 'B' 
                    and c1.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                    where cc1.orden_descuento_cuota_id = c1.id  ),0)
                    ),0)
                    as mora,
                    cb.concepto_1 as organismo,ba.nombre, so.activo,so.persona_id 
                    ,Vendedor.cuit_cuil
                    ,concat(Vendedor.apellido,', ',Vendedor.nombre) as vendedor_apenom  
                    from orden_descuentos o
                    inner join orden_descuento_cuotas c on (c.orden_descuento_id = o.id)
                    inner join persona_beneficios b on (b.id = o.persona_beneficio_id)
                    inner join global_datos cb on (cb.id = b.codigo_beneficio)
                    inner join bancos ba on (ba.id = b.banco_id)
                    inner join socios so on (so.id = c.socio_id)
                    left join mutual_producto_solicitudes Solicitud on Solicitud.id = o.numero
                    left join vendedores v on v.id = Solicitud.vendedor_id
                    left join personas Vendedor on Vendedor.id = v.persona_id
                    
                    where  o.tipo_orden_dto = 'EXPTE' 
                    and c.estado <> 'B' and c.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc
                    where cc.orden_descuento_cuota_id = c.id),0)
                    and so.activo = 1 and so.id = $socioId
                    ".(!empty($this->codigo_organismo) ? " and b.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
                    ".(!empty($this->proveedor_id) ? " and o.proveedor_id = ".$this->proveedor_id : "")."
                    ".(!empty($this->tipo_producto) ? "and o.tipo_producto = '".$this->tipo_producto . "' " : "")."        
                    ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) ? " AND b.codigo_empresa = '".$this->codigo_empresa."'" : "")."			
                    ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) && !empty($this->turno_pago) ? " AND b.turno_pago = '".$this->turno_pago."'" : "")."
                    group by o.numero,o.id,o.socio_id
                    having count(c.id) <= ".$this->cantidad_cuotas;
            
            App::import('model','mutual.OrdenDescuentoCuota');
            $oCUOTA = new OrdenDescuentoCuota();
            $cuotas = $oCUOTA->query($sql);
//            debug($cuotas);
            return $cuotas;
        }
        
        

        
}
?>
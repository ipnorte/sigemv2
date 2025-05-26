<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * 
 * LANZADOR
 * 	/usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php listado_deuda 2812 -app /home/adrian/Trabajo/www/sigemv2/app/
 * 
 */

class ListadoDeudaShell extends Shell {

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
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();			
		
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
						$cuotas = $this->getDeudaSocioConsolidada($persona['Socio']['id']);
						break;
					case 2:
						//CONSOLIDADO POR ORDEN DE DESCUENTO
						$cuotas = $this->getDeudaSocioConsolidadaByOrdenDto($persona['Socio']['id']);
						break;
					case 3:
						//DETALLE DE CUOTAS ADEUDADAS
						$cuotas = $this->getDeudaSocioDetallada($persona['Socio']['id']);
						break;
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
//							debug($orden);
                                                        $orden = array();
							$orden['pago_acumulado'] = (isset($registro[0]['pago_acumulado']) ? $registro[0]['pago_acumulado'] : 0);
							$orden['saldo_actual'] = (isset($registro[0]['saldo_actual']) ? $registro[0]['saldo_actual'] : 0);
							$orden['cuotas_adeudadas'] = (isset($registro[0]['cuotas_adeudadas']) ? $registro[0]['cuotas_adeudadas'] : 0);
							$orden['cuotas_adeudadas'] = (!empty($orden['cuotas_adeudadas']) ? $orden['cuotas_adeudadas'] : 0);
                                                        
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
                                                        
                                                        $orden['cuotas_avencer'] = $registro[0]['cuotas_avencer'];
                                                        $orden['saldo_avencer'] = $registro[0]['saldo_avencer'];
                                                        $orden['importe_total'] = $registro[0]['importe_total'];
                                                        
                                                        $orden['saldo_avencer_3'] = $registro[0]['saldo_avencer_3'];
                                                        $orden['saldo_avencer_6'] = $registro[0]['saldo_avencer_6'];
                                                        $orden['saldo_avencer_12'] = $registro[0]['saldo_avencer_12'];
                                                        $orden['saldo_avencer_13'] = $registro[0]['saldo_avencer_13'];
                                                        
                                                        
                                                        $orden['vendedor_apenom'] = $registro[0]['vendedor_apenom'];
                                                        $orden['vendedor_cuit'] = $registro['Vendedor']['cuit_cuil'];
                                                        $orden['solicitud_fecha'] = $registro['Solicitud']['fecha'];
                                                        $orden['solicitud_aprobacion'] = $registro['Solicitud']['fecha_pago'];
                                                        
							$msg = "$i / $total - PROCESANDO >> " . $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'];
							$msg .= " | " . $orden['tipo_nro'] . " - " . $orden['proveedor_producto'];
							
//							debug($orden);
							
							$asinc->actualizar($i,$total,$msg);
							
//							if(($orden['saldo_actual'] + $orden['saldo_avencer']) != 0):
							
							
								$temp['AsincronoTemporal'] = array(
										'asincrono_id' => $asinc->id,
                                                                                'clave_1' => $persona['Socio']['id'],
                                                                                'clave_2' => $orden['id'],
										'texto_1' => $persona['Persona']['documento'],
										'texto_2' => $persona['Persona']['apellido'] . ", " . $persona['Persona']['nombre'],
										'texto_3' => $orden['tipo_nro'],
										'texto_4' => $orden['proveedor_producto'],
										'texto_5' => $this->OrdenDescuento->periodo($orden['periodo_ini']),
										'texto_6' => $orden['nro_referencia_proveedor'],
										'texto_7' => $orden['cuotas_adeudadas'],
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
										'decimal_1' => round($orden['importe_total'],2),
										'decimal_2' => round($orden['pago_acumulado'],2),
										'decimal_3' => round($orden['saldo_actual'],2),
                                                                                'decimal_4' => round($registro[0]['pendiente_acreditar'],2),
                                                                                'decimal_5' => round($orden['saldo_actual'] - $registro[0]['pendiente_acreditar'],2),  
                                                                                'entero_4' => $orden['cuotas_avencer'],
                                                                                'decimal_6' => round($orden['saldo_avencer'],2),
                                                                                'decimal_7' => round($orden['saldo_avencer_3'],2),
                                                                                'decimal_8' => round($orden['saldo_avencer_6'],2),
                                                                                'decimal_9' => round($orden['saldo_avencer_12'],2),
                                                                                'decimal_10' => round($orden['saldo_avencer_13'],2),
										'entero_1' => $persona['Socio']['id'],
										'entero_2' => $orden['persona_beneficio_id'],
										'entero_3' => $orden['id'],
										
								);	
//							debug($temp);
							
//							endif;
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
						
						debug($temp);
							
						if($asinc->detenido()){
							$STOP = 1;
							break;
						}				
						
						
						if(!empty($temp)):
						
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
		
		

		if($STOP == 0){
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
		
		$sql = "SELECT 
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
                Provincia.nombre
                FROM personas AS Persona
                INNER JOIN persona_beneficios AS beneficio ON (beneficio.persona_id = Persona.id)
                INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
                LEFT JOIN provincias Provincia on (Provincia.id = Persona.provincia_id)
                INNER JOIN orden_descuentos as orden on (orden.socio_id = Socio.id) 
                INNER JOIN orden_descuento_cuotas as cuota on (cuota.socio_id = Socio.id 
                and cuota.orden_descuento_id = orden.id and cuota.proveedor_id = orden.proveedor_id
                and cuota.persona_beneficio_id = beneficio.id)  
                WHERE 1=1
                ".(!empty($this->proveedor_id) ? " AND cuota.proveedor_id = ".$this->proveedor_id : "")."
                ".(!empty($this->tipo_producto) ? " AND orden.tipo_producto = '".$this->tipo_producto."' " : "")."     
                ".(!empty($this->tipo_cuota) ? " AND cuota.tipo_cuota = '".$this->tipo_cuota."' " : "")."         
                ".(!empty($this->codigo_organismo) ? " AND beneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
                ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) ? " AND beneficio.codigo_empresa = '".$this->codigo_empresa."'" : "")."	
                ".(!empty($this->codigo_organismo) && !empty($this->codigo_empresa) && !empty($this->turno_pago) ? " AND beneficio.turno_pago = '".$this->turno_pago."'" : "")."				
                GROUP BY Socio.id";		

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
                            OrdenDescuento.periodo_ini,
                            OrdenDescuentoCuota.persona_beneficio_id,

				OrdenDescuentoCuota.orden_descuento_id,

                            ifnull((SELECT IFNULL(COUNT(*),0) FROM orden_descuento_cuotas cu
                            WHERE cu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                            AND cu.periodo > '".$this->periodo_corte."' and cu.estado NOT IN ('B','D')
                            AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                            INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                            WHERE 
                            cocu.orden_descuento_cuota_id = cu.id
                            AND co.periodo_cobro <= '".$this->periodo_corte."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                            inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$this->periodo_corte."'
                            WHERE orden_descuento_cuota_id = cu.id
                            AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                            order by liquidacion_id desc limit 1 ),0))
                            GROUP BY cu.orden_descuento_id),0) as cuotas_avencer, 

                        ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                        WHERE cu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                            AND cu.periodo > '".$this->periodo_corte."' and cu.estado NOT IN ('B','D')
                            AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                            INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                            WHERE 
                            cocu.orden_descuento_cuota_id = cu.id
                            AND co.periodo_cobro <= '".$this->periodo_corte."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                                inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$this->periodo_corte."'
                            WHERE orden_descuento_cuota_id = cu.id
                            AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                            order by liquidacion_id desc limit 1 ),0))
                            GROUP BY cu.orden_descuento_id),0) as saldo_avencer,
                            
                            ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                            WHERE cu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                                AND cu.periodo > '".$this->periodo_corte."' 
                            and cu.periodo <= date_format(date_add(STR_TO_DATE('".$this->periodo_corte."01','%Y%m%d'), interval 3 month),'%Y%m')
                            and cu.estado NOT IN ('B','D','C')
                                AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                                INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                                WHERE 
                                cocu.orden_descuento_cuota_id = cu.id 
                                AND co.periodo_cobro <= '".$this->periodo_corte."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                                    inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$this->periodo_corte."'
                                WHERE orden_descuento_cuota_id = cu.id
                                AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                order by liquidacion_id desc limit 1 ),0))
                                GROUP BY cu.orden_descuento_id),0) as saldo_avencer_3,    

                            ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                            WHERE cu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                                AND cu.periodo > date_format(date_add(STR_TO_DATE('".$this->periodo_corte."01','%Y%m%d'), interval 3 month),'%Y%m') 
                            and cu.periodo <= date_format(date_add(date_add(STR_TO_DATE('".$this->periodo_corte."01','%Y%m%d'), interval 3 month),interval 6 month),'%Y%m')
                            and cu.estado NOT IN ('B','D','C')
                                AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                                INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                                WHERE 
                                cocu.orden_descuento_cuota_id = cu.id 
                                AND co.periodo_cobro <= '".$this->periodo_corte."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                                    inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$this->periodo_corte."'
                                WHERE orden_descuento_cuota_id = cu.id
                                AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                order by liquidacion_id desc limit 1 ),0))
                                GROUP BY cu.orden_descuento_id),0) as saldo_avencer_6, 

                            ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                            WHERE cu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                                AND cu.periodo > date_format(date_add(date_add(STR_TO_DATE('".$this->periodo_corte."01','%Y%m%d'), interval 3 month),interval 6 month),'%Y%m') 
                            and cu.periodo <= date_format(date_add(date_format(date_add(date_add(STR_TO_DATE('".$this->periodo_corte."01','%Y%m%d'), interval 3 month),interval 6 month),'%Y%m%d'),interval 6 month),'%Y%m')
                            and cu.estado NOT IN ('B','D','C')
                                AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                                INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                                WHERE 
                                cocu.orden_descuento_cuota_id = cu.id 
                                AND co.periodo_cobro <= '".$this->periodo_corte."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                                    inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$this->periodo_corte."'
                                WHERE orden_descuento_cuota_id = cu.id
                                AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                order by liquidacion_id desc limit 1 ),0))
                                GROUP BY cu.orden_descuento_id),0) as saldo_avencer_12,     

                            ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                            WHERE cu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                            and cu.periodo > date_format(date_add(date_format(date_add(date_add(STR_TO_DATE('".$this->periodo_corte."01','%Y%m%d'), interval 3 month),interval 6 month),'%Y%m%d'),interval 6 month),'%Y%m')
                            and cu.estado NOT IN ('B','D','C')
                                AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                                INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                                WHERE 
                                cocu.orden_descuento_cuota_id = cu.id 
                                AND co.periodo_cobro <= '".$this->periodo_corte."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                                    inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$this->periodo_corte."'
                                WHERE orden_descuento_cuota_id = cu.id
                                AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                order by liquidacion_id desc limit 1 ),0))
                                GROUP BY cu.orden_descuento_id),0) as saldo_avencer_13, 



                            ifnull((SELECT IFNULL(COUNT(*),0) FROM orden_descuento_cuotas cu
                            WHERE cu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                            AND cu.periodo <= '".$this->periodo_corte."' and cu.estado NOT IN ('B','D')
                            AND cu.importe > ((SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
                                                                    INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
                                                                    WHERE 
                                                                    cocu.orden_descuento_cuota_id = cu.id
                                                                    AND co.periodo_cobro <= '".$this->periodo_corte."') + ifnull((SELECT ROUND(ifnull(SUM(importe_debitado),0),2) AS importe_debitado FROM liquidacion_cuotas lc
                                                                        inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$this->periodo_corte."'
                                                    WHERE orden_descuento_cuota_id = cu.id
                                                    AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
                                                    order by liquidacion_id desc limit 1 ),0))
                            GROUP BY cu.orden_descuento_id),0) as cuotas_adeudadas,

                            ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                            WHERE cu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                            and cu.estado NOT IN ('B','D')),0) as importe_total,


                                
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
                inner join liquidaciones l on l.id = lc.liquidacion_id and l.periodo <= '".$this->periodo_corte."'
                
					WHERE orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
					AND orden_descuento_cobro_id = 0 and mutual_adicional_pendiente_id = 0
					order by liquidacion_id),0) as pendiente_acreditar 
                                        
                                ,Vendedor.cuit_cuil
                                ,concat(Vendedor.apellido,', ',Vendedor.nombre) as vendedor_apenom
                                ,Solicitud.fecha
                                ,Solicitud.fecha_pago

				from orden_descuento_cuotas as OrdenDescuentoCuota
                                inner join orden_descuentos OrdenDescuento on OrdenDescuento.id =  OrdenDescuentoCuota.orden_descuento_id
				inner join persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
				inner join proveedores as Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
                                inner join global_datos TipoProducto on TipoProducto.id = OrdenDescuentoCuota.tipo_producto
                                inner join global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                                left join global_datos Empresa on Empresa.id = PersonaBeneficio.codigo_empresa
                                left join liquidacion_turnos LiquidacionTurno on LiquidacionTurno.codigo_empresa = PersonaBeneficio.codigo_empresa
                                and LiquidacionTurno.turno = PersonaBeneficio.turno_pago
                                
                                left join mutual_producto_solicitudes Solicitud on Solicitud.id = OrdenDescuento.numero
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
		
		
//			$sql = "SELECT 
//					orden.orden_descuento_id,
//					orden.proveedor_id,
//					orden.persona_beneficio_id,
//					beneficio.codigo_beneficio,
//					beneficio.codigo_empresa,
//					beneficio.turno_pago,
//					FX_TOTALES_ORDEN(orden_descuento_id,'".$this->periodo_corte."',proveedor_id,persona_beneficio_id,'TOTAL_DEVENGADO') AS TOTAL_DEVENGADO,
//					FX_TOTALES_ORDEN(orden_descuento_id,'".$this->periodo_corte."',proveedor_id,persona_beneficio_id,'SALDO_AVENCER') AS SALDO_AVENCER,
//					FX_TOTALES_ORDEN(orden_descuento_id,'".$this->periodo_corte."',proveedor_id,persona_beneficio_id,'TOTAL_PAGADO') AS TOTAL_PAGADO,
//					FX_TOTALES_ORDEN(orden_descuento_id,'".$this->periodo_corte."',proveedor_id,persona_beneficio_id,'SALDO_VENCIDO') AS SALDO_VENCIDO,
//					FX_TOTALES_ORDEN(orden_descuento_id,'".$this->periodo_corte."',proveedor_id,persona_beneficio_id,'CUOTAS_DEVENGADAS') AS CUOTAS_DEVENGADAS,
//					FX_TOTALES_ORDEN(orden_descuento_id,'".$this->periodo_corte."',proveedor_id,persona_beneficio_id,'CUOTAS_VENCIDAS') AS CUOTAS_VENCIDAS,
//					FX_TOTALES_ORDEN(orden_descuento_id,'".$this->periodo_corte."',proveedor_id,persona_beneficio_id,'CUOTAS_AVENCER') AS CUOTAS_AVENCER 
//					FROM orden_descuento_cuotas AS orden 
//					INNER JOIN persona_beneficios AS beneficio ON (beneficio.id = orden.persona_beneficio_id)
//					WHERE orden.socio_id = $socio_id
//					".(!empty($this->proveedor_id) ? " AND orden.proveedor_id = ".$this->proveedor_id : "")."
//					".(!empty($this->codigo_organismo) ? " AND beneficio.codigo_beneficio = '".$this->codigo_organismo."'" : "")."
//					GROUP BY orden.orden_descuento_id,orden.proveedor_id,orden.persona_beneficio_id;";		
		
		
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
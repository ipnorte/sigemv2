<?php

/**
 * REPORTE DE CONTROL DE LA LIQUIDACION
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_control_liquidacion 205 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php reporte_control_liquidacion 37967 -app /home/adrian/trabajo/www/sigem/app/
 * 
 * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php reporte_control_liquidacion 46625 -app /home/mutualam/public_html/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ReporteControlLiquidacion3Shell extends Shell {

    
    
	var $uses = array(
						'Mutual.Liquidacion',
						'Mutual.LiquidacionCuotaNoimputada',
						'Proveedores.Proveedor',
						'Mutual.OrdenDescuentoCuota',
						'Mutual.OrdenDescuento',
						'Mutual.LiquidacionSocio',
						'Proveedores.ProveedorComision',
						'Proveedores.Proveedor',
	);
	var $liquidacion_id;
	var $liquidacion;
	
	var $tasks = array('Temporal');
	
	function main() {
		$STOP = 0;
        Configure::write('debug',1);
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$this->Temporal->pid = $pid;

		$this->liquidacion	= $this->Liquidacion->cargar($asinc->getParametro('p1'));
		
		$imputada = ($asinc->getParametro('p2') == 'CALCULA_IMPUTADO' ? 1 : 0);
		
		$preImputado = $asinc->getParametro('p3');
		$preImputado = (!empty($preImputado) ? $preImputado : 0);

		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO LIQUIDACION...");
		
		//limpio la tabla temporal
//		if(!$this->Temporal->limpiarTabla($asinc->id)){
//			$asinc->fin("SE PRODUJO UN ERROR...");
//			return;
//		}		
		
	
		$proveedores = $this->__cargarProveedores();
		
//		debug($proveedores);
		
		$TOTAL_PROVEEDOR = 0;
		$ATRASO_PROVEEDOR = 0;
		$PERIODO_PROVEEDOR = 0;
		
		if(empty($proveedores)){
			$asinc->actualizar(0,100,"NO EXISTEN LIQUIDACIONES PARA PROCESAR");
			return;			
		}
		
		$total = count($proveedores);
		$asinc->setTotal($total);
		$i = 0;			
			
		$temp = array();
		$acu = 0;
		$acu2 = 0;
		
		//recorro los proveedores
		$this->out("");
		$this->out("");
		$this->out("");
		
        
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $CALC_IVA = false;
        
        if(isset($INI_FILE['general']['discrimina_iva']) && $INI_FILE['general']['discrimina_iva'] != 0){
            $CALC_IVA = $INI_FILE['general']['discrimina_iva'];
        } 
        $CARGO_SOCIO=0;
        if(isset($INI_FILE['general']['cargo_por_socio']) && $INI_FILE['general']['cargo_por_socio'] != 0){
            $CARGO_SOCIO = $INI_FILE['general']['cargo_por_socio'];
        }		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCCUOTA = new OrdenDescuentoCobroCuota();
		
		foreach($proveedores as $idx => $proveedor){
			$acu = 0;
			$TOTAL_PROVEEDOR = 0;
			$ATRASO_PROVEEDOR = 0;
			$PERIODO_PROVEEDOR = 0;			
//			$condiciones['LiquidacionCuota.proveedor_id'] = $proveedor['Proveedor']['id'];
			
//			$conceptos = $this->LiquidacionCuota->find('all',array(
//												'conditions' => array(
//																	'LiquidacionCuotaNoimputada.liquidacion_id' => $this->liquidacion['Liquidacion']['id'],
//																	'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor['Proveedor']['id']
//												),
//												'fields' => array('LiquidacionCuotaNoimputada.tipo_producto,LiquidacionCuotaNoimputada.tipo_cuota'),
//												'group' => array('LiquidacionCuotaNoimputada.tipo_producto,LiquidacionCuotaNoimputada.tipo_cuota'),
//												'order' => array('LiquidacionCuotaNoimputada.tipo_producto,LiquidacionCuotaNoimputada.tipo_cuota')
//			));
			
			$productos = $this->__cargarProductosByProveedor($proveedor['Proveedor']['id']);
			
			$intercambio = $this->Proveedor->read('intercambio',$proveedor['Proveedor']['id']);
			$intercambio = (!empty($intercambio['Proveedor']['intercambio']) ? $intercambio['Proveedor']['intercambio'] : 0);
			
			//recorro los productos por proveedor
			foreach($productos as $producto){
				
				$cuotas = $this->__cargarCuotasByProductoByProveedor($proveedor['Proveedor']['id'],$producto['tipo_producto']);

				$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $proveedor['Proveedor']['razon_social_resumida'] ." - ".$producto['tipo_producto_desc']);				
				
//				$this->Temporal->setErrorMsg("PASO POR ACA $i",$proveedor['Proveedor']['razon_social_resumida']);
				
				//recorro las cuotas por producto y por proveedor
				foreach($cuotas as $cuota){
					
					//saco la deuda del periodo y el atraso por cuota, producto, proveedor
					$conditions = array();
					$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $this->liquidacion['Liquidacion']['id'];
					$conditions['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor['Proveedor']['id'];
					$conditions['LiquidacionCuotaNoimputada.tipo_producto'] = $producto['tipo_producto'];
					$conditions['LiquidacionCuotaNoimputada.tipo_cuota'] = $cuota['tipo_cuota'];
					$conditions['LiquidacionCuotaNoimputada.periodo_cuota'] = $this->liquidacion['Liquidacion']['periodo'];
					
					
					$totalPeriodo = $this->LiquidacionCuotaNoimputada->find('all',array(
													'conditions' => $conditions,
													'fields' => array('ifnull(sum(saldo_actual),0) as saldo_actual,ifnull(sum(importe),0) as importe'),
					));
					
					//saco el atraso
					$conditions = array();
					$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $this->liquidacion['Liquidacion']['id'];
					$conditions['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor['Proveedor']['id'];
					$conditions['LiquidacionCuotaNoimputada.tipo_producto'] = $producto['tipo_producto'];
					$conditions['LiquidacionCuotaNoimputada.tipo_cuota'] = $cuota['tipo_cuota'];
					$conditions['LiquidacionCuotaNoimputada.periodo_cuota <'] = $this->liquidacion['Liquidacion']['periodo'];
					
					$totalAtraso = $this->LiquidacionCuotaNoimputada->find('all',array(
													'conditions' => $conditions,
													'fields' => array('ifnull(sum(saldo_actual),0) as saldo_actual,ifnull(sum(importe),0) as importe'),
					));					
					
					//calculo lo imputado
					$totalImputaPeriodo = 0;
					$totalImputaAtraso = 0;
					$totalImputa = 0;
					$porcenajeComision = 0;
					$comision = 0;
					$totalReversado = 0;
					$totalComision = 0;
					$comision_IVA = 0;
                    
					if($imputada == 1):

						//saco la deuda del periodo y el atraso por cuota, producto, proveedor
						$conditions = array();
						$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $this->liquidacion['Liquidacion']['id'];
						$conditions['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor['Proveedor']['id'];
						$conditions['LiquidacionCuotaNoimputada.tipo_producto'] = $producto['tipo_producto'];
						$conditions['LiquidacionCuotaNoimputada.tipo_cuota'] = $cuota['tipo_cuota'];
						
						if($preImputado == 0)$conditions['LiquidacionCuotaNoimputada.imputada'] = 1;
						else $conditions['LiquidacionCuotaNoimputada.para_imputar'] = 1;
						
						$conditions['LiquidacionCuotaNoimputada.periodo_cuota'] = $this->liquidacion['Liquidacion']['periodo'];
						
						$fields = array('ifnull(sum(importe_debitado),0) as imputado');
						
						
						//vinculo la cuota a la comision del proveedor
						$totalImputaPeriodo = $this->LiquidacionCuotaNoimputada->find('all',array(
														'conditions' => $conditions,
														'fields' => $fields,
						));

						//saco el atraso
						$conditions = array();
						$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $this->liquidacion['Liquidacion']['id'];
						$conditions['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor['Proveedor']['id'];
						$conditions['LiquidacionCuotaNoimputada.tipo_producto'] = $producto['tipo_producto'];
						$conditions['LiquidacionCuotaNoimputada.tipo_cuota'] = $cuota['tipo_cuota'];
						
						if($preImputado == 0)$conditions['LiquidacionCuotaNoimputada.imputada'] = 1;
						else $conditions['LiquidacionCuotaNoimputada.para_imputar'] = 1;
						
						$conditions['LiquidacionCuotaNoimputada.periodo_cuota <'] = $this->liquidacion['Liquidacion']['periodo'];
						
						$totalImputaAtraso = $this->LiquidacionCuotaNoimputada->find('all',array(
														'conditions' => $conditions,
														'fields' => $fields,
						));					

						$totalImputaPeriodo = (isset($totalImputaPeriodo[0][0]['imputado']) ? $totalImputaPeriodo[0][0]['imputado'] : 0);
						$totalImputaAtraso = (isset($totalImputaAtraso[0][0]['imputado']) ? $totalImputaAtraso[0][0]['imputado'] : 0);
						
						$totalImputaPeriodo = round($totalImputaPeriodo,2);
						$totalImputaAtraso = round($totalImputaAtraso,2);
						
						$totalImputa = $totalImputaPeriodo + $totalImputaAtraso;
						$totalImputa = round($totalImputa,2);

//						$totalReversado = $oCCUOTA->getTotalReversoByProveedorByLiquidacion($proveedor['Proveedor']['id'],$this->liquidacion['Liquidacion']['id'],0,0);
						
						$totalReversado = 0;
						
						//saco la comision para el proveedor, producto y cuota
						$conditions = array();
						$conditions['LiquidacionCuotaNoimputada.liquidacion_id'] = $this->liquidacion['Liquidacion']['id'];
						$conditions['LiquidacionCuotaNoimputada.proveedor_id'] = $proveedor['Proveedor']['id'];
						$conditions['LiquidacionCuotaNoimputada.tipo_producto'] = $producto['tipo_producto'];
						$conditions['LiquidacionCuotaNoimputada.tipo_cuota'] = $cuota['tipo_cuota'];
						
						if($preImputado == 0)$conditions['LiquidacionCuotaNoimputada.imputada'] = 1;
						else $conditions['LiquidacionCuotaNoimputada.para_imputar'] = 1;
						
						
//                                                $fields = array('ifnull(sum(comision_cobranza),0) as comision_cobranza');
						$fields = array('ifnull(sum(comision_cobranza),0) as comision_cobranza, max(ifnull(alicuota_comision_cobranza,0)) as alicuota_comision_cobranza');
                                                
//                                                debug($fields);
                                                
						$totalComision = $this->LiquidacionCuotaNoimputada->find('all',array(
														'conditions' => $conditions,
														'fields' => $fields,
						));						
						$totalComision = (isset($totalComision[0][0]['comision_cobranza']) ? $totalComision[0][0]['comision_cobranza'] : 0);
//                                                $porcenajeComision = 0;
                                                $porcenajeComision = (isset($infoComision[0][0]['alicuota_comision_cobranza']) ? $infoComision[0][0]['alicuota_comision_cobranza'] : 0);
						
//                                                debug($infoComision);
                                                
						if(empty($porcenajeComision)){
                                                    $porcenajeComision = $this->ProveedorComision->getComision($this->liquidacion['Liquidacion']['codigo_organismo'],$proveedor['Proveedor']['id'],$producto['tipo_producto'],$cuota['tipo_cuota']);
                                                }
						
						
						if($totalComision == 0)$comision = $totalImputa * $porcenajeComision / 100;
						else $comision = $totalComision;
						
//						$this->out($totalReversado);

					endif;
					
					
					
					$totalLiquiPeriodoSA = (isset($totalPeriodo[0][0]['saldo_actual']) ? $totalPeriodo[0][0]['saldo_actual'] : 0);
					$totalLiquiAtrasoSA = (isset($totalAtraso[0][0]['saldo_actual']) ? $totalAtraso[0][0]['saldo_actual'] : 0);
					$totalLiquiSA = $totalLiquiPeriodoSA + $totalLiquiAtrasoSA;
					
					$totalLiquiPeriodo = (isset($totalPeriodo[0][0]['importe']) ? $totalPeriodo[0][0]['importe'] : 0);
					$totalLiquiAtraso = (isset($totalAtraso[0][0]['importe']) ? $totalAtraso[0][0]['importe'] : 0);
					$totalLiqui = $totalLiquiPeriodo + $totalLiquiAtraso;
					
					if($CALC_IVA != 0){
                        $comision_IVA = $comision * ($CALC_IVA/100);
                        $comision_IVA = round($comision_IVA,2);					
                    }
					
//					$acu += $totalLiqui1;
//					$acu2 += $totalLiqui1;
//					$this->out( str_pad($proveedor['Proveedor']['id']." ".$proveedor['Proveedor']['razon_social_resumida'],25,' ', STR_PAD_RIGHT)."\t\t" . $producto['tipo_producto'] . "\t\t".$cuota['tipo_cuota']."\t\t".str_pad(number_format($totalLiqui1,2),12," ",STR_PAD_LEFT)."\t\t".str_pad(number_format($acu,2),12," ",STR_PAD_LEFT));					
					
					
//					$acu += $totalLiqui1;
//					$this->out("\tTOTAL : $totalLiqui1 -----> acumulado: $acu");
                    
                    ###########################################################################################
                    # CALCULAR LA CANTIDAD DE SOCIOS COBRADOS POR PROVEEDOR / PRODUCTO / CUOTA
                    ###########################################################################################
                    $cantidadCobrados = $this->__getCantidadSociosCobrados($proveedor['Proveedor']['id'],$producto['tipo_producto'],$cuota['tipo_cuota']);
					
					#GRABO LA TABLA TEMPORAL
					$temp['AsincronoTemporal'] = array(
							'id' => 0,				
							'asincrono_id' => $asinc->id,
							'clave_1' => $proveedor['Proveedor']['razon_social_resumida'],
							'clave_2' => $producto['tipo_producto'],	
							'clave_3' => 'REPORTE_1',						
							'texto_1' => $proveedor['Proveedor']['razon_social_resumida'],
							'texto_2' => $producto['tipo_producto'],
							'texto_3' => $cuota['tipo_cuota'],
							'texto_4' => $producto['tipo_producto_desc'],
							'texto_5' => $cuota['tipo_cuota_desc'],
							'texto_12' => 'REPORTE_1',
							'decimal_1' => round($totalLiquiPeriodo,2),
							'decimal_2' => round($totalLiquiAtraso,2),
							'decimal_3' => round($totalLiqui,2),
							'decimal_4' => round($totalImputaPeriodo,2),
							'decimal_5' => round($totalImputaAtraso,2),
							'decimal_6' => round($totalImputa,2),
							'decimal_7' => round($totalLiquiPeriodoSA,2),
							'decimal_8' => round($totalLiquiAtrasoSA,2),
							'decimal_9' => round($totalLiquiSA,2),
							'decimal_10' => $porcenajeComision,
							'decimal_11' => round($comision,2),
							'decimal_12' => round($totalImputa - $totalReversado - $comision - $comision_IVA,2),
							'decimal_13' => round($totalLiquiSA - $totalImputa,2),
							'decimal_14' => 0,//round($totalReversado,2),	
                            'decimal_15' => round($comision_IVA,2),
							'entero_1' => $proveedor['Proveedor']['id'],
							'entero_2' => $intercambio,
                            'entero_3' => $cantidadCobrados,
					);
					

					
					
//					debug($temp);
					
					if($asinc->detenido()){
						$STOP = 1;
						break;
					}				

					if(!$this->Temporal->grabar($temp)){
						$STOP = 1;
						break;
					}
					
				
					
					#CARGO LOS SOCIOS QUE SE LIQUIDARON
					$socios = array();
					
					if($imputada == 0) $socios = $this->__cargarSociosByProveedorByProductoByCuota($proveedor['Proveedor']['id'],$producto['tipo_producto'],$cuota['tipo_cuota']);
					
					if(!empty($socios)):
					
						foreach($socios as $socio){
						
// 							debug($socio);
							
// 							$this->out("\t".$socio['Persona']['apellido'].", ".$socio['Persona']['nombre']."\tPERIODO: ".$socio['LiquidacionCuotaNoimputada']['periodo_cuota']." | CUOTA:".$socio['OrdenDescuentoCuota']['nro_cuota']."/".$socio['OrdenDescuento']['cuotas']."\tTOTAL: ".$socio['LiquidacionCuotaNoimputada']['saldo_actual']."\n");
							
// 							$txt_1 = $socio['GlobalDato']['concepto_1'] . " " . $socio['Persona']['documento'] ." - ". $socio['Persona']['apellido'].", ".$socio['Persona']['nombre'];

							$asinc->actualizar($i,$total,"$i / $total - ".$proveedor['Proveedor']['razon_social_resumida']." - ".$producto['tipo_producto_desc']." >> " . $socio[0]['apenom']);
							
							
							$temp['AsincronoTemporal'] = array(
									'id' => 0,
									'asincrono_id' => $asinc->id,
									'clave_1' => $proveedor['Proveedor']['razon_social_resumida'],
									'clave_2' => $producto['tipo_producto'],	
									'clave_3' => 'REPORTE_2',
									'texto_1' => $socio['Global1']['concepto_1'],
									'texto_2' => $socio['Persona']['documento'],
									'texto_3' => $socio[0]['apenom'],
									'decimal_1' => $socio[0]['importe'],
									'decimal_2' => $socio[0]['saldo_actual'],
									'entero_1' => $proveedor['Proveedor']['id']
							);
							
							if($asinc->detenido()){
								$STOP = 1;
								break;
							}				
		
							if(!$this->Temporal->grabar($temp)){
								$STOP = 1;
								break;
							}						
						}//fin socios
					endif;
					
					
				}//fin cuotas
				
				#GENERO EL REPORTE DE ALTAS
				if($imputada == 0) $ordenes = $this->__cargarAltasByProveedorByProducto($proveedor['Proveedor']['id'],$producto['tipo_producto']);
				else $ordenes = null;
				
				if(!empty($ordenes)):
				
					foreach($ordenes as $orden):
					
						$temp['AsincronoTemporal'] = array(
								'id' => 0,
								'asincrono_id' => $asinc->id,
								'clave_1' => $proveedor['Proveedor']['razon_social_resumida'],
								'clave_2' => $producto['tipo_producto'],
								'clave_3' => 'REPORTE_3',
								'texto_1' => $proveedor['Proveedor']['razon_social_resumida'],
								'texto_2' => $producto['tipo_producto'],
								'texto_3' => $this->OrdenDescuento->GlobalDato('concepto_1',$producto['tipo_producto']),
								'decimal_2' => round($orden[0]['importe_cuota'],2),
								'entero_1' => $proveedor['Proveedor']['id'],
								'entero_2' => round($orden[0]['cantidad'],2),
						);
						
						if($asinc->detenido()){
							$STOP = 1;
							break;
						}				
	
						if(!$this->Temporal->grabar($temp)){
							$STOP = 1;
							break;
						}						
						
						//CARGO LOS SOCIOS DADOS DE ALTA
						$socios = array();
						
						if($imputada == 0) $socios = $this->__cargarSociosByProveedorByProductoByCuota($proveedor['Proveedor']['id'],$producto['tipo_producto'],null,$this->liquidacion['Liquidacion']['periodo']);
						
						if(!empty($socios)):
						
							foreach($socios as $socio){
							
								$asinc->actualizar($i,$total,"$i / $total - ".$proveedor['Proveedor']['razon_social_resumida']." - ".$producto['tipo_producto_desc']." >> " . $socio[0]['apenom']);
									
									
								$temp['AsincronoTemporal'] = array(
										'id' => 0,
										'asincrono_id' => $asinc->id,
										'clave_1' => $proveedor['Proveedor']['razon_social_resumida'],
										'clave_2' => $producto['tipo_producto'],
										'clave_3' => 'REPORTE_4',
										'texto_1' => $socio['Global1']['concepto_1'],
										'texto_2' => $socio['Persona']['documento'],
										'texto_3' => $socio[0]['apenom'],
										'decimal_1' => $socio[0]['importe'],
										'decimal_2' => $socio[0]['saldo_actual'],
										'entero_1' => $proveedor['Proveedor']['id']
								);
									
								if($asinc->detenido()){
									$STOP = 1;
									break;
								}
								
								if(!$this->Temporal->grabar($temp)){
									$STOP = 1;
									break;
								}							
								
							}
							
						endif;	
						
						
					endforeach;
				
				endif;
				
			}//fin productos
			$i++;
//			$this->out("-------------------------------------------------------------------------------------------------------------------------------------------------");
			
			$totalReversado = $oCCUOTA->getTotalReversoByProveedorByLiquidacion($proveedor['Proveedor']['id'],$this->liquidacion['Liquidacion']['id'],0,0);

			//grabar el renglon de reversos
			if($totalReversado != 0):
			
				$comisionesReversadas = $oCCUOTA->getTotalComisionAplicadaReverso($proveedor['Proveedor']['id'],$this->liquidacion['Liquidacion']['id']);
				
				//GABRIEL: 12/05/2011 no devolver las comisiones cobradas por cuotas reversadas posteriormente
//				$comisionesReversadas = 0;
			
				$temp['AsincronoTemporal'] = array(
						'asincrono_id' => $asinc->id,
						'clave_3' => 'REPORTE_1',
						'texto_1' => $proveedor['Proveedor']['razon_social_resumida'],
						'texto_2' => "REVERSADO",
						'texto_3' => "REVERSADO",
						'texto_4' => "REVERSADO",
						'texto_5' => "TOTAL A DESCONTAR",
						'texto_12' => 'REPORTE_1',
						'decimal_1' => 0,
						'decimal_2' => 0,
						'decimal_3' => round($totalReversado,2),
						'decimal_4' => 0,
						'decimal_5' => 0,
						'decimal_6' => 0,
						'decimal_7' => 0,
						'decimal_8' => 0,
						'decimal_9' => 0,
						'decimal_10' => 0,
						'decimal_11' => round($comisionesReversadas,2),
						'decimal_12' => round(($totalReversado - $comisionesReversadas)* -1,2),
						'decimal_13' => 0,
						'decimal_14' => round($totalReversado,2),	
						'entero_1' => $proveedor['Proveedor']['id'],
						'entero_2' => $intercambio,
				);

				if(!$this->Temporal->grabar($temp)){
					$STOP = 1;
					break;
				}						
				
			endif;				
			
				
		}//fin proveedores

		//CARGO EL LISTADO DE ORDENES DE DEBITO
		$socios = null;
		$asinc->actualizar(5,100,"LISTADO DE ORDENES DE DEBITO...");
		if($imputada == 0) $socios = $this->__cargarOrdenDebitos();
		
		if(!empty($socios)):
			$i = 0;
			$total = count($socios);
			foreach($socios as $socio){
				$asinc->actualizar($i,$total,"$i / $total - "." LISTANDO ORDENES DE DEBITO >> " . $socio['LiquidacionSocio']['apenom']);
					
					
				$temp['AsincronoTemporal'] = array(
						'id' => 0,
						'asincrono_id' => $asinc->id,
						'clave_1' => $socio['LiquidacionSocio']['codigo_empresa'],
						'clave_2' => $socio['LiquidacionSocio']['socio_id'],
						'clave_3' => 'REPORTE_5',
						'texto_1' => $socio['Global1']['concepto_1'],
						'texto_2' => $socio['LiquidacionSocio']['documento'],
						'texto_3' => $socio['LiquidacionSocio']['apenom'],
						'texto_4' => $socio['LiquidacionSocio']['sucursal'],
						'texto_5' => $socio['LiquidacionSocio']['nro_cta_bco'],
						'texto_6' => $socio['LiquidacionSocio']['cbu'],
						'texto_7' => $socio['Global2']['concepto_1'],
						'decimal_1' => $socio['LiquidacionSocio']['importe_adebitar'],
						'entero_1' => $socio['LiquidacionSocio']['registro']
				);
					
				if($asinc->detenido()){
					$STOP = 1;
					break;
				}
				
				if(!$this->Temporal->grabar($temp)){
					$STOP = 1;
					break;
				}	

				$i++;
			}
		
		
		endif;
		
		
        ############################################################################################
        # LISTADO DE SOCIOS
        ############################################################################################
        if($CARGO_SOCIO != 0)$this->__generaListadoCargoBySocio($asinc->id,'REPORTE_5',$CARGO_SOCIO, $CALC_IVA);
		
//		$this->out("TOTAL GENERAL ".number_format($acu2,2));
		
// 		if($STOP == 0){
			$asinc->actualizar($total,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
// 		}			
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function __cargarProveedores(){
		$this->LiquidacionCuotaNoimputada->bindModel(array('belongsTo' => array('Proveedor')));
		
		//saco los proveedores
		$proveedores = $this->LiquidacionCuotaNoimputada->find('all',array(
															'conditions' => array('LiquidacionCuotaNoimputada.liquidacion_id' => $this->liquidacion['Liquidacion']['id']),
															'fields' => array('Proveedor.id,Proveedor.razon_social_resumida'),
															'group' => array('LiquidacionCuotaNoimputada.proveedor_id'),
															'order' => array('Proveedor.razon_social')
		));	
		return $proveedores;	
	}
	

	
	function __cargarProductosByProveedor($proveedor_id){
		$productos = array();
		$registros = $this->LiquidacionCuotaNoimputada->find('all',array(
											'joins'	=> array(
														array(
															'table' => 'global_datos',
															'alias' => 'GlobalDato',
															'type' => 'inner',
															'foreignKey' => false,
															'conditions' => array('GlobalDato.id = LiquidacionCuotaNoimputada.tipo_producto')
															),		
											),
											'conditions' => array(
																'LiquidacionCuotaNoimputada.liquidacion_id' => $this->liquidacion['Liquidacion']['id'],
																'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor_id
											),
											'fields' => array('LiquidacionCuotaNoimputada.tipo_producto'),
											'group' => array('LiquidacionCuotaNoimputada.tipo_producto'),
											'order' => array('GlobalDato.concepto_1')
		));	
		foreach($registros as $producto){
			$producto['LiquidacionCuotaNoimputada']['tipo_producto_desc'] = $this->LiquidacionCuotaNoimputada->GlobalDato('concepto_1',$producto['LiquidacionCuotaNoimputada']['tipo_producto']);
			array_push($productos,$producto['LiquidacionCuotaNoimputada']);
		}	
		return $productos;
		
	}
	
	

	function __cargarCuotasByProductoByProveedor($proveedor_id,$tipo_producto){
		$cuotas = array();
		$registros = $this->LiquidacionCuotaNoimputada->find('all',array(
											'joins'	=> array(
														array(
															'table' => 'global_datos',
															'alias' => 'GlobalDato',
															'type' => 'inner',
															'foreignKey' => false,
															'conditions' => array('GlobalDato.id = LiquidacionCuotaNoimputada.tipo_producto')
															),		
											),		
											'conditions' => array(
																'LiquidacionCuotaNoimputada.liquidacion_id' => $this->liquidacion['Liquidacion']['id'],
																'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor_id,
																'LiquidacionCuotaNoimputada.tipo_producto' => $tipo_producto,
											),
											'fields' => array('LiquidacionCuotaNoimputada.tipo_cuota'),
											'group' => array('LiquidacionCuotaNoimputada.tipo_cuota'),
											'order' => array('GlobalDato.concepto_1')
		));
		foreach($registros as $cuota){
			$cuota['LiquidacionCuotaNoimputada']['tipo_cuota_desc'] = $this->LiquidacionCuotaNoimputada->GlobalDato('concepto_1',$cuota['LiquidacionCuotaNoimputada']['tipo_cuota']);
			array_push($cuotas,$cuota['LiquidacionCuotaNoimputada']);
		}	
		return $cuotas;		
	
	}
	
	function __cargarAltasByProveedorByProducto($proveedor_id,$tipo_producto){
		$ordenes = array();
// 		$ordenes = $this->OrdenDescuento->find('all',array(
// 											'joins'	=> array(
// 														array(
// 															'table' => 'global_datos',
// 															'alias' => 'GlobalDato',
// 															'type' => 'inner',
// 															'foreignKey' => false,
// 															'conditions' => array('GlobalDato.id = OrdenDescuento.tipo_producto')
// 															),
// 														array(
// 															'table' => 'persona_beneficios',
// 															'alias' => 'PersonaBeneficio',
// 															'type' => 'inner',
// 															'foreignKey' => false,
// 															'conditions' => array('PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id')
// 															),
// 														array(
// 															'table' => 'proveedores',
// 															'alias' => 'Proveedor',
// 															'type' => 'inner',
// 															'foreignKey' => false,
// 															'conditions' => array('Proveedor.id = OrdenDescuento.proveedor_id')
// 															),																																
// 											),		
// 											'conditions' => array(
// 																'OrdenDescuento.proveedor_id' => $proveedor_id,
// 																'OrdenDescuento.tipo_producto' => $tipo_producto,
// 																'OrdenDescuento.periodo_ini' => $this->liquidacion['Liquidacion']['periodo'],
// 																'PersonaBeneficio.codigo_beneficio' => $this->liquidacion['Liquidacion']['codigo_organismo'],
// 											),
// 											'fields' => array('COUNT(*) as cantidad, SUM(importe_cuota) as importe_cuota'),
// 											'group' => array('OrdenDescuento.proveedor_id,OrdenDescuento.tipo_producto'),
// 											'order' => array('Proveedor.razon_social,GlobalDato.concepto_1')
// 		));
		
		$sql = "SELECT
					OrdenDescuento.proveedor_id,OrdenDescuento.tipo_producto,
					Proveedor.razon_social_resumida,
					Global1.concepto_1,
					COUNT(OrdenDescuento.id) AS cantidad,
					SUM(OrdenDescuentoCuota.importe) AS importe_cuota,
					SUM(OrdenDescuento.importe_total) AS importe_total
				FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
				INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id)
				INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id)
				INNER JOIN global_datos AS Global1 ON (Global1.id = LiquidacionCuotaNoimputada.tipo_producto)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = LiquidacionCuotaNoimputada.proveedor_id)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = ".$this->liquidacion['Liquidacion']['id']."
					AND LiquidacionCuotaNoimputada.proveedor_id = $proveedor_id
					AND LiquidacionCuotaNoimputada.tipo_producto = '$tipo_producto' 
					AND OrdenDescuento.periodo_ini = '".$this->liquidacion['Liquidacion']['periodo']."'
				GROUP BY 	
							OrdenDescuento.proveedor_id,
							OrdenDescuento.tipo_producto,
							LiquidacionCuotaNoimputada.proveedor_id,
							LiquidacionCuotaNoimputada.tipo_producto
				ORDER BY Proveedor.razon_social,Global1.concepto_1;";
		$ordenes = $this->LiquidacionCuota->query($sql);
		return $ordenes;
	}
	
	
	
	function __cargarSociosByProveedorByProductoByCuota($proveedor_id,$tipo_producto,$tipo_cuota,$periodoIni = null){
// 		$registros = $this->LiquidacionCuota->find('all',array(
// 											'joins'	=> array(
// 															array(
// 																'table' => 'socios',
// 																'alias' => 'Socio',
// 																'type' => 'inner',
// 																'foreignKey' => false,
// 																'conditions' => array('LiquidacionCuotaNoimputada.socio_id = Socio.id')
// 																),			
// 															array(
// 																'table' => 'personas',
// 																'alias' => 'Persona',
// 																'type' => 'inner',
// 																'foreignKey' => false,
// 																'conditions' => array('Socio.persona_id = Persona.id')
// 																),
// 															array(
// 																'table' => 'global_datos',
// 																'alias' => 'GlobalDato',
// 																'type' => 'inner',
// 																'foreignKey' => false,
// 																'conditions' => array('GlobalDato.id = Persona.tipo_documento')
// 																),	
// 															array(
// 																'table' => 'orden_descuento_cuotas',
// 																'alias' => 'OrdenDescuentoCuota',
// 																'type' => 'left',
// 																'foreignKey' => false,
// 																'conditions' => array('LiquidacionCuotaNoimputada.orden_descuento_cuota_id = OrdenDescuentoCuota.id')
// 																),
// 														array(
// 																'table' => 'orden_descuentos',
// 																'alias' => 'OrdenDescuento',
// 																'type' => 'left',
// 																'foreignKey' => false,
// 																'conditions' => array('LiquidacionCuotaNoimputada.orden_descuento_id = OrdenDescuento.id')
// 																),																																																				
// 											),
// 											'conditions' => array(
// 																'LiquidacionCuotaNoimputada.liquidacion_id' => $this->liquidacion['Liquidacion']['id'],
// 																'LiquidacionCuotaNoimputada.proveedor_id' => $proveedor_id,
// 																'LiquidacionCuotaNoimputada.tipo_producto' => $tipo_producto,
// 																'LiquidacionCuotaNoimputada.tipo_cuota' => $tipo_cuota
// 											),
// 											'fields' => array(
// 																'GlobalDato.concepto_1,
// 																Persona.documento,
// 																Persona.apellido,
// 																Persona.nombre,
// 																LiquidacionCuotaNoimputada.socio_id,
// 																LiquidacionCuotaNoimputada.orden_descuento_cuota_id,
// 																LiquidacionCuotaNoimputada.periodo_cuota,
// 																LiquidacionCuotaNoimputada.saldo_actual,
// 																LiquidacionCuotaNoimputada.importe_debitado,
// 																OrdenDescuentoCuota.nro_cuota,
// 																OrdenDescuento.cuotas'
																
// 															),
// 											'order' => array('Persona.apellido,Persona.nombre,
// 																LiquidacionCuotaNoimputada.orden_descuento_id,
// 																LiquidacionCuotaNoimputada.periodo_cuota'
// 															)
// 		));

		$sql = "SELECT
					LiquidacionCuotaNoimputada.socio_id,
					Global1.concepto_1, 
					Persona.documento,
					CONCAT(Persona.apellido,', ',Persona.nombre) as apenom,
					SUM(LiquidacionCuotaNoimputada.importe) AS importe,
					SUM(LiquidacionCuotaNoimputada.saldo_actual) AS saldo_actual
				FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
				INNER JOIN socios AS Socio ON (Socio.id = LiquidacionCuotaNoimputada.socio_id)
				INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
				INNER JOIN global_datos AS Global1 ON (Global1.id = Persona.tipo_documento)
				INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = LiquidacionCuotaNoimputada.orden_descuento_cuota_id)
				INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuotaNoimputada.orden_descuento_id)
				INNER JOIN global_datos AS Global2 ON (Global2.id = LiquidacionCuotaNoimputada.tipo_producto)
				INNER JOIN global_datos AS Global3 ON (Global3.id = LiquidacionCuotaNoimputada.tipo_cuota)
				WHERE 
					LiquidacionCuotaNoimputada.liquidacion_id = ".$this->liquidacion['Liquidacion']['id']."
					AND LiquidacionCuotaNoimputada.proveedor_id = $proveedor_id
					AND LiquidacionCuotaNoimputada.tipo_producto = '$tipo_producto' 
					".(!empty($tipo_cuota) ? " AND LiquidacionCuotaNoimputada.tipo_cuota = '$tipo_cuota'" : "")."
					".(!empty($periodoIni) ? " AND OrdenDescuento.periodo_ini = '$periodoIni'" : "")."
				GROUP BY Persona.apellido,Persona.nombre
				ORDER BY Persona.apellido,Persona.nombre;";
		$registros = $this->LiquidacionCuota->query($sql);
		return $registros;	
	}
	
	
	function __cargarOrdenDebitos(){
		$registros = array();
		$sql = "SELECT
				LiquidacionSocio.codigo_empresa,
				LiquidacionSocio.socio_id, 
				Global1.concepto_1,
				LiquidacionSocio.documento,
				LiquidacionSocio.apenom,
				LiquidacionSocio.registro,
				LiquidacionSocio.importe_adebitar,
				LiquidacionSocio.sucursal,
				LiquidacionSocio.nro_cta_bco,
				LiquidacionSocio.cbu,
				LiquidacionSocio.banco_id,
				Global2.concepto_1 
				FROM liquidacion_socios AS LiquidacionSocio
				INNER JOIN global_datos AS Global1 ON (Global1.id = LiquidacionSocio.tipo_documento)
				LEFT JOIN global_datos AS Global2 ON (Global2.id = LiquidacionSocio.codigo_empresa)
				WHERE LiquidacionSocio.liquidacion_id = ".$this->liquidacion['Liquidacion']['id']."
				ORDER BY LiquidacionSocio.apenom,LiquidacionSocio.registro";
		$registros = $this->LiquidacionCuota->query($sql);
		return $registros;		
	}

    function __getCantidadSociosCobrados($proveedor_id = null,$tipo_producto = null,$tipo_cuota = null){
        
        $sql = "SELECT COUNT(distinct(socio_id)) as cantidad 
                FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
                WHERE 
                    LiquidacionCuotaNoimputada.liquidacion_id = ". $this->liquidacion['Liquidacion']['id'] . "
                    ".(!empty($proveedor_id) ? "AND LiquidacionCuotaNoimputada.proveedor_id = $proveedor_id " : " ")."
                    ".(!empty($tipo_producto) ? "AND LiquidacionCuotaNoimputada.tipo_producto = '$tipo_producto' " : " ")."
                    ".(!empty($tipo_cuota) ? "AND LiquidacionCuotaNoimputada.tipo_cuota = '$tipo_cuota' " : " ")."    
                    AND ifnull(LiquidacionCuotaNoimputada.importe_debitado,0) > 0;";
        $cantidad = $this->LiquidacionCuota->query($sql);
//        debug($cantidad);
        if(empty($cantidad)) return 0;
        else return $cantidad[0][0]['cantidad'];
    }
    
    function __generaListadoCargoBySocio($asincID,$claveRepo='REPORTE_5',$cargo = 25.5,$iva = 21){
        $iva = (!empty($iva) ? $iva : 0);
        $cargo = (!empty($cargo) ? $cargo : 0);
        $sql = "INSERT INTO asincrono_temporales(asincrono_id,clave_3,entero_1,texto_1,texto_2,decimal_1,decimal_2,decimal_3,decimal_4,decimal_5,decimal_6) "
                . "SELECT $asincID,'$claveRepo',LiquidacionCuotaNoimputada.socio_id,Persona.documento,concat(Persona.apellido,', ',Persona.nombre),
                sum(LiquidacionCuotaNoimputada.importe) as importe,
                sum(LiquidacionCuotaNoimputada.saldo_actual) as saldo_actual,
                sum(LiquidacionCuotaNoimputada.importe_debitado) as importe_debitado,
                $cargo as cargo_neto,
                $cargo * ($iva/100) as iva,
                $cargo + ($cargo * ($iva/100)) as cargo_total
                FROM liquidacion_cuota_noimputadas AS LiquidacionCuotaNoimputada
                inner join socios Socio on (Socio.id = LiquidacionCuotaNoimputada.socio_id)
                inner join personas Persona on (Persona.id = Socio.persona_id)
                WHERE 
                    LiquidacionCuotaNoimputada.liquidacion_id = ". $this->liquidacion['Liquidacion']['id'] . "
                    AND ifnull(LiquidacionCuotaNoimputada.importe_debitado,0) > 0
                    group by LiquidacionCuotaNoimputada.socio_id
                    order by Persona.apellido,Persona.nombre;";
        $this->LiquidacionCuota->query($sql);
    }
    
    
}
?>
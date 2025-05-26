<?php

/**
 * REPORTE DE CONTROL DE LA LIQUIDACION
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_control_liquidacion 205 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php reporte_control_liquidacion_periodo 27697 -app /home/adrian/trabajo/www/sigem/app/
 * 
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ReporteControlLiquidacionPeriodoShell extends Shell {

    
    
	var $uses = array(
						'Mutual.Liquidacion',
						'Mutual.LiquidacionCuota',
						'Proveedores.Proveedor',
						'Mutual.OrdenDescuentoCuota',
						'Mutual.OrdenDescuento',
						'Mutual.LiquidacionSocio',
						'Proveedores.ProveedorComision',
						'Proveedores.Proveedor',
	);
	var $liquidacion_id;
	var $liquidacion;
    var $liquidacionesIds;
    var $periodoGlobal;
	
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
        $this->periodoGlobal = $asinc->getParametro('p4');
        
        $this->liquidacionesIds = array();
        $datos = $this->Liquidacion->getLiquidacionesByPeriodo($this->periodoGlobal,false,false);
        $this->liquidacionesIds = Set::extract("/Liquidacion/id",$datos);
//        $this->liquidacionesIds = implode(",",$datos);        

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
//																	'LiquidacionCuota.liquidacion_id' => $this->liquidacion['Liquidacion']['id'],
//																	'LiquidacionCuota.proveedor_id' => $proveedor['Proveedor']['id']
//												),
//												'fields' => array('LiquidacionCuota.tipo_producto,LiquidacionCuota.tipo_cuota'),
//												'group' => array('LiquidacionCuota.tipo_producto,LiquidacionCuota.tipo_cuota'),
//												'order' => array('LiquidacionCuota.tipo_producto,LiquidacionCuota.tipo_cuota')
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
                                    
					$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $proveedor['Proveedor']['razon_social_resumida'] ." - ".$producto['tipo_producto_desc']);	
					//saco la deuda del periodo y el atraso por cuota, producto, proveedor
//					$conditions = array();
//					$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacionesIds;
//					$conditions['LiquidacionCuota.proveedor_id'] = $proveedor['Proveedor']['id'];
//					$conditions['LiquidacionCuota.tipo_producto'] = $producto['tipo_producto'];
//					$conditions['LiquidacionCuota.tipo_cuota'] = $cuota['tipo_cuota'];
//					$conditions['LiquidacionCuota.periodo_cuota'] = $this->periodoGlobal;
					
					
//					$totalPeriodo = $this->LiquidacionCuota->find('all',array(
//													'conditions' => $conditions,
//													'fields' => array('ifnull(sum(saldo_actual),0) as saldo_actual,ifnull(sum(importe),0) as importe'),
//					));
                                        
                                        
                                        $sql = "select ifnull(sum(LiquidacionCuota.saldo_actual),0) as saldo_actual,ifnull(sum(LiquidacionCuota.importe),0) as importe 
                                                from liquidacion_cuotas LiquidacionCuota
                                                inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionCuota.liquidacion_id)
                                                inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                                                where Liquidacion.periodo = '".$this->periodoGlobal."'
                                                and LiquidacionCuota.proveedor_id = ".$proveedor['Proveedor']['id']."
                                                and LiquidacionCuota.tipo_producto = '".$producto['tipo_producto']."'
                                                and LiquidacionCuota.tipo_cuota = '".$cuota['tipo_cuota']."'
                                                and LiquidacionCuota.periodo_cuota = Liquidacion.periodo;";
                                        $totalPeriodo = $this->LiquidacionCuota->query($sql);
                                        
//					debug($totalPeriodo);
					//saco el atraso
//					$conditions = array();
//					$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacionesIds;
//					$conditions['LiquidacionCuota.proveedor_id'] = $proveedor['Proveedor']['id'];
//					$conditions['LiquidacionCuota.tipo_producto'] = $producto['tipo_producto'];
//					$conditions['LiquidacionCuota.tipo_cuota'] = $cuota['tipo_cuota'];
//					$conditions['LiquidacionCuota.periodo_cuota <'] = $this->periodoGlobal;
//					
//					$totalAtraso = $this->LiquidacionCuota->find('all',array(
//													'conditions' => $conditions,
//													'fields' => array('ifnull(sum(saldo_actual),0) as saldo_actual,ifnull(sum(importe),0) as importe'),
//					));
                                        
                                        $sql = "select ifnull(sum(LiquidacionCuota.saldo_actual),0) as saldo_actual,ifnull(sum(LiquidacionCuota.importe),0) as importe 
                                                from liquidacion_cuotas LiquidacionCuota
                                                inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionCuota.liquidacion_id)
                                                inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                                                where Liquidacion.periodo = '".$this->periodoGlobal."'
                                                and LiquidacionCuota.proveedor_id = ".$proveedor['Proveedor']['id']."
                                                and LiquidacionCuota.tipo_producto = '".$producto['tipo_producto']."'
                                                and LiquidacionCuota.tipo_cuota = '".$cuota['tipo_cuota']."'
                                                and LiquidacionCuota.periodo_cuota < Liquidacion.periodo;";
                                        $totalAtraso = $this->LiquidacionCuota->query($sql);                                        
					
					//calculo lo imputado
					$totalImputaPeriodo = 0;
					$totalImputaAtraso = 0;
					$totalImputa = 0;
					$porcenajeComision = 0;
					$comision = 0;
					$totalReversado = 0;
					$totalComision = 0;
					$comision_IVA = 0;
                    
//					if($imputada == 1):

						//saco la deuda del periodo y el atraso por cuota, producto, proveedor
//						$conditions = array();
//						$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacionesIds;
//						$conditions['LiquidacionCuota.proveedor_id'] = $proveedor['Proveedor']['id'];
//						$conditions['LiquidacionCuota.tipo_producto'] = $producto['tipo_producto'];
//						$conditions['LiquidacionCuota.tipo_cuota'] = $cuota['tipo_cuota'];
//						
////						if($preImputado == 0)$conditions['LiquidacionCuota.imputada'] = 1;
////						else $conditions['LiquidacionCuota.para_imputar'] = 1;
//						
//						$conditions['LiquidacionCuota.periodo_cuota'] = $this->periodoGlobal;
//						
//						$fields = array('ifnull(sum(importe_debitado),0) as imputado');
//						
//						
//						//vinculo la cuota a la comision del proveedor
//						$totalImputaPeriodo = $this->LiquidacionCuota->find('all',array(
//														'conditions' => $conditions,
//														'fields' => $fields,
//						));
                                                
                                                
                                            $sql = "select ifnull(sum(LiquidacionCuota.importe_debitado),0) as imputado 
                                                    from liquidacion_cuotas LiquidacionCuota
                                                    inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionCuota.liquidacion_id)
                                                    inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                                                    where Liquidacion.periodo = '".$this->periodoGlobal."'
                                                    and LiquidacionCuota.proveedor_id = ".$proveedor['Proveedor']['id']."
                                                    and LiquidacionCuota.tipo_producto = '".$producto['tipo_producto']."'
                                                    and LiquidacionCuota.tipo_cuota = '".$cuota['tipo_cuota']."'
                                                    and LiquidacionCuota.periodo_cuota = Liquidacion.periodo;";                                                
                                                
                                                $totalImputaPeriodo = $this->LiquidacionCuota->query($sql);     
                                                    
						//saco el atraso
//						$conditions = array();
//						$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacionesIds;
//						$conditions['LiquidacionCuota.proveedor_id'] = $proveedor['Proveedor']['id'];
//						$conditions['LiquidacionCuota.tipo_producto'] = $producto['tipo_producto'];
//						$conditions['LiquidacionCuota.tipo_cuota'] = $cuota['tipo_cuota'];
//						
////						if($preImputado == 0)$conditions['LiquidacionCuota.imputada'] = 1;
////						else $conditions['LiquidacionCuota.para_imputar'] = 1;
//						
//						$conditions['LiquidacionCuota.periodo_cuota <'] = $this->periodoGlobal;
//						
//						$totalImputaAtraso = $this->LiquidacionCuota->find('all',array(
//														'conditions' => $conditions,
//														'fields' => $fields,
//						));	
                                                
                                                
                                                
                                            $sql = "select ifnull(sum(LiquidacionCuota.importe_debitado),0) as imputado 
                                                    from liquidacion_cuotas LiquidacionCuota
                                                    inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionCuota.liquidacion_id)
                                                    inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                                                    where Liquidacion.periodo = '".$this->periodoGlobal."'
                                                    and LiquidacionCuota.proveedor_id = ".$proveedor['Proveedor']['id']."
                                                    and LiquidacionCuota.tipo_producto = '".$producto['tipo_producto']."'
                                                    and LiquidacionCuota.tipo_cuota = '".$cuota['tipo_cuota']."'
                                                    and LiquidacionCuota.periodo_cuota < Liquidacion.periodo;";                                                
                                                
                                                $totalImputaAtraso = $this->LiquidacionCuota->query($sql);                                                   

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
						$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacionesIds;
						$conditions['LiquidacionCuota.proveedor_id'] = $proveedor['Proveedor']['id'];
						$conditions['LiquidacionCuota.tipo_producto'] = $producto['tipo_producto'];
						$conditions['LiquidacionCuota.tipo_cuota'] = $cuota['tipo_cuota'];
						
						if($preImputado == 0)$conditions['LiquidacionCuota.imputada'] = 1;
						else $conditions['LiquidacionCuota.para_imputar'] = 1;
						
						
						$fields = array('ifnull(sum(comision_cobranza),0) as comision_cobranza');
						$totalComision = $this->LiquidacionCuota->find('all',array(
														'conditions' => $conditions,
														'fields' => $fields,
						));						
						$totalComision = (isset($totalComision[0][0]['comision_cobranza']) ? $totalComision[0][0]['comision_cobranza'] : 0);
						$comision = $totalComision;
						
//						$porcenajeComision = $this->ProveedorComision->getComision($this->liquidacion['Liquidacion']['codigo_organismo'],$proveedor['Proveedor']['id'],$producto['tipo_producto'],$cuota['tipo_cuota']);
//						
//						if($totalComision == 0)$comision = $totalImputa * $porcenajeComision / 100;
//						else $comision = $totalComision;
						
//						$this->out($totalReversado);

//					endif;
					
					
					
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
							
// 							$this->out("\t".$socio['Persona']['apellido'].", ".$socio['Persona']['nombre']."\tPERIODO: ".$socio['LiquidacionCuota']['periodo_cuota']." | CUOTA:".$socio['OrdenDescuentoCuota']['nro_cuota']."/".$socio['OrdenDescuento']['cuotas']."\tTOTAL: ".$socio['LiquidacionCuota']['saldo_actual']."\n");
							
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
		$this->LiquidacionCuota->bindModel(array('belongsTo' => array('Proveedor')));
                $sql = "select Proveedor.id,Proveedor.razon_social_resumida from liquidacion_cuotas LiquidacionCuota
                        inner join proveedores Proveedor on Proveedor.id = LiquidacionCuota.proveedor_id
                        inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
                        inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                        where Liquidacion.periodo = '".$this->periodoGlobal."'
                        group by LiquidacionCuota.proveedor_id
                        order by Proveedor.razon_social;";

		$proveedores = $this->LiquidacionCuota->query($sql);	        
		return $proveedores;	
	}
	

	
	function __cargarProductosByProveedor($proveedor_id){
		$productos = array();
        
                $sql = "select LiquidacionCuota.tipo_producto from liquidacion_cuotas LiquidacionCuota
                        inner join global_datos GlobalDato on (GlobalDato.id = LiquidacionCuota.tipo_producto)
                        inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
                        inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                        where Liquidacion.periodo = '".$this->periodoGlobal."' and LiquidacionCuota.proveedor_id = $proveedor_id 
                        group by LiquidacionCuota.tipo_producto
                        order by GlobalDato.concepto_1;";
                $registros = $this->LiquidacionCuota->query($sql);
        
		foreach($registros as $producto){
			$producto['LiquidacionCuota']['tipo_producto_desc'] = $this->LiquidacionCuota->GlobalDato('concepto_1',$producto['LiquidacionCuota']['tipo_producto']);
			array_push($productos,$producto['LiquidacionCuota']);
		}	
		return $productos;
		
	}
	
	

	function __cargarCuotasByProductoByProveedor($proveedor_id,$tipo_producto){
            $cuotas = array();

            $sql = "select LiquidacionCuota.tipo_cuota from liquidacion_cuotas LiquidacionCuota
                    inner join global_datos GlobalDato on (GlobalDato.id = LiquidacionCuota.tipo_producto)
                    inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
                    inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                    where Liquidacion.periodo = '".$this->periodoGlobal."' 
                    and LiquidacionCuota.proveedor_id =  $proveedor_id  and LiquidacionCuota.tipo_producto = '$tipo_producto'    
                    group by LiquidacionCuota.tipo_cuota
                    order by GlobalDato.concepto_1;";
            $registros = $this->LiquidacionCuota->query($sql);
        
		foreach($registros as $cuota){
			$cuota['LiquidacionCuota']['tipo_cuota_desc'] = $this->LiquidacionCuota->GlobalDato('concepto_1',$cuota['LiquidacionCuota']['tipo_cuota']);
			array_push($cuotas,$cuota['LiquidacionCuota']);
		}	
		return $cuotas;		
	
	}
	
	function __cargarAltasByProveedorByProducto($proveedor_id,$tipo_producto){
		$ordenes = array();
		
		$sql = "SELECT
					OrdenDescuento.proveedor_id,OrdenDescuento.tipo_producto,
					Proveedor.razon_social_resumida,
					Global1.concepto_1,
					COUNT(OrdenDescuento.id) AS cantidad,
					SUM(OrdenDescuentoCuota.importe) AS importe_cuota,
					SUM(OrdenDescuento.importe_total) AS importe_total
				FROM liquidacion_cuotas AS LiquidacionCuota
                                inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
                                inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
				INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
				INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuota.orden_descuento_id)
				INNER JOIN global_datos AS Global1 ON (Global1.id = LiquidacionCuota.tipo_producto)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = LiquidacionCuota.proveedor_id)
				WHERE 
					Liquidacion.periodo = '".$this->periodoGlobal."' 
					AND LiquidacionCuota.proveedor_id = $proveedor_id
					AND LiquidacionCuota.tipo_producto = '$tipo_producto' 
					AND OrdenDescuento.periodo_ini = '".$this->periodoGlobal."'
				GROUP BY 	
							OrdenDescuento.proveedor_id,
							OrdenDescuento.tipo_producto,
							LiquidacionCuota.proveedor_id,
							LiquidacionCuota.tipo_producto
				ORDER BY Proveedor.razon_social,Global1.concepto_1;";
		$ordenes = $this->LiquidacionCuota->query($sql);
		return $ordenes;
	}
	
	
	
	function __cargarSociosByProveedorByProductoByCuota($proveedor_id,$tipo_producto,$tipo_cuota,$periodoIni = null){
		$sql = "SELECT
					LiquidacionCuota.socio_id,
					Global1.concepto_1, 
					Persona.documento,
					CONCAT(Persona.apellido,', ',Persona.nombre) as apenom,
					SUM(LiquidacionCuota.importe) AS importe,
					SUM(LiquidacionCuota.saldo_actual) AS saldo_actual
				FROM liquidacion_cuotas AS LiquidacionCuota
                                inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
                                inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
				INNER JOIN socios AS Socio ON (Socio.id = LiquidacionCuota.socio_id)
				INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
				INNER JOIN global_datos AS Global1 ON (Global1.id = Persona.tipo_documento)
				INNER JOIN orden_descuento_cuotas AS OrdenDescuentoCuota ON (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
				INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.id = LiquidacionCuota.orden_descuento_id)
				INNER JOIN global_datos AS Global2 ON (Global2.id = LiquidacionCuota.tipo_producto)
				INNER JOIN global_datos AS Global3 ON (Global3.id = LiquidacionCuota.tipo_cuota)
				WHERE 
					Liquidacion.periodo = '".$this->periodoGlobal."' 
					AND LiquidacionCuota.proveedor_id = $proveedor_id
					AND LiquidacionCuota.tipo_producto = '$tipo_producto' 
					".(!empty($tipo_cuota) ? " AND LiquidacionCuota.tipo_cuota = '$tipo_cuota'" : "")."
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
                                inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionSocio.liquidacion_id
                                inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
				INNER JOIN global_datos AS Global1 ON (Global1.id = LiquidacionSocio.tipo_documento)
				LEFT JOIN global_datos AS Global2 ON (Global2.id = LiquidacionSocio.codigo_empresa)
				WHERE Liquidacion.periodo = '".$this->periodoGlobal."'
				ORDER BY LiquidacionSocio.apenom,LiquidacionSocio.registro";
		$registros = $this->LiquidacionCuota->query($sql);
		return $registros;		
	}

    function __getCantidadSociosCobrados($proveedor_id = null,$tipo_producto = null,$tipo_cuota = null){
        
        $sql = "SELECT COUNT(distinct(socio_id)) as cantidad 
                FROM liquidacion_cuotas AS LiquidacionCuota
                inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
                inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                WHERE 
                    Liquidacion.periodo = '".$this->periodoGlobal."'
                    ".(!empty($proveedor_id) ? "AND LiquidacionCuota.proveedor_id = $proveedor_id " : " ")."
                    ".(!empty($tipo_producto) ? "AND LiquidacionCuota.tipo_producto = '$tipo_producto' " : " ")."
                    ".(!empty($tipo_cuota) ? "AND LiquidacionCuota.tipo_cuota = '$tipo_cuota' " : " ")."    
                    AND ifnull(LiquidacionCuota.importe_debitado,0) > 0;";
        $cantidad = $this->LiquidacionCuota->query($sql);
//        debug($cantidad);
        if(empty($cantidad)) return 0;
        else return $cantidad[0][0]['cantidad'];
    }
    
    function __generaListadoCargoBySocio($asincID,$claveRepo='REPORTE_5',$cargo = 25.5,$iva = 21){
        $iva = (empty($iva) ? 0 : $iva);
        $sql = "INSERT INTO asincrono_temporales(asincrono_id,clave_3,entero_1,texto_1,texto_2,decimal_1,decimal_2,decimal_3,decimal_4,decimal_5,decimal_6) "
                . "SELECT $asincID,'$claveRepo',LiquidacionCuota.socio_id,Persona.documento,concat(Persona.apellido,', ',Persona.nombre),
                sum(LiquidacionCuota.importe) as importe,
                sum(LiquidacionCuota.saldo_actual) as saldo_actual,
                sum(LiquidacionCuota.importe_debitado) as importe_debitado,
                $cargo as cargo_neto,
                $cargo * ($iva/100) as iva,
                $cargo + ($cargo * ($iva/100)) as cargo_total
                FROM liquidacion_cuotas AS LiquidacionCuota
                inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
                inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
                inner join socios Socio on (Socio.id = LiquidacionCuota.socio_id)
                inner join personas Persona on (Persona.id = Socio.persona_id)
                WHERE 
                    Liquidacion.periodo = '".$this->periodoGlobal."'
                    AND ifnull(LiquidacionCuota.importe_debitado,0) > 0
                    group by LiquidacionCuota.socio_id
                    order by Persona.apellido,Persona.nombre;";
        $this->LiquidacionCuota->query($sql);
    }
    
    
}
?>
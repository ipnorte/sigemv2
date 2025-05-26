<?php

/**
*
* listado_posicion_consolidada.php
* @author adrian [* 26/01/2013]
* 
* /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_posicion_consolidada 9445 -app /home/adrian/dev/www/sigem/app/
* /usr/bin/php5 /var/www/sigem/cake/console/cake.php listado_posicion_consolidada 9440 -app /var/www/sigem/app/
*/

class ListadoPosicionConsolidadaShell extends Shell{
	
	var $tasks = array('Temporal');
	
	function main(){
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLIQCUOTA = new LiquidacionCuota();
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();	
		
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();		
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}

		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 		

		
		$periodo				= $asinc->getParametro('p1');
		$codigo_organismo		= $asinc->getParametro('p2');
		$proveedor_id			= $asinc->getParametro('p3');
		$empresa				= $asinc->getParametro('p4');
		$detallado				= $asinc->getParametro('p5');
		
		$asinc->actualizar(1,100,"ESPERE, CARGANDO ORGANISMOS, EMPRESAS Y TURNOS A PROCESAR...");
		$STOP = 0;
		$total = 0;
		$i = 0;		
		
			//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}		
		
//		$SQL = "SELECT
//				PersonaBeneficio.codigo_beneficio,
//				PersonaBeneficio.codigo_empresa, 
//				PersonaBeneficio.turno_pago,
//				LiquidacionCuota.proveedor_id,
//				Proveedor.razon_social_resumida,
//				GlobalDato_1.concepto_1,
//				GlobalDato_2.concepto_1,
//				LiquidacionTurno.descripcion
//				FROM liquidacion_cuotas AS LiquidacionCuota
//				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
//				INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
//				INNER JOIN proveedores AS Proveedor ON (Proveedor.id = LiquidacionCuota.proveedor_id)
//				INNER JOIN global_datos AS GlobalDato_1 ON (GlobalDato_1.id = PersonaBeneficio.codigo_beneficio)
//				INNER JOIN global_datos AS GlobalDato_2 ON (GlobalDato_2.id = PersonaBeneficio.codigo_empresa)
//				INNER JOIN liquidacion_turnos AS LiquidacionTurno ON (LiquidacionTurno.turno = PersonaBeneficio.turno_pago)
//				WHERE Liquidacion.periodo = '$periodo'
//				".(!empty($codigo_organismo) ? " AND Liquidacion.codigo_organismo = '$codigo_organismo'" : "")."	
//				".(!empty($proveedor_id) ? " AND LiquidacionCuota.proveedor_id = $proveedor_id" : "")."	
//				".(!empty($codigo_organismo) ? " AND PersonaBeneficio.codigo_beneficio = '$codigo_organismo'" : "")."	
//				".(!empty($empresa) ? " AND PersonaBeneficio.codigo_empresa = '".$empresa."'" : "")."	
//				GROUP BY 
//				PersonaBeneficio.codigo_beneficio,
//				PersonaBeneficio.codigo_empresa, 
//				PersonaBeneficio.turno_pago,
//				LiquidacionCuota.proveedor_id
//				ORDER BY
//				GlobalDato_1.concepto_1,
//				GlobalDato_2.concepto_1,
//				Proveedor.razon_social_resumida,
//				LiquidacionTurno.descripcion";

//		$SQL = "SELECT 
//					PersonaBeneficio.codigo_beneficio,
//					PersonaBeneficio.codigo_empresa, 
//					PersonaBeneficio.turno_pago,
//					LiquidacionCuota.proveedor_id,
//					Proveedor.razon_social_resumida,
//					GlobalDato_1.concepto_1,
//					GlobalDato_2.concepto_1,
//					LiquidacionTurno.descripcion
//					".($detallado == 1 ? ",Persona.documento,CONCAT(Persona.apellido,', ',Persona.nombre) AS apenom,LiquidacionCuota.socio_id,LiquidacionCuota.orden_descuento_id" : "")."
//					FROM liquidacion_cuotas AS LiquidacionCuota
//					INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
//					INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = LiquidacionCuota.persona_beneficio_id)
//					".($detallado == 1 ? "INNER JOIN socios AS Socio ON (Socio.id = LiquidacionCuota.socio_id)" : "")."
//					".($detallado == 1 ? "INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)" : "")."
//					INNER JOIN proveedores AS Proveedor ON (Proveedor.id = LiquidacionCuota.proveedor_id)
//					INNER JOIN global_datos AS GlobalDato_1 ON (GlobalDato_1.id = PersonaBeneficio.codigo_beneficio)
//					INNER JOIN global_datos AS GlobalDato_2 ON (GlobalDato_2.id = PersonaBeneficio.codigo_empresa)
//					INNER JOIN liquidacion_turnos AS LiquidacionTurno ON (LiquidacionTurno.turno = PersonaBeneficio.turno_pago)
//				WHERE 
//					Liquidacion.periodo = '$periodo'
//					".(!empty($codigo_organismo) ? " AND Liquidacion.codigo_organismo = '$codigo_organismo'" : "")."	
//					".(!empty($proveedor_id) ? " AND LiquidacionCuota.proveedor_id = $proveedor_id" : "")."	
//					".(!empty($codigo_organismo) ? " AND PersonaBeneficio.codigo_beneficio = '$codigo_organismo'" : "")."	
//					".(!empty($empresa) ? " AND PersonaBeneficio.codigo_empresa = '".$empresa."'" : "")."	
//				GROUP BY 
//					PersonaBeneficio.codigo_beneficio,
//					PersonaBeneficio.codigo_empresa, 
//					PersonaBeneficio.turno_pago,
//					LiquidacionCuota.proveedor_id
//					".($detallado == 1 ? ",LiquidacionCuota.socio_id,LiquidacionCuota.orden_descuento_id" : "")."
//				ORDER BY
//					".($detallado == 1 ? "Persona.apellido,Persona.nombre," : "")."
//					GlobalDato_1.concepto_1,
//					GlobalDato_2.concepto_1,
//					Proveedor.razon_social_resumida,
//					LiquidacionTurno.descripcion;";	

		
		$SQL = "SELECT 
					be.codigo_beneficio,
					be.codigo_empresa, 
					be.turno_pago,
					cu.proveedor_id,
					pro.razon_social_resumida,
					gl1.concepto_1,
					gl2.concepto_1,
					tur.descripcion
					,pe.documento,CONCAT(pe.apellido,', ',pe.nombre) AS apenom,cu.socio_id,cu.orden_descuento_id
					FROM orden_descuento_cuotas cu
					INNER JOIN persona_beneficios be ON(be.id = cu.persona_beneficio_id)
					INNER JOIN socios AS so ON (so.id = cu.socio_id)
					INNER JOIN personas AS pe ON (pe.id = so.persona_id)
					INNER JOIN proveedores AS pro ON (pro.id = cu.proveedor_id)
					INNER JOIN global_datos AS gl1 ON (gl1.id = be.codigo_beneficio)
					INNER JOIN global_datos AS gl2 ON (gl2.id = be.codigo_empresa)
					INNER JOIN liquidacion_turnos AS tur ON (tur.turno = be.turno_pago)
				WHERE
					1 = 1
					".(!empty($proveedor_id) ? " AND cu.proveedor_id = $proveedor_id" : "")."	
					".(!empty($codigo_organismo) ? " AND be.codigo_beneficio = '$codigo_organismo'" : "")."	
					".(!empty($empresa) ? " AND be.codigo_empresa = '".$empresa."'" : "")."	
				GROUP BY 
					be.codigo_beneficio,
					be.codigo_empresa, 
					be.turno_pago,
					cu.proveedor_id
					".($detallado == 1 ? ",cu.socio_id,cu.orden_descuento_id" : "")."
				ORDER BY
					".($detallado == 1 ? "pe.apellido,pe.nombre," : "")."
					gl1.concepto_1,
					gl2.concepto_1,
					pro.razon_social_resumida,
					tur.descripcion;";

		$datos = $oLIQCUOTA->query($SQL);
		
//		DEBUG($SQL);
		
		if(empty($datos)){
			$asinc->fin("NO EXISTEN DATOS PARA EL CRITERIO SELECCIONADO...");
			return;			
		}
		
		$total = count($datos);
		$asinc->setTotal($total);
		$i = 0;			
		
		foreach($datos as $dato):
		
		
			$documento = null;
			$apenom = null;
			$socioId = 0;
			$beneficioId = 0;
			$ordenId = 0;
			$ordenTipoAndNumero = null;
			if($detallado == 1){
				$documento = $dato['pe']['documento'];
				$apenom = $dato[0]['apenom'];
				$socioId = $dato['cu']['socio_id'];
//				$beneficioId = $dato['LiquidacionCuota']['persona_beneficio_id'];
				$ordenId = $dato['cu']['orden_descuento_id'];
				$orden = $oORDEN->getOrden($ordenId);
				$ordenTipoAndNumero = $orden['OrdenDescuento']['tipo_nro'];
			}
			
			
			$codigo_organismo = $dato['be']['codigo_beneficio'];
			$codigo_empresa = $dato['be']['codigo_empresa'];
			$turno_pago = $dato['be']['turno_pago'];
			$proveedor_id = $dato['cu']['proveedor_id'];
			$proveedor = $dato['pro']['razon_social_resumida'];
			$organismo = $dato['gl1']['concepto_1'];
			$empresa = $dato['gl2']['concepto_1'];
			$turno = $dato['tur']['descripcion'];
			
			//$msg = "$i / $total - PROCESANDO >> " . $organismo . (!empty($empresa) ? " - " .$empresa : "") . (!empty($turno) ? " - " . $turno : "");
			$msg = "$i / $total - " . $organismo . (!empty($empresa) ? " - " .$empresa : "");
			if($detallado == 1) $msg .= " | $apenom";
			
			$asinc->actualizar($i,$total,$msg);
			
			$this->out($msg);
			
			if(substr($codigo_organismo,8,2) != 22) $codigo_empresa = $turno_pago = null;
			
			
			if($detallado == 1):
				$devengado_periodo = $this->getTotalDevengadoPeriodo($ordenId, $proveedor_id, $periodo, $codigo_organismo, $codigo_empresa, $turno);
				
			else:
				$asinc->actualizar(10,100,$msg . " - CALCULANDO DEVENGADO PERIODO");
//				$SQL = "SELECT 
//							IFNULL(SUM(OrdenDescuentoCuota.importe),0) as devengado 
//						FROM orden_descuento_cuotas AS OrdenDescuentoCuota
//						INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//						INNER JOIN liquidacion_cuotas AS LiquidacionCuota ON (LiquidacionCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
//						INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
//						WHERE 
//							Liquidacion.periodo = '$periodo' 
//							AND Liquidacion.codigo_organismo = '$codigo_organismo'
//							AND PersonaBeneficio.codigo_beneficio = '$codigo_organismo'
//							".(!empty($codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '$codigo_empresa'" : "")."
//							".(!empty($turno_pago) ? " AND PersonaBeneficio.turno_pago = '$turno_pago'" : "")."
//							AND OrdenDescuentoCuota.proveedor_id = $proveedor_id
//							AND OrdenDescuentoCuota.periodo = '$periodo'";

				$SQL = "SELECT 
							IFNULL(SUM(OrdenDescuentoCuota.importe),0) as devengado 
						FROM orden_descuento_cuotas AS OrdenDescuentoCuota
						INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
						WHERE 
							PersonaBeneficio.codigo_beneficio = '$codigo_organismo'
							".(!empty($codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '$codigo_empresa'" : "")."
							".(!empty($turno_pago) ? " AND PersonaBeneficio.turno_pago = '$turno_pago'" : "")."
							AND OrdenDescuentoCuota.proveedor_id = $proveedor_id
							AND OrdenDescuentoCuota.periodo = '$periodo'
							AND OrdenDescuentoCuota.estado <> 'B';";
								
//			debug($SQL);
				$devengado_periodo = $oCUOTA->query($SQL);
				$devengado_periodo = $devengado_periodo[0][0]['devengado'];
			endif;
			
//			$this->out($msg." * ");
			if($detallado == 1):
				$cobrado_periodo = $this->getTotalPagadoPeriodo($ordenId, $proveedor_id, $beneficioId, $periodo);
			else:
				$asinc->actualizar(20,100,$msg . " - CALCULANDO COBRADO PERIODO");
				
//				$SQL = "SELECT IFNULL(SUM(cuco.importe),0) as cobrado FROM 
//						orden_descuento_cobro_cuotas cuco
//						INNER JOIN orden_descuento_cobros co ON (co.id = cuco.orden_descuento_cobro_id)
//						INNER JOIN orden_descuento_cuotas AS cu ON (cu.id = cuco.orden_descuento_cuota_id)
//						INNER JOIN persona_beneficios AS be ON (be.id = cu.persona_beneficio_id)
//						INNER JOIN liquidacion_cuotas AS lc ON (lc.orden_descuento_cuota_id = cu.id)
//						INNER JOIN liquidaciones lq ON (lq.id = lc.liquidacion_id) 							
//						WHERE
//							be.codigo_beneficio = '$codigo_organismo'
//							".(!empty($codigo_empresa) ? " AND be.codigo_empresa = '$codigo_empresa'" : "")."
//							".(!empty($turno_pago) ? " AND be.turno_pago = '$turno_pago'" : "")."
//							AND cu.proveedor_id = $proveedor_id
//							AND co.periodo_cobro <= '$periodo'	
//							AND cu.periodo = '$periodo'
//							AND cuco.reversado = 0
//							AND lq.periodo = '$periodo'";

				$SQL = "SELECT IFNULL(SUM(cuco.importe),0) as cobrado FROM 
						orden_descuento_cobro_cuotas cuco
						INNER JOIN orden_descuento_cobros co ON (co.id = cuco.orden_descuento_cobro_id)
						INNER JOIN orden_descuento_cuotas AS cu ON (cu.id = cuco.orden_descuento_cuota_id)
						INNER JOIN persona_beneficios AS be ON (be.id = cu.persona_beneficio_id)
						WHERE
							be.codigo_beneficio = '$codigo_organismo'
							".(!empty($codigo_empresa) ? " AND be.codigo_empresa = '$codigo_empresa'" : "")."
							".(!empty($turno_pago) ? " AND be.turno_pago = '$turno_pago'" : "")."
							AND cu.proveedor_id = $proveedor_id
							AND co.periodo_cobro <= '$periodo'	
							AND cu.periodo = '$periodo'
							AND cuco.reversado = 0;";
				
				
				$cobrado_periodo = $oCUOTA->query($SQL);
				$cobrado_periodo = $cobrado_periodo[0][0]['cobrado'];
			endif;
//			$this->out($msg." ** ");

			if($detallado == 1):
				$devengado_mora = $this->getTotalDevengadoMora($ordenId, $proveedor_id, $beneficioId, $periodo);
			else:
				$asinc->actualizar(30,100,$msg . " - CALCULANDO MORA DEVENGADA");
//				$SQL = "SELECT IFNULL(SUM(OrdenDescuentoCuota.importe),0) as devengado FROM orden_descuento_cuotas AS OrdenDescuentoCuota
//						INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//						INNER JOIN liquidacion_cuotas AS LiquidacionCuota ON (LiquidacionCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
//						INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
//						WHERE Liquidacion.periodo = '$periodo' AND Liquidacion.codigo_organismo = '$codigo_organismo'
//						AND PersonaBeneficio.codigo_beneficio = '$codigo_organismo'
//						".(!empty($codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '$codigo_empresa'" : "")."
//						".(!empty($turno_pago) ? " AND PersonaBeneficio.turno_pago = '$turno_pago'" : "")."
//						AND OrdenDescuentoCuota.proveedor_id = $proveedor_id
//						AND OrdenDescuentoCuota.periodo < '$periodo'";	

				$SQL = "SELECT IFNULL(SUM(OrdenDescuentoCuota.importe),0) as devengado FROM orden_descuento_cuotas AS OrdenDescuentoCuota
						INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
						WHERE PersonaBeneficio.codigo_beneficio = '$codigo_organismo'
						".(!empty($codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '$codigo_empresa'" : "")."
						".(!empty($turno_pago) ? " AND PersonaBeneficio.turno_pago = '$turno_pago'" : "")."
						AND OrdenDescuentoCuota.proveedor_id = $proveedor_id
						AND OrdenDescuentoCuota.periodo < '$periodo'
						AND OrdenDescuentoCuota.estado <> 'B'
						AND OrdenDescuentoCuota.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
							INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
							WHERE 
							cocu.orden_descuento_cuota_id = OrdenDescuentoCuota.id
							AND co.periodo_cobro <= '$periodo');";				
				
//				debug($SQL);
				$devengado_mora = $oCUOTA->query($SQL);
				$devengado_mora = $devengado_mora[0][0]['devengado'];
				
			endif;
//			$this->out($msg." *** ");
			
			if($detallado == 1):
				$cobrado_mora = $this->getTotalPagadoMora($ordenId, $proveedor_id, $beneficioId, $periodo);
			else:
				$asinc->actualizar(40,100,$msg . " - CALCULANDO MORA COBRADA");
//				$SQL = "SELECT IFNULL(SUM(cuco.importe),0) as cobrado FROM 
//						orden_descuento_cobro_cuotas cuco
//						INNER JOIN orden_descuento_cobros co ON (co.id = cuco.orden_descuento_cobro_id)
//						INNER JOIN orden_descuento_cuotas AS cu ON (cu.id = cuco.orden_descuento_cuota_id)
//						INNER JOIN persona_beneficios AS be ON (be.id = cu.persona_beneficio_id)
//						INNER JOIN liquidacion_cuotas AS lc ON (lc.orden_descuento_cuota_id = cu.id)
//						INNER JOIN liquidaciones lq ON (lq.id = lc.liquidacion_id) 							
//						WHERE
//							be.codigo_beneficio = '$codigo_organismo'
//							".(!empty($codigo_empresa) ? " AND be.codigo_empresa = '$codigo_empresa'" : "")."
//							".(!empty($turno_pago) ? " AND be.turno_pago = '$turno_pago'" : "")."
//							AND cu.proveedor_id = $proveedor_id
//							AND co.periodo_cobro <= '$periodo'	
//							AND cu.periodo < '$periodo'
//							AND cuco.reversado = 0
//							AND lq.periodo = '$periodo'";
				
				$SQL = "SELECT IFNULL(SUM(cuco.importe),0) as cobrado FROM 
						orden_descuento_cobro_cuotas cuco
						INNER JOIN orden_descuento_cobros co ON (co.id = cuco.orden_descuento_cobro_id)
						INNER JOIN orden_descuento_cuotas AS cu ON (cu.id = cuco.orden_descuento_cuota_id)
						INNER JOIN persona_beneficios AS be ON (be.id = cu.persona_beneficio_id)
						WHERE
							be.codigo_beneficio = '$codigo_organismo'
							".(!empty($codigo_empresa) ? " AND be.codigo_empresa = '$codigo_empresa'" : "")."
							".(!empty($turno_pago) ? " AND be.turno_pago = '$turno_pago'" : "")."
							AND cu.proveedor_id = $proveedor_id
							AND co.periodo_cobro = '$periodo'	
							AND cu.periodo < '$periodo'
							AND cuco.reversado = 0;";				
				
				$cobrado_mora = $oCUOTA->query($SQL);
				$cobrado_mora = $cobrado_mora[0][0]['cobrado'];			
			endif;
//			$this->out($msg." **** ");
			if($detallado == 1):
			
				$avencer = $this->getTotalAVencer($ordenId, $proveedor_id, $beneficioId, $periodo);
				
			else:
				$asinc->actualizar(50,100,$msg . " - CALCULANDO A VENCER");
				$SQL = "SELECT IFNULL(SUM(OrdenDescuentoCuota.importe),0) as avencer FROM orden_descuento_cuotas AS OrdenDescuentoCuota
						INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
						WHERE 
						PersonaBeneficio.codigo_beneficio = '$codigo_organismo'
						".(!empty($codigo_empresa) ? " AND PersonaBeneficio.codigo_empresa = '$codigo_empresa'" : "")."
						".(!empty($turno_pago) ? " AND PersonaBeneficio.turno_pago = '$turno_pago'" : "")."
						AND OrdenDescuentoCuota.proveedor_id = $proveedor_id
						AND OrdenDescuentoCuota.periodo > '$periodo'
						AND OrdenDescuentoCuota.estado <> 'B';";
//				debug($SQL);
				$avencer = $oCUOTA->query($SQL);
				$avencer = $avencer[0][0]['avencer'];	
				
			endif;
//			$this->out($msg." ***** ");
						
//			$this->out("#".$ordenId."\t".$devengado_periodo."\t".$devengado_mora."\t".$avencer."\t".$cobrado_periodo."\t".$cobrado_mora);
			
			$temp = array();
			
			$temp['AsincronoTemporal'] = array(
					'asincrono_id' => $asinc->id,
					'clave_1' => $codigo_organismo,
					'clave_2' => $codigo_empresa,
					'clave_3' => $turno_pago,
					'texto_1' => $organismo,
					'texto_2' => $empresa,
					'texto_3' => $turno,
					'texto_4' => $proveedor,
					'decimal_1' => $devengado_periodo,
					'decimal_2' => $cobrado_periodo,
					'decimal_3' => $devengado_mora,
					'decimal_4' => $cobrado_mora,
					'decimal_5' => $avencer,
			);
			
			if($detallado == 1):
				$temp['AsincronoTemporal']['texto_5'] = $documento;
				$temp['AsincronoTemporal']['texto_6'] = $apenom;
				$temp['AsincronoTemporal']['texto_7'] = $ordenTipoAndNumero;
				$temp['AsincronoTemporal']['entero_1'] = $ordenId;
				$temp['AsincronoTemporal']['entero_2'] = $socioId;
				$temp['AsincronoTemporal']['entero_3'] = $this->getCantidadCuotasDevengadas($ordenId, $proveedor_id, $beneficioId);
				$temp['AsincronoTemporal']['entero_4'] = $this->getCantidadCuotasPagas($ordenId, $proveedor_id, $beneficioId,$periodo);
				$temp['AsincronoTemporal']['entero_5'] = $this->getCantidadCuotasVencidas($ordenId, $proveedor_id, $beneficioId,$periodo);
				$temp['AsincronoTemporal']['entero_6'] = $this->getCantidadCuotasAvencer($ordenId, $proveedor_id, $beneficioId,$periodo);
			endif;
			
//			DEBUG($temp);
//			if(!empty($temp) && ($devengado_periodo + $devengado_mora) != 0):
			if(!empty($temp)):
				
				if(!$this->Temporal->grabar($temp)){
					$STOP = 1;
					break;
				}								
			
			endif;				
			
//			$this->out("$organismo\t$empresa\t$turno\t$proveedor\t$devengado_periodo\t$cobrado_periodo\t$devengado_mora\t$cobrado_mora\t$avencer");
			
			$i++;
			
			
		endforeach;

		$asinc->actualizar($i,$total,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");		
		
		
	}
	
	
	function getTotalDevengadoPeriodo($ordenDescuentoId,$proveedor_id,$periodo,$codigo_organismo,$codigo_empresa,$turno_pago){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
//		$sql = "SELECT IFNULL(SUM(cu.importe),0) as valor 
//				FROM orden_descuento_cuotas cu
//				INNER JOIN liquidacion_cuotas AS lc ON (lc.orden_descuento_cuota_id = cu.id)
//				INNER JOIN liquidaciones lq ON (lq.id = lc.liquidacion_id) 				
//				WHERE 
//				cu.orden_descuento_id = $ordenDescuentoId
//				AND cu.proveedor_id = $proveedor_id
//				AND cu.periodo = '$periodo'
//				AND lq.periodo = '$periodo';";
		$sql = "SELECT IFNULL(SUM(cu.importe),0) as valor 
				FROM orden_descuento_cuotas cu
				WHERE 
				cu.orden_descuento_id = $ordenDescuentoId
				AND cu.proveedor_id = $proveedor_id
				AND cu.periodo = '$periodo'
				AND cu.estado <> 'B';";		
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);
	}
	
	function getTotalDevengadoMora($ordenDescuentoId,$proveedor_id,$beneficio_id,$periodo){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
//		$sql = "SELECT IFNULL(SUM(cu.importe),0) as valor 
//				FROM orden_descuento_cuotas cu
//				INNER JOIN liquidacion_cuotas AS lc ON (lc.orden_descuento_cuota_id = cu.id)
//				INNER JOIN liquidaciones lq ON (lq.id = lc.liquidacion_id) 
//				WHERE cu.orden_descuento_id = $ordenDescuentoId
//				AND cu.proveedor_id = $proveedor_id
//				AND cu.periodo < '$periodo'
//				AND lq.periodo = '$periodo';";
		$sql = "SELECT IFNULL(SUM(cu.importe),0) as valor 
				FROM orden_descuento_cuotas cu
				WHERE cu.orden_descuento_id = $ordenDescuentoId
				AND cu.proveedor_id = $proveedor_id
				AND cu.periodo < '$periodo'
				AND cu.estado <> 'B'
				AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
							INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
							WHERE 
							cocu.orden_descuento_cuota_id = cu.id
							AND co.periodo_cobro <= '$periodo');";		
//		debug($sql);
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);
	}	
	
	function getTotalAVencer($ordenDescuentoId,$proveedor_id,$beneficio_id,$periodo){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
		$sql = "SELECT IFNULL(SUM(cu.importe),0) as valor 
				FROM orden_descuento_cuotas cu WHERE cu.orden_descuento_id = $ordenDescuentoId
				AND cu.proveedor_id = $proveedor_id
				AND cu.periodo > '$periodo'
				AND cu.estado <> 'B';";
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);		
	}	
	
	
	function getTotalPagadoPeriodo($ordenDescuentoId,$proveedor_id,$beneficio_id,$periodo){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();		
//		$sql = "SELECT IFNULL(SUM(cuco.importe),0) as valor FROM 
//				orden_descuento_cobro_cuotas cuco
//				INNER JOIN orden_descuento_cobros co ON (co.id = cuco.orden_descuento_cobro_id)
//				INNER JOIN orden_descuento_cuotas AS cu ON (cu.id = cuco.orden_descuento_cuota_id)
//				INNER JOIN liquidacion_cuotas AS lc ON (lc.orden_descuento_cuota_id = cu.id)
//				INNER JOIN liquidaciones lq ON (lq.id = lc.liquidacion_id) 					
//				WHERE cu.orden_descuento_id = $ordenDescuentoId
//				AND cuco.orden_descuento_cobro_id = co.id
//				AND cu.proveedor_id = $proveedor_id
//				AND co.periodo_cobro <= '$periodo'
//				AND cu.periodo = '$periodo'
//				AND cuco.reversado = 0
//				AND lq.periodo = '$periodo';";

		
		$sql = "SELECT IFNULL(SUM(cuco.importe),0) as valor FROM 
				orden_descuento_cobro_cuotas cuco
				INNER JOIN orden_descuento_cobros co ON (co.id = cuco.orden_descuento_cobro_id)
				INNER JOIN orden_descuento_cuotas AS cu ON (cu.id = cuco.orden_descuento_cuota_id)
				WHERE cu.orden_descuento_id = $ordenDescuentoId
				AND cuco.orden_descuento_cobro_id = co.id
				AND cu.proveedor_id = $proveedor_id
				AND co.periodo_cobro <= '$periodo'
				AND cu.periodo = '$periodo'
				AND cuco.reversado = 0;";			
		
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);				
	}	

	function getTotalPagadoMora($ordenDescuentoId,$proveedor_id,$beneficio_id,$periodo){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();		
//		$sql = "SELECT IFNULL(SUM(cuco.importe),0) as valor FROM 
//				orden_descuento_cobro_cuotas cuco
//				INNER JOIN orden_descuento_cobros co ON (co.id = cuco.orden_descuento_cobro_id)
//				INNER JOIN orden_descuento_cuotas AS cu ON (cu.id = cuco.orden_descuento_cuota_id)
//				INNER JOIN liquidacion_cuotas AS lc ON (lc.orden_descuento_cuota_id = cu.id)
//				INNER JOIN liquidaciones lq ON (lq.id = lc.liquidacion_id) 				
//				WHERE cu.orden_descuento_id = $ordenDescuentoId
//				AND cuco.orden_descuento_cobro_id = co.id
//				AND cu.proveedor_id = $proveedor_id
//				AND co.periodo_cobro <= '$periodo'
//				AND cu.periodo < '$periodo'
//				AND cuco.reversado = 0
//				AND lq.periodo = '$periodo';";	
		
		$sql = "SELECT IFNULL(SUM(cuco.importe),0) as valor FROM 
				orden_descuento_cobro_cuotas cuco
				INNER JOIN orden_descuento_cobros co ON (co.id = cuco.orden_descuento_cobro_id)
				INNER JOIN orden_descuento_cuotas AS cu ON (cu.id = cuco.orden_descuento_cuota_id)
				WHERE cu.orden_descuento_id = $ordenDescuentoId
				AND cuco.orden_descuento_cobro_id = co.id
				AND cu.proveedor_id = $proveedor_id
				AND co.periodo_cobro = '$periodo'
				AND cu.periodo < '$periodo'
				AND cuco.reversado = 0;";		
		
//		debug($sql);
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);			
	}	
	
	
	function getCantidadCuotasDevengadas($ordenDescuentoId,$proveedor_id,$beneficio_id){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
		$sql = "SELECT IFNULL(COUNT(*),0) as valor 
				FROM orden_descuento_cuotas cu WHERE cu.orden_descuento_id = $ordenDescuentoId
				AND cu.estado <> 'B'
				AND cu.proveedor_id = $proveedor_id;";
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);		
	}	
	
	function getCantidadCuotasPagas($ordenDescuentoId,$proveedor_id,$beneficio_id,$periodo){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
		$sql = "SELECT IFNULL(COUNT(*),0) as valor FROM orden_descuento_cuotas cu
				WHERE cu.orden_descuento_id = $ordenDescuentoId
				AND cu.estado <> 'B'
				AND cu.periodo <= '$periodo' AND cu.proveedor_id = $proveedor_id
				AND cu.importe <= (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
									INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
									WHERE 
									cocu.orden_descuento_cuota_id = cu.id
									AND co.periodo_cobro <= '$periodo')
				GROUP BY cu.orden_descuento_id;";
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);		
	}	
	
	function getCantidadCuotasVencidas($ordenDescuentoId,$proveedor_id,$beneficio_id,$periodo){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
		$sql = "SELECT IFNULL(COUNT(*),0) as valor FROM orden_descuento_cuotas cu
				WHERE cu.orden_descuento_id = $ordenDescuentoId
				AND cu.estado <> 'B'
				AND cu.periodo <= '$periodo' AND cu.proveedor_id = $proveedor_id
				AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
									INNER JOIN orden_descuento_cobros co ON (co.id = cocu.orden_descuento_cobro_id)
									WHERE 
									cocu.orden_descuento_cuota_id = cu.id
									AND co.periodo_cobro <= '$periodo')
				GROUP BY cu.orden_descuento_id;";
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);		
	}	
	
	function getCantidadCuotasAvencer($ordenDescuentoId,$proveedor_id,$beneficio_id,$periodo){
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();
		$sql = "SELECT IFNULL(COUNT(*),0) AS valor FROM orden_descuento_cuotas cu
				WHERE cu.orden_descuento_id = $ordenDescuentoId
				AND cu.estado <> 'B'
				AND cu.periodo > '$periodo' AND cu.proveedor_id = $proveedor_id
				AND cu.importe > (SELECT IFNULL(SUM(cocu.importe),0) FROM orden_descuento_cobro_cuotas cocu
									WHERE 
									cocu.orden_descuento_cuota_id = cu.id)
				GROUP BY cu.orden_descuento_id;";
		$dato = $oORDEN->query($sql);
		return (isset($dato[0][0]['valor']) ? $dato[0][0]['valor'] : 0);		
	}	
	
}

?>
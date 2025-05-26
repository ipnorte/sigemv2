<?php

/**
*
* tmp_recupero.php
* @author adrian [* 28/02/2012]
* 
* /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php tmp_recupero 1 -app /home/adrian/dev/www/sigem/app/
* 
*/

class TmpRecuperoShell extends Shell{
	
	function main(){
		
		$liquidacion_id = 198;
		$proveedor_id = 13;
		
		$this->out("**** RECUPERO DE CARTERA *****");
		
		App::import('Model','Mutual.LiquidacionCuotaRecupero');
		$oLCR = new LiquidacionCuotaRecupero();	
		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();		
		
		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();		
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCOBROCUOTA = new OrdenDescuentoCobroCuota();	

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();	

		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();			

		$sql = "SELECT * FROM temporal.columbia_cbu_enero2012_2 
				WHERE saldo_mora_enero <> 0 AND imputado_diciembre <> 0 AND 
				imputado_diciembre < total_enero ORDER BY apenom;";
		
		
		$result = mysql_query($sql,$this->dbLinkTmp());
		while($row = mysql_fetch_assoc($result)):

//			debug($row);
			
			$socio_id = $row['socio_id'];
			$total_pago = $row['imputado_diciembre'];
			
			$this->out($socio_id . " *** " . $total_pago);
			
			$sql_c = "	SELECT 
						LiquidacionCuota.id,
						LiquidacionCuota.socio_id,
						LiquidacionCuota.persona_beneficio_id,	
						LiquidacionCuota.orden_descuento_id,
						LiquidacionCuota.orden_descuento_cuota_id,
						LiquidacionCuota.periodo_cuota,
						LiquidacionCuota.saldo_actual
						FROM liquidacion_cuotas AS LiquidacionCuota
						WHERE LiquidacionCuota.liquidacion_id = $liquidacion_id
						AND LiquidacionCuota.proveedor_id = $proveedor_id
						AND LiquidacionCuota.imputada = 0
						AND LiquidacionCuota.socio_id = $socio_id;";
			$cuotas = $oLCR->query($sql_c);

			
			$cobro = array('OrdenDescuentoCobro' => array(
					'id' => 0,
					'socio_id' => $socio_id,
					'tipo_cobro' => 'MUTUTCOBRECU',
					'fecha' => '2012-02-22',
					'periodo_cobro' => '201201',
					'importe' => $total_pago,
					'recibo_id' => 0
			));
			
			$cobro['OrdenDescuentoCobroCuota'] = array();
			
			$cuotasImpu = $oLC->distribuyeImporteCuotas($cuotas, $total_pago);
//			debug($cuotasImpu);
//			exit;
			
			foreach($cuotasImpu as $idx => $cuota){
				
//				debug($cuota);
				$cuota['LiquidacionCuota'] = $cuota;
				
				$cobro['OrdenDescuentoCobroCuota'][$idx] = array(
					'periodo_cobro' => '201201',
					'orden_descuento_cuota_id' => $cuota['LiquidacionCuota']['orden_descuento_cuota_id'],
					'importe' => $cuota['LiquidacionCuota']['importe_debitado'],
					'proveedor_id' => 13,
					'alicuota_comision_cobranza' => 2,
					'comision_cobranza' => round($cuota['LiquidacionCuota']['importe_debitado'] * 2 /100,2),
				);
				
			}
			
			debug($cobro);
//			exit;
			###########################################################################################
			#GRABO EL COBRO
			###########################################################################################
//			$oCOBRO->saveAll($cobro);

			$cobroID = $oCOBRO->getLastInsertID();

			foreach($cuotasImpu as $idx => $cuota){
				
//				$cuota['LiquidacionCuota'] = $cuota;
				
				$tcuota = array();
				$tcuota['LiquidacionCuota'] = array();
				$tcuota['LiquidacionCuota']['id'] = $cuota['id'];
				$tcuota['LiquidacionCuota']['importe_debitado'] = $cuota['importe_debitado'];
				$tcuota['LiquidacionCuota']['para_imputar'] = 1;
				$tcuota['LiquidacionCuota']['imputada'] = 1;
				$tcuota['LiquidacionCuota']['orden_descuento_cobro_id'] = $cobroID;
				$tcuota['LiquidacionCuota']['alicuota_comision_cobranza'] = 2;
				$tcuota['LiquidacionCuota']['comision_cobranza'] = round($cuota['importe_debitado'] * 2 /100,2);			
				
				###########################################################################################
				# GRABO LA LIQUIDACION CUOTAS CON EL DATO DEL COBRO
				###########################################################################################
				debug($tcuota);
//				$oLC->save($tcuota);
				
				###########################################################################################
				# MARCO LA CUOTA COMO PAGADA
				###########################################################################################
//				$oCUOTA->marcarPagada($cuota['orden_descuento_cuota_id']);
				
				###########################################################################################
				# GENERO LA CUOTA COMO RECUPERO
				###########################################################################################
				$cuotaRecupero['LiquidacionCuotaRecupero'] = array(
									'id' => 0,
									'orden_descuento_cobro_id' => $cobroID,
									'liquidacion_id' => $liquidacion_id,
									'liquidacion_cuota_id' => $cuota['id'],
									'orden_descuento_cuota_id' => $cuota['orden_descuento_cuota_id'],
									'socio_id' => $socio_id,
									'importe_liquidado' => $cuota['importe_debitado'],
									'saldo_actual' => $cuota['importe_debitado'],
									'proveedor_id' => $proveedor_id,
									'orden_descuento_id' => 0,
									'periodo_socio' => '201202',
									'periodo_proveedor' => '201201',
									'alicuota_comision_cobranza' => 2,
									'comision_cobranza' => round($cuota['importe_debitado'] * 2 /100,2),
									'proveedor_factura_id' => 0,
									'cliente_factura_id' => 0,
				);
				
				debug($cuotaRecupero);
//				$oLCR->save($cuotaRecupero);
				$cuotaRecuperoId = $oLCR->getLastInsertID();
				
				
				###########################################################################################
				# GENERO LA ORDEN DE DESCUENTO DE RECUPERO
				###########################################################################################
				
				$ordenDto = array();
				$ordenDto['OrdenDescuento'] = array(
								'fecha' => $oORDEN->getDiaHabil('201202'),
								'tipo_orden_dto' => 'RECAR',
								'numero' => $cuotaRecuperoId,
								'tipo_producto' => 'MUTUPROD0020',
								'socio_id' => $socio_id,
								'persona_beneficio_id' => $cuota['persona_beneficio_id'],
								'proveedor_id' => 18,
								'periodo_ini' => '201202',
								'importe_cuota' => $cuota['importe_debitado'] / 3,
								'importe_total' => $cuota['importe_debitado'],
								'primer_vto_socio' => $oORDEN->getDiaHabil('201202'),
								'primer_vto_proveedor' => $oORDEN->getDiaHabil('201202'),
								'cuotas' => 3,
								'observaciones' => "** RECUPERO CUOTA *** ENERO 2012  ACTIVOS CBU (BANCO COLUMBIA)",
							);				
				$ordenDto['OrdenDescuentoCuota'] = $oCUOTA->armaCuotas($ordenDto);					
	
				DEBUG($ordenDto);	
//				$oORDEN->saveAll($ordenDto);
				
				$cuotaRecupero['LiquidacionCuotaRecupero']['id'] = $cuotaRecuperoId;
				$cuotaRecupero['LiquidacionCuotaRecupero']['orden_descuento_id'] = $oORDEN->getLastInsertID();
//				$oLCR->save($cuotaRecupero);
				
			}
		
		endwhile;
		

		
		
	}
	
	
	function dbLinkTmp(){
		$db = new DATABASE_CONFIG();
		$link = mysql_connect($db->tmp['host'],$db->tmp['login'],$db->tmp['password'])
		or die ("No se establecio conexion a la base de datos");
		mysql_select_db ($db->tmp['database'],$link);
		return $link;
	}	
	
}

?>
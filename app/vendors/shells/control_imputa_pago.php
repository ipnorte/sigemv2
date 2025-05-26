<?php
/**
 * 
 * @author ADRIAN TORRES
 *
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php control_imputa_pago 88 -app /home/adrian/dev/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php control_imputa_pago 97 -app /var/www/sigem/app/
 *
 */
class ControlImputaPagoShell extends Shell{
	
	var $liquidacion_id = 0;
	
	var $uses = array(
						'Mutual.OrdenDescuentoCobro',
						'Mutual.Liquidacion',
						'Mutual.LiquidacionCuota',
						'Pfyj.Socio'
	);
	
	function main(){
		
		$this->liquidacion_id = $this->args[0];
		$liquidacion =  $this->Liquidacion->cargar($this->liquidacion_id);
		$periodo = $liquidacion['Liquidacion']['periodo'];
		$organismo = substr($liquidacion['Liquidacion']['codigo_organismo'],8,4);
		
		$fileName = WWW_ROOT."files".DS."controles".DS."control_liquidacion_" . $this->liquidacion_id ."_".$periodo."_".$organismo.".log";
		
//		$this->out($fileName);

		if(file_exists($fileName)) unlink($fileName);
		
		
		
//		DEBUG($liquidacion);
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCC = new OrdenDescuentoCobroCuota();
		
		$socios = $this->getSociosCuotasCobros();
		$ACUM = 0;
		$AC_IMP = 0;
		$AC_COB = 0;
		
		$w = 144;
		$c1 = 60;
		$c2 = $c3 = $c4 = $c5 = 20;
		
		
		$str = str_pad("",$w,"*",STR_PAD_RIGHT);
		$str .= "\n";
		$str .= str_pad("CONTROL IMPUTACION LIQUIDACION (DIFERENCIA ENTRE IMPUTADO Y COBRADO)",$w," ",STR_PAD_RIGHT);
		$str .= "\n";
		$str .= str_pad("ORGANISMO: ".$liquidacion['Liquidacion']['organismo']." PERIODO: " . $liquidacion['Liquidacion']['periodo_desc'] ,$w," ",STR_PAD_RIGHT);
		$str .= "\n";
		$str .= str_pad("",$w,"*",STR_PAD_RIGHT);
		$str .= "\n";
		$str .= str_pad("",$w,"-",STR_PAD_RIGHT);
		$str .= "\n";
		$str .= str_pad("| SOCIO",$c1," ",STR_PAD_RIGHT) . str_pad("IMPUTADO",$c2," ",STR_PAD_LEFT);
		$str .= str_pad("COBRADO",$c3," ",STR_PAD_LEFT) . str_pad("DIFERENCIA",$c4," ",STR_PAD_LEFT). str_pad("ACUMULADO",$c5," ",STR_PAD_LEFT);
		$str .= "\n";
		$str .= str_pad("",$w,"-",STR_PAD_RIGHT);
		$str .= "\n";
		
		error_log($str,3,$fileName);
		
		$this->out($str,false);
		
	
		$i = 1;
		
		foreach($socios as $socio):
			
			$imputado = $socio[0]['importe_debitado'];
			
			$cobrado = $oCC->getMontoPagoByOrdenCobro($socio['LiquidacionCuota']['orden_descuento_cobro_id']);
			$cobro = $this->OrdenDescuentoCobro->getCobro($socio['LiquidacionCuota']['orden_descuento_cobro_id']);
			
			if($imputado != $cobrado){
				
				$ACUM += $cobrado - $imputado;
				$AC_IMP += $imputado;
				$AC_COB += $cobrado;				
				
				$col0 = str_pad($i,4," ",STR_PAD_RIGHT);
				$col1 = str_pad($cobro['Socio']['str'],$c1," ",STR_PAD_RIGHT);
				$col2 = str_pad(number_format($imputado,2),$c2," ",STR_PAD_LEFT);
				$col3 = str_pad(number_format($cobrado,2),$c3," ",STR_PAD_LEFT);
				$col4 = str_pad(number_format($cobrado - $imputado,2),$c4," ",STR_PAD_LEFT);
				$col5 = str_pad(number_format($ACUM,2),$c5," ",STR_PAD_LEFT);
				
				$str = $col0.$col1 .$col2.$col3.$col4.$col5."\n";
				
				error_log($str,3,$fileName);
				
				$this->out($col0.$col1 .$col2.$col3.$col4.$col5);
				
				$i++;
				
			}
		
		endforeach;
		$str = str_pad("",$w,"-",STR_PAD_RIGHT);
		$str .= "\n";	
		$str .= str_pad("TOTAL",$c1," ",STR_PAD_RIGHT) . str_pad(number_format($AC_IMP,2),$c2," ",STR_PAD_LEFT);
		$str .= str_pad(number_format($AC_COB,2),$c3," ",STR_PAD_LEFT) . str_pad(number_format($AC_COB - $AC_IMP,2),$c4," ",STR_PAD_LEFT) . str_pad(number_format($ACUM,2),$c5," ",STR_PAD_LEFT);
		$str .= "\n";
		$this->out($str,false);
		error_log($str,3,$fileName);			
		
	}
	
	
	function getSociosCuotasCobros(){
		$conditions = array();
		$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacion_id;
		$conditions['LiquidacionCuota.imputada'] = 1;
		
		$socios = $this->LiquidacionCuota->find('all',array(
																	'joins'	=> array(
		
																		array(
																			'table' => 'socios',
																			'alias' => 'Socio',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('LiquidacionCuota.socio_id = Socio.id')
																			),			
																		array(
																			'table' => 'personas',
																			'alias' => 'Persona',
																			'type' => 'inner',
																			'foreignKey' => false,
																			'conditions' => array('Socio.persona_id = Persona.id')
																			),								
//																		array(
//																			'table' => 'global_datos',
//																			'alias' => 'GlobalDato',
//																			'type' => 'inner',
//																			'foreignKey' => false,
//																			'conditions' => array('GlobalDato.id = Persona.tipo_documento')
//																			),
//																		array(
//																			'table' => 'orden_descuentos',
//																			'alias' => 'OrdenDescuento',
//																			'type' => 'inner',
//																			'foreignKey' => false,
//																			'conditions' => array('OrdenDescuento.id = LiquidacionCuota.orden_descuento_id')
//																			),	
//																		array(
//																			'table' => 'orden_descuento_cuotas',
//																			'alias' => 'OrdenDescuentoCuota',
//																			'type' => 'left',
//																			'foreignKey' => false,
//																			'conditions' => array('OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id')
//																			),																																																																											
																	),															
																	'conditions' => $conditions,
																	'fields' => array(
																						'LiquidacionCuota.socio_id,
																						LiquidacionCuota.orden_descuento_cobro_id,
																						sum(importe_debitado) as importe_debitado'	
																						
																					),
																	'group' => array('LiquidacionCuota.socio_id, LiquidacionCuota.orden_descuento_cobro_id'),				
																	'order' => array('Persona.apellido,Persona.nombre'
																					)															
		));	

		return $socios;
		
	}
	
}

?>
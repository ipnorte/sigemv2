<?php
/**
 * PROCESO PARA CORRECCION DE ALTAS DE SOCIOS
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * LANZADOR
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php tmp_alta_socios -app /home/adrian/dev/www/sigem/app/
 *
 */

class TmpAltaSociosShell extends Shell{


	function main(){
		
//		return null;
		
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();

		App::import('Model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();

		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCCUOTA = new OrdenDescuentoCobroCuota();		

		$sql = "select * from temporal.correccion_altas";
		
		$result = mysql_query($sql,$this->dbLinkTmp());
		while($row = mysql_fetch_assoc($result)):
		
			
			$oORDEN->unbindModel(array('hasMany' => array('OrdenDescuentoCuota'),'belongsTo' => array('Proveedor','Socio')));
			$orden = $oORDEN->read(null,$row['orden_descuento_id']);
			
			debug($row);
//			debug($orden);
			
			//genero la matriz de periodos a controlar desde el inicio al actual (sep2011)
			$periodos = $this->getMatrizPeriodos($row['fecha']);
			
			$valoresCS = array();
			
			if(!empty($periodos)):
			
				foreach($periodos as $periodo => $vto):
			
//					debug($periodos);
					
					$cuota = $oCUOTA->getCuotaSocialByOrdenIdByPeriodo($row['orden_descuento_id'],$periodo);
					
					if(empty($cuota)){
						
						//GENERO LA CUOTA
						$importe = 13;
						$proveedor = 18;
						
						$cs = array('OrdenDescuentoCuota' => array(
										'orden_descuento_id' => $row['orden_descuento_id'],
										'persona_beneficio_id' => $row['persona_beneficio_id'],
										'socio_id' => $row['socio_id'],
										'tipo_orden_dto' => "CMUTU",
										'tipo_producto' => "MUTUPROD0003",
										'periodo' => $periodo,
										'nro_cuota' =>  0,
										'tipo_cuota' => "MUTUTCUOCSOC",
										'estado' => 'P',	
										'situacion' => 'MUTUSICUMUTU',
										'importe' => $importe,
										'proveedor_id' => $proveedor,
										'vencimiento' => $vto,
										'vencimiento_proveedor' => $vto,
										'nro_referencia_proveedor' => $row['socio_id']
									));			
						$oCUOTA->id = 0;
						
						$oCUOTA->save($cs);
						$this->out("$periodo *** CUOTA SOCIAL GENERADA ***");
						
						$cuota_id = $oCUOTA->getLastInsertID();
						
						//GENERO EL PAGO DE LA CUOTA
						$cobro = array();
						$cobro['OrdenDescuentoCobro']['id'] = 0;
						$cobro['OrdenDescuentoCobro']['tipo_cobro'] = "MUTUTCOBTRAN";
						$cobro['OrdenDescuentoCobro']['fecha'] = $vto;
						$cobro['OrdenDescuentoCobro']['importe'] = $importe;
						$cobro['OrdenDescuentoCobro']['periodo_cobro'] = $periodo;
						$cobro['OrdenDescuentoCobro']['socio_id'] = $row['socio_id'];
						
						$oCOBRO->save($cobro);
						
						$cobro_id = $oCOBRO->getLastInsertID();
						
						$cobroCuota = array();
						$cobroCuota['OrdenDescuentoCobroCuota']['id'] = 0;
						$cobroCuota['OrdenDescuentoCobroCuota']['periodo_cobro'] = $periodo;
						$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cobro_id'] = $cobro_id;
						$cobroCuota['OrdenDescuentoCobroCuota']['orden_descuento_cuota_id'] = $cuota_id;
						$cobroCuota['OrdenDescuentoCobroCuota']['importe'] = $importe;
						$cobroCuota['OrdenDescuentoCobroCuota']['proveedor_id'] = $proveedor;
						
						$oCCUOTA->save($cobroCuota);
						
						$this->out("$periodo *** CUOTA SOCIAL COBRADA ***");

					}else{
						
						if($cuota['OrdenDescuentoCuota']['estado'] == 'A'){
							$this->out("$periodo *** CUOTA ADEUDADA ***");
						}
						
					}
			
				endforeach;
			
			
			endif;
			
			
			
			
		
		endwhile;
		
		

		
	}
	
	
	function getMatrizPeriodos($fechaInicio){
		
		App::import('Model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();

		$periodos = array();
		$periodoTope = '201109';
		
		$i = 0;
		$periodoInicio = date('Ym',strtotime($fechaInicio));
		$mkTimeIni = mktime(0,0,0,date('m',strtotime($fechaInicio)),date('d',strtotime($fechaInicio)),date('Y',strtotime($fechaInicio)));
		
		$periodoCalculado = $periodoInicio;
		$fechaCalculada = $fechaInicio;
		
		while($periodoCalculado < $periodoTope):
			$mkt = $oORDEN->addMonthToDate($mkTimeIni,$i);
			$periodoCalculado = date('Ym',$mkt);
			$periodos[$periodoCalculado] = date('Y-m-d',$mkt);
			$i++;
		endwhile;
		
		return $periodos;
		
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
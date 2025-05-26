<?php
/**
 * @deprecated
 * Se utiliza el modelo LiquidacionSocioRendicion
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class LiquidacionIntercambioRegistroProcesado extends MutualAppModel{
	
	var $name = 'LiquidacionIntercambioRegistroProcesado';
	
	
	function totalNoEncontrados($liquidacion_id){
		$registros = $this->noEncontradoEnLiquidacion($liquidacion_id);
		$cantidad = 0;
		$total = 0;
		foreach($registros as $registro){
			$cantidad += 1;
			$total += $registro['LiquidacionIntercambioRegistroProcesado']['importe_debitado'];
		}
		$total = array('cantidad' => $cantidad, 'total' => $total);
		return $total;		
	}
	
	function noEncontradoEnLiquidacion($liquidacion_id,$armarDatos=false){
		$sql = "SELECT 
					`LiquidacionIntercambioRegistroProcesado`.`id`, 
					`LiquidacionIntercambioRegistroProcesado`.`liquidacion_id`, 
					`LiquidacionIntercambioRegistroProcesado`.`codigo_organismo`, 
					`LiquidacionIntercambioRegistroProcesado`.`nro_ley`,
					`LiquidacionIntercambioRegistroProcesado`.`tipo`,
					`LiquidacionIntercambioRegistroProcesado`.`nro_beneficio`, 
					`LiquidacionIntercambioRegistroProcesado`.`sub_beneficio`,
					`LiquidacionIntercambioRegistroProcesado`.`codigo_dto`, 
					`LiquidacionIntercambioRegistroProcesado`.`sub_codigo`, 
					`LiquidacionIntercambioRegistroProcesado`.`cbu`, 
					`LiquidacionIntercambioRegistroProcesado`.`banco_intercambio`, 
					`LiquidacionIntercambioRegistroProcesado`.`documento`, 
					`LiquidacionIntercambioRegistroProcesado`.`importe_debitado`, 
					`LiquidacionIntercambioRegistroProcesado`.`status`, 
					`LiquidacionIntercambioRegistroProcesado`.`fecha_pago`, 
					`LiquidacionIntercambioRegistroProcesado`.`liquidacion_socio_id`,
					`Persona`.`apellido`,
					`Persona`.`nombre`,
					`Banco`.`nombre`,
					`BancoRendicionCodigo`.`descripcion`,
					`BancoRendicionCodigo`.`indica_pago`
				FROM 
					`liquidacion_intercambio_registro_procesados` AS `LiquidacionIntercambioRegistroProcesado` 
				LEFT JOIN `personas` as `Persona` on  (`LiquidacionIntercambioRegistroProcesado`.`documento` = `Persona`.`documento`)
				LEFT JOIN `bancos` as `Banco` on  (`LiquidacionIntercambioRegistroProcesado`.`banco_intercambio` = `Banco`.`id`)
				LEFT JOIN `banco_rendicion_codigos` as `BancoRendicionCodigo` on  (`LiquidacionIntercambioRegistroProcesado`.`banco_intercambio` = `BancoRendicionCodigo`.`banco_id` and `LiquidacionIntercambioRegistroProcesado`.`status` = `BancoRendicionCodigo`.`codigo`)
				WHERE 
					`LiquidacionIntercambioRegistroProcesado`.`liquidacion_id` = $liquidacion_id 
					AND `LiquidacionIntercambioRegistroProcesado`.`liquidacion_socio_id` = 0
				order by `LiquidacionIntercambioRegistroProcesado`.`status`,`Persona`.`apellido`,`Persona`.`nombre`";
		$registros = $this->query($sql);
		foreach($registros as $idx => $registro){
			$tipoOrganismo = parent::GlobalDato('concepto_2',$registro['LiquidacionIntercambioRegistroProcesado']['codigo_organismo']);
			switch ($tipoOrganismo){
				case 'AC':
					$registros[$idx] = $this->__armaStrCBU($registro);	
					break;
				case 'JP':
					$registros[$idx] = $this->__armaStrCJP($registro);	
					break;
				case 'JN':
					$registros[$idx] = $this->__armaStrJN($registro);	
					break;								
			}			
			
		}
		return $registros;	
	}
	
	
	function __armaStrCBU($dato){
		$dato['LiquidacionIntercambioRegistroProcesado']['beneficio_str'] = $dato['Banco']['nombre']. '|'. $dato['LiquidacionIntercambioRegistroProcesado']['cbu'];
		return $dato;
	}
	

	function __armaStrCJP($dato){
		$ley = $dato['LiquidacionIntercambioRegistroProcesado']['nro_ley'];
		$nroBeneficio = $dato['LiquidacionIntercambioRegistroProcesado']['nro_beneficio'];
		$tipo = $dato['LiquidacionIntercambioRegistroProcesado']['tipo'];
		$subBeneficio = $dato['LiquidacionIntercambioRegistroProcesado']['sub_beneficio'];
		
		$string = "LEY:$ley|TIPO:$tipo|BENFICIO:$nroBeneficio|SUB-BENEFICIO:$subBeneficio";
		
		$codigo = $dato['LiquidacionIntercambioRegistroProcesado']['codigo_dto'];
		$subCodigo = $dato['LiquidacionIntercambioRegistroProcesado']['sub_codigo'];		
		$string .= "|CODIGO: $codigo-$subCodigo";
		$dato['LiquidacionIntercambioRegistroProcesado']['beneficio_str'] = $string;
		return $dato;		
	}

	function __armaStrJN($dato){
		$codigo = $dato['LiquidacionIntercambioRegistroProcesado']['codigo_dto'];	
		$dato['LiquidacionIntercambioRegistroProcesado']['beneficio_str'] = "BENFICIO: ".$dato['LiquidacionIntercambioRegistroProcesado']['nro_beneficio'] . " | CODIGO: $codigo";
		return $dato;		
	}

	
	function getTotalCobrado($liquidacionSocio){
		$conditions = $this->buildConditions($liquidacionSocio);
		if(empty($conditions)) return 0;
		$conditions['LiquidacionIntercambioRegistroProcesado.indica_pago'] = 1;
		$registro = $this->find('all',array('conditions' => $conditions,'fields' => array("SUM(LiquidacionIntercambioRegistroProcesado.importe_debitado) as importe_debitado")));
		return (isset($registro[0][0]['importe_debitado']) ? $registro[0][0]['importe_debitado'] : 0);
	}
	
	
	function getDatosIntercambio($liquidacionSocio,$indica_pago=1){
		$resultado = array();
		$conditions = $this->buildConditions($liquidacionSocio);
		
		if(empty($conditions)) return 0;
		$conditions['LiquidacionIntercambioRegistroProcesado.indica_pago'] = $indica_pago;
		
		$registro = $this->find('all',array('conditions' => $conditions));
		$resultado['id'] = (isset($registro[0]['LiquidacionIntercambioRegistroProcesado']['id']) ? $registro[0]['LiquidacionIntercambioRegistroProcesado']['id'] : 0);
		$resultado['indica_pago'] = (isset($registro[0]['LiquidacionIntercambioRegistroProcesado']['indica_pago']) ? $registro[0]['LiquidacionIntercambioRegistroProcesado']['indica_pago'] : 0);
		$resultado['status'] = (isset($registro[0]['LiquidacionIntercambioRegistroProcesado']['status']) ? $registro[0]['LiquidacionIntercambioRegistroProcesado']['status'] : NULL);
		$resultado['fecha_pago'] = (isset($registro[0]['LiquidacionIntercambioRegistroProcesado']['fecha_pago']) ? $registro[0]['LiquidacionIntercambioRegistroProcesado']['fecha_pago'] : NULL);
		$resultado['banco_intercambio'] = (isset($registro[0]['LiquidacionIntercambioRegistroProcesado']['banco_intercambio']) ? $registro[0]['LiquidacionIntercambioRegistroProcesado']['banco_intercambio'] : "99999");
		
		return $resultado;
	}

	function buildConditions($liquidacionSocio){
		$conditions = array();
		$organismo = $liquidacionSocio['LiquidacionSocio']['codigo_organismo'];
		$conditions['LiquidacionIntercambioRegistroProcesado.liquidacion_id'] = $liquidacionSocio['LiquidacionSocio']['liquidacion_id'];
		switch ($organismo){
			case "MUTUCORG2201":
				$conditions['LiquidacionIntercambioRegistroProcesado.documento'] = $liquidacionSocio['LiquidacionSocio']['documento'];
				break;
			case "MUTUCORG6601":
				$conditions['LiquidacionIntercambioRegistroProcesado.nro_beneficio'] = $liquidacionSocio['LiquidacionSocio']['nro_beneficio'];
				$conditions['LiquidacionIntercambioRegistroProcesado.codigo_dto'] = $liquidacionSocio['LiquidacionSocio']['codigo_dto'];
				$conditions['LiquidacionIntercambioRegistroProcesado.sub_codigo'] = $liquidacionSocio['LiquidacionSocio']['sub_codigo'];
				break;
			case "MUTUCORG7701":
				$conditions['LiquidacionIntercambioRegistroProcesado.nro_ley'] = $liquidacionSocio['LiquidacionSocio']['nro_ley'];
				$conditions['LiquidacionIntercambioRegistroProcesado.tipo'] = $liquidacionSocio['LiquidacionSocio']['tipo'];
				$conditions['LiquidacionIntercambioRegistroProcesado.nro_beneficio'] = $liquidacionSocio['LiquidacionSocio']['nro_beneficio'];
				$conditions['LiquidacionIntercambioRegistroProcesado.sub_beneficio'] = $liquidacionSocio['LiquidacionSocio']['sub_beneficio'];
				$conditions['LiquidacionIntercambioRegistroProcesado.codigo_dto'] = $liquidacionSocio['LiquidacionSocio']['codigo_dto'];
				$conditions['LiquidacionIntercambioRegistroProcesado.sub_codigo'] = $liquidacionSocio['LiquidacionSocio']['sub_codigo'];
				break;
			default:
				return null;
				break;	
		}
		return $conditions;		
	}
	
}
?>
<?php
/**
 * Liquidacion Socio Rendicion
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model 
 *
 */
class LiquidacionSocioRendicion extends MutualAppModel{
	
	var $name = 'LiquidacionSocioRendicion';
	
	/**
	 * devuelve el total cobrado para un socio y para una liquidacion
	 * @param $socio_id
	 * @param $liquidacion_id
	 * @return unknown_type
	 */
	function getTotalCobrado($socio_id,$liquidacion_id){
		return $this->getTotalBySocioByLiquidacion($socio_id,$liquidacion_id,1);
	}
	
	/**
	 * Get Total por Socio por Liquidacion
	 * Devuelve el total cobrado o no cobrado de un socio para una liquidacion
	 * Si se para el parametro $proveedor_id busca las rendiciones de archivos vinculados a un proveedor
	 * @param $socio_id
	 * @param $liquidacion_id
	 * @param $indicaPago
	 * @param $proveedor_id
	 * @return decimal
	 */
	function getTotalBySocioByLiquidacion($socio_id,$liquidacion_id,$indicaPago = 1, $proveedor_id = null){
		$total = 0;
		
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocioRendicion.socio_id'] = $socio_id;
		$conditions['LiquidacionSocioRendicion.indica_pago'] = $indicaPago;
		if(!empty($proveedor_id)) $conditions['LiquidacionSocioRendicion.proveedor_id'] = $proveedor_id;
		$conditions['LiquidacionSocioRendicion.proveedor_id'] = (empty($proveedor_id) ? 0 : $proveedor_id);
		$total = $this->find('all',array(
												'conditions' => $conditions,
												'fields' => array('sum(importe_debitado) as importe_debitado'),
											)
								);
		return (isset($total[0][0]['importe_debitado']) ? $total[0][0]['importe_debitado'] : 0);		
	}
	
	/**
	 * Devuelve el ID del cobro
	 * @param unknown_type $socio_id
	 * @param unknown_type $liquidacion_id
	 * @return number
	 */
	function getOrdenCobroID($socio_id,$liquidacion_id){
		$orden = $this->find('all',array(
												'conditions' => 
													array(
															'LiquidacionSocioRendicion.liquidacion_id' => $liquidacion_id,
															'LiquidacionSocioRendicion.socio_id' => $socio_id, 
															'LiquidacionSocioRendicion.indica_pago' => 1,
															'LiquidacionSocioRendicion.orden_descuento_cobro_id <>' => 0
													),
												'fields' => array('LiquidacionSocioRendicion.orden_descuento_cobro_id'),
												'group' => array('LiquidacionSocioRendicion.orden_descuento_cobro_id')		
											)
								);
		return (isset($orden[0]['LiquidacionSocioRendicion']['orden_descuento_cobro_id']) ? $orden[0]['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] : 0);								
	}
	
	
	/**
	 * Get Total por Socio por Periodo
	 * @param $socio_id
	 * @param $periodo
	 * @param $indicaPago
	 * @return unknown_type
	 */
	function getTotalBySocioByPeriodo($socio_id,$periodo,$indicaPago=1){
		$total = 0;
		$total = $this->find('all',array(
												'conditions' => 
													array(
															'LiquidacionSocioRendicion.socio_id' => $socio_id, 
															'LiquidacionSocioRendicion.periodo' => $periodo,
															'LiquidacionSocioRendicion.indica_pago' => $indicaPago
													),
												'fields' => array('sum(importe_debitado) as importe_debitado'),
											)
								);
		return (isset($total[0][0]['importe_debitado']) ? $total[0][0]['importe_debitado'] : 0);		
	}	
	
	/**
	 * Get Total Por Liquidacion
	 * Devuelve el total cobrado o no cobrado para una liquidacion dada
	 * @param integer $liquidacion_id
	 * @param integer $indicaPago
	 * @return float
	 */
	function getTotalByLiquidacion($liquidacion_id,$indicaPago=1,$encontrados=1){
		$total = 0;
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocioRendicion.indica_pago'] = $indicaPago;
		if($encontrados == 1)$conditions['LiquidacionSocioRendicion.socio_id <>'] = 0;
		else if($encontrados == 0)$conditions['LiquidacionSocioRendicion.socio_id'] = 0;		
		$total = $this->find('all',array(
												'conditions' => $conditions,
												'fields' => array('sum(importe_debitado) as importe_debitado'),
											)
								);
		return (isset($total[0][0]['importe_debitado']) ? $total[0][0]['importe_debitado'] : 0);		
	}
	
	/**
	 * Get Cantidad de Registros Recibidos
	 * @param unknown_type $liquidacion_id
	 * @return unknown_type
	 */
	function getCantidadRegistrosRecibidos($liquidacion_id){
		$cantidad = $this->find('count',array('conditions' => array('LiquidacionSocioRendicion.liquidacion_id' => $liquidacion_id)));
		return $cantidad;
	}
	
	/**
	 * Devuelve los registros de rendicion de un socio para un periodo indicado
	 * @param $socio_id
	 * @param $periodo
	 * @return unknown_type
	 */
	function getBySocioPeriodo($socio_id,$periodo, $codigoOrganismo = null){
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.socio_id'] = $socio_id;
		$conditions['LiquidacionSocioRendicion.periodo'] = $periodo;
		if(!empty($codigoOrganismo)) $conditions['LiquidacionSocioRendicion.codigo_organismo'] = $codigoOrganismo;
		
		$registros = $this->find('all',array('conditions' => $conditions));
		foreach($registros as $idx => $registro):
			$registros[$idx] = $this->armaDatos($registro);
		endforeach;
		return $registros;
	}
	
	function getBySocioByLiquidacion($socio_id,$liquidacion_id){
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocioRendicion.socio_id'] = $socio_id;
		$registros = $this->find('all',array('conditions' => $conditions));
		foreach($registros as $idx => $registro):
			$registros[$idx] = $this->armaDatos($registro);
		endforeach;
		return $registros;
	}

	
	function getUltimaFechaDebito($socio_id,$liquidacion_id){
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocioRendicion.socio_id'] = $socio_id;
		$registros = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionSocioRendicion.fecha_debito DESC'), 'limit' => 1));
		if(!empty($registros[0]) && isset($registros[0]['fecha_debito'])) return $registros[0]['fecha_debito'];
		else return null;
	}
	
	
	function getByArchivoIntercambioID($archivo_id){

		$joinPersona = "LEFT JOIN personas as Persona on  (LiquidacionSocioRendicion.documento = Persona.documento)";
		$joinSocioPersona = "INNER JOIN socios as Socio on (LiquidacionSocioRendicion.socio_id = Socio.id) ";
		$joinSocioPersona .= "INNER JOIN personas as Persona on (Socio.persona_id = Persona.id)";
		
		$sql = "SELECT 
					`LiquidacionSocioRendicion`.`id`, 
					`LiquidacionSocioRendicion`.`liquidacion_id`, 
					`LiquidacionSocioRendicion`.`codigo_organismo`, 
					`LiquidacionSocioRendicion`.`socio_id`, 
					`LiquidacionSocioRendicion`.`nro_ley`,
					`LiquidacionSocioRendicion`.`tipo`,
					`LiquidacionSocioRendicion`.`nro_beneficio`, 
					`LiquidacionSocioRendicion`.`sub_beneficio`,
					`LiquidacionSocioRendicion`.`codigo_dto`, 
					`LiquidacionSocioRendicion`.`sub_codigo`, 
					`LiquidacionSocioRendicion`.`cbu`, 
					`LiquidacionSocioRendicion`.`sucursal`,
					`LiquidacionSocioRendicion`.`tipo_cta_bco`, 
					`LiquidacionSocioRendicion`.`nro_cta_bco`,   
					`LiquidacionSocioRendicion`.`banco_intercambio`, 
					`LiquidacionSocioRendicion`.`documento`, 
					`LiquidacionSocioRendicion`.`importe_debitado`, 
					`LiquidacionSocioRendicion`.`status`, 
					`LiquidacionSocioRendicion`.`fecha_debito`, 
					`Persona`.`tipo_documento`,
					`Persona`.`documento`,
					`Persona`.`apellido`,
					`Persona`.`nombre`,
					`Banco`.`nombre`,
					`BancoRendicionCodigo`.`descripcion`,
					`BancoRendicionCodigo`.`indica_pago`
				FROM 
					`liquidacion_socio_rendiciones` AS `LiquidacionSocioRendicion` 
				INNER JOIN socios as Socio on (LiquidacionSocioRendicion.socio_id = Socio.id)
				INNER JOIN personas as Persona on (Socio.persona_id = Persona.id)
				LEFT JOIN `bancos` as `Banco` on  (`LiquidacionSocioRendicion`.`banco_intercambio` = `Banco`.`id`)
				LEFT JOIN `banco_rendicion_codigos` as `BancoRendicionCodigo` on  (`LiquidacionSocioRendicion`.`banco_intercambio` = `BancoRendicionCodigo`.`banco_id` and `LiquidacionSocioRendicion`.`status` = `BancoRendicionCodigo`.`codigo`)
				WHERE 
					`LiquidacionSocioRendicion`.`liquidacion_intercambio_id` = $archivo_id 
				order by `Persona`.`apellido`,`Persona`.`nombre`";
		$registros = $this->query($sql);
		if(empty($registros)) return null;
		
		App::import('Model', 'Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
		
				
		foreach($registros as $idx => $registro):
		
			$registro = $this->armaDatos($registro);
			
			$registro['LiquidacionSocioRendicion']['descripcion'] = "";
			$registro['LiquidacionSocioRendicion']['empresa'] = "";
			$registro['LiquidacionSocioRendicion']['turno_pago'] = "";
			
			$liqSoc = $oLS->getLiquidacionBySocio($registro['LiquidacionSocioRendicion']['socio_id'],$registro['LiquidacionSocioRendicion']['liquidacion_id']);
			
			if(isset($liqSoc[0])):
			
				$liqSoc = $liqSoc[0];
				
				$registro['LiquidacionSocioRendicion']['descripcion'] = $oTURNO->getDescripcionByTruno($liqSoc['LiquidacionSocio']['turno_pago']);
				$registro['LiquidacionSocioRendicion']['empresa'] = parent::GlobalDato('concepto_1',$liqSoc['LiquidacionSocio']['codigo_empresa']);
				$registro['LiquidacionSocioRendicion']['turno_pago'] = $liqSoc['LiquidacionSocio']['turno_pago'];
				if( $liqSoc['LiquidacionSocio']['codigo_empresa'] == 'MUTUEMPR') $registro['LiquidacionSocioRendicion']['empresa'] = "**S/D**";
				
				if(empty($liqSoc['LiquidacionSocio']['turno_pago']) || $liqSoc['LiquidacionSocio']['turno_pago'] == "SDATO"){
					$registro['LiquidacionSocioRendicion']['empresa'] = "*** SIN DATOS ***";
					$registro['LiquidacionSocioRendicion']['turno_pago'] = "SDATO";
				}
								
//				debug($liqSoc);
				
			endif;
//			debug($registro);
			$registros[$idx] = $registro;
		
		endforeach;
		
//		exit;
		
		return $registros;			
		
		
	}	
	

	
	function getByLiquidacionID($liquidacion_id){

		$joinPersona = "LEFT JOIN personas as Persona on  (LiquidacionSocioRendicion.documento = Persona.documento)";
		$joinSocioPersona = "INNER JOIN socios as Socio on (LiquidacionSocioRendicion.socio_id = Socio.id) ";
		$joinSocioPersona .= "INNER JOIN personas as Persona on (Socio.persona_id = Persona.id)";
		
		$sql = "SELECT 
					`LiquidacionSocioRendicion`.`id`, 
					`LiquidacionSocioRendicion`.`liquidacion_id`, 
					`LiquidacionSocioRendicion`.`codigo_organismo`, 
					`LiquidacionSocioRendicion`.`socio_id`, 
					`LiquidacionSocioRendicion`.`nro_ley`,
					`LiquidacionSocioRendicion`.`tipo`,
					`LiquidacionSocioRendicion`.`nro_beneficio`, 
					`LiquidacionSocioRendicion`.`sub_beneficio`,
					`LiquidacionSocioRendicion`.`codigo_dto`, 
					`LiquidacionSocioRendicion`.`sub_codigo`, 
					`LiquidacionSocioRendicion`.`cbu`, 
					`LiquidacionSocioRendicion`.`sucursal`,
					`LiquidacionSocioRendicion`.`tipo_cta_bco`, 
					`LiquidacionSocioRendicion`.`nro_cta_bco`,   
					`LiquidacionSocioRendicion`.`banco_intercambio`, 
					`LiquidacionSocioRendicion`.`documento`, 
					`LiquidacionSocioRendicion`.`importe_debitado`, 
					`LiquidacionSocioRendicion`.`status`, 
					`LiquidacionSocioRendicion`.`fecha_debito`, 
					`Persona`.`tipo_documento`,
					`Persona`.`documento`,
					`Persona`.`apellido`,
					`Persona`.`nombre`,
					`Banco`.`nombre`,
					`BancoRendicionCodigo`.`descripcion`,
					`BancoRendicionCodigo`.`indica_pago`
				FROM 
					`liquidacion_socio_rendiciones` AS `LiquidacionSocioRendicion` 
				INNER JOIN socios as Socio on (LiquidacionSocioRendicion.socio_id = Socio.id)
				INNER JOIN personas as Persona on (Socio.persona_id = Persona.id)
				LEFT JOIN `bancos` as `Banco` on  (`LiquidacionSocioRendicion`.`banco_intercambio` = `Banco`.`id`)
				LEFT JOIN `banco_rendicion_codigos` as `BancoRendicionCodigo` on  (`LiquidacionSocioRendicion`.`banco_intercambio` = `BancoRendicionCodigo`.`banco_id` and `LiquidacionSocioRendicion`.`status` = `BancoRendicionCodigo`.`codigo`)
				WHERE 
					`LiquidacionSocioRendicion`.`liquidacion_id` = $liquidacion_id 
				order by `Persona`.`apellido`,`Persona`.`nombre`";
		$registros = $this->query($sql);
		if(empty($registros)) return null;
		
		App::import('Model', 'Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
		
				
		foreach($registros as $idx => $registro):
		
			$registro = $this->armaDatos($registro);
			
			$registro['LiquidacionSocioRendicion']['descripcion'] = "";
			$registro['LiquidacionSocioRendicion']['empresa'] = "";
			$registro['LiquidacionSocioRendicion']['turno_pago'] = "";
			
			$liqSoc = $oLS->getLiquidacionBySocio($registro['LiquidacionSocioRendicion']['socio_id'],$registro['LiquidacionSocioRendicion']['liquidacion_id']);
			
			if(isset($liqSoc[0])):
			
				$liqSoc = $liqSoc[0];
				
				$registro['LiquidacionSocioRendicion']['descripcion'] = $oTURNO->getDescripcionByTruno($liqSoc['LiquidacionSocio']['turno_pago']);
				$registro['LiquidacionSocioRendicion']['empresa'] = parent::GlobalDato('concepto_1',$liqSoc['LiquidacionSocio']['codigo_empresa']);
				$registro['LiquidacionSocioRendicion']['turno_pago'] = $liqSoc['LiquidacionSocio']['turno_pago'];
				if( $liqSoc['LiquidacionSocio']['codigo_empresa'] == 'MUTUEMPR') $registro['LiquidacionSocioRendicion']['empresa'] = "**S/D**";
				
				if(empty($liqSoc['LiquidacionSocio']['turno_pago']) || $liqSoc['LiquidacionSocio']['turno_pago'] == "SDATO"){
					$registro['LiquidacionSocioRendicion']['empresa'] = "*** SIN DATOS ***";
					$registro['LiquidacionSocioRendicion']['turno_pago'] = "SDATO";
				}
								
//				debug($liqSoc);
				
			endif;
//			debug($registro);
			$registros[$idx] = $registro;
		
		endforeach;
		
//		exit;
		
		return $registros;			
		
		
	}	
	
	
	
	/**
	 * arma datos adicionales
	 * @param $registro
	 * @return unknown_type
	 */
	function armaDatos($registro){
		$registro['LiquidacionSocioRendicion']['banco_intercambio_desc'] = parent::getNombreBanco($registro['LiquidacionSocioRendicion']['banco_intercambio']);
		$registro['LiquidacionSocioRendicion']['organismo'] = parent::GlobalDato('concepto_1',$registro['LiquidacionSocioRendicion']['codigo_organismo']);
		App::import('Model'.'Config.BancoRendicionCodigo');
		$oBCRend = new BancoRendicionCodigo();
		$registro['LiquidacionSocioRendicion']['status_desc'] = $oBCRend->getDescripcionCodigo($registro['LiquidacionSocioRendicion']['banco_intercambio'],$registro['LiquidacionSocioRendicion']['status']);
		//arma el string de la identificacion
		$registro = $this->armaIdentificacionBeneficio($registro);
	
		return $registro;
	}
	
	/**
	 * Arma el string para identificar al beneficio por el cual vino el debito
	 * @param array $registro
	 * @return array
	 */
	function armaIdentificacionBeneficio($registro){
		$string = "";
		switch (substr($registro['LiquidacionSocioRendicion']['codigo_organismo'],8,2)){
			case 22:
				$string = "CBU:".$registro['LiquidacionSocioRendicion']['cbu'] ."|SUC:".$registro['LiquidacionSocioRendicion']['sucursal'] ."|CTA:". $registro['LiquidacionSocioRendicion']['nro_cta_bco'] ;
				if(!empty($registro['LiquidacionSocioRendicion']['proveedor_id'])){
					App::import('Model','proveedores.Proveedor');
					$oPROV = new Proveedor();
					$string .= "[*".$oPROV->getRazonSocialResumida($registro['LiquidacionSocioRendicion']['proveedor_id'])."*]";
				}
				break;
			case 77:
				$ley = $registro['LiquidacionSocioRendicion']['nro_ley'];
				$nroBeneficio = $registro['LiquidacionSocioRendicion']['nro_beneficio'];
				$tipo = $registro['LiquidacionSocioRendicion']['tipo'];
				$subBeneficio = $registro['LiquidacionSocioRendicion']['sub_beneficio'];
				$string = "LEY:$ley|TIPO:$tipo|BEN:$nroBeneficio|SUB:$subBeneficio";
				$codigo = $registro['LiquidacionSocioRendicion']['codigo_dto'];
				$subCodigo = $registro['LiquidacionSocioRendicion']['sub_codigo'];		
				$string .= "|COD:$codigo-$subCodigo";
				if(!empty($registro['LiquidacionSocioRendicion']['orden_descuento_id']) && $registro['LiquidacionSocioRendicion']['sub_codigo'] == 1){
					$string .= "[ORD #".$registro['LiquidacionSocioRendicion']['orden_descuento_id'];
					if(!empty($registro['LiquidacionSocioRendicion']['saldo_operacion_informado'])){
						$string .= " SDL:".number_format($registro['LiquidacionSocioRendicion']['saldo_operacion_informado'],2);
					}
					$string .= "]";
				}
				
				break;
			case 66:
				$string = "BEN:" .$registro['LiquidacionSocioRendicion']['nro_beneficio'] . "|COD:" . $registro['LiquidacionSocioRendicion']['codigo_dto'];
				break;		
		}
		$registro['LiquidacionSocioRendicion']['identificacion'] = $string;
		return $registro;		
	}
	
	/**
	 * Total Liquidado
	 * Devuelve un array con la cantidad de registros y total (encontrados o no encontrados) para una rendicion. 
	 * @param integer $liquidacion_id
	 * @param boolean $encontrados
	 * @return array
	 */
	function totalLiquidado($liquidacion_id,$encontrados=1,$indicaPago=1){
		$conditions = array();
		$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
		$conditions['LiquidacionSocioRendicion.indica_pago'] = $indicaPago;
		if($encontrados == 1)$conditions['LiquidacionSocioRendicion.socio_id <>'] = 0;
		else $conditions['IFNULL(LiquidacionSocioRendicion.socio_id,0)'] = 0;
		$total = 0;
		$total = $this->find('all',array(
												'conditions' =>$conditions,
												'fields' => array('sum(importe_debitado) as importe_debitado,count(*) as cantidad'),
											)
								);
		$total = array('cantidad' => (isset($total[0][0]['cantidad']) ? $total[0][0]['cantidad'] : 0), 'total' => (isset($total[0][0]['importe_debitado']) ? $total[0][0]['importe_debitado'] : 0));
		return $total;		
	}

	
	/**
	 * Liquidados
	 * devuelve los registros de la rendicion para una liquidacion. El segundo parametro es para indicar si trae los encontrados
	 * en la liquidacion o no
	 * @param integer $liquidacion_id
	 * @param integer $opcion opciones de listado (1 = encontrados; 2 = no encontrados; 3 = todos
	 * @param string $codigo
	 * @return array 
	 */
	function liquidados($liquidacion_id,$opcion=1,$codigo=null,$bancoIntercambio=null){

		$joinPersona = "LEFT JOIN personas as Persona on  (LiquidacionSocioRendicion.documento = Persona.documento)";
		$joinSocioPersona = "INNER JOIN socios as Socio on (LiquidacionSocioRendicion.socio_id = Socio.id) ";
		$joinSocioPersona .= "INNER JOIN personas as Persona on (Socio.persona_id = Persona.id)";
		
		$sql = "SELECT 
					`LiquidacionSocioRendicion`.`id`, 
					`LiquidacionSocioRendicion`.`liquidacion_id`, 
					`LiquidacionSocioRendicion`.`codigo_organismo`, 
					`LiquidacionSocioRendicion`.`nro_ley`,
					`LiquidacionSocioRendicion`.`tipo`,
					`LiquidacionSocioRendicion`.`nro_beneficio`, 
					`LiquidacionSocioRendicion`.`sub_beneficio`,
					`LiquidacionSocioRendicion`.`codigo_dto`, 
					`LiquidacionSocioRendicion`.`sub_codigo`, 
					`LiquidacionSocioRendicion`.`cbu`, 
					`LiquidacionSocioRendicion`.`sucursal`,
					`LiquidacionSocioRendicion`.`tipo_cta_bco`, 
					`LiquidacionSocioRendicion`.`nro_cta_bco`,   
					`LiquidacionSocioRendicion`.`banco_intercambio`, 
					`LiquidacionSocioRendicion`.`documento`, 
					`LiquidacionSocioRendicion`.`importe_debitado`, 
					`LiquidacionSocioRendicion`.`status`, 
					`LiquidacionSocioRendicion`.`fecha_debito`, 
					`Persona`.`tipo_documento`,
					`Persona`.`documento`,
					`Persona`.`apellido`,
					`Persona`.`nombre`,
					`Banco`.`nombre`,
					`BancoRendicionCodigo`.`descripcion`,
					`BancoRendicionCodigo`.`indica_pago`
				FROM 
					`liquidacion_socio_rendiciones` AS `LiquidacionSocioRendicion` 
				".($opcion == 1 ? $joinSocioPersona : $joinPersona)."
				LEFT JOIN `bancos` as `Banco` on  (`LiquidacionSocioRendicion`.`banco_intercambio` = `Banco`.`id`)
				LEFT JOIN `banco_rendicion_codigos` as `BancoRendicionCodigo` on  (`LiquidacionSocioRendicion`.`banco_intercambio` = `BancoRendicionCodigo`.`banco_id` and `LiquidacionSocioRendicion`.`status` = `BancoRendicionCodigo`.`codigo`)
				WHERE 
					`LiquidacionSocioRendicion`.`liquidacion_id` = $liquidacion_id 
					".($opcion == 1 ? " AND LiquidacionSocioRendicion.socio_id <> 0 " : ( $opcion == 2 ? " AND IFNULL(LiquidacionSocioRendicion.socio_id,0) = 0 " : ($opcion == 3 ? "" : " AND 1=2")))."
					".(!empty($codigo) ? "AND LiquidacionSocioRendicion.status = '$codigo' AND LiquidacionSocioRendicion.banco_intercambio = '$bancoIntercambio' " : "")."
				order by `LiquidacionSocioRendicion`.`status`,`Persona`.`apellido`,`Persona`.`nombre`";
		$registros = $this->query($sql);
		foreach($registros as $idx => $registro):
			$registros[$idx] = $this->armaDatos($registro);
		endforeach;
		return $registros;		
				
	}
	
	/**
	 * Arma el resumen del procesamiento de los archivos de rendicion
	 * @param unknown_type $liquidacion_id
	 * @param unknown_type $opcion
	 * @return NULL|Ambigous <unknown, multitype:>
	 */
	function resumenRendicion($liquidacion_id,$opcion=1){
		
		$sql = "	SELECT 
						LiquidacionSocioRendicion.banco_intercambio,
						LiquidacionSocioRendicion.status,
						BancoRendicionCodigo.descripcion,
						LiquidacionSocioRendicion.indica_pago,
						count(*) as cantidad_recibida,
						sum(importe_debitado) as importe_debitado,
						IFNULL((SELECT COUNT(*) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio 
							),0)
						AS cantidad_recibida_total,	
						IFNULL((SELECT COUNT(*) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio AND
							LiquidacionSocioRendicion2.indica_pago = 1
							),0)
						AS registros_cobrados,	
						IFNULL((SELECT COUNT(*) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio AND
							LiquidacionSocioRendicion2.indica_pago = 0
							),0)
						AS registros_no_cobrados,												
						IFNULL((SELECT SUM(importe_debitado) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio 
							),0)
						AS importe_debitado_total,
						IFNULL((SELECT SUM(importe_debitado) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio AND
							LiquidacionSocioRendicion2.indica_pago = 1
							),0)
						AS importe_cobrado_total,
						IFNULL((SELECT SUM(importe_debitado) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio AND
							LiquidacionSocioRendicion2.indica_pago = 0
							),0)
						AS importe_nocobrado_total,
						IFNULL((SELECT SUM(importe_debitado) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.indica_pago = 1
							),0)
						AS importe_cobrado_total_general,
						IFNULL((SELECT SUM(importe_debitado) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.indica_pago = 0
							),0)
						AS importe_nocobrado_total_general,
						IFNULL((SELECT SUM(importe_debitado) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio
							),0)
						AS importe_archivo,
						IFNULL((SELECT SUM(importe_debitado) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
							),0)
						AS importe_archivo_general,
						IFNULL((SELECT count(*) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.indica_pago = 1
							),0)
						AS registros_archivo_general_cobrados,	
						IFNULL((SELECT count(*) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id AND
							LiquidacionSocioRendicion2.indica_pago = 0
							),0)
						AS registros_archivo_general_no_cobrados,											
						IFNULL((SELECT count(*) FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion2
						WHERE 
							LiquidacionSocioRendicion2.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
							),0)
						AS registros_archivo_general													
					FROM 
						liquidacion_socio_rendiciones as LiquidacionSocioRendicion
					LEFT JOIN banco_rendicion_codigos as BancoRendicionCodigo ON (
																					LiquidacionSocioRendicion.banco_intercambio = BancoRendicionCodigo.banco_id AND
																					LiquidacionSocioRendicion.status = BancoRendicionCodigo.codigo
																				)	 
					WHERE 
						LiquidacionSocioRendicion.liquidacion_id = $liquidacion_id 
						".($opcion == 1 ? " AND LiquidacionSocioRendicion.socio_id <> 0 " : ( $opcion == 2 ? " AND LiquidacionSocioRendicion.socio_id = 0 " : ($opcion == 3 ? "" : " AND 1=2")))."
					GROUP BY
						LiquidacionSocioRendicion.banco_intercambio,
						LiquidacionSocioRendicion.status,
						LiquidacionSocioRendicion.indica_pago
					ORDER BY
						LiquidacionSocioRendicion.banco_intercambio,
						LiquidacionSocioRendicion.indica_pago DESC	
				";
		$registros = $this->query($sql);
		
// 		debug($sql);
		
		if(empty($registros)) return null;
		
//		debug($registros);
		
		$resumen = array();
		$bancoActual = null;
		$i = 0;
		
		$TOTAL_COBRADO = $TOTAL_NO_COBRADO = $TOTAL_ARCHIVO = $CANTIDAD_REGISTROS = $CANTIDAD_REGISTROS_COB = $CANTIDAD_REGISTROS_NOCOB = 0;
		
		foreach($registros as $idx => $registro):
		
			$TOTAL_COBRADO = $registro[0]['importe_cobrado_total_general'];
			$TOTAL_NO_COBRADO = $registro[0]['importe_nocobrado_total_general'];
			$TOTAL_ARCHIVO = $registro[0]['importe_archivo_general'];
			$CANTIDAD_REGISTROS = $registro[0]['registros_archivo_general'];
			$CANTIDAD_REGISTROS_COB = $registro[0]['registros_archivo_general_cobrados'];
			$CANTIDAD_REGISTROS_NOCOB = $registro[0]['registros_archivo_general_no_cobrados'];
		
			if($bancoActual != $registro['LiquidacionSocioRendicion']['banco_intercambio']){
				$i = 0;
				$bancoActual = $registro['LiquidacionSocioRendicion']['banco_intercambio'];
			}
		
			$registro['LiquidacionSocioRendicion']['cantidad_recibida'] = $registro[0]['cantidad_recibida'];
			$registro['LiquidacionSocioRendicion']['importe_debitado'] = $registro[0]['importe_debitado'];
			
			$registro['LiquidacionSocioRendicion']['cantidad_recibida_porc'] = ($registro['LiquidacionSocioRendicion']['cantidad_recibida'] / $registro[0]['cantidad_recibida_total']) * 100;
			$registro['LiquidacionSocioRendicion']['importe_debitado_porc'] = ($registro['LiquidacionSocioRendicion']['importe_debitado'] / $registro[0]['importe_debitado_total']) * 100;
			
			//redondeo
			$registro['LiquidacionSocioRendicion']['cantidad_recibida'] = round($registro['LiquidacionSocioRendicion']['cantidad_recibida'],2);
			$registro['LiquidacionSocioRendicion']['importe_debitado'] = round($registro['LiquidacionSocioRendicion']['importe_debitado'],2);
			$registro['LiquidacionSocioRendicion']['cantidad_recibida_porc'] = round($registro['LiquidacionSocioRendicion']['cantidad_recibida_porc'],2);
			$registro['LiquidacionSocioRendicion']['importe_debitado_porc'] = round($registro['LiquidacionSocioRendicion']['importe_debitado_porc'],2);
			
			$registro['LiquidacionSocioRendicion']['descripcion'] = $registro['BancoRendicionCodigo']['descripcion'];
			$registro['LiquidacionSocioRendicion']['banco'] =  parent::getNombreBanco($registro['LiquidacionSocioRendicion']['banco_intercambio']);
		

			//anexo los valores al array
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['nombre'] = $registro['LiquidacionSocioRendicion']['banco'];
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['registros'] = $registro[0]['cantidad_recibida_total'];
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['registros_cobrados'] = $registro[0]['registros_cobrados'];
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['registros_nocobrados'] = $registro[0]['registros_no_cobrados'];
			
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['registros_porc'] = round($registro[0]['cantidad_recibida_total'] / $registro[0]['registros_archivo_general'] * 100,2);
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['registros_cobrados_porc'] = round($registro[0]['registros_cobrados'] / $registro[0]['registros_archivo_general'] * 100,2);
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['registros_nocobrados_porc'] = round($registro[0]['registros_no_cobrados'] / $registro[0]['registros_archivo_general'] * 100,2);
			
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total_archivo_general'] = round($registro[0]['importe_archivo_general'],2);			
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total_archivo'] = round($registro[0]['importe_archivo'],2);
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total_cobrado'] = round($registro[0]['importe_cobrado_total'],2);
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total_no_cobrado'] = round($registro[0]['importe_nocobrado_total'],2);
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total_archivo_porc'] = round($registro[0]['importe_archivo'] / $registro[0]['importe_archivo_general'] * 100,2);
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total_cobrado_porc'] = round($registro[0]['importe_cobrado_total'] / $registro[0]['importe_archivo_general'] * 100,2);
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total_no_cobrado_porc'] = round($registro[0]['importe_nocobrado_total'] / $registro[0]['importe_archivo_general'] * 100,2);
			$resumen[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['codigos'][$i] = $registro['LiquidacionSocioRendicion'];
			
			$i++;
		
		endforeach;
		
		$datos['total_registros'] = $CANTIDAD_REGISTROS;
		$datos['total_registros_cob'] = $CANTIDAD_REGISTROS_COB;
		$datos['total_registros_nocob'] = $CANTIDAD_REGISTROS_NOCOB;

		$datos['total_registros_cob_porc'] = round($CANTIDAD_REGISTROS_COB / $CANTIDAD_REGISTROS * 100,2);
		$datos['total_registros_nocob_porc'] = round($CANTIDAD_REGISTROS_NOCOB / $CANTIDAD_REGISTROS * 100,2);
		
		$datos['total_archivo'] = $TOTAL_ARCHIVO;
		$datos['total_cobrado'] = $TOTAL_COBRADO;
		$datos['total_no_cobrado'] = $TOTAL_NO_COBRADO;
		$datos['total_cobrado_porc'] = round($TOTAL_COBRADO / $TOTAL_ARCHIVO * 100,2);
		$datos['total_no_cobrado_porc'] = round($TOTAL_NO_COBRADO / $TOTAL_ARCHIVO * 100,2);
		
		
		$datos['detalle'] = $resumen;
		
		
		return $datos;
		
		
//		$TOTAL_REGISTROS = 0;
//		$TOTAL_IMPORTE = 0;
//		
//		$totales = array();
//		
//		$bancoActual = null;
//		
//		foreach($registros as $i => $registro):
//		
//			if($bancoActual != $registro['LiquidacionSocioRendicion']['banco_intercambio']):
//				$bancoActual = $registro['LiquidacionSocioRendicion']['banco_intercambio'];
//				$TOTAL_REGISTROS = 0;
//				$TOTAL_IMPORTE = 0;			
//			endif;
//		
//			$registro['LiquidacionSocioRendicion']['cantidad_recibida'] = $registro[0]['cantidad_recibida'];
//			$registro['LiquidacionSocioRendicion']['importe_debitado'] = $registro[0]['importe_debitado'];
//			
//			$registro['LiquidacionSocioRendicion']['descripcion'] = $registro['BancoRendicionCodigo']['descripcion'];
//			$registro['LiquidacionSocioRendicion']['banco'] =  parent::getNombreBanco($registro['LiquidacionSocioRendicion']['banco_intercambio']);
//
//			$TOTAL_REGISTROS += $registro['LiquidacionSocioRendicion']['cantidad_recibida'];
//			$TOTAL_IMPORTE += $registro['LiquidacionSocioRendicion']['importe_debitado'];
//			
//			$totales[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['cantidad'] = $TOTAL_REGISTROS;
//			$totales[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total'] = $TOTAL_IMPORTE;
//			
//			$registros[$i] = $registro;
//	
//		endforeach;
//		
////		debug($registros);
//		
//		$registros = Set::extract("/LiquidacionSocioRendicion",$registros);
//		
//	
//		$porcentajeRegistro = 0;
//		$porcentajeImporte = 0;
//		
//		foreach($registros as $i => $registro):
//		
//			$porcentajeRegistro = ($registro['LiquidacionSocioRendicion']['cantidad_recibida'] / $totales[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['cantidad']) * 100;
//			$porcentajeImporte = ($registro['LiquidacionSocioRendicion']['importe_debitado'] / $totales[$registro['LiquidacionSocioRendicion']['banco_intercambio']]['total']) * 100;		
//		
//			$registro['LiquidacionSocioRendicion']['cantidad_recibida_porc'] = $porcentajeRegistro;
//			$registro['LiquidacionSocioRendicion']['importe_debitado_porc'] = $porcentajeImporte;
//			
//			$registros[$i] = $registro;
//			
//		endforeach;
//		
////		debug($TOTAL_PORCENTAJE_REGISTRO);
////		debug($TOTAL_PORCENTAJE_IMPORTE);
//		
////		debug($registros);
////		exit;
//		
//		return $registros;
		
	}
	
	
	function imputados($liquidacion_id,$armaDatos=false){
		$sql = "select 
				Persona.tipo_documento,
				Persona.documento,
				concat(concat(Persona.apellido,', '),Persona.nombre) as apenom,
				LiquidacionSocioRendicion.socio_id,
				(select sum(importe_dto) from liquidacion_socios as LiquidacionSocio
					where LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
					and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id group by  LiquidacionSocioRendicion.socio_id
				) as importe_dto,
				(select sum(importe_adebitar) from liquidacion_socios as LiquidacionSocio
					where LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
					and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id group by  LiquidacionSocioRendicion.socio_id
				) as importe_adebitar,
				sum(importe_debitado) as importe_debitado,
				(select sum(importe_debitado) from
					liquidacion_cuotas as LiquidacionCuota where LiquidacionCuota.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
					and LiquidacionCuota.socio_id = LiquidacionSocioRendicion.socio_id group by  LiquidacionSocioRendicion.socio_id)
				as importe_imputado, 
				(sum(importe_debitado) - (select sum(importe_debitado) from
				liquidacion_cuotas as LiquidacionCuota where LiquidacionCuota.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
				and LiquidacionCuota.socio_id = LiquidacionSocioRendicion.socio_id group by  LiquidacionSocioRendicion.socio_id)) 
				as importe_reintegro,
				LiquidacionSocioRendicion.*
			from 
				liquidacion_socio_rendiciones as LiquidacionSocioRendicion
			inner join socios as Socio on (LiquidacionSocioRendicion.socio_id = Socio.id)
			inner join personas as Persona on (Socio.persona_id = Persona.id)
			where 
				LiquidacionSocioRendicion.liquidacion_id = 88 and LiquidacionSocioRendicion.socio_id <> 0
				and LiquidacionSocioRendicion.indica_pago = 1
			group by 
				LiquidacionSocioRendicion.socio_id 
			order by Persona.apellido,Persona.nombre";
		$registros = $this->query($sql);
		
		if(empty($registros)) return null;
		
		if(!$armaDatos) return $registros;
		
		foreach($registros as $i => $registro):
			$registro = $this->armaDatos($registro);
			$registros[$i] = $registro;
		endforeach;
		
		return $registros;
		
	}
	
	/**
	 * Reprocesa Intercambio. Solo para CJP y ANSES
	 * 
	 * @param $liquidacion_id
	 * @param $socio_id
	 * @param $organismo
	 * @return unknown_type
	 */
	function reprocesaIntercambio($liquidacion_id,$socio_id,$organismo){
		
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();		
		
		//cargo los beneficios de la liquidacion socio
		$beneficiosLiquidados = $oLS->find('all',array('conditions' => array('LiquidacionSocio.liquidacion_id' => $liquidacion_id, 'LiquidacionSocio.socio_id' => $socio_id),  'fields' => array('LiquidacionSocio.persona_beneficio_id'), 'group' => array('LiquidacionSocio.persona_beneficio_id')));

		if(empty($beneficiosLiquidados)) return null;
		
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBENEFICIO = new PersonaBeneficio();
				
		$beneficio = array();
		
		$organismo = substr($organismo,8,2);		
		
		foreach($beneficiosLiquidados as $beneficioLiquidado):
		
			$oBENEFICIO->unbindModel(array('belongsTo' => array('Persona')));
			$beneficio = $oBENEFICIO->read(null,$beneficioLiquidado['LiquidacionSocio']['persona_beneficio_id']);
			$PROCESAR = FALSE;
			
			$conditions = array();
			$conditions['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
			
			###########################################################################
			# CAJA DE JUBILACIONES
			###########################################################################
			if($organismo == 77):
				$conditions['LiquidacionSocioRendicion.tipo'] = $beneficio['PersonaBeneficio']['tipo'];
				$conditions['LiquidacionSocioRendicion.nro_ley'] = $beneficio['PersonaBeneficio']['nro_ley'];
				$conditions['LiquidacionSocioRendicion.nro_beneficio'] = $beneficio['PersonaBeneficio']['nro_beneficio'];
				$conditions['LiquidacionSocioRendicion.nro_beneficio'] = $beneficio['PersonaBeneficio']['sub_beneficio'];
				$PROCESAR = TRUE;
			endif;
			###########################################################################
			# ANSES
			###########################################################################
			if($organismo == 66):
				$conditions['LiquidacionSocioRendicion.nro_beneficio'] = $beneficio['PersonaBeneficio']['nro_beneficio'];
				$PROCESAR = TRUE;
			endif;			
			$rendiciones = null;
			if($PROCESAR) $rendiciones = $this->find('all',array('conditions'=>$conditions));

			if(!empty($rendiciones)):
				foreach($rendiciones as $rendicion):
					$rendicion['LiquidacionSocioRendicion']['socio_id'] = $socio_id;
					$this->save($rendicion);
				endforeach;
			endif;
			
			
		endforeach;
		
		return true;
		
	}
	
	function getTotalRegistros($liquidacion_intercambio_id){
		$total = $this->find('all',array(
												'conditions' => 
													array(
															'LiquidacionSocioRendicion.liquidacion_intercambio_id' => $liquidacion_intercambio_id													),
												'fields' => array('count(*) as total_registros'),
											)
								);
		return (isset($total[0][0]['total_registros']) ? $total[0][0]['total_registros'] : 0);		
	
	}
	

	
	function getRegistrosCobrados($liquidacion_intercambio_id){
		$total = $this->find('all',array(
												'conditions' => 
													array(
															'LiquidacionSocioRendicion.liquidacion_intercambio_id' => $liquidacion_intercambio_id,
															'LiquidacionSocioRendicion.indica_pago' => 1
													),
												'fields' => array('count(*) as registros_cobrados'),
											)
								);
		return (isset($total[0][0]['registros_cobrados']) ? $total[0][0]['registros_cobrados'] : 0);		
	
	}
	

	function getImporteCobrado($liquidacion_intercambio_id){
		$total = $this->find('all',array(
												'conditions' => 
													array(
															'LiquidacionSocioRendicion.liquidacion_intercambio_id' => $liquidacion_intercambio_id,
															'LiquidacionSocioRendicion.indica_pago' => 1
													),
												'fields' => array('sum(importe_debitado) as importe_debitado'),
											)
								);
		return (isset($total[0][0]['importe_debitado']) ? $total[0][0]['importe_debitado'] : 0);		
		
	}
	
	
	function getResumenByEmpresaTurno($archivoIntercambioId, $noCobrados = false){
		
		$sql = "SELECT 
				GlobalDato.concepto_1,
				LiquidacionTurno.codigo_empresa,	
				LiquidacionTurno.turno,
				LiquidacionTurno.descripcion,
				BancoRendicionCodigo.codigo,
				BancoRendicionCodigo.descripcion,
				SUM(LiquidacionSocioRendicion.importe_debitado) AS importe_debitado
				FROM liquidacion_socio_rendiciones 
				AS LiquidacionSocioRendicion
				INNER JOIN liquidacion_socios AS LiquidacionSocio ON (
						LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
						AND LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id	
						-- AND LiquidacionSocio.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio
						-- AND LiquidacionSocio.fecha_debito = LiquidacionSocioRendicion.fecha_debito
						)
				INNER JOIN global_datos AS GlobalDato ON (GlobalDato.id = LiquidacionSocio.codigo_empresa)
				INNER JOIN liquidacion_turnos AS LiquidacionTurno ON (LiquidacionTurno.turno = LiquidacionSocio.turno_pago)		
				INNER JOIN banco_rendicion_codigos AS BancoRendicionCodigo ON (
					BancoRendicionCodigo.banco_id = LiquidacionSocioRendicion.banco_intercambio
					AND BancoRendicionCodigo.codigo = LiquidacionSocioRendicion.status
				)
				WHERE 
				LiquidacionSocioRendicion.liquidacion_intercambio_id = $archivoIntercambioId
				AND LiquidacionSocioRendicion.indica_pago = " . ($noCobrados ? 0 : 1) . "
				GROUP BY LiquidacionSocio.codigo_empresa,LiquidacionSocio.turno_pago,LiquidacionSocioRendicion.status
				ORDER BY GlobalDato.concepto_1, LiquidacionTurno.descripcion, BancoRendicionCodigo.descripcion;";
		
		$datos = $this->query($sql);
		
		
		if(empty($datos)) return null;
		
		$resumen = array();
		
		$tmp = array();
		
		foreach($datos as $idx => $dato):
		
			$tmp['LiquidacionSocioRendicion']['codigo_empresa'] = $dato['LiquidacionTurno']['codigo_empresa'];
			$tmp['LiquidacionSocioRendicion']['codigo_empresa_desc'] = $dato['GlobalDato']['concepto_1'];
			$tmp['LiquidacionSocioRendicion']['turno_pago'] = $dato['LiquidacionTurno']['turno'];
			if(!empty($dato['LiquidacionTurno']['descripcion']))$tmp['LiquidacionSocioRendicion']['turno_pago_desc'] = $dato['LiquidacionTurno']['turno'] . " - " . $dato['LiquidacionTurno']['descripcion'];
			else $tmp['LiquidacionSocioRendicion']['turno_pago_desc'] = null;
			
			$tmp['LiquidacionSocioRendicion']['codigo_rendicion'] = $dato['BancoRendicionCodigo']['codigo'];
			$tmp['LiquidacionSocioRendicion']['codigo_rendicion_desc'] = $dato['BancoRendicionCodigo']['codigo'] . " - " . $dato['BancoRendicionCodigo']['descripcion'];
		
			$tmp['LiquidacionSocioRendicion']['importe'] = round($dato[0]['importe_debitado'],2);
			
			$tmp['LiquidacionSocioRendicion']['key_select'] = $tmp['LiquidacionSocioRendicion']['codigo_empresa'] . "|" . $tmp['LiquidacionSocioRendicion']['turno_pago'] . "|" . $tmp['LiquidacionSocioRendicion']['codigo_rendicion'];
			
			$tmp['LiquidacionSocioRendicion']['key_select_label'] = $tmp['LiquidacionSocioRendicion']['codigo_empresa_desc'];
			
			if(!empty($tmp['LiquidacionSocioRendicion']['turno_pago_desc'])) $tmp['LiquidacionSocioRendicion']['key_select_label'].= " | " . $tmp['LiquidacionSocioRendicion']['turno_pago_desc'];
			$tmp['LiquidacionSocioRendicion']['key_select_label'].= " | " . $tmp['LiquidacionSocioRendicion']['codigo_rendicion_desc'] . " [" . number_format($tmp['LiquidacionSocioRendicion']['importe'],2) . "]"; 
			
			$resumen[$idx] = $tmp;
			
		endforeach;
		return $resumen;
		
		
	}
	
	function deleteAllByLiquidacionId($liquidacionId){
		return $this->deleteAll("LiquidacionSocioRendicion.liquidacion_id = " . $liquidacionId);
	}
	
	function deleteAllByArchivoIntercambioId($intercambioId){
		return $this->deleteAll("LiquidacionSocioRendicion.liquidacion_intercambio_id = " . $intercambioId);
	}
	
	
	/**
	 * Devuelve las cadenas de intercambios recibidas para un periodo / organismo dado
	 * @author adrian [02/02/2012]
	 * @param int $socio_id
	 * @param string $periodo
	 * @param string $codigoOrganismo
	 * @return string
	 */
	function getStringIntercambioRecibido($socio_id,$periodo,$codigoOrganismo){
		$str = null;
		$sql = "SELECT LiquidacionSocioRendicion.registro FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
				INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionSocioRendicion.liquidacion_id)
				WHERE LiquidacionSocioRendicion.socio_id = 	$socio_id
				AND Liquidacion.periodo = '$periodo' AND Liquidacion.codigo_organismo = '$codigoOrganismo'
				AND IFNULL(LiquidacionSocioRendicion.registro,'0') <> 0";
		$socios = $this->query($sql);
		foreach($socios as $idx => $socio){
			$str .= preg_replace("[\n|\r|\n\r]","",$socio['LiquidacionSocioRendicion']['registro'])."\n";
		}
		$str = str_replace(" ",".",$str);
		return $str;
	}	
	
	function getSociosByArchivoIntercambio($intercambio_id, $cobrado = true){
		$socios = $this->find('all',array('conditions' => array('LiquidacionSocioRendicion.liquidacion_intercambio_id' => $intercambio_id, 'LiquidacionSocioRendicion.indica_pago' => $cobrado), 'fields' => array('LiquidacionSocioRendicion.socio_id, SUM(LiquidacionSocioRendicion.importe_debitado) AS importe_debitado'), 'group' => array('LiquidacionSocioRendicion.socio_id')));
		return $socios;
	}
	
	/**
	 * Devuelve un array con los socio_id que fueron informados como debitados por el banco
	 * y que no tienen una orden de cobro emitida
	 * 
	 * @author adrian [28/03/2012]
	 * @param unknown_type $liquidacion_id
	 */
	function getSociosDebitadosNoCobrados($liquidacion_id){
		$socios = $this->find('all',array('conditions' => array('LiquidacionSocioRendicion.liquidacion_id' => $liquidacion_id, 'LiquidacionSocioRendicion.indica_pago' => 1 , 'LiquidacionSocioRendicion.orden_descuento_cobro_id' => 0, 'LiquidacionSocioRendicion.socio_id <>' => null), 'fields' => array('LiquidacionSocioRendicion.socio_id'), 'group' => array('LiquidacionSocioRendicion.socio_id')));
		$socios = Set::extract("/LiquidacionSocioRendicion/socio_id",$socios);
		return $socios;
	}
	/**
	 * Devuelve un array con los socio_id que fueron informados como debitados por el banco
	 * y que tienen una orden de cobro emitida
	 * 
	 * @author adrian [28/03/2012]
	 * @param unknown_type $liquidacion_id
	 */	
	function getSociosDebitadosCobrados($liquidacion_id){
		$socios = $this->find('all',array('conditions' => array('LiquidacionSocioRendicion.liquidacion_id' => $liquidacion_id, 'LiquidacionSocioRendicion.indica_pago' => 1 , 'LiquidacionSocioRendicion.orden_descuento_cobro_id <>' => 0), 'fields' => array('LiquidacionSocioRendicion.socio_id'), 'group' => array('LiquidacionSocioRendicion.socio_id')));
		$socios = Set::extract("/LiquidacionSocioRendicion/socio_id",$socios);
		return $socios;
	}	
	
    
    
	function getResumenByEmpresaTurnoByLiquidacionId($liquidacionId, $noCobrados = false){
		
		$sql = "SELECT 
				GlobalDato.concepto_1,
				LiquidacionTurno.codigo_empresa,	
				LiquidacionTurno.turno,
				LiquidacionTurno.descripcion,
				BancoRendicionCodigo.codigo,
				BancoRendicionCodigo.descripcion,
				SUM(LiquidacionSocioRendicion.importe_debitado) AS importe_debitado
				FROM liquidacion_socio_rendiciones 
				AS LiquidacionSocioRendicion
				INNER JOIN liquidacion_socios AS LiquidacionSocio ON (
						LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
						AND LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id	
						AND LiquidacionSocio.banco_intercambio = LiquidacionSocioRendicion.banco_intercambio
						-- AND LiquidacionSocio.fecha_debito = LiquidacionSocioRendicion.fecha_debito
						)
				INNER JOIN global_datos AS GlobalDato ON (GlobalDato.id = LiquidacionSocio.codigo_empresa)
				INNER JOIN liquidacion_turnos AS LiquidacionTurno ON (LiquidacionTurno.turno = LiquidacionSocio.turno_pago)		
				INNER JOIN banco_rendicion_codigos AS BancoRendicionCodigo ON (
					BancoRendicionCodigo.banco_id = LiquidacionSocioRendicion.banco_intercambio
					AND BancoRendicionCodigo.codigo = LiquidacionSocioRendicion.status
				)
				WHERE 
				LiquidacionSocioRendicion.liquidacion_id = $liquidacionId
				AND LiquidacionSocioRendicion.indica_pago = " . ($noCobrados ? 0 : 1) . "
				GROUP BY LiquidacionSocio.codigo_empresa,LiquidacionSocio.turno_pago,LiquidacionSocioRendicion.status
				ORDER BY GlobalDato.concepto_1, LiquidacionTurno.descripcion, BancoRendicionCodigo.descripcion;";
		
		$datos = $this->query($sql);
//		debug($sql);
		
		if(empty($datos)) return null;
		
		$resumen = array();
		
		$tmp = array();
		
		foreach($datos as $idx => $dato):
		
			$tmp['LiquidacionSocioRendicion']['codigo_empresa'] = $dato['LiquidacionTurno']['codigo_empresa'];
			$tmp['LiquidacionSocioRendicion']['codigo_empresa_desc'] = $dato['GlobalDato']['concepto_1'];
			$tmp['LiquidacionSocioRendicion']['turno_pago'] = $dato['LiquidacionTurno']['turno'];
			if(!empty($dato['LiquidacionTurno']['descripcion']))$tmp['LiquidacionSocioRendicion']['turno_pago_desc'] = $dato['LiquidacionTurno']['turno'] . " - " . $dato['LiquidacionTurno']['descripcion'];
			else $tmp['LiquidacionSocioRendicion']['turno_pago_desc'] = null;
			
			$tmp['LiquidacionSocioRendicion']['codigo_rendicion'] = $dato['BancoRendicionCodigo']['codigo'];
			$tmp['LiquidacionSocioRendicion']['codigo_rendicion_desc'] = $dato['BancoRendicionCodigo']['codigo'] . " - " . $dato['BancoRendicionCodigo']['descripcion'];
		
			$tmp['LiquidacionSocioRendicion']['importe'] = round($dato[0]['importe_debitado'],2);
			
			$tmp['LiquidacionSocioRendicion']['key_select'] = $tmp['LiquidacionSocioRendicion']['codigo_empresa'] . "|" . $tmp['LiquidacionSocioRendicion']['turno_pago'] . "|" . $tmp['LiquidacionSocioRendicion']['codigo_rendicion'];
			
			$tmp['LiquidacionSocioRendicion']['key_select_label'] = $tmp['LiquidacionSocioRendicion']['codigo_empresa_desc'];
			
			if(!empty($tmp['LiquidacionSocioRendicion']['turno_pago_desc'])) $tmp['LiquidacionSocioRendicion']['key_select_label'].= " | " . $tmp['LiquidacionSocioRendicion']['turno_pago_desc'];
			$tmp['LiquidacionSocioRendicion']['key_select_label'].= " | " . $tmp['LiquidacionSocioRendicion']['codigo_rendicion_desc'] . " [" . number_format($tmp['LiquidacionSocioRendicion']['importe'],2) . "]"; 
			
			$resumen[$idx] = $tmp;
			
		endforeach;
		return $resumen;
		
		
	}    
    
    function checkBancoAndCBU($bancoIntercambio,$cbu){
        $sql = "select count(*) as cant from liquidacion_socio_rendiciones
                where banco_intercambio = '$bancoIntercambio' and cbu = '$cbu';";
        $datos = $this->query($sql);
        if(empty($datos)) return FALSE;
        return ($datos[0][0]['cant'] == 0 ? FALSE : TRUE);        
    }
    
    
    function get_historico_stop_debit($socio_id){
        
        $sql = "select Banco.id,Banco.nombre,fecha_debito,sum(importe_debitado) as importe_debitado from liquidacion_socio_rendiciones LiquidacionSocioRendicion
                inner join bancos Banco on Banco.id = LiquidacionSocioRendicion.banco_intercambio
                where socio_id = $socio_id and status = 'R08'
                group by fecha_debito order by fecha_debito desc;";
        $datos = $this->query($sql);
        if(empty($datos)) return null;
        $resumen = array();
        foreach($datos as $idx => $dato):
            $resumen[$idx] = array(
                'banco_id' => $dato['Banco']['id'],
                'banco_nombre' => $dato['Banco']['nombre'],
                'fecha_debito' => $dato['LiquidacionSocioRendicion']['fecha_debito'],
                'importe_debitado' => $dato[0]['importe_debitado'],
            );
        endforeach;
        return $resumen;
    }
    
    
    public function getBancoIntercambiosStops($liquidacionId,$toList=TRUE){
        
        $sql = "select Banco.id,Banco.nombre
                from liquidacion_socio_rendiciones LiquidacionSocioRendicion
                inner join bancos Banco on Banco.id = LiquidacionSocioRendicion.banco_intercambio
                left join banco_rendicion_codigos BancoRendicionCodigo 
                on BancoRendicionCodigo.banco_id = LiquidacionSocioRendicion.banco_intercambio
                and BancoRendicionCodigo.codigo = LiquidacionSocioRendicion.status
                where liquidacion_id = $liquidacionId and 
                (BancoRendicionCodigo.calificacion_socio = 'MUTUCALISDEB' or BancoRendicionCodigo.codigo in ('R08','008'))
                group by banco_intercambio;";
        $bancos = $this->query($sql);
        if($toList & !empty($bancos)){
            $lista = array();
            foreach($bancos as $banco){
                $lista[$banco['Banco']['id']] = $banco['Banco']['nombre'];
            }
            return $lista;
        }
        return $bancos;
    }
    
    public function getSociosStop($liquidacionId,$bancoId){
        
        $sql = "select 
                LiquidacionSocioRendicion.socio_id 
                ,Persona.documento
                ,Persona.apellido
                ,Persona.nombre
                ,sum(LiquidacionSocioRendicion.importe_debitado) as importe_stop
                ,ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
                inner join liquidaciones l on l.id = lc.liquidacion_id 
                where l.id = Liquidacion.id
                and lc.socio_id = LiquidacionSocioRendicion.socio_id
                and lc.periodo_cuota = l.periodo and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
                ),0) as saldo_periodo
                ,ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
                inner join liquidaciones l on l.id = lc.liquidacion_id 
                where l.id = Liquidacion.id
                and lc.socio_id = LiquidacionSocioRendicion.socio_id
                and lc.periodo_cuota <> l.periodo and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
                ),0) as saldo_mora
                ,ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
                inner join liquidaciones l on l.id = lc.liquidacion_id 
                where l.id = Liquidacion.id
                and lc.socio_id = LiquidacionSocioRendicion.socio_id
                and ifnull(lc.mutual_adicional_pendiente_id,0) <> 0
                ),0) as cargos_adicionales
                ,ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
                inner join liquidaciones l on l.id = lc.liquidacion_id 
                where l.id = Liquidacion.id
                and lc.socio_id = LiquidacionSocioRendicion.socio_id
                and lc.periodo_cuota = l.periodo and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
                ),0) 
                + ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
                inner join liquidaciones l on l.id = lc.liquidacion_id 
                where l.id = Liquidacion.id
                and lc.socio_id = LiquidacionSocioRendicion.socio_id
                and lc.periodo_cuota <> l.periodo and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
                ),0)
                + ifnull((select sum(saldo_actual) from liquidacion_cuotas lc
                inner join liquidaciones l on l.id = lc.liquidacion_id 
                where l.id = Liquidacion.id
                and lc.socio_id = LiquidacionSocioRendicion.socio_id
                and ifnull(lc.mutual_adicional_pendiente_id,0) <> 0
                ),0) as total_liquidado
                ,Organismo.concepto_1
                ,LiquidacionSocioRendicionStop.*
                from liquidacion_socio_rendiciones LiquidacionSocioRendicion
                inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionSocioRendicion.liquidacion_id
                inner join banco_rendicion_codigos BancoRendicionCodigo 
                on BancoRendicionCodigo.banco_id = LiquidacionSocioRendicion.banco_intercambio
                and BancoRendicionCodigo.codigo = LiquidacionSocioRendicion.status
                inner join socios Socio on Socio.id = LiquidacionSocioRendicion.socio_id
                inner join personas Persona on Persona.id = Socio.persona_id
                left join liquidacion_socio_rendicion_stops LiquidacionSocioRendicionStop on
                LiquidacionSocioRendicionStop.liquidacion_id = Liquidacion.id 
                and LiquidacionSocioRendicionStop.banco_id = LiquidacionSocioRendicion.banco_intercambio
                and LiquidacionSocioRendicionStop.socio_id = LiquidacionSocioRendicion.socio_id 
                left join global_datos Organismo on Organismo.id = LiquidacionSocioRendicionStop.nuevo_organismo
                where LiquidacionSocioRendicion.liquidacion_id = $liquidacionId
                and LiquidacionSocioRendicion.banco_intercambio = '$bancoId'
                and (BancoRendicionCodigo.calificacion_socio = 'MUTUCALISDEB' or BancoRendicionCodigo.codigo in ('R08','008'))
                group by LiquidacionSocioRendicion.socio_id 
                order by Persona.apellido,Persona.nombre;";
        return $this->query($sql);
    }
    
    
}
?>
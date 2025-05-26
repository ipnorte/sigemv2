<?php

/**
*
* liquidacion_socio_envio_registro.php
* @author adrian [* 02/03/2012]
*/

class LiquidacionSocioEnvioRegistroNoimputada extends MutualAppModel{
	
	var $name = 'LiquidacionSocioEnvioRegistroNoimputada';
	var $belongsTo = array('LiquidacionSocioEnvioNoimputada','LiquidacionSocioNoimputada');
	
	
	function getTotales($envio_id,$excluido = 0){
		$totales = array('IMPORTE' => 0, 'CANTIDAD' => 0);
		$sql = "SELECT SUM(importe_adebitar) as importe_adebitar,COUNT(*) as cantidad
				FROM liquidacion_socio_envio_registros as LiquidacionSocioEnvioRegistro
				WHERE LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = $envio_id
				AND LiquidacionSocioEnvioRegistro.excluido = $excluido;";
		$envio = $this->query($sql);
		if(empty($envio)) return $totales;
		$totales['IMPORTE'] = (isset($envio[0][0]['importe_adebitar']) ? $envio[0][0]['importe_adebitar'] : 0);
		$totales['CANTIDAD'] = (isset($envio[0][0]['cantidad']) ? $envio[0][0]['cantidad'] : 0);
		return $totales;
	}
	
	
	function getInfoParaReporte($id){
		
		$tmpResumeByTurno = array();

		
		$sql = "SELECT * FROM liquidacion_socio_envios as LiquidacionSocioEnvio WHERE LiquidacionSocioEnvio.id = $id";
		$diskette = $this->query($sql);
		
		if(empty($diskette)) return $tmpResumeByTurno;
		
		$tmpResumeByTurno['diskette'] =	$diskette[0]['LiquidacionSocioEnvio'];
		
		$sql = "SELECT 
					LiquidacionSocio.codigo_empresa,
					LiquidacionSocio.turno_pago,
					GlobalDato.concepto_1,
					LiquidacionTurno.descripcion AS turno,
					LiquidacionSocio.turno_pago
				FROM liquidacion_socio_envio_registros AS LiquidacionSocioEnvioRegistro
				INNER JOIN liquidacion_socios AS LiquidacionSocio ON (LiquidacionSocio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_id)
				INNER JOIN global_datos AS GlobalDato ON (GlobalDato.id = LiquidacionSocio.codigo_empresa)
				LEFT JOIN global_datos AS GlobalDato2 ON (GlobalDato2.id = LiquidacionSocio.turno_pago)
				INNER JOIN liquidacion_turnos AS LiquidacionTurno ON (LiquidacionTurno.codigo_empresa = LiquidacionSocio.codigo_empresa AND LiquidacionTurno.turno = LiquidacionSocio.turno_pago)
				WHERE 
				LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = $id
				GROUP BY LiquidacionSocio.turno_pago
				ORDER BY GlobalDato.concepto_1,LiquidacionTurno.descripcion";
		
		$turnos = $this->query($sql);
		
		if(empty($turnos)) return $tmpResumeByTurno;
		
		$DETALLE_ERRORES_GLOBAL = array();
		
		foreach($turnos as $turno):
		
			$sql = "SELECT 
						LiquidacionSocio.documento,
						LiquidacionSocio.apenom,
						LiquidacionSocio.registro,
						LiquidacionSocio.ultima_calificacion,
						LiquidacionSocio.socio_id,
						LiquidacionSocio.codigo_empresa,
						LiquidacionSocio.turno_pago,
						LiquidacionSocio.sucursal,
						LiquidacionSocio.nro_cta_bco,
						LiquidacionSocio.cbu,
						LiquidacionSocio.importe_dto,
						LiquidacionSocioEnvioRegistro.importe_adebitar,
						LiquidacionSocioEnvioRegistro.registro,
						LiquidacionSocioEnvioRegistro.excluido,
						LiquidacionSocioEnvioRegistro.motivo
					FROM liquidacion_socio_envio_registros AS LiquidacionSocioEnvioRegistro
					LEFT JOIN liquidacion_socios AS LiquidacionSocio ON (LiquidacionSocio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_id)
					INNER JOIN global_datos AS GlobalDato ON (GlobalDato.id = LiquidacionSocio.codigo_empresa)
					WHERE 
					LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = $id
					AND LiquidacionSocio.codigo_empresa = '".$turno['LiquidacionSocio']['codigo_empresa']."'
					AND LiquidacionSocio.turno_pago = '".$turno['LiquidacionSocio']['turno_pago']."'
					ORDER BY GlobalDato.concepto_1,LiquidacionSocio.apenom,LiquidacionSocio.registro";
		
			$datos = $this->query($sql);
			
			if(!empty($datos)):
			
				$codigoTurno = $turno['LiquidacionSocio']['turno_pago'];

				$tmpResumeByTurno['info_procesada_by_turno'][$codigoTurno]['descripcion'] = $turno['GlobalDato']['concepto_1'] . (!empty($turno['LiquidacionTurno']['turno']) ? " - " . $turno['LiquidacionTurno']['turno'] : "");
				
				$sociosOK = $sociosERROR = array();

				$IMPORTE_DTO = $IMPORTE_DEBITO_OK = $IMPORTE_DEBITO_ERROR = $CANT_OK = $CANT_ERROR = 0;
				
				foreach($datos as $dato):
				
					$tmp = array();
					
					$dato['LiquidacionSocio']['importe_adebitar'] = $dato['LiquidacionSocioEnvioRegistro']['importe_adebitar'];
					$dato['LiquidacionSocio']['error_cbu'] = $dato['LiquidacionSocioEnvioRegistro']['excluido'];
					$dato['LiquidacionSocio']['error_intercambio'] = $dato['LiquidacionSocioEnvioRegistro']['motivo'];
					$dato['LiquidacionSocio']['intercambio'] = $dato['LiquidacionSocioEnvioRegistro']['registro'];
					
					$tmp['LiquidacionSocio'] = $dato['LiquidacionSocio'];
					
					$IMPORTE_DTO += $tmp['LiquidacionSocio']['importe_dto'];
					
					if($dato['LiquidacionSocioEnvioRegistro']['excluido'] == 0){
						array_push($sociosOK, $tmp);
						$CANT_OK++;
						$IMPORTE_DEBITO_OK += $tmp['LiquidacionSocio']['importe_adebitar'];
					}else{
						array_push($sociosERROR, $tmp);
						array_push($DETALLE_ERRORES_GLOBAL, $tmp);
						$CANT_ERROR++;
						$IMPORTE_DEBITO_ERROR += $tmp['LiquidacionSocio']['importe_adebitar'];
					}
				
				endforeach;
				$tmpResumeByTurno['info_procesada_by_turno'][$codigoTurno]['importe_dto'] = round($IMPORTE_DTO,2);
				$tmpResumeByTurno['info_procesada_by_turno'][$codigoTurno]['cantidad_ok'] = $CANT_OK;
				$tmpResumeByTurno['info_procesada_by_turno'][$codigoTurno]['cantidad_errores'] = $CANT_ERROR;
				$tmpResumeByTurno['info_procesada_by_turno'][$codigoTurno]['importe_adebitar'] = round($IMPORTE_DEBITO_OK,2);
				$tmpResumeByTurno['info_procesada_by_turno'][$codigoTurno]['registros'] = $sociosOK;
				$tmpResumeByTurno['info_procesada_by_turno'][$codigoTurno]['errores'] = $sociosERROR;
			
			endif;
			
		endforeach;
		
		$tmpResumeByTurno['errores'] = $DETALLE_ERRORES_GLOBAL;
		
		return $tmpResumeByTurno;
		
	}	
	
	
	function getRegistrosByLoteByIdentificador($liquidacionEnvioId, $idDebito){
		$sql = "SELECT * FROM liquidacion_socio_envio_registros as LiquidacionSocioEnvioRegistro
				WHERE liquidacion_socio_envio_id = $liquidacionEnvioId AND identificador_debito LIKE '%$idDebito%'";
		$datos = $this->query($sql);
		return $datos;
	}
	
	
	function getRegistrosByLoteByNroCta($liquidacionEnvioId,$nroCta){
		$sql = "SELECT * FROM liquidacion_socio_envio_registros as LiquidacionSocioEnvioRegistro
		WHERE liquidacion_socio_envio_id = $liquidacionEnvioId AND registro LIKE '%$nroCta%'";
		$datos = $this->query($sql);
		return $datos;		
	}
	
	function getRegistrosByLote($liquidacionEnvioId,$bancoId){
		
		App::import('Model','config.Banco');
		$oBANCO = new Banco();
			
		App::import('Model','pfyj.Socio');
		$oSOCIO = new Socio();		
		
		if($bancoId == '99999'):
		
			$sql = "SELECT * FROM liquidacion_socio_envio_registros as LiquidacionSocioEnvioRegistro
					WHERE liquidacion_socio_envio_id = $liquidacionEnvioId
					and id not in
					(select LiquidacionSocioEnvioRegistro.id from
					liquidacion_socio_rendiciones as LiquidacionSocioRendicion,
					liquidacion_socio_envios as LiquidacionSocioEnvio,
					liquidacion_socio_envio_registros as LiquidacionSocioEnvioRegistro
					where LiquidacionSocioEnvio.id = $liquidacionEnvioId and
					LiquidacionSocioRendicion.liquidacion_id = LiquidacionSocioEnvio.liquidacion_id
					and LiquidacionSocioRendicion.importe_debitado = LiquidacionSocioEnvioRegistro.importe_adebitar
					and LiquidacionSocioEnvioRegistro.socio_id = LiquidacionSocioRendicion.socio_id
					and LiquidacionSocioRendicion.indica_pago = 1
					group by LiquidacionSocioEnvioRegistro.id)
					order by substr(LiquidacionSocioEnvioRegistro.registro,18,52);";
				
			$datos = $this->query($sql);
			if(!empty($datos)){	
				foreach ($datos as $id => $dato){
				$decode = $oBANCO->decodeStringDebitoMutual($dato['LiquidacionSocioEnvioRegistro']['registro']);
				$socio = $oSOCIO->getApenom($decode['socio_id']);
				$datos[$id]['decode'] = $decode;
				$datos[$id]['socio_apenom'] = $socio;
				$datos[$id]['socio_id'] = $decode['socio_id'];
					
				}
			}
			
// 			debug($datos);
// 			exit;
		
		endif;
		
		
		if($bancoId == '00011'):
		
			$sql = "SELECT * FROM liquidacion_socio_envio_registros as LiquidacionSocioEnvioRegistro
			WHERE liquidacion_socio_envio_id = $liquidacionEnvioId
			and id not in
			(select LiquidacionSocioEnvioRegistro.id from
			liquidacion_socio_rendiciones as LiquidacionSocioRendicion,
			liquidacion_socio_envios as LiquidacionSocioEnvio,
			liquidacion_socio_envio_registros as LiquidacionSocioEnvioRegistro
			where LiquidacionSocioEnvio.id = $liquidacionEnvioId and
			LiquidacionSocioRendicion.liquidacion_id = LiquidacionSocioEnvio.liquidacion_id
			and LiquidacionSocioRendicion.importe_debitado = LiquidacionSocioEnvioRegistro.importe_adebitar
			and LiquidacionSocioEnvioRegistro.socio_id = LiquidacionSocioRendicion.socio_id
			and LiquidacionSocioRendicion.indica_pago = 1
			group by LiquidacionSocioEnvioRegistro.id)
			order by substr(LiquidacionSocioEnvioRegistro.registro,8,11);";
			
			$datos = $this->query($sql);
			
			if(!empty($datos)){
				

					
				foreach ($datos as $id => $dato){
					$decode = $oBANCO->decodeStringDebitoBcoNacion($dato['LiquidacionSocioEnvioRegistro']['registro']);
					$socio = $oSOCIO->getApenom($decode['socio_id']);
					$datos[$id]['decode'] = $decode;
					$datos[$id]['socio_apenom'] = $socio;
					$datos[$id]['socio_id'] = $decode['socio_id'];
			
				}
			}
		
		
		endif;
		
// 		debug($datos);
		return $datos;
	}	
	
	
	
	function getResumenByEnvio($liquidacionEnvioId){
		
		$sql = "SELECT codigo_rendicion,IFNULL(descripcion_codigo,'') AS descripcion_codigo,COUNT(*)
				AS registros,SUM(importe_adebitar) AS importe
				FROM liquidacion_socio_envio_registros WHERE liquidacion_socio_envio_id = $liquidacionEnvioId
				GROUP BY IFNULL(codigo_rendicion,'')
				ORDER BY IFNULL(descripcion_codigo,'')";
		
		$datos = $this->query($sql);
		return $datos;
		
	}
	
}

?>
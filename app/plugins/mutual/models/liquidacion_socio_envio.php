<?php

/**
*
* liquidacion_socio_envio.php
* @author adrian [* 02/03/2012]
*/

class LiquidacionSocioEnvio extends MutualAppModel{
	
	var $name = 'LiquidacionSocioEnvio';
	var $hasMany = array('LiquidacionSocioEnvioRegistro');
	
	function getByLiquidacionId($liquidacionId,$cargarDetalle = true,$banco_id=null,$uuid = NULL){
		if(!$cargarDetalle) $this->unbindModel(array('hasMany' => array('LiquidacionSocioEnvioRegistro')));
		$conditions = array();
		$conditions['LiquidacionSocioEnvio.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocioEnvio.bloqueado'] = 0;
		if(!empty($banco_id)) $conditions['LiquidacionSocioEnvio.banco_id'] = $banco_id;
		if(!empty($uuid)) $conditions['LiquidacionSocioEnvio.uuid'] = $uuid;
		$registros = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionSocioEnvio.banco_nombre,LiquidacionSocioEnvio.created DESC')));
		if(!empty($uuid) && !empty($registros)){
			return $registros[0];
		}
		return $registros;
	}

	/**
	 * Valida la cabecera del archivo con el detalle de registros NO excluidos
	 * 
	 * @author adrian [05/03/2012]
	 * @param unknown_type $id
	 */
	function isValido($id){
		$envio = $this->getEnvio($id);
		if(empty($envio['LiquidacionSocioEnvioRegistro'])) return false;
		App::import('Model','mutual.LiquidacionSocioEnvioRegistro');
		$oLSER = new LiquidacionSocioEnvioRegistro();
		//saco los totales OK
		$detalle = $oLSER->getTotales($id);
		if($envio['LiquidacionSocioEnvio']['cantidad_registros'] != $detalle['CANTIDAD']) return false;
		if($envio['LiquidacionSocioEnvio']['importe_debito'] != $detalle['IMPORTE']) return false;
		return true;
	}
	
	
	function getEnvio($id){
		$envio = $this->read(null,$id);
		return $envio;
	}
	
	function getEnviosBySocioByPeriodo($socio_id,$periodo){
        $envios = array();
        $sql = "select
                Liquidacion.id,
                Liquidacion.periodo,
                Organismo.concepto_1,
                LiquidacionSocioEnvio.id,
                LiquidacionSocioEnvio.banco_id,
                LiquidacionSocioEnvio.banco_nombre,
                LiquidacionSocioEnvio.fecha_debito,
                LiquidacionSocioEnvio.archivo,
                LiquidacionSocioEnvio.user_created,                
                LiquidacionSocioEnvio.created,
                LiquidacionSocioEnvioRegistro.importe_adebitar,
                LiquidacionSocioEnvioRegistro.registro,
                LiquidacionSocioEnvioRegistro.excluido,
                LiquidacionSocioEnvioRegistro.motivo
                from liquidacion_socio_envio_registros as LiquidacionSocioEnvioRegistro
                inner join liquidacion_socio_envios LiquidacionSocioEnvio 
                on (LiquidacionSocioEnvio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id)
                inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionSocioEnvio.liquidacion_id)
                inner join global_datos Organismo on (Organismo.id = Liquidacion.codigo_organismo)
                where Liquidacion.periodo = '$periodo' and socio_id = $socio_id;";
        $datos = $this->query($sql);
        if(!empty($datos)){
            $envios = $datos;
        }
        return $envios;
    }
	
	function genDiskette($banco_id,$liquidacionId,$fechaDebito,$fechaPresenta,$empresas,$turnos,$parametrosGlobales = array()){
		$banco = parent::getBanco($banco_id);
		$SP = stripslashes(strip_tags(trim($banco['Banco']['metodo_str_encode'])));
		if(empty($SP)){return NULL;}
		$uuid = parent::generarPIN(20);
		$PARAMS = parse_ini_string(stripslashes(strip_tags(trim($banco['Banco']['parametros_intercambio']))));
		$SPCALL = "CALL $SP(
			'".$banco_id."',
			$liquidacionId,
			'".implode(',',$empresas)."',
			'".implode(',',$turnos)."',
			'".$fechaDebito."',
			'".$fechaPresenta."',
			'".(isset($_SESSION[$this->keyNameUserLogon]) ? $_SESSION[$this->keyNameUserLogon] : 'APLICACION_SERVER')."',
			'".$uuid."'
			".(!empty($parametrosGlobales) ? ",'".implode("','",$parametrosGlobales)."'" : "")."
			".(!empty($PARAMS) ? ",'".implode("','",$PARAMS)."'" : "")."
			)";
		$this->query($SPCALL);
		if(!empty($this->getDataSource()->error)){
			echo $this->getDataSource()->error;
			exit;
		}
		return $uuid;		


		// $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
		// $sql = sprintf("CALL SP_DISKETTE_BANCO_COMAFI(%s,%u,%s,%s,%s,%s,%s,%s,%s,%s,%s);",
		// 	"'".$banco_id."'", 
		// 	$liquidacionId,
		// 	"'".implode(',',$turnos)."'",
		// 	"'".$fechaDebito."'",
		// 	"'".$fechaPresenta."'",
		// 	"'".(isset($_SESSION[$this->keyNameUserLogon]) ? $_SESSION[$this->keyNameUserLogon] : 'APLICACION_SERVER')."'",
		// 	"'".$uuid."'",
		// 	"'".$DATOS_GLOBALES['comafi_empresa_codigo']."'",
		// 	"'".$DATOS_GLOBALES['comafi_empresa_cuit']."'",
		// 	"'".$DATOS_GLOBALES['comafi_empresa_prestacion']."'",
		// 	"'".$DATOS_GLOBALES['comafi_empresa_ctabco']."'"
		// );		
		// $this->query($sql);
		// return $uuid;
	}


	function getDiskette($liquidacionId,$uuid){
		$envio = $this->getByLiquidacionId($liquidacionId,FALSE,NULL,$uuid);
		$diskette = array('diskette');
		if(!empty($envio)){
			$diskette = array();
			$diskette['diskette']['uuid'] = $uuid;
			$diskette['diskette']['status'] = $envio['LiquidacionSocioEnvio']['status'];
			$diskette['diskette']['observaciones'] = $envio['LiquidacionSocioEnvio']['observaciones'];
			$diskette['diskette']['archivo'] = $envio['LiquidacionSocioEnvio']['archivo'];
			$diskette['diskette']['banco_intercambio'] = $envio['LiquidacionSocioEnvio']['banco_id'];
			$diskette['diskette']['banco_intercambio_nombre'] = $envio['LiquidacionSocioEnvio']['banco_nombre'];
			$diskette['diskette']['fecha_debito'] = $envio['LiquidacionSocioEnvio']['fecha_debito'];
			$diskette['diskette']['importe_debito'] = $envio['LiquidacionSocioEnvio']['importe_debito'];
			$diskette['diskette']['cantidad_registros'] = $envio['LiquidacionSocioEnvio']['cantidad_registros'];
			$diskette['diskette']['lote'] = $envio['LiquidacionSocioEnvio']['lote'];
			$diskette['diskette']['cabecera'] = null;
			$diskette['diskette']['pie'] = null;
			$diskette['diskette']['longitud_registro'] = $envio['LiquidacionSocioEnvio']['longitud_registro'];

			//resumen operativo
			$SQL_RESUMEN = "/*SELECT * FROM (select 
							IF(LiquidacionTurno.codigo_empresa IS NOT NULL,LiquidacionSocio.turno_pago,LiquidacionSocio.turno_pago) as turno_pago,
							CONCAT(TRIM(IFNULL(LiquidacionTurno.empresa,'*** TURNO/s NO ASOCIADO A LA LIQUIDACION ***')),
							IF(LiquidacionTurno.descripcion IS NOT NULL,CONCAT(' - ', LiquidacionTurno.descripcion), '')) AS turno_pago_desc,							
							COUNT(*) AS liquidados,
							SUM(LiquidacionSocio.importe_dto) AS importe_dto,
							SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar, 'TOTAL' AS tipo
							from liquidacion_socio_envio_registros LiquidacionSocioEnvioRegistro
							inner join liquidacion_socios LiquidacionSocio on LiquidacionSocio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_id
							left join (select lt.codigo_empresa,lt.turno,lt.descripcion,Empresa.concepto_1 as empresa 
							from liquidacion_turnos lt
							LEFT JOIN global_datos as Empresa on (Empresa.id = lt.codigo_empresa)
							group by lt.codigo_empresa,lt.turno) as LiquidacionTurno on (LiquidacionTurno.codigo_empresa = 
							LiquidacionSocio.codigo_empresa AND LiquidacionTurno.turno = 
							LiquidacionSocio.turno_pago)							
							where LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = ".$envio['LiquidacionSocioEnvio']['id']." and LiquidacionSocio.diskette = 1
							GROUP BY LiquidacionSocio.turno_pago
							) T1
							union*/
							SELECT * FROM (select 
							IF(LiquidacionTurno.codigo_empresa IS NOT NULL,LiquidacionSocio.turno_pago,LiquidacionSocio.turno_pago) as turno_pago,
							CONCAT(TRIM(IFNULL(LiquidacionTurno.empresa,'*** TURNO/s NO ASOCIADO A LA LIQUIDACION ***')),
							IF(LiquidacionTurno.descripcion IS NOT NULL,CONCAT(' - ', LiquidacionTurno.descripcion), '')) AS turno_pago_desc,							
							COUNT(*) AS liquidados,
							SUM(LiquidacionSocio.importe_dto) AS importe_dto,
							SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar, 'OK' AS tipo
							from liquidacion_socio_envio_registros LiquidacionSocioEnvioRegistro
							inner join liquidacion_socios LiquidacionSocio on LiquidacionSocio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_id
							left join (select lt.codigo_empresa,lt.turno,lt.descripcion,Empresa.concepto_1 as empresa 
							from liquidacion_turnos lt
							LEFT JOIN global_datos as Empresa on (Empresa.id = lt.codigo_empresa)
							group by lt.codigo_empresa,lt.turno) as LiquidacionTurno on (LiquidacionTurno.codigo_empresa = 
							LiquidacionSocio.codigo_empresa AND LiquidacionTurno.turno = 
							LiquidacionSocio.turno_pago)							
							where LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = ".$envio['LiquidacionSocioEnvio']['id']." and LiquidacionSocio.diskette = 1
							AND LiquidacionSocio.error_cbu = 0
							GROUP BY LiquidacionSocio.turno_pago) T2
							/*union
							SELECT * FROM (SELECT 
							IF(LiquidacionTurno.codigo_empresa IS NOT NULL,LiquidacionSocio.turno_pago,LiquidacionSocio.turno_pago) as turno_pago,
							CONCAT(TRIM(IFNULL(LiquidacionTurno.empresa,'*** TURNO/s NO ASOCIADO A LA LIQUIDACION ***')),
							IF(LiquidacionTurno.descripcion IS NOT NULL,CONCAT(' - ', LiquidacionTurno.descripcion), '')) AS turno_pago_desc,							
							COUNT(*) AS liquidados,
							SUM(LiquidacionSocio.importe_dto) AS importe_dto,
							SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar, 'ERROR' AS tipo 
							from liquidacion_socio_envio_registros LiquidacionSocioEnvioRegistro
							inner join liquidacion_socios LiquidacionSocio on LiquidacionSocio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_id
							left join (select lt.codigo_empresa,lt.turno,lt.descripcion,Empresa.concepto_1 as empresa 
							from liquidacion_turnos lt
							LEFT JOIN global_datos as Empresa on (Empresa.id = lt.codigo_empresa)
							group by lt.codigo_empresa,lt.turno) as LiquidacionTurno on (LiquidacionTurno.codigo_empresa = 
							LiquidacionSocio.codigo_empresa AND LiquidacionTurno.turno = LiquidacionSocio.turno_pago)							
							where LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = ".$envio['LiquidacionSocioEnvio']['id']." and LiquidacionSocio.diskette = 1
							AND LiquidacionSocio.error_cbu = 1
							GROUP BY LiquidacionSocio.turno_pago) T3*/";

			$SQL_RESUMEN = "select 
							IF(LiquidacionTurno.codigo_empresa IS NOT NULL,LiquidacionSocio.turno_pago,LiquidacionSocio.turno_pago) as turno_pago,
							CONCAT(TRIM(IFNULL(LiquidacionTurno.empresa,'*** TURNO/s NO ASOCIADO A LA LIQUIDACION ***')),
							IF(LiquidacionTurno.descripcion IS NOT NULL,CONCAT(' - ', LiquidacionTurno.descripcion), '')) AS turno_pago_desc,							
							COUNT(*) AS liquidados,
							SUM(LiquidacionSocio.importe_dto) AS importe_dto,
							SUM(LiquidacionSocio.importe_adebitar) AS importe_adebitar, 'OK' AS tipo
							from liquidacion_socio_envio_registros LiquidacionSocioEnvioRegistro
							inner join liquidacion_socios LiquidacionSocio on LiquidacionSocio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_id
							left join (select lt.codigo_empresa,lt.turno,lt.descripcion,Empresa.concepto_1 as empresa 
							from liquidacion_turnos lt
							LEFT JOIN global_datos as Empresa on (Empresa.id = lt.codigo_empresa)
							group by lt.codigo_empresa,lt.turno) as LiquidacionTurno on (LiquidacionTurno.codigo_empresa = 
							LiquidacionSocio.codigo_empresa AND LiquidacionTurno.turno = 
							LiquidacionSocio.turno_pago)							
							where LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = ".$envio['LiquidacionSocioEnvio']['id']." and LiquidacionSocio.diskette = 1
							AND LiquidacionSocio.error_cbu = 0
							GROUP BY LiquidacionSocio.turno_pago";

			$diskette['resumen_operativo'] = $this->query($SQL_RESUMEN);
			// $diskette['resumen_operativo_ok'] = Set::extract("{n}[tipo=OK]",$diskette['resumen_operativo']);

			$SQL_OK = "	SELECT LiquidacionSocio.documento, LiquidacionSocio.apenom, LiquidacionSocio.cuit_cuil,
							LiquidacionSocio.registro, LiquidacionSocio.ultima_calificacion, 
							LiquidacionSocio.socio_id, LiquidacionSocio.turno_pago, LiquidacionSocio.sucursal, 
							LiquidacionSocio.nro_cta_bco, LiquidacionSocio.cbu, LiquidacionSocio.importe_dto, 
							LiquidacionSocio.importe_adebitar, LiquidacionSocio.intercambio, LiquidacionSocio.error_cbu, 
							LiquidacionSocio.error_intercambio, GlobalDato.concepto_1, GlobalDato.concepto_2, 
							GlobalDato.logico_1 FROM 
							liquidacion_socio_envio_registros LiquidacionSocioEnvioRegistro
							inner join liquidacion_socios LiquidacionSocio on LiquidacionSocio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_id
							INNER JOIN global_datos AS GlobalDato ON (LiquidacionSocio.codigo_empresa = GlobalDato.id) 
							WHERE LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = ".$envio['LiquidacionSocioEnvio']['id']." 
							AND LiquidacionSocio.diskette = 1 
							AND LiquidacionSocio.error_cbu = 0
							ORDER BY GlobalDato.concepto_1 ASC, LiquidacionSocio.apenom ASC, 
							LiquidacionSocio.registro ASC ";
			
			$diskette['registros_ok'] = $this->query($SQL_OK);

			$SQL_ERROR = "	SELECT LiquidacionSocio.documento, LiquidacionSocio.apenom, LiquidacionSocio.cuit_cuil,
							LiquidacionSocio.registro, LiquidacionSocio.ultima_calificacion, 
							LiquidacionSocio.socio_id, LiquidacionSocio.turno_pago, LiquidacionSocio.sucursal, 
							LiquidacionSocio.nro_cta_bco, LiquidacionSocio.cbu, LiquidacionSocio.importe_dto, 
							LiquidacionSocio.importe_adebitar, LiquidacionSocio.intercambio, LiquidacionSocio.error_cbu, 
							LiquidacionSocio.error_intercambio, GlobalDato.concepto_1, GlobalDato.concepto_2, 
							GlobalDato.logico_1 FROM 
							liquidacion_socio_envio_registros LiquidacionSocioEnvioRegistro
							inner join liquidacion_socios LiquidacionSocio on LiquidacionSocio.id = LiquidacionSocioEnvioRegistro.liquidacion_socio_id
							INNER JOIN global_datos AS GlobalDato ON (LiquidacionSocio.codigo_empresa = GlobalDato.id) 
							WHERE LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id = ".$envio['LiquidacionSocioEnvio']['id']." 
							AND LiquidacionSocio.diskette = 1 
							AND LiquidacionSocio.error_cbu = 1
							ORDER BY GlobalDato.concepto_1 ASC, LiquidacionSocio.apenom ASC, 
							LiquidacionSocio.registro ASC ";
			
			$diskette['registros_error'] = $this->query($SQL_ERROR);			
		}

		// debug($diskette['registros_error']);
		// debug($diskette);
		// exit;

		return $diskette;
	}


}

?>
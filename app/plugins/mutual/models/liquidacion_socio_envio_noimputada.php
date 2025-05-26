<?php

/**
*
* liquidacion_socio_envio.php
* @author adrian [* 02/03/2012]
*/

class LiquidacionSocioEnvioNoimputada extends MutualAppModel{
	
	var $name = 'LiquidacionSocioEnvioNoimputada';
	var $hasMany = array('LiquidacionSocioEnvioRegistroNoimputada');
	
	function getByLiquidacionId($liquidacionId,$cargarDetalle = true,$banco_id=null){
		if(!$cargarDetalle) $this->unbindModel(array('hasMany' => array('LiquidacionSocioEnvioRegistroNoimputada')));
		$conditions = array();
		$conditions['LiquidacionSocioEnvioNoimputada.liquidacion_id'] = $liquidacionId;
		$conditions['LiquidacionSocioEnvioNoimputada.bloqueado'] = 0;
		if(!empty($banco_id)) $conditions['LiquidacionSocioEnvioNoimputada.banco_id'] = $banco_id;
		$registros = $this->find('all',array('conditions' => $conditions,'order' => array('LiquidacionSocioEnvioNoimputada.banco_nombre,LiquidacionSocioEnvioNoimputada.created DESC')));
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
		if(empty($envio['LiquidacionSocioEnvioRegistroNoimputada'])) return false;
		App::import('Model','mutual.LiquidacionSocioEnvioRegistroNoimputada');
		$oLSER = new LiquidacionSocioEnvioRegistro();
		//saco los totales OK
		$detalle = $oLSER->getTotales($id);
		if($envio['LiquidacionSocioEnvioNoimputada']['cantidad_registros'] != $detalle['CANTIDAD']) return false;
		if($envio['LiquidacionSocioEnvioNoimputada']['importe_debito'] != $detalle['IMPORTE']) return false;
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
                LiquidacionSocioEnvioNoimputada.id,
                LiquidacionSocioEnvioNoimputada.banco_id,
                LiquidacionSocioEnvioNoimputada.banco_nombre,
                LiquidacionSocioEnvioNoimputada.fecha_debito,
                LiquidacionSocioEnvioNoimputada.archivo,
                LiquidacionSocioEnvioNoimputada.user_created,                
                LiquidacionSocioEnvioNoimputada.created,
                LiquidacionSocioEnvioRegistroNoimputada.importe_adebitar,
                LiquidacionSocioEnvioRegistroNoimputada.registro 
                from liquidacion_socio_envio_registro_noimputadas as LiquidacionSocioEnvioRegistroNoimputada
                inner join liquidacion_socio_envios_noimputadas LiquidacionSocioEnvioNoimputada 
                on (LiquidacionSocioEnvioNoimputada.id = LiquidacionSocioEnvioRegistroNoimputada.liquidacion_socio_envio_id)
                inner join liquidaciones Liquidacion on (Liquidacion.id = LiquidacionSocioEnvioNoimputada.liquidacion_id)
                inner join global_datos Organismo on (Organismo.id = Liquidacion.codigo_organismo)
                where Liquidacion.periodo = '$periodo' and socio_id = $socio_id;";
        $datos = $this->query($sql);
        if(!empty($datos)){
            $envios = $datos;
        }
        return $envios;
    }
	
	
}

?>
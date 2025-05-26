<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php tmp_arma_intercambio -app /home/adrian/Desarrollo/www/sigem/app/
 *
 */

class TmpArmaIntercambioShell extends Shell{
	
	var $uses 		= array(
								'Mutual.LiquidacionIntercambioRegistro',
								'Mutual.LiquidacionSocio',
								'Mutual.Liquidacion',
								'Mutual.LiquidacionSocioRendicion',
								'Config.Banco'
							);

	function main(){
		
		$liquidaciones = $this->Liquidacion->find('all');
		
		if(empty($liquidaciones)) return;
		
		foreach($liquidaciones as $liquidacion):
		
			$this->LiquidacionSocioRendicion->deleteAll("LiquidacionSocioRendicion.liquidacion_id = " . $liquidacion['Liquidacion']['id']);
			$conditions = array();
			$conditions['LiquidacionIntercambioRegistro.liquidacion_id'] = $liquidacion['Liquidacion']['id'];
			$archivos = $this->LiquidacionIntercambioRegistro->find('all',array('conditions' => $conditions));
			if(!empty($archivos)):
				foreach($archivos as $archivo):
					$this->__procesar($archivo,$liquidacion);
				endforeach;
			endif;
		endforeach;

		
	
	}
	
	
	
	function __procesar($archivo,$liquidacion){
		$rendicionSocio = array();

		$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_id'] = $archivo['LiquidacionIntercambioRegistro']['liquidacion_id'];
		$rendicionSocio['LiquidacionSocioRendicion']['codigo_organismo'] = $liquidacion['Liquidacion']['codigo_organismo'];
		$rendicionSocio['LiquidacionSocioRendicion']['registro'] = $archivo['LiquidacionIntercambioRegistro']['registro'];
		$rendicionSocio['LiquidacionSocioRendicion']['periodo'] = $liquidacion['Liquidacion']['periodo'];
		$rendicionSocio['LiquidacionSocioRendicion']['banco_intercambio'] = $archivo['LiquidacionIntercambioRegistro']['banco_intercambio'];
		$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_intercambio_id'] = $archivo['LiquidacionIntercambioRegistro']['liquidacion_intercambio_id'];
		
		#############################################################################################
		#BANCO DE CORDOBA
		#############################################################################################
		
		if($archivo['LiquidacionIntercambioRegistro']['banco_intercambio'] == '00020'){
			$registro = $this->Banco->decodeStringDebitoBcoCba($archivo['LiquidacionIntercambioRegistro']['registro']);
			foreach($registro as $key => $value){
				$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
			}
			
			$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
						'LiquidacionSocio.liquidacion_id' => $archivo['LiquidacionIntercambioRegistro']['liquidacion_id'],
						'LiquidacionSocio.documento' => $rendicionSocio['LiquidacionSocioRendicion']['documento']
			)));
			$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);
			if($rendicionSocio['LiquidacionSocioRendicion']['indica_pago'] == 1) $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = (!empty($socio[0]['LiquidacionSocio']['orden_descuento_cobro_id']) ? $socio[0]['LiquidacionSocio']['orden_descuento_cobro_id'] : 0);
			else $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = 0;
		}
		
		#############################################################################################
		#STANDAR BANK
		#############################################################################################
		
		if($archivo['LiquidacionIntercambioRegistro']['banco_intercambio'] == '00430'){
			$registro = $this->Banco->decodeStringDebitoStandarBank($archivo['LiquidacionIntercambioRegistro']['registro']);
			foreach($registro as $key => $value){
				$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
			}
			
			$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
						'LiquidacionSocio.liquidacion_id' => $archivo['LiquidacionIntercambioRegistro']['liquidacion_id'],
						'LiquidacionSocio.documento' => $rendicionSocio['LiquidacionSocioRendicion']['documento']
			)));
			$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);
			if($rendicionSocio['LiquidacionSocioRendicion']['indica_pago'] == 1) $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = (!empty($socio[0]['LiquidacionSocio']['orden_descuento_cobro_id']) ? $socio[0]['LiquidacionSocio']['orden_descuento_cobro_id'] : 0);
			else $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = 0;

		}
		
		#############################################################################################
		#CAJA DE JUBILACIONES
		#############################################################################################
		
		if($archivo['LiquidacionIntercambioRegistro']['banco_intercambio'] == '99999' && $liquidacion['Liquidacion']['codigo_organismo'] == 'MUTUCORG7701'){
			$registro = $this->Banco->decodeStringDebitoCJP($archivo['LiquidacionIntercambioRegistro']['registro']);
			foreach($registro as $key => $value){
				$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
			}
			$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
						'LiquidacionSocio.liquidacion_id' => $archivo['LiquidacionIntercambioRegistro']['liquidacion_id'],
						'LiquidacionSocio.tipo' => $rendicionSocio['LiquidacionSocioRendicion']['tipo'],
						'LiquidacionSocio.nro_ley' => $rendicionSocio['LiquidacionSocioRendicion']['nro_ley'],
						'LiquidacionSocio.nro_beneficio' => $rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio'],
						'LiquidacionSocio.sub_beneficio' => $rendicionSocio['LiquidacionSocioRendicion']['sub_beneficio'],
						'LiquidacionSocio.codigo_dto' => $rendicionSocio['LiquidacionSocioRendicion']['codigo_dto'],
						'LiquidacionSocio.sub_codigo' => $rendicionSocio['LiquidacionSocioRendicion']['sub_codigo'],
			)));
			$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);
			if($rendicionSocio['LiquidacionSocioRendicion']['indica_pago'] == 1) $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = (!empty($socio[0]['LiquidacionSocio']['orden_descuento_cobro_id']) ? $socio[0]['LiquidacionSocio']['orden_descuento_cobro_id'] : 0);
			else $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = 0;
		}
		
		#############################################################################################
		#ANSES
		#############################################################################################
		
		if($archivo['LiquidacionIntercambioRegistro']['banco_intercambio'] == '99999' && $liquidacion['Liquidacion']['codigo_organismo'] == 'MUTUCORG6601'){
			$registro = $this->Banco->decodeStringDebitoANSES($archivo['LiquidacionIntercambioRegistro']['registro']);
			foreach($registro as $key => $value){
				$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
			}
			$socio = $this->LiquidacionSocio->find('all',array('conditions' => array(
						'LiquidacionSocio.liquidacion_id' => $archivo['LiquidacionIntercambioRegistro']['liquidacion_id'],
						'LiquidacionSocio.nro_beneficio' => $rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio'],
						'LiquidacionSocio.codigo_dto' => $rendicionSocio['LiquidacionSocioRendicion']['codigo_dto'],
			)));
			$rendicionSocio['LiquidacionSocioRendicion']['socio_id'] = (!empty($socio[0]['LiquidacionSocio']['socio_id']) ? $socio[0]['LiquidacionSocio']['socio_id'] : 0);
			if($rendicionSocio['LiquidacionSocioRendicion']['indica_pago'] == 1) $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = (!empty($socio[0]['LiquidacionSocio']['orden_descuento_cobro_id']) ? $socio[0]['LiquidacionSocio']['orden_descuento_cobro_id'] : 0);
			else $rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = 0;
		}
		$this->out($archivo['LiquidacionIntercambioRegistro']['registro']);
		$this->LiquidacionSocioRendicion->id = 0;
		$this->LiquidacionSocioRendicion->save($rendicionSocio);		
	}
	
	
	
}
?>
<?php
/**
 * -------------------------------------------------
 * ---- PROCESO SHELL DE RELIQUIDACION DE DEUDA -----
 * -------------------------------------------------
 * 
 *	PARAMETROS PASADOS:
 *	P1: liquidacion_id
 *	P2: reprocesar cuotas
 *
 *	LANZADORES (depende de la instalacion del PHP)
 *	/usr/bin/php5 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reliquida_deuda 123 0 -app /home/adrian/Desarrollo/www/sigem/app/
 *	/opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reliquida_deuda 123 0 -app /home/adrian/Desarrollo/www/sigem/app/
 *	/usr/bin/php5 /var/www/sigem/cake/console/cake.php reliquida_deuda 123 0 -app /var/www/sigem/app/
 *
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 */

class ReliquidaDeudaShell extends Shell{
	
	var $tasks 		= array('Error');
	var $uses 		= array('Mutual.OrdenDescuento','Mutual.OrdenDescuentoCuota','Pfyj.Socio','Config.GlobalDato','Mutual.MutualProducto','Mutual.LiquidacionSocio');
	var $liquidacionID = 0;
	var $reprocesa_deuda = false;

	function main(){
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}		
		
		$this->liquidacionID = $this->args[0];
		$this->reprocesa_deuda = (empty($this->args[1]) ? false : ($this->args[1] == 1 ? true : false));

//		DEBUG($this->reprocesa_deuda);

		$periodo = $this->__getCampo('periodo');
		$organismo = $this->__getCampo('codigo_organismo');
		
		//controlar si ya esta corriendo
		if($this->__getCampo('bloqueada') == 1){
			$this->out("LIQUIDACION BLOQUEADA");
			return;			
		}
		
		$this->__setCampo('bloqueada',1);		

		
		App::import('Model','Mutual.LiquidacionCuota');
		$oLIQCUOTA = new LiquidacionCuota();		
		
		if($this->reprocesa_deuda):
			#CARGO LOS SOCIOS QUE VOY A LIQUIDAR
			App::import('Model','Pfyj.Socio');
			$oSocio = new Socio();
			$oSocio->unbindModel(array('belongsTo' => array('Persona')));
			$sql = "SELECT Socio.id FROM socios AS Socio";
			$socios = $oSocio->query($sql);
			$socios = Set::extract('/Socio/id',$socios);
		else:
			
			$conditions = array();
			$conditions['LiquidacionCuota.liquidacion_id'] = $this->liquidacionID;
			$socios = $oLIQCUOTA->find('all',array('conditions' => $conditions, 'fields' => array('LiquidacionCuota.socio_id'),'group' => array('LiquidacionCuota.socio_id')));
			$socios = Set::extract('/LiquidacionCuota/socio_id',$socios);
		
		endif;
		
//		debug($socios);
//		$this->__setCampo('bloqueada',0);
//		return;
		
		$ERROR = array();
		$STOP = 0;
		
		if(!empty($socios)):
			
			$total = count($socios);
//			$asinc->setTotal($total);
			$i = 0;
			
			foreach($socios as $socio_id):	
			
				$porc = intval(round($i/$total,2) * 100);
				

				$this->out($i."\t".$porc." %"."\tPROCESANDO\t".$socio_id);

//				$socio_id = 3963;
				
				$resumen = $oLIQCUOTA->getInfoDto_AMAN($this->liquidacionID,$socio_id,$periodo,$organismo);
				
				if(!empty($resumen)):
				
					foreach($resumen as $liquidacionSocio):
					
						$string = $this->toStringLiquiCuota($liquidacionSocio);
//						$this->out($string);
						
						error_log($string."\r\n", 3,'/home/adrian/Desarrollo/tmp/prueba.log');
					
					endforeach;
				
				endif;
				
//				break;
				
				
				
//				debug($LiquidacionSocios);
			
//				$asinc->actualizar($i,$total,"$i / $total - LIQUIDANDO SOCIO #$socio_id");			
//				$ERROR = $this->LiquidacionSocio->liquidar($socio_id,$periodo,$organismo,$this->liquidacionID);

//				if(empty($ERROR) && $ERROR[0]==1):
//					$asinc->actualizar($i,$total,"$i / $total - LIQUIDANDO SOCIO #$socio_id - ERROR: " .(!empty($ERROR[1]) ? $ERROR[1] : "ERROR GRAVE"));
//					break;
//				endif;
//				if($asinc->detenido()){
//					$STOP = 1;
//					$this->__setCampo('bloqueada',0);
//					break;
//				}
				$i++;				
			
			endforeach;
			
			if($STOP == 0):
//				$asinc->actualizar(100,100,"ACTUALIZANDO CABECERA DE LIQUIDACION...");
//				#actualizo la cabecera
//				$this->__actualizarCabecera($this->liquidacionID,$pid);
//				$this->__setCampo('bloqueada',0);
			endif;

			if($STOP == 0):
//				$asinc->actualizar(100,100,"FINALIZANDO...");
//				$asinc->fin("**** PROCESO FINALIZADO ****");	
				$this->__setCampo('bloqueada',0);	
			endif;

		else:

//			$asinc->actualizar(100,100,"NO EXISTEN REGISTROS PARA PROCESAR...");
			
		endif;	
		
	
	}
	#FIN PROCESO DE LIQUIDACION
	
//	###############################################################################################################################
//	# METODOS DE LA CLASE
//	###############################################################################################################################
//	
	/**
	 * devuelve un campo especificado de la tabla liquidaciones
	 * @param $field
	 * @return contenido del campo
	 */
	function __getCampo($field){
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$liquidacion = $oLQ->read($field,$this->liquidacionID);
		return $liquidacion['Liquidacion'][$field];
	}	
	
	/**
	 * setea un valor de un campo para la liquidacion
	 * @param $field
	 * @param $value
	 */
	function __setCampo($field,$value){
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$oLQ->id = $this->liquidacionID;
		return $oLQ->saveField($field,$value);
	}	
	/**
	 * genera la cabecera de la liquidacion
	 * @param $periodo
	 * @return unknown_type
	 */
	function __generarCabeceraLiquidacion($periodo,$organismo){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		//verifico que no exista
		$liq = $oLiq->find('all',array('conditions' => array('Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.periodo' => $periodo,'Liquidacion.en_proceso' => 1),'fields' => array('Liquidacion.id'),'limit' => 1));
		if(!empty($liq)) return $liq['Liquidacion']['id']; 
		$data = array('Liquidacion' => array(
				'periodo' => $periodo,
				'codigo_organismo' => $organismo
		));
		if($oLiq->save($data))	return $oLiq->getLastInsertID();
		else return null;
		
	}
	/**
	 * actualiza la cabecera de la liquidacion
	 * @param $id
	 * @param $pid
	 * @return unknown_type
	 */
	function __actualizarCabecera($id,$pid){
		App::import('Model','Mutual.Liquidacion');
		App::import('Model','Mutual.LiquidacionCuota');
		App::import('Model','Mutual.LiquidacionSocio');
		
		$oLiq = new Liquidacion();
		$oDet = new LiquidacionCuota();	
		$oLS = new LiquidacionSocio();	

		$liquidacion = $oLiq->read(null,$id);
		
		$liquidacion['Liquidacion']['pid'] = $pid;
		$liquidacion['Liquidacion']['cuota_social_vencida'] = $oDet->getTotalCuotaSocial($id,1);
		$liquidacion['Liquidacion']['cuota_social_periodo'] = $oDet->getTotalCuotaSocial($id,0);

		$liquidacion['Liquidacion']['deuda_vencida'] = $oDet->getTotalDeuda($id,1);
		$liquidacion['Liquidacion']['deuda_periodo'] = $oDet->getTotalDeuda($id,0);	

		$liquidacion['Liquidacion']['total_vencido'] = $oDet->getTotalVencido($id,1);
		$liquidacion['Liquidacion']['total_periodo'] = $oDet->getTotalVencido($id,0);
		$liquidacion['Liquidacion']['total'] = $oDet->getTotal($id);
		$liquidacion['Liquidacion']['en_proceso'] = 0;	
		
		$liquidacion['Liquidacion']['registros_enviados'] = $oLS->getTotalRegistros($id);
		$liquidacion['Liquidacion']['altas'] = $oLS->getTotalAltas($id,$liquidacion['Liquidacion']['codigo_organismo']);
		
		return $oLiq->save($liquidacion);
	}
//	/**
//	 * graba el detalle de la liquidacion en base a una cuota pasada por parametro
//	 * si el saldo de la cuota es negativo no la liquida
//	 * @param $id
//	 * @param $cuota
//	 * @param $periodoLiquidado
//	 * @return unknown_type
//	 */
//	function __grabarDetalleLiquidacion($id,$cuota,$periodoLiquidado){
//		App::import('Model','Mutual.LiquidacionCuota');
//		$oDet = new LiquidacionCuota();
//		return $oDet->generarDetalleLiquidacionCuota($id,$periodoLiquidado,$cuota);	
//	}
//	
//
	/**
	 * Borra la liquidacion abierta para un periodo y organismo
	 * @param $periodo
	 * @param $organismo
	 * @return unknown_type
	 */
	function __borrarLiquidacion($periodo,$organismo){
		App::import('Model','Mutual.Liquidacion');
		App::import('Model','Mutual.LiquidacionCuota');
		App::import('Model','Mutual.LiquidacionSocio');
		App::import('Model','Mutual.MutualAdicionalPendiente');
		App::import('Model','Mutual.LiquidacionIntercambio');
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		
		$oLiq = new Liquidacion();
		$oDet = new LiquidacionCuota();
		$oLSoc = new LiquidacionSocio();
		$oAdic = new MutualAdicionalPendiente();
		$oLI = new LiquidacionIntercambio();
		$oSR = new LiquidacionSocioRendicion();

		$liquidaciones = $oLiq->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.cerrada' => 0)));
		foreach($liquidaciones as $liquidacion){
			if($liquidacion['Liquidacion']['cerrada'] == 0){
				$oDet->deleteAll("LiquidacionCuota.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oLSoc->deleteAll("LiquidacionSocio.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oAdic->deleteAll("MutualAdicionalPendiente.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oLI->borrarIntercambioByLiquidacion($liquidacion['Liquidacion']['id']);
				$oSR->deleteAll("LiquidacionSocioRendicion.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oLiq->del($liquidacion['Liquidacion']['id'],false);
			}
		}
	}
	
	/**
	 * verifica si esta procesandose la liquidacion para el periodo y organismo en otra instancia
	 * @param unknown_type $periodo
	 * @param unknown_type $organismo
	 */
	function __isBloqueada($periodo,$organismo){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$cantidad = $oLiq->find('count',array('conditions'=> array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.bloqueada' => 1)));
		if($cantidad == 0) return false;
		else return true;
	}

	/**
	 * Chequea que no exista una liquidacion abierta para el periodo / organismo
	 * si existe devuelve el ID para reliquidar, si NO existe devuelve 0
	 * @param unknown_type $periodo
	 * @param unknown_type $organismo
	 * @return unknown_type
	 */
	function ifExists($periodo,$organismo){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();		
		$conditions = array();
		$conditions['Liquidacion.periodo'] = $periodo;
		$conditions['Liquidacion.codigo_organismo'] = $organismo;
		$conditions['Liquidacion.cerrada'] = 0;
		$conditions['Liquidacion.imputada'] = 0;
		$conditions['Liquidacion.archivos_procesados'] = 0;
		$liquidacion = $oLiq->find('all',array('conditions'=> $conditions));
		if(empty($liquidacion)) return 0;
		if(isset($liquidacion['Liquidacion']['id']) && $liquidacion['Liquidacion']['id'] != 0) return $liquidacion['Liquidacion']['id'];
		else return 0;
	}
	
	
	function toStringLiquiCuota($dato){
		$string = "";
		if(!empty($dato)):
			$string = implode("|",$dato['LiquidacionCuota']);
		endif;
		return $string;
	}
	
	
}
?>
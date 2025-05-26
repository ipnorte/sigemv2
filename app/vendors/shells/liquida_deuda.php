<?php
/**
 * -------------------------------------------------
 * ---- PROCESO SHELL DE LIQUIDACION DE DEUDA -----
 * -------------------------------------------------
 * 
 * El proceso consta de 4 pasos 
 * 
 * PASO 1: 	Liquida los conceptos permanentes del socio para el periodo que se esta procesando
 * PASO 2: 	Procesa las cuotas adeudadas del socio al periodo liquidado inclusive (ADEUDADAS Y SITUACION DE DEUDA).  
 * 			Por cada cuota va determinando el saldo.  Estas cuotas las va generando en la tabla liquidacion_cuotas
 * PASO 3:	Determina los adicionales que puede cargarse, por ejemplo: GASTO ADMINISTRATIVO CBU.  Estos gastos
 * 			estan configurados en la tabla mutual_adicionales.  Si el adicional esta configurado para que se devengue
 * 			automaticamente genera la cuota en la orden_descuento_cuotas. Si nó lo genera en la tabla mutual_adicional_pendientes
 * 			y en la liquidacion_cuotas, una vez que se procesa el pago se genera la cuota respectiva en la tabla orden_descuento_cuotas.
 * PASO 4: 	Procesa la liquidacion_cuotas y genera el resumen en la liquidacion_socios de donde se tienen que sacar los distintos archivos
 * 			para enviar a los organismos de retencion
 * 
 *
 *	PARAMETROS PASADOS:
 *	P1: PERIODO
 *	P2: ORGANISMO A LIQUIDAR
 *
 *	LANZADORES (depende de la instalacion del PHP)
 *	/usr/bin/php5.6 /home/adrian/trabajo/www/sigem/cake/console/cake.php liquida_deuda 31583 -app /home/adrian/trabajo/www/sigem/app/
 *	/opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php liquida_deuda 157 -app /home/adrian/Desarrollo/www/sigem/app/
 *	/usr/bin/php5 /var/www/sigem/cake/console/cake.php liquida_deuda 1 -app /var/www/sigem/app/
 *	/usr/bin/php5 /var/www/virtual/mutual22septiembre.com.ar/htdocs/sigem/cake/console/cake.php liquida_deuda 33 -app /var/www/virtual/mutual22septiembre.com.ar/htdocs/sigem/app/
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 */

class LiquidaDeudaShell extends Shell{
	
	var $tasks 		= array('Error');
	var $uses 		= array('Mutual.OrdenDescuento','Mutual.OrdenDescuentoCuota','Pfyj.Socio','Config.GlobalDato','Mutual.MutualProducto','Mutual.LiquidacionSocio');
	var $pid		= null;
	var $oASINC 	= null;
	var $liquidacionID = 0;

	function main(){
        
        Configure::write('debug',1);
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}		
		
		$pid = $this->args[0];
		$this->pid = $pid;
		
		$this->Error->pid = $pid;
		$this->Error->limpiarTabla();
		
//		$this->__limpiarTemportal($pid);
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid;

		$this->oASINC = $asinc;
		
		
		$this->__setCampo('bloqueada',1);	
        
        
//                $this->out("poraca");
//                return;
		
		##################################################################################################################
		# PROCESO MASIVO DE LIQUIDACION DE DEUDA
		##################################################################################################################
		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");
		
		$periodo 	= $asinc->getParametro('p1');
		$organismo 	= $asinc->getParametro('p2');
		$pre_imputacion 	= $asinc->getParametro('p3');
		
		//controlar si ya esta corriendo
		if($this->__isBloqueada($periodo,$organismo)){
			$asinc->actualizar(100,100,"PROCESO BLOQUEADO POR OTRO USUARIO....");
			return;			
		}
		if($this->__isCerrada($periodo,$organismo)){
			$asinc->actualizar(100,100,"LIQUIDACION CERRADA....");
			return;			
		}
		if($this->__isImputada($periodo,$organismo)){
			$asinc->actualizar(100,100,"LIQUIDACION IMPUTADA....");
			return;			
		}        
		$idLiquidacionExistente = $this->ifExists($periodo,$organismo);
//        $this->out($idLiquidacionExistente);
//		
		if($idLiquidacionExistente == 0):
		
			#borro la liquidacion si esta abierta
			#NO BORRA las cuotas generadas permanentes por la liquidacion
			$asinc->actualizar(3,100,"ESPERE, BORRANDO DATOS DE LA LIQUIDACION PREVIA DEL PERIODO ...");
			$this->__borrarLiquidacion($periodo,$organismo);
	
			#GENERO UNA CABECERA NUEVA
			$this->liquidacionID = $this->__generarCabeceraLiquidacion($periodo,$organismo,$pre_imputacion);
			
		else:
//		
			$this->liquidacionID = $idLiquidacionExistente;
            $asinc->actualizar(3,100,"ESPERE, BORRANDO DATOS DE LA LIQUIDACION PREVIA DEL PERIODO ...");
            $this->__borrarLiquidacion($periodo,$organismo,true);
            $this->__actualizarFechaCreated($idLiquidacionExistente);
//		
		endif;
        
		
		#CARGO LOS SOCIOS QUE VOY A LIQUIDAR
		$asinc->actualizar(1,100,"ESPERE, CARGANDO SOCIOS PARA LIQUIDAR ...");
		App::import('Model','Pfyj.Socio');
		$oSocio = new Socio();
		$oSocio->unbindModel(array('belongsTo' => array('Persona')));
		//$sql = "SELECT Socio.id FROM socios AS Socio";
        
		$sql = "SELECT Socio.id FROM socios AS Socio
				INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.socio_id = Socio.id)
				INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
				WHERE PersonaBeneficio.codigo_beneficio = '$organismo'
				GROUP BY Socio.id";
		
		$socios = $oSocio->query($sql);
//		DEBUG($sql);
		
		$socios = Set::extract('/Socio/id',$socios);		
//		debug($socios);
		$ERROR = array(0 => 0, 1 => '');
		$STOP = 0;
		
		if(!empty($socios)):
			
			$total = count($socios);
			$asinc->setTotal($total);
			$i = 0;
            
            ///////////////////////////////////////////////////////////////////
            //VERIFICAR SI TIENE SETEADO EN mutual.ini EL CONTROL PARA LIQUIDAR
            //LA CUOTA SOCIAL SOLO SI TIENE DEUDA
            ///////////////////////////////////////////////////////////////////
            $liqSiTieneDeuda = false;
            $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
            if(isset($INI_FILE['general']['cuota_social_permanente']) && $INI_FILE['general']['cuota_social_permanente'] == 0){
                $liqSiTieneDeuda = true;
            }
            ///////////////////////////////////////////////////////////////////
            
			
            ##################################################################################################################
            $CONTROL_NACION = false;
            $BANCO_CONTROL = null;
            $file = parse_ini_file(CONFIGS.'mutual.ini', true);
            if(isset($file['general']['banco_nacion_debito_periodo']) && $file['general']['banco_nacion_debito_periodo'] != ""){
                $CONTROL_NACION = true;
                $BANCO_CONTROL = $file['general']['banco_nacion_debito_periodo'];
            }              
            
            $LIQUIDA_DEUDA_CBU_SP = FALSE;
            // if(isset($file['general']['sp_liquida_deuda_cbu']) && $file['general']['sp_liquida_deuda_cbu'] == "1"){
            //     $LIQUIDA_DEUDA_CBU_SP = FALSE;
            // }
            
            $DISCRIMINA_PERMANENTES = FALSE;
            if(isset($INI_FILE['general']['discrimina_conceptos_permanentes_orden_debito']) && $INI_FILE['general']['discrimina_conceptos_permanentes_orden_debito'] == 0){
                $DISCRIMINA_PERMANENTES = TRUE;
            }            
            
            
			foreach($socios as $socio_id):	

//				$this->out($i."\t".$socio_id);
			
				$asinc->actualizar($i,$total,"$i / $total - LIQUIDANDO SOCIO #$socio_id");			
				
                if(substr($organismo,8,2) == "22" && $LIQUIDA_DEUDA_CBU_SP){
                    // $oSocio->query("CALL SP_LIQUIDA_DEUDA_CBU($socio_id,'$periodo','$organismo',".($pre_imputacion == 1 ? 'TRUE':'FALSE').")");
                }else{
                    $ERROR = $this->LiquidacionSocio->liquidar($socio_id,$periodo,$organismo,$this->liquidacionID,true,$pre_imputacion,$liqSiTieneDeuda,$CONTROL_NACION,$BANCO_CONTROL,$DISCRIMINA_PERMANENTES);
                }
                
				if(empty($ERROR) && $ERROR[0]==1):
					$asinc->actualizar($i,$total,"$i / $total - LIQUIDANDO SOCIO #$socio_id - ERROR: " .(!empty($ERROR[1]) ? $ERROR[1] : "ERROR GRAVE"));
					break;
				endif;
				if($asinc->detenido()){
					$STOP = 1;
					$this->__setCampo('bloqueada',0);
					break;
				}
				$i++;				
			
			endforeach;
			
			if($STOP == 0):
				$asinc->actualizar(99,100,"ACTUALIZANDO CABECERA DE LIQUIDACION...");
				#actualizo la cabecera
				$this->__actualizarCabecera($this->liquidacionID,$pid);
				$this->__setCampo('bloqueada',0);
                $this->__setCampo('archivos_procesados',0);
			endif;

			if($STOP == 0):
				$asinc->actualizar(99,100,"FINALIZANDO...");
				$asinc->fin("**** PROCESO FINALIZADO ****");	
				$this->__setCampo('bloqueada',0);	
                $this->__setCampo('archivos_procesados',0);
			endif;

		else:
			
			$asinc->actualizar(100,100,"NO EXISTEN REGISTROS PARA PROCESAR...");
			$this->__borrarLiquidacion($periodo,$organismo);
			$asinc->fin("**** PROCESO FINALIZADO ****");
			
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
	function __generarCabeceraLiquidacion($periodo,$organismo,$pre_imputacion){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		//verifico que no exista
		$liq = $oLiq->find('all',array('conditions' => array('Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.periodo' => $periodo,'Liquidacion.en_proceso' => 1),'fields' => array('Liquidacion.id'),'limit' => 1));
		if(!empty($liq)) return $liq['Liquidacion']['id']; 
		$data = array('Liquidacion' => array(
				'periodo' => $periodo,
				'codigo_organismo' => $organismo,
				'sobre_pre_imputacion' => $pre_imputacion,
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
        $liquidacion['Liquidacion']['scoring'] = 1;
        $liquidacion['Liquidacion']['archivos_procesados'] = 0;
		
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
	function __borrarLiquidacion($periodo,$organismo,$dejarCabecera = false){
		App::import('Model','Mutual.Liquidacion');
		App::import('Model','Mutual.LiquidacionCuota');
		App::import('Model','Mutual.LiquidacionSocio');
		App::import('Model','Mutual.MutualAdicionalPendiente');
		App::import('Model','Mutual.LiquidacionIntercambio');
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		
		App::import('Model','Mutual.LiquidacionSocioEnvio');
		App::import('Model','Mutual.LiquidacionSocioEnvioRegistro');
		
		
		$oLiq = new Liquidacion();
		$oDet = new LiquidacionCuota();
		$oLSoc = new LiquidacionSocio();
		$oAdic = new MutualAdicionalPendiente();
		$oLI = new LiquidacionIntercambio();
		$oSR = new LiquidacionSocioRendicion();
		
		$oLSE = new LiquidacionSocioEnvio();
		$oLSER = new LiquidacionSocioEnvioRegistro();

		$liquidaciones = $oLiq->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.cerrada' => 0)));
		foreach($liquidaciones as $liquidacion){
			if($liquidacion['Liquidacion']['cerrada'] == 0){
				$oDet->deleteAll("LiquidacionCuota.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oLSoc->deleteAll("LiquidacionSocio.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oAdic->deleteAll("MutualAdicionalPendiente.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oLI->borrarIntercambioByLiquidacion($liquidacion['Liquidacion']['id']);
				$oSR->deleteAll("LiquidacionSocioRendicion.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oLSER->deleteAll("LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id IN (select id from liquidacion_socio_envios where liquidacion_id = ".$liquidacion['Liquidacion']['id'].")",false);
				$oLSE->deleteAll("LiquidacionSocioEnvio.liquidacion_id = " .  $liquidacion['Liquidacion']['id'], false);
                
                $oLSE->query("delete from liquidacion_socio_scores where liquidacion_id = " .  $liquidacion['Liquidacion']['id']);
                
				if(!$dejarCabecera)$oLiq->del($liquidacion['Liquidacion']['id'],false);
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
	function __isCerrada($periodo,$organismo){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$cantidad = $oLiq->find('count',array('conditions'=> array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.cerrada' => 1)));
		if($cantidad == 0) return false;
		else return true;
	}
    
	function __isImputada($periodo,$organismo){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$cantidad = $oLiq->find('count',array('conditions'=> array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.imputada' => 1)));
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
//		$conditions['Liquidacion.cerrada'] = 0;
//		$conditions['Liquidacion.imputada'] = 0;
//		$conditions['Liquidacion.archivos_procesados'] = 0;
		$liquidacion = $oLiq->find('all',array('conditions'=> $conditions));
		if(empty($liquidacion)) return 0;
        $liquidacion = $liquidacion[0];
		if(isset($liquidacion['Liquidacion']['id']) && $liquidacion['Liquidacion']['id'] != 0) return $liquidacion['Liquidacion']['id'];
		else return 0;
	}
	
	function __actualizarFechaCreated($id){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
        $liquidacion = $oLiq->read('created',$id);
        $liquidacion['Liquidacion']['created'] = date('Y-m-d H:i:s');
        
        $oLiq->save($liquidacion);
	}	
}
?>
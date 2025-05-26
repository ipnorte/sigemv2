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
 * 			Por cada cuota va determinando el saldo.  Estas cuotas las va generando en la tabla liquidacion_cuotas_no_imputadas
 * PASO 3:	Determina los adicionales que puede cargarse, por ejemplo: GASTO ADMINISTRATIVO CBU.  Estos gastos
 * 			estan configurados en la tabla mutual_adicionales.  Si el adicional esta configurado para que se devengue
 * 			automaticamente genera la cuota en la orden_descuento_cuotas. Si nó lo genera en la tabla mutual_adicional_pendientes
 * 			y en la liquidacion_cuotas_no_imputadas, una vez que se procesa el pago se genera la cuota respectiva en la tabla orden_descuento_cuotas.
 * PASO 4: 	Procesa la liquidacion_cuotas_no_imputadas y genera el resumen en la liquidacion_socios_no_imputadas de donde se tienen que sacar los distintos archivos
 * 			para enviar a los organismos de retencion
 * 
 *
 *	PARAMETROS PASADOS:
 *	P1: PERIODO
 *	P2: ORGANISMO A LIQUIDAR
 *
 *	LANZADORES (depende de la instalacion del PHP)
 *	/usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php liquida_deuda_fraccion_nuevo_lote 24271 -app /home/adrian/trabajo/www/sigem/app/
 *	/opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php liquida_deuda_fraccion_nuevo_lote 157 -app /home/adrian/Desarrollo/www/sigem/app/
 *	/usr/bin/php5 /var/www/sigem/cake/console/cake.php liquida_deuda_fraccion_nuevo_lote 1 -app /var/www/sigem/app/
 *	/usr/bin/php5 /var/www/virtual/mutual22septiembre.com.ar/htdocs/sigem/cake/console/cake.php liquida_deuda_fraccion_nuevo_lote 33 -app /var/www/virtual/mutual22septiembre.com.ar/htdocs/sigem/app/
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 */

App::import('Vendor','exec',array('file' => 'exec.php'));

class LiquidaDeudaFraccionNuevoLoteShell extends Shell{
	
	var $tasks 		= array('Error');
	var $uses 		= array('Mutual.OrdenDescuento','Mutual.OrdenDescuentoCuota','Pfyj.Socio','Config.GlobalDato','Mutual.MutualProducto','Mutual.LiquidacionSocioNoimputada');
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
		
        $this->liquidacionID    = $this->args[0];
		$pid                    = $this->args[1];
        $SHELL_PID              = $this->args[2];
        $i                      = $this->args[3];
        $limit                  = $this->args[4];
        $organismo              = $this->args[5];
        $periodo                = $this->args[6];
        $pre_imputacion         = $this->args[7];
        $LIQUIDA_DEUDA_CBU_SP   = $this->args[8];
        $liqSiTieneDeuda        = $this->args[9];
        $CONTROL_NACION         = $this->args[10];
        $BANCO_CONTROL          = isset($this->args[11]) ? $this->args[11] : "";
        
        App::import('Model','Shells.Asincrono');
        $oASINC = new Asincrono();
        $oASINC->auditable = FALSE;

		$oASINC->id = $pid;
        $SHELL_sPID = $oASINC->getParametro('shell_pid');
        $tipoFiltro = $oASINC->getParametro('p4');

        
		App::import('Model','Pfyj.Socio');
		$oSocio = new Socio();
                $oSocio->auditable = FALSE;
		$oSocio->unbindModel(array('belongsTo' => array('Persona')));        
        
        $sql = "SELECT Socio.id FROM socios AS Socio
                INNER JOIN orden_descuentos AS OrdenDescuento ON (OrdenDescuento.socio_id = Socio.id)
                INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
                INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
                WHERE PersonaBeneficio.codigo_beneficio = '$organismo' and Persona.fallecida = 0 GROUP BY Socio.id ORDER BY Socio.id LIMIT $i,$limit;";
        $socios = $oSocio->query($sql); 
        
		$socios = $oSocio->query($sql);
//		DEBUG($sql);
		
		$socios = Set::extract('/Socio/id',$socios);		
//		debug($socios);
		$ERROR = array(0 => 0, 1 => '');
		$STOP = 0;
		
		if(!empty($socios)):
			
			$total = count($socios);
			$oASINC->setTotal($total);
			$i = 0;
         
            $SHELL = new exec();
            
			foreach($socios as $socio_id):	

//				$this->out($i."\t".$socio_id);
                //SI EL HILO PADRE ESTA CORTADO, SALGO DEL PROCESO
                            if(!$SHELL->is_running($SHELL_PID)){
                                $SHELL->kill($SHELL_sPID);
                                break;
                            }
		
				$oASINC->actualizar($i,$total,"II - LIQUIDANDO SOCIO #$socio_id");	
                
//                $oSocio->query("CALL SP_LIQUIDA_DEUDA_CBU($socio_id,'$periodo','$organismo',".($pre_imputacion == 1 ? 'TRUE':'FALSE').")");
		$this->LiquidacionSocioNoimputada->auditable = FALSE;		
                $ERROR = $this->LiquidacionSocioNoimputada->liquidar($socio_id,$periodo,$organismo,$this->liquidacionID,true,$pre_imputacion,$liqSiTieneDeuda,$CONTROL_NACION,$BANCO_CONTROL,FALSE,$tipoFiltro);
                
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
			
            $oASINC->actualizar($total,$total,"**** PROCESO FINALIZADO ****");
            $oASINC->fin("**** PROCESO FINALIZADO ****");

			
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
                $oLQ->auditable = FALSE;
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
                $oLQ->auditable = FALSE;
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
                $oLiq->auditable = FALSE;
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
		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
		App::import('Model','Mutual.LiquidacionSocioNoimputada');
		
		$oLiq = new Liquidacion();
		$oDet = new LiquidacionCuotaNoimputada();	
		$oLS = new LiquidacionSocioNoimputada();
                
        $oLiq->auditable = FALSE;
        $oDet->auditable = FALSE;
        $oLS->auditable = FALSE;                

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
//		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
//		$oDet = new LiquidacionCuotaNoimputada();
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
		App::import('Model','Mutual.LiquidacionCuotaNoimputada');
		App::import('Model','Mutual.LiquidacionSocioNoimputada');
		App::import('Model','Mutual.MutualAdicionalPendiente');
		App::import('Model','Mutual.LiquidacionIntercambio');
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		
		App::import('Model','Mutual.LiquidacionSocioEnvio');
		App::import('Model','Mutual.LiquidacionSocioEnvioRegistro');
		
		
		$oLiq = new Liquidacion();
		$oDet = new LiquidacionCuotaNoimputada();
		$oLSoc = new LiquidacionSocioNoimputada();
		$oAdic = new MutualAdicionalPendiente();
		$oLI = new LiquidacionIntercambio();
		$oSR = new LiquidacionSocioRendicion();
		
		$oLSE = new LiquidacionSocioEnvio();
		$oLSER = new LiquidacionSocioEnvioRegistro();
                
        $oLiq->auditable = FALSE;
        $oDet->auditable = FALSE;
        $oLSoc->auditable = FALSE;
        $oAdic->auditable = FALSE;
        $oLI->auditable = FALSE;
        $oSR->auditable = FALSE;
        $oLSE->auditable = FALSE;
        $oLSER->auditable = FALSE;                

		$liquidaciones = $oLiq->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.cerrada' => 0)));
		foreach($liquidaciones as $liquidacion){
			if($liquidacion['Liquidacion']['cerrada'] == 0){
				$oDet->deleteAll("LiquidacionCuotaNoimputada.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
				$oLSoc->deleteAll("LiquidacionSocioNoimputada.liquidacion_id = " . $liquidacion['Liquidacion']['id'],false);
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
                $oLiq->auditable = FALSE;
		$cantidad = $oLiq->find('count',array('conditions'=> array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.bloqueada' => 1)));
		if($cantidad == 0) return false;
		else return true;
	}
	function __isCerrada($periodo,$organismo){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
                $oLiq->auditable = FALSE;
		$cantidad = $oLiq->find('count',array('conditions'=> array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.cerrada' => 1)));
		if($cantidad == 0) return false;
		else return true;
	}
    
	function __isImputada($periodo,$organismo){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
                $oLiq->auditable = FALSE;
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
                $oLiq->auditable = FALSE;
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
                $oLiq->auditable = FALSE;
        $liquidacion = $oLiq->read('created',$id);
        $liquidacion['Liquidacion']['created'] = date('Y-m-d H:i:s');
        $oLiq->save($liquidacion);
	}	
}
?>
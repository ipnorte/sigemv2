<?php

/**
 * PROCESO DE IMPUTACION DE PAGOS DE UNA LIQUIDACION
 * 
 * LANZADORES 
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php imputar_pagos_por_archivo 35850 -app /home/adrian/trabajo/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php imputar_pagos_por_archivo 113 -app /var/www/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * 
 */

// 
// 
class ImputarPagosPorArchivoShell extends Shell {
	
	var $liquidacionID = 0;
	var $fecha_pago = NULL;
	var $nro_recibo = NULL;
        var $archivo_id = NULL;
	var $PROCCES_ID = 0;
	
	var $tasks = array('Temporal');
	
	function main() {
		
		$ERROR = NULL;
		
		App::import('Model','Mutual.OrdenDescuentoCobro');
		App::import('Model','Mutual.Liquidacion');
		
		
		$oCOBRO = new OrdenDescuentoCobro();
		$oLIQUI = new Liquidacion();				
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$this->PROCCES_ID = $pid;
		
		$this->Temporal->pid = $pid;
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$this->liquidacionID		= $asinc->getParametro('p1');
		$this->fecha_pago		= $asinc->getParametro('p2');
		$this->nro_recibo		= $asinc->getParametro('p3');
		
		$DESIMPUTAR			= ($asinc->getParametro('p4') == 1 ? TRUE : FALSE);
                $this->archivo_id		= $asinc->getParametro('p5');
                

		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");
		

		
		if($oLIQUI->isBloqueada($this->liquidacionID)):
			$idBloquedo = $oLIQUI->getBloqueoPID($this->liquidacionID);
			$msg = "PROCESO BLOQUEADO POR OTRO USUARIO [PID #$idBloquedo]....";
			$asinc->actualizar(2,100,$msg);
			$msg2 = $asinc->getCadenaInfo($idBloquedo);
			$this->Temporal->setErrorMsg("BOQUEO PID #$idBloquedo",$msg2);
			return;
		endif;
		
		$oLIQUI->cerrar($this->liquidacionID);
		//verifico que no haya otros procesos bloqueados
		$oLIQUI->bloquear($this->liquidacionID,$pid);
		
		//cargar la tabla liquidacion_cuotas para la liquidacion ID
		
		
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();	

		
		if($DESIMPUTAR):
			$asinc->actualizar(2,100,"ESPERE, CARGANDO SOCIOS CON IMPUTACION PREVIA...");
			$socios = $oLSR->getSociosDebitadosCobrados($this->liquidacionID,$this->archivo_id);
			
			$total = 0;
			$i = 0;		
			
			$total = count($socios);
			$asinc->setTotal($total);	
			
			//DESIMPUTAR COBROS EMITIDOS
			if(!empty($socios)):
				foreach($socios as $socio_id){
					$oCOBRO->desimputarLiquidacion($this->liquidacionID, $socio_id,$this->archivo_id);
					$porc = round($i/$total * 100,0);
					$msg ="$i / $total - DESIMPUTADO >> SOCIO #$socio_id";
					$asinc->actualizar($i,$total,$msg);
					$i++;
				}
			endif;
		
		endif;
		
//		$socios = $this->__getSociosPreimputados();
		$asinc->actualizar(3,100,"ESPERE, CARGANDO SOCIOS PARA IMPUTAR...");
		$socios = $oLSR->getSociosDebitadosNoCobrados($this->liquidacionID,$this->archivo_id);
		
		$total = 0;
		$i = 1;		
		
		$total = count($socios);
		$asinc->setTotal($total);		
		
		//imputar los pagos
		if(!empty($socios)):
		
			$ERROR = NULL;
			
			foreach($socios as $socio_id):
	
				$msg = "$i / $total - IMPUTANDO PAGOS >> SOCIO #$socio_id";
				
				$this->__calificarSocio($socio_id);	
			
				$asinc->actualizar($i,$total,$msg);
				
				$resultado = $oCOBRO->imputarLiquidacion($this->liquidacionID, $socio_id, $this->fecha_pago, $this->nro_recibo,TRUE,$this->archivo_id);
				
				if($resultado['ERROR'] == 1){
					$ERROR = $resultado['MENSAJE'];
					$this->Temporal->setErrorMsg($msg,$ERROR);
				}
				
				$msg .= "| COBRO EMITIDO #" . $resultado['COBRO_ID'];
				
				$asinc->actualizar($i,$total,$msg);
				
				$i++;
				
			endforeach;

			if(empty($ERROR)){
//				$oLIQUI->setTotales($this->liquidacionID);			
//				$oLIQUI->setImputada($this->liquidacionID,$this->fecha_pago,$this->nro_recibo);
				$oLIQUI->desbloquear($this->liquidacionID);
				$asinc->actualizar(99,100,"FINALIZANDO...");
				$asinc->fin("**** PROCESO FINALIZADO ****");		
			}else{
				$asinc->actualizar(99,100,"ULTIMO ERROR: $ERROR");
			}
			
		else:

			$asinc->actualizar(10,100,"NO EXISTEN DATOS PARA PROCESAR");

		endif;

		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	/**
	 * genera la calificacion del socio en base a la tabla liquidacion_socios para la liquidacion procesada
	 * @param $socio_id
	 */
	function __calificarSocio($socio_id){
		
		App::import('Model','Pfyj.SocioCalificacion');
		$oSC = new SocioCalificacion();
				
//		$sql = "	select 
//						LiquidacionSocioRendicion.socio_id,
//						LiquidacionSocioRendicion.banco_intercambio,
//						LiquidacionSocioRendicion.status,
//						BancoRendicionCodigo.calificacion_socio,
//						LiquidacionSocio.persona_beneficio_id,
//						Liquidacion.periodo
//					from liquidacion_socio_rendiciones as LiquidacionSocioRendicion
//					left join banco_rendicion_codigos as BancoRendicionCodigo on (LiquidacionSocioRendicion.banco_intercambio = BancoRendicionCodigo.banco_id and LiquidacionSocioRendicion.status = BancoRendicionCodigo.codigo)
//					inner join liquidacion_socios as LiquidacionSocio on (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id and LiquidacionSocio.socio_id and LiquidacionSocioRendicion.socio_id)
//					inner join liquidaciones as Liquidacion on (LiquidacionSocio.liquidacion_id = Liquidacion.id)
//					where 
//						LiquidacionSocioRendicion.liquidacion_id = $this->liquidacionID
//						and LiquidacionSocioRendicion.socio_id = $socio_id
//					group by LiquidacionSocioRendicion.socio_id,LiquidacionSocioRendicion.banco_intercambio,LiquidacionSocioRendicion.status
//					order by LiquidacionSocioRendicion.socio_id, LiquidacionSocioRendicion.indica_pago LIMIT 1";


		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();	

		$liquidacion = $oLQ->read(null,$this->liquidacionID);
		
		//saco la calificacion del periodo
		$sql = "	select 
						Liquidacion.id,
						LiquidacionSocioRendicion.socio_id,
						LiquidacionSocioRendicion.banco_intercambio,
						LiquidacionSocioRendicion.status,
						BancoRendicionCodigo.calificacion_socio,
						LiquidacionSocio.persona_beneficio_id,
						Liquidacion.periodo
					from liquidacion_socio_rendiciones as LiquidacionSocioRendicion
					left join banco_rendicion_codigos as BancoRendicionCodigo on (LiquidacionSocioRendicion.banco_intercambio = BancoRendicionCodigo.banco_id and LiquidacionSocioRendicion.status = BancoRendicionCodigo.codigo)
					inner join liquidacion_socios as LiquidacionSocio on (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id)
					inner join liquidaciones as Liquidacion on (LiquidacionSocio.liquidacion_id = Liquidacion.id)
					where 
						LiquidacionSocioRendicion.socio_id = $socio_id
						and Liquidacion.periodo = '".$liquidacion['Liquidacion']['periodo']."'
						and IFNULL(LiquidacionSocioRendicion.status,'') <> ''
					group by Liquidacion.id,LiquidacionSocioRendicion.socio_id,LiquidacionSocio.persona_beneficio_id,LiquidacionSocioRendicion.banco_intercambio,LiquidacionSocioRendicion.status
					order by LiquidacionSocioRendicion.indica_pago ASC, LiquidacionSocioRendicion.indica_pago LIMIT 1";
		$datos = $oSC->query($sql);
		
		if(!empty($datos)):
			$periodo = $datos[0]['Liquidacion']['periodo'];
			$persona_beneficio_id = $datos[0]['LiquidacionSocio']['persona_beneficio_id'];	
			$calificacion = $datos[0]['BancoRendicionCodigo']['calificacion_socio'];	
			$oSC->deleteAll("SocioCalificacion.socio_id = $socio_id and SocioCalificacion.persona_beneficio_id = $persona_beneficio_id and SocioCalificacion.periodo = '$periodo'");
			$oSC->calificar($socio_id,$calificacion,$persona_beneficio_id,$periodo,$this->fecha_pago);
		
		endif;
		
	}
	
	/**
	 * setea la cabecera de la liquidacion con los datos de los archivos recibidos
	 * importe cobrado y no cobrado
	 */
	function __setTotales(){
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$liquidacion = $oLQ->read(null,$this->liquidacionID);
		
	
		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();

		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
	
		$liquidacion['Liquidacion']['registros_recibidos'] = $oLSR->getCantidadRegistrosRecibidos($this->liquidacionID);
		$liquidacion['Liquidacion']['importe_cobrado'] = $oLSR->getTotalByLiquidacion($this->liquidacionID,1);
		$liquidacion['Liquidacion']['importe_no_cobrado'] = $oLSR->getTotalByLiquidacion($this->liquidacionID,0);
		$liquidacion['Liquidacion']['importe_imputado'] = $oLC->getTotalImputadoByLiquidacion($this->liquidacionID);
		$liquidacion['Liquidacion']['importe_reintegro'] = $liquidacion['Liquidacion']['importe_cobrado'] - $liquidacion['Liquidacion']['importe_imputado'];
		$liquidacion['Liquidacion']['importe_recibido'] = $oLSR->getTotalByLiquidacion($this->liquidacionID,1,null);
		$liquidacion['Liquidacion']['fecha_imputacion'] = $this->fecha_pago;
		$liquidacion['Liquidacion']['nro_recibo'] = $this->nro_recibo;
		
		return $oLQ->save($liquidacion);

	}
	
	
	/**
	 * devuelve el calculo de la liquidacion del proveedor en base a lo cobrado y marcado como imputado
	 * @return $proveedores
	 */
	function __getLiquidacionProveedores(){
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();
		$proveedores = $oLC->getCuotasImputadasByLiquidacionByProveedor($this->liquidacionID);
		return $proveedores;		
	}
	
	/**
	 * graba la liquidacion del proveedor
	 * @param $liquidacionProveedor
	 */
	function __generarLiquidacionProveedor($liquidacionProveedor){
		
		App::import('Model','Proveedores.ProveedorLiquidacion');
		$oPL = new ProveedorLiquidacion();		
		$oPL->id = 0;
		
		//borro la que existe
		$oPL->deleteAll("ProveedorLiquidacion.liquidacion_id = ".$this->liquidacionID." and ProveedorLiquidacion.proveedor_id = " . $liquidacionProveedor['LiquidacionCuota']['proveedor_id']);
		
		$liquidacion = array('ProveedorLiquidacion' => array(
			'proveedor_id' => $liquidacionProveedor['LiquidacionCuota']['proveedor_id'],
			'periodo' => $this->__getCampo('periodo'),
			'codigo_organismo' => $this->__getCampo('codigo_organismo'),
			'liquidacion_id' => $this->liquidacionID,
			'tipo_cuota' => $liquidacionProveedor['LiquidacionCuota']['tipo_cuota'],
			'importe_liquidado' => $liquidacionProveedor['LiquidacionCuota']['saldo_actual'],
			'importe_debitado' => $liquidacionProveedor['LiquidacionCuota']['importe_debitado']
		
		));
		
		$oPL->save($liquidacion);
		
		$proveedorLiquidacionID = $oPL->getLastInsertID();
		
		//MARCO LA CUOTA DE LA LIQUIDACION CON EL ID DE LA LIQUIDACION DEL PROVEEDOR
		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();

		$oLC->updateAll(
				array('LiquidacionCuota.proveedor_liquidacion_id' => $proveedorLiquidacionID),
				array(
						'LiquidacionCuota.liquidacion_id' => $this->liquidacionID, 
						'LiquidacionCuota.proveedor_id' => $liquidacionProveedor['LiquidacionCuota']['proveedor_id'],
						'LiquidacionCuota.imputada' => 1
				)
		);
		
		//marco la liquidacion del proveedor en el pago de la cuota
		$cuotasLiquidadas = $oLC->find('all',array('conditions' => array(
						'LiquidacionCuota.liquidacion_id' => $this->liquidacionID, 
						'LiquidacionCuota.proveedor_id' => $liquidacionProveedor['LiquidacionCuota']['proveedor_id'],
						'LiquidacionCuota.imputada' => 1,
						'LiquidacionCuota.proveedor_liquidacion_id' => $proveedorLiquidacionID
		)));
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCOBROCUOTA = new OrdenDescuentoCobroCuota();		
		
		foreach ($cuotasLiquidadas as $cuotasLiquidada){
			$oCOBROCUOTA->id = $cuotasLiquidada['LiquidacionCuota']['orden_descuento_cobro_cuota_id'];
			$cobroCuota = $oCOBROCUOTA->read(null,$cuotasLiquidada['LiquidacionCuota']['orden_descuento_cobro_cuota_id']);
			$cobroCuota['proveedor_liquidacion_id'] = $cuotasLiquidada['LiquidacionCuota']['proveedor_liquidacion_id'];
			$oCOBROCUOTA->save($cobroCuota);
//			$oCOBROCUOTA->saveField('proveedor_liquidacion_id',$cuotasLiquidada['LiquidacionCuota']['proveedor_liquidacion_id']);
		}
		
		return true;
	}	
	
	
	####################################################################################################
	# METODOS GENERALES
	####################################################################################################
	
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
}
?>
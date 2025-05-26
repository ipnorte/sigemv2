<?php

/**
* Proceso asincrono para grabar el registro de intercambio de la tabla de 
* liquidacion_socios
* genera_archivo.php
* @author adrian [* 27/01/2012]
* 
* /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php genera_archivo3 455 -app /home/adrian/trabajo/www/sigem/app/
* /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php genera_archivo3 455 -app /home/mutualam/public_html/sigem/app/
* 
*/
Configure::write('debug',1);
class GeneraArchivo3Shell extends Shell{
	
	var $tasks = array('Temporal');
	
	function main(){
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$datos = $asinc->getParametro('txt1');
// 		$datos = base64_decode($datos);
		$datos = unserialize($datos);
		
//        echo "PASO";
//        print_r($datos);
//        exit;
        
		$this->Temporal->pid = $pid;
		
//		debug($datos);
        
        $asinc->auditable = false;

		$asinc->actualizar(5,100,"ESPERE, INICIANDO PROCESO II...");
		$STOP = 0;
		$total = 0;
		$i = 0;		
		
		#####################################################################################
		$file = CONFIGS.'mutual.ini';
		$iniFile = parse_ini_file($file, true);
		Configure::write('APLICACION',array('cuit_mutual' => $iniFile['general']['cuit_mutual'],'intercambio_bancos' => $iniFile['intercambio']));		
		#####################################################################################
		
		
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();		
		
		App::import('Model','Mutual.LiquidacionSocioNoimputada');
		$oLS = new LiquidacionSocioNoimputada();
		
		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();

		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);		
		
		App::import('Model', 'Pfyj.Socio');
		$oSOCIO = new Socio(null);	

                $oLQ->auditable = false;
		$oLQ->cerrar($datos['LiquidacionSocioNoimputada']['liquidacion_id']);
		$oLQ->bloquear($datos['LiquidacionSocioNoimputada']['liquidacion_id'],$pid);
                
                $PERIODO = $oLQ->getPeriodo($datos['LiquidacionSocioNoimputada']['liquidacion_id']);
		
		#CARGO LOS REGISTROS A PROCESAR
		$socios = $oLS->find('all',array(
										'joins' => array(
												array(
													'table' => 'global_datos',
													'alias' => 'GlobalDato',
													'type' => 'inner',
													'foreignKey' => false,
													'conditions' => array('LiquidacionSocioNoimputada.codigo_empresa = GlobalDato.id')
													),		
										),
										'conditions' => array(
																	'LiquidacionSocioNoimputada.liquidacion_id' => $datos['LiquidacionSocioNoimputada']['liquidacion_id'],
																	'LiquidacionSocioNoimputada.turno_pago' => $datos['LiquidacionSocioNoimputada']['turno_pago_array'],
																	'LiquidacionSocioNoimputada.diskette' => 1,
																),
										'order' => array('GlobalDato.concepto_1,LiquidacionSocioNoimputada.apenom,LiquidacionSocioNoimputada.registro'),					
		));		
		
//		debug($socios);
		$total = count($socios);
		$asinc->setTotal($total);
		$i = 0;		
		
		
		$ACUM_REGISTROS = 0;
		$ACUM_IMPORTE = $ACUM_IMPORTE_DISK = $ACUM_REGISTROS = $ACUM_REGISTROS_ERROR =  $ACUM_REGISTROS_DISK = 0;

		$registros = array();

		###############################################################################################################################
		# GENERO LA CABECERA DEL ENVIO
		###############################################################################################################################
		App::import('Model','Mutual.LiquidacionSocioEnvioNoimputada');
		App::import('Model','Mutual.LiquidacionSocioEnvioRegistroNoimputada');
		
		$oLSE = new LiquidacionSocioEnvioNoimputada();		
		$oLSER = new LiquidacionSocioEnvioRegistroNoimputada();
        
                $oLSE->auditable = false;
                $oLSER->auditable = false;
        
		$envio = array();
		$envio['LiquidacionSocioEnvioNoimputada'] = array();
		$envio['LiquidacionSocioEnvioNoimputada']['id'] = 0;
		$envio['LiquidacionSocioEnvioNoimputada']['asincrono_id'] = $pid;
		$envio['LiquidacionSocioEnvioNoimputada']['bloqueado'] = 1;
		$envio['LiquidacionSocioEnvioNoimputada']['liquidacion_id'] = $datos['LiquidacionSocioNoimputada']['liquidacion_id'];
		$envio['LiquidacionSocioEnvioNoimputada']['banco_id'] = $datos['LiquidacionSocioNoimputada']['banco_intercambio'];
		$oLSE->save($envio);
		$ID_ENVIO = $oLSE->getLastInsertID(); 
		###############################################################################################################################
		
        
        
		foreach($socios as $idx => $socio){
		
			$envioRegistro = array();
			
			$socio['LiquidacionSocioNoimputada']['ERROR_INTERCAMBIO'] = "OK";
			
			$socio['LiquidacionSocioNoimputada']['error_cbu'] = 0;
//			$socio['LiquidacionSocio']['fecha_debito'] = $oLS->armaFecha($datos['LiquidacionSocio']['fecha_debito']);
                        $socio['LiquidacionSocioNoimputada']['fecha_debito'] = $datos['LiquidacionSocioNoimputada']['fecha_debito'];
			
//			$calificacion = $oSOCIO->getUltimaCalificacion($socio['LiquidacionSocio']['socio_id'],$socio['LiquidacionSocio']['persona_beneficio_id']);
			$calificacion = $oSOCIO->getUltimaCalificacion($socio['LiquidacionSocioNoimputada']['socio_id'],NULL,FALSE,FALSE,FALSE,$PERIODO);
			$socio['LiquidacionSocioNoimputada']['ultima_calificacion'] = $calificacion;
			
			$bancoIntercambio	= $datos['LiquidacionSocioNoimputada']['banco_intercambio'];
			$fechaDebito		= $socio['LiquidacionSocioNoimputada']['fecha_debito'];
			$importe 		= $socio['LiquidacionSocioNoimputada']['importe_adebitar'];
			$registroNro 		= $socio['LiquidacionSocioNoimputada']['registro'];
			$idDebito 		= $oLS->__genDebitoID($socio,($datos['LiquidacionSocioNoimputada']['banco_intercambio'] === '00011' ? false : true));
			$liquidacionSocioId     = $socio['LiquidacionSocioNoimputada']['id'];
			$socioId 		= $socio['LiquidacionSocioNoimputada']['socio_id'];
			$sucursal 		= $socio['LiquidacionSocioNoimputada']['sucursal'];
			$cuenta 		= $socio['LiquidacionSocioNoimputada']['nro_cta_bco'];
			$cbu 			= $socio['LiquidacionSocioNoimputada']['cbu'];
			$codOrganismo 		= $socio['LiquidacionSocioNoimputada']['codigo_organismo'];
			$calificacion 		= $socio['LiquidacionSocioNoimputada']['ultima_calificacion'];
			$apenom		 	= str_replace(","," ",$socio['LiquidacionSocioNoimputada']['apenom']);
			$ndoc		 	= $socio['LiquidacionSocioNoimputada']['documento'];
			$beneficioBancoId       = $socio['LiquidacionSocioNoimputada']['banco_id'];
                        $liquidacionID 		= $socio['LiquidacionSocioNoimputada']['liquidacion_id'];
                        $convenioBcoCba         = $datos['LiquidacionSocioNoimputada']['nro_convenio_cba'];
                        $socioCuitCuil          = $socio['LiquidacionSocioNoimputada']['cuit_cuil'];
                        $nroArchivo             = $datos['LiquidacionSocioNoimputada']['nro_archivo'];
                        $fechaPresentacion      = $datos['LiquidacionSocioNoimputada']['fecha_presentacion'];
                        $fechaMaxima            = $datos['LiquidacionSocioNoimputada']['fecha_maxima'];
                        $ciclos                 = $datos['LiquidacionSocioNoimputada']['nro_ciclos'];
            
            //genRegistroDisketteBanco($bancoIntercambio, $fechaDebito, $importe, $registroNro, $idDebito, $liquidacionSocioId, $socioId, $sucursal, $cuenta, $cbu, $codOrganismo, $apenom, $ndoc, $calificacion = null, $beneficioBancoId = null,$liquidacionID = null)
			$registro = $oBanco->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$apenom,$ndoc,$calificacion,$beneficioBancoId,$liquidacionID,$convenioBcoCba,$socioCuitCuil,$nroArchivo,$fechaPresentacion,$fechaMaxima,$ciclos);
			
			
			$socio['LiquidacionSocioNoimputada']['importe_adebitar'] = $registro['importe_debito'];
			$socio['LiquidacionSocioNoimputada']['error_cbu'] = $registro['error'];
			$socio['LiquidacionSocioNoimputada']['intercambio'] = $registro['cadena'];
			$socio['LiquidacionSocioNoimputada']['error_intercambio'] = null;
			if(!empty($registro['mensaje']))$socio['LiquidacionSocioNoimputada']['error_intercambio'] = $registro['mensaje'];
			
			$socio['LiquidacionSocioNoimputada']['sucursal'] = $registro['sucursal_formed'];
			$socio['LiquidacionSocioNoimputada']['nro_cta_bco'] = $registro['cuenta_formed'];
			
			
//			if(!empty($registro['cadena']))$this->out("$i|$total\t" . $registro['cadena'],false);
                        $asinc->auditable = false;
			$asinc->actualizar($i,$total,"[$i|$total] " . $socio['LiquidacionSocioNoimputada']['apenom'] . "|" . $registro['cadena']);
			
			$ACUM_REGISTROS++;
			$ACUM_IMPORTE += $socio['LiquidacionSocioNoimputada']['importe_adebitar'];

			###############################################################################################################################
			#GENERO LA LIQUIDACION_SOCIO_ENVIO_REGISTROS
			###############################################################################################################################
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['id'] = 0;
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['liquidacion_socio_envio_id'] = $ID_ENVIO;
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['liquidacion_socio_id'] = $socio['LiquidacionSocio']['id'];
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['socio_id'] = $socioId;
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['identificador_debito'] = $idDebito;
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['importe_adebitar'] = $importe;
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['registro'] = $registro['cadena'];
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['excluido'] = $registro['error'];
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['motivo'] = $registro['mensaje'];
			$envioRegistro['LiquidacionSocioEnvioRegistroNoimputada']['user_created'] = $asinc->getPropietario();
                        $oLSER->auditable = false;
			$oLSER->save($envioRegistro);				
			###############################################################################################################################
			
			if($registro['error'] == 0){
				$ACUM_REGISTROS_DISK++;
				$ACUM_IMPORTE_DISK += $socio['LiquidacionSocioNoimputada']['importe_adebitar'];
				array_push($registros,$socio);
			}else{
				$this->Temporal->setErrorMsg($socio['LiquidacionSocioNoimputada']['documento'] . " - " . $socio['LiquidacionSocioNoimputada']['apenom'],$envioRegistro['LiquidacionSocioEnvioRegistro']['motivo']);
				$ACUM_REGISTROS_ERROR++;
			}
			
			//ACTUALIZO LA TABLA LIQUIDACION_SOCIOS
			$update = array(
				'LiquidacionSocioNoimputada.importe_adebitar' => $socio['LiquidacionSocioNoimputada']['importe_adebitar'],
				'LiquidacionSocioNoimputada.error_cbu' => $socio['LiquidacionSocioNoimputada']['error_cbu'],
				'LiquidacionSocioNoimputada.banco_intercambio' => "'$bancoIntercambio'",
				'LiquidacionSocioNoimputada.fecha_debito' => "'$fechaDebito'",
				'LiquidacionSocioNoimputada.intercambio' => "'".$socio['LiquidacionSocioNoimputada']['intercambio']."'",
				'LiquidacionSocioNoimputada.sucursal' => "'".$socio['LiquidacionSocioNoimputada']['sucursal']."'",
				'LiquidacionSocioNoimputada.nro_cta_bco' => "'".$socio['LiquidacionSocioNoimputada']['nro_cta_bco']."'",
				'LiquidacionSocioNoimputada.ultima_calificacion' => "'".$socio['LiquidacionSocioNoimputada']['ultima_calificacion']."'",
				'LiquidacionSocioNoimputada.error_intercambio' => "'".$socio['LiquidacionSocioNoimputada']['error_intercambio']."'",
			);
            
//            debug($socio);
            
			$oLS->auditable = false;
			if(!$oLS->updateAll($update,array('LiquidacionSocioNoimputada.id' => $socio['LiquidacionSocioNoimputada']['id']))){
				$ERROR = 1;
				break;
			}
	
			if($asinc->detenido()){
				$STOP = 1;
				break;
			}
			
//			$socios[$idx] = $socio;
		
			$i++;
		}
		
		$resultados = array(
			'registros' => $ACUM_REGISTROS,
			'liquidado' => $ACUM_IMPORTE,
			'errores' => $ACUM_REGISTROS_ERROR,
			'registros_disk' => $ACUM_REGISTROS_DISK,
			'importe_disk' => $ACUM_IMPORTE_DISK,
			'importe_error' => $ACUM_IMPORTE - $ACUM_IMPORTE_DISK,
		);
		
		#CARGO EL RESULTADO EN EL CAMPO TXT2 DEL ASINCRONO PARA LEERLO DESDE EL CONTROLLER
// 		$asinc->setValue('txt2',base64_encode(serialize($resultados)));
                $asinc->auditable = true;
		$asinc->setValue('txt2',serialize($resultados));
		$asinc->auditable = true;
		$asinc->actualizar(98,100,"GENERANDO LOTE...");
		#GENERO EL REGISTRO DE LOTE EN LA LIQUIDACION_SOCIO_ENVIOS
                $oLS->auditable = false;
		$socios = $oLS->generarDisketteCBUFromAsincronoProcess($pid);
//		debug($socios);
		###############################################################################################################################
		# GUARDO EL ENVIO
		###############################################################################################################################
		if($socios['diskette']['status'] == 'OK'):
			$envio['LiquidacionSocioEnvioNoimputada']['id'] = $ID_ENVIO;
			$envio['LiquidacionSocioEnvioNoimputada']['bloqueado'] = 0;
			$envio['LiquidacionSocioEnvioNoimputada']['banco_nombre'] = $socios['diskette']['banco_intercambio_nombre'];
			$envio['LiquidacionSocioEnvioNoimputada']['fecha_debito'] = date('Y-m-d',strtotime($socios['diskette']['fecha_debito']));
			$envio['LiquidacionSocioEnvioNoimputada']['cantidad_registros'] = $socios['diskette']['cantidad_registros'];
			$envio['LiquidacionSocioEnvioNoimputada']['status'] = $socios['diskette']['status'];
			$envio['LiquidacionSocioEnvioNoimputada']['importe_debito'] = $socios['diskette']['importe_debito'];
			$envio['LiquidacionSocioEnvioNoimputada']['observaciones'] = $socios['diskette']['observaciones'];
			$envio['LiquidacionSocioEnvioNoimputada']['longitud_registro'] = $socios['diskette']['longitud_registro'];
			$envio['LiquidacionSocioEnvioNoimputada']['uuid'] = $socios['diskette']['uuid'];
			$envio['LiquidacionSocioEnvioNoimputada']['archivo'] = $socios['diskette']['archivo'];
			$envio['LiquidacionSocioEnvioNoimputada']['lote'] = $socios['diskette']['lote'];
			$envio['LiquidacionSocioEnvioNoimputada']['user_created'] = $asinc->getPropietario();
//			$envio['LiquidacionSocioEnvioRegistro'] = $envioRegistros;
			
			$oLSE->auditable = false;
			$oLSE->save($envio);	
			
			//VALIDAR EL ARCHIVO
			if($oLSE->isValido($ID_ENVIO)){
				$asinc->setValue('p1',$ID_ENVIO);
			}else{
				$oLSE->del($ID_ENVIO,true);
			}
			
		else:
			$oLSE->del($ID_ENVIO,true);
		endif;	
		###############################################################################################################################
		
		$asinc->actualizar(99,100,"FINALIZANDO...");
                $asinc->auditable = true;
		$asinc->fin("**** PROCESO FINALIZADO ****");
		$oLQ->desbloquear($datos['LiquidacionSocioNoimputada']['liquidacion_id']);		
		

		
	}
	
}

?>
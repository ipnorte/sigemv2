<?php

/**
* Proceso asincrono para grabar el registro de intercambio de la tabla de
* liquidacion_socios
* genera_archivo.php
* @author adrian [* 27/01/2012]
*
* /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php genera_archivo 27078 -app /home/adrian/Trabajo/www/sigemv2/app/
* /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php genera_archivo 16994 -app /home/mutualam/public_html/sigem/app/
*
*/
Configure::write('debug',1);
class GeneraArchivoShell extends Shell{

function decrypt($string){
	$output = false;
	$key = hash(SECURITY_ENCRYPTION_ALGORITHM, SECURITY_ENCRYPTION_KEY);
	$iv = substr(hash(SECURITY_ENCRYPTION_ALGORITHM, SECURITY_IV), 0, 16);
	$output = openssl_decrypt(base64_decode($string), SECURITY_ENCRYPTION_MECHANISM, $key, 0, $iv);
	return $output;
}

	var $tasks = array('Temporal');

	function main(){

		$pid = $this->args[0];

		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid;

		$datos = $asinc->getParametro('txt1');
// 	$datos = base64_decode($datos);
		$datos = unserialize($datos);

		$empresas = array();
		$turnos = array();
		foreach($datos['LiquidacionSocio']['turno_pago_array'] as $valor){
			list($empresa,$turno) = explode('|',$valor);
			array_push($empresas,$empresa);
			array_push($turnos,$turno);
		}		


//        echo "PASO";
//        print_r($datos);
//        exit;

		$this->Temporal->pid = $pid;

//		debug($datos);

    $asinc->auditable = false;

		$asinc->actualizar(5,100,"ESPERE, INICIANDO PROCESO...");
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

		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();

		App::import('Model', 'Config.Banco');
		$oBanco = new Banco(null);

		App::import('Model', 'Pfyj.Socio');
		$oSOCIO = new Socio(null);

		$oLQ->auditable = false;
		$oLQ->cerrar($datos['LiquidacionSocio']['liquidacion_id']);
		$oLQ->bloquear($datos['LiquidacionSocio']['liquidacion_id'],$pid);

		$PERIODO = $oLQ->getPeriodo($datos['LiquidacionSocio']['liquidacion_id']);

		#CARGO LOS REGISTROS A PROCESAR
		$socios = $oLS->find('all',array(
										'joins' => array(
												array(
													'table' => 'global_datos',
													'alias' => 'GlobalDato',
													'type' => 'inner',
													'foreignKey' => false,
													'conditions' => array('GlobalDato.id = LiquidacionSocio.codigo_empresa')
													),
													array(
														'table' => 'persona_beneficios',
														'alias' => 'PersonaBeneficio',
														'type' => 'inner',
														'foreignKey' => false,
														'conditions' => array('PersonaBeneficio.id = LiquidacionSocio.persona_beneficio_id')
													),
													array(
														'table' => 'socios',
														'alias' => 'Socio',
														'type' => 'inner',
														'foreignKey' => false,
														'conditions' => array('Socio.id = LiquidacionSocio.socio_id')
													),
													array(
														'table' => 'personas',
														'alias' => 'PersonaDatosPersonales',
														'type' => 'inner',
														'foreignKey' => false,
														'conditions' => array('PersonaDatosPersonales.id = PersonaBeneficio.persona_id')
													),
													array(
														'table' => 'provincias',
														'alias' => 'Provincia',
														'type' => 'left',
														'foreignKey' => false,
														'conditions' => array('Provincia.id = PersonaDatosPersonales.provincia_id')
													),


										),
										'fields' => array(
											'LiquidacionSocio.*
                                            ,PersonaBeneficio.id
											,PersonaBeneficio.tarjeta_debito
											,PersonaDatosPersonales.documento
											,PersonaDatosPersonales.nombre
											,PersonaDatosPersonales.apellido
											,PersonaDatosPersonales.fecha_nacimiento
											,PersonaDatosPersonales.calle
											,PersonaDatosPersonales.numero_calle
											,PersonaDatosPersonales.piso
											,PersonaDatosPersonales.dpto
											,PersonaDatosPersonales.codigo_postal
											,PersonaDatosPersonales.localidad
											,PersonaDatosPersonales.telefono_movil_c
											,PersonaDatosPersonales.telefono_movil
											,PersonaDatosPersonales.e_mail
											,Provincia.nombre
                                            ,Socio.id
                                            ,Socio.fecha_alta
										'),
										'conditions' => array(
																	'LiquidacionSocio.liquidacion_id' => $datos['LiquidacionSocio']['liquidacion_id'],
																	'LiquidacionSocio.codigo_empresa' => $empresas,
																	'LiquidacionSocio.turno_pago' => $turnos,
//																	'LiquidacionSocio.importe_adebitar >' => 0,
																	'LiquidacionSocio.diskette' => 1,
//																	'LiquidacionSocio.socio_id' => 3553,
																),
										'order' => array('GlobalDato.concepto_1,LiquidacionSocio.apenom,LiquidacionSocio.registro'),
		                                'group' => array('LiquidacionSocio.id')
		));
		/*debug($socios);
		//debug($oLS->getDataSource());
			exit();*/
			/*debug($socios);
			exit();*/


		$total = count($socios);
		$asinc->setTotal($total);
		$i = 0;


		$ACUM_REGISTROS = 0;
		$ACUM_IMPORTE = $ACUM_IMPORTE_DISK = $ACUM_REGISTROS = $ACUM_REGISTROS_ERROR =  $ACUM_REGISTROS_DISK = 0;

		$registros = array();

		###############################################################################################################################
		# GENERO LA CABECERA DEL ENVIO
		###############################################################################################################################
		App::import('Model','Mutual.LiquidacionSocioEnvio');
		App::import('Model','Mutual.LiquidacionSocioEnvioRegistro');

		$oLSE = new LiquidacionSocioEnvio();
		$oLSER = new LiquidacionSocioEnvioRegistro();

    $oLSE->auditable = false;
    $oLSER->auditable = false;

		$envio = array();
		$envio['LiquidacionSocioEnvio'] = array();
		$envio['LiquidacionSocioEnvio']['id'] = 0;
		$envio['LiquidacionSocioEnvio']['asincrono_id'] = $pid;
		$envio['LiquidacionSocioEnvio']['bloqueado'] = 1;
		$envio['LiquidacionSocioEnvio']['liquidacion_id'] = $datos['LiquidacionSocio']['liquidacion_id'];
		$envio['LiquidacionSocioEnvio']['banco_id'] = $datos['LiquidacionSocio']['banco_intercambio'];
		$oLSE->save($envio);
		$ID_ENVIO = $oLSE->getLastInsertID();
		###############################################################################################################################

		App::import('Vendor','crypt');
		$oCRYPT = new Crypt();
		$oTarjeta = new stdClass();

		foreach($socios as $idx => $socio){

			//////////////////********  CARGO DATOS PERSONALES (BRUNO)********************************/////
			// $persona 		= $oSOCIO->getPersona($socio['LiquidacionSocio']['socio_id']);
			$documento = $socio['PersonaDatosPersonales']['documento'];
			$nombre 	= $socio['PersonaDatosPersonales']['nombre'];
			$apellido 	= $socio['PersonaDatosPersonales']['apellido'];
			$fechaNacimiento = $socio['PersonaDatosPersonales']['fecha_nacimiento'];
			$calle 			= $socio['PersonaDatosPersonales']['calle'];
			$nroCalle 	= $socio['PersonaDatosPersonales']['numero_calle'];
			$piso				= $socio['PersonaDatosPersonales']['piso']." ".$socio['PersonaDatosPersonales']['dpto'];
			$codigoPostal = $socio['PersonaDatosPersonales']['codigo_postal'];
			$localidad 	= $socio['PersonaDatosPersonales']['localidad'];
			$provincia 	= $socio['Provincia']['nombre'];
			$pais = "ARGENTINA";
			$codArea = $socio['PersonaDatosPersonales']['telefono_movil_c'];
			$nroCel = $socio['PersonaDatosPersonales']['telefono_movil'];
			$email = $socio['PersonaDatosPersonales']['e_mail'];
			$beneficioId = $socio['PersonaBeneficio']['id'];
			
			$fechaAltaSocio = $socio['Socio']['fecha_alta'];

			$interval = "mensual";
			$intervalCount = 1;
			$periodos = 1;
			$tipoTarjeta = "debito";

			$zenrise = NULL;

			if (!empty($socio['PersonaBeneficio']['tarjeta_debito'])) {
				$tarjetaDebito = $oCRYPT->decrypt($socio['PersonaBeneficio']['tarjeta_debito']);
				$oTarjeta = unserialize($tarjetaDebito);
				$nroTarjeta = $oTarjeta->card_number;
				$fechaVencimiento = date('y-m-d', strtotime(
					$oTarjeta->card_expiration_year
					.'-'
					.str_pad($oTarjeta->card_expiration_month,2,0,STR_PAD_LEFT)
					.'-01'
				));				
				
				$zenrise = array(
					'documento' => $documento,
					'nombre'=>$nombre,
					'apellido'=>$apellido,
					'fechaNacimiento'=>$fechaNacimiento,
					'calle'=>$calle,
					'nroCalle'=>$nroCalle,
					'piso'=>$piso,
					'codigoPostal'=>$codigoPostal,
					'localidad'=>$localidad,
					'provincia'=>$provincia,
					'pais'=>$pais,
					'codArea'=>$codArea,
					'nroCel'=>$nroCel,
					'email'=>$email,
					'interval'=>$interval,
					'intervalCount'=>$intervalCount,
					'periodos'=>$periodos,
					'tipoTarjeta'=>$tipoTarjeta,
					'fechaVencimiento'=>$fechaVencimiento,
					'nroTarjeta'=>$nroTarjeta,
				    'beneficioId' => $beneficioId
				);
			}

			/*debug($zenrise);
			exit();*/

			$envioRegistro = array();

			$socio['LiquidacionSocio']['ERROR_INTERCAMBIO'] = "OK";

			$socio['LiquidacionSocio']['error_cbu'] = 0;
//		$socio['LiquidacionSocio']['fecha_debito'] = $oLS->armaFecha($datos['LiquidacionSocio']['fecha_debito']);
    	$socio['LiquidacionSocio']['fecha_debito'] = $datos['LiquidacionSocio']['fecha_debito'];

//		$calificacion = $oSOCIO->getUltimaCalificacion($socio['LiquidacionSocio']['socio_id'],$socio['LiquidacionSocio']['persona_beneficio_id']);
			$calificacion = $oSOCIO->getUltimaCalificacion($socio['LiquidacionSocio']['socio_id'],NULL,FALSE,FALSE,FALSE,$PERIODO);
			$socio['LiquidacionSocio']['ultima_calificacion'] = $calificacion;

			$bancoIntercambio	= $datos['LiquidacionSocio']['banco_intercambio'];
			$fechaDebito		= $socio['LiquidacionSocio']['fecha_debito'];
			$importe 			= $socio['LiquidacionSocio']['importe_adebitar'];
			$registroNro 		= $socio['LiquidacionSocio']['registro'];
			$idDebito 			= $oLS->__genDebitoID($socio,($datos['LiquidacionSocio']['banco_intercambio'] === '00011' ? false : true));
			$liquidacionSocioId = $socio['LiquidacionSocio']['id'];
			$socioId 			= $socio['LiquidacionSocio']['socio_id'];
			$sucursal 			= $socio['LiquidacionSocio']['sucursal'];
			$cuenta 			= $socio['LiquidacionSocio']['nro_cta_bco'];
			$cbu 				= $socio['LiquidacionSocio']['cbu'];
			$codOrganismo 		= $socio['LiquidacionSocio']['codigo_organismo'];
			$calificacion 		= $socio['LiquidacionSocio']['ultima_calificacion'];
			$apenom		 		= str_replace(","," ",$socio['LiquidacionSocio']['apenom']);
			$ndoc		 		= $socio['LiquidacionSocio']['documento'];
			$beneficioBancoId   = $socio['LiquidacionSocio']['banco_id'];
      $liquidacionID 		= $socio['LiquidacionSocio']['liquidacion_id'];
      $convenioBcoCba     = ( isset($datos['LiquidacionSocio']['nro_convenio_cba']) ? $datos['LiquidacionSocio']['nro_convenio_cba'] : null);
      $socioCuitCuil     = $socio['LiquidacionSocio']['cuit_cuil'];
      $nroArchivo     = (isset($datos['LiquidacionSocio']['nro_archivo']) ? $datos['LiquidacionSocio']['nro_archivo'] : 1);
      $fechaPresentacion = $datos['LiquidacionSocio']['fecha_presentacion'];
      $fechaMaxima = (isset($datos['LiquidacionSocio']['fecha_maxima']) ? $datos['LiquidacionSocio']['fecha_maxima'] : null);
      $ciclos = (isset($datos['LiquidacionSocio']['nro_ciclos']) ? $datos['LiquidacionSocio']['nro_ciclos'] : 1);
      $idDebitoMin 			= $oLS->__genDebitoID($socio,TRUE);
      
      
      // banco ADSUS 
//       $this->out($socio['LiquidacionSocio']['importe_adebitar']);
      /*
       * Nancy (11/02/2025)
       * Debe ser que por más que en liquidación de deuda salgan los registros altos a la hora de mandar el archivo los cortaba a los 7500
       */
      //if($bancoIntercambio == '99960') {         
          // $importeDebito = ($importe < 7500 ? $importe : 7500);
          // $importe = ( ceil($importeDebito) % 10 === 0) ? ceil($importeDebito) : round(($importeDebito + 10 / 2) / 10 )* 10;
      //}
      
//       $this->out($importe);

      //genRegistroDisketteBanco($bancoIntercambio, $fechaDebito, $importe, $registroNro, $idDebito, $liquidacionSocioId, $socioId, $sucursal, $cuenta, $cbu, $codOrganismo, $apenom, $ndoc, $calificacion = null, $beneficioBancoId = null,$liquidacionID = null)
      $registro = $oBanco->genRegistroDisketteBanco($bancoIntercambio,$fechaDebito,$importe,$registroNro,$idDebito,$liquidacionSocioId,$socioId,$sucursal,$cuenta,$cbu,$codOrganismo,$apenom,$ndoc,$calificacion,$beneficioBancoId,$liquidacionID,$convenioBcoCba,$socioCuitCuil,$nroArchivo,$fechaPresentacion,$fechaMaxima,$ciclos,$idDebitoMin,$zenrise,$fechaAltaSocio);

// 			debug($registro);

			$socio['LiquidacionSocio']['importe_adebitar'] = $registro['importe_debito'];
			$socio['LiquidacionSocio']['error_cbu'] = $registro['error'];
			$socio['LiquidacionSocio']['intercambio'] = $registro['cadena'];
			$socio['LiquidacionSocio']['error_intercambio'] = null;
			if(!empty($registro['mensaje']))$socio['LiquidacionSocio']['error_intercambio'] = $registro['mensaje'];

			$socio['LiquidacionSocio']['sucursal'] = $registro['sucursal_formed'];
			$socio['LiquidacionSocio']['nro_cta_bco'] = $registro['cuenta_formed'];


//		if(!empty($registro['cadena']))$this->out("$i|$total\t" . $registro['cadena'],false);
      $asinc->auditable = false;
			$asinc->actualizar($i,$total,"[$i|$total] " . $socio['LiquidacionSocio']['apenom'] . "|" . $registro['cadena']);

			$ACUM_REGISTROS++;
			$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];

			###############################################################################################################################
			#GENERO LA LIQUIDACION_SOCIO_ENVIO_REGISTROS
			###############################################################################################################################
			$envioRegistro['LiquidacionSocioEnvioRegistro']['id'] = 0;
			$envioRegistro['LiquidacionSocioEnvioRegistro']['liquidacion_socio_envio_id'] = $ID_ENVIO;
			$envioRegistro['LiquidacionSocioEnvioRegistro']['liquidacion_socio_id'] = $socio['LiquidacionSocio']['id'];
			$envioRegistro['LiquidacionSocioEnvioRegistro']['socio_id'] = $socioId;
			$envioRegistro['LiquidacionSocioEnvioRegistro']['identificador_debito'] = $idDebito;
			$envioRegistro['LiquidacionSocioEnvioRegistro']['importe_adebitar'] = $importe;
			$envioRegistro['LiquidacionSocioEnvioRegistro']['registro'] = $registro['cadena'];
			$envioRegistro['LiquidacionSocioEnvioRegistro']['excluido'] = $registro['error'];
			$envioRegistro['LiquidacionSocioEnvioRegistro']['motivo'] = $registro['mensaje'];
			$envioRegistro['LiquidacionSocioEnvioRegistro']['user_created'] = $asinc->getPropietario();
      $oLSER->auditable = false;
			$oLSER->save($envioRegistro);
			###############################################################################################################################

			if($registro['error'] == 0){
				$ACUM_REGISTROS_DISK++;
				$ACUM_IMPORTE_DISK += $socio['LiquidacionSocio']['importe_adebitar'];
				array_push($registros,$socio);
			}else{
				$this->Temporal->setErrorMsg($socio['LiquidacionSocio']['documento'] . " - " . $socio['LiquidacionSocio']['apenom'],$envioRegistro['LiquidacionSocioEnvioRegistro']['motivo']);
				$ACUM_REGISTROS_ERROR++;
			}

			//ACTUALIZO LA TABLA LIQUIDACION_SOCIOS
			$update = array(
				'LiquidacionSocio.importe_adebitar' => $socio['LiquidacionSocio']['importe_adebitar'],
				'LiquidacionSocio.error_cbu' => $socio['LiquidacionSocio']['error_cbu'],
				'LiquidacionSocio.banco_intercambio' => "'$bancoIntercambio'",
				'LiquidacionSocio.fecha_debito' => "'$fechaDebito'",
				'LiquidacionSocio.intercambio' => "'".$socio['LiquidacionSocio']['intercambio']."'",
				'LiquidacionSocio.sucursal' => "'".$socio['LiquidacionSocio']['sucursal']."'",
				'LiquidacionSocio.nro_cta_bco' => "'".$socio['LiquidacionSocio']['nro_cta_bco']."'",
				'LiquidacionSocio.ultima_calificacion' => "'".$socio['LiquidacionSocio']['ultima_calificacion']."'",
				'LiquidacionSocio.error_intercambio' => "'".$socio['LiquidacionSocio']['error_intercambio']."'",
			);

//            debug($socio);

			$oLS->auditable = false;
			if(!$oLS->updateAll($update,array('LiquidacionSocio.id' => $socio['LiquidacionSocio']['id']))){
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
			$envio['LiquidacionSocioEnvio']['id'] = $ID_ENVIO;
			$envio['LiquidacionSocioEnvio']['bloqueado'] = 0;
			$envio['LiquidacionSocioEnvio']['banco_nombre'] = $socios['diskette']['banco_intercambio_nombre'];
			$envio['LiquidacionSocioEnvio']['fecha_debito'] = date('Y-m-d',strtotime($socios['diskette']['fecha_debito']));
			$envio['LiquidacionSocioEnvio']['cantidad_registros'] = $socios['diskette']['cantidad_registros'];
			$envio['LiquidacionSocioEnvio']['status'] = $socios['diskette']['status'];
			$envio['LiquidacionSocioEnvio']['importe_debito'] = $socios['diskette']['importe_debito'];
			$envio['LiquidacionSocioEnvio']['observaciones'] = $socios['diskette']['observaciones'];
			$envio['LiquidacionSocioEnvio']['longitud_registro'] = $socios['diskette']['longitud_registro'];
			$envio['LiquidacionSocioEnvio']['uuid'] = $socios['diskette']['uuid'];
			$envio['LiquidacionSocioEnvio']['archivo'] = $socios['diskette']['archivo'];
			$envio['LiquidacionSocioEnvio']['lote'] = $socios['diskette']['lote'];
			$envio['LiquidacionSocioEnvio']['user_created'] = $asinc->getPropietario();
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
		$oLQ->desbloquear($datos['LiquidacionSocio']['liquidacion_id']);

//		$resultados['info_procesada'] = $socios;

//
//		$registros = Set::extract("/LiquidacionSocio[error_cbu=0]/intercambio",$socios);
//		$importes = Set::extract("/LiquidacionSocio[error_cbu=0]/importe_adebitar",$socios);
//		$resultados['diskette'] = $oBanco->genDisketteBanco($bancoIntercambio,$fechaDebito,$ACUM_REGISTROS_DISK,$ACUM_IMPORTE_DISK,$datos['LiquidacionSocio']['nro_archivo'],$registros);
//
//		debug($resultados);

	}

}

?>

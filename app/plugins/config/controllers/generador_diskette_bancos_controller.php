<?php

/**
* 	adrian
* 	30/06/2010
*/

class GeneradorDisketteBancosController extends AppController {

	var $uses = null;
	var $name = 'GeneradorDisketteBancos';

	function beforeFilter(){
		$this->Seguridad->allow('formatos','encriptar_bcocba','excel_municipio','excel_zenrise','excel_reintegros',
                        'excel_cobrodigital','excel_fenanjor','excel_cuenca',
                        'excel_arcofisa','excel_sicon','unificar_comafi',
                        'excel_cjpc', 'excel_arcofisa2','excel_bcocomer','zip_coinag','bna','unificar_cronocred','divide_celesol', 'excel_firstdata',
                        'excel_cofincred', 'excel_reversos_santander', 'divide_liquidacion', 'excel_cjpc_main');
		parent::beforeFilter();
	}

	function index(){
		$this->redirect('exportar');
	}

	function exportar($UID = null, $fileDownload = false){

		$datos = array();
		$ERROR = FALSE;

		if(!empty($UID) && !$fileDownload && $this->Session->check($UID."_DISKETTE")):
			$this->set('datos',$this->Session->read($UID."_DISKETTE"));
			$this->render('txt','blank');
		endif;
                
		if(!empty($UID) && $fileDownload):
                    $file = WWW_ROOT . "files" . DS . "reportes" . DS . $UID;
                    $datos = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    $datosDecoded = unserialize(base64_decode($datos[0]));
                    $this->set('datos',$datosDecoded);
                    $this->render('txt','blank');
		endif;                

		if(!empty($this->data)){

			if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0):

				$partes = explode('.',$this->data['GeneradorDisketteBanco']['archivo_datos']['name']);

				if(strtolower($partes[1]) == 'xls'):

					//genero un ID para meterlo en la sesion
					$UID = String::uuid();

					if($this->Session->check($UID."_DISKETTE"))$this->Session->del($UID."_DISKETTE");

					App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
					App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
					App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

					App::import('Model','Config.Banco');
					$oBANCO = new Banco();

					$oPHPExcel = new PHPExcel();
					$oXLS = PHPExcel_IOFactory::load($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);

					$objWorksheet = $oXLS->getActiveSheet();
			        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
			        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
			        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

			        if($highestColumnIndex != 7){
			        	$this->Mensaje->error("LA PLANILLA NO TIENE LAS COLUMNAS ESPECIFICAS, VERIFIQUE CON EL FORMATO ESTABLECIDO");
			        	$ERROR = TRUE;
			        }

			        $REGISTROS = 0;
			        $IMPORTE = 0;

			        $fechaDebito = $oBANCO->armaFecha($this->data['GeneradorDisketteBanco']['fecha_debito']);
			        $fechaDebito = date('Ymd',strtotime($fechaDebito));

			        for ($row = 1; $row <= $highestRow; ++$row):

			        	$fila = array();
			        	$fila['identificador'] = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
			        	$fila['registro'] = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
			        	$fila['comprobante'] = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
			        	$fila['sucursal'] = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
			        	$fila['cuenta'] = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
			        	$fila['cbu'] = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
			        	$fila['importe'] =
			        	$legalChars = "%[^0-9\-\. ]%";
			        	$fila['importe'] =  preg_replace($legalChars,"",$objWorksheet->getCellByColumnAndRow(6, $row)->getValue());
			        	$fila['fecha_debito'] = date('d-m-Y',strtotime($fechaDebito));

                        //LIMPIO LAS COMAS DEL IDENTIFICADOR
			        	$legalChars = "%[^ A-Za-z0-9_]%";
			        	$fila['identificador'] = preg_replace($legalChars," ",$fila['identificador']);
			        	//VALIDACIONES

			        	$fila['error'] = 0;
			        	$fila['status'] = 'OK';
			        	if(!$oBANCO->isCbuValido($fila['cbu'])){
			        		$fila['error'] = 1;
			        		$fila['status'] = 'ERROR CBU';
			        	}elseif((intval($fila['sucursal']) == 0) || (intval($fila['cuenta']) == 0)){
			        		$fila['error'] = 1;
			        		$fila['status'] = 'ERROR SUCURSAL / CUENTA';
			        	}


			        	//armo el registro
			        	if($this->data['GeneradorDisketteBanco']['banco_intercambio'] == '00020'):
							$campos = array(
											2 => $fila['sucursal'],
											5 => $fila['cuenta'],
											6 => $fila['importe'],
											7 => $fechaDebito,
											8 => $this->data['GeneradorDisketteBanco']['nro_empresa'],
											9 => $fila['comprobante'],
											10 => $fila['cbu'],
											11 => $fila['registro'],
                                                                                        12=> $fila['identificador'],
//											12 => substr($fila['identificador'],0,18).str_pad($this->data['GeneradorDisketteBanco']['nro_empresa'],4,0,STR_PAD_LEFT).  str_pad($fila['registro'],2,0,STR_PAD_LEFT),

							);
			        		$fila['string'] = $oBANCO->armaStringDebitoBcoCba($campos);
//                                                $fila['string'] = substr($fila['string'],0,51) . str_pad($row,6,"0",STR_PAD_LEFT).substr($fila['string'],57,  strlen($fila['string']));

			        	endif;

			        	if($this->data['GeneradorDisketteBanco']['banco_intercambio'] == '00430'):
							$campos = array(
											3 => substr($fila['cbu'],0,8),
											4 => substr($fila['cbu'],8,14),
											5 => $fila['identificador'],
											6 => $fila['importe'],
											7 => $fila['comprobante'],
											8 => $fechaDebito
							);
							$fila['string'] = $oBANCO->armaStringDebitoStandarBank($campos);
//							$fila['fecha_debito'] = $this->data['GeneradorDisketteBanco']['fecha_debito'];

			        	endif;

			        	if($this->data['GeneradorDisketteBanco']['banco_intercambio'] == '00011'):
							$campos = array(
											2 => $fila['sucursal'],
											4 => $fila['cuenta'],
											5 => $fila['importe'],
											10 => $fila['identificador'],
							);
							$fila['string'] = $oBANCO->armaStringDebitoBcoNacion($campos);
//							$fila['fecha_debito'] = $this->data['GeneradorDisketteBanco']['fecha_debito'];

			        	endif;

			        	if($fila['error'] == 0){
			        		$REGISTROS++;
			        		$IMPORTE += $fila['importe'];
			        	}

						array_push($datos,$fila);

			        endfor;

//			        debug($this->data);

					$diskette = array();

					$diskette['banco_intercambio'] = $this->data['GeneradorDisketteBanco']['banco_intercambio'];
					$diskette['archivo'] = $this->data['GeneradorDisketteBanco']['archivo_salida'];
					$diskette['info_cabecera'] = array();
					$diskette['info_pie'] = array();

					if($this->data['GeneradorDisketteBanco']['banco_intercambio'] == '00020'):
						//VALIDAR DATOS DE ENTRADA
						$errores = array();
						if(empty($this->data['GeneradorDisketteBanco']['nro_empresa'])) $errores[0] = "DEBE INDICAR EL NUMERO DE EMPRESA DE 4 DIGITOS.";
						if(!empty($errores)) $datos = null;
						$this->Mensaje->errores("OCURRIERON LOS SIGUIENTES ERRORES, VERIFICAR!",$errores);
						$diskette['archivo'] = "DEB". str_pad(trim($this->data['GeneradorDisketteBanco']['nro_empresa']),5,0,STR_PAD_LEFT).".HAB";

					endif;

					if($this->data['GeneradorDisketteBanco']['banco_intercambio'] == '00430'):
						$diskette['info_cabecera'][0] = $this->data['GeneradorDisketteBanco']['nro_cuit'];
						$diskette['info_cabecera'][1] = ($this->data['GeneradorDisketteBanco']['moneda_cuenta_banco_nacion'] == 'P' ? 0 : 1);
						$diskette['info_cabecera'][2] = $this->data['GeneradorDisketteBanco']['prestacion'];
						$diskette['info_cabecera'][3] = $fechaDebito;
						$diskette['info_pie'][0] = $REGISTROS;
						$diskette['info_pie'][1] = round($IMPORTE,2);
						$diskette['cabecera'] = $oBANCO->armaStringCabeceraStandarBank($diskette['info_cabecera']);
						$diskette['pie'] = $oBANCO->armaStringPieStandarBank($diskette['info_pie']);

						//VALIDO DATOS DE ENTRADA
						$errores = array();
						if(empty($this->data['GeneradorDisketteBanco']['nro_cuit'])) $errores[0] = "DEBE INDICAR EL NUMERO DE CUIT";
						if(empty($this->data['GeneradorDisketteBanco']['prestacion'])) $errores[1] = "DEBE INDICAR EL CONCEPTO DE LA PRESTACION";
						if(empty($this->data['GeneradorDisketteBanco']['archivo_salida'])) $errores[2] = "DEBE INDICAR EL NOMBRE DEL ARCHIVO A GENERAR";
						if(!empty($errores)) $datos = null;
						$this->Mensaje->errores("OCURRIERON LOS SIGUIENTES ERRORES, VERIFICAR!",$errores);

					endif;

					if($this->data['GeneradorDisketteBanco']['banco_intercambio'] == '00011'):

//						debug($this->data);
						$diskette['info_cabecera'][0] = $this->data['GeneradorDisketteBanco']['sucursal_bco_nacion'];
						$diskette['info_cabecera'][1] = $this->data['GeneradorDisketteBanco']['tipo_cuenta_banco_nacion'];
						$diskette['info_cabecera'][2] = $this->data['GeneradorDisketteBanco']['cuenta_banco_nacion'];
						$diskette['info_cabecera'][3] = $this->data['GeneradorDisketteBanco']['moneda_cuenta_banco_nacion'];
						$diskette['info_cabecera'][4] = $fechaDebito;
						$diskette['info_cabecera'][5] = $this->data['GeneradorDisketteBanco']['nro_archivo_banco_nacion'];
						$diskette['info_cabecera'][6] = $this->data['GeneradorDisketteBanco']['lote_banco_nacion'];
						$diskette['info_pie'][0] = $REGISTROS;
						$diskette['info_pie'][1] = round($IMPORTE,2);

						$diskette['cabecera'] = $oBANCO->armaStringCabeceraBancoNacion($diskette['info_cabecera']);
						$diskette['pie'] = $oBANCO->armaStringPieBancoNacion($diskette['info_pie']);

						//VALIDO DATOS DE ENTRADA
						$errores = array();
						if(empty($this->data['GeneradorDisketteBanco']['sucursal_bco_nacion'])) $errores[0] = "DEBE INDICAR EL NUMERO DE SUCURSAL";
						if(empty($this->data['GeneradorDisketteBanco']['cuenta_banco_nacion'])) $errores[1] = "DEBE INDICAR EL NUMERO DE CUENTA";
						if(empty($this->data['GeneradorDisketteBanco']['archivo_salida'])) $errores[2] = "DEBE INDICAR EL NOMBRE DEL ARCHIVO A GENERAR";
						if(empty($this->data['GeneradorDisketteBanco']['nro_archivo_banco_nacion'])) $errores[3] = "DEBE INDICAR EL NUMERO DE ARCHIVO (1,2,3,...,N)";
						if(!empty($errores)) $datos = null;
						$this->Mensaje->errores("OCURRIERON LOS SIGUIENTES ERRORES, VERIFICAR!",$errores);


					endif;

					$diskette['registros'] = Set::extract("/string",$datos);

					$this->Session->write($UID."_DISKETTE",$diskette);

					$this->set('UID',$UID);

//					if($ERROR){$datos = null;}



				else:

					$this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xls)");

				endif;

			else:

				$this->Mensaje->error("INDICAR EL ARCHIVO");

			endif;

		}

		if($ERROR)$datos = null;

		$this->set('datos',$datos);

	}

	function importar($UID = null){

//		$planilla = null;
		$planilla_XLS = null;
		$XLS = array();

		if(!empty($this->data)){

//			DEBUG($this->data);
//			EXIT;

			if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0):

				if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == "text/plain" || $this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == "application/octet-stream"):

					//TODO OK ABRO EL ARCHIVO Y LO CARGO EN UN ARRAY
					$registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);

					if(!empty($registros)):

						App::import('Model','Config.Banco');
						$oBanco = new Banco();

						$bancoIntercambio = $this->data['GeneradorDisketteBanco']['banco_intercambio'];

						$bancoTipoRegistro = $oBanco->read(null,$bancoIntercambio);

						$tipoRegistro = $bancoTipoRegistro['Banco']['tipo_registro'];
						$longitudRegistro = $bancoTipoRegistro['Banco']['longitud'];

						$idRegistroDetalle = trim($bancoTipoRegistro['Banco']['indicador_detalle']);

						$rows = array();
						$ERROR = FALSE;
						$PRIMERO = TRUE;



						$i = 0;

						foreach($registros as $registro):

							$primerCaracter = substr(trim($registro),0,1);
							$decode = array();
							$XLS_COLS = array();

							$registro = preg_replace("[\n|\r|\n\r]","", $registro);
							$ln = strlen($registro);

//                                                        debug($registro);

//							if($longitudRegistro != $ln && $PRIMERO){
//
//								$errores = array();
//								$errores[0] = "Longitud de registro requerida para el Banco <strong>" . $bancoTipoRegistro['Banco']['nombre'] ."</strong> = <strong>$longitudRegistro</strong>";
//								$errores[1] = "Longitud de registro detectada en el archivo <strong>" . $this->data['GeneradorDisketteBanco']['archivo_datos']['name'] ."</strong> = <strong>$ln</strong>";
//								$errores[2] = "Verifique que el archivo que intenta procesar corresponda al Banco seleccionado.";
//								$this->Mensaje->errores("ERROR EN ANALISIS DEL ARCHIVO.",$errores);
//								$ERROR = TRUE;
//								break;
//							}

							//controlo que para el caso del nacion venga el caracter D en la posicion 6
							if($bancoIntercambio == '00011' && $PRIMERO):
								$marcaNacionEnvio = substr(trim($registro),18,1);
								if($marcaNacionEnvio == 'E'):
									$this->Mensaje->error("El archivo <strong>" . $this->data['GeneradorDisketteBanco']['archivo_datos']['name'] . "</strong> correspondiente al Banco <strong>".$bancoTipoRegistro['Banco']['nombre']."</strong> es incorrecto (esta procesando el de envío).");
									$ERROR = TRUE;
									break;
								endif;
							endif;


							$PRIMERO = FALSE;
							//#  	IDENTIFICADOR  	R  	COMPROBANTE  	SUCURSAL  	CUENTA  	CBU  	FECHA DEBITO  	IMPORTE

							if($tipoRegistro == 1):
								$i++;
								if($bancoIntercambio == '00020'):
									$decode = $oBanco->decodeStringDebitoBcoCbaGeneral($registro);
									//#  	IDENTIFICADOR  	R  	COMPROBANTE  	SUCURSAL  	CUENTA  	CBU  	FECHA DEBITO  	IMPORTE
									$XLS_COLS[0] = $decode[11];
									$XLS_COLS[1] = $decode[10];
									$XLS_COLS[2] = $decode[8];
									$XLS_COLS[3] = $decode[1];
									$XLS_COLS[4] = $decode[4];
									$XLS_COLS[5] = $decode[9];
									$XLS_COLS[6] = $decode[6];
									$XLS_COLS[7] = $decode[5];
									$XLS_COLS[8] = $decode[13];
									$XLS_COLS[9] = $decode[14];

								endif;


							elseif($tipoRegistro == 3 && $idRegistroDetalle == $primerCaracter):

								if($bancoIntercambio == '00430'):
									$decode = $oBanco->decodeStringDebitoStandarBankGeneral($registro);
									//#  	IDENTIFICADOR  	R  	COMPROBANTE  	SUCURSAL  	CUENTA  	CBU  	FECHA DEBITO  	IMPORTE
									$decoCBU = $oBanco->deco_cbu($decode[6].$decode[7]);
									$XLS_COLS[0] = $decode[10];
									$XLS_COLS[1] = 0;
									$XLS_COLS[2] = $decode[9];
									$XLS_COLS[3] = (isset($decoCBU['sucursal']) ? $decoCBU['sucursal'] : "");
									$XLS_COLS[4] = (isset($decoCBU['nro_cta_bco']) ? $decoCBU['nro_cta_bco'] : "");
									$XLS_COLS[5] = $decode[6].$decode[7];
									$XLS_COLS[6] = $decode[4];
									$XLS_COLS[7] = $decode[8];
									$XLS_COLS[8] = $decode[17];
									$XLS_COLS[9] = $decode[18];

								endif;

								if($bancoIntercambio == '00011'):
									$decode = $oBanco->decodeStringDebitoBcoNacionGeneral($registro);
									//#  	IDENTIFICADOR  	R  	COMPROBANTE  	SUCURSAL  	CUENTA  	CBU  	FECHA DEBITO  	IMPORTE
									$XLS_COLS[0] = $decode[8];
									$XLS_COLS[1] = 0;
									$XLS_COLS[2] = 0;
									$XLS_COLS[3] = $decode[1];
									$XLS_COLS[4] = $decode[3];
									$XLS_COLS[5] = str_pad("", 22, "0", STR_PAD_LEFT);
									$XLS_COLS[6] = $decode[5];
									$XLS_COLS[7] = $decode[4];
									$XLS_COLS[8] = $decode[11];
									$XLS_COLS[9] = $decode[12];

								endif;

							endif;
//							debug($decode);
							if(!empty($decode))array_push($rows,$decode);
							if(!empty($XLS_COLS))array_push($XLS,$XLS_COLS);

						endforeach;

//						debug($rows);
//						exit;

						//armo las columnas para el excel
						$planilla = array();
						$cols = array();
						$XLS_COLS_TITLES = array();
						$XLS_COLS_TITLES[0] = "IDENTIFICADOR";
						$XLS_COLS_TITLES[1] = "R";
						$XLS_COLS_TITLES[2] = "COMPROBANTE";
						$XLS_COLS_TITLES[3] = "SUCURSAL";
						$XLS_COLS_TITLES[4] = "CUENTA";
						$XLS_COLS_TITLES[5] = "CBU";
						$XLS_COLS_TITLES[6] = "FECHA_DEBITO";
						$XLS_COLS_TITLES[7] = "IMPORTE";
						$XLS_COLS_TITLES[8] = "STATUS";
						$XLS_COLS_TITLES[9] = "PAGO";

						$planilla_XLS['hoja'] = $bancoTipoRegistro['Banco']['nombre'];

						$planilla_XLS['titulos'] = array(
							'A2' => $bancoTipoRegistro['Banco']['nombre'],
						);

						$planilla_XLS['columnas'] = $XLS_COLS_TITLES;
						$planilla_XLS['renglones'] = $XLS;

//						if($bancoIntercambio == '00020'):
//							$cols[0] = "TIPO_CONVENIO";
//							$cols[1] = "SUCURSAL";
//							$cols[2] = "MONEDA";
//							$cols[3] = "SISTEMA";
//							$cols[4] = "CUENTA";
//							$cols[5] = "IMPORTE";
//							$cols[6] = "FECHA_RENDICION";
//							$cols[7] = "EMPRESA";
//							$cols[8] = "COMPROBANTE";
//							$cols[9] = "CBU";
//							$cols[10] = "REGISTRO";
//							$cols[11] = "ID_CLIENTE";
//							$cols[12] = "CODIGO";
//							$cols[13] = "DESC_CODIGO";
//							$cols[14] = "INDICA_PAGO";
//
//							$planilla['columnas'] = $cols;
//							$planilla['renglones'] = $rows;
//
//						endif;
//
//						if($bancoIntercambio == '00430'):
//							$cols[0] = "REGISTRO";
//							$cols[1] = "PRESTACION";
//							$cols[2] = "VTO_ORIGINAL";
//							$cols[3] = "FECHA_COMPENS";
//							$cols[4] = "FECHA_RENDIC";
//							$cols[5] = "CODIGO_TRANSACC";
//							$cols[6] = "BLOQUE1_CBU";
//							$cols[7] = "BLOQUE2_CBU";
//							$cols[8] = "MONTO_DEBITADO";
//							$cols[9] = "IDENTIFICADOR";
//							$cols[10] = "ID_CLIENTE";
//							$cols[11] = "CODIGO";
//							$cols[12] = "NOTIFICACION_CAMBIO";
//							$cols[13] = "FECHA_RECHAZO";
//							$cols[14] = "FECHA_REVERSA";
//							$cols[15] = "INFO_ADICIONAL";
//							$cols[16] = "FILLER";
//							$cols[17] = "DESC_CODIGO";
//							$cols[18] = "INDICA_PAGO";
//
//							$planilla['columnas'] = $cols;
//							$planilla['renglones'] = $rows;
//
//						endif;
//
//						if($bancoIntercambio == '00011'):
//							$cols[0] = "REGISTRO";
//							$cols[1] = "SUCURSAL";
//							$cols[2] = "SISTEMA";
//							$cols[3] = "NRO_CUENTA";
//							$cols[4] = "IMPORTE";
//							$cols[5] = "VENCIMIENTO";
//							$cols[6] = "ESTADO";
//							$cols[7] = "MOTIVO_RECHAZO";
//							$cols[8] = "ID_CLIENTE";
//							$cols[9] = "FILLER";
//							$cols[10] = "CODIGO";
//							$cols[11] = "DESC_CODIGO";
//							$cols[12] = "INDICA_PAGO";
//
//							$planilla['columnas'] = $cols;
//							$planilla['renglones'] = $rows;
//
//						endif;

						$this->set('banco',$bancoTipoRegistro['Banco']['nombre']);


					else:

						$this->Mensaje->error("ARCHIVO VACIO");

					endif;
				else:

					$errores = array();
					$errores[0] = "Archivo analizado: <strong>" . $this->data['GeneradorDisketteBanco']['archivo_datos']['name'] ."</strong>";
					$errores[1] = "Tipo de archivo detectado: <strong>" . $this->data['GeneradorDisketteBanco']['archivo_datos']['type'] ."</strong>";
					$errores[2] = "Tipo de archivo requerido: <strong>text/plain</strong>";
					$this->Mensaje->errores("TIPO DE ARCHIVO INCORRECTO.",$errores);


				endif;

			else:
				$this->Mensaje->error("INDICAR EL ARCHIVO");

			endif;

//			exit;

		}

		$this->set('planilla_XLS',$planilla_XLS);
		if(!empty($planilla_XLS) && !$ERROR) $this->render('xls','blank');
	}


	function formatos(){}


	function leerArchivo($path){
		if(!file_exists($path)) return false;
		$registros = array();
		$registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if(!is_array($registros)) return null;
		return $registros;
	}


    function encriptar_bcocba($UID = null){

		$datos = array();
		$ERROR = FALSE;

        $files = array();

		if(!empty($UID) && $this->Session->check($UID."_DISKETTE")):
			$this->set('datos',$this->Session->read($UID."_DISKETTE"));
			$this->render('txt','blank');
		endif;

		if(!empty($this->data)){

            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){


                $diskette = array();

                $diskette['banco_intercambio'] = '00020';
                $diskette['archivo'] = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                $diskette['info_cabecera'] = array();
                $diskette['info_pie'] = array();

                App::import('Model','config.EncriptadorBancoCordoba');
                $oENC = new EncriptadorBancoCordoba();

                $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);

                $diskette['registros'] = null;

                $ACCION = (isset($this->data['GeneradorDisketteBanco']['accion']) ? $this->data['GeneradorDisketteBanco']['accion'] : "");

                if(!empty($registros)){
                    $lin = 1;
                    foreach($registros as $i => $registro){
                        if($ACCION == 'ENCRIPTAR'){
                            $enc = $oENC->encripta($registro,$lin);
                            $diskette['registros'][$i] = $enc . "\r\n";
                        }else if($ACCION == 'DESENCRIPTAR'){
                            $enc = $oENC->desencripta($registro,$lin);
                            $diskette['registros'][$i] = $enc . "\r\n";
                        }else if($ACCION == 'NORMALIZAR'){
                            $diskette['registros'][$i] = trim($registro) . "\r\n";
                        }
                        $lin++;
                    }
                }

                App::import('Model','Config.Banco');
                $oBANCO = new Banco();


//                debug($diskette);
//                exit;
//
//                if(isset($this->data['GeneradorDisketteBanco']['accion']) && $this->data['GeneradorDisketteBanco']['accion'] == 'NORMALIZAR'){
//                    debug($diskette['registros']);
//                }
//                exit;

                // TRAER LAS LIQUIDACIONES
                App::import('Model','mutual.Liquidacion');
                $oLQ = new Liquidacion();
                $liquidaciones = $oLQ->find('all',array('conditions' => array('Liquidacion.imputada' => 0)));
                $ids = Set::extract("{n}.Liquidacion.id", $liquidaciones);
                
                $sql = "select liquidacion_id, concat(lpad(socio_id,12,0), lpad(liquidacion_id, 8, 0)) as identificador from liquidacion_socios where liquidacion_id in (" . implode(",", $ids) . ")
                        group by liquidacion_id, socio_id;";
                App::import('Model','pfyj.Socio');
                $oSOCIO = new Socio();
                $socios = $oSOCIO->query($sql); 
                $identificadores = Set::extract("{n}.0.identificador", $socios);
                
                foreach ($ids as $i => $value ) {
                    $ids[$i] = str_pad($value, 8, '0', STR_PAD_LEFT);
                }
                
                
                if($ACCION == 'DESENCRIPTAR'){
                    
                    foreach($diskette['registros'] as $cadena){
                        
                        $decode = $oBANCO->decodeStringDebitoBcoCbaGeneral($cadena);
//                         $idl = $this->get_cba_liquidacion($cadena);
//                         $idl = str_pad($idl, 8, '0', STR_PAD_LEFT);
//                         if(in_array(substr($decode[11],0,20), $identificadores)) {
//                             $files[] = $cadena;
//                         }
                        $files[] = $cadena;
                    }
                    
                    if(!empty($files)){

                        ksort($files);

                       $diskette_1 = array();
                       $diskette_1['uuid'] = $UID = String::uuid();
                       $diskette_1['banco_intercambio'] = '00020';
                       $diskette_1['archivo'] = $idl."_".$this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                       $diskette_1['info_cabecera'] = array();
                       $diskette_1['info_pie'] = array();
                       $diskette_1['registros'] = array();
                       
                       
                        foreach ($files as $idl => $cadena_1){
                            
                            $diskette_1['registros'][] = $cadena_1;
                            
//                             $linea = 1;
//                             $files[$idl] = array();

//                             $diskette_1 = array();
//                             $diskette_1['uuid'] = $UID = String::uuid();
//                             $diskette_1['banco_intercambio'] = '00020';
//                             $diskette_1['archivo'] = $idl."_".$this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
//                             $diskette_1['info_cabecera'] = array();
//                             $diskette_1['info_pie'] = array();
//                             $diskette_1['registros'] = array();
                            
//                             array_push($diskette_1['registros'], $cadena_1);

//                             foreach($diskette['registros'] as $i => $cadena_2){
//                                 $idl2 = $this->get_cba_liquidacion($cadena_2);
//                                 if(trim($idl2) === trim($idl)){
//                                     array_push($diskette_1['registros'], $cadena_2);
//                                 }
//                                 $linea++;
//                             }


                        }
                        $diskette_1['lineas'] = count($diskette_1['registros']);
                        
                        if($this->Session->check($UID."_DISKETTE"))$this->Session->del($UID."_DISKETTE");
                        $this->Session->write($UID."_DISKETTE",$diskette_1);
//                         $files[$idl] = $diskette_1;
                        
                    }
                    $datos[0] = $diskette_1;

                }else{
                    $this->set('datos',$diskette);
                    $this->render('txt','blank');
                }
//                debug($files);

//                $this->set('datos',$diskette);
//                $this->render('txt','blank');


            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }

        }

		if($ERROR)$datos = null;

		$this->set('datos',$datos);


    }


    function excel_municipio(){

		$datos = array();
		$ERROR = FALSE;

        if(!empty($this->data)){
            
            
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){
                
                $XLS_TYPES = array(
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                );
                
                if(in_array($this->data['GeneradorDisketteBanco']['archivo_datos']['type'], $XLS_TYPES)){

					$UID = String::uuid();

					if($this->Session->check($UID."_DISKETTE"))$this->Session->del($UID."_DISKETTE");

					App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
					App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
					App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

					App::import('Model','Config.Banco');
					$oBANCO = new Banco();

					App::import('Model','pfyj.Socio');
					$oSOCIO = new Socio();

                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
                    $inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $registros = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);


			        $fechaDebito = $oBANCO->armaFecha($this->data['GeneradorDisketteBanco']['fecha_debito']);
                    //$fechaDebito = date('Ymd',strtotime($fechaDebito));
                    
                    foreach($registros as $registro){
                        $fila = array();
                        $fila['ndoc'] = $registro['A'];
                        $socio = $oSOCIO->getSocioByDocumento(str_pad($fila['ndoc'],8,'0',STR_PAD_LEFT));
                        $fila['apenom'] = utf8_decode(substr($registro['B'],0,53));
                        $legalChars = "%[^0-9\-\. ]%";
                        $fila['importe'] =  preg_replace($legalChars,"",$registro['C']);
                        $fila['socio_id'] = $socio['Socio']['id'];
                        if($fila['importe'] != 0)$fila['status'] = "001";
                        else $fila['status'] = "000";
                        $fila['fecha_debito'] = $fechaDebito;
                        $fila['error'] = 0;
                        
                        if(is_numeric($fila['importe'])) {
                            $campos = array(
                                            1 => $fila['ndoc'],
                                            2 => $fila['apenom'],
                                            3 => $fila['importe'],
                                            4 => $fila['socio_id'],
                                            5 => $fila['status'].date('Ymd',strtotime($fechaDebito)),
                            );
                            $fila['string'] = $oBANCO->armaStringDebitoMutual($campos);

                            array_push($datos,$fila);                              
                        }

                    }
                    
                    $diskette = array();
                    $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
                    $fileName = str_replace(" ", "",$fileName);
                    $diskette['banco_intercambio'] = "99999";
                    $diskette['archivo'] = $fileName."_lote.txt";
                    $diskette['info_cabecera'] = array();
                    $diskette['info_pie'] = array();

                    $diskette['registros'] = Set::extract("/string",$datos);

                    $this->Session->write($UID."_DISKETTE",$diskette);

                    $this->set('UID',$UID);



                }else{
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xls)");
                }


            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }

		if($ERROR)$datos = null;

		$this->set('datos',$datos);

    }

		//AGREGO FUNCION PARA CONVERTIR ARCHIVO DE ZENRISE (BRUNO)
		function excel_zenrise(){

		$datos = array();
		$ERROR = FALSE;

        if(!empty($this->data)){
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){

					$UID = String::uuid();

					if($this->Session->check($UID."_DISKETTE"))$this->Session->del($UID."_DISKETTE");

					App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
					App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
					App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

					App::import('Model','Config.Banco');
					$oBANCO = new Banco();

					App::import('Model','pfyj.Socio');
					$oSOCIO = new Socio();

					$oPHPExcel = new PHPExcel();
					$oXLS = PHPExcel_IOFactory::load($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);

							$registros = $oXLS->getActiveSheet()->toArray(null, true, true, true);
							array_shift($registros);
							

			                 $fechaDebito = $oBANCO->armaFecha($this->data['GeneradorDisketteBanco']['fecha_debito']);
			                 
			                 $dnis = Set::extract('{n}.E',$registros);	
			                 $dnisFiltrados = array();
			                 foreach ($dnis as $i => $dni){
			                     if(intval($dni) !== 0 && !empty($dni)) {
			                         array_push($dnisFiltrados, str_pad(trim($dni),8,0,STR_PAD_LEFT));
			                     }
			                 }

			                 $sql = "select Socio.id,Socio.persona_id,Persona.id,Persona.cuit_cuil,Persona.documento,Persona.apellido,Persona.nombre from personas as Persona
                                    inner join socios Socio on Socio.persona_id = Persona.id
                                    where Persona.documento in ('" . implode("','", $dnisFiltrados) . "');";
			                 
			                 $socios = $oSOCIO->query($sql);
			                 
			                 $registrosFiltrados = array();
			                 foreach($registros as $registro) {
			                     if(intval($registro['F']) !== 0 && !empty($registro['F'])) {
			                         array_push($registrosFiltrados, $registro);
			                     }
			                 }
			                 

			                 foreach($registrosFiltrados as $registro){

								$fila = array();
								$fila['nombre_completo'] = utf8_decode($registro['B']);
                                $fila['email'] = strtoupper(utf8_decode($registro['C']));
                                $fila['descripcion'] = strtoupper(iconv("utf-8","ascii//TRANSLIT",$registro['D']));
								$fila['ref_ext_contacto'] = utf8_decode($registro['E']);
								$fila['documento_contacto'] = utf8_decode($registro['F']);
								$fila['ref_ext_factura'] = utf8_decode($registro['G']);
								$fila['monto_a_pagar'] = round(floatval($registro['H']),2);
								$fila['monto_pagado'] = round(floatval($registro['I']),2);
								$fila['medio_pago'] = utf8_decode($registro['J']);
								$fila['primera_fecha_venc'] = utf8_decode($registro['K']);
								$fila['segunda_fecha_venc'] = utf8_decode($registro['L']);
								$fila['fechas_pago'] = utf8_decode($registro['M']);
								$fila['fecha_transferencia'] = utf8_decode($registro['N']);
								$fila['nombre_grupos'] = utf8_decode($registro['O']);
								$fila['estado'] = utf8_decode($registro['P']);
								

								if(empty($fila['ref_ext_factura'])){
								    $fila['socio_id'] = 0;
								    $fila['apenom_sigem'] = '';
								    $persona = Set::extract("/Persona[documento=".trim($fila['ref_ext_contacto'])."]",$socios);
								    if(!empty($persona)){
								        $socio = Set::extract("/Socio[persona_id=".$persona[0]['Persona']['id']."]",$socios);
								        if(!empty($socio)){
								            $fila['error'] = 0;
								            $fila['socio_id'] = $socio[0]['Socio']['id'];
								            $fila['apenom_sigem'] = $persona[0]['Persona']['apellido']." ".$persona[0]['Persona']['nombre'];
								        }
								    }
								    $fila['ref_ext_factura'] = substr(str_pad($fila['socio_id'],12,'0',STR_PAD_LEFT).str_pad('',10,'0',STR_PAD_RIGHT),0,22);
								}
								
								
								$estado = 'UNDEFINED';
								
								switch (strtolower(substr($fila['estado'],0,5))) {

								    case 'pagad':
								        $estado = 'PAGADO';
								        break;
								    case 'acred':
								        $estado = 'ACREDITADO';
								        break;
								    case 'pendi': 
								        $estado = 'PENDIENTE';
								        break;
								    case 'Devol': 
								        $estado = 'DEVOLUCION';
								        break;
								    case 'contr':
								        $estado = 'DEVOLUCION';
								        break;
								}

								$campos = array(
										0 => $fila['nombre_completo'],
										1 => $fila['email'],
										2 => $fila['descripcion'],
										3 => $fila['ref_ext_contacto'],
										4 => $fila['ref_ext_factura'],
										5 => $fila['monto_a_pagar'],
										6 => $fila['monto_pagado'],
										7 => $fila['medio_pago'],
										8 => $fila['primera_fecha_venc'],
										9 => $fila['segunda_fecha_venc'],
										10 => $fila['fechas_pago'],
										11 => $fila['fecha_transferencia'],
										12 => $fila['nombre_grupos'],
								        13 => $estado,
								);

								if(!$fila['error']){
										$fila['string'] = $oBANCO->armaStringZenrise($campos);
								}
								
								array_push($datos,$fila);

							}
							

										$diskette = array();
										$fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                		$fileName = str_replace(".", "-",$fileName);
                		$fileName = str_replace(" ", "",$fileName);
										$diskette['banco_intercambio'] = "99998";
										$diskette['archivo'] = $fileName."_lote.txt";
										$diskette['info_cabecera'] = array();
										$diskette['info_pie'] = array();

										$diskette['registros'] = Set::extract("/string",$datos);

										$this->Session->write($UID."_DISKETTE",$diskette);

										$this->set('UID',$UID);

                }else{
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xlsx)");
                }

            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }

				if($ERROR)$datos = null;

				$this->set('datos',$datos);

    }


    function get_cba_liquidacion($cadena,$start=97,$offset=4,$length=4){       
        App::import('Model','Config.Banco');
        $oBANCO = new Banco();
        $decode = $oBANCO->decodeStringDebitoBcoCba($cadena);
        return ($decode['socio_id'] !== 0 ? $decode['liquidacion_id'] : 0);
    }



    function excel_reintegros(){
		$datos = array();
		$ERROR = FALSE;
        if(!empty($this->data)){
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){
                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.ms-excel' || $this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/download'){
                    $UID = String::uuid();
                    if($this->Session->check($UID."_DISKETTE"))$this->Session->del($UID."_DISKETTE");
					App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
					App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
					App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

					App::import('Model','Config.Banco');
					$oBANCO = new Banco();

					App::import('Model','pfyj.Socio');
					$oSOCIO = new Socio();

					$inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
					$rows = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
					$registros = array_slice($rows, 4);
					
					$fechaDebito = $oBANCO->armaFecha($this->data['GeneradorDisketteBanco']['fecha_debito']);
					
					foreach ($registros as $registro) {
					    $fila = array();
					    $fila['ndoc'] = (is_numeric($registro['A']) ? $registro['A'] : NULL);
					    $socio = $oSOCIO->getSocioByDocumento(str_pad($fila['ndoc'],8,'0',STR_PAD_LEFT));
					    $fila['apenom'] = str_replace(","," ", $registro['B']);
					    $fila['beneficio'] = $registro['C'];
					    $legalChars = "%[^0-9\-\. ]%";
					    $fila['importe'] =  preg_replace($legalChars,"",$registro['I']);
					    $fila['socio_id'] = (!empty($socio) ? $socio[0]['Socio']['id'] : NULL);
					    $fila['status'] = "001";
					    $fila['fecha_debito'] = $fechaDebito;
					    $fila['error'] = (empty($fila['socio_id']) ? 1 : 0);
					    if(!$fila['error']) {
					        $campos = array(
					            0 => 2,
					            2 => $fila['ndoc'],
					            7 => $fila['importe'],
					            8 => 'COBRADO ** REINTEGROS **',
					            9 => 'RNT',
					            10 => '1',
					            11 => $fechaDebito,
					            
					        );
					        
// 					        $fila['string'] = $oBANCO->armaStringLoteIntercambioGeneral($campos);

					        $campos = array(
					            1 => $fila['ndoc'],
					            2 => $fila['apenom'],
					            3 => $fila['importe'],
					            4 => $fila['socio_id'],
					            5 => '001'.date('Ymd', strtotime($fechaDebito))
					        );
					        
					        $fila['string'] = $oBANCO->armaStringDebitoMutual($campos);
					        // armaStringDebitoMutual
					        array_push($datos,$fila);
					    }
					}
					
					$diskette = array();
					$fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
                    $fileName = str_replace(" ", "",$fileName);
					$diskette['banco_intercambio'] = "99999";
					$diskette['archivo'] = $fileName."_lote.txt";
// 					$diskette['cabecera'] = $oBANCO->armaStringLoteIntercambioGeneral(array(1));
// 					$diskette['pie'] = $oBANCO->armaStringLoteIntercambioGeneral(array(3));

					$diskette['registros'] = Set::extract("/string",$datos);

					$this->Session->write($UID."_DISKETTE",$diskette);

					$this->set('UID',$UID);
                }else{
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xls)");
                }
            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }
		if($ERROR)$datos = null;

		$this->set('datos',$datos);
    }

    function excel_cobrodigital($action = 'XLS_TO_TXT'){

        $datos = array();
        $ERROR = FALSE;

        App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
        App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
        App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

        App::import('Model','Config.Banco');
        $oBANCO = new Banco();

        App::import('Model','pfyj.Socio');
        $oSOCIO = new Socio();

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();


        if(!empty($this->data) && $action == 'XLS_TO_TXT'){

            $types = array("application/vnd.ms-excel","application/download", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");


            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if(in_array($this->data['GeneradorDisketteBanco']['archivo_datos']['type'],$types)){

                    $UID = String::uuid();
                    if($this->Session->check($UID."_DISKETTE")){
                        $this->Session->del($UID."_DISKETTE");
                    }

                    $inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $registros = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    array_shift($registros);

                    $datos = array();

                    $codigos = $oCODIGO->getCodigos("99910");
                    $cuits = Set::extract('{n}.M',$registros);

                    $sql = "select Socio.id,Socio.persona_id,Persona.id,Persona.cuit_cuil,Persona.documento,Persona.apellido,Persona.nombre from personas as Persona
                            inner join socios Socio on Socio.persona_id = Persona.id
                            where lpad(trim(Persona.cuit_cuil),11,0) in ('" . implode("','", $cuits) . "');";
                    $socios = $oSOCIO->query($sql);

                    $file = WWW_ROOT . "files" . DS . "reportes" . DS . $UID;
                    
                    if(file_exists($file)) {
                        unlink($file);
                    }
                    
                    $TXT_FILE = new File($file, true);
                    
                    foreach($registros as $registro){

                        $fila = array();
                        $fila['fecha_debito'] = date("Y-m-d", strtotime(str_replace("/","-", $registro['B'])));
                        $fila['tipo'] = strtoupper(utf8_decode($registro['A']));
                        $fila['apenom'] = strtoupper(utf8_decode($registro['L']));
                        $fila['cuit_cuil'] = trim($registro['M']);
                        $fila['ndoc'] = substr(trim($fila['cuit_cuil']),2,8);
                        $fila['concepto'] = strtoupper($registro['N']);
                        $fila['observaciones'] = preg_replace('([^A-Za-z0-9 ])', '', trim(strtoupper(utf8_decode($registro['O']))));
                        //$fila['importe'] = round(floatval(str_replace(",",".",str_replace(".","",$registro['C']))),2);
                        $fila['importe'] = round(floatval(str_replace('.','',$registro['C'])),2);
                        $fila['estado'] = strtoupper($registro['A']);
                        $fila['codigo_estado'] = 'ERR';
                        $fila['indica_pago'] = '0';
                        $fila['reversado'] = strtoupper($registro['J']);
                        
                        

                        if(in_array($fila['estado'], array('DEBITADO','COMPLETADO','SIN OBSERVACIONES'))){

                            $fila['codigo_estado'] = '001';
                            $fila['indica_pago'] = 1;

                        }else{
                            $codigo = Set::extract("/BancoRendicionCodigo[descripcion=".trim($fila['observaciones'])."]",$codigos);
                            if(!empty($codigo)){
                                $fila['codigo_estado'] = $codigo[0]['BancoRendicionCodigo']['codigo'];
                                $fila['indica_pago'] = $codigo[0]['BancoRendicionCodigo']['indica_pago'];
                            }
                            $fila['estado'] = trim($fila['observaciones']);

                        }
                        
                        if($fila['reversado'] !== "INACTIVO") {
                            $fila['estado'] = "REVERSO DE DEBITO";
                            $fila['codigo_estado'] = "REV";
                            $fila['indica_pago'] = 0;
                        }
                        
                        
                        $fila['error'] = 1;
                        $fila['socio_id'] = 0;
                        $fila['apenom_sigem'] = '';
                        $persona = Set::extract("/Persona[cuit_cuil=".trim($fila['cuit_cuil'])."]",$socios);
                        if(!empty($persona)){
                            $socio = Set::extract("/Socio[persona_id=".$persona[0]['Persona']['id']."]",$socios);
                            if(!empty($socio)){
                                $fila['error'] = 0;
                                $fila['socio_id'] = $socio[0]['Socio']['id'];
                                $fila['apenom_sigem'] = $persona[0]['Persona']['apellido']." ".$persona[0]['Persona']['nombre'];
                            }
                        }
                        if($fila['error']){
                            $fila['estado'] = "S/DATO PERS";
                        }

                        
                       
                        $cad1 = preg_replace("[^A-Za-z0-9]\s+", "", $fila['apenom']);
                        $cad2 = preg_replace("[^A-Za-z0-9]\s+", "", $fila['apenom_sigem']);

                        $fila['error_nombre'] = 0;
                        if($cad1 !== $cad2){
                            $fila['error_nombre'] = 1;
                        }

                        $campos = array(
                            0 => $fila['socio_id'],
                            1 => $fila['fecha_debito'],
                            2 => preg_replace("[^A-Za-z0-9]", "", $fila['apenom']),
                            3 => $fila['cuit_cuil'],
                            4 => preg_replace("[^A-Za-z0-9]", "", $fila['concepto']),
                            5 => $fila['importe'],
                            6 => preg_replace("[^A-Za-z0-9]", "", $fila['codigo_estado'].substr($fila['estado'],0,20)),
                        );

                        if(!$fila['error']){
                            $fila['string'] = $oBANCO->armaStringDebitoCobroDigital($campos);
                            $fila['string_ln'] = strlen($fila['string']);
                            if(intval($fila['string_ln']) !== 134) {
                                $fila['error'] = 1;
                                $fila['estado'] = "Error Longitud";
                            }
                        }
                        array_push($datos,$fila);
                        

                    }

                    $diskette = array();
                    $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
                    $fileName = str_replace(" ", "",$fileName);

                    $fechaDebito = $oBANCO->armaFecha($this->data['GeneradorDisketteBanco']['fecha_debito']);

                    $diskette['banco_intercambio'] = "99910";
                    $diskette['archivo'] = "COBRODIGITAL_".date('Ymd',  strtotime($fechaDebito)).".txt";
                    $diskette['info_cabecera'] = array();
                    $diskette['info_pie'] = array();
                    $diskette['longitud_registro'] = 134;
                    $diskette['registros'] = Set::extract("/string",$datos);
                    $this->Session->write($UID."_DISKETTE",$diskette);
                    $this->set('UID',$UID);
                    
                    $TXT_FILE->append(base64_encode(serialize($diskette)));
                    
                    $TXT_FILE->close();
                    
                }else{
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel .xls");
                }
            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
            if($ERROR)$datos = null;

            $this->set('datos',$datos);
        }



        if(!empty($this->data) && $action == 'TXT_TO_XLS'){

            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'text/plain'){

                    $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
//                    debug($registros);
//                    exit;
                    if(!empty($registros)){

                        $ID = NULL;
                        $IMP = 0;
                        $idsr = array();
                        $cant = 1;

                        ##############################################################
                        # RECORRO LOS REGISTROS PARA DETECTAR SOCIOS CON MAS DE UN MISMO
                        # IMPORTE DE DEBITO.  LOS GUARDO EN UN ARRAY TOTALIZANDO LA CANTIDAD
                        # DE REGISTROS REPETIDOS. SI TIENE 2 DE 100, GENERO UN REGISTRO UNICO DE 200.
                        ##############################################################

                        foreach ($registros as $key => $value) {

                            $data = $oBANCO->decodeStringDebitoCobroDigital($value);

                            $socio_id = intval($data['socio_id']);
                            $apenom = $data['apenom'];
                            $cuit = $data['cuit_cuil'];
                            $ndoc = $data['documento'];
                            $concepto = $data['concepto'];
                            $fechaDebito = $data['fecha_debito'];
                            $impo_deb = floatval($data['importe_debitado']);

                            if(intval($socio_id) != intval($ID) && $IMP != $impo_deb){

                                $ID = $socio_id;
                                $IMP = $impo_deb;
                                $cant = 1;
                                $tmp = array();

                            }  else {

                                if($IMP === $impo_deb && intval($socio_id) == intval($ID)){
                                    $cant++;
                                    $string = substr($value,0,75).str_pad(number_format($IMP*$cant * 100,0,"",""), 12, '0', STR_PAD_LEFT).substr($value,-45);
                                    $idsr[$socio_id] = array($socio_id,$apenom,$cuit,$concepto,$fechaDebito,$IMP*$cant,$string,$IMP,$cant);
                                }

                            }
                        }
//                        debug($idsr);

                        $newRows = array();

                        /*********************************************************
                         * RECORRO LOS REGISTROS Y VOY GUARDANDO EN UN NUEVO ARRAY
                         * TODOS LOS QUE NO TENGAN REGISTROS DUPLICADOS (SOCIO E IMPORTE)
                         *********************************************************/
                        foreach ($registros as $key => $value){

                            $data = $oBANCO->decodeStringDebitoCobroDigital($value);

                            $socio_id = intval($data['socio_id']);
                            $importeDebito = floatval($data['importe_debitado']);
                            //si no estan dentro del array de unificados los cargo
                            if(floatval($idsr[$socio_id][7]) !== $importeDebito){
                                array_push ($newRows, $value);
                            }
                        }
                        /*******************************************************************
                         * PROCESO LOS QUE TIENEN IMPORTES REPETIDOS Y AGREGO EL NUEVO VALOR
                         *******************************************************************/
                        foreach($idsr as $key => $values){
                            array_push ($newRows, $values[6]);
                        }

                        $cantidadDeArchivos = $this->data['GeneradorDisketteBanco']['registros_xls'];

                        if(empty($cantidadDeArchivos)){
                            $cantidadDeArchivos = count($newRows);
                        }

//                        $n = floor(count($newRows) / $this->data['GeneradorDisketteBanco']['registros_xls']);

                        $files = array_chunk($newRows, $cantidadDeArchivos, FALSE);

                        if(!empty($files)){

                            $planillas_XLS = $planilla_XLS = null;

                            foreach($files as $i => $file){

                                $planilla_XLS = null;

                                if(!empty($file)){

                                    $XLS = $rows = array();

                                    foreach ($file as $key => $value){

                                        $XLS_COLS = $decode = array();
                                        $decode = $oBANCO->decodeStringDebitoCobroDigital($value,FALSE);

                                        $decode['apellido'] = substr($decode['apenom'],0,strpos($decode['apenom'],' '));
                                        $decode['nombre'] = substr($decode['apenom'],strpos($decode['apenom'],' ') + 1, strlen($decode['apenom']));

//                                        debug($decode);

                                        $XLS_COLS[0] = utf8_encode($decode['nombre']);
                                        $XLS_COLS[1] = utf8_encode($decode['apellido']);
                                        $XLS_COLS[2] = $decode['cuit_cuil'];
                                        $XLS_COLS[3] = $decode['cbu'];
                                        $XLS_COLS[4] = round($decode['importe_debitado'],2);
                                        $XLS_COLS[5] = date('Ymd',  strtotime($decode['fecha_debito']));
                                        $XLS_COLS[6] = $decode['concepto'];

                                        if (!empty($decode)) {
                                            array_push($rows, $decode);
                                        }
                                        if (!empty($XLS_COLS)) {
                                            array_push($XLS, $XLS_COLS);
                                        }
                                    }

                                    $XLS_COLS_TITLES = array();
                                    $XLS_COLS_TITLES[0] = "NOMBRE";
                                    $XLS_COLS_TITLES[1] = "APELLIDO";
                                    $XLS_COLS_TITLES[2] = "CUIT";
                                    $XLS_COLS_TITLES[3] = "CBU";
                                    $XLS_COLS_TITLES[4] = "IMPORTE";
                                    $XLS_COLS_TITLES[5] = "FECHA";
                                    $XLS_COLS_TITLES[6] = "CONCEPTO";

                                    $planilla_XLS['hoja'] = "COBRO_DIGITAL";

                                    $planilla_XLS['titulos'] = array(
        //                                    'A2' => "COBRO DIGITAL",
                                    );

                                    $planilla_XLS['columnas'] = $XLS_COLS_TITLES;
                                    $planilla_XLS['renglones'] = $XLS;

//                                    debug($planilla_XLS);

                                    if(!empty($planilla_XLS)){
                                        $planillas_XLS[$i] = $planilla_XLS;
                                    }

                                }

                            }

                        }

                        if(!empty($planillas_XLS)){
                            $this->set('banco',$this->data['GeneradorDisketteBanco']['archivo_datos']['name']);
                            $this->set('planillas_XLS',$planillas_XLS);
                            $this->set('planillas_XLS',$planillas_XLS);
                            $this->set('registros_xls',$this->data['GeneradorDisketteBanco']['registros_xls']);
                            $this->render('xls_cobro_digital_output','blank');
                            return;
                        }

                    }
                }else{

                    $this->Mensaje->error("NO ES UN ARCHIVO DEL TIPO [text/plain] (.txt)");

                }


            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }

    }

    function excel_fenanjor($action = 'XLS_TO_TXT'){

        $datos = array();
        $ERROR = FALSE;

        App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
        App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
        App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

        App::import('Model','Config.Banco');
        $oBANCO = new Banco();

        App::import('Model','pfyj.Socio');
        $oSOCIO = new Socio();

        App::import('Model','pfyj.PersonaBeneficio');
        $oPB = new PersonaBeneficio();

        App::import('Model','mutual.LiquidacionSocio');
        $oLS = new LiquidacionSocio();

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();


        if(!empty($this->data) && $action == 'XLS_TO_TXT'){
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){

                    $UID = String::uuid();

                    if ($this->Session->check($UID . "_DISKETTE")) {
                        $this->Session->del($UID . "_DISKETTE");
                    }





//                    $oPHPExcel = new PHPExcel();

                    $cabecera = $pie = array();


                    if($this->data['GeneradorDisketteBanco']['tipo_reporte'] == 2){



                        App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
                        $inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
                        $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                        $objPHPExcel = $objReader->load($inputFileName);
                        $registros = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                        array_shift($registros);
//                        debug($registros);
//                        exit;
                        if(!empty($registros)){

                            $row = 1;

                            foreach ($registros as $i => $registro){

                                $fila = array();

//                                debug($registro);

                                $socio_id = intval(substr(trim($registro['A']),-6));
                                $socio = $oSOCIO->read(NULL,$socio_id);
//                                debug($socio);
                                $fila['socio_id'] = $socio['Socio']['id'];
                                $fila['apenom'] = strtoupper($socio['Persona']['apellido']." ".$socio['Persona']['nombre']);
                                $fila['cuit_cuil'] = $socio['Persona']['cuit_cuil'];
                                $fila['ndoc'] = $socio['Persona']['documento'];
                                $fila['nro_sucursal'] = str_pad($registro['F'], 5, "0", STR_PAD_LEFT);
                                $fila['nro_cta_banco'] = str_pad($registro['E'], 10, "0", STR_PAD_LEFT);
                                $fila['cbu'] = str_pad($registro['D'], 22, "0", STR_PAD_LEFT);
                                $fila['fecha_debito'] = date("d-m-Y");
                                $legalChars = "%[^0-9\-\. ]%";
                                $fila['importe'] =  preg_replace($legalChars,"",$registro['G']);
                                
                                if (empty($registro['H'])) {
                                    $fila['codigo_estado'] = ($fila['importe'] > 0 ? '000' : '001');
                                } else {
                                    $fila['codigo_estado'] = $registro['H'];
                                }

                                if(!empty($fila['estado'])){
                                    $fila['codigo_estado'] = $oCODIGO->getCodigoByConcepto("99921", trim($fila['estado']));
                                    $fila['codigo_estado'] = (!empty($fila['codigo_estado']) ?  trim($fila['codigo_estado']) : "ERR");
                                }
                                $fila['indica_pago'] = ($oCODIGO->isCodigoPago("99921",$fila['codigo_estado']) ? 1 : 0);
                                $fila['estado_descripcion'] = $oCODIGO->getDescripcionCodigo("99921",$fila['codigo_estado']);
                                if(empty($socio['Socio']['id'])){
                                    $fila['estado_descripcion'] = '*** PERSONA DESCONOCIDA ***';
                                    $fila['indica_pago'] = 0;
                                }


                                $fila['string'] = str_pad($registro['A'], 10, "0", STR_PAD_LEFT);
                                $fila['string'] .= str_pad($fila['ndoc'], 11, "0", STR_PAD_LEFT);
                                $fila['string'] .= str_pad(substr($fila['apenom'],0,40),40," ",STR_PAD_RIGHT);
                                $fila['string'] .= str_pad($fila['cbu'],22,"0",STR_PAD_LEFT);
                                $fila['string'] .= str_pad($fila['nro_cta_banco'],11,"0",STR_PAD_LEFT);
                                $fila['string'] .= str_pad($fila['nro_sucursal'],5,"0",STR_PAD_LEFT);
                                $fila['string'] .= date("Ymd");
                                $fila['string'] .= str_pad(number_format($fila['importe'] * 100,0,"",""), 10, '0', STR_PAD_LEFT);
                                $fila['string'] .= str_pad($fila['codigo_estado'],11," ",STR_PAD_RIGHT);
                                $fila['string'] .= "\r\n";

                                $fila['renglon'] = $row;


                                $fila['error'] = (empty($socio['Socio']['id']) ? 1 : 0);
                                array_push($datos,$fila);
                                $row++;
                            }


                            $diskettes = $diskette = array();
                            $diskette['uid'] = String::uuid();
                            $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                            $fileName = str_replace(".", "-",$fileName);
                            $fileName = str_replace(" ", "",$fileName);
                            $diskette['banco_intercambio'] = "99921";
                            $diskette['archivo'] = $fileName."_lote.txt";


                            $diskette['cabecera'] = array();
                            $diskette['pie'] = array();
                            $diskette['registros'] = Set::extract("/string",$datos);


                            $this->Session->write($diskette['uid']."_DISKETTE",$diskette);
                            $diskettes = array($diskette);
//                            $this->Session->write($UID."_DISKETTE",$diskette);
//
//                            $this->set('UID',$UID);


//                            debug($datos);
//                            exit;
                        }

                        $this->set('diskettes',$diskettes);

                    }

                    if($this->data['GeneradorDisketteBanco']['tipo_reporte'] == 1){
                        
                        
                        $oXLS = PHPExcel_IOFactory::load($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);

                        $objWorksheet = $oXLS->getActiveSheet();
                        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

                        $cabecera = $pie = array();

                        $organismos = array();

                        for ($row = 1; $row <= $highestRow; ++$row){

                            $tipoRegistro = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                            $tipoRegistro = intval($tipoRegistro);


                            if($tipoRegistro === 1){
                                $cabecera = array(
                                    $objWorksheet->getCellByColumnAndRow(0, $row)->getValue(),
                                    $objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),
                                    $objWorksheet->getCellByColumnAndRow(2, $row)->getValue(),
                                    $objWorksheet->getCellByColumnAndRow(3, $row)->getValue(),
                                    $objWorksheet->getCellByColumnAndRow(4, $row)->getValue(),
                                );
                            }
                            if($tipoRegistro === 3){
                                $pie = array(
                                    $objWorksheet->getCellByColumnAndRow(0, $row)->getValue(),
                                    $objWorksheet->getCellByColumnAndRow(1, $row)->getValue(),
                                    $objWorksheet->getCellByColumnAndRow(2, $row)->getValue(),
                                    $objWorksheet->getCellByColumnAndRow(3, $row)->getValue(),
                                    $objWorksheet->getCellByColumnAndRow(4, $row)->getValue(),
                                );
                            }

                            if($tipoRegistro === 2){

                                $fila = array();
                                $nro_cta_banco = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                                $nro_sucursal = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
                                $sistema = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();


                                $socio = $oPB->get_by_sucursal_cuenta($nro_cta_banco, $nro_sucursal,NULL,NULL);

//                                debug($socio);

                                $fecha = intval($objWorksheet->getCellByColumnAndRow(7, $row)->getValue());

                                $organismos[$socio['Organismo']['id']."|".trim($socio['Organismo']['concepto_1'])] = array();


                                $fila['socio_id'] = $socio['Socio']['id'];
                                $fila['codigo_organismo'] = $socio['Organismo']['id'];
                                $fila['codigo_organismo_descripcion'] = $socio['Organismo']['concepto_1'];
                                $fila['fecha_debito'] = date("Y-m-d", strtotime(substr($fecha,0,4)."-".substr($fecha,4,2)."-".substr($fecha,6,2)));
                                $fila['apenom'] = strtoupper($socio['Persona']['apellido']." ".$socio['Persona']['nombre']);
                                $fila['cuit_cuil'] = $socio['Persona']['cuit_cuil'];
                                $fila['ndoc'] = $socio['Persona']['documento'];
                                $legalChars = "%[^0-9\-\. ]%";

                                $fila['importe'] =  preg_replace($legalChars,"",$objWorksheet->getCellByColumnAndRow(6, $row)->getValue());

                                $fila['estado'] = strtoupper($objWorksheet->getCellByColumnAndRow(8, $row)->getValue());

                                $fila['nro_sucursal'] = $nro_sucursal;
                                $fila['nro_cta_banco'] = $nro_cta_banco;
                                $fila['codigo_estado'] = "000";
                                if(!empty($fila['estado'])){
                                    $fila['codigo_estado'] = $oCODIGO->getCodigoByConcepto("00011", trim($fila['estado']));
                                    $fila['codigo_estado'] = (!empty($fila['codigo_estado']) ?  trim($fila['codigo_estado']) : "999");
                                }
                                $fila['indica_pago'] = ($oCODIGO->isCodigoPago("00011",$fila['codigo_estado']) ? 1 : 0);
                                $fila['estado_descripcion'] = $oCODIGO->getDescripcionCodigo("00011",$fila['codigo_estado']);

                                #ARMO LA CADENA DE RENDICION DEL BANCO NACION
                                $fila['string'] = trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue());
                                $fila['string'] .= str_pad($nro_sucursal, 4, "0", STR_PAD_LEFT);
                                $fila['string'] .= str_pad($sistema, 2, "0", STR_PAD_LEFT);
                                $fila['string'] .= str_pad($nro_cta_banco, 11, "0", STR_PAD_LEFT);
                                $fila['string'] .= str_pad(number_format($fila['importe'] * 100,0,"",""), 15, '0', STR_PAD_LEFT);
                                $fila['string'] .= date('Ymd',  strtotime($fila['fecha_debito']));
                                $fila['string'] .= ($fila['indica_pago'] == 1 ? 0 : 9);
                                $fila['string'] .= str_pad($fila['estado'], 30, " ", STR_PAD_RIGHT);
                                $fila['string'] .= str_pad($fila['socio_id'], 10, "0", STR_PAD_LEFT);
                                $fila['string'] .= str_pad("", 46, " ", STR_PAD_LEFT);
                                $fila['string'] .= "\r\n";

                                $fila['renglon'] = $row;
                                $fila['error'] = (empty($socio['Socio']['id']) ? 1 : 0);

                                array_push($datos,$fila);


                            }


                        }

                        foreach($datos as $value){
                            array_push($organismos[$value['codigo_organismo']."|".$value['codigo_organismo_descripcion']], $value);
                        }

//                        debug($datos);
//                        exit;

                        $diskettes = array();

                        foreach($organismos as $id => $organismo){

                            list($codigoOrganismo,$nombreOrganismo) = explode("|", $id);

                            if(!empty($codigoOrganismo)){
                                $diskette = array();
                                $diskette['uid'] = String::uuid();
                                $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                                $fileName = str_replace(".", "-",$fileName);
                                $fileName = str_replace(" ", "",$fileName);
                                $codigoOrganismoNum = substr($codigoOrganismo,-4);
                                $fileName .= "_" . (empty($codigoOrganismoNum) ? 'SIN-ORGANISMO' : str_replace(" ","-", $nombreOrganismo));

                                $diskette['banco_intercambio'] = "99999";
                                $diskette['codigo_organismo'] = $codigoOrganismo;
                                $diskette['organismo'] = $nombreOrganismo;
                                $diskette['archivo'] = $fileName."_lote.txt";

                                $diskette['cabecera'] = $cabecera[0].$cabecera[1].$cabecera[2].$cabecera[3].$cabecera[4];
                                $diskette['cabecera'] = str_pad(trim($diskette['cabecera']),128," ",STR_PAD_RIGHT)."\r\n";


                                $diskette['pie'] = $pie[0].$pie[1].$pie[2].$pie[3].$pie[4];
                                $diskette['pie'] = str_pad(trim($diskette['pie']),128," ",STR_PAD_RIGHT)."\r\n";
                                $diskette['registros'] = Set::extract("/string",$organismo);


                                $this->Session->write($diskette['uid']."_DISKETTE",$diskette);
                                $diskettes[$id] = $diskette;

                            }


                        }
                        $this->set('diskettes',$diskettes);

//                        debug($diskettes);
//                        exit;
//
//                        $diskette = array();
//                        $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
//                        $fileName = str_replace(".", "-",$fileName);
//                        $fileName = str_replace(" ", "",$fileName);
//                        $diskette['banco_intercambio'] = "99999";
//                        $diskette['archivo'] = $fileName."_lote.txt";
//
//
//                        $diskette['cabecera'] = $cabecera[0].$cabecera[1].$cabecera[2].$cabecera[3].$cabecera[4];
//                        $diskette['cabecera'] = str_pad(trim($diskette['cabecera']),128," ",STR_PAD_RIGHT)."\r\n";
//
//
//                        $diskette['pie'] = $pie[0].$pie[1].$pie[2].$pie[3].$pie[4];
//                        $diskette['pie'] = str_pad(trim($diskette['pie']),128," ",STR_PAD_RIGHT)."\r\n";
//                        $diskette['registros'] = Set::extract("/string",$datos);
//
//
//                        $this->Session->write($UID."_DISKETTE",$diskette);
//
//                        $this->set('UID',$UID);

                    }







////		    debug($highestColumnIndex);
//                    if($highestColumnIndex != 11){
////                            $this->Mensaje->error("LA PLANILLA NO TIENE LAS COLUMNAS ESPECIFICAS");
////                            $ERROR = TRUE;
//                    }



//                    $cabecera = $pie = array();



//                    debug($cabecera);
//                    debug($datos);
//                    exit;





                }else{
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xls)");
                }


            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }

        if(!empty($this->data) && $action == 'TXT_TO_XLS'){

            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'text/plain'){


                    $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);

                    if(!empty($registros)){
                        
                        
                        // OBTENER EL ID DEL EMPLEADOR
                        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                        $EMPLEADOR_ID = (isset($INI_FILE['intercambio']['cliente_fenanjor']) && $INI_FILE['intercambio']['cliente_fenanjor'] != 0 ? $INI_FILE['intercambio']['cliente_fenanjor'] : '1001');

                        array_shift($registros);
                        array_pop($registros);

                        $XLS = $rows = array();
                        $planilla_XLS = array();

                        foreach ($registros as $key => $value){

                            $data = $oBANCO->decodeStringDebitoBcoNacion($value);
                            $persona = $oSOCIO->getPersonaBySocioID($data['socio_id']);
//                            debug($persona);

                            $data['apenom'] = $persona['Persona']['apellido']." ".$persona['Persona']['nombre'];
                            $data['ndoc'] =  $persona['Persona']['documento'];

                            $data['cbu'] = $oPB->get_cbu_by_sucursal_cuenta($persona['Persona']['id'], $data['nro_cta_bco'], $data['sucursal']);



                            if(empty($data['cbu'])){
                                $data['cbu'] = $oLS->get_cbu_liquidado($data['liquidacion_id'], $data['socio_id']);
                            }

                            $XLS_COLS[0] = $data['id_debito'];
                            $XLS_COLS[1] = $data['ndoc'];
                            $XLS_COLS[2] = $data['apenom'];
                            $XLS_COLS[3] = $data['cbu'];
                            $XLS_COLS[4] = $data['nro_cta_bco'];
                            $XLS_COLS[5] = $data['sucursal'];
                            $XLS_COLS[6] = 'TUCUMAN 2133';
                            $XLS_COLS[7] = '1050';
                            $XLS_COLS[8] = '1';
                            $XLS_COLS[9] = '1';
                            $XLS_COLS[10] = '';
                            $XLS_COLS[11] = '1';
                            $XLS_COLS[12] = $EMPLEADOR_ID;
                            $XLS_COLS[13] = '';
                            $XLS_COLS[14] = '';
                            $XLS_COLS[15] = round($data['importe_debitado'],2);

                            if (!empty($XLS_COLS)) {
                                array_push($XLS, $XLS_COLS);
                            }

                        }

                        $XLS_COLS_TITLES = array();
                        $XLS_COLS_TITLES[0] = "ID Cliente";
                        $XLS_COLS_TITLES[1] = "Nro. Doc.";
                        $XLS_COLS_TITLES[2] = "Apellido y Nombres";
                        $XLS_COLS_TITLES[3] = "CBU";
                        $XLS_COLS_TITLES[4] = "Nro. Cta.";
                        $XLS_COLS_TITLES[5] = "Sucursal";
                        $XLS_COLS_TITLES[6] = "Direccion";
                        $XLS_COLS_TITLES[7] = "Codigo Postal";
                        $XLS_COLS_TITLES[8] = "Id Localidad";
                        $XLS_COLS_TITLES[9] = "Id Provincia";
                        $XLS_COLS_TITLES[10] = "Fecha Nac.";
                        $XLS_COLS_TITLES[11] = "Id Banco";
                        $XLS_COLS_TITLES[12] = "Id Empleador";
                        $XLS_COLS_TITLES[13] = "Empleador";
                        $XLS_COLS_TITLES[14] = "Fecha Otorg.";
                        $XLS_COLS_TITLES[15] = "Importe";

                        $planilla_XLS['hoja'] = "SOTEIN";
                        $planilla_XLS['columnas'] = $XLS_COLS_TITLES;
                        $planilla_XLS['renglones'] = $XLS;

                        if(!empty($planilla_XLS)){
                            $this->set('banco',$this->data['GeneradorDisketteBanco']['archivo_datos']['name']);
                            $this->set('planilla_XLS',$planilla_XLS);
                            $this->render('xls_fenanjor_output','blank');
                            return;
                        }

                    }

//                    debug($XLS);
//                    exit;

                }else{

                    $this->Mensaje->error("NO ES UN ARCHIVO DEL TIPO [text/plain] (.txt)");

                }


            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }

        }


        if ($ERROR) {
            $datos = null;
        }

        $this->set('datos',$datos);

    }

    public function excel_cuenca($UID=null){

        $files = array();
        $ERROR = FALSE;

        if(!empty($UID) && $this->Session->check($UID."_DISKETTE")){

            Configure::write('debug',0);
            $diskette = $this->Session->read($UID."_DISKETTE");

            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
            header('Cache-Control: max-age=0');
            if (!empty($diskette['cabecera'])) {
                echo $diskette['cabecera'];
            }
            foreach($diskette['registros'] as $registro):
                if(!empty($registro)) {
                    echo $registro;
                }
            endforeach;
            if (!empty($diskette['pie'])) {
                echo $diskette['pie'];
            }
            exit;
        }


        if(!empty($this->data)){


            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.ms-excel'){

                    $UID = String::uuid();

                    if ($this->Session->check($UID . "_DISKETTE")) {
                        $this->Session->del($UID . "_DISKETTE");
                    }

//                    App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
//                    App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

                    App::import('Model','Config.Banco');
                    $oBANCO = new Banco();
//
//                    App::import('Model','pfyj.Socio');
//                    $oSOCIO = new Socio();
//
//                    App::import('Model','pfyj.PersonaBeneficio');
//                    $oPB = new PersonaBeneficio();
//
//                    App::import('Model','mutual.Liquidacion');
//                    $oLIQ = new Liquidacion();


//                    $oPHPExcel = new PHPExcel();

                    $inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $registros = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    array_shift($registros);



                    if(!empty($registros)){

                        $liquidaciones = array();

                        foreach ($registros as $i => $registro){

                            $idLiq = intval(substr($registro['J'],8,5));

                            $cadena = "";
                            $cadena .= str_pad($registro['D'],11,"0",STR_PAD_LEFT);
                            $cadena .= str_pad($registro['E'],22,"0",STR_PAD_LEFT);
                            $cadena .= str_pad($registro['J'],10,"0",STR_PAD_LEFT);
                            $cadena .= str_replace("/", "", $registro['G']);
                            $cadena .= str_replace("/", "", $registro['K']);
                            $cadena .= str_pad(number_format($registro['F'] * 100,0,"",""),10,0,STR_PAD_LEFT);
                            $cadena .= str_pad(trim($registro['C']),10," ",STR_PAD_RIGHT);
                            $cadena .= str_pad(substr($registro['H'],0,1),1," ",STR_PAD_LEFT);
                            $cadena .= str_pad($registro['I'],3,"COB",STR_PAD_LEFT);

                            $liquidaciones[$idLiq][$i] = $cadena;

                            $campos = $oBANCO->decode_str_debito_cuenca($cadena);
//                            debug($campos);
                        }
//                        exit;
//                        debug($liquidaciones);
//                        exit;

                        foreach($liquidaciones as $idLiq => $liquidacion){


                            $diskette_1 = array();
                            $diskette_1['uuid'] = $UID = String::uuid();
                            $diskette_1['banco_intercambio'] = "65203";
                            $diskette_1['archivo'] = $idLiq."_".$this->data['GeneradorDisketteBanco']['archivo_datos']['name'].".txt";
                            $diskette_1['info_cabecera'] = array();
                            $diskette_1['info_pie'] = array();
                            $diskette_1['registros'] = array();


                            foreach($liquidacion as $registro){

                                array_push($diskette_1['registros'], $registro . "\r\n");

                            }
                            $diskette_1['lineas'] = count($diskette_1['registros']);
                            if ($this->Session->check($UID . "_DISKETTE")) {
                                $this->Session->del($UID . "_DISKETTE");
                            }
                            $this->Session->write($UID."_DISKETTE",$diskette_1);
                            $files[$idLiq] = $diskette_1;
                        }


//                        debug($files);
//                        exit;

                    }




                }else{
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xls)");
                }

            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }

        if ($ERROR) {
            $datos = null;
        }

        $this->set('files',$files);

    }


    function excel_arcofisa2($UID=null){

        $datos = array();
        $ERROR = FALSE;
        $BANCO_ID = '99910';

        if(!empty($UID) && $this->Session->check($UID."_DISKETTE")){

            Configure::write('debug',0);
            $diskette = $this->Session->read($UID."_DISKETTE");

            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
            header('Cache-Control: max-age=0');
            if (!empty($diskette['cabecera'])) {
                echo $diskette['cabecera'];
            }
            foreach($diskette['registros'] as $registro):
                if(!empty($registro)) {
                    echo $registro;
                }
            endforeach;
            if (!empty($diskette['pie'])) {
                echo $diskette['pie'];
            }
            exit;
        }


        if(!empty($this->data)){


            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.ms-excel'){

                    $UID = String::uuid();

                    if ($this->Session->check($UID . "_DISKETTE")) {
                        $this->Session->del($UID . "_DISKETTE");
                    }

//                    App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
//                    App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

//                    App::import('Model','Config.Banco');
//                    $oBANCO = new Banco();
//
                    App::import('Model','pfyj.Socio');
                    $oSOCIO = new Socio();

                    App::import('Model','Config.BancoRendicionCodigo');
                    $oCODIGO = new BancoRendicionCodigo();

//
//                    App::import('Model','pfyj.PersonaBeneficio');
//                    $oPB = new PersonaBeneficio();
//
//                    App::import('Model','mutual.Liquidacion');
//                    $oLIQ = new Liquidacion();


//                    $oPHPExcel = new PHPExcel();

                    $inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $registros = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    array_shift($registros);


//                    debug($registros);
//                    exit;

                    if(!empty($registros)){


                        $codigos = $oCODIGO->getCodigos($BANCO_ID);
                        $documentos = Set::extract('{n}.D',$registros);

                        $sql = "select Socio.id,Socio.persona_id,Persona.id,Persona.cuit_cuil,Persona.documento,Persona.apellido,Persona.nombre from personas as Persona
                                inner join socios Socio on Socio.persona_id = Persona.id
                                where Persona.documento in ('" . implode("','", $documentos) . "');";
                        $socios = $oSOCIO->query($sql);

//                        debug($codigos);
//                        debug($socios);
//
//                        exit;

                        foreach ($registros as $i => $registro){

                            $fila = array();

                            $fila['error'] = FALSE;
                            $fila['error_msg'] = '';
                            $fila['ndoc'] = trim($registro['D']);
                            $fila['fecha_debito'] = $registro['E'];
                            $fila['fecha_debito'] = preg_replace('/[^0-9]/', '', $fila['fecha_debito']);
//                            $fila['importe_str'] = $registro['C'];
                            $fila['importe'] = $registro['B'];
                            $fila['status'] = $registro['H'];
                            $fila['movimiento'] = $registro['F'];
                            $fila['motivo_rechazo'] = $registro['G'];

//                            if(strlen($fila['cuit_cuil']) < 11){
//                                $fila['ndoc'] = str_pad(trim($fila['cuit_cuil']), 8, '0', STR_PAD_LEFT);
//                            }else{
//                                $fila['ndoc'] = substr(trim($fila['cuit_cuil']),2,8);
//                            }


//                            $socio = $oSOCIO->getSocioByDocumento(str_pad($fila['ndoc'],8,'0',STR_PAD_LEFT));

                            $fila['socio_id'] = '';
                            $fila['apenom'] = '';
                            $fila['apenom_str'] = '';

                            $persona = Set::extract("/Persona[documento=".trim($fila['ndoc'])."]",$socios);
                            if(!empty($persona)){
                                $socio = Set::extract("/Socio[persona_id=".$persona[0]['Persona']['id']."]",$socios);
                                if(!empty($socio)){
                                    $fila['error'] = FALSE;
                                    $fila['socio_id'] = $socio[0]['Socio']['id'];
                                    $fila['apenom'] = $persona[0]['Persona']['apellido']." ".$persona[0]['Persona']['nombre'];
                                    $fila['apenom'] = utf8_encode($fila['apenom']);
                                    $fila['apenom_str'] = preg_replace("[^A-Za-z0-9]", "", $fila['apenom']);
                                }
                            }else{
                                $fila['error'] = TRUE;
                                $fila['error_msg'] = "S/DATO PERS";
                            }


//                            if(!empty($socio)){
//                                $fila['socio_id'] = $socio['Socio']['id'];
//                                $fila['apenom'] = substr($socio['Persona']['apellido']." ".$socio['Persona']['nombre'],0,40);
//                                $fila['apenom'] = utf8_encode($fila['apenom']);
//                                $fila['apenom_str'] = ereg_replace("[^A-Za-z0-9]", "", $fila['apenom']);
//                            }else{
//                                $fila['error'] = TRUE;
//                                $fila['error_msg'] = 'S/DATO PERS';
//                            }

//                            debug($socio);

                            $fila['codigo'] = str_pad(substr(trim($fila['motivo_rechazo']),0,3), 2, '0', STR_PAD_LEFT);
                            $fila['codigo'] = ($fila['codigo'] == '000' ? '001' : $fila['codigo']);



//                            debug($fila);

//                            $fila['codigo_concepto'] = $oCODIGO->getDescripcionCodigo($BANCO_ID, $fila['codigo']);
                            $codigo = Set::extract("/BancoRendicionCodigo[codigo=".trim($fila['codigo'])."]",$codigos);
                            if(!empty($codigo)){
                                $fila['codigo_estado'] = $codigo[0]['BancoRendicionCodigo']['codigo'];
                                $fila['indica_pago'] = $codigo[0]['BancoRendicionCodigo']['indica_pago'];
                                $fila['codigo_concepto'] = $codigo[0]['BancoRendicionCodigo']['descripcion'];
                            }

                            if(empty($fila['codigo_concepto'])){
                                $fila['error'] = TRUE;
                                $fila['error_msg'] = 'S/CODIGO';
                            }

                            $string = "";

                            if(!$fila['error']){

//                                $string .= str_pad(trim($fila['socio_id']), 6, '0', STR_PAD_LEFT);
//                                $string .= str_pad(trim($fila['apenom_str']), 40, ' ', STR_PAD_RIGHT);
//                                $string .= $fila['fecha_debito'];
//                                $string .= str_pad(trim($fila['cuit_cuil']), 11, '0', STR_PAD_LEFT);
//                                $string .= str_pad("", 10, ' ', STR_PAD_RIGHT);
//                                $string .= str_pad(number_format($fila['importe'] * 100,0,"",""), 12, '0', STR_PAD_LEFT);
//                                $string .= $fila['codigo'];
//                                $string .= str_pad(substr(trim($fila['codigo_concepto']),0,20), 20, ' ', STR_PAD_RIGHT);
//                                $string .= str_pad("", 22, ' ', STR_PAD_RIGHT);
//                                $string .= "\r\n";
//                                $fila['string'] = $string;

                                /**
                                 * INTERFACE COBRO DIGITAL * ENTRADA * (110)
                                 * 1    6   SOCIO_ID
                                 * 2    8   FECHA DEBITO
                                 * 3    40  NOMBRE
                                 * 4    11  CUIT / DOCUMENTO
                                 * 5    10  CONCEPTO DEBITO
                                 * 6    12  IMPORTE
                                 * 7    3  CODIGO
                                 * 8    20 CONCEPTO CODIGO
                                 * 9    22 CBU
                                 * @param type $cadena
                                 * @return array
                                 */

                                $string .= str_pad(trim($fila['socio_id']), 6, '0', STR_PAD_LEFT);
                                $string .= $fila['fecha_debito'];
                                $string .= str_pad(trim($fila['apenom_str']), 40, ' ', STR_PAD_RIGHT);
                                $string .= str_pad(trim($fila['cuit_cuil']), 11, '0', STR_PAD_LEFT);
                                $string .= str_pad("", 10, ' ', STR_PAD_RIGHT);
                                $string .= str_pad(number_format($fila['importe'] * 100,0,"",""), 12, '0', STR_PAD_LEFT);
                                $string .= $fila['codigo'];
                                $string .= str_pad(substr(trim($fila['codigo_concepto']),0,20), 20, ' ', STR_PAD_RIGHT);
                                $string .= str_pad("", 22, '0', STR_PAD_RIGHT);
                                $string .= "\r\n";
                                $fila['string'] = $string;

                                if(strlen($fila['string']) !== 134){
                                    $fila['error'] = TRUE;
                                    $fila['error_msg'] = 'Longitud de Cadena incorrecta. Revisar excel ' . strlen($fila['string']);
                                }


                            }



//                            debug($fila);

                            array_push($datos,$fila);

                        }
//                        exit;
//                        debug($datos);
//                        exit;


                    }


                    $diskette = array();
                    $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
//                    $fileName = str_replace(" ", "",$fileName);

                    $diskette['banco_intercambio'] = "99910";
                    $diskette['archivo'] = "ARCOFISA_".$fileName.".txt";
                    $diskette['info_cabecera'] = array();
                    $diskette['info_pie'] = array();
                    $diskette['registros'] = Set::extract("/string",$datos);
                    $this->Session->write($UID."_DISKETTE",$diskette);
                    $this->set('UID',$UID);

                }else{
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xls)");
                }

            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }

        if ($ERROR) {
            $datos = null;
        }

        $this->set('datos',$datos);
        $this->render('excel_arcofisa');

    }


    function excel_arcofisa($UID=null){

        $datos = array();
        $ERROR = FALSE;
        $BANCO_ID = '99910';

        if(!empty($UID) && $this->Session->check($UID."_DISKETTE")){

            Configure::write('debug',0);
            $diskette = $this->Session->read($UID."_DISKETTE");

            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
            header('Cache-Control: max-age=0');
            if (!empty($diskette['cabecera'])) {
                echo $diskette['cabecera'];
            }
            foreach($diskette['registros'] as $registro):
                if(!empty($registro)) {
                    echo $registro;
                }
            endforeach;
            if (!empty($diskette['pie'])) {
                echo $diskette['pie'];
            }
            exit;
        }


        if(!empty($this->data)){


            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.ms-excel'){

                    $UID = String::uuid();

                    if ($this->Session->check($UID . "_DISKETTE")) {
                        $this->Session->del($UID . "_DISKETTE");
                    }

//                    App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
//                    App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

//                    App::import('Model','Config.Banco');
//                    $oBANCO = new Banco();
//
                    App::import('Model','pfyj.Socio');
                    $oSOCIO = new Socio();

                    App::import('Model','Config.BancoRendicionCodigo');
                    $oCODIGO = new BancoRendicionCodigo();

//
//                    App::import('Model','pfyj.PersonaBeneficio');
//                    $oPB = new PersonaBeneficio();
//
//                    App::import('Model','mutual.Liquidacion');
//                    $oLIQ = new Liquidacion();


//                    $oPHPExcel = new PHPExcel();

                    $inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $registros = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    array_shift($registros);


//                    debug($registros);
//                    exit;

                    if(!empty($registros)){


                        $codigos = $oCODIGO->getCodigos($BANCO_ID);
                        $cuits = Set::extract('{n}.A',$registros);

                        $sql = "select Socio.id,Socio.persona_id,Persona.id,Persona.cuit_cuil,Persona.documento,Persona.apellido,Persona.nombre from personas as Persona
                                inner join socios Socio on Socio.persona_id = Persona.id
                                where Persona.cuit_cuil in ('" . implode("','", $cuits) . "');";
                        $socios = $oSOCIO->query($sql);

//                        debug($codigos);
//                        debug($socios);
//
//                        exit;

                        foreach ($registros as $i => $registro){

                            $fila = array();

                            $fila['error'] = FALSE;
                            $fila['error_msg'] = '';
                            $fila['cuit_cuil'] = trim($registro['A']);
                            $fila['fecha_debito'] = $registro['B'];
                            $fila['fecha_debito'] = preg_replace('/[^0-9]/', '', $fila['fecha_debito']);
//                            $fila['importe_str'] = $registro['C'];
                            $fila['importe'] = $registro['D'];
                            $fila['status'] = $registro['E'];
                            $fila['movimiento'] = $registro['F'];
                            $fila['motivo_rechazo'] = $registro['G'];

                            if(strlen($fila['cuit_cuil']) < 11){
                                $fila['ndoc'] = str_pad(trim($fila['cuit_cuil']), 8, '0', STR_PAD_LEFT);
                            }else{
                                $fila['ndoc'] = substr(trim($fila['cuit_cuil']),2,8);
                            }


//                            $socio = $oSOCIO->getSocioByDocumento(str_pad($fila['ndoc'],8,'0',STR_PAD_LEFT));

                            $fila['socio_id'] = '';
                            $fila['apenom'] = '';
                            $fila['apenom_str'] = '';

                            $persona = Set::extract("/Persona[cuit_cuil=".trim($fila['cuit_cuil'])."]",$socios);
                            if(!empty($persona)){
                                $socio = Set::extract("/Socio[persona_id=".$persona[0]['Persona']['id']."]",$socios);
                                if(!empty($socio)){
                                    $fila['error'] = FALSE;
                                    $fila['socio_id'] = $socio[0]['Socio']['id'];
                                    $fila['apenom'] = $persona[0]['Persona']['apellido']." ".$persona[0]['Persona']['nombre'];
                                    $fila['apenom'] = utf8_encode($fila['apenom']);
                                    $fila['apenom_str'] = preg_replace("[^A-Za-z0-9]", "", $fila['apenom']);
                                }
                            }else{
                                $fila['error'] = TRUE;
                                $fila['error_msg'] = "S/DATO PERS";
                            }


//                            if(!empty($socio)){
//                                $fila['socio_id'] = $socio['Socio']['id'];
//                                $fila['apenom'] = substr($socio['Persona']['apellido']." ".$socio['Persona']['nombre'],0,40);
//                                $fila['apenom'] = utf8_encode($fila['apenom']);
//                                $fila['apenom_str'] = ereg_replace("[^A-Za-z0-9]", "", $fila['apenom']);
//                            }else{
//                                $fila['error'] = TRUE;
//                                $fila['error_msg'] = 'S/DATO PERS';
//                            }

//                            debug($socio);

                            $fila['codigo'] = 'R'.str_pad(trim($fila['motivo_rechazo']), 2, '0', STR_PAD_LEFT);
                            $fila['codigo'] = ($fila['codigo'] == 'R00' ? '001' : $fila['codigo']);


//                            $fila['codigo_concepto'] = $oCODIGO->getDescripcionCodigo($BANCO_ID, $fila['codigo']);
                            $codigo = Set::extract("/BancoRendicionCodigo[codigo=".trim($fila['codigo'])."]",$codigos);
                            if(!empty($codigo)){
                                $fila['codigo_estado'] = $codigo[0]['BancoRendicionCodigo']['codigo'];
                                $fila['indica_pago'] = $codigo[0]['BancoRendicionCodigo']['indica_pago'];
                                $fila['codigo_concepto'] = $codigo[0]['BancoRendicionCodigo']['descripcion'];
                            }

                            if(empty($fila['codigo_concepto'])){
                                $fila['error'] = TRUE;
                                $fila['error_msg'] = 'S/CODIGO';
                            }

                            $string = "";

                            if(!$fila['error']){

//                                $string .= str_pad(trim($fila['socio_id']), 6, '0', STR_PAD_LEFT);
//                                $string .= str_pad(trim($fila['apenom_str']), 40, ' ', STR_PAD_RIGHT);
//                                $string .= $fila['fecha_debito'];
//                                $string .= str_pad(trim($fila['cuit_cuil']), 11, '0', STR_PAD_LEFT);
//                                $string .= str_pad("", 10, ' ', STR_PAD_RIGHT);
//                                $string .= str_pad(number_format($fila['importe'] * 100,0,"",""), 12, '0', STR_PAD_LEFT);
//                                $string .= $fila['codigo'];
//                                $string .= str_pad(substr(trim($fila['codigo_concepto']),0,20), 20, ' ', STR_PAD_RIGHT);
//                                $string .= str_pad("", 22, ' ', STR_PAD_RIGHT);
//                                $string .= "\r\n";
//                                $fila['string'] = $string;

                                /**
                                 * INTERFACE COBRO DIGITAL * ENTRADA * (110)
                                 * 1    6   SOCIO_ID
                                 * 2    8   FECHA DEBITO
                                 * 3    40  NOMBRE
                                 * 4    11  CUIT / DOCUMENTO
                                 * 5    10  CONCEPTO DEBITO
                                 * 6    12  IMPORTE
                                 * 7    3  CODIGO
                                 * 8    20 CONCEPTO CODIGO
                                 * 9    22 CBU
                                 * @param type $cadena
                                 * @return array
                                 */

                                $string .= str_pad(trim($fila['socio_id']), 6, '0', STR_PAD_LEFT);
                                $string .= $fila['fecha_debito'];
                                $string .= str_pad(trim($fila['apenom_str']), 40, ' ', STR_PAD_RIGHT);
                                $string .= str_pad(trim($fila['cuit_cuil']), 11, '0', STR_PAD_LEFT);
                                $string .= str_pad("", 10, ' ', STR_PAD_RIGHT);
                                $string .= str_pad(number_format($fila['importe'] * 100,0,"",""), 12, '0', STR_PAD_LEFT);
                                $string .= $fila['codigo'];
                                $string .= str_pad(substr(trim($fila['codigo_concepto']),0,20), 20, ' ', STR_PAD_RIGHT);
                                $string .= str_pad("", 22, '0', STR_PAD_RIGHT);
                                $string .= "\r\n";
                                $fila['string'] = $string;

                            }



//                            debug($fila);

                            array_push($datos,$fila);

                        }
//                        exit;
//                        debug($datos);
//                        exit;


                    }


                    $diskette = array();
                    $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
//                    $fileName = str_replace(" ", "",$fileName);

                    $diskette['banco_intercambio'] = "99910";
                    $diskette['archivo'] = "ARCOFISA_".$fileName.".txt";
                    $diskette['info_cabecera'] = array();
                    $diskette['info_pie'] = array();
                    $diskette['registros'] = Set::extract("/string",$datos);
                    $this->Session->write($UID."_DISKETTE",$diskette);
                    $this->set('UID',$UID);

                }else{
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel 97/2000/XP (.xls)");
                }

            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }

        if ($ERROR) {
            $datos = null;
        }

        $this->set('datos',$datos);


    }

    function excel_sicon($action = 'XLS_TO_TXT'){

        $datos = array();
        $ERROR = FALSE;

        App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
        App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
        App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

        App::import('Model','Config.Banco');
        $oBANCO = new Banco();

        App::import('Model','pfyj.Socio');
        $oSOCIO = new Socio();

        App::import('Model','pfyj.PersonaBeneficio');
        $oPB = new PersonaBeneficio();

        App::import('Model','mutual.LiquidacionSocio');
        $oLS = new LiquidacionSocio();

        if(!empty($this->data) && $action == 'XLS_TO_XLS'){

            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.ms-excel'){

                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
                    $inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $registros = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    array_shift($registros);
                    array_shift($registros);
                    array_shift($registros);
                    array_shift($registros);
                    array_shift($registros);
                    if(!empty($registros)){

                        $XLS = $rows = array();
                        $planilla_XLS = array();

                        foreach ($registros as $i => $registro){

                            $datosSocio = $oSOCIO->getDatoSocio($registro['F'], TRUE);

                            $XLS_COLS[0] = date('d/m/Y',strtotime($datosSocio['fecha_alta']));
                            $XLS_COLS[1] = $registro['F'];
                            $XLS_COLS[2] = date('d/m/Y',strtotime($registro['K']));
                            $XLS_COLS[3] = $registro['F'];
                            $XLS_COLS[4] = intval($registro['B']);
                            $XLS_COLS[5] = $registro['D']." ".$registro['E'];
                            $XLS_COLS[6] = intval($registro['H'])."-".intval($registro['I']);
                            $XLS_COLS[7] = $registro['J'];
                            $XLS_COLS[8] = "1";
                            $XLS_COLS[9] = "";
                            $XLS_COLS[10] = $registro['G'];
                            $XLS_COLS[11] = (isset($this->data['GeneradorDisketteBanco']['nro_lote']) ? $this->data['GeneradorDisketteBanco']['nro_lote'] : 1);
                            $XLS_COLS[12] = "";

                            if (!empty($XLS_COLS)) {
                                array_push($XLS, $XLS_COLS);
                            }

//                            debug($registro);
                        }

                        $XLS_COLS_TITLES = array();
                        $XLS_COLS_TITLES[0] = "Fecha de Adhesion";
                        $XLS_COLS_TITLES[1] = "Solc. De Adhesion";
                        $XLS_COLS_TITLES[2] = "Fecha de Cobro";
                        $XLS_COLS_TITLES[3] = "Cliente Nro";
                        $XLS_COLS_TITLES[4] = "DNI";
                        $XLS_COLS_TITLES[5] = "Apellido y Nombre";
                        $XLS_COLS_TITLES[6] = "Banco";
                        $XLS_COLS_TITLES[7] = "Importe presentado";
                        $XLS_COLS_TITLES[8] = "Plan Cuotas";
                        $XLS_COLS_TITLES[9] = "Respuesta";
                        $XLS_COLS_TITLES[10] = "CBU/ Cuenta";
                        $XLS_COLS_TITLES[11] = "LOTE";
                        $XLS_COLS_TITLES[12] = "Telefono";

                        $planilla_XLS['hoja'] = "SICON-M22S";
                        $planilla_XLS['columnas'] = $XLS_COLS_TITLES;
                        $planilla_XLS['renglones'] = $XLS;

                        if(!empty($planilla_XLS)){
                            $this->set('banco',$this->data['GeneradorDisketteBanco']['archivo_datos']['name']);
                            $this->set('planilla_XLS',$planilla_XLS);
                            $this->render('xls_sicon_output','blank');
                            return;
                        }


                    }
//                    debug($XLS);
//                    debug($registros);
//                    exit;

                }else{

                    $this->Mensaje->error("NO ES UN ARCHIVO DEL TIPO [ms-excel] (.xls)");

                }


            }else{
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }

        }


        if ($ERROR) {
            $datos = null;
        }

        $this->set('datos',$datos);

    }

    public function unificar_comafi($UID = null,$download = 0){

        $files = array();

        if(!empty($UID) && $this->Session->check($UID."_FILES")){

            $files = $this->Session->read($UID."_FILES");

            if($download){

                Configure::write('debug',0);
                header("Content-type: text/plain");
                header('Content-Disposition: attachment;filename="BANCO_COMAFI_UNIFICADO.txt');
                header('Cache-Control: max-age=0');
                if(!empty($files)){
                    foreach($files as $file){
                        if(!empty($file['registros'])){
                            foreach($file['registros'] as $registro){
                                echo $registro . "\r\n";
                            }
                        }
                    }
                }
                exit;
            }

        }else{

            $UID = String::uuid();
        }


        if(!empty($this->data)){



            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                foreach ($registros as $i => $registro) {
                    $registros[$i] = preg_replace("[^A-Za-z0-9]", "",$registro);
                }
                
                // sacar el total
                $TOTAL = 0;
                foreach ($registros as $registro) {
                    $TOTAL += intval(substr($registro,61,10)) / pow(10,2);
                }

                array_push($files,array('file' => $this->data['GeneradorDisketteBanco']['archivo_datos'], 'registros' => $registros, 'total' => $TOTAL));

                $this->Session->write($UID."_FILES",$files);

            }

        }
        $this->set('UID',$UID);
        $this->set('files',$files);

    }

    public function excel_cjpc($UID=null){

        if(!empty($UID) && $this->Session->check($UID."_DISKETTE")){

            Configure::write('debug',0);
            $diskette = $this->Session->read($UID."_DISKETTE");

            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
            header('Cache-Control: max-age=0');
            if (!empty($diskette['cabecera'])) {
                echo $diskette['cabecera'];
            }
            foreach($diskette['registros'] as $registro):
                if(!empty($registro)) {
                    echo $registro;
                }
            endforeach;
            if (!empty($diskette['pie'])) {
                echo $diskette['pie'];
            }
            exit;
        }

        App::import('Model','Config.Banco');
        $oBANCO = new Banco();

        App::import('Model','pfyj.PersonaBeneficio');
        $oPB = new PersonaBeneficio();
        $datos = array();
        $linea = 1;
//        $UID = NULL;
        if(!empty($this->data)){

            if ($this->Session->check($UID . "_DISKETTE")) {
                $this->Session->del($UID . "_DISKETTE");
            }

            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                $UID = String::uuid();



                $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                if(!empty($registros)){
                    foreach($registros as $registro){
                        
                        $decode = $oBANCO->decodeNuevoStringDebitoCJP($registro);
                        
                        if($decode['sub_codigo'] == 1) {
                            $sql = "select OrdenDescuento.id,OrdenDescuento.importe_cuota from persona_beneficios PersonaBeneficio
                                inner join personas Persona on Persona.id = PersonaBeneficio.persona_id
                                inner join orden_descuentos OrdenDescuento on OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
                                where
                                    PersonaBeneficio.codigo_beneficio = 'MUTUCORG7701'
                                    and OrdenDescuento.activo = 1
                                    and PersonaBeneficio.tipo = '".$decode['tipo']."'
                                    and PersonaBeneficio.nro_ley = '".$decode['nro_ley']."'
                                    and PersonaBeneficio.nro_beneficio = '".$decode['nro_beneficio']."'
                                    and PersonaBeneficio.sub_beneficio = '".$decode['sub_beneficio']."'
                                    and Persona.documento = '".str_pad($decode['documento'],8,'0',STR_PAD_LEFT)."'
                                    and ifnull(OrdenDescuento.nro_referencia_proveedor,'') = '".$decode['orden_descuento_id']."';";
                        }else {
                            
                            $sql = "select OrdenDescuento.id,OrdenDescuento.importe_cuota from persona_beneficios PersonaBeneficio
                                inner join personas Persona on Persona.id = PersonaBeneficio.persona_id
                                inner join orden_descuentos OrdenDescuento on OrdenDescuento.persona_beneficio_id = PersonaBeneficio.id
                                where
                                    PersonaBeneficio.codigo_beneficio = 'MUTUCORG7701'
                                    and OrdenDescuento.activo = 1
                                    and PersonaBeneficio.tipo = '".$decode['tipo']."'
                                    and PersonaBeneficio.nro_ley = '".$decode['nro_ley']."'
                                    and PersonaBeneficio.nro_beneficio = '".$decode['nro_beneficio']."'
                                    and PersonaBeneficio.sub_beneficio = '".$decode['sub_beneficio']."'
                                    and Persona.documento = '".str_pad($decode['documento'],8,'0',STR_PAD_LEFT)."' 
                                    and tipo_orden_dto = 'CMUTU' and tipo_producto = 'MUTUPROD0003' and nueva_orden_descuento_id is null;";
                        }

                        $results = $oPB->query($sql);
                        $solicitudId = $impoCuo = NULL;
                        if(!empty($results)){
                            $solicitudId = $results[0]['OrdenDescuento']['id'];
                            $impoCuo = $results[0]['OrdenDescuento']['importe_cuota'];
                        }
                        $decode['linea'] = $linea;
                        $decode['error'] = 1;
                        $decode['error_msg'] = 'NO SE ENCONTRO LA SOLICITUD';
                        $decode['norden_descuento_id'] = 0;
                        $decode['importe_cuota'] = 0;
                        $decode['cadena_rectificada'] = '';
                        $decode['beneficio_str'] = ($decode['tipo'] == '1' ? 'J' : 'P').$decode['nro_ley'].$decode['nro_beneficio'].$decode['sub_beneficio'];

                        if(!empty($solicitudId)){
                            $decode['error'] = 0;
                            $decode['error_msg'] = '';
                            $decode['norden_descuento_id'] = $solicitudId;
                            $decode['importe_cuota'] = $impoCuo;
                            $cad1 = substr($registro,0,58);
                            $cad2 = str_pad($solicitudId,12,'0',STR_PAD_LEFT);
                            $cad3 = substr($registro,70,11);
                            $decode['cadena_original'] = $registro;
                            $decode['string'] = $cad1.$cad2.$cad3."\r\n";
                        }
                        array_push($datos,$decode);
                        $linea++;

                    }

                    $diskette = array();
                    $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
//                    $fileName = str_replace(" ", "",$fileName);

                    $diskette['banco_intercambio'] = "99999";
                    $diskette['archivo'] = "CJPC_".$fileName.".txt";
                    $diskette['info_cabecera'] = array();
                    $diskette['info_pie'] = array();
                    $diskette['registros'] = Set::extract("/string",$datos);
                    $this->Session->write($UID."_DISKETTE",$diskette);


                }
//                debug($registros);
            }

        }
        $this->set('UID',$UID);
        $this->set('datos',$datos);
    }


    function excel_bcocomer($UID = null){

        if(!empty($UID) && $this->Session->check($UID."_DISKETTE")){

            Configure::write('debug',0);
            $diskette = $this->Session->read($UID."_DISKETTE");

            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
            header('Cache-Control: max-age=0');
            if (!empty($diskette['cabecera'])) {
                echo $diskette['cabecera'];
            }
            foreach($diskette['registros'] as $registro):
                if(!empty($registro)) {
                    echo $registro;
                }
            endforeach;
            if (!empty($diskette['pie'])) {
                echo $diskette['pie'];
            }
            exit;
        }

        $datos = array();

        if(!empty($this->data)){

            if ($this->Session->check($UID . "_DISKETTE")) {
                $this->Session->del($UID . "_DISKETTE");
            }

            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                $UID = String::uuid();

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.ms-excel'){

                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
                    $inputFileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name'];
                    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFileName);
                    $registros = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    array_shift($registros);

                    $linea = 2;

                    if(!empty($registros)){

                        foreach($registros as $registro){

                            $fila = array();
                            $fechaVtoOrig = DateTime::createFromFormat('Y-m-d', $registro['A']);
                            $fechaEnvio = DateTime::createFromFormat('Y-m-d', $registro['B']);
                            $fechaRecibo = DateTime::createFromFormat('Y-m-d', $registro['C']);


                            if(!empty($fechaVtoOrig) && !empty($fechaEnvio) && !empty($fechaRecibo)){

                                $fila['linea'] = $linea;

                                $fila['fecha_vto_orig'] = $fechaVtoOrig->format('Y-m-d');
                                $fila['fecha_envio'] = $fechaEnvio->format('Y-m-d');
                                $fila['fecha_recibo'] = $fechaRecibo->format('Y-m-d');
                                $fila['cod_empresa'] = $registro['D'];
                                $fila['nom_empresa'] = $registro['E'];
                                $fila['nro_op'] = $registro['F'];
                                $fila['cuota'] = $registro['G'];
                                $fila['intento'] = $registro['H'];
                                $fila['importe'] = $registro['I'];
                                $fila['cliente'] = $registro['J'];
                                $fila['respuesta'] = $registro['K'];
                                $fila['desc_rechazo'] = $registro['L'];
                                $fila['cbu'] = $registro['M'];

                                
                                $fila['desc_rechazo'] = utf8_decode(preg_replace("[^A-Za-z0-9]", "", $fila['desc_rechazo']));                                
                                
                                $fila['cadena'] = "";
                                $fila['cadena'] .= str_pad($fila['nro_op'],7,0,STR_PAD_LEFT);
                                $fila['cadena'] .= str_pad($fila['cuota'],3,0,STR_PAD_LEFT);
                                $fila['cadena'] .= str_pad(substr($fila['cliente'],0,40),40," ",STR_PAD_RIGHT);
                                $fila['cadena'] .= str_pad(number_format($fila['importe'] * 100,0,"",""), 7, 0, STR_PAD_LEFT);
                                $fila['cadena'] .= str_pad($fila['intento'],2,0,STR_PAD_LEFT);
                                $fila['cadena'] .= str_pad(date("Ymd",strtotime($fila['fecha_vto_orig'])),8,0,STR_PAD_LEFT);
                                $fila['cadena'] .= str_pad(date("Ymd",strtotime($fila['fecha_recibo'])),8,0,STR_PAD_LEFT);
                                $fila['cadena'] .= str_pad(substr($fila['respuesta'],0,1),10," ",STR_PAD_RIGHT);
                                $fila['cadena'] .= str_pad(substr($fila['desc_rechazo'],0,50),50," ",STR_PAD_RIGHT);
                                $fila['cadena'] .= str_pad(substr($fila['nom_empresa'],0,30),30," ",STR_PAD_RIGHT);
                                $fila['cadena'] .= str_pad($fila['cbu'],22,0,STR_PAD_LEFT);
                                $fila['cadena'] .= "\r\n";

                                array_push($datos,$fila);

                                $linea++;


                            }


                        }

                    }

//                    debug($datos);
//                    exit;

                    $diskette = array();
                    $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
//                    $fileName = str_replace(" ", "",$fileName);

                    $diskette['banco_intercambio'] = "00300";
                    $diskette['archivo'] = $fileName.".txt";
                    $diskette['info_cabecera'] = array();
                    $diskette['info_pie'] = array();
                    $diskette['registros'] = Set::extract("/cadena",$datos);
                    $this->Session->write($UID."_DISKETTE",$diskette);



                }else{

                    $this->Mensaje->error("NO ES UN ARCHIVO DEL TIPO [ms-excel] (.xls)");

                }

            } else {

                $this->Mensaje->error("INDICAR EL ARCHIVO");

            }

        }

        $this->set('UID',$UID);
        $this->set('datos',$datos);

    }


    function zip_coinag($UID = NULL,$download = 0){


        $files = array();

        if(!empty($UID) && $this->Session->check($UID."_FILES")){

            $files = $this->Session->read($UID."_FILES");

            if($download){

                if(!empty($files)){

                    $header = $footer = NULL;
                    $registros = array();
                    $fileInfo = $this->Session->read($UID."_FILE_INFO");

                    $nReg = $mTot = 0;

                    foreach($files as $file){

                        $header = array_shift($file['registros']);
                        $footer = array_pop($file['registros']);

                        foreach($file['registros'] as $registro){
                            array_push($registros,$registro);
                        }
                    }

                    App::import('Model','Config.Banco');
                    $oBANCO = new Banco();

                    $toZip = array();
                    $toZipSTR = $header."\r\n";
                    array_push($toZip,$header);
                    foreach($registros as $registro){
                        array_push($toZip,$registro);
                        $toZipSTR .= $registro."\r\n";
                        $decode = $oBANCO->decode_str_debito_coinag($registro);
                        $mTot += $decode['importe_debitado'];
                        $nReg++;
                    }

                    $footer = $oBANCO->arma_str_debito_coinag(array($nReg,$mTot),'T');

                    array_push($toZip,$footer);
                    $toZipSTR .= $footer."\r\n";

                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $entidad = (isset($INI_FILE['intercambio']['coinag_empresa_entidad']) && $INI_FILE['intercambio']['coinag_empresa_entidad'] != 0 ? $INI_FILE['intercambio']['coinag_empresa_entidad'] : '0000');

                    $zipname = $entidad."G". $fileInfo['periodo'].".ZIP";
                    
                    $file = WWW_ROOT . "files" . DS . "reportes" . DS . $zipname;

                    $zip = new ZipArchive();
                    if ($zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                        $result = $zip->addFromString($fileInfo['name'], $toZipSTR);
                        $zip->close();
                    }
                    
                    header('Content-Type: application/zip');
                    header('Content-disposition: attachment; filename='.$zipname);
                    header('Content-Length: ' . filesize($file));
                    readfile($file);

                    unlink($file);

                    exit();

                }

            }

        }else{

            $UID = String::uuid();
        }


        if(!empty($this->data)){

            $ERROR = FALSE;

            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){


                # CONTROLAR LOS NOMBRES DE ARCHIVO PARA ARMAR EL ZIP
                $info = new SplFileInfo($this->data['GeneradorDisketteBanco']['archivo_datos']['name']);
                $name = $info->getBasename();
                $ext = $info->getExtension();
                $baseName = $info->getBasename('.' . $info->getExtension());
                $periodo = substr($baseName,-4);

                $fileInfo = $this->Session->read($UID."_FILE_INFO");



                if(!empty($fileInfo)){

                    #control de periodo
                    if($fileInfo['periodo'] != $periodo){

                        //$ERROR = true;
                        //$this->Mensaje->error("LOS ARCHIVOS DEBEN PERTENECER AL MISMO PERIODO : " . $fileInfo['periodo']);

                    } else if($fileInfo['ext'] != $ext){

                        $ERROR = true;
                        $this->Mensaje->error("LOS ARCHIVOS DEBEN PERTENECER AL MISMO CONVENIO : " . $fileInfo['ext']);

                    }

                }else{

                    $this->Session->write($UID."_FILE_INFO",array('name' => $name, 'ext' => $ext, 'periodo' => $periodo));
                }


                if(!$ERROR){

                    $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                    array_push($files,array('file' => $this->data['GeneradorDisketteBanco']['archivo_datos'], 'registros' => $registros));
                    $this->Session->write($UID."_FILES",$files);

                }



                // debug($this->Session);





                // $info = new SplFileInfo($this->data['GeneradorDisketteBanco']['archivo_datos']['name']);

                // $name = $info->getBasename();
                // $baseName = $info->getBasename('.' . $info->getExtension());

                // $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                // $entidad = (isset($INI_FILE['intercambio']['coinag_empresa_entidad']) && $INI_FILE['intercambio']['coinag_empresa_entidad'] != 0 ? $INI_FILE['intercambio']['coinag_empresa_entidad'] : '0000');

                // $zipname = $entidad."G". substr($baseName,-4).".ZIP";

                // $zip = new ZipArchive();
                // if( $zip->open($zipname, (ZipArchive::CREATE | ZipArchive::OVERWRITE) ) === TRUE ){
                //     $zip->addFromString($name, file_get_contents($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']));
                //     $zip->close();
                // }

                // header('Content-Type: application/zip');
                // header('Content-disposition: attachment; filename='.$zipname);
                // header('Content-Length: ' . filesize($zipname));
                // readfile($zipname);

                // unlink(WWW_ROOT.$zipname);

                // exit();

            }
        }
        $this->set('UID',$UID);
        $this->set('files',$files);
    }



    function bna($UID = null){


        if(!empty($UID) && $this->Session->check($UID."_DISKETTE")){

            // Configure::write('debug',0);
            $diskette = $this->Session->read($UID."_DISKETTE");

            // debug($diskette);
            // exit;

            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
            header('Cache-Control: max-age=0');
            if (!empty($diskette['cabecera'])) {
                echo $diskette['cabecera']."\r\n";
            }
            foreach($diskette['registros'] as $registro):
                if(!empty($registro)) {
                    echo $registro."\r\n";
                }
            endforeach;
            if (!empty($diskette['pie'])) {
                echo $diskette['pie'];
            }
            exit;
        }


        if(!empty($this->data)){

            if ($this->Session->check($UID . "_DISKETTE")) {
                $this->Session->del($UID . "_DISKETTE");
            }

            App::import('Model','Config.Banco');
            $oBANCO = new Banco();

            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                $UID = String::uuid();

                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'text/plain'){


                    $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);

                    if(!empty($registros)){
                        
                        
                        
                        array_pop($registros);
                        array_pop($registros);
                        array_pop($registros);
                        
                        $registrosSinCabecera = array();
                        foreach($registros as $registro){
                            $ree = strstr($registro,'REE');
                            if($ree == 'REE'){
                                $cabecera = str_pad($registro,128,' ',STR_PAD_RIGHT);
                            }else{
                                array_push($registrosSinCabecera, $registro);
                            }
                        }

//                         $ree = strstr($registros[0],'REE');
//                         if($ree == 'REE'){
//                             $cabecera = str_pad(array_shift($registros),128,' ',STR_PAD_RIGHT);
//                         }else{
//                             $cabecera = str_pad('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',128,' ',STR_PAD_RIGHT);
//                         }
                        



//                         debug($rows);
//                         exit;

                        $row = $rows = array();
                        $importe = 0;

                        foreach ($registrosSinCabecera as $key => $value){


                            /**
                             * Descompone la cadena de rendicion del bco Nacion
                             * 1	TIPO REGISTRO (2) VALOR = 2
                             * 2	SUCURSAL (4)
                             * 3	SISTEMA CUENTA (2) CA=CAJA DE AHORRO O CTA CTE ESPECIAL
                             * 4	NRO DE CUENTA (11)
                             * 5	IMPORTE (15) 13 ENTEROS 2 DECIMALES
                             * 6	FECHA VENCIMIENTO AAAAMMDD  (SI VIENE CERO NO SE REALIZO DEBITO)
                             * 7	ESTADO (1) 0 = APLICADO EL DEBITO 9 = RECHAZADO (NO APLICO EL DEBITO)
                             * 8	MOTIVO DEL RECHAZO (30) SI ESTADO = 9 SE INFORMA LA DESCRIPCION DEL MOTIVO DEL RECHAZO
                             * 9	ID UNIVOCO DEL DEBITO (10)  SOCIO_ID
                             */


                            $cadena = '';
                            $cadena .= trim(substr($value,0,1));
                            $cadena .= str_pad(trim(substr($value,1,4)),4,0,STR_PAD_LEFT);
                            $cadena .= trim(substr($value,5,3));
                            // $cadena .= trim(substr($value,8,2));
                            $cadena .= trim(substr($value,10,12));
                            $cadena .= str_pad(str_replace(',','',str_replace('.','',trim(substr($value,35,21)))),15,0,STR_PAD_LEFT);
                            $cadena .= trim(substr($value,22,9));
                            $val = trim(substr($value,56,31));
                            $cadena .= (empty($val) ? 0 : 9);
                            $cadena .= str_pad(substr(trim(substr($value,56,31)),0,30),30,' ',STR_PAD_RIGHT);
                            $cadena .= trim(substr($value,87,10));
                            $cadena .= str_pad("", 46, " ", STR_PAD_RIGHT);
                            // $cadena .= "\n\r";

                            array_push($rows,$cadena);

                            #importe
                            $importe += strval(str_replace(',','.',str_replace('.','',trim(substr($value,35,21)))));

                            // debug($cadena);

                            // $decode = $oBANCO->decodeStringDebitoBcoNacion($cadena);
                            // debug($decode);
                        }

                        /**
                         * Arma el string para el registro pie del archivo de debito
                         * Datos del Array
                         * 	0	CANTIDAD DE REGISTROS DEL LOTE
                         * 	1	SUMATORIA DE IMPORTE DEL LOTE
                         */

                        $pie = $oBANCO->armaStringPieBancoNacion(array(0 => count($rows), 1 => $importe));



                        $diskette = array();
                        $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                        $fileName = str_replace(".", "-",$fileName);
    //                    $fileName = str_replace(" ", "",$fileName);

                        $diskette['banco_intercambio'] = "00011";
                        $diskette['archivo'] = $fileName.".txt";
                        $diskette['cabecera'] = $cabecera;
                        $diskette['pie'] = $pie;
                        $diskette['registros'] = $rows;
                        $this->Session->write($UID."_DISKETTE",$diskette);

                        // debug($diskette);

                        // exit;

                        $this->redirect('bna/' . $UID);

                    }

                }else{

                    $this->Mensaje->error("NO ES UN ARCHIVO DEL TIPO [text/plain] (.txt)");

                }

            } else {
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }
    }

    function unificar_cronocred($UID = null,$download = 0){

        $files = array();

        if(!empty($UID) && $this->Session->check($UID."_FILES")){

            $files = $this->Session->read($UID."_FILES");

            if($download){

                App::import('Model','Config.Banco');
                $oBANCO = new Banco();

                Configure::write('debug',0);
                header("Content-type: text/plain");
                header('Content-Disposition: attachment;filename="CRONOCRED_UNIFICADO.txt');
                header('Cache-Control: max-age=0');
                if(!empty($files)){
                    $n = $importeTotal = 0;
                    echo $files[0]['cabecera'] . "\r\n";
                    foreach($files as $file){
                        if(!empty($file['registros'])){
                            $n += count($file['registros']);
                            foreach($file['registros'] as $registro){
                                $importeTotal += number_format(intval(substr($registro,18,15)) / pow(10,2),2,".","");
                                echo $registro . "\r\n";
                            }
                        }
                    }
                    echo $oBANCO->armaStringPieBancoNacion(array($n,$importeTotal));
                }
                exit;
            }

        }else{

            $UID = String::uuid();
        }

        if(!empty($this->data)){



            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){

                $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);

                //SACAR LAS CABECERAS Y PIE
                $cabecera = array_shift($registros);
                $pie = array_pop($registros);


                // foreach($registros as $registro){

                // }


                array_push($files,array('file' => $this->data['GeneradorDisketteBanco']['archivo_datos'], 'registros' => $registros,'cabecera' => $cabecera, 'pie' => $pie));

                $this->Session->write($UID."_FILES",$files);

            }

        }

        $this->set('UID',$UID);
        $this->set('files',$files);

    }
    
    function divide_celesol($accion = 'DIVIDE',$UID = null, $download = 0) {
        
       
        if(!empty($UID) && $this->Session->check($UID."_FILESU") && $accion == 'UNIFICA' && $download == 1) {
            
            $files = $this->Session->read($UID."_FILESU");
            $convenioCba = $this->Session->read($UID."_CONVENIO");
            
            if(!empty($files)) {
                $registros = array();
                $lote = array();
                
                foreach ($files as $key => $value) {
                    foreach ($value['registros'] as $key => $value) {
                        array_push($registros, $value);
                    }
                }
                
                App::import('Model','config.EncriptadorBancoCordoba');
                $oENC = new EncriptadorBancoCordoba();   
                
                $lin = 1;
                foreach($registros as $i => $registro){
                    $regNume = substr($registro,0,51) . str_pad($i + 1,6,"0",STR_PAD_LEFT).substr($registro,57,  strlen($registro));
                    $enc = $oENC->encripta($regNume,$lin);
                    array_push($lote, $enc);
                    $lin++;
                }                
                
                if(!empty($lote))  {
                    
                    Configure::write('debug',0);
                    header("Content-type: text/plain");
                    header('Content-Disposition: attachment;filename="'."DEB".$convenioCba.".HAB".'"');
                    header('Cache-Control: max-age=0');                    
                    foreach ($lote as $value) {
                        echo $value . "\r\n";
                    }
                    exit;
                }
            }
        }
        
        if(!$this->Session->check($UID."_FILESU") && !$this->Session->check($UID."_FILES")) {
            
            $files = array();
            $UID = String::uuid();
            
            
        } else {

            $files = $this->Session->read($UID."_FILESU");
            
        }
        
        if(!empty($this->data) && $accion == 'UNIFICA') {
            
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0) {
                
                $convenioCba = $this->data['GeneradorDisketteBanco']['nro_convenio_cba']; 
                
                $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
                $CONVENIO = (!empty($convenioCba) ? $convenioCba : $DATOS_GLOBALES['nro_empresa_banco_cordoba']); 
                $CONVENIO = str_pad(trim($CONVENIO),5,'0',STR_PAD_LEFT);
                
                $this->Session->write($UID."_CONVENIO",$CONVENIO);
                
                $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                $TOTAL = 0;
                array_push($files,array('file' => $this->data['GeneradorDisketteBanco']['archivo_datos'], 'registros' => $registros, 'total' => $TOTAL));
                $this->Session->write($UID."_FILESU",$files);
            }
            
        }
        
        $this->set('UID_FILESU',$UID);
        $this->set('files',$files);         

//            debug($accion);
//            debug($UID);
//            debug($download);
//            $files = $this->Session->read($UID."_FILES");
//            debug($files);
//            exit;    
            
            
        if(!empty($UID) && $this->Session->check($UID."_FILES") && $download == 1 && $accion == 'DIVIDE') {
            

           $archivo = $this->Session->read($UID."_FILES");
            Configure::write('debug',0);
            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$archivo['FILE']);
            header('Cache-Control: max-age=0');
            if(!empty($archivo['REGISTROS'])){
                foreach($archivo['REGISTROS'] as $registro){
                    echo $registro . "\r\n";
                }
            }
            exit;
            
        }
        
        $archivos = null;
        
        if(!empty($this->data) && $accion == 'DIVIDE') {
            
            $UID_OTRO = String::uuid();
            $archivos[$UID_OTRO] = array(
                'LABEL' => 'OTROS',
                'FILE' => '',
                'REGISTROS' => array(),
                'COUNT' => 0,
                'COBRADO' => 0,
                'ENVIADO' => 0
            );
            
            $UID_CELSOL = String::uuid();
            $archivos[$UID_CELSOL] = array(
                'LABEL' => 'CELESOL',
                'FILE' => '',
                'REGISTROS' => array(),
                'COUNT' => 0,
                'COBRADO' => 0,
                'ENVIADO' => 0
            );

            $UID_SIGEM = String::uuid();
            $archivos[$UID_SIGEM] = array(
                'LABEL' => 'SIGEM',
                'FILE' => '',
                'REGISTROS' => array(),
                'COUNT' => 0,
                'COBRADO' => 0,
                'ENVIADO' => 0
            );
            
            
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0){
                
                $registros = $this->leerArchivo($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                
//                 debug($registros);

                $sql = "select ler.identificador_debito from liquidacion_socio_envios le 
                        inner join liquidacion_socio_envio_registros ler on ler.liquidacion_socio_envio_id = le.id
                        inner join liquidaciones l on l.id = le.liquidacion_id and l.imputada = 0
                        where le.banco_id = '00020'
                        group by ler.identificador_debito;";
                
                App::import('Model','Config.Banco');
                $oBANCO = new Banco();
                
                $identificadores = $oBANCO->query($sql);
                $identificadores = Set::extract("{n}.ler.identificador_debito", $identificadores);
                
                $n = $m = $o = 0;
                $cobOtro = $liquiOtro = $cobCelesol = 0;
                foreach($registros as $i => $string){
                    
                    $id = intval(substr($string,85,12));
                    $comprobante = substr($string,51,6);
                    $cuota = substr($string,79,2);
                    $importe = number_format(intval(substr($string,20,18)) / pow(10,2),2,".","");
                    $codigo = trim(substr($string,103,3));
                    //echo $cuota.$id ."\n";
                    
                    $idSearch = substr($string,20,18) . $cuota . str_pad($id,18,0,STR_PAD_LEFT);
                    
//                     echo $idSearch ."\n";

                    $decode = $oBANCO->decodeStringDebitoBcoCbaGeneral($string);
                    
                    if($id == 0) {
                        
                        $archivos[$UID_OTRO]['FILE'] = $archivos[$UID_OTRO]['LABEL']."_".$fileName;
                        
                        //file_put_contents("M22S_".$archivoRecibido, $string . "\r\n", FILE_APPEND | LOCK_EX);
                        array_push($archivos[$UID_OTRO]['REGISTROS'], $string);
                        if($codigo == 'COB'){ $archivos[$UID_OTRO]['COBRADO'] += $importe; }
                        $archivos[$UID_OTRO]['ENVIADO'] += $importe;
                        $n++;
                        $archivos[$UID_OTRO]['COUNT'] = $n;
                    
                    } else if(in_array($decode[11], $identificadores)){
                        
                        $archivos[$UID_SIGEM]['FILE'] = $archivos[$UID_SIGEM]['LABEL']."_".$fileName;
                        
                        array_push($archivos[$UID_SIGEM]['REGISTROS'], $string);
                        
                        if($codigo == 'COB'){ $archivos[$UID_SIGEM]['COBRADO'] += $importe; }
                        $archivos[$UID_SIGEM]['ENVIADO'] += $importe;
                        $n++;
                        $archivos[$UID_SIGEM]['COUNT'] = $n;
                        
                    }else{
                        
                        $archivos[$UID_CELSOL]['FILE'] = $archivos[$UID_CELSOL]['LABEL']."_".$fileName;
                        //file_put_contents("CELESOL_".$archivoRecibido, $string . "\r\n", FILE_APPEND | LOCK_EX);
                        array_push($archivos[$UID_CELSOL]['REGISTROS'],$string);
                        if($codigo == 'COB'){ $archivos[$UID_CELSOL]['COBRADO'] += $importe;}
                        $archivos[$UID_CELSOL]['ENVIADO'] += $importe;
                        $o++;
                        $archivos[$UID_CELSOL]['COUNT'] = $o;
                    }
                    
                    $m++;
                    
                }
                
                
                $this->Session->write($UID_OTRO."_FILES",$archivos[$UID_OTRO]);
                $this->Session->write($UID_CELSOL."_FILES",$archivos[$UID_CELSOL]);
                $this->Session->write($UID_SIGEM."_FILES",$archivos[$UID_SIGEM]);
            }

        }
        
        $this->set('archivos',$archivos);
    }
    
    
    function excel_firstdata($UID = NULL) {
        
        $datos = array();
        $ERROR = FALSE;
        
        
        if(!empty($UID) && $this->Session->check($UID."_DISKETTE")){
            
            // Configure::write('debug',0);
            $diskette = $this->Session->read($UID."_DISKETTE");
            
            // debug($diskette);
            // exit;
            
            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
            header('Cache-Control: max-age=0');
            if (!empty($diskette['cabecera'])) {
                echo $diskette['cabecera'];
            }
            foreach($diskette['registros'] as $registro):
            if(!empty($registro)) {
                echo $registro;
            }
            endforeach;
            if (!empty($diskette['pie'])) {
                echo $diskette['pie'];
            }
            exit;
        }
        
        
        if(!empty($this->data)) {
            
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0) {
                
                if($this->data['GeneradorDisketteBanco']['archivo_datos']['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                    
                    
                    $UID = String::uuid();
                    
                    if($this->Session->check($UID."_DISKETTE"))$this->Session->del($UID."_DISKETTE");
                    
                    App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
                    App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
                    
                    App::import('Model','Config.Banco');
                    $oBANCO = new Banco();
                    
                    $oXLS = PHPExcel_IOFactory::load($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                    
                    $registros = $oXLS->getActiveSheet()->toArray(null, true, true, true);
                    array_shift($registros);
                    
                    $IMPORTE_TOTAL = 0;
                    
                    foreach($registros as $registro) {
                        
                        $fila = array();
                        
//                         $apenom = strtoupper(iconv("UTF-8","ASCII//IGNORE",$registro['A'])) . " ". strtoupper(iconv("UTF-8","ASCII//IGNORE",$registro['B']));
                        $apenom = $registro['A'] . " ". $registro['B'];
                        $apenom = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $apenom);
                        $apenom = preg_replace('([^A-Za-z0-9 ])', '', $apenom);
                        
//                         debug($apenom);
                        
//                         $apenom = preg_replace("([^A-Za-z0-9 ])", "", $apenom);
                        $apenom = substr($apenom,0,40);
                        
                        $fila['nombre_completo'] = $apenom;
                        $fila['fecha_nacimiento'] = strtoupper(utf8_decode($registro['C']));
                        $fila['calle'] = strtoupper(iconv("utf-8","ascii//TRANSLIT",$registro['D']));
                        $fila['numero_calle'] = utf8_decode($registro['E']);
                        $fila['piso_dpto'] = utf8_decode($registro['F']);
                        $fila['codigo_postal'] = utf8_decode($registro['G']);
                        $fila['ciudad'] = utf8_decode($registro['H']);
                        $fila['provincia'] = utf8_decode($registro['I']);
                        $fila['pais'] = utf8_decode($registro['J']);
                        $fila['celular'] = utf8_decode($registro['K']);
                        $fila['email'] = utf8_decode($registro['L']);
                        $fila['dni'] = utf8_decode($registro['M']);
                        $fila['monto'] = round(floatval($registro['N']),2);
                        $fila['interval'] = intval($registro['O']);
                        $fila['interval_count'] = intval($registro['P']);
                        $fila['periodos'] = intval($registro['Q']);
                        $fila['primer_fecha_cobro'] = strtoupper(utf8_decode($registro['R']));
                        $fila['tipo_tarjeta'] = utf8_decode($registro['S']);
                        $fila['primer_vencimiento'] = strtoupper(utf8_decode($registro['T']));
                        $fila['nro_tarjeta'] = utf8_decode($registro['U']);
                        $fila['csv_tarjeta'] = utf8_decode($registro['V']);
                        
                        $primerVto = preg_replace('([^0-9])', '', $fila['primer_fecha_cobro']);
                        $fecha = substr($primerVto, 4, 4) . "-" . substr($primerVto, 2, 2).  "-" . substr($primerVto, 0, 2);
                       
                        $fila['error'] = 0;
                        $fila['error_msg'] = '';
                        $tarjetaLen = intval(strlen(trim($fila['nro_tarjeta'])));
                        
                        if(empty($fila['nro_tarjeta'])) {
                            $fila['error'] = 1;
                            $fila['error_msg'] = 'VACIO';
                        } else if($tarjetaLen != 16) {
                            $fila['error'] = 1;
                            $fila['error_msg'] = 'TARJETA > 16!';
                        }
                        
                        if(intval($fila['monto']) !== 0 && !empty($fila['nro_tarjeta'])) {
                            $campos = array(
                                0 => $fila['dni'],
                                1 => str_pad(substr($fila['nro_tarjeta'],0,16),0,16,STR_PAD_LEFT),
                                5 => $fila['monto'],
                                11 => $fecha,
                                12 => substr($fila['nombre_completo'],0, 39)
                            );
                            $fila['string'] = $oBANCO->arma_str_debito_firsdata($campos);
                            array_push($datos,$fila);
                            $IMPORTE_TOTAL += $fila['monto'];
                        }

                    }
                    
                    
//                     exit;
                    
                    $diskette = array();
                    $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
                    $fileName = str_replace(" ", "",$fileName);
                    $diskette['archivo'] = $fileName."_firstdata.txt";
                    
                    $fechaPresentacion = $oBANCO->armaFecha($this->data['GeneradorDisketteBanco']['fecha_presentacion']);
                    
                    $campos = array(
                        0 => $fechaPresentacion,
                        1 => count($registros),
                        2 => round($IMPORTE_TOTAL,2),
                        13 => $this->data['GeneradorDisketteBanco']['comercio']
                    );
                    
                    $header = $oBANCO->arma_str_debito_firsdata($campos, 'H');
                    
                    $diskette['cabecera'] = $header;
                    
                    $diskette['pie'] = array();
                    
                    $diskette['registros'] = Set::extract("/string",$datos);
                    
                    $this->Session->write($UID."_DISKETTE",$diskette);
                    
                    
//                     $this->redirect('excel_firstdata/' . $UID);
                    
                    
                } else {
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel .xls");
                }
                
            } else {
                
                $this->Mensaje->error("INDICAR EL ARCHIVO");
                
            }
            
        }
        
        $this->set('UID',$UID);
        $this->set('datos',$datos);
        
    }
    
    function excel_cofincred() {
        
        $datos = array();
        $ERROR = FALSE;        
        
        if(!empty($this->data)) {
            
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0) {
                
                
                $XLS_TYPES = array(
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                );
                
                if(in_array($this->data['GeneradorDisketteBanco']['archivo_datos']['type'], $XLS_TYPES)) {
                    
                    
                    $UID = String::uuid();
                    
                    if($this->Session->check($UID."_DISKETTE"))$this->Session->del($UID."_DISKETTE");
                    
                    App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
                    App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
                    
                    App::import('Model','Config.Banco');
                    $oBANCO = new Banco();
                    
                    $oXLS = PHPExcel_IOFactory::load($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                    
                    $registros = $oXLS->getActiveSheet()->toArray(null, true, true, true);
                    $IMPORTE_TOTAL = 0;
                    
                    
                    $ln = 1;
                    
                    foreach($registros as $registro) {

                        $fila = array();
                        
                        $fila['socio'] = utf8_decode($registro['A']);
                        $fila['nombre'] = utf8_decode($registro['C']);
                        $fila['ndoc'] = utf8_decode($registro['B']);
                        $fila['cbu'] = utf8_decode($registro['D']);
                        $fila['sucursal'] = utf8_decode($registro['F']);
                        $fila['cuenta'] = utf8_decode($registro['E']);
                        $fila['importe'] = round(floatval($registro['P']),2);
                        $fila['ln'] = $ln++;
                        
                        $campos = array(
                            1 => utf8_decode($registro['B']),
                            2 => utf8_decode($registro['F']),
                            3 => utf8_decode($registro['C']),
                            4 => utf8_decode($registro['E']),
                            5 => round(floatval($registro['P']),2),
                            6 => utf8_decode($registro['D']),
                            9 => utf8_decode($registro['A']),
                            10 => utf8_decode($registro['A']),
                        );
                        $fila['string'] = $oBANCO->armaStringDebitoBcoNacion($campos); 
                        
                        $fila['error'] = 0;
                        $fila['error_msg'] = '';
                        array_push($datos,$fila);
                        $IMPORTE_TOTAL += $fila['importe'];                        
                        
                        
                    }
                    
                    $diskette = array();
                    $fileName = $this->data['GeneradorDisketteBanco']['archivo_datos']['name'];
                    $fileName = str_replace(".", "-",$fileName);
                    $fileName = str_replace(" ", "",$fileName);
                    $diskette['archivo'] = $fileName."_cofincred.txt";
                    
                    $fechaDebito = $oBANCO->armaFecha($this->data['GeneradorDisketteBanco']['fecha_debito']);
                    $nroArchivo = $this->data['GeneradorDisketteBanco']['nro_archivo_banco_nacion'];
                    
                    
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $diskette['archivo'] = "NACION".date('Ymd',strtotime($fechaDebito)).".TXT";
                    $tmp['info_cabecera'][0] = $INI_FILE['intercambio']['cofincred_sucursal_bco_nacion'];
                    $tmp['info_cabecera'][1] = $INI_FILE['intercambio']['cofincred_tipo_cuenta_banco_nacion'];
                    $tmp['info_cabecera'][2] = $INI_FILE['intercambio']['cofincred_cuenta_banco_nacion'];
                    $tmp['info_cabecera'][3] = $INI_FILE['intercambio']['cofincred_moneda_cuenta_banco_nacion'];
                    $tmp['info_cabecera'][4] = $fechaDebito;
                    $tmp['info_cabecera'][5] = (empty($nroArchivo) ? 1 : $nroArchivo);
                    $tmp['info_cabecera'][6] = $INI_FILE['intercambio']['cofincred_indicador_lote_banco_nacion'];;
                    $tmp['info_pie'][0] = count($datos);
                    $tmp['info_pie'][1] = round($IMPORTE_TOTAL,2);  
                    
                    $diskette['cabecera'] = $oBANCO->armaStringCabeceraBancoNacion($tmp['info_cabecera']);
                    $diskette['pie'] = $oBANCO->armaStringPieBancoNacion($tmp['info_pie']);   
                    $diskette['registros'] = Set::extract("/string",$datos);
                    
                    $this->Session->write($UID."_DISKETTE",$diskette);
                    
                } else {
                    
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel");
                }
                
                
            } else {
            
                $this->Mensaje->error("INDICAR EL ARCHIVO");
                
            }
            
        }
        
        $this->set('UID',$UID);
        $this->set('datos',$datos);        
        
    }
    
    function excel_reversos_santander($param) {
        
        
        
        if(!empty($this->data)) {
            
            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
            
            if($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0) {
                
                $XLS_TYPES = array(
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                );  
                
                if(in_array($this->data['GeneradorDisketteBanco']['archivo_datos']['type'], $XLS_TYPES)){
                    
                    $UID = String::uuid();
                    
                    if($this->Session->check($UID."_DISKETTE"))$this->Session->del($UID."_DISKETTE");
                    
                    App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
                    App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
                    App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
                    
                    App::import('Model','Config.Banco');
                    $oBANCO = new Banco();
                    
                    $oXLS = PHPExcel_IOFactory::load($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                    
                    $registros = $oXLS->getActiveSheet()->toArray(null, true, true, true);
                    $IMPORTE_TOTAL = 0;
                    
                    
                    $ln = 1;
                    
                    $cadena = "20\r\n";
                    
                    $prefix = 'santander';                    
                    $concepto = (isset($DATOS_GLOBALES[$prefix.'_descripcion']) && !empty($DATOS_GLOBALES[$prefix.'_descripcion']) ? $DATOS_GLOBALES[$prefix.'_descripcion'] : "CUOTA");
                    $longitud =  (isset($DATOS_GLOBALES[$prefix.'_long_partida']) && $DATOS_GLOBALES[$prefix.'_long_partida'] > 0 ? $DATOS_GLOBALES[$prefix.'_long_partida'] : 11);
                                     
                    array_shift($registros);
                    
                    foreach($registros as $registro) {

                        $cadena .= "21";
                        $cadena .= str_pad($concepto, 10, ' ', STR_PAD_RIGHT);
                        $cadena .= str_pad(str_pad(intval($registro['D']), $longitud, '0', STR_PAD_LEFT),22,' ',STR_PAD_RIGHT);
                        $cadena .= str_pad($registro['V'], 22, ' ', STR_PAD_RIGHT);
                        $cadena .= date("Ymd",strtotime($registro['K']));
                        $cadena .= str_pad(number_format($registro['G'] * 100,0,"",""), 16, '0', STR_PAD_LEFT);
                        $cadena .= str_pad($registro['F'],15,' ',STR_PAD_RIGHT);
                        $cadena .= (trim($registro['A']) == 'Reversion' ? 'REV' : (trim($registro['A']) == 'Baja' ? 'BAJ' : 'ERR'));
                        $cadena .= str_pad("", 50, ' ', STR_PAD_RIGHT);
                        $cadena .= "\r\n";  
                        
                    }

                    Configure::write('debug',0);
                    header("Content-type: text/plain");
                    header('Content-Disposition: attachment;filename="BajasReversosRio_'.  rand() .'.txt"');
                    header('Cache-Control: max-age=0');                    
                    echo $cadena;
                    exit;
                    
                } else {
                    
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel");
                    
                }
                
            } else {
                
                $this->Mensaje->error("INDICAR EL ARCHIVO");
                
            }
            
        }
        
    }
    
    function divide_liquidacion() {
        $files = [];
        if(!empty($this->data)) {
            App::import('Model','Mutual.LiquidacionIntercambio');
            $oLQI = new LiquidacionIntercambio();    
            $files = $oLQI->subdividirLotePorLiquidacion(
                    $this->data['LiquidacionIntercambio']['banco_id'], 
                    $this->data['LiquidacionIntercambio']['archivo']['name'], 
                    $this->data['LiquidacionIntercambio']['archivo']['tmp_name'], 
                    $this->Session
            );  

            $this->set('files',$files);
        }
    }
    
    function excel_cjpc_main($UID = null) {
        Configure::write('debug', 1);
        App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
        App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
        App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));

        $datos = array();

        if (!empty($UID) && $this->Session->check($UID."_DISKETTE")) {
            $diskette = $this->Session->read($UID."_DISKETTE");

            if (ob_get_level()) ob_end_clean();
            header("Content-type: text/plain");
            header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
            header('Cache-Control: max-age=0');

            if (!empty($diskette['cabecera'])) echo $diskette['cabecera'];
            foreach($diskette['registros'] as $registro) {
                if (!empty($registro)) {
                    echo $registro;
                    flush();
                }
            }
            if (!empty($diskette['pie'])) echo $diskette['pie'];
            exit;
        }

        if (!empty($this->data)) {
            
            if ($this->data['GeneradorDisketteBanco']['archivo_datos']['error'] == 0) {
                $XLS_TYPES = array(
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-excel'
                );

                if (in_array($this->data['GeneradorDisketteBanco']['archivo_datos']['type'], $XLS_TYPES)) {
                    $UID = String::uuid();

                    $reader = PHPExcel_IOFactory::createReaderForFile($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                    $reader->setReadDataOnly(true);
                    $oXLS = $reader->load($this->data['GeneradorDisketteBanco']['archivo_datos']['tmp_name']);
                    $worksheet = $oXLS->getActiveSheet();

                    $registros = array();
                    $ln = 1;

                    foreach ($worksheet->getRowIterator(2) as $row) {
                        $cells = $row->getCellIterator();
                        $cells->setIterateOnlyExistingCells(false);

                        $registro = array();
                        foreach ($cells as $cell) {
                            $registro[] = utf8_decode(trim($cell->getValue()));
                        }

                        $fila = array();
                        $fila['tipo'] = intval($registro[0]);
                        $fila['nombre'] = substr(trim($registro[4]),0,24);
                        $fila['cuit_cuil'] = str_pad(intval($registro[5]),11,0, STR_PAD_LEFT);
                        $fila['dni'] = str_pad(substr($fila['cuit_cuil'],2,8),8,0, STR_PAD_LEFT);
                        $fila['operacion'] = intval($registro[2]);
                        $fila['importe'] = round(floatval($registro[6]),2);
                        $fila['valor'] = round(floatval($registro[7]),2);
                        $fila['estado'] = $registro[8];

                        $datoBeneficio = explode('-', $registro[3]);
                        $fila['beneficio'] = str_pad(trim($datoBeneficio[0]),11,0, STR_PAD_LEFT);

                        $cadena = '';
                        $cadena .= $fila['beneficio'];
                        $cadena .= str_pad($fila['nombre'],24,' ', STR_PAD_RIGHT);
                        $cadena .= $fila['tipo'];
                        $cadena .= str_pad($fila['importe'] * 100, 10, '0', STR_PAD_LEFT);
                        $cadena .= str_pad($fila['dni'],9,0, STR_PAD_LEFT);
                        $cadena .= str_pad(0,12,0, STR_PAD_LEFT);
                        $cadena .= str_pad(0 * 100, 11, '0', STR_PAD_LEFT);
                        $cadena .= "\r\n";

                        $datos['unificado'][] = $cadena;
                        
                        $prefix = strtoupper(substr($cadena, 0, 1));
                        $sufix = substr($cadena, 1, strlen($cadena));
                        $datos['por_codigo'][$fila['tipo']][] = ( $prefix == 'J' ? 1 : 0 ) . $sufix;
                    }
                    
                    $fileName = str_replace(".", "-", $this->data['GeneradorDisketteBanco']['archivo_datos']['name']);
                    $UUID = String::uuid();
                    $diskette = array(
                        'archivo' => "CJPC_".$fileName.".txt",
                        'cabecera' => '',
                        'registros' => $datos['unificado'],
                        'pie' => '',
                        'uuid' => $UUID,
                    );
                    $this->Session->write($UUID."_DISKETTE",$diskette);
                    
                    $diskettes['UNIFICADO'] = $diskette;
                    
                    foreach ($datos['por_codigo'] as $codigo => $value) {
                        $UUID = String::uuid();
                        $diskette = array(
                            'archivo' => "CJPC_".$fileName. "_" . $codigo .".txt",
                            'cabecera' => '',
                            'registros' => $value,
                            'pie' => '',
                            'uuid' => $UUID,
                        );
                        $diskettes[$codigo] = $diskette;
                        $this->Session->write($UUID."_DISKETTE",$diskette);
                        
                    }

                } else {
                    $this->Mensaje->error("NO ES UN ARCHIVO Microsoft Excel");
                }
            } else {
                $this->Mensaje->error("INDICAR EL ARCHIVO");
            }
        }

        $this->set('UID', $UID);
        $this->set('datos', $datos);
        $this->set('diskettes', $diskettes);
    }
    

}
?>

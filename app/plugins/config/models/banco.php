<?php
/**
 *
 */
class Banco extends ConfigAppModel{

	var $name = 'Banco';
    var $EOL = "\r\n";
    
    var $spGenera = array(
        'SP_DISKETTE_BANCO_COMAFI' => 'BANCO COMAFI',
        'SP_DISKETTE_BANCO_COMERCIO' => 'BANCO COMERCIAL',
        'SP_DISKETTE_BANCO_CORDOBA' => 'BANCO CORDOBA',
        'SP_DISKETTE_BANCO_CREDICOOP' => 'BANCO CREDICOOP',
        'SP_DISKETTE_BANCO_FRANCES' => 'BANCO FRANCES',
        'SP_DISKETTE_BANCO_GALICIA' => 'BANCO GALICIA',
        'SP_DISKETTE_BANCO_ITAU' => 'BANCO ITAU',
        'SP_DISKETTE_BANCO_NACION' => 'BANCO NACION',
        'SP_DISKETTE_BANCO_MACRO' => 'BANCO MACRO',
        'SP_DISKETTE_BANCO_SANTANDER' => 'SANTANDER RIO',
        'SP_DISKETTE_BANCO_STANDARBANK' => 'STANDARBANK / ICBC',
    );


	function save($data = null, $validate = true, $fieldList = array()){
		$ret = parent::save($data,$validate,$fieldList);
		return $ret;
	}


	/**
	 * Genera el string con los datos para armar el diskette para el Banco Cordoba
	 * 	1	TIPO_CONVENIO (3)
	 * 	2	SUCURSAL (5)
	 * 	3	MONEDA (01 = PESOS)
	 * 	4	SISTEMA (1) (1 - CTA.CTE. / 3 - CAJA DE AHORRO)
	 * 	5	NUMERO DE CUENTA (9)
	 * 	6	IMPORTE (18) (16 + 2 DECIMALES)
	 * 	7	FECHA A DEBITAR (8) (AAAAMMDD)
	 * 	8	NUMERO DE EMPRESA (5)
	 * 	9	NUMERO DE COMPROBANTE (6)
	 * 	10	NRO DE CBU (22)
	 * 	11	NUMERO CUOTA (2) (SI VAN DOS O MAS REGISTROS POR CBU DIFERENCIAR POR NUMERO DE CUOTA)
	 * 	12	IDENTIFICADOR UNIVOCO DEL DEBITO (22)
	 * @param array $campos
	 * @return string
	 */
	function armaStringDebitoBcoCba($campos){

        // $campos = array(
        //     2 => $sucursal,
        //     5 => $cuenta,
        //     6 => $importeDebito,
        //     7 => $fechaDebito,
        //     9 => 0,
        //     10 => $cbu,
        //     11 => $registroNro,
        //     12 => $idDebito,
        //     8 => $convenioBcoCba,
        // );

            $cadena = "";
            $cadena .= "003";
            $cadena .= str_pad(trim($campos[2]), 5, '0', STR_PAD_LEFT);
            $cadena .= "01";
            $cadena .= "3";
            $nroCuentaBanco = trim($campos[5]);
//		if(strlen($nroCuentaBanco) > 9) $nroCuentaBanco = substr($nroCuentaBanco,-9,9);
            if(strlen($nroCuentaBanco) > 9) $nroCuentaBanco = substr($nroCuentaBanco,-9);
            $cadena .= str_pad($nroCuentaBanco, 9, '0', STR_PAD_LEFT);
            $importe = number_format($campos[6] * 100,0,"","");
            $cadena .= str_pad($importe, 18, '0', STR_PAD_LEFT);
            $cadena .= date('Ymd',strtotime($campos[7]));

            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
            $nroEmpresa = str_pad(trim($DATOS_GLOBALES['nro_empresa_banco_cordoba']),5,"0",STR_PAD_LEFT);
            $cadena .= (isset($campos[8]) && !empty($campos[8])? str_pad(substr(trim($campos[8]),-5),5,'0',STR_PAD_LEFT) : $nroEmpresa);
            $cadena .= str_pad(trim($campos[9]), 6, '0', STR_PAD_LEFT);
            $cadena .= str_pad(trim($campos[10]), 22, '0', STR_PAD_LEFT);
            $cadena .= str_pad($campos[11], 2, '0', STR_PAD_LEFT);
            $cadena .= str_pad(trim(substr($campos[12],0,22)), 22, '0', STR_PAD_LEFT);
            $cadena .= "\r\n";
            return $cadena;
	}

	/**
	 * Genera un array con los datos para armar el diskette para el Banco Cordoba
	 * 	1	TIPO_CONVENIO (3)
	 * 	2	SUCURSAL (5)
	 * 	3	MONEDA (01 = PESOS)
	 * 	4	SISTEMA (1) (1 - CTA.CTE. / 3 - CAJA DE AHORRO)
	 * 	5	NUMERO DE CUENTA (9)
	 * 	6	IMPORTE (18) (16 + 2 DECIMALES)
	 * 	7	FECHA A DEBITAR (8) (AAAAMMDD)
	 * 	8	NUMERO DE EMPRESA (5)
	 * 	9	NUMERO DE COMPROBANTE (6)
	 * 	10	NRO DE CBU (22)
	 * 	11	NUMERO CUOTA (2) (SI VAN DOS O MAS REGISTROS POR CBU DIFERENCIAR POR NUMERO DE CUOTA)
	 * 	12	IDENTIFICADOR UNIVOCO DEL DEBITO (22)
	 * @param string $cadena
	 * @return array
	 */

	function getDatosStringDebitoBcoCba($string){
		$campos = array();
		$campos[0] = substr($string,0,3);
		$campos[1] = substr($string,3,5);
		$campos[2] = substr($string,8,2);
		$campos[3] = substr($string,10,1);
		$campos[4] = substr($string,11,9);
		$campos[5] = substr($string,20,18);
		$campos[6] = substr($string,38,8);
		$campos[7] = substr($string,46,5);
		$campos[8] = substr($string,51,6);
		$campos[9] = substr($string,57,22);
		$campos[10] = substr($string,79,2);
		$campos[11] = substr($string,81,22);
		return $campos;
	}

	/**
	 *  Descompone la cadena de rendicion del bco Cordoba
	 *  1	TIPO CONVENIO (3)
	 *  2	SUCURSAL (5)
	 *  3	MONEDA (2)
	 *  4	SISTEMA (1) 1 = CTACTE 3=CAJA DE AHORRO
	 *  5	NUMERO DE CUENTA (9)
	 *  6	IMPORTE (18) 16 ENTEROS 2 DECIMALES
	 *  7	FECHA DEBITO (8) AAAAMMDD
	 *  8	NRO EMPRESA (5)
	 *  9	NRO COMPROBANTE (6)
	 *  10	CBU (22)
	 *  11	NRO CUOTA (2)
	 *  12	IDENTIFICADOR (22) SOCIO_ID (12) | LIQUIDACION_ID (8) | REGISTRO (2)
	 *  13	CODIGO (3)
	 * @param $string
	 * @return array
	 */
	function decodeStringDebitoBcoCba($string){

		$decode = $this->decodeStringDebitoBcoCbaGeneral($string);

		$campos['sucursal'] = $decode[1];
		$campos['nro_cta_bco'] = $decode[4];
		$campos['importe_debitado'] = $decode[5];
		$campos['fecha_debito'] = $decode[6];
		$campos['socio_id'] = intval(substr($decode[11],0,12));
		$campos['liquidacion_id'] = 0;
		if(intval(substr($decode[11],12,1)) == 0) {
		    $campos['liquidacion_id'] = intval(substr($decode[11],12,8));
		}
		
		$campos['status'] = trim($decode[12]);
		$campos['indica_pago'] = $decode[14];

//		$campos = array();
//		$campos[0] = substr($string,0,3);
//		$campos['sucursal'] = substr($string,3,5);
//		$campos[2] = substr($string,8,2);
//		$campos[3] = substr($string,10,1);
//		$campos['nro_cta_bco'] = substr($string,11,9);
//		$campos['importe_debitado'] = intval(substr($string,20,18)) / pow(10,2);
//		$campos['fecha_debito'] = substr(substr($string,38,8),0,4).'-'.substr(substr($string,38,8),4,2).'-'.substr(substr($string,38,8),6,2);
//		$campos[7] = substr($string,46,5);
//		$campos[8] = substr($string,51,6);
//		$campos[9] = substr($string,57,22);
//		$campos[10] = substr($string,79,2);

//		$campos['socio_id'] = intval(substr($string,81,12));

//		$campos[11] = substr($string,81,1);
//		$campos['documento'] = substr($string,82,8);
//		$campos[13] = substr($string,90,4);
//		$campos[14] = substr($string,94,9);

//		$campos['status'] = trim(substr($string,103,3));
//		if(empty($campos['status']))$campos['status'] = "ERR";
//
//		if($campos['status'] == 'COB') $campos['indica_pago'] = 1;
//		else $campos['indica_pago'] = 0;

		return $campos;
	}


	/**
	 *  Descompone la cadena de rendicion del bco Cordoba
	 *  0	TIPO CONVENIO (3)
	 *  1	SUCURSAL (5)
	 *  2	MONEDA (2)
	 *  3	SISTEMA (1) 1 = CTACTE 3=CAJA DE AHORRO
	 *  4	NUMERO DE CUENTA (9)
	 *  5	IMPORTE (18) 16 ENTEROS 2 DECIMALES
	 *  6	FECHA DEBITO (8) AAAAMMDD
	 *  7	NRO EMPRESA (5)
	 *  8	NRO COMPROBANTE (6)
	 *  9	CBU (22)
	 *  10	NRO CUOTA (2)
	 *  11	IDENTIFICADOR (22) SOCIO_ID (12) | LIQUIDACION_ID (2) | REGISTRO (2)
	 *  12	CODIGO (3)
	 *  13	DESCRIPCION CODIGO DEBITO
	 *  14	INDICA SI ES UN COBRO
	 * @param $string
	 * @return array
	 */
	function decodeStringDebitoBcoCbaGeneral($string){
		$campos = array();
		$campos[0] = substr($string,0,3);
		$campos[1] = substr($string,3,5);
		$campos[2] = substr($string,8,2);
		$campos[3] = substr($string,10,1);
		$campos[4] = substr($string,11,9);
		$campos[5] = number_format(intval(substr($string,20,18)) / pow(10,2),2,".","");
		$campos[6] = parent::strToDate(substr($string,38,8));
		$campos[7] = substr($string,46,5);
		$campos[8] = substr($string,51,6);
		$campos[9] = substr($string,57,22);
		$campos[10] = substr($string,79,2);
		$campos[11] = substr($string,81,22);
		$campos[12] = trim(substr($string,103,3));

		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();

		$campos[13] = $oCODIGO->getDescripcionCodigo("00020",(!empty($campos[12]) ? $campos[12] : "ERR"));
		$campos[14] = ($oCODIGO->isCodigoPago("00020",(!empty($campos[12]) ? $campos[12] : "ERR")) ? 1 : 0);

		return $campos;
	}


	/**
	 * genera la cadena para diskette del banco standar bank
	 * 	1	TIPO REGISTRO (1) VALOR = 2
	 * 	2	CODIGO TRANSACCION (4) 3700 ORDEN DE DEBITO
	 * 	3	CBU BLOQUE 1 (8)
	 * 	4	CBU BLOQUE 2 (14)
	 * 	5	IDENTIFICADOR UNIVOCO DEL CLIENTE (22)
	 * 	6	MONTO DEL DEBITO (10) 8 + 2 DECIMALES
	 * 	7	IDENTIFICADOR UNIVOCA DEL DEBITO (15)
	 * 	8	FECHA VENCIMIENTO ORIGINAL (8) AAAAMMDD
	 * 	9	FILLER (18) BLANK
	 * @param $campos
	 * @return string
	 */
	function armaStringDebitoStandarBank($campos){

        // $campos = array(
        //     3 => substr($cbu,0,8),
        //     4 => substr($cbu,8,14),
        //     5 => $idDebito,
        //     6 => $importeDebito,
        //     7 => $liquidacionSocioId,
        //     8 => $fechaDebito
        // );
        
		$cadena = "";
		$cadena .= $this->getIndicadorDetalleRegistro("00430");
		$cadena .= "3700";
		$cadena .= $campos[3];
		$cadena .= $campos[4];
		$cadena .= str_pad(substr(trim($campos[5]),0,22), 22, '0', STR_PAD_LEFT);
		$importe = number_format($campos[6] * 100,0,"","");
		$cadena .= str_pad($importe, 10, '0', STR_PAD_LEFT);
		$cadena .= str_pad($campos[7], 15, '0', STR_PAD_LEFT);
		$cadena .= date('Ymd',strtotime($campos[8]));
		$cadena .= str_pad("", 18, " ", STR_PAD_LEFT);
		$cadena .= "\r\n";
		return $cadena;
	}

	/**
	 * Arma string registro cabecera del archivo de debito
	 * Datos del Array
	 * 	0	TIPO REGISTRO 1 (1)
	 * 	1	CUIT (11)
	 * 	2	MONEDA (0 = PESOS, 1 = DOLARES) (1)
	 * 	3	PRESTACION (10)
	 * 	4	FECHA DE VENCIMIENTO (8) AAAAMMDD
	 * 	5 	FILLER (69)
	 * @param $campos
	 * @return unknown_type
	 */
	function armaStringCabeceraStandarBank($campos){

        // $tmp['info_cabecera'][0] = Configure::read('APLICACION.cuit_mutual');
        // $tmp['info_cabecera'][1] = 0;
        // $tmp['info_cabecera'][2] = "CUOTA PTMO";
        // $tmp['info_cabecera'][3] = date('Ymd',strtotime($fechaDebito));        

		$cadena = $this->getIndicadorCabeceraRegistro("00430");
		$cadena .= $campos[0];
		$cadena .= $campos[1];
		$cadena .= str_pad($campos[2],10, " ", STR_PAD_RIGHT);
		$cadena .= date('Ymd',strtotime($campos[3]));
		$cadena .= str_pad("", 69, " ", STR_PAD_LEFT);
		$cadena .= "\r\n";
		return $cadena;
	}

	/**
	 * Arma string registro pie archivo de debito
	 * Datos del Array
	 * 	0	CANTIDAD DE REGISTROS DE LOTE
	 * 	1	SUMATORIA DE IMPORTE DEL LOTE
	 * @param $campos
	 * @return unknown_type
	 */
	function armaStringPieStandarBank($campos){

        // $tmp['info_pie'][0] = $registros;
        // $tmp['info_pie'][1] = round($importeTotal,2);        

		$cadena = $this->getIndicadorPieRegistro("00430");
		$registros = str_pad($campos[0], 8, '0', STR_PAD_LEFT);
		$sumatoria = (float) $campos[1];
		$sumatoria = $sumatoria * 100;
		$sumatoria = number_format($sumatoria,0,"","");
		$cadena .= $registros;
		$cadena .= str_pad($sumatoria, 14, '0', STR_PAD_LEFT);
		$cadena .= str_pad("", 77, " ", STR_PAD_LEFT);
		$cadena .= "\r\n";
		return $cadena;
	}

	/**
	 * Descompone la cadena de rendicion del bco Standar
	 * 1	TIPO REGISTRO (1) = 2
	 * 2	PRESTACION (10)
	 * 3	FECHA VTO ORIGINAL (8)
	 * 4	FECHA COMPENSACION (8)
	 * 5	FECHA RENDICION (8)
	 * 6	CODIGO TRANSACCION (4)
	 * 7	BLOQUE_1 CBU (8)
	 * 8	BLOQUE_2 CBU (14)
	 * 9	MONTO DEBITADO (10) 8 ENTEROS 2 DECIMALES
	 * 10	REFERENCIA UNIVOCA DEBITO (15) LIQUIDACION_SOCIO_ID
	 * 11	ID UNIVOCO DEL DEBITO (22) 	SOCIO_ID (12) | LIQUIDACION_ID (2) | REGISTRO (2)
	 * 12	MOTIVO DEL RECHAZO (3)
	 * 13	NOTIFICACION DE CAMBIOS (22) (CBU O ID DEL ADHERENTE)
	 * 14	FECHA DE RECHAZO (8) AAAAMMDD
	 * 15	FECHA DE REVERSA (8) AAAAMMDD
	 * 16	INFORMACION ADICIONAL (44)
	 * 17	FILLER (7)
	 * @param $string
	 * @return array
	 */
	function decodeStringDebitoStandarBank($string){
		$campos = array();

		$decode = $this->decodeStringDebitoStandarBankGeneral($string);

		$campos['fecha_debito'] = $decode[4];
		$campos['cbu'] = $decode[6].$decode[7];
		$cbuDecode = $this->deco_cbu($campos['cbu']);
		$campos['banco_id'] = $cbuDecode['banco_id'];
		$campos['sucursal'] = (isset($cbuDecode['sucursal']) ? $cbuDecode['sucursal'] : "");
		$campos['tipo_cta_bco'] = (isset($cbuDecode['tipo_cta_bco']) ? $cbuDecode['tipo_cta_bco'] : "");
		$campos['nro_cta_bco'] = (isset($cbuDecode['nro_cta_bco']) ? $cbuDecode['nro_cta_bco'] : "");
		$campos['importe_debitado'] = $decode[8];
		$campos['socio_id'] = intval(substr($decode[10],0,12));
		$campos['status'] = $decode[11];
		$campos['indica_pago'] = $decode[18];
		$campos['liquidacion_socio_id'] = intval($decode[9]);

//		$campos[0] = substr($string,0,1);
//		$campos[1] = substr($string,1,10);
//		$campos[2] = substr($string,11,8);
//		$campos[3] = substr($string,19,8);
//		$campos['fecha_debito'] = substr(substr($string,27,8),0,4).'-'.substr(substr($string,27,8),4,2).'-'.substr(substr($string,27,8),6,2);
//		$campos[5] = substr($string,35,4);
//		$campos[6] = substr($string,39,8);
//		$campos[7] = substr($string,47,14);
//		$campos['cbu'] = substr($string,39,8).substr($string,47,14);

//		$cbuDecode = $this->deco_cbu($campos['cbu']);
//		$campos['banco_id'] = $cbuDecode['banco_id'];
//		$campos['sucursal'] = $cbuDecode['sucursal'];
//		$campos['tipo_cta_bco'] = $cbuDecode['tipo_cta_bco'];
//		$campos['nro_cta_bco'] = $cbuDecode['nro_cta_bco'];

//		$campos['importe_debitado'] = intval(substr($string,61,10)) / pow(10,2);

//		$campos[9] = substr($string,71,15);
//		$campos['socio_id'] = intval(substr($string,86,12));

//		$campos['documento'] = substr($string,78,8);
//		$campos[11] = substr($string,86,22);

//		$campos['status'] = trim(substr($string,108,3));
//		if(empty($campos['status']))$campos['status'] = "ERR";
//		if($campos['status'] == "OK") $campos['indica_pago'] = 1;
//		else $campos['indica_pago'] = 0;

//		$campos[13] = substr($string,111,22);
//		$campos[14] = substr($string,133,8);
//		$campos[15] = substr($string,141,8);
//		$campos[16] = substr($string,149,44);
//		$campos[17] = substr($string,193,7);

		return $campos;
	}


	/**
	 * Descompone la cadena de rendicion del bco Standar
	 * 0	TIPO REGISTRO (1) = 2
	 * 1	PRESTACION (10)
	 * 2	FECHA VTO ORIGINAL (8)
	 * 3	FECHA COMPENSACION (8)
	 * 4	FECHA RENDICION (8)
	 * 5	CODIGO TRANSACCION (4)
	 * 6	BLOQUE_1 CBU (8)
	 * 7	BLOQUE_2 CBU (14)
	 * 8	MONTO DEBITADO (10) 8 ENTEROS 2 DECIMALES
	 * 9	REFERENCIA UNIVOCA DEBITO (15) LIQUIDACION_SOCIO_ID
	 * 10	ID UNIVOCO DEL DEBITO (22) 	SOCIO_ID (12) | LIQUIDACION_ID (2) | REGISTRO (2)
	 * 11	MOTIVO DEL RECHAZO (3)
	 * 12	NOTIFICACION DE CAMBIOS (22) (CBU O ID DEL ADHERENTE)
	 * 13	FECHA DE RECHAZO (8) AAAAMMDD
	 * 14	FECHA DE REVERSA (8) AAAAMMDD
	 * 15	INFORMACION ADICIONAL (44)
	 * 16	FILLER (7)
	 * @param $string
	 * @return array
	 */

	function decodeStringDebitoStandarBankGeneral($string){
		$campos = array();
		$campos[0] = substr($string,0,1);
		$campos[1] = substr($string,1,10);
		$campos[2] = parent::strToDate(substr($string,11,8));
		$campos[3] = parent::strToDate(substr($string,19,8));
		$campos[4] = parent::strToDate(substr($string,27,8));
		$campos[5] = substr($string,35,4);
		$campos[6] = substr($string,39,8);
		$campos[7] = substr($string,47,14);
		$campos[8] = number_format(intval(substr($string,61,10)) / pow(10,2),2,".","");
		$campos[9] = substr($string,71,15);
		$campos[10] = substr($string,86,22);
		$campos[11] = trim(substr($string,108,3));
		$campos[12] = substr($string,111,22);
		$campos[13] = parent::strToDate(substr($string,133,8));
		$campos[14] = parent::strToDate(substr($string,141,8));
		$campos[15] = substr($string,149,44);
		$campos[16] = substr($string,193,7);

		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();

		$campos[17] = $oCODIGO->getDescripcionCodigo("00430",(!empty($campos[11]) ? $campos[11] : "ERR"));
		$campos[18] = ($oCODIGO->isCodigoPago("00430",(!empty($campos[11]) ? $campos[11] : "ERR")) ? 1 : 0);

		return $campos;
	}


	/**
	 * Arma la cadena para el diskette del bco. Nacion
	 * 	1	TIPO_REGISTRO (1) VALOR = 2
	 * 	2	SUCURSAL (4)
	 * 	3	SISTEMA (2) CA=CAJA DE AHORRO O CTACTE ESPECIAL | CC=CUENTA CORRIENTE
	 * 	4	NRO_CUENTA (11) 1RA POSICION = 0
	 * 	5	IMPORTE(15) 13 ENTEROS 2 DECIMALES
	 * 	6	FECHA_VTO (8) COMPLETAR CON CEROS PARA ENVIO AAAAMMDD
	 * 	7	ESTADO (1) VALOR=0 PARA ENVIO
	 * 	8	MOTIVO_RECHAZO (30) VALOR=BLANK PARA ENVIO
	 * 	9	CONCEPTO (10) CONCEPTO DEBITO (envio el ID de la liquidacion_socios)
	 * 	10	FILLER (46) RELLENO (envio el identificador del debito)
	 * @param $campos
	 * @return string
	 */
	function armaStringDebitoBcoNacion($campos,$fecha = null){

				// $campos = array(
				// 				2 => $sucursal,
				// 				4 => $cuenta,
				// 				5 => $importeDebito,
				// 				9 => $socioId,
				// 				10 => $idDebito,
				// );   
				
	    $importeDebito = $campos[5];
		$cadena = "";
		$cadena .= trim($this->getIndicadorDetalleRegistro("00011"));
		$cadena .= str_pad(substr(trim($campos[2]),-4), 4, '0', STR_PAD_LEFT);
		$cadena .= "CA";
		$cadena .= str_pad(trim(substr($campos[4],0,11)), 11, '0', STR_PAD_LEFT);
		$importe = number_format($importeDebito * 100,0,"","");
        $cadena .= str_pad($importe, 15, '0', STR_PAD_LEFT);

        $fecha = (empty($fecha) ? '00000000': date('Ymd',strtotime($fecha)));

		$cadena .= $fecha;
		$cadena .= "0";
		$cadena .= str_pad("", 30, " ", STR_PAD_LEFT);
//		$cadena .= str_pad(substr(trim($campos[9]),0,10), 10, '0', STR_PAD_LEFT);
        $cadena .= str_pad(substr(trim($campos[10]),0,10), 10, '0', STR_PAD_LEFT);
		$cadena .= str_pad("", 46, " ", STR_PAD_LEFT);
		$cadena .= "\r\n";
		return $cadena;
	}


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
	 * @param $string
	 * @return array
	 */
	function decodeStringDebitoBcoNacion($string, $banco = '00011'){
		$campos = array();

		$decode = $this->decodeStringDebitoBcoNacionGeneral($string, $banco);
		$campos['sucursal'] = $decode[1];
		$campos['nro_cta_bco'] = $decode[3];
		$campos['importe_debitado'] = $decode[4];
		$campos['fecha_debito'] = $decode[5];
		$campos['indica_pago'] = $decode[12];
		$campos['status'] = $decode[10];
		$campos['socio_id'] = intval(substr($decode[8],-6));
		$campos['id_debito'] = $decode[8];
        $campos['liquidacion_id'] = intval(substr($decode[8],0,4));

        $campos['liquidacion_id'] = (empty($campos['liquidacion_id']) ? 999999 : $campos['liquidacion_id']);

//		$campos[0] = substr($string,0,1);
//		$campos['sucursal'] = substr($string,1,4);
//		$campos[2] = substr($string,5,2);
//		$campos['nro_cta_bco'] = substr($string,7,11);
//		$campos['importe_debitado'] = intval(substr($string,18,15)) / pow(10,2);
//		$campos['fecha_debito'] = (intval(substr($string,33,8)) != 0 ? substr(substr($string,33,8),0,4).'-'.substr(substr($string,33,8),4,2).'-'.substr(substr($string,33,8),6,2) : date('Y-m-d'));
//		$campos['indica_pago'] = (substr($string,41,1) == 0 ? 1 : 0);
//		$campos[7] = substr($string,42,30);
//
//		//armo el codigo de status
//		switch (trim($campos[7])) {
//			case "":
//				$campos['status'] = "000";
//				break;
//			case "CASA INEXISTENTE":
//				$campos['status'] = "001";
//				break;
//			case "CASA O CTA.INEX.O MONED.INCO.":
//				$campos['status'] = "002";
//				break;
//			case "MONTO SUP. AL DISPONIBLE":
//				$campos['status'] = "003";
//				break;
//			case "CUENTA BLOQUEADA":
//				$campos['status'] = "004";
//				break;
//			default:
//				$campos['status'] = '999';
//				break;
//		}
//
//		if(!is_string(trim($campos[7])))$campos['status'] = "ERR";
//
//		$campos['socio_id'] = intval(substr($string,72,10));
//		$campos[7] = substr($string,82,46);

		return $campos;
	}

	/**
	 * Descompone la cadena de rendicion del bco Nacion (FUNCION GENERAL)
	 * 0	TIPO REGISTRO (2) VALOR = 2
	 * 1	SUCURSAL (4)
	 * 2	SISTEMA CUENTA (2) CA=CAJA DE AHORRO O CTA CTE ESPECIAL
	 * 3	NRO DE CUENTA (11)
	 * 4	IMPORTE (15) 13 ENTEROS 2 DECIMALES
	 * 5	FECHA VENCIMIENTO AAAAMMDD  (SI VIENE CERO NO SE REALIZO DEBITO)
	 * 6	ESTADO (1) 0 = APLICADO EL DEBITO 9 = RECHAZADO (NO APLICO EL DEBITO)
	 * 7	MOTIVO DEL RECHAZO (30) SI ESTADO = 9 SE INFORMA LA DESCRIPCION DEL MOTIVO DEL RECHAZO
	 * 8	ID UNIVOCO DEL DEBITO (10)  SOCIO_ID
	 * 9	FILLER
	 * 10	CODIGO STATUS DEBITO
	 * 11 	DESCRIPCION CODIGO STATUS DEBITO
	 * 12	MARCA QUE INDICA SI ES PAGO O NO
	 * @param $string
	 * @return array
	 */

	function decodeStringDebitoBcoNacionGeneral($string, $banco = '00011'){
		$campos = array();
		$campos[0] = substr($string,0,1);
		$campos[1] = substr($string,1,4);
		$campos[2] = substr($string,5,2);
		$campos[3] = substr($string,7,11);
		$campos[4] = number_format(intval(substr($string,18,15)) / pow(10,2),2,".","");
		$campos[5] = parent::strToDate(substr($string,33,8));
		$campos[6] = substr($string,41,1);
		$campos[7] = substr($string,42,30);
		$campos[8] = substr($string,72,10);
		$campos[9] = substr($string,82,46);

		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();
		$campos[7] = trim($campos[7]);
		if(!empty($campos[7])){
		    $campos[10] = $oCODIGO->getCodigoByConcepto($banco, trim($campos[7]));
			$campos[10] = (!empty($campos[10]) ?  trim($campos[10]) : "999");
		}else{
			$campos[10] = "000";
		}

		if(!is_string(trim($campos[7])))$campos[10] = "ERR";


		$campos[11] = $oCODIGO->getDescripcionCodigo("00011",(!empty($campos[10]) ? $campos[10] : "ERR"));
		$campos[12] = ($oCODIGO->isCodigoPago("00011",(!empty($campos[10]) ? $campos[10] : "ERR")) ? 1 : 0);

		return $campos;
	}

	/**
	 * Arma el string para el registro cabecera del archivo para debito
	 * Formato Array de campos
	 *  0	ID CABECERA (1)
	 * 	1	SUCURSAL (4)
	 * 	2	TIPO Y MONEDA DE LA CUENTA (10=CTA.CTE $, 11=CTA.CTE.U$S, 20=CA $, 21=CA U$S, 27=CTA.CTE.ESP $, 28=CTA.CTE.ESP.U$S (2)
	 * 	3	NRO DE CUENTA (10)
	 * 	4	MONEDA (P=PESOS, D=DOLARES) (1)
	 *  5	FIX (E) (1)
	 *  6	SECUENCIA ARCHIVO (4) MES + NRO
	 * 	7	FECHA TOPE RENDICION (8) AAAAMMDD
	 * 	8	INDICADOR DE LOTE EMPLEADOS BNA (BNA=EMPL.BCO NACION, EMP=COBRAN SUELDOS EN BNA, REE=CLIENTES COMUNES)
	 *  9	FILLER (94) BLANCOS
	 * @param unknown_type $campos
	 * @return string
	 */
	function armaStringCabeceraBancoNacion($campos){
        // $tmp['info_cabecera'][0] = $DATOS_GLOBALES['sucursal_bco_nacion'];
        // $tmp['info_cabecera'][1] = $DATOS_GLOBALES['tipo_cuenta_banco_nacion'];
        // $tmp['info_cabecera'][2] = $DATOS_GLOBALES['cuenta_banco_nacion'];
        // $tmp['info_cabecera'][3] = $DATOS_GLOBALES['moneda_cuenta_banco_nacion'];
        // $tmp['info_cabecera'][4] = $fechaDebito;
        // $tmp['info_cabecera'][5] = (empty($nroArchivo) ? 1 : $nroArchivo);
        // $tmp['info_cabecera'][6] = "REE";

		$cadena = $this->getIndicadorCabeceraRegistro("00011");
		$cadena .= str_pad($campos[0], 4, '0', STR_PAD_LEFT);
		$cadena .= str_pad($campos[1], 2, '0', STR_PAD_LEFT);
		$cadena .= str_pad($campos[2], 10, '0', STR_PAD_LEFT);
		$cadena .= $campos[3];
		$cadena .= "E";
		$cadena .= date('m',strtotime($campos[4]));
		$cadena .= str_pad(trim($campos[5]), 2, "0", STR_PAD_LEFT);
		$cadena .= date('Ymd',strtotime($campos[4]));
		$cadena .= $campos[6];
		$cadena .= str_pad("", 94, " ", STR_PAD_LEFT);
		$cadena .= "\r\n";
		return $cadena;
	}

	/**
	 * Arma el string para el registro pie del archivo de debito
	 * Datos del Array
	 * 	0	CANTIDAD DE REGISTROS DEL LOTE
	 * 	1	SUMATORIA DE IMPORTE DEL LOTE
	 * @param unknown_type $campos
	 * @return unknown_type
	 */
	function armaStringPieBancoNacion($campos){

        // $tmp['info_pie'][0] = $registros;
        // $tmp['info_pie'][1] = round($importeTotal,2);

		$cadena = $this->getIndicadorPieRegistro("00011");
		$sumatoria = (float) $campos[1];
		$sumatoria = $sumatoria * 100;
		$sumatoria = number_format($sumatoria,0,"","");
		$cadena .= str_pad($sumatoria, 15, '0', STR_PAD_LEFT);
		$cadena .= str_pad($campos[0], 6, '0', STR_PAD_LEFT);
		$cadena .= str_pad("", 15, '0', STR_PAD_LEFT);
		$cadena .= str_pad("", 6, '0', STR_PAD_LEFT);
		$cadena .= str_pad("", 85, " ", STR_PAD_LEFT);
		$cadena .= "\r\n";
		return $cadena;

	}

	function deco_cbu($cbu){
            $datos = array();
            if(empty($cbu)){
                return $datos;
            }
            $datos['validado'] = parent::validarCBU($cbu);
            $datos['bloque_1'] = substr($cbu,0,8);
            $datos['bloque_2'] = substr($cbu,8,14);
            $datos['banco_id'] = str_pad(substr($cbu,0,3),5,'0',STR_PAD_LEFT);
            $datos['digito_1'] = substr($cbu,7,1);
            $datos['sucursal'] = substr($cbu,3,4);
            $datos['tipo_cta_bco'] = substr($cbu,8,2);
            $datos['nro_cta_bco'] = substr($cbu,10,11);
            #VER TEMA SUCURSAL
            App::import('Model','Config.BancoSucursal');
            $oBancoSucursal = new BancoSucursal();
            $sucursal = $oBancoSucursal->find('all',array('conditions' => array('BancoSucursal.banco_id' => $datos['banco_id'],'BancoSucursal.codigo_bcra' => $datos['sucursal']),'fields' => 'BancoSucursal.nro_sucursal', 'limit' => 1));
            if(!empty($sucursal)){
                $datos['sucursal'] = $sucursal[0]['BancoSucursal']['nro_sucursal'];
            }
            //si el banco es el cordoba agregar un cero antes del ultimo digito del nro de cuenta
            if($datos['banco_id']=='00020'){
                    $nroCta = $datos['nro_cta_bco'];
                    $datos['nro_cta_bco'] = substr($nroCta,0,10)."0".substr($nroCta,10,1);
            }
            $datos['digito_2'] = substr($cbu,21,1);
            return $datos;
	}

	/**
	 * VALIDADOR DE NRO DE CBU
	 * Formato del CBU:
	 * EEESSSS-V TTTTTTTTTTTTT-V
	 * Bloque 1:
	 * 	EEE - Número de entidad (3 posiciones)
	 * 	SSSS - Número de sucursal (4 posiciones)
	 * 	V - Dígito verificador de las primeras 7 posiciones
	 *
	 * Bloque 2:
	 * 	TTTTTTTTTTTTT - Identificación de la cuenta individual
	 * 	V - Dígito verificador de las anteriores 13 posiciones
	 *
	 * Para el cálculo de los dígitos verificadores se debe aplicar la clave 10 con el ponderador 9713
	 */
	function isCbuValido($cbu){

		if(empty($cbu)) return false;

		$pond = str_split("9713");

//		$cbu = "0200405511000011135180";
//		$cbu = "0200324311000011237794";
//		debug($cbu);

		$cbu = trim(preg_replace("[^0-9]","",$cbu));

		if(strlen($cbu) != 22) return false;

		$bloque_1 = substr($cbu,0,7);
		$dv_1 = substr($cbu,7,1);
		if(parent::digitoVerificador($bloque_1) != $dv_1) return false;
		$bloque_2 = substr($cbu,8,13);
		$dv_2 = substr($cbu,21,1);
		if(parent::digitoVerificador($bloque_2) != $dv_2) return false;
		return true;
	}

	/**
	 * arma el string para intercambio con la caja
	 * 	1	TIPO (1) 0=PENSIONADO 1=JUBILADO
	 * 	2	LEY (2)
	 * 	3	BENEFICIO (6)
	 * 	4	SUB-BENEFICIO (2)
	 * 	5	CODIGO DE DESCUENTO (3) 207
	 * 	6	SUB-CODIGO (1) 0=CUOTA SOCIAL 1=CUOTA CREDITO
	 * 	7	IMPORTE (7) 5 + 2 DECIMALES
	 * 	8	TIPO_IMPORTE (1) I=IMPORTE P=PORCENTAJE
	 * 	9	FECHA_OTORGAMIENTO (8) AAAAMMDD
	 * 	10	IMPORTE_PRESTAMO (7) 5 + 2
	 * 	11	CANTIDAD_CUOTAS (3)
	 * 	12	IMPORTE_CUOTA (7) 5 + 2
	 * 	13	DEUDA (7) 5 + 2
	 * 	14	DEUDA_VENCIDA (7) 5 + 2
	 * 	15	DEUDA_NO_VENCIDA (7) 5 + 2
	 * 	16 	NRO_OPERACION (12)
	 *
	 * @param unknown_type $campos
	 * @param boolean $formatoNuevo
	 */
	function armaStringDebitoCJP($campos,$formatoNuevo=true){
		$cadena = "";
		$cadena .= $campos[1];
		$cadena .= str_pad($campos[2], 2, '0', STR_PAD_LEFT);
//		$cadena .= str_pad(substr(trim($campos[3]),0,6), 6, '0', STR_PAD_LEFT);
		$cadena .= substr(str_pad(trim($campos[3]), 6, '0', STR_PAD_LEFT),-6);
		$cadena .= str_pad($campos[4], 2, '0', STR_PAD_LEFT);
		$cadena .= $campos[5];
		$cadena .= $campos[6];
		$cadena .= str_pad($campos[7] * 100, 7, '0', STR_PAD_LEFT);
		$cadena .= $campos[8];
		//AGREGO CAMPOS NUEVOS
		if($formatoNuevo):
			if(!empty($campos[9])) $cadena .= date("Ymd",strtotime($campos[9]));
			else $cadena .= "00000000";

			if(!empty($campos[10])) $cadena .= str_pad($campos[10] * 100,7,"0",STR_PAD_LEFT);
			else $cadena .= "0000000";

			if(!empty($campos[11])) $cadena .= str_pad($campos[11],3,"0",STR_PAD_LEFT);
			else $cadena .= "000";

			if(!empty($campos[12])) $cadena .= str_pad($campos[12] * 100,7,"0",STR_PAD_LEFT);
			else $cadena .= "0000000";

			if(!empty($campos[13])) $cadena .= str_pad($campos[13] * 100,7,"0",STR_PAD_LEFT);
			else $cadena .= "0000000";

			if(!empty($campos[14])) $cadena .= str_pad($campos[14] * 100,7,"0",STR_PAD_LEFT);
			else $cadena .= "0000000";

			if(!empty($campos[15])) $cadena .= str_pad($campos[15] * 100,7,"0",STR_PAD_LEFT);
			else $cadena .= "0000000";

			if(!empty($campos[16])) $cadena .= str_pad($campos[16],12,"0",STR_PAD_LEFT);
			else $cadena .= "000000000000";
		endif;
		$cadena .= "\r\n";
		return $cadena;
	}

	/**
	 * Decodifica el string que viene en el diskette de la CJP
	 * @param unknown_type $string
	 * @return string
	 */
	function decodeStringDebitoCJP($string){
		$campos = array();
		$campos['tipo'] = substr($string,0,1);
		$campos['nro_ley'] = substr($string,1,2);
		$campos['nro_beneficio'] = substr($string,3,6);
		$campos['sub_beneficio'] = substr($string,9,2);
//		$campos[4] = substr($string,11,24);
		$campos['codigo_dto'] = substr($string,35,3);
		$campos['sub_codigo'] = substr($string,38,1);
		$campos['importe_debitado'] = intval(substr($string,39,10)) / pow(10,2);
//		$campos[8] = substr($string,49,9);
//		$campos[9] = substr($string,58,1);
		$campos['documento'] = substr($string,59,8);
		$campos['indica_pago'] = 1;
		$campos['status'] =  'OK';

//		$campos[11] = substr($string,67,8);
//		$campos[12] = substr($string,75,3);

		return $campos;
	}


	/**
	 * Nuevo diseño de rendicion de la CJP
	 * 	1	(1) 	PENS=0, JUB=1
	 * 	2	(2)		NRO DE LEY
	 * 	3	(6)		NRO DE BENEFICIO
	 * 	4	(2) 	SUB BENEFICIO
	 * 	5	(24)	APENOM
	 * 	6	(3)		CODIGO ENTIDAD (207)
	 * 	7	(1)		0 = CSOC, 1= CONSUMOS
	 * 	8	(10)	IMPORTE DESCONTADO (8,2)
	 * 	9	(9)		DNI BENEFICIARIO
	 * 	10	(12)	NRO INTERNO OPERACION
	 *  11	(11)	SALDO DE OPERACION
	 * @param string $string
	 * @return array:
	 */
	function decodeNuevoStringDebitoCJP($string){

	    $string = utf8_decode($string);
	    
		$campos = array();

		$campos['tipo'] = substr($string,0,1);
		$campos['nro_ley'] = substr($string,1,2);
		$campos['nro_beneficio'] = substr($string,3,6);
		$campos['sub_beneficio'] = substr($string,9,2);
		$campos['apenom'] = substr($string,11,24);
		$campos['codigo_dto'] = substr($string,35,3);
		$campos['sub_codigo'] = substr($string,38,1);
		$campos['importe_debitado'] = intval(substr($string,39,10)) / pow(10,2);
		$campos['documento'] = str_pad(intval(substr($string,49,9)),8,'0',STR_PAD_LEFT);
		$campos['orden_descuento_id'] = intval(substr($string,58,12));
		$campos['saldo_operacion_informado'] = intval(substr($string,70,11))  / pow(10,2);
		$campos['indica_pago'] = 1;
		$campos['status'] =  'OK';
		return $campos;

	}




	/**
	 * Decodifica el string del diskette del ANSES
	 * @param $string
	 * @return unknown_type
	 */
	function decodeStringDebitoANSES($string){
		$campos = array();
		$campos['nro_beneficio'] = substr($string,0,11);
//		$campos[1] = substr($string,11,22);
//		$campos[2] = substr($string,33,3);
		$campos['documento'] = substr($string,36,8);
		$campos['codigo_dto'] = substr($string,44,6);
//		$campos[5] = substr($string,50,11);
		$campos['importe_debitado'] = intval(substr($string,61,11)) / pow(10,2);
		$campos['indica_pago'] = 1;
		$campos['status'] =  'OK';
//		$campos[7] = substr($string,72,4);
//		$campos[8] = substr($string,76,11);
//		$campos[9] = substr($string,87,6);
		return $campos;
	}

	/**
	 * Generador de CBU
	 * Formato:
	 * 	EEESSSS-V TTTTTTTTTTTTT-V
	 * 	Bloque 1:
	 * 		EEE - Número de entidad (3 posiciones)
	 * 		SSSS - Número de sucursal (4 posiciones)
	 * 		V - Dígito verificador de las primeras 7 posiciones
	 * 	Bloque 2:
	 * 		TTTTTTTTTTTTT - Identificación de la cuenta individual
	 * 		V - Dígito verificador de las anteriores 13 posiciones
	 *
	 * @param unknown_type $bancoID
	 * @param unknown_type $sucursal
	 * @param unknown_type $cuenta
	 * @return unknown_type
	 */
	function genCbu($bancoID,$sucursal,$cuenta){
		$cbu = array();
		$cbu['error'] = 0;
		$cbu['mensaje'] = "";
		$cbu['cbu'] = 0;

		$banco = parent::getBanco($bancoID);

		//bloque 1
		$entidad = str_pad(substr($bancoID,2,3), 3, '0', STR_PAD_LEFT);

		if(strlen($entidad) != 3){
			$cbu['error'] = 1;
			$cbu['mensaje'] = "EL CODIGO DEL BANCO DEBE TENER 3 DIGITOS";
			return $cbu;
		}

		if(empty($sucursal)){
			$cbu['error'] = 1;
			$cbu['mensaje'] = "FALTA INDICAR EL NUMERO DE SUCURSAL";
			return $cbu;
		}

		$sucursal = str_pad(trim($sucursal), 4, '0', STR_PAD_LEFT);

		$bloque_1 = $entidad.$sucursal . parent::digitoVerificador($entidad.$sucursal);

		if(empty($cuenta)){
			$cbu['error'] = 1;
			$cbu['mensaje'] = "FALTA INDICAR EL NUMERO DE CUENTA";
			return $cbu;
		}

		$tipoCta = (isset($banco['Banco']['tipo_cta_sueldo']) ? $banco['Banco']['tipo_cta_sueldo'] : "");
		$tipoCta = str_pad(trim($tipoCta), 2, '0', STR_PAD_LEFT);

		$cuenta = str_pad(trim($cuenta), 11, '0', STR_PAD_LEFT);
		$bloque_2 = $tipoCta.$cuenta . parent::digitoVerificador($tipoCta.$cuenta);

		if(!$this->isCbuValido($bloque_1.$bloque_2)):
			$cbu['error'] = 1;
			$cbu['mensaje'] = "EL CBU GENERADO (".$bloque_1.$bloque_2.") NO ES CORRECTO";
			return $cbu;
		endif;

		$cbu['cbu'] = $bloque_1.$bloque_2;

		return $cbu;
	}


	function getIndicadorCabeceraRegistro($banco_id){
		$banco = $this->read('indicador_cabecera',$banco_id);
		return $banco['Banco']['indicador_cabecera'];
	}

	function getIndicadorDetalleRegistro($banco_id){
		$banco = $this->read('indicador_detalle',$banco_id);
		return $banco['Banco']['indicador_detalle'];
	}

	function getIndicadorPieRegistro($banco_id){
		$banco = $this->read('indicador_pie',$banco_id);
		return $banco['Banco']['indicador_pie'];
	}

	function getLongitudRegistro($banco_id,$IO='IN'){
		$banco = $this->read(($IO == 'IN' ? 'longitud' : 'longitud_salida'),$banco_id);
		return $banco['Banco'][($IO == 'IN' ? 'longitud' : 'longitud_salida')];
	}


	/**
	 * Arma string para envio de información al banco CREDICOOP
	 * 	1	(3)		CODIGO DE BANCO
	 * 	2	(2)		CODIGO DE REGISTRO (== 51)
	 * 	3	(6)		FECHA VENCIMIENTO (AAMMDD)
	 * 	4	(5) 	EMPRESA SUBEMPRESA (NRO ASIGNADO POR EL BANCO)
	 * 	5	(22) 	IDENTIFICADOR DE DEBITO (ID SOCIO 7 + 15 ESPACIOS RIGHT)
	 * 	6	(1) 	MONEDA (P/D)
	 * 	7	(8) 	BLOQUE_1 CBU
	 * 	8	(14) 	BLOQUE_2 CBU
	 * 	9	(10) 	IMPORTE (8 ENTEROS 2 DECIMALES)
	 * 	10	(11) 	CUIT DE LA EMPRESA
	 * 	11	(10)	DESCRIPCION (CUOTA PTMO)
	 * 	12	(15)	VTO / DOCUMENTO
	 * @param array $campos
	 */
	function armaStringDebitoBancoCrediCoop($campos){
        /*
        
				$campos = array(
								1 => substr($cbu,0,3),
								2 => substr($cbu,0,8),
								3 => substr($cbu,8,14),
								4 => $socioId,
								5 => $importeDebito,
								6 => $liquidacionSocioId,
								7 => $fechaDebito,
                                8 => $liquidacionID
				);        
        */
		$DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
		$cadena = str_pad(trim($campos[1]), 3, '0', STR_PAD_LEFT);
		$cadena .= $DATOS_GLOBALES['credicoop_codigo_registro_envio'];
		$cadena .= date("ymd",strtotime($campos[7]));
		$cadena .= $DATOS_GLOBALES['credicoop_empresa_subempresa'];
		$cadena .= str_pad(str_pad(trim($campos[4]), 7, '0', STR_PAD_LEFT),22,' ',STR_PAD_RIGHT);
		$cadena .= "P";
		$cadena .= $campos[2];
		$cadena .= $campos[3];
		$cadena .= str_pad(number_format($campos[5] * 100,0,"",""), 10, '0', STR_PAD_LEFT);
		$cadena .= $DATOS_GLOBALES['credicoop_empresa_cuit'];
//        $cadena .= Configure::read('APLICACION.cuit_mutual');
		$cadena .= $DATOS_GLOBALES['credicoop_descripcion'];
		$cadena .= str_pad(trim($campos[6]), 15, '0', STR_PAD_LEFT);
		$cadena .= "\r\n";
		return $cadena;

	}


	/**
	 * Descompone la cadena de rendicion del bco CREDICOOP
	 * 	1	(3)		CODIGO DE BANCO
	 * 	2	(2)		CODIGO DE REGISTRO (== 52)
	 * 	3	(6)		FECHA VENCIMIENTO (AAMMDD)
	 * 	4	(5) 	EMPRESA SUBEMPRESA (NRO ASIGNADO POR EL BANCO)
	 * 	5	(22) 	IDENTIFICADOR DE DEBITO (ID SOCIO 7 + 15 ESPACIOS RIGHT)
	 * 	6	(1) 	MONEDA (P/D)
	 * 	7	(8) 	BLOQUE_1 CBU
	 * 	8	(14) 	BLOQUE_2 CBU
	 * 	9	(10) 	IMPORTE (8 ENTEROS 2 DECIMALES)
	 * 	10	(11) 	CUIT DE LA EMPRESA
	 * 	11	(10)	DESCRIPCION (CUOTA PTMO)
	 * 	12	(15)	VTO / DOCUMENTO
	 * 	13	(15) 	REFERENCIA UNIVOCA
	 * 	14	(22)	NUEVO CBU O IDENTIFICADOR
	 * 	15	(3)		CODIGO DE RETORNO
	 *
	 * @param string $string
	 * @return array $campos
	 */
	function decodeStringDebitoBancoCredicoop($string){

		$campos = array();
		$campos['codigo_banco'] 		= substr($string,0,3);
		$campos['codigo_registro'] 		= substr($string,3,2);
		$campos['fecha_vto'] 			= substr($string,5,6);
		$campos['empresa'] 				= substr($string,11,5);
		$campos['socio_id'] 			= substr($string,16,22);
		$campos['moneda'] 				= substr($string,38,1);
		$campos['cbu_b1'] 				= substr($string,39,8);
		$campos['cbu_b2'] 				= substr($string,47,14);
		$campos['importe_debitado'] 	= substr($string,61,10);
		$campos['cuit_empresa'] 		= substr($string,71,11);
		$campos['descripcion'] 			= substr($string,82,10);
		$campos['vto_docu'] 			= substr($string,92,15);
		$campos['ref_univ'] 			= substr($string,107,15);
		$campos['ncbu_id'] 				= substr($string,122,22);
		$campos['status'] 				= substr($string,144,3);

		//campos adicionales
		$campos['fecha_debito'] = date('Y-m-d',mktime(0, 0, 0, substr($campos['fecha_vto'],2,2), substr($campos['fecha_vto'],4,2), substr($campos['fecha_vto'],0,2)));
		$campos['socio_id'] = intval(trim($campos['socio_id']));
		$campos['liquidacion_socio_id'] = intval(trim($campos['vto_docu']));
		$campos['importe_debitado'] = intval(trim($campos['importe_debitado'])) / pow(10,2);


		$campos['indica_pago'] = 0;

		$campos['status'] = str_pad(trim($campos['status']),3,'0',STR_PAD_LEFT);

		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();

		$campos['indica_pago'] = ($oCODIGO->isCodigoPago("00191",(!empty($campos['status']) ? $campos['status'] : "ERR")) ? 1 : 0);

		$campos['cbu'] = $campos['cbu_b1'].$campos['cbu_b2'];
		$cbuDecode = $this->deco_cbu($campos['cbu']);
		$campos['banco_id'] = $cbuDecode['banco_id'];
		$campos['sucursal'] = (isset($cbuDecode['sucursal']) ? $cbuDecode['sucursal'] : "");
		$campos['tipo_cta_bco'] = (isset($cbuDecode['tipo_cta_bco']) ? $cbuDecode['tipo_cta_bco'] : "");
		$campos['nro_cta_bco'] = (isset($cbuDecode['nro_cta_bco']) ? $cbuDecode['nro_cta_bco'] : "");

		//armo el arreglo de datos definitivo que uso para insertar
		$datos = array();
		$datos['socio_id'] = $campos['socio_id'];
		$datos['banco_id'] = $campos['banco_id'];
		$datos['sucursal'] = $campos['sucursal'];
		$datos['tipo_cta_bco'] = $campos['tipo_cta_bco'];
		$datos['nro_cta_bco'] = $campos['nro_cta_bco'];
		$datos['cbu'] = $campos['cbu'];
		$datos['importe_debitado'] = $campos['importe_debitado'];
		$datos['status'] = $campos['status'];
		$datos['indica_pago'] = $campos['indica_pago'];
		$datos['fecha_debito'] = $campos['fecha_debito'];

		return $datos;

	}

	/**
	 * GENERADOR DE DISKETTE
	 * Adrian 06/01/2012
	 * @param string $bancoIntercambio
	 * @param date $fechaDebito
	 * @param int $registros
	 * @param float $importeTotal
	 * @param int $nroArchivo
	 * @param array $lote (0 => cadena_renglon_1, 1 => cadena_renglon_2 ....)
	 */
	function genDisketteBanco($bancoIntercambio,$fechaDebito,$registros=0,$importeTotal=0,$nroArchivo=0,$lote = array(),$fechaPresentacion = null,$parametros = null){

		$diskette = array();
		$diskette['uuid'] = parent::generarPIN(20);
		$diskette['status'] = 'OK';
		$diskette['observaciones'] = null;
		$diskette['archivo'] = null;
		$diskette['banco_intercambio'] = $bancoIntercambio;
		$diskette['banco_intercambio_nombre'] = parent::getNombreBanco($bancoIntercambio);
		$diskette['fecha_debito'] = date("d-m-Y",strtotime($fechaDebito));
		$diskette['importe_debito'] = $importeTotal;
		$diskette['cantidad_registros'] = $registros;
		$diskette['lote'] = null;
		$diskette['cabecera'] = null;
		$diskette['pie'] = null;
		$diskette['longitud_registro'] = $this->getLongitudRegistro($bancoIntercambio,'OUT');

		$tmp = array();
                
                $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);

		#BANCO PROVINCIA DE CORDOBA
		if($bancoIntercambio == '00020'):
			$DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
            $CONVENIO = str_pad(trim($DATOS_GLOBALES['nro_empresa_banco_cordoba']),5,'0',STR_PAD_LEFT);
            if(isset($parametros['LiquidacionSocio']['nro_convenio_cba']) && !empty($parametros['LiquidacionSocio']['nro_convenio_cba'])){
                $CONVENIO = str_pad(trim($parametros['LiquidacionSocio']['nro_convenio_cba']),5,'0',STR_PAD_LEFT);
            }
			$diskette['archivo'] = "DEB".$CONVENIO.".HAB";
            $lin = 1;
            foreach($lote as $i => $registro){
                $lote[$i] = substr($registro,0,51) . str_pad($i + 1,6,"0",STR_PAD_LEFT).substr($registro,57,  strlen($registro));
                $lin++;
            }
		endif;

		#STANDAR BANK
		if($bancoIntercambio == '00430'):
			$diskette['archivo'] = "STBANK_".date('Ymd',strtotime($fechaDebito)).".txt";
			$tmp['info_cabecera'][0] = Configure::read('APLICACION.cuit_mutual');
			$tmp['info_cabecera'][1] = 0;
			$tmp['info_cabecera'][2] = "CUOTA PTMO";
			$tmp['info_cabecera'][3] = date('Ymd',strtotime($fechaDebito));

			if(intval($tmp['info_cabecera'][0]) == 0){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "CUIT MUTUAL INCORRECTO";
			}
			if(intval($tmp['info_cabecera'][3]) == 0){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "FECHA DEBITO NO VALIDA";
			}
			$tmp['info_pie'][0] = $registros;
			$tmp['info_pie'][1] = round($importeTotal,2);
			$diskette['cabecera'] = $this->armaStringCabeceraStandarBank($tmp['info_cabecera']);
			$diskette['pie'] = $this->armaStringPieStandarBank($tmp['info_pie']);
		endif;

		#BANCO NACION
		if($bancoIntercambio == '00011'):
			$DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
			$diskette['archivo'] = "NACION".date('Ymd',strtotime($fechaDebito)).".TXT";
			$tmp['info_cabecera'][0] = $DATOS_GLOBALES['sucursal_bco_nacion'];
			$tmp['info_cabecera'][1] = $DATOS_GLOBALES['tipo_cuenta_banco_nacion'];
			$tmp['info_cabecera'][2] = $DATOS_GLOBALES['cuenta_banco_nacion'];
			$tmp['info_cabecera'][3] = $DATOS_GLOBALES['moneda_cuenta_banco_nacion'];
			$tmp['info_cabecera'][4] = $fechaDebito;
			$tmp['info_cabecera'][5] = (empty($nroArchivo) ? 1 : $nroArchivo);
			$tmp['info_cabecera'][6] = "REE";
			$tmp['info_pie'][0] = $registros;
			$tmp['info_pie'][1] = round($importeTotal,2);

			//VALIDO LA CABECERA DEL LOTE
			if(intval($tmp['info_cabecera'][0]) == 0){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "SUCURSAL CUENTA ACREDITACION ERRONEA";
			}
			if(intval($tmp['info_cabecera'][2]) == 0){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "NUMERO DE CUENTA ACREDITACION ERRONEA";
			}
			if(!parent::is_date($tmp['info_cabecera'][4])){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "FECHA DEBITO NO VALIDA";
			}
			$diskette['cabecera'] = $this->armaStringCabeceraBancoNacion($tmp['info_cabecera']);
			$diskette['pie'] = $this->armaStringPieBancoNacion($tmp['info_pie']);
		endif;

		#BANCO CREDICOOP
		if($bancoIntercambio == '00191'):
			$DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
			$diskette['archivo'] = "MAIN".substr($DATOS_GLOBALES['credicoop_empresa_subempresa'],0,3)."_".date('Ymd',strtotime($fechaDebito)).".TXT";
		endif;

		#BANCO SANTANDER RIO
		if($bancoIntercambio == '00072'):
                    $diskette['archivo'] = "RIO_".date('Ymd',strtotime($fechaDebito)).".TXT";
                    $cabecera = "10";
                    $cabecera .= date('Ymd',strtotime($fechaPresentacion));
                    $cabecera .= str_pad(intval($registros),5,'0',STR_PAD_LEFT);
                    $cabecera .= str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 19, '0', STR_PAD_LEFT);
                    $cabecera .= $this->EOL;
                    $diskette['cabecera'] = $cabecera;
                    $diskette['longitud_registro'] = 34;
		endif;

		#BANCO SANTANDER RIO *** BARRIDO ***
		if($bancoIntercambio == '90072'):
                    $diskette['archivo'] = "RIO_".date('Ymd',strtotime($fechaDebito)).".TXT";
                    $cabecera = "10";
                    $cabecera .= date('Ymd',strtotime($fechaPresentacion));
                    $cabecera .= str_pad(intval($registros),5,'0',STR_PAD_LEFT);
                    $cabecera .= str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 19, '0', STR_PAD_LEFT);
                    $cabecera .= $this->EOL;
                    $diskette['cabecera'] = $cabecera;
                    $diskette['longitud_registro'] = 34;
        endif;

		#BANCO SANTANDER RIO *** MARGEN COMERCIAL ***
		if($bancoIntercambio == '99072'):
            $diskette['archivo'] = "RIO_".date('Ymd',strtotime($fechaDebito)).".TXT";
            $cabecera = "10";
            $cabecera .= date('Ymd',strtotime($fechaPresentacion));
            $cabecera .= str_pad(intval($registros),5,'0',STR_PAD_LEFT);
            $cabecera .= str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 19, '0', STR_PAD_LEFT);
            $cabecera .= $this->EOL;
            $diskette['cabecera'] = $cabecera;
            $diskette['longitud_registro'] = 34;
        endif;

		#BANCO GALICIA
		if($bancoIntercambio == '00007'):
			$diskette['archivo'] = "GALICIA_".date('Ymd',strtotime($fechaDebito)).".TXT";
//            debug($fechaPresentacion);
//            debug(date('Ymd',$fechaPresentacion));
//            exit;
			$tmp = array();
			$tmp[2] = "9866";
			$tmp[4] = date('Ymd',strtotime($fechaPresentacion));
			$tmp[5] = (empty($nroArchivo) ? 1 : $nroArchivo);
			$tmp[7] = str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 14, '0', STR_PAD_LEFT);
			$tmp[8] = str_pad(intval($registros),7,'0',STR_PAD_LEFT);
			$diskette['cabecera'] = $this->armaStringCabeceraPieDebitoBancoGalicia($tmp);
			$diskette['pie'] = $this->armaStringCabeceraPieDebitoBancoGalicia($tmp,'P');
// 			$diskette['longitud_registro'] = 350;
		endif;

                #BBVA FRANCES - CUENTAS PROPIAS
		if($bancoIntercambio == '00017'):
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $CODIGO_EMPRESA_NOMBRE_FILE = (isset($INI_FILE['intercambio']['bbva_frances_file_name_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_file_name_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_file_name_codigo_empresa'] : 0);
                    $CODIGO_EMPRESA = str_pad(trim($INI_FILE['intercambio']['bbva_frances_codigo_empresa']),5,'0',STR_PAD_LEFT);
                    $diskette['archivo'] = "PRESENTA.REC";
                    if($CODIGO_EMPRESA_NOMBRE_FILE == 1){
                        $diskette['archivo'] = "DTO".str_pad(trim($CODIGO_EMPRESA),5,'0',STR_PAD_LEFT).".REC";
                    }
                    $tmp = array(
                        0 => '4110',
                        1 => $CODIGO_EMPRESA,
                        2 => date('Ymd',  strtotime($fechaPresentacion)),
                        3 => date('Ymd',  strtotime($fechaPresentacion)),
                        4 => str_pad(intval($registros),8,'0',STR_PAD_LEFT),
                        5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 15, '0', STR_PAD_LEFT),
                        6 => $INI_FILE['intercambio']['bbva_frances_sucursal_cuenta_cargo'],
                        7 => $INI_FILE['intercambio']['bbva_frances_sucursal_cuenta_cargo_dc'],
                        8 => $INI_FILE['intercambio']['bbva_frances_cuenta_cargo'],
                        9 => str_pad($INI_FILE['intercambio']['bbva_frances_codigo_servicio'],10," ",STR_PAD_RIGHT),
                        10 => $INI_FILE['intercambio']['bbva_frances_cuenta_divisa'],
                        11 => '0',
                        12 => $diskette['archivo'],
                        13 => str_pad(trim($INI_FILE['intercambio']['bbva_frances_nombre_ordenante']),36," ",STR_PAD_RIGHT),
                        14 => $INI_FILE['intercambio']['bbva_frances_tipo_cuenta_cbu'],
                        15 => str_pad("",141," ",STR_PAD_LEFT),
                        16 => str_pad((count($lote)*4) + 2,10,0,STR_PAD_LEFT),
                        17 => str_pad("",208," ",STR_PAD_LEFT),
                    );
                    $diskette['cabecera'] = $this->armaStringDebitoCabeceraBancoFrances($tmp);
                    $tmp[0] = '4910';
                    $diskette['pie'] = $this->armaStringDebitoPieBancoFrances($tmp);

                endif;

                #BANCO FRANCES  BARRIDO
		if($bancoIntercambio == '90017'):
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $CODIGO_EMPRESA_NOMBRE_FILE = (isset($INI_FILE['intercambio']['bbva_frances_1_file_name_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_1_file_name_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_1_file_name_codigo_empresa'] : 0);
                    $CODIGO_EMPRESA = str_pad(trim($INI_FILE['intercambio']['bbva_frances_1_codigo_empresa']),5,'0',STR_PAD_LEFT);
                    $diskette['archivo'] = "PRESENTA.REC";
                    if($CODIGO_EMPRESA_NOMBRE_FILE == 1){
                        $diskette['archivo'] = "DTO".str_pad(trim($CODIGO_EMPRESA),5,'0',STR_PAD_LEFT).".REC";
                    }
                    $tmp = array(
                        0 => '4110',
                        1 => $CODIGO_EMPRESA,
                        2 => date('Ymd',  strtotime($fechaPresentacion)),
                        3 => date('Ymd',  strtotime($fechaPresentacion)),
                        4 => str_pad(intval($registros),8,'0',STR_PAD_LEFT),
                        5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 15, '0', STR_PAD_LEFT),
                        6 => $INI_FILE['intercambio']['bbva_frances_1_sucursal_cuenta_cargo'],
                        7 => $INI_FILE['intercambio']['bbva_frances_1_sucursal_cuenta_cargo_dc'],
                        8 => $INI_FILE['intercambio']['bbva_frances_1_cuenta_cargo'],
                        9 => str_pad($INI_FILE['intercambio']['bbva_frances_1_codigo_servicio'],10," ",STR_PAD_RIGHT),
                        10 => $INI_FILE['intercambio']['bbva_frances_1_cuenta_divisa'],
                        11 => '0',
                        12 => $diskette['archivo'],
                        13 => str_pad(trim($INI_FILE['intercambio']['bbva_frances_1_nombre_ordenante']),36," ",STR_PAD_RIGHT),
                        14 => $INI_FILE['intercambio']['bbva_frances_1_tipo_cuenta_cbu'],
                        15 => str_pad("",141," ",STR_PAD_LEFT),
                        16 => str_pad((count($lote)*4) + 2,10,0,STR_PAD_LEFT),
                        17 => str_pad("",208," ",STR_PAD_LEFT),
                    );
                    $diskette['cabecera'] = $this->armaStringDebitoCabeceraBancoFrances($tmp);
                    $tmp[0] = '4910';
                    $diskette['pie'] = $this->armaStringDebitoPieBancoFrances($tmp);

                endif;


                #BANCO FRANCES MULTICOBRO CUENTAS PROPIAS
		if($bancoIntercambio == '91017'):
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $CODIGO_EMPRESA_NOMBRE_FILE = (isset($INI_FILE['intercambio']['bbva_frances_2_file_name_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_2_file_name_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_2_file_name_codigo_empresa'] : 0);
                    $CODIGO_EMPRESA = str_pad(trim($INI_FILE['intercambio']['bbva_frances_2_codigo_empresa']),5,'0',STR_PAD_LEFT);
                    $diskette['archivo'] = "PRESENTA.REC";
                    if($CODIGO_EMPRESA_NOMBRE_FILE == 1){
                        $diskette['archivo'] = "DTO".str_pad(trim($CODIGO_EMPRESA),5,'0',STR_PAD_LEFT).".REC";
                    }
                    $tmp = array(
                        0 => '4110',
                        1 => $CODIGO_EMPRESA,
                        2 => date('Ymd',  strtotime($fechaPresentacion)),
                        3 => date('Ymd',  strtotime($fechaPresentacion)),
                        4 => str_pad(intval($registros),8,'0',STR_PAD_LEFT),
                        5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 15, '0', STR_PAD_LEFT),
                        6 => $INI_FILE['intercambio']['bbva_frances_2_sucursal_cuenta_cargo'],
                        7 => $INI_FILE['intercambio']['bbva_frances_2_sucursal_cuenta_cargo_dc'],
                        8 => $INI_FILE['intercambio']['bbva_frances_2_cuenta_cargo'],
                        9 => str_pad($INI_FILE['intercambio']['bbva_frances_2_codigo_servicio'],10," ",STR_PAD_RIGHT),
                        10 => $INI_FILE['intercambio']['bbva_frances_2_cuenta_divisa'],
                        11 => '0',
                        12 => $diskette['archivo'],
                        13 => str_pad(trim($INI_FILE['intercambio']['bbva_frances_2_nombre_ordenante']),36," ",STR_PAD_RIGHT),
                        14 => $INI_FILE['intercambio']['bbva_frances_2_tipo_cuenta_cbu'],
                        15 => str_pad("",141," ",STR_PAD_LEFT),
                        16 => str_pad((count($lote)*4) + 2,10,0,STR_PAD_LEFT),
                        17 => str_pad("",208," ",STR_PAD_LEFT),
                    );
                    $diskette['cabecera'] = $this->armaStringDebitoCabeceraBancoFrances($tmp);
                    $tmp[0] = '4910';
                    $diskette['pie'] = $this->armaStringDebitoPieBancoFrances($tmp);

                endif;

                #BANCO FRANCES MULTICOBRO CAMARA COMPENSADORA
		if($bancoIntercambio == '92017'):
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $CODIGO_EMPRESA_NOMBRE_FILE = (isset($INI_FILE['intercambio']['bbva_frances_3_file_name_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_3_file_name_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_3_file_name_codigo_empresa'] : 0);
                    $CODIGO_EMPRESA = str_pad(trim($INI_FILE['intercambio']['bbva_frances_3_codigo_empresa']),5,'0',STR_PAD_LEFT);
                    $diskette['archivo'] = "PRESENTA.REC";
                    if($CODIGO_EMPRESA_NOMBRE_FILE == 1){
                        $diskette['archivo'] = "DTO".str_pad(trim($CODIGO_EMPRESA),5,'0',STR_PAD_LEFT).".REC";
                    }
                    $tmp = array(
                        0 => '4110',
                        1 => $CODIGO_EMPRESA,
                        2 => date('Ymd',  strtotime($fechaPresentacion)),
                        3 => date('Ymd',  strtotime($fechaPresentacion)),
                        4 => str_pad(intval($registros),8,'0',STR_PAD_LEFT),
                        5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 15, '0', STR_PAD_LEFT),
                        6 => $INI_FILE['intercambio']['bbva_frances_3_sucursal_cuenta_cargo'],
                        7 => $INI_FILE['intercambio']['bbva_frances_3_sucursal_cuenta_cargo_dc'],
                        8 => $INI_FILE['intercambio']['bbva_frances_3_cuenta_cargo'],
                        9 => str_pad($INI_FILE['intercambio']['bbva_frances_3_codigo_servicio'],10," ",STR_PAD_RIGHT),
                        10 => $INI_FILE['intercambio']['bbva_frances_3_cuenta_divisa'],
                        11 => '0',
                        12 => $diskette['archivo'],
                        13 => str_pad(trim($INI_FILE['intercambio']['bbva_frances_3_nombre_ordenante']),36," ",STR_PAD_RIGHT),
                        14 => $INI_FILE['intercambio']['bbva_frances_3_tipo_cuenta_cbu'],
                        15 => str_pad("",141," ",STR_PAD_LEFT),
                        16 => str_pad((count($lote)*4) + 2,10,0,STR_PAD_LEFT),
                        17 => str_pad("",208," ",STR_PAD_LEFT),
                    );
                    $diskette['cabecera'] = $this->armaStringDebitoCabeceraBancoFrances($tmp);
                    $tmp[0] = '4910';
                    $diskette['pie'] = $this->armaStringDebitoPieBancoFrances($tmp);

                endif;

               #BANCO FRANCES GRUPO JUNIOR CUENTAS PROPIAS
		if($bancoIntercambio == '91117'):
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $CODIGO_EMPRESA_NOMBRE_FILE = (isset($INI_FILE['intercambio']['bbva_frances_4_file_name_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_4_file_name_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_4_file_name_codigo_empresa'] : 0);
                    $CODIGO_EMPRESA = str_pad(trim($INI_FILE['intercambio']['bbva_frances_4_codigo_empresa']),5,'0',STR_PAD_LEFT);
                    $diskette['archivo'] = "PRESENTA.REC";
                    if($CODIGO_EMPRESA_NOMBRE_FILE == 1){
                        $diskette['archivo'] = "DTO".str_pad(trim($CODIGO_EMPRESA),5,'0',STR_PAD_LEFT).".REC";
                    }
                    $tmp = array(
                        0 => '4110',
                        1 => $CODIGO_EMPRESA,
                        2 => date('Ymd',  strtotime($fechaPresentacion)),
                        3 => date('Ymd',  strtotime($fechaPresentacion)),
                        4 => str_pad(intval($registros),8,'0',STR_PAD_LEFT),
                        5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 15, '0', STR_PAD_LEFT),
                        6 => $INI_FILE['intercambio']['bbva_frances_4_sucursal_cuenta_cargo'],
                        7 => $INI_FILE['intercambio']['bbva_frances_4_sucursal_cuenta_cargo_dc'],
                        8 => $INI_FILE['intercambio']['bbva_frances_4_cuenta_cargo'],
                        9 => str_pad($INI_FILE['intercambio']['bbva_frances_4_codigo_servicio'],10," ",STR_PAD_RIGHT),
                        10 => $INI_FILE['intercambio']['bbva_frances_4_cuenta_divisa'],
                        11 => '0',
                        12 => $diskette['archivo'],
                        13 => str_pad(trim($INI_FILE['intercambio']['bbva_frances_4_nombre_ordenante']),36," ",STR_PAD_RIGHT),
                        14 => $INI_FILE['intercambio']['bbva_frances_4_tipo_cuenta_cbu'],
                        15 => str_pad("",141," ",STR_PAD_LEFT),
                        16 => str_pad((count($lote)*4) + 2,10,0,STR_PAD_LEFT),
                        17 => str_pad("",208," ",STR_PAD_LEFT),
                    );
                    $diskette['cabecera'] = $this->armaStringDebitoCabeceraBancoFrances($tmp);
                    $tmp[0] = '4910';
                    $diskette['pie'] = $this->armaStringDebitoPieBancoFrances($tmp);

                endif;

                #BANCO FRANCES GRUPO JUNIOR CAMARA COMPENSADORA
		if($bancoIntercambio == '92117'):
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $CODIGO_EMPRESA_NOMBRE_FILE = (isset($INI_FILE['intercambio']['bbva_frances_5_file_name_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_5_file_name_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_5_file_name_codigo_empresa'] : 0);
                    $CODIGO_EMPRESA = str_pad(trim($INI_FILE['intercambio']['bbva_frances_5_codigo_empresa']),5,'0',STR_PAD_LEFT);
                    $diskette['archivo'] = "PRESENTA.REC";
                    if($CODIGO_EMPRESA_NOMBRE_FILE == 1){
                        $diskette['archivo'] = "DTO".str_pad(trim($CODIGO_EMPRESA),5,'0',STR_PAD_LEFT).".REC";
                    }
                    $tmp = array(
                        0 => '4110',
                        1 => $CODIGO_EMPRESA,
                        2 => date('Ymd',  strtotime($fechaPresentacion)),
                        3 => date('Ymd',  strtotime($fechaPresentacion)),
                        4 => str_pad(intval($registros),8,'0',STR_PAD_LEFT),
                        5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 15, '0', STR_PAD_LEFT),
                        6 => $INI_FILE['intercambio']['bbva_frances_5_sucursal_cuenta_cargo'],
                        7 => $INI_FILE['intercambio']['bbva_frances_5_sucursal_cuenta_cargo_dc'],
                        8 => $INI_FILE['intercambio']['bbva_frances_5_cuenta_cargo'],
                        9 => str_pad($INI_FILE['intercambio']['bbva_frances_5_codigo_servicio'],10," ",STR_PAD_RIGHT),
                        10 => $INI_FILE['intercambio']['bbva_frances_5_cuenta_divisa'],
                        11 => '0',
                        12 => $diskette['archivo'],
                        13 => str_pad(trim($INI_FILE['intercambio']['bbva_frances_5_nombre_ordenante']),36," ",STR_PAD_RIGHT),
                        14 => $INI_FILE['intercambio']['bbva_frances_5_tipo_cuenta_cbu'],
                        15 => str_pad("",141," ",STR_PAD_LEFT),
                        16 => str_pad((count($lote)*4) + 2,10,0,STR_PAD_LEFT),
                        17 => str_pad("",208," ",STR_PAD_LEFT),
                    );
                    $diskette['cabecera'] = $this->armaStringDebitoCabeceraBancoFrances($tmp);
                    $tmp[0] = '4910';
                    $diskette['pie'] = $this->armaStringDebitoPieBancoFrances($tmp);

                endif;

               #BANCO FRANCES FENIX SERVICIOS CUENTAS PROPIAS
		if($bancoIntercambio == '91217'):
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $CODIGO_EMPRESA_NOMBRE_FILE = (isset($INI_FILE['intercambio']['bbva_frances_6_file_name_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_6_file_name_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_6_file_name_codigo_empresa'] : 0);
                    $CODIGO_EMPRESA = str_pad(trim($INI_FILE['intercambio']['bbva_frances_6_codigo_empresa']),5,'0',STR_PAD_LEFT);
                    $diskette['archivo'] = "PRESENTA.REC";
                    if($CODIGO_EMPRESA_NOMBRE_FILE == 1){
                        $diskette['archivo'] = "DTO".str_pad(trim($CODIGO_EMPRESA),5,'0',STR_PAD_LEFT).".REC";
                    }
                    $tmp = array(
                        0 => '4110',
                        1 => $CODIGO_EMPRESA,
                        2 => date('Ymd',  strtotime($fechaPresentacion)),
                        3 => date('Ymd',  strtotime($fechaPresentacion)),
                        4 => str_pad(intval($registros),8,'0',STR_PAD_LEFT),
                        5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 15, '0', STR_PAD_LEFT),
                        6 => $INI_FILE['intercambio']['bbva_frances_6_sucursal_cuenta_cargo'],
                        7 => $INI_FILE['intercambio']['bbva_frances_6_sucursal_cuenta_cargo_dc'],
                        8 => $INI_FILE['intercambio']['bbva_frances_6_cuenta_cargo'],
                        9 => str_pad($INI_FILE['intercambio']['bbva_frances_6_codigo_servicio'],10," ",STR_PAD_RIGHT),
                        10 => $INI_FILE['intercambio']['bbva_frances_6_cuenta_divisa'],
                        11 => '0',
                        12 => $diskette['archivo'],
                        13 => str_pad(trim($INI_FILE['intercambio']['bbva_frances_6_nombre_ordenante']),36," ",STR_PAD_RIGHT),
                        14 => $INI_FILE['intercambio']['bbva_frances_6_tipo_cuenta_cbu'],
                        15 => str_pad("",141," ",STR_PAD_LEFT),
                        16 => str_pad((count($lote)*4) + 2,10,0,STR_PAD_LEFT),
                        17 => str_pad("",208," ",STR_PAD_LEFT),
                    );
                    $diskette['cabecera'] = $this->armaStringDebitoCabeceraBancoFrances($tmp);
                    $tmp[0] = '4910';
                    $diskette['pie'] = $this->armaStringDebitoPieBancoFrances($tmp);

                endif;

                #BANCO FRANCES FENIX SERVICIOS CAMARA COMPENSADORA
		if($bancoIntercambio == '92217'):
                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $CODIGO_EMPRESA_NOMBRE_FILE = (isset($INI_FILE['intercambio']['bbva_frances_7_file_name_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_7_file_name_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_7_file_name_codigo_empresa'] : 0);
                    $CODIGO_EMPRESA = str_pad(trim($INI_FILE['intercambio']['bbva_frances_7_codigo_empresa']),5,'0',STR_PAD_LEFT);
                    $diskette['archivo'] = "PRESENTA.REC";
                    if($CODIGO_EMPRESA_NOMBRE_FILE == 1){
                        $diskette['archivo'] = "DTO".str_pad(trim($CODIGO_EMPRESA),5,'0',STR_PAD_LEFT).".REC";
                    }
                    $tmp = array(
                        0 => '4110',
                        1 => $CODIGO_EMPRESA,
                        2 => date('Ymd',  strtotime($fechaPresentacion)),
                        3 => date('Ymd',  strtotime($fechaPresentacion)),
                        4 => str_pad(intval($registros),8,'0',STR_PAD_LEFT),
                        5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 15, '0', STR_PAD_LEFT),
                        6 => $INI_FILE['intercambio']['bbva_frances_7_sucursal_cuenta_cargo'],
                        7 => $INI_FILE['intercambio']['bbva_frances_7_sucursal_cuenta_cargo_dc'],
                        8 => $INI_FILE['intercambio']['bbva_frances_7_cuenta_cargo'],
                        9 => str_pad($INI_FILE['intercambio']['bbva_frances_7_codigo_servicio'],10," ",STR_PAD_RIGHT),
                        10 => $INI_FILE['intercambio']['bbva_frances_7_cuenta_divisa'],
                        11 => '0',
                        12 => $diskette['archivo'],
                        13 => str_pad(trim($INI_FILE['intercambio']['bbva_frances_7_nombre_ordenante']),36," ",STR_PAD_RIGHT),
                        14 => $INI_FILE['intercambio']['bbva_frances_7_tipo_cuenta_cbu'],
                        15 => str_pad("",141," ",STR_PAD_LEFT),
                        16 => str_pad((count($lote)*4) + 2,10,0,STR_PAD_LEFT),
                        17 => str_pad("",208," ",STR_PAD_LEFT),
                    );
                    $diskette['cabecera'] = $this->armaStringDebitoCabeceraBancoFrances($tmp);
                    $tmp[0] = '4910';
                    $diskette['pie'] = $this->armaStringDebitoPieBancoFrances($tmp);

                endif;


        #BANCO MERIDIAN
        if($bancoIntercambio == '00281'):
            $diskette['archivo'] = "PRESENTA.REC";
            $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
            $diskette['archivo'] = $INI_FILE['intercambio']['meridian_file_name'];
            $pie = array(
                0 => 'T',
                1 => str_pad(count($lote),11,0,STR_PAD_LEFT),
                2 => date('Ymd',  strtotime($fechaPresentacion)),
                3 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 10, '0', STR_PAD_LEFT),
                4 => $INI_FILE['intercambio']['meridian_cuit_empresa'],
                5 => $INI_FILE['intercambio']['medidian_razon_social'],
            );
            $diskette['pie'] = $this->armaStringDebitoPieBancoMeridian($pie);

        endif;
        #######################################################################
        # COBRO DIGITAL
        #######################################################################
        if($bancoIntercambio == '99910'):
            $diskette['archivo'] = "COBRODIGITAL_".date('Ymd',strtotime($fechaDebito)).".TXT";
        endif;

        #######################################################################
        #BANCO COMAFI
        #######################################################################
        if($bancoIntercambio == '00299'):
            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
            $diskette['archivo'] = "EB".$DATOS_GLOBALES['comafi_empresa_codigo'].".".date('Ymd',strtotime($fechaPresentacion));
        endif;

        #######################################################################
        # CUENCA
        #######################################################################
        if($bancoIntercambio == '65203'):
            $diskette['archivo'] = "CUENCA_".date('Ymd',strtotime($fechaDebito)).".TXT";
        endif;

        #######################################################################
        #BANCO NACION *** ISSAR ***
        #######################################################################
        if($bancoIntercambio == '90011'):
                $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
                $diskette['archivo'] = "NACION".date('Ymd',strtotime($fechaDebito)).".TXT";
                $tmp['info_cabecera'][0] = $DATOS_GLOBALES['sucursal_bco_nacion_1'];
                $tmp['info_cabecera'][1] = $DATOS_GLOBALES['tipo_cuenta_banco_nacion_1'];
                $tmp['info_cabecera'][2] = $DATOS_GLOBALES['cuenta_banco_nacion_1'];
                $tmp['info_cabecera'][3] = $DATOS_GLOBALES['moneda_cuenta_banco_nacion_1'];
                $tmp['info_cabecera'][4] = $fechaDebito;
                $tmp['info_cabecera'][5] = (empty($nroArchivo) ? 1 : $nroArchivo);
                $tmp['info_cabecera'][6] = "REE";
                $tmp['info_pie'][0] = $registros;
                $tmp['info_pie'][1] = round($importeTotal,2);

                //VALIDO LA CABECERA DEL LOTE
                if(intval($tmp['info_cabecera'][0]) == 0){
                        $diskette['status'] = 'ERROR';
                        $diskette['observaciones'] = "SUCURSAL CUENTA ACREDITACION ERRONEA";
                }
                if(intval($tmp['info_cabecera'][2]) == 0){
                        $diskette['status'] = 'ERROR';
                        $diskette['observaciones'] = "NUMERO DE CUENTA ACREDITACION ERRONEA";
                }
                if(!parent::is_date($tmp['info_cabecera'][4])){
                        $diskette['status'] = 'ERROR';
                        $diskette['observaciones'] = "FECHA DEBITO NO VALIDA";
                }
                $diskette['cabecera'] = $this->armaStringCabeceraBancoNacion($tmp['info_cabecera']);
                $diskette['pie'] = $this->armaStringPieBancoNacion($tmp['info_pie']);
        endif;
        #######################################################################
        #BANCO NACION *** OTROS ***
        #######################################################################
        if($bancoIntercambio == '91011'):
        $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
        $diskette['archivo'] = "NACION".date('Ymd',strtotime($fechaDebito)).".TXT";
        $tmp['info_cabecera'][0] = $DATOS_GLOBALES['sucursal_bco_nacion_2'];
        $tmp['info_cabecera'][1] = $DATOS_GLOBALES['tipo_cuenta_banco_nacion_2'];
        $tmp['info_cabecera'][2] = $DATOS_GLOBALES['cuenta_banco_nacion_2'];
        $tmp['info_cabecera'][3] = $DATOS_GLOBALES['moneda_cuenta_banco_nacion_2'];
        $tmp['info_cabecera'][4] = $fechaDebito;
        $tmp['info_cabecera'][5] = (empty($nroArchivo) ? 1 : $nroArchivo);
        $tmp['info_cabecera'][6] = "REE";
        $tmp['info_pie'][0] = $registros;
        $tmp['info_pie'][1] = round($importeTotal,2);
        
        //VALIDO LA CABECERA DEL LOTE
        if(intval($tmp['info_cabecera'][0]) == 0){
            $diskette['status'] = 'ERROR';
            $diskette['observaciones'] = "SUCURSAL CUENTA ACREDITACION ERRONEA";
        }
        if(intval($tmp['info_cabecera'][2]) == 0){
            $diskette['status'] = 'ERROR';
            $diskette['observaciones'] = "NUMERO DE CUENTA ACREDITACION ERRONEA";
        }
        if(!parent::is_date($tmp['info_cabecera'][4])){
            $diskette['status'] = 'ERROR';
            $diskette['observaciones'] = "FECHA DEBITO NO VALIDA";
        }
        $diskette['cabecera'] = $this->armaStringCabeceraBancoNacion($tmp['info_cabecera']);
        $diskette['pie'] = $this->armaStringPieBancoNacion($tmp['info_pie']);
        endif;
                #######################################################################
		#FENANJOR ** BANCO NACION **
                #######################################################################
		if($bancoIntercambio == '99920'):
			$DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
			$diskette['archivo'] = "NACION".date('Ymd',strtotime($fechaDebito)).".TXT";
			$tmp['info_cabecera'][0] = $DATOS_GLOBALES['sucursal_bco_nacion'];
			$tmp['info_cabecera'][1] = $DATOS_GLOBALES['tipo_cuenta_banco_nacion'];
			$tmp['info_cabecera'][2] = $DATOS_GLOBALES['cuenta_banco_nacion'];
			$tmp['info_cabecera'][3] = $DATOS_GLOBALES['moneda_cuenta_banco_nacion'];
			$tmp['info_cabecera'][4] = $fechaDebito;
			$tmp['info_cabecera'][5] = (empty($nroArchivo) ? 1 : $nroArchivo);
			$tmp['info_cabecera'][6] = "REE";
			$tmp['info_pie'][0] = $registros;
			$tmp['info_pie'][1] = round($importeTotal,2);

			//VALIDO LA CABECERA DEL LOTE
			if(intval($tmp['info_cabecera'][0]) == 0){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "SUCURSAL CUENTA ACREDITACION ERRONEA";
			}
			if(intval($tmp['info_cabecera'][2]) == 0){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "NUMERO DE CUENTA ACREDITACION ERRONEA";
			}
			if(!parent::is_date($tmp['info_cabecera'][4])){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "FECHA DEBITO NO VALIDA";
			}
			$diskette['cabecera'] = $this->armaStringCabeceraBancoNacion($tmp['info_cabecera']);
			$diskette['pie'] = $this->armaStringPieBancoNacion($tmp['info_pie']);
		endif;

                #######################################################################
		#FENANJOR ** BANCO MACRO **
                #######################################################################
                if($bancoIntercambio == '99921'):
                    $diskette['archivo'] = "FENANJOR_MACRO_".date('Ymd',strtotime($fechaDebito)).".TXT";
                endif;

                #######################################################################
		#FENANJOR ** BANCO CORDOBA **
                #######################################################################
                if($bancoIntercambio == '99922'):   
                    
                    $fenanjor_cba_cliente_id = (isset($INI_FILE['intercambio']['fenanjor_cba_cliente_id']) && $INI_FILE['intercambio']['fenanjor_cba_cliente_id'] != 0 ? $INI_FILE['intercambio']['fenanjor_cba_cliente_id'] : '');                    
                    
                    
                    $diskette['archivo'] = "FENANJOR_CORDOBA_".$fenanjor_cba_cliente_id."_".date('Ymd',strtotime($fechaDebito)).".csv";
                    $diskette['cabecera'] = "ID Cliente;Nro. Doc.;Apellido y Nombres;CBU;Nro. Cta.;Sucursal;Direccion;Codigo Postal;Id Localidad;Id Provincia;Fecha Nac.;Id Banco;Id Empleador;Empleador;fecha;Importe\r\n";
                    $diskette['content_type'] = 'application/vnd.ms-excel';
                    $diskette['field_separator'] = ';';
                    $diskette['eol'] = '\r\n';
                    $diskette['file_extension'] = 'xlsx';                
                endif;                
                
                #######################################################################
		# BANCO MACRO
                #######################################################################
                if($bancoIntercambio == '00285'):

                    $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
                    $fileName = "D". str_pad($DATOS_GLOBALES['macro_nro_convenio'], 5,0,STR_PAD_LEFT);
                    $nroArchivo = (empty($nroArchivo) ? '01' : str_pad($nroArchivo,2,0,STR_PAD_LEFT));
                    $fileName.= $nroArchivo.".285";

                    $diskette['archivo'] = $fileName;

                    /**
                     * 0 => fechaCreacion
                     * 1 => ImporteTotal
                     * 2 => nroConvenio
                     */
                    $tmp['info_cabecera'][0] = date('Ymd',  strtotime($fechaPresentacion));
                    $tmp['info_cabecera'][1] = round($importeTotal,2);
                    $tmp['info_cabecera'][2] = str_pad($DATOS_GLOBALES['macro_nro_convenio'], 5,0,STR_PAD_LEFT);
                    $diskette['cabecera'] = $this->arma_str_debito_macro_cabecera($tmp['info_cabecera']);
                    $diskette['pie'] = array();

                endif;

                #######################################################################
		# BANCO MACRO *** BARRIDO
                #######################################################################
                if($bancoIntercambio == '90285'):

                    $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
                    $fileName = "D". str_pad($DATOS_GLOBALES['macro_b_nro_convenio'], 5,0,STR_PAD_LEFT);
                    $nroArchivo = (empty($nroArchivo) ? '01' : str_pad($nroArchivo,2,0,STR_PAD_LEFT));
                    $fileName.= $nroArchivo.".285";

                    $diskette['archivo'] = $fileName;

                    /**
                     * 0 => fechaCreacion
                     * 1 => ImporteTotal
                     * 5 => nroConvenio
                     */
                    $tmp['info_cabecera'][0] = date('Ymd',  strtotime($fechaPresentacion));
                    $tmp['info_cabecera'][1] = round($importeTotal,2);
                    $tmp['info_cabecera'][2] = str_pad($DATOS_GLOBALES['macro_b_nro_convenio'], 5,0,STR_PAD_LEFT);
                    $diskette['cabecera'] = $this->arma_str_debito_macro_cabecera($tmp['info_cabecera']);
                    $diskette['pie'] = array();


                endif;

                #######################################################################
		#CRONOCRED ** BANCO NACION **
                #######################################################################
		if($bancoIntercambio == '99950'):
			$DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
			$diskette['archivo'] = "NACION".date('Ymd',strtotime($fechaDebito)).".TXT";
			$tmp['info_cabecera'][0] = $DATOS_GLOBALES['cronocred_sucursal_bco_nacion'];
			$tmp['info_cabecera'][1] = $DATOS_GLOBALES['cronocred_tipo_cuenta_banco_nacion'];
			$tmp['info_cabecera'][2] = $DATOS_GLOBALES['cronocred_cuenta_banco_nacion'];
			$tmp['info_cabecera'][3] = $DATOS_GLOBALES['cronocred_moneda_cuenta_banco_nacion'];
			$tmp['info_cabecera'][4] = $fechaDebito;
			$tmp['info_cabecera'][5] = (empty($nroArchivo) ? 1 : $nroArchivo);
			$tmp['info_cabecera'][6] = "REE";
			$tmp['info_pie'][0] = $registros;
			$tmp['info_pie'][1] = round($importeTotal,2);

			//VALIDO LA CABECERA DEL LOTE
			if(intval($tmp['info_cabecera'][0]) == 0){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "SUCURSAL CUENTA ACREDITACION ERRONEA";
			}
			if(intval($tmp['info_cabecera'][2]) == 0){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "NUMERO DE CUENTA ACREDITACION ERRONEA";
			}
			if(!parent::is_date($tmp['info_cabecera'][4])){
				$diskette['status'] = 'ERROR';
				$diskette['observaciones'] = "FECHA DEBITO NO VALIDA";
			}
			$diskette['cabecera'] = $this->armaStringCabeceraBancoNacion($tmp['info_cabecera']);
			$diskette['pie'] = $this->armaStringPieBancoNacion($tmp['info_pie']);
		endif;


                #######################################################################
		        #ADSUS * NACION * ** BANCO NACION **
                #######################################################################
                if($bancoIntercambio == '99960'):
                    $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
                    $diskette['archivo'] = "NACION".date('Ymd',strtotime($fechaDebito)).".TXT";
                    
//                     $lin = 1;
//                     $impoDebito = 0;
//                     foreach($lote as $i => $registro){
//                         $impoDebito += number_format(intval(substr($registro,18,15)) / pow(10,2),2,".","");
//                     }
                    
                    
                    $tmp['info_cabecera'][0] = $DATOS_GLOBALES['adsus_sucursal_bco_nacion'];
                    $tmp['info_cabecera'][1] = $DATOS_GLOBALES['adsus_tipo_cuenta_banco_nacion'];
                    $tmp['info_cabecera'][2] = $DATOS_GLOBALES['adsus_cuenta_banco_nacion'];
                    $tmp['info_cabecera'][3] = $DATOS_GLOBALES['adsus_moneda_cuenta_banco_nacion'];
                    $tmp['info_cabecera'][4] = $fechaDebito;
                    $tmp['info_cabecera'][5] = (empty($nroArchivo) ? 1 : $nroArchivo);
                    $tmp['info_cabecera'][6] = "REE";
                    $tmp['info_pie'][0] = $registros;
                    $tmp['info_pie'][1] = round($importeTotal,2);
        
                    //VALIDO LA CABECERA DEL LOTE
                    if(intval($tmp['info_cabecera'][0]) == 0){
                        $diskette['status'] = 'ERROR';
                        $diskette['observaciones'] = "SUCURSAL CUENTA ACREDITACION ERRONEA";
                    }
                    if(intval($tmp['info_cabecera'][2]) == 0){
                        $diskette['status'] = 'ERROR';
                        $diskette['observaciones'] = "NUMERO DE CUENTA ACREDITACION ERRONEA";
                    }
                    if(!parent::is_date($tmp['info_cabecera'][4])){
                        $diskette['status'] = 'ERROR';
                        $diskette['observaciones'] = "FECHA DEBITO NO VALIDA";
                    }
                    $diskette['cabecera'] = $this->armaStringCabeceraBancoNacion($tmp['info_cabecera']);
                    $diskette['pie'] = $this->armaStringPieBancoNacion($tmp['info_pie']);
                endif;        

                #######################################################################
		        # BANCO DE INVERSION Y COMERCIO EXTERIOR
                #######################################################################
                if($bancoIntercambio == '00300'):

                    $fileName = "CUOTAS_BCOMER_";
                    $nroArchivo = (empty($nroArchivo) ? '1' : str_pad($nroArchivo,2,0,STR_PAD_LEFT));
                    $fileName.= $nroArchivo.'_'.date('Ymd',strtotime($fechaDebito)).".csv";

                    $diskette['archivo'] = $fileName;
                    $diskette['cabecera'] = "nro_entreg;cbu;nro_operac;nro_cuota;fe_vto;nombre;importe\r\n";

                endif;

                
                # cardcred (BRUNO) genDisketteBanco
                #######################################################################
                if($bancoIntercambio == '99997'):
                
                $fileName = "CARDCRED_";
                $nroArchivo = (empty($nroArchivo) ? '1' : str_pad($nroArchivo,2,0,STR_PAD_LEFT));
                $fileName.= $nroArchivo.'_'.date('Ymd',strtotime($fechaDebito)).".csv";
                
                $diskette['archivo'] = $fileName;
                $diskette['cabecera'] = "Nombre;Apellido;Fecha de Nac;Calle;Numero calle;Piso Dpto;Codigo Postal;Ciudad;Provincia;Pais;Cod Area Cel;Nro. Celular;Email;DNI;Monto;Interval;Interval Count;Periodos;Primera fecha a cobrar;Tipo Tarjeta;Fecha Venc;Nro. Tarjeta;Referencia Externa Transaccion\r\n";
                
                // //SETEO PARA QUE SEA DESCARGADO COMO UN ARCHIVO
                $diskette['content_type'] = 'application/vnd.ms-excel';
                $diskette['field_separator'] = ';';
                $diskette['eol'] = '\r\n';
                $diskette['file_extension'] = 'xlsx';
                
                endif;
                
			    #######################################################################
			    # ZENRISE (BRUNO) genDisketteBanco
                #######################################################################
                if($bancoIntercambio == '99998'):

                    $fileName = "ZENRISE_";
                    $nroArchivo = (empty($nroArchivo) ? '1' : str_pad($nroArchivo,2,0,STR_PAD_LEFT));
                    $fileName.= $nroArchivo.'_'.date('Ymd',strtotime($fechaDebito)).".csv";

                    $diskette['archivo'] = $fileName;
                    $diskette['cabecera'] = "Nombre;Apellido;Fecha de Nac;Calle;Numero calle;Piso Dpto;Codigo Postal;Ciudad;Provincia;Pais;Cod Area Cel;Nro. Celular;Email;DNI;Monto;Interval;Interval Count;Periodos;Primera fecha a cobrar;Tipo Tarjeta;Fecha Venc;Nro. Tarjeta;Referencia Externa Transaccion\r\n";

                    // //SETEO PARA QUE SEA DESCARGADO COMO UN ARCHIVO
                   	$diskette['content_type'] = 'application/vnd.ms-excel';
                    $diskette['field_separator'] = ';';
                    $diskette['eol'] = '\r\n';
                    $diskette['file_extension'] = 'xlsx';
                    $diskette['formato'] = 'ZENRISE';

                endif;
                
                ############################################################################
                # INSRED - Archivo xlsx
                ############################################################################
                if($bancoIntercambio=='99996') {
                    
                    // "Codigo Entidad;Id Cliente;Nro Referencia;Fecha 1er Vto;Importe 1er Vto;Fecha 2do Vto;Importe 2do Vto;Fecha 3er Vto;Importe 3er Vto;Mensaje Ticket;Mensaje Pantalla;Codigo Barras/CBU;Servicio;Fecha Vigencia;Importe a Pagar"
                    
                    $fileName = "INSRED_";
                    $nroArchivo = (empty($nroArchivo) ? '1' : str_pad($nroArchivo,2,0,STR_PAD_LEFT));
                    $fileName.= $nroArchivo.'_'.date('Ymd',strtotime($fechaDebito)).".csv";

                    $diskette['archivo'] = $fileName;
                    $diskette['cabecera'] = "Codigo Entidad;Id Cliente;Nro Referencia;Fecha 1er Vto;Importe 1er Vto;Fecha 2do Vto;Importe 2do Vto;Fecha 3er Vto;Importe 3er Vto;Mensaje Ticket;Mensaje Pantalla;Codigo Barras/CBU;Servicio;Fecha Vigencia;Importe a Pagar\r\n";
                    $diskette['formato'] = array(
                        
                    );

                    // SETEO PARA QUE SEA DESCARGADO COMO UN ARCHIVO
                    $diskette['content_type'] = 'application/vnd.ms-excel';
                    $diskette['field_separator'] = ';';
                    $diskette['eol'] = '\r\n';
                    $diskette['file_extension'] = 'xlsx';                    
                    
                    
                }


                #######################################################################
		# BANCO ITAU
                #######################################################################
		if($bancoIntercambio == '00259'){

                    $fileName = "ITAU_";
                    $nroArchivo = (empty($nroArchivo) ? '1' : str_pad($nroArchivo,2,0,STR_PAD_LEFT));
                    $fileName.= $nroArchivo.'_'.date('Ymd',strtotime($fechaDebito)).".txt";

                    /**
                     * 0 = nro de envio
                     * 1 = fecha de envio
                     */
                    $diskette['cabecera'] = $this->arma_string_debito_itau(array($nroArchivo,$fechaPresentacion),'H');


                    /**
                     * 0 = nro de envio
                     * 1 = fecha de envio
                     * 2 = total lote
                     * 3 = registros detalle
                     */
                    $diskette['pie'] = $this->arma_string_debito_itau(
                            array(
                                $nroArchivo,
                                $fechaPresentacion,
                                $importeTotal,
                                intval($registros)
                            ),'T');
                    $diskette['archivo'] = $fileName;
                }


                #######################################################################
		# BANCO COINAG
                #######################################################################
                if($bancoIntercambio == '00431'){

                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $codigo_empresa = (isset($INI_FILE['intercambio']['coinag_empresa_cprestamo']) && $INI_FILE['intercambio']['coinag_empresa_cprestamo'] != 0 ? $INI_FILE['intercambio']['coinag_empresa_cprestamo'] : '0000');

                    $fileName = "DDE".date('md',strtotime($fechaDebito)).'.'.$codigo_empresa;

                    $diskette['archivo'] = $fileName;
                    $diskette['cabecera'] = $this->arma_str_debito_coinag(array($fechaPresentacion),'H');
                    $diskette['pie'] = $this->arma_str_debito_coinag(array(intval($registros),$importeTotal),'T');

                }
                
                #######################################################################
		# BANCO COINAG - M22S
                #######################################################################
                if($bancoIntercambio == '90431'){

                    $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                    $codigo_empresa = (isset($INI_FILE['intercambio']['coinag_empresa1_cprestamo']) && $INI_FILE['intercambio']['coinag_empresa1_cprestamo'] != 0 ? $INI_FILE['intercambio']['coinag_empresa1_cprestamo'] : '0000');

                    $fileName = "DDE".date('md',strtotime($fechaDebito)).'.'.$codigo_empresa;

                    $diskette['archivo'] = $fileName;
                    $diskette['cabecera'] = $this->arma_str_debito_coinag(array($fechaPresentacion),'H', 1);
                    $diskette['pie'] = $this->arma_str_debito_coinag(array(intval($registros),$importeTotal),'T', 1);

                }
                

                #######################################################################
		# BANCO ROELA
                #######################################################################
                if($bancoIntercambio == '00247'){

                    $fileName = "20218387005.".date('Ymd',strtotime($fechaDebito));
                    $diskette['archivo'] = $fileName;
                    $diskette['cabecera'] = $this->arma_str_debito_roela(array($fechaPresentacion),'H');
                    $diskette['pie'] = $this->arma_str_debito_roela(array($fechaPresentacion,intval($registros),$importeTotal),'T');

                }
                
                #####################################################################
                # FIRSDATA
                #####################################################################
                if($bancoIntercambio == '99970') {
                    $diskette['archivo'] = 'DA168D.txt';
                    $diskette['cabecera'] = $this->arma_str_debito_firsdata(
                        array(
                            $fechaPresentacion,
                            $registros,
                            round($importeTotal,2)
                        ),'H');
                }

        #######################################################################
	#BANCO MUTUAL
        #######################################################################
		if($bancoIntercambio == '99999'):
            $diskette['archivo'] = "RECIBOSUELDO_".date('Ymd',strtotime($fechaDebito)).".TXT";
		endif;

		#LONGITUD DE REGISTRO
		$longRegistro = $this->getLongitudRegistro($bancoIntercambio,'IN');

		$linea = 1;

		#REGISTRO CABECERA
		if(!empty($diskette['cabecera'])){
			$len = strlen(str_replace($this->EOL,"", $diskette['cabecera']));
			if($len <> $diskette['longitud_registro']){
//				$diskette['status'] = 'ERROR';
//				$diskette['observaciones'] = "LONGITUD CABECERA DEL LOTE [$len <> ".$diskette['longitud_registro']."]";
			}
			$diskette['lote'] = $diskette['cabecera'];
			$linea++;
		}

		#LOTE DE DATOS
//		if(!empty($lote)){
//
//			foreach($lote as $renglon):
//
//				$len = strlen(str_replace($this->EOL,"",$renglon));
//
//				$diskette['lote'] .= $renglon;
//
//				if($len <> $diskette['longitud_registro']){
//					$diskette['status'] = 'ERROR';
//					$diskette['observaciones'] = "LONGITUD REGISTRO [$len <> ".$diskette['longitud_registro']."] EN LINEA $linea";
//					$diskette['lote'] .= "****** ERROR DE ESTRUCTURA ******" . $this->EOL;
//					break;
//				}
//				$linea++;
//
//			endforeach;
//		}

		$diskette['lote'] .= implode("",$lote);

		#REGISTRO PIE
		if(!empty($diskette['pie'])){
			$len = strlen(str_replace("$this->EOL","", $diskette['pie']));
			if($len <> $diskette['longitud_registro']){
//				$diskette['status'] = 'ERROR';
//				$diskette['observaciones'] = "LONGITUD PIE DEL LOTE [$len <> ".$diskette['longitud_registro']."]";
			}
			$diskette['lote'] .= $diskette['pie'];
		}


		#CONTROL GENERAL DE TOTALES REGISTROS E IMPORTE
		if(count($lote) != $registros){
			$diskette['status'] = 'ERROR';
			$diskette['observaciones'] = "LA CANTIDAD DE REGISTROS DEL LOTE [".count($lote)."] NO COINCIDE CON EL TOTAL [$registros]";
			$diskette['lote'] = null;
		}

		if($registros == 0){
			$diskette['status'] = 'ERROR';
			$diskette['observaciones'] = "ARCHIVO VACIO";
			$diskette['lote'] = null;
		}
		if($importeTotal == 0){
			$diskette['status'] = 'ERROR';
			$diskette['observaciones'] = "IMPORTE TOTAL INCORRECTO";
			$diskette['lote'] = null;
		}
		return $diskette;

	}


	/**
	 *
	 * GENERA EL REGISTRO PARA EL DISKETTE
	 * @param int $bancoIntercambio
	 * @param date $fechaDebito
	 * @param float $importe
	 * @param int $registroNro
	 * @param string $idDebito
	 * @param int $liquidacionSocioId
	 * @param int $socioId
	 * @param string $sucursal
	 * @param string $cuenta
	 * @param string $cbu
	 * @param string $codOrganismo
	 * @param string $calificacion
	 */
	function genRegistroDisketteBanco($bancoIntercambio, $fechaDebito, $importe, $registroNro, $idDebito, $liquidacionSocioId, $socioId, $sucursal, $cuenta, $cbu, $codOrganismo, $apenom, $ndoc, $calificacion = null, $beneficioBancoId = null,$liquidacionID = null,$convenioBcoCba = null,$socioCuitCuil = null,$nroArchivo = NULL,$fechaPresentacion = NULL,$fechaMaxima = NULL,$ciclos = NULL,$idDebitoMin = NULL,$zenrise = NULL, $fechaAltaSocio = NULL){


		$registro = array();
		$registro['cadena'] = null;
		$registro['error'] = 0;
		$registro['mensaje'] = null;
		$registro['importe_debito'] = 0;

//		debug(func_get_args());
//		exit;
		//FORMATEO LA SUCURSAL Y EL NRO DE CUENTA DEL BANCO
		$sucursal = str_pad(trim($sucursal),5,'0',STR_PAD_LEFT);
		$cuenta = str_pad(trim(substr(trim($cuenta),-11)),11,'0',STR_PAD_LEFT);

		$registro['sucursal_formed'] = $sucursal;
		$registro['cuenta_formed'] = $cuenta;


		//CONTROLO EL TOPE
		$importeLiquidado = $importeDebito = $importe;
		$topeCBU = parent::GlobalDato('decimal_2',$codOrganismo);

		/*if($importeLiquidado > $topeCBU){
			$importeDebito = $topeCBU;
			$registro['mensaje'] = "TOPE CBU";
		}*/

		$registro['importe_debito'] = $importeDebito;



		//CONTROLO EN BASE A LA ULTIMA CALIFICACION SI SE ENVIA O NO
		if(!empty($calificacion)) $noEnvia = parent::GlobalDato('logico_2',$calificacion);
		else $noEnvia = 0;

		if($noEnvia == 1){
			$registro['error'] = 1;
			$registro['mensaje'] = parent::GlobalDato('concepto_1',$calificacion);
			return $registro;
		}


		//VERIFICO PARAMETROS CRITICOS
		if(empty($bancoIntercambio)){
			$registro['error'] = 1;
			$registro['mensaje'] = "S/BANCO";
			return $registro;
		}
		if(empty($fechaDebito) && $bancoIntercambio != "99999"){
			$registro['error'] = 1;
			$registro['mensaje'] = "S/FEC.DEB.";
			return $registro;
		}
		if(empty($importe)){
			$registro['error'] = 1;
			$registro['mensaje'] = "S/IMP.";
			return $registro;
		}
		if(empty($codOrganismo)){
			$registro['error'] = 1;
			$registro['mensaje'] = "S/ORG.";
			return $registro;
		}
		if(intval($sucursal) == 0  && $bancoIntercambio == "00011"){
			$registro['error'] = 1;
			$registro['mensaje'] = "S/SUCURSAL";
			return $registro;
		}
		$ctrSinCta = array('99999', '99998', '99997');
		if(intval($cuenta) == 0 && !in_array($cuenta, $ctrSinCta)){
			$registro['error'] = 1;
			$registro['mensaje'] = "S/CUENTA";
			return $registro;
		}

		if($beneficioBancoId === '00020' && empty($cbu)){
                $aCBU = $this->genCbu($beneficioBancoId, $sucursal, $cuenta);
                if($aCBU['error'] != 1){
                    $cbu = $aCBU['cbu'];
                }
        }

//         debug('paso1');
        
        //verifico que si es zenrise tenga el dato de la tarjeta (numero/vencimiento)
        $bancosTarjeta = array('99970','99998','99997');
        if(in_array($bancoIntercambio,$bancosTarjeta) && (empty($zenrise['nroTarjeta']) || empty($zenrise['fechaVencimiento'])
        || !isset($zenrise['nroTarjeta']) || !isset($zenrise['fechaVencimiento']))){
			$registro['error'] = 1;
			$registro['mensaje'] = "S/TARJETA";
			return $registro;
        }
        
        if($bancoIntercambio == "99970" && isset($zenrise['nroTarjeta']) && !empty($zenrise['nroTarjeta'])){
            $longNroTarjeta = strlen($zenrise['nroTarjeta']);
            if($longNroTarjeta != 16) {
                $registro['error'] = 1;
                $registro['mensaje'] = "NTARJETA-16";
                return $registro;
            }
        }
        
//         debug('paso2');
        //controlar el vencimiento de la tarjeta
        $bancos = array('99997','99998');
        if(in_array($bancoIntercambio, $bancos) && !empty($zenrise['nroTarjeta']) && !empty($zenrise['fechaVencimiento'])){
            $periodoTarjeta = date('Ym',strtotime($zenrise['fechaVencimiento']));
            $periodoDebito = date('Ym',strtotime($fechaDebito));
            
//             debug($periodoTarjeta . " * ". $periodoDebito);
            
            if($periodoTarjeta < $periodoDebito){
                $registro['error'] = 1;
                $registro['mensaje'] = "TARJETA/VENC";
                return $registro;
            }
            
        }

//         debug('paso3');

		//VALIDO EL CBU
		if(!parent::validarCBU($cbu) && $bancoIntercambio != "00011" && $bancoIntercambio != "99999"){
			$registro['error'] = 1;
			$registro['mensaje'] = "ERR.CBU";
			return $registro;
		}

                $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);


		//PROCESO LOS DATOS
		switch ($bancoIntercambio) {

			###############################################################
			#BANCO DE CORDOBA
			###############################################################
			case "00020":

				if(strlen($cuenta) > 9) $cuenta = substr($cuenta,-9);

                if($beneficioBancoId != '00020') $sucursal = '800';

				$campos = array(
								2 => $sucursal,
								5 => $cuenta,
								6 => $importeDebito,
								7 => $fechaDebito,
								9 => 0,
								10 => $cbu,
								11 => $registroNro,
								12 => $idDebito,
                                8 => $convenioBcoCba,
				);
//                debug($convenioBcoCba);
				$cadena = $this->armaStringDebitoBcoCba($campos);
//				$valCadena = $this->validateStringDetalleDebitoBancoCordoba($cadena,$bancoIntercambio,($sucursal == '800' ? true : false));
//				if($valCadena['ERROR'] == 0){
					$registro['cadena'] = $cadena;
//				}else{
//					$registro['error'] = 1;
//					$registro['mensaje'] = $valCadena['MENSAJE'];
//				}
				break;

			###############################################################
			#STANDAR BANK
			###############################################################
			case "00430":
				$campos = array(
								3 => substr($cbu,0,8),
								4 => substr($cbu,8,14),
								5 => $idDebito,
								6 => $importeDebito,
								7 => $liquidacionSocioId,
								8 => $fechaDebito
				);
				$cadena = $this->armaStringDebitoStandarBank($campos);
//				$valCadena = $this->validateStringDetalleDebitoBancoStandarBank($cadena,$bancoIntercambio);
//				if($valCadena['ERROR'] == 0){
					$registro['cadena'] = $cadena;
//				}else{
//					$registro['error'] = 1;
//					$registro['mensaje'] = $valCadena['MENSAJE'];
//				}
				break;

			###############################################################
			#BANCO NACION
			###############################################################
			case "00011":
				$campos = array(
								2 => $sucursal,
								4 => $cuenta,
								5 => $importeDebito,
								9 => $socioId,
								10 => $idDebito,
				);
				$cadena = $this->armaStringDebitoBcoNacion($campos);
//				$valCadena = $this->validateStringDetalleDebitoBancoNacion($cadena,$bancoIntercambio);
//				if($valCadena['ERROR'] == 0){
					$registro['cadena'] = $cadena;
//				}else{
//					$registro['error'] = 1;
//					$registro['mensaje'] = $valCadena['MENSAJE'];
//				}
				break;

			###############################################################
			#BANCO CREDICOOP
			###############################################################
			case "00191":
				$campos = array(
								1 => substr($cbu,0,3),
								2 => substr($cbu,0,8),
								3 => substr($cbu,8,14),
								4 => $socioId,
								5 => $importeDebito,
								6 => $liquidacionSocioId,
								7 => $fechaDebito,
                                8 => $liquidacionID
				);
				$cadena = $this->armaStringDebitoBancoCrediCoop($campos);
//				$valCadena = $this->validateStringDetalleDebitoBancoCrediCoop($cadena,$bancoIntercambio);
//				if($valCadena['ERROR'] == 0){
					$registro['cadena'] = $cadena;
//				}else{
//					$registro['error'] = 1;
//					$registro['mensaje'] = $valCadena['MENSAJE'];
//				}
				break;

			###############################################################
			#BANCO SANTANDER RIO
			###############################################################
			case "00072":
				$campos = array(
						1 => $cbu,
						2 => $importeDebito,
						3 => $socioId,
						4 => $liquidacionSocioId,
						5 => $liquidacionID,
						6 => strtoupper($apenom),
						7 => $fechaDebito,
				);

				$cadena = $this->armaStringDebitoBancoSantanderRio($campos);
				$registro['cadena'] = $cadena;
				break;

			###############################################################
			#BANCO SANTANDER RIO ** BARRIDO ***
			###############################################################
			case "90072":
				$campos = array(
						1 => $cbu,
						2 => $importeDebito,
						3 => $socioId,
						4 => $liquidacionSocioId,
						5 => $liquidacionID,
						6 => strtoupper($apenom),
						7 => $fechaDebito,
				);

				$cadena = $this->armaStringDebitoBancoSantanderRio($campos,'santander_1');
				$registro['cadena'] = $cadena;
				break;

			###############################################################
			#BANCO SANTANDER RIO ** MARGEN COMERCIAL ***
			###############################################################
			case "99072":
				$campos = array(
						1 => $cbu,
						2 => $importeDebito,
						3 => $socioId,
						4 => $liquidacionSocioId,
						5 => $liquidacionID,
						6 => strtoupper($apenom),
						7 => $fechaDebito,
				);

				$cadena = $this->armaStringDebitoBancoSantanderRio($campos,'santander_2');
				$registro['cadena'] = $cadena;
				break;

			###############################################################
			#BANCO GALICIA
			###############################################################
			case "00007":
				$campos = array(
                                        1 => $socioId,
                                        2 => $cbu,
                                        7 => $liquidacionID,
                                        8 => $fechaDebito,
                                        9 => $importeDebito,
                                        10=> $liquidacionID,
                                        11 => $liquidacionSocioId,
                                        12 => $idDebito,
                                        13 => str_pad($liquidacionID,5,"0",STR_PAD_LEFT).str_pad($registroNro,3,"0",STR_PAD_LEFT),
				);
// 				$campos = array();
// 				$campos[1] = "10052822";
// 				$campos[2] = "0200429111000030008858";
// 				$campos[7] = "10052822";
// 				$campos[8] = "20131225";
// 				$campos[9] = 152.66;

				$cadena = $this->armaStringDebitoBancoGalicia($campos);
				$registro['cadena'] = $cadena;
				break;

			###############################################################
			# BANCO FRANCES
			###############################################################
                        case "00017":
                            $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_codigo_empresa'] : '00000');
                            $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_longitud_clave'] : 22);
                            $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_concepto_debito'] : 'CUOTA');
                            
                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                                    9 => $convenioBcoCba,
                                    10 => $codigo_empresa,
                                    11 => $long_clave,
                                    12 => $concepto_debito,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoFrances($campos);
                            $registro['error'] = 0;
                            break;

			###############################################################
			# BANCO FRANCES BARRIDO
			###############################################################
                        case "90017":
                            $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_1_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_1_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_1_codigo_empresa'] : '00000');
                            $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_1_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_1_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_1_longitud_clave'] : 22);
                            $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_1_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_1_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_1_concepto_debito'] : 'CUOTA');

                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                                    9 => $convenioBcoCba,
                                    10 => $codigo_empresa,
                                    11 => $long_clave,
                                    12 => $concepto_debito,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoFrances($campos);
                            $registro['error'] = 0;
                            break;

			###############################################################
			# BANCO FRANCES MULTICOBRO - CUENTAS PROPIAS
			###############################################################
                        case "91017":

                            $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_2_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_2_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_2_codigo_empresa'] : '00000');
                            $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_2_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_2_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_2_longitud_clave'] : 22);
                            $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_2_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_2_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_2_concepto_debito'] : 'CUOTA');

                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                                    9 => $convenioBcoCba,
                                    10 => $codigo_empresa,
                                    11 => $long_clave,
                                    12 => $concepto_debito,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoFrances($campos);
                            $registro['error'] = 0;
                            break;

			###############################################################
			# BANCO FRANCES MULTICOBRO - CAMARA COMPENSADORA
			###############################################################
                        case "92017":

                           $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_3_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_3_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_3_codigo_empresa'] : '00000');
                            $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_3_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_3_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_3_longitud_clave'] : 22);
                            $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_3_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_3_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_3_concepto_debito'] : 'CUOTA');

                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                                    9 => $convenioBcoCba,
                                    10 => $codigo_empresa,
                                    11 => $long_clave,
                                    12 => $concepto_debito,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoFrances($campos);
                            $registro['error'] = 0;
                            break;

			###############################################################
			# BANCO FRANCES GRUPO JUNIOR - CUENTAS PROPIAS
			###############################################################
                        case "91117":

                            $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_4_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_4_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_4_codigo_empresa'] : '00000');
                            $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_4_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_4_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_4_longitud_clave'] : 22);
                            $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_4_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_4_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_4_concepto_debito'] : 'CUOTA');

                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                                    9 => $convenioBcoCba,
                                    10 => $codigo_empresa,
                                    11 => $long_clave,
                                    12 => $concepto_debito,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoFrances($campos);
                            $registro['error'] = 0;
                            break;

			###############################################################
			# BANCO FRANCES GRUPO JUNIOR - CAMARA COMPENSADORA
			###############################################################
                        case "92117":

                           $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_5_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_5_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_5_codigo_empresa'] : '00000');
                            $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_5_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_5_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_5_longitud_clave'] : 22);
                            $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_5_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_5_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_5_concepto_debito'] : 'CUOTA');

                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                                    9 => $convenioBcoCba,
                                    10 => $codigo_empresa,
                                    11 => $long_clave,
                                    12 => $concepto_debito,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoFrances($campos);
                            $registro['error'] = 0;
                            break;


			###############################################################
			# BANCO FRANCES FENIX SERVICIOS - CUENTAS PROPIAS
			###############################################################
                        case "91217":

                            $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_6_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_6_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_6_codigo_empresa'] : '00000');
                            $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_6_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_6_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_6_longitud_clave'] : 22);
                            $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_6_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_6_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_6_concepto_debito'] : 'CUOTA');

                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                                    9 => $convenioBcoCba,
                                    10 => $codigo_empresa,
                                    11 => $long_clave,
                                    12 => $concepto_debito,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoFrances($campos);
                            $registro['error'] = 0;
                            break;

			###############################################################
			# BANCO FRANCES FENIX SERVICIOS - CAMARA COMPENSADORA
			###############################################################
                        case "92217":

                           $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_7_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_7_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_7_codigo_empresa'] : '00000');
                            $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_7_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_7_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_7_longitud_clave'] : 22);
                            $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_7_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_7_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_7_concepto_debito'] : 'CUOTA');

                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                                    9 => $convenioBcoCba,
                                    10 => $codigo_empresa,
                                    11 => $long_clave,
                                    12 => $concepto_debito,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoFrances($campos);
                            $registro['error'] = 0;
                            break;


                        ################################################################
                        # BANCO MERIDIAN
                        ################################################################
                        /**
                         * 1    1   INDICADOR DE LOTE "D"
                         * 2    11  CUIT EMPRESA
                         * 3    22  CBU SOCIO
                         * 4    22  ID CLIENTE
                         * 5    8   VENCIMIENTO
                         * 6    10  PRESTACION
                         * 7    15  FILLER
                         * 8    15  REFERENCIA DEBITO
                         * @param type $campos
                         */
                        case "00281":
                            $campos = array(
                                    0 => $idDebito,
                                    1 => $registroNro,
                                    2 => $apenom,
                                    3 => $cbu,
                                    4 => $importeDebito,
                                    5 => $fechaDebito,
                                    6 => $socioId,
                                    7 => $liquidacionSocioId,
                                    8 => $liquidacionID,
                            );
                            $registro['cadena'] = $this->armaStringDebitoBancoMeridian($campos);
                            $registro['error'] = 0;
                            break;

                        ###############################################################
			# COBRO DIGITAL
			###############################################################
                        case "99910":
                            $campos = array(
                                    0 => $socioId,
                                    1 => $fechaDebito,
                                    2 => substr($apenom,0,49),
                                    3 => (!empty($socioCuitCuil) ? $socioCuitCuil : $ndoc),
                                    4 => "CUOTA",
                                    5 => $importeDebito,
                                    6 => null,
                                    7 => $cbu,
                            );
                            $registro['cadena'] = $this->armaStringDebitoCobroDigital($campos);
                            $registro['error'] = 0;
                            break;

			###############################################################
			#BANCO COMAFI
			###############################################################
			case "00299":
				$campos = array(
						1 => $socioId,
						2 => $cbu,
						7 => str_pad($liquidacionID,4,"0",STR_PAD_LEFT).str_pad($registroNro,2,"0",STR_PAD_LEFT),
						8 => $fechaDebito,
						9 => $importeDebito,
                                                10 => $liquidacionID,
                                                11 => $liquidacionSocioId,
				);
				$cadena = $this->arma_str_debito_banco_comafi($campos);
				$registro['cadena'] = $cadena;
				break;


			###############################################################
			#MUTUAL
			###############################################################

			case "99999":

				$campos = array(
					1 => $ndoc,
					2 => $apenom,
					3 => $importeDebito,
					4 => $socioId,
				);
				$registro['cadena'] = $this->armaStringDebitoMutual($campos);
				$registro['error'] = 0;
				break;


	  ###############################################################
	  # CAJA DE CREDITO CUENCA COOPERATIVA LIM
	  ###############################################################
	  case "65203":
	      $campos = array(
	          0 => (!empty($socioCuitCuil) ? $socioCuitCuil : $ndoc),
	          1 => $cbu,
	          2 => "",
	          3 => $socioId,
	          4 => $fechaPresentacion,
	          5 => $fechaDebito,
	          6 => $importeDebito,
	          7 => $ciclos,
	          8 => $liquidacionID,
	          9 => $registroNro,
	          10 => $liquidacionSocioId,
	          11 => $fechaMaxima,
	          12 => $fechaAltaSocio
	      );
	      $cadena = $this->arma_str_debito_cuenca($campos);
	      $registro['cadena'] = $cadena;
	      break;

			###############################################################
			#BANCO NACION *** ISSAR ***
			###############################################################
			case "90011":

//				$campos = array(
//                                                              1 => $ndoc,
//								2 => $sucursal,
//								4 => $cuenta,
//								5 => $importeDebito,
//								9 => $socioId,
//								10 => $idDebito,
//				);


                                $idDebito = str_pad($liquidacionID, 4, '0', STR_PAD_LEFT);
                                $idDebito .= str_pad($socioId, 6, '0', STR_PAD_LEFT);
				$campos = array(
                                                                1 => $ndoc,
								2 => $sucursal,
								4 => $cuenta,
								5 => $importeDebito,
								9 => $socioId,
								10 => $idDebito,
				);
				$cadena = $this->armaStringDebitoBcoNacion($campos,$fechaPresentacion);
//				$valCadena = $this->validateStringDetalleDebitoBancoNacion($cadena,$bancoIntercambio);
//				if($valCadena['ERROR'] == 0){
					$registro['cadena'] = $cadena;
//				}else{
//					$registro['error'] = 1;
//					$registro['mensaje'] = $valCadena['MENSAJE'];
//				}
				break;

				
				###############################################################
				#BANCO NACION *** OTROS ***
				###############################################################
			case "91011":
				    
				    //				$campos = array(
				        //                                                              1 => $ndoc,
				        //								2 => $sucursal,
				        //								4 => $cuenta,
				        //								5 => $importeDebito,
				        //								9 => $socioId,
				        //								10 => $idDebito,
				        //				);
				
				
				        $idDebito = str_pad($liquidacionID, 4, '0', STR_PAD_LEFT);
				        $idDebito .= str_pad($socioId, 6, '0', STR_PAD_LEFT);
				        $campos = array(
				            1 => $ndoc,
				            2 => $sucursal,
				            4 => $cuenta,
				            5 => $importeDebito,
				            9 => $socioId,
				            10 => $idDebito,
				        );
				        $cadena = $this->armaStringDebitoBcoNacion($campos,$fechaPresentacion);
				        //				$valCadena = $this->validateStringDetalleDebitoBancoNacion($cadena,$bancoIntercambio);
				        //				if($valCadena['ERROR'] == 0){
				        $registro['cadena'] = $cadena;
				        //				}else{
				        //					$registro['error'] = 1;
				        //					$registro['mensaje'] = $valCadena['MENSAJE'];
				        //				}
				        break;
				
                        #############################################################################################
                        # FENANJOR NACION -- 99920
                        #############################################################################################
                        case '99920':

//				$campos = array(
//								1 => $ndoc,
//								2 => $sucursal,
//								3 => $apenom,
//								4 => $cuenta,
//								5 => $importeDebito,
//								6 => $cbu,
//								9 => $socioId,
//								10 => $idDebito,
//				);

                                $idDebito = str_pad($liquidacionID, 4, '0', STR_PAD_LEFT);
                                $idDebito .= str_pad($socioId, 6, '0', STR_PAD_LEFT);
				$campos = array(
								1 => $ndoc,
                                                                2 => $sucursal,
                                                                3 => $apenom,
								4 => $cuenta,
								5 => $importeDebito,
								6 => $cbu,
                                                                9 => $socioId,
								10 => $idDebito,
				);
				$cadena = $this->armaStringDebitoBcoNacion($campos);
                                $registro['cadena'] = $cadena;

                            break;
                        #############################################################################################
                        # FENANJOR MACRO -- 99921
                        #############################################################################################
                        case '99921':

//				$campos = array(
//								1 => $ndoc,
//								2 => $sucursal,
//								3 => $apenom,
//								4 => $cuenta,
//								5 => $importeDebito,
//								6 => $cbu,
//								7 => $fechaDebito,
//								9 => $socioId,
//								10 => $idDebito,
//				);
                                $idDebito = str_pad($liquidacionID, 4, '0', STR_PAD_LEFT);
                                $idDebito .= str_pad($socioId, 6, '0', STR_PAD_LEFT);
				$campos = array(
								1 => $ndoc,
                                                                2 => $sucursal,
								3 => $apenom,
                                                                4 => $cuenta,
								5 => $importeDebito,
								6 => $cbu,
                                                                7 => $fechaDebito,
                                                                9 => $socioId,
								10 => $idDebito,
				);
				$cadena = $this->arma_str_debito_fenanjor_macro($campos);
                                $registro['cadena'] = $cadena;
                            break;
                        #############################################################################################
                        # FENANJOR CORDOBA -- 99921
                        #############################################################################################
                        
                        case '99922':
                                
                            $fenanjor_cba_cliente = (isset($INI_FILE['intercambio']['fenanjor_cba_cliente']) && $INI_FILE['intercambio']['fenanjor_cba_cliente'] != 0 ? $INI_FILE['intercambio']['fenanjor_cba_cliente'] : '');
                            $fenanjor_cba_cliente_id = (isset($INI_FILE['intercambio']['fenanjor_cba_cliente_id']) && $INI_FILE['intercambio']['fenanjor_cba_cliente_id'] != 0 ? $INI_FILE['intercambio']['fenanjor_cba_cliente_id'] : '');
                            $fenanjor_cba_direccion = (isset($INI_FILE['intercambio']['fenanjor_cba_direccion']) && $INI_FILE['intercambio']['fenanjor_cba_direccion'] != "" ? $INI_FILE['intercambio']['fenanjor_cba_direccion'] : '');
                            $fenanjor_cba_cp = (isset($INI_FILE['intercambio']['fenanjor_cba_cp']) && $INI_FILE['intercambio']['fenanjor_cba_cp'] != "" ? $INI_FILE['intercambio']['fenanjor_cba_cp'] : '');
                            $fenanjor_cba_localidad_id = (isset($INI_FILE['intercambio']['fenanjor_cba_localidad_id']) && $INI_FILE['intercambio']['fenanjor_cba_localidad_id'] != "" ? $INI_FILE['intercambio']['fenanjor_cba_localidad_id'] : '');
                            $fenanjor_cba_provincia_id = (isset($INI_FILE['intercambio']['fenanjor_cba_provincia_id']) && $INI_FILE['intercambio']['fenanjor_cba_provincia_id'] != "" ? $INI_FILE['intercambio']['fenanjor_cba_provincia_id'] : '');
                            $fenanjor_cba_banco_id = (isset($INI_FILE['intercambio']['fenanjor_cba_banco_id']) && $INI_FILE['intercambio']['fenanjor_cba_banco_id'] != "" ? $INI_FILE['intercambio']['fenanjor_cba_banco_id'] : '');
                            
                            
                            $campos = array(
                                0 => $socioId,
                                2 => utf8_decode($apenom),
                                1 => intval($ndoc),
                                3 => utf8_decode($cbu),
                                4 => strval($cuenta),
                                5 => strval($sucursal),
                                6 => $fenanjor_cba_direccion,
                                7 => $fenanjor_cba_cp,
                                8 => $fenanjor_cba_localidad_id,
                                9 => $fenanjor_cba_provincia_id,
                                10 => '',
                                11 => $fenanjor_cba_banco_id,
                                12 => $fenanjor_cba_cliente_id,
                                13 => $fenanjor_cba_cliente,
                                14 => $fechaDebito,
                                15 => $importeDebito,                                
                            );                                
                            $cadena = $this->arma_csv_row($campos);
                            $registro['cadena'] = $cadena;                                
                                
                            break;

			###############################################################
			#BANCO MACRO
			###############################################################
                        case '00285':

                            /**
                             * 0 => CBU
                             * 1 => idDebito
                             * 2 => liquidacionSocioId
                             * 3 => fechaDebito
                             * 4 => importeDebito
                             * 5 => nroConvenio
                             */

                            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
                            $nroConvenio = $DATOS_GLOBALES['macro_nro_convenio'];

                            $campos = array(
                                                            0 => $cbu,
                                                            1 => $idDebito,
                                                            2 => $liquidacionSocioId,
                                                            3 => $fechaDebito,
                                                            4 => $importeDebito,
                                                            5 => $nroConvenio,
                            );
                            $cadena = $this->arma_str_debito_macro($campos);
                            $registro['cadena'] = $cadena;

                            break;

			###############################################################
			#BANCO MACRO *** BARRIDO ***
			###############################################################
                        case '90285':

                            /**
                             * 0 => CBU
                             * 1 => idDebito
                             * 2 => liquidacionSocioId
                             * 3 => fechaDebito
                             * 4 => importeDebito
                             * 5 => nroConvenio
                             */

                            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
                            $nroConvenio = $DATOS_GLOBALES['macro_b_nro_convenio'];

                            $campos = array(
                                                            0 => $cbu,
                                                            1 => $idDebito,
                                                            2 => $liquidacionSocioId,
                                                            3 => $fechaDebito,
                                                            4 => $importeDebito,
                                                            5 => $nroConvenio,
                            );
                            $cadena = $this->arma_str_debito_macro($campos);
                            $registro['cadena'] = $cadena;

                            break;

                        #############################################################################################
                        # CRONOCRED NACION -- 99950
                        #############################################################################################
                        case '99950':

//				$campos = array(
//								1 => $ndoc,
//								2 => $sucursal,
//								3 => $apenom,
//								4 => $cuenta,
//								5 => $importeDebito,
//								6 => $cbu,
//								9 => $socioId,
//								10 => $idDebito,
//				);

                                $idDebito = str_pad($liquidacionID, 4, '0', STR_PAD_LEFT);
                                $idDebito .= str_pad($socioId, 6, '0', STR_PAD_LEFT);
				$campos = array(
								1 => $ndoc,
                                                                2 => $sucursal,
                                                                3 => $apenom,
								4 => $cuenta,
								5 => $importeDebito,
								6 => $cbu,
                                                                9 => $socioId,
								10 => $idDebito,
				);
				$cadena = $this->armaStringDebitoBcoNacion($campos);
                                $registro['cadena'] = $cadena;

                            break;

			###############################################################
			# 00431 - BANCO COINAG
			###############################################################
			case '00431':
                            $idDebitoMin = str_pad($socioId,5,"0",STR_PAD_LEFT).str_pad($liquidacionID,4,"0",STR_PAD_LEFT).str_pad($registroNro,2,"0",STR_PAD_LEFT);
                            $campos = array(
                                                            0 => $ndoc,
                                                            1 => $cbu,
                                                            2 => $idDebitoMin,
                                                            3 => $importeDebito,
                                                            4 => $fechaDebito,
                            );
                            $cadena = $this->arma_str_debito_coinag($campos,'D');
                            $registro['cadena'] = $cadena;
                            break;

			###############################################################
			# 00431 - BANCO COINAG - M22S
			###############################################################
			case '90431':
                            $idDebitoMin = str_pad($socioId,5,"0",STR_PAD_LEFT).str_pad($liquidacionID,4,"0",STR_PAD_LEFT).str_pad($registroNro,2,"0",STR_PAD_LEFT);
                            $campos = array(
                                                            0 => $ndoc,
                                                            1 => $cbu,
                                                            2 => $idDebitoMin,
                                                            3 => $importeDebito,
                                                            4 => $fechaDebito,
                            );
                            $cadena = $this->arma_str_debito_coinag($campos,'D', 1);
                            $registro['cadena'] = $cadena;
                            break;


        ###############################################################
        #BANCO DE INVERSION Y COMERCIO EXTERIOR
        ###############################################################
                    case '00300':
                        $campos = array(
                                                        0 => $cbu,
                                                        1 => $idDebito,
                                                        2 => $liquidacionSocioId,
                                                        3 => $fechaDebito,
                                                        4 => $importeDebito,
                                                        5 => $nroArchivo,
                                                        6 => $apenom,
                                                        7 => $socioId,
                                                        8 => $registroNro,
                                                        9 => $liquidacionID
                        );
                        $cadena = $this->arma_string_debito_comercial($campos);
                        $registro['cadena'] = $cadena;
                        break;


	###############################################################
	#CARDCRED genRegistroDisketteBanco
	###############################################################
                    case '99997':

                        $campos = array(
                                                        0 => $zenrise['nombre'],
                                                        1 => $zenrise['apellido'],
                                                        2 => $zenrise['fechaNacimiento'],
                                                        3 => $zenrise['calle'],
                                                        4 => $zenrise['nroCalle'],
                                                        5 => $zenrise['piso'],
                                                        6 => $zenrise['codigoPostal'],
                                                        7 => $zenrise['localidad'],
																												8 => $zenrise['provincia'],
																												9 => $zenrise['pais'],
																												10 => $zenrise['codArea'],
																												11 => $zenrise['nroCel'],
																												12 => $zenrise['email'],
																												13 => $ndoc,
																												14 => $importe,
																												15 => $zenrise['interval'],
																												16 => $zenrise['intervalCount'],
																												17 => $zenrise['periodos'],
																												18 => $fechaDebito,
																												19 => $zenrise['tipoTarjeta'],
																												20 => $zenrise['fechaVencimiento'],
																												21 => $zenrise['nroTarjeta'],
																												22 => $idDebito,

                        );
												/*debug($zenrise);
												exit();*/
                        $cadena = $this->arma_csv_row($campos);

                        $registro['cadena'] = $cadena;

                        break;

                        ###############################################################
                        #ZENRISE genRegistroDisketteBanco
                        ###############################################################
                    case '99998':
                            
                            $campos = array(
                                0 => $zenrise['nombre'],
                                1 => $zenrise['apellido'],
                                2 => $zenrise['fechaNacimiento'],
                                3 => $zenrise['calle'],
                                4 => $zenrise['nroCalle'],
                                5 => $zenrise['piso'],
                                6 => $zenrise['codigoPostal'],
                                7 => $zenrise['localidad'],
                                8 => $zenrise['provincia'],
                                9 => $zenrise['pais'],
                                10 => $zenrise['codArea'],
                                11 => $zenrise['nroCel'],
                                12 => $zenrise['email'],
                                13 => $ndoc,
                                14 => $importe,
                                15 => $zenrise['interval'],
                                16 => $zenrise['intervalCount'],
                                17 => $zenrise['periodos'],
                                18 => $fechaDebito,
                                19 => $zenrise['tipoTarjeta'],
                                20 => $zenrise['fechaVencimiento'],
                                21 => $zenrise['nroTarjeta'],
                                22 => $idDebito,
                                
                            );
                            /*debug($zenrise);
                             exit();*/
                            $cadena = $this->arma_csv_row($campos);
                            
                            $registro['cadena'] = $cadena;
                            
                            break;
                        

			###############################################################
			#BANCO ITAU
			###############################################################
                        case '00259':

                            /**
                             * 0 = IDD
                             * 1 = apenom
                             * 2 = CUIT
                             * 3 = fecha debito
                             * 4 = importe debito
                             * 5 = CBU
                             */
                            $apenom = str_replace(","," ",utf8_decode($apenom));
                            $idDebitoMin = str_pad($socioId,5,"0",STR_PAD_LEFT).str_pad($liquidacionID,4,"0",STR_PAD_LEFT).str_pad($registroNro,2,"0",STR_PAD_LEFT);
                            $campos = array(
                                                            0 => $idDebitoMin,
                                                            1 => $apenom,
                                                            2 => $socioCuitCuil,
                                                            3 => $fechaDebito,
                                                            4 => $importeDebito,
                                                            5 => $cbu,
                            );
                            $cadena = $this->arma_string_debito_itau($campos,'D');
                            $registro['cadena'] = $cadena;
                            break;

                        ##################################################################
                        # BANCO ROELA 00247
                        ##################################################################
                        case '00247':

                            /**
                             * 0 NDOC
                             * 1 iddMin
                             * 2 fechaDebito
                             * 3 importe
                             */
                            $idDebitoMin = str_pad($socioId,5,"0",STR_PAD_LEFT).str_pad($liquidacionID,4,"0",STR_PAD_LEFT).str_pad($registroNro,2,"0",STR_PAD_LEFT);
                            $campos = array(
                                                            0 => $ndoc,
                                                            1 => $idDebitoMin,
                                                            2 => $fechaDebito,
                                                            3 => $importeDebito,
                            );
                            $cadena = $this->arma_str_debito_roela($campos,'D');
                            $registro['cadena'] = $cadena;
                            break;

                        #############################################################################################
                        # ADSUS * NACION * -- 99960
                        #############################################################################################
                        case '99960':

                            //				$campos = array(
                            //								1 => $ndoc,
                            //								2 => $sucursal,
                            //								3 => $apenom,
                            //								4 => $cuenta,
                            //								5 => $importeDebito,
                            //								6 => $cbu,
                            //								9 => $socioId,
                            //								10 => $idDebito,
                            //				);
                            
                            $idDebito = str_pad($liquidacionID, 4, '0', STR_PAD_LEFT);
                            $idDebito .= str_pad($socioId, 6, '0', STR_PAD_LEFT);
                                                            
//                 	        $importeDebito = ($importeDebito < 7500 ? $importeDebito : 7500);
//                 	        $importeDebito = ( ceil($importeDebito) % 10 === 0) ? ceil($importeDebito) : round(($importeDebito + 10 / 2) / 10 )* 10;
                                                            
                                                            
                            $campos = array(
                                            1 => $ndoc,
                                            2 => $sucursal,
                                            3 => $apenom,
                                            4 => $cuenta,
                                            5 => $importeDebito,
                                            6 => $cbu,
                                            9 => $socioId,
                                            10 => $idDebito,
                            );
                            $cadena = $this->armaStringDebitoBcoNacion($campos, null, true);
                            $registro['cadena'] = $cadena;
                          
                            break;

                        #############################################################################################
                        # FIRSDATA -- 99970
                        #############################################################################################
                        case '99970':
                            $campos = array(
                                0 => $socioCuitCuil,
                                1 => $zenrise['nroTarjeta'],
                                2 => $sucursal,
                                3 => $apenom,
                                4 => $cuenta,
                                5 => $importeDebito,
                                6 => $liquidacionID,
                                9 => $socioId,
                                10 => $liquidacionSocioId,
                                11 => $fechaDebito,
                                12 => str_pad($socioId,5,"0",STR_PAD_LEFT).str_pad($liquidacionID,4,"0",STR_PAD_LEFT).str_pad($registroNro,2,"0",STR_PAD_LEFT),
                            );
                            $cadena = $this->arma_str_debito_firsdata($campos);
                            $registro['cadena'] = $cadena;
                            
                            break;
                          
                        #############################################################################################
                        # INSRED
                        #############################################################################################                        
                        case '99996':
                            
                            $insred_entidad = (isset($INI_FILE['intercambio']['insred_entidad']) && $INI_FILE['intercambio']['insred_entidad'] != 0 ? $INI_FILE['intercambio']['insred_entidad'] : '000');
                            $insred_msg_ticket = (isset($INI_FILE['intercambio']['insred_msg_ticket']) && $INI_FILE['intercambio']['insred_msg_ticket'] != "" ? $INI_FILE['intercambio']['insred_msg_ticket'] : '');
                            $insred_msg_pantalla = (isset($INI_FILE['intercambio']['insred_msg_pantalla']) && $INI_FILE['intercambio']['insred_msg_pantalla'] != "" ? $INI_FILE['intercambio']['insred_msg_pantalla'] : '');
                            $insred_servicio = (isset($INI_FILE['intercambio']['insred_servicio']) && $INI_FILE['intercambio']['insred_servicio'] != "" ? $INI_FILE['intercambio']['insred_servicio'] : '');
                            
                            $campos = array(
                                0 => $insred_entidad,
                                1 => intval($ndoc),
                                2 => intval($socioCuitCuil),
                                3 => date('d/m/Y', strtotime($fechaDebito)),
                                4 => $importeDebito,
                                5 => date('d/m/Y', strtotime($fechaDebito)),
                                6 => $importeDebito,
                                7 => date('d/m/Y', strtotime($fechaDebito)),
                                8 => $importeDebito,
                                9 => $insred_msg_ticket,
                                10 => $insred_msg_pantalla,
                                11 => $cbu,
                                12 => $insred_servicio,
                                13 => date('d/m/Y', strtotime($fechaDebito)),
                                14 => $importeDebito,                                
                            );

                            $cadena = $this->arma_csv_row($campos);                            
                            $registro['cadena'] = $cadena;                            
                            break;
                            

			###############################################################
			#SIN BANCO ESPECIFICADO
			###############################################################
			default:
				$registro['error'] = 1;
				$registro['mensaje'] = "S/BANCO";
				break;
		}

		#VALIDO QUE EL IMPORTE A DEBITAR NO SEA INFERIOR AL MINIMO
		if($registro['error'] == 0 && (round($importeDebito,2) < round($this->impoMinDtoCBU,2))):
			$registro['error'] = 1;
			$registro['mensaje'] = "MINIMO[$" . $this->impoMinDtoCBU . "]";
			$registro['cadena'] = null;
		endif;


		return $registro;

	}


	/**
	 * Valida datos criticos de la cadena de intercambio del banco nacion
	 * 	1	TIPO_REGISTRO (1) VALOR = 2
	 * 	2	SUCURSAL (4)
	 * 	3	SISTEMA (2) CA=CAJA DE AHORRO O CTACTE ESPECIAL | CC=CUENTA CORRIENTE
	 * 	4	NRO_CUENTA (11) 1RA POSICION = 0
	 * 	5	IMPORTE(15) 13 ENTEROS 2 DECIMALES
	 * 	6	FECHA_VTO (8) COMPLETAR CON CEROS PARA ENVIO
	 * 	7	ESTADO (1) VALOR=0 PARA ENVIO
	 * 	8	MOTIVO_RECHAZO (30) VALOR=BLANK PARA ENVIO
	 * 	9	CONCEPTO (10) CONCEPTO DEBITO (envio el ID de la liquidacion_socios)
	 * 	10	FILLER (46) RELLENO (envio el identificador del debito)
	 * @param string $string
	 * @return array
	 */

	function validateStringDetalleDebitoBancoNacion($string,$codigoBanco = '00011'){

		$validado = array('ERROR' => 0, 'MENSAJE' => null);

		$len = $len = strlen(str_replace($this->EOL,"", $string));
		$longRegistro = $this->getLongitudRegistro($codigoBanco,'IN');

		$sucursal = substr($string,1,4);
		$cuenta = substr($string,7,11);
		$importe = substr($string,18,15);
		$idSocio = substr($string,72,10);

		if($len != $longRegistro){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.ERR.LONG.");
			return $validado;
		}
		if(intval($sucursal) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/SUC.");
			return $validado;
		}
		if(intval($cuenta) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/CTA.");
			return $validado;
		}
		if(intval($importe) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/IMP.");
			return $validado;
		}
		if(intval($idSocio) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/ID.");
			return $validado;
		}

//		App::import('Model','pfyj.PersonaBeneficio');
//		$oBEN = new PersonaBeneficio();
//
//		if(!$oBEN->isValidateSocioCuentaSucursal(intval($idSocio), $sucursal, $cuenta)){
//			$validado = array('ERROR' => 1, 'MENSAJE' => "SOCIO ERR CTA/SUC");
//			return $validado;
//		}


		return $validado;

	}


	/**
	 * Valida campos criticos
	 * 	1	TIPO REGISTRO (1) VALOR = 2
	 * 	2	CODIGO TRANSACCION (4) 3700 ORDEN DE DEBITO
	 * 	3	CBU BLOQUE 1 (8)
	 * 	4	CBU BLOQUE 2 (14)
	 * 	5	IDENTIFICADOR UNIVOCO DEL CLIENTE (22)
	 * 	6	MONTO DEL DEBITO (10) 8 + 2 DECIMALES
	 * 	7	IDENTIFICADOR UNIVOCA DEL DEBITO (15)
	 * 	8	FECHA VENCIMIENTO ORIGINAL (8) AAAAMMDD
	 * 	9	FILLER (18) BLANK
	 * @param $string
	 * @param $codigoBanco
	 * @return array
	 */
	function validateStringDetalleDebitoBancoStandarBank($string,$codigoBanco = '00430'){

		$validado = array('ERROR' => 0, 'MENSAJE' => null);

		$len = $len = strlen(str_replace($this->EOL,"", $string));
		$longRegistro = $this->getLongitudRegistro($codigoBanco,'IN');

		$cbu = substr($string,5,22);
		$importe = substr($string,49,10);
		$idSocio = substr($string,27,22);

		if($len != $longRegistro){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.ERR.LONG.");
			return $validado;
		}
		if(intval($cbu) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/CBU.");
			return $validado;
		}
		if(intval($importe) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/IMP.");
			return $validado;
		}
		if(intval($idSocio) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/ID.");
			return $validado;
		}

//		App::import('Model','pfyj.PersonaBeneficio');
//		$oBEN = new PersonaBeneficio();
//
//		if(!$oBEN->isValidateSocioCBU(intval($idSocio), $cbu)){
//			$validado = array('ERROR' => 1, 'MENSAJE' => "SOCIO ERR CBU");
//			return $validado;
//		}

		return $validado;

	}

	/**
	 * Valida campos criticos CREDICOOP
	 * 	1	(3)		CODIGO DE BANCO
	 * 	2	(2)		CODIGO DE REGISTRO (== 51)
	 * 	3	(6)		FECHA VENCIMIENTO (AAMMDD)
	 * 	4	(5) 	EMPRESA SUBEMPRESA (NRO ASIGNADO POR EL BANCO)
	 * 	5	(22) 	IDENTIFICADOR DE DEBITO (ID SOCIO 7 + 15 ESPACIOS RIGHT)
	 * 	6	(1) 	MONEDA (P/D)
	 * 	7	(8) 	BLOQUE_1 CBU
	 * 	8	(14) 	BLOQUE_2 CBU
	 * 	9	(10) 	IMPORTE (8 ENTEROS 2 DECIMALES)
	 * 	10	(11) 	CUIT DE LA EMPRESA
	 * 	11	(10)	DESCRIPCION (CUOTA PTMO)
	 * 	12	(15)	VTO / DOCUMENTO
	 * @param string $string
	 */
	function validateStringDetalleDebitoBancoCrediCoop($string,$codigoBanco = '00191'){
		$validado = array('ERROR' => 0, 'MENSAJE' => null);

		$len = $len = strlen(str_replace($this->EOL,"", $string));
		$longRegistro = $this->getLongitudRegistro($codigoBanco,'IN');

		$codBco = substr($string,0,3);
		$fechaDeb = substr($string,5,6);
		$cbu = substr($string,39,22);

		$importe = substr($string,61,10);
		$idSocio = substr($string,16,22);

		if($len != $longRegistro){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.ERR.LONG.");
			return $validado;
		}
		if($codBco != substr($cbu,0,3)){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/CODBCO.");
			return $validado;
		}
		if(intval($fechaDeb) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/FECD.");
			return $validado;
		}
		if(intval($cbu) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/CBU.");
			return $validado;
		}
		if(intval($importe) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/IMP.");
			return $validado;
		}
		if(intval($idSocio) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/ID.");
			return $validado;
		}

//		App::import('Model','pfyj.PersonaBeneficio');
//		$oBEN = new PersonaBeneficio();
//
//		if(!$oBEN->isValidateSocioCBU(intval($idSocio), $cbu)){
//			$validado = array('ERROR' => 1, 'MENSAJE' => "SOCIO ERR CBU");
//			return $validado;
//		}
		return $validado;
	}


	/**
	 * VALIDA CAMPOS CRITICOS BANCO CORDOBA
	 * 	1	TIPO_CONVENIO (3)
	 * 	2	SUCURSAL (5)
	 * 	3	MONEDA (01 = PESOS)
	 * 	4	SISTEMA (1) (1 - CTA.CTE. / 3 - CAJA DE AHORRO)
	 * 	5	NUMERO DE CUENTA (9)
	 * 	6	IMPORTE (18) (16 + 2 DECIMALES)
	 * 	7	FECHA A DEBITAR (8) (AAAAMMDD)
	 * 	8	NUMERO DE EMPRESA (5)
	 * 	9	NUMERO DE COMPROBANTE (6)
	 * 	10	NRO DE CBU (22)
	 * 	11	NUMERO CUOTA (2) (SI VAN DOS O MAS REGISTROS POR CBU DIFERENCIAR POR NUMERO DE CUOTA)
	 * 	12	IDENTIFICADOR UNIVOCO DEL DEBITO (22)
	 * @param array $campos
	 * @return string
	 */

	function validateStringDetalleDebitoBancoCordoba($string,$codigoBanco = '00020',$controlSucursalOtrosBanco = false){

		$validado = array('ERROR' => 0, 'MENSAJE' => null);

		$len = $len = strlen(str_replace($this->EOL,"", $string));
		$longRegistro = $this->getLongitudRegistro($codigoBanco,'IN');
		/*
		 *
		$cadena = "";
		$cadena .= "000";
		$cadena .= str_pad(trim($campos[2]), 5, '0', STR_PAD_LEFT);
		$cadena .= "01";
		$cadena .= "3";
		$nroCuentaBanco = trim($campos[5]);
		if(strlen($nroCuentaBanco) > 9) $nroCuentaBanco = substr($nroCuentaBanco,-9,9);
		$cadena .= str_pad($nroCuentaBanco, 9, '0', STR_PAD_LEFT);
		$importe = number_format($campos[6] * 100,0,"","");
		$cadena .= str_pad($importe, 18, '0', STR_PAD_LEFT);
		$cadena .= date('Ymd',strtotime($campos[7]));

		$DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
		$nroEmpresa = trim($DATOS_GLOBALES['nro_empresa_banco_cordoba']);
		$nroEmpresa = str_pad($nroEmpresa,5,"0",STR_PAD_LEFT);

		$cadena .= (isset($campos[8]) && !empty($campos[8]) ? str_pad(substr(trim($campos[8]),0,5),5,'0',STR_PAD_LEFT) : $nroEmpresa);
		$cadena .= str_pad(trim($campos[9]), 6, '0', STR_PAD_LEFT);;
		$cadena .= str_pad(trim($campos[10]), 22, '0', STR_PAD_LEFT);
		$cadena .= str_pad($campos[11], 2, '0', STR_PAD_LEFT);
		$cadena .= str_pad(trim(substr($campos[12],0,22)), 22, '0', STR_PAD_LEFT);
		$cadena .= "\r\n";
		 */

		$convenio = substr($string,0,3);
		$sucursal = substr($string,3,5);
		$moneda = substr($string,8,2);
		$sistema = substr($string,10,1);
		$cuenta = substr($string,11,9);
		$importe = substr($string,20,18);
		$fecha = substr($string,38,8);
		$empresa = substr($string,46,5);
		$comprobante = substr($string,51,6);
		$cbu = substr($string,57,22);
		$cuota = substr($string,79,2);
		$idSocio = substr($string,81,12);

		if($len != $longRegistro){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.ERR.LONG.");
			return $validado;
		}
		if(intval($convenio) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/CONV.<>0");
			return $validado;
		}
		if(intval($sucursal) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/SUC.");
			return $validado;
		}
		if($moneda != "01"){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/MONEDA.");
			return $validado;
		}
		if($sistema != "3"){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/SISTEMA.");
			return $validado;
		}
		if(intval($cuenta) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/CTA.");
			return $validado;
		}
		if(intval($importe) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/IMP.");
			return $validado;
		}
		if(intval($idSocio) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/ID.");
			return $validado;
		}
		if(intval($cbu) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/CBU.");
			return $validado;
		}
		if(substr($cbu,0,3) != substr($codigoBanco, -3) && !$controlSucursalOtrosBanco){
			$validado = array('ERROR' => 1, 'MENSAJE' => "SUCURSAL <> 800");
			return $validado;
		}

		return $validado;

	}


	/**
         * ARCHIVO GENERICO DEBITO
         * 1    6   SOCIO ID
         * 2    11  DOCUMENTO
         * 3    53  NOMBRE SOCIO
         * 4    12  IMPORTE DEBITO
         * 5    11  FILLER
         * @param type $campos
         * @return string
         */
	function armaStringDebitoMutual($campos,$fechaDebito = NULL, $codigoRendicion = NULL){
//                $campos = array(
//                        1 => $ndoc,
//                        2 => $apenom,
//                        3 => $importeDebito,
//                        4 => $socioId,
//                        5 => $fila['status'].date('Ymd',strtotime($fechaDebito)),
//                );
		$cadena = "";
		$cadena .= str_pad($campos[4],6,'0', STR_PAD_LEFT);
		$cadena .= str_pad($campos[1],11,'0', STR_PAD_LEFT);
		$cadena .= str_pad(substr($campos[2],0,53),53,' ', STR_PAD_RIGHT);
		$importe = number_format($campos[3] * 100,0,"","");
		$cadena .= str_pad($importe, 12, '0', STR_PAD_LEFT);
		$cadena .= str_pad((isset($campos[5]) && !empty($campos[5]) ? trim($campos[5]) : ""),11,' ',STR_PAD_LEFT);
// 		if(!empty($codigoRendicion)) {
// 		    $cadena .= $codigoRendicion;
// 		    $cadena .= date("Ymd", strtotime($fechaDebito));
// 		}
		$cadena .= "\r\n";
		return $cadena;
	}

	/**
         * ARCHIVO ZENRISE
         * 1    6   SOCIO ID
         * 2    11  DOCUMENTO
         * 3    53  NOMBRE SOCIO
         * 4    12  IMPORTE DEBITO
         * 5    11  FILLER
         * @param type $campos
         * @return string
         */

	//FUNCIÓN PARA CONVERTIR EL EXCEL QUE VIENE DE ZENRISE Y PROCESAR EN EL SISTEMA CON FORMATO TXT
	function armaStringZenrise($campos){

		$cadena = "";
		$cadena .= substr(str_pad($campos[0],20,' ',STR_PAD_RIGHT), 0,20);//nombre
		$cadena .= substr(str_pad($campos[1],20,' ',STR_PAD_RIGHT), 0,20);//email
		$cadena .= substr(str_pad($campos[2],40,' ',STR_PAD_RIGHT),0,40);//descripcion
		$cadena .= substr(str_pad($campos[3],8,' ',STR_PAD_RIGHT),0,8);//ref ext contacto
		$cadena .= substr(str_pad($campos[4],22,' ',STR_PAD_RIGHT),0,22);//ref ext factura
		$monto_a_pagar = number_format($campos[5] * 100,0,"","");
		$monto_pagado = number_format($campos[6] * 100,0,"","");
		
		$cadena .= str_pad($monto_a_pagar, 14, '0', STR_PAD_LEFT);
		$cadena .= str_pad($monto_pagado, 14, '0', STR_PAD_LEFT);
		
		$cadena .= substr(str_pad($campos[7],10,' ',STR_PAD_RIGHT),0,10);//medio de pago
		$cadena .= substr(str_pad($campos[8],10,' ',STR_PAD_RIGHT),0,10);//1ra fecha venc
		$cadena .= substr(str_pad($campos[9],10,' ',STR_PAD_RIGHT),0,10);//2da fecha venc
		$cadena .= substr(str_pad($campos[10],10,' ',STR_PAD_RIGHT),0,10);//fecha de pago
		$cadena .= substr(str_pad($campos[11],10,' ',STR_PAD_RIGHT),0,10);//fecha estimada
		$cadena .= substr(str_pad($campos[12],10,' ',STR_PAD_RIGHT),0,10);//nombre grupos
		$cadena .= substr(str_pad($campos[13],10,' ',STR_PAD_RIGHT),0,10);//estados
		$cadena .= "\r\n";
		return $cadena;
	}

        /**
         * ARCHIVO GENERICO DEBITO
         * 1    6   SOCIO
         * 2    11  NRO DE DOCUMENTO
         * 3    53  NOMBRE SOCIO
         * 4    12  IMPORTE 10 E, 2D
         * 5    3   CODIGO RENDICION
         * 6    8   FECHA DEBITO
         * @param type $string
         * @return type
         */
	function decodeStringDebitoMutual($string){
		$campos = array();
		$campos['socio_id'] = intval(substr($string,0,6));
		$campos['documento'] = substr(substr($string,6,11),-8);
		$campos['apenom'] = substr($string,17,53);
		$campos['importe_debitado'] = number_format(intval(substr($string,70,12)) / pow(10,2),2,".","");
		$campos['codigo_rendicion'] = substr($string,82,3);
		$campos['fecha_debito'] = parent::strToDate(substr($string,85,8));
		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();
		$campos['status'] = (!empty($campos['codigo_rendicion']) ? $campos['codigo_rendicion'] : "ERR");
		$campos['indica_pago'] = $oCODIGO->isCodigoPago("99999",$campos['status']);
		return $campos;
	}


	/**
	 * Arma string para envio de información al banco SANTANDER RIO
	 * 	1	(2)		FIJO "11"
	 * 	2	(10)	CODIGO DE SERVICIO DEFINIDO POR EL BANCO ("CUENTA")
	 * 	3	(22)	PARTIDA - NUMERO DE CLIENTE DEFINIDO POR LA EMPRESA (SOCIO_ID)
	 * 	4	(22) 	CBU
	 * 	5	(8) 	FECHA DE DEBITO
	 * 	6	(16) 	IMPORTE DEBITO 14 ENTEROS + 2 DECIMALES
	 * 	7	(15) 	REFERENCIA UNIVOCA DEL DEBITO (SOCIO_ID + LIQUIDACION_ID + ID DEBITO)
	 * 	8	(30) 	NOMBRE DEL CLIENTE
	 * 	9	(1) 	FILLER (ESPACIO)
	 * 	10	(127) 	DATO COMPLEMENTARIO O ESPACIOS EN BLANCO
	 * @param array $campos
	 */

	function armaStringDebitoBancoSantanderRio($campos,$prefix='santander'){

        // $campos = array(
        //     1 => $cbu,
        //     2 => $importeDebito,
        //     3 => $socioId,
        //     4 => $liquidacionSocioId,
        //     5 => $liquidacionID,
        //     6 => strtoupper($apenom),
        //     7 => $fechaDebito,
        // );        

            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');

		//armo la partida campo[3] (socio_id) con el formato ####
            $longitud =  (isset($DATOS_GLOBALES[$prefix.'_long_partida']) && $DATOS_GLOBALES[$prefix.'_long_partida'] > 0 ? $DATOS_GLOBALES[$prefix.'_long_partida'] : 11);
            $PARTIDA = str_pad($campos[3], $longitud, '0', STR_PAD_LEFT);
            $ID_DEBITO = str_pad($campos[5], 5, '0', STR_PAD_LEFT).str_pad($campos[4], 10, '0', STR_PAD_LEFT);
// 		$ID_DEBITO = str_pad($campos[4], 10, '0', STR_PAD_LEFT);


            $concepto = (isset($DATOS_GLOBALES[$prefix.'_descripcion']) && !empty($DATOS_GLOBALES[$prefix.'_descripcion']) ? $DATOS_GLOBALES[$prefix.'_descripcion'] : "CUOTA");

            $cadena = "11";
            $cadena .= str_pad($concepto, 10, ' ', STR_PAD_RIGHT);
            $cadena .= str_pad($PARTIDA,22,' ',STR_PAD_RIGHT);
            $cadena .= str_pad($campos[1], 22, ' ', STR_PAD_RIGHT);
            $cadena .= date("Ymd",strtotime($campos[7]));
            $cadena .= str_pad(number_format($campos[2] * 100,0,"",""), 16, '0', STR_PAD_LEFT);
            $cadena .= str_pad($ID_DEBITO,15,' ',STR_PAD_RIGHT);
            $cadena .= str_pad(substr(trim(utf8_decode($campos[6])),0,30), 30, ' ', STR_PAD_RIGHT);
            $cadena .= " ";
            $cadena .= str_pad("", 50, ' ', STR_PAD_RIGHT);
            $cadena .= "\r\n";
            return $cadena;

	}

	/**
	 * Valida campos criticos CREDICOOP
	 * 	1	(2)		FIJO "11"
	 * 	2	(10)	CODIGO DE SERVICIO DEFINIDO POR EL BANCO ("CUENTA")
	 * 	3	(22)	PARTIDA - NUMERO DE CLIENTE DEFINIDO POR LA EMPRESA (SOCIO_ID)
	 * 	4	(22) 	CBU
	 * 	5	(8) 	FECHA DE DEBITO
	 * 	6	(16) 	IMPORTE DEBITO 14 ENTEROS + 2 DECIMALES
	 * 	7	(15) 	REFERENCIA UNIVOCA DEL DEBITO (SOCIO_ID + LIQUIDACION_ID + ID DEBITO)
	 * 	8	(30) 	NOMBRE DEL CLIENTE
	 * 	9	(1) 	FILLER (ESPACIO)
	 * 	10	(127) 	DATO COMPLEMENTARIO O ESPACIOS EN BLANCO
	 * @param string $string
	 */
	function validateStringDetalleDebitoBancoSantanderRio($string,$codigoBanco = '00072'){

		$validado = array('ERROR' => 0, 'MENSAJE' => null);

// 		$len = strlen(str_replace($this->EOL,"", $string));
		$len = strlen($string);
		$longRegistro = $this->getLongitudRegistro($codigoBanco,'IN');

// 		debug($longRegistro . " ** " . $len);

		$D1 = substr($string,0,2);
		$D2 = trim(substr($string,2,10));

		$idSocio = intval(trim(substr($string,12,22)));
		$cbu = trim(substr($string,34,22));
		$fechaDeb = trim(substr($string,56,8));
		$importe = trim(substr($string,64,16));
		$lsid = trim(substr($string,80,15));
		$apenom = trim(substr($string,95,30));


		if($len != $longRegistro){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.ERR.LONG.");
			return $validado;
		}
		if(intval($fechaDeb) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/FECD.");
			return $validado;
		}
		if(intval($cbu) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/CBU.");
			return $validado;
		}
		if(intval($importe) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/IMP.");
			return $validado;
		}
		if(intval($idSocio) == 0){
			$validado = array('ERROR' => 1, 'MENSAJE' => "CAD.S/ID.");
			return $validado;
		}
		return $validado;
	}

	/**
	 * Descompone la cadena de rendicion del bco SANTANDER RIO
	 * 	1	(2)	TIPO SERVICIO (==21)
	 * 	2	(10)	DESCRIPCION DEL SERVICIO
	 * 	3	(22)	NRO DE CLIENTE (ID SOCIO)
	 * 	4	(22) 	CBU
	 * 	5	(8) 	FECHA VENCIMIENTO AAAAMMDD
	 * 	6	(16) 	IMPORTE DEBITO (14 E + 2 D)
	 * 	7	(15) 	IDENTIFICADOR UNIVOCO DEL DEBITO
	 * 	8	(3) 	CODIGO DE RENDICION
	 * 	9	(50) 	REFERENCIA EMPRESA
	 *
	 * @param string $string
	 * @return array $campos
	 */
	function decodeStringDebitoBancoSantanderRio($string){

		$campos = array();
		$campos['id_registro'] 			= substr($string,0,2);
		$campos['servicio'] 			= substr($string,2,10);
		$campos['socio_id'] 			= substr($string,12,22);
		$campos['cbu'] 				= substr($string,34,22);
		$campos['fecha_vto']			= substr($string,56,8);
		$campos['importe_debitado']		= substr($string,64,16);
		$campos['id_univ'] 			= substr($string,80,15);
		$campos['status']			= substr($string,95,3);
		$campos['referencia'] 			= substr($string,98,50);

// 		//campos adicionales
		$campos['fecha_debito'] = date('Y-m-d',mktime(0, 0, 0, substr($campos['fecha_vto'],4,2), substr($campos['fecha_vto'],6,2), substr($campos['fecha_vto'],0,4)));
		$campos['socio_id'] = intval(trim($campos['socio_id']));
// 		$campos['liquidacion_socio_id'] = intval(trim($campos['vto_docu']));
		$campos['importe_debitado'] = intval(trim($campos['importe_debitado'])) / pow(10,2);


		$campos['indica_pago'] = 0;

		$campos['status'] = str_pad(trim($campos['status']),3,'0',STR_PAD_LEFT);

		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();

		$campos['indica_pago'] = ($oCODIGO->isCodigoPago("00072",(!empty($campos['status']) ? $campos['status'] : "ERR")) ? 1 : 0);

// 		$campos['cbu'] = $campos['cbu_b1'].$campos['cbu_b2'];
		$cbuDecode = $this->deco_cbu($campos['cbu']);
		$campos['banco_id'] = $cbuDecode['banco_id'];
		$campos['sucursal'] = (isset($cbuDecode['sucursal']) ? $cbuDecode['sucursal'] : "");
		$campos['tipo_cta_bco'] = (isset($cbuDecode['tipo_cta_bco']) ? $cbuDecode['tipo_cta_bco'] : "");
		$campos['nro_cta_bco'] = (isset($cbuDecode['nro_cta_bco']) ? $cbuDecode['nro_cta_bco'] : "");

		//armo el arreglo de datos definitivo que uso para insertar
		$datos = array();
		$datos['socio_id'] = $campos['socio_id'];
		$datos['banco_id'] = $campos['banco_id'];
		$datos['sucursal'] = $campos['sucursal'];
		$datos['tipo_cta_bco'] = $campos['tipo_cta_bco'];
		$datos['nro_cta_bco'] = $campos['nro_cta_bco'];
		$datos['cbu'] = $campos['cbu'];
		$datos['importe_debitado'] = $campos['importe_debitado'];
		$datos['status'] = $campos['status'];
		$datos['indica_pago'] = $campos['indica_pago'];
		$datos['fecha_debito'] = $campos['fecha_debito'];
                $datos['id_univ'] = $campos['id_univ'];

		return $datos;

	}

	/**
	 * GENERA CADENA PARA BANCO GALICIA
	 * tipo lote: CABECERA - DETALLE - PIE
	 * Diseño de DETALLE
	 *
	 * 1	4	TIPO_REGISTRO	(0370 = ORDEN DE DEBITO ENVIADO POR LA EMPRESA)
	 * 2	22	ID_CLIENTE
	 * 3	8	BLOQUE_1 CBU
	 * 4	1	DIGITO_CBU_1
	 * 5	16	BLOQUE_2 CBU
	 * 6	1	DIGITO_CBU 2
	 * 7	15	REFERENCIA UNIVOCA
	 * 8	8	VTO ORIGINAL (AAAAMMDD)
	 * 9	14	IMPORTE ORIGINAL (12 ENTEROS, 2 DECIMALES)
	 * 10	8	FECHA 2DO VTO (AAAAMMDD)
	 * 11	12	IMPORTE 2DO VTO (12 ENTEROS, 2 DECIMALES)
	 * 12	8	FECHA 3ER VTO (AAAAMMDD)
	 * 13	12	IMPORTE 3ER VTO (12 ENTEROS, 2 DECIMALES)
	 * 14	1	MONEDA FACTURA (0 = PESOS, 1 = DOLARES)
	 * 15	3	MOTIVO RECHAZO
	 * 16	4 	TDOC
	 * 17	11	NDOC
	 * 18	22 	NUEVA ID DEL CLIENTE (ALINEADO IZQUIERDA CON ESPACIOS A LA DERECHA)
	 * 19	26	NUEVO CBU
	 * 20	14	IMPORTE MINIMO (12 E, 2 D)
	 * 21	8	FECHA PROXIMO VENCIMIENTO (AAAAMMDD)
	 * 22	22	ID DEL CLIENTE ANTERIOR
	 * 23	40	MENSAJE ATM
	 * 24	10	CONCEPTO FACTURA
	 * 25	8	FECHA COBRO (AAAAMMDD)
	 * 26	14	IMPORTE COBRADO (12E,2D)
	 * 27	8	FECHA ACREDITAMIENTO (AAAAMMDD)
	 * 28	26	LIBRE (ESPACIOS EN BLANCO)
	 * @param unknown $campos
	 */
	function armaStringDebitoBancoGalicia($campos){

//                $campos = array(
//                                1 => $socioId,
//                                2 => $cbu,
//                                7 => str_pad($liquidacionID,5,"0",STR_PAD_LEFT).str_pad($registroNro,3,"0",STR_PAD_LEFT),
//                                8 => $fechaDebito,
//                                9 => $importeDebito,
//                                10 => $liquidacionID,
//                                11 => $liquidacionSocioId,
//                );


//				$campos = array(
//                                        1 => $socioId,
//                                        2 => $cbu,
//                                        7 => $liquidacionID,
//                                        8 => $fechaDebito,
//                                        9 => $importeDebito,
//                                        10=> $liquidacionID,
//                                        11 => $liquidacionSocioId,
//                                        12 => $idDebito,
//                                        13 => str_pad($liquidacionID,5,"0",STR_PAD_LEFT).str_pad($registroNro,3,"0",STR_PAD_LEFT),
//				);

		$decoCBU = $this->deco_cbu($campos[2]);
		$bloque_CBU_1 = "0".substr(trim($campos[2]),0,7);
		$bloque_CBU_1_dv = $decoCBU['digito_1'];
		$bloque_CBU_2 = "000".substr(trim($campos[2]),8,13);
		$bloque_CBU_2_dv = $decoCBU['digito_2'];

		$cadena = "0370";
        $ID_CLIENTE = str_pad(trim($campos[10]),5,'0',STR_PAD_LEFT).str_pad(trim($campos[1]),6,'0',STR_PAD_LEFT);
//        $REF_UNIVOCA = str_pad(trim($campos[1]),7,'0',STR_PAD_LEFT).trim($campos[7]);
        $REF_UNIVOCA = str_pad(trim($campos[1]),7,'0',STR_PAD_LEFT).trim($campos[13]);

		$cadena .= str_pad($ID_CLIENTE,22,' ',STR_PAD_RIGHT); //id del cliente
		$cadena .= $bloque_CBU_1; //cbu 8
		$cadena .= $bloque_CBU_1_dv; //cbu 1
		$cadena .= $bloque_CBU_2; //cbu 16
		$cadena .= $bloque_CBU_2_dv; //cbu 1
                $cadena .= str_pad($REF_UNIVOCA,15,' ',STR_PAD_RIGHT); //liquidacionID
//		$cadena .= str_pad(trim($campos[7]),15,' ',STR_PAD_RIGHT); //liquidacionID
		$cadena .= date("Ymd",strtotime($campos[8])); //1er vto
		$cadena .= str_pad(number_format($campos[9] * 100,0,"",""), 14, '0', STR_PAD_LEFT); //importe 1er vto
		$cadena .= str_pad("",8," ",STR_PAD_RIGHT); //2do vto
		$cadena .= str_pad("",14," ",STR_PAD_RIGHT); //imp 2do vto
		$cadena .= str_pad("",8," ",STR_PAD_RIGHT); //3er vto
		$cadena .= str_pad("",14," ",STR_PAD_RIGHT); //imp 3er vto
		$cadena .= "0"; //moneda
		$cadena .= str_pad("",3," ",STR_PAD_RIGHT); //codigo rendicion
		$cadena .= str_pad("",4," ",STR_PAD_RIGHT);; //tipo documento
		$cadena .= str_pad("",11," ",STR_PAD_RIGHT); //nro documento
		$cadena .= str_pad("",22," ",STR_PAD_RIGHT); //nueva id del cliente
		$cadena .= str_pad("",26," ",STR_PAD_RIGHT); // nueva CBU
		$cadena .= str_pad("",14," ",STR_PAD_RIGHT); //importe minimo
		$cadena .= str_pad("",8," ",STR_PAD_RIGHT); //fecha proximo vto
		$cadena .= str_pad("",22," ",STR_PAD_RIGHT); //id anterior del cliente
		$cadena .= str_pad("",40," ",STR_PAD_RIGHT); //mensaje ATM
		$cadena .= str_pad($campos[11],10," ",STR_PAD_RIGHT); //concepto factura
		$cadena .= str_pad("",8," ",STR_PAD_RIGHT); // fecha cobro
		$cadena .= str_pad("",14," ",STR_PAD_RIGHT); //importe cobrado
		$cadena .= str_pad("",8," ",STR_PAD_RIGHT); // fecha acreditacion
		$cadena .= str_pad("",26," ",STR_PAD_RIGHT); // filler
		$cadena .= "\r\n";
		return $cadena;


	}

	/**
	 * REGISTRO CABECERA / PIE BANCO GALICIA
	 *
	 * 1	4	TIPO_REGISTRO (0000 = CABECERA / 9999 = PIE)
	 * 2	4	NRO DE PRESTACION  (ASIGNADO POR EL BANCO)
	 * 3	1	SERVICIO (D=DEBITO AUTOMATICO, P=PAGO AUTOMATICO, C=SISTEMA NACIONAL DE PAGOS)
	 * 4	8	FECHA_GENERACION (AAAAMMDD)
	 * 5	1	ID DE ARCHIVO (NO SE PUEDE REPETIR PARA UNA MISMA PRESTACION, SERVICIO Y FECHA GENERACION
	 * 6	7	ORIGEN (EMPRESA o BANCO)
	 * 7	14	IMPORTE TOTAL (12 ENTEROS 2 DECIMALES)
	 * 8	7	CANTIDAD DE REGISTROS DETALLE
	 * 9	304	LIBRE (ESPACIOS EN BLANCO)
	 * @param unknown $campos
	 */
	function armaStringCabeceraPieDebitoBancoGalicia($campos,$tipo='C'){
        /*
			$tmp = array();
			$tmp[2] = "9866";
			$tmp[4] = date('Ymd',strtotime($fechaPresentacion));
			$tmp[5] = (empty($nroArchivo) ? 1 : $nroArchivo);
			$tmp[7] = str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 14, '0', STR_PAD_LEFT);
			$tmp[8] = str_pad(intval($registros),7,'0',STR_PAD_LEFT);        
        */
		$cadena = ($tipo == 'C' ? "0000" : "9999");
		$cadena .= str_pad(trim($campos[2]),4,'0',STR_PAD_RIGHT); //nro de prestacion
		$cadena .= "C";
		$cadena .= date("Ymd",strtotime($campos[4])); //fecha generacion
		$cadena .= str_pad(trim($campos[5]),1,'0',STR_PAD_RIGHT); // id de archivo
		$cadena .= "EMPRESA"; // ORIGEN
		$cadena .= str_pad(number_format($campos[7] * 100,0,"",""), 14, '0', STR_PAD_LEFT); //importe TOTAL
		$cadena .= str_pad(trim($campos[8]),7,'0',STR_PAD_LEFT);
		$cadena .= str_pad("",304,' ',STR_PAD_RIGHT); // filler
		$cadena .= "\r\n";
		return $cadena;
	}



	/**
	 * GENERA CADENA PARA BANCO GALICIA
	 * tipo lote: CABECERA - DETALLE - PIE
	 * Diseño de DETALLE
	 *
	 * 1	4	TIPO_REGISTRO	(0370 = ORDEN DE DEBITO ENVIADO POR LA EMPRESA)
	 * 2	22	ID_CLIENTE
	 * 3	8	BLOQUE_1 CBU
	 * 4	1	DIGITO_CBU_1
	 * 5	16	BLOQUE_2 CBU
	 * 6	1	DIGITO_CBU 2
	 * 7	15	REFERENCIA UNIVOCA
	 * 8	8	VTO ORIGINAL (AAAAMMDD)
	 * 9	14	IMPORTE ORIGINAL (12 ENTEROS, 2 DECIMALES)
	 * 10	8	FECHA 2DO VTO (AAAAMMDD)
	 * 11	12	IMPORTE 2DO VTO (12 ENTEROS, 2 DECIMALES)
	 * 12	8	FECHA 3ER VTO (AAAAMMDD)
	 * 13	12	IMPORTE 3ER VTO (12 ENTEROS, 2 DECIMALES)
	 * 14	1	MONEDA FACTURA (0 = PESOS, 1 = DOLARES)
	 * 15	3	MOTIVO RECHAZO
	 * 16	4 	TDOC
	 * 17	11	NDOC
	 * 18	22 	NUEVA ID DEL CLIENTE (ALINEADO IZQUIERDA CON ESPACIOS A LA DERECHA)
	 * 19	26	NUEVO CBU
	 * 20	14	IMPORTE MINIMO (12 E, 2 D)
	 * 21	8	FECHA PROXIMO VENCIMIENTO (AAAAMMDD)
	 * 22	22	ID DEL CLIENTE ANTERIOR
	 * 23	40	MENSAJE ATM
	 * 24	10	CONCEPTO FACTURA
	 * 25	8	FECHA COBRO (AAAAMMDD)
	 * 26	14	IMPORTE COBRADO (12E,2D)
	 * 27	8	FECHA ACREDITAMIENTO (AAAAMMDD)
	 * 28	26	LIBRE (ESPACIOS EN BLANCO)
	 * @param string $string
	 */
	function decodeStringDebitoBancoGalicia($string){

        $campos = array();
        $campos['tipo_registro']    = substr($string,0,4);
        $campos['id_cliente']       = substr($string,4,22);
        $campos['cbu_1n']           = substr($string,26,8);
        $campos['cbu_1d']           = substr($string,34,1);
        $campos['cbu_2n']           = substr($string,35,16);
        $campos['cbu_2d']           = substr($string,51,1);
        $campos['ref_univ']         = substr($string,52,15);
        $campos['fec_vto_1']        = substr($string,67,8);
        $campos['imp_vto_1']        = substr($string,75,14);
        $campos['fec_vto_2']        = substr($string,89,8);
        $campos['imp_vto_2']        = substr($string,97,14);
        $campos['fec_vto_3']        = substr($string,111,8);
        $campos['imp_vto_3']        = substr($string,119,14);
        $campos['moneda_fac']       = substr($string,133,1);
        $campos['motivo_rech']      = substr($string,134,3);
        $campos['tdoc']             = substr($string,137,4);
        $campos['ndoc']             = substr($string,141,11);
        $campos['nvo_id_cliente']   = substr($string,152,22);
        $campos['nvo_cbu']          = substr($string,174,26);
        $campos['impo_min']         = substr($string,100,14);
        $campos['fec_prox_vto']     = substr($string,214,8);
        $campos['id_client_ant']    = substr($string,222,22);
        $campos['msj_atm']          = substr($string,244,40);
        $campos['concep_fact']      = substr($string,284,10);
        $campos['fecha_cobro']      = substr($string,294,8);
        $campos['importe_cobro']    = substr($string,302,14);
        $campos['fecha_acredita']   = substr($string,316,8);
        $campos['filler']           = substr($string,324,26);

		$campos['fecha_debito'] = date('Y-m-d',mktime(0, 0, 0, substr($campos['fecha_cobro'],4,2), substr($campos['fecha_cobro'],6,2), substr($campos['fecha_cobro'],0,4)));
		$campos['socio_id'] = intval(substr(trim($campos['id_cliente']),-6));
		$campos['importe_debitado'] = intval(trim($campos['importe_cobro'])) / pow(10,2);


		$campos['indica_pago'] = 0;
        $campos['cbu'] = substr($campos['cbu_1n'].$campos['cbu_1d'],-8).substr($campos['cbu_2n'].$campos['cbu_2d'],-14);

		$campos['status'] = str_pad(trim($campos['motivo_rech']),3,'0',STR_PAD_LEFT);

		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();

		$campos['indica_pago'] = ($oCODIGO->isCodigoPago("00007",(!empty($campos['status']) ? $campos['status'] : "ERR")) ? 1 : 0);

        if($campos['indica_pago'] == 0){
            $campos['importe_debitado'] = intval(trim($campos['imp_vto_1'])) / pow(10,2);
        }

// 		$campos['cbu'] = $campos['cbu_b1'].$campos['cbu_b2'];
		$cbuDecode = $this->deco_cbu($campos['cbu']);
		$campos['banco_id'] = $cbuDecode['banco_id'];
		$campos['sucursal'] = (isset($cbuDecode['sucursal']) ? $cbuDecode['sucursal'] : "");
		$campos['tipo_cta_bco'] = (isset($cbuDecode['tipo_cta_bco']) ? $cbuDecode['tipo_cta_bco'] : "");
		$campos['nro_cta_bco'] = (isset($cbuDecode['nro_cta_bco']) ? $cbuDecode['nro_cta_bco'] : "");

		$datos = array();
		$datos['socio_id'] = $campos['socio_id'];
		$datos['banco_id'] = $campos['banco_id'];
		$datos['sucursal'] = $campos['sucursal'];
		$datos['tipo_cta_bco'] = $campos['tipo_cta_bco'];
		$datos['nro_cta_bco'] = $campos['nro_cta_bco'];
		$datos['cbu'] = $campos['cbu'];
		$datos['importe_debitado'] = $campos['importe_debitado'];
		$datos['status'] = $campos['status'];
		$datos['indica_pago'] = $campos['indica_pago'];
		$datos['fecha_debito'] = $campos['fecha_debito'];
                $datos['ref_univ'] = $campos['ref_univ'];

                //$campos['id_cliente']
                $datos['liquidacion_id'] = intval(substr($campos['id_cliente'],0,5));

		return $datos;

    }

    /**
     * GENERA CADENA PARA ZENRISE
     * tipo lote: CABECERA - DETALLE - PIE
     * Diseño de DETALLE
     *
     * 1	4	TIPO_REGISTRO	(0370 = ORDEN DE DEBITO ENVIADO POR LA EMPRESA)
     * 2	22	ID_CLIENTE
     * 3	8	BLOQUE_1 CBU
     * 4	1	DIGITO_CBU_1
     * 5	16	BLOQUE_2 CBU
     * 6	1	DIGITO_CBU 2
     * 7	15	REFERENCIA UNIVOCA
     * 8	8	VTO ORIGINAL (AAAAMMDD)
     * 9	14	IMPORTE ORIGINAL (12 ENTEROS, 2 DECIMALES)
     * 10	8	FECHA 2DO VTO (AAAAMMDD)
     * 11	12	IMPORTE 2DO VTO (12 ENTEROS, 2 DECIMALES)
     * 12	8	FECHA 3ER VTO (AAAAMMDD)
     * 13	12	IMPORTE 3ER VTO (12 ENTEROS, 2 DECIMALES)
     * 14	1	MONEDA FACTURA (0 = PESOS, 1 = DOLARES)
     * 15	3	MOTIVO RECHAZO
     * 16	4 	TDOC
     * 17	11	NDOC
     * 18	22 	NUEVA ID DEL CLIENTE (ALINEADO IZQUIERDA CON ESPACIOS A LA DERECHA)
     * 19	26	NUEVO CBU
     * 20	14	IMPORTE MINIMO (12 E, 2 D)
     * 21	8	FECHA PROXIMO VENCIMIENTO (AAAAMMDD)
     * 22	22	ID DEL CLIENTE ANTERIOR
     * 23	40	MENSAJE ATM
     * 24	10	CONCEPTO FACTURA
     * 25	8	FECHA COBRO (AAAAMMDD)
     * 26	14	IMPORTE COBRADO (12E,2D)
     * 27	8	FECHA ACREDITAMIENTO (AAAAMMDD)
     * 28	26	LIBRE (ESPACIOS EN BLANCO)
     * @param string $string
     */
    function decodeStringDebitoZenrise($string){

        $campos = array();
        $campos['nombre']  			= substr($string,0,20); //NOMBRE
        $campos['email']    		= substr($string,20,20); //EMAIL
        $campos['descripcion']      = substr($string,40,40); //DESCRIPCION
        $campos['ref_factura']      = substr($string,80,8); //REF EXT CONTACTO
        $campos['referencia']      	= substr($string,88,22); //REF EXT FACTURA
        $campos['importe_cobro']    = number_format(intval(substr($string,110,14)) / pow(10,2),2,".","");  // substr($string,110,14); //MONTO A PAGAR
        $campos['importe_debitado'] = number_format(intval(substr($string,124,14)) / pow(10,2),2,".",""); //MONTO PAGADO
        $campos['medio_pago']       = substr($string,138,10); //MEDIO DE PAGO
        $campos['1ra_fecha_venc']   = substr($string,148,10); //1RA FECHA VENC
        $campos['2da_fecha_venc']   = substr($string,158,10); //2DA FECHA VENC
        $campos['fecha_debito']     = substr($string,168,10); //FECHA DE PAGO
        $campos['fecha_estimada']   = substr($string,178,10); //FECHA ESTIMADA TRANSFERENCIA
        $campos['nombre_grupos']    = substr($string,188,10); //NOMBRE GRUPOS
        $campos['estado']           = substr($string,198,10); //status
        
        $campos['socio_id'] 		= intval(substr($campos['referencia'],0,12));
        
        #ESTADOS
        switch ($campos['estado']) {
            case 'PENDIENTE':
                $campos['indica_pago'] 		= 0;
                $campos['status'] 			= '000';
                break;
            case 'DEVOLUCION':
                $campos['indica_pago'] 		= 0;
                $campos['status'] 			= 'DEV';
                break;
            default:                 
                $campos['indica_pago'] 		= 1;
                $campos['status'] 			= '001';
        }

        if ($campos['importe_debitado'] == 0) {
            $campos['status'] = '000';
            $campos['indica_pago'] = 0;
        }

        $datos = array();
        $datos['socio_id'] = $campos['socio_id'];
        $datos['banco_id'] = '99998';
        $datos['importe_debitado'] = $campos['importe_debitado'];
        $datos['status'] = $campos['status'];
        $datos['indica_pago'] = $campos['indica_pago'];
        $datos['fecha_debito'] = $campos['fecha_debito'];
        $datos['ref_univ'] = $campos['referencia'];
        $liqId = intval(substr($campos['referencia'],12,8));
        if(!empty($liqId)){
            $datos['liquidacion_id'] = $liqId;
        }
        
        return $datos;


    }

    function armaStringLoteIntercambioGeneral($campos){
        $cadena = $campos[0];

        if($campos[0] == 1){
//            $lote['cabecera'] .= "1";
//            $lote['cabecera'] .= trim($cuit);
//            $lote['cabecera'] .= date('Ymd');
//            $lote['cabecera'] = str_pad($lote['cabecera'],150,' ',STR_PAD_RIGHT);
            $cadena .= str_pad(trim((isset($campos[1]) ? $campos[1] : 0)),11,0,STR_PAD_LEFT);
            $cadena .= date('Ymd');
            $cadena = str_pad($cadena,150,' ',STR_PAD_RIGHT);
            $cadena .= "\r\n";
        }

        if($campos[0] == 2){
            $cadena .= str_pad(trim((isset($campos[1]) ? $campos[1] : 0)),10,0,STR_PAD_LEFT);
            $cadena .= str_pad(trim($campos[2]),8,0,STR_PAD_LEFT);
            $cadena .= str_pad(trim((isset($campos[3]) ? $campos[3] : 0)),10,0,STR_PAD_LEFT);
            $cadena .= str_pad(trim((isset($campos[4]) ? $campos[4] : 0)),3,0,STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[5]) ? $campos[5] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[6]) ? $campos[6] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(number_format($campos[7] * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(trim($campos[8]),46,' ',STR_PAD_RIGHT);
            $cadena .= str_pad(trim($campos[9]),3,0,STR_PAD_LEFT);
            $cadena .= str_pad(trim($campos[10]),1,0,STR_PAD_LEFT);
            $cadena .= date("Ymd",strtotime((isset($campos[11]) ? $campos[11] : date('Y-m-d'))));
            $cadena .= str_pad(number_format((isset($campos[12]) ? $campos[12] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[13]) ? $campos[13] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= "\r\n";
        }

        if($campos[0] == 3){
//            $lote['pie'] = "";
//            $lote['pie'] .= "3" . str_pad(count($lote['detalle']),10,0,STR_PAD_LEFT);
//            $lote['pie'] .= str_pad($indicaPago,10,0,STR_PAD_LEFT);
//            $lote['pie'] .= str_pad($importeCuota * 100,12,0,STR_PAD_LEFT);
//            $lote['pie'] .= str_pad($saldoActual * 100,12,0,STR_PAD_LEFT);
//            $lote['pie'] .= str_pad($importeDebitado * 100,12,0,STR_PAD_LEFT);
//            $lote['pie'] .= str_pad($comisionCobranza * 100,12,0,STR_PAD_LEFT);
//            $lote['pie'] .= str_pad($iva * 100,12,0,STR_PAD_LEFT);
//            $lote['pie'] .= str_pad($NetoProveedor * 100,12,0,STR_PAD_LEFT);
//
//            $lote['pie'] = str_pad($lote['pie'],150,' ',STR_PAD_RIGHT);
            $cadena .= str_pad(trim((isset($campos[1]) ? $campos[1] : 0)),10,0,STR_PAD_LEFT);
            $cadena .= str_pad(trim((isset($campos[2]) ? $campos[2] : 0)),10,0,STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[3]) ? $campos[3] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[4]) ? $campos[4] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[5]) ? $campos[5] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[6]) ? $campos[6] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[7]) ? $campos[7] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena .= str_pad(number_format((isset($campos[8]) ? $campos[8] : 0) * 100,0,"",""), 12, '0', STR_PAD_LEFT);
            $cadena = str_pad($cadena,150,' ',STR_PAD_RIGHT);
            $cadena .= "\r\n";
        }
        return $cadena;
    }

    function decodeStringLoteIntercambioGeneral($string){
        $campos = array();
        $campos['indicador_registro']       = substr($string,0,1);
        $campos['nro_referencia_proveedor'] = substr($string,1,10);
        $campos['documento']                = substr($string,11,8);
        $campos['orden_descuento_id']       = substr($string,19,10);
        $campos['nro_cuota']                = substr($string,29,3);
        $campos['importe']                  = substr($string,32,12);
        $campos['saldo_actual']             = substr($string,44,12);
        $campos['importe_debitado']         = substr($string,56,12);
        $campos['codigo_descripcion']       = substr($string,68,46);
        $campos['codigo']                   = substr($string,114,3);
        $campos['indica_pago']              = substr($string,117,1);
        $campos['fecha_debito']             = substr($string,118,8);
        $campos['comision_cobranza']        = substr($string,126,12);
        $campos['neto_proveedor']           = substr($string,138,12);

        $datos = array();
        $datos['documento'] = str_pad($campos['documento'],8,0,STR_PAD_LEFT);
        $datos['orden_descuento_id'] = intval($campos['nro_referencia_proveedor']);
        $datos['nro_cuota'] = intval($campos['nro_cuota']);
        $datos['fecha_debito'] = date('Y-m-d',mktime(0, 0, 0, substr($campos['fecha_debito'],4,2), substr($campos['fecha_debito'],6,2), substr($campos['fecha_debito'],0,4)));
        App::import('model','mutual.OrdenDescuento');
        $oODTO = new OrdenDescuento();
        $oODTO->unbindModel(array('hasMany' => array('OrdenDescuentoCuota')));
        $orden = $oODTO->read('socio_id',$datos['orden_descuento_id']);
        if(isset($orden['OrdenDescuento']['socio_id']) && !empty($orden['OrdenDescuento']['socio_id'])) $datos['socio_id'] = $orden['OrdenDescuento']['socio_id'];
        else $datos['socio_id'] = null;


        $datos['codigo'] = $campos['codigo'];
        $datos['indica_pago'] = $campos['indica_pago'];
        $datos['importe_debitado'] = ($datos['indica_pago'] == 1 ? intval(trim($campos['importe_debitado'])) / pow(10,2) : intval(trim($campos['saldo_actual'])) / pow(10,2));
        $datos['status'] = $campos['codigo'];

//        debug($campos);

        return $datos;

    }


    /**
     * LOTE DE DATOS BANCO FRANCES
     * Se generan 4 registros por cada debito
     * REGISTRO_1
     *
     *  CODIGO (4) FIX 4210
     *  ID_EMPRESA (5)
     *  LIBRE (2) BLANCOS
     *  ID_BENEFICIARIO (22)
     *  CBU (22)
     *  IMPORTE_ENTERO (13)
     *  IMPORTE_DECIMAL (2)
     *  CODIGO_DEVOLUCION (6) ENVIO COMPLETAR CON BLANCOS
     *  REFERENCIA (22) REFERENCIA DEL DEBITO
     *  FECHA_VTO (8) FECHA DEBITO AAAAMMDD
     *  LIBRE (2) BLANCOS
     *  NRO DE FACTURA (15) PARA CBU FRANCES VAN CEROS, PARA CBU <> FRANCES UN NUMERO <> 0
     *  CODIGO ESTADO DEV. DOMICILIACIONES (1) BLANCO
     *  DESCRIPCION DE LA DEVOLUCION (40) BLANCOS
     *  FILLER (86) BLANCOS
     *
     * REGISTRO_2
     *
     *  CODIGO_REGISTRO (4) FIX 4220
     *  ID_EMPRESA (5)
     *  LIBRE (2) BLANCOS
     *  ID_BENEFICIARIO (22)
     *  NOMBRE_BENEFICIARIO (36)
     *  DOMICILIO (36) BLANCOS
     *  DOMICILIO CONT (36) BLANCOS
     *  FILLER (109) BLANCOS
     *
     * REGISTRO_3

     *  CODIGO (4) FIX 4230
     *  ID_EMPRESA (5)
     *  LIBRE (2) BLANCOS
     *  ID_BENEFICIARIO (22)
     *  LOCALIDAD (36) BLANCOS
     *  PROVINCIA (36) BLANCOS
     *  CODIGO_POSTAL (36) BLANCOS
     *  FILLER (109) BLANCOS
     *
     * REGISTRO_4
     *
     *  CODIGO (4) FIX 4240
     *  ID_EMPRESA (5)
     *  LIBRE (2) BLANCOS
     *  ID_BENEFICIARIO (22)
     *  CONCEPTO_DEBITO (40)
     *  FILLER (177)
     *
     * @param type $campos
     */
    function armaStringDebitoBancoFrances($campos){

//        $campos = array(
//                0 => $idDebito,
//                1 => $registroNro,
//                2 => $apenom,
//                3 => $cbu,
//                4 => $importeDebito,
//                5 => $fechaDebito,
//                6 => $socioId,
//                7 => $liquidacionSocioId,
//                8 => $liquidacionID,
//                9 => $convenioBcoCba,
//                10 => $codigo_empresa,
//                11 => $long_clave,
//                12 => $concepto_debito,
//        );

        #ARMO LA CADENA COMUN A TODOS LOS REGISTROS
//        $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
//        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
//        $codigo_empresa = (isset($INI_FILE['intercambio']['bbva_frances_codigo_empresa']) && $INI_FILE['intercambio']['bbva_frances_codigo_empresa'] != 0 ? $INI_FILE['intercambio']['bbva_frances_codigo_empresa'] : '00000');
//
//        $long_clave = (isset($INI_FILE['intercambio']['bbva_frances_longitud_clave']) && $INI_FILE['intercambio']['bbva_frances_longitud_clave'] != 0 ? $INI_FILE['intercambio']['bbva_frances_longitud_clave'] : 22);
//        $concepto_debito = (isset($INI_FILE['intercambio']['bbva_frances_concepto_debito']) && $INI_FILE['intercambio']['bbva_frances_concepto_debito'] != "" ? $INI_FILE['intercambio']['bbva_frances_concepto_debito'] : 'CUOTA');

        $codigo_empresa = $campos[10];
        $long_clave = $campos[11];
        $concepto_debito = $campos[12];
        
        


        if(isset($campos[9]) && !empty($campos[9]) && is_numeric($campos[9])){
            $codigo_empresa = str_pad($campos[9],5,0,STR_PAD_LEFT);
        }

        $comun = str_pad($codigo_empresa,5,0,STR_PAD_LEFT);
        $comun .= str_pad("",2," ",STR_PAD_LEFT);
        
        $ID_DEBITO = str_pad($campos[6],  intval($long_clave),0,STR_PAD_LEFT);
        $comun .= str_pad($ID_DEBITO,22," ",STR_PAD_RIGHT);

        $cadena = "";

        #REGISTRO_1
        $cadena .= "4210".$comun;
        $cadena .= str_pad($campos[3],22,0,STR_PAD_LEFT);
        $cadena .= str_pad(number_format($campos[4] * 100,0,"",""), 15, '0', STR_PAD_LEFT);
        $cadena .= str_pad("",6," ",STR_PAD_LEFT);

//        $cadena .= str_pad(trim($campos[0]),22," ",STR_PAD_LEFT);

        $cadena .= str_pad(str_pad($campos[6],7,0,STR_PAD_LEFT).str_pad($campos[8],6,0,STR_PAD_LEFT).str_pad($campos[1],2,0,STR_PAD_LEFT),22," ",STR_PAD_RIGHT);


        $cadena .= date("Ymd",strtotime($campos[5]));
        $cadena .= str_pad("",2," ",STR_PAD_LEFT);
        $cadena .= (substr($campos[3],0,3) == '017' ? str_pad("",15,"0",STR_PAD_LEFT) : str_pad($campos[6],7,0,STR_PAD_LEFT).str_pad($campos[8],6,0,STR_PAD_LEFT).str_pad($campos[1],2,0,STR_PAD_LEFT));
        $cadena .= str_pad("",1," ",STR_PAD_LEFT);
        $cadena .= str_pad("",40," ",STR_PAD_LEFT);
        $cadena .= str_pad("",86," ",STR_PAD_LEFT);
        $cadena .= "\r\n";

        #REGISTRO_2
        $cadena .= "4220".$comun;
        $cadena .= substr(str_pad(strtoupper($campos[2]),36,' ',STR_PAD_RIGHT),-36);
        $cadena .= str_pad("",36," ",STR_PAD_LEFT);
        $cadena .= str_pad("",36," ",STR_PAD_LEFT);
        $cadena .= str_pad("",109," ",STR_PAD_LEFT);
        $cadena .= "\r\n";

        #REGISTRO_3
        $cadena .= "4230".$comun;
        $cadena .= str_pad("",36," ",STR_PAD_LEFT);
        $cadena .= str_pad("",36," ",STR_PAD_LEFT);
        $cadena .= str_pad("",36," ",STR_PAD_LEFT);
        $cadena .= str_pad("",109," ",STR_PAD_LEFT);
        $cadena .= "\r\n";

        #REGISTRO_4
        $cadena .= "4240".$comun;
//        $cadena .= str_pad("CUOTA ". str_pad($campos[1],2,0,STR_PAD_LEFT),40," ",STR_PAD_RIGHT);



        $cadena .= str_pad($concepto_debito,40," ",STR_PAD_RIGHT);
        $cadena .= str_pad("",177," ",STR_PAD_LEFT);
        $cadena .= "\r\n";

        return $cadena;

    }

    /**
     *
     * @param type $campos
     */
    function armaStringDebitoCabeceraBancoFrances($campos){

//            $tmp = array(
//                0 => '4110',
//                1 => $DATOS_GLOBALES['bbva_frances_codigo_empresa'],
//                2 => date('Ymd',  strtotime($fechaDebito)),
//                3 => date('Ymd',  strtotime($fechaPresentacion)),
//                4 => str_pad(intval($registros),7,'0',STR_PAD_LEFT),
//                5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 14, '0', STR_PAD_LEFT),
//                6 => $DATOS_GLOBALES['bbva_frances_sucursal_cuenta_cargo'],
//                7 => $DATOS_GLOBALES['bbva_frances_sucursal_cuenta_cargo_dc'],
//                8 => $DATOS_GLOBALES['bbva_frances_cuenta_cargo'],
//                9 => $DATOS_GLOBALES['bbva_frances_codigo_servicio'],
//                10 => $DATOS_GLOBALES['bbva_frances_cuenta_divisa'],
//                11 => '0',
//                12 => $diskette['archivo'],
//                13 => $DATOS_GLOBALES['bbva_frances_nombre_ordenante'],
//                14 => $DATOS_GLOBALES['bbva_frances_tipo_cuenta_cbu'],
//                15 => str_pad("",141," ",STR_PAD_LEFT),
//            );

        $cadena = $campos[0];
        $cadena .= $campos[1];
        $cadena .= $campos[3];
        $cadena .= $campos[2];
        $cadena .= "0017";
        $cadena .= $campos[6];
        $cadena .= $campos[7];
        $cadena .= $campos[8];
        $cadena .= $campos[9];
        $cadena .= $campos[10];
        $cadena .= $campos[11];
        $cadena .= $campos[12];
        $cadena .= $campos[13];
        $cadena .= $campos[14];
        $cadena .= $campos[15];
        $cadena .= "\r\n";

        return $cadena;
    }


    /**
     *
     * @param type $campos
     */
    function armaStringDebitoPieBancoFrances($campos){

//            $tmp = array(
//                0 => '4110',
//                1 => $DATOS_GLOBALES['bbva_frances_codigo_empresa'],
//                2 => date('Ymd',  strtotime($fechaDebito)),
//                3 => date('Ymd',  strtotime($fechaPresentacion)),
//                4 => str_pad(intval($registros),7,'0',STR_PAD_LEFT),
//                5 => str_pad(number_format(round($importeTotal,2) * 100,0,"",""), 14, '0', STR_PAD_LEFT),
//                6 => $DATOS_GLOBALES['bbva_frances_sucursal_cuenta_cargo'],
//                7 => $DATOS_GLOBALES['bbva_frances_sucursal_cuenta_cargo_dc'],
//                8 => $DATOS_GLOBALES['bbva_frances_cuenta_cargo'],
//                9 => $DATOS_GLOBALES['bbva_frances_codigo_servicio'],
//                10 => $DATOS_GLOBALES['bbva_frances_cuenta_divisa'],
//                11 => '0',
//                12 => $diskette['archivo'],
//                13 => $DATOS_GLOBALES['bbva_frances_nombre_ordenante'],
//                14 => $DATOS_GLOBALES['bbva_frances_tipo_cuenta_cbu'],
//                15 => str_pad("",141," ",STR_PAD_LEFT),
//            );

        $cadena = $campos[0];
        $cadena .= $campos[1];
        $cadena .= $campos[5];
        $cadena .= $campos[4];
        $cadena .= $campos[16];
        $cadena .= $campos[17];
        $cadena .= "\r\n";
        return $cadena;
    }


    function decodeStringDebitoBancoFrances($cadena){
        $campos = array();
        $campos['codigo_registro'] = substr($cadena,0,4);
        $campos['id_empresa'] = substr($cadena,4,5);
        $campos['filler_1'] = substr($cadena,9,2);
        $campos['socio_id'] = intval(substr($cadena,11,22));
        $campos['cbu'] = substr($cadena,33,22);
        $campos['status'] = str_pad(intval(substr($cadena,70,6)),3,'0',STR_PAD_LEFT);
        $campos['fecha_debito'] = date('Y-m-d',mktime(0, 0, 0, substr($cadena,102,2), substr($cadena,104,2), substr($cadena,98,4)));

        $campos['ref_univ'] = substr($cadena,76,15);
        $campos['liquidacion_id'] = intval(substr($campos['ref_univ'],7,6));

		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();

		$campos['indica_pago'] = ($oCODIGO->isCodigoPago("00017",(!empty($campos['status']) ? $campos['status'] : "ERR")) ? 1 : 0);


        $campos['importe_debitado'] = intval(substr($cadena,55,15)) / pow(10,2);

// 		$campos['cbu'] = $campos['cbu_b1'].$campos['cbu_b2'];
		$cbuDecode = $this->deco_cbu($campos['cbu']);
		$campos['banco_id'] = $cbuDecode['banco_id'];
		$campos['sucursal'] = (isset($cbuDecode['sucursal']) ? $cbuDecode['sucursal'] : "");
		$campos['tipo_cta_bco'] = (isset($cbuDecode['tipo_cta_bco']) ? $cbuDecode['tipo_cta_bco'] : "");
		$campos['nro_cta_bco'] = (isset($cbuDecode['nro_cta_bco']) ? $cbuDecode['nro_cta_bco'] : "");

		$datos = array();
                $datos['codigo_registro'] = $campos['codigo_registro'];
		$datos['socio_id'] = $campos['socio_id'];
		$datos['banco_id'] = $campos['banco_id'];
		$datos['sucursal'] = $campos['sucursal'];
		$datos['tipo_cta_bco'] = $campos['tipo_cta_bco'];
		$datos['nro_cta_bco'] = $campos['nro_cta_bco'];
		$datos['cbu'] = $campos['cbu'];
		$datos['importe_debitado'] = $campos['importe_debitado'];
		$datos['status'] = $campos['status'];
		$datos['indica_pago'] = $campos['indica_pago'];
		$datos['fecha_debito'] = $campos['fecha_debito'];
                $datos['ref_univ'] = $campos['ref_univ'];
                $datos['liquidacion_id'] = $campos['liquidacion_id'];

//        debug($campos);
//        debug($datos);

//        if($campos['codigo_registro'] !== '4210'){
//            $datos['indica_pago'] = 0;
//        }


        return $datos;
    }


    /**
     * 1    1   INDICADOR DE LOTE "D"
     * 2    11  CUIT EMPRESA
     * 3    22  CBU SOCIO
     * 4    22  ID CLIENTE
     * 5    8   VENCIMIENTO
     * 6    10  PRESTACION
     * 7    15  FILLER
     * 8    15  REFERENCIA DEBITO
     * 9    10  IMPORTE (8,2)
     * 10   1   TIPO MONEDA (P/D)
     * 11   36  FILLER
     * 12   3   CODIGO RENDICION
     * 13   6   FILLER
     * @param type $campos
     */
    function armaStringDebitoBancoMeridian($campos){
        $cadena = "";


//        $campos = array(
//                0 => $idDebito,
//                1 => $registroNro,
//                2 => $apenom,
//                3 => $cbu,
//                4 => $importeDebito,
//                5 => $fechaDebito,
//                6 => $socioId,
//                7 => $liquidacionSocioId,
//                8 => $liquidacionID,
//        );

        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $cuit_empresa = (isset($INI_FILE['intercambio']['meridian_cuit_empresa']) && $INI_FILE['intercambio']['meridian_cuit_empresa'] != 0 ? $INI_FILE['intercambio']['meridian_cuit_empresa'] : '00000000000');

        $cadena .= "D";
        $cadena .= str_pad($cuit_empresa,11,0,STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[3]),22,0,STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[0]),22,0,STR_PAD_LEFT);
        $cadena .= date("Ymd",strtotime($campos[5]));

        $prestacion_empresa = (isset($INI_FILE['intercambio']['meridian_prestacion']) && $INI_FILE['intercambio']['meridian_prestacion'] != "" ? $INI_FILE['intercambio']['meridian_prestacion'] : '0000000000');
        $cadena .= str_pad($prestacion_empresa,10,0,STR_PAD_LEFT);
        $cadena .= str_pad("",15," ",STR_PAD_LEFT);

        $cadena .= str_pad(trim($campos[8]), 7, '0', STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[6]), 8, '0', STR_PAD_LEFT);
        $cadena .= str_pad(number_format($campos[4] * 100,0,"",""), 10, '0', STR_PAD_LEFT);
        $cadena .= "P";
        $cadena .= str_pad("",36," ",STR_PAD_LEFT);
        $cadena .= str_pad("",3," ",STR_PAD_LEFT);
        $cadena .= str_pad("",6," ",STR_PAD_LEFT);



        $cadena .= "\r\n";

        return $cadena;
    }

    /**
     * 1    1   TIPO NOVEDAD FIJO "T"
     * 2    11  CANTIDAD DE REGISTROS
     * 3    8   FECHA PROCESO
     * 4    10  TOTAL DEBITOS (8,2)
     * 5    11  CUIT EMPRESA
     * 6    50  RAZON SOCIAL EMPRESA
     * 7    69  FILLER
     * @param type $campos
     */
    function armaStringDebitoPieBancoMeridian($campos){

        $cadena = "";
        $cadena .= str_pad(trim($campos[0]),1,"T",STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[1]), 11, '0', STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[2]), 8, '0', STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[3]), 10, '0', STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[4]), 11, '0', STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[5]), 50, ' ', STR_PAD_RIGHT);
        $cadena .= str_pad("",69," ",STR_PAD_LEFT);
        $cadena .= "\r\n";

        return $cadena;
    }


    /**
     * INTERFACE COBRO DIGITAL * SALIDA * (110)
     * 1    6   SOCIO_ID
     * 2    8   FECHA DEBITO
     * 3    40  NOMBRE
     * 4    11  CUIT / DOCUMENTO
     * 5    10  CONCEPTO
     * 6    12  IMPORTE
     * 7    23  ESTADO
     * 8    22  CBU
     * @param type $campos
     * @return string
     */
    function armaStringDebitoCobroDigital($campos){
        $cadena = "";
        $cadena .= str_pad(trim($campos[0]), 6, '0', STR_PAD_LEFT);
        $cadena .= str_pad(date("Ymd",strtotime($campos[1])),8,0,STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[2]), 40, ' ', STR_PAD_RIGHT);
        $cadena .= str_pad(trim($campos[3]), 11, '0', STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[4]), 10, ' ', STR_PAD_RIGHT);
        $cadena .= str_pad(number_format($campos[5] * 100,0,"",""), 12, '0', STR_PAD_LEFT);
        $cadena .= str_pad(trim($campos[6]), 23, ' ', STR_PAD_RIGHT);
        $cadena .= str_pad(trim($campos[7]),22,0,STR_PAD_LEFT);
        $cadena .= "\r\n";
        return $cadena;
    }

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
    function decodeStringDebitoCobroDigital($cadena,$checkIsPago=TRUE){
        $campos = array();
        $campos['socio_id'] = intval(substr($cadena,0,6));
        $campos['fecha_debito'] = date('Y-m-d',mktime(0, 0, 0, substr($cadena,10,2), substr($cadena,12,2), substr($cadena,6,4)));
        $campos['apenom'] = substr($cadena,14,40);
        $campos['cuit_cuil'] = substr($cadena,54,11);
        $campos['documento'] = str_pad(intval(substr($cadena,54,11)),8,0,STR_PAD_RIGHT);
        $campos['concepto'] = substr($cadena,65,10);
        $campos['importe_debitado'] = intval(substr($cadena,75,12)) / pow(10,2);
        $campos['status'] = str_pad(trim(substr($cadena,87,3)),3,0,STR_PAD_RIGHT);
        $campos['concepto_rendicion'] = strtoupper(trim(substr($cadena,90,20)));
        $campos['cbu'] = substr($cadena,110,22);

        $campos['indica_pago'] = 0;
        if($checkIsPago){
            App::import('Model','Config.BancoRendicionCodigo');
            $oCODIGO = new BancoRendicionCodigo();
            $campos['indica_pago'] = ($oCODIGO->isCodigoPago("99910",(!empty($campos['status']) ? $campos['status'] : "ERR")) ? 1 : 0);
        }


        return $campos;
    }


    /**
     * DISEÑO DE REGISTRO BANCO COMAFI (00299)
     * 1    3   COD TRANSACCCION
     * 2    8   FECHA VENCIMIENTO
     * 3    5   CODIGO EMPRESA
     * 4    22  CLIENTE ID
     * 5    1   MONEDA
     * 6    22  CBU
     * 7    10  IMPORTE
     * 8    11  CUIT EMPRESA
     * 9    10  PRESTACION
     * 10   15  REFERENCIA OPERACION
     * 11   15  REF UNIVOCA
     * 12   15  CUENTA EMPRESA
     * 13   22  ID CLIENTE CBU NUEVO (PARA LAS ALTAS/MODIF)
     * 14   3   CODIGO RENDICION
     * @param array $campos
     */
    function arma_str_debito_banco_comafi($campos){

//        $campos = array(
//                        1 => $socioId,
//                        2 => $cbu,
//                        7 => str_pad($liquidacionID,4,"0",STR_PAD_LEFT).str_pad($registroNro,2,"0",STR_PAD_LEFT),
//                        8 => $fechaDebito,
//                        9 => $importeDebito,
//                        10 => $liquidacionID,
//                        11 => $liquidacionSocioId,
//        );

        $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');

        $cadena = "051";
        $cadena .= date("dmY",strtotime($campos[8]));
        $cadena .= str_pad($DATOS_GLOBALES['comafi_empresa_codigo'],5,"0",STR_PAD_LEFT);
        $cadena .= str_pad(str_pad(trim($campos[1]),5,'0',STR_PAD_LEFT).str_pad(trim($campos[7]),6,'0',STR_PAD_LEFT),22," ",STR_PAD_RIGHT);
        $cadena .= "0";
        $cadena .= str_pad(trim($campos[2]),22,"0",STR_PAD_LEFT);
        $cadena .= str_pad(number_format($campos[9] * 100,0,"",""), 10, '0', STR_PAD_LEFT);
        $cadena .= str_pad(trim($DATOS_GLOBALES['comafi_empresa_cuit']),11,"0",STR_PAD_LEFT);
        $cadena .= str_pad(trim($DATOS_GLOBALES['comafi_empresa_prestacion']),10," ",STR_PAD_RIGHT);
        $cadena .= str_pad(trim($campos[11]),15," ",STR_PAD_LEFT);
        $cadena .= str_pad(str_pad(trim($campos[1]),5,'0',STR_PAD_LEFT).str_pad(trim($campos[7]),6,'0',STR_PAD_LEFT),15," ",STR_PAD_RIGHT);
        $cadena .= str_pad(trim($DATOS_GLOBALES['comafi_empresa_ctabco']),15,"0",STR_PAD_LEFT);
        $cadena .= str_pad("",22," ",STR_PAD_LEFT);
        $cadena .= str_pad("",3," ",STR_PAD_LEFT);
        $cadena .= "\r\n";

        return $cadena;
    }

        /**
     * DISEÑO DE REGISTRO BANCO COMAFI (00299)
     * 1    3   COD TRANSACCCION
     * 2    8   FECHA VENCIMIENTO
     * 3    5   CODIGO EMPRESA
     * 4    22  CLIENTE ID
     * 5    1   MONEDA
     * 6    22  CBU
     * 7    10  IMPORTE
     * 8    11  CUIT EMPRESA
     * 9    10  PRESTACION
     * 10   15  REFERENCIA OPERACION
     * 11   15  REF UNIVOCA
     * 12   15  CUENTA EMPRESA
     * 13   22  ID CLIENTE CBU NUEVO (PARA LAS ALTAS/MODIF)
     * 14   3   CODIGO RENDICION
     * @param string $cadena
     */
    function decode_str_debito_banco_comafi($cadena){

//        debug($cadena);

        $campos = array();
        $campos['codigo_transaccion'] = substr($cadena,0,3);
        $campos['fecha_debito'] = substr($cadena,3,8);
        $campos['codigo_empresa'] = substr($cadena,11,5);
        $campos['id_cliente'] = substr($cadena,16,22);
        $campos['moneda'] = substr($cadena,38,1);
        $campos['cbu'] = substr($cadena,39,22);
        $campos['importe_debitado'] = substr($cadena,61,10);
        $campos['cuit_empresa'] = substr($cadena,71,11);
        $campos['prestacion'] = substr($cadena,82,10);
        $campos['ref_operacion'] = substr($cadena,92,15);
        $campos['ref_univ'] = substr($cadena,107,15);
        $campos['cta_empresa'] = substr($cadena,122,15);
        $campos['id_cliente_cbu_nvo'] = substr($cadena,137,22);
        $campos['codigo_rendicion'] = substr($cadena,159,3);

        $campos['fecha_debito'] = date('Y-m-d',strtotime(substr($campos['fecha_debito'],-4)."-".substr($campos['fecha_debito'],2,2)."-".substr($campos['fecha_debito'],0,2)));
        $campos['socio_id'] = intval(substr($campos['id_cliente'],0,5));
        $campos['liquidacion_id'] = intval(substr($campos['id_cliente'],5,4));
        $campos['regnro'] = intval(substr($campos['id_cliente'],9,2));

        $campos['importe_debitado'] = intval($campos['importe_debitado']) / pow(10,2);

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();



        $campos['codigo_rendicion'] = trim($campos['codigo_rendicion']);

        $campos['status'] = (empty($campos['codigo_rendicion']) && $campos['codigo_transaccion'] == '052' ? 'COB' : $campos['codigo_rendicion']);

        #otros movimientos distintos del 50 (debito aceptado)
        if($campos['codigo_transaccion'] !== '052'){$campos['status'] = $campos['codigo_transaccion'];}


        $campos['indica_pago'] = ($oCODIGO->isCodigoPago("00299",(!empty($campos['status']) ? $campos['status'] : "ERR")) ? 1 : 0);


        $cbuDecode = $this->deco_cbu($campos['cbu']);
        $campos['banco_id'] = $cbuDecode['banco_id'];
        $campos['sucursal'] = (isset($cbuDecode['sucursal']) ? $cbuDecode['sucursal'] : "");
        $campos['tipo_cta_bco'] = (isset($cbuDecode['tipo_cta_bco']) ? $cbuDecode['tipo_cta_bco'] : "");
        $campos['nro_cta_bco'] = (isset($cbuDecode['nro_cta_bco']) ? $cbuDecode['nro_cta_bco'] : "");

        $datos = array();
        $datos['codigo_transaccion'] = intval($campos['codigo_transaccion']);
        $datos['socio_id'] = $campos['socio_id'];
        $datos['banco_id'] = $campos['banco_id'];
        $datos['sucursal'] = $campos['sucursal'];
        $datos['tipo_cta_bco'] = $campos['tipo_cta_bco'];
        $datos['nro_cta_bco'] = $campos['nro_cta_bco'];
        $datos['cbu'] = $campos['cbu'];
        $datos['importe_debitado'] = $campos['importe_debitado'];
        $datos['status'] = $campos['status'];
        $datos['indica_pago'] = $campos['indica_pago'];
        $datos['fecha_debito'] = $campos['fecha_debito'];
        $datos['ref_univ'] = $campos['ref_univ'];
        $datos['liquidacion_id'] = $campos['liquidacion_id'];

        $codigosTransaccion = array(52,70);

        if (!in_array($datos['codigo_transaccion'], $codigosTransaccion) ) {
            $datos['indica_pago'] = 0;
            $datos['status'] = 'ERR';
        }

       if($datos['codigo_transaccion'] == 65){
           $datos['indica_pago'] = 0;
           $datos['status'] = 'R15';
       }

        return $datos;
    }

    /**
     *
     * @param type $campos
     *
     * 1    CUIL (11)
     * 2    CBU (22)
     * 3    REF (10)
     * 4    FECHAPROC (6 AAMMDD)
     * 5    FECHAVTO  (6 AAMMDD)
     * 6    IMPORTE (ENTERO SIN DECIMAL)
     * 7    PRESTACION (10)
     * 8    FVTOMAX (6 AAMMDD)
     * 9    CICLOS (2)
     * 
     * 10   OPERACION (10)
     * 11   FECHA (6 AAMMDD)
     * 12   TIPO (S=SERVICIO, C=CREDITO)
     *
     */
    function arma_str_debito_cuenca($campos){
//        $campos = array(
//            0 => (!empty($socioCuitCuil) ? $socioCuitCuil : $ndoc),
//            1 => $cbu,
//            2 => "",
//            3 => $socioId,
//            4 => $fechaPresentacion,
//            5 => $fechaDebito,
//            6 => $importeDebito,
//            7 => $ciclos,
//            8 => $liquidacionID,
//            9 => $registroNro,
//            10 => $liquidacionSocioId,
//            12 => $fechaAltaSocio
//        );

        $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');

        $cadena = "";
        $cadena .= str_pad($campos[0],11,"0",STR_PAD_LEFT);
        $cadena .= ";";
        $cadena .= str_pad($campos[1],22,"0",STR_PAD_LEFT);
        $cadena .= ";";
        $cadena .= "";
        $cadena .= ";";
//        $cadena .= str_pad("",10,"0",STR_PAD_LEFT);
//        $cadena .= ";";
        $cadena .= str_pad(trim($campos[3]),8,'0',STR_PAD_LEFT).str_pad(trim($campos[8]),5,'0',STR_PAD_LEFT).str_pad(trim($campos[9]),2,'0',STR_PAD_LEFT);
        $cadena .= ";";
        $cadena .= date('ymd', strtotime($campos[4]));
        $cadena .= ";";
        $cadena .= date('ymd', strtotime($campos[5]));
        $cadena .= ";";
        $cadena .= number_format($campos[6] * 100,0,"","");
        $cadena .= ";";
        $cadena .= str_pad(trim($DATOS_GLOBALES['cuenca_identificador_presentacion']),10," ",STR_PAD_RIGHT);
        $cadena .= ";";
        $cadena .= date('ymd', strtotime($campos[11]));
        $cadena .= ";";
        $cadena .= str_pad(trim($campos[7]),2,'0',STR_PAD_LEFT);
        $cadena .= ";";
        $cadena .= str_pad(trim($campos[3]),10,'0',STR_PAD_LEFT);
        $cadena .= ";";
        $cadena .= date('ymd', strtotime($campos[12]));
        $cadena .= ";";
        $cadena .= (isset($DATOS_GLOBALES['cuenca_identificador_tipo']) ? $DATOS_GLOBALES['cuenca_identificador_tipo'] : 'C');
        $cadena .= "\r\n";
        return $cadena;

    }

    /**
     *
     * @param type $cadena
     *
     * 1    CUIL (11)
     * 2    CBU (22)
     * 3    REF (15)
     * 4    FECHAPROC (6 AAMMDD)
     * 5    FECHAVTO  (6 AAMMDD)
     * 6    IMPORTE (ENTERO SIN DECIMAL)
     * 7    PRESTACION (10)
     * 8    FVTOMAX (6 AAMMDD)
     * 9    CICLOS (2)
     *
     */

    function decode_str_debito_cuenca($cadena){

        $campos = array();
        $campos['cuil'] = substr($cadena,0,11);
        $campos['cbu'] = substr($cadena,11,22);
        $campos['referencia'] = substr($cadena,33,15);
        $campos['fecha_proc'] = substr($cadena,48,6);
        $campos['fecha_vto'] = substr($cadena,54,6);
        $campos['importe'] = substr($cadena,60,10);
        $campos['prestacion'] = substr($cadena,70,10);
        $campos['estado'] = substr($cadena,80,1);
        $campos['motivo_rechazo'] = substr($cadena,81,3);

        if($campos['estado'] == 'R' && $campos['motivo_rechazo'] == 'COB'){
            $campos['motivo_rechazo'] = 'ERR';
        }

        $campos['fecha_debito'] = date('Y-m-d',strtotime(substr($campos['fecha_vto'],-4)."-".substr($campos['fecha_vto'],2,2)."-".substr($campos['fecha_vto'],0,2)));
        $campos['socio_id'] = intval(substr($campos['referencia'],0,8));
        $campos['liquidacion_id'] = intval(substr($campos['referencia'],8,5));
        $campos['regnro'] = intval(substr($campos['referencia'],13,2));

        $campos['importe_debitado'] = intval($campos['importe']) / pow(10,2);

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();
        $campos['codigo_rendicion'] = trim($campos['motivo_rechazo']);
        $campos['status'] = (empty($campos['motivo_rechazo']) ? 'COB' : $campos['motivo_rechazo']);

        $campos['indica_pago'] = ($oCODIGO->isCodigoPago("65203",(!empty($campos['status']) ? $campos['status'] : "ERR")) ? 1 : 0);


        $cbuDecode = $this->deco_cbu($campos['cbu']);
        $campos['banco_id'] = $cbuDecode['banco_id'];
        $campos['sucursal'] = (isset($cbuDecode['sucursal']) ? $cbuDecode['sucursal'] : "");
        $campos['tipo_cta_bco'] = (isset($cbuDecode['tipo_cta_bco']) ? $cbuDecode['tipo_cta_bco'] : "");
        $campos['nro_cta_bco'] = (isset($cbuDecode['nro_cta_bco']) ? $cbuDecode['nro_cta_bco'] : "");
        $campos['ref_univ'] = $campos['referencia'];
        return $campos;

    }

    /**
     * 1    10 ID DEBITO (4 LIQUIDACION / 6 SOCIO)
     * 2    11 DNI
     * 3    40 APENOM
     * 4    22 CBU
     * 5    11 CUENTA
     * 6    5  SUCURSAL
     * 7    8 FECHA DEBITO
     * 8    10 IMPORTE (10,2)
     * 9    11 filler
     *
     * LONGITUD 128
     *
     * @param type $campos
     * @return string
     */
    function arma_str_debito_fenanjor_macro($campos){

//				$campos = array(
//								1 => $ndoc,
//								2 => $sucursal,
//								3 => $apenom,
//								4 => $cuenta,
//								5 => $importeDebito,
//								6 => $cbu,
//                                                              7 => $fechaDebito
//								9 => $socioId,
//								10 => $idDebito,
//				);

        $cadena = "";
        $cadena .= str_pad($campos[10],10,"0",STR_PAD_LEFT);
        $cadena .= str_pad($campos[1],11,"0",STR_PAD_LEFT);
        $cadena .= str_pad(substr($campos[3],0,40),40," ",STR_PAD_RIGHT);
        $cadena .= str_pad($campos[6],22,"0",STR_PAD_LEFT);
        $cadena .= str_pad($campos[4],11,"0",STR_PAD_LEFT);
        $cadena .= str_pad($campos[2],5,"0",STR_PAD_LEFT);
        $cadena .= date("Ymd",strtotime($campos[7]));
        $cadena .= str_pad(number_format($campos[5] * 100,0,"",""), 10, '0', STR_PAD_LEFT);
        $cadena .= str_pad("",11," ",STR_PAD_LEFT);
        $cadena .= "\r\n";
        return $cadena;
    }

    /**
     *
     * @param type $cadena
     * @return array
     */
    function decode_str_debito_fenanjor_macro($cadena){
        $campos = array();
        $campos['referencia'] = substr($cadena,0,10);
        $campos['dni'] = substr($cadena,10,11);
        $campos['apenom'] = substr($cadena,21,40);
        $campos['cbu'] = substr($cadena,61,22);
        $campos['nro_cta_bco'] = substr($cadena,83,11);
        $campos['sucursal'] = substr($cadena,94,5);
        $campos['fecha_debito'] = date('Y-m-d',mktime(0, 0, 0, substr($cadena,105,2), substr($cadena,103,2), substr($cadena,99,4)));
        $campos['importe_debitado'] = substr($cadena,107,10);
        $campos['importe_debitado'] = intval($campos['importe_debitado']) / pow(10,2);
        $campos['codigo_rendicion'] = substr($cadena,117,3);


        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();
        $campos['codigo_rendicion'] = trim($campos['codigo_rendicion']);
        $campos['status'] = (empty($campos['codigo_rendicion']) ? 'ERR' : $campos['codigo_rendicion']);

        $campos['indica_pago'] = ($oCODIGO->isCodigoPago("99921",(!empty($campos['status']) ? $campos['status'] : "ERR")) ? 1 : 0);


        $cbuDecode = $this->deco_cbu($campos['cbu']);
        $campos['banco_id'] = $cbuDecode['banco_id'];


        $campos['liquidacion_id'] = intval(substr($campos['referencia'],0,4));
        $campos['socio_id'] = intval(substr($campos['referencia'],4,6));

        return $campos;
    }

    /******************************************************************************************
     * FORMATO INTERCAMBIO BANCO MACRO
     *
     * 1    1   FILLER 1
     * 2    5   CONVENIO
     * 3    10  NRO SERVICIO
     * 4    5   NRO EMPRESA SUELDOS
     * 5    3   BANC0
     * 6    4   SUCURSAL Para Banco Macro  informar el numero de sucursal. Para otros bancos, informar las posiciones 4 a 7 del bloque 1 De la CBU
     * 7    1   TIPO CUENTA 3 - Cta Cte ,  4 - Caja de Ahorros para cuentas de Banco Macro - Bansud. Para cuentas de otros bancos no informar
     * 8    15  CUENTA Para Bco Macro informar el numero de cuenta. Para otros bancos, informar el bloque 2 De la CBU y rellenar con un cero a izquierda.
     * 9    22  CLIENTE ID
     * 10   15  COMPROBANTE
     * 11   2   FUNCION O USO (BLANCOS)
     * 12   4   CODIGO RESPUESTA (BLANCOS)
     * 13   8   FECHA VENCIMIENTO AAAAMMDD
     * 14   3   MONEDA 002 - dolares 080 - pesos
     * 15   13  IMPORTE DEBITO
     * 16   8   FECHA TOPE DEVOLUCION (CEROS)
     * 17   13  IMPORTE DEBITADO (CEROS)
     * 18   4   NUEVA SUCURSAL BANCARIA (CEROS)
     * 19   1   TIPO CUENTA (CEROS)
     * 20   15  NUEVA CUENTA (CEROS)
     * 21   22  NUEVO ID DEL CLIENTE (BLANCOS)
     * 22   40  DATOS DE RETORNO (INFO ADICIONAL BLANCOS)
     * 23   5   SIN USAR (BLANCOS)
     * 24   1   FILLER (CERO)
     *
     *
     * @param type $campos
     */
    function arma_str_debito_macro($campos){

        /**
         * 0 => CBU
         * 1 => idDebito
         * 2 => liquidacionSocioId
         * 3 => fechaDebito
         * 4 => importeDebito
         * 5 => nroConvenio
         */


        $cbuDecode = $this->deco_cbu($campos[0]);

        if($cbuDecode['banco_id'] == '0285'){
            $cbuDecode['tipo_cta_bco'] = substr(trim($campos[0]),8,1);
            $cbuDecode['nro_cta_bco'] = $cbuDecode['tipo_cta_bco'].str_pad(intval(substr(trim($campos[0]),3,4)),3,0,STR_PAD_LEFT).str_pad(substr(trim($cbuDecode['nro_cta_bco']),-11),11,0,STR_PAD_LEFT);
        }else{
            $cbuDecode['tipo_cta_bco'] = 0;
            $cbuDecode['sucursal'] = substr(trim($campos[0]),3,4);
            $cbuDecode['nro_cta_bco'] = str_pad(substr(trim($campos[0]),-14),15,0,STR_PAD_LEFT);
        }

        $cadena = "";
        $cadena .= "0";
        $cadena .= str_pad(trim($campos[5]),5,"0",STR_PAD_LEFT);
        $cadena .= str_pad("",10," ",STR_PAD_RIGHT);
        $cadena .= str_pad("",5,0,STR_PAD_LEFT);
        $cadena .= str_pad(intval($cbuDecode['banco_id']),3,0,STR_PAD_LEFT);
        $cadena .= str_pad(intval($cbuDecode['sucursal']),4,0,STR_PAD_LEFT);
        $cadena .=  $cbuDecode['tipo_cta_bco'];
        $cadena .= str_pad(intval($cbuDecode['nro_cta_bco']),15,0,STR_PAD_LEFT);
        $cadena .= str_pad($campos[1],22," ",STR_PAD_RIGHT);
        $cadena .= str_pad($campos[2],15," ",STR_PAD_RIGHT);
        $cadena .= str_pad("",2," ",STR_PAD_RIGHT);
        $cadena .= str_pad("",4," ",STR_PAD_RIGHT);
        $cadena .= str_pad(date("Ymd",strtotime($campos[3])),8,0,STR_PAD_LEFT);
        $cadena .= "080";
        $cadena .= str_pad(number_format($campos[4] * 100,0,"",""), 13, '0', STR_PAD_LEFT);
        $cadena .= str_pad("",8,0,STR_PAD_LEFT);
        $cadena .= str_pad("",13,0,STR_PAD_LEFT);
        $cadena .= str_pad("",4,0,STR_PAD_LEFT);
        $cadena .= str_pad("",1,0,STR_PAD_LEFT);
        $cadena .= str_pad("",15,0,STR_PAD_LEFT);
        $cadena .= str_pad("",22," ",STR_PAD_RIGHT);
        $cadena .= str_pad("",40," ",STR_PAD_RIGHT);
        $cadena .= str_pad("",5," ",STR_PAD_RIGHT);
        $cadena .= "0";
        $cadena .= "\r\n";
        return $cadena;

    }

    /**
     * GENERA REGISTRO CABECERA LOTE BANCO MACRO
     * 1    1   FILLER 1
     * 2    5   CONVENIO
     * 3    10  NRO SERVICIO
     * 4    5   NRO EMPRESA SUELDOS
     * @param type $campos
     */
    function arma_str_debito_macro_cabecera($campos){

        /**
         * 0 => fechaCreacion
         * 1 => ImporteTotal
         * 2 => nroConvenio
         */

        $cadena = "";
        $cadena .= "1";
        $cadena .= str_pad(trim($campos[2]),5,"0",STR_PAD_LEFT);
        $cadena .= str_pad("",10," ",STR_PAD_RIGHT);
        $cadena .= str_pad("",5,0,STR_PAD_LEFT);
        $cadena .= str_pad(date("Ymd",strtotime($campos[0])),8,0,STR_PAD_LEFT);
        $cadena .= str_pad(number_format($campos[1] * 100,0,"",""), 18, '0', STR_PAD_LEFT);
        $cadena .= "080";
        $cadena .= "01";
        $cadena .= str_pad("",98,0,STR_PAD_LEFT);
        $cadena .= str_pad("",69," ",STR_PAD_RIGHT);
        $cadena .= "0";
        $cadena .= "\r\n";
        return $cadena;

    }


    function decode_str_debito_banco_macro($cadena){

        $campos = array();
        $campos['reglote'] = substr($cadena,0,1);
        $campos['conveni'] = substr($cadena,1,5);
        $campos['servici'] = substr($cadena,6,10);
        $campos['emp_sue'] = substr($cadena,16,5);
        $campos['cod_bco'] = substr($cadena,21,3);
        $campos['cod_suc'] = substr($cadena,24,4);
        $campos['cod_cta'] = substr($cadena,28,1);
        $campos['nro_cta'] = substr($cadena,29,15);
        $campos['idclien'] = substr($cadena,44,22);
        $campos['iddebit'] = substr($cadena,66,15);
        $campos['funcion'] = substr($cadena,81,2);
        $campos['rechazo'] = substr($cadena,83,4);
        $campos['fechvto'] = substr($cadena,87,8);
        $campos['cmoneda'] = substr($cadena,95,3);
        $campos['impoade'] = substr($cadena,98,13);
        $campos['fechrin'] = substr($cadena,111,8);
        $campos['impodeb'] = substr($cadena,119,13);
        $campos['nvasucu'] = substr($cadena,132,4);
        $campos['nvatcta'] = substr($cadena,136,1);
        $campos['nvancta'] = substr($cadena,137,15);
        $campos['nvaidcl'] = substr($cadena,152,22);
        $campos['datoret'] = substr($cadena,174,40);
        $campos['sinusar'] = substr($cadena,214,5);
        $campos['fillerf'] = substr($cadena,219,1);

        $iddebito = $this->decode_iddebito_general($campos['idclien']);

        $datos = array();

        $datos['socio_id'] = $iddebito['socio_id'];
//        $datos['banco_id'] = str_pad($campos['cod_bco'],5,0,STR_PAD_LEFT);
//        $datos['sucursal'] = str_pad($campos['cod_suc'],5,0,STR_PAD_LEFT);
//        $datos['tipo_cta_bco'] = str_pad($campos['cod_cta'],2,0,STR_PAD_LEFT);
//        $datos['nro_cta_bco'] = substr($campos['nro_cta'],-10);
        $datos['importe_debitado'] = intval($campos['impodeb']) / pow(10, 2);
        $datos['status'] = substr(str_pad(trim($campos['rechazo']),3,0,STR_PAD_LEFT),0,3);

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();
        $datos['indica_pago'] = ($oCODIGO->isCodigoPago("00285",(!empty($datos['status']) ? $datos['status'] : "ERR")) ? 1 : 0);
        $datos['fecha_debito'] = date('Y-m-d',strtotime(substr($campos['fechvto'],0,4)."-".substr($campos['fechvto'],4,2)."-".substr($campos['fechvto'],-2)));
        $datos['ref_univ'] = $campos['idclien'];
        $datos['liquidacion_id'] = $iddebito['liquidacion_id'];

        return $datos;

    }


    function decode_str_debito_banco_macro_barrido($cadena){

        $campos = array();
        $campos['reglote'] = substr($cadena,0,1);
        $campos['conveni'] = substr($cadena,1,5);
        $campos['servici'] = substr($cadena,6,10);
        $campos['emp_sue'] = substr($cadena,16,5);
        $campos['idclien'] = substr($cadena,21,20);
        $campos['fechvto'] = substr($cadena,41,8);
        $campos['impoade'] = substr($cadena,49,11);
        $campos['cod_bco'] = substr($cadena,60,3);
        $campos['cod_cta'] = substr($cadena,63,1);
        $campos['cod_suc'] = substr($cadena,64,3);
        $campos['nro_cta'] = substr($cadena,67,16);
        $campos['secuenc'] = substr($cadena,83,2);
        $campos['cuota'] = substr($cadena,85,2);
        $campos['estado'] = substr($cadena,87,1);
        $campos['sinusar'] = substr($cadena,88,20);
        $campos['impoapl'] = substr($cadena,108,11);



//        $campos['idclien'] = substr($cadena,44,22);
//        $campos['iddebit'] = substr($cadena,66,15);
//        $campos['funcion'] = substr($cadena,81,2);
//        $campos['rechazo'] = substr($cadena,83,4);
//
//        $campos['cmoneda'] = substr($cadena,95,3);
//        $campos['impoade'] = substr($cadena,98,13);
//        $campos['fechrin'] = substr($cadena,111,8);
//
//        $campos['nvasucu'] = substr($cadena,132,4);
//        $campos['nvatcta'] = substr($cadena,136,1);
//        $campos['nvancta'] = substr($cadena,137,15);
//        $campos['nvaidcl'] = substr($cadena,152,22);
//        $campos['datoret'] = substr($cadena,174,40);
//        $campos['fillerf'] = substr($cadena,219,1);

//        debug($campos);

        $iddebito = $this->decode_iddebito_general($campos['idclien']);

        $datos = array();

        $datos['socio_id'] = $iddebito['socio_id'];
//        $datos['banco_id'] = str_pad($campos['cod_bco'],5,0,STR_PAD_LEFT);
//        $datos['sucursal'] = str_pad($campos['cod_suc'],5,0,STR_PAD_LEFT);
//        $datos['tipo_cta_bco'] = str_pad($campos['cod_cta'],2,0,STR_PAD_LEFT);
//        $datos['nro_cta_bco'] = substr($campos['nro_cta'],-10);
        $datos['importe_debitado'] = intval($campos['impoapl']) / pow(10, 2);

        switch ($campos['estado']){
            case 'E':
                $campos['rechazo'] = 'R02';
                $datos['importe_debitado'] = intval($campos['impoade']) / pow(10, 2);
                break;
            case 'R':
                $campos['rechazo'] = 'R08';
                $datos['importe_debitado'] = intval($campos['impoade']) / pow(10, 2);
                break;
            default:
                $campos['rechazo'] = '000';
                if(empty($datos['importe_debitado'])){
                    $campos['rechazo'] = 'R10';
                    $datos['importe_debitado'] = intval($campos['impoade']) / pow(10, 2);
                }
                break;
        }

//        $campos['rechazo'] = ($campos['estado'] == 'E' ? 'R02' : '000');
//        $campos['rechazo'] = ($campos['estado'] == 'R' ? 'R08' : '000');
//        if($campos['estado'] == 'E'){
//            $campos['rechazo'] = 'R02';
//        }

//        $datos['importe_debitado'] = ($campos['estado'] == 'E' ? $datos['importe_debitado'] : $campos['impoapl']);

//        if(empty($datos['importe_debitado'])){
//            $campos['rechazo'] = 'R10';
//            $datos['importe_debitado'] = intval($campos['impoade']) / pow(10, 2);
//        }
        $datos['status'] = substr(str_pad(trim($campos['rechazo']),3,0,STR_PAD_LEFT),0,3);

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();
        $datos['indica_pago'] = ($oCODIGO->isCodigoPago("00285",(!empty($datos['status']) ? $datos['status'] : "ERR")) ? 1 : 0);
        $datos['fecha_debito'] = date('Y-m-d',strtotime(substr($campos['fechvto'],0,4)."-".substr($campos['fechvto'],4,2)."-".substr($campos['fechvto'],-2)));
        $datos['ref_univ'] = $campos['idclien'];
        $datos['liquidacion_id'] = $iddebito['liquidacion_id'];

        return $datos;

    }

    /**
     * decode_iddebito_general
     * longitud 22
     * 12 socio_id
     * 8 liquidacion_id
     * 2 registro
     * @param type $idd
     * @return type
     */
    function decode_iddebito_general($idd,$min = FALSE){
        if(!$min){
            $iddebito = array(
                'socio_id' => intval(substr($idd,0,12)),
                'liquidacion_id' => intval(substr($idd,12,8)),
                'registro' => intval(substr($idd,20,2))
            );
        }else{
            $iddebito = array(
                'socio_id' => intval(substr($idd,0,5)),
                'liquidacion_id' => intval(substr($idd,5,4)),
                'registro' => intval(substr($idd,9,2))
            );
        }
        return $iddebito;
    }

    /**
     * DISEÑO DE REGISTRO
     * HEADER / FOOTER
     *
     * 1    cod_trx (2) 01
     * 2    cliente (22)
     * 3    banco(3)
     * 4    cbu (23)
     * 5    factura (15)
     * 6    importe (17 | 15,2)
     * 7    Fh_vto (8) aaaammdd
     * 8    Cod_recha (2) 00
     * 9    Moneda (1) P
     * 10   filler (17) blanco
     *
     * @param type $campos
     * @param type $tipoRegistro
     */
    public function arma_str_debito_coinag($campos,$tipoRegistro = 'D', $iniIndex = ''){

        $cadena = "";

        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $codigo_empresa = (isset($INI_FILE['intercambio']['coinag_empresa'.$iniIndex.'_cprestamo']) && $INI_FILE['intercambio']['coinag_empresa'.$iniIndex.'_cprestamo'] != 0 ? $INI_FILE['intercambio']['coinag_empresa'.$iniIndex.'_cprestamo'] : '0000');
        $cuit_empresa = (isset($INI_FILE['intercambio']['coinag_empresa'.$iniIndex.'_cuit']) && $INI_FILE['intercambio']['coinag_empresa'.$iniIndex.'_cuit'] != 0 ? $INI_FILE['intercambio']['coinag_empresa'.$iniIndex.'_cuit'] : '00000000000');

        #HEADER
        if($tipoRegistro == 'H'){
            /**
             * 0 = fechaProceso
             */
            $cadena .= "00";
            $cadena .= str_pad(trim($codigo_empresa),4,"0",STR_PAD_LEFT);
            $cadena .= str_pad(trim($cuit_empresa),11,"0",STR_PAD_LEFT);
            $cadena .= date('Ymd', strtotime($campos[0]));
            $cadena .= str_pad('DEBITOS',20," ",STR_PAD_RIGHT);
            $cadena .= str_pad("",65," ",STR_PAD_LEFT);
        }

        #DETALLE
        if($tipoRegistro == 'D'){
            /**
             * 0 = nDoc
             * 1 = cbu
             * 2 = idd min
             * 3 = importe
             * 4 = fecha debito
             *
             */
            $cadena .= "01";
            $cadena .= str_pad(trim($campos[0]),22," ",STR_PAD_RIGHT);
            $cadena .= str_pad(substr(trim($campos[1]),0,3),3,"0",STR_PAD_LEFT);
            $cadena .= str_pad(trim($campos[1]),23,"0",STR_PAD_LEFT);
            $cadena .= str_pad(trim($campos[2]),15," ",STR_PAD_RIGHT);
            $cadena .= str_pad(number_format($campos[3] * 100, 0, "", ""), 17, '0', STR_PAD_LEFT);
            $cadena .= date('Ymd', strtotime($campos[4]));
            $cadena .= "00";
            $cadena .= "P";
            $cadena .= str_pad("",17," ",STR_PAD_LEFT);

        }

        #TRAILER
        if($tipoRegistro == 'T'){
            /**
             * 0 = cantidad de registros
             * 1 = total del lote
             */
            $cadena .= "99";
            $cadena .= str_pad(trim($campos[0]),8,"0",STR_PAD_LEFT);
            $cadena .= str_pad(number_format($campos[1] * 100, 0, "", ""), 18, '0', STR_PAD_LEFT);
            $cadena .= str_pad("",82," ",STR_PAD_LEFT);

        }


        $cadena .= "\r\n";
        return $cadena;
    }

    /**
     * ESTRUCTURA
     * 1    Cod_trx 2 (01 ACEPTADO, 03 RECHAZADO)
     * 2    Cliente (22)
     * 3    Banco (3)
     * 4    CBU (23)
     * 5    Factura (15)
     * 6    Importe (17)
     * 7    Fh_vto (8)
     * 8    Cod_recha (2)
     * 9    Moneda (1)
     * 10   Filler (17)
     *
     * @param type $cadena
     */
    function decode_str_debito_coinag($cadena, $banco_id = '00431'){
        $campos = array();
        $campos['cod_trx'] = substr($cadena,0,2);
        $campos['cliente'] = substr($cadena,2,22);
        $campos['banco'] = substr($cadena,24,3);
        $campos['cbu'] = substr($cadena,27,23);
        $campos['factura'] = substr($cadena,50,15);
        $campos['importe'] = substr($cadena,65,17);
        $campos['vencimiento'] = substr($cadena,82,8);
        $campos['cod_recha'] = substr($cadena,90,2);
        $campos['moneda'] = substr($cadena,92,1);
        $campos['filler'] = substr($cadena,93,17);

        $iddebito = $this->decode_iddebito_general($campos['factura'],TRUE);

        $datos = array();

        $datos['socio_id'] = $iddebito['socio_id'];
        $datos['importe_debitado'] = intval($campos['importe']) / pow(10, 2);
        $datos['status'] = substr(str_pad(trim($campos['cod_recha']),3,0,STR_PAD_LEFT),0,3);
        
        
        if($campos['cod_trx'] === '04') {
            $datos['status'] = '095';
        }
        

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();
        $datos['indica_pago'] = ($oCODIGO->isCodigoPago($banco_id,(!empty($datos['status']) ? $datos['status'] : "ERR")) ? 1 : 0);
        $datos['fecha_debito'] = date('Y-m-d',strtotime(substr($campos['vencimiento'],0,4)."-".substr($campos['vencimiento'],4,2)."-".substr($campos['vencimiento'],-2)));
        $datos['ref_univ'] = $campos['factura'];
        $datos['liquidacion_id'] = $iddebito['liquidacion_id'];


        return $datos;

    }


    /**
     *
     * @param type $campos
     * @return string
     */
    function arma_string_debito_itau($campos,$tipoRegistro = 'D'){
        $cadena = "";

        #HEADER
        if($tipoRegistro == 'H'){
            /**
             * 0 = nro de envio
             * 1 = fecha de envio
             */
            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
            $cadena .= "H";
            $cadena .= $DATOS_GLOBALES['itau_cuit_empresa'];
            $cadena .= "300";
            $cadena .= $DATOS_GLOBALES['itau_convenio'];
            $cadena .= str_pad(trim($campos[0]),5,"0",STR_PAD_LEFT);
            $cadena .= date('Ymd', strtotime($campos[1]));
            $cadena .= " ";
            $cadena .= "B";
            $cadena .= "D";
            $cadena .= str_pad("",763," ",STR_PAD_LEFT);

        }

        #TRAILER
        if($tipoRegistro == 'T'){
            /**
             * 0 = nro de envio
             * 1 = fecha de envio
             * 2 = total lote
             * 3 = registros detalle
             */
            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
            $cadena .= "T";
            $cadena .= $DATOS_GLOBALES['itau_cuit_empresa'];
            $cadena .= "300";
            $cadena .= $DATOS_GLOBALES['itau_convenio'];
            $cadena .= str_pad(trim($campos[0]),5,"0",STR_PAD_LEFT);
            $cadena .= date('Ymd', strtotime($campos[1]));
            $cadena .= str_pad("",5,"0",STR_PAD_LEFT);
            $cadena .= str_pad(number_format($campos[2] * 100,0,"",""), 17, '0', STR_PAD_LEFT);
            $cadena .= str_pad(trim($campos[3]),9,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",735," ",STR_PAD_LEFT);
        }

        #DETALLE
        if($tipoRegistro == 'D'){

            /**
             * 0 = IDD
             * 1 = apenom
             * 2 = CUIT
             * 3 = fecha debito
             * 4 = importe debito
             * 5 = CBU
             */

            $DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');
            $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);

            $cadena .= "D";
            $cadena .= "A";
            $cadena .= "FC";
            $cadena .= str_pad(trim($campos[0]),15,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",7," ",STR_PAD_LEFT);
            $cadena .= "000";
//            $cadena .= str_pad(trim($campos[0]),22,"0",STR_PAD_RIGHT);
            $cadena .= str_pad(trim($campos[0]),22,(isset($INI_FILE['intercambio']['itau_id_filler']) ? $INI_FILE['intercambio']['itau_id_filler'] : '0'),STR_PAD_RIGHT);
            $cadena .= str_pad(trim(substr($campos[1],0,60)),60," ",STR_PAD_RIGHT);
            $cadena .= "CL";
            $cadena .= str_pad(trim($campos[2]),11,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",41," ",STR_PAD_LEFT);
            $cadena .= str_pad("",49,"0",STR_PAD_LEFT);
            $cadena .= date('Ymd', strtotime($campos[3]));
            $cadena .= str_pad(number_format($campos[4] * 100,0,"",""), 17, '0', STR_PAD_LEFT);

            // 2do vto
            $cadena .= str_pad("",8,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",17,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",7,"0",STR_PAD_LEFT);

            // 3er vto
            $cadena .= str_pad("",8,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",17,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",7,"0",STR_PAD_LEFT);

            $cadena .= str_pad("",32,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",24," ",STR_PAD_LEFT);
            $cadena .= str_pad("",7,"0",STR_PAD_LEFT);


            $cadena .= str_pad(trim($campos[5]),22,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",52,"0",STR_PAD_LEFT);


            $cadena .= str_pad("",30," ",STR_PAD_LEFT);
            $cadena .= str_pad("",30," ",STR_PAD_LEFT);
            $cadena .= str_pad("",30," ",STR_PAD_LEFT);

            $cadena .= str_pad("",18," ",STR_PAD_LEFT);
            $cadena .= str_pad("",15," ",STR_PAD_LEFT);
            $cadena .= str_pad("",15," ",STR_PAD_LEFT);

            $cadena .= str_pad("",30," ",STR_PAD_LEFT);
            $cadena .= str_pad("",6,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",6," ",STR_PAD_LEFT);
            $cadena .= str_pad("",6," ",STR_PAD_LEFT);

            $cadena .= str_pad("",1," ",STR_PAD_LEFT);
            $cadena .= str_pad("",4,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",3," ",STR_PAD_LEFT);

            $cadena .= str_pad("",100," ",STR_PAD_LEFT);

            $cadena .= str_pad("",66," ",STR_PAD_LEFT);

        }

        $cadena .= "\r\n";
        return $cadena;
    }



    /**
     *
     * BANCO COMERCIAL (formato CSV)
     *
     * 1    nro_entreg N(5)
     * 2    cbu C(22)
     * 3    nro_operac N(7)
     * 4    nro_cuota N(3)
     * 5    fe_vto (dd/mm/AAAA)
     * 6    nombre C(30)
     * 7    importe N(10) sin punto decimal
     *
     * @param type $campos
     *
     */
    function arma_string_debito_comercial($campos){

//        $campos = array(
//                                        0 => $cbu,
//                                        1 => $idDebito,
//                                        2 => $liquidacionSocioId,
//                                        3 => $fechaDebito,
//                                        4 => $importeDebito,
//                                        5 => $bcoComercioNroPartida,
//                                        6 => $apenom,
//                                        7 => $socioId,
//                                        8 => $registroNro,
//                                        9 => $liquidacionID
//        );

        $IDU = str_pad($campos[7], 5, '0', STR_PAD_LEFT).str_pad($campos[9], 4, '0', STR_PAD_LEFT).str_pad($campos[8], 2, '0', STR_PAD_LEFT);
        $campos[6] = str_replace(',', ' ', $campos[6]);

        // $NRO_OPE = str_pad($campos[7], 4, '0', STR_PAD_LEFT).str_pad($campos[9], 3, '0', STR_PAD_LEFT);
//         $NRO_OPE = str_pad($campos[7], 7, '0', STR_PAD_LEFT);
        
        
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $BASE = (isset($INI_FILE['intercambio']['bcomercial_base_nro_operacion']) ? intval($INI_FILE['intercambio']['bcomercial_base_nro_operacion']) : 0);
        $NRO_OPE = $BASE + intval($campos[7]);
        
        
        $NRO_CUOTA = intval(str_pad($campos[8],3,0,STR_PAD_RIGHT)) + intval(substr($campos[9],-3));

        $cadena = "";
        $cadena .= $campos[5];
        $cadena .= ";";
        $cadena .= $campos[0];
        $cadena .= ";";
        $cadena .= $NRO_OPE;
        $cadena .= ";";
        $cadena .= $NRO_CUOTA;
        $cadena .= ";";
        $cadena .= date('d/m/Y', strtotime($campos[3]));
        $cadena .= ";";
        $cadena .= substr(str_pad('ID'.$IDU.utf8_decode($campos[6]),30,' ',STR_PAD_RIGHT),0,30);
        $cadena .= ";";
        $cadena .= number_format($campos[4] * 100,0,"","");
        $cadena .= "\r\n";
        return $cadena;
    }

	/**
     *
     * ZENRISE (formato CSV)
     *
     * 1    nro_entreg N(5)
     * 2    cbu C(22)
     * 3    nro_operac N(7)
     * 4    nro_cuota N(3)
     * 5    fe_vto (dd/mm/AAAA)
     * 6    nombre C(30)
     * 7    importe N(10) sin punto decimal
     *
     * @param type $campos
     *
     */
    function arma_string_zenrise($campos){
        
        foreach($campos as $key => $value){
            $campos[$key] = str_replace(";","", $value);
        }

        $cadena = $campos[0]; //NOMBRE
				$cadena .= ";";
        $cadena .= $campos[1]; //NOMBRE
        $cadena .= ";";
        $cadena .= $campos[2];
        $cadena .= ";";
				$cadena .= $campos[3];
        $cadena .= ";";
        $cadena .= $campos[4];
				$cadena .= ";";
        $cadena .= $campos[5];
				$cadena .= ";";
        $cadena .= $campos[6];
				$cadena .= ";";
        $cadena .= $campos[7];
				$cadena .= ";";
        $cadena .= $campos[8];
				$cadena .= ";";
        $cadena .= $campos[9];
				$cadena .= ";";
        $cadena .= $campos[10];
				$cadena .= ";";
        $cadena .= $campos[11];
				$cadena .= ";";
        $cadena .= $campos[12];
				$cadena .= ";";
        $cadena .= $campos[13];
				$cadena .= ";";
        $cadena .= $campos[14];
				$cadena .= ";";
        $cadena .= $campos[15];
				$cadena .= ";";
        $cadena .= $campos[16];
				$cadena .= ";";
        $cadena .= $campos[17];
				$cadena .= ";";
				$cadena .= $campos[18];
				$cadena .= ";";
        $cadena .= $campos[19];
				$cadena .= ";";
				$cadena .= $campos[20];
				$cadena .= ";";
        $cadena .= $campos[21];
				$cadena .= ";";
        $cadena .= $campos[22];
        $cadena .= "\r\n";
        return $cadena;
    }


    function decode_str_debito_banco_comercial($cadena){

        $campos = array();
        $campos['nroope'] = substr($cadena,0,7);
        $campos['nrocuo'] = substr($cadena,7,3);
        $campos['nombre'] = substr($cadena,10,40);
        $campos['importe'] = substr($cadena,50,7);
        $campos['intento'] = substr($cadena,57,2);
        $campos['fecha1'] = substr($cadena,59,8);
        $campos['fecha2'] = substr($cadena,67,8);
        $campos['respuesta'] = substr($cadena,75,10);
        $campos['motivo'] = substr($cadena,85,50);
        $campos['empresa'] = substr($cadena,135,30);
        $campos['cbu'] = substr($cadena,165,22);

        if(substr($campos['nombre'],0,3) == 'IDD'){
            $campos['idclien'] = substr($campos['nombre'],3,11);
        }else{
            $campos['idclien'] = substr($campos['nombre'],2,11);
        }



        $iddebito = $this->decode_iddebito_general($campos['idclien'],TRUE);

        $datos = array();
        $datos['socio_id'] = $iddebito['socio_id'];
        $datos['liquidacion_id'] = $iddebito['liquidacion_id'];
        $datos['importe_debitado'] = intval($campos['importe']) / pow(10, 2);

        $datos['status'] = substr(str_pad(trim($campos['motivo']),3,0,STR_PAD_LEFT),0,3);

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();
        $datos['indica_pago'] = ($oCODIGO->isCodigoPago("00300",(!empty($datos['status']) ? $datos['status'] : "ERR")) ? 1 : 0);
        $datos['fecha_debito'] = date('Y-m-d',strtotime(substr($campos['fecha1'],0,4)."-".substr($campos['fecha1'],4,2)."-".substr($campos['fecha1'],-2)));
        $datos['ref_univ'] = $campos['idclien'];

        return $datos;
    }


    /**
     * Archivo de intercambio banco Roela
     * @param type $campos
     * @param type $tipoRegistro
     */
    public function arma_str_debito_roela($campos,$tipoRegistro = 'D'){

        $cadena = "";

        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $concepto = (isset($INI_FILE['intercambio']['roela_empresa_cprestamo']) && $INI_FILE['intercambio']['roela_empresa_cprestamo'] != 0 ? $INI_FILE['intercambio']['roela_empresa_cprestamo'] : '');
        $cuit_empresa = (isset($INI_FILE['intercambio']['roela_empresa_cuit']) && $INI_FILE['intercambio']['roela_empresa_cuit'] != 0 ? $INI_FILE['intercambio']['roela_empresa_cuit'] : '00000000000');
        $codigo_empresa = (isset($INI_FILE['intercambio']['roela_empresa_idempresa']) && $INI_FILE['intercambio']['roela_empresa_idempresa'] != 0 ? $INI_FILE['intercambio']['roela_empresa_idempresa'] : '0');

        #HEADER
        if($tipoRegistro == 'H'){
            /**
             * 0 = fechaProceso
             */
            $cadena .= "0";
            $cadena .= '400';
            $cadena .= '0000';
            $cadena .= date('Ymd', strtotime($campos[0]));
            $cadena .= str_pad("",264,"0",STR_PAD_LEFT);
        }

        #DETALLE
        /**
         * 0 NDOC
         * 1 iddMin
         * 2 fechaDebito
         * 3 importe
         */
        if($tipoRegistro == 'D'){

            $nroReferencia = "0".str_pad($campos[0],8,"0",STR_PAD_LEFT).str_pad($codigo_empresa,10,"0",STR_PAD_LEFT);

            $cadena .= "5";
            $cadena .= $nroReferencia;
            $cadena .= str_pad($campos[1],15,"0",STR_PAD_LEFT);
            $cadena .= "0";
            $cadena .= date('my', strtotime($campos[2]));
            $cadena .= "0";
            $cadena .= date('Ymd', strtotime($campos[2]));
            $cadena .= str_pad(number_format($campos[3] * 100,0,"",""), 11, '0', STR_PAD_LEFT);
            $cadena .= date('Ymd', strtotime($campos[2]));
            $cadena .= str_pad(number_format($campos[3] * 100,0,"",""), 11, '0', STR_PAD_LEFT);
            $cadena .= date('Ymd', strtotime($campos[2]));
            $cadena .= str_pad(number_format($campos[3] * 100,0,"",""), 11, '0', STR_PAD_LEFT);
            $cadena .= str_pad("",19,"0",STR_PAD_LEFT);
            $cadena .= str_pad($nroReferencia,19,"0",STR_PAD_LEFT);
            $cadena .= str_pad($concepto,40," ",STR_PAD_RIGHT);
            $cadena .= str_pad($concepto,15," ",STR_PAD_RIGHT);
            $cadena .= str_pad("",60," ",STR_PAD_LEFT);
            $cadena .= str_pad("",29,"0",STR_PAD_LEFT);
        }


        if($tipoRegistro == 'T'){
            /**
             * 0 = fechaProceso
             * 1 = registros
             */
            $cadena .= "9";
            $cadena .= '400';
            $cadena .= '0000';
            $cadena .= date('Ymd', strtotime($campos[0]));
            $cadena .= str_pad(trim($campos[1]),7,"0",STR_PAD_LEFT);
            $cadena .= str_pad("",7,"0",STR_PAD_LEFT);
            $cadena .= str_pad(number_format($campos[2] * 100,0,"",""), 11, '0', STR_PAD_LEFT);
            $cadena .= str_pad("",239,"0",STR_PAD_LEFT);
        }

        $cadena .= "\r\n";
        return $cadena;
    }


    public function decode_str_debito_banco_itau($cadena){
        $campos = array();
        $campos['cuit'] = substr($cadena,0,11);
        $campos['producto'] = substr($cadena,11,3);
        $campos['convenio'] = substr($cadena,14,6);
        $campos['nrorendi'] = substr($cadena,20,5);
        $campos['fechagen'] = substr($cadena,25,14);
        $campos['tiporeg'] = substr($cadena,39,1);
        $campos['idcliente'] = substr($cadena,40,22);
        $campos['tdoc'] = substr($cadena,62,2);
        $campos['ndoc'] = substr($cadena,64,11);
        $campos['filler1'] = substr($cadena,75,11);
        $campos['idope'] = substr($cadena,86,19);
        $campos['codintr'] = substr($cadena,105,2);
        $campos['cbu'] = substr($cadena,107,22);
        $campos['filler2'] = substr($cadena,129,7);
        $campos['secintr'] = substr($cadena,136,5);
        $campos['fpagintr'] = substr($cadena,141,8);
        $campos['importe'] = substr($cadena,149,17);
        $campos['filler3'] = substr($cadena,166,8);
        $campos['fechintr'] = substr($cadena,174,8);
        $campos['fechacred'] = substr($cadena,182,8);
        $campos['filler4'] = substr($cadena,190,65);
        $campos['codesta'] = substr($cadena,255,3);
        $campos['desesta'] = substr($cadena,258,8);
        $campos['fecesta'] = substr($cadena,266,8);
        $campos['filler5'] = substr($cadena,274,3);
        $campos['motrech'] = substr($cadena,277,2);
        $campos['filler6'] = substr($cadena,279,521);

        $iddebito = $this->decode_iddebito_general($campos['idcliente'],TRUE);
        $datos = array();
        $datos['socio_id'] = $iddebito['socio_id'];
        $datos['liquidacion_id'] = $iddebito['liquidacion_id'];
        $datos['importe_debitado'] = intval($campos['importe']) / pow(10, 2);

        $datos['status'] = substr(str_pad(trim($campos['motrech']),3,0,STR_PAD_LEFT),0,3);

        #SI ES UN REVERSO EN LA POSICION 283 TIENE UNA LETRA S
        #Analizarlo en el $campos['filler6']
        $campos['reverso'] = substr($campos['filler6'],3,1);
        if(!empty($campos['reverso']) && $campos['reverso'] == 'S'){$datos['status'] = '095';}

        App::import('Model','Config.BancoRendicionCodigo');
        $oCODIGO = new BancoRendicionCodigo();
        $datos['indica_pago'] = ($oCODIGO->isCodigoPago("00259",(!empty($datos['status']) ? $datos['status'] : "ERR")) ? 1 : 0);
        $datos['fecha_debito'] = date('Y-m-d',strtotime(substr($campos['fechintr'],0,4)."-".substr($campos['fechintr'],4,2)."-".substr($campos['fechintr'],-2)));
        $datos['ref_univ'] = $campos['idcliente'];

        return $datos;
    }

    /**
     * FIRSDATA
     * 1    8   NRO COMERCIO
     * 2    1   TIPO REGISTRO
     * 3    16  NRO TARJETA
     * 4    12  REFERENCIA (MISMO VALOR PARA MISMA TARJETA)
     * 5    3   CUOTAS (999)
     * 6    3   CUOTA PLAN 000
     * 7    2   FRECUENCIA DEBITO 01
     * 8    11  IMPORTE (9,2)
     * 9    5   PERIODO (FILLER CON ESPACIOS)
     * 10   1   FILLER (ESPACIO)
     * 11   6   VTO PAGO DDMMAA
     * 12   40  DATOS AUXILIARES (FILLER ESPACIOS)
     * 13   92  FILLER (ESPACIOS)
     * 
     * @param unknown $campos
     * @return string
     */
    
    public function arma_str_debito_firsdata($campos,$tipoRegistro = 'D'){
        
//         $campos = array(
//              0 => $socioCuitCuil,
//              1 => $nroTarjeta,
//              2 => $sucursal,
//              3 => $apenom,
//              4 => $cuenta,
//              5 => $importeDebito,
//              6 => $liquidacionID,
//              9 => $socioId,
//              10 => $liquidacionSocioId,
//              11 => $fechaDebito,
//              12 => str_pad($socioId,5,"0",STR_PAD_LEFT).str_pad($liquidacionID,4,"0",STR_PAD_LEFT).str_pad($registroNro,2,"0",STR_PAD_LEFT),
//         );
        
      
            
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $firstDataComercio = (isset($INI_FILE['intercambio']['firsdata_comercio']) && $INI_FILE['intercambio']['firsdata_comercio'] != 0 ? $INI_FILE['intercambio']['firsdata_comercio'] : '');
        
        
        $cadena = "";
       
        
        if($tipoRegistro == 'H') {
            
            // 0 fechaPresenta
            // 1 registros
            // 2 importeTotal
            
            $cadena .= str_pad($firstDataComercio,8,"0",STR_PAD_LEFT);
            $cadena .= '1';
            $cadena .= date('dmy', strtotime($campos[0]));
            $cadena .= str_pad($campos[1],7,"0",STR_PAD_LEFT);
            $cadena .= '0';
            $cadena .= str_pad(number_format($campos[2] * 100,0,"",""), 14, '0', STR_PAD_LEFT);
            $cadena .= str_pad("",91," ",STR_PAD_RIGHT);
            
        }
        
        
        if($tipoRegistro == 'D') {
            
            $cadena .= str_pad($firstDataComercio,8,"0",STR_PAD_LEFT);
            $cadena .= '2';
            $cadena .= trim($campos[1]);
            $cadena .= str_pad($campos[0],12,"0",STR_PAD_LEFT);
            $cadena .= '001';
            $cadena .= '001';
            $cadena .= '01';
            $cadena .= str_pad(number_format($campos[5] * 100,0,"",""), 11, '0', STR_PAD_LEFT);
            $cadena .= str_pad("",5," ",STR_PAD_RIGHT);
            $cadena .= str_pad("",1," ",STR_PAD_RIGHT);
            $cadena .= date('dmy', strtotime($campos[11]));
            $cadena .= str_pad($campos[12],40," ",STR_PAD_RIGHT);
            $cadena .= str_pad("",20," ",STR_PAD_RIGHT);
        }
        

        
        $cadena .= "\r\n";
        
        return $cadena;
        
    }
    
    function arma_str_debito_fenanjor_cordoba($campos) {
        
    }
    

    public function getRegistroDecodificado($bancoIntercambio,$registro){

        $decode = NULL;

        #############################################################################################
        #BANCO DE CORDOBA
        #############################################################################################
        if ($bancoIntercambio == '00020') {
                $decode = $this->decodeStringDebitoBcoCba($registro);
        }

        #############################################################################################
        #STANDAR BANK
        #############################################################################################
        if ($bancoIntercambio == '00430') {
                $decode = $this->decodeStringDebitoStandarBank($registro);
        }

        #############################################################################################
        #BANCO NACION
        #############################################################################################
        if ($bancoIntercambio == '00011') {
                $decode = $this->decodeStringDebitoBcoNacion($registro);
        }


        #############################################################################################
        #BANCO CREDICOOP
        #############################################################################################
        if ($bancoIntercambio == '00191') {
                $decode = $this->decodeStringDebitoBancoCredicoop($registro);
        }

        #############################################################################################
        #SANTANDER RIO
        #############################################################################################
        $bancos = array('00072','90072');
        if(in_array($bancoIntercambio,$bancos)){$decode = $this->decodeStringDebitoBancoSantanderRio($registro);}

        #############################################################################################
        #BANCO MUTUAL
        #############################################################################################
        if($bancoIntercambio == '99999') $decode = $this->decodeStringDebitoMutual($registro);


        #############################################################################################
        #BANCO GALICIA
        #############################################################################################
        if ($bancoIntercambio == '00007') {
                $decode = $this->decodeStringDebitoBancoGalicia($registro);
        }

        #############################################################################################
        #MARGEN COMERCIAL
        #############################################################################################
        if ($bancoIntercambio == '99900') {
                $decode = $this->decodeStringLoteIntercambioGeneral($registro);
        }

		#############################################################################################
        #ZENRISE
        #############################################################################################
        $bancos = array('99997','99998');
        if (in_array($bancoIntercambio,$bancos)) {
                $decode = $this->decodeStringDebitoZenrise($registro);
								/*debug($decode);
								exit();*/
        }

        #############################################################################################
        #BANCO BBVA
        #############################################################################################

        $bancosFrances= array('00017','90017','91017','91117','91217','92017','92117','92217');
        if(in_array($bancoIntercambio, $bancosFrances)){$decode = $this->decodeStringDebitoBancoFrances($registro);}

        #############################################################################################
        #COBRO DIGITAL
        #############################################################################################
        if ($bancoIntercambio == '99910') {
                $decode = $this->decodeStringDebitoCobroDigital($registro);
        }

        #############################################################################################
        #BANCO COMAFI
        #############################################################################################
        $bancosComafi = array('00299', '90299');
        if (in_array($bancoIntercambio, $bancosComafi)) {
                $decode = $this->decode_str_debito_banco_comafi($registro);
        }

        #############################################################################################
        #BANCO NACION *** ISSAR ***
        #############################################################################################
        if ($bancoIntercambio == '90011') {
            $decode = $this->decodeStringDebitoBcoNacion($registro, $bancoIntercambio);
        }

        #############################################################################################
        #BANCO NACION *** OTROS ***
        #############################################################################################
        if ($bancoIntercambio == '91011') {
            $decode = $this->decodeStringDebitoBcoNacion($registro, $bancoIntercambio);
        }
        

        #############################################################################################
        #BANCO CUENCA
        #############################################################################################
        if ($bancoIntercambio == '65203') {
                $decode = $this->decode_str_debito_cuenca($registro);
        }

        #############################################################################################
        # FENANJOR NACION -- 99920
        #############################################################################################
        if($bancoIntercambio == '99920'){
            $decode = $this->decodeStringDebitoBcoNacion($registro, $bancoIntercambio);
        }


        #############################################################################################
        # FENANJOR MACRO -- 99921
        #############################################################################################
        if($bancoIntercambio == '99921'){
            $decode = $this->decode_str_debito_fenanjor_macro($registro);
        }

        #############################################################################################
        #BANCO MACRO
        #############################################################################################
        if ($bancoIntercambio == '00285') {
                $decode = $this->decode_str_debito_banco_macro($registro);
        }

        #############################################################################################
        #BANCO MACRO *** BARRIDO ***
        #############################################################################################
        if ($bancoIntercambio == '90285') {
                $decode = $this->decode_str_debito_banco_macro_barrido($registro);
        }

        #############################################################################################
        # CONOCRED -- 99950
        #############################################################################################
        if($bancoIntercambio == '99950'){
            $decode = $this->decodeStringDebitoBcoNacion($registro);
        }

        #############################################################################################
        #BANCO DE COMERCIO
        #############################################################################################
        if ($bancoIntercambio == '00300') {
            $decode = $this->decode_str_debito_banco_comercial($registro);
        }

        #############################################################################################
        #BANCO COINAG
        #############################################################################################
        if ($bancoIntercambio == '00431') {
            $decode = $this->decode_str_debito_coinag($registro);
        }
        
        #############################################################################################
        #BANCO COINAG - M22S
        #############################################################################################
        if ($bancoIntercambio == '90431') {
            $decode = $this->decode_str_debito_coinag($registro, '90431');
        }        

        #############################################################################################
        #BANCO ITAU
        #############################################################################################
        if ($bancoIntercambio == '00259') {
            $decode = $this->decode_str_debito_banco_itau($registro);
        }
        
        #############################################################################################
        # ADSUS * NACION *
        #############################################################################################
        if($bancoIntercambio == '99960'){
            $decode = $this->decodeStringDebitoBcoNacion($registro, $bancoIntercambio);
        }

    	if (empty($decode)) {
            return false;
        }

        return $decode;
    }


    function arma_csv_row($campos, $separator = ';') {        
        foreach($campos as $key => $value){
            $campos[$key] = str_replace(";","", $value);
        }
        $cadena = implode($separator, $campos);
        $cadena .= "\r\n";
        return $cadena;        
    }

    function get_cba_liquidacion($cadena,$start=97,$offset=4,$length=4){       
        $decode = $this->decodeStringDebitoBcoCba($cadena);
        return ($decode['socio_id'] !== 0 ? $decode['liquidacion_id'] : 0);
    }
    
}
?>

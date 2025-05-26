<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/xtcpdf.php');

class MutualProductoSolicitudPDF extends XTCPDF{
    
    var $PIE = true;
    var $HEADER = true;
    var $INI_Y = null;
    var $H;
    var $INI_FILE = NULL;    
    
	function MutualProductoSolicitudPDF($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false){
                parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
                $this->INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
	}
    
	/**
	 * redefino el metodo Footer para que no imprima el pie de pagina por defecto
	 */
	function Footer(){
        if($this->PIE){parent::Footer();}
    }
    
	function Header(){if($this->HEADER) parent::Header ();}   
    
    private function checkImpresionAutorizacionDebito($bancoCaller,$tipoOrdenDto,$bancoId){
        $ini = parse_ini_file(CONFIGS.'mutual.ini', true);
        if (!isset($ini['autorizacion_debito'])) {
            return false;
        }
        if(!isset($ini['autorizacion_debito'][$bancoCaller])) {
            return false;
        }
        if(!isset($ini['autorizacion_debito']['imprimir']) && $bancoCaller != $bancoId) {
            return false;
        }
        if($ini['autorizacion_debito']['imprimir'] == 'UNA' && $bancoCaller != $bancoId) {
            return false;
        }
        $tipos = NULL;
        if(isset($ini['autorizacion_debito'][$bancoId])) {
            $tipos = explode(",", $ini['autorizacion_debito'][$bancoId]);
        }
        if (!is_array($tipos) || empty($tipos)) {
            return false;
        }
        return in_array($tipoOrdenDto, $tipos);
    }
    
    function imprimirAutorizacionBancoNacion($logo,$orden){
        
        if(!$this->checkImpresionAutorizacionDebito('00011',$orden['MutualProductoSolicitud']['tipo_orden_dto'],$orden['MutualProductoSolicitud']['beneficio_banco_id'])){
            return;
        }
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);
        $this->Rect($this->GetX(),$this->GetY(),190,230);
        $this->Rect($this->GetX(),$this->GetY(),55,16);
        $this->Rect($this->GetX() + 55,$this->GetY(),135,16);
        $this->image($logo,11,10,40);

//         $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
//         $this->SetY(21);
//         $this->Cell(55,5,"CUIT 30-50001091-2",0,0,'C');

        $this->SetY(12);
        $this->SetX(65);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(135,5,"SERVICIO DEBITOS NACION",0,0,'C');

        $this->SetY(17);
        $this->SetX(65);
        $this->Cell(135,5,"CARTA AUTORIZACION",0,0,'C');

        $this->SetY(26);
        $this->SetX(10);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(20,5,"Sucursal:",0,0,'L');
        $this->SetX(30);
        $this->Cell(120,5,"",'B',0,'L');
        $this->SetX(150);
        $this->Cell(15,5,"Código:",0,0,'L');
        $this->SetX(165);
        $this->Cell(34,5,"",'B',0,'L');

        $this->SetY(31);
        $this->SetX(10);
        $this->Cell(60,5,"EMPRESA/MUTUAL/ASOCIACION:",0,0,'L');
        $this->SetX(70);
        $this->Cell(129,5,"",'B',0,'L');

        $this->SetY(36);
        $this->SetX(11);
        $this->Cell(188,5,"",'B',0,'L');

        $this->SetY(41);
        $this->SetX(10);
        $this->Cell(32,5,"Tipo nro de Cuenta",'',0,'L');	
        $this->SetX(42);
        $this->Cell(75,5,"",'B',0,'L');
        $this->SetX(117);
        $this->Cell(47,5,"Nro de cl/socio p/la empresa:",'',0,'L');
        $this->SetX(164);
        $this->Cell(35,5,"",'B',0,'L');

        $this->SetY(47);
        $this->SetX(10);
        $this->Cell(194,5,"Lugar y Fecha ________________________________, ______________ de ____________________ de __________",'',0,'L');

        $this->SetY(53);
        $this->SetX(10);
        $TEXTO = "En mi/nuestro carácter de titular/es de la cuenta citada, solicito/amos al Banco de la Nación Argentina que considere la posibilidad de mi/nuestra adhesión al sistema del título, para aplicar al pago del importe informado por la Empresa/Mutual/Asociación arriba mencionada, correspondiente a todos los gastos y consumos que realice a través de la misma. Asimismo me/nos notifico/amos que, en caso de acceder el Banco a la presente solicitud, el sistema se regirá por las condiciones que se describen a continuación, que declaro/amos conocer:";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Los importes enviados mensualmente por la Empresa/Mutual/Asociación serán debitados de mi/nuestra cuenta al momento de registrarse la acreditación de mis/nuestros haberes y/o en forma inmediata cuando se corrobore la existencia de saldo de conformidad a la información que bajo su exclusiva responsabilidad la Empresa/Mutual/Asociación brinde al Banco, en los términos del convenio oportunamente celebrado entre ambas entidades.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Autorizo/amos al BANCO DE LA NACIÓN ARGENTINA para que brinde a la Empresa/Mutual/Asociación la información correspondiente a los datos identificatorios de la cuenta a través de la cual se llevará a cabo la operatoria que solicito/amos, eximiéndolo de toda responsabilidad al respecto.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "En caso que el servicio no se halle a mi/nuestro nombre, debo/eremos acompañar a la presente una autorización del titular del mismo facultándome a pagar dicha deuda.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "El Banco podrá procesar las modificaciones de oficio del número de clientes generados por la Empresa/Mutual/Asociación, a efectos de continuar vinculado al débito, sin otra autorización más que la presente.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Se podrá ordenar la suspensión de un débito hasta el día hábil anterior -inclusive- a la fecha de vencimiento y la alternativa de revertir débitos por el total de cada operación, ante una instrucción expresa mediante la suscripción del F.63010 \"Solicitud de Reversión\" dentro de los 30 (treinta) días corridos contados desde la fecha del débito. La devolución será efectuada dentro de las 72 (setenta y dos) horas hábiles siguientes a la fecha en que el BANCO reciba la instrucción del cliente, siempre que la Empresa/Mutual/Asociación no se oponga a la reversión por haberse hecho efectiva la diferencia de facturación en forma directa, conforme lo prevé la normativa vigente del BCRA sobre Reglamentación de Cuentas de Depósito- Reversión de Débitos Automáticos.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "A partir de la firma de la presente tomo conocimiento que las cuotas serán debitadas de mi/nuestra cuenta, considerando para ello la fecha en que la Empresa/Mutual/Asociación haga entrega de esta carta autorización al Banco, considerando para ello:";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "1- Solicitud presentada hasta el día 15 del mes de cita, los débitos comenzarán a efectuarse con el depósito del sueldo del mes en curso y/o el saldo a partir de la fecha de presentación.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "2- Si fuera presentado con posterioridad al día 15, los débitos comenzarán a efectuarse sobre el depósito de los haberes del mes siguiente o sobre el saldo a partir de la fecha de presentación.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Será a mi/nuestro exclusivo cargo y responsabilidad efectuar todos los reclamos y/o aclaraciones que pudieran suscitarse con la Empresa/Mutual/Asociación por los débitos realizados en mi cuenta.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "El BANCO DE LA NACIÓN ARGENTINA podrá dejar de prestar este servicio por las siguientes razones:";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "1. Falta de fondos suficientes al momento de corresponder un débito.";
        $TEXTO .= "\n";
        $TEXTO .= "2. Cierre de la cuenta bancaria debido a cualquiera de las causas previstas en las normas en vigencia.";
        $TEXTO .= "\n";
        $TEXTO .= "3. Por voluntad del suscripto.";
        $TEXTO .= "\n";
        $TEXTO .= "4. Por otras causas a criterio del Banco.";
        $TEXTO .= "\n";
        $TEXTO .= "5. Por decisión de la Empresa.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "En mi/nuestro carácter de titular/es de la cuenta arriba aludida, autorizo/amos al Banco de la Nación Argentina a debitar el importe informado por la Empresa/Mutual/asociación correspondiente a y por todos los gastos y consumos que haya realizado a través de la misma, careciendo de derecho a reclamo alguno al Banco sobre los citados débitos.";
        $TEXTO .= "\n";
        $TEXTO .= "En prueba de conformidad, y declarando conocer y aceptar los términos de la presente, firmo/amos este ejemplar en _______________________ a los _____ días del mes de ___________ de ______";
        $TEXTO .= "\n";


        $this->MultiCell(0,11,$TEXTO,0,'J');	

        $this->ln(10);
        $this->SetX(15);
        $this->Cell(15,5,"F-60440",'B',0,'C');
        $this->ln(5);
        $this->SetX(15);
        $this->Cell(15,5,"OCT/15",'',0,'C');

        $this->ln(15);
        $this->marcaParaFirmaDigital(50, 50);
        
        // $this->ln(5);
        $this->SetX(50);
        $this->Cell(45,5,"Firma",'T',0,'C');

        $this->SetX(140);
        $this->Cell(45,5,"Firma",'T',0,'C');


        $this->ln(10);
        $this->SetX(50);
        $this->Cell(45,5,"Aclaración",'T',0,'C');

        $this->SetX(140);
        $this->Cell(45,5,"Aclaración",'T',0,'C');        
    }
    
    
	function imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor,$tipo="Solicitud de Préstamo"){

		$size = 10;
                if(!empty($nroSolicitud)){
                        $this->linea[1] = array(
				'posx' => 110,
				'ancho' => 65,
				'texto' => utf8_decode("$tipo N°"),
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
                        );
                        $this->linea[2] = array(
                                        'posx' => 175,
                                        'ancho' => 25,
                                        'texto' => $nroSolicitud,
                                        'borde' => '',
                                        'align' => 'R',
                                        'fondo' => 0,
                                        'style' => 'B',
                                        'colorf' => '#D8DBD4',
                                        'size' => $size + 3
                        );
                        $this->Imprimir_linea();
                }

		$this->SetFont('courier','',12);
		$size = 10;
                if(!empty($fecha)){
                        $this->linea[1] = array(
				'posx' => 150,
				'ancho' => 20,
				'texto' => "Fecha:",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
                        );
                        $this->linea[2] = array(
                                        'posx' => 170,
                                        'ancho' => 30,
                                        'texto' => $fecha,
                                        'borde' => '',
                                        'align' => 'R',
                                        'fondo' => 0,
                                        'style' => 'B',
                                        'colorf' => '#D8DBD4',
                                        'size' => $size
                        );
                        $this->Imprimir_linea();
                }
                if(!empty($usuario)){
                        $this->linea[2] = array(
				'posx' => 130,
				'ancho' => 40,
				'texto' => "Emitida por usuario:",
				'borde' => '',
				'align' => 'R',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size - 2
                        );
                        $this->linea[3] = array(
                                        'posx' => 185,
                                        'ancho' => 15,
                                        'texto' => $usuario,
                                        'borde' => '',
                                        'align' => 'R',
                                        'fondo' => 0,
                                        'style' => '',
                                        'colorf' => '#D8DBD4',
                                        'size' => $size - 2
                        );
                        $this->Imprimir_linea();
                }

		if(!empty($vendedor)){
			$this->linea[1] = array(
					'posx' => 100,
					'ancho' => 100,
					'texto' => "Vendedor: $vendedor",
					'borde' => '',
					'align' => 'R',
					'fondo' => 0,
					'style' => '',
					'colorf' => '#D8DBD4',
					'size' => $size - 2
			);
			$this->Imprimir_linea();				
		}
		$this->ln(4);		
	}
 
	function imprimirDatosTitular($orden,$soloApenomTdocNdoc = FALSE,$header=TRUE){
		
		$size = 10;
		$sized = 13;		
		if($header){
                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 190,
                                    'texto' => "Datos del Titular",
                                    'borde' => 'B',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->Imprimir_linea();
                    $this->ln(2);                    
                }
		
		
		$this->linea[1] = array(
				'posx' => 10,
				'ancho' => 55,
				'texto' => "Apellido y Nombres:",
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $sized
                );
                
                $apenom = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']);


                $BORDER = (empty($orden) ? 'B' : '');

		$this->linea[2] = array(
				'posx' => 65,
				'ancho' => 135,
				'texto' => $apenom,
				'borde' => $BORDER,
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $sized
		);
		$this->Imprimir_linea();
		
		
		$this->linea[1] = array(
				'posx' => 10,
				'ancho' => 30,
				'texto' => utf8_decode("DNI/LC/LE N°:"),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->linea[2] = array(
				'posx' => 40,
				'ancho' => 30,
				'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_tdocndoc']),
				'borde' => $BORDER,
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->linea[3] = array(
				'posx' => 100,
				'ancho' => 30,
				'texto' => utf8_decode("CUIT/CUIL N°:"),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->linea[4] = array(
				'posx' => 130,
				'ancho' => 30,
				'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_cuit_cuil']),
				'borde' => $BORDER,
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->Imprimir_linea();
                
                if(!$soloApenomTdocNdoc){
                    
                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 45,
                                    'texto' => utf8_decode("Fecha de Nacimiento:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[2] = array(
                                    'posx' => 55,
                                    'ancho' => 30,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_fecha_nacimiento']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->linea[3] = array(
                                    'posx' => 90,
                                    'ancho' => 12,
                                    'texto' => utf8_decode("Edad:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[4] = array(
                                    'posx' => 102,
                                    'ancho' => 10,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_edad']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->linea[5] = array(
                                    'posx' => 130,
                                    'ancho' => 30,
                                    'texto' => utf8_decode("Estado Civil:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[6] = array(
                                    'posx' => 160,
                                    'ancho' => 40,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_estado_civil']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->Imprimir_linea();
                    #############################################################################################
                    # DOMICILIO
                    #############################################################################################
                    $this->ln(2);
                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 60,
                                    'texto' => utf8_decode("Domicilio Particular Calle:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[2] = array(
                                    'posx' => 70,
                                    'ancho' => 70,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_calle']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[3] = array(
                                    'posx' => 140,
                                    'ancho' => 10,
                                    'texto' => utf8_decode("N°:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[4] = array(
                                    'posx' => 150,
                                    'ancho' => 10,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_numero_calle']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[5] = array(
                                    'posx' => 160,
                                    'ancho' => 25,
                                    'texto' => utf8_decode("Piso/Dpto.:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[6] = array(
                                    'posx' => 185,
                                    'ancho' => 15,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_piso']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->Imprimir_linea();	

                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 17,
                                    'texto' => utf8_decode("Barrio:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[2] = array(
                                    'posx' => 27,
                                    'ancho' => 43,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_barrio']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->linea[3] = array(
                                    'posx' => 70,
                                    'ancho' => 25,
                                    'texto' => utf8_decode("Localidad:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[4] = array(
                                    'posx' => 95,
                                    'ancho' => 40,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_localidad']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->linea[5] = array(
                                    'posx' => 135,
                                    'ancho' => 7,
                                    'texto' => utf8_decode("CP:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[6] = array(
                                    'posx' => 142,
                                    'ancho' => 10,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_cp']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->linea[7] = array(
                                    'posx' => 152,
                                    'ancho' => 22,
                                    'texto' => utf8_decode("Provincia:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[8] = array(
                                    'posx' => 174,
                                    'ancho' => 26,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_provincia']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->Imprimir_linea();



                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 15,
                                    'texto' => utf8_decode("Tel.:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[2] = array(
                                    'posx' => 25,
                                    'ancho' => 110,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_telefono_fijo']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->linea[3] = array(
                                    'posx' => 135,
                                    'ancho' => 20,
                                    'texto' => utf8_decode("Tel.Cel.:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[4] = array(
                                    'posx' => 155,
                                    'ancho' => 45,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_telefono_movil']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );


                    $this->Imprimir_linea();


                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 25,
                                    'texto' => utf8_decode("Referencia:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[2] = array(
                                    'posx' => 35,
                                    'ancho' => 90,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_persona_referencia']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->linea[3] = array(
                                    'posx' => 125,
                                    'ancho' => 15,
                                    'texto' => utf8_decode("Tel.:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[4] = array(
                                    'posx' => 140,
                                    'ancho' => 50,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_telefono_referencia']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );


                    $this->Imprimir_linea();                    
                    
                }
		

		
	}
	
	function imprimirDatosCuentaDebito($orden,$soloBanco=FALSE){
		$size = 10;
		$sized = 13;		

		$this->ln(4);
		$this->linea[1] = array(
				'posx' => 10,
				'ancho' => 190,
				'texto' => utf8_decode("Datos de la Cuenta Bancaria para el débito de la cuota"),
				'borde' => 'B',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->Imprimir_linea();
		$this->ln(2);
                
                $BORDER = (empty($orden) ? 'B' : '');
		
		$this->linea[1] = array(
				'posx' => 10,
				'ancho' => 15,
				'texto' => utf8_decode("Banco:"),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->linea[2] = array(
				'posx' => 25,
				'ancho' => 110,
				'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficio_banco']),
				'borde' => $BORDER,
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->linea[3] = array(
				'posx' => 135,
				'ancho' => 25,
				'texto' => utf8_decode("Sucursal:"),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->linea[4] = array(
				'posx' => 160,
				'ancho' => 30,
				'texto' =>  utf8_decode($orden['MutualProductoSolicitud']['beneficio_sucursal']),
				'borde' => $BORDER,
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->Imprimir_linea();
		
		
		
		$this->linea[1] = array(
				'posx' => 10,
				'ancho' => 50,
				'texto' => utf8_decode("Tipo de Cuenta: C.A./Cta.Cte."),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		
		$this->linea[2] = array(
				'posx' => 80,
				'ancho' => 30,
				'texto' => utf8_decode("N° de Cuenta:"),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->linea[3] = array(
				'posx' => 110,
				'ancho' => 60,
				'texto' =>  utf8_decode($orden['MutualProductoSolicitud']['beneficio_cuenta']),
				'borde' => $BORDER,
				'align' => 'L',
				'fondo' => 0,
				'style' => '',
				'colorf' => '#D8DBD4',
				'size' => $size
		);
		$this->Imprimir_linea();
		
		$this->ln(2);
		
		$this->linea[1] = array(
				'posx' => 10,
				'ancho' => 35,
				'texto' => utf8_decode("N° de CBU:"),
				'borde' => '',
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $sized
		);
		$this->linea[2] = array(
				'posx' => 45,
				'ancho' => 110,
				'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficio_cbu']),
				'borde' => $BORDER,
				'align' => 'L',
				'fondo' => 0,
				'style' => 'B',
				'colorf' => '#D8DBD4',
				'size' => $sized
		);
		$this->Imprimir_linea();
		if(!$soloBanco){
                    
                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 65,
                                    'texto' => utf8_decode("Organismo de Liq. de Haberes:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[2] = array(
                                    'posx' => 75,
                                    'ancho' => 55,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['organismo_desc']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[3] = array(
                                    'posx' => 130,
                                    'ancho' => 32,
                                    'texto' => "Fecha de Ing.:",
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[4] = array(
                                    'posx' => 162,
                                    'ancho' => 22,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficio_ingreso']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[5] = array(
                                    'posx' => 184,
                                    'ancho' => 16,
                                    'texto' => utf8_decode("(".$orden['MutualProductoSolicitud']['beneficio_antiguedad']." años)"),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->Imprimir_linea();


                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 55,
                                    'texto' => utf8_decode("N° de Beneficio / Legajo:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[2] = array(
                                    'posx' => 65,
                                    'ancho' => 40,
                        'texto' => (!empty($orden['MutualProductoSolicitud']['beneficio_legajo']) ? utf8_decode($orden['MutualProductoSolicitud']['beneficio_legajo']) : !empty($orden['MutualProductoSolicitud']['beneficio_str']) ? $orden['MutualProductoSolicitud']['beneficio_str']:""),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );

                    $this->Imprimir_linea();


                    $this->linea[1] = array(
                                    'posx' => 10,
                                    'ancho' => 30,
                                    'texto' => utf8_decode("Lugar de Pago:"),
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );
                    $this->linea[2] = array(
                                    'posx' => 40,
                                    'ancho' => 160,
                                    'texto' => utf8_decode($orden['MutualProductoSolicitud']['turno_desc']),
                                    'borde' => $BORDER,
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '#D8DBD4',
                                    'size' => $size
                    );


                    $this->Imprimir_linea();


                    
                    if(isset($orden['MutualProductoSolicitud']['beneficio_tarjeta_numero']) && !empty($orden['MutualProductoSolicitud']['beneficio_tarjeta_numero'])){
                        $this->linea[1] = array(
                                        'posx' => 10,
                                        'ancho' => 190,
                                        'texto' => utf8_decode("Tarjeta de Débito: " . $orden['MutualProductoSolicitud']['beneficio_tarjeta_numero'] . " | Titular: " . $orden['MutualProductoSolicitud']['beneficio_tarjeta_titular']),
                                        'borde' => '',
                                        'align' => 'L',
                                        'fondo' => 0,
                                        'style' => 'B',
                                        'colorf' => '#D8DBD4',
                                        'size' => $size
                        );                    
                        $this->Imprimir_linea();
                    } 
                    if(isset($orden['MutualProductoSolicitud']['sueldo_neto']) && !empty($orden['MutualProductoSolicitud']['sueldo_neto'])){
                        $this->linea[1] = array(
                                        'posx' => 10,
                                        'ancho' => 30,
                                        'texto' => utf8_decode("Sueldo Neto: " . number_format($orden['MutualProductoSolicitud']['sueldo_neto'],2,'.',''). " - Debitos Bancarios: " . number_format($orden['MutualProductoSolicitud']['debitos_bancarios'],2,'.','')),
                                        'borde' => '',
                                        'align' => 'L',
                                        'fondo' => 0,
                                        'style' => '',
                                        'colorf' => '#D8DBD4',
                                        'size' => $size
                        );                    
                        $this->Imprimir_linea();
                    }                                         
                    
                }

                
                
	}    
    
    
    
    function imprimirPagare($orden,$printNroSolicitud = true,$vtoBlank =true,$emiteBlank=true, $imprimeMonto = true){
        
       
        $size = 10;
        if($printNroSolicitud):
            $this->linea[1] = array(
                    'posx' => 110,
                    'ancho' => 65,
                    'texto' => utf8_decode("Solicitud de Préstamo N°"),
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => $size
            );
            $this->linea[2] = array(
                    'posx' => 175,
                    'ancho' => 25,
                    'texto' => $orden['MutualProductoSolicitud']['nro_print'],
                    'borde' => '',
                    'align' => 'R',
                    'fondo' => 0,
                    'style' => 'B',
                    'colorf' => '#D8DBD4',
                    'size' => $size
            );
            $this->Imprimir_linea();
        endif;
        $this->ln(4);
        $size = 20;
        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "P A G A R E",
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();

        $this->ln(10);
        $size = 12;
        
        $nroPagare = $orden['MutualProductoSolicitud']['nro_print'];
        

        
        if($vtoBlank){$vtoPagare = "____ de _______________ de ______";}
        if($emiteBlank){$vtoPagare = "_____ de __________________ de ________";}

        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 50,
                'texto' => utf8_decode("N° $nroPagare"),
                'borde' => 'TBLR',
                'align' => 'L',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size + 1
        );
        $this->Imprimir_linea();
        $this->ln(5);

        $localidadPagare = strtoupper(($orden['MutualProductoSolicitud']['proveedor_localidad_pagare'] ? $orden['MutualProductoSolicitud']['proveedor_localidad_pagare'] : '_________________'));
        
        $fechaPagare =  $localidadPagare . ", _____ de __________________ de ________";
        $vtoPagare = "____ de _______________ de ______";
        $aQuienPago = "_________________________________________";
        $recibidoEn = "efectivo";
        $domiPago = "_______________________________________";


        if(!$orden['MutualProductoSolicitud']['proveedor_pagare_blank']){
            $fechaPagare = $localidadPagare
            .", "
                .$orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']
                ." de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']
                . " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'];
                
//                 $vtoPagare = $orden['MutualProductoSolicitud']['vencimiento_pagare_str']['dia']['numero']
//                 ." de " . $orden['MutualProductoSolicitud']['vencimiento_pagare_str']['mes']['string']
//                 . " de " . $orden['MutualProductoSolicitud']['vencimiento_pagare_str']['anio']['numero'];
                $aQuienPago = $orden['MutualProductoSolicitud']['proveedor_full_name'];
                // $recibidoEn = $orden['MutualProductoSolicitud']['proveedor'];
                $domiPago = $orden['MutualProductoSolicitud']['proveedor_pagare_direccion'];
                
        }
        

        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => utf8_decode($fechaPagare),
                'borde' => '',
                'align' => 'R',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );

        // debug($orden);
        
        $montoNumero = ($imprimeMonto ? "$ ". number_format($orden['MutualProductoSolicitud']['importe_total'],2, ',', '.') : '');
        $montoLetras = ($imprimeMonto ? $orden['MutualProductoSolicitud']['total_letras'] : '_______________________________________');

        $this->Imprimir_linea();
        $this->ln(5);
        $this->linea[1] = array(
                'posx' => 150,
                'ancho' => 50,
                'texto' => $montoNumero,
                'borde' => 'TBLR',
                'align' => 'R',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size + 1
        );
        $this->Imprimir_linea();

        

        $TXT_PAGARE = "El $vtoPagare PAGARE SIN PROTESTO (Art. 50 Decreto ";
        $TXT_PAGARE .= "Ley 5965/63) a $aQuienPago ";
        $TXT_PAGARE .= "o a su órden la cantidad de PESOS ".$montoLetras." por igual valor recibido ";
        $TXT_PAGARE .= "en $recibidoEn a mi entera satisfacción, pagadero en $domiPago.-\n";
        $this->ln(5);
        $this->SetFontSizeConf($size);
        $this->MultiCell(0,20,$TXT_PAGARE,0,'J',0,2);

        $this->ln(10);

        $Y = $this->GetY();
        $size = 7;
        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 90,
                'texto' => utf8_decode("Firmante: " . $orden['MutualProductoSolicitud']['beneficiario_apenom']),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 90,
                'texto' => "Tipo y Nro. Doc.: " . $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'],
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();
//        $this->linea[1] = array(
//                'posx' => 10,
//                'ancho' => 90,
//                'texto' => utf8_decode("Domicilio: " . $orden['MutualProductoSolicitud']['beneficiario_domicilio']),
//                'borde' => '',
//                'align' => 'L',
//                'fondo' => 0,
//                'style' => '',
//                'colorf' => '#D8DBD4',
//                'size' => $size
//        );
//        $this->Imprimir_linea();
//        $this->linea[1] = array(
//                'posx' => 10,
//                'ancho' => 90,
//                'texto' => utf8_decode("Teléfonos: " . $orden['MutualProductoSolicitud']['beneficiario_telefonos']),
//                'borde' => '',
//                'align' => 'L',
//                'fondo' => 0,
//                'style' => '',
//                'colorf' => '#D8DBD4',
//                'size' => $size
//        );
//        $this->Imprimir_linea();

        $this->setY($Y);
        $this->ln(2);
        
        $this->marcaParaFirmaDigital(130, 50);
        
        $this->linea[1] = array(
                'posx' => 130,
                'ancho' => 50,
                'texto' => "Firma",
                'borde' => 'T',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(5);
        $this->linea[1] = array(
                'posx' => 130,
                'ancho' => 50,
                'texto' => utf8_decode("Aclaración"),
                'borde' => 'T',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(5);
        $this->linea[1] = array(
                'posx' => 130,
                'ancho' => 50,
                'texto' => utf8_decode("N° Documento"),
                'borde' => 'T',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();        
    }
    
    
    function imprimirAutorizacionDebitoBcoCordoba($orden){
        
        if(!$this->checkImpresionAutorizacionDebito('00020',$orden['MutualProductoSolicitud']['tipo_orden_dto'],$orden['MutualProductoSolicitud']['beneficio_banco_id'])){
            return;
        }        
        
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $auto_debito_bcocba = (isset($INI_FILE['autorizacion_debito']['auto_debito_bcocba']) ? $INI_FILE['autorizacion_debito']['auto_debito_bcocba'] : '___________________________________');
        
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);

        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"AUTORIZACION DE DEBITOS DE CAJA DE AHORRO",0,0,'C');  
        $this->SetY(15);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',15);
        $this->Cell(190,5,"BANCO DE LA PROVINCIA DE CORDOBA",0,0,'C');  
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $this->SetY(25);
        
        $TEXTO = "Por la presente faculto irrevocablemente al Banco de la Provincia de Córdoba a ";
        $TEXTO .= "debitar sin previo aviso, de mis Cuentas de Ahorro en dicha institución bancaria ";
        $TEXTO .= "y que al momento es la Caja de Ahorro Nº ".utf8_decode($orden['MutualProductoSolicitud']['beneficio_cuenta'])." ";
        $TEXTO .= "de la Filial Nro ".utf8_decode($orden['MutualProductoSolicitud']['beneficio_sucursal'])." ";
        $TEXTO .= "y/o en las cuentas y sucursales que en el futuro mi empleador me deposite mis haberes mensuales ";
        $TEXTO .= "hasta cubrir el total de Pesos ".$orden['MutualProductoSolicitud']['total_letras']." ($ ".number_format($orden['MutualProductoSolicitud']['importe_total'],2)."), ";
        $TEXTO .= " más la cuota social, gastos e intereses que se devenguen por el incumplimiento de las condiciones pautadas en la presente operación y que además tenga fondos acreditados ";
        $TEXTO .= "en dichas cuentas, los importes informados por \"$auto_debito_bcocba\", y/o ";
        $TEXTO .= "____________________________";
        $TEXTO .= "como gestora de cobro, a la/s que autorizo a debitar de dichas cuentas, originadas en negocios jurídicos ";
        $TEXTO .= "convenidos por mí en \"$auto_debito_bcocba\" . Me obligo a disponer ";
        $TEXTO .= "de fondos suficientes, al momento que los débitos se realicen.--------\n";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        
        
        $this->MultiCell(0,15,$TEXTO,0,'J');
        
        $this->ln(15);
        $this->firmaSocio();

        $TEXTO = "\n";
        $TEXTO .= "\n";        
        $TEXTO .= "P/Certificación de Firma: ______________________________________\n";

        
        $this->MultiCell(0,15,$TEXTO,0,'J');
        
    }     
    
    
    function imprimirAutorizacionDebitoMargenComercial($orden){
        
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',14);
        $this->Cell(0,5,"Margen Comercial SA",0);
        $this->Ln(5);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(0,5,"Av. Velez Sarsfield 94 - EP of. 9 - CORDOBA",0);
        $this->Ln(3);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(0,5,"TEL: 0351 4225706 - email: info@margenweb.com.ar",0);
        $this->Ln(4);

       
        $this->SetY(25);
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"AUTORIZACION DE COBRANZA POR DEBITO DIRECTO",0,0,'C');  
        $this->SetY(30);
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        $TEXTO = "Por la presente, AUTORIZO a MARGEN COMERCIAL S.A., a debitar de mi Cuenta Corriente / Caja de ahorros detallada en la presente, los montos que correspondan según el plan comercial al que adhiero, en forma mensual y consecutiva a partir de la fecha de aprobación de la operación solicitada, en un todo de acuerdo con los datos consignados en esta autorización.\n";
        $this->MultiCell(0,11,$TEXTO);

        $this->SetY(50);
        $this->imprimirDatosTitular($orden);
        $size = 10;
        $sized = 13;

        #############################################################################################
        # CUENTA PARA DEBITO
        #############################################################################################
        $this->imprimirDatosCuentaDebito($orden);
        
        #############################################################################################
        # DATOS PLAN COMERCIAL
        #############################################################################################
        $this->ln(4);
        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => utf8_decode("Datos del Plan Comercial"),
                'borde' => 'B',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(4);


        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 55,
                'texto' => utf8_decode("EMPRESA PROVEEDORA:"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $sized
        );
        $this->linea[2] = array(
                'posx' => 65,
                'ancho' => 90,
                'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_full_name']),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $sized
        );
        $this->linea[3] = array(
                'posx' => 155,
                'ancho' => 15,
                'texto' => utf8_decode("CUIT:"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $sized
        );
        $this->linea[4] = array(
                'posx' => 170,
                'ancho' => 30,
                'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_cuit']),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $sized
        );
        $this->Imprimir_linea();


        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 55,
                'texto' => utf8_decode("Monto Total Autorizado: $"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[2] = array(
                'posx' => 65,
                'ancho' => 22,
                'texto' => number_format($orden['MutualProductoSolicitud']['importe_total'], 2),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[3] = array(
                'posx' => 87,
                'ancho' => 43,
                'texto' => utf8_decode("Cantidad de Cuotas:"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[4] = array(
                'posx' => 130,
                'ancho' => 7,
                'texto' => utf8_decode($orden['MutualProductoSolicitud']['cuotas_print']),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[5] = array(
                'posx' => 137,
                'ancho' => 40,
                'texto' => utf8_decode("Monto de Cuota: $"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[6] = array(
                'posx' => 177,
                'ancho' => 23,
                'texto' => number_format($orden['MutualProductoSolicitud']['importe_cuota'], 2),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();

        #############################################################################################
        # TEXTO
        #############################################################################################
        $this->ln(4);
        $this->SetFontSize(10);
        $TEXTO = "Dejo Expresa constancia que en caso de no poderse realizar la cobranza de la forma pactada, AUTORIZO en forma expresa a MARGEN COMERCIAL S.A. o a la empresa proveedora, a seguir realizando los descuentos correspondientes a la total cancelación de las obligaciones por mi contraídas en este acto, con mas los intereses y gastos por mora que pudieren corresponder.-\n\n";
        $TEXTO .= "Asimismo, AUTORIZO en forma expresa a MARGEN COMERCIAL S.A. a que en caso de no poseer fondos de manera consecutiva en la cuenta indicada, o de cambiar la cuenta en la que se acreditan mis haberes, la cobranza sea direccionada a la cuenta de mi titularidad que posea fondos para la cancelación de las obligaciones contraídas.-\n";
        $this->MultiCell(0,11,$TEXTO);
        $this->ln(2);
        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "GASTOS ADMINISTRATIVOS",
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => 11
        );
        $this->Imprimir_linea();
        $this->SetFontSize(11);
        $TEXTO = "AUTORIZO en forma expresa a MARGEN COMERCIAL S.A. a debitar de mi Cuenta Bancaria el gasto administrativo mensual que se genera por la presente cobranza, el cual tendrá vigencia mientras exista saldo deudor de la operación detallada en el presente, y la cobranza de dicho saldo sea gestionado por MARGEN COMERCIAL S.A..-\n";
        $this->MultiCell(0,11,$TEXTO);
        $this->ln(20);

        $this->firmaSocio();

        $this->ln(4);
        $this->barCode($orden['MutualProductoSolicitud']['barcode']);        
        
        
        
    }
    
    
    function imprimeAutoPagoDirectoSantanderRio($orden){
        $this->AddPage();

        $this->reset();
        $this->SetY(10);
        $this->SetX(10);

        $this->SetFont(PDF_FONT_NAME_MAIN,'B',14);
        $this->Cell(0,5,$this->membrete['L1'],0);
        $this->Ln(5);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(0,5,$this->membrete['L2'],0);
        $this->Ln(3);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(0,5,$this->membrete['L3'],0);
        $this->Ln(4);


        $this->SetY(30);

        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"AUTORIZACION DE PAGO DIRECTO - BANCO SANTANDER RIO S.A.",0,0,'C');  
        $this->SetY(40);

        $TEXTO = "";
        $TEXTO .= "Estimado Asociado:";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "         Nos complace informarles que hemos celebrado un convenio con el BANCO SANTANDER RIO S.A., con el objeto de facilitar los canales de pago de las cuotas mensuales y todo otro valor debido a la Mutual, incorporando, de esta manera una nueva modalidad de cobranza a las ya existentes.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "         Las condiciones actuales de bancarización del país, facilitan la utilización de cuentas bancarias (cuentas corrientes o cajas de ahorro) para la cancelación de obligaciones periódicas, por lo cual nuestra Mutual aprovechando las facilidades practicas y funcionales de nuevos canales de cobro, implementará a partir del _______ el servicio de Pago Directo poniéndolo a disposición de nuestros asociados.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "         Bastará con que cada uno tenga una cuenta en cualquier banco del mercado y dé su conformidad. Los datos necesarios que el asociado debe informar, para debitar las cuotas mencionadas son los siguientes: CBU (clave bancaria única), número y nombre del asociado, CUIT o CUIL.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "         El BANCO SANTANDER RIO S.A., con los datos antes mencionados, cargará los mismos en el sistema de Pago Directo y procederá a debitar las obligaciones de los asociados en las fechas y con los montos preacordados, como es habitual.- El asociado deberá contar con fondos suficientes en su cuenta, actuando el resumen de la misma como comprobante legal del pago efectuado, ahorrando así pérdidas de tiempo y trámites innecesarios de parte de nuestros asociados.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";

        //$this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        //$this->Cell(190,5,"ADHESION A PAGO DIRECTO",0,0,'C');
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        $this->MultiCell(0,11,$TEXTO,0,'J');
        $this->ln(2);
        $this->Rect($this->GetX(),$this->GetY(),190,120);
        $this->ln(3);

        $TEXTO = "";
        $TEXTO .= "POR MEDIO DE LA PRESENTE Y CON MI FIRMA ESTAMPADA AUTORIZO A REALIZAR LA OPERATORIA DE PAGO DIRECTO CORRESPONDIENTE AL SISTEMA NACIONAL DE PAGOS, REGLAMENTADO POR EL B.C.R.A. EN SUS COMUNICACIONES A2559 y MODIF ; PARA LA CANCELACION DE MIS OBLIGACIONES DE PAGO DE CUOTAS DE LA ". strtoupper(Configure::read('APLICACION.nombre_fantasia')).".-";
        $TEXTO .= "\n";
        //$this->SetFont(PDF_FONT_NAME_MAIN,'B',10);
        $this->MultiCell(0,11,$TEXTO,0,'J');
        $this->ln(5);
        $this->reset();
        $this->linea[1] = array(
                    'posx' => 10,
                    'ancho' => 75,
                    'texto' => "CBU (CLAVE BANCARIA UNICA) NUMERO:",
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->linea[2] = array(
                    'posx' => 85,
                    'ancho' => 50,
                    'texto' => $orden['MutualProductoSolicitud']['beneficio_cbu'],
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => 'B',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                    'posx' => 10,
                    'ancho' => 75,
                    'texto' => "ASOCIADO NUMERO:",
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->linea[2] = array(
                    'posx' => 85,
                    'ancho' => 50,
                    'texto' => $orden['MutualProductoSolicitud']['socio_id'],
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => 'B',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                    'posx' => 10,
                    'ancho' => 75,
                    'texto' => "APELLIDO Y NOMBRES:",
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->linea[2] = array(
                    'posx' => 85,
                    'ancho' => 50,
                    'texto' => $orden['MutualProductoSolicitud']['beneficiario_apenom'],
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => 'B',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                    'posx' => 10,
                    'ancho' => 75,
                    'texto' => "CUIT o CUIL NUMERO:",
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->linea[2] = array(
                    'posx' => 85,
                    'ancho' => 50,
                    'texto' => $orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'],
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => 'B',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                    'posx' => 10,
                    'ancho' => 75,
                    'texto' => "DOCUMENTO TIPO Y NUMERO:",
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->linea[2] = array(
                    'posx' => 85,
                    'ancho' => 50,
                    'texto' => $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'],
                    'borde' => '',
                    'align' => 'L',
                    'fondo' => 0,
                    'style' => 'B',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->Imprimir_linea();

        $this->ln(25);
        $this->linea[1] = array(
                    'posx' => 120,
                    'ancho' => 40,
                    'texto' => "FIRMA Y ACLARACION",
                    'borde' => 'T',
                    'align' => 'C',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->Imprimir_linea();        
    }
    
    
    function imprimeMinutaMutuo($orden){
        
        $this->PIE = false;
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);

        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"SOLICITUD DE PRESTAMOS PERSONALES",0,0,'C');    
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        $this->SetY(20);
        
        $tna = "_____%";
        $tem = "_____%";
        $cft = "_____%";

        $capital = "$ _________";
        $interes = "$ _________";

        if(!empty($orden['MutualProductoSolicitud']['tna'])){ $tna = number_format($orden['MutualProductoSolicitud']['tna'],2) . "%";}
        if(!empty($orden['MutualProductoSolicitud']['tnm'])){ $tem = number_format($orden['MutualProductoSolicitud']['tnm'],2) . "%";}
        if(!empty($orden['MutualProductoSolicitud']['cft'])){ $cft = number_format($orden['MutualProductoSolicitud']['cft'],2) . "%";}
        
        if(!empty($orden['MutualProductoSolicitud']['capital_puro'])){
            $capital = "$ ".number_format($orden['MutualProductoSolicitud']['capital_puro'],2);
            $interes = "$ ".number_format($orden['MutualProductoSolicitud']['importe_cuota'] - $orden['MutualProductoSolicitud']['capital_puro'],2);
        }

        $TEXTO = "";
        $TEXTO .= "Por el presente vengo a solicitar a ".$orden['MutualProductoSolicitud']['proveedor_full_name']." ";
        $TEXTO .= "un préstamo por la suma de pesos ".trim($orden['MutualProductoSolicitud']['total_letras'])." ($ ".number_format($orden['MutualProductoSolicitud']['importe_total'],2)."), ";
        $TEXTO .= "por el que me comprometo a restituir en ". trim($orden['MutualProductoSolicitud']['cantidad_cuota_letras'])." (".$orden['MutualProductoSolicitud']['cuotas_print'].") ";
        $TEXTO .= "cuotas, iguales y consecutivas, del 1 al 10 de cada mes, a partir del mes siguiente del que me sea entregado. Si la fecha para el pago coincidiera con un día inhábil, sábado o domingo, pagaré el día hábil inmediato siguiente.";
        $TEXTO .= "\n";
        $TEXTO .= "En este acto se me INFORMA, y así lo entiendo, las condiciones para el otorgamiento del crédito: EL INTERES A PAGAR: El pago de intereses será en la misma cantidad de cuotas y con idénticos vencimientos que el pactado para la devolución del capital. La tasa de interés que devengará esta operación será del $tna nominal anual (TNA), equivalente a una tasa efectiva mensual del $tem (TEAM), lo que hace un costo Financiero Total de $cft (CFT) por ciento efectivo anual. La mencionada tasa permanecerá invariable a lo largo de toda la operación;";
        $TEXTO .= "\n";
        $TEXTO .= "LA MODALIDAD DE PAGO: Las cuotas mensuales pactadas (capital, intereses y demás rubros que se generen o devenguen) se abonarán a través del débito en caja de ahorro (condición esencial para evaluar el otorgamiento). ";
        $TEXTO .= "Excepcionalmente podré pagar en efectivo en el domicilio del otorgante del crédito; EL LUGAR DE PAGO: En el caso que no se pudiere debitar de la caja de ahorro informada por el solicitante, la cuota mensual o se optare pagar en efectivo, el lugar de pago es en calle __________________________, N° ___ de esta ciudad de ___________________, provincia de ______________; ";
        $TEXTO .= "LA MORA: La falta de pago hará incurrir en mora de pleno derecho, sin necesidad de interpelación alguna, por la que devengará un interés mensual del 50% del interés compensatorio, capitalizable cada 6 meses. También hará incurrir en mora si por cualquier situación no se pudiere debitar la cuota del mes y no concurriere dentro de las 72hs a hacer efectivo el pago en el domicilio del otorgante del crédito; ";
        $TEXTO .= " y LA ACEPTACION: En caso de ser aceptada la presente solicitud, se firmará un CONTRATO PARA PRÉSTAMOS PERSONALES por el que se regirá la relación entre las partes (solicitante y otorgante del crédito).- ";
        $TEXTO .= "\n";
        $TEXTO .= "Entiendo que la presente MINUTA DE SOLICITUD DE PRÉSTAMOS PERSONALES, queda supeditada al análisis y posterior aprobación del otorgante del crédito.";
        $TEXTO .= "\n";
        $TEXTO .= "Acompaño a la presente mis datos personales para que se evalúe el otorgamiento del Préstamo Personal, como la documental requerida. ";
        $TEXTO .= strtoupper("Declaro bajo juramento que los datos son precisos, exactos y fieles a la verdad, haciendome responsable por la falsedad y/o parcialidad de los mismos.");
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "TITULAR: ".$orden['MutualProductoSolicitud']['beneficiario'];
        $TEXTO .= "\n";
        $TEXTO .= "PRODUCTO: ".$orden['MutualProductoSolicitud']['producto'];
        $TEXTO .= "\n";
        $TEXTO .= "BANCO: ".$orden['MutualProductoSolicitud']['beneficio_banco'];
        $TEXTO .= "\n";
        $TEXTO .= "SUCURSAL: ".$orden['MutualProductoSolicitud']['beneficio_sucursal'];
        $TEXTO .= "\n";
        $TEXTO .= "CUENTA NRO: ".$orden['MutualProductoSolicitud']['beneficio_cuenta'];
        $TEXTO .= "\n";
        $TEXTO .= "CBU: ".$orden['MutualProductoSolicitud']['beneficio_cbu'];
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J'); 
        $this->ln(25);
        
        $this->firmaSocio();
   
    }
    
    
    function imprimeContratoMutuo($orden){
        
        $this->PIE = false;
        
        $this->AddPage();
        
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);
//         $this->setFooterMargin(15);
        $this->SetAutoPageBreak(true,20);

        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"CONTRATO PARA PRESTAMOS PERSONALES",0,0,'C');    
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        $this->SetY(20);

        $tna = "____________________________ porciento (_____%)";
        $tem = "____________________________ porciento (_____%)";
        $cft = "____________________________ porciento (_____%)";
        $tea = "____________________________ porciento (_____%)";

        $capital = "$ _________";
        $interes = "$ _________";


        if(!empty($orden['MutualProductoSolicitud']['tna']) && $orden['MutualProductoSolicitud']['tna'] != 0){ 
            $tna = number_format($orden['MutualProductoSolicitud']['tna'],2) . "%";
            if(!empty($orden['MutualProductoSolicitud']['tnm']) && $orden['MutualProductoSolicitud']['tnm'] != 0){ 
                $tem = number_format($orden['MutualProductoSolicitud']['tnm'],2) . "%";
                if(!empty($orden['MutualProductoSolicitud']['cft']) && $orden['MutualProductoSolicitud']['cft'] != 0){ 
                    $cft = number_format($orden['MutualProductoSolicitud']['cft'],2) . "%";
                }
            }
        }
        
        if(!empty($orden['MutualProductoSolicitud']['detalle_calculo_plan'])){
            $objetoCalculado = json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']);
            if(isset($objetoCalculado->tea)){
                $tea = number_format($objetoCalculado->tea,2). "%";
                $metodoDeCalculo = $objetoCalculado->metodoCalculoFormula;
            }
        }
        
        if(!empty($orden['MutualProductoSolicitud']['capital_puro']) && $orden['MutualProductoSolicitud']['capital_puro'] != 0){
            $capital = "$ ".number_format($orden['MutualProductoSolicitud']['capital_puro'],2);
            $interes = "$ ".number_format($orden['MutualProductoSolicitud']['importe_cuota'] - $orden['MutualProductoSolicitud']['capital_puro'],2);
        }
        // debug($tna);
        // debug($orden['MutualProductoSolicitud']['tnm']);
        // debug(!empty($orden['MutualProductoSolicitud']['tnm']));
        // debug($orden['MutualProductoSolicitud']['tnm'] != 0);
        // debug($cft);
        // debug($orden);
        // exit;
        
        $proveedorFullName = $orden['MutualProductoSolicitud']['proveedor_full_name'];
        if($orden['MutualProductoSolicitud']['proveedor_pagare_blank']) {
            $proveedorFullName = "_________________________________________";
        }

        $TEXTO = ""; 
        $TEXTO .= "En la Ciudad de ".$this->INI_FILE['general']['domi_fiscal_localidad'].", Provincia de ".$this->INI_FILE['general']['domi_fiscal_provincia'].", a los ".$orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']." días del mes de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']." del año ".$orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'].", ". $proveedorFullName ." , con domicilio en la calle ______________________ , N° ____ de la ciudad de ________________________, Pvcia. de ______________ , en adelante denominado el \"MUTUANTE/ACREEDOR\" por una parte, y por la otra el Sr/a ".$orden['MutualProductoSolicitud']['beneficiario_apenom'].", ".$orden['MutualProductoSolicitud']['beneficiario_tdocndoc']." , con domicilio en la calle ".$orden['MutualProductoSolicitud']['beneficiario_domicilio']." , en adelante denominado el \"MUTUARIO/DEUDOR\", acuerdan celebrar el presente CONTRATO DE PRESTAMO, en virtud de la Minuta para Solicitud de Préstamos Personales, presentada y aprobada, sujetándose a las siguientes cláusulas:";
        $TEXTO .= "\n";
        $TEXTO .= "PRIMERA: CRÉDITO. El MUTUARIO recibe en este acto y su a entera satisfacción, de parte del MUTUANTE, la suma solicitada de Pesos ".trim($orden['MutualProductoSolicitud']['total_importe_solicitado_letras'])." ($ ". number_format($orden['MutualProductoSolicitud']['importe_solicitado'],2).") en efectivo y cuyo destino es para CONSUMO.-";
        $TEXTO .= "\n";
        $TEXTO .= "SEGUNDA: DEVOLUCIÓN DEL CRÉDITO. El PRÉSTAMO  será restituido al MUTUANTE en ". trim($orden['MutualProductoSolicitud']['cantidad_cuota_letras'])." (".$orden['MutualProductoSolicitud']['cuotas_print'].") cuotas mensuales, iguales y consecutivas, con vencimiento la primera de ellas el día ".date('d/m/Y',strtotime($orden['MutualProductoSolicitud']['primer_vto_socio']))." y las restantes del 1 al 10 de cada mes sub-siguiente. Las cuotas fijas serán comprensivas de capital e intereses pactados de modo tal que todas las cuotas representen igual valor. El pago de la cuota cuyo vencimiento coincidiera con un día inhábil, sábado o domingo, se producirá indefectiblemente el día hábil inmediato siguiente.-";
        $TEXTO .= "\n";
        $TEXTO .= "TERCERA: INTERESES COMPENSATORIOS. El CRÉDITO devengará una tasa de interés nominal anual – T.N.A. - del $tna, equivalente a una tasa de interés efectiva anual – T.E.A. - del $tea lo que hace un Costo Financiero Total – C.F.T. - del $cft, estas se mantendrán fijas en todas las cuotas de amortización.-";
        $TEXTO .= "\n";
        $TEXTO .= "CUARTA: CUOTA MENSUAL. El valor de la cuota mensual se compone de capital en la suma de $capital y el interés mensual por la suma de $interes haciendo una suma total a pagar mensualmente de PESOS ".trim($orden['MutualProductoSolicitud']['total_cuota_letras'])." ($ ". number_format($orden['MutualProductoSolicitud']['importe_cuota'],2) .").-";
        $TEXTO .= "\n";
        $TEXTO .= "QUINTA: GRAVÁMENES. Todo gasto, sellado e importe que grave la operación estarán a cargo del DEUDOR.-";
        $TEXTO .= "\n";
        $TEXTO .= "SEXTA: AUTORIZACIÓN DE DÉBITO DEL PAGO. El DEUDOR da mandato a favor del MUTUANTE, hasta la total cancelación del préstamo, para que debite mensualmente de su cuenta de Caja de Ahorro Nro. ".$orden['MutualProductoSolicitud']['beneficio_cuenta'].", CBU ".$orden['MutualProductoSolicitud']['beneficio_cbu']." abierta en el BANCO ".$orden['MutualProductoSolicitud']['beneficio_banco']." Sucursal ".$orden['MutualProductoSolicitud']['beneficio_sucursal'].", el importe dinerario para cancelar la cuota del mes en curso de acuerdo a lo descripto en el punto segundo del presente contrato. Aceptada esta modalidad de pago por el MUTUARIO, se comprometerá a verificar mensualmente que en su cuenta se debite el valor de la cuota. En caso de no debitarse deberá, dentro de las 72 hs. posteriores al vencimiento de la cuota, concurrir al domicilio del MUTUANTE a abonar en efectivo la cuota no debitada, e informar si se encuentra activo el débito automático para el mes siguiente, caso contrario incurrirá en mora. -";
        $TEXTO .= "\n";
        $TEXTO .= "SÉPTIMA: STOP DEBIT. CIERRE DE CUENTA. CAMBIO DE BANCO. El MUTUA­RIO se obliga irrevocablemente por este acto, a mantener abierta la cuenta de Caja de Ahorro antes denunciada durante el plazo de amortización del presente crédito. Se compromete a no solicitar la cancelación del débito, el cierre de la misma y/o el cambio de Banco. Previo a incurrir en alguna de estas acciones, el MUTUARIO deberá comunicar en forma fehaciente al MUTUANTE mediante carta documento dirigida al domicilio de éste. En caso de incumplimiento harán incurrir en mora de pleno derecho al MUTUARIO, con las consecuencias que ello conlleva (Ver cláusulas MORA y CONSECUENCIA DE LA MORA). Para el otorgamiento del crédito el MUTUANTE tuvo en especial referencia la inclusión del solicitante en el sistema financiero (Bancarizado).-";
        $TEXTO .= "\n";
        $TEXTO .= "OCTAVA: EL PAGO. El pago se realizará en el plazo y en las condiciones antes mencionadas. En el caso de haber optado por pago en efectivo este se hará en el domicilio del MUTUANTE o en el lugar en que éste oportunamente lo indique, dentro de la misma plaza y del horario apertura al público. Asimismo, en el caso que no se pudiere debitar la cuota, el MUTUARIO deberá concurrir en el plazo de 72 hs al domicilio del MUTUANTE para hacer efectivo el pago. Dicho pago no significa novación de la obligación. Las cuotas se imputarán a todos los valores por igual como se ha detallado. El pago parcial no significará espera, quita, transacción o novación de la cuota, la misma se acumulará a la del mes siguiente con más los intereses moratorios que devengue el saldo no abonado.-";
        $TEXTO .= "\n";
        $TEXTO .= "NOVENA: CANCELACIÓN ANTICIPADA: Los plazos se presumen establecidos en beneficio de ambas partes, según lo que de común acuerdo han establecido, dejando a salvo la facultad del DEUDOR de pre-cancelar total o parcialmente el crédito, abonando los intereses devengados hasta la fecha de la pre-cancelación. Con la cancelación anticipada se eliminarán los intereses de las cuotas no vencidas. Para optar por la cancelación anticipada, el crédito no deberá encontrarse en mora. Por dicha operación el MUTUARIO deberá abonar en efectivo un diez por ciento (10%) del capital remanente, lo que el MUTUANTE acepta como compensación razonable por el otorgamiento del crédito, como también todos los gastos, impuestos y costos, que dicha pre-cancelación originare. Podrá optar por la cancelación anticipada en cualquier momento de la relación contractual.-";
        $TEXTO .= "\n";
        $TEXTO .= "DÉCIMA: MORA: La mora se producirá de pleno derecho y sin necesidad de requerimiento o interpelación judicial o extrajudicial alguna, por el simple incumplimiento del MUTUARIO en los plazos pactados de cualquiera de las obligaciones; por la falta de pago de una cuota, o el pago insuficiente o parcial de una de ellas.-";
        $TEXTO .= "\n";
        $TEXTO .= "DÉCIMO PRIMERA: CONSECUENCIAS DE LA MORA – CADUCIDAD DE LOS PLAZOS: El acaecimiento del supuesto de la cláusula anterior, producirá de pleno derecho:  la caducidad de todos los plazos, haciéndose exigible la inmediata e íntegra devolución y reembolso del capital desembolsado por el MUTUANTE, con más los intereses compensatorios y puni­torios pactados hasta la total devolución del capital adeudado con más los intereses moratorios, honorarios y costos que se originen como consecuencia del procedimiento de ejecución, y dará derecho al MUTUANTE de ejecutar este contrato y/o los documentos que instrumenten el préstamo, total o parcialmente, contra el MUTUARIO, y/o sus garantes, avalistas o codeudores, en forma individual o conjunta.-";
        $TEXTO .= "\n";
        $TEXTO .= "DÉCIMO SEGUNDA: INTERÉS PUNITORIO - CAPITALIZACIÓN: En todos los casos de mora, sobre el saldo del capital debido, se calculará un interés punitorio del cincuenta por ciento (50%) del interés compensatorio. Se pacta expresamente que en estos casos, tanto el interés compensatorio como el punitorio, se capitalizarán a partir de los seis meses de mora, en los términos del art. 770 del C. Civil y Comercial de la Nación.-";
        $TEXTO .= "\n";
        $TEXTO .= "DÉCIMO TERCERA: CESIÓN DEL CRÉDITO. El MUTUANTE podrá transferir el presente, por cualquiera de los medios previstos en la ley, adquiriendo el o los cesionarios los mismos beneficios y/o derechos y/o acciones del ACREEDOR bajo el presente contrato. De optar por la cesión prevista en los artículos 70 a 72 de la Ley 24.441, la cesión del crédito y su garantía podrá hacerse sin notificación al DEUDOR y tendrá validez desde su fecha de formalización, en un todo de acuerdo con lo establecido por el artículo 72 de la ley precitada. El MUTUARIO expresamente manifiesta que, tal como lo prevé la mencionada ley, la cesión tendrá efecto desde la fecha en que se opere la misma y que sólo podrá oponer contra el cesionario las excepciones previstas en el mencionado artículo. No obstante, en el supuesto que la cesión implique modificación del domicilio de pago, el nuevo domicilio de pago deberá notificarse en forma fehaciente a la parte deudora. Habiendo mediado modificación del domicilio de pago, no podrá oponerse excepción de pago documentado, en relación a pagos practicados a anteriores cedentes con posterioridad a la notificación del nuevo domicilio de pago.-";
        $TEXTO .= "\n";
        $TEXTO .= "DÉCIMO CUARTA: INFORMACIÓN. El DEUDOR reconoce que ha sido debidamente informado sobre todas las condiciones establecidas para el otorgamiento del Crédito, tanto en la MINUTA DE SOLICITUD DE PRÉSTAMOS PERSONALES como en el CONTRATO PARA PRÉSTAMOS PERSONALES, todo de conformidad con lo dispuesto por el art. 4° de Ley N° 24.240, su reforma por la Ley 26.361 y demás normas que regulan las relaciones de consumo, aceptando conocer su contenido.-";
        $TEXTO .= "\n";
        $TEXTO .= "DÉCIMO QUINTA: DE FORMA. En caso de controversia las partes se someterán a los Tribunales Ordinarios de la Provincia de ".$this->INI_FILE['general']['domi_fiscal_provincia'].", renunciado al fuero federal. De conformidad las partes suscriben dos (2) ejemplares en un mismo tenor y a un sólo efecto, en la ciudad de ".$this->INI_FILE['general']['domi_fiscal_localidad'].", a los ".$orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']." días del mes de  ".$orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']." de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'].".-";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        $this->ln(25);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->linea[0] = array(
                    'posx' => 20,
                    'ancho' => 60,
                    'texto' => "por  MUTUANTE/ACREEDOR",
                    'borde' => 'T',
                    'align' => 'C',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            ); 
        
        $this->marcaParaFirmaDigital(120);
        
        $this->linea[1] = array(
                    'posx' => 120,
                    'ancho' => 60,
                    'texto' => "por  MUTUARIO/DEUDOR",
                    'borde' => 'T',
                    'align' => 'C',
                    'fondo' => 0,
                    'style' => '',
                    'colorf' => '#D8DBD4',
                    'size' => 10
            );
        $this->Imprimir_linea(); 
        $this->reset();
    }
    
    
    function imprimeContratoMutuoAMAN($orden){
        
        $this->PIE = true;
        
        $this->AddPage();
        
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);
        //         $this->setFooterMargin(15);
        $this->SetAutoPageBreak(true,20);
        $size = 12;
        $this->linea[1] = array(
            'posx' => 110,
            'ancho' => 65,
            'texto' => utf8_decode("Solicitud de Préstamo N°"),
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 175,
            'ancho' => 25,
            'texto' => $orden['MutualProductoSolicitud']['nro_print'],
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(10);
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"CONTRATO DE PRÉSTAMO (MUTUO) DINERARIO.",0,0,'C');
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        $this->SetY(30);

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("EMPRESA ________________________________________________"),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(5);
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("CONTRATO DE MUTUO."),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        $this->ln();

        
        $tna = "____________________________ porciento (_____%)";
        $tem = "____________________________ porciento (_____%)";
        $cft = "____________________________ porciento (_____%)";
        $tea = "____________________________ porciento (_____%)";
        
        $capital = "$ _________";
        $interes = "$ _________";
        
        
        if(!empty($orden['MutualProductoSolicitud']['tna']) && $orden['MutualProductoSolicitud']['tna'] != 0){
            $tna = number_format($orden['MutualProductoSolicitud']['tna'],2) . "%";
            if(!empty($orden['MutualProductoSolicitud']['tnm']) && $orden['MutualProductoSolicitud']['tnm'] != 0){
                $tem = number_format($orden['MutualProductoSolicitud']['tnm'],2) . "%";
                if(!empty($orden['MutualProductoSolicitud']['cft']) && $orden['MutualProductoSolicitud']['cft'] != 0){
                    $cft = number_format($orden['MutualProductoSolicitud']['cft'],2) . "%";
                }
            }
        }
        
        if(!empty($orden['MutualProductoSolicitud']['detalle_calculo_plan'])){
            $objetoCalculado = json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']);
            if(isset($objetoCalculado->tea)){
                $tea = number_format($objetoCalculado->tea,2). "%";
                $metodoDeCalculo = $objetoCalculado->metodoCalculoFormula;
            }
        }
        
        if(!empty($orden['MutualProductoSolicitud']['capital_puro']) && $orden['MutualProductoSolicitud']['capital_puro'] != 0){
            $capital = "$ ".number_format($orden['MutualProductoSolicitud']['capital_puro'],2);
            $interes = "$ ".number_format($orden['MutualProductoSolicitud']['importe_cuota'] - $orden['MutualProductoSolicitud']['capital_puro'],2);
        }
        // debug($tna);
        // debug($orden['MutualProductoSolicitud']['tnm']);
        // debug(!empty($orden['MutualProductoSolicitud']['tnm']));
        // debug($orden['MutualProductoSolicitud']['tnm'] != 0);
        // debug($cft);
        // debug($orden);
        // exit;
        
        $proveedorFullName = $orden['MutualProductoSolicitud']['proveedor_full_name'];
        if($orden['MutualProductoSolicitud']['proveedor_pagare_blank']) {
            $proveedorFullName = "_________________________________________";
        }
        
        $nroCta = "_______________";
        if(!empty($orden['MutualProductoSolicitud']['beneficio_cuenta'])){
            $nroCta = $orden['MutualProductoSolicitud']['beneficio_cuenta'];
        }
        
        $nroCbu = "_____________________________";
        if(!empty($orden['MutualProductoSolicitud']['beneficio_cbu'])){
            $nroCbu = $orden['MutualProductoSolicitud']['beneficio_cbu'];
        }
        $banco = "__________________________";
        if(!empty($orden['MutualProductoSolicitud']['beneficio_banco'])){
            $banco = $orden['MutualProductoSolicitud']['beneficio_banco'];
        }
        $sucursal = "______";
        if(!empty($orden['MutualProductoSolicitud']['beneficio_sucursal'])){
            $sucursal = $orden['MutualProductoSolicitud']['beneficio_sucursal'];
        }
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',9);
        
        $TEXTO = "";
        $TEXTO .= "PRELIMINAR(A- Partes) Entre $proveedorFullName, con domicilio en calle _____________________________, «MUTUANTE» y el/la   Sr/Sra. ".$orden['MutualProductoSolicitud']['beneficiario_apenom']." ".$orden['MutualProductoSolicitud']['beneficiario_tdocndoc']." con domicilio en ".$orden['MutualProductoSolicitud']['beneficiario_domicilio'].", en adelante «MUTUARIO», celebran este CONTRATO DE PRÉSTAMO (MUTUO) DE DINERO EN EFECTIVO, sujeto a las estipulaciones siguientes:";
        $TEXTO .= "\n";        
        $TEXTO .= "PRIMERA(I- Objeto) 1-El MUTUANTE da en préstamo a los MUTUARIOS la suma de PESOS ".trim($orden['MutualProductoSolicitud']['total_importe_solicitado_letras'])." ($ ". number_format($orden['MutualProductoSolicitud']['importe_solicitado'],2)."), en efectivo. El mutuario deberá restituir dicha suma en ". trim($orden['MutualProductoSolicitud']['cantidad_cuota_letras'])." (".$orden['MutualProductoSolicitud']['cuotas_print'].") cuotas mensuales, iguales y consecutivas, con vencimiento los días 10 de cada mes, de PESOS ".trim($orden['MutualProductoSolicitud']['total_cuota_letras'])." ($ ".number_format($orden['MutualProductoSolicitud']['importe_cuota'],2).") , lo que hace un total de pesos $ ".number_format($orden['MutualProductoSolicitud']['importe_total'],2)." por aplicación de un interés del $tea efectiva anual, fijo sobre saldo conforme al sistema francés. La primer cuota vence el ___/________/_____. Las cuotas serán abonadas en __________________________. Todo eso de acuerdo al detalle porcentual que se fija en la cláusula séptima. ";        
        $TEXTO .= "\n";
        $TEXTO .= "SEGUNDA (II- Mora) La mora operará de pleno derecho por el solo vencimiento de los términos. En caso de mora, el Deudor deberá abonar al la empresa –en adición a los intereses compensatorios pactados- un interés punitorio equivalente al cincuenta por ciento (50%) de los intereses compensatorios o a la tasa máxima que autorice el Banco Central de la República Argentina, si ésta fuere mayor. ";        
        $TEXTO .= "La falta de pago de dos (2) cuotas consecutivas o alternadas, según lo previsto en la cláusula primera de este contrato, hará caducar el plazo de las restantes, siendo exigible la totalidad del saldo pendiente de pago, quedando el MUTUARIO a su inmediata restitución, sin necesidad de interpelación judicial o extrajudicial alguna.";
        $TEXTO .= "\n";
        $TEXTO .= "TERCERA  Las partes de común acuerdo le reconocen al presente instrumento el carácter de titulo ejecutivo en los términos del alcance del art. 518, inc. 1° del Código Procesal Civil y Comercial  de la Provincia de Córdoba, en concepto de cobro del total del capital e intereses convenidos y/o su saldo correspondientes, por lo que la falta de pago en termino de los mismos facultará a la MUTUANTE a reclamar dichas sumas por la vía ejecutiva respectiva. ";
        $TEXTO .= "\n";
        $TEXTO .= "CUARTA (IV- garantía – renuncia a la inembargabilidad) El MUTUARIO/DEUDOR declara expresamente renunciar al derecho de inembargabilidad sobre los haberes, jubilaciones, pensiones, indemnizaciones u otros beneficios patrimoniales nacionales o provinciales, que percibe actualmente o en un futuro, los cuales afecta voluntariamente a constituirlos como garantía de cumplimiento de la presente obligación hasta un máximo de un 20% mensual de los mismos, por embargo judicial (ley 8024 Art. 45 inc. C; Ley 24.241 Art. 14 Inc. C; Decreto Ley 6.754; LEY N° 22.919 art. 22; entre otros)";
        $TEXTO .= "\n";
        $TEXTO .= "QUINTA (V- libramiento de pagare) El Mutuario acepta expresamente garantizar la deuda en un pagare el cual se librará por el monto total adeudado, con la misma fecha de libramiento del presente. El Mutuante podrá a su sola opción, iniciar la ejecución con cualquiera de los documentos que prefiera, accionar contra el Mutuante/Deudor y/o sus garantes, avalistas o codeudores, en forma individual o conjunta. Una vez cancelado el crédito, a requerimiento del Mutuario, se restituirá el documento pagare.";
        $TEXTO .= "\n";
        $TEXTO .= "SEXTA (VI- Impuestos, tributos, gastos extras, seguros y adicionales) (descuentos en el monto inicial del crédito) Los impuestos que puedan alcanzar a este acto serán pagados por el mutuario, a ser los siguientes: a) Comisión por Colocación b) Impuesto a los sellos c) Recupero de Impuestos a los Débitos y Créditos Bancarios d) Gastos de análisis crediticios.";
        $TEXTO .= "\n";
        $TEXTO .= "SEPTIMA (VII- información de tasas y condiciones de la operación)";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        $this->imprimeTablaCalculo($orden);
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',9);
        $TEXTO = "";
        $TEXTO .= "OCTAVA (VIII- Litigio) A todos los efectos legales, las partes se someterán a los tribunales ordinarios de la ciudad de Córdoba, con expresa renuncia a cualquier otra jurisdicción que pudiera corresponderles. ";
        $TEXTO .= "\n";
        $TEXTO .= "NOVENA (IX- Firmas e instrumentación) En prueba de conformidad se firman dos (2) ejemplares de un mismo tenor y a un sólo efecto y se entregan a cada partes. ";
        $TEXTO .= "\n";
        $TEXTO .= "DECIMA (X- Lugar y fecha) Dado en ".$this->INI_FILE['general']['domi_fiscal_provincia'].", a los ".$orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']." días del mes de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']." del año ".$orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'].".";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        
        
        
        $this->ln(10);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->linea[0] = array(
            'posx' => 20,
            'ancho' => 60,
            'texto' => "por  MUTUANTE/ACREEDOR",
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 10
        );
        $this->linea[1] = array(
            'posx' => 120,
            'ancho' => 60,
            'texto' => "por  MUTUARIO/DEUDOR",
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 10
        );
        $this->Imprimir_linea();
        $this->ln(10);
        $this->linea[0] = array(
            'posx' => 20,
            'ancho' => 60,
            'texto' => utf8_decode("Aclaración / Sello"),
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 10
        );
        $this->linea[1] = array(
            'posx' => 120,
            'ancho' => 60,
            'texto' => utf8_decode("Aclaración / Sello"),
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 10
        );
        $this->Imprimir_linea();
        
        $this->reset();
    }
    
    
    function imprimeAutoPagoDirectoBancoPciaBsAs($logo,$orden){
//        if(!$this->checkImpresionAutorizacionDebito('00014',$orden['MutualProductoSolicitud']['tipo_orden_dto'],$orden['MutualProductoSolicitud']['beneficio_banco_id'])){
//            return;
//        }
        $this->PIE = false;
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);
//        $this->Rect($this->GetX(),$this->GetY(),190,220);
//        $this->Rect($this->GetX(),$this->GetY(),55,15);
//        $this->Rect($this->GetX() + 55,$this->GetY(),135,15);
        $this->image($logo,13,13,50);   
        
        $this->SetY(30);

        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"SOLICITUD DE ADHESIÓN AL PAGO DIRECTO",0,0,'C');  
        $this->SetY(40);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
//        $this->SetY(20);
        
        $TEXTO = "";  
        $TEXTO .= "Lugar y fecha: ".$this->INI_FILE['general']['domi_fiscal_localidad'].", a los ".$orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']." días del mes de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']." del año ".$orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'];
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "En mi carácter de titular de la cuenta corriente/ caja de ahorros (testar lo que no corresponda) correspondiente a la Clave bancaria Uniforme indicada precedentemente, radicada en vuestra _________________________________ (consignar el nombre de la Casa o Filial), solicito al BANCO DE LA PROVINCIA DE BUENOS AIRES mi adhesión al Sistema de PAGO DIRECTO normado por el BCRA, para el abono de la s factura/ s y/o prestaciones de la/ s empresa/ s y/o repartición/ es que se detallan en el reverso de este formulario. A estos efectos acompaño original y fotocopia de la última factura abonada para cada servicio.";
        $TEXTO .= "\n";
        $TEXTO .= "En tal sentido autorizo a debitar de mi citada cuenta en las fechas de vencimiento mensual o en los períodos respectivos, los importes de las cuotas y/o abonos y/o facturas correspondientes a los PAGOS SOLICITADOS en la presente, en tanto estos resulten aceptados en un todo de acuerdo con las condiciones generales que se describen en la presente y que declaramos conocer y aceptar en su totalidad.";
        $TEXTO .= "\n";
        $TEXTO .= "A) Continuaré efectuando los pagos personalmente o por interpósita persona, en caja o por ventanilla, según corresponda, desde la fecha del presente y hasta el día en que reciba la /s factura/ s cursada/ s por la empresa con la leyenda que indique que las mismas serán canceladas mediante débito en la Cuenta Bancaria citada precedentemente.";
        $TEXTO .= "\n";
        $TEXTO .= "B) Los importes de la/ s factura/ s será /n debitado s de mi cuenta el día de vencimiento de su/ s respectivo/ s pago/ s, o el primer día hábil siguiente, de ser este feriado o día no laborable en la actividad bancaria, de acuerdo con las normas que a tal efecto fije el B.C.R.A. Se tendrán en cuenta las prórrogas que a tal efecto pudieran otorgarles las empresas.";
        $TEXTO .= "\n";
        $TEXTO .= "C) A tal efecto me comprometo a mantener saldo suficiente en la cuenta citada a fin de que los débitos puedan ser formalmente efectuados en cada vencimiento. En caso de que los débitos sean efectuados en Caja de Ahorros y no existieran fondos suficientes, el servicio / factura quedará impago.";
        $TEXTO .= "\n";
        $TEXTO .= "D) Para el caso que el Banco decidiera autorizar los débitos no existiendo fondos suficientes en mi cuenta corriente, quedaré obligado al pago del saldo deudor que se origine, con más sus intereses compensatorios en  el plazo que me fuera reclamado, y ante mi incumplimiento será de aplicación la normativa legal vigente para la cuenta corriente bancaria la que declaro conocer y que fue aceptada por mi en el momento de la apertura de la cuenta corriente.";
        $TEXTO .= "\n";
        $TEXTO .= "E) Será a mi exclusivo cargo y responsabilidad efectuar todos los reclamos, aclaraciones y solucionar todas las diferencias que pudieran suscitarse con la empresa por los importes debitados en mi cuenta.";
        $TEXTO .= "\n";
        $TEXTO .= "F) Los débitos que tengan por origen mi adhesión a este sistema no serán computados en mi cuenta de caja de ahorros para el cálculo de cantidad máxima de extracciones mensuales autorizadas.";
        $TEXTO .= "\n";
        $TEXTO .= "G) El Banco podrá dejar de prestar este servicio a partir del momento en que se produzca cualquiera de las siguientes circunstancias y la baja del mismo quedará efectivizada cuando deje de consignarse en la/s factura/s la leyenda indicada en A).";
        $TEXTO .= " 1) Por falta de fondos suficientes acreditados en  mi cuenta a la fecha que fuera necesario efectuar el/los débitos de la/s facturas/s.";
        $TEXTO .= " 2) Por el cierre de mi cuenta bancaria cualquiera fuera su causa.";        
        $TEXTO .= " 4) Por decisión de la/s empresa/s prestataria/s del/los servicio/s.";
        $TEXTO .= " 5) Por mi propia decisión mediante comunicación por escrito a la empresa prestadora del servicio.";
        $TEXTO .= "\n";
        $TEXTO .= "H) MODALIDAD STOP DEBIT: Si en alguna oportunidad y como caso de excepción, considero que el importe de mi factura está equivocado, tendré la opción de solicitar en la sucursal donde tenga radicada la cuenta, la modalidad STOP DEBIT. El Stop Debit deberá ordenarse, hasta las 48 horas hábiles anteriores –inclusive – a la fecha de vencimiento, quedando bajo mi responsabilidad regularizar la gestión mediante nota al Banco. Con respecto a las siguientes facturaciones las mismas seguirán efectuándose normalmente.";        
        $TEXTO .= "\n";
        $TEXTO .= "I) MODALIDAD REVERSIÓN: queda convenido que hasta 30 días corridos posteriores a la fecha del débito en cuenta bancaria de los importes facturados por la empresa, podremos requerir al Banco que revierta dichos débitos sujeto a las siguientes condiciones: 1) El requerimiento deberá ser formulado en la sucursal del Banco donde tenga radicada la cuenta o en la empresa. 2) En caso de que el reclamo se presente en el Banco, éste efectuará el reintegro de los fondos, comunicando tal situación a la empresa. 3) Si el importe no supera el limite que en su momento pueda establecer el BCRA o la autoridad de aplicación correspondiente, el mismo será revertido dentro de las 72 horas hábiles bancarias siguientes a la fecha en que el Banco haya recibido la instrucción por mi parte. Si el importe supera el limite impuesto por el BCRA o la autoridad de aplicación correspondiente, el mismo será revertido dentro de las 72 horas hábiles bancarias siguientes a la fecha en que el BANCO haya recibido  la instrucción por mi parte, siempre y cuando las empresas originantes del débito no se opongan a su reversión. En ambas circunstancias sin corresponder responsabilidad de ninguna índole para el Banco por las consecuencias que se hubieren derivado del débito cuestionado o las que se deriven de su reversión. En caso de no ser autorizados los reintegros por parte de la empresa los débitos cuestionados no serán revertidos, sin ello implicar responsabilidad alguna para el Banco, debiendo dilucidar las controversias directamente los suscriptos con las empresas involucradas.";
        $TEXTO .= "\n";        
        $TEXTO .= "J) El Banco no efectuará reintegro de intereses compensatorios y/o punitorios por débito generados erróneamente por la empresa o repartición. En esta circunstancia deberé hacer el reclamo ante la empresa.";
        $TEXTO .= "\n";        
        $TEXTO .= "K) Asumo que al existir un extracto donde figuran los débitos efectuados, el Banco dará por conforme la totalidad de los movimientos, cobrados o rechazados de cada período informado, excepto comunicación por escrito en contrario dentro de los treinta días contados a partir de la fecha de vencimiento del servicio informada por la empresa al Banco.";
        $TEXTO .= "\n";
        $TEXTO .= "L) La Cláusula I) Modalidad Reversión, será considerada sólo para aquellas empresas que acepten por convenio esta modalidad.";        
        $TEXTO .= "\n";
        $TEXTO .= "M) LIMITACION DE RESPONSABILIDAD: El Banco no asume responsabilidad alguna por el o los montos consignados en las facturas e informados en el medio magnético. En ningún caso el Banco será responsable por circunstancias dañosas que hayan surgido de la intervención de la cámara, el Banco receptor o cualquier otro tercero que haya participado de algún modo en la operatoria que se implementa en virtud de esta solicitud. Los reclamos deberán ser interpuestos por escrito ante la empresa, no pudiendo actuar en ningún caso el Banco en calidad de intercesor para su formulación. Queda expresamente establecido que el Banco se limitará única y exclusivamente a realizar los débitos y las acreditaciones, de conformidad con el detalle e instrucciones que proporcionará la empresa, sin que pueda exigírsele el cumplimiento de cualquier otra obligación no determinada en la presente. En este servicio el Banco es intermediario y recibe información de la empresa sobre los débitos que debe realizar, y los procesa a su orden, desentendiéndose de todo lo relacionado con el aspecto comercial que generaron los mismos.";        
        $TEXTO .= "\n";
        $TEXTO .= "N) Me informaré de las transferencias efectuadas a través de los medios electrónicos habilitados en el Banco (cajeros automáticos y Home Banking).";
        $TEXTO .= "\n";        
        $TEXTO .= "\n";
        $TEXTO .= "La presente continuará vigente hasta tanto medie comunicación fehaciente de mi parte para revocarla.";        
        $TEXTO .= "\n";
//        $TEXTO .= "\n";
//        $TEXTO .= "\n";        
//        $TEXTO .= "\n";
//        $TEXTO .= "\n";        
//        $TEXTO .= "\n";
//        $TEXTO .= "\n";
//        $TEXTO .= "\n";        

        $this->MultiCell(0,11,$TEXTO,0,'J');   
        $this->ln(25);
        $this->reset();
//        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
//        $this->firmaSocio('P');
        
        $this->linea[0] = array(
                                    'posx' => 20,
                                    'ancho' => 50,
                                    'texto' => "FIRMA",
                                    'borde' => 'T',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => 8
                    );
        $this->linea[1] = array(
                                    'posx' => 140,
                                    'ancho' => 50,
                                    'texto' => "FIRMA",
                                    'borde' => 'T',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => 8
                    );        
        $this->Imprimir_linea();
        $this->ln(10);
        $this->linea[0] = array(
                                'posx' => 20,
                                'ancho' => 50,
                                'texto' => "ACLARACION",
                                'borde' => 'T',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );
        $this->linea[1] = array(
                                'posx' => 140,
                                'ancho' => 50,
                                'texto' => "ACLARACION",
                                'borde' => 'T',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );        
        $this->Imprimir_linea();
        $this->ln(10);
        $this->linea[0] = array(
                                'posx' => 20,
                                'ancho' => 50,
                                'texto' => "TIPO Y NRO DE DOCUMENTO",
                                'borde' => 'T',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );
        $this->linea[1] = array(
                                'posx' => 140,
                                'ancho' => 50,
                                'texto' => "TIPO Y NRO DE DOCUMENTO",
                                'borde' => 'T',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );        
        $this->Imprimir_linea(); 
        
        
       
        
    }
    
    
    function imprime_modelo_liquidacion_cuotas($orden){
        
        $this->PIE = false;
        
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);  
        
        
        
        $this->imprimirMembrete(FALSE);
        
        
        $this->SetY(30);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"LIQUIDACION DEL PRESTAMO",0,0,'C');  
        $this->SetY(40);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);        
        
        $nroSolicitud = $orden['MutualProductoSolicitud']['nro_print'];
        $fecha = date('d-m-Y', strtotime($orden['MutualProductoSolicitud']['fecha']));
        $usuario = $orden['MutualProductoSolicitud']['user_created'];
        $vendedor = (isset($orden['MutualProductoSolicitud']['vendedor_nombre_min']) ? $orden['MutualProductoSolicitud']['vendedor_nombre_min'] : "");
        $this->SetY(10);
        $this->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor);        

        $this->ln(10);
        $this->imprimirDatosTitular($orden,TRUE);
        
        $this->ln(2);
        $TEXTO = ""; 
        $TEXTO .= "IMPORTANTE:";
        $TEXTO .= "\n";
        $TEXTO .= "Al momento de cobrar sus haberes NO RETIRAR de su cuenta bancaria el monto mensual que corresponde al servicio convenido.-";
        $TEXTO .= "\n";
        $TEXTO .= "Si Ud. Advierte que no se  ha realizado el debito dirijirse a nuestra sucursal.-";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J'); 
        $this->ln(25);
        $this->reset();          

        $this->SetY(90);
        $this->imprimir_producto_solicitado($orden);
        
        $this->ln(3);
        $this->linea[0] = array(
                                'posx' => 15,
                                'ancho' => 20,
                                'texto' => 'NRO CUOTA',
                                'borde' => 'LTRB',
                                'align' => 'C',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );        
        $this->linea[1] = array(
                                'posx' => 35,
                                'ancho' => 20,
                                'texto' => 'MES (*)',
                                'borde' => 'LTRB',
                                'align' => 'C',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );
        $this->linea[2] = array(
                                'posx' => 55,
                                'ancho' => 20,
                                'texto' => ($orden['MutualProductoSolicitud']['fdoas'] == 1 ? "IMPORTE(**)" : "IMPORTE"),
                                'borde' => 'LTRB',
                                'align' => 'C',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );         
        $this->Imprimir_linea();
        
        $oUT = new UtilHelper();
        
        foreach ($orden['MutualProductoSolicitud']['cronograma_de_vencimientos'] as $ncuo => $values) {
            
            $this->linea[0] = array(
                                    'posx' => 15,
                                    'ancho' => 20,
                                    'texto' => $ncuo,
                                    'borde' => 'LTRB',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => 8
                    );
            $this->linea[1] = array(
                                    'posx' => 35,
                                    'ancho' => 20,
                                    'texto' => $oUT->periodo($values['periodo'],FALSE,'/'),
                                    'borde' => 'LTRB',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => 8
                    );    
            $this->linea[2] = array(
                                    'posx' => 55,
                                    'ancho' => 20,
                                    'texto' => number_format($values['importe_cuota'],2),
                                    'borde' => 'LTRB',
                                    'align' => 'R',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => 8
                    );            
            $this->Imprimir_linea();             
            
        }
        $this->ln(3);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',6); 
        
        $TEXTO = "MES (*): SUJETO A LA FECHA DE APROBACION.-\n";
        if($orden['MutualProductoSolicitud']['fdoas'] == 1){
            $TEXTO .= "IMPORTE(**): CUOTA PURA. NO INCLUYE LA COBERTURA POR RIESGO CONTINGENTE DE $ " . number_format($orden['MutualProductoSolicitud']['fdoas_total_cuota'],2).". NO INCLUYE GASTOS ADMINISTRATIVOS POR LA GESTION DE COBRANZA.-\n";
        }
        
        $this->MultiCell(0,11,$TEXTO,0,'J'); 
        
//        $this->ln(3);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8); 
        $TEXTO = "SE DESCONTARA POR LOS SIGUIENTES CODIGOS:\nMUTUAL 22 DE SEPTIEMBRE, SOLUCIONES INFORMATICAS, MARGEN COMERCIAL.\n";
        $this->MultiCell(0,11,$TEXTO,0,'J'); 
        
        $this->ln(20);
        $this->firmaSocio();
        
//        $this->imprimirDatosTitular($orden,TRUE);
//        $this->SetY(60);
        

        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        
       
        
    }
    
    
    function imprimir_producto_solicitado($orden){
        
        $size = 10;
        $sized = 13;        
        
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 190,
                        'texto' => "Producto",
                        'borde' => 'B',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(4);


        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 30,
                        'texto' => utf8_decode("Solicitado $"),
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        // $this->linea[2] = array(
        //                 'posx' => 55,
        //                 'ancho' => 20,
        //                 'texto' => number_format($orden['MutualProductoSolicitud']['importe_solicitado'],2) . " (Son Pesos " . utf8_decode($orden['MutualProductoSolicitud']['total_importe_solicitado_letras']) . ")",
        //                 'borde' => '',
        //                 'align' => 'L',
        //                 'fondo' => 0,
        //                 'style' => '',
        //                 'colorf' => '#D8DBD4',
        //                 'size' => $size
        // );
        $this->linea[2] = array(
            'posx' => 40,
            'ancho' => 20,
            'texto' => number_format($orden['MutualProductoSolicitud']['importe_solicitado'],2),
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );        
        $this->linea[3] = array(
            'posx' => 60,
            'ancho' => 145,
            'texto' => "(Son Pesos " . utf8_decode($orden['MutualProductoSolicitud']['total_importe_solicitado_letras']) . ")",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size - 2
        );        

        $this->Imprimir_linea();

        if(!empty($orden['MutualProductoSolicitud']['gasto_admin']) && $orden['MutualProductoSolicitud']['gasto_admin'] != 0){

            $totalDeducciones = $orden['MutualProductoSolicitud']['gasto_admin'];
            $cadena = utf8_decode("Gtos.Administrativos $ ") . number_format($orden['MutualProductoSolicitud']['gasto_admin'],2);



            if(!empty($orden['MutualProductoSolicitud']['sellado']) && $orden['MutualProductoSolicitud']['sellado'] != 0){

                $totalDeducciones += $orden['MutualProductoSolicitud']['sellado'];
                $cadena .= utf8_decode(" + Sellados $ ") . number_format($orden['MutualProductoSolicitud']['sellado'],2);
            }

            $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 30,
                'texto' => utf8_decode("Deducciones $ "),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->linea[2] = array(
                'posx' => 40,
                'ancho' => 20,
                'texto' => number_format($totalDeducciones,2),
                'borde' => '',
                'align' => 'R',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );   
            $this->linea[3] = array(
                'posx' => 60,
                'ancho' => 145,
                'texto' => "(" . $cadena . ")",
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size - 2
            );                                 
            $this->Imprimir_linea();

        }


//        $this->linea[1] = array(
//                        'posx' => 10,
//                        'ancho' => 50,
//                        'texto' => utf8_decode("Neto Solicitado $"),
//                        'borde' => '',
//                        'align' => 'L',
//                        'fondo' => 0,
//                        'style' => '',
//                        'colorf' => '#D8DBD4',
//                        'size' => $sized
//        );
//        $this->linea[2] = array(
//                        'posx' => 60,
//                        'ancho' => 25,
//                        'texto' => number_format($orden['MutualProductoSolicitud']['importe_percibido'],2),
//                        'borde' => '',
//                        'align' => 'L',
//                        'fondo' => 0,
//                        'style' => 'B',
//                        'colorf' => '#D8DBD4',
//                        'size' => $sized
//        );
        $sized1 = $sized - 2;
        if($orden['MutualProductoSolicitud']['total_cancelacion'] != 0){
            
            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 30,
                            'texto' => utf8_decode("Solicitado $"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $sized1
            );
            $this->linea[2] = array(
                            'posx' => 40,
                            'ancho' => 25,
                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_percibido'],2),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $sized1
            );            
            
            
            $this->linea[3] = array(
                            'posx' => 65,
                            'ancho' => 40,
                            'texto' => utf8_decode("Cancelaciones $"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $sized1
            );  
            $this->linea[4] = array(
                            'posx' => 105,
                            'ancho' => 25,
                            'texto' => number_format($orden['MutualProductoSolicitud']['total_cancelacion'],2),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $sized1
            );
            $this->linea[5] = array(
                            'posx' => 130,
                            'ancho' => 45,
                            'texto' => utf8_decode("Neto a Cobrar $"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 1,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $sized
            );
            $this->linea[6] = array(
                            'posx' => 175,
                            'ancho' => 25,
                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_percibido'] - $orden['MutualProductoSolicitud']['total_cancelacion'],2),
                            'borde' => '',
                            'align' => 'R',
                            'fondo' => 1,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $sized
            );            
            
        }else{
            
            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 50,
                            'texto' => utf8_decode("Neto a Cobrar $"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $sized
            );
            $this->linea[2] = array(
                            'posx' => 60,
                            'ancho' => 25,
                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_percibido'],2),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $sized
            );            
        }
        
        $this->Imprimir_linea();

        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 45,
                        'texto' => utf8_decode("Cantidad de Cuotas:"),
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->linea[2] = array(
                        'posx' => 55,
                        'ancho' => 60,
                        'texto' => utf8_decode($orden['MutualProductoSolicitud']['cantidad_cuota_letras']) . "(".$orden['MutualProductoSolicitud']['cuotas'].")",
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->linea[3] = array(
                        'posx' => 115,
                        'ancho' => 45,
                        'texto' => "Monto de la Cuota: $",
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->linea[4] = array(
                        'posx' => 160,
                        'ancho' => 40,
                        'texto' => number_format($orden['MutualProductoSolicitud']['importe_cuota'],2),
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->Imprimir_linea();


        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 35,
                        'texto' => utf8_decode("Total a Reintegrar $"),
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size -3
        );
        // $this->linea[2] = array(
        //     'posx' => 55,
        //     'ancho' => 155,
        //     'texto' => number_format($orden['MutualProductoSolicitud']['importe_total'],2) . " (Son Pesos " . utf8_decode($orden['MutualProductoSolicitud']['total_letras']) . ")",
        //     'borde' => '',
        //     'align' => 'L',
        //     'fondo' => 0,
        //     'style' => '',
        //     'colorf' => '#D8DBD4',
        //     'size' => $size
        // );        
        $this->linea[2] = array(
                        'posx' => 45,
                        'ancho' => 20,
                        'texto' => number_format($orden['MutualProductoSolicitud']['importe_total'],2),
                        'borde' => '',
                        'align' => 'R',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size - 2
        );
        $this->linea[3] = array(
            'posx' => 65,
            'ancho' => 145,
            'texto' => "(Son Pesos " . utf8_decode($orden['MutualProductoSolicitud']['total_letras']) . ")",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size - 3
);        

        $this->Imprimir_linea();        
    }
    
    function imprimir_liquidacion($orden,$imprimeInstruccion=true){
        
        $size = 10;
        $sized = 13;           
        
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 190,
                        'texto' => utf8_decode("Liquidación"),
                        'borde' => 'B',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(4);

        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 35,
                        'texto' => utf8_decode("Forma de Pago:"),
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->linea[2] = array(
                        'posx' => 45,
                        'ancho' => 80,
                        'texto' => $orden['MutualProductoSolicitud']['forma_pago_desc'],
                        'borde' => '',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        if($imprimeInstruccion){
            $this->linea[3] = array(
                            'posx' => 125,
                            'ancho' => 60,
                            'texto' => utf8_decode("Instrucción de Pago:"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $sized
            );
            $this->linea[4] = array(
                            'posx' => 185,
                            'ancho' => 15,
                            'texto' => (empty($orden['MutualProductoSolicitudInstruccionPago']) || count($orden['MutualProductoSolicitudInstruccionPago']) == 1 ? "NO" : "SI"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $sized
            );            
        }

        $this->Imprimir_linea();        
//        if($orden['MutualProductoSolicitud']['total_cancelacion'] != 0){
//            
//            $this->linea[1] = array(
//                            'posx' => 10,
//                            'ancho' => 50,
//                            'texto' => utf8_decode("A la orden Personal: $"),
//                            'borde' => '',
//                            'align' => 'L',
//                            'fondo' => 0,
//                            'style' => '',
//                            'colorf' => '#D8DBD4',
//                            'size' => $size
//            );
//            $this->linea[2] = array(
//                            'posx' => 60,
//                            'ancho' => 80,
//                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_percibido'] - $orden['MutualProductoSolicitud']['total_cancelacion'],2),
//                            'borde' => '',
//                            'align' => 'L',
//                            'fondo' => 0,
//                            'style' => 'B',
//                            'colorf' => '#D8DBD4',
//                            'size' => $size
//            );            
//            
//            
//            $this->Imprimir_linea(); 
//        }
    }
    
    
    function imprime_autorizacion_debito_cuenca($orden){
        
        $this->PIE = false;
        
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);  
        
        
        
//        $this->imprimirMembrete(FALSE); 
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"CARTA DE AUTORIZACION DEBITO DIRECTO -  Caja de Crédito Cuenca C.L. ",0,0,'C');    
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        $this->SetY(20);  
        
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8); 
        $TEXTO = "";
        $TEXTO .= "En mi carácter de  titular de la cuenta  indicada,  solicito a Caja de Crédito Cuenca C.L. mi adhesión al Sistema Débito Directo, conforme a lo reglamentado por la Comunicación A 2559 y modificatorias del B.C.R.A., las que declaro aceptar y conocer.-.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "En consecuencia, autorizo a Caja de Crédito Cuenca C.L. a realizar los débitos de la cuenta indicada, por los importes indicados y en las fechas informadas por la Empresa de sus servicios otorgados, comprometiéndome a mantener saldo suficiente en la cuenta de mi titularidad y a fin de que los débitos puedan ser formalmente efectuados en cada vencimiento.-";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "TITULAR DE LA CUENTA: ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']);
        $TEXTO .= "\n";
        $TEXTO .= "D.N.I.: ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_ndoc']);
        $TEXTO .= "\n";
        $TEXTO .= "C.U.I.T.: ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_cuit_cuil']);
        $TEXTO .= "\n";        
        $TEXTO .= "Banco: ".utf8_decode($orden['MutualProductoSolicitud']['beneficio_banco']);
        $TEXTO .= "\n";  
        $TEXTO .= "Sucursal: ".utf8_decode($orden['MutualProductoSolicitud']['beneficio_sucursal']);
        $TEXTO .= "\n";   
        $TEXTO .= "Nro. Caja de Ahorros: ".utf8_decode($orden['MutualProductoSolicitud']['beneficio_cuenta']);
        $TEXTO .= "\n";
        $TEXTO .= "Nro. Cuenta Corriente Común: ";
        $TEXTO .= "\n";  
        $TEXTO .= "Nro. Cuenta Corriente Especial: ";
        $TEXTO .= "\n";                 
        $TEXTO .= "C.B.U.: ".utf8_decode($orden['MutualProductoSolicitud']['beneficio_cbu']);        
        $TEXTO .= "\n";   
        $TEXTO .= "\n";
        $TEXTO .= "Presto conformidad,  en caso del rechazo del débito, a que la Empresa pueda efectuar los reintentos necesarios, con costo a mi cargo, para el cumplimiento de mis compromisos asumidos.-";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Asimismo tomo conocimiento que Caja de Crédito Cuenca C.L. no asume ninguna responsabilidad por los importes a debitar informados por la Empresa y que tampoco será responsable por circunstancias dañosas que hayan surgido de la intervención de la cámara, del Banco receptor o cualquier otro tercero que haya participado de algún modo en la operatoria que se implementa en virtud de esta solicitud. Por tal motivo los reclamos deberán ser interpuestos por escrito ante la Empresa, no pudiendo actuar en ningún caso Caja de Crédito Cuenca C.L. en calidad de intercesor para su resolución. Queda establecido que Caja de Crédito Cuenca C.L. se limitará única y exclusivamente a realizar los débitos y las acreditaciones, de conformidad con el detalle e instrucciones que proporcionará la Empresa. En este servicio Caja de Crédito Cuenca C.L. es intermediario y recibe información de la Empresa sobre los débitos que debe realizar, y los procesa a su orden, desentendiéndose de todo lo relacionado con el aspecto comercial que generaron los mismos.";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
   
        
        $this->ln(20);
        $this->firmaSocio();        
        
    }
    
    
    function imprimirFdoAs($orden){
        if(!isset($orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']) && !empty($orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['importe_total'])):
                $this->ln(1);
                $this->linea[1] = array(
                                'posx' => 10,
                                'ancho' => 190,
                                'texto' => "COBERTURA POR RIESGO CONTINGENTE",
                                'borde' => '',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '#D8DBD4',
                                'size' => 9
                );
                $this->Imprimir_linea();
                $this->SetFontSize(8);
                $TEXTO = "AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." ";
                $TEXTO .= "a descontar de mis haberes la cantidad de ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['cuotas']." (".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['total_cuota_cantidad_letras'].") cuotas mensuales y consecutivas de $ ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['importe_cuota']." (PESOS ".$orden['MutualProductoSolicitud']['OrdenDescuentoSeguro']['total_cuota_letras'].") cada una a favor de ______________________ en concepto de pago de la cobertura por Riesgo Contingente a partir de ".(!empty($orden['MutualProductoSolicitud']['inicia_en']) ? $orden['MutualProductoSolicitud']['inicia_en'] : "___/_____").".-\n";
                $this->MultiCell(0,11,$TEXTO);
        elseif($orden['MutualProductoSolicitud']['fdoas']):
                $this->ln(1);
                $this->linea[1] = array(
                                'posx' => 10,
                                'ancho' => 190,
                                'texto' => "COBERTURA POR RIESGO CONTINGENTE",
                                'borde' => '',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '#D8DBD4',
                                'size' => 9
                );
                $this->Imprimir_linea();
                $this->SetFontSize(8);
                $TEXTO = "AUTORIZO en forma expresa a ".Configure::read('APLICACION.nombre_fantasia')." ";
                $TEXTO .= "a descontar de mis haberes la cantidad de ".$orden['MutualProductoSolicitud']['fdoas_total_cuota_cantidad']." (".$orden['MutualProductoSolicitud']['fdoas_total_cuota_cantidad_letras'].") cuotas mensuales y consecutivas de $ ".$orden['MutualProductoSolicitud']['fdoas_total_cuota']." (PESOS ".$orden['MutualProductoSolicitud']['fdoas_total_cuota_letras'].") cada una a favor de ______________________ en concepto de pago de la cobertura por Riesgo Contingente a partir de ".(!empty($orden['MutualProductoSolicitud']['inicia_en']) ? $orden['MutualProductoSolicitud']['inicia_en'] : "___/_____").".-\n";
                $this->MultiCell(0,11,$TEXTO);

        endif;        
    }
    
    function imprime_solicitud_afiliacion_isar($orden){
        $this->AddPage();
        $this->reset();
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        //$this->SetY(10);
        //$this->SetX(10);
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 190,
                        'texto' => utf8_decode("Integración Solidaria de Ayuda Recíproca"),
                        'borde' => '',
                        'align' => 'C',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => 10
        );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 190,
                        'texto' => utf8_decode("Mat.2206"),
                        'borde' => '',
                        'align' => 'C',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => 7
        );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 190,
                        'texto' => utf8_decode("Viamonte 759 6º piso Of. 64"),
                        'borde' => '',
                        'align' => 'C',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => 7
        );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 190,
                        'texto' => utf8_decode("(1053) Capital Federal"),
                        'borde' => '',
                        'align' => 'C',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => 7
        );
        $this->Imprimir_linea();
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 190,
                        'texto' => utf8_decode("Tel. 4322-1332"),
                        'borde' => '',
                        'align' => 'C',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => 7
        );
        $this->Imprimir_linea();
        $this->Ln();
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 190,
                        'texto' => utf8_decode("SOLICITUD DE AFILIACIÓN"),
                        'borde' => '',
                        'align' => 'C',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => 14
        );
        $this->Imprimir_linea();
        $this->SetFontSize(10);
        $TEXTO = "Señores Consejo Directivo de:\nASOCIACIÓN MUTUAL INTEGRACIÓN SOLIDARIA DE AYUDA RECIPROCA\nPor  la presente solicito asociarme a vuestra entidad mutual en carácter de Asociado: Activo / Adherente.\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        $this->Ln(1);
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 90,
                        'texto' => utf8_decode("DATOS PERSONALES"),
                        'borde' => 'B',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => 10
        );
        $this->linea[2] = array(
                        'posx' => 100,
                        'ancho' => 50,
                        'texto' => utf8_decode("AFILIADO Nº "),
                        'borde' => 'B',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => 10
        );
        $this->linea[3] = array(
                        'posx' => 150,
                        'ancho' => 50,
                        'texto' => utf8_decode("FECHA: ") . date('d/m/Y', strtotime($orden['MutualProductoSolicitud']['fecha'])),
                        'borde' => 'B',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => 10
        );
        $this->Imprimir_linea();
        $this->Ln(2);
        $this->imprimirDatosTitular($orden,FALSE,FALSE);
        $this->imprimirDatosCuentaDebito($orden,TRUE);
        $this->Ln(2);
        $this->SetFontSize(10);
        $TEXTO = "Autorización de Descuento: En mi carácter de Asociado a la ASOCIACIÓN MUTUAL INTEGRACIÓN SOLIDARIA DE AYUDA RECÍPROCA solicito se debite de mi cuenta en el Banco " . utf8_decode($orden['MutualProductoSolicitud']['beneficio_banco']) . " ";
        $TEXTO .= "por el código que la ASOCIACIÓN MUTUAL INTEGRACIÓN SOLIDARIA DE AYUDA RECÍPROCA posee en dicho banco el importe de $ ________ (Pesos_______________________) correspondiente a la cuota social.";
        $TEXTO .= "\n";
        $TEXTO .= "El uso de los servicios implica mi aceptación de las condiciones vigentes para el asociado, hasta el día en que por medio fehaciente les haga saber mi voluntad de dejar de revestir tal carácter.";
        $TEXTO .= "\n";
        $TEXTO .= "Si no se hiciera el descuento por el sistema mencionado o el mismo fuese insuficiente concurriré a la sede de la ASOCIACIÓN MUTUAL INTEGRACIÓN SOLIDARIA DE AYUDA RECÍPROCA, para realizar los pagos.";
        $TEXTO .= "\n";
        $this->MultiCell(0,11, $TEXTO,'B','J');

        $this->Ln(2);
        $TEXTO = "Por la presente declaro que los datos vertidos mas arriba son verdaderos y con los mismos solicito al Sr. Presidente ser aceptado como afiliado a la  ASOCIACIÓN MUTUAL INTEGRACIÓN SOLIDARIA DE AYUDA RECÍPROCA, comprometiéndome a cumplir lo dispuesto por los Estatutos en vigencia.";
        $TEXTO .= "\n";
        $this->MultiCell(0,11, $TEXTO,'B','J');

        $this->ln(20);
        $this->firmaSocio();        
    }
    

    public function imprimirSolicitudAfiliacion($membrete,$orden = NULL){

	$this->AddPage();
	$this->reset();
	// $this->ln(4);

	$this->SetY(10);

	$this->SetFont('courier','',12);


	$this->SetFont(PDF_FONT_NAME_MAIN,'B',14);
	$this->Cell(0,5,$membrete['L1'],0);
	$this->Ln(5);
	$this->SetFont(PDF_FONT_NAME_MAIN,'',8);
	$this->Cell(0,5,$membrete['L2'],0);
	$this->Ln(3);
	$this->SetFont(PDF_FONT_NAME_MAIN,'',8);
	$this->Cell(0,5,$membrete['L3'],0);
	$this->Ln(4);

	$size = 10;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => utf8_decode("FECHA: ") . date('d/m/Y', strtotime($orden['MutualProductoSolicitud']['fecha'])),
			'borde' => '',
			'align' => 'R',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$this->Imprimir_linea();        

	$size = 20;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "SOLICITUD DE AFILIACION",
			'borde' => '',
			'align' => 'C',
			'fondo' => 1,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$this->Imprimir_linea();
        $this->Ln(4);
	$this->SetFontSize(11);
	$TEXTO = "Sres. Miembros del Consejo Directivo:\n\nPor la Presente me dirijo a Uds., a los efectos de solicitarles mi inscripción como socio/a de la ".Configure::read('APLICACION.nombre_fantasia').",de acuerdo a lo estipulado en los Estatutos Sociales de la Entidad, para lo cual, adjunto los datos que a continuación se detallan:.\n";
	$this->MultiCell(0,11,$TEXTO,0,'J');

	$this->imprimirDatosTitular($orden);
	$this->imprimirDatosCuentaDebito($orden);
	$this->Ln(4);
	$this->SetFontSize(11);
	$TEXTO = "Asimismo, autorizo a la ".Configure::read('APLICACION.nombre_fantasia')." a descontar de mis haberes mensuales, a través de los códigos que correspondan, los montos equivalentes a la Cuota Social (en forma mensual y consecutiva), como así también los que se generen por Fondos de asistencia, seguros, y todo otro consumo o gasto que surja de los comprobantes pertinentes.-\n\nDeclaro bajo juramento que los datos consignados en la presente planilla son auténticos, comprometiéndome a comunicar a la MUTUAL todo cambio que se produzca, dentro de las 72 hs. de acontecido el mismo, adjuntando para ello la documentación que la entidad me solicite a tal efecto.-\n";
	$this->MultiCell(0,11,$TEXTO,0,'J');

	$this->ln(20);

	$this->firmaSocio();

	$this->ln(4);
	$this->barCode($orden['MutualProductoSolicitud']['barcode']);
        
        // agregar grilla aprobacion
        $this->ln(10);
        $this->linea[0] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => 'APROBACION DE LA SOLICITUD',
                'borde' => 'LTRB',
                'align' => 'C',
                'fondo' => 1,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => 13
        );
        $this->Imprimir_linea(); 
        $alto = 20;
        $this->linea[0] = array(
                'posx' => 10,
                'ancho' => 50,
                'texto' => 'Fecha de Alta: ___/___/____',
                'borde' => 'LBR',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => 8,
                'alto' => $alto
        );
        $this->linea[1] = array(
                'posx' => 60,
                'ancho' => 70,
                'texto' => utf8_decode('Autorizó: ______________________________'),
                'borde' => 'LBR',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => 8,
                'alto' => $alto
        );
        $this->linea[2] = array(
                'posx' => 130,
                'ancho' => 70,
                'texto' => utf8_decode('Firma Autorizante: _____________________'),
                'borde' => 'LBR',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => 8,
                'alto' => $alto
        );           
        $this->Imprimir_linea();
        
        
        // ANEXO SUJETO OBLIGADO UIF
        
	$this->AddPage();
	$this->reset();
	// $this->ln(4);

	$this->SetY(10);

	$size = 14;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => utf8_decode("ANEXO VI - DECLARACIÓN JURADA DE SUJETO OBLIGADO"),
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$this->Imprimir_linea();        
        
        $this->ln(10);
        
        $TEXTO = "";  
        $TEXTO .= $this->INI_FILE['general']['domi_fiscal_localidad'].", ".$orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']." de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']." de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'];
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'R'); 
        
        
        $this->ln(3);
        
        $apenom = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']);
        
        $TEXTO = "";
        $TEXTO .= "<b>$apenom " . $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'] . "</b>, en mi carácter de titular / representante legal / apoderado de la empresa (razón social________________), CUIT ".$orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'].", manifiesto en calidad de <b>DECLARACIÓN JURADA</b> que dicha empresa:";
        $TEXTO .= "<br/>";
        $TEXTO .= "<br/>";
        $this->textoHTML($TEXTO);
        $this->Rect($this->GetX(), $this->GetY(), 5, 5, 'D'); // Cambia las dimensiones según necesites
        $this->Cell(5);          
        
        $TEXTO = " <b>SI</b> se encuentra incluida y/o alcanzada dentro de la “Nómina de Sujetos Obligados” enumerados en el artículo 20 de la Ley 25.246 y sus modificatorias, declarando que tiene conocimiento del alcance y propósitos establecidos por la Ley 25.246, sus normas modificatorias y complementarias, en las resoluciones emitidas por la Unidad de Información Financiera y demás disposiciones vigentes en materia de Prevención de Lavado de Activos y Financiamiento del Terrorismo, y que cumple la mencionada normativa. ";
        $TEXTO .= "<br/>";
        $TEXTO .= "<br/>";
        $this->textoHTML($TEXTO);
        
        $this->Rect($this->GetX(), $this->GetY(), 5, 5, 'D'); // Cambia las dimensiones según necesites
        $this->Cell(5);        
        
        $TEXTO = " <b>NO</b> es sujeto obligado.";
        $TEXTO .= "<br/>";
        $TEXTO .= "<br/>";
        $TEXTO .= "Cuando revista la calidad de Sujeto Obligado deberá presentar la Constancia de Inscripción en la Unidad de Información Financiera (UIF). Además, asume el compromiso de informar cualquier modificación que se produzca a este respecto, dentro de los treinta (30) días de ocurrida, mediante la presentación de una nueva declaración jurada.";
        $TEXTO .= "<br/>";
        $this->textoHTML($TEXTO);
        
	$this->ln(20);

	$this->firmaSocio();

        $this->ln(10);
        
	$size = 10;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => utf8_decode("CARÁCTER (*): _________________________________________") ,
			'borde' => '',
			'align' => 'L',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$this->Imprimir_linea();   
        $this->ln(5);
        
        $this->MultiCell(0,11,"* Debe consignarse la firma de su representante legal o apoderado con facultades suficientes y acreditadas mediante la presentación de poder al efecto\n",0,'J');
        
        $this->ln(25);
        $this->reset();        
        
        // ---- PERSONA POLITICAMENTE EXPUESTA
	$this->AddPage();
	$this->reset();
	// $this->ln(4);

	$this->SetY(10);
        
	$size = 14;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => utf8_decode("DECLARACIÓN JURADA"),
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$this->Imprimir_linea();     
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => utf8_decode("SOBRE LA CONDICIÓN DE PERSONA EXPUESTA POLÍTICAMENTE"),
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$this->Imprimir_linea();         

        $size = 10;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => utf8_decode("(LEY N° 25.246 y modif., RESOLUCIÓN UIF N° RESOL-2023-35-APN-UlF#MEC)"),
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
	$this->Imprimir_linea();  
        $this->ln(10);
        $TEXTO = "En cumplimiento de lo dispuesto por la Unidad de Información Financiera (UIF), el Sr./Sra.(1) <b>$apenom</b> (2) por la presente <b>DECLARA BAJO JURAMENTO</b> que los datos consignados en la presente son correctos, completos y fiel expresión de la verdad y que SI/NO (1) se encuentra incluido y/o alcanzado dentro de la 'Nómina de Personas Expuestas Políticamente' aprobada por la UIF que se encuentra al dorso de la presente y a la que ha dado lectura. ";
        $TEXTO .= "<br/>";
        $TEXTO .= "<br/>";
        $TEXTO .= "En caso afirmativo indicar detalladamente el motivo__________________________<br/>________________________________________________________. Además asumo el compromiso de informar cualquier modificación que se produzca a este respecto, dentro de los treinta (30) días de ocurrida, mediante la presentación de una nueva declaración jurada.";
        
        $TEXTO .= "<br/>";
        $TEXTO .= "<br/>";
        $TEXTO .= "Documento: Tipo (3) <b>".$orden['MutualProductoSolicitud']['beneficiario_tdoc']."</b> Nº <b>" . $orden['MutualProductoSolicitud']['beneficiario_ndoc'] ."</b>";
        $TEXTO .= "<br/>";
        $TEXTO .= "Pais y Autoridad de Emisión _____________________________________________";
        
        $TEXTO .= "<br/>";
        $TEXTO .= "Caráter Invocado ________________________________________________________";        
        
        $TEXTO .= "<br/>";
        $TEXTO .= "CUIT/CUIL/CDI Nº <b>".$orden['MutualProductoSolicitud']['beneficiario_cuit_cuil']."</b>"; 
        
        
        $this->textoHTML($TEXTO);
        $this->ln(15);
        $this->firmaSocio();
        
        
        
        $TEXTO = "Lugar y Fecha:_______________________________";  
        
        
        
        $TEXTO .= "<br/>";
        $TEXTO .= "Certifico que la firma que antecede ha sido puesta en mi presencia."; 
        $TEXTO .= "<br/>";
        $TEXTO .= "Lugar:_______________________________ Fecha: ____ de __________ de 20____";  
        
        $TEXTO .= "<br/>";
        $TEXTO .= "<br/>";        
        $TEXTO .= "Firma y sello del certificante:"; 
        
        $TEXTO .= "<br/>";   
        $TEXTO .= "<br/>";
        $TEXTO .= "<br/>";   
        $TEXTO .= "Observaciones: __________________________________________________________";
        $TEXTO .= "<br/>";    
        $TEXTO .= "__________________________________________________________________________";
        $TEXTO .= "<br/>";    
        $TEXTO .= "__________________________________________________________________________";
        $TEXTO .= "<br/>";    
        $TEXTO .= "__________________________________________________________________________";
        $TEXTO .= "<br/>";    
        $TEXTO .= "__________________________________________________________________________";  

        $this->textoHTML($TEXTO);
        
        // $this->marcaParaFirmaDigital(140);

        
        $this->ln(3);
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $TEXTO = "(1) Tachar lo que no corresponda. (2) Integrar con el nombre y apellido del usuario/cliente, en el caso de personas humanas, aun cuando en su representación firme un apoderado. (3) Indicar DNI, LE o LC para argentinos nativos. Para extranjeros: DNI extranjeros, carné Internacional, Pasaporte, Certificado provisorio, Documento de identidad del respectivo país, según corresponda. (4) Indicar titular, representante legal, apoderado. Cuando se trate de apoderado, el poder otorgado debe ser amplio y general y estar vigente a la fecha en que se suscriba la presente declaración.";
        $TEXTO .= "<br/>";
        $TEXTO .= "<br/>";
        $TEXTO .= "Nota: Esta declaración deberá ser integrada por duplicado, el que intervenido por el sujeto obligado servirá como constancia de recepción de la presente declaración para el cliente/usuario. Esta declaración podrá ser integrada en los legajos o cualquier otro formulario que utilicen habitualmente los Sujetos Obligados para vincularse con sus clientes/usuarios";
        $this->textoHTML($TEXTO);
        
        
    }


    function imprime_autorizacion_debito_lextsrl($orden,$logo1,$logo2){
	$this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);
        $this->image($logo1,13,10,20);
        $this->image($logo2,160,10,40);      
        
	// $this->Ln(4);
        $this->SetY(25);

	$size = 14;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "AUTORIZACION DE DESCUENTO HABERES y/o DEBITO BANCARIO",
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
	);
        $this->Imprimir_linea();
        $size = 12;
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "COMPROMISO de PAGO de CUOTA y PRESTACIONES",
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();

	$this->Ln(4);
	$this->SetFontSize(10);
	$TEXTO = "Quién suscribe la presente ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']).",Argentino/a, mayor de edad, DNI n° ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_ndoc']).", Boleta n°: _____________, IPSS n°:___________, con domicilio en ". utf8_decode($orden['MutualProductoSolicitud']['beneficiario_domicilio']) . ", asumo el compromiso y AUTORIZO  expresamente  a LEX  S.R.L. y/o a quien este indique,  a descontar ".utf8_decode($orden['MutualProductoSolicitud']['cantidad_cuota_letras'])." (".utf8_decode($orden['MutualProductoSolicitud']['cuotas_print']).") cuotas de $ ".$orden['MutualProductoSolicitud']['importe_cuota']." (Pesos ".$orden['MutualProductoSolicitud']['total_cuota_letras'].") de mis haberes mensuales,  contemplado los haberes de _____________________ a _____________________ inclusive, y/o a debitar mediante CBU de la cuenta corriente y/o caja de ahorro de donde resulto titular en la actualidad o que poseyere en el futuro;  en entidad bancaria y/o financiera del sistema financiero argentino que  autoriza el BCRA, y/o del sistema de descuento que la empresa disponga en el presente y/o futuro, la suma de pesos expresada precedentemente, asumiendo la responsabilidad correspondiente por la cesión que en este acto efectuó; y autorizando expresamente que se me descuente aún por encima del 20% de mis haberes mensuales y hasta cubrir el importe autorizado en la presente.-\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        $this->Ln(2);
        $size = 14;
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "CUIT",
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size - 3
        );
        $this->Imprimir_linea();
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => $orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'],
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();        

        // $this->Ln(2); 
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "CBU",
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size - 3
        );
        $this->Imprimir_linea();   
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => $orden['MutualProductoSolicitud']['beneficio_cbu'],
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();         
        $this->Ln(1); 
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "Entidad Bancaria o Financiera",
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size - 3
        );
        $this->Imprimir_linea();
        $this->Ln(1);
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => $orden['MutualProductoSolicitud']['beneficio_banco'],
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size - 1
        );
        $this->Imprimir_linea();                         
        $size = 12;     
        $this->Ln(4);
	$this->SetFontSize(10);
	$TEXTO = "Como consecuencia del servicio contratado me comprometo expresamente a dejar en saldos suficientes en mi cuenta Bancaria dentro de las 5 días hábiles de acreditado su sueldo mensual y/o acreditado ingresos, por la totalidad de los importes que corresponden al servicio contratado con la firma, asumiendo la obligación en caso de no efectuarlo de abonar el porcentaje equivalente al 10% del monto del servicio contratado por cada oportunidad que no fuera dejados los fondos, autorizando expresamente a que dicho porcentaje sea descontado y/o debitado del mes siguiente; a lo que se le aplicarán intereses al 3% (tres por ciento) mensual, desde la mora hasta la fecha de su total y efectivo pago. Igualmente me comprometo expresamente a abstenerme de solicitar la suspensión del descuento que se autorizará por medio de la presente hasta tanto no se desvincule del servicio de la empresa, ya sea que fuera solicitado a mi empleador y/o al la entidad bancaria y/o financiera donde soy titular de cuenta donde se efectúa debito. Así también me comprometo a abstenerme de solicitar la reversión de los importes debitados (comunicación A N 5054 del BCRA y/o cualquier otra que lo contemple en el futuro) en el sistema financiero y que fueran por mi expresamente autorizados a debitar, manifestando que en el caso de realizarlo procederá a abonar el porcentaje equivalente al 10% del monto del servicio contratado por cada oportunidad que solicite la reversión de fondos, autorizando expresamente a que dicho porcentaje sea descontado y/o debitado del mes siguiente; a lo que se le aplicarán intereses al 3% (tres por ciento) mensual, desde la mora hasta la fecha de su total y efectivo pago del monto revertido.-\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        $this->Ln(20);
        $this->firmaSocio();

    }
    
    function imprime_acta_reununcia_cjpc($orden){
	$this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);

	$size = 10;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "ANEXO UNICO",
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => '',
			'colorf' => '#D8DBD4',
			'size' => $size
        );
       
        $this->Imprimir_linea();    
        // $size = 14;    
        $this->Ln(2);
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "ACTA DE RENUNCIA A LA FACULTAD DE REVOCAR AUTORIZACION PARA DESCUENTO DE HABERES",
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();        

        $this->Ln(4);
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "Entidad: " . Configure::read('APLICACION.nombre_fantasia'),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();
        $this->Ln(4);
        $this->SetFontSize(10);

        $oUT = new UtilHelper();

	$TEXTO = "En la ciudad de Córdoba, a los ".date('d', strtotime($orden['MutualProductoSolicitud']['fecha']))." días del mes ".$oUT->mesToStr(date('m', strtotime($orden['MutualProductoSolicitud']['fecha'])),true)." de ".date('Y', strtotime($orden['MutualProductoSolicitud']['fecha'])).", compareció el/la Sr./Sra. ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']).", CUIL N° ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_cuit_cuil']).", beneficiario/a de la Caja de Jubilaciones, Pensiones y Retiros de Córdoba con el Expediente N° ".utf8_decode($orden['MutualProductoSolicitud']['beneficio_cjpc_nro']).", con domicilio en calle ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_calle'])." N° ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_numero_calle']).", de la ciudad de ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_localidad']).", provincia de ".utf8_decode($orden['MutualProductoSolicitud']['beneficiario_provincia']).", y dijo:\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');

        $this->Ln(2);
	$TEXTO = "Que renuncia a la facultad de revocar la autorización de descuento del haber que le otorga el art. 45, párrafos 6° y 7°, del Decreto N° 41/09, en relación específicamente al pago de __________________________________________.-\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');   
        
        $this->Ln(3);
        $size = 10;
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "Solicitud Nro: " . $orden['MutualProductoSolicitud']['nro_print'],
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
        $this->Imprimir_linea();          
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "Fecha inicio del descuento: " . $orden['MutualProductoSolicitud']['inicia_en'],
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
        $this->Imprimir_linea();  
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "Fecha final del descuento: " . $orden['MutualProductoSolicitud']['finaliza_en'],
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
        $this->Imprimir_linea();                        
        $this->Ln(25);
        $size = 7;
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 80,
                'texto' => utf8_decode("Firma y Aclaración del Socio"),
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
	$this->linea[2] = array(
                'posx' => 90,
                'ancho' => 20,
                'texto' => "",
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
	$this->linea[3] = array(
                'posx' => 110,
                'ancho' => 80,
                'texto' => "Firma y Sello de la Entidad",
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );                    
        $this->Imprimir_linea(); 

        $this->Ln(10);
        $this->SetFont('times','I');
        $this->SetFontSize(9);
        $TEXTO = "Normas aplicables:\nLey 8024, según redacción ley 9504\nArt. 45: Están sujetas a deducciones por cargos provenientes de créditos a favor de los organismos de previsión, como así también a favor des fisco, por la percepción indebida de prestaciones provisionales y las retenciones que solicitaren las entidades que agrupan a los sectores activos y pasivos legalmente reconocidos y de conformidad a las leyes y reglamentaciones que se dictaren o le fueren aplicables. Estas deducciones no podrán exceder del veinte por ciento (20%) del importe mensual de la prestación, salvo los casos en que los pedidos de deducciones correspondiesen a créditos a favor de las obras sociales y/o entidades mutuales, que provengan de provisión de medicamentos, alimentos o vestimenta y exista la expresa manifestación escrita del afiliado autorizado a la Caja esos descuentos y hasta un cincuenta por ciento (50%) de su haber jubilatorio o de pensión.\n";
        $TEXTO .= "\n";
        $TEXTO .= "Decreto reglamentario N° 41/90:\nArt. 45: A partir del 1 de Julio de 2009, la autorización prestada por los beneficiarios para el descuento por códigos será revocable mediante manifestación expresa efectuada ante la Caja. Ante esta presentación la Caja suspenderá y notificará a la institución afectada la baja en el sistema de descuento.\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');          

        // debug($orden);
        // exit;
    }


    function imprime_contrato_descontar($orden){
        $this->AddPage();
        $this->PIE= false;
        $this->reset();
        $this->SetY(15);
        $this->SetX(10);

	$size = 12;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "CONTRATO DE PRESTACION DE SERVICIOS",
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
        );
       
        $this->Imprimir_linea(); 
        $this->Ln(5);  
        $this->SetFont('times','');
        $this->SetFontSize(11);        
        $TEXTO = "Entre el/la Sr/a. __________________________ D.N.I.: ________________, con domicilio en calle _____________________, Barrio _____________,_______________, en adelante el COMITENTE, por una parte, y por la otra el/la Sr/a. _________________________, D.N.I. ______________, CORREDOR PUBLICO C.H. 04-_____, constituyendo domicilio en calle __________________, Barrio________________, ______________, en adelante el/la PRESTADOR/A DE SERVICIOS, ambas partes de común acuerdo han resuelto celebrar el presente Contrato de Prestación de Servicios, el que se regirá bajo las siguientes cláusulas:\n";
        $TEXTO .= "\n";
        $TEXTO .= "PRIMERA: El COMITENTE, contrata los servicios profesionales de Corredor Público de el/la PRESTADOR/A DE SERVICIOS y este acepta prestar los mismos, en el domicilio ubicado en calle___________________________, Barrio______________ , de la Ciudad de ________________, en lo atinente al servicio de corretaje.-\n";
        $TEXTO .= "\n";
        $TEXTO .= "SEGUNDA: El/la PRESTADOR/A DE SERVICIOS se compromete a prestar sus servicios profesionales como Corredor Público adscripto al COMITENTE, tarea para la cual declara encontrarse habilitado/a, al día y debidamente inscripto en el Colegio Profesional de Martilleros  Corredores Públicos de la Provincia de Córdoba bajo el Certificado Habilitante 04-_____,.-\n";
        $TEXTO .= "\n";
        $TEXTO .= "TERCERA: DURACIÓN: Se conviene que el presente contrato tendrá una duración de _________(___) meses a partir del día uno (1) del mes  de  ____________ del año 20__, por lo que su vencimiento se producirá indefectiblemente el día _______(__) del mes de ________ de 20___.-\n";
        $TEXTO .= "\n";
        $TEXTO .= "CUARTA: Las partes convienen libremente y de conformidad una compensación a favor de el/la PRESTADOR/A DE SERVICIOS, por los servicios prestados, de PESOS __________________________________($ _______________) mensuales, pagaderos del uno al diez de cada mes en el domicilio de ______________________________________________.-\n";
        $TEXTO .= "\n";
        $TEXTO .= "QUINTA: Cualquiera de las partes podrá rescindir anticipadamente el presente contrato, debiendo notificar a la otra parte. En caso que la rescisión fuere realizada por el COMITENTE, éste deberá indemnizar a el/la PRESTADOR/A DE SERVICIOS por todos los gastos y trabajos realizados.-\n";
        $TEXTO .= "\n";
        $TEXTO .= "SEXTA: Las partes se someten a la jurisdicción de los tribunales ordinarios de la Ciudad de Córdoba, renunciando a cualquier otro fuero y/o jurisdicción que pudiera corresponderles y constituyen domicilio a los efectos del presente en los denunciados al comienzo del contrato. De conformidad y previa lectura  y ratificación se firman dos ejemplares de un mismo tenor y a un solo efecto, en la Ciudad de Córdoba, a los ____ días del mes de ____________ del año_____.-\n";
        $TEXTO .= "\n";

        $this->MultiCell(0,11,$TEXTO,0,'J'); 
        // debug($orden);
        // exit;
    }


    function imprime_contrato_descontar_2($orden){
        $this->AddPage();
        $this->PIE= false;
        $this->reset();
        $this->SetY(15);
        $this->SetX(10);

	$size = 12;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "CONTRATO",
			'borde' => '',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
        );
       
        $this->Imprimir_linea(); 
        $this->Ln(5);  
        $this->SetFont('times','');
        $this->SetFontSize(11);   
        $TEXTO = "Entre GRUPO TANTO S_A_ CUIT N° 30-71147195-9 con domicilio en _________________________ _______________,__________________________, de la Ciudad de _______________, denominado en adelante el \"VENDEDOR\", y ___________________________________________________ DNI (_______),con domicilio en _________________________________________________________________________________________, de la ciudad de __________________________________, ___________ denominado en adelante el \"COMPRADOR\"; se celebra el presente contrato de compraventa de cosa ajena, el que se regirá por las normas contenidas en el Código Civil y Comercial de la Nación en sus art_ 1008 y 1132 y las siguientes cláusulas:";
        $TEXTO .= "\n";
        $TEXTO .= "PRIMERA: El vendedor vende al comprador el/los siguiente/s bien/es mueble/s: ";
        $TEXTO .= "\n";
        $TEXTO .= "Marca:\nModelo:\nN° serie:\nOtras:";
        $TEXTO .= "\n";
        $TEXTO .= "SEGUNDA: El importe de la presente compraventa se fija en la suma total de PESOS _______________________________ ($ _____________) que será abonada de la siguiente forma:  ______ ( _____________) cuotas de pesos_______ cada una,  mediante transferencia bancaria/ depósito bancario en la cuenta corriente en pesos N° _______________ del Banco ______________ titularidad del vendedor, cheque o debito automático en la cuenta del comprador con CBU N°____________________ del banco __________, o como el vendedor lo dispongan en el futuro comunicando al comprador de manera fehaciente.";
        $TEXTO .= "\n";
        $TEXTO .= "TERCERA: el vendedor se obliga a transmitir o hacer transmitir su posesión al comprador y asume la obligación de adquirirla de su dueño para transmitírsela al comprador al abonarse la última cuota pactada en el presente.";
        $TEXTO .= "\n";
        $TEXTO .= "CUARTA: Para todos los efectos judiciales o extrajudiciales del presente contrato el vendedor y el comprador constituyen domicilio en los indicados ut-supra donde serán válidas todas las notificaciones que pudieran suscitarse como consecuencia de la interpretación y ejecución del presente, donde tendrán validez todas las notificaciones que allí se realicen.";
        $TEXTO .= "\n";
        $TEXTO .= "QUINTA: Toda controversia judicial derivada de este contrato será sometida a la competencia de los Tribunales Ordinarios de la Ciudad de Córdoba con renuncia a cualquier otro fuero o jurisdicción.-";
        $TEXTO .= "\n";
        $TEXTO .= "En carácter de DECLARACIÓN JURADA se firman dos (2) ejemplares de un mismo tenor a los ____________ días del mes de _______ de 20__. ";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');          
        $this->Ln(25);
        $size = 8;
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 80,
                'texto' => utf8_decode("Firma y Aclaración del Vendedor"),
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
	$this->linea[2] = array(
                'posx' => 90,
                'ancho' => 20,
                'texto' => "",
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
	$this->linea[3] = array(
                'posx' => 110,
                'ancho' => 80,
                'texto' => utf8_decode("Firma y Aclaración del Comprador"),
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );                    
        $this->Imprimir_linea();
        $this->Ln(5);  
        $this->SetFont('times','');
        $this->SetFontSize(11);         
        $TEXTO = "Firmado ante mi (en caso de certificar la identidad del comprador y vendedor un funcionario competente, deberá agregarse nombre, cargo y firma de este funcionario).";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');  
    }


    function imprime_croquis_ubicacion($orden){
        $this->AddPage();
        $this->PIE= false;
        $this->reset();
        $this->SetY(15);
        $this->SetX(10);

	$size = 10;
	$this->linea[1] = array(
			'posx' => 10,
			'ancho' => 190,
			'texto' => "CROQUIS DE UBICACION DE: " . utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']),
			'borde' => 'B',
			'align' => 'C',
			'fondo' => 0,
			'style' => 'B',
			'colorf' => '#D8DBD4',
			'size' => $size
        );  
        $this->Imprimir_linea();      
        $this->Ln(15);  
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[2] = array(
                'posx' => 60,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[3] = array(
                'posx' => 110,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
	$this->linea[4] = array(
                'posx' => 160,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );                        
        $this->Imprimir_linea();

        for($i = 1; $i <= 4; $i++){
                $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                ); 
                $this->linea[2] = array(
                        'posx' => 60,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                ); 
                $this->linea[3] = array(
                        'posx' => 110,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                );  
                $this->linea[4] = array(
                        'posx' => 160,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                );                        
                $this->Imprimir_linea();                 
        }
        
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[2] = array(
                'posx' => 60,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[3] = array(
                'posx' => 110,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
	$this->linea[4] = array(
                'posx' => 160,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );                        
        $this->Imprimir_linea(); 
        
        
        $this->Ln(15);  
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[2] = array(
                'posx' => 60,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[3] = array(
                'posx' => 110,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
	$this->linea[4] = array(
                'posx' => 160,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );                        
        $this->Imprimir_linea();

        for($i = 1; $i <= 4; $i++){
                $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                ); 
                $this->linea[2] = array(
                        'posx' => 60,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                ); 
                $this->linea[3] = array(
                        'posx' => 110,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                );  
                $this->linea[4] = array(
                        'posx' => 160,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                );                        
                $this->Imprimir_linea();                 
        }
        
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[2] = array(
                'posx' => 60,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[3] = array(
                'posx' => 110,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
	$this->linea[4] = array(
                'posx' => 160,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );                        
        $this->Imprimir_linea();
        
        $this->Ln(15);  
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[2] = array(
                'posx' => 60,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[3] = array(
                'posx' => 110,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
	$this->linea[4] = array(
                'posx' => 160,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'TLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );                        
        $this->Imprimir_linea();

        for($i = 1; $i <= 4; $i++){
                $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                ); 
                $this->linea[2] = array(
                        'posx' => 60,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                ); 
                $this->linea[3] = array(
                        'posx' => 110,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                );  
                $this->linea[4] = array(
                        'posx' => 160,
                        'ancho' => 30,
                        'texto' => '',
                        'borde' => 'LR',
                        'align' => 'C',
                        'fondo' => 1,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
                );                        
                $this->Imprimir_linea();                 
        }
        
	$this->linea[1] = array(
                'posx' => 10,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[2] = array(
                'posx' => 60,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        ); 
	$this->linea[3] = array(
                'posx' => 110,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );  
	$this->linea[4] = array(
                'posx' => 160,
                'ancho' => 30,
                'texto' => '',
                'borde' => 'BLR',
                'align' => 'C',
                'fondo' => 1,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
        );                        
        $this->Imprimir_linea();        

        $this->Ln(15);
        $this->SetFontSize(11);         
        $TEXTO = "1) Marque con una (X) la ubicación del domicilio y la altura al lado.";
        $TEXTO .= "\n";
        $TEXTO .= "2) Escriba el nombre de todas las calles aledañas.";
        $TEXTO .= "\n";
        $TEXTO .= "3) Dibuje o indique en el plano, cualquier referencia de utilidad.\n(Ej: Negocios, calles diagonales, plazas, etc)";
        $TEXTO .= "\n";        

        $this->MultiCell(0,11,$TEXTO,0,'J'); 


    }


    function imprime_modelo2_liquidacion_cuotas($orden){
        
        $this->PIE = false;
        
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);  
        
        
        
        //$this->imprimirMembrete(FALSE);
        
        
        $this->SetY(30);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"LIQUIDACION DEL PRESTAMO",0,0,'C');  
        $this->SetY(40);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);        
        
        $nroSolicitud = $orden['MutualProductoSolicitud']['nro_print'];
        $fecha = date('d-m-Y', strtotime($orden['MutualProductoSolicitud']['fecha']));
        $usuario = $orden['MutualProductoSolicitud']['user_created'];
        $vendedor = (isset($orden['MutualProductoSolicitud']['vendedor_nombre_min']) ? $orden['MutualProductoSolicitud']['vendedor_nombre_min'] : "");
        $this->SetY(10);
        $this->imprimirDatosGenerales(NULL,$fecha,NULL,NULL);        

        $this->ln(20);
        $this->imprimirDatosTitular($orden,TRUE);
        
        $this->ln(2);
        $TEXTO = ""; 
        $TEXTO .= "IMPORTANTE:";
        $TEXTO .= "\n\n";
        $TEXTO .= "* Para finalizar la solicitud debe verificar su número de celular enviando un WhatsApp al +54 9 351 516-5444 con su nro de DNI y nro de SOLICITUD.-";
        $TEXTO .= "\n\n";
        $TEXTO .= "* Usted es responsable del pago, en tiempo y forma, de las cuotas acordadas en su contrato de préstamo personal. Si advierte que no se ha realizado el débito correspondiente al mes, ponerse en contacto inmediatamente para regularizar su situación.";
        $TEXTO .= "\n\n";
        $TEXTO .= "* Los débitos figurarán en sus movimientos bancarios como “Margen Comercial”; nombre del agente de cobranzas asociado a la empresa.";
        $TEXTO .= "\n\n";
        $TEXTO .= "* ADVERTENCIA: En caso de que la irregularidad se prolongue, la empresa iniciará acciones legales, resultando en un EMBARGO de sus habares y agravando su deuda con mayores intereses y costes legales.";
        $TEXTO .= "\n\n";
        $TEXTO .= "* Cualquier duda o consulta, comunicarse por nuestros canales oficiales:";
        $TEXTO .= "\n- WhatsApp +54 9 351 516-5444 ";
        $TEXTO .= "\n- Instagram.com/Rolicred";
        $TEXTO .= "\n- Vélez Sarsfield 54 , oficina 4°A";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J'); 
        $this->ln(40);
        $this->reset();          

        $this->SetY(180);
        $this->imprimir_producto_solicitado($orden);
        
        $this->ln(3);
        $this->linea[0] = array(
                                'posx' => 15,
                                'ancho' => 20,
                                'texto' => 'NRO CUOTA',
                                'borde' => 'LTRB',
                                'align' => 'C',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );        
        $this->linea[1] = array(
                                'posx' => 35,
                                'ancho' => 20,
                                'texto' => 'MES (*)',
                                'borde' => 'LTRB',
                                'align' => 'C',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );
        $this->linea[2] = array(
                                'posx' => 55,
                                'ancho' => 20,
                                'texto' => ($orden['MutualProductoSolicitud']['fdoas'] == 1 ? "IMPORTE(**)" : "IMPORTE"),
                                'borde' => 'LTRB',
                                'align' => 'C',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#D8DBD4',
                                'size' => 8
                );         
        $this->Imprimir_linea();
        
        $oUT = new UtilHelper();
        
        foreach ($orden['MutualProductoSolicitud']['cronograma_de_vencimientos'] as $ncuo => $values) {
            
            $this->linea[0] = array(
                                    'posx' => 15,
                                    'ancho' => 20,
                                    'texto' => $ncuo,
                                    'borde' => 'LTRB',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => 8
                    );
            $this->linea[1] = array(
                                    'posx' => 35,
                                    'ancho' => 20,
                                    'texto' => $oUT->periodo($values['periodo'],FALSE,'/'),
                                    'borde' => 'LTRB',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => 8
                    );    
            $this->linea[2] = array(
                                    'posx' => 55,
                                    'ancho' => 20,
                                    'texto' => number_format($values['importe_cuota'],2),
                                    'borde' => 'LTRB',
                                    'align' => 'R',
                                    'fondo' => 0,
                                    'style' => '',
                                    'colorf' => '#D8DBD4',
                                    'size' => 8
                    );            
            $this->Imprimir_linea();             
            
        }
        $this->ln(3);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',6); 
        
        $TEXTO = "MES (*): SUJETO A LA FECHA DE APROBACION.-\n";
        if($orden['MutualProductoSolicitud']['fdoas'] == 1){
            $TEXTO .= "IMPORTE(**): CUOTA PURA. NO INCLUYE LA COBERTURA POR RIESGO CONTINGENTE DE $ " . number_format($orden['MutualProductoSolicitud']['fdoas_total_cuota'],2).". NO INCLUYE GASTOS ADMINISTRATIVOS POR LA GESTION DE COBRANZA.-\n";
        }
        
        $this->MultiCell(0,11,$TEXTO,0,'J'); 

        $this->ln(20);
        $this->firmaSocio();
        
//        $this->imprimirDatosTitular($orden,TRUE);
//        $this->SetY(60);
        

        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        
       
        
    }


    function ocom_imprime_mutuo_ryvsa($orden,$TEXTO,$imprimePlanilla=true,$showTEA=false, $imprimeLogo = false){
        $this->PIE = FALSE;
        
        $this->AddPage();
        $this->reset();
        $this->SetY(10);
        $this->SetX(10);

        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,"CONTRATO PARA PRESTAMOS PERSONALES",0,0,'C');    
        $this->SetFont(PDF_FONT_NAME_MAIN,'',9);
        $this->SetY(15);

        $tna = "____________________________ porciento (_____%)";
        $tem = "____________________________ porciento (_____%)";
        $cft = "____________________________ porciento (_____%)";

        $capital = "$ _________";
        $interes = "$ _________";
        $interesLetras = "_________________________________";



        if(!empty($orden['MutualProductoSolicitud']['tna']) && $orden['MutualProductoSolicitud']['tna'] != 0){ 
            $tna = number_format($orden['MutualProductoSolicitud']['tna'],2) . "%";
            if(!empty($orden['MutualProductoSolicitud']['tnm']) && $orden['MutualProductoSolicitud']['tnm'] != 0){ 
                $tem = number_format($orden['MutualProductoSolicitud']['tnm'],2) . "%";
                $interesMoratorio = number_format($orden['MutualProductoSolicitud']['tnm'] * 0.5,2) . "%";
                if(!empty($orden['MutualProductoSolicitud']['cft']) && $orden['MutualProductoSolicitud']['cft'] != 0){ 
                    $cft = number_format($orden['MutualProductoSolicitud']['cft'],2) . "%";
                }
            }
        }
        
        if(!empty($orden['MutualProductoSolicitud']['capital_puro']) && $orden['MutualProductoSolicitud']['capital_puro'] != 0){
            $capital = "$ ".number_format($orden['MutualProductoSolicitud']['capital_puro'],2);
            $interes = "$ ".number_format($orden['MutualProductoSolicitud']['importe_cuota'] - $orden['MutualProductoSolicitud']['capital_puro'],2);

        }

        $vtoPrimerCuota = "____________";
        $vtoUltimaCuota = "____________";
        $gtoAdminAlicuota = $selladoAlicuota =  $interesMoratorio = $costoCancelacionAnticipada = "____";
        $totalRetiene = "____________";
        $totalRetieneLetras = "_________________________";

        $tea = 0;
        
        if(!empty($orden['MutualProductoSolicitud']['detalle_calculo_plan'])){
            $objetoCalculado = json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']);
            // debug($objetoCalculado);
            // debug($objetoCalculado->detalleCuotas);
            
            $cuotas = (array) $objetoCalculado->detalleCuotas;
            
            $primeraCuota = array_shift($cuotas);
            $ultimaCuota = array_pop($cuotas);

//             $primeraCuota = array_shift($objetoCalculado->detalleCuotas);
//             $ultimaCuota = array_pop($objetoCalculado->detalleCuotas);

            $vtoPrimerCuota = date('d/m/Y',strtotime($primeraCuota->vtoSocio));
            $vtoUltimaCuota = date('d/m/Y',strtotime($ultimaCuota->vtoSocio));

            // debug($vtoPrimerCuota);
            // debug($vtoUltimaCuota);
            App::import('Model', 'mutual.MutualProductoSolicitud');
            $oSOL = new MutualProductoSolicitud(null);  
            $interes = $objetoCalculado->liquidacion->interesesDevengados;
            $interesLetras = $oSOL->num2letras($interes); 
            $interes = number_format($interes,2);
            
            $gtoAdminAlicuota = number_format($objetoCalculado->liquidacion->gastoAdminstrativo->porcentaje,2);
            $selladoAlicuota = number_format($objetoCalculado->liquidacion->sellado->porcentaje,2);

            $totalRetiene = $objetoCalculado->liquidacion->gastoAdminstrativo->importe + $objetoCalculado->liquidacion->sellado->importe;
            $totalRetieneLetras = $oSOL->num2letras($totalRetiene);
            $totalRetiene = number_format($totalRetiene,2);

            $interesMoratorio = number_format($objetoCalculado->liquidacion->interesMoratorio,2);
            $costoCancelacionAnticipada = number_format($objetoCalculado->liquidacion->costoCancelacionAnticipada,2);
            
            $tea = number_format($objetoCalculado->tea,2);

        }

        $fechaContrato = strtoupper($this->INI_FILE['general']['domi_fiscal_provincia'])
        .", "
        .$orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['string']
        ." de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']
        . " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'];        


        // debug($orden);
        // exit;

        // $TEXTO = "";
        // $TEXTO .= "En la Ciudad de CORDOBA, Provincia de CORDOBA, a los 14 días del mes de .MARZO del año 20.., RV S.A. C.U.I.T.  30-71525475-8, con domicilio en la calle 27 de abril N° 261, PB Dpto. F - Bº Centro, de la Ciudad de CORDOBA, Pvcia. de CORDOBA, en adelante denominado el \"MUTUANTE/ACREEDOR\" por una parte, representado en este acto por el abajo firmante en su carácter de apoderado, con facultades suficientes y vigentes para ello, y por la otra el Sr/a. Ramallo , Marcelo Fabian, DNI 20059230, con domicilio en la calle Martiniano Chilavert 3365 - CORDOBA (CP 5000) - CORDOBA, en adelante denominado el \"MUTUARIO/DEUDOR\", acuerdan celebrar el presente CONTRATO DE PRESTAMO DE CONSUMO, sujetándose a las siguientes cláusulas:";
        // $TEXTO .= "\n";
        // $TEXTO .= "PRIMERA: CRÉDITO. El MUTUARIO recibe en este acto, a su petición, con destino de consumo y a su entera satisfacción, de parte del MUTUANTE, la suma solicitada de PESOS ".trim($orden['MutualProductoSolicitud']['total_importe_solicitado_letras'])." ($ ".number_format($orden['MutualProductoSolicitud']['importe_solicitado'],2).") -con menos los gastos detallados en la clausula quinta- en efectivo o mediante la acreditación, simultanea con la firma del presente, en la cuenta de Caja de Ahorro Nro. ".$orden['MutualProductoSolicitud']['beneficio_cuenta']." CBU ".$orden['MutualProductoSolicitud']['beneficio_cbu']." abierta en el ".$orden['MutualProductoSolicitud']['beneficio_banco']." Sucursal ".$orden['MutualProductoSolicitud']['beneficio_sucursal'].", a elección del solicitante, sirviendo el presente de suficiente recibo.-";
        // $TEXTO .= "\n";
        // $TEXTO .= "SEGUNDA: DEVOLUCIÓN DEL CRÉDITO. En base a los requerimientos y posibilidades del solicitante, el préstamo será restituido al MUTUANTE en ". trim($orden['MutualProductoSolicitud']['cantidad_cuota_letras'])." (". trim($orden['MutualProductoSolicitud']['cuotas']).") cuotas mensuales, iguales y consecutivas. El vencimiento de las mismas se producirá el primer día de cada mes, pudiendo el deudor abonarla sin cargo hasta el décimo (10) día de cada mes; la primer cuota vence el día $vtoPrimerCuota y la ultima el día $vtoUltimaCuota.-";
        // $TEXTO .= "\n";
        // $TEXTO .= "TERCERA: INTERESES COMPENSATORIOS. El crédito devengará una tasa de interés nominal anual – T.N.A. – del $tna, equivalente a una tasa efectiva de interés mensual – T.E.M. – del $tem lo que hace un total de intereses a pagar de PESOS $interesLetras ($ $interes) conforme al sistema de amortización directo. Por lo que la suma total adeudada y a restituir es de PESOS ".$orden['MutualProductoSolicitud']['total_letras']." ($ ".number_format($orden['MutualProductoSolicitud']['importe_total'],2).") dicho monto incluye el componente impositivo IVA.-";
        // $TEXTO .= "\n";
        // $TEXTO .= "CUARTA: CUOTA MENSUAL. El valor de la cuota mensual a pagar mensualmente es de PESOS ".$orden['MutualProductoSolicitud']['total_cuota_letras']." ($ ".number_format($orden['MutualProductoSolicitud']['importe_cuota'],2)."). El pago de la cuota cuyo vencimiento coincidiera con un día inhábil, sábado o domingo, se producirá indefectiblemente el día hábil inmediato siguiente.-";
        // $TEXTO .= "\n";
        // $TEXTO .= "QUINTA: GASTOS. Todo gasto, sellado e importe que grave la operación estarán a cargo del DEUDOR. Los gastos imprescindibles y obligatorios para la realización de la presente operación solicitada por el deudor arrojan un valor total de $totalRetieneLetras ($ $totalRetiene); son: A) Impuesto de sellado del presente contrato más el Impuesto de sellado del pagare ($selladoAlicuota% de la suma total adeudada) B) Gastos de comisión,otorgamiento y análisis de calificación crediticia ($gtoAdminAlicuota% sobre la suma solicitada).- ";
        // $TEXTO .= "\n";
        // $TEXTO .= "SEXTA: EL PAGO. El pago debe realizarlo en efectivo hasta el vencimiento de la cuota, en el domicilio del MUTUANTE o en el lugar en que éste oportunamente lo indique, dentro de la misma plaza y del horario apertura al público. Las cuotas se imputarán a todos los valores por igual como se ha detallado. El pago parcial no significará espera, quita, transacción o novación de la cuota, siendo imputable dicha suma previamente a intereses, y el saldo se acumulará a la del mes siguiente con más los intereses moratorios que devengue lo no abonado. Se tomaran como validos todos los pagos hechos por el deudor en cualquiera de las bocas de pagos habilitadas por el MUTUANTE, tales como Rapipago, Pago Facil, Cobro Express, MercadoPago, Pagos con tarjetas de debito de forma presencial o virtual, PagosMisCuentas y todas aquellas que el Mutuante habilite en un futuro, los cuales serán informados al Mutuario.-";
        // $TEXTO .= "\n";
        // $TEXTO .= "SÉPTIMA: GARANTIA – RENUNCIA A LA INEMBARGABILIDAD: El MUTUARIO/DEUDOR declara expresamente renunciar al derecho de inembargabilidad sobre los haberes, jubilaciones, pensiones, indemnizaciones u otros beneficios patrimoniales nacionales o provinciales, que percibe actualmente o en un futuro, los cuales afecta voluntariamente a constituirlos como garantía de cumplimiento de la presente obligación hasta un máximo de un 20% mensual de los mismos, por embargo judicial (ley 8024 Art. 45 inc. C; Ley 24.241 Art. 14 Inc. C; Decreto Ley 6.754; LEY N° 22.919 art. 22; entre otros).-";
        // $TEXTO .= "\n";
        // $TEXTO .= "OCTAVA: LIBRAMIENTO DE PAGARE: El Mutuario acepta expresamente documentar la deuda en un pagare el cual se librará por el monto total adeudado, con la misma fecha de libramiento del presente y la fecha de vencimiento quedará en blanco a los fines de ser completada con el primer periodo adeudado o en mora – conforme clausula decimoprimera-. El Mutuante podrá a su sola opción, iniciar la ejecución con cualquiera de los documentos que prefiera, accionar contra el Mutuante/Deudor y/o sus garantes, avalistas o codeudores, en forma individual o conjunta. Una vez cancelado el crédito, a requerimiento del Mutuario, se devolverá el documento pagare, siempre que lo requiera dentro de los noventa días contados a partir de la fecha de cancelación del mismo. Vencido este plazo, el acreedor procederá a su destrucción, sin que ello genere reclamo alguno por parte del Mutuario.-";
        // $TEXTO .= "\n";
        // $TEXTO .= "NOVENA: CANCELACIÓN ANTICIPADA: Los plazos se presumen establecidos en beneficio de ambas partes, según lo que de común acuerdo han establecido, dejando a salvo el derecho del DEUDOR de ejercer la cancelación anticipada total o parcial del crédito. Con la cancelación anticipada solo se quitarán los intereses de las cuotas no vencidas. Para optar por la cancelación anticipada, el deudor no deberá encontrarse en mora, haber transcurrido la mitad del plazo del préstamo y deberá abonar el total del capital bruto en efectivo con más un diez por ciento ($costoCancelacionAnticipada%) del capital remanente, lo que el MUTUANTE acepta como compensación razonable por el otorgamiento del crédito, como también todos los gastos de gestión, impuestos y costos. Podrá optar por la cancelación anticipada en cualquier momento de la relación contractual. -";
        // $TEXTO .= "\n";
        // $TEXTO .= "DÉCIMA: MORA: La mora se producirá de pleno derecho y sin necesidad de requerimiento o interpelación judicial o extrajudicial alguna, por el simple incumplimiento del MUTUARIO en los plazos pactados de cualquiera de las obligaciones; por la falta de pago de una cuota, o el pago insuficiente o parcial de una de ellas. -";
        // $TEXTO .= "\n";
        // $TEXTO .= "DÉCIMO PRIMERA: CONSECUENCIAS DE LA MORA – CADUCIDAD DE LOS PLAZOS: A los 60 días del acaecimiento del supuesto de la cláusula anterior sin regularizar su situación, producirá de pleno derecho la caducidad de todos los plazos, haciéndose exigible la inmediata e íntegra devolución y reembolso del capital desembolsado por el MUTUANTE, con más los intereses compensatorios y moratorios pactados hasta la total devolución del capital adeudado con más los intereses judiciales, honorarios y costos que se originen como consecuencia del procedimiento de ejecución (1088, 1089 y 1529 CCCN).-";
        // $TEXTO .= "\n";
        // $TEXTO .= "DÉCIMO SEGUNDA: INTERÉS MORATORIO - CAPITALIZACIÓN: En todos los casos de mora, sobre el saldo del capital debido, se calculará un interés moratorio del $interesMoratorio% mensual (es decir, 50% del T.E.M.) a contabilizar desde el primer día de cada mes. Se pacta expresamente que en estos casos, tanto el interés compensatorio como el moratorio, se capitalizarán a partir de los seis meses de mora, en los términos del art. 770 del C. Civil y Comercial de la Nación. ";
        // $TEXTO .= "\n";

        // // $this->MultiCell(0,11,$TEXTO,0,'J');

        // // $this->PIE = FALSE;
        
        // // $this->AddPage();
        // // $this->reset();
        // // $this->SetY(10);
        // // $this->SetX(10);

   
        // // $this->SetFont(PDF_FONT_NAME_MAIN,'',9);
        // // $this->SetY(15);        

        // $TEXTO .= "DÉCIMO TERCERA: CESIÓN DEL CRÉDITO. El MUTUANTE podrá transferir el presente, por cualquiera de los medios previstos en la ley, adquiriendo el o los cesionarios los mismos beneficios y/o derechos y/o acciones del ACREEDOR bajo el presente contrato. De optar por la cesión prevista en los artículos 70 a 72 de la Ley 24.441, la cesión del crédito y su garantía podrá hacerse sin notificación al DEUDOR y tendrá validez desde su fecha de formalización, en un todo de acuerdo con lo establecido por el artículo 72 de la ley precitada. El MUTUARIO expresamente manifiesta que, tal como lo prevé la mencionada ley, la cesión tendrá efecto desde la fecha en que se opere la misma y que sólo podrá oponer contra el cesionario las excepciones previstas en el mencionado artículo. No obstante, en el supuesto que la cesión implique modificación del domicilio de pago, el nuevo domicilio de pago deberá notificarse en forma fehaciente a la parte deudora. Habiendo mediado modificación del domicilio de pago, no podrá oponerse excepción de pago documentado, en relación a pagos practicados a anteriores cedentes con posterioridad a la notificación del nuevo domicilio de pago. -";
        // $TEXTO .= "\n";
        // $TEXTO .= "DÉCIMO CUARTA: INFORMACIÓN. El DEUDOR reconoce que ha sido debidamente informado sobre todas las condiciones establecidas para el otorgamiento del Crédito, tanto en la FICHA INFORMATIVA DEL PRÉSTAMO PERSONAL SOLICITADO como en el CONTRATO PARA PRÉSTAMOS PERSONALES, todo de conformidad con lo dispuesto por el art. 4° de Ley N° 24.240, su reforma por la Ley 26.361 y demás normas que regulan las relaciones de consumo, aceptando conocer su contenido. -        El deudor denuncia el siguiente correo electrónico ".(!empty($orden['MutualProductoSolicitud']['beneficiario_e_mail']) ? $orden['MutualProductoSolicitud']['beneficiario_e_mail'] : "________________________________")." y número de teléfono celular ".$orden['MutualProductoSolicitud']['beneficiario_telefono_movil'].", donde opta de forma expresa recibir las notificaciones e intimaciones por medio de mail y/o wathsapp y/o SMS, considerando las mismas validas y suficientes.-";
        // $TEXTO .= "\n";
        // $TEXTO .= "DÉCIMO QUINTA: DE FORMA. En caso de controversia las partes se someterán a los Tribunales Ordinarios de la Provincia de CORDOBA, renunciado al fuero federal. De conformidad las partes suscriben dos (2) ejemplares en un mismo tenor y a un sólo efecto, en la ciudad de ".strtoupper($this->INI_FILE['general']['domi_fiscal_provincia']).", a los ".$orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['string']." días del mes de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']." de ".$orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'].".-";
        // $TEXTO .= "\n";
        // $TEXTO .= "\n";

        $margenes = $this->getMargins();
        $this->SetRightMargin(15);
        $this->SetLeftMargin(15);
        $this->SetAutoPageBreak(true,25);

        $this->MultiCell(0,11,$TEXTO,0,'J',0,1,'',0); 

        $this->SetRightMargin($margenes['right']);
        $this->SetLeftMargin($margenes['left']); 
        $this->SetAutoPageBreak(true,$margenes['bottom']);       
        
        $this->ln(20);
        ########################################################################################
        #FIRMAS
        ########################################################################################
        $this->linea[1] = array(
            'posx' => 30,
            'ancho' => 50,
            'texto' => "",
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 2
        );
        $this->linea[2] = array(
            'posx' => 80,
            'ancho' => 40,
            'texto' => "",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 2
        );   
        $this->linea[2] = array(
            'posx' => 120,
            'ancho' => 50,
            'texto' => "",
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 2
        );             
        $this->Imprimir_linea();

        $this->linea[1] = array(
            'posx' => 30,
            'ancho' => 50,
            'texto' => "Firma Mutuante / Acreedor",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 8
        );
        $this->linea[2] = array(
            'posx' => 80,
            'ancho' => 40,
            'texto' => "",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 8
        );   
        $this->linea[2] = array(
            'posx' => 120,
            'ancho' => 50,
            'texto' => "Firma Mutuario / Deudor",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 8
        );             
        $this->Imprimir_linea();        
        $this->ln(10);
        $this->linea[1] = array(
            'posx' => 30,
            'ancho' => 50,
            'texto' => "",
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 2
        );
        $this->linea[2] = array(
            'posx' => 80,
            'ancho' => 40,
            'texto' => "",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 2
        );   
        $this->linea[2] = array(
            'posx' => 120,
            'ancho' => 50,
            'texto' => "",
            'borde' => 'T',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 2
        );             
        $this->Imprimir_linea();

        $this->linea[1] = array(
            'posx' => 30,
            'ancho' => 50,
            'texto' => utf8_decode("Aclaración o Sello"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 8
        );
        $this->linea[2] = array(
            'posx' => 80,
            'ancho' => 40,
            'texto' => "",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 8
        );   
        $this->linea[2] = array(
            'posx' => 120,
            'ancho' => 50,
            'texto' => utf8_decode("Aclaración o Sello"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 8
        );             
        $this->Imprimir_linea();         

        // $this->firmaSocio(); 
        
        if(!$imprimePlanilla) {return;}
        
        ########################################################################################
        # ficha informativa
        ########################################################################################
        $this->PIE = false;

       
        
        $this->AddPage();
        $this->reset();

        $membrete = array(
            'L1' => $orden['MutualProductoSolicitud']['proveedor_full_name'],
            'L2' => "C.U.I.T. " . $orden['MutualProductoSolicitud']['proveedor_cuit'],
            'L3' => $orden['MutualProductoSolicitud']['proveedor_domicilio']." - CP " . $orden['MutualProductoSolicitud']['proveedor_localidad'],
        );

        $this->SetY(10);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',14);
        if ($imprimeLogo) {
            $fileLogo = IMAGES . 'logos' . DS . Configure::read('APLICACION.logo_pdf');
            if(file_exists($fileLogo)){ 
                $this->logo = $fileLogo;
                $this->Image($this->logo,5,1,45,22);
                $this->SetFont(PDF_FONT_NAME_MAIN,'B',11);
            }
            $this->SetY(17);              
        }
        $this->SetX(10);

        
        $this->Cell(0,5,$membrete['L1'],0);
        $this->Ln(5);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(0,5,$membrete['L2'],0);
        $this->Ln(3);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(0,5,$membrete['L3'],0);
        $this->SetY(30);



        $this->SetFont(PDF_FONT_NAME_MAIN,'B',13);
        $this->Cell(190,5,utf8_decode("FICHA INFORMATIVA DEL PRESTAMO PERSONAL SOLICITADO"),0,0,'C');    
        $this->SetFont(PDF_FONT_NAME_MAIN,'',9);
        $this->SetY(40);
        $this->SetFont('courier','',9);
        
        $size = 10;

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "Solicitante: " . utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']) . " " . $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'] . "  CUIT/CUIL: " . $orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'],
            'borde' => 'TLR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();   
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "Lugar de Trabajo: " . utf8_decode($orden['MutualProductoSolicitud']['turno_desc']),
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();               

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "Domicilio: " . utf8_decode($orden['MutualProductoSolicitud']['beneficiario_domicilio']),
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea(); 

        // debug($orden);

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "Celular: " . utf8_decode($orden['MutualProductoSolicitud']['beneficiario_telefono_movil']) . " Telefonos: " . $orden['MutualProductoSolicitud']['beneficiario_telefono_fijo'] . " " . $orden['MutualProductoSolicitud']['beneficiario_telefono_referencia'] . "(".$orden['MutualProductoSolicitud']['beneficiario_persona_referencia'].")",
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );

        $this->Imprimir_linea();  
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "Email: " . $orden['MutualProductoSolicitud']['beneficiario_e_mail'],
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();    
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "CBU: " . $orden['MutualProductoSolicitud']['beneficio_cbu'] . " Banco: " . $orden['MutualProductoSolicitud']['beneficio_banco'],
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "Cuenta: " . $orden['MutualProductoSolicitud']['beneficio_cuenta'] . " Sucursal: " . $orden['MutualProductoSolicitud']['beneficio_sucursal'],
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea(); 
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "",
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 5
        );
        $this->Imprimir_linea(); 
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 40,
            'texto' => "Capital Solicitado",
            'borde' => 'L',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 50,
            'ancho' => 25,
            'texto' => number_format($orden['MutualProductoSolicitud']['importe_solicitado'],2),
            'borde' => '',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[3] = array(
            'posx' => 75,
            'ancho' => 34,
            'texto' => utf8_decode("Deducción Gtos."),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[4] = array(
            'posx' => 109,
            'ancho' => 25,
            'texto' => $totalRetiene,
            'borde' => '',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[5] = array(
            'posx' => 134,
            'ancho' => 36,
            'texto' => "Monto a Percibir",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[6] = array(
            'posx' => 170,
            'ancho' => 30,
            'texto' => number_format($orden['MutualProductoSolicitud']['importe_percibido'],2),
            'borde' => 'R',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );                                        
        $this->Imprimir_linea(); 
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "",
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 3
        );
        $this->Imprimir_linea();         


        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 15,
            'texto' => ( $showTEA ? "T.E.A." : "T.N.A."),
            'borde' => 'L',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 25,
            'ancho' => 20,
            'texto' => ($showTEA ? $tea : $orden['MutualProductoSolicitud']['tna']). "%",
            'borde' => '',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[3] = array(
            'posx' => 45,
            'ancho' => 15,
            'texto' => "T.E.M.",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[4] = array(
            'posx' => 60,
            'ancho' => 20,
            'texto' => $orden['MutualProductoSolicitud']['tnm']. "%",
            'borde' => '',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[5] = array(
            'posx' => 80,
            'ancho' => 15,
            'texto' => "Cuotas",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[6] = array(
            'posx' => 95,
            'ancho' => 7,
            'texto' => $orden['MutualProductoSolicitud']['cuotas'],
            'borde' => '',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[7] = array(
            'posx' => 102,
            'ancho' => 22,
            'texto' => "Intereses",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[8] = array(
            'posx' => 124,
            'ancho' => 25,
            'texto' => $interes,
            'borde' => '',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[9] = array(
            'posx' => 149,
            'ancho' => 25,
            'texto' => "Monto Cuota",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[10] = array(
            'posx' => 174,
            'ancho' => 26,
            'texto' => number_format($orden['MutualProductoSolicitud']['importe_cuota'],2),
            'borde' => 'R',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );                                                                        
        $this->Imprimir_linea(); 

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "",
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 3
        );
        $this->Imprimir_linea(); 

        $objetoCalculado = json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']);

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 25,
            'texto' => "Sist.Amort.",
            'borde' => 'L',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 35,
            'ancho' => 17,
            'texto' => $objetoCalculado->metodoCalculoFormula,
            'borde' => '',
            'align' => 'L',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[3] = array(
            'posx' => 52,
            'ancho' => 30,
            'texto' => "Periodicidad",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        ); 
        $this->linea[4] = array(
            'posx' => 80,
            'ancho' => 20,
            'texto' => "MENSUAL",
            'borde' => '',
            'align' => 'L',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        ); 
        $this->linea[5] = array(
            'posx' => 100,
            'ancho' => 25,
            'texto' => "Primer Vto.",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        ); 
        $this->linea[6] = array(
            'posx' => 125,
            'ancho' => 25,
            'texto' => $vtoPrimerCuota,
            'borde' => '',
            'align' => 'L',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[7] = array(
            'posx' => 150,
            'ancho' => 25,
            'texto' => utf8_decode("Ultimo Vto."),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[8] = array(
            'posx' => 175,
            'ancho' => 25,
            'texto' => $vtoUltimaCuota,
            'borde' => 'R',
            'align' => 'L',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );                                                       
        $this->Imprimir_linea(); 

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "",
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 3
        );
        $this->Imprimir_linea();   
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 75,
            'texto' => utf8_decode("Ult. día de pago s/recargo (c/mes)"),
            'borde' => 'L',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
         
        $this->linea[2] = array(
            'posx' => 85,
            'ancho' => 10,
            'texto' => date('d',strtotime($primeraCuota->vtoSocio)),
            'borde' => '',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        
        $this->linea[3] = array(
            'posx' => 95,
            'ancho' => 37,
            'texto' => "Int.Mora Mensual",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );        
        
        $this->linea[4] = array(
            'posx' => 132,
            'ancho' => 15,
            'texto' => $interesMoratorio . "%",
            'borde' => '',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );

        $this->linea[5] = array(
            'posx' => 147,
            'ancho' => 38,
            'texto' => utf8_decode("Costo Cancelación"),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        
        $this->linea[6] = array(
            'posx' => 185,
            'ancho' => 15,
            'texto' => $costoCancelacionAnticipada . "%",
            'borde' => 'R',
            'align' => 'R',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );        

        $this->Imprimir_linea();

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "",
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 3
        );
        $this->Imprimir_linea();   
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 50,
            'texto' => utf8_decode("Monto Total Financiado"),
            'borde' => 'L',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );

        $this->linea[2] = array(
            'posx' => 60,
            'ancho' => 140,
            'texto' =>  str_pad("$ ".number_format($orden['MutualProductoSolicitud']['importe_total'],2)." ",65,'/',STR_PAD_RIGHT),
            'borde' => 'R',
            'align' => 'L',
            'fondo' => 1,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        //$orden['MutualProductoSolicitud']['importe_total']
        // $this->linea[3] = array(
        //     'posx' => 85,
        //     'ancho' => 50,
        //     'texto' => utf8_decode($orden['MutualProductoSolicitud']['total_letras']),
        //     'borde' => 'L',
        //     'align' => 'L',
        //     'fondo' => 1,
        //     'style' => 'B',
        //     'colorf' => '#D8DBD4',
        //     'size' => $size - 2
        // );        
        
        $this->Imprimir_linea();

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "",
            'borde' => 'LR',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 3
        );
        $this->Imprimir_linea();   
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 40,
            'texto' => utf8_decode("Domicilio de Pago"),
            'borde' => 'L',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 50,
            'ancho' => 150,
            'texto' => substr(utf8_decode($orden['MutualProductoSolicitud']['proveedor_pagare_direccion']),0,70),
            'borde' => 'R',
            'align' => 'L',
            'fondo' => 1,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );        
        $this->Imprimir_linea();
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "",
            'borde' => 'LRB',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => 3
        );
        $this->Imprimir_linea(); 
        
        $this->ln(10);

        $this->SetFont(PDF_FONT_NAME_MAIN,'',11);
        $TEXTO = "El solicitante  manifiesta que:";
        $TEXTO .= "\n";
        $TEXTO .= "* Recibió un trato digno, asistencia personalizada y que le suministraron las explicaciones suficientes a los fines de evaluar el contrato de crédito propuesto y considera que el mismo se ajusta a sus necesidades, a sus intereses y a su situación financiera.-";
        $TEXTO .= "\n";
        $TEXTO .= "* De conformidad con la ley 25.326, cuyos términos conoce, declara en relación a la totalidad de los datos que proporciona, que ninguno de los mismos son sensibles, además son exactos, ciertos y presta irrevocable conformidad para que los mismos sean utilizados en relación con el producto o servicio que solicita a ".$orden['MutualProductoSolicitud']['proveedor_full_name'].", como en el futuro recibir ofrecimiento o promociones. Asimismo se obliga a que cualquier cambio o inexactitud será informado inmediatamente a ".$orden['MutualProductoSolicitud']['proveedor_full_name'].".-";
        $TEXTO .= "\n";
        $TEXTO .= "* Por lo expuesto se obliga a poner en conocimiento de la empresa cualquier cambio de domicilio, dirección de mail o de número telefónico, bajo su responsabilidad.-";
        $TEXTO .= "\n";
        $TEXTO .= "* Recibe copia de los documentos subscriptos ficha informativa y contrato de préstamo personal confeccionado en base a los datos aquí suministrados.-";
        $TEXTO .= "\n";

        $this->MultiCell(0,11,$TEXTO,0,'J');

        $this->ln(20);
        $this->firmaSocio();
        //$interesMoratorio
        // debug($objetoCalculado);
        // debug($orden);
        
        $this->ln(20);
//        if(!empty($orden['MutualProductoSolicitud']['barcode'])){
//            $this->barCode($orden['MutualProductoSolicitud']['barcode']);
//        }
        
        
    }


    function autorizacionCobranzaDebitoDirectoGeneral($orden, $gtoAdminOwner = null, $autoDebitoOwner = NULL){

        $nroSolicitud = $orden['MutualProductoSolicitud']['nro_print'];
        $fecha = date('d-m-Y',strtotime($orden['MutualProductoSolicitud']['fecha']));
        $usuario = $orden['MutualProductoSolicitud']['user_created'];
        $vendedor = (isset($orden['MutualProductoSolicitud']['vendedor_nombre_min']) ? $orden['MutualProductoSolicitud']['vendedor_nombre_min'] : "");
        
        $L1 = ( !empty($autoDebitoOwner) ? $autoDebitoOwner : Configure::read('APLICACION.nombre_fantasia'));
        $L2 = Configure::read('APLICACION.domi_fiscal');
        $L3 = "TEL: " . Configure::read('APLICACION.telefonos') ." - email: ".Configure::read('APLICACION.email');

        $membrete = array(
            'L1' => $L1,
            'L2' => $L2,
            'L3' => $L3
        );
        
        $this->AddPage();
        $this->reset();
        
        $this->SetFont('courier','',12);
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',14);
        $this->Cell(0,5,$membrete['L1'],0);
        $this->Ln(5);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(0,5,$membrete['L2'],0);
        $this->Ln(3);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $this->Cell(0,5,$membrete['L3'],0);
        $this->Ln(4);
        
        $this->SetY(10);
        $this->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor);
        $size = 16;
        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => "AUTORIZACION DE COBRANZA POR DEBITO DIRECTO",
                'borde' => '',
                'align' => 'C',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();
        
        
        $this->SetFontSize(8);
        $TEXTO = "Por la presente, AUTORIZO a $L1, a debitar de mi Cuenta Corriente / Caja de ahorros detallada en la presente, los montos que correspondan según el plan comercial al que adhiero, en forma mensual y consecutiva a partir de la fecha de aprobación de la operación solicitada, en un todo de acuerdo con los datos consignados en esta autorización.\n";
        $this->MultiCell(0,11,$TEXTO);
        
        $this->SetY(62);
        $this->imprimirDatosTitular($orden);
        $size = 10;
        $sized = 13;

        #############################################################################################
        # CUENTA PARA DEBITO
        #############################################################################################
        $this->imprimirDatosCuentaDebito($orden);



        #############################################################################################
        # DATOS PLAN COMERCIAL
        #############################################################################################
        $this->ln(4);
        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => utf8_decode("Datos del Plan Comercial"),
                'borde' => 'B',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(4);


        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 55,
                'texto' => utf8_decode("EMPRESA PROVEEDORA:"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $sized
        );
        $this->linea[2] = array(
                'posx' => 65,
                'ancho' => 90,
                'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_full_name']),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $sized
        );
        $this->linea[3] = array(
                'posx' => 155,
                'ancho' => 15,
                'texto' => utf8_decode("CUIT:"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $sized
        );
        $this->linea[4] = array(
                'posx' => 170,
                'ancho' => 30,
                'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_cuit']),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $sized
        );
        $this->Imprimir_linea();


        $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 55,
                'texto' => utf8_decode("Monto Total Autorizado: $"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[2] = array(
                'posx' => 65,
                'ancho' => 22,
                'texto' => number_format($orden['MutualProductoSolicitud']['importe_total'],2),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[3] = array(
                'posx' => 87,
                'ancho' => 43,
                'texto' => utf8_decode("Cantidad de Cuotas:"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[4] = array(
                'posx' => 130,
                'ancho' => 7,
                'texto' => utf8_decode($orden['MutualProductoSolicitud']['cuotas_print']),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[5] = array(
                'posx' => 137,
                'ancho' => 40,
                'texto' => utf8_decode("Monto de Cuota: $"),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->linea[6] = array(
                'posx' => 177,
                'ancho' => 23,
                'texto' => number_format($orden['MutualProductoSolicitud']['importe_cuota'],2),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
        );
        $this->Imprimir_linea();

        #############################################################################################
        # TEXTO
        #############################################################################################
        $this->ln(1);
        $this->SetFontSize(8);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $TEXTO = "Dejo Expresa constancia que en caso de no poderse realizar la cobranza de la forma pactada, AUTORIZO en forma expresa a $L1 o a la empresa proveedora, a seguir realizando los descuentos correspondientes a la total cancelación de las obligaciones por mi contraídas en este acto, con mas los intereses y gastos por mora que pudieren corresponder.-\n\n";
        $TEXTO .= "Asimismo, AUTORIZO en forma expresa a $L1 a que en caso de no poseer fondos de manera consecutiva en la cuenta indicada, o de cambiar la cuenta en la que se acreditan mis haberes, la cobranza sea direccionada a la cuenta de mi titularidad que posea fondos para la cancelación de las obligaciones contraídas.-\n";
        $this->MultiCell(0,8,$TEXTO);


        //FONDO DE ASISTENCIA
        $this->imprimirFdoAs($orden);

        if(empty($gtoAdminOwner)) {
            $gtoAdminOwner = Configure::read('APLICACION.nombre_fantasia');
        }

        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        if(isset($INI_FILE['general']['cuota_social_permanente']) && $INI_FILE['general']['cuota_social_permanente'] == 0):
            $this->ln(2);
            $this->linea[1] = array(
                    'posx' => 10,
                    'ancho' => 190,
                    'texto' => "GASTOS ADMINISTRATIVOS",
                    'borde' => '',
                    'align' => 'C',
                    'fondo' => 0,
                    'style' => 'B',
                    'colorf' => '#D8DBD4',
                    'size' => 8
            );
            $this->Imprimir_linea();
            $this->SetFontSize(8);
            $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
            $TEXTO = "AUTORIZO en forma expresa a $gtoAdminOwner a debitar de mi Cuenta Bancaria el gasto administrativo mensual que se genera por la presente cobranza, el cual tendrá vigencia mientras exista saldo deudor de la operación detallada en el presente, y la cobranza de dicho saldo sea gestionado por $gtoAdminOwner.-\n";
            $this->MultiCell(0,8,$TEXTO);
        endif;

        $this->ln(20);
        $this->firmaSocio();
        $this->ln(3);
        $this->barCode($orden['MutualProductoSolicitud']['barcode']);


    }

    
    function imprimirRecibo($orden) {

        $this->AddPage();
        $this->reset();
        $size = 11;
        
//         debug($orden);
        
        $this->SetFont('courier','',12);
        
        $fechaRecibo = $orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']
            ." de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']
            . " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero'];
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode($fechaRecibo),
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->Ln(5);
        
        $this->linea[2] = array(
            'posx' => 10,
            'ancho' => 20,
            'texto' => "Nombre: ",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[3] = array(
            'posx' => 30,
            'ancho' => 60,
            'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[4] = array(
            'posx' => 90,
            'ancho' => 10,
            'texto' => "DNI",
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[1] = array(
            'posx' => 100,
            'ancho' => 90,
            'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_ndoc']),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        $this->Ln(5);
        
        $proveedorFullName = $orden['MutualProductoSolicitud']['proveedor_full_name'];
        if($orden['MutualProductoSolicitud']['proveedor_pagare_blank']) {
            $proveedorFullName = "_________________________________________";
        }
        
        $this->linea[2] = array(
            'posx' => 10,
            'ancho' => 20,
            'texto' => utf8_decode("Recibí de " . $proveedorFullName),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size + 4
        );
        $this->Imprimir_linea();
        
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 50,
            'texto' => "La cantidad de pesos ",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size,
//             'family' => 'helvetica'
        );
        $this->linea[2] = array(
            'posx' => 60,
            'ancho' => 140,
            'texto' => str_pad($orden['MutualProductoSolicitud']['total_importe_solicitado_letras']." ",59,'/',STR_PAD_RIGHT),
            'borde' => '',
            'align' => 'L',
            'fondo' => 1,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size,
//             'family' => 'helvetica'
        );
        $this->Imprimir_linea();
        
//         $TEXTO = "En concepto de liquidación de crédito Nro " . $orden['MutualProductoSolicitud']['nro_print'];
        $TEXTO = "En concepto de liquidación de crédito";
        $TEXTO .= " otorgado a mi favor el día " . date('d/m/Y',strtotime($orden['MutualProductoSolicitud']['fecha']));
        $TEXTO .= " la cual me comprometo a restituir en los términos y condiciones pactados en el Contrato de Préstamo Personal.\n";

        $this->SetFont('courier','',11);
        $this->MultiCell(0,9,$TEXTO);
        $this->ln(20);
        $this->firmaSocio();
//         debug($orden);
        
        
        
    }
    
    function imprimirInstruccionDePago($orden){
        
        $nroSolicitud = $orden['MutualProductoSolicitud']['nro_print'];
        $fecha = date('d-m-Y',strtotime($orden['MutualProductoSolicitud']['fecha']));
        $usuario = $orden['MutualProductoSolicitud']['user_created'];
        $vendedor = (isset($orden['MutualProductoSolicitud']['vendedor_nombre_min']) ? $orden['MutualProductoSolicitud']['vendedor_nombre_min'] : "");
        
        
        $this->AddPage();
        $this->reset();
        
        $this->ln(4);
        $this->imprimirDatosGenerales($nroSolicitud,$fecha,$usuario,$vendedor);
        $size = 10;
        $size = 16;
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "INSTRUCCION DE PAGO",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(4);
        $size = 11;
        $this->SetFontSizeConf($size);
        $TEXTO = "Por medio de la presente, el/la que suscribe ".$orden['MutualProductoSolicitud']['beneficiario_apenom'].", con ".$orden['MutualProductoSolicitud']['beneficiario_tdocndoc'].", como solicitante y adjudicatario del crédito según Solicitud Nº ".$orden['MutualProductoSolicitud']['nro_print'].", INSTRUYO y ORDENO irrevocablemente a _______________________, para que los fondos netos resultantes del mismo sean pagados de la siguiente manera:\n";
        $this->MultiCell(0,11,$TEXTO);
        
        //$orden['MutualProductoSolicitudInstruccionPago'] = null;
        
        if(!isset($orden['MutualProductoSolicitudInstruccionPago']) || empty($orden['MutualProductoSolicitudInstruccionPago'])):
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 102,
            'texto' => "1) A mi orden personal, el importe de pesos",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 112,
            'ancho' => 88,
            'texto' => "",
            'borde' => 'B',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 40,
            'texto' => "2) A la orden de",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 50,
            'ancho' => 150,
            'texto' => "",
            'borde' => 'B',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 40,
            'texto' => "3) A la orden de",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 50,
            'ancho' => 150,
            'texto' => "",
            'borde' => 'B',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 40,
            'texto' => "4) A la orden de",
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 50,
            'ancho' => 150,
            'texto' => "",
            'borde' => 'B',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        else:
        $n = 1;
        $TEXTO = "";
        foreach($orden['MutualProductoSolicitudInstruccionPago'] as $instruccion):
        $TEXTO .= "$n)" . $instruccion['a_la_orden_de'] . ", en concepto de " . $instruccion['concepto'] . " por un importe de PESOS $ " . number_format($instruccion['importe'],2) . ".-\n";
        $n++;
        endforeach;
        $this->MultiCell(0,11,$TEXTO);
        endif;
        $TEXTO = "Sin mas, saludo a Uds. muy atentamente.\n";
        $this->MultiCell(0,11,$TEXTO);
        
        $this->ln(20);
        
        $this->firmaSocio();
        
        $this->ln(4);
//         $this->barCode($orden['MutualProductoSolicitud']['barcode']);
        
//         $this->ln(20);
        
//         $this->linea[1] = array(
//             'posx' => 10,
//             'ancho' => 190,
//             'texto' => "",
//             'borde' => 'T',
//             'align' => 'L',
//             'fondo' => 0,
//             'style' => 'B',
//             'colorf' => '#D8DBD4',
//             'size' => $size
//         );
//         $this->Imprimir_linea();
//         $this->ln(4);
    }
    
    
    function imprimirPlanComercial($orden) {
        $size = 10;
        $sized = 13;
        $this->ln(4);
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("Datos del Plan Comercial"),
            'borde' => 'B',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(4);
        
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 55,
            'texto' => utf8_decode("EMPRESA PROVEEDORA:"),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $sized
        );
        $this->linea[2] = array(
            'posx' => 65,
            'ancho' => 90,
            'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_full_name']),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $sized
        );
        $this->linea[3] = array(
            'posx' => 155,
            'ancho' => 15,
            'texto' => utf8_decode("CUIT:"),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $sized
        );
        $this->linea[4] = array(
            'posx' => 170,
            'ancho' => 30,
            'texto' => utf8_decode($orden['MutualProductoSolicitud']['proveedor_cuit']),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $sized
        );
        $this->Imprimir_linea();
        
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 55,
            'texto' => utf8_decode("Monto Total Autorizado: $"),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 65,
            'ancho' => 22,
            'texto' => number_format($orden['MutualProductoSolicitud']['importe_total'],2),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[3] = array(
            'posx' => 87,
            'ancho' => 43,
            'texto' => utf8_decode("Cantidad de Cuotas:"),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[4] = array(
            'posx' => 130,
            'ancho' => 7,
            'texto' => utf8_decode($orden['MutualProductoSolicitud']['cuotas_print']),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[5] = array(
            'posx' => 137,
            'ancho' => 40,
            'texto' => utf8_decode("Monto de Cuota: $"),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[6] = array(
            'posx' => 177,
            'ancho' => 23,
            'texto' => number_format($orden['MutualProductoSolicitud']['importe_cuota'],2),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
    }
    
    
    function imprimeAutoDebitoTarjeta($orden) {
        $this->AddPage();
        $this->reset();
        $this->ln(4);
        $size = 14;
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => "AUTORIZACION COBRO POR TARJETA DE DEBITO RECURRENTE",
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(4);
        $size = 11;
        $this->SetFontSizeConf($size);
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("Córdoba, ". $orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero'] . " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']. " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero']),
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        $TEXTO = "Autorizo a ".Configure::read('APLICACION.nombre_fantasia')." por cuenta y orden de ".utf8_decode($orden['MutualProductoSolicitud']['proveedor_full_name'])." y/o quien éste designe, a descontar los saldos adeudados por mí, por cualquier concepto, debitando de mi caja de ahorro/cuenta corriente vinculada a la tarjeta de débito declarada en esta solicitud, o cualquier otra que poseyere e informara en el futuro. Asimismo me comprometo a informar los datos de mi nueva Tarjeta de Débito en caso de reposición por pérdida, robo, vencimiento o cualquier motivo que causara el cambio de la misma. La presente autorización es permanente e irrevocable, mientras subsista el crédito referenciado en el punto precedente, eventualmente solo se podría revocar con la conformidad expresa por parte de " . utf8_decode($orden['MutualProductoSolicitud']['proveedor_full_name']) . "------------";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO);
        
//         debug($orden);
        if(isset($orden['MutualProductoSolicitud']['beneficio_tarjeta_numero']) && !empty($orden['MutualProductoSolicitud']['beneficio_tarjeta_numero'])){
            $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => utf8_decode("Tarjeta de Débito: " . $orden['MutualProductoSolicitud']['beneficio_tarjeta_numero'] . " | Titular: " . $orden['MutualProductoSolicitud']['beneficio_tarjeta_titular']),
                'borde' => '',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->Imprimir_linea();
        } 
        
        $this->ln(20);
        $this->firmaSocio();
        
    }
    
    function imprimeAutoDebitoTarjetaModeloII($orden){
        $this->AddPage();
        $this->reset();
        $this->ln(4);
        $size = 13;
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("NOTIFICACION DE CBU / TARJETA DE DEBITO  / TARJETA DE CRÉDITO"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("AUTORIZACIÓN PARA CUMPLIMIENTO DE OBLIGACIONES DE PAGO"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(4);
        $size = 11;
        $this->SetFontSizeConf($size);

        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("Córdoba, ". $orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero'] . " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string']. " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero']),
            'borde' => '',
            'align' => 'R',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(2);
        
        //cargo los datos de la tarjeta
        $cardBank = '_________________________';
        $cardNumber = '_____________________';
        $cardExpiration = '____/____';
        $cardCSV = '____________';
        
        if(!empty($orden['MutualProductoSolicitud']['beneficio_tarjeta_debito'])){
            App::import('Vendor','crypt');
            $oCRYPT = new Crypt();
            $tarjeta = unserialize($oCRYPT->decrypt($orden['MutualProductoSolicitud']['beneficio_tarjeta_debito']));
//             debug($tarjeta);
        }
        
        $TEXTO = "Por la presente pongo en conocimiento de ".Configure::read('APLICACION.nombre_fantasia')." la actualización de datos de cuenta bancaria ";
        $TEXTO .= "conforme se detalla a continuación: CBU: ".utf8_decode($orden['MutualProductoSolicitud']['beneficio_cbu']).", solicitando mi adhesión al SNP o cualquier ";
        $TEXTO .= "otro que lo reemplace o corresponda mediante el débito directo por transferencia automática y de acuerdo a las normas";
        $TEXTO .= "que fije el BCRA, y/o la incorporación de los pagos en el resumen de tarjeta correspondiente,  para el pago de el/los cargo/s ";
        $TEXTO .= "que emita ".utf8_decode($orden['MutualProductoSolicitud']['proveedor_full_name']).", CUIT Nº ".utf8_decode($orden['MutualProductoSolicitud']['proveedor_cuit'])." sobre la tarjeta de débito / crédito (tachar lo que no corresponda), ";
        $TEXTO .= "del Banco $cardBank, Nº $cardNumber, a mi nombre personal, fecha de vencimiento $cardExpiration, ";
        $TEXTO .= "código de verificación $cardCSV y autorizando a ".Configure::read('APLICACION.nombre_fantasia')." y Pagos Online y/o  Card Cred S.R.L ";
        $TEXTO .= "y/o quienes éstos designen a debitar todas las cuotas del préstamo que fueran necesarias para el pago en término como asimismo ";
        $TEXTO .= "que por cualquier motivo se encuentren en mora, incluyendo gastos de cobranza y/o las cuotas y/o de los servicios pactados.";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO);
        $this->ln(20);
        $this->firmaSocio();
//         debug($orden);
        
    }
    
    function imprimeTablaCalculo($orden) {
        
        if(!empty($orden['MutualProductoSolicitud']['detalle_calculo_plan'])){
            $objetoCalculado = json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']);
//             debug($objetoCalculado);
            $size = 8;
            $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => utf8_decode("INFORMACION DE TASAS Y CONDICIONES DE LA OPERACION"),
                'borde' => 'LTR',
                'align' => 'C',
                'fondo' => 0,
                'style' => 'B',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->Imprimir_linea();
            $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 190,
                'texto' => utf8_decode("IMPORTE TOTAL DEL PRÉSTAMO: ") . number_format($objetoCalculado->liquidacion->capitalSolicitado,2),
                'borde' => 'LR',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->Imprimir_linea();
            $tea = (isset($objetoCalculado->tea) ? number_format($objetoCalculado->tea,2) : "______%");
            $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 95,
                'texto' => utf8_decode("T.E.A.: ") . $tea,
                'borde' => 'L',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->linea[2] = array(
                'posx' => 105,
                'ancho' => 95,
                'texto' => utf8_decode("Gastos de Otorgamiento: ") . number_format($objetoCalculado->liquidacion->gastoAdminstrativo->importe,2),
                'borde' => 'R',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->Imprimir_linea();
            $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 95,
                'texto' => utf8_decode("T.N.A.: ") . number_format($objetoCalculado->tna,2),
                'borde' => 'L',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->linea[2] = array(
                'posx' => 105,
                'ancho' => 95,
                'texto' => utf8_decode("Sistema de Amortización: ") . $objetoCalculado->metodoCalculoFormula,
                'borde' => 'R',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->Imprimir_linea();
            $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 95,
                'texto' => utf8_decode("T.E.M.: ") . number_format($objetoCalculado->tem,2),
                'borde' => 'L',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->linea[2] = array(
                'posx' => 105,
                'ancho' => 95,
                'texto' => utf8_decode("Cantidad de Cuotas: ") . $objetoCalculado->cuotas,
                'borde' => 'R',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->Imprimir_linea();
            $this->linea[1] = array(
                'posx' => 10,
                'ancho' => 95,
                'texto' => utf8_decode("C.F.T.: ") . number_format($objetoCalculado->cuotaPromedio->cft,2),
                'borde' => 'LB',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->linea[2] = array(
                'posx' => 105,
                'ancho' => 95,
                'texto' => utf8_decode("Periodicidad de pago de cuotas: Mensual"),
                'borde' => 'RB',
                'align' => 'L',
                'fondo' => 0,
                'style' => '',
                'colorf' => '#D8DBD4',
                'size' => $size
            );
            $this->Imprimir_linea();
        
        }
        
        
        
    }
    
    
    function hojaEnBlanco($header = false, $pie = false) {
        $this->HEADER = $header;
        $this->PIE = $pie;
        $this->AddPage();
        $this->reset();
    }
    
    
    function imprimeCobroDigital($logo, $orden, $imprimeCBU = TRUE) {
        $this->HEADER = $header;
        $this->PIE = $pie;
        $this->AddPage();
        $this->reset();
        $this->image($logo,150,13,50);
        $this->reset();
        $this->ln(20);
        $size = 12;
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("Autorización de DÉBITO en cuenta bancaria bajo la denominación COBRO DIGITAL"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        
        $this->ln(10);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $TEXTO = "Por la presente, autorizo a realizar el débito de mi cuenta bancaria CBU ".($imprimeCBU ? utf8_decode($orden['MutualProductoSolicitud']['beneficio_cbu']) : '______________________')." de los siguientes servicios/productos:";
        $TEXTO .= "\n";
        $TEXTO .= "__________________________________________________________________";
        $TEXTO .= "\n";
        $TEXTO .= "__________________________________________________________________";
        $TEXTO .= "\n";
        $TEXTO .= "__________________________________________________________________";
        $TEXTO .= "\n";
        $TEXTO .= "__________________________________________________________________";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO);
        
        $this->ln(5);
        
        $TEXTO = "Entendiendo que el destinatario de los fondos es:";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Razón Social:";
        $TEXTO .= "\n";
        $TEXTO .= "CUIT:";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO);
        
        $this->ln(10);
        
        $TEXTO = "Por consiguiente dejo expresada mi conformidad con los débitos a realizarse por la empresa Cobro Digital SRL. en mi cuenta bancaria indicada ut supra.";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO);
        
        $this->ln(20);
        
        $space = 10;
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 70,
            'texto' => "Firma",
            'borde' => 'T',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        $this->ln($space);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 70,
            'texto' => utf8_decode("Aclaración"),
            'borde' => 'T',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        $this->ln($space);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 70,
            'texto' => utf8_decode("CUIL/CUIT"),
            'borde' => 'T',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
        $this->ln($space);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 70,
            'texto' => utf8_decode("E-mail"),
            'borde' => 'T',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        
    }
    
    
    function ocom_imprime_solicitud_amtec($orden) {
        
        // debug($orden);
        // exit;
        
        $this->AddPage();
        $this->reset();
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',20);
        $size = 20;
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("AMTEC"),
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea(); 
        $this->ln(8);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',8);
        $TEXTO = "Asociación Mutual de Trabajadores Estatales de Córdoba";
        $TEXTO .= "\n";
        $TEXTO .= "Colon N° 352- Of. 13- P.B.- Galería Condor";
        $TEXTO .= "\n";
        $TEXTO .= "Cordoba (5000)- Tel: 0351- 4230580";
        $TEXTO .= "\n";
        $TEXTO .= "Matrícula I.N.A.M. N° 827";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'L');        
        
        $this->ln(5);
        $size = 12;
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("SOLICITUD DE INSCRIPCION COMO SOCIO"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();         
        
        $this->ln(5);
        
        $TEXTO = "DATOS PERSONALES";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Apellido y Nombre: " . $orden['MutualProductoSolicitud']['beneficiario_apenom'];
        $TEXTO .= "\n";
        $TEXTO .= "Tipo y Nro de Documento: ". $orden['MutualProductoSolicitud']['beneficiario_tdocndoc'];
        $TEXTO .= "\n";
        $TEXTO .= "Domicilio: ". $orden['MutualProductoSolicitud']['beneficiario_calle'] . " " . $orden['MutualProductoSolicitud']['beneficiario_numero_calle'];
        $TEXTO .= "\n";
        $TEXTO .= "Localidad: ". $orden['MutualProductoSolicitud']['beneficiario_localidad'] ." Provincia: " . $orden['MutualProductoSolicitud']['beneficiario_provincia'] . " CP: " . $orden['MutualProductoSolicitud']['beneficiario_cp'];
        $TEXTO .= "\n";
        $TEXTO .= "Apellido y Nombre de Cónyuge: _________________________________________________";
        $TEXTO .= "\n";
        $TEXTO .= "Núcleo Familiar: ____________________ Personas Económicamente a su cargo:______";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "TRABAJADOR ESTATAL";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Ministerio: _____________________________________________ Código: _____________";
        $TEXTO .= "\n";
        $TEXTO .= "Repartición: ____________________________________________ Código: _____________";
        $TEXTO .= "\n";
        $TEXTO .= "Presta Servicios en: __________________________________________________________";
        $TEXTO .= "\n";
        $TEXTO .= "Cargo: __________________________________________________ Código: _____________";
        $TEXTO .= "\n";
        $TEXTO .= "Ingresos Mensuales: _______________________ Código Lugar de Pago: _____________";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Por la presente tengo el agrado de dirigirme al Señor Presidente a efecto de solicitarle contemple la solicitud de inscripción como socio, como así también quiero dejar expresa constancia que:";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "      a) Conozco los derechos y obligaciones como asociado de la Mutual y los alcances del estudio social y de los Reglamentos internos.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "      b) Autorizo al Consejo Directivo de la ASOCIACION MUTUAL DE TRABAJADORES ESTATALES DE CORDOBA, a descontar de mis haberes, jubilación, pensión o retiro, el importe correspondiente a la cuota social establecida y el valor que corresponda a los servicios por mis suscriptos.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Córdoba, ". $orden['MutualProductoSolicitud']['fecha_emision_str']['dia']['numero']. " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['mes']['string'] . " de " . $orden['MutualProductoSolicitud']['fecha_emision_str']['anio']['numero']. ".-";
        $TEXTO .= "\n";
        
        $this->MultiCell(0,11,$TEXTO,0,'J');
        
        $this->ln(20);
        $this->firmaSocio();
        

    }
    
    function ocom_imprime_auto_insredsa($orden) {

       $this->AddPage();
        $this->reset();
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',20);
        $size = 12;
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("CARTA DE AUTORIZACION DEBITO DIRECTO INS RED SA CUIT 3071687630"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();
        $this->ln(3);
        $TEXTO = "En mi carácter de  titular de la cuenta  indicada,  solicito a INS RED SA (CUIT 3071687630) mi adhesión al Sistema Débito Directo, conforme a lo reglamentado por la Comunicación A 2559 y modificatorias del B.C.R.A., las que declaro aceptar y conocer.-.";
        $TEXTO .= "\n";
        $TEXTO .= "En consecuencia, autorizo a INS RED SA a realizar los débitos de la cuenta indicada, por los importes indicados y en las fechas informadas por la Empresa de sus servicios otorgados, comprometiéndome a mantener saldo suficiente en la cuenta de mi titularidad y a fin de que los débitos puedan ser formalmente efectuados en cada vencimiento.";
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "TITULAR DE LA CUENTA";
        $TEXTO .= "\n";
        $TEXTO .= "D.N.I.: " . $orden['MutualProductoSolicitud']['beneficiario_ndoc'];
        $TEXTO .= "\n";
        $TEXTO .= "C.U.I.T.: " . $orden['MutualProductoSolicitud']['beneficiario_cuit_cuil'];
        $TEXTO .= "\n";
        $TEXTO .= "Banco: " . $orden['MutualProductoSolicitud']['beneficio_banco'];
        $TEXTO .= "\n";
        $TEXTO .= "Sucursal: " . $orden['MutualProductoSolicitud']['beneficio_sucursal'];
        $TEXTO .= "\n";
        $TEXTO .= "Nro. Caja de Ahorros: " . $orden['MutualProductoSolicitud']['beneficio_cuenta'];
        $TEXTO .= "\n";
        $TEXTO .= "C.B.U.: " . $orden['MutualProductoSolicitud']['beneficio_cbu'];
        $TEXTO .= "\n";
        $TEXTO .= "\n";
        $TEXTO .= "Presto conformidad, en caso del rechazo del débito, a que la Empresa pueda efectuar los reintentos necesarios, con costo a mi cargo, para el cumplimiento de mis compromisos asumidos. ";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        $this->ln(15);
        $this->firmaSocio();        
    }
    
    function imprime_anexo_ddjj_cjpc($orden, $logo) {
        $this->AddPage();
        $this->reset();
        $this->image($logo,10,10,190);
        $this->reset();
        $this->ln(40);
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        
        $this->MultiCell(0,11,"Declaración Jurada de destino de ayudas económicas/préstamos provenientes de terceras entidades de acuerdo con el Art. 45 Inc. “b” - Ley 8024",1,'C');
        $this->ln(5);
        
        $TEXTO = "Esta declaración jurada deberá ser completada; firmada y enviada a través del servicio web Caja de Jubilaciones, opción Mis consultas > Nueva consulta a Títulos; mutuales y Terceras Entidades sólo por el beneficiario que quiera ampliar su tope para descuentos voluntarios al 50% de sus haberes.";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        
        $size = 10;
        $this->ln(5);

        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 45,
                        'texto' => "NOMBRE Y APELLIDO:",
                        'borde' => 'LTB',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );

        $apenom = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']);

        $this->linea[2] = array(
                        'posx' => 55,
                        'ancho' => 145,
                        'texto' => $apenom,
                        'borde' => 'TBR',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->Imprimir_linea();
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);

        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 30,
                        'texto' => utf8_decode("DOCUMENTO:"),
                        'borde' => 'LB',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->linea[2] = array(
                        'posx' => 40,
                        'ancho' => 60,
                        'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_ndoc']),
                        'borde' => 'BR',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => 'B',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->linea[3] = array(
                        'posx' => 100,
                        'ancho' => 30,
                        'texto' => utf8_decode("TELEFONO:"),
                        'borde' => 'B',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->linea[4] = array(
                        'posx' => 130,
                        'ancho' => 70,
                        'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_telefono_movil']),
                        'borde' => 'BR',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->Imprimir_linea();        
        
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        
        
        $this->linea[1] = array(
                        'posx' => 10,
                        'ancho' => 25,
                        'texto' => utf8_decode("DOMICILIO:"),
                        'borde' => 'LB',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );
        $this->linea[2] = array(
                        'posx' => 35,
                        'ancho' => 165,
                        'texto' => utf8_decode($orden['MutualProductoSolicitud']['beneficiario_domicilio']),
                        'borde' => 'BR',
                        'align' => 'L',
                        'fondo' => 0,
                        'style' => '',
                        'colorf' => '#D8DBD4',
                        'size' => $size
        );

        $this->Imprimir_linea();        
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        
        $this->ln(5);
        
        
        $TEXTO = "Declaro bajo juramento que el importe de el/los créditos provistos por terceras entidades (Mutuales; Cooperativas; Sindicatos, etc.) estarán destinados a la compra de alimentos, medicamentos y/o vestimenta, en un todo de acuerdo con lo dispuesto por el Art. 45 Inc. “b” - Ley 8024 y sus modificatorias; por ello en uso de las atribuciones que el mismo me confiere:";
        $TEXTO .= "\n";
        $TEXTO .= "SOLICITO a la Caja de Jubilaciones, Pensiones y Retiros de Córdoba, descontar hasta el 50% de mis haberes para transferir a las entidades titulares de los préstamos, créditos o ayudas económicas que me hayan brindado.";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');
        
        $this->ln(5);
        
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',10);
        $TEXTO = "NOTIFICACION: La duración del nuevo Tope de Descuento del 50% tendrá vigencia de un (1) año, sin protesto. Resolución Serie “F“ N° 2024 /013 Articulo: 7 Inc. “b“";
        $TEXTO .= "\n";
        $this->MultiCell(0,11,$TEXTO,0,'J');        

        
        $this->ln(35);
        $this->firmaSocio();
        
    }
    
     function imprimeContratoMutuoCBASERVICIOS($orden) {
        $this->AddPage();
        $this->reset();
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',20);
        $size = 11;
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 140,
            'texto' => utf8_decode("CONTRATO DE MUTUO"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size + 2
        );
        $this->linea[2] = array(
            'posx' => 150,
            'ancho' => 50,
            'texto' => utf8_decode("Préstamo Nro: ") . $orden['MutualProductoSolicitud']['nro_print'],
            'borde' => '',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );        
        $this->Imprimir_linea();
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',20);
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 140,
            'texto' => "",
            'borde' => 'B',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->linea[2] = array(
            'posx' => 150,
            'ancho' => 50,
            'texto' => utf8_decode("Cliente Nro:"),
            'borde' => 'B',
            'align' => 'L',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );        
        $this->Imprimir_linea();        
        
        $this->ln(3);
        $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        $apenom = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']);
        $dni = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_ndoc']);
        $domicilioDeudor = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_domicilio']);
        $domicilioDeudor = ($domicilioDeudor == ' ' ? '________________________' : $domicilioDeudor);
        
$TEXTO = <<<EOD
Condiciones Generales: La presente operación se realizará de acuerdo a las siguientes condiciones.

Primera: El mutuo documentado en esta solicitud se integra con el pagaré a la vista sin protesto Art. 50 Decreto Ley 5965/63 que el Sr./Sra. $apenom, DNI $dni, con domicilio en $domicilioDeudor, suscribe a favor de _______________ por el total del préstamo solicitado con más sus intereses. Las partes convienen que dicho pagaré podrá ser ejecutado judicialmente, sin perjuicio de las acciones emergentes del presente contrato de mutuo, en el caso de producirse el incumplimiento de las condiciones a que se sujeta el otorgamiento del préstamo.

Segunda: Asimismo, las partes convienen que NO será necesaria interpelación previa judicial o extrajudicial para la constitución de la Mora del deudor, es decir que la Mora se producirá en forma automática de pleno derecho, ante la falta de pago de cualesquiera de las cuotas, lo que implicará la caducidad de todos los plazos, considerándose en tal caso la deuda como íntegramente vencida, quedando facultado _______________ a reclamar la totalidad del crédito con más los intereses punitorios y gastos correspondientes.

Tercera: En caso de incurrir en mora, el deudor se obliga a pagar, además del saldo adeudado, un interés adicional en carácter de punitorio, equivalente al 50% del interés compensatorio, mientras dure la mora y hasta la cancelación total de la deuda.

Cuarta: Para el supuesto que ________________________ no pudiese debitar las CUOTAS de la Caja de Ahorro y/o Cuenta Corriente a nombre del deudor, éste se compromete a cancelar las mismas del 1 al 10 de cada mes en el domicilio de _______________ o cuenta bancaria que éste le indique al efecto.

Quinta: Si el deudor optara por realizar la Cancelación Total en forma Anticipada del crédito otorgado mediante la presente, deberá abonar además del saldo del capital adeudado, un recargo de hasta el 30% (treinta por ciento), de dicho saldo de capital, en concepto de compensación.

Sexta: El deudor toma a su cargo el pago de todos los impuestos presentes o futuros, costos, costas, comisiones, tasas de cualquier naturaleza que existan o fuesen creadas en el futuro por el Gobierno Nacional, Provincial o Municipal.

Séptima: Todas y cada una de las cuotas se calcularán bajo el régimen de amortización Francés. El deudor abonará sobre el capital adeudado una Tasa de interés fija del ____% Nominal Anual (T.N.A.), Tasa Efectiva Mensual ____% (T.E.M.), Tasa Efectiva Anual ____% (T.E.A.), Costo Financiero Total ____% (C.F.T.).

Octava: El deudor declara bajo juramento que _______________ le ha informado previamente, que en cumplimiento de la Ley de Habeas Data y reglamentarias, los datos personales y patrimoniales relacionados con la operación crediticia que contrata por el presente podrán ser inmediatamente informados y registrados en la base de datos de las organizaciones de información crediticia, públicas y/o privadas.

Novena: El deudor cuando lo considere necesario podrá exigir a $apenom el detalle de cuotas canceladas y a cancelar.

Décima: Mediante el presente el deudor presta expresa conformidad a que _______________ ceda y/o transfiera el presente crédito y/o los derechos emanados del mismo, sin necesidad que dicha cesión y/o transferencia le sea notificada, ello, de conformidad con lo establecido por los Art. 70 y 72 de la Ley 24.441.

Undécima: El deudor declara bajo juramento que el destino de los fondos otorgados por _______________ será: Consumo---------------------------------------.
EOD;

    // Utilizar MultiCell para imprimir el texto con interlineado sencillo
    $this->MultiCell(0, 6, $TEXTO, 0, 'J');  
    
        $this->ln(35);
        $this->firmaSocio();    
        
     }
     
    
function imprimeCancelacionStop($orden) {
    
    $this->AddPage();
    $this->reset();
    $size = 20;
    $this->linea[1] = array(
        'posx' => 10,
        'ancho' => 190,
        'texto' => utf8_decode("Cancelación de Stop Debit"),
        'borde' => '',
        'align' => 'C',
        'fondo' => 0,
        'style' => 'B',
        'colorf' => '#D8DBD4',
        'size' => $size
    );
    $this->Imprimir_linea();   
    $this->ln(3);
    $this->SetFont(PDF_FONT_NAME_MAIN,'',10); 

    $apenom = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']);
    $dni = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_tdocndoc']);
    $domicilioDeudor = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_domicilio']);
    $domicilioDeudor = ($domicilioDeudor == ' ' ? '________________________' : $domicilioDeudor); 
    
    $telefonoDeudor = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_telefono_movil']);
    $telefonoDeudor = ($telefonoDeudor == '' ? '________________________' : $telefonoDeudor); 
    
    $emailDeudor = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_e_mail']);
    $emailDeudor = ($emailDeudor == '' ? '________________________' : $emailDeudor);    
    
    $bancoNombre = utf8_decode($orden['MutualProductoSolicitud']['beneficio_banco']);
    $bancoCuenta = utf8_decode($orden['MutualProductoSolicitud']['beneficio_sucursal']) . " - " . utf8_decode($orden['MutualProductoSolicitud']['beneficio_cuenta']);
    $bancoCBU = utf8_decode($orden['MutualProductoSolicitud']['beneficio_cbu']);

$TEXTO = <<<EOD
Apellido y nombre: $apenom       
Tipo y numero de documento: $dni        
Domicilio: $domicilioDeudor        
Numero de teléfono : $telefonoDeudor        
Dirección de correo electrónico : $emailDeudor

Dato de la cuenta adherida a debito automático:

Banco : $bancoNombre
Numero : $bancoCuenta
CBU : $bancoCBU

En mi carácter de titular/es de la cuenta CBU numero $bancoCBU solicito/amos, al banco de la provincia de Córdoba dejar sin efecto el Stop Debit vigente, solicitado de las obligaciones/facturaciones, de las siguientes empresas de cobranza.

    1. Merida
    2. Mutual 22 de Septiembre
    3. Mutual Celesol
    4. Asoc Mutual 22
    5. Socore

Autorizo en forma expresa a debitar de mi cuenta corriente/caja de ahorros detallada en la presente los montos que correspondan según el plan pactado en forma mensual y consecutiva a partir de la fecha de aprobación de la operación solicitada.

En un todo de acuerdo con los datos consignados.

EOD;

    $this->MultiCell(0, 6, $TEXTO, 0, 'J');
        $this->ln(25);
        $this->firmaSocio();      
}
    

    function imprime_mutuo_cjpc($orden, $logo) {
        
        // debug($orden);
        
        $this->AddPage();
        $this->reset();
        $this->image($logo,10,10,190);
        $this->reset();
        $this->ln(40);

        $size = 14;
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode("Mutuo Acuerdo de Operaciones de Descuento"),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => 'B',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();   
        $this->ln(3);

        $size = 12;
        
        $entidad = 'CODIGO: '. $orden['MutualProductoSolicitud']['beneficio_codigo_cjpc'] . ' - ENTIDAD: ' . Configure::read('APLICACION.nombre_fantasia');
        
        $this->linea[1] = array(
            'posx' => 10,
            'ancho' => 190,
            'texto' => utf8_decode($entidad),
            'borde' => '',
            'align' => 'C',
            'fondo' => 0,
            'style' => '',
            'colorf' => '#D8DBD4',
            'size' => $size
        );
        $this->Imprimir_linea();         
        
        // $this->SetFont(PDF_FONT_NAME_MAIN,'',10);
        
        $this->ln(3);
        
        $apenom = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_apenom']);
        $dni = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_tdocndoc']);        
        $cuil = utf8_decode($orden['MutualProductoSolicitud']['beneficiario_cuit_cuil']);
        
        $tipo = ($orden['MutualProductoSolicitud']['beneficio_tipo_beneficio'] == 1 ? 'JUBILACION' : 'PENSION');
        $ley = $orden['MutualProductoSolicitud']['beneficio_nro_ley'];
        $nroBeneficio = $orden['MutualProductoSolicitud']['beneficio_nro_beneficio'];
        $subBeneficio = $orden['MutualProductoSolicitud']['beneficio_sub_beneficio'];
        $beneficio = $orden['MutualProductoSolicitud']['beneficio_cjpc_nro'];
        $fechaOtorga = date('d/m/Y', strtotime($orden['MutualProductoSolicitud']['fecha']));
        $nroOpe = $orden['MutualProductoSolicitud']['id'] . '1';
        $impoCuota = number_format($orden['MutualProductoSolicitud']['importe_cuota'], 2);
        $impoTotal = number_format($orden['MutualProductoSolicitud']['importe_total'], 2);
        $cuotas = $orden['MutualProductoSolicitud']['cuotas'];
        
$TEXTO = <<<EOD
Tipo: $tipo Ley: $ley Nro: $nroBeneficio Sub: $subBeneficio [Beneficio: $beneficio]
Apellido y nombre: $apenom   
Documento: $dni  CUIL: $cuil       

Tipo de Descuento: RETENCIONES VARIAS  Tipo Retención: PRESTAMO
Tipo Valor: IMPORTE  Fecha de Otorgamiento: $fechaOtorga
        
OP: $nroOpe Cuotas: $cuotas Importe:$ $impoCuota Total:$ $impoTotal

EOD;

        $this->MultiCell(0, 6, $TEXTO, 0, 'J');
        $this->ln(25);
        $this->firmaSocio();          
        
        
    }
    
}
<?php

require_once(dirname(__FILE__).'/mutual_producto_solicitud_pdf.php');


/**
 * ListadoPDF
 * @author adrian
 *
 */

class SolicitudCreditoGeneralPDF  extends MutualProductoSolicitudPDF{
	


	/**
	 * Construcctor
	 * @param $orientation
	 * @param $unit
	 * @param $format
	 * @param $unicode
	 * @param $encoding
	 * @param $diskcache
	 * @return unknown_type
	 */
	function SolicitudCreditoGeneralPDF($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false){
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        $this->HEADER = false;
	}
	

	function imprimeCalculoAyudaEconomica($orden){
            $size = 10;
            $sized = 13;            
            $this->ln(4);
            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 190,
                            'texto' => "Producto Solicitado",
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
                            'ancho' => 45,
                            'texto' => utf8_decode("Capital Solicitado $"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size
            );
            $this->linea[2] = array(
                            'posx' => 55,
                            'ancho' => 155,
                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_solicitado'],2, ',', '.') . " (Son Pesos " . utf8_decode($orden['MutualProductoSolicitud']['total_importe_solicitado_letras']) . ")",
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size
            );

            $this->Imprimir_linea();

            $TASAS = "TNA:" . number_format($orden['MutualProductoSolicitud']['tna'],2,',','.')."%";
            $TASAS .= " | TNM:" .number_format($orden['MutualProductoSolicitud']['tnm'],2,',','.')."%";
            if($orden['MutualProductoSolicitud']['sellado_porc'] != 0){
                $TASAS .= " | SELLADO:" .number_format($orden['MutualProductoSolicitud']['sellado_porc'],2,',','.')."%";
            }
            if($orden['MutualProductoSolicitud']['cft'] != 0){
                $TASAS .= " | CFT:" .number_format($orden['MutualProductoSolicitud']['cft'],2,',','.')."%";
            }

            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 190,
                            'texto' => $TASAS,
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size - 3,
            );

            $this->Imprimir_linea();

            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 50,
                            'texto' => "DEVENGAMIENTO: ",
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size,
            );
            $this->linea[2] = array(
                            'posx' => 60,
                            'ancho' => 20,
                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_solicitado'] * ($orden['MutualProductoSolicitud']['tna'] / 100),2,',','.'),
                            'borde' => '',
                            'align' => 'R',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size,
            );

            $this->Imprimir_linea();


            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 50,
                            'texto' => "GASTOS ADMINIST: ",
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size,
            );
            $this->linea[2] = array(
                            'posx' => 60,
                            'ancho' => 20,
                            'texto' => number_format($orden['MutualProductoSolicitud']['gasto_admin'] * $orden['MutualProductoSolicitud']['cuotas'],2,',','.'),
                            'borde' => '',
                            'align' => 'R',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size,
            );

            $this->Imprimir_linea();


            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 50,
                            'texto' => "SELLADO PROVINCIAL: ",
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size,
            );
            $this->linea[2] = array(
                            'posx' => 60,
                            'ancho' => 20,
                            'texto' => number_format($orden['MutualProductoSolicitud']['sellado'] * $orden['MutualProductoSolicitud']['cuotas'],2,',','.'),
                            'borde' => '',
                            'align' => 'R',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size,
            );

            $this->Imprimir_linea();


            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 50,
                            'texto' => utf8_decode("Neto a Cobrar $"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $size
            );
            $this->linea[2] = array(
                            'posx' => 60,
                            'ancho' => 20,
                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_percibido'],2,',','.'),
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
                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_cuota'],2,',','.'),
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
                            'ancho' => 45,
                            'texto' => utf8_decode("Total a Reintegrar $"),
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size
            );
            $this->linea[2] = array(
                            'posx' => 55,
                            'ancho' => 155,
                            'texto' => number_format($orden['MutualProductoSolicitud']['importe_total'],2,',','.') . " (Son Pesos " . utf8_decode($orden['MutualProductoSolicitud']['total_letras']) . ")",
                            'borde' => '',
                            'align' => 'L',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size
            );

            $this->Imprimir_linea();            
        }
	

	function imprimeLiquidacion($orden,$imprimeInstruccion=true){
            $size = 10;
            $sized = 13;               
            $this->ln(4);
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
            if($imprimeInstruccion){
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
                $this->Imprimir_linea();                
            }
            
        }
    

	
	function SetLinea($value){
		if(empty($this->INI_Y)){
			parent::SetY($value);
		}else{
			$y = $this->INI_Y + ($this->H * ($value - 1));
			parent::SetY($y);
		}
	}
        
        
        
        public function imprimePagare($orden,$util,$lugar='CORDOBA'){
            $size = 10;
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

            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 50,
                            'texto' => utf8_decode("N°"),
                            'borde' => 'TBLR',
                            'align' => 'L',
                            'fondo' => 1,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $size + 1
            );
            $this->Imprimir_linea();

            $this->linea[1] = array(
                            'posx' => 10,
                            'ancho' => 190,
                            'texto' => utf8_decode("$lugar, _____ de __________________ de ________"),
                            'borde' => '',
                            'align' => 'R',
                            'fondo' => 0,
                            'style' => '',
                            'colorf' => '#D8DBD4',
                            'size' => $size
            );
            $this->Imprimir_linea();
            $this->ln(5);
            $this->linea[1] = array(
                            'posx' => 150,
                            'ancho' => 50,
                            'texto' => "$ " . $util->nf($orden['MutualProductoSolicitud']['importe_total']),
                            'borde' => 'TBLR',
                            'align' => 'R',
                            'fondo' => 1,
                            'style' => 'B',
                            'colorf' => '#D8DBD4',
                            'size' => $size + 1
            );
            $this->Imprimir_linea();

            $TXT_PAGARE = "El ____ de _______________ de ______ PAGARE SIN PROTESTO (Art. 50 Decreto ";
            $TXT_PAGARE .= "Ley 5965/63) a _________________________________________ ";
            $TXT_PAGARE .= "o a su órden la cantidad de PESOS ".$orden['MutualProductoSolicitud']['total_letras']." por igual valor recibido ";
            $TXT_PAGARE .= "en efectivo a mi entera satisfacción, pagadero en _______________________________________.-\n";
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
            //$this->linea[1] = array(
            //		'posx' => 10,
            //		'ancho' => 90,
            //		'texto' => utf8_decode("Domicilio: " . $orden['MutualProductoSolicitud']['beneficiario_domicilio']),
            //		'borde' => '',
            //		'align' => 'L',
            //		'fondo' => 0,
            //		'style' => '',
            //		'colorf' => '#D8DBD4',
            //		'size' => $size
            //);
            //$this->Imprimir_linea();
            //$this->linea[1] = array(
            //		'posx' => 10,
            //		'ancho' => 90,
            //		'texto' => utf8_decode("Teléfonos: " . $orden['MutualProductoSolicitud']['beneficiario_telefonos']),
            //		'borde' => '',
            //		'align' => 'L',
            //		'fondo' => 0,
            //		'style' => '',
            //		'colorf' => '#D8DBD4',
            //		'size' => $size
            //);
            //$this->Imprimir_linea();

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
    
        

        
}
?>
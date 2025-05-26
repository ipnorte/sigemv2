<?php
require_once(dirname(__FILE__).'/xtcpdf.php');


/**
 * ListadoPDF
 * @author adrian
 *
 */

class FacturaAfip extends XTCPDF{
    var $responsable; 
    var $copias;
    var $razon_social;
    var $CdcnIva;
    var $CbteLetra;
    var $CbteTipo;
    var $CbteDscr;
    var $CbteNro;
    var $CbtePVta;
    var $FchEmi;
    var $IniAct;
    var $CuitEmi;
    var $IngBrutos;
    var $ImpTotal;
    var $CodBarra;
    var $cae;
    var $FchVto;
    
    var $ImpNeto;
    var $ImpIVA;
    

    /**
     * Construcctor
     * @param $orientation
     * @param $unit
     * @param $format
     * @param $unicode
     * @param $encoding
     * @param $diskcache
     * // @return unknown_type
     */
    function FacturaAfip($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false){
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        $this->setFooterMargin(5);
        $this->SetAutoPageBreak(true,69);
        $this->responsable = $_SESSION['NAME_USER_LOGON_SIGEM'];
        $this->lMargin = 5;
        $this->tMargin = 0;
        $this->topMargen = 6;

    }

/**
     * sobrecarga del metodo Header
     * @see app/vendors/tcpdf/TCPDF#Header()
     */
    function Header(){

        $linea_back = $this->linea;
        $this->linea = array();
                    // 210      10
        $ancho = $this->w - $this->lMargin - $this->rMargin;
        $medio = $this->w / 2;

        $this->Rect($this->lMargin, $this->topMargen, $this->w - $this->rMargin, $this->topMargen + 50);
        $this->Line($this->lMargin, $this->topMargen + 10, $this->w - $this->lMargin, $this->topMargen + 10);
        $this->Rect($medio-8, $this->topMargen + 10, 16, 12);
        $this->Line($medio, $this->topMargen + 22, $medio, $this->topMargen + 55);

        $this->y = $this->topMargen + 2;

        $this->linea[0] = array();
        $this->linea[0]['posx'] = $this->lMargin + 3;
        $this->linea[0]['ancho'] = $ancho-2;
        $this->linea[0]['texto'] = $this->copias;
        $this->linea[0]['size']  = $this->fontSizeTitulo1-.5;
        $this->linea[0]['borde'] = '';
        $this->linea[0]['align'] = 'C';
        $this->linea[0]['fondo'] = 1;
        $this->linea[0]['style'] = 'B';
//        $this->linea[0]['colorf'] = '#ccc';
        $this->Imprimir_linea();
        $this->Ln(0.5);

        $yBack = $this->y;
        
        
//        $this->SetY($yBack+1);
        $this->SetY(16.1);
        $this->linea[0] = array();
        $this->linea[0]['posx'] = $medio - 7;
        $this->linea[0]['ancho'] = 14;
        $this->linea[0]['texto'] = $this->CbteLetra;
        $this->linea[0]['size']  = $this->fontSizeTitulo1+8;
        $this->linea[0]['borde'] = '';
        $this->linea[0]['align'] = 'C';
        $this->linea[0]['fondo'] = 1;
        $this->linea[0]['style'] = 'B';
//        $this->linea[0]['colorf'] = '#ccc';
        $this->Imprimir_linea();

        
//        $this->SetY($yBack+9.5);
        $this->SetY(24.5);
        $this->linea[0] = array();
        $this->linea[0]['posx'] = $medio - 7;
        $this->linea[0]['ancho'] = 14;
        $this->linea[0]['texto'] = 'Cod.' . $this->CbteTipo;
        $this->linea[0]['size']  = $this->fontSizeTitulo1-8;
        $this->linea[0]['borde'] = '';
        $this->linea[0]['align'] = 'C';
        $this->linea[0]['fondo'] = 1;
        $this->linea[0]['style'] = 'B';
//        $this->linea[0]['colorf'] = '#ccc';
        $this->Imprimir_linea();

        $this->SetY($yBack + 3);

        if(!empty($this->logo)){

            $this->Image($this->logo,25,2,22,13);
//            $this->Ln(5);
//            $this->SetFont(PDF_FONT_NAME_MAIN,'B',7);
//            $this->Cell(0,5,$this->membrete['L1'],'L');
//            $this->Ln(3);
//            $this->SetFont(PDF_FONT_NAME_MAIN,'',5);
//            $this->Cell(0,5,$this->membrete['L2'],'L');
//            $this->Ln(3);
//            $this->SetFont(PDF_FONT_NAME_MAIN,'',5);
//            $this->Cell(0,5,$this->membrete['L3'],'L');
            $this->Ln(2); 

            $this->linea[0] = array(
                                    'posx' => $medio + 15,
                                    'ancho' => $medio - 10,
                                    'texto' => $this->CbteDscr,
                                    'borde' => '',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '',
                                    'size' => 15,
                                    'family' => 'helvetica'
            );

            $this->Imprimir_linea();
            $this->Ln(3);
        }else{
//            $this->Ln(); 
            $this->SetFont(PDF_FONT_NAME_MAIN,'B',15);
            $this->Cell(5);
            $this->MultiCell($medio - 15, 15, $this->razon_social, 0, 'C');
/*            
            $this->linea[0] = array(
                                    'posx' => 10,
                                    'ancho' => $medio - 15,
//                                    'texto' => $this->membrete['L1'],
                                    'texto' => $this->razon_social,
                                    'borde' => '',
                                    'align' => 'C',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '',
                                    'size' => 15,
                                    'family' => 'helvetica'
            );
*/
            $this->SetY($yBack + 5);
//            $this->Ln(); 
            
            $this->linea[0] = array(
                                    'posx' => $medio + 15,
                                    'ancho' => $medio - 10,
                                    'texto' => $this->CbteDscr,
                                    'borde' => '',
                                    'align' => 'L',
                                    'fondo' => 0,
                                    'style' => 'B',
                                    'colorf' => '',
                                    'size' => 15,
                                    'family' => 'helvetica'
            );

            $this->Imprimir_linea();
            $this->Ln(3);
            /*
            $this->Ln(4);
            $this->SetFont(PDF_FONT_NAME_MAIN,'B',15);
            $this->Cell(5);
            $this->MultiCell($medio - 15, 15, $this->membrete['L1'], 0, 'C');
//            $this->Ln(2);
//            $yBack = $this->y;
             * 
             */
        }
        $this->linea[0] = array(
                                'posx' => $medio + 5,
                                'ancho' => $ancho / 4,
                                'texto' => 'Punto Venta: ' . str_pad($this->CbtePVta,5,0, STR_PAD_LEFT),
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
        
        $this->linea[1] = array(
                                'posx' => ($medio / 2) * 3,
                                'ancho' => $ancho / 4,
                                'texto' => 'Comp. Nro.: ' . str_pad($this->CbteNro,8,0, STR_PAD_LEFT),
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
        $this->Imprimir_linea();
        $this->Ln(1);
        $yBack = $this->GetY();
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',10);
        $this->Cell(25);
        $this->MultiCell($medio - 30, 0, $this->razon_social, 0, 'C');

        $this->SetY($yBack);
        $this->linea[0] = array(
                                'posx' => 8,
                                'ancho' => $ancho / 2,
//                                'texto' => 'Razon Social: ' . $this->membrete['L1'],
                                'texto' => 'Razon Social: ',
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
// áíóúé        
        $this->linea[1] = array(
                                'posx' => $medio + 5,
                                'ancho' => $ancho / 2,
                                'texto' => 'Fecha de Emision: ' . date('d/m/Y',strtotime($this->FchEmi)),
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
        $this->Imprimir_linea();
        $this->Ln(2);
//        $this->Ln(1);

        
        $this->linea[0] = array(
                                'posx' => $medio + 5,
                                'ancho' => $ancho / 2,
                                'texto' => 'CUIT: ' . $this->CuitEmi,
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
        $this->Imprimir_linea();
        $this->Ln(1);
        
        
        $this->linea[0] = array(
                                'posx' => 8,
                                'ancho' => $ancho / 2,
                                'texto' => 'Domicilio: ' . $this->membrete['L2'],
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
        
        $this->linea[1] = array(
                                'posx' => $medio + 5,
                                'ancho' => $ancho / 2,
                                'texto' => 'Ingresos Brutos: ' . $this->IngBrutos,
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
        $this->Imprimir_linea();
        $this->Ln(1);

        
        $this->linea[0] = array(
                                'posx' => 8,
                                'ancho' => $ancho / 2,
                                'texto' => 'Condicion frente al IVA: ' . $this->CdcnIva,
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
        
        $this->linea[1] = array(
                                'posx' => $medio + 5,
                                'ancho' => $ancho / 2,
                                'texto' => 'Fecha de Inicio de Actividades: ' . date('d/m/Y',strtotime($this->IniAct)),
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );
        $this->Imprimir_linea();
        $this->Ln();
        
        
/*        
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',10);
        $this->Cell(5);
        $this->Cell(0,10,'Razón Social: ' . $this->membrete['L1']);
        $this->Ln(6);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->Cell(5);
        $this->Cell(0,10,'Domicilio' . $this->membrete['L2']);
        $this->Ln(6);
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',8);
        $this->Cell(5);
        $this->Cell(0,10,'Condición IVA: ' . $this->CdcnIva);
        $this->Ln(6);

        $this->y = 22;

        $yBack = $this->y;
        

        $this->Rect($medio-5, 17, 10, 10);
        $this->Line($medio, 35, $medio, 50);
        $this->Line($this->w - $this->rMargin, 45, $this->w - $this->rMargin, 60);
        
        $this->y = $this->topMargen;
        $this->linea[0] = array();
        $this->linea[0]['posx'] = 79;
        $this->linea[0]['ancho'] = $ancho;
        $this->linea[0]['texto'] = (isset($this->textoHeader) ? $this->textoHeader." " : date('d-m-Y')." ");
        $this->linea[0]['size']  = $this->fontSizeTitulo1;
        $this->linea[0]['borde'] = 'LTBR';
        $this->linea[0]['align'] = 'R';
        $this->linea[0]['fondo'] = 1;
        $this->linea[0]['style'] = 'B';
        $this->linea[0]['colorf'] = '#ccc';
        $this->Imprimir_linea();

        $this->linea[0] = array();
        $this->linea[0]['posx'] = 79;
        $this->linea[0]['ancho'] = $ancho;
        $this->linea[0]['texto'] = $this->titulo['titulo1']." ";
        $this->linea[0]['size']  = $this->fontSizeTitulo1;
        $this->linea[0]['borde'] = 'LR';
        $this->linea[0]['align'] = 'R';
        $this->Imprimir_linea();

        $this->linea[0] = array();
        $this->linea[0]['posx'] = 79;
        $this->linea[0]['ancho'] = $ancho;
        $this->linea[0]['texto'] = $this->titulo['titulo2']." ";
        $this->linea[0]['size']  = $this->fontSizeTitulo2;
        $this->linea[0]['borde'] = 'LR';
        $this->linea[0]['align'] = 'R';
        $this->Imprimir_linea();

        $this->linea[0] = array();
        $this->linea[0]['posx'] = 79;
        $this->linea[0]['ancho'] = $ancho;
        $this->linea[0]['texto'] = $this->titulo['titulo3'];
        $this->linea[0]['size']  = $this->fontSizeTitulo3;
        $this->linea[0]['borde'] = 'LR';
        $this->linea[0]['align'] = 'C';
        $this->linea[0]['style'] = 'B';
        $this->Imprimir_linea();

        $this->y = $yBack;
        $this->Cell(138,3,'', 'B');
        $this->Ln(4);

//    	//IMPRIMO LAS COLUMNAS
        if(is_array($this->encabezado)){

            $this->Ln(0.5);

            foreach($this->encabezado as $encabezado){

                $this->linea = $encabezado;
                $this->imprimir_linea();

            }

        }
*/

        $this->tMargin = $this->y;
        $this->linea = $linea_back;
        $this->Reset();

    }	
	
    function Footer(){
        // 92
        $this->SetY(-69);
        $yBack = $this->GetY();
        $this->Rect($this->lMargin, $this->y, $this->w - $this->rMargin, 30);
        $this->SetY($yBack+2);
        
                    // 210      10
        $ancho = ($this->w - $this->lMargin - $this->rMargin) / 8;
        $medio = $this->w / 2;
        
        $this->linea[0] = array(
                                'posx' => $medio,
                                'ancho' => ($ancho * 3) - 2,
                                'texto' => 'Subtotal: $',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );

        $this->linea[1] = array(
                                'posx' => $medio + $ancho * 3,
                                'ancho' => $ancho,
                                'texto' => $this->ImpNeto,
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );

        $this->Imprimir_linea();
        $this->Ln(0.5);
                
        
        $this->linea[0] = array(
                                'posx' => $medio,
                                'ancho' => ($ancho * 3) - 2,
                                'texto' => 'I.V.A.: $',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );

        $this->linea[1] = array(
                                'posx' => $medio + $ancho * 3,
                                'ancho' => $ancho,
                                'texto' => $this->ImpIVA,
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );

        $this->Imprimir_linea();
        $this->Ln(0.5);
                
        
        $this->linea[0] = array(
                                'posx' => $medio,
                                'ancho' => ($ancho * 3) - 2,
                                'texto' => 'Subtotal c/ I.V.A.: $',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );

        $this->linea[1] = array(
                                'posx' => $medio + $ancho * 3,
                                'ancho' => $ancho,
                                'texto' => $this->ImpTotal,
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );

        $this->Imprimir_linea();
        $this->Ln(0.5);
                
        
        $this->linea[0] = array(
                                'posx' => $medio,
                                'ancho' => ($ancho * 3) - 2,
                                'texto' => 'Importe Otros Tributos: $',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 10,
                                'family' => 'helvetica'
        );

        $this->linea[1] = array(
                                'posx' => $medio + $ancho * 3,
                                'ancho' => $ancho,
                                'texto' => '0,00',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );

        $this->Imprimir_linea();
        $this->Ln(2);
                
        
        $this->linea[0] = array(
                                'posx' => $medio,
                                'ancho' => ($ancho * 3) - 2,
                                'texto' => 'Importe Total: $',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 11,
                                'family' => 'helvetica'
        );

        $this->linea[1] = array(
                                'posx' => $medio + $ancho * 3,
                                'ancho' => $ancho,
                                'texto' => $this->ImpTotal,
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 1,
                                'style' => 'B',
                                'colorf' => '#ccc',
                                'size' => 11,
                                'family' => 'helvetica'
        );

        $this->Imprimir_linea();
        $this->Ln(3);
                
        
        $this->linea[0] = array(
                                'posx' => $medio,
                                'ancho' => ($ancho * 3) - 8,
                                'texto' => 'CAE Nro.:',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 11,
                                'family' => 'helvetica'
        );

        $this->linea[1] = array(
                                'posx' => $medio + $ancho * 3 - 7,
                                'ancho' => $ancho,
                                'texto' => $this->cae,
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '',
                                'size' => 11,
                                'family' => 'helvetica'
        );

        $this->Imprimir_linea();
                
        
        $this->linea[0] = array(
                                'posx' => $medio,
                                'ancho' => ($ancho * 3) - 8,
                                'texto' => 'Fecha de Vto. de CAE:',
                                'borde' => '',
                                'align' => 'R',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 11,
                                'family' => 'helvetica'
        );

        $this->linea[1] = array(
                                'posx' => $medio + $ancho * 3 - 7,
                                'ancho' => $ancho,
                                'texto' => date('d/m/Y', strtotime($this->FchVto)),
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => '',
                                'colorf' => '',
                                'size' => 11,
                                'family' => 'helvetica'
        );
        $this->Imprimir_linea();

        
        $this->linea[0] = array(
                                'posx' => 8,
                                'ancho' => ($ancho * 4),
                                'texto' => 'Esta Administracion Federal no se responzabiliza por los datos ingresados en el detalle de la operacion',
                                'borde' => '',
                                'align' => 'L',
                                'fondo' => 0,
                                'style' => 'I',
                                'colorf' => '',
                                'size' => 6,
                                'family' => 'helvetica'
        );
        $this->Imprimir_linea();

        
        ##########################################################################################################################
        # CODIGO DE BARRA CUERPO MUTUAL
        ##########################################################################################################################

        $this->barCode($this->CodBarra, 'I25', 8, 0, 80, 15, 0.2);
        $this->Ln(7);
        
        $this->linea[0] = array(
                                'posx' => 8,
                                'ancho' => 80,
                                'texto' => $this->CodBarra,
                                'borde' => '',
                                'align' => 'C',
                                'fondo' => 0,
                                'style' => 'B',
                                'colorf' => '',
                                'size' => 9,
                                'family' => 'helvetica'
        );
        $this->Imprimir_linea();
        /*
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('courier','',6);
        $this->Cell(0,3,'RESPONSABLE: ' . $this->responsable,'',1,'L');
        $this->Cell(0,3,'PAGINA '.$this->PageNo().'/{nb}','T',1,'R');
        $this->Cell(0,3,'IMPRESO EL '.date('d-m-Y H:m:s'),'',1,'L');
         * 
         */
        $this->Reset();
    }
    
    
}
?>
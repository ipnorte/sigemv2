<?php
require_once(dirname(__FILE__).'/xtcpdf.php');


/**
 * ListadoPDF
 * @author adrian
 *
 */

class OrdenPagoA5  extends XTCPDF{
	var $responsable; 

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
	function OrdenPagoA5($orientation='P', $unit='mm', $format='A5', $unicode=true, $encoding='UTF-8', $diskcache=false){
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
		$this->setFooterMargin(5);
		$this->SetAutoPageBreak(true,10);
		if($orientation == 'L'):
		endif;	
		$this->responsable = $_SESSION['NAME_USER_LOGON_SIGEM'];
		$this->lMargin = 5;
                $this->tMargin = 0;
			
	}
	
/**
	 * sobrecarga del metodo Header
	 * @see app/vendors/tcpdf/TCPDF#Header()
	 */
	function Header(){
			
		$linea_back = $this->linea;
		$this->linea = array();
		
		$ancho = $this->w - $this->rMargin - 74;
		
		$this->y = $this->topMargen;
		
    	if(!empty($this->logo)){
    		
    		$this->Image($this->logo,25,2,22,13);
			$this->Ln(5);
    		$this->SetFont(PDF_FONT_NAME_MAIN,'B',7);
	    	$this->Cell(0,5,$this->membrete['L1'],0);
    		$this->Ln(3);
    		$this->SetFont(PDF_FONT_NAME_MAIN,'',5);
    		$this->Cell(0,5,$this->membrete['L2'],0);
    		$this->Ln(3);
    		$this->SetFont(PDF_FONT_NAME_MAIN,'',5);
    		$this->Cell(0,5,$this->membrete['L3'],0);
    		$this->Ln(4); 
    		
    	}else{
    		
			$this->SetFont(PDF_FONT_NAME_MAIN,'B',10);
	    	$this->Cell(0,5,$this->membrete['L1'],0);
    		$this->Ln(5);
    		$this->SetFont(PDF_FONT_NAME_MAIN,'',6);
    		$this->Cell(0,5,$this->membrete['L2'],0);
    		$this->Ln(3);
    		$this->SetFont(PDF_FONT_NAME_MAIN,'',6);
    		$this->Cell(0,5,$this->membrete['L3'],0);
    		$this->Ln(4);
	    	
    	}
    	
		$this->y = 22;
		
    	$yBack = $this->y;

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

    	$this->tMargin = $this->y;
    	
//    	//IMPRIMO LAS COLUMNAS
    	if(is_array($this->encabezado)){
    		
    		$this->Ln(0.5);

    		foreach($this->encabezado as $encabezado){
    			
    			$this->linea = $encabezado;
    			$this->imprimir_linea();
    			
    		}
    		
    	}
    	
    	$this->Ln(3);
    	
    	$this->tMargin = $this->y;
    	$this->linea = $linea_back;
    	
    	$this->Reset();
    	
	}	
	
    function Footer(){
        $this->SetY(-12);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('courier','',6);
        $this->Cell(0,3,'RESPONSABLE: ' . $this->responsable,'',1,'L');
        $this->Cell(0,3,'PAGINA '.$this->PageNo().'/{nb}','T',1,'R');
        $this->Cell(0,3,'IMPRESO EL '.date('d-m-Y H:m:s'),'',1,'L');
        $this->Reset();
    }
    
    
}
?>
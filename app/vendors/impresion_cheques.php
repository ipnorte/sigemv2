<?php
require_once('pdf_js.php');

class ImpresionCheques extends PDF_JavaScript
{
	
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

    function ImpresionCheques($orientation='P', $unit='cm', $format='A5', $unicode=true, $encoding='UTF-8', $diskcache=false){
	parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
	$this->setFooterMargin(0);
	$this->SetAutoPageBreak(true,0);
	if($orientation == 'L'):
	endif;	
	$this->responsable = $_SESSION['NAME_USER_LOGON_SIGEM'];
	$this->tMargin = -2;
	$this->lMargin = 0;
			
    }
	

    /**
    * sobrecarga del metodo Header
    * @see app/vendors/tcpdf/TCPDF#Header()
    */

    function Header(){

    }	
	
    function Footer(){
    }
	
    function AutoPrint($dialog=false)
    {
        //Open the print dialog or start printing immediately on the standard printer
        $param=($dialog ? 'true' : 'false');
        $script="print($param);";
        $this->IncludeJS($script);
    }

	
    function AutoPrintToPrinter($server, $printer, $dialog=false)
    {
        //Print on a shared printer (requires at least Acrobat 6)
        $script = "var pp = getPrintParams();";
        if($dialog)
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
        else
            $script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
        $script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
        $script .= "print(pp);";
        $this->IncludeJS($script);
    }

}

// Ejemplo
// $pdf=new PDF_AutoPrint();
// $pdf->AddPage();
// $pdf->SetFont('Arial','',20);
// $pdf->Text(90, 50, 'Print me!');
// //Open the print dialog
// $pdf->AutoPrint(true);
// $pdf->Output();

?>
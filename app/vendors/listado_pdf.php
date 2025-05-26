<?php

require_once(dirname(__FILE__).'/xtcpdf.php');


/**
 * ListadoPDF
 * @author adrian
 *
 */

class ListadoPDF  extends XTCPDF{

	var $PIEUser = false;
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
	function ListadoPDF($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false){
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
		$this->setFooterMargin(10);
		$this->SetAutoPageBreak(true,15);
		if($orientation == 'L'):
		endif;		
		$this->responsable = $_SESSION['NAME_USER_LOGON_SIGEM'];
	}
	
	
    function Footer(){
    	if(!$this->PIEUser):
    		parent::Footer();
    	else:
	        $this->SetY(-12);
	        $this->SetTextColor(0, 0, 0);
	        $this->SetFont('courier','',6);
	        $this->Cell(0,3,'Impreso por: ' . $this->responsable,'T',0,'L');
	        $this->Cell(0,3,'PAGINA '.$this->PageNo().'/{nb}','T',1,'R');
	        $this->Reset();
    	endif;
    }
    
    
	}
?>
<?php 
App::import('Vendor','tcpdf/tcpdf');

class FOLIOPDF extends TCPDF{
	
	var $lineheight; //! int
	var $nroLibro = 0;
	var $hojaActual = 1;
	var $fillNroLibro = 3;
	var $fillNroHoja = 5;
	var $showHeader = true;	
    var $nombreLibro = "";
	
	function FOLIOPDF($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false){
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
		$this->AliasNbPages();
		$this->SetFontSizeConf(11);
		$this->SetCreator(Configure::read('APLICACION.nombre') . " " . Configure::read('APLICACION.version'));
		$this->SetAuthor("Cordoba Soft IT | www.cordobasoft.com | @2009 - ".date("Y")." | All Right Reserved");
		$this->topMargen = $this->tMargin;
		$this->setFooterMargin(20);
		$this->SetAutoPageBreak(true,1);
//        $this->nroLibro = str_pad($this->nroLibro,$this->fillNroLibro,"0",STR_PAD_LEFT);
//        debug(intval($this->nroLibro));

//        debug($this);
//        exit;
        
	}

	function SetFontSizeConf($size){
		$this->SetFontSize($size);
		$this->actualFontSize = $size;
  		$this->lineheight = 5 * $this->actualFontSize / 11;         // Related to FontSizePt == 11
	}

	function Header(){
		if($this->showHeader):
			$nroLibro = str_pad($this->nroLibro,$this->fillNroLibro,"0",STR_PAD_LEFT);
			$nroHoja = str_pad($this->hojaActual,$this->fillNroHoja,"0",STR_PAD_LEFT);
			$this->Ln(5);
			$this->SetFont(PDF_FONT_NAME_MAIN,'B',10);
			$this->Cell(0,5,Configure::read('APLICACION.nombre_fantasia'),0);
			$this->Ln(4);
			$this->SetFont(PDF_FONT_NAME_MAIN,'',8);
			$this->Cell(0,5,Configure::read('APLICACION.domi_fiscal'),0);
			$this->Ln(4);
			$this->Cell(0,5,"MATRICULA I.N.A.E.S NRO. ".Configure::read('APLICACION.matricula_inaes'),0);
			$this->Ln(4);
            $titulo = strtoupper($this->nombreLibro);
            if(intval($nroLibro) != 0){
                $titulo = $titulo . " Nro: " . $this->nroLibro;
            }            
            
			$this->SetFont(PDF_FONT_NAME_MAIN,'B',12);
			$this->Cell(0,0,$titulo,0,0,"C");
			$this->Ln(7);
			$this->Cell(0,0,"","T");
			$y = $this->y;
			$this->y = $this->topMargen;
			$this->SetFont(PDF_FONT_NAME_MAIN,'',10);
			$this->SetX(100);
			$this->Cell(0,5,"Hoja Nro: " . $nroHoja,0,0,"R");
			$this->y = $y;
			$this->Ln(3);
			$this->tMargin = $this->y;
			
		else:
		
			$this->SetTopMargin(26);
			$this->SetY(26);
			$this->SetFont(PDF_FONT_NAME_MAIN,'B',5);
			//imprimo el encabezado de las columnas
			$columnas = array();
			$this->Cell(20,4,"DOCUMENTO","LTB",0,"C");
			$this->Cell(50,4,"APELLIDO Y NOMBRE","TB",0,"C");
			$this->Cell(85,4,"DIRECCION","TB",0,"C");
			$this->Cell(20,4,"CATEGORIA","TB",0,"C");
			$this->Cell(15,4,"ALTA","RTB",0,"C");
			$this->SetY(30);
			$this->SetTopMargin(30);			
				
		endif;
	}
	
	function Footer(){
		
	}
	
	
	/**
	 * imprime una linea
	 * @return unknown_type
	 */
	function imprimir_linea(){
		
			$pos    = $this->GetX();
			$ancho  = 0;
			$alto   = 5;
			$texto  = '';
			$borde  = 0;
			$aling  = 'L';
			$fondo  = 0;
			$link   = '';

			$family = '';
			$style  = '';
			$size   = 11;

			$colorb = 255;
			$colorf = 255;
			$colort = 0;
			$Maxlineheight = 0;

//			$this->Reset();
			
			foreach($this->linea as $linea){
				
				$pos    = array_key_exists('posx', $linea)   ? $linea['posx']   : $pos;
				$ancho  = array_key_exists('ancho', $linea)  ? $linea['ancho']  : $this->GetStringWidth($linea['texto']);
				$texto  = utf8_encode($linea['texto']);
				$borde  = array_key_exists('borde', $linea)  ? $linea['borde']  : 0;
				$align  = array_key_exists('align', $linea)  ? $linea['align']  : 'L';
				$fondo  = array_key_exists('fondo', $linea)  ? $linea['fondo']  : 0;
				$link   = array_key_exists('link', $linea)   ? $linea['link']   : '';
				$family = array_key_exists('family', $linea) ? $linea['family'] : '';
				$style  = array_key_exists('style', $linea)  ? $linea['style']  : '';
				$size   = array_key_exists('size', $linea)   ? $linea['size']   : $this->actualFontSize;
				
				$colorb = array_key_exists('colorb', $linea) ? $this->ConvertColor($linea['colorb']) : $this->ConvertColor('black');
				$colorf = array_key_exists('colorf', $linea) ? $this->ConvertColor($linea['colorf']) : $this->ConvertColor('white');
				$colort = array_key_exists('colort', $linea) ? $this->ConvertColor($linea['colort']) : $this->ConvertColor('black');
				
				$this->lineheight = 5 * $size / 11;

				if($this->lineheight > $Maxlineheight) $Maxlineheight = $this->lineheight;
	    		
				$this->SetFillColor($colorf['R'],$colorf['G'],$colorf['B']);
    			$this->SetTextColor($colort['R'],$colort['G'],$colort['B']);
	    		$this->SetDrawColor($colorb['R'],$colorb['G'],$colorb['B']);
   				
	    		$this->SetFont($family,$style,$size);
		 		
   				$this->SetX($pos);
    			
   				$this->Cell($ancho, $this->lineheight, $texto, $borde, 0, $align, $fondo, $link);
	 			
    			$pos = $this->GetX();

			}
			
   			$this->Ln($Maxlineheight);
	   		$this->Reset();
	}

	/**
	 * Restauro valores por defecto
	 * @return unknown_type
	 */
	function Reset(){
		$colort = $this->ConvertColor('black');
		$colorb = $this->ConvertColor('black');
		$colorf = $this->ConvertColor('white');
		$this->SetFont('courier','',$this->actualFontSize); // Changeable?(not yet...)
  		$this->lineheight = 5 * $this->actualFontSize / 11;         // Related to FontSizePt == 11
    	$this->SetFillColor($colorf['R'],$colorf['G'],$colorf['B']);
    	$this->SetTextColor($colort['R'],$colort['G'],$colort['B']);
    	$this->SetDrawColor($colorb['R'],$colorb['G'],$colorb['B']);
		$this->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0));    	
  		$this->linea  = array();
	}	
	
	function ConvertColor($color="#000000"){
	
	  //W3C approved color array (disabled)
	  //static $common_colors = array('black'=>'#000000','silver'=>'#C0C0C0','gray'=>'#808080', 'white'=>'#FFFFFF','maroon'=>'#800000','red'=>'#FF0000','purple'=>'#800080','fuchsia'=>'#FF00FF','green'=>'#008000','lime'=>'#00FF00','olive'=>'#808000','yellow'=>'#FFFF00','navy'=>'#000080', 'blue'=>'#0000FF','teal'=>'#008080','aqua'=>'#00FFFF');
	  //All color names array
	  static $common_colors = array('antiquewhite'=>'#FAEBD7','aquamarine'=>'#7FFFD4','beige'=>'#F5F5DC','black'=>'#000000','blue'=>'#0000FF','brown'=>'#A52A2A','cadetblue'=>'#5F9EA0','chocolate'=>'#D2691E','cornflowerblue'=>'#6495ED','crimson'=>'#DC143C','darkblue'=>'#00008B','darkgoldenrod'=>'#B8860B','darkgreen'=>'#006400','darkmagenta'=>'#8B008B','darkorange'=>'#FF8C00','darkred'=>'#8B0000','darkseagreen'=>'#8FBC8F','darkslategray'=>'#2F4F4F','darkviolet'=>'#9400D3','deepskyblue'=>'#00BFFF','dodgerblue'=>'#1E90FF','firebrick'=>'#B22222','forestgreen'=>'#228B22','gainsboro'=>'#DCDCDC','gold'=>'#FFD700','gray'=>'#808080','green'=>'#008000','greenyellow'=>'#ADFF2F','hotpink'=>'#FF69B4','indigo'=>'#4B0082','khaki'=>'#F0E68C','lavenderblush'=>'#FFF0F5','lemonchiffon'=>'#FFFACD','lightcoral'=>'#F08080','lightgoldenrodyellow'=>'#FAFAD2','lightgreen'=>'#90EE90','lightsalmon'=>'#FFA07A','lightskyblue'=>'#87CEFA','lightslategray'=>'#778899','lightyellow'=>'#FFFFE0','limegreen'=>'#32CD32','magenta'=>'#FF00FF','mediumaquamarine'=>'#66CDAA','mediumorchid'=>'#BA55D3','mediumseagreen'=>'#3CB371','mediumspringgreen'=>'#00FA9A','mediumvioletred'=>'#C71585','mintcream'=>'#F5FFFA','moccasin'=>'#FFE4B5','navy'=>'#000080','olive'=>'#808000','orange'=>'#FFA500','orchid'=>'#DA70D6','palegreen'=>'#98FB98','palevioletred'=>'#D87093','peachpuff'=>'#FFDAB9','pink'=>'#FFC0CB','powderblue'=>'#B0E0E6','red'=>'#FF0000','royalblue'=>'#4169E1','salmon'=>'#FA8072','seagreen'=>'#2E8B57','sienna'=>'#A0522D','skyblue'=>'#87CEEB','slategray'=>'#708090','springgreen'=>'#00FF7F','tan'=>'#D2B48C','thistle'=>'#D8BFD8','turquoise'=>'#40E0D0','violetred'=>'#D02090','white'=>'#FFFFFF','yellow'=>'#FFFF00');
	  //http://www.w3schools.com/css/css_colornames.asp
	  if ( ($color{0} != '#') and ( strstr($color,'(') === false ) ) $color = $common_colors[strtolower($color)];
	
	  if ($color{0} == '#') //case of #nnnnnn or #nnn
	  {
	  	$cor = strtoupper($color);
	  	if (strlen($cor) == 4) // Turn #RGB into #RRGGBB
	  	{
		 	  $cor = "#" . $cor{1} . $cor{1} . $cor{2} . $cor{2} . $cor{3} . $cor{3};
		  }
		  $R = substr($cor, 1, 2);
		  $vermelho = hexdec($R);
		  $V = substr($cor, 3, 2);
		  $verde = hexdec($V);
		  $B = substr($cor, 5, 2);
		  $azul = hexdec($B);
		  $color = array();
		  $color['R']=$vermelho;
		  $color['G']=$verde;
		  $color['B']=$azul;
	  }
	  else //case of RGB(r,g,b)
	  {
	  	$color = str_replace("rgb(",'',$color); //remove Žrgb(Ž
	  	$color = str_replace("RGB(",'',$color); //remove ŽRGB(Ž -- PHP < 5 does not have str_ireplace
	  	$color = str_replace(")",'',$color); //remove Ž)Ž
	    $cores = explode(",", $color);
	    $color = array();
		  $color['R']=$cores[0];
		  $color['G']=$cores[1];
		  $color['B']=$cores[2];
	  }
	  if (empty($color)) return array('R'=>255,'G'=>255,'B'=>255);
	  else return $color; // array['R']['G']['B']
	}	
	
 
//	/**
//	 * (non-PHPdoc)
//	 * @see app/vendors/excel/PHPExcel/Shared/PDF/TCPDF#Output($name, $dest)
//	 */
//	function Output($file){
//		$file = str_replace(".","_",$file);
//		$file .= "_".intval(mt_rand()).".pdf";
//		parent::Output($file);
//	}	
	
	
}

?>
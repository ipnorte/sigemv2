<?php

require_once(dirname(__FILE__).'/comun.php');

class PagoFacil extends Comun{
	
	#ATRIBUTOS DE LA CLASE#
	public $nro_empresa='';
	public $nro_cedulon='';
	public $monto_1vto = 0;
	public $monto_2vto = 0;
	public $fecha_1vto = '';
	public $dias_2vto = 0;
	public $moneda = '0';

	private $filler = '0';

	public function PagoFacil($nEmp,$nCed,$m1vto,$f1vto,$m2vto=0,$d2vto=0){
		$this->nro_empresa = $this->getNroEmpresaToStr($nEmp);
		$this->nro_cedulon = $this->getNroCedulonToStr($nCed);
		$this->monto_1vto = $this->getMontoToStr($m1vto);
		if($m2vto==0)$this->monto_2vto = $this->getMontoToStr($m1vto);
		else $this->monto_2vto = $this->getMontoToStr($m2vto);
		$this->fecha_1vto = $f1vto;
		$this->dias_2vto = str_pad($d2vto,3,"0",STR_PAD_LEFT);
	}

	public function __destruct(){unset($this);}

	public function getCodigo(){
		$str_codigo = $this->nro_empresa;
		$str_codigo .= $this->monto_1vto;
		$str_codigo .= $this->get_fecha_juliana($this->fecha_1vto,true);
		$str_codigo .= $this->nro_cedulon;
		$str_codigo .= $this->moneda;
		$str_codigo .= $this->monto_2vto;
		$str_codigo .= $this->dias_2vto;
		$str_codigo .= $this->filler;
		$str_codigo .= $this->get_digito_verificador($str_codigo);
		return $str_codigo;
	}

	private function getNroEmpresaToStr($nro){
		return str_pad($nro,3,"0",STR_PAD_LEFT);
	}

	private function getNroCedulonToStr($cedulon){
		return str_pad($cedulon,10,"0",STR_PAD_LEFT);
	}

	private function getMontoToStr($monto){
		$strm = number_format($monto,2,'','');
		return str_pad($strm,8,"0",STR_PAD_LEFT);
	}

//	private function genFechaJuliana($fecha,$conanio=true){
//		/*La fecha juliana la calcula haciendo la diferencia entre los dias
//		 *julianos al 1º día del 1º mes del año de la fecha y la fecha de vto
//		 *al resultado le suma 1*/
//		$fecha = date("Y-m-d",strtotime($fecha));
//		$diaVto = date('d',strtotime($fecha));
//		$mesVto = date('m',strtotime($fecha));
//		$anioVto = date('y',strtotime($fecha));
//		$anioFullVto = date('Y',strtotime($fecha));
//		$DiasJ = gregoriantojd($mesVto,$diaVto,$anioFullVto) - gregoriantojd(1,1,$anioFullVto) + 1;
//
//		if($conanio)$juliana = $anioVto.$DiasJ;
//		else $juliana = $DiasJ;
//		return $juliana;
//	}
//
    
    
	protected function get_digito_verificador($num){
		$num = trim($num);
		$ln = strlen($num);
		$b = 1;
		$sum = 0;
		for($a=0;$a<$ln;$a++){
			$sum += substr($num,$a,1) * $b;
			$b += 2;
			if($b>9)$b=3;
		}
		$sum = intval($sum / 2);
		$sum = $sum % 10;
		return $sum;
	}	
}
?>
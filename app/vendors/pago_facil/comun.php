<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Comun{
    
    
    protected function set_numero_to_string($importe,$enteros=8,$decimales=0){
        $elementos = explode('.',number_format($importe, $decimales,'.',''));
        $cadena = str_pad($elementos[0],$enteros,"0",STR_PAD_LEFT);
        if(isset($elementos[1])) $cadena.= str_pad($elementos[1],$decimales,"0",STR_PAD_LEFT);
        return $cadena;
    }


    protected function get_fecha_juliana($fecha,$conanio=true,$filler=2){
		/*La fecha juliana la calcula haciendo la diferencia entre los dias
		 *julianos al 1º día del 1º mes del año de la fecha y la fecha de vto
		 *al resultado le suma 1*/
		$fecha = date("Y-m-d",strtotime($fecha));
		$diaVto = date('d',strtotime($fecha));
		$mesVto = date('m',strtotime($fecha));
		$anioVto = date('y',strtotime($fecha));
		$anioFullVto = date('Y',strtotime($fecha));
		$DiasJ = gregoriantojd($mesVto,$diaVto,$anioFullVto) - gregoriantojd(1,1,$anioFullVto) + 1;
        $DiasJ = str_pad($DiasJ,$filler,0,STR_PAD_LEFT);
		if($conanio)$juliana = $anioVto.$DiasJ;
		else $juliana = $DiasJ;
		return $juliana;
	}

	protected function get_digito_verificador($num = null){
	}    
    
    
}

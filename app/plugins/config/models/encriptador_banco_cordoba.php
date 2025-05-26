<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class EncriptadorBancoCordoba{
    
    /**
     * 
     * @param type $f3v1 cadena
     * @param type $f3v9 linea
     */
    public function desencripta($f3v1, $f3v9){
        $f3v8 = 0;
        $f3v5 = "";
        $f3v6 = array_chunk(str_split($f3v1),3,true);
        array_pop($f3v6);
        foreach($f3v6 as $grupo){
			$f3v8 += strval(implode($grupo));
		}
		$a1 = $this->a_uno((strlen($f3v1) / 3) - 1 + $f3v9);
		$a2 = $this->a_dos($f3v8);
		$f3v4 = str_pad(intval( $a1 + 57 + $a2),3,'0',STR_PAD_LEFT);
		$f3v10 = 0;
		if($f3v4 === substr($f3v1,-3)){
			foreach($f3v6 as $grupo){
				$f3v10 = $f3v10 + 1;
				$aux_1 = strval(implode($grupo));
				$aux_2 = $this->a_uno($f3v10 + $f3v9);
				$f3v7 = ($aux_1 - 255 - $aux_2) * (-1);
				if($f3v7 >= 0 && $f3v7 <= 255){
					$f3v5 .= chr($f3v7);
				}else{
					$f3v5 = $f3v1;
					break;
				}
			}
		}else{
			$f3v5 = $f3v1;
		}	
        return $f3v5;
        
    }


    /**
     * 
     * @param type $f1v1 cadena
     * @param type $f1v7 linea
     */
    public function encripta($f1v1, $f1v7){
        $f1v3 = "";
        $f1v4 = strlen($f1v1);
        $f1v9 = 0;
        $f1v5 = str_split($f1v1);
        $i = 1;
        foreach($f1v5 as $caracter){
			$f1v6 = 255 - ord($caracter);
			$f1v6 += $this->a_uno($i + $f1v7);
			$f1v9 += $f1v6;
			$f1v3 .= str_pad(intval($f1v6),3,'0',STR_PAD_LEFT);
			$i++;
		}
		$a1 = $this->a_uno($f1v4 + $f1v7);
		$a2 = $this->a_dos($f1v9);
		$f1v3 .= str_pad(($a1 + 57 + $a2), 3,'0',STR_PAD_LEFT);
        return $f1v3;
    }
    
    /*******************************
     * 
     ******************************/
    private function a_uno($f2v1){
        $f2v2 = trim($f2v1);
        while(strlen($f2v2) > 1){
            $f2v3 = 0;
            $f2v4 = str_split($f2v2);
            foreach($f2v4 as $caracter){
				$f2v3 += intval($caracter);
			}
            $f2v2 = $f2v3;
        }
        return intval($f2v2);
    }
    
    /**
     * 
     */ 
    private function a_dos($f4v1){
        $f4v2 = trim($f4v1);
        while(strlen($f4v2) > 2){
            $f4v3 = strval(substr($f4v2,-1));
            $f4v4 = strval(substr($f4v2, 0,(strlen($f4v2) - 1)));
            $f4v3 = $f4v3 + $f4v4;
            $f4v2 = trim($f4v3);
        }
        return intval($f4v2);
    }
    
    
}

?>

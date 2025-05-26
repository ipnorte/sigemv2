<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(__FILE__).'/comun.php');

class RapiPago extends Comun{
    
    /**
     * NUMERO DE EMPRESA ASIGNADO POR RAPIPAGO (3)
     * @var type 
     */
    private $codigo_empresa;
    /**
     * NUMERO DE IDENTIFICACION UNIVOCA INTERNA DEL CLIENTE (8)
     * @var type 
     */
    private $nro_cliente;
    /**
     * NUMERO DE IDENTIFICACION DEL COMPROBANTE / FACTURA DE PAGO (11)
     * @var type 
     */
    private $nro_comprobante;
    /**
     * IMPORTE DEL COMPROBANTE AL PRIMER VENCIMIENTO (6E + 2D)
     * @var type 
     */
    private $importe;
    /**
     * PRIMER VENCIMIENTO JULIANO (2 + 3) (AÑO + DIAS)
     * @var type 
     */
    private $vto_1;
    /**
     * RECARGO AL SEGUNDO VENCIMIENTO (4E + 2D)
     * @var type 
     */
    private $recargo;
    /**
     * CANTIDAD DE DIAS CORRIDOS ENTRE EL PRIMER Y SEGUNDO VENCIMIENTO
     * @var type 
     */
    private $vto_2;
    
    /**
     * SECUENCIA PARA CALCULAR DIGITO VERIFICADOR
     * @var type 
     */
    private $PONDERADOR = "1357935793579357935793579357935793579357935";
    
    /**
     * CADENA QUE REPRESENTA EL CODIGO DE BARRAS
     * @var type 
     */
    private $bar_code = "";
    
    /**
     * LONGITUD DEL CODIGO
     * @var type 
     */
    private $LONGITUD = 44;
    
    /**
     *  Constructor
     * @param type $codigo_empresa
     * @param type $nro_cliente
     * @param type $nro_comprobante
     * @param type $importe_1
     * @param type $vto_1
     * @param type $recargo
     * @param type $vto_2
     */
    public function __construct($nro_cliente = null,$nro_comprobante = null,$importe = null,$vto_1 = null,$recargo = null,$vto_2 = null){
        $this->bar_code = "";
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $this->set_codigo_empresa((isset($INI_FILE['intercambio']['rapipago_codigo_empresa']) ? intval($INI_FILE['intercambio']['rapipago_codigo_empresa']) : 0));
        $this->set_nro_cliente($nro_cliente);
        $this->set_nro_comprobante($nro_comprobante);
        $this->set_importe($importe);
        $this->set_vto_1($vto_1);
        $this->set_recargo($recargo);
        $this->set_vto_2($vto_2); 
        $this->bar_code .= $this->get_digito_verificador($this->bar_code);
    }
    
    public function get_bar_code(){
        return $this->bar_code;
    }

    public function set_codigo_empresa($codigo_empresa){
        $this->codigo_empresa = parent::set_numero_to_string($codigo_empresa, 3);
        $this->bar_code .= $this->codigo_empresa;
    }
    public function set_nro_cliente($nro_cliente){
        $this->nro_cliente = parent::set_numero_to_string($nro_cliente, 8);
        $this->bar_code .= $this->nro_cliente;
    }
    public function set_nro_comprobante($nro_comprobante){
        $this->nro_comprobante = parent::set_numero_to_string($nro_comprobante, 11);
        $this->bar_code .= $this->nro_comprobante;
    }
    public function set_importe($importe){
        $this->importe = parent::set_numero_to_string($importe, 6, 2);
        $this->bar_code .= $this->importe;
    }    
    public function set_vto_1($vto_1){
        $this->vto_1 = parent::get_fecha_juliana($vto_1,true,3);
        $this->bar_code .= $this->vto_1;
    }   
    public function set_recargo($recargo){
        $this->recargo = parent::set_numero_to_string($recargo,4,2);
        $this->bar_code .= $this->recargo;
    }     
    public function set_vto_2($vto_2){
        $this->vto_2 = parent::set_numero_to_string($vto_2, 2);
        $this->bar_code .= $this->vto_2;
    } 
    
    public function get_digito_verificador($cadena){
        if(strlen($cadena) < ($this->LONGITUD - 1)) return 0;
        $ponderador = str_split(strval($this->PONDERADOR));
        $bar_code_secuencia = str_split(strval($cadena));
        $suma = 0;
        foreach($bar_code_secuencia as $i => $numero){
            $suma += $numero * $ponderador[$i];
        }
        $digito = array_pop(str_split(strval(intval($suma / 2))));
        return intval($digito);        
    }
    
    public function validate_bar_code($secuencia){
        if(strlen(trim($secuencia)) == 0) return false;
        $digito_1 = intval(substr(trim($secuencia),-1));
        $digito_2 = $this->get_digito_verificador(substr(trim($secuencia),0,  strlen($secuencia) - 1));
        if($digito_1 === $digito_2) return true;
        else return false;
    }
    
    
}
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MetodoCalculoCuota{
    
    var $name = 'MetodoCalculoCuota';
    var $useTable = false;  

    var $solicitado;
    var $cuotas;
    var $tasa;
    var $TNA = 0;
    var $TEM = 0;
    var $TNM = 0;
    var $CFT = 0;
    var $CFTA = 0;
    var $porcAdic;
    var $porcIVA;
    var $porcSello;
    var $METODO_CALCULO = 1;
    var $error = 0;
    var $msgError = null;
    var $plan = array();
    var $cuota = array();
    var $cuota_promedio = array();
    var $METODOS = array(1 => 'FRANCES',2 => 'DIRECTO');
    var $CRITERIO_APLICACION = array(1 => '1 - SOLICITADO',2 => '2 - TOTAL A REINTEGRAR', 4 => '4 - SOLICITADO EN CUOTA');
    var $tipoCuotaGAdmin = null;
    var $tipoCuotaSellado = null;
    var $baseCalculoGadmin = 1;
    var $baseCalculoSellado = 2;
    var $liquidacion = array(
                        'capitalBaseCalculo' => 0,
                        'capitalSolicitado' => 0,
                        'netoPercibe' => 0,
                        'totalPrestamo' => 0,
                        'gastoAdmin' => 0,
                        'sellados' => 0,
                        'porcGastoAdmin' => 0,
                        'porcSellado' => 0,
                        'tipoCuotaGAdmin' => null,
                        'tipoCuotaSellado' => null,
                        'baseCalculoGadmin' => null,
                        'baseCalculoSellado' => null,
                        'tipoCuotaSelladoDesc' => null,
                        'tipoCuotaGAdminDesc' => null,
                        'TNA' => 0,
                        'TEM' => 0,
                        'CFT' => 0,
                        'CFTA' => 0,
                        'metodo' => 0,
                        'interesesDevengados' => 0,
                        'interesMoratorio' => 0,
                        'costoCancelacionAnticipada' => 0
                    );

    var $objetoCalculo = null;
    var $objetoOpciones = array();
    var $interesMoratorio = 0;
    var $costoCancelacionAnticipada = 0;

        
    public $opciones_capital = array();

    function __construct(){
        //parent::__construct();
        $this->reset();
    }
	
    function reset(){
            $this->plan = array();
            $this->cuota = $this->cuota_promedio = array(
                    'CUOTA' => 1,
                    'IMPORTE' => 0,
                    'CAPITAL' => 0,
                    // 'ADICIONAL' => 0,
                    // 'SELLADO' => 0,
                    'INTERES' => 0,
                    'IVA' => 0,
                    'SALDO' => 0,
                    'CFT' => 0,
                    'CFTA' => 0,
            );
            $this->error = 0;
            $this->msgError = null;	
            $this->liquidacion = array(
                    'capitalBaseCalculo' => 0,
                    'capitalSolicitado' => 0,
                    'netoPercibe' => 0,
                    'totalPrestamo' => 0,
                    'gastoAdmin' => 0,
                    'sellados' => 0,
                    'porcGastoAdmin' => 0,
                    'porcSellado' => 0,
                    'tipoCuotaGAdmin' => null,
                    'tipoCuotaGAdminDesc' => null,
                    'tipoCuotaSellado' => null,
                    'tipoCuotaSelladoDesc' => null,
                    'baseCalculoGadmin' => null,
                    'baseCalculoSellado' => null,
                    'TNA' => 0,
                    'TEM' => 0,
                    'TEA' => 0,
                    'CFT' => 0,
                    'CFTA' => 0,
                    'metodo' => 0,
                    'interesesDevengados' => 0,
                    'interesMoratorio' => 0,
                    'costoCancelacionAnticipada' => 0                        
            );
            $this->objetoCalculo = new stdClass();                	
    }
	
    function get_objetoCalculo($toJSON = true){
            return ($toJSON ? json_encode($this->objetoCalculo) : $this->objetoCalculo);
    }

    function get_objetoOpciones($toJSON = true){
            return ($toJSON ? json_encode($this->objetoOpciones) : $this->objetoOpciones);
    }        

    function set_objetoCalculo(){
            $objeto = new stdClass();
            $objeto->solicitado = floatval($this->solicitado);
            $objeto->cuotas = intval($this->cuotas);
            $objeto->tna = floatval($this->TNA);
            $objeto->tem = floatval($this->TEM);
            $objeto->tea = floatval($this->TEA);
            $objeto->ivaAlicuota = floatval($this->porcIVA);
            $objeto->metodoCalculo = intval($this->METODO_CALCULO);
            $objeto->metodoCalculoFormula = $this->METODOS[$objeto->metodoCalculo];
            $objeto->liquidacion = new stdClass();
            $objeto->liquidacion->capitalSolicitado = floatval($this->liquidacion['capitalSolicitado']);
            $objeto->liquidacion->capitalBaseCalculo = floatval($this->liquidacion['capitalBaseCalculo']);
            $objeto->liquidacion->netoPercibe = floatval($this->liquidacion['netoPercibe']);
            $objeto->liquidacion->totalPrestamo = floatval($this->liquidacion['totalPrestamo']);
            // $objeto->liquidacion->gastoAdmin = $this->liquidacion['gastoAdmin'];
            // $objeto->liquidacion->sellados = $this->liquidacion['sellados'];

            $objeto->liquidacion->gastoAdminstrativo = new stdClass();
            $objeto->liquidacion->gastoAdminstrativo->porcentaje = floatval($this->liquidacion['porcGastoAdmin']);
            $objeto->liquidacion->gastoAdminstrativo->importe = floatval($this->liquidacion['gastoAdmin']);
            $objeto->liquidacion->gastoAdminstrativo->tipoCuota = $this->liquidacion['tipoCuotaGAdmin'];
            $objeto->liquidacion->gastoAdminstrativo->descripcion = $this->liquidacion['tipoCuotaGAdminDesc'];
            $objeto->liquidacion->gastoAdminstrativo->baseCalculo = intval($this->liquidacion['baseCalculoGadmin']);
            $objeto->liquidacion->gastoAdminstrativo->baseCalculoCriterio = $this->CRITERIO_APLICACION[$this->liquidacion['baseCalculoGadmin']];

            $objeto->liquidacion->sellado = new stdClass();
            $objeto->liquidacion->sellado->porcentaje = floatval($this->liquidacion['porcSellado']);
            $objeto->liquidacion->sellado->importe = floatval($this->liquidacion['sellados']);
            $objeto->liquidacion->sellado->tipoCuota = $this->liquidacion['tipoCuotaSellado'];
            $objeto->liquidacion->sellado->descripcion = $this->liquidacion['tipoCuotaSelladoDesc'];
            $objeto->liquidacion->sellado->baseCalculo = intval($this->liquidacion['baseCalculoSellado']);
            $objeto->liquidacion->sellado->baseCalculoCriterio = $this->CRITERIO_APLICACION[$this->liquidacion['baseCalculoSellado']];


            // $objeto->liquidacion->porcGastoAdmin = $this->liquidacion['porcGastoAdmin'];

            // $objeto->liquidacion->porcSellado = $this->liquidacion['porcSellado'];
            // $objeto->liquidacion->tipoCuotaGAdmin = $this->liquidacion['tipoCuotaGAdmin'];
            // $objeto->liquidacion->tipoCuotaSellado = $this->liquidacion['tipoCuotaSellado'];
            // $objeto->liquidacion->tipoCuotaGAdminDesc = $this->liquidacion['tipoCuotaGAdminDesc'];
            // $objeto->liquidacion->tipoCuotaSelladoDesc = $this->liquidacion['tipoCuotaSelladoDesc'];                
            // $objeto->liquidacion->baseCalculoGadmin = $this->liquidacion['baseCalculoGadmin'];
            // $objeto->liquidacion->baseCalculoSellado = $this->liquidacion['baseCalculoSellado'];
            $objeto->liquidacion->interesesDevengados = floatval($this->liquidacion['interesesDevengados']);
            $objeto->liquidacion->interesMoratorio = floatval($this->liquidacion['interesMoratorio']);
            $objeto->liquidacion->costoCancelacionAnticipada = floatval($this->liquidacion['costoCancelacionAnticipada']);

            $objeto->cuotaPromedio = new stdClass();
            $objeto->cuotaPromedio->cuota = intval($this->cuota_promedio['CUOTA']);
            $objeto->cuotaPromedio->capital = floatval($this->cuota_promedio['CAPITAL']);
            $objeto->cuotaPromedio->interes = floatval($this->cuota_promedio['INTERES']);
            $objeto->cuotaPromedio->iva = floatval($this->cuota_promedio['IVA']);
            $objeto->cuotaPromedio->importe = floatval($this->cuota_promedio['IMPORTE']);
            $objeto->cuotaPromedio->cft = floatval($this->cuota_promedio['CFT']);
            $objeto->cuotaPromedio->cfta = floatval($this->cuota_promedio['CFTA']);
            $objeto->cuotaPromedio->saldo = floatval($this->cuota_promedio['SALDO']);                



            $objeto->detalleCuotas = array();

            foreach($this->plan as $value){
                    $cuota = new stdClass();
                    $cuota->periodo = null;
                    $cuota->vtoSocio = null;
                    $cuota->vtoProveedor = null;
                    $cuota->nroCuota = intval($value['CUOTA']);
                    $cuota->capital = round($value['CAPITAL'],2);
                    $cuota->interes = round($value['INTERES'],2);
                    $cuota->iva = round($value['IVA'],2);
                    $cuota->importe = round($value['IMPORTE'],2);
                    $cuota->cft = round($value['CFT'],2);
                    $cuota->cfta = round($value['CFTA'],2);
                    $cuota->saldo = round($value['SALDO'],2);
                    array_push($objeto->detalleCuotas,$cuota);
            }

            $objeto->periodoInicio = null;
            $objeto->primerVtoSocio = null;
            $objeto->primerVtoProveedor = null;


            $this->objetoCalculo = $objeto;
    }

	function set_valor_cuota($nro_cuota = 1,$saldo = null){
            
            if($this->solicitado == 0) {return;}
            if($this->tasa == 0) {return;}
            if($this->cuotas == 0) {return;}
            
		if($this->error) {return;}
		if(empty($this->solicitado)){
			$this->error = 1;
			$this->reset();
			$this->msgError = "EL IMPORTE SOLICITADO ES REQUERIDO";	
			return;		
		}
		if(empty($this->cuotas)){
			$this->error = 1;
			$this->reset();
			$this->msgError = "LA CANTIDAD DE CUOTAS ES REQUERIDO";			
			return;
		}
		$capital = $this->getCapitalBaseDeCalculo();
		/** METODO FRANCES */
                if($this->METODO_CALCULO == 1){
                    
                    $this->cuota['CUOTA'] = $nro_cuota;
                //     $capital = $this->solicitado * (1 + ($this->porcAdic / 100));
                    $this->cuota['SALDO'] = (!empty($saldo) ? $saldo : $capital);
                    if(empty($this->tasa)){
                            $this->cuota['IMPORTE'] = $this->cuota['SALDO'] / $this->cuotas;
                        //     $this->cuota['ADICIONAL'] = round($this->cuota['IMPORTE'] * ($this->porcAdic / 100),2);
                        //     $this->cuota['CAPITAL'] = round($this->cuota['IMPORTE'] - ($this->cuota['INTERES'] + $this->cuota['IVA'] + $this->cuota['ADICIONAL']),2);
                            $this->cuota['CAPITAL'] = round($this->cuota['IMPORTE'] - ($this->cuota['INTERES'] + $this->cuota['IVA']),2);
                            return;
                    }                    
                    $this->cuota['IMPORTE'] = round($this->calcularCuotaFrances($capital),2);
                    if($this->porcIVA != 0){
                            $this->cuota['INTERES'] = round(($this->cuota['SALDO'] * ($this->tasa / 100)) / ( 1 + ($this->porcIVA / 100)),2);
                            $this->cuota['IVA'] = round($this->cuota['INTERES'] * ($this->porcIVA / 100),2);
                    }else{
                            $this->cuota['INTERES'] = round($this->cuota['SALDO'] * ($this->tasa / 100),2);
                    }

                //     $this->cuota['ADICIONAL'] = round(($this->solicitado * ($this->porcAdic / 100)) / $this->cuotas,2);
                //     $this->cuota['SELLADO'] = round(($this->solicitado * ($this->porcSello / 100)) / $this->cuotas,2);
                //     $this->cuota['CAPITAL'] = round($this->cuota['IMPORTE'] - ($this->cuota['INTERES'] + $this->cuota['IVA'] + $this->cuota['ADICIONAL'] + $this->cuota['SELLADO']),2);
                //     $this->cuota['SALDO'] = round($this->cuota['SALDO'] - ($this->cuota['CAPITAL'] + $this->cuota['ADICIONAL'] + $this->cuota['SELLADO']),2);

                    $this->cuota['CAPITAL'] = round($this->cuota['IMPORTE'] - ($this->cuota['INTERES'] + $this->cuota['IVA']),2);
                    $this->cuota['SALDO'] = round($this->cuota['SALDO'] - ($this->cuota['CAPITAL']),2);
                    

                }else{
                    /** INTERES DIRECTO */
                    $this->cuota['CUOTA'] = $nro_cuota;
                //     $capital = $this->solicitado;
                    $this->cuota['SALDO'] = (!empty($saldo) ? $saldo : $capital);
                //     $this->cuota['ADICIONAL'] = round(($this->solicitado * ($this->porcAdic / 100)) / $this->cuotas,2);
                //     $this->cuota['SELLADO'] = round(($this->solicitado * ($this->porcSello / 100)) / $this->cuotas,2);
                    
                    if(empty($this->tasa)){
                            $this->cuota['IMPORTE'] = $this->cuota['SALDO'] / $this->cuotas;
                        //     $this->cuota['ADICIONAL'] = round($this->cuota['IMPORTE'] * ($this->porcAdic / 100),2);
                        //     $this->cuota['CAPITAL'] = round($this->cuota['IMPORTE'] - ($this->cuota['INTERES'] + $this->cuota['IVA'] + $this->cuota['ADICIONAL']),2);
                            $this->cuota['CAPITAL'] = round($this->cuota['IMPORTE'] - ($this->cuota['INTERES'] + $this->cuota['IVA']),2);
                            return;
                    }                    
                    
                    if($this->porcIVA != 0){
                            $this->cuota['INTERES'] = round(($capital * ($this->tasa / 100)) / ( 1 + ($this->porcIVA / 100)),2);
                            $this->cuota['IVA'] = round($this->cuota['INTERES'] * ($this->porcIVA / 100),2);
                    }else{
                            $this->cuota['INTERES'] = round($capital * ($this->tasa / 100),2);
                    }                    
                    $this->cuota['IMPORTE'] = round($this->calcularCuotaDirecta($capital),2);
                //     $this->cuota['IMPORTE'] += $this->cuota['ADICIONAL'] + $this->cuota['SELLADO'];

                //     $this->cuota['CAPITAL'] = round($this->cuota['IMPORTE'] - ($this->cuota['INTERES'] + $this->cuota['IVA'] + $this->cuota['ADICIONAL'] + $this->cuota['SELLADO']),2);
                    $this->cuota['CAPITAL'] = round($this->cuota['IMPORTE'] - ($this->cuota['INTERES'] + $this->cuota['IVA']),2);
                    $this->cuota['SALDO'] = round($this->cuota['SALDO'] - $this->cuota['CAPITAL'],2);
                    
                }
                
                $this->cuota['CFT'] = round(((($this->cuota['IMPORTE'] * $this->cuotas) - $this->solicitado) / $this->solicitado) * 100,2);
                // FORMULA CFT - CR. MARTIN POSSE 30/11/2021
                // (((1 + TEM) ^ n) - 1) x 100
                $this->cuota['CFT'] = round((pow((1 + ($this->TEM / 100)), $this->cuotas) - 1) * 100,2);
                
                
        		$this->cuota['CFTA'] = ($this->cuotas != 12 ? round(($this->cuota['CFT'] / $this->cuotas) * 12,2) : $this->cuota['CFT']);
        		$this->cuota['TNA'] = $this->TNA;
                $this->cuota['TEM'] = $this->TEM;

                $this->CFT = $this->cuota['CFT'];
                $this->CFTA = $this->cuota['CFTA'];
                
                
	}

    public function calcularCuotaFrances($capital){
        if($this->tasa == 0) {return 0;}
        $cuota = $capital * ($this->tasa / 100) / (1 - pow((1 + ($this->tasa / 100)),($this->cuotas * (-1))));
        return round($cuota,2);
    }
	
    public function calcularCuotaDirecta($capital){
        $cuota = (($capital + ( $capital * $this->cuotas * ($this->tasa / 100))) / $this->cuotas);
        return round($cuota,2);
    }
        



	public function armar_plan(){
		// $saldo = $this->solicitado * (1 + ($this->porcAdic / 100));
                $saldo = $this->getCapitalBaseDeCalculo();
                $cuo = $capi = $adi = $inte = $gto = $sell = $iva = 0;
                
		for($i=1;$i<=$this->cuotas;$i++){
			$this->set_valor_cuota($i,$saldo);
			$this->plan[$i] = $this->cuota;
			$saldo = round($this->cuota['SALDO'],2);
                        
                        $cuo += $this->cuota['IMPORTE'];
                        $capi += $this->cuota['CAPITAL'];
                        $inte += $this->cuota['INTERES'];
                        // $adi += $this->cuota['ADICIONAL'];
                        // $sell += $this->cuota['SELLADO'];
                        $iva += $this->cuota['IVA'];
                        
			if($i==$this->cuotas && $saldo != 0){
                            $this->plan[$i]['CAPITAL'] += round($saldo,2);
                            $this->plan[$i]['SALDO'] = 0;
			}
		}
                $this->cuota_promedio = array(
                                        'CUOTA' => $this->cuotas,
                                        'IMPORTE' => round($cuo / $this->cuotas,2),
                                        'CAPITAL' => round($capi / $this->cuotas,2),
                                        // 'ADICIONAL' => round($adi / $this->cuotas,2),
                                        // 'SELLADO' => round($sell / $this->cuotas,2),
                                        'INTERES' => round($inte / $this->cuotas,2),
                                        'IVA' => round($iva / $this->cuotas,2),
                                        'TNA' => $this->TNA,
                                        'TEM' => $this->TEM,
                                        'SALDO' => 0,
                                        'CFT' => 0,
                                        'CFTA' => 0,
                                );  
                if($this->solicitado != 0){$this->cuota_promedio['CFT'] = round(((($this->cuota_promedio['IMPORTE'] * $this->cuotas) - $this->solicitado) / $this->solicitado) * 100,2);}
		$this->cuota_promedio['CFTA'] = ($this->cuota_promedio > 12 ? round(($this->cuota_promedio['CFT'] / $this->cuotas) * 12,2) : $this->cuota_promedio['CFT']);
                $this->calcularLiquidacion($inte,$cuo);

                $this->set_objetoCalculo();

//                debug($capi . " ** " . $adi . " ** ". $inte . " ** ". $sell . " ** ". $iva);
        //        debug($this->liquidacion);
        //        exit;
	}   
        

        private function getCapitalBaseDeCalculo(){
            $capitalBaseCalculo = $this->solicitado;
            if(!empty($this->tipoCuotaGAdmin) && !empty($this->porcAdic) && ($this->baseCalculoGadmin == 1 || $this->baseCalculoGadmin == 4)){
                $capitalBaseCalculo += round($this->solicitado * ($this->porcAdic / 100),2);
            }
            if(!empty($this->tipoCuotaSellado) && !empty($this->porcSello) && ($this->baseCalculoSellado == 1 || $this->baseCalculoSellado == 4)){
                $capitalBaseCalculo += round($this->solicitado * ($this->porcSello / 100),2);
            }
            return round($capitalBaseCalculo,2);        
        }
        

        function calcularLiquidacion($totalInteres,$totalPrestamo){
                /// ARMO LA LIQUIDACION DEL PLAN
                $this->liquidacion['netoPercibe'] = $this->solicitado;
                //totalPrestamo
                $this->liquidacion['totalPrestamo'] = $totalPrestamo;

                App::import('Model', 'Config.GlobalDato');
                $this->GlobalDato = new GlobalDato(null);
              

                //gto otorgamiento
                if(!empty($this->tipoCuotaGAdmin) && !empty($this->porcAdic)){
                        $this->liquidacion['porcGastoAdmin'] = $this->porcAdic;
                        $this->liquidacion['baseCalculoGadmin'] = $this->baseCalculoGadmin; // baseCalculoGadmin
                        $this->liquidacion['tipoCuotaGAdmin'] = $this->tipoCuotaGAdmin; // baseCalculoGadmin

                        $dato = $this->GlobalDato->read("concepto_1",$this->liquidacion['tipoCuotaGAdmin']);
                        $this->liquidacion['tipoCuotaGAdminDesc'] = $dato['GlobalDato']["concepto_1"];                          

                        switch ($this->baseCalculoGadmin) {
                                case 1:
                                        #sobre capital
                                        $this->liquidacion['gastoAdmin']  = round($this->liquidacion['netoPercibe'] * ($this->porcAdic / 100),2);
                                        break;
                                case 2: 
                                        # sobre interes
                                        $this->liquidacion['gastoAdmin']  = round($totalInteres * ($this->porcAdic / 100),2);
                                case 3:
                                        # sobre total a reintegrar
                                        $this->liquidacion['gastoAdmin']  = round($totalPrestamo * ($this->porcAdic / 100),2);
                                        break;
                                case 4:
                                    #sobre capital Y SE APLICA AL CALCULO DE INTERESES
                                    $this->liquidacion['gastoAdmin']  = round($this->liquidacion['netoPercibe'] * ($this->porcAdic / 100),2);
                                    break;
                                default:
                                $this->liquidacion['gastoAdmin']  = 0;                       
                        }
                }

                //sellados
                if(!empty($this->tipoCuotaSellado) && !empty($this->porcSello)){
                        $this->liquidacion['porcSellado'] = $this->porcSello;
                        $this->liquidacion['baseCalculoSellado'] = $this->baseCalculoSellado; // baseCalculoGadmin
                        $this->liquidacion['tipoCuotaSellado'] = $this->tipoCuotaSellado; // baseCalculoGadmin

                        $dato = $this->GlobalDato->read("concepto_1",$this->liquidacion['tipoCuotaSellado']);
                        $this->liquidacion['tipoCuotaSelladoDesc'] = $dato['GlobalDato']["concepto_1"];  
                        
                        switch ($this->baseCalculoSellado) {
                                case 1:
                                        #sobre capital
                                        $this->liquidacion['sellados']  = round($this->liquidacion['netoPercibe'] * ($this->porcSello / 100),2);
                                        break;
                                case 2: 
                                        # sobre interes
                                        $this->liquidacion['sellados']  = round($totalInteres * ($this->porcSello / 100),2);
                                case 3:
                                        # sobre total a reintegrar
                                        $this->liquidacion['sellados']  = round($totalPrestamo * ($this->porcSello / 100),2);
                                        break; 
                                case 4:
                                    #sobre capital
                                    $this->liquidacion['sellados']  = round($this->liquidacion['netoPercibe'] * ($this->porcSello / 100),2);
                                    break;
                                default:
                                $this->liquidacion['sellados']  = 0;                       
                        }
                }                

                $this->liquidacion['capitalSolicitado'] = $this->solicitado + $this->liquidacion['gastoAdmin'] + $this->liquidacion['sellados'];
                $this->liquidacion['capitalBaseCalculo'] = $this->getCapitalBaseDeCalculo();
                $this->liquidacion['TNA'] = $this->TNA;
                $this->liquidacion['TEM'] = $this->TEM;
                $this->liquidacion['TEA'] = $this->TEA;
                $this->liquidacion['CFT'] = $this->CFT;
                $this->liquidacion['CFTA'] = $this->CFTA;
                $this->liquidacion['metodo'] = $this->METODOS[$this->METODO_CALCULO];
                $this->liquidacion['interesesDevengados'] = $this->liquidacion['totalPrestamo'] - $this->liquidacion['netoPercibe'];
                $this->liquidacion['interesMoratorio'] = $this->interesMoratorio;
                $this->liquidacion['costoCancelacionAnticipada'] = $this->costoCancelacionAnticipada;


        }

	
	function determinar_tasa($valorCuota){
		$tasa_base = 0.01;
		$instance = new MetodoCalculoCuota();
		$instance->solicitado = $this->getCapitalBaseDeCalculo();
		$instance->cuotas = $this->cuotas;
		$instance->tasa = $tasa_base;
		// $instance->porcAdic = $this->porcAdic;
		// $instance->porcIVA = $this->porcIVA;		
		$instance->set_valor_cuota();
		$intentos = 10000;
		while(true){
			$instance->reset();
			$instance->tasa += $tasa_base;
			$instance->tasa = round($instance->tasa,2);
			$instance->set_valor_cuota();
			$diff = $instance->cuota['IMPORTE'] - $valorCuota;
                        if($diff == 0){break;}
			$intentos-=1;
			if($intentos == 0){
				$this->reset();
				$this->error = 1;
				$this->cuota['IMPORTE'] = $valorCuota;
				$this->msgError = "NO SE PUDO ESTABLECER LOS VALORES";
				break;
			}	
		}
		return $instance->tasa;
	}
	
	
	function set_valor_cuota_directo($valorCuota){

            if(empty($this->solicitado)){
                    $this->error = 1;
                    $this->reset();
                    $this->msgError = "EL IMPORTE SOLICITADO ES REQUERIDO";	
                    return;		
            }
            if(empty($this->cuotas)){
                    $this->error = 1;
                    $this->reset();
                    $this->msgError = "LA CANTIDAD DE CUOTAS ES REQUERIDO";			
                    return;
            }
            $this->cuota['IMPORTE'] = round($valorCuota,2);
            $capital_1 = $this->solicitado;
            $capital_2 = $this->solicitado * (1 + ($this->porcAdic / 100));
            $this->cuota['CAPITAL'] = round($capital_1  / $this->cuotas,2);
            if($capital_2 > $capital_1) {$this->cuota['ADICIONAL'] = round(($capital_2 - $capital_1)  / $this->cuotas,2);}
            else {$this->cuota['ADICIONAL'] = 0;}
            if(!empty($this->porcIVA)) {$this->cuota['INTERES'] = round(($this->cuota['IMPORTE'] - ( $this->cuota['CAPITAL'] + $this->cuota['ADICIONAL'])) / (1 + ($this->porcIVA / 100)),2);}
            else if(!empty($this->porcIVA)) {$this->cuota['INTERES'] = round(($this->cuota['IMPORTE'] - ( $this->cuota['CAPITAL'] + $this->cuota['ADICIONAL'])),2);}
            $this->cuota['IVA'] = round(($this->cuota['IMPORTE'] - ( $this->cuota['CAPITAL'] + $this->cuota['ADICIONAL'])) - $this->cuota['INTERES'],2) ;
            $this->msgError = "*** DIRECTO ***";	
	}    
    
        
        
        function tna_to_tea($tna){
            $tea = (pow((1 + ($tna / 100) / 12),12) - 1) * 100;
            return round($tea,2);
        }
        
        function tea_to_tem($tea){
            $tem = (pow((1 + ($tea / 100)),(30/360)) - 1) * 100;
            return round($tem,2);
        }
        
        function tna_to_tem($tna){
            $tea = $this->tna_to_tea($tna);
            $tem = $this->tea_to_tem($tea);
            return round($tem,2);
        }
        
        function tna_to_tnm($tna){
            $tea = $this->tna_to_tea($tna);
            $tem = $this->tea_to_tem($tea);
            return round($tem,2);
        }        
        
        function cft($capital,$montoCuota,$cuotas){
            $cft = ((($montoCuota * $cuotas) - $capital) / $capital) * 100;
            return round($cft,2);
        }
        
        function validate_fields($fields){
            
            $validate = TRUE;
            
            if(!is_numeric($fields['ProveedorPlanGrilla']['capital_minimo']) || empty($fields['ProveedorPlanGrilla']['capital_minimo'])){
                $validate = FALSE;
                // parent::notificar ("El capital MINIMO es requerido");
            }
            if(!is_numeric($fields['ProveedorPlanGrilla']['capital_maximo']) || empty($fields['ProveedorPlanGrilla']['capital_maximo'])){
                $validate = FALSE;
                // parent::notificar ("El capital MAXIMO es requerido");
            }
            if($fields['ProveedorPlanGrilla']['capital_minimo'] < $fields['ProveedorPlanGrilla']['capital_maximo'] && (!is_numeric($fields['ProveedorPlanGrilla']['capital_incremento']) || empty($fields['ProveedorPlanGrilla']['capital_incremento']))){
                $validate = FALSE;
                // parent::notificar ("El incremento de capital es requerido");
            } 
            
            if(!is_array(explode(',', $fields['ProveedorPlanGrilla']['cuotas_disponibles'])) || empty($fields['ProveedorPlanGrilla']['cuotas_disponibles'])){
                $validate = FALSE;
                // parent::notificar ("Las opciones de CUOTAS son requeridas");                
            }
                
            return $validate;    
        }
        
        function arma_opciones($min,$max,$inc,$cuotas){
            $valores = array();
            $capital = $min;
            while($capital <= $max){
                $aCuota = array();                
                if(!empty($cuotas)){
                    foreach($cuotas as $cuota){
                        $aCuota[$cuota] = $cuota;
                    }
                }
                ksort($aCuota);
                $valores[$capital] = $aCuota;
                if ($capital == $max) {break;}
                $capital+=$inc;
            }
            $opciones = array();
            if (!empty($valores)) {
                foreach ($valores as $capital => $cuotas) {
                    $this->solicitado = $capital;
                    $objOpcion = new stdClass();
                    $objOpcion->solicitado =  $this->solicitado;
                    $objOpcion->objetosCalculo = array(); 

                    foreach ($cuotas as $cuota) {
                        $this->cuotas = $cuota;
                        $this->armar_plan();
                        $opciones[$capital][$cuota] = array(
                            'liquidacion' => $this->liquidacion,
                            'cuotaPromedio' => $this->cuota_promedio,
                            'objetoCalculo' => $this->get_objetoCalculo(),
                        );
                        array_push($objOpcion->objetosCalculo,$this->get_objetoCalculo(FALSE));
                        $this->reset();
                    }
                    array_push($this->objetoOpciones,$objOpcion);
                }
            }            

            return $opciones;
        }


        function setPeriodosAndVencimientos($objetoCalculado,$periodoInicio,$primerVtoSocio,$primerVtoProveedor,$model,$toJSON = TRUE){
                $objetoCalculado->periodoInicio = $periodoInicio;
                $objetoCalculado->primerVtoSocio = $primerVtoSocio;
                $objetoCalculado->primerVtoProveedor = $primerVtoProveedor;

                $mkIni = mktime(0,0,0,substr($periodoInicio,4,2),1,substr($periodoInicio,0,4));	
                $mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($primerVtoSocio)),date('d',strtotime($primerVtoSocio)),date('Y',strtotime($primerVtoSocio)));
                $mkIniVtoProv = mktime(0,0,0,date('m',strtotime($primerVtoProveedor)),date('d',strtotime($primerVtoProveedor)),date('Y',strtotime($primerVtoProveedor)));
                $i = 0;
                foreach($objetoCalculado->detalleCuotas as $key => $objetoCuota){
                        $objetoCuota->periodo = date('Ym',$model->addMonthToDate($mkIni,$i));
                        $objetoCuota->vtoSocio = date('Y-m-d',$model->addMonthToDate($mkIniVtoSocio,$i));
                        $objetoCuota->vtoProveedor = date('Y-m-d',$model->addMonthToDate($mkIniVtoProv,$i));
                        $objetoCalculado->detalleCuotas[$key] = $objetoCuota;
                        $i++;
                }
                return ($toJSON ? json_encode($objetoCalculado) : $objetoCalculado);
        }

        
}
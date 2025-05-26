<?php

/**
 * PROCESO DE IMPUTACION DE PAGOS DE UNA LIQUIDACION
 * 
 * LANZADORES 
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php imputar_pagos_fraccion 381486 -app /home/adrian/trabajo/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php imputar_pagos_fraccion 113 -app /var/www/sigem/app/
 * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php imputar_pagos_fraccion 47710 -app /home/mutualam/public_html/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * 
 */

// 
// 

App::import('Vendor','exec',array('file' => 'exec.php'));

class ImputarPagosFraccionLoteShell extends Shell {
	
	var $liquidacionID = 0;
	var $fecha_pago = null;
	var $nro_recibo = null;
	var $PROCCES_ID = 0;
//	var $pid		= null;
	var $oASINC 	= null;
        
	
	var $tasks = array('Temporal','Error');
	
	function main() {
		
            if(empty($this->args[0])){
                    $this->out("ERROR: PID NO ESPECIFICADO");
                    return;
            } 
            
            
            
            $liquidacionID          = $this->args[0];
            $DESIMPUTAR             = $this->args[1];
            $pid                    = $this->args[2];
            $SHELL_PID              = $this->args[3];
            $i                      = $this->args[4];
            $limit                  = $this->args[5];
            $FPAGO                  = $this->args[6];
            $NROREC                 = $this->args[7];

		
//            debug($this->args);
            
//            $this->out($i." ** ".$limit);
            
            App::import('Model','Shells.Asincrono');
            $oASINC = new Asincrono();
            $oASINC->auditable = FALSE;

            $oASINC->id = $pid; 
            $SHELL_sPID = $oASINC->getParametro('shell_pid');
            
            
            App::import('Model','Mutual.LiquidacionSocioRendicion');
            $oLSR = new LiquidacionSocioRendicion();
            
            $SQL = "select socio_id from liquidacion_socio_rendiciones
                    where liquidacion_id = $liquidacionID and indica_pago = 1
                    and orden_descuento_cobro_id = 0 
                    group by socio_id order by socio_id LIMIT $i,$limit;";

            $socios = $oLSR->query($SQL); 
            
            if(!empty($socios)){
                
                $total = count($socios);
                $oASINC->setTotal($total);
                $i = 0; 
                $SHELL = new exec();
                
                App::import('Model','Mutual.OrdenDescuentoCobro');
                $oCOBRO = new OrdenDescuentoCobro();
                
                foreach($socios as $socio){
                    
                    if(!$SHELL->is_running($SHELL_PID)){
                        $SHELL->kill($SHELL_sPID);
                        break;
                    } 
                    
                    $socio_id = $socio['liquidacion_socio_rendiciones']['socio_id'];

//                    $this->out($msg);
                    #########################################################################
                    # ANALIZAR SI SE DESIMPUTA AL SOCIO
                    #########################################################################
                    if($DESIMPUTAR){
                        $SQL = "select count(*) as cantidad from liquidacion_socio_rendiciones
                                where liquidacion_id = $liquidacionID and socio_id = $socio_id and indica_pago = 1
                                and ifnull(orden_descuento_cobro_id,0) <> 0";
                        $datos = $oLSR->query($SQL);
                        if(!empty($datos)){
                            $oCOBRO->desimputarLiquidacion($this->liquidacionID, $socio_id);
                            $msg = "$i / $total - DESIMPUTANDO PAGOS >> SOCIO #$socio_id";
                            $oASINC->actualizar($i,$total,$msg);                            
                        }
                    }
                    #########################################################################
                    # IMPUTAR AL SOCIO
                    #########################################################################
                    $msg = "$i / $total - IMPUTANDO PAGOS >> SOCIO #$socio_id";
                    $oASINC->actualizar($i,$total,$msg);                    
                    $resultado = $oCOBRO->imputarLiquidacion($liquidacionID, $socio_id, $FPAGO,$NROREC,FALSE,TRUE);
                    if(empty($resultado)){
                        $resultado['ERROR'] = 1;
                        $resultado['MENSAJE'] = "ERROR EN PROCEDIMIENTO imputarLiquidacion *** RESULTADO NULO - SOCIO # $socio_id***";
                    }

                    if($resultado['ERROR'] == 1){
                            $ERROR = $resultado['MENSAJE'];
                            $oASINC->actualizar($i,$total,$ERROR);
                            $this->Temporal->setErrorMsg($msg,$ERROR);
                    }else{
                        $msg .= "| COBRO EMITIDO #" . $resultado['COBRO_ID'];
                        $asinc->actualizar($i,$total,$msg);
                    }

//                    if(empty($ERROR)) $this->__calificarSocio($socio_id);
                    
                    $i++;
                    
                } //endforeach socios
                
                
                
            } // end if empty(socios)
            
            $oASINC->actualizar($total,$total,"**** PROCESO FINALIZADO ****");
            $oASINC->fin("**** PROCESO FINALIZADO ****");            
            
//            debug($socios);
		
	}

}
?>
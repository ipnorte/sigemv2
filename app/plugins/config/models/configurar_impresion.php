<?php

class ConfigurarImpresion extends ConfigAppModel{
	
    var $name = 'ConfigurarImpresion';
        
    var $hasMany = array('ConfigurarImpresionDetalle' => array('dependent' => true, 'order' => array('ConfigurarImpresionDetalle.superior', 'ConfigurarImpresionDetalle.izquierda')));
    
    function Grabar($datos) {
//        App::import('Model', 'config.ConfigurarImpresionDetalle')
        
        $oCnfImpDetalle = $this->importarModelo('ConfigurarImpresionDetalle', 'config');
        $gbrCnfImpresion = array('ConfigurarImpresion' => array('id' => 0, 'descripcion' => '', 'ancho' => 0.00, 'alto' => 0.00, 'talonario' => 0));
        $gbrCnfImpDetalle = array('ConfigurarImpresionDetalle' => array());
        
        
        
        $gbrCnfImpresion['ConfigurarImpresion']['descripcion'] = $datos['CnfImpresion']['Descripcion']; 
        $gbrCnfImpresion['ConfigurarImpresion']['ancho']       = number_format($datos['CnfImpresion']['Ancho'],2); 
        $gbrCnfImpresion['ConfigurarImpresion']['alto']        = number_format($datos['CnfImpresion']['Alto'],2); 
        $gbrCnfImpresion['ConfigurarImpresion']['talonario']   = number_format($datos['CnfImpresion']['Talonario'],0); 


        parent::begin();
        
        if(isset($datos['CnfImpresion']['id']) && $datos['CnfImpresion']['id'] > 0):
            $gbrCnfImpresion['ConfigurarImpresion']['id']   = $datos['CnfImpresion']['id']; 
            if(!$oCnfImpDetalle->deleteAll("ConfigurarImpresionDetalle.configurar_impresion_id=" . $datos['CnfImpresion']['id'])):
                return false;
            endif;
        endif;
        
        
        if(!$this->save($gbrCnfImpresion)):
           return FALSE;
        endif;
        
        $cnfImpId = (isset($datos['CnfImpresion']['id']) && $datos['CnfImpresion']['id'] > 0 ? $datos['CnfImpresion']['id'] : $this->getLastInsertID());

        if($datos['CnfImpresion']['DiaVen'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'DIAVEN';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['DiaVenIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['DiaVenSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['DiaVenAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['DiaVenAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = $datos['CnfImpresion']['DiaVenFor'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;

            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['MesVen'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'MESVEN';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['MesVenIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['MesVenSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['MesVenAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['MesVenAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = $datos['CnfImpresion']['MesVenFor'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['AnoVen'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'ANOVEN';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['AnoVenIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['AnoVenSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['AnoVenAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['AnoVenAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = $datos['CnfImpresion']['AnoVenFor'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['FecVen'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'FECVEN';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['FecVenIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['FecVenSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['FecVenAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['FecVenAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = $datos['CnfImpresion']['FecVenFor'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['DiaEmi'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'DIAEMI';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['DiaEmiIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['DiaEmiSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['DiaEmiAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['DiaEmiAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = $datos['CnfImpresion']['DiaEmiFor'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['MesEmi'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'MESEMI';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['MesEmiIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['MesEmiSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['MesEmiAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['MesEmiAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = $datos['CnfImpresion']['MesEmiFor'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['AnoEmi'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'ANOEMI';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['AnoEmiIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['AnoEmiSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['AnoEmiAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['AnoEmiAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = $datos['CnfImpresion']['AnoEmiFor'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['FecEmi'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'FECEMI';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['FecEmiIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['FecEmiSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['FecEmiAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['FecEmiAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = $datos['CnfImpresion']['FecEmiFor'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['Destin'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'DESTIN';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['DestinIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['DestinSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['DestinAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['DestinAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = '';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['CntNro'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'CNTNRO';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['CntNroIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['CntNroSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['CntNroAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['CntNroAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = '';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
        
        if($datos['CnfImpresion']['CntLtr'] === '1'):
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['id'] = 0;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['configurar_impresion_id'] = $cnfImpId;
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['variable'] = 'CNTLTR';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['izquierda'] = $datos['CnfImpresion']['CntLtrIzq'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['superior'] = $datos['CnfImpresion']['CntLtrSup'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['ancho'] = $datos['CnfImpresion']['CntLtrAnc'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['alto'] = $datos['CnfImpresion']['CntLtrAlt'];
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['formato'] = '';
            $gbrCnfImpDetalle['ConfigurarImpresionDetalle']['imprime'] = 1;
            
            if(!$oCnfImpDetalle->save($gbrCnfImpDetalle)):
                parent::rollback();
                return FALSE;
            endif;
        endif;
     
        parent::commit();

        return TRUE;
    }
	
    
    function getChequeId($id){

        // ARMO LA FECHA        
        App::import('Helper', 'Util');
        $oUT = new UtilHelper();                
            
        $oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
        $oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
        $cheque = $oBancoMovimiento->getMovimientoId($id, false);
        $cuenta = $oBancoCuenta->getCuenta($cheque[0]['BancoCuentaMovimiento']['banco_cuenta_id']);
        $chqConfiguracion = $this->find('all', array('conditions' => array('ConfigurarImpresion.id' => $cuenta['BancoCuenta']['formato_cheque'])));

        /*
         * Gravo el campo imprimir en 0 (CERO).
         */
         $cheque[0]['BancoCuentaMovimiento']['imprimir'] = 0;
        $oBancoMovimiento->save($cheque[0]);

        $chqConfiguracion[0]['ConfigurarImpresion']['numero_cheque']= $cheque[0]['BancoCuentaMovimiento']['numero_operacion'];


        /*
         * Buscamos el destinatario del cheque
         */
         if($cheque[0]['BancoCuentaMovimiento']['orden_pago_id'] > 0):
             $cheque[0]['BancoCuentaMovimiento']['destinatario'] = $this->getChqDestinatario($cheque[0]['BancoCuentaMovimiento']['orden_pago_id']);
         endif;
         
        /* str_pad($input, 10, "-=", STR_PAD_LEFT)
         * Pasamos el importe en letras
         */  
         $cheque[0]['BancoCuentaMovimiento']['importe_letra'] = $this->num2letras($cheque[0]['BancoCuentaMovimiento']['importe']);

         foreach($chqConfiguracion[0]['ConfigurarImpresionDetalle'] as $clave => $variable):

            if($variable['variable'] == 'DIAVEN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getDia($variable['formato'], $cheque[0]['BancoCuentaMovimiento']['fecha_vencimiento']);
            endif;
            if($variable['variable'] == 'MESVEN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getMes($variable['formato'], $cheque[0]['BancoCuentaMovimiento']['fecha_vencimiento']);
            endif;
            if($variable['variable'] == 'ANOVEN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getAnio($variable['formato'], $cheque[0]['BancoCuentaMovimiento']['fecha_vencimiento']);
            endif;
            if($variable['variable'] == 'FECVEN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getFecha($variable['formato'], $cheque[0]['BancoCuentaMovimiento']['fecha_vencimiento']);
            endif;
            if($variable['variable'] == 'DIAEMI'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getDia($variable['formato'], $cheque[0]['BancoCuentaMovimiento']['fecha_operacion']);
            endif;
            if($variable['variable'] == 'MESEMI'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getMes($variable['formato'], $cheque[0]['BancoCuentaMovimiento']['fecha_operacion']);
            endif;
            if($variable['variable'] == 'ANOEMI'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getAnio($variable['formato'], $cheque[0]['BancoCuentaMovimiento']['fecha_operacion']);;
            endif;
            if($variable['variable'] == 'FECEMI'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getFecha($variable['formato'], $cheque[0]['BancoCuentaMovimiento']['fecha_operacion']);
            endif;
            if($variable['variable'] == 'DESTIN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $cheque[0]['BancoCuentaMovimiento']['destinatario'];
            endif;
            if($variable['variable'] == 'CNTNRO'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = number_format($cheque[0]['BancoCuentaMovimiento']['importe'],2, ',', '.');
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['izquierda'] -= 1; 
            endif;
            if($variable['variable'] == 'CNTLTR'):
                $str_len = mb_strlen($cheque[0]['BancoCuentaMovimiento']['importe_letra']);
                if($str_len > 64):
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = str_pad(mb_substr($cheque[0]['BancoCuentaMovimiento']['importe_letra'], 0, 64), 65, '-', STR_PAD_RIGHT);
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['id'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['id'];
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['configurar_impresion_id'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['configurar_impresion_id'];
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['variable'] = str_pad(mb_substr($cheque[0]['BancoCuentaMovimiento']['importe_letra'], 64), 70, '-', STR_PAD_RIGHT);
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['izquierda'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['izquierda']-2;
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['superior'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['superior']+0.3;
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['ancho'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['ancho'];
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['alto'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['alto'];
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['formato'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['formato'];
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['imprime'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['imprime'];
                else:
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = str_pad($cheque[0]['BancoCuentaMovimiento']['importe_letra'], 65, '-', STR_PAD_RIGHT);
                endif;
            
            endif;
        endforeach;
        
        return $chqConfiguracion;
            
        
    }
    
    function getChqDestinatario($op_id){
        $oOPago = $this->importarModelo('OrdenPago', 'proveedores');
        
        $aOPago = $oOPago->getOrdenDePago($op_id);
        
        return $aOPago['OrdenPago']['Proveedor']['destinatario'];
    }
    
    function getDia($formato, $fecha){
        $retorno = '';
 
        switch ($formato){
            case 0:
                $retorno = trim($this->num2letras(date('d',strtotime($fecha))));
                 break;
            case 1:
                $retorno = strval(intval(date('d',strtotime($fecha))));
                break;
            case 2:
                $retorno = str_pad(date('d',strtotime($fecha)), 2, "0", STR_PAD_LEFT);
                break;
        }
        
        return $retorno;

    }
    
    function getMes($formato, $fecha){
        $aMesAbreviado = array(1 => 'Ene.', 2 => 'Feb.', 3 => 'Mar.', 4 => 'Abr.', 5 => 'Mayo', 6 => 'Jun.', 7 => 'Jul.', 8 => 'Ago.', 9 => 'Sep.', 10 => 'Oct.', 11 => 'Nov.', 12 => 'Dic.');
        $aMesAmpliado = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
        $nIndice = strval(intval(date('m',strtotime($fecha))));
        $retorno = '';


        switch ($formato){
            case 0:
                $retorno = strval(intval(date('m',strtotime($fecha))));
                 break;
            case 1:
                $retorno = str_pad(date('m',strtotime($fecha)), 2, "0", STR_PAD_LEFT);
                break;
            case 2:
                $retorno = $aMesAbreviado[$nIndice];
                break;
            case 3:
                $retorno = $aMesAmpliado[$nIndice];
                break;
        }
        
        return $retorno;

    }
           
    
    function getAnio($formato, $fecha){
        $retorno = '';

        switch ($formato){
            case 0:
                $retorno = strval(intval(date('y',strtotime($fecha))));
                break;
            case 1:
                $retorno = strval(intval(date('Y',strtotime($fecha))));
                break;
        }
        
        return $retorno;

    }
           
    
    function getFecha($formato, $fecha){
        $retorno = '';
        $cDia = strval(intval(date('d',strtotime($fecha))));
        $cMesAbreviado = $this->getMes(2,$fecha);
        $cMesAmpliado = $this->getMes(3,$fecha);
        $cAnio2 = strval(intval(date('y',strtotime($fecha))));
        $cAnio4 = strval(intval(date('Y',strtotime($fecha))));

        
        switch ($formato){
            case 0:
                /* 31/12/07 */
                $retorno = strval(intval(date('d-m-y',strtotime($fecha))));
                break;
            case 1:
                /* 31/12/2007 */
                $retorno = strval(intval(date('d-m-Y',strtotime($fecha))));
                break;
            case 2:
                /* 31 de Diciembre de 07 */
                $retorno = $cDia . ' de ' . $cMesAmpliado . ' de ' . $cAnio2;
                break;
            case 3:
                /* 31 de Dic. de 07 */
                $retorno = $cDia . ' de ' . $cMesAbreviado . ' de ' . $cAnio2;
                break;
            case 4:
                /* 31 de Diciembre de 2007 */
                $retorno = $cDia . ' de ' . $cMesAmpliado . ' de ' . $cAnio4;
                break;
            case 5:
                /* 31 de Dic. de 2007 */
                $retorno = $cDia . ' de ' . $cMesAbreviado . ' de ' . $cAnio4;
                break;
        }

        return $retorno;

    }
	
    
    function getEjemploId($id){

        // ARMO LA FECHA        
        App::import('Helper', 'Util');
        $oUT = new UtilHelper();                
            
//        $oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
//        $oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
//        $cheque = $oBancoMovimiento->getMovimientoId($id, false);
//        $cuenta = $oBancoCuenta->getCuenta($cheque[0]['BancoCuentaMovimiento']['banco_cuenta_id']);
        $chqConfiguracion = $this->find('all', array('conditions' => array('ConfigurarImpresion.id' => $id)));

//        $importe = 67755123.30;
        $cuit = Configure::read('APLICACION.cuit_mutual');
        $importe = strval(substr($cuit,-7)) / 10;
        
        
        /*
         * Pasamos el importe en letras
         */  
         $importe_letra = $this->num2letras($importe);

         foreach($chqConfiguracion[0]['ConfigurarImpresionDetalle'] as $clave => $variable):

            if($variable['variable'] == 'DIAVEN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getDia($variable['formato'], date('Y-m-d'));
            endif;
            if($variable['variable'] == 'MESVEN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getMes($variable['formato'], date('Y-m-d'));
            endif;
            if($variable['variable'] == 'ANOVEN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getAnio($variable['formato'], date('Y-m-d'));
            endif;
            if($variable['variable'] == 'FECVEN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getFecha($variable['formato'], date('Y-m-d'));
            endif;
            if($variable['variable'] == 'DIAEMI'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getDia($variable['formato'], date('Y-m-d'));
            endif;
            if($variable['variable'] == 'MESEMI'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getMes($variable['formato'], date('Y-m-d'));
            endif;
            if($variable['variable'] == 'ANOEMI'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getAnio($variable['formato'], date('Y-m-d'));
            endif;
            if($variable['variable'] == 'FECEMI'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = $this->getFecha($variable['formato'], date('Y-m-d'));
            endif;
            if($variable['variable'] == 'DESTIN'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = Configure::read('APLICACION.nombre_fantasia');
            endif;
            if($variable['variable'] == 'CNTNRO'):
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = number_format($importe,2, ',', '.');
                $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['izquierda'] -= 1; 
            endif;
            if($variable['variable'] == 'CNTLTR'):
                $str_len = mb_strlen($importe_letra);
                if($str_len > 64):
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = str_pad(mb_substr($importe_letra, 0, 64), 65, '-', STR_PAD_RIGHT);
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['id'] = 0;
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['configurar_impresion_id'] = $id;
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['variable'] = str_pad(mb_substr($importe_letra, 64), 70, '-', STR_PAD_RIGHT);
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['izquierda'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['izquierda']-2;
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['superior'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['superior']+0.3;
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['ancho'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['ancho'];
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['alto'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['alto'];
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['formato'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['formato'];
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave+1]['imprime'] = $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['imprime'];
                else:
                    $chqConfiguracion[0]['ConfigurarImpresionDetalle'][$clave]['variable'] = str_pad($importe_letra, 65, '-', STR_PAD_RIGHT);
                endif;
            
            endif;
        endforeach;
        
        return $chqConfiguracion;
    }
    
}
?>
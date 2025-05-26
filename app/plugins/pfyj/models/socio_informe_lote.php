<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of socio_informe_lote
 *
 * @author adrian
 */

App::import('Vendor','GeneraXLS',array('file' => 'genera_xls.php'));

class SocioInformeLote extends PfyjAppModel {
    //put your code here
    
    var $name = "SocioInformeLote";
    var $hasMany = array('SocioInforme');
    
    
    function borrarLote($id){
        
        App::import('model','pfyj.SocioInforme');
        $oSI = new SocioInforme(); 
            
        parent::begin();
        if(!$oSI->updateAll(array('SocioInforme.socio_informe_lote_id' => NULL),array('SocioInforme.socio_informe_lote_id' => $id))){
            parent::rollback();
            return false;            
        }
        if(!$this->del($id)){
            parent::rollback();
            return false;             
        }
        return parent::commit();
    }
    
    function generarLote($datos,$formato = "XLS"){
        
//        debug($datos);
        
        if(!empty($datos['SocioInforme']['id'])){
            
            $xls = array();
            
            App::import('model','pfyj.SocioInforme');
            $oSI = new SocioInforme(); 
            
            parent::begin();
            $lote = array();
            $lote['SocioInformeLote'] = array(
                'id' => 0,
                'empresa' => $datos['SocioInforme']['empresa'],
                'lote' => 0,
                'periodo_hasta' => $datos['SocioInforme']['periodo_hasta'],
            );   
            
            
            if(!$this->save($lote)){
                parent::rollback();
                return false;
            }
            
            $loteId = $this->getLastInsertID();   

            if(empty($loteId)){
                parent::rollback();
                return false;                
            }
            $lote['SocioInformeLote']['id'] = $loteId;
//            debug($lote);
//            exit;    
            $socioInforme = array();
            
//            App::import('Model', 'Config.Provincia');
//            $oPROVINCIA = new Provincia(null);            
            
            foreach($datos['SocioInforme']['id'] as $id){
                
                $info = $oSI->get($id);
                
                $info['SocioInforme']['socio_informe_lote_id'] = $loteId;
                
//                debug($info);
//                exit;
                
                if(empty($info)){
                    parent::rollback();
                    exit;
                    return false;
                }
                
                $ultimoDia = parent::ultimoDiaMes(substr($info['SocioInforme']['periodo_hasta'],-2), substr($info['SocioInforme']['periodo_hasta'],0,4));

                
                $row = array(
                    'apellido' => $info['Socio']['Persona']['apellido'],
                    'nombre' => $info['Socio']['Persona']['nombre'],
                    'documento' => $info['Socio']['Persona']['documento'],
                    'tipo_ope' => 'PRESTAMO PERSONAL',
                    'nro_ope' => $info['Socio']['id'],
                    'importe' => $info['SocioInforme']['deuda_informada'],
                    'fecha_mora' => $ultimoDia."/".substr($info['SocioInforme']['periodo_hasta'],-2)."/".substr($info['SocioInforme']['periodo_hasta'],0,4),
                    'calle' => $info['Socio']['Persona']['calle'],
                    'numero' => $info['Socio']['Persona']['numero_calle'],
                    'piso' => $info['Socio']['Persona']['piso'],
                    'barrio' => $info['Socio']['Persona']['barrio'],
                    'localidad' => $info['Socio']['Persona']['localidad'],
                    'provincia' => $info['Socio']['Persona']['Provincia']['nombre'],
                    'codigo_postal' => $info['Socio']['Persona']['codigo_postal'],
                    'sexo' => $info['Socio']['Persona']['sexo'],
                    'telefono' => $info['Socio']['Persona']['telefono_fijo'],
                    'e_mail' => $info['Socio']['Persona']['e_mail'],
                    'celular' => $info['Socio']['Persona']['telefono_movil'],
                    
                );
                
                array_push($xls, $row);
                array_push($socioInforme,$info['SocioInforme']);
                
            }
            
            $lote['SocioInformeLote']['lote'] = $this->generaExcelVeraz($xls);
            $lote['SocioInforme'] = $socioInforme;
//            debug($lote);
            if(!$this->saveAll($lote)){
                parent::rollback();
                return false;
            }            

            parent::commit();
            
            return $lote['SocioInformeLote']['id'];
            
            
        }
        

        
    }
    
    
    
    public function generaExcelVeraz($datos){
        
        $oXLS = new GeneraXLS("veraz_".date('Ymd').".xls");
        $set = array();
        $set['labels'] = array(
            'A1' => 'APELLIDO',
            'B1' => 'NOMBRE',
            'C1' => 'DOCUMENTO',
            'D1' => 'TIPO DE OPERACION',
            'E1' => 'NUMERO DE OPERACION',
            'F1' => 'IMPORTE',
            'G1' => 'FECHA EN LA CUAL ENTRO EN MORA',
            'H1' => 'CALLE',
            'I1' => 'NUMERO',
            'J1' => 'PISO',
            'K1' => 'BARRIO',
            'L1' => 'LOCALIDAD',
            'M1' => 'PROVINCIA',
            'N1' => 'CP',
            'O1' => 'SEXO',
            'P1' => 'TELEFONO',
            'Q1' => 'EMAIL',
            'R1' => 'CELULAR',
            'S1' => 'CALIDAD',
            'T1' => 'CODIGO DE BARRA NUMERO',
        );
        $oXLS->prepareXLSSheet(0, $set,FALSE,10);
//        $oXLS->bolderColumnValue(array_keys($set['labels']));
        $oXLS->fillerColumnValue(array_keys($set['labels']),0,'FABD84');
//        $oXLS->verticalColumnValue(array_keys($set['labels']));
        if(!empty($datos)){
            $r = 2;
            foreach($datos as $dato){
                $oXLS->writeXLSCell($dato['apellido'],0,$r);
                $oXLS->writeXLSCell($dato['nombre'],1,$r);
                $oXLS->writeXLSCell($dato['documento'],2,$r);
                $oXLS->writeXLSCell($dato['tipo_ope'],3,$r);
                $oXLS->writeXLSCell($dato['nro_ope'],4,$r);
                $oXLS->writeXLSCell($dato['importe'],5,$r);
                $oXLS->writeXLSCell($dato['fecha_mora'],6,$r);
                $oXLS->writeXLSCell($dato['calle'],7,$r);
                $oXLS->writeXLSCell($dato['numero'],8,$r);
                $oXLS->writeXLSCell($dato['piso'],9,$r);
                $oXLS->writeXLSCell($dato['barrio'],10,$r);
                $oXLS->writeXLSCell($dato['localidad'],11,$r);
                $oXLS->writeXLSCell($dato['provincia'],12,$r);
                $oXLS->writeXLSCell($dato['codigo_postal'],13,$r);
                $oXLS->writeXLSCell($dato['sexo'],14,$r);
                $oXLS->writeXLSCell($dato['telefono'],15,$r);
                $oXLS->writeXLSCell($dato['e_mail'],16,$r);
                $oXLS->writeXLSCell($dato['celular'],17,$r);
                $oXLS->writeXLSCell("Titular",18,$r);
                $oXLS->writeXLSCell("",19,$r);
                $r++;
            }
            $oXLS->saveToXLSFile();
            return $oXLS->getXLSFileBuffer();
        }
        
    }
    
}

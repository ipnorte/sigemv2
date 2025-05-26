<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of informe_comerciales_controller
 *
 * @author adrian
 */
class InformeComercialesController extends MutualAppController {
    
    var $name = 'InformeComerciales';
    var $uses = array('pfyj.SocioInforme','pfyj.SocioInformeLote');
    
    function index(){
        
        $lotes = $this->SocioInformeLote->find('all');
        $this->set('lotes',$lotes);
        
    }
    
    function generar_informe(){
        
        $socios = NULL;
        $empresa = NULL;
        $periodo_corte = date('Ym');
        
        if(!empty($this->data)){
//            debug($this->data);
            $empresa = $this->data['SocioInforme']['empresa'];
            $periodo_corte = $this->data['SocioInforme']['periodo_corte']['year'].$this->data['SocioInforme']['periodo_corte']['month'];
            #cargar info para check
            $socios = $this->SocioInforme->getPendientes($empresa,$periodo_corte);
            
            if(isset($this->data['SocioInforme']['id'])){
//                debug($this->data);
                
                App::import('model','pfyj.SocioInformeLote');
                $oSIL = new SocioInformeLote();
                
                $lote_id = $oSIL->generarLote($this->data);
                if(!$lote_id){
                    $this->Mensaje->error("SE PRODUJO UN ERROR AL GENERAR EL LOTE");
                    $this->redirect('generar_informe');
                }else{
                    $this->redirect('index');
                }
            }
        }
        $this->set('periodo_corte',$periodo_corte);
        $this->set('socios',$socios);
        $this->set('empresa',$empresa);
        
    }
    
    function del($id = NULL){
        if(empty($id)) parent::noDisponible();
        
        App::import('model','pfyj.SocioInformeLote');
        $oSIL = new SocioInformeLote();
        
        if(!$oSIL->borrarLote($id)){
            $this->Mensaje->error("SE PRODUJO UN ERROR AL BORRAR EL LOTE");
                       
        }
        $this->redirect('index'); 
    }
    
    function download_lote_xls($id = NULL){
        
        if(empty($id)) parent::noDisponible();
        
        $lote = $this->SocioInformeLote->read(null,$id);
        Configure::write('debug',0);
        header("Content-type: application/vnd.ms-excel"); 
        $fileName = $id."_informe.xls";
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        echo $lote['SocioInformeLote']['lote'];
        exit;
    }
    
    
}

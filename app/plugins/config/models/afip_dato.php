<?php

class AfipDato extends ConfigAppModel{
	
    var $name = 'AfipDato';
    
    var $useTable = false;
        
//    var $hasMany = array();
    
    function getAfipDato() {
        $oGlobalDato = $this->importarModelo('GlobalDato', 'config');
        
	$values = $oGlobalDato->find('all',array('conditions' => array('GlobalDato.id LIKE ' => 'WEBSAFIP%', 'GlobalDato.id <> ' => 'WEBSAFIP'),'order' => array('GlobalDato.id')));

        $afipDato = array();
        foreach($values as $datos){
            if($datos['GlobalDato']['id'] === 'WEBSAFIPCERT') 
                $afipDato['AfipDato']['certificado']  = $datos['GlobalDato']['concepto_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPCLAV') 
                $afipDato['AfipDato']['clave'] = $datos['GlobalDato']['concepto_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPCPEM') 
                $afipDato['AfipDato']['pem'] = $datos['GlobalDato']['concepto_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPCUIT') 
                $afipDato['AfipDato']['cuit'] = (float) $datos['GlobalDato']['concepto_2'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPFACT') 
                $afipDato['AfipDato']['factura'] = $datos['GlobalDato']['entero_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPMODO') 
                $afipDato['AfipDato']['modo'] = $datos['GlobalDato']['entero_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPNCRE') 
                $afipDato['AfipDato']['credito'] = $datos['GlobalDato']['entero_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPNDEB') 
                $afipDato['AfipDato']['debito'] = $datos['GlobalDato']['entero_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPPVTA') 
                $afipDato['AfipDato']['punto'] = $datos['GlobalDato']['entero_1'];
        }

        return $afipDato;
    }

    function getAfipDatoFct() {
        $oGlobalDato = $this->importarModelo('GlobalDato', 'config');
        
	$values = $oGlobalDato->find('all',array('conditions' => array('GlobalDato.id LIKE ' => 'WEBSAFIP%', 'GlobalDato.id <> ' => 'WEBSAFIP'),'order' => array('GlobalDato.id')));

        $afipDato = array();
        $afipDatoFct = array();
        foreach($values as $datos){
            if($datos['GlobalDato']['id'] === 'WEBSAFIPCERT') 
                $afipDato['AfipDato']['certificado']  = $datos['GlobalDato']['concepto_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPCLAV') 
                $afipDato['AfipDato']['clave'] = $datos['GlobalDato']['concepto_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPCPEM') 
                $afipDato['AfipDato']['pem'] = $datos['GlobalDato']['concepto_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPCUIT'){ 
                $afipDato['AfipDato']['cuit'] = (float) $datos['GlobalDato']['concepto_2'];
                $afipDatoFct['razon_social'] = $datos['GlobalDato']['concepto_3'];
                $afipDatoFct['iva_responsable'] = $datos['GlobalDato']['concepto_4'];
                $afipDatoFct['inicio_actividad'] = $datos['GlobalDato']['fecha_1'];
                $afipDatoFct['ingreso_bruto'] = $datos['GlobalDato']['texto_1'];
            }
            if($datos['GlobalDato']['id'] === 'WEBSAFIPFACT'){
                $afipDatoFct['factura'] = array();
                $afipDato['AfipDato']['factura'] = $datos['GlobalDato']['entero_1'];
                $afipDatoFct['factura']['descripcion'] = $datos['GlobalDato']['concepto_2'];
                $afipDatoFct['factura']['letra'] = $datos['GlobalDato']['concepto_3'];
            }
            if($datos['GlobalDato']['id'] === 'WEBSAFIPMODO') 
                $afipDato['AfipDato']['modo'] = $datos['GlobalDato']['entero_1'];
            if($datos['GlobalDato']['id'] === 'WEBSAFIPNCRE'){
                $afipDatoFct['credito'] = array();
                $afipDato['AfipDato']['credito'] = $datos['GlobalDato']['entero_1'];
                $afipDatoFct['credito']['descripcion'] = $datos['GlobalDato']['concepto_2'];
                $afipDatoFct['credito']['letra'] = $datos['GlobalDato']['concepto_3'];
            }
            if($datos['GlobalDato']['id'] === 'WEBSAFIPNDEB'){
                $afipDatoFct['debito'] = array();
                $afipDato['AfipDato']['debito'] = $datos['GlobalDato']['entero_1'];
                $afipDatoFct['debito']['descripcion'] = $datos['GlobalDato']['concepto_2'];
                $afipDatoFct['debito']['letra'] = $datos['GlobalDato']['concepto_3'];
            }
            if($datos['GlobalDato']['id'] === 'WEBSAFIPPVTA') 
                $afipDato['AfipDato']['punto'] = $datos['GlobalDato']['entero_1'];
        }
        $afipDato['documento'] = $afipDatoFct;

        return $afipDato;
    }

}
?>
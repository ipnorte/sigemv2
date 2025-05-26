<?php
class AsincronoTemporal extends ShellsAppModel{
	
	var $name = 'AsincronoTemporal';
	var $auditable = false;
	var $hasMany = array('AsincronoTemporalDetalle' => array('dependent' => true));
	
	function getTemporalesByAsincId($asincrono_id){
		
	}
	
    function leerTemporal($asincrono_id,$fields=array(),$order=array(),$clave_1 = NULL,$clave_2 = NULL,$clave_3 = NULL,$clave_4 = NULL,$clave_5 = NULL){
        $this->unbindModel(array('hasMany' => array('AsincronoTemporalDetalle')));
        $conditions = array();
        $conditions['AsincronoTemporal.asincrono_id'] = $asincrono_id;
        if(!empty($clave_1)){$conditions['AsincronoTemporal.clave_1'] = $clave_1;}
        if(!empty($clave_2)){$conditions['AsincronoTemporal.clave_2'] = $clave_2;}
        if(!empty($clave_3)){$conditions['AsincronoTemporal.clave_3'] = $clave_3;}
        if(!empty($clave_4)){$conditions['AsincronoTemporal.clave_4'] = $clave_4;}
        if(!empty($clave_5)){$conditions['AsincronoTemporal.clave_5'] = $clave_5;}
        if(!empty($fields)){
            if(!array_key_exists('clave_1',$fields)){array_push($fields,'clave_1');}
            if(!array_key_exists('clave_2',$fields)){array_push($fields,'clave_2');}
            if(!array_key_exists('clave_3',$fields)){array_push($fields,'clave_3');}
            if(!array_key_exists('clave_4',$fields)){array_push($fields,'clave_4');}
            if(!array_key_exists('clave_5',$fields)){array_push($fields,'clave_5');}
            
        }
        // $datos = $this->find('all',array('conditions' => $conditions,'fields' => $fields,'order' => array('clave_1','clave_2','clave_3','clave_4','clave_5')));
        $datos = $this->find('all',array('conditions' => $conditions,'fields' => $fields,'order' => $order));
        return $datos;
    }

}
?>
<?php
class Localidad extends ConfigAppModel{
	
	var $name = "Localidad";

    var $validate = array(
						'nombre' => array( 
    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
    									)
    					); 		
	
    var $belongsTo = array(
        'Provincia' => array(
            'className'    => 'Provincia',
            'foreignKey'    => 'provincia_id'
        )
    );

    
    function save($data = null, $validate = true, $fieldList = array()){
        if(MODULO_V1){
                App::import('Model', 'V1.LocalidadV1');
                $this->LocalidadV1 = new LocalidadV1(null);	
                if($this->LocalidadV1->save($data)){
                        $data['Localidad']['idr'] = $this->LocalidadV1->getLastInsertID();
                }
        }
        App::import('Model', 'Config.Provincia');
        $this->Provincia = new Provincia(null);
        $prv = $this->Provincia->read('letra', $data['Localidad']['provincia_id']);
        $data['Localidad']['letra_provincia'] = $prv['Provincia']['letra'];	
        return parent::save($data);   	
    }
    
    function getByProvinciaAndCP($provinciaId,$cp){
        $datos = $this->find('all',array('conditions' => array(
            'Localidad.provincia_id' => $provinciaId,
            'Localidad.cp' => trim($cp)
        ),
        'limit' => 1));
        if(!empty($datos)){
            return $datos[0];
        }else{
            return NULL;
        }
    }
    
	
}
?>
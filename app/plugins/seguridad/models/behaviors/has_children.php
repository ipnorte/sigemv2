<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package seguridad
 * @subpackage model
 */
class HasChildrenBehavior extends ModelBehavior{
    
	function setup(&$model) {
        $this->model = $model;
    }

    function beforeDelete(){
        if(isset($this->model->hasMany)){
            foreach($this->model->hasMany as $key=>$value){
                $childRecords = $this->model->{$key}->find('count', array('conditions'=>array($value['foreignKey']=>$this->model->id)));
                if($childRecords > 0){
                    return false;
                }
            }
        }
        //Checking habtm relation as well, thanks to Zoltan
        if(isset($this->model->hasAndBelongsToMany)){
            foreach($this->model->hasAndBelongsToMany as $key=>$value){
                $childRecords = $this->model->{$key}->find('count', array('conditions'=>array($value['foreignKey']=>$this->model->id)));
                if($childRecords > 0){
                    return false;
                }
            }
        }
        return true;
    }    
    
}
?>
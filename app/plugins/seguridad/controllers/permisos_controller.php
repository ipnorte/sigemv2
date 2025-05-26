<?php
class PermisosController extends SeguridadAppController {  
	
	var $name = 'Permisos';  

	function beforeFilter(){  
		$this->Seguridad->allow('prueba','llamador');
		parent::beforeFilter();  
	}	
	
	function index(){}
	
	function opcionesMenu($grupo=0,$parent=0,$quick=false){
		$this->Permiso->bindModel(array('hasAndBelongsToMany' => array('Grupo')));
        if(!empty($grupo))$menuGeneral = $this->Permiso->findAll("activo = '1' and main = '1' and parent = $parent and id in (select permiso_id from grupos_permisos where grupo_id = $grupo)","Permiso.*",($parent == 0 ? "order" : "descripcion"));
        else $menuGeneral = $this->Permiso->findAll("activo = '1' and main = '1' and parent = $parent and id in (select permiso_id from grupos_permisos)","Permiso.*",($parent == 0 ? "order" : "descripcion"));
        $menuRapido = $this->Permiso->findAll("activo = '1' and quick = '1' and id in (select permiso_id from grupos_permisos where grupo_id = $grupo)","Permiso.*","order");
        $RESET_PASSWORD = $this->Seguridad->user('reset_password');
        //if($RESET_PASSWORD == 1) $menuGeneral = null;
        return (!$quick ? $menuGeneral : $menuRapido);

	}
	
	
}
?>
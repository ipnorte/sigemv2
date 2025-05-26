<?php

class Grupo extends SeguridadAppModel {

	var $name = 'Grupo';
//	var $recursive = 0;
	var $displayField = 'nombre';
	
//	var $actsAs = array('Seguridad.ExtendAssociations','Seguridad.HasChildren'); 
	var $actsAs = array('Seguridad.ExtendAssociations'); 
	
    var $validate = array(
						'nombre' => array( 
    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => 'Debe indicar un nombre de Grupo'),
    										'alphaNumeric' => array('rule' => array('checkUserName'),'message' => 'El nombre de Grupo ya existe!'),
    									)	
    					); 	
	

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
			'Usuario' => array('className' => 'Usuario',
								'foreignKey' => 'grupo_id',
								'dependent' => false,
								'conditions' => '',
								'fields' => '',
								'order' => '',
								'limit' => '',
								'offset' => '',
								'exclusive' => '',
								'finderQuery' => '',
								'counterQuery' => ''
			)
	);

	var $hasAndBelongsToMany = array(
			'Permiso' => array('className' => 'Permiso',
						'joinTable' => 'grupos_permisos',
						'foreignKey' => 'grupo_id',
						'associationForeignKey' => 'permiso_id',
						'unique' => true,
						'conditions' => '',
						'fields' => '',
						'order' => 'Permiso.order',
						'limit' => '',
						'offset' => '',
						'finderQuery' => '',
						'deleteQuery' => '',
						'insertQuery' => ''
			)
	);
	
	/**
	 * checkUserName
	 * verifica que un usuario no exista
	 * @param unknown_type $data
	 * @return unknown
	 */
    function checkUserName($data){
        return $this->isUnique(array('nombre' => $data['nombre']));
	}	
	
	/**
	 * nombre
	 * devuelve el nombre del grupo
	 * @return nombre
	 */
	function nombre(){
		$thisGrupo = $this->read(null,$this->id);
		return $thisGrupo['Grupo']['nombre'];
	}
	
	/**
	 * permisosHabilitados
	 * devuelve los permisos habilitados para este grupo
	 * @return permisos
	 */
	function permisosHabilitados(){
		$permisos = $this->find(array('Grupo.id'=>$this->id),null,null,2);
		return $permisos['Permiso'];
	}
	
	/**
	 * actions
	 * Verifica si el grupo puede consultar, agregar, modificar o borrar
	 */
	function actions(){
		$actions = array();
		$grupo = $this->find(array('Grupo.id'=>$this->id),null,null,0);
		$actions['index'] = $grupo['Grupo']['consultar'];
		$actions['view'] = $grupo['Grupo']['vista'];
		$actions['add'] = $grupo['Grupo']['agregar'];
		$actions['edit'] = $grupo['Grupo']['modificar'];
		$actions['del'] = $grupo['Grupo']['borrar'];
		return $actions;		
	}
	
	
	/**
	 * permisosDenegados
	 * devuelve los permisos denegados
	 * @return permisos
	 */
	function permisosDenegados(){
		$this->GruposPermiso->bindModel(array('belongsTo' => array('Grupo', 'Permiso')));
		$habilitados = $this->permisosHabilitados();
		$idHabilitados = Set::extract($habilitados,'{n}.id');
		$condiciones = array('Permiso.activo' => '=1','NOT'=>array('Permiso.id'=>$idHabilitados));
		return $this->Permiso->findAll($condiciones,null,"Permiso.order");
	}
	
	/**
	 * usuarios
	 * devuelve los usuarios de un grupo
	 * @return unknown
	 */
	function usuarios(){
		$this->bindModel(array('hasMany' => array('Usuario')));
		return  $this->Usuario->findAll(array('Usuario.grupo_id' => $this->id,'Usuario.usuario <>' => 'admin'),null,"Usuario.usuario");
	}
	
	/**
	 * denegarPermiso
	 * Denegar un permiso dado o todos los permisos
	 * @param unknown_type $permiso_id
	 */
	function denegarPermiso($permiso_id = null){
		if(!empty($permiso_id))$this->habtmDelete('Permiso',$this->id,$permiso_id);
		else $this->habtmDeleteAll('Permiso',$this->id); 
	}
	
	/**
	 * establecerPermiso
	 * Establece un permiso en particular o todos
	 * @param unknown_type $permiso_id
	 */
	function establecerPermiso($permiso_id=null){
		if(!empty($permiso_id)){
			//deniego el permiso
			$this->denegarPermiso($permiso_id);
			$this->habtmAdd('Permiso',$this->id,$permiso_id); 			
		}else{
			//deniego todos los permisos
			$this->denegarPermiso();
			$permisos = Set::extract($this->permisosDenegados(),'{n}.Permiso.id');
			$this->habtmAdd('Permiso',$this->id,$permisos); 
		}
	}

	/**
	 * replicarPermisos
	 * Replica los permisos de un grupo dado por parametro al grupo actual
	 * @param unknown_type $grupo
	 */
	function replicarPermisos($fromGrupo){
		$permisos = $this->find(array('Grupo.id'=>$fromGrupo));
		$permisos = $permisos['Permiso'];
		$permisos = Set::extract($permisos,'{n}.id');
		$this->habtmAdd('Permiso',$this->id,$permisos); 
	}
	
	function getGrupoVendedores(){
		$sql = "SELECT gr.id FROM grupos gr
				INNER JOIN usuarios us ON (gr.id = us.grupo_id)
				INNER JOIN vendedores ve ON (ve.usuario_id = us.id)
				GROUP BY gr.id ORDER BY gr.created DESC LIMIT 1;";
		$datos = $this->query($sql);
		if(isset($datos[0]['gr']['id']) && !empty($datos[0]['gr']['id']) ){
			return $datos[0]['gr']['id'];
		}else{
			$sql = "SELECT * FROM grupos gr WHERE nombre = 'VENDEDORES'";
			$datos = $this->query($sql);
			if(!empty($datos)){
				if(isset($datos[0]['gr']['id']) && !empty($datos[0]['gr']['id'])) return $datos[0]['gr']['id'];
			}
			$grupo = array();
			$grupo['Grupo']['id'] = 0;
			$grupo['Grupo']['nombre'] = 'VENDEDORES';
			$grupo['Grupo']['activo'] = 1;
			if($this->save($grupo)){
				return $this->getLastInsertID();
			}else{
				return 0;
			}
		}
	}	

    
    function get_permisos($grupo_id){
        $sql = "select id,descripcion,url,main,parent,`order`,icon,
                (select count(*) from grupos_permisos where grupo_id = $grupo_id and permiso_id = Permiso.id) as habilitado 
                from permisos as Permiso where activo = 1 
                order by `order`,parent;";
        $datos =  $this->query($sql);
        return $datos;
    }
    
    
}
?>
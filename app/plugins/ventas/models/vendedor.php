<?php
class Vendedor extends VentasAppModel{
	
	var $name = 'Vendedor';
	var $belongsTo = array('Persona','Usuario');
	
	function getByPersonaId($persona_id){
            $vendedor = $this->find('all',array('conditions' => array('Vendedor.persona_id' => $persona_id),'limit' => 1));
            return $vendedor;
	}
        
        function suspender($vendedor_id,$habilitado = 1){
            $this->unbindModel(array('belongsTo'=> array('Persona','Usuario')));
            $vendedor = $this->read(null,$vendedor_id);
            $vendedor['Vendedor']['activo'] = $habilitado;
            if(!$this->save($vendedor)) return false;
            App::import('model','seguridad.usuario');
            $oUsuario = new Usuario(); 
            $usuario = $oUsuario->read(null,$vendedor['Vendedor']['usuario_id']);
            $usuario['Usuario']['activo'] = $habilitado;
            return $oUsuario->save($usuario);
        }
        
        function get_persona_by_cuit($cuit_cuil){
            App::import('model','pfyj.persona');
            $oPERSONA = new Persona(); 
            $persona = $oPERSONA->getByCUIT($cuit_cuil, true);
//            if(!empty($persona)) $persona = $persona[0];
            return $persona;
        }
                
	
        function alta_nueva($persona_id){
            
            if(empty($persona_id)) return false;
            App::import('model','pfyj.persona');
            $oPERSONA = new Persona(); 
            $persona = $oPERSONA->read(null,$persona_id);
            
            if(empty($persona)){
               parent::notificar("NO EXISTE LA PERSONA"); 
               return false;
            }
            
            App::import('model','seguridad.grupo');
            $oGRUPO = new Grupo();
            
            $GRUPO_ID = $oGRUPO->getGrupoVendedores();
            if($GRUPO_ID == 0){
                parent::notificar("NO EXISTE GRUPO CREADO PARA VENDEDORES");
                return false;
            }
            
            App::import('model','seguridad.usuario');
            $oUsuario = new Usuario();
            
            $usuario = array();
            $usuario['Usuario']['id'] = 0;
            $usuario['Usuario']['grupo_id'] = $GRUPO_ID;
            $usuario['Usuario']['usuario'] = $persona['Persona']['cuit_cuil'];
            $pws = Security::hash($persona['Persona']['cuit_cuil'], null, true);
            $usuario['Usuario']['password'] = $pws;
            $usuario['Usuario']['activo'] = 1;
            $usuario['Usuario']['descripcion'] = $persona['Persona']['nombre']." " . $persona['Persona']['apellido'];
            $usuario['Usuario']['email'] = trim($persona['Persona']['e_mail']);
            
            if(empty($usuario['Usuario']['email'])){
                parent::notificar("La persona debe contar con una dirección de EMAIL válida");
                parent::rollback();
                return false;                
            }
            

            parent::begin();
            
            if(!$oUsuario->save($usuario)){
                parent::notificar("ERROR AL CREAR EL USUARIO");
                parent::rollback();
                return false;
            }
            $usuarioID = $oUsuario->getLastInsertID();
            
            $nvendedor = array();
            $nvendedor['Vendedor']['id'] = 0;
            $nvendedor['Vendedor']['persona_id'] = $persona_id;
            $nvendedor['Vendedor']['usuario_id'] = $usuarioID;  
            
            if(!parent::save($nvendedor)){
                parent::notificar("ERROR AL CREAR EL VENDEDOR");                
                parent::rollback();
                return false;                
            }
            $vendedorId = $this->getLastInsertID();
            $usuario['Usuario']['id'] = $usuarioID;
            $usuario['Usuario']['vendedor_id'] = $vendedorId;
            if($oUsuario->save($usuario)){
                    parent::commit();
                    return $vendedorId;
            }else{
                parent::notificar("ERROR AL VINCULAR USUARIO -> VENDEDOR");
                parent::rollback();
                return null;
            }            
            
        }
        
	function alta($datos){
		
		App::import('model','pfyj.persona');
		$oPERSONA = new Persona();
		
		parent::begin();
		
		$personaID = $oPERSONA->alta($datos);
		
		if(!empty($personaID)){
			
			$vendedor = $this->getByPersonaId($personaID);
			
			if(empty($vendedor)){
				
				App::import('model','seguridad.grupo');
				$oGRUPO = new Grupo();				
				
				$GRUPO_ID = $oGRUPO->getGrupoVendedores();

				if($GRUPO_ID == 0){
					parent::rollback();
					return null;
				}

				App::import('model','seguridad.usuario');
				$oUsuario = new Usuario();	

				$usuario = array();
				$usuario['Usuario']['id'] = 0;
				$usuario['Usuario']['grupo_id'] = $GRUPO_ID;
				$usuario['Usuario']['usuario'] = $datos['Persona']['cuit_cuil'];
				$pws = Security::hash($datos['Persona']['cuit_cuil'], null, true);
				$usuario['Usuario']['password'] = $pws;
				$usuario['Usuario']['activo'] = 1;
				$usuario['Usuario']['descripcion'] = $datos['Persona']['nombre']." " . $datos['Persona']['apellido'];

				if(!$oUsuario->save($usuario)){
					$this->rollback();
					return null;					
				}
				
				$usuarioID = $oUsuario->getLastInsertID();
				
				$nvendedor = array();
				$nvendedor['Vendedor']['id'] = 0;
				$nvendedor['Vendedor']['persona_id'] = $personaID;
				$nvendedor['Vendedor']['usuario_id'] = $usuarioID;
				
				if(parent::save($nvendedor)){
					
					$vendedorId = $this->getLastInsertID();
					$usuario['Usuario']['id'] = $usuarioID;
					$usuario['Usuario']['vendedor_id'] = $vendedorId;
					if($oUsuario->save($usuario)){
						
						parent::commit();
						return $vendedorId;
					}else{
						parent::rollback();
						return null;
					}
				}else{			
					parent::rollback();
					return null;
				}
			}else{
				return $vendedor['Vendedor']['id'];
			}	
		}else{
				parent::rollback();
				return null;
		}
	}
	
	function getVendedores($toList = false,$soloActivos = FALSE){
            
            App::import('model','seguridad.Usuario');
            $this->oUSER = new Usuario();             
            $vendedorId = $this->oUSER->get_vendedorId_logon();

            
            $this->recursive = 3;
            $this->Usuario->bindModel(array('belongsTo' => array('Grupo')));
            $conditions = array();
            if(!empty($vendedorId)){
                $conditions['Vendedor.id'] = $vendedorId;
            }
            if($soloActivos){
                $conditions['Usuario.activo'] = 1;
            }
            $vendedores = $this->find('all',array('conditions' => $conditions,'order' => 'Persona.apellido,Persona.nombre'));
//            exit;
//            $vendedor = $this->find('all',array('conditions' => array('Vendedor.persona_id' => $persona_id),'limit' => 1));
//            $vendedores = null;
//            if(empty($vendedorId)){
//                $vendedores = $this->find('all',array('order' => 'Persona.apellido,Persona.nombre'));
//            }else{
//                $vendedores = $this->find('all',array('conditions' => array('Vendedor.id' => $vendedorId),'order' => 'Persona.apellido,Persona.nombre'));
//            }
            if(!empty($vendedores)){
                    foreach($vendedores as $i => $vendedor){
                            $vendedores[$i] = $this->armaDatos($vendedor);
                    }
                    if($toList){
                            $lista = array();
                            foreach($vendedores as $i => $vendedor){
                                    $lista[$vendedor['Vendedor']['id']] = $vendedor['Persona']['tdoc_ndoc_apenom'];
                            }
                            return $lista;
                    }
            }
            return $vendedores;
	}
	
	function armaDatos($dato){
		$usuario = array();
		App::import('model','pfyj.persona');
		$oPERSONA = new Persona();
		$usuario = $oPERSONA->__armaDatos($dato);
		return $usuario;
	}
	
	function getVendedor($id){
		$this->recursive = 3;
		$this->Usuario->bindModel(array('belongsTo' => array('Grupo')));
		$vendedor = $this->read(null,$id);
		return $this->armaDatos($vendedor);
	}
	
	function cargarComision($data){
		App::import('model','ventas.vendedor_proveedor_plan');
		$oVENDEDORPLAN = new VendedorProveedorPlan();
		return $oVENDEDORPLAN->guardarComision($data);
	}
	
	function getPlanesDisponibles($id){
		App::import('model','ventas.vendedor_proveedor_plan');
		$oVENDEDORPLAN = new VendedorProveedorPlan();
		return $oVENDEDORPLAN->getPlanesDisponibles($id);		
	}
	
	function getPlanes($id){
		App::import('model','ventas.vendedor_proveedor_plan');
		$oVENDEDORPLAN = new VendedorProveedorPlan();
		return $oVENDEDORPLAN->getPlanes($id);		
	}
	
	function getComision($planId){
		App::import('model','ventas.vendedor_proveedor_plan');
		$oVENDEDORPLAN = new VendedorProveedorPlan();
		return $oVENDEDORPLAN->getPlanComision($planId);		
	}
	
	function borrarComision($id){
		App::import('model','ventas.VendedorProveedorPlan');
		$oVENDEDORPLAN = new VendedorProveedorPlan();
		return $oVENDEDORPLAN->borrar($id);
	}
	
	function getSolicitudes($vendedor_id){
		
		App::import('model','mutual.MutualProductoSolicitud');
		$oSOLICITUD = new MutualProductoSolicitud();	

		return $oSOLICITUD->getSolicitudesByVendedor($vendedor_id,array('MUTUESTA0001','MUTUESTA0004'));
		
	}
	
	
}
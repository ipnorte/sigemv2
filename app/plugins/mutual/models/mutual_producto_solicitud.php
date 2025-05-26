<?php


/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class MutualProductoSolicitud extends MutualAppModel{
	var $name = 'MutualProductoSolicitud';
	
	var $belongsTo = array('Socio','MutualProducto','ProveedorPlan');
	var $hasMany = array('MutualProductoSolicitudPago','MutualProductoSolicitudCancelacion','MutualProductoSolicitudInstruccionPago','MutualProductoSolicitudEstado');
	
//    var $validate = array(
//						'importe_solicitado' => array( 
//    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
//    									),
//						'importe_percibido' => array( 
//    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
//    									),    
//						'importe_cuota' => array( 
//    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
//    									),
//						'cuotas' => array( 
//    										VALID_NOT_EMPTY => array('rule' => VALID_NOT_EMPTY,'message' => '(*)Requerido')
//    									),
//    				); 

    				
    				
    				
//    function save($data = null, $validate = true, $fieldList = array()){
//    	$data = $this->__setDatos($data);
//    	$ret = parent::save($data);
//    	return $ret;
//    }
    
    function getOrden($id){
    	
    	$orden = $this->read(null,$id);
    	return $orden;
    }
    
    function getOrdenByPersonaAndId($persona_id,$id){
		$this->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
		$solicitudes = $this->find('all',array('conditions' => array('MutualProductoSolicitud.id' => $id,'MutualProductoSolicitud.persona_id' => $persona_id),'order'=>'MutualProductoSolicitud.created DESC'));
        if(isset($solicitudes[0])) return $solicitudes[0];
        else return null;
    }
    
    
    /**
     * devuelve las solicitudes no anuladas de un socio
     * @param $socio_id
     */
    function getOrdenesBySocio($socio_id,$cargaOrdenDto=false){
		$this->recursive = 4;
		$this->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
		$solicitudes = $this->find('all',array('conditions' => array('MutualProductoSolicitud.socio_id' => $socio_id,'MutualProductoSolicitud.anulada' => 0),'order'=>'MutualProductoSolicitud.created DESC'));
    	foreach($solicitudes as $idx => $solicitud){
    		$solicitud = $this->armaDatos($solicitud,$cargaOrdenDto);
    		$solicitudes[$idx] = $solicitud;
    	}
		return $solicitudes;
    }
    
    /**
     * Ordenes de Consumo por persona
     * @param $persona_id
     * @param $cargaOrdenDto
     */
    function getOrdenesByPersona($persona_id,$cargaOrdenDto=false,$anuladas=false){
        $sql = "SELECT MutualProductoSolicitud.*
                FROM mutual_producto_solicitudes AS MutualProductoSolicitud
                WHERE MutualProductoSolicitud.persona_id = $persona_id 
                AND MutualProductoSolicitud.anulada = ".($anuladas ? 1 : 0)." 
                ".(MODULO_V1 ? " AND MutualProductoSolicitud.tipo_orden_dto <> '".Configure::read('APLICACION.tipo_orden_dto_credito')."' " : "")."    
                ORDER BY MutualProductoSolicitud.created DESC";
        $solicitudes = $this->query($sql);
        if(!empty($solicitudes)){
            foreach($solicitudes as $idx => $solicitud){
                    $solicitud = $this->armaDatos($solicitud,$cargaOrdenDto);
                    $solicitudes[$idx] = $solicitud;
            }            
        }
        
        
//        debug($solicitudes);
//        exit;
        
//        $this->recursive = 4;
//        $this->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
//        $conditions = array();
//        $conditions['MutualProductoSolicitud.persona_id'] = $persona_id;
//        $conditions['MutualProductoSolicitud.anulada'] = ($anuladas ? 1 : 0);
//        if(MODULO_V1){
//                $conditions['MutualProductoSolicitud.tipo_orden_dto <>'] = Configure::read('APLICACION.tipo_orden_dto_credito');
//        }
//        $solicitudes = $this->find('all',array('conditions' => $conditions,'order'=>'MutualProductoSolicitud.created DESC'));
//        foreach($solicitudes as $idx => $solicitud){
//    		$solicitud = $this->armaDatos($solicitud,$cargaOrdenDto);
//    		$solicitudes[$idx] = $solicitud;
//    	}
	return $solicitudes;
    }

    

    
    
    /**
     * devuelve las ordenes anuladas por socio
     * @param $socio_id
     */
    function getOrdenesAnuladasBySocio($socio_id){
        $this->recursive = 4;
        $this->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
        $solicitudes = $this->find('all',array('conditions' => array('MutualProductoSolicitud.socio_id' => $socio_id,'MutualProductoSolicitud.anulada' => 1),'order'=>'MutualProductoSolicitud.created DESC'));
    	return $solicitudes;
    }    
    
    function getImporteTotal($id){
    	$sol = $this->read('importe_total',$id);
    	return $sol['MutualProductoSolicitud']['importe_total'];
    }
    
    function isAprobada($id){
    	$sol = $this->read('aprobada',$id);
//        debug($sol);
    	return ($sol['MutualProductoSolicitud']['aprobada'] == 1 ? true : false);
    }
    
    function getBySocioNoAprobadas($socio_id){
    	$solicitudes = $this->find('all',array('conditions' => array('MutualProductoSolicitud.socio_id' => $socio_id,'MutualProductoSolicitud.aprobada' => 0, 'MutualProductoSolicitud.anulada' => 0),'order'=>'MutualProductoSolicitud.created DESC'));
    	return $solicitudes;
    }
    
    function getNoAprobadas(){
    	$solicitudes = $this->find('all',array('conditions' => array('MutualProductoSolicitud.aprobada' => 0,'MutualProductoSolicitud.estado' => 'MUTUESTA0002', 'MutualProductoSolicitud.anulada' => 0),'order'=>'MutualProductoSolicitud.created DESC'));
    	foreach($solicitudes as $idx => $solicitud){
    		$solicitudes[$idx] = $this->armaDatos($solicitud);
    	}
    	return $solicitudes;
    } 
    
    function getNoAprobadas2($estado = 'MUTUESTA0002',$nroSolSearch = NULL,$nDocSearch = NULL, $apeSearch = NULL, $nomSearch = NULL){

        $sql = "select * from ((select 
                MutualProductoSolicitud.id
                ,MutualProductoSolicitud.fecha
                ,MutualProductoSolicitud.tipo_orden_dto
                ,concat(MutualProductoSolicitud.tipo_orden_dto,' #',MutualProductoSolicitud.id) as tipo_numero
                ,MutualProductoSolicitud.periodo_ini
                ,MutualProductoSolicitud.importe_total
                ,MutualProductoSolicitud.cuotas
                ,MutualProductoSolicitud.importe_cuota
                ,MutualProductoSolicitud.permanente
                ,MutualProductoSolicitud.sin_cargo
                ,Persona.id as persona_id
                ,Persona.documento
                ,Persona.apellido
                ,Persona.nombre
                ,concat('DNI ',Persona.documento,' - ',Persona.apellido,', ',Persona.nombre) as beneficiario
                ,concat(Persona.apellido,', ',Persona.nombre) as beneficiario_apenom
                ,concat('DNI ',Persona.documento) as beneficiario_tdoc_ndoc
                ,MutualProductoSolicitud.socio_id
                ,TipoProducto.concepto_1 as tipo_producto
                ,TipoProducto.concepto_3 as tipo
                ,Proveedor.razon_social
                ,concat(if(ifnull(Proveedor.razon_social_resumida,'') = '',Proveedor.razon_social,Proveedor.razon_social_resumida),' - ',TipoProducto.concepto_1) as proveedor_producto
                ,Organismo.concepto_1 as organismo
                ,Empresa.concepto_1 as empresa
                ,PersonaBeneficio.cbu
                ,concat(Organismo.concepto_1,' - ' ,ifnull(Empresa.concepto_1,''),' | ',ifnull(PersonaBeneficio.cbu,'')) as beneficio_str
                ,MutualProductoSolicitud.user_created
                ,MutualProductoSolicitud.created
                ,concat(MutualProductoSolicitud.user_created,'-',MutualProductoSolicitud.created) as emitida_por
                ,concat('#',Vendedor.id, ' - ',VendedorPersona.documento,' - ',VendedorPersona.apellido, ', ',VendedorPersona.nombre) as vendedor_nombre
                from 
                mutual_producto_solicitudes MutualProductoSolicitud
                inner join personas Persona on Persona.id = MutualProductoSolicitud.persona_id
                inner join global_datos TipoProducto on TipoProducto.id = MutualProductoSolicitud.tipo_producto
                inner join proveedores Proveedor on Proveedor.id = MutualProductoSolicitud.proveedor_id
                inner join persona_beneficios PersonaBeneficio on PersonaBeneficio.id = MutualProductoSolicitud.persona_beneficio_id
                inner join global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                left join global_datos Empresa on Empresa.id = PersonaBeneficio.codigo_empresa
                LEFT JOIN vendedores Vendedor on (Vendedor.id = MutualProductoSolicitud.vendedor_id)
                LEFT JOIN personas VendedorPersona on (VendedorPersona.id = Vendedor.persona_id)
                where
                MutualProductoSolicitud.aprobada = 0
                and MutualProductoSolicitud.estado = '$estado'
                and MutualProductoSolicitud.anulada = 0)
                union
                (select
                MutualServicioSolicitud.id
                ,MutualServicioSolicitud.fecha_emision
                ,MutualServicio.tipo_orden_dto
                ,concat(MutualServicio.tipo_orden_dto,' #',MutualServicioSolicitud.id) as tipo_numero
                ,MutualServicioSolicitud.periodo_desde
                ,MutualServicioSolicitud.importe_mensual
                ,0
                ,MutualServicioSolicitud.importe_mensual
                ,1
                ,0
                ,Persona.id as persona_id
                ,Persona.documento
                ,Persona.apellido
                ,Persona.nombre
                ,concat('DNI ',Persona.documento,' - ',Persona.apellido,', ',Persona.nombre) as beneficiario
                ,concat(Persona.apellido,', ',Persona.nombre) as beneficiario_apenom
                ,concat('DNI ',Persona.documento) as beneficiario_tdoc_ndoc
                ,MutualServicioSolicitud.socio_id
                ,TipoProducto.concepto_1 as tipo_producto
                ,TipoProducto.concepto_3 as tipo
                ,Proveedor.razon_social
                ,concat(if(ifnull(Proveedor.razon_social_resumida,'') = '',Proveedor.razon_social,Proveedor.razon_social_resumida),' - ',TipoProducto.concepto_1) as proveedor_producto
                ,Organismo.concepto_1 as organismo
                ,Empresa.concepto_1 as empresa
                ,PersonaBeneficio.cbu
                ,concat(Organismo.concepto_1,' - ' ,ifnull(Empresa.concepto_1,''),' | ',ifnull(PersonaBeneficio.cbu,'')) as beneficio_str
                ,MutualServicioSolicitud.user_created
                ,MutualServicioSolicitud.created
                ,concat(MutualServicioSolicitud.user_created,'-',MutualServicioSolicitud.created) as emitida_por
                ,''
                from mutual_servicio_solicitudes MutualServicioSolicitud
                inner join mutual_servicios MutualServicio on MutualServicio.id = MutualServicioSolicitud.mutual_servicio_id
                inner join global_datos TipoProducto on TipoProducto.id = MutualServicio.tipo_producto
                inner join proveedores Proveedor on Proveedor.id = MutualServicio.proveedor_id
                inner join personas Persona on Persona.id = MutualServicioSolicitud.persona_id
                inner join persona_beneficios PersonaBeneficio on PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id
                inner join global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                left join global_datos Empresa on Empresa.id = PersonaBeneficio.codigo_empresa
                where MutualServicioSolicitud.aprobada = 0)
                
                order by id,apellido,nombre,fecha) as solicitud 
                where 
                    id like '".$nroSolSearch."%'
                    and documento like '".$nDocSearch."%'  
                    and apellido like '".$apeSearch."%' 
                    and nombre like '".$nomSearch."%' ;";
        
        
//        debug($sql);
        $solicitudes = $this->query($sql);
//        debug($solicitudes);
        return $solicitudes;
    }
    
    function nueva($data){
		if(!isset($data['MutualProductoSolicitud']['tipo_producto_mutual_producto_id'])) return false;
    	$data = $this->__setDatos($data);

		$data = $this->__chequearSocio($data);
		
		if(!isset($data['MutualProductoSolicitud']['proveedor_id'])) return false;
		if(!isset($data['MutualProductoSolicitud']['mutual_producto_id'])) return false;
		if(!isset($data['MutualProductoSolicitud']['persona_beneficio_id'])) return false;
    	if($data['MutualProductoSolicitud']['actualizar_cuota_social'] == 1 && $data['MutualProductoSolicitud']['socio_id'] != 0 ){
    		if(!$this->__actualizarCuotaSocial($data)) return false;
    	}
    	
    	$codigoEstado = parent::GlobalDato("concepto_2", "MUTUESTA0001");
    	$codigoEstado = (empty($codigoEstado) ? "MUTUESTA0001" : $codigoEstado);
    	
    	$data['MutualProductoSolicitud']['estado']  = $codigoEstado;

        parent::begin();
        
        if(!parent::save($data)){
            parent::rollback();
            return false;
        }
        $data['MutualProductoSolicitud']['id'] = $this->getLastInsertId();
    	$this->guardarHistorial($data);

        return parent::commit();

    }
    /*
     * Anular Solicitud de Consumo o Servicio
     */
    function anular($id){

	$orden = $this->read(null,$id);
        $this->begin();

        App::import('Model','Mutual.OrdenDescuento');
        $oODTO = new OrdenDescuento();

        $mensaje = "POR ANULACION DE LA ORDEN DE CONSUMO #$id";

        if(!empty($orden['MutualProductoSolicitud']['orden_descuento_id'])){
            $ordenDto = $this->get_orden_descuento_emitida($id,$orden,'orden_descuento_id');
            if(!$oODTO->anularOrden($ordenDto['OrdenDescuento']['id'],$mensaje)){
                    $this->rollback();
                    parent::notificar("ERROR AL ANULAR LA ORDEN DE DESCUENTO");
                    return false;
            }
        }
        if(!empty($orden['MutualProductoSolicitud']['orden_descuento_seguro_id'])){
            $ordenDto = $this->get_orden_descuento_emitida($id,$orden,'orden_descuento_seguro_id');
            if(!$oODTO->anularOrden($ordenDto['OrdenDescuento']['id'],$mensaje)){
                    $this->rollback();
                    parent::notificar("ERROR AL ANULAR LA ORDEN DE SEGURO");
                    return false;
            }
        }

        $orden['MutualProductoSolicitud']['estado'] = 'MUTUESTA0000';
        $orden['MutualProductoSolicitud']['anulada'] = 1;
        if(!$this->save($orden)){
                parent::notificar("ERROR AL ANULAR LA SOLICITUD");
                $this->rollback();
                return false;			
        }
        
        
        App::import('model','Mutual.CancelacionOrden');
        $oCANCELACION = new CancelacionOrden();

        $sqlCancela = "select cancelacion_orden_id as id from mutual_producto_solicitud_cancelaciones mpsc where mutual_producto_solicitud_id = $id";
        $cancelaciones = $this->query($sqlCancela);
        
        $sqlCS = "DELETE FROM mutual_producto_solicitud_cancelaciones where mutual_producto_solicitud_id = $id";
        $this->query($sqlCS);
 	if(!empty($this->getDataSource()->error)){
            parent::notificar($this->getDataSource()->error);
            $this->rollback();
            return false;	            
        }          
        
        if(!empty($cancelaciones)) {
            
            foreach ($cancelaciones as $key => $value) {
                
                $oCANCELACION->query("SET FOREIGN_KEY_CHECKS = 0");
                $sqlCC = "  delete cc.* from cancelacion_ordenes c, cancelacion_orden_cuotas cc 
                            where c.id = ".$value['mpsc']['id']." and c.id = cc.cancelacion_orden_id
                            and c.estado <> 'P'
                            and ifnull(c.orden_descuento_cobro_id,0) = 0;";
                $oCANCELACION->query($sqlCC);
                $error = $oCANCELACION->getDataSource()->error;
                if(!empty($error)){
                    parent::notificar($error);
                    $this->rollback();
                    return false;	            
                } 
                
                $sqlCA = "DELETE FROM cancelacion_ordenes WHERE id = " . $value['mpsc']['id'] . " and estado <> 'P' and ifnull(orden_descuento_cobro_id,0) = 0";
                $oCANCELACION->query($sqlCA);
                $error = $oCANCELACION->getDataSource()->error;
                if(!empty($error)){
                    parent::notificar($error);
                    $this->rollback();
                    return false;	            
                } 
                $oCANCELACION->query("SET FOREIGN_KEY_CHECKS = 1");
            }
            
        }

        $this->guardarHistorial($orden);
        
        return $this->commit();
    }
    
    
    
    
    /**
     * Aprobar Solicitud de Consumo o Servicio
     * Si la orden trae importe cero es una orden solamente y no se tiene que generar la orden de descuento
     * @param unknown_type $id
     * cambio el parametro $id por $datos y agregro la primera linea en la funcion. Gustavo 28/9/2015 es para generar la O.P en Proveedores
     * como antincipo de pago.
     */
//    function aprobar($id,$OrganismoNuevo = null){
    function aprobar($datos,$OrganismoNuevo = null){
    	$id = $datos['MutualProductoSolicitud']['id'];
//        $id = $datos;
//        debug($_POST);
//    	exit;
    //    debug($datos);
    //    exit;
        if(!$this->isAprobada($id)){

            $orden = $this->getOrden($id);

			// debug($orden);
			// exit;

            parent::begin();

            if(isset($datos['MutualProductoSolicitud']['persona_beneficio_id'])){
                $orden['MutualProductoSolicitud']['persona_beneficio_id'] = $datos['MutualProductoSolicitud']['persona_beneficio_id'];
            }
            $orden['MutualProductoSolicitud']['proveedor_id'] = (empty($orden['MutualProductoSolicitud']['reasignar_proveedor_id']) ? $orden['MutualProductoSolicitud']['proveedor_id'] : $orden['MutualProductoSolicitud']['reasignar_proveedor_id']);
			$orden['MutualProductoSolicitud']['nro_referencia_proveedor'] = (isset($datos['MutualProductoSolicitud']['nro_referencia_proveedor']) ? $datos['MutualProductoSolicitud']['nro_referencia_proveedor'] : NULL);
			

            App::import('Model', 'Proveedores.ProveedorVencimiento');
            $oVTOS = new ProveedorVencimiento(null);

            $orden['MutualProductoSolicitud']['fecha_pago'] = date('Y-m-d');

            $vtos = $oVTOS->calculaVencimiento($orden['MutualProductoSolicitud']['proveedor_id'],$orden['MutualProductoSolicitud']['persona_beneficio_id'],$orden['MutualProductoSolicitud']['fecha_pago']);

//            $orden['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
            
            $orden['MutualProductoSolicitud']['periodo_ini'] = (isset($datos['MutualProductoSolicitud']['modifica_periodo']) ? $datos['MutualProductoSolicitud']['periodo_ini']['year'].$datos['MutualProductoSolicitud']['periodo_ini']['month'] : $vtos['inicia_en']);
            
            $orden['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
            $orden['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];

			//REARMAR LOS VENCIMIENTOS SI CAMBIA EL PERIODO DE INICIO
			if(isset($orden['MutualProductoSolicitud']['detalle_calculo_plan']) && !empty($orden['MutualProductoSolicitud']['detalle_calculo_plan'])){

                App::import('model', 'proveedores.MetodoCalculoCuota');
                $oCALC = new MetodoCalculoCuota();
				$orden['MutualProductoSolicitud']['detalle_calculo_plan'] = $oCALC->setPeriodosAndVencimientos(
																				json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']),
																				$orden['MutualProductoSolicitud']['periodo_ini'],
																				$orden['MutualProductoSolicitud']['primer_vto_socio'],
																				$orden['MutualProductoSolicitud']['primer_vto_proveedor'],
																				$this,
																				TRUE
																			);

			}			

            App::import('Model','Pfyj.PersonaBeneficio');
            $oBeneficio = new PersonaBeneficio();

            $orden['MutualProductoSolicitud']['organismo'] = $oBeneficio->getCodigoOrganismo($orden['MutualProductoSolicitud']['persona_beneficio_id']);

            if(!empty($OrganismoNuevo)){
                $orden['MutualProductoSolicitud']['persona_beneficio_id'] = $oBeneficio->clonarBeneficio($orden['MutualProductoSolicitud']['persona_beneficio_id'],$OrganismoNuevo);
                $orden['MutualProductoSolicitud']['organismo'] = $OrganismoNuevo;
            }            

            App::import('Model','mutual.Liquidacion');
            $oLQ = new Liquidacion();
            $orden['MutualProductoSolicitud']['bloqueo_liquidacion'] = $oLQ->isCerrada($orden['MutualProductoSolicitud']['organismo'], $orden['MutualProductoSolicitud']['periodo_ini']);
//            debug($orden);
//            exit;
            if($orden['MutualProductoSolicitud']['bloqueo_liquidacion'] == 1){
                parent::notificar("Bloqueo por Liquidacion");
                parent::rollback();
                return false;
            }    		

            //si no no esta aprobada la solicitud de afiliacion la apruebo
            if($orden['MutualProductoSolicitud']['socio_id'] == 0):

                App::import('Model', 'Pfyj.Socio');
                $oSoc = new Socio();    		

                //verifico si no aprobaron la solicitud y generaron el socio
                $socio = $oSoc->getSocioByPersonaId($orden['MutualProductoSolicitud']['persona_id']);

                if(!empty($socio)):

                    $orden['MutualProductoSolicitud']['socio_id'] = $socio['Socio']['id'];    			

                else:

                    //no tengo socio generado, busco la solicitud y la apruebo

                    App::import('Model', 'Pfyj.SocioSolicitud');
                    $oSolSoc = new SocioSolicitud();

                    $afiliacionID = $oSolSoc->find('all',array('conditions' => array('SocioSolicitud.persona_id' => $orden['MutualProductoSolicitud']['persona_id'], 'SocioSolicitud.aprobada' => 0),'fields' => array('SocioSolicitud.id'),'order' => array('SocioSolicitud.created DESC'), 'limit' => 1));

                    $afiliacionID = Set::extract("{n}.SocioSolicitud",$afiliacionID);

                    if(!empty($afiliacionID[0]['id'])):

                        $afiliacion = $oSolSoc->read(null,$afiliacionID[0]['id']);

                    else:

                        $nroSolicitud = $oSoc->altaDirecta($orden['MutualProductoSolicitud']['persona_id'], $orden['MutualProductoSolicitud']['persona_beneficio_id'],$orden['MutualProductoSolicitud']['periodo_ini']);
                        $afiliacion = $oSolSoc->read(null,$nroSolicitud);
                        if(empty($afiliacion)){
                            $this->notificaciones = $oSoc->notificaciones;
                            parent::notificar("No se pudo generar la solicitud de Alta del Cliente");
                            parent::rollback();
                            return false;
                        }


                    // 						parent::notificar("Error al generar la solicitud de afilicacion");
                    // 						//genero un error
                    // 						return false;

                    endif;
                    if(!empty($OrganismoNuevo)){
                        $afiliacion['SocioSolicitud']['persona_beneficio_id'] = $orden['MutualProductoSolicitud']['persona_beneficio_id'];
                    }                        

                    $orden['MutualProductoSolicitud']['socio_id'] = $oSolSoc->aprobar($afiliacion,null,$afiliacion['SocioSolicitud']['importe_cuota_social']); 

                    if(empty($orden['MutualProductoSolicitud']['socio_id'])){parent::rollback(); return false;}

                endif;

            else:

                //si el socio esta NO ACTIVO lo activo nuevamente
                if($orden['Socio']['activo'] == 0):

                    App::import('Model', 'Pfyj.Socio');
                    $oSoc = new Socio();    

                    $oSoc->alta($orden['Socio']['id'],$orden['MutualProductoSolicitud']['persona_beneficio_id'],$orden['MutualProductoSolicitud']['periodo_ini'],$orden['MutualProductoSolicitud']['primer_vto_socio']);

                endif;


            endif;

            if($orden['MutualProductoSolicitud']['sin_cargo'] == 0):

                $OrdenDto['OrdenDescuento'] = array(
                    'fecha' => $orden['MutualProductoSolicitud']['fecha_pago'],
                    'tipo_orden_dto' => $orden['MutualProductoSolicitud']['tipo_orden_dto'],
                    'numero' => $orden['MutualProductoSolicitud']['id'],
                    'tipo_producto' => $orden['MutualProductoSolicitud']['tipo_producto'],
                    'socio_id' => $orden['MutualProductoSolicitud']['socio_id'],
                    'persona_beneficio_id' => $orden['MutualProductoSolicitud']['persona_beneficio_id'],
                    'proveedor_id' => $orden['MutualProductoSolicitud']['proveedor_id'],
                    'mutual_producto_id' => 0,
                    'periodo_ini' => $orden['MutualProductoSolicitud']['periodo_ini'],
                    'importe_cuota' => $orden['MutualProductoSolicitud']['importe_cuota'],
                    'importe_total' => $orden['MutualProductoSolicitud']['importe_total'],
                    'primer_vto_socio' => $orden['MutualProductoSolicitud']['primer_vto_socio'],
                    'primer_vto_proveedor' => $orden['MutualProductoSolicitud']['primer_vto_proveedor'],
                    'cuotas' => $orden['MutualProductoSolicitud']['cuotas'],
                    'permanente' => $orden['MutualProductoSolicitud']['permanente'],
                    'tna' => $orden['MutualProductoSolicitud']['tna'],
                    'tnm' => $orden['MutualProductoSolicitud']['tnm'],
                    'tem' => $orden['MutualProductoSolicitud']['tem'],
                    'cft' => $orden['MutualProductoSolicitud']['cft'],
                    'capital_puro' => $orden['MutualProductoSolicitud']['capital_puro'],
                    'interes' => $orden['MutualProductoSolicitud']['interes'],
                    'iva' => $orden['MutualProductoSolicitud']['iva'],
                    'gasto_admin' => $orden['MutualProductoSolicitud']['gasto_admin'],
                    'sellado' => $orden['MutualProductoSolicitud']['sellado'],
                    'metodo_calculo' => $orden['MutualProductoSolicitud']['metodo_calculo'],
                    'gasto_admin_porc' => $orden['MutualProductoSolicitud']['gasto_admin_porc'],
                    'sellado_porc' => $orden['MutualProductoSolicitud']['sellado_porc'],
                    'iva_porc' => $orden['MutualProductoSolicitud']['iva_porc'],
                    'ayuda_economica' => $orden['MutualProductoSolicitud']['ayuda_economica'],
                    'importe_solicitado' => $orden['MutualProductoSolicitud']['importe_solicitado'],
                    'importe_capital' => $orden['MutualProductoSolicitud']['importe_solicitado'],
                    'nro_referencia_proveedor' => $orden['MutualProductoSolicitud']['nro_referencia_proveedor'],
					'mutual_producto_solicitud_id' => $orden['MutualProductoSolicitud']['id']
                );
                App::import('Model','Mutual.OrdenDescuentoCuota');
                $oCuota = new OrdenDescuentoCuota();

				// debug($OrdenDto);
				// exit;

                if(isset($orden['MutualProductoSolicitud']['detalle_calculo_plan']) && !empty($orden['MutualProductoSolicitud']['detalle_calculo_plan'])){

                    $objetoCalculado = json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']);
                    $tipoCuota = parent::GlobalDato('concepto_2',$orden['MutualProductoSolicitud']['tipo_producto']);
                    $cuota = array();
                    $cuotas = array();
                    foreach($objetoCalculado->detalleCuotas as $nroCuota => $detalle){
                        $cuota['persona_beneficio_id'] = $OrdenDto['OrdenDescuento']['persona_beneficio_id'];
                        $cuota['socio_id'] = $OrdenDto['OrdenDescuento']['socio_id'];
                        $cuota['tipo_orden_dto'] = $OrdenDto['OrdenDescuento']['tipo_orden_dto'];
                        $cuota['tipo_producto'] = $OrdenDto['OrdenDescuento']['tipo_producto'];
                        $cuota['periodo'] = $detalle->periodo;
                        $cuota['nro_cuota'] = $detalle->nroCuota;
                        $cuota['tipo_cuota'] = $tipoCuota;
                        $cuota['situacion'] = 'MUTUSICUMUTU';
                        $cuota['estado'] = 'A';
                        $cuota['importe'] = $OrdenDto['OrdenDescuento']['importe_cuota'];
                        $cuota['proveedor_id'] = $OrdenDto['OrdenDescuento']['proveedor_id'];
                        $cuota['vencimiento'] = $detalle->vtoSocio;
                        $cuota['vencimiento_proveedor'] = $detalle->vtoProveedor;	
                        $cuota['nro_referencia_proveedor'] = (isset($OrdenDto['OrdenDescuento']['nro_referencia_proveedor']) ? $OrdenDto['OrdenDescuento']['nro_referencia_proveedor'] : "");			

                        $cuota['capital'] = $detalle->capital;
                        $cuota['interes'] = $detalle->interes;
                        $cuota['iva'] = $detalle->iva;

                        array_push($cuotas,$cuota);                        
                    }
                    $OrdenDto['OrdenDescuentoCuota'] = $cuotas;

                }else{

                    $OrdenDto['OrdenDescuentoCuota'] = $oCuota->armaCuotas($OrdenDto);

                }


                App::import('Model','Mutual.OrdenDescuento');
                $oODTO = new OrdenDescuento();

                if($orden['MutualProductoSolicitud']['permanente'] == 0){
                    //genero la orden de descuento y las cuotas					
                    if(!$oODTO->saveAll($OrdenDto)){
                        parent::notificar("No se pudo emitir la Orden de Descuento");
                        parent::rollback();
                        return false;
                    }
                }else{
                    //genero la orden de descuento solamente
                    //	    			if(!$oODTO->save($OrdenDto)) return false;
                    if(!$oODTO->saveAll($OrdenDto)){
                        parent::notificar("No se pudo emitir la Orden de Descuento permanente.");
                        parent::rollback();
                        return false;
                    }
                }
                $orden['MutualProductoSolicitud']['orden_descuento_id'] = $oODTO->getLastInsertID();

                //GENERAR LA ORDEN DE DESCUENTO DEL FONDO DE ASISTENCIA
                // 	    		if($orden['MutualProductoSolicitud']['tipo_producto'] == '')
                $TIPO_PRODUCTO_SIGEM = parent::GlobalDato('concepto_4',$orden['MutualProductoSolicitud']['tipo_producto']);
                $importeSeguro = 0;
                if(!empty($TIPO_PRODUCTO_SIGEM)) $importeSeguro = parent::GlobalDato('decimal_1',$TIPO_PRODUCTO_SIGEM);

                if(!empty($importeSeguro)){

                    $TIPO_ORDEN_SIGEM = parent::GlobalDato('concepto_3', $TIPO_PRODUCTO_SIGEM);
                    $TIPO_ORDEN_SIGEM = (empty($TIPO_ORDEN_SIGEM) ? 'EXPTE' : $TIPO_ORDEN_SIGEM);	    			

                    $proveedorSeguroId = parent::GlobalDato('entero_1',$TIPO_PRODUCTO_SIGEM);
                    $importeSeguroTotal = $importeSeguro * $orden['MutualProductoSolicitud']['cuotas'];

                    $OrdenDto['OrdenDescuento'] = array(
                        'id' => 0,
                        'fecha' => $orden['MutualProductoSolicitud']['fecha_pago'],
                        'tipo_orden_dto' => $TIPO_ORDEN_SIGEM,
                        'numero' => $orden['MutualProductoSolicitud']['id'],
                        'tipo_producto' => $TIPO_PRODUCTO_SIGEM,
                        'socio_id' => $orden['MutualProductoSolicitud']['socio_id'],
                        'persona_beneficio_id' => $orden['MutualProductoSolicitud']['persona_beneficio_id'],
                        'proveedor_id' => $proveedorSeguroId,
                        'mutual_producto_id' => 0,
                        'periodo_ini' => $orden['MutualProductoSolicitud']['periodo_ini'],
                        'importe_cuota' => $importeSeguro,
                        'importe_total' => $importeSeguroTotal,
                        'primer_vto_socio' => $orden['MutualProductoSolicitud']['primer_vto_socio'],
                        'primer_vto_proveedor' => $orden['MutualProductoSolicitud']['primer_vto_proveedor'],
                        'cuotas' => $orden['MutualProductoSolicitud']['cuotas'],
                        'permanente' => 0,
						'mutual_producto_solicitud_id' => $orden['MutualProductoSolicitud']['id']
                    );
                    
                    



                    $OrdenDto['OrdenDescuentoCuota'] = $oCuota->armaCuotas($OrdenDto);
                    
                    
                    if(!$oODTO->saveAll($OrdenDto)){
                        parent::notificar("No se pudo emitir la Orden de Descuento por el Fondo de Asistencia.");
                        parent::rollback();
                        return false;
                    }
                    $orden['MutualProductoSolicitud']['orden_descuento_seguro_id'] = $oODTO->getLastInsertID();
                }


            endif;

            

            // SI LA ORDEN ES UNA AYUDA O DE TIPO PRESTAMO QUIERE DECIR QUE HUBO SALIDA DE DINERO,
            // POR TAL MOTIVO GENERO UNA ORDEN DE PAGO. 
            if($orden['MutualProductoSolicitud']['prestamo'] == 1):
                $this->oMovimiento = $this->importarModelo('Movimiento', 'proveedores');
                $this->oOPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
                $this->oOPago = $this->importarModelo('OrdenPago', 'proveedores');
//                debug($datos);
//                exit;
                if(!$this->oMovimiento->guardarOpago($datos)):
                    parent::rollback();
                    return false;
                endif;
                $orden['MutualProductoSolicitud']['orden_pago_id'] = $this->oOPagoDetalle->getOPagoByProductoSolicitud($id); 
                $aOPagoDetalle = $this->oOPago->getDetalle($orden['MutualProductoSolicitud']['orden_pago_id']);
                $aOPagoDetalle['OrdenPagoDetalle']['MutualProductoSolicitudId'] = 0;
                if(!$this->oOPagoDetalle->saveAll($aOPagoDetalle)):
                    parent::rollback();
                    return false;
                endif;
            endif;
	    	
            
            
            
            $orden['MutualProductoSolicitud']['aprobada'] = 1;
            $orden['MutualProductoSolicitud']['aprobada_por'] = (isset($_SESSION['NAME_USER_LOGON_SIGEM']) ? $_SESSION['NAME_USER_LOGON_SIGEM'] : 'APLICACION_SERVER');
            $orden['MutualProductoSolicitud']['aprobada_el'] = date("Y-m-d");
            $orden['MutualProductoSolicitud']['estado'] = 'MUTUESTA0014';
            $orden['MutualProductoSolicitud']['vendedor_notificar'] = true;
            $orden['MutualProductoSolicitud']['nro_referencia_proveedor'] = (isset($datos['MutualProductoSolicitud']['nro_referencia_proveedor']) ? $datos['MutualProductoSolicitud']['nro_referencia_proveedor'] : NULL);
            if(!parent::save($orden)){
                parent::notificar("No se pudo Actualizar la información de Aprobación de la Solicitud");
                parent::rollback();
                return false;
            }else{
                //APROBAR LAS CANCELACIONES
                if(!empty($orden['MutualProductoSolicitudCancelacion'])){
                    App::import('model','Mutual.OrdenDescuentoCobro');
                    $oCobro = new OrdenDescuentoCobro();
                    App::import('model','Mutual.CancelacionOrden');
					$oCANCELACION = new CancelacionOrden();
					
					$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
					$PROCESA_CANCELACIONES = (isset($INI_FILE['general']['recauda_cancelaciones_solicitudes']) && $INI_FILE['general']['recauda_cancelaciones_solicitudes'] != 0 ? TRUE : FALSE);

                    foreach ($orden['MutualProductoSolicitudCancelacion'] as $cancel){
                        $cancelacion = $oCANCELACION->read(null,$cancel['cancelacion_orden_id']);
                        if(!empty($cancelacion) && 
                                $cancelacion['CancelacionOrden']['estado'] !== 'P' &&
                                ($cancelacion['CancelacionOrden']['orden_descuento_cobro_id'] === '0' || empty($cancelacion['CancelacionOrden']['orden_descuento_cobro_id']))){

                            if($PROCESA_CANCELACIONES){
                                // -------------------------------------------------------------------- 
                                $tipoCobro = 'MUTUTCOBCANC';
                                $retiene_comercio = 0;
                                $proveedorOrigenFondoId = $orden['MutualProductoSolicitud']['proveedor_id'];
                                if($cancelacion['CancelacionOrden']['orden_proveedor_id'] === $orden['MutualProductoSolicitud']['proveedor_id']) {
                                    $tipoCobro = 'MUTUTCOBCCOM';
                                    $retiene_comercio = 1;
                                }
                                // --------------------------------------------------------------------
                                if(!empty($cancelacion['CancelacionOrdenCuota'])){
                                    
                                    ##########################################################################################
                                    # PARA RECAUDAR LA CANCELACION USAR EL METODO DE cancelacion_orden.php ->cancelacionByCaja
                                    ##########################################################################################
                                    $cobro = array();
                                    $hoyToArray = array(
                                        'day' => date('d'),
                                        'month' => date('m'),
                                        'year' => date('Y')                                        
                                    );
                                    $cobro['CancelacionOrden'] = array(
                                        'id_check' => array(
                                            $cancelacion['CancelacionOrden']['id'] => $cancelacion['CancelacionOrden']['importe_proveedor'] * 100
                                        ),
                                        'importe_total' => $cancelacion['CancelacionOrden']['importe_proveedor'],
                                        'fecha_comprobante' => $hoyToArray,
                                        'observacion' => 'CANCELACION #'.$cancelacion['CancelacionOrden']['id'].' | ' . $cancelacion['CancelacionOrden']['concepto'],
                                        'compensa_pago' => 1,
                                        'forma_cobro' => 'EFT',
                                        'numero_deposito' => '',
                                        'fdeposito' => null,
                                        'banco_id' => null,
                                        'plaza' => '',
                                        'numero_cheque' => null,
                                        'fcheque' => null,
                                        'fvenc' => null,
                                        'librador' => null,
                                        'importe' => $cancelacion['CancelacionOrden']['importe_proveedor'],
                                        'liquidacion_id' => null,
                                        'tipo_documento' => 'REC',
                                        'cabecera_socio_id' => $cancelacion['CancelacionOrden']['socio_id'],
                                        'forma_cobro_desc' => 'EFECTIVO',
                                        'importe_cobro' => $cancelacion['CancelacionOrden']['importe_proveedor'],
                                        'acumula' => 0,
                                        'importe_cancela' => $cancelacion['CancelacionOrden']['importe_proveedor'],
                                        'proveedor_origen_id' => $orden['MutualProductoSolicitud']['proveedor_id'],
                                        'retiene_comercio' => $retiene_comercio,
                                    );
                                    $cobro['Referencia'] = array('modelo' => 'CancelacionOrden');
                                    if($oCANCELACION->cancelacionByCaja($cobro)) {
                                        $cancelacion['CancelacionOrden']['estado'] = 'P';
                                    }
                                    // $cancelacion['CancelacionOrden']['orden_descuento_cobro_id'] = $oCobro->generarCobroByCancelacion($cancelacion,date("Y-m-d"),$tipoCobro, $proveedorOrigenFondoId);
                                }
                            }
                            //$orden['MutualProductoSolicitud']['orden_descuento_id']
                            
                            $cancelacion['CancelacionOrden']['nueva_orden_dto_id'] = $orden['MutualProductoSolicitud']['orden_descuento_id'];
                            $cancelacion['CancelacionOrden']['nro_solicitud'] = $orden['MutualProductoSolicitud']['id'];
                            
                            $cancelacion['CancelacionOrden']['mutual_producto_solicitud_id'] = $orden['MutualProductoSolicitud']['id'];
                            $cancelacion['CancelacionOrden']['nueva_orden_dto_id'] = $orden['MutualProductoSolicitud']['orden_descuento_id'];
                            $cancelacion['CancelacionOrden']['origen_proveedor_id'] = $orden['MutualProductoSolicitud']['proveedor_id'];
                            
                            if(!$oCANCELACION->save($cancelacion)){
                                parent::notificar("Error al actualizar datos de la cancelación");
                                parent::rollback();
                                return false;	    						
                            }
                            // 	    					$oCANCELACION->save($cancelacion);
                        }
                    }
                }
                //                debug($OrganismoNuevo);
                //                 debug($orden);
                //                 debug($OrdenDto);
                // 	    		exit; 


				/** 
				 * CARGAR LAS CUOTA DEL GASTO ADMINISTRATIVO Y SELLADOS
				 * HACERLES UN COBRO POR CAJA RETIENE COMERCIO ??
				 * O DEJAR QUE DECIDAN QUE HACER CUANDO SE APRUEBA?
				 */
                App::import('Model','Mutual.OrdenDescuentoCuota');
                $oCUOTA = new OrdenDescuentoCuota();				
				$objetoCalculado = json_decode($orden['MutualProductoSolicitud']['detalle_calculo_plan']);
                $cargosOtorgamientos = array();
				if(!empty($objetoCalculado->liquidacion->gastoAdminstrativo->importe) 
					&& !empty($objetoCalculado->liquidacion->gastoAdminstrativo->tipoCuota)){
					$gtoAdmin = array();
					$gtoAdmin['OrdenDescuentoCuota'] = array(
						'id' => 0,
						'orden_descuento_id' => $orden['MutualProductoSolicitud']['orden_descuento_id'],
						'persona_beneficio_id' => $orden['MutualProductoSolicitud']['persona_beneficio_id'],
						'socio_id' => $orden['MutualProductoSolicitud']['socio_id'],
						'tipo_orden_dto' => $orden['MutualProductoSolicitud']['tipo_orden_dto'],
						'tipo_producto' => $orden['MutualProductoSolicitud']['tipo_producto'],
						'tipo_cuota' => $objetoCalculado->liquidacion->gastoAdminstrativo->tipoCuota,
						'periodo' => $orden['MutualProductoSolicitud']['periodo_ini'],
						'nro_cuota' => 0,
						'situacion' => 'MUTUSICUMUTU',
						'estado' => 'A',
						'importe' => $objetoCalculado->liquidacion->gastoAdminstrativo->importe,
						'proveedor_id' => $orden['MutualProductoSolicitud']['proveedor_id'],
						'vencimiento' => $orden['MutualProductoSolicitud']['fecha_pago'],
						'vencimiento_proveedor' => $orden['MutualProductoSolicitud']['fecha_pago'],
						'nro_referencia_proveedor' => $orden['MutualProductoSolicitud']['nro_referencia_proveedor'],
					);
					if(!$oCUOTA->save($gtoAdmin)){
						parent::notificar("ERROR AL CARGAR EL GASTO ADMINISTRATIVO");
                        parent::rollback();
						return false;	    						
					}
					$gtoAdmin['OrdenDescuentoCuota']['id'] = $oCUOTA->id;
					array_push($cargosOtorgamientos, $gtoAdmin['OrdenDescuentoCuota']);
                    
				}
				if(!empty($objetoCalculado->liquidacion->sellado->importe) 
					&& !empty($objetoCalculado->liquidacion->sellado->tipoCuota)){
					$gtoSell = array();
					$gtoSell['OrdenDescuentoCuota'] = array(
						'id' => 0,
						'orden_descuento_id' => $orden['MutualProductoSolicitud']['orden_descuento_id'],
						'persona_beneficio_id' => $orden['MutualProductoSolicitud']['persona_beneficio_id'],
						'socio_id' => $orden['MutualProductoSolicitud']['socio_id'],
						'tipo_orden_dto' => $orden['MutualProductoSolicitud']['tipo_orden_dto'],
						'tipo_producto' => $orden['MutualProductoSolicitud']['tipo_producto'],
						'tipo_cuota' => $objetoCalculado->liquidacion->sellado->tipoCuota,
						'periodo' => $orden['MutualProductoSolicitud']['periodo_ini'],
						'nro_cuota' => 0,
						'situacion' => 'MUTUSICUMUTU',
						'estado' => 'A',
						'importe' => $objetoCalculado->liquidacion->sellado->importe,
						'proveedor_id' => $orden['MutualProductoSolicitud']['proveedor_id'],
						'vencimiento' => $orden['MutualProductoSolicitud']['fecha_pago'],
						'vencimiento_proveedor' => $orden['MutualProductoSolicitud']['fecha_pago'],
						'nro_referencia_proveedor' => $orden['MutualProductoSolicitud']['nro_referencia_proveedor'],
					);
					if(!$oCUOTA->save($gtoSell)){
						parent::notificar("ERROR AL CARGAR EL GASTO DE SELLADOS");
                        parent::rollback();
						return false;	    						
					}
					$gtoSell['OrdenDescuentoCuota']['id'] = $oCUOTA->id;
					array_push($cargosOtorgamientos, $gtoSell['OrdenDescuentoCuota']);
				}
                #RECAUDAR LAS CUOTAS
				if(!empty($cargosOtorgamientos)){
                    
                    App::import('Model','Mutual.OrdenDescuentoCobro');
                    $oCOBRO = new OrdenDescuentoCobro();
                    
                    if(!$oCOBRO->cobrarGastoAdminSelladosPrestamo(
                        $orden['MutualProductoSolicitud']['socio_id'], 
                        $cargosOtorgamientos,
                        $orden['MutualProductoSolicitud']['aprobada_el'],
                        $orden['MutualProductoSolicitud']['periodo_ini']
                        )) {
                            parent::notificar("ERROR AL RECAUDAR LOS GASTOS DE OTORGAMIENTO Y SELLADOS");
                            parent::rollback();
                            return false;
                        }
                }				

                if(!$this->guardarHistorial($orden)){
                    parent::notificar("ERROR AL GUARDAR EL HISTORIAL");
                    parent::rollback();
                    return false;                   
                };
                
            }

            parent::commit();
            return true;
            
        }
        
        parent::notificar("La Solicitud ya se encuentra aprobada.");
        return false;
    }
    
    
    function __actualizarCuotaSocial($data){
    	App::import('Model', 'Pfyj.Socio');
		$oSocio = new Socio();
		return $oSocio->actualizarCuotaSocial($data['MutualProductoSolicitud']['socio_id'],$data['MutualProductoSolicitud']['persona_beneficio_id'],$data['MutualProductoSolicitud']['cuota_social_vigente']);    	
    }
    
    /**
     * Verifica si es un socio nuevo o si el socio esta no activo
     * Si no es socio le genero una solicitud de afiliacion y la dejo pendiente de aprobar.
     * Si es socio y NO esta activo le genero una solicitud de afiliacion y la dejo pendiente de afiliacion.
     * @param $data
     * @return unknown_type
     */
    function __chequearSocio($data){
    	
    	App::import('Model', 'Pfyj.Socio');
		$oSoc = new Socio();

		$socio = $oSoc->read(null,$data['MutualProductoSolicitud']['socio_id']); 

		//verifico que no tenga ya un socio asignado (lo busco por persona_id)
//		$data['MutualProductoSolicitud']['socio_id'] = 0;
		if(empty($socio)){
			$socio = $oSoc->getSocioByPersonaId($data['MutualProductoSolicitud']['persona_id']);
			$data['MutualProductoSolicitud']['socio_id'] = $socio['Socio']['id'];
		}
		
		if(empty($socio)){
			#SOCIO NUEVO	
	    	App::import('Model', 'Pfyj.SocioSolicitud');
			$oSolSoc = new SocioSolicitud();
            
    		App::import('Model', 'Proveedores.ProveedorVencimiento');
    		$oVTOS = new ProveedorVencimiento(null);
    		$vtos = $oVTOS->calculaVencimiento(18,$data['MutualProductoSolicitud']['persona_beneficio_id'],date('Y-m-d'));
    		$data['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
    		$data['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
    		$data['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];            
					
			#GENERO UNA SOLICITUD DE AFILIACION
			$afiliacion = array('SocioSolicitud' => array(
				'tipo_solicitud' => 'A',
				'aprobada' => 0,
				'persona_id' => $data['MutualProductoSolicitud']['persona_id'],
				'persona_beneficio_id' => $data['MutualProductoSolicitud']['persona_beneficio_id'],
				'fecha' => date('Y-m-d'),
				'periodo_ini' => $data['MutualProductoSolicitud']['periodo_ini'],
				'primer_vto_socio' => $data['MutualProductoSolicitud']['primer_vto_socio'],
				'primer_vto_proveedor' => $data['MutualProductoSolicitud']['primer_vto_proveedor'],
				'importe_cuota_social' => $data['MutualProductoSolicitud']['cuota_social'],
                                'vendedor_id'=> (isset($data['MutualProductoSolicitud']['vendedor_id']) ? $data['MutualProductoSolicitud']['vendedor_id'] : NULL)
			));
			if(!$oSolSoc->save($afiliacion)) return false;
//			$id = $oSolSoc->getLastInsertID();
//			$afiliacion = $oSolSoc->read(null,$id);
//			$data['MutualProductoSolicitud']['socio_id'] = $oSolSoc->aprobar($afiliacion,null,$data['MutualProductoSolicitud']['cuota_social']); 

//	    	return $data;	
	    			
//		}else if($socio['Socio']['activo'] == 0){

//			App::import('Model', 'Pfyj.Socio');
//			$oSocio = new Socio(null);
//			$oSocio->alta($data['MutualProductoSolicitud']['socio_id'],$data['MutualProductoSolicitud']['persona_beneficio_id'], $data['MutualProductoSolicitud']['periodo_ini'],$data['MutualProductoSolicitud']['primer_vto_socio']);
			
			//genero una solicitud de re-afiliacion
			//el socio lo doy de alta cuando apruebo el consumo
//	    	App::import('Model', 'Pfyj.SocioSolicitud');
//			$oSolSoc = new SocioSolicitud();
//			$afiliacion = array('SocioSolicitud' => array(
//				'tipo_solicitud' => 'A',
//				'aprobada' => 0,
//				'persona_id' => $data['MutualProductoSolicitud']['persona_id'],
//				'persona_beneficio_id' => $data['MutualProductoSolicitud']['persona_beneficio_id'],
//				'fecha' => date('Y-m-d'),
//				'periodo_ini' => $data['MutualProductoSolicitud']['periodo_ini'],
//				'primer_vto_socio' => $data['MutualProductoSolicitud']['primer_vto_socio'],
//				'primer_vto_proveedor' => $data['MutualProductoSolicitud']['primer_vto_proveedor'],
//				'importe_cuota_social' => $data['MutualProductoSolicitud']['cuota_social'],
//			));
//			if(!$oSolSoc->save($afiliacion)) return false;						
//			$data['MutualProductoSolicitud']['socio_id'] = $socio['Socio']['id'];
//			
////	    	return $data;	
			
		}else{
			//verificar si se modifica la cuota social
			$data['MutualProductoSolicitud']['socio_id'] = $socio['Socio']['id'];
//			return $data;
		}
		
		return $data;
		
    }
    
    /**
     * @deprecated
     * @param $data
     * @return unknown_type
     */
    function __generarSocio($data){
//    	App::import('Model', 'Pfyj.SocioSolicitud');
//		$oSolSoc = new SocioSolicitud();
//				
//		#GENERO UNA SOLICITUD DE AFILIACION
//		$afiliacion = array('SocioSolicitud' => array(
//			'tipo_solicitud' => 'A',
//			'aprobada' => 0,
//			'persona_id' => $data['MutualProductoSolicitud']['persona_id'],
//			'persona_beneficio_id' => $data['MutualProductoSolicitud']['persona_beneficio_id'],
//			'fecha' => date('Y-m-d'),
//			'periodo_ini' => $data['MutualProductoSolicitud']['periodo_ini'],
//			'primer_vto_socio' => $data['MutualProductoSolicitud']['primer_vto_socio'],
//			'primer_vto_proveedor' => $data['MutualProductoSolicitud']['primer_vto_proveedor'],
//		));
//		if(!$oSolSoc->save($afiliacion)) return false;
//		$id = $oSolSoc->getLastInsertID();
//		$afiliacion = $oSolSoc->read(null,$id);
//		$data['MutualProductoSolicitud']['socio_id'] = $oSolSoc->aprobar($afiliacion,null,$data['MutualProductoSolicitud']['cuota_social']); 
//  	
//    	return $data;
    }
    
    function __setDatos($data){

    	
		list($producto_id,$tipo_producto,$tipo_orden_dto,$proveedor_id,$impoFijo,$cuotaSocialDiferenciada) = explode('|',$data['MutualProductoSolicitud']['tipo_producto_mutual_producto_id']);
		

		
		$data['MutualProductoSolicitud']['mutual_producto_id'] = $producto_id;
		$data['MutualProductoSolicitud']['tipo_producto'] = $tipo_producto;
		$data['MutualProductoSolicitud']['tipo_orden_dto'] = $tipo_orden_dto;
		$data['MutualProductoSolicitud']['proveedor_id'] = $proveedor_id;
		
		#BUSCO EL IMPORTE VIGENTE DE LA CUOTA SOCIAL A LA FECHA SEGUN EL BENEFICIO
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBeneficio = new PersonaBeneficio();
		$cuotaSoc = $oBeneficio->getImporteCuotaSocial($data['MutualProductoSolicitud']['persona_beneficio_id']);
		$data['MutualProductoSolicitud']['cuota_social_vigente'] = $cuotaSoc['decimal_1'];
		$data['MutualProductoSolicitud']['actualizar_cuota_social'] = 0;		
		
		#SI NO ES SOCIO, CALCULO LA CUOTA SOCIAL
		if($data['MutualProductoSolicitud']['socio_id'] == 0){
			
			if($cuotaSocialDiferenciada != 0) $data['MutualProductoSolicitud']['cuota_social'] = $cuotaSocialDiferenciada;
			else $data['MutualProductoSolicitud']['cuota_social'] = $cuotaSoc['decimal_1'];
			
		}else{
			#SI ES SOCIO, VERIFICO EL IMPORTE DE LA CUOTA SOCIAL QUE ESTA DESCONTANDO
			App::import('Model','Pfyj.Socio');
			$oSocio = new Socio();
			$oSocio->bindModel(array('hasOne' => array('OrdenDescuento')));	
			$socio = $oSocio->read(null,$data['MutualProductoSolicitud']['socio_id']);
			$data['MutualProductoSolicitud']['cuota_social'] = $socio['OrdenDescuento']['importe_cuota'];
			if($data['MutualProductoSolicitud']['cuota_social'] != $data['MutualProductoSolicitud']['cuota_social_vigente']) $data['MutualProductoSolicitud']['actualizar_cuota_social'] = 1;
		}

		
		//calculo vencimientos
		App::import('Model', 'Proveedores.ProveedorVencimiento');
		$this->ProveedorVencimiento = new ProveedorVencimiento(null);
		
		$data['MutualProductoSolicitud']['fecha_pago'] = $data['MutualProductoSolicitud']['fecha_pago']['year'].'-'.$data['MutualProductoSolicitud']['fecha_pago']['month'] .'-'. $data['MutualProductoSolicitud']['fecha_pago']['day'];
		
		$vtos = $this->ProveedorVencimiento->calculaVencimiento($proveedor_id,$data['MutualProductoSolicitud']['persona_beneficio_id'],$data['MutualProductoSolicitud']['fecha_pago']);
		
		$data['MutualProductoSolicitud']['fecha'] = date('Y-m-d');
		$data['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
		$data['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
		$data['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
    	
		
		
		if($data['MutualProductoSolicitud']['permanente'] == 1){
			$data['MutualProductoSolicitud']['importe_total'] = (isset($data['MutualProductoSolicitud']['importe_total']) ? $data['MutualProductoSolicitud']['importe_total'] : $impoFijo);
			$data['MutualProductoSolicitud']['importe_cuota'] = $data['MutualProductoSolicitud']['importe_total'];
			$data['MutualProductoSolicitud']['cuotas'] = 1;
		}else{
			if(!isset($data['MutualProductoSolicitud']['importe_total'])) $data['MutualProductoSolicitud']['importe_total'] = 0;
			$data['MutualProductoSolicitud']['importe_cuota'] = $data['MutualProductoSolicitud']['importe_total'] / (!empty($data['MutualProductoSolicitud']['cuotas']) ? $data['MutualProductoSolicitud']['cuotas'] : 1);
			
		}
                
                $data['MutualProductoSolicitud']['importe_solicitado'] = $data['MutualProductoSolicitud']['importe_total'];
                $data['MutualProductoSolicitud']['importe_percibido'] = $data['MutualProductoSolicitud']['importe_total'];
		
		return $data;
    }

    /**
     * @deprecated
     * Implementado en $this->getSolicitud()
     * @param unknown $solicitud
     * @param boolean $cargarOrdenDto
     * @param boolean $cargarDatosPersona
     * @return NULL|array|number
     */
    function armaDatos($solicitud,$cargarOrdenDto=true,$cargarDatosPersona = false){
        
        // return $this->getSolicitud($solicitud['MutualProductoSolicitud']['id'],$cargarOrdenDto,$cargarDatosPersona);
        
    	App::import('Model','Proveedores.Proveedor');
    	$oProveedor = new Proveedor();
    	$oProveedor->unbindModel(array('hasMany' => array('MutualProducto')));
    	$proveedor = $oProveedor->read(null,$solicitud['MutualProductoSolicitud']['proveedor_id']);
    	$glb = parent::getGlobalDato('concepto_1',$solicitud['MutualProductoSolicitud']['tipo_producto']);
//    	debug($solicitud);
//        exit;

        // App::import('Helper', 'Util');
        // $oUT = new UtilHelper();
        // $solicitud['MutualProductoSolicitud']['fecha_emision_string'] = $oUT->fechaToString($solicitud['MutualProductoSolicitud']['fecha']);
        
    	$solicitud['MutualProductoSolicitud']['estado_desc'] = parent::GlobalDato("concepto_1", $solicitud['MutualProductoSolicitud']['estado']);
    	$solicitud['MutualProductoSolicitud']['forma_pago_desc'] = parent::GlobalDato("concepto_1", $solicitud['MutualProductoSolicitud']['forma_pago']);
    	$solicitud['MutualProductoSolicitud']['nro_print'] = str_pad(strval($solicitud['MutualProductoSolicitud']['id']),8,'0',STR_PAD_LEFT);
    	 
    	
    	$solicitud['MutualProductoSolicitud']['proveedor_producto'] = $proveedor['Proveedor']['razon_social_resumida'] .' - ' . $glb['GlobalDato']['concepto_1'] . ($solicitud['MutualProductoSolicitud']['nro_referencia_proveedor'] != '' ? ' (REF: '.$solicitud['MutualProductoSolicitud']['nro_referencia_proveedor'].')' : '');

    	$solicitud['MutualProductoSolicitud']['proveedor'] = $proveedor['Proveedor']['razon_social_resumida'];
    	$solicitud['MutualProductoSolicitud']['proveedor_pagare_blank'] = $proveedor['Proveedor']['pagare_blank'];
        $solicitud['MutualProductoSolicitud']['proveedor_pagare_direccion'] = $proveedor['Proveedor']['direccion_pagare'];

    	$solicitud['MutualProductoSolicitud']['proveedor_full_name'] = $proveedor['Proveedor']['razon_social'];
    	$solicitud['MutualProductoSolicitud']['proveedor_cuit'] = $proveedor['Proveedor']['cuit'];
    	 
    	$solicitud['MutualProductoSolicitud']['proveedor_domicilio'] = $proveedor['Proveedor']['calle'] ." " . $proveedor['Proveedor']['numero_calle'] . " - " . $proveedor['Proveedor']['piso'] . " Of." . $proveedor['Proveedor']['dpto'];
    	$solicitud['MutualProductoSolicitud']['proveedor_localidad'] = $proveedor['Proveedor']['codigo_postal'] . " - " . $proveedor['Proveedor']['localidad'];
    	$solicitud['MutualProductoSolicitud']['proveedor_localidad_pagare'] = $proveedor['Proveedor']['localidad'];
    	$solicitud['MutualProductoSolicitud']['proveedor_telefono'] = $proveedor['Proveedor']['telefono_fijo'];
    	$solicitud['MutualProductoSolicitud']['proveedor_pagare_blank'] = $proveedor['Proveedor']['pagare_blank'];
    	$solicitud['MutualProductoSolicitud']['proveedor_reasignable'] = $proveedor['Proveedor']['reasignable'];
    	 
    	if($solicitud['MutualProductoSolicitud']['proveedor_reasignable'] == 1){
    		$solicitud['MutualProductoSolicitud']['proveedor_reasignable_a'] = $oProveedor->cargarProveedoresReasignacion($solicitud['MutualProductoSolicitud']['proveedor_id'],true);
    	}
    	$solicitud['MutualProductoSolicitud']['proveedor_reasignada_a'] = null;
    	if($solicitud['MutualProductoSolicitud']['reasignar_proveedor_id'] != 0){
    		$proveedorReasignado = $oProveedor->read(null,$solicitud['MutualProductoSolicitud']['reasignar_proveedor_id']);
    		$solicitud['MutualProductoSolicitud']['proveedor_reasignada_a'] = $proveedorReasignado['Proveedor']['razon_social'];
    		$solicitud['MutualProductoSolicitud']['proveedor_producto'] = $solicitud['MutualProductoSolicitud']['proveedor_producto'] . " ** ".$solicitud['MutualProductoSolicitud']['proveedor_reasignada_a']." **";
    	}    	
    	
    	$solicitud['MutualProductoSolicitud']['producto'] = $glb['GlobalDato']['concepto_1'];
    	
    	App::import('Model','Pfyj.Socio');
    	$oSocio = new Socio();
    	App::import('Model','Pfyj.Persona');
    	$oPersona = new Persona();    	
    	
    	if($solicitud['MutualProductoSolicitud']['socio_id'] != 0):
            
            $persona = $oSocio->getPersona($solicitud['MutualProductoSolicitud']['socio_id']);

            $solicitud['MutualProductoSolicitud']['beneficiario'] = $persona['Persona']['tipo_documento_desc']. ' ' . $persona['Persona']['documento']." - ".$persona['Persona']['apenom'];
            $solicitud['MutualProductoSolicitud']['beneficiario_apenom'] = $persona['Persona']['apenom'];
            $solicitud['MutualProductoSolicitud']['beneficiario_apellido'] = $persona['Persona']['apellido'];
            $solicitud['MutualProductoSolicitud']['beneficiario_nombre'] = $persona['Persona']['nombre'];
            $solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc'] = $persona['Persona']['tipo_documento_desc']. '-' . $persona['Persona']['documento'];
            $solicitud['MutualProductoSolicitud']['beneficiario_tdoc'] = $persona['Persona']['tipo_documento_desc'];
            $solicitud['MutualProductoSolicitud']['beneficiario_ndoc'] = $persona['Persona']['documento'];
            $solicitud['MutualProductoSolicitud']['beneficiario_socio'] = $oSocio->getDatoSocio($solicitud['MutualProductoSolicitud']['socio_id']);
            $solicitud['MutualProductoSolicitud']['beneficiario_cuit_cuil'] = $persona['Persona']['cuit_cuil'];
            $solicitud['MutualProductoSolicitud']['beneficiario_conyuge'] = $persona['Persona']['nombre_conyuge'];
            $solicitud['MutualProductoSolicitud']['beneficiario_cuit_cuil_pick'] = '';
            if(strlen($persona['Persona']['cuit_cuil']) == 11):
                
                $prefix = substr($solicitud['MutualProductoSolicitud']['beneficiario_cuit_cuil'], 0,2);
                $sufix = substr($solicitud['MutualProductoSolicitud']['beneficiario_cuit_cuil'], -1);
                $number = substr($solicitud['MutualProductoSolicitud']['beneficiario_cuit_cuil'], 2,8);
                $solicitud['MutualProductoSolicitud']['beneficiario_cuit_cuil_pick'] = $prefix . "-".$number."-".$sufix;
                
                
            endif;
        
//	    	$solicitud['MutualProductoSolicitud']['beneficiario'] = $oSocio->getApenom($solicitud['MutualProductoSolicitud']['socio_id'],true);
//	    	$solicitud['MutualProductoSolicitud']['beneficiario_apenom'] = $oSocio->getApenom($solicitud['MutualProductoSolicitud']['socio_id'],false);
//	    	$solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc'] = $oSocio->getTdocNdoc($solicitud['MutualProductoSolicitud']['socio_id']);
//	    	$solicitud['MutualProductoSolicitud']['beneficiario_socio'] = $oSocio->getDatoSocio($solicitud['MutualProductoSolicitud']['socio_id']);
                
                
    	else:
            
            $persona = $oPersona->getPersona($solicitud['MutualProductoSolicitud']['persona_id']);

            $solicitud['MutualProductoSolicitud']['beneficiario'] = $persona['Persona']['tipo_documento_desc']. ' ' . $persona['Persona']['documento']." - ".$persona['Persona']['apenom'];
            $solicitud['MutualProductoSolicitud']['beneficiario_apenom'] = $persona['Persona']['apenom'];
            $solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc'] = $persona['Persona']['tipo_documento_desc']. '-' . $persona['Persona']['documento'];
            $solicitud['MutualProductoSolicitud']['beneficiario_tdoc'] = $persona['Persona']['tipo_documento_desc'];
            $solicitud['MutualProductoSolicitud']['beneficiario_ndoc'] = $persona['Persona']['documento'];
        
//	    	$solicitud['MutualProductoSolicitud']['beneficiario'] = $oPersona->getApenom($solicitud['MutualProductoSolicitud']['persona_id'],true);
//	    	$solicitud['MutualProductoSolicitud']['beneficiario_apenom'] = $oPersona->getApenom($solicitud['MutualProductoSolicitud']['persona_id'],false);
//	    	$solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc'] = $oPersona->getTdocNdoc($solicitud['MutualProductoSolicitud']['persona_id']);
//	    	$solicitud['MutualProductoSolicitud']['beneficiario_socio'] = null;
                
                
	    endif;
	    
	    $solicitud['MutualProductoSolicitud']['beneficiario_domicilio'] = null;
	    $solicitud['MutualProductoSolicitud']['beneficiario_telefonos'] = null;
	    $solicitud['MutualProductoSolicitud']['beneficiario_complementarios'] = null;

	    if($cargarDatosPersona){
	    	$solicitud['MutualProductoSolicitud']['beneficiario_domicilio'] = $oPersona->getDomicilioByPersonaId($solicitud['MutualProductoSolicitud']['persona_id'],false);
	    	$solicitud['MutualProductoSolicitud']['beneficiario_medio_contacto'] = $oPersona->getMediosContacto($solicitud['MutualProductoSolicitud']['persona_id']);
	    	$solicitud['MutualProductoSolicitud']['beneficiario_complementarios'] = $oPersona->getDatosComplementarios($solicitud['MutualProductoSolicitud']['persona_id'],false,$solicitud['MutualProductoSolicitud']['fecha']);
	    
	    	$datos = $oPersona->getDatosComplementarios($solicitud['MutualProductoSolicitud']['persona_id'],true,$solicitud['MutualProductoSolicitud']['fecha']);
	    	$solicitud['MutualProductoSolicitud']['beneficiario_estado_civil'] = $datos['estado_civil'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_sexo'] = $datos['sexo'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_fecha_nacimiento'] = $datos['fecha_nacimiento'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_edad'] = $datos['edad'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_cuit_cuil'] = $datos['cuit_cuil'];
	    
	    	$datos = $oPersona->getDomicilioByPersonaId($solicitud['MutualProductoSolicitud']['persona_id'],true);
	    	$solicitud['MutualProductoSolicitud']['beneficiario_calle'] = $datos['calle'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_numero_calle'] = $datos['numero_calle'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_piso'] = $datos['piso'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_dpto'] = $datos['dpto'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_barrio'] = $datos['barrio'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_localidad'] = $datos['localidad'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_cp'] = $datos['cp'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_provincia'] = $datos['provincia'];
	    
	    	$datos = $oPersona->getMediosContacto($solicitud['MutualProductoSolicitud']['persona_id'],true);
	    	$solicitud['MutualProductoSolicitud']['beneficiario_telefono_fijo'] = $datos['telefono_fijo'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_telefono_movil'] = $datos['telefono_movil'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_telefono_referencia'] = $datos['telefono_referencia'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_persona_referencia'] = $datos['persona_referencia'];
	    	$solicitud['MutualProductoSolicitud']['beneficiario_e_mail'] = $datos['e_mail'];
	    	// 	    	debug($solicitud);
	    	// 	    	exit;
	    
	    }	    
	    
	    
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBeneficio = new PersonaBeneficio();
		
		$beneficio = $oBeneficio->getBeneficio($solicitud['MutualProductoSolicitud']['persona_beneficio_id'],false,true);
		// debug($beneficio);
                
		$solicitud['MutualProductoSolicitud']['beneficio_activo'] = $beneficio['PersonaBeneficio']['activo'];
		$solicitud['MutualProductoSolicitud']['beneficio_str'] = $beneficio['PersonaBeneficio']['string'];
		$solicitud['MutualProductoSolicitud']['beneficio_banco'] = (isset($beneficio['PersonaBeneficio']['banco'])? $beneficio['PersonaBeneficio']['banco'] : NULL);
		$solicitud['MutualProductoSolicitud']['beneficio_acuerdo_debito'] = $beneficio['PersonaBeneficio']['acuerdo_debito'];
		
		$datos = $oBeneficio->getDatosBanco($solicitud['MutualProductoSolicitud']['persona_beneficio_id'],$solicitud['MutualProductoSolicitud']['fecha']);
		$solicitud['MutualProductoSolicitud']['beneficio_banco_id'] = $datos['banco_id'];
		$solicitud['MutualProductoSolicitud']['beneficio_banco'] = $datos['banco'];
		$solicitud['MutualProductoSolicitud']['beneficio_sucursal'] = $datos['sucursal'];
		$solicitud['MutualProductoSolicitud']['beneficio_cuenta'] = $datos['nro_cta_bco'];
		$solicitud['MutualProductoSolicitud']['beneficio_cbu'] = $datos['cbu'];
		$solicitud['MutualProductoSolicitud']['beneficio_ingreso'] = $datos['fecha_ingreso'];
		$solicitud['MutualProductoSolicitud']['beneficio_antiguedad'] = $datos['antiguedad'];
		$solicitud['MutualProductoSolicitud']['beneficio_legajo'] = $datos['legajo'];
		$solicitud['MutualProductoSolicitud']['beneficio_tipo_beneficio'] = $beneficio['PersonaBeneficio']['tipo'];
		$solicitud['MutualProductoSolicitud']['beneficio_nro_beneficio'] = $beneficio['PersonaBeneficio']['nro_beneficio'];
		$solicitud['MutualProductoSolicitud']['beneficio_nro_ley'] = $beneficio['PersonaBeneficio']['nro_ley'];
		$solicitud['MutualProductoSolicitud']['beneficio_sub_beneficio'] = $beneficio['PersonaBeneficio']['sub_beneficio'];
		$solicitud['MutualProductoSolicitud']['beneficio_cjpc_nro'] = $beneficio['PersonaBeneficio']['beneficio_cjpc_nro'];
		$solicitud['MutualProductoSolicitud']['beneficio_codigo_reparticion'] = $datos['codigo_reparticion'];		
                $solicitud['MutualProductoSolicitud']['beneficio_codigo_cjpc'] = $datos['codigo_cjpc'];	
		$solicitud['MutualProductoSolicitud']['beneficio_tarjeta_titular'] = $beneficio['PersonaBeneficio']['tarjeta_titular'];
		$solicitud['MutualProductoSolicitud']['beneficio_tarjeta_numero'] = $beneficio['PersonaBeneficio']['tarjeta_numero'];
		$solicitud['MutualProductoSolicitud']['beneficio_tarjeta_debito'] = $beneficio['PersonaBeneficio']['tarjeta_debito'];
		
		
// 		debug($solicitud);
// 		exit;
		
		
    	$solicitud['MutualProductoSolicitud']['organismo'] = $oBeneficio->getCodigoOrganismo($solicitud['MutualProductoSolicitud']['persona_beneficio_id']);
    	$solicitud['MutualProductoSolicitud']['organismo_desc'] = $oBeneficio->getOrganismo($solicitud['MutualProductoSolicitud']['persona_beneficio_id']);
    	$solicitud['MutualProductoSolicitud']['turno'] = $oBeneficio->getTurno($solicitud['MutualProductoSolicitud']['persona_beneficio_id']);
    	$solicitud['MutualProductoSolicitud']['turno_desc'] = $oBeneficio->getTurnoDescripcion($solicitud['MutualProductoSolicitud']['persona_beneficio_id']);
		
    	$solicitud['MutualProductoSolicitud']['tipo_numero'] = $solicitud['MutualProductoSolicitud']['tipo_orden_dto']." #".$solicitud['MutualProductoSolicitud']['id'];
		
    	$solicitud['MutualProductoSolicitud']['total_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['importe_total']);
    	
    	$solicitud['MutualProductoSolicitud']['total_importe_solicitado_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['importe_solicitado']);
    	$solicitud['MutualProductoSolicitud']['total_importe_percibido_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['importe_percibido']);
    	$solicitud['MutualProductoSolicitud']['total_cuota_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['importe_cuota']);
    	$solicitud['MutualProductoSolicitud']['cantidad_cuota_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['cuotas'],false);
    	$solicitud['MutualProductoSolicitud']['cuotas_print'] = str_pad($solicitud['MutualProductoSolicitud']['cuotas'],2,0,STR_PAD_LEFT);
    	 

        $solicitud['MutualProductoSolicitud']['proveedor_saldo_operativo'] = 0.00;
        if($solicitud['MutualProductoSolicitud']['prestamo'] === '1'):
            $solicitud['MutualProductoSolicitud']['proveedor_saldo_operativo'] = $oProveedor->proveedorSaldoOperativo($solicitud['MutualProductoSolicitud']['proveedor_id']);
        endif;
        
        
    	$solicitud = $this->setBarcode($solicitud);
    	
    	App::import('Model','Mutual.MutualProductoSolicitudPago');
    	$oMPSP = new MutualProductoSolicitudPago();    	
    	$pagos = $oMPSP->getPagosBySolicitud($solicitud['MutualProductoSolicitud']['id']);
    	$pagos = Set::extract('{n}.MutualProductoSolicitudPago',$pagos);
    	$solicitud['MutualProductoSolicitud']['MutualProductoSolicitudPago'] = $pagos;
    	
    	App::import('Model','Mutual.OrdenDescuento');
    	$oOdto = new OrdenDescuento();     

    	if($solicitud['MutualProductoSolicitud']['orden_descuento_id']!=0)$solicitud['MutualProductoSolicitud']['estado_orden_dto'] = $oOdto->isActiva($solicitud['MutualProductoSolicitud']['orden_descuento_id']);
    	$solicitud['MutualProductoSolicitud']['estado_orden_dto'] = 1;
        
        ########################################################################
        # CARGAR ORDENES NOVADAS
        ########################################################################
        if(!empty($solicitud['MutualProductoSolicitud']['orden_descuento_id'])){
            
            $orden = $this->get_orden_descuento_emitida($solicitud['MutualProductoSolicitud']['id']);
//            debug($orden['OrdenDescuento']);
//            $orden = $oOdto->getOrdenByNumero($solicitud['MutualProductoSolicitud']['id'], $solicitud['MutualProductoSolicitud']['tipo_orden_dto'], $solicitud['MutualProductoSolicitud']['tipo_producto'],FALSE,TRUE,TRUE,$soloActivo);
            $solicitud['MutualProductoSolicitud']['orden_descuento_id'] = $orden['OrdenDescuento']['id'];
//            $solicitud['MutualProductoSolicitud']['importe_total'] = $orden['OrdenDescuento']['importe_total'];
//            $solicitud['MutualProductoSolicitud']['importe_cuota'] = $orden['OrdenDescuento']['importe_cuota'];
//            $solicitud['MutualProductoSolicitud']['cuotas'] = $orden['OrdenDescuento']['cuotas'];
            
        }
    	
        if(!empty($solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'])){
            
//            $tipoProducto = $oOdto->GlobalDato("concepto_4", $solicitud['MutualProductoSolicitud']['tipo_producto']);
//            $orden = $oOdto->getOrdenByNumero($solicitud['MutualProductoSolicitud']['id'], $solicitud['MutualProductoSolicitud']['tipo_orden_dto'],$tipoProducto);
            
            $orden = $this->get_orden_descuento_emitida($solicitud['MutualProductoSolicitud']['id'],NULL,'orden_descuento_seguro_id');
////            debug($orden['OrdenDescuento']);
            $solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'] = $orden['OrdenDescuento']['id'];
        }   
        
        
        
        
//    	//verifico que la orden no este novada
//    	$orden = $oOdto->read(null,$solicitud['MutualProductoSolicitud']['orden_descuento_id']);
//    	$novacion = false;
//    	while(!$novacion){
//    		if($orden['OrdenDescuento']['activo'] == 0 && !empty($orden['OrdenDescuento']['nueva_orden_descuento_id']) && empty($orden['OrdenDescuento']['anterior_orden_descuento_id'])){
//    			$orden = $oOdto->read(null,$orden['OrdenDescuento']['nueva_orden_descuento_id']);
//    		}else{
//    			$novacion = true;
//    		}
//    	}
//    	if($solicitud['MutualProductoSolicitud']['orden_descuento_id']!= $orden['OrdenDescuento']['id']){
//    		$solicitud['MutualProductoSolicitud']['orden_descuento_id'] = $orden['OrdenDescuento']['id'];
//    		if($solicitud['MutualProductoSolicitud']['importe_total'] != $orden['OrdenDescuento']['importe_total']){
//    			$solicitud['MutualProductoSolicitud']['importe_total'] = $orden['OrdenDescuento']['importe_total'];
//    		}
//    		if($solicitud['MutualProductoSolicitud']['importe_cuota'] != $orden['OrdenDescuento']['importe_cuota']){
//    			$solicitud['MutualProductoSolicitud']['importe_cuota'] = $orden['OrdenDescuento']['importe_cuota'];
//    		} 
//    		if($solicitud['MutualProductoSolicitud']['cuotas'] != $orden['OrdenDescuento']['cuotas']){
//    			$solicitud['MutualProductoSolicitud']['cuotas'] = $orden['OrdenDescuento']['cuotas'];
//    		}    		   		 
//    	}
        
//    	$orden = $oOdto->read(null,$solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id']);
//    	$novacion = false;
//    	while(!$novacion){
//    		if($orden['OrdenDescuento']['activo'] == 0 && !empty($orden['OrdenDescuento']['nueva_orden_descuento_id']) && empty($orden['OrdenDescuento']['anterior_orden_descuento_id'])){
//    			$orden = $oOdto->read(null,$orden['OrdenDescuento']['nueva_orden_descuento_id']);
//    		}else{
//    			$novacion = true;
//    		}
//    	}
//    	if($solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id']!= $orden['OrdenDescuento']['id']){
//    		$solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'] = $orden['OrdenDescuento']['id'];
//    		if($solicitud['MutualProductoSolicitud']['importe_total'] != $orden['OrdenDescuento']['importe_total']){
//    			$solicitud['MutualProductoSolicitud']['importe_total'] = $orden['OrdenDescuento']['importe_total'];
//    		}
//    		if($solicitud['MutualProductoSolicitud']['importe_cuota'] != $orden['OrdenDescuento']['importe_cuota']){
//    			$solicitud['MutualProductoSolicitud']['importe_cuota'] = $orden['OrdenDescuento']['importe_cuota'];
//    		} 
//    		if($solicitud['MutualProductoSolicitud']['cuotas'] != $orden['OrdenDescuento']['cuotas']){
//    			$solicitud['MutualProductoSolicitud']['cuotas'] = $orden['OrdenDescuento']['cuotas'];
//    		}    		   		 
//    	}
        
    	 
    	if($cargarOrdenDto):
	    	#BUSCO LA ORDEN DE DESCUENTO ASOCIADA
	    	$orden = $oOdto->getOrden($solicitud['MutualProductoSolicitud']['orden_descuento_id']);
	    	$solicitud['MutualProductoSolicitud']['OrdenDescuento'] = $orden['OrdenDescuento'];
	    	if(!empty($solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id'])){
	    		$ordenSeg = $oOdto->getOrden($solicitud['MutualProductoSolicitud']['orden_descuento_seguro_id']);
	    		$solicitud['MutualProductoSolicitud']['OrdenDescuentoSeguro'] = $ordenSeg['OrdenDescuento'];	    		
	    	}
    	endif;
    	
    	
//     	$solicitud['MutualProductoSolicitud']['inicia_en'] = parent::periodo($solicitud['MutualProductoSolicitud']['periodo_ini']);
    	
		if(isset($solicitud['MutualProductoSolicitud']['orden_pago_id'])):
			$solicitud['MutualProductoSolicitud']['orden_pago_link'] = '';
			if($solicitud['MutualProductoSolicitud']['orden_pago_id'] > 0):
				$solicitud['MutualProductoSolicitud']['orden_pago_link'] = $this->getOrdenPagoLink($solicitud['MutualProductoSolicitud']['orden_pago_id']);
			endif;
		endif;
		
		$solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] = 0;
		if($solicitud['MutualProductoSolicitud']['aprobada'] == 0){
			App::import('Model', 'Proveedores.ProveedorVencimiento');
			$oVTOS = new ProveedorVencimiento(null);
			$fechaPago = date('Y-m-d');
			$vtos = $oVTOS->calculaVencimiento($solicitud['MutualProductoSolicitud']['proveedor_id'],$solicitud['MutualProductoSolicitud']['persona_beneficio_id'],$fechaPago);
			$solicitud['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
			$solicitud['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
			$solicitud['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
			App::import('Model','mutual.Liquidacion');
			$oLQ = new Liquidacion();
			$solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] = $oLQ->isCerrada($solicitud['MutualProductoSolicitud']['organismo'], $solicitud['MutualProductoSolicitud']['periodo_ini']);
		}else{
			App::import('Model','mutual.Liquidacion');
			$oLQ = new Liquidacion();
			$solicitud['MutualProductoSolicitud']['bloqueo_liquidacion'] = $oLQ->isCerrada($solicitud['MutualProductoSolicitud']['organismo'], $solicitud['MutualProductoSolicitud']['periodo_ini']);
		}		
		$solicitud['MutualProductoSolicitud']['inicia_en'] = parent::periodo($solicitud['MutualProductoSolicitud']['periodo_ini']);

		
		$solicitud['MutualProductoSolicitud']['vendedor_nombre'] = "";
		$solicitud['MutualProductoSolicitud']['vendedor_remito'] = "";
                $solicitud['MutualProductoSolicitud']['vendedor_nombre_min'] = "";
                $solicitud['MutualProductoSolicitud']['vendedor_cuit'] = "";
                $solicitud['MutualProductoSolicitud']['vendedor_apenom'] = '';
		if(!empty($solicitud['MutualProductoSolicitud']['vendedor_id'])){
			App::import('model','ventas.Vendedor');
			$oVENDEDOR = new Vendedor();
			$vendedor = $oVENDEDOR->getVendedor($solicitud['MutualProductoSolicitud']['vendedor_id']);
			$solicitud['MutualProductoSolicitud']['vendedor_nombre'] = "#".$solicitud['MutualProductoSolicitud']['vendedor_id'] . " - " . $vendedor['Persona']['tdoc_ndoc_apenom'];
			$solicitud['MutualProductoSolicitud']['vendedor_nombre_min'] = "#".$solicitud['MutualProductoSolicitud']['vendedor_id'] . " - " . $vendedor['Persona']['apenom'];
			$solicitud['MutualProductoSolicitud']['vendedor_cuit'] = $vendedor['Persona']['cuit_cuil'];
                        $solicitud['MutualProductoSolicitud']['vendedor_apenom'] = $vendedor['Persona']['apellido']." ".$vendedor['Persona']['nombre'];

			if(!empty($solicitud['MutualProductoSolicitud']['vendedor_remito_id'])){
				App::import('model','ventas.VendedorRemito');
				$oREMITO = new VendedorRemito();
				$remito = $oREMITO->read(null,$solicitud['MutualProductoSolicitud']['vendedor_remito_id']);
                
                $solicitud['MutualProductoSolicitud']['vendedor_remito_nro'] = $remito['VendedorRemito']['id'];
                $solicitud['MutualProductoSolicitud']['vendedor_remito_user_created'] = $remito['VendedorRemito']['user_created'];
                $solicitud['MutualProductoSolicitud']['vendedor_remito_created'] = $remito['VendedorRemito']['created'];
				
                $solicitud['MutualProductoSolicitud']['vendedor_remito'] = "#" . $remito['VendedorRemito']['id'] . " - " . $remito['VendedorRemito']['user_created'] . " (" . $remito['VendedorRemito']['created'] .")";
			}
		}
		
		$solicitud['MutualProductoSolicitud']['proveedor_plan'] = "";
                $solicitud['MutualProductoSolicitud']['proveedor_plan_descripcion'] = NULL;
                $solicitud['MutualProductoSolicitud']['proveedor_plan_activo'] = NULL;
                $solicitud['MutualProductoSolicitud']['proveedor_plan_producto'] = NULL;
                $solicitud['MutualProductoSolicitud']['proveedor_plan_string'] = NULL;
                $solicitud['MutualProductoSolicitud']['proveedor_interes_moratorio'] = NULL;
                $solicitud['MutualProductoSolicitud']['proveedor_costo_cancela'] = NULL;
                
                
        $MODELO_DEFAULT = parent::GlobalDato('concepto_2', 'MUTUIMPR1001');  
        
               
        $solicitud['MutualProductoSolicitud']['proveedor_plan_modelo_solicitud'] = ( !empty($MODELO_DEFAULT) ? $MODELO_DEFAULT : "imprimir_credito_mutual_pdf");

		if(!empty($solicitud['MutualProductoSolicitud']['proveedor_plan_id'])){
			App::import('model','proveedores.ProveedorPlan');
			$oPLAN = new ProveedorPlan();
                        $oPLAN->unbindModel(array('hasMany' => array('ProveedorPlanOrganismo','ProveedorPlanAnexo')));
			$plan = $oPLAN->getPlan($solicitud['MutualProductoSolicitud']['proveedor_plan_id']);

                        if(!empty($plan)){
                            $plan = $oPLAN->arma_datos($plan);
                            $solicitud['MutualProductoSolicitud']['proveedor_plan'] = "#".$plan['ProveedorPlan']['id']." ".$plan['Proveedor']['razon_social_resumida'] . " - " . $plan['ProveedorPlan']['descripcion'];

                            $solicitud['MutualProductoSolicitud']['proveedor_plan_descripcion'] = $plan['ProveedorPlan']['descripcion'];
                            $solicitud['MutualProductoSolicitud']['proveedor_plan_activo'] = $plan['ProveedorPlan']['activo'];
                            $solicitud['MutualProductoSolicitud']['proveedor_plan_producto'] = $plan['ProveedorPlan']['tipo_producto'];
                            $solicitud['MutualProductoSolicitud']['proveedor_plan_producto_descripcion'] = $plan['ProveedorPlan']['producto'];
                            $solicitud['MutualProductoSolicitud']['proveedor_plan_string'] = $plan['ProveedorPlan']['cadena'];                          

                            $solicitud['MutualProductoSolicitud']['proveedor_plan_modelo_solicitud'] = $plan['ProveedorPlan']['modelo_solicitud'];
                            $solicitud['MutualProductoSolicitud']['proveedor_plan_modelo_solicitud_2'] = $oPLAN->get_modelo_print($plan['ProveedorPlan']['id']);
                            $solicitud['MutualProductoSolicitud']['proveedor_plan_anexos'] = $oPLAN->get_anexos_print($plan['ProveedorPlan']['id']);
                            $solicitud['MutualProductoSolicitud']['proveedor_interes_moratorio'] = $plan['ProveedorPlan']['interes_moratorio'];
                            $solicitud['MutualProductoSolicitud']['proveedor_costo_cancela'] = $plan['ProveedorPlan']['costo_cancelacion_anticipada'];                            
                        }
                        
//                        
//                        debug($plan);
//                        exit;
                        
//                        debug($plan);
		}
                
                if(!empty($solicitud['MutualProductoSolicitud']['mutual_producto_id'])){
                    App::import('model','mutual.MutualProducto');
                    $oMPROD = new MutualProducto();
                    $solicitud['MutualProductoSolicitud']['proveedor_plan_modelo_solicitud'] = $oMPROD->get_modelo_print($solicitud['MutualProductoSolicitud']['mutual_producto_id']);
                    $solicitud['MutualProductoSolicitud']['proveedor_plan_anexos'] = $oMPROD->get_anexos_print($solicitud['MutualProductoSolicitud']['mutual_producto_id']);
                    
                }
                

		$solicitud['MutualProductoSolicitud']['fdoas'] = 0;
		$solicitud['MutualProductoSolicitud']['fdoas_total'] = null;
		$solicitud['MutualProductoSolicitud']['fdoas_total_cuota'] = null;
		$solicitud['MutualProductoSolicitud']['fdoas_total_cuota_cantidad'] = null;		
		$solicitud['MutualProductoSolicitud']['fdoas_total_letras'] = null;
		$solicitud['MutualProductoSolicitud']['fdoas_total_cuota_letras'] = null;
		$solicitud['MutualProductoSolicitud']['fdoas_total_cuota_cantidad_letras'] = null;
		$TIPO_PRODUCTO_SIGEM = parent::GlobalDato('concepto_4',$solicitud['MutualProductoSolicitud']['tipo_producto']);
		$importeSeguro = 0;
		if(!empty($TIPO_PRODUCTO_SIGEM)) $importeSeguro = parent::GlobalDato('decimal_1',$TIPO_PRODUCTO_SIGEM);
		 
		if(!empty($importeSeguro)){
			$solicitud['MutualProductoSolicitud']['fdoas'] = 1;
			$importeSeguroTotal = $importeSeguro * $solicitud['MutualProductoSolicitud']['cuotas'];
			
			$solicitud['MutualProductoSolicitud']['fdoas_total'] = $importeSeguroTotal;
			$solicitud['MutualProductoSolicitud']['fdoas_total_cuota'] = $importeSeguro;
			$solicitud['MutualProductoSolicitud']['fdoas_total_cuota_cantidad'] = $solicitud['MutualProductoSolicitud']['cuotas'];			
			
			$solicitud['MutualProductoSolicitud']['fdoas_total_letras'] = parent::num2letras($importeSeguroTotal);
			$solicitud['MutualProductoSolicitud']['fdoas_total_cuota_letras'] = parent::num2letras($importeSeguro);
			$solicitud['MutualProductoSolicitud']['fdoas_total_cuota_cantidad_letras'] = parent::num2letras($solicitud['MutualProductoSolicitud']['cuotas'],false);
				
			
// 			$TIPO_ORDEN_SIGEM = parent::GlobalDato('concepto_3', $TIPO_PRODUCTO_SIGEM);
// 			$TIPO_ORDEN_SIGEM = (empty($TIPO_ORDEN_SIGEM) ? 'EXPTE' : $TIPO_ORDEN_SIGEM);
		
// 			$proveedorSeguroId = parent::GlobalDato('entero_1',$TIPO_PRODUCTO_SIGEM);
// 			$importeSeguroTotal = $importeSeguro * $orden['MutualProductoSolicitud']['cuotas'];		
		}
		
		//BUSCO SI TIENE DOCUMENTOS ADJUNTOS
		$solicitud['MutualProductoSolicitudDocumento'] = array();
//		$sql = "
//                    SELECT * FROM (
//                        SELECT 
//                            m.id
//                            ,m.file_name
//                            ,m.file_type
//                            , g.concepto_1 as concepto_1
//                        FROM mutual_producto_solicitud_documentos m 
//                        LEFT JOIN global_datos g ON m.codigo_documento = g.id
//                        WHERE mutual_producto_solicitud_id = " . $solicitud['MutualProductoSolicitud']['id'].
//                    ") as MutualProductoSolicitudDocumento" ;
//		$solicitud['MutualProductoSolicitudDocumento'] = $this->query($sql);
                
                //refactoring
                $sql = "select MutualProductoSolicitudDocumento.*,GlobalDato.concepto_1 from mutual_producto_solicitud_documentos MutualProductoSolicitudDocumento
                        left join global_datos as GlobalDato on GlobalDato.id = MutualProductoSolicitudDocumento.codigo_documento
                        where mutual_producto_solicitud_id = " . $solicitud['MutualProductoSolicitud']['id'];
                $documentos = $this->query($sql);
                if(!empty($documentos)){
                    $solicitud['MutualProductoSolicitudDocumento'] = $documentos;
                }
                
                
        
        // ARMO LA FECHA        
        App::import('Helper', 'Util');
        $oUT = new UtilHelper();                
        $solicitud['MutualProductoSolicitud']['fecha_emision_str'] = array(
            
            'dia' => array('numero' => date('d',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),'string' => trim(parent::num2letras(date('d',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),false))),
            'mes' => array('numero' => date('m',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),'string' => $oUT->mesToStr(date('m',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),true)),
            'anio' => array('numero' => date('Y',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),'string' => trim(parent::num2letras(date('Y',strtotime($solicitud['MutualProductoSolicitud']['fecha'])),false))),
            
        );
        $mkTFC = mktime(0,0,0,date('m',strtotime($solicitud['MutualProductoSolicitud']['primer_vto_socio'])),date('d',strtotime($solicitud['MutualProductoSolicitud']['primer_vto_socio'])),date('Y',strtotime($solicitud['MutualProductoSolicitud']['primer_vto_socio'])));
        $mktVto = parent::addMonthToDate($mkTFC,$solicitud['MutualProductoSolicitud']['cuotas']);
//         $vencimientoPagare = date('Y-m-d',$mktVto);
        $vencimientoPagare = date('Y-m-d',$mkTFC); #fecha vto en la primer cuota
        $solicitud['MutualProductoSolicitud']['vencimiento_pagare'] = $vencimientoPagare;
        $solicitud['MutualProductoSolicitud']['vencimiento_pagare_str'] = array(
            
            'dia' => array('numero' => date('d',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),'string' => trim(parent::num2letras(date('d',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),false))),
            'mes' => array('numero' => date('m',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),'string' => $oUT->mesToStr(date('m',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),true)),
            'anio' => array('numero' => date('Y',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),'string' => trim(parent::num2letras(date('Y',strtotime($solicitud['MutualProductoSolicitud']['vencimiento_pagare'])),false))),
            
        );        
        
//        //ANALIZO EL HISTORIAL
//        debug($solicitud['MutualProductoSolicitudDocumento']);
//        exit;
        
        
        //CARGO EL HISTORIAL
    	App::import('Model','Mutual.MutualProductoSolicitudEstado');
    	$oMPSE = new MutualProductoSolicitudEstado();         
        $estados = $oMPSE->getEstadosBySolicitud($solicitud['MutualProductoSolicitud']['id']);
    	$estados = Set::extract('{n}.MutualProductoSolicitudEstado',$estados);
    	$solicitud['MutualProductoSolicitud']['MutualProductoSolicitudEstado'] = $estados;
        
        
        #######################################################################################
        # ARMAR EL DETALLE DE LOS VENCIMIENTOS
        #######################################################################################
        $planVencimientos = array();
        $mkIni = mktime(0,0,0,substr($solicitud['MutualProductoSolicitud']['periodo_ini'],4,2),1,substr($solicitud['MutualProductoSolicitud']['periodo_ini'],0,4));	
        $periodoInicio = $solicitud['MutualProductoSolicitud']['periodo_ini'];
        for( $i=1 ; $i <= $solicitud['MutualProductoSolicitud']['cuotas']; $i++ ){
            $planVencimientos[$i] = array(
                'periodo' => $periodoInicio,
                'importe_cuota' => $solicitud['MutualProductoSolicitud']['importe_cuota'],
                'importe_foas' => $solicitud['MutualProductoSolicitud']['fdoas_total_cuota']
			);
			$solicitud['MutualProductoSolicitud']['finaliza_en'] = parent::periodo($periodoInicio);
            $periodoInicio = date('Ym',$this->addMonthToDate($mkIni,$i));
		}
		
        $solicitud['MutualProductoSolicitud']['cronograma_de_vencimientos'] = $planVencimientos;
//        debug($solicitud['MutualProductoSolicitud']);
//        exit;
        
        $totalCancela = 0;
        if(!empty($solicitud['MutualProductoSolicitudCancelacion'])){
            $cancelaciones = array();
            App::import('model','mutual.CancelacionOrden');
            $oCANC = new CancelacionOrden();
            foreach($solicitud['MutualProductoSolicitudCancelacion'] as $cancelacionId){
                $cancelacion = $oCANC->read(null,$cancelacionId['cancelacion_orden_id']);
                $totalCancela += $cancelacion['CancelacionOrden']['importe_proveedor'];
            }
        }        
        $solicitud['MutualProductoSolicitud']['total_cancelacion'] = $totalCancela;
        
//         debug($solicitud);
        
//         $this->armaDatosOptimizado($solicitud['MutualProductoSolicitud']['id'],false,$cargarDatosPersona);
        
//         exit;

        
        if( !$solicitud['MutualProductoSolicitud']['aprobada'] && isset($solicitud['MutualProductoSolicitud']['detalle_calculo_plan']) && !empty($solicitud['MutualProductoSolicitud']['detalle_calculo_plan'])){
            
            App::import('model', 'proveedores.MetodoCalculoCuota');
            $oCALC = new MetodoCalculoCuota();
            $solicitud['MutualProductoSolicitud']['detalle_calculo_plan'] = $oCALC->setPeriodosAndVencimientos(
                json_decode($solicitud['MutualProductoSolicitud']['detalle_calculo_plan']),
                $solicitud['MutualProductoSolicitud']['periodo_ini'],
                $solicitud['MutualProductoSolicitud']['primer_vto_socio'],
                $solicitud['MutualProductoSolicitud']['primer_vto_proveedor'],
                $this,
                TRUE
                );
        }	

        return $solicitud;
        
    }
    
    
    function setBarcode($solicitud){
    	$barCode = parent::fill($solicitud['MutualProductoSolicitud']['id'],7,'0','L');
    	$barCode .= parent::fill($solicitud['MutualProductoSolicitud']['socio_id'],7,'0','L');
    	$barCode .= substr($solicitud['MutualProductoSolicitud']['tipo_producto'],8,4);
    	$barCode .= parent::fill($solicitud['MutualProductoSolicitud']['importe_total']*100,7,'0','L');
    	$barCode .= parent::fill($solicitud['MutualProductoSolicitud']['cuotas'],2,'0','L');
    	$barCode .= date('Ymd',strtotime($solicitud['MutualProductoSolicitud']['fecha_pago']));
    	$barCode .= parent::digitoVerificador($barCode);
    	$solicitud['MutualProductoSolicitud']['barcode'] = $barCode;
    	return $solicitud;
    }
    
    function baja($id){
    	$solicitud = $this->read(null,$id);
    	if(empty($solicitud)) return null;
    	$solicitud = $this->armaDatos($solicitud);
    	App::import('Model','Mutual.OrdenDescuento');
    	$oOdto = new OrdenDescuento();    	
    	if($oOdto->baja($solicitud['MutualProductoSolicitud']['OrdenDescuento']['id'])){
    		return parent::del($solicitud['MutualProductoSolicitud']['id']);
    	}else{
    		return false;
    	}
    }
    
    function actualizarExpediente($id,$idexp){
    	$data = $this->read(null,$id);
    	$data['MutualProductoSolicitud']['expediente_id'] = $idexp;
    	return parent::save($data);
    }
    
    
    /**
     * modifica el importe de una orden permanente y actualiza el valor a las cuotas que estan adeudadas
     * 
     * @param $id
     * @param $nuevoImporte
     * @param $reprocesaDeuda
     * @return unknown_type
     */
    function modificarImportePermanente($id,$nuevoImporte,$reprocesaDeuda=false,$periodoDesde=null){

    	$data = $this->read(null,$id);
    	
    	App::import('Model','Mutual.OrdenDescuento');
    	$oOdto = new OrdenDescuento();    	
    	
    	//verifico que la orden no este novada
    	$orden = $oOdto->read(null,$data['MutualProductoSolicitud']['orden_descuento_id']);
    	$novacion = false;
    	while(!$novacion){
    		if($orden['OrdenDescuento']['activo'] == 0 && !empty($orden['OrdenDescuento']['nueva_orden_descuento_id'])){
    			$orden = $oOdto->read(null,$orden['OrdenDescuento']['nueva_orden_descuento_id']);
    		}else{
    			$novacion = true;
    		}
    	}
    	if($data['MutualProductoSolicitud']['orden_descuento_id']!= $orden['OrdenDescuento']['id']){
    		$data['MutualProductoSolicitud']['orden_descuento_id'] = $orden['OrdenDescuento']['id'];
    		if($data['MutualProductoSolicitud']['importe_total'] != $orden['OrdenDescuento']['importe_total']){
    			$data['MutualProductoSolicitud']['importe_total'] = $orden['OrdenDescuento']['importe_total'];
    		}
    		if($data['MutualProductoSolicitud']['importe_cuota'] != $orden['OrdenDescuento']['importe_cuota']){
    			$data['MutualProductoSolicitud']['importe_cuota'] = $orden['OrdenDescuento']['importe_cuota'];
    		}
    		if($data['MutualProductoSolicitud']['cuotas'] != $orden['OrdenDescuento']['cuotas']){
    			$data['MutualProductoSolicitud']['cuotas'] = $orden['OrdenDescuento']['cuotas'];
    		}
    	}    	
    	
    	
    	$data['MutualProductoSolicitud']['importe_total'] = $nuevoImporte;
    	$data['MutualProductoSolicitud']['importe_cuota'] = $nuevoImporte;
    	
    	if(!parent::save($data)) return false;
    	
   	
    	
    	$tipo = $data['MutualProductoSolicitud']['tipo_orden_dto'];
    	$tipoProducto = $data['MutualProductoSolicitud']['tipo_producto'];
    	
//     	$orden = $oOdto->getOrdenByNumero($id,$tipo,$tipoProducto,true,false,false);
    	$orden = $oOdto->getOrden($data['MutualProductoSolicitud']['orden_descuento_id'], null, false, false);

    	if(!empty($orden)):
    	
    		$orden['OrdenDescuento']['importe_total'] = $nuevoImporte;
    		$orden['OrdenDescuento']['importe_cuota'] = $nuevoImporte;    	
    	
    		if(!empty($orden['OrdenDescuentoCuota']) && $reprocesaDeuda):
        		foreach($orden['OrdenDescuentoCuota'] as $idx => $cuota){
    				if($cuota['estado'] == 'A' && $cuota['periodo'] >= (!empty($periodoDesde) ? $periodoDesde : '999999')) $cuota['importe'] = $nuevoImporte;
    				$orden['OrdenDescuentoCuota'][$idx] = $cuota;
    			}    		
    		
    		endif;
    		
    		if(!$oOdto->saveAll($orden)) return false;
    	
    	endif;
    	
    	return true;
    	
    }
    
    
    
    function replaceCampos($str,$solicitud_id){
    	$txt = "";
    	$this->recursive = 3;
    	$this->bindModel(array('belongsTo' => array('PersonaBeneficio')));
    	$this->Socio->bindModel(array('belongsTo' => array('Persona')));
    	$this->MutualProducto->bindModel(array('belongsTo' => array('Proveedor')));
    	
    	$sol = $this->read(null,$solicitud_id);
    	
    	$glb = $this->getGlobalDato('concepto_2',$sol['MutualProductoSolicitud']['tipo_orden_dto']);
    	
    	$txt = str_replace("#TIPO_ORDEN_DTO#",$glb['GlobalDato']['concepto_2'],$str);
    	$txt = str_replace("#NRO_SOLICITUD#",$sol['MutualProductoSolicitud']['id'],$txt);
    	
    	$txt = str_replace("#FECHA_SOLICITUD#",date('d/m/Y',strtotime($sol['MutualProductoSolicitud']['fecha'])),$txt);
    	$txt = str_replace("#FECHA_PAGO#",date('d/m/Y',strtotime($sol['MutualProductoSolicitud']['fecha_pago'])),$txt);
    	
		$txt = str_replace("#APELLIDO#",$sol['Socio']['Persona']['apellido'],$txt);
//		$txt = str_replace("#APELLIDO#",utf8_encode($sol['Socio']['Persona']['apellido']),$txt);
		$txt = str_replace("#NOMBRE#",$sol['Socio']['Persona']['nombre'],$txt);
		
		$glb = $this->getGlobalDato('concepto_1',$sol['Socio']['Persona']['tipo_documento']);
		
		$txt = str_replace("#TDOCNDOC#",$glb['GlobalDato']['concepto_1'].' - ' .$sol['Socio']['Persona']['documento'],$txt);    	
    	
		$glb = $this->getGlobalDato('concepto_1',$sol['MutualProductoSolicitud']['tipo_producto']);
		
		$producto = $sol['MutualProducto']['Proveedor']['razon_social_resumida'] .' - '.$glb['GlobalDato']['concepto_1'];
		$txt = str_replace("#PRODUCTO#",$producto,$txt);
		
		$txt = str_replace("#NRO_REFERENCIA_PROVEEDOR#",($sol['MutualProductoSolicitud']['nro_referencia_proveedor']!= '' ? " - REF: ". $sol['MutualProductoSolicitud']['nro_referencia_proveedor'] : ''),$txt);
		
		$txt = str_replace("#IMPORTE_TOTAL#",number_format($sol['MutualProductoSolicitud']['importe_total'],2),$txt);
		$txt = str_replace("#CUOTAS#",$sol['MutualProductoSolicitud']['cuotas'],$txt);
		$txt = str_replace("#IMPORTE_CUOTA#",number_format($sol['MutualProductoSolicitud']['importe_cuota'],2),$txt);
		
		$txt = str_replace("#OBSERVACIONES#",$sol['MutualProductoSolicitud']['observaciones'],$txt);
		
		$glb = $this->getGlobalDato('concepto_1,concepto_2',$sol['PersonaBeneficio']['codigo_beneficio']);
		$strBen = "";
		
		
		
		switch ($glb['GlobalDato']['concepto_2']) {
			case 'AC':
				$bco = $this->getBanco($sol['PersonaBeneficio']['banco_id']);
				$strBen = $glb['GlobalDato']['concepto_1'] . ' - ' . $bco['Banco']['nombre'] . ' - CBU: '.$sol['PersonaBeneficio']['cbu'] ;
				break;
			case 'JP':
				$strBen = $glb['GlobalDato']['concepto_1'] . ' - NRO.: ' . $sol['PersonaBeneficio']['nro_beneficio'] . ' - LEY: '.$sol['PersonaBeneficio']['nro_ley'] ;
				break;
			case 'JN':
				$strBen = $glb['GlobalDato']['concepto_1'] . ' - NRO.: ' . $sol['PersonaBeneficio']['nro_beneficio'];
				break;
				
		}		
		$txt = str_replace("#BENEFICIO#",$strBen,$txt);
		
		$txt = str_replace("#PERIODO_INI#",$this->periodo($sol['MutualProductoSolicitud']['periodo_ini'],true),$txt);
		$txt = str_replace("#VTO_PRIMER_CUOTA#",date('d/m/Y',strtotime($sol['MutualProductoSolicitud']['primer_vto_socio'])),$txt);
		$txt = str_replace("#ORDEN_DTO_ID#",$sol['MutualProductoSolicitud']['orden_descuento_id'],$txt);
		
		$txt = str_replace("#PROVEEDOR#",$sol['MutualProducto']['Proveedor']['razon_social'],$txt);
		$txt = str_replace("#DOMI_PAGO_PAGARE#",$sol['MutualProducto']['Proveedor']['calle'] . ' ' . $sol['MutualProducto']['Proveedor']['numero_calle'] . ' - ' . $sol['MutualProducto']['Proveedor']['localidad'],$txt);
		$txt = str_replace("#IMPORTE_TOTAL_LETRAS#",$this->num2letras($sol['MutualProductoSolicitud']['importe_total']),$txt);
		
//    	debug($sol);
//    	exit;
//		$txt = utf8_encode($txt);
    	return $txt;

    	
    }
    
    /**
     * Aprobar Solicitud de Consumo o Servicio
     * Si la orden trae importe cero es una orden solamente y no se tiene que generar la orden de descuento
     * @param unknown_type $id
     */
    function aprobar_opago($datos){
    	$id = $datos['MutualProductoSolicitud']['id'];
    	
    	if(!$this->isAprobada($id)){
    		
			$orden = $this->getOrden($id);
			
			$orden['MutualProductoSolicitud']['nro_referencia_proveedor'] = (isset($datos['MutualProductoSolicitud']['nro_referencia_proveedor']) ? $datos['MutualProductoSolicitud']['nro_referencia_proveedor'] : NULL);
                
			if(isset($datos['MutualProductoSolicitud']['persona_beneficio_id'])){
				$orden['MutualProductoSolicitud']['persona_beneficio_id'] = $datos['MutualProductoSolicitud']['persona_beneficio_id'];
			}
    		
    		//si no no esta aprobada la solicitud de afiliacion la apruebo
    		if($orden['MutualProductoSolicitud']['socio_id'] == 0):
    		
    			App::import('Model', 'Pfyj.Socio');
				$oSoc = new Socio();    		
    			
				//verifico si no aprobaron la solicitud y generaron el socio
				$socio = $oSoc->getSocioByPersonaId($orden['MutualProductoSolicitud']['persona_id']);
				
				if(!empty($socio)):
				
					$orden['MutualProductoSolicitud']['socio_id'] = $socio['Socio']['id'];
					$datos['Movimiento']['socio_id'] = $socio['Socio']['id'];
    		
				else:
					
					//no tengo socio generado, busco la solicitud y la apruebo
					
	    			App::import('Model', 'Pfyj.SocioSolicitud');
					$oSolSoc = new SocioSolicitud();
	    		
					$afiliacionID = $oSolSoc->find('all',array('conditions' => array('SocioSolicitud.persona_id' => $orden['MutualProductoSolicitud']['persona_id'], 'SocioSolicitud.aprobada' => 0),'fields' => array('SocioSolicitud.id'),'order' => array('SocioSolicitud.created DESC'), 'limit' => 1));
					
					$afiliacionID = Set::extract("{n}.SocioSolicitud",$afiliacionID);
					
					if(!empty($afiliacionID[0]['id'])):
					
						$afiliacion = $oSolSoc->read(null,$afiliacionID[0]['id']);
					
					else:
						//genero un error
						return false;
					
					endif;
					
					$orden['MutualProductoSolicitud']['socio_id'] = $oSolSoc->aprobar($afiliacion,null,$afiliacion['SocioSolicitud']['importe_cuota_social']); 
					
					if(empty($orden['MutualProductoSolicitud']['socio_id'])) return false;
					
				endif;
					
    		else:
    		
    			//si el socio esta NO ACTIVO lo activo nuevamente
    			if($orden['Socio']['activo'] == 0):
    			
			    	App::import('Model', 'Pfyj.Socio');
					$oSoc = new Socio();    

					$oSoc->alta($orden['Socio']['id'],$orden['MutualProductoSolicitud']['persona_beneficio_id'],$orden['MutualProductoSolicitud']['periodo_ini'],$orden['MutualProductoSolicitud']['primer_vto_socio']);
				
				endif;
    			
				
			endif;

			$orden['MutualProductoSolicitud']['fecha_pago'] = date('Y-m-d');
                        $datos['Movimiento']['fecha_operacion'] = date('Y-m-d');
			
    		if($orden['MutualProductoSolicitud']['sin_cargo'] == 0):
    		
    			//ACTUALIZO LA FECHA DE PAGO A LA FECHA DE HOY PARA RECALCULAR EL PERIODO DE INICIO Y VENCIMIENTOS FINALES
    			App::import('Model', 'Proveedores.ProveedorVencimiento');
    			$oVTO = new ProveedorVencimiento(null);
    			$vtos = $oVTO->calculaVencimiento($orden['MutualProductoSolicitud']['proveedor_id'],$orden['MutualProductoSolicitud']['persona_beneficio_id'],$orden['MutualProductoSolicitud']['fecha_pago']);
    			$orden['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
    			$orden['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
    			$orden['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
    			
				$OrdenDto['OrdenDescuento'] = array(
								'fecha' => $orden['MutualProductoSolicitud']['fecha_pago'],
								'tipo_orden_dto' => $orden['MutualProductoSolicitud']['tipo_orden_dto'],
								'numero' => $orden['MutualProductoSolicitud']['id'],
								'tipo_producto' => $orden['MutualProductoSolicitud']['tipo_producto'],
								'socio_id' => $orden['MutualProductoSolicitud']['socio_id'],
								'persona_beneficio_id' => $orden['MutualProductoSolicitud']['persona_beneficio_id'],
								'proveedor_id' => $orden['MutualProductoSolicitud']['proveedor_id'],
								'mutual_producto_id' => 0,
								'periodo_ini' => $orden['MutualProductoSolicitud']['periodo_ini'],
								'importe_cuota' => $orden['MutualProductoSolicitud']['importe_cuota'],
								'importe_total' => $orden['MutualProductoSolicitud']['importe_total'],
								'primer_vto_socio' => $orden['MutualProductoSolicitud']['primer_vto_socio'],
								'primer_vto_proveedor' => $orden['MutualProductoSolicitud']['primer_vto_proveedor'],
								'cuotas' => $orden['MutualProductoSolicitud']['cuotas'],
								'permanente' => $orden['MutualProductoSolicitud']['permanente'],
								'tna' => $orden['MutualProductoSolicitud']['tna'],
								'tnm' => $orden['MutualProductoSolicitud']['tnm'],
								'tem' => $orden['MutualProductoSolicitud']['tem'],
								'cft' => $orden['MutualProductoSolicitud']['cft'],
								'nro_referencia_proveedor' => $orden['MutualProductoSolicitud']['nro_referencia_proveedor'],
				                'mutual_producto_solicitud_id' => $orden['MutualProductoSolicitud']['id']
							);
				App::import('Model','Mutual.OrdenDescuentoCuota');
				$oCuota = new OrdenDescuentoCuota();
				
			
				$OrdenDto['OrdenDescuentoCuota'] = $oCuota->armaCuotas($OrdenDto);

				App::import('Model','Mutual.OrdenDescuento');
				$oODTO = new OrdenDescuento();
				
				if($orden['MutualProductoSolicitud']['permanente'] == 0){
					//genero la orden de descuento y las cuotas					
                			if(!$oODTO->saveAll($OrdenDto)) return false;
				}else{
        	    			if(!$oODTO->saveAll($OrdenDto)) return false;
				}
                                $orden['MutualProductoSolicitud']['orden_descuento_id'] = $oODTO->getLastInsertID();
                                
                                
                                
                                $TIPO_PRODUCTO_SIGEM = parent::GlobalDato('concepto_4',$orden['MutualProductoSolicitud']['tipo_producto']);
                                $importeSeguro = 0;
                                if(!empty($TIPO_PRODUCTO_SIGEM)) $importeSeguro = parent::GlobalDato('decimal_1',$TIPO_PRODUCTO_SIGEM); 
                                
                                
                                if(!empty($importeSeguro) && $orden['MutualProductoSolicitud']['permanente'] == 0) {
                                    
                                    $TIPO_ORDEN_SIGEM = parent::GlobalDato('concepto_3', $TIPO_PRODUCTO_SIGEM);
                                    $TIPO_ORDEN_SIGEM = (empty($TIPO_ORDEN_SIGEM) ? 'EXPTE' : $TIPO_ORDEN_SIGEM);	    			

                                    $proveedorSeguroId = parent::GlobalDato('entero_1',$TIPO_PRODUCTO_SIGEM);
                                    $importeSeguroTotal = $importeSeguro * $orden['MutualProductoSolicitud']['cuotas'];

                                    $OrdenDto['OrdenDescuento'] = array(
                                        'id' => 0,
                                        'fecha' => $orden['MutualProductoSolicitud']['fecha_pago'],
                                        'tipo_orden_dto' => $TIPO_ORDEN_SIGEM,
                                        'numero' => $orden['MutualProductoSolicitud']['id'],
                                        'tipo_producto' => $TIPO_PRODUCTO_SIGEM,
                                        'socio_id' => $orden['MutualProductoSolicitud']['socio_id'],
                                        'persona_beneficio_id' => $orden['MutualProductoSolicitud']['persona_beneficio_id'],
                                        'proveedor_id' => $proveedorSeguroId,
                                        'mutual_producto_id' => 0,
                                        'periodo_ini' => $orden['MutualProductoSolicitud']['periodo_ini'],
                                        'importe_cuota' => $importeSeguro,
                                        'importe_total' => $importeSeguroTotal,
                                        'primer_vto_socio' => $orden['MutualProductoSolicitud']['primer_vto_socio'],
                                        'primer_vto_proveedor' => $orden['MutualProductoSolicitud']['primer_vto_proveedor'],
                                        'cuotas' => $orden['MutualProductoSolicitud']['cuotas'],
                                        'permanente' => 0,
                                        'mutual_producto_solicitud_id' => $orden['MutualProductoSolicitud']['id']
                                    );                                    
                                    $OrdenDto['OrdenDescuentoCuota'] = $oCuota->armaCuotas($OrdenDto);
                                    
                                    if(!$oODTO->saveAll($OrdenDto)) return false;
                                    $orden['MutualProductoSolicitud']['orden_descuento_seguro_id'] = $oODTO->getLastInsertID();
                                }
                                
	    	endif;

	    	// SI LA ORDEN ES UNA AYUDA O DE TIPO PRESTAMO QUIERE DECIR QUE HUBO SALIDA DE DINERO,
	    	// POR TAL MOTIVO GENERO UNA ORDEN DE PAGO. 
    		if($orden['MutualProductoSolicitud']['prestamo'] == 1):
    			$this->oMovimiento = $this->importarModelo('Movimiento', 'proveedores');
    			$this->oOPagoDetalle = $this->importarModelo('OrdenPagoDetalle', 'proveedores');
    			
    			$datos['Movimiento']['mutual_producto_solicitud_id'] = $id;
				$aDetalleProducto = array('mutual_producto_solicitud_id' => $id, 'tipo_producto' => $orden['MutualProductoSolicitud']['tipo_producto']);
				$datos['Movimiento']['detalle_producto'] = array();
				array_push($datos['Movimiento']['detalle_producto'], $aDetalleProducto);
//                debug($datos);
//                exit;
    			if(!$this->oMovimiento->guardarOpago($datos)):
    				return false;
    			endif;
	    		$orden['MutualProductoSolicitud']['orden_pago_id'] = $this->oOPagoDetalle->getOPagoByProductoSolicitud($id); 
    		endif;
	    	
    		$orden['MutualProductoSolicitud']['aprobada'] = 1;
	    	$orden['MutualProductoSolicitud']['aprobada_por'] = (isset($_SESSION['NAME_USER_LOGON_SIGEM']) ? $_SESSION['NAME_USER_LOGON_SIGEM'] : 'APLICACION_SERVER');;
	    	$orden['MutualProductoSolicitud']['aprobada_el'] = date("Y-m-d");
	    	
	    	$orden['MutualProductoSolicitud']['estado'] = 'MUTUESTA0014';
	    	$orden['MutualProductoSolicitud']['vendedor_notificar'] = false;	

                
	    	
            $this->guardarHistorial($orden);
            
    		return parent::save($orden);
    	}
    	return false;
    }
    
    
	function anularOrdenPago($MutualProductoSolicitud){
		
		$this->OrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
		$this->OrdenDescuento = $this->importarModelo('OrdenDescuento', 'mutual');
		$this->OrdenDescuentoCuota = $this->importarModelo('OrdenDescuentoCuota', 'mutual');
		
		$orden = $this->getOrden($MutualProductoSolicitud);
		$ordenDescuentoId = $orden['MutualProductoSolicitud']['orden_descuento_id'];
		
		# ANULO LA ORDEN DE PAGO
		if(!$this->OrdenPago->anular($orden['MutualProductoSolicitud']['orden_pago_id'])) return false;
		
		
		$this->begin();
		
		App::import('Model','Mutual.OrdenDescuento');
		$oODTO = new OrdenDescuento();
		
		$mensaje = "POR ANULACION DE LA ORDEN DE CONSUMO #$MutualProductoSolicitud";

		if(!empty($orden['MutualProductoSolicitud']['orden_descuento_id'])){
			if(!$oODTO->anularOrden($orden['MutualProductoSolicitud']['orden_descuento_id'],$mensaje)){
				$this->rollback();
				parent::notificar("ERROR AL ANULAR LA ORDEN DE DESCUENTO");
				return false;
			}
		}
		
		if(!empty($orden['MutualProductoSolicitud']['orden_descuento_seguro_id'])){
			if(!$oODTO->anularOrden($orden['MutualProductoSolicitud']['orden_descuento_seguro_id'],$mensaje)){
				$this->rollback();
				parent::notificar("ERROR AL ANULAR LA ORDEN DE SEGURO");
				return false;
			}
		}
		
		$orden['MutualProductoSolicitud']['estado'] = 'MUTUESTA0002';
		$orden['MutualProductoSolicitud']['aprobada'] = 0;
		$orden['MutualProductoSolicitud']['orden_pago_id'] = 0;
		if(!$this->save($orden)){
			parent::notificar("ERROR AL ANULAR LA SOLICITUD");
			$this->rollback();
			return false;			
		}
        $this->guardarHistorial($orden);
		$this->commit();
		
		
		
		# RESTABLEZCO LOS VALORES
// 		$orden['MutualProductoSolicitud']['orden_descuento_id'] = 0;
// 		$orden['MutualProductoSolicitud']['orden_pago_id'] = 0;
//     	$orden['MutualProductoSolicitud']['aprobada'] = 0;
// 	    $orden['MutualProductoSolicitud']['aprobada_por'] = NULL;
// 	    $orden['MutualProductoSolicitud']['aprobada_el'] = NULL;
		
// 	    if(!parent::save($orden)) return false;
	     
		# BORRO LA ORDEN DESCUENTO CUOTAS
// 		if(!$this->OrdenDescuentoCuota->deleteAll("OrdenDescuentoCuota.orden_descuento_id = " . $ordenDescuentoId)) return false;
		# BORRO LA ORDEN DESCUENTO
// 		if(!$this->OrdenDescuento->deleteAll("OrdenDescuento.id = " . $ordenDescuentoId)) return false;
	    
		return true;
	}
	

	function getCodigoPlanCuentaOPago($id){
		$this->TipoAsientoRenglon = $this->importarModelo('MutualTipoAsientoRenglon', 'mutual');
		$this->MutualCuentaAsiento = $this->importarModelo('MutualCuentaAsiento', 'contabilidad');
		
		$solicitud = $this->read(null, $id);
		$glb = $this->getGlobalDato('concepto_2',$solicitud['MutualProductoSolicitud']['tipo_producto']);

		$conditions = array('MutualCuentaAsiento.tipo_orden_dto' => $solicitud['MutualProductoSolicitud']['tipo_orden_dto'], 
							'MutualCuentaAsiento.tipo_producto' => $solicitud['MutualProductoSolicitud']['tipo_producto'], 
							'MutualCuentaAsiento.tipo_cuota' => $glb['GlobalDato']['concepto_2']);
		
		$cuentaAsiento = $this->MutualCuentaAsiento->find('all', array('conditions' => $conditions));

		$retorno = array('co_plan_cuenta_id' => 0, 'error' => 'OK');
		
		if($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0 && $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'] == 0):
			$retorno['error'] = 'FALTA DEFINIR ASIENTO EN PRODUCTO SOLICITUD';
		else:
			if($cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'] == 0):
				$tipoAsiento = $this->TipoAsientoRenglon->find('all', array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $cuentaAsiento[0]['MutualCuentaAsiento']['mutual_tipo_asiento_id'], 'MutualTipoAsientoRenglon.variable' => 'PRODU')));

				if(empty($tipoAsiento)) $retorno['error'] = 'ASIENTO NO DEFINIDO EN SOLICITUD';
				else $retorno['co_plan_cuenta_id'] = $tipoAsiento[0]['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
			else:
				$retorno['co_plan_cuenta_id'] = $cuentaAsiento[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
			endif;
		endif;

		return $retorno;
	}
	
	
	function getOrdenPagoLink($orden_pago_id=null){
		if(empty($orden_pago_id)) return '';
		$oOrdenPago = parent::importarModelo("OrdenPago","proveedores");
		$ordenPago = $oOrdenPago->read(null, $orden_pago_id);
		return $ordenPago['OrdenPago']['tipo_documento'] . ' - ' . $ordenPago['OrdenPago']['sucursal'] . ' - ' . str_pad($ordenPago['OrdenPago']['nro_orden_pago'],8,0,STR_PAD_LEFT);
	}
	
	function nuevaSolicitudCredito($datos){
	
//            debug($datos);
//            exit;
            
		$solicitud = array();
		$solicitud['MutualProductoSolicitud'] = array();
	
		$solicitud['MutualProductoSolicitud']['id'] = 0;
		$solicitud['MutualProductoSolicitud']['aprobada'] = 0;
//		$solicitud['MutualProductoSolicitud']['prestamo'] = 1;
		$solicitud['MutualProductoSolicitud']['anulada'] = 0;
		$solicitud['MutualProductoSolicitud']['fecha'] = date('Y-m-d');
		//		$solicitud['MutualProductoSolicitud']['fecha_pago'] = null;
	
		$codigoEstado = parent::GlobalDato("concepto_2", "MUTUESTA0001");
		$codigoEstado = (empty($codigoEstado) ? "MUTUESTA0001" : $codigoEstado);
	
		$solicitud['MutualProductoSolicitud']['estado']  = $codigoEstado;
		$solicitud['MutualProductoSolicitud']['observaciones']  =  $datos['MutualProductoSolicitud']['observaciones'];
		$solicitud['MutualProductoSolicitud']['forma_pago']  =  $datos['MutualProductoSolicitud']['forma_pago'];
	
		$solicitud['MutualProductoSolicitud']['vendedor_id'] = (!empty($datos['MutualProductoSolicitud']['vendedor_id']) ? $datos['MutualProductoSolicitud']['vendedor_id'] : null);
	
                
                //CONTROLO EL VENDEDOR CON EL PLAN
                if(!empty($datos['MutualProductoSolicitud']['vendedor_id'])){
                    App::import('Model','ventas.VendedorProveedorPlan');
                    $oVPLAN = new VendedorProveedorPlan();
                    $is_activo = $oVPLAN->is_activo($datos['MutualProductoSolicitud']['vendedor_id'], $datos['ProveedorPlan']['id']);                    
                    if(!$is_activo){
                        parent::notificar("El Vendedor asignado NO tiene habilitado el Plan seleccionado.");
                        return FALSE;
                    }
                }                
                
                
		//cargo el plan
		App::import('Model','proveedores.ProveedorPlan');
		$oPLAN = new ProveedorPlan();
		$plan = $oPLAN->read(null,$datos['ProveedorPlan']['id']);
                
//                debug($plan);
//                exit;
	
		$solicitud['MutualProductoSolicitud']['tipo_orden_dto'] = parent::GlobalDato('concepto_3',$plan['ProveedorPlan']['tipo_producto']);
		$solicitud['MutualProductoSolicitud']['tipo_producto'] = $plan['ProveedorPlan']['tipo_producto'];
                $solicitud['MutualProductoSolicitud']['ayuda_economica'] = $plan['ProveedorPlan']['ayuda_economica'];
	
		$solicitud['MutualProductoSolicitud']['proveedor_id'] = $plan['ProveedorPlan']['proveedor_id'];
	
		$solicitud['MutualProductoSolicitud']['persona_id'] = $datos['MutualProductoSolicitud']['persona_id'];
		$solicitud['MutualProductoSolicitud']['socio_id'] = $datos['MutualProductoSolicitud']['socio_id'];
		$solicitud['MutualProductoSolicitud']['persona_beneficio_id'] = $datos['MutualProductoSolicitud']['persona_beneficio_id'];
		$solicitud['MutualProductoSolicitud']['proveedor_plan_id'] = $datos['ProveedorPlan']['id'];
	
	
		//calculo vencimientos
		App::import('Model', 'Proveedores.ProveedorVencimiento');
		$this->ProveedorVencimiento = new ProveedorVencimiento(null);
//		$solicitud['MutualProductoSolicitud']['fecha_pago'] = $solicitud['MutualProductoSolicitud']['fecha'];
		$vtos = $this->ProveedorVencimiento->calculaVencimiento($solicitud['MutualProductoSolicitud']['proveedor_id'],$solicitud['MutualProductoSolicitud']['persona_beneficio_id'],$solicitud['MutualProductoSolicitud']['fecha']);
	
        App::import('Model', 'Proveedores.Proveedor');    
        $oPROVEEDOR = new Proveedor();
        $proveedor = $oPROVEEDOR->read('liquida_prestamo',$solicitud['MutualProductoSolicitud']['proveedor_id']);        
        if(isset($proveedor['Proveedor']['liquida_prestamo']) && $proveedor['Proveedor']['liquida_prestamo']){
            $solicitud['MutualProductoSolicitud']['prestamo'] = 1;
        }
        
		$solicitud['MutualProductoSolicitud']['fecha'] = date('Y-m-d');
		$solicitud['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
		$solicitud['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
		$solicitud['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];

		//cargo los valores de la cuota
		App::import('Model','proveedores.ProveedorPlanGrillaCuota');
		$oGRILLA = new ProveedorPlanGrillaCuota();
	
		$cuota = $oGRILLA->read(null,$datos['ProveedorPlanGrillaCuota']['cuota_id']);
                
		$TNA = $cuota['ProveedorPlanGrilla']['tna'];
		$TEM = $cuota['ProveedorPlanGrilla']['tem'];
		$GTO = $cuota['ProveedorPlanGrilla']['gasto_admin'];
		$SELL = $cuota['ProveedorPlanGrilla']['sellado'];
		$IVA = $cuota['ProveedorPlanGrilla']['iva'];
		$METODO = $cuota['ProveedorPlanGrilla']['metodo_calculo'];
                
		$solicitud['MutualProductoSolicitud']['importe_cuota'] = $cuota['ProveedorPlanGrillaCuota']['importe'];
		$solicitud['MutualProductoSolicitud']['cuotas'] = $cuota['ProveedorPlanGrillaCuota']['cuotas'];
	
		$solicitud['MutualProductoSolicitud']['importe_total'] = $solicitud['MutualProductoSolicitud']['importe_cuota'] * $solicitud['MutualProductoSolicitud']['cuotas'];
		$solicitud['MutualProductoSolicitud']['importe_solicitado'] = $cuota['ProveedorPlanGrillaCuota']['capital'];
		$solicitud['MutualProductoSolicitud']['importe_percibido'] = $cuota['ProveedorPlanGrillaCuota']['liquido'];
	
		$solicitud['MutualProductoSolicitud']['tna'] = $TNA;
		$solicitud['MutualProductoSolicitud']['tnm'] = $TEM;
		$solicitud['MutualProductoSolicitud']['tem'] = $TEM;
		$solicitud['MutualProductoSolicitud']['cft'] = $cuota['ProveedorPlanGrillaCuota']['cft'];

		$solicitud['MutualProductoSolicitud']['capital_puro'] = $cuota['ProveedorPlanGrillaCuota']['capital_puro'];
		$solicitud['MutualProductoSolicitud']['interes'] = $cuota['ProveedorPlanGrillaCuota']['interes']; 
		
		$solicitud['MutualProductoSolicitud']['gasto_admin'] = $cuota['ProveedorPlanGrillaCuota']['gasto_admin'];
		$solicitud['MutualProductoSolicitud']['gasto_admin_porc'] = $GTO;
		
		$solicitud['MutualProductoSolicitud']['sellado'] = $cuota['ProveedorPlanGrillaCuota']['sellado'];
		$solicitud['MutualProductoSolicitud']['sellado_porc'] = $SELL;
		
		$solicitud['MutualProductoSolicitud']['iva'] = $cuota['ProveedorPlanGrillaCuota']['iva'];
		$solicitud['MutualProductoSolicitud']['iva_porc'] = $IVA;                
		
		$solicitud['MutualProductoSolicitud']['metodo_calculo'] = $METODO;

		//CARGAR LOS PERIODOS Y VENCIMIENTOS EN EL DETALLE DEL CALCULO
		if(isset($cuota['ProveedorPlanGrillaCuota']['calculo']) && !empty($cuota['ProveedorPlanGrillaCuota']['calculo'])){

			App::import('model', 'proveedores.MetodoCalculoCuota');
			$oCALC = new MetodoCalculoCuota();
			$solicitud['MutualProductoSolicitud']['detalle_calculo_plan'] = $oCALC->setPeriodosAndVencimientos(
																			json_decode($cuota['ProveedorPlanGrillaCuota']['calculo']),
																			$solicitud['MutualProductoSolicitud']['periodo_ini'],
																			$solicitud['MutualProductoSolicitud']['primer_vto_socio'],
																			$solicitud['MutualProductoSolicitud']['primer_vto_proveedor'],
																			$this,
																			TRUE
																		);			
			
		}

		
                
//                debug($solicitud);
//                exit;                
                
		#ARMO LA CANCELACION
		$totalCancela = 0;
		$formaPagoCancelacion = array();
		if(!empty($datos['MutualProductoSolicitud']['CancelacionOrden'])){
				$cancelaciones = array();
				App::import('model','mutual.CancelacionOrden');
						$oCANC = new CancelacionOrden();
						foreach($datos['MutualProductoSolicitud']['CancelacionOrden'] as $cancelacionId){
                            $cancelacion = $oCANC->get($cancelacionId);
                            $totalCancela += $cancelacion['CancelacionOrden']['importe_proveedor'];
                            array_push($cancelaciones, array('mutual_producto_solicitud_id' => 0,'cancelacion_orden_id' => $cancelacionId));
                            $formaPago = array();
                            $formaPago['id'] = 0;
                            $formaPago['mutual_producto_solicitud_id'] = 0;
                            $formaPago['a_la_orden_de'] = 'A LA ORDEN DE ' . $cancelacion['CancelacionOrden']['a_la_orden_de'];
                            $formaPago['concepto'] = (!empty($cancelacion['CancelacionOrden']['concepto']) ? $cancelacion['CancelacionOrden']['concepto'] : "CANCELACION #".$cancelacion['CancelacionOrden']['id']);
                            $formaPago['importe'] = $cancelacion['CancelacionOrden']['importe_proveedor'];
                            array_push($formaPagoCancelacion, $formaPago);
	
						}
						$solicitud['MutualProductoSolicitudCancelacion'] = $cancelaciones;
		}
		if($solicitud['MutualProductoSolicitud']['importe_solicitado'] < $totalCancela){
			parent::notificar("EL IMPORTE SOLICITADO (".$solicitud['MutualProductoSolicitud']['importe_percibido'].") ES MENOR QUE EL TOTAL SELECCIONADO A CANCELAR (".number_format($totalCancela,2).")");
			return false;
		}
	
		#ARMO LA FORMA DE PAGO
		$solicitud['MutualProductoSolicitudInstruccionPago'] = array();
		$formaPago = array();
		$formaPago['id'] = 0;
		$formaPago['mutual_producto_solicitud_id'] = 0;
		$formaPago['a_la_orden_de'] = 'A MI ORDEN PERSONAL';
		$formaPago['concepto'] = 'LIQUIDACION PRESTAMO';
		$formaPago['importe'] = $solicitud['MutualProductoSolicitud']['importe_percibido'] - $totalCancela;
		array_push($solicitud['MutualProductoSolicitudInstruccionPago'], $formaPago);
		if(!empty($formaPagoCancelacion)){
			foreach ($formaPagoCancelacion as $formaCanc){
				array_push($solicitud['MutualProductoSolicitudInstruccionPago'], $formaCanc);
			}
		}
	
// 		debug($solicitud);
//        exit;
	
		#BUSCO EL IMPORTE VIGENTE DE LA CUOTA SOCIAL A LA FECHA SEGUN EL BENEFICIO
		App::import('Model','Pfyj.PersonaBeneficio');
		$oBeneficio = new PersonaBeneficio();
		$cuotaSoc = $oBeneficio->getImporteCuotaSocial($solicitud['MutualProductoSolicitud']['persona_beneficio_id']);
		$solicitud['MutualProductoSolicitud']['cuota_social_vigente'] = $cuotaSoc['decimal_1'];
		$solicitud['MutualProductoSolicitud']['actualizar_cuota_social'] = 0;
	
                #SUELDO NETO Y DEBITOS BANCARIOS
                $solicitud['MutualProductoSolicitud']['sueldo_neto']  = (isset($datos['MutualProductoSolicitud']['sueldo_neto']) ? $datos['MutualProductoSolicitud']['sueldo_neto'] : 0);
                $solicitud['MutualProductoSolicitud']['debitos_bancarios']  = (isset($datos['MutualProductoSolicitud']['debitos_bancarios']) ? $datos['MutualProductoSolicitud']['debitos_bancarios'] : 0);
                $oBeneficio->setSueldoNetoDebitoBancario($solicitud['MutualProductoSolicitud']['persona_beneficio_id'], $solicitud['MutualProductoSolicitud']['sueldo_neto'], $solicitud['MutualProductoSolicitud']['debitos_bancarios']);
                
                
		#SI NO ES SOCIO, CALCULO LA CUOTA SOCIAL
		if($solicitud['MutualProductoSolicitud']['socio_id'] == 0){
			$cuotaSocialDiferenciada = 0;
			if($cuotaSocialDiferenciada != 0) $solicitud['MutualProductoSolicitud']['cuota_social'] = $cuotaSocialDiferenciada;
			else $solicitud['MutualProductoSolicitud']['cuota_social'] = $cuotaSoc['decimal_1'];
			$data = $this->__chequearSocio($solicitud);
		}else{
			#SI ES SOCIO, VERIFICO EL IMPORTE DE LA CUOTA SOCIAL QUE ESTA DESCONTANDO
			App::import('Model','Pfyj.Socio');
			$oSocio = new Socio();
			$oSocio->bindModel(array('hasOne' => array('OrdenDescuento')));
			$socio = $oSocio->read(null,$solicitud['MutualProductoSolicitud']['socio_id']);
			$solicitud['MutualProductoSolicitud']['cuota_social'] = $socio['OrdenDescuento']['importe_cuota'];
			if($solicitud['MutualProductoSolicitud']['cuota_social'] < $solicitud['MutualProductoSolicitud']['cuota_social_vigente']) $solicitud['MutualProductoSolicitud']['actualizar_cuota_social'] = 1;
		}
	
// 		debug($solicitud);
	

	
		if(!isset($solicitud['MutualProductoSolicitud']['proveedor_id'])) return false;
		if(!isset($solicitud['MutualProductoSolicitud']['persona_beneficio_id'])) return false;
		if($solicitud['MutualProductoSolicitud']['actualizar_cuota_social'] == 1 && $solicitud['MutualProductoSolicitud']['socio_id'] != 0 ){
    		if(!$this->__actualizarCuotaSocial($solicitud)) return false;
		}
		
		
		
		parent::begin();
		
		
		if(!parent::saveAll($solicitud)){
			parent::notificar("Error al grabar la Solicitud");
			parent::rollback();
    		return false;
		}
		
		
		$solicitud['MutualProductoSolicitud']['id'] = $this->getLastInsertID();
		
		//si se grabo bien, generar un remito para el vendedor porque la solicitud esta presentada
		if(!empty($solicitud['MutualProductoSolicitud']['vendedor_id'])){
			App::import('model','ventas.VendedorRemito');
			$oVR = new VendedorRemito();
			
			$remito = array();
			$remito['VendedorRemito']['id'] = 0;
			$remito['VendedorRemito']['vendedor_id'] = $solicitud['MutualProductoSolicitud']['vendedor_id'];
			$remito['VendedorRemito']['observaciones'] = "*** CONSTANCIA AUTOMATICA POR CARGA Y ASIGNACION DIRECTA ***";
			$oVR->save($remito);
			$solicitud['MutualProductoSolicitud']['vendedor_remito_id'] = $oVR->getLastInsertID();
			$rem = $this->save(
			     array(
			         'MutualProductoSolicitud' => array(
			             'id' => $solicitud['MutualProductoSolicitud']['id'],
			             'vendedor_remito_id' => $solicitud['MutualProductoSolicitud']['vendedor_remito_id']
			         )
			     )    
			);
			if(!$rem){
			    parent::notificar("Error al grabar el remito la Solicitud");
			    parent::rollback();
			    return false;
			}
		}
		
        //GUARDO EL HISTORIAL
        $histo = $this->guardarHistorial($solicitud);
        
        if(!$histo){
            
            parent::notificar("Error al grabar el historial de la Solicitud");
            parent::rollback();
            return false;
        }
        
        return parent::commit();
        
//         parent::rollback();
//         exit;
// 		return true;
	
	}
    
	function reasignarProveedor($id,$proveedor_id = null){
		$this->id = $id;
		if(empty($proveedor_id)){
			if(!parent::saveField("reasignar_proveedor_fecha", null)) return false;
			if(!parent::saveField("reasignar_proveedor_usuario", null)) return false;
			return parent::saveField("reasignar_proveedor_id", 0);
		}else{
			if(!parent::saveField("reasignar_proveedor_fecha", date('Y-m-d H:i:s'))) return false;
			if(!parent::saveField("reasignar_proveedor_usuario", $_SESSION[$this->keyNameUserLogon])) return false;
			return parent::saveField("reasignar_proveedor_id", $proveedor_id);
		}
	}    
	
//	function reasignarProveedor($id,$proveedor_id = null){
//			$this->id = $id;
//			if(empty($proveedor_id)){
//			if(!parent::saveField("reasignar_proveedor_fecha", null)) return false;
//			if(!parent::saveField("reasignar_proveedor_usuario", null)) return false;
//			return parent::saveField("reasignar_proveedor_id", 0);
//			}else{
//			if(!parent::saveField("reasignar_proveedor_fecha", date('Y-m-d H:i:s'))) return false;
//						if(!parent::saveField("reasignar_proveedor_usuario", $_SESSION[$this->keyNameUserLogon])) return false;
//						return parent::saveField("reasignar_proveedor_id", $proveedor_id);
//		}
//	}
	
	
	function setearVencimientos($orden,$fechaPago = null){
		App::import('Model', 'Proveedores.ProveedorVencimiento');
		$oVTOS = new ProveedorVencimiento(null);
		$fechaPago = (empty($fechaPago) ? date('Y-m-d') : $fechaPago);
		$vtos = $oVTOS->calculaVencimiento($orden['MutualProductoSolicitud']['proveedor_id'],$orden['MutualProductoSolicitud']['persona_beneficio_id'],$fechaPago);
				$orden['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
						$orden['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
						$orden['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
						return $orden;
	}
	
	function getSolicitudesByVendedor($vendedor_id,$estado = array(),$fechaEmiteDesde = null, $fechaEmiteHasta = null){
	
		$sql = "SELECT MutualProductoSolicitud.* FROM mutual_producto_solicitudes AS MutualProductoSolicitud
				INNER JOIN personas AS Persona ON (Persona.id = MutualProductoSolicitud.persona_id)
				WHERE
					".(!empty($vendedor_id) ? " MutualProductoSolicitud.vendedor_id IN (".$vendedor_id.")" : " 1=1 ")."
						".(!empty($estado) ? " AND MutualProductoSolicitud.estado IN ('".implode("','", $estado)."')" : "" )."
						".(!empty($fechaEmiteDesde) ? " AND MutualProductoSolicitud.fecha >= '$fechaEmiteDesde'" : " " )."
						".(!empty($fechaEmiteHasta) ? " AND MutualProductoSolicitud.fecha <= '$fechaEmiteHasta'" : " " )."
						ORDER BY Persona.apellido,Persona.nombre,MutualProductoSolicitud.fecha,MutualProductoSolicitud.id;";
		$solicitudes = $this->query($sql);
		foreach($solicitudes as $idx => $solicitud){
			$solicitudes[$idx] = $this->armaDatos($solicitud);
						}
						return $solicitudes;
	
	
				// 		$solicitudes = $this->find('all',array('conditions' => $conditions,'order'=>'MutualProductoSolicitud.created DESC'));
				// 		foreach($solicitudes as $idx => $solicitud){
				// 			$solicitud = $this->armaDatos($solicitud,$cargaOrdenDto);
				// 			$solicitudes[$idx] = $solicitud;
				// 		}
				// 		return $solicitudes;
		}
	
	
	function observar($data){
		$solicitud = $this->read(null,$data['MutualProductoSolicitud']['id']);
		$observaciones = $solicitud['MutualProductoSolicitud']['observaciones'];
		if(!empty($observaciones)) $observaciones .= "<br/>";
//			$observaciones .= "[ *** OBSERVADA EL ".date('d/m/Y H:i:s')." : ";
			$observaciones .= " | " . $data['MutualProductoSolicitud']['observaciones'];
		if(!empty($data['MutualProductoSolicitud']['observaciones'])) $observaciones .= " *** ] ";
			$solicitud['MutualProductoSolicitud']['observaciones'] = $observaciones;
		$solicitud['MutualProductoSolicitud']['estado'] = 'MUTUESTA0004';
        $this->guardarHistorial($solicitud);
		return $this->save($solicitud);
	}	
	
        function getNoAprobadasByCodigoEstado2($codigo = 'MUTUESTA0001'){
    //    	$codigoEstadoFiltra = parent::GlobalDato("concepto_2", $codigo);
    //    	$codigoEstadoFiltra = (empty($codigoEstadoFiltra) ? $codigo : $codigoEstadoFiltra);

            $sql = "SELECT 
                    MutualProductoSolicitud.id,
                    MutualProductoSolicitud.persona_id,
                    MutualProductoSolicitud.user_created,
                    MutualProductoSolicitud.vendedor_id,
                    MutualProductoSolicitud.proveedor_id,
                    MutualProductoSolicitud.persona_beneficio_id,
                    CodigoEstado.concepto_1,
                    MutualProductoSolicitud.fecha,
                    concat(Persona.documento,' - ',Persona.apellido,', ',Persona.nombre) as solicitante,
                    MutualProductoSolicitud.periodo_ini,
                    MutualProductoSolicitud.importe_percibido,
                    MutualProductoSolicitud.cuotas,
                    MutualProductoSolicitud.importe_cuota,
                    Proveedor.razon_social_resumida,
                    TipoProducto.concepto_1,
                    ProveedorReasigna.razon_social,
                    concat('#',Vendedor.id, ' - ',VendedorPersona.documento,' - ',VendedorPersona.apellido, ', ',VendedorPersona.nombre) as vendedor
                    FROM mutual_producto_solicitudes AS MutualProductoSolicitud
                    INNER JOIN personas AS Persona ON (Persona.id = MutualProductoSolicitud.persona_id)
                    INNER JOIN global_datos CodigoEstado on (CodigoEstado.id = MutualProductoSolicitud.estado) 
                    LEFT JOIN proveedor_planes ProveedorPlan on (ProveedorPlan.id = MutualProductoSolicitud.proveedor_plan_id)
                    INNER JOIN proveedores Proveedor on (Proveedor.id = MutualProductoSolicitud.proveedor_id)
                    LEFT JOIN proveedores ProveedorReasigna on (ProveedorReasigna.id = MutualProductoSolicitud.reasignar_proveedor_id)
                    INNER JOIN global_datos TipoProducto on (TipoProducto.id = MutualProductoSolicitud.tipo_producto)
                    LEFT JOIN vendedores Vendedor on (Vendedor.id = MutualProductoSolicitud.vendedor_id)
                    LEFT JOIN personas VendedorPersona on (VendedorPersona.id = Vendedor.persona_id)
                    WHERE MutualProductoSolicitud.aprobada = 0 AND MutualProductoSolicitud.anulada = 0
                    AND MutualProductoSolicitud.estado = '$codigo'
                    ORDER BY Persona.apellido,Persona.nombre,MutualProductoSolicitud.id;";
            $datos = $this->query($sql);
            $solicitudes = array();

            if(!empty($datos)){

                App::import('Model', 'Proveedores.ProveedorVencimiento');
                $oVTOS = new ProveedorVencimiento(null);

                foreach($datos as $i => $dato){
                    $solicitudes[$i]['MutualProductoSolicitud'] = $dato['MutualProductoSolicitud'];
                    $solicitudes[$i]['MutualProductoSolicitud']['nro_print'] = str_pad($dato['MutualProductoSolicitud']['id'],8,0,STR_PAD_LEFT);
                    $solicitudes[$i]['MutualProductoSolicitud']['estado_desc'] = $dato['CodigoEstado']['concepto_1'];
                    $solicitudes[$i]['MutualProductoSolicitud']['beneficiario'] = $dato[0]['solicitante'];
                    $solicitudes[$i]['MutualProductoSolicitud']['proveedor_producto'] = $dato['Proveedor']['razon_social_resumida']." - ".$dato['TipoProducto']['concepto_1'];
                    if(!empty($dato['ProveedorReasigna']['razon_social_resumida'])){
                        $solicitudes[$i]['MutualProductoSolicitud']['proveedor_producto'] .= " *** ".$dato['ProveedorReasigna']['razon_social_resumida']." ***";
                    }
                    $solicitudes[$i]['MutualProductoSolicitud']['vendedor_nombre'] = $dato[0]['vendedor'];

                    if($solicitudes[$i]['MutualProductoSolicitud']['aprobada'] == 0){
                            $fechaPago = date('Y-m-d');
                            $vtos = $oVTOS->calculaVencimiento($solicitudes[$i]['MutualProductoSolicitud']['proveedor_id'],$solicitudes[$i]['MutualProductoSolicitud']['persona_beneficio_id'],$fechaPago);
                            $solicitudes[$i]['MutualProductoSolicitud']['periodo_ini'] = $vtos['inicia_en'];
                            $solicitudes[$i]['MutualProductoSolicitud']['primer_vto_socio'] = $vtos['vto_primer_cuota_socio'];
                            $solicitudes[$i]['MutualProductoSolicitud']['primer_vto_proveedor'] = $vtos['vto_primer_cuota_proveedor'];
                    }
                    $solicitudes[$i]['MutualProductoSolicitud']['inicia_en'] = parent::periodo($solicitudes[$i]['MutualProductoSolicitud']['periodo_ini']);


                }
            }
            return $solicitudes;
        }        
        
	function getNoAprobadasByCodigoEstado($codigo = 'MUTUESTA0001',$tipoOrden = null){
		$codigoEstadoFiltra = parent::GlobalDato("concepto_2", $codigo);
		$codigoEstadoFiltra = (empty($codigoEstadoFiltra) ? $codigo : $codigoEstadoFiltra);
		//     	$conditions = array();
		//     	$conditions['MutualProductoSolicitud.aprobada'] = 0;
		//     	$conditions['MutualProductoSolicitud.anulada'] = 0;
		//     	$conditions['MutualProductoSolicitud.estado'] = $codigoEstadoFiltra;
		//     	$solicitudes = $this->find('all',array('conditions' => $conditions,'order'=>'MutualProductoSolicitud.created DESC'));
		$sql = "SELECT MutualProductoSolicitud.* FROM mutual_producto_solicitudes AS MutualProductoSolicitud
		INNER JOIN personas AS Persona ON (Persona.id = MutualProductoSolicitud.persona_id)
		WHERE MutualProductoSolicitud.aprobada = 0 AND MutualProductoSolicitud.anulada = 0
		".(!empty($tipoOrden) ? " AND MutualProductoSolicitud.tipo_orden_dto = '$tipoOrden'" : " ")."
		AND MutualProductoSolicitud.estado = '$codigoEstadoFiltra'
		ORDER BY Persona.apellido,Persona.nombre,MutualProductoSolicitud.id;";
// 		debug($sql);
		$solicitudes = $this->query($sql);
// 		debug($solicitudes);
		foreach($solicitudes as $idx => $solicitud){
			$solicitudes[$idx] = $this->armaDatos($solicitud);
		}
		return $solicitudes;
	}	
	
	
	function getCreditosNoAprobados(){
		return $this->getNoAprobadasByCodigoEstado('MUTUESTA0002',Configure::read('APLICACION.tipo_orden_dto_credito'));
	}	
	
	function guardarHistorial($solicitud){
        App::import('model','mutual.MutualProductoSolicitudEstado');
        $oMPSE = new MutualProductoSolicitudEstado();
        $estado = array();
        $estado['MutualProductoSolicitudEstado']['id'] = 0;
        $estado['MutualProductoSolicitudEstado']['mutual_producto_solicitud_id'] = $solicitud['MutualProductoSolicitud']['id'];
		$estado['MutualProductoSolicitudEstado']['estado'] = $solicitud['MutualProductoSolicitud']['estado'];
        $estado['MutualProductoSolicitudEstado']['observaciones'] = $solicitud['MutualProductoSolicitud']['observaciones'];
        return $oMPSE->save($estado);        
    }
    
	function getSolicitudesByVendedorGrillaModuloVentas($vendedor_id,$estado = array(),$fechaEmiteDesde = null, $fechaEmiteHasta = null,$nro_solicitud = null,$ndoc = null,$persona_id = null,$aprobada = NULL,$anulada = NULL,$estadoNotIn = array()){
	
            if(!empty($nro_solicitud)){
                $estado = $fechaEmiteDesde = $fechaEmiteHasta = $ndoc = null;
            }
            if(!empty($ndoc)){
                $estado = $fechaEmiteDesde = $fechaEmiteHasta = $nro_solicitud = null;
            }
        
        
            $sql = "SELECT MutualProductoSolicitud.id,MutualProductoSolicitud.estado,MutualProductoSolicitud.anulada,MutualProductoSolicitud.fecha,EstadoSolicitud.concepto_1,Proveedor.razon_social_resumida,Proveedor.razon_social,TipoProducto.concepto_1,
            TipoDocumento.concepto_1,Persona.documento,Persona.apellido,Persona.nombre,MutualProductoSolicitud.importe_total,MutualProductoSolicitud.importe_solicitado,
            MutualProductoSolicitud.importe_percibido,MutualProductoSolicitud.cuotas,MutualProductoSolicitud.importe_cuota FROM mutual_producto_solicitudes AS MutualProductoSolicitud
                            INNER JOIN personas AS Persona ON (Persona.id = MutualProductoSolicitud.persona_id)
            INNER JOIN proveedores AS Proveedor ON (Proveedor.id = MutualProductoSolicitud.proveedor_id)
            LEFT JOIN proveedor_planes AS ProveedorPlan ON (ProveedorPlan.id = MutualProductoSolicitud.proveedor_plan_id)
            INNER JOIN global_datos AS EstadoSolicitud ON (EstadoSolicitud.id = MutualProductoSolicitud.estado)
            INNER JOIN global_datos AS TipoDocumento ON (TipoDocumento.id = Persona.tipo_documento)
            INNER JOIN global_datos AS TipoProducto ON (TipoProducto.id = MutualProductoSolicitud.tipo_producto)
            WHERE
                    ".(!empty($vendedor_id) ? " MutualProductoSolicitud.vendedor_id in (". $vendedor_id .")" : " 1=1 ")."
                    ".(!empty($nro_solicitud) ? " AND MutualProductoSolicitud.id = $nro_solicitud" : "  ")."
                    ".(!empty($ndoc) ? " AND Persona.documento = $ndoc" : "  ")."
                    ".(!empty($persona_id) ? " AND Persona.id = $persona_id" : "  ")."    
                    ".(!empty($estado) ? " AND MutualProductoSolicitud.estado IN ('".implode("','", $estado)."')" : "" )."
                    ".(!empty($estadoNotIn) ? " AND MutualProductoSolicitud.estado NOT IN ('".implode("','", $estadoNotIn)."')" : "" )."    
                    ".(!empty($fechaEmiteDesde) ? " AND MutualProductoSolicitud.fecha >= '$fechaEmiteDesde'" : " " )."
                    ".(!empty($fechaEmiteHasta) ? " AND MutualProductoSolicitud.fecha <= '$fechaEmiteHasta'" : " " )."
                    ".(!empty($aprobada) ? " AND MutualProductoSolicitud.aprobada = $aprobada " : " " )."
                    ".(!empty($anulada) ? " AND MutualProductoSolicitud.anulada = $anulada " : " " )."
                    ORDER BY MutualProductoSolicitud.fecha,Persona.apellido,Persona.nombre,MutualProductoSolicitud.id LIMIT 50;";
            $solicitudes = $this->query($sql);
            return $solicitudes;

	}
        
        
    function get_orden_descuento_emitida($solicitud_id,$solicitud = NULL,$field = 'orden_descuento_id'){
    	App::import('Model','Mutual.OrdenDescuento');
    	$oOdto = new OrdenDescuento();
        if(empty($solicitud)){
            $solicitud = $this->read($field,$solicitud_id);
        }
        
    	//verifico que la orden no este novada
    	$orden = $oOdto->read(null,$solicitud['MutualProductoSolicitud'][$field]);
//        debug($orden['OrdenDescuento']);
    	$novacion = false;
//    	while(!$novacion){
//            if($orden['OrdenDescuento']['activo'] == 0 && !empty($orden['OrdenDescuento']['nueva_orden_descuento_id']) && empty($orden['OrdenDescuento']['anterior_orden_descuento_id'])){
//                    $orden = $oOdto->read(null,$orden['OrdenDescuento']['nueva_orden_descuento_id']);
//            }else {
//                $orden = $oOdto->read(null,$orden['OrdenDescuento']['nueva_orden_descuento_id']);
//                $novacion = true;
//                break;
//            }
//    	}
        
        do {
            
            if(!empty($orden['OrdenDescuento']['nueva_orden_descuento_id'])){
                    
                $orden = $oOdto->read(null,$orden['OrdenDescuento']['nueva_orden_descuento_id']);
                    
            }else{
                
//                $orden = $oOdto->read(null,$orden['OrdenDescuento']['nueva_orden_descuento_id']);
//                echo "paso! ";
                $novacion = true;
                break;
                
            }            
            
//            echo $orden['OrdenDescuento']['id']." | ";
            
        }while(!$novacion);
//        debug($orden['OrdenDescuento']);
        return $orden;        
    }
    
    
    /**
     * NUEVO METODO DE CARGA DE UNA SOLICITUD CON TODA LA INFORMACION ADICIONAL
     * NECESARIA PARA LLEVARLA A LA VISTA
     * @param unknown $solicitudId
     * @return NULL|unknown[][]|NULL[][]
     */
    function getSolicitud($solicitudId) {
        App::import('model','mutual.MutualProductoSolicitudService');
        $oSERV = new MutualProductoSolicitudService();
        
        $solicitud = $oSERV->getSolicitud($solicitudId);
        if( !$solicitud['MutualProductoSolicitud']['aprobada'] && isset($solicitud['MutualProductoSolicitud']['detalle_calculo_plan']) && !empty($solicitud['MutualProductoSolicitud']['detalle_calculo_plan'])){
            App::import('model', 'proveedores.MetodoCalculoCuota');
            $oCALC = new MetodoCalculoCuota();
            $solicitud['MutualProductoSolicitud']['detalle_calculo_plan'] = $oCALC->setPeriodosAndVencimientos(
                json_decode($solicitud['MutualProductoSolicitud']['detalle_calculo_plan']),
                $solicitud['MutualProductoSolicitud']['periodo_ini'],
                $solicitud['MutualProductoSolicitud']['primer_vto_socio'],
                $solicitud['MutualProductoSolicitud']['primer_vto_proveedor'],
                $this,
                TRUE
                );
        }
        
        return $solicitud;
    }
    
}
?>

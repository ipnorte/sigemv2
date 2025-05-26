<?php
class GlobalDatosController extends ConfigAppController{
	
	var $name = "GlobalDatos";

	var $autorizar = array(
            'combo',
            'forma_pago',
            'valor',
            'cmb_provincias',
            'datos_globales',
            'cmb_tipoCuota',
            'get_cuotas_puntuales',
            'get_tipos_cobro_caja',
            'get_todos_tipos_cobro_caja',
            'get_tipos_documento',
            'get_organismos',
            'calificaciones_socio',
            'get_empresas',
            'get_tipo_comprobante',
            'get_concepto_gasto',
            'get_config_reasignacion_proveedores',
            'combo_empresas_ajax',
            'get_tipo_productos',
            'get_estados_solicitud',
            'get_fpago_solicitud',
            'get_tipo_productos_consumos',
            'get_organismos_activos',
            'get_tipo_cuotas',
            'get_tipo_producto_servicios',
            'get_ente_recaudadores',
            'get_organismos_activos_cbu',
            'get_estados_civil',
            'get_solicitud_anexos',
            'get_solicitud_templates',
			'get_estados_disponibles_solicitud',
			'get_estados_solicitud_vendedor',
			'get_tipo_situaciones_cuotas',
			'get_solicitud_documentos',
			'get_webservices_intranet',
            'productos_siisa'
	);
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}

	
	function index($nivel = 1, $prefijo = ''){
		
		$this->GlobalDato->recursive = 0;
		
		if($nivel == 1){
			$ln = 4;
			$prefijo = '';
			$prefijo_a = '';
		}else if($nivel == 2){
			$ln = 8;
			$prefijo = substr($prefijo,0,4);
			$prefijo_a = '';
		}else if($nivel == 3){
			$ln = 12;
			$prefijo = substr($prefijo,0,8);
			$prefijo_a = substr($prefijo,0,4);
		}
		
		
		
		$conditions = array();
		$this->set('datos', $this->GlobalDato->find('all',array('conditions' => array('LENGTH(GlobalDato.id)' => $ln, 'GlobalDato.id LIKE' => $prefijo .'%'),'order' => array('GlobalDato.id'))));	
		$this->set('ln_s',$nivel + 1);
		$this->set('ln_a',$nivel - 1);
		$this->set('pref_s',$prefijo);
		$this->set('pref_a',$prefijo_a);
	}
	
	function add($nivel = 1, $prefijo = ''){
		if (!empty($this->data)) {
			$this->data['GlobalDato']['id'] = $this->data['GlobalDato']['codigo_prefijo'] . $this->data['GlobalDato']['codigo']; 
			if ($this->GlobalDato->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index/'.$nivel.'/'.$prefijo));					
			}else{
				$this->Mensaje->errorGuardar();
			}		
		}
		$this->set('nivel',$nivel - 1);
		$this->set('prefijo',$prefijo);				
	}
	
	function edit($id=null,$nivel = 1, $prefijo = ''){
		if(empty($id)) $this->redirect('index');
		if (!empty($this->data)) {
			if ($this->GlobalDato->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'index/'.$nivel.'/'.$prefijo));				
			}else{
				$this->Mensaje->errorGuardar();
			}		
		}
		$this->data = $this->GlobalDato->read(null,$id);
		$this->set('nivel',$nivel - 1);
		$this->set('prefijo',$prefijo);		
	}
	
	function del($id = null,$nivel = 1, $prefijo = ''){
		if(empty($id)) $this->redirect('index');
		if ($this->GlobalDato->del($id)) {
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
		}else{
			$this->Mensaje->error("No se puede borrar el codigo " .$id ." porque tiene niveles dependientes." );
		}
		$nivel = $nivel - 1;		
		$this->redirect(array('action'=>'index/'.$nivel.'/'.$prefijo));
	}	
	
	
	function combo($label,$model,$prefix,$disable=0,$empty=0,$selected='',$logico=0){
		$values = $this->GlobalDato->find('list',array('conditions' => array('GlobalDato.id LIKE ' => $prefix . '%', 'GlobalDato.id <> ' => $prefix,'GlobalDato.logico_1' => $logico),'fields' => array('concepto_1'),'order' => array('GlobalDato.id')));
		$this->set('values',$values);
		$this->set('model',$model);
		$this->set('label',($label=='.'? null:$label));
		$this->set('disabled',$disable);
		$this->set('empty',(!empty($selected) ? 0 : $empty));
		$this->set('selected',$selected);
		$this->render();
	}
	
	
	function forma_pago(){
		$values = $this->GlobalDato->find('list',array('conditions' => array('GlobalDato.id LIKE ' => 'FPAG%', 'GlobalDato.id <> ' => 'FPAG'),'fields' => array('concepto_1'),'order' => array('GlobalDato.id')));
		
		$this->set('values',$values);
		$this->set('disabled','');		
		$this->render();
	}
	
	function valor($id=null,$field='concepto_1',$render=1){
		if(empty($id)) return null;
		$dato = $this->GlobalDato->read(null,$id);
		$this->set('valor',$dato['GlobalDato'][$field]);
		if($render == 1){
			$this->render(null,'blank');
		}else{
			return $dato['GlobalDato'][$field];
		}
	}

        /**
         * 
         * @param type $label
         * @param type $model
         * @param type $disable
         * @param type $selected
         * 
         * @deprecated since version 2.5
         */
	function cmb_provincias($label,$model,$disable=0,$selected=''){
		App::import('Model', 'Config.Provincia');
		$this->Provincia = new Provincia(null);
		$values = $this->Provincia->find('list',array('conditions'=>array(),'fields' => array('nombre'), 'order' => 'nombre'));
		$this->set('values',$values);
		$this->set('model',$model);
		$this->set('label',$label);
		$this->set('disabled',$disable);
		$this->set('empty',0);
                $this->set('selected',$selected);
		$this->render('combo');					
	}
	
	function datos_globales($prefix){
		return $this->GlobalDato->find('all',array('conditions' => array('GlobalDato.id LIKE ' => $prefix . '%', 'GlobalDato.id <> ' => $prefix),'order' => array('GlobalDato.id')));
	}

	function cmb_tipoCuota($label,$model,$signo='SUMA',$disable=0,$selected=''){
		$signo = ($signo == 'RESTA' ? '-' : '+');
		$label = ($label == '0' ? "" : $label);
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUTCUO%', 'GlobalDato.id <> ' => 'MUTUTCUO', 'GlobalDato.concepto_2 LIKE' => $signo . '%', 'GlobalDato.logico_2' => 1),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		$this->set('values',$values);
		$this->set('model',$model);
		$this->set('label',$label);
		$this->set('disabled',$disable);
		$this->set('empty',0);
                $this->set('selected',$selected);
		$this->render('combo','ajax');					
	}
	
	function get_cuotas_puntuales(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUTCUO%', 'GlobalDato.id <> ' => 'MUTUTCUO', 'GlobalDato.logico_2' => 1),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}
	
	function get_tipos_cobro_caja(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUTCOB%', 'GlobalDato.id <> ' => 'MUTUTCOB', 'GlobalDato.logico_2' => 1),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}
	
	function get_todos_tipos_cobro_caja(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUTCOB%', 'GlobalDato.id <> ' => 'MUTUTCOB'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}	
	
	function get_tipos_documento(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'PERSTPDC%', 'GlobalDato.id <> ' => 'PERSTPDC'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}		
	
	function get_organismos(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUCORG%', 'GlobalDato.id <> ' => 'MUTUCORG'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}
	
	function get_organismos_activos2(){
            $vendedor = $this->Seguridad->user('vendedor_id');
            $values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUCORG%', 'GlobalDato.id <> ' => 'MUTUCORG', 'GlobalDato.logico_1' => 1),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
            return $values;
	}
        
        function get_organismos_activos(){
            $vendedor = $this->Seguridad->user('vendedor_id');
            // Si el vendedor tiene un valor, obtener los códigos de organismo habilitados
            if ($vendedor) {
                App::import('Model', 'proveedores.ProveedorPlanOrganismo');
                $oPPO = new ProveedorPlanOrganismo();

                // Obtener los códigos de organismo habilitados para el vendedor
                $organismosHabilitadosData = $oPPO->find('all', [
                    'conditions' => ['VendedorProveedorPlan.vendedor_id' => $vendedor],
                    'fields' => ['ProveedorPlanOrganismo.codigo_organismo'],
                    'group' => 'ProveedorPlanOrganismo.codigo_organismo',
                    'joins' => [
                        [
                            'table' => 'vendedor_proveedor_planes',
                            'alias' => 'VendedorProveedorPlan',
                            'type' => 'INNER',
                            'conditions' => [
                                'VendedorProveedorPlan.proveedor_plan_id = ProveedorPlanOrganismo.proveedor_plan_id'
                            ]
                        ]
                    ]
                ]);
                // Convertir los resultados a un array de códigos de organismo utilizando Set::extract
                $organismosHabilitados = Set::extract('/ProveedorPlanOrganismo/codigo_organismo', $organismosHabilitadosData);

                // Filtrar los organismos de global_datos que están habilitados para el vendedor
                $values = $this->GlobalDato->find('list', [
                    'conditions' => [
                        'GlobalDato.id LIKE' => 'MUTUCORG%',
                        'GlobalDato.id <>' => 'MUTUCORG',
                        'GlobalDato.logico_1' => 1,
                        'GlobalDato.id' => $organismosHabilitados
                    ],
                    'fields' => ['GlobalDato.concepto_1'],
                    'order' => 'GlobalDato.concepto_1'
                ]);
            } else {
                // Si no hay un vendedor, realizar la consulta original
                $values = $this->GlobalDato->find('list', [
                    'conditions' => [
                        'GlobalDato.id LIKE' => 'MUTUCORG%',
                        'GlobalDato.id <>' => 'MUTUCORG',
                        'GlobalDato.logico_1' => 1
                    ],
                    'fields' => ['GlobalDato.concepto_1'],
                    'order' => 'GlobalDato.concepto_1'
                ]);
            }
            return $values;
        }



        

	function calificaciones_socio(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUCALI%', 'GlobalDato.id <> ' => 'MUTUCALI'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}

	function get_empresas($soloActivas=1){
        return  $this->GlobalDato->getEmpresas($soloActivas);
//		if($soloActivas != 1)$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUEMPR%', 'GlobalDato.id <> ' => 'MUTUEMPR'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
//		else $values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUEMPR%', 'GlobalDato.id <> ' => 'MUTUEMPR', 'GlobalDato.logico_1' => 1),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
//		return $values;
	}	
	
	function get_tipo_comprobante(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'PROVDOCU%', 'GlobalDato.id <> ' => 'PROVDOCU'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}		
	
	function get_concepto_gasto(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'PROVCGAS%', 'GlobalDato.id <> ' => 'PROVCGAS'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}

	
	function get_config_reasignacion_proveedores($concepto2){
		$values = $this->GlobalDato->find('all',array('conditions'=>array('GlobalDato.id LIKE ' => 'PROVREAS%', 'GlobalDato.concepto_2' => $concepto2, 'GlobalDato.logico_1' => 1),'fields' => array('GlobalDato.concepto_1,GlobalDato.concepto_3,GlobalDato.entero_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;		
	}
	
	function combo_empresas_ajax($organismo=null,$selected=null,$empty=null,$turno=null){
		$this->set('values',$this->GlobalDato->getEmpresaList($organismo,(!empty($turno) ? true : false)));
		$this->set('selected',(empty($selected) ? null : $selected));
		$this->set('empty',$empty);
		$this->render(null,'ajax');
	}		
	
	
	function get_tipo_productos(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUPROD%', 'GlobalDato.id <> ' => 'MUTUPROD', 'GlobalDato.logico_2' => 1),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}
	
	function get_estados_solicitud(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUESTA%', 'GlobalDato.id <> ' => 'MUTUESTA'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}
	
	function get_fpago_solicitud(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUFPAG%', 'GlobalDato.id <> ' => 'MUTUFPAG'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}

	function get_tipo_productos_consumos($todos = FALSE){
            $conditions = array('GlobalDato.id LIKE ' => 'MUTUPROD%', 'GlobalDato.id <> ' => 'MUTUPROD');
            if(!$todos) $conditions['GlobalDato.logico_1'] = 1;

            $values = $this->GlobalDato->find('list',array('conditions'=>$conditions,'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
            return $values;
	}	
	
    
	function get_tipo_cuotas(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUTCUO%', 'GlobalDato.id <> ' => 'MUTUTCUO'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}    
    
    
	function get_tipo_producto_servicios(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUPROD%', 'GlobalDato.id <> ' => 'MUTUPROD', 'GlobalDato.logico_2' => 1, 'GlobalDato.concepto_3' => 'OSERV'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}
    
	function get_ente_recaudadores(){
		$values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUENRE%', 'GlobalDato.id <> ' => 'MUTUENRE', 'GlobalDato.logico_1' => 1),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
		return $values;
	}  
    
	function get_organismos_activos_cbu(){
            $values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUCORG%', 'GlobalDato.id <> ' => 'MUTUCORG', 'GlobalDato.logico_1' => 1 , 'GlobalDato.concepto_2' => 'CBU',),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
            return $values;
	}
        
	function get_estados_civil(){
            $values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'PERSXXEC%', 'GlobalDato.id <> ' => 'PERSXXEC',),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
            return $values;
	}

        function get_solicitud_templates($tipo = 0){
            $conditions = array();
            $conditions['GlobalDato.id LIKE'] = 'MUTUIMPR'.$tipo.'%';
            $conditions['GlobalDato.id <>'] = 'MUTUIMPR';
//            $conditions['GlobalDato.entero_1'] = 1;
            $values = $this->GlobalDato->find('list',array('conditions'=>$conditions,'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.id'));
            return $values;            
        }
        function get_solicitud_anexos(){
            $conditions = array();
            $conditions['GlobalDato.id LIKE'] = 'MUTUIMPR9%';
            $conditions['GlobalDato.id <>'] = 'MUTUIMPR';
//            $conditions['GlobalDato.entero_1'] = 2;
            $values = $this->GlobalDato->find('list',array('conditions'=>$conditions,'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.id'));
            return $values;            
        }        
        
        function get_empresas_info_deuda(){
            $conditions = array();
            $conditions['GlobalDato.id LIKE'] = 'MUTUSOIN%';
            $conditions['GlobalDato.id <>'] = 'MUTUSOIN';
//            $conditions['GlobalDato.entero_1'] = 2;
            $values = $this->GlobalDato->find('list',array('conditions'=>$conditions,'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.id'));
            return $values;            
            
        }
        
        function get_webservices_intranet(){
            $conditions = array();
            $conditions['GlobalDato.id LIKE'] = 'PERSINTR%';
            $conditions['GlobalDato.id <>'] = 'PERSINTR';
            $conditions['GlobalDato.logico_1'] = TRUE;
            $values = $this->GlobalDato->find('all',array('conditions'=>$conditions, 'order' => 'GlobalDato.id'));
            return $values;            
            
        }
        
        function get_solicitud_documentos(){
            $conditions = array();
            $conditions['GlobalDato.id LIKE'] = 'MUTUIMPR8%';
//            $conditions['GlobalDato.concepto_2 <>'] = 'MUTUIMPR';
//            $conditions['GlobalDato.entero_1'] = 2;
            $values = $this->GlobalDato->find('list',array('conditions'=>$conditions,'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.id'));
            return $values;            
        } 
        
    function get_estados_disponibles_solicitud($estado_solicitud) {
        $values = $this->GlobalDato->find('list', array('conditions' => array('GlobalDato.id = ' => $estado_solicitud, 'GlobalDato.id <> ' => 'MUTUESTA'), 'fields' => 'GlobalDato.concepto_3'));
        foreach ($values as $valor) {
            $estados_disponibles = explode("|", $valor);
            $estados = array();
            foreach ($estados_disponibles as $estado) {
                $tmp = $this->GlobalDato->read('id,concepto_1', $estado);
                $estados[$tmp['GlobalDato']['id']] = $tmp['GlobalDato']['concepto_1'];
            }
        }
        return $estados;
    }

    function get_estados_solicitud_vendedor() {
        $values = $this->GlobalDato->find('list', array('conditions' => array('GlobalDato.logico_2 = ' => '1', 'GlobalDato.id like ' => 'MUTUESTA0%'), 'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
        return $values;
    }

    function get_tipo_situaciones_cuotas() {
        $values = $this->GlobalDato->find('list', array('conditions' => array('GlobalDato.id LIKE ' => 'MUTUSICU%', 'GlobalDato.id <> ' => 'MUTUSICU'), 'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
        return $values;
	}
	
	
	function liquidaciones($id = NULL){

		if (!empty($this->data)) {
			if ($this->GlobalDato->save($this->data)){
				$this->Auditoria->log();
				$this->Mensaje->okGuardar();
				$this->redirect(array('action'=>'liquidaciones'));				
			}else{
				$this->Mensaje->errorGuardar();
			}		
		}

		$datos = $this->GlobalDato->find('all',array('conditions'=>array('GlobalDato.id LIKE ' => 'MUTUCORG%', 'GlobalDato.id <> ' => 'MUTUCORG'), 'order' => 'GlobalDato.concepto_1'));
		if(!empty($id)){
			$this->data = $this->GlobalDato->read(null,$id);
		}
		$this->set('datos',$datos);
		$this->set('spLiquidaPeriodo',$this->GlobalDato->spLiquidaPeriodo);
		$this->set('spLiquidaMora',$this->GlobalDato->spLiquidaMora);
	}
        
    function productos_siisa(){
        $values = $this->GlobalDato->find('list',array('conditions'=>array('GlobalDato.id LIKE ' => 'PERSSISA%', 'GlobalDato.id <> ' => 'PERSSISA', 'GlobalDato.concepto_2' => 'LINEA'),'fields' => array('GlobalDato.concepto_1'), 'order' => 'GlobalDato.concepto_1'));
        return $values;
    }        
        
}
?>
<?php
class EjerciciosController extends ContabilidadAppController{
	var $name = 'Ejercicios';
	var $uses = array('contabilidad.Ejercicio', 'contabilidad.PlanCuenta', 'contabilidad.Asiento');
	
	var $autorizar = array('get_ejercicio'
	);
	
	function beforeFilter(){  
		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();  
	}	
	
	function index(){
		$this->set('Ejercicios', $this->Ejercicio->traerEjercicio());
	}
	
	function add(){
		if(!empty($this->data)):
			if($this->Ejercicio->antesGrabar($this->data)):
				if($this->Ejercicio->guardar($this->data)):
					$okPlanCuenta = true;
					if($this->data['Ejercicio']['copiar'] == 1):
						$okPlanCuenta = $this->PlanCuenta->CopiarPlanCuenta($this->Ejercicio->getLastInsertID());
					endif;
					$this->Auditoria->log();
				
					if($okPlanCuenta) $this->Mensaje->okGuardar();
					else $this->Mensaje->errorGuardar();
				
					$this->redirect('index');
				else:
					$this->Mensaje->errorGuardar();
				endif;
			else:
				$this->Mensaje->errorGuardar();
			endif;
		endif;
	}
	
	function edit($id=null){
		if(empty($id)) $this->redirect('index');
		if (!empty($this->data)):
			if($this->Ejercicio->guardar($this->data)):
				$okPlanCuenta = true;
				if($this->data['Ejercicio']['copiar'] == 1):
					$okPlanCuenta = $this->PlanCuenta->CopiarPlanCuenta($this->data['Ejercicio']['id']);
				endif;
				$this->Auditoria->log();
				
				if($okPlanCuenta) $this->Mensaje->okGuardar();
				else $this->Mensaje->errorGuardar();
				
				$this->redirect(array('action'=>'index'));				
			else:
				$this->Mensaje->errorGuardar();
			endif;	
		endif;
		$this->data = $this->Ejercicio->read(null,$id);
		$existe = $this->PlanCuenta->find('count', array('conditions' => array('PlanCuenta.co_ejercicio_id' => $id)));
		$this->data['Ejercicio']['existe'] = ($existe > 0 ? 1 : 0);
// debug($this->data);
// exit;

	}
	
	function del($id = null){
		if(empty($id)) $this->redirect('index');
		if ($this->Ejercicio->del($id)):
			$this->Mensaje->okBorrar();
			$this->Auditoria->log();
		else:
			$this->Mensaje->error("No se puede borrar el Ejercicio " .$id ." porque tiene Plan de Cuentas y Asientos." );
		endif;
		$this->redirect('index');
	}	
	
	function get_ejercicio(){
		$return = $this->Ejercicio->find('list',array('fields' => array('Ejercicio.descripcion'), 'order' => 'Ejercicio.fecha_desde'));
		return $return;
	}
	
	
        function trasladar_ejercicio($respuesta=0){
/*
 *
 * INSERT INTO `permisos` (`id`, `descripcion`, `url`, `order`, `main`, `icon`, `parent`) VALUES ('505', 'Trasladar Ejercicio', '/contabilidad/ejercicios/trasladar_ejercicio', '505', '1', 'arrow_right2.gif', '500'); 
 * INSERT INTO `grupos_permisos` (`grupo_id`, `permiso_id`) VALUES ('1', '505'); 
*/
//            debug("HOLA");
//            exit;
            $ejercicio = $this->Ejercicio->traeEjercVigente();
            $ejerPos = $this->Ejercicio->traeEjercicioPos($ejercicio['id']);
            $aPlanCtaVig = $this->PlanCuenta->traerPlanCuenta($ejercicio['id']);
            $aPlanCtaPos = $this->PlanCuenta->traerPlanCuenta($ejerPos['id']);
            $existeFinal = $this->Asiento->existeFinal($ejercicio['id']);
            $proceso = 0;
            $exito = 0;
            if(!empty($this->data)){
                $exito = $this->Ejercicio->trasladar_ejercicio($this->data);
                $proceso = 1;
//                exit;
            }
//            debug($existeFinal);
//            debug($ejerPos);
//            exit;

            $this->set('cancela', $respuesta);
            $this->set('ejercicio', $ejercicio);
            $this->set('ejerPos', $ejerPos);
            $this->set('aPlanCtaPos', $aPlanCtaPos['datos']);
            $this->set('existeFinal', 1);
            $this->set('proceso', $proceso);
            $this->set('exito', $exito);
            
            
        }
}
?>
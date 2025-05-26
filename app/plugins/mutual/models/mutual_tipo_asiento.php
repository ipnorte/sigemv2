<?php

/**
*
* mutual_tipo_asiento.php
* @author adrian [* 19/07/2012]
*/

class MutualTipoAsiento extends MutualAppModel{
	
	var $name = 'MutualTipoAsiento';
	
	function cargar($id = null,$all = true){
		
		$conditions = array();
		if(!empty($id)){
			$tipo = $this->read(null,$id);
			return $this->armaDatos($tipo);
		}else if($all){
			$tipos = $this->find('all');
			if(empty($tipos)) return null;
			foreach($tipos as $idx => $tipo){
				$tipos[$idx] = $this->armaDatos($tipo);
			}
			return $tipos;
		}else{
			return null;
		}
	}
	
	function cargarList(){
		$tipos = $this->find('list',array('fields' => array('MutualTipoAsiento.concepto')));
		return $tipos;
	}
	
	function borrar($id){
		$tipo = $this->read(null,$id);
		if(empty($tipo)) return false;
		App::import('Model','mutual.MutualTipoAsientoRenglon');
		$oRENG = new MutualTipoAsientoRenglon();
		if(!$oRENG->deleteAll("MutualTipoAsientoRenglon.mutual_tipo_asiento_id = $id")){
			parent::rollback();
			return false;			
		}
		if(!$this->del($id)){
			parent::rollback();
			return false;
		}
		
		parent::commit();
		return true;
	}
	
	function armaDatos($tipo){
		
		App::import('Model','mutual.MutualTipoAsientoRenglon');
		$oRENG = new MutualTipoAsientoRenglon();

		App::import('Model','contabilidad.PlanCuenta');
		$oCTA = new PlanCuenta();		
		
		$renglones = $oRENG->find('all',array('conditions' => array('MutualTipoAsientoRenglon.mutual_tipo_asiento_id' => $tipo['MutualTipoAsiento']['id'])));
		
		$tipo['TipoAsiento']['renglones'] = array();
		
		foreach($renglones as $idx => $renglon){
			
			$cuenta = $oCTA->getCuenta($renglon['MutualTipoAsientoRenglon']['co_plan_cuenta_id']);
			$tipo['MutualTipoAsiento']['renglones'][$idx]['id'] = $renglon['MutualTipoAsientoRenglon']['id'];
			$tipo['MutualTipoAsiento']['renglones'][$idx]['variable'] = $renglon['MutualTipoAsientoRenglon']['variable'];
			$tipo['MutualTipoAsiento']['renglones'][$idx]['co_plan_cuenta_id'] = $renglon['MutualTipoAsientoRenglon']['co_plan_cuenta_id'];
			$tipo['MutualTipoAsiento']['renglones'][$idx]['cuenta'] = $cuenta['PlanCuenta']['codigo'] . " " . $cuenta['PlanCuenta']['descripcion'];
			$tipo['MutualTipoAsiento']['renglones'][$idx]['debe_haber'] = $renglon['MutualTipoAsientoRenglon']['debe_haber'];
			
		}
		
		return $tipo;
	}
	
	
	function guardar($datos){
		
		$tipo = array();
		
		parent::begin();
		
//		$tipo['MutualTipoAsiento']['id'] = $datos['MutualTipoAsiento']['id'];
//		$tipo['MutualTipoAsiento']['concepto'] = $datos['MutualTipoAsiento']['descripcion'];
//		$tipo['MutualTipoAsiento']['tipo_asiento'] = $datos['MutualTipoAsiento']['tipo_asiento'];
		
		$this->id = $datos['MutualTipoAsiento']['id'];
		
		if(!$this->save($datos)){
			parent::rollback();
			return false;
		}

		App::import('Model','mutual.MutualTipoAsientoRenglon');
		$oRENG = new MutualTipoAsientoRenglon();
		
		if($datos['MutualTipoAsiento']['id'] != 0){
			if(!$oRENG->deleteAll("MutualTipoAsientoRenglon.mutual_tipo_asiento_id = " . $datos['MutualTipoAsiento']['id'])){
				parent::rollback();
				return false;			
			}			
		}
		
		$reng = array();
		$reng['MutualTipoAsientoRenglon']['id'] = 0;
		$reng['MutualTipoAsientoRenglon']['mutual_tipo_asiento_id'] = $this->id;
		$reng['MutualTipoAsientoRenglon']['variable'] = 'TOTAL';
		$reng['MutualTipoAsientoRenglon']['co_plan_cuenta_id'] = $datos['MutualTipoAsiento']['ngravcuenta'];
		$reng['MutualTipoAsientoRenglon']['debe_haber'] = $datos['MutualTipoAsiento']['ngravtipo'];
		$reng['MutualTipoAsientoRenglon']['tipo_asiento'] = $datos['MutualTipoAsiento']['tipo_asiento'];
		
		
		if(!$oRENG->save($reng)){
			parent::rollback();
			return false;
		}
		

		$reng = array();
		$reng['MutualTipoAsientoRenglon']['id'] = 0;
		$reng['MutualTipoAsientoRenglon']['mutual_tipo_asiento_id'] = $this->id;
		$reng['MutualTipoAsientoRenglon']['variable'] = 'PRODUCTO';
		$reng['MutualTipoAsientoRenglon']['co_plan_cuenta_id'] = $datos['MutualTipoAsiento']['totalcuenta'];
		$reng['MutualTipoAsientoRenglon']['debe_haber'] = $datos['MutualTipoAsiento']['totaltipo'];
		$reng['MutualTipoAsientoRenglon']['tipo_asiento'] = $datos['MutualTipoAsiento']['tipo_asiento'];
		
		
		if(!$oRENG->save($reng)){
			parent::rollback();
			return false;
		}
		
		parent::commit();
		
		return true;
	}
	
	
}

?>
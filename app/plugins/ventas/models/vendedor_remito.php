<?php
class VendedorRemito extends VentasAppModel{
	
	var $name = 'VendedorRemito';
	var $hasMany = array('MutualProductoSolicitud');
	var $belongsTo = array('Vendedor');
	
	function generar($datos){
		
		$remito = array();
		$remito['VendedorRemito']['id'] = 0;
		$remito['VendedorRemito']['vendedor_id'] = $datos['VendedorRemito']['vendedor_id'];
		$remito['VendedorRemito']['observaciones'] = $datos['VendedorRemito']['observaciones'];
		
		if(!parent::save($remito)){
			parent::notificar("Se produjo un error al crear el nuevo remito");
			return null;
		}
		
		$remito_id = $this->getLastInsertID();
		
		if(empty($remito_id)){
			parent::notificar("Numero de remito no generado");
			return null;
		}
		
		App::import('model','mutual.MutualProductoSolicitud');
		$oSOLICITUD = new MutualProductoSolicitud();
		
//		foreach($datos['VendedorRemito']['solicitud_id']  as $id => $solicitud){
//			$oSOLICITUD->id = $id;
//			$oSOLICITUD->saveField("vendedor_remito_id", $remito_id);
//			$oSOLICITUD->saveField("estado", "MUTUESTA0002");
//                        $solicitud = $oSOLICITUD->read(null,$id);
//                        $oSOLICITUD->guardarHistorial($solicitud);
//		}

		  
                
		foreach($datos['VendedorRemito']['solicitud_id']  as $id => $solicitud){
		    $APROBADA = $oSOLICITUD->isAprobada($id);
			$oSOLICITUD->id = $id;
			$oSOLICITUD->saveField("vendedor_remito_id", $remito_id);
			if(!$APROBADA) {
			    $oSOLICITUD->saveField("estado", $datos['VendedorRemito']['estado_solicitud']);
			}
            $solicitud = $oSOLICITUD->read(null,$id);
            $oSOLICITUD->guardarHistorial($solicitud,$datos['VendedorRemito']['observaciones']);
            if($datos['VendedorRemito']['estado_solicitud'] == 'MUTUESTA0000'){
                $oSOLICITUD->anular($id);
            }
		}                
		
		$remito = $this->read(null,$remito_id);

		return $remito;
	}
	
	function cargarRemito($id){

		$remito = $this->read(null,$id);

		App::import('model','ventas.Vendedor');
		$oVENDEDOR = new Vendedor();

		$vendedor = $oVENDEDOR->getVendedor($remito['VendedorRemito']['vendedor_id']);
		$remito['Vendedor'] = $vendedor;
		
		if(!empty($remito['MutualProductoSolicitud'])){
			App::import('model','mutual.MutualProductoSolicitud');
			$oSOLICITUD = new MutualProductoSolicitud();			
			foreach ($remito['MutualProductoSolicitud'] as $i => $solicitud){
				$armaDatos['MutualProductoSolicitud'] = $solicitud;
				$armaDatos = $oSOLICITUD->armaDatos($armaDatos);
				$remito['MutualProductoSolicitud'][$i] = $armaDatos['MutualProductoSolicitud'];
			}
		}
		return $remito;		
	}
	
	function getRemitosByVendedor($vendedor_id,$armaDatos = true){
		$this->unbindModel(array('hasMany' => array('MutualProductoSolicitud'),'belongsTo' => array('Vendedor')));
		$datos = $this->find("all",array('conditions' => array('VendedorRemito.vendedor_id' => $vendedor_id),'order' => 'VendedorRemito.created DESC'));
		if(!$armaDatos) return $datos;
		$remitos = array();
		if(!empty($datos)){
			foreach($datos as $dato){
				array_push($remitos,$this->cargarRemito($dato['VendedorRemito']['id']));
			}
		}
		return $remitos;
	}
	
}
?>
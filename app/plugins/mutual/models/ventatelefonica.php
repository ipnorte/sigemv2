<?php

/**
*
* ventatelefonica.php
* @author adrian [* 23/05/2012]
*/

App::import('Model','pfyj.Persona');
App::import('Model','pfyj.SocioAdicional');
App::import('Model','mutual.MutualServicioSolicitud');

class Ventatelefonica extends MutualAppModel{
	
	var $name = 'Ventatelefonica';
	var $useTable = false;	
	var $oPERSONA = null;
	var $oADICIONAL = null;
	var $oSERVICIO = null;
	
	function __construct(){
		$this->oPERSONA = new Persona();
		$this->oADICIONAL = new SocioAdicional();
		$this->oSERVICIO = new MutualServicioSolicitud();
		parent::__construct();
	}
	

	function altaNuevaSolicitud($datosPost){
		if($this->oSERVICIO->ifExists($datosPost['MutualServicioSolicitud']['mutual_servicio_id'],$datosPost['MutualServicioSolicitud']['persona_id'])){
			App::import('Model','mutual.MutualServicio');
			$oSERV = new MutualServicio();			
			$servicio = $oSERV->getNombreProveedorServicio($datosPost['MutualServicioSolicitud']['mutual_servicio_id']);
			parent::notificar("EL SOCIO YA CUENTA CON EL SERVICIO $servicio");
			return false;
		}
		if(!$this->oSERVICIO->generarNuevaSolicitud($datosPost)) return false;
		
		$solicitud = $this->oSERVICIO->getSolicitud($this->oSERVICIO->id);
		
		$proveedor_id = $this->GlobalDato('entero_1',$solicitud['MutualServicioSolicitud']['mutual_servicio_codigo']);
		
		if($proveedor_id = $this->lomas_villa_allende){
			App::import('Vendor','lomas_villa_allende');
			$oLOMASVA = new LomasVillaAllende($proveedor_id);
			$solicitud = $oLOMASVA->setValoresServicio($solicitud);
			return $this->oSERVICIO->saveAll($solicitud);			
		}
		return true;
	}
	
	
	function altaNuevoAdicional($datosPost){
		
		$ERROR = false;
		$cadena = parent::GlobalDato('concepto_1',$datosPost['SocioAdicional']['tipo_documento']). " - " .$datosPost['SocioAdicional']['documento']." " . $datosPost['SocioAdicional']['apellido']." ".$datosPost['SocioAdicional']['nombre'];		
		if($this->oADICIONAL->isExistsByTdocNdoc($datosPost['SocioAdicional']['tipo_documento'],$datosPost['SocioAdicional']['documento'])){
			parent::notificar("YA EXISTE UN ADICIONAL PARA EL TIPO Y NRO DE DOCUMENTO INDICADO [$cadena]");
			$ERROR = true;
		}else if(!$this->oADICIONAL->guardar($datosPost)){
			parent::notificar("SE PRODUJO UN ERROR AL GUARDAR EL ADICIONAL [$cadena]");
			$ERROR = true;
		}
				
		if(!$ERROR){
			
			$adic = $this->oADICIONAL->getByTdocNdoc($datosPost['SocioAdicional']['tipo_documento'],$datosPost['SocioAdicional']['documento']);
			
			if(!empty($datosPost['MutualServicioSolicitud']['mutual_servicio_id'])){
				
				foreach($datosPost['MutualServicioSolicitud']['mutual_servicio_id'] as $servicio_id => $beneficio_id){
					if(!$this->oSERVICIO->anexarAdicionales($servicio_id,$datosPost['MutualServicioSolicitudAdicional']['periodo_desde'],array($adic['SocioAdicional']['id']),$datosPost['MutualServicioSolicitudAdicional']['fecha_alta'],$beneficio_id)){
						parent::notificar("SE PRODUJO UN ERROR AL INCORPORAR EL ADICIONAL AL SERVICIO #$servicio_id");
						$ERROR = true;
						break;
					}
					
				}
			}

		}
		
		return ($ERROR ? false : true);
		

	}	
	
}

?>
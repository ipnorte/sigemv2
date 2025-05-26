<?php
class SocioConvenio extends PfyjAppModel{
	
	var $name = "SocioConvenio";
	var $belongsTo = array('Socio');
	var $hasMany = array('SocioConvenioCuota');
	
	function getConveniosBySocio($socio_id,$detalleOrginales=false){
		if(!$detalleOrginales) $this->unbindModel(array('hasMany' => array('SocioConvenioCuota')));
		$convenios = $this->find('all',array('conditions' => array('SocioConvenio.socio_id' => $socio_id)));
		foreach($convenios as $idx => $convenio){
			$convenios[$idx] = $this->__armaDatos($convenio);
		}
		return $convenios;
	}
	
	function getConvenio($id){
		$convenio = $this->read(null,$id);
		$convenio = $this->__armaDatos($convenio);
		return $convenio;
	}
	
	function __armaDatos($convenio){
		App::import('Model','Proveedores.Proveedor');
		$oPROVEEDOR = new Proveedor();
		$convenio['SocioConvenio']['proveedor_razon_social_resumida'] = $oPROVEEDOR->getRazonSocialResumida($convenio['SocioConvenio']['proveedor_id']);
		$convenio['SocioConvenio']['proveedor_razon_social'] = $oPROVEEDOR->getRazonSocial($convenio['SocioConvenio']['proveedor_id']);
		App::import('Model','Pfyj.PersonaBeneficio');
		$oPB = new PersonaBeneficio();		
		$convenio['SocioConvenio']['beneficio_str'] = $oPB->getStrBeneficio($convenio['SocioConvenio']['persona_beneficio_id']);
		$convenio['SocioConvenio']['organismo_desc'] = $oPB->getOrganismo($convenio['SocioConvenio']['persona_beneficio_id']);
		$tipoConvenio = parent::getGlobalDato('concepto_1',$convenio['SocioConvenio']['tipo_convenio']);
		$convenio['SocioConvenio']['tipo_convenio_desc'] = $tipoConvenio['GlobalDato']['concepto_1'];
		
		if(isset($convenio['SocioConvenioCuota']) && !empty($convenio['SocioConvenioCuota'])){
			App::import('Model','Mutual.OrdenDescuentoCuota');
			$oCuota = new OrdenDescuentoCuota();
			$cuota = null;			
			foreach($convenio['SocioConvenioCuota'] as $idx => $convenioCuota){
				$cuota = $oCuota->getCuota($convenioCuota['orden_descuento_cuota_id']);
				$convenioCuota['OrdenDescuentoCuota'] = $cuota['OrdenDescuentoCuota'];
				$convenio['SocioConvenioCuota'][$idx] = $convenioCuota;
			}
		}
		return $convenio;
	}
	

	function generarConvenio($datos,$preview=false){
		$convenio = array();
		$tipoProducto = 'MUTUPROD0004';
		$tipoOrden = "CONVE";
		$fechaConvenio = date('Y-m-d');
		$tipoConvenio = 'MUTUTCON0002';
		
		$glb = $this->getGlobalDato('concepto_2,entero_1',$tipoProducto);
		$vtos = $this->__armaVtos($glb['GlobalDato']['entero_1'],$datos['SocioConvenio']['persona_beneficio_id'],$fechaConvenio);		
		
		$convenio['SocioConvenio'] = array(
				'proveedor_id' => $glb['GlobalDato']['entero_1'],
				'persona_beneficio_id' => $datos['SocioConvenio']['persona_beneficio_id'],
				'socio_id' => $datos['SocioConvenio']['socio_id'],
				'fecha' => $fechaConvenio,
				'tipo_convenio' => $tipoConvenio,
				'periodo_ini' => $vtos['inicia_en'],
				'primer_vto_socio' => $vtos['vto_primer_cuota_socio'],
				'primer_vto_proveedor' => $vtos['vto_primer_cuota_proveedor'],
				'cuotas' => $datos['SocioConvenio']['cuotas'],
				'importe_total' => $datos['SocioConvenio']['importe'],
				'importe_cuota' => $datos['SocioConvenio']['importe'] / $datos['SocioConvenio']['cuotas'],
		);
		$convenio['SocioConvenioCuota'] = array();
		$detalleConvenio = array();
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
				
		foreach($datos['SocioConvenioCuota']['orden_descuento_cuota_id'] as $orden_descuento_cuota_id => $importeCuota){
			$importe = $importeCuota / 100;
			$ponderado = ($importe / $datos['SocioConvenio']['importe']) * 100;
			$proveedor_id = $oCuota->getProveedor($orden_descuento_cuota_id);
			$detalleConvenio['orden_descuento_cuota_id'] = $orden_descuento_cuota_id;
			$detalleConvenio['proveedor_id'] = $proveedor_id;
			$detalleConvenio['importe'] = $importe;
			$detalleConvenio['ponderacion'] = $ponderado;
			array_push($convenio['SocioConvenioCuota'],$detalleConvenio);
		}
		//GRABO LA CABECERA Y EL DETALLE DEL CONVENIO
		if(!$preview)if(!parent::saveAll($convenio)) return false;

		//genero la orden de descuento y las cuotas
		App::import('Model','Mutual.OrdenDescuento');
		$oODTO = new OrdenDescuento();
		$OrdenDto = array();
		
		$convenio_id = (!$preview ? $this->getLastInsertID() : 0);
		
		$OrdenDto['OrdenDescuento'] = array(
						'fecha' => $convenio['SocioConvenio']['fecha'],
						'tipo_orden_dto' => $tipoOrden,
						'numero' => $convenio_id,
						'tipo_producto' => $tipoProducto,
						'socio_id' => $convenio['SocioConvenio']['socio_id'],
						'persona_beneficio_id' => $convenio['SocioConvenio']['persona_beneficio_id'],
						'proveedor_id' => $glb['GlobalDato']['entero_1'],
						'mutual_producto_id' => 0,
						'periodo_ini' => $vtos['inicia_en'],
						'importe_cuota' => $datos['SocioConvenio']['importe'] / $datos['SocioConvenio']['cuotas'],
						'importe_total' => $datos['SocioConvenio']['importe'],
						'primer_vto_socio' => $vtos['vto_primer_cuota_socio'],
						'primer_vto_proveedor' => $vtos['vto_primer_cuota_proveedor'],
						'cuotas' => $datos['SocioConvenio']['cuotas'],
					);
		$OrdenDto['OrdenDescuentoCuota'] = $oCuota->armaCuotas($OrdenDto);							
//		$nroCuota = 1;
//		$cuota = array();
//		$cuotas = array();
//
//		$inicio = $convenio['SocioConvenio']['periodo_ini'];
//		$mIni = substr($inicio,4,2);
//		$yIni = substr($inicio,0,4);
//		$mkIni = mktime(0,0,0,$mIni,1,$yIni);	
//		$mkIniVtoSocio = mktime(0,0,0,date('m',strtotime($OrdenDto['OrdenDescuento']['primer_vto_socio'])),date('d',strtotime($OrdenDto['OrdenDescuento']['primer_vto_socio'])),date('Y',strtotime($OrdenDto['OrdenDescuento']['primer_vto_socio'])));
//		$mkIniVtoProv = mktime(0,0,0,date('m',strtotime($OrdenDto['OrdenDescuento']['primer_vto_proveedor'])),date('d',strtotime($OrdenDto['OrdenDescuento']['primer_vto_proveedor'])),date('Y',strtotime($OrdenDto['OrdenDescuento']['primer_vto_proveedor'])));
//		
//		$i = 0;
//		for($nroCuota=1;$nroCuota <= $datos['SocioConvenio']['cuotas']; $nroCuota++){
//			
//				$periodoCuota = date('Ym',$this->addMonthToDate($mkIni,$i));
//				$vtoSocio = date('Y-m-d',$this->addMonthToDate($mkIniVtoSocio,$i));
//				$vtoProv = date('Y-m-d',$this->addMonthToDate($mkIniVtoProv,$i));
//				
//			
//				$cuota['persona_beneficio_id'] = $convenio['SocioConvenio']['persona_beneficio_id'];
//				$cuota['socio_id'] = $convenio['SocioConvenio']['socio_id'];
//				$cuota['tipo_orden_dto'] = $tipoOrden;
//				$cuota['tipo_producto'] = $tipoProducto;
//				$cuota['periodo'] = $periodoCuota;
//				$cuota['nro_cuota'] = $nroCuota;
//				$cuota['tipo_cuota'] = $glb['GlobalDato']['concepto_2'];
//				$cuota['situacion'] = 'MUTUSICUMUTU';
//				$cuota['importe'] = $datos['SocioConvenio']['importe'] / $datos['SocioConvenio']['cuotas'];
//				$cuota['proveedor_id'] = $glb['GlobalDato']['entero_1'];
//				$cuota['vencimiento'] = $vtoSocio;
//				$cuota['vencimiento_proveedor'] = $vtoProv;				
//				array_push($cuotas,$cuota);
//				$i++;
//		}
//		$OrdenDto['OrdenDescuentoCuota'] = $cuotas;
		//GRABO LA CABECERA Y CUOTAS DE LA ORDEN DE DESCUENTO
		if(!$preview)if(!$oODTO->saveAll($OrdenDto)) return false;
		
		//devuelvo la vista previa	
		$convenio['orden_descuento'] = $OrdenDto;
		if($preview) return $this->__armaDatos($convenio);
		
		
		$orden_descuento_id = $oODTO->getLastInsertID();
		
		//actualizo en el convenio el id de la orden de descuento
		$this->id = $this->getLastInsertID();
		if(!$this->saveField('orden_descuento_id',$orden_descuento_id)) return false;
		$return = true;
		//marco las cuotas como en convenio
		foreach($datos['SocioConvenioCuota']['orden_descuento_cuota_id'] as $orden_descuento_cuota_id => $importeCuota){
			if(!$oCuota->cambiarEstado($orden_descuento_cuota_id,'C')){
				$return = false;
				break;
			}
		}
		return $return;		
	
	}
	
	
	function __armaVtos($proveedor_id,$persona_beneficio_id,$fecha){
		App::import('Model', 'Proveedores.ProveedorVencimiento');
		$oVto = new ProveedorVencimiento(null);
		return $oVto->calculaVencimiento($proveedor_id,$persona_beneficio_id,$fecha);
	}	
	
}
?>
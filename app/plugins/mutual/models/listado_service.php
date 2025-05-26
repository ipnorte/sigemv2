<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */
class ListadoService extends MutualAppModel{
	
	var $name = 'ListadoService';
	var $useTable = false;
	
	function getTemporalByID($id){
		App::import('Model','Shells.AsincronoTemporal');
		$oTMP = new AsincronoTemporal();
		$oTMP->unbindModel(array('hasMany' => array('AsincronoTemporalDetalle')));
		return $oTMP->read(null,$id);		
	}
	
	function getTemporal($pid,$bindDetalle=true,$order=array()){
		App::import('Model','Shells.AsincronoTemporal');
		$oTMP = new AsincronoTemporal();
		if(!$bindDetalle)$oTMP->unbindModel(array('hasMany' => array('AsincronoTemporalDetalle')));
		$datos = $oTMP->find('all',array('conditions' => array('AsincronoTemporal.asincrono_id' => $pid),'order' => $order));
		return $datos;
	}
	
	
	function getTemporalByConditions($bindDetalle=true,$conditions=array(),$order=array(),$fields = array(),$group = array()){
		App::import('Model','Shells.AsincronoTemporal');
		$oTMP = new AsincronoTemporal();
		if(!$bindDetalle)$oTMP->unbindModel(array('hasMany' => array('AsincronoTemporalDetalle')));
		else $oTMP->bindModel(array('hasMany' => array('AsincronoTemporalDetalle')));
		$datos = $oTMP->find('all',array('conditions' => $conditions,'order' => $order,'fields' => $fields,'group' => $group));
		return $datos;
	}
	

	function getTemporalByOpciones($bindDetalle=true,$options=array()){
		App::import('Model','Shells.AsincronoTemporal');
		$oTMP = new AsincronoTemporal();
		if(!$bindDetalle)$oTMP->unbindModel(array('hasMany' => array('AsincronoTemporalDetalle')));
		$datos = $oTMP->find('all',$options);
		return $datos;
	}	
	
	
	function getDetalleTemporal($id,$bindHead=true,$order=array()){
		App::import('Model','Shells.AsincronoTemporalDetalle');
		$oTMPD = new AsincronoTemporalDetalle();
		if(!$bindHead)$oTMPD->unbindModel(array('belongsTo' => array('AsincronoTemporal')));
		$datos = $oTMPD->find('all',array('conditions' => array('AsincronoTemporalDetalle.asincrono_temporal_id' => $id),'order' => $order));
		$datos = Set::extract('{n}.AsincronoTemporalDetalle',$datos);
		return $datos;		
	}
	
	
	function getDetalleToExcel($conditions=array(),$order=array(),$headers=array(),$fields = array(),$group = array()){
		
		$xls = array();
		$datos = $this->getTemporalByConditions(false,$conditions,$order,$fields,$group);
		$datos = Set::extract('{n}.AsincronoTemporal',$datos);

		foreach($datos as $idx => $registro){
			
			if(!empty($headers)):
			
				foreach($headers as $key => $header):
					$xls[$header] = $registro[$key];
				endforeach;
				
			else:
			
					$xls = $registro;
					
			endif;
						
			$datos[$idx] = $xls;
		}
		
		return $datos;	
	}
	
	function getTemporalFacturas($pid,$id,$bindDetalle=false){
		App::import('Model','Shells.AsincronoTemporal');
		$oTMP = new AsincronoTemporal();
		if(!$bindDetalle)$oTMP->unbindModel(array('hasMany' => array('AsincronoTemporalDetalle')));
		$order = array();
		$datos = $oTMP->find('all',array('conditions' => array('AsincronoTemporal.asincrono_id' => $pid),'order' => $order));
		$datos = Set::extract("/AsincronoTemporal[decimal_3>0]",$datos);
		$datos = Set::extract("{n}.AsincronoTemporal",$datos);
		$factura = $this->getFacturas($datos, $id);
		return $factura;
	}
	
	
	function getFacturas($proveedores, $id){
		$oLiq = $this->importarModelo('Liquidacion','mutual');
		$liquidacion = $oLiq->cargar($id);

		$PROVEEDOR = 0;
		$PRIMERO = true;
		$IMPUTADO = 0;
		$COMISION = 0;
		$COMISION_REVERSO = 0;
		$REVERSO = 0;
		$RAZON_SOCIAL = '';
		
		$porComision = 0;
		$cantidad = 0;
		
		$aFacturas = array();
		$tmpFacturas = array();
		
		foreach($proveedores as $dato):
		
//			DEBUG($dato);
			
			if(trim($dato['entero_1']) != MUTUALPROVEEDORID):
				if($PROVEEDOR != trim($dato['entero_1'])):
						
					if($PRIMERO):
						$PRIMERO = false;
					else:
						if($IMPUTADO > 0):
							$tmpFacturas['proveedor_id'] = $PROVEEDOR;
							$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
							$tmpFacturas['descripcion'] = 'COBRANZA ' . $liquidacion['Liquidacion']['periodo_desc'];
							$tmpFacturas['importe'] = $IMPUTADO;
							$tmpFacturas['cantidad'] = 0;
							$tmpFacturas['tipo'] = 'E';
							$tmpFacturas['tipo_documento'] = 'FAC';
							array_push($aFacturas, $tmpFacturas);
						endif;
							
						if($REVERSO > 0):
							$tmpFacturas['proveedor_id'] = $PROVEEDOR;
							$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
							$tmpFacturas['descripcion'] = 'REVERSO ' . $liquidacion['Liquidacion']['periodo_desc'];
							$tmpFacturas['importe'] = $REVERSO;
							$tmpFacturas['cantidad'] = 1;
							$tmpFacturas['tipo'] = 'E';
							$tmpFacturas['tipo_documento'] = 'NCR';
							array_push($aFacturas, $tmpFacturas);
						endif;
						
						if($COMISION > 0):
							$tmpFacturas['proveedor_id'] = $PROVEEDOR;
							$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
							$tmpFacturas['descripcion'] = 'COMISION COBRANZA ' . $liquidacion['Liquidacion']['periodo_desc'];
							$tmpFacturas['importe'] = $COMISION;
							$tmpFacturas['cantidad'] = 1;
							$tmpFacturas['tipo'] = 'I';
							$tmpFacturas['tipo_documento'] = 'FA';
							array_push($aFacturas, $tmpFacturas);
						endif;
							
						if($COMISION_REVERSO > 0):
							$tmpFacturas['proveedor_id'] = $PROVEEDOR;
							$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
							$tmpFacturas['descripcion'] = 'COMISION POR REVERSO ' . $liquidacion['Liquidacion']['periodo_desc'];
							$tmpFacturas['importe'] = $COMISION_REVERSO;
							$tmpFacturas['cantidad'] = 0;
							$tmpFacturas['tipo'] = 'I';
							$tmpFacturas['tipo_documento'] = 'NC';
							array_push($aFacturas, $tmpFacturas);
						endif;
						
					endif;
					
					$PROVEEDOR = trim($dato['entero_1']);
					$RAZON_SOCIAL = trim($dato['texto_1']);
					$IMPUTADO = 0;
					$COMISION = 0;
					$COMISION_REVERSO = 0;
					$REVERSO = 0;
						
					$porComision = 0;
					$cantidad = 0;
				endif;
						
				if($dato['texto_4'] != 'REVERSADO'):
					$IMPUTADO += $dato['decimal_6'];
					$COMISION +=  $dato['decimal_11'];
				else:
					$REVERSO += $dato['decimal_14'];
					$COMISION_REVERSO += ($dato['decimal_11']);
				endif;
				if($dato['decimal_10'] > 0.00):
					$porComision += $dato['decimal_10'];
					$cantidad += 1; 
				endif;
			endif;					
		endforeach;
		
		if($IMPUTADO > 0):
			$tmpFacturas['proveedor_id'] = $PROVEEDOR;
			$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
			$tmpFacturas['descripcion'] = 'COBRANZA ' . $liquidacion['Liquidacion']['periodo_desc'];
			$tmpFacturas['importe'] = $IMPUTADO;
			$tmpFacturas['cantidad'] = 0;
			$tmpFacturas['tipo'] = 'E';
			$tmpFacturas['tipo_documento'] = 'FAC';
			array_push($aFacturas, $tmpFacturas);
		endif;
							
		if($REVERSO > 0):
			$tmpFacturas['proveedor_id'] = $PROVEEDOR;
			$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
			$tmpFacturas['descripcion'] = 'REVERSO ' . $liquidacion['Liquidacion']['periodo_desc'];
			$tmpFacturas['importe'] = $REVERSO;
			$tmpFacturas['cantidad'] = 0;
			$tmpFacturas['tipo'] = 'E';
			$tmpFacturas['tipo_documento'] = 'NCR';
			array_push($aFacturas, $tmpFacturas);
		endif;
					
		if($COMISION > 0):
			$tmpFacturas['proveedor_id'] = $PROVEEDOR;
			$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
			$tmpFacturas['descripcion'] = 'COMISION COBRANZA ' . $liquidacion['Liquidacion']['periodo_desc'];
			$tmpFacturas['importe'] = $COMISION;
			$tmpFacturas['cantidad'] = 1;
			$tmpFacturas['tipo'] = 'I';
			$tmpFacturas['tipo_documento'] = 'FA';
			array_push($aFacturas, $tmpFacturas);
		endif;
							
		if($COMISION_REVERSO > 0):
			$tmpFacturas['proveedor_id'] = $PROVEEDOR;
			$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
			$tmpFacturas['descripcion'] = 'COMISION POR REVERSO ' . $liquidacion['Liquidacion']['periodo_desc'];
			$tmpFacturas['importe'] = $COMISION_REVERSO;
			$tmpFacturas['cantidad'] = 1;
			$tmpFacturas['tipo'] = 'I';
			$tmpFacturas['tipo_documento'] = 'NC';
			array_push($aFacturas, $tmpFacturas);
		endif;
						
//		debug($aFacturas);
//		exit;
		
		return $aFacturas;
	}
	

	function getTemporalFacturaLiquidacion($pid,$id,$bindDetalle=false){
		App::import('Model','Shells.AsincronoTemporal');
		$oTMP = new AsincronoTemporal();
		if(!$bindDetalle)$oTMP->unbindModel(array('hasMany' => array('AsincronoTemporalDetalle')));
		$order = array();
		$datos = $oTMP->find('all',array('conditions' => array('AsincronoTemporal.asincrono_id' => $pid),'order' => $order));
		$datos = Set::extract("/AsincronoTemporal[decimal_3>0]",$datos);
		$datos = Set::extract("{n}.AsincronoTemporal",$datos);
		$factura = $this->getFacturaLiquidacion($datos, $id);
		return $factura;
	}
	
	
	function getFacturaLiquidacion($proveedores, $id){
		$oLiq = $this->importarModelo('Liquidacion','mutual');
		$liquidacion = $oLiq->cargar($id);

		$PROVEEDOR = 0;
		$PRIMERO = true;
		$IMPUTADO = 0;
		$COMISION = 0;
		$COMISION_REVERSO = 0;
		$REVERSO = 0;
		$RAZON_SOCIAL = '';
		
		$porComision = 0;
		$cantidad = 0;
		
		$aFacturas = array();
		$tmpFacturas = array();
		
		foreach($proveedores as $dato):
		
//			DEBUG($dato);
			
			if(trim($dato['entero_1']) != MUTUALPROVEEDORID):
				if($PROVEEDOR != trim($dato['entero_1'])):
						
					if($PRIMERO):
						$PRIMERO = false;
					else:
						if($IMPUTADO > 0):
							$tmpFacturas['proveedor_id'] = $PROVEEDOR;
							$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
							$tmpFacturas['descripcion_proveedor'] = 'COBRANZA ' . $liquidacion['Liquidacion']['periodo_desc'];
							$tmpFacturas['descripcion_cliente'] = 'COMISION COBRANZA ' . $liquidacion['Liquidacion']['periodo_desc'];
							$tmpFacturas['importe_proveedor'] = $IMPUTADO;
							$tmpFacturas['importe_cliente'] = $COMISION;
							$tmpFacturas['cantidad'] = 0;
							$tmpFacturas['tipo_documento_proveedor'] = 'FAC';
							$tmpFacturas['tipo_documento_cliente'] = 'FA';
							$tmpFacturas['proveedor_factura_id'] = 0;
							$tmpFacturas['cliente_factura_id'] = 0;
							array_push($aFacturas, $tmpFacturas);
						endif;
							
						if($REVERSO > 0):
							$tmpFacturas['proveedor_id'] = $PROVEEDOR;
							$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
							$tmpFacturas['descripcion_proveedor'] = 'REVERSO ' . $liquidacion['Liquidacion']['periodo_desc'];
							$tmpFacturas['descripcion_cliente'] = 'COMISION POR REVERSO ' . $liquidacion['Liquidacion']['periodo_desc'];
							$tmpFacturas['importe_proveedor'] = $REVERSO;
							$tmpFacturas['importe_cliente'] = $COMISION_REVERSO;
							$tmpFacturas['cantidad'] = 1;
							$tmpFacturas['tipo'] = 'E';
							$tmpFacturas['tipo_documento_proveedor'] = 'NCR';
							$tmpFacturas['tipo_documento_cliente'] = 'NC';
							$tmpFacturas['proveedor_factura_id'] = 0;
							$tmpFacturas['cliente_factura_id'] = 0;
							array_push($aFacturas, $tmpFacturas);
						endif;
						
						
					endif;
					
					$PROVEEDOR = trim($dato['entero_1']);
					$RAZON_SOCIAL = trim($dato['texto_1']);
					$IMPUTADO = 0;
					$COMISION = 0;
					$COMISION_REVERSO = 0;
					$REVERSO = 0;
						
					$porComision = 0;
					$cantidad = 0;
				endif;
						
				if($dato['texto_4'] != 'REVERSADO'):
					$IMPUTADO += $dato['decimal_6'];
					$COMISION +=  $dato['decimal_11'];
				else:
					$REVERSO += $dato['decimal_14'];
					$COMISION_REVERSO += ($dato['decimal_11']);
				endif;
				if($dato['decimal_10'] > 0.00):
					$porComision += $dato['decimal_10'];
					$cantidad += 1; 
				endif;
			endif;					
		endforeach;
		
		if($IMPUTADO > 0):
			$tmpFacturas['proveedor_id'] = $PROVEEDOR;
			$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
			$tmpFacturas['descripcion_proveedor'] = 'COBRANZA ' . $liquidacion['Liquidacion']['periodo_desc'];
			$tmpFacturas['descripcion_cliente'] = 'COMISION COBRANZA ' . $liquidacion['Liquidacion']['periodo_desc'];
			$tmpFacturas['importe_proveedor'] = $IMPUTADO;
			$tmpFacturas['importe_cliente'] = $COMISION;
			$tmpFacturas['cantidad'] = 0;
			$tmpFacturas['tipo_documento_proveedor'] = 'FAC';
			$tmpFacturas['tipo_documento_cliente'] = 'FA';
			$tmpFacturas['proveedor_factura_id'] = 0;
			$tmpFacturas['cliente_factura_id'] = 0;
			array_push($aFacturas, $tmpFacturas);
		endif;
							
		if($REVERSO > 0):
			$tmpFacturas['proveedor_id'] = $PROVEEDOR;
			$tmpFacturas['razon_social'] = $RAZON_SOCIAL;
			$tmpFacturas['descripcion_proveedor'] = 'REVERSO ' . $liquidacion['Liquidacion']['periodo_desc'];
			$tmpFacturas['descripcion_cliente'] = 'COMISION POR REVERSO ' . $liquidacion['Liquidacion']['periodo_desc'];
			$tmpFacturas['importe_proveedor'] = $REVERSO;
			$tmpFacturas['importe_cliente'] = $COMISION_REVERSO;
			$tmpFacturas['cantidad'] = 1;
			$tmpFacturas['tipo'] = 'E';
			$tmpFacturas['tipo_documento_proveedor'] = 'NCR';
			$tmpFacturas['tipo_documento_cliente'] = 'NC';
			$tmpFacturas['proveedor_factura_id'] = 0;
			$tmpFacturas['cliente_factura_id'] = 0;
			array_push($aFacturas, $tmpFacturas);
		endif;
								
//		debug($aFacturas);
//		exit;
		
		return $aFacturas;
	}
}
?>
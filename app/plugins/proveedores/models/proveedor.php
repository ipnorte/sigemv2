<?php
class Proveedor extends ProveedoresAppModel{
	var $name = 'Proveedor';
	var $mutual_proveedor_ID = MUTUALPROVEEDORID;
//	var $hasMany = 'MutualProducto';
	
    var $hasMany = array(
        'MutualProducto' => array(
						            'className'  => 'MutualProducto',
						            'conditions' => array('MutualProducto.activo' => '1'),
						            'order'      => 'MutualProducto.tipo_producto DESC'
        					),
        'MutualServicio' => array(
						            'className'  => 'MutualServicio',
						            'conditions' => array('MutualServicio.activo' => '1'),
						            'order'      => 'MutualServicio.tipo_producto DESC'
        					),
        'ProveedorPrioridadImputaOrganismo'
        					
    );	
	
    /**
     * genera un array de datos con el proveedor y todos sus productos que son de caracter permanente
     * (se liquidan mensualmente)
     * @return unknown_type
     */
    function proveedoresProductosMensuales(){
    	$this->bindModel(array('hasMany' => array('MutualProducto' => array('conditions' => array('MutualProducto.activo' => '1','MutualProducto.mensual' => '1')))));
    	$proveedores = $this->find('all',array('order' => array('Proveedor.razon_social')));
    	$datos = array();
    	foreach($proveedores as $proveedor){
    		if(count($proveedor['MutualProducto'])!=0){
    			foreach($proveedor['MutualProducto'] as $idx => $producto){
    				$glb = parent::getGlobalDato('concepto_1',$producto['tipo_producto']);
    				$producto['str'] =  $proveedor['Proveedor']['razon_social_resumida'] . ' - ' .$glb['GlobalDato']['concepto_1'] . ($producto['importe_fijo'] > 0 ? " (PERMANENTE $ " . number_format($producto['importe_fijo'],2) .")" : '');
    				$proveedor['MutualProducto'][$idx] = $producto;
    			}
    			array_push($datos,$proveedor);
    		}
    	}
    	return $datos;
    }
    
    
    function proveedoresProductos($proveedor_id = null){
    	
    	$this->bindModel(array('hasMany' => array('MutualProducto' => array('conditions' => array('MutualProducto.activo' => '1')))));
    	$conditions = array();
    	if(!empty($proveedor_id)) $conditions['Proveedor.id'] = $proveedor_id;
    	$proveedores = $this->find('all',array('conditions' => $conditions,'order' => array('Proveedor.razon_social')));
    	$datos = array();
    	foreach($proveedores as $proveedor){
    		if(count($proveedor['MutualProducto'])!=0){
    			foreach($proveedor['MutualProducto'] as $idx => $producto){
    				$glb = parent::getGlobalDato('concepto_1',$producto['tipo_producto']);
    				$producto['str'] =  $proveedor['Proveedor']['razon_social_resumida'] . ' - ' .$glb['GlobalDato']['concepto_1'] . ($producto['importe_fijo'] > 0 ? " (PERMANENTE $ " . number_format($producto['importe_fijo'],2) .")" : '');
    				$proveedor['MutualProducto'][$idx] = $producto;
    			}
    			array_push($datos,$proveedor);
    		}
    	}
    	return $datos;
    }    
    

     /**
     * genera un array de datos con el proveedor y todos sus servicios
     * (se liquidan mensualmente)
     * @return unknown_type
     */   
    function proveedoresServiciosActivos($soloCallCenter = false,$soloActivos = TRUE){
        
        $conditions = array();
        
        if($soloActivos){ $conditions['MutualServicio.activo'] = 1;}
        if ($soloCallCenter){$conditions['MutualServicio.call_center'] = 1;}
        
        $this->bindModel(array('hasMany' => array('MutualServicio' => array('conditions' => $conditions))));

        $proveedores = $this->find('all',array('order' => array('Proveedor.razon_social')));
    	$datos = array();
    	foreach($proveedores as $proveedor){
    		if(count($proveedor['MutualServicio'])!=0){
 	    		foreach($proveedor['MutualServicio'] as $idx => $producto){
	    			$glb = parent::getGlobalDato('concepto_1',$producto['tipo_producto']);
	    			$producto['str'] =  $proveedor['Proveedor']['razon_social_resumida'] . ' - ' .$glb['GlobalDato']['concepto_1'];
	   				$proveedor['MutualServicio'][$idx] = $producto;
	    		}
	    		array_push($datos,$proveedor);
    		}
    	}
    	return $datos;
    }    
    
    
    function getProveedor($id, $conSaldo=0){
    	
    	$this->unbindModel(array('hasMany' => array('MutualProducto')));
    	$proveedor = $this->read(null,$id);
        $proveedor['Proveedor']['destinatario'] = $proveedor['Proveedor']['responsable'];
            
    	$proveedor = $this->__armaDatos($proveedor);
    	if($conSaldo) $proveedor['Proveedor']['saldo'] = $this->__traerSaldo($id);
        	return $proveedor;
    } 
    
    public function getProveedorByCuit($cuit){
        $proveedores = $this->find('all',array('conditions' => array('Proveedor.cuit' => $cuit)));
        return (!empty($proveedores) ? $proveedores[0] : null);
    }
    
    function getRazonSocial($id){
    	$this->unbindModel(array('hasMany' => array('MutualProducto')));
    	$proveedor = $this->read('razon_social',$id);
    	return $proveedor['Proveedor']['razon_social'];
    }    
    
    function getRazonSocialResumida($id){
    	$this->unbindModel(array('hasMany' => array('MutualProducto')));
    	$proveedor = $this->read('razon_social_resumida',$id);
    	return $proveedor['Proveedor']['razon_social_resumida'];
    }
    
    
    function proveedoresOrdenDto($orden_descuento_id,$soloCuotasAdeudadas=true){
    	$proveedores = array();
    	App::import('Model'.'Mutual.OrdenDescuentoCuota');
    	$oODTOC = new OrdenDescuentoCuota();
    	if($soloCuotasAdeudadas)$resultados = $oODTOC->getProveedoresCuotasAdeudadasByOrdenDto($orden_descuento_id);
    	else $resultados = $oODTOC->getProveedoresCuotasByOrdenDto($orden_descuento_id);
    	
    	foreach($resultados as $proveedor){
    		$proveedores[$proveedor['OrdenDescuentoCuota']['proveedor_id']] = $this->getRazonSocial($proveedor['OrdenDescuentoCuota']['proveedor_id']);
    	}
		$proveedores[$this->mutual_proveedor_ID] = $this->getRazonSocial($this->mutual_proveedor_ID);    	
    	return $proveedores;
    }
    
   	function getProveedorByPIN($PIN){
//   		$proveedor = $this->findAllByCodigoAccesoWs($PIN);
   		$proveedores = $this->find('all',array('conditions' => array('Proveedor.codigo_acceso_ws' => $PIN)));
   		return (!empty($proveedores) ? $proveedores[0] : null);
   	}    
   
	function getDomicilio($proveedor){
		$domicilio = ltrim(rtrim($proveedor['Proveedor']['calle'])) . " " .($proveedor['Proveedor']['numero_calle'] != 0 ? $proveedor['Proveedor']['numero_calle'] : '');
		if(!empty($proveedor['Proveedor']['piso'])) $domicilio .= ' Piso ' . ltrim(rtrim($proveedor['Proveedor']['piso'])); 
		if(!empty($proveedor['Proveedor']['dpto'])) $domicilio .= ' Dpto ' . ltrim(rtrim($proveedor['Proveedor']['dpto']));
		$domicilio .= ' - ' . ltrim(rtrim($proveedor['Proveedor']['barrio']));
		$domicilio .= ' - ' . ltrim(rtrim($proveedor['Proveedor']['localidad'])) . ' (CP: ' . $proveedor['Proveedor']['codigo_postal'] . ')'; 
		return $domicilio;
	}
	
	function __armaDatos($proveedor){
		if(isset($proveedor['Proveedor'])){
			$proveedor['Proveedor']['domicilio'] = $this->getDomicilio($proveedor);
			
			if(strlen($proveedor['Proveedor']['cuit']) == 11) $proveedor['Proveedor']['formato_cuit'] = substr($proveedor['Proveedor']['cuit'],0,2) . '-' . substr($proveedor['Proveedor']['cuit'], 2,8) . '-' . substr($proveedor['Proveedor']['cuit'], 10);
			else $proveedor['Proveedor']['formato_cuit'] = $proveedor['Proveedor']['cuit'];
			
			$glb = $this->getGlobalDato('concepto_1',$proveedor['Proveedor']['condicion_iva']);
			$proveedor['Proveedor']['iva_concepto'] = $glb['GlobalDato']['concepto_1'];
			
		}
		return $proveedor;
	}
	
	
	function grabar($datos){
		$oCliente = $this->importarModelo('Cliente', 'clientes');
		$regCliente = $oCliente->find('all', array('conditions' => array('Cliente.proveedor_id' => $datos['Proveedor']['id'])));
		
		$aFactura = array();

		$aFactura['id'] = 0;
		$aFactura['tipo'] = $datos['Proveedor']['tipo_saldo'] == 0 ? 'SD' : 'SA';
		$aFactura['fecha_comprobante'] = $datos['Proveedor']['fecha_saldo'];
		$aFactura['vencimiento1'] = $datos['Proveedor']['fecha_saldo'];
		$aFactura['tipo_comprobante'] = 'SALDOPROVEED';
		$aFactura['importe_no_gravado'] = $datos['Proveedor']['importe_saldo']; 
		$aFactura['total_comprobante'] = $datos['Proveedor']['importe_saldo']; 
		$aFactura['importe_venc1'] = $datos['Proveedor']['importe_saldo']; 
		$aFactura['estado'] = 'A'; 
			
		if($datos['Proveedor']['proveedor_factura_id'] != 0) $aFactura['id'] = $datos['Proveedor']['proveedor_factura_id'];

		$this->begin();
		if($this->save($datos)):
			if(!isset($datos['Proveedor']['id'])):
				$id = $this->getLastInsertID();
				App::import('model','proveedores.proveedor_vencimiento');
				$oVto = new ProveedorVencimiento();
				$oVto->cargarValoresPorDefault($id);	
			else:
				$id = $datos['Proveedor']['id'];
			endif;
                        
                        ###############################################################################
                        # CARGAR LA TABLA DE PROVEEDORES PRIORIDAD POR ORGANISMO
                        ###############################################################################
                        App::import('model','config.GlobalDato');
                        $oGLB = new GlobalDato();
                        $organismos = $oGLB->getOrganismos();

                        App::import('model','proveedores.ProveedorPrioridadImputaOrganismo');
                        $oPIP = new ProveedorPrioridadImputaOrganismo();   
                        
                        if(!empty($organismos)){
                            $prioridades = array();
                            foreach ($organismos as $idx => $organismo){
                                $data = $oPIP->get_by_proveedor($id, $organismo);
                                if(empty($data)) array_push($prioridades, array('proveedor_id' => $id, 'codigo_organismo' => $idx, 'prioridad' => ($id == 18 ? 1 : 5)) );
                            }
                            if(!empty($prioridades)){
                                if(!$oPIP->saveAll($prioridades)){
                                    $this->rollback();
                                    return 0;
                                }
                            }
                        }
                        ###############################################################################
                        
				
			if($datos['Proveedor']['importe_saldo'] != 0):
				$datos['Proveedor']['id'] = $id;
				$aFactura['proveedor_id'] = $id;
				$oFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
	    		if($oFactura->save($aFactura)):
	    			$datos['Proveedor']['proveedor_factura_id'] = ($aFactura['id'] != 0 ? $aFactura['id'] : $oFactura->getLastInsertID());
					if($this->save($datos)):
						$this->commit();
						return $id;
					else:
						$this->rollback();
						return 0;
					endif;
				else:
					$this->rollback();
					return 0;
				endif;
			else:
				$this->commit();
				return $id;
			endif;
	    else:
			$this->rollback();
			return 0;
		endif;
		
	}
	
	
	function getEstado($datos){
		if(!empty($datos['Proveedor']['proveedor_factura_id'])):
    		$oFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
    		$estado = ($oFactura->tienePago($datos['Proveedor']['proveedor_factura_id']) ? 'P' : 'A');
    	else:
    		$estado = 'A';
    	endif;
    	
    	return $estado;
		
	}
	
	function comboProveedores($agregar = NULL){
		$aProveedores = $this->find('all');
		if(!empty($agregar)):
			$cmbProveedores = array(0 => $agregar);
		else:
			$cmbProveedores = array();
		endif;

		$aProveedores = Set::extract("{n}.Proveedor",$aProveedores);
		$aProveedores = $this->asortMultiDimensional($aProveedores, 'razon_social');
		foreach($aProveedores as $Proveedor){
			if($Proveedor['tipo_proveedor'] == '1'):
				$cmbProveedores[$Proveedor['id']] = $Proveedor['razon_social'];
			endif;
		}
		
		return $cmbProveedores;
	}
	
 	function asortMultiDimensional ($toOrderArray, $field, $inverse = false) {  
     	$position = array();  
     	$newRow = array();  
     	foreach ($toOrderArray as $key => $row) {  
        		$position[$key]  = $row[$field];  
             	$newRow[$key] = $row;  
     	}  
     	if ($inverse) {  
         	arsort($position);  
     	}  
     	else {  
         	asort($position);  
     	}  
     	$returnArray = array();  
     	foreach ($position as $key => $pos) {       
         	$returnArray[] = $newRow[$key];  
     	}  
     	return $returnArray;  
 	}
 	
 	
    function getClienteId($id){
    	$this->unbindModel(array('hasMany' => array('MutualProducto')));
    	$proveedor = $this->read('cliente_id',$id);
    	return $proveedor['Proveedor']['cliente_id'];
    } 

    
    function getRazonSocialAndRazonSocialResumida($id){
    	$this->unbindModel(array('hasMany' => array('MutualProducto')));
    	$proveedor = $this->read('razon_social,razon_social_resumida',$id);
    	$ret['razon_social'] = $proveedor['Proveedor']['razon_social'];
    	$ret['razon_social_resumida'] = $proveedor['Proveedor']['razon_social_resumida'];
    	return $ret;
    }    
    
    
	function __traerSaldo($proveedor_id){
		$oFacturas = $this->importarModelo('ProveedorFactura', 'proveedores');
//		$sql = "SELECT 
//				ProveedorFactura.*, sum(ProveedorFactura.total_comprobante) as total,
//				sum(IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)) AS pago_comprobante,
//				
//				sum(if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo='FA',ProveedorFactura.total_comprobante, ProveedorFactura.total_comprobante * -1) - 
//				IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)) AS saldo,
//								
//				sum(if(ProveedorFactura.tipo != 'SD' And ProveedorFactura.tipo!='FA', IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//				WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id)-ProveedorFactura.total_comprobante,0),0)) AS saldo_comprobante
//				
//				FROM proveedor_facturas AS ProveedorFactura
//				WHERE
//				proveedor_id = $proveedor_id
//				group	by proveedor_id";

//		$sql = "SELECT 
//					sum(if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo = 'FA', total_comprobante, total_comprobante *-1)) + 
//					sum(if(ProveedorFactura.tipo != 'SD' and ProveedorFactura.tipo != 'FA', (SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//					WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id), 0)) as total_comprobante,
//				
//					sum(IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//					WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)) + ifnull((select sum(importe) from orden_pago_detalles as OrdenPagoDetalle
//					where OrdenPagoDetalle.tipo_pago = 'AN' and OrdenPagoDetalle.proveedor_id = ProveedorFactura.proveedor_id and OrdenPagoDetalle.id not in(
//					select orden_pago_detalle_id from orden_pago_facturas)),0)  AS pago_comprobante,
//					
//					sum(if(ProveedorFactura.tipo = 'SD' or ProveedorFactura.tipo = 'FA', total_comprobante, total_comprobante *-1)) + 
//					sum(if(ProveedorFactura.tipo != 'SD' and ProveedorFactura.tipo != 'FA', (SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//					WHERE OrdenPagoFactura.proveedor_credito_id = ProveedorFactura.id), 0)) -
//					(sum(IFNULL((SELECT SUM(importe) FROM orden_pago_facturas AS OrdenPagoFactura
//					WHERE OrdenPagoFactura.proveedor_factura_id = ProveedorFactura.id),0)) + ifnull((select sum(importe) from orden_pago_detalles as OrdenPagoDetalle
//					where OrdenPagoDetalle.tipo_pago = 'AN' and OrdenPagoDetalle.proveedor_id = ProveedorFactura.proveedor_id and OrdenPagoDetalle.id not in(
//					select orden_pago_detalle_id from orden_pago_facturas)),0))  AS saldo
//				
//					FROM proveedor_facturas AS ProveedorFactura
//					WHERE
//					proveedor_id = $proveedor_id
//					group	by proveedor_id
//				";
		

		$sql = "SELECT	
				(SELECT	SUM(total_comprobante)
				FROM	proveedor_facturas
				WHERE	proveedor_id = '$proveedor_id' AND tipo != 'NC') AS total_comprobante,
				
				(SELECT	SUM(total_comprobante)
				FROM	proveedor_facturas
				WHERE	proveedor_id = '$proveedor_id' AND tipo = 'NC') +
				
				
				(SELECT	SUM(importe)
				FROM	orden_pagos
				WHERE	proveedor_id = '$proveedor_id' AND anulado = 0) AS pago_comprobante,
				
				(SELECT	SUM(total_comprobante)
				FROM	proveedor_facturas
				WHERE	proveedor_id = '$proveedor_id' AND tipo != 'NC') -
				
				
				(SELECT	SUM(total_comprobante)
				FROM	proveedor_facturas
				WHERE	proveedor_id = '$proveedor_id' AND tipo = 'NC') -
				
				
				(SELECT	SUM(importe)
				FROM	orden_pagos
				WHERE	proveedor_id = '$proveedor_id' AND anulado = 0) AS saldo
				
				
				FROM	proveedor_facturas
				WHERE	proveedor_id = '$proveedor_id'
				GROUP	BY proveedor_id
		
		";
		
		
		$aFacturas = $this->query($sql);
		
		
		return $aFacturas[0][0]['saldo'];
		
	}
	
	function setReasignable($id){
		$this->id = $id;
		return parent::saveField("reasignable",1);
	}

	function unsetReasignable($id = null){
		if(!empty($id)):
			$this->id = $id;
			return parent::saveField("reasignable",0);
		else:
			return parent::updateAll(array('Proveedor.reasignable' => 0),array("1=1"));
		endif;
	}	
    
	
	function getComisiones($id,$organismo = null, $tipo = 'COB'){
		App::import('Model','Proveedores.ProveedorComision');
		$oCOM = new ProveedorComision();
		$comisiones = $oCOM->getComisionesByProveedor($id,$organismo,$tipo);
		return $comisiones;
	}
	
	
	function borrarComision($proveedor_id,$codigo_organismo,$fecha_vigencia,$comision){
		App::import('Model','Proveedores.ProveedorComision');
		$oCOM = new ProveedorComision();
		return $oCOM->borrarComision($proveedor_id,$codigo_organismo,$fecha_vigencia,$comision);
	}
	
	
	function guardarComision($datos){
		App::import('Model','Proveedores.ProveedorComision');
		$oCOM = new ProveedorComision();
		
		$organismos = array_keys($datos['ProveedorComision']['codigo_organismo']);
		$proveedorId = $datos['ProveedorComision']['proveedor_id'];
		$tipoComision = $datos['ProveedorComision']['tipo'];
		$comision = $datos['ProveedorComision']['comision'];

		return $oCOM->guardarComision($proveedorId,$organismos,$comision,$tipoComision);
	}
	
	function cargarProveedoresReasignacion($proveedor_id,$toList = false){
		
		$sql = "SELECT * FROM proveedores as Proveedor WHERE cuit IN
                (SELECT TRIM(concepto_3) FROM global_datos WHERE id LIKE 'PROVREAS%'
                AND id <> 'PROVREAS' AND (TRIM(concepto_2) = (SELECT cuit FROM proveedores WHERE id = $proveedor_id))
                OR TRIM(concepto_4) = (SELECT cuit FROM proveedores WHERE id = $proveedor_id)) 
                and Proveedor.reasignable = 1
                ORDER BY razon_social;";
		
		if(!$toList) { return $this->query($sql); }
		$datos = $this->query($sql);
		$lista = array();
		if(!empty($datos)){
			foreach ($datos as $dato):
			$lista[$dato['Proveedor']['id']] = $dato['Proveedor']['razon_social'];
			endforeach;
		}
		return $lista;
	
	}
        
        function proveedorSaldoOperativo($id){
            $proveedor = $this->read('liquida_prestamo',$id);
            
            if($proveedor['Proveedor']['liquida_prestamo'] === '0') return 0;
            
            App::import('Model','Proveedores.Movimiento');
            $oMovimiento = new Movimiento();
            $aSaldoOperativo = $oMovimiento->saldoOperativo($id);


            return $aSaldoOperativo[0][0]['importe'] * -1;
            
        }
	
}
?>
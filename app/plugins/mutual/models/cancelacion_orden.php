<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class CancelacionOrden extends MutualAppModel{
	
	var $name = 'CancelacionOrden';
	var $actsAs   = array('Mutual.transaction');
	var $hasMany = array('Mutual.CancelacionOrdenCuota');
	
	function getBySocio($socio_id){
		$cancelaciones = $this->findAllBySocioId($socio_id);
		return $this->armaDatos($cancelaciones);
	}
	
	function getBySocioByEstado($socio_id,$estado='E',$bindCuotas=false){
		if($bindCuotas)$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		$cancelaciones = $this->find('all',array('conditions' => array('CancelacionOrden.socio_id' => $socio_id,'CancelacionOrden.estado' => $estado)));
		return $this->armaDatos($cancelaciones,$bindCuotas);
	}
        
        function getCancelacionesBySolicitudV1($socio_id,$nro_solicitud,$estado='E'){            
            $sql = "select CancelacionOrden.* from cancelacion_ordenes CancelacionOrden
                    where CancelacionOrden.socio_id = $socio_id
                    and CancelacionOrden.estado = '$estado'
                    union
                    select CancelacionOrden.* from cancelacion_ordenes CancelacionOrden
                    where CancelacionOrden.socio_id = $socio_id
                    and CancelacionOrden.nro_solicitud = $nro_solicitud;";
            $data = $this->query($sql);
            $cancelaciones = array();
            if(!empty($data)){
                foreach($data as $cancelacion){
                    $tmp['CancelacionOrden'] = $cancelacion[0];
                    array_push($cancelaciones,$tmp);
                }
            }
            return $this->armaDatos($cancelaciones,FALSE);
        }
        
        
        /**
         * Devuelve las ordenes de cancelacion que pueden ser cobradas por caja. No tienen que estar asociadas a ninguna solicitud
         * @param type $socio_id
         * @return type
         */
        function get_socio_by_estado_cobrables($socio_id){
            $sql = "select CancelacionOrden.*,
                    ifnull((select sum(importe) from cancelacion_orden_cuotas coc
                    where coc.cancelacion_orden_id = CancelacionOrden.id),0) as total_cuotas_orden,
                    ifnull((select sum(odc.importe) from orden_descuento_cuotas odc,
                    cancelacion_orden_cuotas coc
                    where odc.id = coc.orden_descuento_cuota_id 
                    and coc.cancelacion_orden_id = CancelacionOrden.id),0) 
                    - ifnull((select sum(odcc.importe) from orden_descuento_cobro_cuotas odcc,
                    cancelacion_orden_cuotas coc
                    where odcc.orden_descuento_cuota_id = coc.orden_descuento_cuota_id 
                    and coc.cancelacion_orden_id = CancelacionOrden.id),0) as saldo_cuotas_orden             
                    from cancelacion_ordenes CancelacionOrden
                    where CancelacionOrden.socio_id = $socio_id
                    and CancelacionOrden.estado = 'E';";
            $cancelaciones = $this->query($sql);
            return $this->armaDatos($cancelaciones,FALSE);
        }
	
        /**
         * CARGA DE DATOS OPTIMIZADA PARA LA GRILLA
         * @param type $socio_id
         * @param type $estado
         * @return type
         */
        function get_socio_by_estado_2($socio_id,$estado='E',$cuota_id = NULL){
            
            $sql_aman_fiedls = null;
            $sql_aman_joins = null;
			
				
            
            if(MODULO_V1){

				App::import('Model','v1.Solicitud');
				$oSOLICITUD = new Solicitud();	
	
				$db = & ConnectionManager::getDataSource($oSOLICITUD->useDbConfig);
				$useDB = $db->config['database'];
	

                $sql_aman_fiedls = ",aman_solicitud.nro_solicitud
                                    ,aman_solicitud.codigo_producto
                                    ,aman_solicitud.estado
                                    ,aman_producto.descripcion
                                    ,aman_proveedor.codigo_proveedor
                                    ,aman_proveedor.razon_social
                                    ,aman_cod_estado.descripcion";
                $sql_aman_joins = " left join $useDB.solicitudes aman_solicitud on aman_solicitud.nro_solicitud = CancelacionOrden.nro_solicitud
                                    left join $useDB.proveedores_productos aman_producto on aman_producto.codigo_producto = aman_solicitud.codigo_producto
                                    left join $useDB.proveedores aman_proveedor on aman_proveedor.codigo_proveedor = aman_producto.codigo_proveedor
                                    left join $useDB.solicitud_codigo_estados aman_cod_estado on aman_cod_estado.codigo = aman_solicitud.estado"; 
            }
            
            $sql = "select 
                    CancelacionOrden.id
                    ,CancelacionOrden.socio_id
                    ,CancelacionOrden.estado
                    ,if(CancelacionOrden.estado = 'P','PROCESADA','EMITIDA') as estado_cancelacion_desc
                    ,if(CancelacionOrden.tipo_cancelacion = 'P','PARCIAL','TOTAL') as tipo_cancelacion_desc
                    ,CancelacionOrden.orden_descuento_id
                    ,concat(OrdenDescuento.tipo_orden_dto,' #'
                    ,OrdenDescuento.numero) as tipo_nro_orden_cancela
                    ,ProveedorOrigen.razon_social_resumida
                    ,TipoProducto.concepto_1
                    ,ProveedorDestino.razon_social_resumida
                    ,CancelacionOrden.concepto
                    ,CancelacionOrden.importe_proveedor
                    ,CancelacionOrden.saldo_orden_dto
                    ,CancelacionOrden.importe_seleccionado
                    ,TipoCuotaDiferencia.concepto_1
                    ,CancelacionOrden.importe_diferencia
                    ,CancelacionOrden.tipo_cuota_diferencia
                    ,CancelacionOrden.fecha_vto
                    ,(CancelacionOrden.importe_seleccionado + CancelacionOrden.importe_diferencia) as total_orden_cancela
                    ,CancelacionOrden.fecha_imputacion
                    ,CancelacionOrden.nueva_orden_dto_id
                    ,CancelacionOrden.observaciones
                    ,OrdenDescuentoNueva.id
                    ,concat(OrdenDescuentoNueva.tipo_orden_dto,' #'
                    ,OrdenDescuentoNueva.numero) as tipo_nro_orden_nueva
                    ,ProveedorCancela.razon_social_resumida
                    ,TipoProductoCancela.concepto_1
                    ,MutualProductoSolicitud.id
                    ,MutualProductoSolicitud.tipo_orden_dto
                    ,MutualProductoSolicitud.proveedor_id
                    ,MutualProductoSolicitud.tipo_producto
                    ,MutualProductoSolicitud.estado
                    ,ProveedorMutualProductoSolicitud.razon_social
                    ,TipoProductoMutualProductoSolicitud.concepto_1
                    ,EstadoMutualProductoSolicitud.concepto_1
                    ,OrdenDescuentoCobro.id
                    ,OrdenDescuentoCobro.tipo_cobro
                    ,TipoOrdenDescuentoCobro.concepto_1
                    ,OrdenDescuentoCobro.fecha
                    ,Recibo.id
                    ,Recibo.tipo_documento
                    ,Recibo.sucursal
                    ,Recibo.nro_recibo
                    ,Recibo.fecha_comprobante
                    ,ifnull((select group_concat(cu.nro_cuota) 
                    from cancelacion_orden_cuotas coc
                    inner join orden_descuento_cuotas cu on cu.id = coc.orden_descuento_cuota_id
                    where coc.cancelacion_orden_id = CancelacionOrden.id),'') as cuotas_str
                    ".(!empty($sql_aman_fiedls) ? " $sql_aman_fiedls " : "")."
                        
                    ,ifnull((select sum(importe) from cancelacion_orden_cuotas coc
                    where coc.cancelacion_orden_id = CancelacionOrden.id),0) as total_cuotas_orden,
                    ifnull((select sum(odc.importe) from orden_descuento_cuotas odc,
                    cancelacion_orden_cuotas coc
                    where odc.id = coc.orden_descuento_cuota_id 
                    and coc.cancelacion_orden_id = CancelacionOrden.id),0) 
                    - ifnull((select sum(odcc.importe) from orden_descuento_cobro_cuotas odcc,
                    cancelacion_orden_cuotas coc
                    where odcc.orden_descuento_cuota_id = coc.orden_descuento_cuota_id 
                    and coc.cancelacion_orden_id = CancelacionOrden.id),0) as saldo_cuotas_orden 
                    

                    from cancelacion_ordenes CancelacionOrden
                    ".(!empty($cuota_id) ? " inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = CancelacionOrden.id " : "")."
                    left join orden_descuentos OrdenDescuento on OrdenDescuento.id = CancelacionOrden.orden_descuento_id
                    left join proveedores ProveedorOrigen on ProveedorOrigen.id = CancelacionOrden.origen_proveedor_id
                    left join proveedores ProveedorDestino on ProveedorDestino.id = CancelacionOrden.orden_proveedor_id
                    left join global_datos TipoProducto on TipoProducto.id = OrdenDescuento.tipo_producto
                    left join global_datos TipoCuotaDiferencia on TipoCuotaDiferencia.id = CancelacionOrden.tipo_cuota_diferencia
                    left join orden_descuentos OrdenDescuentoNueva on OrdenDescuentoNueva.id = CancelacionOrden.nueva_orden_dto_id
                    left join proveedores ProveedorCancela on ProveedorCancela.id = OrdenDescuentoNueva.proveedor_id
                    left join global_datos TipoProductoCancela on TipoProductoCancela.id = OrdenDescuentoNueva.tipo_producto
                    left join mutual_producto_solicitud_cancelaciones CancelacionesMutualProductoSolicitud on CancelacionesMutualProductoSolicitud.cancelacion_orden_id = CancelacionOrden.id
                    left join mutual_producto_solicitudes MutualProductoSolicitud on MutualProductoSolicitud.id = CancelacionesMutualProductoSolicitud.mutual_producto_solicitud_id
                    left join proveedores ProveedorMutualProductoSolicitud on ProveedorMutualProductoSolicitud.id = MutualProductoSolicitud.proveedor_id
                    left join global_datos TipoProductoMutualProductoSolicitud on TipoProductoMutualProductoSolicitud.id = MutualProductoSolicitud.tipo_producto
                    left join global_datos EstadoMutualProductoSolicitud on EstadoMutualProductoSolicitud.id = MutualProductoSolicitud.estado
                    left join orden_descuento_cobros OrdenDescuentoCobro on OrdenDescuentoCobro.id = CancelacionOrden.orden_descuento_cobro_id
                    left join global_datos TipoOrdenDescuentoCobro on TipoOrdenDescuentoCobro.id = OrdenDescuentoCobro.tipo_cobro
                    left join recibos Recibo on Recibo.id = OrdenDescuentoCobro.recibo_id                    
                    ".(!empty($sql_aman_joins) ? " $sql_aman_joins " : "")."
                    where CancelacionOrden.socio_id = $socio_id
                    and CancelacionOrden.estado = '$estado'
                    ".(!empty($cuota_id) ? " and coc.orden_descuento_cuota_id = $cuota_id " : "")."    
                    order by CancelacionOrden.created;";
            $datos = $this->query($sql);
            $cancelaciones = array();
            if(!empty($datos)){
                
                foreach ($datos as $i => $dato){
//                    debug($dato);
                    $cancelaciones[$i]['CancelacionOrden'] = $dato['CancelacionOrden'];
                    $cancelaciones[$i]['CancelacionOrden']['cuotas_str'] = $dato[0]['cuotas_str'];
                    $cancelaciones[$i]['CancelacionOrden']['tipo_cuota_diferencia_desc'] = $dato['TipoCuotaDiferencia']['concepto_1'];
                    $cancelaciones[$i]['CancelacionOrden']['estado_cancelacion_desc'] = $dato[0]['estado_cancelacion_desc'];
                    $cancelaciones[$i]['CancelacionOrden']['tipo_cancelacion_desc'] = $dato[0]['tipo_cancelacion_desc'];
                    $cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_id'] = $dato['CancelacionOrden']['orden_descuento_id'];
                    $cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_tipo_nro'] = $dato[0]['tipo_nro_orden_cancela'];
                    $cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_total'] = $dato[0]['total_orden_cancela'];
                    $cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_proveedor'] = $dato['ProveedorOrigen']['razon_social_resumida'];
                    $cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_producto'] = $dato['TipoProducto']['concepto_1'];
                    $cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_proveedor_producto'] = $cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_proveedor'].(!empty($cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_producto']) ? "/".$cancelaciones[$i]['CancelacionOrden']['orden_dto_cancela_producto'] : "");
                    $cancelaciones[$i]['CancelacionOrden']['a_la_orden_de'] = $dato['ProveedorDestino']['razon_social_resumida'];
                    $cancelaciones[$i]['CancelacionOrden']['origen'] = (!empty($dato['CancelacionOrden']['orden_descuento_id']) ? "PROPIA" : "DE TERCEROS");
                    $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] = null;
                    $cancelaciones[$i]['CancelacionOrden']['mutual_producto_solicitud_id'] = null;
                    $cancelaciones[$i]['CancelacionOrden']['mutual_producto_solicitud_tipo_orden'] = null;
                    $cancelaciones[$i]['CancelacionOrden']['mutual_producto_solicitud_proveedor_id'] = null;
                    $cancelaciones[$i]['CancelacionOrden']['mutual_producto_solicitud_estado'] = null;
                    
                    $cancelaciones[$i]['CancelacionOrden']['total_cuotas_orden'] = 0;
                    $cancelaciones[$i]['CancelacionOrden']['saldo_cuotas_orden'] = 0;
                    if($estado == 'E'){
                        if(isset($dato[0]['total_cuotas_orden']) && !empty($dato[0]['total_cuotas_orden'])) $cancelaciones[$i]['CancelacionOrden']['total_cuotas_orden'] = $dato[0]['total_cuotas_orden'];
                        if(isset($dato[0]['saldo_cuotas_orden']) && !empty($dato[0]['saldo_cuotas_orden'])) $cancelaciones[$i]['CancelacionOrden']['saldo_cuotas_orden'] = $dato[0]['saldo_cuotas_orden'];
                    }
                    
                    if(MODULO_V1 && !empty($dato['aman_solicitud']['nro_solicitud'])){
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] = "EXPTE #".$dato['aman_solicitud']['nro_solicitud'];
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] .= " | ";
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] .= $dato['aman_proveedor']['razon_social']." / ".$dato['aman_producto']['descripcion'];
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] .= " (".$dato['aman_cod_estado']['descripcion'].")";
                        $cancelaciones[$i]['CancelacionOrden']['aman_nro_solicitud'] = $dato['aman_solicitud']['nro_solicitud'];
                        $cancelaciones[$i]['CancelacionOrden']['aman_codigo_proveedor'] = $dato['aman_proveedor']['codigo_proveedor'];
                        $cancelaciones[$i]['CancelacionOrden']['aman_codigo_producto'] = $dato['aman_solicitud']['codigo_producto'];
                        $cancelaciones[$i]['CancelacionOrden']['aman_codigo_estado'] = $dato['aman_solicitud']['estado'];
                    }else if(!empty($dato['MutualProductoSolicitud']['id'])){
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] = $dato['MutualProductoSolicitud']['tipo_orden_dto']." #".$dato['MutualProductoSolicitud']['id'];
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] .= "|";
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] .= $dato['ProveedorMutualProductoSolicitud']['razon_social']."/".$dato['TipoProductoMutualProductoSolicitud']['concepto_1'];
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] .= "(".$dato['EstadoMutualProductoSolicitud']['concepto_1'].")";
                        $cancelaciones[$i]['CancelacionOrden']['mutual_producto_solicitud_id'] = $dato['MutualProductoSolicitud']['id'];
                        $cancelaciones[$i]['CancelacionOrden']['mutual_producto_solicitud_tipo_orden'] = $dato['MutualProductoSolicitud']['tipo_orden_dto'];
                        $cancelaciones[$i]['CancelacionOrden']['mutual_producto_solicitud_proveedor_id'] = $dato['MutualProductoSolicitud']['proveedor_id'];
                        $cancelaciones[$i]['CancelacionOrden']['mutual_producto_solicitud_estado'] = $dato['MutualProductoSolicitud']['estado'];
                    }else if(!empty ($dato['OrdenDescuentoCobro']['id'])){

                        $cancelaciones[$i]['CancelacionOrden']['cobro_id'] = $dato['OrdenDescuentoCobro']['id'];
                        $cancelaciones[$i]['CancelacionOrden']['cobro_fecha'] = $dato['OrdenDescuentoCobro']['fecha'];
                        $cancelaciones[$i]['CancelacionOrden']['cobro_tipo'] = $dato['OrdenDescuentoCobro']['tipo_cobro'];
                        $cancelaciones[$i]['CancelacionOrden']['cobro_tipo_desc'] = $dato['TipoOrdenDescuentoCobro']['concepto_1'];
                        $cancelaciones[$i]['CancelacionOrden']['cobro_recibo_id'] = $dato['Recibo']['id'];
                        $cancelaciones[$i]['CancelacionOrden']['cobro_recibo_tipo'] = $dato['Recibo']['tipo_documento'];
                        $cancelaciones[$i]['CancelacionOrden']['cobro_recibo_sucursal'] = $dato['Recibo']['sucursal'];
                        $cancelaciones[$i]['CancelacionOrden']['cobro_recibo_nro_recibo'] = $dato['Recibo']['nro_recibo'];
                        
                        $cancelaciones[$i]['CancelacionOrden']['cancela_con_str'] .= "#".$dato['OrdenDescuentoCobro']['id'] . "-" . $dato['TipoOrdenDescuentoCobro']['concepto_1'] . " [" . $dato['OrdenDescuentoCobro']['fecha']."] / " . $dato['Recibo']['tipo_documento']."-".$dato['Recibo']['sucursal']."-".$dato['Recibo']['nro_recibo'];
                        
                    }
                    
//                    debug($dato);
                }
                
            }
            
//            debug($cancelaciones);
            
            return $cancelaciones;
            
        }
        
        
	function get($id,$bindCuotas=false){
            $this->unbindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		if($bindCuotas)$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		$orden = $this->read(null,$id);
		if(!empty($orden))return $this->armaDato($orden);
		else return null;
	}
	
	/**
	 * setea los datos adicionales para una cancelacion pasada por parametro
	 * @param $cancelacion
	 */
	function armaDato($cancelacion,$detallaCuotas=false,$detalleInfoPagoProveedor=true){
		App::import('model','Mutual.OrdenDescuento');
		$oOdto = new OrdenDescuento();
				
		App::import('model','Proveedores.Proveedor');
		$oProveedor = new Proveedor();	

		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();	

		App::import('model','Pfyj.Socio');
		$oSocio = new Socio();

		$cancelacion['CancelacionOrden']['tipo_cancelacion_desc'] = ($cancelacion['CancelacionOrden']['tipo_cancelacion'] == 'T' ? 'TOTAL' : 'PARCIAL');

		$oOdto->unbindModel(array('hasMany' => array('OrdenDescuentoCuota')));
		$orden = $oOdto->getOrden($cancelacion['CancelacionOrden']['orden_descuento_id']);
		
		$cancelacion['CancelacionOrden']['tipo_nro_odto'] = $orden['OrdenDescuento']['tipo_nro'];
		$cancelacion['CancelacionOrden']['proveedor_producto_odto'] = $orden['OrdenDescuento']['proveedor_producto'];
		$cancelacion['CancelacionOrden']['nro_referencia_proveedor'] = $orden['OrdenDescuento']['nro_referencia_proveedor'];
		
		$oProveedor->unbindModel(array('hasMany' => array('MutualProducto')));
		$proveedor = $oProveedor->read(null,$cancelacion['CancelacionOrden']['orden_proveedor_id']);
		$cancelacion['CancelacionOrden']['a_la_orden_de'] = $proveedor['Proveedor']['razon_social'];
		
		$glb = parent::getGlobalDato('concepto_1',$cancelacion['CancelacionOrden']['forma_cancelacion']);
		$cancelacion['CancelacionOrden']['forma_cancelacion_desc'] = $glb['GlobalDato']['concepto_1'];

		$glb = parent::getGlobalDato('concepto_1',$cancelacion['CancelacionOrden']['forma_pago']);
		$cancelacion['CancelacionOrden']['forma_pago_desc'] = $glb['GlobalDato']['concepto_1'];			
		
		$glb = parent::getGlobalDato('concepto_1',$cancelacion['CancelacionOrden']['tipo_cuota_diferencia']);
		$cancelacion['CancelacionOrden']['tipo_cuota_diferencia_desc'] = $glb['GlobalDato']['concepto_1'];
		$cancelacion['CancelacionOrden']['nc_nd_str'] = '';
		if($cancelacion['CancelacionOrden']['importe_diferencia'] != 0){
			$cancelacion['CancelacionOrden']['nc_nd_str'] = $cancelacion['CancelacionOrden']['tipo_cuota_diferencia_desc'].' $ '.$cancelacion['CancelacionOrden']['importe_diferencia'];
		}
		
                
                
                $cancelacion['CancelacionOrden']['total_cuotas_orden'] = 0;
                $cancelacion['CancelacionOrden']['saldo_cuotas_orden'] = 0;
                
                if(isset($cancelacion[0]['total_cuotas_orden']) && !empty($cancelacion[0]['total_cuotas_orden'])) $cancelacion['CancelacionOrden']['total_cuotas_orden'] = $cancelacion[0]['total_cuotas_orden'];
                if(isset($cancelacion[0]['saldo_cuotas_orden']) && !empty($cancelacion[0]['saldo_cuotas_orden'])) $cancelacion['CancelacionOrden']['saldo_cuotas_orden'] = $cancelacion[0]['saldo_cuotas_orden'];
                
		if(isset($cancelacion['CancelacionOrdenCuota']) && !empty($cancelacion['CancelacionOrdenCuota'])){
			foreach($cancelacion['CancelacionOrdenCuota'] as $i => $cuotaCancela){
				$cuota = $oCuota->getCuota($cuotaCancela['orden_descuento_cuota_id']);
				$cuotaCancela['OrdenDescuentoCuota'] = $cuota['OrdenDescuentoCuota'];
				$cancelacion['CancelacionOrdenCuota'][$i] = $cuotaCancela;
//                                debug($cuotaCancela);
//                                debug($cuota);
                                $cancelacion['CancelacionOrden']['total_cuotas_orden'] += $cuotaCancela['importe'];
                                $cancelacion['CancelacionOrden']['saldo_cuotas_orden'] += $cuota['OrdenDescuentoCuota']['saldo_cuota'];
			}
		}

		$cancelacion['CancelacionOrden']['beneficiario'] = $oSocio->getApenom($cancelacion['CancelacionOrden']['socio_id']);
		$cancelacion['CancelacionOrden']['beneficiario_apenom'] = $oSocio->getApenom($cancelacion['CancelacionOrden']['socio_id'],false);
		$cancelacion['CancelacionOrden']['beneficiario_tdocndoc'] = $oSocio->getTdocNdoc($cancelacion['CancelacionOrden']['socio_id']);
		
		$cancelacion['CancelacionOrden']['total_orden'] = $cancelacion['CancelacionOrden']['importe_seleccionado'] + $cancelacion['CancelacionOrden']['importe_diferencia'];
		
		$cancelacion['CancelacionOrden']['estado_desc'] = ($cancelacion['CancelacionOrden']['estado'] == 'E' ? 'EMITIDA' : 'PROCESADA');
		
		if($detallaCuotas){
			App::import('Model','Mutual.CancelacionOrdenCuota');
			$oCuotaCancela = new CancelacionOrdenCuota();
			$cuotas = $oCuotaCancela->getByOrden($cancelacion['CancelacionOrden']['id']);
			$cuotas = Set::extract('{n}.CancelacionOrdenCuota',$cuotas);
			$cancelacion['CancelacionOrdenCuota'] = $cuotas;
		}
		
		//nueva orden de descuento
		$cancelacion['CancelacionOrden']['norden_str'] = '';
		if($cancelacion['CancelacionOrden']['nueva_orden_dto_id'] != 0){
			$nOrden = $oOdto->getOrden($cancelacion['CancelacionOrden']['nueva_orden_dto_id']);
			$cancelacion['CancelacionOrden']['norden_nro'] = $cancelacion['CancelacionOrden']['nueva_orden_dto_id'];
			$cancelacion['CancelacionOrden']['norden_tipo_nro_odto'] = $nOrden['OrdenDescuento']['tipo_nro'];
			$cancelacion['CancelacionOrden']['norden_proveedor_producto_odto'] = $nOrden['OrdenDescuento']['proveedor_producto'];
			$cancelacion['CancelacionOrden']['norden_str'] = "ORD.# ".$cancelacion['CancelacionOrden']['nueva_orden_dto_id']."|".$nOrden['OrdenDescuento']['tipo_nro']."|".$nOrden['OrdenDescuento']['proveedor_producto'];				
		}		
		
		$cancelacion['CancelacionOrden']['cuotas_str'] = "";
		$cancelacion['CancelacionOrden']['periodo_str'] = "";
		
		if(!empty($cancelacion['CancelacionOrdenCuota'])):
			$tmp = array();
			$tmp = Set::extract("/CancelacionOrdenCuota/OrdenDescuentoCuota/nro_cuota",$cancelacion);
			$cancelacion['CancelacionOrden']['cuotas_str'] = implode('-', $tmp) ."/". $orden['OrdenDescuento']['cuotas'];
			$tmp = Set::extract("/CancelacionOrdenCuota/OrdenDescuentoCuota/periodo",$cancelacion);
			foreach($tmp as $key => $valor):
				$tmp[$key] = substr($valor,0,4) . "/" . substr($valor,-2);
			endforeach;
			$cancelacion['CancelacionOrden']['periodo_str'] = implode(';', $tmp);
		endif;
		$cancelacion['CancelacionOrden']['recibo_detalle'] = $orden['OrdenDescuento']['recibo_detalle'] . " CTA: " . $cancelacion['CancelacionOrden']['cuotas_str'];
		if($orden['OrdenDescuento']['tipo_orden_dto'] == 'CMUTU'):
			$cancelacion['CancelacionOrden']['recibo_detalle'] = $orden['OrdenDescuento']['recibo_detalle'] . " PER. " . $cancelacion['CancelacionOrden']['periodo_str'];
		endif;
		
		if(!empty($cancelacion['CancelacionOrden']['nro_solicitud']) && MODULO_V1):
			App::import('Model','V1.Solicitud');
			$oSOLICITUD = new Solicitud();		
			$solicitud = $oSOLICITUD->getSolicitud($cancelacion['CancelacionOrden']['nro_solicitud']);
			$solicitud = Set::extract("/Solicitud",$solicitud);
			$solicitud = $solicitud[0];
			$cancelacion['CancelacionOrden']['solicitud_str'] = "EXPTE #".$solicitud['Solicitud']['nro_solicitud'] . "|". $solicitud['Solicitud']['proveedor_producto'] . " (".$solicitud['Solicitud']['estado_descripcion'].")";
		endif;
		
		// DATOS DE LA COMISION COBRANZA
		$cancelacion['CancelacionOrden']['comision_cobranza'] = 0;
		if (!empty($cancelacion['CancelacionOrden']['orden_descuento_cobro_id'])):
			$sql = "select	sum(OrdenDescuentoCobroCuota.comision_cobranza) as comision_cobranza
					from orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
					inner join cancelacion_orden_cuotas CancelacionOrdenCuota
					on OrdenDescuentoCobroCuota.orden_descuento_cuota_id = CancelacionOrdenCuota.orden_descuento_cuota_id
					where CancelacionOrdenCuota.cancelacion_orden_id = ".$cancelacion['CancelacionOrden']['id']." and 
					OrdenDescuentoCobroCuota.orden_descuento_cobro_id = ".$cancelacion['CancelacionOrden']['orden_descuento_cobro_id'];
			
			$comision_cobranza = $this->query($sql);
			$cancelacion['CancelacionOrden']['comision_cobranza'] = $comision_cobranza[0][0]['comision_cobranza'];

		endif;
		//DATOS RELACIONADOS AL COBRO DE LA CANCELACION
		$cancelacion['CancelacionOrden']['Recibo'] = null;
		$cancelacion['CancelacionOrden']['ProveedorFactura'] = null;
		$cancelacion['CancelacionOrden']['OrdenPago'] = null;
		
		if($detalleInfoPagoProveedor){
			
			//cargo el recibo emitido al socio
			if(!empty($cancelacion['CancelacionOrden']['recibo_id'])){
				App::import('Model','clientes.Recibo');
				$oRECIBO = new Recibo();
				$recibo = $oRECIBO->getRecibo($cancelacion['CancelacionOrden']['recibo_id'],false,false);	
				if($recibo['Recibo']['anulado'] == 0) $cancelacion['CancelacionOrden']['Recibo'] = $recibo['Recibo'];
			}else{
				App::import('Model','mutual.OrdenDescuentoCobro');
				$oCOBRO = new OrdenDescuentoCobro();
				$cobro = $oCOBRO->getCobroByCancelacion($cancelacion['CancelacionOrden']['id'],false);
				if($cobro['OrdenDescuentoCobro']['Recibo']['anulado'] == 0)$cancelacion['CancelacionOrden']['Recibo'] = $cobro['OrdenDescuentoCobro']['Recibo'];
			}
			
			//cargo la factura emitida al proveedor
			App::import('Model','proveedores.ProveedorFactura');
			$oPROVFACT = new ProveedorFactura();
			
			if(!empty($cancelacion['CancelacionOrden']['proveedor_factura_id'])){
				$factura = $oPROVFACT->getFactura($cancelacion['CancelacionOrden']['proveedor_factura_id']);
				$cancelacion['CancelacionOrden']['ProveedorFactura'] = $factura;
			}else{
				$facturas = $oPROVFACT->getFacturasByCancelacionId($cancelacion['CancelacionOrden']['id']);
				$cancelacion['CancelacionOrden']['ProveedorFactura'] = $facturas[0]['ProveedorFactura'];
			}
			
			//cargo la orden de pago
			if(!empty($cancelacion['CancelacionOrden']['orden_pago_id'])){
				App::import('Model','proveedores.OrdenPago');
				$oORDENPAGO = new OrdenPago();
				$opago = $oORDENPAGO->getOrdenDePago($cancelacion['CancelacionOrden']['orden_pago_id']);
				if($opago['OrdenPago']['anulado'] == 0)$cancelacion['CancelacionOrden']['OrdenPago'] = $opago['OrdenPago'];
			}
			
			
		}
		
		return $cancelacion;	
	}
	
	function armaDatos($datos,$detallaCuotas=false){
		
//		App::import('model','Mutual.OrdenDescuento');
//		$oOdto = new OrdenDescuento();
//				
//		App::import('model','Proveedores.Proveedor');
//		$oProveedor = new Proveedor();	
//
//		App::import('model','Mutual.OrdenDescuentoCuota');
//		$oCuota = new OrdenDescuentoCuota();	
//
//		App::import('model','Pfyj.Socio');
//		$oSocio = new Socio();		
		
		foreach($datos as $idx => $dato){
			$datos[$idx] = $this->armaDato($dato,$detallaCuotas);
			
//			$datos[$idx]['CancelacionOrden']['tipo_cancelacion_desc'] = ($datos[$idx]['CancelacionOrden']['tipo_cancelacion'] == 'T' ? 'TOTAL' : 'PARCIAL');
//
//			$oOdto->unbindModel(array('hasMany' => array('OrdenDescuentoCuota')));
//			$orden = $oOdto->getOrden($dato['CancelacionOrden']['orden_descuento_id']);
//			
//			$datos[$idx]['CancelacionOrden']['tipo_nro_odto'] = $orden['OrdenDescuento']['tipo_nro'];
//			$datos[$idx]['CancelacionOrden']['proveedor_producto_odto'] = $orden['OrdenDescuento']['proveedor_producto'];
//			
//			$oProveedor->unbindModel(array('hasMany' => array('MutualProducto')));
//			$proveedor = $oProveedor->read(null,$dato['CancelacionOrden']['orden_proveedor_id']);
//			$datos[$idx]['CancelacionOrden']['a_la_orden_de'] = $proveedor['Proveedor']['razon_social'];
//			
//			$glb = parent::getGlobalDato('concepto_1',$dato['CancelacionOrden']['forma_cancelacion']);
//			$datos[$idx]['CancelacionOrden']['forma_cancelacion_desc'] = $glb['GlobalDato']['concepto_1'];
//
//			$glb = parent::getGlobalDato('concepto_1',$dato['CancelacionOrden']['forma_pago']);
//			$datos[$idx]['CancelacionOrden']['forma_pago_desc'] = $glb['GlobalDato']['concepto_1'];			
//			
//			$glb = parent::getGlobalDato('concepto_1',$dato['CancelacionOrden']['tipo_cuota_diferencia']);
//			$datos[$idx]['CancelacionOrden']['tipo_cuota_diferencia_desc'] = $glb['GlobalDato']['concepto_1'];
//			$datos[$idx]['CancelacionOrden']['nc_nd_str'] = '';
//			if($dato['CancelacionOrden']['importe_diferencia'] != 0){
//				$datos[$idx]['CancelacionOrden']['nc_nd_str'] = $datos[$idx]['CancelacionOrden']['tipo_cuota_diferencia_desc'].' $ '.$dato['CancelacionOrden']['importe_diferencia'];
//			}
//			
//			if(isset($dato['CancelacionOrdenCuota']) && !empty($dato['CancelacionOrdenCuota'])){
//				foreach($dato['CancelacionOrdenCuota'] as $i => $cuotaCancela){
//					$cuota = $oCuota->getCuota($cuotaCancela['orden_descuento_cuota_id']);
//					$cuotaCancela['OrdenDescuentoCuota'] = $cuota['OrdenDescuentoCuota'];
//					$datos[$idx]['CancelacionOrdenCuota'][$i] = $cuotaCancela;
//				}
//			}
//			
//			$datos[$idx]['CancelacionOrden']['beneficiario'] = $oSocio->getApenom($dato['CancelacionOrden']['socio_id']);
//			$datos[$idx]['CancelacionOrden']['total_orden'] = $dato['CancelacionOrden']['importe_seleccionado'] + $dato['CancelacionOrden']['importe_diferencia'];
//			
//			//nueva orden de descuento
//			$datos[$idx]['CancelacionOrden']['norden_str']  = '';
//			if($dato['CancelacionOrden']['nueva_orden_dto_id'] != 0){
//				$nOrden = $oOdto->getOrden($dato['CancelacionOrden']['nueva_orden_dto_id']);
//				$datos[$idx]['CancelacionOrden']['norden_nro'] = $dato['CancelacionOrden']['nueva_orden_dto_id'];
//				$datos[$idx]['CancelacionOrden']['norden_tipo_nro_odto'] = $nOrden['OrdenDescuento']['tipo_nro'];
//				$datos[$idx]['CancelacionOrden']['norden_proveedor_producto_odto'] = $nOrden['OrdenDescuento']['proveedor_producto'];
//				$datos[$idx]['CancelacionOrden']['norden_str'] = "ORD.# ".$dato['CancelacionOrden']['nueva_orden_dto_id']."|".$nOrden['OrdenDescuento']['tipo_nro']."|".$nOrden['OrdenDescuento']['proveedor_producto'];				
//			}
			
			
		}
		
		return $datos;
	}
	
	/**
	 * Genera una orden de cancelacion
	 * 
	 * @author adrian [01/02/2012]
	 * @param array $data (datos del post - view sel_cuota.ctp)
	 * @return boolean
	 */
	function generar($data){
		
		$this->begin();
		
		#armo los datos de la cabecera
		$data['CancelacionOrden']['id'] = 0;
		$data['CancelacionOrden']['orden_proveedor_id'] = $data['CancelacionOrden']['proveedor_id'];
		$data['CancelacionOrden']['importe_seleccionado'] = $data['CancelacionOrden']['importe'];
		
		$data['CancelacionOrden']['fecha_vto'] = parent::armaFecha($data['CancelacionOrden']['fecha_vto']);
		
		if(!isset($data['CancelacionOrden']['tipo_cancelacion'])) $data['CancelacionOrden']['tipo_cancelacion'] = 'P';
		
//		$data['CancelacionOrden']['tipo_cancelacion'] = 'T';
//		
//		if($data['CancelacionOrden']['importe_seleccionado'] < $data['CancelacionOrden']['saldo_orden_dto'])$data['CancelacionOrden']['tipo_cancelacion'] = 'P';
		
		//analizo la diferencia
		$data['CancelacionOrden']['importe_diferencia'] = $data['CancelacionOrden']['importe_proveedor'] - $data['CancelacionOrden']['importe_seleccionado'];

		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();

		App::import('model','Mutual.OrdenDescuentoCobroCuota');
		$oCobroCuota = new OrdenDescuentoCobroCuota();
		
		$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		$tmp = array();
                $nros_cuotas = array();
		foreach($data['CancelacionOrdenCuota']['cancelacion_orden_cuota_id'] as $cuota_id => $importe){
//			$oCuota->unbindModel(array('belongsTo' => array('Socio','Proveedor'),'hasMany' => array('OrdenDescuentoCobroCuota')));
                        $oCuota->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota')));
			$cuota = $oCuota->read(null,$cuota_id);
                        array_push($nros_cuotas, $cuota['OrdenDescuentoCuota']['nro_cuota']);
//			debug($cuota);
			$importeCancela = $data['CancelacionOrdenCuota']['cancelacion_orden_cuota_id1'][$cuota_id];
			
			#verifico si la cuota esta marcada como vencida para calcular la comision
			$isVencida = (!empty($data['CancelacionOrdenCuota']['cancelacion_orden_cuota_vencida'][$cuota_id]) ? true : false);
			
			$alicuotaComision = 0;
			$importeComision = 0;
			
			if($isVencida){
				$calculoComision = $oCobroCuota->calcularComisionCobranza($cuota_id,$importeCancela);
				$alicuotaComision = $calculoComision['alicuota'];
				$importeComision = $calculoComision['comision'];
			}
			
			$aux = array(
					'orden_descuento_cuota_id' => $cuota_id,
					'importe' => $importeCancela,
					'proveedor_id' => $cuota['OrdenDescuentoCuota']['proveedor_id'],
					'cuota_vencida' => $isVencida,
					'alicuota_comision_cobranza' => $alicuotaComision,
					'comision_cobranza' => $importeComision,
				);
			array_push($tmp,$aux);
		}
		$data['CancelacionOrdenCuota'] = $tmp;
                
		App::import('model','Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();                
                
                $orden = $oORDEN->getOrden($data['CancelacionOrden']['orden_descuento_id'], null, false, true);
                $data['CancelacionOrden']['concepto'] = $orden['OrdenDescuento']['tipo_nro']."-".$orden['OrdenDescuento']['producto_descripcion']." (CUOTAS: ".  implode(",", $nros_cuotas).")";
//                debug($nros_cuotas);
//                debug($orden);
//                debug($data);
//                $this->rollback();
//                exit;
		$data['CancelacionOrden']['estado'] = 'E';
		$STATUS = parent::saveAll($data,array('atomic' => false));
		
		
		if($STATUS) $this->commit();
		else $this->rollback();
		
		//si viene un numero de solicitud procesarla
		// MODIFICADO EL DIA 14/12/2011 POR GUSTAVO. NO DEBE GENERAR LA ORDEN DESCUENTO COBRO. ESTO SE DEBE RECAUDAR POR OTRA VIA.
		// ACA NO GENERA EL RECIBO DE INGRESO. TODO LO QUE SE RECAUDE DEBE SER INGRESADO POR UN RECIBO.
//		if(!empty($data['CancelacionOrden']['nro_solicitud'])):
//		
//		
//			$OrdenGenerada = $this->read(null,$this->getLastInsertID());
//			$fecha_cobro = parent::armaFecha($data['CancelacionOrden']['fecha_imputacion']);
//			#GENERAR EL COBRO
//			App::import('model','Mutual.OrdenDescuentoCobro');
//			$oCobro = new OrdenDescuentoCobro();			
//			$orden_descuento_cobro_id = $oCobro->generarPagoByCancelacion($OrdenGenerada,$fecha_cobro);
//			
//			$OrdenGenerada['CancelacionOrden']['estado'] = 'P';
//			$OrdenGenerada['CancelacionOrden']['orden_descuento_cobro_id'] = $orden_descuento_cobro_id;
//			
//			$STATUS = $this->save($OrdenGenerada);
//			
//		endif;
		
//		$this->rollback();
//		exit;		
		
		
		return $STATUS;
		
//		if(!parent::saveAll($data,array('atomic' => false))){
//			$this->rollback();
//			return false;
//		}else{
//			$this->commit();
//			return true;
//		}
		
	}
	
	
	function datosPago($data){
//		$this->id = $data['CancelacionOrden']['id'];
		if($data['CancelacionOrden']['forma_cancelacion'] == 'MUTUTICA0002'){
			if($data['CancelacionOrden']['forma_pago']== "MUTUFPAG0001"){
				$data['CancelacionOrden']['banco_id'] = '';
				$data['CancelacionOrden']['nro_cta_bco'] = '';
				$data['CancelacionOrden']['nro_operacion'] = '';
			}
		}else{
			$data['CancelacionOrden']['forma_pago'] = '';
			$data['CancelacionOrden']['pendiente_rendicion_proveedor'] = '0';
			$data['CancelacionOrden']['banco_id'] = '';
			$data['CancelacionOrden']['nro_cta_bco'] = '';
			$data['CancelacionOrden']['nro_operacion'] = '';
			
		}
		
		return parent::save($data);
	}
	
	
	function _procesar($orden_cancelacion_id,$orden_dto_id=0,$fecha_cobro=null,$TRANSACTION=false){
		
		if(empty($fecha_cobro)) $fecha_cobro = date('Y-m-d');
		
		$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		$orden = $this->get($orden_cancelacion_id);
		
		//PROCESO SI ESTA EN ESTADO EMITIDA
		if($orden['CancelacionOrden']['estado'] == 'E'){

			$NC = 0;
			#GENERAR NOTA DE CREDITO
			$NC = ($orden['CancelacionOrden']['importe_seleccionado'] - $orden['CancelacionOrden']['importe_proveedor']) * (-1);
	
			#ABRIR UNA TRANSACCION
			if($TRANSACTION)parent::begin();
			
			#GENERAR EL COBRO
			App::import('model','Mutual.OrdenDescuentoCobro');
			$oCobro = new OrdenDescuentoCobro();		
	
			$orden_descuento_cobro_id = $oCobro->generarPagoByCancelacion($orden,$fecha_cobro);
			
			if(!$orden_descuento_cobro_id){
				if($TRANSACTION) parent::rollback();
				return false;
			}
			
		
			if(!$this->__marcarProcesada($orden_cancelacion_id,$orden_descuento_cobro_id,$orden_dto_id)){
				if($TRANSACTION) parent::rollback();
				return false;			
			}
			
			if($TRANSACTION) parent::commit();
			
			
		}else{
			//actualizo la orden de descuento
			if($orden_dto_id != 0){
				App::import('model','Mutual.OrdenDescuento');
				$oODTO = new OrdenDescuento();	
				$proveedor_id = $oODTO->getProveedorID($orden_dto_id);				
				$this->id = $orden_cancelacion_id;
				if(!$this->saveField('nueva_orden_dto_id',$orden_dto_id)){
					if($TRANSACTION) parent::rollback();
					return false;
				}
				if(!$this->saveField('origen_proveedor_id',$proveedor_id)){
					if($TRANSACTION) parent::rollback();
					return false;
				}				
			}
		}
		

		return true;
	}
	
	/**
	 * marca procesada la cancelacion, le graba la orden de descuento cobro y en caso
	 * de ser una renovacion pone el id de la nueva orden de descuento generada
	 * @param unknown_type $id
	 * @param unknown_type $orden_descuento_cobro_id
	 * @param unknown_type $nueva_orden_dto_id
	 * @return string|string|string|string
	 */
	function __marcarProcesada($id,$orden_descuento_cobro_id=0,$nueva_orden_dto_id=0){
		$proveedor_id = 0;
		if($nueva_orden_dto_id != 0){
			App::import('model','Mutual.OrdenDescuento');
			$oODTO = new OrdenDescuento();	
			$proveedor_id = $oODTO->getProveedorID($nueva_orden_dto_id);
		}
		
		$this->id = $id;
		if(!$this->saveField('estado','P')) return false;
		if(!$this->saveField('orden_descuento_cobro_id',$orden_descuento_cobro_id)) return false;
		if(!$this->saveField('nueva_orden_dto_id',$nueva_orden_dto_id)) return false;
//		if(!$this->saveField('origen_proveedor_id',$proveedor_id)) return false;
		return true;
	}
	
	
	function recaudarByCaja($datos){
		
		$recaudado = true;
		$forma_pago = $datos['CancelacionOrden']['forma_pago'];
		$pendiente_rendicion_proveedor = $datos['CancelacionOrden']['pendiente_rendicion_proveedor'];
		$banco_id = $datos['CancelacionOrden']['banco_id'];
		$nro_cta_bco = $datos['CancelacionOrden']['nro_cta_bco'];
		$nro_operacion = $datos['CancelacionOrden']['nro_operacion'];	
		$nro_recibo = $datos['CancelacionOrden']['nro_recibo'];
//		$datos['CancelacionOrden']['fecha_imputacion'] = str_replace('/','-',$datos['CancelacionOrden']['fecha_imputacion']);
//		$fecha_imputacion = date('Y-m-d',strtotime($datos['CancelacionOrden']['fecha_imputacion']));	
		$fecha_imputacion = parent::armaFecha($datos['CancelacionOrden']['fecha_imputacion']);
//		$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));

		parent::begin();
		

		foreach($datos['CancelacionOrden']['id_check'] as $id => $valor){
			$orden = $this->get($id);

                        if($orden['CancelacionOrden']['total_cuotas_orden'] > $orden['CancelacionOrden']['saldo_cuotas_orden']){
                            parent::rollback();
                            return false;
                        }
                        
			if($orden['CancelacionOrden']['estado'] == 'E'){
				//cargo los datos del pago de la orden
				$orden['CancelacionOrden']['forma_cancelacion'] = 'MUTUTICA0002';
				$orden['CancelacionOrden']['forma_pago'] = $forma_pago;
				$orden['CancelacionOrden']['banco_id'] = $banco_id;
				$orden['CancelacionOrden']['nro_cta_bco'] = $nro_cta_bco;
				$orden['CancelacionOrden']['nro_operacion'] = $nro_operacion;
				$orden['CancelacionOrden']['nro_recibo'] = $nro_recibo;
				$orden['CancelacionOrden']['fecha_imputacion'] = $fecha_imputacion;
				$orden['CancelacionOrden']['origen_proveedor_id'] = (isset($datos['CancelacionOrden']['proveedor_origen_id']) ? $datos['CancelacionOrden']['proveedor_origen_id'] : $orden['CancelacionOrden']['orden_proveedor_id']);
//				$orden['CancelacionOrden']['observaciones'] = (empty($orden['CancelacionOrden']['observaciones']) ? $datos['CancelacionOrden']['observacion'] : $orden['CancelacionOrden']['observaciones']); 
				$orden['CancelacionOrden']['observaciones'] = $orden['CancelacionOrden']['observaciones'] . ' / ' . $datos['CancelacionOrden']['observacion']; 
//				$orden['CancelacionOrden']['estado'] = 'P';
				
				
				$this->id = $id;
				if(!parent::save($orden)){
					$recaudado = false;
					break;
				}
				if(!$this->_procesar($id,0,$fecha_imputacion,TRUE)){
					$recaudado = false;
					break;
				}
			}
			
		}
		
		if(!$recaudado){
			parent::rollback();
			return false;
		}else{
			parent::commit();
			return true;
		}
		
	}
	
	
	function getListProveedoresDestino(){
		$lista = array();
		$sql = "select CancelacionOrden.orden_proveedor_id,
				Proveedor.razon_social from cancelacion_ordenes as CancelacionOrden
				inner join proveedores as Proveedor on (Proveedor.id = CancelacionOrden.orden_proveedor_id)
				group by CancelacionOrden.orden_proveedor_id order by Proveedor.razon_social";
		$proveedores = $this->query($sql);
		if(empty($proveedores)) return null;
		foreach($proveedores as $proveedor){
			$lista[$proveedor['CancelacionOrden']['orden_proveedor_id']] = $proveedor['Proveedor']['razon_social'];
		}
		return $lista;
	}
	
	/**
	 * Anula Orden de Cancelacion
	 * Borra el cobro y el detalle del cobro.  Borra la NC o ND generada. Coloca el periodo
	 * original de la cuota y la marca como adeudada
	 * @param unknown_type $id
	 * @return unknown_type
	 */
	function anular($id){
		
		parent::begin();
		
		//ELIMINO EL COBRO
		App::import('model','Mutual.OrdenDescuentoCobro');
		$oCOBRO = new OrdenDescuentoCobro();		
		$conditions = array();
		$conditions['OrdenDescuentoCobro.cancelacion_orden_id'] = $id;
		$conditions['OrdenDescuentoCobro.anulado'] = 0;
		$cobros = $oCOBRO->find('all',array('conditions' => $conditions));
		if(!empty($cobros)):
			$id_cobro = (!empty($cobros) ? $cobros[0]['OrdenDescuentoCobro']['id'] : 0);
			$cobro =  $oCOBRO->getCobro($id_cobro);
			if(!$oCOBRO->borrarDetalle($id_cobro,false)):
				parent::rollback();
				return false;
			endif;
		endif;
		
		$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();		
		
		//elimino la nota de credito generada
		$cancelacion = $this->read(null,$id);
		
		if($cancelacion['CancelacionOrden']['cuota_diferencia_id'] != 0):
			$oCUOTA->borrarCuota($cancelacion['CancelacionOrden']['cuota_diferencia_id']);
		else:
			//no tiene grabado el id de la NC/ND en la cabecera lo busco en el detalle
			$cuota = Set::extract("/OrdenDescuentoCobroCuota/OrdenDescuentoCuota[tipo_cuota=".$cancelacion['CancelacionOrden']['tipo_cuota_diferencia']."]",$cobro);
			$cuota = Set::extract("/OrdenDescuentoCuota/id",$cuota);

			if(!empty($cuota)) $oCUOTA->borrarCuota($cuota[0]);
			
		endif;
		
		//REESTABLECER EL PERIODO ORIGINAL DE LA CUOTA
		if(!empty($cancelacion['CancelacionOrdenCuota'])):
		
			foreach($cancelacion['CancelacionOrdenCuota'] as $cuotaCancelada):
				$oCUOTA->unbindModel(array('belongsTo' => array('Socio','Proveedor','OrdenDescuento'),'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
				$cuotaOriginal = $oCUOTA->read(null,$cuotaCancelada['orden_descuento_cuota_id']);
				if(!empty($cuotaOriginal['OrdenDescuentoCuota']['periodo_origen']))$cuotaOriginal['OrdenDescuentoCuota']['periodo'] = $cuotaOriginal['OrdenDescuentoCuota']['periodo_origen'];
				$cuotaOriginal['OrdenDescuentoCuota']['periodo_origen'] = null;
				$cuotaOriginal['OrdenDescuentoCuota']['estado'] = 'A';
				$oCUOTA->save($cuotaOriginal);
			endforeach;
		
		endif;
		
		if(!$this->CancelacionOrdenCuota->deleteAll("CancelacionOrdenCuota.cancelacion_orden_id = $id")):
			parent::rollback();
			return false;		
		endif;
		
		if(!$this->del($id)):
			parent::rollback();
			return false;			
		endif;

//		parent::rollback();
//		exit;		
		
		parent::commit();
		return true;
		
	}
	
	
	function cobroByCaja($datos){

		$recaudado = true;
		$forma_pago = '';
		$pendiente_rendicion_proveedor = '';
		$banco_id = '';
		$nro_cta_bco = '';
		$nro_operacion = '';	
		$nro_recibo = '';
		$fecha_imputacion = parent::armaFecha($datos['CancelacionOrden']['fecha_comprobante']);
		$importe_proveedor = 0;
		
		$aTmpOPago = array();
		$aOrdenPago = array();

		// Tipo de Documento a utilizar ('Recibo')
		$oTipoDocumento = $this->importarModelo('TipoDocumento', 'config');
			
		// Busco el Numero de Recibo
		$nroRecibo = $oTipoDocumento->getNumero($datos['CancelacionOrden']['tipo_documento']);
		if($nroRecibo == 0):		
			return false;
		endif;
		$datos['CancelacionOrden']['numero_recibo'] = $nroRecibo;
		
		parent::begin();
		
		// Grabo el Recibo de Ingreso
		$nReciboId = $this->grabarReciboCancelacion($datos);
		if(!$nReciboId):
			parent::rollback();
			$oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
			return false;
		endif;

		$datos['CancelacionOrden']['recibo_id'] = $nReciboId;
		$cTipoCobro = 'MUTUTCOBCANC';  // CANCELACION COBRADA EN LA MUTUAL, NO ESTA ASOCIADA A UN CREDITO.
		if($datos['CancelacionOrden']['proveedor_origen_id'] != MUTUALPROVEEDORID):
			$cTipoCobro = 'MUTUTCOBCCOM';  // CANCELACION COBRADA EN COMERCIO O A TRAVES DE UNA RENOVACION.
		endif;
		
		foreach($datos['CancelacionOrden']['id_check'] as $id => $valor):
			$orden = $this->get($id);
			
			if($orden['CancelacionOrden']['estado'] == 'E'):
				//cargo los datos para la orden de cancelacion
				$orden['CancelacionOrden']['forma_cancelacion'] = 'MUTUTICA0002';
				$orden['CancelacionOrden']['nro_recibo'] = $nroRecibo;
				$orden['CancelacionOrden']['fecha_imputacion'] = $fecha_imputacion;
				$orden['CancelacionOrden']['recibo_id'] = $nReciboId;
				$orden['CancelacionOrden']['compensa_pago'] = (isset($datos['CancelacionOrden']['compensa_pago']) ? 1 : 0);
				// $orden['CancelacionOrden']['proveedor_origen_id'] = $datos['CancelacionOrden']['proveedor_origen_id'];
				$orden['CancelacionOrden']['origen_proveedor_id'] = $datos['CancelacionOrden']['proveedor_origen_id'];
				$orden['CancelacionOrden']['estado'] = 'P';
				// $orden['CancelacionOrden']['compensa_pago'] = 1; // $datos['CancelacionOrden']['compensa_pago'];

				$this->id = $id;
				if(!parent::save($orden)):
					$recaudado = false;
					break;
				endif;

				$orden_descuento_cobro_id = $this->_procesarCancelacion($id,$fecha_imputacion, $nReciboId, $cTipoCobro);
				if(!$orden_descuento_cobro_id){
					$recaudado = false;
					break;
				}
				
				$orden = $this->get($id);
				$orden['CancelacionOrden']['orden_descuento_cobro_id'] = $orden_descuento_cobro_id;
				$datos['CancelacionOrden']['orden_descuento_cobro_id'] = $orden_descuento_cobro_id;
				
				// grabo el Detalle del Recibo con los datos de la orden descuento cobros.
				if(!$this->detalleReciboCancelacion($orden)):
					$recaudado = false;
					break;
				endif;
				
				// grabo la factura de proveedor si corresponde, si es en el mismo comercio grabo la salida del dinero para compensar los cobros.
				if(!$this->FacturaProveedor($orden)):
					$recaudado = false;
					break;
				endif;
				
				if(isset($datos['CancelacionOrden']['compensa_pago']) && $datos['CancelacionOrden']['proveedor_origen_id'] != $orden['CancelacionOrden']['orden_proveedor_id']):
					$importe_proveedor += $orden['CancelacionOrden']['importe_proveedor'];
					$aTmpOPago = array(
						'id' => $id,
						'orden_pago_id' => 0
					);
					array_push($aOrdenPago, $aTmpOPago);
				endif;
				
				$this->id = $id;
				if(!$this->saveField('orden_descuento_cobro_id',$orden_descuento_cobro_id)) return false;
			endif;
			
		endforeach;
		
		if(!$recaudado):
			parent::rollback();
			$oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
			$oTipoDocumento->unLookRegistro('OPA');
			return false;
		else:
			if(isset($datos['CancelacionOrden']['compensa_pago']) && $importe_proveedor > 0):
				$orden_pago_id = $this->grabarOPagoAnticipo($datos, $importe_proveedor);
				if(!$orden_pago_id):
					parent::rollback();
					$oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
					$oTipoDocumento->unLookRegistro('OPA');
					return false;
				endif;
				$datos['CancelacionOrden']['orden_pago_id'] = $orden_pago_id;
				
				// Actualizar Cancelacion Ordenes
				foreach($aOrdenPago as $ordenPago):
					$ordenPago['orden_pago_id'] = $orden_pago_id;
					$this->id = $ordenPago['id'];
					if(!parent::save($ordenPago)):
						parent::rollback();
						$oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
						$oTipoDocumento->unLookRegistro('OPA');
						return false;
					endif;
				endforeach;		
			endif;

			parent::commit();
			$oTipoDocumento->putNumero($datos['CancelacionOrden']['tipo_documento']);
			$oTipoDocumento->putNumero('OPA');
			return $nReciboId;
		endif;
		
	}
	
	
	function grabarReciboCancelacion($datos){
		// Recibo
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		$nReciboId = $oRecibo->grabarReciboCancelacion($datos);
		
		return $nReciboId;
		
	}
	
	
	function _procesarCancelacion($orden_cancelacion_id,$fecha_cobro=null, $nReciboId, $cTipoCobro = 'MUTUTCOBCCOM'){
		
		if(empty($fecha_cobro)) $fecha_cobro = date('Y-m-d');
		
		$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		$orden = $this->get($orden_cancelacion_id);
		$orden['CancelacionOrden']['recibo_id'] = $nReciboId;

		#GENERAR EL COBRO
		App::import('model','Mutual.OrdenDescuentoCobro');
		$oCobro = new OrdenDescuentoCobro();		
	
		$orden_descuento_cobro_id = $oCobro->generarCobroByCancelacion($orden, $fecha_cobro, $cTipoCobro);
			
		if(!$orden_descuento_cobro_id):
			return false;
		endif;
			
		
		return $orden_descuento_cobro_id;
	}
	
			
	function FacturaProveedor(&$orden){
		
		// En caso que la Cancelacion sea para el Comercio y si la misma es del mismo comercio sea hace un pago de contado al comercio 
		// para compensar la entrada y salida de dinero.
		// En caso que la Cancelacion sea de distinto comercio, se hace una factura del Comercio para luego pagarla.//
		if($orden['CancelacionOrden']['orden_proveedor_id'] != MUTUALPROVEEDORID):
			if($orden['CancelacionOrden']['proveedor_origen_id'] == $orden['CancelacionOrden']['orden_proveedor_id']):
				$orden['CancelacionOrden']['banco_cuenta_movimiento_id'] = $this->__contado($orden);
				if(!$orden['CancelacionOrden']['banco_cuenta_movimiento_id']):
					return false;
				endif;
			else:
				$orden['CancelacionOrden']['proveedor_factura_id'] = $this->generarFacturaProveedor($orden);
				if(!$orden['CancelacionOrden']['proveedor_factura_id']):
					return false;
				endif;
			endif;
		endif;
			
		return true;
	}
	
	
	function generarFacturaProveedor($orden){

    	$oFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
		
    	$proveedor_factura_id = $oFactura->grabarFacturaCancelacion($orden['CancelacionOrden']);
    	if(!$proveedor_factura_id):
    		return false;
    	endif;
    	
    	return $proveedor_factura_id;
	}
	
	
	function grabarOPagoAnticipo($datos, $importe_proveedor){
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oOrdenPago = $this->importarModelo('OrdenPago', 'proveedores');
		
		$anticipos = array(
			'fecha_operacion' => $datos['CancelacionOrden']['fecha_comprobante'],
			'proveedor_id' => $datos['CancelacionOrden']['proveedor_origen_id'],
			'importe' => $importe_proveedor,
			'comentario' => 'CANCELACION ORDENES'
		);
		
		return $oOrdenPago->grabarPagoAnticipado($anticipos);
		
	}
	
	
	function __contado($orden){
	
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
			
		// Caja y Banco Cuentas. ('Banco Cuentas').
		$oBancoCuenta = $this->importarModelo('BancoCuenta', 'cajabanco');
		$cajaId = $oBancoCuenta->getCuentaCajaId();
		$cncCajaId = 0;
		
		// Armo los renglones de la Forma de cobro
    	$formaCobro = array();

    	$formaCobro['BancoCuentaMovimiento']['id'] = 0;
		$formaCobro['BancoCuentaMovimiento']['banco_cuenta_id'] = $cajaId;
		$formaCobro['BancoCuentaMovimiento']['fecha_operacion'] = $orden['CancelacionOrden']['fecha_imputacion'];
		$formaCobro['BancoCuentaMovimiento']['descripcion'] = $orden['CancelacionOrden']['recibo_detalle'];
		
		$formaCobro['BancoCuentaMovimiento']['banco_concepto_id'] = 0;
		$formaCobro['BancoCuentaMovimiento']['tipo'] = 7; 
		$formaCobro['BancoCuentaMovimiento']['concepto'] = 'CAJA';
		
		$formaCobro['BancoCuentaMovimiento']['importe'] = $orden['CancelacionOrden']['importe_proveedor'];
		$formaCobro['BancoCuentaMovimiento']['debe_haber'] = 1;
		$formaCobro['BancoCuentaMovimiento']['descripcion_cobro'] = 'EFECTIVO';
		$formaCobro['BancoCuentaMovimiento']['cancelacion_orden_id'] = $orden['CancelacionOrden']['id'];
    	
		// Grabar los movimiento de Caja y Banco
		if(!$oBancoMovimiento->save($formaCobro)):
			return false;
		endif;

		return $oBancoMovimiento->getLastInsertID();
		
	}

	
	function detalleReciboCancelacion($orden){
		$oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');
		
		if(!$oReciboDetalle->grabarReciboDetalleCancelacion($orden)):
			return false;
		endif;
		
		return true;
	}


	function getRecibo($id=null){
		if(empty($id)) return array();
		
		$oRecibo = $this->importarModelo('Recibo', 'clientes');
		
		return $oRecibo->getRecibo($id);
	}
	

	function anularRecibo($nReciboId){
		$this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');

		return $this->OrdenDescuentoCobro->anularCobro(0, $nReciboId);
	}
	
	
	function getCancelacionByNroSolicitud($nroSolicitud, $estado='E', $bindCuotas=false){
		if($bindCuotas)$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		$cancelaciones = $this->find('all',array('conditions' => array('CancelacionOrden.nro_solicitud' => $nroSolicitud, 'estado' => $estado)));
		return $this->armaDatos($cancelaciones,$bindCuotas);
	}
	

//	function detalleReciboCancelacion($datos){
//		$oReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');
//		
//		if(!$oReciboDetalle->grabarReciboDetalleCancelacion($datos)):
//			return false;
//		endif;
//		
//		return true;
//	}
	
	
	function getCancelacionByNroSolicitudEstado($nroSolicitud, $bindCuotas=false){
		if($bindCuotas)$this->bindModel(array('hasMany' => array('CancelacionOrdenCuota')));
		$cancelaciones = $this->find('all',array('conditions' => array('CancelacionOrden.nro_solicitud' => $nroSolicitud)));
		return $this->armaDatos($cancelaciones,$bindCuotas);
	}
	

	function cancelacionByCaja($datos){
            
            // debug($datos);
            // exit;

            $recaudado = true;
            $datos['CancelacionOrden']['forma_pago'] = '';
            $datos['CancelacionOrden']['pendiente_rendicion_proveedor'] = '';
            $datos['CancelacionOrden']['banco_id'] = '';
            $datos['CancelacionOrden']['nro_cta_bco'] = '';
            $datos['CancelacionOrden']['nro_operacion'] = '';	
            $datos['CancelacionOrden']['nro_recibo'] = '';
            $datos['CancelacionOrden']['fecha_imputacion'] = $datos['CancelacionOrden']['fecha_comprobante'];
            $datos['CancelacionOrden']['origen_proveedor_id'] = $datos['CancelacionOrden']['proveedor_origen_id'];
            $datos['CancelacionOrden']['observaciones'] = $datos['CancelacionOrden']['observacion'];

            $fechaCobro = parent::armaFecha($datos['CancelacionOrden']['fecha_comprobante']);
            $periodo_cobro = date('Ym',strtotime($fechaCobro));

//                debug($datos);
//                exit;
		
            if(!$this->recaudarByCaja($datos)):
                parent::notificar("NO SE RECAUDO LAS CANCELACIONES");
                return false;
            endif;

            $this->OrdenDescuentoCobro = $this->importarModelo('OrdenDescuentoCobro', 'mutual');
            $this->ProveedorFactura = $this->importarModelo('ProveedorFactura', 'proveedores');
            $this->ClienteFactura = $this->importarModelo('ClienteFactura', 'clientes');
            $this->ReciboDetalle = $this->importarModelo('ReciboDetalle', 'clientes');
            $this->ProveedorLiquidacion = $this->importarModelo('ProveedorLiquidacion', 'proveedores');
            $this->Recibo = $this->importarModelo('Recibo', 'clientes');


            // Cuando el importe de cobro es igual a cero es por que se retuvo la cancelacion, solo se procesa para pagar las cuotas de la cancelacion.
            if($datos['CancelacionOrden']['importe_cobro'] == 0 || $datos['CancelacionOrden']['retiene_comercio'] == 1):
                parent::begin();
                foreach($datos['CancelacionOrden']['id_check'] as $id => $valor):

                    $orden = $this->get($id,true);

                    $orden['CancelacionOrden']['estado'] = 'P';

                    $this->id = $id;
                    if(!parent::save($orden)):
                        parent::notificar("NO SE RECAUDO LAS CANCELACIONES");
                        $this->rollback();
                        return false;
                    endif;

                    // grabo la liquidacion a Proveedores
                    /*
                     * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
                     * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
                     * esta funcion queda obsoleta.
                    $orden['CancelacionOrden']['proveedor_liquidacion_id'] = $this->ProveedorLiquidacion->grabarLiquidacionByCancelacion($orden['CancelacionOrden']['orden_descuento_cobro_id']);
                    if(!$orden['CancelacionOrden']['proveedor_liquidacion_id']):
                        parent::notificar("NO SE GRABO LA LIQUIDACION A PROVEEDORES");
                        $this->rollback();
                        return false;
                    endif;
                     * 
                     */


                endforeach;
                parent::commit();
                return 'A';
            endif;
		
		
            # TIPO DE DOCUMENTO.
            $this->TipoDocumento = $this->importarModelo('TipoDocumento', 'config');
    	
            #########################
            # BUSCO EL NUMERO DE RECIBO Y BLOQUEO LA TABLA.
            $nNroRecibo = $this->TipoDocumento->getNumero($datos['CancelacionOrden']['tipo_documento']);
            if($nNroRecibo == 0):
                parent::notificar('LA TABLA DE RECIBO ESTA OCUPADO POR OTRO USUARIO');
                return false;
            endif;

            #########################
            # Busco el Numero de la Factura del cliente
            $nroFacturaCliente = $this->TipoDocumento->getNumero('FAC');
            if($nroFacturaCliente == 0):
                parent::notificar('LA TABLA DE FACTURA ESTA OCUPADO POR OTRO USUARIO');
                $this->TipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
                return false;
            endif;


            parent::begin();

            $fecha_imputacion = parent::armaFecha($datos['CancelacionOrden']['fecha_comprobante']);

//		$aTmpOPago = array();
//		$aOrdenPago = array();


            $nReciboId = 'A';
            $datos['CancelacionOrden']['recibo_id'] = 0;
            # SI COMPENSA PAGO NO GENERO EL RECIBO DE INGRESO POR QUE NO HAY ENTRADA DE DINERO.
            if(!isset($datos['CancelacionOrden']['compensa_pago'])):
                $datos['CancelacionOrden']['numero_recibo'] = str_pad($nNroRecibo, 8, 0, STR_PAD_LEFT);
                $aCmpRecibo = $this->TipoDocumento->getComprobante($datos['CancelacionOrden']['tipo_documento']);
                $datos['Recibo']['letra'] = $aCmpRecibo['letra'];
                // Grabo el Recibo de Ingreso
                $nReciboId = $this->Recibo->grabarReciboCancelacion($datos);
                if(!$nReciboId):
                    parent::notificar("NO SE GENERO EL RECIBO DE INGRESO");
                    parent::rollback();
                    $this->TipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
                    $this->TipoDocumento->unLookRegistro('FAC');
                    return false;
                endif;
                $datos['CancelacionOrden']['recibo_id'] = $nReciboId;
            endif;
		
            $aComprobante = $this->TipoDocumento->getComprobante('FAC');
            $nCantidadFactura = 0;

            $aCancelacionFacturas = array();
            $aTmpCancelacionFac = array();
		
            foreach($datos['CancelacionOrden']['id_check'] as $id => $valor):
                $orden = $this->get($id);

                // Actualizo la Orden Descuento Cobro con el id del Recibo
                $this->OrdenDescuentoCobro->id = $orden['CancelacionOrden']['orden_descuento_cobro_id'];
                if(!$this->OrdenDescuentoCobro->saveField('recibo_id',$datos['CancelacionOrden']['recibo_id'])):
                    parent::notificar('EN LA ORDEN DESCUENTO COBRO NO SE ACTUALIZO EL ID DEL RECIBO');
                    $recaudado = false;
                    break;
                endif;



                //cargo los datos para la orden de cancelacion
                $orden['CancelacionOrden']['recibo_id'] = $datos['CancelacionOrden']['recibo_id'];
                $orden['CancelacionOrden']['compensa_pago'] = (isset($datos['CancelacionOrden']['compensa_pago']) ? 1 : 0);
                $orden['CancelacionOrden']['origen_proveedor_id'] = $datos['CancelacionOrden']['proveedor_origen_id'];
                $orden['CancelacionOrden']['estado'] = 'P';


                # SI COMPENSA PAGO NO GENERO EL RECIBO DE INGRESO POR QUE NO HAY ENTRADA DE DINERO.
                if(!isset($datos['CancelacionOrden']['compensa_pago'])):
                    // grabo el Detalle del Recibo con los datos de la orden descuento cobros.
                    if (!$this->ReciboDetalle->grabarReciboDetalle($orden['CancelacionOrden']['orden_descuento_cobro_id'], $nReciboId)):
                        parent::notificar("EL DETALLE DEL RECIBO NO SE PUDO GENERAR");
                        $recaudado = false;
                        break;
                    endif;
                endif;


                // grabo la factura de proveedor si corresponde, si es en el mismo comercio no se graba.
                if($orden['CancelacionOrden']['orden_proveedor_id'] != MUTUALPROVEEDORID):

                    $nProvFactId = $this->ProveedorFactura->grabarFacturaByCancelacion($orden['CancelacionOrden']['orden_descuento_cobro_id']);
                    $orden['CancelacionOrden']['proveedor_factura_id'] = 0;
                    if(!$nProvFactId):
                        parent::notificar("LA FACTURA DE PROVEEDOR NO SE GENERO");
                        $recaudado = false;
                        break;
                    elseif($nProvFactId > 0):
                        $orden['CancelacionOrden']['proveedor_factura_id'] = $nProvFactId;
                    endif;
                    

                    // Sumo 1 a la factura de Clientes
                    $aComprobante['numero'] += 1;
                    // Cuento la cantidad de factura a grabar
                    $nCantidadFactura += 1;
                    // grabo la factura de cliente si corresponde cobrarle la comision
                    $nClieFactId = $this->ClienteFactura->grabarFacturaByCancelacion($orden['CancelacionOrden']['orden_descuento_cobro_id'], $aComprobante);
                    $orden['CancelacionOrden']['cliente_factura_id'] = 0;
                    if(!$nClieFactId):
                        parent::notificar("NO SE GENERO LA FACTURA DE CLIENTE");
                        $recaudado = false;
                        break;
                    elseif($nClieFactId > 0):
                        $orden['CancelacionOrden']['cliente_factura_id'] = $nClieFactId;
                    else:
                        $aComprobante['numero'] -= 1;
                        $nCantidadFactura -= 1;
                    endif;
                endif;


                if(isset($datos['CancelacionOrden']['compensa_pago'])):
                    // Preparo la orden de pago anticipado si corresponde.
                    $importe_proveedor = 0;
                    if($orden['CancelacionOrden']['origen_proveedor_id'] != $orden['CancelacionOrden']['orden_proveedor_id'] &&
                        $orden['CancelacionOrden']['origen_proveedor_id'] != MUTUALPROVEEDORID):
                        $importe_proveedor += $orden['CancelacionOrden']['importe_proveedor'];
                        $aTmpCancelacionFac = array('id' => $id, 'credito_proveedor_factura_id' => 0);
                        array_push($aCancelacionFacturas, $aTmpCancelacionFac);
                    endif;
                endif;

                $this->id = $id;
                if(!parent::save($orden)):
                    parent::notificar("LA CANCELACION NO SE PUDO ACTUALIZAR");
                    $recaudado = false;
                    break;
                endif;


                // grabo la liquidacion a Proveedores
                if($orden['CancelacionOrden']['orden_proveedor_id'] != MUTUALPROVEEDORID):
                    $orden['CancelacionOrden']['proveedor_liquidacion_id'] = $this->ProveedorLiquidacion->grabarLiquidacionByCancelacion($orden['CancelacionOrden']['orden_descuento_cobro_id']);
                    if(!$orden['CancelacionOrden']['proveedor_liquidacion_id']):
                        parent::notificar("NO SE GENERO LA LIQUIDACION A PROVEEDOR");
                        $recaudado = false;
                        break;
                    endif;
                endif;


            endforeach;

		
            if(!$recaudado):
                parent::notificar("DETALLE DE LA CANCELACION");
                parent::rollback();
                $this->TipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
                $this->TipoDocumento->unLookRegistro('FAC');
                return false;
            endif;



            // VERIFICO SI EXISTE REINTEGRO PARA SOCIO
            // EN ESE CASO GENERO UN REINTEGRO ANTICIPADO.
            if($datos['CancelacionOrden']['importe_cobro'] > $datos['CancelacionOrden']['importe_cancela']):

                $this->Liquidacion = $this->importarModelo('Liquidacion', 'mutual');
                $this->SocioReintegro = $this->importarModelo('SocioReintegro', 'pfyj');

                $liquidacion = $this->Liquidacion->read('periodo',$datos['CancelacionOrden']['liquidacion_id']);

                $importeReintegro = round($datos['CancelacionOrden']['importe_cobro'] - $datos['CancelacionOrden']['importe_cancela'],2);
                $aReintegro = array('SocioReintegro' => array
                (
                    'id' => 0,
                    'liquidacion_id' => $datos['CancelacionOrden']['liquidacion_id'],
                    'importe_reintegro' => $importeReintegro,
                    'socio_id' => $datos['CancelacionOrden']['cabecera_socio_id'],
                    'anticipado' => 1,
                    'periodo' => $liquidacion['Liquidacion']['periodo'],
                    'recibo_id' => $nReciboId
                ));



                // GRAVO EL REINTEGRO ANTICIPADO AL SOCIO.
                if(!$this->SocioReintegro->save($aReintegro)):
                    parent::notificar('EL REINTEGRO NO SE GENERO CORRECTAMENTE');
                    parent::rollback();
                    $this->TipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
                    $this->TipoDocumento->unLookRegistro('FAC');
                    return false;
                endif;

                // GENERO EL DETALLE EN EL RECIBO POR EL REINTEGRO.
                $aReciboDetalle = array('ReciboDetalle' => array(
                    'id' => 0,
                    'socio_id' => $datos['cabecera_socio_id'],
                    'recibo_id' => $nReciboId,
                    'tipo_cobro' => 'FA',
                    'socio_reintegro_id' => $this->SocioReintegro->id,
                    'concepto' => 'REINTEGRO A SOCIO',
                    'importe' => $importeReintegro
                ));

                // GRAVO EL REINTEGRO ANTICIPADO EN EL RECIBO DETALLE.
                if(!$this->ReciboDetalle->save($aReciboDetalle)):
                    parent::notificar('EL REINTEGRO NO SE GENERO CORRECTAMENTE EN EL DETALLE DEL RECIBO');
                    parent::rollback();
                    $this->TipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
                    $this->TipoDocumento->unLookRegistro('FAC');
                    return false;
                endif;


            endif;
		
		
		
// Orden de pago anticipado si se recaudo en comercio y no entregro el dinero.
//		$nroOrdenPago = $oTipoDocumento->getNumero('OPA');
//		if($nroOrdenPago == 0):		
//			parent::notificar("EL NUMERO DE ORDEN DE PAGO ESTA BLOQUEADO POR OTRO USARIO");
//			return false;
//		endif;
//		if(isset($datos['CancelacionOrden']['compensa_pago']) && $importe_proveedor > 0):
//			$orden_pago_id = $this->grabarOPagoAnticipo($datos, $importe_proveedor, $nroOrdenPago);
//			if(!$orden_pago_id):
//				parent::notificar("NO SE GENERO LA ORDEN DE PAGO ANTICIPADO");
//				parent::rollback();
//				$oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
//				$oTipoDocumento->unLookRegistro('FAC');
//				$oTipoDocumento->unLookRegistro('OPA');
//				return false;
//			endif;
////			$datos['CancelacionOrden']['orden_pago_id'] = $orden_pago_id;
//				
//			// Actualizar Cancelacion Ordenes
//			foreach($aOrdenPago as $ordenPago):
//				$ordenPago['orden_pago_id'] = $orden_pago_id;
//				$this->id = $ordenPago['id'];
//				if(!parent::save($ordenPago)):
//					parent::notificar("NO SE ACTUALIZO LA CANCELACION POR LA ORDEN DE PAGO ANTICIPADA");
//					parent::rollback();
//					$oTipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
//					$oTipoDocumento->unLookRegistro('FAC');
//					$oTipoDocumento->unLookRegistro('OPA');
//					return false;
//				endif;
//			endforeach;		
//		endif;

		
            # GENERO UNA NOTA DE CREDITO SI LA RECAUDACION FUE EN UN COMERCIO Y ESTE NO ENTREGO EL EFECTIVO.
            $importe_credito = 0;
            $nCreditoId = 0;
            if($datos['CancelacionOrden']['proveedor_origen_id'] != MUTUALPROVEEDORID && isset(
                $datos['CancelacionOrden']['compensa_pago'])):
                $datos['CancelacionOrden']['periodo_cobro'] = $periodo_cobro;
                $aFacturaProveedor = $this->ProveedorFactura->prepararCreditoProvCancela($datos);
                if(!$this->ProveedorFactura->save($aFacturaProveedor)):
                    $flag = false;
                    parent::notificar('LA NOTA DE CREDITO NO PUDO SER GENERADA');
                    parent::rollback(); 
                    $this->TipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
                    $this->TipoDocumento->unLookRegistro('FAC');
                    return false;
                endif;

                $datos['CancelacionOrden']['credito_proveedor_factura_id'] = $this->ProveedorFactura->getLastInsertID();
//			$importe_credito = $datos['CancelacionOrden']['importe_cobrado'];

                /*
                 * A pedido de M22S no tiene que grabar la comision de Comercio, la tabla no permite grabar el campo cliente_id en 0 o NULL.
                 * Es una tabla que no tiene importancia, era solo para control. No es necesario grabar los datos en esta tabla.
                 * esta funcion queda obsoleta.
                if(!$this->ProveedorLiquidacion->grabarLiquidacionCreditoCancela($datos)):
                        parent::notificar('LA LIQUIDACION DE CREDITO A COMERCIO NO PUDO SER ACTUALIZADA');
                        parent::rollback(); 
                        $this->TipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
                        $this->TipoDocumento->unLookRegistro('FAC');
                        return false;
                endif;
                 */

                foreach ($aCancelacionFacturas as $aCancelFactura):
                    $aCancelFactura['credito_proveedor_factura_id'] = $this->ProveedorFactura->id;
                    $aCancelFactura['observaciones'] = 'ADEUDA POR ' . $aFacturaProveedor['razon_social'];
                    $this->id = $aCancelFactura['id'];
                    if(!parent::save($aCancelFactura)):
                        parent::notificar("NO SE ACTUALIZO LA CANCELACION POR CREDITO");
                        parent::rollback();
                        $this->TipoDocumento->unLookRegistro($datos['CancelacionOrden']['tipo_documento']);
                        $this->TipoDocumento->unLookRegistro('FAC');
                        return false;
                    endif;

                endforeach;
            endif;

		
            parent::commit();

            // Actualizo los numeros de documentos
            $this->TipoDocumento->putNumero($datos['CancelacionOrden']['tipo_documento']);
            $this->TipoDocumento->putNumero('FAC', $nCantidadFactura);


            return $nReciboId;
		
	}
	
	function generarOrdenDeTerceros($datos){
	
		$datos['CancelacionOrden']['id'] = 0;
		$datos['CancelacionOrden']['orden_proveedor_id'] = $datos['CancelacionOrden']['proveedor_id'];
		$datos['CancelacionOrden']['fecha_vto'] = parent::armaFecha($datos['CancelacionOrden']['fecha_vto']);
		$datos['CancelacionOrden']['tipo_cancelacion'] = 'T';
		$datos['CancelacionOrden']['importe_seleccionado'] = $datos['CancelacionOrden']['importe_proveedor'];
		$datos['CancelacionOrden']['saldo_orden_dto'] = $datos['CancelacionOrden']['importe_proveedor'];
		$datos['CancelacionOrden']['tipo_cuota_diferencia'] = NULL;
	
		if(!parent::save($datos)){
			$this->rollback();
			return false;
		}else{
			$this->commit();
			return true;
		}
	
	}
	
	function getPendientes($socio_id){
		$sql = "SELECT * FROM cancelacion_ordenes AS CancelacionOrden
		WHERE socio_id = $socio_id
		AND fecha_vto >= CURDATE()
		AND id NOT IN (SELECT cancelacion_orden_id FROM mutual_producto_solicitud_cancelaciones);";
		$cancelaciones = $this->query($sql);
		if(!empty($cancelaciones)){
		$cancelaciones = $this->armaDatos($cancelaciones);
		}
		return $cancelaciones;
	}	
	
        function is_deleteable($id){
            $sql = "SELECT CancelacionOrden.id FROM cancelacion_ordenes AS CancelacionOrden
                    WHERE id = $id AND estado = 'E'
                    AND id NOT IN (SELECT cancelacion_orden_id FROM mutual_producto_solicitud_cancelaciones);";
            $cancelaciones = $this->query($sql);
            return (!empty($cancelaciones) ? true : false);
        }        
        
        
        public function get_by_cuota($cuota_id){
            $sql = "select CancelacionOrden.* from cancelacion_ordenes CancelacionOrden
                    inner join cancelacion_orden_cuotas coc on coc.cancelacion_orden_id = CancelacionOrden.id
                    where coc.orden_descuento_cuota_id =  $cuota_id";
            $cancelaciones = $this->query($sql);
            if(!empty($cancelaciones)){
            $cancelaciones = $this->armaDatos($cancelaciones);
            }
            return $cancelaciones;            
        }
        
        
        public function getCancelacionBySolicitudMin($solicitudId) {
            $sql = "select co.id, p.razon_social, co.concepto, co.importe_proveedor, o.mutual_producto_solicitud_id, 
                    (select GROUP_CONCAT(odc.nro_cuota) from cancelacion_orden_cuotas coc 
                    inner join orden_descuento_cuotas odc on odc.id = coc.orden_descuento_cuota_id
                    where coc.cancelacion_orden_id = co.id) cuotas
                    from cancelacion_ordenes co
                    inner join mutual_producto_solicitud_cancelaciones mpsc on mpsc.cancelacion_orden_id = co.id
                    inner join proveedores p on p.id = co.orden_proveedor_id
                    inner join orden_descuentos o on o.id = co.orden_descuento_id
                    where mpsc.mutual_producto_solicitud_id = $solicitudId";
            $cancelaciones = $this->query($sql);
            return $cancelaciones;
        }
        
}
?>
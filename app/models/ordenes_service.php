<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::import('Vendor','sigem_service');
App::import('Model', 'mutual.OrdenDescuento');
App::import('Model', 'mutual.OrdenDescuentoCuota');
App::import('Model', 'mutual.OrdenDescuentoCobro');
App::import('Model', 'mutual.OrdenDescuentoCobroCuota');
App::import('Model', 'mutual.MutualProductoSolicitud');
App::import('Model', 'mutual.CancelacionOrden');
App::import('Model', 'pfyj.Persona');
App::import('Model', 'pfyj.PersonaNovedad');

class OrdenesService extends SIGEMService{
 
	var $name = 'OrdenesService';
	var $useTable = false; 
        var $ROWS = 50;
                
	/**
	 * Obtiene un token
	 * @param string $PIN
         * @return string
	 */        
        function getToken($PIN) {
            $this->validatePIN($PIN, false);
            return $this->getResponse();            
        }
    
    /**
     * Cargar Ordenes Aprobadas emitidas entre fechas
     * @param string $PIN
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @param int $offset
     * @return string
     */
    function getOrdenesNoAprobadasEmitidasEntreFechas($PIN,$fechaDesde,$fechaHasta,$offset = 0){
        if (!$this->validatePIN($PIN)) {
            return $this->getResponse();
        }
        $pendientes = $this->cargarOrdenes($PIN,0,$fechaDesde,$fechaHasta);
        $this->setResponse('result',$pendientes);
        $this->setResponse('find',count($pendientes));
        return $this->getResponse();
    }    
    
    /**
     * Cargar Ordenes Aprobadas entre fechas de pago/aprobacion
     * @param string $PIN
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @param int $offset
     * @return string
     */
    function getOrdenesAprobadasEntreFechas($PIN,$fechaDesde,$fechaHasta,$offset = 0){
        if (!$this->validatePIN($PIN)) {
            return $this->getResponse();
        }
        $pendientes = $this->cargarOrdenes($PIN,1,$fechaDesde,$fechaHasta,TRUE,false, $offset);
        $this->setResponse('result',$pendientes);
        $this->setResponse('find',count($pendientes));
        return $this->getResponse();        
    }
    
    /**
     * Cargar Ordenes Aprobadas entre fechas de pago/aprobacion
     * @param string $PIN
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @param int $offset
     * @return string
     */
    function getOrdenesAprobadasEntreFechasConSaldoPendiente($PIN,$fechaDesde,$fechaHasta,$offset = 0){
        if (!$this->validatePIN($PIN)) {
            return $this->getResponse();
        }
        $pendientes = $this->cargarOrdenesAdeudadas($PIN,$fechaDesde,$fechaHasta,$offset);
        $this->setResponse('result',$pendientes);
        $this->setResponse('find',count($pendientes));
        return $this->getResponse();
    }
    

    /**
     * Cargar Ordenes Aprobadas entre fechas
     * @param string $PIN
     * @param string $fechaDesde
     * @param string $fechaHasta
     * @param int $offset
     * @return string
     */
    function getOrdenesAprobadasEmitidasEntreFechas($PIN,$fechaDesde,$fechaHasta,$offset = 0){
        if (!$this->validatePIN($PIN)) {
            return $this->getResponse();
        }
        $pendientes = $this->cargarOrdenes($PIN,1,$fechaDesde,$fechaHasta,false,false,$offset);
        $this->setResponse('result',$pendientes);
        $this->setResponse('find',count($pendientes));
        return $this->getResponse();        
    }

    /**
     * Traer una solicitud para un proveedor
     * @param string $PIN
     * @param string $nroSolicitud
     * @return string
     */	
    function getSolicitud($PIN, $nroSolicitud){
        if (!$this->validatePIN($PIN)) {
            return $this->getResponse();
        }
        $oSOLICITUD = new MutualProductoSolicitud();
        $solicitud = $oSOLICITUD->getOrden($nroSolicitud);
        $solicitud = $oSOLICITUD->armaDatos($solicitud,false,true);
   
        if($solicitud['MutualProductoSolicitud']['proveedor_cuit'] != $this->proveedor['cuit']){
            if($solicitud['MutualProductoSolicitud']['reasignar_proveedor_id'] != $this->proveedor['id']){
                $this->setResponse('error',1);
                $this->setResponse('msg_error',"EL NUMERO DE SOLICITUD NO PERTENECE AL PIN");
                return $this->getResponse();
            }
        }
        if(empty($solicitud)){
                $this->setResponse('error',1);
                $this->setResponse('msg_error',"EL NUMERO DE SOLICITUD NO EXISTE");
                return $this->getResponse();			
        }
        $solicitud = $this->armaDatoSolicitud($solicitud);
        $this->setResponse('find',1);
        $this->setResponse('result',$solicitud);
        return $this->getResponse();		
    }    
    
    
    
    /**
     * 
     * @param string $documento
     * @param string $PIN
     * @return string
     */
    function estadoCuenta($documento,$PIN){
        $this->validateToken();
        if (!$this->validatePIN($PIN)) {
            return $this->getResponse();
        }
        $cuotas = array();
        $oCUOTA = new OrdenDescuentoCuota();
        $sql = "select 
                tdoc.concepto_1, 
                p.documento,
                concat(p.apellido,', ',p.nombre) as apenom,
                p.calle,
                p.numero_calle,
                p.piso,
                p.dpto,
                p.barrio,
                p.localidad,
                p.codigo_postal,
                p.telefono_fijo,
                p.telefono_movil,
                p.telefono_referencia,
                p.e_mail,
                p.cuit_cuil,
                o.id,
                o.tipo_orden_dto,
                o.numero,
                cu.id,
                cu.nro_referencia_proveedor,
                prod.concepto_1,
                tcuo.concepto_1,
                cu.periodo,
                concat(lpad(cu.nro_cuota,2,0),'/',lpad(o.cuotas,2,0)) as cuota,
                cu.importe,
                ifnull((select sum(importe) from orden_descuento_cobro_cuotas co
                where co.orden_descuento_cuota_id = cu.id),0) as pagos,
                (cu.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas co
                where co.orden_descuento_cuota_id = cu.id),0)) as saldo_conciliado,
                ifnull((select sum(importe_debitado) from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = cu.id and lc.para_imputar = 1 and lc.imputada = 0),0) as pendiente_acreditar,
                (cu.importe - ifnull((select sum(importe) from orden_descuento_cobro_cuotas co
                where co.orden_descuento_cuota_id = cu.id),0) - ifnull((select sum(importe_debitado) 
                from liquidacion_cuotas lc
                where lc.orden_descuento_cuota_id = cu.id and lc.para_imputar = 1 and lc.imputada = 0),0)) as saldo_aconciliar,
                ba.nombre,
                b.cbu,
                b.nro_sucursal,
                b.nro_cta_bco,
                emp.concepto_1,
                ifnull(b.acuerdo_debito,0) as acuerdo_debito                 
                from orden_descuento_cuotas cu
                inner join orden_descuentos o on (o.id = cu.orden_descuento_id)
                inner join socios s on (s.id = cu.socio_id)
                inner join personas p on (p.id = s.persona_id)
                inner join global_datos tdoc on (tdoc.id = p.tipo_documento)
                inner join persona_beneficios b on (b.id = cu.persona_beneficio_id)
                inner join global_datos prod on (prod.id = cu.tipo_producto)
                inner join global_datos tcuo on (tcuo.id = cu.tipo_cuota)
                inner join proveedores prv on (prv.id = cu.proveedor_id)
                left join bancos ba on (ba.id = b.banco_id)
                left join global_datos emp on (emp.id = b.codigo_empresa)                
                where p.documento = lpad(cast('". mysql_real_escape_string($documento)."' as unsigned),8,0)
                and prv.codigo_acceso_ws = '" . mysql_real_escape_string($PIN) . "'
                and cu.estado <> 'B'
                order by periodo;";
        $datos = $oCUOTA->query($sql);
        
        if(!empty($datos)){
            $estadoCuenta = array();
            $dato = $datos[0];
            $estadoCuenta['datos_personales'] = array(
                'tdoc' => $dato['tdoc']['concepto_1'],
                'ndoc' => $dato['p']['documento'],
                'apenom' => $dato[0]['apenom'],
                'calle' => $dato['p']['calle'],
                'numero' => $dato['p']['numero_calle'],
                'piso' => $dato['p']['piso'],
                'dpto' => $dato['p']['dpto'],
                'barrio' => $dato['p']['barrio'],
                'localidad' => $dato['p']['localidad'],
                'codigo_postal' => $dato['p']['codigo_postal'],
                'telefono_fijo' => $dato['p']['telefono_fijo'],
                'telefono_movil' => $dato['p']['telefono_movil'],
                'telefono_referencia' => $dato['p']['telefono_referencia'],
                'email' => $dato['p']['e_mail'],
                'cuil' => $dato['p']['cuit_cuil'],
            );
            $estadoCuenta['estado_cuotas'] = array();
            
            $saldoConciliadoAcumulado = $saldoAcumulado = 0;
            
            foreach($datos as $n => $dato){
                
                $cuotaId = $dato['cu']['id'];
                $pagosSql = "SELECT 
                                co.id,
                                co.fecha,
                                RIGHT(co.tipo_cobro,4) tipo_cobro,
                                gd.concepto_1 concepto,
                                co.periodo_cobro,
                                cdc.importe
                             FROM orden_descuento_cobro_cuotas cdc
                             INNER JOIN orden_descuento_cobros co ON co.id = cdc.orden_descuento_cobro_id
                             LEFT JOIN global_datos gd on gd.id = co.tipo_cobro
                             WHERE cdc.orden_descuento_cuota_id = $cuotaId order by co.fecha ASC;";

                $detallePagos = $oCUOTA->query($pagosSql);   

                $detallePagos2 = array_map(function ($pago) use ($oCUOTA){
                    return [
                        'cobro' =>  $pago['co']['id'],
                        'fecha_imputacion' => $pago['co']['fecha'],
                        'tipo_cobro' => $pago[0]['tipo_cobro'],
                        'concepto' => $pago['gd']['concepto'],
                        'periodo_cobro' => $oCUOTA->periodo($pago['co']['periodo_cobro']),
                        'importe_cobrado' => $pago['cdc']['importe'],
                    ];
                }, $detallePagos);
                
                $cuotas[$n]['nro_referencia_proveedor'] = $dato['cu']['nro_referencia_proveedor'];
                $cuotas[$n]['orden_descuento'] = $dato['o']['id'];
                $cuotas[$n]['tipo'] = $dato['o']['tipo_orden_dto'];
                $cuotas[$n]['solicitud'] = $dato['o']['numero'];
                $cuotas[$n]['producto'] = $dato['prod']['concepto_1'];
                $cuotas[$n]['concepto'] = $dato['tcuo']['concepto_1'];
                $cuotas[$n]['periodo'] = $oCUOTA->periodo($dato['cu']['periodo']);
                $cuotas[$n]['cuota'] = $dato[0]['cuota'];
                $cuotas[$n]['importe'] = $dato['cu']['importe'];
                $cuotas[$n]['pagos'] = $dato[0]['pagos'];
                $cuotas[$n]['saldo_conciliado'] = $dato[0]['saldo_conciliado'];
                $cuotas[$n]['pendiente_acreditar'] = $dato[0]['pendiente_acreditar'];
                $cuotas[$n]['saldo_aconciliar'] = $dato[0]['saldo_aconciliar'];
                
                $saldoConciliadoAcumulado += $dato[0]['saldo_conciliado'];
                $saldoAcumulado += $dato[0]['saldo_aconciliar'];
                $cuotas[$n]['saldo_conciliado_acumulado'] = $saldoConciliadoAcumulado;
                $cuotas[$n]['saldo_aconciliar_acumulado'] = $saldoAcumulado;                
                
                $cuotas[$n]['banco'] = $dato['ba']['nombre'];
                $cuotas[$n]['cbu'] = $dato['b']['cbu'];
                $cuotas[$n]['nro_cta'] = $dato['b']['nro_cta_bco'];
                $cuotas[$n]['sucursal'] = $dato['b']['nro_sucursal'];
                $cuotas[$n]['empresa'] = $dato['emp']['concepto_1'];
                $cuotas[$n]['acuerdo_debito'] = $dato[0]['acuerdo_debito'];
                $cuotas[$n]['detalle_pagos'] = $detallePagos2;
                
                
            }
        }
        
        $estadoCuenta['estado_cuotas'] = $cuotas;
        $this->setResponse('find',count($cuotas));
        $this->setResponse('result',$estadoCuenta);
        return $this->getResponse();        
    }    
    

    /**
     * Novedades por persona
     * @param string $documento
     * @param string $PIN
     * @return string
     */
    public function getNovedadesPersona($documento, $PIN){
        $this->validateToken();
        if (!$this->validatePIN($PIN)) {
            return $this->getResponse();
        }        
        $oPERSONA = new Persona();
        $oPNOV = new PersonaNovedad();
        
        $personas = $oPERSONA->getByNdoc($documento,FALSE);
        
        if(empty($personas)){
            
            $this->oRESPONSE->error = 1;
            $this->oRESPONSE->msg_error = "El documento $documento no fue encontrado.";
        }
        
        $persona = $personas[0];

        $novedades = $oPNOV->find('all',array('conditions' => array('PersonaNovedad.persona_id' => $persona['Persona']['id']),'order' => array('created DESC')));
        
        $personaSTD = new stdClass();
        $personaSTD->documento = $persona['Persona']['documento'];
        $personaSTD->nombre = $persona['Persona']['nombre'];
        $personaSTD->apellido = $persona['Persona']['apellido'];
        $personaSTD->novedades = array();
        
        $personaSET = array(
            'documento' => $persona['Persona']['documento'],
            'nombre' => $persona['Persona']['nombre']. ' ' . $persona['Persona']['apellido'],
            'novedades' => array(),
        );
        
        if(!empty($novedades)){
        
            $this->oRESPONSE->find = count($novedades);
            
            $this->setResponse('find',count($novedades));
            
            foreach($novedades as $value){
                
                $novedad = new stdClass();
                $novedad->numero = $value['PersonaNovedad']['id'];
                $novedad->fecha = $value['PersonaNovedad']['created'];
                $novedad->usuario = $value['PersonaNovedad']['user_created'];
                $novedad->descripcion = $value['PersonaNovedad']['descripcion'];
                
                array_push($personaSTD->novedades, $novedad);
                
                $novedadSET = array(
                    'numero' => $value['PersonaNovedad']['id'],
                    'fecha' => $value['PersonaNovedad']['created'],
                    'usuario' => $value['PersonaNovedad']['user_created'],
                    'descripcion' => $value['PersonaNovedad']['descripcion'],
                );
                
                array_push($personaSET['novedades'], $novedadSET);
                
            }
            $this->oRESPONSE->result = $personaSTD;
            $this->setResponse('result',$personaSET);
            
        }
        
//        return $this->getResponse();
        return $this->getResponseObjectJSON();
        
    }




    /**
     * 
     * @param type $pin
     * @param type $aprobada
     * @return array
     */
    private function cargarOrdenes($pin,$aprobada = 0,$fechaDesde = NULL,$fechaHasta = NULL,$byFechaPago = FALSE, $soloDeuda = FALSE, $OFFSET = 0){
        $ordenes = array();
        
        $fechaDesde = (empty($fechaDesde) ? '2000-01-01' : $fechaDesde);
        $fechaHasta = (empty($fechaHasta) ? date('Y-m-d') : $fechaHasta);
        
        App::import('model','Mutual.MutualProductoSolicitudService');
        $oSOLSERV = new MutualProductoSolicitudService();
        /*
        $conditions = " WHERE MutualProductoSolicitud.aprobada = $aprobada AND MutualProductoSolicitud.estado = ".($aprobada == 0 ? "'MUTUESTA0002'" : "'MUTUESTA0014'")."
                        AND (Proveedor.codigo_acceso_ws = '$pin' OR ProveedorReasigna.codigo_acceso_ws = '$pin')
                        AND MutualProductoSolicitud.".(!$byFechaPago ? "fecha" : "fecha_pago")." BETWEEN '$fechaDesde' AND '$fechaHasta'
                        ORDER BY MutualProductoSolicitud.fecha LIMIT $OFFSET, ".$this->ROWS.";";
        */
        $conditions = " WHERE MutualProductoSolicitud.aprobada = $aprobada AND MutualProductoSolicitud.anulada = 0
                        AND (Proveedor.codigo_acceso_ws = '$pin' OR ProveedorReasigna.codigo_acceso_ws = '$pin')
                        AND MutualProductoSolicitud.".(!$byFechaPago ? "fecha" : "fecha_pago")." BETWEEN '$fechaDesde' AND '$fechaHasta'
                        ORDER BY MutualProductoSolicitud.fecha LIMIT $OFFSET, ".$this->ROWS.";";        
        
        $solicitudes = $oSOLSERV->getSolicitudes($conditions);
        
        if(!empty($solicitudes)) {
            foreach($solicitudes as $row){
                $solicitud = $this->armaDatoSolicitud($row);
                array_push($ordenes, $solicitud);
            }            
        }
        $LOG = date('Y-m-d H:i:s')."|".$pin."|".$query;
        parent::writeLog($LOG);
        return $ordenes;
    }
    
    
    private function cargarOrdenesAdeudadas($pin,$fechaDesde = NULL,$fechaHasta = NULL, $OFFSET = 0) {
        $ordenes = array();
        
        $sql = "select s.id, FX_ORDENDTO_SALDO_VENCIDO_POR_RANGO(o.id,
                date_format(now(), '%Y%m' ),1,9999) as saldo from orden_descuentos o 
                inner join mutual_producto_solicitudes s on s.id = o.mutual_producto_solicitud_id
                inner join proveedores on (proveedores.id = o.proveedor_id and proveedores.codigo_acceso_ws = '$pin')
                where o.fecha between '$fechaDesde' and '$fechaHasta'
                having saldo > 0 LIMIT $OFFSET, ".$this->ROWS."";
        
        $oORDEN = new OrdenDescuento();
        $oSOLICITUD = new MutualProductoSolicitud();
        $datos = $oORDEN->query($sql);
        
        if(!empty($datos)){
            foreach($datos as $row){
                $solicitud = $oSOLICITUD->getOrden($row['s']['id']);
                $solicitud = $oSOLICITUD->armaDatos($solicitud,false,true);
                $solicitud = $this->armaDatoSolicitud($solicitud, $row['s']['saldo']);
                array_push($ordenes, $solicitud);
            }
        }
        $LOG = date('Y-m-d H:i:s')."|".$pin."|".$sql;
        parent::writeLog($LOG);
        return $ordenes;
        
    }
    
    /**
     * 
     * @param array $solicitud
     * @return array
     */
    private function armaDatoSolicitud($solicitud, $saldo = 0){

        $datos = array();
        $datos['solicitud_numero'] = $solicitud['MutualProductoSolicitud']['id'];
        $datos['solicitud_fecha'] = $solicitud['MutualProductoSolicitud']['fecha'];
        $datos['solicitud_fecha_pago'] = $solicitud['MutualProductoSolicitud']['fecha_pago'];
        $datos['solicitud_codigo_estado'] = substr($solicitud['MutualProductoSolicitud']['estado'],-4);
        $datos['solicitud_codigo_estado_descripcion'] = utf8_encode($solicitud['MutualProductoSolicitud']['estado_desc']);
        $datos['solicitud_codigo_producto'] = substr($solicitud['MutualProductoSolicitud']['tipo_producto'],-4);
        $datos['solicitud_codigo_producto_descripcion'] = utf8_encode($solicitud['MutualProductoSolicitud']['producto']);
        $datos['solicitud_importe_total'] = $solicitud['MutualProductoSolicitud']['importe_total'];
        $datos['solicitud_cuotas'] = $solicitud['MutualProductoSolicitud']['cuotas'];
        $datos['solicitud_importe_cuota'] = $solicitud['MutualProductoSolicitud']['importe_cuota'];
        $datos['solicitud_importe_solicitado'] = $solicitud['MutualProductoSolicitud']['importe_solicitado'];
        $datos['solicitud_importe_percibido'] = $solicitud['MutualProductoSolicitud']['importe_percibido'];
        $datos['solicitud_inicia_en'] = $solicitud['MutualProductoSolicitud']['inicia_en'];
        $datos['solicitud_primer_vto'] = $solicitud['MutualProductoSolicitud']['primer_vto_socio'];
        $datos['solicitud_primer_vto'] = $solicitud['MutualProductoSolicitud']['primer_vto_socio'];
        $datos['solicitud_codigo_forma_pago'] = $solicitud['MutualProductoSolicitud']['forma_pago'];
        $datos['solicitud_codigo_forma_pago_descripcion'] = utf8_encode($solicitud['MutualProductoSolicitud']['forma_pago_desc']);
        $datos['solicitud_barcode'] = $solicitud['MutualProductoSolicitud']['barcode'];
        $datos['solicitud_saldo'] = $saldo;
        $datos['beneficiario_tdocndoc'] = $solicitud['MutualProductoSolicitud']['beneficiario_tdocndoc'];
        $datos['beneficiario_apenom'] = utf8_encode($solicitud['MutualProductoSolicitud']['beneficiario_apenom']);
        $datos['beneficiario_domicilio'] = utf8_encode($solicitud['MutualProductoSolicitud']['beneficiario_domicilio']);
        $datos['beneficiario_telefonos'] = utf8_encode($solicitud['MutualProductoSolicitud']['beneficiario_telefonos']);
        $datos['beneficiario_complementarios'] = utf8_encode($solicitud['MutualProductoSolicitud']['beneficiario_complementarios']);
        $datos['beneficio_codigo_organismo'] = substr($solicitud['MutualProductoSolicitud']['organismo'],-4);
        $datos['beneficio_codigo_organismo_descripcion'] = utf8_encode($solicitud['MutualProductoSolicitud']['organismo_desc']);
        $datos['beneficio_codigo_turno'] = substr($solicitud['MutualProductoSolicitud']['turno'],-5);
        $datos['beneficio_codigo_turno_descripcion'] = utf8_encode($solicitud['MutualProductoSolicitud']['turno_desc']);
        $datos['beneficio_banco_codigo'] = $solicitud['MutualProductoSolicitud']['beneficio_banco_codigo'];
        $datos['beneficio_banco'] = utf8_encode($solicitud['MutualProductoSolicitud']['beneficio_banco']);
        $datos['beneficio_sucursal'] = $solicitud['MutualProductoSolicitud']['beneficio_sucursal'];
        $datos['beneficio_cuenta'] = $solicitud['MutualProductoSolicitud']['beneficio_cuenta'];
        $datos['beneficio_cbu'] = $solicitud['MutualProductoSolicitud']['beneficio_cbu'];
        $datos['beneficio_ingreso'] = $solicitud['MutualProductoSolicitud']['beneficio_ingreso'];
        $datos['beneficio_legajo'] = $solicitud['MutualProductoSolicitud']['beneficio_legajo'];
        $datos['beneficio_nro_beneficio'] = $solicitud['MutualProductoSolicitud']['beneficio_cjpc_nro'];
        $datos['vendedor_cuit'] = $solicitud['MutualProductoSolicitud']['vendedor_cuit'];
        $datos['vendedor_datos'] = utf8_encode($solicitud['MutualProductoSolicitud']['vendedor_nombre']);
        $datos['proveedor_plan_id'] = $solicitud['MutualProductoSolicitud']['proveedor_plan_id'];
        $datos['proveedor_plan_descripcion'] = $solicitud['MutualProductoSolicitud']['proveedor_plan_descripcion'];
        $datos['proveedor_plan_status'] = $solicitud['MutualProductoSolicitud']['proveedor_plan_activo'];
        $datos['proveedor_plan_producto'] = $solicitud['MutualProductoSolicitud']['proveedor_plan_producto'];
        $datos['proveedor_plan_producto_descripcion'] = $solicitud['MutualProductoSolicitud']['proveedor_plan_producto_descripcion'];
        $datos['proveedor_plan_string'] = $solicitud['MutualProductoSolicitud']['proveedor_plan_string'];
        $datos['solicitud_cancelaciones'] = [];
        
        $oCAN = new CancelacionOrden();
        $cancelaciones = $oCAN->getCancelacionBySolicitudMin($solicitud['MutualProductoSolicitud']['id']);   
        if(!empty($cancelaciones)) {
            foreach ($cancelaciones as $key => $value) {
                array_push($datos['solicitud_cancelaciones'], array(
                    'orden_cancelacion' => $value['co']['id'],
                    'proveedor' => utf8_encode($value['p']['razon_social']),
                    'referencia' => utf8_encode($value['co']['concepto']),
                    'importe_cancela' => $value['co']['importe_proveedor'],
                    'solicitud' => $value['o']['mutual_producto_solicitud_id'],
                    'cuota_nro' => $value[0]['cuotas'],
                ));
            }
        }
        return $datos;
    }
    
    
}
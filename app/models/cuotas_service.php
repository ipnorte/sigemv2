<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::import('Vendor','sigem_service');
App::import('Model', 'mutual.OrdenDescuentoCuota');

class CuotasService extends SIGEMService{
    
    var $useTable = false;
    var $name = 'CuotasService';
    
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
	 * Valida la conexion
	 * @param string $PIN
	 * @return string
	 */
	function validate($PIN){
		$this->validatePIN($PIN);
		return $this->getResponse();
	}    
    
	/**
	 * Metodo de prueba
	 * @param string $param
	 * @param string $PIN
	 * @return string
	 */
	function testing($param,$PIN){
		$response = $param;
		if(!$this->validatePIN($PIN)) return $this->getResponse();
		//SIGO CON EL METODO
		$resultado = array("$param",2,3,4,5,6 => $param);
		$this->setResponse('find',count($resultado));
		$this->setResponse('result',$resultado);
		return $this->getResponse();
	}   
    
    /**
     * 
     * @param string $documento
     * @param string $PIN
     * @return string
     */
    function estadoCuenta($documento,$PIN){
        if(!$this->validatePIN($PIN)) return $this->getResponse();
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
                                cdc.importe as importe_cuota
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
                        'importe_cuota' => $pago['cdc']['importe_cuota'],
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
    
    
}

?>
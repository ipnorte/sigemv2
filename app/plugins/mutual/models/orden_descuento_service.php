<?php

define('SQL_ORDENDTO',"

select 
                    OrdenDescuento.* 
                    ,lpad(OrdenDescuento.id,7,0) nro_print
                    ,concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) tipo_nro
                    ,lpad(OrdenDescuento.cuotas,2,0) cuotas_print
                    ,concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero,' - ',Producto.concepto_1) recibo_detalle
                    
                    -- datos del solicitante
                    ,concat('DNI ',Persona.documento, ' - ',Persona.apellido,', ',Persona.nombre) beneficiario
                    ,concat(Persona.apellido,', ',Persona.nombre) beneficiario_apenom
                    ,concat('DNI - ',Persona.documento) beneficiario_tdocndoc
                    ,'DNI' beneficiario_tdoc
                    ,Persona.documento beneficiario_ndoc
                    ,concat('#',Socio.id,'| ALTA: ',date_format(Socio.fecha_alta,'%d-%m-%Y'),' | CATEGORIA: ',SocioCategoria.concepto_1,' | CALIF: ',ifnull(SocioCalificacion.concepto_1,'***'),' (',Socio.fecha_calificacion,')') beneficiario_socio
                    ,ifnull(SocioCalificacion.concepto_1,'***') ultima_calificacion
                    ,Persona.cuit_cuil beneficiario_cuit_cuil
                    ,Persona.nombre_conyuge beneficiario_conyuge
                    ,case length(Persona.cuit_cuil) when 11 then concat(left(Persona.cuit_cuil,2),'-',substr(Persona.cuit_cuil,3,8),'-',right(Persona.cuit_cuil,2)) else null end beneficiario_cuit_cuil_pick
                    ,upper(concat(Persona.calle,' ',if(Persona.numero_calle <> 0,Persona.numero_calle,''),' - ',Persona.localidad,' (CP ',Persona.codigo_postal,')'	,if(Persona.provincia_id is not null,concat(' - ',Provincia.nombre),''))) beneficiario_domicilio
                    ,concat('CEL.: ',Persona.telefono_movil,' | TEL: ',Persona.telefono_fijo,' | OTRO: ',Persona.telefono_referencia,' (Ref.: ',ifnull(Persona.persona_referencia,''),') | EMAIL: ',ifnull(Persona.e_mail,'')) beneficiario_telefonos
                    ,concat('EST. CIVIL: ',ifnull(EstadoCivil.concepto_1,''),' | SEXO: ',ifnull(Persona.sexo,''),' | NACIMIENTO: ',date_format(Persona.fecha_nacimiento,'%d-%m-%Y'),' | EDAD: ',TIMESTAMPDIFF(YEAR,Persona.fecha_nacimiento,now()),' | CUIT/L: ',Persona.cuit_cuil) beneficiario_complementarios
                    
                    -- datos del beneficio
                    ,PersonaBeneficio.activo beneficio_activo
                    
                    ,concat(case 
                    	when left(right(PersonaBeneficio.codigo_beneficio,4),2) = 22 
                        then 
                    	(select concat(Organismo.concepto_1,' - ',substr(ifnull(Empresa.concepto_1,''),1,40),'|'
                    	,if(PersonaBeneficio.codigo_empresa <> PersonaBeneficio.turno_pago, ifnull(PersonaBeneficio.turno_pago,''),'')
                        ,'|',ifnull(PersonaBeneficio.codigo_reparticion,''),'|CBU:',PersonaBeneficio.cbu))
                        
                    	when left(right(PersonaBeneficio.codigo_beneficio,4),2) <> 22 
                        then 
                    	(select concat(Organismo.concepto_1,' - BENEFICIO: ',concat(PersonaBeneficio.tipo,PersonaBeneficio.nro_ley,PersonaBeneficio.nro_beneficio,PersonaBeneficio.sub_beneficio)))   
                        
                    end, if(PersonaBeneficio.activo = 0,' ** NO VIGENTE **','')) beneficio_str
                    
                    
                    ,ifnull(Banco.nombre,'') beneficio_banco
                    ,PersonaBeneficio.acuerdo_debito beneficio_acuerdo_debito
                    ,PersonaBeneficio.banco_id beneficio_banco_id
                    ,PersonaBeneficio.nro_sucursal beneficio_sucursal
                    ,PersonaBeneficio.nro_cta_bco beneficio_cuenta
                    ,PersonaBeneficio.cbu beneficio_cbu
                    ,PersonaBeneficio.fecha_ingreso beneficio_ingreso
                    ,TIMESTAMPDIFF(YEAR,PersonaBeneficio.fecha_ingreso,now()) beneficio_antiguedad
                    ,PersonaBeneficio.nro_legajo beneficio_legajo
                    ,PersonaBeneficio.tipo beneficio_tipo_beneficio
                    ,PersonaBeneficio.nro_beneficio beneficio_nro_beneficio
                    ,PersonaBeneficio.nro_ley beneficio_nro_ley
                    ,PersonaBeneficio.sub_beneficio beneficio_sub_beneficio
                    ,case left(right(PersonaBeneficio.codigo_beneficio,4),2) when 77 then concat(PersonaBeneficio.tipo,PersonaBeneficio.nro_ley,PersonaBeneficio.nro_beneficio,PersonaBeneficio.sub_beneficio) else '' end beneficio_cjpc_nro
                    ,PersonaBeneficio.codigo_reparticion beneficio_codigo_reparticion
                    ,PersonaBeneficio.tarjeta_titular beneficio_tarjeta_titular
                    ,PersonaBeneficio.tarjeta_numero beneficio_tarjeta_numero
                    ,PersonaBeneficio.codigo_beneficio organismo
                    ,Organismo.concepto_1 organismo_desc
                    ,if(PersonaBeneficio.codigo_empresa <> PersonaBeneficio.turno_pago, PersonaBeneficio.turno_pago, PersonaBeneficio.codigo_empresa) turno
                    ,concat(Empresa.concepto_1,' - ',right(PersonaBeneficio.turno_pago,5)) turno_desc
                    
                    -- datos del proveedor
                    ,concat(Proveedor.razon_social_resumida,' - ',Producto.concepto_1) proveedor_producto
                    ,Proveedor.razon_social_resumida proveedor
                    ,Proveedor.pagare_blank proveedor_pagare_blank
                    ,Proveedor.direccion_pagare proveedor_pagare_direccion
                    ,Proveedor.razon_social proveedor_full_name
                    ,Proveedor.cuit proveedor_cuit
                    ,concat(Proveedor.calle,' ',Proveedor.numero_calle,' - ',Proveedor.piso,' - Of. ',Proveedor.dpto) proveedor_domicilio
                    ,concat(Proveedor.codigo_postal,' - ',Proveedor.localidad) proveedor_localidad
                    ,Proveedor.telefono_fijo proveedor_telefono
                    ,Proveedor.reasignable proveedor_reasignable
                    ,Producto.concepto_1 producto
                    
                    from orden_descuentos OrdenDescuento 
                    inner join proveedores Proveedor on Proveedor.id = OrdenDescuento.proveedor_id
                    inner join global_datos Producto on Producto.id = OrdenDescuento.tipo_producto
                    -- 
                    inner join socios Socio on Socio.id = OrdenDescuento.socio_id
                    inner join personas Persona on Persona.id = Socio.persona_id
                    left join provincias Provincia on Provincia.id = Persona.provincia_id
                    left join global_datos EstadoCivil on EstadoCivil.id = Persona.estado_civil
                    
                    left join global_datos SocioCategoria on SocioCategoria.id = Socio.categoria
                    left join global_datos SocioCalificacion on SocioCalificacion.id = Socio.calificacion
                    
                    inner join persona_beneficios PersonaBeneficio on PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id
                    inner join global_datos Organismo on Organismo.id = PersonaBeneficio.codigo_beneficio
                    left join bancos Banco on Banco.id = PersonaBeneficio.banco_id
                    left join global_datos Empresa on Empresa.id = PersonaBeneficio.codigo_empresa 

");

class OrdenDescuentoService extends MutualAppModel
{
    public $useTable = FALSE;
    public $name = "OrdenService";
    
    public function getOrden($id) {
        
        $sql = SQL_ORDENDTO . " where OrdenDescuento.id = $id ";

        $result = $this->query($sql);
        if(empty($result)) {return null;}
        
       
        
        $orden = array();
        
        $ordenDescuento = Set::extract("{n}.OrdenDescuento",$result);
        foreach ($ordenDescuento[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $camposCalculados = Set::extract("{n}.0",$result);
        foreach ($camposCalculados[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $persona = Set::extract("{n}.Persona",$result);
        foreach ($persona[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $beneficio = Set::extract("{n}.PersonaBeneficio",$result);
        foreach ($beneficio[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $proveedor = Set::extract("{n}.Proveedor",$result);
        foreach ($proveedor[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $producto = Set::extract("{n}.Producto",$result);
        foreach ($producto[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $organismo = Set::extract("{n}.Organismo",$result);
        foreach ($organismo[0] as $key => $value) {
            $orden['OrdenDescuento'][$key] = $value;
        }
        
        $orden['OrdenDescuento']['total_letras'] = parent::num2letras($orden['OrdenDescuento']['importe_total']);
        $orden['OrdenDescuento']['total_cuota_letras'] = parent::num2letras($orden['OrdenDescuento']['importe_cuota']);
        $orden['OrdenDescuento']['cantidad_cuota_letras'] = parent::num2letras($orden['OrdenDescuento']['cuotas'],false);
        
        return $orden;
    }
    
    public function getByTipoAndNumero($tipoOrden,$numero) {
        $sql = SQL_ORDENDTO . " where OrdenDescuento.tipo_orden_dto = '$tipoOrden' and OrdenDescuento.numero = '$numero' and OrdenDescuento.activo = 1 ORDER BY OrdenDescuento.periodo_ini DESC,OrdenDescuento.created DESC";
        $result = $this->query($sql);
        if(empty($result)) {return null;}
        return $this->_procesarLLaves($result);
        
    }
    
    private function _procesarLLaves($result){
        
        if(empty($result)) {return null;}
        $orden = $ordenes = array();
        
        foreach ($result as $dataSet) {
            
            foreach ($dataSet['OrdenDescuento'] as $key => $value) {
                $orden['OrdenDescuento'][$key] = $value;
            }
            foreach ($dataSet[0] as $key => $value) {
                $orden['OrdenDescuento'][$key] = $value;
            }
            
            foreach ($dataSet['Persona'] as $key => $value) {
                $orden['OrdenDescuento'][$key] = $value;
            }
            
            foreach ($dataSet['PersonaBeneficio'] as $key => $value) {
                $orden['OrdenDescuento'][$key] = $value;
            }
            
            foreach ($dataSet['Proveedor'] as $key => $value) {
                $orden['OrdenDescuento'][$key] = $value;
            }
            
            foreach ($dataSet['Producto'] as $key => $value) {
                $orden['OrdenDescuento'][$key] = $value;
            }
            
            foreach ($dataSet['Organismo'] as $key => $value) {
                $orden['OrdenDescuento'][$key] = $value;
            }
            
            array_push($ordenes,$orden);
        }
        return $ordenes;
    }
    
}


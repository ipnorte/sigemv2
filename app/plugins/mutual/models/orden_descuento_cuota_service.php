<?php

define('SQL_CUOTA',"
select 
OrdenDescuentoCuota.* 
,ifnull(Producto.concepto_1,'') tipo_producto_desc
,ifnull(TipoCuota.concepto_1,'') tipo_cuota_desc
,ifnull(SituacionCuota.concepto_1,'') situacion_desc
,concat(Producto.concepto_1,' - ',TipoCuota.concepto_1) producto_cuota
,concat(Producto.concepto_1,' - ',TipoCuota.concepto_1,if(OrdenDescuentoCuota.nro_referencia_proveedor <> '',concat(' (REF: ',OrdenDescuentoCuota.nro_referencia_proveedor,')'),'')) producto_cuota_ref


,
case OrdenDescuentoCuota.estado
	when 'B'
    then 0
	else
ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0)
end  saldo_cuota

,ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc 
where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) pagado

,case 
	when 
			ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
			where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) <> 0 and OrdenDescuentoCuota.estado <> 'B'
	then 'Adeudada' 
    when 
			ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
			where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) = 0 and OrdenDescuentoCuota.estado <> 'B'    
	then 'Pagada'
    when OrdenDescuentoCuota.estado = 'B' then 'Baja'
    when OrdenDescuentoCuota.estado = 'C' then 'Convenio'
    when OrdenDescuentoCuota.estado = 'D' then 'Cob.Directo'
    end 
    estado_desc

    
,case 
	when 
			ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
			where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) > 0 and OrdenDescuentoCuota.estado <> 'B'
		then 'A' 
    when 
			ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
			where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) = 0     
		then 
			'P'
	else 
		OrdenDescuentoCuota.estado
    end 
    estado    

-- FECHA ULTIMO PAGO
,(select max(co.periodo_cobro) from orden_descuento_cobro_cuotas cc
inner join orden_descuento_cobros co on co.id = cc.orden_descuento_cobro_id 
where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id) periodo_ultimo_pago
,(select max(co.fecha) from orden_descuento_cobro_cuotas cc
inner join orden_descuento_cobros co on co.id = cc.orden_descuento_cobro_id 
where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id) fecha_ultimo_pago

,if(OrdenDescuentoCuota.periodo < DATE_FORMAT(NOW(), '%Y%m'),1,0) vencida
    
,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',lpad(OrdenDescuento.cuotas,2,0)) cuota
,lpad(OrdenDescuento.id,7,0) nro_print
,concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) tipo_nro
,lpad(OrdenDescuento.cuotas,2,0) cuotas_print
,concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero,' - ',Producto.concepto_1) recibo_detalle
,OrdenDescuento.numero numero_odto
,OrdenDescuento.periodo_ini orden_descuento_periodo_ini
,OrdenDescuento.importe_total orden_descuento_total
,OrdenDescuento.cuotas orden_descuento_cuotas
,OrdenDescuento.importe_cuota orden_descuento_impo_cuota
,OrdenDescuento.primer_vto_socio orden_descuento_primer_vto_socio
,OrdenDescuento.primer_vto_proveedor  orden_descuento_primer_vto_proveedor
,OrdenDescuento.fecha orden_descuento_fecha
,OrdenDescuento.permanente orden_descuento_permanente


-- bloqueo de liquidacion
,(
SELECT 
concat(Liquidacion.id,'|',
Liquidacion.imputada,'|',
Liquidacion.cerrada,'|',
Liquidacion.periodo,'|',
Liquidacion.codigo_organismo,'|',
OrganismoLiquidado.concepto_1,'|',
LiquidacionCuota.importe_debitado) as bloqueo 
FROM liquidacion_cuotas AS LiquidacionCuota
INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
inner join global_datos OrganismoLiquidado on OrganismoLiquidado.id = Liquidacion.codigo_organismo
WHERE LiquidacionCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id
ORDER BY Liquidacion.id DESC
LIMIT 1
) bloqueo_liquidacion

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
	(select concat(Organismo.concepto_1,' - ',substr(ifnull(Empresa.concepto_1,''),1,20),'|'
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

from orden_descuento_cuotas OrdenDescuentoCuota

inner join orden_descuentos OrdenDescuento on OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id 
inner join proveedores Proveedor on Proveedor.id = OrdenDescuento.proveedor_id
inner join global_datos Producto on Producto.id = OrdenDescuentoCuota.tipo_producto
inner join global_datos TipoCuota on TipoCuota.id = OrdenDescuentoCuota.tipo_cuota
left join global_datos SituacionCuota on SituacionCuota.id = OrdenDescuentoCuota.situacion
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

"
    );

define('SQL_CUOTA_JOINS',"

    inner join orden_descuentos OrdenDescuento on OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id 
    inner join proveedores Proveedor on Proveedor.id = OrdenDescuento.proveedor_id
    inner join global_datos Producto on Producto.id = OrdenDescuentoCuota.tipo_producto
    inner join global_datos TipoCuota on TipoCuota.id = OrdenDescuentoCuota.tipo_cuota
    left join global_datos SituacionCuota on SituacionCuota.id = OrdenDescuentoCuota.situacion
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

define('SQL_CUOTA_FIELDS',"

OrdenDescuentoCuota.* 
,ifnull(Producto.concepto_1,'') tipo_producto_desc
,ifnull(TipoCuota.concepto_1,'') tipo_cuota_desc
,ifnull(SituacionCuota.concepto_1,'') situacion_desc
,concat(Producto.concepto_1,' - ',TipoCuota.concepto_1) producto_cuota
,concat(Producto.concepto_1,' - ',TipoCuota.concepto_1,if(OrdenDescuentoCuota.nro_referencia_proveedor <> '',concat(' (REF: ',OrdenDescuentoCuota.nro_referencia_proveedor,')'),'')) producto_cuota_ref


,
case OrdenDescuentoCuota.estado
	when 'B'
    then 0
	else
ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0)
end  saldo_cuota

,ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc 
where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) pagado

,case 
	when 
			ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
			where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) <> 0 and OrdenDescuentoCuota.estado <> 'B'
	then 'Adeudada' 
    when 
			ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
			where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) = 0 and OrdenDescuentoCuota.estado <> 'B'    
	then 'Pagada'
    when OrdenDescuentoCuota.estado = 'B' then 'Baja'
    when OrdenDescuentoCuota.estado = 'C' then 'Convenio'
    when OrdenDescuentoCuota.estado = 'D' then 'Cob.Directo'
    end 
    estado_desc

    
,case 
	when 
			ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
			where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) > 0 and OrdenDescuentoCuota.estado <> 'B'
		then 'A' 
    when 
			ifnull(OrdenDescuentoCuota.importe - (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc 
			where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id),0) = 0     
		then 
			'P'
	else 
		OrdenDescuentoCuota.estado
    end 
    estado    

-- FECHA ULTIMO PAGO
,(select max(co.periodo_cobro) from orden_descuento_cobro_cuotas cc
inner join orden_descuento_cobros co on co.id = cc.orden_descuento_cobro_id 
where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id) periodo_ultimo_pago
,(select max(co.fecha) from orden_descuento_cobro_cuotas cc
inner join orden_descuento_cobros co on co.id = cc.orden_descuento_cobro_id 
where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id) fecha_ultimo_pago

,if(datediff(OrdenDescuentoCuota.vencimiento,now()) < 0,1,0) vencida
    
,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',lpad(OrdenDescuento.cuotas,2,0)) cuota
,lpad(OrdenDescuento.id,7,0) nro_print
,concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) tipo_nro
,lpad(OrdenDescuento.cuotas,2,0) cuotas_print
,concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero,' - ',Producto.concepto_1) recibo_detalle
,OrdenDescuento.numero numero_odto
,OrdenDescuento.periodo_ini orden_descuento_periodo_ini
,OrdenDescuento.importe_total orden_descuento_total
,OrdenDescuento.cuotas orden_descuento_cuotas
,OrdenDescuento.importe_cuota orden_descuento_impo_cuota
,OrdenDescuento.primer_vto_socio orden_descuento_primer_vto_socio
,OrdenDescuento.primer_vto_proveedor  orden_descuento_primer_vto_proveedor
,OrdenDescuento.fecha orden_descuento_fecha
,OrdenDescuento.permanente orden_descuento_permanente


-- bloqueo de liquidacion
,(
SELECT 
concat(Liquidacion.id,'|',
Liquidacion.imputada,'|',
Liquidacion.cerrada,'|',
Liquidacion.periodo,'|',
Liquidacion.codigo_organismo,'|',
OrganismoLiquidado.concepto_1,'|',
LiquidacionCuota.importe_debitado) as bloqueo 
FROM liquidacion_cuotas AS LiquidacionCuota
INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = LiquidacionCuota.liquidacion_id)
inner join global_datos OrganismoLiquidado on OrganismoLiquidado.id = Liquidacion.codigo_organismo
WHERE LiquidacionCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id
ORDER BY Liquidacion.id DESC
LIMIT 1
) bloqueo_liquidacion

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
	(select concat(Organismo.concepto_1,' - ',substr(ifnull(Empresa.concepto_1,''),1,20),'|'
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

");

class OrdenDescuentoCuotaService extends MutualAppModel
{
    
    public $useTable = false;
    public $name = "OrdenDescuentoCuotaService";
    
    public function getCuota($id) {
        $sql = SQL_CUOTA . "where OrdenDescuentoCuota.id = $id;";
        $result = $this->query($sql);
        if(empty($result)) {return null;}
        
        $cuota = array();
        
        $ordenDtoCuota = Set::extract("{n}.OrdenDescuentoCuota",$result);
        foreach ($ordenDtoCuota[0] as $key => $value) {
            $cuota['OrdenDescuentoCuota'][$key] = $value;
        }
        $camposCalculados = Set::extract("{n}.0",$result);
        foreach ($camposCalculados[0] as $key => $value) {
            $cuota['OrdenDescuentoCuota'][$key] = $value;
        }
        $ordenDto = Set::extract("{n}.OrdenDescuento",$result);
        foreach ($ordenDto[0] as $key => $value) {
            $cuota['OrdenDescuentoCuota'][$key] = $value;
        }
        $persona = Set::extract("{n}.Persona",$result);
        foreach ($persona[0] as $key => $value) {
            $cuota['OrdenDescuentoCuota'][$key] = $value;
        }
        $personaBeneficio = Set::extract("{n}.PersonaBeneficio",$result);
        foreach ($personaBeneficio[0] as $key => $value) {
            $cuota['OrdenDescuentoCuota'][$key] = $value;
        }
        $organismo = Set::extract("{n}.Organismo",$result);
        foreach ($organismo[0] as $key => $value) {
            $cuota['OrdenDescuentoCuota'][$key] = $value;
        }
        $proveedor = Set::extract("{n}.Proveedor",$result);
        foreach ($proveedor[0] as $key => $value) {
            $cuota['OrdenDescuentoCuota'][$key] = $value;
        }
        $producto = Set::extract("{n}.Producto",$result);
        foreach ($producto[0] as $key => $value) {
            $cuota['OrdenDescuentoCuota'][$key] = $value;
        }
        
        return $cuota;
    }
    
    function cuotasByOrdenDto($cuota_descuento_id,$discriminaPagos=false,$soloDeuda=false){
        $sql = SQL_CUOTA . "where OrdenDescuentoCuota.orden_descuento_id = $cuota_descuento_id ORDER BY OrdenDescuentoCuota.periodo, OrdenDescuentoCuota.nro_cuota ASC;";
        $result = $this->query($sql);
        if(empty($result)) {return null;}
        $cuotas = $this->_procesarLLaves($result);
        return $cuotas;
    }
    
    public function getCuotasByOrdenDeCobro($ordenDeCobroId) {
//         $sql = SQL_CUOTA 
//                 . " inner join orden_descuento_cobro_cuotas cc on cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id 
//                 where cc.orden_descuento_cobro_id = $ordenDeCobroId;";

        $sql = "SELECT " . SQL_CUOTA_FIELDS . ", OrdenDescuentoCobroCuota.* from orden_descuento_cuotas OrdenDescuentoCuota "
                . SQL_CUOTA_JOINS 
                . " inner join orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota 
                    on OrdenDescuentoCobroCuota.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                WHERE OrdenDescuentoCobroCuota.orden_descuento_cobro_id = $ordenDeCobroId;
                 ";
        
        
        $result = $this->query($sql);
        if(empty($result)) {return null;}
        foreach ($result as $i => $dataSet){
            foreach ($dataSet['OrdenDescuentoCobroCuota'] as $key => $value) {
                $result[$i]['OrdenDescuentoCuota']["cobro_".$key] = $value;
            }
        }
        $cuotas = $this->_procesarLLaves($result);
        return $cuotas;
    }
    
    
    private function _procesarLLaves($result){
        
        if(empty($result)) {return null;}
        $cuota = $cuotas = array();

        foreach ($result as $dataSet) {

            foreach ($dataSet['OrdenDescuentoCuota'] as $key => $value) {
                $cuota['OrdenDescuentoCuota'][$key] = $value;
            }
            foreach ($dataSet['OrdenDescuento'] as $key => $value) {
                $cuota['OrdenDescuentoCuota'][$key] = $value;
            }
            foreach ($dataSet[0] as $key => $value) {
                $cuota['OrdenDescuentoCuota'][$key] = $value;
            }
            
            foreach ($dataSet['Persona'] as $key => $value) {
                $cuota['OrdenDescuentoCuota'][$key] = $value;
            }
            
            foreach ($dataSet['PersonaBeneficio'] as $key => $value) {
                $cuota['OrdenDescuentoCuota'][$key] = $value;
            }
            
            foreach ($dataSet['Proveedor'] as $key => $value) {
                $cuota['OrdenDescuentoCuota'][$key] = $value;
            }
            
            foreach ($dataSet['Producto'] as $key => $value) {
                $cuota['OrdenDescuentoCuota'][$key] = $value;
            }
            
            foreach ($dataSet['Organismo'] as $key => $value) {
                $cuota['OrdenDescuentoCuota'][$key] = $value;
            }
            
            array_push($cuotas,$cuota);
        }
        return $cuotas;
    }
    
}


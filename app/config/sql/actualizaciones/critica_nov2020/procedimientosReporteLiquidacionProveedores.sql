DROP PROCEDURE IF EXISTS `SP_REPORTE_PROVEEDOR_BAJAS`;
DROP PROCEDURE IF EXISTS `SP_REPORTE_PROVEEDOR_CAJA`;
DROP PROCEDURE IF EXISTS `SP_REPORTE_PROVEEDOR_DETALLE`;
DROP PROCEDURE IF EXISTS `SP_REPORTE_PROVEEDOR_MORACUOTA`;
DROP PROCEDURE IF EXISTS `SP_REPORTE_PROVEEDOR_MORATEMPRANA`;
DROP PROCEDURE IF EXISTS `SP_REPORTE_PROVEEDOR_REVERSO`;
DROP PROCEDURE IF EXISTS `SP_REPORTE_PROVEEDOR_STOPS`;

DELIMITER $$
CREATE PROCEDURE `SP_REPORTE_PROVEEDOR_BAJAS`(
in vASINCID int(11),
in vCLAVE1 varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vLIQ int(11),
in vPROV Int(11),
in vTPROD varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vTCUOT varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vSOCIO_ID int(11)
)
BEGIN

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQ;

DROP TEMPORARY TABLE IF EXISTS tmp_proveedores;
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_proveedores (INDEX IDX_1(liquidacion_id,codigo_organismo,proveedor_id,periodo)) as 
select liquidacion_id,Liquidacion.periodo,Liquidacion.codigo_organismo,proveedor_id from liquidacion_cuotas LiquidacionCuota
inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
where LiquidacionCuota.liquidacion_id = 640 and IF(@vPROV IS NULL, TRUE, LiquidacionCuota.proveedor_id = @vPROV) group by proveedor_id;
-- select * from tmp_proveedores;

DROP TEMPORARY TABLE IF EXISTS tmp_reporte_proveedores_baja;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_reporte_proveedores_baja as
select 
	OrdenDescuentoCuota.socio_id as socio_id
	,OrdenDescuentoCuota.id
	,OrdenDescuentoCuota.proveedor_id
	,PersonaBeneficio.codigo_beneficio 
	,concat(TipoDocumento.concepto_1,'-',Persona.documento) as tdoc_ndoc 
	,Persona.documento
	,Persona.cuit_cuil
	,concat(Persona.apellido,', ',Persona.nombre) socio  
	,concat(OrdenDescuento.tipo_orden_dto,'# ',OrdenDescuento.numero) as tipo_numero
	,concat(TipoProducto.concepto_1,'-',TipoCuota.concepto_1) as producto_concepto
	,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',lpad(OrdenDescuento.cuotas,2,0)) as cuota 
	,OrdenDescuentoCuota.periodo
	,OrdenDescuentoCuota.importe    
	,ifnull(MotivoBaja.concepto_1,'') as motivo_baja
from orden_descuento_cuotas OrdenDescuentoCuota
inner join tmp_proveedores t1 on (t1.proveedor_id = OrdenDescuentoCuota.proveedor_id
						AND t1.periodo = OrdenDescuentoCuota.periodo)
inner join persona_beneficios as PersonaBeneficio on (OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id)
inner join personas Persona on (Persona.id = PersonaBeneficio.persona_id)
inner join global_datos TipoDocumento on (TipoDocumento.id = Persona.tipo_documento)
inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
inner join global_datos TipoProducto on TipoProducto.id = OrdenDescuentoCuota.tipo_producto
inner join global_datos TipoCuota on TipoCuota.id = OrdenDescuentoCuota.tipo_cuota
inner join global_datos MotivoBaja on MotivoBaja.id = OrdenDescuentoCuota.situacion                        
where estado = 'B'
and PersonaBeneficio.codigo_beneficio = @ORGANISMO
AND IF(@vSOCIO_ID IS NULL,TRUE,OrdenDescuentoCuota.socio_id = @vSOCIO_ID)
AND IF(@vTPROD IS NULL, TRUE, OrdenDescuentoCuota.tipo_producto = @vTPROD)
AND IF(@vTCUOT IS NULL, TRUE, OrdenDescuentoCuota.tipo_cuota = @vTCUOT); 

-- select * from tmp_reporte_proveedores_baja;

DROP TEMPORARY TABLE IF EXISTS tmp_reporte_proveedores_baja;            
DROP TEMPORARY TABLE IF EXISTS tmp_proveedores;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_REPORTE_PROVEEDOR_CAJA`(
in vASINCID int(11),
in vCLAVE1 varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vLIQ int(11),
in vPROV Int(11),
in vTPROD varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vTCUOT varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vSOCIO_ID int(11)
)
BEGIN

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQ;

DROP TEMPORARY TABLE IF EXISTS tmp_proveedores;
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_proveedores (INDEX IDX_1(liquidacion_id,codigo_organismo,proveedor_id,periodo)) as 
select liquidacion_id,Liquidacion.periodo,Liquidacion.codigo_organismo,proveedor_id from liquidacion_cuotas LiquidacionCuota
inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
where LiquidacionCuota.liquidacion_id = vLIQ and IF(vPROV IS NULL, TRUE, LiquidacionCuota.proveedor_id = vPROV) group by proveedor_id;


DROP TEMPORARY TABLE IF EXISTS tmp_reporte_proveedores_ccaja;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_reporte_proveedores_ccaja as
select 
	OrdenDescuentoCobroCuota.id
    ,Proveedor.razon_social
	,OrdenDescuentoCuota.socio_id as socio_id
	,OrdenDescuentoCobroCuota.orden_descuento_cobro_id
	,OrdenDescuentoCobroCuota.orden_descuento_cuota_id 
    ,OrdenDescuento.id as orden_id
	,OrdenDescuentoCobroCuota.proveedor_id
	,PersonaBeneficio.codigo_beneficio 
	,concat(TipoDocumento.concepto_1,'-',Persona.documento) as tdoc_ndoc 
	,Persona.documento
	,Persona.cuit_cuil
	,concat(Persona.apellido,', ',Persona.nombre) socio  
	,concat(OrdenDescuento.tipo_orden_dto,'# ',OrdenDescuento.numero) as tipo_numero
	,concat(TipoProducto.concepto_1,'-',TipoCuota.concepto_1) as producto_concepto
	,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',lpad(OrdenDescuento.cuotas,2,0)) as cuota 
	,OrdenDescuentoCuota.periodo as periodo_cuota
    ,OrdenDescuentoCuota.nro_referencia_proveedor
    ,TipoCobro.concepto_1 as concepto_cobro
    ,ifnull(FormaCancelacion.concepto_1,'') as forma_cancelacion
	,OrdenDescuentoCobroCuota.periodo_cobro
	,OrdenDescuentoCobroCuota.importe
	,OrdenDescuentoCobroCuota.alicuota_comision_cobranza
	,OrdenDescuentoCobroCuota.comision_cobranza 

from orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
INNER JOIN orden_descuento_cobros AS OrdenDescuentoCobro on OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
inner join tmp_proveedores t1 on (t1.proveedor_id = OrdenDescuentoCobroCuota.proveedor_id
						AND t1.periodo = OrdenDescuentoCobroCuota.periodo_cobro)
inner join orden_descuento_cuotas OrdenDescuentoCuota on OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id    
inner join persona_beneficios as PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
inner join personas Persona on (Persona.id = PersonaBeneficio.persona_id)
inner join global_datos TipoDocumento on (TipoDocumento.id = Persona.tipo_documento)
inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
inner join global_datos TipoProducto on TipoProducto.id = OrdenDescuentoCuota.tipo_producto
inner join global_datos TipoCuota on TipoCuota.id = OrdenDescuentoCuota.tipo_cuota
inner join global_datos TipoCobro on TipoCobro.id = OrdenDescuentoCobro.tipo_cobro
left join cancelacion_ordenes CancelacionOrden on CancelacionOrden.id = OrdenDescuentoCobro.cancelacion_orden_id
left join global_datos FormaCancelacion on FormaCancelacion.id = CancelacionOrden.forma_cancelacion
inner join proveedores Proveedor on Proveedor.id = OrdenDescuentoCobroCuota.proveedor_id
where 
OrdenDescuentoCobro.tipo_cobro <> 'MUTUTCOBRECS'
and OrdenDescuentoCobroCuota.reversado = 0
and PersonaBeneficio.codigo_beneficio = @ORGANISMO
AND IF(@vSOCIO_ID IS NULL,TRUE,OrdenDescuentoCuota.socio_id = vSOCIO_ID)
AND IF(@vTPROD IS NULL, TRUE, OrdenDescuentoCuota.tipo_producto = vTPROD)
AND IF(@vTCUOT IS NULL, TRUE, OrdenDescuentoCuota.tipo_cuota = vTCUOT)  
group by OrdenDescuentoCobroCuota.orden_descuento_cuota_id
order by
        Proveedor.razon_social,
        Persona.apellido,Persona.nombre,
		OrdenDescuentoCuota.orden_descuento_id,
		OrdenDescuentoCuota.periodo;

-- select * from tmp_reporte_proveedores_ccaja;

DROP TEMPORARY TABLE IF EXISTS tmp_reporte_proveedores_ccaja;
DROP TEMPORARY TABLE IF EXISTS tmp_proveedores;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_REPORTE_PROVEEDOR_DETALLE`(
in vASINCID int(11),
in vCLAVE1 varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vLIQ int(11),
in vPROV Int(11),
in vTPROD varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vTCUOT varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vSOCIO_ID int(11),
in vALICUOTAIVA DECIMAL(10,2)
)
BEGIN

DELETE FROM asincrono_temporales where asincrono_id = vASINCID AND clave_1 = vCLAVE1;

SET vTPROD = IF(vTPROD = '' OR vTPROD = '0',NULL,vTPROD);
SET vTCUOT = IF(vTCUOT = '' OR vTCUOT = '0',NULL,vTCUOT);
SET vSOCIO_ID = IF(vSOCIO_ID = '' OR vSOCIO_ID = '0',NULL,vSOCIO_ID);
SET vPROV = IF(vPROV = '' OR vPROV = '0',NULL,vPROV);
SET vALICUOTAIVA = IF(IFNULL(vALICUOTAIVA,0) = '0',0,vALICUOTAIVA);

/*
insert into asincrono_temporales(
        asincrono_id,
        clave_1,
        clave_2,
        clave_3,
        clave_4,        
        texto_1,
		texto_2,
		texto_3,
		texto_4,
		texto_5,
		texto_6,
		texto_7,
		texto_8,
		texto_9,
		texto_10,
		texto_11,
		texto_12,
		texto_13,
		decimal_1,
		decimal_2,
		decimal_3,
		decimal_4,
        entero_1,
        entero_2,
        entero_3
        )
*/
DROP TEMPORARY TABLE IF EXISTS tmp_reporte_proveedores;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_reporte_proveedores (index IDX(id)) as
select 
        vASINCID
        ,vCLAVE1
        ,vLIQ
        ,LiquidacionCuota.id
        ,LiquidacionCuota.proveedor_id
        ,LiquidacionCuota.socio_id   
        ,Proveedor.razon_social
		,concat(TipoDocumento.concepto_1,'-',Persona.documento) as tdoc_ndoc 
        ,Persona.documento
        ,Persona.cuit_cuil
		,concat(Persona.apellido,', ',Persona.nombre) socio
        ,if(ifnull(Persona.sexo,'M') = '','M',Persona.sexo) sexo
		,concat(OrdenDescuento.tipo_orden_dto,'# ',OrdenDescuento.numero) as tipo_numero
        ,concat(TipoProducto.concepto_1,'-',TipoCuota.concepto_1) as producto_concepto
        ,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',lpad(OrdenDescuento.cuotas,2,0)) as cuota
        ,TipoCuota.concepto_1
        ,LiquidacionCuota.periodo_cuota
        ,OrdenDescuentoCuota.nro_referencia_proveedor
        ,LiquidacionCuota.saldo_actual
        ,LiquidacionCuota.importe_debitado
        ,LiquidacionCuota.saldo_actual - LiquidacionCuota.importe_debitado as saldo
        ,LiquidacionCuota.importe
        ,ifnull(ProveedorComision.comision,0) as alicuota_comision
        ,round(ifnull(LiquidacionCuota.importe_debitado * (ifnull(ProveedorComision.comision,0) / 100),0),2) as comision
        ,(LiquidacionCuota.importe_debitado - round(ifnull(LiquidacionCuota.importe_debitado * (ifnull(ProveedorComision.comision,0) / 100),0),2)) as neto_proveedor
        ,LiquidacionCuota.orden_descuento_cuota_id as cuota_id
		,LiquidacionCuota.orden_descuento_id as orden_id
		,CASE 
			WHEN 
				LiquidacionSocioRendicion.banco_intercambio IS NOT NULL 
			THEN LiquidacionSocioRendicion.`status`
            WHEN 
				LiquidacionSocioRendicion.banco_intercambio IS NOT NULL 
                AND LiquidacionSocioRendicion.indica_pago = 1 
				AND (LiquidacionCuota.saldo_actual - LiquidacionCuota.importe_debitado) > 0 
			THEN 'PRO'
		END as  `status`        
        ,
        IF(
			LiquidacionSocioRendicion.banco_intercambio IS NOT NULL
            ,BancoRendicionCodigo.descripcion
            ,IF(LiquidacionSocioRendicion.indica_pago = 1 
				AND (LiquidacionCuota.saldo_actual - LiquidacionCuota.importe_debitado) > 0,
            'PRORRATEO','PENDIENTE DE INFORMAR')
		) AS status_descripcion
        ,Banco.nombre
        ,ifnull(ifnull(ifnull(MutualProductoSolicitud.importe_solicitado,OrdenDescuento.importe_capital),OrdenDescuento.importe_total),0) as importe_solicitado
        ,ifnull(ifnull(ifnull(MutualProductoSolicitud.importe_percibido,OrdenDescuento.importe_solicitado),OrdenDescuento.importe_total),0) as importe_percibido
		,ifnull(round(((ifnull(ifnull(MutualProductoSolicitud.importe_percibido,OrdenDescuento.importe_solicitado),OrdenDescuento.importe_total)) / OrdenDescuento.cuotas),2),OrdenDescuentoCuota.importe) as capital_cuota
        ,ifnull(round((round(OrdenDescuentoCuota.importe 
        - round(((ifnull(ifnull(MutualProductoSolicitud.importe_percibido,OrdenDescuento.importe_solicitado),
			OrdenDescuento.importe_total)) / OrdenDescuento.cuotas),2))) / round(( 1 + (vALICUOTAIVA / 100)),2),2),0)
        as interes_cuota
        
		,ifnull(round(round(OrdenDescuentoCuota.importe 
			- ((ifnull(ifnull(MutualProductoSolicitud.importe_percibido,OrdenDescuento.importe_solicitado),OrdenDescuento.importe_total)) / OrdenDescuento.cuotas) ,2) -- * (vALICUOTAIVA / 100)
		-(round(OrdenDescuentoCuota.importe 
        - round(((ifnull(ifnull(MutualProductoSolicitud.importe_percibido,OrdenDescuento.importe_solicitado),
        OrdenDescuento.importe_total)) / OrdenDescuento.cuotas),2))) / round(( 1 + (vALICUOTAIVA / 100)),2),2),0)        
        as iva_cuota
        ,vALICUOTAIVA as alicuota_iva
        
		from liquidacion_cuotas LiquidacionCuota
        inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
		inner join socios Socio on (Socio.id = LiquidacionCuota.socio_id)
		inner join personas Persona on (Persona.id = Socio.persona_id)
		inner join global_datos TipoDocumento on (TipoDocumento.id = Persona.tipo_documento)
		inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = LiquidacionCuota.orden_descuento_id)
		left join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
        inner join global_datos TipoCuota on TipoCuota.id = LiquidacionCuota.tipo_cuota
        inner join global_datos TipoProducto on TipoProducto.id = LiquidacionCuota.tipo_producto
        inner join proveedores Proveedor on Proveedor.id = LiquidacionCuota.proveedor_id
        
        left join liquidacion_socio_rendiciones LiquidacionSocioRendicion 
        on (
			LiquidacionSocioRendicion.liquidacion_id = LiquidacionCuota.liquidacion_id
			and LiquidacionSocioRendicion.socio_id = LiquidacionCuota.socio_id 
            and IFNULL(LiquidacionSocioRendicion.status,'') <> ''
		)
        left join banco_rendicion_codigos BancoRendicionCodigo 
        on (
			BancoRendicionCodigo.banco_id = LiquidacionSocioRendicion.banco_intercambio
			and BancoRendicionCodigo.codigo = LiquidacionSocioRendicion.status
        )
        left join bancos Banco on Banco.id = LiquidacionSocioRendicion.banco_intercambio   
        
        left join proveedor_comisiones ProveedorComision on (
			ProveedorComision.proveedor_id = LiquidacionCuota.proveedor_id
            and ProveedorComision.codigo_organismo = Liquidacion.codigo_organismo
            and ProveedorComision.tipo = 'COB'
            and ProveedorComision.comision > 0
        )
        
        left join mutual_producto_solicitudes MutualProductoSolicitud
        on
        (
			MutualProductoSolicitud.id = OrdenDescuento.numero
            and 
            if(ifnull(MutualProductoSolicitud.reasignar_proveedor_id,0) = 0, MutualProductoSolicitud.proveedor_id,MutualProductoSolicitud.reasignar_proveedor_id) = OrdenDescuentoCuota.proveedor_id
        )
        
		where LiquidacionCuota.liquidacion_id = vLIQ
        AND IF(vSOCIO_ID IS NULL,TRUE,LiquidacionCuota.socio_id = vSOCIO_ID)
		and IF(vPROV IS NULL, TRUE, LiquidacionCuota.proveedor_id = vPROV)
        AND IF(vTPROD IS NULL, TRUE, LiquidacionCuota.tipo_producto = vTPROD)
        AND IF(vTCUOT IS NULL, TRUE, LiquidacionCuota.tipo_cuota = vTCUOT)	
        group by 
			LiquidacionCuota.socio_id
            ,LiquidacionCuota.orden_descuento_cuota_id
		order by 
        Proveedor.razon_social,
        Persona.apellido,Persona.nombre,
		LiquidacionCuota.orden_descuento_id,
		LiquidacionCuota.periodo_cuota;
        
/*
select 'repo' as tipo,count(*) cantidad,sum(saldo_actual) as saldo_actual, sum(importe_debitado) importe_debitado 
from tmp_reporte_proveedores
union
select 'tabla' as tipo,count(*) cantidad,sum(saldo_actual) as lcsa,sum(importe_debitado) lcid from liquidacion_cuotas 
where liquidacion_id = vLIQ;
-- and ifnull(mutual_adicional_pendiente_id,0) = 0;

select * from tmp_reporte_proveedores t
left join liquidacion_cuotas l on l.id = t.id
where l.id is null;

select cuota_id,proveedor_id,count(*) from tmp_reporte_proveedores group by cuota_id,proveedor_id having count(*) > 1;
*/
select * from tmp_reporte_proveedores;

DROP TEMPORARY TABLE IF EXISTS tmp_reporte_proveedores;          
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_REPORTE_PROVEEDOR_MORACUOTA`(
in vASINCID int(11),
in vCLAVE1 varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vLIQ int(11),
in vPROV Int(11),
in vTPROD varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vTCUOT varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vSOCIO_ID int(11),
in vCANTIDAD_CUOTAS INT(11)
)
BEGIN

SET vCANTIDAD_CUOTAS = IFNULL(vCANTIDAD_CUOTAS,1);

select 
	p.documento
	,p.apellido
	,p.nombre
	,p.telefono_fijo
	,p.telefono_movil
	,p.telefono_referencia                    
	,p.telefono_fijo_c
	,p.telefono_fijo_n
	,p.telefono_movil_c
	,p.telefono_movil_n
	,p.telefono_referencia_c
	,p.telefono_referencia_n                    
	,o.id
	,o.tipo_orden_dto
	,o.numero
	,pr.razon_social
	,pr.razon_social_resumida
	,tp.concepto_1
	,tc.concepto_1
	,e.concepto_1
	,c.periodo
	,c.nro_cuota
	,c.importe 
	,lc.saldo_actual
	,lc.importe_debitado
	,lc.saldo_actual-lc.importe_debitado AS saldo_cuota  
	,lsr.fecha_debito
	from liquidacion_cuotas lc
	inner join liquidacion_socio_rendiciones lsr on lsr.liquidacion_id = lc.liquidacion_id
	and lsr.socio_id = lc.socio_id                    
	inner join orden_descuentos o on o.id = lc.orden_descuento_id
	inner join liquidaciones l on l.id = lc.liquidacion_id
	inner join orden_descuento_cuotas c on c.id = lc.orden_descuento_cuota_id
	inner join global_datos tp on tp.id = c.tipo_producto
	inner join global_datos tc on tc.id = c.tipo_cuota
	inner join proveedores pr on pr.id = c.proveedor_id
	inner join persona_beneficios b on b.id = c.persona_beneficio_id
	inner join global_datos e on e.id = b.codigo_empresa
	inner join socios s on s.id = o.socio_id
	inner join personas p on p.id = s.persona_id
	where lc.liquidacion_id = vLIQ
	and o.permanente = 0
	and c.nro_cuota = vCANTIDAD_CUOTAS
        AND IF(vSOCIO_ID IS NULL,TRUE,c.socio_id = vSOCIO_ID)
        and IF(vPROV IS NULL, TRUE, c.proveedor_id = vPROV)
	and c.periodo = l.periodo
	and lc.importe_debitado < lc.saldo_actual
	group by lc.orden_descuento_id
	order by p.apellido,p.nombre,o.id,o.numero;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_REPORTE_PROVEEDOR_MORATEMPRANA`(
in vASINCID int(11),
in vCLAVE1 varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vLIQ int(11),
in vPROV Int(11),
in vTPROD varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vTCUOT varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vSOCIO_ID int(11)
)
BEGIN

select 
	p.documento
	,p.apellido
	,p.nombre
	,p.telefono_fijo
	,p.telefono_movil
	,p.telefono_referencia                    
	,p.telefono_fijo_c
	,p.telefono_fijo_n
	,p.telefono_movil_c
	,p.telefono_movil_n
	,p.telefono_referencia_c
	,p.telefono_referencia_n                    
	,o.id
	,o.tipo_orden_dto
	,o.numero
	,pr.razon_social
	,pr.razon_social_resumida
	,tp.concepto_1
	,tc.concepto_1
	,e.concepto_1
	,c.periodo
	,c.nro_cuota
	,c.importe
	,lc.saldo_actual
	,lc.importe_debitado
	,lc.saldo_actual-lc.importe_debitado AS saldo_cuota
	,
	ifnull((
	select sum(importe) from orden_descuento_cuotas c1
	where c1.orden_descuento_id = o.id and c1.periodo < c.periodo
	and c1.estado not in ('B','C')
	),0) as devengado
	,
	ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
	inner join orden_descuento_cuotas c2 on c2.id = cc.orden_descuento_cuota_id
	inner join orden_descuentos o1 on o1.id = c2.orden_descuento_id
	where o1.id = o.id and c2.periodo < l.periodo and cc.periodo_cobro < l.periodo
	),0) as pagado
	,
	ifnull((
	select sum(importe) from orden_descuento_cuotas c1
	where c1.orden_descuento_id = o.id and c1.periodo < c.periodo
	and c1.estado not in ('B','C')
	),0)
	-
	ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
	inner join orden_descuento_cuotas c2 on c2.id = cc.orden_descuento_cuota_id
	inner join orden_descuentos o1 on o1.id = c2.orden_descuento_id
	where o1.id = o.id and c2.periodo < l.periodo  and cc.periodo_cobro < l.periodo
	),0) as saldo
	,lsr.fecha_debito
	from liquidacion_cuotas lc
	inner join liquidacion_socio_rendiciones lsr on lsr.liquidacion_id = lc.liquidacion_id
	and lsr.socio_id = lc.socio_id                     
	inner join orden_descuentos o on o.id = lc.orden_descuento_id
	inner join liquidaciones l on l.id = lc.liquidacion_id
	inner join orden_descuento_cuotas c on c.id = lc.orden_descuento_cuota_id
	inner join global_datos tp on tp.id = c.tipo_producto
	inner join global_datos tc on tc.id = c.tipo_cuota
	inner join proveedores pr on pr.id = c.proveedor_id
	inner join persona_beneficios b on b.id = c.persona_beneficio_id
	inner join global_datos e on e.id = b.codigo_empresa
	inner join socios s on s.id = o.socio_id
	inner join personas p on p.id = s.persona_id
	where lc.liquidacion_id = vLIQ
	and o.permanente = 0
	and c.nro_cuota > 1
	and c.periodo = l.periodo
	AND IF(vSOCIO_ID IS NULL,TRUE,c.socio_id = vSOCIO_ID)
	and IF(vPROV IS NULL, TRUE, c.proveedor_id = vPROV)
	AND IF(vTPROD IS NULL, TRUE, c.tipo_producto = vTPROD)
	AND IF(vTCUOT IS NULL, TRUE, c.tipo_cuota = vTCUOT)		                    
	and lc.importe_debitado < lc.saldo_actual
	group by lc.orden_descuento_id
	having saldo = 0
	order by p.apellido,p.nombre;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_REPORTE_PROVEEDOR_REVERSO`(
in vASINCID int(11),
in vCLAVE1 varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vLIQ int(11),
in vPROV Int(11),
in vTPROD varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vTCUOT varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vSOCIO_ID int(11)
)
BEGIN

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQ;

DROP TEMPORARY TABLE IF EXISTS tmp_proveedores;
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_proveedores (INDEX IDX_1(liquidacion_id,codigo_organismo,proveedor_id,periodo)) as 
select liquidacion_id,Liquidacion.periodo,Liquidacion.codigo_organismo,proveedor_id from liquidacion_cuotas LiquidacionCuota
inner join liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
where LiquidacionCuota.liquidacion_id = vLIQ and IF(vPROV IS NULL, TRUE, LiquidacionCuota.proveedor_id = vPROV) group by proveedor_id;


DROP TEMPORARY TABLE IF EXISTS tmp_reporte_proveedores_reversos;            
CREATE TEMPORARY TABLE IF NOT EXISTS tmp_reporte_proveedores_reversos as

select 
	OrdenDescuentoCobroCuota.id
    ,Proveedor.razon_social
	,OrdenDescuentoCuota.socio_id as socio_id
	,OrdenDescuentoCobroCuota.orden_descuento_cobro_id
	,OrdenDescuentoCobroCuota.orden_descuento_cuota_id
    ,OrdenDescuento.id as orden_id
	,OrdenDescuentoCobroCuota.proveedor_id
	,PersonaBeneficio.codigo_beneficio 
	,concat(TipoDocumento.concepto_1,'-',Persona.documento) as tdoc_ndoc 
	,Persona.documento
	,Persona.cuit_cuil
	,concat(Persona.apellido,', ',Persona.nombre) socio  
	,concat(OrdenDescuento.tipo_orden_dto,'# ',OrdenDescuento.numero) as tipo_numero
	,concat(TipoProducto.concepto_1,'-',TipoCuota.concepto_1) as producto_concepto
	,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',lpad(OrdenDescuento.cuotas,2,0)) as cuota 
	,OrdenDescuentoCuota.periodo as periodo_cuota
    ,TipoCobro.concepto_1 as concepto_cobro
	,OrdenDescuentoCobroCuota.periodo_cobro
	,OrdenDescuentoCobroCuota.importe
	,OrdenDescuentoCobroCuota.alicuota_comision_cobranza
	,OrdenDescuentoCobroCuota.comision_cobranza    
    ,OrdenDescuentoCobroCuota.importe_reversado
from orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota
INNER JOIN orden_descuento_cobros AS OrdenDescuentoCobro on OrdenDescuentoCobro.id = OrdenDescuentoCobroCuota.orden_descuento_cobro_id
inner join tmp_proveedores t1 on (t1.proveedor_id = OrdenDescuentoCobroCuota.proveedor_id
						AND t1.periodo = OrdenDescuentoCobroCuota.periodo_proveedor_reverso)
inner join orden_descuento_cuotas OrdenDescuentoCuota on OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id    
inner join persona_beneficios as PersonaBeneficio on 
(
	PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id
    and PersonaBeneficio.codigo_beneficio = t1.codigo_organismo
)
inner join personas Persona on (Persona.id = PersonaBeneficio.persona_id)
inner join global_datos TipoDocumento on (TipoDocumento.id = Persona.tipo_documento)
inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
inner join global_datos TipoProducto on TipoProducto.id = OrdenDescuentoCuota.tipo_producto
inner join global_datos TipoCuota on TipoCuota.id = OrdenDescuentoCuota.tipo_cuota
inner join global_datos TipoCobro on TipoCobro.id = OrdenDescuentoCobro.tipo_cobro
left join cancelacion_ordenes CancelacionOrden on CancelacionOrden.id = OrdenDescuentoCobro.cancelacion_orden_id
left join global_datos FormaCancelacion on FormaCancelacion.id = CancelacionOrden.forma_cancelacion
inner join proveedores Proveedor on Proveedor.id = OrdenDescuentoCobroCuota.proveedor_id
where 
OrdenDescuentoCobroCuota.reversado = 1
-- and PersonaBeneficio.codigo_beneficio = @ORGANISMO
AND IF(@vSOCIO_ID IS NULL,TRUE,OrdenDescuentoCuota.socio_id = vSOCIO_ID)
AND IF(@vTPROD IS NULL, TRUE, OrdenDescuentoCuota.tipo_producto = vTPROD)
AND IF(@vTCUOT IS NULL, TRUE, OrdenDescuentoCuota.tipo_cuota = vTCUOT)  
group by OrdenDescuentoCobroCuota.orden_descuento_cuota_id
order by
        Proveedor.razon_social,
        Persona.apellido,Persona.nombre,
		OrdenDescuentoCuota.orden_descuento_id,
		OrdenDescuentoCuota.periodo;

/*
select 
	LiquidacionCuota.proveedor_id
	,t1.socio_id   
	,Proveedor.razon_social    
    ,t1.tdoc_ndoc
    ,t1.documento
    ,t1.cuit_cuil
    ,t1.socio
    ,t1.tipo_numero
    ,t1.producto_concepto
    ,t1.cuota
    ,t1.periodo
    ,t1.importe_reversado
    ,t1.alicuota_comision_cobranza
    ,t1.comision_cobranza
from liquidacion_cuotas LiquidacionCuota
inner join (
	select 
		OrdenDescuentoCobroCuota.id
        ,OrdenDescuentoCuota.socio_id as socio_id
        ,OrdenDescuentoCobroCuota.orden_descuento_cobro_id
		,OrdenDescuentoCobroCuota.orden_descuento_cuota_id
        ,OrdenDescuentoCobroCuota.proveedor_id
		,OrdenDescuentoCobroCuota.periodo_cobro
        ,OrdenDescuentoCobroCuota.importe_reversado
		,OrdenDescuentoCobroCuota.periodo_proveedor_reverso
        ,OrdenDescuentoCobroCuota.alicuota_comision_cobranza
        ,OrdenDescuentoCobroCuota.comision_cobranza
		,PersonaBeneficio.codigo_beneficio 
		,concat(TipoDocumento.concepto_1,'-',Persona.documento) as tdoc_ndoc 
        ,Persona.documento
        ,Persona.cuit_cuil
		,concat(Persona.apellido,', ',Persona.nombre) socio  
        ,concat(OrdenDescuento.tipo_orden_dto,'# ',OrdenDescuento.numero) as tipo_numero
        ,concat(TipoProducto.concepto_1,'-',TipoCuota.concepto_1) as producto_concepto
        ,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',lpad(OrdenDescuento.cuotas,2,0)) as cuota 
        ,OrdenDescuentoCuota.periodo
	from orden_descuento_cobro_cuotas OrdenDescuentoCobroCuota 
	inner join orden_descuento_cuotas OrdenDescuentoCuota on OrdenDescuentoCuota.id = OrdenDescuentoCobroCuota.orden_descuento_cuota_id
	inner join persona_beneficios as PersonaBeneficio on (OrdenDescuentoCuota.persona_beneficio_id = PersonaBeneficio.id)
    inner join personas Persona on (Persona.id = PersonaBeneficio.persona_id)
    inner join global_datos TipoDocumento on (TipoDocumento.id = Persona.tipo_documento)
    inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
	inner join global_datos TipoCuota on TipoCuota.id = OrdenDescuentoCuota.tipo_cuota
	inner join global_datos TipoProducto on TipoProducto.id = OrdenDescuentoCuota.tipo_producto
	where OrdenDescuentoCobroCuota.reversado = 1
	and OrdenDescuentoCobroCuota.periodo_proveedor_reverso = @PERIODO
	and PersonaBeneficio.codigo_beneficio = @ORGANISMO
	AND IF(vSOCIO_ID IS NULL,TRUE,OrdenDescuentoCuota.socio_id = vSOCIO_ID)
	and IF(vPROV IS NULL, TRUE, OrdenDescuentoCobroCuota.proveedor_id = vPROV)
	AND IF(vTPROD IS NULL, TRUE, OrdenDescuentoCuota.tipo_producto = vTPROD)
	AND IF(vTCUOT IS NULL, TRUE, OrdenDescuentoCuota.tipo_cuota = vTCUOT)    
) t1 
on (t1.proveedor_id = LiquidacionCuota.proveedor_id)
inner join proveedores Proveedor on Proveedor.id = LiquidacionCuota.proveedor_id
where LiquidacionCuota.liquidacion_id = vLIQ group by LiquidacionCuota.proveedor_id;
*/

-- select * from tmp_reporte_proveedores_reversos;

DROP TEMPORARY TABLE IF EXISTS tmp_reporte_proveedores_reversos;  
DROP TEMPORARY TABLE IF EXISTS tmp_proveedores;

END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE `SP_REPORTE_PROVEEDOR_STOPS`(
in vASINCID int(11),
in vCLAVE1 varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vLIQ int(11),
in vPROV Int(11),
in vTPROD varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vTCUOT varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci,
in vSOCIO_ID int(11)
)
BEGIN

END$$
DELIMITER ;

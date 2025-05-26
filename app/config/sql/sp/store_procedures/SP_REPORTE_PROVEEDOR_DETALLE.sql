DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REPORTE_PROVEEDOR_DETALLE`(
in vASINCID int(11),
in vCLAVE1 varchar(12),
in vLIQ int(11),
in vPROV Int(11),
in vTPROD varchar(12),
in vTCUOT varchar(12)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
declare vSocioId int(11);

DECLARE C_SOCIOS CURSOR FOR 
select 
LiquidacionCuota.socio_id
from liquidacion_cuotas LiquidacionCuota
inner join socios Socio on LiquidacionCuota.socio_id = Socio.id
inner join personas Persona on Socio.persona_id = Persona.id
-- inner join global_datos GlobalDato on GlobalDato.id = Persona.tipo_documento
-- inner join orden_descuentos OrdenDescuento on OrdenDescuento.id = LiquidacionCuota.orden_descuento_id
-- inner join orden_descuento_cuotas OrdenDescuentoCuota on OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id
where 
LiquidacionCuota.liquidacion_id = vLIQ
and LiquidacionCuota.proveedor_id = vPROV
and LiquidacionCuota.tipo_producto like concat('%', ifnull(vTPROD,'')  ,'%')
and  LiquidacionCuota.tipo_cuota like concat('%', ifnull(vTCUOT,'') ,'%')
group by LiquidacionCuota.socio_id
order by Persona.apellido,Persona.nombre;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;

delete from asincrono_temporales where asincrono_id = vASINCID and clave_1 =vCLAVE1;

OPEN C_SOCIOS;
c1_loop: LOOP
FETCH C_SOCIOS INTO vSocioId;
        IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;
        
		-- detalle del socio
        insert into asincrono_temporales(
        asincrono_id,
        clave_1,
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
        decimal_5,
        decimal_6,
        decimal_7,
        decimal_8,
        entero_1,
        entero_2,
        entero_3,
        entero_4,
        entero_5,
        entero_6
        )
		select 
        vASINCID
        ,vCLAVE1
		,concat(GlobalDato.concepto_1,'-',Persona.documento)  as texto_1
        ,Persona.documento as texto_2
        ,Persona.cuit_cuil as texto_3
		,concat(Persona.apellido,' ',Persona.nombre) as texto_4
        ,if(ifnull(Persona.sexo,'M') = '','M',Persona.sexo) as texto_5
		,concat(OrdenDescuento.tipo_orden_dto,'# ',OrdenDescuento.numero) as texto_6  
        ,concat(TipoProducto.concepto_1,'-',TipoCuota.concepto_1) as texto_7
        ,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,0),'/',OrdenDescuento.cuotas) as texto_8
        ,TipoCuota.concepto_1 as texto_9
        ,LiquidacionCuota.periodo_cuota as texto_10
        ,LiquidacionCuota.orden_descuento_cuota_id as texto_11
        ,OrdenDescuentoCuota.nro_referencia_proveedor as texto_12
		,ifnull((select Prov.razon_social from orden_descuentos od
		inner join global_datos TipoProd on (TipoProd.id = od.tipo_producto)
		inner join proveedores Prov on (Prov.id = od.proveedor_id)
		where od.numero = OrdenDescuento.numero and od.proveedor_id <> OrdenDescuento.proveedor_id 
		and od.socio_id = OrdenDescuento.socio_id and od.activo = 1
		and TipoProd.concepto_4 = OrdenDescuento.tipo_producto
		),'') as texto_13      
        ,LiquidacionCuota.saldo_actual as decimal_1
        ,LiquidacionCuota.importe_debitado as decimal_2
        ,LiquidacionCuota.saldo_actual - LiquidacionCuota.importe_debitado as decimal_3
        ,LiquidacionCuota.importe as decimal_4
        ,LiquidacionCuota.comision_cobranza as decimal_5
        ,LiquidacionCuota.alicuota_comision_cobranza as decimal_6
        ,ifnull(MutualProductoSolicitud.importe_solicitado,0) as decimal_7
        ,ifnull(MutualProductoSolicitud.importe_percibido,0) as decimal_8
        ,Liquidacion.periodo as entero_1
        ,Persona.id as entero_2
        ,LiquidacionCuota.socio_id as entero_3
        ,OrdenDescuento.numero as entero_4
        ,OrdenDescuentoCuota.nro_cuota as entero_5
        ,LiquidacionCuota.persona_beneficio_id as entero_6
        
		from liquidacion_cuotas LiquidacionCuota
        inner join  liquidaciones Liquidacion on Liquidacion.id = LiquidacionCuota.liquidacion_id
		inner join socios Socio on (Socio.id = LiquidacionCuota.socio_id)
		inner join personas Persona on (Persona.id = Socio.persona_id)
		inner join global_datos GlobalDato on (GlobalDato.id = Persona.tipo_documento)
		inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = LiquidacionCuota.orden_descuento_id)
		inner join orden_descuento_cuotas OrdenDescuentoCuota on (OrdenDescuentoCuota.id = LiquidacionCuota.orden_descuento_cuota_id)
        inner join global_datos TipoCuota on TipoCuota.id = LiquidacionCuota.tipo_cuota
        inner join global_datos TipoProducto on TipoProducto.id = LiquidacionCuota.tipo_producto
        left join mutual_producto_solicitudes MutualProductoSolicitud on 
        MutualProductoSolicitud.id = OrdenDescuento.numero and MutualProductoSolicitud.persona_id = Persona.id
        and MutualProductoSolicitud.tipo_producto = OrdenDescuento.tipo_producto
		where LiquidacionCuota.liquidacion_id = vLIQ
		and LiquidacionCuota.socio_id = vSocioId
		and LiquidacionCuota.proveedor_id = vPROV
		and LiquidacionCuota.tipo_producto like concat('%', ifnull(vTPROD,'')  ,'%')
		and LiquidacionCuota.tipo_cuota like concat('%', ifnull(vTCUOT,'') ,'%')
		order by Persona.apellido,Persona.nombre,
		LiquidacionCuota.orden_descuento_id,
		LiquidacionCuota.periodo_cuota;        
        

        
        
END LOOP c1_loop;
CLOSE C_SOCIOS;        
END$$
DELIMITER ;

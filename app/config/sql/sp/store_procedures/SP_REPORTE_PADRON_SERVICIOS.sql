DROP PROCEDURE IF EXISTS SP_REPORTE_PADRON_SERVICIOS;
-- 166
CALL SP_TMP_DESIMPUTA_LIQUIDACION(166);

select * from liquidacion_cuotas where liquidacion_id = 166
and importe_debitado > 0;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REPORTE_PADRON_SERVICIOS`(
IN 
	vPID INT(11),
    vSOCIO_ID INT(11),
    vSERVICIO_ID INT(11),
    vFECHA_COBERTURA DATE,
    vCUOTAS_SOCIALES_MINIMAS INT(11)
)
BEGIN
		
		INSERT INTO asincrono_temporales(asincrono_id,clave_1,entero_1,texto_1,texto_2,
		texto_3,texto_4,texto_5,texto_6,texto_7,texto_8,
		texto_9,texto_10,texto_11,texto_12,texto_13,texto_14,texto_15,texto_16,texto_17,decimal_1)
        
		select
			vPID,
			'REPORTE_1',
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			Persona.documento,
			concat(Persona.apellido,', ',Persona.nombre) as apenom,
			Persona.sexo,
			Persona.calle,
			Persona.numero_calle,
			Persona.piso,
			Persona.dpto,
			Persona.barrio,
			Persona.localidad,
			Persona.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitud.fecha_alta_servicio,'%d-%m-%Y') as fecha_alta_servicio,
			date_format(MutualServicioSolicitud.fecha_baja_servicio,'%d-%m-%Y') as fecha_baja_servicio,
			'TIT' as condicion,
			Persona.fecha_nacimiento,
            CORG.concepto_1,
			ifnull((select costo_titular from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1),0) as costo_titular
		from mutual_servicio_solicitudes MutualServicioSolicitud
		inner join personas Persona on (Persona.id = MutualServicioSolicitud.persona_id)
		inner join global_datos TDOC on (TDOC.id = Persona.tipo_documento)
		inner join provincias Provincia on (Provincia.id = Persona.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
        inner join global_datos CORG on (CORG.id = PersonaBeneficio.codigo_beneficio)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
        and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC' 
        and periodo >= date_format(date_sub(vFECHA_COBERTURA, INTERVAL (vCUOTAS_SOCIALES_MINIMAS) MONTH),'%Y%m')),0) > 0
        and MutualServicioSolicitud.fecha_baja_servicio IS NULL 
        GROUP BY Persona.documento
union
		(
select
			vPID,
            'REPORTE_2',
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			Persona.documento,
			concat(Persona.apellido,', ',Persona.nombre) as apenom,
			Persona.sexo,
			Persona.calle,
			Persona.numero_calle,
			Persona.piso,
			Persona.dpto,
			Persona.barrio,
			Persona.localidad,
			Persona.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitud.fecha_alta_servicio,'%d-%m-%Y') as fecha_alta_servicio,
			date_format(MutualServicioSolicitud.fecha_baja_servicio,'%d-%m-%Y') as fecha_baja_servicio,
			'TIT' as condicion,
			Persona.fecha_nacimiento,   
            CORG.concepto_1,
			ifnull((select costo_titular from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1),0) as costo_titular
		from mutual_servicio_solicitudes MutualServicioSolicitud
		inner join personas Persona on (Persona.id = MutualServicioSolicitud.persona_id)
		inner join global_datos TDOC on (TDOC.id = Persona.tipo_documento)
		inner join provincias Provincia on (Provincia.id = Persona.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
        inner join global_datos CORG on (CORG.id = PersonaBeneficio.codigo_beneficio)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
        and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
        and periodo >= date_format(date_sub(vFECHA_COBERTURA, INTERVAL (vCUOTAS_SOCIALES_MINIMAS) MONTH),'%Y%m')),0) > 0        
        and MutualServicioSolicitud.fecha_baja_servicio IS NOT NULL GROUP BY Persona.documento         
        )
union
		(select
			vPID,
			'REPORTE_1',
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			SocioAdicional.documento,
			concat(SocioAdicional.apellido,', ',SocioAdicional.nombre) as apenom,
			SocioAdicional.sexo,
			SocioAdicional.calle,
			SocioAdicional.numero_calle,
			SocioAdicional.piso,
			SocioAdicional.dpto,
			SocioAdicional.barrio,
			SocioAdicional.localidad,
			SocioAdicional.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitudAdicional.fecha_alta,'%d-%m-%Y') as fecha_alta,
			date_format(MutualServicioSolicitudAdicional.fecha_baja,'%d-%m-%Y') as fecha_baja,
			'ADI' as condicion,   
			SocioAdicional.fecha_nacimiento, 
            CORG.concepto_1,
			ifnull((select costo_adicional from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1),0) as costo_adicional
		from mutual_servicio_solicitud_adicionales MutualServicioSolicitudAdicional
		inner join mutual_servicio_solicitudes MutualServicioSolicitud on (MutualServicioSolicitud.id = MutualServicioSolicitudAdicional.mutual_servicio_solicitud_id)
		inner join socio_adicionales SocioAdicional on (SocioAdicional.id = MutualServicioSolicitudAdicional.socio_adicional_id)
		inner join global_datos TDOC on (TDOC.id = SocioAdicional.tipo_documento)
		inner join provincias Provincia on (Provincia.id = SocioAdicional.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
        inner join global_datos CORG on (CORG.id = PersonaBeneficio.codigo_beneficio)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and MutualServicioSolicitudAdicional.fecha_alta <= vFECHA_COBERTURA
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
        and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC' 
        and periodo >= date_format(date_sub(vFECHA_COBERTURA, INTERVAL (vCUOTAS_SOCIALES_MINIMAS) MONTH),'%Y%m')),0) > 0        
        and MutualServicioSolicitud.fecha_baja_servicio IS NULL GROUP BY SocioAdicional.documento
		order by SocioAdicional.apellido,SocioAdicional.nombre)
union 
(select
			vPID,
            'REPORTE_1',
			MutualServicioSolicitud.id,
			TDOC.concepto_1,
			SocioAdicional.documento,
			concat(SocioAdicional.apellido,', ',SocioAdicional.nombre) as apenom,
			SocioAdicional.sexo,
			SocioAdicional.calle,
			SocioAdicional.numero_calle,
			SocioAdicional.piso,
			SocioAdicional.dpto,
			SocioAdicional.barrio,
			SocioAdicional.localidad,
			SocioAdicional.codigo_postal,
			Provincia.nombre,
			date_format(MutualServicioSolicitudAdicional.fecha_alta,'%d-%m-%Y') as fecha_alta,
			date_format(MutualServicioSolicitudAdicional.fecha_baja,'%d-%m-%Y') as fecha_baja,
			'ADI' as condicion,   
			SocioAdicional.fecha_nacimiento,  
            CORG.concepto_1,
			ifnull((select costo_adicional from mutual_servicio_valores v where
			v.mutual_servicio_id = MutualServicioSolicitud.mutual_servicio_id
			and v.periodo_vigencia <= date_format(now(),'%Y%m')
			and v.codigo_organismo = PersonaBeneficio.codigo_beneficio
			order by v.periodo_vigencia desc limit 1),0) as costo_adicional
		from mutual_servicio_solicitud_adicionales MutualServicioSolicitudAdicional
		inner join mutual_servicio_solicitudes MutualServicioSolicitud on (MutualServicioSolicitud.id = MutualServicioSolicitudAdicional.mutual_servicio_solicitud_id)
		inner join socio_adicionales SocioAdicional on (SocioAdicional.id = MutualServicioSolicitudAdicional.socio_adicional_id)
		inner join global_datos TDOC on (TDOC.id = SocioAdicional.tipo_documento)
		inner join provincias Provincia on (Provincia.id = SocioAdicional.provincia_id)
		inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = MutualServicioSolicitud.persona_beneficio_id)
        inner join global_datos CORG on (CORG.id = PersonaBeneficio.codigo_beneficio)
		where
		MutualServicioSolicitud.socio_id = vSOCIO_ID
		and MutualServicioSolicitud.mutual_servicio_id = vSERVICIO_ID
		and MutualServicioSolicitudAdicional.fecha_alta <= vFECHA_COBERTURA
		and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC'
		and importe > (select ifnull(sum(importe),0) from orden_descuento_cobro_cuotas 
		where orden_descuento_cobro_cuotas.orden_descuento_cuota_id = orden_descuento_cuotas.id)),0) <= vCUOTAS_SOCIALES_MINIMAS
        and ifnull((select count(*) from orden_descuento_cuotas where socio_id = vSOCIO_ID
		and proveedor_id = 18 and tipo_cuota = 'MUTUTCUOCSOC' 
        and periodo >= date_format(date_sub(vFECHA_COBERTURA, INTERVAL (vCUOTAS_SOCIALES_MINIMAS) MONTH),'%Y%m')),0) > 0        
        and MutualServicioSolicitud.fecha_baja_servicio IS NOT NULL GROUP BY SocioAdicional.documento
		order by SocioAdicional.apellido,SocioAdicional.nombre);              
        
 
END$$
DELIMITER ;

select * from liquidacion_socio_rendiciones where liquidacion_id = 166
and ifnull(orden_descuento_cobro_id,0) <> 0;



DROP PROCEDURE `ipnorte_descontar`.`SP_LIQUIDA_GTO_BANCARIOS`;
DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_LIQUIDA_GTO_BANCARIOS`(
vLIQUIDACION_ID INT(11),
IN vSOCIO_ID INT(11)
)
BEGIN

SET @ORGANISMO = NULL;
SET @PERIODO = NULL;
SET @PROVEEDOR = NULL;
SET @VALOR = NULL;
SET @TIPOCUOTA = NULL;
SET @DEVENGADO = 0;

select codigo_organismo,periodo into @ORGANISMO,@PERIODO from liquidaciones where id = vLIQUIDACION_ID;

select imputar_proveedor_id,
valor,tipo_cuota,devengado_previo into @PROVEEDOR,@VALOR,@TIPOCUOTA,@DEVENGADO
from mutual_adicionales
where codigo_organismo = @ORGANISMO and valor > 0
AND deuda_calcula = 6 and activo = 1
and ifnull(periodo_desde,'000000') <= @PERIODO
and ifnull(periodo_hasta,'999912') >= @PERIODO
order by created desc limit 1;

SET @VALOR = IFNULL(@VALOR,0);

delete l.* from liquidacion_cuotas l, mutual_adicional_pendientes a 
where l.mutual_adicional_pendiente_id = a.id and l.liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,l.socio_id = vSOCIO_ID) and a.deuda_calcula = 6;

DELETE FROM mutual_adicional_pendientes where liquidacion_id = vLIQUIDACION_ID
AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID) and deuda_calcula = 6;

SET @DEVENGADO_CUOTAID = NULL;
IF @DEVENGADO = 1 THEN
    SELECT id into @DEVENGADO_CUOTAID FROM orden_descuento_cuotas where socio_id = vSOCIO_ID and tipo_cuota = @TIPOCUOTA
    and periodo = @PERIODO and estado not in ('B');
END IF;

IF @VALOR <> 0 THEN

	insert into mutual_adicional_pendientes(liquidacion_id,socio_id,codigo_organismo,
	proveedor_id,
	tipo,deuda_calcula,valor,tipo_cuota,periodo,total_deuda,importe,
	orden_descuento_id,persona_beneficio_id)

	select liquidacion_id,socio_id,@ORGANISMO,@PROVEEDOR,'I',6,round(@VALOR,2),@TIPOCUOTA
	,@PERIODO,count(*),round(count(*) * @VALOR,2),NULL,persona_beneficio_id from 
	liquidacion_socios where liquidacion_id = vLIQUIDACION_ID
	AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID)
	group by socio_id;


	insert into liquidacion_cuotas(liquidacion_id,socio_id,persona_beneficio_id,orden_descuento_id,
			orden_descuento_cuota_id,tipo_orden_dto,tipo_producto,tipo_cuota,
			periodo_cuota,proveedor_id,vencida,importe,saldo_actual
			,codigo_organismo,
			mutual_adicional_pendiente_id
	)
	SELECT a.liquidacion_id,a.socio_id,a.persona_beneficio_id,o.id,NULL,o.tipo_orden_dto,
	o.tipo_producto,a.tipo_cuota,a.periodo,a.proveedor_id,0,a.importe,a.importe,
	a.codigo_organismo,a.id
	FROM mutual_adicional_pendientes a
	left join orden_descuentos o on o.socio_id = a.socio_id and o.proveedor_id = ifnull(a.proveedor_id,18)
	and ifnull(o.nueva_orden_descuento_id,0) = 0 
	and o.tipo_orden_dto = 'CMUTU'
	where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,a.socio_id = vSOCIO_ID)
	and a.deuda_calcula = 6
	group by a.id; 

	IF @DEVENGADO = 1 THEN 

		IF @DEVENGADO_CUOTAID IS NULL THEN

			insert into orden_descuento_cuotas(orden_descuento_id,socio_id,persona_beneficio_id,tipo_orden_dto,
			tipo_producto,tipo_cuota,periodo,estado,situacion,vencimiento,vencimiento_proveedor,nro_cuota,importe,
			proveedor_id)
			SELECT o.id,a.socio_id,a.persona_beneficio_id,o.tipo_orden_dto,
			o.tipo_producto,a.tipo_cuota,a.periodo,'A','MUTUSICUMUTU', now(), now(),0,a.importe,a.proveedor_id
			FROM mutual_adicional_pendientes a
			left join orden_descuentos o on o.socio_id = a.socio_id and o.proveedor_id = ifnull(a.proveedor_id,18)
			and ifnull(o.nueva_orden_descuento_id,0) = 0 
			and o.tipo_orden_dto = 'CMUTU'
			where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,a.socio_id = vSOCIO_ID)
			and a.deuda_calcula = 6
			group by a.id;   
        
		ELSE 
    
			SELECT a.importe into @IMPOGTOBCO
			FROM mutual_adicional_pendientes a
			left join orden_descuentos o on o.socio_id = a.socio_id and o.proveedor_id = ifnull(a.proveedor_id,18)
			and ifnull(o.nueva_orden_descuento_id,0) = 0 
			and o.tipo_orden_dto = 'CMUTU'
			where liquidacion_id = vLIQUIDACION_ID AND IF(vSOCIO_ID IS NULL,TRUE,a.socio_id = vSOCIO_ID)
			and a.deuda_calcula = 6
			group by a.id;  
    
			update orden_descuento_cuotas set importe = @IMPOGTOBCO where id = @DEVENGADO_CUOTAID;
    
		END IF;

	END IF;

	UPDATE liquidacion_socios 
	set importe_dto = importe_dto + @VALOR,importe_adebitar = importe_adebitar + @VALOR
	where liquidacion_id = vLIQUIDACION_ID
	AND IF(vSOCIO_ID IS NULL,TRUE,socio_id = vSOCIO_ID);

END IF;

END$$
DELIMITER ;

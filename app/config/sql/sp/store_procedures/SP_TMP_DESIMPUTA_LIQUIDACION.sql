DROP PROCEDURE IF EXISTS SP_TMP_DESIMPUTA_LIQUIDACION;
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_TMP_DESIMPUTA_LIQUIDACION`(
IN vLIQUIDACION_ID INT(11)
)
BEGIN
DECLARE l_last_row INT DEFAULT 0;
DECLARE nReintegros INT(11);
DECLARE vCOBRO_ID INT(11);
DECLARE reintegros_abonados CONDITION FOR SQLSTATE '45000';


DECLARE C_CUOTAS CURSOR FOR 

select orden_descuento_cobro_id 
from liquidacion_cuotas where liquidacion_id = vLIQUIDACION_ID
and imputada = 1 and ifnull(orden_descuento_cobro_id,0) <> 0 
GROUP BY orden_descuento_cobro_id;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_last_row=1;
DECLARE CONTINUE HANDLER FOR reintegros_abonados RESIGNAL SET MESSAGE_TEXT = '*** REINTEGROS ABONADOS ***';
DECLARE exit handler for sqlexception
BEGIN 
    ROLLBACK;
END;
DECLARE exit handler for sqlwarning
BEGIN 
    ROLLBACK;
END;

-- CONTROLAR SI NO TIENE REINTEGROS ABONADOS
set nReintegros = 0;

select count(*) into nReintegros from socio_reintegros r
inner join orden_pago_detalles opd on opd.socio_reintegro_id = r.id
where r.liquidacion_id = vLIQUIDACION_ID and r.anticipado = 0;

if nReintegros <> 0 then
    signal reintegros_abonados;
end if;

START TRANSACTION;

OPEN C_CUOTAS;
c1_loop: LOOP
FETCH C_CUOTAS INTO vCOBRO_ID;

        IF (l_last_row = 1) THEN
			LEAVE c1_loop; 
		END IF;	
        
        
		-- -----------------------------------------------------------------
		-- BORRO LOS ADICIONALES DEVENGADOS
        -- -----------------------------------------------------------------
        
        SET FOREIGN_KEY_CHECKS = 0;
        
        DELETE cu.* FROM orden_descuento_cuotas cu
        inner join liquidacion_cuotas lc on (lc.liquidacion_id = vLIQUIDACION_ID 
        and lc.orden_descuento_cuota_id = cu.id)
        inner join orden_descuento_cobro_cuotas co on (co.orden_descuento_cobro_id = vCOBRO_ID and co.orden_descuento_cuota_id = cu.id)
        where ifnull(lc.mutual_adicional_pendiente_id,0) <> 0;

        update liquidacion_cuotas set orden_descuento_cuota_id = null
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID
        and ifnull(mutual_adicional_pendiente_id,0) <> 0;           

        /*
        -- ANULO LOS GASTOS DE GESTION EMITIDOS Y COBRADOS
        update orden_descuento_cuotas cu, liquidacion_cuotas lc,orden_descuento_cobro_cuotas co
        set cu.estado = 'B'
        where 
        lc.liquidacion_id = vLIQUIDACION_ID and lc.orden_descuento_cuota_id = cu.id
        and co.orden_descuento_cobro_id = vCOBRO_ID and co.orden_descuento_cuota_id = cu.id
        and ifnull(lc.mutual_adicional_pendiente_id,0) <> 0;  
        */
        
        update liquidacion_cuotas set imputada = 0, orden_descuento_cobro_id = 0
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID
        and ifnull(mutual_adicional_pendiente_id,0) <> 0;          
        
		-- -----------------------------------------------------------------
        -- MARCO LAS CUOTAS COMO NO IMPUTADAS
        -- -----------------------------------------------------------------
        update liquidacion_cuotas set imputada = 0, orden_descuento_cobro_id = 0
        where liquidacion_id = vLIQUIDACION_ID and orden_descuento_cobro_id = vCOBRO_ID;

		-- -----------------------------------------------------------------
        -- BORRO EL DETALLE DEL COBRO Y ANULO LA CABECERA
        -- -----------------------------------------------------------------
		DELETE FROM orden_descuento_cobro_cuotas where orden_descuento_cobro_id = vCOBRO_ID;

        update orden_descuento_cobros set anulado = 1 where id = vCOBRO_ID;
        
        SET FOREIGN_KEY_CHECKS = 1;
        
        
        
END LOOP c1_loop;
CLOSE C_CUOTAS;


-- -----------------------------------------------------------------
-- SACO LA MARCA DEL COBRO EN LA SOCIO RENDICIONES
-- -----------------------------------------------------------------

update liquidacion_socio_rendiciones 
set orden_descuento_cobro_id = 0 where liquidacion_id = vLIQUIDACION_ID
and ifnull(orden_descuento_cobro_id,0) <> 0;


-- -----------------------------------------------------------------
-- BORRO LOS REINTEGROS
-- -----------------------------------------------------------------
delete from socio_reintegros where liquidacion_id = vLIQUIDACION_ID and anticipado = 0;

-- -----------------------------------------------------------------
-- MARCO LOS ADICIONALES PENDIENTES
-- -----------------------------------------------------------------

update mutual_adicional_pendientes 
set procesado = 0, orden_descuento_cuota_id = null
where liquidacion_id = vLIQUIDACION_ID
and procesado = 1 and ifnull(orden_descuento_cuota_id,0) <> 0;

-- -----------------------------------------------------------------
-- ACTUALIZO LA CABECERA DE LA LIQUIDACION
-- -----------------------------------------------------------------
update liquidaciones set imputada = 0 where id = vLIQUIDACION_ID;

COMMIT;

END$$
DELIMITER ;

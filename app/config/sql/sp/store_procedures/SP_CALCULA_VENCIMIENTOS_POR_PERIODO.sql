CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `SP_CALCULA_VENCIMIENTOS_POR_PERIODO`(
IN vPERSONA_BENEFICIO_ID INT(11),
IN vPROVEEDOR_ID INT(11),
IN vCODIGO_ORGANISMO VARCHAR(12),
IN vPERIODO VARCHAR(6),
IN vFECHA DATE,
OUT vPERIODO_INI VARCHAR(6),
OUT vVENCIMIENTO_SOCIO DATE,
OUT vVENCIMIENTO_PROVEEDOR DATE,
OUT vULTIMO_PERIODO_LIQUIDADO VARCHAR(6)
)
BEGIN



IF vPERSONA_BENEFICIO_ID IS NOT NULL THEN
	SELECT codigo_beneficio into vCODIGO_ORGANISMO from persona_beneficios where id = vPERSONA_BENEFICIO_ID;
END IF;

SELECT periodo into vULTIMO_PERIODO_LIQUIDADO FROM liquidaciones 
where codigo_organismo = vCODIGO_ORGANISMO
and cerrada = 1 order by periodo desc limit 1;

select d_corte, d_vto_socio, d_vto_proveedor_suma, mes, 
m_ini_socio_ac_suma, m_ini_socio_dc_suma, m_vto_socio_suma 
INTO @d_corte, @d_vto_socio, @d_vto_proveedor_suma, @mes, 
@m_ini_socio_ac_suma, @m_ini_socio_dc_suma, @m_vto_socio_suma 
from proveedor_vencimientos 
where proveedor_id = vPROVEEDOR_ID and codigo_organismo = vCODIGO_ORGANISMO
and mes = cast(trim(substring(vPERIODO,5,2)) as char(2));

IF @d_corte IS NULL THEN
select d_corte INTO @d_corte from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

IF @m_ini_socio_dc_suma IS NULL THEN
select m_ini_socio_dc_suma INTO @m_ini_socio_dc_suma from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

IF @m_ini_socio_ac_suma IS NULL THEN
select m_ini_socio_ac_suma INTO @m_ini_socio_ac_suma from proveedor_vencimientos where proveedor_id = 18 and codigo_organismo = 'MUTUCORG2201'and mes = '01';
END IF;

SET @PERIODO_INICIO = DATE_ADD(vFECHA, INTERVAL IF(DAY(vFECHA) <= @d_corte,@m_ini_socio_ac_suma,@m_ini_socio_dc_suma) MONTH);
SET vPERIODO_INI = DATE_FORMAT(@PERIODO_INICIO,'%Y%m');

IF vPERIODO IS NULL THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(STR_TO_DATE(CONCAT(DATE_FORMAT(@PERIODO_INICIO,'%Y-%m'),'-',@d_vto_socio),'%Y-%m-%d'),INTERVAL @m_vto_socio_suma MONTH);
ELSE 
	SET vVENCIMIENTO_SOCIO = STR_TO_DATE(CONCAT(vPERIODO,@d_vto_socio),'%Y%m%d'); 
    SET vPERIODO_INI = NULL; 
END IF;
IF DATE_FORMAT(vVENCIMIENTO_SOCIO,'%w') = 6 THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL 2 DAY);
END IF;
IF DATE_FORMAT(vVENCIMIENTO_SOCIO,'%w') = 0 THEN SET vVENCIMIENTO_SOCIO = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL 1 DAY);
END IF;

SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_SOCIO, INTERVAL @d_vto_proveedor_suma DAY);

IF DATE_FORMAT(vVENCIMIENTO_PROVEEDOR,'%w') = 6 THEN SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_PROVEEDOR, INTERVAL 2 DAY);
END IF;
IF DATE_FORMAT(vVENCIMIENTO_PROVEEDOR,'%w') = 0 THEN SET vVENCIMIENTO_PROVEEDOR = DATE_ADD(vVENCIMIENTO_PROVEEDOR, INTERVAL 1 DAY);
END IF;

END
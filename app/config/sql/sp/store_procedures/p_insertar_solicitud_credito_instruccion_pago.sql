CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_instruccion_pago`(
	in vSOLICITUD_ID INT(11),
	vORDEN varchar(255),
	vCONCEPTO text,
	vIMPORTE decimal(10,2)
    )
BEGIN
	SET vORDEN = IFNULL(vORDEN,'A MI ORDEN PERSONAL');
	SET vCONCEPTO = IFNULL(vCONCEPTO,'LIQUIDACION PRESTAMO');
	INSERT INTO mutual_producto_solicitud_instruccion_pagos(mutual_producto_solicitud_id,
	a_la_orden_de,concepto,importe)
	values(vSOLICITUD_ID,vORDEN,vCONCEPTO,vIMPORTE);
    END
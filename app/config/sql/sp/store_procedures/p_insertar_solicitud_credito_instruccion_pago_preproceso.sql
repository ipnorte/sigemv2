CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_instruccion_pago_preproceso`(
	in vUUID VARCHAR(100),
	vORDEN varchar(255),
	vCONCEPTO text,
	vIMPORTE decimal(10,2)
    )
BEGIN
	SET vORDEN = IFNULL(vORDEN,'A MI ORDEN PERSONAL');
	SET vCONCEPTO = IFNULL(vCONCEPTO,'LIQUIDACION PRESTAMO');
	INSERT INTO mutual_producto_solicitud_preproceso(uuid_identificador,tipo,
	a_la_orden_de,concepto,importe)
	values(vUUID,1,vORDEN,vCONCEPTO,vIMPORTE);
    END
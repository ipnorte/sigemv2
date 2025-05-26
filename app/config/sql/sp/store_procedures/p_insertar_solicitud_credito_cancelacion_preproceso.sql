CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_cancelacion_preproceso`(
	in vUUID VARCHAR(100),
	vCANCELACION_ID INT(11)
    )
BEGIN
	INSERT INTO mutual_producto_solicitud_preproceso(uuid_identificador,tipo,cancelacion_id)
	VALUES(vUUID,2,vCANCELACION_ID);
    END
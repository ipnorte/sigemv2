CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_cancelacion`(
	in vSOLICITUD_ID INT(11),
	vCANCELACION_ID INT(11)
    )
BEGIN
	INSERT INTO mutual_producto_solicitud_cancelaciones(mutual_producto_solicitud_id,cancelacion_orden_id)
	VALUES(vSOLICITUD_ID,vCANCELACION_ID);
    END
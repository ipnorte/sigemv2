CREATE DEFINER=`root`@`localhost` PROCEDURE `p_marcar_solicitud_notificacion_leida`(
	in vID INT(11)
    )
BEGIN
	update mutual_producto_solicitudes SET vendedor_notificar = 0 where id = vID;
    END
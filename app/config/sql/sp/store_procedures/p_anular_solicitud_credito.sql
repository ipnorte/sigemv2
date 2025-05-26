CREATE DEFINER=`root`@`localhost` PROCEDURE `p_anular_solicitud_credito`(
	in vID INT(11)
    )
BEGIN
    
	UPDATE mutual_producto_solicitudes
	set anulada = 1, estado = 'MUTUESTA0000'
	where
		id = vID
		and aprobada = 0 and estado = 'MUTUESTA0001';
		
	DELETE FROM mutual_producto_solicitud_instruccion_pagos WHERE mutual_producto_solicitud_id = vID;
	DELETE FROM mutual_producto_solicitud_cancelaciones WHERE mutual_producto_solicitud_id = vID;	
	delete from mutual_producto_solicitud_pagos WHERE mutual_producto_solicitud_id = vID;
    END
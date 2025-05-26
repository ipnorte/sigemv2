CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_documento`(
	IN vSOLICITUD_ID INT (11),
	vFILE_NAME VARCHAR(100),
	vFILE_TYPE VARCHAR(100),
	vFILE_DATA LONGBLOB 
)
BEGIN
INSERT INTO `mutual_producto_solicitud_documentos` 
	(`mutual_producto_solicitud_id`,`file_name`, `file_type`, `file_data`)
	VALUES (vSOLICITUD_ID,vFILE_NAME,vFILE_TYPE,vFILE_DATA);
END
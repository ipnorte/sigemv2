CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_solicitud_credito_documento_preproceso`(
	in vUUID VARCHAR(100),
	vFILE_NAME VARCHAR(100),
	vFILE_TYPE VARCHAR(100),
	vFILE_DATA LONGBLOB 
)
BEGIN
INSERT INTO `mutual_producto_solicitud_preproceso` 
	(uuid_identificador,tipo,`file_name`, `file_type`, `file_data`)
	VALUES (vUUID,3,vFILE_NAME,vFILE_TYPE,vFILE_DATA);
END
CREATE DEFINER=`root`@`localhost` PROCEDURE `p_insertar_asincrono`(
	vPROPIETARIO varchar(120),
	vREMOTE_IP varchar(100),
	vPROCESO VARCHAR(150),
	vTITULO VARCHAR(250),
	vSUB_TITULO VARCHAR(250),
	vP1 VARCHAR(250),
	vP2 VARCHAR(250),
	vP3 VARCHAR(250),
	vP4 VARCHAR(250),
	vP5 VARCHAR(250)
)
BEGIN
    SET @ID = 0;
    INSERT INTO asincronos(propietario,remote_ip,proceso,titulo,subtitulo,p1,p2,p3,p4,p5)
    values(vPROPIETARIO,vREMOTE_IP,vPROCESO,vTITULO,vSUB_TITULO,vP1,vP2,vP3,vP4,vP5);
    SELECT * from v_asincronos where ID = LAST_INSERT_ID();
END
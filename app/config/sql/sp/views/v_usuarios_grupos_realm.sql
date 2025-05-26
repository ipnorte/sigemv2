CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_usuarios_grupos_realm` AS
    SELECT 
        `v_usuarios`.`ID` AS `ID`,
        `v_usuarios`.`GRUPO_ID` AS `GRUPO_ID`,
        `v_usuarios`.`USUARIO` AS `USUARIO`,
        `v_usuarios`.`PASSWORD` AS `PASSWORD`,
        `v_usuarios`.`ACTIVO` AS `ACTIVO`,
        `v_usuarios`.`DESCRIPCION` AS `DESCRIPCION`,
        `v_usuarios`.`VENDEDOR_ID` AS `VENDEDOR_ID`,
        SUBSTR(`v_grupos`.`NOMBRE`, 1, 5) AS `ROL`
    FROM
        (`v_usuarios`
        JOIN `v_grupos` ON ((`v_grupos`.`ID` = `v_usuarios`.`GRUPO_ID`)))
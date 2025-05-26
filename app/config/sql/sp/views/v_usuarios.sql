CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_usuarios` AS
    (SELECT 
        `usuarios`.`id` AS `ID`,
        `usuarios`.`grupo_id` AS `GRUPO_ID`,
        `usuarios`.`usuario` AS `USUARIO`,
        `usuarios`.`password` AS `PASSWORD`,
        `usuarios`.`activo` AS `ACTIVO`,
        `usuarios`.`descripcion` AS `DESCRIPCION`,
        `usuarios`.`vendedor_id` AS `VENDEDOR_ID`
    FROM
        `usuarios`)
CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_asincronos` AS
    (SELECT 
        `asincronos`.`id` AS `ID`,
        `asincronos`.`shell_pid` AS `SHELL_PID`,
        `asincronos`.`propietario` AS `PROPIETARIO`,
        `asincronos`.`remote_ip` AS `REMOTE_IP`,
        `asincronos`.`final` AS `FINAL`,
        `asincronos`.`proceso` AS `PROCESO`,
        `asincronos`.`bloqueado` AS `BLOQUEADO`,
        `asincronos`.`p1` AS `P1`,
        `asincronos`.`p2` AS `P2`,
        `asincronos`.`p3` AS `P3`,
        `asincronos`.`p4` AS `P4`,
        `asincronos`.`p5` AS `P5`,
        `asincronos`.`p6` AS `P6`,
        `asincronos`.`p7` AS `P7`,
        `asincronos`.`p8` AS `P8`,
        `asincronos`.`p9` AS `P9`,
        `asincronos`.`p10` AS `P10`,
        `asincronos`.`p11` AS `P11`,
        `asincronos`.`p12` AS `P12`,
        `asincronos`.`p13` AS `P13`,
        `asincronos`.`txt1` AS `TXT1`,
        `asincronos`.`txt2` AS `TXT2`,
        `asincronos`.`action_do` AS `ACTION_DO`,
        `asincronos`.`target` AS `TARGET`,
        `asincronos`.`btn_label` AS `BTN_LABEL`,
        `asincronos`.`titulo` AS `TITULO`,
        `asincronos`.`subtitulo` AS `SUB_TITULO`,
        `asincronos`.`estado` AS `ESTADO`,
        `asincronos`.`total` AS `TOTAL`,
        `asincronos`.`contador` AS `CONTADOR`,
        `asincronos`.`porcentaje` AS `PORCENTAJE`,
        `asincronos`.`msg` AS `MSG`,
        `asincronos`.`errores` AS `ERRORES`,
        `asincronos`.`created` AS `CREATED`,
        `asincronos`.`modified` AS `MODIFIED`
    FROM
        `asincronos`)
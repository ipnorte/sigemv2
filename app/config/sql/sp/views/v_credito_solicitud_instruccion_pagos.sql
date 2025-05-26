CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `v_credito_solicitud_instruccion_pagos` AS
    (SELECT 
        `mutual_producto_solicitud_instruccion_pagos`.`id` AS `ID`,
        `mutual_producto_solicitud_instruccion_pagos`.`mutual_producto_solicitud_id` AS `MUTUAL_PRODUCTO_SOLICITUD_ID`,
        `mutual_producto_solicitud_instruccion_pagos`.`a_la_orden_de` AS `A_LA_ORDEN_DE`,
        `mutual_producto_solicitud_instruccion_pagos`.`concepto` AS `CONCEPTO`,
        `mutual_producto_solicitud_instruccion_pagos`.`importe` AS `IMPORTE`
    FROM
        `mutual_producto_solicitud_instruccion_pagos`)
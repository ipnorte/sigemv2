CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` 
SQL SECURITY DEFINER VIEW `v_plan_montos` 
AS (select `proveedor_plan_grilla_cuotas`.`id` AS `ID`,`proveedor_planes`.`id` 
AS `PLAN_ID`,`proveedor_plan_grillas`.`vigencia_desde` 
AS `VIGENCIA`,`proveedor_plan_grilla_cuotas`.`liquido` 
AS `LIQUIDO` from ((`proveedor_planes` join `proveedor_plan_grillas`) 
join `proveedor_plan_grilla_cuotas`)
 where ((`proveedor_planes`.`id` = `proveedor_plan_grillas`.`proveedor_plan_id`)
 and (`proveedor_plan_grilla_cuotas`.`liquido` <= `FX_PROVEEDOR_PRESTAMO_SALDO_DISPONIBLE`(`proveedor_planes`.`proveedor_id`)) 
and (`proveedor_plan_grillas`.`id` = (select `grillas`.`id` from `proveedor_plan_grillas` `grillas` 
where ((`grillas`.`proveedor_plan_id` = `proveedor_planes`.`id`) and (`grillas`.`vigencia_desde` <= curdate())) 
order by `grillas`.`vigencia_desde` desc limit 1)) and (`proveedor_plan_grilla_cuotas`.`proveedor_plan_grilla_id` = `proveedor_plan_grillas`.`id`)) 
group by `proveedor_planes`.`id`,`proveedor_plan_grillas`.`vigencia_desde`,`proveedor_plan_grilla_cuotas`.`liquido` 
order by `proveedor_planes`.`id`,`proveedor_plan_grilla_cuotas`.`liquido`);

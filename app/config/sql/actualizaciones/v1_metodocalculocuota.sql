
INSERT INTO `mutualam_sigemdb`.`permisos` (`id`, `descripcion`, `url`, `order`, `main`, `quick`, `icon`, `activo`, `parent`) 
VALUES ('351', 'Actualizar Grillas', '/v1/proveedores', '350', '1', '', 'arrow_right2.gif', '1', '300');

insert into mutualam_sigemdb.grupos_permisos values (1,351);


USE mutualam_amandb;

ALTER TABLE `proveedores` ADD COLUMN `direccion_pagare` VARCHAR(100) NULL;

ALTER TABLE `proveedores_productos` 
	ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `gasto_admin` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `sellado` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `metodo_calculo` INT NULL,
	ADD COLUMN `tipo_cuota_gasto_admin` VARCHAR(12) NULL,
	ADD COLUMN `tipo_cuota_sellado` VARCHAR(12) NULL,
	ADD COLUMN `gasto_admin_base_calculo` INT NULL,
	ADD COLUMN `sellado_base_calculo` INT NULL,
	ADD COLUMN `interes_moratorio` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `costo_cancelacion_anticipada` DECIMAL(10,2) NULL DEFAULT 0;


ALTER TABLE `proveedores_productos_cuotas` 
	ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `metodo_calculo` INT(11) NULL DEFAULT 0,
	ADD COLUMN `capital_puro` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `gasto_admin` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `sellado` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `calculo` LONGTEXT NULL DEFAULT NULL ;


ALTER TABLE `solicitudes` 
	ADD COLUMN `tna` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `tem` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `metodo_calculo` INT(11) NULL DEFAULT 1,
	ADD COLUMN `iva_porc` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `gasto_admin_porc` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `sellado_porc` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `capital_puro` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `interes` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `iva` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `gasto_admin` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `sellado` DECIMAL(10,2) NULL DEFAULT 0,
	ADD COLUMN `detalle_calculo_plan` LONGTEXT NULL;


DROP PROCEDURE IF EXISTS SP_COPIAR_CUOTAS;

DELIMITER $$
CREATE DEFINER=CURRENT_USER PROCEDURE `SP_COPIAR_CUOTAS`(
vCUITPROVEEDOR VARCHAR(11),
vCODIGOPRODUCTO VARCHAR(5),
vPLAN INT
)
BEGIN

DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN    
        ROLLBACK;
    END;
    
start transaction;

-- ACTUALIZAR EL DOMICILIO DEL PAGARE EN EL PROVEEDOR
select pr.id,pr.direccion_pagare into @IDPROV2,@DIRECCIONPAGARE from mutualam_sigemdb.proveedores pr
inner join mutualam_sigemdb.proveedor_planes pp on pp.proveedor_id = pr.id
where pp.id = vPLAN; 

update mutualam_amandb.proveedores 
set direccion_pagare = ltrim(rtrim(ifnull(@DIRECCIONPAGARE,'')))
,idr = @IDPROV2
where codigo_proveedor = vCUITPROVEEDOR;

update mutualam_sigemdb.proveedores set idr = vCUITPROVEEDOR where id = @IDPROV2;

-- ACTUALIZAR DATOS DE CALCULO DEL PLAN EN EL PRODUCTO
select tipo_producto,tna,iva,gasto_admin,sellado,metodo_calculo,tipo_cuota_gasto_admin,
tipo_cuota_sellado,gasto_admin_base_calculo,sellado_base_calculo,
interes_moratorio,costo_cancelacion_anticipada 
into @tipo_producto,@tna,@iva,@gasto_admin,@sellado,@metodo_calculo,@tipo_cuota_gasto_admin,
@tipo_cuota_sellado,@gasto_admin_base_calculo,@sellado_base_calculo,
@interes_moratorio,@costo_cancelacion_anticipada
from mutualam_sigemdb.proveedor_planes where id = 3;

update mutualam_amandb.proveedores_productos
set 
codigo_producto_sigem = @tipo_producto,
tna = @tna,iva = @iva,gasto_admin = @gasto_admin,sellado = @sellado
,metodo_calculo = @metodo_calculo,tipo_cuota_gasto_admin = @tipo_cuota_gasto_admin,
tipo_cuota_sellado = @tipo_cuota_sellado,gasto_admin_base_calculo = @gasto_admin_base_calculo
,sellado_base_calculo = @sellado_base_calculo,
interes_moratorio = @interes_moratorio,costo_cancelacion_anticipada = @costo_cancelacion_anticipada
where codigo_proveedor = vCUITPROVEEDOR and codigo_producto = vCODIGOPRODUCTO;

-- BORRAR LAS CUOTAS ACTUALES
delete FROM mutualam_amandb.proveedores_productos_cuotas
where codigo_proveedor = vCUITPROVEEDOR
and codigo_producto = vCODIGOPRODUCTO;

-- CARGAR LAS CUOTAS NUEVAS
INSERT INTO `mutualam_amandb`.`proveedores_productos_cuotas`
(`codigo_producto`,`codigo_proveedor`,`solicitado`,`en_mano`,
`cuotas`,`monto_cuota`,`en_mano_100`,`tna`,`tem`,`metodo_calculo`,
`capital_puro`,`interes`,`iva`,`gasto_admin`,`sellado`,`calculo`)
select vCODIGOPRODUCTO,vCUITPROVEEDOR,
ppgc.capital,ppgc.liquido,
ppgc.cuotas,ppgc.importe,
null,
ppg.tna,ppg.tem,ppg.metodo_calculo,
ppgc.capital_puro,ppgc.interes,
ppgc.iva,
ppgc.gasto_admin,
ppgc.sellado,ppgc.calculo from mutualam_sigemdb.proveedor_plan_grilla_cuotas ppgc 
inner join mutualam_sigemdb.proveedor_plan_grillas ppg on ppg.id = ppgc.proveedor_plan_grilla_id
where 
ppgc.proveedor_plan_grilla_id = (
SELECT id FROM mutualam_sigemdb.proveedor_plan_grillas AS ProveedorPlanGrilla
                            WHERE ProveedorPlanGrilla.proveedor_plan_id = vPLAN 
                            AND ProveedorPlanGrilla.vigencia_desde <= date_format(now(),'%Y-%m-%d')
                            ORDER BY ProveedorPlanGrilla.vigencia_desde DESC
                            LIMIT 1
);

 


commit;

END$$
DELIMITER ;




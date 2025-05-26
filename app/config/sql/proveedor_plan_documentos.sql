/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  Lucho
 * Created: 04/11/2019
 */

CREATE TABLE IF NOT EXISTS `proveedor_plan_documentos` (
  `proveedor_plan_id` int(11) NOT NULL,
  `codigo_documento` varchar(12) NOT NULL,
  PRIMARY KEY (`proveedor_plan_id`,`codigo_documento`),
  KEY `fk_proveedor_plan_documentos_idx` (`proveedor_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `proveedor_plan_documentos` 
ADD CONSTRAINT `fk_proveedor_plan_documentos_1`
  FOREIGN KEY (`codigo_documento`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;







--NOTA: VERIFICAR SI EL CODIGO ES 8001 o MUTUIMPR8001

-- delete from proveedor_plan_documentos;
-- delete from global_datos where id like 'MUTUIMPR8%';

insert into global_datos(id,concepto_1)
values
('MUTUIMPR8001','DNI'),
('MUTUIMPR8002','RECIBO DE SUELDO - 1'),
('MUTUIMPR8003','RECIBO DE SUELDO - 2'),
('MUTUIMPR8004','RECIBO DE SUELDO - 3'),
('MUTUIMPR8005','MOVIMIENTOS BANCARIOS - 1'),
('MUTUIMPR8006','MOVIMIENTOS BANCARIOS - 2'),
('MUTUIMPR8007','MOVIMIENTOS BANCARIOS - 3'),
('MUTUIMPR8008','IMPUESTO/SERVICIO');



-- INSERT INTO proveedor_plan_documentos(proveedor_plan_id, codigo_documento) select distinct id, '8001' from proveedor_planes;
-- INSERT INTO proveedor_plan_documentos(proveedor_plan_id, codigo_documento) select distinct id, '8002' from proveedor_planes;
-- INSERT INTO proveedor_plan_documentos(proveedor_plan_id, codigo_documento) select distinct id, '8003' from proveedor_planes;
-- INSERT INTO proveedor_plan_documentos(proveedor_plan_id, codigo_documento) select distinct id, '8004' from proveedor_planes;

INSERT INTO proveedor_plan_documentos(proveedor_plan_id, codigo_documento)
select pp.id,g.id from proveedor_planes pp, global_datos g 
where g.id like 'MUTUIMPR8%';




ALTER TABLE `mutual_producto_solicitud_documentos` 
ADD COLUMN `codigo_documento` VARCHAR(12) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL DEFAULT NULL;


ALTER TABLE `mutual_producto_solicitud_documentos` 
ADD INDEX `fk_mutual_producto_solicitud_documentos_1_idx` (`codigo_documento` ASC);
;
ALTER TABLE `mutual_producto_solicitud_documentos` 
ADD CONSTRAINT `fk_mutual_producto_solicitud_documentos_1`
  FOREIGN KEY (`codigo_documento`)
  REFERENCES `global_datos` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


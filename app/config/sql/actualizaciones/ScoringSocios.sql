drop table if exists liquidacion_socio_scores;
CREATE TABLE `liquidacion_socio_scores` (
  `liquidacion_id` int(11) NOT NULL,
  `socio_id` int(11) NOT NULL,
  `13` decimal(10,2) NOT NULL DEFAULT '0.00',
  `12` decimal(10,2) NOT NULL DEFAULT '0.00',
  `09` decimal(10,2) NOT NULL DEFAULT '0.00',
  `06` decimal(10,2) NOT NULL DEFAULT '0.00',
  `03` decimal(10,2) NOT NULL DEFAULT '0.00',
  `00` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cargos_adicionales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `saldo_actual` decimal(10,2) DEFAULT NULL,
  `riesgo` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`liquidacion_id`,`socio_id`),
  KEY `fk_liquidacion_socio_scores_2_idx` (`socio_id`),
  CONSTRAINT `fk_liquidacion_socio_scores_1` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_liquidacion_socio_scores_2` FOREIGN KEY (`socio_id`) REFERENCES `socios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `liquidaciones` ADD COLUMN `scoring` TINYINT NOT NULL DEFAULT 0 AFTER `nro_recibo`;




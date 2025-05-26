CREATE TABLE `usuario_accesos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `logon_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(45) DEFAULT NULL,
  `host` varchar(45) DEFAULT NULL,
  `agente` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuario_accesos_1_idx` (`usuario_id`),
  CONSTRAINT `fk_usuario_accesos_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB;


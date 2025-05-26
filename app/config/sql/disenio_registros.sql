/*
SQLyog Community Edition- MySQL GUI v8.01 
MySQL - 5.0.75-0ubuntu10 : Database - sigem_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`sigem_db` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `sigem_db`;

/*Table structure for table `liquidacion_disenio_registros` */

DROP TABLE IF EXISTS `liquidacion_disenio_registros`;

CREATE TABLE `liquidacion_disenio_registros` (
  `id` int(11) NOT NULL auto_increment,
  `codigo_organismo` varchar(12) default NULL,
  `entrada_salida` varchar(1) default 'E',
  `banco_id` varchar(5) default NULL,
  `columna` int(11) default '0',
  `tipo_dato` varchar(1) default 'C',
  `longitud` int(11) default '0',
  `decimales` int(11) default '0',
  `columna_destino` varchar(5) default NULL,
  `modelo` varchar(100) default NULL,
  `modelo_campo` varchar(100) default NULL,
  `orden_igualacion` int(11) default '0',
  `codigo_status` tinyint(1) default '0',
  `campo_consulta` tinyint(1) default '0',
  `agrupa` tinyint(1) default '0',
  `sumar` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  KEY `idx_codigo_organismo` (`codigo_organismo`),
  KEY `idx_banco` (`banco_id`),
  KEY `idx_entrada_salida` (`entrada_salida`),
  KEY `idx_criterios` (`modelo`,`modelo_campo`,`orden_igualacion`,`codigo_status`,`campo_consulta`,`agrupa`,`sumar`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `liquidacion_disenio_registros` */

insert  into `liquidacion_disenio_registros`(`id`,`codigo_organismo`,`entrada_salida`,`banco_id`,`columna`,`tipo_dato`,`longitud`,`decimales`,`columna_destino`,`modelo`,`modelo_campo`,`orden_igualacion`,`codigo_status`,`campo_consulta`,`agrupa`,`sumar`) values (1,'MUTUCORG2201','E','00020',1,'C',3,0,'C1',NULL,NULL,0,0,0,0,0),(2,'MUTUCORG2201','E','00020',2,'C',5,0,'C2',NULL,NULL,0,0,0,0,0),(3,'MUTUCORG2201','E','00020',3,'C',2,0,'C3',NULL,NULL,0,0,0,0,0),(4,'MUTUCORG2201','E','00020',4,'C',1,0,'C4',NULL,NULL,0,0,0,0,0),(5,'MUTUCORG2201','E','00020',5,'C',9,0,'C5',NULL,NULL,0,0,0,0,0),(6,'MUTUCORG2201','E','00020',6,'D',18,2,'D1','LiquidacionSocio','importe_debitado',2,0,1,0,1),(7,'MUTUCORG2201','E','00020',7,'F',8,0,'F1','LiquidacionSocio','fecha_pago',0,0,1,0,0),(8,'MUTUCORG2201','E','00020',8,'C',5,0,'C6',NULL,NULL,0,0,0,0,0),(9,'MUTUCORG2201','E','00020',9,'C',6,0,'C7',NULL,NULL,0,0,0,0,0),(10,'MUTUCORG2201','E','00020',10,'C',22,0,'C8',NULL,NULL,0,0,0,0,0),(11,'MUTUCORG2201','E','00020',11,'C',2,0,'C9',NULL,NULL,0,0,0,0,0),(12,'MUTUCORG2201','E','00020',12,'I',1,0,'C10',NULL,NULL,0,0,0,0,0),(13,'MUTUCORG2201','E','00020',13,'C',8,0,'C11','LiquidacionSocio','documento',1,0,1,1,0),(14,'MUTUCORG2201','E','00020',14,'I',4,0,'C12',NULL,NULL,0,0,0,0,0),(15,'MUTUCORG2201','E','00020',15,'C',9,0,'C13',NULL,NULL,0,0,0,0,0),(16,'MUTUCORG2201','E','00020',16,'C',3,0,'C14','LiquidacionSocio','status',0,1,1,1,0),(18,'MUTUCORG2201','E','00430',1,'D',1,0,'D2',NULL,NULL,0,0,0,0,0),(19,'MUTUCORG2201','E','00430',2,'C',10,0,'C1',NULL,NULL,0,0,0,0,0),(20,'MUTUCORG2201','E','00430',3,'F',8,0,'F2',NULL,NULL,0,0,0,0,0),(21,'MUTUCORG2201','E','00430',4,'F',8,0,'F3',NULL,NULL,0,0,0,0,0),(22,'MUTUCORG2201','E','00430',5,'F',8,0,'F1','LiquidacionSocio','fecha_pago',0,0,1,0,0),(23,'MUTUCORG2201','E','00430',6,'I',4,0,'C2',NULL,NULL,0,0,0,0,0),(24,'MUTUCORG2201','E','00430',7,'I',8,0,'C3',NULL,NULL,0,0,0,0,0),(25,'MUTUCORG2201','E','00430',8,'I',14,0,'C4',NULL,NULL,0,0,0,0,0),(26,'MUTUCORG2201','E','00430',9,'D',10,2,'D1','LiquidacionSocio','importe_debitado',2,0,1,0,1),(27,'MUTUCORG2201','E','00430',10,'C',7,0,'C5',NULL,NULL,0,0,0,0,0),(28,'MUTUCORG2201','E','00430',11,'C',8,0,'C11','LiquidacionSocio','documento',1,0,1,1,0),(29,'MUTUCORG2201','E','00430',12,'C',22,0,'C7',NULL,NULL,0,0,0,0,0),(30,'MUTUCORG2201','E','00430',13,'C',3,0,'C14','LiquidacionSocio','status',0,1,1,1,0),(31,'MUTUCORG2201','E','00430',14,'I',22,0,'C9',NULL,NULL,0,0,0,0,0),(32,'MUTUCORG2201','E','00430',15,'F',8,0,'F4',NULL,NULL,0,0,0,0,0),(33,'MUTUCORG2201','E','00430',16,'F',8,0,'F5',NULL,NULL,0,0,0,0,0),(34,'MUTUCORG2201','E','00430',17,'C',44,0,'C10',NULL,NULL,0,0,0,0,0),(35,'MUTUCORG2201','E','00430',18,'C',7,0,'C12',NULL,NULL,0,0,0,0,0),(36,'MUTUCORG6601','E','99999',1,'C',11,0,'C1','LiquidacionSocio','nro_beneficio',0,0,1,0,0),(37,'MUTUCORG6601','E','99999',2,'C',22,0,'C2',NULL,NULL,0,0,0,0,0),(38,'MUTUCORG6601','E','99999',3,'C',3,0,'C3',NULL,NULL,0,0,0,0,0),(39,'MUTUCORG6601','E','99999',4,'C',8,0,'C4','LiquidacionSocio','documento',0,0,1,1,0),(40,'MUTUCORG6601','E','99999',5,'C',6,0,'C5','LiquidacionSocio','codigo_dto',0,0,1,1,0),(41,'MUTUCORG6601','E','99999',6,'C',11,0,'C6',NULL,NULL,0,0,0,0,0),(42,'MUTUCORG6601','E','99999',7,'D',11,2,'D1','LiquidacionSocio','importe_debitado',0,0,1,0,1),(43,'MUTUCORG6601','E','99999',8,'C',4,0,'C7',NULL,NULL,0,0,0,0,0),(44,'MUTUCORG6601','E','99999',9,'C',11,0,'C8',NULL,NULL,0,0,0,0,0),(45,'MUTUCORG6601','E','99999',10,'C',6,0,'C9',NULL,NULL,0,0,0,0,0),(46,'MUTUCORG7701','E','99999',1,'C',1,0,'C1','LiquidacionSocio','tipo',0,0,1,0,0),(47,'MUTUCORG7701','E','99999',2,'C',2,0,'C2','LiquidacionSocio','nro_ley',0,0,1,0,0),(48,'MUTUCORG7701','E','99999',3,'C',6,0,'C3','LiquidacionSocio','nro_beneficio',0,0,1,0,0),(49,'MUTUCORG7701','E','99999',4,'C',2,0,'C4','LiquidacionSocio','sub_beneficio',0,0,1,0,0),(50,'MUTUCORG7701','E','99999',5,'C',24,0,'C5',NULL,NULL,0,0,0,0,0),(51,'MUTUCORG7701','E','99999',6,'C',3,0,'C6','LiquidacionSocio','codigo_dto',0,0,1,1,0),(52,'MUTUCORG7701','E','99999',7,'C',1,0,'C7','LiquidacionSocio','sub_codigo',0,0,1,1,0),(53,'MUTUCORG7701','E','99999',8,'D',10,2,'D1','LiquidacionSocio','importe_debitado',0,0,1,0,1),(54,'MUTUCORG7701','E','99999',9,'C',9,0,'C8',NULL,NULL,0,0,0,0,0),(55,'MUTUCORG7701','E','99999',10,'C',1,0,'C9','','',0,0,0,0,0),(56,'MUTUCORG7701','E','99999',11,'C',8,0,'C11','LiquidacionSocio','documento',0,0,1,1,0),(57,'MUTUCORG7701','E','99999',12,'F',8,0,'F1',NULL,NULL,0,0,0,0,0),(58,'MUTUCORG7701','E','99999',13,'C',3,0,'C12',NULL,NULL,0,0,0,0,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

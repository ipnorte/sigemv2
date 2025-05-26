/*
SQLyog Community v10.3 
MySQL - 5.1.61-0ubuntu0.10.04.1-log : Database - sigem_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `tipo_documentos` */

insert  into `tipo_documentos`(`id`,`tipo_documento`,`documento`,`descripcion`,`letra`,`sucursal`,`numero`,`destino`,`copias`,`longitud_pagina`,`look`,`user_created`,`user_modified`,`created`,`modified`) values (1,'OPA','OPA','ORDEN DE PAGO',NULL,1,8368,NULL,1,0,0,NULL,'SOLEDAD',NULL,'2011-03-01 15:58:06'),(3,'REC','REC','RECIBO','C',1,12402,NULL,2,0,0,NULL,'CECILIA',NULL,'2011-06-01 16:13:12'),(4,'','FAC','FACTURA','C',1,2767,NULL,1,0,0,NULL,'CECILIA',NULL,'2011-08-09 21:27:57');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

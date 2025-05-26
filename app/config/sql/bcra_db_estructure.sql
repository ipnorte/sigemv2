/*
SQLyog Community Edition- MySQL GUI v8.01 
MySQL - 5.0.75-0ubuntu10.2-log : Database - bcra_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `deubaja` */

DROP TABLE IF EXISTS `deubaja`;

CREATE TABLE `deubaja` (
  `COD_ENTI` varchar(5) NOT NULL,
  `FECHA_INF` varchar(6) NOT NULL,
  `TIPO_ID` varchar(2) NOT NULL,
  `NRO_ID` varchar(11) NOT NULL,
  `SAL_ADEU` decimal(10,2) default NULL,
  PRIMARY KEY  (`COD_ENTI`,`FECHA_INF`,`TIPO_ID`,`NRO_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `deudores` */

DROP TABLE IF EXISTS `deudores`;

CREATE TABLE `deudores` (
  `CODIGO_ENT` varchar(5) NOT NULL,
  `FEC_INF` varchar(6) NOT NULL,
  `TIPO` varchar(2) NOT NULL,
  `IDEN` varchar(11) NOT NULL,
  `ACTIVIDAD` varchar(3) default NULL,
  `SITUACION` varchar(2) default NULL,
  `PRESTAMOS` decimal(9,2) default NULL,
  `PARTICIP` decimal(9,2) default NULL,
  `GAROTOR` decimal(9,2) default NULL,
  `O_CPTOS` decimal(9,2) default NULL,
  `GARPREFA` decimal(9,2) default NULL,
  `GARPREFB` decimal(9,2) default NULL,
  `SINGARPREF` decimal(9,2) default NULL,
  `CGARPREFA` decimal(9,2) default NULL,
  `CGARPREFB` decimal(9,2) default NULL,
  `SINCGARPRE` decimal(9,2) default NULL,
  `PREVI` decimal(9,2) default NULL,
  `DEUDA_CUBR` int(11) default NULL,
  `PROC_JUD` int(11) default NULL,
  `REFINANC` int(11) default NULL,
  `REC_OBLIG` int(11) default NULL,
  `SIT_JURIDI` int(11) default NULL,
  `IRRE_DISPT` int(11) default NULL,
  `DIA_ATRASO` int(11) default NULL,
  PRIMARY KEY  (`CODIGO_ENT`,`FEC_INF`,`TIPO`,`IDEN`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `maeent` */

DROP TABLE IF EXISTS `maeent`;

CREATE TABLE `maeent` (
  `MEN_CODENT` varchar(5) NOT NULL,
  `MEN_NOMENT` varchar(80) default NULL,
  PRIMARY KEY  (`MEN_CODENT`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `morexent` */

DROP TABLE IF EXISTS `morexent`;

CREATE TABLE `morexent` (
  `INF_PRES` varchar(6) NOT NULL,
  `TIPO` varchar(2) NOT NULL,
  `IDEN` varchar(11) NOT NULL,
  `NOMENT` varchar(120) default NULL,
  `PROC_JUD` int(11) default NULL,
  PRIMARY KEY  (`INF_PRES`,`TIPO`,`IDEN`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `nodeuba` */

DROP TABLE IF EXISTS `nodeuba`;

CREATE TABLE `nodeuba` (
  `CUIT` varchar(11) NOT NULL,
  `DENOM` varchar(55) default NULL,
  PRIMARY KEY  (`CUIT`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `nomdeu` */

DROP TABLE IF EXISTS `nomdeu`;

CREATE TABLE `nomdeu` (
  `CUIT` varchar(11) NOT NULL,
  `DENOM` varchar(55) default NULL,
  PRIMARY KEY  (`CUIT`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `nommor` */

DROP TABLE IF EXISTS `nommor`;

CREATE TABLE `nommor` (
  `CUIT` varchar(11) NOT NULL,
  `DENOM` varchar(55) default NULL,
  PRIMARY KEY  (`CUIT`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `padron` */

DROP TABLE IF EXISTS `padron`;

CREATE TABLE `padron` (
  `CUIT` varchar(11) NOT NULL,
  `DENOM` varchar(55) default NULL,
  `MARCA` varchar(1) default NULL,
  PRIMARY KEY  (`CUIT`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

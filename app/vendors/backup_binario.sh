#!/bin/bash
#################################################################################
# COMANDO BASH PARA COPIA BINARIA DEL DIRECTORIO DE DATOS MYSQL
# Detiene los servicios Apache y MySQL
# hace un tar de la carpeta donde esta el data dir de mysql junto con la carpeta de los ini (apache,php y mysql) + la carpeta
# log donde esta los bin-log de mysql
# ADRIAN 19/03/2012
# USO DESDE LINEA DE COMANDOS
# 	sh backup_binario.sh parametro_1 parametro_2
# 	parametro_1 = ruta a la carpeta donde estan las bases de datos del mysql 
#									ejemplo: "/home/adrian/dev/appServers/mysqlData"
#		parametro_2 = ruta al directorio donde se va almacenar el archivo tar
#									ejemplo: "/home/adrian/tmp/sigem/datos/backups/mysql/diario/archivo.tar.gz"
#	ejemplo:  sh backup_binario.sh /home/adrian/dev/appServers/mysqlData /home/adrian/tmp/sigem/datos/backups/mysql/diario/archivo.tar.gz
#################################################################################

if [ -f $1 ];then
	exit 1;
fi

if [ -f $2 ];then
	exit 1;
fi

service apache2 stop
service mysql stop
tar -czf $2 $1 "/etc/mysql" "/etc/apache2" "/etc/php5" "/var/log/mysql"
service mysql start
service apache2 start


exit 0

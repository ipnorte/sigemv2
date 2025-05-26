#!/bin/bash

REPO='http://190.136.179.204/aman/sigem_svn/trunk/'
RELEASE='/var/release/sigem'
#APP='/datos/www/sigem/app'
APP='/var/www/sigem/app'
MSG_CONFIRM='(S/N)[ENTER=N]'

SVN_ACTION="svn --force export -r HEAD  $REPO $RELEASE"

PLUGINS_RELEASE="$RELEASE/app/plugins"
VENDORS_RELEASE="$RELEASE/app/vendors"

#VENDORS_APP="$APP/app/vendors"
#PLUGINS_APP="$APP/app/plugins"

echo "----------------------------------------------------------------------------------------------"
echo " *** ACTUALIZACION SIGEM BASH *** Adrian 04/01/2012"
echo "----------------------------------------------------------------------------------------------"

if [ -f $RELEASE ];then
	mkdir $RELEASE
fi

#sudo $SVN_ACTION

#sudo svn --force export -r HEAD http://192.168.0.128:8080/svn2/trunk /var/release/sigem

read -p "Exportar Release $MSG_CONFIRM: " REPLY
if [ -z $REPLY ]; then
	REPLY='N'
fi
if [ $REPLY = 'S' ]; then
	if [ -d $RELEASE ];then
		echo "EJECUTANDO: $SVN_ACTION"
		sudo $SVN_ACTION
	else
		echo "EJECUTANDO: $SVN_ACTION"
		mkdir $RELEASE
		sudo $SVN_ACTION
	fi
fi
if [ -d $APP ]; then
	#COPIO PLUGINS
	if [ -d $RELEASE/app/plugins ] && [ -d $APP ]; then
		cp -R $RELEASE/app/plugins $APP
		echo 'Actualizacion plugins.................[OK]'
	else
		echo "ERROR: $RELEASE/app/plugins NO EXISTE !!!"
		exit 0
	fi
	#COPIO VENDORS
	if [ -d $RELEASE/app/vendors ] && [ -d $APP ]; then
		cp -R $RELEASE/app/vendors $APP
		echo 'Actualizacion vendors.................[OK]'
	else
		echo "ERROR: $RELEASE/app/vendors NO EXISTE !!!"
		exit 0
	fi

else
	echo "ERROR: $APP NO EXISTE !!!"
	exit 0
fi


#ACTUALIZACIONES PUNTUALES
read -p "Actualizar views $MSG_CONFIRM: " REPLY
if [ -z $REPLY ]; then
	REPLY='N'
fi
if [ $REPLY = 'S' ]; then
	if [ -d $RELEASE/app/views ] && [ -d $APP ]; then
		cp -R $RELEASE/app/views $APP
		echo 'Actualizacion views.................[OK]'
	else
		echo "ERROR: $RELEASE/app/vendors NO EXISTE."
	fi
fi
read -p "Actualizar app_controller.php $MSG_CONFIRM: " REPLY
if [ -z $REPLY ]; then
	REPLY='N'
fi
if [ $REPLY = 'S' ]; then
	if [ -f $RELEASE/app/app_controller.php ] && [ -d $APP ]; then
		cp -R $RELEASE/app/app_controller.php $APP
		echo 'Actualizacion app_controller.php .................[OK]'
	else
		echo 'Actualizacion app_controller .................[FAIL]'
	fi
fi
read -p "Actualizar app_model.php $MSG_CONFIRM: " REPLY
if [ -z $REPLY ]; then
	REPLY='N'
fi
if [ $REPLY = 'S' ]; then
	if [ -f $RELEASE/app/app_model.php ] && [ -d $APP ]; then
		cp -R $RELEASE/app/app_model.php $APP
		echo 'Actualizacion app_model.php .................[OK]'
	else
		echo 'Actualizacion app_model .................[FAIL]'
	fi
fi
read -p "Actualizar app_helper.php $MSG_CONFIRM: " REPLY
if [ -z $REPLY ]; then
	REPLY='N'
fi
if [ $REPLY = 'S' ]; then
	if [ -f $RELEASE/app/app_helper.php ] && [ -d $APP ]; then
		cp -R $RELEASE/app/app_helper.php $APP
		echo 'Actualizacion app_helper.php .................[OK]'
	else
		echo 'Actualizacion app_helper .................[FAIL]'
	fi
fi
read -p "Actualizar imagenes $MSG_CONFIRM: " REPLY
if [ -z $REPLY ]; then
	REPLY='N'
fi
if [ $REPLY = 'S' ]; then
	if [ -d $RELEASE/app/webroot/img ] && [ -d $APP ]; then
		cp -R $RELEASE/app/webroot/img $APP/webroot/img
		echo 'Actualizacion img .................[OK]'
	else
		echo 'Actualizacion img .................[FAIL]'
	fi
fi
read -p "Actualizar js $MSG_CONFIRM: " REPLY
if [ -z $REPLY ]; then
	REPLY='N'
fi
if [ $REPLY = 'S' ]; then
	if [ -d $RELEASE/app/webroot/js ] && [ -d $APP ]; then
		cp -R $RELEASE/app/webroot/js $APP/webroot
		echo 'Actualizacion js .................[OK]'
	else
		echo 'Actualizacion js .................[FAIL]'
	fi
fi
read -p "Actualizar css $MSG_CONFIRM: " REPLY
if [ -z $REPLY ]; then
	REPLY='N'
fi
if [ $REPLY = 'S' ]; then
	if [ -d $RELEASE/app/webroot/css ] && [ -d $APP ]; then
		cp -R $RELEASE/app/webroot/css $APP/webroot
		echo 'Actualizacion css .................[OK]'
	else
		echo 'Actualizacion css .................[FAIL]'	
	fi
fi

echo ""
echo "*********** ACTUALIZACION FINALIZADA **************"
echo ""

#read OPTION

exit 0

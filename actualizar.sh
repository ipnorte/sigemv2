#!/bin/bash
rm -rf /home/cordobas/release/sigemv2
cd /home/cordobas/release

#git clone https://bitbucket.org/AdrianTorres/sigemv2.git
# https://support.atlassian.com/bitbucket-cloud/docs/troubleshoot-ssh-issues/

git clone git@bitbucket.org:AdrianTorres/sigemv2.git


chown -R cordobas:nobody /home/cordobas/release

echo ""
echo "-----------------------------------"
echo "Iniciando Actualizacion SIGEM"
echo "-----------------------------------"
echo ""


cp -R /home/cordobas/release/sigemv2/app/plugins /home/cordobas/public_html/cbaservicios/app
cp -R /home/cordobas/release/sigemv2/app/vendors /home/cordobas/public_html/cbaservicios/app
cp -R /home/cordobas/release/sigemv2/app/views /home/cordobas/public_html/cbaservicios/app
cp -R /home/cordobas/release/sigemv2/app/controllers /home/cordobas/public_html/cbaservicios/app
cp -R /home/cordobas/release/sigemv2/app/models /home/cordobas/public_html/cbaservicios/app
cp -R /home/cordobas/release/sigemv2/app/app_controller.php /home/cordobas/public_html/cbaservicios/app
cp -R /home/cordobas/release/sigemv2/app/app_model.php /home/cordobas/public_html/cbaservicios/app
cp -R /home/cordobas/release/sigemv2/app/webroot/img /home/cordobas/public_html/cbaservicios/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/css /home/cordobas/public_html/cbaservicios/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/js /home/cordobas/public_html/cbaservicios/app/webroot
echo 'CBASERVICIOS  -> [OK]'

cp -R /home/cordobas/release/sigemv2/app/plugins /home/cordobas/public_html/platilandia/app
cp -R /home/cordobas/release/sigemv2/app/vendors /home/cordobas/public_html/platilandia/app
cp -R /home/cordobas/release/sigemv2/app/views /home/cordobas/public_html/platilandia/app
cp -R /home/cordobas/release/sigemv2/app/controllers /home/cordobas/public_html/platilandia/app
cp -R /home/cordobas/release/sigemv2/app/models /home/cordobas/public_html/platilandia/app
cp -R /home/cordobas/release/sigemv2/app/app_controller.php /home/cordobas/public_html/platilandia/app
cp -R /home/cordobas/release/sigemv2/app/app_model.php /home/cordobas/public_html/platilandia/app
cp -R /home/cordobas/release/sigemv2/app/webroot/img /home/cordobas/public_html/platilandia/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/css /home/cordobas/public_html/platilandia/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/js /home/cordobas/public_html/platilandia/app/webroot
echo 'PLATILANDIA   -> [OK]'

cp -R /home/cordobas/release/sigemv2/app/plugins /home/cordobas/public_html/soluciones/app
cp -R /home/cordobas/release/sigemv2/app/vendors /home/cordobas/public_html/soluciones/app
cp -R /home/cordobas/release/sigemv2/app/views /home/cordobas/public_html/soluciones/app
cp -R /home/cordobas/release/sigemv2/app/controllers /home/cordobas/public_html/soluciones/app
cp -R /home/cordobas/release/sigemv2/app/models /home/cordobas/public_html/soluciones/app
cp -R /home/cordobas/release/sigemv2/app/app_controller.php /home/cordobas/public_html/soluciones/app
cp -R /home/cordobas/release/sigemv2/app/app_model.php /home/cordobas/public_html/soluciones/app
cp -R /home/cordobas/release/sigemv2/app/webroot/img /home/cordobas/public_html/soluciones/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/css /home/cordobas/public_html/soluciones/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/js /home/cordobas/public_html/soluciones/app/webroot
echo 'SOLUCIONES    -> [OK]'

#cp -R /home/cordobas/release/sigemv2/app/plugins /home/cordobas/public_html/rivacred/app
#cp -R /home/cordobas/release/sigemv2/app/vendors /home/cordobas/public_html/rivacred/app
#cp -R /home/cordobas/release/sigemv2/app/views /home/cordobas/public_html/rivacred/app
#cp -R /home/cordobas/release/sigemv2/app/controllers /home/cordobas/public_html/rivacred/app
#cp -R /home/cordobas/release/sigemv2/app/models /home/cordobas/public_html/rivacred/app
#cp -R /home/cordobas/release/sigemv2/app/app_controller.php /home/cordobas/public_html/rivacred/app
#cp -R /home/cordobas/release/sigemv2/app/app_model.php /home/cordobas/public_html/rivacred/app
#cp -R /home/cordobas/release/sigemv2/app/webroot/img /home/cordobas/public_html/rivacred/app/webroot
#cp -R /home/cordobas/release/sigemv2/app/webroot/css /home/cordobas/public_html/rivacred/app/webroot
#cp -R /home/cordobas/release/sigemv2/app/webroot/js /home/cordobas/public_html/rivacred/app/webroot
#echo 'RIVACRED      -> [OK]'

cp -R /home/cordobas/release/sigemv2/app/plugins /home/cordobas/public_html/solydar/app
cp -R /home/cordobas/release/sigemv2/app/vendors /home/cordobas/public_html/solydar/app
cp -R /home/cordobas/release/sigemv2/app/views /home/cordobas/public_html/solydar/app
cp -R /home/cordobas/release/sigemv2/app/controllers /home/cordobas/public_html/solydar/app
cp -R /home/cordobas/release/sigemv2/app/models /home/cordobas/public_html/solydar/app
cp -R /home/cordobas/release/sigemv2/app/app_controller.php /home/cordobas/public_html/solydar/app
cp -R /home/cordobas/release/sigemv2/app/app_model.php /home/cordobas/public_html/solydar/app
cp -R /home/cordobas/release/sigemv2/app/webroot/img /home/cordobas/public_html/solydar/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/css /home/cordobas/public_html/solydar/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/js /home/cordobas/public_html/solydar/app/webroot
echo 'SOLYDAR       -> [OK]'

cp -R /home/cordobas/release/sigemv2/app/plugins /home/mutualam/public_html/sigem/app
cp -R /home/cordobas/release/sigemv2/app/vendors /home/mutualam/public_html/sigem/app
cp -R /home/cordobas/release/sigemv2/app/views /home/mutualam/public_html/sigem/app
cp -R /home/cordobas/release/sigemv2/app/controllers /home/mutualam/public_html/sigem/app
cp -R /home/cordobas/release/sigemv2/app/models /home/mutualam/public_html/sigem/app
cp -R /home/cordobas/release/sigemv2/app/app_controller.php /home/mutualam/public_html/sigem/app
cp -R /home/cordobas/release/sigemv2/app/app_model.php /home/mutualam/public_html/sigem/app
cp -R /home/cordobas/release/sigemv2/app/webroot/img /home/mutualam/public_html/sigem/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/css /home/mutualam/public_html/sigem/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/js /home/mutualam/public_html/sigem/app/webroot
echo 'AMAN          -> [OK]'
chown -R mutualam:nobody /home/mutualam/public_html/sigem
rm -rf /home/mutualam/public_html/sigem/app/tmp/cache/persistent/cake_*


cp -R /home/cordobas/release/sigemv2/app/plugins /home/cordobas/public_html/ryvsa/app
cp -R /home/cordobas/release/sigemv2/app/vendors /home/cordobas/public_html/ryvsa/app
cp -R /home/cordobas/release/sigemv2/app/views /home/cordobas/public_html/ryvsa/app
cp -R /home/cordobas/release/sigemv2/app/controllers /home/cordobas/public_html/ryvsa/app
cp -R /home/cordobas/release/sigemv2/app/models /home/cordobas/public_html/ryvsa/app
cp -R /home/cordobas/release/sigemv2/app/app_controller.php /home/cordobas/public_html/ryvsa/app
cp -R /home/cordobas/release/sigemv2/app/app_model.php /home/cordobas/public_html/ryvsa/app
cp -R /home/cordobas/release/sigemv2/app/webroot/img /home/cordobas/public_html/ryvsa/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/css /home/cordobas/public_html/ryvsa/app/webroot
cp -R /home/cordobas/release/sigemv2/app/webroot/js /home/cordobas/public_html/ryvsa/app/webroot
echo 'RYVSA         -> [OK]'
echo ""
echo "-----------------------------------"

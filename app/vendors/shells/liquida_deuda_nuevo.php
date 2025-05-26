<?php

/**
 * -------------------------------------------------
 * ---- PROCESO SHELL DE LIQUIDACION DE DEUDA -----
 * -------------------------------------------------
 * 
 * El proceso consta de 4 pasos 
 * 
 * PASO 1: 	Liquida los conceptos permanentes del socio para el periodo que se esta procesando
 * PASO 2: 	Procesa las cuotas adeudadas del socio al periodo liquidado inclusive (ADEUDADAS Y SITUACION DE DEUDA).  
 * 			Por cada cuota va determinando el saldo.  Estas cuotas las va generando en la tabla liquidacion_cuotas
 * PASO 3:	Determina los adicionales que puede cargarse, por ejemplo: GASTO ADMINISTRATIVO CBU.  Estos gastos
 * 			estan configurados en la tabla mutual_adicionales.  Si el adicional esta configurado para que se devengue
 * 			automaticamente genera la cuota en la orden_descuento_cuotas. Si n贸 lo genera en la tabla mutual_adicional_pendientes
 * 			y en la liquidacion_cuotas, una vez que se procesa el pago se genera la cuota respectiva en la tabla orden_descuento_cuotas.
 * PASO 4: 	Procesa la liquidacion_cuotas y genera el resumen en la liquidacion_socios de donde se tienen que sacar los distintos archivos
 * 			para enviar a los organismos de retencion
 * 
 *
 * 	PARAMETROS PASADOS:
 * 	P1: PERIODO
 * 	P2: ORGANISMO A LIQUIDAR
 *
 * 	LANZADORES (depende de la instalacion del PHP)
 * 	/usr/bin/php5.6 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php liquida_deuda_nuevo 1569 -app /home/adrian/Trabajo/www/sigemv2/app/
 * 	/opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php liquida_deuda_fraccion_nuevo 157 -app /home/adrian/Desarrollo/www/sigem/app/
 * 	/usr/bin/php5 /var/www/sigem/cake/console/cake.php liquida_deuda_fraccion_nuevo 1 -app /var/www/sigem/app/
 * 	/usr/bin/php5 /var/www/virtual/mutual22septiembre.com.ar/htdocs/sigem/cake/console/cake.php liquida_deuda_fraccion_nuevo 33 -app /var/www/virtual/mutual22septiembre.com.ar/htdocs/sigem/app/
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * RENDIMIENTO ANTERIOR
 * 703 SOCIOS -> 7.8/minuto -> 90 min
 * proyeccion 20000 SOCIOS ->  (+/-) 42 HS 
 * 
 * RENDIMIENTO ACTUAL
 * 703 SOCIOS -> 78.1/minuto -> 9 min
 * proyeccion 20000 SOCIOS -> (+/-) 4.26 HS 
 * 
 */


class LiquidaDeudaNuevoShell extends Shell {

    var $tasks = array('Error');
    var $pid = null;
    var $oASINC = null;
    var $liquidacionID = 0;

    function main() {

        $starttime = microtime(true);

        Configure::write('debug', 1);

        $STOP = 0;

        if (empty($this->args[0])) {
            $this->out("ERROR: PID NO ESPECIFICADO");
            return;
        }

        $pid = $this->args[0];
        $this->pid = $pid;

        $this->Error->pid = $pid;
        $this->Error->limpiarTabla();

        App::import('Model','Shells.Asincrono');
        $oASINC = new Asincrono(); 
        $oASINC->auditable = TRUE;
        $oASINC->id = $pid;

        $this->oASINC = $oASINC;

        App::import('Model', 'Mutual.Liquidacion');
        $oLQ = new Liquidacion();
        
        App::import('Model','Config.GlobalDato');
        $oGLB = new GlobalDato();


        ##################################################################################################################
        # PROCESO MASIVO DE LIQUIDACION DE DEUDA
        ##################################################################################################################
        $oASINC->actualizar(1, 100, "ESPERE, INICIANDO PROCESO * TESTING * ...");

        $periodo = $oASINC->getParametro('p1');
        $organismo = $oASINC->getParametro('p2');
        $tipo_deuda_liquida = $oASINC->getParametro('p3');
        $tipo_deuda_liquida = (empty($tipo_deuda_liquida) ? 0 : $tipo_deuda_liquida);

        $pre_imputacion = $oASINC->getParametro('p4');
        $pre_imputacion = (empty($pre_imputacion) ? 0 : 1);        

         
        $oASINC->actualizar(10, 100, "ESPERE, LIQUIDANDO CUOTA SERVICIOS...");
        $SPCALL = "call SP_LIQUIDA_CUOTA_SERVICIOS('".$periodo."','".$organismo."');";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(20, 100, 'SP_LIQUIDA_CUOTA_SERVICIOS - ' . $oLQ->getDataSource()->error);
            return;
        }
        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,3);    
        
        ///////////////////////////////////////////////////////////////////
        //VERIFICAR SI TIENE SETEADO EN mutual.ini EL CONTROL PARA LIQUIDAR
        //LA CUOTA SOCIAL SOLO SI TIENE DEUDA
        ///////////////////////////////////////////////////////////////////
        $liqSiTieneDeuda = 'FALSE';
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        if(isset($INI_FILE['general']['cuota_social_permanente']) && $INI_FILE['general']['cuota_social_permanente'] == 0){
            $liqSiTieneDeuda = 'TRUE';
        }        
        

        $oASINC->actualizar(20, 100, "ESPERE, LIQUIDANDO CUOTA SOCIAL...");
        $SPCALL = "call SP_LIQUIDA_CUOTA_SOCIAL('".$periodo."','".$organismo."','MUTUPROD0003','MUTUTCUOCSOC','CMUTU','MUTUSICUMUTU',$liqSiTieneDeuda);";
        
        $oLQ->query($SPCALL);
        if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(40, 100, 'SP_LIQUIDA_CUOTA_SOCIAL - ' .$oLQ->getDataSource()->error);
            return;
        }
        
        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,3); 
        $oASINC->actualizar(30, 100, "ESPERE, LIQUIDANDO DEUDA...");
        $SPCALL = "CALL SP_LIQUIDA_DEUDA('".$periodo."','".$organismo."',$tipo_deuda_liquida,$pre_imputacion,NULL);";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(60, 100, 'SP_LIQUIDA_DEUDA - ' . $oLQ->getDataSource()->error);
            return;
        } 


        $liquidacion = $oLQ->getByPeriodoOrganismo($periodo,$organismo);
        $liquidacionID = $liquidacion['Liquidacion']['id'];


        
		###########################################################################################################################
		#GENERAR CONCEPTOS ADICIONALES
		###########################################################################################################################
        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,3); 


        $oASINC->actualizar(60, 100, "ESPERE, LIQUIDANDO ADICIONALES...");
        $SPCALL = "CALL SP_LIQUIDA_ADICIONALES($liquidacionID,NULL);";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(80, 100, "SP_LIQUIDA_ADICIONALES - " . $oLQ->getDataSource()->error);
            return;
        }
        
        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,3);          
		###########################################################################################################################
		#GENERAR CONCEPTOS PUNITORIOS
		###########################################################################################################################
        $oASINC->actualizar(70, 100, "ESPERE, LIQUIDANDO ADICIONALES...");
        $SPCALL = "CALL SP_LIQUIDA_PUNITORIOS($liquidacionID,NULL);";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(80, 100, "SP_LIQUIDA_PUNITORIOS - " . $oLQ->getDataSource()->error);
            return;
        }         

        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,3);          
		###########################################################################################################################
		#GENERAR ACUERDO DEBITO SOCIOS
		###########################################################################################################################
        $oASINC->actualizar(80, 100, "ESPERE, LIQUIDACION ACUERDOS DE DEBITO...");
        $SPCALL = "CALL SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO($liquidacionID, NULL);";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(80, 100, "SP_LIQUIDA_DEUDA_CBU_ACUERDO_DEBITO - " . $oLQ->getDataSource()->error);
            return;
        } 

        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,3);  
        
        ###########################################################################################################################
        # LEER LOS PARAMETROS DE CONFIGURACION DE LA LIQUIDACION DEL ORGANISMO PARA SACAR EL STORE QUE USA
        ###########################################################################################################################
        $SP_PERIODO = $oGLB->GlobalDato('concepto_4',$organismo);
        $SP_MORA = $oGLB->GlobalDato('concepto_5',$organismo);

        $SP_ETIQUETA_PERIODO = $oGLB->spLiquidaPeriodo[$SP_PERIODO];
        $SP_ETIQUETA_MORA = $oGLB->spLiquidaMora[$SP_MORA];


		###########################################################################################################################
		#GENERAR LIQUIDACION SOCIOS -- desde ac谩 toma los parametros seteados para el organismo
        ###########################################################################################################################
        if(!empty($SP_PERIODO)){
            $oASINC->actualizar(80, 100, "ESPERE, $SP_ETIQUETA_PERIODO...");
            $SPCALL = "CALL $SP_PERIODO($liquidacionID,NULL);";
            $oLQ->query($SPCALL);
            if(!empty($oLQ->getDataSource()->error)){
                $oASINC->actualizar(80, 100, "$SP_PERIODO - " . $oLQ->getDataSource()->error);
                return;
            } 
        }

        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,3);          
		###########################################################################################################################
		#GENERAR LIQUIDACION SOCIOS
        ###########################################################################################################################
        if(!empty($SP_MORA)){
            $oASINC->actualizar(90, 100, "ESPERE, $SP_ETIQUETA_MORA...");
            $SPCALL = "CALL $SP_MORA($liquidacionID,NULL);";
            $oLQ->query($SPCALL);
            if(!empty($oLQ->getDataSource()->error)){
                $oASINC->actualizar(80, 100, "$SP_MORA - " . $oLQ->getDataSource()->error);
                return;
            }  
        }

		###########################################################################################################################
		#GASTOS BANCARIOS POR REGISTRO
		###########################################################################################################################
        $oASINC->actualizar(95, 100, "ESPERE, GENERANDO GASTOS BANCARIOS...");
        $SPCALL = "CALL SP_LIQUIDA_GTO_BANCARIOS($liquidacionID,NULL);";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(80, 100, "SP_LIQUIDA_GTO_BANCARIOS - " . $oLQ->getDataSource()->error);
            return;
        }        

		###########################################################################################################################
		#RENUMERAR REGISTROS POR SOCIO
		###########################################################################################################################
        $oASINC->actualizar(96, 100, "ESPERE, RENUMERANDO REGISTROS...");
        $SPCALL = "CALL SP_LIQUIDA_DEUDA_REGISTRO_RENUMERA($liquidacionID,NULL);";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(80, 100, "SP_LIQUIDA_DEUDA_REGISTRO_RENUMERA - " . $oLQ->getDataSource()->error);
            return;
        } 

        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,3);         
       
		###########################################################################################################################
		#GENERAR EL SCORING DE LOS SOCIOS
        ###########################################################################################################################
        $oASINC->actualizar(97, 100, "ESPERE, GENERANDO SCORING DE SOCIOS...");
        $SPCALL = "CALL SP_LIQUIDA_DEUDA_SOCIOS_SCORING($liquidacionID,NULL);";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(80, 100, "SP_LIQUIDA_DEUDA_SOCIOS_SCORING - " . $oLQ->getDataSource()->error);
            return;
        } 
		###########################################################################################################################
		#TOTALIZADOR
        ###########################################################################################################################
        $oASINC->actualizar(98, 100, "ESPERE, TOTALIZANDO LIQUIDACION...");
        $SPCALL = "CALL SP_LIQUIDA_DEUDA_TOTALIZADOR($liquidacionID);";
        $oLQ->query($SPCALL);
		if(!empty($oLQ->getDataSource()->error)){
            $oASINC->actualizar(80, 100, "SP_LIQUIDA_DEUDA_TOTALIZADOR - " . $oLQ->getDataSource()->error);
            return;
        } 

//         Configure::write('debug', 3);

//         $liquidacion = $oLQ->read(null, $liquidacionID);
        
        $data = array('Liquidacion' => array(
            'id' => $liquidacionID,
            'tipo_liquida' => $tipo_deuda_liquida
        ));
        $oLQ->save($data);
//         debug($oLQ->save($data));
//         debug($data);
        
        //dejo segundos para el caso de bases muy chicas
        sleep (5);
        $endtime = microtime(true);
        $timediff = round(intval($endtime - $starttime)/60,2);      
 
        $oASINC->actualizar(100,100,"FINALIZANDO...$timediff min");
        $oASINC->fin("**** PROCESO FINALIZADO **** $timediff min");	
        

        

    }

    #FIN PROCESO DE LIQUIDACION
   /*

alter table liquidaciones add column tipo_liquida int(11) default 0 after bloqueada;
ALTER TABLE `liquidaciones` 
CHANGE COLUMN `created` `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ;

    * 
    * */


}

?>
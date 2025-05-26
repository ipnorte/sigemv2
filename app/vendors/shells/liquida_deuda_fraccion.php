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
 * 			automaticamente genera la cuota en la orden_descuento_cuotas. Si nó lo genera en la tabla mutual_adicional_pendientes
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
 * 	/usr/bin/php5.6 /home/adrian/trabajo/www/sigem/cake/console/cake.php liquida_deuda_fraccion 31583 -app /home/adrian/trabajo/www/sigem/app/
 * 	/opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php liquida_deuda_fraccion 157 -app /home/adrian/Desarrollo/www/sigem/app/
 * 	/usr/bin/php5 /var/www/sigem/cake/console/cake.php liquida_deuda_fraccion 1 -app /var/www/sigem/app/
 * 	/usr/bin/php5 /var/www/virtual/mutual22septiembre.com.ar/htdocs/sigem/cake/console/cake.php liquida_deuda_fraccion 33 -app /var/www/virtual/mutual22septiembre.com.ar/htdocs/sigem/app/
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


App::import('Vendor','exec',array('file' => 'exec.php'));


class LiquidaDeudaFraccionShell extends Shell {

    var $tasks = array('Error');
    var $uses = array('Mutual.OrdenDescuento', 'Mutual.OrdenDescuentoCuota', 'Pfyj.Socio', 'Config.GlobalDato', 'Mutual.MutualProducto', 'Mutual.LiquidacionSocio');
    var $pid = null;
    var $oASINC = null;
    var $liquidacionID = 0;

    function main() {

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

//		$this->__limpiarTemportal($pid);

//        $oASINC = &ClassRegistry::init(array('class' => 'Shells.Asincrono', 'alias' => 'Asincrono'));
        App::import('Model','Shells.Asincrono');
        $oASINC = new Asincrono(); 
        $oASINC->auditable = TRUE;
        $oASINC->id = $pid;

        $this->oASINC = $oASINC;


        $this->__setCampo('bloqueada', 1);


        


        ##################################################################################################################
        # PROCESO MASIVO DE LIQUIDACION DE DEUDA
        ##################################################################################################################
        $oASINC->actualizar(1, 100, "ESPERE, INICIANDO PROCESO...");

        $periodo = $oASINC->getParametro('p1');
        $organismo = $oASINC->getParametro('p2');
        $pre_imputacion = $oASINC->getParametro('p3');
        $tipoLiquidacion = $oASINC->getParametro('p4');
        $SHELL_PID = $oASINC->getParametro('shell_pid');
        
        $tipoLiquidacion = (empty($tipoLiquidacion) ? 0 : $tipoLiquidacion);
        
        $this->actualizar_prioridad_imputacion_organismo($organismo);

        //controlar si ya esta corriendo
        if ($this->__isBloqueada($periodo, $organismo)) {
            $oASINC->actualizar(100, 100, "PROCESO BLOQUEADO POR OTRO USUARIO....");
            return;
        }
        if ($this->__isCerrada($periodo, $organismo)) {
            $oASINC->actualizar(100, 100, "LIQUIDACION CERRADA....");
            return;
        }
        if ($this->__isImputada($periodo, $organismo)) {
            $oASINC->actualizar(100, 100, "LIQUIDACION IMPUTADA....");
            return;
        }
        $idLiquidacionExistente = $this->ifExists($periodo, $organismo);
//        $this->out($idLiquidacionExistente);
//		
        if ($idLiquidacionExistente == 0):

            #borro la liquidacion si esta abierta
            #NO BORRA las cuotas generadas permanentes por la liquidacion
            $oASINC->actualizar(3, 100, "ESPERE, BORRANDO DATOS DE LA LIQUIDACION PREVIA DEL PERIODO ...");
            $this->__borrarLiquidacion($periodo, $organismo);

            #GENERO UNA CABECERA NUEVA
            $this->liquidacionID = $this->__generarCabeceraLiquidacion($periodo, $organismo, $pre_imputacion, $tipoLiquidacion);

        else:
//		
            $this->liquidacionID = $idLiquidacionExistente;
            $oASINC->actualizar(3, 100, "ESPERE, BORRANDO DATOS DE LA LIQUIDACION PREVIA DEL PERIODO ...");
            $this->__borrarLiquidacion($periodo, $organismo, true);
            $this->__actualizarFechaCreated($idLiquidacionExistente);
            
            App::import('Model', 'Mutual.Liquidacion');
            $oLQ = new Liquidacion();
            $data = array('Liquidacion' => array(
                'id' => $this->liquidacionID,
                'tipo_liquida' => $tipoLiquidacion
            ));
            $oLQ->save($data);
            
//		
        endif;
        
        #####################################################################
        #REPROGRAMAR DEUDA
        #####################################################################
//        $proveedoresIDs = $oASINC->GlobalDato("concepto_2", "MUTUMUTUREPR");
        $proveedoresIDs = null;
        if(!empty($proveedoresIDs)){
            $sql = "select orden_descuento_id
                    from orden_descuento_cuotas OrdenDescuentoCuota
                    inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                    where 
                    OrdenDescuento.activo = 1 and OrdenDescuento.permanente = 0 and OrdenDescuentoCuota.estado <> 'B' and
                    OrdenDescuentoCuota.proveedor_id in ($proveedoresIDs)
                    and OrdenDescuentoCuota.importe > ifnull((
                    select sum(importe) from orden_descuento_cobro_cuotas cc
                    where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id)
                    ,0) group by orden_descuento_id;";
            $ordenes = $oASINC->query($sql);
            if(!empty($ordenes)){
                App::import('Model', 'mutual.OrdenDescuento');
                $oODTO = new OrdenDescuento();  
                $oODTO->auditable = FALSE;
                $cantidad = count($ordenes);
                $n = 1;
                foreach($ordenes as $orden){
                    $oODTO->reprograma_orden2($orden['OrdenDescuentoCuota']['orden_descuento_id'], $periodo);
                    $oASINC->actualizar(3, 100, "ESPERE, REPROGRAMANDO ORDEN #".$orden['OrdenDescuentoCuota']['orden_descuento_id']." ($n/$cantidad)...");
                    $n++;
                }                
            }
            
        }
        
        

        #CARGO LOS SOCIOS QUE VOY A LIQUIDAR
        $oASINC->actualizar(1, 100, "ESPERE, CARGANDO SOCIOS PARA LIQUIDAR ...");
        App::import('Model', 'Pfyj.Socio');
        $oSocio = new Socio();
        $oSocio->auditable = FALSE;
        
        $oSocio->unbindModel(array('belongsTo' => array('Persona')));

        $sql = "SELECT COUNT(socio_id) as cantidad  FROM orden_descuentos OrdenDescuento  
                INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
                INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
                WHERE PersonaBeneficio.codigo_beneficio = '$organismo' and Persona.fallecida = 0 group by socio_id";

        $registros = $oSocio->query($sql);
        $cantidad = 0;
        if (!empty($registros)) {
            $cantidad = count($registros);
        }


        $oASINC->setTotal($cantidad);


        $socios = null;
        $SHELL = new exec();
        
        ///////////////////////////////////////////////////////////////////
        //VERIFICAR SI TIENE SETEADO EN mutual.ini EL CONTROL PARA LIQUIDAR
        //LA CUOTA SOCIAL SOLO SI TIENE DEUDA
        ///////////////////////////////////////////////////////////////////
        $liqSiTieneDeuda = 0;
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        if(isset($INI_FILE['general']['cuota_social_permanente']) && $INI_FILE['general']['cuota_social_permanente'] == 0){
            $liqSiTieneDeuda = 1;
        }
        ///////////////////////////////////////////////////////////////////
        $DISCRIMINA_PERMANENTES = FALSE;
        if(isset($INI_FILE['general']['discrimina_conceptos_permanentes_orden_debito']) && $INI_FILE['general']['discrimina_conceptos_permanentes_orden_debito'] == "1"){
            $DISCRIMINA_PERMANENTES = TRUE;
        }        
        $CONSOLIDADO = FALSE;
        if(isset($INI_FILE['general']['enviar_periodo_mas_mora']) && $INI_FILE['general']['enviar_periodo_mas_mora'] == "1"){
            $CONSOLIDADO = TRUE;
		}  

        ##################################################################################################################
        $CONTROL_NACION = 0;
        $BANCO_CONTROL = 0;
        $file = parse_ini_file(CONFIGS.'mutual.ini', true);
        if(isset($file['general']['banco_nacion_debito_periodo']) && $file['general']['banco_nacion_debito_periodo'] != ""){
            $CONTROL_NACION = 1;
            $BANCO_CONTROL = $file['general']['banco_nacion_debito_periodo'];
        }              

        $LIQUIDA_DEUDA_CBU_SP = 0;
        // if(isset($file['general']['sp_liquida_deuda_cbu']) && $file['general']['sp_liquida_deuda_cbu'] == 1){
        //     $LIQUIDA_DEUDA_CBU_SP = 0;
        // }
        
        
        
        $FRACCIONAR_LOTE = 20;
        if(isset($file['general']['liquidador_fraccionar_lote']) && $file['general']['liquidador_fraccionar_lote'] != 0){
            $FRACCIONAR_LOTE = $file['general']['liquidador_fraccionar_lote'];
        }        

        #########################################################################################################
        # CREO LOS SUBPROCESOS (DIVIDO EN 5 PAQUETES DE DATOS)
        #########################################################################################################
        $i = 0;
        $limit = intval($cantidad / $FRACCIONAR_LOTE);        
        $limit = ($limit > 0 ? $limit : $cantidad);
        
        #BORRO LOS SUBPROCESOS
        $oASINC->query("DELETE FROM asincronos where trim(p13) = $pid");
        
        $STOP = FALSE;
        
        $oASINC->auditable = FALSE;
        
        while (true) {    

            $sql = "SELECT socio_id FROM orden_descuentos OrdenDescuento
                    INNER JOIN persona_beneficios AS PersonaBeneficio ON (PersonaBeneficio.id = OrdenDescuento.persona_beneficio_id)
                    INNER JOIN personas as Persona on (Persona.id = PersonaBeneficio.persona_id)
                    WHERE PersonaBeneficio.codigo_beneficio = '$organismo' and Persona.fallecida = 0 GROUP BY socio_id ORDER BY socio_id LIMIT $i,$limit;";
            $socios = $oSocio->query($sql);

            if (empty($socios)){
                break;
            }

            //CREO UN REGISTRO ASINCRONO CLONANDO EL PRINCIPAL
            $asincrono = $oASINC->read(null,$pid);
            
            $SUBASINCRONO = new Asincrono();
            
            $asincrono['Asincrono']['id'] = 0;
            $asincrono['Asincrono']['p13'] = $pid; //guardo el id del principal
            $asincrono['Asincrono']['titulo'] .= " [$i]";
            $SUBASINCRONO->save($asincrono);
            $asincrono['Asincrono']['id'] = $SUBASINCRONO->getLastInsertID();
            
            $CORE = CORE_PATH . "cake" . DIRECTORY_SEPARATOR . "console" . DIRECTORY_SEPARATOR . "cake.php";
            $CMD = $oASINC->get_phpcli() . " $CORE liquida_deuda_fraccion_lote " . $this->liquidacionID . " " . $asincrono['Asincrono']['id'] . " $SHELL_PID $i $limit $organismo $periodo $pre_imputacion $LIQUIDA_DEUDA_CBU_SP $liqSiTieneDeuda $CONTROL_NACION $BANCO_CONTROL $DISCRIMINA_PERMANENTES $CONSOLIDADO -app ". APP_PATH;
            
            $asincrono['Asincrono']['txt1'] = $CMD;
            $asincrono['Asincrono']['shell_pid'] = $SHELL->background($CMD);
            $SUBASINCRONO->save($asincrono);
            
            $oASINC->actualizar(10, $cantidad, "CREANDO SUBPROCESO #" . $asincrono['Asincrono']['shell_pid'] . " [$i/$cantidad]");
            
            $i += $limit;
            
            if(!$SHELL->is_running($SHELL_PID)){
                $STOP = TRUE;
                break;
            }
            
            if($oASINC->detenido()){
                $STOP = TRUE;
                $SHELL->kill ($asincrono['Asincrono']['shell_pid']);
                break;
            }

            
        }
        
        while(true && $SHELL->is_running($SHELL_PID)){
            
            $sql = "SELECT sum(total) as total, sum(contador) as contador FROM asincronos as Asincrono WHERE trim(p13) = $pid;";
            $procesos = $oASINC->query($sql); 
            
            if(empty($procesos)) break;
            
            if(!isset($procesos[0][0]['total']) || !isset($procesos[0][0]['contador'])) break;
            
            $total = $procesos[0][0]['total'];
            $contador = $procesos[0][0]['contador'];
            
            $oASINC->actualizar($contador, $total, "$contador / $total - LIQUIDANDO SOCIOS ...");
            
            if($contador >= $total){
                $oASINC->actualizar($contador, $total, "FINALIZANDO ...");
                $oASINC->fin("**** PROCESO FINALIZADO ****");
                break;
            }
            
            if($oASINC->detenido()){
                $STOP = TRUE;
                $sql = "SELECT id,shell_pid FROM asincronos as Asincrono WHERE trim(p13) = $pid;";
                $procesos = $oASINC->query($sql);
                foreach ($procesos as $proceso){
                    $proceso['Asincrono']['estado'] = 'S';
                    $SHELL->kill ($proceso['Asincrono']['shell_pid']);
                    $oASINC->save($asincrono);
                    
                }
            }
            
        }
        $oASINC->auditable = TRUE;
//        if(!$STOP):
//            $oASINC->actualizar(99,100,"ACTUALIZANDO CABECERA DE LIQUIDACION...");
//            #actualizo la cabecera
//            $this->__actualizarCabecera($this->liquidacionID,$pid);
//            $this->__setCampo('bloqueada',0);
//        endif;

//         App::import('Model', 'Mutual.Liquidacion');
//         $oLQ = new Liquidacion();
//         $data = array('Liquidacion' => array(
//             'id' => $this->liquidacionID,
//             'tipo_liquida' => $tipoLiquidacion
//         ));
//         $oLQ->save($data);
        
        if(!$STOP):
            $this->__actualizarCabecera($this->liquidacionID,$pid,$tipoLiquidacion);
            $oASINC->actualizar(100,100,"FINALIZANDO...");
            $oASINC->fin("**** PROCESO FINALIZADO ****");	
            $this->__setCampo('bloqueada',0);	
        endif;        
        

    }

    #FIN PROCESO DE LIQUIDACION
//	###############################################################################################################################
//	# METODOS DE LA CLASE
//	###############################################################################################################################
//	
    /**
     * devuelve un campo especificado de la tabla liquidaciones
     * @param $field
     * @return contenido del campo
     */
    function __getCampo($field) {
        App::import('Model', 'Mutual.Liquidacion');
        $oLQ = new Liquidacion();
        $oLQ->auditable = FALSE;
        $liquidacion = $oLQ->read($field, $this->liquidacionID);
        return $liquidacion['Liquidacion'][$field];
    }

    /**
     * setea un valor de un campo para la liquidacion
     * @param $field
     * @param $value
     */
    function __setCampo($field, $value) {
        App::import('Model', 'Mutual.Liquidacion');
        $oLQ = new Liquidacion();
        $oLQ->auditable = FALSE;
        $oLQ->id = $this->liquidacionID;
        return $oLQ->saveField($field, $value);
    }

    /**
     * genera la cabecera de la liquidacion
     * @param $periodo
     * @return unknown_type
     */
    function __generarCabeceraLiquidacion($periodo, $organismo, $pre_imputacion,$tipoLiquidacion) {
        App::import('Model', 'Mutual.Liquidacion');
        $oLiq = new Liquidacion();
        $oLiq->auditable = TRUE;
        
        //verifico que no exista
        $liq = $oLiq->find('all', array('conditions' => array('Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.periodo' => $periodo, 'Liquidacion.en_proceso' => 1), 'fields' => array('Liquidacion.id'), 'limit' => 1));
        if (!empty($liq))
            return $liq['Liquidacion']['id'];
        $data = array('Liquidacion' => array(
                'periodo' => $periodo,
                'codigo_organismo' => $organismo,
                'sobre_pre_imputacion' => $pre_imputacion,
            'tipo_liquida' => $tipoLiquidacion
        ));
        
        
        if ($oLiq->save($data))
            return $oLiq->getLastInsertID();
        else
            return null;
    }

    
    function actualizar_prioridad_imputacion_organismo($organismo){
        App::import('Model', 'Mutual.Liquidacion');
        $oLiq = new Liquidacion();
        $oLiq->auditable = FALSE;        
        $sql = "select id from proveedores 
                where id not in (select proveedor_id from proveedor_prioridad_imputa_organismos
                where codigo_organismo = '$organismo');";
        $datos = $oLiq->query($sql);
        if(!empty($datos)){
            $prioridades = array();
            foreach($datos as $dato){
                array_push($prioridades, array('proveedor_id' => $dato['proveedores']['id'], 'codigo_organismo' => $organismo, 'prioridad' => ($dato['proveedores']['id'] == 18 ? 1 : 5)) );
            }
            App::import('model','proveedores.ProveedorPrioridadImputaOrganismo');
            $oPIP = new ProveedorPrioridadImputaOrganismo(); 
            if(!empty($prioridades)) $oPIP->saveAll ($prioridades);            
        }        
    }
    
    /**
     * actualiza la cabecera de la liquidacion
     * @param $id
     * @param $pid
     * @return unknown_type
     */
    function __actualizarCabecera($id, $pid,$tipoLiquidacion) {
        App::import('Model', 'Mutual.Liquidacion');
        App::import('Model', 'Mutual.LiquidacionCuota');
        App::import('Model', 'Mutual.LiquidacionSocio');

        $oLiq = new Liquidacion();
        $oDet = new LiquidacionCuota();
        $oLS = new LiquidacionSocio();
        
        $oLiq->auditable = FALSE;
        $oDet->auditable = FALSE;
        $oLS->auditable = FALSE;

        $liquidacion = $oLiq->read(null, $id);

        $liquidacion['Liquidacion']['pid'] = $pid;
        $liquidacion['Liquidacion']['cuota_social_vencida'] = $oDet->getTotalCuotaSocial($id, 1);
        $liquidacion['Liquidacion']['cuota_social_periodo'] = $oDet->getTotalCuotaSocial($id, 0);

        $liquidacion['Liquidacion']['deuda_vencida'] = $oDet->getTotalDeuda($id, 1);
        $liquidacion['Liquidacion']['deuda_periodo'] = $oDet->getTotalDeuda($id, 0);

        $liquidacion['Liquidacion']['total_vencido'] = $oDet->getTotalVencido($id, 1);
        $liquidacion['Liquidacion']['total_periodo'] = $oDet->getTotalVencido($id, 0);
        $liquidacion['Liquidacion']['total'] = $oDet->getTotal($id);
        $liquidacion['Liquidacion']['en_proceso'] = 0;

        $liquidacion['Liquidacion']['registros_enviados'] = $oLS->getTotalRegistros($id);
        $liquidacion['Liquidacion']['altas'] = $oLS->getTotalAltas($id, $liquidacion['Liquidacion']['codigo_organismo']);
        $liquidacion['Liquidacion']['scoring'] = 1;
//         $liquidacion['Liquidacion']['tipo_liquida'] = $tipoLiquidacion;

        return $oLiq->save($liquidacion);
    }

//	/**
//	 * graba el detalle de la liquidacion en base a una cuota pasada por parametro
//	 * si el saldo de la cuota es negativo no la liquida
//	 * @param $id
//	 * @param $cuota
//	 * @param $periodoLiquidado
//	 * @return unknown_type
//	 */
//	function __grabarDetalleLiquidacion($id,$cuota,$periodoLiquidado){
//		App::import('Model','Mutual.LiquidacionCuota');
//		$oDet = new LiquidacionCuota();
//		return $oDet->generarDetalleLiquidacionCuota($id,$periodoLiquidado,$cuota);	
//	}
//	
//
    /**
     * Borra la liquidacion abierta para un periodo y organismo
     * @param $periodo
     * @param $organismo
     * @return unknown_type
     */
    function __borrarLiquidacion($periodo, $organismo, $dejarCabecera = false) {
        App::import('Model', 'Mutual.Liquidacion');
        App::import('Model', 'Mutual.LiquidacionCuota');
        App::import('Model', 'Mutual.LiquidacionSocio');
        App::import('Model', 'Mutual.MutualAdicionalPendiente');
        App::import('Model', 'Mutual.LiquidacionIntercambio');
        App::import('Model', 'Mutual.LiquidacionSocioRendicion');

        App::import('Model', 'Mutual.LiquidacionSocioEnvio');
        App::import('Model', 'Mutual.LiquidacionSocioEnvioRegistro');


        $oLiq = new Liquidacion();
        $oDet = new LiquidacionCuota();
        $oLSoc = new LiquidacionSocio();
        $oAdic = new MutualAdicionalPendiente();
        $oLI = new LiquidacionIntercambio();
        $oSR = new LiquidacionSocioRendicion();

        $oLSE = new LiquidacionSocioEnvio();
        $oLSER = new LiquidacionSocioEnvioRegistro();
        
        $oLiq->auditable = FALSE;
        $oDet->auditable = FALSE;
        $oLSoc->auditable = FALSE;
        $oAdic->auditable = FALSE;
        $oLI->auditable = FALSE;
        $oSR->auditable = FALSE;
        $oLSE->auditable = FALSE;
        $oLSER->auditable = FALSE;

        $liquidaciones = $oLiq->find('all', array('conditions' => array('Liquidacion.periodo' => $periodo, 'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.cerrada' => 0)));
        foreach ($liquidaciones as $liquidacion) {
            if ($liquidacion['Liquidacion']['cerrada'] == 0) {
                
                $oDet->deleteAll("LiquidacionCuota.liquidacion_id = " . $liquidacion['Liquidacion']['id'], false);
                $oLSoc->deleteAll("LiquidacionSocio.liquidacion_id = " . $liquidacion['Liquidacion']['id'], false);
                $oAdic->deleteAll("MutualAdicionalPendiente.liquidacion_id = " . $liquidacion['Liquidacion']['id'], false);
//                $oLI->borrarIntercambioByLiquidacion($liquidacion['Liquidacion']['id']);
//                $oSR->deleteAll("LiquidacionSocioRendicion.liquidacion_id = " . $liquidacion['Liquidacion']['id'], false);
//                $oLSER->deleteAll("LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id IN (select id from liquidacion_socio_envios where liquidacion_id = " . $liquidacion['Liquidacion']['id'] . ")", false);
//                $oLSE->deleteAll("LiquidacionSocioEnvio.liquidacion_id = " . $liquidacion['Liquidacion']['id'], false);

                $oLSE->query("delete from liquidacion_socio_scores where liquidacion_id = " . $liquidacion['Liquidacion']['id']);
                $oLI->query("update liquidacion_intercambios set procesado = 0 where  liquidacion_id = " . $liquidacion['Liquidacion']['id']);
                
                if (!$dejarCabecera){
                    
                    $oLI->borrarIntercambioByLiquidacion($liquidacion['Liquidacion']['id']);
                    $oSR->deleteAll("LiquidacionSocioRendicion.liquidacion_id = " . $liquidacion['Liquidacion']['id'], false);
                    $oLSER->deleteAll("LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id IN (select id from liquidacion_socio_envios where liquidacion_id = " . $liquidacion['Liquidacion']['id'] . ")", false);
                    $oLSE->deleteAll("LiquidacionSocioEnvio.liquidacion_id = " . $liquidacion['Liquidacion']['id'], false);
                    $oLiq->del($liquidacion['Liquidacion']['id'], false);
                    
                }    
            }
        }
    }

    /**
     * verifica si esta procesandose la liquidacion para el periodo y organismo en otra instancia
     * @param unknown_type $periodo
     * @param unknown_type $organismo
     */
    function __isBloqueada($periodo, $organismo) {
        App::import('Model', 'Mutual.Liquidacion');
        $oLiq = new Liquidacion();
        $oLiq->auditable = FALSE;
        $cantidad = $oLiq->find('count', array('conditions' => array('Liquidacion.periodo' => $periodo, 'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.bloqueada' => 1)));
        if ($cantidad == 0)
            return false;
        else
            return true;
    }

    function __isCerrada($periodo, $organismo) {
        App::import('Model', 'Mutual.Liquidacion');
        $oLiq = new Liquidacion();
        $oLiq->auditable = FALSE;
        $cantidad = $oLiq->find('count', array('conditions' => array('Liquidacion.periodo' => $periodo, 'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.cerrada' => 1)));
        if ($cantidad == 0)
            return false;
        else
            return true;
    }

    function __isImputada($periodo, $organismo) {
        App::import('Model', 'Mutual.Liquidacion');
        $oLiq = new Liquidacion();
        $oLiq->auditable = FALSE;
        $cantidad = $oLiq->find('count', array('conditions' => array('Liquidacion.periodo' => $periodo, 'Liquidacion.codigo_organismo' => $organismo, 'Liquidacion.imputada' => 1)));
        if ($cantidad == 0)
            return false;
        else
            return true;
    }

    /**
     * Chequea que no exista una liquidacion abierta para el periodo / organismo
     * si existe devuelve el ID para reliquidar, si NO existe devuelve 0
     * @param unknown_type $periodo
     * @param unknown_type $organismo
     * @return unknown_type
     */
    function ifExists($periodo, $organismo) {
        App::import('Model', 'Mutual.Liquidacion');
        $oLiq = new Liquidacion();
        $oLiq->auditable = FALSE;
        $conditions = array();
        $conditions['Liquidacion.periodo'] = $periodo;
        $conditions['Liquidacion.codigo_organismo'] = $organismo;
//		$conditions['Liquidacion.cerrada'] = 0;
//		$conditions['Liquidacion.imputada'] = 0;
//		$conditions['Liquidacion.archivos_procesados'] = 0;
        $liquidacion = $oLiq->find('all', array('conditions' => $conditions));
        if (empty($liquidacion))
            return 0;
        $liquidacion = $liquidacion[0];
        if (isset($liquidacion['Liquidacion']['id']) && $liquidacion['Liquidacion']['id'] != 0)
            return $liquidacion['Liquidacion']['id'];
        else
            return 0;
    }

    function __actualizarFechaCreated($id) {
        App::import('Model', 'Mutual.Liquidacion');
        $oLiq = new Liquidacion();
        $oLiq->auditable = FALSE;
        $liquidacion = $oLiq->read('created', $id);
        $liquidacion['Liquidacion']['created'] = date('Y-m-d H:i:s');
        $oLiq->save($liquidacion);
    }

}

?>
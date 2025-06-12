<?php
/**
 *
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage controller
 */

class LiquidacionesController extends MutualAppController{

	var $name = 'Liquidaciones';
	var $dir_up;
	var $dir_url;


	var $autorizar = array(
							'resumen_cruce_informacion',
							'resumen_cruce_informacion_detalle_codigo_pdf',
							'resumen_cruce_informacion_no_encontrados_pdf',
							'grilla_importacion_ajax',
							'resumen_cruce_informacion_anses',
							'resumen_cruce_informacion_cjp',
							'resumen_cruce_informacion_cbu',
							'registros_enviados_no_encontrados_pdf',
							'reintegros_pdf',
							'altas_no_cobradas_pdf',
							'reporte_proveedores',
							'diskette_cbu_txt',
							'diskette_cbu_pdf',
							'diskette_cjp_txt',
							'diskette_cjp_pdf',
							'detalle_turno_pdf',
							'imputados',
							'detalle_turno_diskette',
							'intercambio_proveedores',
							'detalle_archivo',
							'reporte_general_imputacion',
							'detalle_archivo_general',
							'reporte_control_vtos',
							'addRecibo',
							'editRecibo',
							'addRecibojp',
							'editRecibojp',
							'cargar_renglones',
							'cargar_renglones_remover',
							'reintegros_xls',
							'getPeriodosImputados',
							'imputar_comercios',
							'control_diskette_cjp',
							'anular_facturas',
							'diskette_cjp_nocob_cbu',
							'cierre_liquidacion',
							'imprimir_recibo',
							'exportar2',
							'listado_recuperos',
							'get_ultimo_periodo_cerrado',
							'get_periodos_disponibles',
							'get_intercambio',
							'combo_periodos_ajax',
							'importar_generar_lote_download',
							'exportar_masivo',
                            'resumen_cruce_informacion_no_encontrados_xls',
                            'getPeriodosFacturados',
                            'scoring',
                            'consolidado',
                            'consulta2',
                            'proceso_nuevo',
                            'importar_nuevo',
                            'importar_nuevo_recibo',
                            'mora_cuota_uno_pdf',
                            'mora_cuota_uno_xls',
                            'mora_temprana_pdf',
                            'mora_temprana_xls',
                            'proceso_noimp',
                            'exportar3',
                            'diskette_cbu_pdf3',
                            'detalle_turno_diskette3',
                            'detalle_turno_pdf3',
                            'diskette_cjp_txt3',
							'resumencontrol',
							'exportar_sp',
							'resumen_control_diskette_sp'
							,'liquida_deuda_sp'
							,'liquida_deuda_sp_execute'
            ,'notificacion'
            ,'notifica_deuda_envio'
            ,'notifica_deuda_detalle'
	);

	function beforeFilter(){

		$this->Seguridad->allow($this->autorizar);
		parent::beforeFilter();

		$this->dir_up = 'files' . DS . 'intercambio' . DS;
		$this->dir_url = 'files/intercambio/';

	}

	function index(){
		$this->redirect('consulta');
	}

	/**
	 * muestra la liquidacion de un socio para un periodo determinado (por ajax).
	 * Esta funcion tambien se usa para generar el PDF
	 * @param $socio_id
	 * @param $menuPersonas
	 * @param $toPDF
	 */
	function by_socio($socio_id,$menuPersonas=1,$toPDF=0,$periodo=null,$periodo_hasta=null){

		$liquidaciones = null;

		App::import('Model','Pfyj.Socio');
		$oSocio = new Socio();
		$oSocio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $oSocio->read(null,$socio_id);
		$this->set('socio',$socio);
		$this->set('menuPersonas',$menuPersonas);

		$this->set('socio_calificaciones',$oSocio->getResumenCalificaciones($socio_id,true));

		App::import('model','Mutual.LiquidacionSocio');
		$oLiqSoc = new LiquidacionSocio();

		App::import('model','Mutual.LiquidacionCuota');
		$oLCUO = new LiquidacionCuota();		

		$this->set('periodo',$periodo);

		$periodo_hasta = (!empty($periodo_hasta) ? $periodo_hasta : $periodo);

		$this->set('periodo_hasta',$periodo_hasta);

		if(!empty($this->data)){
			$liquidaciones = $oLiqSoc->liquidacionesByPeriodoBySocio($socio_id,$this->data['LiquidacionSocio']['periodo'],$this->data['LiquidacionSocio']['periodo_hasta'],$this->data['LiquidacionSocio']['codigo_organismo'],$this->data['LiquidacionSocio']['proveedor_id']);
			$this->set('periodo',$this->data['LiquidacionSocio']['periodo']);
			$this->set('periodo_hasta',$this->data['LiquidacionSocio']['periodo_hasta']);
		}

		// $periodos = $oLiqSoc->periodosBySocio($socio_id,'DESC');
		$periodos = $oLCUO->periodosBySocio($socio_id,'DESC');

		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$periodosLiquidados = $oLiq->getPeriodosLiquidados();

		$this->set('periodos',$periodos);
		$this->set('liquidaciones',$liquidaciones);

		$this->set('periodos_liquidados',$periodosLiquidados);


		if ($this->RequestHandler->isAjax()){
			$this->disableCache();
			$this->set('mostar_controles',($periodo_hasta == $periodo ? true : false));
			$this->render('by_socio_ajax','ajax');
		}else{
			if($toPDF==0){
				$this->render('by_socio');
			}
			else{
				$this->set('periodo',$periodo);
				$this->set('periodo_hasta',$periodo_hasta);
				$liquidaciones = $oLiqSoc->liquidacionesByPeriodoBySocio($socio_id,$periodo,$periodo_hasta);
				$this->set('liquidaciones',$liquidaciones);
				$this->render('reportes/by_socio_apaisado_pdf','pdf');
			}
		}


	}


	function generar_liquidacion_puntual($socio_id){
		if(!empty($this->data)){
			$this->redirect("/mutual/liquidacion_socios/reliquidar_by_socio/".$this->data['Liquidacion']['socio_id'].'/'.$this->data['Liquidacion']['periodo']);
		}else{
			$this->Mensaje->error("SE PRODUJO UN ERROR");
			$this->redirect("by_socio/$socio_id");
		}
	}

	function by_socio_periodo($socio_id,$periodo){

		App::import('Model','Pfyj.Socio');
		$oSocio = new Socio();
		$oSocio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $oSocio->read(null,$socio_id);
		$this->set('socio',$socio);

		//TRAIGO LA LIQUIDACION DEL SOCIO AL PERIODO
		App::import('model','Mutual.LiquidacionSocio');
		$oLiqSoc = new LiquidacionSocio();
		$oLiqSoc->bindModel(array('belongsTo' => array('Liquidacion')));

		$liquidaciones = $oLiqSoc->liquidacionesByPeriodoBySocio($socio_id,$periodo);

		$this->set('liquidaciones',$liquidaciones);
		$this->set('menuPersonas',0);
		$this->set('mostar_controles',false);

		$this->set('periodo',$periodo);
		$this->set('periodo_hasta',$periodo);

		$this->set('socio_id',$socio_id);
		$this->render('by_socio_ajax','ajax');
	}

	function proceso_noimp(){

        }

	/**
	 * PROCESO DE LIQUIDACION DE DEUDA
	 */
	function proceso(){
		$periodo = null;
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		
		$this->set('tiposLiquidacion',$oLiq->tiposLiquidacion);
		
		
		if(!empty($this->data)){
			$periodo = $this->data['Liquidacion']['periodo_ini']['year'].$this->data['Liquidacion']['periodo_ini']['month'];
			$this->set('organismo',$this->data['Liquidacion']['codigo_organismo']);
			$this->set('periodo',$periodo);
			$this->set('pre_imputacion',(isset($this->data['Liquidacion']['pre_imputacion']) ? 1 : 0));
            $this->set('tipo_deuda_liquida',(isset($this->data['Liquidacion']['tipo_deuda_liquida']) && !empty($this->data['Liquidacion']['tipo_deuda_liquida']) ? $this->data['Liquidacion']['tipo_deuda_liquida'] : 0));

			//verificar si esta cerrada
			if($oLiq->isCerrada($this->data['Liquidacion']['codigo_organismo'],$periodo)){
				$this->Mensaje->error('ATENCION!: LIQUIDACION CERRADA');
				$this->render('liquidar_deuda');
			}else if($oLiq->getUltimoPeriodoImputado($this->data['Liquidacion']['codigo_organismo']) > $periodo){
				$this->Mensaje->error('ATENCION!: EXISTEN PERIODOS POSTERIORES IMPUTADOS PARA EL ORGANISMO');
				$this->render('liquidar_deuda');

            }else{
				$archivos = $oLiq->isArchivosGenerados($this->data['Liquidacion']['codigo_organismo'],$periodo);
// 				debug($archivos);
				$this->set('archivos',$archivos);
				$this->render('liquidar_deuda_proceso');
			}
		}else{
			$this->set('periodo',$periodo);
			$this->render('liquidar_deuda');
		}
	}

	/**
	 * NO SE USA MAS (VER listados_controller.php --> reporte_liquidacion_deuda
	 * VISTA DEL RESUMEN DE LIQUIDACION DE DEUDA. SE UTILIZA TAMBIEN PARA GENERA EL PDF DE ACUERDO AL VALOR DEL PARAMETRO
	 * @param $periodo
	 * @param $organismo
	 * @param $toPDF
	 */
	function resumen_liquidacion_deuda($periodo,$organismo,$toPDF=0){

		parent::noDisponible();

//		if(!empty($this->params['url']['pid'])) $pid = $this->params['url']['pid'];
//
//		App::import('Model','Mutual.Liquidacion');
//		$oLiq = new Liquidacion();
//
//		$resumen = $oLiq->find('all',array('conditions' => array('Liquidacion.periodo' => $periodo,'Liquidacion.codigo_organismo' => $organismo)));
//
//		$this->set('periodo',$periodo);
//		$this->set('organismo',$organismo);
//		$this->set('resumen',$resumen[0]);
//
//		if(!empty($resumen)):
//
//			$this->set('resumen',$resumen[0]);
//
//			App::import('Model','Mutual.LiquidacionCuota');
//			$oDet = new LiquidacionCuota();
//
//			$deuda = $oDet->getResumenLiquidacionByProveedor($resumen[0]['Liquidacion']['id'],$periodo);
//
//			App::import('Model','Mutual.LiquidacionSocio');
//			$oSoc = new LiquidacionSocio();
//
//			$socios = $oSoc->getLiquidacionByID($resumen[0]['Liquidacion']['id'],$organismo);
//
//			$this->set('deuda',$deuda);
//
//			$this->set('socios',$socios);
//
//
//		else:
//
//			$this->set('resumen',null);
//
//		endif;
//
//		if($toPDF == 0)$this->render('resumen_liquidacion_deuda');
//		else $this->render('reportes/resumen_liquidacion_deuda_pdf','pdf');
	}

	/**
	 * VISTA CONSULTA DE LIQUIDACIONES
	 */
	function consulta(){
            $liquidaciones = null;
            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
//            $liquidaciones = $oLiq->find('all',array('conditions' => array('Liquidacion.en_proceso' => 0),'order'=>array('Liquidacion.periodo DESC','Liquidacion.codigo_organismo ASC')));

			$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);

            // if(isset($INI_FILE['general']['consulta_liquidaciones_totalizada']) && $INI_FILE['general']['consulta_liquidaciones_totalizada'] == 1) $liquidaciones = $oLiq->getLiquidaciones();
            // else $liquidaciones = $oLiq->find('all',array('conditions' => array('Liquidacion.en_proceso' => 0),'order'=>array('Liquidacion.periodo DESC','Liquidacion.codigo_organismo ASC')));

			// debug($liquidaciones);
			// exit;
			$liquidaciones = $oLiq->getLiquidaciones();
            $this->set('liquidaciones',$liquidaciones);


			$render = (isset($INI_FILE['general']['consulta_liquidaciones_totalizada'])
					&& $INI_FILE['general']['consulta_liquidaciones_totalizada'] == '1'
					? "consulta_totalizada" : "consulta");

            $this->render($render);
	}

	/**
	 * EXPORTAR DATOS (SE REDIRECCIONA AL METODO EXPORTAR2 QUE TRABAJA CON UN ASINCRONO PARA GENERAR
	 * EL DISKETTE
	 *
	 * @author adrian [03/02/2012]
	 * @param int $id
	 */
	function exportar($id=null){

		if(empty($id))$this->redirect('consulta');


		#########################################################################################################################
		# NUEVO ESQUEMA DE EXPORTACION
		#########################################################################################################################
		$this->redirect("exportar2/$id");
		#########################################################################################################################


		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		$liquidacion = $oLiq->cargar($id);
		$this->set('liquidacion',$liquidacion);

		$organismo = substr($liquidacion['Liquidacion']['codigo_organismo'],8,2);

		if(!empty($this->data)):


			$this->data['LiquidacionSocio']['turno_pago'] = array_keys($this->data['LiquidacionSocio']['turno_pago']);

			$this->Session->del($this->name.'.turnos_token');
			$this->Session->del($this->name.'.banco_intrecambio_token');
			$this->Session->del($this->name.'.fecha_debito_token');
			$this->Session->del($this->name.'.nro_archivo_token');

			$turnos = base64_encode(serialize($this->data['LiquidacionSocio']['turno_pago']));
			$fechaDebito = base64_encode(serialize($this->data['LiquidacionSocio']['fecha_debito']));

			$this->Session->write($this->name.'.turnos_token', $turnos);
			$this->Session->write($this->name.'.banco_intrecambio_token', $this->data['LiquidacionSocio']['banco_intercambio']);
			$this->Session->write($this->name.'.fecha_debito_token', $fechaDebito);
			$this->Session->write($this->name.'.nro_archivo_token', !empty($this->data['LiquidacionSocio']['nro_archivo']) ? $this->data['LiquidacionSocio']['nro_archivo'] : '01');

//			$socios = $oLS->generarDisketteCBU($this->data);
			$socios = $oLS->generarDisketteCBUNuevo($this->data);


			$this->Session->write("DISKETTE_" . $socios['diskette']['uuid'] ,base64_encode(serialize($socios)));
			$this->set('DISKETTE_UUID',$socios['diskette']['uuid']);

//			if(empty($socios)){
			if(empty($socios['info_procesada'])){
				$this->Mensaje->error("ERROR: No existen registros para el pre-procesamiento del diskette!");
				$this->redirect('exportar/'.$id);
				return;
			}

//			$this->set('socios',$socios);
			$this->set('socios',$socios['info_procesada']);
			$this->set('turnos',$turnos);
			$this->set('banco_intercambio',$this->data['LiquidacionSocio']['banco_intercambio']);
			$this->set('fechaDebito',$oLS->armaFecha($this->data['LiquidacionSocio']['fecha_debito']));

			$this->set('banco_intercambio',$this->data['LiquidacionSocio']['banco_intercambio']);

			switch($organismo){
				case 22:
					$this->render('exportar_p2_registros_cbu');
					break;
				case 77:
					$this->render('exportar_cjp');
					break;
				case 66:
					break;
			}

		else:

			$this->set('turnos',$oLS->getResumenByTurno($id));


			switch($organismo){
				case 22:
					//cbu muestro el paso 1 donde elijo los turnos
					$this->render('exportar_p1_cbu_turnos');
					break;
				case 77:
					//caja de jubilaciones ya mando directamente la salida
					$socios = $oLS->generarDisketteCJP($id);
					$this->set('socios',$socios);
					$this->render('exportar_cjp');
					break;
			}

		endif;

	}


        /**
         * SACA LOS DATOS DE LAS TABLAS NUEVAS DE LAS LIQUIDACIONES NO IMPUTADAS
         * 2019-03-02
         * @param type $id
         * @param type $toXLS
         * @return type
         */
	function exportar3($id=null,$toXLS=FALSE){

            if (empty($id)) {
                $this->redirect('consulta');
            }

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            App::import('Model','Mutual.LiquidacionSocioNoimputada');
            $oLS = new LiquidacionSocioNoimputada();

            App::import('Model','Mutual.LiquidacionSocioEnvio');
            $oLSE = new LiquidacionSocioEnvio();

            $liquidacion = $oLiq->cargar($id);

            #########################################################################################################################
            # SI ESTA IMPUTADA LA MANDO A LA VISTA ANTERIOR
            #########################################################################################################################
            if($liquidacion['Liquidacion']['imputada']){
                $this->redirect("exportar2/$id");
            }
            #########################################################################################################################


            $this->set('liquidacion',$liquidacion);

            $organismo = substr($liquidacion['Liquidacion']['codigo_organismo'],8,2);

            #########################################################################
            # ACTUALIZAR TURNOS
            #########################################################################
            App::import('Model','Mutual.LiquidacionTurno');
            $oLT = new LiquidacionTurno();
            $oLT->importar_empresas();

		if(!empty($this->data)):

//                        debug($this->data);

			$this->data['LiquidacionSocioNoimputada']['fecha_debito'] = $oLS->armaFecha($this->data['LiquidacionSocioNoimputada']['fecha_debito']);
			$this->data['LiquidacionSocioNoimputada']['fecha_presentacion'] = $oLS->armaFecha($this->data['LiquidacionSocioNoimputada']['fecha_presentacion']);
			$this->data['LiquidacionSocioNoimputada']['turno_pago_array'] = array_keys($this->data['LiquidacionSocioNoimputada']['turno_pago']);
			$this->data['LiquidacionSocioNoimputada']['banco_intercambio_desc'] = $oLiq->getNombreBanco($this->data['LiquidacionSocioNoimputada']['banco_intercambio']);
			$this->data['LiquidacionSocioNoimputada']['fecha_maxima'] = $oLS->armaFecha($this->data['LiquidacionSocioNoimputada']['fecha_maxima']);
			$this->set('datos',$this->data);
			$this->set('datos_serialized',serialize($this->data));
			$this->set('envios',$oLSE->getByLiquidacionId($id,false));
			switch($organismo){
				case 22:
					$this->render('exportar_p1_cbu_proceso3');
					break;
				case 77:
					$this->render('exportar_cjp3');
					break;
				case 66:
					break;
			}
		else:

			if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){

				$socios = $oLS->generarDisketteCBUFromAsincronoProcess($this->params['url']['pid']);

				$this->Session->write("DISKETTE_" . $socios['diskette']['uuid'] ,base64_encode(serialize($socios)));
				$this->Session->write("DISKETTE_" . $socios['diskette']['uuid'] ,serialize($socios));
				$this->set('DISKETTE_UUID',$socios['diskette']['uuid']);
				$this->set('sociosByTurno',$socios['info_procesada_by_turno']);
				$this->set('errores',$socios['errores']);
				$this->set('datos',$socios['parametros']);
				$this->render('exportar_p2_registros_cbu3');
				return;
			}

//			$this->set('turnos',$oLS->getResumenByTurno($id));


                        $render = 'exportar_p1_cbu_turnos3';

			switch($organismo){
//				case 22:
//					//cbu muestro el paso 1 donde elijo los turnos
//					$this->set('turnos',$oLS->getResumenByTurno($id));
//					$render = 'exportar_p1_cbu_turnos2';
//					break;
				case 77:
					//caja de jubilaciones ya mando directamente la salida
//					$socios = $oLS->generarDisketteCJP($id);
					$socios = $oLS->getErroresDisketteCJP($id);
					$this->set('socios',$socios);
					$render = 'exportar_cjp3';
					break;
				default:
					$this->set('turnos',$oLS->getResumenByTurno($id));
					$render = 'exportar_p1_cbu_turnos3';
					break;
			}

                        if(!$toXLS)$this->render($render);
                        else $this->render("exportar_p1_cbu_turnos2_xls","blank");

		endif;

	}



        /**
	 * Nueva interface de exportacion mediante proceso asincrono
	 *
	 * @author adrian [30/01/2012]
	 * @param int $id
	 */
	function exportar2($id=null,$toXLS=FALSE){

            if (empty($id)) {
                $this->redirect('consulta');
            }

        App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		App::import('Model','Mutual.LiquidacionSocioEnvio');
		$oLSE = new LiquidacionSocioEnvio();

		$liquidacion = $oLiq->cargar($id);

//            if(!$liquidacion['Liquidacion']['imputada']){
//                $this->redirect("exportar3/$id");
//            }

		$this->set('liquidacion',$liquidacion);

		$organismo = substr($liquidacion['Liquidacion']['codigo_organismo'],8,2);

                #########################################################################
                # ACTUALIZAR TURNOS
                #########################################################################
		App::import('Model','Mutual.LiquidacionTurno');
		$oLT = new LiquidacionTurno();
                $oLT->importar_empresas();

		if(!empty($this->data)):

		    
		
			$this->data['LiquidacionSocio']['fecha_debito'] = $oLS->armaFecha($this->data['LiquidacionSocio']['fecha_debito']);
			$this->data['LiquidacionSocio']['fecha_presentacion'] = $oLS->armaFecha($this->data['LiquidacionSocio']['fecha_presentacion']);
			$this->data['LiquidacionSocio']['turno_pago_array'] = array_keys($this->data['LiquidacionSocio']['turno_pago']);
			$this->data['LiquidacionSocio']['banco_intercambio_desc'] = $oLiq->getNombreBanco($this->data['LiquidacionSocio']['banco_intercambio']);
			

			if(isset($this->data['LiquidacionSocio']['fecha_maxima'])){
			    $this->data['LiquidacionSocio']['fecha_maxima'] = $oLS->armaFecha($this->data['LiquidacionSocio']['fecha_maxima']);
			    $fechaMaxima = $this->data['LiquidacionSocio']['fecha_maxima'];
			}
			
			$this->set('datos',$this->data);
// 			$this->set('datos_serialized',base64_encode(serialize($this->data)));
			$this->set('datos_serialized',serialize($this->data));
			$this->set('envios',$oLSE->getByLiquidacionId($id,false));
//			$this->set('envios',null);
			// debug(($this->data));
			// exit;
			/************************************************************************************************************/
			// NUEVA FUNCIONALIDAD: NOV/2020 - ADRIAN
			// SI LA FUNCION DEVUELVE EL UUID DIRECTAMENTE VA A LA PAGINA DE DESCARGA
			// ESTO TRABAJA CON LOS PROCEDIMIENTOS ALMACENADOS PARA GENERAR EL DISKETTE
			/************************************************************************************************************/
			$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
			if(isset($INI_FILE['intercambio']['genera_diskete_sp']) && $INI_FILE['intercambio']['genera_diskete_sp'] != ""){
				$parametrosGlobales = array();
				if(isset($this->data['LiquidacionSocio']['nro_archivo'])){
					$parametrosGlobales['nro_archivo'] = (empty($this->data['LiquidacionSocio']['nro_archivo']) ? 1 : $this->data['LiquidacionSocio']['nro_archivo']);
				}
				if(isset($this->data['LiquidacionSocio']['nro_convenio_cba']) && !empty($this->data['LiquidacionSocio']['nro_convenio_cba'])){
					$parametrosGlobales['nro_convenio_cba']=$this->data['LiquidacionSocio']['nro_convenio_cba'];
				}
				if(isset($this->data['LiquidacionSocio']['fecha_maxima']) && !empty($fechaMaxima)){
					$parametrosGlobales['fecha_maxima']=$fechaMaxima;
				}
				if(isset($this->data['LiquidacionSocio']['nro_ciclos']) && !empty($this->data['LiquidacionSocio']['nro_ciclos'])){
					$parametrosGlobales['nro_ciclos']=$this->data['LiquidacionSocio']['nro_ciclos'];
				}
				$empresas = array();
				$turnos = array();
				foreach($this->data['LiquidacionSocio']['turno_pago'] as $clave => $valor ){
					list($empresa,$turno) = explode('|',$clave);
					array_push($empresas,$empresa);
					array_push($turnos,$turno);
				}									
				$uuid = $oLSE->genDiskette(
					$this->data['LiquidacionSocio']['banco_intercambio'], 
					$id,
					$this->data['LiquidacionSocio']['fecha_debito'],
					$this->data['LiquidacionSocio']['fecha_presentacion'],
					$empresas,
					$turnos,
					$parametrosGlobales		
				);
				if(!empty($uuid)){
					$this->redirect("exportar_sp/$id/$uuid");
				}					
			} 			

			/************************************************************************************************************/

			switch($organismo){
				case 22:
					$this->render('exportar_p1_cbu_proceso');
					break;
				case 77:
					$this->render('exportar_cjp');
					break;
				case 66:
					break;
			}

		else:

			if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){

				$socios = $oLS->generarDisketteCBUFromAsincronoProcess($this->params['url']['pid']);
				$this->Session->write("DISKETTE_" . $socios['diskette']['uuid'] ,base64_encode(serialize($socios)));
				$this->Session->write("DISKETTE_" . $socios['diskette']['uuid'] ,serialize($socios));
				$this->set('DISKETTE_UUID',$socios['diskette']['uuid']);
				$this->set('sociosByTurno',$socios['info_procesada_by_turno']);
				$this->set('errores',$socios['errores']);
				$this->set('datos',$socios['parametros']);
				$this->render('exportar_p2_registros_cbu2');
				return;
			}

//			$this->set('turnos',$oLS->getResumenByTurno($id));


                        $render = 'exportar_p1_cbu_turnos2';

			switch($organismo){
//				case 22:
//					//cbu muestro el paso 1 donde elijo los turnos
//					$this->set('turnos',$oLS->getResumenByTurno($id));
//					$render = 'exportar_p1_cbu_turnos2';
//					break;
				case 77:
					//caja de jubilaciones ya mando directamente la salida
//					$socios = $oLS->generarDisketteCJP($id);
					$socios = $oLS->getErroresDisketteCJP($id);
					$this->set('socios',$socios);
					$render = 'exportar_cjp';
					break;
				default:
					$this->set('turnos',$oLS->getResumenByTurno($id));
					$render = 'exportar_p1_cbu_turnos2';
					break;
			}

                        if(!$toXLS)$this->render($render);
                        else $this->render("exportar_p1_cbu_turnos2_xls","blank");

		endif;

	}

	function exportar_sp($id,$uuid){
		if (empty($id) || empty($uuid)){
			parent::noDisponible();
		}
        App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $oLiq->cargar($id);
		$this->set('liquidacion',$liquidacion);		
		App::import('Model','Mutual.LiquidacionSocioEnvio');
		$oLSE = new LiquidacionSocioEnvio();
		$diskette = $oLSE->getDiskette($id,$uuid);
		$this->Session->write("DISKETTE_" . $uuid ,serialize($diskette));
		$this->set('DISKETTE_UUID',$uuid);
		$this->set('diskette',$diskette);
	}


	function detalle_turno_pdf3($id=null,$turno=null,$output = 'PDF'){

		if(empty($id) || empty($turno))$this->redirect('consulta');

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		App::import('Model','Mutual.LiquidacionSocioNoimputada');
		$oLS = new LiquidacionSocioNoimputada();

//		$socios = $oLS->getDatosParaDisketteCBU($id,$turno,0,false);
//		$socios_error_cbu = $oLS->getDatosParaDisketteCBU($id,$turno,1,false);

		$socios = $oLS->getDatosParaDisketteCBUReporteByTurno($id,$turno,false);
//		$socios_error_cbu = $oLS->getDatosParaDisketteCBUReporteByTurno($id,$turno,true);
                $socios_error_cbu = null;

//                debug($socios);
//                exit;

		$liquidacion = $oLiq->cargar($id);


		$this->set('liquidacion',$liquidacion);

		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
		$this->set('descripcion_turno',$oTURNO->getDescripcionByTruno($turno));

		$this->set('socios',$socios);
		$this->set('socios_error_cbu',$socios_error_cbu);
		if($output == "PDF")$this->render('reportes/detalle_turno_pdf3','pdf');
		if($output == "XLS")$this->render('reportes/detalle_turno_xls3','blank');
	}


	function detalle_turno_pdf($id=null,$turno=null,$output = 'PDF'){

		if(empty($id) || empty($turno))$this->redirect('consulta');

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

//		$socios = $oLS->getDatosParaDisketteCBU($id,$turno,0,false);
//		$socios_error_cbu = $oLS->getDatosParaDisketteCBU($id,$turno,1,false);

		$socios = $oLS->getDatosParaDisketteCBUReporteByTurno($id,$turno,false);
//		$socios_error_cbu = $oLS->getDatosParaDisketteCBUReporteByTurno($id,$turno,true);
        $socios_error_cbu = null;

		$liquidacion = $oLiq->cargar($id);


		$this->set('liquidacion',$liquidacion);

		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
		$this->set('descripcion_turno',$oTURNO->getDescripcionByTruno($turno));

		$this->set('socios',$socios);
		$this->set('socios_error_cbu',$socios_error_cbu);
		if($output == "PDF")$this->render('reportes/detalle_turno_pdf','pdf');
		if($output == "XLS")$this->render('reportes/detalle_turno_xls','blank');
	}

	function diskette_cjp_txt3($id=null,$nuevoFormato=1,$codDto=0,$altaBaja = 'T'){
		if(empty($id))$this->redirect('consulta');
		App::import('Model','Mutual.LiquidacionSocioNoimputada');
		$oLS = new LiquidacionSocioNoimputada();
		$orden = array('LiquidacionSocioNoimputada.tipo,LiquidacionSocioNoimputada.nro_ley,LiquidacionSocioNoimputada.nro_beneficio,LiquidacionSocioNoimputada.sub_beneficio');
		if($nuevoFormato == 1)$socios = $oLS->getDatosParaDisketteCJP($id,$orden,$codDto,$altaBaja);
		else $socios = $oLS->getDatosParaDisketteCJPFormatoAnterior($id,$orden,$codDto);
//                debug($socios);
//		exit;
		if(empty($socios)){
			$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
			$this->redirect('exportar/'.$id);
			return;
		}
		$this->set('socios',$socios);
		$this->set('codDto',$codDto);
		$this->set('nuevoFormato',$nuevoFormato);
		$this->set('altaBaja',$altaBaja);
		$this->render('intercambios/diskette_cjp_txt3','blank');
	}

	function diskette_cjp_txt($id=null,$nuevoFormato=1,$codDto=0,$altaBaja = 'T'){
		if(empty($id))$this->redirect('consulta');
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		$orden = array('LiquidacionSocio.tipo,LiquidacionSocio.nro_ley,LiquidacionSocio.nro_beneficio,LiquidacionSocio.sub_beneficio');
		if($nuevoFormato == 1)$socios = $oLS->getDatosParaDisketteCJP($id,$orden,$codDto,$altaBaja);
		else $socios = $oLS->getDatosParaDisketteCJPFormatoAnterior($id,$orden,$codDto);
//		exit;
		if(empty($socios)){
			$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
			$this->redirect('exportar/'.$id);
			return;
		}
		$this->set('socios',$socios);
		$this->set('codDto',$codDto);
		$this->set('nuevoFormato',$nuevoFormato);
		$this->set('altaBaja',$altaBaja);
		$this->render('intercambios/diskette_cjp_txt','blank');
	}

	/**
	 * listado soporte de los datos del txt
	 * @param $id
	 */
	function diskette_cjp_pdf($id=null,$nuevoFormato=1,$codDto=0,$altaBaja = 'T'){
		if(empty($id))$this->redirect('consulta');

		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

//		$socios = $oLS->getDatosParaDisketteCJP($id);
		if($nuevoFormato == 1)$socios = $oLS->getDatosParaDisketteCJP($id,array('LiquidacionSocio.apenom,LiquidacionSocio.registro'),$codDto,$altaBaja);
		else $socios = $oLS->getDatosParaDisketteCJPFormatoAnterior($id,array('LiquidacionSocio.apenom,LiquidacionSocio.registro'),$codDto);

		$errores = Set::extract("/LiquidacionSocio[error_cbu=1]",$socios);
		$socios = Set::extract("/LiquidacionSocio[error_cbu=0]",$socios);

		if(empty($socios)){
			$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
			$this->redirect('exportar/'.$id);
			return;
		}
		$liquidacion = $oLiq->cargar($id);
		$this->set('liquidacion',$liquidacion);
		$this->set('errores',$errores);
		$this->set('socios',$socios);
		$this->set('cuotaSocialAB',$altaBaja);
		$this->set('codDto',$codDto);
		if($nuevoFormato == 1 && $codDto == 1) $this->render('reportes/diskette_cjp_consumos_pdf','pdf');
		else $this->render('reportes/diskette_cjp_pdf','pdf');
	}

	/**
	 * genera el listado PDF con los datos del txt
	 * @param $id
	 */
	function diskette_cbu_pdf($id=null, $nvoFormato = 0 ,$uuid = null,$output="PDF"){

		if(empty($id))$this->redirect('consulta');

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$socios = null;
		$socios_error_cbu = null;
		$resumenTurnos = null;
		$bancoIntercambioNombre = null;

		$liquidacion = $oLiq->cargar($id);
		$this->set('liquidacion',$liquidacion);

                $output = (!empty($output) ? $output : "PDF");

		if($nvoFormato == 0):

			$turnosToken = $this->Session->read($this->name.'.turnos_token');
			$bancoIntercambioToken = $this->Session->read($this->name.'.banco_intrecambio_token');
			$fechaDebitoToken = $this->Session->read($this->name.'.fecha_debito_token');


			$turnos = unserialize(base64_decode($turnosToken));
			$fechaDebito = $oLiq->armaFecha(unserialize(base64_decode($fechaDebitoToken)));

			App::import('Model','Mutual.LiquidacionSocio');
			$oLS = new LiquidacionSocio();

			$socios = $oLS->getDatosParaDisketteCBU($id,$turnos,0,true,true);
			$socios_error_cbu = $oLS->getDatosParaDisketteCBU($id,$turnos,1,false);

			$resumenTurnos = $oLS->getResumenByTurnoDisketteCBU($id,$turnos);

			$bancoIntercambioNombre = $oLS->getNombreBanco($bancoIntercambioToken);
			
			if(empty($socios)){
				$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
				$this->redirect('exportar/'.$id);
				return;
			}


			$this->set('fechaDebito',$fechaDebito);
			$this->set('socios',$socios);
			$this->set('resumenTurnos',$resumenTurnos);
			$this->set('socios_error_cbu',$socios_error_cbu);
			$this->set('banco_intercambio',$bancoIntercambioNombre);
			if($output == "PDF")$this->render('reportes/diskette_cbu_pdf','pdf');
                        else $this->render('reportes/diskette_cbu_xls','blank');


		else:
			if(isset($_SESSION["DISKETTE_$uuid"])){
// 			if(session_is_registered("DISKETTE_$uuid")){

				$diskette = $_SESSION["DISKETTE_$uuid"];
// 				$diskette = base64_decode($diskette);
				$diskette = unserialize($diskette);

				$bancoIntercambioNombre = $diskette['parametros']['LiquidacionSocio']['banco_intercambio_desc'];
				// debug($diskette);
				// exit;
				if(empty($diskette['info_procesada'])){
					$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
					$this->redirect('exportar/'.$id);
					return;
				}
				$this->set('fechaDebito',$diskette['parametros']['LiquidacionSocio']['fecha_debito']);
				$this->set('datos',$diskette['info_procesada_by_turno']);
				$this->set('diskette',$diskette['diskette']);
				$this->set('banco_intercambio',$diskette['parametros']['LiquidacionSocio']['banco_intercambio_desc']);
				$this->set('errores',$diskette['errores']);
				if($output == "PDF") $this->render('reportes/diskette_cbu_pdf2','pdf');
                                else $this->render('reportes/diskette_cbu_xls','blank');

				return;

			}else{

				App::import('Model','Mutual.LiquidacionSocioEnvioRegistro');
				$oLSER = new LiquidacionSocioEnvioRegistro();

				$datos = $oLSER->getInfoParaReporte($uuid);

//				debug($datos);
//				exit;

				$this->set('fechaDebito',$datos['diskette']['fecha_debito']);
				$this->set('datos',$datos['info_procesada_by_turno']);
				$this->set('diskette',$datos['diskette']);
				$this->set('banco_intercambio',$datos['diskette']['banco_nombre']);
				$this->set('errores',null);
				if($output == "PDF") $this->render('reportes/diskette_cbu_pdf2','pdf');
                                else $this->render('reportes/diskette_cbu_xls','blank');

				//parent::noDisponible();
				exit;

			}

		endif;



	}


	/**
	 * 
	 */
	function resumen_control_diskette_sp($id=null, $uuid = null,$output="PDF"){
		if(empty($id))$this->redirect('consulta');

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$socios = null;
		$socios_error_cbu = null;
		$resumenTurnos = null;
		$bancoIntercambioNombre = null;

		$liquidacion = $oLiq->cargar($id);
		$this->set('liquidacion',$liquidacion);

		$output = (!empty($output) ? $output : "PDF");
		if(isset($_SESSION["DISKETTE_$uuid"])){
			$diskette = $_SESSION["DISKETTE_$uuid"];
			$diskette = unserialize($diskette);
			if(empty($diskette['diskette'])){
				$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
				$this->redirect('exportar/'.$id);
				return;
			}		
			$this->set('fechaDebito',$diskette['diskette']['fecha_debito']);
			$this->set('datos',$diskette['registros_ok']);
			$this->set('diskette',$diskette['diskette']);
			$this->set('resumen_operativo',$diskette['resumen_operativo']);
			$this->set('banco_intercambio',$diskette['diskette']['banco_intercambio_nombre']);
			$this->set('errores',$diskette['registros_error']);
			if($output == "PDF"){
				$this->render('reportes/diskette_cbu_pdf_sp','pdf');
			} else {
				$this->render('reportes/diskette_cbu_xls_sp','blank');
			}	
			return;


		}

	}

	/**
	 * genera el listado PDF con los datos del txt
	 * @param $id
	 */
	function diskette_cbu_pdf3($id=null, $nvoFormato = 0 ,$uuid = null,$output="PDF"){

		if(empty($id))$this->redirect('consulta');

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$socios = null;
		$socios_error_cbu = null;
		$resumenTurnos = null;
		$bancoIntercambioNombre = null;

		$liquidacion = $oLiq->cargar($id);
		$this->set('liquidacion',$liquidacion);

                $output = (!empty($output) ? $output : "PDF");

		if($nvoFormato == 0):

			$turnosToken = $this->Session->read($this->name.'.turnos_token');
			$bancoIntercambioToken = $this->Session->read($this->name.'.banco_intrecambio_token');
			$fechaDebitoToken = $this->Session->read($this->name.'.fecha_debito_token');


			$turnos = unserialize(base64_decode($turnosToken));
			$fechaDebito = $oLiq->armaFecha(unserialize(base64_decode($fechaDebitoToken)));

			App::import('Model','Mutual.LiquidacionSocioNoimputada');
			$oLS = new LiquidacionSocioNoimputada();

			$socios = $oLS->getDatosParaDisketteCBU($id,$turnos,0,true,true);
			$socios_error_cbu = $oLS->getDatosParaDisketteCBU($id,$turnos,1,false);

			$resumenTurnos = $oLS->getResumenByTurnoDisketteCBU($id,$turnos);

			$bancoIntercambioNombre = $oLS->getNombreBanco($bancoIntercambioToken);

			if(empty($socios)){
				$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
				$this->redirect('exportar/'.$id);
				return;
			}


			$this->set('fechaDebito',$fechaDebito);
			$this->set('socios',$socios);
			$this->set('resumenTurnos',$resumenTurnos);
			$this->set('socios_error_cbu',$socios_error_cbu);
			$this->set('banco_intercambio',$bancoIntercambioNombre);
			if($output == "PDF")$this->render('reportes/diskette_cbu_pdf','pdf');
                        else $this->render('reportes/diskette_cbu_xls','blank');


		else:
			if(isset($_SESSION["DISKETTE_$uuid"])){
// 			if(session_is_registered("DISKETTE_$uuid")){

				$diskette = $_SESSION["DISKETTE_$uuid"];
// 				$diskette = base64_decode($diskette);
				$diskette = unserialize($diskette);

//                                debug($diskette);
//                                exit;

				$bancoIntercambioNombre = $diskette['parametros']['LiquidacionSocioNoimputada']['banco_intercambio_desc'];

				if(empty($diskette['info_procesada'])){
					$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
					$this->redirect('exportar/'.$id);
					return;
				}
				$this->set('fechaDebito',$diskette['parametros']['LiquidacionSocioNoimputada']['fecha_debito']);
				$this->set('datos',$diskette['info_procesada_by_turno']);
				$this->set('diskette',$diskette['diskette']);
				$this->set('banco_intercambio',$diskette['parametros']['LiquidacionSocioNoimputada']['banco_intercambio_desc']);
				$this->set('errores',$diskette['errores']);
				if($output == "PDF") $this->render('reportes/diskette_cbu_pdf3','pdf');
                                else $this->render('reportes/diskette_cbu_xls3','blank');

				return;

			}else{

				App::import('Model','Mutual.LiquidacionSocioEnvioRegistro');
				$oLSER = new LiquidacionSocioEnvioRegistro();

				$datos = $oLSER->getInfoParaReporte($uuid);

//				debug($datos);
//				exit;

				$this->set('fechaDebito',$datos['diskette']['fecha_debito']);
				$this->set('datos',$datos['info_procesada_by_turno']);
				$this->set('diskette',$datos['diskette']);
				$this->set('banco_intercambio',$datos['diskette']['banco_nombre']);
				$this->set('errores',null);
				if($output == "PDF") $this->render('reportes/diskette_cbu_pdf2','pdf');
                                else $this->render('reportes/diskette_cbu_xls','blank');

				//parent::noDisponible();
				exit;

			}

		endif;



	}


	/**
	 * genera la descarga del txt
	 * @param $id
	 */
	function diskette_cbu_txt($id=null){

		if(empty($id))$this->redirect('consulta');

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

		$liquidacion = $oLiq->cargar($id);

		$this->set('liquidacion',$liquidacion);


		$turnosToken = $this->Session->read($this->name.'.turnos_token');
		$bancoIntercambioToken = $this->Session->read($this->name.'.banco_intrecambio_token');
		$fechaDebitoToken = $this->Session->read($this->name.'.fecha_debito_token');
		$nroArchivoToken = $this->Session->read($this->name.'.nro_archivo_token');


		$turnos = unserialize(base64_decode($turnosToken));

		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		$socios = $oLS->getDatosParaDisketteCBU($id,$turnos,0,true,true);

		if(empty($socios)){
			$this->Mensaje->error("ERROR: No existen registros para generar el diskette!");
			$this->redirect('exportar/'.$id);
			return;
		}

		$fechaDebito = $oLiq->armaFecha(unserialize(base64_decode($fechaDebitoToken)));
		$diskette = array();

		$registros = Set::extract("/LiquidacionSocio/intercambio",$socios);


//		$diskette['banco_intercambio'] = $bancoIntercambioToken;
//		$diskette['info_cabecera'] = array();
//		$diskette['info_pie'] = array();

		App::import('Model','Config.Banco');
		$oBANCO = new Banco();

		$ACUM_REGISTROS = 0;
		$ACUM_IMPORTE = 0;
		foreach($socios as $socio):
			$ACUM_REGISTROS++;
			$ACUM_IMPORTE += $socio['LiquidacionSocio']['importe_adebitar'];
		endforeach;

		$diskette = $oBANCO->genDisketteBanco($bancoIntercambioToken,$fechaDebito,$ACUM_REGISTROS,$ACUM_IMPORTE,$nroArchivoToken);
		$diskette['registros'] = $registros;

//		debug($diskette);
//		exit;


		$this->set('diskette',$diskette);
		$this->render('intercambios/diskette','blank');
//		$this->set('socios',$socios);
//		$this->set('fechaDebito',$fechaDebito);
//		$this->set('nro_archivo',$nroArchivoToken);
//		$this->render('intercambios/diskette_'.$bancoIntercambioToken.'_txt','blank');
	}



	/**
	 * SUBE ARCHIVOS AL SERVIDOR. CARGA EL SHELL RESPECTIVO DE ACUERDO AL ORGANISMO
	 * MANEJA LA ACTUALIZACION DE LA GRILLA VIA AJAX, BORRADO MASIVO Y PUNTUAL DE ARCHIVOS
	 * @param $id
	 */
	function importar($id=null,$UID=null){

		if(empty($id))$this->redirect('consulta');

		if(!empty($UID) && $this->Session->check($UID."_DISKETTE")):
			$this->set('datos',$this->Session->read($UID."_DISKETTE"));
			$this->render('importar_subdivide_txt','blank');
            return;
		endif;


		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $oLiq->cargar($id);
		$liquidacion['recibo_link'] = '';
		if($liquidacion['Liquidacion']['recibo_id'] > 0):
			$liquidacion['Liquidacion']['recibo_link'] = $oLiq->getReciboLink($liquidacion['Liquidacion']['recibo_id']);
		endif;
		$this->set('liquidacion',$liquidacion);

		//seteo el proceso a llamar
		if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) == 22) $this->set('proceso_shell','procesa_archivo_cbu');
		if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) == 66) $this->set('proceso_shell','procesa_archivo_anses');
		if(substr($liquidacion['Liquidacion']['codigo_organismo'],8,2) == 77) $this->set('proceso_shell','procesa_archivo_cjpc');

		$this->set('liquidaciones',$oLiq->datosComboImportar());

		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();

		$archivos = $oFile->getByLiquidacionId($id,true);
		$this->set('archivos',$archivos);


		App::import('Model','Mutual.LiquidacionCuota');
		$oLC = new LiquidacionCuota();

		//proceso el borrado de todos los archivos asociados a una liquidacion
		if(isset($this->params['url']['action']) && $this->params['url']['action'] == 'dropAll'){

            // borrar la preimputacion
            if($oLC->anularPreImputacion($id) && $oFile->setNoProcesado($id)){
                $oLiq->desbloquear($id);
                $this->Mensaje->ok("LA PRE-IMPUTACION FUE ANULADA CORRECTAMENTE");
                $this->redirect('importar/' . $id);
            }else{
                $this->Mensaje->error("Se produjo un error al anular la pre-imputacion");
            }

//			if($oFile->borrarArchivosByLiquidacion($id)){
//				$this->Mensaje->ok("ARCHIVOS BORRADOS CORRECTAMENTE");
//				$this->redirect('importar/' . $id);
//			}else{
//				$this->Mensaje->errores("ERRORES:",$oFile->notificaciones);
//			}

		}

		//borrado puntual de archivo
		if(isset($this->params['url']['action']) && $this->params['url']['action'] == 'dropOne' && !empty($this->params['url']['file'])){
			if($oFile->borrarArchivo($this->params['url']['file'])){
				$this->Mensaje->ok("ARCHIVO BORRADO CORRECTAMENTE");
				$this->redirect('importar/' . $id);
			}else{
				$this->Mensaje->errores("ERRORES:",$oFile->notificaciones);
			}

		}

		//proceso el refresco de la grilla
		if($this->params['isAjax'] == 1){
			$this->render('grilla_importacion_ajax','ajax');
			return;
		}

		if(!empty($UID) && $this->Session->check($UID."_DISKETTE")):
			$this->set('datos',$this->Session->read($UID."_DISKETTE"));
			$this->render('txt','blank');
		endif;

		if(!empty($this->data)){

            $files = array();
            
            if(isset($this->data['LiquidacionIntercambio']['subdividir'])){
                
                App::import('Model','Mutual.LiquidacionIntercambio');
                $oLQI = new LiquidacionIntercambio();    
                $files = $oLQI->subdividirLotePorLiquidacion(
                        $this->data['LiquidacionIntercambio']['banco_id'], 
                        $this->data['LiquidacionIntercambio']['archivo']['name'], 
                        $this->data['LiquidacionIntercambio']['archivo']['tmp_name'], 
                        $this->Session
                );  

            $this->set('files',$files);
            }else if($oFile->subir($this->data)){
				$this->Mensaje->ok('Archivo <strong>'. $this->data['LiquidacionIntercambio']['archivo']['name'] .'</strong> subido correctamente!');
				$this->redirect('importar/' . $this->data['LiquidacionIntercambio']['liquidacion_id']);
			}
			else $this->Mensaje->errores("ERRORES:",$oFile->notificaciones);

	    }

	}


	/**
	 * borrar archivo
	 * @param unknown_type $id
	 * @deprecated
	 */
	function del($id = null, $cascade = true){

		if(empty($id)) $this->redirect('consulta');
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();

		$archivo = $oFile->read(null,$id);
		$path = WWW_ROOT . $archivo['LiquidacionIntercambio']['archivo_file'];
		$this->Util->BorrarArchivo($path);
		$oFile->del($id);

		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oFile2 = new LiquidacionSocioRendicion();
		$oFile2->deleteAll("LiquidacionSocioRendicion.liquidacion_intercambio_id = " . $id);

		$this->redirect('importar/'.$archivo['LiquidacionIntercambio']['liquidacion_id']);
	}

	/**
	 * @deprecated
	 * procesa el archivo plano cargado en el server en varios pasos
	 * @param unknown_type $id
	 * @param unknown_type $paso
	 */
	function procesar_archivo($id,$paso=1){
		if(empty($id)) $this->redirect('consulta');
		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();
		$archivo = $oFile->read(null,$id);
		if(empty($archivo))	$this->redirect('importar');
		$this->set('archivo',$archivo);
		$this->render('procesar_archivo_paso'.$paso);
	}

	function imputados($id,$toPDF=0){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));
		$datos = null;
		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){
			App::import('model','Mutual.ListadoService');
			$oListado = new ListadoService();

			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1');
			$datos = $oListado->getTemporalByConditions(false,$conditions,array('AsincronoTemporal.texto_2'));

//			$datos = $oListado->getTemporal($this->params['url']['pid'],false);
			$datos = Set::extract("/AsincronoTemporal",$datos);
			$datos = Set::extract("{n}.AsincronoTemporal",$datos);

			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_2');
			$datos2 = $oListado->getTemporalByConditions(false,$conditions,array('AsincronoTemporal.texto_1'));
			$datos2 = Set::extract("/AsincronoTemporal",$datos2);
			$datos2 = Set::extract("{n}.AsincronoTemporal",$datos2);

			$this->set('PID',$this->params['url']['pid']);
			$this->set('datos',$datos);
			$this->set('datos2',$datos2);
		}

		if($toPDF==1 && !empty($datos)) $this->render('reportes/imputados_pdf','pdf');
	}

	/**
	 *
	 * @param $id
	 * @param $toPDF
	 */
	function resumen_cruce_informacion($id,$toPDF=0){

            if (empty($id)) {
                $this->redirect('consulta');
            }

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();

            App::import('Model','Mutual.LiquidacionSocio');
            $oLS = new LiquidacionSocio();

            App::import('Model','Mutual.LiquidacionSocioRendicion');
            $oLSR = new LiquidacionSocioRendicion();

            App::import('Model','Mutual.LiquidacionCuota');
            $oLC = new LiquidacionCuota();

            $liquidacion = $oLiq->read('codigo_organismo',$id);

            if (empty($liquidacion)) {
                $this->redirect('consulta');
            }


            $this->set('liquidacion',$oLiq->cargar($id));

		$organismo = substr($liquidacion['Liquidacion']['codigo_organismo'],8,2);

		$this->set('organismo',$organismo);

		App::import('Model','Mutual.LiquidacionCuotaRecupero');
		$oRECUPERO = new LiquidacionCuotaRecupero();


		$recuperosEmitidos = $oRECUPERO->getImporteTotalRecupero($id);
		$this->set('recuperosEmitidos',$recuperosEmitidos);

		switch ($organismo){

			case 22:

				################################################################################
				# CBU
				################################################################################

				$this->set('resumenes',$oLSR->resumenRendicion($id,1));

//				if($opcionResumen == 1)$titulo_opcion = "REGISTROS ENCONTRADOS EN LIQUIDACION";
//				if($opcionResumen == 2)$titulo_opcion = "REGISTROS NO ENCONTRADOS EN LIQUIDACION";
//				if($opcionResumen == 3)$titulo_opcion = "RESUMEN DISKETTES";

//				$this->set('titulo_opcion',$titulo_opcion);
//				$this->set('opcionResumen',$opcionResumen);

				$this->set('total_reintegros',$oLS->totalReintegros($id));
				$this->set('total_nocobrados',$oLS->totalPrimerDtoNoCobrado($id));
				$this->set('total_imputado',$oLC->getTotalImputadoByLiquidacion($id));
				$this->set('total_liquidados_no_rendidos',$oLS->totalLiquidadosNoRendidosEnArchivos($id));
				$this->set('total_enviados_no_rendidos',$oLS->totalLiquidadosNoRendidosEnArchivos($id,true));

				$this->set('total_mora_cuota_uno',$oLC->get_detalle_mora_cuota($id,1,TRUE));
				$this->set('total_mora_temprana',$oLC->get_detalle_mora_temprana($id,TRUE));

				if($toPDF==1) $this->render('reportes/resumen_cruce_informacion_cbu_pdf','pdf');


				break;


			case 66:

				################################################################################
				# ANSES
				################################################################################

				$this->set('total_enviado_no_liquidado',$oLSR->totalLiquidado($id,2));
				$this->set('total_enviado_liquidado',$oLSR->totalLiquidado($id,1));

				$this->set('total_reintegros',$oLS->totalReintegros($id));
				$this->set('total_nocobrados',$oLS->totalPrimerDtoNoCobrado($id));
				$this->set('total_imputado',$oLC->getTotalImputadoByLiquidacion($id));
				$this->set('total_liquidados_no_rendidos',$oLS->totalLiquidadosNoRendidosEnArchivos($id));

				if($toPDF==1) $this->render('reportes/resumen_cruce_informacion_anses_pdf','pdf');

				break;


			case 77:

				################################################################################
				# CAJA DE JUBILACIONES
				################################################################################

				$this->set('total_enviado_no_liquidado',$oLSR->totalLiquidado($id,2));
				$this->set('total_enviado_liquidado',$oLSR->totalLiquidado($id,1));

				$this->set('total_reintegros',$oLS->totalReintegros($id));
				$this->set('total_nocobrados',$oLS->totalPrimerDtoNoCobrado($id));
				$this->set('total_imputado',$oLC->getTotalImputadoByLiquidacion($id));
				$this->set('total_liquidados_no_rendidos_0',$oLS->totalLiquidadosNoRendidosEnArchivos($id,false,true,0));
				$this->set('total_liquidados_no_rendidos_1',$oLS->totalLiquidadosNoRendidosEnArchivos($id,false,true,1));
                                
				$this->set('total_mora_cuota_uno',$oLC->get_detalle_mora_cuota($id,1,TRUE));
				$this->set('total_mora_temprana',$oLC->get_detalle_mora_temprana($id,TRUE));
                               

				if($toPDF==1) $this->render('reportes/resumen_cruce_informacion_cjp_pdf','pdf');

				break;

			default:

				$this->redirect('consulta');
				break;

		}

	}

	/**
	 * CBU
	 * REPORTE DE SOCIOS POR CADA CODIGO DE DESCUENTO
	 * @param $id
	 * @param $banco_id
	 * @param $codigo_status
	 */
	function resumen_cruce_informacion_detalle_codigo_pdf($id,$banco_id,$codigo_status,$opcionResumen=1,$toXLS = FALSE){

		if(empty($id)) $this->redirect('consulta');

//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();

		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();

		$this->set('socios',$oLSR->liquidados($id,$opcionResumen,$codigo_status,$banco_id));

		if($opcionResumen == 1)$titulo_opcion = "REGISTROS ENCONTRADOS EN LIQUIDACION";
		if($opcionResumen == 2)$titulo_opcion = "REGISTROS NO ENCONTRADOS EN LIQUIDACION";
		if($opcionResumen == 3)$titulo_opcion = "RESUMEN DISKETTES";

		$this->set('titulo_opcion',$titulo_opcion);
		$this->set('opcionResumen',$opcionResumen);

		App::import('Model','Config.BancoRendicionCodigo');
		$oCODIGO = new BancoRendicionCodigo();

		$this->set('banco_nombre',$oLSR->getNombreBanco($banco_id));
		$this->set('status',$codigo_status);
		$this->set('status_descripcion',$oCODIGO->getDescripcionCodigo($banco_id,$codigo_status));

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));

		if(!$toXLS){
                    $this->render('reportes/resumen_cruce_informacion_detalle_codigo_pdf','pdf');
                }else{
                    $this->render('reportes/resumen_cruce_informacion_detalle_codigo_xls','blank');
                }

	}

	/**
	 * GENERA EL PDF CON EL LISTADO DE REGISTROS LIQUIDADOS Y QUE NO SE ENCONTRARON EN LOS ARCHIVOS DE RENCIDION
	 * @param $id
	 */
	function resumen_cruce_informacion_no_encontrados_pdf($id,$soloEnviadosEnDiskette=0,$isCjp=0,$valSubCodCjp=1){
		if(empty($id)) $this->redirect('consulta');
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

//		App::import('Model','Mutual.LiquidacionSocioRendicion');
//		$oLSR = new LiquidacionSocioRendicion();

		if($isCjp == 1){
			$this->set('socios',$oLS->liquidadosNoRendidosEnArchivos($id,true,false,true,$valSubCodCjp));
		}else{
			$this->set('socios',$oLS->liquidadosNoRendidosEnArchivos($id,true,($soloEnviadosEnDiskette==0) ? false : true));
		}


		$this->set('banco_nombre','');
		$this->set('status','');
		$this->set('status_descripcion','REGISTROS NO ENCONTRADOS');

//		$this->set('titulo_reporte',(($soloEnviadosEnDiskette==0) ? "LIQUIDADOS NO ENVIADOS A DEBITAR" : "A DEBITAR NO DEVUELTOS POR EL ORGANISMO"));
		$this->set('titulo_reporte',"A DEBITAR NO DEVUELTOS POR EL ORGANISMO");

		$this->set('soloEnviadosEnDiskette',$soloEnviadosEnDiskette);
		$this->set('isCjp',$isCjp);
		$this->set('valSubCodCjp',$valSubCodCjp);

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));
		$this->render('reportes/resumen_cruce_informacion_no_encontrados_pdf','pdf');
	}


	/**
	 * GENERA EL PDF CON EL LISTADO DE REGISTROS LIQUIDADOS Y QUE NO SE ENCONTRARON EN LOS ARCHIVOS DE RENCIDION
	 * @param $id
	 */
	function resumen_cruce_informacion_no_encontrados_xls($id,$soloEnviadosEnDiskette=0,$isCjp=0,$valSubCodCjp=1){
		if(empty($id)) $this->redirect('consulta');
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

//		App::import('Model','Mutual.LiquidacionSocioRendicion');
//		$oLSR = new LiquidacionSocioRendicion();

		if($isCjp == 1){
			$this->set('socios',$oLS->liquidadosNoRendidosEnArchivos($id,true,false,true,$valSubCodCjp));
		}else{
			$this->set('socios',$oLS->liquidadosNoRendidosEnArchivos($id,true,($soloEnviadosEnDiskette==0) ? false : true));
		}


		$this->set('banco_nombre','');
		$this->set('status','');
		$this->set('status_descripcion','REGISTROS NO ENCONTRADOS');

//		$this->set('titulo_reporte',(($soloEnviadosEnDiskette==0) ? "LIQUIDADOS NO ENVIADOS A DEBITAR" : "A DEBITAR NO DEVUELTOS POR EL ORGANISMO"));
		$this->set('titulo_reporte',"A DEBITAR NO DEVUELTOS POR EL ORGANISMO");

		$this->set('soloEnviadosEnDiskette',$soloEnviadosEnDiskette);
		$this->set('isCjp',$isCjp);
		$this->set('valSubCodCjp',$valSubCodCjp);

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));
		$this->render('reportes/resumen_cruce_informacion_no_encontrados_xls','blank');
	}

	/**
	 * GENERA EL PDF DE LOS REGISTROS ENVIADOS EN EL ARCHIVO DE RENDICION Y QUE NO ESTAN LIQUIDADOS
	 * @param $id
	 */
	function registros_enviados_no_encontrados_pdf($id,$enviados=0){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));

		//busco los registros que fueron enviados y que no estan liquidados
//		App::import('Model','Mutual.LiquidacionIntercambioRegistroProcesado');
//		$oRegistroProcesado = new LiquidacionIntercambioRegistroProcesado();

		App::import('Model','Mutual.LiquidacionSocioRendicion');
		$oLSR = new LiquidacionSocioRendicion();

		$this->set('enviado_no_liquidado',$oLSR->liquidados($id,($enviados == 0 ? 1 : 2)));

		$this->render('reportes/registros_enviados_no_encontrados_pdf','pdf');
	}

	function reintegros_pdf($id){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		$reintegros = $oLS->reintegros($id,true,false);
		$anticipos = Set::extract("/LiquidacionSocio[importe_anticipado>0]",$reintegros);
		$this->set('reintegros',$reintegros);
		$this->set('anticipos',$anticipos);
		$this->render('reportes/reintegros_pdf','pdf');

	}


	function reintegros_xls($id){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		$this->set('reintegros',$oLS->reintegros($id));
		$this->render('reportes/reintegros_xls','blank');
	}


	function altas_no_cobradas_pdf($id){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		$this->set('reintegros',$oLS->primerDtoNoCobrado($id));
		$this->render('reportes/altas_no_cobradas_pdf','pdf');
	}


	/**
	 * @deprecated
	 * GENERA EL PDF CON LOS REGISTROS LIQUIDADOS NO ENCONTRADOS EN EL ENVIO DE ARCHIVOS
	 * @param $id
	 */
	function resumen_cruce_informacion_errores_pdf($id){

		if(empty($id)) $this->redirect('consulta');
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();
		$this->set('socios',$oLS->resumenRegistrosNoEncontrados($id));

		$this->set('banco_nombre','');
		$this->set('status','');
		$this->set('status_descripcion','REGISTROS NO ENCONTRADOS');

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));
		$this->render('reportes/resumen_cruce_informacion_errores_pdf','pdf');

	}


	function imputar_pagos($id,$PID = null){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));
		$this->set('show_asinc',0);
		$this->set('PID',$PID);
                $this->set('reprocesar',0);
		if(!empty($this->data)){
			$this->set('show_asinc',1);
			$this->set('fecha_imputacion',$oLiq->armaFecha($this->data['Liquidacion']['fecha_imputacion']));
			$this->set('nro_recibo',$this->data['Liquidacion']['nro_recibo']);
			$this->set('reprocesar',(isset($this->data['Liquidacion']['desimputar']) ? 1 : 0));
		}
	}


	function reporte_proveedores($id,$verReporte=0,$toPDF=0,$procesarSobrePreImputacion=0,$periodo=null){
            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $liquidacion = null;
            if(!empty($id))$liquidacion = $oLiq->cargar($id);
            App::import('Model','Proveedores.ProveedorLiquidacion');
            $oPL = new ProveedorLiquidacion();
//		$proveedores = $oPL->getProveedoresLiquidados($id);
            $proveedores = null;
            $this->set('PID',NULL);
            $socios = null;

            $render = "reporte_proveedores";

            App::import('model','Mutual.ListadoService');
            $oListado = new ListadoService();
            if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){
//		$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1','AsincronoTemporal.decimal_3 >' => 0);
//		$proveedores = $oListado->getTemporalByConditions(false,$conditions,array('AsincronoTemporal.texto_1'));
                $proveedores = $oListado->getTemporal($this->params['url']['pid'],false);
                $proveedores = Set::extract("/AsincronoTemporal[clave_3=REPORTE_1]",$proveedores);
                $proveedores = Set::extract("/AsincronoTemporal[decimal_3>0]",$proveedores);
                $proveedores = Set::extract("{n}.AsincronoTemporal",$proveedores);
                $this->set('PID',$this->params['url']['pid']);

                $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
                if(isset($INI_FILE['general']['discrimina_iva']) && $INI_FILE['general']['discrimina_iva'] != 0){
                    $socios = $oListado->getTemporal($this->params['url']['pid'],false);
                    $socios = Set::extract("/AsincronoTemporal[clave_3=REPORTE_5]",$socios);
                    $socios = Set::extract("{n}.AsincronoTemporal",$socios);
                    $this->set('socios',$socios);
                    if(empty($id) && !empty($periodo)){
//                        $this->render('reporte_proveedores_periodo_iva');
//                        return;
                        $render = "reporte_proveedores_periodo_iva";
                    }else{
//                        $this->render('reporte_proveedores_iva');
//                         return;
                        $render = "reporte_proveedores_iva";
                    }
                }


            }
            $this->set('liquidacion',$liquidacion);
            $this->set('periodo',$periodo);
            $this->set('proveedores',$proveedores);
            $this->set('procesarSobrePreImputacion',$procesarSobrePreImputacion);

//            $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
//            if(isset($INI_FILE['general']['discrimina_iva']) && $INI_FILE['general']['discrimina_iva'] != 0){
//                $socios = $oListado->getTemporal($this->params['url']['pid'],false);
//                $socios = Set::extract("/AsincronoTemporal[clave_3=REPORTE_5]",$socios);
//                $socios = Set::extract("{n}.AsincronoTemporal",$socios);
//                $this->set('socios',$socios);
//                if(empty($id) && !empty($periodo)){
//                    $this->render('reporte_proveedores_periodo_iva');
//                    return;
//                }else{
//                    $this->render('reporte_proveedores_iva');
//                     return;
//                }
//            }

            if(empty($id) && !empty($periodo)){
//                $this->render('reporte_proveedores_periodo');
//                return;
                $render = "reporte_proveedores_periodo";
            }


            $this->render($render);

	}


	function detalle_turno_diskette3($id,$turno,$orderBy='SOCIO',$action = NULL){

		App::import('Model','Mutual.LiquidacionSocioNoimputada');
		$oLS = new LiquidacionSocioNoimputada();

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

                if(!empty($action)){

                    if($action == 'NOENVIAR_ALL'){
			$oLS->updateAll(
					array('LiquidacionSocioNoimputada.diskette' => 0),
					array(
							'LiquidacionSocioNoimputada.liquidacion_id' => $id,
							'LiquidacionSocioNoimputada.turno_pago' => $turno
					)
			);
                        $this->redirect('detalle_turno_diskette3/'.$id.'/'.$turno);

                    }

                    if($action == 'ENVIAR_ALL'){
			$oLS->updateAll(
					array('LiquidacionSocioNoimputada.diskette' => 1),
					array(
							'LiquidacionSocioNoimputada.liquidacion_id' => $id,
							'LiquidacionSocioNoimputada.turno_pago' => $turno
					)
			);
                        $this->redirect('exportar3/'.$id);
                    }




                }


		if(!empty($this->data)):

                    if(!empty($this->data['LiquidacionSocioNoimputada']['noenvia_diskette'])):

                            foreach($this->data['LiquidacionSocioNoimputada']['noenvia_diskette'] as $liquidacionSocio_id):
                                    $liquiSocio = $oLS->read('diskette',$liquidacionSocio_id);
//                                    $liquiSocio['LiquidacionSocioNoimputada']['diskette'] = 1;

                                    if( !$liquiSocio['LiquidacionSocioNoimputada']['diskette'] ) $liquiSocio['LiquidacionSocioNoimputada']['diskette'] = 1;
                                    else $liquiSocio['LiquidacionSocioNoimputada']['diskette'] = 0;

                                    $oLS->save($liquiSocio);
                            endforeach;
                            $this->redirect('exportar3/'.$this->data['LiquidacionSocioNoimputada']['liquidacion_id']);
                    endif;



		endif;


//		$socios = $oLS->getDatosParaDisketteCBU($id,$turno,0,false,false,$orderBy);
                $socios = $oLS->getDetalleDeTurnoDiskette($id,$turno);

		$liquidacion = $oLiq->cargar($id);

		$this->set('liquidacion',$liquidacion);
		$this->set('ultimoPeriodoImputado',$oLiq->getUltimoPeriodoImputado($liquidacion['Liquidacion']['codigo_organismo']));



		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
		$this->set('descripcion_turno',$oTURNO->getDescripcionByTruno($turno));

//                debug($socios);
//                exit;

		$this->set('socios',$socios);
		$this->set('turno',$turno);

		$this->render('detalle_turno_diskette3');

	}


	function detalle_turno_diskette($id,$turno,$orderBy='SOCIO',$action = NULL){

		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();

                if(!empty($action)){

                    if($action == 'NOENVIAR_ALL'){
			$oLS->updateAll(
					array('LiquidacionSocio.diskette' => 0),
					array(
							'LiquidacionSocio.liquidacion_id' => $id,
							'LiquidacionSocio.turno_pago' => $turno
					)
			);
                        $this->redirect('detalle_turno_diskette/'.$id.'/'.$turno);

                    }

                    if($action == 'ENVIAR_ALL'){
			$oLS->updateAll(
					array('LiquidacionSocio.diskette' => 1),
					array(
							'LiquidacionSocio.liquidacion_id' => $id,
							'LiquidacionSocio.turno_pago' => $turno
					)
			);
                        $this->redirect('exportar/'.$id);
                    }




                }


		if(!empty($this->data)):

//			$oLS->updateAll(
//					array('LiquidacionSocio.diskette' => 1),
//					array(
//							'LiquidacionSocio.liquidacion_id' => $this->data['LiquidacionSocio']['liquidacion_id'],
//							'LiquidacionSocio.turno_pago' => $this->data['LiquidacionSocio']['turno_pago']
//					)
//			);

//                        debug($this->data);
//                        exit;

//                    debug($this->data);
//                    exit;


                    if(!empty($this->data['LiquidacionSocio']['noenvia_diskette'])):

                            foreach($this->data['LiquidacionSocio']['noenvia_diskette'] as $liquidacionSocio_id):
                                    $liquiSocio = $oLS->read('diskette',$liquidacionSocio_id);
//                                    $liquiSocio['LiquidacionSocio']['diskette'] = 1;

                                    if( !$liquiSocio['LiquidacionSocio']['diskette'] ) $liquiSocio['LiquidacionSocio']['diskette'] = 1;
                                    else $liquiSocio['LiquidacionSocio']['diskette'] = 0;

                                    $oLS->save($liquiSocio);
                            endforeach;
                            $this->redirect('exportar/'.$this->data['LiquidacionSocio']['liquidacion_id']);
                    endif;



		endif;


//		$socios = $oLS->getDatosParaDisketteCBU($id,$turno,0,false,false,$orderBy);
                $socios = $oLS->getDetalleDeTurnoDiskette($id,$turno);

		$liquidacion = $oLiq->cargar($id);

		$this->set('liquidacion',$liquidacion);
		$this->set('ultimoPeriodoImputado',$oLiq->getUltimoPeriodoImputado($liquidacion['Liquidacion']['codigo_organismo']));



		App::import('Model', 'Mutual.LiquidacionTurno');
		$oTURNO = new LiquidacionTurno();
		$this->set('descripcion_turno',$oTURNO->getDescripcionByTruno($turno));

//                debug($socios);
//                exit;

		$this->set('socios',$socios);
		$this->set('turno',$turno);

		$this->render('detalle_turno_diskette2');

	}

	/**
	 * Intercambio Proveedores
	 * Arma un archivo plano
	 * @param unknown_type $liquidacion_id
	 * @param unknown_type $proveedor_id
	 * @return unknown_type
	 */
	function intercambio_proveedores($liquidacion_id,$proveedor_id){

		App::import('Model','Proveedores.Proveedor');
		$oPRV = new Proveedor();

		$proveedor = $oPRV->read(null,$proveedor_id);

//		if($proveedor['Proveedor']['intercambio'] != 1) parent::noDisponible();

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $oLiq->cargar($liquidacion_id);
		$this->set('liquidacion',$liquidacion);

		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){

//			App::import('model','Mutual.ListadoService');
//			$oListado = new ListadoService();
//
//			//ARMO LA SALIDA DE ACUERDO AL PROVEEDOR
//			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1','AsincronoTemporal.decimal_2 <> ' => 0);
//			$cobrados = $oListado->getTemporalByConditions(false,$conditions,array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_7'));
//            //debug($cobrados);
//            //exit;
//			//if(empty($cobrados)) parent::noDisponible();
//
//			$cobrados = Set::extract('{n}.AsincronoTemporal',$cobrados);
//
//
//			$this->set('cobrados',$cobrados);
//			$this->set('proveedor',$proveedor);

            $this->set('proveedor',$proveedor);
            App::import('Model','mutual.LiquidacionCuota');
            $oLC = new LiquidacionCuota();
            $registros = $oLC->generarLoteRendicion($liquidacion_id, $proveedor_id);

            $this->set('registros',$registros);

//			$this->render('intercambios/urgencias_txt','blank');
			$this->render('intercambios/'.(!empty($proveedor['Proveedor']['template_intercambio']) ? $proveedor['Proveedor']['template_intercambio'] : "general").'_txt','blank');

		}else{

			parent::noDisponible();

		}

//        $this->set('proveedor',$proveedor);
//		App::import('Model','mutual.LiquidacionCuota');
//		$oLC = new LiquidacionCuota();
//        $registros = $oLC->generarLoteRendicion($liquidacion_id, $proveedor_id);
//        $this->set('registros',$registros);

//        $this->render('intercambios/'.(!empty($proveedor['Proveedor']['template_intercambio']) ? $proveedor['Proveedor']['template_intercambio'] : "general").'_txt','blank');


	}


	function modificar_importes_dto($socio_id,$liquidacion_id,$menuPersonas=1){
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		$liquidaciones = $oLS->getLiquidacionBySocio($socio_id,$liquidacion_id);

		if(empty($liquidaciones)) parent::noDisponible();
		
		if(!empty($this->data)):

			if(!empty($this->data['LiquidacionSocio']['importe_adebitar'])):

				foreach($this->data['LiquidacionSocio']['importe_adebitar'] as $id => $importe):
					$importe = (float) $importe;
//					if($importe != 0):
						$liquidacionSocio = $oLS->read(null,$id);
// 						debug($liquidacionSocio);
						
						$topeCBU = $oLS->GlobalDato('decimal_2',$liquidacionSocio['LiquidacionSocio']['codigo_organismo']);
						$topeCBU = floatval($topeCBU);
						
						if($importe <= $topeCBU) {
						    $liquidacionSocio['LiquidacionSocio']['importe_adebitar'] = $importe;
						    $oLS->save($liquidacionSocio);
						}
//					endif;
				endforeach;
				$this->redirect('by_socio/'.$socio_id.'/1');
			endif;

		endif;

		App::import('Model','Pfyj.Socio');
		$oSocio = new Socio();
		$oSocio->bindModel(array('belongsTo' => array('Persona')));
		$socio = $oSocio->read(null,$socio_id);
		$this->set('socio',$socio);
		$this->set('menuPersonas',$menuPersonas);

		$this->set('liquidaciones',$liquidaciones);
		$this->set('liquidacion',$this->Liquidacion->read(null,$liquidacion_id));

	}


	/**
	 * Genera un excel con los datos de un archivo de rendicion
	 *
	 * @param unknown_type $liquidacion_id
	 * @param unknown_type $LiqIntercambioId
	 */
	function detalle_archivo($liquidacion_id, $LiqIntercambioId  = null){

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $oLiq->cargar($liquidacion_id);

		App::import('Model','Mutual.LiquidacionIntercambio');
		$oINTERC = new LiquidacionIntercambio();

		$archivo = null;
		if(!empty($LiqIntercambioId)) $archivo = $oINTERC->get($LiqIntercambioId);

		$this->set('liquidacion',$liquidacion);
		$this->set('archivo',$archivo);

		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

			App::import('model','Mutual.ListadoService');
			$oListado = new ListadoService();


			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();

			$asinc = $oASINC->read('p1',$this->params['url']['pid']);

			$columnas = array(
								'texto_1' => 'DOCUMENTO',
								'texto_2' => 'APELLIDO_NOMBRE',
								'texto_3' => 'NRO_SOCIO',
//                                'texto_16' => 'DOMICILIO',
//                                'texto_17' => 'INFO_CONTACTO',
								'texto_4' => 'IDENTIFICACION',
								'texto_5' => 'EMPRESA',
								'texto_6' => 'TURNO',
								'texto_7' => 'TURNO_DESC',
								'decimal_1' => 'IMPORTE',
								'texto_9' => 'CODIGO',
								'texto_10' => 'DESCRIPCION',
								'texto_11' => 'COBRO',
								'texto_12' => 'FECHA_DEBITO',
								'texto_13' => 'ARCHIVO',
			);

			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1');
			$order = array('AsincronoTemporal.texto_2');
			$datos = $oListado->getDetalleToExcel($conditions,$order,$columnas);
			$this->set('datos',$datos);
			$this->render('reportes/detalle_archivo_general_xls','blank');
			return;

		endif;


		$this->render('detalle_archivo_general');

	}

	/**
	 * @deprecated
	 * Enter description here ...
	 * @param unknown_type $liquidacion_id
	 */
	function detalle_archivo_general($liquidacion_id){

		if(empty($liquidacion_id))$this->redirect('consulta');


		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $oLiq->cargar($liquidacion_id);

		$this->set('liquidacion',$liquidacion);

		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

			App::import('model','Mutual.ListadoService');
			$oListado = new ListadoService();


			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();

			$asinc = $oASINC->read('p1',$this->params['url']['pid']);


			/**
			SELECT
			texto_1,
			texto_2,
			texto_3,
			texto_4,
			texto_14,
			texto_5,
			texto_6,
			texto_7,
			decimal_1,
			texto_9,
			texto_10,
			texto_11,
			texto_12,
			texto_15,
			texto_13
			FROM asincrono_temporales WHERE asincrono_id = 4782
			ORDER BY texto_2
			 */

			$columnas = array(
								'texto_1' => 'DOCUMENTO',
								'texto_2' => 'APELLIDO_NOMBRE',
								'texto_3' => 'NRO_SOCIO',
                                'texto_16' => 'DOMICILIO',
                                'texto_19' => 'LOCALIDAD',
                                'texto_20' => 'PROVINCIA',
                                'texto_17' => 'INFO_CONTACTO',
								'texto_4' => 'IDENTIFICACION',
								'texto_14' => 'BANCO',
								'texto_5' => 'EMPRESA',
								'texto_6' => 'TURNO',
								'texto_7' => 'TURNO_DESC',
								'decimal_1' => 'IMPORTE',
								'texto_9' => 'CODIGO',
								'texto_10' => 'DESCRIPCION',
								'texto_11' => 'COBRO',
								'texto_12' => 'FECHA_DEBITO',
								'texto_15' => 'BANCO_INTERCAMBIO',
								'texto_13' => 'ARCHIVO',
                                                                'texto_18' => 'TELEFONO_MOVIL',
			);

			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1');
			$order = array('AsincronoTemporal.texto_2');
			$datos = $oListado->getDetalleToExcel($conditions,$order,$columnas);
			$this->set('datos',$datos);

			$columnas = array(
								'texto_1' => 'DOCUMENTO',
								'texto_2' => 'APELLIDO_NOMBRE',
                                                                'texto_12' => 'DOMICILIO',
                                                                'texto_10' => 'LOCALIDAD',
                                                                'texto_11' => 'PROVINCIA',
								'texto_3' => 'BANCO',
								'texto_4' => 'IDENTIFICACION',
								'texto_5' => 'FECHA_DEBITO',
								'texto_6' => 'CODIGO',
								'texto_7' => 'DESCRIPCION',
								'decimal_1' => 'IMPORTE',
								'texto_8' => 'BANCO_INTERCAMBIO',
								'texto_9' => 'TELEFONO_MOVIL',
			);

			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_2');
			$order = array('AsincronoTemporal.texto_2');
			$datos = $oListado->getDetalleToExcel($conditions,$order,$columnas);
			$this->set('nocob',$datos);

			$this->render('reportes/detalle_archivo_general_xls','blank');


		endif;




//		$this->render('reportes/detalle_archivo_general_xls','blank');

	}


	function reporte_general_imputacion(){

		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$periodos = $oLiq->getPeriodosLiquidados(FALSE,FALSE,null,'DESC',TRUE);
		ksort($periodos);
		$this->set('periodos_desde',$periodos);
		krsort($periodos);
		$this->set('periodos_hasta',$periodos);

		$disableForm = 0;
		$showAsincrono = 0;

		$optionsTipoInforme = array(
										1 => "1 - DETALLE DE CUOTAS PAGADAS Y ADEUDADAS",
										2 => "2 - CUOTAS NO PAGADAS CON MOTIVO DEL NO PAGO",
										3 => "3 - CUOTAS REVERSADAS",
										4 => "4 - OTRAS COBRANZAS NO EFECTUADAS POR RECIBO DE SUELDO",
								);
		$this->set('optionsTipoInforme',$optionsTipoInforme);

		if(!empty($this->data)){

//                    debug($this->data);

                    $codigo_organismo = base64_encode(serialize(array_keys($this->data['Liquidacion']['codigo_organismo'])));
//                debug(unserialize(base64_decode($codigo_organismo)));
//                exit;
			$this->set('periodo_desde',$this->data['Liquidacion']['periodo_desde']);
			$this->set('periodo_hasta',$this->data['Liquidacion']['periodo_hasta']);
			$this->set('codigo_organismo', $codigo_organismo);
			$this->set('proveedor_id',base64_encode(serialize($this->data['Proveedor']['proveedor_id'])));
			$this->set('tipo_informe',$this->data['Liquidacion']['tipo_informe']);
			$this->set('tipo_informe_desc',$optionsTipoInforme[$this->data['Liquidacion']['tipo_informe']]);

            #################################################################################################
            # PREPARO DATOS CONFIGURACION EXCEL
            # 2015/09/11
            #################################################################################################
            $excel = array(
                'file_name' => $this->Liquidacion->generarPIN(20).".xls",
                'titulo' => null,
                'columnas' => array(),
            );
            switch ($this->data['Liquidacion']['tipo_informe']):
                case 1:
                    $excel['titulo'] = $optionsTipoInforme[$this->data['Liquidacion']['tipo_informe']];
                    $excel['columnas'] = array(
                                        'texto_18' => 'PERIODO_LIQUIDADO',
                                        'texto_15' => 'TIPO_DOC',
                                        'texto_1' => 'NRO_DOC',
                                        'texto_2' => 'APELLIDO_NOMBRE',
                                        'texto_13' => 'BANCO_DEBITO',
                                        'texto_17' => 'CTA_BANCO_DEBITO',
                                        'texto_14' => 'FECHA_DEBITO',
                                        'texto_3' => 'TIPO_ORD_DTO',
                                        'texto_16' => 'NRO_ORD_DTO',
                                        'texto_4' => 'PRODUCTO_CONCEPTO',
                                        'texto_6' => 'CUOTA',
                                        'texto_19' => 'PERIODO_CUOTA',
                                        'decimal_10' => 'SOLICITADO',
                                        'decimal_1' => 'LIQUIDADO',
                                        'decimal_2' => 'COBRADO',
                                        'decimal_3' => 'SALDO',
                                        'decimal_4' => 'PORC_COMISION',
                                        'decimal_5' => 'COMISION',
                                        'decimal_9' => 'I.V.A.',
                                        'decimal_6' => 'NETO_PROVEEDOR',
                                        'texto_9' => 'REF_PROVEEDOR',
                                        'texto_20' => 'CBU',
                                        'clave_2' => 'ORGANISMO',
										'clave_3' => 'EMPRESA',
										'entero_2' => 'SOCIO',
                    );
                    break;
                case 2:
                    $excel['titulo'] = $optionsTipoInforme[$this->data['Liquidacion']['tipo_informe']];
                    $excel['columnas'] = array(
                                        'entero_1' => 'PERIODO_LIQUIDADO',
                                        'texto_1' => 'TIPO_NRO_DOCUMENTO',
                                        'texto_2' => 'APELLIDO_NOMBRE',
                                        'texto_15' => 'ORGANISMO',
                                        'texto_16' => 'EMPRESA',
                                        'texto_13' => 'CALIF.',
                                        'texto_14' => 'PERIODO_CALIF.',
                                        'texto_3' => 'TIPO_NUMERO',
                                        'texto_4' => 'PRODUCTO_CONCEPTO',
                                        'texto_6' => 'CUOTA',
                                        'texto_7' => 'PERIODO_CUOTA',
                                        'decimal_10' => 'SOLICITADO',
                                        'decimal_1' => 'LIQUIDADO',
                                        'texto_11' => 'CODIGO',
                                        'texto_12' => 'DESCRIPCION',
                                        'texto_9' => 'REF_PROVEEDOR',
                                        'texto_17' => 'CBU',
                                        'entero_4' => 'NRO_ORD_DTO',
                    );
                    break;
                case 3:
                    $excel['titulo'] = $optionsTipoInforme[$this->data['Liquidacion']['tipo_informe']];
                    $excel['columnas'] = array(
                                        'texto_14' => 'ORGANISMO',
                                        'entero_1' => 'PERIODO_LIQUIDADO',
                                        'texto_1' => 'TIPO_NRO_DOCUMENTO',
                                        'texto_2' => 'SOCIO',
                                        'texto_5' => 'TIPO_NUMERO',
                                        'texto_13' => 'PROVEEDOR',
                                        'texto_5' => 'PRODUCTO_CONCEPTO',
                                        'texto_8' => 'PERIODO_CUOTA',
                                        'texto_6' => 'CUOTA',
                                        'texto_10' => 'FECHA_REVERSO',
                                        'texto_9' => 'PERIODO_INFORMADO_PROVEEDOR',
                                        'decimal_1' => 'IMPORTE_REVERSADO',
                                        'decimal_2' => '%_COMISION',
                                        'decimal_3' => 'COMISION_REVERSADA',
                                        'decimal_4' => 'NETO_PROVEEDOR',
                                        'texto_4' => 'REF_PROVEEDOR',
                                        'texto_15' => 'EMPRESA/TURNO',
                                        'decimal_5' => 'IMPORTE_COBRADO',
                                        'texto_16' => 'TIPO_REVERSO',
                                        'entero_2' => 'ORDEN_DTO',
                    );
                    break;
                case 4:
                    $excel['titulo'] = $optionsTipoInforme[$this->data['Liquidacion']['tipo_informe']];
                    $excel['columnas'] = array(
                                        'texto_1' => 'TIPO_NRO_DOCUMENTO',
                                        'texto_2' => 'APELLIDO_NOMBRE',
                                        'texto_5' => 'FECHA',
                                        'texto_4' => 'TIPO COBRO',
                                        'texto_12' => 'TIPO CANCEL',
                                        'texto_13' => 'FORMA CANCEL',
                                        'texto_8' => 'TIPO_NUMERO',
                                        'texto_9' => 'PROVEEDOR_CONCEPTO',
                                        'texto_14' => 'NRO_REF_PROVEEDOR',
                                        'texto_11' => 'CUOTA',
                                        'texto_6' => 'PERIODO',
                                        'decimal_1' => 'COBRADO',
                    );
                    break;
            endswitch;
            $this->set('excel_params', $excel);

			$disableForm = 1;
			$showAsincrono = 1;
		}

		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):


			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();

			$asinc = $oASINC->read('p1,p2,p3,p4,p6',$this->params['url']['pid']);

            $this->redirect('/mutual/listados/download/'.$asinc['Asincrono']['p6']);

//			App::import('model','Mutual.ListadoService');
//			$oListado = new ListadoService();
//
//
//			App::import('model','Shells.Asincrono');
//			$oASINC = new Asincrono();
//
//			$asinc = $oASINC->read('p1,p2,p3,p4',$this->params['url']['pid']);
//
//			App::import('Model','Proveedores.Proveedor');
//			$oPRV = new Proveedor();
//			$oPRV->unbindModel(array('hasMany' => array('MutualProducto')));
//			$this->set('proveedor',$oPRV->read(null,$asinc['Asincrono']['p4']));
//			$this->set('periodo_desde',$asinc['Asincrono']['p1']);
//			$this->set('periodo_hasta',$asinc['Asincrono']['p2']);
//			$this->set('codigo_organismo',$asinc['Asincrono']['p3']);
//
////			$columnas = array(
////								'entero_1' => 'PERIODO_LIQUIDADO',
////								'texto_1' => 'TIPO_NRO_DOCUMENTO',
////								'texto_2' => 'APELLIDO_NOMBRE',
////								'texto_13' => 'CALIF.',
////								'texto_14' => 'PERIODO_CALIF.',
////								'texto_3' => 'TIPO_NUMERO',
////								'texto_9' => 'REF_PROVEEDOR',
////								'texto_4' => 'PRODUCTO_CONCEPTO',
////								'texto_6' => 'CUOTA',
////								'texto_7' => 'PERIODO',
////								'decimal_1' => 'LIQUIDADO',
////								'decimal_2' => 'COBRADO',
////								'decimal_3' => 'SALDO',
////								'decimal_4' => 'PORC_COMISION',
////								'decimal_5' => 'COMISION',
////								'decimal_6' => 'NETO_PROVEEDOR',
////			);
//
////			$columnas = array(
////								'texto_18' => 'PERIODO_LIQUIDADO',
////								'texto_15' => 'TIPO_DOC',
////								'texto_1' => 'NRO_DOC',
////								'texto_2' => 'APELLIDO_NOMBRE',
////								'texto_13' => 'BANCO_DEBITO',
////								'texto_14' => 'FECHA_DEBITO',
////								'texto_3' => 'TIPO_ORD_DTO',
////								'texto_16' => 'NRO_ORD_DTO',
////								'texto_9' => 'REF_PROVEEDOR',
////								'texto_4' => 'PRODUCTO_CONCEPTO',
////								'texto_6' => 'CUOTA',
////								'texto_19' => 'PERIODO_CUOTA',
////								'decimal_1' => 'LIQUIDADO',
////								'decimal_2' => 'COBRADO',
////								'decimal_3' => 'SALDO',
////								'decimal_4' => 'PORC_COMISION',
////								'decimal_5' => 'COMISION',
////								'decimal_6' => 'NETO_PROVEEDOR',
////			);
//
//			$columnas = array(
//								'texto_18' => 'PERIODO_LIQUIDADO',
//								'texto_15' => 'TIPO_DOC',
//								'texto_1' => 'NRO_DOC',
//								'texto_2' => 'APELLIDO_NOMBRE',
//								'texto_13' => 'BANCO_DEBITO',
//								'texto_17' => 'CTA_BANCO_DEBITO',
//								'texto_14' => 'FECHA_DEBITO',
//								'texto_3' => 'TIPO_ORD_DTO',
//								'texto_16' => 'NRO_ORD_DTO',
//								'texto_9' => 'REF_PROVEEDOR',
//								'texto_4' => 'PRODUCTO_CONCEPTO',
//								'texto_6' => 'CUOTA',
//								'texto_19' => 'PERIODO_CUOTA',
//								'decimal_1' => 'LIQUIDADO',
//								'decimal_2' => 'COBRADO',
//								'decimal_3' => 'SALDO',
//								'decimal_4' => 'PORC_COMISION',
//								'decimal_5' => 'COMISION',
//								'decimal_9' => 'I.V.A.',
//								'decimal_6' => 'NETO_PROVEEDOR',
//			);
//
//			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1');
//			$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.entero_3,AsincronoTemporal.texto_3,AsincronoTemporal.texto_6');
//			$cuotas = $oListado->getDetalleToExcel($conditions,$order,$columnas);
////			foreach($cuotas as $idx => $cuota){
////				$cuota['PERIODO'] = $oListado->periodo($cuota['PERIODO']);
////				$cuota['PERIODO_LIQUIDADO'] = $oListado->periodo($cuota['PERIODO_LIQUIDADO']);
////				$cuotas[$idx] = $cuota;
////			}
//
//
//			$this->set('cuotas',$cuotas);
//
//			$columnas = array(
//								'entero_1' => 'PERIODO_LIQUIDADO',
//								'texto_1' => 'TIPO_NRO_DOCUMENTO',
//								'texto_2' => 'APELLIDO_NOMBRE',
//								'texto_13' => 'CALIF.',
//								'texto_14' => 'PERIODO_CALIF.',
//								'texto_3' => 'TIPO_NUMERO',
//								'texto_9' => 'REF_PROVEEDOR',
//								'texto_4' => 'PRODUCTO_CONCEPTO',
//								'texto_6' => 'CUOTA',
//								'texto_7' => 'PERIODO',
//								'decimal_1' => 'LIQUIDADO',
//								'texto_11' => 'CODIGO',
//								'texto_12' => 'DESCRIPCION',
//
//			);
//
//			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_2');
//			$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_4');
//			$noCobradosBanco = $oListado->getDetalleToExcel($conditions,$order,$columnas);
//
//			foreach($noCobradosBanco as $idx => $cuota){
//				$cuota['PERIODO'] = $oListado->periodo($cuota['PERIODO']);
//				$cuota['PERIODO_LIQUIDADO'] = $oListado->periodo($cuota['PERIODO_LIQUIDADO']);
//				$noCobradosBanco[$idx] = $cuota;
//			}
//
//			$this->set('noCobradosBanco',$noCobradosBanco);
//
////			App::import('Model','Mutual.OrdenDescuentoCobroCuota');
////			$oCCUOTA = new OrdenDescuentoCobroCuota();
//
//
//
////			$reversos = $oCCUOTA->reversosByProveedorByLiquidacion($proveedor_id,$liquidacion_id,$tipo_producto,$tipo_cuota);
////			$this->set('reversos',$reversos);
//			$columnas = array(
//								'entero_1' => 'PERIODO_LIQUIDADO',
//								'texto_1' => 'TIPO_NRO_DOCUMENTO',
//								'texto_2' => 'SOCIO',
//								'texto_5' => 'TIPO_NUMERO',
//								'texto_4' => 'REF_PROVEEDOR',
//								'texto_5' => 'PRODUCTO_CONCEPTO',
//								'texto_8' => 'PERIODO_CUOTA',
//								'texto_6' => 'CUOTA',
//								'texto_10' => 'FECHA_REVERSO',
//								'texto_9' => 'PERIODO_INFORMADO_PROVEEDOR',
//								'decimal_1' => 'IMPORTE_REVERSADO',
//								'decimal_2' => '%_COMISION',
//								'decimal_3' => 'COMISION_REVERSADA',
//								'decimal_4' => 'NETO_PROVEEDOR',
//			);
//
//			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_4');
//			$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_3,AsincronoTemporal.texto_4');
//			$reversos = $oListado->getDetalleToExcel($conditions,$order,$columnas);
//
//			foreach($reversos as $idx => $cuota){
//				$cuota['PERIODO_CUOTA'] = $oListado->periodo($cuota['PERIODO_CUOTA']);
//				$cuota['PERIODO_LIQUIDADO'] = $oListado->periodo($cuota['PERIODO_LIQUIDADO']);
//				$reversos[$idx] = $cuota;
//			}
//
//			$this->set('reversos',$reversos);
//
//
//			// COBROS POR CAJA
//			$columnas = array(
//								'texto_1' => 'TIPO_NRO_DOCUMENTO',
//								'texto_2' => 'APELLIDO_NOMBRE',
//								'texto_5' => 'FECHA',
//								'texto_4' => 'TIPO COBRO',
//								'texto_12' => 'TIPO CANCEL',
//								'texto_13' => 'FORMA CANCEL',
//								'texto_8' => 'TIPO_NUMERO',
//								'texto_9' => 'PROVEEDOR_CONCEPTO',
//								'texto_14' => 'NRO_REF_PROVEEDOR',
//								'texto_11' => 'CUOTA',
//								'texto_6' => 'PERIODO',
//								'decimal_1' => 'COBRADO',
//			);
//			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_3');
//			$cobrosByCaja = $oListado->getDetalleToExcel($conditions,array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_5,AsincronoTemporal.texto_11'),$columnas);
//
//			foreach($cobrosByCaja as $idx => $cuota){
//				$cuota['PERIODO'] = $oListado->periodo($cuota['PERIODO']);
//				$cobrosByCaja[$idx] = $cuota;
//			}
//
//			$this->set('cobrosByCaja',$cobrosByCaja);
//
//			$this->render('/listados/liquidacion_proveedores/reportes/liquidacion_detallada_general_xls','blank');

		endif;

		$this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);

	}


	function reporte_control_vtos(){

		$disableForm = 0;
		$showAsincrono = 0;

		App::import('Model','Proveedores.Proveedor');
		$oPRV = new Proveedor();

        $this->set('periodo_ctrl',date('Y-m-d'));

		if(!empty($this->data)):

			$disableForm = 1;
			$showAsincrono = 1;

			$periodo_ctrl = $this->data['Liquidacion']['periodo_control']['year'].$this->data['Liquidacion']['periodo_control']['month'];
			$proveedor_id = $this->data['Liquidacion']['proveedor_id'];
			$codigo_organismo = $this->data['Liquidacion']['codigo_organismo'];
            $tipoProducto = $this->data['Liquidacion']['tipo_producto'];
            $tipoCuota = $this->data['Liquidacion']['tipo_cuota'];

			$tipo_informe_desc = (!empty($proveedor_id) ? $oPRV->getRazonSocial($proveedor_id) : "TODOS")." | ".$oPRV->GlobalDato('concepto_1',$codigo_organismo);

			$this->set('periodo_ctrl',$periodo_ctrl);
			$this->set('tipo_informe_desc',$tipo_informe_desc);
			$this->set('proveedor_id',$proveedor_id);
			$this->set('codigo_organismo',$codigo_organismo);
            $this->set('tipoProducto',$tipoProducto);
            $this->set('tipoCuota',$tipoCuota);
			$this->set('a_vencer',(isset($this->data['Liquidacion']['a_vencer']) ? 1 : 0));

		endif;


		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

			App::import('model','Mutual.ListadoService');
			$oListado = new ListadoService();


			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();

			$asinc = $oASINC->read('p1,p2,p3,p4,p6',$this->params['url']['pid']);

            $this->redirect('/mutual/listados/download/'.$asinc['Asincrono']['p6']);

//			$oPRV->unbindModel(array('hasMany' => array('MutualProducto')));
//			$this->set('proveedor',$oPRV->read(null,$asinc['Asincrono']['p3']));
//			$this->set('periodo_control',$asinc['Asincrono']['p1']);
//			$this->set('codigo_organismo',$asinc['Asincrono']['p2']);
//			$this->set('a_vencer',(empty($asinc['Asincrono']['p4']) ? 0 : $asinc['Asincrono']['p4']));
//
//			$columnas = array(
//								'texto_7' => 'REF_PROVEEDOR',
//								'texto_1' => 'TIPO_NRO_DOCUMENTO',
//								'texto_2' => 'APELLIDO_NOMBRE',
//								'texto_15' => 'BANCO',
//                                'texto_16' => 'ORGANISMO',
//								'texto_14' => 'BENEFICIO',
//								'texto_3' => 'EMPRESA',
//								'texto_4' => 'TURNO',
//								'texto_5' => 'TIPO_NUMERO',
//								'texto_13' => 'PROVEEDOR',
//								'texto_6' => 'PRODUCTO_CONCEPTO',
//								'entero_2' => 'CANTIDAD_CUOTAS',
//								'entero_1' => 'CUOTA',
//								'decimal_1' => 'IMPORTE_TOTAL',
//								'decimal_2' => 'IMPORTE_CUOTA',
//								'decimal_3' => 'MORA',
//                                'texto_17' => 'VENDEDOR',
//			);
//
//			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid'],'AsincronoTemporal.clave_1' => 'REPORTE_1');
//			$order = array('AsincronoTemporal.texto_2,AsincronoTemporal.texto_9,AsincronoTemporal.entero_1');
//			$datos = $oListado->getDetalleToExcel($conditions,$order,$columnas);
//			$this->set('datos',$datos);
//
//			//saco el maximo de cuotas que quedan a vencer
//			if($asinc['Asincrono']['p4'] == 1):
//			App::import('Model','Shells.AsincronoTemporal');
//			$oTMP = new AsincronoTemporal();
//			$max = $oTMP->find('all',array('conditions' => $conditions, 'order' => array('AsincronoTemporal.entero_3 DESC'), 'limit' => 1));
//			if(!empty($max)) $max = $max[0]['AsincronoTemporal']['entero_3'];
//			else $max = 0;
//			else:
//				$max = 0;
//			endif;
//			$this->set('max',$max);
//			$this->render('reportes/control_vencimiento_xls','blank');

		endif;

		$this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);

	}

	function addRecibo($lqdIntercambioId=null, $liquidacionId=null){
		$this->Session->del('grilla_cobros');

		if(empty($lqdIntercambioId)) $this->redirect('importar/'.$liquidacionId);

		if(!empty($this->data)):
//			if($this->Liquidacion->guardarRecibo($this->data)){
//				$this->redirect('importar/' . $liquidacionId);
//			}else{
////				$this->Mensaje->errores("ERRORES: ",$this->BancoCuentaChequera->notificaciones);
//			}
   			$ReciboId = $this->Liquidacion->guardarRecibo($this->data);
   			if(!$ReciboId):
				$this->Mensaje->errorGuardar();
   			else:
				$this->Mensaje->okGuardar();
   				$this->redirect('editRecibo/' . $lqdIntercambioId . '/' . $liquidacionId);
			endif;

		endif;

		$oLqdInterCambio = $this->Liquidacion->importarModelo('LiquidacionIntercambio', 'mutual');
		$aLqdInterCambio = $oLqdInterCambio->get($lqdIntercambioId);

		$oBancoCuenta = $this->Liquidacion->importarModelo('BancoCuenta', 'cajabanco');
		$cmbBancoCuenta = $oBancoCuenta->comboByBanco($aLqdInterCambio['LiquidacionIntercambio']['banco_id']);

		$oTipoDocumento = $this->Liquidacion->importarModelo('TipoDocumento', 'config');
		$cmbRecibo = $oTipoDocumento->comboRecibo();

		$this->set('LqdInterCambio', $aLqdInterCambio);
		$this->set('cmbCuenta', $cmbBancoCuenta);
		$this->set('cmbRecibo', $cmbRecibo);
		$this->render('recibos/add_recibo');

	}


	function editRecibo($lqdIntercambioId=null, $liquidacionId){
		if(empty($lqdIntercambioId)) $this->redirect('importar/'.$liquidacionId);

		if(!empty($this->data)):
			if ($this->Liquidacion->anularRecibo($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;

			$this->redirect('importar/' . $liquidacionId);

		endif;

//		if(!empty($this->data)):
//			// Si accion es 1 Anular Recibo
////			if($this->data['Recibo']['accion'] == 1):
//				if($this->Liquidacion->anularRecibo($this->data)){
//					$this->redirect('importar/' . $liquidacionId);
//				}else{
//				}
//			// Si accion es 2 Re-Imprimir el Recibo
////			else:
////				$this->redirect('imprimir_recibo/' . $this->data['Recibo']['id']);
////			endif;
//
//		endif;

		$oLqdInterCambio = $this->Liquidacion->importarModelo('LiquidacionIntercambio', 'mutual');
		$aLqdInterCambio = $oLqdInterCambio->get($lqdIntercambioId);

		$aRecibo = $this->Liquidacion->getRecibo($aLqdInterCambio['LiquidacionIntercambio']['recibo_id']);
		$aRecibo['Recibo']['liquidacion_intercambio_id'] = $lqdIntercambioId;
		$aRecibo['Recibo']['liquidacion_id'] = $liquidacionId;
		$aRecibo['Recibo']['action'] = "editRecibo/" . $aRecibo['Recibo']['liquidacion_intercambio_id'] . '/' . $aRecibo['Recibo']['liquidacion_id'];
		$aRecibo['Recibo']['url'] = '/mutual/liquidaciones/editRecibo/0/' . $aRecibo['Recibo']['liquidacion_id'];

		$this->set('Recibo', $aRecibo);
		$this->render('recibos/edit_recibo');

	}

	function addRecibojp($liquidacionId){
		$this->Session->del('grilla_cobros');

//		if(empty($liquidacionId)) $this->redirect('importar/'.$liquidacionId);

		if(!empty($this->data)):

   			$ReciboId = $this->Liquidacion->guardarRecibo($this->data);
   			if(!$ReciboId):
				$this->Mensaje->errorGuardar();
   			else:
				$this->Mensaje->okGuardar();
   				$this->redirect('editRecibojp/' . $liquidacionId);
			endif;

//			if($this->Liquidacion->guardarRecibo($this->data)){
//				$this->redirect('importar/' . $liquidacionId);
//			}else{
//				$this->Mensaje->errores();
//			}

		endif;

		$oLiquidacion = $this->Liquidacion->importarModelo('Liquidacion', 'mutual');
		$aLiquidacion = $oLiquidacion->read(null, $liquidacionId);
		$aLiquidacion['Liquidacion']['banco_id'] = '99999';

		$oBancoCuenta = $this->Liquidacion->importarModelo('BancoCuenta', 'cajabanco');
		$cmbBancoCuenta = $oBancoCuenta->comboByBanco($aLiquidacion['Liquidacion']['banco_id']);

		$oTipoDocumento = $this->Liquidacion->importarModelo('TipoDocumento', 'config');
		$cmbRecibo = $oTipoDocumento->comboRecibo();

		$this->set('Liquidacion', $aLiquidacion);
		$this->set('cmbCuenta', $cmbBancoCuenta);
		$this->set('cmbRecibo', $cmbRecibo);
		$this->render('recibos/add_recibojp');

	}


	function editRecibojp($liquidacionId){

//		if(!empty($this->data)):
//			// Si accion es 1 Anular Recibo
//			if($this->data['Recibo']['accion'] == 1):
//				if($this->Liquidacion->anularRecibo($this->data)){
//					$this->redirect('importar/' . $liquidacionId);
//				}else{
//				}
//			// Si accion es 2 Re-Imprimir el Recibo
//			else:
//				$this->redirect('imprimir_recibo/' . $this->data['Recibo']['id']);
//			endif;
//
//		endif;

		if(!empty($this->data)):
			if ($this->Liquidacion->anularRecibo($this->data)):
				$this->Mensaje->okGuardar();
			else:
				$this->Mensaje->errorGuardar();
			endif;

			$this->redirect('importar/' . $liquidacionId);

		endif;

		$aLiquidacion = $this->Liquidacion->read(null, $liquidacionId);

		$aRecibo = $this->Liquidacion->getRecibo($aLiquidacion['Liquidacion']['recibo_id']);
		$aRecibo['Recibo']['liquidacion_id'] = $liquidacionId;
		$aRecibo['Recibo']['action'] = "editRecibojp/" . $aRecibo['Recibo']['liquidacion_id'];
		$aRecibo['Recibo']['url'] = '/mutual/liquidaciones/importar/' . $liquidacionId;

		$this->set('Recibo', $aRecibo);
		$this->render('recibos/edit_recibojp');

	}


	function getPeriodosImputados($order="DESC",$abiertos=1,$imputados=0,$organismo=0){
		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$abiertos = ($abiertos == 1 ? false : true);
		$imputados = ($imputados == 1 ? true : false);
		$organismo = (empty($organismo) ? null : $organismo);
		$periodosLiquidados = $oLiq->getPeriodosLiquidados($abiertos,$imputados,$organismo,$order,true);
		return $periodosLiquidados;
	}


	function imputar_comercios($id, $pid, $facturada=0){

            if(!empty($this->data)){

                $this->set('factura', 'NULL');
                if($this->Liquidacion->grabarFacturaLiquidacion($this->data)){
                    $this->set('factura', 'OK');
                }
                $id = $this->data['Liquidacion']['id'];
                $pid = $this->data['Liquidacion']['pid'];
            }


            $oLiq = $this->Liquidacion->importarModelo('Liquidacion','mutual');
            $liquidacion = $oLiq->cargar($id);

            $oPL = $this->Liquidacion->importarModelo('ProveedorLiquidacion','proveedores');

            $oListado = $this->Liquidacion->importarModelo('ListadoService','mutual');
            $proveedores = $oListado->getTemporalFacturas($pid,$id);

            $proveedores = $oLiq->cargarFacturado($proveedores, $id, $facturada);

            $this->set('PID',$pid);


            $this->set('liquidacion',$liquidacion);

            $this->set('proveedores',$proveedores);


	}


	function control_diskette_cjp($id=null,$codDto=1,$altaBaja = 'T'){

		if(empty($id))$this->redirect('consulta');
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		$liquidacion = $oLiq->cargar($id);
		$this->set('liquidacion',$liquidacion);

		$socios = $oLS->getDatosParaDisketteCJP($id,array('LiquidacionSocio.apenom,LiquidacionSocio.registro'),$codDto,$altaBaja);

//		$socios = $oLS->generarDisketteCJP($id);
		$this->set('socios',$socios);
		$this->set('codDto',$codDto);
		$this->set('altaBaja',$altaBaja);
		$this->render('reportes/control_diskette_cjp_xls','blank');

	}


	function anular_facturas($liquidacion_id, $pid){
            $return = true;

            $this->Liquidacion = $this->Liquidacion->importarModelo('Liquidacion','mutual');
            $liquidacion = $this->Liquidacion->cargar($liquidacion_id);

            // PROVEEDOR LIQUIDACION
            $this->ProveedorLiquidacion = $this->Liquidacion->importarModelo('ProveedorLiquidacion', 'proveedores');

            // FACTURA DE COMERCIO
            $this->ProveedorFactura = $this->Liquidacion->importarModelo('ProveedorFactura','proveedores');

            // FACTURA DE CLIENTES
            $this->ClienteFactura = $this->Liquidacion->importarModelo('ClienteFactura','clientes');
            // FACTURA DE CLIENTES DETALLES
            $this->FacturaDetalle = $this->Liquidacion->importarModelo('ClienteFacturaDetalle', 'clientes');

	    $aClienteFacturas = $this->ClienteFactura->findAll("ClienteFactura.liquidacion_id = $liquidacion_id");
	    foreach($aClienteFacturas as $aFactura):
	    	if(!$this->FacturaDetalle->deleteAll("ClienteFacturaDetalle.cliente_factura_id = " . $aFactura['ClienteFactura']['id'])):
                    $return = false;
                    break;
	    	endif;
	    	$aFactura['ClienteFactura']['anulado'] = 1;
	    	if(!$this->ClienteFactura->save($aFactura)):
                    $return = false;
                    break;
	    	endif;
	    endforeach;

	    if($return):
	    	if(!$this->ProveedorFactura->deleteAll("ProveedorFactura.liquidacion_id = " . $liquidacion_id)):
                    $return = false;
                endif;
	    endif;

	    if($return):
	    	if(!$this->ProveedorLiquidacion->deleteAll("ProveedorLiquidacion.liquidacion_id = " . $liquidacion_id)):
                    $return = false;
                    endif;
	    endif;

	    $OK = false;
	    if($return):
	    	$liquidacion['Liquidacion']['facturada'] = 0;
                $OK = $this->Liquidacion->save($liquidacion);
	    endif;

            $oPL = $this->Liquidacion->importarModelo('ProveedorLiquidacion','proveedores');

            $oListado = $this->Liquidacion->importarModelo('ListadoService','mutual');
            $proveedores = $oListado->getTemporalFacturas($pid,$liquidacion_id);

            $this->set('PID',$pid);

            $this->set('anular', $OK);

            $this->set('liquidacion',$liquidacion);

            $this->set('proveedores',$proveedores);



	}


	function diskette_cjp_nocob_cbu($id,$showListadoControl = null,$formato = null){

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($id));


		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();



		$showDatosDiskette = 0;
		$DISKETTE_UUID = null;

		if(!empty($this->data)):

			$showDatosDiskette = 1;

			$bancoIntercambio = $this->data['LiquidacionSocio']['banco_intercambio'];
			$fechaDebito = $oLS->armaFecha($this->data['LiquidacionSocio']['fecha_debito']);

			$datos = $oLS->getConsumosNoDescontadosCjpParaDebitoByCBU($this->data['LiquidacionSocio']['liquidacion_id'],$bancoIntercambio,$fechaDebito);

//			debug($datos);
//			exit;

			$this->Session->write("DISKETTE_" . $datos['diskette']['uuid'] ,base64_encode(serialize($datos)));
			$DISKETTE_UUID = $datos['diskette']['uuid'];


			$this->set('datos',$datos['info_procesada']);



		else:

			$datos = $oLS->getConsumosNoDescontadosCjpParaDebitoByCBU($id);
			$this->set('datos',$datos['info_procesada']);

		endif;

		if(!empty($showListadoControl)){

			$datos = unserialize(base64_decode($this->Session->read("DISKETTE_$showListadoControl")));

			$this->set('datos',$datos['info_procesada']);

			if($formato == 'XLS') $this->render('reportes/diskette_cjp_nocob_cbu_xls','blank');

			return;

		}

		$this->set('DISKETTE_UUID',$DISKETTE_UUID);

		$this->set('showDatosDiskette',$showDatosDiskette);


	}

	function cierre_liquidacion($id,$option){
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		Configure::write('debug',0);
		if($option == 0){
			if($oLiq->abrir($id)){
				echo $option;
				exit;
			}else{
				echo 1;
				exit;
			}
		}
		if($option == 1){
			if($oLiq->cerrar($id)){
				echo $option;
				exit;
			}else{
				echo 0;
				exit;
			}
		}
//		$liquidacion = $oLiq->read(null, $id);
//		$liquidacion['Liquidacion']['cerrada'] = $option;
//		$oLiq->save($liquidacion);

//		echo $option;
//		exit;
	}

	function imprimir_recibo($id){
		// traer Recibo
		$this->Cliente = $this->Liquidacion->importarModelo('Cliente', 'clientes');
		$aRecibo = $this->Cliente->getRecibo($id);
		$this->set('aRecibo', $aRecibo);
		$this->render('recibos/imprimir_recibo_pdf', 'pdf');

	}


	function listado_recuperos($liquidacion_id,$formato){

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$this->set('liquidacion',$oLiq->cargar($liquidacion_id));

		App::import('Model','Mutual.LiquidacionCuotaRecupero');
		$oRECUPERO = new LiquidacionCuotaRecupero();

		$recuperosEmitidos = $oRECUPERO->getByLiquidacion($liquidacion_id);

		$this->set('recuperosEmitidos',$recuperosEmitidos);

		if($formato == 'PDF') $this->render('reportes/listado_recuperos_pdf','pdf');
		else $this->render('reportes/listado_recuperos_xls','blank');


	}


	function get_ultimo_periodo_cerrado($organismo,$mesesOffSet=0){
		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		return $oLiq->getUltimoPeriodoCerrado($organismo,$mesesOffSet);
	}


	function get_periodos_disponibles($organismo,$cantidadPeriodos=1,$periodoMinimo=0){
            App::import('model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $periodoMinimo = (empty($periodoMinimo) ? NULL : $periodoMinimo);
            return $oLiq->generarPeriodosDesdeUltimoCerrado($organismo,$cantidadPeriodos,($periodoMinimo == 0 ? NULL: $periodoMinimo));

	}


	function get_intercambio($intercambio_id){
		App::import('model','Mutual.LiquidacionIntercambio');
		$oLI = new LiquidacionIntercambio();
		return $oLI->get($intercambio_id);
	}


	function consolidado(){

		$disableForm = 0;
		$showAsincrono = 0;

		App::import('Model','Proveedores.Proveedor');
		$oPRV = new Proveedor();

		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$periodosCorte = $oLiq->getPeriodosLiquidados(false,true,null,'DESC',true);
		$this->set('periodosCorte',$periodosCorte);

		if(!empty($this->data)):

			$disableForm = 1;
			$showAsincrono = 1;

			$periodo_ctrl = $this->data['Liquidacion']['periodo_control'];
			$proveedor_id = $this->data['Liquidacion']['proveedor_id'];
			$codigo_organismo = $this->data['Liquidacion']['codigo_organismo'];
			$codigo_empresa = (isset($this->data['Liquidacion']['codigo_empresa']) && !empty($this->data['Liquidacion']['codigo_empresa']) ? $this->data['Liquidacion']['codigo_empresa'] : null);
			$detallado = (isset($this->data['Liquidacion']['detallado']) ? 1 : 0);

			$tipo_informe_desc = (!empty($proveedor_id) ? $oPRV->getRazonSocial($proveedor_id) : "TODOS LOS PROVEEDORES")." | ".(!empty($codigo_organismo) ? $oPRV->GlobalDato('concepto_1',$codigo_organismo) : "TODOS LOS ORGANISMOS");
			if(!empty($codigo_empresa)) $tipo_informe_desc .= " | " . $oPRV->GlobalDato('concepto_1',$codigo_empresa);
			if($detallado == 1) $tipo_informe_desc .= " | DETALLADO";
			$this->set('periodo_ctrl',$periodo_ctrl);
			$this->set('tipo_informe_desc',$tipo_informe_desc);
			$this->set('proveedor_id',$proveedor_id);
			$this->set('codigo_organismo',$codigo_organismo);
			$this->set('codigo_empresa',$codigo_empresa);
			$this->set('detallado',$detallado);

		endif;


		if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])):

			App::import('model','Mutual.ListadoService');
			$oListado = new ListadoService();


			App::import('model','Shells.Asincrono');
			$oASINC = new Asincrono();

			$asinc = $oASINC->read('p1,p2,p3,p4,p5',$this->params['url']['pid']);

			$oPRV->unbindModel(array('hasMany' => array('MutualProducto')));
			$this->set('proveedor',$oPRV->read(null,$asinc['Asincrono']['p3']));
			$this->set('periodo_control',$asinc['Asincrono']['p1']);
			$this->set('codigo_organismo',$asinc['Asincrono']['p2']);

			$detallado = trim($asinc['Asincrono']['p5']);
//			$this->set('detallado',$detallado);


			if($detallado == 1):
				$columnas = array(
									'texto_1' => 'ORGANISMO',
									'texto_2' => 'EMPRESA',
									'texto_3' => 'TURNO',
									'texto_4' => 'PROVEEDOR',
									'texto_5' => 'DOCUMENTO',
									'texto_6' => 'APELLIDO Y NOMBRE',
									'entero_2' => '#SOCIO',
									'entero_1' => '#ORDEN',
									'texto_7' => 'TIPO_NRO',
									'decimal_1' => 'IMPORTE_PERIODO',
									'decimal_3' => 'IMPORTE_MORA',
									'decimal_2' => 'COBRADO_PERIODO',
									'decimal_4' => 'COBRADO_MORA',
									'decimal_5' => 'A VENCER',
									'entero_3' => 'CUOTAS_DEVENGADAS',
									'entero_4' => 'CUOTAS_PAGAS',
									'entero_5' => 'CUOTAS_VENCIDAS',
									'entero_6' => 'CUOTAS_AVENCER',
				);
				$order = array('AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.texto_4,AsincronoTemporal.texto_6');
			else:

				$columnas = array(
									'texto_1' => 'ORGANISMO',
									'texto_2' => 'EMPRESA',
									'texto_3' => 'TURNO',
									'texto_4' => 'PROVEEDOR',
									'decimal_1' => 'IMPORTE_PERIODO',
									'decimal_3' => 'IMPORTE_MORA',
									'decimal_2' => 'COBRADO_PERIODO',
									'decimal_4' => 'COBRADO_MORA',
									'decimal_5' => 'A VENCER',
				);
				$order = array('AsincronoTemporal.texto_1,AsincronoTemporal.texto_2,AsincronoTemporal.texto_4');
			endif;

			$conditions = array('AsincronoTemporal.asincrono_id' => $this->params['url']['pid']);


			$datos = $oListado->getDetalleToExcel($conditions,$order,$columnas);
			$this->set('datos',$datos);

			$this->render('reportes/consolidado_xls','blank');
			return;

		endif;
		$this->set('disable_form',$disableForm);
		$this->set('show_asincrono',$showAsincrono);

	}


	function combo_periodos_ajax($organismo=null,$selected=null,$empty=null,$abierta=false,$imputada=true){
		$conditions = $values = array();
		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$values = $oLiq->getPeriodosLiquidados($abierta,$imputada,$organismo,'DESC',true);
		$this->set('values',$values);
		$this->set('selected',(empty($selected) ? null : $selected));
		$this->set('empty',$empty);
		$this->render(null,'ajax');
	}


	function importar_generar_lote($liquidacion_id = null,$bancoId=null){

		if(empty($liquidacion_id)) parent::noDisponible();

		$this->set('bancoId',$bancoId);

		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$liquidacion = $oLiq->cargar($liquidacion_id);
		if(empty($liquidacion)) parent::noDisponible();
		$this->set('liquidacion',$liquidacion);

		App::import('Model','mutual.LiquidacionSocioEnvio');
		$oLSE = new LiquidacionSocioEnvio();
		$lotes = $oLSE->getByLiquidacionId($liquidacion_id,false,$bancoId);
		$this->set('lotes',$lotes);

		$registros = null;
		$datosResumen = null;
		$preimputar = 0;
		if(!empty($this->data)){

			App::import('Model','mutual.LiquidacionSocioEnvioRegistro');
			$oLSER = new LiquidacionSocioEnvioRegistro();

			App::import('Model','config.BancoRendicionCodigo');
			$oCODIGOS = new BancoRendicionCodigo();


			if($this->data['LiquidacionSocioEnvio']['procesar'] == 'CABECERA'){

				if(!empty($this->data['LiquidacionSocioEnvio']['identificador_debito'])){

					$registros = $oLSER->getRegistrosByLoteByIdentificador($this->data['LiquidacionSocioEnvio']['id'],$this->data['LiquidacionSocioEnvio']['identificador_debito']);

					$this->set('registros',$registros);


					$envio = $oLSE->getEnvio($this->data['LiquidacionSocioEnvio']['id']);
					// 				debug($envio['LiquidacionSocioEnvio']['banco_id']);

					$codigos = $oCODIGOS->getCodigos($envio['LiquidacionSocioEnvio']['banco_id']);
					// 				debug($codigos);
					$this->set('codigos',$codigos);
				}else if(!empty($this->data['LiquidacionSocioEnvio']['nro_cta_bco']) && empty($this->data['LiquidacionSocioEnvio']['identificador_debito'])){

					$registros = $oLSER->getRegistrosByLoteByNroCta($this->data['LiquidacionSocioEnvio']['id'],$this->data['LiquidacionSocioEnvio']['nro_cta_bco']);
					$this->set('registros',$registros);
					$envio = $oLSE->getEnvio($this->data['LiquidacionSocioEnvio']['id']);
					$codigos = $oCODIGOS->getCodigos($envio['LiquidacionSocioEnvio']['banco_id']);
					$this->set('codigos',$codigos);

				}else if(isset($this->data['LiquidacionSocioEnvio']['preimputar']) && empty($this->data['LiquidacionSocioEnvio']['nro_cta_bco']) && empty($this->data['LiquidacionSocioEnvio']['identificador_debito'])){

					$registros = $oLSER->getRegistrosByLote($this->data['LiquidacionSocioEnvio']['id'],$bancoId);
					$this->set('registros',$registros);
					$envio = $oLSE->getEnvio($this->data['LiquidacionSocioEnvio']['id']);
					$codigos = $oCODIGOS->getCodigos($envio['LiquidacionSocioEnvio']['banco_id']);
					$this->set('codigos',$codigos);
					$preimputar = 1;



				}else{
					//saco el resumen del lote
					$datosResumen = $oLSER->getResumenByEnvio($this->data['LiquidacionSocioEnvio']['id']);
					$this->set('datosResumen',$datosResumen);
// 					$oLSER->query("UPDATE `liquidacion_socio_envio_registros` AS `LiquidacionSocioEnvioRegistro`
// 								SET `LiquidacionSocioEnvioRegistro`.`codigo_rendicion` = 998,
// 								`LiquidacionSocioEnvioRegistro`.`descripcion_codigo` = 'PENDIENTE DE INFORMAR X BANCO'
// 								where `LiquidacionSocioEnvioRegistro`.`liquidacion_socio_envio_id` = ".$this->data['LiquidacionSocioEnvio']['id']."
// 								AND `LiquidacionSocioEnvioRegistro`.`procesado` = 0");


// 					$oLSER->updateAll(array('LiquidacionSocioEnvioRegistro.codigo_rendicion' => '998','LiquidacionSocioEnvioRegistro.descripcion_codigo' => 'PENDIENTE DE INFORMAR X BANCO'),array('LiquidacionSocioEnvioRegistro.liquidacion_socio_envio_id' => $this->data['LiquidacionSocioEnvio']['id'], 'LiquidacionSocioEnvioRegistro.procesado' => 0));

// 					debug($this->data);

				}



			}

			$this->set('preimputar',$preimputar);

			if($this->data['LiquidacionSocioEnvio']['procesar'] == 'DETALLE'){



				foreach($this->data['LiquidacionSocioEnvioRegistro']['id'] as $id => $cadena){

					list($codigo,$descripcion) = explode("|",$cadena);


					$oLSER->unbindModel(array('belongsTo' => array('LiquidacionSocioEnvio','LiquidacionSocio')));
					$registro = $oLSER->read(null,$id);
					$registro['LiquidacionSocioEnvioRegistro']['codigo_rendicion'] = $codigo;
					$registro['LiquidacionSocioEnvioRegistro']['descripcion_codigo'] = $descripcion;
					$registro['LiquidacionSocioEnvioRegistro']['procesado'] = 1;

					$oLSER->save($registro);

				}


			}

			$selected = array();

			if($this->data['LiquidacionSocioEnvio']['procesar'] == 'GENERAR_PREVIEW_LOTE'){

				if(!empty($this->data['LiquidacionSocioEnvioRegistro']['include_id'])){

// 					debug($this->data);

					$codigoPagos = $oCODIGOS->getCodigosPago($bancoId);

					$codigoPagosDesc = $codigoPagos[0]['BancoRendicionCodigo']['descripcion'];
					$codigoPagos = $codigoPagos[0]['BancoRendicionCodigo']['codigo'];

					foreach ($this->data['LiquidacionSocioEnvioRegistro']['include_id'] as $id => $value){

						$reg = $this->data['LiquidacionSocioEnvioRegistro']['registro_serialized'][$id];
						$reg = base64_decode($reg);
						$reg = unserialize($reg);

						$estado = $this->data['LiquidacionSocioEnvioRegistro']['estado_id'][$id];
						list($codigo,$descripcion) = explode("|",$estado);

						$codigo = (empty($codigo) ? $codigoPagos:$codigo);
						$descripcion =  (empty($descripcion) ? $codigoPagosDesc:$descripcion);

						$reg['codigo_estado'] = $codigo;
						$reg['codigo_estado_desc'] = $descripcion;

                                                if(!$oCODIGOS->isCodigoPago($bancoId, $codigo)){
                                                    $reg['decode']['indica_pago'] = 0;
                                                }

//						debug($reg);
						array_push($selected, $reg);

					}

				}
			}

			$this->set('selected',$selected);

			if($this->data['LiquidacionSocioEnvio']['procesar'] == 'GENERAR_LOTE'){


				$fechaAcredita = $oLSER->armaFecha($this->data['LiquidacionSocioEnvio']['fecha_acreditacion']);
				$fechaAcredita = date('Ymd',strtotime($fechaAcredita));
// 				debug($fechaAcredita);
				$lote = base64_decode($this->data['LiquidacionSocioEnvio']['lote']);
				$lote = unserialize($lote);


				if(!empty($lote)){

					App::import('Model','config.Banco');
					$oBANCO = new Banco();

                    App::import('Model','mutual.LiquidacionIntercambio');
                    $oINTER = new LiquidacionIntercambio();


					$archivo = array();
					$cantidad = 0;
					$importe = 0;
                    $fileName = null;

					if($bancoId == '00011'):

						App::import('Model','mutual.LiquidacionSocioEnvio');
						$oLSE = new LiquidacionSocioEnvio();
						$envio = $oLSE->read(null,$this->data['LiquidacionSocioEnvio']['id']);
						$envio = explode("\r\n",$envio['LiquidacionSocioEnvio']['lote']);
                        array_push($archivo,$envio[0]);

						foreach($lote as $reg){
							$registro = $reg['LiquidacionSocioEnvioRegistro']['registro'];
							$estado = $reg['codigo_estado_desc'];
							$estado = str_pad(trim($estado), 30, " ", STR_PAD_RIGHT);
							$cad1 = substr($registro,0,33);
							$cad2 = substr($registro,33,8);
							$cad3 = substr($registro,41,1);
							$cad4 = substr($registro,42,30);
							$cad5 = substr($registro,72,strlen($registro));
                            $cad5 = str_replace("\r\n","", $cad5);
							array_push($archivo, $cad1.$fechaAcredita.$cad3.$estado.$cad5);
							$cantidad++;
							$importe += $reg['LiquidacionSocioEnvioRegistro']['importe_adebitar'];
						}

						$campos = array($cantidad,$importe);
						$pie = $oBANCO->armaStringPieBancoNacion($campos);

                        array_push($archivo,$pie);
                        $fileName = "NACION_LOTE_COBRANZA_$fechaAcredita.txt";
					endif;

					if($bancoId == '99999'):
						foreach($lote as $reg):
							$registro = $reg['LiquidacionSocioEnvioRegistro']['registro'];
							$cad1 = substr($registro,0,81);
							$estado = $reg['codigo_estado'];
							array_push($archivo, $cad1.$estado.$fechaAcredita);
						endforeach;
                        $fileName = "MUTUAL_RECIBOSUELDO_$fechaAcredita.txt";
					endif;


                    $liquidacionId = $this->data['LiquidacionSocioEnvio']['liquidacion_id'];
                    $periodo = $this->data['LiquidacionSocioEnvio']['periodo'];
                    $codigoOrganismo = $this->data['LiquidacionSocioEnvio']['codigo_organismo'];
                    if($oINTER->generarLote($liquidacionId, $periodo, $codigoOrganismo, $bancoId, $fileName, $archivo)){

                        $this->Mensaje->ok("ARCHIVO GENERADO CORRECTAMENTE!");
                    }


//                    debug($this->data);
//                    exit;
//
//                    Configure::write('debug',0);
//                    header("Content-type: text/plain");
//                    header('Content-Disposition: attachment;filename="'.$fileName.'"');
//                    header('Cache-Control: max-age=0');
//                    foreach($archivo as $linea){
//                        echo $linea."\r\n";
//                    }
//
//					exit;


				}

// 				debug($lote);
			}

		}

		$this->render('importar_generar_lote_'.$bancoId);
	}

	function importar_generar_lote_download($envioId = null,$bancoId=null){

		App::import('Model','mutual.LiquidacionSocioEnvio');
		$oLSE = new LiquidacionSocioEnvio();

		$envio = $oLSE->read(null,$envioId);
		$this->set('envio',$envio);

		$this->render('importar_generar_lote_download_'.$bancoId,'blank');
	}


	function exportar_masivo($periodo = null){

		if(empty($periodo)) parent::noDisponible();

		App::import('Model','Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();

		$turnos = $oLS->getResumenByTurnoByPeriodo($periodo);
		$this->set('turnos',$turnos);
		$this->set('periodo',$periodo);
	}


	function getPeriodosFacturados($order="DESC",$facturados=0,$organismo=0){
		App::import('model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		$facturados = ($facturados == 1 ? true : false);
		$organismo = (empty($organismo) ? null : $organismo);
		$periodosFacturados = $oLiq->getPeriodosFacturados($facturados,$organismo,$order,true);
		return $periodosFacturados;
	}


    function scoring($liquidacion_id = null,$output=null){
        if(empty($liquidacion_id)) parent::noDisponible();
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
        $liquidacion = $oLiq->cargar($liquidacion_id);
        $this->set('liquidacion',$liquidacion);
        if(!empty($output)){
            if($output == 'XLS'){
                $datos = $oLiq->cargarScoringByRango($liquidacion_id);
                $this->set('datos',$datos);
                $this->render('reportes/scoring_rango_xls','blank');
            }
        }
        $scoring = $oLiq->cargarTotalesScoring($liquidacion_id);
        $historicos = $oLiq->cargarHistorico($liquidacion_id);
		$scores = $oLiq->cargarScoresHistorico($liquidacion_id);

		// debug($historicos);
		// exit;

        $this->set('scoring',$scoring);
        $this->set('historicos',$historicos);
		$this->set('scores',$scores);
    }


	function consulta2(){
            $liquidaciones = null;
            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
//            $liquidaciones = $oLiq->find('all',array('conditions' => array('Liquidacion.en_proceso' => 0),'order'=>array('Liquidacion.periodo DESC','Liquidacion.codigo_organismo ASC')));

            if(isset($_SESSION['MUTUAL_INI']['general']['consulta_liquidaciones_totalizada']) && $_SESSION['MUTUAL_INI']['general']['consulta_liquidaciones_totalizada'] == 1) $liquidaciones = $oLiq->getLiquidaciones();
            else $liquidaciones = $oLiq->find('all',array('conditions' => array('Liquidacion.en_proceso' => 0),'order'=>array('Liquidacion.periodo DESC','Liquidacion.codigo_organismo ASC')));

            $this->set('liquidaciones',$liquidaciones);


            $render = (isset($_SESSION['MUTUAL_INI']['general']['consulta_liquidaciones_totalizada']) && $_SESSION['MUTUAL_INI']['general']['consulta_liquidaciones_totalizada'] == 1 ? "consulta_4" : "consulta_3");

            $this->render($render);
	}

	function proceso_nuevo(){
		$periodo = null;
		
		App::import('Model','Mutual.Liquidacion');
		$oLiq = new Liquidacion();
		
		$this->set('tiposLiquidacion',$oLiq->tiposLiquidacion);
		
		if(!empty($this->data)){
                    $periodo = $this->data['Liquidacion']['periodo_ini']['year'].$this->data['Liquidacion']['periodo_ini']['month'];
                    $this->set('organismo',$this->data['Liquidacion']['codigo_organismo']);
					$this->set('periodo',$periodo);
					$this->set('pre_imputacion',(isset($this->data['Liquidacion']['pre_imputacion']) ? 1 : 0));
                    $this->set('tipo_deuda_liquida',(isset($this->data['Liquidacion']['tipo_deuda_liquida']) && !empty($this->data['Liquidacion']['tipo_deuda_liquida']) ? $this->data['Liquidacion']['tipo_deuda_liquida'] : 0));
                    //verificar si esta cerrada
                    if($oLiq->isCerrada($this->data['Liquidacion']['codigo_organismo'],$periodo)){
                        $this->Mensaje->error('ATENCION!: LIQUIDACION CERRADA');
                        $this->render('liquidar_deuda_nuevo');
                    }else if($oLiq->getUltimoPeriodoImputado($this->data['Liquidacion']['codigo_organismo']) > $periodo){
                        $this->Mensaje->error('ATENCION!: EXISTEN PERIODOS POSTERIORES IMPUTADOS PARA EL ORGANISMO');
                        $this->render('liquidar_deuda_nuevo');

                    }else{
                        $archivos = $oLiq->isArchivosGenerados($this->data['Liquidacion']['codigo_organismo'],$periodo);
                        $this->set('archivos',$archivos);
                        $this->render('liquidar_deuda_proceso_nuevo');
					}
		}else{
			$this->set('periodo',$periodo);
			$this->render('liquidar_deuda_nuevo');
		}

        }


        function importar_nuevo($id=null,$UID=null){

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $liquidacion = $oLiq->cargar($id);
            $liquidacion['recibo_link'] = '';
            if($liquidacion['Liquidacion']['recibo_id'] > 0):
                    $liquidacion['Liquidacion']['recibo_link'] = $oLiq->getReciboLink($liquidacion['Liquidacion']['recibo_id']);
            endif;
            $this->set('liquidacion',$liquidacion);

            if(!empty($this->data)){

		App::import('Model','Mutual.LiquidacionIntercambio');
		$oFile = new LiquidacionIntercambio();

                if(isset($this->data['LiquidacionIntercambio']['subdividir'])){
                    debug("SUBDIVIDOR");
                }else{
                    $idFile = $oFile->subir($this->data);
                    if(!empty($idFile)){
                        $this->Mensaje->ok('Archivo <strong>'. $this->data['LiquidacionIntercambio']['archivo']['name'] .'</strong> subido correctamente!');
                        $this->redirect('importar_nuevo_recibo/' . $this->data['LiquidacionIntercambio']['liquidacion_id'].'/'.$idFile);
                    }else{
                        $this->Mensaje->errores("ERRORES:",$oFile->notificaciones);
                    }

                }
            }




        }


        function importar_nuevo_recibo($liquidacion_id,$archivo_id){

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $liquidacion = $oLiq->cargar($liquidacion_id);
            $liquidacion['recibo_link'] = '';
            if($liquidacion['Liquidacion']['recibo_id'] > 0):
                    $liquidacion['Liquidacion']['recibo_link'] = $oLiq->getReciboLink($liquidacion['Liquidacion']['recibo_id']);
            endif;
            $this->set('liquidacion',$liquidacion);

        }


        function mora_cuota_uno_pdf($liquidacion_id){
            App::import('Model','Mutual.LiquidacionCuota');
            $oLC = new LiquidacionCuota();

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $liquidacion = $oLiq->cargar($liquidacion_id);
            $this->set('liquidacion',$liquidacion);
            $this->set('mora_cuota_uno',$oLC->get_detalle_mora_cuota($liquidacion_id,1));
            $this->render('reportes/mora_cuota_uno_pdf','pdf');
        }


        function mora_cuota_uno_xls($liquidacion_id){

            App::import('Model','Mutual.LiquidacionCuota');
            $oLC = new LiquidacionCuota();

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $liquidacion = $oLiq->cargar($liquidacion_id);
            $this->set('liquidacion',$liquidacion);


            $this->set('mora_cuota_uno',$oLC->get_detalle_mora_cuota($liquidacion_id,1));
            $this->render('reportes/mora_cuota_uno_xls','blank');
        }

        function mora_temprana_pdf($liquidacion_id){
            App::import('Model','Mutual.LiquidacionCuota');
            $oLC = new LiquidacionCuota();

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $liquidacion = $oLiq->cargar($liquidacion_id);
            $this->set('liquidacion',$liquidacion);
            $this->set('mora_temprana',$oLC->get_detalle_mora_temprana($liquidacion_id));
            $this->render('reportes/mora_temprana_pdf','pdf');
        }


        function mora_temprana_xls($liquidacion_id){

            App::import('Model','Mutual.LiquidacionCuota');
            $oLC = new LiquidacionCuota();

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $liquidacion = $oLiq->cargar($liquidacion_id);
            $this->set('liquidacion',$liquidacion);


            $this->set('mora_temprana',$oLC->get_detalle_mora_temprana($liquidacion_id));
            $this->render('reportes/mora_temprana_xls','blank');
        }

        function resumencontrol($periodo,$organismo){

            if(empty($periodo)) parent::noDisponible ();
            if(empty($organismo)) parent::noDisponible ();

            App::import('Model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $liquidacion = $oLiq->getByPeriodoOrganismo($periodo,$organismo);
            $this->set('liquidacion',$liquidacion);

            App::import('Model','Mutual.LiquidacionCuota');
            $oLC = new LiquidacionCuota();

            $this->set('datos',$oLC->resumencontrol($periodo,$organismo));

        }

        public function liquida_deuda_sp(){			
                App::import('Model','Mutual.Liquidacion');
                $oLiq = new Liquidacion();
                Configure::write('debug',0);
                $state = $oLiq->sp_liquida_sp();
                if($state['error'] == 0){
                        $this->redirect("exportar2/" . $state['liquidacion_id']);
                }
        }

        function notificacion($periodo) {
            $user = $this->Seguridad->user();
            $this->set('user_created',$user['Usuario']['usuario']);
            $this->set('periodo',$periodo);
        }
        
        function notifica_deuda() {
            
            App::import('Model', 'mutual.Notificacion');
            $oNOTI = new Notificacion();

            $notificaciones = $oNOTI->listar();
            
            $user = $this->Seguridad->user();
            $this->set('user_created',$user['Usuario']['usuario']);
            $this->set('notificaciones', $notificaciones);
           
        }
        
        function notifica_deuda_envio() {
            
            App::import('model','Mutual.Liquidacion');
            $oLiq = new Liquidacion();
            $periodos = $oLiq->getPeriodosLiquidados(FALSE,FALSE,null,'DESC',TRUE);
            krsort($periodos);
            $this->set('periodos',$periodos);
            
            $disableForm = $showAsincrono = false;
            $periodo = NULL;
            $logo_url = NULL;
            $codigoNotificacion = 'MUTUNOTI0001';
            
            if(!empty($this->data)) {
                
                $periodo = $this->data['Liquidacion']['periodo'];
                $codigoNotificacion = $this->data['Liquidacion']['codigoNotificacion'];
                
                $disableForm = $showAsincrono = true;
            }

            $user = $this->Seguridad->user();
            $this->set('user_created',$user['Usuario']['usuario']);
            $this->set('disable_form',$disableForm);
            $this->set('show_asincrono',$showAsincrono);
            $this->set('periodo',$periodo);
            $this->set('codigoNotificacion', $codigoNotificacion);
            
        }
        
        function notifica_deuda_detalle($notificacion_id = null) {
            
            if(isset($this->params['url']['pid']) || !empty($this->params['url']['pid'])){
                App::import('Model','Shells.Asincrono');
		$oASINC = new Asincrono();
                $asincrono = $oASINC->read('p3', $this->params['url']['pid']);
                $notificacion_id = $asincrono['Asincrono']['p3'];
            }
            
            
            App::import('Model', 'mutual.Notificacion');
            $oNOTI = new Notificacion();
            $notificacion = $oNOTI->cargarConDetalle($notificacion_id);
            $this->set('notificacion', $notificacion); 
        }
}
?>

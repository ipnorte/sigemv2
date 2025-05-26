<?php 

class TestingController extends AppController{

	var $name = 'Testing';
	var $uses = null;

	function beforeFilter(){
		$this->Seguridad->allow('blank','demo','estilos','plan','vtos','test','gendisk','test_xls','backup','liquidador','desbloqueardocumentos','desbloquearliquidacion');
		parent::beforeFilter();
	}
    
    function blank(){$this->render();}

    function index(){exit;}

	// function backup(){

	// 	$file = "/var/backups/mysql/acumulado/sigemce_db_20140115.tar.gz";

	// 	header('Content-Description: File Transfer');
	// 	header('Content-Type: application/octet-stream');
	// 	header('Content-Disposition: attachment; filename='.basename($file));
	// 	header('Content-Transfer-Encoding: binary');
	// 	header('Expires: 0');
	// 	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	// 	header('Pragma: public');
	// 	header('Content-Length: ' . filesize($file));
	// 	ob_clean();
	// 	flush();
	// 	readfile($file);
	// 	exit;

	// 	$ficheros = array_diff(scandir("/var/backups"), array('..', '.'));
	// 	$this->set('ficheros',$ficheros);
	// }


	// function demo(){
	// 	App::import('Model', 'contabilidad.PlanCuenta');
	// 	$oPlanCuenta = new PlanCuenta();
	// 	$ejercicioId = 1;

	// 	$planCuenta = $oPlanCuenta->traerPlanCuenta($ejercicioId);

	// 	$planCuentaXls = array();
	// 	foreach($planCuenta['datos'] as $cuenta):
	// 		$rubro = 'ACTIVO';
	// 		if($cuenta['PlanCuenta']['tipo_cuenta'] == 'PA') $rubro = 'PASIVO';
	// 		if($cuenta['PlanCuenta']['tipo_cuenta'] == 'PN') $rubro = 'PATRIMONIO NETO';
	// 		if($cuenta['PlanCuenta']['tipo_cuenta'] == 'RP') $rubro = 'RESULTADO POSITIVO';
	// 		if($cuenta['PlanCuenta']['tipo_cuenta'] == 'RN') $rubro = 'RESULTADO NEGATIVO';

	// 		$aTmpPlanCuenta = array(
	// 			'Cuenta' => $cuenta['PlanCuenta']['cuenta'],
	// 			'Descripcion' => $cuenta['PlanCuenta']['descripcion'],
	// 			'Rubro' => $rubro,
	// 			'Asiento' => ($cuenta['PlanCuenta']['imputable'] == 0 ? 'NO' : 'SI'),
	// 			'Sumariza' => $cuenta['PlanCuenta']['co_plan_cuenta_id']
	// 		);
	// 		array_push($planCuentaXls, $aTmpPlanCuenta);
	// 	endforeach;

	// 	$this->set('planCuenta', $planCuentaXls);
	// 	$this->set('ejercicioDescripcion', $planCuenta['ejercicio']['descripcion']);

	// 	$this->render(null, 'blank');

	// }

	function estilos(){}

	function add(){

	}

	// function vtos(){
	// 	$vtos = null;
	// 	$vtos1 = null;

	// 	if(!empty($this->data)){

	// 		App::import('Model','Proveedores.ProveedorVencimiento');
	// 		$oVto = new ProveedorVencimiento();

	// 		$fechaInicioOdto = $this->data['ProveedorVencimiento']['fecha'];
	// 		$periodoLiquidado = $this->data['ProveedorVencimiento']['periodo_liquidado'];
	// 		$organismo = $this->data['ProveedorVencimiento']['codigo_organismo'];
	// 		$proveedor_id = $this->data['ProveedorVencimiento']['proveedor_id'];
	// 		$beneficio_id = $this->data['ProveedorVencimiento']['persona_beneficio_id'];

	// 		$diaInicio = date('d',strtotime($fechaInicioOdto));
	// 		$anioPeriodo = substr($periodoLiquidado,0,4);
	// 		$mesPeriodo = substr($periodoLiquidado,4,2);

	// 		$fecha = $anioPeriodo .'-'.$mesPeriodo.'-'.$diaInicio;

	// 		$vtos = $oVto->calculaVencimientoByPeriodo($proveedor_id,$organismo,$periodoLiquidado,$fechaInicioOdto);
	// 		$vtos1 = $oVto->calculaVencimiento($proveedor_id,$beneficio_id,$fechaInicioOdto);



	// 	}

	// 	$this->set('vtos',$vtos);
	// 	$this->set('vtos1',$vtos1);
	// }


	// function liquidador($periodo,$propietario,$organismo){

    //         App::import('Vendor','exec',array('file' => 'exec.php'));
    //         App::import('Model','Shells.Asincrono');
    //         $oASINC = new Asincrono();

    //         $SHELL = new exec();

    //         $SQL = "select * from asincronos AS Asincrono
    //                         where proceso like 'liquida_deuda_fraccion%'
    //                         and p1 = '$periodo' and propietario = '$propietario'
    //                             and p2 = '$organismo'
    //                         and porcentaje <> 100 order by id";

    //         $procesos = $oASINC->query($SQL);
    //         if(!empty($procesos)){
    //             foreach($procesos as $proceso){
    //                 $pid = $proceso['Asincrono']['shell_pid'];
    //                 if(!$SHELL->is_running($pid)){
    //                     echo "<h4 style='color:red;'>PROCESO DETENIDO :: # ".$proceso['Asincrono']['id']. " - " . $proceso['Asincrono']['subtitulo']." [".$proceso['Asincrono']['porcentaje']."% | (".$proceso['Asincrono']['contador']."/".$proceso['Asincrono']['total'].")]</h4>";
    //                     echo "<p>COMANDO SHELL:</p>";
    //                     echo "<p>".$proceso['Asincrono']['txt1']."</p>";
    //                     echo "<hr/>";
    //                 }else{
    //                     echo "<h6 style='color:green;'>OK -> # ".$proceso['Asincrono']['id']. " - " . $proceso['Asincrono']['subtitulo']." [".$proceso['Asincrono']['porcentaje']."% | (".$proceso['Asincrono']['contador']."/".$proceso['Asincrono']['total'].")]</h6>";
    //                     echo "<hr/>";
    //                 }
    //             }
    //         }
    //         exit;
	// }


	function test($param = null){
	    
            App::import('Model', 'mutual.CancelacionOrden');
            $oCAN = new CancelacionOrden();
            $ret = $oCAN->getCancelacionBySolicitudMin(6332);
            debug($ret);
            
            
	    /*App::import('Vendor','SMTPMailer',array('file' => 'SMTPMailer.php'));
	    $mail = new SMTPMailer(true);
	    $mail->sendEmailBlankPassword("m.adrian.torres@gmail.com","Adrian", "Prueba");
	    debug($mail);*/
	    
/* 	    App::import('Model', 'Pfyj.SocioCalificacion');
 	    $oCALIFICACION = new SocioCalificacion(null);
 	    $califica = $oCALIFICACION->isStopDebit(4112,'202305');
 	    debug($califica);
*/            

	    
	    
        /*App::import('Model','config.Banco');
        $oBCO = new Banco();
        $str = $oBCO->decodeStringDebitoBancoSantanderRio("21CUOTA MUTU0327                  28506001400800697384962024032100000000000735002470002458917  REV");
        debug($str);*/

	    
        /*
        App::import('Model','ordenes_service');
        $obj = new OrdenesService(); 
	       
        $response = $obj->estadoCuenta('36808701','4HM1EPJU1218');
        debug($response);
	*/
            
            
	    
	    
	    
	    
// 	    App::import('Model', 'Pfyj.Socio');
// 	    $oSOCIO = new Socio(null);
// 	    $calificacion = $oSOCIO->getUltimaCalificacion(553,NULL,FALSE,TRUE,FALSE,'202206');
// 	    $noEnvia = $oSOCIO->GlobalDato('logico_2',$calificacion);
	    
// 	    debug($calificacion);
// 	    debug($noEnvia);
	    
	    
// 	    $path = '/home/adrian/Trabajo/clientes/argentina-nueva/CJPC/Pagos_202204_Cod2071.txt';
	    
// 	    $registros = array();
// 	    $registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
// 	    if(!is_array($registros)) return null;
	    
// 	    // limpiar cadenas
// 	    foreach ($registros as $i => $registro) {      
// // 	        echo strlen($registro)."</br>";
// 	        $registros[$i] = preg_replace('/[\x00-\x1F\x7F]/', '',utf8_encode($registro));
// // 	        $registros[$i] = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $registro);
// // 	        $registros[$i] = utf8_decode(preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', utf8_encode($registro)));
// 	    }

// // 	    debug($registros);
	    
//         App::import('Model','Mutual.LiquidacionSocio');
//         $oLS = new LiquidacionSocio();
	    
//         App::import('Model','config.Banco');
//         $oBCO = new Banco();
//         $codigo=207;
//         foreach ($registros as $registro) {
//             $decode = $oBCO->decodeNuevoStringDebitoCJP($registro);
//             $codDecod = intval($decode['codigo_dto']);
//             if($codigo !== $codDecod) {
//                 debug($decode);
//                 break;
//             }
            
//         }
        

        
//         $rendicionSocio['LiquidacionSocioRendicion'] = array();
        
//         foreach($decode as $key => $value){
//             $rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
//         }
//         $beneficio = intval($rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio']);
        
//         $rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio'] = str_pad($beneficio,6,0,STR_PAD_LEFT);
        
//         debug($rendicionSocio);
        
//         Configure::write('debug',3);
        
//         $socio = $oLS->find('all',array('conditions' => array(
//             'LiquidacionSocio.liquidacion_id' => 107,
//             'LiquidacionSocio.tipo' => $rendicionSocio['LiquidacionSocioRendicion']['tipo'],
//             'LiquidacionSocio.nro_ley' => $rendicionSocio['LiquidacionSocioRendicion']['nro_ley'],
//             'LiquidacionSocio.nro_beneficio' => $rendicionSocio['LiquidacionSocioRendicion']['nro_beneficio'],
//             'LiquidacionSocio.sub_beneficio' => $rendicionSocio['LiquidacionSocioRendicion']['sub_beneficio'],
//             'LiquidacionSocio.codigo_dto' => $rendicionSocio['LiquidacionSocioRendicion']['codigo_dto'],
//             'LiquidacionSocio.sub_codigo' => $rendicionSocio['LiquidacionSocioRendicion']['sub_codigo'],
//         )));
        
//         debug($socio);
        
        exit;
	    
// 	    App::import('model', 'proveedores.metodo_calculo_cuota');
// 	    $oCALC = new MetodoCalculoCuota();
// 	    $capital = 280;
// 	    $oCALC->tasa = 12.55;
// 	    $oCALC->cuotas = 6;
// 	    $result = $oCALC->calcularCuotaFrances($capital);
// 	    $result2 = $oCALC->calcularCuotaDirecta($capital);
// 	    debug($result);
// 	    debug($result2);
        exit;


//            App::import('Vendor','SMTPMailer',array('file' => 'SMTPMailer.php'));
//            $mail = new SMTPMailer(true);
//            $mail->sendEmailBlankPassword('m.adrian.torres@gmail.com','ADRIAN','ADRIAN TORRES');
//            $mail->sendEmailPINAcceso('m.adrian.torres@gmail.com','ADRIAN','8O7QMVK2TL84','201.212.54.200','201-212-54-200.cab.prima.net.ar');




//		App::import('Model','Mutual.OrdenDescuentoCuota');
//		$oCuota = new OrdenDescuentoCuota();
//                $cuotas = $oCuota->get_mora_by_orden_dto($param,'201901');
//                debug($cuotas);

            // App::import('model','mutual.LiquidacionCuota');
            // $oLC = new LiquidacionCuota();
            // $cuotas = $oLC->__armaResumenCBU_NUEVO(300,264,'201910','MUTUCORG2298',false,null,TRUE,TRUE);
            // debug($cuotas);
//            $cuotas = $oLC->distribuyeImporteCuotas($cuotas,10068.99);
//            debug($cuotas);


//            App::import('Model','Mutual.LiquidacionSocioNoimputada');
//            $obj = new LiquidacionSocioNoimputada();
//            App::import('Model','Mutual.LiquidacionSocio');
//            $obj = new LiquidacionSocio();
//            $cuotas = $obj->liquidar(2866,'201903','MUTUCORG2201',503);
//            debug($cuotas);

//            App::import('model','OrdenesService');
//            $obj = new OrdenesService();
//            $res = $obj->getOrdenesAprobadasEmitidasEntreFechas("4HM1EPJU1218",'2018-01-01','2018-01-5');
//            $res = json_decode($res);
//            debug($res);


//		App::import('model','pfyj.Persona');
//		$oP = new Persona();
//
//                $dom = $oP->getDomicilioByPersonaId($param, TRUE);
//                debug($dom);

//            App::import('Model','config.Banco');
//            $oBCO = new Banco();
//            $str = $oBCO->decode_str_debito_fenanjor_macro("022000058900032225568HERRERA MARTA JULIA                     01108246300824000716290824000716201572201811090000108700           ");
//            debug($str);
//            exit;


//		App::import('model','mutual.MutualProductoSolicitud');
//		$oSOLICITUD = new MutualProductoSolicitud();
//                $solicitud = $oSOLICITUD->read(NULL,8806);
////                $solicitud = $oSOLICITUD->get_orden_descuento_emitida(8806,$solicitud);
//                $solicitud = $oSOLICITUD->armaDatos($solicitud);
//                debug($solicitud);


//		App::import('Model','Pfyj.SocioCalificacion');
//		$oSC = new SocioCalificacion();
//                $stop = $oSC->isStopDebit(1943,'201808');
//                debug("*** $stop *** ");
//                $oSC->calificar(3102,'MUTUCALINORM',5442,'201811',date('Y-m-d'));



//            App::import('Model','OrdenesService');
//            $oService = new OrdenesService();
//            $data = $oService->getOrdenesNoAprobadas('E2RBBE8SS036');
//            debug($data);
//            $liquidacion_id = 537;
//            $socio_id = 12742;
//
//            App::import('Model','config.Banco');
//            $oBCO = new Banco();
//            $str = $oBCO->decodeStringDebitoBcoNacion("21046CA01081622468000000000111037201807030                              0000003632                                              ");
//            debug($str);
//            exit;



//            App::import('Model','Mutual.MutualAdicionalPendiente');
//            $oAP = new MutualAdicionalPendiente();
//            $adicionales = $oAP->generarAdicional(315, 321, 'MUTUCORG2202','201709', 'MUTUSICUMUTU', FALSE,FALSE);
//            debug($adicionales);

//            App::import('model','mutual.LiquidacionCuota');
//            $oLC = new LiquidacionCuota();
//            $cuotas = $oLC->armaImputacion(308,2143);
//            debug($cuotas);


//            App::import('model','mutual.Liquidacion');
//            $oLiq = new Liquidacion();
//            App::import('Model','Mutual.OrdenDescuentoCobro');
//            $oCOBRO = new OrdenDescuentoCobro();
//            App::import('Model','Mutual.LiquidacionSocio');
//            $oLS = new LiquidacionSocio();
////
//            $liq_id = 308;
//            $socio_id = 948;
//            $oLiq->unSetImputada($liq_id);
//            $oCOBRO->desimputarLiquidacion($liq_id, $socio_id);
//            $ret = $oLS->reliquidar($socio_id,'201705',false,false,'MUTUCORG2206',false,false);
//
////            $ret = $oLS->liquidar($socio_id,'201705','MUTUCORG2206',$liq_id,true);
//
//            debug($ret);
//
//
//            $resultado = $oCOBRO->imputarLiquidacion(308,1099, '2017-10-23', 'LIQ#308',FALSE,TRUE);
//            debug($resultado);







//            App::import('model','mutual.LiquidacionCuota');
//            $oLC = new LiquidacionCuota();
//
//            $cuotas = $oLC->getCuotasByCriterioImputacion(285, 1177);
//            debug($cuotas);

//            App::import('Model','Mutual.OrdenDescuentoCuota');
//            $oCuota = new OrdenDescuentoCuota();
//            $cuotas = $oCuota->getTotalDeudaBySocio(108,'201612');
//            debug($cuotas);

//            $orden_dto_id = $param;
//            $periodo = '201603';

//            App::import('Model','Mutual.OrdenDescuento');
//            $oOdto = new OrdenDescuento();
//            $oOdto->unbindModel(array('belongsTo' => array('Socio','Proveedor')));
//            $orden = $oOdto->read(null,10013);
//
//            $orden = $oOdto->getOrdenByNumero(4943, 'EXPTE', 'MUTUPROD0001');
//            debug($orden);
//
//
//            $tp = $oOdto->GlobalDato("concepto_4", 'MUTUPROD0001');
//            $orden = $oOdto->getOrdenByNumero(4943, 'EXPTE', 'MUTUPROD0002');
//            debug($orden);

//            App::import('model','mutual.MutualProductoSolicitud');
//            $oSOLICITUD = new MutualProductoSolicitud();
//
//            $solicitud = $oSOLICITUD->read(null,4943);
//            $solicitud = $oSOLICITUD->armaDatos($solicitud);
//            debug($solicitud);




//            $registros = array();
//            $registros = file("/home/adrian/trabajo/scripts/aman/BancoCOMAFI/rendicion/Noviembre/BE14269.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//            App::import('Model','config.Banco');
//            $oBCO = new Banco();
//            foreach($registros as $registro){
//                $deco = $oBCO->decode_str_debito_banco_comafi($registro);
//                debug($deco);
//            }



//            App::import('model','mutual.LiquidacionSocioRendicion');
//            $oLSR = new LiquidacionSocioRendicion();
//            echo $oLSR->checkBancoAndCBU('00007','0200305211000011143512');

//           debug(intval(16 / 20));
//
//		Configure::write('debug',3);
////
//                App::import('model','proveedores.MetodoCalculoCuota');
//                $oCALC = new MetodoCalculoCuota();
////                $oCALC->tasa = 9.99;
//                $oCALC->solicitado = 3000;
//                $oCALC->METODO_CALCULO = 1;
//                $oCALC->cuotas = 12;
//                $oCALC->porcAdic = 0;
//                $oCALC->porcIVA = 21;
//
//                $oCALC->determinar_tasa(519.20);
//                $oCALC->set_valor_cuota_directo(519.20);
//
////                $oCALC->armar_plan();
//                debug($oCALC);
//

//		App::import('model','pfyj.Socio');
//		$oSOCIO = new Socio();
//		$stop = $oSOCIO->isStopDebit(3295);

		//$calificacion = $oSOCIO->find('all',array('conditions' => array('SocioCalificacion.socio_id' => 3295),'order' => array('SocioCalificacion.created DESC'),'limit' => 1));

//		debug("** $stop **");
//		debug($calificacion);

//		App::import('Model','config.Banco');
//		$oBCO = new Banco();
//
//		$DECO = $oBCO->decodeStringDebitoCobroDigital('00006820160831AMARFIL CARLOS                          00033429761CUOTA     000000019700001COMPLETADO');
//		debug($DECO);

//		App::import('Model','Mutual.OrdenDescuentoCuota');
//		$oCuota = new OrdenDescuentoCuota();
//                $cuotas = $oCuota->getMoraByOrdenDtoHastaPeriodo(17448,'201608');
//                debug($cuotas);

//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();
//
//		$ret = $oLS->reliquidar(11653,'201607',false,false,'MUTUCORG2201',false,false,false);
//                debug($ret);

//            App::import('Model','config.GlobalDato');
//            $oGLOBAL = new GlobalDato();
//            $prefix = 'PROVREAS';
//            $nReg = $oGLOBAL->query("SELECT concat('PROVREAS',lpad(cast(max(right(`GlobalDato`.`id`,4)) as unsigned) + 1,4,0)) as nID,
//                                    cast(max(right(`GlobalDato`.`id`,4)) as unsigned) + 1 as nReg
//                                    FROM `global_datos` AS `GlobalDato` WHERE `GlobalDato`.`id`
//                                    like '$prefix%' AND `GlobalDato`.`id` <> '$prefix' ");
//            debug($nReg);

//		App::import('Model','config.Banco');
//		$oBCO = new Banco();
//
//		$DECO = $oBCO->decodeStringDebitoBancoFrances('421039941  0000000000000000001414017008484000004498544600000000019410000                          20160226                 PCARGADO OK                                                                                                                    ');
//		debug($DECO);

//        App::import('Vendor','RapiPago',array('file' => 'pago_facil/rapi_pago.php'));
//        $oRP = new RapiPago(1864,236693, 1233.25, '2016-03-02',0.55,5);
//        $bc = $oRP->get_bar_code();
//        debug($bc);
//        debug($oRP->validate_bar_code("1230000011100000000222000333001662000000004"));
//        debug($oRP->validate_bar_code("33300000111000000002220003332516062000055050"));
//        debug($oRP->validate_bar_code("3330000186400000236693123325160620055050"));

//		App::import('Model','Mutual.OrdenDescuentoCuota');
//		$oCuota = new OrdenDescuentoCuota();
//
//        $oCuota->borrarConsumoPermanenteDevengado(3,'201508');

//		App::import('Model','Mutual.MutualAdicionalPendiente');
//		$oADICIONAL = new MutualAdicionalPendiente();
//        ini_set("memory_limit", "500M");
//        $datos = $oADICIONAL->query("call prueba(18133)");
//        debug($datos);

//        $adicionales = $oADICIONAL->generarAdicional(31, 23, 'MUTUCORG2201', '201508', 'MUTUSICUMUTU', true);


//		App::import('Model','Mutual.OrdenDescuentoCuota');
//		$oCuota = new OrdenDescuentoCuota();
//
//        $cuotas = $oCuota->cuotasAdeudadasBySocioAlPeriodoByOrganismo(188,'201508','MUTUCORG2201','MUTUSICUMUTU',true);
//        debug($cuotas);

// 		App::import('Model','Mutual.LiquidacionSocio');
// 		$oLS = new LiquidacionSocio();
//
//        $resultados = $oLS->generarDisketteCBUFromAsincronoProcess(17958);
//        debug($resultados);

//		App::import('Model','config.Banco');
//		$oBCO = new Banco();
//
//		$DECO = $oBCO->decodeStringMargenComercial('20000008107276473460000001596000000000008900000000008900000000000000COBRADO                                       000120150203000000000000000000000000');
//		debug($DECO);

// 		App::import('Model','mutual.MutualServicio');
// 		$oSERV = new MutualServicio();

// 		$values = $oSERV->getDatosAdicionales(1);

// 		$solicitudes = $oSERV->getSolicitudesBySocioID(21678,null,1);
// 		debug($solicitudes);

// 		App::import('Model','Mutual.LiquidacionSocio');
// 		$oLS = new LiquidacionSocio();

// 		$status = $oLS->liquidar(97,'201401','MUTUCORGMUTU',5,true);
// 		debug($status);

// 		App::import('Model', 'V1.Solicitud');
// 		$oSOLICITUD = new Solicitud();
// 		$solicitud = $oSOLICITUD->getSolicitud(538882);
// 		debug($solicitud);

// 		App::import('Model','Mutual.LiquidacionCuota');
// 		$oLQ = new LiquidacionCuota();

// 		$cuotas = $oLQ->armaImputacionCJP(292,13348);
// 		debug($cuotas);
//
//		$cuotas = $oLQ->__armaResumenCBU(212,19359,"201205","MUTUCORG2202");
//		$acu = 0;
//		debug($cuotas);
//		foreach($cuotas as $cuota){
//			$acu += $cuota[0]['importe_adebitar'];
//		}
//		debug($acu);
//
//		App::import('Model','Mutual.OrdenDescuentoCuota');
//		$oCuota = new OrdenDescuentoCuota();
//		$cuotas = $oCuota->cuotasAdeudadasBySocioAlPeriodoByOrganismo(19359,"201205","MUTUCORG2202");
//		debug($cuotas);
//			exit;

//		exit;

//		App::import('Model','Mutual.LiquidacionCuota');
//		$oLC = new LiquidacionCuota();
//
//		$cuotas = $oLC->__armaResumenCBU(195,5025,"201112","MUTUCORG2201");
//
//		debug($cuotas);

//		App::import('Model','Mutual.LiquidacionSocioRendicion');
//		$oLSR = new LiquidacionSocioRendicion();
//		$condProv = array();
//		$condProv['LiquidacionSocioRendicion.liquidacion_id'] = 198;
//		$condProv['LiquidacionSocioRendicion.socio_id'] = 18580;
//		$condProv['LiquidacionSocioRendicion.indica_pago'] = 1;
//		$fieldsProv = array("LiquidacionSocioRendicion.proveedor_id, sum(LiquidacionSocioRendicion.importe_debitado) as importe_debitado");
//		$groupProv = array("LiquidacionSocioRendicion.proveedor_id");
//		$ordProv = array("LiquidacionSocioRendicion.proveedor_id ASC");
//
//		$proveedores = $oLSR->find("all",array('conditions' => $condProv, "fields" => $fieldsProv, "group" => $groupProv, "order" => $ordProv));
//
//		debug($proveedores);
//
//
//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();
//
//		$ret = $oLS->reliquidar(18580,'201201',false,false,'MUTUCORG2201',false,false,false);
//
//		DEBUG($ret);


//		App::import('Model','Pfyj.SocioReintegro');
//		$oREINTEGRO = new SocioReintegro();
//		App::import('Model','Mutual.LiquidacionCuota');
//		$oLC = new LiquidacionCuota();
//		App::import('Model','Mutual.LiquidacionSocioRendicion');
//		$oLSR = new LiquidacionSocioRendicion();
//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();
//
//		$impoImputado = $oLC->getTotalImputadoBySocioByLiquidacion(195,16117);
//		$impoDebitado = $oLSR->getTotalBySocioByLiquidacion(16117,195,1);
//
//		$impoLiquidado = $oLS->getTotalImporteLiquidadoBySocio(195,16117);
//
//		//VERIFICO QUE NO TENGA REINTEGROS ANTICIPADOS
//		$anticipado = $oREINTEGRO->getTotalReintegrosAnticipados(16117,195);
//		$impoReintegro = abs($impoDebitado - $impoImputado);
//
//		debug($anticipado . " ** " . $impoReintegro);
//
//		$reintegros = $oREINTEGRO->getReintegrosAnticipados(16117,195);
//
////		debug($reintegros);
//
//		foreach($reintegros as $reintegro):
//			$reintegro['SocioReintegro']['importe_debitado'] = $reintegro['SocioReintegro']['pagos'];
//			$reintegro['SocioReintegro']['importe_reintegro'] = $reintegro['SocioReintegro']['pagos'];
//			debug($reintegro);
//		endforeach;


//		$path = WWW_ROOT . $this->getCampoIntercambio($intercambio_id,'archivo_file');
//		$path = "http://192.168.0.2/sigem/files/intercambio/201112_MUTUCORG2201_00011_AMAN1S.TXT";
//
////		if(!file_exists($path)) echo "error file";
//		$registros = array();
//		$registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//		if(!is_array($registros)) echo "empty";
//
////		debug($registros);
//
//		App::import('Model','config.Banco');
//		$oBANCO = new Banco();
//
//		foreach($registros as $registro){
//
//
//			$primerCaracter = substr(trim($registro),0,1);
//
//			if(2 == $primerCaracter):
//
//				$decode = $oBANCO->decodeStringDebitoBcoNacion($registro);
//
////				debug($decode);
//
//				$rendicionSocio = array();
//				$rendicionSocio['LiquidacionSocioRendicion']['id'] = 0;
//				$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_id'] = 195;
//				$rendicionSocio['LiquidacionSocioRendicion']['codigo_organismo'] = 'MUTUCORG2201';
//				$rendicionSocio['LiquidacionSocioRendicion']['registro'] = $registro;
//				$rendicionSocio['LiquidacionSocioRendicion']['periodo'] = '201112';
//				$rendicionSocio['LiquidacionSocioRendicion']['banco_intercambio'] = '00011';
//				$rendicionSocio['LiquidacionSocioRendicion']['orden_descuento_cobro_id'] = 0;
//				$rendicionSocio['LiquidacionSocioRendicion']['liquidacion_intercambio_id'] = 554;
//
//				foreach($decode as $key => $value){
//					$rendicionSocio['LiquidacionSocioRendicion'][$key] = $value;
//				}
//
//				debug($rendicionSocio);
//
//			endif;
//		}

//		echo $sucursal = str_pad(trim('03155'),5,'0',STR_PAD_LEFT);


// 		App::import('Model','config.Banco');
// 		$oBANCO = new Banco();

// 		$decode = $oBANCO->decodeStringDebitoMutual("00029936486AGUERO DANIEL EDUARDO                                000000063683001");
// 		debug($decode);

//		$registro = $oBANCO->genRegistroDisketteBanco('00191','20120215',86.64,1,16538,0,16538,'2610','03841139212','0110303630030308212547','MUTUCORG2201');
//		DEBUG($registro);

//		$path = "/home/adrian/dev/proyectos/MutualAman/sigem/cbu/testing/NACION20120315.TXT";
//		$registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//		$registros = array_slice($registros,1,count($registros) - 2);
//
//		foreach($registros as $lin => $registro){
//			$ret = $oBANCO->validateStringDetalleDebitoBancoNacion($registro);
//			if($ret['ERROR'] == 1){
//				debug($lin + 1 . " -- " .$registro);
//				debug($ret['MENSAJE']);
//			}
//		}
//		if($ret['ERROR'] == 0) debug ("ARCHIVO BANCO NACION OK");
//
//		$path = "/home/adrian/dev/proyectos/MutualAman/sigem/cbu/testing/MAIN108_20120315.TXT";
//		$registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//		$registros = array_slice($registros,1,count($registros) - 2);
//
//		foreach($registros as $lin => $registro){
//			$ret = $oBANCO->validateStringDetalleDebitoBancoCrediCoop($registro);
//			if($ret['ERROR'] == 1){
//				debug($lin + 1 . " -- " .$registro);
//				debug($ret['MENSAJE']);
//			}
//		}
//		if($ret['ERROR'] == 0) debug ("ARCHIVO BANCO CREDICOOP OK");
//
//		$path = "/home/adrian/dev/proyectos/MutualAman/sigem/cbu/testing/STBANK_20120315.TXT";
//		$registros = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//		$registros = array_slice($registros,1,count($registros) - 2);
//
//		foreach($registros as $lin => $registro){
//			$ret = $oBANCO->validateStringDetalleDebitoBancoStandarBank($registro);
//			if($ret['ERROR'] == 1){
//				debug($lin + 1 . " -- " .$registro);
//				debug($ret['MENSAJE']);
//			}
//		}
//		if($ret['ERROR'] == 0) debug ("ARCHIVO BANCO STANDAR OK");


//		App::import('Model','Mutual.LiquidacionSocioRendicion');
//		$oLSR = new LiquidacionSocioRendicion();
//
//		App::import('Model','Mutual.LiquidacionCuota');
//		$oLC = new LiquidacionCuota();
//
//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();
//
//		$liquidacion_id = 198;
//		$socio_id = 6268;
//		$periodo = '201201';
//		$organismo = 'MUTUCORG2201';
//
//		$ret = $oLS->reimputar($socio_id,$liquidacion_id);
//		debug($ret);

//		$condProv = array();
//		$condProv['LiquidacionSocioRendicion.liquidacion_id'] = $liquidacion_id;
//		$condProv['LiquidacionSocioRendicion.socio_id'] = $socio_id;
//		$condProv['LiquidacionSocioRendicion.indica_pago'] = 1;
//		$fieldsProv = array("LiquidacionSocioRendicion.proveedor_id, sum(LiquidacionSocioRendicion.importe_debitado) as importe_debitado");
//		$groupProv = array("LiquidacionSocioRendicion.proveedor_id");
//		$ordProv = array("LiquidacionSocioRendicion.proveedor_id ASC");
//
//		$proveedores = $oLSR->find("all",array('conditions' => $condProv, "fields" => $fieldsProv, "group" => $groupProv, "order" => $ordProv));
//
//
//
////		debug($proveedores);
//
//		foreach($proveedores as $proveedor):
//
//			$cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id,$proveedor['LiquidacionSocioRendicion']['proveedor_id']);
//			debug($cuotas);
////			$oLC->saveAll($cuotas);
//
//		endforeach;

//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();
//
//		$socios = $oLS->generarDisketteCJP(204,19919);
//		$socios = $oLS->generarDisketteCJP(204,17079);
//		$socios = $oLS->generarDisketteCJP(204,20100);
//		$socios = $oLS->generarDisketteCJP(204,37);
//		debug($socios);


//		App::import('Model','Mutual.LiquidacionCuota');
//		$oLC = new LiquidacionCuota();
//		$liquidacion_id = 203;
//		$socio_id = 2043;
//		$proveedor_id = 0;
//
//
//		$cuotas = $oLC->armaImputacion($liquidacion_id,$socio_id,$proveedor_id);
//
//		debug($cuotas);
//
//		$oLC->saveAll($cuotas);

//		$liquidacion_id = 203;
//		$socio_id = 16905;
//
//
//		App::import('Model','Mutual.LiquidacionIntercambio');
//		$oFile = new LiquidacionIntercambio();
//		$archivos = $oFile->find('all',array('conditions' => array(
//													'LiquidacionIntercambio.liquidacion_id' => $liquidacion_id,
//													'LiquidacionIntercambio.id' => array(594,596,597,598,599),
//											)
//		));
//
////		debug($archivos);
//
//
//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();
//
//		App::import('Model','Mutual.LiquidacionSocioRendicion');
//		$oLSR = new LiquidacionSocioRendicion();
//
//		foreach($archivos as $archivo):
//
//			if($archivo['LiquidacionIntercambio']['fragmentado'] == 0):
//
//
//			endif;
//
//			$sql = "SELECT
//						LiquidacionSocioRendicion.socio_id
//					FROM liquidacion_socio_rendiciones AS LiquidacionSocioRendicion
//					WHERE
//						LiquidacionSocioRendicion.liquidacion_id = ".$liquidacion_id."
//						AND LiquidacionSocioRendicion.liquidacion_intercambio_id = ".$archivo['LiquidacionIntercambio']['id']."
//						AND IFNULL(LiquidacionSocioRendicion.socio_id,0) <> 0
//						AND LiquidacionSocioRendicion.indica_pago = 1 AND LiquidacionSocioRendicion.socio_id = $socio_id"
//						.
//						(!empty($archivo['LiquidacionIntercambio']['proveedor_id']) ?
//						"
//						AND LiquidacionSocioRendicion.socio_id IN
//							(
//								SELECT
//									socio_id
//								FROM liquidacion_cuotas
//								WHERE
//									liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
//									AND proveedor_id = LiquidacionSocioRendicion.proveedor_id
//							)
//						"
//						: "")
//						.
//
//						"
//					GROUP BY LiquidacionSocioRendicion.socio_id;";
//			$socios = $oLSR->query($sql);
//			$socios = Set::extract('/LiquidacionSocioRendicion/socio_id',$socios);
//
////			debug($socios);
//
//			if(!empty($socios)):
//
//				foreach($socios as $socio_id):
//
//					$ret = $oLS->reliquidar($socio_id,'201202',true,false,'MUTUCORG2201',false,false,false);
//					debug($ret);
//
//				endforeach;
//
//			endif;
//
//		endforeach;



//		App::import('Model','Mutual.OrdenDescuentoCobro');
//		$oCOBRO = new OrdenDescuentoCobro();
//
//		$ret = $oCOBRO->imputarLiquidacion(203,18418,'2012-03-23','CBU#ADRIAN',false);
//		debug($ret);

//		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
//		$oCOBCU = new OrdenDescuentoCobroCuota();
//
//		$comision = $oCOBCU->calcularComisionCobranza(2413310,100);
//		debug($comision);

//		App::import('Model','Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();
//
//		$socios = $oLS->generarDisketteCJP(210,$param);
//		debug($socios);

//		App::import('Model','Mutual.MutualServicioValor');
//		$oSERV_VALOR = new MutualServicioValor();
//
//		$valores = $oSERV_VALOR->calcularImporteMensual(45579,'201206','',false);
//		debug($valores);


//
//
//		App::import('Model','Mutual.OrdenDescuentoCuota');
//		$oCUOTA = new OrdenDescuentoCuota();

//		App::import('Model','Mutual.OrdenDescuento');
//		$oORDEN = new OrdenDescuento();
//
//		$orden = $oORDEN->read(null,126969);
//
//
//
//
//		$orden['OrdenDescuentoCuota'] = $oCUOTA->armaCuotas($orden);
//
//		$oORDEN->saveAll($orden);
//		debug($orden);

//		App::import('Model','Pfyj.SocioCalificacion');
//		$oSC = new SocioCalificacion();
//
//		App::import('Model','Mutual.Liquidacion');
//		$oLQ = new Liquidacion();
//
//		$liquidacion = $oLQ->read(null,212);
//		$socio_id = 18376;
//		//saco la calificacion del periodo
//		$sql = "	select
//						Liquidacion.id,
//						LiquidacionSocioRendicion.socio_id,
//						LiquidacionSocioRendicion.banco_intercambio,
//						LiquidacionSocioRendicion.status,
//						BancoRendicionCodigo.calificacion_socio,
//						LiquidacionSocio.persona_beneficio_id,
//						Liquidacion.periodo
//					from liquidacion_socio_rendiciones as LiquidacionSocioRendicion
//					left join banco_rendicion_codigos as BancoRendicionCodigo on (LiquidacionSocioRendicion.banco_intercambio = BancoRendicionCodigo.banco_id and LiquidacionSocioRendicion.status = BancoRendicionCodigo.codigo)
//					inner join liquidacion_socios as LiquidacionSocio on (LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id)
//					inner join liquidaciones as Liquidacion on (LiquidacionSocio.liquidacion_id = Liquidacion.id)
//					where
//						LiquidacionSocioRendicion.socio_id = $socio_id
//						and Liquidacion.periodo = '".$liquidacion['Liquidacion']['periodo']."'
//						and IFNULL(LiquidacionSocioRendicion.status,'') <> ''
//					group by Liquidacion.id,LiquidacionSocioRendicion.socio_id,LiquidacionSocio.persona_beneficio_id,LiquidacionSocioRendicion.banco_intercambio,LiquidacionSocioRendicion.status
//					order by LiquidacionSocioRendicion.indica_pago ASC, LiquidacionSocioRendicion.indica_pago LIMIT 1";
//		$datos = $oSC->query($sql);
////		debug($datos);
//
//		$periodo = $datos[0]['Liquidacion']['periodo'];
//		$persona_beneficio_id = $datos[0]['LiquidacionSocio']['persona_beneficio_id'];
//		$calificacion = $datos[0]['BancoRendicionCodigo']['calificacion_socio'];
//
//		debug($periodo." | ".$persona_beneficio_id." | ".$calificacion);
//
////		$oSC->deleteAll("SocioCalificacion.socio_id = $socio_id and SocioCalificacion.persona_beneficio_id = $persona_beneficio_id and SocioCalificacion.periodo = '$periodo'");
////		$oSC->calificar(18376,$calificacion,$persona_beneficio_id,$periodo,"2012-06-25");
//


//		App::import('Model','Mutual.LiquidacionCuota');
//		$oLC = new LiquidacionCuota();
//
//		$oLC->__armaResumenCBU(224,6556,"201209","MUTUCORG2201");


// 		App::import('Model','Mutual.OrdenDescuento');
// 		$oORDEN = new OrdenDescuento();

// 		$orden = $oORDEN->calculaReprogramacion(134614,"2012-12-01");
// 		debug($orden['OrdenDescuentoCuota']);


		exit;


	}


	function gendisk(){

		$oLS = ClassRegistry::init('Mutual.LiquidacionSocio');


//		$oLS->reprocesarDisketteCBU(182,'MUTUEMPRE010','MUTUEMPRE010',null,0, '00011', '20120106', 2);

		$resultados = $oLS->generarDisketteCBUFromAsincronoProcess(49);

		debug($resultados);

		exit;

	}


	function test_xls(){
		$this->render(null,'blank');
	}    

}

?>
<?php
/**
 * 
 * @author adrian
 *	
 *	/usr/bin/php5 /home/adrian/trabajo/www/margen/cake/console/cake.php ventas_listado_solicitudes 16796 -app /home/adrian/trabajo/www/margen/app/
 *  /usr/bin/php5 /home/adrian/Trabajo/www/sigem/cake/console/cake.php ventas_listado_solicitudes 36103 -app /home/adrian/Trabajo/www/sigem/app/
 *
 */

Configure::write('debug',1);

class VentasListadoSolicitudesShell extends Shell{
	
	var $tasks = array('Temporal');
	
	function main(){
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
//		$this->out($pid);
                
                
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid;	

                $asinc->actualizar(5,100,"ESPERE, INICIANDO PROCESO...");
                
                $this->Temporal->limpiarTabla($pid);
                
                
		$vendedorID         = $asinc->getParametro('p1');
		$estado             = $asinc->getParametro('p2');
		$fDesde             = $asinc->getParametro('p3');
		$fHasta             = $asinc->getParametro('p4');
		$tipoOrden          = $asinc->getParametro('p5');
                $proveedorId        = $asinc->getParametro('p6');
                $periodo_corte      = $asinc->getParametro('p7');
                $vendedorIDLoggedIn  = $asinc->getParametro('p8');

                
//                $tipoOrden = (!empty($tipoOrden) ? $tipoOrden : 'EXPTE');

		App::import('model','mutual.MutualProductoSolicitud');
		$oSOLICITUD = new MutualProductoSolicitud();
        
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
        
                App::import('model','Mutual.Liquidacion');
                $oLiq = new Liquidacion();
                
                $FILE_EXCEL = "REPORTE_VENDEDORES_".$periodo_corte."_".date('Ymd-His').".xls";
                $this->Temporal->setXLSObject(3);

                $oXLS = $this->Temporal->getXLSObject();                
                
                $set = array();
                $set['sheet_title'] = 'SOLICITUDES';
                $set['labels'] = array(
//                    'A1' => 'INFORME:',
//                    'B1' => 'Detalle de Ordenes finalizadas'
                );
                $set['columns'] = array(
                    'texto_16' => 'USUARIO',
                    'texto_17' => 'FECHA_EMISION',
                    'texto_1' => 'VENDEDOR',
                    'texto_2' => 'PLAN',
                    'texto_3' => 'SOLICITUD',
                    'texto_4' => 'FECHA',
                    'texto_5' => 'ESTADO',
                    'texto_6' => 'REASIGNADA',
                    'texto_7' => 'SOLICITANTE',
                    'decimal_1' => 'CAPITAL',
                    'decimal_2' => 'SOLICITADO',
                    'entero_1' => 'CUOTAS',
                    'decimal_3' => 'IMPORTE_CUOTA',
                    'decimal_4' => 'TOTAL',
                    'texto_8' => 'ORGANISMO',
                    'texto_9' => 'EMPRESA_TURNO',
                    'texto_10' => 'BENEFICIO',
                    'texto_11' => 'ORDEN DTO',
                    'entero_4' => 'NRO ORDEN DTO',
                    'texto_12' => 'FECHA_ORDEN',
                    'texto_13' => 'PROVEEDOR_PRODUCTO',
                    'texto_14' => 'INICIA_EN',
                    'texto_15' => 'APROBADA_POR',
                    'decimal_9' => 'COBRADO_OPTIMO',        
                    'decimal_8' => 'COBRADO_REAL',        
                    'decimal_5' => 'SALDO',
                    'decimal_6' => 'PENDIENTE_ACREDITAR',
                    'decimal_7' => 'SALDO_NETO',
                    'decimal_15' => 'SALDO_AVENCER',
                    'entero_3' => 'CUOTAS_ADEUDADAS',
                    'entero_6' => 'CUOTAS_AVENCER',
                    'entero_5' => 'PERIODO_CORTE',
                    'decimal_10' => 'VENC_0_3_MESES', 
                    'decimal_11' => 'VENC_3_6_MESES', 
                    'decimal_12' => 'VENC_6_9_MESES', 
                    'decimal_13' => 'VENC_9_12_MESES', 
                    'decimal_14' => 'VENC_+12_MESES', 
                    'decimal_16' => 'AVENC_3_MESES', 
                    'decimal_17' => 'AVENC_6_MESES', 
                    'decimal_19' => 'AVENC_12_MESES', 
                    'decimal_20' => 'AVENC_+12_MESES', 
                );		        
                $this->Temporal->prepareXLSSheet(0,$set);                 
		
        
                #########################################################################################
                # OJOTA: VER TEMA DE NOVACION DE ORDEN
                #########################################################################################
        
		$sql = "SELECT MutualProductoSolicitud.*,PersonaVendedor.apellido,PersonaVendedor.nombre 
                        FROM mutual_producto_solicitudes AS MutualProductoSolicitud
				INNER JOIN personas AS Persona ON (Persona.id = MutualProductoSolicitud.persona_id)
				LEFT JOIN vendedores AS Vendedor ON (Vendedor.id = MutualProductoSolicitud.vendedor_id)
				LEFT JOIN personas AS PersonaVendedor ON (PersonaVendedor.id = Vendedor.persona_id)
				WHERE
					".(!empty($tipoOrden) ? "  MutualProductoSolicitud.tipo_orden_dto = '$tipoOrden' " : " 1 = 1")."
					".(!empty($vendedorID) ? "AND MutualProductoSolicitud.vendedor_id =  $vendedorID" : " ")."
                                        ".(!empty($vendedorIDLoggedIn) ? " AND MutualProductoSolicitud.vendedor_id IN ($vendedorIDLoggedIn )" : " ")."    
					".(!empty($estado) ? " AND MutualProductoSolicitud.estado = '$estado'" : "" )."
					".(!empty($fDesde) ? " AND MutualProductoSolicitud.fecha >= '$fDesde'" : " " )."
					".(!empty($fHasta) ? " AND MutualProductoSolicitud.fecha <= '$fHasta'" : " " )."
                    ".(!empty($proveedorId) ? " AND (MutualProductoSolicitud.proveedor_id = $proveedorId OR IFNULL(MutualProductoSolicitud.reasignar_proveedor_id,0) = $proveedorId)" : " " )."    
				ORDER BY 
					PersonaVendedor.apellido,PersonaVendedor.nombre,		
					Persona.apellido,Persona.nombre,MutualProductoSolicitud.fecha,MutualProductoSolicitud.id;";
		
// 		debug($sql);
// 		exit;
		
                
                
                
		$solicitudes = $oSOLICITUD->query($sql);		
//		$solicitudes = NULL;
                
		if(!empty($solicitudes)):
		
			$total = count($solicitudes);
			$asinc->setTotal($total);
			$i = 0;		
		
			foreach($solicitudes as $solicitud):

                            $proveedorReasigna = $solicitud['MutualProductoSolicitud']['reasignar_proveedor_id'];
                            $proveedorOriginal = $solicitud['MutualProductoSolicitud']['proveedor_id'];
                            
//                            debug($solicitud);
//                            $vendedorNombre = NULL;
//                            $vendedorNombre = $solicitud['PersonaVendedor']['apellido'].", ".$solicitud['PersonaVendedor']['nombre'];

                            
//                            $this->out($vendedorNombre);
                            
                            $solicitud = $oSOLICITUD->armaDatos($solicitud);
                            
//                            debug($solicitud);
                            
                            $saldo = $cuotas = $pendiente = $saldoAjustado = $devengado = $cobrado = $cobrado_optimo = $cuotasNoVencidas = 0 ;
                            $saldo_0003 = $saldo_0306 = $saldo_0609 = $saldo_0912 = $saldo_1213 = 0;
                            $saldo_avencer = $saldo_av3 = $saldo_av6 = $saldo_av9 = $saldo_av12  = $saldo_av13 = 0;
                            $cobradoTermino = $cobradoNoTermino = 0;
//                            $periodoControl = date('Ym');
                            if(!empty($solicitud['MutualProductoSolicitud']['orden_descuento_id'])){
//                                $ultimoPeriodoLiquidado = $oLiq->getUltimoPeriodoLiquidado($solicitud['MutualProductoSolicitud']['organismo']);
        //                        $imputado = $oLiq->isImputada($solicitud['MutualProductoSolicitud']['organismo'], $ultimoPeriodoImputado);
//                                $orden = $oCuota->getMoraByOrdenDtoHastaPeriodo($solicitud['MutualProductoSolicitud']['orden_descuento_id'],$periodo_corte);
                                $orden = $oCuota->get_mora_by_orden_dto($solicitud['MutualProductoSolicitud']['orden_descuento_id'],$periodo_corte);
                                if(!empty($orden)){
                                    $cuotas = $orden['cuotas_vencidas'];
                                    $cuotasNoVencidas = $orden['cuotas_avencer'];
                                    $pendiente = $orden['pendiente_acreditar'];
                                    $saldo = $orden['saldo'];
                                    $saldoAjustado = $saldo - $pendiente;
                                    $cobrado = $orden['cobrado'];
                                    $cobrado_optimo = $orden['cobrado_optimo'];
                                    $saldo_0003 = $orden['saldo_0003'];
                                    $saldo_0306 = $orden['saldo_0306'];
                                    $saldo_0609 = $orden['saldo_0609'];
                                    $saldo_0912 = $orden['saldo_0912'];
                                    $saldo_1213 = $orden['saldo_1213'];
                                    
                                    $saldo_avencer = $orden['saldo_avencer'];
                                    $saldo_av3 = $orden['saldoav_03'];
                                    $saldo_av6 = $orden['saldoav_06'];
                                    $saldo_av9 = $orden['saldoav_09'];
                                    $saldo_av12 = $orden['saldoav_12'];
                                    $saldo_av13 = $orden['saldoav_13'];   
                                    $cobradoTermino = $orden['cobrado_termino'];
                                    $cobradoNoTermino = $orden['cobrado_ntermino'];
                                    
                                }
                            }
                            $asinc->actualizar($i,$total,"$i / $total - PROCESANDO #" . $solicitud['MutualProductoSolicitud']['nro_print'] . " | " . $solicitud['MutualProductoSolicitud']['beneficiario']);
		
//                            $this->out("$i /$total - PROCESANDO #" . $solicitud['MutualProductoSolicitud']['nro_print'] . " | " . $solicitud['MutualProductoSolicitud']['beneficiario']);
                            
                            $impoTotal = $solicitud['MutualProductoSolicitud']['importe_cuota'] * $solicitud['MutualProductoSolicitud']['cuotas'];
                            
                            
                            $temp = array();
                            $temp['AsincronoTemporal'] = array();
                            $temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
                            $temp['AsincronoTemporal']['clave_1'] = (!empty($solicitud['MutualProductoSolicitud']['vendedor_id']) ? $solicitud['MutualProductoSolicitud']['vendedor_id'] : strtoupper(utf8_encode($solicitud['MutualProductoSolicitud']['user_created'])));
                            $temp['AsincronoTemporal']['clave_2'] = $solicitud['MutualProductoSolicitud']['proveedor_plan_id'];
                            $temp['AsincronoTemporal']['clave_3'] = 'REPORTE_1';
                            $temp['AsincronoTemporal']['texto_1'] = $solicitud['MutualProductoSolicitud']['vendedor_nombre'];
                            $temp['AsincronoTemporal']['texto_2'] = $solicitud['MutualProductoSolicitud']['proveedor_plan'];
                            $temp['AsincronoTemporal']['texto_3'] = $solicitud['MutualProductoSolicitud']['nro_print'];
                            $temp['AsincronoTemporal']['texto_4'] = date('d-m-Y', strtotime($solicitud['MutualProductoSolicitud']['fecha']));
                            $temp['AsincronoTemporal']['texto_5'] = $solicitud['MutualProductoSolicitud']['estado_desc'];
                            $temp['AsincronoTemporal']['texto_6'] = $solicitud['MutualProductoSolicitud']['proveedor_reasignada_a'];
                            $temp['AsincronoTemporal']['texto_7'] = $solicitud['MutualProductoSolicitud']['beneficiario'];
                            $temp['AsincronoTemporal']['texto_8'] = $solicitud['MutualProductoSolicitud']['organismo_desc'];
                            $temp['AsincronoTemporal']['texto_9'] = $solicitud['MutualProductoSolicitud']['turno_desc'];
                            $temp['AsincronoTemporal']['texto_10'] = $solicitud['MutualProductoSolicitud']['beneficio_str'];
                            
//                            $temp['AsincronoTemporal']['texto_18'] = $solicitud['PersonaVendedor']['apellido']." ".$solicitud['PersonaVendedor']['nombre'];
                            $temp['AsincronoTemporal']['texto_11'] = "";
                            $temp['AsincronoTemporal']['texto_12'] = "";
                            $temp['AsincronoTemporal']['texto_13'] = "";
                            $temp['AsincronoTemporal']['texto_14'] = "";
                            $temp['AsincronoTemporal']['texto_15'] = "";
                            
                            if(isset($solicitud['MutualProductoSolicitud']['OrdenDescuento'])){
                                    $temp['AsincronoTemporal']['texto_11'] = $solicitud['MutualProductoSolicitud']['OrdenDescuento']['tipo_nro'];
                                    $temp['AsincronoTemporal']['texto_12'] = date('d-m-Y',strtotime($solicitud['MutualProductoSolicitud']['OrdenDescuento']['fecha']));
                                    $temp['AsincronoTemporal']['texto_13'] = $solicitud['MutualProductoSolicitud']['OrdenDescuento']['proveedor_producto'];
                                    $temp['AsincronoTemporal']['texto_14'] = $solicitud['MutualProductoSolicitud']['OrdenDescuento']['inicia_en'];
                                    $temp['AsincronoTemporal']['texto_15'] = $solicitud['MutualProductoSolicitud']['OrdenDescuento']['user_created'];
                            }
                            $temp['AsincronoTemporal']['texto_16'] = $solicitud['MutualProductoSolicitud']['user_created'];
                            $temp['AsincronoTemporal']['texto_17'] = $solicitud['MutualProductoSolicitud']['created'];
                            
                            $temp['AsincronoTemporal']['texto_18'] = strtoupper((!empty($solicitud['MutualProductoSolicitud']['vendedor_apenom']) ? utf8_encode($solicitud['MutualProductoSolicitud']['vendedor_apenom']) : utf8_encode($solicitud['MutualProductoSolicitud']['user_created'])));
                            $temp['AsincronoTemporal']['texto_19'] = $solicitud['MutualProductoSolicitud']['aprobada'];
                            $temp['AsincronoTemporal']['texto_20'] = $solicitud['MutualProductoSolicitud']['anulada'];
                            
                            $temp['AsincronoTemporal']['decimal_1'] = $solicitud['MutualProductoSolicitud']['importe_solicitado'];
                            $temp['AsincronoTemporal']['decimal_2'] = $solicitud['MutualProductoSolicitud']['importe_percibido'];
                            $temp['AsincronoTemporal']['entero_1'] = $solicitud['MutualProductoSolicitud']['cuotas'];
                            $temp['AsincronoTemporal']['decimal_3'] = $solicitud['MutualProductoSolicitud']['importe_cuota'];
                            $temp['AsincronoTemporal']['decimal_4'] = $solicitud['MutualProductoSolicitud']['importe_total'];
                            $temp['AsincronoTemporal']['entero_2'] = $i;
                            $temp['AsincronoTemporal']['decimal_5'] = round($saldo,2);
                            $temp['AsincronoTemporal']['decimal_6'] = round($pendiente,2);
                            $temp['AsincronoTemporal']['decimal_7'] = round($saldoAjustado,2);
                            $temp['AsincronoTemporal']['decimal_8'] = round($cobrado,2);
                            $temp['AsincronoTemporal']['decimal_9'] = round($cobrado_optimo,2);
                            $temp['AsincronoTemporal']['entero_3'] = $cuotas;
                            $temp['AsincronoTemporal']['entero_4'] = $solicitud['MutualProductoSolicitud']['orden_descuento_id'];
                            $temp['AsincronoTemporal']['entero_5'] = $periodo_corte;
                            $temp['AsincronoTemporal']['entero_6'] = $cuotasNoVencidas;
                            
                            $temp['AsincronoTemporal']['decimal_10'] = round($saldo_0003,2);
                            $temp['AsincronoTemporal']['decimal_11'] = round($saldo_0306,2);
                            $temp['AsincronoTemporal']['decimal_12'] = round($saldo_0609,2);
                            $temp['AsincronoTemporal']['decimal_13'] = round($saldo_0912,2);
                            $temp['AsincronoTemporal']['decimal_14'] = round($saldo_1213,2);
                            
                            $temp['AsincronoTemporal']['decimal_15'] = round($solicitud['MutualProductoSolicitud']['importe_total'] - $cobrado_optimo,2);
                            
                            $temp['AsincronoTemporal']['decimal_16'] = round($saldo_av3,2);
                            $temp['AsincronoTemporal']['decimal_17'] = round($saldo_av6,2);
                            $temp['AsincronoTemporal']['decimal_18'] = round($saldo_av9,2);
                            $temp['AsincronoTemporal']['decimal_19'] = round($saldo_av12,2);
                            $temp['AsincronoTemporal']['decimal_20'] = round($saldo_av13,2);
                            
//                            DEBUG($temp);
                            
                            
                            $this->Temporal->writeXLSRow(0,$temp['AsincronoTemporal']);
					
                            $this->Temporal->grabar($temp);
				
                            $i++;
			
			endforeach; //solicitudes
                        
			
						
		
		else:
		
			$asinc->actualizar(10,100,"NO EXISTEN SOLICITUDES PARA EL CRITERIO DE BUSQUEDA SELECCIONADO...");

		endif; //empty($solicitudes)
                
                $set = array();
                $set['sheet_title'] = 'ALTA DE SOCIOS';
                $set['labels'] = array(
//                    'A1' => 'INFORME:',
//                    'B1' => 'Detalle de Ordenes finalizadas'
                );
                $set['columns'] = array(
                        'clave_2' => 'USUARIO',
                        'texto_1' => 'VENDEDOR',
                        'texto_3' => 'SOCIO_SOLICITUD',
                        'texto_2' => 'TIPO',                          
                        'texto_4' => 'FECHA',
                        'texto_5' => 'APROBADA',
                        'texto_6' => 'DOCUMENTO',
                        'texto_7' => 'SOLICITANTE',
                );		        
                $this->Temporal->prepareXLSSheet(1,$set);                  

                
                #######################################################################
                # PROCESO ALTAS DE SOCIOS
                #######################################################################
                $asinc->actualizar(10,100,"ESPERE, PROCESANDO ALTAS DE SOCIOS...");
                $sql = "SELECT SocioSolicitud.*,PersonaVendedor.apellido,PersonaVendedor.nombre,
                        PersonaVendedor.cuit_cuil,Persona.documento,Persona.apellido,Persona.nombre,
                        Socio.id
                        FROM socio_solicitudes AS SocioSolicitud
                                INNER JOIN personas AS Persona ON (Persona.id = SocioSolicitud.persona_id)
                                LEFT JOIN vendedores AS Vendedor ON (Vendedor.id = SocioSolicitud.vendedor_id)
                                LEFT JOIN personas AS PersonaVendedor ON (PersonaVendedor.id = Vendedor.persona_id)
                                INNER JOIN socios AS Socio ON (Socio.socio_solicitud_id = SocioSolicitud.id)
                                WHERE
                                        1 = 1
                                        ".(!empty($vendedorID) ? "AND SocioSolicitud.vendedor_id = $vendedorID" : " ")."
                                        ".(!empty($vendedorIDLoggedIn) ? " AND SocioSolicitud.vendedor_id = $vendedorIDLoggedIn" : " ")."    
                                        ".(!empty($fDesde) ? " AND SocioSolicitud.fecha >= '$fDesde'" : " " )."
                                        ".(!empty($fHasta) ? " AND SocioSolicitud.fecha <= '$fHasta'" : " " )."
                                ORDER BY 
                                        PersonaVendedor.apellido,PersonaVendedor.nombre,		
                                        Persona.apellido,Persona.nombre,SocioSolicitud.fecha,SocioSolicitud.id;";
                $solicitudes = $oSOLICITUD->query($sql);

                if(!empty($solicitudes)){

                    $total = count($solicitudes);
                    $asinc->setTotal($total);
                    $i = 0;

                    foreach($solicitudes as $solicitud){

                        $asinc->actualizar($i,$total,"$i / $total - ALTAS DE SOCIO APROBADAS #" . $solicitud['SocioSolicitud']['id']);
                        
//                        $this->out("$i /$total - ALTAS DE SOCIO APROBADAS #" . $solicitud['SocioSolicitud']['id']);
                        
                        
                        $temp = array();
                        $temp['AsincronoTemporal'] = array();
                        $temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
                        $temp['AsincronoTemporal']['clave_1'] = $solicitud['SocioSolicitud']['vendedor_id'];
                        $temp['AsincronoTemporal']['clave_2'] = strtoupper($solicitud['SocioSolicitud']['user_created']);
                        $temp['AsincronoTemporal']['clave_3'] = 'REPORTE_2';
                        $temp['AsincronoTemporal']['texto_1'] = (!empty($solicitud['SocioSolicitud']['vendedor_id']) ? $solicitud['PersonaVendedor']['apellido'].", ".$solicitud['PersonaVendedor']['nombre'] : '');
                        $temp['AsincronoTemporal']['texto_2'] = ($solicitud['SocioSolicitud']['tipo_solicitud'] == 'A' ? 'ALTA' : ($solicitud['SocioSolicitud']['tipo_solicitud'] == 'B' ? 'BAJA' : ($solicitud['SocioSolicitud']['tipo_solicitud'] == 'R' ? 'REEMP.' : 'MODIF.')));
                        $temp['AsincronoTemporal']['texto_3'] = $solicitud['SocioSolicitud']['id'];
                        $temp['AsincronoTemporal']['texto_4'] = date('d-m-Y', strtotime($solicitud['SocioSolicitud']['fecha']));
                        $temp['AsincronoTemporal']['texto_5'] = ($solicitud['SocioSolicitud']['aprobada'] == 1 ? 'SI':'NO');
                        $temp['AsincronoTemporal']['texto_6'] = $solicitud['Persona']['documento'];
                        $temp['AsincronoTemporal']['texto_7'] = $solicitud['Persona']['apellido'].", ".$solicitud['Persona']['nombre'];
                        $temp['AsincronoTemporal']['texto_8'] = $solicitud['Socio']['id'];
                        $temp['AsincronoTemporal']['texto_9'] = strtoupper($solicitud['SocioSolicitud']['user_created']);
                        
                        $this->Temporal->writeXLSRow(1,$temp['AsincronoTemporal']);
                        
                        $this->Temporal->grabar($temp);
                        
                        $i++;
                    }                            

                }
                
                
                #####################################################################################
                # GENERO HORA RESUMEN Y REPORTE ESTADISTICO                
                #####################################################################################
                $asinc->actualizar(10,100,"ESPERE, GENERANDO RESUMEN POR ESTADO...");
                
                $set = array();
                $set['sheet_title'] = 'RESUMEN ESTADOS';
                $set['labels'] = array();
                $set['columns'] = array();


                $this->Temporal->prepareXLSSheet(2,$set);                 
                
                # 1) DETERMINO LAS SERIES EN BASE AL USUARIO
                
                $sql = "select ltrim(rtrim(texto_5)) as estado from asincrono_temporales
                        where asincrono_id = $asinc->id
                        and clave_3 = 'REPORTE_1'
                        group by texto_5;";
                $estados = $oSOLICITUD->query($sql);
//                debug($estados);
                
                $sql = "select ltrim(rtrim(clave_1)) as vendedor_id,ltrim(rtrim(ifnull(texto_18,''))) as vendedor from asincrono_temporales
                        where asincrono_id = $asinc->id
                        and clave_3 = 'REPORTE_1'
                        group by clave_1;";
                $vendedores = $oSOLICITUD->query($sql);  
//                debug($usuarios);
                
                
                $total = count($estados);
                $asinc->setTotal($total);
                $i = 0;                
                
                if(!empty($estados)){
                    
                    $row = 2;
                    $col = 1; 
                    $col_ini = $col - 1;
                    $labels = array();
                    
                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row - 1, "RESUMEN POR ESTADO DE SOLICITUD");

                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row, "VENDEDOR/USUARIO");

                    $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFont()->setBold(true);
                    $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->getStartColor()->setRGB('969696');
                    
                    
                    foreach ($estados as $estado){
                        
                        
                        
                        $row = 2;
                        $estado = $estado[0]['estado'];
                        
                        $asinc->actualizar($i,$total,"$i / $total - TOTALIZANDO POR ESTADO");
                        
                        $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row, utf8_encode($estado));

                        $coordinate = PHPExcel_Cell::stringFromColumnIndex($col).$row;

                        $oXLS->getActiveSheet()->getStyle($coordinate)->getFont()->setBold(true);
                        $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                        $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->getStartColor()->setRGB('969696');

                        array_push($labels, PHPExcel_Cell::stringFromColumnIndex($col)); 
                        
                        foreach($vendedores as $vendedor){
                            
                            $row++;
                            $userVendedor = $vendedor[0]['vendedor_id'];
                            $userName = $vendedor[0]['vendedor'];
                            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col_ini, $row, $userName);
                            $sql = "select count(*) as cantidad from asincrono_temporales t
                                    where t.asincrono_id = $asinc->id
                                    and t.clave_3 = 'REPORTE_1' 
                                    and ifnull(t.texto_5,'') = '$estado' and ifnull(t.clave_1,'') = '$userVendedor' ";
                            $cantidad = $oSOLICITUD->query($sql);
                            $cantidad = $cantidad[0][0]['cantidad'];
                            $cantidad = (!empty($cantidad) && $cantidad != 0 ? $cantidad : "");
                            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$cantidad);                             
                            
                        }
                        
                        $i++;
                        $col++;
                        
                    }
                    
                }
                
                $asinc->actualizar(10,100,"ESPERE, GENERANDO RESUMEN OPERATIVO...");
                
                $sql = "select ltrim(rtrim(clave_1)) as vendedor_id,ltrim(rtrim(texto_18)) as vendedor
                        ,count(*) as operaciones
                        ,sum(decimal_1) as capital
                        ,sum(decimal_2) as solicitado
                        ,sum(decimal_4) as impo_total
                        ,sum(decimal_8) as cobrado
                        ,sum(decimal_5) as saldo
                        ,sum(decimal_15) as saldo_avencer
                        ,sum(decimal_9) as cobrado_op
                        ,IFNULL(ROUND((sum(decimal_8) / sum(decimal_9)) * 100 ,2),0) as eficiencia_cob
                        ,sum(decimal_10) as saldo_0003
                        ,sum(decimal_11) as saldo_0306
                        ,sum(decimal_12) as saldo_0609
                        ,sum(decimal_13) as saldo_0912
                        ,sum(decimal_14) as saldo_1213
                        ,sum(decimal_16) as saldo_av3
                        ,sum(decimal_17) as saldo_av6
                        ,sum(decimal_18) as saldo_av9
                        ,sum(decimal_19) as saldo_av12
                        ,sum(decimal_20) as saldo_av13
                        from asincrono_temporales
                        where asincrono_id = $asinc->id
                        and clave_3 = 'REPORTE_1' and texto_19 = 1 and texto_20 = 0
                        group by clave_1 order by saldo desc";
                $vendedores = $oSOLICITUD->query($sql);    
                
                
                $set = array();
                $set['sheet_title'] = 'RESUMEN OPERATIVO';
                $set['labels'] = array();
                $set['columns'] = array();


                $this->Temporal->prepareXLSSheet(3,$set);

                $total = count($vendedores);
                $asinc->setTotal($total);
                $i = 0;                  
                
                if($vendedores){
                    
                    $oXLS->getActiveSheet()->setCellValue("A1","VENDEDOR/USUARIO");
                    $oXLS->getActiveSheet()->setCellValue("B1","OPERACIONES APROBADAS");
                    $oXLS->getActiveSheet()->setCellValue("C1","CAPITAL");
                    $oXLS->getActiveSheet()->setCellValue("D1","SOLICITADO");
                    $oXLS->getActiveSheet()->setCellValue("E1","DEVENGADO");
                    $oXLS->getActiveSheet()->setCellValue("F1","COBRADO OPTIMO");
                    $oXLS->getActiveSheet()->setCellValue("G1","COBRADO");
                    $oXLS->getActiveSheet()->setCellValue("H1","EFICIENCIA");
                    $oXLS->getActiveSheet()->setCellValue("I1","SALDO VENCIDO");
                    $oXLS->getActiveSheet()->setCellValue("J1","SALDO A VENCER");
//                    $oXLS->getActiveSheet()->setCellValue("J1","SALDO_6_9");
//                    $oXLS->getActiveSheet()->setCellValue("K1","SALDO_9_12");
//                    $oXLS->getActiveSheet()->setCellValue("L1","SALDO_+_12");

                    $oXLS->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
                    $oXLS->getActiveSheet()->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $oXLS->getActiveSheet()->getStyle("A1")->getFill()->getStartColor()->setRGB('969696');
                    $oXLS->getActiveSheet()->duplicateStyle( $oXLS->getActiveSheet()->getStyle("A1"), "B1:J1");                      
                    
                    $r = 1;
                    foreach($vendedores as $vendedor){
                        
                        $asinc->actualizar($i,$total,"$i / $total - TOTALIZANDO POR RESUMEN OPERATIVO");
                        
                        $r++;
                        $oXLS->getActiveSheet()->setCellValue("A$r",$vendedor[0]['vendedor']);
                        $oXLS->getActiveSheet()->setCellValue("B$r",$vendedor[0]['operaciones']);
                        $oXLS->getActiveSheet()->setCellValue("C$r",$vendedor[0]['capital']);
                        $oXLS->getActiveSheet()->setCellValue("D$r",$vendedor[0]['solicitado']);
                        $oXLS->getActiveSheet()->setCellValue("E$r",$vendedor[0]['impo_total']);
                        $oXLS->getActiveSheet()->setCellValue("F$r",$vendedor[0]['cobrado_op']);
                        $oXLS->getActiveSheet()->setCellValue("G$r",$vendedor[0]['cobrado']);
                        $oXLS->getActiveSheet()->setCellValue("H$r",$vendedor[0]['eficiencia_cob']);
                        $oXLS->getActiveSheet()->setCellValue("I$r",$vendedor[0]['saldo']);
                        $oXLS->getActiveSheet()->setCellValue("J$r",$vendedor[0]['saldo_avencer']);
//                        $oXLS->getActiveSheet()->setCellValue("J$r",$vendedor[0]['saldo_0609']);
//                        $oXLS->getActiveSheet()->setCellValue("K$r",$vendedor[0]['saldo_0912']);
//                        $oXLS->getActiveSheet()->setCellValue("L$r",$vendedor[0]['saldo_1213']);
                        
                        $i++;
                        
                    }
                    
                }
                
                
                $this->Temporal->saveToXLSFile($FILE_EXCEL);
                $asinc->setValue('p9',$FILE_EXCEL);
                
                $asinc->actualizar(99,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");
		
		
		
	}
}

?>
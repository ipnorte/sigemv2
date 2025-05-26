<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tmp_analisis_estadistico
 *
 * @author adrian
 * 
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php tmp_analisis_estadistico 1 -app /home/adrian/trabajo/www/sigem/app/
 * 
 */

App::import('model','mutual.OrdenDescuento');
App::import('model','mutual.OrdenDescuentoCuota');
App::import('model','mutual.OrdenDescuentoCobroCuota');
App::import('model','mutual.Liquidacion');
App::import('model','mutual.LiquidacionCuota');

App::import('Model','Shells.Asincrono');

class TmpAnalisisEstadisticoShell extends Shell{
    //put your code here
    
    var $tasks = array('Temporal');
    var $periodo_control = NULL;
    var $meses = NULL;
    
    public function main(){
        
        Configure::write('debug',1);
        
        if(empty($this->args[0])){
                $this->out("ERROR: PID NO ESPECIFICADO");
                return;
        }

        $pid = $this->args[0];
        
//        $pid = $this->Temporal->prepare_asincrono('tmp_analisis_estadistico');
//        echo $pid;
//        exit;

        $asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
        $asinc->id = $pid;         
        
        $this->periodo_control = $asinc->getParametro('p1');
        $this->meses = $asinc->getParametro('p2');
        
        $this->periodo_control = (empty($this->periodo_control) ? date('Ym') : $this->periodo_control);
        $this->meses = (empty($this->meses) ? 12 : $this->meses);        
        
        $this->Temporal->limpiarTabla($pid);
        $asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
        
        $periodoDesc = $asinc->periodo($this->periodo_control);
        
        
        $FILE_EXCEL = "REPORTE_ESTADISTICO_".$periodoDesc."_".date('Ymd-His').".xls";
        $this->Temporal->setXLSObject(8);
        
        $oXLS = $this->Temporal->getXLSObject();
        
        
        $set = array();
        $set['sheet_title'] = 'ORDENES_FINALIZADAS';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Detalle de Ordenes finalizadas'
        );
        $set['columns'] = array(
                            'texto_1' => 'TIPO_NRO_DOCUMENTO',
                            'texto_2' => 'APELLIDO_NOMBRE',
                            'texto_3' => 'ORGANISMO',
                            'texto_4' => 'EMPRESA',
                            'texto_5' => 'PROVEEDOR',
                            'texto_6' => 'PRODUCTO',
                            'entero_1' => 'ORDEN_DTO',
                            'decimal_1' => 'TOTAL_ORDEN',
                            'decimal_4' => 'TOTAL_BAJA',
                            'decimal_2' => 'PAGADO_TERMINO',
                            'decimal_3' => 'PAGADO_VENCIDO',
        );		        
        $this->Temporal->prepareXLSSheet(0,$set);        
        
        
        
        $oORDEN = new OrdenDescuento();
        $oCUOTA = new OrdenDescuentoCuota();
        $oCOBRO = new OrdenDescuentoCobroCuota();
        
        $ordenes  = $this->get_ordenes_pagadas_totalmente();
        
//        $filename = LOGS . 'REPORTE_'.date('Ymd') . '.csv';
//        $log = new File($filename, true);
        
        $total = count($ordenes);
        $asinc->setTotal($total);
        $i = 0;	        
        
        $STOP = 0;
        
//        $ordenes = array();
        
        foreach($ordenes as $orden){
            
//            debug($orden);
            
            $orden_id = $orden['cu']['orden_descuento_id'];
            
//            $this->out($orden_id);
            
            $TOTAL_CUOTA = $TOTAL_BAJA = $TOTAL_COBRADO_TERMINO = $TOTAL_COBRADO_VENCIDO = 0;
            
            $sql = "select cu.id,cu.periodo,cu.importe from orden_descuento_cuotas cu where orden_descuento_id = $orden_id";
            $cuotas = $oCUOTA->query($sql);
            
            
            
            
            
            $TOTAL_BAJA = $orden[0]['baja'];
            
            foreach($cuotas as $cuota){
                
                $cuota_id = $cuota['cu']['id'];
                $periodo = $cuota['cu']['periodo'];
                $importe = $cuota['cu']['importe'];
                
                $TOTAL_CUOTA += $importe;
                
                $sql = "select ifnull(sum(importe),0) as importe from orden_descuento_cobro_cuotas "
                        . "where orden_descuento_cuota_id = $cuota_id and periodo_cobro = '$periodo'";
                
                $cobro = $oCOBRO->query($sql);
                $impoCob1 = $cobro[0][0]['importe'];
                
                $TOTAL_COBRADO_TERMINO += $impoCob1;
                
                $sql = "select ifnull(sum(importe),0) as importe from orden_descuento_cobro_cuotas "
                        . "where orden_descuento_cuota_id = $cuota_id and periodo_cobro <> '$periodo'";
                
                $cobro2 = $oCOBRO->query($sql);
                $impoCob2 = $cobro2[0][0]['importe'];                
                
                $TOTAL_COBRADO_VENCIDO += $impoCob2;
                
            }
            
            $odto = $oORDEN->getOrden($orden_id);
//            debug($odto['OrdenDescuento']);
            
            $tDocNdoc = $odto['OrdenDescuento']['persona_tdocndoc']; 
            $apeNom = $odto['OrdenDescuento']['persona_apenom']; 
            $org = $odto['OrdenDescuento']['organismo'];
            $empresa = $odto['OrdenDescuento']['empresa'];
            $proveedor =  $odto['OrdenDescuento']['proveedor'];
            $producto =  $odto['OrdenDescuento']['producto_descripcion'];
            
//            $message = $tDocNdoc.";".$apeNom.";".$org.";".$empresa.";".$orden_id.";".$TOTAL_CUOTA.";".$TOTAL_COBRADO_TERMINO.";".$TOTAL_COBRADO_VENCIDO."\r\n";
//            
//            $log->append($message);
            
            $asinc->actualizar($i,$total,"$i / $total - ORDEN DE DESCUENTO FINALIZADA >> #" . $orden_id . ' - ' . $apeNom);
            
//            $this->out($tDocNdoc."\t".$apeNom."\t".$org."\t".$empresa."\t".$orden_id."\t".$TOTAL_CUOTA."\t".$TOTAL_COBRADO_TERMINO."\t".$TOTAL_COBRADO_VENCIDO);
            
            $this->out("$i / $total - ORDEN DE DESCUENTO FINALIZADA >> #" . $orden_id . ' - ' . $apeNom);
            
            $temp = array();
            $temp['AsincronoTemporal'] = array();
            $temp['AsincronoTemporal']['id'] = 0;
            $temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
            $temp['AsincronoTemporal']['clave_1'] = 'ORDENES_FINALIZADAS';
            $temp['AsincronoTemporal']['clave_2'] = $org;
            $temp['AsincronoTemporal']['clave_3'] = $proveedor;
            $temp['AsincronoTemporal']['texto_1'] = $tDocNdoc;
            $temp['AsincronoTemporal']['texto_2'] = $apeNom;
            $temp['AsincronoTemporal']['texto_3'] = $org;
            $temp['AsincronoTemporal']['texto_4'] = $empresa;
            $temp['AsincronoTemporal']['texto_5'] = $proveedor;
            $temp['AsincronoTemporal']['texto_6'] = $producto;
            $temp['AsincronoTemporal']['entero_1'] = $orden_id;
            $temp['AsincronoTemporal']['decimal_1'] = $TOTAL_CUOTA;
            $temp['AsincronoTemporal']['decimal_2'] = $TOTAL_COBRADO_TERMINO;
            $temp['AsincronoTemporal']['decimal_3'] = $TOTAL_COBRADO_VENCIDO;
            $temp['AsincronoTemporal']['decimal_4'] = $TOTAL_BAJA;
            
            $this->Temporal->writeXLSRow(0,$temp['AsincronoTemporal']);
            
//            
//            $this->out(implode(",", $temp['AsincronoTemporal']));
            
//            debug($temp);
            
//            $this->Temporal->grabar($temp);
            
            if(!$this->Temporal->grabar($temp)){
                    $STOP = 1;
                    break;
            }			

            if($asinc->detenido()){
                    $STOP = 1;
                    break;
            }            
            
            $i++;
            
        }
               
        
        ##########################################################################################
        # GENERAR GRAFICIOS ORDENES FINALIZADAS
        ##########################################################################################
        $asinc->actualizar(5,100,"ESPERE, GENERANDO GRAFICOS...");
        $set = array();
        $set['sheet_title'] = 'RESUMEN_FINALIZADAS';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Resumen de Ordenes finalizadas'
        );		        
        $this->Temporal->prepareXLSSheet(1,$set);        
        
        $sql = "select count(*) as cantidad
                ,
                ifnull((select count(*) from asincrono_temporales t1
                where t1.asincrono_id = t.asincrono_id
                and ifnull(t1.decimal_4,0) <> 0
                ),0) as bajas 
                ,
                ifnull((select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t.asincrono_id
                and ifnull(t2.decimal_2,0) + ifnull(t2.decimal_3,0) > 0
                and ifnull(t2.decimal_4,0) = 0),0) as pagadas

                ,ifnull((select count(*) from asincrono_temporales t3
                where t3.asincrono_id = t.asincrono_id
                and ifnull(t3.decimal_2,0) =  ifnull(t3.decimal_1,0)
                and ifnull(t3.decimal_4,0) = 0 
                and ifnull(t3.decimal_3,0) = 0),0) as pagadas_termino
                ,
                ifnull((select count(*) from asincrono_temporales t4
                where t4.asincrono_id = t.asincrono_id
                and ifnull(t4.decimal_2,0) + ifnull(t4.decimal_3,0) =  ifnull(t4.decimal_1,0)
                and ifnull(t4.decimal_4,0) = 0 and ifnull(t4.decimal_3,0) <> 0),0) as pagadas_mora
                from asincrono_temporales t
                where t.asincrono_id = ".$asinc->id." and t.clave_1 = 'ORDENES_FINALIZADAS' group by t.asincrono_id;";
        
        $datos = $oORDEN->query($sql);
        
        if(!empty($datos)){
            $oXLS->getActiveSheet()->setCellValue("A3","TOTAL");
            $oXLS->getActiveSheet()->setCellValue("B3","PAGADAS");
            $oXLS->getActiveSheet()->setCellValue("C3","BAJAS");
            $oXLS->getActiveSheet()->setCellValue("D3","PAGADAS A TERMINO");
            $oXLS->getActiveSheet()->setCellValue("E3","PAGADAS CON MORA");

            $oXLS->getActiveSheet()->setCellValue("A4",$datos[0][0]['cantidad']);
            $oXLS->getActiveSheet()->setCellValue("B4",$datos[0][0]['pagadas']);
            $oXLS->getActiveSheet()->setCellValue("C4",$datos[0][0]['bajas']);
            $oXLS->getActiveSheet()->setCellValue("D4",$datos[0][0]['pagadas_termino']);
            $oXLS->getActiveSheet()->setCellValue("E4",$datos[0][0]['pagadas_mora']);
            
            $oXLS->getActiveSheet()->getStyle("A3")->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle("A3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle("A3")->getFill()->getStartColor()->setRGB('969696');
            $oXLS->getActiveSheet()->duplicateStyle( $oXLS->getActiveSheet()->getStyle("A3"), "B3:E3");            
            
        }
        
        
        $graph = array(
            'sheet' => 1,
            'dataSeriesLabels' => array('String', 'RESUMEN_FINALIZADAS!$B$3:$C$3'),
            'xAxisTickValues' => array('String', 'RESUMEN_FINALIZADAS!$B$3:$C$3'),
            'dataSeriesValues' => array('Number', 'RESUMEN_FINALIZADAS!B$4:$C$4'),
            'title' => 'ORDENES FINALIZADAS',
            'size' => array('A5','H20')
        );
        $this->Temporal->createPieGraph($graph);
        $graph = array(
            'sheet' => 1,
            'dataSeriesLabels' => array('String', 'RESUMEN_FINALIZADAS!$D$3:$E$3'),
            'xAxisTickValues' => array('String', 'RESUMEN_FINALIZADAS!$D$3:$E$3'),
            'dataSeriesValues' => array('Number', 'RESUMEN_FINALIZADAS!$D$4:$E$4'),
            'title' => 'PAGADO A TERMINO / MORA',
            'size' => array('A22','H37')
        );
        $this->Temporal->createPieGraph($graph);        

        #TOTALES PAGADOS POR PROVEEDOR
        $sql = "select ltrim(rtrim(t.clave_3)) as proveedor,count(*) as cantidad
                ,
                ifnull((select count(*) from asincrono_temporales t1
                where t1.asincrono_id = t.asincrono_id
                and t1.clave_3 = t.clave_3
                and ifnull(t1.decimal_4,0) <> 0
                ),0) as bajas 
                ,
                ifnull((select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t.asincrono_id
                and t2.clave_3 = t.clave_3
                and ifnull(t2.decimal_2,0) + ifnull(t2.decimal_3,0) > 0
                and ifnull(t2.decimal_4,0) = 0),0) as pagadas

                ,ifnull((select count(*) from asincrono_temporales t3
                where t3.asincrono_id = t.asincrono_id
                and t3.clave_3 = t.clave_3
                and ifnull(t3.decimal_2,0) =  ifnull(t3.decimal_1,0)
                and ifnull(t3.decimal_4,0) = 0 
                and ifnull(t3.decimal_3,0) = 0),0) as pagadas_termino
                ,
                ifnull((select count(*) from asincrono_temporales t4
                where t4.asincrono_id = t.asincrono_id
                and t4.clave_3 = t.clave_3
                and ifnull(t4.decimal_2,0) + ifnull(t4.decimal_3,0) =  ifnull(t4.decimal_1,0)
                and ifnull(t4.decimal_4,0) = 0 and ifnull(t4.decimal_3,0) <> 0),0) as pagadas_mora
                from asincrono_temporales t
                where t.asincrono_id = ".$asinc->id." and t.clave_1 = 'ORDENES_FINALIZADAS' 
                group by t.clave_3;";
        $datos = $oORDEN->query($sql);
//        debug($datos);
        
        if(!empty($datos)){
            $oXLS->getActiveSheet()->setCellValue("I3","PROVEEDOR");
            $oXLS->getActiveSheet()->setCellValue("J3","TOTAL");
            $oXLS->getActiveSheet()->setCellValue("K3","BAJAS");
            $oXLS->getActiveSheet()->setCellValue("L3","PAGADAS");
            $oXLS->getActiveSheet()->setCellValue("M3","PAGADAS A TERMINO");
            $oXLS->getActiveSheet()->setCellValue("N3","PAGADAS CON MORA");
            
            $oXLS->getActiveSheet()->getStyle("I3")->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle("I3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle("I3")->getFill()->getStartColor()->setRGB('969696');
            $oXLS->getActiveSheet()->duplicateStyle( $oXLS->getActiveSheet()->getStyle("I3"), "J3:N3");            

            $r = 3;
            foreach($datos as $dato){
                $r++;
                $oXLS->getActiveSheet()->setCellValue("I$r",$dato[0]['proveedor']);
                $oXLS->getActiveSheet()->setCellValue("J$r",$dato[0]['cantidad']);
                $oXLS->getActiveSheet()->setCellValue("K$r",$dato[0]['bajas']);
                $oXLS->getActiveSheet()->setCellValue("L$r",$dato[0]['pagadas']);
                $oXLS->getActiveSheet()->setCellValue("M$r",$dato[0]['pagadas_termino']); 
                $oXLS->getActiveSheet()->setCellValue("N$r",$dato[0]['pagadas_mora']); 
                
            }
            

        }
        $n1 = $r+1;
        $n2 = $n1 + 25;
        $graph = array(
            'sheet' => 1,
            'dataSeriesLabels' => array('String', "RESUMEN_FINALIZADAS!\$I$4:\$I$$r"),
            'xAxisTickValues' => array('String', "RESUMEN_FINALIZADAS!\$I$4:\$I$$r"),
            'dataSeriesValues' => array('Number', "RESUMEN_FINALIZADAS!\$M$4:\$M$$r"),
            'title' => 'PAGADAS A TERMINO / PROVEEDOR',
            'size' => array("I$n1","O$n2")
        );
        $this->Temporal->createPieGraph($graph);        
        $n1+= 25;
        $n2+= 20;
        $graph = array(
            'sheet' => 1,
            'dataSeriesLabels' => array('String', "RESUMEN_FINALIZADAS!\$I$4:\$I$$r"),
            'xAxisTickValues' => array('String', "RESUMEN_FINALIZADAS!\$I$4:\$I$$r"),
            'dataSeriesValues' => array('Number', "RESUMEN_FINALIZADAS!\$N$4:\$N$$r"),
            'title' => 'PAGADAS CON MORA / PROVEEDOR',
            'size' => array("I$n1","O$n2")
        );
        $this->Temporal->createPieGraph($graph); 


        #TOTALES PAGADOS POR ORGANISMO
        $sql = "select ltrim(rtrim(t.clave_2)) as organismo,count(*) as cantidad
                ,
                ifnull((select count(*) from asincrono_temporales t1
                where t1.asincrono_id = t.asincrono_id
                and t1.clave_2 = t.clave_2
                and ifnull(t1.decimal_4,0) <> 0
                ),0) as bajas 
                ,
                ifnull((select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t.asincrono_id
                and t2.clave_2 = t.clave_2
                and ifnull(t2.decimal_2,0) + ifnull(t2.decimal_3,0) > 0
                and ifnull(t2.decimal_4,0) = 0),0) as pagadas

                ,ifnull((select count(*) from asincrono_temporales t3
                where t3.asincrono_id = t.asincrono_id
                and t3.clave_2 = t.clave_2
                and ifnull(t3.decimal_2,0) =  ifnull(t3.decimal_1,0)
                and ifnull(t3.decimal_4,0) = 0 
                and ifnull(t3.decimal_3,0) = 0),0) as pagadas_termino
                ,
                ifnull((select count(*) from asincrono_temporales t4
                where t4.asincrono_id = t.asincrono_id
                and t4.clave_2 = t.clave_2
                and ifnull(t4.decimal_2,0) + ifnull(t4.decimal_3,0) =  ifnull(t4.decimal_1,0)
                and ifnull(t4.decimal_4,0) = 0 and ifnull(t4.decimal_3,0) <> 0),0) as pagadas_mora
                from asincrono_temporales t
                where t.asincrono_id = ".$asinc->id." and t.clave_1 = 'ORDENES_FINALIZADAS' 
                group by t.clave_2;";
        $datos = $oORDEN->query($sql);
//        debug($datos);
        $r = 3;
        if(!empty($datos)){
            $oXLS->getActiveSheet()->setCellValue("Q3","ORGANISMO");
            $oXLS->getActiveSheet()->setCellValue("R3","TOTAL");
            $oXLS->getActiveSheet()->setCellValue("S3","BAJAS");
            $oXLS->getActiveSheet()->setCellValue("T3","PAGADAS");
            $oXLS->getActiveSheet()->setCellValue("U3","PAGADAS A TERMINO");
            $oXLS->getActiveSheet()->setCellValue("V3","PAGADAS CON MORA");
            
            $oXLS->getActiveSheet()->getStyle("Q3")->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle("Q3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle("Q3")->getFill()->getStartColor()->setRGB('969696');
            $oXLS->getActiveSheet()->duplicateStyle( $oXLS->getActiveSheet()->getStyle("Q3"), "R3:V3");              

            
            foreach($datos as $dato){
                $r++;
                $oXLS->getActiveSheet()->setCellValue("Q$r",$dato[0]['organismo']);
                $oXLS->getActiveSheet()->setCellValue("R$r",$dato[0]['cantidad']);
                $oXLS->getActiveSheet()->setCellValue("S$r",$dato[0]['bajas']);
                $oXLS->getActiveSheet()->setCellValue("T$r",$dato[0]['pagadas']);
                $oXLS->getActiveSheet()->setCellValue("U$r",$dato[0]['pagadas_termino']); 
                $oXLS->getActiveSheet()->setCellValue("V$r",$dato[0]['pagadas_mora']); 
                
            }
            

        }
        $n1 = $r+1;
        $n2 = 30;
        $graph = array(
            'sheet' => 1,
            'dataSeriesLabels' => array('String', "RESUMEN_FINALIZADAS!\$Q$4:\$Q$$r", NULL, 1),
            'xAxisTickValues' => array('String', "RESUMEN_FINALIZADAS!\$Q$4:\$Q$$r", NULL, 1),
            'dataSeriesValues' => array('Number', "RESUMEN_FINALIZADAS!\$U$4:\$U$$r", NULL, $r),
            'title' => 'PAGADAS A TERMINO / ORGANISMO',
            'size' => array("Q$n1","W$n2")
        );
        $this->Temporal->createPieGraph($graph);        
        $n1+= 20;
        $n2+= 20;
        $graph = array(
            'sheet' => 1,
            'dataSeriesLabels' => array('String', "RESUMEN_FINALIZADAS!\$Q$4:\$Q$$r", NULL, 1),
            'xAxisTickValues' => array('String', "RESUMEN_FINALIZADAS!\$Q$4:\$Q$$r", NULL, 1),
            'dataSeriesValues' => array('Number', "RESUMEN_FINALIZADAS!\$V$4:\$V$$r", NULL, $r),
            'title' => 'PAGADAS CON MORA / ORGANISMO',
            'size' => array("Q$n1","W$n2")
        );
        $this->Temporal->createPieGraph($graph);         
        
//        $this->finalize($asinc, $FILE_EXCEL, $i, $total);
//        return;          
        

        
        
//        exit;
        $set = array();
        $set['sheet_title'] = 'ORDENES_POR_FINALIZAR';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Detalle de Ordenes por Finalizar'
        );
        $set['columns'] = array(
                            'texto_1' => 'TIPO_NRO_DOCUMENTO',
                            'texto_2' => 'APELLIDO_NOMBRE',
                            'texto_3' => 'ORGANISMO',
                            'texto_4' => 'EMPRESA',
                            'texto_5' => 'PROVEEDOR',
                            'texto_6' => 'PRODUCTO',
                            'entero_1' => 'ORDEN_DTO',
                            'decimal_1' => 'TOTAL_ORDEN',
                            'decimal_2' => 'SALDO_VENCIDO',
                            'decimal_3' => 'SALDO_AVENCER',
                            'entero_2' => 'CUOTAS',
        );
        
        
        $this->Temporal->prepareXLSSheet(2,$set);         
        
        $asinc->actualizar(1,100,"ESPERE, ANALIZANDO ORDENES POR TERMINAR...");
        
        $ordenes  = $this->get_ordenes_por_finalizar();
        
//        $filename = LOGS . 'REPORTE_'.date('Ymd') . '.csv';
//        $log = new File($filename, true);
        
        $total = count($ordenes);
        $asinc->setTotal($total);
        $i = 0;	        
        
        $STOP = 0;
//        $ordenes = array();
        
        foreach($ordenes as $orden){
            
//            debug($orden);
            
            $orden_id = $orden[0]['orden_descuento'];
            
            $odto = $oORDEN->getOrden($orden_id);
            
            $tDocNdoc = $odto['OrdenDescuento']['persona_tdocndoc']; 
            $apeNom = $odto['OrdenDescuento']['persona_apenom']; 
            $org = $odto['OrdenDescuento']['organismo'];
            $empresa = $odto['OrdenDescuento']['empresa'];
            $proveedor =  $odto['OrdenDescuento']['proveedor'];
            $producto =  $odto['OrdenDescuento']['producto_descripcion'];
            
            $SALDO_VENCIDO = $orden[0]['mora'];
            $SALDO_AVENCER = $orden[0]['saldo'];
            $cuotas = $orden[0]['cuotas'];
            
            $asinc->actualizar($i,$total,"$i / $total - ORDEN DE POR TERMINAR >> #" . $orden_id . ' - ' . $apeNom);
            
            $this->out("$i / $total - ORDEN DE POR TERMINAR >> #" . $orden_id . ' - ' . $apeNom);
            
            $temp = array();
            $temp['AsincronoTemporal'] = array();
            $temp['AsincronoTemporal']['id'] = 0;
            $temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
            $temp['AsincronoTemporal']['clave_1'] = 'ORDENES_POR_FINALIZAR';
            $temp['AsincronoTemporal']['clave_2'] = $proveedor;
            $temp['AsincronoTemporal']['clave_3'] = $org;
            $temp['AsincronoTemporal']['texto_1'] = $tDocNdoc;
            $temp['AsincronoTemporal']['texto_2'] = $apeNom;
            $temp['AsincronoTemporal']['texto_3'] = $org;
            $temp['AsincronoTemporal']['texto_4'] = $empresa;
            $temp['AsincronoTemporal']['texto_5'] = $proveedor;
            $temp['AsincronoTemporal']['texto_6'] = $producto;
            $temp['AsincronoTemporal']['entero_1'] = $orden_id;
            $temp['AsincronoTemporal']['entero_2'] = $cuotas;
//            $temp['AsincronoTemporal']['decimal_1'] = $TOTAL_CUOTA;
            $temp['AsincronoTemporal']['decimal_2'] = $SALDO_VENCIDO;
            $temp['AsincronoTemporal']['decimal_3'] = $SALDO_AVENCER;
            
            
            $this->Temporal->writeXLSRow(2,$temp['AsincronoTemporal']);
            
            if(!$this->Temporal->grabar($temp)){
                    $STOP = 1;
                    break;
            }			

            if($asinc->detenido()){
                    $STOP = 1;
                    break;
            }            
            
            $i++;
            
        }
        
        ################################################################################################
        # GENERO GRAFICOS ESTADISTICOS
        ################################################################################################
        $asinc->actualizar(5,100,"ESPERE, GENERANDO GRAFICOS...");
        $set = array();
        $set['sheet_title'] = 'RESUMEN_POR_FINALIZAR';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Resumen de Ordenes por Finalizar'
        );
        $this->Temporal->prepareXLSSheet(3,$set);         
        
        
        $sql = "select ltrim(rtrim(t.clave_2)) as proveedor from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'ORDENES_POR_FINALIZAR'
                group by t.clave_2;";
        $proveedores = $oORDEN->query($sql);
        
        $sql = "select ltrim(rtrim(t.entero_2)) as cuota from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'ORDENES_POR_FINALIZAR'
                group by t.entero_2;";
        $cuotas = $oORDEN->query($sql);  
        
        $row = 3;

        $col = 1;
        $col_ini = $col - 1; 
        
        $labels = array();

        if(!empty($proveedores)){
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row, "CUOTAS");

            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->getStartColor()->setRGB('969696');

            foreach($proveedores as $proveedor){
                
                //reset
                $row = 3;
                
                $clave_p = $proveedor[0]['proveedor'];
                $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row, utf8_encode($clave_p));
                
                $coordinate = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFont()->setBold(true);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->getStartColor()->setRGB('969696');
                
                array_push($labels, PHPExcel_Cell::stringFromColumnIndex($col));
                
                foreach($cuotas as $cuota){
                
                    $row++;
                    
                    $clave_c = $cuota[0]['cuota'];
                    
                    if($clave_c > 6 ){
                        break;
                    }
                    

                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col_ini, $row, $clave_c);
                    $sql = "select count(*) as cantidad from asincrono_temporales t
                            where t.asincrono_id = $asinc->id
                            and t.clave_1 = 'ORDENES_POR_FINALIZAR' 
                            and t.clave_2 = '$clave_p' and t.entero_2 = $clave_c ";
                    $cantidad = $oORDEN->query($sql);
                    $cantidad = $cantidad[0][0]['cantidad'];
                    $cantidad = (!empty($cantidad) && $cantidad != 0 ? $cantidad : "");
                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$cantidad);     
                    
//                    $this->out("$clave_p\t$clave_c\t$cantidad");
                    
                }
                
                $col++;
                
            }
            
        }
        
        $col -= 1;
        $graph = array();
        foreach($labels as $i => $coord){
            $graph['dataSeriesLabels'][$i] = array('String', 'RESUMEN_POR_FINALIZAR!$'.$coord."$3");
            $graph['dataSeriesValues'][$i] = array('Number', "RESUMEN_POR_FINALIZAR!\$$coord\$4:$coord\$9");
        }
        
        $graph['xAxisTickValues'] = array('String', 'RESUMEN_POR_FINALIZAR!$A$4:$A$9');
        $graph['title'] = 'PROVEEDOR / CUOTAS';
        $graph['xAxisLabel'] = 'Cuotas Restantes';
        $graph['yAxisLabel'] = 'Ordenes';
        $graph['size'] = array('A'.($row + 2),PHPExcel_Cell::stringFromColumnIndex($col+5)."40");
        $graph['sheet'] = 3;
        $this->Temporal->createBarGraph($graph);
        
        ##########################################################################################
        # TOTALES POR CUOTAS DISCRIMINADO VENCIDO A VENCER
        ##########################################################################################
        
        $sql = "select 
                ltrim(rtrim(t.entero_2)) AS cuota
                ,
                ifnull((select count(*) from asincrono_temporales t1
                where t1.asincrono_id = t.asincrono_id
                and t1.clave_1 = t.clave_1
                and t1.entero_2 = t.entero_2
                and t1.decimal_2 = 0),0) as al_dia
                ,ifnull((select count(*) from asincrono_temporales t1
                where t1.asincrono_id = t.asincrono_id
                and t1.clave_1 = t.clave_1
                and t1.entero_2 = t.entero_2
                and t1.decimal_2 > 0),0) as con_mora
                from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'ORDENES_POR_FINALIZAR'
                group by t.entero_2;";
        $datos = $oORDEN->query($sql);
        $row = 43;
        if(!empty($datos)){
            
            $oXLS->getActiveSheet()->setCellValue("A43","CUOTAS");
            $oXLS->getActiveSheet()->setCellValue("B43","CON MORA");
            $oXLS->getActiveSheet()->setCellValue("C43","AL DIA");
            
            $oXLS->getActiveSheet()->getStyle("A43")->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle("A43")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle("A43")->getFill()->getStartColor()->setRGB('969696');
            $oXLS->getActiveSheet()->duplicateStyle( $oXLS->getActiveSheet()->getStyle("A43"), "B43:C43");              

            
            
            foreach($datos as $dato){
                $row++;
                $oXLS->getActiveSheet()->setCellValue("A$row",$dato[0]['cuota']);
                $oXLS->getActiveSheet()->setCellValue("B$row",$dato[0]['con_mora']);
                $oXLS->getActiveSheet()->setCellValue("C$row",$dato[0]['al_dia']);
                
            }            
            
        }
        
        $graph['dataSeriesLabels'] = array(
            array('String', 'RESUMEN_POR_FINALIZAR!$B$43'),
            array('String', 'RESUMEN_POR_FINALIZAR!$C$43'),            
        );
        
        $graph['dataSeriesValues'] = array(
            array('Number', 'RESUMEN_POR_FINALIZAR!$B$44:B$49'),
            array('Number', 'RESUMEN_POR_FINALIZAR!$C$44:C$49'),
        );
        
        $graph['xAxisTickValues'] = array('String', 'RESUMEN_POR_FINALIZAR!$A$44:$A$49');
        $graph['title'] = 'ESTADO / CUOTAS';
        $graph['xAxisLabel'] = 'Cuotas Restantes';
        $graph['yAxisLabel'] = 'Ordenes';
        $graph['size'] = array('A'.($row + 2),"I80");
        $graph['sheet'] = 3;
        $this->Temporal->createBarGraph($graph,TRUE);        
        
        
        
        
//        $this->finalize($asinc, $FILE_EXCEL, $i, $total);
//        return;        
        
        
        $set = array();
        $set['sheet_title'] = 'ANALISIS PERIODOS';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Altas y Bajas por Periodo'
        );
        $set['columns'] = array(
                            'clave_1' => 'NOVEDAD',
                            'texto_7' => 'PERIODO',
                            'texto_1' => 'DOCUMENTO',
                            'texto_2' => 'NOMBRE',
                            'texto_3' => 'ORGANISMO',
                            'texto_4' => 'PROVEEDOR',
                            'texto_5' => 'PRODUCTO',
                            'texto_6' => 'CONCEPTO',
                            'entero_1' => 'ORDEN_DTO',
                            'decimal_1' => 'SALDO_ACTUAL',
                            'decimal_2' => 'IMPORTE_DEBITADO',
                            'decimal_3' => 'SALDO_PERIODO',
        );
        
        
        $this->Temporal->prepareXLSSheet(4,$set);         
        
        $asinc->actualizar(1,100,"ESPERE, ANALIZANDO PERIODOS...");        
        

        $periodos = $this->get_periodos();
        
        $total = count($periodos);
        $asinc->setTotal($total);
        $i = 0;	
        
        
        $oLC = new LiquidacionCuota();
        Configure::write('debug',1);
        
        foreach($periodos as $periodo){
            
            $periodo = $periodo['l']['periodo'];
            
//            $sql = "select 
//                    '1_ALTA_PERSONAS' as novedad
//                    ,l.periodo
//                    ,p.documento
//                    ,p.apellido
//                    ,p.nombre
//                    ,'' as organismo
//                    ,'' as razon_social
//                    ,'' as orden_descuento_id
//                    ,'' as tipo_producto
//                    ,'' as tipo_cuota
//                    ,sum(lc.saldo_actual) as saldo_actual
//                    ,sum(lc.importe_debitado) as importe_debitado
//                    ,sum(lc.saldo_actual) - sum(lc.importe_debitado) as saldo
//                    from liquidacion_cuotas lc
//                    inner join persona_beneficios b on b.id = lc.persona_beneficio_id
//                    inner join global_datos org on org.id = b.codigo_beneficio
//                    inner join liquidaciones l on l.id = lc.liquidacion_id
//                    inner join socios s on s.id = lc.socio_id
//                    inner join personas p on p.id = s.persona_id
//                    inner join global_datos tp on tp.id = lc.tipo_producto
//                    inner join global_datos tc on tc.id = lc.tipo_cuota
//                    inner join proveedores pr on pr.id = lc.proveedor_id
//                    where l.periodo = '$periodo'
//                    and lc.socio_id not in
//                    (select socio_id from liquidacion_cuotas lc2
//                    inner join liquidaciones l2 on l2.id = lc2.liquidacion_id 
//                    where l2.periodo < l.periodo)
//                    group by lc.socio_id
//                    union
//                    select
//                    '2_ALTA_ORDENES' as novedad
//                    ,l.periodo
//                    ,p.documento
//                    ,p.apellido
//                    ,p.nombre
//                    ,org.concepto_1 as organismo
//                    ,pr.razon_social
//                    ,lc.orden_descuento_id
//                    ,tp.concepto_1 as tipo_producto
//                    ,tc.concepto_1 as tipo_cuota
//                    ,sum(lc.saldo_actual) as saldo_actual
//                    ,sum(lc.importe_debitado) as importe_debitado
//                    ,sum(lc.saldo_actual) - sum(lc.importe_debitado) as saldo
//                    from liquidacion_cuotas lc
//                    inner join persona_beneficios b on b.id = lc.persona_beneficio_id
//                    inner join global_datos org on org.id = b.codigo_beneficio
//                    inner join liquidaciones l on l.id = lc.liquidacion_id
//                    inner join socios s on s.id = lc.socio_id
//                    inner join personas p on p.id = s.persona_id
//                    inner join global_datos tp on tp.id = lc.tipo_producto
//                    inner join global_datos tc on tc.id = lc.tipo_cuota
//                    inner join proveedores pr on pr.id = lc.proveedor_id
//                    where l.periodo = '$periodo'
//                    and lc.orden_descuento_id not in
//                    (select orden_descuento_id from liquidacion_cuotas lc2
//                    inner join liquidaciones l2 on l2.id = lc2.liquidacion_id 
//                    where l2.periodo < l.periodo)
//                    group by lc.orden_descuento_id,lc.tipo_producto,lc.tipo_cuota
//                    union
//                    select 
//                    '3_BAJA_PERSONAS' as novedad
//                    ,'$periodo'
//                    ,p.documento
//                    ,p.apellido
//                    ,p.nombre
//                    ,'' as organismo
//                    ,'' as razon_social
//                    ,'' as orden_descuento_id
//                    ,'' as tipo_producto
//                    ,'' as tipo_cuota
//                    ,sum(lc.saldo_actual) as saldo_actual
//                    ,sum(lc.importe_debitado) as importe_debitado
//                    ,sum(lc.saldo_actual) - sum(lc.importe_debitado) as saldo
//                    from liquidacion_cuotas lc
//                    inner join persona_beneficios b on b.id = lc.persona_beneficio_id
//                    inner join global_datos org on org.id = b.codigo_beneficio
//                    inner join liquidaciones l on l.id = lc.liquidacion_id
//                    inner join socios s on s.id = lc.socio_id
//                    inner join personas p on p.id = s.persona_id
//                    inner join global_datos tp on tp.id = lc.tipo_producto
//                    inner join global_datos tc on tc.id = lc.tipo_cuota
//                    inner join proveedores pr on pr.id = lc.proveedor_id
//                    where l.periodo = date_format(date_sub(STR_TO_DATE(concat('$periodo','01'),'%Y%m%d'), interval 1 month),'%Y%m')
//                    and lc.socio_id not in
//                    (select socio_id from liquidacion_cuotas lc2
//                    inner join liquidaciones l2 on l2.id = lc2.liquidacion_id 
//                    where l2.periodo = '$periodo')
//                    group by lc.socio_id 
//                    union
//                    select 
//                    '4_BAJA_ORDENES' as novedad
//                    ,'$periodo'
//                    ,p.documento
//                    ,p.apellido
//                    ,p.nombre
//                    ,org.concepto_1 as organismo
//                    ,pr.razon_social
//                    ,lc.orden_descuento_id
//                    ,tp.concepto_1 as tipo_producto
//                    ,tc.concepto_1 as tipo_cuota
//                    ,sum(lc.saldo_actual) as saldo_actual
//                    ,sum(lc.importe_debitado) as importe_debitado
//                    ,sum(lc.saldo_actual) - sum(lc.importe_debitado) as saldo
//                    from liquidacion_cuotas lc
//                    inner join persona_beneficios b on b.id = lc.persona_beneficio_id
//                    inner join global_datos org on org.id = b.codigo_beneficio
//                    inner join liquidaciones l on l.id = lc.liquidacion_id
//                    inner join socios s on s.id = lc.socio_id
//                    inner join personas p on p.id = s.persona_id
//                    inner join global_datos tp on tp.id = lc.tipo_producto
//                    inner join global_datos tc on tc.id = lc.tipo_cuota
//                    inner join proveedores pr on pr.id = lc.proveedor_id
//                    where l.periodo = date_format(date_sub(STR_TO_DATE(concat('$periodo','01'),'%Y%m%d'), interval 1 month),'%Y%m')
//                    and lc.socio_id not in
//                    (select socio_id from liquidacion_cuotas lc2
//                    inner join liquidaciones l2 on l2.id = lc2.liquidacion_id 
//                    where l2.periodo = '$periodo')
//                    group by lc.orden_descuento_id
//                    order by novedad,apellido,nombre";
//            
//            $datos = $oLC->query($sql);
//            $datos = null;
            
            $datos = $this->get_info_periodo($periodo);
//            debug($datos);
            if(!empty($datos)){
                
                $total = count($datos);
                $asinc->setTotal($total);
                $i = 0;	                
                
                foreach($datos as $dato){
                    
                    $asinc->actualizar($i,$total,"$i / $total - PERIODO >> #" . $dato[0]['periodo'] .  " | " . $dato[0]['novedad']." ** " . $dato[0]['apellido']." ".$dato[0]['nombre']);
                    
                    $this->out("$i / $total - PERIODO >> #" . $dato[0]['periodo'] .  " | " . $dato[0]['novedad']." ** " . $dato[0]['apellido']." ".$dato[0]['nombre']);
                    $clave_c_desc = $oORDEN->periodo($dato[0]['periodo']);
                    $temp = array();
                    $temp['AsincronoTemporal'] = array();                    
                    $temp['AsincronoTemporal'] = array(
                                        'id' => 0,
                                        'asincrono_id' => $asinc->id,
                                        'clave_1' => $dato[0]['novedad'],
                                        'clave_2' => $dato[0]['periodo'],
                                        'texto_1' => $dato[0]['documento'],
                                        'texto_2' => $dato[0]['apellido']." ".$dato[0]['nombre'],
                                        'texto_3' => $dato[0]['organismo'],
                                        'texto_4' => $dato[0]['razon_social'],
                                        'texto_5' => $dato[0]['tipo_producto'],
                                        'texto_6' => $dato[0]['tipo_cuota'],
                                        'texto_7' => $clave_c_desc,
                                        'entero_1' => $dato[0]['orden_descuento_id'],
                                        'decimal_1' => $dato[0]['saldo_actual'],
                                        'decimal_2' => $dato[0]['importe_debitado'],
                                        'decimal_3' => $dato[0]['saldo'],
                    );                    
                    
                    $this->Temporal->writeXLSRow(4,$temp['AsincronoTemporal']);

                    if(!$this->Temporal->grabar($temp)){
                            $STOP = 1;
                            break;
                    }                    
                    
                    
                    if($asinc->detenido()){
                            $STOP = 1;
                            break;
                    }            

//                    debug($temp['AsincronoTemporal']);                   
                    
                    $i++;
                }
            }
             
            
            
//            $this->out($periodo);
            
        }
        
        
        #GENERO HOJA CON DATOS ESTADISTICOS#
        
        #1 ALTA / BAJAS DE PERSONAS POR PERIODO
        
        $asinc->actualizar(1,100,"ESPERE, TOTALIZANDO PERSONAS/PERIODO...");        
         
        $sql = "select clave_2,
                ifnull((select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t1.asincrono_id
                and t2.clave_2 = t1.clave_2
                and t2.clave_1 = '1_ALTA_PERSONAS'),0) as altas
                ,
                ifnull((select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t1.asincrono_id
                and t2.clave_2 = t1.clave_2
                and t2.clave_1 = '3_BAJA_PERSONAS'),0) as bajas
                 from asincrono_temporales t1
                where t1.asincrono_id = ".$asinc->id." and clave_1 in ('1_ALTA_PERSONAS','3_BAJA_PERSONAS')
                group by t1.clave_2;";
        $datos = array();
        $datos = $oLC->query($sql);
        
//        debug($datos);
        
        $set = array();
        $set['sheet_title'] = 'RESUMEN_PERSONAS';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Altas y Bajas de Personas por Periodo'
        );
        $set['columns'] = array(
                            'texto_1' => 'PERIODO',
                            'entero_1' => 'ALTAS',
                            'entero_2' => 'BAJAS',
        );
        
        
        $this->Temporal->prepareXLSSheet(5,$set);        
        
        $total = count($datos);
        $asinc->setTotal($total);
        $i = 0;        
        
        
        if(!empty($datos)){
            $dato = array();

            foreach($datos as $dato){
                
                    $clave_c_desc = $oORDEN->periodo($dato['t1']['clave_2']);
                
                    $temp = array();
                    $temp['AsincronoTemporal'] = array();                    
                    $temp['AsincronoTemporal'] = array(
                                        'id' => 0,
                                        'asincrono_id' => $asinc->id,
                                        'clave_1' => 'RESUMEN_ALTAS_BAJA_PERS',
                                        'clave_2' => $dato['t1']['clave_2'],
                                        'texto_1' => $clave_c_desc,
                                        'entero_1' => $dato[0]['altas'],
                                        'entero_2' => $dato[0]['bajas'],
                    );     
                    
                    $asinc->actualizar($i,$total,"$i / $total - TOTALIZANDO PERSONAS / PERIODO >> #" . $dato['t1']['clave_2']);
                    
                    $this->out("$i / $total - TOTALIZANDO PERSONAS / PERIODO >> #" . $dato['t1']['clave_2']);
                    
                    $this->Temporal->writeXLSRow(5,$temp['AsincronoTemporal']);
                    if(!$this->Temporal->grabar($temp)){
                            $STOP = 1;
                            break;
                    }                    
                    
                    
                    if($asinc->detenido()){
                            $STOP = 1;
                            break;
                    }
                    
                    $i++;
                
            }
            
        }
        
        
        ############################################################################################
        # GENERO GRAFICO
        ############################################################################################
        $asinc->actualizar(5,100,"ESPERE, GENERANDO GRAFICOS...");
        
        $graph['dataSeriesLabels'] = array(
            array('String', 'RESUMEN_PERSONAS!$B$3'),
            array('String', 'RESUMEN_PERSONAS!$C$3'),            
        );
        
        $graph['dataSeriesValues'] = array(
            array('Number', 'RESUMEN_PERSONAS!$B$4:B$'. ($i + 3)),
            array('Number', 'RESUMEN_PERSONAS!$C$4:C$'. ($i + 3)),
        );
        
        $graph['xAxisTickValues'] = array('String', 'RESUMEN_PERSONAS!$A$4:$A$'. ($i + 3));
        $graph['title'] = 'EVOLUCION';
        $graph['xAxisLabel'] = 'Periodos';
        $graph['yAxisLabel'] = 'Cantidad';
        $graph['size'] = array('E3',"L20");
        $graph['sheet'] = 5;
        
//        debug($graph);
        
        $this->Temporal->createBarGraph($graph);        
        
        
        #1 ALTA / BAJAS DE ORDENES POR PERIODO
        
        $asinc->actualizar(1,100,"ESPERE, TOTALIZANDO ORDENES/PERIODO...");        
         
        $sql = "select clave_2,
                (select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t1.asincrono_id
                and t2.clave_2 = t1.clave_2
                and t2.clave_1 = '2_ALTA_ORDENES') as altas
                ,
                (select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t1.asincrono_id
                and t2.clave_2 = t1.clave_2
                and t2.clave_1 = '4_BAJA_ORDENES') as bajas
                 from asincrono_temporales t1
                where t1.asincrono_id = ".$asinc->id."
                and clave_1 in ('2_ALTA_ORDENES','4_BAJA_ORDENES')
                group by t1.clave_2;";
        $datos = array();
        $datos = $oLC->query($sql);
        
//        debug($datos);
        
        $set = array();
        $set['sheet_title'] = 'RESUMEN_ORDENES';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Altas y Bajas de Ordenes por Periodo'
        );
        $set['columns'] = array(
                            'texto_1' => 'PERIODO',
                            'entero_1' => 'ALTAS',
                            'entero_2' => 'BAJAS',
        );
        
        
        $this->Temporal->prepareXLSSheet(6,$set);        
        
        $total = count($datos);
        $asinc->setTotal($total);
        $i = 0;        
        
        
        if(!empty($datos)){
            $dato = array();
            foreach($datos as $dato){
                
                $clave_c_desc = $oORDEN->periodo($dato['t1']['clave_2']);
                
                    $temp = array();
                    $temp['AsincronoTemporal'] = array();                    
                    $temp['AsincronoTemporal'] = array(
                                        'id' => 0,
                                        'asincrono_id' => $asinc->id,
                                        'clave_1' => 'RESUMEN_ALTAS_BAJA_ORD',
                                        'clave_2' => $dato['t1']['clave_2'],
                                        'texto_1' => $clave_c_desc,
                                        'entero_1' => $dato[0]['altas'],
                                        'entero_2' => $dato[0]['bajas'],
                    );     
                    
                    $asinc->actualizar($i,$total,"$i / $total - TOTALIZANDO ORDENES / PERIODO >> #" . $dato['t1']['clave_2']);
                    
                    $this->out("$i / $total - TOTALIZANDO ORDENES / PERIODO >> #" . $dato['t1']['clave_2']);
                    
                    $this->Temporal->writeXLSRow(6,$temp['AsincronoTemporal']);
                    if(!$this->Temporal->grabar($temp)){
                            $STOP = 1;
                            break;
                    }                    
                    
                    
                    if($asinc->detenido()){
                            $STOP = 1;
                            break;
                    }
                    
                    $i++;
                
            }
            
        }        
        
        
        ############################################################################################
        # GENERO GRAFICO
        ############################################################################################
        $asinc->actualizar(5,100,"ESPERE, GENERANDO GRAFICOS...");
        
        $graph['dataSeriesLabels'] = array(
            array('String', 'RESUMEN_ORDENES!$B$3'),
            array('String', 'RESUMEN_ORDENES!$C$3'),            
        );
        
        $graph['dataSeriesValues'] = array(
            array('Number', 'RESUMEN_ORDENES!$B$4:B$'. ($i + 3)),
            array('Number', 'RESUMEN_ORDENES!$C$4:C$'. ($i + 3)),
        );
        
        $graph['xAxisTickValues'] = array('String', 'RESUMEN_ORDENES!$A$4:$A$'. ($i + 3));
        $graph['title'] = 'EVOLUCION';
        $graph['xAxisLabel'] = 'Periodos';
        $graph['yAxisLabel'] = 'Cantidad';
        $graph['size'] = array('E3',"L20");
        $graph['sheet'] = 6;
        
//        debug($graph);
        
        $this->Temporal->createBarGraph($graph,TRUE);        
        
        
        
        #1 ALTA / BAJAS DISCRIMINADAS DE ORDENES POR PERIODO
        
        $asinc->actualizar(1,100,"ESPERE, TOTALIZANDO ORDENES-DISCRIMINADAS/PERIODO...");        
         
        $sql = "select clave_2,texto_3,texto_4,texto_5,
                (select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t1.asincrono_id
                and t2.clave_2 = t1.clave_2
                and t2.clave_1 = '2_ALTA_ORDENES'
                and t2.texto_3 = t1.texto_3
                and t2.texto_4 = t1.texto_4
                and t2.texto_5 = t1.texto_5) as altas
                ,
                (select count(*) from asincrono_temporales t2
                where t2.asincrono_id = t1.asincrono_id
                and t2.clave_2 = t1.clave_2
                and t2.clave_1 = '4_BAJA_ORDENES'
                and t2.texto_3 = t1.texto_3
                and t2.texto_4 = t1.texto_4
                and t2.texto_5 = t1.texto_5) as bajas
                 from asincrono_temporales t1
                where t1.asincrono_id = ".$asinc->id."
                and clave_1 in ('2_ALTA_ORDENES','4_BAJA_ORDENES')
                group by clave_2,texto_3,texto_4,texto_5;";
        $datos = array();
        $datos = $oLC->query($sql);
        
//        debug($datos);
        
        $set = array();
        $set['sheet_title'] = 'PERIODO_ORDENES_DETALLE';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Altas y Bajas de Ordenes por Periodo'
        );
        $set['columns'] = array(
                            'texto_1' => 'PERIODO',
                            'texto_3' => 'ORGANISMO',
                            'texto_4' => 'PROVEEDOR',
                            'texto_5' => 'PRODUCTO',
                            'entero_1' => 'ALTAS',
                            'entero_2' => 'BAJAS',
        );
        
        
        $this->Temporal->prepareXLSSheet(7,$set);        
        
        $total = count($datos);
        $asinc->setTotal($total);
        $i = 0;        
        
        
        if(!empty($datos)){
            $dato = array();
            foreach($datos as $dato){
                
                $clave_c_desc = $oORDEN->periodo($dato['t1']['clave_2']);
                
                    $temp = array();
                    $temp['AsincronoTemporal'] = array();                    
                    $temp['AsincronoTemporal'] = array(
                                        'id' => 0,
                                        'asincrono_id' => $asinc->id,
                                        'clave_1' => 'RESUMEN_ALTAS_BAJA_ORD_DISC',
                                        'clave_2' => $dato['t1']['clave_2'],
                                        'texto_1' => $clave_c_desc,
                                        'texto_3' => $dato['t1']['texto_3'],
                                        'texto_4' => $dato['t1']['texto_4'],
                                        'texto_5' => $dato['t1']['texto_5'],
                                        'entero_1' => $dato[0]['altas'],
                                        'entero_2' => $dato[0]['bajas'],
                    );     
                    
                    $asinc->actualizar($i,$total,"$i / $total - TOTALIZANDO ORDENES-DISCR / PERIODO >> #" . $dato['t1']['clave_2']);
                    $this->out("$i / $total - TOTALIZANDO ORDENES-DISCR / PERIODO >> #" . $dato['t1']['clave_2']);
                    
                    $this->Temporal->writeXLSRow(7,$temp['AsincronoTemporal']);
                    if(!$this->Temporal->grabar($temp)){
                            $STOP = 1;
                            break;
                    }                    
                    
                    
                    if($asinc->detenido()){
                            $STOP = 1;
                            break;
                    }
                    
                    $i++;
                
            }
            
        } 
        
        
        ############################################################################################
        # GENERO GRAFICO
        ############################################################################################
        
        $set = array();
        $set['sheet_title'] = 'RESUMEN_ORDENES_DETALLE';
        $set['labels'] = array(
            'A1' => 'INFORME:',
            'B1' => 'Resumen de Altas y Bajas por Periodo / Proveedor / Producto'
        );
        $set['columns'] = array(
                            'clave_2' => 'PERIODO',
                            'entero_1' => 'ALTAS',
                            'entero_2' => 'BAJAS',
        );
        
        
        $this->Temporal->prepareXLSSheet(8,$set);        
        
        $asinc->actualizar(5,100,"ESPERE, GENERANDO GRAFICOS...");
        
        $sql = "select ltrim(rtrim(t.clave_2)) as periodo from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC'
                group by t.clave_2;";
        $periodos = $oORDEN->query($sql);
        
        $sql = "select ltrim(rtrim(t.texto_4)) as proveedor from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC'
                group by t.texto_4;";
        $proveedores = $oORDEN->query($sql);  
        
        $row = 3;

        $col = 1;
        $col_ini = $col - 1; 
        
        $labels = array();

        if(!empty($proveedores)){
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row - 1, "ALTAS POR PROVEEDOR");
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row, "PERIODO");

            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->getStartColor()->setRGB('969696');

            foreach($proveedores as $proveedor){
                
                //reset
                $row = 3;
                
                $clave_p = $proveedor[0]['proveedor'];
                $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row, utf8_encode($clave_p));
                
                $coordinate = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFont()->setBold(true);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->getStartColor()->setRGB('969696');
                
                array_push($labels, PHPExcel_Cell::stringFromColumnIndex($col));
                
                foreach($periodos as $periodo){
                
                    $row++;
                    
                    $clave_c = $periodo[0]['periodo'];
                    $clave_c_desc = $oORDEN->periodo($clave_c);

                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col_ini, $row, $clave_c_desc);
                    $sql = "select sum(entero_1) as altas from asincrono_temporales t
                            where t.asincrono_id = $asinc->id
                            and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC' 
                            and t.clave_2 = '$clave_c' and t.texto_4 = '$clave_p' ";
                    $cantidad = $oORDEN->query($sql);
                    $cantidad = $cantidad[0][0]['altas'];
                    $cantidad = (!empty($cantidad) && $cantidad != 0 ? $cantidad : "");
                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$cantidad);     
                    
//                    $this->out("$clave_p\t$clave_c\t$cantidad");
                    
                }
                
                $col++;
                
            }
            
        }
        
        $col -= 1;
        $graph = array();
        foreach($labels as $i => $coord){
            $graph['dataSeriesLabels'][$i] = array('String', 'RESUMEN_ORDENES_DETALLE!$'.$coord."$3");
            $graph['dataSeriesValues'][$i] = array('Number', "RESUMEN_ORDENES_DETALLE!\$$coord\$4:\$$coord\$" . $row);
        }
        
        $graph['xAxisTickValues'] = array('String', 'RESUMEN_ORDENES_DETALLE!$'.PHPExcel_Cell::stringFromColumnIndex($col_ini).'$4:$'.PHPExcel_Cell::stringFromColumnIndex($col_ini).'$' . $row);
        $graph['title'] = 'ALTAS POR PERIODO / PROVEEDOR';
        $graph['xAxisLabel'] = 'Periodos';
        $graph['yAxisLabel'] = 'Ordenes';
        $graph['size'] = array(PHPExcel_Cell::stringFromColumnIndex($col_ini).($row + 2),PHPExcel_Cell::stringFromColumnIndex($col + 5).($row + 34));
        $graph['sheet'] = 8;
        
//        debug($graph);
        
        $this->Temporal->createBarGraph($graph);        
        
        #grafico las bajas

        $sql = "select ltrim(rtrim(t.clave_2)) as periodo from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC'
                group by t.clave_2;";
        $periodos = $oORDEN->query($sql);
        
        $sql = "select ltrim(rtrim(t.texto_4)) as proveedor from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC'
                group by t.texto_4;";
        $proveedores = $oORDEN->query($sql);  
        
        $row = 3;

        $col += 7;
        $col_ini = $col - 1; 
        
        $labels = array();

        if(!empty($proveedores)){
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row - 1, "BAJAS POR PROVEEDOR");
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row, "PERIODO");

            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->getStartColor()->setRGB('969696');

            foreach($proveedores as $proveedor){
                
                //reset
                $row = 3;
                
                $clave_p = $proveedor[0]['proveedor'];
                $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row, utf8_encode($clave_p));
                
                $coordinate = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFont()->setBold(true);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->getStartColor()->setRGB('969696');
                
                array_push($labels, PHPExcel_Cell::stringFromColumnIndex($col));
                
                foreach($periodos as $periodo){
                
                    $row++;
                    
                    $clave_c = $periodo[0]['periodo'];
                    
                    $clave_c_desc = $oORDEN->periodo($clave_c);
                    

                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col_ini, $row, $clave_c_desc);
                    $sql = "select sum(entero_2) as bajas from asincrono_temporales t
                            where t.asincrono_id = $asinc->id
                            and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC' 
                            and t.clave_2 = '$clave_c' and t.texto_4 = '$clave_p' ";
                    $cantidad = $oORDEN->query($sql);
                    $cantidad = $cantidad[0][0]['bajas'];
                    $cantidad = (!empty($cantidad) && $cantidad != 0 ? $cantidad : "");
                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$cantidad);     
                    
//                    $this->out("$clave_p\t$clave_c\t$cantidad");
                    
                }
                
                $col++;
                
            }
            
        }
        
        $col -= 1;
        $graph = array();
        foreach($labels as $i => $coord){
            $graph['dataSeriesLabels'][$i] = array('String', 'RESUMEN_ORDENES_DETALLE!$'.$coord."$3");
            $graph['dataSeriesValues'][$i] = array('Number', "RESUMEN_ORDENES_DETALLE!\$$coord\$4:\$$coord\$" . $row);
        }
        
        $graph['xAxisTickValues'] = array('String', 'RESUMEN_ORDENES_DETALLE!$'.PHPExcel_Cell::stringFromColumnIndex($col_ini).'$4:$'.PHPExcel_Cell::stringFromColumnIndex($col_ini).'$' . $row);
        $graph['title'] = 'BAJAS POR PERIODO / PROVEEDOR';
        $graph['xAxisLabel'] = 'Periodos';
        $graph['yAxisLabel'] = 'Ordenes';
        $graph['size'] = array(PHPExcel_Cell::stringFromColumnIndex($col_ini).($row + 2),PHPExcel_Cell::stringFromColumnIndex($col + 5).($row + 34));
        $graph['sheet'] = 8;
        
//        debug($graph);
        
        $this->Temporal->createBarGraph($graph); 


        ################################################################################
        #resumen de altas/bajas por producto
        ################################################################################
        
        $sql = "select ltrim(rtrim(t.clave_2)) as periodo from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC'
                group by t.clave_2;";
        $periodos = $oORDEN->query($sql);
        
        $sql = "select ltrim(rtrim(t.texto_5)) as producto from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC'
                group by t.texto_5;";
        $productos = $oORDEN->query($sql);  
        
        $row_ini = ($row + 38);
        
        $row = $row_ini;

        $col = 1;
        $col_ini = $col - 1; 
        
        $labels = array();

        if(!empty($productos)){
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col_ini,$row - 1, "ALTAS POR PRODUCTO");
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col_ini,$row, "PERIODO");

            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->getStartColor()->setRGB('969696');

            foreach($productos as $producto){
                
                //reset
                $row = $row_ini;
                
                $clave_p = $producto[0]['producto'];
                $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row, utf8_encode($clave_p));
                
                $coordinate = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFont()->setBold(true);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->getStartColor()->setRGB('969696');
                
                array_push($labels, PHPExcel_Cell::stringFromColumnIndex($col));
                
                foreach($periodos as $periodo){
                
                    $row++;
                    
                    $clave_c = $periodo[0]['periodo'];
                    $clave_c_desc = $oORDEN->periodo($clave_c);

                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col_ini, $row, $clave_c_desc);
                    $sql = "select sum(entero_1) as altas from asincrono_temporales t
                            where t.asincrono_id = $asinc->id
                            and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC' 
                            and t.clave_2 = '$clave_c' and t.texto_5 = '$clave_p' ";
                    $cantidad = $oORDEN->query($sql);
                    $cantidad = $cantidad[0][0]['altas'];
                    $cantidad = (!empty($cantidad) && $cantidad != 0 ? $cantidad : "");
                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$cantidad);     
                    
//                    $this->out("$clave_p\t$clave_c\t$cantidad");
                    
                }
                
                $col++;
                
            }
            
        }
        
        $col -= 1;
        $graph = array();
        foreach($labels as $i => $coord){
            $graph['dataSeriesLabels'][$i] = array('String', 'RESUMEN_ORDENES_DETALLE!$'.$coord."$".$row_ini);
            $graph['dataSeriesValues'][$i] = array('Number', "RESUMEN_ORDENES_DETALLE!\$$coord\$".($row_ini + 1).":\$$coord\$" . $row);
        }
        $graph['xAxisTickValues'] = array('String', 'RESUMEN_ORDENES_DETALLE!$'.PHPExcel_Cell::stringFromColumnIndex($col_ini).'$'.($row_ini + 1).':$'.PHPExcel_Cell::stringFromColumnIndex($col_ini).'$' . $row);
        $graph['title'] = 'ALTAS POR PERIODO / PRODUCTO';
        $graph['xAxisLabel'] = 'Periodos';
        $graph['yAxisLabel'] = 'Ordenes';
        $graph['size'] = array(PHPExcel_Cell::stringFromColumnIndex($col_ini).($row + 2),PHPExcel_Cell::stringFromColumnIndex($col + 5)."40");
        $graph['sheet'] = 8;
        
//        debug($graph);
               
        $this->Temporal->createBarGraph($graph);        
        
        #grafico las bajas

        $sql = "select ltrim(rtrim(t.clave_2)) as periodo from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC'
                group by t.clave_2;";
        $periodos = $oORDEN->query($sql);
        
        $sql = "select ltrim(rtrim(t.texto_5)) as producto from asincrono_temporales t
                where t.asincrono_id = $asinc->id
                and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC'
                group by t.texto_5;";
        $productos = $oORDEN->query($sql);  
        
        $row = $row_ini;

        $col += 8;
        $col_ini = $col - 1; 
        
        $labels = array();

        if(!empty($productos)){
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row - 1, "BAJAS POR PRODUCTO");
            
            $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col-1,$row, "PERIODO");

            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFont()->setBold(true);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oXLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_ini).$row)->getFill()->getStartColor()->setRGB('969696');

            foreach($productos as $producto){
                
                //reset
                $row = $row_ini;
                
                $clave_p = $producto[0]['producto'];
                $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row, utf8_encode($clave_p));
                
                $coordinate = PHPExcel_Cell::stringFromColumnIndex($col).$row;
                
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFont()->setBold(true);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $oXLS->getActiveSheet()->getStyle($coordinate)->getFill()->getStartColor()->setRGB('969696');
                
                array_push($labels, PHPExcel_Cell::stringFromColumnIndex($col));
                
                foreach($periodos as $periodo){
                
                    $row++;
                    
                    $clave_c = $periodo[0]['periodo'];
                    
                    $clave_c_desc = $oORDEN->periodo($clave_c);
                    

                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col_ini, $row, $clave_c_desc);
                    $sql = "select sum(entero_2) as bajas from asincrono_temporales t
                            where t.asincrono_id = $asinc->id
                            and t.clave_1 = 'RESUMEN_ALTAS_BAJA_ORD_DISC' 
                            and t.clave_2 = '$clave_c' and t.texto_5 = '$clave_p' ";
                    $cantidad = $oORDEN->query($sql);
                    $cantidad = $cantidad[0][0]['bajas'];
                    $cantidad = (!empty($cantidad) && $cantidad != 0 ? $cantidad : "");
                    $oXLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$cantidad);     
                    
//                    $this->out("$clave_p\t$clave_c\t$cantidad");
                    
                }
                
                $col++;
                
            }
            
        }
        
        $col -= 1;
        $graph = array();
        foreach($labels as $i => $coord){
            $graph['dataSeriesLabels'][$i] = array('String', 'RESUMEN_ORDENES_DETALLE!$'.$coord."$".$row_ini);
            $graph['dataSeriesValues'][$i] = array('Number', "RESUMEN_ORDENES_DETALLE!\$$coord\$".($row_ini + 1).":\$$coord\$" . $row);
        }
        
        $graph['xAxisTickValues'] = array('String', 'RESUMEN_ORDENES_DETALLE!$'.PHPExcel_Cell::stringFromColumnIndex($col_ini).'$'.($row_ini + 1).':$'.PHPExcel_Cell::stringFromColumnIndex($col_ini).'$' . $row);
        $graph['title'] = 'BAJAS POR PERIODO / PRODUCTO';
        $graph['xAxisLabel'] = 'Periodos';
        $graph['yAxisLabel'] = 'Ordenes';
        $graph['size'] = array(PHPExcel_Cell::stringFromColumnIndex($col_ini).($row + 2),PHPExcel_Cell::stringFromColumnIndex($col + 5)."40");
        $graph['sheet'] = 8;
        
//        debug($graph);
        
        $this->Temporal->createBarGraph($graph); 
        
        
        
        
        
//        debug($altasBajas);
        
        $this->finalize($asinc, $FILE_EXCEL, $i, $total);
        


        
    }
    
    
    public function finalize($asinc,$FILE_EXCEL,$i,$total,$STOP=0){
        $this->Temporal->saveToXLSFile($FILE_EXCEL);
        $asinc->setValue('p6',$FILE_EXCEL);
        if($STOP == 0){
                $asinc->actualizar($i,$total,"FINALIZANDO...");
                $asinc->fin("**** PROCESO FINALIZADO ****");
        }        
    }

    



    public function get_ordenes_por_finalizar($limit = NULL){
        
        $oCUOTA = new OrdenDescuentoCuota();
        
        $sql = "select 
                o.tipo_orden_dto,
                o.numero,
                o.id as orden_descuento,
                count(c.id) as cuotas,
                ifnull(
                (
                select sum(importe) - ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                from orden_descuento_cuotas c1 where c1.orden_descuento_id = o.id
                AND c1.periodo <= date_format(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'),'%Y%m') and c1.estado <> 'B' 
                and c1.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                ),0)
                as mora,
                ifnull(
                (
                select sum(importe) - ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                from orden_descuento_cuotas c1 where c1.orden_descuento_id = o.id
                AND c1.periodo > date_format(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'),'%Y%m')  and c1.estado <> 'B' 
                and c1.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                ),0)
                as saldo,
                cb.concepto_1 as organismo,ba.nombre, so.activo,so.persona_id 
                ,Vendedor.cuit_cuil
                ,concat(Vendedor.apellido,', ',Vendedor.nombre) as vendedor_apenom  
                from orden_descuentos o
                inner join orden_descuento_cuotas c on (c.orden_descuento_id = o.id)
                inner join persona_beneficios b on (b.id = o.persona_beneficio_id)
                inner join global_datos cb on (cb.id = b.codigo_beneficio)
                inner join bancos ba on (ba.id = b.banco_id)
                inner join socios so on (so.id = c.socio_id)
                left join mutual_producto_solicitudes Solicitud on Solicitud.id = o.numero
                left join vendedores v on v.id = Solicitud.vendedor_id
                left join personas Vendedor on Vendedor.id = v.persona_id

                where  o.tipo_orden_dto = 'EXPTE' 
                and c.estado <> 'B' and c.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc
                where cc.orden_descuento_cuota_id = c.id),0)
                and so.activo = 1 
                group by o.numero,o.id,o.socio_id
                having count(c.id) = 1
                UNION
                select 
                o.tipo_orden_dto,
                o.numero,
                o.id as orden_descuento,
                count(c.id) as cuotas,
                ifnull(
                (
                select sum(importe) - ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                from orden_descuento_cuotas c1 where c1.orden_descuento_id = o.id
                AND c1.periodo <= date_format(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'),'%Y%m')  and c1.estado <> 'B' 
                and c1.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                ),0)
                as mora,
                ifnull(
                (
                select sum(importe) - ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                from orden_descuento_cuotas c1 where c1.orden_descuento_id = o.id
                AND c1.periodo > date_format(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'),'%Y%m')  and c1.estado <> 'B' 
                and c1.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                ),0)
                as saldo,
                cb.concepto_1 as organismo,ba.nombre, so.activo,so.persona_id 
                ,Vendedor.cuit_cuil
                ,concat(Vendedor.apellido,', ',Vendedor.nombre) as vendedor_apenom  
                from orden_descuentos o
                inner join orden_descuento_cuotas c on (c.orden_descuento_id = o.id)
                inner join persona_beneficios b on (b.id = o.persona_beneficio_id)
                inner join global_datos cb on (cb.id = b.codigo_beneficio)
                inner join bancos ba on (ba.id = b.banco_id)
                inner join socios so on (so.id = c.socio_id)
                left join mutual_producto_solicitudes Solicitud on Solicitud.id = o.numero
                left join vendedores v on v.id = Solicitud.vendedor_id
                left join personas Vendedor on Vendedor.id = v.persona_id

                where  o.tipo_orden_dto = 'EXPTE' 
                and c.estado <> 'B' and c.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc
                where cc.orden_descuento_cuota_id = c.id),0)
                and so.activo = 1 
                group by o.numero,o.id,o.socio_id
                having count(c.id) = 2
                UNION
                select 
                o.tipo_orden_dto,
                o.numero,
                o.id as orden_descuento,
                count(c.id) as cuotas,
                ifnull(
                (
                select sum(importe) - ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                from orden_descuento_cuotas c1 where c1.orden_descuento_id = o.id
                AND c1.periodo <= date_format(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'),'%Y%m')  and c1.estado <> 'B' 
                and c1.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                ),0)
                as mora,
                ifnull(
                (
                select sum(importe) - ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                from orden_descuento_cuotas c1 where c1.orden_descuento_id = o.id
                AND c1.periodo > date_format(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'),'%Y%m')  and c1.estado <> 'B' 
                and c1.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc1 
                where cc1.orden_descuento_cuota_id = c1.id  ),0)
                ),0)
                as saldo,
                cb.concepto_1 as organismo,ba.nombre, so.activo,so.persona_id 
                ,Vendedor.cuit_cuil
                ,concat(Vendedor.apellido,', ',Vendedor.nombre) as vendedor_apenom  
                from orden_descuentos o
                inner join orden_descuento_cuotas c on (c.orden_descuento_id = o.id)
                inner join persona_beneficios b on (b.id = o.persona_beneficio_id)
                inner join global_datos cb on (cb.id = b.codigo_beneficio)
                inner join bancos ba on (ba.id = b.banco_id)
                inner join socios so on (so.id = c.socio_id)
                left join mutual_producto_solicitudes Solicitud on Solicitud.id = o.numero
                left join vendedores v on v.id = Solicitud.vendedor_id
                left join personas Vendedor on Vendedor.id = v.persona_id
                where  o.tipo_orden_dto = 'EXPTE' 
                and c.estado <> 'B' and c.importe > ifnull((select sum(importe) from orden_descuento_cobro_cuotas cc
                where cc.orden_descuento_cuota_id = c.id),0)
                and so.activo = 1 
                group by o.numero,o.id,o.socio_id
                having count(c.id) > 2 and count(c.id) < 7 ".(!empty($limit) ? " limit $limit" : " ").";";
        $ordenes = $oCUOTA->query($sql);
        return $ordenes;        
        
    }

    

    public function get_ordenes_pagadas_totalmente($limit = NULL){
        
        $oCUOTA = new OrdenDescuentoCuota();
        
//        $sql = "select t1.* from
//                (select orden_descuento_id,sum(importe) as importe 
//                from orden_descuento_cuotas cu
//                inner join orden_descuentos o on o.id = cu.orden_descuento_id
//                where o.permanente = 0
//                group by orden_descuento_id) t1,
//                (select c.orden_descuento_id,sum(cc.importe) as importe 
//                from orden_descuento_cobro_cuotas cc
//                inner join orden_descuento_cuotas c on c.id = cc.orden_descuento_cuota_id
//                group by c.orden_descuento_id) t2
//                where t1.orden_descuento_id = t2.orden_descuento_id
//                and t1.importe = t2.importe";
        
//        $sql = "select 
//                cu.orden_descuento_id
//                ,
//                sum(cu.importe)
//                - 
//                ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
//                inner join orden_descuento_cobros co on co.id = cc.orden_descuento_cobro_id
//                inner join orden_descuento_cuotas c2 on c2.id = cc.orden_descuento_cuota_id
//                where 
//                c2.orden_descuento_id = cu.orden_descuento_id 
//                ),0) as saldo
//                from orden_descuento_cuotas cu
//                where 
//                cu.periodo >= date_format(date_sub(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'), interval ".$this->meses." month),'%Y%m')
//                and cu.estado not in ('B','C')
//                group by cu.orden_descuento_id
//                having saldo = 0;";
        
        
        $sql = "select 
                cu.orden_descuento_id
                ,sum(cu.importe) as devengado
                ,ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cobros co on co.id = cc.orden_descuento_cobro_id
                inner join orden_descuento_cuotas c2 on c2.id = cc.orden_descuento_cuota_id
                where 
                c2.orden_descuento_id = cu.orden_descuento_id 
                ),0) as pagos
                ,
                (select ifnull(sum(cu1.importe),0) from orden_descuento_cuotas cu1
                where cu1.orden_descuento_id = cu.orden_descuento_id
                and cu1.estado in ('B','C')) as baja
                ,sum(cu.importe)
                - 
                ifnull((select sum(cc.importe) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cobros co on co.id = cc.orden_descuento_cobro_id
                inner join orden_descuento_cuotas c2 on c2.id = cc.orden_descuento_cuota_id
                where 
                c2.orden_descuento_id = cu.orden_descuento_id 
                ),0)
                -
                (select ifnull(sum(cu1.importe),0) from orden_descuento_cuotas cu1
                where cu1.orden_descuento_id = cu.orden_descuento_id
                and cu1.estado in ('B','C'))  as saldo
                from orden_descuento_cuotas cu
                inner join orden_descuentos o on o.id = cu.orden_descuento_id
                where 
                o.permanente = 0
                group by cu.orden_descuento_id
                having saldo = 0 ".(!empty($limit) ? " limit $limit" : "").";";
        
        $ordenes = $oCUOTA->query($sql);
//        debug($ordenes);
        return $ordenes;
    }
    
    
    public function get_periodos(){
        $oLQ = new Liquidacion();
        $sql = "select periodo from liquidaciones l where periodo > date_format(date_sub(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'), interval ".$this->meses." month),'%Y%m')"
                . "and periodo <= date_format(STR_TO_DATE(concat('".$this->periodo_control."','01'),'%Y%m%d'),'%Y%m') group by periodo;";
        $periodo = $oLQ->query($sql);
        return $periodo;        
    }
    
    
    public function get_info_periodo($periodo,$limit = NULL){
        $sql = "select 
                '1_ALTA_PERSONAS' as novedad
                ,l.periodo
                ,p.documento
                ,p.apellido
                ,p.nombre
                ,'' as organismo
                ,'' as razon_social
                ,'' as orden_descuento_id
                ,'' as tipo_producto
                ,'' as tipo_cuota
                ,sum(lc.saldo_actual) as saldo_actual
                ,sum(lc.importe_debitado) as importe_debitado
                ,sum(lc.saldo_actual) - sum(lc.importe_debitado) as saldo
                from liquidacion_cuotas lc
                inner join persona_beneficios b on b.id = lc.persona_beneficio_id
                inner join global_datos org on org.id = b.codigo_beneficio
                inner join liquidaciones l on l.id = lc.liquidacion_id
                inner join socios s on s.id = lc.socio_id
                inner join personas p on p.id = s.persona_id
                inner join global_datos tp on tp.id = lc.tipo_producto
                inner join global_datos tc on tc.id = lc.tipo_cuota
                inner join proveedores pr on pr.id = lc.proveedor_id
                where l.periodo = '$periodo'
                and lc.socio_id not in
                (select socio_id from liquidacion_cuotas lc2
                inner join liquidaciones l2 on l2.id = lc2.liquidacion_id 
                where l2.periodo < l.periodo)
                group by lc.socio_id
                union
                select
                '2_ALTA_ORDENES' as novedad
                ,l.periodo
                ,p.documento
                ,p.apellido
                ,p.nombre
                ,org.concepto_1 as organismo
                ,pr.razon_social
                ,lc.orden_descuento_id
                ,tp.concepto_1 as tipo_producto
                ,tc.concepto_1 as tipo_cuota
                ,sum(lc.saldo_actual) as saldo_actual
                ,sum(lc.importe_debitado) as importe_debitado
                ,sum(lc.saldo_actual) - sum(lc.importe_debitado) as saldo
                from liquidacion_cuotas lc
                inner join persona_beneficios b on b.id = lc.persona_beneficio_id
                inner join global_datos org on org.id = b.codigo_beneficio
                inner join liquidaciones l on l.id = lc.liquidacion_id
                inner join socios s on s.id = lc.socio_id
                inner join personas p on p.id = s.persona_id
                inner join global_datos tp on tp.id = lc.tipo_producto
                inner join global_datos tc on tc.id = lc.tipo_cuota
                inner join proveedores pr on pr.id = lc.proveedor_id
                where l.periodo = '$periodo'
                and lc.orden_descuento_id not in
                (select orden_descuento_id from liquidacion_cuotas lc2
                inner join liquidaciones l2 on l2.id = lc2.liquidacion_id 
                where l2.periodo < l.periodo)
                group by lc.orden_descuento_id,lc.tipo_producto,lc.tipo_cuota
                union
                select 
                '3_BAJA_PERSONAS' as novedad
                ,'$periodo'
                ,p.documento
                ,p.apellido
                ,p.nombre
                ,'' as organismo
                ,'' as razon_social
                ,'' as orden_descuento_id
                ,'' as tipo_producto
                ,'' as tipo_cuota
                ,sum(lc.saldo_actual) as saldo_actual
                ,sum(lc.importe_debitado) as importe_debitado
                ,sum(lc.saldo_actual) - sum(lc.importe_debitado) as saldo
                from liquidacion_cuotas lc
                inner join persona_beneficios b on b.id = lc.persona_beneficio_id
                inner join global_datos org on org.id = b.codigo_beneficio
                inner join liquidaciones l on l.id = lc.liquidacion_id
                inner join socios s on s.id = lc.socio_id
                inner join personas p on p.id = s.persona_id
                inner join global_datos tp on tp.id = lc.tipo_producto
                inner join global_datos tc on tc.id = lc.tipo_cuota
                inner join proveedores pr on pr.id = lc.proveedor_id
                where l.periodo = date_format(date_sub(STR_TO_DATE(concat('$periodo','01'),'%Y%m%d'), interval 1 month),'%Y%m')
                and lc.socio_id not in
                (select socio_id from liquidacion_cuotas lc2
                inner join liquidaciones l2 on l2.id = lc2.liquidacion_id 
                where l2.periodo = '$periodo')
                group by lc.socio_id 
                union
                select 
                '4_BAJA_ORDENES' as novedad
                ,'$periodo'
                ,p.documento
                ,p.apellido
                ,p.nombre
                ,org.concepto_1 as organismo
                ,pr.razon_social
                ,lc.orden_descuento_id
                ,tp.concepto_1 as tipo_producto
                ,tc.concepto_1 as tipo_cuota
                ,sum(lc.saldo_actual) as saldo_actual
                ,sum(lc.importe_debitado) as importe_debitado
                ,sum(lc.saldo_actual) - sum(lc.importe_debitado) as saldo
                from liquidacion_cuotas lc
                inner join persona_beneficios b on b.id = lc.persona_beneficio_id
                inner join global_datos org on org.id = b.codigo_beneficio
                inner join liquidaciones l on l.id = lc.liquidacion_id
                inner join socios s on s.id = lc.socio_id
                inner join personas p on p.id = s.persona_id
                inner join global_datos tp on tp.id = lc.tipo_producto
                inner join global_datos tc on tc.id = lc.tipo_cuota
                inner join proveedores pr on pr.id = lc.proveedor_id
                where l.periodo = date_format(date_sub(STR_TO_DATE(concat('$periodo','01'),'%Y%m%d'), interval 1 month),'%Y%m')
                and lc.socio_id not in
                (select socio_id from liquidacion_cuotas lc2
                inner join liquidaciones l2 on l2.id = lc2.liquidacion_id 
                where l2.periodo = '$periodo')
                group by lc.orden_descuento_id
                order by novedad,apellido,nombre ".(!empty($limit) ? " limit $limit " : "").";";  
        $oLC = new LiquidacionCuota();
        $datos = $oLC->query($sql);
        return $datos;
    }
    
}

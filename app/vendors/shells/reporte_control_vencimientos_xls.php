<?php

/**
 * REPORTE DE CONTROL DE LA LIQUIDACION
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_control_vencimientos_xls 314 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php reporte_control_vencimientos_xls 220 -app /home/adrian/trabajo/www/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ReporteControlVencimientosXlsShell extends Shell {

	var $uses = array('Mutual.OrdenDescuentoCuota','Pfyj.PersonaBeneficio');
	
	var $tasks = array('Temporal');
	
	function main() {
		
            $STOP = 0;

            if(empty($this->args[0])){
                    $this->out("ERROR: PID NO ESPECIFICADO");
                    return;
            }

            $pid = $this->args[0];

            $asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
            $asinc->id = $pid; 

            $periodo_ctrl = $asinc->getParametro('p1');
            $codigo_organismo = $asinc->getParametro('p2');
            $proveedor_id = $asinc->getParametro('p3');
            $a_vencer = $asinc->getParametro('p4');
            $a_vencer = (empty($a_vencer) ? FALSE : TRUE);
            $tipoProducto = $asinc->getParametro('p5');
            $tipoCuota = $asinc->getParametro('p6');
		
            $asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");
		
		
            $this->Temporal->limpiarTabla($pid);
		
		
            $STOP = 0;
            $total = 0;
            $i = 0;
            $asinc->actualizar(2,100,"ESPERE, CONSULTANDO CUOTAS...");		
		
            $cuotas = $this->cargarCuotas($periodo_ctrl,$codigo_organismo,$proveedor_id,$tipoProducto,$tipoCuota);
		
            $total = count($cuotas);
            $asinc->setTotal($total);
            $i = 0;		
		
            if(!empty($cuotas)):
            
                App::import('Helper','Util');
                $oUT = new UtilHelper();            

                ##################################################################################################################
                # PREPARO EXCEL
                ##################################################################################################################
                $FILE_EXCEL = $this->OrdenDescuentoCuota->generarPIN(20).".xls";
                App::import('Model','Proveedores.Proveedor');
                $oPRV = new Proveedor();         
                $oPRV->unbindModel(array('hasMany' => array('MutualProducto')));
                $proveedor = $oPRV->read(null,$proveedor_id);          

                $set = array();
                $set['sheet_title'] = 'CONTROL VENCIMIENTOS';
                $set['labels'] = array(
                    'A1' => 'PROVEEDOR:',
                    'B1' => (!empty($proveedor_id) ? $proveedor['Proveedor']['razon_social'] : '*** TODOS ***'),
                    'A2' => 'ORGANISMO:',
                    'B2' => (!empty($codigo_organismo) ? $oUT->globalDato($codigo_organismo) : ' *** TODOS ***'),
                    'A3' => 'PERIODO CONTROL:',
                    'B3' => $oUT->periodo($periodo_ctrl,true),
                );
                $set['columns'] = array(
                    'texto_7' => 'REF_PROVEEDOR',
                    'texto_1' => 'TIPO_NRO_DOCUMENTO',
                    'texto_2' => 'APELLIDO_NOMBRE',
                    'texto_15' => 'BANCO',
                    'texto_16' => 'ORGANISMO',
                    'texto_14' => 'BENEFICIO',
                    'texto_3' => 'EMPRESA',
                    'texto_4' => 'TURNO',
                    'texto_17' => 'VENDEDOR',
                    'entero_4' => 'ORDEN_DTO',
                    'texto_5' => 'TIPO_NUMERO',
                    'texto_13' => 'PROVEEDOR',
                    'texto_6' => 'PRODUCTO_CONCEPTO',
                    'entero_2' => 'CANTIDAD_CUOTAS',
                    'entero_1' => 'CUOTA',		
                    'decimal_1' => 'IMPORTE_TOTAL',
                    'decimal_2' => 'IMPORTE_CUOTA',
                    'decimal_3' => 'SALDO_CONCILIADO_PERIODO',								
                    'decimal_4' => 'PENDIENTE_ACREDITAR',
                    'decimal_5' => 'SALDO_ACONCILIAR',	
                );	            
                $this->Temporal->prepareXLSSheet(0,$set);
                $ROW = $this->Temporal->XLS->setActiveSheetIndex(0)->getHighestRow();

                $COL = $this->Temporal->XLS->setActiveSheetIndex(0)->getHighestColumn();
                $COL = PHPExcel_Cell::columnIndexFromString($COL);

                ##################################################################################################################
                $temp = array();
		
                foreach($cuotas as $cuotaId):


                        $cuota['persona_documento'] = $cuotaId[0]['todc'];
                        $cuota['persona_apenom'] = $cuotaId[0]['apenom'];
                        $cuota['socio_id'] = $cuotaId[0]['socio_id'];
                        $cuota['empresa'] = $cuotaId[0]['empresa'];
                        $cuota['turno'] = $this->PersonaBeneficio->getTurnoDescripcion($cuotaId[0]['persona_beneficio_id']);
                        $cuota['tipo_nro'] = $cuotaId[0]['tipo_nro'];
                        $cuota['producto_cuota_ref'] = $cuotaId[0]['producto_cuota'];
                        $cuota['nro_referencia_proveedor'] = $cuotaId[0]['nro_referencia_proveedor'];
                        $cuota['numero_odto'] = $cuotaId['OrdenDescuentoCuota']['orden_descuento_id'];                
                        $cuota['cuota'] = $cuotaId[0]['cuota_cuotas'];
                        $cuota['orden_descuento_periodo_ini_d'] = $cuotaId[0]['periodo_ini'];
                        $cuota['orden_descuento_primer_vto_socio'] = $cuotaId[0]['primer_vto_socio'];
                        $cuota['orden_descuento_primer_vto_proveedor'] = $cuotaId[0]['primer_vto_proveedor'];
                        $cuota['proveedor'] =  $cuotaId[0]['razon_social'];                       
                        $cuota['beneficio'] = "";
                        $cuota['banco'] = $cuotaId[0]['nombre'];
                        $cuota['organismo'] = $cuotaId[0]['concepto_1'];
                        $cuota['vendedor'] = $cuotaId[0]['vendedor'];                
                        $cuota['nro_cuota'] = $cuotaId[0]['nro_cuota'];
                        $cuota['cantidad_cuotas'] = $cuotaId[0]['cuotas'];
                        $cuota['cantidad_cuotas_adeudadas'] = $cuotaId[0]['cuotas_adeudadas'];
                        $cuota['orden_descuento_total'] = $cuotaId[0]['importe_total'];
                        $cuota['importe'] = $cuotaId[0]['importe'];
                        $cuota['mora_orden'] = $cuotaId[0]['mora_orden'];
                        $cuota['pendiente_acreditar'] = $cuotaId[0]['pendiente_acreditar'];


//                        $this->out($i."\t".$total);

                        $asinc->actualizar($i,$total,"$i | $total :: PROCESANDO CUOTAS|" . $cuota['persona_apenom'] . "|" . $cuota['tipo_nro'] . "|" . $cuota['cuota']);

                        $temp['AsincronoTemporal'] = array(
                            'asincrono_id' => $asinc->id,
                            'clave_1' => 'REPORTE_1',
                            'clave_2' => $cuota['numero_odto'],
                            'texto_1' => $cuota['persona_documento'],
                            'texto_2' => $cuota['persona_apenom'],
                            'texto_3' => $cuota['empresa'],
                            'texto_4' => $cuota['turno'],				
                            'texto_5' => $cuota['tipo_nro'],
                            'texto_6' => $cuota['producto_cuota_ref'],
                            'texto_7' => $cuota['nro_referencia_proveedor'],				
                            'texto_8' => $cuota['numero_odto'],
                            'texto_9' => $cuota['cuota'],
                            'texto_10' => $cuota['orden_descuento_periodo_ini_d'],
                            'texto_11' => $cuota['orden_descuento_primer_vto_socio'],
                            'texto_12' => $cuota['orden_descuento_primer_vto_proveedor'],
                            'texto_13' => $cuota['proveedor'],
                            'texto_14' => $cuota['beneficio'],
                            'texto_15' => $cuota['banco'],
                            'texto_16' => $cuota['organismo'],
                            'texto_17' => $cuota['vendedor'],
                            'entero_1' => $cuota['cantidad_cuotas_adeudadas'],
                            'entero_2' => $cuota['cantidad_cuotas'],
                            'decimal_1' => $cuota['orden_descuento_total'],
                            'decimal_2' => round($cuota['importe'],2),
                            'decimal_3' => round($cuota['mora_orden'],2),
                            'decimal_4' => round($cuota['pendiente_acreditar'],2),
                            'decimal_5' => round($cuota['mora_orden'] - $cuota['pendiente_acreditar'],2),
                            'entero_4' => $cuota['numero_odto'],
                            'entero_5' => $cuota['socio_id'],
                        );

                        $COL1 = $COL;
                        ####################################################################
                        # CALCULO DE CUOTAS A VENCER POR ORDEN DTO
                        ####################################################################
                        $cuotasVencer = NULL;
                        if($a_vencer)$cuotasVencer = $this->getCuotasAVencer($cuota['numero_odto'], $periodo_ctrl);


                        if(!empty($cuotasVencer) && $a_vencer){
                            $z = 1;
                            foreach ($cuotasVencer as $key => $value) {
                                $this->Temporal->XLS->getActiveSheet()->setCellValueByColumnAndRow($COL1, $ROW, $oUT->periodo($oUT->desplazarPeriodo($periodo_ctrl,$z),true));
                                $this->Temporal->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($COL1).$ROW)->getFont()->setBold(true);
                                $this->Temporal->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($COL1).$ROW)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                                $this->Temporal->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($COL1).$ROW)->getFill()->getStartColor()->setRGB('969696');
                                $COL1++;
                                $z++;
                            }                    
                        }

                        $this->Temporal->writeXLSRow(0,$temp['AsincronoTemporal']);

                        if(!empty($cuotasVencer) && $a_vencer){
                            $COL1 = $COL;
                            $i = $this->Temporal->XLS->setActiveSheetIndex(0)->getHighestRow();
                            foreach ($cuotasVencer as $key => $value){
                                $this->Temporal->XLS->getActiveSheet()->setCellValueByColumnAndRow($COL1++,$i, $value[0]['saldo']);
                            }
                        }
                        if($asinc->detenido()){
                                $STOP = 1;
                                break;
                        }				

                        $i++;

                endforeach; // foreach cuotas
		
            endif; // empty(cuotas)

            $asinc->actualizar(98,100,"CREANDO ARCHIVO $FILE_EXCEL ...");
            $this->Temporal->saveToXLSFile($FILE_EXCEL);
            $asinc->setValue('p6',$FILE_EXCEL);

            $asinc->actualizar(99,100,"FINALIZANDO...");
            $asinc->fin("**** PROCESO FINALIZADO ****");
            return;
		

	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function cargarCuotas($periodo,$organismo,$proveedor,$tipoProducto,$tipoCuota){
		

		
//		$this->OrdenDescuentoCuota->unbindModel(array('belongsTo' => array('OrdenDescuento','Proveedor'), 'hasMany' => array('OrdenDescuentoCobroCuota','LiquidacionCuota')));
//		$this->OrdenDescuentoCuota->bindModel(array('belongsTo' => array('PersonaBeneficio')));
//		
//		$conditions = array();
//		$conditions['OrdenDescuentoCuota.periodo'] = $periodo;
//		if(!empty($proveedor))$conditions['OrdenDescuentoCuota.proveedor_id'] = $proveedor;
//		if(!empty($organismo) || $organismo != '')$conditions['PersonaBeneficio.codigo_beneficio'] = $organismo;
//        if(!empty($tipoProducto) || $tipoProducto != '')$conditions['OrdenDescuentoCuota.tipo_producto'] = $tipoProducto;
//        if(!empty($tipoCuota) || $tipoCuota != '')$conditions['OrdenDescuentoCuota.tipo_cuota'] = $tipoCuota;
//        
////		$conditions['OrdenDescuentoCuota.estado'] = 'A';
//		$conditions['OrdenDescuentoCuota.situacion'] = 'MUTUSICUMUTU';
//		
//		$cuotas = $this->OrdenDescuentoCuota->find('all',array('conditions' => $conditions, 'fields' => array('OrdenDescuentoCuota.id,OrdenDescuentoCuota.orden_descuento_id')));	
        
        
//        $sql = "select Vendedor.id,Persona.documento,Persona.apellido,Persona.nombre, OrdenDescuentoCuota.id,OrdenDescuentoCuota.orden_descuento_id from orden_descuento_cuotas OrdenDescuentoCuota
//                inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
//                left join mutual_producto_solicitudes MutualProductoSolicitud on (MutualProductoSolicitud.id = OrdenDescuento.numero)
//                left join vendedores Vendedor on (Vendedor.id = MutualProductoSolicitud.vendedor_id)
//                left join personas Persona on (Persona.id = Vendedor.persona_id)
//                where
//                OrdenDescuentoCuota.periodo = '$periodo'
//                ".(!empty($proveedor) ? "and OrdenDescuentoCuota.proveedor_id = $proveedor " : "")."
//                ".(!empty($organismo) || $organismo != '' ? " and PersonaBeneficio.codigo_beneficio = '$organismo' " : "")."  
//                ".(!empty($tipoProducto) || $tipoProducto != '' ? " and OrdenDescuentoCuota.tipo_producto = '$tipoProducto' " : "")."
//                ".(!empty($tipoCuota) || $tipoCuota != '' ? " and OrdenDescuentoCuota.tipo_cuota = '$tipoCuota' " : "")."    
//                and OrdenDescuentoCuota.situacion = 'MUTUSICUMUTU'";

   
/*        
        $sql = "select 
                OrdenDescuentoCuota.nro_referencia_proveedor,
                OrdenDescuento.periodo_ini,
                OrdenDescuento.primer_vto_socio,
                OrdenDescuento.primer_vto_proveedor,
                OrdenDescuentoCuota.persona_beneficio_id,
                concat(Vendedor.id,' - ',Persona.apellido,' ',Persona.nombre) as vendedor,
                concat(TDoc.concepto_1,' - ', 
                Persona2.documento) as todc,concat(Persona2.apellido,', ',Persona2.nombre) as apenom,
                Banco.nombre,
                Organismo.concepto_1,
                if(length(PersonaBeneficio.codigo_empresa) = 12,Empresa.concepto_1,'') as empresa,
                concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_nro,
                Proveedor.razon_social,
                concat(TipoProducto.concepto_1,' / ',TipoCuota.concepto_1) as producto_cuota,
                if(OrdenDescuento.permanente = 0,OrdenDescuento.cuotas,0) as cuotas,
                if(OrdenDescuento.permanente = 0,OrdenDescuentoCuota.nro_cuota,0) as nro_cuota,
                if(OrdenDescuento.permanente = 0,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,'0'),'/',lpad(OrdenDescuento.cuotas,2,'0')),'00/00') as cuota_cuotas,
                OrdenDescuento.importe_total,
                OrdenDescuentoCuota.importe,
                (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= '$periodo') as pagos_cuota,

                (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc
                inner join liquidaciones l on (l.id = lc.liquidacion_id)
                where l.periodo <= '$periodo' and l.codigo_organismo = PersonaBeneficio.codigo_beneficio 
                and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                ".(!empty($proveedor) ? " and lc.proveedor_id = $proveedor " : "")."
                and lc.para_imputar = 1 and lc.imputada = 0
                and lc.orden_descuento_cobro_id = 0) as pendiente_cuota_acreditar,

                OrdenDescuentoCuota.importe - 
                (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= '$periodo') as saldo_cuota_conciliado,
                
                OrdenDescuentoCuota.importe - 

                (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= '$periodo')
                - (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc
                inner join liquidaciones l on (l.id = lc.liquidacion_id)
                where l.periodo <= '$periodo' and l.codigo_organismo = PersonaBeneficio.codigo_beneficio 
                and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
                and lc.para_imputar = 1 and lc.imputada = 0
                and lc.orden_descuento_cobro_id = 0) as saldo_cuota_aconciliar,

                ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                where cu.orden_descuento_id = OrdenDescuento.id
                and cu.periodo < '$periodo'),0) -
                (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                inner join orden_descuento_cuotas c on (c.id = cc.orden_descuento_cuota_id)
                inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                where c.orden_descuento_id = OrdenDescuento.id and co.periodo_cobro < '$periodo'
                ) as mora_orden,

                OrdenDescuentoCuota.id,OrdenDescuentoCuota.orden_descuento_id 
                from orden_descuento_cuotas OrdenDescuentoCuota
                inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                left join mutual_producto_solicitudes MutualProductoSolicitud on (MutualProductoSolicitud.id = OrdenDescuento.numero)
                left join vendedores Vendedor on (Vendedor.id = MutualProductoSolicitud.vendedor_id)
                left join personas Persona on (Persona.id = Vendedor.persona_id)
                inner join personas as Persona2 on (Persona2.id = PersonaBeneficio.persona_id)
                inner join global_datos TDoc on (TDoc.id = Persona2.tipo_documento)
                left join bancos Banco on (Banco.id = PersonaBeneficio.banco_id)
                inner join global_datos Organismo on (Organismo.id = PersonaBeneficio.codigo_beneficio)
                inner join global_datos Empresa on (Empresa.id = PersonaBeneficio.codigo_empresa)
                inner join proveedores Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
                inner join global_datos TipoProducto on (TipoProducto.id = OrdenDescuentoCuota.tipo_producto)
                inner join global_datos TipoCuota on (TipoCuota.id = OrdenDescuentoCuota.tipo_cuota)
                where
                OrdenDescuentoCuota.periodo = '$periodo'
                ".(!empty($proveedor) ? " and OrdenDescuentoCuota.proveedor_id = $proveedor " : "")."
                ".(!empty($organismo) || $organismo != '' ? " and PersonaBeneficio.codigo_beneficio = '$organismo' " : "")."  
                ".(!empty($tipoProducto) || $tipoProducto != '' ? " and OrdenDescuentoCuota.tipo_producto = '$tipoProducto' " : "")."
                ".(!empty($tipoCuota) || $tipoCuota != '' ? " and OrdenDescuentoCuota.tipo_cuota = '$tipoCuota' " : "")."                        
                and OrdenDescuentoCuota.estado <> 'B' 
                -- and OrdenDescuentoCuota.importe > (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                -- inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                -- where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= '$periodo')
                -- order by Persona2.apellido,Persona2.nombre,OrdenDescuento.id,OrdenDescuentoCuota.nro_cuota
                ;";
*/
       
//        $sql = "(select 
//
//                    OrdenDescuentoCuota.nro_referencia_proveedor,
//                    OrdenDescuento.periodo_ini,
//                    OrdenDescuento.primer_vto_socio,
//                    OrdenDescuento.primer_vto_proveedor,
//                    OrdenDescuentoCuota.persona_beneficio_id,
//                    concat(Vendedor.id,' - ',Persona.apellido,' ',Persona.nombre) as vendedor,
//                    concat(TDoc.concepto_1,' - ', 
//                    Persona2.documento) as todc,concat(Persona2.apellido,', ',Persona2.nombre) as apenom,
//                    Banco.nombre,
//                    Organismo.concepto_1,
//                    if(length(PersonaBeneficio.codigo_empresa) = 12,Empresa.concepto_1,'') as empresa,
//                    concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_nro,
//                    Proveedor.razon_social,
//                    concat(TipoProducto.concepto_1,' / ',TipoCuota.concepto_1) as producto_cuota,
//                    if(OrdenDescuento.permanente = 0,OrdenDescuento.cuotas,0) as cuotas,
//                    if(OrdenDescuento.permanente = 0,OrdenDescuentoCuota.nro_cuota,0) as nro_cuota,
//                    if(OrdenDescuento.permanente = 0,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,'0'),'/',lpad(OrdenDescuento.cuotas,2,'0')),'00/00') as cuota_cuotas,
//
//                    OrdenDescuento.importe_total,
//
//                    OrdenDescuentoCuota.importe,
//
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro = lc.periodo) as pagos_cuota,
//
//                    OrdenDescuentoCuota.importe - 
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= lc.periodo) as saldo_cuota_conciliado,
//                    
//                    OrdenDescuentoCuota.importe - 
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= lc.periodo)
//                    - (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc
//                    inner join liquidaciones l on (l.id = lc.liquidacion_id)
//                    where l.periodo <= lc.periodo and l.codigo_organismo = PersonaBeneficio.codigo_beneficio 
//                    and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//                    and lc.para_imputar = 1 and lc.imputada = 0
//                    and lc.proveedor_id = OrdenDescuentoCuota.proveedor_id 
//                    and ifnull(lc.orden_descuento_cobro_id,0)) as saldo_cuota_aconciliar,
//
//                    ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
//                    where cu.orden_descuento_id = OrdenDescuento.id
//                    and cu.periodo < lc.periodo),0) -
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cuotas c on (c.id = cc.orden_descuento_cuota_id)
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where c.orden_descuento_id = OrdenDescuento.id and co.periodo_cobro < lc.periodo
//                    ) as mora_orden,
//
//                    ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
//                    where cu.orden_descuento_id = OrdenDescuento.id
//                    and cu.periodo < lc.periodo),0) -
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cuotas c on (c.id = cc.orden_descuento_cuota_id)
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where c.orden_descuento_id = OrdenDescuento.id and co.periodo_cobro < lc.periodo
//                    )-(select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc
//                    inner join liquidaciones l on (l.id = lc.liquidacion_id)
//                    where l.periodo <= lc.periodo and l.codigo_organismo = PersonaBeneficio.codigo_beneficio 
//                    and lc.orden_descuento_id = OrdenDescuento.id
//                    and lc.periodo_cuota < lc.periodo
//                    and lc.para_imputar = 1 and lc.imputada = 0
//                    and lc.proveedor_id = OrdenDescuentoCuota.proveedor_id 
//                    and ifnull(lc.orden_descuento_cobro_id,0) = 0) as mora_orden_a_conciliar,
//                    OrdenDescuentoCuota.id,
//                    OrdenDescuentoCuota.orden_descuento_id 
//                    ,OrdenDescuentoCuota.periodo,
//
//                    0 as saldo_actual_periodo,
//                    0 as pendiente_imputar_periodo,
//                    lc.saldo_actual as saldo_actual_mora,
//                    lc.importe_debitado as pendiente_imputar_mora
//
//
//                    from orden_descuento_cuotas OrdenDescuentoCuota
//                    inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                    inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
//                    left join mutual_producto_solicitudes MutualProductoSolicitud on (MutualProductoSolicitud.id = OrdenDescuento.numero)
//                    left join vendedores Vendedor on (Vendedor.id = MutualProductoSolicitud.vendedor_id)
//                    left join personas Persona on (Persona.id = Vendedor.persona_id)
//                    inner join personas as Persona2 on (Persona2.id = PersonaBeneficio.persona_id)
//                    inner join global_datos TDoc on (TDoc.id = Persona2.tipo_documento)
//                    left join bancos Banco on (Banco.id = PersonaBeneficio.banco_id)
//                    inner join global_datos Organismo on (Organismo.id = PersonaBeneficio.codigo_beneficio)
//                    inner join global_datos Empresa on (Empresa.id = PersonaBeneficio.codigo_empresa)
//                    inner join proveedores Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
//                    inner join global_datos TipoProducto on (TipoProducto.id = OrdenDescuentoCuota.tipo_producto)
//                    inner join global_datos TipoCuota on (TipoCuota.id = OrdenDescuentoCuota.tipo_cuota)
//
//                    left join (select l.periodo, l.codigo_organismo,lc.orden_descuento_cuota_id,
//                    lc.periodo_cuota,lc.saldo_actual,lc.importe_debitado,lc.mutual_adicional_pendiente_id 
//                    from liquidacion_cuotas lc
//                    inner join liquidaciones l on (l.id = lc.liquidacion_id)
//                    ) as lc on lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//
//                    where
//                    lc.periodo = '$periodo'
//                    and ifnull(lc.mutual_adicional_pendiente_id,0) = 0
//                    and OrdenDescuentoCuota.periodo < lc.periodo
//                    and OrdenDescuentoCuota.estado NOT IN ('B','D')
//                    ".(!empty($proveedor) ? " and OrdenDescuentoCuota.proveedor_id = $proveedor " : "")."
//                    ".(!empty($organismo) || $organismo != '' ? " and lc.codigo_organismo = '$organismo' " : "")."  
//                    ".(!empty($tipoProducto) && $tipoProducto != '' ? " and OrdenDescuentoCuota.tipo_producto = '$tipoProducto' " : "")."
//                    ".(!empty($tipoCuota) && $tipoCuota != '' ? " and OrdenDescuentoCuota.tipo_cuota = '$tipoCuota' " : "")."                        
//
//                    )
//                    union
//                    (
//                    
//                    select 
//                    OrdenDescuentoCuota.nro_referencia_proveedor,
//                    OrdenDescuento.periodo_ini,
//                    OrdenDescuento.primer_vto_socio,
//                    OrdenDescuento.primer_vto_proveedor,
//                    OrdenDescuentoCuota.persona_beneficio_id,
//                    concat(Vendedor.id,' - ',Persona.apellido,' ',Persona.nombre) as vendedor,
//                    concat(TDoc.concepto_1,' - ', 
//                    Persona2.documento) as todc,concat(Persona2.apellido,', ',Persona2.nombre) as apenom,
//                    Banco.nombre,
//                    Organismo.concepto_1,
//                    if(length(PersonaBeneficio.codigo_empresa) = 12,Empresa.concepto_1,'') as empresa,
//                    concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_nro,
//                    Proveedor.razon_social,
//                    concat(TipoProducto.concepto_1,' / ',TipoCuota.concepto_1) as producto_cuota,
//                    if(OrdenDescuento.permanente = 0,OrdenDescuento.cuotas,0) as cuotas,
//                    if(OrdenDescuento.permanente = 0,OrdenDescuentoCuota.nro_cuota,0) as nro_cuota,
//                    if(OrdenDescuento.permanente = 0,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,'0'),'/',lpad(OrdenDescuento.cuotas,2,'0')),'00/00') as cuota_cuotas,
//
//                    OrdenDescuento.importe_total,
//
//                    OrdenDescuentoCuota.importe,
//
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro = lc.periodo) as pagos_cuota,
//
//                    OrdenDescuentoCuota.importe - 
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= lc.periodo) as saldo_cuota_conciliado,
//                    
//                    OrdenDescuentoCuota.importe - 
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where cc.orden_descuento_cuota_id = OrdenDescuentoCuota.id and co.periodo_cobro <= lc.periodo)
//                    - (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc
//                    inner join liquidaciones l on (l.id = lc.liquidacion_id)
//                    where l.periodo <= lc.periodo and l.codigo_organismo = PersonaBeneficio.codigo_beneficio 
//                    and lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//                    and lc.para_imputar = 1 and lc.imputada = 0
//                    and lc.proveedor_id = OrdenDescuentoCuota.proveedor_id 
//                    and ifnull(lc.orden_descuento_cobro_id,0)) as saldo_cuota_aconciliar,
//
//                    ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
//                    where cu.orden_descuento_id = OrdenDescuento.id
//                    and cu.periodo < lc.periodo),0) -
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cuotas c on (c.id = cc.orden_descuento_cuota_id)
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where c.orden_descuento_id = OrdenDescuento.id and co.periodo_cobro < lc.periodo
//                    ) as mora_orden,
//
//                    ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
//                    where cu.orden_descuento_id = OrdenDescuento.id
//                    and cu.periodo < lc.periodo),0) -
//                    (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
//                    inner join orden_descuento_cuotas c on (c.id = cc.orden_descuento_cuota_id)
//                    inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
//                    where c.orden_descuento_id = OrdenDescuento.id and co.periodo_cobro < lc.periodo
//                    )-(select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc
//                    inner join liquidaciones l on (l.id = lc.liquidacion_id)
//                    where l.periodo <= lc.periodo and l.codigo_organismo = PersonaBeneficio.codigo_beneficio 
//                    and lc.orden_descuento_id = OrdenDescuento.id
//                    and lc.periodo_cuota < lc.periodo
//                    and lc.para_imputar = 1 and lc.imputada = 0
//                    and lc.proveedor_id = OrdenDescuentoCuota.proveedor_id 
//                    and ifnull(lc.orden_descuento_cobro_id,0) = 0) as mora_orden_a_conciliar,
//                    OrdenDescuentoCuota.id,
//                    OrdenDescuentoCuota.orden_descuento_id 
//                    ,OrdenDescuentoCuota.periodo,
//
//                    lc.saldo_actual as saldo_actual_periodo,
//                    lc.importe_debitado as pendiente_imputar_periodo,
//                    0 as saldo_actual_mora,
//                    0 as pendiente_imputar_mora
//
//                    from orden_descuento_cuotas OrdenDescuentoCuota
//                    inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
//                    inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
//                    left join mutual_producto_solicitudes MutualProductoSolicitud on (MutualProductoSolicitud.id = OrdenDescuento.numero)
//                    left join vendedores Vendedor on (Vendedor.id = MutualProductoSolicitud.vendedor_id)
//                    left join personas Persona on (Persona.id = Vendedor.persona_id)
//                    inner join personas as Persona2 on (Persona2.id = PersonaBeneficio.persona_id)
//                    inner join global_datos TDoc on (TDoc.id = Persona2.tipo_documento)
//                    left join bancos Banco on (Banco.id = PersonaBeneficio.banco_id)
//                    inner join global_datos Organismo on (Organismo.id = PersonaBeneficio.codigo_beneficio)
//                    inner join global_datos Empresa on (Empresa.id = PersonaBeneficio.codigo_empresa)
//                    inner join proveedores Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
//                    inner join global_datos TipoProducto on (TipoProducto.id = OrdenDescuentoCuota.tipo_producto)
//                    inner join global_datos TipoCuota on (TipoCuota.id = OrdenDescuentoCuota.tipo_cuota)
//
//                    left join (select l.periodo, l.codigo_organismo,lc.orden_descuento_cuota_id,
//                    lc.periodo_cuota,lc.saldo_actual,lc.importe_debitado,lc.mutual_adicional_pendiente_id 
//                    from liquidacion_cuotas lc
//                    inner join liquidaciones l on (l.id = lc.liquidacion_id)
//                    ) as lc on lc.orden_descuento_cuota_id = OrdenDescuentoCuota.id
//
//                    where
//
//                    lc.periodo = '$periodo'
//                    and ifnull(lc.mutual_adicional_pendiente_id,0) = 0 
//                    and OrdenDescuentoCuota.periodo = lc.periodo
//                    and OrdenDescuentoCuota.estado NOT IN ('B','D')
//                    ".(!empty($proveedor) ? " and OrdenDescuentoCuota.proveedor_id = $proveedor " : "")."
//                    ".(!empty($organismo) || $organismo != '' ? " and lc.codigo_organismo = '$organismo' " : "")."  
//                    ".(!empty($tipoProducto) && $tipoProducto != '' ? " and OrdenDescuentoCuota.tipo_producto = '$tipoProducto' " : "")."
//                    ".(!empty($tipoCuota) && $tipoCuota != '' ? " and OrdenDescuentoCuota.tipo_cuota = '$tipoCuota' " : "")."                        
//
//                    order by OrdenDescuentoCuota.orden_descuento_id,OrdenDescuentoCuota.nro_cuota
//                    )";
        

            $sql = "select 
                        OrdenDescuentoCuota.nro_referencia_proveedor,
                        OrdenDescuento.periodo_ini,
                        OrdenDescuento.primer_vto_socio,
                        OrdenDescuento.primer_vto_proveedor,
                        OrdenDescuentoCuota.persona_beneficio_id,
                        concat(Vendedor.id,' - ',Persona.apellido,' ',Persona.nombre) as vendedor,
                        concat(TDoc.concepto_1,' - ', 
                        Persona2.documento) as todc,concat(Persona2.apellido,', ',Persona2.nombre) as apenom,
                        Banco.nombre,
                        Organismo.concepto_1,
                        if(length(PersonaBeneficio.codigo_empresa) = 12,Empresa.concepto_1,'') as empresa,
                        concat(OrdenDescuento.tipo_orden_dto,' #',OrdenDescuento.numero) as tipo_nro,
                        Proveedor.razon_social,
                        concat(TipoProducto.concepto_1,' / ',TipoCuota.concepto_1) as producto_cuota,

                        if(OrdenDescuento.permanente = 0,OrdenDescuento.cuotas,(
                        select count(*) from orden_descuento_cuotas ocu where ocu.periodo <= OrdenDescuentoCuota.periodo
                        and ocu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id)) as cuotas,

                        (select count(*) from orden_descuento_cuotas ocu where ocu.periodo <= OrdenDescuentoCuota.periodo
                        and ocu.orden_descuento_id = OrdenDescuentoCuota.orden_descuento_id
                        and ocu.importe > ifnull((
                        select sum(cocu.importe) from orden_descuento_cobro_cuotas cocu
                        inner join orden_descuento_cobros co on co.id = cocu.orden_descuento_cobro_id
                        where ocu.id = cocu.orden_descuento_cuota_id and co.periodo_cobro <= OrdenDescuentoCuota.periodo
                        ),0)) as cuotas_adeudadas,

                        if(OrdenDescuento.permanente = 0,OrdenDescuentoCuota.nro_cuota,0) as nro_cuota,

                        if(OrdenDescuento.permanente = 0,concat(lpad(OrdenDescuentoCuota.nro_cuota,2,'0'),'/',lpad(OrdenDescuento.cuotas,2,'0')),'00/00') as cuota_cuotas,

                        if(OrdenDescuento.permanente = 0,OrdenDescuento.importe_total,
                        ifnull((select sum(cu.importe) from orden_descuento_cuotas cu
                        where cu.orden_descuento_id = OrdenDescuento.id
                        and cu.periodo <= OrdenDescuentoCuota.periodo),0)
                        ) as importe_total,

                        SUM(OrdenDescuentoCuota.importe) AS importe,
                        OrdenDescuentoCuota.id,
                        OrdenDescuentoCuota.orden_descuento_id 
                        ,OrdenDescuentoCuota.periodo,

                        ifnull((select sum((cu.importe)) from orden_descuento_cuotas cu
                        where cu.orden_descuento_id = OrdenDescuento.id
                        and cu.periodo <= OrdenDescuentoCuota.periodo
                        and cu.tipo_producto = OrdenDescuentoCuota.tipo_producto
                        and cu.tipo_cuota = OrdenDescuentoCuota.tipo_cuota                        
                        ),0) -
                        (select ifnull(sum(cc.importe),0) from orden_descuento_cobro_cuotas cc
                        inner join orden_descuento_cuotas c on (c.id = cc.orden_descuento_cuota_id)
                        inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                        where c.orden_descuento_id = OrdenDescuento.id and co.periodo_cobro <= OrdenDescuentoCuota.periodo
                        and c.tipo_producto = OrdenDescuentoCuota.tipo_producto
                        and c.tipo_cuota = OrdenDescuentoCuota.tipo_cuota                         
                        ) as mora_orden,

                        (select ifnull(sum((cc.importe)),0) from orden_descuento_cobro_cuotas cc
                        inner join orden_descuento_cobros co on (co.id = cc.orden_descuento_cobro_id)
                        inner join orden_descuento_cuotas c on (c.id = cc.orden_descuento_cuota_id)
                        where c.orden_descuento_id = OrdenDescuento.id
                        and co.periodo_cobro <= OrdenDescuentoCuota.periodo) as pagos_cuota,

                        (select ifnull(sum(importe_debitado),0) from liquidacion_cuotas lc
                        inner join liquidaciones l on (l.id = lc.liquidacion_id)
                        where 
                        l.periodo <= OrdenDescuentoCuota.periodo 
                        and l.codigo_organismo = PersonaBeneficio.codigo_beneficio 
                        and lc.orden_descuento_id = OrdenDescuento.id
                        and lc.proveedor_id = OrdenDescuentoCuota.proveedor_id 
                        and ifnull(lc.orden_descuento_cobro_id,0) = 0) as pendiente_acreditar


                    from orden_descuento_cuotas OrdenDescuentoCuota
                    inner join persona_beneficios PersonaBeneficio on (PersonaBeneficio.id = OrdenDescuentoCuota.persona_beneficio_id)
                    inner join orden_descuentos OrdenDescuento on (OrdenDescuento.id = OrdenDescuentoCuota.orden_descuento_id)
                    left join mutual_producto_solicitudes MutualProductoSolicitud on (MutualProductoSolicitud.id = OrdenDescuento.numero)
                    left join vendedores Vendedor on (Vendedor.id = MutualProductoSolicitud.vendedor_id)
                    left join personas Persona on (Persona.id = Vendedor.persona_id)
                    inner join personas as Persona2 on (Persona2.id = PersonaBeneficio.persona_id)
                    inner join global_datos TDoc on (TDoc.id = Persona2.tipo_documento)
                    left join bancos Banco on (Banco.id = PersonaBeneficio.banco_id)
                    inner join global_datos Organismo on (Organismo.id = PersonaBeneficio.codigo_beneficio)
                    inner join global_datos Empresa on (Empresa.id = PersonaBeneficio.codigo_empresa)
                    inner join proveedores Proveedor on (Proveedor.id = OrdenDescuentoCuota.proveedor_id)
                    inner join global_datos TipoProducto on (TipoProducto.id = OrdenDescuentoCuota.tipo_producto)
                    inner join global_datos TipoCuota on (TipoCuota.id = OrdenDescuentoCuota.tipo_cuota)
                    where
                        OrdenDescuentoCuota.periodo = '$periodo'
                        and OrdenDescuentoCuota.estado NOT IN ('B','D')
                        ".(!empty($proveedor) ? " and OrdenDescuentoCuota.proveedor_id = $proveedor " : "")."
                        ".(!empty($organismo) || $organismo != '' ? " and PersonaBeneficio.codigo_beneficio = '$organismo' " : "")."    
                        ".(!empty($tipoProducto) && $tipoProducto != '' ? " and OrdenDescuentoCuota.tipo_producto = '$tipoProducto' " : "")."
                        ".(!empty($tipoCuota) && $tipoCuota != '' ? " and OrdenDescuentoCuota.tipo_cuota = '$tipoCuota' " : "")."    
                        group by OrdenDescuentoCuota.socio_id, OrdenDescuentoCuota.orden_descuento_id, OrdenDescuentoCuota.periodo
                        ,OrdenDescuentoCuota.tipo_producto,OrdenDescuentoCuota.tipo_cuota
                        having mora_orden > 0 ;";
            
        $cuotas = $this->OrdenDescuentoCuota->query($sql);

		return $cuotas;
		
		
	}
	

	function getMoraByOrdenDto($orden_dto_id,$periodo){
		

		
	}
	
	
    function getCuotasAVencer($id,$periodoControl){
        App::import('Model', 'Mutual.OrdenDescuento');
        $oORD = new OrdenDescuento();        
        return $oORD->getCuotasAVencer($id, $periodoControl);
    }	

}
?>

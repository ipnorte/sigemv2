<?php

/**
 * Reporte Liquidacion Proveedor Detalle
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_liquidacion_proveedor_detalle2 885 -app /home/adrian/Desarrollo/www/sigem/app/
 * "D:\develop\xampp\php\php.exe" "D:\develop\xampp\htdocs\sigem\cake\console\cake.php" reporte_liquidacion_proveedor_detalle2 29 -app "D:\develop\xampp\htdocs\sigem\app\"
 * /usr/bin/php5 /mnt/800/trabajo/www/sigem/cake/console/cake.php reporte_liquidacion_proveedor_detalle_xls 7 -app /mnt/800/trabajo/www/sigem/app/
 * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php reporte_liquidacion_proveedor_detalle_xls 33300 -app /home/mutualam/public_html/sigem/app/
 * 
 * /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php reporte_liquidacion_proveedor_detalle_xls_store 71695 -app /home/adrian/Trabajo/www/sigemv2/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ReporteLiquidacionProveedorDetalleXlsStoreShell extends Shell {

	var $fecha_desde;
	var $fecha_hasta;
	var $uses = array(
						'Mutual.Liquidacion',
						'Mutual.LiquidacionCuota',
						'Proveedores.Proveedor',
						'Mutual.OrdenDescuentoCuota',
						'Mutual.LiquidacionSocio',
						'Config.BancoRendicionCodigo',
						'Mutual.LiquidacionSocioRendicion',
	);
	var $liquidacion_id;
	var $proveedor_id;
	var $tipo_cuota;
	var $tipo_producto;
	var $oLiquidacion;
	var $procesarSobrePreImputacion;
	
	var $tasks = array('Temporal');
	
	function main() {
        
        Configure::write('debug',1);
        
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$this->liquidacion_id	= $asinc->getParametro('p1');
		$this->proveedor_id		= $asinc->getParametro('p2');
		$this->tipo_cuota		= $asinc->getParametro('p3');
		$this->tipo_producto	= $asinc->getParametro('p4');
		
		$this->procesarSobrePreImputacion	= $asinc->getParametro('p5');
		$this->procesarSobrePreImputacion = (!empty($this->procesarSobrePreImputacion) ? $this->procesarSobrePreImputacion : 0);
		
		$liquidacion =  $this->Liquidacion->cargar($this->liquidacion_id);
		
        $this->oLiquidacion = $liquidacion;
        $this->proveedor_id = 0;
		
		$asinc->actualizar(1,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO LIQUIDACION...");

		//limpio la tabla temporal
		// if(!$this->Temporal->limpiarTabla($asinc->id)){
		// 	$asinc->fin("SE PRODUJO UN ERROR...");
		// 	return;
		// }
		
		App::import('Model','Mutual.OrdenDescuentoCobroCuota');
		$oCCUOTA = new OrdenDescuentoCobroCuota();

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();	

		App::import('Helper','Util');
		$oUT = new UtilHelper();

		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oORDCUO = new OrdenDescuentoCuota();
        
		App::import('Model','Mutual.MutualProductoSolicitud');
		$oSOLICITUD = new MutualProductoSolicitud();        
		
		App::import('Model','proveedores.MetodoCalculoCuota');
		$oMETODO = new MetodoCalculoCuota();
                
        App::import('Model','Mutual.LiquidacionCuota');
        $oLC = new LiquidacionCuota();                

        App::import('Model','Proveedores.Proveedor');
        $oPRV = new Proveedor(); 
        
		App::import('Model','Shells.AsincronoTemporal');
		$oTEMP = new AsincronoTemporal();        


        ##########################################################################################################################
        # PHPEXCEL
        ##########################################################################################################################
        $FILE_EXCEL = $oSOLICITUD->generarPIN(20).".xls";
        $this->Temporal->setXLSObject(7);
        $oPRV->unbindModel(array('hasMany' => array('MutualProducto')));
        $proveedor = $oPRV->read(null,$this->proveedor_id);          
        $set = array();
        $set[0]['sheet_title'] = 'DETALLE ' . ($this->procesarSobrePreImputacion == 1 ? " (PRE-IMPUTACION)" : "" );
        $set[0]['labels'] = array(
            'A1' => 'LIQUIDACION:',
            'B1' => $oUT->globalDato($liquidacion['Liquidacion']['codigo_organismo']) ." - ". $oUT->periodo($liquidacion['Liquidacion']['periodo']) . ($this->procesarSobrePreImputacion == 1 ? " ** PRE-IMPUTADA **" : ""),
            'A2' => 'PROVEEDOR:',
            'B2' => $proveedor['Proveedor']['razon_social'],
            'A3' => 'CONCEPTO:',
            'B3' => $oUT->globalDato($this->tipo_producto)." - ". $oUT->globalDato($this->tipo_cuota),
            'B5' => 'DETALLE DE LA LIQUIDACION ' . ($this->procesarSobrePreImputacion == 1 ? " *** PRE-IMPUTADA ***" : "" ),
        );
        $set[0]['columns'] = array(
                            'texto_1' => 'TIPO_NRO_DOCUMENTO',
                            'texto_4' => 'APELLIDO_NOMBRE',
                            'texto_13' => 'CALIF.',
                            'texto_14' => 'PERIODO_CALIF.',
                            'texto_6' => 'TIPO_NUMERO',
                            'texto_12' => 'REF_PROVEEDOR',
                            'texto_7' => 'PRODUCTO_CONCEPTO',
                            'texto_8' => 'CUOTA',
                            'texto_10' => 'PERIODO',
                            'texto_14' => 'REF_ORIGEN',
                            'decimal_1' => 'LIQUIDADO',
                            'decimal_2' => ($this->procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO"),
                            'decimal_3' => 'SALDO',				
                            'decimal_4' => 'PORC_COMISION',
                            'decimal_5' => 'COMISION',
                            'decimal_6' => 'NETO_PROVEEDOR',
                            'texto_18' => 'CUIT_CUIL',
        );

        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        if(isset($INI_FILE['general']['discrimina_iva']) && $INI_FILE['general']['discrimina_iva'] != 0){
            $set[0]['columns'] = array(
                                'texto_1' => 'TIPO_NRO_DOCUMENTO',
                                'texto_2' => 'APELLIDO_NOMBRE',
                                'texto_13' => 'CALIF.',
                                'texto_14' => 'PERIODO_CALIF.',
                                'texto_3' => 'TIPO_NUMERO',
                                'texto_9' => 'REF_PROVEEDOR',
                                'texto_4' => 'PRODUCTO_CONCEPTO',
                                'texto_6' => 'CUOTA',
                                'texto_7' => 'PERIODO',
                                'texto_17' => 'REF_ORIGEN',
                                'decimal_1' => 'LIQUIDADO',
                                'decimal_2' => ($this->procesarSobrePreImputacion == 1 ? "PRE-IMPUTADO" : "IMPUTADO"),
                                'decimal_3' => 'SALDO',				
                                'decimal_4' => 'PORC_COMISION',
                                'decimal_5' => 'COMISION',
                                'decimal_9' => 'IVA',
                                'decimal_6' => 'NETO_PROVEEDOR',
                                'texto_18' => 'CUIT_CUIL',
                                'decimal_14' => 'IMPORTE_CUOTA',
                                'decimal_10' => 'CAPITAL_CUOTA',
                                'decimal_11' => 'ADICIONAL_CUOTA',
                                'decimal_12' => 'INTERES_CUOTA',
                                'decimal_13' => 'IVA_CUOTA',
                                'texto_16' => 'MFRANCES',                
            );
        }        
        $this->Temporal->prepareXLSSheet(0,$set[0]);
        
        $set[1]['sheet_title'] = 'DEBITOS NO COBRADOS';
        $set[1]['labels']['B5'] = 'DETALLE DEBITOS NO COBRADOS INFORMADOS POR BANCO';
        $set[1]['columns'] = array(
                            'texto_1' => 'TIPO_NRO_DOCUMENTO',
                            'texto_2' => 'APELLIDO_NOMBRE',
                            'texto_13' => 'CALIF.',
                            'texto_14' => 'PERIODO_CALIF.',
                            'texto_3' => 'TIPO_NUMERO',
                            'texto_9' => 'REF_PROVEEDOR',
                            'texto_4' => 'PRODUCTO_CONCEPTO',
                            'texto_6' => 'CUOTA',
                            'texto_7' => 'PERIODO',
                            'decimal_1' => 'LIQUIDADO',
                            'texto_11' => 'CODIGO',
                            'texto_12' => 'DESCRIPCION',

        );        
        $this->Temporal->prepareXLSSheet(1,$set[1]);
        
        $set[2]['sheet_title'] = 'REVERSOS';
        $set[2]['labels']['B5'] = 'DETALLE CUOTAS REVERSADAS';
        $set[2]['columns'] = array(
            'SOCIO',
            'TIPO_NUMERO',
            'REF_PROVEEDOR',
            'PRODUCTO_CONCEPTO',
            'CUOTA',
            'PERIODO',
            'IMPORTE_REVERSADO',
            '%_COMISION',
            'COMISION_REVERSADA',
            'NETO_PROVEEDOR',
        );
        $this->Temporal->prepareXLSSheet(2,$set[2]);
        
        $set[3]['sheet_title'] = 'OTRAS COBRANZAS';
        $set[3]['labels']['B5'] = 'INFORME DE COBRANZAS NO EFECTUADAS POR RECIBO DE SUELDO';
        $set[3]['columns'] = array(
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
        $this->Temporal->prepareXLSSheet(3,$set[3]);
        
        $set[4]['sheet_title'] = 'BAJAS';
        $set[4]['labels']['B5'] = 'INFORME DE CUOTAS CORRESPONDIENTES AL PERIODO DADAS DE BAJA';
        $set[4]['columns'] = array(
                            'texto_1' => 'TIPO_NRO_DOCUMENTO',
                            'texto_2' => 'APELLIDO_NOMBRE',
                            'texto_4' => 'TIPO NUMERO',
                            'texto_10' => 'REFERENCIA_PROV',
                            'texto_5' => 'PROVEEDOR_PRODUCTO',
                            'texto_6' => 'CONCEPTO CUOTA',
                            'texto_7' => 'SITUACION',
                            'texto_8' => 'PERIODO_CUOTA',
                            'texto_9' => 'NRO_CUOTA',
                            'decimal_1' => 'IMPORTE',
        );		        
        $this->Temporal->prepareXLSSheet(4,$set[4]);
        
        
        $set[5]['sheet_title'] = 'STOP_DEBIT';
        $set[5]['labels']['B5'] = 'INFORME DE STOP DEBIT ANTERIORES';
        $set[5]['columns'] = array(
                            'texto_1' => 'DOCUMENTO',
                            'texto_2' => 'APELLIDO_NOMBRE',
                            'texto_3' => 'ORDEN',
                            'texto_4' => 'TIPO_NUMERO',
                            'texto_5' => 'PERIODO',
                            'decimal_1' => 'SALDO',
        );	        
        $this->Temporal->prepareXLSSheet(5,$set[5]); 
        
        $set[6]['sheet_title'] = 'MORA_CUOTA_UNO';
        $set[6]['labels']['B5'] = 'MORA CUOTA UNO';
        $set[6]['columns'] = array(
                            'texto_1' => 'DOCUMENTO',
                            'texto_2' => 'APELLIDO_NOMBRE',
                            'texto_3' => 'TEL.FIJO',
                            'texto_4' => 'TEL.MOVIL',
                            'texto_5' => 'TEL.MENS',
                            'texto_6' => 'EMPRESA/REPARTICION',
                            'texto_7' => 'ORDEN_DTO',
                            'texto_8' => 'TIPO_NRO',
//                            'texto_9' => 'FECHA_DEBITO',
                            'decimal_1' => 'SALDO',
        );	        
        $this->Temporal->prepareXLSSheet(6,$set[3]); 
        
        $set[7]['sheet_title'] = 'MORA_TEMPRANA';
        $set[7]['labels']['B5'] = 'MORA TEMPRANA';
        $set[7]['columns'] = array(
                            'texto_1' => 'DOCUMENTO',
                            'texto_2' => 'APELLIDO_NOMBRE',
                            'texto_3' => 'TEL.FIJO',
                            'texto_4' => 'TEL.MOVIL',
                            'texto_5' => 'TEL.MENS',
                            'texto_6' => 'EMPRESA/REPARTICION',
                            'texto_7' => 'ORDEN_DTO',
                            'texto_8' => 'TIPO_NRO',
//                            'texto_9' => 'FECHA_DEBITO',
                            'decimal_1' => 'SALDO',
        );	        
        $this->Temporal->prepareXLSSheet(7,$set[7]);        

        ##########################################################################################################################
        
        $INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
        $CALC_IVA = false;
        if(isset($INI_FILE['general']['discrimina_iva']) && $INI_FILE['general']['discrimina_iva'] != 0){
            $CALC_IVA = $INI_FILE['general']['discrimina_iva'];
        } 

        $asinc->actualizar(10,100,"ESPERE, GENERANDO DETALLE DE LA LIQUIDACION...");
        $SPCALL = "CALL SP_REPORTE_PROVEEDOR_DETALLE(".$asinc->id.",'REPORTE_A1',". $this->liquidacion_id.",".$this->proveedor_id.",'".$this->tipo_producto."','".$this->tipo_cuota."',NULL);";
        $oLC->query($SPCALL);
        
		if(!empty($oLC->getDataSource()->error)){
            $asinc->actualizar(10, 100, $oLC->getDataSource()->error);
            return;
        }
        $asinc->actualizar(20,100,"ESPERE, GENERANDO DETALLE DE NO COBRADOS...");
        $SPCALL = "CALL SP_REPORTE_PROVEEDOR_NOCOBRADO(".$asinc->id.",'REPORTE_A2',". $this->liquidacion_id.",".$this->proveedor_id.",'".$this->tipo_producto."','".$this->tipo_cuota."',NULL);";
        $oLC->query($SPCALL);
		if(!empty($oLC->getDataSource()->error)){
            $asinc->actualizar(20, 100, $oLC->getDataSource()->error);
            return;
        }


        $STOP = 0;
        $asinc->actualizar(90,100,"GENERANDO REPORTE EXCEL ...");
        $datos = $this->Temporal->leerTemporal($asinc->id);
        if(!empty($datos)){

            foreach($datos as $dato){
				if($asinc->detenido()){$STOP = 1;break;}

                $dato = $dato['AsincronoTemporal'];

                if($dato['clave_1'] == 'REPORTE_A1'){
                    $resultado = array_intersect_key($dato,$set[0]['columns']);
                    debug($resultado);
                    debug($set[0]['columns']);
                    $this->Temporal->writeXLSRow(0,$resultado);
                }
                if($dato['clave_1'] == 'REPORTE_A2'){
                    $resultado = array_intersect_key($dato,$set[1]['columns']);
                    $this->Temporal->writeXLSRow(1,$resultado);
                }

            }
        }        
        

        if($STOP == 0){
            $asinc->actualizar(98,100,"CREANDO ARCHIVO $FILE_EXCEL ...");
            $this->Temporal->saveToXLSFile($FILE_EXCEL);
            $asinc->setValue('p6',$FILE_EXCEL);        
    
            $asinc->actualizar(100,100,"FINALIZANDO...");
            $asinc->fin("**** PROCESO FINALIZADO ****");    
        }

        return;

		
		
		
	}
	//FIN PROCESO ASINCRONO
	

    function filtrar(){

    }

	
}
?>
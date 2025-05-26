<?php

/**
 * REPORTE DE CONTROL DE LA LIQUIDACION
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_control_liquidacion 205 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php procesar_asientos 8521 -app /var/www/sigem/app/
 *  D:\Desarrollo\xampp\php\php.exe D:\Desarrollo\xampp\htdocs\sigem\cake\console\cake.php procesar_asientos 4633 -app D:\Desarrollo\xampp\htdocs\sigem\app\
 *  C:\xampp\php\php.exe C:\xampp\htdocs\sigem\cake\console\cake.php procesar_asientos 39432 -app C:\xampp\htdocs\sigem\app\
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ProcesarAsientosShell extends Shell {

	var $uses = array('contabilidad.MutualProcesoAsiento'
//	, 'clientes.Recibo', 'proveedores.OrdenPago', 'contabilidad.Ejercicio'
	);
	
	var $tasks = array('Temporal');
	
	function main() {
            
            Configure::write('debug', 1);

		$oRecibo = $this->MutualProcesoAsiento->importarModelo('Recibo', 'clientes');
		$oOrdenPago = $this->MutualProcesoAsiento->importarModelo('OrdenPago', 'proveedores');
		$oEjercicio = $this->MutualProcesoAsiento->importarModelo('Ejercicio', 'contabilidad');
		
		$STOP = 0;

		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$this->Temporal->pid = $pid;

		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 18;
		$i = 0;
		
		$asinc->setTotal($total);
		$contador = 0;	
		$procesoId = $asinc->getParametro('p1');		
			
		$nLectura = 21;
		$nContando = 1;
		
		$asinc->actualizar($nContando,$nLectura,"ESPERE, LIMPIANDO TABLAS...");
		$this->LimpiarTablaAsiento($asinc->getParametro('p1'));


		$procesoAsiento = $this->MutualProcesoAsiento->read(null, $asinc->getParametro('p1'));
		$agrupar = $procesoAsiento['MutualProcesoAsiento']['agrupar'];
		
		$fecha_desde_OP = date("Y-m-d", $this->MutualProcesoAsiento->addDayToDate(strtotime($procesoAsiento['MutualProcesoAsiento']['fecha_desde']), 1));
		$fecha_desde = $procesoAsiento['MutualProcesoAsiento']['fecha_desde'];


                
		####################################################################################################
                # CONFIGURO LA CUENTA DE ANTICIPO. Las Ordenes de Pagos y Recibos hacen anticipos, esto pueden ir
                # a la cuenta contable de ANTICIPO o a la cuenta contable correspondiente al PROVEEDOR/CLIENTE.
		####################################################################################################
                $cnfCtaAnticipo = $this->MutualProcesoAsiento->getGlobalDato('LOGICO_1', 'CONTANTI');
                
		####################################################################################################
		# LEYENDO LOS RECIBOS
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO RECIBOS...");
		
		$aRecibos = array();
		$aRecibos = $oRecibo->getRecibosEntreFecha($fecha_desde_OP, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		

		####################################################################################################
		# LEYENDO COMPENSACION DE ANTICIPOS CON FACTURAS DE CLIENTES
		####################################################################################################
                $aClienteAnticipo = array();
                if(!$cnfCtaAnticipo['GlobalDato']['LOGICO_1']){
                    $asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO COMP.FACTURAS CLIENTES CON ANTICIPOS...");
		
                    $aClienteAnticipo = $this->MutualProcesoAsiento->getClienteFacturaAnticipo($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
                }
		
		####################################################################################################
		# LEYENDO LAS ORDENES DE PAGOS
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO ORDENES DE PAGOS...");
		$aOrdenPagos = array();
		$aOrdenPagos = $oOrdenPago->getOrdenPagoEntreFecha($fecha_desde_OP, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);

		####################################################################################################
		# LEYENDO COMPENSACION DE ANTICIPOS CON FACTURAS DE PROVEEDORES
		####################################################################################################
                $aFactAnticipo = array();
                if(!$cnfCtaAnticipo['GlobalDato']['LOGICO_1']){
                    $asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO COMP.FACTURAS PROV.CON ANTICIPOS...");

                    $aFactAnticipo = $this->MutualProcesoAsiento->getProveedorFacturaAnticipo($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
                }

		####################################################################################################
		# LEYENDO LOS COBROS POR LIQUIDACION
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO COBRO POR LIQUIDACION...");
		$aLiquidaciones = array();
		$aLiquidaciones = $this->MutualProcesoAsiento->getCobroLiquidacion($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);

		####################################################################################################
		# LEYENDO LOS REVERSOS
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO REVERSOS...");
		$aReverso = array();
		$aReverso = $this->MutualProcesoAsiento->getReverso($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);

		
		####################################################################################################
		# LEYENDO REVERSOS POR REINTEGROS
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO REVERSOS POR REINTEGROS...");
		$aReversoReintegros = array();
		$aReversoReintegros = $this->MutualProcesoAsiento->getReversoReintegros($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);

		
		####################################################################################################
		# LEYENDO REVERSOS POR CAJA Y BANCO
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO REVERSOS POR MOV. CAJA Y BANCO...");
		$aReversoBanco = array();
		$aReversoBanco = $this->MutualProcesoAsiento->getReversoBanco($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);


		
		####################################################################################################
		# LEYENDO LAS ORDENES DE COBRO CAJA
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO ORDENES DE COBRO CAJA...");
		$aCajaCobro = array();
		$aCajaCobro = $this->MutualProcesoAsiento->getOrdenCajaCobro($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);

                
		####################################################################################################
		# LEYENDO CANCELACIONES
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO CANCELACIONES...");
		$aCancelacionRecibos = array();
		$aCancelacionRecibos = $this->MutualProcesoAsiento->getCancelacionRecibo($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		
		
		####################################################################################################
		# LEYENDO CANCELACIONES
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO CANCELACIONES...");
		$aCancelaciones = array();
		$aCancelaciones = $this->MutualProcesoAsiento->getCancelaciones($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		
		
		####################################################################################################
		# LEYENDO SOLICITUDES APROBADAS
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO SOLICITUDES APROBADAS...");
		$aSolicitudes = array();
		$aSolicitudes = $this->MutualProcesoAsiento->getMutualSolicitudes($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		
		
		####################################################################################################
		# LEYENDO CUOTAS SOCIAL
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO CUOTA SOCIAL Y CARGO MUTUAL...");
		$aCuotas = array();
		$aCuotas = $this->MutualProcesoAsiento->getMutualCuotas($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		
		
		####################################################################################################
		# LEYENDO REINTEGROS
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO REINTEGROS...");
		$aReintegros = array();
		$aReintegros = $this->MutualProcesoAsiento->getReintegro($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		
		
		####################################################################################################
		# LEYENDO FACTURAS DE PROVEEDORES
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO FACTURAS DE PROVEEDORES...");
		$aProveedorFacturas = array();
		$aProveedorFacturas = $this->MutualProcesoAsiento->getProveedorFactura($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		
		
		####################################################################################################
		# LEYENDO COMISIONES FACTURADAS
		####################################################################################################
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO COMISIONES FACTURADAS...");
		$aClienteFacturas = array();
		$aClienteFacturas = $this->MutualProcesoAsiento->getClienteFactura($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		
		
		####################################################################################################
		# LEYENDO MOVIMIENTOS DE CAJA Y BANCO
		####################################################################################################
		$aCajaBancoInd = array();
		$aCajaBancoRel = array();
		$aCajaBancoRee = array();
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO MOVIMIENTOS DE CAJA Y BANCO...");
		$aCajaBancoInd = $this->MutualProcesoAsiento->getCajaBancoIndividual($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO MOVIMIENTOS DE CAJA Y BANCO...");
		$aCajaBancoRel = $this->MutualProcesoAsiento->getCajaBancoRelacionado($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
		$asinc->actualizar($nContando++,$nLectura,"ESPERE, LEYENDO CHEQUES REEMPLAZADOS...");
		$aCajaBancoRee = $this->MutualProcesoAsiento->getCajaBancoReemplazo($fecha_desde, $procesoAsiento['MutualProcesoAsiento']['fecha_hasta']);
				
		
/*===================================================================================*/
		$asinc->actualizar(100,100,"ESPERE, CONTANDO ...");
//		$total = count($aCajaCobro);
		// Cuento todo lo que voy a procesar
		$total = count($aRecibos) + count($aOrdenPagos) + count($aLiquidaciones) + count($aReverso) + count($aReversoReintegros) + count($aReversoBanco) +
				 count($aCajaCobro) + count($aCancelacionRecibos) + count($aCancelaciones) + count($aSolicitudes) + count($aCuotas) + count($aReintegros) +
				 count($aProveedorFacturas) + count($aClienteFacturas) + count($aCajaBancoInd) + count($aCajaBancoRel) + count($aCajaBancoRee) + 
				 count($aFactAnticipo) + count($aClienteAnticipo);

		$asinc->setTotal($total);

				 
		// ASIENTO DEVENGAMIENTO DE SOLICITUDES DE PRODUCTOS DE LA MUTUAL EN LA INSTANCIA APROBACION.
		if(!empty($aSolicitudes)):
			foreach($aSolicitudes as $solicitud):
				$this->MutualProcesoAsiento->getAsientoSolicitudes($solicitud, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO SOLICITUDES >> ");
			endforeach;
			
		endif;
		
		// ASIENTO DEVENGAMIENTO DE SOLICITUDES DE PRODUCTOS DE LA MUTUAL EN LA INSTANCIA DE LIQUIDACION.
		if(!empty($aCuotas)):
			foreach($aCuotas as $cuota):
				$this->MutualProcesoAsiento->getAsientoCuotas($cuota, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO CARGO MUTUAL >> ");
			endforeach;
			
		endif;

		// ASIENTO DE LIQUIDACION
		if(!empty($aLiquidaciones)):
//			$nIndice = 0;
//			$renglones = count($aLiquidaciones);
//			while ($nIndice < $renglones):
//				$fecha_corte = $aLiquidaciones[$nIndice]['OrdenDescuentoCobro']['fecha'];
//				$organismo = $aLiquidaciones[$nIndice]['LiquidacionCuota']['codigo_organismo'];
//				$nLiquidacion = $aLiquidaciones[$nIndice]['LiquidacionCuota']['liquidacion_id'];
//				$total_organismo = 0;
//				$temporalAsiento = array();
	
				
//				while ($nIndice < $renglones && $fecha_corte == $aLiquidaciones[$nIndice]['OrdenDescuentoCobro']['fecha'] && $organismo == $aLiquidaciones[$nIndice]['LiquidacionCuota']['codigo_organismo']):
					
//					$total_organismo += $aLiquidaciones[$nIndice][0]['importe_cobrado'];

//					$tmpAsiento = $this->MutualProcesoAsiento->getAsientoLiquiRenglon($aLiquidaciones[$nIndice]);
					
					
//					array_push($temporalAsiento, $tmpAsiento);
//					$nIndice += 1;
								
//					$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO COBRANZA >> " . $organismo);
//				endwhile;
	
//				$this->MutualProcesoAsiento->getAsientoLiquiOrganismo($organismo, $total_organismo, $temporalAsiento, $fecha_corte, $procesoId, $nLiquidacion, $agrupar);
				
//			endwhile;

                        
                    $nIndice = 0;
                    $renglones = count($aLiquidaciones);
                    $oLS = $this->MutualProcesoAsiento->importarModelo('LiquidacionSocio', 'mutual');
                    $oMutualCuentaAsiento = $this->MutualProcesoAsiento->importarModelo('MutualCuentaAsiento', 'contabilidad');
                    while ($nIndice < $renglones):
                        $fecha_corte = $aLiquidaciones[$nIndice]['OrdenDescuentoCobro']['fecha'];
                        $organismo = $aLiquidaciones[$nIndice]['LiquidacionCuota']['codigo_organismo'];
                        $nLiquidacion = $aLiquidaciones[$nIndice]['LiquidacionCuota']['liquidacion_id'];
                        $total_organismo = 0;
                        $temporalAsiento = array();


//                        while ($nIndice < $renglones && $fecha_corte == $aLiquidaciones[$nIndice]['OrdenDescuentoCobro']['fecha'] && $organismo == $aLiquidaciones[$nIndice]['LiquidacionCuota']['codigo_organismo']):
                        while ($nIndice < $renglones && $nLiquidacion == $aLiquidaciones[$nIndice]['LiquidacionCuota']['liquidacion_id']){
                            $total_organismo += $aLiquidaciones[$nIndice][0]['importe_cobrado'];

                            $tmpAsiento = $this->MutualProcesoAsiento->getAsientoLiquiRenglon($aLiquidaciones[$nIndice]);


                            array_push($temporalAsiento, $tmpAsiento);
                            $nIndice += 1;

                            $asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO COBRANZA >> " . $organismo);
                        }
                        
                        $aReintLiq = $oLS->totalReintegros($nLiquidacion);
                        if(!empty($aReintLiq)){
                            $tmpAsiento = array();
                            $total_organismo += $aReintLiq['total'] + $aReintLiq['total_anticipado'];

                            $cuentaReintegro = $oMutualCuentaAsiento->find('all', array('conditions' => array('MutualCuentaAsiento.tipo_orden_dto' => 'SOCIO','MutualCuentaAsiento.tipo_producto' => 'REINTEGRO')));
                            $tmpAsiento['MutualTemporalAsientoRenglon']['fecha'] = $fecha_corte;
                            if(empty($cuentaReintegro)){
                                $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = '';
                                $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($aReintLiq['total'] + $aReintLiq['total_anticipado']) * (-1);
                                $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'COBRO LIQUIDACION REINTEGROS # ' . $nLiquidacion;
                                $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 1;
                                $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = 'NO DEFINIDO EN LA CONFIGURACION DE ASIENTO LA CUENTA REINTEGRO'; 
                            }
                            else{
                                $coPlanCuentaId = $cuentaReintegro[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                                $tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id'] = $cuentaReintegro[0]['MutualCuentaAsiento']['co_plan_cuenta_id'];
                                $tmpAsiento['MutualTemporalAsientoRenglon']['importe'] = ($aReintLiq['total'] + $aReintLiq['total_anticipado']) * (-1);
                                $tmpAsiento['MutualTemporalAsientoRenglon']['referencia'] = 'COBRO LIQUIDACION REINTEGROS # ' . $nLiquidacion;
                                $tmpAsiento['MutualTemporalAsientoRenglon']['error'] = 0;
                                $tmpAsiento['MutualTemporalAsientoRenglon']['error_descripcion'] = ''; 
                            }
            
                            $cuenta = $this->MutualProcesoAsiento->getCuenta($tmpAsiento['MutualTemporalAsientoRenglon']['co_plan_cuenta_id']);
                            $tmpAsiento['MutualTemporalAsientoRenglon']['cuenta'] = $cuenta['cuenta'];
                            $tmpAsiento['MutualTemporalAsientoRenglon']['descripcion'] = $cuenta['descripcion'];
                            $tmpAsiento['MutualTemporalAsientoRenglon']['modulo'] = 'LIQUCOBR';
                            $tmpAsiento['MutualTemporalAsientoRenglon']['tipo_asiento'] = 0;

                            array_push($temporalAsiento, $tmpAsiento);
                        }
            
                        $this->MutualProcesoAsiento->getAsientoLiquiOrganismo($organismo, $total_organismo, $temporalAsiento, $fecha_corte, $procesoId, $nLiquidacion, $agrupar);
                       
                        $aReversoLiquidacion = $this->MutualProcesoAsiento->getReversoLiquidacion($nLiquidacion);
                        if(!empty($aReversoLiquidacion)){
                            $total_organismo = 0;
                            $temporalAsiento = array();
                            $tmpAsiento = array();
                            foreach($aReversoLiquidacion as $reverso){
                                $total_organismo += $reverso['Reverso']['importe_reversado'];

                                $tmpAsiento = $this->MutualProcesoAsiento->getAsientoReversoRenLiq($reverso);

                                array_push($temporalAsiento, $tmpAsiento);
                                
                            }
            
                            $this->MutualProcesoAsiento->getAsientoReversoOrgLiq($organismo, $total_organismo, $temporalAsiento, $fecha_corte, $procesoId, $nLiquidacion, $agrupar);
                       
                        }

                    endwhile;
                        
		endif;
		
		
		// ASIENTO REINTEGROS
		if(!empty($aReintegros)):
			foreach($aReintegros as $reintegro):
				$this->MutualProcesoAsiento->getAsientoReintegro($reintegro, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO REINTEGROS >> ");
			endforeach;
			
		endif;		
		
/*		
		// ASIENTO REVERSO
		if(!empty($aReverso)):

			$nIndice = 0;
			$renglones = count($aReverso);
			while ($nIndice < $renglones):
				$fecha_corte = $aReverso[$nIndice]['BancoCuentaMovimiento']['fecha_reverso'];
				$organismo = $aReverso[$nIndice]['PersonaBeneficio']['codigo_beneficio'];
				$total_organismo = 0;
				$temporalAsiento = array();
	
				
				while ($nIndice < $renglones && $fecha_corte == $aReverso[$nIndice]['BancoCuentaMovimiento']['fecha_reverso'] && 
						$organismo == $aReverso[$nIndice]['PersonaBeneficio']['codigo_beneficio']):
					
					$total_organismo += $aReverso[$nIndice][0]['importe_reversado'];

					$tmpAsiento = $this->MutualProcesoAsiento->getAsientoReversoRenglon($aReverso[$nIndice]);
					
					array_push($temporalAsiento, $tmpAsiento);
					$nIndice += 1;
								
					$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO REVERSOS >> ");
				endwhile;

				$this->MutualProcesoAsiento->getAsientoReversoOrganismo($organismo, $total_organismo, $temporalAsiento, $fecha_corte, $procesoId, $agrupar);
				
			endwhile;
			
		endif;
*/		
		
		// ASIENTO REVERSO DEL REINTEGRO
		if(!empty($aReversoReintegros)):
			foreach($aReversoReintegros as $aReverso):
				$this->MutualProcesoAsiento->getAsientoReversoReintegro($aReverso, $procesoId, $agrupar);
								
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO REVERSOS DE REINTEGROS >> ");
			endforeach;
			
		endif;
				
		// ASIENTO REVERSO CAJA/BANCO
		if(!empty($aReversoBanco)):
			$nIndice = 0;
			$renglones = count($aReversoBanco);
			while ($nIndice < $renglones):
				$bancoId = $aReversoBanco[$nIndice][0]['banco_cuenta_movimiento_id'];
				$fecha_corte = $aReversoBanco[$nIndice][0]['fecha_reverso'];
				$co_plan_cuenta_id = $aReversoBanco[$nIndice][0]['co_plan_cuenta_id'];
				$total_banco = 0;
				$temporalAsiento = array();
	
				
				while ($nIndice < $renglones && $bancoId == $aReversoBanco[$nIndice][0]['banco_cuenta_movimiento_id']):
					
					$total_banco += $aReversoBanco[$nIndice][0]['importe_reversado'];

					$glb = $this->MutualProcesoAsiento->getAsientoRevBcoOrganismo($aReversoBanco[$nIndice]);
				
					$nIndice += 1;			
					$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO REVERSOS DE CAJA Y BANCO >> ");
				endwhile;
	
				$this->MutualProcesoAsiento->getAsientoRevBcoRenglon($co_plan_cuenta_id, $total_banco, $fecha_corte, $glb, $procesoId, $agrupar);
				
			endwhile;
			
			
		endif;
		
		
		// ASIENTO DE ORDEN COBRO CAJA
		if(!empty($aCajaCobro)):
			foreach($aCajaCobro as $aCobro):
				$this->MutualProcesoAsiento->getAsientoCajaCobro($aCobro, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO COBRO DE CAJA >> ");
			endforeach;
			
		endif;
		
		// ASIENTO DE CANCELACIONES
		if(!empty($aCancelacionRecibos)):
			foreach($aCancelacionRecibos as $aCancelacion):
				$this->MutualProcesoAsiento->getAsientoCancelacionRecibo($aCancelacion, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO CANCELACIONES >> ");
			endforeach;
			
		endif;
		
		// ASIENTO DE CANCELACIONES
		
		if(!empty($aCancelaciones)):
			foreach($aCancelaciones as $aCancelacion):
				if($aCancelacion[0]['credito'] == 0):
					$this->MutualProcesoAsiento->getAsientoCancelacionRecibo($aCancelacion, $procesoId, $agrupar);
				else:
					if($aCancelacion['CancelacionOrden']['orden_proveedor_id'] == MUTUALPROVEEDORID):
						$this->MutualProcesoAsiento->getAsientoCancelaciones($aCancelacion, $procesoId, $agrupar);
					endif;
				endif;
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO CANCELACIONES >> ");
			endforeach;
			
		endif;
		
		// ASIENTO PROVEEDORES
		if(!empty($aProveedorFacturas)):
			foreach($aProveedorFacturas as $factura):
				$this->MutualProcesoAsiento->getAsientoProveedorFactura($factura, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO PROVEEDORES >> ");
			endforeach;
			
		endif;
		
		
		// ASIENTO CLIENTES
		if(!empty($aClienteFacturas)):
			foreach($aClienteFacturas as $factura):
				$this->MutualProcesoAsiento->getAsientoClienteFactura($factura, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO COMISIONES DE COMERCIOS >> ");
			endforeach;
			
		endif;
		
		
		// ASIENTO CAJA Y BANCO
		if(!empty($aCajaBancoInd)):
			foreach($aCajaBancoInd as $cajaBanco):
				$this->MutualProcesoAsiento->getAsientoCajaBancoIndividual($cajaBanco, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO CAJA Y BANCO >> ");
			endforeach;
			
		endif;
		
		if(!empty($aCajaBancoRel)):
			foreach($aCajaBancoRel as $cajaBanco):
				$this->MutualProcesoAsiento->getAsientoCajaBancoRelacionado($cajaBanco, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO CAJA Y BANCO >> ");
			endforeach;
			
		endif;
		
		if(!empty($aCajaBancoRee)):
			foreach($aCajaBancoRee as $cajaBanco):
				$this->MutualProcesoAsiento->getAsientoCajaBancoReemplazar($cajaBanco, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO CHEQUES REEMPLAZADOS >> ");
			endforeach;
			
		endif;
		
		
		// ASIENTO RECIBOS DE INGRESOS
		if(!empty($aRecibos)):
			foreach($aRecibos as $recibo):
				$this->MutualProcesoAsiento->getAsientoRecibo($recibo, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO RECIBOS >> " . $recibo['Recibo']['sucursal'] . '-' . $recibo['Recibo']['nro_recibo']);
			endforeach;
			
		endif;
		
		// ASIENTO COMPENSACION DE FACTURAS DE CLIENTES CON ANTICIPOS DE RECIBOS
		if(!empty($aClienteAnticipo)):
			foreach($aClienteAnticipo as $factura):
				$this->MutualProcesoAsiento->getAsientoClienteFacturaAnticipo($factura, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO COMP.FACT.CLIENTE C/ANTIC.RECIBOS >> " . $factura['Cliente']['razon_social']);
			endforeach;
			
		endif;
		
		// ASIENTO ORDEN DE PAGO DE EGRESOS
		if(!empty($aOrdenPagos)):
			foreach($aOrdenPagos as $OrdenPago):
				$this->MutualProcesoAsiento->getAsientoOPago($OrdenPago, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO ORDENES PAGOS >> " . $OrdenPago['OrdenPago']['sucursal'] . '-' . $OrdenPago['OrdenPago']['nro_orden_pago']);
			endforeach;
			
		endif;
		
		// ASIENTO COMPENSACION DE FACTURAS DE PROVEEDORES CON ANTICIPOS DE ORDEN DE PAGOS
		if(!empty($aFactAnticipo)):
			foreach($aFactAnticipo as $factura):
				$this->MutualProcesoAsiento->getAsientoProveedorFacturaAnticipo($factura, $procesoId, $agrupar);
				$asinc->actualizar($contador++,$total,"$contador / $total - PROCESANDO COMP.FACT.PROV. C/ANTIC.ORDENES PAGOS >> " . $factura['Proveedor']['razon_social']);
			endforeach;
			
		endif;
		
		if($agrupar != 0):
			$this->MutualProcesoAsiento->grabarAsientoAgrupados($procesoId, $agrupar, $pid);
		endif;
		
		
/*===================================================================================*/
				
		if($STOP == 0){
			$asinc->actualizar($total,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}			
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	function LimpiarTablaAsiento($procesoId){
		$sql = "DELETE FROM mutual_asientos WHERE mutual_proceso_asiento_id = '$procesoId'";
		$this->MutualProcesoAsiento->query($sql);
		
		$sql = "DELETE FROM mutual_asiento_renglones WHERE mutual_proceso_asiento_id = '$procesoId'";
		$this->MutualProcesoAsiento->query($sql);
		
		$sqlTemporal = "DELETE FROM mutual_temporal_asiento_renglones";
		$this->MutualProcesoAsiento->query($sqlTemporal);
	}
		
		
		
}
?>
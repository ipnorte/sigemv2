<?php

/**
 * 
 * @author GUSTAVO LUJAN
 * @package shells
 * @subpackage background-execute
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php agrupar_asientos 43 -app /home/adrian/dev/www/sigem/app/
 *  C:\xampp\php\php.exe C:\xampp\htdocs\sigem\cake\console\cake.php agrupar_asientos 30665 -app C:\xampp\htdocs\sigem\app\ > C:\xampp\php\debug.txt
 * 
 * 
 */

class AgruparAsientosShell extends Shell {

	var $ejercicioId;
	var $fecha_cierre;
	var $ejercicio;
	var $asientos;
	var $lError = false;
	
	var $uses = array('contabilidad.Asiento','contabilidad.Ejercicio', 'contabilidad.PlanCuenta', 'contabilidad.AsientoRenglon');
	var $tasks = array('Temporal');
	
	function main() {
                $temporal = array();
                $tmpRenglon = array();
                
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$this->ejercicioId = $asinc->getParametro('p1');
		$this->ejercicio = $this->Ejercicio->getEjercicio($this->ejercicioId);		
		
		
		$this->Temporal->limpiarTabla($asinc->id);

		$this->Temporal->pid = $pid;
		
		$temp = array();
		$temp1 = array();
		
		$nro_asiento = 0;
		$i = 0;
		
		$asinc->actualizar(1,3,"PREPARANDO ASIENTO RENGLONES >>> ");
		$ejercicio_id = $this->ejercicioId;

                $sqlRenglones = "UPDATE co_asiento_renglones set importe = debe WHERE co_asiento_id in(SELECT id FROM co_asientos WHERE co_ejercicio_id = '$ejercicio_id') AND debe > 0";
                $this->AsientoRenglon->query($sqlRenglones);
                
		$asinc->actualizar(2,3,"PREPARANDO ASIENTO RENGLONES >>> ");

                $sqlRenglones = "UPDATE co_asiento_renglones set importe = haber * (-1) WHERE co_asiento_id in(SELECT id FROM co_asientos WHERE co_ejercicio_id = '$ejercicio_id') AND haber > 0";
                $this->AsientoRenglon->query($sqlRenglones);
                
		$asinc->actualizar(3,3,"LEYENDO ASIENTOS >>> ");
//                $sqlNroUno = "SELECT * FROM co_asientos Asiento WHERE co_ejercicio_id = '$ejercicio_id' AND nro_asiento = 1";
                $sqlNroUno = "SELECT * FROM co_asientos Asiento WHERE co_ejercicio_id = '$ejercicio_id' AND tipo = 1 AND borrado = 0";
                $asientoNroUno = $this->Asiento->query($sqlNroUno);
                
//                $sqlAgrupado = "SELECT * FROM co_asientos Asiento WHERE co_ejercicio_id = '$ejercicio_id' AND nro_asiento != 1 AND borrado = 0 GROUP BY fecha";
                $sqlAgrupado = "SELECT * FROM co_asientos Asiento WHERE co_ejercicio_id = '$ejercicio_id' AND tipo = 2 AND borrado = 0 GROUP BY fecha";
                $asientoAgrupados = $this->Asiento->query($sqlAgrupado);
                
                $sqlRefundicion = "SELECT * FROM co_asientos Asiento WHERE co_ejercicio_id = '$ejercicio_id' AND tipo = 3 AND borrado = 0";
                $asientoRefundicion = $this->Asiento->query($sqlRefundicion);
                
                $sqlCierre = "SELECT * FROM co_asientos Asiento WHERE co_ejercicio_id = '$ejercicio_id' AND tipo = 4 AND borrado = 0";
                $asientoCierre = $this->Asiento->query($sqlCierre);
                
		$total = count($asientoAgrupados) + 3;
		$asinc->setTotal($total);
		$nroAst = 1;

		$asinc->actualizar($i,$total,"$i / $total - ASIENTO NRO.: $i >>> ");

                $temporal['AsincronoTemporal']['asincrono_id'] = $asinc->id;
                $temporal['AsincronoTemporal']['entero_1'] = $nroAst;
                $temporal['AsincronoTemporal']['texto_1'] = date('d-m-Y', strtotime($asientoNroUno[0]['Asiento']['fecha']));
                
                if(!$this->Temporal->grabar($temporal)):
                    $asinc->actualizar(50, 100, "**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    $this->Temporal->setErrorMsg("**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    return;
                    
                endif;
                
                $tmpId = $this->Temporal->temporalID;
                $sqlAstRenglon = "SELECT PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.* FROM co_asiento_renglones AsientoRenglon, co_plan_cuentas PlanCuenta "
                        . "WHERE AsientoRenglon.co_asiento_id = " . $asientoNroUno[0]['Asiento']['id']
                        . " AND AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id "
                        . "ORDER BY PlanCuenta.cuenta";
                
                $asientoRenglon = $this->Asiento->query($sqlAstRenglon);
                
                $totDebe = 0;
                $totHaber = 0;
                foreach ($asientoRenglon as $renglon):
                    if($renglon['AsientoRenglon']['importe'] > 0):
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_temporal_id'] = $tmpId;
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_1'] = $renglon['PlanCuenta']['cuenta'];
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_2'] = $renglon['PlanCuenta']['descripcion'];
                        $tmpRenglon['AsincronoTemporalDetalle']['entero_1'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                        $tmpRenglon['AsincronoTemporalDetalle']['decimal_1'] = $renglon['AsientoRenglon']['importe'];
                        if(!$this->Temporal->grabarTemporalDetalle($tmpRenglon)):
                            $asinc->actualizar(50, 100, "**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            $this->Temporal->setErrorMsg("**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            return;
                        endif;
                        $totDebe += $renglon['AsientoRenglon']['importe'];
                    endif;
                endforeach;

                $tmpRenglon = array();
                foreach ($asientoRenglon as $renglon):
                    if($renglon['AsientoRenglon']['importe'] < 0):
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_temporal_id'] = $tmpId;
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_1'] = $renglon['PlanCuenta']['cuenta'];
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_2'] = $renglon['PlanCuenta']['descripcion'];
                        $tmpRenglon['AsincronoTemporalDetalle']['entero_1'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                        $tmpRenglon['AsincronoTemporalDetalle']['decimal_2'] = $renglon['AsientoRenglon']['importe'] * (-1);
                        if(!$this->Temporal->grabarTemporalDetalle($tmpRenglon)):
                            $asinc->actualizar(50, 100, "**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            $this->Temporal->setErrorMsg("**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            return;
                        endif;
                        $totHaber += ($renglon['AsientoRenglon']['importe'] * -1);
                    endif;
                endforeach;

                $temporal['AsincronoTemporal']['decimal_1'] = $totDebe;
                $temporal['AsincronoTemporal']['decimal_2'] = $totHaber;
                $temporal['AsincronoTemporal']['id'] = $tmpId;
                if(!$this->Temporal->grabar($temporal)):
                    $asinc->actualizar(50, 100, "**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    $this->Temporal->setErrorMsg("**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    return;
                    
                endif;
                
                /*
                 * AGRUPACION ASIENTO DE TIPO 2 COMUNES
                 */
                foreach ($asientoAgrupados as $asiento):
                    $temporal = array();
                    $tmpRenglon = array();
                    $nroAst += 1;
                    $asinc->actualizar($nroAst,$total,"$nroAst / $total - ASIENTO NRO.: $nroAst >>> ");
                    
                    $temporal['AsincronoTemporal']['asincrono_id'] = $asinc->id;
                    $temporal['AsincronoTemporal']['entero_1'] = $nroAst;
                    $temporal['AsincronoTemporal']['texto_1'] = date('d-m-Y', strtotime($asiento['Asiento']['fecha']));
                
                    if(!$this->Temporal->grabar($temporal)):
                        $asinc->actualizar(50, 100, "**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                        $this->Temporal->setErrorMsg("**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                        return;

                    endif;

                    $tmpId = $this->Temporal->temporalID;
                    /*
                     * Ahora se diferencia por tipo de asiento.
                     * 
                    $sqlAstRenglon = "SELECT PlanCuenta.cuenta, PlanCuenta.descripcion, SUM(AsientoRenglon.importe) as t_importe, AsientoRenglon.* FROM co_asiento_renglones AsientoRenglon, co_plan_cuentas PlanCuenta "
                            . "WHERE AsientoRenglon.fecha = '" . $asiento['Asiento']['fecha'] . "' AND AsientoRenglon.co_asiento_id != " . $asientoNroUno[0]['Asiento']['id']
                            . " AND AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id "
                            . "GROUP BY AsientoRenglon.co_plan_cuenta_id "
                            . "ORDER BY PlanCuenta.cuenta";
                     */

                    $sqlAstRenglon = "SELECT PlanCuenta.cuenta, PlanCuenta.descripcion, SUM(AsientoRenglon.importe) AS t_importe, AsientoRenglon.* "
                            . "FROM co_asiento_renglones AsientoRenglon, co_plan_cuentas PlanCuenta, co_asientos Asiento "
                            . "WHERE Asiento.fecha = '" . $asiento['Asiento']['fecha'] . "' AND AsientoRenglon.co_asiento_id = Asiento.id AND Asiento.tipo = 2 "
                            . "AND AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id "
                            . "GROUP BY AsientoRenglon.co_plan_cuenta_id "
                            . "ORDER BY PlanCuenta.cuenta";
                    
                    $asientoRenglon = $this->Asiento->query($sqlAstRenglon);

                    $totDebe = 0;
                    $totHaber = 0;
                    foreach ($asientoRenglon as $renglon):
                        if($renglon[0]['t_importe'] > 0):
                            $tmpRenglon['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
                            $tmpRenglon['AsincronoTemporalDetalle']['asincrono_temporal_id'] = $tmpId;
                            $tmpRenglon['AsincronoTemporalDetalle']['texto_1'] = $renglon['PlanCuenta']['cuenta'];
                            $tmpRenglon['AsincronoTemporalDetalle']['texto_2'] = $renglon['PlanCuenta']['descripcion'];
                            $tmpRenglon['AsincronoTemporalDetalle']['entero_1'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                            $tmpRenglon['AsincronoTemporalDetalle']['decimal_1'] = $renglon[0]['t_importe'];
                            if(!$this->Temporal->grabarTemporalDetalle($tmpRenglon)):
                                $asinc->actualizar(50, 100, "**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                                $this->Temporal->setErrorMsg("**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                                return;
                            endif;
                            $totDebe += $renglon[0]['t_importe'];
                        endif;
                    endforeach;

                    $tmpRenglon = array();
                    foreach ($asientoRenglon as $renglon):
                        if($renglon[0]['t_importe'] < 0):
                            $tmpRenglon['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
                            $tmpRenglon['AsincronoTemporalDetalle']['asincrono_temporal_id'] = $tmpId;
                            $tmpRenglon['AsincronoTemporalDetalle']['texto_1'] = $renglon['PlanCuenta']['cuenta'];
                            $tmpRenglon['AsincronoTemporalDetalle']['texto_2'] = $renglon['PlanCuenta']['descripcion'];
                            $tmpRenglon['AsincronoTemporalDetalle']['entero_1'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                            $tmpRenglon['AsincronoTemporalDetalle']['decimal_2'] = $renglon[0]['t_importe'] * (-1);
                            if(!$this->Temporal->grabarTemporalDetalle($tmpRenglon)):
                                $asinc->actualizar(50, 100, "**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                                $this->Temporal->setErrorMsg("**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                                return;
                            endif;
                            $totHaber += ($renglon[0]['t_importe'] * -1);
                        endif;
                    endforeach;

                    $temporal['AsincronoTemporal']['decimal_1'] = $totDebe;
                    $temporal['AsincronoTemporal']['decimal_2'] = $totHaber;
                    $temporal['AsincronoTemporal']['id'] = $tmpId;
                    if(!$this->Temporal->grabar($temporal)):
                        $asinc->actualizar(50, 100, "**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                        $this->Temporal->setErrorMsg("**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                        return;
                    endif;
                

		endforeach;
                /*
                 * ASIENTO DE REFUNDICION DE CUENTAS DE RESULTADO.
                 */
                $nroAst += 1;
                $asinc->actualizar($nroAst,$total,"$nroAst / $total - ASIENTO NRO.: $nroAst >>> ");
                $temporal = array();
                
                $temporal['AsincronoTemporal']['asincrono_id'] = $asinc->id;
                $temporal['AsincronoTemporal']['entero_1'] = $nroAst;
                $temporal['AsincronoTemporal']['texto_1'] = date('d-m-Y', strtotime($asientoRefundicion[0]['Asiento']['fecha']));
                
                if(!$this->Temporal->grabar($temporal)):
                    $asinc->actualizar(50, 100, "**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    $this->Temporal->setErrorMsg("**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    return;
                    
                endif;
                
                $tmpId = $this->Temporal->temporalID;
                $sqlAstRenglon = "SELECT PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.* FROM co_asiento_renglones AsientoRenglon, co_plan_cuentas PlanCuenta "
                        . "WHERE AsientoRenglon.co_asiento_id = " . $asientoRefundicion[0]['Asiento']['id']
                        . " AND AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id "
                        . "ORDER BY PlanCuenta.cuenta";
                
                $asientoRenglon = $this->Asiento->query($sqlAstRenglon);

                $totDebe = 0;
                $totHaber = 0;
                $tmpRenglon = array();
                foreach ($asientoRenglon as $renglon):
                    if($renglon['AsientoRenglon']['importe'] > 0):
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_temporal_id'] = $tmpId;
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_1'] = $renglon['PlanCuenta']['cuenta'];
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_2'] = $renglon['PlanCuenta']['descripcion'];
                        $tmpRenglon['AsincronoTemporalDetalle']['entero_1'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                        $tmpRenglon['AsincronoTemporalDetalle']['decimal_1'] = $renglon['AsientoRenglon']['importe'];
                        if(!$this->Temporal->grabarTemporalDetalle($tmpRenglon)):
                            $asinc->actualizar(50, 100, "**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            $this->Temporal->setErrorMsg("**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            return;
                        endif;
                        $totDebe += $renglon['AsientoRenglon']['importe'];
                    endif;
                endforeach;

                $tmpRenglon = array();
                foreach ($asientoRenglon as $renglon):
                    if($renglon['AsientoRenglon']['importe'] < 0):
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_temporal_id'] = $tmpId;
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_1'] = $renglon['PlanCuenta']['cuenta'];
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_2'] = $renglon['PlanCuenta']['descripcion'];
                        $tmpRenglon['AsincronoTemporalDetalle']['entero_1'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                        $tmpRenglon['AsincronoTemporalDetalle']['decimal_2'] = $renglon['AsientoRenglon']['importe'] * (-1);
                        if(!$this->Temporal->grabarTemporalDetalle($tmpRenglon)):
                            $asinc->actualizar(50, 100, "**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            $this->Temporal->setErrorMsg("**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            return;
                        endif;
                        $totHaber += ($renglon['AsientoRenglon']['importe'] * -1);
                    endif;
                endforeach;

                $temporal['AsincronoTemporal']['decimal_1'] = $totDebe;
                $temporal['AsincronoTemporal']['decimal_2'] = $totHaber;
                $temporal['AsincronoTemporal']['id'] = $tmpId;
                if(!$this->Temporal->grabar($temporal)):
                    $asinc->actualizar(50, 100, "**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    $this->Temporal->setErrorMsg("**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    return;
                    
                endif;
                
		
                /*
                 * ASIENTO DE CIERRE.
                 */
                $nroAst += 1;
                $asinc->actualizar($nroAst,$total,"$nroAst / $total - ASIENTO NRO.: $nroAst >>> ");

                $temporal = array();
                
                $temporal['AsincronoTemporal']['asincrono_id'] = $asinc->id;
                $temporal['AsincronoTemporal']['entero_1'] = $nroAst;
                $temporal['AsincronoTemporal']['texto_1'] = date('d-m-Y', strtotime($asientoCierre[0]['Asiento']['fecha']));
                
                if(!$this->Temporal->grabar($temporal)):
                    $asinc->actualizar(50, 100, "**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    $this->Temporal->setErrorMsg("**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    return;
                    
                endif;
                
                $tmpId = $this->Temporal->temporalID;
                $sqlAstRenglon = "SELECT PlanCuenta.cuenta, PlanCuenta.descripcion, AsientoRenglon.* FROM co_asiento_renglones AsientoRenglon, co_plan_cuentas PlanCuenta "
                        . "WHERE AsientoRenglon.co_asiento_id = " . $asientoCierre[0]['Asiento']['id']
                        . " AND AsientoRenglon.co_plan_cuenta_id = PlanCuenta.id "
                        . "ORDER BY PlanCuenta.cuenta";
                
                $asientoRenglon = $this->Asiento->query($sqlAstRenglon);
                
                $totDebe = 0;
                $totHaber = 0;
                $tmpRenglon = array();
                foreach ($asientoRenglon as $renglon):
                    if($renglon['AsientoRenglon']['importe'] > 0):
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_temporal_id'] = $tmpId;
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_1'] = $renglon['PlanCuenta']['cuenta'];
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_2'] = $renglon['PlanCuenta']['descripcion'];
                        $tmpRenglon['AsincronoTemporalDetalle']['entero_1'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                        $tmpRenglon['AsincronoTemporalDetalle']['decimal_1'] = $renglon['AsientoRenglon']['importe'];
                        if(!$this->Temporal->grabarTemporalDetalle($tmpRenglon)):
                            $asinc->actualizar(50, 100, "**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            $this->Temporal->setErrorMsg("**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            return;
                        endif;
                        $totDebe += $renglon['AsientoRenglon']['importe'];
                    endif;
                endforeach;

                $tmpRenglon = array();
                foreach ($asientoRenglon as $renglon):
                    if($renglon['AsientoRenglon']['importe'] < 0):
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_id'] = $asinc->id;
                        $tmpRenglon['AsincronoTemporalDetalle']['asincrono_temporal_id'] = $tmpId;
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_1'] = $renglon['PlanCuenta']['cuenta'];
                        $tmpRenglon['AsincronoTemporalDetalle']['texto_2'] = $renglon['PlanCuenta']['descripcion'];
                        $tmpRenglon['AsincronoTemporalDetalle']['entero_1'] = $renglon['AsientoRenglon']['co_plan_cuenta_id'];
                        $tmpRenglon['AsincronoTemporalDetalle']['decimal_2'] = $renglon['AsientoRenglon']['importe'] * (-1);
                        if(!$this->Temporal->grabarTemporalDetalle($tmpRenglon)):
                            $asinc->actualizar(50, 100, "**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            $this->Temporal->setErrorMsg("**** ERROR EN GRABAR RENGLON ASIENTO NRO.: $nroAst ****");
                            return;
                        endif;
                        $totHaber += ($renglon['AsientoRenglon']['importe'] * -1);
                    endif;
                endforeach;

                $temporal['AsincronoTemporal']['decimal_1'] = $totDebe;
                $temporal['AsincronoTemporal']['decimal_2'] = $totHaber;
                $temporal['AsincronoTemporal']['id'] = $tmpId;
                if(!$this->Temporal->grabar($temporal)):
                    $asinc->actualizar(50, 100, "**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    $this->Temporal->setErrorMsg("**** ERROR EN GRABAR CABECERA ASIENTO NRO.: $nroAst ****");
                    return;
                    
                endif;
			
	$asinc->fin("**** PROCESO FINALIZADO SIN ERRORES ****");	
	}
	//FIN PROCESO ASINCRONO
	

}
?>
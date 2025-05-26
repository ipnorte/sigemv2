<?php

/**
 * REPORTE DE CONTROL DE LA LIQUIDACION
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php aprobar_asientos 205 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /var/www/sigem/cake/console/cake.php aprobar_asientos 4633 -app /var/www/sigem/app/
 * E:\Desarrollo\xampp\php\php.exe E:\Desarrollo\xampp\htdocs\sigem\cake\console\cake.php aprobar_asientos 10540 -app E:\Desarrollo\xampp\htdocs\sigem\app\
 * 
 * @author GUSTAVO LUJAN
 * @package shells
 * @subpackage background-execute
 *
 */

class AprobarAsientosShell extends Shell {

	var $uses = array(
						'contabilidad.MutualProcesoAsiento'
//	, 'clientes.Recibo', 'proveedores.OrdenPago', 'contabilidad.Ejercicio'
	);
	
	var $tasks = array('Temporal');
	
	function main() {
            $oMutualAsiento = $this->MutualProcesoAsiento->importarModelo('MutualAsiento', 'contabilidad');
            $oMutualAsientoRenglon = $this->MutualProcesoAsiento->importarModelo('MutualAsientoRenglon', 'contabilidad');
            $oAsiento = $this->MutualProcesoAsiento->importarModelo('Asiento', 'contabilidad');
            $oAsientoRenglon = $this->MutualProcesoAsiento->importarModelo('AsientoRenglon', 'contabilidad');
            $oEjercicio = $this->MutualProcesoAsiento->importarModelo('Ejercicio', 'contabilidad');


            if(empty($this->args[0])){
                $this->out("ERROR: PID NO ESPECIFICADO");
                return;
            }

            $pid = $this->args[0];

            $asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
            $asinc->id = $pid; 

            $this->Temporal->pid = $pid;

            $user_created = $asinc->getPropietario();
            $asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
            $STOP = 0;
            $procesoId = $asinc->getParametro('p1');		

            $asientos = $oMutualAsiento->find('all', array('conditions' => array('MutualAsiento.mutual_proceso_asiento_id' => $procesoId, 'MutualAsiento.co_asiento_id' => 0), 'order' => array('MutualAsiento.fecha')));


            $total = count($asientos);


            $asinc->setTotal($total);


            $aMutualProcesoAsiento = $this->MutualProcesoAsiento->read(null, $procesoId);
            $ejercicioId = $aMutualProcesoAsiento['MutualProcesoAsiento']['co_ejercicio_id'];
            $aEjercicio = $oEjercicio->read(null, $ejercicioId);


            $contador = 0;
            $nro_asiento_contador = $nro_asiento - 1;
            $gravoOK = true;
            $unLookOk = true;
            $STOP = 0;


            foreach($asientos as $asiento):
                if($asinc->detenido()):
                        $STOP = 1;
                        break;
                endif;				

                $contador += 1;

                if($asiento['MutualAsiento']['co_asiento_id'] == 0):
                    while (true):
                        $nro_asiento = $this->MutualProcesoAsiento->getNumeroAsiento($ejercicioId);
                        if($nro_asiento == 0):
                            $asinc->actualizar($contador,$total,"$contador / $total - CONTADOR DE ASIENTO BLOQUEADO POR OTRO USUARIO ...");
                        else:
                            $date = date('Y-m-d H:i:s');
                            $mutualAsientoId = $asiento['MutualAsiento']['id'];
                            $sqlInsert = "INSERT INTO co_asientos 
                                                (mutual_asiento_id, nro_asiento, co_ejercicio_id, fecha, tipo_documento, nro_documento, 
                                                referencia, debe, haber, user_created, created, modified
                                                )
                                        SELECT 	id, '$nro_asiento' AS nro_asiento, '$ejercicioId' AS co_ejercicio_id, fecha, 
                                                tipo_documento, nro_documento, referencia, debe, haber, 
                                                '$user_created' AS user_created, '$date' AS created, '$date' AS modified
                                        FROM 
                                                mutual_asientos 
                                        WHERE	id = '$mutualAsientoId'
                                        ";

                            $this->MutualProcesoAsiento->begin();
                            $this->MutualProcesoAsiento->query($sqlInsert);
                            $asientoId = $this->lastInsertID();
                            if($asientoId > 0):
                                $date = date('Y-m-d H:i:s');
                                $insertRenglon = "INSERT INTO co_asiento_renglones
                                                    (co_asiento_id, fecha, co_plan_cuenta_id, referencia, 
                                                    debe, haber, user_created, created, modified
                                                    )
                                        SELECT 	'$asientoId' AS co_asiento_id, fecha, co_plan_cuenta_id, referencia, debe, 
                                                    haber, '$user_created' AS user_created, '$date' AS created, '$date' AS modified
                                        FROM 
                                                    mutual_asiento_renglones
                                        WHERE	mutual_asiento_id = '$mutualAsientoId'
                                ";
                                $this->MutualProcesoAsiento->query($insertRenglon);

                                $asiento['MutualAsiento']['co_asiento_id'] = $asientoId;
                                $asiento['MutualAsiento']['nro_asiento'] = $nro_asiento;
                                $asiento['MutualAsiento']['co_ejercicio_id'] = $ejercicioId;
                                if($oMutualAsiento->save($asiento)):
                                    $this->MutualProcesoAsiento->commit();
                                    $this->MutualProcesoAsiento->putNumeroAsiento($ejercicioId, 1);
                                    $asinc->actualizar($contador,$total,"$contador / $total - PROCESANDO ASIENTO NRO.: $nro_asiento");
                                else:
                                    $gravoOK = false;
                                    $this->MutualProcesoAsiento->rollback();
                                    $asinc->actualizar($contador,$total,"$contador / $total - NO SE GRAVO ASIENTO NRO.: $nro_asiento");
                                    if($oEjercicio->unLookRegistro($ejercicioId)):
                                        $unLookOk = true;
                                    else:
                                        $unLookOk = false;
                                    endif;
                                endif;
                            else:
                                $gravoOK = false;
                                $this->MutualProcesoAsiento->rollback();
                                $asinc->actualizar($contador,$total,"$contador / $total - NO SE GRAVO CABECERA DE ASIENTO NRO.: $nro_asiento");
                                if($oEjercicio->unLookRegistro($ejercicioId)):
                                    $unLookOk = true;
                                else:
                                    $unLookOk = false;
                                endif;
                            endif;

                            break;

                        endif;
                    endwhile;
                else:
                    $nro_asiento = $asiento['MutualAsiento']['nro_asiento'];
                    $asinc->actualizar($contador,$total,"$contador / $total - PROCESANDO ASIENTO NRO.: $nro_asiento");
                endif;

                if(!$unLookOk) break;

            endforeach;

            if($STOP == 1):
                return;
            endif;

            if(!$unLookOk):
                $asinc->actualizar($contador,$total,"NO SE PUDO DESBLOQUEAR EL CONTADOR DE ASIENTOS ...");
                $gravoOK = false;
            endif;

            if($gravoOK):
                $this->MutualProcesoAsiento->id = $aMutualProcesoAsiento['MutualProcesoAsiento']['id'];
                if(!$this->MutualProcesoAsiento->saveField('cerrado', 1)) $gravoOK = false;

                if($gravoOK):
                    $oEjercicio->id = $ejercicioId;
                    if(!$oEjercicio->saveField('fecha_proceso', $aMutualProcesoAsiento['MutualProcesoAsiento']['fecha_hasta'])):
                        $this->MutualProcesoAsiento->id = $aMutualProcesoAsiento['MutualProcesoAsiento']['id'];
                        $this->MutualProcesoAsiento->saveField('cerrado', 0);
                        $gravoOK = false;
                    endif;
                endif;
            else:
// 			$asinc->actualizar($total,$total,"EL PROCESO NO TERMINO CORRECTAMENTE ...");
                return;
            endif;

            if($gravoOK):
                $asinc->actualizar($total,$total,"FINALIZANDO...");
                $asinc->fin("**** PROCESO FINALIZADO ****");
            endif;		


            return;		
	}
	//FIN PROCESO ASINCRONO
	

	function lastInsertID(){
            $sqlId = "SELECT LAST_INSERT_ID() AS insertID";
            $id = $this->MutualProcesoAsiento->query($sqlId);

            if (!empty($id[0]) && isset($id[0][0]['insertID'])) {
                    return $id[0][0]['insertID'];
            }

            return 0;
	}
}
?>
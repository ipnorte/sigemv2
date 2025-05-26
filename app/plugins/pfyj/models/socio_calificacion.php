<?php
class SocioCalificacion extends PfyjAppModel{
	
	var $name = "SocioCalificacion";
	var $belongsTo = array('Socio');
	
	/**
	 * genera una nueva calificacion para el socio
	 * @param $socio_id
	 * @param $calificacion
	 * @return unknown_type
	 */
	function calificar($socio_id,$calificacion,$persona_beneficio_id=NULL,$periodoCalifica=null,$fecha_calificacion=null,$prioritaria=FALSE){
		$periodoCalifica = (empty($periodoCalifica) ? date('Ym') : $periodoCalifica);
		
//		$data = array('SocioCalificacion' => array('socio_id' => $socio_id,'periodo' => $periodoCalifica,'calificacion' => $calificacion,'persona_beneficio_id' => $persona_beneficio_id));
		$data = array();
		$data['SocioCalificacion']['id'] = 0;
		$data['SocioCalificacion']['socio_id'] = $socio_id;
		$data['SocioCalificacion']['calificacion'] = $calificacion;
		$data['SocioCalificacion']['periodo'] = $periodoCalifica;
		$data['SocioCalificacion']['persona_beneficio_id'] = $persona_beneficio_id;
                $data['SocioCalificacion']['prioritaria'] = $prioritaria;
		if (!empty($fecha_calificacion)) {
                    $data['SocioCalificacion']['created'] = $fecha_calificacion;
                }
                $ultima = $this->ultimaCalificacion($socio_id,NULL,FALSE,TRUE);

                if(parent::save($data)){
                        #actualizar siempre y cuando el periodo de la ultima calificacion sea menor al actual
                        if($ultima[2] <= $periodoCalifica || $prioritaria){
                            #actualizo la cabecera con la ultima calificacion
                            $this->Socio->id = $socio_id;
                            $this->Socio->saveField('calificacion',$calificacion);
                            $this->Socio->saveField('fecha_calificacion',(empty($fecha_calificacion) ? date('Y-m-d') : $fecha_calificacion));
                        }
//			$log = new File(LOGS. 'ADRIAN.log', true);
//			$log->append("SOCIO\t$socio_id\t$calificacion\t$fecha_calificacion\n\r");
		}
		return $this->getLastInsertID();
	}
	

	/**
	 * Ultima Calificacion del Socio
	 * @param unknown_type $socio_id
	 * @param unknown_type $persona_beneficio_id
	 * @param unknown_type $toString
	 * @param unknown_type $incluyeFecha
	 * @param unknown_type $noEnviaDiskette
	 * @return unknown_type
	 */
	function ultimaCalificacion($socio_id,$persona_beneficio_id=null,$toString=false,$incluyeFecha=false,$noEnviaDiskette=false,$periodo = NULL){
		$this->unbindModel(array('belongsTo' => array('Socio')));
		$conditions = array();
		$conditions['SocioCalificacion.socio_id'] = $socio_id;
		if (!empty($persona_beneficio_id)) {
            $conditions['SocioCalificacion.persona_beneficio_id'] = $persona_beneficio_id;
        }
        if(!empty($periodo)){
            $conditions['SocioCalificacion.periodo <='] = $periodo;
        }                
        $calificacion = $this->find('all',array('conditions' => $conditions,'order' => array('SocioCalificacion.periodo DESC','SocioCalificacion.prioritaria DESC','SocioCalificacion.created DESC'),'limit' => 1));
		$codigo = (!empty($calificacion[0]['SocioCalificacion']['calificacion']) ? $calificacion[0]['SocioCalificacion']['calificacion'] : NULL);
		$fecha = (!empty($calificacion[0]['SocioCalificacion']['created']) ? $calificacion[0]['SocioCalificacion']['created'] : NULL);
		$periodo = (!empty($calificacion[0]['SocioCalificacion']['periodo']) ? $calificacion[0]['SocioCalificacion']['periodo'] : NULL);
		if(!empty($fecha)) {
		    $fecha = parent::strToDate($fecha);
		}
		$glb = parent::getGlobalDato('concepto_1,logico_2',$codigo);
		$cal = array();
		$cal[0] = ($toString ? $glb['GlobalDato']['concepto_1'] : $codigo);
		$cal[1] = $fecha;
		$cal[2] = $periodo;
		if($noEnviaDiskette && $glb['GlobalDato']['logico_2'] == 0){
		    $cal = array(0 => null, 1 => null, 2 => null);
		}
		if(!$incluyeFecha) {
		    return $cal[0];
		} else {
		    return $cal;
		}
	}
	
	/**
	 * Controla si la ultima calificacion que tiene el socio es de STOP DEBIT.
	 * @param int $socio_id
	 * @return boolean TRUE = ultima calificacion es STOP 
	 */
	function isStopDebit($socio_id,$periodo = NULL){
            $this->unbindModel(array('belongsTo' => array('Socio')));
            $IS_STOP = FALSE;
            #VERIFICAR SI TIENE CALIFICACION PRIORITARIA PARA EL PERIODO
            if(!empty($periodo)){
                $sql = " SELECT `SocioCalificacion`.`id`, `SocioCalificacion`.`socio_id`, `SocioCalificacion`.`persona_beneficio_id`, 
                        `SocioCalificacion`.`periodo`, `SocioCalificacion`.`calificacion`, `SocioCalificacion`.`prioritaria`, `SocioCalificacion`.`user_created`, 
                        `SocioCalificacion`.`user_modified`, `SocioCalificacion`.`created`, `SocioCalificacion`.`modified`, 
                        `Socio`.`id`, `Socio`.`categoria`, `Socio`.`persona_id`, `Socio`.`socio_solicitud_id`, 
                        `Socio`.`persona_beneficio_id`, `Socio`.`periodo_ini`, `Socio`.`periodicidad`, `Socio`.`periodo_hasta`, 
                        `Socio`.`activo`, `Socio`.`fecha_alta`, `Socio`.`orden_descuento_id`, `Socio`.`calificacion`, 
                        `Socio`.`fecha_calificacion`, `Socio`.`codigo_baja`, `Socio`.`fecha_baja`, `Socio`.`observaciones`, 
                        `Socio`.`importe_cuota_social`, `Socio`.`periodo_mayor_envio`, `Socio`.`importe_mayor_envio`, 
                        `Socio`.`periodo_mayor_debito`, `Socio`.`importe_mayor_debito`, `Socio`.`idr`, `Socio`.`user_created`, 
                        `Socio`.`user_modified`, `Socio`.`created`, `Socio`.`modified`, `Socio`.`updated` 
                        ,`Calificacion`.`concepto_1`,`Calificacion`.`logico_1`,`Calificacion`.`logico_2`,`Calificacion`.`logico_3`
                        FROM `socio_calificaciones` 
                        AS `SocioCalificacion` 
                        LEFT JOIN `socios` AS `Socio` ON (`SocioCalificacion`.`socio_id` = `Socio`.`id`) 
                        INNER JOIN `global_datos` as `Calificacion` on `Calificacion`.`id` = `SocioCalificacion`.`calificacion`
                        WHERE `SocioCalificacion`.`socio_id` = $socio_id and `SocioCalificacion`.`periodo` = '$periodo' 
                        and  `SocioCalificacion`.`prioritaria` = 1 
                        ORDER BY `SocioCalificacion`.`periodo` DESC,`SocioCalificacion`.`prioritaria` DESC,`SocioCalificacion`.`created` DESC LIMIT 1";    
                $calificacion = $this->query($sql);
                if(!empty($calificacion)){

                    if(isset($calificacion[0]['SocioCalificacion']['calificacion'])){
//                         if($calificacion[0]['Calificacion']['logico_2'] == 0){ return FALSE;}
                        if($calificacion[0]['SocioCalificacion']['calificacion'] == 'MUTUCALISDEB' || $calificacion[0]['Calificacion']['logico_3']) {$IS_STOP = true;}
//                         else {return false;}
                    }else{
//                         {return false;}
                    }                    
                } else {
//                     {return false;}
                }
            }
            
            IF($IS_STOP) {return true;}
            
            $sql = " SELECT `SocioCalificacion`.`id`, `SocioCalificacion`.`socio_id`, `SocioCalificacion`.`persona_beneficio_id`, 
                    `SocioCalificacion`.`periodo`, `SocioCalificacion`.`calificacion`, `SocioCalificacion`.`prioritaria`, `SocioCalificacion`.`user_created`, 
                    `SocioCalificacion`.`user_modified`, `SocioCalificacion`.`created`, `SocioCalificacion`.`modified`, 
                    `Socio`.`id`, `Socio`.`categoria`, `Socio`.`persona_id`, `Socio`.`socio_solicitud_id`, 
                    `Socio`.`persona_beneficio_id`, `Socio`.`periodo_ini`, `Socio`.`periodicidad`, `Socio`.`periodo_hasta`, 
                    `Socio`.`activo`, `Socio`.`fecha_alta`, `Socio`.`orden_descuento_id`, `Socio`.`calificacion`, 
                    `Socio`.`fecha_calificacion`, `Socio`.`codigo_baja`, `Socio`.`fecha_baja`, `Socio`.`observaciones`, 
                    `Socio`.`importe_cuota_social`, `Socio`.`periodo_mayor_envio`, `Socio`.`importe_mayor_envio`, 
                    `Socio`.`periodo_mayor_debito`, `Socio`.`importe_mayor_debito`, `Socio`.`idr`, `Socio`.`user_created`, 
                    `Socio`.`user_modified`, `Socio`.`created`, `Socio`.`modified`, `Socio`.`updated` 
                    ,`Calificacion`.`concepto_1`,`Calificacion`.`logico_1`,`Calificacion`.`logico_2`,`Calificacion`.`logico_3`
                    FROM `socio_calificaciones` 
                    AS `SocioCalificacion` 
                    LEFT JOIN `socios` AS `Socio` ON (`SocioCalificacion`.`socio_id` = `Socio`.`id`) 
                    INNER JOIN `global_datos` as `Calificacion` on `Calificacion`.`id` = `SocioCalificacion`.`calificacion`
                    WHERE `SocioCalificacion`.`socio_id` = $socio_id " . (!empty($periodo) ? " and `SocioCalificacion`.`periodo` < '$periodo' " : " ") . "
                    ORDER BY `SocioCalificacion`.`periodo` DESC,`SocioCalificacion`.`prioritaria` DESC,`SocioCalificacion`.`created` DESC LIMIT 1";        
                    
            $calificacion = $this->query($sql);
            if(empty($calificacion)) {return false;}
    		if(isset($calificacion[0]['SocioCalificacion']['calificacion'])){      
    		    if($calificacion[0]['Calificacion']['logico_2'] == 0) {return false;}
    		    if($calificacion[0]['SocioCalificacion']['calificacion'] == 'MUTUCALISDEB' || $calificacion[0]['Calificacion']['logico_3']) {return true;}
                else {return false;}
    		}else{
    		    {return false;}
    		}
	}
	
	
	function getResumenCalificaciones($socio_id){
		$resumen = array();
//		$resumen['SocioCalificacion'] = array();
		$sql = "select 
					SocioCalificacion.calificacion,
					GlobalDato.concepto_1,
					count(*) as cantidad
				from 
					socio_calificaciones as SocioCalificacion
				inner join 
					global_datos as GlobalDato on (GlobalDato.id = SocioCalificacion.calificacion)
				where 
					SocioCalificacion.socio_id = $socio_id
				group by 
					SocioCalificacion.calificacion
				order by 
					cantidad desc";
		$datos = $this->query($sql);
		if(empty($datos)) return null;
		foreach($datos as $i => $calificacion):
			$resumen[$i]['SocioCalificacion']['calificacion'] = $calificacion['SocioCalificacion']['calificacion'];
			$resumen[$i]['SocioCalificacion']['calificacion_desc'] = $calificacion['GlobalDato']['concepto_1'];
			$resumen[$i]['SocioCalificacion']['cantidad'] = $calificacion[0]['cantidad'];
		endforeach;
		return $resumen;
	}
	
}
?>
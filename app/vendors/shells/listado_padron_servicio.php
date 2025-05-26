<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_padron_servicio 6428 -app /home/adrian/dev/www/sigem/app/
 * 
 * 
 */

class ListadoPadronServicioShell extends Shell {

	var $fecha_desde;
	var $fecha_hasta;

	
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
		
		$servicioID = $asinc->getParametro('p1');
		$fechaCoberturaDesde = $asinc->getParametro('p2');
		
		App::import('Model','mutual.MutualServicioSolicitud');
		$oSERV = new MutualServicioSolicitud();
		
		App::import('Model','Mutual.MutualServicioValor');
		$oSERV_VALOR = new MutualServicioValor();		
		
        
        $CUOTAS_SOCIALES_ADEUDADAS_MINIMO = 3;
		
//		debug($servicioID . " ** " . $fechaCoberturaDesde);

		$asinc->actualizar(1,100,"INICIANDO PROCESO...");
		
		//ACTUALIZAR EL PADRON ECCO CAPITAL
        
        $CORDOBA = implode("','",array(
                    'CORDOBA',
                    'COORDOBA',
                    'CORDABA',
                    'CORDONA',
                    'CORODBA',
                    'CORDABA',
                    'CNA',
                    'CIORDOBA',
                    'CENTRO',
                    'CBA�',
                    'CBA CAPITAL',
                    'CBA',
                    'CB',
                    'CAPITAL-CORDOBA',
                    'CORDOBA-CAPITAL',
                    'CAPITAL',
                    '5',
                    '5000',
                    '5006',
                    '5008',
                    'CBA        0351155925424',
                    'CBA       0351153573828',
                    'CBA       03514551195',
                    'CBA       153570074',
                    'CBA     0351155320095',
                    'CBA     0351155339411',
                    'CBA     4265392',
                    'CBA  CEL156184646',
                    'CBA - 155328994',
                    'CBA - 155586252',
                    'CBA 156971367'            
        ));
        
        $CORDOBA = "'".$CORDOBA."'";
        
		if($servicioID == 1 && 1 == 2):
		
			$sql = "SELECT Persona.*,Socio.id AS socio_id,Socio.fecha_alta,Socio.persona_beneficio_id 
					FROM personas AS Persona
					INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
					WHERE 
					(
						Persona.codigo_postal = '5000' OR 
						Persona.localidad LIKE 'CORDOBA%' OR 
						Persona.localidad LIKE 'CBA%' OR
						Persona.localidad IN ($CORDOBA)
					) AND 
					Socio.activo = 1 AND Persona.fallecida = 0 and ifnull(Persona.provincia_id,0) = 22 AND
					IFNULL(Socio.calificacion,'') <> 'MUTUCALISDEB' AND
					Persona.id NOT IN
					(SELECT persona_id FROM mutual_servicio_solicitudes WHERE
					mutual_servicio_id in (select id
                    from mutual_servicios where proveedor_id = (select proveedor_id from mutual_servicios where id = $servicioID))
                    AND fecha_baja_servicio is null)
					ORDER BY Persona.apellido,Persona.nombre;";
			
			$personas = $oSERV->query($sql);
			
			if(!empty($personas)){
				
				$asinc->actualizar(5,100,"ACTUALIZANDO PADRON");
				
				$total = count($personas);
				$asinc->setTotal($total);
				$i = 0;				
				
				foreach($personas as $persona){
	
					$servicio = array();
					$servicio['MutualServicioSolicitud'] = array();
					
					$fechaAlta = ($persona['Socio']['fecha_alta'] < '2011-07-01' ? '2011-07-01' : $persona['Socio']['fecha_alta']);
					
					$servicio['MutualServicioSolicitud']['id'] = 0;
					$servicio['MutualServicioSolicitud']['mutual_servicio_id'] = $servicioID;
					$servicio['MutualServicioSolicitud']['persona_id'] = $persona['Persona']['id'];
					$servicio['MutualServicioSolicitud']['socio_id'] = $persona['Socio']['socio_id'];
					$servicio['MutualServicioSolicitud']['persona_beneficio_id'] = $persona['Socio']['persona_beneficio_id'];
					$servicio['MutualServicioSolicitud']['fecha_emision'] = $fechaAlta;
					$servicio['MutualServicioSolicitud']['aprobada'] = 1;
					$servicio['MutualServicioSolicitud']['fecha_aprobacion'] = $fechaAlta;
					$servicio['MutualServicioSolicitud']['fecha_alta_servicio'] = $oSERV->calculaFechaCobertura($fechaAlta);
					$servicio['MutualServicioSolicitud']['importe_mensual'] = 0;
					$servicio['MutualServicioSolicitud']['importe_mensual_total'] = 0;
					$servicio['MutualServicioSolicitud']['user_created'] = 'ALTA_AUTOMATICA_'.date('Ym');
					$servicio['MutualServicioSolicitud']['created'] = date('Y-m-d H:i:s');
					
					$msg = "ACTUALIZANDO PADRON | $i/$total " . $persona['Persona']['apellido'].", " . $persona['Persona']['nombre'];
					$asinc->actualizar($i,$total,$msg);
					
					$oSERV->save($servicio);	
					
					$i++;
				}
				
			}
		endif;

		//ACTUALIZAR ECCO INTERIOR
		if($servicioID == 2 && 1 == 2):
		
//			$sql = "SELECT Persona.localidad 
//					FROM mutual_servicio_solicitudes as MutualServicioSolicitud
//					INNER JOIN personas as Persona ON (Persona.id = MutualServicioSolicitud.persona_id)
//					WHERE MutualServicioSolicitud.mutual_servicio_id = $servicioID 
//                    and ifnull(Persona.provincia_id,0) = 22 and ifnull(Persona.codigo_postal,'') <> ''   
//					GROUP BY Persona.localidad;";
//		
//			$localidades = $oSERV->query($sql);
//			$localidades = Set::extract('/Persona/localidad',$localidades);
//			$localidades = "'".implode("','", $localidades) . "'";
			
//			$sql = "SELECT Persona.*,Socio.id AS socio_id,Socio.fecha_alta,Socio.persona_beneficio_id 
//					FROM personas AS Persona
//					INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
//					WHERE 
//						Persona.localidad IN ($localidades)
//						AND Socio.activo = 1 
//						AND IFNULL(Socio.calificacion,'') <> 'MUTUCALISDEB' 
//						AND Persona.id NOT IN (SELECT persona_id FROM mutual_servicio_solicitudes WHERE mutual_servicio_id = $servicioID AND fecha_baja_servicio is null)
//					ORDER BY Persona.apellido,Persona.nombre;";
            
			$sql = "SELECT Persona.*,Socio.id AS socio_id,Socio.fecha_alta,Socio.persona_beneficio_id 
					FROM personas AS Persona
					INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
					WHERE 
						Persona.localidad NOT IN ($CORDOBA)
                        and ifnull(Persona.provincia_id,0) = 22    
						AND Socio.activo = 1 AND Persona.fallecida = 0
						AND IFNULL(Socio.calificacion,'') <> 'MUTUCALISDEB' 
						AND Persona.id NOT IN (SELECT persona_id FROM mutual_servicio_solicitudes 
                        WHERE mutual_servicio_id in (select id
                            from mutual_servicios where proveedor_id = (select proveedor_id from mutual_servicios where id = $servicioID)) 
                            AND fecha_baja_servicio is null)
					ORDER BY Persona.apellido,Persona.nombre;";
            
			$personas = $oSERV->query($sql);
		
			if(!empty($personas)){
			
				$asinc->actualizar(5,100,"ACTUALIZANDO PADRON");
				
				$total = count($personas);
				$asinc->setTotal($total);
				$i = 0;				
				
				foreach($personas as $persona){
	
					$servicio = array();
					$servicio['MutualServicioSolicitud'] = array();
					
					$fechaAlta = ($persona['Socio']['fecha_alta'] < '2011-07-01' ? '2011-07-01' : $persona['Socio']['fecha_alta']);
					
					$servicio['MutualServicioSolicitud']['id'] = 0;
					$servicio['MutualServicioSolicitud']['mutual_servicio_id'] = $servicioID;
					$servicio['MutualServicioSolicitud']['persona_id'] = $persona['Persona']['id'];
					$servicio['MutualServicioSolicitud']['socio_id'] = $persona['Socio']['socio_id'];
					$servicio['MutualServicioSolicitud']['persona_beneficio_id'] = $persona['Socio']['persona_beneficio_id'];
					$servicio['MutualServicioSolicitud']['fecha_emision'] = $fechaAlta;
					$servicio['MutualServicioSolicitud']['aprobada'] = 1;
					$servicio['MutualServicioSolicitud']['fecha_aprobacion'] = $fechaAlta;
					$servicio['MutualServicioSolicitud']['fecha_alta_servicio'] = $oSERV->calculaFechaCobertura($fechaAlta);
					$servicio['MutualServicioSolicitud']['importe_mensual'] = 0;
					$servicio['MutualServicioSolicitud']['importe_mensual_total'] = 0;
					$servicio['MutualServicioSolicitud']['user_created'] = 'ALTA_AUTOMATICA_'.date('Ym');
					$servicio['MutualServicioSolicitud']['created'] = date('Y-m-d H:i:s');
					
					$msg = "ACTUALIZANDO PADRON | $i/$total " . $persona['Persona']['apellido'].", " . $persona['Persona']['nombre'];
					$asinc->actualizar($i,$total,$msg);
					$oSERV->save($servicio);	
					
					$i++;
				}
				
			}
			
		endif;
		
		//actualizar padron AMSEC (PARA SOCIOS CON DOMICILIO EN CAPITAL)
		if($servicioID == 3):
		
			$sql = "SELECT Persona.*,Socio.id AS socio_id,Socio.fecha_alta,Socio.persona_beneficio_id 
					FROM personas AS Persona
					INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
					WHERE 
					(
						Persona.codigo_postal = '5000' OR 
						Persona.localidad LIKE 'CORDOBA%' OR 
						Persona.localidad LIKE 'CBA%' OR
						Persona.localidad IN ($CORDOBA) AND 
					Socio.activo = 1 AND Persona.fallecida = 0 and ifnull(Persona.provincia_id,0) = 22 AND
					IFNULL(Socio.calificacion,'') <> 'MUTUCALISDEB' AND
					Persona.id NOT IN
					(SELECT persona_id FROM mutual_servicio_solicitudes WHERE
					mutual_servicio_id = $servicioID AND fecha_baja_servicio is null)
					ORDER BY Persona.apellido,Persona.nombre;";
			
			$personas = $oSERV->query($sql);
			
				if(!empty($personas)){
			
				$asinc->actualizar(5,100,"ACTUALIZANDO PADRON");
				
				$total = count($personas);
				$asinc->setTotal($total);
				$i = 0;				
				
				foreach($personas as $persona){
	
					$servicio = array();
					$servicio['MutualServicioSolicitud'] = array();
					
					$fechaAlta = ($persona['Socio']['fecha_alta'] < '2011-07-01' ? '2011-07-01' : $persona['Socio']['fecha_alta']);
					
					$servicio['MutualServicioSolicitud']['id'] = 0;
					$servicio['MutualServicioSolicitud']['mutual_servicio_id'] = $servicioID;
					$servicio['MutualServicioSolicitud']['persona_id'] = $persona['Persona']['id'];
					$servicio['MutualServicioSolicitud']['socio_id'] = $persona['Socio']['socio_id'];
					$servicio['MutualServicioSolicitud']['persona_beneficio_id'] = $persona['Socio']['persona_beneficio_id'];
					$servicio['MutualServicioSolicitud']['fecha_emision'] = $fechaAlta;
					$servicio['MutualServicioSolicitud']['aprobada'] = 1;
					$servicio['MutualServicioSolicitud']['fecha_aprobacion'] = $fechaAlta;
					$servicio['MutualServicioSolicitud']['fecha_alta_servicio'] = $oSERV->calculaFechaCobertura($fechaAlta);
					$servicio['MutualServicioSolicitud']['importe_mensual'] = 0;
					$servicio['MutualServicioSolicitud']['importe_mensual_total'] = 0;
					$servicio['MutualServicioSolicitud']['user_created'] = 'ALTA_AUTOMATICA_'.date('Ym');
					$servicio['MutualServicioSolicitud']['created'] = date('Y-m-d H:i:s');
					
					$msg = "ACTUALIZANDO PADRON | $i/$total " . $persona['Persona']['apellido'].", " . $persona['Persona']['nombre'];
					$asinc->actualizar($i,$total,$msg);
					$oSERV->save($servicio);	
					
					$i++;
				}
				
			}			

			
		
		endif;
		
		// PROCESAR LAS BAJAS
		$asinc->actualizar(10,100,"PROCESANDO BAJAS NO EMITIDAS");
		$sql = "SELECT MutualServicioSolicitud.id,if(Persona.fallecida = 1,Persona.fecha_fallecimiento,Socio.fecha_baja),Persona.apellido,Persona.nombre FROM mutual_servicio_solicitudes AS MutualServicioSolicitud
				INNER JOIN socios AS Socio ON (Socio.id = MutualServicioSolicitud.socio_id)
				INNER JOIN personas AS Persona ON (Persona.id = Socio.persona_id)
				WHERE 
				MutualServicioSolicitud.mutual_servicio_id = $servicioID
				AND MutualServicioSolicitud.fecha_baja_servicio IS NULL
				AND (Socio.activo = 0 OR Persona.fallecida = 1)
				ORDER BY Persona.apellido,Persona.nombre;";
		$bajas = $oSERV->query($sql);
		if(!empty($bajas)):
		
			$total = count($bajas);
			$asinc->setTotal($total);
			$i = 0;
			foreach($bajas as $baja):
				$fechaBaja = $oSERV->calculaFechaCobertura($baja['Socio']['fecha_baja'],true);
				$periodoHasta = date('Ym',strtotime($fechaBaja));
				if($oSERV->bajaSolicitud($baja['MutualServicioSolicitud']['id'],$fechaBaja,$periodoHasta,"** BAJA DEL SOCIO **")){
					$msg = "PROCESANDO BAJAS | $i/$total " . $baja['Persona']['apellido'].", " . $baja['Persona']['nombre'];
					$asinc->actualizar($i,$total,$msg);
				}
				$i++;
			endforeach;
		
		endif;
		
		$asinc->actualizar(5,100,"CARGANDO DATOS PARA EMITIR PADRON");
		
		$conditions = array();
		$conditions['MutualServicioSolicitud.mutual_servicio_id'] = $servicioID;
		$conditions['MutualServicioSolicitud.aprobada'] = 1;
		$conditions['MutualServicioSolicitud.fecha_alta_servicio <='] = $fechaCoberturaDesde;
		
//		$conditions['MutualServicioSolicitud.socio_id'] = 17532;
		
//		debug($conditions);
		
		$socios = $oSERV->find('all',array('conditions' => $conditions,'fields' => array('MutualServicioSolicitud.socio_id'),'group' => array('MutualServicioSolicitud.socio_id'),'order' => "MutualServicioSolicitud.id"));
		
		######################################################################################
		#CARGAR SOCIOS QUE POR LO MENOS TENGAN PAGA UNA CUOTA SOCIAL EN LOS ULTIMOS TRES MESES
		######################################################################################
		
		
		if(empty($socios)){
			$asinc->fin("**** PROCESO FINALIZADO :: NO EXISTEN REGISTROS PARA PROCESAR ****");
			return;
		}
		
		$total = count($socios);
		$asinc->setTotal($total);
		$i = 0;		
		
		
		$this->Temporal->limpiarTabla($asinc->id);
		
		foreach($socios as $socio):
		
			$solicitudes = $oSERV->getSolicitudesBySocioID($socio['MutualServicioSolicitud']['socio_id'],null,$servicioID,true);

//			debug($solicitudes);
			
			$temp = array();
			$temp1 = array();
			
			if(!empty($solicitudes)):
			
				foreach($solicitudes as $solicitud):
				
				
					$msg = "PROCESANDO --> " . $solicitud['MutualServicioSolicitud']['tipo_numero'] . " | " . strtoupper($solicitud['MutualServicioSolicitud']['titular_tdocndoc_apenom']);
					$asinc->actualizar($i,$total,$msg);
				
//					$this->out($msg);
					
					$temp1 = array();
					$temp['AsincronoTemporal'] = array();
					$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
					$temp['AsincronoTemporal']['texto_1'] = $solicitud['MutualServicioSolicitud']['titular_tdoc'];
					$temp['AsincronoTemporal']['texto_2'] = $solicitud['MutualServicioSolicitud']['titular_ndoc'];
					$temp['AsincronoTemporal']['texto_3'] = strtoupper($solicitud['MutualServicioSolicitud']['titular_apenom']);
					$temp['AsincronoTemporal']['texto_4'] = $solicitud['MutualServicioSolicitud']['titular_sexo'];
					$temp['AsincronoTemporal']['texto_5'] = strtoupper($solicitud['MutualServicioSolicitud']['titular_calle']);
					$temp['AsincronoTemporal']['texto_6'] = $solicitud['MutualServicioSolicitud']['titular_numero_calle'];
					$temp['AsincronoTemporal']['texto_7'] = $solicitud['MutualServicioSolicitud']['titular_piso'];
					$temp['AsincronoTemporal']['texto_8'] = $solicitud['MutualServicioSolicitud']['titular_dpto'];
					$temp['AsincronoTemporal']['texto_9'] = strtoupper($solicitud['MutualServicioSolicitud']['titular_barrio']);
					$temp['AsincronoTemporal']['texto_10'] = strtoupper($solicitud['MutualServicioSolicitud']['titular_localidad']);
					$temp['AsincronoTemporal']['texto_11'] = $solicitud['MutualServicioSolicitud']['titular_cp'];
					$temp['AsincronoTemporal']['texto_12'] = $solicitud['MutualServicioSolicitud']['titular_provincia'];
					$temp['AsincronoTemporal']['texto_13'] = date('d-m-Y',strtotime($solicitud['MutualServicioSolicitud']['fecha_alta_servicio']));
					$temp['AsincronoTemporal']['texto_14'] = (!empty($solicitud['MutualServicioSolicitud']['fecha_baja_servicio']) ? date('d-m-Y',strtotime($solicitud['MutualServicioSolicitud']['fecha_baja_servicio'])) : "");
					$temp['AsincronoTemporal']['texto_15'] = "TIT";
					$temp['AsincronoTemporal']['texto_16'] = (isset($solicitud['MutualServicioSolicitud']['titular_fecha_nacimiento']) && !empty($solicitud['MutualServicioSolicitud']['titular_fecha_nacimiento']) ? date('d-m-Y',strtotime($solicitud['MutualServicioSolicitud']['titular_fecha_nacimiento'])) : "");
					$temp['AsincronoTemporal']['decimal_1'] = $solicitud['MutualServicioSolicitud']['costo_mensual_actual_titular'];
					
					
					$temp['AsincronoTemporal']['entero_1'] = $solicitud['MutualServicioSolicitud']['id'];

					$temp['AsincronoTemporal']['clave_1'] = "REPORTE_1";
					
					if(!empty($solicitud['MutualServicioSolicitud']['fecha_baja_servicio']) && $solicitud['MutualServicioSolicitud']['fecha_baja_servicio'] <= $fechaCoberturaDesde):
						$temp['AsincronoTemporal']['clave_1'] = "REPORTE_2";
					endif;
					
					if($solicitud['MutualServicioSolicitud']['cuotas_sociales_adeudadas'] < $CUOTAS_SOCIALES_ADEUDADAS_MINIMO)$this->Temporal->grabar($temp);
					
					if(!empty($solicitud['MutualServicioSolicitudAdicional'])):
					
						foreach($solicitud['MutualServicioSolicitudAdicional'] as $adicional):
						
							$temp['AsincronoTemporal'] = array();
						
							$temp1 = array();
							$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
							$temp['AsincronoTemporal']['texto_1'] = $adicional['adicional_tdoc'];
							$temp['AsincronoTemporal']['texto_2'] = $adicional['adicional_ndoc'];
							$temp['AsincronoTemporal']['texto_3'] = strtoupper($adicional['adicional_apenom']);
							$temp['AsincronoTemporal']['texto_4'] = $adicional['adicional_sexo'];
							$temp['AsincronoTemporal']['texto_5'] = strtoupper($adicional['adicional_calle']);
							$temp['AsincronoTemporal']['texto_6'] = $adicional['adicional_numero_calle'];
							$temp['AsincronoTemporal']['texto_7'] = $adicional['adicional_piso'];
							$temp['AsincronoTemporal']['texto_8'] = $adicional['adicional_dpto'];
							$temp['AsincronoTemporal']['texto_9'] = strtoupper($adicional['adicional_barrio']);
							$temp['AsincronoTemporal']['texto_10'] = strtoupper($adicional['adicional_localidad']);
							$temp['AsincronoTemporal']['texto_11'] = $adicional['adicional_cp'];
							$temp['AsincronoTemporal']['texto_12'] = $adicional['adicional_provincia'];
							$temp['AsincronoTemporal']['texto_13'] = date('d-m-Y',strtotime($adicional['fecha_alta']));
							$temp['AsincronoTemporal']['texto_14'] = (!empty($adicional['fecha_baja']) ? date('d-m-Y',strtotime($adicional['fecha_baja'])) : "");
							$temp['AsincronoTemporal']['texto_15'] = "ADI";
							$temp['AsincronoTemporal']['texto_16'] = (isset($adicional['fecha_nacimiento']) && !empty($adicional['fecha_nacimiento']) ? date('d-m-Y',strtotime($adicional['fecha_nacimiento'])) : "");
							$temp['AsincronoTemporal']['decimal_1'] = $solicitud['MutualServicioSolicitud']['costo_mensual_actual_adicional'];
							
							
							$temp['AsincronoTemporal']['entero_1'] = $solicitud['MutualServicioSolicitud']['id'];
							
							$temp['AsincronoTemporal']['clave_1'] = "REPORTE_1";
							
							if($adicional['fecha_alta'] <= $fechaCoberturaDesde){
								
								if((!empty($adicional['fecha_baja']) && $adicional['fecha_baja'] <= $fechaCoberturaDesde))$temp['AsincronoTemporal']['clave_1'] = "REPORTE_2";
								
								if($solicitud['MutualServicioSolicitud']['cuotas_sociales_adeudadas'] < $CUOTAS_SOCIALES_ADEUDADAS_MINIMO) $this->Temporal->grabar($temp);
								
								$msg = "PROCESANDO --> " . $solicitud['MutualServicioSolicitud']['tipo_numero'] . " | " . strtoupper($adicional['adicional_tdoc_ndoc_apenom']);
								$asinc->actualizar($i,$total,$msg);
								
//								$this->out($msg);
								
							}
							
							
							
						endforeach;
					
					endif;
				
//					debug($solicitud);

				if($asinc->detenido()){
					break;
				}					
					
				
				endforeach;
			
			endif;
			
			$i++;
		
		endforeach;
		
		$asinc->actualizar(100,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");		
		
//		debug($socios);
		
//		$solicitudes = $oSERV->getSolicitudesVigentesByServicioID($servicioID,$fechaCoberturaDesde);

	
//		if($STOP == 0){
//			$asinc->actualizar($i,$total,"FINALIZANDO...");
//			$asinc->fin("**** PROCESO FINALIZADO ****");
//		}
		
		
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	function getOrdenes(){
		$this->MutualProductoSolicitud->unbindModel(array('belongsTo' => array('Proveedor','OrdenDescuento')));
		$this->MutualProductoSolicitud->Socio->bindModel(array('belongsTo' => array('Persona')));
		$sql = "SELECT 
				`MutualProductoSolicitud`.`id`, 
				`MutualProductoSolicitud`.`aprobada`, 
				`MutualProductoSolicitud`.`fecha`, 
				`MutualProductoSolicitud`.`fecha_pago`, 
				`MutualProductoSolicitud`.`tipo_orden_dto`, 
				`MutualProductoSolicitud`.`tipo_producto`, 
				`MutualProductoSolicitud`.`proveedor_id`, 
				`MutualProductoSolicitud`.`mutual_producto_id`, 
				`MutualProductoSolicitud`.`estado`, 
				`MutualProductoSolicitud`.`socio_id`, 
				`MutualProductoSolicitud`.`persona_beneficio_id`, 
				`MutualProductoSolicitud`.`importe_total`, 
				`MutualProductoSolicitud`.`cuotas`, 
				`MutualProductoSolicitud`.`importe_cuota`, 
				`MutualProductoSolicitud`.`importe_solicitado`, 
				`MutualProductoSolicitud`.`importe_percibido`, 
				`MutualProductoSolicitud`.`periodo_ini`, 
				`MutualProductoSolicitud`.`periodicidad`, 
				`MutualProductoSolicitud`.`primer_vto_socio`, 
				`MutualProductoSolicitud`.`primer_vto_proveedor`, 
				`MutualProductoSolicitud`.`observaciones`, 
				`MutualProductoSolicitud`.`permanente`, 
				`MutualProductoSolicitud`.`orden_descuento_id`, 
				`MutualProductoSolicitud`.`nro_referencia_proveedor`, 
				`MutualProductoSolicitud`.`sin_cargo`, 
				`MutualProductoSolicitud`.`user_created`, 
				`MutualProductoSolicitud`.`user_modified`, 
				`MutualProductoSolicitud`.`created`, 
				`MutualProductoSolicitud`.`modified`
				FROM 
					`mutual_producto_solicitudes` AS `MutualProductoSolicitud` 
				LEFT JOIN `socios` AS `Socio` ON (`MutualProductoSolicitud`.`socio_id` = `Socio`.`id`) 
				LEFT JOIN `persona_beneficios` AS `PersonaBeneficio` ON (`MutualProductoSolicitud`.`persona_beneficio_id` = `PersonaBeneficio`.`id`) 
				LEFT JOIN `personas` AS `Persona` ON (`Socio`.`persona_id` = `Persona`.`id`) 
				LEFT JOIN `global_datos` AS `GlobalDato` ON (`MutualProductoSolicitud`.`tipo_producto` = `GlobalDato`.`id`) 
				WHERE 
					`MutualProductoSolicitud`.`aprobada` = 1 
					AND `MutualProductoSolicitud`.`fecha` BETWEEN '".$this->fecha_desde."' AND '".$this->fecha_hasta."'
				ORDER BY `GlobalDato`.`concepto_1`, `PersonaBeneficio`.`codigo_beneficio`, `Persona`.`apellido` ASC,  `Persona`.`nombre` ASC ";
		$ordenes = $this->MutualProductoSolicitud->query($sql);
		return $ordenes;
	}

}
?>
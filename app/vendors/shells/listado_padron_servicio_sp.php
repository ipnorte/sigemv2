<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php listado_padron_servicio_sp 27537 -app /home/adrian/trabajo/www/sigem/app/
 * /usr/bin/php5 /home/mutualam/public_html/sigem/cake/console/cake.php listado_padron_servicio_sp 28415 -app /home/mutualam/public_html/sigem/app/
 * 
 * 
 */

class ListadoPadronServicioSpShell extends Shell {

	var $fecha_desde;
	var $fecha_hasta;

	
	var $tasks = array('Temporal');
    
    
	
	function main() {
        
        Configure::write('debug',0);
        
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
		$oSERVSOL = new MutualServicioSolicitud();
        
		App::import('Model','mutual.MutualServicio');
		$oSERV = new MutualServicio();        
		
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
		
//			$sql = "SELECT Persona.*,Socio.id AS socio_id,Socio.fecha_alta,Socio.persona_beneficio_id 
//					FROM personas AS Persona
//					INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
//					WHERE 
//					(
//						Persona.codigo_postal = '5000' OR 
//						Persona.localidad LIKE 'CORDOBA%' OR 
//						Persona.localidad LIKE 'CBA%' OR
//						Persona.localidad IN ($CORDOBA)
//					) AND 
//					Socio.activo = 1 AND Persona.fallecida = 0 and ifnull(Persona.provincia_id,0) = 22 AND
//					IFNULL(Socio.calificacion,'') <> 'MUTUCALISDEB' AND
//					Persona.id NOT IN
//					(SELECT persona_id FROM mutual_servicio_solicitudes WHERE
//					mutual_servicio_id in (select id
//                    from mutual_servicios where proveedor_id = (select proveedor_id from mutual_servicios where id = $servicioID))
//                    AND fecha_baja_servicio is null)
//					ORDER BY Persona.apellido,Persona.nombre;";
                    
                    /**
                     * CONTROLA CON LA TABLA GLOBAL LOS CODIGOS POSTALES DE CORDOBA CAPITAL
                     */
                        $sql = "select Persona.*,Socio.id AS socio_id,Socio.fecha_alta,Socio.persona_beneficio_id from personas Persona
                                inner join socios Socio on Socio.persona_id = Persona.id
                                where Socio.activo = 1 and Persona.fallecida = 0 
                                and Persona.codigo_postal in (select entero_1 from global_datos
                                where id like 'SERVECCP%' and id <> 'SERVECCP' and logico_1 = 1)
                                AND IFNULL(Socio.calificacion,'') <> 'MUTUCALISDEB'
                                AND Persona.id NOT IN (SELECT persona_id FROM mutual_servicio_solicitudes 
                                WHERE mutual_servicio_id = $servicioID AND fecha_baja_servicio is null)
                                ORDER BY Persona.apellido,Persona.nombre;";                    
			
			$personas = $oSERVSOL->query($sql);
			
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
					
					$oSERVSOL->save($servicio);	
					
					$i++;
				}
				
			}
		endif;

                ##########################################################################################
		# ACTUALIZAR ECCO INTERIOR
                ##########################################################################################
		if($servicioID == 2 && 1 == 2):
		
                    //select entero_1 from global_datos where id like 'SERVECIN%' and id <> 'SERVECIN' group by entero_1;
                    
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
            
//			$sql = "SELECT Persona.*,Socio.id AS socio_id,Socio.fecha_alta,Socio.persona_beneficio_id 
//					FROM personas AS Persona
//					INNER JOIN socios AS Socio ON (Socio.persona_id = Persona.id)
//					WHERE 
//						Persona.localidad NOT IN ($CORDOBA)
//                        and Persona.codigo_postal IN ('5107','5854','5186','5189','5189','5147','5117','2550','5158',
//                        '5155','5184','5162','5223','5166','5280','5121','5149','5281','5151','5151','5107',
//                        '5857','5145','5800','5174','5220','5220','5189','5151','5178','5172','5115','5189',
//                        '6120','5805','5182','5823','5194','5125','2580','5107','5889','5166','5887','5980',
//                        '5158','5972','5022','5111','5800','5960','5850','5149','5113','5153','5182','2400','5182',
//                        '5282','5825','5164','5196','5220','5155','5109','5248','5168','5105','5152','5189','5891',
//                        '5248','5870','5194','5176','5186','5220','5903','5101','5151','5152','5149','5101')  
//                        and ifnull(Persona.provincia_id,0) = 22    
//						AND Socio.activo = 1 AND Persona.fallecida = 0
//						AND IFNULL(Socio.calificacion,'') <> 'MUTUCALISDEB' 
//						AND Persona.id NOT IN (SELECT persona_id FROM mutual_servicio_solicitudes 
//                        WHERE mutual_servicio_id in (select id
//                            from mutual_servicios where proveedor_id = (select proveedor_id from mutual_servicios where id = $servicioID)) 
//                            AND fecha_baja_servicio is null)
//					ORDER BY Persona.apellido,Persona.nombre;";
            
                    /**
                     * CONTROLA CON LA TABLA GLOBAL LOS CODIGOS POSTALES DE LAS LOCALIDADES
                     * DEL INTERIOR QUE SE BRINDA EL SERVICIO DE ECCO INTERIOR
                     */
                        $sql = "select Persona.*,Socio.id AS socio_id,Socio.fecha_alta,Socio.persona_beneficio_id from personas Persona
                                inner join socios Socio on Socio.persona_id = Persona.id
                                where Socio.activo = 1 and Persona.fallecida = 0 
                                and Persona.codigo_postal in (select entero_1 from global_datos
                                where id like 'SERVECIN%' and id <> 'SERVECIN' and logico_1 = 1)
                                AND IFNULL(Socio.calificacion,'') <> 'MUTUCALISDEB'
                                AND Persona.id NOT IN (SELECT persona_id FROM mutual_servicio_solicitudes 
                                WHERE mutual_servicio_id = $servicioID AND fecha_baja_servicio is null)
                                ORDER BY Persona.apellido,Persona.nombre;";
                    
			$personas = $oSERVSOL->query($sql);
		
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
					$oSERVSOL->save($servicio);	
					
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
			
			$personas = $oSERVSOL->query($sql);
			
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
					$oSERVSOL->save($servicio);	
					
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
		$bajas = $oSERVSOL->query($sql);
		if(!empty($bajas)):
		
			$total = count($bajas);
			$asinc->setTotal($total);
			$i = 0;
			foreach($bajas as $baja):
				$fechaBaja = $oSERVSOL->calculaFechaCobertura($baja['Socio']['fecha_baja'],true);
				$periodoHasta = date('Ym',strtotime($fechaBaja));
				if($oSERVSOL->bajaSolicitud($baja['MutualServicioSolicitud']['id'],$fechaBaja,$periodoHasta,"** BAJA DEL SOCIO **")){
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
        
        $sql = "SELECT MutualServicioSolicitud.socio_id FROM mutual_servicio_solicitudes MutualServicioSolicitud 
                WHERE 
                MutualServicioSolicitud.mutual_servicio_id = $servicioID 
                AND MutualServicioSolicitud.aprobada = 1 
                AND MutualServicioSolicitud.fecha_alta_servicio <= '$fechaCoberturaDesde';";
        $socios = $oSERVSOL->query($sql);
        
		
//		$socios = $oSERVSOL->find('all',array('conditions' => $conditions,'fields' => array('MutualServicioSolicitud.socio_id'),'group' => array('MutualServicioSolicitud.socio_id'),'order' => "MutualServicioSolicitud.id"));
		
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
        
        ######################################################################################
        # SETEO DATOS PARA EXCEL
        ######################################################################################
		App::import('Helper','Util');
		$oUT = new UtilHelper();        
        $FILE_EXCEL = $oSERV->generarPIN(20).".xls";
        $this->Temporal->setXLSObject(1);
        
        
        
        $set = array();
        $set['sheet_title'] = 'PADRON';
        $set['labels'] = array(
            'A1' => 'PADRON',
            'B1' => $oSERV->getNombreProveedorServicio($servicioID),
            'A2' => 'COBERTURA DESDE:',
            'B2' => $oUT->armaFecha($fechaCoberturaDesde),
        );        
        $set['columns'] = array(
                            'texto_1' => 'TIPO',
                            'texto_2' => 'DOCUMENTO',
                            'texto_3' => 'APELLIDO_NOMBRE',
                            'texto_4' => 'SEXO',
                            'texto_16' => 'FECHA_NACIMIENTO',
                            'texto_5' => 'CALLE',
                            'texto_6' => 'NRO',
                            'texto_7' => 'PISO',
                            'texto_8' => 'DPTO',
                            'texto_9' => 'BARRIO',
                            'texto_10' => 'LOCALIDAD',
                            'texto_11' => 'CP',
                            'texto_12' => 'PROVINCIA',
                            'texto_13' => 'COBERTURA_DESDE',
                            'texto_14' => 'COBERTURA_HASTA',
                            'texto_15' => 'CONDICION',
                            'decimal_1' => 'IMPORTE',
                            'texto_17' => 'ORGANISMO',
        );
        $this->Temporal->prepareXLSSheet(0,$set);
        $set['sheet_title'] = 'BAJAS';
        $set['labels'] = array(
            'A1' => 'PADRON',
            'B1' => $oSERV->getNombreProveedorServicio($servicioID),
            'A2' => 'COBERTURA HASTA:',
            'B2' => $oUT->armaFecha($fechaCoberturaDesde),
        );         
        $this->Temporal->prepareXLSSheet(1,$set);
        
      
        
        ######################################################################################
		
		foreach($socios as $socio):
            
            $sql = "CALL SP_REPORTE_PADRON_SERVICIOS(".$asinc->id.", ".$socio['MutualServicioSolicitud']['socio_id'].", $servicioID, '$fechaCoberturaDesde',$CUOTAS_SOCIALES_ADEUDADAS_MINIMO);";
            $oSERVSOL->query($sql);
            
            $asinc->actualizar($i,$total,"$i|$total - PROCESANDO SOCIO #".$socio['MutualServicioSolicitud']['socio_id']);
            $i++;
		
		endforeach;
        
        $asinc->actualizar(5,100,"GENERANDO REPORTE ...");
        $sql = "SELECT clave_1,entero_1,texto_1,texto_2,
                texto_3,texto_4,texto_5,texto_6,texto_7,texto_8,
                texto_9,texto_10,texto_11,texto_12,texto_13,texto_14,texto_15,texto_16,texto_17,decimal_1 FROM asincrono_temporales AsincronoTemporal
                WHERE asincrono_id = " . $asinc->id ." AND clave_1 = 'REPORTE_1' group by AsincronoTemporal.texto_2 order by AsincronoTemporal.texto_3;";
        $datos = $asinc->query($sql);        
        if(!empty($datos)){
            
            $total = count($datos);
            $asinc->setTotal($total);
            $i = 0;            
            foreach($datos as $dato){
                $asinc->actualizar($i,$total,"$i|$total - GENERANDO PADRON #".$dato['AsincronoTemporal']['texto_3']);
                $this->Temporal->writeXLSRow(0,$dato['AsincronoTemporal']);
                $i++;
            }
        }
        $sql = "SELECT clave_1,entero_1,texto_1,texto_2,
                texto_3,texto_4,texto_5,texto_6,texto_7,texto_8,
                texto_9,texto_10,texto_11,texto_12,texto_13,texto_14,texto_15,texto_16,texto_17,decimal_1 FROM asincrono_temporales AsincronoTemporal
                WHERE asincrono_id = " . $asinc->id ." AND clave_1 = 'REPORTE_2' group by AsincronoTemporal.texto_2 order by AsincronoTemporal.texto_3;";
        $datos = $asinc->query($sql);        
        if(!empty($datos)){
            $total = count($datos);
            $asinc->setTotal($total);
            $i = 0;            
            foreach($datos as $dato){
                $asinc->actualizar($i,$total,"$i|$total - GENERANDO BAJAS #".$dato['AsincronoTemporal']['texto_3']);
                $this->Temporal->writeXLSRow(1,$dato['AsincronoTemporal']);
                $i++;
            }
        }
        
        $asinc->actualizar(98,100,"CREANDO ARCHIVO $FILE_EXCEL ...");
        $this->Temporal->saveToXLSFile($FILE_EXCEL); 
        $asinc->setValue('p6',$FILE_EXCEL);
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
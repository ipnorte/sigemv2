<?php

/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 * 
 * /usr/bin/php5 /home/adrian/Trabajo/www/sigemv2/cake/console/cake.php reporte_padron_personas 80625 -app /home/adrian/Trabajo/www/sigemv2/app/
 * 
 */

class ReportePadronPersonasShell extends Shell {


	var $uses = array('Pfyj.Persona');
	
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

		$tipoListado		= $asinc->getParametro('p1');
		$totalDeuda			= $asinc->getParametro('p2');
		$periodoCorte		= $asinc->getParametro('p3');
                
                $opciones = unserialize(base64_decode($asinc->getParametro('txt1')));

                
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO PADRON DE PERSONAS / SOCIOS...");
		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}

		$personas = $this->getPersonas($tipoListado);
		$total = count($personas);
		$asinc->setTotal($total);
		$i = 0;	

		$temp = array();
		
		App::import('Model','Mutual.OrdenDescuentoCuota');
		$oCUOTA = new OrdenDescuentoCuota();
                
		App::import('Helper', 'Util');
		$util = new UtilHelper();                
                
                App::import('model','mutual.Liquidacion');
                $oLiq = new Liquidacion(); 
//                $ULTIMO_PERIODO_IMPUTADO = $oLiq->getUltimoPeriodoImputado(NULL);
                $ULTIMO_PERIODO_IMPUTADO = $periodoCorte;
                
                $FILE_EXCEL = "PADRON_".date('Ymd-His').".xls";
                $this->Temporal->setXLSObject(0);
                $oXLS = $this->Temporal->getXLSObject();                 

                $set = array();
                $set['sheet_title'] = 'PADRON DE PERSONAS - SOCIOS';
                $set['labels'] = array(
                    'A1' => 'OPCION REPORTE:',
                    'B1' => $opciones[$tipoListado]
                );
                if($totalDeuda == 1){
                    $set['labels']['A2'] = 'PARAMETROS';
                    $set['labels']['B2'] = "INCLUYE DEUDA - PERIODO CORTE: " . $util->periodo($periodoCorte,'/');
                }
                
                $set['columns'] = array(
                    'texto_1' => 'DOCUMENTO',
                    'texto_2' => 'APENOM',
                    'texto_3' => 'DOMICILIO',				
                    'texto_12' => 'LOCALIDAD',
                    'texto_13' => 'CODIGO_POSTAL',
                    'texto_11' => 'PROVINCIA',
                    'texto_14' => 'TEL_FIJO',
                    'texto_15' => 'TEL_MOVIL',
                    'texto_16' => 'TEL_MENSAJES',
                    'texto_17' => 'E_MAIL',
                    'texto_20' => 'CUIT_CUIL',
                    'texto_4' => 'NRO_SOCIO',
                    'texto_10' => 'CATEGORIA',
                    'texto_7' => 'FECHA_ALTA',
                    'texto_5' => 'CALIFICACION',
                    'texto_6' => 'FECHA_CALIFICACION',
                    'texto_8' => 'FECHA_BAJA',
                    'texto_9' => 'MOTIVO_BAJA',
                    'decimal_2' => 'CUOTA_SOCIAL_ADEUDADA',
                    'entero_1' => 'CANTIDAD_CUOTA_SOCIAL_ADEUDADA',
                    'decimal_1' => 'DEUDA_OTROS_CONCEPTOS',
                    'entero_2' => 'EDAD',
                    'texto_18' => 'USUARIO_ALTA',
                    'texto_19' => 'VENDEDOR',                    
                    'decimal_3' => 'PAGO_CSOCIAL',
                    'entero_3' => 'PERIODO',
                    
                );		        
                $this->Temporal->prepareXLSSheet(0,$set);
                
		
		foreach($personas as $persona){

//			debug($persona);
			
//			$this->out($persona['Persona']['apellido'].", ".$persona['Persona']['nombre']. (isset($persona['Socio']['id']) ? "\tSOCIO #".$persona['Socio']['id'] : ""));
			
			$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $persona['Persona']['apellido'].", ".$persona['Persona']['nombre']);
			
			$deuda = 0;
			$deuda = (isset($persona['Socio']['id']) && $persona['Socio']['id'] != 0 && $totalDeuda == 1 ? $oCUOTA->getTotalDeudaBySocio($persona['Socio']['id'],$periodoCorte,true) : 0);

			$cuotaSocialAdeudada = (isset($persona['Socio']['id']) && $persona['Socio']['id'] != 0 && $totalDeuda == 1 ? $oCUOTA->getTotalCuotaSocialAdeudadaBySocio($persona['Socio']['id'],$periodoCorte) : array(0 => 0, 1 => 0));

                        $importesCuotaSocial = (isset($persona['Socio']['id']) && $persona['Socio']['id'] != 0  ? $oCUOTA->getCuotaSocialImportes($persona['Socio']['id'],$ULTIMO_PERIODO_IMPUTADO) : NULL);
                        
                        $cuotaSocialPagada = 0;
                        if(!empty($importesCuotaSocial)){
                            $cuotaSocialPagada = $importesCuotaSocial['pagado'];
                        }
                        
			$temp['AsincronoTemporal'] = array(
                                        'asincrono_id' => $asinc->id,
                                        'texto_1' => $this->Persona->GlobalDato('concepto_1',$persona['Persona']['tipo_documento']) ."-".$persona['Persona']['documento'],
                                        'texto_2' => $persona['Persona']['apellido'].", ".$persona['Persona']['nombre'],
                                        'texto_3' => $this->Persona->getDomicilio($persona),
                                        'texto_4' => (isset($persona['Socio']['id']) && $persona['Socio']['id'] != 0 ? "#".$persona['Socio']['id'] : ""),
                                        'texto_5' => (isset($persona['Socio']['id']) ? $this->Persona->GlobalDato('concepto_1',$persona['Socio']['calificacion']) : ""),
                                        'texto_6' => (isset($persona['Socio']['id']) && !empty($persona['Socio']['fecha_calificacion']) ? date('d-m-Y',strtotime($persona['Socio']['fecha_calificacion'])) : ""),
                                        'texto_7' => (isset($persona['Socio']['id']) ? date('d-m-Y',strtotime($persona['Socio']['fecha_alta'])) : ""),
                                        'texto_8' => (isset($persona['Socio']['id']) && $tipoListado == 4 && !empty($persona['Socio']['fecha_baja']) ? date('d-m-Y',strtotime($persona['Socio']['fecha_baja'])) : ""),
                                        'texto_9' => (isset($persona['Socio']['id']) && $tipoListado == 4 ? $this->Persona->GlobalDato('concepto_1',$persona['Socio']['codigo_baja']) : ""),
                                        'texto_10' => (isset($persona['Socio']['id']) ? $this->Persona->GlobalDato('concepto_2',$persona['Socio']['categoria']) : ""),
//                                      'texto_11' => $persona['Persona']['barrio'],
                                        'texto_11' => $persona['Provincia']['nombre'],
                                        'texto_12' => $persona['Persona']['localidad'],
                                        'texto_13' => $persona['Persona']['codigo_postal'],
                                        'texto_14' => $persona['Persona']['telefono_fijo'],
                                        'texto_15' => $persona['Persona']['telefono_movil'],
                                        'texto_16' => $persona['Persona']['telefono_referencia'],
                                        'texto_17' => $persona['Persona']['e_mail'],
                                        'texto_18' => (isset($persona['SocioSolicitud']['user_created']) ? $persona['SocioSolicitud']['user_created'] : ""),
                                        'texto_19' => (isset($persona['Vendedor']['id']) ? $persona[0]['vendedor_apenom'] : ""),
                                        'texto_20' => $persona['Persona']['cuit_cuil'],
                                        'decimal_1' => $deuda,
                                        'decimal_2' => $cuotaSocialAdeudada[0],
                                        'entero_1' => $cuotaSocialAdeudada[1],
                                        'entero_2' => $persona[0]['edad'],
                                        'entero_3' => $ULTIMO_PERIODO_IMPUTADO,
                                        'decimal_3' => $cuotaSocialPagada
			);
//			debug($temp);
                        $this->Temporal->writeXLSRow(0,$temp['AsincronoTemporal']);
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
	
		if($STOP == 0){
                    $asinc->actualizar(99,100,"Generando planilla...");
                    $this->Temporal->saveToXLSFile($FILE_EXCEL);
                    $asinc->setValue('p6',$FILE_EXCEL); 

                    $asinc->actualizar($i,$total,"FINALIZANDO...");
                    $asinc->fin("**** PROCESO FINALIZADO ****");
		}
		
		return;
		
	}
	//FIN PROCESO ASINCRONO
	
	####################################################################################################
	# METODOS ESPECIFICOS DEL PROCESO
	####################################################################################################
	
	/**
	 * @param $tipoListado 1 = SOCIOS ACTIVOS Y ADHERENTES | 2 = SOLO SOCIOS ACTIVOS | 3 = SOLO SOCIOS ADHERENTES
	 * | 4 = SOCIOS NO VIGENTES | 5 = PERSONAS NO ASOCIADAS
	 * @return unknown_type
	 */
	function getPersonas($tipoListado){
		
//		$conditions = array();
		
		App::import('Model','Pfyj.Persona');
		$oPER = new Persona();
		
		#SOCIOS ACTIVOS Y ADHERENTES
		if($tipoListado == 1){
//			$sql = "SELECT	Persona.*,Socio.* , Provincia.*, TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad 
//					FROM personas as Persona 
//					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
//                                        LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
//					WHERE Socio.activo = 1 AND Socio.categoria IN ('MUTUCASOACTI','MUTUCASOADHE')";
                        
                        $sql = "SELECT	Persona.*,Socio.* , Provincia.*, 
                                TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad,
                                SocioSolicitud.id,
                                SocioSolicitud.user_created,SocioSolicitud.created,
                                Vendedor.id,
                                Vendedor.cuit_cuil as vendedor_cuit_cuil,
                                concat(Vendedor.apellido,' ',Vendedor.nombre) as vendedor_apenom 
                                FROM personas as Persona 
                                INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
                                LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
                                left join socio_solicitudes SocioSolicitud on SocioSolicitud.id = Socio.socio_solicitud_id and SocioSolicitud.tipo_solicitud = 'A'
                                left join vendedores v on v.id = SocioSolicitud.vendedor_id
                                left join personas Vendedor on Vendedor.id = v.persona_id 
                                WHERE Socio.activo = 1 AND Socio.categoria IN ('MUTUCASOACTI','MUTUCASOADHE')";
                        
		}
		#SOLO SOCIOS ACTIVOS
		if($tipoListado == 2){
//			$sql = "SELECT	Persona.*,Socio.*, Provincia.*, TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad  
//					FROM personas as Persona 
//					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
//                                        LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
//					WHERE Socio.activo = 1 AND Socio.categoria = 'MUTUCASOACTI'";
                        $sql = "SELECT	Persona.*,Socio.* , Provincia.*, 
                                TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad,
                                SocioSolicitud.id,
                                SocioSolicitud.user_created,SocioSolicitud.created,
                                Vendedor.id,
                                Vendedor.cuit_cuil as vendedor_cuit_cuil,
                                concat(Vendedor.apellido,' ',Vendedor.nombre) as vendedor_apenom 
                                FROM personas as Persona 
                                INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
                                LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
                                left join socio_solicitudes SocioSolicitud on SocioSolicitud.id = Socio.socio_solicitud_id and SocioSolicitud.tipo_solicitud = 'A'
                                left join vendedores v on v.id = SocioSolicitud.vendedor_id
                                left join personas Vendedor on Vendedor.id = v.persona_id 
                                WHERE Socio.activo = 1 AND Socio.categoria IN ('MUTUCASOACTI')";                        
		}
		#SOLO SOCIOS ADHERENTES
		if($tipoListado == 3){
//			$sql = "SELECT	Persona.*,Socio.* , Provincia.*, TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad 
//					FROM personas as Persona 
//					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
//                                        LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
//					WHERE Socio.activo = 1 AND Socio.categoria = 'MUTUCASOADHE'";
                        $sql = "SELECT	Persona.*,Socio.* , Provincia.*, 
                                TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad,
                                SocioSolicitud.id,
                                SocioSolicitud.user_created,SocioSolicitud.created,
                                Vendedor.id,
                                Vendedor.cuit_cuil as vendedor_cuit_cuil,
                                concat(Vendedor.apellido,' ',Vendedor.nombre) as vendedor_apenom 
                                FROM personas as Persona 
                                INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
                                LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
                                left join socio_solicitudes SocioSolicitud on SocioSolicitud.id = Socio.socio_solicitud_id and SocioSolicitud.tipo_solicitud = 'A'
                                left join vendedores v on v.id = SocioSolicitud.vendedor_id
                                left join personas Vendedor on Vendedor.id = v.persona_id 
                                WHERE Socio.activo = 1 AND Socio.categoria IN ('MUTUCASOADHE')";                        
		}
		#SOCIOS NO VIGENTES
		if($tipoListado == 4){
//			$sql = "SELECT	Persona.*,Socio.* , Provincia.*, TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad  
//					FROM personas as Persona 
//					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
//                                        LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
//					WHERE Socio.activo = 0";
                        $sql = "SELECT	Persona.*,Socio.* , Provincia.*, 
                                TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad,
                                SocioSolicitud.id,
                                SocioSolicitud.user_created,SocioSolicitud.created,
                                Vendedor.id,
                                Vendedor.cuit_cuil as vendedor_cuit_cuil,
                                concat(Vendedor.apellido,' ',Vendedor.nombre) as vendedor_apenom 
                                FROM personas as Persona 
                                INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
                                LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
                                left join socio_solicitudes SocioSolicitud on SocioSolicitud.id = Socio.socio_solicitud_id and SocioSolicitud.tipo_solicitud = 'A'
                                left join vendedores v on v.id = SocioSolicitud.vendedor_id
                                left join personas Vendedor on Vendedor.id = v.persona_id 
                                WHERE Socio.activo = 0";
		}				
		#PERSONAS NO ASOCIADAS
		if($tipoListado == 5){
			$sql = "SELECT	Persona.*, Provincia.*, TIMESTAMPDIFF(YEAR, Persona.fecha_nacimiento, CURDATE()) AS edad 
					FROM personas as Persona 
                                        LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
					WHERE Persona.id NOT IN (SELECT persona_id FROM socios)";			
		}
		#PADRON INAES
		if($tipoListado == 6){
			$sql = "SELECT	* 
					FROM personas as Persona 
					INNER JOIN socios as Socio ON (Socio.persona_id = Persona.id)
                                        LEFT JOIN provincias as Provincia on (Provincia.id = Persona.provincia_id)
					WHERE Socio.activo = 1 AND Socio.categoria IN ('MUTUCASOACTI','MUTUCASOADHE')";
		}		
		
		$sql .= " ORDER BY Persona.apellido,Persona.nombre;";
		$personas = $oPER->query($sql);
		return $personas;
				
	}

}
?>
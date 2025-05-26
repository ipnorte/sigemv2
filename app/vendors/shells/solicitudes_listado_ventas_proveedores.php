<?php 
/**
 * 
 * @author adrian
 *
 *	/usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php solicitudes_listado_ventas_proveedores 10217 -app /home/adrian/dev/www/sigem/app/
 *
 */

App::import('Model','v1.Solicitud');
	


class SolicitudesListadoVentasProveedoresShell extends Shell{
	
	
	var $tasks = array('Temporal');
	
	function main(){
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 
		
		$periodoDesde = $asinc->getParametro('p1');
		$periodoHasta = $asinc->getParametro('p2');
		$proveedorSelected = $asinc->getParametro('p3');
		
		
//		$this->out("periodo control: $periodo");	
		
		$this->Temporal->limpiarTabla($asinc->id);
		
		$proveedores = $this->getProveedores($periodoDesde,$periodoHasta,$proveedorSelected);
		
		$total = count($proveedores);
		$asinc->setTotal($total);
		$i = 0;			
		
		if(empty($proveedores)):
			$asinc->fin("**** PROCESO FINALIZADO :: NO EXISTEN REGISTROS PARA PROCESAR ****");
			return;		
		endif;
		
		$oSOLICITUD = new Solicitud();
		
		$asinc->actualizar(1,100,"CARGANDO LISTA DE PROVEEDORES");
		
		
//		debug($proveedores);

		
		foreach($proveedores as $proveedor):
			
//			break;	
		
			$razonSocial = strtoupper(utf8_encode($proveedor['Proveedor']['razon_social']));
		
//			$this->out($razonSocial);
			
			$asinc->actualizar($i,$total,"PROCESANDO $razonSocial");
			
			$solicitudes = $this->getSolicitudes($periodoDesde,$periodoHasta,$proveedor['Proveedor']['codigo_proveedor']);
			
			
			if(!empty($solicitudes)):
		
//				debug($solicitudes);

				$temp = array();
				$z = 1;
				$total1 = count($solicitudes);
				
				foreach($solicitudes as $solicitud):
				
					$apeNom = strtoupper(utf8_encode($solicitud['Persona']['apellido'].", ".$solicitud['Persona']['nombre']));
				
					$asinc->actualizar($z,$total1,"PROCESANDO $razonSocial | SOLICITUD " . $solicitud['Solicitud1']['nro_solicitud'] . " | $apeNom");
				
					$temp = array();
					$temp['AsincronoTemporal'] = array();
					$temp['AsincronoTemporal']['id'] = 0;
					$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
					$temp['AsincronoTemporal']['clave_1'] = "LISTADO_1";
					$temp['AsincronoTemporal']['clave_2'] = $proveedor['Proveedor']['codigo_proveedor'];
					$temp['AsincronoTemporal']['texto_1'] = $proveedor['Proveedor']['razon_social'];
					$temp['AsincronoTemporal']['texto_2'] = $solicitud['Glb1']['concepto']." ".$solicitud['Persona']['documento'];
					$temp['AsincronoTemporal']['texto_3'] = $apeNom;
					$temp['AsincronoTemporal']['texto_4'] = $solicitud['Solicitud1']['nro_solicitud'];
					$temp['AsincronoTemporal']['texto_5'] = date('d-m-Y',strtotime($solicitud['Solicitud1']['fecha_solicitud']));
					$temp['AsincronoTemporal']['texto_6'] = $solicitud['Estado']['descripcion'];
					$temp['AsincronoTemporal']['decimal_1'] = $solicitud['Solicitud1']['solicitado'];
					$temp['AsincronoTemporal']['decimal_2'] = $solicitud['Solicitud1']['en_mano'];
					$temp['AsincronoTemporal']['entero_1'] = $solicitud['Solicitud1']['cuotas'];
					$temp['AsincronoTemporal']['decimal_3'] = $solicitud['Solicitud1']['monto_cuota'];
					$temp['AsincronoTemporal']['texto_7'] = $solicitud[0]['organismo'];
					$temp['AsincronoTemporal']['texto_8'] = $solicitud['Glb']['concepto'];
					$temp['AsincronoTemporal']['texto_9'] = $solicitud[0]['periodo'];
					
//					debug($temp);
					
					$this->Temporal->grabar($temp);
					if($asinc->detenido()) break;	
					
					$z++;
					
				endforeach;
			
			endif;
			
			$i++;
			
		endforeach;
		

		//PROCESO EL RESUMEN
		$proveedores = $this->getResumenProveedores($periodoDesde,$periodoHasta,$proveedorSelected);
		$asinc->actualizar(1,100,"CARGANDO RESUMEN DE PROVEEDORES");
		
		$total = count($proveedores);
		$asinc->setTotal($total);
		$i = 0;			
		
		$periodos = $this->getResumenPeriodosProveedores($periodoDesde,$periodoHasta,$proveedorSelected);
		
		foreach($proveedores as $proveedor):
		
			$razonSocial = strtoupper(utf8_encode($proveedor['Proveedor']['razon_social']));
		
			$asinc->actualizar($i,$total,"PROCESANDO $razonSocial | PERIODO " . $oSOLICITUD->periodo($proveedor[0]['periodo'],true));

//			debug($proveedor);

			$col = 0;
			
			foreach($periodos as $idx => $periodo):
			
				if($proveedor[0]['periodo'] == $periodo[0]['periodo']){
					$col = $idx;
					break;
				}				
			
			endforeach;
			
			
			$temp = array();
			$temp['AsincronoTemporal'] = array();
			$temp['AsincronoTemporal']['id'] = 0;
			$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
			$temp['AsincronoTemporal']['clave_1'] = "LISTADO_2";
			$temp['AsincronoTemporal']['clave_2'] = $proveedor['Proveedor']['codigo_proveedor'];
			$temp['AsincronoTemporal']['clave_3'] = $proveedor[0]['periodo'];
			$temp['AsincronoTemporal']['texto_1'] = $proveedor['Proveedor']['razon_social'];
			$temp['AsincronoTemporal']['texto_2'] = $oSOLICITUD->periodo($proveedor[0]['periodo'],true);
			$temp['AsincronoTemporal']['decimal_1'] = $proveedor[0]['venta'];
			$temp['AsincronoTemporal']['entero_1'] = $col;
			
			$this->Temporal->grabar($temp);
			if($asinc->detenido()) break;				
		
			$i++;
		
		endforeach;
		
		
		$asinc->actualizar(100,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");	

		return;
		
	}
	
	
	function getSolicitudes($periodoDesde,$periodoHasta,$codigo_proveedor){
		
		$sql = "SELECT 
				Solicitud1.nro_solicitud,
				Solicitud1.fecha_solicitud,
				Estado.descripcion,
				Glb1.concepto,
				Persona.documento,
				Persona.apellido,
				Persona.nombre,
				Solicitud1.solicitado,
				Solicitud1.en_mano,
				Solicitud1.cuotas,
				Solicitud1.monto_cuota,
				SUBSTR(Beneficio.codigo_beneficio,1,2) AS organismo,
				Glb.concepto,
				CONCAT(Liquidacion.anio,Liquidacion.periodo) as periodo
				FROM productores_liquidacion_solicitudes AS Solicitud
				INNER JOIN productores_liquidacion_items AS Items ON (Items.item = Solicitud.item)
				INNER JOIN productores_liquidacion AS Liquidacion ON (Liquidacion.comprobante = Items.comprobante)
				INNER JOIN solicitudes AS Solicitud1 ON (Solicitud1.nro_solicitud = Solicitud.nro_solicitud)
				INNER JOIN proveedores_productos AS Producto ON (Producto.codigo_producto = Solicitud1.codigo_producto)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.codigo_proveedor = Producto.codigo_proveedor)
				INNER JOIN personas AS Persona ON (Persona.id_persona = Solicitud1.id_persona)
				INNER JOIN solicitud_codigo_estados AS Estado ON (Estado.codigo = Solicitud1.estado)
				INNER JOIN personas_beneficios AS Beneficio ON (Beneficio.id_beneficio = Solicitud1.id_beneficio)
				INNER JOIN tglobal AS Glb ON (Glb.codigo = CONCAT('XXTO',Beneficio.codigo_beneficio))
				INNER JOIN tglobal AS Glb1 ON (Glb1.codigo = CONCAT('TPDC',Persona.tipo_documento))
				WHERE Proveedor.codigo_proveedor = '$codigo_proveedor'
				AND CONCAT(Liquidacion.anio,Liquidacion.periodo) between '$periodoDesde' and '$periodoHasta'
				ORDER BY Persona.apellido,Persona.nombre";
		$oSOLICITUD = new Solicitud();
		return $oSOLICITUD->query($sql);		
		
	}	
	
	
	
	function getProveedores($periodoDesde,$periodoHasta,$proveedor){
		$sql = "SELECT 
				Proveedor.codigo_proveedor,
				Proveedor.razon_social
				FROM productores_liquidacion_solicitudes AS LiquiSolicitudes
				INNER JOIN solicitudes AS Solicitudes ON (Solicitudes.nro_solicitud = LiquiSolicitudes.nro_solicitud)
				INNER JOIN proveedores_productos AS ProveedorProducto ON (ProveedorProducto.codigo_producto = Solicitudes.codigo_producto)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.codigo_proveedor = ProveedorProducto.codigo_proveedor)
				WHERE item IN
				(SELECT item FROM productores_liquidacion_items WHERE comprobante
				IN(SELECT comprobante FROM productores_liquidacion
				WHERE CONCAT(anio,periodo) between '$periodoDesde' and '$periodoHasta'))
				".(!empty($proveedor) ? " AND Proveedor.codigo_proveedor = '$proveedor'" : "")."
				GROUP BY Proveedor.codigo_proveedor 
				ORDER BY Proveedor.razon_social";
		$oSOLICITUD = new Solicitud();
		return $oSOLICITUD->query($sql);		
	}
	
	
	function getResumenProveedores($periodoDesde,$periodoHasta,$proveedor){
		$sql = "SELECT 
				Proveedor.codigo_proveedor,
				Proveedor.razon_social,
				CONCAT(Liquidacion.anio,Liquidacion.periodo) as periodo,
				SUM(Solicitud1.en_mano) as venta
				FROM productores_liquidacion_solicitudes AS Solicitud
				INNER JOIN productores_liquidacion_items AS Items ON (Items.item = Solicitud.item)
				INNER JOIN productores_liquidacion AS Liquidacion ON (Liquidacion.comprobante = Items.comprobante)
				INNER JOIN solicitudes AS Solicitud1 ON (Solicitud1.nro_solicitud = Solicitud.nro_solicitud)
				INNER JOIN proveedores_productos AS Producto ON (Producto.codigo_producto = Solicitud1.codigo_producto)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.codigo_proveedor = Producto.codigo_proveedor)
				INNER JOIN personas AS Persona ON (Persona.id_persona = Solicitud1.id_persona)
				INNER JOIN solicitud_codigo_estados AS Estado ON (Estado.codigo = Solicitud1.estado)
				INNER JOIN personas_beneficios AS Beneficio ON (Beneficio.id_beneficio = Solicitud1.id_beneficio)
				INNER JOIN tglobal AS Glb ON (Glb.codigo = CONCAT('XXTO',Beneficio.codigo_beneficio))
				INNER JOIN tglobal AS Glb1 ON (Glb1.codigo = CONCAT('TPDC',Persona.tipo_documento))
				WHERE 
					CONCAT(Liquidacion.anio,Liquidacion.periodo) between '$periodoDesde' and '$periodoHasta'
					".(!empty($proveedor) ? " AND Proveedor.codigo_proveedor = '$proveedor'" : "")."
				group by Proveedor.codigo_proveedor,CONCAT(Liquidacion.anio,Liquidacion.periodo)
				ORDER BY Proveedor.razon_social,CONCAT(Liquidacion.anio,Liquidacion.periodo);";
		$oSOLICITUD = new Solicitud();
		return $oSOLICITUD->query($sql);		
	}
	
	
	function getResumenPeriodosProveedores($periodoDesde,$periodoHasta,$proveedor){
		$sql = "SELECT 
				CONCAT(Liquidacion.anio,Liquidacion.periodo) as periodo,
				SUM(Solicitud1.en_mano) as venta
				FROM productores_liquidacion_solicitudes AS Solicitud
				INNER JOIN productores_liquidacion_items AS Items ON (Items.item = Solicitud.item)
				INNER JOIN productores_liquidacion AS Liquidacion ON (Liquidacion.comprobante = Items.comprobante)
				INNER JOIN solicitudes AS Solicitud1 ON (Solicitud1.nro_solicitud = Solicitud.nro_solicitud)
				INNER JOIN proveedores_productos AS Producto ON (Producto.codigo_producto = Solicitud1.codigo_producto)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.codigo_proveedor = Producto.codigo_proveedor)
				INNER JOIN personas AS Persona ON (Persona.id_persona = Solicitud1.id_persona)
				INNER JOIN solicitud_codigo_estados AS Estado ON (Estado.codigo = Solicitud1.estado)
				INNER JOIN personas_beneficios AS Beneficio ON (Beneficio.id_beneficio = Solicitud1.id_beneficio)
				INNER JOIN tglobal AS Glb ON (Glb.codigo = CONCAT('XXTO',Beneficio.codigo_beneficio))
				INNER JOIN tglobal AS Glb1 ON (Glb1.codigo = CONCAT('TPDC',Persona.tipo_documento))
				WHERE CONCAT(Liquidacion.anio,Liquidacion.periodo) between '$periodoDesde' and '$periodoHasta'
				".(!empty($proveedor) ? " AND Proveedor.codigo_proveedor = '$proveedor'" : "")."
				group by CONCAT(Liquidacion.anio,Liquidacion.periodo)
				ORDER BY CONCAT(Liquidacion.anio,Liquidacion.periodo);";
		$oSOLICITUD = new Solicitud();
		return $oSOLICITUD->query($sql);		
	}	
	
	
}

?>
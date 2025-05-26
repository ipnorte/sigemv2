<?php 
/**
 * 
 * @author adrian
 *
 *	/usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php solicitudes_listado_ventas_productores 13 -app /home/adrian/dev/www/sigem/app/
 *
 */

App::import('Model','v1.Solicitud');
	


class SolicitudesListadoVentasProductoresShell extends Shell{
	
	
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
		
		
//		$this->out("periodo control: $periodo");	
		
		$this->Temporal->limpiarTabla($asinc->id);
		
		$productores = $this->cargarProductores($periodoDesde,$periodoHasta);
		
		$total = count($productores);
		$asinc->setTotal($total);
		$i = 0;			
		
		if(empty($productores)):
			$asinc->fin("**** PROCESO FINALIZADO :: NO EXISTEN REGISTROS PARA PROCESAR ****");
			return;		
		endif;
		
		$oSOLICITUD = new Solicitud();
		
		$asinc->actualizar(1,100,"CARGANDO LISTA DE PROVEEDORES");
		
		$proveedores = $this->getProveedores($periodoDesde,$periodoHasta);
		
		foreach($productores as $productor):
		
			$prod = $this->getProductor($productor['productores_liquidacion']['codigo_vendedor']);
			
			$tDoc = $prod[0]['productores']['tipo_documento'];
			$nDoc = $prod[0]['productores']['documento'];
			
			$apeNom = $prod[0]['productores']['apellido'].", ".$prod[0]['productores']['nombre'];
			
			$tDocDesc = $oSOLICITUD->getGlobal("concepto","TPDC$tDoc");
			$tDocDesc = $tDocDesc['Tglobal']['concepto'];
			
			$totalVenta = $productor[0]['total_venta'];
			

//			$this->out($tDocDesc." ".$nDoc." - " . $apeNom."\t\t\t$totalVenta");
		
			$asinc->actualizar($i,$total,"PROCESANDO $apeNom");
			
			$resumenVenta = $this->getResumenVenta($periodoDesde,$periodoHasta,$productor['productores_liquidacion']['codigo_vendedor']);

			
			$temp = array();
			
			if(!empty($resumenVenta)):
			
			
				foreach($resumenVenta as $venta):
				
//					debug($venta);

					//SACO EL PROVEEDOR PARA TABULAR
					$col = 0;
					foreach($proveedores as $idx => $proveedor):
						if($proveedor['Proveedor']['codigo_proveedor'] == $venta['Proveedor']['codigo_proveedor']){
							$col = $idx;
							break;
						}
					endforeach;
					
					$temp = array();
					$temp['AsincronoTemporal'] = array();
					$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
					
					$temp['AsincronoTemporal']['clave_1'] = $productor['productores_liquidacion']['codigo_vendedor'];
					$temp['AsincronoTemporal']['clave_2'] = $venta['Proveedor']['codigo_proveedor'];
					$temp['AsincronoTemporal']['texto_1'] = $tDocDesc." ".$nDoc;
					$temp['AsincronoTemporal']['texto_2'] = $apeNom;
					$temp['AsincronoTemporal']['texto_3'] = $venta['Proveedor']['razon_social'];
					$temp['AsincronoTemporal']['texto_4'] = $venta['Proveedor']['codigo_proveedor'];
					$temp['AsincronoTemporal']['decimal_1'] = $venta[0]['venta'];
					$temp['AsincronoTemporal']['decimal_2'] = $totalVenta;
					$temp['AsincronoTemporal']['entero_1'] = $col;
					
//					debug($temp);
					$this->Temporal->grabar($temp);
					
					if($asinc->detenido()) break;					
				
				endforeach;
			
			endif;
			
			$i++;
		
		endforeach;
		
		$asinc->actualizar(100,100,"FINALIZANDO...");
		$asinc->fin("**** PROCESO FINALIZADO ****");	

		return;
		
	}
	
	
	
	function cargarProductores($periodoDesde,$periodoHasta){
		$sql = "SELECT codigo_vendedor,SUM(total_venta) AS total_venta FROM productores_liquidacion
				WHERE CONCAT(anio,periodo) between '$periodoDesde' and '$periodoHasta'
				GROUP BY codigo_vendedor";
		$oSOLICITUD = new Solicitud();
		return $oSOLICITUD->query($sql);
	}
	
	
	function getProductor($codigoProductor){
		
		$sql = "select * from productores where codigo_vendedor = '$codigoProductor'";
		$oSOLICITUD = new Solicitud();
		return $oSOLICITUD->query($sql);
		
	}
	
	
	function getResumenVenta($periodoDesde,$periodoHasta,$codigoProductor){
		$sql = "SELECT 
				Proveedor.codigo_proveedor,
				Proveedor.razon_social,
				SUM(LiquiSolicitudes.en_mano) AS venta
				FROM productores_liquidacion_solicitudes AS LiquiSolicitudes
				INNER JOIN solicitudes AS Solicitudes ON (Solicitudes.nro_solicitud = LiquiSolicitudes.nro_solicitud)
				INNER JOIN proveedores_productos AS ProveedorProducto ON (ProveedorProducto.codigo_producto = Solicitudes.codigo_producto)
				INNER JOIN proveedores AS Proveedor ON (Proveedor.codigo_proveedor = ProveedorProducto.codigo_proveedor)
				WHERE item IN
				(SELECT item FROM productores_liquidacion_items WHERE comprobante
				IN(SELECT comprobante FROM productores_liquidacion
				WHERE CONCAT(anio,periodo) between '$periodoDesde' and '$periodoHasta' AND codigo_vendedor = '$codigoProductor'))
				GROUP BY Proveedor.codigo_proveedor 
				ORDER BY Proveedor.razon_social";
		$oSOLICITUD = new Solicitud();
		return $oSOLICITUD->query($sql);
	}
	
	
	function getProveedores($periodoDesde,$periodoHasta){
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
				GROUP BY Proveedor.codigo_proveedor 
				ORDER BY Proveedor.razon_social";
		$oSOLICITUD = new Solicitud();
		return $oSOLICITUD->query($sql);		
	}
	
}

?>
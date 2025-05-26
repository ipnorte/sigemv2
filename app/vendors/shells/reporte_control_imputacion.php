<?php
/**
 * Reporte control de imputacion
 * 
 * /opt/lampp/bin/php-5.2.8 /home/adrian/Desarrollo/www/sigem/cake/console/cake.php reporte_control_imputacion 95 -app /home/adrian/Desarrollo/www/sigem/app/
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php reporte_control_imputacion 360 -app /home/adrian/dev/www/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 * 
 */

class ReporteControlImputacionShell extends Shell{
	
	var $tasks = array('Temporal');
	var $uses = array('Mutual.LiquidacionSocioRendicion');
	var $liquidacion_id;
	
	function main(){
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$this->liquidacion_id	= $asinc->getParametro('p1');
		
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,100,"ESPERE, CONSULTANDO LIQUIDACION...");
		
		//limpio la tabla temporal
		if(!$this->Temporal->limpiarTabla($asinc->id)){
			$asinc->fin("SE PRODUJO UN ERROR...");
			return;
		}
		
//		$datos = $this->LiquidacionSocioRendicion->imputados($this->liquidacion_id);
		$datos = $this->getDatos();
		
		if(!empty($datos)):
		
			$total = count($datos);
			$asinc->setTotal($total);
			$i = 0;
			
			$temp = array();
			
			foreach($datos as $dato):
//				$dato = $this->LiquidacionSocioRendicion->armaDatos($dato);
				
				$tdocNdoc = $this->LiquidacionSocioRendicion->GlobalDato('concepto_1',$dato['Persona']['tipo_documento'])." ".$dato['Persona']['documento'];
				$apenom = $dato[0]['apenom'];
				
				$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $apenom);
				
				$temp['AsincronoTemporal'] = array(
									
						'asincrono_id' => $asinc->id,
						'clave_1' => 'REPORTE_1',
						'texto_1' => $tdocNdoc,
						'texto_2' => $apenom,
						'decimal_1' => $dato[0]['importe_dto'],
						'decimal_2' => $dato[0]['importe_adebitar'],
						'decimal_3' => $dato[0]['importe_debitado'],
						'decimal_4' => $dato[0]['importe_imputado'],
						'decimal_5' => $dato[0]['importe_reintegro'],
						'entero_1' => $dato['LiquidacionSocioRendicion']['socio_id'],
				);

//				debug($temp);
				if($asinc->detenido()){
					$STOP = 1;
					break;
				}				

				if(!$this->Temporal->grabar($temp)){
					$STOP = 1;
					break;
				}

				$i++;				
			
			endforeach;
			
			$asinc->actualizar(5,100,"ESPERE, ARMANDO RESUMEN PROVEEDORES...");
			$datosProveedores = $this->getResumenByProveedor();
			
			if(!empty($datosProveedores)):
			
				$total = count($datosProveedores);
				$asinc->setTotal($total);
				$i = 0;
				
				$temp = array();			
			
				foreach($datosProveedores as $datoProv):
				
					$asinc->actualizar($i,$total,"$i / $total - PROCESANDO >> " . $datoProv['Proveedor']['razon_social']);
					
					$temp['AsincronoTemporal'] = array(
										
							'asincrono_id' => $asinc->id,
							'clave_1' => 'REPORTE_2',
							'texto_1' => $datoProv['Proveedor']['razon_social'],
							'decimal_1' => $datoProv[0]['liquidado'],
							'decimal_2' => $datoProv[0]['saldo_actual'],
							'decimal_3' => $datoProv[0]['importe_debitado'],
					);
	
					if($asinc->detenido()){
						$STOP = 1;
						break;
					}				
	
					if(!$this->Temporal->grabar($temp)){
						$STOP = 1;
						break;
					}
	
					$i++;						
				
				endforeach;
			
			
			endif;

		endif;
		
		
		

		if($STOP == 0){
			$asinc->actualizar($total,$total,"FINALIZANDO...");
			$asinc->fin("**** PROCESO FINALIZADO ****");
		}			
		
	}
	
	function getDatos(){
		
		$sql = "	select 
							Persona.tipo_documento,
							Persona.documento,
							concat(concat(Persona.apellido,', '),Persona.nombre) as apenom,
							socio_id,
							ifnull(
							(select sum(importe_dto) from liquidacion_socios as LiquidacionSocio
								where LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
								and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id group by  LiquidacionSocioRendicion.socio_id
							),0) as importe_dto,
							ifnull(
							(select sum(importe_adebitar) from liquidacion_socios as LiquidacionSocio
								where LiquidacionSocio.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
								and LiquidacionSocio.socio_id = LiquidacionSocioRendicion.socio_id group by  LiquidacionSocioRendicion.socio_id
							),0) as importe_adebitar,
							ifnull(sum(importe_debitado),0) as importe_debitado,
							ifnull(
							(select sum(importe_debitado) from
								liquidacion_cuotas as LiquidacionCuota where LiquidacionCuota.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
								and LiquidacionCuota.socio_id = LiquidacionSocioRendicion.socio_id group by  LiquidacionSocioRendicion.socio_id
							),0)as importe_imputado, 
							(sum(importe_debitado) - ifnull((select sum(importe_debitado) from
													liquidacion_cuotas as LiquidacionCuota where LiquidacionCuota.liquidacion_id = LiquidacionSocioRendicion.liquidacion_id
							and LiquidacionCuota.socio_id = LiquidacionSocioRendicion.socio_id group by  LiquidacionSocioRendicion.socio_id),0)) 
							as importe_reintegro
					from 
							liquidacion_socio_rendiciones as LiquidacionSocioRendicion
					inner join socios as Socio on (LiquidacionSocioRendicion.socio_id = Socio.id)
					inner join personas as Persona on (Socio.persona_id = Persona.id)						
					where 
							LiquidacionSocioRendicion.liquidacion_id = ".$this->liquidacion_id." and LiquidacionSocioRendicion.socio_id <> 0
							and LiquidacionSocioRendicion.indica_pago = 1
					group by 
							socio_id
					order by 
							Persona.apellido,Persona.nombre";
		
		$datos = $this->LiquidacionSocioRendicion->query($sql);
		return $datos;
	}
	
	
	function getResumenByProveedor(){
		
		$sql = "select 
				Proveedor.razon_social, sum(importe) as liquidado,sum(saldo_actual) as saldo_actual
				,sum(importe_debitado) as importe_debitado
				from liquidacion_cuotas as LiquidacionCuota
				inner join proveedores as Proveedor on (Proveedor.id = LiquidacionCuota.proveedor_id)
				where LiquidacionCuota.liquidacion_id = ".$this->liquidacion_id."
				and para_imputar = 1
				group by proveedor_id
				order by Proveedor.razon_social";
		$datos = $this->LiquidacionSocioRendicion->query($sql);
		return $datos;		
	}
	
	
}

?>
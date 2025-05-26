<?php 
/**
 * Listado de Reintegros
 * @author adrian
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php listado_reintegros 16 -app /home/adrian/dev/www/sigem/app/
 *
 */
class ListadoReintegrosShell extends Shell{
	
	var $periodoDesde;
	var $periodoHasta;
	var $codigoOrganismo;
	var $codigoEmpresa;
	
	var $uses = array('Pfyj.SocioReintegro');
	var $tasks = array('Temporal');
	
	function main(){
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$this->periodoDesde			= $asinc->getParametro('p1');
		$this->periodoHasta			= $asinc->getParametro('p2');
		$this->codigoOrganismo		= $asinc->getParametro('p3');
		$this->codigoEmpresa		= $asinc->getParametro('p4');
		$consolidado				= $asinc->getParametro('p5');
		
		$asinc->actualizar(0,100,"ESPERE, INICIANDO PROCESO...");
		
//		$consolidado = 1;
		
		$reintegros = $this->getReintegros($consolidado);
		$total = count($reintegros);
		$asinc->setTotal($total);
		$i = 0;	

		if(empty($reintegros)){
			$asinc->fin("NO EXISTEN REINTEGROS PARA PROCESAR...");
			return;
		}		

		App::import('Model','Pfyj.Socio');
		$oSOCIO = new Socio();			
		
		
		
		foreach($reintegros as $reintegro):
		
			if($consolidado == 0) $reintegro['SocioReintegro']['organismo'] = $this->SocioReintegro->GlobalDato("concepto_1",$reintegro['Liquidacion']['codigo_organismo']);
			$reintegro['SocioReintegro']['socio_tdocndoc'] = $oSOCIO->getTdocNdoc($reintegro['SocioReintegro']['socio_id']);
			$reintegro['SocioReintegro']['socio_apenom'] = $oSOCIO->getApenom($reintegro['SocioReintegro']['socio_id'],false);
			if($consolidado == 1){
				$reintegro['SocioReintegro']['importe_dto'] = $reintegro[0]['importe_dto'];
				$reintegro['SocioReintegro']['importe_debitado'] = $reintegro[0]['importe_debitado'];
				$reintegro['SocioReintegro']['importe_imputado'] = $reintegro[0]['importe_imputado'];
				$reintegro['SocioReintegro']['importe_aplicado'] = $reintegro[0]['importe_aplicado'];
				$reintegro['SocioReintegro']['cantidad'] = $reintegro[0]['cantidad'];
			}
			$reintegro = $reintegro['SocioReintegro'];
//			debug($reintegro);

			
			$asinc->actualizar($i,$total,"PROCESANDO $i/$total | " . $reintegro['socio_apenom']);
			
			$temp = array();
			
			$temp = array();
			$temp['AsincronoTemporal'] = array();
			$temp['AsincronoTemporal']['id'] = 0;
			$temp['AsincronoTemporal']['asincrono_id'] = $asinc->id;
			$temp['AsincronoTemporal']['clave_1'] = $reintegro['id'];
			$temp['AsincronoTemporal']['clave_2'] = $reintegro['socio_id'];
			
			if($consolidado == 0){
				$temp['AsincronoTemporal']['clave_3'] = $reintegro['liquidacion_id'];
				$temp['AsincronoTemporal']['texto_1'] = $reintegro['socio_tdocndoc'];
				$temp['AsincronoTemporal']['texto_2'] = $reintegro['socio_apenom'];
				$temp['AsincronoTemporal']['texto_3'] = $oSOCIO->periodo($reintegro['periodo'],false);
				$temp['AsincronoTemporal']['texto_4'] = $reintegro['organismo'];
				$temp['AsincronoTemporal']['texto_5'] = ($reintegro['anticipado'] == 1 ? "SI":"NO");
				$temp['AsincronoTemporal']['texto_6'] = ($reintegro['compensa_imputacion'] == 1 ? "SI":"NO");
				$temp['AsincronoTemporal']['texto_7'] = ($reintegro['procesado'] == 1 ? "SI":"NO");
				$temp['AsincronoTemporal']['decimal_1'] = $reintegro['importe_dto'];
				$temp['AsincronoTemporal']['decimal_2'] = $reintegro['importe_debitado'];
				$temp['AsincronoTemporal']['decimal_3'] = $reintegro['importe_imputado'];
				$temp['AsincronoTemporal']['decimal_4'] = ($reintegro['importe_reintegro'] == 0 ? ($reintegro['importe_debitado'] - $reintegro['importe_imputado']): $reintegro['importe_reintegro']);
				$temp['AsincronoTemporal']['decimal_5'] = $reintegro['importe_aplicado'];
			}else{
				$temp['AsincronoTemporal']['texto_1'] = $reintegro['socio_tdocndoc'];
				$temp['AsincronoTemporal']['texto_2'] = $reintegro['socio_apenom'];
				$temp['AsincronoTemporal']['decimal_1'] = $reintegro['importe_dto'];
				$temp['AsincronoTemporal']['decimal_2'] = $reintegro['importe_debitado'];
				$temp['AsincronoTemporal']['decimal_3'] = $reintegro['importe_imputado'];
				$temp['AsincronoTemporal']['decimal_4'] = ($reintegro['importe_reintegro'] == 0 ? ($reintegro['importe_debitado'] - $reintegro['importe_imputado']): $reintegro['importe_reintegro']);
				$temp['AsincronoTemporal']['decimal_5'] = $reintegro['importe_aplicado'];
				$temp['AsincronoTemporal']['entero_1'] = $reintegro['cantidad'];
			}
			
//			debug($temp);
			$this->Temporal->grabar($temp);
			if($asinc->detenido()) break;				
		
			$i++;
			
		endforeach;
		
		$asinc->fin("**** PROCESO FINALIZADO ****");		
		
	}
	
	
	function getReintegros($consolidar = 0){
			if($consolidar == 0):
			$sql = "SELECT * FROM socio_reintegros AS SocioReintegro
					INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = SocioReintegro.liquidacion_id)
					WHERE Liquidacion.periodo BETWEEN '$this->periodoDesde' AND '$this->periodoHasta'
					".(!empty($this->codigoOrganismo) ? "AND Liquidacion.codigo_organismo = '$this->codigoOrganismo'" : "")."
					AND SocioReintegro.socio_id IN (SELECT socio_id FROM liquidacion_socios AS LiquidacionSocio
					WHERE LiquidacionSocio.liquidacion_id = SocioReintegro.liquidacion_id
					".(!empty($this->codigoEmpresa) ? "AND LiquidacionSocio.codigo_empresa = '$this->codigoEmpresa'" : "")."
					)ORDER BY SocioReintegro.socio_id, SocioReintegro.periodo";	
		else:
			$sql = "SELECT 
						SocioReintegro.socio_id,
						SUM(SocioReintegro.importe_dto) as importe_dto,
						SUM(SocioReintegro.importe_debitado) as importe_debitado,
						SUM(SocioReintegro.importe_imputado) as importe_imputado,
						SUM(SocioReintegro.importe_reintegro) as importe_reintegro,
						SUM(SocioReintegro.importe_aplicado) as importe_aplicado,
						COUNT(1) AS cantidad
					FROM socio_reintegros AS SocioReintegro
					INNER JOIN liquidaciones AS Liquidacion ON (Liquidacion.id = SocioReintegro.liquidacion_id)
					WHERE Liquidacion.periodo BETWEEN '$this->periodoDesde' AND '$this->periodoHasta'
					".(!empty($this->codigoOrganismo) ? "AND Liquidacion.codigo_organismo = '$this->codigoOrganismo'" : "")."
					AND SocioReintegro.socio_id IN (SELECT socio_id FROM liquidacion_socios AS LiquidacionSocio
					WHERE LiquidacionSocio.liquidacion_id = SocioReintegro.liquidacion_id
					".(!empty($this->codigoEmpresa) ? "AND LiquidacionSocio.codigo_empresa = '$this->codigoEmpresa'" : "")."
					)GROUP BY SocioReintegro.socio_id";	
		endif;	
		$datos = $this->SocioReintegro->query($sql);
		if(empty($datos)) return null;
		else return $datos;
	}
	
	
}


?>
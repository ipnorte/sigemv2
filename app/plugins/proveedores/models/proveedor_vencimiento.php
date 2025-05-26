<?php
class ProveedorVencimiento extends ProveedoresAppModel{
	
	var $name = 'ProveedorVencimiento';
	var $belongsTo = array('Proveedor');
	
	function getDiaVtos($proveedor_id,$beneficio_id,$fecha){
		
		$fecha_carga = date('Y-m-d',strtotime($fecha));
		
		App::import('Model', 'Pfyj.PersonaBeneficio');
		$this->PersonaBeneficio = new PersonaBeneficio(null);		
		
		$beneficio = $this->PersonaBeneficio->read('codigo_beneficio',$beneficio_id);
		$setup = $this->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => $beneficio['PersonaBeneficio']['codigo_beneficio'], 'ProveedorVencimiento.proveedor_id' => $proveedor_id,'ProveedorVencimiento.mes' => date('m',strtotime($fecha_carga))),'fields' => array('d_vto_socio','d_vto_proveedor_suma')));
		return $setup[0]['ProveedorVencimiento'];
	}
	
	function calculaVencimiento($proveedor_id,$beneficio_id,$fecha){
		
		$fecha_carga = date('Y-m-d',strtotime($fecha));
		
		$this->unbindModel(array('belongsTo' => array('Proveedor')));
		
		App::import('Model', 'Pfyj.PersonaBeneficio');
		$this->PersonaBeneficio = new PersonaBeneficio(null);	

		$beneficio = $this->PersonaBeneficio->read('codigo_beneficio',$beneficio_id);
		
		$ultimoPeriodo = $this->__ultimaLiquidacionCerrada($beneficio['PersonaBeneficio']['codigo_beneficio']);
		
		$setup = $this->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => $beneficio['PersonaBeneficio']['codigo_beneficio'], 'ProveedorVencimiento.proveedor_id' => $proveedor_id,'ProveedorVencimiento.mes' => date('m',strtotime($fecha_carga)))));
// 		debug($setup);
		
		if(empty($setup)) $setup = $this->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => $beneficio['PersonaBeneficio']['codigo_beneficio'], 'ProveedorVencimiento.proveedor_id' => 18,'ProveedorVencimiento.mes' => date('m',strtotime($fecha_carga)))));;
		if(empty($setup)) $setup = $this->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => 'MUTUCORG2201', 'ProveedorVencimiento.proveedor_id' => 18,'ProveedorVencimiento.mes' => date('m',strtotime($fecha_carga)))));;
		$setup = $setup[0];
				
		$d_corte = $setup['ProveedorVencimiento']['d_corte'];
		$d_vto_socio = $setup['ProveedorVencimiento']['d_vto_socio'];
		$d_vto_proveedor_suma = $setup['ProveedorVencimiento']['d_vto_proveedor_suma'];
		$m_ini_socio_ac_suma = $setup['ProveedorVencimiento']['m_ini_socio_ac_suma'];
		$m_ini_socio_dc_suma = $setup['ProveedorVencimiento']['m_ini_socio_dc_suma'];
		$m_vto_socio_suma = $setup['ProveedorVencimiento']['m_vto_socio_suma'];
		
		
		$dia = date('d',strtotime($fecha_carga));
		$mes = date('m',strtotime($fecha_carga));
		$anio = date('Y',strtotime($fecha_carga));
		
		$mkTFC = mktime(0,0,0,$mes,$dia,$anio);
		
		
		if($dia <= $d_corte){
			
//			$iniciaSec = $mkTFC + $m_ini_socio_ac_suma * 31 * 24 * 60 * 60;
			$iniciaSec = $this->addMonthToDate($mkTFC,$m_ini_socio_ac_suma);
			$inicia = date('Ym',$iniciaSec);
		
		}else{

//			$iniciaSec = $mkTFC + $m_ini_socio_dc_suma * 31 * 24 * 60 * 60;
			$iniciaSec = $this->addMonthToDate($mkTFC,$m_ini_socio_dc_suma);
			$inicia = date('Ym',$iniciaSec);
		
		}
		
		$mkTvtoSocio = mktime(0,0,0,date('m',$iniciaSec),$d_vto_socio,date('Y',$iniciaSec));
//		$vtoSocSec = $mkTvtoSocio + $m_vto_socio_suma * 31 * 24 * 60 * 60;
		$vtoSocSec = $this->addMonthToDate($mkTvtoSocio,$m_vto_socio_suma);
		$vtoSocio = date('Y-m-d',$vtoSocSec);
		$vtoSocio = $this->checkFinDeSemana($vtoSocio);
		$mkTvtoSocio = mktime(0,0,0,date('m',strtotime($vtoSocio)),date('d',strtotime($vtoSocio)),date('Y',strtotime($vtoSocio)));

//		$vtoProSec = $vtoSocSec + $d_vto_proveedor_suma * 24 * 60 * 60;
		$vtoProSec = $this->addDayToDate($mkTvtoSocio,$d_vto_proveedor_suma);
		$vtoProv = date('Y-m-d',$vtoProSec);

		$vto['fecha_carga'] = $fecha_carga;
		$vto['inicia_en'] = $inicia;
		$vto['vto_primer_cuota_socio'] = $vtoSocio;
		$vto['vto_primer_cuota_proveedor'] = $this->checkFinDeSemana($vtoProv);	
		$vto['ultimo_periodo_liquidado'] = (!empty($ultimoPeriodo) ? $ultimoPeriodo : "190001");	
		
		return $vto;
		
	}
	
	
	function calculaVencimientoByPeriodo($proveedor_id,$codigo_organismo,$periodo,$fecha){

		$mesPeriodo = substr($periodo,4,2);
		$anioPeriodo = substr($periodo,0,4);
		
		$fecha = date('Y-m-d',strtotime($fecha));
		
		$this->unbindModel(array('belongsTo' => array('Proveedor')));
        $fecha_carga = date('Y-m-d');
		$setup = $this->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => $codigo_organismo, 'ProveedorVencimiento.proveedor_id' => $proveedor_id,'ProveedorVencimiento.mes' => date('m',strtotime($fecha)))));
		if(empty($setup)){
            // si no esta definido el vencimiento tomo el de la mutual para cbu
            $setup = $this->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => 'MUTUCORG2201', 'ProveedorVencimiento.proveedor_id' => 18,'ProveedorVencimiento.mes' => date('m',strtotime($fecha_carga)))));;
        }
        $setup = $setup[0];
		$ultimoPeriodo = $this->__ultimaLiquidacionCerrada($codigo_organismo);
		$setup['ProveedorVencimiento']['fecha_odto'] = $fecha;
		$setup['ProveedorVencimiento']['odto_d'] = date('d',strtotime($fecha));
		$setup['ProveedorVencimiento']['odto_m'] = date('m',strtotime($fecha));
		$setup['ProveedorVencimiento']['odto_y'] = date('Y',strtotime($fecha));
		
		$d_corte = $setup['ProveedorVencimiento']['d_corte'];
		$d_vto_socio = $setup['ProveedorVencimiento']['d_vto_socio'];
		$d_vto_proveedor_suma = $setup['ProveedorVencimiento']['d_vto_proveedor_suma'];
		$m_ini_socio_ac_suma = $setup['ProveedorVencimiento']['m_ini_socio_ac_suma'];
		$m_ini_socio_dc_suma = $setup['ProveedorVencimiento']['m_ini_socio_dc_suma'];
		$m_vto_socio_suma = $setup['ProveedorVencimiento']['m_vto_socio_suma'];
		
		
		$dia = date('d',strtotime($fecha));
		
		$mkTFC = mktime(0,0,0,$mesPeriodo,$dia,$anioPeriodo);	

		
		if($dia <= $d_corte){

			$iniciaSec = $this->addMonthToDate($mkTFC,$m_ini_socio_ac_suma);
			$inicia = date('Ym',$iniciaSec);
		
		}else{

			$iniciaSec = $this->addMonthToDate($mkTFC,$m_ini_socio_dc_suma);
			$inicia = date('Ym',$iniciaSec);
		
		}
		
//		debug($inicia);
		
		$mkTvtoSocio = mktime(0,0,0,date('m',$iniciaSec),$d_vto_socio,date('Y',$iniciaSec));
		$vtoSocSec = $this->addMonthToDate($mkTvtoSocio,$m_vto_socio_suma);
		$vtoSocio = date('Y-m-d',$vtoSocSec);
		$vtoSocio = $this->checkFinDeSemana($vtoSocio);
		$mkTvtoSocio = mktime(0,0,0,date('m',strtotime($vtoSocio)),date('d',strtotime($vtoSocio)),date('Y',strtotime($vtoSocio)));

		$vtoProSec = $this->addDayToDate($mkTvtoSocio,$d_vto_proveedor_suma);
		$vtoProv = date('Y-m-d',$vtoProSec);
		
		$vto['vto_cuota_socio'] = $vtoSocio;
		$vto['vto_cuota_proveedor'] = $this->checkFinDeSemana($vtoProv);
		$vto['ultimo_periodo_liquidado'] = (!empty($ultimoPeriodo) ? $ultimoPeriodo : "190001");			
		
		return $vto;
		
	}
	
	function calculaVencimientoByCodigoOrganismo($proveedor_id,$codigo_organismo,$fecha){
		
		$fecha_carga = date('Y-m-d',strtotime($fecha));
		
		$this->unbindModel(array('belongsTo' => array('Proveedor')));
		
		$setup = $this->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => $codigo_organismo, 'ProveedorVencimiento.proveedor_id' => $proveedor_id,'ProveedorVencimiento.mes' => date('m',strtotime($fecha_carga)))));
		
		if(empty($setup)) $setup = $this->find('all',array('conditions' => array('ProveedorVencimiento.codigo_organismo' => $codigo_organismo, 'ProveedorVencimiento.proveedor_id' => 18,'ProveedorVencimiento.mes' => date('m',strtotime($fecha_carga)))));;
		$setup = $setup[0];
		
		
		$ultimoPeriodo = $this->__ultimaLiquidacionCerrada($codigo_organismo);
		
		$d_corte = $setup['ProveedorVencimiento']['d_corte'];
		$d_vto_socio = $setup['ProveedorVencimiento']['d_vto_socio'];
		$d_vto_proveedor_suma = $setup['ProveedorVencimiento']['d_vto_proveedor_suma'];
		$m_ini_socio_ac_suma = $setup['ProveedorVencimiento']['m_ini_socio_ac_suma'];
		$m_ini_socio_dc_suma = $setup['ProveedorVencimiento']['m_ini_socio_dc_suma'];
		$m_vto_socio_suma = $setup['ProveedorVencimiento']['m_vto_socio_suma'];
		
		$dia = date('d',strtotime($fecha_carga));
		$mes = date('m',strtotime($fecha_carga));
		$anio = date('Y',strtotime($fecha_carga));
		
		$mkTFC = mktime(0,0,0,$mes,$dia,$anio);
		
		if($dia <= $d_corte){
			
//			$iniciaSec = $mkTFC + $m_ini_socio_ac_suma * 31 * 24 * 60 * 60;
			$iniciaSec = $this->addMonthToDate($mkTFC,$m_ini_socio_ac_suma);
			$inicia = date('Ym',$iniciaSec);
		
		}else{

//			$iniciaSec = $mkTFC + $m_ini_socio_dc_suma * 31 * 24 * 60 * 60;
			$iniciaSec = $this->addMonthToDate($mkTFC,$m_ini_socio_dc_suma);
			$inicia = date('Ym',$iniciaSec);
		
		}
		
		$mkTvtoSocio = mktime(0,0,0,date('m',$iniciaSec),$d_vto_socio,date('Y',$iniciaSec));
//		$vtoSocSec = $mkTvtoSocio + $m_vto_socio_suma * 31 * 24 * 60 * 60;
		$vtoSocSec = $this->addMonthToDate($mkTvtoSocio,$m_vto_socio_suma);
		$vtoSocio = date('Y-m-d',$vtoSocSec);
		$vtoSocio = $this->checkFinDeSemana($vtoSocio);
		$mkTvtoSocio = mktime(0,0,0,date('m',strtotime($vtoSocio)),date('d',strtotime($vtoSocio)),date('Y',strtotime($vtoSocio)));

//		$vtoProSec = $vtoSocSec + $d_vto_proveedor_suma * 24 * 60 * 60;
		$vtoProSec = $this->addDayToDate($mkTvtoSocio,$d_vto_proveedor_suma);
		$vtoProv = date('Y-m-d',$vtoProSec);

		$vto['fecha_carga'] = $fecha_carga;
		$vto['inicia_en'] = $inicia;
		$vto['vto_primer_cuota_socio'] = $vtoSocio;
		$vto['vto_primer_cuota_proveedor'] = $this->checkFinDeSemana($vtoProv);	
		$vto['ultimo_periodo_liquidado'] = (!empty($ultimoPeriodo) ? $ultimoPeriodo : "190001");	
		
		return $vto;
		
	}	
	
	/**
	 * checkFinDeSemana
	 * Verifica si la fecha cae un sabado o un domingo y lo corre al dia habil inmediato posterior (LUNES)
	 * @param $fecha
	 * @return unknown_type
	 */
	function checkFinDeSemana($fecha){
		$fechaTime = mktime(0,0,0,date('m',strtotime($fecha)),date('d',strtotime($fecha)),date('Y',strtotime($fecha)));
		$diaSemana = date('N',$fechaTime);
//		debug($fecha);
//		debug($diaSemana);
		switch ($diaSemana) {
			case 6:
				//SABADO
//				$nFechaTime = $fechaTime + (2*24*60*60);
				$nFechaTime = $this->addDayToDate($fechaTime,2);
				break;
			case 7:
				//DOMINGO
//				$nFechaTime = $fechaTime + (1*24*60*60);
				$nFechaTime = $this->addDayToDate($fechaTime,1);;
				break;
			default:
				$nFechaTime = $fechaTime;
				break;	
		}
		return date('Y-m-d', $nFechaTime);		
	}
	
	function __ultimaLiquidacionCerrada($codigo_organismo){
		App::import('Model','Mutual.Liquidacion');
		$oLQ = new Liquidacion();
		$liquidacion = $oLQ->find('all',array(
							'conditions' => array(
								'Liquidacion.codigo_organismo' => $codigo_organismo,
								'Liquidacion.cerrada' => 1
							),
							'fields' => array('Liquidacion.periodo'),
							'order' => array('Liquidacion.periodo DESC'),
							'limit' => 1
		
		));
		if(isset($liquidacion[0]['Liquidacion']['periodo'])) return $liquidacion[0]['Liquidacion']['periodo'];
		else return null;
	}
	
	
	function cargarValoresPorDefault($proveedor_id){
		/*
		 * INSERT INTO proveedor_vencimientos(proveedor_id,codigo_organismo,d_corte,d_vto_socio,
				d_vto_proveedor_suma,mes,m_ini_socio_ac_suma,m_ini_socio_dc_suma,
				m_vto_socio_suma)
		 * */
		$sql = "SELECT ProveedorVencimiento.*
				 FROM proveedor_vencimientos as ProveedorVencimiento WHERE ProveedorVencimiento.proveedor_id = 18
				AND ProveedorVencimiento.codigo_organismo NOT IN (SELECT codigo_organismo FROM 
				proveedor_vencimientos WHERE proveedor_id = $proveedor_id)";
		$datos = $this->query($sql);
		if(!empty($datos)){
			foreach ($datos as $dato):
				$dato['ProveedorVencimiento']['id'] = 0;
				$dato['ProveedorVencimiento']['proveedor_id'] = $proveedor_id;
				$this->save($dato);
			endforeach;
		}
		
	}
	
	
}
?>